<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 2 — Statistics for Data Science (Newbie) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering beginner statistics in Python
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mirrors Module 2 curriculum):
 *   2.1  Types of data (categorical vs numerical, discrete vs continuous)
 *   2.2  Measures of Central Tendency: Mean, Median, Mode
 *   2.3  Measures of Variability: Range, Variance, SD, IQR
 *   2.4  Probability Fundamentals & Bayes' Theorem
 *   2.5  Probability Distributions: Normal & Binomial
 *   2.6  Correlation & Covariance
 *   2.7  Simple Linear Regression & R-Squared
 *   2.8  Hypothesis Testing, P-Values & Error Types
 *   2.9  Sampling Methods & Central Limit Theorem
 *   2.10 Building a Statistics Dashboard (summary stats output)
 *
 * Difficulty: Newbie — straightforward formulas, no advanced libraries,
 *             use only Python's `math`, `statistics`, and basic built-ins.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module2CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 2 — Statistics for Data Science (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Statistics for Data Science',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply fundamental statistics concepts in Python. Compute measures of central tendency, variability, probability, correlation, and regression using only Python\'s built-in tools and the math/statistics modules.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 500,
                'order_index'        => 2,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.1: Types of Data (Q1–Q4)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Classify a list of values as `categorical` or `numerical`.

Rules:
- If every value can be converted to a float → `numerical`
- Otherwise → `categorical`

Read `n` values (one per line). Print `numerical` or `categorical`.

Example:
```
Input:
3
10
20
30
Output: numerical
```
```
Input:
3
red
blue
green
Output: categorical
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [input() for _ in range(n)]\n# Classify and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Classify numerical data as `discrete` or `continuous`.

Rules:
- If all values are whole numbers (no decimal part) → `discrete`
- Otherwise → `continuous`

Read `n` numbers (one per line). Print `discrete` or `continuous`.

Example:
```
Input:
3
1
2
3
Output: discrete
```
```
Input:
3
1.5
2.3
3.7
Output: continuous
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Classify and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Count the frequency of each unique value in a dataset and print them sorted alphabetically (or numerically for numeric data). Format: `value: count`.

Read `n` values (one per line, treated as strings).

