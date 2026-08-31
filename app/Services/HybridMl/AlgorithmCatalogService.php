<?php

namespace App\Services\HybridMl;

use App\Models\AlgorithmConfig;
use Illuminate\Validation\ValidationException;

class AlgorithmCatalogService
{
    /** @return array<string,array<string,mixed>> */
    public function definitions(): array
    {
        return (array) config('hybrid_ml.algorithms', []);
    }

    public function active(?string $problemType = null): array
    {
        $definitions = $this->definitions();
        if ($problemType !== null) {
            $definitions = array_filter(
                $definitions,
                static fn (array $definition): bool => ($definition['problem_type'] ?? null) === $problemType
            );
        }
        return $definitions;
    }


    /** @param array<string,mixed> $schemaProfile @return array<string,array<string,mixed>> */
    public function learningGuides(string $problemType, array $schemaProfile): array
    {
        $guides = [];
        foreach ($this->active($problemType) as $key => $definition) {
            $guides[$key] = [
                'why_suitable' => $this->whySuitable($key, $problemType, $schemaProfile),
                'strengths' => array_values((array) ($definition['strengths'] ?? [])),
                'weaknesses' => array_values((array) ($definition['weaknesses'] ?? [])),
                'when_not_to_use' => array_values((array) ($definition['when_not_to_use'] ?? [])),
                'expected_training_time' => (string) ($definition['expected_training_time'] ?? 'Depends on dataset size and parameters.'),
                'expected_behavior' => $this->expectedBehavior($key),
            ];
        }

        return $guides;
    }

    /** @return array<string,mixed> */
    public function definition(string $algorithmKey): array
    {
        $definition = $this->definitions()[$algorithmKey] ?? null;
        if (! is_array($definition)) {
            throw ValidationException::withMessages(['algorithm_key' => 'Choose a supported machine-learning algorithm.']);
        }
        return $definition;
    }

