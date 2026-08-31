<?php

namespace App\Http\Controllers;

use App\Models\StudentDataToolkitActivity;
use App\Services\DataToolkit\BuiltInDatasetService;
use App\Services\DataToolkit\DataToolkitAnalysisService;
use App\Services\DataToolkit\EdaWorkspaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StudentDataToolkitController extends Controller
{
    public function index(BuiltInDatasetService $datasets): View
    {
        return view('student.data-toolkit.index', [
            'datasets' => $datasets->all(),
            'maxUploadMegabytes' => 10,
            'maxRows' => EdaWorkspaceService::MAX_ROWS,
        ]);
    }

    public function upload(Request $request, EdaWorkspaceService $workspaces): RedirectResponse
    {
        $validated = $request->validate([
            'dataset_csv' => ['required', 'file', 'max:10240'],
        ], [
            'dataset_csv.required' => 'Choose a CSV file to begin.',
            'dataset_csv.file' => 'The selected upload is not a readable file.',
            'dataset_csv.max' => 'File is too large. The maximum allowed size is 10 MB.',
        ]);

        $dataset = $workspaces->storeUpload($validated['dataset_csv'], (int) $request->user()->id);

        return redirect()
            ->route('student.data-toolkit.show', ['dataset' => $dataset['key'], 'step' => 1])
            ->with('success', 'Dataset loaded. DataSensei automatically inspected its rows, columns, variable types, missing values, duplicates, and potential outliers.');
    }

    public function show(
        Request $request,
        string $dataset,
        BuiltInDatasetService $datasets,
        EdaWorkspaceService $workspaces,
        DataToolkitAnalysisService $analysis
    ): View {
        $datasetData = $this->resolveDataset($dataset, $datasets, $workspaces, (int) $request->user()->id);
        $overview = $analysis->overviewDataset($datasetData);
        $step = max(1, min(8, (int) $request->query('step', 1)));

        $this->recordActivity($dataset, 'view_dataset', ['roadmap_step' => $step], [
            'title' => $overview['dataset']['title'],
            'rows' => $overview['row_count'],
            'columns' => $overview['column_count'],
        ]);

        return view('student.data-toolkit.show', [
            'overview' => $overview,
            'step' => $step,
        ]);
    }

    public function rows(
        Request $request,
        string $dataset,
        BuiltInDatasetService $datasets,
        EdaWorkspaceService $workspaces
    ): JsonResponse {
        $datasetData = $this->resolveDataset($dataset, $datasets, $workspaces, (int) $request->user()->id);
        $columns = array_values(array_map('strval', (array) ($datasetData['columns'] ?? [])));
        $rows = array_values((array) ($datasetData['rows'] ?? []));
        $perPage = 250;
        $total = count($rows);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($lastPage, (int) $request->query('page', 1)));
        $offset = ($page - 1) * $perPage;

        return response()->json([
            'dataset' => (string) ($datasetData['title'] ?? 'Dataset'),
            'columns' => $columns,
            'rows' => array_slice($rows, $offset, $perPage),
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total_rows' => $total,
                'last_page' => $lastPage,
                'from' => $total === 0 ? 0 : $offset + 1,
                'to' => min($total, $offset + $perPage),
            ],
        ]);
    }

    public function objective(
        Request $request,
        string $dataset,
        BuiltInDatasetService $datasets,
        EdaWorkspaceService $workspaces
    ): RedirectResponse {
        $validated = $request->validate([
            'objective' => ['required', 'string', 'max:600'],
        ], [
            'objective.required' => 'Write one short objective before continuing.',
        ]);
        $datasetData = $this->resolveDataset($dataset, $datasets, $workspaces, (int) $request->user()->id);
        $saved = $workspaces->saveObjective($datasetData, $validated['objective'], (int) $request->user()->id);

        return redirect()->route('student.data-toolkit.show', ['dataset' => $saved['key'], 'step' => 2]);
    }

    public function clean(
        Request $request,
        string $dataset,
        BuiltInDatasetService $datasets,
        EdaWorkspaceService $workspaces,
        DataToolkitAnalysisService $analysis
    ): RedirectResponse {
        $datasetData = $this->resolveDataset($dataset, $datasets, $workspaces, (int) $request->user()->id);
        $overview = $analysis->overviewDataset($datasetData);
        $validated = $request->validate([
            'duplicates_action' => ['required', Rule::in(['remove', 'keep'])],
            'missing_action' => ['required', Rule::in(['fill', 'keep'])],
            'categories_action' => ['required', Rule::in(['standardize', 'keep'])],
            'invalid_action' => ['required', Rule::in(['replace', 'remove', 'keep'])],
        ]);
        $saved = $workspaces->cleanAndStore(
            $datasetData,
            $overview['numeric_columns'],
            $overview['categorical_columns'],
            [
                'duplicates' => $validated['duplicates_action'],
                'missing' => $validated['missing_action'],
                'categories' => $validated['categories_action'],
                'invalid' => $validated['invalid_action'],
            ],
            $overview,
            (int) $request->user()->id
        );

        $this->recordActivity((string) $saved['key'], 'data_quality_analysis', [], [
            'cleaning_summary' => $saved['cleaning_summary'] ?? [],
        ]);

        return redirect()
            ->route('student.data-toolkit.show', ['dataset' => $saved['key'], 'step' => 3])
            ->with('success', 'Your selected cleaning actions were applied. Review the before-and-after results, then continue.');
    }

    public function outliers(
        Request $request,
        string $dataset,
        BuiltInDatasetService $datasets,
        EdaWorkspaceService $workspaces,
        DataToolkitAnalysisService $analysis
    ): RedirectResponse {
        $datasetData = $this->resolveDataset($dataset, $datasets, $workspaces, (int) $request->user()->id);
        $overview = $analysis->overviewDataset($datasetData);
        $validated = $request->validate([
            'action' => ['required', Rule::in(['keep', 'remove'])],
            'columns' => ['nullable', 'array'],
            'columns.*' => ['string', Rule::in($overview['numeric_columns'])],
        ]);

        if ($validated['action'] === 'keep') {
            $this->recordActivity($dataset, 'outliers_analysis', [], ['decision' => 'kept']);

            return redirect()->route('student.data-toolkit.show', ['dataset' => $dataset, 'step' => 5]);
        }

        $selectedColumns = array_values((array) ($validated['columns'] ?? []));
        if ($selectedColumns === []) {
            throw ValidationException::withMessages([
                'columns' => 'Select at least one variable whose potential outliers should be removed.',
            ]);
        }

        $saved = $workspaces->removeOutliersAndStore(
            $datasetData,
            $selectedColumns,
            $overview['numeric_columns'],
            $overview,
            (int) $request->user()->id
        );
        $this->recordActivity((string) $saved['key'], 'outliers_analysis', ['columns' => $selectedColumns], [
            'removed_rows' => $saved['outlier_summary']['removed_rows'] ?? 0,
        ]);

        return redirect()
            ->route('student.data-toolkit.show', ['dataset' => $saved['key'], 'step' => 5])
            ->with('success', 'The selected potential outlier rows were removed from this working copy.');
    }

    public function feature(
        Request $request,
        string $dataset,
        BuiltInDatasetService $datasets,
        EdaWorkspaceService $workspaces,
        DataToolkitAnalysisService $analysis
    ): RedirectResponse {
        $datasetData = $this->resolveDataset($dataset, $datasets, $workspaces, (int) $request->user()->id);
        $overview = $analysis->overviewDataset($datasetData);
        $allowedKeys = array_values(array_filter(array_map(
            fn (array $suggestion): string => (string) ($suggestion['key'] ?? ''),
            $overview['feature_suggestions']
        )));
        $validated = $request->validate([
            'action' => ['required', Rule::in(['accept', 'skip'])],
            'suggestion_key' => ['nullable', 'string', Rule::in($allowedKeys)],
        ]);

        if ($validated['action'] === 'skip') {
            return redirect()->route('student.data-toolkit.show', ['dataset' => $dataset, 'step' => 8]);
        }

        $suggestion = collect($overview['feature_suggestions'])
            ->firstWhere('key', (string) ($validated['suggestion_key'] ?? ''));
        if (! is_array($suggestion)) {
            throw ValidationException::withMessages([
                'suggestion_key' => 'Choose one suggested feature to continue.',
            ]);
        }

        $saved = $workspaces->addFeatureAndStore($datasetData, $suggestion, $overview, (int) $request->user()->id);

        return redirect()
            ->route('student.data-toolkit.show', ['dataset' => $saved['key'], 'step' => 8])
            ->with('success', "The {$suggestion['name']} feature was created.");
    }

    public function analyze(
        Request $request,
        string $dataset,
        BuiltInDatasetService $datasets,
        EdaWorkspaceService $workspaces,
        DataToolkitAnalysisService $analysis
    ): JsonResponse {
        $datasetData = $this->resolveDataset($dataset, $datasets, $workspaces, (int) $request->user()->id);
        $overview = $analysis->overviewDataset($datasetData);
        $validated = $request->validate([
            'analysis_type' => ['required', 'string', Rule::in(['descriptive', 'categorical', 'correlation', 'regression', 'data_quality', 'outliers'])],
            'numeric_column' => ['nullable', 'string', Rule::in($overview['numeric_columns'])],
            'categorical_column' => ['nullable', 'string', Rule::in($overview['categorical_columns'])],
            'x_column' => ['nullable', 'required_if:analysis_type,regression', 'string', Rule::in($overview['numeric_columns'])],
            'y_column' => ['nullable', 'required_if:analysis_type,regression', 'string', Rule::in($overview['numeric_columns'])],
        ]);

        if (($validated['analysis_type'] ?? null) === 'regression' && ($validated['x_column'] ?? null) === ($validated['y_column'] ?? null)) {
            return response()->json([
                'message' => 'Choose two different numeric columns for regression.',
                'errors' => ['y_column' => ['The Y column must be different from the X column.']],
            ], 422);
        }

        $result = $analysis->analyzeDataset($datasetData, $validated);
        $selectedColumns = [
            'numeric_column' => $validated['numeric_column'] ?? null,
            'categorical_column' => $validated['categorical_column'] ?? null,
            'x_column' => $validated['x_column'] ?? null,
            'y_column' => $validated['y_column'] ?? null,
        ];
        $this->recordActivity($dataset, $validated['analysis_type'] . '_analysis', $selectedColumns, [
            'analysis_type' => $result['analysis_type'],
            'dataset_title' => $overview['dataset']['title'],
        ]);

        return response()->json(array_merge($result, [
            'selected_columns' => $selectedColumns,
            'dataset_title' => $overview['dataset']['title'],
            'generated_at' => now()->format('M d, Y h:i:s A'),
        ]));
    }

    public function report(
        Request $request,
        string $dataset,
        BuiltInDatasetService $datasets,
        EdaWorkspaceService $workspaces,
        DataToolkitAnalysisService $analysis
    ): View {
        $datasetData = $this->resolveDataset($dataset, $datasets, $workspaces, (int) $request->user()->id);
        $overview = $analysis->overviewDataset($datasetData);
        $validated = $request->validate([
            'x_column' => ['nullable', 'string', Rule::in($overview['numeric_columns'])],
            'y_column' => ['nullable', 'string', Rule::in($overview['numeric_columns'])],
        ]);

        if (($validated['x_column'] ?? null) && ($validated['x_column'] ?? null) === ($validated['y_column'] ?? null)) {
            $validated = [];
        }

        $report = $analysis->reportDataset($datasetData, $validated);
        $this->recordActivity($dataset, 'report_view', [], [
            'dataset_title' => $report['dataset']['title'],
            'generated_at' => (string) $report['generated_at'],
        ]);

        return view('student.data-toolkit.report', [
            'report' => $report,
            'student' => Auth::user(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveDataset(
        string $datasetKey,
        BuiltInDatasetService $datasets,
        EdaWorkspaceService $workspaces,
        int $userId
    ): array {
        $predefined = $datasets->find($datasetKey);
        if ($predefined) {
            $predefined['source'] = 'predefined';

            return $predefined;
        }

        $workspace = $workspaces->find($datasetKey, $userId);
        if ($workspace) {
            return $workspace;
        }

        abort(404, 'Dataset not found or no longer available.');
    }

    /**
     * @param array<string, mixed> $selectedColumns
     * @param array<string, mixed> $summary
     */
    private function recordActivity(string $datasetKey, string $activityType, array $selectedColumns = [], array $summary = []): void
    {
        if (! Auth::check() || ! Schema::hasTable('student_data_toolkit_activities')) {
            return;
        }

        StudentDataToolkitActivity::create([
            'user_id' => Auth::id(),
            'dataset_key' => $datasetKey,
            'activity_type' => $activityType,
            'selected_columns' => $selectedColumns,
            'result_summary' => $summary,
        ]);
    }
}
