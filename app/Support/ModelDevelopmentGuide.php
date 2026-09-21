<?php

namespace App\Support;

/**
 * Plain-language teaching content for the student Model Development roadmap.
 *
 * Everything here is static, deterministic copy and simple rules so the
 * Blade views stay readable and the wording can be unit tested.
 */
final class ModelDevelopmentGuide
{
    /** Algorithms that are easiest to understand and are safe first choices. */
    private const BEGINNER_ALGORITHMS = [
        'logistic_regression',
        'decision_tree',
        'linear_regression',
        'decision_tree_regressor',
        'kmeans',
    ];

    /** Metrics stored by the runner on a 0-100 scale. */
    private const PERCENT_METRICS = ['accuracy', 'precision', 'recall', 'f1', 'roc_auc', 'average_precision'];

    /** Metrics that are whole numbers. */
    private const INTEGER_METRICS = ['cluster_count', 'training_time_ms'];

    /**
     * A short "new here?" note for each of the four stops. Two sentences and
     * one warning each; anything longer was not being read.
     *
     * @return array<int, array{what:string,why:string,example:string,tip:string,mistake:string}>
     */
    public static function stepGuides(): array
    {
        return [
            1 => [
                'what' => 'Pick a table of examples. Each row is one example and each column is one fact about it.',
                'why' => 'A model can only learn patterns that are in its data.',
                'example' => 'The breast cancer table has 569 tumours, twelve measurements for each, and whether it was cancer.',
                'tip' => 'Start with a built-in dataset. They are already clean.',
                'mistake' => 'Uploading a sheet with notes or merged cells above the header row.',
            ],
            2 => [
                'what' => 'Choose the column you want predicted. DataSensei picks the clue columns and the learning method for you.',
                'why' => 'The answer column shows the model what "right" looks like while it studies.',
                'example' => 'To predict whether a tumour is cancer, the answer column is "diagnosis" and the measurements are the clues.',
                'tip' => 'Leave everything else on automatic for your first model, then change one thing at a time.',
                'mistake' => 'Leaving in a clue column that is really the answer in disguise. The score looks perfect, but the model is cheating.',
            ],
            3 => [
                'what' => 'Read how often the model was right on rows that were hidden from it during training.',
                'why' => 'A score on hidden rows tells you how the model should do on brand-new data.',
                'example' => '"Right 96 times out of 100" means about 4 in every 100 new tumours would be labelled wrongly.',
                'tip' => 'Compare the score with "always guess the most common answer". A useful model beats that clearly.',
                'mistake' => 'Trusting one number. Check which kind of mistake the model makes, because some mistakes cost more than others.',
            ],
            4 => [
                'what' => 'Type in values for a new example and read the model\'s answer in plain words.',
                'why' => 'This is what models are for: answering a question about something they have never seen.',
                'example' => 'Enter a tumour\'s measurements and the model answers "likely breast cancer" or "likely not", and how sure it is.',
                'tip' => 'Load a real example first. You can compare the model\'s answer with what really happened.',
                'mistake' => 'Treating "very sure" as proof. The model gives an estimate based on past examples.',
            ],
        ];
    }

    /** @return array{what:string,why:string,example:string,tip:string,mistake:string} */
    public static function stepGuide(int $step): array
    {
        $guides = self::stepGuides();

        return $guides[$step] ?? $guides[1];
    }

    /**
     * The journey in one line each, shown on the start page.
     *
     * @return array<int, array{title:string,steps:string,text:string}>
     */
    public static function phases(): array
    {
        $phases = [];
        foreach (ModelDevelopmentRoadmap::steps() as $number => $step) {
            $phases[] = ['title' => $step['label'], 'steps' => 'Step '.$number, 'text' => $step['description']];
        }

        return $phases;
    }

    public static function isBeginnerAlgorithm(string $algorithmKey): bool
    {
        return in_array($algorithmKey, self::BEGINNER_ALGORITHMS, true);
    }

