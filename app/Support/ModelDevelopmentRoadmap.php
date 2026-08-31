<?php

namespace App\Support;

final class ModelDevelopmentRoadmap
{
    /** @return array<int, array{key:string,label:string,description:string}> */
    public static function steps(): array
    {
        return [
            1 => [
                'key' => 'dataset',
                'label' => 'Dataset',
                'description' => 'Choose the data you want to use for your model.',
            ],
            2 => [
                'key' => 'target',
                'label' => 'Target',
                'description' => 'Choose what you want the model to predict.',
            ],
            3 => [
                'key' => 'features',
                'label' => 'Features',
                'description' => 'Choose the information the model will use to make predictions.',
            ],
            4 => [
                'key' => 'problem_type',
                'label' => 'Problem Type',
                'description' => 'Choose whether the result is a category, number, or discovered group.',
            ],
            5 => [
                'key' => 'split',
                'label' => 'Train/Test Split',
                'description' => 'Reserve unseen data so model performance can be checked fairly.',
            ],
            6 => [
                'key' => 'algorithm',
                'label' => 'Algorithm',
                'description' => 'Choose the machine-learning method you want to use.',
            ],
            7 => [
                'key' => 'train',
                'label' => 'Train Model',
                'description' => 'Allow the model to learn patterns from the training data.',
            ],
            8 => [
                'key' => 'evaluate',
                'label' => 'Evaluate',
                'description' => 'Check how well the model performs on unseen data.',
            ],
            9 => [
                'key' => 'predict',
                'label' => 'Predict',
                'description' => 'Use the trained model to make a new prediction.',
            ],
            10 => [
                'key' => 'save',
                'label' => 'Save Model',
                'description' => 'Keep the trained version in Model History for later use.',
            ],
        ];
    }

    public static function totalSteps(): int
    {
        return count(self::steps());
    }

    /** @return array{completed:int,total:int,percent:int} */
    public static function progress(int $completed): array
    {
        $total = self::totalSteps();
        $completed = max(0, min($total, $completed));

        return [
            'completed' => $completed,
            'total' => $total,
            'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ];
    }

    public static function normalizeAuthoringStep(mixed $step, int $fallback = 2): int
    {
        $step = is_numeric($step) ? (int) $step : $fallback;

        return max(2, min(7, $step));
    }

    /** @param array<int, string> $fields */
    public static function stepForValidationFields(array $fields): ?int
    {
        $fieldSteps = [
            'target_column' => 2,
            'features' => 3,
            'problem_type' => 4,
            'test_size' => 5,
            'random_state' => 5,
            'cross_validation' => 5,
            'scale_mode' => 5,
            'numeric_imputation' => 5,
            'remove_duplicates' => 5,
            'algorithm_key' => 6,
            'parameters' => 6,
            'model_name' => 7,
            'class_id' => 7,
        ];

        $steps = [];
        foreach ($fields as $field) {
            $root = explode('.', (string) $field)[0];
            if (isset($fieldSteps[$root])) {
                $steps[] = $fieldSteps[$root];
            }
        }

        return $steps === [] ? null : min($steps);
    }

    public static function resultStep(?string $requested, bool $hasPrediction): int
    {
        $step = match ($requested) {
            'predict' => 9,
            'save' => 10,
            default => 8,
        };

        return $step === 10 && ! $hasPrediction ? 9 : $step;
    }
}
