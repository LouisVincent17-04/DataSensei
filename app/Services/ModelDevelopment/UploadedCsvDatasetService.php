<?php

namespace App\Services\ModelDevelopment;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use SplFileObject;

class UploadedCsvDatasetService
{
    /**
     * @return array<string, mixed>
     */
    public function store(UploadedFile $file, int $userId): array
    {
        $dataset = $this->parse($file);
        $token = (string) Str::uuid();
        $dataset['key'] = 'csv-' . $token;
        $dataset['source'] = 'uploaded_csv';
        $dataset['uploaded_at'] = now()->toIso8601String();

        $directory = $this->directory($userId);
        File::ensureDirectoryExists($directory, 0755, true);
        File::put(
            $directory . DIRECTORY_SEPARATOR . $token . '.json',
            json_encode($dataset, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR)
        );

        return $dataset;
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

    public function isUploadedKey(string $datasetKey): bool
    {
        return $this->tokenFromKey($datasetKey) !== null;
    }

    public function delete(string $datasetKey, int $userId): void
    {
        $token = $this->tokenFromKey($datasetKey);
        if ($token === null) {
            return;
        }

        File::delete($this->directory($userId) . DIRECTORY_SEPARATOR . $token . '.json');
    }

    /**
     * @return array<string, mixed>
     */
    private function parse(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        if (! is_string($path) || ! is_file($path)) {
            throw ValidationException::withMessages([
                'dataset_csv' => 'The uploaded CSV file could not be read.',
            ]);
        }

        $delimiter = $this->detectDelimiter($path);
        $csv = new SplFileObject($path, 'r');
        $csv->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        $csv->setCsvControl($delimiter);

        $headers = null;
        $rows = [];
        $maxRows = max(20, (int) config('model_development.max_dataset_rows', 1000));
        $maxColumns = max(2, (int) config('model_development.max_dataset_columns', 50));

        foreach ($csv as $record) {
            if (! is_array($record) || $this->rowIsEmpty($record)) {
                continue;
            }

            if ($headers === null) {
                $headers = array_map(fn ($value): string => $this->cleanHeader((string) $value), $record);
                if (isset($headers[0])) {
                    $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]) ?? $headers[0];
                }

                if (count($headers) < 2) {
                    throw ValidationException::withMessages([
                        'dataset_csv' => 'The CSV must contain at least two columns.',
                    ]);
                }
                if (count($headers) > $maxColumns) {
                    throw ValidationException::withMessages([
                        'dataset_csv' => "The CSV may contain at most {$maxColumns} columns.",
                    ]);
                }
                if (in_array('', $headers, true)) {
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

            if (count($record) > count($headers)) {
                $extra = array_slice($record, count($headers));
                if (! $this->rowIsEmpty($extra)) {
                    throw ValidationException::withMessages([
                        'dataset_csv' => 'At least one CSV row contains more values than the header row.',
                    ]);
                }
            }

            $record = array_pad(array_slice($record, 0, count($headers)), count($headers), null);
            $row = [];
            foreach ($headers as $index => $header) {
                $value = $record[$index] ?? null;
                if (is_string($value)) {
                    $value = trim($value);
                    if (strlen($value) > 10000) {
                        throw ValidationException::withMessages([
                            'dataset_csv' => "A value under '{$header}' is too long.",
                        ]);
                    }
                }
                $row[$header] = $value === '' ? null : $value;
            }

            if (! $this->rowIsEmpty(array_values($row))) {
                $rows[] = $row;
            }

            if (count($rows) > $maxRows) {
                throw ValidationException::withMessages([
                    'dataset_csv' => "The CSV may contain at most {$maxRows} data rows.",
                ]);
            }
        }

        if ($headers === null) {
            throw ValidationException::withMessages([
                'dataset_csv' => 'The CSV file is empty.',
            ]);
        }
        if (count($rows) < 20) {
            throw ValidationException::withMessages([
                'dataset_csv' => 'The CSV must contain at least 20 data rows for model training.',
            ]);
        }

        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = substr($filename, 0, 80);
        $title = trim(ucwords(str_replace(['-', '_'], ' ', $filename)));
        if ($title === '') {
            $title = 'Uploaded Dataset';
        }

        return [
            'title' => $title,
            'category' => 'Uploaded CSV',
            'difficulty' => 'Custom dataset',
            'description' => 'A CSV dataset uploaded by the student for controlled model training.',
            'learning_objective' => 'Inspect the uploaded CSV, select appropriate features and a target, then train and evaluate a supported machine-learning model.',
            'columns' => $headers,
            'rows' => $rows,
            'original_filename' => $file->getClientOriginalName(),
        ];
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
        $header = preg_replace('/\s+/', ' ', $header) ?? $header;

        return substr($header, 0, 120);
    }

    private function directory(int $userId): string
    {
        return storage_path('app/model_development/uploads/' . $userId);
    }

    private function tokenFromKey(string $datasetKey): ?string
    {
        if (preg_match('/^csv-([0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})$/i', $datasetKey, $match) !== 1) {
            return null;
        }

        return strtolower($match[1]);
    }
}