Example:
```
Input:
5
cat
dog
cat
fish
dog
Output:
cat: 2
dog: 2
fish: 1
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [input() for _ in range(n)]\n# Count and print frequencies\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Given a list of numbers, identify the **unique values** (sorted ascending) and print the count of unique values.

Read `n` numbers (one per line). Print the sorted unique values space-separated, then the count on the next line.

Example:
```
Input:
6
3
1
2
2
3
4
Output:
1 2 3 4
4
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Print unique sorted values and count\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.2: Measures of Central Tendency (Q5–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Compute the **mean** (arithmetic average) of a list of numbers.

Read `n` numbers (one per line). Print the mean rounded to 2 decimal places.

Example:
```
Input:
5
10
20
30
40
50
Output: 30.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Compute and print mean\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Compute the **median** of a list of numbers **without** using the `statistics` module.

Read `n` numbers (one per line). Print the median rounded to 2 decimal places.

Example:
```
Input:
5
3
1
4
1
5
Output: 3.0
```
```
Input:
4
1
2
3
4
Output: 2.5
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Compute median manually and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Compute the **mode** (most frequent value) of a list of numbers. If there are multiple modes, print them all in ascending order on one line separated by spaces.

Read `n` numbers (one per line).

Example:
```
Input:
6
1
2
2
3
3
4
Output: 2 3
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Find and print mode(s)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Compute the **weighted mean** of a dataset.

Read `n` values and weights (each pair on its own line as `value weight`). Print the weighted mean rounded to 2 decimal places.

Weighted mean = Σ(value × weight) / Σ(weight)

Example:
```
Input:
3
10 1
20 2
30 3
Output: 23.33
```
MD,
                'starter_code'        => "n = int(input())\npairs = []\nfor _ in range(n):\n    v, w = map(float, input().split())\n    pairs.append((v, w))\n# Compute weighted mean\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Compute the **geometric mean** of a list of positive numbers.

Geometric mean = (x1 × x2 × ... × xn)^(1/n)

Read `n` numbers (one per line). Print the geometric mean rounded to 4 decimal places.

Example:
```
Input:
3
1
4
16
Output: 4.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nnums = [float(input()) for _ in range(n)]\n# Compute geometric mean\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Given a dataset, print the mean, median, and mode on separate lines (each rounded to 2 decimal places). For mode, if multiple, print them space-separated on one line.

Read `n` integers (one per line).

Example:
```
Input:
5
1
2
2
3
4
Output:
2.4
2.0
2
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Print mean, median, mode\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.3: Measures of Variability (Q11–Q17)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Compute the **range** of a dataset (max − min).

Read `n` numbers (one per line). Print the range.

Example:
```
Input:
5
4
8
2
9
1
Output: 8
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\nprint(max(nums) - min(nums))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Compute the **population variance** of a dataset.

Population variance = Σ(xi − mean)² / n

Read `n` numbers (one per line). Print the variance rounded to 4 decimal places.

Example:
```
Input:
4
2
4
4
4
Output: 1.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Compute population variance\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Compute the **sample variance** of a dataset.

Sample variance = Σ(xi − mean)² / (n − 1)

Read `n` numbers (one per line). Print the sample variance rounded to 4 decimal places.

Example:
```
Input:
4
2
4
4
4
Output: 1.3333
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Compute sample variance (ddof=1)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Compute the **population standard deviation** of a dataset.

Standard deviation = √(population variance)

Read `n` numbers (one per line). Print the standard deviation rounded to 4 decimal places.

Example:
```
Input:
5
2
4
4
4
5
Output: 0.8944
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nnums = [float(input()) for _ in range(n)]\n# Compute population SD\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Compute the **Interquartile Range (IQR)** of a dataset.

IQR = Q3 − Q1

where Q1 is the 25th percentile and Q3 is the 75th percentile. Use the exclusive method:
- Q1 = median of the lower half (values below the median)
- Q3 = median of the upper half (values above the median)

Read `n` numbers (one per line). Print the IQR rounded to 2 decimal places.

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
8
Output: 4.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = sorted([float(input()) for _ in range(n)])\n# Compute IQR\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Identify **outliers** in a dataset using the IQR method. A value is an outlier if it is below `Q1 − 1.5 × IQR` or above `Q3 + 1.5 × IQR`.

Read `n` numbers (one per line). Print the outliers sorted in ascending order, one per line. If none, print `None`.

Example:
```
Input:
6
1
2
3
4
5
100
Output: 100
```
MD,
                'starter_code'        => "n = int(input())\nnums = sorted([float(input()) for _ in range(n)])\n# Find and print outliers using IQR method\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Compute the **coefficient of variation (CV)** of a dataset.

CV = (standard deviation / mean) × 100

Read `n` numbers (one per line). Print the CV rounded to 2 decimal places followed by `%`.

Example:
```
Input:
4
10
20
30
40
Output: 51.64%
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nnums = [float(input()) for _ in range(n)]\n# Compute CV\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.4: Probability Fundamentals & Bayes' Theorem (Q18–Q23)
            // ═══════════════════════════════════════════════════════════════

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Compute a simple probability. Given the number of favourable outcomes and total outcomes, print the probability rounded to 4 decimal places.

Read two integers: `favourable` and `total`.

Example:
```
Input:
3
6
Output: 0.5
```
MD,
                'starter_code'        => "favourable = int(input())\ntotal = int(input())\nprint(round(favourable / total, 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Compute the probability of two **independent** events both occurring.

P(A and B) = P(A) × P(B)

Read two probabilities (floats between 0 and 1). Print the result rounded to 4 decimal places.

Example:
```
Input:
0.5
0.3
Output: 0.15
```
MD,
                'starter_code'        => "pa = float(input())\npb = float(input())\nprint(round(pa * pb, 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Compute the probability of event A **or** event B occurring (union) for mutually exclusive events.

P(A or B) = P(A) + P(B)

And for non-mutually exclusive events:

P(A or B) = P(A) + P(B) − P(A and B)

Read three values: `P(A)`, `P(B)`, and `P(A and B)`. Print `P(A or B)` rounded to 4 decimal places.

Example:
```
Input:
0.4
0.3
0.1
Output: 0.6
```
MD,
                'starter_code'        => "pa = float(input())\npb = float(input())\npab = float(input())\nprint(round(pa + pb - pab, 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Compute the **complement** of an event.

P(not A) = 1 − P(A)

Read `P(A)`. Print `P(not A)` rounded to 4 decimal places.

Example:
```
Input: 0.7
Output: 0.3
```
MD,
                'starter_code'        => "pa = float(input())\nprint(round(1 - pa, 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Apply **Bayes' Theorem**:

P(A|B) = P(B|A) × P(A) / P(B)

Read three values: `P(B|A)`, `P(A)`, and `P(B)`. Print `P(A|B)` rounded to 4 decimal places.

Example:
```
Input:
0.9
0.01
0.05
Output: 0.18
```
MD,
                'starter_code'        => "p_b_given_a = float(input())\np_a = float(input())\np_b = float(input())\n# Apply Bayes' theorem\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Simulate rolling a fair **6-sided die** `n` times and print the **theoretical probability** of each face (1–6), formatted as `face: probability` (4 decimal places).

The theoretical probability of any face is 1/6.

Read `n` (any positive integer; not used in calculation — just echo it). Print 6 lines.

Example:
```
Input: 1000
Output:
1: 0.1667
2: 0.1667
3: 0.1667
4: 0.1667
5: 0.1667
6: 0.1667
```
MD,
                'starter_code'        => "n = int(input())\nfor face in range(1, 7):\n    print(f'{face}: {1/6:.4f}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.5: Probability Distributions (Q24–Q29)
            // ═══════════════════════════════════════════════════════════════

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Compute the **binomial coefficient** C(n, k) = n! / (k! × (n−k)!).

Read `n` and `k`. Print C(n, k).

Example:
```
Input:
5
2
Output: 10
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nk = int(input())\nprint(math.comb(n, k))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Compute the **binomial probability** P(X = k) for a binomial distribution.

P(X = k) = C(n, k) × p^k × (1−p)^(n−k)

Read `n` (trials), `k` (successes), and `p` (probability of success). Print the probability rounded to 6 decimal places.

Example:
```
Input:
10
3
0.5
Output: 0.117188
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nk = int(input())\np = float(input())\n# Compute binomial probability\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Compute the **mean** and **variance** of a binomial distribution.

Mean = n × p
Variance = n × p × (1 − p)

Read `n` and `p`. Print the mean and variance each rounded to 4 decimal places on separate lines.

Example:
```
Input:
10
0.3
Output:
3.0
2.1
```
MD,
                'starter_code'        => "n = int(input())\np = float(input())\n# Compute and print mean and variance\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Compute the **z-score** for a value in a normally distributed dataset.

z = (x − mean) / standard_deviation

Read `x`, `mean`, and `standard_deviation`. Print the z-score rounded to 4 decimal places.

Example:
```
Input:
85
75
10
Output: 1.0
```
MD,
                'starter_code'        => "x = float(input())\nmean = float(input())\nsd = float(input())\nprint(round((x - mean) / sd, 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Compute the **68-95-99.7 rule** ranges for a normal distribution.

Given mean (μ) and standard deviation (σ), print:
- 1σ range: `[μ − σ, μ + σ]`
- 2σ range: `[μ − 2σ, μ + 2σ]`
- 3σ range: `[μ − 3σ, μ + 3σ]`

Read `mean` and `sd`. Print each range on its own line rounded to 2 decimal places.

Example:
```
Input:
100
15
Output:
[85.0, 115.0]
[70.0, 130.0]
[55.0, 145.0]
```
MD,
                'starter_code'        => "mean = float(input())\nsd = float(input())\nfor i in range(1, 4):\n    lo = round(mean - i * sd, 2)\n    hi = round(mean + i * sd, 2)\n    print(f'[{lo}, {hi}]')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Given a list of z-scores, classify each as:
- `unlikely` if |z| > 2
- `common` otherwise

Read `n` z-scores (floats, one per line). Print `unlikely` or `common` for each.

Example:
```
Input:
4
0.5
-1.0
2.5
-3.0
Output:
common
common
unlikely
unlikely
```
MD,
                'starter_code'        => "n = int(input())\nfor _ in range(n):\n    z = float(input())\n    print('unlikely' if abs(z) > 2 else 'common')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.6: Correlation & Covariance (Q30–Q34)
            // ═══════════════════════════════════════════════════════════════

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Compute the **covariance** between two datasets X and Y.

Population covariance = Σ((xi − x̄)(yi − ȳ)) / n

Read `n`, then `n` x values (one per line), then `n` y values (one per line). Print the covariance rounded to 4 decimal places.

Example:
```
Input:
3
1
2
3
4
5
6
Output: 0.6667
```

Note: Use population covariance (divide by n).
MD,
                'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n# Compute population covariance\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Compute the **Pearson correlation coefficient** between two datasets.

r = Σ((xi − x̄)(yi − ȳ)) / √(Σ(xi − x̄)² × Σ(yi − ȳ)²)

Read `n`, then `n` x values, then `n` y values. Print `r` rounded to 4 decimal places.

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
Output: 1.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n# Compute Pearson r\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Interpret a Pearson correlation coefficient `r` and print the relationship type:

- `strong positive` if r ≥ 0.7
- `moderate positive` if 0.3 ≤ r < 0.7
- `weak positive` if 0 < r < 0.3
- `no correlation` if r == 0
- `weak negative` if -0.3 < r < 0
- `moderate negative` if -0.7 < r ≤ -0.3
- `strong negative` if r ≤ -0.7

Read `r`. Print the relationship type.

Example:
```
Input: 0.85
Output: strong positive
```
MD,
                'starter_code'        => "r = float(input())\n# Classify correlation\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Compute the **Spearman rank correlation** between two datasets.

Steps:
1. Rank each dataset (use average rank for ties)
2. Compute d = rank(xi) − rank(yi) for each pair
3. rs = 1 − (6 × Σd²) / (n × (n² − 1))

Read `n`, then `n` x values, then `n` y values. Print `rs` rounded to 4 decimal places.

Example:
```
Input:
5
1
2
3
4
5
5
4
3
2
1
Output: -1.0
```
MD,
                'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n\ndef rank(data):\n    sorted_data = sorted(enumerate(data), key=lambda p: p[1])\n    ranks = [0] * len(data)\n    i = 0\n    while i < len(sorted_data):\n        j = i\n        while j < len(sorted_data) - 1 and sorted_data[j+1][1] == sorted_data[i][1]:\n            j += 1\n        avg_rank = (i + j) / 2 + 1\n        for k in range(i, j + 1):\n            ranks[sorted_data[k][0]] = avg_rank\n        i = j + 1\n    return ranks\n\n# Compute Spearman rs\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Determine whether two variables are **positively correlated**, **negatively correlated**, or **uncorrelated** based on their covariance.

- covariance > 0 → `positive`
- covariance < 0 → `negative`
- covariance == 0 → `uncorrelated`

Read `n`, then `n` x values, then `n` y values. Compute population covariance and print the direction.

Example:
```
Input:
3
1
2
3
3
2
1
Output: negative
```
MD,
                'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n# Compute covariance and print direction\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.7: Simple Linear Regression & R-Squared (Q35–Q39)
            // ═══════════════════════════════════════════════════════════════

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Compute the **slope (m)** and **intercept (b)** of the simple linear regression line y = mx + b.

m = Σ((xi − x̄)(yi − ȳ)) / Σ((xi − x̄)²)
b = ȳ − m × x̄

Read `n`, then `n` x values, then `n` y values. Print `m` and `b` each rounded to 4 decimal places on separate lines.

Example:
```
Input:
3
1
2
3
2
4
6
Output:
2.0
0.0
```
MD,
                'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n# Compute slope m and intercept b\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Given a linear regression model (slope and intercept), **predict** the y value for a given x.

y = m × x + b

Read `m`, `b`, then `q` queries (x values). For each x, print the predicted y rounded to 2 decimal places.

Example:
```
Input:
2.0
1.0
3
0
5
10
Output:
1.0
11.0
21.0
```
MD,
                'starter_code'        => "m = float(input())\nb = float(input())\nq = int(input())\nfor _ in range(q):\n    x = float(input())\n    print(round(m * x + b, 2))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Compute the **R-Squared (coefficient of determination)** of a linear regression.

R² = 1 − (SS_res / SS_tot)

where:
- SS_res = Σ(yi − ŷi)²  (residual sum of squares)
- SS_tot = Σ(yi − ȳ)²  (total sum of squares)

Read `n`, then `n` actual y values, then `n` predicted ŷ values. Print R² rounded to 4 decimal places.

Example:
```
Input:
4
3
8
10
17
2.5
7.5
10.5
17.5
Output: 0.9972
```
MD,
                'starter_code'        => "n = int(input())\nactual = [float(input()) for _ in range(n)]\npredicted = [float(input()) for _ in range(n)]\n# Compute R-squared\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Compute the **residuals** of a linear regression (actual − predicted) and print them one per line rounded to 2 decimal places.

Read `n`, then `n` actual y values, then `n` predicted ŷ values.

Example:
```
Input:
3
10
20
30
9.5
21.0
29.5
Output:
0.5
-1.0
0.5
```
MD,
                'starter_code'        => "n = int(input())\nactual = [float(input()) for _ in range(n)]\npredicted = [float(input()) for _ in range(n)]\nfor a, p in zip(actual, predicted):\n    print(round(a - p, 2))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Interpret the R² value:

- R² ≥ 0.9 → `excellent fit`
- 0.7 ≤ R² < 0.9 → `good fit`
- 0.5 ≤ R² < 0.7 → `moderate fit`
- R² < 0.5 → `poor fit`

Read `R²`. Print the interpretation.

Example:
```
Input: 0.92
Output: excellent fit
```
MD,
                'starter_code'        => "r2 = float(input())\n# Classify R-squared\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.8: Hypothesis Testing & Error Types (Q40–Q43)
            // ═══════════════════════════════════════════════════════════════

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Compute the **one-sample z-test statistic**:

z = (x̄ − μ₀) / (σ / √n)

Read `sample_mean`, `population_mean`, `population_sd`, and `n`. Print `z` rounded to 4 decimal places.

Example:
```
Input:
52
50
10
25
Output: 1.0
```
MD,
                'starter_code'        => "import math\n\nsample_mean = float(input())\npop_mean    = float(input())\npop_sd      = float(input())\nn           = int(input())\nz = (sample_mean - pop_mean) / (pop_sd / math.sqrt(n))\nprint(round(z, 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Decide whether to **reject or fail to reject** the null hypothesis based on the z-score and significance level.

A two-tailed test rejects H₀ if |z| > critical_value.

Common critical values: α=0.05 → 1.96, α=0.01 → 2.576, α=0.10 → 1.645

Read `z` (float) and `alpha` (0.05, 0.01, or 0.10). Print `Reject H0` or `Fail to reject H0`.

Example:
```
Input:
2.5
0.05
Output: Reject H0
```
MD,
                'starter_code'        => "z = float(input())\nalpha = float(input())\ncritical = {0.05: 1.96, 0.01: 2.576, 0.10: 1.645}\ncv = critical[alpha]\nprint('Reject H0' if abs(z) > cv else 'Fail to reject H0')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Classify a statistical error.

- **Type I error (false positive)**: rejecting a true null hypothesis
- **Type II error (false negative)**: failing to reject a false null hypothesis

Read two lines:
- `H0`: `true` or `false` (is the null hypothesis actually true?)
- `decision`: `reject` or `fail to reject`

Print `Type I error`, `Type II error`, `Correct rejection`, or `Correct acceptance`.

Example:
```
Input:
true
reject
Output: Type I error
```
MD,
                'starter_code'        => "h0_true = input().strip().lower() == 'true'\ndecision = input().strip().lower()\n# Classify the error type\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Compute the **margin of error** for a confidence interval.

Margin of Error = z* × (σ / √n)

Common z* values: 90% → 1.645, 95% → 1.96, 99% → 2.576

Read `confidence_level` (90, 95, or 99), `sigma` (population SD), and `n` (sample size). Print the margin of error rounded to 4 decimal places.

Example:
```
Input:
95
15
100
Output: 2.94
```
MD,
                'starter_code'        => "import math\n\ncl = int(input())\nsigma = float(input())\nn = int(input())\nz_star = {90: 1.645, 95: 1.96, 99: 2.576}\nme = z_star[cl] * (sigma / math.sqrt(n))\nprint(round(me, 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.9: Sampling & Central Limit Theorem (Q44–Q47)
            // ═══════════════════════════════════════════════════════════════

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Perform **systematic sampling**: select every k-th element from a list starting from index 0.

Read `n` values (one per line), then `k`. Print the sampled values one per line.

Example:
```
Input:
8
10
20
30
40
50
60
70
80
3
Output:
10
40
70
```
MD,
                'starter_code'        => "n = int(input())\ndata = [input() for _ in range(n)]\nk = int(input())\nfor i in range(0, n, k):\n    print(data[i])\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Compute the **standard error of the mean (SEM)**.

SEM = σ / √n

Read `sigma` (population SD) and `n` (sample size). Print the SEM rounded to 4 decimal places.

Example:
```
Input:
20
100
Output: 2.0
```
MD,
                'starter_code'        => "import math\n\nsigma = float(input())\nn = int(input())\nprint(round(sigma / math.sqrt(n), 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
According to the **Central Limit Theorem**, the sampling distribution of the mean approaches a normal distribution as `n` increases, with:

- Mean = population mean (μ)
- SD of sampling distribution = σ / √n (SEM)

Read `mu`, `sigma`, and `n`. Print the mean and SEM of the sampling distribution, each rounded to 4 decimal places on separate lines.

Example:
```
Input:
100
20
64
Output:
100.0
2.5
```
MD,
                'starter_code'        => "import math\n\nmu = float(input())\nsigma = float(input())\nn = int(input())\nprint(round(mu, 4))\nprint(round(sigma / math.sqrt(n), 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Determine the **minimum sample size** needed to achieve a given margin of error at 95% confidence (z* = 1.96).

n ≥ (z* × σ / E)²

Round **up** to the nearest integer.

Read `sigma` and `E` (margin of error). Print the minimum sample size.

Example:
```
Input:
15
3
Output: 97
```
MD,
                'starter_code'        => "import math\n\nsigma = float(input())\nE = float(input())\nn = (1.96 * sigma / E) ** 2\nprint(math.ceil(n))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.10: Statistics Dashboard (Q48–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Print a **basic statistics summary** for a dataset. Output the following (in order):
- `Count: n`
- `Min: X`
- `Max: X`
- `Range: X`
- `Mean: X` (2 decimal places)
- `Median: X` (2 decimal places)
- `Std Dev: X` (4 decimal places, population)

Read `n` numbers (one per line).

Example:
```
Input:
5
2
4
4
4
5
Output:
Count: 5
Min: 2.0
Max: 5.0
Range: 3.0
Mean: 3.8
Median: 4.0
Std Dev: 0.9798
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nnums = sorted([float(input()) for _ in range(n)])\n# Print statistics summary\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Print a **five-number summary** for a dataset:
- `Min: X`
- `Q1: X`
- `Median: X`
- `Q3: X`
- `Max: X`

Use the exclusive quartile method (Q1 = median of lower half, Q3 = median of upper half). Round all to 2 decimal places.

Read `n` numbers (one per line).

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
8
Output:
Min: 1.0
Q1: 2.5
Median: 4.5
Q3: 6.5
Max: 8.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = sorted([float(input()) for _ in range(n)])\n# Compute and print five-number summary\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Print a **correlation summary** between two datasets.

Output:
- `Covariance: X` (4 decimal places, population)
- `Pearson r: X` (4 decimal places)
- `Relationship: TYPE` (use the same interpretation as Q32: strong/moderate/weak positive/negative/no correlation)

Read `n`, then `n` x values, then `n` y values.

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
Output:
Covariance: 1.25
Pearson r: 1.0
Relationship: strong positive
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n# Compute covariance, Pearson r, and interpret\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
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

        // ── Q1: categorical vs numerical ─────────────────────────────────
        $seed(1, [
            ['input' => "3\n10\n20\n30",           'expected_output' => "numerical",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nred\nblue\ngreen",      'expected_output' => "categorical",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1.5\n2.5",             'expected_output' => "numerical",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nhello\nworld",          'expected_output' => "categorical",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: discrete vs continuous ────────────────────────────────────
        $seed(2, [
            ['input' => "3\n1\n2\n3",              'expected_output' => "discrete",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1.5\n2.3\n3.7",        'expected_output' => "continuous",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n20\n30\n40",       'expected_output' => "discrete",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0.1\n0.2",             'expected_output' => "continuous",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: frequency count ───────────────────────────────────────────
        $seed(3, [
            ['input' => "5\ncat\ndog\ncat\nfish\ndog",   'expected_output' => "cat: 2\ndog: 2\nfish: 1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\na\nb\nc",                    'expected_output' => "a: 1\nb: 1\nc: 1",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nx\nx\nx\ny",                'expected_output' => "x: 3\ny: 1",                 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nhello",                      'expected_output' => "hello: 1",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: unique values ─────────────────────────────────────────────
        $seed(4, [
            ['input' => "6\n3\n1\n2\n2\n3\n4",    'expected_output' => "1 2 3 4\n4",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5",             'expected_output' => "5\n1",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4",          'expected_output' => "1 2 3 4\n4",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n7",                   'expected_output' => "7\n1",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: mean ──────────────────────────────────────────────────────
        $seed(5, [
            ['input' => "5\n10\n20\n30\n40\n50",   'expected_output' => "30.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",              'expected_output' => "2.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n2\n4\n6\n8",           'expected_output' => "5.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n7\n3",                 'expected_output' => "5.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: median ────────────────────────────────────────────────────
        $seed(6, [
            ['input' => "5\n3\n1\n4\n1\n5",   'expected_output' => "3.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4",      'expected_output' => "2.5",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n9",               'expected_output' => "9.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n6\n5\n4\n3\n2\n1",'expected_output' => "3.5",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: mode ──────────────────────────────────────────────────────
        $seed(7, [
            ['input' => "6\n1\n2\n2\n3\n3\n4",   'expected_output' => "2 3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n1\n2\n3",         'expected_output' => "1",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5\n5\n5",            'expected_output' => "5",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4",         'expected_output' => "1 2 3 4",'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q8: weighted mean ─────────────────────────────────────────────
        $seed(8, [
            ['input' => "3\n10 1\n20 2\n30 3",   'expected_output' => "23.33",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 1\n100 1",          'expected_output' => "50.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n10 3\n20 1",          'expected_output' => "12.5",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n7 4",                 'expected_output' => "7.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: geometric mean ────────────────────────────────────────────
        $seed(9, [
            ['input' => "3\n1\n4\n16",   'expected_output' => "4.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n4\n9",       'expected_output' => "6.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2\n8\n32",   'expected_output' => "8.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n100",        'expected_output' => "100.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: mean/median/mode ─────────────────────────────────────────
        $seed(10, [
            ['input' => "5\n1\n2\n2\n3\n4",   'expected_output' => "2.4\n2.0\n2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n1\n1",         'expected_output' => "1.0\n1.0\n1",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n2\n4\n4\n6",      'expected_output' => "4.0\n4.0\n4",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n3\n7",            'expected_output' => "5.0\n5.0\n3 7", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: range ────────────────────────────────────────────────────
        $seed(11, [
            ['input' => "5\n4\n8\n2\n9\n1",   'expected_output' => "8.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n1\n1",         'expected_output' => "0.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n20\n30\n40",  'expected_output' => "30.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n5\n15",           'expected_output' => "10.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: population variance ──────────────────────────────────────
        $seed(12, [
            ['input' => "4\n2\n4\n4\n4",       'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",          'expected_output' => "0.6667", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n10\n10\n10",   'expected_output' => "0.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0\n10",            'expected_output' => "25.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: sample variance ──────────────────────────────────────────
        $seed(13, [
            ['input' => "4\n2\n4\n4\n4",   'expected_output' => "1.3333",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",      'expected_output' => "1.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0\n10",        'expected_output' => "50.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n2\n4\n4\n4\n5",'expected_output' => "1.3",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: population SD ────────────────────────────────────────────
        $seed(14, [
            ['input' => "5\n2\n4\n4\n4\n5",   'expected_output' => "0.8944",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",         'expected_output' => "0.8165",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n10\n10\n10",  'expected_output' => "0.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0\n10",           'expected_output' => "5.0",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: IQR ──────────────────────────────────────────────────────
        $seed(15, [
            ['input' => "8\n1\n2\n3\n4\n5\n6\n7\n8",   'expected_output' => "4.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4",               'expected_output' => "2.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1\n3\n5\n7\n9\n11",        'expected_output' => "6.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n2\n4\n6\n8\n10",           'expected_output' => "6.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: outliers ─────────────────────────────────────────────────
        $seed(16, [
            ['input' => "6\n1\n2\n3\n4\n5\n100",       'expected_output' => "100",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4",               'expected_output' => "None",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n-100\n1\n2\n3\n4\n5",      'expected_output' => "-100",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n10\n12\n11\n13\n50",       'expected_output' => "50",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: coefficient of variation ────────────────────────────────
        $seed(17, [
            ['input' => "4\n10\n20\n30\n40",   'expected_output' => "51.64%",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5",          'expected_output' => "0.0%",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4",       'expected_output' => "51.64%",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n100\n200",         'expected_output' => "33.33%",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: simple probability ───────────────────────────────────────
        $seed(18, [
            ['input' => "3\n6",    'expected_output' => "0.5",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n4",    'expected_output' => "0.25",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n3",    'expected_output' => "0.6667", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n52",   'expected_output' => "0.0192", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: independent events ───────────────────────────────────────
        $seed(19, [
            ['input' => "0.5\n0.3",   'expected_output' => "0.15",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n1.0",   'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.4\n0.25",  'expected_output' => "0.1",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.6\n0.6",   'expected_output' => "0.36",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: union probability ────────────────────────────────────────
        $seed(20, [
            ['input' => "0.4\n0.3\n0.1",   'expected_output' => "0.6",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n0.5\n0.0",   'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.3\n0.4\n0.12",  'expected_output' => "0.58",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.2\n0.3\n0.06",  'expected_output' => "0.44",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: complement ───────────────────────────────────────────────
        $seed(21, [
            ['input' => "0.7",    'expected_output' => "0.3",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0",    'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.25",   'expected_output' => "0.75",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0",    'expected_output' => "0.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Bayes' theorem ───────────────────────────────────────────
        $seed(22, [
            ['input' => "0.9\n0.01\n0.05",    'expected_output' => "0.18",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n0.5\n0.5",      'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.8\n0.1\n0.4",      'expected_output' => "0.2",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.95\n0.02\n0.1",    'expected_output' => "0.19",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: die probability ──────────────────────────────────────────
        $seed(23, [
            ['input' => "1000",   'expected_output' => "1: 0.1667\n2: 0.1667\n3: 0.1667\n4: 0.1667\n5: 0.1667\n6: 0.1667",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1",      'expected_output' => "1: 0.1667\n2: 0.1667\n3: 0.1667\n4: 0.1667\n5: 0.1667\n6: 0.1667",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "500",    'expected_output' => "1: 0.1667\n2: 0.1667\n3: 0.1667\n4: 0.1667\n5: 0.1667\n6: 0.1667",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "999",    'expected_output' => "1: 0.1667\n2: 0.1667\n3: 0.1667\n4: 0.1667\n5: 0.1667\n6: 0.1667",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: binomial coefficient ─────────────────────────────────────
        $seed(24, [
            ['input' => "5\n2",    'expected_output' => "10",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0",    'expected_output' => "1",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "10\n3",   'expected_output' => "120",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n6",    'expected_output' => "1",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: binomial probability ─────────────────────────────────────
        $seed(25, [
            ['input' => "10\n3\n0.5",   'expected_output' => "0.117188",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1\n0.5",    'expected_output' => "0.5",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n1.0",    'expected_output' => "1.0",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2\n0.3",    'expected_output' => "0.2646",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: binomial mean & variance ─────────────────────────────────
        $seed(26, [
            ['input' => "10\n0.3",   'expected_output' => "3.0\n2.1",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "20\n0.5",   'expected_output' => "10.0\n5.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1.0",    'expected_output' => "5.0\n0.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "100\n0.25", 'expected_output' => "25.0\n18.75",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: z-score ──────────────────────────────────────────────────
        $seed(27, [
            ['input' => "85\n75\n10",   'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "75\n75\n10",   'expected_output' => "0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "60\n75\n10",   'expected_output' => "-1.5",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "90\n80\n5",    'expected_output' => "2.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: 68-95-99.7 rule ──────────────────────────────────────────
        $seed(28, [
            ['input' => "100\n15",   'expected_output' => "[85.0, 115.0]\n[70.0, 130.0]\n[55.0, 145.0]",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n1",      'expected_output' => "[-1.0, 1.0]\n[-2.0, 2.0]\n[-3.0, 3.0]",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "50\n10",    'expected_output' => "[40.0, 60.0]\n[30.0, 70.0]\n[20.0, 80.0]",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "200\n25",   'expected_output' => "[175.0, 225.0]\n[150.0, 250.0]\n[125.0, 275.0]",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: z-score classify ─────────────────────────────────────────
        $seed(29, [
            ['input' => "4\n0.5\n-1.0\n2.5\n-3.0",   'expected_output' => "common\ncommon\nunlikely\nunlikely",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0.0",                      'expected_output' => "common",                               'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n2.0\n-2.0",               'expected_output' => "common\ncommon",                       'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n2.01\n-2.01",             'expected_output' => "unlikely\nunlikely",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: covariance ───────────────────────────────────────────────
        $seed(30, [
            ['input' => "3\n1\n2\n3\n4\n5\n6",     'expected_output' => "0.6667",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n3\n2\n1",     'expected_output' => "-0.6667",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n2\n1\n2",           'expected_output' => "0.25",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4\n10\n10\n10\n10", 'expected_output' => "0.0",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Pearson r ────────────────────────────────────────────────
        $seed(31, [
            ['input' => "4\n1\n2\n3\n4\n2\n4\n6\n8",   'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n3\n2\n1",         'expected_output' => "-1.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4\n1\n2\n3\n4",   'expected_output' => "1.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2\n3\n4\n4\n4",         'expected_output' => "0.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: correlation classification ──────────────────────────────
        $seed(32, [
            ['input' => "0.85",    'expected_output' => "strong positive",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "-0.9",    'expected_output' => "strong negative",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5",     'expected_output' => "moderate positive",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0",     'expected_output' => "no correlation",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Spearman rank ────────────────────────────────────────────
        $seed(33, [
            ['input' => "5\n1\n2\n3\n4\n5\n5\n4\n3\n2\n1",   'expected_output' => "-1.0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n1\n2\n3",               'expected_output' => "1.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4\n4\n3\n2\n1",         'expected_output' => "-1.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4\n1\n2\n3\n4",         'expected_output' => "1.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: covariance direction ─────────────────────────────────────
        $seed(34, [
            ['input' => "3\n1\n2\n3\n3\n2\n1",   'expected_output' => "negative",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n1\n2\n3",   'expected_output' => "positive",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3\n5\n5\n5",   'expected_output' => "uncorrelated", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4\n2\n4\n6\n8", 'expected_output' => "positive", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: linear regression slope & intercept ─────────────────────
        $seed(35, [
            ['input' => "3\n1\n2\n3\n2\n4\n6",    'expected_output' => "2.0\n0.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0\n1\n0\n1",          'expected_output' => "1.0\n0.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4\n2\n3\n4\n5", 'expected_output' => "1.0\n1.0",'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n1\n2\n3\n3\n2\n1",   'expected_output' => "-1.0\n4.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: predict ──────────────────────────────────────────────────
        $seed(36, [
            ['input' => "2.0\n1.0\n3\n0\n5\n10",   'expected_output' => "1.0\n11.0\n21.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n5.0\n1\n100",        'expected_output' => "5.0",                'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0\n0.0\n2\n2\n4",       'expected_output' => "6.0\n12.0",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n2.0\n2\n3\n7",       'expected_output' => "5.0\n9.0",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: R-squared ────────────────────────────────────────────────
        $seed(37, [
            ['input' => "4\n3\n8\n10\n17\n2.5\n7.5\n10.5\n17.5",   'expected_output' => "0.9972",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n1\n2\n3",                      'expected_output' => "1.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3\n2\n2\n2",                      'expected_output' => "0.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4\n1.1\n1.9\n3.1\n3.9",       'expected_output' => "0.9933",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: residuals ────────────────────────────────────────────────
        $seed(38, [
            ['input' => "3\n10\n20\n30\n9.5\n21.0\n29.5",   'expected_output' => "0.5\n-1.0\n0.5",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5\n10\n5\n10",                  'expected_output' => "0.0\n0.0",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3\n2\n2\n2",             'expected_output' => "-1.0\n0.0\n1.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n7\n9\n6\n10",                  'expected_output' => "1.0\n-1.0",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: R-squared interpretation ─────────────────────────────────
        $seed(39, [
            ['input' => "0.92",   'expected_output' => "excellent fit",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.75",   'expected_output' => "good fit",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.55",   'expected_output' => "moderate fit",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.30",   'expected_output' => "poor fit",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: z-test statistic ─────────────────────────────────────────
        $seed(40, [
            ['input' => "52\n50\n10\n25",   'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "50\n50\n10\n100",  'expected_output' => "0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "55\n50\n5\n25",    'expected_output' => "5.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "48\n50\n10\n100",  'expected_output' => "-2.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: reject/fail hypothesis ───────────────────────────────────
        $seed(41, [
            ['input' => "2.5\n0.05",   'expected_output' => "Reject H0",            'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n0.05",   'expected_output' => "Fail to reject H0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.6\n0.01",   'expected_output' => "Reject H0",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.6\n0.10",   'expected_output' => "Fail to reject H0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: error type classification ───────────────────────────────
        $seed(42, [
            ['input' => "true\nreject",          'expected_output' => "Type I error",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "false\nfail to reject", 'expected_output' => "Type II error",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "true\nfail to reject",  'expected_output' => "Correct acceptance", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "false\nreject",         'expected_output' => "Correct rejection",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: margin of error ──────────────────────────────────────────
        $seed(43, [
            ['input' => "95\n15\n100",   'expected_output' => "2.94",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "95\n10\n400",   'expected_output' => "0.98",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "99\n20\n64",    'expected_output' => "6.44",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "90\n5\n25",     'expected_output' => "1.645",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: systematic sampling ──────────────────────────────────────
        $seed(44, [
            ['input' => "8\n10\n20\n30\n40\n50\n60\n70\n80\n3",   'expected_output' => "10\n40\n70",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\na\nb\nc\nd\n2",                       'expected_output' => "a\nc",                'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1\n2\n3\n4\n5\n6\n1",                 'expected_output' => "1\n2\n3\n4\n5\n6",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\nA\nB\nC\nD\nE\n5",                    'expected_output' => "A",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: SEM ──────────────────────────────────────────────────────
        $seed(45, [
            ['input' => "20\n100",   'expected_output' => "2.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n25",    'expected_output' => "2.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "15\n9",     'expected_output' => "5.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "30\n100",   'expected_output' => "3.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: CLT sampling distribution ───────────────────────────────
        $seed(46, [
            ['input' => "100\n20\n64",    'expected_output' => "100.0\n2.5",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "50\n10\n100",    'expected_output' => "50.0\n1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n1\n1",        'expected_output' => "0.0\n1.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "200\n30\n36",    'expected_output' => "200.0\n5.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: min sample size ──────────────────────────────────────────
        $seed(47, [
            ['input' => "15\n3",     'expected_output' => "97",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n2",     'expected_output' => "97",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "20\n4",     'expected_output' => "97",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "25\n5",     'expected_output' => "97",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: statistics summary ───────────────────────────────────────
        $seed(48, [
            ['input' => "5\n2\n4\n4\n4\n5",     'expected_output' => "Count: 5\nMin: 2.0\nMax: 5.0\nRange: 3.0\nMean: 3.8\nMedian: 4.0\nStd Dev: 0.9798",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",           'expected_output' => "Count: 3\nMin: 1.0\nMax: 3.0\nRange: 2.0\nMean: 2.0\nMedian: 2.0\nStd Dev: 0.8165",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n42",               'expected_output' => "Count: 1\nMin: 42.0\nMax: 42.0\nRange: 0.0\nMean: 42.0\nMedian: 42.0\nStd Dev: 0.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n20\n30\n40",   'expected_output' => "Count: 4\nMin: 10.0\nMax: 40.0\nRange: 30.0\nMean: 25.0\nMedian: 25.0\nStd Dev: 11.1803",'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q49: five-number summary ──────────────────────────────────────
        $seed(49, [
            ['input' => "8\n1\n2\n3\n4\n5\n6\n7\n8",   'expected_output' => "Min: 1.0\nQ1: 2.5\nMedian: 4.5\nQ3: 6.5\nMax: 8.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4",               'expected_output' => "Min: 1.0\nQ1: 1.5\nMedian: 2.5\nQ3: 3.5\nMax: 4.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n2\n4\n6\n8\n10\n12",       'expected_output' => "Min: 2.0\nQ1: 4.0\nMedian: 7.0\nQ3: 10.0\nMax: 12.0",'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1\n3\n5\n7\n9",            'expected_output' => "Min: 1.0\nQ1: 2.0\nMedian: 5.0\nQ3: 8.0\nMax: 9.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: correlation summary ──────────────────────────────────────
        $seed(50, [
            ['input' => "4\n1\n2\n3\n4\n2\n4\n6\n8",   'expected_output' => "Covariance: 1.25\nPearson r: 1.0\nRelationship: strong positive",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n3\n2\n1",         'expected_output' => "Covariance: -0.6667\nPearson r: -1.0\nRelationship: strong negative", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3\n5\n5\n5",         'expected_output' => "Covariance: 0.0\nPearson r: 0.0\nRelationship: no correlation",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4\n1\n2\n4\n3",  'expected_output' => "Covariance: 0.625\nPearson r: 0.75\nRelationship: moderate positive",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 2 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}