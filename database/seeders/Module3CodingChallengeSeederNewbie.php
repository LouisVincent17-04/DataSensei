<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 3 — Introduction to Data Science (Newbie / Level 1) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering beginner Data Science concepts
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mapped to lessons 293–303):
 *   L1.1  What is Data Science? (conceptual warm-ups using basic Python)
 *   L1.2  NumPy: Arrays, Vectorization & Linear Algebra
 *   L1.3  Pandas: DataFrames, Cleaning & GroupBy
 *   L1.4  Visualization: Matplotlib & Seaborn (data prep / descriptive tasks)
 *   L1.5  Statistics: Distributions, Hypothesis Testing & Correlation
 *   L1.6  Feature Engineering: Scaling, Encoding & Transformation
 *   L1.7  EDA: Exploratory Data Analysis in Practice
 *   L1.8  Machine Learning: Classification, Regression & Evaluation
 *   L1.9  Unsupervised Learning: K-Means & PCA
 *   L1.10 Time Series: Decomposition, Resampling & Forecasting Features
 *   L1.11 NLP: Text Preprocessing, TF-IDF & Sentiment Analysis
 *
 * Difficulty: Newbie — all problems are solved with pure Python (no third-party
 * libraries required). Learners reproduce the core computations that NumPy,
 * Pandas, scikit-learn etc. perform under the hood.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module3CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 3 — Introduction to Data Science (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Introduction to Data Science',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Explore the building blocks of Data Science using pure Python. Compute statistics, manipulate datasets, scale features, and process text — all from scratch, without any libraries. These exercises mirror what NumPy, Pandas, and scikit-learn do under the hood.',
                'time_limit_seconds' => 1200,
                'base_xp'            => 800,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: What is Data Science? (Q1–Q4)  →  Lesson 293
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Read `n` numbers (first line is `n`, then one number per line). Print the **mean** rounded to 2 decimal places.

Example:
```
Input:
4
10
20
30
40
Output: 25.00
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read `n` numbers and print the **range** (max − min).

Example:
```
Input:
5
3
7
1
9
4
Output: 8
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read `n` numbers and print `True` if **all** numbers are positive, otherwise print `False`.

Example:
```
Input:
3
1
2
-1
Output: False
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read `n` numbers and print the count of **missing** values represented by `-1`.

Example:
```
Input:
5
3
-1
7
-1
2
Output: 2
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: NumPy — Arrays & Vectorization (Q5–Q9)  →  Lesson 294
            // ═══════════════════════════════════════════════════════════════

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read a row of space-separated integers and print the **element-wise square** as a space-separated list.

Example:
```
Input:  1 2 3 4
Output: 1 4 9 16
```
MD,
                'starter_code'        => "nums = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Read two rows of space-separated integers of equal length. Print their **element-wise sum** as a space-separated list.

Example:
```
Input:
1 2 3
4 5 6
Output: 5 7 9
```
MD,
                'starter_code'        => "a = list(map(int, input().split()))\nb = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Read a row of space-separated integers and a scalar `k` (on the next line). Print every element **multiplied by k**, space-separated.

Example:
```
Input:
2 4 6
3
Output: 6 12 18
```
MD,
                'starter_code'        => "nums = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Read a row of space-separated integers. Print the **dot product** of the list with itself (sum of squares).

Example:
```
Input:  1 2 3
Output: 14
```
MD,
                'starter_code'        => "nums = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read two rows of space-separated integers of equal length. Print their **dot product** (sum of element-wise products).

Example:
```
Input:
1 2 3
4 5 6
Output: 32
```
MD,
                'starter_code'        => "a = list(map(int, input().split()))\nb = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Pandas — DataFrames & Cleaning (Q10–Q14)  → Lesson 295
            // ═══════════════════════════════════════════════════════════════

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,score`). Print the **name** with the highest score.

Example:
```
Input:
3
Alice,85
Bob,92
Carol,78
Output: Bob
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,score`). Some scores are the string `NA`. Print the **count of missing scores**.

Example:
```
Input:
4
Alice,85
Bob,NA
Carol,NA
Dave,90
Output: 2
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,score`). Replace any `NA` score with `0` and print the **average** score rounded to 2 decimal places.

Example:
```
Input:
3
Alice,80
Bob,NA
Carol,100
Output: 60.00
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,group,score`). Print the **average score per group**, one per line, sorted alphabetically by group name, rounded to 2 decimal places. Format: `group: avg`

Example:
```
Input:
4
Alice,A,80
Bob,B,90
Carol,A,70
Dave,B,100
Output:
A: 75.00
B: 95.00
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,score`). Print the names sorted by **score descending**, one per line.

Example:
```
Input:
3
Alice,85
Bob,92
Carol,78
Output:
Bob
Alice
Carol
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Visualization — Descriptive Summaries (Q15–Q18) → L296
            // ═══════════════════════════════════════════════════════════════

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Read `n` numbers and print the **median**.
- If `n` is odd, print the middle value.
- If `n` is even, print the average of the two middle values rounded to 2 decimal places.

Example:
```
Input:
5
3
1
4
1
5
Output: 3
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Read `n` numbers and print the **mode** (most frequent value). If there is a tie, print the smallest.

Example:
```
Input:
6
1
2
2
3
3
2
Output: 2
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Read `n` numbers and print the **frequency** of each unique value sorted in ascending order. Format: `value: count`

Example:
```
Input:
5
1
2
1
3
2
Output:
1: 2
2: 2
3: 1
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Read `n` numbers and print the **interquartile range** (IQR = Q3 − Q1) rounded to 2 decimal places.

Use the method where Q1 = median of the lower half, Q3 = median of the upper half (exclude the median for odd-length lists).

Example:
```
Input:
6
1
2
3
4
5
6
Output: 3.00
```
MD,
                'starter_code'        => "n = int(input())\nnums = sorted([float(input()) for _ in range(n)])\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Statistics (Q19–Q23)  →  Lesson 297
            // ═══════════════════════════════════════════════════════════════

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Read `n` numbers and print the **variance** (population variance) rounded to 2 decimal places.

Variance = mean of (each value − mean)²

Example:
```
Input:
4
2
4
4
4
Output: 1.00
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Read `n` numbers and print the **standard deviation** (population) rounded to 2 decimal places.

Standard deviation = square root of variance.

Example:
```
Input:
4
2
4
4
4
Output: 1.00
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Read `n` pairs of numbers (format: `x y` per line). Print the **Pearson correlation coefficient** rounded to 2 decimal places.

Formula: r = Σ((xᵢ−x̄)(yᵢ−ȳ)) / (n × σx × σy)

Example:
```
Input:
3
1 2
2 4
3 6
Output: 1.00
```
MD,
                'starter_code'        => "import math\nn = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Read `n` numbers. Print `True` if the distribution is **right-skewed** (mean > median), otherwise print `False`.

Example:
```
Input:
5
1
2
3
4
100
Output: True
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Read `n` numbers and a **z-score threshold** `t` (on the next line). Print the values that are **outliers** (|z-score| > t), one per line, in the order they appear. If none, print `None`.

z-score = (value − mean) / std_dev

Example:
```
Input:
5
1
2
3
4
100
2
Output: 100.0
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnums = [float(input()) for _ in range(n)]\nt = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Feature Engineering (Q24–Q28)  →  Lesson 298
            // ═══════════════════════════════════════════════════════════════

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Read `n` numbers and apply **Min-Max normalization**. Print each scaled value rounded to 2 decimal places, one per line.

Formula: scaled = (x − min) / (max − min)

Example:
```
Input:
4
0
5
10
20
Output:
0.00
0.25
0.50
1.00
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Read `n` numbers and apply **Z-score standardization**. Print each standardized value rounded to 2 decimal places, one per line.

Formula: z = (x − mean) / std_dev

Example:
```
Input:
3
2
4
6
Output:
-1.00
0.00
1.00
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Read `n` category labels (strings). Print the **one-hot encoding** for each label as a binary list. First sort the unique categories alphabetically to determine the column order.

Example:
```
Input:
3
cat
dog
cat
Output:
[1, 0]
[0, 1]
[1, 0]
```
MD,
                'starter_code'        => "n = int(input())\nlabels = [input().strip() for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Read `n` numbers and apply a **log transformation** (natural log). Print each result rounded to 2 decimal places, one per line. All values are guaranteed to be positive.

Example:
```
Input:
3
1
10
100
Output:
0.00
2.30
4.61
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Read `n` numbers and **bin** them into 3 equal-width bins: `low`, `medium`, `high`. Print the bin label for each number, one per line.

Bins are based on the range of all values:
- low:    [min, min + width)
- medium: [min + width, min + 2*width)
- high:   [min + 2*width, max]

Example:
```
Input:
5
0
3
6
9
12
Output:
low
low
medium
high
high
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: EDA (Q29–Q32)  →  Lesson 299
            // ═══════════════════════════════════════════════════════════════

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,age,score`). Print the **summary statistics** for the `score` column:
```
min: <value>
max: <value>
mean: <value>
```
All values rounded to 2 decimal places.

