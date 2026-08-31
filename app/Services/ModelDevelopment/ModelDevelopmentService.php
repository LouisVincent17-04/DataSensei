<?php

namespace App\Services\ModelDevelopment;

use App\Models\ModelDevelopmentRun;
use App\Services\DataToolkit\BuiltInDatasetService;
use App\Services\DataToolkit\DataToolkitAnalysisService;
use App\Services\PythonSandboxService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ModelDevelopmentService
{
    private const TASKS = [
        'classification' => [
            'label' => 'Classification',
            'description' => 'Predict a category or class, such as Yes/No, condition, segment, or program.',
            'metrics' => ['Accuracy', 'Precision', 'Recall', 'F1 Score'],
        ],
        'regression' => [
            'label' => 'Regression',
            'description' => 'Predict a continuous numeric value, such as grade, revenue, rainfall, or wellness score.',
            'metrics' => ['MAE', 'MSE', 'RMSE', 'R²'],
        ],
        'clustering' => [
            'label' => 'Clustering',
            'description' => 'Group similar records without selecting a target variable.',
            'metrics' => ['Inertia', 'Silhouette Score', 'Cluster Sizes'],
        ],
    ];

    private const ALGORITHMS = [
        'classification' => [
            'logistic_regression' => [
                'label' => 'Logistic Regression',
                'description' => 'A strong, interpretable baseline for classification.',
                'scale_sensitive' => true,
            ],
            'decision_tree' => [
                'label' => 'Decision Tree',
                'description' => 'Learns easy-to-follow decision rules from the selected features.',
                'scale_sensitive' => false,
            ],
            'random_forest' => [
                'label' => 'Random Forest',
                'description' => 'Combines multiple decision trees for more stable predictions.',
                'scale_sensitive' => false,
            ],
            'knn' => [
                'label' => 'K-Nearest Neighbors',
                'description' => 'Predicts using the most similar training records.',
                'scale_sensitive' => true,
            ],
            'naive_bayes' => [
                'label' => 'Naïve Bayes',
                'description' => 'A fast probabilistic classifier that works well as a learning baseline.',
                'scale_sensitive' => false,
            ],
        ],
        'regression' => [
            'linear_regression' => [
                'label' => 'Linear Regression',
                'description' => 'Models a linear relationship between features and a numeric target.',
                'scale_sensitive' => false,
            ],
            'ridge_regression' => [
                'label' => 'Ridge Regression',
                'description' => 'Linear regression with L2 regularization to reduce unstable coefficients.',
                'scale_sensitive' => true,
            ],
            'lasso_regression' => [
                'label' => 'Lasso Regression',
                'description' => 'Linear regression with L1 regularization that can shrink weak coefficients to zero.',
                'scale_sensitive' => true,
            ],
        ],
        'clustering' => [
            'kmeans' => [
                'label' => 'K-Means',
                'description' => 'Separates records into a selected number of similarity-based clusters.',
                'scale_sensitive' => true,
            ],
        ],
    ];

    public function __construct(
        private readonly BuiltInDatasetService $datasets,
        private readonly UploadedCsvDatasetService $uploadedDatasets,
        private readonly DataToolkitAnalysisService $analysis,
        private readonly PythonSandboxService $sandbox,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function catalog(): array
    {
        return collect($this->datasets->all())
            ->map(fn (array $dataset): array => $this->datasetProfile((string) $dataset['key']))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function datasetProfile(string $datasetKey, ?int $userId = null): array
    {
        $dataset = $this->resolveDataset($datasetKey, $userId);
        if (! $dataset) {
            abort(404, 'Dataset not found. Upload the CSV again if it has been removed.');
        }

        return $this->profileDataset($dataset);
    }

    /**
     * @param array<string, mixed> $dataset
     * @return array<string, mixed>
     */
    public function profileDataset(array $dataset): array
    {
        $overview = $this->analysis->overviewDataset($dataset);
        $columnProfiles = $overview['eda']['data_types'];
        $maxCategories = max(2, (int) config('model_development.max_categories_per_feature', 50));
        $blocked = [];

        foreach ($columnProfiles as $column => $profile) {
            $nameLooksLikeId = preg_match('/(^id$|_id$|^id_|identifier)/i', (string) $column) === 1;
            $uniqueCount = (int) ($profile['unique_count'] ?? 0);
            $constantOrEmpty = $uniqueCount <= 1;
            $highCardinalityCategory = ! in_array($column, $overview['numeric_columns'], true)
                && $uniqueCount > $maxCategories;
            $unsupportedType = in_array((string) ($profile['type'] ?? ''), ['Identifier', 'Text', 'Date'], true)
                && (float) ($profile['unique_percent'] ?? 0) >= 80;

            if ($nameLooksLikeId || $constantOrEmpty || $highCardinalityCategory || $unsupportedType) {
                $blocked[$column] = $nameLooksLikeId
                    ? 'Identifier-like columns are excluded because they identify records rather than describe patterns.'
                    : ($constantOrEmpty
                        ? 'Empty or constant columns are excluded because they do not provide a learnable pattern.'
                        : ($highCardinalityCategory
                            ? "This categorical column has more than {$maxCategories} unique values."
                            : 'This high-cardinality text or date column is not enabled for the controlled learning workflow.'));
            }
        }

        $numericTargets = array_values(array_filter(
            $overview['numeric_columns'],
            fn (string $column): bool => ! isset($blocked[$column])
        ));
        $classificationTargets = [];

        foreach ((array) ($dataset['columns'] ?? []) as $column) {
            if (isset($blocked[$column])) {
                continue;
            }

            $uniqueCount = (int) ($columnProfiles[$column]['unique_count'] ?? 0);
            if ($uniqueCount >= 2 && $uniqueCount <= 20) {
                $classificationTargets[] = $column;
            }
        }

        $availableFeatures = array_values(array_filter(
            (array) ($dataset['columns'] ?? []),
            fn (string $column): bool => ! isset($blocked[$column])
        ));
        $numericFeatures = array_values(array_intersect($availableFeatures, $overview['numeric_columns']));
        $recommendation = $this->recommendationFor(
            (string) ($dataset['key'] ?? 'uploaded-csv'),
            $availableFeatures,
            $numericTargets,
            $classificationTargets
        );

        return [
            'dataset' => $dataset,
            'row_count' => $overview['row_count'],
            'column_count' => $overview['column_count'],
            'preview' => array_slice((array) ($dataset['rows'] ?? []), 0, 8),
            'column_profiles' => $columnProfiles,
            'numeric_columns' => $overview['numeric_columns'],
            'categorical_columns' => $overview['categorical_columns'],
            'available_features' => $availableFeatures,
            'numeric_features' => $numericFeatures,
            'blocked_features' => $blocked,
            'classification_targets' => $classificationTargets,
            'regression_targets' => $numericTargets,
            'supported_tasks' => [
                'classification' => count($classificationTargets) > 0 && count($availableFeatures) >= 2,
                'regression' => count($numericTargets) > 0 && count($availableFeatures) >= 2,
                'clustering' => count($numericFeatures) >= 2,
            ],
            'recommendation' => $recommendation,
            'eda_summary' => [
                'quality_score' => $overview['eda']['quality_score'],
                'missing' => $overview['eda']['missing']['total_missing'],
                'duplicates' => $overview['eda']['duplicates']['duplicate_records'],
                'outliers' => $overview['eda']['outliers']['total_outliers'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function resolveDataset(string $datasetKey, ?int $userId = null): ?array
    {
        $dataset = $this->datasets->find($datasetKey);
        if ($dataset) {
            return $dataset;
        }

        return $userId === null ? null : $this->uploadedDatasets->find($datasetKey, $userId);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function taskDefinitions(): array
    {
        return self::TASKS;
    }

    /**
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function algorithmDefinitions(): array
    {
        return self::ALGORITHMS;
    }

    /**
     * @return array<int, string>
     */
    public function limitations(): array
    {
        return [
            'Training requires a CSV dataset with at least 20 rows and no more than the configured row and column limits.',
            'Only the published classification, regression, and K-Means algorithms are allowed.',
            'Deep learning, neural networks, image models, large language models, arbitrary scripts, and package installation are outside this module.',
            'Training is CPU-only, network-disabled, time-limited, memory-limited, and limited to a small feature set.',
            'Hyperparameters are restricted to safe educational ranges; large grid searches and automated mass tuning are disabled.',
            'Serialized model files are not stored. Predictions retrain the saved configuration on the same uploaded CSV for reproducibility.',
        ];
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed> $profile
     * @return array<string, mixed>
     */
    public function normalizeConfiguration(array $input, array $profile): array
    {
        $task = (string) ($input['task_type'] ?? '');
        $algorithm = (string) ($input['algorithm'] ?? '');
        $features = array_values(array_unique(array_map('strval', (array) ($input['features'] ?? []))));
        $target = $task === 'clustering' ? null : trim((string) ($input['target_column'] ?? ''));
        $errors = [];
        $maxFeatures = max(2, (int) config('model_development.max_features', 12));

        if (! isset(self::TASKS[$task]) || ! ($profile['supported_tasks'][$task] ?? false)) {
            $errors['task_type'][] = 'The selected task is not supported by this dataset.';
        }
        if (! isset(self::ALGORITHMS[$task][$algorithm])) {
            $errors['algorithm'][] = 'Choose an algorithm that belongs to the selected task.';
        }

        $minimumFeatures = $task === 'clustering' ? 2 : 1;
        if (count($features) < $minimumFeatures || count($features) > $maxFeatures) {
            $errors['features'][] = "Choose between {$minimumFeatures} and {$maxFeatures} valid feature columns.";
        }

        foreach ($features as $feature) {
            if (! in_array($feature, $profile['available_features'], true)) {
                $errors['features'][] = "The feature '{$feature}' is not available for controlled model training.";
            }
            if ($target !== null && $feature === $target) {
                $errors['features'][] = 'The target column cannot also be selected as a feature.';
            }
        }

        if ($task === 'classification' && ! in_array($target, $profile['classification_targets'], true)) {
            $errors['target_column'][] = 'Choose a target with between 2 and 20 distinct classes.';
        }
        if ($task === 'regression' && ! in_array($target, $profile['regression_targets'], true)) {
            $errors['target_column'][] = 'Regression requires a valid numeric target column.';
        }
        if ($task === 'clustering') {
            foreach ($features as $feature) {
                if (! in_array($feature, $profile['numeric_features'], true)) {
                    $errors['features'][] = 'K-Means accepts numeric feature columns only.';
                }
            }
        }

        $testSize = $task === 'clustering' ? null : round((float) ($input['test_size'] ?? 0.20), 2);
        $allowedTestSizes = array_map('floatval', (array) config('model_development.allowed_test_sizes', [0.10, 0.20, 0.25, 0.30]));
        if ($testSize !== null && ! in_array($testSize, $allowedTestSizes, true)) {
            $errors['test_size'][] = 'Choose one of the approved training and testing split ratios.';
        }

        $randomSeed = (int) ($input['random_seed'] ?? config('model_development.default_random_seed', 42));
        $allowedSeeds = array_map('intval', (array) config('model_development.allowed_random_seeds', [7, 21, 42, 100]));
        if (! in_array($randomSeed, $allowedSeeds, true)) {
            $errors['random_seed'][] = 'Choose one of the approved reproducible random seeds.';
        }

        $numericImputation = (string) ($input['numeric_imputation'] ?? 'median');
        if (! in_array($numericImputation, ['median', 'mean'], true)) {
            $errors['numeric_imputation'][] = 'Choose median or mean numeric imputation.';
        }

        $scaleMode = (string) ($input['scale_mode'] ?? 'auto');
        if (! in_array($scaleMode, ['auto', 'standard', 'none'], true)) {
            $errors['scale_mode'][] = 'Choose automatic, standard, or no feature scaling.';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        $parameters = $this->normalizeParameters($algorithm, $input);
        $algorithmDefinition = self::ALGORITHMS[$task][$algorithm];
        $applyScaling = $scaleMode === 'standard'
            || ($scaleMode === 'auto' && (bool) ($algorithmDefinition['scale_sensitive'] ?? false));

        return [
            'operation' => 'train',
            'task_type' => $task,
            'algorithm' => $algorithm,
            'features' => $features,
            'target_column' => $target,
            'test_size' => $testSize,
            'random_seed' => $randomSeed,
            'preprocessing' => [
                'numeric_imputation' => $numericImputation,
                'categorical_imputation' => 'most_frequent',
                'scale_mode' => $scaleMode,
                'apply_scaling' => $applyScaling,
                'remove_duplicates' => filter_var($input['remove_duplicates'] ?? true, FILTER_VALIDATE_BOOL),
                'one_hot_encode_categories' => true,
            ],
            'parameters' => $parameters,
        ];
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<string, mixed> $configuration
     * @return array<string, mixed>
     */
    public function train(array $dataset, array $configuration): array
    {
        return $this->execute($dataset, $configuration);
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<string, mixed> $inputValues
     * @return array<string, mixed>
     */
    public function predict(ModelDevelopmentRun $run, array $dataset, array $inputValues): array
    {
        $features = (array) $run->feature_columns;
        $columnProfiles = $this->profileDataset($dataset)['column_profiles'];
        $normalizedInput = [];
        $errors = [];

        foreach ($features as $feature) {
            $value = $inputValues[$feature] ?? null;
            if ($value === '') {
                $value = null;
            }

            $isNumeric = in_array((string) ($columnProfiles[$feature]['type'] ?? ''), ['Integer', 'Decimal'], true);
            if ($value !== null && $isNumeric && ! is_numeric($value)) {
                $errors["prediction_input.{$feature}"][] = "{$feature} must be numeric.";
                continue;
            }

            if (is_string($value) && strlen($value) > 200) {
                $errors["prediction_input.{$feature}"][] = "{$feature} is too long.";
                continue;
            }

            $normalizedInput[$feature] = $value === null ? null : ($isNumeric ? (float) $value : trim((string) $value));
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        $configuration = [
            'operation' => 'predict',
            'task_type' => $run->task_type,
            'algorithm' => $run->algorithm,
            'features' => $features,
            'target_column' => $run->target_column,
            'test_size' => $run->test_size,
            'random_seed' => $run->random_seed,
            'preprocessing' => $run->preprocessing ?: [],
            'parameters' => $run->parameters ?: [],
            'prediction_input' => $normalizedInput,
        ];

        return $this->execute($dataset, $configuration);
    }

    public function taskLabel(string $task): string
    {
        return (string) (self::TASKS[$task]['label'] ?? Str::headline($task));
    }

    public function algorithmLabel(string $task, string $algorithm): string
    {
        return (string) (self::ALGORITHMS[$task][$algorithm]['label'] ?? Str::headline($algorithm));
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, int|float|null>
     */
    private function normalizeParameters(string $algorithm, array $input): array
    {
        return match ($algorithm) {
            'decision_tree' => [
                'max_depth' => $this->boundedInt($input['max_depth'] ?? 5, 2, 10),
            ],
            'random_forest' => [
                'n_estimators' => $this->boundedInt($input['n_estimators'] ?? 60, 10, 100),
                'max_depth' => $this->boundedInt($input['max_depth'] ?? 6, 2, 10),
            ],
            'knn' => [
                'n_neighbors' => $this->boundedInt($input['n_neighbors'] ?? 5, 1, 15),
            ],
            'ridge_regression', 'lasso_regression' => [
                'alpha' => $this->boundedFloat($input['alpha'] ?? 1.0, 0.01, 10.0),
            ],
            'kmeans' => [
                'n_clusters' => $this->boundedInt($input['n_clusters'] ?? 3, 2, 8),
            ],
            default => [],
        };
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<string, mixed> $configuration
     * @return array<string, mixed>
     */
    private function execute(array $dataset, array $configuration): array
    {
        $rows = (array) ($dataset['rows'] ?? []);
        $maxRows = max(20, (int) config('model_development.max_dataset_rows', 1000));
        if (count($rows) < 20) {
            throw new RuntimeException('The selected dataset does not have enough records for this learning workflow.');
        }
        if (count($rows) > $maxRows) {
            throw new RuntimeException("This module accepts at most {$maxRows} records per CSV dataset.");
        }

        $workspace = storage_path('app/model_development/tmp_' . Str::uuid());
        File::ensureDirectoryExists($workspace, 0755, true);

        try {
            File::put($workspace . '/dataset.json', json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR));
            File::put($workspace . '/config.json', json_encode($configuration, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR));
            $runner = resource_path('model-development/model_runner.py');
            if (! is_file($runner)) {
                throw new RuntimeException('The trusted Model Development runner is missing.');
            }

            $code = File::get($runner);
            $result = $this->sandbox->runWorkspace(
                $workspace,
                'main.py',
                $code,
                '',
                ['timeout' => max(5, min(45, (int) config('model_development.timeout_seconds', 25)))]
            );

            if (($result['failed'] ?? true) || (int) ($result['exit_code'] ?? 1) !== 0) {
                $message = trim((string) ($result['stderr'] ?? ''));
                throw new RuntimeException($message !== ''
                    ? substr($message, 0, 700)
                    : 'The isolated model-training process failed.');
            }

            $stdout = (string) ($result['stdout'] ?? '');
            if (preg_match('/__MODEL_RESULT__:(\{.*\})/s', $stdout, $match) !== 1) {
                throw new RuntimeException('The model runner returned an invalid result. Rebuild the Python sandbox image and try again.');
            }

            $decoded = json_decode($match[1], true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($decoded) || ($decoded['ok'] ?? false) !== true) {
                throw new RuntimeException((string) ($decoded['message'] ?? 'The model runner could not complete the requested operation.'));
            }

            $decoded['execution_time_ms'] = (int) ($result['execution_time_ms'] ?? 0);
            return $decoded;
        } catch (\JsonException $exception) {
            throw new RuntimeException('The model runner returned malformed data.', 0, $exception);
        } finally {
            File::deleteDirectory($workspace);
        }
    }

    /**
     * @param array<int, string> $availableFeatures
     * @param array<int, string> $numericTargets
     * @param array<int, string> $classificationTargets
     * @return array<string, mixed>
     */
    private function recommendationFor(
        string $datasetKey,
        array $availableFeatures,
        array $numericTargets,
        array $classificationTargets,
    ): array {
        $map = [
            'student-performance' => ['regression', 'final_grade', ['study_hours', 'attendance_rate', 'quiz_score', 'project_score'], 'linear_regression'],
            'monthly-sales' => ['regression', 'revenue', ['product_category', 'units_sold', 'unit_price', 'customer_rating'], 'ridge_regression'],
            'weather-observations' => ['classification', 'condition', ['temperature', 'humidity', 'rainfall', 'wind_speed'], 'random_forest'],
            'learning-survey' => ['regression', 'satisfaction_score', ['program', 'year_level', 'learning_platform_used', 'difficulty_rating', 'study_time_minutes'], 'ridge_regression'],
            'fitness-wellness' => ['regression', 'wellness_score', ['age', 'activity_level', 'steps', 'calories_burned', 'sleep_hours', 'water_intake_liters'], 'ridge_regression'],
            'customer-eda-quality' => ['classification', 'churned', ['region', 'customer_segment', 'age', 'visits_per_month', 'monthly_spend', 'satisfaction_score'], 'logistic_regression'],
        ];

        if (isset($map[$datasetKey])) {
            [$task, $target, $features, $algorithm] = $map[$datasetKey];
        } elseif ($numericTargets !== []) {
            $task = 'regression';
            $target = $numericTargets[array_key_last($numericTargets)];
            $features = array_slice(array_values(array_diff($availableFeatures, [$target])), 0, 4);
            $algorithm = 'linear_regression';
        } elseif ($classificationTargets !== []) {
            $task = 'classification';
            $target = $classificationTargets[0];
            $features = array_slice(array_values(array_diff($availableFeatures, [$target])), 0, 4);
            $algorithm = 'logistic_regression';
        } else {
            $task = 'clustering';
            $target = null;
            $features = array_slice($numericTargets, 0, 4);
            $algorithm = 'kmeans';
        }

        if ($task === 'classification' && ! in_array($target, $classificationTargets, true)) {
            $task = $numericTargets !== [] ? 'regression' : 'clustering';
            $target = $task === 'regression' ? $numericTargets[array_key_last($numericTargets)] : null;
            $features = array_slice(array_values(array_diff($availableFeatures, [$target])), 0, 4);
            $algorithm = $task === 'regression' ? 'linear_regression' : 'kmeans';
        }

        $features = array_values(array_diff(array_intersect($features, $availableFeatures), [$target]));

        return [
            'task_type' => $task,
            'target_column' => $target,
            'features' => $features,
            'algorithm' => $algorithm,
            'reason' => match ($task) {
                'classification' => 'This dataset has a categorical outcome suitable for supervised classification.',
                'clustering' => 'This dataset can be explored by grouping similar records without a target column.',
                default => 'This dataset has a numeric outcome suitable for supervised regression.',
            },
        ];
    }

    private function boundedInt(mixed $value, int $minimum, int $maximum): int
    {
        return max($minimum, min($maximum, (int) $value));
    }

    private function boundedFloat(mixed $value, float $minimum, float $maximum): float
    {
        return round(max($minimum, min($maximum, (float) $value)), 4);
    }
}
