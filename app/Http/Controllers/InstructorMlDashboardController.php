<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\MlModel;
use App\Models\TrainingJob;
use App\Models\UserDataset;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorMlDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $instructorId = (int) $request->user()->id;
        $classes = ClassRoom::query()->where('instructor_id', $instructorId)->active()->orderBy('name')->get();
        $allowedIds = $classes->pluck('id');
        $classId = $request->integer('class_id') ?: null;
        if ($classId !== null && ! $allowedIds->contains($classId)) {
            abort(403);
        }

        $datasets = UserDataset::query()
            ->whereIn('class_id', $allowedIds)
            ->when($classId, fn ($query) => $query->where('class_id', $classId))
            ->with(['user:id,name,email', 'classRoom:id,name,section'])
            ->latest()->limit(50)->get();

        $jobs = TrainingJob::query()
            ->whereIn('class_id', $allowedIds)
            ->when($classId, fn ($query) => $query->where('class_id', $classId))
            ->with(['user:id,name,email', 'classRoom:id,name,section', 'dataset:id,name', 'userDataset:id,name', 'model.currentVersion'])
            ->latest()->limit(75)->get();

        $models = MlModel::query()
            ->where('pipeline_type', 'user')
            ->whereIn('class_id', $allowedIds)
            ->when($classId, fn ($query) => $query->where('class_id', $classId))
            ->with(['user:id,name,email', 'classRoom:id,name,section', 'dataset:id,name', 'userDataset:id,name', 'currentVersion'])
            ->latest()->limit(50)->get();

        $completed = $jobs->where('status', 'completed');
        $failed = $jobs->where('status', 'failed');
        $commonMistakes = $failed->groupBy(fn ($job) => str($job->error_message ?: 'Unknown training error')->before('.')->limit(100)->toString())
            ->map->count()->sortDesc()->take(8);

        $bestModels = $models->filter(fn ($model) => $model->currentVersion)
            ->sortByDesc(function ($model) {
                $metrics = (array) $model->currentVersion->metrics;
                return $model->problem_type === 'regression'
                    ? -1 * (float) ($metrics['rmse'] ?? PHP_FLOAT_MAX)
                    : (float) ($metrics['f1'] ?? $metrics['accuracy'] ?? $metrics['silhouette'] ?? -1);
            })->take(10);

        return view('instructor.model-development.index', [
            'classes' => $classes,
            'selectedClassId' => $classId,
            'datasets' => $datasets,
            'jobs' => $jobs,
            'models' => $models,
            'bestModels' => $bestModels,
            'commonMistakes' => $commonMistakes,
            'summary' => [
                'students' => $jobs->pluck('user_id')->unique()->count(),
                'datasets' => $datasets->count(),
                'models' => $models->count(),
                'completed' => $completed->count(),
                'failed' => $failed->count(),
            ],
        ]);
    }
}
