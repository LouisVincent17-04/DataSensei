<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 11 — Bayesian Statistics (Newbie) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering all Bayesian basics
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (Newbie):
 *   11.1  What Is Bayesian Thinking?
 *   11.2  Bayes' Theorem: The Engine of Inference
 *   11.3  Prior Distributions: Encoding Prior Knowledge
 *   11.4  Likelihood Functions and Statistical Models
 *   11.5  Conjugate Priors and Analytical Posteriors
 *   11.6  Posterior Inference: Summaries and Credible Intervals
 *   11.7  MCMC: Markov Chain Monte Carlo
 *   11.8  Bayesian Regression
 *   11.9  Bayesian Model Comparison
 *   11.10 Hierarchical Bayesian Models
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module11CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 11 — Bayesian Statistics (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Bayesian Statistics',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply fundamental Bayesian statistics concepts in Python — compute priors, likelihoods, and posteriors using Bayes\' theorem, work with conjugate priors, and perform basic MCMC and model comparison calculations. Tasks are short and self-contained, requiring only simple arithmetic and the math module.',
                'time_limit_seconds' => 900,
                'base_xp'            => 500,
                'order_index'        => 11,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.1: What Is Bayesian Thinking? (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Read a prior probability `P(H)` as a float. Classify it:
- `P(H) >= 0.7` → print `Strong prior`
- `P(H) >= 0.4` → print `Moderate prior`
- Otherwise     → print `Weak prior`

Example:
```
Input: 0.75
Output: Strong prior
```
MD,
                'starter_code'        => "p = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read a prior probability `P(H)` as a float. Print the **complement** `P(not H) = 1 - P(H)`, rounded to **4 decimal places**.

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

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read two floats: `P(H)` and `P(not H)`. Print `Valid` if they sum to 1.0 (within a tolerance of 0.001), otherwise print `Invalid`.

Example:
```
Input:
0.6
0.4
Output: Valid
```
MD,
                'starter_code'        => "p_h = float(input())\np_not_h = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
In Bayesian thinking, observing evidence updates our belief. Read an **old belief** and a **new belief** (both floats). Print:
- `Updated Up` if new > old
- `Updated Down` if new < old
- `No Change` if equal

Example:
```
Input:
0.3
0.6
Output: Updated Up
```
MD,
                'starter_code'        => "old_belief = float(input())\nnew_belief = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read `n` (int), then `n` prior probabilities (floats, one per line). Print the prior **closest to 0.5** (the most uncertain prior), rounded to **4 decimal places**. If there is a tie, print the first one encountered.

Example:
```
Input:
3
0.1
0.6
0.9
Output: 0.6000
```
MD,
                'starter_code'        => "n = int(input())\npriors = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.2: Bayes' Theorem: The Engine of Inference (Q6–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Apply **Bayes' Theorem**: `P(H|E) = P(E|H) × P(H) / P(E)`.

Read three floats on separate lines: `P(H)`, `P(E|H)`, `P(E)`. Print `P(H|E)` rounded to **4 decimal places**.

Example:
```
Input:
0.4
0.7
0.5
Output: 0.5600
```
MD,
                'starter_code'        => "p_h = float(input())\np_e_given_h = float(input())\np_e = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Compute the **marginal probability** `P(E)` using the **Law of Total Probability**:

`P(E) = P(E|H) × P(H) + P(E|¬H) × (1 − P(H))`

Read: `P(H)`, `P(E|H)`, `P(E|¬H)` (three floats, one per line). Print `P(E)` rounded to **4 decimal places**.

Example:
```
Input:
0.3
0.9
0.2
Output: 0.4100
```
MD,
                'starter_code'        => "p_h = float(input())\np_e_given_h = float(input())\np_e_given_not_h = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Compute the **full posterior** `P(H|E)` using Bayes' theorem and the Law of Total Probability.

Read: `P(H)`, `P(E|H)`, `P(E|¬H)` (three floats, one per line).

First compute `P(E) = P(E|H)×P(H) + P(E|¬H)×(1−P(H))`, then compute `P(H|E) = P(E|H)×P(H) / P(E)`.

Print `P(H|E)` rounded to **4 decimal places**.

Example:
```
Input:
0.3
0.9
0.2
Output: 0.6585
```
MD,
                'starter_code'        => "p_h = float(input())\np_e_given_h = float(input())\np_e_given_not_h = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read a posterior probability `P(H|E)` as a float. Print it as a **percentage** (multiply by 100), rounded to **2 decimal places**, followed by a `%` sign.

Example:
```
Input: 0.6585
Output: 65.85%
```
MD,
                'starter_code'        => "posterior = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read two floats: prior `P(H)` and posterior `P(H|E)`. Print:
- `Belief increased` if posterior > prior
- `Belief decreased` if posterior < prior
- `No change` if equal

Example:
```
Input:
0.3
0.6585
Output: Belief increased
```
MD,
                'starter_code'        => "prior = float(input())\nposterior = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.3: Prior Distributions: Encoding Prior Knowledge (Q11–Q15)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
The **Beta distribution** with parameters `α` and `β` has mean = `α / (α + β)`.

Read `alpha` and `beta` (two floats, one per line). Print the Beta mean rounded to **4 decimal places**.

Example:
```
Input:
2.0
5.0
Output: 0.2857
```
MD,
                'starter_code'        => "alpha = float(input())\nbeta = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
The **Beta distribution** (with `α > 1` and `β > 1`) has mode = `(α − 1) / (α + β − 2)`.

Read `alpha` and `beta` (two floats, one per line). Print the Beta mode rounded to **4 decimal places**.

Example:
```
Input:
3.0
5.0
Output: 0.3333
```
MD,
                'starter_code'        => "alpha = float(input())\nbeta = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
A **Uniform prior** over the interval `[a, b]` has mean = `(a + b) / 2`.

Read `a` and `b` (two floats, one per line). Print the Uniform prior mean rounded to **4 decimal places**.

Example:
```
Input:
0.0
1.0
Output: 0.5000
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
The **Beta distribution** variance formula is:

`Var = (α × β) / ((α + β)² × (α + β + 1))`

Read `alpha` and `beta` (two floats, one per line). Print the variance rounded to **6 decimal places**.

Example:
```
Input:
2.0
5.0
Output: 0.025510
```
MD,
                'starter_code'        => "alpha = float(input())\nbeta = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Classify a prior as **Informative**, **Weakly informative**, or **Non-informative** based on its strength `kappa` relative to the sample size `n`:
- `kappa >= n`     → `Informative`
- `kappa >= n / 2` → `Weakly informative`
- Otherwise        → `Non-informative`

Read `kappa` (float) and `n` (int), one per line.

Example:
```
Input:
10.0
5
Output: Informative
```
MD,
                'starter_code'        => "kappa = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.4: Likelihood Functions and Statistical Models (Q16–Q20)
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
The **Bernoulli likelihood** for a single observation `x` (0 or 1) given probability `p` is:

`L(p | x) = p^x × (1 − p)^(1 − x)`

Read `p` (float) and `x` (int, 0 or 1), one per line. Print `L` rounded to **4 decimal places**.

Example:
```
Input:
0.7
1
Output: 0.7000
```
MD,
                'starter_code'        => "p = float(input())\nx = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
The **Binomial likelihood** for `k` successes in `n` trials with probability `p` is:

`L(p | k, n) = C(n, k) × p^k × (1 − p)^(n − k)`

Read `n` (int), `k` (int), `p` (float), one per line. Print `L` rounded to **6 decimal places**.

Hint: use `import math` and `math.comb(n, k)`.

Example:
```
Input:
10
3
0.3
Output: 0.266828
```
MD,
                'starter_code'        => "import math\nn = int(input())\nk = int(input())\np = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
The **Bernoulli log-likelihood** for observation `x` (0 or 1) given `p` is:

`log L = x × log(p) + (1 − x) × log(1 − p)`

Read `p` (float) and `x` (int, 0 or 1), one per line. Print log L rounded to **4 decimal places**.

Hint: use `import math` and `math.log()`.

Example:
```
Input:
0.7
1
Output: -0.3567
```
MD,
                'starter_code'        => "import math\np = float(input())\nx = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
The **Binomial log-likelihood** (ignoring the combinatorial constant) is:

`log L = k × log(p) + (n − k) × log(1 − p)`

Read `p` (float), `n` (int), `k` (int), one per line. Print log L rounded to **4 decimal places**.

Hint: use `import math` and `math.log()`.

Example:
```
Input:
0.3
10
3
Output: -6.1085
```
MD,
                'starter_code'        => "import math\np = float(input())\nn = int(input())\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Read two likelihood values `L_A` and `L_B` (floats, one per line). Print `Model A` if `L_A > L_B`, `Model B` if `L_B > L_A`, or `Equal` if they are equal.

Example:
```
Input:
0.45
0.30
Output: Model A
```
MD,
                'starter_code'        => "L_A = float(input())\nL_B = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.5: Conjugate Priors and Analytical Posteriors (Q21–Q25)
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
For the **Beta-Binomial** conjugate model, the posterior parameters are:

`posterior_alpha = prior_alpha + successes`
`posterior_beta  = prior_beta  + failures`

Read `prior_alpha`, `prior_beta`, `successes`, `failures` (all integers, one per line). Print `posterior_alpha` and `posterior_beta`, each on its own line.

Example:
```
Input:
1
1
7
3
Output:
8
4
```
MD,
                'starter_code'        => "prior_alpha = int(input())\nprior_beta = int(input())\nsuccesses = int(input())\nfailures = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
After a Beta-Binomial update, compute the **posterior mean**:

`mean = alpha / (alpha + beta)`

Read the posterior `alpha` and `beta` (two floats, one per line). Print the posterior mean rounded to **4 decimal places**.

Example:
```
Input:
8.0
4.0
Output: 0.6667
```
MD,
                'starter_code'        => "alpha = float(input())\nbeta = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Given a **Beta prior** with parameters `prior_alpha` and `prior_beta`, and observing `k` successes in `n` total trials, compute the **posterior mean**:

`posterior_mean = (prior_alpha + k) / (prior_alpha + k + prior_beta + n − k)`

Read `prior_alpha` (float), `prior_beta` (float), `k` (int), `n` (int), one per line. Print the posterior mean rounded to **4 decimal places**.

Example:
```
Input:
1.0
1.0
3
10
Output: 0.3333
```
MD,
                'starter_code'        => "prior_alpha = float(input())\nprior_beta = float(input())\nk = int(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
After observing `k` successes in `n` total trials, compute and print the **posterior Beta parameters**:

`posterior_alpha = prior_alpha + k`
`posterior_beta  = prior_beta  + (n − k)`

Read `prior_alpha` (int), `prior_beta` (int), `k` (int), `n` (int), one per line. Print `posterior_alpha` and `posterior_beta`, each on its own line.

Example:
```
Input:
2
2
5
10
Output:
7
7
```
MD,
                'starter_code'        => "prior_alpha = int(input())\nprior_beta = int(input())\nk = int(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Read `prior_alpha` and `prior_beta` (two floats, one per line). Based on the Beta prior parameters, classify the distribution skew:
- `prior_alpha > prior_beta` → `Right-skewed`
- `prior_alpha < prior_beta` → `Left-skewed`
- `prior_alpha == prior_beta` → `Symmetric`

Example:
```
Input:
5.0
2.0
Output: Right-skewed
```
MD,
                'starter_code'        => "prior_alpha = float(input())\nprior_beta = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.6: Posterior Inference: Summaries and Credible Intervals (Q26–Q30)
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Read `n` (int), then `n` posterior samples (floats, one per line). Print the **posterior mean** rounded to **4 decimal places**.

Example:
```
Input:
5
0.2
0.4
0.6
0.8
1.0
Output: 0.6000
```
MD,
                'starter_code'        => "n = int(input())\nsamples = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Read `n` (int), then `n` posterior samples (floats, one per line). Print the **population standard deviation** of the samples, rounded to **4 decimal places**.

Population std = sqrt( mean of (x − mean)² )

Example:
```
Input:
5
0.2
0.4
0.6
0.8
1.0
Output: 0.2828
```
MD,
                'starter_code'        => "n = int(input())\nsamples = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Compute a **95% credible interval** using the Normal approximation:

`lower = mu − 1.96 × sigma`
`upper = mu + 1.96 × sigma`

Read `mu` (float) and `sigma` (float), one per line. Print `lower` and `upper`, each rounded to **4 decimal places**, each on its own line.

Example:
```
Input:
100.0
10.0
Output:
80.4000
119.6000
```
MD,
                'starter_code'        => "mu = float(input())\nsigma = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Read a credible interval's `lower` bound and `upper` bound (two floats, one per line). Print the **interval width** = upper − lower, rounded to **4 decimal places**.

Example:
```
Input:
80.4000
119.6000
Output: 39.2000
```
MD,
                'starter_code'        => "lower = float(input())\nupper = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Read `n` (int), a `threshold` (float), then `n` posterior samples (floats, one per line). Print the **proportion of samples greater than the threshold**, rounded to **4 decimal places**.

This represents the posterior probability `P(θ > threshold)`.

Example:
```
Input:
5
0.5
0.1
0.3
0.6
0.8
0.9
Output: 0.6000
```
MD,
                'starter_code'        => "n = int(input())\nthreshold = float(input())\nsamples = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.7: MCMC: Markov Chain Monte Carlo (Q31–Q35)
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Read the number of **accepted proposals** and **total proposals** (two integers, one per line). Print the **MCMC acceptance rate** = accepted / total, rounded to **4 decimal places**.

Example:
```
Input:
75
100
Output: 0.7500
```
MD,
                'starter_code'        => "accepted = int(input())\ntotal = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
MCMC chains begin with a **burn-in** period that is discarded. Read `burn_in` (int) and `total_samples` (int), one per line. Print the number of **effective samples** = total_samples − burn_in.

Example:
```
Input:
100
1000
Output: 900
```
MD,
                'starter_code'        => "burn_in = int(input())\ntotal_samples = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Read `n` (int), `burn_in` (int), then `n` MCMC samples (floats, one per line). Discard the first `burn_in` samples, then print the **mean of the remaining samples**, rounded to **4 decimal places**.

Example:
```
Input:
6
2
1.0
2.0
3.0
4.0
5.0
6.0
Output: 4.5000
```
MD,
                'starter_code'        => "n = int(input())\nburn_in = int(input())\nsamples = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Running multiple **parallel chains** is common in MCMC. Read `n_chains` (int) and `samples_per_chain` (int), one per line. Print the **total number of samples** = n_chains × samples_per_chain.

Example:
```
Input:
4
1000
Output: 4000
```
MD,
                'starter_code'        => "n_chains = int(input())\nsamples_per_chain = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Read `n` (int), then `n` MCMC samples (floats, one per line). Print the **posterior median** rounded to **2 decimal places**.

- If `n` is odd: median = middle element of the sorted list.
- If `n` is even: median = average of the two middle elements.

Example:
```
Input:
5
0.3
0.5
0.7
0.1
0.9
Output: 0.50
```
MD,
                'starter_code'        => "n = int(input())\nsamples = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.8: Bayesian Regression (Q36–Q40)
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
In Bayesian linear regression, the predicted value is:

`ŷ = b0 + b1 × x`

Read `b0` (intercept, float), `b1` (slope, float), and `x` (float), one per line. Print `ŷ` rounded to **2 decimal places**.

Example:
```
Input:
5.0
2.0
10.0
Output: 25.00
```
MD,
                'starter_code'        => "b0 = float(input())\nb1 = float(input())\nx = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
A simple way to incorporate a **prior slope** is to average the prior mean slope with the observed slope (equal weighting):

`posterior_slope = (prior_mean_slope + observed_slope) / 2`

Read `prior_mean_slope` (float) and `observed_slope` (float), one per line. Print the posterior slope rounded to **4 decimal places**.

Example:
```
Input:
1.0
3.0
Output: 2.0000
```
MD,
                'starter_code'        => "prior_mean_slope = float(input())\nobserved_slope = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Read `y_actual` (float) and `y_pred` (float), one per line. Print the **residual** = y_actual − y_pred, rounded to **4 decimal places**.

Example:
```
Input:
15.0
12.5
Output: 2.5000
```
MD,
                'starter_code'        => "y_actual = float(input())\ny_pred = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Read `n` (int), then `n` pairs of (actual, predicted) values — actual and predicted alternating on separate lines. Compute the **Mean Absolute Error (MAE)** = average of |actual − predicted|, rounded to **4 decimal places**.

Example:
```
Input:
3
10
12
20
18
30
33
Output: 2.3333
```
MD,
                'starter_code'        => "n = int(input())\npairs = [(float(input()), float(input())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
In Bayesian analysis, the **posterior uncertainty** (std) should be smaller than the **prior uncertainty** when data is informative.

Read `prior_std` (float) and `posterior_std` (float), one per line. Print:
- `Informative data` if posterior_std < prior_std
- `No update` if equal
- `Wider posterior` otherwise

Example:
```
Input:
5.0
3.0
Output: Informative data
```
MD,
                'starter_code'        => "prior_std = float(input())\nposterior_std = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.9: Bayesian Model Comparison (Q41–Q45)
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
The **Bayes Factor** (BF) compares two models:

`BF = marginal_likelihood_M1 / marginal_likelihood_M2`

Read `marginal_M1` (float) and `marginal_M2` (float), one per line. Print `BF` rounded to **4 decimal places**.

Example:
```
Input:
0.8
0.4
Output: 2.0000
```
MD,
                'starter_code'        => "marginal_M1 = float(input())\nmarginal_M2 = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Interpret a **Bayes Factor** (BF) using the Jeffreys scale:
- `BF >= 100` → `Decisive`
- `BF >= 10`  → `Strong`
- `BF >= 3`   → `Moderate`
- `BF < 3`    → `Weak`

Read `BF` as a float. Print the interpretation.

Example:
```
Input: 150.0
Output: Decisive
```
MD,
                'starter_code'        => "BF = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Read two **log marginal likelihoods**: `log_L1` and `log_L2` (floats, one per line). Print `Model 1` if `log_L1 > log_L2`, `Model 2` if `log_L2 > log_L1`, or `Tie` if equal.

(Higher log marginal likelihood indicates a better-fitting model.)

Example:
```
Input:
-10.5
-12.3
Output: Model 1
```
MD,
                'starter_code'        => "log_L1 = float(input())\nlog_L2 = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
The **posterior odds** updates the prior odds by the Bayes Factor:

`posterior_odds = prior_odds × BF`

Read `prior_odds` (float) and `BF` (float), one per line. Print `posterior_odds` rounded to **4 decimal places**.

Example:
```
Input:
1.0
3.0
Output: 3.0000
```
MD,
                'starter_code'        => "prior_odds = float(input())\nBF = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
The **Deviance Information Criterion (DIC)** is used for Bayesian model comparison — **lower is better**.

Read `DIC1` (float) and `DIC2` (float), one per line. Print `Model 1` if DIC1 < DIC2, `Model 2` if DIC2 < DIC1, or `Tie` if equal.

Example:
```
Input:
450.0
480.0
Output: Model 1
```
MD,
                'starter_code'        => "DIC1 = float(input())\nDIC2 = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11.10: Hierarchical Bayesian Models (Q46–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
In hierarchical models, the **grand mean** is the mean of group-level means.

Read `n` (int), then `n` group means (floats, one per line). Print the grand mean rounded to **4 decimal places**.

Example:
```
Input:
3
10.0
20.0
30.0
Output: 20.0000
```
MD,
                'starter_code'        => "n = int(input())\ngroup_means = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Compute the **between-group variance**: the population variance of the group-level means.

Read `n` (int), then `n` group means (floats, one per line). Print the variance rounded to **4 decimal places**.

Population variance = mean of (each mean − grand mean)²

Example:
```
Input:
3
10.0
20.0
30.0
Output: 66.6667
```
MD,
                'starter_code'        => "n = int(input())\ngroup_means = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
The **Intraclass Correlation Coefficient (ICC)** measures how much of the total variance is due to between-group differences:

`ICC = between_var / (between_var + within_var)`

Read `between_var` (float) and `within_var` (float), one per line. Print ICC rounded to **4 decimal places**.

Example:
```
Input:
4.0
6.0
Output: 0.4000
```
MD,
                'starter_code'        => "between_var = float(input())\nwithin_var = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
In hierarchical Bayesian models, the **shrinkage factor** determines how much group-level estimates are pulled toward the grand mean:

`shrinkage = prior_n / (prior_n + data_n)`

A shrinkage of 1 means full pooling; 0 means no pooling.

Read `prior_n` (int) and `data_n` (int), one per line. Print the shrinkage factor rounded to **4 decimal places**.

Example:
```
Input:
10
10
Output: 0.5000
```
MD,
                'starter_code'        => "prior_n = int(input())\ndata_n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Based on the **Intraclass Correlation Coefficient (ICC)**, decide on a pooling strategy:
- `ICC < 0.1`  → `Full pooling` (groups are very similar)
- `ICC < 0.9`  → `Partial pooling` (some group differences)
- `ICC >= 0.9` → `No pooling` (groups are very different)

Read ICC as a float. Print the pooling strategy.

Example:
```
Input: 0.5
Output: Partial pooling
```
MD,
                'starter_code'        => "icc = float(input())\n# Write your solution below\n",
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

        // ── Q1: Prior classification ──────────────────────────────────────
        $seed(1, [
            ['input' => '0.75',  'expected_output' => 'Strong prior',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.5',   'expected_output' => 'Moderate prior', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.2',   'expected_output' => 'Weak prior',     'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.4',   'expected_output' => 'Moderate prior', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Complement ────────────────────────────────────────────────
        $seed(2, [
            ['input' => '0.35',  'expected_output' => '0.6500', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.7',   'expected_output' => '0.3000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.1',   'expected_output' => '0.9000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.95',  'expected_output' => '0.0500', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Valid probability pair ────────────────────────────────────
        $seed(3, [
            ['input' => "0.6\n0.4",   'expected_output' => 'Valid',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.3\n0.7",   'expected_output' => 'Valid',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n0.6",   'expected_output' => 'Invalid', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n0.0",   'expected_output' => 'Valid',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Belief update direction ───────────────────────────────────
        $seed(4, [
            ['input' => "0.3\n0.6",   'expected_output' => 'Updated Up',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.8\n0.5",   'expected_output' => 'Updated Down', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n0.5",   'expected_output' => 'No Change',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.1\n0.9",   'expected_output' => 'Updated Up',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Most uncertain prior ──────────────────────────────────────
        $seed(5, [
            ['input' => "3\n0.1\n0.6\n0.9",              'expected_output' => '0.6000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0.2\n0.3\n0.7\n0.9",         'expected_output' => '0.3000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.05\n0.5\n0.95",            'expected_output' => '0.5000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0.1\n0.2\n0.8\n0.9",         'expected_output' => '0.2000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Bayes' Theorem ────────────────────────────────────────────
        $seed(6, [
            ['input' => "0.4\n0.7\n0.5",     'expected_output' => '0.5600', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.3\n0.8\n0.4",     'expected_output' => '0.6000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.1\n0.95\n0.2",    'expected_output' => '0.4750', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n0.6\n0.6",     'expected_output' => '0.5000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Marginal probability ──────────────────────────────────────
        $seed(7, [
            ['input' => "0.3\n0.9\n0.2",     'expected_output' => '0.4100', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n0.8\n0.4",     'expected_output' => '0.6000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.6\n0.7\n0.3",     'expected_output' => '0.5400', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.2\n0.6\n0.1",     'expected_output' => '0.2000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Full posterior ────────────────────────────────────────────
        $seed(8, [
            ['input' => "0.3\n0.9\n0.2",     'expected_output' => '0.6585', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n0.8\n0.4",     'expected_output' => '0.6667', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.1\n0.95\n0.05",   'expected_output' => '0.6786', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.7\n0.6\n0.8",     'expected_output' => '0.6364', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Posterior as percentage ───────────────────────────────────
        $seed(9, [
            ['input' => '0.6585',   'expected_output' => '65.85%', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.5',      'expected_output' => '50.00%', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.3',      'expected_output' => '30.00%', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.9999',   'expected_output' => '99.99%', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Prior vs Posterior ───────────────────────────────────────
        $seed(10, [
            ['input' => "0.3\n0.6585",   'expected_output' => 'Belief increased', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.8\n0.5",      'expected_output' => 'Belief decreased', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n0.5",      'expected_output' => 'No change',        'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.1\n0.7",      'expected_output' => 'Belief increased', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Beta mean ────────────────────────────────────────────────
        $seed(11, [
            ['input' => "2.0\n5.0",    'expected_output' => '0.2857', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3.0\n3.0",    'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "8.0\n2.0",    'expected_output' => '0.8000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n4.0",    'expected_output' => '0.2000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Beta mode ────────────────────────────────────────────────
        $seed(12, [
            ['input' => "3.0\n5.0",    'expected_output' => '0.3333', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5.0\n3.0",    'expected_output' => '0.6667', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0\n2.0",   'expected_output' => '0.9000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.0\n8.0",    'expected_output' => '0.1250', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Uniform prior mean ───────────────────────────────────────
        $seed(13, [
            ['input' => "0.0\n1.0",    'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n8.0",    'expected_output' => '5.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1.0\n1.0",   'expected_output' => '0.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10.0\n20.0",  'expected_output' => '15.0000','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Beta variance ────────────────────────────────────────────
        $seed(14, [
            ['input' => "2.0\n5.0",    'expected_output' => '0.025510', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3.0\n3.0",    'expected_output' => '0.035714', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0\n10.0",  'expected_output' => '0.011905', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n1.0",    'expected_output' => '0.083333', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Prior informativeness ────────────────────────────────────
        $seed(15, [
            ['input' => "10.0\n5",   'expected_output' => 'Informative',       'is_hidden' => false, 'order_index' => 1],
            ['input' => "3.0\n10",   'expected_output' => 'Weakly informative', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n20",   'expected_output' => 'Non-informative',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "25.0\n10",  'expected_output' => 'Informative',        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Bernoulli likelihood ─────────────────────────────────────
        $seed(16, [
            ['input' => "0.7\n1",    'expected_output' => '0.7000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.7\n0",    'expected_output' => '0.3000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.9\n1",    'expected_output' => '0.9000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.4\n0",    'expected_output' => '0.6000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Binomial likelihood ──────────────────────────────────────
        $seed(17, [
            ['input' => "10\n3\n0.3",   'expected_output' => '0.266828', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n2\n0.5",    'expected_output' => '0.312500', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "8\n4\n0.4",    'expected_output' => '0.232243', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n0\n0.2",    'expected_output' => '0.262144', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Bernoulli log-likelihood ─────────────────────────────────
        $seed(18, [
            ['input' => "0.7\n1",    'expected_output' => '-0.3567', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.7\n0",    'expected_output' => '-1.2040', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.9\n1",    'expected_output' => '-0.1054', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n0",    'expected_output' => '-0.6931', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Binomial log-likelihood ──────────────────────────────────
        $seed(19, [
            ['input' => "0.3\n10\n3",   'expected_output' => '-6.1085', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n10\n5",   'expected_output' => '-6.9315', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.8\n5\n4",    'expected_output' => '-2.5018', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.9\n6\n6",    'expected_output' => '-0.6324', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Compare likelihoods ──────────────────────────────────────
        $seed(20, [
            ['input' => "0.45\n0.30",   'expected_output' => 'Model A', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.20\n0.55",   'expected_output' => 'Model B', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.40\n0.40",   'expected_output' => 'Equal',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.10\n0.80",   'expected_output' => 'Model B', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Beta-Binomial posterior parameters ───────────────────────
        $seed(21, [
            ['input' => "1\n1\n7\n3",    'expected_output' => "8\n4",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5\n10\n5",   'expected_output' => "12\n10", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n3\n5\n5",    'expected_output' => "8\n8",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n2\n0\n10",   'expected_output' => "5\n12",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Posterior Beta mean ──────────────────────────────────────
        $seed(22, [
            ['input' => "8.0\n4.0",    'expected_output' => '0.6667', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "12.0\n10.0",  'expected_output' => '0.5455', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "8.0\n8.0",    'expected_output' => '0.5000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "5.0\n12.0",   'expected_output' => '0.2941', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Posterior mean from prior + data ─────────────────────────
        $seed(23, [
            ['input' => "1.0\n1.0\n3\n10",   'expected_output' => '0.3333', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n2.0\n5\n10",   'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0\n1.0\n8\n10",   'expected_output' => '0.7857', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n9.0\n1\n5",    'expected_output' => '0.1333', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Posterior Beta parameters ────────────────────────────────
        $seed(24, [
            ['input' => "2\n2\n5\n10",   'expected_output' => "7\n7",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1\n3\n10",   'expected_output' => "4\n8",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n10\n20",  'expected_output' => "15\n15",'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n7\n2\n8",    'expected_output' => "5\n13", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Prior skew classification ────────────────────────────────
        $seed(25, [
            ['input' => "5.0\n2.0",    'expected_output' => 'Right-skewed', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n8.0",    'expected_output' => 'Left-skewed',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4.0\n4.0",    'expected_output' => 'Symmetric',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n5.0",    'expected_output' => 'Left-skewed',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Posterior mean from samples ──────────────────────────────
        $seed(26, [
            ['input' => "5\n0.2\n0.4\n0.6\n0.8\n1.0",     'expected_output' => '0.6000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1.5\n2.5\n3.5\n4.5",           'expected_output' => '3.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.1\n0.5\n0.9",                'expected_output' => '0.5000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n10.0\n20.0\n30.0\n40.0\n50.0\n60.0", 'expected_output' => '35.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q27: Posterior std from samples ───────────────────────────────
        $seed(27, [
            ['input' => "5\n0.2\n0.4\n0.6\n0.8\n1.0",   'expected_output' => '0.2828', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1.0\n2.0\n3.0\n4.0",         'expected_output' => '1.1180', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n10.0\n10.0\n10.0\n10.0\n10.0",'expected_output' => '0.0000','is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1.0\n2.0\n3.0\n4.0\n5.0\n6.0",'expected_output' => '1.7078','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: 95% Credible interval ────────────────────────────────────
        $seed(28, [
            ['input' => "100.0\n10.0",   'expected_output' => "80.4000\n119.6000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0",      'expected_output' => "-1.9600\n1.9600",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "50.0\n5.0",     'expected_output' => "40.2000\n59.8000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "200.0\n20.0",   'expected_output' => "160.8000\n239.2000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Credible interval width ──────────────────────────────────
        $seed(29, [
            ['input' => "80.4\n119.6",   'expected_output' => '39.2000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "-1.96\n1.96",   'expected_output' => '3.9200',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "40.2\n59.8",    'expected_output' => '19.6000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n100.0",    'expected_output' => '100.0000','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: P(theta > threshold) ─────────────────────────────────────
        $seed(30, [
            ['input' => "5\n0.5\n0.1\n0.3\n0.6\n0.8\n0.9",   'expected_output' => '0.6000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0.0\n-0.2\n0.1\n0.5\n0.8",        'expected_output' => '0.7500', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n0.7\n0.5\n0.6\n0.7\n0.8\n0.9\n1.0",'expected_output' => '0.5000','is_hidden' => true, 'order_index' => 3],
            ['input' => "5\n0.9\n0.1\n0.5\n0.7\n0.8\n0.95",   'expected_output' => '0.2000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: MCMC acceptance rate ─────────────────────────────────────
        $seed(31, [
            ['input' => "75\n100",   'expected_output' => '0.7500', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "50\n200",   'expected_output' => '0.2500', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "180\n200",  'expected_output' => '0.9000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n100",    'expected_output' => '0.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Effective samples after burn-in ──────────────────────────
        $seed(32, [
            ['input' => "100\n1000",   'expected_output' => '900',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "500\n2000",   'expected_output' => '1500', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "200\n500",    'expected_output' => '300',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n1000",     'expected_output' => '1000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: MCMC mean after burn-in ──────────────────────────────────
        $seed(33, [
            ['input' => "6\n2\n1.0\n2.0\n3.0\n4.0\n5.0\n6.0",          'expected_output' => '4.5000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1\n0.5\n1.0\n2.0\n3.0\n4.0",                'expected_output' => '2.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n2\n10.0\n20.0\n30.0\n40.0",                  'expected_output' => '35.0000','is_hidden' => true,  'order_index' => 3],
            ['input' => "8\n4\n1.0\n2.0\n3.0\n4.0\n5.0\n6.0\n7.0\n8.0", 'expected_output' => '6.5000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Total MCMC samples ───────────────────────────────────────
        $seed(34, [
            ['input' => "4\n1000",   'expected_output' => '4000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n500",    'expected_output' => '1000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "8\n2000",   'expected_output' => '16000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5000",   'expected_output' => '5000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Posterior median ─────────────────────────────────────────
        $seed(35, [
            ['input' => "5\n0.3\n0.5\n0.7\n0.1\n0.9",   'expected_output' => '0.50',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1.0\n2.0\n3.0\n4.0",         'expected_output' => '2.50',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n10.0\n20.0\n30.0\n40.0\n50.0",'expected_output' => '30.00','is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2.0\n8.0\n4.0\n6.0",          'expected_output' => '5.00', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Bayesian predicted value ─────────────────────────────────
        $seed(36, [
            ['input' => "5.0\n2.0\n10.0",    'expected_output' => '25.00',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.5\n4.0",     'expected_output' => '6.00',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0\n0.5\n20.0",   'expected_output' => '20.00',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "-3.0\n2.0\n5.0",    'expected_output' => '7.00',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Posterior mean slope ─────────────────────────────────────
        $seed(37, [
            ['input' => "1.0\n3.0",    'expected_output' => '2.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n1.5",    'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n4.0",    'expected_output' => '3.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n2.0",    'expected_output' => '1.0000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Residual ─────────────────────────────────────────────────
        $seed(38, [
            ['input' => "15.0\n12.5",   'expected_output' => '2.5000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "8.0\n10.0",    'expected_output' => '-2.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "100.0\n100.0", 'expected_output' => '0.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "50.0\n45.5",   'expected_output' => '4.5000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Bayesian MAE ─────────────────────────────────────────────
        $seed(39, [
            ['input' => "3\n10\n12\n20\n18\n30\n33",      'expected_output' => '2.3333',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n100\n100\n200\n200\n50\n50\n75\n75", 'expected_output' => '0.0000','is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n50\n40\n60\n80",               'expected_output' => '15.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2\n3\n4\n5\n6",             'expected_output' => '1.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Posterior uncertainty ────────────────────────────────────
        $seed(40, [
            ['input' => "5.0\n3.0",    'expected_output' => 'Informative data', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3.0\n5.0",    'expected_output' => 'Wider posterior',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4.0\n4.0",    'expected_output' => 'No update',        'is_hidden' => true,  'order_index' => 3],
            ['input' => "10.0\n2.0",   'expected_output' => 'Informative data', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Bayes Factor ─────────────────────────────────────────────
        $seed(41, [
            ['input' => "0.8\n0.4",    'expected_output' => '2.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.9\n0.1",    'expected_output' => '9.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.3\n0.6",    'expected_output' => '0.5000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n0.5",    'expected_output' => '2.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Bayes Factor interpretation ─────────────────────────────
        $seed(42, [
            ['input' => '150.0',  'expected_output' => 'Decisive', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '25.0',   'expected_output' => 'Strong',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '5.0',    'expected_output' => 'Moderate', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '1.5',    'expected_output' => 'Weak',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Model selection by log marginal likelihood ───────────────
        $seed(43, [
            ['input' => "-10.5\n-12.3",   'expected_output' => 'Model 1', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "-15.0\n-8.0",    'expected_output' => 'Model 2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-5.0\n-5.0",     'expected_output' => 'Tie',     'is_hidden' => true,  'order_index' => 3],
            ['input' => "-200.0\n-100.0", 'expected_output' => 'Model 2', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Posterior odds ───────────────────────────────────────────
        $seed(44, [
            ['input' => "1.0\n3.0",    'expected_output' => '3.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n5.0",    'expected_output' => '10.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n10.0",   'expected_output' => '5.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3.0\n2.5",    'expected_output' => '7.5000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: DIC model comparison ─────────────────────────────────────
        $seed(45, [
            ['input' => "450.0\n480.0",   'expected_output' => 'Model 1', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "600.0\n550.0",   'expected_output' => 'Model 2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "300.0\n300.0",   'expected_output' => 'Tie',     'is_hidden' => true,  'order_index' => 3],
            ['input' => "1200.0\n950.0",  'expected_output' => 'Model 2', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Grand mean ───────────────────────────────────────────────
        $seed(46, [
            ['input' => "3\n10.0\n20.0\n30.0",              'expected_output' => '20.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n5.0\n5.0\n5.0\n5.0",            'expected_output' => '5.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n100.0\n200.0",                   'expected_output' => '150.0000','is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1.0\n2.0\n3.0\n4.0\n5.0",       'expected_output' => '3.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Between-group variance ───────────────────────────────────
        $seed(47, [
            ['input' => "3\n10.0\n20.0\n30.0",      'expected_output' => '66.6667', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n5.0\n5.0\n5.0\n5.0",    'expected_output' => '0.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n100.0\n200.0",           'expected_output' => '2500.0000','is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n1.0\n2.0\n3.0",          'expected_output' => '0.6667',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: ICC ─────────────────────────────────────────────────────
        $seed(48, [
            ['input' => "4.0\n6.0",    'expected_output' => '0.4000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "9.0\n1.0",    'expected_output' => '0.9000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n9.0",    'expected_output' => '0.1000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "5.0\n5.0",    'expected_output' => '0.5000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Shrinkage factor ─────────────────────────────────────────
        $seed(49, [
            ['input' => "10\n10",   'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n9",     'expected_output' => '0.1000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n15",    'expected_output' => '0.2500', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "20\n5",    'expected_output' => '0.8000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Pooling strategy ─────────────────────────────────────────
        $seed(50, [
            ['input' => '0.05',   'expected_output' => 'Full pooling',    'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.5',    'expected_output' => 'Partial pooling', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.95',   'expected_output' => 'No pooling',      'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.0',    'expected_output' => 'Full pooling',    'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 11 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}