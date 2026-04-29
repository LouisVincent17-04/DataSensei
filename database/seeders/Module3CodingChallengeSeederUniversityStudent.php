<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 3 — Introduction to Data Science (University Student / Level 2) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the University Student tier
 *   2. coding_questions    — 50 questions covering intermediate Data Science concepts
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mapped to lessons 293–303):
 *   L1.1  What is Data Science? (pipeline thinking, data summarization)
 *   L1.2  NumPy: Arrays, Vectorization & Linear Algebra
 *   L1.3  Pandas: DataFrames, Cleaning & GroupBy
 *   L1.4  Visualization: Descriptive/distributional summaries
 *   L1.5  Statistics: Distributions, Hypothesis Testing & Correlation
 *   L1.6  Feature Engineering: Scaling, Encoding & Transformation
 *   L1.7  EDA: Exploratory Data Analysis in Practice
 *   L1.8  Machine Learning: Classification, Regression & Evaluation
 *   L1.9  Unsupervised Learning: K-Means & PCA
 *   L1.10 Time Series: Decomposition, Resampling & Forecasting Features
 *   L1.11 NLP: Text Preprocessing, TF-IDF & Sentiment Analysis
 *
 * Difficulty: University Student — problems require multi-step reasoning,
 * combining concepts (e.g. cleaning + aggregation, scaling + distance),
 * and implementing algorithms from scratch using only Python builtins + math.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module3CodingChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (! $category) {
            $this->command->error('University Student category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 3 — Introduction to Data Science (University Student) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Introduction to Data Science',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Tackle intermediate Data Science problems using pure Python. You will clean messy datasets, engineer features, implement evaluation metrics, build simple ML models from scratch, and process text — without any third-party libraries.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 1200,
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
Read `n` rows of CSV data (format: `name,value`). Some values are the string `NA`. Replace `NA` with the **mean of the non-missing values**, then print the **updated mean** of all values rounded to 2 decimal places.

Example:
```
Input:
4
Alice,10
Bob,NA
Carol,20
Dave,30
Output: 20.00
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Replace NA with mean, then compute updated mean\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `id,feature1,feature2,label`). Print the **count of each label**, sorted alphabetically by label. Format: `label: count`

Example:
```
Input:
5
1,2.1,3.0,cat
2,1.5,2.8,dog
3,3.0,1.2,cat
4,2.2,3.1,bird
5,1.0,2.0,dog
Output:
bird: 1
cat: 2
dog: 2
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Count labels and print sorted\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,score`). Print a summary:
```
count: N
mean: X
std: X
min: X
max: X
```
All numeric values rounded to 2 decimal places. Use **population** standard deviation.

Example:
```
Input:
3
Alice,80
Bob,90
Carol,70
Output:
count: 3
mean: 80.00
std: 8.16
min: 70.00
max: 90.00
```
MD,
                'starter_code'        => "import math\nn = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Compute and print summary statistics\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 175,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,value`). Remove **duplicate names** keeping the **last** occurrence. Print the remaining rows in their original relative order as `name,value`.

Example:
```
Input:
4
Alice,10
Bob,20
Alice,30
Carol,40
Output:
Bob,20
Alice,30
Carol,40
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Remove duplicates keeping last, preserve order\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: NumPy — Arrays, Vectorization & Linear Algebra (Q5–Q9)
            // ═══════════════════════════════════════════════════════════════

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read two matrices A (m×k) and B (k×n) and compute their **matrix product** C = A × B. Print each row of C space-separated.

Input format:
- Line 1: `m k n`
- Next `m` lines: rows of A
- Next `k` lines: rows of B

Example:
```
Input:
2 3 2
1 2 3
4 5 6
7 8
9 10
11 12
Output:
58 64
139 154
```
MD,
                'starter_code'        => "m, k, n = map(int, input().split())\nA = [list(map(int, input().split())) for _ in range(m)]\nB = [list(map(int, input().split())) for _ in range(k)]\n# Compute matrix product\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Read a square matrix of size n×n and print its **transpose**, each row space-separated.

Example:
```
Input:
3
1 2 3
4 5 6
7 8 9
Output:
1 4 7
2 5 8
3 6 9
```
MD,
                'starter_code'        => "n = int(input())\nM = [list(map(int, input().split())) for _ in range(n)]\n# Transpose and print\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Read a vector of `n` floats. Compute the **L2 norm** (Euclidean length) and return the **unit vector** (each element divided by L2 norm), rounded to 4 decimal places, space-separated.

Example:
```
Input: 3 4 0
Output: 0.6 0.8 0.0
```
MD,
                'starter_code'        => "import math\nnums = list(map(float, input().split()))\n# Compute unit vector\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 175,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Read an n×n matrix and compute the **trace** (sum of diagonal elements) and the **Frobenius norm** (square root of sum of squares of all elements), each rounded to 4 decimal places.

Print `trace: X` and `frobenius: X`.

Example:
```
Input:
2
1 2
3 4
Output:
trace: 5
frobenius: 5.4772
```
MD,
                'starter_code'        => "import math\nn = int(input())\nM = [list(map(float, input().split())) for _ in range(n)]\n# Compute trace and Frobenius norm\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 175,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read two vectors of the same length and compute the **cosine similarity** rounded to 4 decimal places.

cosine_similarity = dot(A, B) / (||A|| × ||B||)

Example:
```
Input:
1 0 0
0 1 0
Output: 0.0
```
```
Input:
1 2 3
1 2 3
Output: 1.0
```
MD,
                'starter_code'        => "import math\na = list(map(float, input().split()))\nb = list(map(float, input().split()))\n# Compute cosine similarity\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Pandas — DataFrames, Cleaning & GroupBy (Q10–Q14)
            // ═══════════════════════════════════════════════════════════════

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,dept,salary`). Some salaries are `NA`. For each department, print the **mean salary** (ignoring NA) sorted alphabetically by dept, rounded to 2 decimal places. Format: `dept: mean`

Example:
```
Input:
5
Alice,Eng,90000
Bob,Eng,NA
Carol,HR,70000
Dave,HR,80000
Eve,Eng,100000
Output:
Eng: 95000.00
HR: 75000.00
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# GroupBy dept, mean salary ignoring NA\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `id,value`). Some values are `NA`. **Forward-fill** the NA values (replace each NA with the most recent non-NA value above it). If the first value is NA, leave it as `NA`. Print all values, one per line.

Example:
```
Input:
5
1,10
2,NA
3,NA
4,30
5,NA
Output:
10
10
10
30
30
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Forward fill NA values\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,category,value`). Print a **pivot table**: for each unique category (columns, sorted alphabetically) and each unique name (rows, sorted alphabetically), show the value. Use `NA` if a combination doesn't exist.

Example:
```
Input:
4
Alice,math,90
Bob,math,80
Alice,science,85
Bob,science,75
Output:
name,math,science
Alice,90,85
Bob,80,75
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Build and print pivot table\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,score1,score2`). Print a new column `grade` appended: `A` if average ≥ 90, `B` if ≥ 80, `C` if ≥ 70, `D` if ≥ 60, `F` otherwise. Print as `name,score1,score2,grade`.

Example:
```
Input:
3
Alice,95,85
Bob,70,60
Carol,50,55
Output:
Alice,95,85,A
Bob,70,60,C
Carol,50,55,F
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Compute grade and print with new column\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 175,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,dept,score`). For each department, print the **name of the top scorer**. Sort output alphabetically by department. If tie, print the name that comes first alphabetically.

Example:
```
Input:
5
Alice,Eng,95
Bob,Eng,88
Carol,HR,91
Dave,HR,91
Eve,Eng,99
Output:
Eng: Eve
HR: Carol
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Find top scorer per department\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Visualization — Distributional Summaries (Q15–Q18)
            // ═══════════════════════════════════════════════════════════════

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Read `n` numbers and print a **text histogram** using `k` equal-width bins. For each bin, print `[lo, hi): ***` where `*` count equals the number of values in that bin. Values equal to `max` go in the last bin.

