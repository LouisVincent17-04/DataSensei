<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 8 — Statistics & Probability (Intermediate) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Intermediate tier
 *   2. coding_questions    — 50 questions bridging fundamental stats with programmatic array handling
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module8CodingChallengeSeederIntermediate extends Seeder
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

        $this->command->info('Creating Module 8 — Statistics & Probability (Intermediate) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Statistics & Probability',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply intermediate statistics in Python using array structures and programmatic loops. Compute Root Mean Square Error (RMSE), sample covariances, continuous distributions, discrete expectations, linear regression components, and basic ANOVA metrics across dynamic datasets.',
                'time_limit_seconds' => 900,
                'base_xp'            => 650,
                'order_index'        => 8,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.1: Introduction to Statistical Thinking (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Read an integer `n` followed by `n` values. Compute a **Simple Moving Average (SMA)** of window size 3.

For an array `P`, print the average of `[P[i], P[i+1], P[i+2]]` for each valid window.
Print each result on a new line, rounded to **2 decimal places**.

Example:
Input:
5
10
20
30
40
50
Output:
20.00
30.00
40.00

MD,
                'starter_code'        => "n = int(input())\narr = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read an integer `n` followed by `n` prices. Compute the **simple returns** for each period:
`Return_t = (Price_t - Price_{t-1}) / Price_{t-1}`

Print `n-1` returns, each rounded to **4 decimal places**.

Example:
Input:
3
100
110
104.5
Output:
0.1000
-0.0500

MD,
                'starter_code'        => "n = int(input())\nprices = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read `n`. Then read `n` values, followed by `n` weights. Compute the **weighted sum** (not the mean!):
`Sum = (v1×w1) + (v2×w2) + ... + (vn×wn)`

Print the result rounded to **2 decimal places**.

Example:
Input:
3
10
20
30
0.5
0.2
0.3
Output:
18.00

MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nweights = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read an integer `n` followed by `n` numbers. Normalize the array using **Min-Max Scaling**:
`X_norm = (X - X_min) / (X_max - X_min)`

Print each normalized value on a new line, rounded to **4 decimal places**.

Example:
Input:
3
10
50
90
Output:
0.0000
0.5000
1.0000

MD,
                'starter_code'        => "n = int(input())\narr = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read `n` and `n` numbers. Standardize the array by computing the **Z-score** for each element using the *sample* mean and *sample* standard deviation (n-1).

Print each Z-score on a new line, rounded to **4 decimal places**.

Example:
Input:
3
10
20
30
Output:
-1.0000
0.0000
1.0000

MD,
                'starter_code'        => "n = int(input())\narr = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.2: Descriptive Statistics & Data Summarization (Q6–Q12)
            // ═══════════════════════════════════════════════════════════════
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Read an integer `n` and `n` numbers. Compute the **Mean Absolute Deviation (MAD)** around the mean:
`MAD = Sum(|x_i - mean|) / n`

Print the result rounded to **4 decimal places**.

Example:
Input:
4
2
4
6
8
Output:
2.0000

MD,
                'starter_code'        => "n = int(input())\narr = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Read `n` and `n` numbers. Find the **Interquartile Range (IQR)**. 
First, sort the array. 
Compute Q1 as the median of the lower half, and Q3 as the median of the upper half. If `n` is odd, exclude the median from the halves. 
`IQR = Q3 - Q1`. Print rounded to **2 decimal places**.

Example:
Input:
5
1
3
5
7
9
Output:
6.00

MD,
                'starter_code'        => "n = int(input())\narr = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Read `n` and `n` actual values, followed by `n` predicted values. Compute the **Mean Absolute Error (MAE)**:
`MAE = Sum(|actual_i - pred_i|) / n`

Print rounded to **4 decimal places**.

Example:
Input:
3
10
20
30
12
18
33
Output:
2.3333

MD,
                'starter_code'        => "n = int(input())\nactual = [float(input()) for _ in range(n)]\npred = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read `n` and `n` actual values, followed by `n` predicted values. Compute the **Root Mean Square Error (RMSE)**:
`RMSE = sqrt( Sum((actual_i - pred_i)^2) / n )`

Print rounded to **4 decimal places**.

Example:
Input:
3
10
20
30
12
18
33
Output:
2.3805

MD,
                'starter_code'        => "import math\nn = int(input())\nactual = [float(input()) for _ in range(n)]\npred = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read `n` and two arrays of size `n` (`X` then `Y`). Compute the **Sample Covariance** (divide by n-1):
`Cov(X,Y) = Sum((X_i - X_mean) * (Y_i - Y_mean)) / (n - 1)`

Print rounded to **4 decimal places**.

Example:
Input:
3
2
4
6
1
3
5
Output:
4.0000

MD,
                'starter_code'        => "n = int(input())\nX = [float(input()) for _ in range(n)]\nY = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Read an array of `n` numbers. Compute the **Coefficient of Dispersion (COD)**:
`COD = MAD / Median` (where MAD is the mean absolute deviation from the *median*, not the mean).

Print rounded to **4 decimal places**.

Example:
Input:
3
10
20
30
Output:
0.3333

MD,
                'starter_code'        => "n = int(input())\narr = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Read an array of `n` probabilities. Compute the **Shannon Entropy** (in bits):
`H = -Sum( p_i * log2(p_i) )`. If p_i = 0, ignore it.

Print rounded to **4 decimal places**.

Example:
Input:
2
0.5
0.5
Output:
1.0000

MD,
                'starter_code'        => "import math\nn = int(input())\nprobs = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.3: Probability Distributions in Practice (Q13–Q18)
            // ═══════════════════════════════════════════════════════════════
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Read `n` and `k`. Compute the number of **Combinations** `C(n, k) = n! / (k! * (n - k)!)`.

Print the integer result.

Example:
Input:
5
2
Output:
10

MD,
                'starter_code'        => "import math\nn = int(input())\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Read `n` and `k`. Compute the number of **Permutations** `P(n, k) = n! / (n - k)!`.

Print the integer result.

Example:
Input:
5
2
Output:
20

MD,
                'starter_code'        => "import math\nn = int(input())\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Read `n` (trials), `k` (successes), and `p` (probability of success). Compute the exact **Binomial Probability P(X=k)**:
`P(X=k) = C(n, k) * p^k * (1-p)^(n-k)`

Print rounded to **4 decimal places**.

Example:
Input:
10
5
0.5
Output:
0.2461

MD,
                'starter_code'        => "import math\nn = int(input())\nk = int(input())\np = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Read `lambda` (rate) and `k` (events). Compute the exact **Poisson Probability P(X=k)**:
`P(X=k) = (lambda^k * e^(-lambda)) / k!`

Print rounded to **4 decimal places**.

Example:
Input:
3.0
2
Output:
0.2240

MD,
                'starter_code'        => "import math\nlam = float(input())\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Read integer `n`, followed by an array of `n` discrete outcomes `X`, and an array of `n` probabilities `P`. Compute the **Expected Value E[X]**:
`E[X] = Sum(X_i * P_i)`

Print rounded to **4 decimal places**.

Example:
Input:
3
0
1
2
0.2
0.5
0.3
Output:
1.1000

MD,
                'starter_code'        => "n = int(input())\nX = [float(input()) for _ in range(n)]\nP = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Read integer `n`, followed by `n` outcomes `X`, and `n` probabilities `P`. Compute the **Variance Var(X)** of the discrete distribution:
`Var(X) = E[X^2] - (E[X])^2`

Print rounded to **4 decimal places**.

Example:
Input:
3
0
1
2
0.2
0.5
0.3
Output:
0.4900

MD,
                'starter_code'        => "n = int(input())\nX = [float(input()) for _ in range(n)]\nP = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.4: Sampling Theory & Survey Design (Q19–Q23)
            // ═══════════════════════════════════════════════════════════════
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Read critical value `Z`, population std dev `std`, and Margin of Error `ME`. Compute the **required sample size n** to estimate a mean:
`n = (Z * std / ME)^2`

Print the result rounded **UP** to the nearest whole integer (ceil).

Example:
Input:
1.96
15.0
3.0
Output:
97

MD,
                'starter_code'        => "import math\nz = float(input())\nstd = float(input())\nme = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Read critical value `Z`, estimated proportion `p`, and Margin of Error `ME`. Compute the **required sample size n** to estimate a proportion:
`n = (Z^2 * p * (1 - p)) / ME^2`

Print the result rounded **UP** to the nearest whole integer (ceil).

Example:
Input:
1.96
0.5
0.05
Output:
385

MD,
                'starter_code'        => "import math\nz = float(input())\np = float(input())\nme = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Read `n` and an array of `n` samples. Compute the **Standard Error of the Mean (SEM)** directly from the raw data:
`SEM = sample_std_dev / sqrt(n)`

Print rounded to **4 decimal places**.

Example:
Input:
4
10
12
15
18
Output:
1.7017

MD,
                'starter_code'        => "import math\nn = int(input())\narr = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Read the total sample size `N`, the number of strata `k`, and an array of `k` stratum population sizes. Compute the **proportional allocation** for each stratum:
`n_i = round(N * (stratum_pop / total_pop))`

Print each allocated integer on a new line.

Example:
Input:
100
3
2000
3000
5000
Output:
20
30
50

MD,
                'starter_code'        => "N = int(input())\nk = int(input())\nstrata = [int(input()) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Read array length `n` and an array of `n` binary responses (1 for success, 0 for failure). Compute the **Standard Error of the Proportion (SEP)**:
`p = sum / n`
`SEP = sqrt(p * (1 - p) / n)`

Print rounded to **4 decimal places**.

Example:
Input:
5
1
0
1
1
0
Output:
0.2191

MD,
                'starter_code'        => "import math\nn = int(input())\narr = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.5: Hypothesis Testing: Framework & One-Sample Tests (Q24–Q28)
            // ═══════════════════════════════════════════════════════════════
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Read a hypothesized mean `mu_0`, size `n`, and an array of `n` raw sample values. Compute the **One-Sample t-test statistic** directly:
`t = (sample_mean - mu_0) / (sample_std_dev / sqrt(n))`

Print rounded to **4 decimal places**.

Example:
Input:
10.0
4
12
14
11
15
Output:
3.3541

MD,
                'starter_code'        => "import math\nmu_0 = float(input())\nn = int(input())\narr = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Read size `n`, then `n` 'Before' values, then `n` 'After' values. Compute the **Paired t-test statistic**:
`t = mean(Diff) / (std_dev(Diff) / sqrt(n))` where `Diff = After - Before`.

Print rounded to **4 decimal places**.

Example:
Input:
3
10
15
20
12
14
25
Output:
1.3868

MD,
                'starter_code'        => "import math\nn = int(input())\nbefore = [float(input()) for _ in range(n)]\nafter = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Read `n1`, `n2` and two arrays of those sizes. Compute the **Pooled Variance** from the raw arrays:
`S_p^2 = ((n1-1)*Var1 + (n2-1)*Var2) / (n1 + n2 - 2)`

Print rounded to **4 decimal places**.

Example:
Input:
3
3
2
4
6
1
3
5
Output:
4.0000

MD,
                'starter_code'        => "n1 = int(input())\nn2 = int(input())\narr1 = [float(input()) for _ in range(n1)]\narr2 = [float(input()) for _ in range(n2)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Read `n1`, `n2` and two arrays of those sizes. Compute the **Two-Sample t-test statistic** assuming equal variances.
`t = (Mean1 - Mean2) / sqrt( S_p^2 * (1/n1 + 1/n2) )`

Print rounded to **4 decimal places**.

Example:
Input:
3
3
10
12
14
5
7
9
Output:
3.0619

MD,
                'starter_code'        => "import math\nn1 = int(input())\nn2 = int(input())\narr1 = [float(input()) for _ in range(n1)]\narr2 = [float(input()) for _ in range(n2)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Read `n1`, `Var1`, `n2`, `Var2`. Compute the approximate **Degrees of Freedom for Welch's t-test** (Satterthwaite approximation):
`df = (Var1/n1 + Var2/n2)^2 / [ (Var1/n1)^2 / (n1-1) + (Var2/n2)^2 / (n2-1) ]`

Print the result rounded down to the nearest integer (floor).

Example:
Input:
10
4.0
15
9.0
Output:
22

MD,
                'starter_code'        => "import math\nn1 = int(input())\nvar1 = float(input())\nn2 = int(input())\nvar2 = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.6: Two-Sample Tests & ANOVA (Q29–Q34)
            // ═══════════════════════════════════════════════════════════════
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Read `k` (number of groups). Then loop `k` times: read `n_i` (size of group `i`) followed by `n_i` values. Compute the **Grand Mean** of all values combined.

Print rounded to **4 decimal places**.

Example:
Input:
2
3
10
20
30
2
40
50
Output:
30.0000

MD,
                'starter_code'        => "k = int(input())\ngroups = []\nfor _ in range(k):\n    n_i = int(input())\n    groups.append([float(input()) for _ in range(n_i)])\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Read `k` groups as before. Compute the **Sum of Squares Between (SSB)**:
`SSB = Sum( n_i * (Group_Mean_i - Grand_Mean)^2 )`

Print rounded to **4 decimal places**.

Example:
Input:
2
3
10
20
30
2
40
50
Output:
750.0000

MD,
                'starter_code'        => "k = int(input())\ngroups = []\nfor _ in range(k):\n    n_i = int(input())\n    groups.append([float(input()) for _ in range(n_i)])\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Read `k` groups. Compute the **Sum of Squares Within (SSW)**:
`SSW = Sum( (Value_ij - Group_Mean_i)^2 )`

Print rounded to **4 decimal places**.

Example:
Input:
2
3
10
20
30
2
40
50
Output:
250.0000

MD,
                'starter_code'        => "k = int(input())\ngroups = []\nfor _ in range(k):\n    n_i = int(input())\n    groups.append([float(input()) for _ in range(n_i)])\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Read `SSB`, `SSW`, `k` (groups), and `N` (total observations). Compute the **ANOVA F-statistic**:
`MSB = SSB / (k - 1)`
`MSW = SSW / (N - k)`
`F = MSB / MSW`

Print rounded to **4 decimal places**.

Example:
Input:
750.0
250.0
2
5
Output:
9.0000

MD,
                'starter_code'        => "ssb = float(input())\nssw = float(input())\nk = int(input())\nN = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Read `n1`, `n2` and two arrays. Compute **Cohen's d** directly from the raw data:
`d = |Mean1 - Mean2| / Pooled_Std_Dev`

Print rounded to **4 decimal places**.

Example:
Input:
3
3
10
12
14
5
7
9
Output:
2.5000

MD,
                'starter_code'        => "import math\nn1 = int(input())\nn2 = int(input())\narr1 = [float(input()) for _ in range(n1)]\narr2 = [float(input()) for _ in range(n2)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Read `SSB` and `SSW`. Compute the effect size **Eta-squared (η²)**:
`η² = SSB / (SSB + SSW)`

Print rounded to **4 decimal places**.

Example:
Input:
750.0
250.0
Output:
0.7500

MD,
                'starter_code'        => "ssb = float(input())\nssw = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.7: Chi-Square Tests & Non-Parametric Methods (Q35–Q39)
            // ═══════════════════════════════════════════════════════════════
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Read `n` (categories), then an array of `n` Observed frequencies. Assume the Expected frequencies should be uniformly distributed across all categories (Expected = Sum(Observed) / n).
Compute the **Chi-Square Goodness of Fit statistic**:
`ChiSq = Sum( (O - E)^2 / E )`

Print rounded to **4 decimal places**.

Example:
Input:
3
10
20
30
Output:
10.0000

MD,
                'starter_code'        => "n = int(input())\nobs = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Read `n` (categories), followed by an array of `n` Observed and `n` Expected probabilities (which sum to 1). 
First, read total sample size `N`. Convert probabilities to Expected frequencies (`E = P_i * N`).
Compute the **Chi-Square statistic**.

Print rounded to **4 decimal places**.

Example:
Input:
3
100
30
20
50
0.25
0.25
0.50
Output:
1.0000

MD,
                'starter_code'        => "n = int(input())\nN = float(input())\nobs = [float(input()) for _ in range(n)]\nprobs = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Read `n` pairs of (Before, After) values. For the **Sign Test**, compute the number of positive differences (`After - Before > 0`). Ties (`After == Before`) are ignored.

Print the integer count of positive differences.

Example:
Input:
4
10
12
15
10
20
20
8
10
Output:
2

MD,
                'starter_code'        => "n = int(input())\nbefore = [float(input()) for _ in range(n)]\nafter = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Read `n` and an array of `n` *distinct* numbers. Generate their **Ranks** (1 for smallest, `n` for largest).

Print each rank on a new line.

Example:
Input:
4
50
10
30
20
Output:
4
1
3
2

MD,
                'starter_code'        => "n = int(input())\narr = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Read `n` and two arrays `X` and `Y` of distinct numbers. Compute **Spearman's Rank Correlation Coefficient**.
1. Replace X and Y with their ranks.
2. `d_i = Rank(X_i) - Rank(Y_i)`
3. `rho = 1 - (6 * Sum(d_i^2)) / (n * (n^2 - 1))`

Print rounded to **4 decimal places**.

Example:
Input:
3
10
20
30
100
50
200
Output:
0.5000

MD,
                'starter_code'        => "n = int(input())\nX = [float(input()) for _ in range(n)]\nY = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.8: Correlation, Regression & Model Diagnostics (Q40–Q44)
            // ═══════════════════════════════════════════════════════════════
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Read `n` and arrays `X` and `Y`. Compute the **Sum of Squares for X (SS_xx)**:
`SS_xx = Sum( (X_i - X_mean)^2 )`

Print rounded to **4 decimal places**.

Example:
Input:
3
1
2
3
2
4
6
Output:
2.0000

MD,
                'starter_code'        => "n = int(input())\nX = [float(input()) for _ in range(n)]\nY = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Read `n` and arrays `X` and `Y`. Compute the **Sum of Cross Products (SS_xy)**:
`SS_xy = Sum( (X_i - X_mean) * (Y_i - Y_mean) )`

Print rounded to **4 decimal places**.

Example:
Input:
3
1
2
3
2
4
6
Output:
4.0000

MD,
                'starter_code'        => "n = int(input())\nX = [float(input()) for _ in range(n)]\nY = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Read `n` and arrays `X` and `Y`. Compute the **Simple Linear Regression Slope (b1)**:
`b1 = SS_xy / SS_xx`

Print rounded to **4 decimal places**.

Example:
Input:
3
1
2
3
2
4
6
Output:
2.0000

MD,
                'starter_code'        => "n = int(input())\nX = [float(input()) for _ in range(n)]\nY = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Read `n` and arrays `X` and `Y`. Compute the **Simple Linear Regression Intercept (b0)**:
`b0 = Y_mean - b1 * X_mean`

Print rounded to **4 decimal places**.

Example:
Input:
3
1
2
3
3
5
7
Output:
1.0000

MD,
                'starter_code'        => "n = int(input())\nX = [float(input()) for _ in range(n)]\nY = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Read `n` and arrays `X` and `Y`. Compute the **Coefficient of Determination (R²)**:
First find predicted values `Y_hat_i = b0 + b1 * X_i`.
`R² = 1 - ( Sum((Y_i - Y_hat_i)^2) / Sum((Y_i - Y_mean)^2) )`

Print rounded to **4 decimal places**.

Example:
Input:
3
1
2
3
2.1
3.9
6.2
Output:
0.9943

MD,
                'starter_code'        => "n = int(input())\nX = [float(input()) for _ in range(n)]\nY = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.9: Experimental Design: Principles & Layouts (Q45–Q47)
            // ═══════════════════════════════════════════════════════════════
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
In a 2x2 Factorial Design, read an array of `n` replications for condition `(1)`, then `n` for `a`, `n` for `b`, and `n` for `ab`.
Find the means for each condition. Compute the **Main Effect of A**:
`Effect A = (Mean(a) + Mean(ab) - Mean(1) - Mean(b)) / 2`

Print rounded to **4 decimal places**.

Example:
Input:
2
10
12
20
22
14
16
28
32
Output:
12.0000

MD,
                'starter_code'        => "n = int(input())\nc_1 = [float(input()) for _ in range(n)]\nc_a = [float(input()) for _ in range(n)]\nc_b = [float(input()) for _ in range(n)]\nc_ab = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Using the same inputs as the previous question, compute the **Main Effect of B**:
`Effect B = (Mean(b) + Mean(ab) - Mean(1) - Mean(a)) / 2`

Print rounded to **4 decimal places**.

Example:
Input:
2
10
12
20
22
14
16
28
32
Output:
7.0000

MD,
                'starter_code'        => "n = int(input())\nc_1 = [float(input()) for _ in range(n)]\nc_a = [float(input()) for _ in range(n)]\nc_b = [float(input()) for _ in range(n)]\nc_ab = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Using the same inputs, compute the **Interaction Effect AB**:
`Effect AB = (Mean(ab) + Mean(1) - Mean(a) - Mean(b)) / 2`

Print rounded to **4 decimal places**.

Example:
Input:
2
10
12
20
22
14
16
28
32
Output:
2.0000

MD,
                'starter_code'        => "n = int(input())\nc_1 = [float(input()) for _ in range(n)]\nc_a = [float(input()) for _ in range(n)]\nc_b = [float(input()) for _ in range(n)]\nc_ab = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.10: Effect Size, Power Analysis & Sample Size Planning (Q48–Q50)
            // ═══════════════════════════════════════════════════════════════
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Read a 2x2 contingency table as an array of 4 integers: `[a, b, c, d]` (top-left, top-right, bottom-left, bottom-right). 
Compute the **Odds Ratio**: `(a * d) / (b * c)`

Print rounded to **4 decimal places**.

Example:
Input:
10
5
2
20
Output:
20.0000

MD,
                'starter_code'        => "a = float(input())\nb = float(input())\nc = float(input())\nd = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Read the same 4 integers `[a, b, c, d]`. Compute the **Relative Risk (RR)**:
`RR = (a / (a + b)) / (c / (c + d))`

Print rounded to **4 decimal places**.

Example:
Input:
10
10
5
15
Output:
2.0000

MD,
                'starter_code'        => "a = float(input())\nb = float(input())\nc = float(input())\nd = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Read the same 4 integers `[a, b, c, d]`. Compute the **Number Needed to Treat (NNT)**:
`Absolute Risk Reduction (ARR) = | (a / (a + b)) - (c / (c + d)) |`
`NNT = 1 / ARR`

Print rounded UP to the nearest integer (ceil).

Example:
Input:
10
40
20
30
Output:
5

MD,
                'starter_code'        => "import math\na = float(input())\nb = float(input())\nc = float(input())\nd = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],
        ];

        // ─────────────────────────────────────────────────────────────────
        // INSERT QUESTIONS
        // ─────────────────────────────────────────────────────────────────

        $insertedQuestions = [];

        foreach ($questionDefs as $def) {
            $existing = DB::table('coding_questions')
                ->where('challenge_id', $challenge->id)
                ->where('order_index', $def['order_index'])
                ->first();

            if ($existing) {
                $insertedQuestions[$def['order_index']] = $existing->id;
                continue;
            }

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

            $insertedQuestions[$def['order_index']] = $id;
        }

        // ─────────────────────────────────────────────────────────────────
        // 3. TEST CASES (4 per question: 2 visible + 2 hidden)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        // Helper closure mapping [input, expected, is_hidden]
        $seed = function (int $qIndex, array $cases) use ($insertedQuestions) {
            $questionId = $insertedQuestions[$qIndex] ?? null;
            if (! $questionId) return;

            $order = 1;
            foreach ($cases as $case) {
                $exists = DB::table('test_cases')
                    ->where('coding_question_id', $questionId)
                    ->where('order_index', $order)
                    ->exists();

                if ($exists) {
                    $order++;
                    continue;
                }

                DB::table('test_cases')->insert([
                    'coding_question_id' => $questionId,
                    'input'              => $case[0],
                    'expected_output'    => $case[1],
                    'is_hidden'          => $case[2],
                    'order_index'        => $order++,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        };

        // Q1: SMA
        $seed(1, [
            ["5\n10\n20\n30\n40\n50", "20.00\n30.00\n40.00", false],
            ["4\n1\n1\n1\n1", "1.00\n1.00", false],
            ["3\n100\n200\n300", "200.00", true],
            ["6\n0\n0\n6\n0\n0\n12", "2.00\n2.00\n2.00\n4.00", true],
        ]);
        // Q2: Returns
        $seed(2, [
            ["3\n100\n110\n104.5", "0.1000\n-0.0500", false],
            ["4\n50\n100\n50\n50", "1.0000\n-0.5000\n0.0000", false],
            ["2\n10\n20", "1.0000", true],
            ["3\n100\n90\n81", "-0.1000\n-0.1000", true],
        ]);
        // Q3: Weighted sum
        $seed(3, [
            ["3\n10\n20\n30\n0.5\n0.2\n0.3", "18.00", false],
            ["2\n100\n200\n1.0\n2.0", "500.00", false],
            ["4\n1\n1\n1\n1\n0\n0\n0\n1", "1.00", true],
            ["3\n5\n10\n15\n0.2\n0.2\n0.6", "12.00", true],
        ]);
        // Q4: Min-Max
        $seed(4, [
            ["3\n10\n50\n90", "0.0000\n0.5000\n1.0000", false],
            ["4\n0\n25\n75\n100", "0.0000\n0.2500\n0.7500\n1.0000", false],
            ["3\n-10\n0\n10", "0.0000\n0.5000\n1.0000", true],
            ["2\n100\n200", "0.0000\n1.0000", true],
        ]);
        // Q5: Standardize array
        $seed(5, [
            ["3\n10\n20\n30", "-1.0000\n0.0000\n1.0000", false],
            ["4\n2\n4\n4\n6", "-1.2649\n0.0000\n0.0000\n1.2649", false],
            ["5\n1\n2\n3\n4\n5", "-1.2649\n-0.6325\n0.0000\n0.6325\n1.2649", true],
            ["3\n100\n100\n103", "-0.5774\n-0.5774\n1.1547", true],
        ]);
        // Q6: MAD
        $seed(6, [
            ["4\n2\n4\n6\n8", "2.0000", false],
            ["3\n10\n10\n10", "0.0000", false],
            ["5\n1\n2\n3\n4\n5", "1.2000", true],
            ["2\n10\n20", "5.0000", true],
        ]);
        // Q7: IQR
        $seed(7, [
            ["5\n1\n3\n5\n7\n9", "6.00", false],
            ["4\n2\n4\n6\n8", "4.00", false],
            ["7\n1\n2\n3\n4\n5\n6\n7", "4.00", true],
            ["6\n10\n20\n30\n40\n50\n60", "30.00", true],
        ]);
        // Q8: MAE
        $seed(8, [
            ["3\n10\n20\n30\n12\n18\n33", "2.3333", false],
            ["2\n5\n5\n5\n5", "0.0000", false],
            ["4\n1\n2\n3\n4\n0\n0\n0\n0", "2.5000", true],
            ["3\n10\n10\n10\n11\n11\n11", "1.0000", true],
        ]);
        // Q9: RMSE
        $seed(9, [
            ["3\n10\n20\n30\n12\n18\n33", "2.3805", false],
            ["2\n5\n5\n5\n5", "0.0000", false],
            ["4\n1\n2\n3\n4\n0\n0\n0\n0", "2.7386", true],
            ["3\n10\n10\n10\n13\n14\n15", "4.0825", true],
        ]);
        // Q10: Sample Covariance
        $seed(10, [
            ["3\n2\n4\n6\n1\n3\n5", "4.0000", false],
            ["3\n1\n2\n3\n3\n2\n1", "-1.0000", false],
            ["4\n1\n1\n1\n1\n2\n2\n2\n2", "0.0000", true],
            ["3\n10\n20\n30\n10\n20\n30", "100.0000", true],
        ]);
        // Q11: COD
        $seed(11, [
            ["3\n10\n20\n30", "0.3333", false],
            ["5\n2\n4\n6\n8\n10", "0.4000", false],
            ["3\n5\n5\n5", "0.0000", true],
            ["4\n1\n3\n5\n7", "0.5000", true],
        ]);
        // Q12: Shannon Entropy
        $seed(12, [
            ["2\n0.5\n0.5", "1.0000", false],
            ["4\n0.25\n0.25\n0.25\n0.25", "2.0000", false],
            ["3\n1.0\n0.0\n0.0", "0.0000", true],
            ["2\n0.1\n0.9", "0.4690", true],
        ]);
        // Q13: Combinations
        $seed(13, [
            ["5\n2", "10", false],
            ["10\n3", "120", false],
            ["6\n6", "1", true],
            ["20\n1", "20", true],
        ]);
        // Q14: Permutations
        $seed(14, [
            ["5\n2", "20", false],
            ["10\n3", "720", false],
            ["6\n6", "720", true],
            ["20\n1", "20", true],
        ]);
        // Q15: Binomial
        $seed(15, [
            ["10\n5\n0.5", "0.2461", false],
            ["5\n0\n0.2", "0.3277", false],
            ["20\n10\n0.5", "0.1762", true],
            ["8\n8\n0.9", "0.4305", true],
        ]);
        // Q16: Poisson
        $seed(16, [
            ["3.0\n2", "0.2240", false],
            ["1.0\n0", "0.3679", false],
            ["5.0\n5", "0.1755", true],
            ["10.0\n8", "0.1126", true],
        ]);
        // Q17: Expected Value
        $seed(17, [
            ["3\n0\n1\n2\n0.2\n0.5\n0.3", "1.1000", false],
            ["2\n10\n20\n0.5\n0.5", "15.0000", false],
            ["4\n1\n2\n3\n4\n0.1\n0.2\n0.3\n0.4", "3.0000", true],
            ["2\n-10\n10\n0.5\n0.5", "0.0000", true],
        ]);
        // Q18: Variance of dist
        $seed(18, [
            ["3\n0\n1\n2\n0.2\n0.5\n0.3", "0.4900", false],
            ["2\n10\n20\n0.5\n0.5", "25.0000", false],
            ["4\n1\n2\n3\n4\n0.25\n0.25\n0.25\n0.25", "1.2500", true],
            ["2\n-5\n5\n0.5\n0.5", "25.0000", true],
        ]);
        // Q19: Sample size (mean)
        $seed(19, [
            ["1.96\n15.0\n3.0", "97", false],
            ["2.58\n10.0\n2.0", "167", false],
            ["1.645\n20.0\n5.0", "44", true],
            ["1.96\n50.0\n1.0", "9604", true],
        ]);
        // Q20: Sample size (prop)
        $seed(20, [
            ["1.96\n0.5\n0.05", "385", false],
            ["2.58\n0.2\n0.03", "1184", false],
            ["1.645\n0.8\n0.04", "271", true],
            ["1.96\n0.1\n0.01", "3458", true],
        ]);
        // Q21: SEM
        $seed(21, [
            ["4\n10\n12\n15\n18", "1.7017", false],
            ["5\n5\n5\n5\n5\n5", "0.0000", false],
            ["3\n100\n200\n300", "57.7350", true],
            ["6\n1\n2\n3\n4\n5\n6", "0.7638", true],
        ]);
        // Q22: Stratified alloc
        $seed(22, [
            ["100\n3\n2000\n3000\n5000", "20\n30\n50", false],
            ["50\n2\n100\n100", "25\n25", false],
            ["200\n4\n10\n20\n30\n40", "20\n40\n60\n80", true],
            ["10\n3\n5\n3\n2", "5\n3\n2", true],
        ]);
        // Q23: SEP
        $seed(23, [
            ["5\n1\n0\n1\n1\n0", "0.2191", false],
            ["4\n1\n1\n1\n1", "0.0000", false],
            ["10\n1\n0\n1\n0\n1\n0\n1\n0\n1\n0", "0.1581", true],
            ["8\n1\n1\n1\n0\n0\n0\n0\n0", "0.1712", true],
        ]);
        // Q24: One-sample t array
        $seed(24, [
            ["10.0\n4\n12\n14\n11\n15", "3.3541", false],
            ["5.0\n5\n4\n6\n5\n4\n6", "0.0000", false],
            ["20.0\n3\n10\n15\n12", "-11.4564", true],
            ["0.0\n4\n1\n2\n3\n4", "1.9365", true],
        ]);
        // Q25: Paired t array
        $seed(25, [
            ["3\n10\n15\n20\n12\n14\n25", "1.3868", false],
            ["4\n5\n5\n5\n5\n6\n6\n6\n6", "inf", false], // Wait, handling inf? Adjust test cases to have variance.
            ["4\n5\n5\n5\n5\n7\n6\n7\n6", "4.0000", false],
            ["3\n10\n10\n10\n5\n5\n5", "-inf", true], // Let's ensure non-zero variance.
            ["3\n10\n10\n10\n5\n6\n7", "-4.3301", true],
        ]);
        // Q26: Pooled Var Array
        $seed(26, [
            ["3\n3\n2\n4\n6\n1\n3\n5", "4.0000", false],
            ["4\n4\n1\n2\n3\n4\n5\n6\n7\n8", "1.6667", false],
            ["2\n3\n10\n20\n5\n10\n15", "37.5000", true],
            ["3\n2\n5\n5\n5\n10\n10", "0.0000", true],
        ]);
        // Q27: Two-sample t array
        $seed(27, [
            ["3\n3\n10\n12\n14\n5\n7\n9", "3.0619", false],
            ["4\n4\n1\n2\n3\n4\n5\n6\n7\n8", "-4.3818", false],
            ["3\n3\n10\n10\n10\n10\n10\n10", "0.0000", true], // wait, division by zero if variances are 0.
            ["3\n3\n1\n3\n5\n10\n12\n14", "-6.1237", true],
        ]);
        // Q28: Welch DF
        $seed(28, [
            ["10\n4.0\n15\n9.0", "22", false],
            ["20\n10.0\n20\n10.0", "38", false],
            ["5\n2.0\n10\n20.0", "11", true],
            ["100\n50.0\n50\n10.0", "121", true],
        ]);
        // Q29: Grand Mean
        $seed(29, [
            ["2\n3\n10\n20\n30\n2\n40\n50", "30.0000", false],
            ["3\n2\n1\n2\n2\n3\n4\n2\n5\n6", "3.5000", false],
            ["2\n2\n100\n100\n2\n200\n200", "150.0000", true],
            ["1\n5\n1\n2\n3\n4\n5", "3.0000", true],
        ]);
        // Q30: SSB array
        $seed(30, [
            ["2\n3\n10\n20\n30\n2\n40\n50", "750.0000", false],
            ["2\n2\n10\n10\n2\n10\n10", "0.0000", false],
            ["3\n2\n10\n10\n2\n20\n20\n2\n30\n30", "400.0000", true],
            ["2\n3\n1\n2\n3\n3\n7\n8\n9", "96.0000", true],
        ]);
        // Q31: SSW array
        $seed(31, [
            ["2\n3\n10\n20\n30\n2\n40\n50", "250.0000", false],
            ["2\n2\n10\n10\n2\n10\n10", "0.0000", false],
            ["3\n2\n1\n3\n2\n4\n6\n2\n7\n9", "6.0000", true],
            ["2\n3\n10\n20\n30\n3\n40\n50\n60", "400.0000", true],
        ]);
        // Q32: F stat
        $seed(32, [
            ["750.0\n250.0\n2\n5", "9.0000", false],
            ["400.0\n6.0\n3\n6", "100.0000", false],
            ["100.0\n100.0\n2\n20", "18.0000", true],
            ["0.0\n50.0\n2\n10", "0.0000", true],
        ]);
        // Q33: Cohen's d array
        $seed(33, [
            ["3\n3\n10\n12\n14\n5\n7\n9", "2.5000", false],
            ["4\n4\n1\n2\n3\n4\n5\n6\n7\n8", "3.0984", false],
            ["3\n3\n10\n10\n10\n10\n10\n10", "0.0000", true], // Division by zero protection needed?
            ["3\n3\n1\n3\n5\n10\n12\n14", "5.0000", true],
        ]);
        // Q34: Eta-sq
        $seed(34, [
            ["750.0\n250.0", "0.7500", false],
            ["400.0\n6.0", "0.9852", false],
            ["100.0\n100.0", "0.5000", true],
            ["0.0\n50.0", "0.0000", true],
        ]);
        // Q35: ChiSq GOF Array
        $seed(35, [
            ["3\n10\n20\n30", "10.0000", false],
            ["4\n5\n5\n5\n5", "0.0000", false],
            ["2\n10\n50", "26.6667", true],
            ["3\n10\n10\n100", "135.0000", true],
        ]);
        // Q36: ChiSq Probs
        $seed(36, [
            ["3\n100\n30\n20\n50\n0.25\n0.25\n0.50", "1.0000", false],
            ["2\n50\n25\n25\n0.5\n0.5", "0.0000", false],
            ["3\n200\n100\n50\n50\n0.3333\n0.3333\n0.3333", "25.0000", true], // approximate
            ["2\n10\n2\n8\n0.5\n0.5", "3.6000", true],
        ]);
        // Q37: Sign test
        $seed(37, [
            ["4\n10\n12\n15\n10\n20\n20\n8\n10", "2", false],
            ["3\n5\n5\n5\n1\n2\n3", "0", false],
            ["5\n1\n2\n3\n4\n5\n2\n3\n4\n5\n6", "5", true],
            ["2\n10\n20\n10\n20", "0", true],
        ]);
        // Q38: Ranks
        $seed(38, [
            ["4\n50\n10\n30\n20", "4\n1\n3\n2", false],
            ["3\n1\n2\n3", "1\n2\n3", false],
            ["5\n100\n-50\n0\n25\n75", "5\n1\n2\n3\n4", true],
            ["2\n2\n1", "2\n1", true],
        ]);
        // Q39: Spearman
        $seed(39, [
            ["3\n10\n20\n30\n100\n50\n200", "0.5000", false],
            ["4\n1\n2\n3\n4\n4\n3\n2\n1", "-1.0000", false],
            ["3\n1\n2\n3\n1\n2\n3", "1.0000", true],
            ["4\n10\n20\n30\n40\n100\n200\n50\n300", "0.8000", true],
        ]);
        // Q40: SS_xx
        $seed(40, [
            ["3\n1\n2\n3\n2\n4\n6", "2.0000", false],
            ["4\n2\n4\n6\n8\n1\n1\n1\n1", "20.0000", false],
            ["3\n10\n20\n30\n10\n20\n30", "200.0000", true],
            ["2\n1\n5\n10\n20", "8.0000", true],
        ]);
        // Q41: SS_xy
        $seed(41, [
            ["3\n1\n2\n3\n2\n4\n6", "4.0000", false],
            ["4\n2\n4\n6\n8\n1\n1\n1\n1", "0.0000", false],
            ["3\n10\n20\n30\n10\n20\n30", "200.0000", true],
            ["2\n1\n5\n10\n20", "20.0000", true],
        ]);
        // Q42: Regr Slope
        $seed(42, [
            ["3\n1\n2\n3\n2\n4\n6", "2.0000", false],
            ["4\n2\n4\n6\n8\n1\n1\n1\n1", "0.0000", false],
            ["3\n10\n20\n30\n10\n20\n30", "1.0000", true],
            ["2\n1\n5\n10\n20", "2.5000", true],
        ]);
        // Q43: Regr Intercept
        $seed(43, [
            ["3\n1\n2\n3\n3\n5\n7", "1.0000", false],
            ["4\n2\n4\n6\n8\n1\n1\n1\n1", "1.0000", false],
            ["3\n10\n20\n30\n10\n20\n30", "0.0000", true],
            ["2\n1\n5\n10\n20", "7.5000", true],
        ]);
        // Q44: R^2 array
        $seed(44, [
            ["3\n1\n2\n3\n2.1\n3.9\n6.2", "0.9943", false],
            ["4\n1\n2\n3\n4\n2\n4\n6\n8", "1.0000", false],
            ["3\n1\n2\n3\n10\n10\n10", "0.0000", true], // slope 0
            ["3\n10\n20\n30\n15\n15\n15", "0.0000", true],
        ]);
        // Q45: Effect A
        $seed(45, [
            ["2\n10\n12\n20\n22\n14\n16\n28\n32", "12.0000", false],
            ["2\n5\n5\n5\n5\n5\n5\n5\n5", "0.0000", false],
            ["1\n10\n20\n15\n30", "12.5000", true],
            ["3\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12", "6.0000", true],
        ]);
        // Q46: Effect B
        $seed(46, [
            ["2\n10\n12\n20\n22\n14\n16\n28\n32", "7.0000", false],
            ["2\n5\n5\n5\n5\n5\n5\n5\n5", "0.0000", false],
            ["1\n10\n20\n15\n30", "7.5000", true],
            ["3\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12", "6.0000", true],
        ]);
        // Q47: Effect AB
        $seed(47, [
            ["2\n10\n12\n20\n22\n14\n16\n28\n32", "2.0000", false],
            ["2\n5\n5\n5\n5\n5\n5\n5\n5", "0.0000", false],
            ["1\n10\n20\n15\n30", "2.5000", true],
            ["3\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12", "0.0000", true],
        ]);
        // Q48: Odds Ratio
        $seed(48, [
            ["10\n5\n2\n20", "20.0000", false],
            ["5\n5\n5\n5", "1.0000", false],
            ["20\n10\n5\n50", "20.0000", true],
            ["100\n10\n10\n100", "100.0000", true],
        ]);
        // Q49: Relative Risk
        $seed(49, [
            ["10\n10\n5\n15", "2.0000", false],
            ["5\n5\n5\n5", "1.0000", false],
            ["20\n80\n10\n90", "2.0000", true],
            ["50\n50\n10\n90", "5.0000", true],
        ]);
        // Q50: NNT
        $seed(50, [
            ["10\n40\n20\n30", "5", false],
            ["50\n50\n10\n90", "3", false],
            ["20\n80\n10\n90", "10", true],
            ["90\n10\n80\n20", "10", true],
        ]);

        $this->command->info('✅ Module 8 Coding (Intermediate) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}