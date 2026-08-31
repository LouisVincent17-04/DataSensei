<?php

namespace App\Services\HybridMl;

use App\Models\MlModel;
use App\Models\ModelVersion;
use App\Models\QualityReport;
use App\Models\TrainingJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class ModelStorageService
{
    public function __construct(private readonly EducationalExplanationService $explanations) {}

    /**
     * @param array<string,mixed> $result
     */
    public function storeCompletedTraining(TrainingJob $job, array $result, string $outputDirectory): ModelVersion
    {
        if (! is_file($outputDirectory.DIRECTORY_SEPARATOR.'model.joblib')) {
            throw new RuntimeException('The training result does not contain a model artifact.');
        }

        $identityKey = $this->identityKey($job);
        [$model, $version] = DB::transaction(function () use ($job, $identityKey): array {
            $candidate = MlModel::query()->firstOrCreate(
                ['identity_key' => $identityKey],
                [
                    'user_id' => $job->user_id,
                    'class_id' => $job->class_id,
                    'dataset_id' => $job->dataset_id,
                    'user_dataset_id' => $job->user_dataset_id,
                    'uuid' => (string) Str::uuid(),
                    'name' => $job->model_name,
                    'pipeline_type' => 'user',
                    'problem_type' => $job->problem_type,
                    'algorithm_key' => $job->algorithm_key,
                    'status' => 'processing',
                    'is_read_only' => false,
                    'metadata' => ['created_from_training_job' => $job->uuid],
                ]
            );

            // Serialize version-number allocation for concurrent jobs targeting
            // the same logical model. The unique identity key prevents a second
            // logical model from being created during a race.
            $model = MlModel::query()->whereKey($candidate->id)->lockForUpdate()->firstOrFail();
            if ($model->pipeline_type !== 'user' || (int) $model->user_id !== (int) $job->user_id) {
                throw new RuntimeException('The logical model identity is not owned by this training job.');
            }

            $next = ((int) $model->versions()->max('version_number')) + 1;
            $relativeDirectory = "ml/users/{$job->user_id}/models/{$model->uuid}/v{$next}";
            $version = ModelVersion::create([
                'ml_model_id' => $model->id,
                'training_job_id' => $job->id,
                'dataset_version_id' => $job->dataset_version_id,
                'created_by' => $job->user_id,
                'version_number' => $next,
                'version_label' => 'v'.$next,
                'artifact_path' => $relativeDirectory.'/model.joblib',
                'metadata_path' => $relativeDirectory.'/metadata.json',
                'metrics' => [],
                'hyperparameters' => [],
                'feature_names' => [],
                'visualizations' => [],
                'explanations' => [],
                'status' => 'processing',
                'is_active' => false,
            ]);

            $job->update(['ml_model_id' => $model->id]);
            return [$model, $version];
        }, 3);

        $relativeDirectory = dirname($version->artifact_path);
        $absoluteDirectory = storage_path('app/'.$relativeDirectory);
        File::ensureDirectoryExists($absoluteDirectory, 0755, true);

        try {
            File::copy($outputDirectory.DIRECTORY_SEPARATOR.'model.joblib', $absoluteDirectory.DIRECTORY_SEPARATOR.'model.joblib');
            if (is_file($outputDirectory.DIRECTORY_SEPARATOR.'metadata.json')) {
                File::copy($outputDirectory.DIRECTORY_SEPARATOR.'metadata.json', $absoluteDirectory.DIRECTORY_SEPARATOR.'metadata.json');
            }

            $visualizations = [];
            foreach ((array) ($result['visualizations'] ?? []) as $key => $filename) {
                $safeName = basename((string) $filename);
                $source = $outputDirectory.DIRECTORY_SEPARATOR.$safeName;
                if (! is_file($source) || strtolower(pathinfo($safeName, PATHINFO_EXTENSION)) !== 'png') {
                    continue;
                }
                File::copy($source, $absoluteDirectory.DIRECTORY_SEPARATOR.$safeName);
                $visualizations[$key] = $relativeDirectory.'/'.$safeName;
            }

            $qualitySummary = $this->qualitySummary($job);
            $educational = $this->explanations->explain(
                $job->problem_type,
                $job->algorithm_key,
                (array) ($result['metrics'] ?? []),
                $qualitySummary,
                (array) ($result['training_summary'] ?? [])
            );

            DB::transaction(function () use ($job, $model, $version, $result, $visualizations, $educational): void {
                ModelVersion::query()
                    ->where('ml_model_id', $model->id)
                    ->where('id', '!=', $version->id)
                    ->update(['is_active' => false]);

                $version->update([
                    'metrics' => $result['metrics'] ?? [],
                    'hyperparameters' => [
                        'parameters' => $result['parameters'] ?? [],
                        'preprocessing' => $result['preprocessing'] ?? [],
                        'configuration' => $job->configuration,
                    ],
                    'feature_names' => $result['features'] ?? [],
                    'target_column' => $result['target_column'] ?? null,
                    'visualizations' => $visualizations,
                    'explanations' => [
                        'educational' => $educational,
                        'feature_importance' => $result['feature_importance'] ?? [],
                        'prediction_schema' => $result['prediction_schema'] ?? [],
                    ],
                    'training_time_ms' => (int) ($result['training_time_ms'] ?? $job->duration_ms ?? 0),
                    'python_version' => $result['python_version'] ?? null,
                    'sklearn_version' => $result['sklearn_version'] ?? null,
                    'status' => 'ready',
                    'is_active' => true,
                ]);

                $model->update([
                    'current_version_id' => $version->id,
                    'status' => 'ready',
                    'class_id' => $job->class_id,
                    'metadata' => array_merge((array) $model->metadata, [
                        'last_training_job' => $job->uuid,
                        'latest_version' => $version->version_label,
                    ]),
                ]);

                $job->update([
                    'ml_model_id' => $model->id,
                    'status' => 'completed',
                    'progress' => 100,
                    'stage' => 'Training completed',
                    'result' => [
                        'model_id' => $model->id,
                        'model_uuid' => $model->uuid,
                        'version_id' => $version->id,
                        'version_label' => $version->version_label,
                        'metrics' => $result['metrics'] ?? [],
                    ],
                    'error_message' => null,
                    'duration_ms' => (int) ($result['training_time_ms'] ?? $job->duration_ms ?? 0),
                    'finished_at' => now(),
                ]);
            }, 3);

            return $version->fresh(['model', 'trainingJob', 'datasetVersion']);
        } catch (\Throwable $exception) {
            File::deleteDirectory($absoluteDirectory);
            DB::transaction(function () use ($model, $version): void {
                $version->delete();
                if (! $model->versions()->exists()) {
                    $model->forceDelete();
                } else {
                    $model->update(['status' => 'failed']);
                }
            });
            throw $exception;
        }
    }


    public function deleteUserModel(MlModel $model): void
    {
        if ($model->isSystemModel() || $model->is_read_only || $model->user_id === null) {
            throw new RuntimeException('System benchmark models cannot be deleted.');
        }

        $directory = storage_path('app/ml/users/'.$model->user_id.'/models/'.$model->uuid);
        DB::transaction(function () use ($model): void {
            $model->update(['current_version_id' => null]);
            $model->forceDelete();
        }, 3);
        File::deleteDirectory($directory);
    }

    public function rollback(MlModel $model, ModelVersion $version): void
    {
        if ((int) $version->ml_model_id !== (int) $model->id || $model->is_read_only) {
            throw new RuntimeException('This model version cannot be activated.');
        }
        if ($version->status !== 'ready' || ! is_file(storage_path('app/'.$version->artifact_path))) {
            throw new RuntimeException('The selected model version is not available.');
        }

        DB::transaction(function () use ($model, $version): void {
            ModelVersion::query()->where('ml_model_id', $model->id)->update(['is_active' => false]);
            $version->update(['is_active' => true]);
            $model->update(['current_version_id' => $version->id, 'status' => 'ready']);
        });
    }

    private function identityKey(TrainingJob $job): string
    {
        $datasetScope = $job->dataset_id
            ? 'system:'.$job->dataset_id
            : 'user:'.$job->user_dataset_id;

        return hash('sha256', implode('|', [
            'user',
            (string) $job->user_id,
            (string) ($job->class_id ?? 0),
            $datasetScope,
            strtolower(trim($job->algorithm_key)),
            strtolower(trim($job->model_name)),
        ]));
    }

    /** @return array<string,mixed> */
    private function qualitySummary(TrainingJob $job): array
    {
        $report = QualityReport::query()
            ->when($job->dataset_id, fn ($query) => $query->where('dataset_id', $job->dataset_id))
            ->when($job->user_dataset_id, fn ($query) => $query->where('user_dataset_id', $job->user_dataset_id))
            ->latest('generated_at')
            ->first();
        return (array) ($report?->summary ?? []);
    }
}
