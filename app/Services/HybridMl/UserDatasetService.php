<?php

namespace App\Services\HybridMl;

use App\Models\DatasetVersion;
use App\Models\QualityReport;
use App\Models\UserDataset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserDatasetService
{
    public function __construct(
        private readonly TabularDatasetReader $reader,
        private readonly DatasetProfiler $profiler,
        private readonly DatasetQualityAnalyzer $qualityAnalyzer,
    ) {}

    public function create(
        UploadedFile $file,
        int $userId,
        ?int $classId = null,
        ?string $name = null,
        ?string $targetColumn = null,
        ?string $problemType = null,
    ): UserDataset {
        $analysis = $this->analyzeUpload($file, $targetColumn, $problemType);
        $parsed = $analysis['parsed'];
        $profile = $analysis['profile'];
        $quality = $analysis['quality'];
        $targetColumn = $analysis['target_column'];
        $problemType = $analysis['problem_type'];
        $uuid = (string) Str::uuid();
        $datasetName = $this->cleanName($name ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $relativePath = "ml/users/{$userId}/datasets/{$uuid}/v1/dataset.csv";
        $absolutePath = storage_path('app/'.$relativePath);
        File::ensureDirectoryExists(dirname($absolutePath), 0755, true);
        $this->writeCanonicalCsv($absolutePath, $parsed['headers'], $parsed['rows']);
        $checksum = hash_file('sha256', $absolutePath);

        try {
            return DB::transaction(function () use (
                $file, $userId, $classId, $uuid, $datasetName, $relativePath, $absolutePath,
                $profile, $quality, $parsed, $targetColumn, $problemType, $checksum
            ): UserDataset {
                $dataset = UserDataset::create([
                    'user_id' => $userId,
                    'class_id' => $classId,
                    'uuid' => $uuid,
                    'name' => $datasetName,
                    'original_filename' => substr($file->getClientOriginalName(), 0, 191),
                    'stored_filename' => 'dataset.csv',
                    'mime_type' => substr((string) ($file->getMimeType() ?: 'text/csv'), 0, 120),
                    'storage_path' => $relativePath,
                    'file_size' => (int) filesize($absolutePath),
                    'row_count' => (int) $profile['row_count'],
                    'column_count' => (int) $profile['column_count'],
                    'target_column' => $targetColumn,
                    'problem_type' => $problemType,
                    'status' => 'ready',
                    'quality_score' => (float) $quality['quality_score'],
                    'schema_profile' => $profile,
                    'metadata' => [
                        'upload_format' => $parsed['metadata']['format'] ?? 'csv',
                        'formula_cells_sanitized' => (int) ($parsed['metadata']['formula_cells_sanitized'] ?? 0),
                        'preview' => $profile['preview'],
                    ],
                ]);

                $version = DatasetVersion::create([
                    'user_dataset_id' => $dataset->id,
                    'created_by' => $userId,
                    'version_number' => 1,
                    'version_label' => 'v1',
                    'checksum_sha256' => $checksum,
                    'storage_path' => $relativePath,
                    'row_count' => (int) $profile['row_count'],
                    'column_count' => (int) $profile['column_count'],
                    'schema_profile' => $profile,
                    'metadata' => ['original_filename' => $file->getClientOriginalName()],
                ]);

                QualityReport::create([
                    'user_dataset_id' => $dataset->id,
                    'dataset_version_id' => $version->id,
                    'user_id' => $userId,
                    'quality_score' => (float) $quality['quality_score'],
                    'summary' => $quality['summary'],
                    'column_analysis' => $quality['column_analysis'],
                    'recommendations' => $quality['recommendations'],
                    'generated_at' => now(),
                ]);

                return $dataset->load(['versions', 'qualityReports']);
            });
        } catch (\Throwable $exception) {
            File::deleteDirectory(dirname(dirname($absolutePath)));
            throw $exception;
        }
    }


    /**
     * Validate and analyze an upload without persisting it.
     *
     * @return array{parsed:array<string,mixed>,profile:array<string,mixed>,quality:array<string,mixed>,target_column:?string,problem_type:string}
     */
    public function analyzeUpload(
        UploadedFile $file,
        ?string $targetColumn = null,
        ?string $problemType = null,
    ): array {
        $parsed = $this->reader->readUploadedFile($file);
        $profile = $this->profiler->profile($parsed['headers'], $parsed['rows']);

        $emptyColumns = array_keys(array_filter(
            (array) $profile['columns'],
            static fn (array $column): bool => ($column['type'] ?? '') === 'empty'
        ));
        if ($emptyColumns !== []) {
            throw ValidationException::withMessages([
                'dataset_file' => 'Remove empty columns before uploading: '.implode(', ', array_slice($emptyColumns, 0, 10)).'.',
            ]);
        }

        $targetColumn = $this->resolveTarget($targetColumn, $profile);
        $problemType = $this->resolveProblemType($problemType, $targetColumn, $profile);
        $quality = $this->qualityAnalyzer->analyze($parsed['rows'], $profile, $targetColumn, $problemType);

        return [
            'parsed' => $parsed,
            'profile' => $profile,
            'quality' => $quality,
            'target_column' => $targetColumn,
            'problem_type' => $problemType,
        ];
    }

    /** @return array<int,array<string,mixed>> */
    public function preview(UserDataset $dataset, ?int $limit = null): array
    {
        $limit ??= max(5, (int) config('hybrid_ml.preview_rows', 20));
        return $this->readRows(storage_path('app/'.$dataset->storage_path), $limit);
    }

    /** @return array<int,array<string,mixed>> */
    public function readRows(string $path, ?int $limit = null): array
    {
        if (! is_file($path)) {
            return [];
        }

        $file = new \SplFileObject($path, 'r');
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
        $headers = null;
        $rows = [];
        foreach ($file as $record) {
            if (! is_array($record) || $record === [null]) {
                continue;
            }
            if ($headers === null) {
                $headers = array_map('strval', $record);
                continue;
            }
            $record = array_pad(array_slice($record, 0, count($headers)), count($headers), null);
            $rows[] = array_combine($headers, $record) ?: [];
            if ($limit !== null && count($rows) >= $limit) {
                break;
            }
        }
        return $rows;
    }

    public function delete(UserDataset $dataset): void
    {
        $directory = storage_path('app/ml/users/'.$dataset->user_id.'/datasets/'.$dataset->uuid);
        DB::transaction(static function () use ($dataset): void {
            $dataset->delete();
        });
        File::deleteDirectory($directory);
    }

    /**
     * @param array<int,string> $headers
     * @param array<int,array<string,mixed>> $rows
     */
    private function writeCanonicalCsv(string $path, array $headers, array $rows): void
    {
        $handle = fopen($path, 'wb');
        if ($handle === false) {
            throw new \RuntimeException('The private dataset storage could not be created.');
        }
        try {
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, array_map(static fn (string $header): mixed => $row[$header] ?? null, $headers));
            }
        } finally {
            fclose($handle);
        }
    }

    /** @param array<string,mixed> $profile */
    private function resolveTarget(?string $target, array $profile): ?string
    {
        $headers = (array) ($profile['headers'] ?? []);
        if ($target !== null && $target !== '') {
            if (! in_array($target, $headers, true)) {
                throw ValidationException::withMessages(['target_column' => 'The selected target column does not exist in the dataset.']);
            }
            return $target;
        }
        return $profile['suggested_target'] ?? null;
    }

    /** @param array<string,mixed> $profile */
    private function resolveProblemType(?string $problemType, ?string $target, array $profile): string
    {
        $problemType = $problemType ?: (string) ($profile['detected_problem_type'] ?? 'classification');
        if (! in_array($problemType, ['classification', 'regression', 'clustering'], true)) {
            throw ValidationException::withMessages(['problem_type' => 'Choose classification, regression, or clustering.']);
        }
        if ($problemType !== 'clustering' && $target === null) {
            throw ValidationException::withMessages(['target_column' => 'A target column is required for classification and regression.']);
        }
        return $problemType;
    }

    private function cleanName(string $name): string
    {
        $name = trim(preg_replace('/\s+/', ' ', $name) ?? $name);
        return substr($name !== '' ? $name : 'Uploaded Dataset', 0, 160);
    }
}
