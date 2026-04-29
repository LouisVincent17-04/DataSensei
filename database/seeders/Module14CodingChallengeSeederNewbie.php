<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 14 — Supervised Learning (Newbie) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering beginner supervised learning in Python
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mirrors Module 14 curriculum):
 *   14.1  What Is Supervised Learning?
 *   14.2  Train/Test Split & Cross-Validation
 *   14.3  Linear Regression
 *   14.4  Logistic Regression & Classification Metrics
 *   14.5  Decision Trees
 *   14.6  Random Forests & Ensemble Methods
 *   14.7  Support Vector Machines (SVM)
 *   14.8  K-Nearest Neighbors (KNN)
 *   14.9  Hyperparameter Tuning: Grid Search & Pipelines
 *   14.10 Gradient Boosting & the Bias-Variance Tradeoff
 *
 * Difficulty: Newbie — foundational ML concepts implemented from scratch
 *             using only Python built-ins and the math module.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module14CodingChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (! $category) {
            $this->command->error('Newbie category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 14 — Supervised Learning (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Supervised Learning',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Implement fundamental supervised learning algorithms from scratch in Python. Practice train/test splitting, regression, classification metrics, decision trees, KNN, and more using only Python built-ins and the math module.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 500,
                'order_index'        => 14,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 14.1: What Is Supervised Learning? (Q1–Q4)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Classify a learning task as `regression` or `classification`.

Rules:
- If the target label is a continuous number → `regression`
- If the target label is a category/class → `classification`

Read `n` label samples (one per line). Print `regression` if all can be converted to float, otherwise print `classification`.

Example:
```
Input:
3
1.5
2.3
3.7
Output: regression
```
```
Input:
3
cat
dog
cat
Output: classification
```
MD,
                'starter_code'        => "n = int(input())\nlabels = [input() for _ in range(n)]\n# Determine task type and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Identify whether a dataset is **labeled** or **unlabeled**.

Read `n` rows. Each row has either 2 space-separated values (feature + label) or just 1 value (feature only).

Print `labeled` if every row has exactly 2 values, otherwise print `unlabeled`.

Example:
```
Input:
3
5 1
8 0
3 1
Output: labeled
```
```
Input:
3
5
8
3
Output: unlabeled
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split() for _ in range(n)]\n# Determine if labeled or unlabeled\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Count the number of **features** and **samples** in a dataset.

Read `n` lines, each containing space-separated feature values (no label). Print `Samples: <n>` then `Features: <f>` where `f` is the number of values per row.

Example:
```
Input:
3
1 2 3
4 5 6
7 8 9
Output:
Samples: 3
Features: 3
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split() for _ in range(n)]\n# Print sample and feature counts\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Count **unique classes** in a classification target column.

Read `n` labels (one per line, strings). Print the number of unique classes, then the unique class names sorted alphabetically, one per line.

Example:
```
Input:
5
cat
dog
cat
fish
dog
Output:
3
cat
dog
fish
```
MD,
                'starter_code'        => "n = int(input())\nlabels = [input() for _ in range(n)]\n# Print count and sorted unique classes\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 14.2: Train/Test Split & Cross-Validation (Q5–Q9)
            // ═══════════════════════════════════════════════════════════════

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Perform a **train/test split** on a list of indices.

Given `n` total samples and a test ratio (e.g. `0.2`), compute the number of training samples and test samples.

- Test count = floor(n × ratio)
- Train count = n − test count

Read `n` and `ratio`. Print `Train: <train_count>` then `Test: <test_count>`.

Example:
```
Input:
100
0.2
Output:
Train: 80
Test: 20
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nratio = float(input())\n# Compute and print split counts\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Split a list of values into **train** and **test** sets.

Read `n` values (one per line), then a float `ratio` (test proportion). The last `floor(n × ratio)` values form the test set; the rest form the train set.

Print `Train:` followed by train values space-separated, then `Test:` followed by test values space-separated.

Example:
```
Input:
5
10
20
30
40
50
0.4
Output:
Train: 10 20 30
Test: 40 50
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nvalues = [int(input()) for _ in range(n)]\nratio = float(input())\n# Split and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Compute **k-fold cross-validation** fold sizes.

Given `n` total samples and `k` folds, each fold has `floor(n / k)` samples. The first `n mod k` folds get one extra sample.

Read `n` and `k`. Print the size of each fold, one per line.

Example:
```
Input:
10
3
Output:
4
3
3
```
MD,
                'starter_code'        => "n = int(input())\nk = int(input())\n# Print fold sizes\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Compute the **training and validation sizes** for one fold in k-fold cross-validation.

Given `n` samples and `k` folds, assume equal fold sizes (`n` is divisible by `k`). For a given fold index `i` (0-based):
- Validation size = n / k
- Training size = n − validation size

Read `n`, `k`, and `i`. Print `Train: <train>` then `Val: <val>`.

Example:
```
Input:
100
5
2
Output:
Train: 80
Val: 20
```
MD,
                'starter_code'        => "n = int(input())\nk = int(input())\ni = int(input())\n# Compute and print train/val sizes\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Compute the **average validation accuracy** across k folds.

Read `k` accuracy scores (floats between 0 and 1, one per line). Print the mean rounded to 4 decimal places.

Example:
```
Input:
4
0.80
0.85
0.90
0.75
Output: 0.825
```
MD,
                'starter_code'        => "k = int(input())\nscores = [float(input()) for _ in range(k)]\n# Print mean accuracy\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 14.3: Linear Regression (Q10–Q14)
            // ═══════════════════════════════════════════════════════════════

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Compute the **slope** and **intercept** of a simple linear regression line.

Formulas:
- slope (m) = (n·Σxy − Σx·Σy) / (n·Σx² − (Σx)²)
- intercept (b) = (Σy − m·Σx) / n

Read `n`, then `n` pairs of `x y` values (space-separated). Print `Slope: <m>` and `Intercept: <b>`, each rounded to 4 decimal places.

Example:
```
Input:
3
1 2
2 4
3 6
Output:
Slope: 2.0
Intercept: 0.0
```
MD,
                'starter_code'        => "n = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Compute slope and intercept\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Predict `y` values using a linear regression model `y = m·x + b`.

Read `slope` and `intercept` (floats), then `n` x-values (one per line). Print each predicted y value rounded to 4 decimal places, one per line.

Example:
```
Input:
2.0
1.0
3
0
5
10
Output:
1.0
11.0
21.0
```
MD,
                'starter_code'        => "slope = float(input())\nintercept = float(input())\nn = int(input())\nxs = [float(input()) for _ in range(n)]\n# Print predictions\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Compute the **Mean Squared Error (MSE)** of predictions.

MSE = Σ(y_actual − y_predicted)² / n

Read `n`, then `n` lines each with `actual predicted` (space-separated floats). Print the MSE rounded to 4 decimal places.

Example:
```
Input:
3
2 2.5
4 3.5
6 6.0
Output: 0.25
```
MD,
                'starter_code'        => "n = int(input())\npairs = [list(map(float, input().split())) for _ in range(n)]\n# Compute and print MSE\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Compute the **R-squared (R²)** score of a regression model.

R² = 1 − SS_res / SS_tot

where:
- SS_res = Σ(y_actual − y_predicted)²
- SS_tot = Σ(y_actual − y_mean)²

Read `n`, then `n` lines of `actual predicted` pairs. Print R² rounded to 4 decimal places.

Example:
```
Input:
3
1 1
2 2
3 3
Output: 1.0
```
MD,
                'starter_code'        => "n = int(input())\npairs = [list(map(float, input().split())) for _ in range(n)]\n# Compute and print R-squared\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Compute the **Mean Absolute Error (MAE)** of predictions.

MAE = Σ|y_actual − y_predicted| / n

Read `n`, then `n` lines of `actual predicted` pairs. Print the MAE rounded to 4 decimal places.

Example:
```
Input:
4
3 2.5
5 4.0
7 8.0
9 9.0
Output: 0.875
```
MD,
                'starter_code'        => "n = int(input())\npairs = [list(map(float, input().split())) for _ in range(n)]\n# Compute and print MAE\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 14.4: Logistic Regression & Classification Metrics (Q15–Q20)
            // ═══════════════════════════════════════════════════════════════

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Compute the **sigmoid** function for a given value.

sigmoid(z) = 1 / (1 + e^(−z))

Read a float `z`. Print the sigmoid value rounded to 4 decimal places.

Example:
```
Input:
0
Output: 0.5
```
MD,
                'starter_code'        => "import math\n\nz = float(input())\n# Compute and print sigmoid\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Apply a **classification threshold** to sigmoid outputs.

Read `n` sigmoid values (floats, one per line) and a threshold `t`. If value >= t → print `1`, else → print `0`.

Example:
```
Input:
4
0.8
0.3
0.5
0.6
0.5
Output:
1
0
1
1
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nt = float(input())\n# Apply threshold and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Compute **accuracy** of a binary classifier.

Accuracy = correct predictions / total predictions

Read `n`, then `n` lines of `actual predicted` (space-separated 0s and 1s). Print accuracy rounded to 4 decimal places.

Example:
```
Input:
4
1 1
0 0
1 0
0 1
Output: 0.5
```
MD,
                'starter_code'        => "n = int(input())\npairs = [list(map(int, input().split())) for _ in range(n)]\n# Compute and print accuracy\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Build a **confusion matrix** for binary classification.

Read `n`, then `n` lines of `actual predicted` (0 or 1). Print:
```
TP: <tp>
FP: <fp>
TN: <tn>
FN: <fn>
```

Example:
```
Input:
4
1 1
0 1
0 0
1 0
Output:
TP: 1
FP: 1
TN: 1
FN: 1
```
MD,
                'starter_code'        => "n = int(input())\npairs = [list(map(int, input().split())) for _ in range(n)]\n# Compute and print confusion matrix values\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Compute **precision** and **recall** for binary classification.

- Precision = TP / (TP + FP)
- Recall = TP / (TP + FN)

Read `TP`, `FP`, `FN` (one per line). Print `Precision: <p>` and `Recall: <r>`, each rounded to 4 decimal places.

Example:
```
Input:
8
2
2
Output:
Precision: 0.8
Recall: 0.8
```
MD,
                'starter_code'        => "tp = int(input())\nfp = int(input())\nfn = int(input())\n# Compute and print precision and recall\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Compute the **F1 Score** from precision and recall.

F1 = 2 × (precision × recall) / (precision + recall)

Read `precision` and `recall` (floats). Print the F1 score rounded to 4 decimal places.

Example:
```
Input:
0.8
0.6
Output: 0.6857
```
MD,
                'starter_code'        => "precision = float(input())\nrecall = float(input())\n# Compute and print F1 score\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 14.5: Decision Trees (Q21–Q25)
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Compute the **Gini Impurity** of a node.

Gini = 1 − Σ(pᵢ²)

where pᵢ is the proportion of class i.

Read `k` class counts (one per line). Print the Gini impurity rounded to 4 decimal places.

Example:
```
Input:
2
50
50
Output: 0.5
```
MD,
                'starter_code'        => "k = int(input())\ncounts = [int(input()) for _ in range(k)]\n# Compute and print Gini impurity\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Compute the **Entropy** of a node.

Entropy = −Σ(pᵢ × log₂(pᵢ))

where pᵢ is the proportion of class i (skip classes with count 0).

Read `k` class counts (one per line). Print the entropy rounded to 4 decimal places.

Example:
```
Input:
2
50
50
Output: 1.0
```
MD,
                'starter_code'        => "import math\n\nk = int(input())\ncounts = [int(input()) for _ in range(k)]\n# Compute and print entropy\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Compute the **Information Gain** of a split.

IG = Entropy(parent) − Σ(|child| / |parent|) × Entropy(child)

Read:
- Line 1: parent entropy (float)
- Line 2: number of children `c`
- For each child: first line is child size, second line is child entropy

Print the information gain rounded to 4 decimal places.

Example:
```
Input:
1.0
2
50
0.0
50
0.0
Output: 1.0
```
MD,
                'starter_code'        => "parent_entropy = float(input())\nc = int(input())\nchildren = []\nfor _ in range(c):\n    size = int(input())\n    entropy = float(input())\n    children.append((size, entropy))\n# Compute information gain\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Determine the **majority class** label for a decision tree leaf.

Read `k` class names and counts (one `name count` pair per line). Print the class with the highest count. If tied, print the alphabetically first class.

Example:
```
Input:
3
cat 10
dog 25
fish 5
Output: dog
```
MD,
                'starter_code'        => "k = int(input())\nclasses = []\nfor _ in range(k):\n    parts = input().split()\n    classes.append((parts[0], int(parts[1])))\n# Find and print majority class\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Apply a simple **1-level decision tree** (decision stump) to classify samples.

The stump rule: if feature value <= threshold → class A, else → class B.

Read `threshold` (float), `class_a`, `class_b` (strings), then `n` feature values (one per line). Print the predicted class for each, one per line.

Example:
```
Input:
5.0
small
large
4
3
6
5
7
Output:
small
large
small
large
```
MD,
                'starter_code'        => "threshold = float(input())\nclass_a = input()\nclass_b = input()\nn = int(input())\nfeatures = [float(input()) for _ in range(n)]\n# Apply stump and print predictions\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 14.6: Random Forests & Ensemble Methods (Q26–Q29)
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Implement **majority vote** ensemble prediction.

Given predictions from multiple classifiers, output the class that appears most often. If tied, output the alphabetically first class.

Read `k` classifier predictions (strings, one per line). Print the majority vote winner.

Example:
```
Input:
5
cat
dog
cat
fish
cat
Output: cat
```
MD,
                'starter_code'        => "k = int(input())\npredictions = [input() for _ in range(k)]\n# Determine majority vote\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Compute the **average ensemble prediction** (soft voting / bagging).

Read `k` float predictions (one per line). Print their mean rounded to 4 decimal places.

Example:
```
Input:
4
0.8
0.7
0.9
0.6
Output: 0.75
```
MD,
                'starter_code'        => "k = int(input())\npredictions = [float(input()) for _ in range(k)]\n# Compute and print mean\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Simulate **bootstrap sampling** by computing sample size for a bootstrap set.

A bootstrap sample has the same size as the original dataset (sampling with replacement). Given `n` samples, print the bootstrap sample size and the expected number of unique samples (approximately 63.2% of n — use `round(0.632 * n)`).

Read `n`. Print `Bootstrap size: <n>` then `Expected unique: <u>`.

Example:
```
Input:
100
Output:
Bootstrap size: 100
Expected unique: 63
```
MD,
                'starter_code'        => "n = int(input())\n# Compute and print bootstrap info\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Compute the **feature importance** (normalized) from raw importance scores.

Normalize each score by dividing by the total sum so they sum to 1.0.

Read `n` feature names and raw importance scores (`name score` per line). Print each `name: <normalized>` rounded to 4 decimal places, sorted by normalized score descending.

Example:
```
Input:
3
age 30
income 50
height 20
Output:
income: 0.5
age: 0.3
height: 0.2
```
MD,
                'starter_code'        => "n = int(input())\nfeatures = []\nfor _ in range(n):\n    parts = input().split()\n    features.append((parts[0], float(parts[1])))\n# Normalize and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 14.7: Support Vector Machines (Q30–Q33)
            // ═══════════════════════════════════════════════════════════════

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Compute the **dot product** of two vectors (used in SVM kernel functions).

Read `n` (vector length), then `n` values for vector A (one per line), then `n` values for vector B. Print the dot product rounded to 4 decimal places.

Example:
```
Input:
3
1
2
3
4
5
6
Output: 32.0
```
MD,
                'starter_code'        => "n = int(input())\na = [float(input()) for _ in range(n)]\nb = [float(input()) for _ in range(n)]\n# Compute and print dot product\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Compute the **Euclidean distance** between two points (the basis for SVM margin calculations).

Read `n`, then `n` values for point A, then `n` values for point B. Print the distance rounded to 4 decimal places.

Example:
```
Input:
2
0
0
3
4
Output: 5.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\na = [float(input()) for _ in range(n)]\nb = [float(input()) for _ in range(n)]\n# Compute and print Euclidean distance\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Classify a point using a **linear SVM decision boundary**: `w · x + b`.

If the result > 0 → `positive`, if < 0 → `negative`, if == 0 → `on boundary`.

Read `n` (dimensions), then `n` weight values, then `b`, then `n` feature values for the point. Print the classification.

Example:
```
Input:
2
1
1
-3
1
2
Output: positive
```
MD,
                'starter_code'        => "n = int(input())\nw = [float(input()) for _ in range(n)]\nb = float(input())\nx = [float(input()) for _ in range(n)]\n# Compute decision value and classify\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Compute the **margin** of a linear SVM.

Margin = 2 / ||w||

where ||w|| is the L2 norm of the weight vector.

Read `n`, then `n` weight values (one per line). Print the margin rounded to 4 decimal places.

Example:
```
Input:
2
3
4
Output: 0.4
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nw = [float(input()) for _ in range(n)]\n# Compute and print margin\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 14.8: K-Nearest Neighbors (Q34–Q38)
            // ═══════════════════════════════════════════════════════════════

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Compute **distances from a query point** to all training points.

Read `n` training points, each on one line as `x y label`. Then read the query point as `qx qy`. Print the Euclidean distance from the query to each training point, rounded to 4 decimal places, one per line.

Example:
```
Input:
3
0 0 A
3 4 B
1 1 A
0 0
Output:
0.0
5.0
1.4142
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\ntraining = []\nfor _ in range(n):\n    parts = input().split()\n    training.append((float(parts[0]), float(parts[1]), parts[2]))\nqx, qy = map(float, input().split())\n# Compute and print distances\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Implement **1-Nearest Neighbor** classification.

Read `n` training points (`x y label` per line), then a query `qx qy`. Find the training point with the smallest Euclidean distance to the query. Print its label.

Example:
```
Input:
3
0 0 A
3 4 B
6 8 B
5 5
Output: B
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\ntraining = []\nfor _ in range(n):\n    parts = input().split()\n    training.append((float(parts[0]), float(parts[1]), parts[2]))\nqx, qy = map(float, input().split())\n# Find nearest neighbor and print label\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Implement **KNN classification** with majority vote.

Read `n` training points (`x y label` per line), then `k`, then a query `qx qy`. Find the k nearest training points and print the majority class label. On a tie, print the alphabetically first label.

Example:
```
Input:
5
0 0 A
1 1 A
5 5 B
6 6 B
3 3 A
3
3 3
Output: A
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\ntraining = []\nfor _ in range(n):\n    parts = input().split()\n    training.append((float(parts[0]), float(parts[1]), parts[2]))\nk = int(input())\nqx, qy = map(float, input().split())\n# KNN classify and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Compute the **KNN regression** prediction (average of k nearest neighbors' values).

Read `n` training points (`x y_value` per line), then `k`, then a query `qx`. Compute Euclidean distance in 1D (absolute difference). Print the mean of the k nearest y-values rounded to 4 decimal places.

Example:
```
Input:
5
1 10
2 20
3 30
4 40
5 50
2
3
Output: 25.0
```
MD,
                'starter_code'        => "n = int(input())\ntraining = []\nfor _ in range(n):\n    parts = input().split()\n    training.append((float(parts[0]), float(parts[1])))\nk = int(input())\nqx = float(input())\n# KNN regression and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Choose the best **k** for KNN by finding the k with the lowest error from a list.

Read `m` lines of `k error` pairs (space-separated). Print the k value with the lowest error. If tied, print the smallest k.

Example:
```
Input:
4
1 0.30
3 0.15
5 0.20
7 0.18
Output: 3
```
MD,
                'starter_code'        => "m = int(input())\nresults = []\nfor _ in range(m):\n    parts = input().split()\n    results.append((int(parts[0]), float(parts[1])))\n# Find and print best k\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 14.9: Hyperparameter Tuning: Grid Search & Pipelines (Q39–Q43)
            // ═══════════════════════════════════════════════════════════════

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Count the total number of **hyperparameter combinations** in a grid search.

Read `p` parameter names and their candidate counts (one `name count` per line). Print the total number of combinations (product of all counts).

Example:
```
Input:
3
learning_rate 3
max_depth 4
n_estimators 5
Output: 60
```
MD,
                'starter_code'        => "p = int(input())\nparams = []\nfor _ in range(p):\n    parts = input().split()\n    params.append((parts[0], int(parts[1])))\n# Compute and print total combinations\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Find the **best hyperparameter configuration** from grid search results.

Read `n` results, each line: `config_name score` (space-separated). Print the config_name with the highest score. If tied, print the one that appears first.

Example:
```
Input:
4
config_A 0.82
config_B 0.91
config_C 0.88
config_D 0.91
Output: config_B
```
MD,
                'starter_code'        => "n = int(input())\nresults = []\nfor _ in range(n):\n    parts = input().split()\n    results.append((parts[0], float(parts[1])))\n# Find and print best config\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Apply **min-max normalization** as a pipeline preprocessing step.

Normalize each feature value to [0, 1]: normalized = (x − min) / (max − min)

Read `n` values (one per line). Print each normalized value rounded to 4 decimal places, one per line.

Example:
```
Input:
4
10
20
30
40
Output:
0.0
0.3333
0.6667
1.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Normalize and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Apply **z-score standardization** as a pipeline step.

z = (x − mean) / std_dev

Use population standard deviation. Read `n` values. Print each z-score rounded to 4 decimal places, one per line.

Example:
```
Input:
4
2
4
4
4
Output:
-1.5
0.5
0.5
0.5
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Standardize and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Simulate a **pipeline** with two steps: standardize then threshold-classify.

Read `n` values (one per line). Standardize them (z-score, population std). Then classify: if z-score >= 0 → `positive`, else → `negative`. Print one label per line.

Example:
```
Input:
4
10
20
30
40
Output:
negative
negative
positive
positive
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Standardize then classify\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 14.10: Gradient Boosting & Bias-Variance Tradeoff (Q44–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Classify a model's behavior as **high bias**, **high variance**, or **balanced**.

Rules:
- If train_acc < 0.75 and test_acc < 0.75 → `high bias`
- If train_acc >= 0.90 and test_acc < 0.75 → `high variance`
- Otherwise → `balanced`

Read `train_acc` and `test_acc` (floats). Print the classification.

Example:
```
Input:
0.95
0.60
Output: high variance
```
MD,
                'starter_code'        => "train_acc = float(input())\ntest_acc = float(input())\n# Classify and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Compute the **bias-variance decomposition** components.

Given:
- Expected prediction = mean of multiple model predictions
- True value = known correct value

Bias² = (expected_prediction − true_value)²
Variance = population variance of predictions

Read `true_value`, then `n` model predictions (one per line). Print `Bias^2: <b>` and `Variance: <v>`, each rounded to 4 decimal places.

Example:
```
Input:
5
3
4
5
6
7
Output:
Bias^2: 0.0
Variance: 2.0
```
MD,
                'starter_code'        => "import math\n\ntrue_value = float(input())\nn = int(input())\npredictions = [float(input()) for _ in range(n)]\n# Compute bias^2 and variance\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Simulate one step of **gradient boosting** by computing residuals.

Residual = actual − predicted

Read `n`, then `n` lines of `actual predicted` pairs. Print each residual rounded to 4 decimal places, one per line.

Example:
```
Input:
3
10 8
20 22
30 30
Output:
2.0
-2.0
0.0
```
MD,
                'starter_code'        => "n = int(input())\npairs = [list(map(float, input().split())) for _ in range(n)]\n# Compute and print residuals\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Apply a **learning rate** to gradient boosting updates.

New prediction = old prediction + learning_rate × residual

Read `learning_rate`, then `n` lines of `old_pred residual` pairs. Print each new prediction rounded to 4 decimal places, one per line.

Example:
```
Input:
0.1
3
8 2.0
22 -2.0
30 0.0
Output:
8.2
21.8
30.0
```
MD,
                'starter_code'        => "lr = float(input())\nn = int(input())\npairs = [list(map(float, input().split())) for _ in range(n)]\n# Compute and print updated predictions\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Compute the **total error reduction** after boosting.

Read `n` initial residuals and `n` new residuals (after one boosting step). Each on its own line — first all initial, then all new. Print the total absolute error before and after, then the reduction.

Format:
```
Before: <sum_abs_initial>
After: <sum_abs_new>
Reduction: <reduction>
```
All values rounded to 4 decimal places.

Example:
```
Input:
3
2.0
-2.0
1.0
0.5
-0.3
0.2
Output:
Before: 5.0
After: 1.0
Reduction: 4.0
```
MD,
                'starter_code'        => "n = int(input())\ninitial = [float(input()) for _ in range(n)]\nnew = [float(input()) for _ in range(n)]\n# Compute and print error reduction\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Identify the best **regularization strategy** based on train/test gap.

Rules:
- gap = train_acc − test_acc
- If gap > 0.15 → `increase regularization`
- If gap < 0.05 → `decrease regularization`
- Otherwise → `regularization is appropriate`

Read `train_acc` and `test_acc`. Print the recommendation.

Example:
```
Input:
0.95
0.70
Output: increase regularization
```
MD,
                'starter_code'        => "train_acc = float(input())\ntest_acc = float(input())\n# Determine and print recommendation\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Build a **supervised learning model summary** report.

Read the following inputs (one per line):
1. Model name (string)
2. Number of training samples (int)
3. Number of features (int)
4. Train accuracy (float)
5. Test accuracy (float)
6. Number of boosting rounds (int)

Print:
```
Model: <name>
Training samples: <n>
Features: <f>
Train accuracy: <train_acc>
Test accuracy: <test_acc>
Boosting rounds: <rounds>
Overfit: <Yes/No>
```

Overfit = Yes if (train_acc − test_acc) > 0.10, else No.

Example:
```
Input:
GradientBoost
1000
10
0.96
0.82
100
Output:
Model: GradientBoost
Training samples: 1000
Features: 10
Train accuracy: 0.96
Test accuracy: 0.82
Boosting rounds: 100
Overfit: Yes
```
MD,
                'starter_code'        => "model = input()\nn_samples = int(input())\nn_features = int(input())\ntrain_acc = float(input())\ntest_acc = float(input())\nrounds = int(input())\n# Print summary report\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
        ];

        // ─────────────────────────────────────────────────────────────────
        // Insert questions
        // ─────────────────────────────────────────────────────────────────

        $questions = [];

        foreach ($questionDefs as $def) {
            $q = DB::table('coding_questions')->where([
                'challenge_id' => $challenge->id,
                'order_index'  => $def['order_index'],
            ])->first();

            if (! $q) {
                $id = DB::table('coding_questions')->insertGetId([
                    'challenge_id'        => $challenge->id,
                    'order_index'         => $def['order_index'],
                    'problem_description' => $def['problem_description'],
                    'starter_code'        => $def['starter_code'],
                    'time_limit_seconds'  => $def['time_limit_seconds'],
                    'base_xp'             => $def['base_xp'],
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
                $questions[$def['order_index']] = $id;
            } else {
                $questions[$def['order_index']] = $q->id;
            }
        }

        // ─────────────────────────────────────────────────────────────────
        // 3. TEST CASES
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $seed = function (int $qIndex, array $cases) use ($questions) {
            $qId = $questions[$qIndex] ?? null;
            if (! $qId) return;

            foreach ($cases as $case) {
                $exists = DB::table('test_cases')->where([
                    'coding_question_id' => $qId,
                    'input'              => $case['input'],
                    'expected_output'    => $case['expected_output'],
                ])->exists();

                if (! $exists) {
                    DB::table('test_cases')->insert([
                        'coding_question_id' => $qId,
                        'input'              => $case['input'],
                        'expected_output'    => $case['expected_output'],
                        'is_hidden'          => $case['is_hidden'],
                        'order_index'        => $case['order_index'],
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
            }
        };

        // ── Q1: regression vs classification ─────────────────────────────
        $seed(1, [
            ['input' => "3\n1.5\n2.3\n3.7",        'expected_output' => "regression",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\ncat\ndog\ncat",         'expected_output' => "classification",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n20\n30\n40",        'expected_output' => "regression",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nyes\nno\nyes",          'expected_output' => "classification",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: labeled vs unlabeled ──────────────────────────────────────
        $seed(2, [
            ['input' => "3\n5 1\n8 0\n3 1",        'expected_output' => "labeled",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n8\n3",              'expected_output' => "unlabeled",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 0\n2 1\n3 0\n4 1",  'expected_output' => "labeled",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10\n20",               'expected_output' => "unlabeled",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: samples and features ──────────────────────────────────────
        $seed(3, [
            ['input' => "3\n1 2 3\n4 5 6\n7 8 9",          'expected_output' => "Samples: 3\nFeatures: 3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10 20\n30 40",                  'expected_output' => "Samples: 2\nFeatures: 2",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2\n3 4\n5 6\n7 8",           'expected_output' => "Samples: 4\nFeatures: 2",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n9 8 7 6",                       'expected_output' => "Samples: 1\nFeatures: 4",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: unique classes ────────────────────────────────────────────
        $seed(4, [
            ['input' => "5\ncat\ndog\ncat\nfish\ndog",      'expected_output' => "3\ncat\ndog\nfish",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nyes\nno\nyes",                  'expected_output' => "2\nno\nyes",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nA\nB\nC\nA",                   'expected_output' => "3\nA\nB\nC",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nspam\nham\nspam",               'expected_output' => "2\nham\nspam",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: train/test split count ────────────────────────────────────
        $seed(5, [
            ['input' => "100\n0.2",    'expected_output' => "Train: 80\nTest: 20",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "50\n0.3",     'expected_output' => "Train: 35\nTest: 15",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "200\n0.25",   'expected_output' => "Train: 150\nTest: 50",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1000\n0.1",   'expected_output' => "Train: 900\nTest: 100",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: split values ──────────────────────────────────────────────
        $seed(6, [
            ['input' => "5\n10\n20\n30\n40\n50\n0.4",  'expected_output' => "Train: 10 20 30\nTest: 40 50",           'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n0.5",          'expected_output' => "Train: 1 2\nTest: 3 4",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n5\n10\n15\n20\n25\n30\n0.33", 'expected_output' => "Train: 5 10 15 20\nTest: 25 30",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n7\n8\n9\n0.33",            'expected_output' => "Train: 7 8\nTest: 9",                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: k-fold sizes ──────────────────────────────────────────────
        $seed(7, [
            ['input' => "10\n3",   'expected_output' => "4\n3\n3",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n2",   'expected_output' => "5\n5",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "11\n4",   'expected_output' => "3\n3\n3\n2",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "12\n5",   'expected_output' => "3\n3\n2\n2\n2",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: fold train/val sizes ──────────────────────────────────────
        $seed(8, [
            ['input' => "100\n5\n2",   'expected_output' => "Train: 80\nVal: 20",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "60\n3\n0",    'expected_output' => "Train: 40\nVal: 20",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "200\n4\n1",   'expected_output' => "Train: 150\nVal: 50",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "50\n5\n4",    'expected_output' => "Train: 40\nVal: 10",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: average cross-val accuracy ───────────────────────────────
        $seed(9, [
            ['input' => "4\n0.80\n0.85\n0.90\n0.75",   'expected_output' => "0.825",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.70\n0.80\n0.90",         'expected_output' => "0.8",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0.60\n0.65\n0.70\n0.75\n0.80", 'expected_output' => "0.7", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0.91\n0.89",               'expected_output' => "0.9",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: slope and intercept ──────────────────────────────────────
        $seed(10, [
            ['input' => "3\n1 2\n2 4\n3 6",        'expected_output' => "Slope: 2.0\nIntercept: 0.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 1\n1 2",             'expected_output' => "Slope: 1.0\nIntercept: 1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 3\n2 5\n3 7\n4 9",   'expected_output' => "Slope: 2.0\nIntercept: 1.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 4\n2 3\n3 2",        'expected_output' => "Slope: -1.0\nIntercept: 5.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: predictions from model ───────────────────────────────────
        $seed(11, [
            ['input' => "2.0\n1.0\n3\n0\n5\n10",   'expected_output' => "1.0\n11.0\n21.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n5.0\n1\n100",        'expected_output' => "5.0",                'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0\n0.0\n2\n2\n4",       'expected_output' => "6.0\n12.0",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.5\n2.0\n2\n4\n6",       'expected_output' => "8.0\n11.0",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: MSE ──────────────────────────────────────────────────────
        $seed(12, [
            ['input' => "3\n2 2.5\n4 3.5\n6 6.0",       'expected_output' => "0.25",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10 10\n20 20",               'expected_output' => "0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2\n2 3\n3 4\n4 5",        'expected_output' => "1.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n5 3\n7 8\n9 9",             'expected_output' => "2.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: R-squared ────────────────────────────────────────────────
        $seed(13, [
            ['input' => "3\n1 1\n2 2\n3 3",              'expected_output' => "1.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2\n2 2\n3 2",              'expected_output' => "0.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1.1\n2 1.9\n3 3.1\n4 3.9",'expected_output' => "0.9933",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2 2.5\n4 3.5\n6 6.0",       'expected_output' => "0.9375",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: MAE ──────────────────────────────────────────────────────
        $seed(14, [
            ['input' => "4\n3 2.5\n5 4.0\n7 8.0\n9 9.0",   'expected_output' => "0.875",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10 10\n20 20",                  'expected_output' => "0.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 3\n2 4\n3 5",                'expected_output' => "2.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n5 4\n6 7\n7 7\n8 9",           'expected_output' => "0.75",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: sigmoid ──────────────────────────────────────────────────
        $seed(15, [
            ['input' => "0",      'expected_output' => "0.5",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "1",      'expected_output' => "0.7311",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1",     'expected_output' => "0.2689",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2",      'expected_output' => "0.8808",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: threshold classification ────────────────────────────────
        $seed(16, [
            ['input' => "4\n0.8\n0.3\n0.5\n0.6\n0.5",   'expected_output' => "1\n0\n1\n1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.9\n0.4\n0.6\n0.7",        'expected_output' => "1\n0\n0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0.1\n0.2\n0.3\n0.4\n0.5",  'expected_output' => "0\n0\n0\n0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0.5\n0.9\n0.5",             'expected_output' => "1\n1",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: accuracy ─────────────────────────────────────────────────
        $seed(17, [
            ['input' => "4\n1 1\n0 0\n1 0\n0 1",    'expected_output' => "0.5",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 1\n1 1\n0 0\n0 0",    'expected_output' => "1.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0\n0 1\n1 0",         'expected_output' => "0.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 1\n0 0\n1 1\n0 1\n1 1",'expected_output' => "0.8",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: confusion matrix ─────────────────────────────────────────
        $seed(18, [
            ['input' => "4\n1 1\n0 1\n0 0\n1 0",    'expected_output' => "TP: 1\nFP: 1\nTN: 1\nFN: 1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 1\n1 1\n0 0\n0 0",    'expected_output' => "TP: 2\nFP: 0\nTN: 2\nFN: 0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0\n0 1\n1 1",         'expected_output' => "TP: 1\nFP: 1\nTN: 0\nFN: 1",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 1\n0 0\n1 1\n0 1\n1 0",'expected_output' => "TP: 2\nFP: 1\nTN: 1\nFN: 1", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: precision and recall ─────────────────────────────────────
        $seed(19, [
            ['input' => "8\n2\n2",     'expected_output' => "Precision: 0.8\nRecall: 0.8",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n0\n0",    'expected_output' => "Precision: 1.0\nRecall: 1.0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n5",     'expected_output' => "Precision: 0.5\nRecall: 0.5",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2",     'expected_output' => "Precision: 0.75\nRecall: 0.6",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: F1 score ─────────────────────────────────────────────────
        $seed(20, [
            ['input' => "0.8\n0.6",     'expected_output' => "0.6857",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n1.0",     'expected_output' => "1.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n0.5",     'expected_output' => "0.5",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.9\n0.7",     'expected_output' => "0.7875",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Gini impurity ────────────────────────────────────────────
        $seed(21, [
            ['input' => "2\n50\n50",      'expected_output' => "0.5",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n100\n0",      'expected_output' => "0.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n10\n10\n10",  'expected_output' => "0.6667",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n30\n70",      'expected_output' => "0.42",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: entropy ──────────────────────────────────────────────────
        $seed(22, [
            ['input' => "2\n50\n50",      'expected_output' => "1.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n100\n0",      'expected_output' => "0.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n10\n10\n10",  'expected_output' => "1.585",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n25\n75",      'expected_output' => "0.8113",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: information gain ─────────────────────────────────────────
        $seed(23, [
            ['input' => "1.0\n2\n50\n0.0\n50\n0.0",     'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n2\n50\n0.5\n50\n0.5",     'expected_output' => "0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n2\n40\n0.0\n60\n0.9183",  'expected_output' => "0.4490", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.8\n2\n50\n0.2\n50\n0.4",     'expected_output' => "0.5",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: majority class ───────────────────────────────────────────
        $seed(24, [
            ['input' => "3\ncat 10\ndog 25\nfish 5",     'expected_output' => "dog",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA 10\nB 10",                 'expected_output' => "A",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nspam 40\nham 30\njunk 5",    'expected_output' => "spam",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nyes 0\nno 100",              'expected_output' => "no",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: decision stump ───────────────────────────────────────────
        $seed(25, [
            ['input' => "5.0\nsmall\nlarge\n4\n3\n6\n5\n7",    'expected_output' => "small\nlarge\nsmall\nlarge",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\nyes\nno\n3\n0.3\n0.5\n0.7",      'expected_output' => "yes\nyes\nno",                 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0\nlow\nhigh\n3\n5\n10\n15",        'expected_output' => "low\nlow\nhigh",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "100.0\ncheap\npricey\n2\n99\n101",     'expected_output' => "cheap\npricey",               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: majority vote ────────────────────────────────────────────
        $seed(26, [
            ['input' => "5\ncat\ndog\ncat\nfish\ncat",   'expected_output' => "cat",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nA\nB\nA\nB",                'expected_output' => "A",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nyes\nno\nyes",              'expected_output' => "yes",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nX\nY\nZ",                   'expected_output' => "X",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: average ensemble ─────────────────────────────────────────
        $seed(27, [
            ['input' => "4\n0.8\n0.7\n0.9\n0.6",   'expected_output' => "0.75",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.5\n0.5\n0.5",        'expected_output' => "0.5",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1.0\n0.0",             'expected_output' => "0.5",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n0.6\n0.7\n0.8\n0.9\n1.0", 'expected_output' => "0.8", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: bootstrap sampling ───────────────────────────────────────
        $seed(28, [
            ['input' => "100",   'expected_output' => "Bootstrap size: 100\nExpected unique: 63",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "50",    'expected_output' => "Bootstrap size: 50\nExpected unique: 32",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "200",   'expected_output' => "Bootstrap size: 200\nExpected unique: 126",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1000",  'expected_output' => "Bootstrap size: 1000\nExpected unique: 632",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: feature importance ───────────────────────────────────────
        $seed(29, [
            ['input' => "3\nage 30\nincome 50\nheight 20",          'expected_output' => "income: 0.5\nage: 0.3\nheight: 0.2",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA 1\nB 1",                              'expected_output' => "A: 0.5\nB: 0.5",                        'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nx 10\ny 20\nz 70",                      'expected_output' => "z: 0.7\ny: 0.2\nx: 0.1",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\na 25\nb 25\nc 25\nd 25",                'expected_output' => "a: 0.25\nb: 0.25\nc: 0.25\nd: 0.25",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: dot product ──────────────────────────────────────────────
        $seed(30, [
            ['input' => "3\n1\n2\n3\n4\n5\n6",     'expected_output' => "32.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n0\n0\n1",           'expected_output' => "0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n1\n1\n2\n2\n2",     'expected_output' => "6.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n3\n4\n3\n4",           'expected_output' => "25.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Euclidean distance ───────────────────────────────────────
        $seed(31, [
            ['input' => "2\n0\n0\n3\n4",      'expected_output' => "5.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0\n5",            'expected_output' => "5.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3\n4\n5\n6",'expected_output' => "5.1962",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1\n1\n4\n5",      'expected_output' => "5.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: SVM linear classify ──────────────────────────────────────
        $seed(32, [
            ['input' => "2\n1\n1\n-3\n1\n2",     'expected_output' => "positive",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n1\n-5\n1\n2",     'expected_output' => "negative",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n1\n-3\n1\n2",     'expected_output' => "positive",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n2\n-1\n0\n1\n2",     'expected_output' => "on boundary",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: SVM margin ───────────────────────────────────────────────
        $seed(33, [
            ['input' => "2\n3\n4",    'expected_output' => "0.4",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n0",    'expected_output' => "2.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2",       'expected_output' => "1.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n1\n1", 'expected_output' => "1.1547", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: distances to training points ────────────────────────────
        $seed(34, [
            ['input' => "3\n0 0 A\n3 4 B\n1 1 A\n0 0",   'expected_output' => "0.0\n5.0\n1.4142",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0 A\n1 1 B\n1 1",          'expected_output' => "1.4142\n0.0",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0 0 A\n4 0 B\n0 3 A\n0 0",   'expected_output' => "0.0\n4.0\n3.0",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n2 2 X\n5 6 Y\n5 6",          'expected_output' => "5.0\n0.0",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: 1-NN classification ──────────────────────────────────────
        $seed(35, [
            ['input' => "3\n0 0 A\n3 4 B\n6 8 B\n5 5",   'expected_output' => "B",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0 A\n10 10 B\n0 1",        'expected_output' => "A",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 1 A\n5 5 B\n9 9 C\n5 5",  'expected_output' => "B",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0 0 X\n4 3 Y\n4 3",         'expected_output' => "Y",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: KNN classification ───────────────────────────────────────
        $seed(36, [
            ['input' => "5\n0 0 A\n1 1 A\n5 5 B\n6 6 B\n3 3 A\n3\n3 3",   'expected_output' => "A",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0 0 A\n1 0 A\n10 0 B\n11 0 B\n1\n5 0",        'expected_output' => "B",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0 0 A\n1 1 B\n2 2 A\n1\n1 1",                 'expected_output' => "B",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0 0 A\n0 1 A\n5 5 B\n5 6 B\n2\n0 0",         'expected_output' => "A",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: KNN regression ───────────────────────────────────────────
        $seed(37, [
            ['input' => "5\n1 10\n2 20\n3 30\n4 40\n5 50\n2\n3",   'expected_output' => "25.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0 0\n1 10\n2 20\n1\n1",                 'expected_output' => "10.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 5\n2 10\n3 15\n4 20\n2\n2",           'expected_output' => "7.5",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n10 100\n20 200\n30 300\n1\n20",         'expected_output' => "200.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: best k ───────────────────────────────────────────────────
        $seed(38, [
            ['input' => "4\n1 0.30\n3 0.15\n5 0.20\n7 0.18",   'expected_output' => "3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 0.10\n3 0.10\n5 0.20",           'expected_output' => "1",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n3 0.05\n5 0.08\n7 0.12",           'expected_output' => "3",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2 0.25\n4 0.18\n6 0.18\n8 0.30",   'expected_output' => "4",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: grid search combinations ────────────────────────────────
        $seed(39, [
            ['input' => "3\nlearning_rate 3\nmax_depth 4\nn_estimators 5",  'expected_output' => "60",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nalpha 3\nbeta 3",                                'expected_output' => "9",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\na 2\nb 3\nc 4\nd 5",                            'expected_output' => "120",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nC 5",                                            'expected_output' => "5",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: best config ──────────────────────────────────────────────
        $seed(40, [
            ['input' => "4\nconfig_A 0.82\nconfig_B 0.91\nconfig_C 0.88\nconfig_D 0.91",   'expected_output' => "config_B",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nrun1 0.75\nrun2 0.80\nrun3 0.70",                               'expected_output' => "run2",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nX 0.90\nY 0.90",                                                'expected_output' => "X",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nalpha 0.60\nbeta 0.99\ngamma 0.75",                             'expected_output' => "beta",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: min-max normalization ────────────────────────────────────
        $seed(41, [
            ['input' => "4\n10\n20\n30\n40",      'expected_output' => "0.0\n0.3333\n0.6667\n1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0\n100",              'expected_output' => "0.0\n1.0",                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5\n5\n5",             'expected_output' => "0.0\n0.0\n0.0",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n3\n5",             'expected_output' => "0.0\n0.5\n1.0",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: z-score standardization ─────────────────────────────────
        $seed(42, [
            ['input' => "4\n2\n4\n4\n4",      'expected_output' => "-1.5\n0.5\n0.5\n0.5",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",         'expected_output' => "-1.2247\n0.0\n1.2247",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0\n10",           'expected_output' => "-1.0\n1.0",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n20\n30\n40",  'expected_output' => "-1.3416\n-0.4472\n0.4472\n1.3416", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q43: pipeline standardize then classify ───────────────────────
        $seed(43, [
            ['input' => "4\n10\n20\n30\n40",   'expected_output' => "negative\nnegative\npositive\npositive",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5\n15",            'expected_output' => "negative\npositive",                        'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3",          'expected_output' => "negative\npositive\npositive",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n1\n5\n5",       'expected_output' => "negative\nnegative\npositive\npositive",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: bias-variance classification ────────────────────────────
        $seed(44, [
            ['input' => "0.95\n0.60",   'expected_output' => "high variance",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.60\n0.58",   'expected_output' => "high bias",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.85\n0.82",   'expected_output' => "balanced",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.72\n0.70",   'expected_output' => "balanced",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: bias^2 and variance ──────────────────────────────────────
        $seed(45, [
            ['input' => "5\n3\n4\n5\n6\n7",    'expected_output' => "Bias^2: 0.0\nVariance: 2.0",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n3\n10\n8\n9\n11", 'expected_output' => "Bias^2: 1.0\nVariance: 6.8",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n3\n0\n0\n0\n0",    'expected_output' => "Bias^2: 0.0\nVariance: 0.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n3\n4\n4\n6\n6",    'expected_output' => "Bias^2: 0.0\nVariance: 1.0",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: residuals ────────────────────────────────────────────────
        $seed(46, [
            ['input' => "3\n10 8\n20 22\n30 30",    'expected_output' => "2.0\n-2.0\n0.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5 5\n10 10",            'expected_output' => "0.0\n0.0",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2\n3 1\n5 5",        'expected_output' => "-1.0\n2.0\n0.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4 3\n8 9\n12 11\n16 17",'expected_output' => "1.0\n-1.0\n1.0\n-1.0",'is_hidden' => true,'order_index' => 4],
        ]);

        // ── Q47: apply learning rate ──────────────────────────────────────
        $seed(47, [
            ['input' => "0.1\n3\n8 2.0\n22 -2.0\n30 0.0",     'expected_output' => "8.2\n21.8\n30.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n2\n10 4.0\n20 -4.0",            'expected_output' => "12.0\n18.0",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.2\n3\n5 5.0\n10 -5.0\n15 0.0",     'expected_output' => "6.0\n9.0\n15.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n2\n0 3.0\n10 -3.0",             'expected_output' => "3.0\n7.0",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: error reduction ──────────────────────────────────────────
        $seed(48, [
            ['input' => "3\n2.0\n-2.0\n1.0\n0.5\n-0.3\n0.2",   'expected_output' => "Before: 5.0\nAfter: 1.0\nReduction: 4.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n4.0\n-4.0\n1.0\n-1.0",             'expected_output' => "Before: 8.0\nAfter: 2.0\nReduction: 6.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1.0\n1.0\n1.0\n0.5\n0.5\n0.5",     'expected_output' => "Before: 3.0\nAfter: 1.5\nReduction: 1.5",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0.0\n0.0\n0.0\n0.0",               'expected_output' => "Before: 0.0\nAfter: 0.0\nReduction: 0.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: regularization recommendation ───────────────────────────
        $seed(49, [
            ['input' => "0.95\n0.70",   'expected_output' => "increase regularization",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.80\n0.78",   'expected_output' => "decrease regularization",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.90\n0.80",   'expected_output' => "regularization is appropriate", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.60\n0.58",   'expected_output' => "decrease regularization",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: model summary report ─────────────────────────────────────
        $seed(50, [
            ['input' => "GradientBoost\n1000\n10\n0.96\n0.82\n100",   'expected_output' => "Model: GradientBoost\nTraining samples: 1000\nFeatures: 10\nTrain accuracy: 0.96\nTest accuracy: 0.82\nBoosting rounds: 100\nOverfit: Yes",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "RandomForest\n500\n5\n0.88\n0.85\n50",       'expected_output' => "Model: RandomForest\nTraining samples: 500\nFeatures: 5\nTrain accuracy: 0.88\nTest accuracy: 0.85\nBoosting rounds: 50\nOverfit: No",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "KNN\n200\n3\n0.99\n0.60\n0",                 'expected_output' => "Model: KNN\nTraining samples: 200\nFeatures: 3\nTrain accuracy: 0.99\nTest accuracy: 0.60\nBoosting rounds: 0\nOverfit: Yes",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "LinearSVM\n800\n8\n0.91\n0.90\n0",           'expected_output' => "Model: LinearSVM\nTraining samples: 800\nFeatures: 8\nTrain accuracy: 0.91\nTest accuracy: 0.90\nBoosting rounds: 0\nOverfit: No",         'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 14 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}