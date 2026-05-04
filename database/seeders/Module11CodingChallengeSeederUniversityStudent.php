<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 11 — Bayesian Statistics (University Student) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the University Student tier
 *   2. coding_questions    — 50 questions covering intermediate Bayesian topics
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (University Student):
 *   11.1  Probability Distributions & PDFs
 *   11.2  Grid Approximation (Single Parameter)
 *   11.3  Advanced Conjugate Priors (Normal-Normal, Gamma-Poisson)
 *   11.4  Loss Functions & Point Estimates
 *   11.5  Empirical Inference & Intervals
 *   11.6  Markov Chains (Discrete State)
 *   11.7  Metropolis-Hastings (Basics)
 *   11.8  Bayesian Linear & Logistic Regression (Matrices)
 *   11.9  Information Criteria (AIC, BIC, LogSumExp)
 *   11.10 Mixture Models & Entropy
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module11CodingChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'university-student')
            ->orWhere('slug', 'university')
            ->orWhere('slug', 'intermediate')
            ->first();

        if (! $category) {
            $this->command->error('University Student category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 11 — Bayesian Statistics (University Student) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Bayesian Statistics',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Elevate your Bayesian analysis skills by writing Python scripts to compute continuous probability density functions (PDFs), perform grid approximation, implement Markov Chains, apply Bayesian decision theory with loss functions, and evaluate models using information criteria. Tasks require logical manipulation, the math module, and a deeper understanding of probabilistic modeling.',
                'time_limit_seconds' => 1200,
                'base_xp'            => 1000,
                'order_index'        => 11,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.1: Probability Distributions & PDFs (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
The **Probability Density Function (PDF)** of the Normal distribution is:

`f(x) = (1 / (σ × √(2π))) × exp( -0.5 × ((x - μ) / σ)² )`

Read `mu` (float), `sigma` (float), and `x` (float), one per line. Compute and print `f(x)` rounded to **4 decimal places**.

Example:
Input:
0.0
1.0
0.0
Output: 0.3989

MD,
                'starter_code'        => "import math\nmu = float(input())\nsigma = float(input())\nx = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
The **Exponential PDF** is:

`f(x) = λ × exp(-λ × x)`  for `x >= 0` (and `0` for `x < 0`).

Read `lambda_val` (float) and `x` (float), one per line. Print `f(x)` rounded to **4 decimal places**.

Example:
Input:
2.0
1.0
Output: 0.2707

MD,
                'starter_code'        => "import math\nlambda_val = float(input())\nx = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
The **Uniform Cumulative Distribution Function (CDF)** over `[a, b]` is:
- `0` if `x < a`
- `(x - a) / (b - a)` if `a <= x <= b`
- `1` if `x > b`

Read `a`, `b`, and `x` (floats, one per line). Print the CDF rounded to **4 decimal places**.

Example:
Input:
0.0
10.0
5.0
Output: 0.5000

MD,
                'starter_code'        => "a = float(input())\nb = float(input())\nx = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
The **Normal CDF** can be computed using the error function (`math.erf`):

`CDF(x) = 0.5 × (1 + erf((x - μ) / (σ × √2)))`

Read `mu`, `sigma`, and `x` (floats). Print the CDF rounded to **4 decimal places**.

Example:
Input:
0.0
1.0
1.96
Output: 0.9750

MD,
                'starter_code'        => "import math\nmu = float(input())\nsigma = float(input())\nx = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
**Inverse Transform Sampling**: To generate a random sample `x` from an Exponential distribution using a uniform random variable `u` in `(0, 1)`:

`x = -ln(1 - u) / λ`

Read `lambda_val` and `u` (floats). Print `x` rounded to **4 decimal places**.

Example:
Input:
2.0
0.5
Output: 0.3466

MD,
                'starter_code'        => "import math\nlambda_val = float(input())\nu = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.2: Grid Approximation (Single Parameter) (Q6–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
**Grid Approximation**: Generate `n` evenly spaced points (a grid) between `start` and `end` (inclusive).

Read `start` (float), `end` (float), and `n` (int). Print each grid point on a single line separated by spaces, rounded to **4 decimal places**.

Example:
Input:
0.0
1.0
3
Output: 0.0000 0.5000 1.0000

MD,
                'starter_code'        => "start = float(input())\nend = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Calculate the **Binomial likelihood** over a grid of probabilities.
Read `n` (trials) and `k` (successes). Then read `m` (number of grid points), followed by `m` grid points representing probability `p`.
Print the **maximum likelihood value** found across the grid, rounded to **4 decimal places**.

(Likelihood = `(n! / (k!(n-k)!)) × p^k × (1-p)^(n-k)`)

Example:
Input:
10
7
3
0.5
0.7
0.9
Output: 0.2668

MD,
                'starter_code'        => "import math\nn = int(input())\nk = int(input())\nm = int(input())\ngrid = [float(input()) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Compute the sum of the **unnormalized posterior** over a grid.
Assume a uniform prior `P(p) = 1` for all grid points.
Read `m` (int), then `m` likelihood values. The unnormalized posterior is simply `prior × likelihood`.
Print the sum of the unnormalized posteriors across the grid, rounded to **4 decimal places**.

Example:
Input:
3
0.1
0.2
0.3
Output: 0.6000

MD,
                'starter_code'        => "m = int(input())\nlikelihoods = [float(input()) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Normalize an unnormalized posterior distribution.
Read `m` (int), then `m` unnormalized posterior values.
Divide each value by the sum of all values. Find and print the **maximum normalized probability**, rounded to **4 decimal places**.

Example:
Input:
3
0.1
0.2
0.3
Output: 0.5000

MD,
                'starter_code'        => "m = int(input())\nunnorm_post = [float(input()) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Find the **Maximum A Posteriori (MAP)** estimate from a grid.
Read `m` (int).
Then read `m` grid parameter values `theta`.
Then read `m` corresponding unnormalized posterior values.
Print the `theta` that corresponds to the **highest** posterior value, rounded to **4 decimal places**.

Example:
Input:
2
0.1
0.2
0.4
0.6
Output: 0.2000

MD,
                'starter_code'        => "m = int(input())\nthetas = [float(input()) for _ in range(m)]\nposts = [float(input()) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.3: Advanced Conjugate Priors (Q11–Q15)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
For a **Normal-Normal** conjugate model (known variance), we often work with precision `τ = 1/σ²`.

`posterior_precision = prior_precision + n × data_precision`

Read `prior_tau` (float), `n` (int), and `data_tau` (float). Print the posterior precision rounded to **4 decimal places**.

Example:
Input:
1.0
10
2.0
Output: 21.0000

MD,
                'starter_code'        => "prior_tau = float(input())\nn = int(input())\ndata_tau = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
For a **Normal-Normal** conjugate model, the posterior mean is precision-weighted:

`μ_post = (τ_prior × μ_prior + n × τ_data × x_bar) / τ_post`

Read `tau_prior`, `mu_prior`, `n` (int), `tau_data`, and `x_bar`. Print the posterior mean rounded to **4 decimal places**.

Example:
Input:
1.0
0.0
10
2.0
5.0
Output: 4.7619

MD,
                'starter_code'        => "tau_prior = float(input())\nmu_prior = float(input())\nn = int(input())\ntau_data = float(input())\nx_bar = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
For the **Gamma-Poisson** conjugate model, the posterior shape parameter `α` is updated as:

`α_post = α_prior + Σ(x_i)`

Read `alpha_prior` (float), `n` (int), and then `n` integer observations `x`. Print the posterior shape parameter rounded to **4 decimal places**.

Example:
Input:
2.0
3
5
5
5
Output: 17.0000

MD,
                'starter_code'        => "alpha_prior = float(input())\nn = int(input())\nx_vals = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
For the **Gamma-Poisson** conjugate model, the posterior rate parameter `β` is updated as:

`β_post = β_prior + n`

Read `beta_prior` (float) and `n` (int). Print the posterior rate parameter rounded to **4 decimal places**.

Example:
Input:
1.0
5
Output: 6.0000

MD,
                'starter_code'        => "beta_prior = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
The **Beta-Binomial predictive probability** of the next trial being a success, given previous data, is:

`P(x_new = 1) = (α_prior + successes) / (α_prior + β_prior + n)`

Read `alpha_prior`, `beta_prior`, `n` (total trials), and `k` (successes). Print the predictive probability rounded to **4 decimal places**.

Example:
Input:
2.0
2.0
10
6
Output: 0.5714

MD,
                'starter_code'        => "alpha_prior = float(input())\nbeta_prior = float(input())\nn = int(input())\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.4: Loss Functions & Point Estimates (Q16–Q20)
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
**Quadratic Loss** (L2 loss) penalizes larger errors exponentially and is minimized by the posterior mean.

`Loss = (y_actual - y_pred)²`

Read `y_actual` and `y_pred`. Print the quadratic loss rounded to **4 decimal places**.

Example:
Input:
10.0
8.0
Output: 4.0000

MD,
                'starter_code'        => "y_actual = float(input())\ny_pred = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
**Absolute Loss** (L1 loss) penalizes errors linearly and is minimized by the posterior median.

`Loss = |y_actual - y_pred|`

Read `y_actual` and `y_pred`. Print the absolute loss rounded to **4 decimal places**.

Example:
Input:
10.0
8.0
Output: 2.0000

MD,
                'starter_code'        => "y_actual = float(input())\ny_pred = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
**Zero-One Loss** is 0 if the prediction is exactly right (or within a tight tolerance), and 1 otherwise. It is minimized by the MAP estimate.

Read `y_actual`, `y_pred`, and `tolerance`. If `|y_actual - y_pred| <= tolerance`, the loss is 0. Otherwise, 1.
Print the integer loss.

Example:
Input:
10.0
8.0
1.0
Output: 1

MD,
                'starter_code'        => "y_actual = float(input())\ny_pred = float(input())\ntol = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
**Expected Loss**: Sum of `Probability(y) × Loss(y, y_pred)`.

Read `n` (int). Then read `n` probability values, followed by `n` loss values. Compute the expected loss and print it rounded to **4 decimal places**.

Example:
Input:
2
0.2
0.8
10.0
5.0
Output: 6.0000

MD,
                'starter_code'        => "n = int(input())\nprobs = [float(input()) for _ in range(n)]\nlosses = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
**Asymmetric Linear Loss** (LinEx Loss) penalizes overestimation and underestimation differently.

If `y_pred > y_actual`, Loss = `c × (y_pred - y_actual)`
If `y_pred <= y_actual`, Loss = `y_actual - y_pred`

Read `c`, `y_actual`, and `y_pred`. Print the loss rounded to **4 decimal places**.

Example:
Input:
2.0
10.0
12.0
Output: 4.0000

MD,
                'starter_code'        => "c = float(input())\ny_actual = float(input())\ny_pred = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.5: Empirical Inference & Intervals (Q21–Q25)
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
**Empirical Transformations**: MCMC allows computing expectations of transformed variables easily.
Calculate the **empirical mean of the natural logarithm** of posterior samples.

Read `n` (int), then `n` samples. Compute `mean(ln(x))` and print it rounded to **4 decimal places**.

Example:
Input:
2
1.0
2.718281828
Output: 0.5000

MD,
                'starter_code'        => "import math\nn = int(input())\nsamples = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
**Monte Carlo Standard Error (MCSE)** estimates the uncertainty in the posterior mean due to sampling:

`MCSE = sample_std_dev / √n`

Read `std_dev` (float) and `n` (int). Print the MCSE rounded to **4 decimal places**.

Example:
Input:
5.0
100
Output: 0.5000

MD,
                'starter_code'        => "import math\nstd_dev = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Compute the **empirical probability** that the parameter falls in a specific range `(a < x < b)`.

Read `n` (int). Then read `n` samples. Then read `a` and `b`.
Count how many samples are strictly greater than `a` and strictly less than `b`. Divide by `n`. Print the probability rounded to **4 decimal places**.

Example:
Input:
5
1.0
2.0
3.0
4.0
5.0
1.5
4.5
Output: 0.6000

MD,
                'starter_code'        => "n = int(input())\nsamples = [float(input()) for _ in range(n)]\na = float(input())\nb = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Calculate an **Empirical 90% Equal-Tailed Interval**.
Read `n` (int), then `n` samples.
Sort the samples in ascending order.
The lower bound is the element at index `int(n * 0.05)`.
The upper bound is the element at index `int(n * 0.95)`.
Print the `lower` and `upper` bounds on separate lines, rounded to **4 decimal places**.

Example:
Input:
100
(1 to 100 on separate lines)
Output:
6.0000
96.0000

MD,
                'starter_code'        => "n = int(input())\nsamples = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Approximate an **Empirical MAP** (Mode) from continuous samples by rounding each sample to the nearest integer and finding the most frequent value.

Read `n` (int), then `n` samples. Round each sample to an int. Print the integer that appears most frequently, formatted as a float rounded to **4 decimal places**. (If tied, pick the smallest integer).

Example:
Input:
4
1.1
1.2
2.9
1.4
Output: 1.0000

MD,
                'starter_code'        => "n = int(input())\nsamples = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.6: Markov Chains (Discrete State) (Q26–Q30)
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Check if a vector is a **Valid Probability Distribution**. All elements must be `>= 0` and their sum must be `1.0` (within `0.001` tolerance).

Read `n` (int), then `n` floats. Print `Valid` or `Invalid`.

Example:
Input:
2
0.4
0.6
Output: Valid

MD,
                'starter_code'        => "n = int(input())\nvector = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Perform a **one-step transition** in a Markov Chain.
Given a 1x2 state vector `P = [P0, P1]` and a 2x2 transition matrix `T`:
`P_new[0] = P0 × T00 + P1 × T10`
`P_new[1] = P0 × T01 + P1 × T11`

Read `P0`, `P1`, `T00`, `T01`, `T10`, `T11`. Print `P_new[0]` and `P_new[1]` space-separated, rounded to **4 decimal places**.

Example:
Input:
0.5
0.5
0.8
0.2
0.4
0.6
Output: 0.6000 0.4000

MD,
                'starter_code'        => "P0 = float(input())\nP1 = float(input())\nT00 = float(input())\nT01 = float(input())\nT10 = float(input())\nT11 = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Calculate the **Trace** of the squared transition matrix `T²`.
For a 2x2 matrix `T`, `T²` is the matrix product `T × T`. The trace is the sum of the diagonal elements of `T²`.

Read `T00`, `T01`, `T10`, `T11`. Compute `T²` and print its trace rounded to **4 decimal places**.

Example:
Input:
0.0
1.0
1.0
0.0
Output: 2.0000

MD,
                'starter_code'        => "T00 = float(input())\nT01 = float(input())\nT10 = float(input())\nT11 = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Check the **Detailed Balance** condition for a 2-state system:
`π0 × T01 == π1 × T10`

Read `pi0`, `pi1`, `T00`, `T01`, `T10`, `T11`. Print `True` if detailed balance holds (within `0.001` tolerance), else `False`.

Example:
Input:
0.6
0.4
0.8
0.2
0.3
0.7
Output: True

MD,
                'starter_code'        => "pi0 = float(input())\npi1 = float(input())\nT00 = float(input())\nT01 = float(input())\nT10 = float(input())\nT11 = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Compute the **Stationary Distribution** `π0` for a 2-state regular Markov chain.
Analytically, `π0 = T10 / (T01 + T10)`.

Read `T00`, `T01`, `T10`, `T11`. Print `pi0` rounded to **4 decimal places**.

Example:
Input:
0.8
0.2
0.3
0.7
Output: 0.6000

MD,
                'starter_code'        => "T00 = float(input())\nT01 = float(input())\nT10 = float(input())\nT11 = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.7: Metropolis-Hastings (Basics) (Q31–Q35)
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
**Metropolis Acceptance Ratio** (symmetric proposal):
`A = min(1.0, P_new / P_old)`

Read `P_new` and `P_old`. Print `A` rounded to **4 decimal places**.

Example:
Input:
0.05
0.01
Output: 1.0000

MD,
                'starter_code'        => "P_new = float(input())\nP_old = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
**Metropolis-Hastings Acceptance Ratio** (asymmetric proposal):
`R = (P_new × q_old_given_new) / (P_old × q_new_given_old)`
`A = min(1.0, R)`

Read `P_new`, `P_old`, `q_old_given_new`, and `q_new_given_old`. Print `A` rounded to **4 decimal places**.

Example:
Input:
0.04
0.02
0.25
0.5
Output: 1.0000

MD,
                'starter_code'        => "P_new = float(input())\nP_old = float(input())\nq_old_given_new = float(input())\nq_new_given_old = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Compute the **Log-Acceptance Ratio** for numerical stability:
`log_R = log(P_new) - log(P_old)`

Read `P_new` and `P_old`. Print `log_R` rounded to **4 decimal places**.

Example:
Input:
0.135335283
0.006737947
Output: 3.0000

MD,
                'starter_code'        => "import math\nP_new = float(input())\nP_old = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
**Accept / Reject Decision**: In log-space, you accept the proposal if `log(u) < log_R`, where `u` is drawn from `Uniform(0,1)`.

Read `log_R` and `u`. Print `Accept` if the condition is met, else `Reject`.

Example:
Input:
-0.5
0.8
Output: Reject

MD,
                'starter_code'        => "import math\nlog_R = float(input())\nu = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
**Random Walk Proposal Update**: A common proposal is adding a small step.
`x_new = x_old + step × direction`

Read `x_old`, `step`, and `direction` (+1 or -1). Print `x_new` rounded to **4 decimal places**.

Example:
Input:
5.0
0.5
-1
Output: 4.5000

MD,
                'starter_code'        => "x_old = float(input())\nstep = float(input())\ndirection = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.8: Bayesian Linear & Logistic Regression (Q36–Q40)
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
**L2 Prior (Ridge Equivalent)**: The unnormalized log-prior for a vector of weights `w` assuming a Gaussian prior with zero mean and precision `tau` is:
`log_prior = -0.5 × τ × Σ(w_i²)`

Read `tau` (float), `n` (number of weights), then `n` weight values. Print `log_prior` rounded to **4 decimal places**.

Example:
Input:
2.0
2
1.0
-2.0
Output: -5.0000

MD,
                'starter_code'        => "tau = float(input())\nn = int(input())\nweights = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
**L1 Prior (Lasso Equivalent)**: The unnormalized log-prior assuming a Laplace distribution is:
`log_prior = -λ × Σ(|w_i|)`

Read `lambda_val` (float), `n` (int), then `n` weight values. Print `log_prior` rounded to **4 decimal places**.

Example:
Input:
3.0
2
1.0
-2.0
Output: -9.0000

MD,
                'starter_code'        => "lambda_val = float(input())\nn = int(input())\nweights = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
**Linear Prediction (Dot Product)**: Predict `y` from a weight vector `w` and a feature vector `x`.
`y_pred = w₀×x₀ + w₁×x₁ + ...`

Read `n`. Then read `n` values for `w`, followed by `n` values for `x`. Print `y_pred` rounded to **4 decimal places**.

Example:
Input:
2
2.0
3.0
1.0
4.0
Output: 14.0000

MD,
                'starter_code'        => "n = int(input())\nw = [float(input()) for _ in range(n)]\nx = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
**Sigmoid Function**: In Bayesian Logistic Regression, outputs are squashed to (0, 1):
`σ(z) = 1 / (1 + exp(-z))`

Read `z` (float). Print `σ(z)` rounded to **4 decimal places**.

Example:
Input:
0.0
Output: 0.5000

MD,
                'starter_code'        => "import math\nz = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
**Logistic Log-Likelihood**: For a single observation `y` (0 or 1) and predicted probability `p`:
`log L = y × log(p) + (1 - y) × log(1 - p)`

Read `y` (int) and `p` (float). Print the log-likelihood rounded to **4 decimal places**.

Example:
Input:
1
0.8
Output: -0.2231

MD,
                'starter_code'        => "import math\ny = int(input())\np = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.9: Information Criteria (Q41–Q45)
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
**LogSumExp Trick**: Safely compute `log(exp(v1) + exp(v2))` without overflow.
`m = max(v1, v2)`
`result = m + log( exp(v1 - m) + exp(v2 - m) )`

Read `v1` and `v2`. Print the result rounded to **4 decimal places**.

Example:
Input:
-1000.0
-1001.0
Output: -999.6867

MD,
                'starter_code'        => "import math\nv1 = float(input())\nv2 = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
**Deviance**: Often used as a baseline for Information Criteria.
`Deviance = -2 × log_likelihood`

Read `log_likelihood` (float). Print the Deviance rounded to **4 decimal places**.

Example:
Input:
-5.5
Output: 11.0000

MD,
                'starter_code'        => "log_likelihood = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
**Akaike Information Criterion (AIC)**: Penalizes model complexity.
`AIC = Deviance + 2 × k`

Read `deviance` (float) and `k` (number of parameters, int). Print AIC rounded to **4 decimal places**.

Example:
Input:
11.0
3
Output: 17.0000

MD,
                'starter_code'        => "deviance = float(input())\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
**Bayesian Information Criterion (BIC)**: More heavily penalizes models with many parameters.
`BIC = Deviance + k × ln(n)`

Read `deviance` (float), `k` (int), and `n` (sample size, int). Print BIC rounded to **4 decimal places**.

Example:
Input:
11.0
3
10
Output: 17.9078

MD,
                'starter_code'        => "import math\ndeviance = float(input())\nk = int(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
**Bayes Factor from Log Marginal Likelihoods**:
`BF = exp(log_M1 - log_M2)`

Read `log_M1` and `log_M2`. Compute the Bayes Factor. Print it rounded to **4 decimal places**.

Example:
Input:
-10.0
-12.0
Output: 7.3891

MD,
                'starter_code'        => "import math\nlog_M1 = float(input())\nlog_M2 = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.10: Mixture Models & Entropy (Q46–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
**Gaussian Mixture PDF** (2 components):
`P(x) = w1 × N(x | μ1, σ1) + w2 × N(x | μ2, σ2)`

Read `w1`, `w2`, `mu1`, `mu2`, `sigma1`, `sigma2`, `x`.
Compute the mixture PDF and print it rounded to **4 decimal places**.
*(Reminder: Normal PDF = `(1/(σ√2π)) * exp(-0.5*((x-μ)/σ)²) `)*

Example:
Input:
0.4
0.6
0.0
5.0
1.0
1.0
0.0
Output: 0.1596

MD,
                'starter_code'        => "import math\nw1 = float(input())\nw2 = float(input())\nmu1 = float(input())\nmu2 = float(input())\nsigma1 = float(input())\nsigma2 = float(input())\nx = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
**Responsibility (E-step)**: What is the probability that component 1 generated `x`?
`Resp1 = (w1 × N1) / (w1 × N1 + w2 × N2)`

Read `w1`, `w2`, `N1` (PDF value from component 1), and `N2` (PDF value from component 2). Print the responsibility of component 1 rounded to **4 decimal places**.

Example:
Input:
0.4
0.6
0.10
0.05
Output: 0.5714

MD,
                'starter_code'        => "w1 = float(input())\nw2 = float(input())\nN1 = float(input())\nN2 = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
**Log-Posterior Calculation**:
`log_posterior = log_prior + log_likelihood`

Read `log_prior` and `log_likelihood` (floats). Print the log-posterior rounded to **4 decimal places**.

Example:
Input:
-2.0
-5.0
Output: -7.0000

MD,
                'starter_code'        => "log_prior = float(input())\nlog_likelihood = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
**Entropy of a Discrete Distribution**: Measures uncertainty.
`H = -Σ(P_i × ln(P_i))`

Read `n` (int), then `n` probabilities. Compute the entropy and print it rounded to **4 decimal places**. (Assume P_i > 0).

Example:
Input:
2
0.2
0.8
Output: 0.5004

MD,
                'starter_code'        => "import math\nn = int(input())\nprobs = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**Kullback-Leibler (KL) Divergence**: Measures how one probability distribution `Q` diverges from a reference distribution `P`.
`D_KL(P || Q) = Σ(P_i × ln(P_i / Q_i))`

Read `n` (int). Then read `n` values for `P`, followed by `n` values for `Q`. Print the KL divergence rounded to **4 decimal places**.

Example:
Input:
2
0.5
0.5
0.2
0.8
Output: 0.2231

MD,
                'starter_code'        => "import math\nn = int(input())\nP = [float(input()) for _ in range(n)]\nQ = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
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

        // ── Q1: Normal PDF ────────────────────────────────────────────────
        $seed(1, [
            ['input' => "0.0\n1.0\n0.0",   'expected_output' => '0.3989', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0\n1.0",   'expected_output' => '0.2420', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n2.0\n5.0",   'expected_output' => '0.1995', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.0\n0.5\n2.5",   'expected_output' => '0.4839', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Exponential PDF ───────────────────────────────────────────
        $seed(2, [
            ['input' => "2.0\n1.0",    'expected_output' => '0.2707', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n-1.0",   'expected_output' => '0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n4.0",    'expected_output' => '0.0677', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n0.0",    'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Uniform CDF ───────────────────────────────────────────────
        $seed(3, [
            ['input' => "0.0\n10.0\n5.0",   'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n10.0\n12.0",  'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n10.0\n-2.0",  'expected_output' => '0.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "-5.0\n5.0\n0.0",   'expected_output' => '0.5000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Normal CDF ────────────────────────────────────────────────
        $seed(4, [
            ['input' => "0.0\n1.0\n1.96",   'expected_output' => '0.9750', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0\n0.0",    'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n2.0\n5.0",    'expected_output' => '0.5000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10.0\n1.0\n8.04",  'expected_output' => '0.0250', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Inverse Transform Sampling ────────────────────────────────
        $seed(5, [
            ['input' => "2.0\n0.5",     'expected_output' => '0.3466', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n0.1",     'expected_output' => '0.1054', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n0.9",     'expected_output' => '0.4605', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n0.99",    'expected_output' => '9.2103', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Grid Approximation ────────────────────────────────────────
        $seed(6, [
            ['input' => "0.0\n1.0\n3",   'expected_output' => '0.0000 0.5000 1.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0\n5",   'expected_output' => '0.0000 0.2500 0.5000 0.7500 1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1.0\n1.0\n3",  'expected_output' => '-1.0000 0.0000 1.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10.0\n12.0\n3", 'expected_output' => '10.0000 11.0000 12.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Binomial likelihood over grid ─────────────────────────────
        $seed(7, [
            ['input' => "10\n7\n3\n0.5\n0.7\n0.9",   'expected_output' => '0.2668', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n2\n2\n0.2\n0.4",         'expected_output' => '0.3456', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10\n5\n3\n0.1\n0.5\n0.9",   'expected_output' => '0.2461', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0\n2\n0.1\n0.8",         'expected_output' => '0.7290', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Unnormalized posterior sum ────────────────────────────────
        $seed(8, [
            ['input' => "3\n0.1\n0.2\n0.3",    'expected_output' => '0.6000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0.05\n0.05\n0.1\n0.2", 'expected_output' => '0.4000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0.8\n0.2",         'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0.0\n0.5\n0.0",    'expected_output' => '0.5000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Normalize unnormalized posterior ──────────────────────────
        $seed(9, [
            ['input' => "3\n0.1\n0.2\n0.3",    'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0.1\n0.1\n0.1\n0.1",'expected_output' => '0.2500', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0.8\n0.2",         'expected_output' => '0.8000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0.0\n0.5\n0.0",    'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: MAP from grid ────────────────────────────────────────────
        $seed(10, [
            ['input' => "2\n0.1\n0.2\n0.4\n0.6",      'expected_output' => '0.2000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.0\n0.5\n1.0\n0.1\n0.8\n0.1", 'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.3\n0.4\n0.5\n0.9\n0.8\n0.7", 'expected_output' => '0.3000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1.0\n2.0\n0.5\n0.5",      'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Normal-Normal tau_post ───────────────────────────────────
        $seed(11, [
            ['input' => "1.0\n10\n2.0",  'expected_output' => '21.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n5\n1.0",   'expected_output' => '5.5000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n100\n0.1", 'expected_output' => '12.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n10\n3.0",  'expected_output' => '30.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Normal-Normal mu_post ────────────────────────────────────
        $seed(12, [
            ['input' => "1.0\n0.0\n10\n2.0\n5.0",   'expected_output' => '4.7619', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n10.0\n5\n1.0\n20.0",  'expected_output' => '17.1429','is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.0\n10\n1.0\n5.0",   'expected_output' => '5.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n5.0\n0\n1.0\n10.0",   'expected_output' => '5.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Gamma-Poisson posterior shape ────────────────────────────
        $seed(13, [
            ['input' => "2.0\n3\n5\n5\n5",    'expected_output' => '17.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n2\n10\n20",     'expected_output' => '31.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n4\n0\n0\n0\n0", 'expected_output' => '5.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n1\n3",          'expected_output' => '3.5000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Gamma-Poisson posterior rate ─────────────────────────────
        $seed(14, [
            ['input' => "1.0\n5",   'expected_output' => '6.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.5\n10",  'expected_output' => '12.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n100", 'expected_output' => '100.0000','is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n3",   'expected_output' => '3.5000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Beta-Binomial predictive probability ─────────────────────
        $seed(15, [
            ['input' => "2.0\n2.0\n10\n6",   'expected_output' => '0.5714', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n1.0\n5\n5",    'expected_output' => '0.8571', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n5.0\n10\n0",   'expected_output' => '0.2500', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10.0\n2.0\n0\n0",   'expected_output' => '0.8333', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Quadratic Loss ───────────────────────────────────────────
        $seed(16, [
            ['input' => "10.0\n8.0",    'expected_output' => '4.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5.0\n10.0",    'expected_output' => '25.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.0",     'expected_output' => '0.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "-2.0\n2.0",    'expected_output' => '16.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Absolute Loss ────────────────────────────────────────────
        $seed(17, [
            ['input' => "10.0\n8.0",    'expected_output' => '2.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5.0\n10.0",    'expected_output' => '5.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-5.0\n-2.0",   'expected_output' => '3.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3.14\n3.14",   'expected_output' => '0.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Zero-One Loss ────────────────────────────────────────────
        $seed(18, [
            ['input' => "10.0\n8.0\n1.0",    'expected_output' => '1', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "10.0\n9.5\n1.0",    'expected_output' => '0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n5.0\n0.0",     'expected_output' => '0', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n2.0\n0.99",    'expected_output' => '1', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Expected Loss ────────────────────────────────────────────
        $seed(19, [
            ['input' => "2\n0.2\n0.8\n10.0\n5.0",   'expected_output' => '6.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.5\n0.3\n0.2\n0\n10\n20", 'expected_output' => '7.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1.0\n0.0\n100.0\n0.0",  'expected_output' => '100.0000','is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0.5\n0.5\n5.0\n5.0",    'expected_output' => '5.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: LinEx Loss ───────────────────────────────────────────────
        $seed(20, [
            ['input' => "2.0\n10.0\n12.0",  'expected_output' => '4.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n10.0\n8.0",   'expected_output' => '2.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n5.0\n10.0",   'expected_output' => '2.5000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "5.0\n20.0\n20.0",  'expected_output' => '0.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Empirical Mean of ln(x) ──────────────────────────────────
        $seed(21, [
            ['input' => "2\n1.0\n2.718281828",   'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1.0\n1.0\n1.0",      'expected_output' => '0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n7.389056\n20.085536",'expected_output' => '2.5000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n2.718281828",        'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: MCSE ─────────────────────────────────────────────────────
        $seed(22, [
            ['input' => "5.0\n100",    'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "10.0\n400",   'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n10000",  'expected_output' => '0.0200', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n50",     'expected_output' => '0.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Empirical P(a < x < b) ───────────────────────────────────
        $seed(23, [
            ['input' => "5\n1.0\n2.0\n3.0\n4.0\n5.0\n1.5\n4.5",  'expected_output' => '0.6000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0.0\n0.5\n0.8\n1.0\n0.2\n0.9",       'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1.0\n2.0\n3.0\n0.0\n4.0",            'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1.0\n2.0\n3.0\n5.0\n6.0",            'expected_output' => '0.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: 90% Equal-Tailed Interval ────────────────────────────────
        // Create inputs for 100 elements. Array 1 to 100. Lower=6, Upper=96
        $input100 = "100\n" . implode("\n", range(1, 100));
        // Array 100 to 1 descending. Still Lower=6, Upper=96
        $input100_desc = "100\n" . implode("\n", range(100, 1));
        // Array of 20 elements 1 to 20. 5% = index 1 (value 2), 95% = index 19 (value 20).
        $input20 = "20\n" . implode("\n", range(1, 20));
        $input20_desc = "20\n" . implode("\n", range(20, 1));

        $seed(24, [
            ['input' => $input100,       'expected_output' => "6.0000\n96.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => $input20,        'expected_output' => "2.0000\n20.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => $input100_desc,  'expected_output' => "6.0000\n96.0000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => $input20_desc,   'expected_output' => "2.0000\n20.0000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Empirical MAP (Rounded Mode) ─────────────────────────────
        $seed(25, [
            ['input' => "4\n1.1\n1.2\n2.9\n1.4",         'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n5.1\n5.4\n5.6\n6.1\n6.2",    'expected_output' => '6.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n0.1\n0.2\n0.9\n1.1\n1.9\n2.1", 'expected_output' => '1.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n10.1\n10.2\n20.0",           'expected_output' => '10.0000','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Valid Probability Distribution ───────────────────────────
        $seed(26, [
            ['input' => "2\n0.4\n0.6",      'expected_output' => 'Valid',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.3\n0.3\n0.3", 'expected_output' => 'Invalid', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1.2\n-0.2",     'expected_output' => 'Invalid', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0.25\n0.25\n0.25\n0.25", 'expected_output' => 'Valid', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q27: Markov Chain Transition ──────────────────────────────────
        $seed(27, [
            ['input' => "0.5\n0.5\n0.8\n0.2\n0.4\n0.6",   'expected_output' => '0.6000 0.4000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n0.0\n0.1\n0.9\n0.5\n0.5",   'expected_output' => '0.1000 0.9000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n1.0\n0.8\n0.2\n0.4\n0.6",   'expected_output' => '0.4000 0.6000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.2\n0.8\n1.0\n0.0\n0.0\n1.0",   'expected_output' => '0.2000 0.8000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: T² Trace ─────────────────────────────────────────────────
        $seed(28, [
            ['input' => "0.0\n1.0\n1.0\n0.0",   'expected_output' => '2.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.8\n0.2\n0.4\n0.6",   'expected_output' => '1.1600', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n0.0\n0.0\n1.0",   'expected_output' => '2.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n0.5\n0.5\n0.5",   'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Detailed Balance ─────────────────────────────────────────
        $seed(29, [
            ['input' => "0.6\n0.4\n0.8\n0.2\n0.3\n0.7",   'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n0.5\n0.9\n0.1\n0.2\n0.8",   'expected_output' => 'False', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n0.5\n0.5\n0.5\n0.5\n0.5",   'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n0.0\n0.8\n0.2\n0.3\n0.7",   'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Stationary Distribution ──────────────────────────────────
        $seed(30, [
            ['input' => "0.8\n0.2\n0.3\n0.7",   'expected_output' => '0.6000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.9\n0.1\n0.4\n0.6",   'expected_output' => '0.8000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n0.5\n0.5\n0.5",   'expected_output' => '0.5000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n1.0\n1.0\n0.0",   'expected_output' => '0.5000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: MH Acceptance Ratio (symmetric) ──────────────────────────
        $seed(31, [
            ['input' => "0.05\n0.01",   'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.01\n0.05",   'expected_output' => '0.2000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n0.5",     'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n0.1",     'expected_output' => '0.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: MH Acceptance Ratio (asymmetric) ─────────────────────────
        $seed(32, [
            ['input' => "0.04\n0.02\n0.25\n0.5",  'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.02\n0.04\n0.5\n0.25",  'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.1\n0.1\n0.2\n0.4",     'expected_output' => '0.5000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n0.1\n0.1\n0.9",     'expected_output' => '0.5556', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Log-Acceptance Ratio ─────────────────────────────────────
        $seed(33, [
            ['input' => "0.135335283\n0.006737947", 'expected_output' => '3.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.006737947\n0.135335283", 'expected_output' => '-3.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n1.0",                 'expected_output' => '0.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.7182818\n1.0",           'expected_output' => '1.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Accept / Reject ──────────────────────────────────────────
        $seed(34, [
            ['input' => "-0.5\n0.8",    'expected_output' => 'Reject', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "-0.1\n0.5",    'expected_output' => 'Accept', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n0.9",     'expected_output' => 'Accept', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "-2.0\n0.2",    'expected_output' => 'Reject', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Random Walk Proposal ─────────────────────────────────────
        $seed(35, [
            ['input' => "5.0\n0.5\n-1", 'expected_output' => '4.5000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5.0\n0.5\n1",  'expected_output' => '5.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n1.2\n-1", 'expected_output' => '-1.2000','is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n1.2\n1",  'expected_output' => '1.2000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: L2 Prior ─────────────────────────────────────────────────
        $seed(36, [
            ['input' => "2.0\n2\n1.0\n-2.0",      'expected_output' => '-5.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n3\n1.0\n1.0\n1.0",  'expected_output' => '-1.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n2\n0.0\n0.0",       'expected_output' => '-0.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10.0\n1\n3.0",           'expected_output' => '-45.0000','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: L1 Prior ─────────────────────────────────────────────────
        $seed(37, [
            ['input' => "3.0\n2\n1.0\n-2.0",      'expected_output' => '-9.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n3\n1.0\n1.0\n1.0",  'expected_output' => '-3.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n2\n0.0\n0.0",       'expected_output' => '-0.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10.0\n1\n3.0",           'expected_output' => '-30.0000','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Linear Prediction ────────────────────────────────────────
        $seed(38, [
            ['input' => "2\n2.0\n3.0\n1.0\n4.0",      'expected_output' => '14.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1.0\n1.0\n1.0\n2\n3\n4",  'expected_output' => '9.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0.0\n0.0\n5.0\n10.0",     'expected_output' => '0.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n-5.0\n2.0",               'expected_output' => '-10.0000','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Sigmoid Function ─────────────────────────────────────────
        $seed(39, [
            ['input' => "0.0",      'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0",      'expected_output' => '0.8808', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-2.0",     'expected_output' => '0.1192', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10.0",     'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Logistic Log-Likelihood ──────────────────────────────────
        $seed(40, [
            ['input' => "1\n0.8",   'expected_output' => '-0.2231', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n0.2",   'expected_output' => '-0.2231', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0.5",   'expected_output' => '-0.6931', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n0.9",   'expected_output' => '-2.3026', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: LogSumExp Trick ──────────────────────────────────────────
        $seed(41, [
            ['input' => "-1000.0\n-1001.0", 'expected_output' => '-999.6867', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5.0\n5.0",         'expected_output' => '5.6931',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "-10.0\n-5.0",      'expected_output' => '-4.9933',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n0.0",         'expected_output' => '0.6931',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Deviance ─────────────────────────────────────────────────
        $seed(42, [
            ['input' => "-5.5",   'expected_output' => '11.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "-10.0",  'expected_output' => '20.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0",    'expected_output' => '-0.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "-2.5",   'expected_output' => '5.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: AIC ──────────────────────────────────────────────────────
        $seed(43, [
            ['input' => "11.0\n3",   'expected_output' => '17.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "20.0\n5",   'expected_output' => '30.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n10",   'expected_output' => '25.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n2",    'expected_output' => '4.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: BIC ──────────────────────────────────────────────────────
        $seed(44, [
            ['input' => "11.0\n3\n10",    'expected_output' => '17.9078', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "20.0\n5\n100",   'expected_output' => '43.0259', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n2\n50",     'expected_output' => '12.8240', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10.0\n1\n1000",  'expected_output' => '16.9078', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Bayes Factor (from LogL) ─────────────────────────────────
        $seed(45, [
            ['input' => "-10.0\n-12.0", 'expected_output' => '7.3891', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "-12.0\n-10.0", 'expected_output' => '0.1353', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-5.0\n-5.0",   'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "-8.0\n-9.0",   'expected_output' => '2.7183', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Gaussian Mixture PDF ─────────────────────────────────────
        $seed(46, [
            ['input' => "0.4\n0.6\n0.0\n5.0\n1.0\n1.0\n0.0", 'expected_output' => '0.1596', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n0.5\n0.0\n0.0\n1.0\n1.0\n0.0", 'expected_output' => '0.3989', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.8\n0.2\n2.0\n-2.0\n1.0\n1.0\n2.0",'expected_output' => '0.3192', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n0.0\n0.0\n10.0\n2.0\n1.0\n0.0",'expected_output' => '0.1995', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Responsibility ───────────────────────────────────────────
        $seed(47, [
            ['input' => "0.4\n0.6\n0.10\n0.05", 'expected_output' => '0.5714', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n0.5\n0.20\n0.20", 'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.8\n0.2\n0.01\n0.90", 'expected_output' => '0.0426', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.1\n0.9\n0.50\n0.01", 'expected_output' => '0.8475', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Log-Posterior ────────────────────────────────────────────
        $seed(48, [
            ['input' => "-2.0\n-5.0",   'expected_output' => '-7.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "-0.5\n-10.5",  'expected_output' => '-11.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-100.0\n-20.0",'expected_output' => '-120.0000','is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n-3.14",   'expected_output' => '-3.1400',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Entropy ──────────────────────────────────────────────────
        $seed(49, [
            ['input' => "2\n0.2\n0.8",      'expected_output' => '0.5004', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.5\n0.5",      'expected_output' => '0.6931', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.333333\n0.333333\n0.333334", 'expected_output' => '1.0986', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n0.1\n0.2\n0.3\n0.4", 'expected_output' => '1.2799', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q50: KL Divergence ────────────────────────────────────────────
        $seed(50, [
            ['input' => "2\n0.5\n0.5\n0.2\n0.8",   'expected_output' => '0.2231', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.5\n0.5\n0.5\n0.5",   'expected_output' => '0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.8\n0.1\n0.1\n0.1\n0.8\n0.1", 'expected_output' => '1.4556', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0.9\n0.1\n0.5\n0.5",   'expected_output' => '0.3680', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 11 Coding (University Student) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}