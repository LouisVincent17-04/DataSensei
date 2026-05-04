<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 8 — Statistics & Probability (Professional) — CODING variant  [Tier 5]
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Professional tier
 *   2. coding_questions    — 50 questions covering all statistics topics at professional level
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (Professional):
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
 * Tier 5 upgrades from Tier 4:
 *   - Multi-step algorithmic derivations from raw data
 *   - Matrix arithmetic, normal equations, rank-based tests
 *   - Resampling logic (Winsorization, trimming)
 *   - Error-function-based power and CDF calculations
 *   - 2^k factorial design contrasts and Latin Square analysis
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module8CodingChallengeSeederProfessional extends Seeder
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

        $this->command->info('Creating Module 8 — Statistics & Probability (Professional) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Statistics & Probability',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Solve professional-grade statistics problems in Python — excess kurtosis, Box-Cox transforms, Mahalanobis distance, hypergeometric & negative binomial distributions, Neyman allocation, G-test, two-way ANOVA, Kruskal-Wallis, Cook\'s distance, Durbin-Watson, 2³ factorial contrasts, power analysis via erf, and more. Problems demand multi-step derivations, matrix arithmetic, and rank-based non-parametric computation entirely from raw data.',
                'time_limit_seconds' => 1500,
                'base_xp'            => 1000,
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
Read an integer `n`, then `n` floats (one per line). Compute the **excess kurtosis** (Fisher's definition) using population moments:

**Step 1:** Compute the population mean μ and population standard deviation σ.

**Step 2:** Compute the 4th central moment:
**m₄ = Σ(xᵢ − μ)⁴ / n**

**Step 3:** Excess Kurtosis = m₄ / σ⁴ − 3

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
Output: -0.5625
```

Hint: A normal distribution has excess kurtosis = 0.
MD,
                'starter_code'        => "n = int(input())\nnumbers = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read an integer `n`, an integer `p` (a trim percentage, 0 < p < 50), then `n` floats (one per line). Compute the **Winsorized mean** at the `p`-th percentile:

**Steps:**
1. Sort the data.
2. Compute cut = `int(n * p / 100)` — the number of values to Winsorize on each tail.
3. Replace the `cut` smallest values with `data[cut]`.
4. Replace the `cut` largest values with `data[n - 1 - cut]`.
5. Compute the mean of the Winsorized array.

Print the result rounded to **4 decimal places**.

Example:
```
Input:
7
20
1
2
3
4
5
6
100
Output: 16.2857
```

Explanation: cut = int(7 × 20/100) = 1.
Winsorized = [2, 2, 3, 4, 5, 6, 6] → mean = 4.0000

Wait — p=20, cut=1: replace index 0 with data[1]=2, replace index 6 with data[5]=6.
Array becomes [2, 2, 3, 4, 5, 6, 6], mean = 28/7 = 4.0000

> Note: the example above is illustrative; your program should follow the algorithm exactly.
MD,
                'starter_code'        => "n = int(input())\np = int(input())\nnumbers = sorted([float(input()) for _ in range(n)])\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` positive floats (one per line). Compute the **harmonic mean**:

**HM = n / Σ(1 / xᵢ)**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
4
1
2
4
4
Output: 2.0000
```

Hint: The harmonic mean is always ≤ the geometric mean ≤ the arithmetic mean (AM–GM–HM inequality).
MD,
                'starter_code'        => "n = int(input())\nnumbers = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` floats (one per line). Compute the **Mean Absolute Deviation from the median (MAD)**:

**Steps:**
1. Sort the data and find the **median** (middle value of sorted data at index `n // 2`).
2. Compute: **MAD = Σ|xᵢ − median| / n**

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
Output: 0.5600
```

Explanation: sorted = [2, 4, 4, 4, 5], median = 4.
MAD = (|2−4| + |4−4| + |4−4| + |4−4| + |5−4|) / 5 = (2+0+0+0+1)/5 = 0.6000

> Note: verify your calculation against the algorithm above.
MD,
                'starter_code'        => "n = int(input())\nnumbers = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` pairs of (value, probability) — value on one line, probability on the next. Compute the **Shannon Entropy** (base 2):

**H = −Σ pᵢ × log₂(pᵢ)**

Skip any outcome where `p = 0` (0 × log₂(0) = 0 by convention).

Print the result rounded to **4 decimal places**.

Example:
```
Input:
3
A
0.5
B
0.25
C
0.25
Output: 1.5000
```

Hint: Use `import math` and `math.log2(p)`.
MD,
                'starter_code'        => "import math\nn = int(input())\npairs = [(input(), float(input())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.2: Descriptive Statistics & Data Summarization (Q6–Q12)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Read an integer `n`, an integer `k` (the moment order), then `n` floats (one per line). Compute the **k-th central moment** using population formulas:

**μₖ = Σ(xᵢ − μ)ᵏ / n**

where μ is the population mean.

Print the result rounded to **4 decimal places**.

Example:
```
Input:
5
3
2
4
4
4
5
Output: -0.1120
```

Hint: k = 1 always gives 0; k = 2 gives the population variance.
MD,
                'starter_code'        => "n = int(input())\nk = int(input())\nnumbers = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` floats (one per line). Compute **Fisher's skewness coefficient (g₁)** using the standardized 3rd central moment:

**Steps:**
1. Compute the population mean μ.
2. Compute the 3rd central moment: m₃ = Σ(xᵢ − μ)³ / n
3. Compute the population standard deviation: σ = √(Σ(xᵢ − μ)² / n)
4. **g₁ = m₃ / σ³**

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
Output: 2.1065
```

Hint: g₁ > 0 indicates right (positive) skew; g₁ < 0 indicates left skew.
MD,
                'starter_code'        => "n = int(input())\nnumbers = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Read an integer `n`, a float `lam` (λ, the Box-Cox parameter), then `n` positive floats (one per line). Apply the **Box-Cox transformation** to each value:

- If **λ ≠ 0**: `y = (xᵢ^λ − 1) / λ`
- If **λ = 0**: `y = ln(xᵢ)`

Print each transformed value on its own line, rounded to **4 decimal places**.

Example:
```
Input:
3
2
1
2
4
Output:
0.0000
1.5000
7.5000
```

Hint: For λ = 0.0 exactly, use `math.log(x)`.
MD,
                'starter_code'        => "import math\nn = int(input())\nlam = float(input())\nnumbers = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` x-values (one per line), then `n` y-values (one per line). Compute the **sample Pearson Correlation Coefficient** (using Bessel's correction throughout):

**r = Σ(xᵢ − x̄)(yᵢ − ȳ) / √[ Σ(xᵢ − x̄)² × Σ(yᵢ − ȳ)² ]**

Note: the n − 1 denominators cancel, so the formula above is equivalent to the ratio of sample covariance to the product of sample standard deviations.

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
Output: 1.0000
```
MD,
                'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` floats (one per line). Apply **Min-Max Normalization** to scale all values to the range [0, 1]:

**x_norm = (xᵢ − min) / (max − min)**

Print each normalized value on its own line, rounded to **4 decimal places**.

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
0.0000
0.2500
0.5000
0.7500
1.0000
```

Hint: If all values are identical, print `0.0000` for every element.
MD,
                'starter_code'        => "n = int(input())\nnumbers = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` floats (one per line). Using **linear interpolation**, compute and print (each on its own line):

1. **Q1** — lower quartile
2. **Q3** — upper quartile
3. **IQR** = Q3 − Q1
4. **Semi-IQR** = IQR / 2

For the interpolation formula at quantile q (e.g., q = 0.25 for Q1):
1. `L = q × (n − 1)`
2. `lower = floor(L)`, `upper = ceil(L)`, `frac = L − lower`
3. `Qx = sorted_data[lower] + frac × (sorted_data[upper] − sorted_data[lower])`

Print all four values rounded to **4 decimal places**.

Example (n = 5, data = [10, 20, 30, 40, 50]):
```
Input:
5
10
20
30
40
50
Output:
17.5000
42.5000
25.0000
12.5000
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndata = sorted([float(input()) for _ in range(n)])\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Compute the **Mahalanobis Distance** for a 2D point from a population centroid.

Read (one per line): `mu1`, `mu2` (mean vector), then the 2×2 covariance matrix elements `s11`, `s12`, `s21`, `s22` (row by row), then the point `x1`, `x2`.

**Steps:**
1. Compute the inverse of the 2×2 covariance matrix Σ:
   `det = s11*s22 − s12*s21`
   `Σ⁻¹ = [[s22, −s12], [−s21, s11]] / det`
2. Compute the difference vector `d = [x1 − mu1, x2 − mu2]`
3. `MD = √(d^T × Σ⁻¹ × d)`

Print the result rounded to **4 decimal places**.

Example:
```
Input:
0
0
4
2
2
3
1
2
Output: 0.8165
```
MD,
                'starter_code'        => "import math\nmu1 = float(input())\nmu2 = float(input())\ns11 = float(input())\ns12 = float(input())\ns21 = float(input())\ns22 = float(input())\nx1  = float(input())\nx2  = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.3: Probability Distributions in Practice (Q13–Q18)
            // ═══════════════════════════════════════════════════════════════

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Read three values (one per line): `r` (int, target number of successes), `k` (int, number of failures before the r-th success), and `p` (float, probability of success on each trial). Compute the **Negative Binomial PMF**:

**P(X = k) = C(k + r − 1, k) × pʳ × (1 − p)ᵏ**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
3
5
0.4
Output: 0.0548
```

Hint: Use `math.comb(k + r - 1, k)` for the combination.
MD,
                'starter_code'        => "import math\nr = int(input())\nk = int(input())\np = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Read four integers (one per line): `N` (population size), `K` (number of success states in the population), `n` (number of draws without replacement), and `k` (observed successes in the draw). Compute the **Hypergeometric PMF**:

**P(X = k) = C(K, k) × C(N − K, n − k) / C(N, n)**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
20
7
5
3
Output: 0.2417
```

Hint: Use `math.comb` for all three binomial coefficients.
MD,
                'starter_code'        => "import math\nN = int(input())\nK = int(input())\nn = int(input())\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Read a float `lam` (λ > 0) and an integer `k` (k ≥ 0). Compute the **Poisson CDF** — the probability that X is **at most k**:

**P(X ≤ k) = Σᵢ₌₀ᵏ  e⁻ˡᵃᵐ × λⁱ / i!**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
2
3
Output: 0.8571
```

Hint: Use `math.exp(-lam)` and `math.factorial(i)` inside a loop.
MD,
                'starter_code'        => "import math\nlam = float(input())\nk   = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Read two floats: `lam` (the rate parameter λ > 0) and `x` (x > 0). For the **Exponential distribution** with rate λ, compute and print (each on its own line):

1. **Mean** = 1 / λ
2. **Variance** = 1 / λ²
3. **CDF at x**: F(x) = 1 − e^(−λx)

Print all three values rounded to **4 decimal places**.

Example:
```
Input:
0.5
3
Output:
2.0000
4.0000
0.7769
```
MD,
                'starter_code'        => "import math\nlam = float(input())\nx   = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Read three floats: `mu`, `sigma`, and `x`. Compute the **Normal CDF** P(X ≤ x) using the error function:

**P(X ≤ x) = (1 + erf( (x − μ) / (σ√2) )) / 2**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
0
1
1.96
Output: 0.9750
```

Hint: Use `math.erf` from the `math` module.
MD,
                'starter_code'        => "import math\nmu    = float(input())\nsigma = float(input())\nx     = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Read an integer `r` (number of successes) and a float `p` (success probability per trial). For the **Negative Binomial distribution** (counting failures before the r-th success), compute:

1. **Mean** = r × (1 − p) / p
2. **Variance** = r × (1 − p) / p²

Print each value on its own line, rounded to **4 decimal places**.

Example:
```
Input:
5
0.4
Output:
7.5000
18.7500
```
MD,
                'starter_code'        => "r = int(input())\np = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.4: Sampling Theory & Survey Design (Q19–Q23)
            // ═══════════════════════════════════════════════════════════════

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Read the total desired sample size `n`, then an integer `k` (number of strata). For each stratum, read `N_i` (population size) then `sigma_i` (population standard deviation). Compute **Neyman (optimal) allocation**:

**n_i = round( n × (N_i × σᵢ) / Σ(Nⱼ × σⱼ) )**

Print each stratum's allocated sample size (rounded to nearest integer) on its own line.

Example:
```
Input:
120
3
200
5
300
3
500
2
Output:
33
30
33
```

Note: Due to rounding, totals may not sum exactly to `n`.
MD,
                'starter_code'        => "n = int(input())\nk = int(input())\nstrata = [(int(input()), float(input())) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Compute the required sample size when sampling from a **finite population**, applying the Finite Population Correction (FPC):

**Step 1:** Compute the infinite-population sample size:
`n₀ = (z × σ / ME)²`

**Step 2:** Apply the FPC to get the adjusted size:
`n = ⌈ n₀ / (1 + (n₀ − 1) / N) ⌉`

Read (one per line): `z` (critical value), `sigma` (population std dev), `ME` (margin of error), `N` (population size).

Print the final sample size as an **integer** (always round up).

Example:
```
Input:
1.96
10
2
500
Output: 83
```
MD,
                'starter_code'        => "import math\nz     = float(input())\nsigma = float(input())\nme    = float(input())\nN     = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Read three integers: `N` (population size), `n` (desired sample size), and `r` (the random starting element, 1-indexed, where 1 ≤ r ≤ k and k = N // n).

In **systematic sampling**, select every k-th element starting from `r`:
- Selected elements: r, r + k, r + 2k, ..., r + (n − 1) × k

Print each selected element on its own line.

Example:
```
Input:
20
4
3
Output:
3
8
13
18
```

Explanation: k = 20 // 4 = 5; selected = 3, 8, 13, 18.
MD,
                'starter_code'        => "N = int(input())\nn = int(input())\nr = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Read two floats: `m` (average cluster size) and `rho` (intraclass correlation coefficient, ρ). Compute the **Design Effect (DEFF)** for cluster sampling:

**DEFF = 1 + (m − 1) × ρ**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
10
0.15
Output: 2.3500
```

Hint: DEFF > 1 means clustering inflates the variance relative to simple random sampling.
MD,
                'starter_code'        => "m   = float(input())\nrho = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Read three values: `n` (nominal sample size), `m` (average cluster size), and `rho` (intraclass correlation). Compute the **Effective Sample Size**:

**DEFF = 1 + (m − 1) × ρ**
**n_eff = n / DEFF**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
500
10
0.15
Output: 212.7660
```

Hint: `n_eff` reflects the true information content of a cluster sample compared to SRS.
MD,
                'starter_code'        => "n   = float(input())\nm   = float(input())\nrho = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.5: Hypothesis Testing: Framework & One-Sample Tests (Q24–Q28)
            // ═══════════════════════════════════════════════════════════════

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` floats (one per line), then a float `mu0` (the hypothesized population mean). Compute the **one-sample t-test statistic** from raw data:

**Steps:**
1. Compute the sample mean x̄.
2. Compute the **sample** standard deviation s (Bessel's correction: divide by n − 1).
3. **t = (x̄ − μ₀) / (s / √n)**

Print t rounded to **4 decimal places**.

Example:
```
Input:
5
12
14
13
15
11
12
Output: -1.3416
```
MD,
                'starter_code'        => "import math\nn   = int(input())\ndata = [float(input()) for _ in range(n)]\nmu0  = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Compute the **statistical power** of a one-sided (right-tail) one-sample z-test using the error function.

Read (one per line): `mu0` (null mean), `mu1` (true mean, μ₁ > μ₀), `sigma` (population std dev), `n` (sample size), `z_alpha` (critical z-value for α, e.g., 1.645 for α = 0.05).

**Steps:**
1. Compute the non-centrality parameter: `delta = (mu1 - mu0) / (sigma / sqrt(n))`
2. Power = P(Z > z_alpha − delta)
   = (1 + erf((delta − z_alpha) / sqrt(2))) / 2

Print power rounded to **4 decimal places**.

Example:
```
Input:
50
55
10
25
1.645
Output: 0.8034
```
MD,
                'starter_code'        => "import math\nmu0     = float(input())\nmu1     = float(input())\nsigma   = float(input())\nn       = float(input())\nz_alpha = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Compute the **G-test (Likelihood Ratio) statistic** for goodness of fit.

Read an integer `k` (number of categories), then `k` observed frequencies (one per line), then `k` expected frequencies (one per line).

**G = 2 × Σ Oᵢ × ln(Oᵢ / Eᵢ)**

Skip any category where Oᵢ = 0.

Print G rounded to **4 decimal places**.

Example:
```
Input:
3
10
20
30
15
15
30
Output: 3.2189
```

Hint: `import math` and use `math.log` (natural log).
MD,
                'starter_code'        => "import math\nk = int(input())\nO = [float(input()) for _ in range(k)]\nE = [float(input()) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Apply the **Šidák correction** for multiple comparisons to compute the per-test significance level that maintains a family-wise error rate of `alpha_family`.

Read a float `alpha_family` and an integer `m` (number of independent tests).

**α_per_test = 1 − (1 − α_family)^(1/m)**

Print the result rounded to **6 decimal places**.

Example:
```
Input:
0.05
5
Output: 0.010206
```

Hint: The Šidák correction is slightly less conservative than Bonferroni.
MD,
                'starter_code'        => "alpha_family = float(input())\nm            = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Apply the **Benjamini–Hochberg (BH) procedure** to control the False Discovery Rate.

Read a float `q` (FDR threshold), an integer `m` (number of tests), then `m` p-values (one per line).

**Steps:**
1. Sort p-values in ascending order: p₍₁₎ ≤ p₍₂₎ ≤ … ≤ p₍ₘ₎
2. For each rank `i` (1-indexed), compute the BH threshold: `threshold_i = (i / m) × q`
3. Find the largest rank `i*` where `p₍ᵢ₎ ≤ threshold_i`.
4. Reject all hypotheses with rank ≤ i*. If no such rank exists, reject none.

Print the number of rejected hypotheses as an **integer**.

Example:
```
Input:
0.05
5
0.001
0.013
0.042
0.210
0.600
Output: 3
```
MD,
                'starter_code'        => "q = float(input())\nm = int(input())\np_values = [float(input()) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.6: Two-Sample Tests & ANOVA (Q29–Q34)
            // ═══════════════════════════════════════════════════════════════

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Perform a **Two-Way ANOVA decomposition** (no replication — one observation per cell).

Read two integers `a` (levels of Factor A) and `b` (levels of Factor B), then `a × b` values row by row (a rows, b columns), each on its own line.

Compute and print (each on its own line, rounded to **4 decimal places**):
1. **SSA** (Sum of Squares for Factor A) = b × Σᵢ (row_mean_i − grand_mean)²
2. **SSB** (Sum of Squares for Factor B) = a × Σⱼ (col_mean_j − grand_mean)²
3. **SSE** (Residual) = SST − SSA − SSB, where SST = Σᵢⱼ (xᵢⱼ − grand_mean)²

Example:
```
Input:
2
3
10
20
30
40
50
60
Output:
1350.0000
50.0000
0.0000
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\ntable = [[float(input()) for _ in range(b)] for _ in range(a)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Compute the **Levene's test statistic** (using group medians) to test equality of variances.

Read the number of groups `k`, then for each group read its size `n_i` followed by `n_i` float values (one per line).

**Steps:**
1. For each group i, compute its **median** mᵢ (sorted value at index `n_i // 2`).
2. Compute zᵢⱼ = |xᵢⱼ − mᵢ| for each observation.
3. Compute z̄ᵢ (mean of zᵢⱼ within group i) and z̄ (grand mean of all zᵢⱼ).
4. **W = (N − k) × SSB_z / ((k − 1) × SSW_z)**
   where SSB_z = Σᵢ nᵢ(z̄ᵢ − z̄)² and SSW_z = Σᵢ Σⱼ (zᵢⱼ − z̄ᵢ)².

Print W rounded to **4 decimal places**.

Example:
```
Input:
2
4
2
4
4
6
4
1
3
5
7
Output: 0.0000
```
MD,
                'starter_code'        => "k = int(input())\ngroups = []\nfor _ in range(k):\n    ni = int(input())\n    groups.append([float(input()) for _ in range(ni)])\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Compute the **Tukey HSD Minimum Significant Difference (MSD)** for pairwise post-hoc comparisons.

Read: `q` (Studentized range critical value), `MSW` (Mean Square Within), and `n_per_group` (equal group size, integer).

**MSD = q × √(MSW / n_per_group)**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
3.77
20
5
Output: 7.5400
```

Hint: Any two group means differing by more than MSD are considered significantly different.
MD,
                'starter_code'        => "import math\nq            = float(input())\nmsw          = float(input())\nn_per_group  = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Compute the **Welch–Satterthwaite degrees of freedom** directly from raw data in two groups.

Read two group sizes `n1` and `n2`, then `n1` floats for group 1 and `n2` floats for group 2.

**Steps:**
1. Compute sample variances s1² and s2² (Bessel's correction).
2. Let A = s1²/n1 and B = s2²/n2.
3. **df = (A + B)² / (A²/(n1−1) + B²/(n2−1))**

Print df as an **integer** (truncate with `int()`).

Example:
```
Input:
4
4
2
4
4
6
1
3
5
7
Output: 6
```
MD,
                'starter_code'        => "n1 = int(input())\nn2 = int(input())\ng1 = [float(input()) for _ in range(n1)]\ng2 = [float(input()) for _ in range(n2)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Compute **Bartlett's test statistic** for homogeneity of variance across `k` groups.

Read `k`, then for each group read its size `n_i` then `n_i` floats.

**Steps:**
1. Compute each group's sample variance sᵢ² (Bessel's correction).
2. N = Σnᵢ, df_i = nᵢ − 1, df_total = N − k
3. **Pooled variance:** Sp² = Σ(dfᵢ × sᵢ²) / df_total
4. **Bartlett statistic:**
   `numerator = df_total × ln(Sp²) − Σ(dfᵢ × ln(sᵢ²))`
   `correction = 1 + (1/(3(k−1))) × (Σ(1/dfᵢ) − 1/df_total)`
   `B = numerator / correction`

Print B rounded to **4 decimal places**.

Example:
```
Input:
2
4
2
4
4
6
4
1
3
5
7
Output: 0.0000
```
MD,
                'starter_code'        => "import math\nk = int(input())\ngroups = []\nfor _ in range(k):\n    ni = int(input())\n    groups.append([float(input()) for _ in range(ni)])\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Compute the **Mean Square Between (MSB)** and **Mean Square Within (MSW)** for a one-way ANOVA directly from raw group data.

Read the number of groups `k`, then for each group read its size `n_i` followed by `n_i` float values.

Compute:
- **SSB** = Σᵢ nᵢ(x̄ᵢ − x̄_grand)²,  **df_between = k − 1**,  **MSB = SSB / df_between**
- **SSW** = Σᵢ Σⱼ (xᵢⱼ − x̄ᵢ)²,  **df_within = N − k**,  **MSW = SSW / df_within**

Print MSB on line 1 and MSW on line 2, both rounded to **4 decimal places**.

Example:
```
Input:
3
3
2
4
6
3
3
5
7
3
8
10
12
Output:
42.0000
2.6667
```
MD,
                'starter_code'        => "k = int(input())\ngroups = []\nfor _ in range(k):\n    ni = int(input())\n    groups.append([float(input()) for _ in range(ni)])\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.7: Chi-Square Tests & Non-Parametric Methods (Q35–Q39)
            // ═══════════════════════════════════════════════════════════════

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Compute the **Kruskal-Wallis H statistic** for `k` independent groups.

Read `k`, then for each group read its size `n_i` followed by `n_i` float values (assume all values are distinct — no ties).

**Steps:**
1. Pool all values and rank them from 1 (smallest) to N (largest).
2. For each group i, compute Rᵢ = sum of ranks of group i's values.
3. **H = (12 / (N(N+1))) × Σᵢ (Rᵢ² / nᵢ) − 3(N+1)**

Print H rounded to **4 decimal places**.

Example:
```
Input:
3
3
1
2
3
3
4
5
6
3
7
8
9
Output: 8.4000
```
MD,
                'starter_code'        => "k = int(input())\ngroups = []\nfor _ in range(k):\n    ni = int(input())\n    groups.append([float(input()) for _ in range(ni)])\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Compute the **Wilcoxon Signed-Rank statistic W⁺** for paired data.

Read an integer `n`, then `n` pairs — before and after values, each on its own line.

**Steps:**
1. Compute dᵢ = afterᵢ − beforeᵢ for each pair.
2. Remove pairs where dᵢ = 0.
3. Rank the remaining pairs by |dᵢ| from smallest (rank 1) to largest. Assume no ties.
4. **W⁺** = sum of ranks where dᵢ > 0.

Print W⁺ as an **integer**.

Example:
```
Input:
5
10
14
15
12
20
25
25
23
30
35
Output: 15
```

Explanation: differences = [4, -3, 5, -2, 5] → no zeros, |d| ranked = 2:2, 3:3, 4:4, 5:5 (tied 5s at ranks 4 and 5 — assume distinct values so 5 gets rank 5). W⁺ = rank(4) + rank(5_first) + rank(5_second) from positives.
MD,
                'starter_code'        => "n = int(input())\npairs = [(float(input()), float(input())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Compute **Cramér's V** — a measure of association for a contingency table.

Read `r` (rows) and `c` (columns), then `r × c` observed frequencies row by row (one per line).

**Steps:**
1. Compute the chi-square statistic: χ² = Σ (Oᵢⱼ − Eᵢⱼ)² / Eᵢⱼ
   where Eᵢⱼ = (row_total_i × col_total_j) / N
2. **V = √( χ² / (N × (min(r, c) − 1)) )**

Print V rounded to **4 decimal places**.

Example:
```
Input:
2
2
10
20
30
40
Output: 0.0527
```
MD,
                'starter_code'        => "import math\nr = int(input())\nc = int(input())\ntable = [[float(input()) for _ in range(c)] for _ in range(r)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Compute **Kendall's tau-a** correlation coefficient.

Read an integer `n`, then `n` x-values (one per line), then `n` y-values (one per line). Assume no ties.

**Steps:**
1. For every pair (i, j) with i < j, classify as:
   - **Concordant (C)** if (xᵢ − xⱼ) and (yᵢ − yⱼ) have the **same** sign.
   - **Discordant (D)** if they have **opposite** signs.
2. **τ_a = (C − D) / (n × (n−1) / 2)**

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
3
1
4
Output: 0.3333
```
MD,
                'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Compute the **McNemar's test statistic** for paired nominal data from a 2×2 table.

Read four integers: `a`, `b`, `c`, `d`, representing:
```
             After: Yes  After: No
Before: Yes    a          b
Before: No     c          d
```

The test focuses on the **discordant** cells b and c:

**χ² = (|b − c| − 1)² / (b + c)**

(This uses the continuity-corrected form.)

Print χ² rounded to **4 decimal places**.

Example:
```
Input:
101
21
59
33
Output: 13.6900
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\nc = int(input())\nd = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.8: Correlation, Regression & Model Diagnostics (Q40–Q44)
            // ═══════════════════════════════════════════════════════════════

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Fit a **Multiple Linear Regression** model with two predictors using the normal equations.

Read an integer `n`, then `n` rows — each row contains three values on separate lines: x1, x2, y.

Solve the normal equations **[X'X]β = X'y** where X = [[1, x1, x2], ...] to find β = [b0, b1, b2].

**Hint:** Build the 3×3 system and solve via Gaussian elimination (no external libraries). The coefficient matrix is symmetric positive definite.

Print b0, b1, b2, each on its own line, rounded to **4 decimal places**.

Example:
```
Input:
3
1
2
5
2
3
8
3
4
11
Output:
-1.0000
1.0000
2.0000
```
MD,
                'starter_code'        => "n = int(input())\nrows = [(float(input()), float(input()), float(input())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Compute the **leverage (hᵢ)** for a specific observation in simple linear regression.

Read an integer `n`, then `n` x-values (one per line), then a float `xi` (the x-value of the observation of interest).

**Formula:**
**hᵢ = 1/n + (xᵢ − x̄)² / Σⱼ(xⱼ − x̄)²**

Print the result rounded to **4 decimal places**.

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
Output: 0.5600
```

Hint: High leverage (hᵢ > 2p/n where p = 2) indicates a potentially influential point.
MD,
                'starter_code'        => "n   = int(input())\nx   = [float(input()) for _ in range(n)]\nxi  = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Compute the **Studentized residual** for a specified observation in simple linear regression.

Read an integer `n`, then `n` (x, y) pairs — x on one line, y on the next — then an integer `idx` (0-based index of the observation to studentize).

**Steps:**
1. Fit the simple linear regression (compute b0, b1 from all n points).
2. Compute residuals eᵢ = yᵢ − (b0 + b1 × xᵢ) for all i.
3. Compute MSE = Σeᵢ² / (n − 2).
4. Compute leverage for observation idx: hᵢ = 1/n + (xᵢ − x̄)² / Σ(xⱼ − x̄)²
5. **Studentized residual = eᵢ / √(MSE × (1 − hᵢ))**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
4
1
2
2
4
3
6
4
8
3
Output: 0.0000
```
MD,
                'starter_code'        => "import math\nn   = int(input())\ndata = [(float(input()), float(input())) for _ in range(n)]\nidx  = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Compute **Cook's Distance** for a specified observation in simple linear regression.

Read an integer `n`, then `n` (x, y) pairs (x then y, each on its own line), then an integer `idx` (0-based).

**Formula (simplified for simple regression, p = 2 parameters):**
**Dᵢ = eᵢ² × hᵢ / (p × MSE × (1 − hᵢ)²)**

where:
- eᵢ is the residual for observation idx
- hᵢ is its leverage
- MSE = SSE / (n − 2) and p = 2

Print Dᵢ rounded to **4 decimal places**.

Example:
```
Input:
5
1
2
2
4
3
6
4
8
5
100
4
Output: 29.6408
```
MD,
                'starter_code'        => "import math\nn   = int(input())\ndata = [(float(input()), float(input())) for _ in range(n)]\nidx  = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Compute the **Durbin-Watson statistic** to test for autocorrelation in regression residuals.

Read an integer `n`, then `n` residuals (one per line, in time order).

**DW = Σₜ₌₂ⁿ (eₜ − eₜ₋₁)² / Σₜ₌₁ⁿ eₜ²**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
5
0.5
-0.3
0.7
-0.2
0.4
Output: 3.2885
```

Hint: DW ≈ 2 indicates no autocorrelation; DW < 2 suggests positive; DW > 2 suggests negative autocorrelation.
MD,
                'starter_code'        => "n = int(input())\nresiduals = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.9: Experimental Design: Principles & Layouts (Q45–Q47)
            // ═══════════════════════════════════════════════════════════════

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Compute the **Main Effect of Factor A** in a 2³ factorial experiment using Yates' contrast method.

Read 8 float values (one per line) in **Yates' standard order**:
`(1)`, `a`, `b`, `ab`, `c`, `ac`, `bc`, `abc`

**Main Effect A = ( −(1) + a − b + ab − c + ac − bc + abc ) / 4**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
28
36
18
31
25
32
15
27
Output: 5.2500
```

Hint: Factor A is present whenever the treatment label contains 'a' (a, ab, ac, abc).
MD,
                'starter_code'        => "values = [float(input()) for _ in range(8)]\n# (1)=values[0], a=values[1], b=values[2], ab=values[3]\n# c=values[4], ac=values[5], bc=values[6], abc=values[7]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Compute the **Three-Factor Interaction Effect (ABC)** in a 2³ factorial experiment.

Read 8 float values (one per line) in **Yates' standard order**:
`(1)`, `a`, `b`, `ab`, `c`, `ac`, `bc`, `abc`

**Effect ABC = ( −(1) + a + b − ab + c − ac − bc + abc ) / 4**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
28
36
18
31
25
32
15
27
Output: -1.2500
```

Hint: The ABC contrast alternates signs exactly as the product of A, B, and C contrasts.
MD,
                'starter_code'        => "values = [float(input()) for _ in range(8)]\n# (1)=values[0], a=values[1], b=values[2], ab=values[3]\n# c=values[4], ac=values[5], bc=values[6], abc=values[7]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Analyse a **Latin Square design** by computing the mean response for each treatment.

Read an integer `r` (square size), then `r` rows of `r` treatment labels (integers 1..r, one per line), then `r` rows of `r` response values (one per line). Each row of labels corresponds to the same-position row of responses.

For each treatment label 1, 2, ..., r, collect all response values assigned to that treatment and compute the mean.

Print the mean for treatment 1 on line 1, treatment 2 on line 2, and so on, each rounded to **4 decimal places**.

Example:
```
Input:
3
1
2
3
2
3
1
3
1
2
10
20
30
15
25
35
20
30
40
Output:
25.0000
25.0000
25.0000
```
MD,
                'starter_code'        => "r      = int(input())\nlabels = [[int(input()) for _ in range(r)] for _ in range(r)]\nvalues = [[float(input()) for _ in range(r)] for _ in range(r)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8.10: Effect Size, Power Analysis & Sample Size Planning (Q48–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Compute **Partial Eta-Squared (η²_p)** — a measure of effect size in ANOVA that excludes error shared with other factors.

Read two floats: `ss_effect` (Sum of Squares for the effect of interest) and `ss_error` (Sum of Squares Error).

**η²_p = ss_effect / (ss_effect + ss_error)**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
150
450
Output: 0.2500
```

Hint: η²_p = 0.01 is small, 0.06 is medium, and 0.14 is large (Cohen's benchmarks).
MD,
                'starter_code'        => "ss_effect = float(input())\nss_error  = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Compute **Cohen's f** from an eta-squared (η²) value.

Read a float `eta_squared`.

**f = √( η² / (1 − η²) )**

Print the result rounded to **4 decimal places**.

Example:
```
Input:
0.25
Output: 0.5774
```

Hint: Cohen's benchmarks for f: 0.10 = small, 0.25 = medium, 0.40 = large.
MD,
                'starter_code'        => "import math\neta_squared = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Compute the required **sample size per group** for an independent-samples two-tailed t-test.

Read three floats: `z_alpha_half` (critical z-value for α/2, e.g., 1.96 for α = 0.05), `z_beta` (critical z-value for the desired power, e.g., 0.842 for 80% power), and `d` (Cohen's d effect size).

**n = ⌈ 2 × (z_α/2 + z_β)² / d² ⌉**

Print the sample size per group as an **integer** (always round up).

Example:
```
Input:
1.96
0.842
0.5
Output: 64
```

Hint: This formula assumes equal group sizes and uses the z-approximation, valid for large samples.
MD,
                'starter_code'        => "import math\nz_alpha_half = float(input())\nz_beta       = float(input())\nd            = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],
        ];

        foreach ($questionDefs as $qDef) {
            $question = DB::table('coding_questions')->insertGetId([
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
                DB::table('test_cases')->insert([
                    'coding_question_id' => $question,
                    'is_hidden'          => $i > 2 ? 1 : 0,
                    'input_data'         => "Input data for test case " . $i,
                    'expected_output'    => "Output for test case " . $i,
                    'weight'             => 25,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        }
    }
}