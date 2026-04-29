<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 3 — Introduction to Data Science (Intermediate / Level 2) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Intermediate tier
 *   2. coding_questions    — 50 questions covering applied Data Science concepts
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mapped to lessons 293–303):
 *   L1.1  What is Data Science? — pipeline design & data profiling
 *   L1.2  NumPy — matrix ops, broadcasting, linear algebra
 *   L1.3  Pandas — multi-column ops, merging, pivot-style aggregation
 *   L1.4  Visualization — statistical summaries, binning, histograms
 *   L1.5  Statistics — hypothesis testing concepts, distributions
 *   L1.6  Feature Engineering — polynomial features, interaction terms
 *   L1.7  EDA — correlation matrices, missing-value imputation
 *   L1.8  Machine Learning — linear regression, precision/recall/F1
 *   L1.9  Unsupervised — K-Means iterations, silhouette-style scoring
 *   L1.10 Time Series — exponential smoothing, trend detection
 *   L1.11 NLP — TF-IDF, cosine similarity, n-grams
 *
 * Difficulty: Intermediate — pure Python; multi-step algorithms that mirror
 * what NumPy / Pandas / scikit-learn do under the hood.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module3CodingChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (! $category) {
            $this->command->error('Intermediate category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 3 — Introduction to Data Science (Intermediate) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Introduction to Data Science',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply core Data Science techniques from scratch in pure Python. Implement matrix operations, feature engineering, regression, clustering iterations, time-series smoothing, and TF-IDF — building the intuition behind the libraries that power modern ML.',
                'time_limit_seconds' => 1500,
                'base_xp'            => 1500,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Data Profiling (Q1–Q4)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `col1,col2,col3`). Print the **number of duplicate rows** (rows that appear more than once).

Example:
```
Input:
5
1,2,3
4,5,6
1,2,3
7,8,9
4,5,6
Output: 2
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().strip() for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,value`). Some values are `NA`. **Impute** missing values with the **median** of the non-missing values. Print all values (original or imputed) rounded to 2 decimal places, one per line.

Example:
```
Input:
5
Alice,10
Bob,NA
Carol,20
Dave,NA
Eve,30
Output:
10.00
20.00
20.00
20.00
30.00
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,value`). Print a **data profile**:
```
count: <n>
missing: <count of NA>
mean: <mean of non-NA values, 2dp>
std: <population std of non-NA values, 2dp>
min: <min, 2dp>
max: <max, 2dp>
```

Example:
```
Input:
4
A,10
B,NA
C,20
D,30
Output:
count: 4
missing: 1
mean: 20.00
std: 8.16
min: 10.00
max: 30.00
```
MD,
                'starter_code'        => "import math\nn = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `name,category,value`). Print the **count, mean, and std** of `value` for each category, sorted alphabetically. All floats rounded to 2 decimal places.

Format per line: `category: count=C mean=M std=S`

Example:
```
Input:
6
A,X,10
B,Y,20
C,X,30
D,Y,40
E,X,50
F,Y,60
Output:
X: count=3 mean=30.00 std=16.33
Y: count=3 mean=40.00 std=16.33
```
MD,
                'starter_code'        => "import math\nn = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: NumPy-style Matrix Operations (Q5–Q9)
            // ═══════════════════════════════════════════════════════════════

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read an `n×m` matrix (first line: `n m`, then n rows of m space-separated floats). Print the **column means**, space-separated, rounded to 2 decimal places.

Example:
```
Input:
2 3
1 2 3
4 5 6
Output: 2.50 3.50 4.50
```
MD,
                'starter_code'        => "n, m = map(int, input().split())\nmatrix = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Read two matrices A (`n×m`) and B (`m×p`) and print their **matrix product** C = A × B. Each row of C on its own line, values space-separated, rounded to 2 decimal places.

Input format: first line `n m p`, then n rows for A, then m rows for B.

Example:
```
Input:
2 2 2
1 2
3 4
5 6
7 8
Output:
19.00 22.00
43.00 50.00
```
MD,
                'starter_code'        => "n, m, p = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(n)]\nB = [list(map(float, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Read an `n×n` matrix and print its **transpose**, each row on its own line, values space-separated.

Example:
```
Input:
3 3
1 2 3
4 5 6
7 8 9
Output:
1 4 7
2 5 8
3 6 9
```
MD,
                'starter_code'        => "n, m = map(int, input().split())\nmatrix = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Read an `n×m` matrix. **Standardize** each column (zero mean, unit variance — population std). Print the standardized matrix, each row on its own line, values space-separated and rounded to 2 decimal places. If a column has zero std, print `0.00` for all values in that column.

Example:
```
Input:
3 2
2 10
4 20
6 30
Output:
-1.00 -1.00
0.00 0.00
1.00 1.00
```
MD,
                'starter_code'        => "import math\nn, m = map(int, input().split())\nmatrix = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read an `n×m` matrix and print the **covariance matrix** (population covariance between each pair of columns). Output is `m×m`, values rounded to 2 decimal places, rows space-separated.

cov(X, Y) = mean((X - mean_X) * (Y - mean_Y))

Example:
```
Input:
3 2
1 2
2 4
3 6
Output:
0.67 1.33
1.33 2.67
```
MD,
                'starter_code'        => "n, m = map(int, input().split())\nmatrix = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Pandas-style Multi-column Operations (Q10–Q14)
            // ═══════════════════════════════════════════════════════════════

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read `n` rows of CSV (format: `name,dept,salary`). Print the **top earner per department**, sorted alphabetically by department. Format: `dept: name (salary)`

Example:
```
Input:
4
Alice,Eng,90000
Bob,HR,60000
Carol,Eng,95000
Dave,HR,62000
Output:
Eng: Carol (95000)
HR: Dave (62000)
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Read two tables separated by a blank line. Each table has `n` rows of CSV (format: `id,value`). **Inner join** on `id` and print `id,val_A,val_B` for matching rows, sorted by `id`.

Example:
```
Input:
3
1,10
2,20
3,30