Read `n`, then the numbers, then `k`.

Example:
```
Input:
8
1 2 3 4 5 6 7 8
4
Output:
[1.0, 3.0): **
[3.0, 5.0): **
[5.0, 7.0): **
[7.0, 9.0): **
```
MD,
                'starter_code'        => "n = int(input())\nnums = list(map(float, input().split()))\nk = int(input())\n# Build and print text histogram\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Read `n` numbers and print their **five-number summary**:
```
min: X
Q1: X
median: X
Q3: X
max: X
```
All rounded to 2 decimal places. Use the exclusive quartile method (Q1 = median of lower half, Q3 = median of upper half, excluding the median for odd n).

Example:
```
Input:
7
1 3 5 7 9 11 13
Output:
min: 1.00
Q1: 3.00
median: 7.00
Q3: 11.00
max: 13.00
```
MD,
                'starter_code'        => "n = int(input())\nnums = sorted(map(float, input().split()))\n# Compute and print five-number summary\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Read `n` numbers and compute the **skewness** using Pearson's moment coefficient:

skewness = [n / ((n−1)(n−2))] × Σ((xᵢ − mean) / std)³

Use **sample** standard deviation (ddof=1). Print the skewness rounded to 4 decimal places, then classify as `right-skewed` (> 0.5), `left-skewed` (< -0.5), or `approximately symmetric`.

Example:
```
Input:
5
2 8 0 4 1
Output:
0.5280
approximately symmetric
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnums = list(map(float, input().split()))\n# Compute skewness and classify\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Read `n` numbers and compute the **empirical CDF** (cumulative distribution function). For each unique value (sorted ascending), print `value: cumulative_proportion` rounded to 4 decimal places.

Example:
```
Input:
5
3 1 2 3 4
Output:
1: 0.2
2: 0.4
3: 0.8
4: 1.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = list(map(float, input().split()))\n# Compute and print empirical CDF\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Statistics (Q19–Q23)  →  Lesson 297
            // ═══════════════════════════════════════════════════════════════

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Perform a **one-sample z-test**. Read:
- `n` sample values (one per line after n)
- population mean `mu`
- population std `sigma`
- significance level `alpha` (0.05 or 0.01)

Print the z-statistic (4 dp), and `Reject H0` or `Fail to reject H0` using a two-tailed test (critical values: α=0.05 → 1.96, α=0.01 → 2.576).

Example:
```
Input:
5
52 53 51 54 50
50
2
0.05
Output:
2.2361
Reject H0
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnums = list(map(float, input().split()))\nmu = float(input())\nsigma = float(input())\nalpha = float(input())\n# Compute z-test\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Compute the **95% confidence interval** for a sample mean.

CI = [x̄ − 1.96 × (σ/√n),  x̄ + 1.96 × (σ/√n)]

Read `n` sample values (space-separated) and population std `sigma`. Print the CI as `[lo, hi]` rounded to 4 decimal places.

Example:
```
Input:
5
52 53 51 54 50
2
Output:
[50.2484, 53.7516]
```
MD,
                'starter_code'        => "import math\nnums = list(map(float, input().split()))\nsigma = float(input())\n# Compute and print 95% CI\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Read `n` pairs of numbers (format: `x y` per line). Compute:
- Pearson r (4 dp)
- Spearman rank correlation (4 dp)

Print each on its own line as `pearson: X` and `spearman: X`.

For Spearman, use the formula: rs = 1 − 6Σdᵢ² / (n(n²−1)) where dᵢ = rank(xᵢ) − rank(yᵢ).

Example:
```
Input:
4
1 1
2 3
3 2
4 4
Output:
pearson: 0.9487
spearman: 0.8
```
MD,
                'starter_code'        => "import math\nn = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Compute Pearson and Spearman\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Read `n` numbers and compute the **bootstrap standard error** using `B` bootstrap samples of size `n` **with replacement** (use a fixed random seed = 42 via the `random` module).

Print the standard error of the mean (std of bootstrap sample means) rounded to 4 decimal places.

Example:
```
Input:
5
2 4 6 8 10
1000
Output:
(a float — exact value depends on seed)
```

Note: Use `random.seed(42)` and `random.choices(nums, k=n)` for each bootstrap sample.
MD,
                'starter_code'        => "import random\nrandom.seed(42)\nnums = list(map(float, input().split()))\nB = int(input())\nn = len(nums)\n# Compute bootstrap SE\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Compute the **chi-square statistic** for a goodness-of-fit test.

χ² = Σ (Oᵢ − Eᵢ)² / Eᵢ

Read `n` observed counts (space-separated) and `n` expected counts (next line). Print χ² rounded to 4 decimal places.

Example:
```
Input:
20 30 25 25
25 25 25 25
Output: 2.0
```
MD,
                'starter_code'        => "observed = list(map(float, input().split()))\nexpected = list(map(float, input().split()))\n# Compute chi-square statistic\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Feature Engineering (Q24–Q28)
            // ═══════════════════════════════════════════════════════════════

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Read a dataset as `n` rows of `m` space-separated floats. Apply **Min-Max normalization** column-wise (each column scaled independently to [0, 1]). Print the scaled matrix, each value rounded to 4 decimal places, rows space-separated.

Read `n m` on line 1, then `n` rows.

Example:
```
Input:
3 2
0 0
5 10
10 20
Output:
0.0 0.0
0.5 0.5
1.0 1.0
```
MD,
                'starter_code'        => "n, m = map(int, input().split())\nmatrix = [list(map(float, input().split())) for _ in range(n)]\n# Min-Max scale column-wise\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Read `n` rows of `m` floats. Apply **Z-score standardization** column-wise (population std). Print the standardized matrix rounded to 4 decimal places, rows space-separated.

Read `n m`, then `n` rows.

Example:
```
Input:
3 1
2
4
6
Output:
-1.0
0.0
1.0
```
MD,
                'starter_code'        => "import math\nn, m = map(int, input().split())\nmatrix = [list(map(float, input().split())) for _ in range(n)]\n# Z-score standardize column-wise\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,color,size`). Apply **label encoding** to each categorical column: sort unique values alphabetically and assign integer indices starting from 0. Print the encoded dataset (name column unchanged) as CSV.

