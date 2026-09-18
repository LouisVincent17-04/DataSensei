<?php

namespace App\Services\HybridMl;

use App\Models\MlDataset;
use App\Models\QualityReport;

class SystemDatasetLibrary
{
    public function all()
    {
        return MlDataset::query()
            ->where('is_active', true)
            ->with(['benchmarks' => fn ($query) => $query->with(['model.currentVersion'])->orderBy('benchmark_rank')])
            ->orderBy('name')
            ->get();
    }

    public function preview(MlDataset $dataset, ?int $limit = null): array
    {
        $limit ??= max(5, (int) config('hybrid_ml.preview_rows', 20));
        // Same base directory the download route and training job use (storage/app/...).
        $path = storage_path('app/'.ltrim((string) $dataset->storage_path, '/\\'));
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
            if (count($rows) >= $limit) {
                break;
            }
        }
        return $rows;
    }

    public function qualityReport(MlDataset $dataset): ?QualityReport
    {
        return $dataset->qualityReports()->latest('generated_at')->first();
    }
}
