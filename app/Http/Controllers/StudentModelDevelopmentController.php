<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMlTrainingJob;
use App\Models\DatasetVersion;
use App\Models\MlDataset;
use App\Models\MlModel;
use App\Models\ModelVersion;
use App\Models\TrainingJob;
use App\Models\UserDataset;
use App\Services\HybridMl\AlgorithmCatalogService;
use App\Services\HybridMl\BenchmarkComparisonService;
use App\Services\HybridMl\MlAccessService;
use App\Services\HybridMl\MlWorkerSupervisor;
use App\Services\HybridMl\ModelStorageService;
use App\Services\HybridMl\PredictionService;
use App\Services\HybridMl\PredefinedDatasetRecommendationService;
use App\Services\HybridMl\SystemDatasetLibrary;
use App\Services\HybridMl\UserDatasetService;
use App\Support\ModelDevelopmentRoadmap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class StudentModelDevelopmentController extends Controller
{
    public function index(SystemDatasetLibrary $library): View
    {
        $user = Auth::user();
        $systemDatasets = $library->all();
        $trainingJobs = TrainingJob::query()->where('user_id', $user->id)->with(['dataset', 'userDataset', 'model'])->latest()->limit(15)->get();
        $models = MlModel::query()->where('user_id', $user->id)->with(['currentVersion', 'dataset', 'userDataset'])->latest()->limit(20)->get();

        // Beginners get one obvious starting point: the smallest built-in dataset with a recommended setup.
        $starterDataset = $systemDatasets
            ->filter(fn (MlDataset $dataset): bool => ! empty(data_get($dataset->metadata, 'recommended_setup')))
            ->sortBy('row_count')
            ->first();

        return view('student.model-development.index', [
            'systemDatasets' => $systemDatasets,
            'starterDataset' => $starterDataset,
            'activeTrainingJob' => $trainingJobs->first(fn (TrainingJob $job): bool => ! $job->isTerminal()),
            'userDatasets' => UserDataset::query()->where('user_id', $user->id)->latest()->limit(30)->get(),
            'models' => $models,
            'trainingJobs' => $trainingJobs,
            'classes' => $user->classesAsStudent()->active()->orderBy('name')->get(['classes.id', 'classes.name', 'classes.section']),
            'maxUploadKilobytes' => (int) config('hybrid_ml.max_upload_kilobytes', 10240),
        ]);
    }

    public function uploadDataset(Request $request, UserDatasetService $datasets, MlAccessService $access): RedirectResponse
    {
        $max = (int) config('hybrid_ml.max_upload_kilobytes', 10240);
        $input = $request->validate([
            'dataset_file' => ['required', 'file', "max:{$max}", 'mimes:csv,txt,xlsx'],
            'name' => ['nullable', 'string', 'max:160'],
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'target_column' => ['nullable', 'string', 'max:120'],
            'problem_type' => ['nullable', Rule::in(['classification', 'regression', 'clustering'])],
        ], [
            'dataset_file.required' => 'Choose a CSV or XLSX dataset.',
            'dataset_file.mimes' => 'Only CSV and XLSX datasets are accepted.',
        ]);

        $user = $request->user();
        $classId = isset($input['class_id']) ? (int) $input['class_id'] : null;
        abort_unless($access->canUseClass($user, $classId), 403);
        $dataset = $datasets->create(
            $input['dataset_file'],
            (int) $user->id,
            $classId,
            $input['name'] ?? null,
            $input['target_column'] ?? null,
            $input['problem_type'] ?? null,
        );

        return redirect()->route('student.model-development.user-datasets.show', $dataset)
            ->with('success', 'Dataset validated, sanitized, and analyzed successfully.');
    }

    public function showSystemDataset(MlDataset $dataset, SystemDatasetLibrary $library): View
    {
        abort_unless($dataset->is_active, 404);
        $dataset->load([
            'versions',
            'qualityReports' => fn ($query) => $query->latest('generated_at'),
            'benchmarks' => fn ($query) => $query->with(['model.currentVersion'])->orderBy('benchmark_rank'),
        ]);

        return view('student.model-development.dataset', [
            'datasetType' => 'system',
            'dataset' => $dataset,
            'preview' => $library->preview($dataset),
            'profile' => (array) ($dataset->versions->first()?->schema_profile ?? []),
            'quality' => $dataset->qualityReports->first(),
            'benchmarks' => $dataset->benchmarks,
        ]);
    }

    public function showUserDataset(UserDataset $dataset, UserDatasetService $datasets, MlAccessService $access): View
    {
        abort_unless($access->canViewUserDataset(Auth::user(), $dataset), 403);
        $dataset->load(['versions', 'qualityReports', 'classRoom', 'models.currentVersion']);

        return view('student.model-development.dataset', [
            'datasetType' => 'user',
            'dataset' => $dataset,
            'preview' => $datasets->preview($dataset),
            'profile' => (array) $dataset->schema_profile,
            'quality' => $dataset->qualityReports->first(),
            'benchmarks' => collect(),
        ]);
    }

    public function downloadSystemDataset(MlDataset $dataset): BinaryFileResponse
    {
        abort_unless($dataset->is_active, 404);
        $path = storage_path('app/'.$dataset->storage_path);
        abort_unless(is_file($path), 404);
        return response()->download($path, $dataset->slug.'.csv', ['Content-Type' => 'text/csv']);
    }

    public function downloadUserDataset(UserDataset $dataset, MlAccessService $access): BinaryFileResponse
    {
        abort_unless($access->canViewUserDataset(Auth::user(), $dataset), 403);
        $path = storage_path('app/'.$dataset->storage_path);
        abort_unless(is_file($path), 404);
        return response()->download($path, Str::slug($dataset->name).'.csv', ['Content-Type' => 'text/csv']);
    }

    public function destroyUserDataset(UserDataset $dataset, UserDatasetService $datasets, MlAccessService $access): RedirectResponse
    {
        abort_unless((int) $dataset->user_id === (int) Auth::id() && $access->canViewUserDataset(Auth::user(), $dataset), 403);
        if ($dataset->models()->exists() || TrainingJob::query()->where('user_dataset_id', $dataset->id)->exists()) {
            return back()->withErrors(['dataset' => 'This dataset is linked to training history and cannot be deleted.']);
        }
        $datasets->delete($dataset);
        return redirect()->route('student.model-development.index')->with('success', 'Dataset deleted.');
    }

    public function wizard(
        Request $request,
        AlgorithmCatalogService $algorithms,
        MlAccessService $access,
        PredefinedDatasetRecommendationService $recommendations,
    ): View {
        [$type, $dataset, $version, $profile] = $this->resolveDatasetSelection($request, $access);
        $recommendation = $type === 'system' ? $recommendations->forDataset($dataset, $profile) : null;
        $resumeJob = null;
        $resumeConfiguration = [];

        if ($request->integer('training_job_id') > 0) {
            $resumeJob = TrainingJob::query()->findOrFail($request->integer('training_job_id'));
            abort_unless($access->canViewTrainingJob($request->user(), $resumeJob), 403);

            $matchesDataset = $type === 'system'
                ? (int) $resumeJob->dataset_id === (int) $dataset->id
                : (int) $resumeJob->user_dataset_id === (int) $dataset->id;
            abort_unless($matchesDataset, 404);
            $resumeConfiguration = (array) $resumeJob->configuration;
        }

        $problemType = (string) (
            $request->query('problem_type')
            ?: ($resumeConfiguration['problem_type'] ?? null)
            ?: ($recommendation['problem_type'] ?? null)
            ?: data_get($profile, 'detected_problem_type')
            ?: $dataset->problem_type
            ?: 'classification'
        );
        if (! in_array($problemType, ['classification', 'regression', 'clustering'], true)) {
            $problemType = 'classification';
        }

        $algorithmsByProblem = [];
        $learningGuidesByProblem = [];
        foreach (['classification', 'regression', 'clustering'] as $supportedProblemType) {
            $algorithmsByProblem[$supportedProblemType] = $algorithms->active($supportedProblemType);
            $learningGuidesByProblem[$supportedProblemType] = $algorithms->learningGuides(
                $supportedProblemType,
                $profile
            );
        }

        return view('student.model-development.wizard', [
            'datasetType' => $type,
            'dataset' => $dataset,
            'datasetVersion' => $version,
            'profile' => $profile,
            'rowCount' => (int) ($profile['row_count'] ?? $dataset->row_count ?? 0),
            'problemType' => $problemType,
            'recommendation' => $recommendation,
            'algorithmsByProblem' => $algorithmsByProblem,
            'learningGuidesByProblem' => $learningGuidesByProblem,
            'classes' => $request->user()->classesAsStudent()->active()->orderBy('name')->get(['classes.id', 'classes.name', 'classes.section']),
            'allowedTestSizes' => config('hybrid_ml.allowed_test_sizes', [.2, .25, .3]),
            'allowedRandomStates' => config('hybrid_ml.allowed_random_states', [42]),
            'cvFolds' => config('hybrid_ml.cross_validation_folds', [0, 3, 5, 10]),
            'resumeJob' => $resumeJob,
            'resumeConfiguration' => $resumeConfiguration,
            'requestedStep' => ModelDevelopmentRoadmap::normalizeAuthoringStep($request->query('step'), 2),
        ]);
    }

    public function train(
        Request $request,
        AlgorithmCatalogService $algorithms,
        MlAccessService $access,
        PredefinedDatasetRecommendationService $recommendations,
    ): RedirectResponse {
        [$type, $dataset, $version, $profile] = $this->resolveDatasetSelection($request, $access, true);
        $input = $request->validate([
            'model_name' => ['required', 'string', 'max:160'],
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'problem_type' => ['required', Rule::in(['classification', 'regression', 'clustering'])],
            'algorithm_key' => ['required', 'string', 'max:64'],
            'features' => ['required', 'array', 'min:1', 'max:50'],
            'features.*' => ['required', 'string', 'max:120'],
            'target_column' => ['nullable', 'string', 'max:120'],
            'test_size' => ['nullable', 'numeric'],
            'random_state' => ['required', 'integer'],
            'cross_validation' => ['required', 'integer'],
            'scale_mode' => ['required', Rule::in(['auto', 'standard', 'none'])],
            'numeric_imputation' => ['required', Rule::in(['median', 'mean'])],
            'remove_duplicates' => ['nullable', 'boolean'],
            'parameters' => ['nullable', 'array'],
        ]);

        $classId = isset($input['class_id']) ? (int) $input['class_id'] : null;
        abort_unless($access->canUseClass($request->user(), $classId), 403);
        $configuration = $algorithms->normalizeTrainingConfiguration($input, $profile);
        $configuration['generate_extended_charts'] = true;

        if ($type === 'system') {
            $recommendation = $recommendations->forDataset($dataset, $profile);
            if ($recommendation !== null) {
                $comparison = $recommendations->compareToConfiguration($recommendation, $configuration);
                $configuration['recommendation'] = array_merge($recommendation, $comparison);
            }
        }

        $job = DB::transaction(function () use ($request, $type, $dataset, $version, $classId, $input, $configuration): TrainingJob {
            return TrainingJob::create([
                'user_id' => $request->user()->id,
                'class_id' => $classId,
                'dataset_id' => $type === 'system' ? $dataset->id : null,
                'user_dataset_id' => $type === 'user' ? $dataset->id : null,
                'dataset_version_id' => $version->id,
                'uuid' => (string) Str::uuid(),
                'model_name' => trim($input['model_name']),
                'problem_type' => $configuration['problem_type'],
                'algorithm_key' => $configuration['algorithm_key'],
                'status' => TrainingJob::STATUS_QUEUED,
                'progress' => 0,
                'stage' => 'Waiting for the machine-learning worker',
                'configuration' => $configuration,
            ]);
        }, 3);

        ProcessMlTrainingJob::dispatch($job->id, $job->uuid)->afterCommit();

        // Start a worker if none is running, so the job does not wait at 0%.
        app(MlWorkerSupervisor::class)->ensureWorkerFor($job);

        return redirect()->route('student.model-development.training.show', $job)
            ->with('success', 'Training was queued. This page will update as the model is processed.');
    }

    public function showTrainingJob(TrainingJob $trainingJob, MlAccessService $access, MlWorkerSupervisor $workers): View
    {
        abort_unless($access->canViewTrainingJob(Auth::user(), $trainingJob), 403);
        $trainingJob->load(['dataset', 'userDataset', 'model.currentVersion']);
        return view('student.model-development.training-job', [
            'job' => $trainingJob,
            'worker' => $workers->ensureWorkerFor($trainingJob),
        ]);
    }

    public function trainingStatus(TrainingJob $trainingJob, MlAccessService $access, MlWorkerSupervisor $workers): JsonResponse
    {
        abort_unless($access->canViewTrainingJob(Auth::user(), $trainingJob), 403);
        $trainingJob->refresh();
        $worker = $workers->ensureWorkerFor($trainingJob);
        return response()->json([
            'worker_state' => $worker['state'],
            'worker_message' => $worker['message'],
            'id' => $trainingJob->id,
            'status' => $trainingJob->status,
            'progress' => $trainingJob->progress,
            'stage' => $trainingJob->stage,
            'attempt_number' => $trainingJob->attempt_number,
            'next_retry_at' => optional($trainingJob->next_retry_at)->toIso8601String(),
            'error' => $trainingJob->error_message,
            'model_url' => $trainingJob->ml_model_id ? route('student.model-development.models.show', $trainingJob->ml_model_id) : null,
            'evaluation_url' => $trainingJob->ml_model_id
                ? route('student.model-development.models.show', [
                    'model' => $trainingJob->ml_model_id,
                    'step' => 'evaluate',
                ])
                : null,
            'metrics' => (array) data_get($trainingJob->result, 'metrics', []),
            'duration_seconds' => $trainingJob->duration_ms !== null
                ? round($trainingJob->duration_ms / 1000, 3)
                : null,
            'updated_at' => optional($trainingJob->updated_at)->toIso8601String(),
        ]);
    }

    public function showModel(
        Request $request,
        MlModel $model,
        MlAccessService $access,
        BenchmarkComparisonService $comparisons,
    ): View
    {
        abort_unless($access->canViewModel(Auth::user(), $model), 403);
        $model->load([
            'dataset',
            'userDataset',
            'currentVersion.trainingJob',
            'versions.trainingJob',
            'trainingJobs',
            'currentVersion.predictions' => fn ($query) => $query
                ->where('user_id', Auth::id())
                ->latest()
                ->limit(10),
        ]);
        abort_unless($model->currentVersion, 404);

        $hasPrediction = $model->currentVersion->predictions->isNotEmpty();
        $requestedResultStep = (string) $request->query('step', 'evaluate');
        $roadmapCurrent = ModelDevelopmentRoadmap::resultStep($requestedResultStep, $hasPrediction);

        return view('student.model-development.model', [
            'model' => $model,
            'version' => $model->currentVersion,
            'comparison' => $model->isSystemModel() ? null : $comparisons->compare($model->currentVersion),
            'hasPrediction' => $hasPrediction,
            'roadmapCurrent' => $roadmapCurrent,
            'roadmapNotice' => $requestedResultStep === 'save' && ! $hasPrediction
                ? 'Make at least one prediction before completing the Save Model stage.'
                : null,
        ]);
    }


    public function destroyModel(MlModel $model, MlAccessService $access, ModelStorageService $storage): RedirectResponse
    {
        abort_unless(
            $access->canViewModel(Auth::user(), $model)
            && (int) $model->user_id === (int) Auth::id()
            && ! $model->isSystemModel(),
            403
        );
        $storage->deleteUserModel($model);

        return redirect()->route('student.model-development.index')->with('success', 'Model and all of its versions were deleted.');
    }

    public function rollbackModel(MlModel $model, ModelVersion $version, MlAccessService $access, ModelStorageService $storage): RedirectResponse
    {
        abort_unless($access->canViewModel(Auth::user(), $model) && (int) $model->user_id === (int) Auth::id(), 403);
        $storage->rollback($model, $version);
        return back()->with('success', $version->version_label.' is now the active model version.');
    }

    public function predict(Request $request, MlModel $model, MlAccessService $access, PredictionService $predictions): RedirectResponse
    {
        abort_unless($access->canViewModel($request->user(), $model), 403);
        $version = $model->currentVersion()->firstOrFail();
        $input = $request->validate(
            ['input_values' => ['required', 'array']],
            ['input_values.required' => 'Enter at least one value before generating a prediction.']
        );

        try {
            $result = $predictions->predict($version, (int) $request->user()->id, (array) $input['input_values']);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            // Runner or artifact problems should not show a server error page to a student.
            report($exception);

            return redirect()
                ->route('student.model-development.models.show', ['model' => $model, 'step' => 'predict'])
                ->withInput()
                ->withErrors(['prediction' => 'The prediction could not be completed right now. Check your values and try again. If it keeps failing, ask your instructor to confirm the machine-learning worker is running.']);
        }

        return redirect()
            ->route('student.model-development.models.show', ['model' => $model, 'step' => 'save'])
            ->with('prediction_result', $result)
            ->with('success', 'Prediction completed. Your trained model is saved in Model History.');
    }

    public function visualization(Request $request, ModelVersion $version, string $chart, MlAccessService $access): BinaryFileResponse
    {
        $version->loadMissing('model');
        abort_unless($version->model && $access->canViewModel(Auth::user(), $version->model), 403);
        $path = data_get($version->visualizations, $chart);
        abort_unless(is_string($path) && str_ends_with(strtolower($path), '.png'), 404);
        $absolute = storage_path('app/'.$path);
        abort_unless(is_file($absolute), 404);
        return $request->boolean('download')
            ? response()->download($absolute, Str::slug($version->model->name.'-'.$version->version_label.'-'.$chart).'.png')
            : response()->file($absolute, ['Content-Type' => 'image/png', 'Cache-Control' => 'private, max-age=300']);
    }

    public function report(MlModel $model, MlAccessService $access, BenchmarkComparisonService $comparisons): View
    {
        abort_unless($access->canViewModel(Auth::user(), $model), 403);
        $model->load(['dataset.qualityReports', 'userDataset.qualityReports', 'currentVersion']);
        abort_unless($model->currentVersion, 404);
        return view('student.model-development.report', [
            'model' => $model,
            'version' => $model->currentVersion,
            'quality' => $model->dataset?->qualityReports->first() ?: $model->userDataset?->qualityReports->first(),
            'comparison' => $model->isSystemModel() ? null : $comparisons->compare($model->currentVersion),
        ]);
    }

    /** @return array{0:string,1:MlDataset|UserDataset,2:DatasetVersion,3:array<string,mixed>} */
    private function resolveDatasetSelection(Request $request, MlAccessService $access, bool $fromBody = false): array
    {
        $source = $fromBody ? $request->all() : $request->query();
        $type = (string) ($source['dataset_type'] ?? '');
        $id = (int) ($source['dataset_id'] ?? 0);
        if ($type === 'system') {
            $dataset = MlDataset::query()->where('is_active', true)->findOrFail($id);
            $version = $dataset->versions()->latest('version_number')->firstOrFail();
            return [$type, $dataset, $version, (array) $version->schema_profile];
        }
        if ($type === 'user') {
            $dataset = UserDataset::query()->findOrFail($id);
            abort_unless($access->canViewUserDataset($request->user(), $dataset), 403);
            $version = $dataset->versions()->latest('version_number')->firstOrFail();
            return [$type, $dataset, $version, (array) $dataset->schema_profile];
        }
        throw ValidationException::withMessages(['dataset_type' => 'Select a built-in or uploaded dataset.']);
    }
}