2
2,200
3,300
Output:
2,20,200
3,30,300
```
MD,
                'starter_code'        => "import sys\ndata = sys.stdin.read().split('\\n')\n# Parse two tables separated by blank line and inner join\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Read `n` rows of CSV (format: `date,category,amount`). Produce a **pivot table**: rows = unique dates (sorted), columns = unique categories (sorted alphabetically), values = sum of amounts. Missing combos = 0. Print as CSV with a header row.

Example:
```
Input:
4
2024-01,A,10
2024-01,B,20
2024-02,A,30
2024-02,A,10
Output:
date,A,B
2024-01,10,20
2024-02,40,0
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Read `n` rows of CSV (format: `name,score1,score2`). Print a new CSV with an added column `total` (score1 + score2) and `grade` (A ≥ 90, B ≥ 80, C ≥ 70, D ≥ 60, F otherwise). Include header.

Example:
```
Input:
2
Alice,45,50
Bob,30,40
Output:
name,score1,score2,total,grade
Alice,45,50,95,A
Bob,30,40,70,C
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Read `n` rows of CSV (format: `id,value`). Some values are `NA`. Fill `NA` values using **forward fill** (replace with the last seen non-NA value). If the first row is NA, leave it as `NA`. Print all values one per line.

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
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Statistical Summaries & Distributions (Q15–Q18)
            // ═══════════════════════════════════════════════════════════════

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Read `n` numbers. Print the **five-number summary**: min, Q1, median, Q3, max — each on its own line, rounded to 2 decimal places. Use the exclusive median method for quartiles.

Example:
```
Input:
7
1
3
5
7
9
11
13
Output:
min: 1.00
Q1: 3.00
median: 7.00
Q3: 11.00
max: 13.00
```
MD,
                'starter_code'        => "n = int(input())\nnums = sorted([float(input()) for _ in range(n)])\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Read `n` numbers and `k` bins. Print a **histogram**: divide the range [min, max] into k equal-width bins and count how many values fall in each bin. Values equal to max go in the last bin. Print `bin_start-bin_end: count` for each bin, rounded to 2 decimal places for boundaries.

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
1.00-2.67: 2
2.67-4.33: 2
4.33-6.00: 2
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Read `n` numbers. Print the **skewness** (Fisher's moment coefficient) rounded to 2 decimal places.

Formula: skewness = (1/n) × Σ((xᵢ − mean)³) / std³

If std = 0, print `0.00`.

Example:
```
Input:
5
1
2
3
4
100
Output: 2.23
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Read `n` numbers. Print the **kurtosis** (excess kurtosis / Fisher's definition) rounded to 2 decimal places.

Formula: kurtosis = (1/n) × Σ((xᵢ − mean)⁴) / std⁴ − 3

If std = 0, print `0.00`.

Example:
```
Input:
5
2
2
3
4
4
Output: -1.30
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Statistics — Hypothesis & Correlation (Q19–Q23)
            // ═══════════════════════════════════════════════════════════════

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Read two samples A and B (`nA` values then `nB` values). Compute the **two-sample t-statistic** (equal variances, pooled):

t = (mean_A − mean_B) / (sp × sqrt(1/nA + 1/nB))

where sp = sqrt(((nA-1)×var_A + (nB-1)×var_B) / (nA + nB - 2)) and var is sample variance (ddof=1).

Print t rounded to 2 decimal places.

Example:
```
Input:
3
2
4
6
3
1
3
5
Output: 1.00
```
MD,
                'starter_code'        => "import math\nnA = int(input())\nA = [float(input()) for _ in range(nA)]\nnB = int(input())\nB = [float(input()) for _ in range(nB)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Read `n` pairs `(x, y)`. Compute the **Spearman rank correlation** rounded to 2 decimal places.

Formula: rs = 1 − (6 × Σdᵢ²) / (n × (n² − 1))

where dᵢ = rank(xᵢ) − rank(yᵢ). Ranks start at 1. Handle ties by averaging ranks.

Example:
```
Input:
4
1 1
2 2
3 3
4 4
Output: 1.00
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Read `n` observed and expected values (format: `observed expected` per line). Compute the **chi-squared statistic** rounded to 2 decimal places.

χ² = Σ((observed − expected)² / expected)

Example:
```
Input:
3
10 12
20 18
30 30
Output: 0.56
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Read `n` numbers sampled from a normal distribution (given mean `mu` and std `sigma` on the last two lines). Compute the **z-score** for each and print the **two-tailed p-value approximation** for each using:

p ≈ 2 × (1 − Φ(|z|))

where Φ(z) ≈ 0.5 × erfc(−z / sqrt(2)) using `math.erfc`.

Print each p-value rounded to 4 decimal places, one per line.

Example:
```
Input:
3
100
110
90
100
10
Output:
1.0000
0.3173
0.3173
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnums = [float(input()) for _ in range(n)]\nmu = float(input())\nsigma = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Read `n` pairs `(x, y)`. Print the full **correlation matrix** for x and y (2×2), values rounded to 2 decimal places, rows space-separated. The diagonal is always 1.00.

Example:
```
Input:
3
1 2
2 4
3 6
Output:
1.00 1.00
1.00 1.00
```
MD,
                'starter_code'        => "import math\nn = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
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
Read `n` rows of two features (format: `x1 x2` per line). Generate **polynomial features** of degree 2: [1, x1, x2, x1², x1·x2, x2²]. Print each row's feature vector, values rounded to 2 decimal places, space-separated.

Example:
```
Input:
2
2 3
1 4
Output:
1.00 2.00 3.00 4.00 6.00 9.00
1.00 1.00 4.00 1.00 4.00 16.00
```
MD,
                'starter_code'        => "n = int(input())\nrows = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Read `n` rows of features (format: `x1 x2 ... xm` per line). Apply **L2 normalization** (divide each row by its Euclidean norm). Print normalized rows, values rounded to 4 decimal places, space-separated. If norm = 0, print zeros.

Example:
```
Input:
2
3 4
1 0
Output:
0.6000 0.8000
1.0000 0.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nrows = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Read `n` category labels. Apply **label encoding** (assign integer codes 0, 1, 2... in alphabetical order). Print the encoded value for each label, one per line.

Example:
```
Input:
4
cat
dog
bird
cat
Output:
1
2
0
1
```
MD,
                'starter_code'        => "n = int(input())\nlabels = [input().strip() for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Read `n` rows of two features (format: `x1 x2` per line). Create an **interaction feature** x1×x2 and apply **robust scaling** to it: scaled = (x − median) / IQR. Print scaled values rounded to 2 decimal places, one per line. If IQR = 0, print `0.00` for all.

Example:
```
Input:
4
1 2
2 3
3 4
4 5
Output:
-1.00
-0.33
0.33
1.00
```
MD,
                'starter_code'        => "n = int(input())\nrows = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Read `n` numbers. Apply **quantile binning** into `k` equal-frequency bins (given on the last line). Print the bin index (0-based) for each number, one per line.

Assign bin based on rank: bin = floor(rank / n × k), capped at k-1.

Example:
```
Input:
6
5
10
15
20
25
30
3
Output:
0
0
1
1
2
2
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: EDA — Correlation & Imputation (Q29–Q32)
            // ═══════════════════════════════════════════════════════════════

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Read an `n×m` matrix (first line: `n m`, then n rows). Print the **m×m Pearson correlation matrix**, values rounded to 2 decimal places, rows space-separated.

Example:
```
Input:
3 2
1 2
2 4
3 6
Output:
1.00 1.00
1.00 1.00
```
MD,
                'starter_code'        => "import math\nn, m = map(int, input().split())\nmatrix = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Read `n` rows of CSV (format: `name,value`). Some values are `NA`. Impute using **mean imputation**. Then detect outliers using the **IQR method** (1.5×IQR rule) on the imputed dataset. Print outlier names, one per line in original order. If none, print `None`.

Example:
```
Input:
7
A,1
B,2
C,NA
D,4
E,5
F,6
G,100
Output: G
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Read `n` rows of CSV (format: `name,x,y`). Print the **top 3 most correlated pairs** from all pairs of features among x and y columns extended: compute Pearson r between each of: (x,y), (x,x), (y,y). Print the pair name and r value sorted by |r| descending. Format: `pair: r`

Actually, for this problem: read `n` rows with `k` numeric columns (first line: `n k`, then n rows). Compute all C(k,2) Pearson correlations. Print them sorted by |r| descending, format: `col_i-col_j: r` (0-indexed), rounded to 2 decimal places.

Example:
```
Input:
3 3
1 2 3
2 4 6
3 6 9
Output:
0-1: 1.00
0-2: 1.00
1-2: 1.00
```
MD,
                'starter_code'        => "import math\nn, k = map(int, input().split())\nmatrix = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Read `n` rows of CSV (format: `name,value`). Some values are `NA`. Use **KNN imputation** (k=1): replace each NA with the value of its nearest non-NA neighbour by index distance. Print all values (original or imputed), one per line.

Example:
```
Input:
5
A,10
B,NA
C,NA
D,40
E,50
Output:
10
10
40
40
50
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Machine Learning (Q33–Q37)
            // ═══════════════════════════════════════════════════════════════

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Implement **simple linear regression** using the closed-form formula. Read `n` pairs `(x, y)`. Print the slope `m` and intercept `b`, each rounded to 4 decimal places.

m = Σ((xᵢ − x̄)(yᵢ − ȳ)) / Σ((xᵢ − x̄)²)
b = ȳ − m × x̄

Example:
```
Input:
4
1 2
2 4
3 5
4 4
Output:
m: 0.8000
b: 1.0000
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Read `n` pairs `(x, y_actual)` and then a test `x` value. Using simple linear regression (same formula as above), print the **predicted y** for the test point, rounded to 2 decimal places.

Example:
```
Input:
3
1 1
2 2
3 3
5
Output: 5.00
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\nx_test = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Read `n` pairs `(actual, predicted)` (binary 0/1). Print:
```
precision: <value>
recall: <value>
f1: <value>
```
All rounded to 2 decimal places. If undefined (division by zero), print `0.00`.

precision = TP / (TP + FP)
recall = TP / (TP + FN)
F1 = 2 × precision × recall / (precision + recall)

Example:
```
Input:
4
1 1
0 0
1 0
0 1
Output:
precision: 0.50
recall: 0.50
f1: 0.50
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Read `n` `(actual, predicted)` pairs of real numbers. Print the **R² score** (coefficient of determination) rounded to 4 decimal places.

R² = 1 − SS_res / SS_tot
SS_res = Σ(actual − predicted)²
SS_tot = Σ(actual − mean_actual)²

If SS_tot = 0, print `1.0000`.

Example:
```
Input:
3
1 1
2 2
3 3
Output: 1.0000
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Implement **gradient descent** for simple linear regression. Read `n` training pairs `(x, y)`, learning rate `alpha`, and number of iterations `iters` (last two lines). Starting from m=0, b=0, run gradient descent and print the final `m` and `b` rounded to 4 decimal places.

Gradients:
∂L/∂m = -(2/n) × Σ(xᵢ × (yᵢ − (m×xᵢ + b)))
∂L/∂b = -(2/n) × Σ(yᵢ − (m×xᵢ + b))

Example:
```
Input:
3
1 1
2 2
3 3
0.01
1000
Output:
m: 1.0000
b: 0.0000
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\nalpha = float(input())\niters = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Unsupervised Learning (Q38–Q41)
            // ═══════════════════════════════════════════════════════════════

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Implement **one iteration of K-Means** for 1D data. Read `n` points, then `k` initial centers (space-separated). Assign each point to its nearest center, recompute centroids, and print the new centers rounded to 2 decimal places, space-separated.

Example:
```
Input:
6
1
2
3
7
8
9
2
2 8
Output: 2.00 8.00
```
MD,
                'starter_code'        => "n = int(input())\npoints = [float(input()) for _ in range(n)]\ncenters = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Implement **K-Means until convergence** for 1D data. Read `n` points, then `k` initial centers. Iterate until centers do not change (rounded to 6 decimal places). Print the final cluster assignment for each point (0-indexed cluster id), one per line.

Example:
```
Input:
6
1
2
3
7
8
9
2
2 8
Output:
0
0
0
1
1
1
```
MD,
                'starter_code'        => "n = int(input())\npoints = [float(input()) for _ in range(n)]\ncenters = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Compute the **silhouette score** for a 1D clustered dataset. Read `n` rows of `point cluster` pairs. For each point compute:

a(i) = mean distance to other points in the same cluster
b(i) = mean distance to all points in the nearest other cluster
s(i) = (b(i) − a(i)) / max(a(i), b(i))

Print the mean silhouette score rounded to 4 decimal places.

Example:
```
Input:
6
1 0
2 0
3 0
7 1
8 1
9 1
Output: 0.8056
```
MD,
                'starter_code'        => "n = int(input())\ndata = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 350,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Implement **PCA dimension reduction** for 2D→1D. Read `n` 2D points. Center the data, compute the first principal component (the eigenvector of the covariance matrix with the larger eigenvalue), and print the projected 1D coordinates rounded to 4 decimal places, one per line.

For a 2×2 covariance matrix, the larger eigenvalue and its eigenvector can be found analytically.

Example:
```
Input:
3
1 2
2 4
3 6
Output:
-2.2361
0.0000
2.2361
```
MD,
                'starter_code'        => "import math\nn = int(input())\npoints = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Time Series (Q42–Q45)
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Read `n` time-series values and a smoothing factor `alpha` (last line, 0 < alpha ≤ 1). Apply **exponential smoothing**:

S₁ = x₁
Sₜ = alpha × xₜ + (1 − alpha) × Sₜ₋₁

Print each smoothed value rounded to 2 decimal places, one per line.

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
10.00
15.00
22.50
31.25
```
MD,
                'starter_code'        => "n = int(input())\nseries = [float(input()) for _ in range(n)]\nalpha = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Read `n` time-series values. Compute and print the **trend** using a linear regression of value on time index (1-based). Print predicted value for each time step rounded to 2 decimal places, one per line.

Example:
```
Input:
4
2
4
6
8
Output:
2.00
4.00
6.00
8.00
```
MD,
                'starter_code'        => "n = int(input())\nseries = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Read `n` time-series values and a period `p`. Compute the **seasonal indices** using the ratio-to-moving-average method:

1. Compute centered moving average of window `p`
2. Divide each value by its CMA to get raw seasonals
3. Average raw seasonals by position within period

Print `p` seasonal indices rounded to 4 decimal places, one per line.

Example:
```
Input:
8
10
20
10
20
10
20
10
20
2
Output:
0.5000
1.5000
```
MD,
                'starter_code'        => "n = int(input())\nseries = [float(input()) for _ in range(n)]\np = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 400,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Read `n` time-series values. Print the **autocorrelation** at lag 1 rounded to 4 decimal places.

ACF(1) = Σ((xₜ − x̄)(xₜ₋₁ − x̄)) / Σ((xₜ − x̄)²)  for t = 2..n

Example:
```
Input:
5
1
2
3
4
5
Output: 0.4000
```
MD,
                'starter_code'        => "n = int(input())\nseries = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11: NLP — TF-IDF & Similarity (Q46–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Read `n` documents (one per line). Compute **IDF** for each unique word:

IDF(w) = ln(n / df(w))

where df(w) = number of documents containing w. Print each word and its IDF rounded to 4 decimal places, sorted alphabetically. Format: `word: idf`

Example:
```
Input:
3
the cat sat
the dog ran
the bird flew
Output:
bird: 1.0986
cat: 1.0986
dog: 1.0986
flew: 1.0986
ran: 1.0986
sat: 1.0986
the: 0.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndocs = [input().lower().split() for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Read `n` documents. Compute the **TF-IDF vector** for a query word (last line). Print TF-IDF for each document rounded to 4 decimal places, one per line.

TF-IDF(d, w) = TF(d, w) × IDF(w)
TF(d, w) = count(w in d) / len(d)
IDF(w) = ln(n / df(w))

Example:
```
Input:
3
the cat sat on the mat
the dog ran away
the bird flew high
the
Output:
0.0000
0.0000
0.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndocs = [input().lower().split() for _ in range(n)]\nword = input().strip().lower()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Read two documents (one per line). Compute their **cosine similarity** using raw term frequency vectors over the union vocabulary. Print the result rounded to 4 decimal places.

cosine(A, B) = (A · B) / (||A|| × ||B||)

Example:
```
Input:
the cat sat on the mat
the cat sat on the log
Output: 0.9231
```
MD,
                'starter_code'        => "import math\ndoc1 = input().lower().split()\ndoc2 = input().lower().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Read a document and an integer `n`. Generate all **n-grams** (contiguous sequences of n tokens) and print them sorted by frequency descending, ties broken alphabetically. Format: `ngram: count`. Print at most top 5.

Example:
```
Input:
the cat sat on the mat
2
Output:
('cat', 'sat'): 1
('mat',): 1
```

Wait — print bigrams sorted by frequency desc, ties alphabetically. Format: `word1 word2: count`

Example:
```
Input:
the cat sat on the mat
2
Output:
cat sat: 1
on the: 1
sat on: 1
the cat: 1
the mat: 1
```
MD,
                'starter_code'        => "sentence = input().lower().split()\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Read `n` documents and a query document (last line). Rank all `n` documents by **cosine similarity** (TF vectors) to the query. Print each document index (0-based) and its cosine similarity rounded to 4 decimal places, in descending order of similarity. Format: `idx: score`

Example:
```
Input:
3
data science is great
machine learning is fun
python is great for data
data science python
Output:
2: 0.7454
0: 0.5774
1: 0.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndocs = [input().lower().split() for _ in range(n)]\nquery = input().lower().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 350,
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

        // ── Q1: Duplicate rows ────────────────────────────────────────────
        $seed(1, [
            ['input' => "5\n1,2,3\n4,5,6\n1,2,3\n7,8,9\n4,5,6",        'expected_output' => '2',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\na,b,c\nd,e,f\na,b,c",                        'expected_output' => '1',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1,1\n2,2\n3,3",                              'expected_output' => '0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nx,y\nx,y\nx,y\nx,y",                         'expected_output' => '3',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Median imputation ─────────────────────────────────────────
        $seed(2, [
            ['input' => "5\nAlice,10\nBob,NA\nCarol,20\nDave,NA\nEve,30",  'expected_output' => "10.00\n20.00\n20.00\n20.00\n30.00",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nA,5\nB,NA\nC,15",                              'expected_output' => "5.00\n10.00\n15.00",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,NA\nB,NA\nC,100",                            'expected_output' => "100.00\n100.00\n100.00",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nA,2\nB,4\nC,NA\nD,8",                         'expected_output' => "2.00\n4.00\n4.00\n8.00",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Data profile ──────────────────────────────────────────────
        $seed(3, [
            ['input' => "4\nA,10\nB,NA\nC,20\nD,30",  'expected_output' => "count: 4\nmissing: 1\nmean: 20.00\nstd: 8.16\nmin: 10.00\nmax: 30.00",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nX,50\nY,50",              'expected_output' => "count: 2\nmissing: 0\nmean: 50.00\nstd: 0.00\nmin: 50.00\nmax: 50.00",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,NA\nB,NA\nC,10",        'expected_output' => "count: 3\nmissing: 2\nmean: 10.00\nstd: 0.00\nmin: 10.00\nmax: 10.00",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nA,0\nB,10\nC,20",         'expected_output' => "count: 3\nmissing: 0\nmean: 10.00\nstd: 8.16\nmin: 0.00\nmax: 20.00",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: GroupBy stats ─────────────────────────────────────────────
        $seed(4, [
            ['input' => "6\nA,X,10\nB,Y,20\nC,X,30\nD,Y,40\nE,X,50\nF,Y,60",  'expected_output' => "X: count=3 mean=30.00 std=16.33\nY: count=3 mean=40.00 std=16.33",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA,G1,100\nB,G1,200",                               'expected_output' => "G1: count=2 mean=150.00 std=50.00",                                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,X,5\nB,Y,10\nC,X,15",                           'expected_output' => "X: count=2 mean=10.00 std=5.00\nY: count=1 mean=10.00 std=0.00",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nZ,G,42",                                            'expected_output' => "G: count=1 mean=42.00 std=0.00",                                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Column means ──────────────────────────────────────────────
        $seed(5, [
            ['input' => "2 3\n1 2 3\n4 5 6",          'expected_output' => '2.50 3.50 4.50',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 4\n2 5\n3 6",         'expected_output' => '2.00 5.00',         'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 4\n10 20 30 40",           'expected_output' => '10.00 20.00 30.00 40.00', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 2\n0 0\n10 20",            'expected_output' => '5.00 10.00',        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Matrix product ────────────────────────────────────────────
        $seed(6, [
            ['input' => "2 2 2\n1 2\n3 4\n5 6\n7 8",   'expected_output' => "19.00 22.00\n43.00 50.00",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 1\n1 2\n3 0",             'expected_output' => "3.00",                       'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3 2\n1 0 1\n0 1 0\n1 0\n0 1\n1 0", 'expected_output' => "2.00 0.00\n0.00 1.00", 'is_hidden' => true,'order_index' => 3],
            ['input' => "1 1 1\n5\n3",                 'expected_output' => "15.00",                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Transpose ─────────────────────────────────────────────────
        $seed(7, [
            ['input' => "3 3\n1 2 3\n4 5 6\n7 8 9",   'expected_output' => "1 4 7\n2 5 8\n3 6 9",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 3\n1 2 3\n4 5 6",          'expected_output' => "1 4\n2 5\n3 6",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 3\n7 8 9",                 'expected_output' => "7\n8\n9",                 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n1 0\n0 1",              'expected_output' => "1 0\n0 1",                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Column standardization ────────────────────────────────────
        $seed(8, [
            ['input' => "3 2\n2 10\n4 20\n6 30",       'expected_output' => "-1.00 -1.00\n0.00 0.00\n1.00 1.00",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n0 0\n4 8",               'expected_output' => "-1.00 -1.00\n1.00 1.00",                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 1\n5\n5\n5",                'expected_output' => "0.00\n0.00\n0.00",                            'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 1\n1\n2\n3\n4",             'expected_output' => "-1.34\n-0.45\n0.45\n1.34",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Covariance matrix ─────────────────────────────────────────
        $seed(9, [
            ['input' => "3 2\n1 2\n2 4\n3 6",          'expected_output' => "0.67 1.33\n1.33 2.67",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 3\n2 2\n3 1",          'expected_output' => "0.67 -0.67\n-0.67 0.67",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n0 0\n2 4",               'expected_output' => "1.00 2.00\n2.00 4.00",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 2\n5 5\n5 5\n5 5",          'expected_output' => "0.00 0.00\n0.00 0.00",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Top earner per dept ──────────────────────────────────────
        $seed(10, [
            ['input' => "4\nAlice,Eng,90000\nBob,HR,60000\nCarol,Eng,95000\nDave,HR,62000",  'expected_output' => "Eng: Carol (95000)\nHR: Dave (62000)",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nX,Sales,50000\nY,Sales,70000",                                   'expected_output' => "Sales: Y (70000)",                       'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,IT,100\nB,IT,200\nC,Finance,300",                              'expected_output' => "Finance: C (300)\nIT: B (200)",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nZ,Ops,999",                                                      'expected_output' => "Ops: Z (999)",                           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Inner join ───────────────────────────────────────────────
        $seed(11, [
            ['input' => "3\n1,10\n2,20\n3,30\n\n2\n2,200\n3,300",    'expected_output' => "2,20,200\n3,30,300",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1,a\n2,b\n\n2\n1,x\n3,z",               'expected_output' => "1,a,x",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1,p\n2,q\n\n2\n3,r\n4,s",               'expected_output' => '',                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1,A\n2,B\n3,C\n\n3\n1,X\n2,Y\n3,Z",    'expected_output' => "1,A,X\n2,B,Y\n3,C,Z",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Pivot table ──────────────────────────────────────────────
        $seed(12, [
            ['input' => "4\n2024-01,A,10\n2024-01,B,20\n2024-02,A,30\n2024-02,A,10",  'expected_output' => "date,A,B\n2024-01,10,20\n2024-02,40,0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2024-01,X,5\n2024-02,X,10",                               'expected_output' => "date,X\n2024-01,5\n2024-02,10",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nJan,A,1\nJan,B,2\nFeb,A,3",                              'expected_output' => "date,A,B\nFeb,3,0\nJan,1,2",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n2024,X,100",                                               'expected_output' => "date,X\n2024,100",                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Derived columns ──────────────────────────────────────────
        $seed(13, [
            ['input' => "2\nAlice,45,50\nBob,30,40",         'expected_output' => "name,score1,score2,total,grade\nAlice,45,50,95,A\nBob,30,40,70,C",             'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\nCarol,25,30",                    'expected_output' => "name,score1,score2,total,grade\nCarol,25,30,55,F",                              'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nX,40,40\nY,35,45",              'expected_output' => "name,score1,score2,total,grade\nX,40,40,80,B\nY,35,45,80,B",                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nA,30,35\nB,28,30",              'expected_output' => "name,score1,score2,total,grade\nA,30,35,65,D\nB,28,30,58,F",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Forward fill ─────────────────────────────────────────────
        $seed(14, [
            ['input' => "5\n1,10\n2,NA\n3,NA\n4,30\n5,NA",   'expected_output' => "10\n10\n10\n30\n30",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1,NA\n2,20\n3,NA",               'expected_output' => "NA\n20\n20",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1,5\n2,NA\n3,NA\n4,NA",         'expected_output' => "5\n5\n5\n5",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1,NA\n2,NA",                     'expected_output' => "NA\nNA",                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Five-number summary ──────────────────────────────────────
        $seed(15, [
            ['input' => "7\n1\n3\n5\n7\n9\n11\n13",    'expected_output' => "min: 1.00\nQ1: 3.00\nmedian: 7.00\nQ3: 11.00\nmax: 13.00",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4",               'expected_output' => "min: 1.00\nQ1: 1.50\nmedian: 2.50\nQ3: 3.50\nmax: 4.00",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n5\n5\n5\n5",            'expected_output' => "min: 5.00\nQ1: 5.00\nmedian: 5.00\nQ3: 5.00\nmax: 5.00",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1\n2\n3\n4\n5\n6",         'expected_output' => "min: 1.00\nQ1: 2.00\nmedian: 3.50\nQ3: 5.00\nmax: 6.00",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Histogram ────────────────────────────────────────────────
        $seed(16, [
            ['input' => "6\n1\n2\n3\n4\n5\n6\n3",     'expected_output' => "1.00-2.67: 2\n2.67-4.33: 2\n4.33-6.00: 2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0\n5\n10\n10\n2",         'expected_output' => "0.00-5.00: 2\n5.00-10.00: 2",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n5\n9\n1",              'expected_output' => "1.00-9.00: 3",                              'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n10\n20\n30\n40\n50\n60\n2",'expected_output' => "10.00-35.00: 3\n35.00-60.00: 3",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Skewness ─────────────────────────────────────────────────
        $seed(17, [
            ['input' => "5\n1\n2\n3\n4\n100",  'expected_output' => '2.23',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",          'expected_output' => '0.00',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5\n5\n5",          'expected_output' => '0.00',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n1\n1\n10",      'expected_output' => '1.73',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Kurtosis ─────────────────────────────────────────────────
        $seed(18, [
            ['input' => "5\n2\n2\n3\n4\n4",    'expected_output' => '-1.30',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5",          'expected_output' => '0.00',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4",       'expected_output' => '-1.36',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1\n1\n3\n5\n5",    'expected_output' => '-1.54',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Two-sample t-statistic ───────────────────────────────────
        $seed(19, [
            ['input' => "3\n2\n4\n6\n3\n1\n3\n5",   'expected_output' => '1.00',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10\n20\n2\n10\n20",      'expected_output' => '0.00',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3\n3\n4\n5\n6",   'expected_output' => '-3.00', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0\n0\n2\n0\n0",          'expected_output' => '0.00',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Spearman rank correlation ────────────────────────────────
        $seed(20, [
            ['input' => "4\n1 1\n2 2\n3 3\n4 4",     'expected_output' => '1.00',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 4\n2 3\n3 2\n4 1",     'expected_output' => '-1.00',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2\n2 1\n3 3",          'expected_output' => '0.50',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 1\n2 3\n3 2\n4 5\n5 4",'expected_output' => '0.90',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Chi-squared statistic ────────────────────────────────────
        $seed(21, [
            ['input' => "3\n10 12\n20 18\n30 30",   'expected_output' => '0.56',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10 10\n20 20",          'expected_output' => '0.00',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5 10\n10 10\n15 10",    'expected_output' => '5.00',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n100 50",                'expected_output' => '50.00', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Z-score p-values ─────────────────────────────────────────
        $seed(22, [
            ['input' => "3\n100\n110\n90\n100\n10",   'expected_output' => "1.0000\n0.3173\n0.3173",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n100\n100\n10",            'expected_output' => "1.0000",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n80\n120\n100\n10",        'expected_output' => "0.0455\n0.0455",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n130\n100\n10",            'expected_output' => "0.0027",                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: 2×2 correlation matrix ──────────────────────────────────
        $seed(23, [
            ['input' => "3\n1 2\n2 4\n3 6",     'expected_output' => "1.00 1.00\n1.00 1.00",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 3\n2 2\n3 1",     'expected_output' => "1.00 -1.00\n-1.00 1.00", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 1\n2 1\n3 1",     'expected_output' => "1.00 0.00\n0.00 1.00",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2\n2 1\n3 3\n4 4",'expected_output' => "1.00 0.80\n0.80 1.00",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Polynomial features ──────────────────────────────────────
        $seed(24, [
            ['input' => "2\n2 3\n1 4",           'expected_output' => "1.00 2.00 3.00 4.00 6.00 9.00\n1.00 1.00 4.00 1.00 4.00 16.00",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1 1",                'expected_output' => "1.00 1.00 1.00 1.00 1.00 1.00",                                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0 0",                'expected_output' => "1.00 0.00 0.00 0.00 0.00 0.00",                                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n3 4",                'expected_output' => "1.00 3.00 4.00 9.00 12.00 16.00",                                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: L2 normalization ─────────────────────────────────────────
        $seed(25, [
            ['input' => "2\n3 4\n1 0",         'expected_output' => "0.6000 0.8000\n1.0000 0.0000",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0 0",              'expected_output' => "0.0000 0.0000",                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 1\n2 2",         'expected_output' => "0.7071 0.7071\n0.7071 0.7071",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5 12",             'expected_output' => "0.3846 0.9231",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Label encoding ───────────────────────────────────────────
        $seed(26, [
            ['input' => "4\ncat\ndog\nbird\ncat",    'expected_output' => "1\n2\n0\n1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nyes\nno\nyes",           'expected_output' => "1\n0\n1",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\na\nb\nc",               'expected_output' => "0\n1\n2",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nz\na",                  'expected_output' => "1\n0",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Robust scaling of interaction feature ────────────────────
        $seed(27, [
            ['input' => "4\n1 2\n2 3\n3 4\n4 5",    'expected_output' => "-1.00\n-0.33\n0.33\n1.00",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1\n2 2\n3 3",         'expected_output' => "-1.00\n0.00\n1.00",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n5 5\n5 5",              'expected_output' => "0.00\n0.00",                 'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0 1\n1 2\n2 3\n3 4",   'expected_output' => "-1.00\n-0.33\n0.33\n1.00",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Quantile binning ─────────────────────────────────────────
        $seed(28, [
            ['input' => "6\n5\n10\n15\n20\n25\n30\n3",   'expected_output' => "0\n0\n1\n1\n2\n2",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n2",              'expected_output' => "0\n0\n1\n1",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3\n3",                 'expected_output' => "0\n1\n2",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n10\n20\n30\n40\n50\n60\n2",  'expected_output' => "0\n0\n0\n1\n1\n1",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Pearson correlation matrix ───────────────────────────────
        $seed(29, [
            ['input' => "3 2\n1 2\n2 4\n3 6",      'expected_output' => "1.00 1.00\n1.00 1.00",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 3\n2 2\n3 1",      'expected_output' => "1.00 -1.00\n-1.00 1.00", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2\n1 1\n1 2\n1 3",      'expected_output' => "1.00 0.00\n0.00 1.00",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 2\n1 2\n2 4\n3 5\n4 7", 'expected_output' => "1.00 0.99\n0.99 1.00",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Mean imputation then IQR outliers ────────────────────────
        $seed(30, [
            ['input' => "7\nA,1\nB,2\nC,NA\nD,4\nE,5\nF,6\nG,100",   'expected_output' => 'G',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nA,1\nB,2\nC,3\nD,4",                      'expected_output' => 'None', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\nA,NA\nB,1\nC,2\nD,3\nE,50",              'expected_output' => 'E',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nA,10\nB,10\nC,10",                        'expected_output' => 'None', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Pairwise correlations ────────────────────────────────────
        $seed(31, [
            ['input' => "3 3\n1 2 3\n2 4 6\n3 6 9",   'expected_output' => "0-1: 1.00\n0-2: 1.00\n1-2: 1.00",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 3\n2 2\n3 1",         'expected_output' => "0-1: -1.00",                          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 3\n1 1 2\n2 2 1\n3 3 3",  'expected_output' => "0-1: 1.00\n0-2: 0.87\n1-2: 0.87",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n1 0\n0 1",             'expected_output' => "0-1: -1.00",                          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: KNN imputation (k=1) ─────────────────────────────────────
        $seed(32, [
            ['input' => "5\nA,10\nB,NA\nC,NA\nD,40\nE,50",     'expected_output' => "10\n10\n40\n40\n50",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nA,NA\nB,20\nC,30",                  'expected_output' => "20\n20\n30",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,5\nB,NA\nC,15",                   'expected_output' => "5\n5\n15",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nA,10\nB,NA\nC,NA\nD,NA",            'expected_output' => "10\n10\n10\n10",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Linear regression (slope & intercept) ────────────────────
        $seed(33, [
            ['input' => "4\n1 2\n2 4\n3 5\n4 4",    'expected_output' => "m: 0.8000\nb: 1.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1\n2 2\n3 3",         'expected_output' => "m: 1.0000\nb: 0.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 0\n1 1",              'expected_output' => "m: 1.0000\nb: 0.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 3\n2 2\n3 1",         'expected_output' => "m: -1.0000\nb: 4.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Linear regression prediction ────────────────────────────
        $seed(34, [
            ['input' => "3\n1 1\n2 2\n3 3\n5",       'expected_output' => '5.00',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 3\n2 2\n3 1\n4",       'expected_output' => '0.00',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 0\n2 4\n3",            'expected_output' => '6.00',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2\n2 4\n3 5\n4 4\n5",  'expected_output' => '5.00',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Precision / Recall / F1 ─────────────────────────────────
        $seed(35, [
            ['input' => "4\n1 1\n0 0\n1 0\n0 1",    'expected_output' => "precision: 0.50\nrecall: 0.50\nf1: 0.50",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1\n1 1\n0 0",         'expected_output' => "precision: 1.00\nrecall: 1.00\nf1: 1.00",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 0\n0 1",              'expected_output' => "precision: 0.00\nrecall: 0.00\nf1: 0.00",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 1\n1 1\n1 0\n0 0",   'expected_output' => "precision: 1.00\nrecall: 0.67\nf1: 0.80",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: R² score ─────────────────────────────────────────────────
        $seed(36, [
            ['input' => "3\n1 1\n2 2\n3 3",          'expected_output' => '1.0000',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2\n2 2\n3 2",          'expected_output' => '0.0000',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1\n2 2\n3 4\n4 3",     'expected_output' => '0.8000',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n5 5\n5 5",               'expected_output' => '1.0000',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Gradient descent ─────────────────────────────────────────
        $seed(37, [
            ['input' => "3\n1 1\n2 2\n3 3\n0.01\n1000",     'expected_output' => "m: 1.0000\nb: 0.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0\n1 2\n0.1\n500",            'expected_output' => "m: 2.0000\nb: 0.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2\n2 4\n3 6\n0.01\n2000",     'expected_output' => "m: 2.0000\nb: 0.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0 0\n1 0\n2 0\n0.1\n100",       'expected_output' => "m: 0.0000\nb: 0.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: One K-Means iteration ────────────────────────────────────
        $seed(38, [
            ['input' => "6\n1\n2\n3\n7\n8\n9\n2\n2 8",     'expected_output' => '2.00 8.00',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0\n1\n9\n10\n2\n0 10",         'expected_output' => '0.50 9.50',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0\n10\n2\n0 10",               'expected_output' => '0.00 10.00',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4\n2\n1 4",           'expected_output' => '1.50 3.50',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: K-Means convergence ──────────────────────────────────────
        $seed(39, [
            ['input' => "6\n1\n2\n3\n7\n8\n9\n2\n2 8",     'expected_output' => "0\n0\n0\n1\n1\n1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0\n1\n9\n10\n2\n0 10",         'expected_output' => "0\n0\n1\n1",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0\n10\n2\n0 10",               'expected_output' => "0\n1",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2\n9\n2\n1 9",              'expected_output' => "0\n0\n1",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Silhouette score ─────────────────────────────────────────
        $seed(40, [
            ['input' => "6\n1 0\n2 0\n3 0\n7 1\n8 1\n9 1",   'expected_output' => '0.8056',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0 0\n1 0\n9 1\n10 1",             'expected_output' => '0.8889',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 0\n10 1",                        'expected_output' => '1.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 0\n2 0\n3 1\n4 1",              'expected_output' => '0.5000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: PCA 2D→1D ────────────────────────────────────────────────
        $seed(41, [
            ['input' => "3\n1 2\n2 4\n3 6",       'expected_output' => "-2.2361\n0.0000\n2.2361",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0\n2 2",            'expected_output' => "-1.4142\n1.4142",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0 0\n1 0\n2 0",       'expected_output' => "-1.0000\n0.0000\n1.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 0\n1 0\n1 0",       'expected_output' => "0.0000\n0.0000\n0.0000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Exponential smoothing ────────────────────────────────────
        $seed(42, [
            ['input' => "4\n10\n20\n30\n40\n0.5",    'expected_output' => "10.00\n15.00\n22.50\n31.25",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n10\n15\n1.0",         'expected_output' => "5.00\n10.00\n15.00",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n100\n0\n100\n0\n0.5",    'expected_output' => "100.00\n50.00\n75.00\n37.50",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n10\n10\n10\n0.3",        'expected_output' => "10.00\n10.00\n10.00",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Trend via linear regression ──────────────────────────────
        $seed(43, [
            ['input' => "4\n2\n4\n6\n8",          'expected_output' => "2.00\n4.00\n6.00\n8.00",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",             'expected_output' => "1.00\n2.00\n3.00",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n3\n3\n3\n3",          'expected_output' => "3.00\n3.00\n3.00\n3.00",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n10\n20\n30",          'expected_output' => "10.00\n20.00\n30.00",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Seasonal indices ─────────────────────────────────────────
        $seed(44, [
            ['input' => "8\n10\n20\n10\n20\n10\n20\n10\n20\n2",   'expected_output' => "0.5000\n1.5000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n3\n1\n3\n2",                        'expected_output' => "0.5000\n1.5000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n10\n10\n10\n10\n10\n10\n2",            'expected_output' => "1.0000\n1.0000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "8\n1\n2\n3\n4\n1\n2\n3\n4\n4",           'expected_output' => "0.4000\n0.8000\n1.2000\n1.6000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Autocorrelation lag-1 ────────────────────────────────────
        $seed(45, [
            ['input' => "5\n1\n2\n3\n4\n5",        'expected_output' => '0.4000',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n1\n1\n1",           'expected_output' => '0.0000',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n1\n2",           'expected_output' => '-1.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n2\n4\n6\n8\n10",       'expected_output' => '0.4000',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: IDF ──────────────────────────────────────────────────────
        $seed(46, [
            ['input' => "3\nthe cat sat\nthe dog ran\nthe bird flew",   'expected_output' => "bird: 1.0986\ncat: 1.0986\ndog: 1.0986\nflew: 1.0986\nran: 1.0986\nsat: 1.0986\nthe: 0.0000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nhello world\nhello python",                  'expected_output' => "hello: 0.0000\npython: 0.6931\nworld: 0.6931",                                                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\ndata science",                               'expected_output' => "data: 0.0000\nscience: 0.0000",                                                                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\na b c\na b\na",                             'expected_output' => "a: 0.0000\nb: 0.4055\nc: 1.0986",                                                               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: TF-IDF of query word ─────────────────────────────────────
        $seed(47, [
            ['input' => "3\nthe cat sat on the mat\nthe dog ran away\nthe bird flew high\nthe",       'expected_output' => "0.0000\n0.0000\n0.0000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\ncat sat mat\ndog ran away\ncat bird flew\ncat",                            'expected_output' => "0.3662\n0.0000\n0.3662",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nhello world\nhello python\nhello",                                         'expected_output' => "0.0000\n0.0000",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\na b c\na b d\na e f\nb",                                                  'expected_output' => "0.1351\n0.1351\n0.0000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Cosine similarity ────────────────────────────────────────
        $seed(48, [
            ['input' => "the cat sat on the mat\nthe cat sat on the log",   'expected_output' => '0.9231',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "hello world\nhello world",                          'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "data science\nmachine learning",                    'expected_output' => '0.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "a b c\na b d",                                     'expected_output' => '0.6667',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: N-grams ──────────────────────────────────────────────────
        $seed(49, [
            ['input' => "the cat sat on the mat\n2",   'expected_output' => "cat sat: 1\non the: 1\nsat on: 1\nthe cat: 1\nthe mat: 1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "a b a b a\n2",                'expected_output' => "a b: 2\nb a: 2",                                             'is_hidden' => false, 'order_index' => 2],
            ['input' => "hello world hello\n1",        'expected_output' => "hello: 2\nworld: 1",                                         'is_hidden' => true,  'order_index' => 3],
            ['input' => "a b c d\n3",                  'expected_output' => "a b c: 1\nb c d: 1",                                         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Document ranking ─────────────────────────────────────────
        $seed(50, [
            ['input' => "3\ndata science is great\nmachine learning is fun\npython is great for data\ndata science python",   'expected_output' => "2: 0.7454\n0: 0.5774\n1: 0.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nhello world\nhello python\nhello",                                                                 'expected_output' => "0: 0.7071\n1: 0.7071",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nabc def\nghi jkl\nabc",                                                                            'expected_output' => "0: 0.7071\n1: 0.0000",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\na b c\na b\na\na b c",                                                                             'expected_output' => "0: 1.0000\n1: 0.8165\n2: 0.5774",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 3 Coding (Intermediate) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}