Example:
```
Input:
3
Alice,25,80
Bob,30,90
Carol,22,70
Output:
min: 70.00
max: 90.00
mean: 80.00
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Read `n` numbers and identify **outliers** using the IQR method. A value is an outlier if it is below Q1 − 1.5×IQR or above Q3 + 1.5×IQR. Print outliers one per line in original order. If none, print `None`.

Example:
```
Input:
7
1
2
3
4
5
6
100
Output: 100.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `col1,col2`). Print the **Pearson correlation** between the two columns rounded to 2 decimal places.

Example:
```
Input:
3
1,2
2,4
3,6
Output: 1.00
```
MD,
                'starter_code'        => "import math\nn = int(input())\npairs = [tuple(map(float, input().split(','))) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Read `n` numbers and print the **percentage** of values above the mean, rounded to 2 decimal places.

Example:
```
Input:
5
1
2
3
4
5
Output: 40.00
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Machine Learning Basics (Q33–Q37)  →  Lesson 300
            // ═══════════════════════════════════════════════════════════════

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Read `n` pairs `(actual, predicted)` (format: `actual predicted` per line, both integers 0 or 1). Print the **accuracy** as a percentage rounded to 2 decimal places.

Example:
```
Input:
4
1 1
0 0
1 0
0 1
Output: 50.00
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Read `n` pairs `(actual, predicted)` (format: `actual predicted`). Print:
```
TP: <count>
FP: <count>
TN: <count>
FN: <count>
```

