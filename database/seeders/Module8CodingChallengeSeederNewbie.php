<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 8 — Statistics & Probability (Newbie) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering all statistics basics
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (Newbie):
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
class Module8CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 8 — Statistics & Probability (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Statistics & Probability',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply fundamental statistics concepts in Python — compute means, medians, modes, standard deviations, and basic probability. Tasks are short and self-contained, requiring only simple arithmetic and built-in Python features.',
                'time_limit_seconds' => 900,
                'base_xp'            => 500,
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
Read a list of 5 numbers (one per line) and print the **count** of numbers.

Example:
```
Input:
10
20
30
40
50
Output: 5
```
MD,
                'starter_code'        => "numbers = [int(input()) for _ in range(5)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read 5 integers (one per line) and print the **sum** of all numbers.

Example:
```
Input:
1
2
3
4
5
Output: 15
```
MD,
                'starter_code'        => "numbers = [int(input()) for _ in range(5)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read 5 integers (one per line) and print the **minimum** and **maximum** values, each on its own line.

Example:
```
Input:
3
7
1
9
4
Output:
1
9
```
MD,
                'starter_code'        => "numbers = [int(input()) for _ in range(5)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read 5 integers (one per line) and print the **range** (max minus min).

Example:
```
Input:
3
7
1
9
4
Output: 8
```
MD,
                'starter_code'        => "numbers = [int(input()) for _ in range(5)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read a single integer `n`, then read `n` numbers (one per line). Print `Population` if `n >= 30`, otherwise print `Sample`.

Example:
```
Input:
25
Output: Sample
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.2: Descriptive Statistics & Data Summarization (Q6–Q12)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Read 5 integers (one per line) and print their **mean** (average) rounded to **2 decimal places**.

Example:
```
Input:
4
8
6
2
10
Output: 6.00
```
MD,
                'starter_code'        => "numbers = [int(input()) for _ in range(5)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Read 5 integers (one per line) and print their **median**.

Sort the numbers first, then print the middle value.

Example:
```
Input:
3
1
4
1
5
Output: 3
```
MD,
                'starter_code'        => "numbers = [int(input()) for _ in range(5)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Read 5 integers (one per line) and print the **mode** (the number that appears most often).

If multiple modes exist, print the smallest one.

Example:
```
Input:
4
1
2
4
3
Output: 4
```
MD,
                'starter_code'        => "numbers = [int(input()) for _ in range(5)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read 5 numbers (one per line) and print the **variance** (population variance) rounded to **2 decimal places**.

Population variance formula: sum of (x - mean)² divided by n.

Example:
```
Input:
2
4
4
4
5
Output: 0.96
```

Hint: compute the mean first, then find the average squared deviation.
MD,
                'starter_code'        => "numbers = [float(input()) for _ in range(5)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read 5 numbers (one per line) and print the **population standard deviation** rounded to **2 decimal places**.

Standard deviation is the square root of the variance.

Example:
```
Input:
2
4
4
4
5
Output: 0.98
```

Hint: use `** 0.5` to compute square root.
MD,
                'starter_code'        => "numbers = [float(input()) for _ in range(5)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Read 7 integers (one per line), sort them, and print the **first quartile (Q1)** — the median of the lower half (not including the overall median).

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
Output: 2
```
MD,
                'starter_code'        => "numbers = [int(input()) for _ in range(7)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Read 5 integers (one per line) and print the **interquartile range (IQR)**.

For 5 values sorted as [a, b, c, d, e]:
- Q1 = b (index 1)
- Q3 = d (index 3)
- IQR = Q3 − Q1

Example:
```
Input:
1
3
5
7
9
Output: 4
```
MD,
                'starter_code'        => "numbers = sorted([int(input()) for _ in range(5)])\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.3: Probability Distributions in Practice (Q13–Q18)
            // ═══════════════════════════════════════════════════════════════

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Read two integers: `favorable` outcomes and `total` outcomes. Print the **probability** as a decimal rounded to **4 decimal places**.

Example:
```
Input:
3
10
Output: 0.3000
```
MD,
                'starter_code'        => "favorable = int(input())\ntotal = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
A fair die has 6 sides. Read an integer `n` (1–6) and print the probability of rolling **at most n** on one roll, rounded to **4 decimal places**.

Example:
```
Input: 4
Output: 0.6667
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Read a probability `p` (as a float). Print the **complement** probability (1 − p) rounded to **4 decimal places**.

Example:
```
Input: 0.35
Output: 0.6500
```
MD,
                'starter_code'        => "p = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Read two independent probabilities `p1` and `p2` (one per line as floats). Print the probability that **both** events occur (joint probability for independent events), rounded to **4 decimal places**.

Example:
```
Input:
0.5
0.4
Output: 0.2000
```
MD,
                'starter_code'        => "p1 = float(input())\np2 = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
A binomial experiment has `n` trials each with success probability `p`. Read `n` (int) and `p` (float) on separate lines.

Print the **expected value** (mean = n × p) rounded to **2 decimal places**.

Example:
```
Input:
10
0.3
Output: 3.00
```
MD,
                'starter_code'        => "n = int(input())\np = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Read `n` (int) and `p` (float) for a binomial distribution.

Print the **standard deviation** (√(n × p × (1 − p))) rounded to **4 decimal places**.

Example:
```
Input:
10
0.3
Output: 1.4491
```
MD,
                'starter_code'        => "n = int(input())\np = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.4: Sampling Theory & Survey Design (Q19–Q23)
            // ═══════════════════════════════════════════════════════════════

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Read a population size `N` and a sample size `n` (one per line). Print the **sampling fraction** (n / N) rounded to **4 decimal places**.

Example:
```
Input:
1000
50
Output: 0.0500
```
MD,
                'starter_code'        => "N = int(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Read a population size `N` and a desired margin of error `E` (as a float). Using the simplified formula for sample size with p = 0.5 and z = 1.96:

**n = (z² × p × (1 − p)) / E²**

Print the **minimum sample size** (round up to the nearest integer).

Example:
```
Input:
10000
0.05
Output: 385
```

Hint: use `import math` and `math.ceil()`.
MD,
                'starter_code'        => "import math\nN = int(input())\nE = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Read a population size `N` and a sample size `n`. Read `n` sampled values (one per line, integers). Print the **sample mean** rounded to **2 decimal places**.

Example:
```
Input:
100
3
10
20
30
Output: 20.00
```
MD,
                'starter_code'        => "N = int(input())\nn = int(input())\nsamples = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
A systematic sample takes every k-th element. Read `N` (population size) and `n` (sample size). Print the **sampling interval** k = N // n.

Example:
```
Input:
100
10
Output: 10
```
MD,
                'starter_code'        => "N = int(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Read a sample size `n` and a sample standard deviation `s` (float). Print the **standard error of the mean** (SE = s / √n) rounded to **4 decimal places**.

Example:
```
Input:
25
5.0
Output: 1.0000
```
MD,
                'starter_code'        => "n = int(input())\ns = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.5: Hypothesis Testing: Framework & One-Sample Tests (Q24–Q28)
            // ═══════════════════════════════════════════════════════════════

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Read a p-value (float) and a significance level alpha (float) on separate lines. Print `Reject H0` if p-value ≤ alpha, otherwise print `Fail to Reject H0`.

Example:
```
Input:
0.03
0.05
Output: Reject H0
```
MD,
                'starter_code'        => "p_value = float(input())\nalpha = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Compute the **one-sample z-test statistic**.

Read:
- `x_bar` (sample mean, float)
- `mu` (population mean, float)
- `sigma` (population std dev, float)
- `n` (sample size, int)

Formula: z = (x_bar − mu) / (sigma / √n)

Print z rounded to **4 decimal places**.

Example:
```
Input:
52
50
10
25
Output: 1.0000
```
MD,
                'starter_code'        => "x_bar = float(input())\nmu = float(input())\nsigma = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Compute the **one-sample t-test statistic**.

Read:
- `x_bar` (sample mean, float)
- `mu` (hypothesized mean, float)
- `s` (sample std dev, float)
- `n` (sample size, int)

Formula: t = (x_bar − mu) / (s / √n)

Print t rounded to **4 decimal places**.

Example:
```
Input:
5.2
5.0
0.5
16
Output: 1.6000
```
MD,
                'starter_code'        => "x_bar = float(input())\nmu = float(input())\ns = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Read a sample size `n` and print the **degrees of freedom** for a one-sample t-test (df = n − 1).

Example:
```
Input: 20
Output: 19
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Read a z-score (float). Classify the result using these rules:
- If z > 1.96: print `Reject H0 (two-tailed, alpha=0.05)`
- If z < -1.96: print `Reject H0 (two-tailed, alpha=0.05)`
- Otherwise: print `Fail to Reject H0`

Example:
```
Input: 2.1
Output: Reject H0 (two-tailed, alpha=0.05)
```
MD,
                'starter_code'        => "z = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.6: Two-Sample Tests & ANOVA (Q29–Q34)
            // ═══════════════════════════════════════════════════════════════

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Read two sample means and two sample sizes (one per line: x1, x2, n1, n2). Print the **pooled mean** (weighted average): (x1 × n1 + x2 × n2) / (n1 + n2), rounded to **2 decimal places**.

Example:
```
Input:
10
20
5
5
Output: 15.00
```
MD,
                'starter_code'        => "x1 = float(input())\nx2 = float(input())\nn1 = int(input())\nn2 = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Compute the **two-sample z-test statistic**.

Read:
- `x1` (mean of group 1, float)
- `x2` (mean of group 2, float)
- `s1` (std dev of group 1, float)
- `s2` (std dev of group 2, float)
- `n1` (size of group 1, int)
- `n2` (size of group 2, int)

Formula: z = (x1 − x2) / √(s1²/n1 + s2²/n2)

Print z rounded to **4 decimal places**.

Example:
```
Input:
50
45
10
8
30
30
Output: 2.4254
```
MD,
                'starter_code'        => "x1 = float(input())\nx2 = float(input())\ns1 = float(input())\ns2 = float(input())\nn1 = int(input())\nn2 = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Read two sample sizes `n1` and `n2`. Print the **degrees of freedom** for an independent two-sample t-test using the simple formula: df = n1 + n2 − 2.

Example:
```
Input:
15
12
Output: 25
```
MD,
                'starter_code'        => "n1 = int(input())\nn2 = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Read `k` (number of groups, int) and then `k` group means (one per line as floats). Print the **grand mean** (mean of all group means) rounded to **2 decimal places**.

Example:
```
Input:
3
10.0
20.0
30.0
Output: 20.00
```
MD,
                'starter_code'        => "k = int(input())\nmeans = [float(input()) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Read `k` (number of groups, int), then k group sizes (one per line as ints). Print the **total sample size** N = sum of all group sizes.

Example:
```
Input:
3
10
15
20
Output: 45
```
MD,
                'starter_code'        => "k = int(input())\nsizes = [int(input()) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
For a one-way ANOVA, read `k` (number of groups) and the total sample size `N`. Print the **degrees of freedom between groups** (df_between = k − 1) and **degrees of freedom within groups** (df_within = N − k), each on its own line.

Example:
```
Input:
3
45
Output:
2
42
```
MD,
                'starter_code'        => "k = int(input())\nN = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.7: Chi-Square Tests & Non-Parametric Methods (Q35–Q39)
            // ═══════════════════════════════════════════════════════════════

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Read `n` observed frequencies and `n` expected frequencies (n on the first line, then alternating observed and expected values). Compute the **chi-square statistic**: χ² = Σ((O − E)² / E), rounded to **4 decimal places**.

Example:
```
Input:
3
10
12
20
18
15
15
Output: 0.6111
```
MD,
                'starter_code'        => "n = int(input())\nobserved = []\nexpected = []\nfor _ in range(n):\n    observed.append(float(input()))\n    expected.append(float(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
For a chi-square goodness-of-fit test, read `k` (number of categories). Print the **degrees of freedom** (df = k − 1).

Example:
```
Input: 5
Output: 4
```
MD,
                'starter_code'        => "k = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Read `r` (rows) and `c` (columns) of a contingency table. Print the **degrees of freedom** for a chi-square test of independence: df = (r − 1) × (c − 1).

Example:
```
Input:
3
4
Output: 6
```
MD,
                'starter_code'        => "r = int(input())\nc = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Read 5 integers (one per line) and print the **median** using the following steps:
1. Sort the numbers.
2. Print the middle element (index 2 of the sorted list).

This is the basis of the non-parametric median test.

Example:
```
Input:
9
3
7
1
5
Output: 5
```
MD,
                'starter_code'        => "numbers = [int(input()) for _ in range(5)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Read 6 numbers (one per line) and print their **ranks** (1 = smallest), one per line. If there are ties, assign the average rank.

For simplicity in this problem, all values will be unique.

Example:
```
Input:
40
10
30
20
50
60
Output:
4
1
3
2
5
6
```
MD,
                'starter_code'        => "numbers = [float(input()) for _ in range(6)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.8: Correlation, Regression & Model Diagnostics (Q40–Q44)
            // ═══════════════════════════════════════════════════════════════

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Read 4 numbers: `x_mean`, `y_mean`, `Sxy` (sum of cross-deviations), and `Sxx` (sum of squared x-deviations).

Compute the **regression slope**: b1 = Sxy / Sxx, rounded to **4 decimal places**.

Example:
```
Input:
5.0
10.0
40.0
20.0
Output: 2.0000
```
MD,
                'starter_code'        => "x_mean = float(input())\ny_mean = float(input())\nSxy = float(input())\nSxx = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Read `b1` (slope), `x_mean`, and `y_mean` (all floats). Compute the **y-intercept**: b0 = y_mean − b1 × x_mean, rounded to **4 decimal places**.

Example:
```
Input:
2.0
5.0
10.0
Output: 0.0000
```
MD,
                'starter_code'        => "b1 = float(input())\nx_mean = float(input())\ny_mean = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Read `b0` (intercept), `b1` (slope), and `x` (float). Print the **predicted y-value**: ŷ = b0 + b1 × x, rounded to **2 decimal places**.

Example:
```
Input:
1.0
2.0
4.0
Output: 9.00
```
MD,
                'starter_code'        => "b0 = float(input())\nb1 = float(input())\nx = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Read a Pearson correlation coefficient `r` (float, between -1 and 1). Print the **strength** of the correlation:
- |r| >= 0.7 → `Strong`
- |r| >= 0.4 → `Moderate`
- Otherwise  → `Weak`

Example:
```
Input: -0.85
Output: Strong
```
MD,
                'starter_code'        => "r = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Read 3 pairs of actual and predicted values (actual then predicted, alternating lines). Compute the **Mean Absolute Error (MAE)**: average of |actual − predicted|, rounded to **4 decimal places**.

Example:
```
Input:
10
12
20
18
30
33
Output: 2.3333
```
MD,
                'starter_code'        => "pairs = [(float(input()), float(input())) for _ in range(3)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.9: Experimental Design: Principles & Layouts (Q45–Q47)
            // ═══════════════════════════════════════════════════════════════

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Read `k` (number of treatments) and `n` (observations per treatment). Print the **total number of experimental units**: k × n.

Example:
```
Input:
4
5
Output: 20
```
MD,
                'starter_code'        => "k = int(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Read `k` (treatments) and `b` (blocks) for a Randomized Complete Block Design (RCBD). Print the **degrees of freedom for error**: df_error = (k − 1) × (b − 1).

Example:
```
Input:
4
5
Output: 12
```
MD,
                'starter_code'        => "k = int(input())\nb = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
In a completely randomized design, read `k` (groups) and `N` (total observations). Print the degrees of freedom for:
1. Between groups: k − 1
2. Within groups: N − k
3. Total: N − 1

Print each on its own line.

Example:
```
Input:
3
30
Output:
2
27
29
```
MD,
                'starter_code'        => "k = int(input())\nN = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.10: Effect Size, Power Analysis & Sample Size Planning (Q48–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Compute **Cohen's d** (effect size for two groups).

Read:
- `x1` (mean of group 1, float)
- `x2` (mean of group 2, float)
- `s` (pooled standard deviation, float)

Formula: d = (x1 − x2) / s

Print d rounded to **4 decimal places**.

Example:
```
Input:
55
50
10
Output: 0.5000
```
MD,
                'starter_code'        => "x1 = float(input())\nx2 = float(input())\ns = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Read a Cohen's d value (float) and classify the effect size:
- d < 0.2 → `Negligible`
- d < 0.5 → `Small`
- d < 0.8 → `Medium`
- d >= 0.8 → `Large`

Use the absolute value of d for classification.

Example:
```
Input: -0.6
Output: Medium
```
MD,
                'starter_code'        => "d = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Read a desired effect size `d` (float) and a significance level alpha (float). Using the simplified formula for required sample size per group:

**n = 2 × ((z_alpha + z_beta) / d)²**

Assume z_alpha = 1.96 (alpha = 0.05) and z_beta = 0.84 (power = 80%). Ignore the alpha input for the z-score — just use 1.96.

Print the **minimum sample size per group** (round up to nearest integer).

Example:
```
Input:
0.5
0.05
Output: 64
```

Hint: use `import math` and `math.ceil()`.
MD,
                'starter_code'        => "import math\nd = float(input())\nalpha = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
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

        $seed = function (int $qIndex, array $cases) use ($insertedQuestions) {
            $questionId = $insertedQuestions[$qIndex] ?? null;
            if (! $questionId) return;

            foreach ($cases as $case) {
                $exists = DB::table('test_cases')
                    ->where('coding_question_id', $questionId)
                    ->where('order_index', $case['order_index'])
                    ->exists();

                if ($exists) continue;

                DB::table('test_cases')->insert([
                    'coding_question_id' => $questionId,
                    'input'              => $case['input'],
                    'expected_output'    => $case['expected_output'],
                    'is_hidden'          => $case['is_hidden'],
                    'order_index'        => $case['order_index'],
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        };

        // ── Q1: Count of numbers ──────────────────────────────────────────
        $seed(1, [
            ['input' => "10\n20\n30\n40\n50",   'expected_output' => '5', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2\n3\n4\n5",         'expected_output' => '5', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n200\n300\n400\n500",'expected_output' => '5', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n0\n0\n0\n0",          'expected_output' => '5', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Sum ───────────────────────────────────────────────────────
        $seed(2, [
            ['input' => "1\n2\n3\n4\n5",     'expected_output' => '15', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n10\n10\n10\n10",'expected_output' => '50', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0\n0\n0\n0",     'expected_output' => '0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n15\n25\n35\n45", 'expected_output' => '125','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Min and Max ───────────────────────────────────────────────
        $seed(3, [
            ['input' => "3\n7\n1\n9\n4",   'expected_output' => "1\n9",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n5\n8\n2\n6",  'expected_output' => "2\n10",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0\n0\n0\n0",   'expected_output' => "0\n0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "99\n1\n50\n25\n75",'expected_output' => "1\n99",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Range ────────────────────────────────────────────────────
        $seed(4, [
            ['input' => "3\n7\n1\n9\n4",    'expected_output' => '8',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n5\n8\n2\n6",   'expected_output' => '8',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0\n0\n0\n0",    'expected_output' => '0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "100\n1\n50\n25\n75",'expected_output' => '99','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Population vs Sample ──────────────────────────────────────
        $seed(5, [
            ['input' => '25',  'expected_output' => 'Sample',     'is_hidden' => false, 'order_index' => 1],
            ['input' => '30',  'expected_output' => 'Population', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '100', 'expected_output' => 'Population', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '15',  'expected_output' => 'Sample',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Mean ──────────────────────────────────────────────────────
        $seed(6, [
            ['input' => "4\n8\n6\n2\n10",  'expected_output' => '6.00',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2\n3\n4\n5",   'expected_output' => '3.00',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "10\n20\n30\n40\n50",'expected_output' => '30.00','is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n0\n0\n0\n0",   'expected_output' => '0.00',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Median ───────────────────────────────────────────────────
        $seed(7, [
            ['input' => "3\n1\n4\n1\n5",   'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n2\n8\n4\n6",  'expected_output' => '6', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2\n3\n4\n5",   'expected_output' => '3', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "9\n3\n7\n1\n5",   'expected_output' => '5', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Mode ─────────────────────────────────────────────────────
        $seed(8, [
            ['input' => "4\n1\n2\n4\n3",   'expected_output' => '4', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2\n2\n3\n4",   'expected_output' => '2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n5\n5\n5",   'expected_output' => '5', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n3\n2\n1",   'expected_output' => '1', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Population Variance ───────────────────────────────────────
        $seed(9, [
            ['input' => "2\n4\n4\n4\n5",   'expected_output' => '0.96', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2\n3\n4\n5",   'expected_output' => '2.00', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0\n0\n0\n0",   'expected_output' => '0.00', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n20\n30\n40\n50",'expected_output' => '200.00','is_hidden' => true,'order_index' => 4],
        ]);

        // ── Q10: Population Std Dev ───────────────────────────────────────
        $seed(10, [
            ['input' => "2\n4\n4\n4\n5",    'expected_output' => '0.98', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2\n3\n4\n5",    'expected_output' => '1.41', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0\n0\n0\n0",    'expected_output' => '0.00', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n20\n30\n40\n50",'expected_output' => '14.14','is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q11: Q1 ───────────────────────────────────────────────────────
        $seed(11, [
            ['input' => "1\n2\n3\n4\n5\n6\n7",'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n20\n30\n40\n50\n60\n70",'expected_output' => '20','is_hidden' => false,'order_index' => 2],
            ['input' => "7\n6\n5\n4\n3\n2\n1",'expected_output' => '2', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n4\n1\n5\n9\n2",'expected_output' => '1', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: IQR ─────────────────────────────────────────────────────
        $seed(12, [
            ['input' => "1\n3\n5\n7\n9",   'expected_output' => '6', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n4\n6\n8\n10",  'expected_output' => '6', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10\n20\n30\n40\n50",'expected_output' => '30','is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n2\n3\n4\n5",   'expected_output' => '3', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Probability ─────────────────────────────────────────────
        $seed(13, [
            ['input' => "3\n10",  'expected_output' => '0.3000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2",   'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n10",  'expected_output' => '0.7000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n4",   'expected_output' => '0.2500', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: At most n on a die ───────────────────────────────────────
        $seed(14, [
            ['input' => '4',  'expected_output' => '0.6667', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',  'expected_output' => '0.1667', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '6',  'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '3',  'expected_output' => '0.5000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Complement probability ───────────────────────────────────
        $seed(15, [
            ['input' => '0.35', 'expected_output' => '0.6500', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.5',  'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.0',  'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.99', 'expected_output' => '0.0100', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Joint probability ────────────────────────────────────────
        $seed(16, [
            ['input' => "0.5\n0.4",  'expected_output' => '0.2000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.3\n0.6",  'expected_output' => '0.1800', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n0.5",  'expected_output' => '0.5000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.25\n0.8", 'expected_output' => '0.2000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Binomial expected value ──────────────────────────────────
        $seed(17, [
            ['input' => "10\n0.3",  'expected_output' => '3.00', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "20\n0.5",  'expected_output' => '10.00','is_hidden' => false, 'order_index' => 2],
            ['input' => "50\n0.2",  'expected_output' => '10.00','is_hidden' => true,  'order_index' => 3],
            ['input' => "100\n0.75",'expected_output' => '75.00','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Binomial std dev ─────────────────────────────────────────
        $seed(18, [
            ['input' => "10\n0.3",  'expected_output' => '1.4491', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "20\n0.5",  'expected_output' => '2.2361', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "50\n0.4",  'expected_output' => '3.4641', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "100\n0.25",'expected_output' => '4.3301', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Sampling fraction ────────────────────────────────────────
        $seed(19, [
            ['input' => "1000\n50",  'expected_output' => '0.0500', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "200\n20",   'expected_output' => '0.1000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "500\n100",  'expected_output' => '0.2000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10000\n25", 'expected_output' => '0.0025', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Minimum sample size ──────────────────────────────────────
        $seed(20, [
            ['input' => "10000\n0.05", 'expected_output' => '385', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "50000\n0.03", 'expected_output' => '1068','is_hidden' => false, 'order_index' => 2],
            ['input' => "5000\n0.1",   'expected_output' => '97',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "100000\n0.02",'expected_output' => '2401','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Sample mean ─────────────────────────────────────────────
        $seed(21, [
            ['input' => "100\n3\n10\n20\n30",   'expected_output' => '20.00', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "200\n4\n5\n10\n15\n20", 'expected_output' => '12.50','is_hidden' => false, 'order_index' => 2],
            ['input' => "50\n5\n2\n4\n6\n8\n10", 'expected_output' => '6.00', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1000\n2\n100\n200",      'expected_output' => '150.00','is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q22: Sampling interval ────────────────────────────────────────
        $seed(22, [
            ['input' => "100\n10",  'expected_output' => '10', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "500\n25",  'expected_output' => '20', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1000\n50", 'expected_output' => '20', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "200\n8",   'expected_output' => '25', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Standard error ───────────────────────────────────────────
        $seed(23, [
            ['input' => "25\n5.0",  'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "100\n10.0",'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "16\n4.0",  'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "36\n6.0",  'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Reject/Fail to Reject ────────────────────────────────────
        $seed(24, [
            ['input' => "0.03\n0.05", 'expected_output' => 'Reject H0',           'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.1\n0.05",  'expected_output' => 'Fail to Reject H0',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.05\n0.05", 'expected_output' => 'Reject H0',           'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.01\n0.01", 'expected_output' => 'Reject H0',           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: One-sample z-test statistic ─────────────────────────────
        $seed(25, [
            ['input' => "52\n50\n10\n25",  'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "105\n100\n15\n36",'expected_output' => '2.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "98\n100\n10\n25", 'expected_output' => '-1.0000','is_hidden' => true,  'order_index' => 3],
            ['input' => "55\n50\n10\n100", 'expected_output' => '5.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: One-sample t-test statistic ─────────────────────────────
        $seed(26, [
            ['input' => "5.2\n5.0\n0.5\n16", 'expected_output' => '1.6000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "10.5\n10.0\n1.0\n25",'expected_output' => '2.5000','is_hidden' => false, 'order_index' => 2],
            ['input' => "4.8\n5.0\n0.4\n16",  'expected_output' => '-2.0000','is_hidden' => true, 'order_index' => 3],
            ['input' => "12.0\n10.0\n2.0\n4", 'expected_output' => '2.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q27: Degrees of freedom (one-sample t) ────────────────────────
        $seed(27, [
            ['input' => '20',  'expected_output' => '19', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '30',  'expected_output' => '29', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '100', 'expected_output' => '99', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',   'expected_output' => '0',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: z-score classification ───────────────────────────────────
        $seed(28, [
            ['input' => '2.1',   'expected_output' => 'Reject H0 (two-tailed, alpha=0.05)',       'is_hidden' => false, 'order_index' => 1],
            ['input' => '1.5',   'expected_output' => 'Fail to Reject H0',                        'is_hidden' => false, 'order_index' => 2],
            ['input' => '-2.5',  'expected_output' => 'Reject H0 (two-tailed, alpha=0.05)',       'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.0',   'expected_output' => 'Fail to Reject H0',                        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Pooled mean ──────────────────────────────────────────────
        $seed(29, [
            ['input' => "10\n20\n5\n5",    'expected_output' => '15.00', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "30\n60\n10\n20",  'expected_output' => '50.00', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n50\n20\n30", 'expected_output' => '70.00', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n5\n5\n5",      'expected_output' => '5.00',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Two-sample z-test statistic ─────────────────────────────
        $seed(30, [
            ['input' => "50\n45\n10\n8\n30\n30", 'expected_output' => '2.4254', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "100\n95\n12\n10\n50\n50",'expected_output' => '2.3936','is_hidden' => false, 'order_index' => 2],
            ['input' => "20\n20\n5\n5\n25\n25",   'expected_output' => '0.0000','is_hidden' => true,  'order_index' => 3],
            ['input' => "80\n70\n15\n12\n40\n40",  'expected_output' => '3.7139','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Two-sample t df ──────────────────────────────────────────
        $seed(31, [
            ['input' => "15\n12", 'expected_output' => '25', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "20\n20", 'expected_output' => '38', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "30\n25", 'expected_output' => '53', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n10", 'expected_output' => '18', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Grand mean ───────────────────────────────────────────────
        $seed(32, [
            ['input' => "3\n10.0\n20.0\n30.0",    'expected_output' => '20.00', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n5.0\n10.0\n15.0\n20.0",'expected_output' => '12.50','is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n50.0\n50.0",           'expected_output' => '50.00', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1.0\n2.0\n3.0\n4.0\n5.0",'expected_output' => '3.00','is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q33: Total sample size ────────────────────────────────────────
        $seed(33, [
            ['input' => "3\n10\n15\n20", 'expected_output' => '45', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n5\n5\n5\n5", 'expected_output' => '20', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n30\n30",      'expected_output' => '60', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n10\n10\n10\n10\n10",'expected_output' => '50','is_hidden' => true,'order_index' => 4],
        ]);

        // ── Q34: ANOVA df ─────────────────────────────────────────────────
        $seed(34, [
            ['input' => "3\n45",  'expected_output' => "2\n42", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n40",  'expected_output' => "3\n36", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n50",  'expected_output' => "4\n45", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n20",  'expected_output' => "1\n18", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Chi-square statistic ─────────────────────────────────────
        $seed(35, [
            ['input' => "3\n10\n12\n20\n18\n15\n15", 'expected_output' => '0.6111', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5\n5\n5\n5",              'expected_output' => '0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n10\n5\n5\n10",            'expected_output' => '5.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n15\n20\n25\n20\n20\n20\n20\n20",'expected_output' => '2.5000','is_hidden' => true,'order_index' => 4],
        ]);

        // ── Q36: Chi-square GOF df ────────────────────────────────────────
        $seed(36, [
            ['input' => '5',  'expected_output' => '4', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',  'expected_output' => '2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '10', 'expected_output' => '9', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '2',  'expected_output' => '1', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Contingency table df ─────────────────────────────────────
        $seed(37, [
            ['input' => "3\n4",  'expected_output' => '6', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2",  'expected_output' => '1', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n3",  'expected_output' => '6', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n2",  'expected_output' => '4', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Median (non-parametric) ──────────────────────────────────
        $seed(38, [
            ['input' => "9\n3\n7\n1\n5",   'expected_output' => '5', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n2\n8\n4\n6",  'expected_output' => '6', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1\n1\n1\n2",   'expected_output' => '1', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "100\n50\n75\n25\n60",'expected_output' => '60','is_hidden' => true,'order_index' => 4],
        ]);

        // ── Q39: Ranks ────────────────────────────────────────────────────
        $seed(39, [
            ['input' => "40\n10\n30\n20\n50\n60",   'expected_output' => "4\n1\n3\n2\n5\n6", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2\n3\n4\n5\n6",         'expected_output' => "1\n2\n3\n4\n5\n6", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n5\n4\n3\n2\n1",         'expected_output' => "6\n5\n4\n3\n2\n1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "15\n5\n25\n10\n30\n20",    'expected_output' => "3\n1\n5\n2\n6\n4", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Regression slope ─────────────────────────────────────────
        $seed(40, [
            ['input' => "5.0\n10.0\n40.0\n20.0",  'expected_output' => '2.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3.0\n6.0\n15.0\n10.0",   'expected_output' => '1.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0\n20.0\n100.0\n50.0", 'expected_output' => '2.0000','is_hidden' => true,  'order_index' => 3],
            ['input' => "4.0\n8.0\n0.0\n20.0",    'expected_output' => '0.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: y-intercept ─────────────────────────────────────────────
        $seed(41, [
            ['input' => "2.0\n5.0\n10.0",   'expected_output' => '0.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.5\n4.0\n8.0",    'expected_output' => '2.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0\n2.0\n10.0",   'expected_output' => '4.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n10.0\n20.0",  'expected_output' => '15.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Predicted y ─────────────────────────────────────────────
        $seed(42, [
            ['input' => "1.0\n2.0\n4.0",   'expected_output' => '9.00',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n3.0\n5.0",   'expected_output' => '15.00', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n1.5\n10.0",  'expected_output' => '17.00', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "5.0\n0.5\n0.0",   'expected_output' => '5.00',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Correlation strength ─────────────────────────────────────
        $seed(43, [
            ['input' => '-0.85', 'expected_output' => 'Strong',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.55',  'expected_output' => 'Moderate', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.2',   'expected_output' => 'Weak',     'is_hidden' => true,  'order_index' => 3],
            ['input' => '-0.7',  'expected_output' => 'Strong',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: MAE ─────────────────────────────────────────────────────
        $seed(44, [
            ['input' => "10\n12\n20\n18\n30\n33",  'expected_output' => '2.3333', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n5\n10\n10\n15\n15",    'expected_output' => '0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n90\n50\n60\n200\n190",'expected_output' => '10.0000','is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n3\n2\n5\n3\n7",        'expected_output' => '3.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Total experimental units ─────────────────────────────────
        $seed(45, [
            ['input' => "4\n5",   'expected_output' => '20', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10",  'expected_output' => '30', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n6",   'expected_output' => '30', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n25",  'expected_output' => '50', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: RCBD df_error ────────────────────────────────────────────
        $seed(46, [
            ['input' => "4\n5",   'expected_output' => '12', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n6",   'expected_output' => '10', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n4",   'expected_output' => '12', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10",  'expected_output' => '9',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: CRD degrees of freedom ───────────────────────────────────
        $seed(47, [
            ['input' => "3\n30",  'expected_output' => "2\n27\n29", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n40",  'expected_output' => "3\n36\n39", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n50",  'expected_output' => "4\n45\n49", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n20",  'expected_output' => "1\n18\n19", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Cohen's d ────────────────────────────────────────────────
        $seed(48, [
            ['input' => "55\n50\n10",  'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "100\n90\n20", 'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "80\n60\n10",  'expected_output' => '2.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "50\n50\n5",   'expected_output' => '0.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Effect size classification ──────────────────────────────
        $seed(49, [
            ['input' => '-0.6',  'expected_output' => 'Medium',     'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.1',   'expected_output' => 'Negligible', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.9',   'expected_output' => 'Large',      'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.35',  'expected_output' => 'Small',      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Sample size planning ─────────────────────────────────────
        $seed(50, [
            ['input' => "0.5\n0.05",  'expected_output' => '64',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.8\n0.05",  'expected_output' => '26',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.2\n0.05",  'expected_output' => '394', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n0.05",  'expected_output' => '17',  'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 8 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}