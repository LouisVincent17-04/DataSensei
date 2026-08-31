<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMlTrainingJob;
use App\Models\MlDataset;
use App\Models\MlModel;
use App\Models\ModelVersion;
use App\Models\TrainingJob;
use App\Models\UserDataset;
use App\Services\HybridMl\AlgorithmCatalogService;
use App\Services\HybridMl\BenchmarkComparisonService;
use App\Services\HybridMl\MlAccessService;
use App\Services\HybridMl\ModelStorageService;
use App\Services\HybridMl\PredictionService;
use App\Services\HybridMl\SystemDatasetLibrary;
use App\Services\HybridMl\UserDatasetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HybridMlApiController extends Controller
{
    public function datasets(Request $request, SystemDatasetLibrary $library): JsonResponse
    {
        return response()->json([
            'system' => $library->all(),
            'uploaded' => UserDataset::query()->where('user_id', $request->user()->id)->latest()->paginate(20),
        ]);
    }


    public function validateDataset(Request $request, UserDatasetService $service): JsonResponse
    {
        $max = (int) config('hybrid_ml.max_upload_kilobytes', 10240);
        $input = $request->validate([
            'dataset_file' => ['required', 'file', "max:{$max}", 'mimes:csv,txt,xlsx'],
            'target_column' => ['nullable', 'string', 'max:120'],
            'problem_type' => ['nullable', Rule::in(['classification', 'regression', 'clustering'])],
        ]);

        $analysis = $service->analyzeUpload(
            $input['dataset_file'],
            $input['target_column'] ?? null,
            $input['problem_type'] ?? null,
        );

        return response()->json([
            'data' => [
                'profile' => $analysis['profile'],
                'quality' => $analysis['quality'],
                'target_column' => $analysis['target_column'],
                'problem_type' => $analysis['problem_type'],
                'file_metadata' => $analysis['parsed']['metadata'] ?? [],
            ],
        ]);
    }

    public function upload(Request $request, UserDatasetService $service, MlAccessService $access): JsonResponse
    {
        $max = (int) config('hybrid_ml.max_upload_kilobytes', 10240);
        $input = $request->validate([
            'dataset_file' => ['required', 'file', "max:{$max}", 'mimes:csv,txt,xlsx'],
            'name' => ['nullable', 'string', 'max:160'],
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'target_column' => ['nullable', 'string', 'max:120'],
            'problem_type' => ['nullable', Rule::in(['classification', 'regression', 'clustering'])],
        ]);
        $classId = isset($input['class_id']) ? (int) $input['class_id'] : null;
        abort_unless($access->canUseClass($request->user(), $classId), 403);
        $dataset = $service->create($input['dataset_file'], (int) $request->user()->id, $classId, $input['name'] ?? null, $input['target_column'] ?? null, $input['problem_type'] ?? null);
        return response()->json(['data' => $dataset], 201);
    }

    public function quality(Request $request, string $type, int $id, MlAccessService $access): JsonResponse
    {
        if ($type === 'system') {
            $dataset = MlDataset::query()->findOrFail($id);
            return response()->json(['data' => $dataset->qualityReports()->latest('generated_at')->first()]);
        }
        abort_unless($type === 'user', 404);
        $dataset = UserDataset::query()->findOrFail($id);
        abort_unless($access->canViewUserDataset($request->user(), $dataset), 403);
        return response()->json(['data' => $dataset->qualityReports()->latest('generated_at')->first()]);
    }

    public function createTraining(Request $request, AlgorithmCatalogService $algorithms, MlAccessService $access): JsonResponse
    {
        $input = $request->validate([
            'dataset_type' => ['required', Rule::in(['system', 'user'])],
            'dataset_id' => ['required', 'integer'],
            'model_name' => ['required', 'string', 'max:160'],
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'problem_type' => ['required', Rule::in(['classification', 'regression', 'clustering'])],
            'algorithm_key' => ['required', 'string', 'max:64'],
            'features' => ['required', 'array', 'min:1', 'max:50'],
            'features.*' => ['string', 'max:120'],
            'target_column' => ['nullable', 'string', 'max:120'],
            'test_size' => ['nullable', 'numeric'],
            'random_state' => ['required', 'integer'],
            'cross_validation' => ['required', 'integer'],
            'scale_mode' => ['required', Rule::in(['auto', 'standard', 'none'])],
            'numeric_imputation' => ['required', Rule::in(['median', 'mean'])],
            'remove_duplicates' => ['nullable', 'boolean'],
            'parameters' => ['nullable', 'array'],
        ]);

        if ($input['dataset_type'] === 'system') {
            $dataset = MlDataset::query()->where('is_active', true)->findOrFail($input['dataset_id']);
            $version = $dataset->versions()->latest('version_number')->firstOrFail();
            $profile = (array) $version->schema_profile;
        } else {
            $dataset = UserDataset::query()->findOrFail($input['dataset_id']);
            abort_unless($access->canViewUserDataset($request->user(), $dataset), 403);
            $version = $dataset->versions()->latest('version_number')->firstOrFail();
            $profile = (array) $dataset->schema_profile;
        }
        $classId = isset($input['class_id']) ? (int) $input['class_id'] : null;
        abort_unless($access->canUseClass($request->user(), $classId), 403);
        $config = $algorithms->normalizeTrainingConfiguration($input, $profile);
        $config['generate_extended_charts'] = true;

        $job = DB::transaction(fn () => TrainingJob::create([
            'user_id' => $request->user()->id,
            'class_id' => $classId,
            'dataset_id' => $input['dataset_type'] === 'system' ? $dataset->id : null,
            'user_dataset_id' => $input['dataset_type'] === 'user' ? $dataset->id : null,
            'dataset_version_id' => $version->id,
            'uuid' => (string) Str::uuid(),
            'model_name' => trim($input['model_name']),
            'problem_type' => $config['problem_type'],
            'algorithm_key' => $config['algorithm_key'],
            'status' => 'queued',
            'progress' => 0,
            'stage' => 'Waiting for the machine-learning worker',
            'configuration' => $config,
        ]), 3);
        ProcessMlTrainingJob::dispatch($job->id)->afterCommit();
        return response()->json(['data' => $job, 'status_url' => route('api.student.ml.training.show', $job)], 202);
    }

    public function training(Request $request, TrainingJob $trainingJob, MlAccessService $access): JsonResponse
    {
        abort_unless($access->canViewTrainingJob($request->user(), $trainingJob), 403);
        return response()->json(['data' => $trainingJob->fresh(['model.currentVersion'])]);
    }

    public function model(Request $request, MlModel $model, MlAccessService $access, BenchmarkComparisonService $comparisons): JsonResponse
    {
        abort_unless($access->canViewModel($request->user(), $model), 403);
        $model->load(['dataset', 'userDataset', 'currentVersion', 'versions']);
        return response()->json(['data' => $model, 'comparison' => $model->currentVersion ? $comparisons->compare($model->currentVersion) : null]);
    }

    public function predict(Request $request, MlModel $model, MlAccessService $access, PredictionService $service): JsonResponse
    {
        abort_unless($access->canViewModel($request->user(), $model), 403);
        $input = $request->validate(['input_values' => ['required', 'array']]);
        return response()->json(['data' => $service->predict($model->currentVersion()->firstOrFail(), (int) $request->user()->id, $input['input_values'])]);
    }

    public function destroyModel(
        Request $request,
        MlModel $model,
        MlAccessService $access,
        ModelStorageService $storage,
    ): JsonResponse {
        abort_unless(
            $access->canViewModel($request->user(), $model)
            && (int) $model->user_id === (int) $request->user()->id
            && ! $model->isSystemModel(),
            403
        );
        $storage->deleteUserModel($model);

        return response()->json(['message' => 'Model and all versions were deleted.']);
    }

    public function activateVersion(
        Request $request,
        MlModel $model,
        ModelVersion $version,
        MlAccessService $access,
        ModelStorageService $storage,
    ): JsonResponse {
        abort_unless(
            $access->canViewModel($request->user(), $model)
            && (int) $model->user_id === (int) $request->user()->id,
            403
        );
        $storage->rollback($model, $version);

        return response()->json([
            'data' => $model->fresh(['currentVersion', 'versions']),
            'message' => $version->version_label.' is now active.',
        ]);
    }

    public function visualization(
        Request $request,
        ModelVersion $version,
        string $chart,
        MlAccessService $access,
    ): BinaryFileResponse {
        $version->loadMissing('model');
        abort_unless($version->model && $access->canViewModel($request->user(), $version->model), 403);
        $path = data_get($version->visualizations, $chart);
        abort_unless(is_string($path) && str_ends_with(strtolower($path), '.png'), 404);
        $absolute = storage_path('app/'.$path);
        abort_unless(is_file($absolute), 404);

        return $request->boolean('download')
            ? response()->download($absolute, Str::slug($version->model->name.'-'.$version->version_label.'-'.$chart).'.png')
            : response()->file($absolute, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'private, max-age=300',
            ]);
    }

}