Where 1 = positive, 0 = negative.

Example:
```
Input:
4
1 1
0 0
1 0
0 1
Output:
TP: 1
FP: 1
TN: 1
FN: 1
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Read `n` pairs `(actual, predicted)` of real numbers (regression task). Print the **Mean Absolute Error** (MAE) rounded to 2 decimal places.

MAE = mean of |actual − predicted|

Example:
```
Input:
3
3.0 2.5
-0.5 0.0
2.0 2.0
Output: 0.33
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Read `n` pairs `(actual, predicted)` of real numbers. Print the **Mean Squared Error** (MSE) rounded to 2 decimal places.

MSE = mean of (actual − predicted)²

Example:
```
Input:
3
3.0 2.5
-0.5 0.0
2.0 2.0
Output: 0.08
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Read `n` data points and a **k** value (on the last line). For each data point read its class label and feature value (format: `label value`). Given a query point on the second-to-last line, classify it using **k-Nearest Neighbours** (1D, Euclidean distance). Print the predicted label. Break ties by choosing the alphabetically smaller label.

Example:
```
Input:
5
A 1
A 2
B 8
B 9
B 10
5
3
Output: A
```
MD,
                'starter_code'        => "n = int(input())\npoints = []\nfor _ in range(n):\n    parts = input().split()\n    points.append((parts[0], float(parts[1])))\nquery = float(input())\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Unsupervised Learning (Q38–Q41)  →  Lesson 301
            // ═══════════════════════════════════════════════════════════════

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Read `n` 1D points and `k` cluster centers (on the next line, space-separated). Assign each point to the **nearest center** (by absolute distance). Print the center each point is assigned to, one per line.

Example:
```
Input:
4
1
2
8
9
2
1 10
Output:
1
1
10
10
```
MD,
                'starter_code'        => "n = int(input())\npoints = [float(input()) for _ in range(n)]\ncenters = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Read a list of cluster assignments (integers, one per line after `n`). Print the **count of points in each cluster**, sorted by cluster ID ascending. Format: `cluster_id: count`

Example:
```
Input:
6
0
1
0
1
0
2
Output:
0: 3
1: 2
2: 1
```
MD,
                'starter_code'        => "n = int(input())\nassignments = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Read `n` 2D points (format: `x y` per line). Print the **centroid** (mean of x, mean of y), both rounded to 2 decimal places on the same line separated by a space.

Example:
```
Input:
3
1 2
3 4
5 6
Output: 3.00 4.00
```
MD,
                'starter_code'        => "n = int(input())\npoints = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Read `n` 1D points and their cluster assignment (format: `point cluster` per line). Print the **within-cluster sum of squared distances** (WCSS) rounded to 2 decimal places.

WCSS = for each cluster: sum of (point − cluster_mean)²

Example:
```
Input:
4
1 0
2 0
8 1
9 1
Output: 1.00
```
MD,
                'starter_code'        => "n = int(input())\ndata = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Time Series (Q42–Q45)  →  Lesson 302
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Read `n` numbers representing a time series. Print the **simple moving average** with window size `w` (given on the next line). Print each average rounded to 2 decimal places, one per line. (Output starts from index w-1.)

Example:
```
Input:
6
1
2
3
4
5
6
3
Output:
2.00
3.00
4.00
5.00
```
MD,
                'starter_code'        => "n = int(input())\nseries = [float(input()) for _ in range(n)]\nw = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Read `n` numbers representing a time series. Print the **first-order differences** (each value minus the previous), one per line. (Output has n−1 values.)

Example:
```
Input:
5
10
13
11
15
14
Output:
3
-2
4
-1
```
MD,
                'starter_code'        => "n = int(input())\nseries = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Read `n` numbers representing a time series and a **lag** value `k`. Print the **lagged correlation** (Pearson) between the series and its lagged version (series[k:] vs series[:n-k]), rounded to 2 decimal places.

Example:
```
Input:
5
1
2
3
4
5
1
Output: 1.00
```
MD,
                'starter_code'        => "import math\nn = int(input())\nseries = [float(input()) for _ in range(n)]\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Read `n` numbers representing a time series. Print the **cumulative sum** at each step, one per line.

Example:
```
Input:
4
1
2
3
4
Output:
1
3
6
10
```
MD,
                'starter_code'        => "n = int(input())\nseries = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11: NLP Basics (Q46–Q50)  →  Lesson 303
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Read a sentence. **Tokenize** it (split by spaces) and print each token in lowercase, one per line.

Example:
```
Input:  Hello World this is Python
Output:
hello
world
this
is
python
```
MD,
                'starter_code'        => "sentence = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Read a sentence and a list of **stop words** (second line, space-separated). Print the remaining tokens (lowercase, original order) after removing stop words, space-separated.

Example:
```
Input:
the cat sat on the mat
the on
Output: cat sat mat
```
MD,
                'starter_code'        => "sentence = input().lower().split()\nstop_words = set(input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Read `n` documents (one per line). Compute the **Term Frequency** (TF) for a query word (given on the last line) in each document.

TF = (count of word in document) / (total words in document)

Print TF for each document rounded to 2 decimal places, one per line.

Example:
```
Input:
2
the cat sat on the mat
the dog ran
the
Output:
0.33
0.33
```
MD,
                'starter_code'        => "n = int(input())\ndocs = [input().lower().split() for _ in range(n)]\nword = input().strip().lower()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Read a sentence. Count the number of **positive** and **negative** words using a simple lexicon.

Positive words: `good great excellent happy love best wonderful`
Negative words: `bad terrible awful sad hate worst horrible`

Print:
```
positive: <count>
negative: <count>
```

Example:
```
Input:  the movie was great but the acting was terrible
Output:
positive: 1
negative: 1
```
MD,
                'starter_code'        => "sentence = input().lower().split()\npositive = {'good','great','excellent','happy','love','best','wonderful'}\nnegative = {'bad','terrible','awful','sad','hate','worst','horrible'}\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Read a sentence. Print a simple **sentiment label** based on the balance of positive and negative words.

Positive words: `good great excellent happy love best wonderful`
Negative words: `bad terrible awful sad hate worst horrible`

- If positive count > negative count: print `Positive`
- If negative count > positive count: print `Negative`
- If equal (including both 0): print `Neutral`

Example:
```
Input:  I love this great product but it is terrible
Output: Positive
```
MD,
                'starter_code'        => "sentence = input().lower().split()\npositive = {'good','great','excellent','happy','love','best','wonderful'}\nnegative = {'bad','terrible','awful','sad','hate','worst','horrible'}\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

        ]; // end $questionDefs

        // ─────────────────────────────────────────────────────────────────
        // 3. PERSIST QUESTIONS
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
        // 4. TEST CASES
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $seed = function (int $qIdx, array $cases) use ($questions): void {
            $qId = $questions[$qIdx] ?? null;
            if (! $qId) return;

            foreach ($cases as $case) {
                $exists = DB::table('test_cases')->where([
                    'coding_question_id' => $qId,
                    'order_index'        => $case['order_index'],
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

        // ── Q1: Mean ──────────────────────────────────────────────────────
        $seed(1, [
            ['input' => "4\n10\n20\n30\n40",       'expected_output' => '25.00',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",              'expected_output' => '2.00',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n5\n5\n5\n5",        'expected_output' => '5.00',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0\n100",               'expected_output' => '50.00',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Range ─────────────────────────────────────────────────────
        $seed(2, [
            ['input' => "5\n3\n7\n1\n9\n4",        'expected_output' => '8.0',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10\n20\n30",            'expected_output' => '20.0',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n5\n5\n5\n5",            'expected_output' => '0.0',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n-10\n10",               'expected_output' => '20.0',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: All positive ──────────────────────────────────────────────
        $seed(3, [
            ['input' => "3\n1\n2\n-1",             'expected_output' => 'False',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",              'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0",                    'expected_output' => 'False',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n5\n10\n15\n20",        'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Missing count ─────────────────────────────────────────────
        $seed(4, [
            ['input' => "5\n3\n-1\n7\n-1\n2",      'expected_output' => '2',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",              'expected_output' => '0',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n-1\n-1\n-1\n-1",       'expected_output' => '4',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n5\n-1\n10",            'expected_output' => '1',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Element-wise square ───────────────────────────────────────
        $seed(5, [
            ['input' => '1 2 3 4',             'expected_output' => '1 4 9 16',    'is_hidden' => false, 'order_index' => 1],
            ['input' => '0 5 10',              'expected_output' => '0 25 100',    'is_hidden' => false, 'order_index' => 2],
            ['input' => '3',                   'expected_output' => '9',           'is_hidden' => true,  'order_index' => 3],
            ['input' => '2 4 6 8',             'expected_output' => '4 16 36 64', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Element-wise sum ──────────────────────────────────────────
        $seed(6, [
            ['input' => "1 2 3\n4 5 6",        'expected_output' => '5 7 9',       'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 0\n1 1",            'expected_output' => '1 1',         'is_hidden' => false, 'order_index' => 2],
            ['input' => "10 20 30\n1 2 3",     'expected_output' => '11 22 33',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n5",                'expected_output' => '10',          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Scalar multiply ───────────────────────────────────────────
        $seed(7, [
            ['input' => "2 4 6\n3",            'expected_output' => '6 12 18',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3\n0",            'expected_output' => '0 0 0',     'is_hidden' => false, 'order_index' => 2],
            ['input' => "5 10\n2",             'expected_output' => '10 20',     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n10",               'expected_output' => '30',        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Dot product with self ─────────────────────────────────────
        $seed(8, [
            ['input' => '1 2 3',               'expected_output' => '14',    'is_hidden' => false, 'order_index' => 1],
            ['input' => '0 1',                 'expected_output' => '1',     'is_hidden' => false, 'order_index' => 2],
            ['input' => '3 4',                 'expected_output' => '25',    'is_hidden' => true,  'order_index' => 3],
            ['input' => '1 1 1 1',             'expected_output' => '4',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Dot product ───────────────────────────────────────────────
        $seed(9, [
            ['input' => "1 2 3\n4 5 6",        'expected_output' => '32',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0\n0 1",            'expected_output' => '0',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3\n4 5",            'expected_output' => '23',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 2 3 4\n4 3 2 1",   'expected_output' => '20',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Max score name ───────────────────────────────────────────
        $seed(10, [
            ['input' => "3\nAlice,85\nBob,92\nCarol,78",           'expected_output' => 'Bob',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nDave,70\nEve,95",                      'expected_output' => 'Eve',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nX,50\nY,50\nZ,50",                     'expected_output' => 'X',      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nA,10\nB,20\nC,30\nD,40",               'expected_output' => 'D',      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Count NA ─────────────────────────────────────────────────
        $seed(11, [
            ['input' => "4\nAlice,85\nBob,NA\nCarol,NA\nDave,90",  'expected_output' => '2',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nAlice,80\nBob,90",                     'expected_output' => '0',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,NA\nB,NA\nC,NA",                     'expected_output' => '3',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nZ,NA",                                  'expected_output' => '1',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Mean after replacing NA with 0 ──────────────────────────
        $seed(12, [
            ['input' => "3\nAlice,80\nBob,NA\nCarol,100",          'expected_output' => '60.00',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA,10\nB,20",                           'expected_output' => '15.00',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nX,NA\nY,NA\nZ,NA",                     'expected_output' => '0.00',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nP,50\nQ,NA\nR,100\nS,NA",              'expected_output' => '37.50',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: GroupBy mean ─────────────────────────────────────────────
        $seed(13, [
            ['input' => "4\nAlice,A,80\nBob,B,90\nCarol,A,70\nDave,B,100",     'expected_output' => "A: 75.00\nB: 95.00",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nX,G1,50\nY,G1,100",                                'expected_output' => "G1: 75.00",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,X,10\nB,Y,20\nC,X,30",                           'expected_output' => "X: 20.00\nY: 20.00",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nZ,G3,99",                                           'expected_output' => "G3: 99.00",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Sort by score desc ───────────────────────────────────────
        $seed(14, [
            ['input' => "3\nAlice,85\nBob,92\nCarol,78",           'expected_output' => "Bob\nAlice\nCarol",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA,10\nB,20",                           'expected_output' => "B\nA",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nX,50\nY,50\nZ,100",                    'expected_output' => "Z\nX\nY",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nSolo,42",                              'expected_output' => "Solo",                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Median ───────────────────────────────────────────────────
        $seed(15, [
            ['input' => "5\n3\n1\n4\n1\n5",        'expected_output' => '3.0',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4",           'expected_output' => '2.50',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n7",                    'expected_output' => '7.0',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n5\n1\n9\n3\n7\n2",    'expected_output' => '4.00',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Mode ─────────────────────────────────────────────────────
        $seed(16, [
            ['input' => "6\n1\n2\n2\n3\n3\n2",    'expected_output' => '2.0',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5",             'expected_output' => '5.0',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4",          'expected_output' => '1.0',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n3\n3\n2\n2\n3",       'expected_output' => '3.0',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Frequency table ──────────────────────────────────────────
        $seed(17, [
            ['input' => "5\n1\n2\n1\n3\n2",        'expected_output' => "1: 2\n2: 2\n3: 1",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5",             'expected_output' => "5: 3",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4",          'expected_output' => "1: 1\n2: 1\n3: 1\n4: 1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2\n1\n2",             'expected_output' => "1: 1\n2: 2",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: IQR ─────────────────────────────────────────────────────
        $seed(18, [
            ['input' => "6\n1\n2\n3\n4\n5\n6",    'expected_output' => '3.00',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4",          'expected_output' => '2.00',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "8\n1\n3\n5\n7\n9\n11\n13\n15", 'expected_output' => '8.00', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0\n10",               'expected_output' => '10.00',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Variance ─────────────────────────────────────────────────
        $seed(19, [
            ['input' => "4\n2\n4\n4\n4",          'expected_output' => '1.00',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",             'expected_output' => '0.67',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n5\n5\n5\n5",       'expected_output' => '0.00',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0\n10",               'expected_output' => '25.00',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Standard deviation ───────────────────────────────────────
        $seed(20, [
            ['input' => "4\n2\n4\n4\n4",          'expected_output' => '1.00',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",             'expected_output' => '0.82',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n5\n5\n5\n5",       'expected_output' => '0.00',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0\n10",               'expected_output' => '5.00',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Pearson correlation ──────────────────────────────────────
        $seed(21, [
            ['input' => "3\n1 2\n2 4\n3 6",       'expected_output' => '1.00',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 3\n2 2\n3 1",       'expected_output' => '-1.00',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 1\n2 1\n3 1",       'expected_output' => '0.00',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2\n2 3\n3 4\n4 5",  'expected_output' => '1.00',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Right-skewed ─────────────────────────────────────────────
        $seed(22, [
            ['input' => "5\n1\n2\n3\n4\n100",     'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",             'expected_output' => 'False',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n5\n9",             'expected_output' => 'False',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n1\n2\n100",        'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Z-score outliers ─────────────────────────────────────────
        $seed(23, [
            ['input' => "5\n1\n2\n3\n4\n100\n2",  'expected_output' => '100.0',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n3",          'expected_output' => 'None',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n20\n30\n1000\n2", 'expected_output' => '1000.0', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0\n0\n0\n1",          'expected_output' => 'None',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Min-Max normalization ────────────────────────────────────
        $seed(24, [
            ['input' => "4\n0\n5\n10\n20",        'expected_output' => "0.00\n0.25\n0.50\n1.00",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0\n100",              'expected_output' => "0.00\n1.00",                 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3",             'expected_output' => "0.00\n0.50\n1.00",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n5\n5\n5",             'expected_output' => "0.00\n0.00\n0.00",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Z-score standardization ─────────────────────────────────
        $seed(25, [
            ['input' => "3\n2\n4\n6",             'expected_output' => "-1.00\n0.00\n1.00",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0\n0\n0",             'expected_output' => "0.00\n0.00\n0.00",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4",          'expected_output' => "-1.34\n-0.45\n0.45\n1.34", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n10\n20",              'expected_output' => "-1.00\n1.00",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: One-hot encoding ─────────────────────────────────────────
        $seed(26, [
            ['input' => "3\ncat\ndog\ncat",                 'expected_output' => "[1, 0]\n[0, 1]\n[1, 0]",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nyes\nno",                       'expected_output' => "[0, 1]\n[1, 0]",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\na\nb\nc",                       'expected_output' => "[1, 0, 0]\n[0, 1, 0]\n[0, 0, 1]", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\ncat\ncat",                      'expected_output' => "[1]\n[1]",                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Log transform ────────────────────────────────────────────
        $seed(27, [
            ['input' => "3\n1\n10\n100",          'expected_output' => "0.00\n2.30\n4.61",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n1",                'expected_output' => "0.00\n0.00",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2\n4\n8",             'expected_output' => "0.69\n1.39\n2.08",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n100",                 'expected_output' => "4.61",                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Binning ──────────────────────────────────────────────────
        $seed(28, [
            ['input' => "5\n0\n3\n6\n9\n12",      'expected_output' => "low\nlow\nmedium\nhigh\nhigh", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0\n50\n100",          'expected_output' => "low\nmedium\nhigh",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n5\n9",             'expected_output' => "low\nmedium\nhigh",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n10\n10\n10",          'expected_output' => "low\nlow\nlow",                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Dataset summary ──────────────────────────────────────────
        $seed(29, [
            ['input' => "3\nAlice,25,80\nBob,30,90\nCarol,22,70",  'expected_output' => "min: 70.00\nmax: 90.00\nmean: 80.00",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nX,20,100\nY,21,50",                    'expected_output' => "min: 50.00\nmax: 100.00\nmean: 75.00",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nZ,25,42",                              'expected_output' => "min: 42.00\nmax: 42.00\nmean: 42.00",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nA,1,10\nB,2,20\nC,3,30\nD,4,40",      'expected_output' => "min: 10.00\nmax: 40.00\nmean: 25.00",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: IQR outliers ─────────────────────────────────────────────
        $seed(30, [
            ['input' => "7\n1\n2\n3\n4\n5\n6\n100",    'expected_output' => '100.0', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4",               'expected_output' => 'None',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n2\n3\n4\n100",          'expected_output' => '100.0', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n5\n5\n5",                  'expected_output' => 'None',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: CSV Pearson correlation ──────────────────────────────────
        $seed(31, [
            ['input' => "3\n1,2\n2,4\n3,6",       'expected_output' => '1.00',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1,3\n2,2\n3,1",       'expected_output' => '-1.00',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1,1\n2,1\n3,1",       'expected_output' => '0.00',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1,2\n2,3\n3,4\n4,5", 'expected_output' => '1.00',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Percent above mean ───────────────────────────────────────
        $seed(32, [
            ['input' => "5\n1\n2\n3\n4\n5",       'expected_output' => '40.00',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4",          'expected_output' => '50.00',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n1\n1",             'expected_output' => '0.00',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0\n100",              'expected_output' => '50.00',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Accuracy ─────────────────────────────────────────────────
        $seed(33, [
            ['input' => "4\n1 1\n0 0\n1 0\n0 1",  'expected_output' => '50.00',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1\n1 1\n1 1",       'expected_output' => '100.00',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 0\n0 1",            'expected_output' => '0.00',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 1\n0 0\n1 1\n0 1\n0 0", 'expected_output' => '80.00', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q34: Confusion matrix parts ───────────────────────────────────
        $seed(34, [
            ['input' => "4\n1 1\n0 0\n1 0\n0 1",  'expected_output' => "TP: 1\nFP: 1\nTN: 1\nFN: 1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1\n1 1\n0 0",       'expected_output' => "TP: 2\nFP: 0\nTN: 1\nFN: 0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 0\n0 1",            'expected_output' => "TP: 0\nFP: 1\nTN: 0\nFN: 1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0 0\n1 1",            'expected_output' => "TP: 1\nFP: 0\nTN: 1\nFN: 0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: MAE ──────────────────────────────────────────────────────
        $seed(35, [
            ['input' => "3\n3.0 2.5\n-0.5 0.0\n2.0 2.0",   'expected_output' => '0.33',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1.0 2.0\n3.0 4.0",             'expected_output' => '1.00',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.0 0.0\n0.0 0.0\n0.0 0.0",    'expected_output' => '0.00',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10.0 5.0\n5.0 10.0",           'expected_output' => '5.00',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: MSE ──────────────────────────────────────────────────────
        $seed(36, [
            ['input' => "3\n3.0 2.5\n-0.5 0.0\n2.0 2.0",   'expected_output' => '0.08',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1.0 2.0\n3.0 4.0",             'expected_output' => '1.00',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.0 0.0\n0.0 0.0\n0.0 0.0",    'expected_output' => '0.00',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10.0 5.0\n5.0 10.0",           'expected_output' => '25.00', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: 1-NN classifier ──────────────────────────────────────────
        $seed(37, [
            ['input' => "5\nA 1\nA 2\nB 8\nB 9\nB 10\n5\n3",    'expected_output' => 'A',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nX 1\nX 2\nY 9\nY 10\n2\n8",         'expected_output' => 'Y',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nA 5\nB 5\n1\n5",                     'expected_output' => 'A',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nA 1\nB 10\nA 3\n1\n2",              'expected_output' => 'A',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Cluster assignment ────────────────────────────────────────
        $seed(38, [
            ['input' => "4\n1\n2\n8\n9\n2\n1 10",   'expected_output' => "1.0\n1.0\n10.0\n10.0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0\n5\n10\n2\n0 10",     'expected_output' => "0.0\n0.0\n10.0",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n3\n7\n2\n5 5",          'expected_output' => "5.0\n5.0",                'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n6\n11\n3\n0 5 10",   'expected_output' => "0.0\n5.0\n10.0",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Cluster counts ───────────────────────────────────────────
        $seed(39, [
            ['input' => "6\n0\n1\n0\n1\n0\n2",     'expected_output' => "0: 3\n1: 2\n2: 1",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0\n0\n0",              'expected_output' => "0: 3",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n1\n2",           'expected_output' => "1: 2\n2: 2",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0\n1",                 'expected_output' => "0: 1\n1: 1",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: 2D centroid ──────────────────────────────────────────────
        $seed(40, [
            ['input' => "3\n1 2\n3 4\n5 6",        'expected_output' => '3.00 4.00',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0\n10 10",           'expected_output' => '5.00 5.00',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n7 3",                  'expected_output' => '7.00 3.00',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 1\n1 1\n3 3\n3 3",  'expected_output' => '2.00 2.00',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: WCSS ─────────────────────────────────────────────────────
        $seed(41, [
            ['input' => "4\n1 0\n2 0\n8 1\n9 1",  'expected_output' => '1.00',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5 0\n5 0",            'expected_output' => '0.00',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 0\n3 0\n7 1\n9 1", 'expected_output' => '4.00',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0 0\n10 1\n20 1",    'expected_output' => '25.00',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Moving average ───────────────────────────────────────────
        $seed(42, [
            ['input' => "6\n1\n2\n3\n4\n5\n6\n3",          'expected_output' => "2.00\n3.00\n4.00\n5.00",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n10\n20\n30\n40\n2",            'expected_output' => "15.00\n25.00\n35.00",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n3\n5\n7\n9\n1",             'expected_output' => "1.00\n3.00\n5.00\n7.00\n9.00", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n0\n6\n12\n3",                  'expected_output' => "6.00",                        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: First differences ────────────────────────────────────────
        $seed(43, [
            ['input' => "5\n10\n13\n11\n15\n14",   'expected_output' => "3.0\n-2.0\n4.0\n-1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0\n5\n10",             'expected_output' => "5.0\n5.0",                'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n8\n6\n4",          'expected_output' => "-2.0\n-2.0\n-2.0",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n100\n50",              'expected_output' => "-50.0",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Lagged correlation ───────────────────────────────────────
        $seed(44, [
            ['input' => "5\n1\n2\n3\n4\n5\n1",    'expected_output' => '1.00',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n1",       'expected_output' => '1.00',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n4\n3\n2\n1\n1",    'expected_output' => '1.00',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n3\n2\n4\n2",       'expected_output' => '1.00',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Cumulative sum ───────────────────────────────────────────
        $seed(45, [
            ['input' => "4\n1\n2\n3\n4",           'expected_output' => "1.0\n3.0\n6.0\n10.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5",             'expected_output' => "5.0\n10.0\n15.0",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0\n0\n0\n0\n10",      'expected_output' => "0.0\n0.0\n0.0\n0.0\n10.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n100\n200",             'expected_output' => "100.0\n300.0",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Tokenize ─────────────────────────────────────────────────
        $seed(46, [
            ['input' => 'Hello World this is Python',    'expected_output' => "hello\nworld\nthis\nis\npython",  'is_hidden' => false, 'order_index' => 1],
            ['input' => 'Data Science is Fun',           'expected_output' => "data\nscience\nis\nfun",          'is_hidden' => false, 'order_index' => 2],
            ['input' => 'ONE',                           'expected_output' => "one",                             'is_hidden' => true,  'order_index' => 3],
            ['input' => 'A B C D',                      'expected_output' => "a\nb\nc\nd",                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Remove stop words ────────────────────────────────────────
        $seed(47, [
            ['input' => "the cat sat on the mat\nthe on",        'expected_output' => 'cat sat mat',     'is_hidden' => false, 'order_index' => 1],
            ['input' => "I love data science\nI",                'expected_output' => 'love data science', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "hello world\nhello world",              'expected_output' => '',                 'is_hidden' => true,  'order_index' => 3],
            ['input' => "keep all words here\nno match",         'expected_output' => 'keep all words here', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q48: Term Frequency ───────────────────────────────────────────
        $seed(48, [
            ['input' => "2\nthe cat sat on the mat\nthe dog ran\nthe",     'expected_output' => "0.33\n0.33",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\ndog dog dog\ncat cat\ndog",                    'expected_output' => "1.00\n0.00",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nhello world\nhello",                           'expected_output' => "0.50",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\none two three\nfour five six\none",            'expected_output' => "0.33\n0.00",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Positive/Negative word count ─────────────────────────────
        $seed(49, [
            ['input' => 'the movie was great but the acting was terrible',  'expected_output' => "positive: 1\nnegative: 1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'good great excellent happy',                        'expected_output' => "positive: 4\nnegative: 0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'bad horrible awful',                               'expected_output' => "positive: 0\nnegative: 3", 'is_hidden' => true,  'order_index' => 3],
            ['input' => 'just a plain sentence',                            'expected_output' => "positive: 0\nnegative: 0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Sentiment label ──────────────────────────────────────────
        $seed(50, [
            ['input' => 'I love this great product but it is terrible',     'expected_output' => 'Positive',  'is_hidden' => false, 'order_index' => 1],
            ['input' => 'this is bad and horrible',                         'expected_output' => 'Negative',  'is_hidden' => false, 'order_index' => 2],
            ['input' => 'just a plain sentence',                            'expected_output' => 'Neutral',   'is_hidden' => true,  'order_index' => 3],
            ['input' => 'good and bad in equal measure',                    'expected_output' => 'Neutral',   'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 3 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}