    /** One-sentence explanation of how an algorithm "thinks". */
    public static function algorithmAnalogy(string $algorithmKey): string
    {
        return match ($algorithmKey) {
            'logistic_regression' => 'Adds up weighted clues and turns the total into a probability for each class.',
            'decision_tree' => 'Asks a short series of yes/no questions, like a flowchart, until it reaches an answer.',
            'random_forest' => 'Asks many different decision trees and goes with the majority vote.',
            'knn' => 'Finds the most similar rows it has already seen and copies their most common answer.',
            'naive_bayes' => 'Uses probability rules to estimate which class best explains the values it sees.',
            'svm' => 'Draws the widest possible boundary between the classes.',
            'gradient_boosting' => 'Builds small trees one after another, each fixing the mistakes of the previous ones.',
            'linear_regression' => 'Fits the best straight-line relationship between the features and the number.',
            'decision_tree_regressor' => 'Splits rows into groups with yes/no questions and predicts each group\'s average.',
            'random_forest_regressor' => 'Averages the predictions of many different decision trees.',
            'gradient_boosting_regressor' => 'Adds small trees one after another, each reducing the remaining error.',
            'kmeans' => 'Places group centres and assigns every row to the nearest centre.',
            'auto_classification', 'auto_regression' => 'Holds a small contest between several algorithms on your training rows and keeps the winner.',
            default => 'Learns patterns from the training rows using a controlled configuration.',
        };
    }

    /** Plain explanation for an advanced parameter. */
    public static function parameterHint(string $parameter): string
    {
        return match ($parameter) {
            'c' => 'Lower values keep the model simpler; higher values let it follow the training data more closely.',
            'max_iter' => 'How many improvement rounds the solver may take. Raise it only if training warns that it did not finish.',
            'max_depth' => 'How many questions deep a tree may go. Smaller trees are simpler and overfit less.',
            'min_samples_split' => 'The fewest rows a group must have before the tree may split it again. Higher values give simpler trees.',
            'n_estimators' => 'How many trees are combined. More trees are steadier but take longer to train.',
            'n_neighbors' => 'How many similar rows are consulted. Small numbers react to noise; large numbers smooth things out.',
            'weights' => '"uniform" gives every neighbour an equal vote; "distance" lets closer neighbours count more.',
            'var_smoothing' => 'A tiny safety value that keeps calculations stable. The default is almost always fine.',
            'kernel' => 'The shape of the boundary: "linear" is a straight line, "rbf" and "poly" can curve.',
            'gamma' => 'How far the influence of one training row reaches. "scale" is the safe default.',
            'learning_rate' => 'How big each correction step is. Smaller steps are safer but need more stages.',
            'n_clusters' => 'How many groups K-Means should create. Try 2 to 5 first and compare the silhouette score.',
            'n_init' => 'How many different starting points are tried. The best run is kept.',
            default => 'The safe default works well for a first model.',
        };
    }

    /**
     * @return array<string, array{label:string,plain:string,better:?string}>
     */
    public static function metricGlossary(): array
    {
        return [
            'accuracy' => ['label' => 'Accuracy', 'plain' => 'Share of test rows the model predicted correctly.', 'better' => 'higher'],
            'precision' => ['label' => 'Precision', 'plain' => 'When the model predicts a class, how often it is right.', 'better' => 'higher'],
            'recall' => ['label' => 'Recall', 'plain' => 'Of all real cases of a class, how many the model found.', 'better' => 'higher'],
            'f1' => ['label' => 'F1 Score', 'plain' => 'A balance of precision and recall in one number.', 'better' => 'higher'],
            'roc_auc' => ['label' => 'ROC AUC', 'plain' => 'How well the model ranks the right class above the wrong one.', 'better' => 'higher'],
            'average_precision' => ['label' => 'Average Precision', 'plain' => 'Precision averaged across all confidence thresholds.', 'better' => 'higher'],
            'mae' => ['label' => 'MAE', 'plain' => 'Average size of the prediction error, in the target\'s own unit.', 'better' => 'lower'],
            'mse' => ['label' => 'MSE', 'plain' => 'Average squared error. Large mistakes count much more.', 'better' => 'lower'],
            'rmse' => ['label' => 'RMSE', 'plain' => 'Typical prediction error in the target\'s unit, punishing big misses.', 'better' => 'lower'],
            'r2' => ['label' => 'R²', 'plain' => 'How much of the target\'s variation the model explains (1.0 is perfect).', 'better' => 'higher'],
            'inertia' => ['label' => 'Inertia', 'plain' => 'Total distance from rows to their group centre. Only compare runs with the same features.', 'better' => 'lower'],
            'silhouette' => ['label' => 'Silhouette', 'plain' => 'How clearly separated the groups are, from -1 to 1.', 'better' => 'higher'],
            'cluster_count' => ['label' => 'Clusters', 'plain' => 'Number of groups the model created.', 'better' => null],
            'training_time_ms' => ['label' => 'Training time (ms)', 'plain' => 'How long the algorithm took to learn, in milliseconds.', 'better' => 'lower'],
        ];
    }