Example:
```
Input:
3
Alice,red,large
Bob,blue,small
Carol,red,medium
Output:
Alice,1,0
Bob,0,2
Carol,1,1
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Label encode categorical columns\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Read `n` numbers and create **polynomial features** up to degree 2 for a single feature x: `[1, x, x²]`. Print the feature matrix, each row space-separated, values rounded to 2 decimal places.

Example:
```
Input:
3
2
3
4
Output:
1.0 2.0 4.0
1.0 3.0 9.0
1.0 4.0 16.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Create polynomial features [1, x, x^2]\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 175,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `id,timestamp,value`) where `timestamp` is an integer (Unix seconds). Extract these features from `timestamp`: `hour` (0–23), `day_of_week` (0=Mon…6=Sun using `timestamp // 86400 % 7`), `is_weekend` (1 if day_of_week ≥ 5, else 0). Print as `id,hour,day_of_week,is_weekend`.

Example:
```
Input:
2
1,86400,100
2,172800,200
Output:
1,0,1,0
2,0,2,0
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Extract timestamp features\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: EDA (Q29–Q32)  →  Lesson 299
            // ═══════════════════════════════════════════════════════════════

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,age,score`). Print a **correlation matrix** for `age` and `score` (Pearson r). Format:

```
     age  score
age  1.00  X
score  X  1.00
```
Round off-diagonal values to 2 decimal places.

Example:
```
Input:
4
Alice,20,80
Bob,25,90
Carol,30,85
Dave,35,95
Output:
     age  score
age  1.00  1.00
score  1.00  1.00
```
MD,
                'starter_code'        => "import math\nn = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Compute and print correlation matrix\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Read `n` rows of CSV (format: `feature1,feature2,label`). For each label, print the **mean of feature1 and feature2**, sorted alphabetically by label. Format: `label: f1_mean, f2_mean` (2 dp each).

Example:
```
Input:
6
5.1,3.5,A
4.9,3.0,A
6.3,3.3,B
5.8,2.7,B
6.7,3.1,B
4.6,3.4,A
Output:
A: 4.87, 3.30
B: 6.27, 3.03
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Mean of features grouped by label\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Read `n` numbers and detect **outliers** using **both** the IQR method and the Z-score method (|z| > 2). Print values flagged by **either** method, sorted ascending, one per line. If none, print `None`.

Example:
```
Input:
8
1 2 3 4 5 6 7 100
Output: 100.0
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnums = list(map(float, input().split()))\n# Detect outliers by IQR and Z-score\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Read `n` rows of CSV (format: `name,value`). Compute the **percentage change** from each row to the next (`(new − old) / old × 100`). Print the name of the row and the pct change rounded to 2 decimal places, for rows 2..n. Format: `name: X%`

Example:
```
Input:
4
Jan,100
Feb,110
Mar,99
Apr,120
Output:
Feb: 10.00%
Mar: -10.00%
Apr: 21.21%
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Compute and print percentage change\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 175,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Machine Learning (Q33–Q37)  →  Lesson 300
            // ═══════════════════════════════════════════════════════════════

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Implement **k-Nearest Neighbours (k-NN) classifier** using Euclidean distance. Training data has 2 features and a label.

Input:
- `n` training rows as `x1 x2 label`
- `k`
- `q` test points as `x1 x2`

For each test point, print the predicted label (majority vote; tie-break: alphabetically first label).

Example:
```
Input:
4
1 1 A
2 2 A
8 8 B
9 9 B
1
2
3 3
7 7
Output:
A
B
```
MD,
                'starter_code'        => "import math\nn = int(input())\ntrain = [input().split() for _ in range(n)]\nk = int(input())\nq = int(input())\n# Implement k-NN classifier\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 275,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Implement **simple linear regression** from scratch (OLS). Read `n` training pairs `x y` (one per line), then `q` test x values. Print the predicted y for each test point rounded to 4 decimal places.

Example:
```
Input:
3
1 2
2 4
3 6
2
0
4
Output:
0.0
8.0
```
MD,
                'starter_code'        => "n = int(input())\ndata = [tuple(map(float, input().split())) for _ in range(n)]\nq = int(input())\n# Fit OLS and predict\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Compute full **classification metrics**: Accuracy, Precision, Recall, F1-score for binary classification (1 = positive, 0 = negative).

Read `n` pairs `actual predicted` (one per line). Print:
```
accuracy: X
precision: X
recall: X
f1: X
```
All rounded to 4 decimal places.

Example:
```
Input:
5
1 1
0 0
1 0
0 1
1 1
Output:
accuracy: 0.6
precision: 0.6667
recall: 0.6667
f1: 0.6667
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(int, input().split())) for _ in range(n)]\n# Compute classification metrics\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Implement a **Naive Bayes classifier** for binary classification. Features are binary (0 or 1).

Input:
- `n` training rows as `f1 f2 ... fm label` (space-separated)
- `q` test rows as `f1 f2 ... fm`

Use Laplace smoothing (add 1 to counts). Print the predicted label for each test row.

Example:
```
Input:
4
1 0 A
1 1 A
0 1 B
0 0 B
2
1 0
0 1
Output:
A
B
```
MD,
                'starter_code'        => "n = int(input())\ntrain = [input().split() for _ in range(n)]\nq = int(input())\n# Naive Bayes with Laplace smoothing\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Compute the **R² score** and **RMSE** for a regression model.

Read `n` pairs `actual predicted` (floats, space-separated, one per line). Print:
```
r2: X
rmse: X
```
Both rounded to 4 decimal places.

Example:
```
Input:
4
3 2.5
8 7.5
10 10.5
17 17.5
Output:
r2: 0.9966
rmse: 0.5
```
MD,
                'starter_code'        => "import math\nn = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Compute R2 and RMSE\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Unsupervised Learning (Q38–Q41)  →  Lesson 301
            // ═══════════════════════════════════════════════════════════════

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Implement **one iteration of K-Means** for 2D data. Read `n` points (as `x y`) and `k` initial centroids (as `x y`). Assign each point to the nearest centroid (Euclidean), then compute the new centroids. Print the new centroids rounded to 4 decimal places, one per line as `x y`.

Input format: `n`, then n `x y` lines, then `k`, then k `cx cy` lines.

Example:
```
Input:
4
1 1
1 2
8 1
8 2
2
1 1
8 1
Output:
1.0 1.5
8.0 1.5
```
MD,
                'starter_code'        => "import math\nn = int(input())\npoints = [tuple(map(float, input().split())) for _ in range(n)]\nk = int(input())\ncentroids = [tuple(map(float, input().split())) for _ in range(k)]\n# One K-Means iteration\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 275,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Compute the **silhouette score** for a set of 1D points with given cluster assignments.