    /**
     * @param array<string,mixed> $input
     * @param array<string,mixed> $schemaProfile
     * @return array<string,mixed>
     */
    public function normalizeTrainingConfiguration(array $input, array $schemaProfile): array
    {
        $problemType = (string) ($input['problem_type'] ?? $schemaProfile['detected_problem_type'] ?? 'classification');
        if (! in_array($problemType, ['classification', 'regression', 'clustering'], true)) {
            throw ValidationException::withMessages(['problem_type' => 'Choose classification, regression, or clustering.']);
        }

        $algorithmKey = (string) ($input['algorithm_key'] ?? '');
        $definition = $this->definition($algorithmKey);
        if (($definition['problem_type'] ?? null) !== $problemType) {
            throw ValidationException::withMessages(['algorithm_key' => 'The selected algorithm does not support the chosen problem type.']);
        }

        $headers = array_values(array_map('strval', (array) ($schemaProfile['headers'] ?? [])));
        $features = array_values(array_unique(array_filter(array_map('strval', (array) ($input['features'] ?? [])))));
        $target = $problemType === 'clustering' ? null : trim((string) ($input['target_column'] ?? ''));
        $errors = [];

        if ($features === []) {
            $errors['features'][] = 'Select at least one feature column.';
        }
        if (count($features) > 50) {
            $errors['features'][] = 'Select no more than 50 feature columns for one educational training run.';
        }
        foreach ($features as $feature) {
            if (! in_array($feature, $headers, true)) {
                $errors['features'][] = "The feature '{$feature}' does not exist in the selected dataset.";
            }
            if ($target !== null && $feature === $target) {
                $errors['features'][] = 'The target column cannot also be selected as a feature.';
            }
            $column = $schemaProfile['columns'][$feature] ?? [];
            if (($column['type'] ?? null) === 'empty' || ($column['is_identifier_like'] ?? false)) {
                $errors['features'][] = "The feature '{$feature}' is empty or identifier-like and should not be used for training.";
            }
        }

        if ($problemType !== 'clustering') {
            if ($target === '' || ! in_array($target, $headers, true)) {
                $errors['target_column'][] = 'Choose a valid target column.';
            }
            $targetProfile = $schemaProfile['columns'][$target] ?? [];
            if ($problemType === 'classification' && (int) ($targetProfile['unique_count'] ?? 0) < 2) {
                $errors['target_column'][] = 'Classification requires at least two target classes.';
            }
            if ($problemType === 'regression' && ! in_array($targetProfile['type'] ?? '', ['integer', 'decimal'], true)) {
                $errors['target_column'][] = 'Regression requires a numeric target column.';
            }
        } else {
            if (count($features) < 2) {
                $errors['features'][] = 'K-Means clustering requires at least two numeric feature columns.';
            }
            foreach ($features as $feature) {
                if (! in_array($schemaProfile['columns'][$feature]['type'] ?? '', ['integer', 'decimal'], true)) {
                    $errors['features'][] = 'K-Means accepts numeric features only.';
                    break;
                }
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        $testSize = $problemType === 'clustering' ? null : round((float) ($input['test_size'] ?? 0.20), 2);
        if ($testSize !== null && ! in_array($testSize, array_map('floatval', (array) config('hybrid_ml.allowed_test_sizes', [])), true)) {
            throw ValidationException::withMessages(['test_size' => 'Choose an approved training and testing split.']);
        }

        $randomState = (int) ($input['random_state'] ?? 42);
        if (! in_array($randomState, array_map('intval', (array) config('hybrid_ml.allowed_random_states', [])), true)) {
            throw ValidationException::withMessages(['random_state' => 'Choose an approved reproducible random state.']);
        }

        $crossValidation = (int) ($input['cross_validation'] ?? 5);
        if (! in_array($crossValidation, array_map('intval', (array) config('hybrid_ml.cross_validation_folds', [])), true)) {
            throw ValidationException::withMessages(['cross_validation' => 'Choose 0, 3, 5, or 10 cross-validation folds.']);
        }

        $parameterInput = (array) ($input['parameters'] ?? []);
        $parameters = $this->normalizeParameters($definition, $parameterInput);
        $scaleMode = (string) ($input['scale_mode'] ?? 'auto');
        if (! in_array($scaleMode, ['auto', 'standard', 'none'], true)) {
            $scaleMode = 'auto';
        }

        $scaleSensitive = in_array($algorithmKey, ['logistic_regression', 'knn', 'svm', 'kmeans'], true);
        $applyScaling = $scaleMode === 'standard' || ($scaleMode === 'auto' && $scaleSensitive);

        return [
            'problem_type' => $problemType,
            'algorithm_key' => $algorithmKey,
            'features' => $features,
            'target_column' => $target,
            'test_size' => $testSize,
            'random_state' => $randomState,
            'cross_validation' => $crossValidation,
            'parameters' => $parameters,
            'preprocessing' => [
                'remove_duplicates' => filter_var($input['remove_duplicates'] ?? true, FILTER_VALIDATE_BOOL),
                'numeric_imputation' => in_array(($input['numeric_imputation'] ?? 'median'), ['median', 'mean'], true)
                    ? (string) $input['numeric_imputation'] : 'median',
                'categorical_imputation' => 'most_frequent',
                'scale_mode' => $scaleMode,
                'apply_scaling' => $applyScaling,
                'one_hot_encode' => true,
            ],
            'learning_mode' => [
                'algorithm' => $definition,
                'why_suitable' => $this->whySuitable($algorithmKey, $problemType, $schemaProfile),
            ],
        ];
    }

    /** @param array<string,mixed> $definition @param array<string,mixed> $input */
    private function normalizeParameters(array $definition, array $input): array
    {
        $result = [];
        foreach ((array) ($definition['parameters'] ?? []) as $key => $schema) {
            $value = $input[$key] ?? $schema['default'] ?? null;
            $type = $schema['type'] ?? 'string';
            if ($type === 'integer') {
                $value = max((int) ($schema['min'] ?? PHP_INT_MIN), min((int) ($schema['max'] ?? PHP_INT_MAX), (int) $value));
            } elseif ($type === 'float') {
                $value = max((float) ($schema['min'] ?? -INF), min((float) ($schema['max'] ?? INF), (float) $value));
            } elseif ($type === 'select') {
                $options = array_map('strval', (array) ($schema['options'] ?? []));
                $value = in_array((string) $value, $options, true) ? (string) $value : (string) ($schema['default'] ?? ($options[0] ?? ''));
            }
            $result[$key] = $value;
        }
        return array_merge((array) ($definition['defaults'] ?? []), $result);
    }

    /** @param array<string,mixed> $schema */
    private function whySuitable(string $algorithm, string $problemType, array $schema): string
    {
        $rows = (int) ($schema['row_count'] ?? 0);
        $numeric = count((array) ($schema['numeric_columns'] ?? []));
        $categorical = count((array) ($schema['categorical_columns'] ?? []));

        return match ($algorithm) {
            'random_forest', 'random_forest_regressor' => "The dataset has {$rows} rows and a mix of {$numeric} numeric and {$categorical} categorical columns. Random Forest can capture nonlinear interactions after preprocessing and provides feature-importance estimates.",
            'gradient_boosting', 'gradient_boosting_regressor' => "The dataset has {$rows} rows. Gradient Boosting is suitable for structured tabular data where sequential trees can correct earlier errors, and the learning rate lets students observe the tradeoff between speed and overfitting.",
            'logistic_regression' => 'Logistic Regression provides an interpretable classification baseline and class probabilities. Scaling is applied automatically to numeric features.',
            'linear_regression' => 'Linear Regression is a transparent baseline for a numeric target and exposes coefficients that can be discussed in the learning report.',
            'svm' => 'SVM is suitable for a medium-sized classification dataset and can model nonlinear boundaries after standardization.',
            'knn' => 'KNN is useful for demonstrating similarity-based prediction. Feature scaling is enabled because distance is central to the algorithm.',
            'kmeans' => 'K-Means is appropriate for exploratory grouping because no target is required and the selected features are numeric.',
            default => "This {$problemType} algorithm is available as a controlled educational baseline for the detected dataset structure.",
        };
    }


    private function expectedBehavior(string $algorithm): string
    {
        return match ($algorithm) {
            'logistic_regression', 'linear_regression' => 'Expect a fast, transparent baseline. Performance may level off when relationships are strongly nonlinear.',
            'decision_tree', 'decision_tree_regressor' => 'Expect readable nonlinear splits. Deeper trees usually fit training data more closely but may generalize worse.',
            'random_forest', 'random_forest_regressor' => 'Expect stable nonlinear performance and feature importance, with more training time and a larger model artifact.',
            'gradient_boosting', 'gradient_boosting_regressor' => 'Expect sequential improvements as more trees are added. A smaller learning rate usually needs more estimators, while a large rate may overfit.',
            'knn' => 'Expect very little fitting time but slower prediction as the dataset grows. Results depend heavily on scaling and the neighbor count.',
            'naive_bayes' => 'Expect a very fast probabilistic baseline. It can work well even when its feature-independence assumption is only approximate.',
            'svm' => 'Expect a flexible decision boundary after scaling. Training may slow substantially as row count increases.',
            'kmeans' => 'Expect groups based on distance to learned centers. Cluster numbers are labels, not predefined real-world categories.',
            default => 'Expect a controlled baseline whose behavior depends on the selected features, preprocessing, and hyperparameters.',
        };
    }

    public function syncDatabaseCatalog(): void
    {
        foreach ($this->definitions() as $key => $definition) {
            AlgorithmConfig::updateOrCreate(
                ['algorithm_key' => $key],
                [
                    'problem_type' => $definition['problem_type'],
                    'label' => $definition['label'],
                    'description' => $definition['description'],
                    'strengths' => $definition['strengths'] ?? [],
                    'weaknesses' => $definition['weaknesses'] ?? [],
                    'when_not_to_use' => $definition['when_not_to_use'] ?? [],
                    'expected_training_time' => $definition['expected_training_time'] ?? null,
                    'default_parameters' => $definition['defaults'] ?? [],
                    'parameter_schema' => $definition['parameters'] ?? [],
                    'supports_probability' => (bool) ($definition['supports_probability'] ?? false),
                    'supports_feature_importance' => (bool) ($definition['supports_feature_importance'] ?? false),
                    'is_optional' => false,
                    'is_active' => true,
                ]
            );
        }
    }
}