    /** @return array{label:string,plain:string,better:?string} */
    public static function metric(string $key): array
    {
        return self::metricGlossary()[$key] ?? [
            'label' => ucwords(str_replace('_', ' ', $key)),
            'plain' => 'Measured during the stored evaluation run.',
            'better' => null,
        ];
    }

    public static function isPercentMetric(string $key): bool
    {
        return in_array($key, self::PERCENT_METRICS, true);
    }

    public static function formatMetric(string $key, mixed $value): string
    {
        if (! is_numeric($value)) {
            return $value === null ? '—' : (string) $value;
        }

        if (in_array($key, self::INTEGER_METRICS, true)) {
            return number_format((float) $value, 0);
        }

        if (self::isPercentMetric($key)) {
            return number_format((float) $value, 2).'%';
        }

        $number = (float) $value;
        $decimals = abs($number) >= 1000 ? 2 : 4;

        return number_format($number, $decimals);
    }

    /**
     * The metric cards to show, in a beginner-friendly order.
     *
     * @param array<string,mixed> $metrics
     * @return array<string,float>
     */
    public static function displayMetrics(string $problemType, array $metrics): array
    {
        $order = match ($problemType) {
            'classification' => ['accuracy', 'f1', 'precision', 'recall', 'roc_auc', 'average_precision'],
            'regression' => ['r2', 'mae', 'rmse'],
            default => ['silhouette', 'cluster_count', 'inertia'],
        };

        $result = [];
        foreach ($order as $key) {
            if (isset($metrics[$key]) && is_numeric($metrics[$key])) {
                $result[$key] = (float) $metrics[$key];
            }
        }

        return $result;
    }

    /**
     * A simple, explainable verdict for students.
     *
     * @param array<string,mixed> $metrics
     * @return array{level:string,title:string,summary:string,metric:string,value:string,next_steps:array<int,string>}
     */
    public static function verdict(string $problemType, array $metrics): array
    {
        if ($problemType === 'classification') {
            // Accuracy is the number a beginner can picture ("right 96 times in 100").
            $key = is_numeric($metrics['accuracy'] ?? null) ? 'accuracy' : 'f1';
            $value = is_numeric($metrics[$key] ?? null) ? (float) $metrics[$key] : null;
            $level = self::level($value, 85.0, 70.0);

            // A high score means little when always giving the most common answer scores the same.
            $baseline = is_numeric($metrics['baseline_accuracy'] ?? null) ? (float) $metrics['baseline_accuracy'] : null;
            if ($key === 'accuracy' && $value !== null && $baseline !== null && $value - $baseline < 3.0 && $level !== 'weak') {
                $level = $level === 'strong' ? 'fair' : 'weak';
            }

            return self::buildVerdict($level, $key, $value, [
                'strong' => 'The model classified most unseen test rows correctly.',
                'fair' => 'The model gets many test rows right but still makes a noticeable number of mistakes.',
                'weak' => 'The model often picks the wrong class on unseen rows.',
            ], [
                'strong' => ['Check the confusion matrix to see which classes are still mixed up.', 'Try a prediction with your own values.'],
                'fair' => ['Look at the confusion matrix to find the classes that are confused most.', 'Try a different algorithm, such as Random Forest, and compare.'],
                'weak' => ['Revisit your features: remove unrelated columns and add informative ones.', 'Check that the target really is a category and has enough rows per class.'],
            ]);
        }

        if ($problemType === 'regression') {
            $value = is_numeric($metrics['r2'] ?? null) ? (float) $metrics['r2'] : null;
            $level = self::level($value, 0.75, 0.50);
            $verdict = self::buildVerdict($level, 'r2', $value, [
                'strong' => 'The features explain most of the changes in the target value.',
                'fair' => 'The model captures part of the pattern, but many predictions are still off.',
                'weak' => 'The model explains little of the target\'s variation.',
            ], [
                'strong' => ['Check the residual plot for any remaining pattern.', 'Try a prediction with your own values.'],
                'fair' => ['Compare RMSE with the typical size of the target to judge if the error is acceptable.', 'Try a tree-based model to capture non-linear patterns.'],
                'weak' => ['Add features that are more closely related to the target.', 'Check for outliers or a target that is mostly random.'],
            ]);

            if ($value !== null && $value < 0) {
                $verdict['summary'] = 'R² is below zero, so the model did worse than always guessing the average value.';
            }

            return $verdict;
        }

        $value = is_numeric($metrics['silhouette'] ?? null) ? (float) $metrics['silhouette'] : null;
        $level = self::level($value, 0.50, 0.25);

        return self::buildVerdict($level, 'silhouette', $value, [
            'strong' => 'The groups are clearly separated from each other.',
            'fair' => 'The groups overlap somewhat. Look at the scatter plot before naming them.',
            'weak' => 'The groups are not well separated.',
        ], [
            'strong' => ['Look at each cluster\'s rows and describe what they have in common.'],
            'fair' => ['Try a different number of clusters and compare silhouette scores.'],
            'weak' => ['Try fewer clusters or different numeric features.', 'Make sure feature scaling is enabled.'],
        ]);
    }