For each point i:
- a(i) = mean distance to other points in the same cluster
- b(i) = min mean distance to points in any other cluster
- s(i) = (b(i) − a(i)) / max(a(i), b(i))

Print the mean silhouette score rounded to 4 decimal places.

Input: `n` lines of `point cluster_id`.

Example:
```
Input:
4
1 0
2 0
8 1
9 1
Output: 0.8571
```
MD,
                'starter_code'        => "n = int(input())\ndata = [tuple(map(float, input().split())) for _ in range(n)]\n# Compute silhouette score\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Compute the **first principal component** of a 2D dataset using the covariance method.

Steps:
1. Mean-center the data
2. Compute the 2×2 covariance matrix
3. Find the eigenvector corresponding to the largest eigenvalue using the formula for 2×2 matrices
4. Print the unit eigenvector (2 values rounded to 4 dp), and the explained variance ratio (largest eigenvalue / sum of eigenvalues) rounded to 4 dp.

Read `n m` then n rows of m=2 floats.

Example:
```
Input:
3 2
2 0
0 2
4 4
Output:
0.7071 0.7071
1.0
```
MD,
                'starter_code'        => "import math\nn, m = map(int, input().split())\ndata = [list(map(float, input().split())) for _ in range(n)]\n# Compute first PC of 2D data\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 325,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Compute the **WCSS (Within-Cluster Sum of Squares)** and **BCSS (Between-Cluster Sum of Squares)** for a clustering.

Read `n` lines of `value cluster_id` (1D data). Print:
```
wcss: X
bcss: X
```
Rounded to 4 decimal places.

Example:
```
Input:
4
1 0
2 0
8 1
9 1
Output:
wcss: 1.0
bcss: 24.5
```
MD,
                'starter_code'        => "n = int(input())\ndata = [input().split() for _ in range(n)]\n# Compute WCSS and BCSS\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Time Series (Q42–Q45)  →  Lesson 302
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Compute an **Exponentially Weighted Moving Average (EWMA)** of a time series with smoothing factor `alpha`.

EWMAₜ = alpha × xₜ + (1 − alpha) × EWMAₜ₋₁

Initialise with the first value. Print each EWMA value rounded to 4 decimal places, one per line.

Read `n` values (one per line), then `alpha`.

Example:
```
Input:
4
10
20
30
40
0.5
Output:
10.0
15.0
22.5
31.25
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\nalpha = float(input())\n# Compute EWMA\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Perform simple **time series decomposition** (additive model). Given a series and a period `p`, compute:
- **Trend**: centred moving average of window `p`
- **Seasonal**: average deviation from trend per period position (0-indexed mod p)
- **Residual**: series − trend − seasonal

Print the trend, seasonal component per position, and residual for each timestep where trend is defined. Use `NA` where trend is undefined.

Read `n` values (one per line), then `p`.

Example:
```
Input:
6
2 4 6 8 10 12
2
Output:
trend: NA 5.0 7.0 9.0 11.0 NA
seasonal: 0 -3.0 1 3.0
residual: NA 0.0 0.0 0.0 0.0 NA
```
MD,
                'starter_code'        => "n = int(input())\nnums = list(map(float, input().split()))\np = int(input())\n# Time series decomposition\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 325,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Compute **lag features** for a time series. Given a series and `L` lags, create a feature matrix where each row is `[x[t], x[t-1], ..., x[t-L]]` for t = L..n-1. Print the matrix, rows space-separated, rounded to 2 decimal places.

Read `n` values (space-separated), then `L`.

Example:
```
Input:
5 10 15 20 25
2
Output:
15.0 10.0 5.0
20.0 15.0 10.0
25.0 20.0 15.0
```
MD,
                'starter_code'        => "nums = list(map(float, input().split()))\nL = int(input())\n# Compute lag feature matrix\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Compute **autocorrelation** at lag `k` for a time series.

ACF(k) = Σ[(xₜ − x̄)(xₜ₋ₖ − x̄)] / Σ[(xₜ − x̄)²]

(Sum over t = k..n-1 in numerator, t = 0..n-1 in denominator.)

Read `n` values (space-separated), then `k`. Print the ACF rounded to 4 decimal places.

Example:
```
Input:
5 10 15 20 25
1
Output: 0.8
```
MD,
                'starter_code'        => "nums = list(map(float, input().split()))\nk = int(input())\n# Compute autocorrelation at lag k\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11: NLP (Q46–Q50)  →  Lesson 303
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Compute **TF-IDF** scores for a query term across `n` documents.

TF(t, d) = count(t in d) / len(d)
IDF(t) = log((1 + n) / (1 + df(t))) + 1   [smoothed]
TF-IDF(t, d) = TF × IDF

Read `n` (number of documents), then `n` documents (one per line), then the query term. Print the TF-IDF score for each document rounded to 4 decimal places, one per line.

Example:
```
Input:
2
the cat sat on the mat
the dog ran
the
Output:
0.3948
0.3948
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndocs = [input().split() for _ in range(n)]\nterm = input().strip()\n# Compute TF-IDF for term across docs\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 275,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Implement **text cleaning**: lowercase, remove punctuation (keep only letters and spaces), remove stop words, and stem by stripping trailing `ing`, `ed`, `ly`, `s` (in that priority order, only if the result is ≥ 3 chars).

Read a sentence and stop words (second line, space-separated). Print the cleaned, stemmed tokens space-separated.

Example:
```
Input:
The cats are running quickly and jumped happily
the are and
Output:
cat run quick jump happily
```
MD,
                'starter_code'        => "import re\nsentence = input()\nstopwords = set(input().split())\n# Clean, remove stopwords, stem\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Compute the **cosine similarity between two documents** using TF vectors (raw term frequency, no IDF).

Read two documents (one per line). Print the cosine similarity rounded to 4 decimal places.

Example:
```
Input:
the cat sat on the mat
the dog sat on the log
Output: 0.6667
```
MD,
                'starter_code'        => "import math\ndoc1 = input().split()\ndoc2 = input().split()\n# Compute cosine similarity using TF vectors\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Build a simple **bigram language model**. Read `n` sentences. Count all bigrams (consecutive word pairs). Then read `q` queries (each a pair `word1 word2`). For each query, print the bigram probability P(word2|word1) = count(word1 word2) / count(word1), rounded to 4 decimal places. Print `0.0` if word1 never appears.

Example:
```
Input:
2
I love data science
I love python
2
I love
love data
Output:
1.0
0.5
```
MD,
                'starter_code'        => "n = int(input())\nsentences = [input().split() for _ in range(n)]\nq = int(input())\n# Build bigram model and answer queries\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Implement a **Naive Bayes sentiment classifier**. Read `n` labelled training sentences (format: `label|sentence`, label = `pos` or `neg`). Then classify `q` test sentences. Use Laplace smoothing. Print `pos` or `neg` for each test sentence.

