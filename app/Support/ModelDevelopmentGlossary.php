<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Short, plain-language definitions for the words a student meets in Model
 * Development. Each definition is written for someone who has never studied
 * machine learning: one or two sentences, no formulas, an everyday comparison
 * where it helps.
 *
 * The views print a term with {{ ModelDevelopmentGlossary::term('accuracy') }}.
 * That renders the word with a dotted underline; pointing at it, focusing it
 * with the keyboard, or tapping it opens the definition.
 */
final class ModelDevelopmentGlossary
{
    private static int $sequence = 0;

    /** @return array<string, array{label:string,definition:string}> */
    public static function terms(): array
    {
        return [
            // The basics
            'model' => ['label' => 'Model', 'definition' => 'A set of patterns the computer found in your data. You give it new values and it gives you its best answer.'],
            'machine_learning' => ['label' => 'Machine learning', 'definition' => 'Teaching a computer by showing it many examples instead of writing rules by hand.'],
            'dataset' => ['label' => 'Dataset', 'definition' => 'A table of examples. Each row is one example and each column is one fact about it.'],
            'row' => ['label' => 'Row', 'definition' => 'One example in the table, such as one patient, one house or one flower.'],
            'column' => ['label' => 'Column', 'definition' => 'One kind of fact recorded for every row, such as age or price.'],
            'target' => ['label' => 'Target', 'definition' => 'The column that holds the answer you want the model to predict. Some books call it the label.'],
            'feature' => ['label' => 'Feature', 'definition' => 'A column the model is allowed to look at as a clue when it predicts the answer.'],
            'prediction' => ['label' => 'Prediction', 'definition' => 'The model\'s best answer for values it has not seen before. It is an estimate, not a fact.'],
            'training' => ['label' => 'Training', 'definition' => 'The stage where the model studies rows that already have the answer and looks for patterns.'],

            // Kinds of problem
            'classification' => ['label' => 'Classification', 'definition' => 'Predicting which group something belongs to, such as yes or no, or a flower species.'],
            'regression' => ['label' => 'Regression', 'definition' => 'Predicting a number on a scale, such as a price, a score or a temperature.'],
            'clustering' => ['label' => 'Clustering', 'definition' => 'Sorting rows into groups of similar rows when there is no answer column to learn from.'],
            'class' => ['label' => 'Class', 'definition' => 'One of the possible answers in a classification problem, such as "benign" or "malignant".'],

            // Splitting and checking
            'train_test_split' => ['label' => 'Train/test split', 'definition' => 'Hiding some rows from the model while it learns, then using them as a final exam.'],
            'training_rows' => ['label' => 'Training rows', 'definition' => 'The rows the model is allowed to study. They work like practice questions with the answers shown.'],
            'test_rows' => ['label' => 'Test rows', 'definition' => 'Rows kept hidden until training ends. The model is scored on these, like an exam it has never seen.'],
            'cross_validation' => ['label' => 'Cross-validation', 'definition' => 'Repeating the practice exam several times on different slices of the data, so one lucky split cannot fool you.'],
            'baseline' => ['label' => 'Baseline', 'definition' => 'The score you would get with no model at all, by always giving the most common answer. A useful model must beat it.'],
            'overfitting' => ['label' => 'Overfitting', 'definition' => 'When a model memorises its practice rows instead of learning the pattern, so it does well in practice and badly on new data.'],
            'underfitting' => ['label' => 'Underfitting', 'definition' => 'When a model is too simple to pick up the pattern, so it does badly even on its practice rows.'],
            'data_leakage' => ['label' => 'Data leakage', 'definition' => 'When a clue column secretly contains the answer. The score looks perfect, but the model is cheating and will fail on real data.'],

            // Scores for yes/no and category answers
            'accuracy' => ['label' => 'Accuracy', 'definition' => 'Out of every 100 answers, how many the model got right.'],
            'precision' => ['label' => 'Precision', 'definition' => 'When the model says "yes", how often it is right. Low precision means many false alarms.'],
            'recall' => ['label' => 'Recall', 'definition' => 'Out of all the real "yes" cases, how many the model caught. Low recall means many missed cases.'],
            'f1' => ['label' => 'F1 score', 'definition' => 'One number that balances precision and recall. It is only high when both are high.'],
            'roc_auc' => ['label' => 'ROC AUC', 'definition' => 'How well the model ranks a real "yes" above a real "no". 50% is a coin flip and 100% is perfect.'],
            'average_precision' => ['label' => 'Average precision', 'definition' => 'Precision averaged over every level of strictness. Helpful when "yes" cases are rare.'],
            'confusion_matrix' => ['label' => 'Confusion matrix', 'definition' => 'A small table that counts correct answers and each kind of mistake, so you can see what gets mixed up.'],
            'false_positive' => ['label' => 'False alarm', 'definition' => 'The model said "yes" but the real answer was "no". Also called a false positive.'],
            'false_negative' => ['label' => 'Missed case', 'definition' => 'The model said "no" but the real answer was "yes". Also called a false negative.'],
            'confidence' => ['label' => 'Confidence', 'definition' => 'How sure the model is about one answer, from 0 to 100%. High confidence can still be wrong.'],
            'probability' => ['label' => 'Probability', 'definition' => 'The model\'s estimated chance for each possible answer. The chances add up to 100%.'],

            // Scores for number answers
            'r2' => ['label' => 'R² (R squared)', 'definition' => 'How much of the ups and downs in the answer the model explains. 1 is perfect and 0 is no better than guessing the average.'],
            'mae' => ['label' => 'MAE', 'definition' => 'Mean absolute error: on average, how far off each prediction is, in the same unit as the answer.'],
            'rmse' => ['label' => 'RMSE', 'definition' => 'Like the average miss, but big misses count extra. Useful when large errors are costly.'],
            'residual' => ['label' => 'Residual', 'definition' => 'The real value minus the predicted value for one row. In other words, the size of that one miss.'],

            // Scores for groups
            'silhouette' => ['label' => 'Silhouette score', 'definition' => 'How clearly the groups are separated, from -1 to 1. Closer to 1 means tidy, well separated groups.'],
            'inertia' => ['label' => 'Inertia', 'definition' => 'How tightly rows sit around the centre of their group. Lower means tighter groups.'],
            'cluster' => ['label' => 'Cluster', 'definition' => 'A group of similar rows that the model discovered by itself.'],

            // How the model learns
            'algorithm' => ['label' => 'Algorithm', 'definition' => 'The learning method. Different algorithms look for patterns in different ways.'],
            'automatic_choice' => ['label' => 'Automatic choice', 'definition' => 'DataSensei tries several algorithms on practice exams made from your training rows and keeps the best one.'],
            'tuning' => ['label' => 'Fine-tuning', 'definition' => 'Trying a few different settings for an algorithm and keeping the ones that score best.'],
            'hyperparameter' => ['label' => 'Setting (hyperparameter)', 'definition' => 'A dial you set before training, such as how deep a decision tree may grow.'],
            'scaling' => ['label' => 'Scaling', 'definition' => 'Putting all number columns on a similar scale so a column with big numbers does not drown out the others.'],
            'imputation' => ['label' => 'Filling empty cells', 'definition' => 'Replacing blanks with a sensible value, such as the middle value of that column, so no row is wasted.'],
            'one_hot_encoding' => ['label' => 'One-hot encoding', 'definition' => 'Turning a text column into several yes/no columns, because models only understand numbers.'],
            'feature_importance' => ['label' => 'Feature importance', 'definition' => 'How much the model relied on each column. It shows influence, not proof that one thing causes another.'],
            'random_state' => ['label' => 'Random seed', 'definition' => 'A number that makes the random shuffling repeatable, so you get the same split every time.'],
            'class_imbalance' => ['label' => 'Class imbalance', 'definition' => 'When one answer is far more common than the others. A lazy model can look accurate just by always picking it.'],
            'outlier' => ['label' => 'Outlier', 'definition' => 'A value far away from the rest, such as one house priced ten times higher than any other.'],
            'version' => ['label' => 'Version', 'definition' => 'A saved copy of your model. Training again makes a new version and keeps the old ones.'],

            // Algorithms in one line each
            'logistic_regression' => ['label' => 'Logistic Regression', 'definition' => 'Adds up weighted clues and turns the total into a chance for each answer.'],
            'decision_tree' => ['label' => 'Decision Tree', 'definition' => 'Asks a chain of yes/no questions, like a flowchart, until it reaches an answer.'],
            'random_forest' => ['label' => 'Random Forest', 'definition' => 'Asks many different decision trees and goes with the majority vote.'],
            'gradient_boosting' => ['label' => 'Gradient Boosting', 'definition' => 'Builds small trees one after another, each one fixing the mistakes of the last.'],
            'knn' => ['label' => 'K-Nearest Neighbors', 'definition' => 'Finds the most similar rows it has seen and copies their most common answer.'],
            'naive_bayes' => ['label' => 'Naive Bayes', 'definition' => 'Uses simple probability rules to decide which answer best explains the values.'],
            'svm' => ['label' => 'Support Vector Machine', 'definition' => 'Draws the widest possible boundary between the groups.'],
            'linear_regression' => ['label' => 'Linear Regression', 'definition' => 'Fits the best straight-line relationship between the clues and the number.'],
            'kmeans' => ['label' => 'K-Means', 'definition' => 'Places a centre for each group and assigns every row to its nearest centre.'],
        ];
    }

