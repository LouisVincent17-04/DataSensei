<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 8 — Statistics & Probability (UniversityStudent) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the UniversityStudent tier
 *   2. coding_questions    — 50 questions covering intermediate statistics
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module8CodingChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (! $category) {
            $this->command->error('UniversityStudent category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 8 — Statistics & Probability (UniversityStudent) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Statistics & Probability',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply intermediate university-level statistics in Python — compute weighted means, conditional probabilities, pooled variances, Z-scores, and hypothesis testing components. Tasks involve multi-variable formulas and intermediate distributions.',
                'time_limit_seconds' => 720,
                'base_xp'            => 600,
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
Read 6 values (value1, weight1, value2, weight2, value3, weight3) on separate lines.
Compute the **weighted mean**: `(v1×w1 + v2×w2 + v3×w3) / (w1 + w2 + w3)`.

Print the result rounded to **2 decimal places**.

Example:
Input:
10
2
20
3
30
5
Output: 23.00

MD,
                'starter_code'        => "v1=float(input())\nw1=float(input())\nv2=float(input())\nw2=float(input())\nv3=float(input())\nw3=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read 2 positive numbers. Compute their **geometric mean**: `(a × b)^0.5`.

Print the result rounded to **2 decimal places**.

Example:
Input:
4
9
Output: 6.00

MD,
                'starter_code'        => "a=float(input())\nb=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read 2 positive numbers. Compute their **harmonic mean**: `2 / (1/a + 1/b)`.

Print the result rounded to **2 decimal places**.

Example:
Input:
2
6
Output: 3.00

MD,
                'starter_code'        => "a=float(input())\nb=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read 3 numbers. Compute the **Mean Absolute Deviation (MAD)**.
1. Find the mean `m`.
2. Compute `(|a-m| + |b-m| + |c-m|) / 3`.

Print the result rounded to **2 decimal places**.

Example:
Input:
10
20
30
Output: 6.67

MD,
                'starter_code'        => "a=float(input())\nb=float(input())\nc=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read a value `x`, a population mean `mu`, and a standard deviation `std`.
Compute the **Z-score**: `(x - mu) / std`.

Print the result rounded to **2 decimal places**.

Example:
Input:
15
10
2
Output: 2.50

MD,
                'starter_code'        => "x=float(input())\nmu=float(input())\nstd=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.2: Descriptive Statistics & Data Summarization (Q6–Q12)
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Read 3 sample values. Compute the **sample variance** (divide by n-1, which is 2).

Print the result rounded to **2 decimal places**.

Example:
Input:
2
4
6
Output: 4.00

MD,
                'starter_code'        => "a=float(input())\nb=float(input())\nc=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Read 3 sample values. Compute the **sample standard deviation** (square root of sample variance).

Print the result rounded to **2 decimal places**.

Example:
Input:
2
4
6
Output: 2.00

MD,
                'starter_code'        => "a=float(input())\nb=float(input())\nc=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Read a `mean` and a `std`. Compute the **Coefficient of Variation (CV)** as a percentage: `(std / mean) * 100`.

Print the result rounded to **2 decimal places**.

Example:
Input:
20
5
Output: 25.00

MD,
                'starter_code'        => "mean=float(input())\nstd=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read 3 numbers. Compute the **mid-range**: `(maximum + minimum) / 2`.

Print the result rounded to **2 decimal places**.

Example:
Input:
1
5
9
Output: 5.00

MD,
                'starter_code'        => "a=float(input())\nb=float(input())\nc=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read `mean`, `median`, and `std`. Compute **Pearson's skewness**: `3 * (mean - median) / std`.

Print the result rounded to **2 decimal places**.

Example:
Input:
10
8
4
Output: 1.50

MD,
                'starter_code'        => "mean=float(input())\nmedian=float(input())\nstd=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Read 5 numbers. Sort them, remove the single smallest and single largest value, and compute the **mean** of the remaining 3 values.

Print the result rounded to **2 decimal places**.

Example:
Input:
10
2
8
4
6
Output: 6.00

MD,
                'starter_code'        => "nums = [float(input()) for _ in range(5)]\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Read 3 numbers. Compute the **Sum of Squared Deviations (SSD)** from their mean.

Print the result rounded to **2 decimal places**.

Example:
Input:
2
4
6
Output: 8.00

MD,
                'starter_code'        => "a=float(input())\nb=float(input())\nc=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.3: Probability Distributions in Practice (Q13–Q18)
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Read the joint probability `P(A and B)` and marginal probability `P(B)`. Compute the **conditional probability** `P(A|B)`.

Print the result rounded to **4 decimal places**.

Example:
Input:
0.2
0.5
Output: 0.4000

MD,
                'starter_code'        => "p_a_and_b=float(input())\np_b=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Use **Bayes' Theorem**. Read `P(B|A)`, `P(A)`, and `P(B)`. Compute `P(A|B) = P(B|A) × P(A) / P(B)`.

Print the result rounded to **4 decimal places**.

Example:
Input:
0.8
0.01
0.1
Output: 0.0800

MD,
                'starter_code'        => "p_b_given_a=float(input())\np_a=float(input())\np_b=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
For a continuous uniform distribution on the interval `[a, b]`, read `a` and `b`. Compute the **expected value**: `(a + b) / 2`.

Print the result rounded to **2 decimal places**.

Example:
Input:
2
10
Output: 6.00

MD,
                'starter_code'        => "a=float(input())\nb=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
For a continuous uniform distribution on the interval `[a, b]`, read `a` and `b`. Compute the **variance**: `(b - a)^2 / 12`.

Print the result rounded to **2 decimal places**.

Example:
Input:
2
8
Output: 3.00

MD,
                'starter_code'        => "a=float(input())\nb=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
For an exponential distribution, read the rate parameter `lambda`. Compute the **mean**: `1 / lambda`.

Print the result rounded to **2 decimal places**.

Example:
Input:
0.5
Output: 2.00

MD,
                'starter_code'        => "lam=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
For an exponential distribution, read the rate parameter `lambda`. Compute the **variance**: `1 / (lambda^2)`.

Print the result rounded to **2 decimal places**.

Example:
Input:
0.5
Output: 4.00

MD,
                'starter_code'        => "lam=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.4: Sampling Theory & Survey Design (Q19–Q23)
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Read population size `N` and sample size `n`. Compute the **Finite Population Correction (FPC)** factor: `sqrt( (N - n) / (N - 1) )`.

Print the result rounded to **4 decimal places**.

Example:
Input:
100
10
Output: 0.9535

MD,
                'starter_code'        => "import math\nN=int(input())\nn=int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Read Z-score `z`, sample proportion `p`, and sample size `n`. Compute the **Margin of Error** for a proportion: `z × sqrt( p × (1 - p) / n )`.

Print the result rounded to **4 decimal places**.

Example:
Input:
1.96
0.5
100
Output: 0.0980

MD,
                'starter_code'        => "import math\nz=float(input())\np=float(input())\nn=int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Read total sample size `n`, stratum population `N_h`, and total population `N`. Compute the **proportional stratified allocation**: `round(n × (N_h / N))`.

Print the result as an **integer**.

Example:
Input:
50
200
1000
Output: 10

MD,
                'starter_code'        => "n=int(input())\nN_h=int(input())\nN=int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Read sample standard deviation `s` and sample size `n`. Compute the **Standard Error of the Mean (SEM)**: `s / sqrt(n)`.

Print the result rounded to **4 decimal places**.

Example:
Input:
15.0
25
Output: 3.0000

MD,
                'starter_code'        => "import math\ns=float(input())\nn=int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Read critical Z-value `z`, sample std dev `s`, and size `n`. Compute the **total width of the confidence interval**: `2 × z × (s / sqrt(n))`.

Print the result rounded to **4 decimal places**.

Example:
Input:
1.96
10
25
Output: 7.8400

MD,
                'starter_code'        => "import math\nz=float(input())\ns=float(input())\nn=int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.5: Hypothesis Testing: Framework & One-Sample Tests (Q24–Q28)
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Read a sample proportion `p_hat`, hypothesized proportion `p0`, and sample size `n`. Compute the **one-sample Z-test statistic for a proportion**:
`z = (p_hat - p0) / sqrt( p0 × (1 - p0) / n )`

Print the result rounded to **4 decimal places**.

Example:
Input:
0.6
0.5
100
Output: 2.0000

MD,
                'starter_code'        => "import math\np_hat=float(input())\np0=float(input())\nn=int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Read 3 pairs of observations (before, after). Compute the **mean of the paired differences**: average of `(after - before)`.

Input format: b1, a1, b2, a2, b3, a3 (each on a new line).
Print the result rounded to **2 decimal places**.

Example:
Input:
10
12
15
14
20
25
Output: 2.00

MD,
                'starter_code'        => "b1=float(input())\na1=float(input())\nb2=float(input())\na2=float(input())\nb3=float(input())\na3=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Read group 1 size `n1` and std dev `s1`, then group 2 size `n2` and std dev `s2`. Compute the **pooled variance**:
`((n1-1)×s1^2 + (n2-1)×s2^2) / (n1 + n2 - 2)`.

Print the result rounded to **4 decimal places**.

Example:
Input:
10
4
15
5
Output: 21.4783

MD,
                'starter_code'        => "n1=int(input())\ns1=float(input())\nn2=int(input())\ns2=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Read a pooled variance `var_pooled`. Compute the **pooled standard deviation** (square root of the pooled variance).

Print the result rounded to **4 decimal places**.

Example:
Input:
21.4783
Output: 4.6345

MD,
                'starter_code'        => "import math\nvar_pooled=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Read pooled standard deviation `s_pool`, and sizes `n1`, `n2`. Compute the **Standard Error of the Difference** for a two-sample t-test:
`SE = s_pool × sqrt(1/n1 + 1/n2)`.

Print the result rounded to **4 decimal places**.

Example:
Input:
4.6345
10
15
Output: 1.8920

MD,
                'starter_code'        => "import math\ns_pool=float(input())\nn1=int(input())\nn2=int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.6: Two-Sample Tests & ANOVA (Q29–Q34)
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Read a mean difference `diff` and a standard error `SE`. Compute the **Two-sample t-test statistic**: `diff / SE`.

Print the result rounded to **4 decimal places**.

Example:
Input:
5.0
1.8920
Output: 2.6427

MD,
                'starter_code'        => "diff=float(input())\nse=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Read `n1`, `x1` (group 1 mean), `n2`, `x2` (group 2 mean), and `grand_mean`. Compute the **Between-Group Sum of Squares (SSB)**:
`n1×(x1 - grand_mean)^2 + n2×(x2 - grand_mean)^2`.

Print the result rounded to **2 decimal places**.

Example:
Input:
10
5
10
15
10
Output: 500.00

MD,
                'starter_code'        => "n1=int(input())\nx1=float(input())\nn2=int(input())\nx2=float(input())\ngrand=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Read `n1`, `s1`, `n2`, and `s2`. Compute the **Within-Group Sum of Squares (SSW)**:
`(n1 - 1)×s1^2 + (n2 - 1)×s2^2`.

Print the result rounded to **2 decimal places**.

Example:
Input:
10
4
10
5
Output: 369.00

MD,
                'starter_code'        => "n1=int(input())\ns1=float(input())\nn2=int(input())\ns2=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Read `SSB` and `SSW`. Compute the **Total Sum of Squares (SST)**: `SSB + SSW`.

Print the result rounded to **2 decimal places**.

Example:
Input:
500.0
369.0
Output: 869.00

MD,
                'starter_code'        => "ssb=float(input())\nssw=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Read `SSB` and `k` (number of groups). Compute the **Mean Square Between (MSB)**: `SSB / (k - 1)`.

Print the result rounded to **2 decimal places**.

Example:
Input:
500.0
2
Output: 500.00

MD,
                'starter_code'        => "ssb=float(input())\nk=int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Read `SSW`, total sample size `N`, and groups `k`. Compute the **Mean Square Within (MSW)**: `SSW / (N - k)`.

Print the result rounded to **2 decimal places**.

Example:
Input:
369.0
20
2
Output: 20.50

MD,
                'starter_code'        => "ssw=float(input())\nN=int(input())\nk=int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.7: Chi-Square Tests & Non-Parametric Methods (Q35–Q39)
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
For a contingency table, read `row_total`, `col_total`, and `grand_total`. Compute the **Expected Cell Count**: `(row_total × col_total) / grand_total`.

Print the result rounded to **2 decimal places**.

Example:
Input:
50
40
200
Output: 10.00

MD,
                'starter_code'        => "r=float(input())\nc=float(input())\nt=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Read an Observed frequency `O` and Expected frequency `E`. Compute the **Chi-Square component** for this cell: `(O - E)^2 / E`.

Print the result rounded to **4 decimal places**.

Example:
Input:
15
10
Output: 2.5000

MD,
                'starter_code'        => "o=float(input())\ne=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Read `O` and `E`. Compute the **Yates' continuity-corrected Chi-Square component**: `(|O - E| - 0.5)^2 / E`.

Print the result rounded to **4 decimal places**.

Example:
Input:
15
10
Output: 2.0250

MD,
                'starter_code'        => "o=float(input())\ne=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Read two values `x` and `y`. For a Wilcoxon signed-rank test, print the **absolute difference**: `|x - y|`.

Print the result rounded to **2 decimal places**.

Example:
Input:
4.5
7.0
Output: 2.50

MD,
                'starter_code'        => "x=float(input())\ny=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Read the sum of ranks for group 1 `R1` and the size of group 1 `n1`. Compute the **Mann-Whitney U1 statistic**:
`U1 = R1 - n1 × (n1 + 1) / 2`.

Print the result as an **integer**.

Example:
Input:
12
3
Output: 6

MD,
                'starter_code'        => "R1=int(input())\nn1=int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.8: Correlation, Regression & Model Diagnostics (Q40–Q44)
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Read 2 points: `x1`, `y1`, `x2`, `y2`. Compute the **sample covariance**.
1. `xm = (x1 + x2)/2` and `ym = (y1 + y2)/2`
2. `Cov = ((x1 - xm)(y1 - ym) + (x2 - xm)(y2 - ym)) / 1`

Print the result rounded to **4 decimal places**.

Example:
Input:
2
4
4
8
Output: 4.0000

MD,
                'starter_code'        => "x1=float(input())\ny1=float(input())\nx2=float(input())\ny2=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Read `cov` (covariance), `sx` (std dev of x), and `sy` (std dev of y). Compute the **Pearson correlation r**: `cov / (sx × sy)`.

Print the result rounded to **4 decimal places**.

Example:
Input:
4.0
1.4142
2.8284
Output: 1.0000

MD,
                'starter_code'        => "cov=float(input())\nsx=float(input())\nsy=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Read `r`, `sy`, and `sx`. Compute the **Linear Regression Slope (b1)**: `r × (sy / sx)`.

Print the result rounded to **4 decimal places**.

Example:
Input:
0.8
5.0
2.0
Output: 2.0000

MD,
                'starter_code'        => "r=float(input())\nsy=float(input())\nsx=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Read `mean_y`, `b1`, and `mean_x`. Compute the **Linear Regression Intercept (b0)**: `mean_y - b1 × mean_x`.

Print the result rounded to **4 decimal places**.

Example:
Input:
10.0
2.0
3.0
Output: 4.0000

MD,
                'starter_code'        => "my=float(input())\nb1=float(input())\nmx=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Read an actual `y` value, `b0`, `b1`, and the `x` value. Compute the **Residual**: `y - (b0 + b1 × x)`.

Print the result rounded to **4 decimal places**.

Example:
Input:
12.0
4.0
2.0
3.0
Output: 2.0000

MD,
                'starter_code'        => "y=float(input())\nb0=float(input())\nb1=float(input())\nx=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.9: Experimental Design: Principles & Layouts (Q45–Q47)
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Read a Pearson correlation coefficient `r`. Compute the **Coefficient of Determination (R²)**: `r^2`.

Print the result rounded to **4 decimal places**.

Example:
Input:
0.8
Output: 0.6400

MD,
                'starter_code'        => "r=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
For a 2² Factorial Design, read responses for conditions: `(1)`, `a`, `b`, and `ab`. Compute the **Main Effect A**:
`(a + ab - (1) - b) / 2`.

Print the result rounded to **2 decimal places**.

Example:
Input:
10
20
15
30
Output: 12.50

MD,
                'starter_code'        => "c_1=float(input())\na=float(input())\nb=float(input())\nab=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
For a 2² Factorial Design, read responses for conditions: `(1)`, `a`, `b`, and `ab`. Compute the **Main Effect B**:
`(b + ab - (1) - a) / 2`.

Print the result rounded to **2 decimal places**.

Example:
Input:
10
20
15
30
Output: 7.50

MD,
                'starter_code'        => "c_1=float(input())\na=float(input())\nb=float(input())\nab=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.10: Effect Size, Power Analysis & Sample Size Planning (Q48–Q50)
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Read `SSB` (Between-group Sum of Squares) and `SST` (Total Sum of Squares). Compute **Eta-squared (η²)**: `SSB / SST`.

Print the result rounded to **4 decimal places**.

Example:
Input:
40.0
100.0
Output: 0.4000

MD,
                'starter_code'        => "ssb=float(input())\nsst=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Read a 2x2 contingency table counts: `a`, `b`, `c`, `d`. Compute the **Odds Ratio**: `(a × d) / (b × c)`.

Print the result rounded to **4 decimal places**.

Example:
Input:
10
5
2
20
Output: 20.0000

MD,
                'starter_code'        => "a=float(input())\nb=float(input())\nc=float(input())\nd=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Read a 2x2 contingency table counts: `a`, `b`, `c`, `d`. Compute the **Relative Risk**: `(a / (a + b)) / (c / (c + d))`.

Print the result rounded to **4 decimal places**.

Example:
Input:
10
10
5
15
Output: 2.0000

MD,
                'starter_code'        => "a=float(input())\nb=float(input())\nc=float(input())\nd=float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 720,
                'base_xp'             => 150,
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

        // Q1
        $seed(1, [
            ["10\n2\n20\n3\n30\n5", "23.00", false],
            ["5\n1\n5\n1\n5\n1", "5.00", false],
            ["100\n1\n0\n1\n50\n2", "50.00", true],
            ["1\n10\n2\n20\n3\n30", "2.33", true],
        ]);
        // Q2
        $seed(2, [
            ["4\n9", "6.00", false],
            ["2\n8", "4.00", false],
            ["1\n100", "10.00", true],
            ["5\n20", "10.00", true],
        ]);
        // Q3
        $seed(3, [
            ["2\n6", "3.00", false],
            ["4\n4", "4.00", false],
            ["10\n40", "16.00", true],
            ["3\n6", "4.00", true],
        ]);
        // Q4
        $seed(4, [
            ["10\n20\n30", "6.67", false],
            ["5\n5\n5", "0.00", false],
            ["1\n10\n100", "40.67", true],
            ["-5\n0\n5", "3.33", true],
        ]);
        // Q5
        $seed(5, [
            ["15\n10\n2", "2.50", false],
            ["8\n10\n4", "-0.50", false],
            ["100\n50\n10", "5.00", true],
            ["0\n0\n1", "0.00", true],
        ]);
        // Q6
        $seed(6, [
            ["2\n4\n6", "4.00", false],
            ["1\n1\n1", "0.00", false],
            ["10\n20\n30", "100.00", true],
            ["-1\n0\n1", "1.00", true],
        ]);
        // Q7
        $seed(7, [
            ["2\n4\n6", "2.00", false],
            ["1\n1\n1", "0.00", false],
            ["10\n20\n30", "10.00", true],
            ["-1\n0\n1", "1.00", true],
        ]);
        // Q8
        $seed(8, [
            ["20\n5", "25.00", false],
            ["10\n1", "10.00", false],
            ["50\n10", "20.00", true],
            ["100\n0", "0.00", true],
        ]);
        // Q9
        $seed(9, [
            ["1\n5\n9", "5.00", false],
            ["10\n20\n30", "20.00", false],
            ["-10\n0\n10", "0.00", true],
            ["100\n100\n100", "100.00", true],
        ]);
        // Q10
        $seed(10, [
            ["10\n8\n4", "1.50", false],
            ["5\n5\n2", "0.00", false],
            ["2\n6\n2", "-6.00", true],
            ["100\n90\n10", "3.00", true],
        ]);
        // Q11
        $seed(11, [
            ["10\n2\n8\n4\n6", "6.00", false],
            ["1\n2\n3\n4\n5", "3.00", false],
            ["100\n0\n50\n25\n75", "50.00", true],
            ["9\n9\n9\n9\n9", "9.00", true],
        ]);
        // Q12
        $seed(12, [
            ["2\n4\n6", "8.00", false],
            ["1\n2\n3", "2.00", false],
            ["10\n20\n30", "200.00", true],
            ["0\n0\n0", "0.00", true],
        ]);
        // Q13
        $seed(13, [
            ["0.2\n0.5", "0.4000", false],
            ["0.1\n0.2", "0.5000", false],
            ["0.3\n0.3", "1.0000", true],
            ["0.05\n0.25", "0.2000", true],
        ]);
        // Q14
        $seed(14, [
            ["0.8\n0.01\n0.1", "0.0800", false],
            ["0.5\n0.5\n0.5", "0.5000", false],
            ["0.9\n0.1\n0.3", "0.3000", true],
            ["1.0\n0.2\n0.4", "0.5000", true],
        ]);
        // Q15
        $seed(15, [
            ["2\n10", "6.00", false],
            ["0\n1", "0.50", false],
            ["-5\n5", "0.00", true],
            ["10\n20", "15.00", true],
        ]);
        // Q16
        $seed(16, [
            ["2\n8", "3.00", false],
            ["0\n12", "12.00", false],
            ["-5\n5", "8.33", true],
            ["10\n20", "8.33", true],
        ]);
        // Q17
        $seed(17, [
            ["0.5", "2.00", false],
            ["0.2", "5.00", false],
            ["1.0", "1.00", true],
            ["0.1", "10.00", true],
        ]);
        // Q18
        $seed(18, [
            ["0.5", "4.00", false],
            ["0.2", "25.00", false],
            ["1.0", "1.00", true],
            ["0.1", "100.00", true],
        ]);
        // Q19
        $seed(19, [
            ["100\n10", "0.9535", false],
            ["50\n5", "0.9583", false],
            ["1000\n100", "0.9492", true],
            ["10\n2", "0.9428", true],
        ]);
        // Q20
        $seed(20, [
            ["1.96\n0.5\n100", "0.0980", false],
            ["2.58\n0.2\n50", "0.1459", false],
            ["1.645\n0.8\n200", "0.0465", true],
            ["1.96\n0.1\n1000", "0.0186", true],
        ]);
        // Q21
        $seed(21, [
            ["50\n200\n1000", "10", false],
            ["100\n50\n500", "10", false],
            ["300\n100\n1200", "25", true],
            ["10\n50\n100", "5", true],
        ]);
        // Q22
        $seed(22, [
            ["15.0\n25", "3.0000", false],
            ["10.0\n100", "1.0000", false],
            ["5.0\n4", "2.5000", true],
            ["20.0\n400", "1.0000", true],
        ]);
        // Q23
        $seed(23, [
            ["1.96\n10\n25", "7.8400", false],
            ["2.58\n5\n100", "2.5800", false],
            ["1.645\n20\n400", "3.2900", true],
            ["1.96\n15\n9", "19.6000", true],
        ]);
        // Q24
        $seed(24, [
            ["0.6\n0.5\n100", "2.0000", false],
            ["0.4\n0.5\n100", "-2.0000", false],
            ["0.55\n0.5\n400", "2.0000", true],
            ["0.2\n0.25\n300", "-2.0000", true],
        ]);
        // Q25
        $seed(25, [
            ["10\n12\n15\n14\n20\n25", "2.00", false],
            ["5\n5\n10\n10\n15\n15", "0.00", false],
            ["0\n-5\n10\n5\n20\n15", "-5.00", true],
            ["1\n10\n2\n20\n3\n30", "18.00", true],
        ]);
        // Q26
        $seed(26, [
            ["10\n4\n15\n5", "21.4783", false],
            ["5\n2\n5\n2", "4.0000", false],
            ["20\n10\n30\n15", "175.5208", true],
            ["10\n1\n10\n1", "1.0000", true],
        ]);
        // Q27
        $seed(27, [
            ["21.4783", "4.6345", false],
            ["4.0", "2.0000", false],
            ["16.0", "4.0000", true],
            ["100.0", "10.0000", true],
        ]);
        // Q28
        $seed(28, [
            ["4.6345\n10\n15", "1.8920", false],
            ["2.0\n5\n5", "1.2649", false],
            ["10.0\n20\n20", "3.1623", true],
            ["5.0\n50\n50", "1.0000", true],
        ]);
        // Q29
        $seed(29, [
            ["5.0\n1.892", "2.6427", false],
            ["-2.0\n1.2649", "-1.5812", false],
            ["10.0\n3.1623", "3.1623", true],
            ["0.0\n1.0", "0.0000", true],
        ]);
        // Q30
        $seed(30, [
            ["10\n5\n10\n15\n10", "500.00", false],
            ["5\n2\n5\n6\n4", "40.00", false],
            ["20\n10\n30\n20\n16", "1200.00", true],
            ["10\n0\n10\n0\n0", "0.00", true],
        ]);
        // Q31
        $seed(31, [
            ["10\n4\n10\n5", "369.00", false],
            ["5\n2\n5\n2", "32.00", false],
            ["20\n10\n30\n15", "8425.00", true],
            ["10\n1\n10\n1", "18.00", true],
        ]);
        // Q32
        $seed(32, [
            ["500.0\n369.0", "869.00", false],
            ["40.0\n32.0", "72.00", false],
            ["1200.0\n8425.0", "9625.00", true],
            ["0.0\n18.0", "18.00", true],
        ]);
        // Q33
        $seed(33, [
            ["500.0\n2", "500.00", false],
            ["100.0\n3", "50.00", false],
            ["450.0\n4", "150.00", true],
            ["0.0\n2", "0.00", true],
        ]);
        // Q34
        $seed(34, [
            ["369.0\n20\n2", "20.50", false],
            ["32.0\n10\n2", "4.00", false],
            ["100.0\n30\n5", "4.00", true],
            ["0.0\n20\n2", "0.00", true],
        ]);
        // Q35
        $seed(35, [
            ["50\n40\n200", "10.00", false],
            ["10\n10\n100", "1.00", false],
            ["100\n50\n500", "10.00", true],
            ["25\n25\n50", "12.50", true],
        ]);
        // Q36
        $seed(36, [
            ["15\n10", "2.5000", false],
            ["5\n5", "0.0000", false],
            ["20\n25", "1.0000", true],
            ["0\n10", "10.0000", true],
        ]);
        // Q37
        $seed(37, [
            ["15\n10", "2.0250", false],
            ["5\n5", "0.0250", false],
            ["20\n25", "0.8100", true],
            ["0\n10", "9.0250", true],
        ]);
        // Q38
        $seed(38, [
            ["4.5\n7.0", "2.50", false],
            ["10.0\n10.0", "0.00", false],
            ["-5.0\n5.0", "10.00", true],
            ["100.0\n50.0", "50.00", true],
        ]);
        // Q39
        $seed(39, [
            ["12\n3", "6", false],
            ["15\n5", "0", false],
            ["20\n4", "10", true],
            ["5\n2", "2", true],
        ]);
        // Q40
        $seed(40, [
            ["2\n4\n4\n8", "4.0000", false],
            ["1\n2\n3\n6", "4.0000", false],
            ["10\n10\n20\n0", "-50.0000", true],
            ["5\n5\n5\n5", "0.0000", true],
        ]);
        // Q41
        $seed(41, [
            ["4.0\n1.4142\n2.8284", "1.0000", false],
            ["-50.0\n7.071\n7.071", "-1.0000", false],
            ["0.0\n5.0\n5.0", "0.0000", true],
            ["2.0\n2.0\n2.0", "0.5000", true],
        ]);
        // Q42
        $seed(42, [
            ["0.8\n5.0\n2.0", "2.0000", false],
            ["-0.5\n10.0\n2.0", "-2.5000", false],
            ["1.0\n4.0\n4.0", "1.0000", true],
            ["0.0\n10.0\n1.0", "0.0000", true],
        ]);
        // Q43
        $seed(43, [
            ["10.0\n2.0\n3.0", "4.0000", false],
            ["5.0\n-2.5\n2.0", "10.0000", false],
            ["20.0\n1.0\n10.0", "10.0000", true],
            ["0.0\n0.0\n5.0", "0.0000", true],
        ]);
        // Q44
        $seed(44, [
            ["12.0\n4.0\n2.0\n3.0", "2.0000", false],
            ["5.0\n10.0\n-2.5\n2.0", "0.0000", false],
            ["20.0\n10.0\n1.0\n10.0", "0.0000", true],
            ["0.0\n5.0\n0.0\n10.0", "-5.0000", true],
        ]);
        // Q45
        $seed(45, [
            ["0.8", "0.6400", false],
            ["-0.5", "0.2500", false],
            ["1.0", "1.0000", true],
            ["0.0", "0.0000", true],
        ]);
        // Q46
        $seed(46, [
            ["10\n20\n15\n30", "12.50", false],
            ["5\n5\n5\n5", "0.00", false],
            ["100\n150\n100\n150", "50.00", true],
            ["20\n10\n30\n15", "-12.50", true],
        ]);
        // Q47
        $seed(47, [
            ["10\n20\n15\n30", "7.50", false],
            ["5\n5\n5\n5", "0.00", false],
            ["100\n150\n100\n150", "0.00", true],
            ["20\n10\n30\n15", "2.50", true],
        ]);
        // Q48
        $seed(48, [
            ["40.0\n100.0", "0.4000", false],
            ["10.0\n50.0", "0.2000", false],
            ["50.0\n50.0", "1.0000", true],
            ["0.0\n100.0", "0.0000", true],
        ]);
        // Q49
        $seed(49, [
            ["10\n5\n2\n20", "20.0000", false],
            ["5\n5\n5\n5", "1.0000", false],
            ["20\n10\n5\n50", "20.0000", true],
            ["2\n10\n10\n2", "0.0400", true],
        ]);
        // Q50
        $seed(50, [
            ["10\n10\n5\n15", "2.0000", false],
            ["5\n5\n5\n5", "1.0000", false],
            ["20\n80\n10\n90", "2.0000", true],
            ["5\n45\n10\n40", "0.5000", true],
        ]);

        $this->command->info('✅ Module 8 Coding (UniversityStudent) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}