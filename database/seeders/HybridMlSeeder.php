<?php

namespace Database\Seeders;

use App\Models\BenchmarkModel;
use App\Models\DatasetVersion;
use App\Models\MlDataset;
use App\Models\MlModel;
use App\Models\ModelVersion;
use App\Models\QualityReport;
use App\Services\HybridMl\AlgorithmCatalogService;
use App\Services\HybridMl\DatasetProfiler;
use App\Services\HybridMl\TabularDatasetReader;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class HybridMlSeeder extends Seeder
{
    public function run(): void
    {
        $manifestPath = storage_path('app/'.config('hybrid_ml.system_manifest_path', 'ml/system/manifest.json'));
        if (! is_file($manifestPath)) {
            throw new RuntimeException('Hybrid ML system assets are missing. Run php artisan ml:build-system-assets first.');
        }

        $manifest = json_decode((string) File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($manifest['datasets'] ?? null)) {
            throw new RuntimeException('The Hybrid ML manifest is invalid.');
        }

        app(AlgorithmCatalogService::class)->syncDatabaseCatalog();

        foreach ($manifest['datasets'] as $entry) {
            $schemaProfile = $this->buildSchemaProfile($entry);

            DB::transaction(function () use ($entry, $schemaProfile): void {
                $dataset = MlDataset::query()->updateOrCreate(
                    ['slug' => $entry['slug']],
                    [
                        'name' => $entry['name'],
                        'description' => $entry['description'] ?? null,
                        'source_name' => $entry['source_name'] ?? null,
                        'source_url' => $entry['source_url'] ?? null,
                        'license_name' => $entry['license_name'] ?? null,
                        'storage_path' => $entry['storage_path'],
                        'row_count' => (int) $entry['row_count'],
                        'column_count' => (int) $entry['column_count'],
                        'target_column' => $entry['target'] ?? null,
                        'problem_type' => $entry['problem_type'],
                        'feature_list' => $entry['features'] ?? [],
                        'metadata' => array_merge((array) ($entry['metadata'] ?? []), [
                            'generated_at' => $entry['generated_at'] ?? null,
                            'primary_algorithm' => $entry['primary_algorithm'] ?? null,
                            'recommended_setup' => (array) ($entry['recommended_setup'] ?? []),
                            'system_read_only' => true,
                        ]),
                        'version_label' => $entry['version_label'] ?? 'v1',
                        'is_active' => true,
                    ]
                );

                $datasetVersion = DatasetVersion::query()->updateOrCreate(
                    ['dataset_id' => $dataset->id, 'version_number' => 1],
                    [
                        'user_dataset_id' => null,
                        'created_by' => null,
                        'version_label' => $entry['version_label'] ?? 'v1',
                        'checksum_sha256' => $entry['checksum_sha256'],
                        'storage_path' => $entry['storage_path'],
                        'row_count' => (int) $entry['row_count'],
                        'column_count' => (int) $entry['column_count'],
                        'schema_profile' => $schemaProfile,
                        'metadata' => ['source' => 'system', 'read_only' => true],
                    ]
                );

                $quality = (array) ($entry['metadata']['quality'] ?? []);
                QualityReport::query()->updateOrCreate(
                    ['dataset_id' => $dataset->id, 'dataset_version_id' => $datasetVersion->id, 'user_id' => null],
                    [
                        'user_dataset_id' => null,
                        'quality_score' => (float) ($quality['quality_score'] ?? 0),
                        'summary' => $quality['summary'] ?? [],
                        'column_analysis' => $quality['column_analysis'] ?? [],
                        'recommendations' => $quality['recommendations'] ?? [],
                        'generated_at' => now(),
                    ]
                );

                foreach ((array) ($entry['models'] ?? []) as $modelEntry) {
                    $model = MlModel::withTrashed()->firstOrNew([
                        'pipeline_type' => 'system',
                        'dataset_id' => $dataset->id,
                        'algorithm_key' => $modelEntry['algorithm_key'],
                    ]);
                    if ($model->exists && $model->trashed()) {
                        $model->restore();
                    }
                    $model->fill([
                        'user_id' => null,
                        'class_id' => null,
                        'user_dataset_id' => null,
                        'identity_key' => hash('sha256', 'system|'.$dataset->id.'|'.$modelEntry['algorithm_key']),
                        'uuid' => $model->uuid ?: (string) Str::uuid(),
                        'name' => $dataset->name.' · '.str($modelEntry['algorithm_key'])->replace('_', ' ')->title(),
                        'problem_type' => $dataset->problem_type,
                        'status' => 'ready',
                        'is_read_only' => true,
                        'metadata' => [
                            'benchmark_rank' => $modelEntry['benchmark_rank'] ?? null,
                            'is_primary' => (bool) ($modelEntry['is_primary'] ?? false),
                            'training_summary' => $modelEntry['training_summary'] ?? [],
                        ],
                    ])->save();

                    $version = ModelVersion::query()->updateOrCreate(
                        ['ml_model_id' => $model->id, 'version_number' => 1],
                        [
                            'training_job_id' => null,
                            'dataset_version_id' => $datasetVersion->id,
                            'created_by' => null,
                            'version_label' => 'v1',
                            'artifact_path' => $modelEntry['artifact_path'],
                            'metadata_path' => $modelEntry['metadata_path'] ?? null,
                            'metrics' => $modelEntry['metrics'] ?? [],
                            'hyperparameters' => [
                                'parameters' => $modelEntry['parameters'] ?? [],
                                'preprocessing' => $modelEntry['preprocessing'] ?? [],
                            ],
                            'feature_names' => $entry['features'] ?? [],
                            'target_column' => $entry['target'] ?? null,
                            'visualizations' => $modelEntry['visualizations'] ?? [],
                            'explanations' => [
                                'feature_importance' => $modelEntry['feature_importance'] ?? [],
                                'prediction_schema' => $modelEntry['prediction_schema'] ?? [],
                                'educational' => [
                                    'This is a read-only benchmark trained from the curated system dataset.',
                                    'Use the same dataset and algorithm in the training wizard to compare your configuration fairly.',
                                ],
                            ],
                            'training_time_ms' => (int) ($modelEntry['training_time_ms'] ?? 0),
                            'python_version' => $modelEntry['python_version'] ?? null,
                            'sklearn_version' => $modelEntry['sklearn_version'] ?? null,
                            'status' => 'ready',
                            'is_active' => true,
                        ]
                    );

                    $model->update(['current_version_id' => $version->id]);
                    BenchmarkModel::query()->updateOrCreate(
                        ['dataset_id' => $dataset->id, 'algorithm_key' => $modelEntry['algorithm_key']],
                        [
                            'ml_model_id' => $model->id,
                            'model_version_id' => $version->id,
                            'benchmark_rank' => (int) ($modelEntry['benchmark_rank'] ?? 1),
                            'is_primary' => (bool) ($modelEntry['is_primary'] ?? false),
                            'metrics' => $modelEntry['metrics'] ?? [],
                        ]
                    );
                }
            }, 3);
        }
    }
    /** @param array<string,mixed> $entry @return array<string,mixed> */
    private function buildSchemaProfile(array $entry): array
    {
        $relativePath = (string) ($entry['storage_path'] ?? '');
        $absolutePath = storage_path('app/'.$relativePath);
        if (! is_file($absolutePath)) {
            throw new RuntimeException('A bundled Hybrid ML dataset is missing: '.$relativePath);
        }

        $parsed = app(TabularDatasetReader::class)->readCsv($absolutePath);
        $profile = app(DatasetProfiler::class)->profile($parsed['headers'], $parsed['rows']);
        $profile['suggested_target'] = $entry['target'] ?? $profile['suggested_target'] ?? null;
        $profile['detected_problem_type'] = $entry['problem_type'] ?? $profile['detected_problem_type'] ?? 'classification';

        return $profile;
    }

}
