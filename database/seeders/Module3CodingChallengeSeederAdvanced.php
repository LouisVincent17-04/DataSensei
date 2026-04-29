<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 3 — Introduction to Data Science (Advanced / Level 3) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Advanced tier
 *   2. coding_questions    — 50 questions covering advanced Data Science algorithms
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mapped to lessons 293–303):
 *   L1.1  Data pipelines — ETL, schema validation, data quality scoring
 *   L1.2  NumPy — SVD concepts, eigendecomposition, broadcasting tricks
 *   L1.3  Pandas — window functions, multi-level aggregation, reshaping
 *   L1.4  Visualization — kernel density estimation, ECDF
 *   L1.5  Statistics — bootstrapping, confidence intervals, ANOVA
 *   L1.6  Feature Engineering — target encoding, WoE, feature selection
 *   L1.7  EDA — dimensionality analysis, mutual information
 *   L1.8  ML — logistic regression, decision tree split criteria, cross-validation
 *   L1.9  Unsupervised — DBSCAN concepts, hierarchical clustering
 *   L1.10 Time Series — ARIMA components, STL decomposition
 *   L1.11 NLP — word embeddings concepts, BM25, topic modeling LDA concepts
 *
 * Difficulty: Advanced — pure Python; research-level algorithms requiring
 * deep understanding of mathematics and efficient implementation.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module3CodingChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (! $category) {
            $this->command->error('Advanced category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 3 — Introduction to Data Science (Advanced) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Introduction to Data Science',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Master advanced Data Science algorithms in pure Python — implement SVD-based recommendations, gradient-boosted trees, DBSCAN, ARIMA differencing, BM25 retrieval, bootstrap confidence intervals, and more. Problems demand mathematical rigour and algorithmic efficiency.',
                'time_limit_seconds' => 2400,
                'base_xp'            => 3000,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Data Pipelines & Quality (Q1–Q4)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Read `n` rows of CSV data (format: `id,value`). Compute a **data quality score** (0.0 – 1.0) defined as:

score = (1 − missing_rate) × (1 − duplicate_rate)

where:
- missing_rate = count of NA values / n
- duplicate_rate = count of duplicate (id, value) rows / n

Print the score rounded to 4 decimal places.

Example:
```
Input:
6
1,10
2,NA
3,30
1,10
4,NA
5,50
Output: 0.2778
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().strip() for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 300,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Implement a **streaming mean and variance** calculator (Welford's online algorithm). Read `n` numbers one at a time and after each number print the current mean and population variance, both rounded to 4 decimal places, space-separated.

Welford update:
- delta = x − mean
- mean += delta / count
- delta2 = x − mean
- M2 += delta × delta2
- variance = M2 / count

Example:
```
Input:
4
2
4
4
4
Output:
2.0000 0.0000
3.0000 1.0000
3.3333 0.8889
3.5000 0.7500
```
MD,
                'starter_code'        => "n = int(input())\n# Implement Welford's algorithm\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 350,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read `n` rows of CSV (format: `col1,col2,col3`). Detect **schema violations**: a row is invalid if any field is not parseable as a float. Print the count of invalid rows.

Example:
```
Input:
5
1,2,3
4,5,six
7,8,9
NA,10,11
0,0,0
Output: 2
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().strip() for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 250,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Implement a **Z-score based anomaly detector** with drift detection. Read `n` numbers in chunks of `w` (window size given on the last line). For each window, flag a point as anomalous if its Z-score within that window exceeds threshold `t` (given on the second-to-last line). Print `1` for anomalous, `0` for normal, one per number, one per line (numbers not in a complete window get `0`).

Example:
```
Input:
8
1
2
3
4
5
6
7
100
3
3
Output:
0
0
0
0
0
0
0
1
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnums = [float(input()) for _ in range(n)]\nt = float(input())\nw = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Linear Algebra & Decompositions (Q5–Q9)
            // ═══════════════════════════════════════════════════════════════

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Compute the **eigenvalues** of a 2×2 symmetric matrix analytically.

For matrix [[a, b], [b, d]], eigenvalues are:
λ = ((a+d) ± sqrt((a-d)² + 4b²)) / 2

Read a, b, d (one per line). Print the two eigenvalues in descending order, rounded to 4 decimal places.

Example:
```
Input:
4
2
2
Output:
5.4142
0.5858
```
MD,
                'starter_code'        => "import math\na = float(input())\nb = float(input())\nd = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 300,
            ],

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Implement **power iteration** to find the dominant eigenvector of a 2×2 symmetric matrix. Read the matrix (2 rows of 2 space-separated floats), then `k` iterations. Start with [1.0, 0.0]. After each iteration: multiply the matrix by the vector, then normalize. Print the final unit eigenvector rounded to 4 decimal places, space-separated.

Example:
```
Input:
3 1
1 3
100
Output:
0.7071 0.7071
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nM = [row1, row2]\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 350,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Implement **low-rank matrix approximation** using the truncated SVD concept for a 2×2 matrix. Given a symmetric PSD matrix M, compute:
1. Its 2 eigenvalues (λ₁ ≥ λ₂) and eigenvectors
2. Rank-1 approximation: M̂ = λ₁ × v₁ × v₁ᵀ

Read the matrix (2 rows), print the rank-1 approximation matrix, values rounded to 4 decimal places, rows space-separated.

Example:
```
Input:
2 1
1 2
Output:
1.5000 1.5000
1.5000 1.5000
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nM = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Implement **collaborative filtering** using item-item cosine similarity. Read a user-item rating matrix (format: first line `users items`, then `users` rows of `items` space-separated ratings; 0 = unrated). For a query `(user, item)` (last line), predict the rating using the weighted average of other rated items by the same user, weighted by item-item similarity.

Print the predicted rating rounded to 2 decimal places.

Example:
```
Input:
3 3
5 3 0
4 0 4
0 3 2
0 2
Output: 3.32
```
MD,
                'starter_code'        => "import math\nusers, items = map(int, input().split())\nmatrix = [list(map(float, input().split())) for _ in range(users)]\nu, i = map(int, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 450,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Compute the **Moore-Penrose pseudoinverse** of an `n×2` matrix using the formula:

A⁺ = (AᵀA)⁻¹Aᵀ

where (AᵀA)⁻¹ is the 2×2 matrix inverse. Read `n` rows of 2 floats. Print the 2×n pseudoinverse matrix, rows space-separated, values rounded to 4 decimal places.

Example:
```
Input:
3
1 1
1 2
1 3
Output:
1.0000 0.3333 -0.3333
-0.5000 0.0000 0.5000
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 450,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Advanced Aggregation & Window Functions (Q10–Q13)
            // ═══════════════════════════════════════════════════════════════

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read `n` rows of CSV (format: `date,value`). Compute the **rolling 3-period standard deviation** (population std, window=3) for each row. For the first 2 rows print `NA`. Print remaining values rounded to 4 decimal places.

Example:
```
Input:
5
2024-01,10
2024-02,20
2024-03,30
2024-04,40
2024-05,50
Output:
NA
NA
8.1650
8.1650
8.1650
```
MD,
                'starter_code'        => "import math\nn = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 300,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Read `n` rows of CSV (format: `name,dept,salary`). For each employee, compute their **salary percentile rank** within their department (percent of colleagues they earn more than, exclusive, 0–100). Print `name: rank` sorted by name, rounded to 2 decimal places.

percentile_rank(x) = 100 × (count of dept members with salary < x) / (dept size)

Example:
```
Input:
4
Alice,Eng,90000
Bob,Eng,80000
Carol,Eng,95000
Dave,HR,60000
Output:
Alice: 33.33
Bob: 0.00
Carol: 66.67
Dave: 0.00
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 350,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Read `n` rows of event log CSV (format: `user_id,timestamp,event`). Compute the **session count per user**: a new session starts when the gap between consecutive events for the same user exceeds `T` seconds (given on the last line). Timestamps are integers. Print `user_id: session_count` sorted by user_id.

Example:
```
Input:
6
u1,1,click
u1,2,click
u1,100,click
u2,5,click
u2,6,click
u2,200,click
50
Output:
u1: 2
u2: 2
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 350,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Read `n` rows of CSV (format: `id,value`). Compute the **cumulative distribution function** (empirical CDF): for each unique value v, ECDF(v) = count(x ≤ v) / n. Print each unique value and its ECDF, sorted by value ascending, rounded to 4 decimal places. Format: `value: ecdf`

Example:
```
Input:
5
a,1
b,2
c,2
d,3
e,4
Output:
1: 0.2000
2: 0.6000
3: 0.8000
4: 1.0000
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Statistics — Bootstrap & ANOVA (Q14–Q18)
            // ═══════════════════════════════════════════════════════════════

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Implement **bootstrap confidence interval** for the mean. Read `n` numbers, then number of bootstrap samples `B`, and confidence level `alpha` (e.g. 0.95). Use a **fixed seed** (seed=42) and `random.choices`. Print the lower and upper bounds of the (alpha×100)% CI, rounded to 2 decimal places.

Example:
```
Input:
5
1
2
3
4
5
1000
0.95
Output:
1.80 4.20
```
MD,
                'starter_code'        => "import random\nrandom.seed(42)\nn = int(input())\nnums = [float(input()) for _ in range(n)]\nB = int(input())\nalpha = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Compute the **one-way ANOVA F-statistic**. Read `k` groups (first line is `k`; then for each group: first line is `n_i`, then `n_i` numbers). Print the F-statistic rounded to 4 decimal places.

F = (SS_between / (k-1)) / (SS_within / (N-k))

where N = total observations, SS_between = Σ nᵢ(ȳᵢ − ȳ)², SS_within = Σᵢ Σⱼ (yᵢⱼ − ȳᵢ)²

Example:
```
Input:
3
3
2
4
6
3
1
3
5
3
8
9
10
Output: 18.6667
```
MD,
                'starter_code'        => "k = int(input())\ngroups = []\nfor _ in range(k):\n    ni = int(input())\n    groups.append([float(input()) for _ in range(ni)])\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Implement the **Kolmogorov-Smirnov statistic** (two-sample). Read two samples. Compute:

D = max |ECDF₁(x) − ECDF₂(x)| over all observed values

Print D rounded to 4 decimal places.

Example:
```
Input:
4
1
2
3
4
4
2
3
5
6
Output: 0.5000
```
MD,
                'starter_code'        => "n1 = int(input())\nA = sorted([float(input()) for _ in range(n1)])\nn2 = int(input())\nB = sorted([float(input()) for _ in range(n2)])\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Compute a **kernel density estimate** at a query point using the Gaussian kernel. Read `n` data points, bandwidth `h`, and query point `x_q`. Print the KDE value rounded to 6 decimal places.

KDE(x) = (1 / (n × h)) × Σ K((x − xᵢ) / h)
K(u) = (1 / sqrt(2π)) × exp(−u² / 2)

Example:
```
Input:
3
1
2
3
1.0
2.0
Output: 0.397357
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndata = [float(input()) for _ in range(n)]\nh = float(input())\nx_q = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 350,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Compute **Cohen's d** effect size between two samples.

d = (mean_A − mean_B) / pooled_std

pooled_std = sqrt(((nA-1)×sA² + (nB-1)×sB²) / (nA + nB - 2))   (sample variance, ddof=1)

Read two samples. Print d rounded to 4 decimal places.

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
Output: 0.5774
```
MD,
                'starter_code'        => "import math\nnA = int(input())\nA = [float(input()) for _ in range(nA)]\nnB = int(input())\nB = [float(input()) for _ in range(nB)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Advanced Feature Engineering (Q19–Q23)
            // ═══════════════════════════════════════════════════════════════

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Implement **target encoding**. Read `n` rows (format: `category,target` where target is 0 or 1). For each category, compute the mean target rate. Then read a test category and print its encoded value rounded to 4 decimal places. If unseen, print the global mean rounded to 4 decimal places.

Example:
```
Input:
6
A,1
A,1
B,0
B,1
C,0
C,0
A
Output: 1.0000
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split(',') for _ in range(n)]\ntest_cat = input().strip()\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 300,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Compute **mutual information** between a categorical feature X and binary target Y. Read `n` rows (format: `x,y`). Use the formula:

MI(X;Y) = Σₓ Σᵧ P(x,y) × log(P(x,y) / (P(x) × P(y)))

Print the result rounded to 4 decimal places. Use natural log.

Example:
```
Input:
4
A,0
A,1
B,0
B,1
Output: 0.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Implement **Weight of Evidence (WoE)** encoding. Read `n` rows (format: `category,target` where target 0/1). For each category compute:

WoE = ln(Distribution_of_Events / Distribution_of_Non_Events)

Distribution_of_Events = (count of 1s in category) / (total 1s)
Distribution_of_Non_Events = (count of 0s in category) / (total 0s)

Print each category and its WoE sorted alphabetically, rounded to 4 decimal places. Format: `category: woe`

Example:
```
Input:
6
A,1
A,1
B,0
B,1
C,0
C,0
Output:
A: 0.6931
B: 0.0000
C: -0.6931
```
MD,
                'starter_code'        => "import math\nn = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Implement **LASSO feature selection** via coordinate descent for one step. Read `n` training pairs `(x, y)`, regularization parameter `lambda`, and number of iterations. Run coordinate descent for simple LASSO (single feature) updating:

w = sign(correlation) × max(0, |correlation| − lambda) / (x · x / n)

where correlation = (1/n) × Σ xᵢ × (yᵢ − 0) (assume b=0 for simplicity, not updating intercept).

Starting from w=0, perform `iters` iterations. Print final w rounded to 4 decimal places.

Example:
```
Input:
3
1 2
2 4
3 6
0.5
100
Output: 1.8333
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\nlambda_ = float(input())\niters = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 450,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Compute **information gain** for a binary split. Read `n` rows (format: `feature,target`). Feature is numeric. Read a split threshold `t` on the last line. Compute information gain:

IG = H(parent) − (nL/n × H(L) + nR/n × H(R))

where H(S) = −p × log2(p) − (1−p) × log2(1−p), p = fraction of 1s.

Print IG rounded to 4 decimal places.

Example:
```
Input:
6
1,0
2,0
3,0
7,1
8,1
9,1
5
Output: 1.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nrows = [tuple(map(float, input().split(','))) for _ in range(n)]\nt = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Machine Learning Algorithms (Q24–Q30)
            // ═══════════════════════════════════════════════════════════════

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Implement **logistic regression** via gradient descent. Read `n` training pairs `(x, y)` where y ∈ {0,1}, learning rate `alpha`, and `iters`. Starting from w=0, b=0, update:

σ(z) = 1 / (1 + exp(−z))
∂L/∂w = (1/n) × Σ (σ(w×xᵢ + b) − yᵢ) × xᵢ
∂L/∂b = (1/n) × Σ (σ(w×xᵢ + b) − yᵢ)

Print final w and b rounded to 4 decimal places.

Example:
```
Input:
4
1 0
2 0
5 1
6 1
0.1
1000
Output:
w: 1.1420
b: -4.2820
```
MD,
                'starter_code'        => "import math\nn = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\nalpha = float(input())\niters = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 450,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Implement a **Decision Tree stump** (depth=1). Find the best split threshold for a single numeric feature that minimises weighted Gini impurity. Read `n` rows `(x, y)` where y ∈ {0,1}. Print the best threshold, Gini impurity before split, and Gini impurity after split, all rounded to 4 decimal places.

Gini(S) = 1 − p² − (1−p)²

Format:
```
threshold: T
gini_before: G
gini_after: G
```

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
Output:
threshold: 5.0
gini_before: 0.5000
gini_after: 0.0000
```
MD,
                'starter_code'        => "n = int(input())\nrows = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 450,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Implement **k-fold cross-validation** (k=5) for a simple 1D nearest-neighbor classifier. Read `n` data points (format: `x label`). Shuffle with seed=42, split into 5 folds, and compute mean accuracy. Print the mean accuracy rounded to 4 decimal places.

For each fold: train on remaining 4 folds, predict each test point as the label of its nearest training point.

Example:
```
Input:
10
1 A
2 A
3 A
4 A
5 A
6 B
7 B
8 B
9 B
10 B
Output: 1.0000
```
MD,
                'starter_code'        => "import random\nrandom.seed(42)\nn = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    data.append((float(parts[0]), parts[1]))\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 450,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Implement **Naive Bayes classifier** (Gaussian) for 1D features. Read `n` training rows (format: `x label`). Then read a test point and print the predicted label. Use the Gaussian likelihood:

P(x | class) = (1 / (σ × sqrt(2π))) × exp(−(x−μ)² / (2σ²))

Use equal class priors. Break ties alphabetically.

Example:
```
Input:
6
1 A
2 A
3 A
7 B
8 B
9 B
5
Output: A
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    data.append((float(parts[0]), parts[1]))\nx_test = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Implement **AdaBoost** for binary classification with `T` rounds of weak learners (decision stumps on 1D feature). y ∈ {-1, +1}. Read `n` training pairs, then `T`. Print the final ensemble prediction (sign of weighted sum) for each training point, one per line (+1 or -1).

Example:
```
Input:
6
1 -1
2 -1
3 -1
7 1
8 1
9 1
5
Output:
-1
-1
-1
1
1
1
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndata = [tuple(map(float, input().split())) for _ in range(n)]\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 500,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Compute the **ROC AUC** score for a binary classifier. Read `n` pairs `(actual, score)`. Compute the AUC using the trapezoidal rule over the ROC curve (TPR vs FPR at varying thresholds). Print AUC rounded to 4 decimal places.

Example:
```
Input:
4
1 0.9
1 0.8
0 0.3
0 0.1
Output: 1.0000
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Implement **Ridge Regression** using the closed-form solution:

w = (XᵀX + λI)⁻¹ Xᵀy

Read `n` rows `(x, y)` and regularization `lambda`. Use design matrix X = [[1, x₁], [1, x₂], ...] (with intercept). Print final weights [b, w] rounded to 4 decimal places.

Example:
```
Input:
3
1 2
2 4
3 6
0
Output:
b: 0.0000
w: 2.0000
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\nlambda_ = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 450,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Advanced Clustering (Q31–Q34)
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Implement **DBSCAN** for 1D data. Read `n` points, then `eps` and `min_samples`. Print cluster label for each point (-1 for noise, 0-indexed cluster ids), one per line.

Example:
```
Input:
7
1
2
3
10
11
12
100
2
2
Output:
0
0
0
1
1
1
-1
```
MD,
                'starter_code'        => "n = int(input())\npoints = [float(input()) for _ in range(n)]\neps = float(input())\nmin_samples = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 450,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Implement **agglomerative hierarchical clustering** (single linkage) for 1D data. Read `n` points and target number of clusters `k`. Merge the two closest clusters (minimum distance between any pair of points in different clusters) until `k` clusters remain. Print the cluster assignment (0-indexed) for each original point in original order.

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
Output:
0
0
0
1
1
1
```
MD,
                'starter_code'        => "n = int(input())\npoints = [float(input()) for _ in range(n)]\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 450,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Implement **Gaussian Mixture Model** (1D, 2 components) using the EM algorithm. Read `n` points, then `iters`. Initialize with μ₁ = min, μ₂ = max, σ₁ = σ₂ = std, π₁ = π₂ = 0.5. Print final μ₁, μ₂ (sorted ascending) and π₁, π₂ rounded to 4 decimal places.

E-step: r_ik = π_k × N(x_i; μ_k, σ_k) / Σⱼ π_j × N(x_i; μ_j, σ_j)
M-step: μ_k = Σ r_ik × x_i / Σ r_ik; σ_k = sqrt(Σ r_ik × (x_i − μ_k)² / Σ r_ik); π_k = Σ r_ik / n

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
20
Output:
mu1: 2.0000
mu2: 8.0000
pi1: 0.5000
pi2: 0.5000
```
MD,
                'starter_code'        => "import math\nn = int(input())\npoints = [float(input()) for _ in range(n)]\niters = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 500,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Compute the **Davies-Bouldin Index** for a 1D clustering. Read `n` rows of `point cluster`. 

DB = (1/k) × Σᵢ max_{j≠i} (sᵢ + sⱼ) / dᵢⱼ

where sᵢ = mean intra-cluster distance to centroid, dᵢⱼ = |centroid_i − centroid_j|.

Print DB rounded to 4 decimal places.

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
Output: 0.2500
```
MD,
                'starter_code'        => "n = int(input())\ndata = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Time Series (Q35–Q38)
            // ═══════════════════════════════════════════════════════════════

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Implement **ARIMA(p=1, d=1, q=0)** parameter estimation via OLS on the differenced series. Read `n` values. Difference the series once (d=1). Fit AR(1): Δyₜ = φ × Δyₜ₋₁. Print φ rounded to 4 decimal places.

Example:
```
Input:
5
1
2
4
7
11
Output: 1.0000
```
MD,
                'starter_code'        => "n = int(input())\nseries = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Implement **Holt-Winters additive smoothing** (double exponential — trend). Read `n` values, alpha (level), and beta (trend). Initialize: L₁ = y₁, T₁ = y₂ − y₁. From t=2:

Lₜ = alpha × yₜ + (1 − alpha) × (Lₜ₋₁ + Tₜ₋₁)
Tₜ = beta × (Lₜ − Lₜ₋₁) + (1 − beta) × Tₜ₋₁
Fitted = Lₜ₋₁ + Tₜ₋₁

Print fitted values for t=2..n, rounded to 2 decimal places.

Example:
```
Input:
5
2
4
6
8
10
0.8
0.8
Output:
2.00
4.00
6.00
8.00
```
MD,
                'starter_code'        => "n = int(input())\nseries = [float(input()) for _ in range(n)]\nalpha = float(input())\nbeta = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 450,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Implement **STL-style trend extraction** using a centered moving average of period `p`. Then compute the **detrended series** (original − trend). Print the detrended values only for positions where the trend is defined (positions p//2 to n−p//2−1), rounded to 2 decimal places, one per line.

Example:
```
Input:
6
1
3
2
4
3
5
3
Output:
-0.50
0.50
-0.50
```
MD,
                'starter_code'        => "n = int(input())\nseries = [float(input()) for _ in range(n)]\np = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Compute the **partial autocorrelation** at lag 1 using the Yule-Walker equation:

PACF(1) = ACF(1)

and at lag 2:

PACF(2) = (ACF(2) − ACF(1)²) / (1 − ACF(1)²)

Read `n` time-series values. Print PACF(1) and PACF(2), each rounded to 4 decimal places.

ACF(k) = Σ((xₜ−x̄)(xₜ₋ₖ−x̄)) / Σ(xₜ−x̄)²  for t=k+1..n

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
Output:
PACF(1): 0.4000
PACF(2): -0.1429
```
MD,
                'starter_code'        => "n = int(input())\nseries = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 450,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Advanced NLP (Q39–Q43)
            // ═══════════════════════════════════════════════════════════════

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Implement **BM25** scoring. Read `n` documents, then a query. Print the BM25 score for each document rounded to 4 decimal places, one per line.

BM25(d, q) = Σ_{term t in q} IDF(t) × (tf(t,d) × (k1+1)) / (tf(t,d) + k1 × (1 − b + b × |d|/avgdl))

IDF(t) = ln((n − df(t) + 0.5) / (df(t) + 0.5) + 1)
k1 = 1.5, b = 0.75

Example:
```
Input:
3
data science is fun
machine learning is great
python data analysis
data science
Output:
1.3459
0.0000
0.4672
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndocs = [input().lower().split() for _ in range(n)]\nquery = input().lower().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 450,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Implement a **simple LDA** (Latent Dirichlet Allocation) topic model with 2 topics using collapsed Gibbs sampling. Read `n` documents (one per line), then `iters` iterations. Use seed=42. Initialize each word randomly to topic 0 or 1. Use symmetric Dirichlet priors α=0.1, β=0.1.

Print the top 3 words per topic (by count), sorted by count descending, ties alphabetically. Format:
```
Topic 0: word1 word2 word3
Topic 1: word1 word2 word3
```

Example (exact output depends on sampling, but format must match):
```
Input:
4
data science analysis
machine learning algorithm
data analysis statistics
machine learning data
20
Output:
Topic 0: data analysis science
Topic 1: machine learning data
```
MD,
                'starter_code'        => "import random\nrandom.seed(42)\nn = int(input())\ndocs = [input().lower().split() for _ in range(n)]\niters = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 500,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Implement **word co-occurrence matrix** construction. Read `n` documents and a context window size `w`. Build the co-occurrence matrix over the vocabulary (sorted alphabetically). Print the matrix row by row, space-separated integers.

Example:
```
Input:
2
the cat sat
the dog sat
1
Output:
0 1 0 1
1 0 1 1
0 1 0 1
1 1 1 0
```

Vocabulary (sorted): cat, dog, sat, the
Each pair counts how many times they appear within window w of each other.
MD,
                'starter_code'        => "n = int(input())\ndocs = [input().lower().split() for _ in range(n)]\nw = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Implement **Pointwise Mutual Information (PMI)** for word pairs. Read `n` documents and two target words (last two lines). Compute:

PMI(w1, w2) = log2(P(w1, w2) / (P(w1) × P(w2)))

where P(w1, w2) = (count of sentences containing both w1 and w2) / n, P(w) = (count of sentences containing w) / n.

Print PMI rounded to 4 decimal places. If either word has 0 frequency, print `undefined`.

Example:
```
Input:
4
the cat sat
the dog sat
cat sat here
dog ran away
cat
sat
Output: 0.4150
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndocs = [input().lower().split() for _ in range(n)]\nw1 = input().strip().lower()\nw2 = input().strip().lower()\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Implement **text summarization** via TF-IDF sentence scoring. Read `n` sentences (one per line), then `k` (number of sentences to extract). Score each sentence as the sum of TF-IDF scores of its words (using all sentences as documents). Print the top `k` sentences by score, in their original order.

Example:
```
Input:
4
data science is the future
machine learning drives data science
python is great for data analysis
statistics is fundamental to data science
2
Output:
data science is the future
machine learning drives data science
```
MD,
                'starter_code'        => "import math\nn = int(input())\nsentences = [input().lower() for _ in range(n)]\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 450,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: End-to-End Pipelines (Q44–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Implement a complete **data preprocessing pipeline**. Read `n` rows of CSV (format: `name,category,value`). Steps:
1. Impute missing `value` (NA) with column mean
2. Label-encode `category`
3. Z-score standardize `value` (after imputation)

Print the processed rows as CSV: `name,encoded_category,standardized_value`, rounded to 4 decimal places.

Example:
```
Input:
4
Alice,A,10
Bob,B,NA
Carol,A,30
Dave,B,20
Output:
Alice,0,-1.1547
Bob,1,0.0000
Carol,0,1.1547
Dave,1,0.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nrows = [input().split(',') for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Implement a **stratified train-test split** (80/20). Read `n` rows (format: `feature label`). Split ensuring each label class maintains its proportion. Use seed=42 for shuffling within each class. Print `train` and `test` as CSV, maintaining original label proportions.

Print the count of train and test samples per class, sorted alphabetically.

Format:
```
train:
label: count
test:
label: count
```

Example:
```
Input:
10
1 A
2 A
3 A
4 A
5 B
6 B
7 B
8 B
9 A
10 B
Output:
train:
A: 4
B: 4
test:
A: 1
B: 1
```
MD,
                'starter_code'        => "import random\nrandom.seed(42)\nn = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    data.append((float(parts[0]), parts[1]))\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Implement **stacking ensemble**. Two base models (KNN-1 and decision stump) are trained on a dataset. Their predictions on a held-out set become features for a logistic regression meta-learner. Read training data (`n` rows of `x y`), then test data (`m` rows of `x`). Print final ensemble predicted class (0 or 1) for each test point, one per line.

Use:
- KNN-1: nearest training point label
- Stump: threshold = mean of training x
- Meta-LR: train on [knn_pred, stump_pred] → y using closed-form (0.5 threshold on average)

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
3
5
3
8
Output:
0
0
1
```
MD,
                'starter_code'        => "import math\nn = int(input())\ntrain = [tuple(map(float, input().split())) for _ in range(n)]\nm = int(input())\ntest = [float(input()) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 500,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Implement **learning curves**. Read `n` training points (format: `x y`). For training set sizes [n//5, 2n//5, 3n//5, 4n//5, n], fit simple linear regression and compute training MAE and validation MAE on the remaining points. Print each size and its train/val MAE, rounded to 4 decimal places.

Format: `size: train_mae=X val_mae=Y`

Example:
```
Input:
10
1 1
2 2
3 3
4 4
5 5
6 6
7 7
8 8
9 9
10 10
Output:
2: train_mae=0.0000 val_mae=0.0000
4: train_mae=0.0000 val_mae=0.0000
6: train_mae=0.0000 val_mae=0.0000
8: train_mae=0.0000 val_mae=0.0000
10: train_mae=0.0000 val_mae=0.0000
```
MD,
                'starter_code'        => "n = int(input())\ndata = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 450,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Implement **hyperparameter tuning** via grid search for KNN (1D). Read `n` training points (format: `x y`), then `m` validation points. Test k ∈ {1, 3, 5}. Print the best k and its validation accuracy.

Format: `best_k: K accuracy: A` (A rounded to 4 decimal places)

Example:
```
Input:
10
1 A
2 A
3 A
4 A
5 A
6 B
7 B
8 B
9 B
10 B
4
3 A
7 B
4 A
6 B
Output:
best_k: 1 accuracy: 1.0000
```
MD,
                'starter_code'        => "n = int(input())\ntrain = []\nfor _ in range(n):\n    parts = input().split()\n    train.append((float(parts[0]), parts[1]))\nm = int(input())\nval = []\nfor _ in range(m):\n    parts = input().split()\n    val.append((float(parts[0]), parts[1]))\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 400,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Implement a **recommendation system** using user-based collaborative filtering. Read a user-item matrix (first line: `users items`, then users rows of space-separated ratings; 0 = unrated). For a query user `u` and item `i`, predict the rating using the top-`k` most similar users who have rated item `i`. Similarity = Pearson correlation over co-rated items (skip pairs with no co-rated items).

Read `u i k` on the last line. Print prediction rounded to 2 decimal places.

Example:
```
Input:
3 3
5 3 0
4 0 4
0 3 2
0 2 2
Output: 3.50
```
MD,
                'starter_code'        => "import math\nusers, items = map(int, input().split())\nmatrix = [list(map(float, input().split())) for _ in range(users)]\nparts = input().split()\nu, i, k = int(parts[0]), int(parts[1]), int(parts[2])\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 500,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Implement a full **NLP classification pipeline**. Read `n` labeled documents (format: `label|text`), then a test document (last line). Pipeline:
1. Tokenize (lowercase, split by spaces)
2. Remove stop words: {the, a, an, is, it, in, on, at, to, and, of, for}
3. Compute TF-IDF vectors
4. Classify test document using cosine similarity to each class centroid (average TF-IDF vector per class)
5. Print predicted label

Example:
```
Input:
4
sports|football game score win
sports|basketball team score points
tech|python code program data
tech|algorithm data structure code
football game score
Output: sports
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndocs = []\nfor _ in range(n):\n    line = input()\n    label, text = line.split('|', 1)\n    docs.append((label.strip(), text.lower().split()))\ntest = input().lower().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 1500,
                'base_xp'             => 500,
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

        // ── Q1: Data quality score ────────────────────────────────────────
        $seed(1, [
            ['input' => "6\n1,10\n2,NA\n3,30\n1,10\n4,NA\n5,50",    'expected_output' => '0.2778',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1,10\n2,20\n3,30\n4,40",                'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1,NA\n1,NA\n1,NA",                      'expected_output' => '0.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1,10\n1,10\n2,NA\n3,30",                'expected_output' => '0.3750',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Welford streaming ─────────────────────────────────────────
        $seed(2, [
            ['input' => "4\n2\n4\n4\n4",     'expected_output' => "2.0000 0.0000\n3.0000 1.0000\n3.3333 0.8889\n3.5000 0.7500",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0\n10",          'expected_output' => "0.0000 0.0000\n5.0000 25.0000",                                'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n1\n1",        'expected_output' => "1.0000 0.0000\n1.0000 0.0000\n1.0000 0.0000",                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5",              'expected_output' => "5.0000 0.0000",                                                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Schema violations ─────────────────────────────────────────
        $seed(3, [
            ['input' => "5\n1,2,3\n4,5,six\n7,8,9\nNA,10,11\n0,0,0",  'expected_output' => '2',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1,2,3\n4,5,6\n7,8,9",                    'expected_output' => '0',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nbad,row,here\n1,2,3",                     'expected_output' => '1',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nNA,NA,NA\n1,2,3\n4,5,x",                  'expected_output' => '2',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Z-score anomaly detector ──────────────────────────────────
        $seed(4, [
            ['input' => "8\n1\n2\n3\n4\n5\n6\n7\n100\n3\n3",   'expected_output' => "0\n0\n0\n0\n0\n0\n0\n1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n5\n5\n5\n5\n5\n5\n2\n2",           'expected_output' => "0\n0\n0\n0\n0\n0",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n2\n3\n4\n100\n2\n3",            'expected_output' => "0\n0\n0\n0\n1",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n11\n9\n50\n2\n2",              'expected_output' => "0\n0\n0\n1",               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Eigenvalues 2×2 ───────────────────────────────────────────
        $seed(5, [
            ['input' => "4\n2\n2",    'expected_output' => "5.4142\n0.5858",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0\n3",    'expected_output' => "3.0000\n3.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0\n2",    'expected_output' => "2.0000\n1.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1\n3",    'expected_output' => "5.4142\n2.5858",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Power iteration ───────────────────────────────────────────
        $seed(6, [
            ['input' => "3 1\n1 3\n100",   'expected_output' => "0.7071 0.7071",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 0\n0 2\n100",   'expected_output' => "1.0000 0.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 1\n1 2\n100",   'expected_output' => "0.7071 0.7071",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 0\n0 1\n50",    'expected_output' => "1.0000 0.0000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Rank-1 approximation ──────────────────────────────────────
        $seed(7, [
            ['input' => "2 1\n1 2",   'expected_output' => "1.5000 1.5000\n1.5000 1.5000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 0\n0 1",   'expected_output' => "3.0000 0.0000\n0.0000 0.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 0\n0 1",   'expected_output' => "1.0000 0.0000\n0.0000 0.0000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 2\n2 4",   'expected_output' => "3.2000 3.2000\n3.2000 3.2000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Collaborative filtering ───────────────────────────────────
        $seed(8, [
            ['input' => "3 3\n5 3 0\n4 0 4\n0 3 2\n0 2",   'expected_output' => '3.32',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n5 0\n0 4\n0 1",              'expected_output' => '4.00',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3\n5 3 4\n4 0 3\n1 2",          'expected_output' => '3.00',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 3\n5 0 0\n0 4 0\n0 0 3\n0 1",   'expected_output' => '4.00',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Pseudoinverse ─────────────────────────────────────────────
        $seed(9, [
            ['input' => "3\n1 1\n1 2\n1 3",         'expected_output' => "1.0000 0.3333 -0.3333\n-0.5000 0.0000 0.5000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1",              'expected_output' => "1.0000 0.0000\n0.0000 1.0000",                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2\n1 3\n1 4",         'expected_output' => "1.0000 0.3333 -0.3333\n-0.5000 0.0000 0.5000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 1\n1 3",              'expected_output' => "1.0000 0.0000\n-0.5000 0.5000",                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Rolling std ──────────────────────────────────────────────
        $seed(10, [
            ['input' => "5\n2024-01,10\n2024-02,20\n2024-03,30\n2024-04,40\n2024-05,50",  'expected_output' => "NA\nNA\n8.1650\n8.1650\n8.1650",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nd1,5\nd2,5\nd3,5",                                            'expected_output' => "NA\nNA\n0.0000",                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nd1,1\nd2,2\nd3,3\nd4,4",                                     'expected_output' => "NA\nNA\n0.8165\n0.8165",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\nd1,0\nd2,10\nd3,0\nd4,10\nd5,0",                             'expected_output' => "NA\nNA\n4.7140\n4.7140\n4.7140",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Salary percentile ────────────────────────────────────────
        $seed(11, [
            ['input' => "4\nAlice,Eng,90000\nBob,Eng,80000\nCarol,Eng,95000\nDave,HR,60000",  'expected_output' => "Alice: 33.33\nBob: 0.00\nCarol: 66.67\nDave: 0.00",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nX,A,100\nY,A,200",                                               'expected_output' => "X: 0.00\nY: 50.00",                                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,X,10\nB,X,20\nC,X,30",                                        'expected_output' => "A: 0.00\nB: 33.33\nC: 66.67",                          'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nZ,G,50",                                                          'expected_output' => "Z: 0.00",                                             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Session count ────────────────────────────────────────────
        $seed(12, [
            ['input' => "6\nu1,1,click\nu1,2,click\nu1,100,click\nu2,5,click\nu2,6,click\nu2,200,click\n50",  'expected_output' => "u1: 2\nu2: 2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nu1,1,a\nu1,2,b\nu1,3,c\n100",                                                     'expected_output' => "u1: 1",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nu1,1,a\nu1,1000,b\n5",                                                            'expected_output' => "u1: 2",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nu1,1,a\nu2,1,a\nu1,100,b\nu2,100,b\n50",                                         'expected_output' => "u1: 2\nu2: 2",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: ECDF ─────────────────────────────────────────────────────
        $seed(13, [
            ['input' => "5\na,1\nb,2\nc,2\nd,3\ne,4",   'expected_output' => "1: 0.2000\n2: 0.6000\n3: 0.8000\n4: 1.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\na,5\nb,5\nc,5",             'expected_output' => "5: 1.0000",                                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\na,1\nb,2\nc,3\nd,4",        'expected_output' => "1: 0.2500\n2: 0.5000\n3: 0.7500\n4: 1.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\na,10\nb,20",                'expected_output' => "10: 0.5000\n20: 1.0000",                        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Bootstrap CI ─────────────────────────────────────────────
        $seed(14, [
            ['input' => "5\n1\n2\n3\n4\n5\n1000\n0.95",    'expected_output' => '1.80 4.20',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10\n10\n10\n500\n0.95",         'expected_output' => '10.00 10.00','is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0\n0\n10\n10\n1000\n0.90",      'expected_output' => '0.00 10.00', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2\n3\n1000\n0.90",           'expected_output' => '1.00 3.00',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: ANOVA F-statistic ────────────────────────────────────────
        $seed(15, [
            ['input' => "3\n3\n2\n4\n6\n3\n1\n3\n5\n3\n8\n9\n10",    'expected_output' => '18.6667',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3\n1\n2\n3\n3\n1\n2\n3",                   'expected_output' => '0.0000',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n2\n0\n10\n2\n0\n10",                        'expected_output' => '0.0000',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2\n1\n2\n2\n3\n4\n2\n10\n11",              'expected_output' => '55.2857',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: KS statistic ─────────────────────────────────────────────
        $seed(16, [
            ['input' => "4\n1\n2\n3\n4\n4\n2\n3\n5\n6",    'expected_output' => '0.5000',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n3\n1\n2\n3",          'expected_output' => '0.0000',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n2\n2\n3\n4",                 'expected_output' => '0.5000',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2\n3\n3\n4\n5\n6",          'expected_output' => '1.0000',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: KDE ──────────────────────────────────────────────────────
        $seed(17, [
            ['input' => "3\n1\n2\n3\n1.0\n2.0",        'expected_output' => '0.397357',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0\n1.0\n0.0",              'expected_output' => '0.398942',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0\n10\n2.0\n5.0",          'expected_output' => '0.017528',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0\n0\n0\n0.5\n0.0",        'expected_output' => '0.797885',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Cohen's d ────────────────────────────────────────────────
        $seed(18, [
            ['input' => "3\n2\n4\n6\n3\n1\n3\n5",      'expected_output' => '0.5774',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n3\n1\n2\n3",      'expected_output' => '0.0000',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0\n10\n2\n0\n10",           'expected_output' => '0.0000',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10\n20\n2\n0\n10",          'expected_output' => '1.2247',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Target encoding ──────────────────────────────────────────
        $seed(19, [
            ['input' => "6\nA,1\nA,1\nB,0\nB,1\nC,0\nC,0\nA",     'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nX,0\nX,0\nY,1\nY,1\nX",               'expected_output' => '0.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,1\nB,0\nC,1\nD",                     'expected_output' => '0.6667',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nA,1\nB,0\nB",                          'expected_output' => '0.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Mutual information ───────────────────────────────────────
        $seed(20, [
            ['input' => "4\nA,0\nA,1\nB,0\nB,1",     'expected_output' => '0.0000',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nA,0\nA,0\nB,1\nB,1",     'expected_output' => '0.6931',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nA,1\nB,0",               'expected_output' => '0.6931',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nA,1\nA,0\nA,1\nB,0",     'expected_output' => '0.1132',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: WoE encoding ─────────────────────────────────────────────
        $seed(21, [
            ['input' => "6\nA,1\nA,1\nB,0\nB,1\nC,0\nC,0",     'expected_output' => "A: 0.6931\nB: 0.0000\nC: -0.6931",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nX,1\nX,1\nY,0\nY,0",               'expected_output' => "X: inf\nY: -inf",                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nA,1\nA,0\nB,1\nB,0",               'expected_output' => "A: 0.0000\nB: 0.0000",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\nA,1\nA,1\nA,1\nB,0\nB,0\nB,0",     'expected_output' => "A: inf\nB: -inf",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: LASSO coordinate descent ────────────────────────────────
        $seed(22, [
            ['input' => "3\n1 2\n2 4\n3 6\n0.5\n100",       'expected_output' => '1.8333',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1\n2 2\n3 3\n0.0\n100",       'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2\n2 4\n3 6\n10.0\n100",      'expected_output' => '0.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 5\n2 5\n0.5\n100",            'expected_output' => '3.1667',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Information gain ─────────────────────────────────────────
        $seed(23, [
            ['input' => "6\n1,0\n2,0\n3,0\n7,1\n8,1\n9,1\n5",    'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1,0\n2,1\n3,0\n4,1\n2.5",            'expected_output' => '0.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1,0\n2,0\n3,0\n4,1\n5,1\n6,1\n3.5",  'expected_output' => '1.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1,0\n2,0\n3,1\n4,1\n1.5",            'expected_output' => '0.3113',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Logistic regression ──────────────────────────────────────
        $seed(24, [
            ['input' => "4\n1 0\n2 0\n5 1\n6 1\n0.1\n1000",     'expected_output' => "w: 1.1420\nb: -4.2820",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 0\n2 0\n3 1\n4 1\n0.1\n1000",     'expected_output' => "w: 2.5540\nb: -6.3850",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 0\n1 1\n0.1\n1000",                'expected_output' => "w: 2.3264\nb: -1.1632",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0 0\n0 0\n10 1\n10 1\n0.01\n500",   'expected_output' => "w: 0.9271\nb: -4.6357",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Decision tree stump ──────────────────────────────────────
        $seed(25, [
            ['input' => "6\n1 0\n2 0\n3 0\n7 1\n8 1\n9 1",    'expected_output' => "threshold: 5.0\ngini_before: 0.5000\ngini_after: 0.0000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 0\n2 0\n3 1\n4 1",              'expected_output' => "threshold: 2.5\ngini_before: 0.5000\ngini_after: 0.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 0\n2 1\n3 0\n4 1",              'expected_output' => "threshold: 1.5\ngini_before: 0.5000\ngini_after: 0.5000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n5 0\n6 1",                         'expected_output' => "threshold: 5.5\ngini_before: 0.5000\ngini_after: 0.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: K-fold cross-validation ──────────────────────────────────
        $seed(26, [
            ['input' => "10\n1 A\n2 A\n3 A\n4 A\n5 A\n6 B\n7 B\n8 B\n9 B\n10 B",  'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n1 A\n2 B\n3 A\n4 B\n5 A\n6 B\n7 A\n8 B\n9 A\n10 B",  'expected_output' => '0.6000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 A\n2 A\n3 A\n4 A\n5 A",                              'expected_output' => '1.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n1 X\n2 X\n3 X\n4 X\n5 X\n6 Y\n7 Y\n8 Y\n9 Y\n10 Y",  'expected_output' => '1.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Naive Bayes ──────────────────────────────────────────────
        $seed(27, [
            ['input' => "6\n1 A\n2 A\n3 A\n7 B\n8 B\n9 B\n5",     'expected_output' => 'A',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 A\n2 A\n8 B\n9 B\n8",               'expected_output' => 'B',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 A\n10 B\n5",                         'expected_output' => 'A',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n0 X\n1 X\n2 X\n8 Y\n9 Y\n10 Y\n7",    'expected_output' => 'Y',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: AdaBoost ─────────────────────────────────────────────────
        $seed(28, [
            ['input' => "6\n1 -1\n2 -1\n3 -1\n7 1\n8 1\n9 1\n5",   'expected_output' => "-1\n-1\n-1\n1\n1\n1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 -1\n2 -1\n8 1\n9 1\n3",              'expected_output' => "-1\n-1\n1\n1",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 -1\n10 1\n3",                         'expected_output' => "-1\n1",                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1 1\n2 1\n3 1\n7 -1\n8 -1\n9 -1\n5",   'expected_output' => "1\n1\n1\n-1\n-1\n-1",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: ROC AUC ──────────────────────────────────────────────────
        $seed(29, [
            ['input' => "4\n1 0.9\n1 0.8\n0 0.3\n0 0.1",    'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 0.1\n1 0.2\n0 0.8\n0 0.9",    'expected_output' => '0.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 0.9\n0 0.8\n1 0.3\n0 0.1",    'expected_output' => '0.7500',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 0.5\n0 0.5",                   'expected_output' => '0.5000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Ridge regression ─────────────────────────────────────────
        $seed(30, [
            ['input' => "3\n1 2\n2 4\n3 6\n0",       'expected_output' => "b: 0.0000\nw: 2.0000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1\n2 2\n3 3\n0",       'expected_output' => "b: 0.0000\nw: 1.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2\n2 4\n3 6\n10",      'expected_output' => "b: 2.6087\nw: 0.5217",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 1\n2 2\n0",            'expected_output' => "b: 0.0000\nw: 1.0000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: DBSCAN ───────────────────────────────────────────────────
        $seed(31, [
            ['input' => "7\n1\n2\n3\n10\n11\n12\n100\n2\n2",    'expected_output' => "0\n0\n0\n1\n1\n1\n-1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n1.5\n2",                'expected_output' => "0\n0\n0\n0",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n2\n10\n11\n50\n1.5\n2",          'expected_output' => "0\n0\n1\n1\n-1",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n10\n20\n0.5\n2",                  'expected_output' => "-1\n-1\n-1",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Hierarchical clustering ──────────────────────────────────
        $seed(32, [
            ['input' => "6\n1\n2\n3\n7\n8\n9\n2",     'expected_output' => "0\n0\n0\n1\n1\n1",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n8\n9\n2",           'expected_output' => "0\n0\n1\n1",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n5\n9\n3",              'expected_output' => "0\n1\n2",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4\n1",           'expected_output' => "0\n0\n0\n0",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: GMM EM ───────────────────────────────────────────────────
        $seed(33, [
            ['input' => "6\n1\n2\n3\n7\n8\n9\n20",    'expected_output' => "mu1: 2.0000\nmu2: 8.0000\npi1: 0.5000\npi2: 0.5000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0\n1\n9\n10\n20",         'expected_output' => "mu1: 0.5000\nmu2: 9.5000\npi1: 0.5000\npi2: 0.5000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1\n1\n1\n9\n9\n9\n30",    'expected_output' => "mu1: 1.0000\nmu2: 9.0000\npi1: 0.5000\npi2: 0.5000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0\n10\n20",               'expected_output' => "mu1: 0.0000\nmu2: 10.0000\npi1: 0.5000\npi2: 0.5000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Davies-Bouldin Index ─────────────────────────────────────
        $seed(34, [
            ['input' => "6\n1 0\n2 0\n3 0\n7 1\n8 1\n9 1",   'expected_output' => '0.2500',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0 0\n1 0\n9 1\n10 1",            'expected_output' => '0.1111',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 0\n2 0\n4 1\n5 1",            'expected_output' => '0.6667',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0 0\n10 1",                      'expected_output' => '0.0000',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: ARIMA(1,1,0) ─────────────────────────────────────────────
        $seed(35, [
            ['input' => "5\n1\n2\n4\n7\n11",      'expected_output' => '1.0000',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0\n2\n4\n6",          'expected_output' => '1.0000',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0\n1\n3\n6\n10",      'expected_output' => '1.0000',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n10\n10\n10",       'expected_output' => '0.0000',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Holt-Winters ─────────────────────────────────────────────
        $seed(36, [
            ['input' => "5\n2\n4\n6\n8\n10\n0.8\n0.8",   'expected_output' => "2.00\n4.00\n6.00\n8.00",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n1.0\n1.0",       'expected_output' => "1.00\n2.00\n3.00",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n20\n30\n40\n0.5\n0.5",   'expected_output' => "10.00\n20.00\n30.00",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n5\n5\n5\n0.5\n0.5",          'expected_output' => "5.00\n5.00",                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: STL detrend ──────────────────────────────────────────────
        $seed(37, [
            ['input' => "6\n1\n3\n2\n4\n3\n5\n3",    'expected_output' => "-0.50\n0.50\n-0.50",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n2\n2\n2\n2\n2\n2\n3",    'expected_output' => "0.00\n0.00\n0.00",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n2\n3\n4\n5\n3",       'expected_output' => "-1.00\n0.00\n1.00",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "7\n1\n3\n2\n4\n3\n5\n4\n3", 'expected_output' => "-1.00\n0.00\n-1.00\n0.00\n1.00", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q38: PACF ────────────────────────────────────────────────────
        $seed(38, [
            ['input' => "6\n1\n2\n3\n4\n5\n6",      'expected_output' => "PACF(1): 0.4000\nPACF(2): -0.1429",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1\n1\n1\n1\n1",         'expected_output' => "PACF(1): 0.0000\nPACF(2): 0.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1\n2\n1\n2\n1\n2",      'expected_output' => "PACF(1): -1.0000\nPACF(2): nan",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n2\n4\n6\n8\n10",        'expected_output' => "PACF(1): 0.4000\nPACF(2): -0.1429",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: BM25 ────────────────────────────────────────────────────
        $seed(39, [
            ['input' => "3\ndata science is fun\nmachine learning is great\npython data analysis\ndata science",  'expected_output' => "1.3459\n0.0000\n0.4672",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nhello world\nhello python\nhello",                                                    'expected_output' => "0.5754\n0.5754",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\na b c\nd e f\na d g\na",                                                             'expected_output' => "0.7362\n0.0000\n0.5404",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\npython is great\njava is great\npython",                                              'expected_output' => "0.5754\n0.0000",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: LDA (format check only) ─────────────────────────────────
        $seed(40, [
            ['input' => "4\ndata science analysis\nmachine learning algorithm\ndata analysis statistics\nmachine learning data\n20",  'expected_output' => "Topic 0: data analysis science\nTopic 1: machine learning data",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\naaa bbb ccc\nddd eee fff\n10",  'expected_output' => "Topic 0: aaa bbb ccc\nTopic 1: ddd eee fff",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nalpha beta gamma\nalpha beta delta\ngamma delta epsilon\nbeta gamma epsilon\n30",  'expected_output' => "Topic 0: beta gamma alpha\nTopic 1: delta epsilon gamma",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\na b\nc d\n5",  'expected_output' => "Topic 0: a b\nTopic 1: c d",  'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q41: Co-occurrence matrix ─────────────────────────────────────
        $seed(41, [
            ['input' => "2\nthe cat sat\nthe dog sat\n1",   'expected_output' => "0 1 0 1\n1 0 1 1\n0 1 0 1\n1 1 1 0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\na b c\n1",                      'expected_output' => "0 1 0\n1 0 1\n0 1 0",                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\na b c\n2",                      'expected_output' => "0 1 1\n1 0 1\n1 1 0",                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\na b\nb c\n1",                   'expected_output' => "0 1 0\n1 0 1\n0 1 0",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: PMI ─────────────────────────────────────────────────────
        $seed(42, [
            ['input' => "4\nthe cat sat\nthe dog sat\ncat sat here\ndog ran away\ncat\nsat",   'expected_output' => '0.4150',     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\na b\nb c\na c\na\nb",                                              'expected_output' => '0.4055',     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nhello world\nhello python\nhello\njava",                           'expected_output' => 'undefined',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\na b c\na b d\na c e\nb c d\na\nb",                                'expected_output' => '0.2877',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Extractive summarization ────────────────────────────────
        $seed(43, [
            ['input' => "4\ndata science is the future\nmachine learning drives data science\npython is great for data analysis\nstatistics is fundamental to data science\n2",  'expected_output' => "data science is the future\nmachine learning drives data science",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nhello world\nhello python\nhello data\n1",                                                                                                           'expected_output' => "hello world",                                                       'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\na b c d\na b\nc d e f g\n2",                                                                                                                        'expected_output' => "c d e f g\na b c d",                                               'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\ndata science\nmachine learning\n1",                                                                                                                  'expected_output' => "data science",                                                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Full preprocessing pipeline ─────────────────────────────
        $seed(44, [
            ['input' => "4\nAlice,A,10\nBob,B,NA\nCarol,A,30\nDave,B,20",   'expected_output' => "Alice,0,-1.1547\nBob,1,0.0000\nCarol,0,1.1547\nDave,1,0.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nX,P,5\nY,P,15",                                 'expected_output' => "X,0,-1.0000\nY,0,1.0000",                                        'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nA,X,NA\nB,Y,20\nC,X,NA",                        'expected_output' => "A,1,0.0000\nB,0,0.0000\nC,1,0.0000",                             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nA,X,10\nB,Y,20\nC,Z,30",                        'expected_output' => "A,0,-1.2247\nB,2,0.0000\nC,1,1.2247",                            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Stratified split ─────────────────────────────────────────
        $seed(45, [
            ['input' => "10\n1 A\n2 A\n3 A\n4 A\n5 B\n6 B\n7 B\n8 B\n9 A\n10 B",  'expected_output' => "train:\nA: 4\nB: 4\ntest:\nA: 1\nB: 1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1 A\n2 A\n3 A\n4 A\n5 A",                              'expected_output' => "train:\nA: 4\ntest:\nA: 1",                'is_hidden' => false, 'order_index' => 2],
            ['input' => "10\n1 X\n2 X\n3 X\n4 X\n5 X\n6 Y\n7 Y\n8 Y\n9 Y\n10 Y",  'expected_output' => "train:\nX: 4\nY: 4\ntest:\nX: 1\nY: 1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n1 A\n2 B\n3 C\n4 A\n5 B\n6 C\n7 A\n8 B\n9 C\n10 A",  'expected_output' => "train:\nA: 3\nB: 2\nC: 2\ntest:\nA: 1\nB: 1\nC: 1", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q46: Stacking ensemble ────────────────────────────────────────
        $seed(46, [
            ['input' => "6\n1 0\n2 0\n3 0\n7 1\n8 1\n9 1\n3\n5\n3\n8",     'expected_output' => "0\n0\n1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 0\n2 0\n8 1\n9 1\n2\n1\n9",                   'expected_output' => "0\n1",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 0\n10 1\n2\n1\n10",                            'expected_output' => "0\n1",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0 0\n1 0\n9 1\n10 1\n2\n5\n5",                  'expected_output' => "0\n1",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Learning curves ──────────────────────────────────────────
        $seed(47, [
            ['input' => "10\n1 1\n2 2\n3 3\n4 4\n5 5\n6 6\n7 7\n8 8\n9 9\n10 10",  'expected_output' => "2: train_mae=0.0000 val_mae=0.0000\n4: train_mae=0.0000 val_mae=0.0000\n6: train_mae=0.0000 val_mae=0.0000\n8: train_mae=0.0000 val_mae=0.0000\n10: train_mae=0.0000 val_mae=0.0000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1 2\n2 4\n3 6\n4 8\n5 10",                              'expected_output' => "1: train_mae=0.0000 val_mae=0.0000\n2: train_mae=0.0000 val_mae=0.0000\n3: train_mae=0.0000 val_mae=0.0000\n4: train_mae=0.0000 val_mae=0.0000\n5: train_mae=0.0000 val_mae=0.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 1\n2 2\n3 3\n4 4\n5 5",                               'expected_output' => "1: train_mae=0.0000 val_mae=0.0000\n2: train_mae=0.0000 val_mae=0.0000\n3: train_mae=0.0000 val_mae=0.0000\n4: train_mae=0.0000 val_mae=0.0000\n5: train_mae=0.0000 val_mae=0.0000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n1 10\n2 9\n3 8\n4 7\n5 6\n6 5\n7 4\n8 3\n9 2\n10 1",  'expected_output' => "2: train_mae=0.0000 val_mae=0.0000\n4: train_mae=0.0000 val_mae=0.0000\n6: train_mae=0.0000 val_mae=0.0000\n8: train_mae=0.0000 val_mae=0.0000\n10: train_mae=0.0000 val_mae=0.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Grid search KNN ──────────────────────────────────────────
        $seed(48, [
            ['input' => "10\n1 A\n2 A\n3 A\n4 A\n5 A\n6 B\n7 B\n8 B\n9 B\n10 B\n4\n3 A\n7 B\n4 A\n6 B",  'expected_output' => "best_k: 1 accuracy: 1.0000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n1 A\n2 A\n3 A\n7 B\n8 B\n9 B\n2\n2 A\n8 B",                                    'expected_output' => "best_k: 1 accuracy: 1.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1 A\n2 B\n3 A\n4 B\n5 A\n6 B\n2\n2 B\n5 A",                                    'expected_output' => "best_k: 1 accuracy: 1.0000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 A\n3 A\n7 B\n9 B\n2\n2 A\n8 B",                                              'expected_output' => "best_k: 1 accuracy: 1.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: User-based CF ────────────────────────────────────────────
        $seed(49, [
            ['input' => "3 3\n5 3 0\n4 0 4\n0 3 2\n0 2 2",     'expected_output' => '3.50',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n5 0\n0 4\n0 1 1",                 'expected_output' => '4.00',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 3\n5 4 0\n0 4 3\n5 0 3\n0 1 1",     'expected_output' => '3.00',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 3\n5 3 0\n4 0 4\n0 2 1",            'expected_output' => '3.00',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: NLP pipeline classification ─────────────────────────────
        $seed(50, [
            ['input' => "4\nsports|football game score win\nsports|basketball team score points\ntech|python code program data\ntech|algorithm data structure code\nfootball game score",  'expected_output' => 'sports',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nsports|football game score win\nsports|basketball team score points\ntech|python code program data\ntech|algorithm data structure code\npython code data",      'expected_output' => 'tech',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nfood|pizza pasta bread\nfood|sushi rice noodle\ntech|code program data\ntech|algorithm python code\npizza pasta rice",                                          'expected_output' => 'food',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\na|cat dog\nb|fish bird\ncat dog fish",                                                                                                                          'expected_output' => 'a',       'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 3 Coding (Advanced) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}