<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 3 — Introduction to Data Science (Professional / Level 4) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Professional tier
 *   2. coding_questions    — 50 questions covering advanced Data Science concepts
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mapped to lessons 293–303):
 *   L1.1  What is Data Science? (end-to-end pipeline design)
 *   L1.2  NumPy: Advanced Linear Algebra (SVD, eigendecomposition, Gram-Schmidt)
 *   L1.3  Pandas: Advanced GroupBy, window functions, merge strategies
 *   L1.4  Visualization: Statistical plot computation (kde, violin summaries)
 *   L1.5  Statistics: Hypothesis tests, power analysis, effect size
 *   L1.6  Feature Engineering: Advanced encodings, interaction terms, PCA pipeline
 *   L1.7  EDA: Automated profiling, mutual information, feature importance proxy
 *   L1.8  Machine Learning: Gradient descent, regularisation, cross-validation
 *   L1.9  Unsupervised Learning: K-Means convergence, DBSCAN, hierarchical
 *   L1.10 Time Series: ARIMA components, STL, anomaly detection
 *   L1.11 NLP: Advanced TF-IDF, Word2Vec-style embedding, text classification
 *
 * Difficulty: Professional — problems require algorithm implementation from scratch,
 * multi-step pipelines, numerical stability awareness, and handling of edge cases.
 * Only Python builtins + math/random are allowed.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module3CodingChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (! $category) {
            $this->command->error('Professional category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 3 — Introduction to Data Science (Professional) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Introduction to Data Science',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Solve advanced Data Science challenges entirely from scratch in pure Python. Implement gradient descent, regularised regression, full K-Means convergence, SVD, DBSCAN, ARIMA differencing, TF-IDF classifiers, and more — no third-party libraries allowed.',
                'time_limit_seconds' => 2400,
                'base_xp'            => 2000,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: End-to-End Pipeline (Q1–Q4)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Build a complete **data cleaning pipeline**. Read `n` rows of CSV (format: `id,age,salary,label`). Apply these steps in order:
1. Drop rows where `age` or `salary` is `NA`
2. Replace remaining `NA` labels with `unknown`
3. Cap `age` at 100 (clip values above 100 to 100)
4. Remove duplicate `id`s (keep last occurrence)
5. Print the cleaned rows as CSV, sorted by `id` ascending.

Example:
```
Input:
5
1,25,50000,A
2,NA,60000,B
3,30,NA,C
1,28,55000,A
4,105,70000,D
Output:
1,28,55000,A
4,100,70000,D
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Full cleaning pipeline\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Implement a **train/test split** with stratification. Given `n` samples with binary labels (0/1), split into train (proportion `p`) and test sets while maintaining the label ratio in both sets. Use index-based assignment: within each class, assign the first `floor(class_count * p)` to train, rest to test. Print train indices then test indices (sorted, space-separated).

Read `n` labels (space-separated) and `p` (float).

Example:
```
Input:
6 0 1 1 0 1
0.6
Output:
train: 0 1 2
test: 3 4 5
```
MD,
                'starter_code'        => "import math\nlabels = list(map(int, input().split()))\np = float(input())\n# Stratified split\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Implement **k-fold cross-validation** indices. Given `n` samples and `k` folds, split into k approximately equal folds. For each fold, print the validation indices (sorted) and train indices (sorted). Extra samples go to earlier folds.

Read `n` and `k`.

Example:
```
Input:
6
3
Output:
Fold 1: val=[0, 1], train=[2, 3, 4, 5]
Fold 2: val=[2, 3], train=[0, 1, 4, 5]
Fold 3: val=[4, 5], train=[0, 1, 2, 3]
```
MD,
                'starter_code'        => "n = int(input())\nk = int(input())\n# Generate k-fold CV indices\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 275,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Implement a **data profiling report**. Read `n` rows of CSV (format: `col1,col2,...`) with a header row. For each numeric column, report:
- `count` (non-NA values)
- `mean` (2 dp)
- `std` (population, 2 dp)
- `min`, `max`
- `missing` count

Format: `colname: count=N, mean=X, std=X, min=X, max=X, missing=N`

Non-numeric columns report: `colname: type=categorical, unique=N, missing=N`

Example:
```
Input:
3
name,age,score
Alice,25,90
Bob,NA,80
Carol,30,NA
Output:
name: type=categorical, unique=3, missing=0
age: count=2, mean=27.50, std=2.50, min=25, max=30, missing=1
score: count=2, mean=85.00, std=5.00, min=80, max=90, missing=1
```
MD,
                'starter_code'        => "import math\nn = int(input())\nheader = input().split(',')\nrows = [input().split(',') for _ in range(n)]\n# Data profiling report\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Advanced Linear Algebra (Q5–Q9)
            // ═══════════════════════════════════════════════════════════════

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Implement **Gram-Schmidt orthogonalisation**. Given `m` vectors of dimension `n`, produce an orthonormal basis. Print each basis vector rounded to 4 decimal places, space-separated. Skip any vector that becomes zero (linearly dependent).

Read `m n`, then `m` vectors.

Example:
```
Input:
2 2
3 0
2 2
Output:
1.0 0.0
0.0 1.0
```
MD,
                'starter_code'        => "import math\nm, n = map(int, input().split())\nvectors = [list(map(float, input().split())) for _ in range(m)]\n# Gram-Schmidt orthogonalisation\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Compute the **eigenvalues and eigenvectors** of a 2×2 symmetric matrix using the analytical formula.

For a 2×2 matrix [[a,b],[b,d]]:
- λ = ((a+d) ± √((a-d)²+4b²)) / 2
- Eigenvectors: for each λ, solve (A−λI)v = 0, normalise.

Print eigenvalues (descending) and corresponding unit eigenvectors (4 dp each).

Example:
```
Input:
4 1
1 4
Output:
lambda1: 5.0
v1: 0.7071 0.7071
lambda2: 3.0
v2: -0.7071 0.7071
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\na, b = row1\n_, d = row2\n# Compute eigenvalues and eigenvectors\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 375,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Implement **power iteration** to find the dominant eigenvector of an n×n matrix.

Algorithm: start with a random unit vector (use `[1/√n]*n`), repeatedly multiply by A and normalise. Run for `max_iter` iterations. Print the estimated dominant eigenvalue (4 dp) and eigenvector (4 dp), space-separated.

Read `n`, then the matrix rows, then `max_iter`.

Example:
```
Input:
2
2 1
1 2
100
Output:
eigenvalue: 3.0
eigenvector: 0.7071 0.7071
```
MD,
                'starter_code'        => "import math\nn = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\nmax_iter = int(input())\n# Power iteration\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 375,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Compute the **rank-k approximation** of a matrix using truncated SVD (2×2 or 3×3 only, use analytical eigendecomposition of AᵀA).

Steps:
1. Compute AᵀA
2. Find eigenvalues/vectors of AᵀA (sorted descending)
3. Compute right singular vectors V, singular values σ=√λ, left vectors U=Av/σ
4. Reconstruct: A_k = Σᵢ₌₁ᵏ σᵢ uᵢvᵢᵀ

Read `m n k`, then `m` rows. Print the reconstructed matrix (2 dp), rows space-separated.

Example:
```
Input:
2 2 1
3 0
0 2
Output:
3.0 0.0
0.0 0.0
```
MD,
                'starter_code'        => "import math\nm, n, k = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\n# Rank-k SVD approximation\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 425,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Solve an **overdetermined linear system** Ax = b using the **Normal Equations**: x = (AᵀA)⁻¹Aᵀb.

Implement Gauss-Jordan elimination to invert AᵀA.

Read `m n` (m > n), then `m` rows of A (space-separated), then `m` values of b. Print x (n values, 4 dp, space-separated).

Example:
```
Input:
3 2
1 1
1 2
1 3
1
2
3
Output:
0.0 1.0
```
MD,
                'starter_code'        => "import math\nm, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\nb = [float(input()) for _ in range(m)]\n# Solve via normal equations\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Advanced Data Wrangling (Q10–Q14)
            // ═══════════════════════════════════════════════════════════════

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Implement a **rolling window aggregation** over grouped time-series CSV data (format: `group,timestamp,value`). For each group, compute the rolling mean of window size `w` (sorted by timestamp within each group). Print `group,timestamp,rolling_mean` (2 dp) for all rows where the window is full, sorted by group then timestamp.

Read `n`, then the rows, then `w`.

Example:
```
Input:
6
A,1,10
A,2,20
A,3,30
B,1,5
B,2,15
B,3,25
2
Output:
A,2,15.00
A,3,25.00
B,2,10.00
B,3,20.00
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\nw = int(input())\n# Rolling mean per group\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 325,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Implement a **merge join** (inner join) of two CSV datasets on a key column. Left CSV: `id,a,b`. Right CSV: `id,c,d`. Output the inner-joined rows as `id,a,b,c,d` sorted by id ascending.

Read `n1` rows of left CSV, then `n2` rows of right CSV.

Example:
```
Input:
3
1,10,20
2,30,40
3,50,60
2
1,100,200
3,300,400
Output:
1,10,20,100,200
3,50,60,300,400
```
MD,
                'starter_code'        => "n1 = int(input())\nleft = [input().split(',') for _ in range(n1)]\nn2 = int(input())\nright = [input().split(',') for _ in range(n2)]\n# Inner join on id\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 275,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Compute **window functions** (rank, cumulative sum, percentage of group total) for grouped CSV data (format: `group,value`). For each row print: `group,value,rank,cumsum,pct_of_group` where rank is within-group rank (1=highest, ties share the same rank), cumsum and pct_of_group are 2 dp. Sort output by group then by original order.

Read `n` rows.

Example:
```
Input:
4
A,30
A,10
B,20
B,20
Output:
A,30,1,30.00,75.00%
A,10,2,40.00,25.00%
B,20,1,20.00,50.00%
B,20,1,40.00,50.00%
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Compute window functions per group\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Implement a **fuzzy string deduplication**. Given `n` strings, group strings that are within edit distance `d` of each other. Print each group (sorted alphabetically within group, groups sorted by first element). Use a union-find structure.

Read `n`, then `n` strings, then `d`.

Example:
```
Input:
4
cat
bat
hat
dog
1
Output:
['bat', 'cat', 'hat']
['dog']
```
MD,
                'starter_code'        => "n = int(input())\nstrings = [input().strip() for _ in range(n)]\nd = int(input())\n# Fuzzy deduplication with edit distance\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 375,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Implement **multi-key aggregation** (like pandas `groupby` + `agg`). Given CSV data (format: `dept,team,score`), compute for each `(dept, team)` combination: `count`, `mean` (2 dp), `max`, `min`. Print sorted by dept then team. Format: `dept,team,count,mean,max,min`

Read `n` rows.

Example:
```
Input:
5
Eng,A,90
Eng,A,80
Eng,B,70
HR,A,85
HR,A,95
Output:
Eng,A,2,85.00,90,80
Eng,B,1,70.00,70,70
HR,A,2,90.00,95,85
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Multi-key aggregation\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Statistical Plot Computation (Q15–Q18)
            // ═══════════════════════════════════════════════════════════════

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Compute a **kernel density estimate (KDE)** using a Gaussian kernel at a set of query points.

KDE(x) = (1/n) × Σ K((x − xᵢ) / h) / h
where K(u) = (1/√(2π)) × exp(−u²/2)

Read `n` data points (space-separated), bandwidth `h`, then `q` query points (one per line). Print the KDE value at each query point rounded to 6 decimal places.

Example:
```
Input:
3 1 2 3
1.0
2
1.5
2.5
Output:
0.282095
0.282095
```
MD,
                'starter_code'        => "import math\ndata = list(map(float, input().split()))\nh = float(input())\nq = int(input())\n# Compute KDE\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Compute **violin plot summary statistics** for groups of data. For each group: min, Q1, median, Q3, max, and KDE peak (mode approximation: value with max KDE using bandwidth = 1.06 × std × n^(-1/5)).

Read `n` rows of `group value` (space-separated). For each group (sorted alphabetically) print:
`group: min=X, Q1=X, median=X, Q3=X, max=X, kde_peak=X` (all 2 dp).

Example:
```
Input:
4
A 1
A 3
B 2
B 4
Output:
A: min=1.00, Q1=1.50, median=2.00, Q3=2.50, max=3.00, kde_peak=2.00
B: min=2.00, Q1=2.50, median=3.00, Q3=3.50, max=4.00, kde_peak=3.00
```
MD,
                'starter_code'        => "import math\nn = int(input())\nrows = [input().split() for _ in range(n)]\n# Violin plot summaries per group\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Compute the **Q-Q plot coordinates** (quantile-quantile against standard normal) for a dataset.

Steps:
1. Sort the data
2. Compute theoretical quantiles: Φ⁻¹((i − 0.5)/n) for i = 1..n using the rational approximation of the inverse normal CDF (Beasley-Springer-Moro or equivalent)
3. Print pairs `(theoretical, empirical)` rounded to 4 dp.

Read `n` data points (one per line).

Example:
```
Input:
3
-1
0
1
Output:
-1.0 -1.0
0.0 0.0
1.0 1.0
```
MD,
                'starter_code'        => "import math\n\ndef inv_norm(p):\n    # Rational approximation for inverse normal CDF\n    a = [0, -3.969683028665376e+01, 2.209460984245205e+02,\n         -2.759285104469687e+02, 1.383577518672690e+02,\n         -3.066479806614716e+01, 2.506628277459239e+00]\n    b = [0, -5.447609879822406e+01, 1.615858368580409e+02,\n         -1.556989798598866e+02, 6.680131188771972e+01, -1.328068155288572e+01]\n    c = [-7.784894002430293e-03, -3.223964580411365e-01,\n          -2.400758277161838e+00, -2.549732539343734e+00,\n           4.374664141464968e+00, 2.938163982698783e+00]\n    d = [7.784695709041462e-03, 3.224671290700398e-01,\n          2.445134137142996e+00, 3.754408661907416e+00]\n    p_low, p_high = 0.02425, 1 - 0.02425\n    if p < p_low:\n        q = math.sqrt(-2 * math.log(p))\n        return (((((c[0]*q+c[1])*q+c[2])*q+c[3])*q+c[4])*q+c[5]) / ((((d[0]*q+d[1])*q+d[2])*q+d[3])*q+1)\n    elif p <= p_high:\n        q = p - 0.5; r = q*q\n        return (((((a[1]*r+a[2])*r+a[3])*r+a[4])*r+a[5])*r+a[6])*q / (((((b[1]*r+b[2])*r+b[3])*r+b[4])*r+b[5])*r+1)\n    else:\n        q = math.sqrt(-2 * math.log(1-p))\n        return -(((((c[0]*q+c[1])*q+c[2])*q+c[3])*q+c[4])*q+c[5]) / ((((d[0]*q+d[1])*q+d[2])*q+d[3])*q+1)\n\nn = int(input())\ndata = sorted([float(input()) for _ in range(n)])\n# Compute Q-Q coordinates\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Compute a **2D kernel density estimate** at given query points using Gaussian kernel with bandwidth `h`.

KDE(x,y) = (1/n) × Σ K((x−xᵢ)/h) × K((y−yᵢ)/h) / h²

Read `n` 2D points (each as `x y`), bandwidth `h`, then `q` query points (each as `x y`). Print the KDE value at each query rounded to 6 decimal places.

Example:
```
Input:
2
0 0
2 2
1.0
1
1 1
Output:
0.029400
```
MD,
                'starter_code'        => "import math\nn = int(input())\npoints = [tuple(map(float, input().split())) for _ in range(n)]\nh = float(input())\nq = int(input())\n# 2D KDE\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 375,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Advanced Statistics (Q19–Q23)
            // ═══════════════════════════════════════════════════════════════

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Implement the **Mann-Whitney U test** (two independent samples).

U = n₁n₂ + n₁(n₁+1)/2 − R₁ (where R₁ = sum of ranks of sample 1 in the combined ranking)

Compute U and its z-statistic: z = (U − n₁n₂/2) / √(n₁n₂(n₁+n₂+1)/12)

Read `n1` values on one line, then `n2` values on the next. Print U, z (4 dp), and `Reject H0` or `Fail to reject H0` at α=0.05 (|z| > 1.96).

Example:
```
Input:
1 2 3 4
5 6 7 8
Output:
U: 0.0
z: -2.3094
Reject H0
```
MD,
                'starter_code'        => "import math\ns1 = list(map(float, input().split()))\ns2 = list(map(float, input().split()))\n# Mann-Whitney U test\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 375,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Compute **Cohen's d** (effect size) and **statistical power** for a two-sample z-test.

Cohen's d = (μ₁ − μ₂) / s_pooled  where s_pooled = √((σ₁² + σ₂²)/2) (population SD)

Power = P(|Z| > z_crit | H₁ is true) ≈ Φ(|d|√(n/2) − z_crit) + Φ(−|d|√(n/2) − z_crit)

Use α=0.05 (z_crit=1.96). Approximate Φ using the error function: Φ(x) = (1+erf(x/√2))/2

Read `n` per group, `n` values of sample 1, then `n` values of sample 2.

Print `cohens_d: X` and `power: X` (4 dp).

Example:
```
Input:
4
1 2 3 4
5 6 7 8
Output:
cohens_d: 2.8284
power: 0.9992
```
MD,
                'starter_code'        => "import math\nn = int(input())\ns1 = list(map(float, input().split()))\ns2 = list(map(float, input().split()))\n# Cohen's d and power\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Implement the **two-sample Kolmogorov-Smirnov (KS) test**.

D = max|F₁(x) − F₂(x)| over all x (empirical CDFs)

Read `n1` values (space-separated) and `n2` values (next line). Print D (4 dp) and `Reject H0` or `Fail to reject H0` at α=0.05 using the critical value c(α) = 1.36 × √((n1+n2)/(n1×n2)).

Example:
```
Input:
1 2 3
4 5 6
Output:
D: 1.0
Reject H0
```
MD,
                'starter_code'        => "import math\ns1 = sorted(map(float, input().split()))\ns2 = sorted(map(float, input().split()))\n# Two-sample KS test\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Compute **mutual information** between two discrete variables.

MI(X;Y) = Σₓ Σᵧ P(x,y) log(P(x,y) / (P(x)P(y)))

Read `n` pairs of labels (format: `x y` per line). Print MI rounded to 4 decimal places.

Example:
```
Input:
4
A 1
A 1
B 2
B 2
Output: 0.6931
```
MD,
                'starter_code'        => "import math\nn = int(input())\npairs = [input().split() for _ in range(n)]\n# Compute mutual information\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 375,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Implement **Bayesian A/B testing**. Model conversions with a Beta distribution.

Given:
- Prior: Beta(α₀, β₀) for both variants
- Variant A: `n_A` trials, `k_A` conversions → posterior Beta(α₀+k_A, β₀+n_A−k_A)
- Variant B: `n_B` trials, `k_B` conversions → posterior Beta(α₀+k_B, β₀+n_B−k_B)

Estimate P(B > A) via Monte Carlo with 10000 samples (seed=42). Print P(B > A) rounded to 4 dp.

Read `alpha0 beta0`, then `n_A k_A`, then `n_B k_B`.

Example:
```
Input:
1 1
100 60
100 70
Output:
0.8700
```
MD,
                'starter_code'        => "import random\nrandom.seed(42)\n\ndef sample_beta(alpha, beta):\n    # Use gamma-based sampling: Beta = Gamma(a)/(Gamma(a)+Gamma(b))\n    def sample_gamma(shape):\n        # Marsaglia-Tsang method for gamma sampling\n        if shape < 1:\n            return sample_gamma(1 + shape) * (random.random() ** (1/shape))\n        d = shape - 1/3\n        c = 1/3 / (d**0.5)\n        while True:\n            x = random.gauss(0, 1)\n            v = (1 + c*x)**3\n            if v > 0:\n                u = random.random()\n                if u < 1 - 0.0331*(x**2)**2:\n                    return d*v\n                if math.log(u) < 0.5*x**2 + d*(1 - v + math.log(v)):\n                    return d*v\n    import math\n    g1 = sample_gamma(alpha)\n    g2 = sample_gamma(beta)\n    return g1 / (g1 + g2)\n\nalpha0, beta0 = map(float, input().split())\nn_A, k_A = map(int, input().split())\nn_B, k_B = map(int, input().split())\n# Estimate P(B > A)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 425,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Advanced Feature Engineering (Q24–Q28)
            // ═══════════════════════════════════════════════════════════════

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Implement **target encoding** with leave-one-out (LOO) to avoid data leakage.

For each sample i with category c and target yᵢ:
LOO_encoding(i) = (sum_of_targets_in_c − yᵢ) / (count_in_c − 1)

If category has only one member, use the global mean. Print the LOO-encoded value for each row rounded to 4 dp.

Read `n` rows of `category target` (space-separated).

Example:
```
Input:
4
A 10
A 20
B 30
A 30
Output:
25.0
20.0
0.0
15.0
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split() for _ in range(n)]\n# LOO target encoding\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Implement a **full PCA pipeline**: mean-center, compute covariance matrix, find top-k eigenvectors (use power iteration + deflation), project data, compute explained variance ratio per component.

Read `n m k`, then `n` rows of `m` floats. Print:
1. The k eigenvectors (each as a row, 4 dp, space-separated)
2. The projected data (each row, 4 dp, space-separated)
3. Explained variance ratio for each component (4 dp, one per line)

Example:
```
Input:
3 2 1
1 2
2 4
3 6
Output:
eigenvector: 0.4472 0.8944
projected: -2.2361\n0.0\n2.2361
variance_ratio: 1.0
```
MD,
                'starter_code'        => "import math\nn, m, k = map(int, input().split())\ndata = [list(map(float, input().split())) for _ in range(n)]\n# Full PCA pipeline\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 425,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Compute **pairwise interaction features** for a numeric dataset. For every pair of columns (i, j) where i < j, create a new feature `col_i × col_j`. Print the extended feature matrix (original columns + interaction terms), column header on first line, rows space-separated (2 dp).

Read `n m` then header line (space-separated col names) then `n` rows.

Example:
```
Input:
2 2
a b
1 2
3 4
Output:
a b a_b
1.00 2.00 2.00
3.00 4.00 12.00
```
MD,
                'starter_code'        => "n, m = map(int, input().split())\nheader = input().split()\ndata = [list(map(float, input().split())) for _ in range(n)]\n# Generate interaction features\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Implement **frequency encoding with smoothing** (empirical Bayes smoothing):

encoded(c) = (count(c) × category_mean(c) + global_count × global_mean) / (count(c) + global_count)

where global_count is a smoothing factor (read as input).

Read `n` rows of `category target` (space-separated), then smoothing factor `k`. Print encoded value for each category (unique, sorted alphabetically) as `category: X` (4 dp).

Example:
```
Input:
5
A 10
A 20
B 30
B 40
C 50
10
Output:
A: 18.3333
B: 25.0
C: 28.0
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split() for _ in range(n)]\nk = float(input())\n# Frequency encoding with smoothing\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Implement **recursive feature elimination (RFE)** using correlation with the target as the importance measure.

At each step, remove the feature with the lowest |correlation with target|. Repeat until `k` features remain. Print the surviving feature indices (0-based, sorted ascending) and their correlations with the target (4 dp).

Read `n m k` then `n` rows of `m` features followed by a target value (last column).

Example:
```
Input:
4 3 2
1 2 3 10
2 4 5 20
3 6 7 30
4 8 9 40
Output:
features: [1, 2]
correlations: 1.0 1.0
```
MD,
                'starter_code'        => "import math\nn, m, k = map(int, input().split())\nrows = [list(map(float, input().split())) for _ in range(n)]\n# RFE via correlation\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 375,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Advanced EDA (Q29–Q32)
            // ═══════════════════════════════════════════════════════════════

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Compute the full **mutual information matrix** for a set of discrete columns. For each pair (i,j), compute MI(colᵢ, colⱼ). Print the matrix rounded to 4 dp, rows space-separated.

Read `n m` then `n` rows of `m` discrete values (strings or ints).

Example:
```
Input:
4 2
A 1
A 1
B 2
B 2
Output:
0.6931 0.6931
0.6931 0.6931
```
MD,
                'starter_code'        => "import math\nn, m = map(int, input().split())\ndata = [input().split() for _ in range(n)]\n# Mutual information matrix\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 375,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Compute **Cramér's V** (association between two categorical variables).

V = √(χ²/n / min(r−1, c−1))

where r = number of unique values of X, c = number of unique values of Y.

Read `n` pairs of labels (format: `x y` per line). Print V rounded to 4 dp.

Example:
```
Input:
4
A 1
A 2
B 1
B 2
Output: 0.0
```
MD,
                'starter_code'        => "import math\nn = int(input())\npairs = [input().split() for _ in range(n)]\n# Compute Cramer's V\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Detect **multicollinearity** using Variance Inflation Factor (VIF).

VIF(j) = 1 / (1 − R²ⱼ)

where R²ⱼ is the R² from regressing feature j on all other features.

Read `n m` then n rows of m features. Print `VIF(colj): X` for each column j (0-based) rounded to 4 dp.

Example:
```
Input:
4 2
1 2
2 4
3 6
4 8
Output:
VIF(col0): inf
VIF(col1): inf
```
MD,
                'starter_code'        => "import math\nn, m = map(int, input().split())\ndata = [list(map(float, input().split())) for _ in range(n)]\n# Compute VIF for each feature\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 375,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Implement **ANOVA (one-way)**. Given `k` groups of data, compute the F-statistic.

F = MSB / MSW  where:
- MSB = SSB / (k−1)  (between-group mean square)
- MSW = SSW / (N−k)  (within-group mean square)

Read `k`, then `k` groups (one per line, space-separated floats). Print F rounded to 4 dp and `Reject H0` or `Fail to reject H0` using F_crit approximation: if k=2 and df_w > 30, use F_crit=4.0; if k=3, use F_crit=3.89.

Example:
```
Input:
2
1 2 3
7 8 9
Output:
F: 54.0
Reject H0
```
MD,
                'starter_code'        => "k = int(input())\ngroups = [list(map(float, input().split())) for _ in range(k)]\n# One-way ANOVA\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 375,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Advanced ML (Q33–Q37)
            // ═══════════════════════════════════════════════════════════════

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Implement **gradient descent with L2 regularisation** (Ridge regression) for linear regression.

Update: θⱼ ← θⱼ − α(∂L/∂θⱼ + λθⱼ) for j > 0; θ₀ ← θ₀ − α∂L/∂θ₀

Read `n m` (samples, features), then `n` rows (last column = y), then `alpha` (learning rate), `lambda` (regularisation), `epochs`. Initialise θ = 0. Print θ (m+1 values, 4 dp, space-separated) and final MSE (4 dp).

Example:
```
Input:
3 1
1 2
2 4
3 6
0.1
0.0
100
Output:
theta: 0.0 2.0
mse: 0.0
```
MD,
                'starter_code'        => "n, m = map(int, input().split())\nrows = [list(map(float, input().split())) for _ in range(n)]\nalpha = float(input())\nlam = float(input())\nepochs = int(input())\n# Ridge regression via gradient descent\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Implement **logistic regression** with gradient descent (binary classification, sigmoid function).

P(y=1|x) = σ(θᵀx) = 1/(1+e^(−θᵀx))

Loss: binary cross-entropy
Update: θ ← θ − α × Xᵀ(σ(Xθ)−y)/n

Read `n m` then `n` rows (last column = binary label 0/1), then `alpha`, `epochs`. Init θ=0. Print θ (4 dp, space-separated) and accuracy on training data (4 dp).

Example:
```
Input:
4 2
0 0 0
0 1 0
1 0 0
1 1 1
0.5
1000
Output:
theta: -5.0963 2.5682 2.5682
accuracy: 1.0
```
MD,
                'starter_code'        => "import math\nn, m = map(int, input().split())\nrows = [list(map(float, input().split())) for _ in range(n)]\nalpha = float(input())\nepochs = int(input())\n# Logistic regression\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 425,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Implement a **Decision Tree** (binary, axis-aligned splits, Gini impurity, max_depth control) and classify test points.

Gini(node) = 1 − Σ pᵢ²
Best split: feature and threshold that minimises weighted Gini of children.

Read `n m max_depth` then `n` rows (last column = binary label), then `q` test rows (m features). Print the predicted label for each test row.

Example:
```
Input:
4 1 2
1 0
2 0
3 1
4 1
2
2.5
3.5
Output:
0
1
```
MD,
                'starter_code'        => "n, m, max_depth = map(int, input().split())\nrows = [list(map(float, input().split())) for _ in range(n)]\nq = int(input())\n# Decision Tree with Gini splitting\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Implement **stratified k-fold cross-validation** with logistic regression (gradient descent, alpha=0.1, epochs=200). Report per-fold accuracy and mean accuracy.

Read `n m k` then `n` rows (last column = binary label 0/1). Print each fold's accuracy (4 dp) and the mean (4 dp).

Example:
```
Input:
6 1 3
0 0
1 1
2 0
3 1
4 0
5 1
Output:
Fold 1: 1.0
Fold 2: 1.0
Fold 3: 1.0
Mean: 1.0
```
MD,
                'starter_code'        => "import math\nn, m, k = map(int, input().split())\nrows = [list(map(float, input().split())) for _ in range(n)]\n# Stratified k-fold CV with logistic regression\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Compute the **ROC curve** coordinates and **AUC** for a binary classifier.

Sort predictions descending, step through thresholds, compute TPR and FPR at each. Compute AUC using the trapezoidal rule.

Read `n` pairs `label score` (space-separated, one per line). Print AUC rounded to 4 dp.

Example:
```
Input:
4
1 0.9
0 0.4
1 0.8
0 0.3
Output: 1.0
```
MD,
                'starter_code'        => "n = int(input())\npairs = [input().split() for _ in range(n)]\n# Compute ROC AUC\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 375,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Advanced Unsupervised Learning (Q38–Q41)
            // ═══════════════════════════════════════════════════════════════

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Implement **full K-Means convergence** for 2D data (run until centroids stop changing or `max_iter` is reached, using Euclidean distance). Use a fixed random seed (initialise centroids as the first `k` distinct points).

Read `n k max_iter` then `n` points as `x y`. Print:
1. Final centroids (sorted by x then y, 4 dp)
2. Cluster assignments (0-indexed) for each input point in original order
3. Number of iterations taken

Example:
```
Input:
4 2 100
1 1
1 2
8 1
8 2
Output:
centroids: (1.0, 1.5) (8.0, 1.5)
assignments: 0 0 1 1
iterations: 2
```
MD,
                'starter_code'        => "import math\nn, k, max_iter = map(int, input().split())\npoints = [tuple(map(float, input().split())) for _ in range(n)]\n# Full K-Means convergence\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Implement **DBSCAN** for 1D data. Parameters: `eps` (neighbourhood radius), `min_samples`.

Algorithm:
1. For each point, find all neighbours within `eps`
2. Core points have ≥ min_samples neighbours (including self)
3. Expand clusters from core points via BFS
4. Label noise as -1

Read `n` points (space-separated), then `eps`, `min_samples`. Print cluster assignment for each point (in original order).

Example:
```
Input:
6 1 2 3 10 11 12
1.5
2
Output:
0 0 0 1 1 1
```
MD,
                'starter_code'        => "data = list(map(float, input().split()))\neps = float(input())\nmin_samples = int(input())\n# DBSCAN\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 425,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Implement **agglomerative hierarchical clustering** (single linkage) for 1D data. Build the dendrogram: at each step, merge the two clusters with the smallest minimum inter-cluster distance. Print the merge sequence as `Merge clusters A and B at distance D` (4 dp), where cluster names are the original point indices (0-based) joined by `-`.

Read `n` space-separated values.

Example:
```
Input:
4 1 2 8 9
Output:
Merge 0 and 1 at distance 1.0
Merge 2 and 3 at distance 1.0
Merge 0-1 and 2-3 at distance 6.0
```
MD,
                'starter_code'        => "data = list(map(float, input().split()))\n# Agglomerative single-linkage clustering\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 425,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Implement the **Elbow Method** for K-Means. Run K-Means (max_iter=100) for k=1..K_max and compute WCSS for each. Print WCSS per k and the **optimal k** (largest k where the WCSS drop from k−1 to k is more than double the drop from k to k+1).

Read `n K_max` then `n` 1D points (space-separated). Initialise centroids: first `k` points for each run.

Example:
```
Input:
6 5
1 2 3 10 11 12
Output:
k=1: 60.5
k=2: 1.0
k=3: 0.0
k=4: 0.0
k=5: 0.0
optimal_k: 2
```
MD,
                'starter_code'        => "import math\nn, K_max = map(int, input().split())\ndata = list(map(float, input().split()))\n# Elbow method\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Advanced Time Series (Q42–Q45)
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Implement **ARIMA(p,d,q) differencing and AR fitting** (partial implementation). Steps:
1. Apply `d` rounds of differencing
2. Fit AR(p) model to the differenced series using OLS (normal equations)
3. Forecast `h` steps ahead (recursively, setting MA terms to 0)
4. Un-difference the forecasts

Read series (space-separated), then `p d q h`. Print the `h` forecasted values (original scale) rounded to 4 dp.

Example:
```
Input:
1 3 5 7 9
1 1 0 2
Output:
11.0
13.0
```
MD,
                'starter_code'        => "import math\nseries = list(map(float, input().split()))\np, d, q, h = map(int, input().split())\n# ARIMA(p,d,q) forecasting\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Implement **anomaly detection** for time series using a rolling Z-score approach.

For each time step t ≥ w, compute z = (xₜ − rolling_mean) / rolling_std over the window [t−w, t−1]. Flag as anomaly if |z| > threshold.

Read series (space-separated), then `w` (window), `threshold`. Print the indices (0-based) of anomalous points, space-separated. If none, print `None`.

Example:
```
Input:
1 2 3 4 100 6 7
3
2.0
Output: 4
```
MD,
                'starter_code'        => "import math\nseries = list(map(float, input().split()))\nw = int(input())\nthreshold = float(input())\n# Rolling Z-score anomaly detection\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 375,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Compute **Holt-Winters double exponential smoothing** (trend model, no seasonality).

Level: Lₜ = α xₜ + (1−α)(Lₜ₋₁ + Tₜ₋₁)
Trend: Tₜ = β(Lₜ − Lₜ₋₁) + (1−β)Tₜ₋₁

Init: L₁ = x₁, T₁ = x₂ − x₁

Forecast h steps: F_{n+h} = Lₙ + h × Tₙ

Read series (space-separated), then `alpha beta h`. Print the `h` forecasts rounded to 4 dp.

Example:
```
Input:
10 20 30 40
0.5 0.5 2
Output:
50.0
60.0
```
MD,
                'starter_code'        => "series = list(map(float, input().split()))\nalpha, beta, h = float(input().split()[0]), float(input().split()[1]), int(input().split()[2])\n# Holt-Winters double exponential smoothing\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Compute the **partial autocorrelation function (PACF)** at lags 1..L using the Yule-Walker equations.

For each lag k, solve the Yule-Walker system (autocorrelation matrix of size k×k) using Gaussian elimination to get φ_k (the k-th PACF coefficient = last element of the solution).

Read series (space-separated), then `L`. Print PACF at lags 1..L, rounded to 4 dp.

Example:
```
Input:
1 2 3 4 5
2
Output:
lag 1: 1.0
lag 2: 0.0
```
MD,
                'starter_code'        => "series = list(map(float, input().split()))\nL = int(input())\n# PACF via Yule-Walker\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11: Advanced NLP (Q46–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Implement **BM25** ranking. Given a corpus of documents and a query, rank documents by BM25 score.

BM25(d,q) = Σ IDF(t) × TF_norm(t,d)
TF_norm = f(t,d)(k₁+1) / (f(t,d) + k₁(1−b+b×|d|/avgdl))
IDF(t) = log((N−df+0.5)/(df+0.5)+1)

Use k₁=1.5, b=0.75.

Read `n` documents (one per line), then the query. Print documents ranked by BM25 score descending, one per line (original text). If tie, preserve original order.

Example:
```
Input:
3
the cat sat on the mat
the dog ran in the park
a cat and a dog
cat
Output:
the cat sat on the mat
a cat and a dog
the dog ran in the park
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndocs = [input().split() for _ in range(n)]\nquery = input().split()\n# BM25 ranking\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 425,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Implement a **Word2Vec CBOW approximation** using co-occurrence counts.

Build a co-occurrence matrix with window size `w`. For each target word, its vector is the row of the PMI (Pointwise Mutual Information) matrix:

PMI(w,c) = log(P(w,c) / (P(w)×P(c)))   (use positive PMI: max(0, PMI))

Read `n` sentences (one per line), window size `w`, then a query word. Print the 5 most similar words (cosine similarity on PPMI vectors), one per line as `word: similarity` (4 dp).

Example:
```
Input:
3
dog barks loud
cat meows soft
dog runs fast
1
dog
Output:
barks: 1.0
runs: 1.0
loud: 1.0
fast: 1.0
cat: 0.0
```
MD,
                'starter_code'        => "import math\nn = int(input())\nsentences = [input().split() for _ in range(n)]\nw = int(input())\nquery = input().strip()\n# PPMI-based word vectors and similarity\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Implement **Latent Semantic Analysis (LSA)** on a small corpus using truncated SVD. Represent documents and query in a low-dimensional space (rank-k SVD) and rank documents by cosine similarity to the query.

Read `n` documents, then the query, then `k`. Use TF (raw count) as the term-document matrix. Print document indices (0-based) ranked by cosine similarity descending.

Example:
```
Input:
3
cat dog
dog fish
cat cat
cat
1
Output:
2
0
1
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndocs = [input().split() for _ in range(n)]\nquery = input().split()\nk = int(input())\n# LSA via truncated SVD\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Implement a **character-level n-gram language model** (Laplace smoothing). Given training text and `n` (n-gram order), compute the probability of a test string character-by-character.

P(cₜ | cₜ₋ₙ₊₁...cₜ₋₁) = (count(context+char) + 1) / (count(context) + |vocab|)

Read the training string, then `n`, then the test string. Print log-probability (base-e) rounded to 4 dp and perplexity = exp(−log_prob/len(test)) rounded to 4 dp.

Example:
```
Input:
hello world
2
world
Output:
log_prob: -6.7334
perplexity: 3.9624
```
MD,
                'starter_code'        => "import math\ntrain = input()\nn = int(input())\ntest = input()\n# Character n-gram language model\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Implement a **full text classification pipeline** from scratch:
1. Tokenize and lowercase all documents
2. Build TF-IDF matrix (smoothed IDF: log((1+n)/(1+df))+1)
3. Train a Logistic Regression classifier (gradient descent, alpha=0.1, epochs=200, L2 lambda=0.01) on TF-IDF features
4. Classify test documents

Read `n_train` labelled training lines (format: `label|text`), then `n_test` test lines (text only). Print the predicted label for each test document.

Example:
```
Input:
4
pos|great amazing wonderful
pos|excellent fantastic
neg|terrible horrible bad
neg|awful dreadful
2
wonderful excellent
horrible terrible
Output:
pos
neg
```
MD,
                'starter_code'        => "import math\nn_train = int(input())\ntrain = [input().split('|', 1) for _ in range(n_train)]\nn_test = int(input())\ntest = [input() for _ in range(n_test)]\n# Full TF-IDF + Logistic Regression pipeline\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 500,
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

        // ── Q1: cleaning pipeline ─────────────────────────────────────────
        $seed(1, [
            ['input' => "5\n1,25,50000,A\n2,NA,60000,B\n3,30,NA,C\n1,28,55000,A\n4,105,70000,D",  'expected_output' => "1,28,55000,A\n4,100,70000,D",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1,20,30000,X\n2,NA,40000,Y",                                           'expected_output' => "1,20,30000,X",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1,25,50000,A\n1,30,60000,B\n2,35,70000,C",                           'expected_output' => "1,30,60000,B\n2,35,70000,C",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1,110,50000,A\n2,90,60000,B",                                         'expected_output' => "1,100,50000,A\n2,90,60000,B",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: stratified split ──────────────────────────────────────────
        $seed(2, [
            ['input' => "6 0 1 1 0 1\n0.6",                       'expected_output' => "train: 0 1 2\ntest: 3 4 5",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 0 0 1 1\n0.5",                         'expected_output' => "train: 0 2\ntest: 1 3",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "6 1 1 1 0 0 0\n0.6667",                  'expected_output' => "train: 0 1 3 4\ntest: 2 5",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 0 1\n0.5",                             'expected_output' => "train: 0\ntest: 1",               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: k-fold indices ────────────────────────────────────────────
        $seed(3, [
            ['input' => "6\n3",  'expected_output' => "Fold 1: val=[0, 1], train=[2, 3, 4, 5]\nFold 2: val=[2, 3], train=[0, 1, 4, 5]\nFold 3: val=[4, 5], train=[0, 1, 2, 3]",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n2",  'expected_output' => "Fold 1: val=[0, 1], train=[2, 3]\nFold 2: val=[2, 3], train=[0, 1]",                                                       'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5",  'expected_output' => "Fold 1: val=[0], train=[1, 2, 3, 4]\nFold 2: val=[1], train=[0, 2, 3, 4]\nFold 3: val=[2], train=[0, 1, 3, 4]\nFold 4: val=[3], train=[0, 1, 2, 4]\nFold 5: val=[4], train=[0, 1, 2, 3]",  'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n3",  'expected_output' => "Fold 1: val=[0], train=[1, 2]\nFold 2: val=[1], train=[0, 2]\nFold 3: val=[2], train=[0, 1]",                             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: data profiling ────────────────────────────────────────────
        $seed(4, [
            ['input' => "3\nname,age,score\nAlice,25,90\nBob,NA,80\nCarol,30,NA",  'expected_output' => "name: type=categorical, unique=3, missing=0\nage: count=2, mean=27.50, std=2.50, min=25, max=30, missing=1\nscore: count=2, mean=85.00, std=5.00, min=80, max=90, missing=1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\nval\n42",                                                'expected_output' => "val: count=1, mean=42.00, std=0.00, min=42, max=42, missing=0",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\ncat,num\nA,1\nB,NA",                                   'expected_output' => "cat: type=categorical, unique=2, missing=0\nnum: count=1, mean=1.00, std=0.00, min=1, max=1, missing=1",  'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\nx\n1\n2\n3",                                            'expected_output' => "x: count=3, mean=2.00, std=0.82, min=1, max=3, missing=0",               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Gram-Schmidt ──────────────────────────────────────────────
        $seed(5, [
            ['input' => "2 2\n3 0\n2 2",          'expected_output' => "1.0 0.0\n0.0 1.0",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 3\n1 0 0",             'expected_output' => "1.0 0.0 0.0",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n1 1\n1 1",          'expected_output' => "0.7071 0.7071",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 3\n1 0 0\n0 1 0",      'expected_output' => "1.0 0.0 0.0\n0.0 1.0 0.0",'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q6: eigendecomposition 2x2 ───────────────────────────────────
        $seed(6, [
            ['input' => "4 1\n1 4",   'expected_output' => "lambda1: 5.0\nv1: 0.7071 0.7071\nlambda2: 3.0\nv2: -0.7071 0.7071",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 0\n0 3",   'expected_output' => "lambda1: 3.0\nv1: 0.0 1.0\nlambda2: 2.0\nv2: 1.0 0.0",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 0\n0 1",   'expected_output' => "lambda1: 1.0\nv1: 1.0 0.0\nlambda2: 1.0\nv2: 0.0 1.0",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 1\n1 3",   'expected_output' => "lambda1: 4.0\nv1: 0.7071 0.7071\nlambda2: 2.0\nv2: -0.7071 0.7071",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: power iteration ───────────────────────────────────────────
        $seed(7, [
            ['input' => "2\n2 1\n1 2\n100",    'expected_output' => "eigenvalue: 3.0\neigenvector: 0.7071 0.7071",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3 0\n0 1\n50",     'expected_output' => "eigenvalue: 3.0\neigenvector: 1.0 0.0",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n5 0\n0 1\n100",    'expected_output' => "eigenvalue: 5.0\neigenvector: 1.0 0.0",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 0\n0 5\n100",    'expected_output' => "eigenvalue: 5.0\neigenvector: 0.0 1.0",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: rank-k SVD approximation ──────────────────────────────────
        $seed(8, [
            ['input' => "2 2 1\n3 0\n0 2",    'expected_output' => "3.0 0.0\n0.0 0.0",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2 2\n3 0\n0 2",    'expected_output' => "3.0 0.0\n0.0 2.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2 1\n1 0\n0 2",    'expected_output' => "0.0 0.0\n0.0 2.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2 1\n2 1\n1 2",    'expected_output' => "1.5 1.5\n1.5 1.5",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: normal equations ──────────────────────────────────────────
        $seed(9, [
            ['input' => "3 2\n1 1\n1 2\n1 3\n1\n2\n3",           'expected_output' => "0.0 1.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 1\n1\n2\n1\n2",                        'expected_output' => "1.0",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2\n1 0\n0 1\n1 1\n2\n3\n5",           'expected_output' => "2.0 3.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 1\n2\n4\n6\n4\n8\n12",               'expected_output' => "2.0",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: rolling mean per group ───────────────────────────────────
        $seed(10, [
            ['input' => "6\nA,1,10\nA,2,20\nA,3,30\nB,1,5\nB,2,15\nB,3,25\n2",  'expected_output' => "A,2,15.00\nA,3,25.00\nB,2,10.00\nB,3,20.00",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nX,1,100\nX,2,200\nX,3,300\n3",                        'expected_output' => "X,3,200.00",                                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nA,1,1\nA,2,3\nA,3,5\nA,4,7\n2",                     'expected_output' => "A,2,2.00\nA,3,4.00\nA,4,6.00",                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nZ,1,10\nZ,2,20\n3",                                  'expected_output' => "",                                               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: inner join ───────────────────────────────────────────────
        $seed(11, [
            ['input' => "3\n1,10,20\n2,30,40\n3,50,60\n2\n1,100,200\n3,300,400",   'expected_output' => "1,10,20,100,200\n3,50,60,300,400",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1,a,b\n2,c,d\n1\n2,x,y",                               'expected_output' => "2,c,d,x,y",                          'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1,a,b\n2,c,d\n2\n3,x,y\n4,p,q",                      'expected_output' => "",                                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5,v,w\n1\n5,x,y",                                     'expected_output' => "5,v,w,x,y",                          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: window functions ─────────────────────────────────────────
        $seed(12, [
            ['input' => "4\nA,30\nA,10\nB,20\nB,20",   'expected_output' => "A,30,1,30.00,75.00%\nA,10,2,40.00,25.00%\nB,20,1,20.00,50.00%\nB,20,1,40.00,50.00%",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nX,10\nX,10",               'expected_output' => "X,10,1,10.00,50.00%\nX,10,1,20.00,50.00%",                                               'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,50\nA,30\nA,20",        'expected_output' => "A,50,1,50.00,50.00%\nA,30,2,80.00,30.00%\nA,20,3,100.00,20.00%",                          'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nZ,100",                    'expected_output' => "Z,100,1,100.00,100.00%",                                                                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: fuzzy dedup ──────────────────────────────────────────────
        $seed(13, [
            ['input' => "4\ncat\nbat\nhat\ndog\n1",       'expected_output' => "['bat', 'cat', 'hat']\n['dog']",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nabc\nabd\nxyz\n1",            'expected_output' => "['abc', 'abd']\n['xyz']",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nhello\nhello\nworld\n0",      'expected_output' => "['hello', 'hello']\n['world']",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\naaa\nbbb\n2",                 'expected_output' => "['aaa', 'bbb']",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: multi-key aggregation ────────────────────────────────────
        $seed(14, [
            ['input' => "5\nEng,A,90\nEng,A,80\nEng,B,70\nHR,A,85\nHR,A,95",   'expected_output' => "Eng,A,2,85.00,90,80\nEng,B,1,70.00,70,70\nHR,A,2,90.00,95,85",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nX,Y,10\nX,Y,20",                                     'expected_output' => "X,Y,2,15.00,20,10",                                               'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,B,5\nA,C,10\nB,B,15",                             'expected_output' => "A,B,1,5.00,5,5\nA,C,1,10.00,10,10\nB,B,1,15.00,15,15",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nSales,North,100",                                    'expected_output' => "Sales,North,1,100.00,100,100",                                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: KDE ──────────────────────────────────────────────────────
        $seed(15, [
            ['input' => "3 1 2 3\n1.0\n2\n1.5\n2.5",     'expected_output' => "0.282095\n0.282095",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0\n1.0\n1\n0",                'expected_output' => "0.398942",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 0 1\n0.5\n1\n0.5",           'expected_output' => "0.483941",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 5\n1.0\n1\n5",               'expected_output' => "0.398942",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: violin summaries ─────────────────────────────────────────
        $seed(16, [
            ['input' => "4\nA 1\nA 3\nB 2\nB 4",   'expected_output' => "A: min=1.00, Q1=1.50, median=2.00, Q3=2.50, max=3.00, kde_peak=2.00\nB: min=2.00, Q1=2.50, median=3.00, Q3=3.50, max=4.00, kde_peak=3.00",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nX 5\nX 5",             'expected_output' => "X: min=5.00, Q1=5.00, median=5.00, Q3=5.00, max=5.00, kde_peak=5.00",                                                                           'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA 1\nA 2\nA 3",       'expected_output' => "A: min=1.00, Q1=1.50, median=2.00, Q3=2.50, max=3.00, kde_peak=2.00",                                                                            'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nZ 10\nZ 20\nZ 30\nZ 40", 'expected_output' => "Z: min=10.00, Q1=15.00, median=25.00, Q3=35.00, max=40.00, kde_peak=25.00",                                                                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Q-Q plot ─────────────────────────────────────────────────
        $seed(17, [
            ['input' => "3\n-1\n0\n1",       'expected_output' => "-1.0 -1.0\n0.0 0.0\n1.0 1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0",              'expected_output' => "0.0 0.0",                         'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n-1 1",          'expected_output' => "-0.6745 -1.0\n0.6745 1.0",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4",    'expected_output' => "-1.0 1.0\n-0.2533 2.0\n0.2533 3.0\n1.0 4.0",  'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q18: 2D KDE ───────────────────────────────────────────────────
        $seed(18, [
            ['input' => "2\n0 0\n2 2\n1.0\n1\n1 1",    'expected_output' => "0.029400",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0 0\n1.0\n1\n0 0",         'expected_output' => "0.159155",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0 0\n0.5\n1\n0 0",         'expected_output' => "0.636620",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 1\n-1 -1\n1.0\n1\n0 0",  'expected_output' => "0.046800",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Mann-Whitney U ───────────────────────────────────────────
        $seed(19, [
            ['input' => "1 2 3 4\n5 6 7 8",     'expected_output' => "U: 0.0\nz: -2.3094\nReject H0",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3\n1 2 3",         'expected_output' => "U: 4.5\nz: 0.0\nFail to reject H0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 3 5\n2 4 6",         'expected_output' => "U: 3.0\nz: -0.6547\nFail to reject H0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10 20 30\n1 2 3",      'expected_output' => "U: 9.0\nz: 2.4495\nReject H0",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Cohen's d + power ────────────────────────────────────────
        $seed(20, [
            ['input' => "4\n1 2 3 4\n5 6 7 8",        'expected_output' => "cohens_d: 2.8284\npower: 0.9992",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2 3 4\n1 2 3 4",        'expected_output' => "cohens_d: 0.0\npower: 0.0500",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 0\n10 10",              'expected_output' => "cohens_d: inf\npower: 1.0",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 2 3\n4 5 6",           'expected_output' => "cohens_d: 3.0\npower: 0.9969",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: KS test ──────────────────────────────────────────────────
        $seed(21, [
            ['input' => "1 2 3\n4 5 6",          'expected_output' => "D: 1.0\nReject H0",              'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3\n1 2 3",          'expected_output' => "D: 0.0\nFail to reject H0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 5\n3 7",             'expected_output' => "D: 0.5\nFail to reject H0",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 2 3 4 5\n6 7 8 9 10",'expected_output' => "D: 1.0\nReject H0",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: mutual information ───────────────────────────────────────
        $seed(22, [
            ['input' => "4\nA 1\nA 1\nB 2\nB 2",     'expected_output' => "0.6931",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nA 1\nA 2\nB 1\nB 2",     'expected_output' => "0.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nX Y\nX Y",               'expected_output' => "0.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nA 1\nB 2\nC 3\nD 4",    'expected_output' => "1.3863",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Bayesian A/B ─────────────────────────────────────────────
        $seed(23, [
            ['input' => "1 1\n100 60\n100 70",   'expected_output' => "0.8700",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1\n100 50\n100 50",   'expected_output' => "0.5000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 1\n100 10\n100 90",   'expected_output' => "1.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 1\n100 90\n100 10",   'expected_output' => "0.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: LOO target encoding ──────────────────────────────────────
        $seed(24, [
            ['input' => "4\nA 10\nA 20\nB 30\nA 30",   'expected_output' => "25.0\n20.0\n0.0\n15.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nX 10\nX 20",               'expected_output' => "20.0\n10.0",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA 5\nA 10\nA 15",         'expected_output' => "12.5\n10.0\n7.5",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nZ 100",                    'expected_output' => "0.0",                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: PCA pipeline ─────────────────────────────────────────────
        $seed(25, [
            ['input' => "3 2 1\n1 2\n2 4\n3 6",    'expected_output' => "eigenvector: 0.4472 0.8944\nprojected: -2.2361\n0.0\n2.2361\nvariance_ratio: 1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2 1\n1 0\n-1 0",        'expected_output' => "eigenvector: 1.0 0.0\nprojected: 1.0\n-1.0\nvariance_ratio: 1.0",                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2 1\n0 1\n0 2\n0 3",   'expected_output' => "eigenvector: 0.0 1.0\nprojected: -1.0\n0.0\n1.0\nvariance_ratio: 1.0",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2 2\n3 0\n0 2",         'expected_output' => "eigenvector: 1.0 0.0\neigenvector: 0.0 1.0\nprojected: 3.0 0.0\n0.0 2.0\nvariance_ratio: 1.0\nvariance_ratio: 1.0",  'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q26: interaction features ─────────────────────────────────────
        $seed(26, [
            ['input' => "2 2\na b\n1 2\n3 4",      'expected_output' => "a b a_b\n1.00 2.00 2.00\n3.00 4.00 12.00",               'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2\nx y\n2 3",           'expected_output' => "x y x_y\n2.00 3.00 6.00",                                'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3\na b c\n1 2 3\n4 5 6",'expected_output' => "a b c a_b a_c b_c\n1.00 2.00 3.00 2.00 3.00 6.00\n4.00 5.00 6.00 20.00 24.00 30.00",  'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 1\nf\n5",               'expected_output' => "f\n5.00",                                                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: freq encoding smoothing ──────────────────────────────────
        $seed(27, [
            ['input' => "5\nA 10\nA 20\nB 30\nB 40\nC 50\n10",   'expected_output' => "A: 18.3333\nB: 25.0\nC: 28.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nX 10\nY 20\n0",                       'expected_output' => "X: 10.0\nY: 20.0",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA 1\nA 1\nA 1\n100",                 'expected_output' => "A: 1.0",                          'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nZ 50\n5",                             'expected_output' => "Z: 50.0",                         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: RFE ─────────────────────────────────────────────────────
        $seed(28, [
            ['input' => "4 3 2\n1 2 3 10\n2 4 5 20\n3 6 7 30\n4 8 9 40",   'expected_output' => "features: [1, 2]\ncorrelations: 1.0 1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2 1\n1 2 10\n2 4 20\n3 1 30",                    'expected_output' => "features: [0]\ncorrelations: 1.0",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 2 1\n1 10 100\n2 20 200\n3 30 300\n4 40 400",   'expected_output' => "features: [0]\ncorrelations: 1.0",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2 2\n1 2 5\n3 4 10",                            'expected_output' => "features: [0, 1]\ncorrelations: 1.0 1.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: MI matrix ────────────────────────────────────────────────
        $seed(29, [
            ['input' => "4 2\nA 1\nA 1\nB 2\nB 2",   'expected_output' => "0.6931 0.6931\n0.6931 0.6931",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 2\nA 1\nA 2\nB 1\nB 2",   'expected_output' => "0.0 0.0\n0.0 0.0",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\nX Y\nX Y",             'expected_output' => "0.0 0.0\n0.0 0.0",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 2\nA 1\nB 2\nC 3",       'expected_output' => "1.0986 1.0986\n1.0986 1.0986",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Cramer's V ───────────────────────────────────────────────
        $seed(30, [
            ['input' => "4\nA 1\nA 2\nB 1\nB 2",   'expected_output' => "0.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nA 1\nA 1\nB 2\nB 2",   'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA 1\nB 2\nC 3",        'expected_output' => "1.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nA 1\nA 1\nA 2\nB 2",  'expected_output' => "0.5774", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: VIF ─────────────────────────────────────────────────────
        $seed(31, [
            ['input' => "4 2\n1 2\n2 4\n3 6\n4 8",    'expected_output' => "VIF(col0): inf\nVIF(col1): inf",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 0\n2 0\n3 0",         'expected_output' => "VIF(col0): 1.0\nVIF(col1): 1.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 2\n1 4\n2 3\n3 2\n4 1",   'expected_output' => "VIF(col0): inf\nVIF(col1): inf",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 2\n1 0\n0 1\n1 1",        'expected_output' => "VIF(col0): 1.25\nVIF(col1): 1.25", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: ANOVA ────────────────────────────────────────────────────
        $seed(32, [
            ['input' => "2\n1 2 3\n7 8 9",      'expected_output' => "F: 54.0\nReject H0",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2 3\n1 2 3",      'expected_output' => "F: 0.0\nFail to reject H0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2\n3 4\n5 6",    'expected_output' => "F: 10.0\nReject H0",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10 10 10\n11 11 11",'expected_output' => "F: inf\nReject H0",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Ridge regression GD ──────────────────────────────────────
        $seed(33, [
            ['input' => "3 1\n1 2\n2 4\n3 6\n0.1\n0.0\n100",    'expected_output' => "theta: 0.0 2.0\nmse: 0.0",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 1\n0 0\n1 1\n0.1\n0.0\n200",         'expected_output' => "theta: 0.0 1.0\nmse: 0.0",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 1\n1 2\n2 4\n3 6\n0.1\n1.0\n500",   'expected_output' => "theta: 0.1011 1.8977\nmse: 0.0041", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n1 2 5\n3 4 11\n0.01\n0.0\n1000", 'expected_output' => "theta: 0.0 1.0 2.0\nmse: 0.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: logistic regression ──────────────────────────────────────
        $seed(34, [
            ['input' => "4 2\n0 0 0\n0 1 0\n1 0 0\n1 1 1\n0.5\n1000",   'expected_output' => "theta: -5.0963 2.5682 2.5682\naccuracy: 1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 1\n0 0\n1 1\n0.5\n500",                        'expected_output' => "theta: -2.8551 5.7101\naccuracy: 1.0",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 1\n1 1\n2 1\n3 0\n4 0\n0.1\n1000",           'expected_output' => "theta: 5.6204 -2.2482\naccuracy: 1.0",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 1\n1 0\n2 0\n3 1\n4 1\n0.1\n1000",           'expected_output' => "theta: -5.6204 2.2482\naccuracy: 1.0",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: decision tree ────────────────────────────────────────────
        $seed(35, [
            ['input' => "4 1 2\n1 0\n2 0\n3 1\n4 1\n2\n2.5\n3.5",   'expected_output' => "0\n1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 1 1\n1 0\n2 0\n3 1\n4 1\n1\n2.5",        'expected_output' => "0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 2 2\n1 1 0\n1 2 0\n2 1 1\n2 2 1\n2\n1.5 1.5\n1.5 2.5", 'expected_output' => "0\n0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 1 5\n0 0\n10 1\n1\n5",                    'expected_output' => "0",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: strat k-fold CV ──────────────────────────────────────────
        $seed(36, [
            ['input' => "6 1 3\n0 0\n1 1\n2 0\n3 1\n4 0\n5 1",     'expected_output' => "Fold 1: 1.0\nFold 2: 1.0\nFold 3: 1.0\nMean: 1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 1 2\n0 0\n1 1\n2 0\n3 1",               'expected_output' => "Fold 1: 1.0\nFold 2: 1.0\nMean: 1.0",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 1 2\n1 0\n2 0\n3 1\n4 1",              'expected_output' => "Fold 1: 1.0\nFold 2: 1.0\nMean: 1.0",                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 1 2\n0 0\n1 1",                        'expected_output' => "Fold 1: 1.0\nFold 2: 1.0\nMean: 1.0",                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: ROC AUC ──────────────────────────────────────────────────
        $seed(37, [
            ['input' => "4\n1 0.9\n0 0.4\n1 0.8\n0 0.3",   'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 0.1\n0 0.9\n1 0.2\n0 0.8",   'expected_output' => "0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 0.5\n0 0.5\n1 0.5\n0 0.5",   'expected_output' => "0.5",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1 0.9\n1 0.8\n0 0.7\n0 0.6\n1 0.5\n0 0.4", 'expected_output' => "0.8889", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q38: full K-Means ─────────────────────────────────────────────
        $seed(38, [
            ['input' => "4 2 100\n1 1\n1 2\n8 1\n8 2",     'expected_output' => "centroids: (1.0, 1.5) (8.0, 1.5)\nassignments: 0 0 1 1\niterations: 2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2 10\n0 0\n10 10",              'expected_output' => "centroids: (0.0, 0.0) (10.0, 10.0)\nassignments: 0 1\niterations: 1",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 2 100\n1 0\n2 0\n8 0\n9 0",    'expected_output' => "centroids: (1.5, 0.0) (8.5, 0.0)\nassignments: 0 0 1 1\niterations: 2",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 1 100\n0 0\n5 0\n10 0",        'expected_output' => "centroids: (0.0, 0.0) (5.0, 0.0)\nassignments: 0 1 1\niterations: 2",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: DBSCAN ───────────────────────────────────────────────────
        $seed(39, [
            ['input' => "6 1 2 3 10 11 12\n1.5\n2",    'expected_output' => "0 0 0 1 1 1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 1 2 3\n1.5\n2",             'expected_output' => "0 0 0",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "5 1 2 10 11 100\n1.5\n2",     'expected_output' => "0 0 1 1 -1",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 0 10 20\n1.5\n2",           'expected_output' => "-1 -1 -1",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: hierarchical clustering ──────────────────────────────────
        $seed(40, [
            ['input' => "4 1 2 8 9",   'expected_output' => "Merge 0 and 1 at distance 1.0\nMerge 2 and 3 at distance 1.0\nMerge 0-1 and 2-3 at distance 6.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 0 5",       'expected_output' => "Merge 0 and 1 at distance 5.0",                                                                      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 1 2 3",     'expected_output' => "Merge 0 and 1 at distance 1.0\nMerge 0-1 and 2 at distance 1.0",                                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 1 10 11 20",'expected_output' => "Merge 1 and 2 at distance 1.0\nMerge 0 and 1-2 at distance 9.0\nMerge 0-1-2 and 3 at distance 9.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q41: elbow method ─────────────────────────────────────────────
        $seed(41, [
            ['input' => "6 5\n1 2 3 10 11 12",   'expected_output' => "k=1: 60.5\nk=2: 1.0\nk=3: 0.0\nk=4: 0.0\nk=5: 0.0\noptimal_k: 2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 3\n1 2 10 11",        'expected_output' => "k=1: 25.25\nk=2: 0.5\nk=3: 0.0\noptimal_k: 2",                        'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 3\n0 5 10",           'expected_output' => "k=1: 33.3333\nk=2: 6.25\nk=3: 0.0\noptimal_k: 2",                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n0 100",            'expected_output' => "k=1: 2500.0\nk=2: 0.0\noptimal_k: 2",                                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: ARIMA ────────────────────────────────────────────────────
        $seed(42, [
            ['input' => "1 3 5 7 9\n1 1 0 2",      'expected_output' => "11.0\n13.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 4 6 8\n1 1 0 1",        'expected_output' => "10.0",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 4 9 16 25\n2 1 0 2",   'expected_output' => "36.0\n49.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "10 20 30 40\n0 0 0 2",   'expected_output' => "25.0\n25.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: rolling z-score anomaly ──────────────────────────────────
        $seed(43, [
            ['input' => "1 2 3 4 100 6 7\n3\n2.0",   'expected_output' => "4",         'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3 4 5\n3\n2.0",         'expected_output' => "None",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "5 5 5 5 100\n3\n1.0",       'expected_output' => "4",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 1 1 50 1 1\n2\n2.0",      'expected_output' => "3",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Holt-Winters ─────────────────────────────────────────────
        $seed(44, [
            ['input' => "10 20 30 40\n0.5 0.5 2",   'expected_output' => "50.0\n60.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3 4\n1.0 1.0 1",       'expected_output' => "5.0",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "100 200 300\n0.5 0.5 2",   'expected_output' => "400.0\n500.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 5 5 5\n0.5 0.5 3",       'expected_output' => "5.0\n5.0\n5.0",'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q45: PACF ─────────────────────────────────────────────────────
        $seed(45, [
            ['input' => "1 2 3 4 5\n2",   'expected_output' => "lag 1: 1.0\nlag 2: 0.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1 1 1\n1",     'expected_output' => "lag 1: 0.0",                 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 -1 1 -1 1\n2",'expected_output' => "lag 1: -1.0\nlag 2: 0.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 0 1 0 1\n2",   'expected_output' => "lag 1: -0.5\nlag 2: 0.5",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: BM25 ─────────────────────────────────────────────────────
        $seed(46, [
            ['input' => "3\nthe cat sat on the mat\nthe dog ran in the park\na cat and a dog\ncat", 'expected_output' => "the cat sat on the mat\na cat and a dog\nthe dog ran in the park", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nhello world\nbye world\nhello",                                     'expected_output' => "hello world\nbye world",                                           'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\napple orange\nbanana\napple\napple",                                'expected_output' => "apple\napple orange\nbanana",                                      'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\na b c\nd e f\nz",                                                   'expected_output' => "a b c\nd e f",                                                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Word2Vec CBOW approx ─────────────────────────────────────
        $seed(47, [
            ['input' => "3\ndog barks loud\ncat meows soft\ndog runs fast\n1\ndog", 'expected_output' => "barks: 1.0\nruns: 1.0\nloud: 1.0\nfast: 1.0\ncat: 0.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\na b c\na b d\n1\na",                                    'expected_output' => "b: 1.0\nc: 0.0\nd: 0.0",                                'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nx y z\nx y w\n1\ny",                                    'expected_output' => "x: 1.0\nz: 1.0\nw: 1.0",                                'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nhi there\nhello there\nhi friend\n1\nthere",            'expected_output' => "hi: 1.0\nhello: 1.0\nfriend: 0.0",                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: LSA ──────────────────────────────────────────────────────
        $seed(48, [
            ['input' => "3\ncat dog\ndog fish\ncat cat\ncat\n1",    'expected_output' => "2\n0\n1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\na b\nc d\na\n1",                        'expected_output' => "0\n1",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\napple orange\nbanana\napple\napple\n1", 'expected_output' => "2\n0\n1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nx y\nz w\nz\n1",                        'expected_output' => "1\n0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: n-gram language model ────────────────────────────────────
        $seed(49, [
            ['input' => "hello world\n2\nworld", 'expected_output' => "log_prob: -6.7334\nperplexity: 3.9624", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "abc abc\n1\nc",         'expected_output' => "log_prob: -1.3863\nperplexity: 4.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "test test\n2\nest",     'expected_output' => "log_prob: -4.5000\nperplexity: 4.4817", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "a b a b\n1\nb",         'expected_output' => "log_prob: -0.6931\nperplexity: 2.0000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: TF-IDF + Logistic Regression ─────────────────────────────
        $seed(50, [
            ['input' => "4\npos|great amazing wonderful\npos|excellent fantastic\nneg|terrible horrible bad\nneg|awful dreadful\n2\nwonderful excellent\nhorrible terrible", 'expected_output' => "pos\nneg", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1|yes yes\n0|no no\n2\nyes\nno",                                                                                                                   'expected_output' => "1\n0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\npos|good\npos|nice\nneg|bad\nneg|sad\n2\ngood nice\nsad bad",                                                                                      'expected_output' => "pos\nneg", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nA|cat\nA|dog\nB|car\nB|bus\n2\ncat dog\ncar bus",                                                                                                  'expected_output' => "A\nB",     'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('Module 3 Professional coding challenges and test cases seeded successfully!');
    }
}