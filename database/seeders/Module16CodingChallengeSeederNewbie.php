<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 16 — Multivariate Analysis (Newbie / Level 1) — CODING variant
 *
 * Seeds in one pass:
 * 1. challenges          — one coding challenge for the Newbie tier
 * 2. coding_questions    — 50 questions covering beginner Multivariate concepts
 * 3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mapped to lessons 437–446):
 * L16.1  Introduction to Multivariate Data & the Multivariate Normal
 * L16.2  The Covariance Matrix & Multivariate Descriptive Statistics
 * L16.3  Principal Component Analysis (PCA)
 * L16.4  Factor Analysis
 * L16.5  Multivariate Analysis of Variance (MANOVA)
 * L16.6  Discriminant Analysis (LDA & QDA)
 * L16.7  Cluster Analysis: Hierarchical & k-Means
 * L16.8  Canonical Correlation Analysis (CCA)
 * L16.9  Multidimensional Scaling (MDS) & t-SNE
 * L16.10 Multivariate Regression & Path Analysis
 *
 * Difficulty: Newbie — all problems solvable with pure Python, no third-party
 * libraries required. Learners build intuition for vector/matrix operations
 * and fundamental multivariate metrics from scratch.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module16CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 16 — Multivariate Analysis (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Multivariate Analysis Basics',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Explore the foundations of Multivariate Analysis using pure Python. Compute mean vectors, covariance matrices, distances, principal component projections, clustering fundamentals, and path model effects without relying on external mathematical libraries.',
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
            // TOPIC 1: Intro to Multivariate Data & Normal (Q1–Q5) → L437
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
**Data Matrix Dimensions:** A dataset can be represented as an `n × p` matrix, where `n` is the number of observations (rows) and `p` is the number of variables (columns). Read `n` and `p`. Then read `n` lines, each containing `p` values. Print the dimensions.

Example:
Input:
2 3
1.0 2.0 3.0
4.0 5.0 6.0
Output:
rows: 2
cols: 3

MD,
                'starter_code'        => "n, p = map(int, input().split())\ndata = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
**Mean Vector:** The mean vector represents the center of multivariate data. Read `n` (observations) and `p` (variables), then `n` rows of data. Calculate the mean for each of the `p` variables and print them space-separated, rounded to 4 decimal places.

Example:
Input:
3 2
1.0 2.0
3.0 4.0
5.0 0.0
Output:
3.0000 2.0000

MD,
                'starter_code'        => "n, p = map(int, input().split())\ndata = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
**Vector Addition:** In multivariate statistics, we often add or shift vectors. Read length `p`, then read two vectors (each on a new line). Print the resulting vector, space-separated, rounded to 2 decimal places.

Example:
Input:
3
1 2 3
4 5 6
Output:
5.00 7.00 9.00

MD,
                'starter_code'        => "p = int(input())\nv1 = list(map(float, input().split()))\nv2 = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
**Dot Product:** The inner (dot) product is fundamental to projections and covariance. Read length `p`, then two vectors. Compute and print their dot product rounded to 4 decimal places.

Example:
Input:
2
1 3
2 4
Output:
14.0000

MD,
                'starter_code'        => "p = int(input())\nv1 = list(map(float, input().split()))\nv2 = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
**Standardized Distance (Simplified Mahalanobis):** If variables are independent, standardized distance from the mean is squared Euclidean distance using standardized coordinates. Read `p`, vector `x`, mean vector `m`, and variance vector `v`.
Distance = Sum( (x_i - m_i)² / v_i ). Print rounded to 4 decimal places.

Example:
Input:
2
5.0 5.0
2.0 2.0
1.0 9.0
Output:
10.0000

MD,
                'starter_code'        => "p = int(input())\nx = list(map(float, input().split()))\nm = list(map(float, input().split()))\nv = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Covariance & Descriptive Stats (Q6–Q10) → L438
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
**Centering a Data Matrix:** Subtract the variable means from each observation so the new mean vector is zero. Read `n` and `p`, then the data. Print the centered matrix, row by row, space-separated, rounded to 2 decimal places.

Example:
Input:
2 2
10 5
20 15
Output:
-5.00 -5.00
5.00 5.00

MD,
                'starter_code'        => "n, p = map(int, input().split())\ndata = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
**Sample Variance:** Compute sample variance (ddof=1) for a single variable. Read `n` and `n` values. Print the variance rounded to 4 decimal places.

Example:
Input:
3
1
3
5
Output:
4.0000

MD,
                'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
**Sample Covariance:** Compute sample covariance (ddof=1) between two variables X and Y. Read `n`, then `n` values for X, then `n` values for Y. Print rounded to 4 decimal places.
Cov(X,Y) = Sum( (x_i - mean_x) * (y_i - mean_y) ) / (n - 1)

Example:
Input:
3
1 2 3
2 4 6
Output:
2.0000

MD,
                'starter_code'        => "n = int(input())\nx = list(map(float, input().split()))\ny = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
**Total Variation:** The total variation (trace of the covariance matrix) is the sum of the variances of all variables. Read `n` and `p`, then `n` rows of data. Calculate the sample variance for each column and sum them. Print rounded to 4 decimal places.

Example:
Input:
3 2
1 5
3 5
5 5
Output:
4.0000

MD,
                'starter_code'        => "n, p = map(int, input().split())\ndata = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
**Pearson Correlation:** Compute the correlation coefficient between X and Y: r = Cov(X,Y) / (Std(X) * Std(Y)). Read `n`, then vector X, then vector Y. Print `r` rounded to 4 decimal places.

Example:
Input:
3
1 2 3
2 4 6
Output:
1.0000

MD,
                'starter_code'        => "import math\nn = int(input())\nx = list(map(float, input().split()))\ny = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Principal Component Analysis (PCA) (Q11–Q15) → L439
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
**Vector Norm:** The length (Euclidean norm) of a vector is the square root of the sum of its squared elements. Read `p` and a vector. Print its norm rounded to 4 decimal places.

Example:
Input:
3
3 0 4
Output:
5.0000

MD,
                'starter_code'        => "import math\np = int(input())\nv = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
**Unit Vector Normalization:** To define a PCA direction, we need unit vectors. Read `p` and a vector. Divide each element by the vector's norm. Print the unit vector space-separated, rounded to 4 decimal places.

Example:
Input:
2
3 4
Output:
0.6000 0.8000

MD,
                'starter_code'        => "import math\np = int(input())\nv = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
**1D Projection:** Project a data vector `x` onto a unit vector `u`. The projected scalar value (score) is the dot product of `x` and `u`. Read `p`, vector `x`, and unit vector `u`. Print the projection score rounded to 4 decimal places.

Example:
Input:
2
2 4
0.6 0.8
Output:
4.4000

MD,
                'starter_code'        => "p = int(input())\nx = list(map(float, input().split()))\nu = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
**Projected Variance:** Project an entire dataset onto a unit vector `u` and compute the sample variance (ddof=1) of the resulting scores. Read `n`, `p`, the unit vector `u`, then `n` rows of data. Print the variance rounded to 4 decimal places.

Example:
Input:
3 2
1.0 0.0
1 1
2 2
3 3
Output:
1.0000

MD,
                'starter_code'        => "n, p = map(int, input().split())\nu = list(map(float, input().split()))\ndata = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
**Orthogonal Residual:** After projecting `x` onto a principal component `u`, the residual vector is `r = x - (x · u)u`. Read `p`, vector `x`, and unit vector `u`. Print the residual vector `r` space-separated, rounded to 4 decimal places.

Example:
Input:
2
2 2
1.0 0.0
Output:
0.0000 2.0000

MD,
                'starter_code'        => "p = int(input())\nx = list(map(float, input().split()))\nu = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Factor Analysis (Q16–Q20) → L440
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
**Communality:** In Factor Analysis, a variable's communality is the sum of its squared factor loadings. Read `k` (number of factors) and the loading vector for a single variable. Print the communality rounded to 4 decimal places.

Example:
Input:
2
0.6 0.8
Output:
1.0000

MD,
                'starter_code'        => "k = int(input())\nloadings = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
**Specific Variance:** Specific variance (or uniqueness) for a standardized variable is `1 - communality`. Read `k` factors and the variable's loadings. Print the specific variance rounded to 4 decimal places.

Example:
Input:
2
0.5 0.5
Output:
0.5000

MD,
                'starter_code'        => "k = int(input())\nloadings = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
**Reproduced Covariance:** The model-implied covariance between variables `i` and `j` is the dot product of their loading vectors. Read `k`, then loading vectors `L_i` and `L_j`. Print the reproduced covariance rounded to 4 decimal places.

Example:
Input:
2
0.8 0.2
0.6 0.4
Output:
0.5600

MD,
                'starter_code'        => "k = int(input())\nLi = list(map(float, input().split()))\nLj = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
**Residual Covariance:** The residual is the Actual Covariance minus the Reproduced Covariance. Read the actual covariance value `cov_ij`, the number of factors `k`, and loading vectors `L_i` and `L_j`. Print the residual rounded to 4 decimal places.

Example:
Input:
0.60
2
0.8 0.2
0.6 0.4
Output:
0.0400

MD,
                'starter_code'        => "cov_ij = float(input())\nk = int(input())\nLi = list(map(float, input().split()))\nLj = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
**Variance Explained by Factor:** The total variance explained by a specific factor is the sum of squared loadings for that factor across all `p` variables. Read `p` (variables), then `p` lines of loadings (where the first element is Factor 1, second is Factor 2, etc.). Read the target factor index `idx` (0-indexed). Print the variance explained rounded to 4 decimal places.

Example:
Input:
3
0.8 0.1
0.7 0.2
0.6 0.3
0
Output:
1.4900

MD,
                'starter_code'        => "p = int(input())\nloadings = [list(map(float, input().split())) for _ in range(p)]\nidx = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: MANOVA (Q21–Q25) → L441
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
**Grand Mean:** In MANOVA, the grand mean is the mean vector of all observations across all groups. Read `n` total observations and `p` variables, then `n` rows of data. Print the grand mean vector, space-separated, rounded to 4 decimal places.

Example:
Input:
4 2
2 4
4 6
8 10
10 12
Output:
6.0000 8.0000

MD,
                'starter_code'        => "n, p = map(int, input().split())\ndata = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
**Group Means:** Read `n` observations. Each line contains a group ID (integer) followed by `p=2` variable values. Calculate and print the mean vector for each group ID, sorted by group ID ascending. Format: `Group X: m1 m2` (rounded to 4 decimals).

Example:
Input:
4
1 2.0 4.0
2 8.0 10.0
1 4.0 6.0
2 10.0 12.0
Output:
Group 1: 3.0000 5.0000
Group 2: 9.0000 11.0000

MD,
                'starter_code'        => "n = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    data.append((int(parts[0]), float(parts[1]), float(parts[2])))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
**Within-Group Sum of Squares (SSW):** For a single variable, SSW is the sum of squared deviations of observations from their respective group means. Read `n` total lines: `group_id value`. Print SSW rounded to 4 decimal places.

Example:
Input:
4
1 2.0
1 4.0
2 8.0
2 10.0
Output:
4.0000

MD,
                'starter_code'        => "n = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    data.append((int(parts[0]), float(parts[1])))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
**Between-Group Sum of Squares (SSB):** For a single variable, SSB = Sum( n_k * (mean_k - grand_mean)² ) over all groups `k`. Read `n` lines: `group_id value`. Print SSB rounded to 4 decimal places.

Example:
Input:
4
1 2.0
1 4.0
2 8.0
2 10.0
Output:
36.0000

MD,
                'starter_code'        => "n = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    data.append((int(parts[0]), float(parts[1])))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
**Total Sum of Squares (SST):** For a single variable, SST is the sum of squared deviations of all observations from the grand mean. Read `n` lines: `group_id value` (group_id is ignored for SST). Print SST rounded to 4 decimal places.

Example:
Input:
4
1 2.0
1 4.0
2 8.0
2 10.0
Output:
40.0000

MD,
                'starter_code'        => "n = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    data.append((int(parts[0]), float(parts[1])))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Discriminant Analysis (Q26–Q30) → L442
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
**Decision Boundary Midpoint:** In 1D, the simplest boundary between two class means `m1` and `m2` is their midpoint. Read `m1` and `m2`. Print the midpoint rounded to 4 decimal places.

Example:
Input:
2.0 8.0
Output:
5.0000

MD,
                'starter_code'        => "m1, m2 = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
**1D Nearest Centroid Classifier:** Classify `x` into class 1 (mean `m1`) or class 2 (mean `m2`) based on which is closer. If equidistant, assign to class 1. Read `m1`, `m2`, and `x`. Print `1` or `2`.

Example:
Input:
2.0 8.0
6.0
Output:
2

MD,
                'starter_code'        => "m1, m2 = map(float, input().split())\nx = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
**2D Nearest Centroid:** Read two 2D class centroids `c1` and `c2` (space-separated on separate lines), then a point `p`. Classify `p` into class `1` or `2` based on Euclidean distance. If tied, print `1`.

Example:
Input:
0.0 0.0
10.0 10.0
2.0 2.0
Output:
1

MD,
                'starter_code'        => "c1 = list(map(float, input().split()))\nc2 = list(map(float, input().split()))\np = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
**Pooled Within-Class Variance:** For 1D data with 2 classes, pooled variance is `((n1-1)*s1² + (n2-1)*s2²) / (n1+n2-2)`. Read `n1`, `s1_sq` (variance of class 1), `n2`, and `s2_sq`. Print the pooled variance rounded to 4 decimal places.

Example:
Input:
10 4.0
15 6.0
Output:
5.2174

MD,
                'starter_code'        => "n1, s1_sq = input().split()\nn2, s2_sq = input().split()\nn1, n2 = int(n1), int(n2)\ns1_sq, s2_sq = float(s1_sq), float(s2_sq)\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
**Fisher's Objective (1D):** Fisher's LDA maximizes between-class variance relative to within-class variance. Compute `J = (m1 - m2)² / (s1² + s2²)`. Read `m1`, `m2`, `s1_sq`, `s2_sq`. Print `J` rounded to 4 decimal places.

Example:
Input:
8.0 2.0
2.0 2.0
Output:
9.0000

MD,
                'starter_code'        => "m1, m2 = map(float, input().split())\ns1_sq, s2_sq = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Cluster Analysis (Q31–Q35) → L443
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
**Euclidean Distance:** The basis of k-means clustering. Read length `p` and two vectors `x` and `y`. Print the Euclidean distance between them rounded to 4 decimal places.

Example:
Input:
2
0 0
3 4
Output:
5.0000

MD,
                'starter_code'        => "import math\np = int(input())\nx = list(map(float, input().split()))\ny = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
**K-Means Assignment:** Read `k` (number of centroids) and `p` (dimensions). Read `k` centroids. Read `n` points. For each point, print the 0-based index of the nearest centroid. Ties go to the smaller index.

Example:
Input:
2 2
0 0
10 10
2
1 1
9 9
Output:
0
1

MD,
                'starter_code'        => "import math\nk, p = map(int, input().split())\ncentroids = [list(map(float, input().split())) for _ in range(k)]\nn = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
**Update Centroid:** Read `p` (dimensions) and `n` points belonging to a single cluster. Calculate the new centroid (the mean vector of these points). Print the components space-separated, rounded to 4 decimals.

Example:
Input:
2
3
0 0
0 6
6 0
Output:
2.0000 2.0000

MD,
                'starter_code'        => "p = int(input())\nn = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
**Within-Cluster Sum of Squares (WCSS):** Used to evaluate k-means. Read `n` points. The first line is the centroid. The following `n` lines are the points. Compute the sum of squared Euclidean distances from each point to the centroid. Print rounded to 4 decimals.

Example:
Input:
2
2 2
0 0
4 4
Output:
16.0000

MD,
                'starter_code'        => "n = int(input())\ncentroid = list(map(float, input().split()))\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
**Single-Linkage Distance:** In hierarchical clustering, single-linkage distance between two clusters is the minimum Euclidean distance between any point in cluster A and any point in cluster B. Read `nA` points for A, then `nB` points for B (all 2D). Print the minimum distance rounded to 4 decimals.

Example:
Input:
2
0 0
0 1
2
10 10
0 3
Output:
2.0000

MD,
                'starter_code'        => "import math\nnA = int(input())\nA = [list(map(float, input().split())) for _ in range(nA)]\nnB = int(input())\nB = [list(map(float, input().split())) for _ in range(nB)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Canonical Correlation Analysis (CCA) (Q36–Q40) → L444
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
**Linear Combination (Set 1):** In CCA, we form canonical variates `U = a1*X1 + a2*X2...`. Read length `p`, weights `a`, and variable values `X`. Compute and print `U` rounded to 4 decimal places.

Example:
Input:
2
0.5 0.5
10.0 20.0
Output:
15.0000

MD,
                'starter_code'        => "p = int(input())\na = list(map(float, input().split()))\nX = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
**Batch Linear Combinations:** Read weights `b` of length `q`. Read `n` rows of data vectors `Y` (each length `q`). For each row, compute `V = b · Y`. Print the `n` values of `V` on separate lines, rounded to 4 decimals.

Example:
Input:
2
1.0 -1.0
2
5.0 2.0
10.0 10.0
Output:
3.0000
0.0000

MD,
                'starter_code'        => "q = int(input())\nb = list(map(float, input().split()))\nn = int(input())\nY = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
**Canonical Covariance:** Read `n` values for canonical variate `U` and `n` values for canonical variate `V`. Compute their sample covariance (ddof=1) and print rounded to 4 decimals.

Example:
Input:
3
1 2 3
4 5 6
Output:
1.0000

MD,
                'starter_code'        => "n = int(input())\nU = list(map(float, input().split()))\nV = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
**Scaling to Unit Variance:** CCA requires variates to have a variance of 1. Read `n` values for variate `U`. Calculate its standard deviation `s` (ddof=1). Divide all values by `s` to scale them. Print the scaled values space-separated, rounded to 4 decimals. (Assume s > 0).

Example:
Input:
3
-1.0 0.0 1.0
Output:
-1.0000 0.0000 1.0000

MD,
                'starter_code'        => "import math\nn = int(input())\nU = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
**Cross-Covariance Entry:** In CCA we use the cross-covariance matrix S_xy. Read `n` pairs of (X_i, Y_j) values. Compute their sample covariance (ddof=1) and print rounded to 4 decimal places.

Example:
Input:
4
0 0
1 1
2 2
3 3
Output:
1.6667

MD,
                'starter_code'        => "n = int(input())\npairs = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: MDS & t-SNE (Q41–Q45) → L445
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
**Pairwise Distance Matrix:** Read `n` points (1D values). Construct an `n × n` matrix where entry (i,j) is the absolute difference |p_i - p_j|. Print the matrix row by row, space-separated, rounded to 2 decimals.

Example:
Input:
3
1 3 6
Output:
0.00 2.00 5.00
2.00 0.00 3.00
5.00 3.00 0.00

MD,
                'starter_code'        => "n = int(input())\npoints = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
**Matrix Row Means:** A step in MDS double-centering. Read an `n × n` matrix. Compute the mean of each row and print them space-separated, rounded to 4 decimals.

Example:
Input:
2
1 3
5 7
Output:
2.0000 6.0000

MD,
                'starter_code'        => "n = int(input())\nmat = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
**Matrix Column Means:** Another step in double-centering. Read an `n × n` matrix. Compute the mean of each column and print them space-separated, rounded to 4 decimals.

Example:
Input:
2
1 3
5 7
Output:
3.0000 5.0000

MD,
                'starter_code'        => "n = int(input())\nmat = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
**Matrix Grand Mean:** The final mean for double centering. Read an `n × n` matrix. Compute the average of all `n²` elements. Print rounded to 4 decimals.

Example:
Input:
2
1 3
5 7
Output:
4.0000

MD,
                'starter_code'        => "n = int(input())\nmat = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
**Gaussian Affinity (t-SNE simplified):** t-SNE converts distances to probabilities. Compute affinity `A = exp(- D² / (2 * sigma²))`. Read distance `D` and `sigma`. Print `A` rounded to 4 decimals.

Example:
Input:
2.0
1.0
Output:
0.1353

MD,
                'starter_code'        => "import math\nD = float(input())\nsigma = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Multivariate Regression & Path Analysis (Q46–Q50) → L446
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
**Multiple Regression Prediction:** Predict `y` given an intercept `b0`, `p` coefficients `B`, and `p` feature values `X`. `y = b0 + B1*X1 + B2*X2...` Print `y` rounded to 4 decimals.

Example:
Input:
2
1.5
2.0 3.0
10.0 5.0
Output:
36.5000

MD,
                'starter_code'        => "p = int(input())\nb0 = float(input())\nB = list(map(float, input().split()))\nX = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
**Multivariate Regression Prediction:** Predict `m` dependent variables at once. Read `m` (number of Ys) and `p` (number of Xs). Read `p` values for vector `X`. Then read an `m × p` coefficient matrix `W` (no intercepts here). Compute the predicted vector `Y = W · X`. Print `Y` space-separated, rounded to 4 decimals.

Example:
Input:
2 2
2.0 3.0
1.0 0.0
0.0 1.0
Output:
2.0000 3.0000

MD,
                'starter_code'        => "m, p = map(int, input().split())\nX = list(map(float, input().split()))\nW = [list(map(float, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
**Path Analysis (Indirect Effects):** In a path model X -> M -> Y, the indirect effect is the product of path coefficients `a` (X to M) and `b` (M to Y). The total effect is `direct + indirect`. Read `direct_c`, `path_a`, `path_b`. Print the indirect effect and total effect space-separated, rounded to 4 decimals.

Example:
Input:
0.2 0.5 0.8
Output:
0.4000 0.6000

MD,
                'starter_code'        => "c, a, b = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
**Residual Vector:** In multivariate regression, the error is `e = Y_true - Y_pred`. Read `m` (number of Ys), then vector `Y_true`, then vector `Y_pred`. Print the residual vector `e` space-separated, rounded to 4 decimals.

Example:
Input:
3
10 20 30
8 22 30
Output:
2.0000 -2.0000 0.0000

MD,
                'starter_code'        => "m = int(input())\ny_true = list(map(float, input().split()))\ny_pred = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**Multivariate MSE:** Calculate the Mean Squared Error across all predictions for all variables. Read `n` (samples) and `m` (variables). Then read `n` lines of `Y_true` vectors, followed by `n` lines of `Y_pred` vectors. Compute the average of all `n*m` squared errors. Print rounded to 4 decimals.

Example:
Input:
2 2
1.0 2.0
3.0 4.0
1.0 2.0
2.0 4.0
Output:
0.2500

MD,
                'starter_code'        => "n, m = map(int, input().split())\nY_true = [list(map(float, input().split())) for _ in range(n)]\nY_pred = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
        ];

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

        // Q1: Dimensions
        $seed(1, [
            ['input' => "2 3\n1.0 2.0 3.0\n4.0 5.0 6.0", 'expected_output' => "rows: 2\ncols: 3", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 5\n1 2 3 4 5", 'expected_output' => "rows: 1\ncols: 5", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 1\n1\n2\n3", 'expected_output' => "rows: 3\ncols: 1", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 2\n0 0\n0 0", 'expected_output' => "rows: 2\ncols: 2", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q2: Mean Vector
        $seed(2, [
            ['input' => "3 2\n1.0 2.0\n3.0 4.0\n5.0 0.0", 'expected_output' => "3.0000 2.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 3\n1 1 1\n-1 -1 -1", 'expected_output' => "0.0000 0.0000 0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 4\n5 10 15 20", 'expected_output' => "5.0000 10.0000 15.0000 20.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4 1\n2\n4\n6\n8", 'expected_output' => "5.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q3: Vector Addition
        $seed(3, [
            ['input' => "3\n1 2 3\n4 5 6", 'expected_output' => "5.00 7.00 9.00", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1.5 -1.5\n0.5 1.5", 'expected_output' => "2.00 0.00", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 0 0 0\n1 2 3 4", 'expected_output' => "1.00 2.00 3.00 4.00", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n100\n-50", 'expected_output' => "50.00", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q4: Dot Product
        $seed(4, [
            ['input' => "2\n1 3\n2 4", 'expected_output' => "14.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 0 0\n0 1 0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1 1 1\n2 2 2 2", 'expected_output' => "8.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0.5 0.5\n10 10", 'expected_output' => "10.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q5: Mahalanobis (Simplified)
        $seed(5, [
            ['input' => "2\n5.0 5.0\n2.0 2.0\n1.0 9.0", 'expected_output' => "10.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0 0 0\n0 0 0\n1 1 1", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n10 10\n0 0\n25 25", 'expected_output' => "8.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n5\n1\n2", 'expected_output' => "8.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q6: Center Data Matrix
        $seed(6, [
            ['input' => "2 2\n10 5\n20 15", 'expected_output' => "-5.00 -5.00\n5.00 5.00", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 1\n1\n2\n3", 'expected_output' => "-1.00\n0.00\n1.00", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3\n0 0 0\n2 4 6", 'expected_output' => "-1.00 -2.00 -3.00\n1.00 2.00 3.00", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 2\n5 10", 'expected_output' => "0.00 0.00", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q7: Sample Variance
        $seed(7, [
            ['input' => "3\n1\n3\n5", 'expected_output' => "4.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10\n10", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n2\n4\n4\n4.5", 'expected_output' => "1.2292", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n-5\n5", 'expected_output' => "50.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q8: Sample Covariance
        $seed(8, [
            ['input' => "3\n1 2 3\n2 4 6", 'expected_output' => "2.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n3 2 1", 'expected_output' => "-1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1 2 2\n1 2 1 2", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0 10\n0 -10", 'expected_output' => "-50.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q9: Total Variation
        $seed(9, [
            ['input' => "3 2\n1 5\n3 5\n5 5", 'expected_output' => "4.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 3\n0 0 0\n2 4 6", 'expected_output' => "28.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 2\n1 1\n2 2\n3 3\n4 4", 'expected_output' => "3.3333", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 1\n-1\n1", 'expected_output' => "2.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q10: Pearson Correlation
        $seed(10, [
            ['input' => "3\n1 2 3\n2 4 6", 'expected_output' => "1.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n3 2 1", 'expected_output' => "-1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4\n1 4 2 5", 'expected_output' => "0.6000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n0 5 10\n0 10 20", 'expected_output' => "1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q11: Vector Norm
        $seed(11, [
            ['input' => "3\n3 0 4", 'expected_output' => "5.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 1", 'expected_output' => "1.4142", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0.5 0.5 0.5 0.5", 'expected_output' => "1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n-10", 'expected_output' => "10.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q12: Unit Normalization
        $seed(12, [
            ['input' => "2\n3 4", 'expected_output' => "0.6000 0.8000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 0 0", 'expected_output' => "1.0000 0.0000 0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 1", 'expected_output' => "0.7071 0.7071", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n2 2 2 2", 'expected_output' => "0.5000 0.5000 0.5000 0.5000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q13: 1D Projection
        $seed(13, [
            ['input' => "2\n2 4\n0.6 0.8", 'expected_output' => "4.4000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5 5 5\n1 0 0", 'expected_output' => "5.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n-3 4\n0 1", 'expected_output' => "4.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n1 1 1 1\n0.5 0.5 0.5 0.5", 'expected_output' => "2.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q14: Projected Variance
        $seed(14, [
            ['input' => "3 2\n1.0 0.0\n1 1\n2 2\n3 3", 'expected_output' => "1.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n0.6 0.8\n0 0\n3 4\n6 8", 'expected_output' => "25.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n0.7071 0.7071\n1 0\n0 1", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4 1\n1.0\n1\n2\n3\n4", 'expected_output' => "1.6667", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q15: Orthogonal Residual
        $seed(15, [
            ['input' => "2\n2 2\n1.0 0.0", 'expected_output' => "0.0000 2.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3 4\n0.6 0.8", 'expected_output' => "0.0000 0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3\n0 0 1", 'expected_output' => "1.0000 2.0000 0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n1 0\n0.7071 0.7071", 'expected_output' => "0.5000 -0.5000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q16: Communality
        $seed(16, [
            ['input' => "2\n0.6 0.8", 'expected_output' => "1.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.5 0.5 0.5", 'expected_output' => "0.7500", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0.9", 'expected_output' => "0.8100", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n0.1 0.2 0.3 0.4", 'expected_output' => "0.3000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q17: Specific Variance
        $seed(17, [
            ['input' => "2\n0.5 0.5", 'expected_output' => "0.5000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.6 0.8", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.1 0.1 0.1", 'expected_output' => "0.9700", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n0.9", 'expected_output' => "0.1900", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q18: Reproduced Covariance
        $seed(18, [
            ['input' => "2\n0.8 0.2\n0.6 0.4", 'expected_output' => "0.5600", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0.5\n0.5", 'expected_output' => "0.2500", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0 0\n0 1 0", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0.5 -0.5\n0.5 0.5", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q19: Residual Covariance
        $seed(19, [
            ['input' => "0.60\n2\n0.8 0.2\n0.6 0.4", 'expected_output' => "0.0400", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.25\n1\n0.5\n0.5", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.10\n2\n0.5 -0.5\n0.5 0.5", 'expected_output' => "0.1000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.50\n3\n0.5 0.5 0.5\n0.5 0.5 0.5", 'expected_output' => "-0.2500", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q20: Variance Explained by Factor
        $seed(20, [
            ['input' => "3\n0.8 0.1\n0.7 0.2\n0.6 0.3\n0", 'expected_output' => "1.4900", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.5 0.5\n0.5 0.5\n1", 'expected_output' => "0.5000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0\n0 1\n1 1\n0", 'expected_output' => "2.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n0.9 0.1\n1", 'expected_output' => "0.0100", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q21: Grand Mean
        $seed(21, [
            ['input' => "4 2\n2 4\n4 6\n8 10\n10 12", 'expected_output' => "6.0000 8.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 3\n1 1 1\n3 3 3", 'expected_output' => "2.0000 2.0000 2.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2\n0 0\n10 10\n-10 -10", 'expected_output' => "0.0000 0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 4\n1 2 3 4", 'expected_output' => "1.0000 2.0000 3.0000 4.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q22: Group Means
        $seed(22, [
            ['input' => "4\n1 2.0 4.0\n2 8.0 10.0\n1 4.0 6.0\n2 10.0 12.0", 'expected_output' => "Group 1: 3.0000 5.0000\nGroup 2: 9.0000 11.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5 1 1\n5 3 3\n5 5 5", 'expected_output' => "Group 5: 3.0000 3.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n2 10 20\n1 0 0", 'expected_output' => "Group 1: 0.0000 0.0000\nGroup 2: 10.0000 20.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n1 0 0\n1 2 2\n3 1 1\n3 -1 -1", 'expected_output' => "Group 1: 1.0000 1.0000\nGroup 3: 0.0000 0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q23: SSW
        $seed(23, [
            ['input' => "4\n1 2.0\n1 4.0\n2 8.0\n2 10.0", 'expected_output' => "4.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 5.0\n1 5.0\n2 10.0\n2 10.0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 1.0\n1 2.0\n1 3.0", 'expected_output' => "2.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n1 -1.0\n1 1.0\n2 -2.0\n2 2.0", 'expected_output' => "10.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q24: SSB
        $seed(24, [
            ['input' => "4\n1 2.0\n1 4.0\n2 8.0\n2 10.0", 'expected_output' => "36.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 5.0\n1 5.0\n2 5.0\n2 5.0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 0.0\n1 0.0\n2 10.0\n2 10.0", 'expected_output' => "100.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n1 1.0\n2 2.0\n3 3.0", 'expected_output' => "2.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q25: SST
        $seed(25, [
            ['input' => "4\n1 2.0\n1 4.0\n2 8.0\n2 10.0", 'expected_output' => "40.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 5.0\n1 5.0\n2 5.0\n2 5.0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 1.0\n2 2.0\n3 3.0", 'expected_output' => "2.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n1 0.0\n1 10.0\n2 0.0\n2 10.0", 'expected_output' => "100.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q26: Decision Boundary
        $seed(26, [
            ['input' => "2.0 8.0", 'expected_output' => "5.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "-10.0 10.0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "100.0 200.0", 'expected_output' => "150.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.0 1.0", 'expected_output' => "0.5000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q27: 1D Nearest Centroid
        $seed(27, [
            ['input' => "2.0 8.0\n6.0", 'expected_output' => "2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0 8.0\n5.0", 'expected_output' => "1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0 -10.0\n-5.0", 'expected_output' => "2", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.0 100.0\n10.0", 'expected_output' => "1", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q28: 2D Nearest Centroid
        $seed(28, [
            ['input' => "0.0 0.0\n10.0 10.0\n2.0 2.0", 'expected_output' => "1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0 0.0\n10.0 0.0\n6.0 0.0", 'expected_output' => "2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0 5.0\n-5.0 -5.0\n0.0 0.0", 'expected_output' => "1", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0 1.0\n2.0 2.0\n3.0 3.0", 'expected_output' => "2", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q29: Pooled Variance
        $seed(29, [
            ['input' => "10 4.0\n15 6.0", 'expected_output' => "5.2174", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5 10.0\n5 10.0", 'expected_output' => "10.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 0.0\n3 5.0", 'expected_output' => "3.3333", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "20 1.0\n10 4.0", 'expected_output' => "1.9643", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q30: Fisher Objective
        $seed(30, [
            ['input' => "8.0 2.0\n2.0 2.0", 'expected_output' => "9.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "10.0 10.0\n5.0 5.0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0 10.0\n0.0 10.0", 'expected_output' => "10.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5.0 -5.0\n2.0 3.0", 'expected_output' => "20.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q31: Euclidean Distance
        $seed(31, [
            ['input' => "2\n0 0\n3 4", 'expected_output' => "5.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1 1\n1 1 1", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 0 0 0\n1 1 1 1", 'expected_output' => "2.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n-5 0\n5 0", 'expected_output' => "10.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q32: K-Means Assignment
        $seed(32, [
            ['input' => "2 2\n0 0\n10 10\n2\n1 1\n9 9", 'expected_output' => "0\n1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 1\n0\n5\n3\n-1\n3\n6", 'expected_output' => "0\n1\n1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2\n0 0\n10 0\n0 10\n3\n1 1\n9 1\n1 9", 'expected_output' => "0\n1\n2", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 2\n0 0\n0 0\n1\n5 5", 'expected_output' => "0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q33: Update Centroid
        $seed(33, [
            ['input' => "2\n3\n0 0\n0 6\n6 0", 'expected_output' => "2.0000 2.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n4\n1\n2\n3\n4", 'expected_output' => "2.5000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2\n-1 -1 -1\n1 1 1", 'expected_output' => "0.0000 0.0000 0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n1\n10 20", 'expected_output' => "10.0000 20.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q34: WCSS
        $seed(34, [
            ['input' => "2\n2 2\n0 0\n4 4", 'expected_output' => "16.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0 0\n0 0\n0 0\n0 0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 0\n3 0\n0 4", 'expected_output' => "25.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n5 5\n5 5", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q35: Single-Linkage
        $seed(35, [
            ['input' => "2\n0 0\n0 1\n2\n10 10\n0 3", 'expected_output' => "2.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0 0\n1\n3 4", 'expected_output' => "5.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 1\n2 2\n2\n2 2\n3 3", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n0 0\n10 0\n20 0\n1\n5 0", 'expected_output' => "5.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q36: Linear Combination
        $seed(36, [
            ['input' => "2\n0.5 0.5\n10.0 20.0", 'expected_output' => "15.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 0 0\n5 10 15", 'expected_output' => "5.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1 1 1\n1 2 3 4", 'expected_output' => "10.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n1 -1\n5 5", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q37: Batch Combos
        $seed(37, [
            ['input' => "2\n1.0 -1.0\n2\n5.0 2.0\n10.0 10.0", 'expected_output' => "3.0000\n0.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.5 0.5 0.5\n2\n2 2 2\n4 4 4", 'expected_output' => "3.0000\n6.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2.0\n3\n1\n2\n3", 'expected_output' => "2.0000\n4.0000\n6.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0 1\n2\n99 1\n88 2", 'expected_output' => "1.0000\n2.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q38: Canonical Covariance
        $seed(38, [
            ['input' => "3\n1 2 3\n4 5 6", 'expected_output' => "1.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n3 2 1", 'expected_output' => "-1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1 2 2\n1 2 1 2", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0 10\n0 10", 'expected_output' => "50.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q39: Unit Variance
        $seed(39, [
            ['input' => "3\n-1.0 0.0 1.0", 'expected_output' => "-1.0000 0.0000 1.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 10", 'expected_output' => "0.0000 1.4142", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n2 4 4 2", 'expected_output' => "1.7321 3.4641 3.4641 1.7321", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n10 20 30", 'expected_output' => "1.0000 2.0000 3.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q40: Cross-Covariance Entry
        $seed(40, [
            ['input' => "4\n0 0\n1 1\n2 2\n3 3", 'expected_output' => "1.6667", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 3\n2 2\n3 1", 'expected_output' => "-1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n5 10\n15 20", 'expected_output' => "50.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n0 0\n0 1\n0 2", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q41: Pairwise Distance
        $seed(41, [
            ['input' => "3\n1 3 6", 'expected_output' => "0.00 2.00 5.00\n2.00 0.00 3.00\n5.00 3.00 0.00", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 10", 'expected_output' => "0.00 10.00\n10.00 0.00", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 0 0 0", 'expected_output' => "0.00 0.00 0.00 0.00\n0.00 0.00 0.00 0.00\n0.00 0.00 0.00 0.00\n0.00 0.00 0.00 0.00", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n-5 5", 'expected_output' => "0.00 10.00\n10.00 0.00", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q42: Row Means
        $seed(42, [
            ['input' => "2\n1 3\n5 7", 'expected_output' => "2.0000 6.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1 1\n2 2 2\n3 3 3", 'expected_output' => "1.0000 2.0000 3.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 0\n0 0", 'expected_output' => "0.0000 0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n5", 'expected_output' => "5.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q43: Col Means
        $seed(43, [
            ['input' => "2\n1 3\n5 7", 'expected_output' => "3.0000 5.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n1 2 3\n1 2 3", 'expected_output' => "1.0000 2.0000 3.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 0\n0 0", 'expected_output' => "0.0000 0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n5", 'expected_output' => "5.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q44: Grand Mean
        $seed(44, [
            ['input' => "2\n1 3\n5 7", 'expected_output' => "4.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1 1\n1 1 1\n1 1 1", 'expected_output' => "1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n-10 10\n-5 5", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n5", 'expected_output' => "5.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q45: Affinity
        $seed(45, [
            ['input' => "2.0\n1.0", 'expected_output' => "0.1353", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0", 'expected_output' => "1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0\n5.0", 'expected_output' => "0.1353", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0\n0.1", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q46: Multiple Regression
        $seed(46, [
            ['input' => "2\n1.5\n2.0 3.0\n10.0 5.0", 'expected_output' => "36.5000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.0\n1 1 1\n5 5 5", 'expected_output' => "15.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n10.0\n-2.0\n4.0", 'expected_output' => "2.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0.0\n0 0\n100 100", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q47: Multivariate Prediction
        $seed(47, [
            ['input' => "2 2\n2.0 3.0\n1.0 0.0\n0.0 1.0", 'expected_output' => "2.0000 3.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 3\n1 2 3\n1 1 1", 'expected_output' => "6.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 1\n5\n2\n3", 'expected_output' => "10.0000 15.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 2\n1 1\n0.5 0.5\n-0.5 -0.5", 'expected_output' => "1.0000 -1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q48: Indirect Effects
        $seed(48, [
            ['input' => "0.2 0.5 0.8", 'expected_output' => "0.4000 0.6000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0 1.0 1.0", 'expected_output' => "1.0000 1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5 0.0 0.5", 'expected_output' => "0.0000 0.5000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.1 -0.5 0.4", 'expected_output' => "-0.2000 -0.1000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q49: Residual Vector
        $seed(49, [
            ['input' => "3\n10 20 30\n8 22 30", 'expected_output' => "2.0000 -2.0000 0.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0\n1 1", 'expected_output' => "-1.0000 -1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4\n1 2 3 4", 'expected_output' => "0.0000 0.0000 0.0000 0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n5\n10", 'expected_output' => "-5.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q50: Multivariate MSE
        $seed(50, [
            ['input' => "2 2\n1.0 2.0\n3.0 4.0\n1.0 2.0\n2.0 4.0", 'expected_output' => "0.2500", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 1\n5.0\n10.0\n5.0\n10.0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 3\n1 2 3\n0 0 0", 'expected_output' => "4.6667", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 2\n1 1\n1 1\n0 0\n2 2", 'expected_output' => "1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        $this->command->info('✅ Module 16 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}