Example:
```
Input:
4
pos|great movie loved it
pos|excellent film amazing
neg|terrible waste of time
neg|awful boring film
2
loved the film
awful movie
Output:
pos
neg
```
MD,
                'starter_code'        => "n = int(input())\ntrain = [input().split('|') for _ in range(n)]\nq = int(input())\n# Naive Bayes sentiment classifier\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 325,
            ],
        ];

        // ─────────────────────────────────────────────────────────────────
        // 3. INSERT QUESTIONS
        // ─────────────────────────────────────────────────────────────────

        $questionIds = [];

        foreach ($questionDefs as $qd) {
            $row = DB::table('coding_questions')->where([
                'challenge_id' => $challenge->id,
                'order_index'  => $qd['order_index'],
            ])->first();

            if (! $row) {
                $id = DB::table('coding_questions')->insertGetId([
                    'challenge_id'        => $challenge->id,
                    'order_index'         => $qd['order_index'],
                    'problem_description' => $qd['problem_description'],
                    'starter_code'        => $qd['starter_code'],
                    'time_limit_seconds'  => $qd['time_limit_seconds'],
                    'base_xp'             => $qd['base_xp'],
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            } else {
                $id = $row->id;
            }

            $questionIds[$qd['order_index']] = $id;
        }

        $this->command->info('Questions inserted/verified. Seeding test cases...');

        // ─────────────────────────────────────────────────────────────────
        // 4. TEST CASES (4 per question)
        // ─────────────────────────────────────────────────────────────────

        $seed = function (int $orderIndex, array $cases) use ($questionIds): void {
            $qid = $questionIds[$orderIndex] ?? null;
            if (! $qid) return;

            foreach ($cases as $c) {
                $exists = DB::table('test_cases')->where([
                    'coding_question_id' => $qid,
                    'order_index'        => $c['order_index'],
                ])->exists();

                if (! $exists) {
                    DB::table('test_cases')->insert([
                        'coding_question_id' => $qid,
                        'input'              => $c['input'],
                        'expected_output'    => $c['expected_output'],
                        'is_hidden'          => $c['is_hidden'],
                        'order_index'        => $c['order_index'],
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
            }
        };

        // ── Q1: NA imputation + mean ──────────────────────────────────────
        $seed(1, [
            ['input' => "4\nAlice,10\nBob,NA\nCarol,20\nDave,30",      'expected_output' => "20.00",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA,5\nB,15",                                 'expected_output' => "10.00",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,NA\nB,NA\nC,10",                         'expected_output' => "10.00",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nA,1\nB,2\nC,3",                            'expected_output' => "2.00",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: label counts ──────────────────────────────────────────────
        $seed(2, [
            ['input' => "5\n1,2.1,3.0,cat\n2,1.5,2.8,dog\n3,3.0,1.2,cat\n4,2.2,3.1,bird\n5,1.0,2.0,dog",  'expected_output' => "bird: 1\ncat: 2\ndog: 2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1,1,1,A\n2,2,2,A",                                                              'expected_output' => "A: 2",                      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1,0,0,X\n2,0,0,Y\n3,0,0,X",                                                   'expected_output' => "X: 2\nY: 1",                 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1,1,1,solo",                                                                    'expected_output' => "solo: 1",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: summary stats ─────────────────────────────────────────────
        $seed(3, [
            ['input' => "3\nAlice,80\nBob,90\nCarol,70",  'expected_output' => "count: 3\nmean: 80.00\nstd: 8.16\nmin: 70.00\nmax: 90.00",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\nX,50",                        'expected_output' => "count: 1\nmean: 50.00\nstd: 0.00\nmin: 50.00\nmax: 50.00",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nA,1\nB,2\nC,3\nD,4",         'expected_output' => "count: 4\nmean: 2.50\nstd: 1.12\nmin: 1.00\nmax: 4.00",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nA,0\nB,100",                  'expected_output' => "count: 2\nmean: 50.00\nstd: 50.00\nmin: 0.00\nmax: 100.00", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: dedup keep last ───────────────────────────────────────────
        $seed(4, [
            ['input' => "4\nAlice,10\nBob,20\nAlice,30\nCarol,40",   'expected_output' => "Bob,20\nAlice,30\nCarol,40",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA,1\nA,2",                                'expected_output' => "A,2",                          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nX,1\nY,2\nZ,3",                          'expected_output' => "X,1\nY,2\nZ,3",                'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nA,1\nB,2\nA,3\nB,4",                    'expected_output' => "A,3\nB,4",                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: matrix product ────────────────────────────────────────────
        $seed(5, [
            ['input' => "2 3 2\n1 2 3\n4 5 6\n7 8\n9 10\n11 12",   'expected_output' => "58 64\n139 154",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 1\n1 2\n3\n4",                          'expected_output' => "11",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2 2\n1 0\n0 1\n5 6\n7 8",               'expected_output' => "5 6\n7 8",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 3 1\n1 2 3\n1\n2\n3",                    'expected_output' => "14",               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: transpose ─────────────────────────────────────────────────
        $seed(6, [
            ['input' => "3\n1 2 3\n4 5 6\n7 8 9",   'expected_output' => "1 4 7\n2 5 8\n3 6 9",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n5",                       'expected_output' => "5",                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 2\n3 4",              'expected_output' => "1 3\n2 4",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 0 0\n0 1 0\n0 0 1",  'expected_output' => "1 0 0\n0 1 0\n0 0 1",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: unit vector ───────────────────────────────────────────────
        $seed(7, [
            ['input' => "3 4 0",        'expected_output' => "0.6 0.8 0.0",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0 0",        'expected_output' => "1.0 0.0 0.0",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 1 1 1",      'expected_output' => "0.5 0.5 0.5 0.5",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "0 3 4",        'expected_output' => "0.0 0.6 0.8",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: trace & Frobenius ─────────────────────────────────────────
        $seed(8, [
            ['input' => "2\n1 2\n3 4",       'expected_output' => "trace: 5\nfrobenius: 5.4772",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n7",              'expected_output' => "trace: 7\nfrobenius: 7.0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 0\n0 0",      'expected_output' => "trace: 0\nfrobenius: 0.0",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 0 0\n0 1 0\n0 0 1",  'expected_output' => "trace: 3\nfrobenius: 1.7321",  'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q9: cosine similarity ─────────────────────────────────────────
        $seed(9, [
            ['input' => "1 0 0\n0 1 0",   'expected_output' => "0.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3\n1 2 3",   'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 0\n-1 0",      'expected_output' => "-1.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 4\n4 3",       'expected_output' => "0.96",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: groupby mean salary ──────────────────────────────────────
        $seed(10, [
            ['input' => "5\nAlice,Eng,90000\nBob,Eng,NA\nCarol,HR,70000\nDave,HR,80000\nEve,Eng,100000",  'expected_output' => "Eng: 95000.00\nHR: 75000.00",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA,X,100\nB,X,200",                                                              'expected_output' => "X: 150.00",                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,G1,NA\nB,G1,NA\nC,G1,90",                                                   'expected_output' => "G1: 90.00",                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nA,P,10\nB,Q,20",                                                               'expected_output' => "P: 10.00\nQ: 20.00",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: forward fill ─────────────────────────────────────────────
        $seed(11, [
            ['input' => "5\n1,10\n2,NA\n3,NA\n4,30\n5,NA",   'expected_output' => "10\n10\n10\n30\n30",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1,5\n2,NA\n3,NA",                 'expected_output' => "5\n5\n5",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1,NA\n2,NA\n3,7",                'expected_output' => "NA\nNA\n7",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1,1\n2,2",                        'expected_output' => "1\n2",                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: pivot table ──────────────────────────────────────────────
        $seed(12, [
            ['input' => "4\nAlice,math,90\nBob,math,80\nAlice,science,85\nBob,science,75",   'expected_output' => "name,math,science\nAlice,90,85\nBob,80,75",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA,x,1\nB,y,2",                                                   'expected_output' => "name,x,y\nA,1,NA\nB,NA,2",                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,c1,10\nA,c2,20\nB,c1,30",                                    'expected_output' => "name,c1,c2\nA,10,20\nB,30,NA",                'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nX,col,99",                                                        'expected_output' => "name,col\nX,99",                             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: grade column ─────────────────────────────────────────────
        $seed(13, [
            ['input' => "3\nAlice,95,85\nBob,70,60\nCarol,50,55",   'expected_output' => "Alice,95,85,A\nBob,70,60,C\nCarol,50,55,F",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\nX,90,90",                                'expected_output' => "X,90,90,A",                                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nA,80,80\nB,60,60",                      'expected_output' => "A,80,80,B\nB,60,60,D",                       'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nZ,0,0",                                   'expected_output' => "Z,0,0,F",                                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: top scorer per dept ──────────────────────────────────────
        $seed(14, [
            ['input' => "5\nAlice,Eng,95\nBob,Eng,88\nCarol,HR,91\nDave,HR,91\nEve,Eng,99",   'expected_output' => "Eng: Eve\nHR: Carol",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA,X,50\nB,X,60",                                                   'expected_output' => "X: B",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,D1,100\nB,D2,90\nC,D1,80",                                      'expected_output' => "D1: A\nD2: B",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nSolo,OnlyDept,77",                                                 'expected_output' => "OnlyDept: Solo",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: text histogram ───────────────────────────────────────────
        $seed(15, [
            ['input' => "8\n1 2 3 4 5 6 7 8\n4",   'expected_output' => "[1.0, 3.0): **\n[3.0, 5.0): **\n[5.0, 7.0): **\n[7.0, 9.0): **",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n3",             'expected_output' => "[1.0, 1.67): *\n[1.67, 2.33): *\n[2.33, 3.0): *",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 5 10 10\n2",         'expected_output' => "[0.0, 5.0): *\n[5.0, 10.0): ***",                                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 10\n1",              'expected_output' => "[1.0, 10.0): **",                                                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: five-number summary ──────────────────────────────────────
        $seed(16, [
            ['input' => "7\n1 3 5 7 9 11 13",   'expected_output' => "min: 1.00\nQ1: 3.00\nmedian: 7.00\nQ3: 11.00\nmax: 13.00",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n2 4 6 8",           'expected_output' => "min: 2.00\nQ1: 3.00\nmedian: 5.00\nQ3: 7.00\nmax: 8.00",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 2 3 4 5",         'expected_output' => "min: 1.00\nQ1: 1.50\nmedian: 3.00\nQ3: 4.50\nmax: 5.00",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10 20",             'expected_output' => "min: 10.00\nQ1: 10.00\nmedian: 15.00\nQ3: 20.00\nmax: 20.00", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q17: skewness ─────────────────────────────────────────────────
        $seed(17, [
            ['input' => "5\n2 8 0 4 1",         'expected_output' => "0.528\napproximately symmetric",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3",             'expected_output' => "0.0\napproximately symmetric",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 2 3 4 100",       'expected_output' => "2.1658\nright-skewed",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n100 4 3 2 1",       'expected_output' => "2.1658\nright-skewed",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: empirical CDF ────────────────────────────────────────────
        $seed(18, [
            ['input' => "5\n3 1 2 3 4",   'expected_output' => "1.0: 0.2\n2.0: 0.4\n3.0: 0.8\n4.0: 1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1 1",       'expected_output' => "1.0: 1.0",                                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4 3 2 1",     'expected_output' => "1.0: 0.25\n2.0: 0.5\n3.0: 0.75\n4.0: 1.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n5 10",        'expected_output' => "5.0: 0.5\n10.0: 1.0",                        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: one-sample z-test ────────────────────────────────────────
        $seed(19, [
            ['input' => "5\n52 53 51 54 50\n50\n2\n0.05",   'expected_output' => "2.2361\nReject H0",         'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n10 10 10 10\n10\n1\n0.05",      'expected_output' => "0.0\nFail to reject H0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10 10 10 10\n10\n1\n0.01",      'expected_output' => "0.0\nFail to reject H0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n15 15\n10\n5\n0.05",            'expected_output' => "1.4142\nFail to reject H0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: confidence interval ──────────────────────────────────────
        $seed(20, [
            ['input' => "52 53 51 54 50\n2",   'expected_output' => "[50.2484, 53.7516]",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "10 10 10 10\n1",       'expected_output' => "[9.02, 10.98]",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n10",              'expected_output' => "[80.4, 119.6]",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "0 0 0 0\n5",          'expected_output' => "[-4.9, 4.9]",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: pearson + spearman ───────────────────────────────────────
        $seed(21, [
            ['input' => "4\n1 1\n2 3\n3 2\n4 4",   'expected_output' => "pearson: 0.9487\nspearman: 0.8",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1\n2 2\n3 3",        'expected_output' => "pearson: 1.0\nspearman: 1.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 3\n2 2\n3 1",        'expected_output' => "pearson: -1.0\nspearman: -1.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2\n2 1\n3 4\n4 3",  'expected_output' => "pearson: 0.8\nspearman: 0.8",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: bootstrap SE ─────────────────────────────────────────────
        $seed(22, [
            ['input' => "2 4 6 8 10\n1000",   'expected_output' => "1.2567",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1 1 1\n500",       'expected_output' => "0.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "0 10\n100",          'expected_output' => "2.3",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 5 5 5 5\n200",    'expected_output' => "0.0",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: chi-square ───────────────────────────────────────────────
        $seed(23, [
            ['input' => "20 30 25 25\n25 25 25 25",    'expected_output' => "2.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "25 25 25 25\n25 25 25 25",    'expected_output' => "0.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "50 50\n25 75",                'expected_output' => "33.3333",'is_hidden' => true,  'order_index' => 3],
            ['input' => "10 20 30\n20 20 20",          'expected_output' => "10.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: column-wise min-max ──────────────────────────────────────
        $seed(24, [
            ['input' => "3 2\n0 0\n5 10\n10 20",      'expected_output' => "0.0 0.0\n0.5 0.5\n1.0 1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 1\n1\n3",                   'expected_output' => "0.0\n1.0",                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 1\n5\n5\n5",               'expected_output' => "0.0\n0.0\n0.0",                'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n0 10\n10 0",            'expected_output' => "0.0 1.0\n1.0 0.0",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: column-wise z-score ──────────────────────────────────────
        $seed(25, [
            ['input' => "3 1\n2\n4\n6",           'expected_output' => "-1.0\n0.0\n1.0",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 1\n0\n0",              'expected_output' => "0.0\n0.0",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2\n1 4\n2 5\n3 6",   'expected_output' => "-1.0 -1.0\n0.0 0.0\n1.0 1.0",  'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 2\n0 10\n10 0",       'expected_output' => "-1.0 1.0\n1.0 -1.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: label encoding ───────────────────────────────────────────
        $seed(26, [
            ['input' => "3\nAlice,red,large\nBob,blue,small\nCarol,red,medium",   'expected_output' => "Alice,1,0\nBob,0,2\nCarol,1,1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA,cat,yes\nB,dog,no",                                 'expected_output' => "A,0,1\nB,1,0",                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nX,a,b\nY,a,c\nZ,b,b",                               'expected_output' => "X,0,0\nY,0,1\nZ,1,0",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nN,z,z",                                               'expected_output' => "N,0,0",                           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: polynomial features ──────────────────────────────────────
        $seed(27, [
            ['input' => "3\n2\n3\n4",   'expected_output' => "1.0 2.0 4.0\n1.0 3.0 9.0\n1.0 4.0 16.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n5",         'expected_output' => "1.0 5.0 25.0",                               'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0\n1",      'expected_output' => "1.0 0.0 0.0\n1.0 1.0 1.0",                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10\n-2",    'expected_output' => "1.0 10.0 100.0\n1.0 -2.0 4.0",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: timestamp features ───────────────────────────────────────
        $seed(28, [
            ['input' => "2\n1,86400,100\n2,172800,200",           'expected_output' => "1,0,1,0\n2,0,2,0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1,0,50",                               'expected_output' => "1,0,0,0",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1,432000,10",                         'expected_output' => "1,0,5,1",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1,518400,10",                         'expected_output' => "1,0,6,1",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: correlation matrix ───────────────────────────────────────
        $seed(29, [
            ['input' => "4\nAlice,20,80\nBob,25,90\nCarol,30,85\nDave,35,95",   'expected_output' => "     age  score\nage  1.00  1.00\nscore  1.00  1.00",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA,1,10\nB,2,20",                                     'expected_output' => "     age  score\nage  1.00  1.00\nscore  1.00  1.00",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,1,3\nB,2,2\nC,3,1",                              'expected_output' => "     age  score\nage  1.00  -1.00\nscore  -1.00  1.00",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nA,5,5\nB,5,5\nC,5,5",                              'expected_output' => "     age  score\nage  1.00  0.00\nscore  0.00  1.00",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: feature mean by label ────────────────────────────────────
        $seed(30, [
            ['input' => "6\n5.1,3.5,A\n4.9,3.0,A\n6.3,3.3,B\n5.8,2.7,B\n6.7,3.1,B\n4.6,3.4,A",   'expected_output' => "A: 4.87, 3.30\nB: 6.27, 3.03",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1,2,X\n3,4,X",                                                           'expected_output' => "X: 2.00, 3.00",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0,0,A\n2,2,A\n10,10,B\n12,12,B",                                       'expected_output' => "A: 1.00, 1.00\nB: 11.00, 11.00", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5,5,Z",                                                                  'expected_output' => "Z: 5.00, 5.00",                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: outlier union ────────────────────────────────────────────
        $seed(31, [
            ['input' => "8\n1 2 3 4 5 6 7 100",   'expected_output' => "100.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2 3 4",             'expected_output' => "None",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1 2 3 4 5 200",       'expected_output' => "200.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n5 5 5 5 5",           'expected_output' => "None",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: percentage change ────────────────────────────────────────
        $seed(32, [
            ['input' => "4\nJan,100\nFeb,110\nMar,99\nApr,120",   'expected_output' => "Feb: 10.00%\nMar: -10.00%\nApr: 21.21%",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA,50\nB,100",                           'expected_output' => "B: 100.00%",                               'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,100\nB,50\nC,100",                  'expected_output' => "B: -50.00%\nC: 100.00%",                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nA,200\nB,200",                         'expected_output' => "B: 0.00%",                                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: k-NN classifier ──────────────────────────────────────────
        $seed(33, [
            ['input' => "4\n1 1 A\n2 2 A\n8 8 B\n9 9 B\n1\n2\n3 3\n7 7",   'expected_output' => "A\nB",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0 X\n10 10 Y\n1\n1\n5 5",                     'expected_output' => "X",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0 0 A\n1 0 A\n10 0 B\n1\n1\n0 0",              'expected_output' => "A",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 0 A\n2 0 A\n8 0 B\n9 0 B\n3\n1\n5 0",       'expected_output' => "A",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: simple linear regression ────────────────────────────────
        $seed(34, [
            ['input' => "3\n1 2\n2 4\n3 6\n2\n0\n4",       'expected_output' => "0.0\n8.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0\n1 1\n1\n5",               'expected_output' => "5.0",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 1\n2 2\n3 3\n1\n10",         'expected_output' => "10.0",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 3\n2 2\n3 1\n1\n4",          'expected_output' => "0.0",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: classification metrics ───────────────────────────────────
        $seed(35, [
            ['input' => "5\n1 1\n0 0\n1 0\n0 1\n1 1",   'expected_output' => "accuracy: 0.6\nprecision: 0.6667\nrecall: 0.6667\nf1: 0.6667",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 1\n1 1\n0 0\n0 0",        'expected_output' => "accuracy: 1.0\nprecision: 1.0\nrecall: 1.0\nf1: 1.0",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 0\n1 0\n0 1\n0 1",        'expected_output' => "accuracy: 0.0\nprecision: 0.0\nrecall: 0.0\nf1: 0.0",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 1\n0 0",                  'expected_output' => "accuracy: 1.0\nprecision: 1.0\nrecall: 1.0\nf1: 1.0",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Naive Bayes ─────────────────────────────────────────────
        $seed(36, [
            ['input' => "4\n1 0 A\n1 1 A\n0 1 B\n0 0 B\n2\n1 0\n0 1",    'expected_output' => "A\nB",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 A\n0 B\n2\n1\n0",                            'expected_output' => "A\nB",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1 P\n1 0 P\n0 1 N\n0 0 N\n1\n1 1",         'expected_output' => "P",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 1 Y\n0 0 N\n1\n0 0",                        'expected_output' => "N",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: R2 + RMSE ────────────────────────────────────────────────
        $seed(37, [
            ['input' => "4\n3 2.5\n8 7.5\n10 10.5\n17 17.5",   'expected_output' => "r2: 0.9966\nrmse: 0.5",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1\n2 2\n3 3",                     'expected_output' => "r2: 1.0\nrmse: 0.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2\n2 2\n3 2",                     'expected_output' => "r2: 0.0\nrmse: 0.8165",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0 1\n2 1",                          'expected_output' => "r2: -1.0\nrmse: 1.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: one K-Means iteration ────────────────────────────────────
        $seed(38, [
            ['input' => "4\n1 1\n1 2\n8 1\n8 2\n2\n1 1\n8 1",    'expected_output' => "1.0 1.5\n8.0 1.5",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0\n10 10\n2\n0 0\n10 10",          'expected_output' => "0.0 0.0\n10.0 10.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 0\n2 0\n9 0\n10 0\n2\n0 0\n10 0", 'expected_output' => "1.5 0.0\n9.5 0.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 0\n5 0\n9 0\n3\n0 0\n5 0\n10 0",  'expected_output' => "1.0 0.0\n5.0 0.0\n9.0 0.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q39: silhouette score ─────────────────────────────────────────
        $seed(39, [
            ['input' => "4\n1 0\n2 0\n8 1\n9 1",    'expected_output' => "0.8571",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0\n10 1",             'expected_output' => "0.0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 0\n1 0\n10 1\n11 1", 'expected_output' => "0.8889",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n5 0\n5 0",             'expected_output' => "0.0",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: first principal component ────────────────────────────────
        $seed(40, [
            ['input' => "3 2\n2 0\n0 2\n4 4",   'expected_output' => "0.7071 0.7071\n1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 0\n2 0\n3 0",   'expected_output' => "1.0 0.0\n1.0",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n0 1\n0 -1",       'expected_output' => "0.0 1.0\n1.0",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 2\n1 1\n2 2\n3 3",   'expected_output' => "0.7071 0.7071\n1.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: WCSS + BCSS ──────────────────────────────────────────────
        $seed(41, [
            ['input' => "4\n1 0\n2 0\n8 1\n9 1",     'expected_output' => "wcss: 1.0\nbcss: 24.5",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5 0\n5 0",               'expected_output' => "wcss: 0.0\nbcss: 0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 0\n2 0\n8 1\n10 1",   'expected_output' => "wcss: 2.0\nbcss: 24.5",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0 0\n0 0\n10 1",        'expected_output' => "wcss: 0.0\nbcss: 22.2222",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: EWMA ────────────────────────────────────────────────────
        $seed(42, [
            ['input' => "4\n10\n20\n30\n40\n0.5",   'expected_output' => "10.0\n15.0\n22.5\n31.25",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5\n1.0",          'expected_output' => "5.0\n5.0\n5.0",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0\n10\n0\n0.5",         'expected_output' => "0.0\n5.0\n2.5",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n100\n0\n0.1",           'expected_output' => "100.0\n90.0",                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: time series decomposition ────────────────────────────────
        $seed(43, [
            ['input' => "6\n2 4 6 8 10 12\n2",   'expected_output' => "trend: NA 5.0 7.0 9.0 11.0 NA\nseasonal: 0 -3.0 1 3.0\nresidual: NA 0.0 0.0 0.0 0.0 NA",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2 3 4\n2",         'expected_output' => "trend: NA 2.5 3.5 NA\nseasonal: 0 -1.5 1 1.5\nresidual: NA 0.0 0.0 NA",                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n3 3 3 3 3 3\n3",     'expected_output' => "trend: NA NA 3.0 3.0 NA NA\nseasonal: 0 0.0 1 0.0 2 0.0\nresidual: NA NA 0.0 0.0 NA NA",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10 20 30 40\n4",     'expected_output' => "trend: NA NA 25.0 NA\nseasonal: 0 -15.0 1 -5.0 2 5.0 3 15.0\nresidual: NA NA 0.0 NA",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: lag features ─────────────────────────────────────────────
        $seed(44, [
            ['input' => "5 10 15 20 25\n2",   'expected_output' => "15.0 10.0 5.0\n20.0 15.0 10.0\n25.0 20.0 15.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3\n1",           'expected_output' => "2.0 1.0\n3.0 2.0",                                 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10 20 30 40\n3",    'expected_output' => "40.0 30.0 20.0 10.0",                              'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 4 3 2 1\n1",      'expected_output' => "4.0 5.0\n3.0 4.0\n2.0 3.0\n1.0 2.0",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: autocorrelation ──────────────────────────────────────────
        $seed(45, [
            ['input' => "5 10 15 20 25\n1",   'expected_output' => "0.8",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3 4 5\n0",       'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 -1 1 -1\n1",       'expected_output' => "-1.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 1 1 1 1\n2",       'expected_output' => "0.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: TF-IDF ──────────────────────────────────────────────────
        $seed(46, [
            ['input' => "2\nthe cat sat on the mat\nthe dog ran\nthe",    'expected_output' => "0.3948\n0.3948",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\ndog dog dog\ncat cat\ndog",                   'expected_output' => "0.7726\n0.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\na b c\na b\na\na",                           'expected_output' => "0.1438\n0.2157\n0.5754",  'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\nhello world\nhello",                         'expected_output' => "0.8109",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: text cleaning + stemming ─────────────────────────────────
        $seed(47, [
            ['input' => "The cats are running quickly and jumped happily\nthe are and",   'expected_output' => "cat run quick jump happily",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "I love coding\nI",                                               'expected_output' => "love cod",                      'is_hidden' => false, 'order_index' => 2],
            ['input' => "talking walked slowly\n",                                         'expected_output' => "talk walk slow",                'is_hidden' => true,  'order_index' => 3],
            ['input' => "the quick brown fox\nthe",                                       'expected_output' => "quick brown fox",               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: cosine similarity docs ───────────────────────────────────
        $seed(48, [
            ['input' => "the cat sat on the mat\nthe dog sat on the log",   'expected_output' => "0.6667",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "hello world\nhello world",                          'expected_output' => "1.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "apple banana\norange mango",                        'expected_output' => "0.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "a b c\na b d",                                     'expected_output' => "0.6667",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: bigram language model ────────────────────────────────────
        $seed(49, [
            ['input' => "2\nI love data science\nI love python\n2\nI love\nlove data",   'expected_output' => "1.0\n0.5",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\na b c\n1\na b",                                               'expected_output' => "1.0",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\na b\na c\n2\na b\na c",                                     'expected_output' => "0.5\n0.5",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nfoo bar\n1\nbaz qux",                                        'expected_output' => "0.0",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: NB sentiment ─────────────────────────────────────────────
        $seed(50, [
            ['input' => "4\npos|great movie loved it\npos|excellent film amazing\nneg|terrible waste of time\nneg|awful boring film\n2\nloved the film\nawful movie",   'expected_output' => "pos\nneg",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\npos|good happy\nneg|bad sad\n2\ngood job\nbad day",                                                                                         'expected_output' => "pos\nneg",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\npos|amazing wonderful\nneg|horrible terrible\n1\namazing day",                                                                               'expected_output' => "pos",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\npos|love\nneg|hate\n1\nunknown word",                                                                                                       'expected_output' => "pos",        'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 3 Coding (University Student) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}