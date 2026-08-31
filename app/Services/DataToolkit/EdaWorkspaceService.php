<?php

namespace App\Services\DataToolkit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use SplFileObject;

class EdaWorkspaceService
{
    public const MAX_FILE_BYTES = 10 * 1024 * 1024;

    public const MAX_ROWS = 10000;

    public function __construct(
        private readonly StatisticsService $statistics,
        private readonly EdaAnalysisService $eda,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function storeUpload(UploadedFile $file, int $userId): array
    {
        $dataset = $this->parse($file);
        $dataset['key'] = 'eda-' . Str::uuid();
        $dataset['source'] = 'uploaded_csv';
        $dataset['uploaded_at'] = now()->toIso8601String();

        return $this->persist($dataset, $userId);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $datasetKey, int $userId): ?array
    {
        $token = $this->tokenFromKey($datasetKey);
        if ($token === null) {
            return null;
        }

        $path = $this->directory($userId) . DIRECTORY_SEPARATOR . $token . '.json';
        if (! is_file($path)) {
            return null;
        }

        try {
            $decoded = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @param array<string, mixed> $dataset
     * @return array<string, mixed>
     */
    public function saveObjective(array $dataset, string $objective, int $userId): array
    {
        $dataset = $this->ensureWorkspace($dataset);
        $dataset['analysis_objective'] = trim($objective);

        return $this->persist($dataset, $userId);
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<int, string> $numericColumns
     * @param array<int, string> $categoricalColumns
     * @return array<string, mixed>
     */
    public function cleanAndStore(
        array $dataset,
        array $numericColumns,
        array $categoricalColumns,
        array $actions,
        array $beforeOverview,
        int $userId
    ): array
    {
        $dataset = $this->ensureOriginalSnapshot($this->ensureWorkspace($dataset), $beforeOverview);

        return $this->persist(
            $this->cleanDataset(
                $dataset,
                $numericColumns,
                $categoricalColumns,
                $actions,
                (array) ($dataset['validation_rules'] ?? [])
            ),
            $userId
        );
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<int, string> $numericColumns
     * @param array<int, string> $categoricalColumns
     * @return array<string, mixed>
     */
    public function cleanDataset(
        array $dataset,
        array $numericColumns,
        array $categoricalColumns,
        array $actions = [],
        array $validationRules = []
    ): array
    {
        $actions = array_merge([
            'duplicates' => 'remove',
            'missing' => 'fill',
            'categories' => 'standardize',
            'invalid' => 'replace',
        ], $actions);
        $columns = array_values(array_map('strval', (array) ($dataset['columns'] ?? [])));
        $rows = array_values((array) ($dataset['rows'] ?? []));
        $rowsBefore = count($rows);
        $seen = [];
        $workingRows = [];
        $duplicatesRemoved = 0;

        foreach ($rows as $row) {
            $ordered = [];
            foreach ($columns as $column) {
                $ordered[$column] = $row[$column] ?? null;
            }

            $hash = hash('sha256', json_encode($ordered, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION) ?: '');
            if ($actions['duplicates'] === 'remove' && isset($seen[$hash])) {
                $duplicatesRemoved++;
                continue;
            }

            $seen[$hash] = true;
            $workingRows[] = $ordered;
        }

        $categoriesStandardized = 0;
        if ($actions['categories'] === 'standardize') {
            $canonicalMaps = $this->canonicalCategoryMaps($workingRows, $categoricalColumns);
            foreach ($workingRows as &$row) {
                foreach ($canonicalMaps as $column => $mapping) {
                    $value = $row[$column] ?? null;
                    if ($this->statistics->isMissing($value)) {
                        continue;
                    }

                    $normalized = $this->normaliseCategory((string) $value);
                    $canonical = $mapping[$normalized] ?? null;
                    if ($canonical !== null && (string) $value !== $canonical) {
                        $row[$column] = $canonical;
                        $categoriesStandardized++;
                    }
                }
            }
            unset($row);
        }

        $ranges = (array) ($validationRules['numeric_ranges'] ?? []);
        $invalidRowsRemoved = 0;
        $invalidValuesCorrected = 0;
        if ($actions['invalid'] === 'remove') {
            $workingRows = array_values(array_filter($workingRows, function (array $row) use ($numericColumns, $ranges, &$invalidRowsRemoved): bool {
                foreach ($numericColumns as $column) {
                    $value = $row[$column] ?? null;
                    if ($this->invalidNumericReason($value, (array) ($ranges[$column] ?? [])) !== null) {
                        $invalidRowsRemoved++;

                        return false;
                    }
                }

                return true;
            }));
        } elseif ($actions['invalid'] === 'replace') {
            $numericFill = $this->numericFillValues($workingRows, $numericColumns, $ranges);
            foreach ($workingRows as &$row) {
                foreach ($numericColumns as $column) {
                    $value = $row[$column] ?? null;
                    if ($this->invalidNumericReason($value, (array) ($ranges[$column] ?? [])) !== null
                        && $numericFill[$column] !== null) {
                        $row[$column] = $numericFill[$column];
                        $invalidValuesCorrected++;
                    }
                }
            }
            unset($row);
        }

        $missingValuesFilled = 0;
        if ($actions['missing'] === 'fill') {
            $numericFill = $this->numericFillValues($workingRows, $numericColumns, $ranges);
            $categoryFill = $this->categoryFillValues($workingRows, $categoricalColumns);
            foreach ($workingRows as &$row) {
                foreach ($numericColumns as $column) {
                    if ($this->statistics->isMissing($row[$column] ?? null) && $numericFill[$column] !== null) {
                        $row[$column] = $numericFill[$column];
                        $missingValuesFilled++;
                    }
                }

                foreach ($categoricalColumns as $column) {
                    if ($this->statistics->isMissing($row[$column] ?? null)) {
                        $row[$column] = $categoryFill[$column];
                        $missingValuesFilled++;
                    }
                }
            }
            unset($row);
        }

        foreach ($workingRows as &$row) {
            foreach ($numericColumns as $column) {
                $value = $row[$column] ?? null;
                if (is_numeric($value)) {
                    $numericValue = (float) $value;
                    $row[$column] = floor($numericValue) === $numericValue ? (int) $numericValue : $numericValue;
                }
            }
        }
        unset($row);

        $summary = [
            'rows_before' => $rowsBefore,
            'rows_after' => count($workingRows),
            'duplicates_removed' => $duplicatesRemoved,
            'missing_values_filled' => $missingValuesFilled,
            'categories_standardized' => $categoriesStandardized,
            'invalid_values_corrected' => $invalidValuesCorrected,
            'invalid_rows_removed' => $invalidRowsRemoved,
            'values_filled' => $missingValuesFilled + $invalidValuesCorrected,
            'invalid_numeric_values_replaced' => $invalidValuesCorrected,
            'choices' => $actions,
        ];
        $dataset['rows'] = $workingRows;
        $dataset['cleaning_summary'] = $summary;
        $history = array_values((array) ($dataset['cleaning_history'] ?? []));
        $history[] = $summary;
        $dataset['cleaning_history'] = $history;
        $parts = [
            $actions['duplicates'] === 'remove'
                ? "duplicates removed ({$duplicatesRemoved} row(s))"
                : 'duplicates kept',
            $actions['missing'] === 'fill'
                ? "missing values filled ({$missingValuesFilled} value(s))"
                : 'missing values kept',
            $actions['categories'] === 'standardize'
                ? "category labels standardized ({$categoriesStandardized} value(s))"
                : 'category labels kept',
            match ($actions['invalid']) {
                'replace' => "invalid values replaced ({$invalidValuesCorrected} value(s))",
                'remove' => "invalid rows removed ({$invalidRowsRemoved} row(s))",
                default => 'invalid values kept',
            },
        ];
        $dataset['eda_actions'] = $this->appendAction($dataset, [
            'type' => 'cleaning',
            'label' => 'Cleaning choices applied: ' . implode(', ', $parts) . '.',
        ]);

        return $dataset;
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<int, string> $selectedColumns
     * @param array<int, string> $numericColumns
     * @return array<string, mixed>
     */
    public function removeOutliersAndStore(
        array $dataset,
        array $selectedColumns,
        array $numericColumns,
        array $beforeOverview,
        int $userId
    ): array
    {
        $dataset = $this->ensureOriginalSnapshot($this->ensureWorkspace($dataset), $beforeOverview);

        return $this->persist(
            $this->removeOutliers($dataset, $selectedColumns, $numericColumns),
            $userId
        );
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<int, string> $selectedColumns
     * @param array<int, string> $numericColumns
     * @return array<string, mixed>
     */
    public function removeOutliers(array $dataset, array $selectedColumns, array $numericColumns): array
    {
        $selectedColumns = array_values(array_intersect($numericColumns, $selectedColumns));
        $rows = array_values((array) ($dataset['rows'] ?? []));
        $profile = $this->eda->outlierSummary($rows, $numericColumns);
        $fences = [];

        foreach ($selectedColumns as $column) {
            $columnProfile = $profile['columns'][$column] ?? null;
            if (! is_array($columnProfile) || ($columnProfile['outlier_count'] ?? 0) < 1) {
                continue;
            }
            $fences[$column] = [
                'lower' => $columnProfile['lower_fence'],
                'upper' => $columnProfile['upper_fence'],
            ];
        }

        $kept = [];
        $removed = 0;
        foreach ($rows as $row) {
            $isOutlier = false;
            foreach ($fences as $column => $fence) {
                $value = $row[$column] ?? null;
                if (is_numeric($value) && ((float) $value < (float) $fence['lower'] || (float) $value > (float) $fence['upper'])) {
                    $isOutlier = true;
                    break;
                }
            }

            if ($isOutlier) {
                $removed++;
            } else {
                $kept[] = $row;
            }
        }

        $dataset['rows'] = $kept;
        $dataset['outlier_summary'] = [
            'removed_rows' => $removed,
            'columns' => array_keys($fences),
        ];
        $columnLabel = $fences === [] ? 'no columns' : implode(', ', array_keys($fences));
        $dataset['eda_actions'] = $this->appendAction($dataset, [
            'type' => 'outliers',
            'label' => "Removed {$removed} row(s) flagged as potential outliers in {$columnLabel}.",
        ]);

        return $dataset;
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<string, mixed> $suggestion
     * @return array<string, mixed>
     */
    public function addFeatureAndStore(array $dataset, array $suggestion, array $beforeOverview, int $userId): array
    {
        $dataset = $this->ensureOriginalSnapshot($this->ensureWorkspace($dataset), $beforeOverview);

        return $this->persist(
            $this->addFeature($dataset, $suggestion),
            $userId
        );
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<string, mixed> $suggestion
     * @return array<string, mixed>
     */
    public function addFeature(array $dataset, array $suggestion): array
    {
        $columns = array_values(array_map('strval', (array) ($dataset['columns'] ?? [])));
        $sourceColumns = array_values(array_map('strval', (array) ($suggestion['columns'] ?? [])));
        $name = trim((string) ($suggestion['name'] ?? ''));
        $operation = (string) ($suggestion['operation'] ?? '');

        if ($name === '' || in_array($name, $columns, true) || array_diff($sourceColumns, $columns) !== []) {
            throw ValidationException::withMessages([
                'suggestion_key' => 'This feature suggestion is no longer available for the current dataset.',
            ]);
        }

        $rows = array_values((array) ($dataset['rows'] ?? []));
        foreach ($rows as &$row) {
            $values = array_map(fn (string $column): mixed => $row[$column] ?? null, $sourceColumns);
            $numericValues = array_map(fn (mixed $value): ?float => is_numeric($value) ? (float) $value : null, $values);

            $value = match ($operation) {
                'sum' => in_array(null, $numericValues, true) ? null : array_sum($numericValues),
                'mean' => in_array(null, $numericValues, true) || $numericValues === [] ? null : array_sum($numericValues) / count($numericValues),
                'product' => in_array(null, $numericValues, true) ? null : array_product($numericValues),
                'scale' => isset($numericValues[0]) && $numericValues[0] !== null
                    ? $numericValues[0] * (float) ($suggestion['factor'] ?? 1)
                    : null,
                default => null,
            };

            $row[$name] = $value === null ? null : $this->statistics->roundNumber($value, 4);
        }
        unset($row);

        $dataset['rows'] = $rows;
        $dataset['columns'][] = $name;
        $created = (array) ($dataset['created_features'] ?? []);
        $created[] = [
            'name' => $name,
            'formula' => (string) ($suggestion['formula'] ?? $name),
            'explanation' => (string) ($suggestion['explanation'] ?? ''),
        ];
        $dataset['created_features'] = $created;
        $dataset['eda_actions'] = $this->appendAction($dataset, [
            'type' => 'feature',
            'label' => "Created the {$name} feature.",
        ]);

        return $dataset;
    }

    /**
     * @return array<string, mixed>
     */
    public function parse(UploadedFile $file): array
    {
        if (! $file->isValid()) {
            throw ValidationException::withMessages([
                'dataset_csv' => 'The upload did not finish successfully. Please choose the CSV file again.',
            ]);
        }

        if (strtolower($file->getClientOriginalExtension()) !== 'csv') {
            throw ValidationException::withMessages([
                'dataset_csv' => 'Only CSV files are supported for EDA.',
            ]);
        }

        $size = (int) ($file->getSize() ?? 0);
        if ($size < 1) {
            throw ValidationException::withMessages([
                'dataset_csv' => 'The selected CSV file is empty.',
            ]);
        }
        if ($size > self::MAX_FILE_BYTES) {
            throw ValidationException::withMessages([
                'dataset_csv' => 'File is too large. The maximum allowed size is 10 MB.',
            ]);
        }

        $path = $file->getRealPath();
        if (! is_string($path) || ! is_file($path)) {
            throw ValidationException::withMessages([
                'dataset_csv' => 'The uploaded CSV file could not be read.',
            ]);
        }

        $delimiter = $this->detectDelimiter($path);
        $csv = new SplFileObject($path, 'r');
        $csv->setFlags(SplFileObject::READ_CSV | SplFileObject::DROP_NEW_LINE);
        $csv->setCsvControl($delimiter);

        $headers = null;
        $rows = [];
        $emptyRows = 0;

        foreach ($csv as $record) {
            if (! is_array($record)) {
                continue;
            }

            if ($this->rowIsEmpty($record)) {
                $emptyRows++;
                continue;
            }

            if ($headers === null) {
                $headers = array_map(fn (mixed $value): string => $this->cleanHeader((string) $value), $record);
                if (isset($headers[0])) {
                    $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]) ?? $headers[0];
                }

                $invalidHeader = array_filter($headers, fn (string $header): bool => preg_match('//u', $header) !== 1 || str_contains($header, "\0"));
                if ($invalidHeader !== []) {
                    throw ValidationException::withMessages([
                        'dataset_csv' => 'The CSV contains unreadable column headers. Save it as a UTF-8 CSV and try again.',
                    ]);
                }
                if ($headers === [] || in_array('', $headers, true)) {
                    throw ValidationException::withMessages([
                        'dataset_csv' => 'Every CSV column must have a header.',
                    ]);
                }

                $normalized = array_map(fn (string $header): string => strtolower($header), $headers);
                if (count(array_unique($normalized)) !== count($normalized)) {
                    throw ValidationException::withMessages([
                        'dataset_csv' => 'CSV column headers must be unique.',
                    ]);
                }
                continue;
            }

            if (count($record) > count($headers) && ! $this->rowIsEmpty(array_slice($record, count($headers)))) {
                throw ValidationException::withMessages([
                    'dataset_csv' => 'The CSV is malformed because a row contains more values than the header row.',
                ]);
            }

            $record = array_pad(array_slice($record, 0, count($headers)), count($headers), null);
            $row = [];
            foreach ($headers as $index => $header) {
                $value = $record[$index] ?? null;
                if (is_string($value)) {
                    $value = trim($value);
                    if (str_contains($value, "\0") || preg_match('//u', $value) !== 1) {
                        throw ValidationException::withMessages([
                            'dataset_csv' => 'The CSV contains unreadable text. Save it as a UTF-8 CSV and try again.',
                        ]);
                    }
                }
                $row[$header] = $value === '' ? null : $value;
            }

            if (! $this->rowIsEmpty(array_values($row))) {
                $rows[] = $row;
            }

            if (count($rows) > self::MAX_ROWS) {
                throw ValidationException::withMessages([
                    'dataset_csv' => 'Dataset is too large. The maximum allowed is 10,000 data rows.',
                ]);
            }
        }

        if ($headers === null) {
            throw ValidationException::withMessages([
                'dataset_csv' => 'The selected CSV file is empty or has no readable columns.',
            ]);
        }
        if ($rows === []) {
            throw ValidationException::withMessages([
                'dataset_csv' => 'The CSV contains column headers but no valid data rows.',
            ]);
        }

        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $title = trim(ucwords(str_replace(['-', '_'], ' ', substr($filename, 0, 80))));

        return [
            'title' => $title !== '' ? $title : 'Uploaded Dataset',
            'category' => 'Uploaded CSV',
            'difficulty' => 'Your dataset',
            'description' => 'A CSV dataset uploaded for the guided exploratory data analysis roadmap.',
            'learning_objective' => '',
            'columns' => $headers,
            'rows' => $rows,
            'original_filename' => $file->getClientOriginalName(),
            'upload_metadata' => [
                'file_size' => $size,
                'empty_rows_skipped' => max(0, $emptyRows - 1),
                'delimiter' => $delimiter === "\t" ? 'tab' : $delimiter,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<string, mixed> $overview
     * @return array<string, mixed>
     */
    private function ensureOriginalSnapshot(array $dataset, array $overview): array
    {
        if (is_array($dataset['original_snapshot'] ?? null)) {
            return $dataset;
        }

        $snapshot = $overview['current_snapshot'] ?? null;
        if (is_array($snapshot)) {
            $dataset['original_snapshot'] = $snapshot;
        }

        return $dataset;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $columns
     * @return array<string, array<string, string>>
     */
    private function canonicalCategoryMaps(array $rows, array $columns): array
    {
        $maps = [];
        foreach ($columns as $column) {
            if ($this->isIdentifierColumn($column)) {
                continue;
            }

            $groups = [];
            foreach ($rows as $row) {
                $value = $row[$column] ?? null;
                if ($this->statistics->isMissing($value)) {
                    continue;
                }

                $display = preg_replace('/\s+/u', ' ', (string) $value) ?? (string) $value;
                $normalized = $this->normaliseCategory($display);
                $groups[$normalized][$display] = ($groups[$normalized][$display] ?? 0) + 1;
            }

            $nonMissing = array_sum(array_map(fn (array $variants): int => array_sum($variants), $groups));
            if (count($groups) > max(20, (int) floor($nonMissing * 0.20))) {
                continue;
            }

            foreach ($groups as $normalized => $variants) {
                if (count($variants) < 2) {
                    continue;
                }

                arsort($variants);
                $maps[$column][$normalized] = trim((string) array_key_first($variants));
            }
        }

        return $maps;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $columns
     * @param array<string, array<string, mixed>> $ranges
     * @return array<string, float|int|null>
     */
    private function numericFillValues(array $rows, array $columns, array $ranges): array
    {
        $fills = [];
        foreach ($columns as $column) {
            $values = [];
            foreach ($rows as $row) {
                $value = $row[$column] ?? null;
                if ($this->invalidNumericReason($value, (array) ($ranges[$column] ?? [])) === null
                    && ! $this->statistics->isMissing($value)
                    && is_numeric($value)) {
                    $values[] = (float) $value;
                }
            }
            $fills[$column] = $this->statistics->median($values);
        }

        return $fills;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $columns
     * @return array<string, string>
     */
    private function categoryFillValues(array $rows, array $columns): array
    {
        $fills = [];
        foreach ($columns as $column) {
            $frequency = [];
            foreach ($rows as $row) {
                $value = $row[$column] ?? null;
                if ($this->statistics->isMissing($value)) {
                    continue;
                }
                $key = trim((string) $value);
                $frequency[$key] = ($frequency[$key] ?? 0) + 1;
            }
            arsort($frequency);
            $fills[$column] = (string) (array_key_first($frequency) ?? 'Unknown');
        }

        return $fills;
    }

    /**
     * @param array<string, mixed> $rule
     */
    private function invalidNumericReason(mixed $value, array $rule): ?string
    {
        if ($this->statistics->isMissing($value)) {
            return null;
        }
        if (! is_numeric($value)) {
            return 'not_numeric';
        }
        if (array_key_exists('min', $rule) && (float) $value < (float) $rule['min']) {
            return 'below_minimum';
        }
        if (array_key_exists('max', $rule) && (float) $value > (float) $rule['max']) {
            return 'above_maximum';
        }

        return null;
    }

    private function normaliseCategory(string $value): string
    {
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? trim($value);

        return function_exists('mb_strtolower') ? mb_strtolower($value) : strtolower($value);
    }

    private function isIdentifierColumn(string $column): bool
    {
        $normalized = strtolower(trim($column));

        return $normalized === 'id'
            || str_ends_with($normalized, '_id')
            || str_ends_with($normalized, ' id');
    }

    /**
     * @param array<string, mixed> $dataset
     * @return array<string, mixed>
     */
    private function ensureWorkspace(array $dataset): array
    {
        if ($this->tokenFromKey((string) ($dataset['key'] ?? '')) === null) {
            $dataset['original_dataset_key'] = (string) ($dataset['key'] ?? '');
            $dataset['key'] = 'eda-' . Str::uuid();
            $dataset['source'] = $dataset['source'] ?? 'predefined';
        }

        return $dataset;
    }

    /**
     * @param array<string, mixed> $dataset
     * @return array<string, mixed>
     */
    private function persist(array $dataset, int $userId): array
    {
        $token = $this->tokenFromKey((string) ($dataset['key'] ?? ''));
        if ($token === null) {
            throw new \InvalidArgumentException('The EDA workspace key is invalid.');
        }

        $directory = $this->directory($userId);
        File::ensureDirectoryExists($directory, 0755, true);
        $written = File::put(
            $directory . DIRECTORY_SEPARATOR . $token . '.json',
            json_encode($dataset, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR)
        );
        if ($written === false) {
            throw ValidationException::withMessages([
                'dataset' => 'DataSensei could not save this EDA working copy. Please try again.',
            ]);
        }

        return $dataset;
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<string, string> $action
     * @return array<int, array<string, string>>
     */
    private function appendAction(array $dataset, array $action): array
    {
        $actions = array_values((array) ($dataset['eda_actions'] ?? []));
        $actions[] = $action;

        return $actions;
    }

    private function detectDelimiter(string $path): string
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return ',';
        }

        $line = '';
        while (($candidate = fgets($handle)) !== false) {
            if (trim($candidate) !== '') {
                $line = $candidate;
                break;
            }
        }
        fclose($handle);

        $bestDelimiter = ',';
        $bestCount = 1;
        foreach ([',', ';', "\t", '|'] as $delimiter) {
            $count = count(str_getcsv($line, $delimiter));
            if ($count > $bestCount) {
                $bestCount = $count;
                $bestDelimiter = $delimiter;
            }
        }

        return $bestDelimiter;
    }

    /**
     * @param array<int, mixed> $row
     */
    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function cleanHeader(string $header): string
    {
        $header = trim($header);
        $header = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $header) ?? $header;
        $header = preg_replace('/\s+/', ' ', $header) ?? $header;

        return substr($header, 0, 120);
    }

    private function directory(int $userId): string
    {
        return storage_path('app/data-toolkit/workspaces/' . $userId);
    }

    private function tokenFromKey(string $datasetKey): ?string
    {
        if (preg_match('/^eda-([0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})$/i', $datasetKey, $match) !== 1) {
            return null;
        }

        return strtolower($match[1]);
    }
}