    /** @return array<string,string> */
    public static function chartGuides(): array
    {
        return [
            'confusion_matrix' => 'Rows are the real classes, columns are the predicted ones. Big numbers on the diagonal are correct predictions.',
            'class_distribution' => 'How many rows belong to each class. Very uneven bars mean the classes are imbalanced.',
            'roc_curve' => 'The closer the curve hugs the top-left corner, the better the model separates the two classes.',
            'precision_recall_curve' => 'Shows the trade-off between being precise and finding every positive case. Higher and further right is better.',
            'feature_importance' => 'Longer bars are columns the model relied on more. Influence does not prove cause and effect.',
            'actual_vs_predicted' => 'Each dot is a test row. Dots close to the dashed line were predicted accurately.',
            'residual_plot' => 'Residual = actual minus predicted. A healthy model shows dots scattered randomly around zero.',
            'cluster_scatter' => 'Each colour is one discovered group, drawn using the first two selected features.',
            'cluster_sizes' => 'How many rows landed in each group.',
            'correlation_heatmap' => 'Darker or stronger colours show numeric columns that move together.',
            'missing_values' => 'How many values are empty in each column before cleaning.',
            'missing_value_heatmap' => 'Where empty values appear across the rows.',
            'distribution_plots' => 'The spread of values in each numeric column.',
            'learning_curve' => 'If the training and validation lines stay far apart, the model may be memorising instead of learning.',
            'validation_curve' => 'How the score changes as one setting changes. The peak of the validation line is the sweet spot.',
        ];
    }

    public static function chartGuide(string $chart): string
    {
        return self::chartGuides()[$chart] ?? 'Generated from this model version\'s evaluation run.';
    }

    /** Plain explanation of what a training worker stage is doing. */
    public static function trainingStageExplanations(): array
    {
        return [
            'Reading your rows and checking that the selected columns exist.',
            'Filling empty values, turning categories into numbers, and scaling where needed.',
            'The algorithm studies the training rows and learns patterns.',
            'The model predicts the hidden test rows and the scores are calculated.',
            'Charts, explanations, and the model file are saved to your history.',
        ];
    }

    private static function level(?float $value, float $strong, float $fair): string
    {
        if ($value === null) {
            return 'unknown';
        }

        return match (true) {
            $value >= $strong => 'strong',
            $value >= $fair => 'fair',
            default => 'weak',
        };
    }

    /**
     * @param array<string,string> $summaries
     * @param array<string,array<int,string>> $nextSteps
     * @return array{level:string,title:string,summary:string,metric:string,value:string,next_steps:array<int,string>}
     */
    private static function buildVerdict(string $level, string $metricKey, ?float $value, array $summaries, array $nextSteps): array
    {
        $titles = [
            'strong' => 'Strong result',
            'fair' => 'Fair result, room to improve',
            'weak' => 'Needs improvement',
            'unknown' => 'Result not rated',
        ];

        return [
            'level' => $level,
            'title' => $titles[$level],
            'summary' => $summaries[$level] ?? 'This model version did not report the score used for a quick rating. Review the charts below.',
            'metric' => self::metric($metricKey)['label'],
            'value' => self::formatMetric($metricKey, $value),
            'next_steps' => $nextSteps[$level] ?? ['Review the charts and try another configuration.'],
        ];
    }
}
