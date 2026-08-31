<?php

namespace App\Services\HybridMl;

use App\Models\BenchmarkModel;
use App\Models\ModelVersion;

class BenchmarkComparisonService
{
    /** @return array<string,mixed>|null */
    public function compare(ModelVersion $userVersion): ?array
    {
        $userVersion->loadMissing('model.dataset');
        $datasetId = $userVersion->model?->dataset_id;
        if (! $datasetId || $userVersion->model?->pipeline_type !== 'user') {
            return null;
        }

        // Prefer a like-for-like algorithm benchmark. Fall back to the
        // dataset's primary benchmark when that algorithm was not pretrained.
        $benchmark = BenchmarkModel::query()
            ->where('dataset_id', $datasetId)
            ->where('algorithm_key', $userVersion->model->algorithm_key)
            ->with(['model', 'version'])
            ->first();

        if (! $benchmark?->version) {
            $benchmark = BenchmarkModel::query()
                ->where('dataset_id', $datasetId)
                ->where('is_primary', true)
                ->with(['model', 'version'])
                ->first();
        }
        if (! $benchmark?->version) {
            return null;
        }

        $problemType = (string) $userVersion->model->problem_type;
        $metricKeys = $problemType === 'classification'
            ? ['accuracy', 'precision', 'recall', 'f1', 'roc_auc', 'training_time_ms']
            : ($problemType === 'regression'
                ? ['rmse', 'mae', 'r2', 'training_time_ms']
                : ['silhouette', 'inertia', 'training_time_ms']);

        $userMetrics = array_merge((array) $userVersion->metrics, ['training_time_ms' => $userVersion->training_time_ms]);
        $systemMetrics = array_merge((array) $benchmark->version->metrics, ['training_time_ms' => $benchmark->version->training_time_ms]);
        $rows = [];
        foreach ($metricKeys as $key) {
            if (! array_key_exists($key, $userMetrics) || ! array_key_exists($key, $systemMetrics)) {
                continue;
            }
            $userValue = $userMetrics[$key];
            $systemValue = $systemMetrics[$key];
            if ($userValue === null || $systemValue === null) {
                continue;
            }
            $higherIsBetter = ! in_array($key, ['rmse', 'mae', 'mse', 'training_time_ms', 'inertia'], true);
            $difference = (float) $userValue - (float) $systemValue;
            $tolerance = max(0.0001, abs((float) $systemValue) * 0.005);
            $status = abs($difference) <= $tolerance
                ? 'equal'
                : (($higherIsBetter ? $difference > 0 : $difference < 0) ? 'better' : 'worse');

            $rows[] = [
                'metric' => $key,
                'user' => $userValue,
                'system' => $systemValue,
                'difference' => $difference,
                'status' => $status,
                'higher_is_better' => $higherIsBetter,
            ];
        }

        return [
            'benchmark_model' => $benchmark->model,
            'benchmark_version' => $benchmark->version,
            'rows' => $rows,
            'explanation' => $this->explanation($rows, $userVersion, $benchmark->version),
        ];
    }

    /** @param array<int,array<string,mixed>> $rows @return array<int,string> */
    private function explanation(array $rows, ModelVersion $user, ModelVersion $system): array
    {
        $items = [];
        $better = count(array_filter($rows, static fn (array $row): bool => $row['status'] === 'better'));
        $worse = count(array_filter($rows, static fn (array $row): bool => $row['status'] === 'worse'));
        if ($better > $worse) {
            $items[] = 'The user-trained version outperformed the system benchmark on more displayed measures. This can result from a better algorithm fit, preprocessing, or hyperparameters. Treat the difference as approximate when the train/test split or random state differs from the benchmark configuration.';
        } elseif ($worse > $better) {
            $items[] = 'The system benchmark performed better on more displayed measures. Review preprocessing, feature selection, model complexity, and cross-validation before concluding that the algorithm is weaker.';
        } else {
            $items[] = 'The user and system models are close across the displayed measures. Small differences may come from hyperparameters, preprocessing, and random sampling rather than a meaningful performance gap.';
        }
        if ((int) $user->training_time_ms < (int) $system->training_time_ms) {
            $items[] = 'The user model trained faster, which may be useful when performance is similar and repeated experimentation matters.';
        }
        return $items;
    }
}