    /** @return array{label:string,definition:string}|null */
    public static function find(string $key): ?array
    {
        return self::terms()[self::normalize($key)] ?? null;
    }

    public static function definition(string $key): string
    {
        return self::find($key)['definition'] ?? '';
    }

    /**
     * The word, with its definition attached as an accessible tooltip.
     * Unknown keys print as plain escaped text so a typo never breaks a page.
     */
    public static function term(string $key, ?string $text = null): HtmlString
    {
        $entry = self::find($key);
        $word = e($text ?? ($entry['label'] ?? $key));
        if ($entry === null) {
            return new HtmlString($word);
        }

        $id = 'ml-term-'.(++self::$sequence);

        return new HtmlString(
            '<span class="ml-term" data-ml-term>'
            .'<button type="button" class="ml-term-word" aria-expanded="false" aria-describedby="'.$id.'">'.$word.'</button>'
            .'<span class="ml-term-tip" role="tooltip" id="'.$id.'">'
            .'<b>'.e($entry['label']).'</b>'.e($entry['definition'])
            .'</span></span>'
        );
    }

    /** Maps an algorithm key such as "random_forest_regressor" to its glossary entry. */
    public static function algorithmKey(string $algorithmKey): string
    {
        return match (true) {
            str_starts_with($algorithmKey, 'auto_') => 'automatic_choice',
            str_starts_with($algorithmKey, 'random_forest') => 'random_forest',
            str_starts_with($algorithmKey, 'gradient_boosting') => 'gradient_boosting',
            str_starts_with($algorithmKey, 'decision_tree') => 'decision_tree',
            default => $algorithmKey,
        };
    }

    private static function normalize(string $key): string
    {
        return strtolower(trim(str_replace([' ', '-'], '_', $key)));
    }
}
