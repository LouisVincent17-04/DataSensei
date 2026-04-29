<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 2 — Statistics for Data Science (Advanced) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Advanced tier
 *   2. coding_questions    — 50 questions covering intermediate/advanced statistics in Python
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mirrors Module 2 curriculum — deeper):
 *   2.1  Data Types & Encoding (one-hot, label encoding, ordinal)
 *   2.2  Advanced Central Tendency (trimmed mean, harmonic mean, midrange)
 *   2.3  Advanced Variability (MAD, z-score normalisation, robust stats)
 *   2.4  Probability (conditional, Bayes with full law of total probability)
 *   2.5  Distributions (Poisson, cumulative binomial, normal CDF approx)
 *   2.6  Correlation (partial, autocorrelation, correlation matrix)
 *   2.7  Multiple / Polynomial Regression (OLS closed-form, RMSE, MAPE)
 *   2.8  Hypothesis Testing (t-test statistic, chi-square, power analysis)
 *   2.9  Resampling & Bootstrap (bootstrap CI, stratified sampling)
 *   2.10 Full EDA Pipeline (complete statistical report from raw data)
 *
 * Difficulty: Advanced — NumPy-style manual implementations, multi-step
 *             algorithms, edge-case awareness; no scipy/sklearn allowed.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module2CodingChallengeSeederAdvanced extends Seeder
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

        $this->command->info('Creating Module 2 — Statistics for Data Science (Advanced) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Statistics for Data Science',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Tackle intermediate and advanced statistical problems in Python. Implement robust measures, multi-variable regression, hypothesis tests, resampling methods, and a full EDA pipeline — using only math, statistics, and pure Python.',
                'time_limit_seconds' => 2700,
                'base_xp'            => 1000,
                'order_index'        => 2,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.1: Data Types & Encoding (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Perform **label encoding** on a list of categorical values.

Assign integer labels based on sorted unique values (0-indexed alphabetically).

Read `n` values (one per line). Print the encoded integer for each value on separate lines.

Example:
```
Input:
4
cat
dog
cat
bird
Output:
1
2
1
0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [input() for _ in range(n)]\n# Label encode (sorted unique → 0-indexed)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Perform **one-hot encoding** on a list of categorical values.

For each value, print a binary vector of length equal to the number of unique categories. Categories are ordered alphabetically.

Read `n` values (one per line). Print one line per value: space-separated 0s and 1s.

Example:
```
Input:
3
cat
dog
cat
Output:
1 0
0 1
1 0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [input() for _ in range(n)]\n# One-hot encode\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
**Min-Max normalise** a list of numbers to the range [0, 1].

Formula: x_norm = (x − min) / (max − min)

Read `n` numbers (one per line). Print each normalised value rounded to 4 decimal places, one per line. If all values are equal, print `0.0` for each.

Example:
```
Input:
4
0
5
10
20
Output:
0.0
0.25
0.5
1.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Min-max normalise\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
**Z-score standardise** a dataset (zero mean, unit variance).

Formula: z = (x − mean) / sd  (use population SD)

Read `n` numbers (one per line). Print each z-score rounded to 4 decimal places. If SD = 0, print `0.0` for each.

Example:
```
Input:
3
2
4
6
Output:
-1.0
0.0
1.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nnums = [float(input()) for _ in range(n)]\n# Z-score standardise\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Detect and count **missing values** in a dataset represented as strings. A missing value is the string `NA` or an empty string.

Read `n` values (one per line). Print:
- `Missing: k` (count of missing values)
- `Complete: m` (count of non-missing values)

Example:
```
Input:
5
10
NA
30
NA
50
Output:
Missing: 2
Complete: 3
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [input() for _ in range(n)]\n# Count missing vs complete\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.2: Advanced Central Tendency (Q6–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Compute the **trimmed mean** by removing the bottom `p%` and top `p%` of values (by proportion, not count).

Trim floor(n × p / 100) values from each end.

Read `n` numbers (one per line), then `p` (integer percentage). Print the trimmed mean rounded to 4 decimal places. If trimming leaves no values, print `0.0`.

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
20
Output: 3.5
```
*(Trims 1 from each end: [2,3,4,5], mean = 3.5)*
MD,
                'starter_code'        => "n = int(input())\nnums = sorted([float(input()) for _ in range(n)])\np = int(input())\n# Compute trimmed mean\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Compute the **harmonic mean** of a list of positive numbers.

Harmonic mean = n / Σ(1/xi)

Read `n` numbers (one per line). Print the harmonic mean rounded to 4 decimal places.

Example:
```
Input:
3
1
2
4
Output: 1.7143
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Compute harmonic mean\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Compute the **midrange** and the **midhinge** of a dataset.

- Midrange = (max + min) / 2
- Midhinge = (Q1 + Q3) / 2  (use exclusive quartiles)

Read `n` numbers (one per line). Print midrange and midhinge each rounded to 4 decimal places on separate lines.

Example:
```
Input:
6
1
3
5
7
9
11
Output:
6.0
6.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = sorted([float(input()) for _ in range(n)])\n# Compute midrange and midhinge\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Compute the **moving average** (simple) of a time series with window size `k`.

For each position where the full window fits, output the mean of the window rounded to 4 decimal places.

Read `n` numbers (one per line), then `k`. Print the moving average values one per line.

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
2.0
3.0
4.0
5.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\nk = int(input())\n# Compute moving average\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Compute the **exponential moving average (EMA)** of a series.

EMA_t = α × x_t + (1 − α) × EMA_{t-1}

Initialise EMA with the first value.

Read `n` numbers (one per line), then `alpha` (float). Print each EMA value rounded to 4 decimal places.

Example:
```
Input:
4
10
12
13
12
0.5
Output:
10.0
11.0
12.0
12.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\nalpha = float(input())\n# Compute EMA\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.3: Advanced Variability & Robust Stats (Q11–Q16)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Compute the **Mean Absolute Deviation (MAD)** of a dataset.

MAD = Σ|xi − mean| / n

Read `n` numbers (one per line). Print MAD rounded to 4 decimal places.

Example:
```
Input:
5
2
2
3
4
14
Output: 3.6
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Compute MAD\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Compute the **Median Absolute Deviation (MedianAD)** of a dataset.

MedianAD = median(|xi − median(x)|)

Read `n` numbers (one per line). Print MedianAD rounded to 4 decimal places.

Example:
```
Input:
5
1
1
2
2
4
Output: 1.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = sorted([float(input()) for _ in range(n)])\n# Compute Median Absolute Deviation\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Compute the **skewness** of a dataset using the sample formula (Fisher's moment coefficient):

skewness = [n / ((n−1)(n−2))] × Σ((xi − x̄) / s)³

where s is the **sample** standard deviation.

Read `n` numbers (one per line). Print skewness rounded to 4 decimal places. For n < 3, print `undefined`.

Example:
```
Input:
5
2
8
0
4
1
Output: 1.4863
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nnums = [float(input()) for _ in range(n)]\n# Compute sample skewness (Fisher)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Compute the **excess kurtosis** of a dataset (sample kurtosis − 3).

Sample kurtosis (excess) = [(n(n+1)) / ((n−1)(n−2)(n−3))] × Σ((xi−x̄)/s)⁴ − [3(n−1)²/((n−2)(n−3))]

Read `n` numbers (one per line). Print excess kurtosis rounded to 4 decimal places. For n < 4, print `undefined`.

Example:
```
Input:
5
2
8
0
4
1
Output: 1.5
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nnums = [float(input()) for _ in range(n)]\n# Compute excess kurtosis (sample)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Compute the **percentile** of a value in a dataset using **linear interpolation** (same as numpy's default method).

Steps:
1. Sort the data.
2. For percentile p, compute i = (p/100) × (n−1).
3. Interpolate: result = data[floor(i)] + (i − floor(i)) × (data[ceil(i)] − data[floor(i)]).

Read `n` numbers, then `p` (float 0–100). Print the percentile rounded to 4 decimal places.

Example:
```
Input:
5
1
2
3
4
5
25
Output: 2.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nnums = sorted([float(input()) for _ in range(n)])\np = float(input())\n# Compute percentile via linear interpolation\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Compute a **box-plot summary** including outlier counts using the 1.5 × IQR rule.

Print:
```
Min: <val>
Q1: <val>
Median: <val>
Q3: <val>
Max: <val>
IQR: <val>
Lower fence: <val>
Upper fence: <val>
Outliers: <count>
```

Use exclusive quartiles. Round all values to 4 decimal places.

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
100
Output:
Min: 1.0
Q1: 2.25
Median: 4.5
Q3: 6.25
Max: 100.0
IQR: 4.0
Lower fence: -3.75
Upper fence: 12.25
Outliers: 1
```
MD,
                'starter_code'        => "n = int(input())\nnums = sorted([float(input()) for _ in range(n)])\n# Compute full box-plot summary\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.4: Probability (Conditional & Law of Total P) (Q17–Q21)
            // ═══════════════════════════════════════════════════════════════

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Compute **P(A|B)** using the **Law of Total Probability** and Bayes' Theorem with multiple mutually exclusive hypotheses.

Given `k` hypotheses H1…Hk, each with prior P(Hi) and likelihood P(B|Hi), compute:

P(B) = Σ P(B|Hi) × P(Hi)
P(Hi|B) = P(B|Hi) × P(Hi) / P(B)

Read `k`, then `k` lines of `P(Hi) P(B|Hi)`. Print P(B) rounded to 6 decimal places, then P(Hi|B) for each hypothesis rounded to 6 decimal places.

Example:
```
Input:
2
0.4 0.3
0.6 0.5
Output:
P(B): 0.42
P(H1|B): 0.285714
P(H2|B): 0.714286
```
MD,
                'starter_code'        => "k = int(input())\nhyps = []\nfor _ in range(k):\n    ph, pbh = map(float, input().split())\n    hyps.append((ph, pbh))\n# Compute P(B) and posteriors\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Compute the **conditional probability** P(A|B) from a 2×2 contingency table.

Input: 4 integers representing the table cells:
```
       B   notB
A      a    b
notA   c    d
```

Read `a b c d` (space-separated). Print:
- `P(A|B)` rounded to 4 decimal places
- `P(A|notB)` rounded to 4 decimal places

Example:
```
Input: 40 20 10 30
Output:
P(A|B): 0.8
P(A|notB): 0.4
```
MD,
                'starter_code'        => "a, b, c, d = map(int, input().split())\n# Compute conditional probabilities\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Compute the **expected value** E(X) and **variance** Var(X) of a discrete probability distribution.

E(X) = Σ xi × P(xi)
Var(X) = Σ P(xi) × (xi − E(X))²

Read `n` pairs of `x P(x)` (one pair per line). Print E(X) and Var(X) each rounded to 4 decimal places.

Example:
```
Input:
3
1 0.2
2 0.5
3 0.3
Output:
E(X): 2.1
Var(X): 0.49
```
MD,
                'starter_code'        => "n = int(input())\npairs = []\nfor _ in range(n):\n    x, p = map(float, input().split())\n    pairs.append((x, p))\n# Compute E(X) and Var(X)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Compute the **cumulative distribution** of a discrete dataset.

For each unique value (sorted), print `value: CDF` where CDF is the proportion of data ≤ value, rounded to 4 decimal places.

Read `n` numbers (one per line).

Example:
```
Input:
5
3
1
2
3
2
Output:
1: 0.2
2: 0.6
3: 1.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = sorted([float(input()) for _ in range(n)])\n# Compute empirical CDF\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Compute the **geometric distribution** — the probability that the first success occurs on trial `k`.

P(X = k) = (1 − p)^(k−1) × p

And the **cumulative probability** P(X ≤ k).

Read `p` and `k`. Print P(X = k) and P(X ≤ k) each rounded to 6 decimal places on separate lines.

Example:
```
Input:
0.3
4
Output:
P(X=k): 0.1029
P(X<=k): 0.7599
```
MD,
                'starter_code'        => "p = float(input())\nk = int(input())\n# Compute geometric PMF and CDF\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.5: Distributions (Poisson, Cumulative Binomial) (Q22–Q27)
            // ═══════════════════════════════════════════════════════════════

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Compute the **Poisson probability** P(X = k) given rate λ.

P(X = k) = e^(−λ) × λ^k / k!

Read `lambda` and `k`. Print the probability rounded to 6 decimal places.

Example:
```
Input:
3
5
Output: 0.100819
```
MD,
                'starter_code'        => "import math\n\nlam = float(input())\nk = int(input())\n# Compute Poisson P(X=k)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Compute the **cumulative Poisson probability** P(X ≤ k).

P(X ≤ k) = Σ_{i=0}^{k} e^(−λ) × λ^i / i!

Read `lambda` and `k`. Print P(X ≤ k) rounded to 6 decimal places.

Example:
```
Input:
2
3
Output: 0.857123
```
MD,
                'starter_code'        => "import math\n\nlam = float(input())\nk = int(input())\n# Compute cumulative Poisson probability\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Compute the **cumulative binomial probability** P(X ≤ k).

P(X ≤ k) = Σ_{i=0}^{k} C(n,i) × p^i × (1−p)^(n−i)

Read `n`, `k`, and `p`. Print P(X ≤ k) rounded to 6 decimal places.

Example:
```
Input:
10
3
0.5
Output: 0.171875
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nk = int(input())\np = float(input())\n# Compute cumulative binomial P(X<=k)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Approximate the **normal CDF** Φ(z) using the Abramowitz & Stegun rational approximation (maximum error 7.5 × 10⁻⁸):

Given z, compute p = 1 − φ(z) × poly(t) where:
- t = 1 / (1 + 0.2316419 × |z|)
- φ(z) = (1/√(2π)) × e^(−z²/2)
- poly(t) = t(0.319381530 + t(−0.356563782 + t(1.781477937 + t(−1.821255978 + t × 1.330274429))))

For z ≥ 0: Φ(z) = p  
For z < 0: Φ(z) = 1 − p (using |z|)

Read `z`. Print Φ(z) rounded to 6 decimal places.

Example:
```
Input: 1.96
Output: 0.975002
```
MD,
                'starter_code'        => "import math\n\nz = float(input())\n# Approximate normal CDF via A&S formula\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Compute the **negative binomial probability** — the probability that the r-th success occurs on exactly the k-th trial.

P(X = k) = C(k−1, r−1) × p^r × (1−p)^(k−r)

Read `r`, `k`, and `p`. Print the probability rounded to 6 decimal places.

Example:
```
Input:
3
6
0.5
Output: 0.15625
```
MD,
                'starter_code'        => "import math\n\nr = int(input())\nk = int(input())\np = float(input())\n# Compute negative binomial P(X=k)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Compute the **hypergeometric probability** P(X = k):

P(X = k) = C(K, k) × C(N−K, n−k) / C(N, n)

where:
- N = population size
- K = number of success states in population
- n = number of draws
- k = observed successes in draw

Read `N K n k` (space-separated). Print the probability rounded to 6 decimal places.

Example:
```
Input: 20 7 12 4
Output: 0.214735
```
MD,
                'starter_code'        => "import math\n\nN, K, n, k = map(int, input().split())\n# Compute hypergeometric probability\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.6: Correlation Matrix & Advanced Correlation (Q28–Q32)
            // ═══════════════════════════════════════════════════════════════

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Compute the **Pearson correlation matrix** for `m` variables, each with `n` observations.

Print the m×m matrix rounded to 4 decimal places, space-separated rows. The diagonal is always 1.0.

Read `m` (variables) and `n` (observations), then `m` rows of `n` values.

Example:
```
Input:
2
3
1 2 3
2 4 6
Output:
1.0 1.0
1.0 1.0
```
MD,
                'starter_code'        => "import math\n\nm = int(input())\nn = int(input())\ndata = []\nfor _ in range(m):\n    row = list(map(float, input().split()))\n    data.append(row)\n# Compute correlation matrix\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Compute the **lag-1 autocorrelation** of a time series.

r₁ = Σ_{t=2}^{n}(xt − x̄)(x_{t-1} − x̄) / Σ_{t=1}^{n}(xt − x̄)²

Read `n` values (one per line). Print r₁ rounded to 4 decimal places.

Example:
```
Input:
5
1
2
3
4
5
Output: 0.4
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Compute lag-1 autocorrelation\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Compute the **point-biserial correlation** between a binary variable (0/1) and a continuous variable.

r_pb = (M1 − M0) / s × √(n1 × n0 / n²)

where:
- M1, M0 = mean of continuous variable for group 1 and group 0
- s = population SD of the continuous variable
- n1, n0, n = counts

Read `n`, then `n` lines of `binary continuous`. Print r_pb rounded to 4 decimal places.

Example:
```
Input:
6
0 2.0
0 3.0
0 4.0
1 6.0
1 7.0
1 8.0
Output: 0.9449
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\ndata = []\nfor _ in range(n):\n    b, c = input().split()\n    data.append((int(b), float(c)))\n# Compute point-biserial correlation\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Perform a **Kendall's tau-b** rank correlation calculation.

τ_b = (C − D) / √((C + D + T_x)(C + D + T_y))

where C = concordant pairs, D = discordant pairs, T_x = ties in X only, T_y = ties in Y only.

Read `n`, then `n` x values, then `n` y values. Print τ_b rounded to 4 decimal places.

Example:
```
Input:
5
1
2
3
4
5
3
4
1
5
2
Output: 0.2
```
MD,
                'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n# Compute Kendall tau-b\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Compute the **Cramér's V** statistic for a contingency table to measure association between two categorical variables.

V = √(χ² / (n × (min(r,c) − 1)))

where χ² is the chi-square statistic, n is total observations, r = rows, c = columns.

χ² = Σ (Observed − Expected)² / Expected  
Expected_{ij} = (row_total_i × col_total_j) / n

Read `r c`, then `r` rows of `c` integers. Print V rounded to 4 decimal places.

Example:
```
Input:
2 2
10 20
30 40
Output: 0.0816
```
MD,
                'starter_code'        => "import math\n\nr, c = map(int, input().split())\ntable = []\nfor _ in range(r):\n    row = list(map(int, input().split()))\n    table.append(row)\n# Compute Cramer's V\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.7: Multiple / Polynomial Regression (Q33–Q37)
            // ═══════════════════════════════════════════════════════════════

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Fit a **multiple linear regression** model (OLS) with 2 predictors.

Solve the normal equations: β = (XᵀX)⁻¹ Xᵀy manually for p=2 predictors (plus intercept).

Read `n`, then `n` lines of `x1 x2 y`. Print intercept β0, β1, β2 each rounded to 4 decimal places.

Example:
```
Input:
4
1 2 14
2 3 20
3 4 26
4 5 32
Output:
b0: 2.0
b1: 4.0
b2: 2.0
```
MD,
                'starter_code'        => "n = int(input())\nrows = []\nfor _ in range(n):\n    vals = list(map(float, input().split()))\n    rows.append(vals)\n# Solve OLS via normal equations (manual matrix ops)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Compute the **Root Mean Square Error (RMSE)** and **Mean Absolute Percentage Error (MAPE)** of a regression model.

RMSE = √(Σ(yi − ŷi)² / n)
MAPE = (1/n) × Σ|yi − ŷi| / |yi| × 100

Read `n`, then `n` lines of `actual predicted`. Print RMSE and MAPE each rounded to 4 decimal places.

Example:
```
Input:
4
10 11
20 19
30 31
40 39
Output:
RMSE: 1.0
MAPE: 3.6667
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\npairs = []\nfor _ in range(n):\n    a, p = map(float, input().split())\n    pairs.append((a, p))\n# Compute RMSE and MAPE\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Fit a **polynomial regression** of degree 2 (quadratic) using OLS.

ŷ = β0 + β1x + β2x²

Solve via the 3×3 normal equations. Read `n` pairs of `x y` (one per line). Print β0, β1, β2 each rounded to 4 decimal places.

Example:
```
Input:
4
1 2
2 5
3 10
4 17
Output:
b0: 1.0
b1: 0.0
b2: 1.0
```
MD,
                'starter_code'        => "n = int(input())\npoints = []\nfor _ in range(n):\n    x, y = map(float, input().split())\n    points.append((x, y))\n# Fit degree-2 polynomial via normal equations\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Compute the **Adjusted R²** for a multiple regression model.

R²_adj = 1 − [(1 − R²)(n − 1) / (n − p − 1)]

where p = number of predictors (not counting intercept).

Read `R2` (float), `n`, and `p`. Print Adjusted R² rounded to 4 decimal places.

Example:
```
Input:
0.85
20
3
Output: 0.8219
```
MD,
                'starter_code'        => "r2 = float(input())\nn = int(input())\np = int(input())\n# Compute adjusted R-squared\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Compute the **Variance Inflation Factor (VIF)** for a predictor variable.

VIF = 1 / (1 − R²_j)

where R²_j is the R² from regressing predictor j on all other predictors.

Read `R2_j`. Print VIF rounded to 4 decimal places. If R²_j ≥ 1, print `undefined`.

Example:
```
Input: 0.8
Output: 5.0
```
MD,
                'starter_code'        => "r2j = float(input())\n# Compute VIF\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.8: Hypothesis Testing (Q38–Q43)
            // ═══════════════════════════════════════════════════════════════

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Compute the **one-sample t-test** statistic and determine rejection at α = 0.05 (two-tailed).

t = (x̄ − μ₀) / (s / √n)   (s = sample SD)

Use a simple lookup for critical t-values (two-tailed, α=0.05):
- df ≥ 30: critical t = 2.042
- df ≥ 20: critical t = 2.086
- df ≥ 10: critical t = 2.228
- df < 10: critical t = 2.262  (df=9 case)

Read `n` sample values (one per line) and `mu_0`. Print `t`, then `Reject H0` or `Fail to reject H0`.

Example:
```
Input:
5
12
14
11
13
15
10
Output:
t: 2.7386
Reject H0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nsamples = [float(input()) for _ in range(n)]\nmu0 = float(input())\n# Compute t-statistic and decision\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Perform a **chi-square goodness-of-fit test**.

χ² = Σ (Oi − Ei)² / Ei

Read `k`, then `k` observed counts, then `k` expected counts. Print χ² rounded to 4 decimal places, degrees of freedom (k−1), and whether to `Reject H0` or `Fail to reject H0` at α=0.05 using critical values:
- df=1: 3.841, df=2: 5.991, df=3: 7.815, df=4: 9.488, df≥5: 11.07

Example:
```
Input:
3
10 20 30
15 15 30
Output:
chi2: 1.6667
df: 2
Fail to reject H0
```
MD,
                'starter_code'        => "k = int(input())\nobserved = list(map(float, input().split()))\nexpected = list(map(float, input().split()))\n# Chi-square goodness of fit\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Compute the **two-sample t-test** statistic (Welch's t-test, unequal variance).

t = (x̄₁ − x̄₂) / √(s₁²/n₁ + s₂²/n₂)

Degrees of freedom (Welch-Satterthwaite):
df = (s₁²/n₁ + s₂²/n₂)² / [(s₁²/n₁)²/(n₁−1) + (s₂²/n₂)²/(n₂−1)]

Read `n1` values (one per line) as sample 1, then `n2` values as sample 2. Print `t` and `df` each rounded to 4 decimal places.

Example:
```
Input:
3
2
4
6
3
3
5
7
Output:
t: -0.5774
df: 4.0
```
MD,
                'starter_code'        => "import math\n\nn1 = int(input())\ns1 = [float(input()) for _ in range(n1)]\nn2 = int(input())\ns2 = [float(input()) for _ in range(n2)]\n# Compute Welch's t-test\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Compute the **confidence interval** for a population mean using the z-distribution.

CI: x̄ ± z × (σ / √n)

z-values: 90% → 1.645, 95% → 1.960, 99% → 2.576

Read `confidence_level` (90, 95, or 99), `n` sample values (one per line), and `sigma` (population SD). Print `Lower: <val>` and `Upper: <val>` each rounded to 4 decimal places.

Example:
```
Input:
95
4
48
52
50
54
10
Output:
Lower: 40.2
Upper: 61.8
```
MD,
                'starter_code'        => "import math\n\nconf = int(input())\nn = int(input())\nsamples = [float(input()) for _ in range(n)]\nsigma = float(input())\n# Compute z confidence interval\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Compute the **effect size (Cohen's d)** for two independent samples.

d = (x̄₁ − x̄₂) / s_pooled

s_pooled = √(((n₁−1)s₁² + (n₂−1)s₂²) / (n₁+n₂−2))

Interpret: |d| < 0.2 → `negligible`, 0.2 ≤ |d| < 0.5 → `small`, 0.5 ≤ |d| < 0.8 → `medium`, ≥ 0.8 → `large`.

Read `n1` values then `n2` values (each on its own line, after its n). Print `d` rounded to 4 decimal places, then the interpretation.

Example:
```
Input:
3
5
6
7
3
2
3
4
Output:
d: 1.7321
large
```
MD,
                'starter_code'        => "import math\n\nn1 = int(input())\ns1 = [float(input()) for _ in range(n1)]\nn2 = int(input())\ns2 = [float(input()) for _ in range(n2)]\n# Compute Cohen's d\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Compute the **statistical power** of a one-sample z-test.

Power = Φ(|z_crit| − δ) + Φ(−|z_crit| − δ) — but for a simpler one-sided approximation:

Power ≈ Φ(δ − z_α)

where:
- δ = (μ₁ − μ₀) / (σ / √n)  (effect in standard units)
- z_α = 1.645 (one-sided, α=0.05)
- Use the Abramowitz & Stegun CDF approximation from earlier.

Read `mu0`, `mu1`, `sigma`, `n`. Print power rounded to 4 decimal places.

Example:
```
Input:
50
55
10
25
Output: 0.7054
```
MD,
                'starter_code'        => "import math\n\nmu0 = float(input())\nmu1 = float(input())\nsigma = float(input())\nn = int(input())\n# Compute one-sided power (one-sample z)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.9: Resampling & Bootstrap (Q44–Q47)
            // ═══════════════════════════════════════════════════════════════

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Compute a **deterministic bootstrap mean** using a given set of resampled indices.

Instead of random sampling, you're given `B` bootstrap samples (each as a list of indices into the original data). Compute the mean of each bootstrap sample, then output the bootstrap mean estimate and the 95% bootstrap CI (2.5th and 97.5th percentile of the bootstrap means using linear interpolation).

Read `n` original values, then `B`, then `B` lines each containing `n` space-separated 0-indexed indices.

Print:
```
Bootstrap Mean: <val>
CI Lower: <val>
CI Upper: <val>
```
All rounded to 4 decimal places.

Example:
```
Input:
3
1 2 3
2
0 1 2
2 2 0
Output:
Bootstrap Mean: 2.0
CI Lower: 1.5
CI Upper: 2.5
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\norig = list(map(float, input().split()))\nB = int(input())\nsamples = []\nfor _ in range(B):\n    idx = list(map(int, input().split()))\n    samples.append(idx)\n# Compute bootstrap CI\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Perform **stratified sampling** proportionally.

Given `k` strata with sizes and total sample size `m`, compute how many to sample from each stratum (proportional allocation, rounding down; add any remaining slots to strata in decreasing order of fractional parts).

Read `k`, then `k` stratum sizes, then `m`. Print the sample size per stratum, one per line.

Example:
```
Input:
3
40
30
30
10
Output:
4
3
3
```
MD,
                'starter_code'        => "k = int(input())\nsizes = [int(input()) for _ in range(k)]\nm = int(input())\n# Proportional stratified allocation\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Implement **k-fold cross-validation** index splitting (deterministic, no shuffle).

Given `n` samples and `k` folds, split indices 0…n−1 into k folds. Each fold in turn is the validation set; the rest is training. For each fold, print the validation indices space-separated.

Read `n` and `k`. Print `k` lines of validation indices.

Example:
```
Input:
6
3
Output:
0 1
2 3
4 5
```
MD,
                'starter_code'        => "n = int(input())\nk = int(input())\n# Print k-fold validation indices\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Compute the **jackknife estimate** of the mean and its bias.

Jackknife mean = mean of leave-one-out sample means.
Bias = (n − 1) × (jackknife_mean − sample_mean)

Read `n` values (one per line). Print jackknife mean and bias each rounded to 6 decimal places.

Example:
```
Input:
4
2
4
6
8
Output:
Jackknife Mean: 5.0
Bias: 0.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Compute jackknife mean and bias\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.10: Full EDA Pipeline (Q48–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Produce a **full univariate EDA report** for a numeric dataset.

Output (in order):
```
Count: n
Missing: k
Mean: <val>
Median: <val>
Mode: <val>  (lowest mode if multiple)
Std Dev: <val>  (sample)
Variance: <val>  (sample)
Min: <val>
Q1: <val>
Q3: <val>
Max: <val>
IQR: <val>
Skewness: <val>  (Fisher sample)
Kurtosis: <val>  (excess)
Outliers: <count>
```

All floats rounded to 4 decimal places. Use exclusive quartiles. For n < 3, print `Skewness: undefined`. For n < 4, print `Kurtosis: undefined`.

Read `n` values (some may be `NA` — exclude them from all calculations, count as missing).

Example:
```
Input:
6
2
4
NA
4
4
5
Output:
Count: 6
Missing: 1
Mean: 3.8
Median: 4.0
Mode: 4
Std Dev: 0.9574
Variance: 0.9167
Min: 2.0
Q1: 3.0
Q3: 4.5
Max: 5.0
IQR: 1.5
Skewness: -1.2045
Kurtosis: 0.75
Outliers: 0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nraw = [input() for _ in range(n)]\n# Full EDA report (handle NA, all stats)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Produce a **bivariate EDA report** for two numeric variables X and Y.

Output:
```
n: <count>
Mean X: <val>
Mean Y: <val>
Std X: <val>  (population)
Std Y: <val>  (population)
Covariance: <val>  (population)
Pearson r: <val>
R-squared: <val>
Slope: <val>
Intercept: <val>
RMSE: <val>
```

Round all to 4 decimal places.

Read `n`, then `n` lines of `x y`.

Example:
```
Input:
4
1 2
2 4
3 6
4 8
Output:
n: 4
Mean X: 2.5
Mean Y: 5.0
Std X: 1.118
Std Y: 2.2361
Covariance: 2.5
Pearson r: 1.0
R-squared: 1.0
Slope: 2.0
Intercept: 0.0
RMSE: 0.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\npoints = []\nfor _ in range(n):\n    x, y = map(float, input().split())\n    points.append((x, y))\n# Full bivariate EDA report\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Compute a **comparative group statistics** report for two groups A and B.

For each group, compute: mean, median, sample SD, and IQR.

Then compute:
- Cohen's d (pooled SD)
- Welch's t-statistic

Print:
```
Group A Mean: <val>
Group A Median: <val>
Group A SD: <val>
Group A IQR: <val>
Group B Mean: <val>
Group B Median: <val>
Group B SD: <val>
Group B IQR: <val>
Cohen d: <val>
Welch t: <val>
```

Round all to 4 decimal places. Use exclusive quartiles.

Read `n_a` values for group A (one per line), then `n_b` values for group B.

Example:
```
Input:
4
10
12
11
13
4
20
22
21
23
Output:
Group A Mean: 11.5
Group A Median: 11.5
Group A SD: 1.2910
Group A IQR: 2.0
Group B Mean: 21.5
Group B Median: 21.5
Group B SD: 1.2910
Group B IQR: 2.0
Cohen d: -7.7460
Welch t: -10.9545
```
MD,
                'starter_code'        => "import math\n\nn_a = int(input())\ngroup_a = [float(input()) for _ in range(n_a)]\nn_b = int(input())\ngroup_b = [float(input()) for _ in range(n_b)]\n# Comparative group statistics report\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],
        ];

        // ─────────────────────────────────────────────────────────────────
        // 3. INSERT QUESTIONS
        // ─────────────────────────────────────────────────────────────────

        $questions = [];

        foreach ($questionDefs as $def) {
            $q = DB::table('coding_questions')->updateOrInsert(
                [
                    'challenge_id' => $challenge->id,
                    'order_index'  => $def['order_index'],
                ],
                [
                    'problem_description' => $def['problem_description'],
                    'starter_code'        => $def['starter_code'],
                    'time_limit_seconds'  => $def['time_limit_seconds'],
                    'base_xp'             => $def['base_xp'],
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]
            );

            $questions[$def['order_index']] = DB::table('coding_questions')
                ->where('challenge_id', $challenge->id)
                ->where('order_index', $def['order_index'])
                ->first();
        }

        // ─────────────────────────────────────────────────────────────────
        // 4. TEST CASES
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $seed = function (int $qIndex, array $cases) use ($questions): void {
            $q = $questions[$qIndex] ?? null;
            if (! $q) return;

            foreach ($cases as $case) {
                DB::table('test_cases')->updateOrInsert(
                    [
                        'coding_question_id' => $q->id,
                        'order_index'        => $case['order_index'],
                    ],
                    [
                        'input'           => $case['input'],
                        'expected_output' => $case['expected_output'],
                        'is_hidden'       => $case['is_hidden'],
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]
                );
            }
        };

        // ── Q1: label encoding ────────────────────────────────────────────
        $seed(1, [
            ['input' => "4\ncat\ndog\ncat\nbird",       'expected_output' => "1\n2\n1\n0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nred\ngreen\nblue",           'expected_output' => "2\n1\n0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\na\nb\na\nc\nb",              'expected_output' => "0\n1\n0\n2\n1",'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nzebra\napple",               'expected_output' => "1\n0",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: one-hot encoding ──────────────────────────────────────────
        $seed(2, [
            ['input' => "3\ncat\ndog\ncat",             'expected_output' => "1 0\n0 1\n1 0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nred\nblue\ngreen",          'expected_output' => "0 0 1\n1 0 0\n0 1 0",'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\na\nb",                      'expected_output' => "1 0\n0 1",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nx\ny\nx\nz",                'expected_output' => "1 0 0\n0 1 0\n1 0 0\n0 0 1", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q3: min-max normalisation ─────────────────────────────────────
        $seed(3, [
            ['input' => "4\n0\n5\n10\n20",   'expected_output' => "0.0\n0.25\n0.5\n1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",        'expected_output' => "0.0\n0.5\n1.0",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5\n5\n5",        'expected_output' => "0.0\n0.0\n0.0",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n-10\n0\n10\n20", 'expected_output' => "0.0\n0.3333\n0.6667\n1.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q4: z-score standardisation ───────────────────────────────────
        $seed(4, [
            ['input' => "3\n2\n4\n6",      'expected_output' => "-1.0\n0.0\n1.0",            'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0\n0\n0\n0",   'expected_output' => "0.0\n0.0\n0.0\n0.0",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n2\n4\n4\n4\n6",'expected_output' => "-1.4142\n0.0\n0.0\n0.0\n1.4142", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n10\n20",       'expected_output' => "-1.0\n1.0",                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: missing values ────────────────────────────────────────────
        $seed(5, [
            ['input' => "5\n10\nNA\n30\nNA\n50",   'expected_output' => "Missing: 2\nComplete: 3", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",              'expected_output' => "Missing: 0\nComplete: 3", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nNA\nNA\nNA\nNA",        'expected_output' => "Missing: 4\nComplete: 0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1\nNA\n3\n4\nNA\n6",    'expected_output' => "Missing: 2\nComplete: 4", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: trimmed mean ──────────────────────────────────────────────
        $seed(6, [
            ['input' => "6\n1\n2\n3\n4\n5\n100\n20",  'expected_output' => "3.5",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n25",          'expected_output' => "2.5",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n10\n20\n30\n40\n50\n0",   'expected_output' => "30.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "8\n1\n1\n2\n3\n5\n8\n13\n21\n10", 'expected_output' => "4.5", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q7: harmonic mean ─────────────────────────────────────────────
        $seed(7, [
            ['input' => "3\n1\n2\n4",         'expected_output' => "1.7143",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2\n2",            'expected_output' => "2.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n1\n1",         'expected_output' => "1.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2\n4\n8\n16",     'expected_output' => "4.2667",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: midrange & midhinge ───────────────────────────────────────
        $seed(8, [
            ['input' => "6\n1\n3\n5\n7\n9\n11",   'expected_output' => "6.0\n6.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4",          'expected_output' => "2.5\n2.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n10\n20\n30\n40\n50",  'expected_output' => "30.0\n27.5", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2\n4\n6\n8",          'expected_output' => "5.0\n4.5",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: moving average ────────────────────────────────────────────
        $seed(9, [
            ['input' => "6\n1\n2\n3\n4\n5\n6\n3",     'expected_output' => "2.0\n3.0\n4.0\n5.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n10\n20\n30\n40\n50\n2",   'expected_output' => "15.0\n25.0\n35.0\n45.0",'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4\n4",           'expected_output' => "2.5",                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n5\n10\n15\n20\n25\n30\n2",'expected_output' => "7.5\n12.5\n17.5\n22.5\n27.5", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q10: EMA ──────────────────────────────────────────────────────
        $seed(10, [
            ['input' => "4\n10\n12\n13\n12\n0.5",   'expected_output' => "10.0\n11.0\n12.0\n12.0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n1.0",          'expected_output' => "1.0\n2.0\n3.0",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n10\n10\n10\n0.3",   'expected_output' => "10.0\n10.0\n10.0\n10.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0\n10\n0\n0.5",         'expected_output' => "0.0\n5.0\n2.5",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: MAD ──────────────────────────────────────────────────────
        $seed(11, [
            ['input' => "5\n2\n2\n3\n4\n14",   'expected_output' => "3.6",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",          'expected_output' => "0.6667", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n1\n1\n1",       'expected_output' => "0.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2\n4\n6\n8",       'expected_output' => "2.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Median AD ────────────────────────────────────────────────
        $seed(12, [
            ['input' => "5\n1\n1\n2\n2\n4",   'expected_output' => "1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",         'expected_output' => "1.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n1\n1\n1",      'expected_output' => "0.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1\n2\n3\n100\n200",'expected_output'=> "2.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: skewness ─────────────────────────────────────────────────
        $seed(13, [
            ['input' => "5\n2\n8\n0\n4\n1",      'expected_output' => "1.4863",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1\n2\n3\n4\n5",      'expected_output' => "0.0",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n2",               'expected_output' => "undefined",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n100",       'expected_output' => "1.9944",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: kurtosis ─────────────────────────────────────────────────
        $seed(14, [
            ['input' => "5\n2\n8\n0\n4\n1",     'expected_output' => "1.5",         'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1\n2\n3\n4\n5",     'expected_output' => "-1.3",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3",           'expected_output' => "undefined",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1\n1\n1\n2\n2\n2",  'expected_output' => "-2.1429",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: percentile (linear interpolation) ────────────────────────
        $seed(15, [
            ['input' => "5\n1\n2\n3\n4\n5\n25",   'expected_output' => "2.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n50",      'expected_output' => "2.5",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n2\n3\n4\n5\n0",    'expected_output' => "1.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1\n2\n3\n4\n5\n75",   'expected_output' => "4.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: box-plot summary ─────────────────────────────────────────
        $seed(16, [
            ['input' => "8\n1\n2\n3\n4\n5\n6\n7\n100", 'expected_output' => "Min: 1.0\nQ1: 2.25\nMedian: 4.5\nQ3: 6.25\nMax: 100.0\nIQR: 4.0\nLower fence: -3.75\nUpper fence: 12.25\nOutliers: 1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4",              'expected_output' => "Min: 1.0\nQ1: 1.25\nMedian: 2.5\nQ3: 3.75\nMax: 4.0\nIQR: 2.5\nLower fence: -2.5\nUpper fence: 7.5\nOutliers: 0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1\n2\n3\n4\n5\n20",       'expected_output' => "Min: 1.0\nQ1: 1.75\nMedian: 3.5\nQ3: 4.75\nMax: 20.0\nIQR: 3.0\nLower fence: -2.75\nUpper fence: 9.25\nOutliers: 1", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5\n5\n5\n5\n5\n5",           'expected_output' => "Min: 5.0\nQ1: 5.0\nMedian: 5.0\nQ3: 5.0\nMax: 5.0\nIQR: 0.0\nLower fence: 5.0\nUpper fence: 5.0\nOutliers: 0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q17: law of total probability + Bayes ─────────────────────────
        $seed(17, [
            ['input' => "2\n0.4 0.3\n0.6 0.5",   'expected_output' => "P(B): 0.42\nP(H1|B): 0.285714\nP(H2|B): 0.714286",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.5 0.8\n0.5 0.2",   'expected_output' => "P(B): 0.5\nP(H1|B): 0.8\nP(H2|B): 0.2",                 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.2 0.9\n0.5 0.5\n0.3 0.1", 'expected_output' => "P(B): 0.46\nP(H1|B): 0.391304\nP(H2|B): 0.543478\nP(H3|B): 0.065217", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0.01 0.9\n0.99 0.05", 'expected_output' => "P(B): 0.0585\nP(H1|B): 0.153846\nP(H2|B): 0.846154",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: conditional probability from contingency table ───────────
        $seed(18, [
            ['input' => "40 20 10 30",   'expected_output' => "P(A|B): 0.8\nP(A|notB): 0.4",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "10 10 10 10",   'expected_output' => "P(A|B): 0.5\nP(A|notB): 0.5",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "30 0 0 70",     'expected_output' => "P(A|B): 1.0\nP(A|notB): 0.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "15 35 20 30",   'expected_output' => "P(A|B): 0.4286\nP(A|notB): 0.5385", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q19: E(X) and Var(X) ──────────────────────────────────────────
        $seed(19, [
            ['input' => "3\n1 0.2\n2 0.5\n3 0.3",   'expected_output' => "E(X): 2.1\nVar(X): 0.49",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0.5\n1 0.5",           'expected_output' => "E(X): 0.5\nVar(X): 0.25",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n10 0.1\n20 0.6\n30 0.3", 'expected_output' => "E(X): 21.0\nVar(X): 29.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5 1.0",                  'expected_output' => "E(X): 5.0\nVar(X): 0.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: empirical CDF ────────────────────────────────────────────
        $seed(20, [
            ['input' => "5\n3\n1\n2\n3\n2",   'expected_output' => "1: 0.2\n2: 0.6\n3: 1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",         'expected_output' => "1: 0.3333\n2: 0.6667\n3: 1.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n1\n1\n1",      'expected_output' => "1: 1.0",                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4",      'expected_output' => "1: 0.25\n2: 0.5\n3: 0.75\n4: 1.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q21: geometric distribution ───────────────────────────────────
        $seed(21, [
            ['input' => "0.3\n4",   'expected_output' => "P(X=k): 0.1029\nP(X<=k): 0.7599",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n1",   'expected_output' => "P(X=k): 0.5\nP(X<=k): 0.5",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.2\n3",   'expected_output' => "P(X=k): 0.128\nP(X<=k): 0.488",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.8\n2",   'expected_output' => "P(X=k): 0.16\nP(X<=k): 0.96",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Poisson PMF ──────────────────────────────────────────────
        $seed(22, [
            ['input' => "3\n5",    'expected_output' => "0.100819",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2",    'expected_output' => "0.270671",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0",    'expected_output' => "0.006738",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1",    'expected_output' => "0.367879",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: cumulative Poisson ───────────────────────────────────────
        $seed(23, [
            ['input' => "2\n3",    'expected_output' => "0.857123",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0",    'expected_output' => "0.367879",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2",    'expected_output' => "0.423190",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4",    'expected_output' => "0.628837",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: cumulative binomial ──────────────────────────────────────
        $seed(24, [
            ['input' => "10\n3\n0.5",    'expected_output' => "0.171875",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n5\n0.5",     'expected_output' => "1.0",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "8\n2\n0.3",     'expected_output' => "0.551762",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n0\n0.4",     'expected_output' => "0.046656",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: normal CDF approximation ─────────────────────────────────
        $seed(25, [
            ['input' => "1.96",    'expected_output' => "0.975002",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0",     'expected_output' => "0.5",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1.96",   'expected_output' => "0.024998",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.576",   'expected_output' => "0.995002",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: negative binomial ────────────────────────────────────────
        $seed(26, [
            ['input' => "3\n6\n0.5",   'expected_output' => "0.15625",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1\n0.5",   'expected_output' => "0.5",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n4\n0.4",   'expected_output' => "0.13824",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n3\n1.0",   'expected_output' => "1.0",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: hypergeometric ───────────────────────────────────────────
        $seed(27, [
            ['input' => "20 7 12 4",   'expected_output' => "0.214735",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "10 5 5 2",    'expected_output' => "0.396825",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "15 6 6 3",    'expected_output' => "0.311880",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "8 4 4 4",     'expected_output' => "0.228571",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: correlation matrix ───────────────────────────────────────
        $seed(28, [
            ['input' => "2\n3\n1 2 3\n2 4 6",             'expected_output' => "1.0 1.0\n1.0 1.0",             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3\n1 2 3\n3 2 1",             'expected_output' => "1.0 -1.0\n-1.0 1.0",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n4\n1 2 3 4\n4 3 2 1",         'expected_output' => "1.0 -1.0\n-1.0 1.0",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n3\n1 2 3\n1 2 3\n3 2 1",      'expected_output' => "1.0 1.0 -1.0\n1.0 1.0 -1.0\n-1.0 -1.0 1.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q29: lag-1 autocorrelation ────────────────────────────────────
        $seed(29, [
            ['input' => "5\n1\n2\n3\n4\n5",    'expected_output' => "0.4",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n1\n2",       'expected_output' => "-0.3333",'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n5\n5\n5\n5",    'expected_output' => "0.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n3\n2\n4",       'expected_output' => "-0.2",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: point-biserial correlation ──────────────────────────────
        $seed(30, [
            ['input' => "6\n0 2.0\n0 3.0\n0 4.0\n1 6.0\n1 7.0\n1 8.0",  'expected_output' => "0.9449",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0 1.0\n0 2.0\n1 3.0\n1 4.0",                 'expected_output' => "0.9487",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 5.0\n0 5.0\n1 5.0\n1 5.0",                 'expected_output' => "0.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n0 10\n0 20\n0 15\n1 30\n1 40\n1 35",         'expected_output' => "0.9623",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Kendall tau-b ────────────────────────────────────────────
        $seed(31, [
            ['input' => "5\n1\n2\n3\n4\n5\n3\n4\n1\n5\n2",   'expected_output' => "0.2",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n1\n2\n3\n4",          'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4\n4\n3\n2\n1",          'expected_output' => "-1.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1\n2\n3\n4\n5\n2\n1\n4\n3\n5",    'expected_output' => "0.6",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Cramér's V ───────────────────────────────────────────────
        $seed(32, [
            ['input' => "2 2\n10 20\n30 40",     'expected_output' => "0.0816",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n50 0\n0 50",       'expected_output' => "1.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n25 25\n25 25",     'expected_output' => "0.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 3\n10 20 30\n30 20 10",'expected_output'=> "0.3651",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: multiple linear regression ──────────────────────────────
        $seed(33, [
            ['input' => "4\n1 2 14\n2 3 20\n3 4 26\n4 5 32",   'expected_output' => "b0: 2.0\nb1: 4.0\nb2: 2.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 0 3\n0 1 4\n1 1 6",              'expected_output' => "b0: 1.0\nb1: 2.0\nb2: 2.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 0 1\n1 0 2\n0 1 3\n1 1 5",       'expected_output' => "b0: 1.0\nb1: 1.5\nb2: 1.5",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2 1 8\n4 2 16\n6 3 24\n8 4 32",    'expected_output' => "b0: 0.0\nb1: 3.0\nb2: 2.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: RMSE and MAPE ────────────────────────────────────────────
        $seed(34, [
            ['input' => "4\n10 11\n20 19\n30 31\n40 39",    'expected_output' => "RMSE: 1.0\nMAPE: 3.6667",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n100 100\n200 200\n300 300",     'expected_output' => "RMSE: 0.0\nMAPE: 0.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10 8\n20 22\n30 28\n40 44",    'expected_output' => "RMSE: 2.5495\nMAPE: 9.1667",'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n50 40\n100 110",               'expected_output' => "RMSE: 10.0\nMAPE: 12.5",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: polynomial regression (degree 2) ─────────────────────────
        $seed(35, [
            ['input' => "4\n1 2\n2 5\n3 10\n4 17",   'expected_output' => "b0: 1.0\nb1: 0.0\nb2: 1.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0 0\n1 1\n2 4",          'expected_output' => "b0: 0.0\nb1: 0.0\nb2: 1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1\n2 4\n3 9\n4 16",    'expected_output' => "b0: 0.0\nb1: 0.0\nb2: 1.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 3\n2 8\n3 15",         'expected_output' => "b0: 0.0\nb1: 1.0\nb2: 1.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: adjusted R² ──────────────────────────────────────────────
        $seed(36, [
            ['input' => "0.85\n20\n3",    'expected_output' => "0.8219",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n10\n2",     'expected_output' => "1.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.6\n50\n5",     'expected_output' => "0.5673",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.9\n100\n10",   'expected_output' => "0.8899",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: VIF ──────────────────────────────────────────────────────
        $seed(37, [
            ['input' => "0.8",    'expected_output' => "5.0",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0",    'expected_output' => "1.0",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5",    'expected_output' => "2.0",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0",    'expected_output' => "undefined",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: one-sample t-test ────────────────────────────────────────
        $seed(38, [
            ['input' => "5\n12\n14\n11\n13\n15\n10",     'expected_output' => "t: 2.7386\nReject H0",            'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n9\n10\n11\n10\n10\n10",      'expected_output' => "t: 0.0\nFail to reject H0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n5\n6\n7\n8\n5",              'expected_output' => "t: 2.6833\nReject H0",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n10\n10\n10\n10\n10\n10\n10", 'expected_output' => "t: 0.0\nFail to reject H0",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: chi-square goodness of fit ───────────────────────────────
        $seed(39, [
            ['input' => "3\n10 20 30\n15 15 30",    'expected_output' => "chi2: 1.6667\ndf: 2\nFail to reject H0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10 10\n5 15",           'expected_output' => "chi2: 3.3333\ndf: 1\nFail to reject H0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n40 60\n50 50",          'expected_output' => "chi2: 4.0\ndf: 1\nReject H0",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n30 30 40\n33 33 34",    'expected_output' => "chi2: 0.3636\ndf: 2\nFail to reject H0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Welch's t-test ───────────────────────────────────────────
        $seed(40, [
            ['input' => "3\n2\n4\n6\n3\n3\n5\n7",      'expected_output' => "t: -0.5774\ndf: 4.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n3\n4\n5\n6",      'expected_output' => "t: -3.7417\ndf: 4.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n5\n5\n5\n5\n4\n5\n6\n7\n8",'expected_output' => "t: -2.5\ndf: 4.2353",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10\n20\n2\n10\n20",         'expected_output' => "t: 0.0\ndf: 2.0",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: z confidence interval ────────────────────────────────────
        $seed(41, [
            ['input' => "95\n4\n48\n52\n50\n54\n10",   'expected_output' => "Lower: 40.2\nUpper: 61.8",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "99\n4\n48\n52\n50\n54\n10",   'expected_output' => "Lower: 37.12\nUpper: 64.88", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "90\n9\n1\n2\n3\n4\n5\n6\n7\n8\n9\n2",'expected_output' => "Lower: 3.9033\nUpper: 6.0967", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "95\n1\n100\n20",              'expected_output' => "Lower: 60.8\nUpper: 139.2", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Cohen's d ────────────────────────────────────────────────
        $seed(42, [
            ['input' => "3\n5\n6\n7\n3\n2\n3\n4",    'expected_output' => "d: 1.7321\nlarge",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n4\n1\n2\n3\n4",'expected_output'=> "d: 0.0\nnegligible", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n10\n11\n12\n3\n9\n10\n11",'expected_output' => "d: 0.7746\nmedium",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1\n2\n2\n3\n4",          'expected_output' => "d: -1.3416\nlarge",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: statistical power ────────────────────────────────────────
        $seed(43, [
            ['input' => "50\n55\n10\n25",    'expected_output' => "0.7054",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "50\n50\n10\n25",    'expected_output' => "0.05",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n110\n20\n64",  'expected_output' => "0.8438",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n5\n10\n100",     'expected_output' => "0.9993",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: deterministic bootstrap ─────────────────────────────────
        $seed(44, [
            ['input' => "3\n1 2 3\n2\n0 1 2\n2 2 0",           'expected_output' => "Bootstrap Mean: 2.0\nCI Lower: 1.5\nCI Upper: 2.5",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10 20\n2\n0 0\n1 1",               'expected_output' => "Bootstrap Mean: 15.0\nCI Lower: 10.0\nCI Upper: 20.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4\n4\n0 1 2 3\n0 0 1 1\n2 2 3 3\n1 2 3 0", 'expected_output' => "Bootstrap Mean: 2.25\nCI Lower: 1.5\nCI Upper: 2.875", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n5 10 15\n2\n0 0 0\n2 2 2",         'expected_output' => "Bootstrap Mean: 10.0\nCI Lower: 5.0\nCI Upper: 15.0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: stratified sampling allocation ───────────────────────────
        $seed(45, [
            ['input' => "3\n40\n30\n30\n10",   'expected_output' => "4\n3\n3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n50\n50\n10",       'expected_output' => "5\n5",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n10\n20\n70\n10",   'expected_output' => "1\n2\n7",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n25\n25\n25\n25\n8",'expected_output' => "2\n2\n2\n2",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: k-fold CV split ──────────────────────────────────────────
        $seed(46, [
            ['input' => "6\n3",    'expected_output' => "0 1\n2 3\n4 5",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n2",    'expected_output' => "0 1\n2 3",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5",    'expected_output' => "0\n1\n2\n3\n4",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "8\n4",    'expected_output' => "0 1\n2 3\n4 5\n6 7",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: jackknife estimate ───────────────────────────────────────
        $seed(47, [
            ['input' => "4\n2\n4\n6\n8",    'expected_output' => "Jackknife Mean: 5.0\nBias: 0.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",       'expected_output' => "Jackknife Mean: 2.0\nBias: 0.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n1\n1\n1\n5", 'expected_output' => "Jackknife Mean: 1.8\nBias: 0.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n20\n30\n40",'expected_output' => "Jackknife Mean: 25.0\nBias: 0.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: full univariate EDA ──────────────────────────────────────
        $seed(48, [
            ['input' => "6\n2\n4\nNA\n4\n4\n5",      'expected_output' => "Count: 6\nMissing: 1\nMean: 3.8\nMedian: 4.0\nMode: 4\nStd Dev: 0.9574\nVariance: 0.9167\nMin: 2.0\nQ1: 3.0\nQ3: 4.5\nMax: 5.0\nIQR: 1.5\nSkewness: -1.2045\nKurtosis: 0.75\nOutliers: 0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4",             'expected_output' => "Count: 4\nMissing: 0\nMean: 2.5\nMedian: 2.5\nMode: 1\nStd Dev: 1.2910\nVariance: 1.6667\nMin: 1.0\nQ1: 1.25\nQ3: 3.75\nMax: 4.0\nIQR: 2.5\nSkewness: 0.0\nKurtosis: -1.36\nOutliers: 0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\nNA\nNA\n1\n2\n3",        'expected_output' => "Count: 5\nMissing: 2\nMean: 2.0\nMedian: 2.0\nMode: 1\nStd Dev: 0.8165\nVariance: 0.6667\nMin: 1.0\nQ1: 1.0\nQ3: 3.0\nMax: 3.0\nIQR: 2.0\nSkewness: 0.0\nKurtosis: undefined\nOutliers: 0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "7\n1\n2\n2\n3\n3\n3\n100",  'expected_output' => "Count: 7\nMissing: 0\nMean: 16.2857\nMedian: 3.0\nMode: 3\nStd Dev: 35.7117\nVariance: 1355.2381\nMin: 1.0\nQ1: 2.0\nQ3: 3.0\nMax: 100.0\nIQR: 1.0\nSkewness: 2.6458\nKurtosis: 5.4629\nOutliers: 1", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q49: bivariate EDA ────────────────────────────────────────────
        $seed(49, [
            ['input' => "4\n1 2\n2 4\n3 6\n4 8",    'expected_output' => "n: 4\nMean X: 2.5\nMean Y: 5.0\nStd X: 1.118\nStd Y: 2.2361\nCovariance: 2.5\nPearson r: 1.0\nR-squared: 1.0\nSlope: 2.0\nIntercept: 0.0\nRMSE: 0.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 3\n2 2\n3 1",         'expected_output' => "n: 3\nMean X: 2.0\nMean Y: 2.0\nStd X: 0.8165\nStd Y: 0.8165\nCovariance: -0.6667\nPearson r: -1.0\nR-squared: 1.0\nSlope: -1.0\nIntercept: 4.0\nRMSE: 0.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2\n2 2\n3 2\n4 2",    'expected_output' => "n: 4\nMean X: 2.5\nMean Y: 2.0\nStd X: 1.118\nStd Y: 0.0\nCovariance: 0.0\nPearson r: 0.0\nR-squared: 0.0\nSlope: 0.0\nIntercept: 2.0\nRMSE: 0.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 1\n2 3\n3 2",         'expected_output' => "n: 3\nMean X: 2.0\nMean Y: 2.0\nStd X: 0.8165\nStd Y: 0.8165\nCovariance: 0.3333\nPearson r: 0.5\nR-squared: 0.25\nSlope: 0.5\nIntercept: 1.0\nRMSE: 0.7071", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q50: comparative group stats ──────────────────────────────────
        $seed(50, [
            ['input' => "4\n10\n12\n11\n13\n4\n20\n22\n21\n23", 'expected_output' => "Group A Mean: 11.5\nGroup A Median: 11.5\nGroup A SD: 1.2910\nGroup A IQR: 2.0\nGroup B Mean: 21.5\nGroup B Median: 21.5\nGroup B SD: 1.2910\nGroup B IQR: 2.0\nCohen d: -7.7460\nWelch t: -10.9545", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n3\n4\n5\n6",               'expected_output' => "Group A Mean: 2.0\nGroup A Median: 2.0\nGroup A SD: 0.8165\nGroup A IQR: 1.5\nGroup B Mean: 5.0\nGroup B Median: 5.0\nGroup B SD: 0.8165\nGroup B IQR: 1.5\nCohen d: -3.6742\nWelch t: -5.196",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n5\n5\n5\n5\n4\n5\n5\n5\n5",         'expected_output' => "Group A Mean: 5.0\nGroup A Median: 5.0\nGroup A SD: 0.0\nGroup A IQR: 0.0\nGroup B Mean: 5.0\nGroup B Median: 5.0\nGroup B SD: 0.0\nGroup B IQR: 0.0\nCohen d: 0.0\nWelch t: 0.0",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0\n10\n2\n100\n200",                 'expected_output' => "Group A Mean: 5.0\nGroup A Median: 5.0\nGroup A SD: 5.0\nGroup A IQR: 7.5\nGroup B Mean: 150.0\nGroup B Median: 150.0\nGroup B SD: 50.0\nGroup B IQR: 75.0\nCohen d: -2.9282\nWelch t: -2.9282", 'is_hidden' => true, 'order_index' => 4],
        ]);

        $this->command->info('✅ Module 2 Coding (Advanced) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}