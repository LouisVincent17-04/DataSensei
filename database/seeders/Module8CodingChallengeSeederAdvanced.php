<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 8 — Statistics & Probability (Advanced) — CODING variant  [Tier 4]
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Advanced tier
 *   2. coding_questions    — 50 questions covering all statistics topics at advanced level
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (Advanced):
 *   8.1  Introduction to Statistical Thinking
 *   8.2  Descriptive Statistics & Data Summarization
 *   8.3  Probability Distributions in Practice
 *   8.4  Sampling Theory & Survey Design
 *   8.5  Hypothesis Testing: Framework & One-Sample Tests
 *   8.6  Two-Sample Tests & ANOVA
 *   8.7  Chi-Square Tests & Non-Parametric Methods
 *   8.8  Correlation, Regression & Model Diagnostics
 *   8.9  Experimental Design: Principles & Layouts
 *   8.10 Effect Size, Power Analysis & Sample Size Planning
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module8CodingChallengeSeederAdvanced extends Seeder
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

        $this->command->info('Creating Module 8 — Statistics & Probability (Advanced) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Statistics & Probability',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply intermediate-to-advanced statistics in Python — coefficient of variation, weighted means, outlier detection, z-scores, confidence intervals, Welch\'s t-test, ANOVA decomposition, Spearman correlation, and more. Problems require multi-step computation on variable-length data.',
                'time_limit_seconds' => 1200,
                'base_xp'            => 750,
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

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` floats (one per line). Compute the **Coefficient of Variation (CV)** using the **population standard deviation**:

**CV = (σ / mean) × 100**

Print the result rounded to **2 decimal places**.

Example:
```
Input:
5
10
20
30
40
50
Output: 47.14
```

Hint: CV expresses standard deviation as a percentage of the mean.
MD,
                'starter_code'        => "n = int(input())\nnumbers = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` pairs of (value, weight) — each pair on **two separate lines** (value line, then weight line). Compute the **weighted mean**:

**weighted_mean = Σ(value × weight) / Σ(weight)**

Print the result rounded to **2 decimal places**.

Example:
```
Input:
3
10
2
20
3
30
5
Output: 23.00
```
MD,
                'starter_code'        => "n = int(input())\npairs = [(float(input()), float(input())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read exactly **9** integers (one per line). Using the **1.5 × IQR rule**, count how many values are **outliers**.

Steps:
1. Sort the values.
2. Q1 = sorted value at index 2, Q3 = sorted value at index 6.
3. IQR = Q3 − Q1.
4. Lower fence = Q1 − 1.5 × IQR; Upper fence = Q3 + 1.5 × IQR.
5. Count values below the lower fence or above the upper fence.

Print the **count** of outliers.

Example:
```
Input:
1
2
3
4
5
6
7
8
100
Output: 1
```
MD,
                'starter_code'        => "numbers = [int(input()) for _ in range(9)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` floats (one per line). Compute the **z-score** for each value using the **population mean and population standard deviation**:

**z = (x − μ) / σ**

Print each z-score on its own line, rounded to **4 decimal places**.

Example:
```
Input:
5
10
20
30
40
50
Output:
-1.4142
-0.7071
0.0000
0.7071
1.4142
```
MD,
                'starter_code'        => "n = int(input())\nnumbers = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` integers (one per line). Print the **five-number summary** in the following order, each on its own line:

1. Minimum
2. Q1 (sorted value at index `n // 4`)
3. Median (sorted value at index `n // 2`)
4. Q3 (sorted value at index `3 * n // 4`)
5. Maximum

Example (n = 9):
```
Input:
9
3
1
4
1
5
9
2
6
5
Output:
1
2
4
5
9
```
MD,
                'starter_code'        => "n = int(input())\nnumbers = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.2: Descriptive Statistics & Data Summarization (Q6–Q12)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` floats. Compute the **sample variance** (Bessel's correction — divide by **n − 1**):

**s² = Σ(xi − x̄)² / (n − 1)**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
5
2
4
4
4
5
Output: 1.2000
```
MD,
                'starter_code'        => "n = int(input())\nnumbers = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` floats. Compute the **sample standard deviation** (Bessel's correction):

**s = √( Σ(xi − x̄)² / (n − 1) )**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
5
2
4
4
4
5
Output: 1.0954
```

Hint: use `** 0.5` to compute the square root.
MD,
                'starter_code'        => "n = int(input())\nnumbers = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` floats. Compute **Pearson's skewness coefficient** using the population standard deviation:

**Skewness = 3 × (mean − median) / σ**

Sort the data first, then take the middle element as the median.

Print the result rounded to **4 decimal places**.

Example:
```
Input:
5
1
2
3
4
100
Output: 1.4611
```
MD,
                'starter_code'        => "n = int(input())\nnumbers = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` floats, then an integer `p` (a percentile from 0 to 100). Compute the **p-th percentile** using linear interpolation:

1. Sort the data.
2. Compute: `L = (p / 100) × (n − 1)`
3. `lower = floor(L)`, `upper = ceil(L)`
4. Result = `data[lower] + (L − lower) × (data[upper] − data[lower])`

Print the result rounded to **2 decimal places**.

Example:
```
Input:
5
10
20
30
40
50
40
Output: 26.00
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnumbers = sorted([float(input()) for _ in range(n)])\np = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` floats, then an integer `k`. Compute the **trimmed mean** by removing the `k` smallest and `k` largest values, then averaging the remaining values.

Print the result rounded to **2 decimal places**.

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
1
Output: 4.00
```
MD,
                'starter_code'        => "n = int(input())\nnumbers = sorted([float(input()) for _ in range(n)])\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` x-values (one per line), then `n` y-values (one per line). Compute the **population covariance**:

**Cov(X, Y) = Σ(xi − x̄)(yi − ȳ) / n**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
4
1
2
3
4
2
4
6
8
Output: 2.5000
```
MD,
                'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` positive floats (one per line). Compute the **geometric mean**:

**GM = (x1 × x2 × … × xn)^(1/n)**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
4
1
2
4
8
Output: 2.8284
```

Hint: use `** (1/n)` or raise the product to the power `1/n`.
MD,
                'starter_code'        => "n = int(input())\nnumbers = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.3: Probability Distributions in Practice (Q13–Q18)
            // ═══════════════════════════════════════════════════════════════

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Read three values on separate lines: `n` (int), `k` (int), and `p` (float). Compute the **binomial PMF**:

**P(X = k) = C(n, k) × p^k × (1 − p)^(n − k)**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
10
3
0.5
Output: 0.1172
```

Hint: use `math.comb(n, k)` for the binomial coefficient.
MD,
                'starter_code'        => "import math\nn = int(input())\nk = int(input())\np = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Read `lambda` (float) and `k` (int) on separate lines. Compute the **Poisson PMF**:

**P(X = k) = e^(−λ) × λ^k / k!**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
3
3
Output: 0.2240
```

Hint: use `import math` for `math.exp`, `math.factorial`, and `math.e`.
MD,
                'starter_code'        => "import math\nlam = float(input())\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Read a success probability `p` (float). For a **geometric distribution** (number of trials until the first success), print:

1. Mean: **E[X] = 1 / p**
2. Variance: **Var[X] = (1 − p) / p²**

Print each value on its own line, rounded to **4 decimal places**.

Example:
```
Input: 0.5
Output:
2.0000
2.0000
```
MD,
                'starter_code'        => "p = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Read an integer `n` (number of outcomes), then `n` pairs: value (float) then probability (float), each on its own line. Compute the **expected value** of the discrete distribution:

**E[X] = Σ xi × P(xi)**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
3
0
0.2
1
0.5
2
0.3
Output: 1.1000
```
MD,
                'starter_code'        => "n = int(input())\npairs = [(float(input()), float(input())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` pairs of (value, probability) — each on its own line. Compute the **variance** of the discrete distribution:

**Var[X] = E[X²] − (E[X])²**

where E[X²] = Σ xi² × P(xi)

Print the result rounded to **4 decimal places**.

Example:
```
Input:
3
0
0.2
1
0.5
2
0.3
Output: 0.4900
```
MD,
                'starter_code'        => "n = int(input())\npairs = [(float(input()), float(input())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Read three values: `n` (int), `k` (int), `p` (float). Compute the **binomial CDF**: the probability that X is **at most k**:

**P(X ≤ k) = Σ_{i=0}^{k} C(n, i) × p^i × (1 − p)^(n − i)**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
5
2
0.5
Output: 0.5000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nk = int(input())\np = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.4: Sampling Theory & Survey Design (Q19–Q23)
            // ═══════════════════════════════════════════════════════════════

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Read four values: `x_bar` (sample mean), `sigma` (population std dev), `n` (sample size), and `z` (critical z-value), all as floats. Compute the **z-confidence interval** for the population mean:

**Lower = x̄ − z × (σ / √n)**
**Upper = x̄ + z × (σ / √n)**

Print the lower bound on line 1 and upper bound on line 2, each rounded to **4 decimal places**.

Example:
```
Input:
100
15
25
1.96
Output:
94.1200
105.8800
```
MD,
                'starter_code'        => "x_bar = float(input())\nsigma = float(input())\nn = float(input())\nz = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Read three values: `p_hat` (sample proportion), `n` (sample size, int), and `z` (critical z-value). Compute the **confidence interval for a proportion**:

**SE = √( p̂ × (1 − p̂) / n )**
**Lower = p̂ − z × SE**
**Upper = p̂ + z × SE**

Print the lower bound on line 1 and upper bound on line 2, each rounded to **4 decimal places**.

Example:
```
Input:
0.4
100
1.96
Output:
0.3040
0.4960
```
MD,
                'starter_code'        => "p_hat = float(input())\nn = int(input())\nz = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Read two integers: `N` (population size) and `n` (sample size). Compute the **finite population correction (FPC) factor**:

**FPC = √( (N − n) / (N − 1) )**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
1000
100
Output: 0.9492
```
MD,
                'starter_code'        => "N = int(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Read the total desired sample size `n`, then the number of strata `k`, then `k` stratum population sizes (one per line). Compute the **proportional allocation** for each stratum:

**n_i = round( n × N_i / N_total )**

Print each stratum's allocated sample size on its own line (as integers).

Example:
```
Input:
100
3
200
300
500
Output:
20
30
50
```
MD,
                'starter_code'        => "n = int(input())\nk = int(input())\nstrata = [int(input()) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Read three values: `z` (critical z-value), `sigma` (population std dev), and `n` (sample size). Compute the **margin of error** for the mean:

**ME = z × σ / √n**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
1.96
10
100
Output: 1.9600
```
MD,
                'starter_code'        => "z = float(input())\nsigma = float(input())\nn = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.5: Hypothesis Testing: Framework & One-Sample Tests (Q24–Q28)
            // ═══════════════════════════════════════════════════════════════

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Compute the **Welch's t-test statistic** for two independent groups with unequal variances.

Read (one per line): `x1`, `x2` (group means), `s1`, `s2` (sample std devs), `n1`, `n2` (sample sizes).

**t = (x̄1 − x̄2) / √(s1² / n1 + s2² / n2)**

Print t rounded to **4 decimal places**.

Example:
```
Input:
55
45
12
10
20
25
Output: 2.9880
```
MD,
                'starter_code'        => "x1 = float(input())\nx2 = float(input())\ns1 = float(input())\ns2 = float(input())\nn1 = int(input())\nn2 = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Compute the **Welch–Satterthwaite degrees of freedom** for two groups.

Read (one per line): `s1`, `s2` (sample std devs), `n1`, `n2` (sample sizes).

**df = (s1²/n1 + s2²/n2)² / [ (s1²/n1)² / (n1 − 1) + (s2²/n2)² / (n2 − 1) ]**

Print the result as an **integer** (use `int()` to truncate, not round).

Example:
```
Input:
12
10
20
25
Output: 36
```
MD,
                'starter_code'        => "s1 = float(input())\ns2 = float(input())\nn1 = int(input())\nn2 = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Compute the **paired t-test statistic**.

Read an integer `n`, then `n` pairs of (before, after) values — before and after on separate lines each.

1. Compute differences: d_i = after_i − before_i
2. d̄ = mean of differences
3. s_d = **sample** standard deviation of differences
4. t = d̄ / (s_d / √n)

Print t rounded to **4 decimal places**.

Example:
```
Input:
4
10
12
15
16
20
23
25
26
Output: 3.6558
```
MD,
                'starter_code'        => "n = int(input())\npairs = [(float(input()), float(input())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Compute the **one-proportion z-test statistic**.

Read (one per line): `p_hat` (sample proportion), `p0` (hypothesized proportion), `n` (sample size).

**z = (p̂ − p0) / √( p0 × (1 − p0) / n )**

Print z rounded to **4 decimal places**.

Example:
```
Input:
0.55
0.5
100
Output: 1.0000
```
MD,
                'starter_code'        => "p_hat = float(input())\np0 = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Read a significance level `alpha` (float) and an integer `m` (number of simultaneous tests). Apply the **Bonferroni correction** to compute the adjusted per-test significance level:

**alpha_corrected = alpha / m**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
0.05
5
Output: 0.0100
```
MD,
                'starter_code'        => "alpha = float(input())\nm = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.6: Two-Sample Tests & ANOVA (Q29–Q34)
            // ═══════════════════════════════════════════════════════════════

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Read the number of groups `k`, then for each group read its size `n_i` followed by `n_i` float values (one per line). Compute the **between-group sum of squares (SSB)**:

**SSB = Σ n_i × (x̄_i − x̄_grand)²**

where x̄_grand is the mean of all values across all groups.

Print the result rounded to **4 decimal places**.

Example:
```
Input:
2
3
10
20
30
3
40
50
60
Output: 1350.0000
```
MD,
                'starter_code'        => "k = int(input())\ngroups = []\nfor _ in range(k):\n    ni = int(input())\n    groups.append([float(input()) for _ in range(ni)])\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Read the number of groups `k`, then for each group read its size `n_i` followed by `n_i` float values. Compute the **within-group sum of squares (SSW)**:

**SSW = Σ_i Σ_j (x_ij − x̄_i)²**

where x̄_i is the mean of group i.

Print the result rounded to **4 decimal places**.

Example:
```
Input:
2
3
10
20
30
3
40
50
60
Output: 400.0000
MD,
'starter_code'        => "k = int(input())\ngroups = []\nfor _ in range(k):\n    ni = int(input())\n    groups.append([float(input()) for _ in range(ni)])\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 31,
'problem_description' => <<<'MD'
Read the number of groups k, then for each group read its size n_i followed by n_i float values. Compute the total sum of squares (SST):

SST = Σ (x_ij − x̄_grand)²

Print the result rounded to 4 decimal places.

Example:

Input:
2
3
10
20
30
3
40
50
60
Output: 1750.0000
MD,
'starter_code'        => "k = int(input())\ngroups = []\nfor _ in range(k):\n    ni = int(input())\n    groups.append([float(input()) for _ in range(ni)])\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 32,
'problem_description' => <<<'MD'
Read the number of groups k, then for each group read its size n_i followed by n_i float values. Compute the One-Way ANOVA F-statistic:

F = (SSB / (k - 1)) / (SSW / (N - k))

Where N is the total number of observations. Print the result rounded to 4 decimal places.

Example:

Input:
2
3
10
20
30
3
40
50
60
Output: 13.5000
MD,
'starter_code'        => "k = int(input())\ngroups = []\nfor _ in range(k):\n    ni = int(input())\n    groups.append([float(input()) for _ in range(ni)])\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 33,
'problem_description' => <<<'MD'
Compute Cohen's d for two independent groups. Read two group sizes n1 and n2, followed by their respective float values.

d = (x̄1 − x̄2) / s_pooled

Where s_pooled = √( ((n1 - 1)s1² + (n2 - 1)s2²) / (n1 + n2 - 2) ).
Print the absolute value of d rounded to 4 decimal places.

Example:

Input:
3
10
20
30
3
40
50
60
Output: 3.0000
MD,
'starter_code'        => "n1 = int(input())\ng1 = [float(input()) for _ in range(n1)]\nn2 = int(input())\ng2 = [float(input()) for _ in range(n2)]\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 34,
'problem_description' => <<<'MD'
Compute the Two-Proportion Z-Test statistic. Read x1, n1, x2, n2 on separate lines.

z = (p̂1 - p̂2) / √( p̂(1 - p̂)(1/n1 + 1/n2) )

Where p̂1 = x1/n1, p̂2 = x2/n2, and pooled proportion p̂ = (x1 + x2) / (n1 + n2).
Print z rounded to 4 decimal places.

Example:

Input:
45
100
30
100
Output: 2.1932
MD,
'starter_code'        => "x1 = float(input())\nn1 = float(input())\nx2 = float(input())\nn2 = float(input())\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 35,
'problem_description' => <<<'MD'
Compute the Chi-Square Goodness of Fit statistic. Read integer k (categories), then k observed frequencies, then k expected frequencies.

χ² = Σ ((O_i - E_i)² / E_i)

Print χ² rounded to 4 decimal places.

Example:

Input:
3
10
20
30
15
15
30
Output: 3.3333
MD,
'starter_code'        => "k = int(input())\nO = [float(input()) for _ in range(k)]\nE = [float(input()) for _ in range(k)]\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 36,
'problem_description' => <<<'MD'
Compute the Chi-Square statistic for Independence for a 2x2 table. Read a, b, c, d representing rows and columns.

Use the direct formula for a 2x2 table:
χ² = N(ad - bc)² / ((a+b)(c+d)(a+c)(b+d))

Print χ² rounded to 4 decimal places.

Example:

Input:
10
20
30
40
Output: 0.2778
MD,
'starter_code'        => "a = float(input())\nb = float(input())\nc = float(input())\nd = float(input())\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 37,
'problem_description' => <<<'MD'
Compute the Expected Frequency for a specific cell in a contingency table. Read the row total R, column total C, and grand total N.

E = (R × C) / N

Print E rounded to 4 decimal places.

Example:

Input:
50
40
200
Output: 10.0000
MD,
'starter_code'        => "R = float(input())\nC = float(input())\nN = float(input())\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 38,
'problem_description' => <<<'MD'
Compute the Mann-Whitney U statistic (U1) for the first group. Read two group sizes n1, n2 and their elements.

Combine and rank all elements (ignore ties for simplicity, assume distinct values).

Sum the ranks of group 1 to get R1.

U1 = R1 - (n1(n1 + 1)) / 2

Print U1 as an integer.

Example:

Input:
3
3
1
2
4
10
20
30
Output: 9
MD,
'starter_code'        => "n1 = int(input())\nn2 = int(input())\ng1 = [float(input()) for _ in range(n1)]\ng2 = [float(input()) for _ in range(n2)]\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 39,
'problem_description' => <<<'MD'
Compute Spearman's Rank Correlation Coefficient. Read integer n, then n x-values, then n y-values.

Assume no ties.
r_s = 1 - ( (6 Σ d_i²) / (n(n² - 1)) )

Where d_i is the difference between the rank of x_i and rank of y_i.
Print r_s rounded to 4 decimal places.

Example:

Input:
3
10
20
30
100
50
200
Output: 0.5000
MD,
'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 40,
'problem_description' => <<<'MD'
Compute the Simple Linear Regression Slope (b1). Read integer n, then n x-values, then n y-values.

b1 = Σ((x_i - x̄)(y_i - ȳ)) / Σ((x_i - x̄)²)

Print b1 rounded to 4 decimal places.

Example:

Input:
3
1
2
3
2
4
6
Output: 2.0000
MD,
'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 41,
'problem_description' => <<<'MD'
Compute the Simple Linear Regression Intercept (b0). Read integer n, then n x-values, then n y-values.

b0 = ȳ - b1 × x̄

Print b0 rounded to 4 decimal places.

Example:

Input:
3
1
2
3
3
5
7
Output: 1.0000
MD,
'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 42,
'problem_description' => <<<'MD'
Compute the Coefficient of Determination (R²). Read integer n, then n actual y-values, then n predicted y-values.

R² = 1 - (SSE / SST)

Where SSE = Σ(y_i - ŷ_i)² and SST = Σ(y_i - ȳ)².
Print R² rounded to 4 decimal places.

Example:

Input:
3
2
4
6
2.1
3.9
6.2
Output: 0.9943
MD,
'starter_code'        => "n = int(input())\ny = [float(input()) for _ in range(n)]\ny_pred = [float(input()) for _ in range(n)]\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 43,
'problem_description' => <<<'MD'
Compute the Standard Error of the Estimate. Read integer n, then n actual y-values, then n predicted y-values.

s_e = √( Σ(y_i - ŷ_i)² / (n - 2) )

Print s_e rounded to 4 decimal places.

Example:

Input:
3
2
4
6
2.1
3.9
6.2
Output: 0.2449
MD,
'starter_code'        => "n = int(input())\ny = [float(input()) for _ in range(n)]\ny_pred = [float(input()) for _ in range(n)]\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 44,
'problem_description' => <<<'MD'
Compute the Adjusted R-squared. Read R², sample size n, and number of predictors p.

Adj R² = 1 - ( (1 - R²) × (n - 1) / (n - p - 1) )

Print the result rounded to 4 decimal places.

Example:

Input:
0.85
50
3
Output: 0.8402
MD,
'starter_code'        => "r2 = float(input())\nn = int(input())\np = int(input())\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 45,
'problem_description' => <<<'MD'
Compute the Block Sum of Squares (SSBlock) for a Randomized Block Design. Read integer b (blocks), t (treatments), then b block means, then the grand mean.

SSBlock = t × Σ (x̄_block - x̄_grand)²

Print the result rounded to 4 decimal places.

Example:

Input:
2
3
10
20
15
Output: 150.0000
MD,
'starter_code'        => "b = int(input())\nt = int(input())\nblock_means = [float(input()) for _ in range(b)]\ngrand_mean = float(input())\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 46,
'problem_description' => <<<'MD'
Compute the Main Effect of Factor A in a 2² Factorial Design. Read responses for conditions: (1), a, b, ab.

Effect A = (a + ab - (1) - b) / 2

Print the result rounded to 4 decimal places.

Example:

Input:
10
20
15
25
Output: 10.0000
MD,
'starter_code'        => "c_1 = float(input())\na = float(input())\nb = float(input())\nab = float(input())\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 47,
'problem_description' => <<<'MD'
Compute the Interaction Effect AB in a 2² Factorial Design. Read responses for conditions: (1), a, b, ab.

Effect AB = (ab + (1) - a - b) / 2

Print the result rounded to 4 decimal places.

Example:

Input:
10
20
15
30
Output: 2.5000
MD,
'starter_code'        => "c_1 = float(input())\na = float(input())\nb = float(input())\nab = float(input())\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 48,
'problem_description' => <<<'MD'
Compute the minimum Sample Size required to estimate a population mean. Read z (critical value), σ (population std dev), and ME (margin of error).

n = ⌈ (z × σ / ME)² ⌉

Always round up to the next whole number. Print n as an integer.

Example:

Input:
1.96
15
5
Output: 35
MD,
'starter_code'        => "import math\nz = float(input())\nsigma = float(input())\nme = float(input())\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 49,
'problem_description' => <<<'MD'
Compute the minimum Sample Size required to estimate a population proportion. Read z, p̂ (estimated proportion), and ME.

n = ⌈ z² × p̂ × (1 - p̂) / ME² ⌉

Always round up to the next whole number. Print n as an integer.

Example:

Input:
1.96
0.5
0.05
Output: 385
MD,
'starter_code'        => "import math\nz = float(input())\np_hat = float(input())\nme = float(input())\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
[
'order_index'         => 50,
'problem_description' => <<<'MD'
Compute Eta-squared (η²) for ANOVA. Read SSB (Between-group Sum of Squares) and SSW (Within-group Sum of Squares).

η² = SSB / (SSB + SSW)

Print η² rounded to 4 decimal places.

Example:

Input:
150
300
Output: 0.3333
MD,
'starter_code'        => "ssb = float(input())\nssw = float(input())\n",
'time_limit_seconds'  => 900,
'base_xp'             => 150,
],
];

    foreach ($questionDefs as $qDef) {
        $question = \DB::table('coding_questions')->insertGetId([
            'challenge_id'        => $challenge->id,
            'order_index'         => $qDef['order_index'],
            'problem_description' => $qDef['problem_description'],
            'starter_code'        => $qDef['starter_code'],
            'time_limit_seconds'  => $qDef['time_limit_seconds'],
            'base_xp'             => $qDef['base_xp'],
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        for ($i = 1; $i <= 4; $i++) {
            \DB::table('test_cases')->insert([
                'coding_question_id' => $question,
                'is_hidden'          => $i > 2 ? 1 : 0,
                'input'              => "Input data for test case " . $i,
                'expected_output'    => "Output for test case " . $i,
                'order_index'        => $i,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }
}
}