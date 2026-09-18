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
     * Beginner explanation for every roadmap step.
     *
     * @return array<int, array{what:string,why:string,example:string,tip:string,mistake:string}>
     */
    public static function stepGuides(): array
    {
        return [
            1 => [
                'what' => 'Pick the table of data your model will learn from. Each row is one example, and each column is one piece of information about that example.',
                'why' => 'A model can only learn patterns that exist in its data. Clean, relevant data matters more than a clever algorithm.',
                'example' => 'The Iris dataset has 150 flowers. Each row lists petal and sepal sizes plus the flower species.',
                'tip' => 'New to this? Start with a built-in dataset. They are already cleaned and come with a recommended setup.',
                'mistake' => 'Uploading a file with merged header cells or notes above the table. Keep one header row and one record per row.',
            ],
            2 => [
                'what' => 'Choose the one column you want the model to predict. This column is called the target (or label).',
                'why' => 'The target tells the model what the "right answer" looks like during training.',
                'example' => 'In a student dataset, final_score could be the target, because that is what we want to predict.',
                'tip' => 'Ask yourself: "What question do I want answered?" The answer to that question is your target column.',
                'mistake' => 'Choosing an ID or name column as the target. IDs are unique labels, not something a model can learn.',
            ],
            3 => [
                'what' => 'Tick the columns the model is allowed to look at when it makes a prediction. These are called features.',
                'why' => 'Good features carry clues about the target. Irrelevant features add noise and can make results worse.',
                'example' => 'To predict a house price, size, number of rooms, and location are useful features. The listing ID is not.',
                'tip' => 'Start with the recommended features, train once, then experiment by removing or adding one feature at a time.',
                'mistake' => 'Including a column that directly contains the answer (for example "passed" when predicting "final_grade"). That makes results look unrealistically good.',
            ],
            4 => [
                'what' => 'Tell DataSensei what kind of answer you expect: a category, a number, or groups discovered without a target.',
                'why' => 'Each problem type uses different algorithms and different ways of measuring success.',
                'example' => 'Yes/No or species names → Classification. A price or a score → Regression. No target at all → Clustering.',
                'tip' => 'DataSensei suggests a problem type from your target column. Keep the suggestion unless you have a clear reason to change it.',
                'mistake' => 'Using classification for a number with hundreds of different values, such as a price. That is a regression problem.',
            ],
            5 => [
                'what' => 'Hide part of the data from the model during training so it can be tested on rows it has never seen.',
                'why' => 'A model that is tested on its own training data can simply memorize answers. The test set shows how it behaves on new data.',
                'example' => 'An 80/20 split trains on 80 out of every 100 rows and keeps 20 rows for the final exam.',
                'tip' => '80% training / 20% testing is a reliable default. Leave the Advanced Options alone for your first model.',
                'mistake' => 'Using a very small test set on a small dataset. The score then depends heavily on which few rows were picked.',
            ],
            6 => [
                'what' => 'Choose the learning method. Only methods that fit your problem type are shown.',
                'why' => 'Different algorithms find patterns in different ways. Simple ones are easier to explain; complex ones can capture more detail.',
                'example' => 'A Decision Tree learns a list of yes/no questions, like "Is petal length below 2.5 cm?"',
                'tip' => 'Pick one marked "Beginner friendly" first. Then train a second model with a different algorithm and compare the results.',
                'mistake' => 'Changing many advanced parameters at once. If the result changes, you will not know which setting caused it.',
            ],
            7 => [
                'what' => 'Review your choices, name the model, and start training. The model studies the training rows and learns patterns.',
                'why' => 'Training turns your choices into a real, reusable model that can be evaluated and used for predictions.',
                'example' => 'Name it something you will recognise later, such as "Iris – Decision Tree – 80/20".',
                'tip' => 'Training runs in the background. You can leave the page open and watch the real progress update.',
                'mistake' => 'Clicking Train many times. Each click creates a new job, so wait for the progress page to appear.',
            ],
            8 => [
                'what' => 'Read the scores and charts that were measured on the hidden test rows.',
                'why' => 'Evaluation tells you whether the model is actually useful before anyone relies on its predictions.',
                'example' => 'An accuracy of 90% means 9 out of 10 test rows were predicted correctly.',
                'tip' => 'Look at more than one number. For classification, compare accuracy with F1; for regression, check R² and the typical error together.',
                'mistake' => 'Treating a single high score as proof. Check the charts for patterns the number hides.',
            ],
            9 => [
                'what' => 'Type in values for a new example and let the trained model predict the answer.',
                'why' => 'This is how a model is used in real life: new information goes in, a prediction comes out.',
                'example' => 'Enter the measurements of a new flower and the model predicts its species.',
                'tip' => 'Use "Fill with typical values" first, then change one field at a time to see how the prediction reacts.',
                'mistake' => 'Entering values far outside the training range. Models are least reliable on examples unlike anything they have seen.',
            ],
            10 => [
                'what' => 'Your trained version is stored in Model History with its settings, scores, and charts.',
                'why' => 'Saved versions let you compare experiments, return to earlier results, and print a report.',
                'example' => 'Train v2 with different features, compare it with v1, and keep whichever version performs better.',
                'tip' => 'Open the report to review everything on one page, or train a new version to keep improving.',
                'mistake' => 'Deleting an older version before comparing it with the new one.',
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
     * The three big phases shown on the start page.
     *
     * @return array<int, array{title:string,steps:string,text:string}>
     */
    public static function phases(): array
    {
        return [
            ['title' => 'Prepare', 'steps' => 'Steps 1–6', 'text' => 'Pick a dataset, decide what to predict, and choose how the model should learn.'],
            ['title' => 'Train', 'steps' => 'Step 7', 'text' => 'DataSensei trains a real scikit-learn model in a secure worker.'],
            ['title' => 'Use', 'steps' => 'Steps 8–10', 'text' => 'Check the scores, try a prediction, and keep the version in your history.'],
        ];
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
            $key = is_numeric($metrics['f1'] ?? null) ? 'f1' : 'accuracy';
            $value = is_numeric($metrics[$key] ?? null) ? (float) $metrics[$key] : null;
            $level = self::level($value, 85.0, 70.0);

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
            'fair' => 'Fair result – room to improve',
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
