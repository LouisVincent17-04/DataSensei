<?php

namespace App\Services\HybridMl;

use App\Models\ModelVersion;
use App\Models\PredictionLog;
use Illuminate\Validation\ValidationException;

class PredictionService
{
    public function __construct(
        private readonly HybridMlRunnerService $runner,
        private readonly EducationalExplanationService $explanations,
    ) {}

    /** @param array<string,mixed> $input @return array<string,mixed> */
    public function predict(ModelVersion $version, int $userId, array $input): array
    {
        $schema = (array) data_get($version->explanations, 'prediction_schema', []);
        $normalized = [];
        $errors = [];
        foreach ($schema as $feature => $definition) {
            $value = $input[$feature] ?? null;
            if ($value === '') {
                $value = null;
            }
            if (($definition['type'] ?? null) === 'number') {
                if ($value !== null && ! is_numeric($value)) {
                    $errors["input_values.{$feature}"][] = "{$feature} must be numeric.";
                    continue;
                }
                $normalized[$feature] = $value === null ? null : (float) $value;
            } else {
                if ($value !== null && strlen((string) $value) > 500) {
                    $errors["input_values.{$feature}"][] = "{$feature} is too long.";
                    continue;
                }
                $normalized[$feature] = $value === null ? null : trim((string) $value);
            }
        }
        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        $started = microtime(true);
        $result = $this->runner->predict($version, $normalized);
        $latency = (int) round((microtime(true) - $started) * 1000);
        $model = $version->model()->firstOrFail();
        $explanation = $this->explanations->predictionExplanation(
            (string) $model->problem_type,
            $result,
            (array) ($result['top_features'] ?? [])
        );

        PredictionLog::create([
            'model_version_id' => $version->id,
            'user_id' => $userId,
            'input_values' => $normalized,
            'predicted_value' => is_scalar($result['predicted_value'] ?? null)
                ? (string) $result['predicted_value'] : json_encode($result['predicted_value']),
            'probabilities' => $result['probabilities'] ?? null,
            'explanation' => implode(' ', $explanation),
            'latency_ms' => $latency,
        ]);

        $result['explanation'] = $explanation;
        $result['latency_ms'] = $latency;
        return $result;
    }
}
