<?php

namespace App\Http\Controllers;

use App\Models\StudentDataToolkitActivity;
use App\Services\DataToolkit\BuiltInDatasetService;
use App\Services\DataToolkit\DataToolkitAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentDataToolkitController extends Controller
{
    public function index(BuiltInDatasetService $datasets): View
    {
        return view('student.data-toolkit.index', [
            'datasets' => $datasets->all(),
        ]);
    }

    public function show(string $dataset, DataToolkitAnalysisService $analysis): View
    {
        $overview = $analysis->overview($dataset);

        $this->recordActivity($dataset, 'view_dataset', [], [
            'title' => $overview['dataset']['title'],
            'rows' => $overview['row_count'],
            'columns' => $overview['column_count'],
        ]);

        return view('student.data-toolkit.show', [
            'overview' => $overview,
        ]);
    }

    public function analyze(Request $request, string $dataset, DataToolkitAnalysisService $analysis): JsonResponse
    {
        $overview = $analysis->overview($dataset);
        $validated = $request->validate([
            'analysis_type' => ['required', 'string', Rule::in(['descriptive', 'categorical', 'correlation', 'regression'])],
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

        $result = $analysis->analyze($dataset, $validated);
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

    public function report(Request $request, string $dataset, DataToolkitAnalysisService $analysis): View
    {
        $overview = $analysis->overview($dataset);
        $validated = $request->validate([
            'x_column' => ['nullable', 'string', Rule::in($overview['numeric_columns'])],
            'y_column' => ['nullable', 'string', Rule::in($overview['numeric_columns'])],
        ]);

        if (($validated['x_column'] ?? null) && ($validated['x_column'] ?? null) === ($validated['y_column'] ?? null)) {
            $validated = [];
        }

        $report = $analysis->report($dataset, $validated);

        $this->recordActivity($dataset, 'report_view', [
            'x_column' => $validated['x_column'] ?? null,
            'y_column' => $validated['y_column'] ?? null,
        ], [
            'dataset_title' => $report['dataset']['title'],
            'generated_at' => (string) $report['generated_at'],
        ]);

        return view('student.data-toolkit.report', [
            'report' => $report,
            'student' => Auth::user(),
        ]);
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
