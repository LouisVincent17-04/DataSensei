<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 2 — Statistics for Data Science (Professional) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Professional tier
 *   2. coding_questions    — 50 questions covering expert-level statistics in Python
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (graduate / production-level Module 2 curriculum):
 *   2.1  Dimensionality & Information Theory (entropy, mutual information, PCA by hand)
 *   2.2  Robust Estimation (Huber M-estimator, Winsorisation, breakdown point)
 *   2.3  Advanced Probability (MGF, CLT convergence, characteristic functions)
 *   2.4  Multivariate Distributions (multivariate normal: PDF, Mahalanobis distance)
 *   2.5  Bayesian Inference (MAP, posterior update, conjugate priors)
 *   2.6  Time-Series Statistics (ADF stationary test, ACF/PACF, ARIMA order selection)
 *   2.7  Regularised Regression (Ridge / Lasso closed-form, elastic net, cross-val)
 *   2.8  Non-Parametric Tests (Mann-Whitney U, Kruskal-Wallis H, Wilcoxon signed-rank)
 *   2.9  Resampling & Permutation (permutation test, BCa bootstrap CI skeleton)
 *   2.10 End-to-End Pipeline (full ML-ready statistical preprocessing + report)
 *
 * Difficulty: Professional — multi-algorithm problems, numerical methods,
 *             deep statistical theory, correctness under edge cases.
 *             No scipy/sklearn/numpy; pure Python + math + statistics only.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module2CodingChallengeSeederProfessional extends Seeder
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

        $this->command->info('Creating Module 2 — Statistics for Data Science (Professional) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Statistics for Data Science',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Expert-level statistical computing in pure Python. Implement information-theoretic measures, robust estimators, Bayesian inference, time-series diagnostics, regularised regression, non-parametric tests, resampling methods, and a complete ML-ready preprocessing pipeline — from scratch, no scipy/numpy.',
                'time_limit_seconds' => 3600,
                'base_xp'            => 2000,
                'order_index'        => 2,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.1: Dimensionality & Information Theory (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Compute the **Shannon entropy** of a discrete distribution.

H(X) = −Σ p(x) × log₂(p(x))   (0 × log₂(0) = 0 by convention)

Read `n` probability values (floats summing to 1, one per line). Print H(X) rounded to 6 decimal places.

Example:
```
Input:
2
0.5
0.5
Output: 1.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nprobs = [float(input()) for _ in range(n)]\n# Compute Shannon entropy in bits\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Compute the **KL divergence** D_KL(P ‖ Q) of two probability distributions.

D_KL(P ‖ Q) = Σ P(x) × log₂(P(x) / Q(x))

Undefined (print `undefined`) if any Q(x) = 0 and P(x) > 0.

Read `n`, then `n` P values, then `n` Q values. Print D_KL rounded to 6 decimal places.

Example:
```
Input:
2
0.4
0.6
0.5
0.5
Output: 0.029049
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nP = [float(input()) for _ in range(n)]\nQ = [float(input()) for _ in range(n)]\n# Compute KL divergence D_KL(P||Q)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Compute the **mutual information** I(X;Y) from a joint probability table.

I(X;Y) = Σ_x Σ_y P(x,y) × log₂(P(x,y) / (P(x) × P(y)))

Read `r c` (rows = values of X, cols = values of Y), then `r` rows of `c` joint probabilities. Print I(X;Y) rounded to 6 decimal places.

Example:
```
Input:
2 2
0.4 0.1
0.1 0.4
Output: 0.278088
```
MD,
                'starter_code'        => "import math\n\nr, c = map(int, input().split())\njoint = []\nfor _ in range(r):\n    row = list(map(float, input().split()))\n    joint.append(row)\n# Compute mutual information\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Perform **PCA by hand** (2D → 1D) using eigendecomposition of the covariance matrix.

Given n 2D data points, compute:
1. Centre the data (subtract column means).
2. Compute the 2×2 population covariance matrix.
3. Find eigenvalues λ₁ ≥ λ₂ using the quadratic formula: λ = (tr ± √(tr²−4det)) / 2.
4. Find the principal eigenvector (unit vector) for λ₁.
5. Project all centred points onto PC1.

Print:
- `Eigenvalue 1: <val>` (rounded to 4 dp)
- `Eigenvalue 2: <val>` (rounded to 4 dp)
- `PC1: [<v1>, <v2>]` (unit eigenvector, each rounded to 4 dp)
- Then the projected scores, one per line, rounded to 4 dp.

Read `n`, then `n` lines of `x y`.

Example:
```
Input:
4
2 0
0 2
-2 0
0 -2
Output:
Eigenvalue 1: 2.0
Eigenvalue 2: 2.0
PC1: [1.0, 0.0]
-2.0
0.0
2.0
0.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\npoints = []\nfor _ in range(n):\n    x, y = map(float, input().split())\n    points.append([x, y])\n# PCA: centre, covariance, eigendecomp, project\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Compute the **Mahalanobis distance** of a point from a distribution.

D_M = √((x − μ)ᵀ Σ⁻¹ (x − μ))

For 2D case: read mean vector `μ = [μ₁, μ₂]`, covariance matrix Σ (2×2), and a query point `x`.

Print the Mahalanobis distance rounded to 4 decimal places.

Example:
```
Input:
0 0
1 0
0 1
3 4
Output: 5.0
```
*(μ=[0,0], Σ=identity, point=[3,4] → √(9+16)=5)*
MD,
                'starter_code'        => "import math\n\nmu = list(map(float, input().split()))\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\npoint = list(map(float, input().split()))\n# Compute Mahalanobis distance (2D)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.2: Robust Estimation (Q6–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
**Winsorise** a dataset by clipping values below the `p`-th percentile to that percentile, and values above the `(100−p)`-th percentile to that percentile. Use linear interpolation for percentile computation.

Read `n` values (one per line), then `p` (integer 0–50). Print the Winsorised values one per line, each rounded to 4 decimal places.

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
10
Output:
1.5
2.0
3.0
4.0
5.0
9.5
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nnums = [float(input()) for _ in range(n)]\np = int(input())\n# Winsorise dataset\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Compute the **Huber M-estimator** of location via iteratively reweighted least squares (IRLS).

Algorithm:
1. Initialise μ = median(x).
2. For each iteration:
   a. Compute residuals r_i = x_i − μ.
   b. Compute MAD = median(|r_i|) / 0.6745.
   c. Compute weights: w_i = min(1, δ / |r_i / MAD|) where δ = 1.345.
   d. Update μ = Σ(w_i × x_i) / Σ(w_i).
3. Stop after 50 iterations or when |Δμ| < 1e-8.

Read `n` values (one per line). Print the Huber location estimate rounded to 6 decimal places.

Example:
```
Input:
5
1
2
3
4
100
Output: 2.5
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Huber M-estimator (IRLS, delta=1.345)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Compute the **breakdown point** of three estimators and rank them.

Breakdown point = (max number of arbitrarily bad values the estimator can tolerate) / n

For a dataset of size n:
- Mean: breakdown point = 0 (one outlier suffices to destroy it → 0/n = 0.0)
- Median: breakdown point = floor(n/2) / n
- 10%-Trimmed Mean: breakdown point = floor(0.1 × n) / n

Read `n`. Print each estimator and its breakdown point (4 dp), sorted by breakdown point descending.

Example:
```
Input: 10
Output:
Median: 0.5
Trimmed Mean: 0.1
Mean: 0.0
```
MD,
                'starter_code'        => "n = int(input())\n# Compute and rank breakdown points\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Compute the **biweight (Tukey) location estimator**.

Algorithm:
1. Compute c = median(x), MAD = median(|x − c|) / 0.6745.
2. For each x_i: u_i = (x_i − c) / (9 × MAD).
3. Weights: w_i = (1 − u_i²)² if |u_i| < 1, else 0.
4. Location = c + (Σ w_i(x_i − c)) / Σ w_i.

Read `n` values. Print the biweight location rounded to 6 decimal places.

Example:
```
Input:
5
1
2
3
4
100
Output: 2.5
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Biweight (Tukey) location estimator\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Compute the **Least Median of Squares (LMS)** regression line for 2D data using exhaustive subsets.

For every pair of distinct points, compute the slope and intercept of the line through them, then compute the median squared residual over all points. Choose the line with the minimum median squared residual.

Read `n` pairs `x y` (one per line). Print the optimal slope and intercept, each rounded to 4 decimal places.

Example:
```
Input:
5
1 2
2 4
3 6
4 8
5 1000
Output:
Slope: 2.0
Intercept: 0.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\npoints = []\nfor _ in range(n):\n    x, y = map(float, input().split())\n    points.append((x, y))\n# LMS regression via exhaustive pair search\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 225,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.3: Advanced Probability Theory (Q11–Q15)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Compute the **moment generating function (MGF)** evaluated at `t` for a discrete distribution.

M_X(t) = Σ e^(t×x) × P(x)

Read `n` pairs of `x P(x)` (one per line), then `t`. Print M_X(t) rounded to 6 decimal places.

Example:
```
Input:
3
1 0.3
2 0.4
3 0.3
2
0.5
Output: 4.884312
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\npairs = []\nfor _ in range(n):\n    x, p = map(float, input().split())\n    pairs.append((x, p))\nt = float(input())\n# Compute M_X(t)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Simulate the **Central Limit Theorem convergence** numerically.

Given a discrete distribution and sample size `n`, compute:
- E[X̄_n] = μ (population mean)
- Var[X̄_n] = σ²/n (population variance / n)
- SE = σ/√n

Using these theoretical values (no actual simulation), compute:
P(|X̄_n − μ| > ε) ≤ Var[X̄_n] / ε²  (Chebyshev's inequality bound)

Read `n` x-P(x) pairs, then sample_size `n_s`, then `epsilon`. Print:
- `SE: <val>`
- `Chebyshev bound: <val>`

Both rounded to 6 decimal places.

Example:
```
Input:
2
0 0.5
1 0.5
100
0.1
Output:
SE: 0.05
Chebyshev bound: 0.25
```
MD,
                'starter_code'        => "import math\n\nk = int(input())\npairs = []\nfor _ in range(k):\n    x, p = map(float, input().split())\n    pairs.append((x, p))\nn_s = int(input())\nepsilon = float(input())\n# CLT: SE and Chebyshev bound\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Compute the **cumulants** κ₁ (mean), κ₂ (variance), κ₃, κ₄ of a discrete distribution.

Cumulant relationships:
- κ₁ = μ₁  (first raw moment)
- κ₂ = μ₂ − μ₁²  (variance)
- κ₃ = μ₃ − 3μ₂μ₁ + 2μ₁³  (third central moment)
- κ₄ = μ₄ − 4μ₃μ₁ − 3μ₂² + 12μ₂μ₁² − 6μ₁⁴

where μ_r = Σ x^r × P(x) (r-th raw moment).

Read `n` x-P(x) pairs. Print κ₁ through κ₄ rounded to 6 decimal places.

Example:
```
Input:
2
0 0.5
1 0.5
Output:
k1: 0.5
k2: 0.25
k3: 0.0
k4: -0.125
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\npairs = []\nfor _ in range(n):\n    x, p = map(float, input().split())\n    pairs.append((x, p))\n# Compute cumulants k1..k4\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Compute the **Jensen-Shannon divergence** JSD(P ‖ Q).

JSD(P,Q) = (1/2) × D_KL(P ‖ M) + (1/2) × D_KL(Q ‖ M)
where M = (P + Q) / 2

Read `n`, then `n` P values, then `n` Q values. Print JSD rounded to 6 decimal places (log base 2).

Example:
```
Input:
2
0.4
0.6
0.6
0.4
Output: 0.012255
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nP = [float(input()) for _ in range(n)]\nQ = [float(input()) for _ in range(n)]\n# Compute Jensen-Shannon divergence\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Compute the **total variation distance** TV(P, Q) and **Hellinger distance** H(P, Q).

TV(P,Q) = (1/2) Σ |P(x) − Q(x)|
H(P,Q) = (1/√2) √(Σ (√P(x) − √Q(x))²)

Read `n`, then `n` P values, then `n` Q values. Print TV and H each rounded to 6 decimal places.

Example:
```
Input:
2
0.7
0.3
0.4
0.6
Output:
TV: 0.3
H: 0.230940
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nP = [float(input()) for _ in range(n)]\nQ = [float(input()) for _ in range(n)]\n# Compute TV and Hellinger distances\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.4: Multivariate & Bayesian (Q16–Q20)
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Evaluate the **bivariate normal PDF** at a point (x, y).

f(x,y) = (1 / (2π σ₁ σ₂ √(1−ρ²))) × exp(−z/(2(1−ρ²)))

where z = ((x−μ₁)/σ₁)² − 2ρ((x−μ₁)/σ₁)((y−μ₂)/σ₂) + ((y−μ₂)/σ₂)²

Read `μ₁ μ₂ σ₁ σ₂ ρ` (space-separated), then `x y`. Print the PDF value in scientific notation with 6 significant figures (Python format: `{:.6e}`).

Example:
```
Input:
0 0 1 1 0
0 0
Output: 1.591549e-01
```
MD,
                'starter_code'        => "import math\n\nparams = list(map(float, input().split()))\nmu1, mu2, s1, s2, rho = params\nx, y = map(float, input().split())\n# Bivariate normal PDF\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Perform **Bayesian updating** with a Beta-Binomial conjugate model.

Prior: Beta(α, β). Observe `s` successes in `n` trials.
Posterior: Beta(α + s, β + n − s).

Compute posterior mean, mode (if α+s > 1 and β+n−s > 1, else print `undefined`), and 95% credible interval approximation using the normal approximation to the Beta:
- mean = (α+s) / (α+s+β+n−s)
- var = (a×b) / ((a+b)² × (a+b+1))  where a=α+s, b=β+n−s
- 95% CI: mean ± 1.96 × √var

Read `alpha beta s n`. Print posterior mean, mode, CI lower, CI upper — each rounded to 6 dp.

Example:
```
Input:
1 1 7 10
Output:
Posterior Mean: 0.666667
Posterior Mode: 0.666667
CI Lower: 0.378
CI Upper: 0.955333
```
MD,
                'starter_code'        => "import math\n\nalpha, beta, s, n = map(float, input().split())\n# Bayesian update: Beta-Binomial conjugate\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Compute the **MAP estimate** for a Gaussian likelihood with Gaussian prior.

Likelihood: X | μ ~ N(μ, σ_l²), i.i.d. samples x₁…xₙ.
Prior: μ ~ N(μ₀, σ₀²).

MAP estimate:
μ_MAP = (μ₀/σ₀² + n×x̄/σ_l²) / (1/σ₀² + n/σ_l²)

Posterior variance:
σ_post² = 1 / (1/σ₀² + n/σ_l²)

Read `mu0 sigma0 sigma_l`, then `n` observations. Print μ_MAP and σ_post each rounded to 6 dp.

Example:
```
Input:
0 1 1
3
1
2
3
Output:
MAP: 1.714286
Posterior SD: 0.5
```
MD,
                'starter_code'        => "import math\n\nmu0, sigma0, sigma_l = map(float, input().split())\nn = int(input())\nobs = [float(input()) for _ in range(n)]\n# Gaussian MAP estimation\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Implement the **Naive Bayes classifier** for binary features.

Given a dataset of class labels (0 or 1) and binary feature vectors, compute:
- Prior P(C=0), P(C=1) using Laplace smoothing (add 1 to each class count).
- Likelihood P(xi=1|C=c) for each feature using Laplace smoothing (add 1 to numerator and denominator+2).

For a query vector, compute log posterior (log prior + Σ log likelihood) for each class and predict the class with higher log posterior.

Read `n` (training examples), `f` (features), then `n` lines of `y x1 x2...xf`, then `q` queries (each `f` binary values). Print the predicted class for each query.

Example:
```
Input:
4
2
0 0 1
0 1 0
1 1 1
1 0 0
2
0 1
1 0
Output:
0
1
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nf = int(input())\ntrain = []\nfor _ in range(n):\n    vals = list(map(int, input().split()))\n    train.append(vals)\nq = int(input())\nqueries = []\nfor _ in range(q):\n    queries.append(list(map(int, input().split())))\n# Naive Bayes with Laplace smoothing\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Compute the **Dirichlet-Multinomial posterior predictive** probability.

After observing counts c₁…cₖ from a Multinomial with Dirichlet prior Dir(α₁…αk), the posterior predictive probability of the next observation being category j is:

P(next = j | data) = (α_j + c_j) / (Σ α_i + Σ c_i)

Read `k`, then `k` prior alphas (space-separated), then `k` observed counts. Print the posterior predictive probability for each category rounded to 6 dp.

Example:
```
Input:
3
1 1 1
3 5 2
Output:
0.266667
0.4
0.333333
```
MD,
                'starter_code'        => "k = int(input())\nalphas = list(map(float, input().split()))\ncounts = list(map(float, input().split()))\n# Dirichlet-Multinomial posterior predictive\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.5: Time-Series Statistics (Q21–Q25)
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Compute the **full autocorrelation function (ACF)** up to lag `k`.

ACF(h) = [Σ_{t=h+1}^{n} (x_t − x̄)(x_{t-h} − x̄)] / [Σ_{t=1}^{n} (x_t − x̄)²]

Print ACF(0) through ACF(k), one per line, rounded to 4 decimal places.

Read `n` values, then `k`.

Example:
```
Input:
6
1
2
3
2
1
2
3
Output:
1.0
0.2
-0.4667
-0.4667
-0.0667
0.2
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\nk = int(input())\n# Compute ACF(0) to ACF(k)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Compute the **first difference** and **second difference** of a time series to assess stationarity.

First difference: Δx_t = x_t − x_{t-1}
Second difference: Δ²x_t = Δx_t − Δx_{t-1}

Print the original variance, Δx variance, and Δ²x variance, each rounded to 4 dp. Then print whether the series is likely stationary: `stationary` if Δx variance < original variance, else `non-stationary`.

Read `n` values.

Example:
```
Input:
5
1
3
6
10
15
Output:
Var(x): 24.64
Var(dx): 0.8
Var(d2x): 0.0
stationary
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Differencing and stationarity check\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Compute the **Ljung-Box Q statistic** for testing serial autocorrelation.

Q = n(n+2) × Σ_{h=1}^{m} r_h² / (n−h)

where r_h is the sample autocorrelation at lag h.

Critical value at α=0.05, df=m: use lookup:
m=1→3.841, m=2→5.991, m=3→7.815, m=4→9.488, m=5→11.07

Read `n` time-series values (one per line), then `m`. Print Q rounded to 4 dp, and `Reject H0` (series not white noise) or `Fail to reject H0`.

Example:
```
Input:
6
1
2
3
2
1
2
3
Output:
Q: 5.1571
Fail to reject H0
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\nm = int(input())\n# Ljung-Box Q statistic\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Fit an **AR(1) model** (autoregressive order 1) using OLS.

Model: x_t = φ × x_{t-1} + c + ε_t

Estimate φ and c by regressing x_t on x_{t-1} (use the OLS slope/intercept formula).

Read `n` values (one per line). Print φ and c each rounded to 6 dp. Then print the one-step-ahead forecast for x_{n+1} rounded to 4 dp.

Example:
```
Input:
5
1
2
3
4
5
Output:
phi: 1.0
c: 0.0
Forecast: 6.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Fit AR(1) via OLS and forecast\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Compute the **Holt-Winters double exponential smoothing** forecast (trend model).

Level:  L_t = α × x_t + (1−α) × (L_{t-1} + T_{t-1})
Trend:  T_t = β × (L_t − L_{t-1}) + (1−β) × T_{t-1}

Initialise: L_1 = x_1, T_1 = x_2 − x_1.
Start smoothing from t=2.

Read `n` values, `alpha`, `beta`, and `h` (forecast horizon). Print the h-step-ahead forecast rounded to 4 dp.

Example:
```
Input:
4
10
12
13
15
0.8
0.2
2
Output: 17.2864
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\nalpha = float(input())\nbeta = float(input())\nh = int(input())\n# Holt-Winters double exponential smoothing\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.6: Regularised Regression (Q26–Q30)
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Compute the **Ridge regression** coefficients (L2) using the closed-form solution.

β_ridge = (XᵀX + λI)⁻¹ Xᵀy

For p=1 predictor (+ intercept), expand manually:
- Intercept β₀ = ȳ (ridge does not regularise intercept)
- β₁ = (Σ(xi−x̄)(yi−ȳ)) / (Σ(xi−x̄)² + λ)

Read `n` pairs `x y` (one per line), then `lambda`. Print β₀ and β₁ each rounded to 6 dp.

Example:
```
Input:
4
1 2
2 4
3 6
4 8
1.0
Output:
b0: 0.0
b1: 1.8
```
MD,
                'starter_code'        => "n = int(input())\npoints = []\nfor _ in range(n):\n    x, y = map(float, input().split())\n    points.append((x, y))\nlam = float(input())\n# Ridge regression closed-form\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Compute the **Lasso regression** coefficient (L1) via coordinate descent for a single predictor.

β₁_lasso = soft_threshold(Σ(xi−x̄)(yi−ȳ) / Σ(xi−x̄)², λ / (2 × Σ(xi−x̄)²))

soft_threshold(z, γ) = sign(z) × max(0, |z| − γ)

Intercept β₀ = ȳ − β₁ × x̄

Read `n` pairs `x y` (one per line), then `lambda`. Print β₀ and β₁ each rounded to 6 dp.

Example:
```
Input:
4
1 2
2 4
3 6
4 8
2.0
Output:
b0: 0.5
b1: 1.75
```
MD,
                'starter_code'        => "n = int(input())\npoints = []\nfor _ in range(n):\n    x, y = map(float, input().split())\n    points.append((x, y))\nlam = float(input())\n# Lasso regression (single predictor, coordinate descent formula)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Compute **Elastic Net** coefficients (single predictor, closed-form approximation).

β₁_enet = soft_threshold(Σ(xi−x̄)(yi−ȳ) / Σ(xi−x̄)², λ₁/2) / (1 + λ₂ / Σ(xi−x̄)²)

where λ₁ is the L1 penalty and λ₂ is the L2 penalty.

Intercept β₀ = ȳ − β₁ × x̄

Read `n` pairs `x y`, then `lambda1` and `lambda2`. Print β₀ and β₁ each rounded to 6 dp.

Example:
```
Input:
4
1 2
2 4
3 6
4 8
1.0
1.0
Output:
b0: 0.25
b1: 1.8125
```
MD,
                'starter_code'        => "n = int(input())\npoints = []\nfor _ in range(n):\n    x, y = map(float, input().split())\n    points.append((x, y))\nl1 = float(input())\nl2 = float(input())\n# Elastic Net (single predictor)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Perform **leave-one-out cross-validation (LOOCV)** for simple linear regression.

For each fold, fit a simple OLS regression on the n−1 remaining points, predict the held-out point, compute the squared error. Report the LOOCV-RMSE.

For efficiency, use the shortcut formula: e_i = (y_i − ŷ_i) / (1 − h_ii) where h_ii = 1/n + (xi−x̄)²/Sxx. This avoids refitting.

Read `n` pairs `x y`. Print LOOCV-RMSE rounded to 6 dp.

Example:
```
Input:
4
1 2
2 4
3 6
4 9
Output: 1.5617
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\npoints = []\nfor _ in range(n):\n    x, y = map(float, input().split())\n    points.append((x, y))\n# LOOCV-RMSE via hat matrix shortcut\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Compute the **AIC** and **BIC** for a linear regression model.

AIC = n × ln(RSS/n) + 2(p+1)
BIC = n × ln(RSS/n) + (p+1) × ln(n)

where p = number of predictors (not counting intercept), RSS = Σ(yi − ŷi)².

Read `n` actual values, then `n` predicted values, then `p`. Print AIC and BIC each rounded to 4 dp.

Example:
```
Input:
4
2
4
6
8
2.1
3.9
6.1
7.9
1
Output:
AIC: -15.0745
BIC: -15.5564
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nactual = [float(input()) for _ in range(n)]\npred = [float(input()) for _ in range(n)]\np = int(input())\n# Compute AIC and BIC\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.7: Non-Parametric Tests (Q31–Q35)
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Perform the **Mann-Whitney U test** (exact U statistic).

U₁ = n₁n₂ + n₁(n₁+1)/2 − R₁

where R₁ = sum of ranks of group 1 in the combined ranking (average ranks for ties).
U = min(U₁, U₂) where U₂ = n₁n₂ − U₁.

Read `n1` values, then `n2` values. Print U₁, U₂, and U (the test statistic = min(U₁,U₂)).

Example:
```
Input:
3
1
3
5
3
2
4
6
Output:
U1: 4.0
U2: 5.0
U: 4.0
```
MD,
                'starter_code'        => "n1 = int(input())\ng1 = [float(input()) for _ in range(n1)]\nn2 = int(input())\ng2 = [float(input()) for _ in range(n2)]\n# Mann-Whitney U test\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Perform the **Kruskal-Wallis H test** for k independent groups.

H = [12/(N(N+1))] × Σ(Rj²/nj) − 3(N+1)

where Rj = sum of ranks in group j, nj = size of group j, N = total.
Use average ranks for ties.

Approximate critical value: χ² with df = k−1 at α=0.05 (use lookup: df=1→3.841, df=2→5.991, df=3→7.815, df=4→9.488).

Read `k`, then `k` groups (each line starts with `ni` followed by `ni` values). Print H rounded to 4 dp, df, and `Reject H0` or `Fail to reject H0`.

Example:
```
Input:
3
3 1 2 3
3 4 5 6
3 7 8 9
Output:
H: 8.0
df: 2
Reject H0
```
MD,
                'starter_code'        => "k = int(input())\ngroups = []\nfor _ in range(k):\n    line = list(map(float, input().split()))\n    groups.append(line[1:])\n# Kruskal-Wallis H test\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Perform the **Wilcoxon signed-rank test** for a one-sample location test.

Steps:
1. Compute differences d_i = x_i − μ₀.
2. Remove zeros.
3. Rank the |d_i| values (average ranks for ties).
4. W⁺ = sum of ranks where d_i > 0; W⁻ = sum of ranks where d_i < 0.
5. W = min(W⁺, W⁻).

Read `n` values, then `mu_0`. Print W⁺, W⁻, and W.

Example:
```
Input:
5
1
3
5
7
9
5
Output:
W+: 9.0
W-: 6.0
W: 6.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\nmu0 = float(input())\n# Wilcoxon signed-rank test\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Compute the **Spearman rank correlation** with **p-value approximation**.

Spearman rs computed as before. The t-statistic for testing H0: ρs=0 is:

t = rs × √(n−2) / √(1−rs²)

Use a simplified two-tailed p-value approximation with critical t at α=0.05 (same lookup as before).

Read `n`, then `n` x values, then `n` y values. Print rs, t, and `Reject H0` or `Fail to reject H0` (at α=0.05, two-tailed). Round rs and t to 4 dp.

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
2
1
4
3
6
5
Output:
rs: 0.8286
t: 3.0
Reject H0
```
MD,
                'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n# Spearman rs with t-test\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Perform the **Fisher exact test** on a 2×2 contingency table.

P(table | margins) = (a+b)!(c+d)!(a+c)!(b+d)! / (n! × a! × b! × c! × d!)

Compute the two-tailed p-value: sum the probabilities of all tables with probability ≤ the observed table probability (with the same marginals).

Read `a b c d` (2×2 table). Print the observed probability and two-tailed p-value, each rounded to 6 dp.

Example:
```
Input: 1 9 11 3
Output:
P(observed): 0.001346
p-value: 0.002691
```
MD,
                'starter_code'        => "import math\n\na, b, c, d = map(int, input().split())\n# Fisher exact test (2x2)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 225,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.8: Resampling & Permutation (Q36–Q40)
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Perform a **permutation test** for the difference in means.

Given two groups and a fixed set of permutation assignments (instead of random), compute the permutation distribution of the difference in means and the two-tailed p-value.

Read `n1` group-1 values, then `n2` group-2 values. Then `B` permutations, each as a binary string of length n1+n2 (0=group1, 1=group2). Compute:
- observed difference: x̄₁ − x̄₂
- for each permutation, compute the difference in means
- p-value = proportion of permutations with |diff| ≥ |observed|

Print observed difference and p-value each rounded to 4 dp.

Example:
```
Input:
2
1
2
2
3
4
3
0011
1010
1001
Output:
Observed diff: -2.0
p-value: 1.0
```
MD,
                'starter_code'        => "n1 = int(input())\ng1 = [float(input()) for _ in range(n1)]\nn2 = int(input())\ng2 = [float(input()) for _ in range(n2)]\nB = int(input())\nperms = [input().strip() for _ in range(B)]\n# Permutation test for difference in means\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Compute the **bootstrap bias-corrected (BC) percentile CI** from a given set of bootstrap statistics.

Algorithm:
1. Compute z₀ = Φ⁻¹(proportion of bootstrap stats < observed stat).
2. Lower: Φ(2z₀ − z_{α/2}), Upper: Φ(2z₀ + z_{α/2}) as percentile levels.
3. Take those percentiles from the sorted bootstrap distribution.

Use z_{α/2} = 1.96 for 95% CI. Use the A&S normal CDF and its inverse (via Newton's method).

Read the observed statistic, then `B` bootstrap statistics. Print BC CI lower and upper, each rounded to 4 dp.

Example:
```
Input:
5.0
5
4.5
4.8
5.0
5.2
5.5
Output:
BC CI: [4.5, 5.2]
```
MD,
                'starter_code'        => "import math\n\nobs = float(input())\nB = int(input())\nboot_stats = sorted([float(input()) for _ in range(B)])\n# Bias-corrected bootstrap CI\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 225,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Compute the **subsampling confidence interval** for the median.

For each sub-sample of size `m` from `n` observations (given explicitly as index lists), compute the sub-sample median. The CI is the (α/2, 1−α/2) quantiles of the sub-sample medians.

Use linear interpolation for quantiles. α = 0.05.

Read `n` values, then `B` sub-samples each as `m` space-separated indices. Print CI lower and upper rounded to 4 dp.

Example:
```
Input:
5
1 2 3 4 5
4
0 1 2 3
1 2 3 4
0 2 3 4
0 1 3 4
Output:
CI: [2.0, 3.5]
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\ndata = list(map(float, input().split()))\nB = int(input())\nsubsamples = []\nfor _ in range(B):\n    idx = list(map(int, input().split()))\n    subsamples.append(idx)\n# Subsampling CI for the median\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Compute the **cross-validated R²** using k-fold CV for simple linear regression.

For each fold: fit OLS on training folds, predict validation fold, compute SS_res.
Cross-validated R² = 1 − (Σ SS_res across all folds) / SS_tot (using full dataset mean).

Read `n` pairs `x y`, then `k`. Print cross-validated R² rounded to 6 dp.

Example:
```
Input:
6
1 2
2 4
3 6
4 8
5 10
6 12
3
Output: 1.0
```
MD,
                'starter_code'        => "n = int(input())\npoints = []\nfor _ in range(n):\n    x, y = map(float, input().split())\n    points.append((x, y))\nk = int(input())\n# K-fold cross-validated R²\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Compute the **Markov chain stationary distribution** by power iteration.

Given a `k × k` transition matrix P (rows sum to 1), compute the stationary distribution π such that π P = π. Use power iteration: start with a uniform distribution and multiply by P repeatedly until convergence (max change < 1e-8, max 1000 iterations).

Read `k`, then `k` rows of `k` floats. Print the stationary probability for each state rounded to 6 dp.

Example:
```
Input:
2
0.7 0.3
0.4 0.6
Output:
0.571429
0.428571
```
MD,
                'starter_code'        => "k = int(input())\nP = []\nfor _ in range(k):\n    row = list(map(float, input().split()))\n    P.append(row)\n# Power iteration for stationary distribution\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.9: Advanced Hypothesis & Testing Framework (Q41–Q45)
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Compute the **Bonferroni correction** and **Benjamini-Hochberg (BH) FDR correction** for multiple comparisons.

Bonferroni: reject H₀ᵢ if p_i ≤ α/m.
BH: sort p-values, find largest k where p_(k) ≤ k×α/m, reject all H₀₁…H₀ₖ.

Read `m` p-values (one per line) and `alpha`. Print for each (original order):
`<p_value>: Bonferroni=<Reject/Accept>, BH=<Reject/Accept>`

Round output p-values to 4 dp.

Example:
```
Input:
4
0.01
0.04
0.03
0.20
0.05
Output:
0.01: Bonferroni=Reject, BH=Reject
0.04: Bonferroni=Accept, BH=Reject
0.03: Bonferroni=Accept, BH=Reject
0.20: Bonferroni=Accept, BH=Accept
```
MD,
                'starter_code'        => "m = int(input())\np_values = [float(input()) for _ in range(m)]\nalpha = float(input())\n# Bonferroni and BH FDR corrections\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Compute the **Bayes factor** for a one-sample t-test using the JZS prior (simplified).

BF₁₀ = P(data | H₁) / P(data | H₀)

Use the simple formula for a one-sided test:
BF₁₀ = √(n/2π) × (1 + t²/(n−1))^(−n/2) / beta(1/2, (n−1)/2) × ...

Actually, implement the **Savage-Dickey density ratio** approximation:
BF₁₀ ≈ (1 + t²/df)^(−(df+1)/2) × √(df/π) / (Γ((df+1)/2) / Γ(df/2)) / (posterior_density_at_0)

This is complex — instead implement the **unit information prior** BF:
BF₁₀ = exp(0.5 × (t² × n / (n+1) − log(n+1)))

Read the t-statistic and `n`. Print BF₁₀ rounded to 4 dp. If BF₁₀ > 10: `Strong evidence for H1`. If 3–10: `Moderate evidence for H1`. If 1–3: `Anecdotal evidence for H1`. If < 1: `Evidence for H0`.

Example:
```
Input:
3.5
20
Output:
BF10: 18.9059
Strong evidence for H1
```
MD,
                'starter_code'        => "import math\n\nt = float(input())\nn = int(input())\n# Bayes factor via unit information prior\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Compute the **Sequential Probability Ratio Test (SPRT)** stopping decision.

Boundaries: A = (1−β)/α (reject H0), B = β/(1−α) (accept H0).

For each observation, update the likelihood ratio:
Λ_n = Π f₁(xᵢ) / f₀(xᵢ)

For a normal distribution testing μ₀=0 vs μ₁=δ with known σ=1:
log Λ_n = δ × Σxᵢ − n × δ²/2

Stop when Λ_n ≥ A (reject H0), Λ_n ≤ B (accept H0), or all observations processed.

Read `delta`, `alpha`, `beta`, then observations one per line (end with `END`). Print the stop decision and the stopping index (1-based), or `No decision` if exhausted.

Example:
```
Input:
1.0
0.05
0.20
1.5
2.0
END
Output:
Reject H0 at n=2
```
MD,
                'starter_code'        => "import math\n\ndelta = float(input())\nalpha = float(input())\nbeta = float(input())\nobs = []\nwhile True:\n    line = input().strip()\n    if line == 'END':\n        break\n    obs.append(float(line))\n# SPRT decision\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 225,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Compute the **sample size** needed for a two-sample t-test with given power.

Formula (equal group sizes):
n = 2 × ((z_α/2 + z_β) × σ / δ)²

where:
- z_α/2 = 1.96 for α=0.05 two-tailed
- z_β for desired power: 0.84 for 80%, 1.04 for 85%, 1.28 for 90%, 1.65 for 95%
- δ = |μ₁ − μ₂| (effect size)
- σ = pooled standard deviation

Read `sigma`, `delta`, and power (as 0.80, 0.85, 0.90, or 0.95). Print the required `n` per group (ceiling).

Example:
```
Input:
10
5
0.80
Output: 63
```
MD,
                'starter_code'        => "import math\n\nsigma = float(input())\ndelta = float(input())\npower = float(input())\n# Sample size for two-sample t-test\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Compute the **Akaike Information Criterion with correction (AICc)** for small samples.

AICc = AIC + (2(p+1)(p+2)) / (n − p − 2)

where AIC = n × ln(RSS/n) + 2(p+1) and p = number of predictors.

Also compute the **Δ AICc** and **Akaike weights** for a list of models (relative likelihood).

Read `n` (sample size), then `m` models, each as `RSS p` (space-separated). Print AICc for each model, then the model index (1-based) with lowest AICc, then the Akaike weight for each model (rounded to 4 dp).

Example:
```
Input:
20
2
10.5 1
8.0 2
Output:
Model 1 AICc: -28.0263
Model 2 AICc: -31.5547
Best model: 2
Weight 1: 0.1813
Weight 2: 0.8187
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nm = int(input())\nmodels = []\nfor _ in range(m):\n    rss, p = map(float, input().split())\n    models.append((rss, int(p)))\n# AICc and Akaike weights\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2.10: End-to-End ML-Ready Statistical Pipeline (Q46–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Implement **Yeo-Johnson power transformation** to normalise a variable.

For each x_i:
- If λ ≠ 0 and x ≥ 0: ((x+1)^λ − 1) / λ
- If λ = 0 and x ≥ 0: ln(x+1)
- If λ ≠ 2 and x < 0: −((−x+1)^(2−λ) − 1) / (2−λ)
- If λ = 2 and x < 0: −ln(−x+1)

Read `n` values, then `lambda`. Print transformed values rounded to 6 dp.

Example:
```
Input:
4
-2
0
1
4
0.5
Output:
-1.707107
0.0
1.242641
3.449490
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nnums = [float(input()) for _ in range(n)]\nlam = float(input())\n# Yeo-Johnson transformation\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Compute the **optimal Box-Cox lambda** via maximum likelihood (grid search over λ ∈ {−2, −1.5, −1, −0.5, 0, 0.5, 1, 1.5, 2}).

For each λ, transform x → y(λ) and compute the log-likelihood:
LL(λ) = −(n/2) × ln(Var_pop(y(λ))) + (λ−1) × Σ ln(x_i)

where Box-Cox requires x > 0.

Box-Cox transform: y = (x^λ − 1)/λ for λ≠0, ln(x) for λ=0.

Read `n` positive values. Print the optimal λ (highest LL) and the LL at that λ, rounded to 4 dp.

Example:
```
Input:
4
1
2
4
8
Output:
Optimal lambda: 0.0
LL: -0.8047
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nnums = [float(input()) for _ in range(n)]\n# Box-Cox lambda search\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Perform a **full multivariate outlier detection** pipeline using Mahalanobis distance and chi-squared cutoff.

Steps:
1. Compute mean vector and population covariance matrix for `k` variables.
2. Compute Mahalanobis distance for each observation.
3. Flag as outlier if D² > chi2_crit (use: k=2→5.991, k=3→7.815, k=4→9.488 at α=0.05).

Read `k`, then `n` observations of `k` values each. Print each observation's D² rounded to 4 dp and `outlier` or `inlier`.

Example:
```
Input:
2
5
1 2
2 3
3 4
4 5
10 20
Output:
1.6: inlier
0.0: inlier
0.0: inlier
1.6: inlier
37.44: outlier
```
MD,
                'starter_code'        => "import math\n\nk = int(input())\nn = int(input())\nobs = []\nfor _ in range(n):\n    row = list(map(float, input().split()))\n    obs.append(row)\n# Mahalanobis outlier detection\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 225,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Implement **iterative imputation** for missing values using simple linear regression.

Given a dataset with two columns X and Y where some Y values are `NA`:
1. Use complete rows to fit a simple OLS regression (X → Y).
2. Impute missing Y values as ŷ = β₀ + β₁ × x.
3. Refit with imputed values.
4. Repeat until convergence (max Δ imputed value < 1e-6 or 50 iterations).

Read `n` rows of `x y` (y may be `NA`). Print the final imputed dataset (all rows), each `x y` pair rounded to 4 dp.

Example:
```
Input:
4
1 2
2 4
3 NA
4 8
Output:
1.0 2.0
2.0 4.0
3.0 6.0
4.0 8.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nrows = []\nfor _ in range(n):\n    parts = input().split()\n    x = float(parts[0])\n    y = None if parts[1] == 'NA' else float(parts[1])\n    rows.append([x, y])\n# Iterative imputation via OLS\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 225,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Build a **complete ML-ready statistical preprocessing & diagnostic report** for a dataset.

Given a dataset of `n` observations with 2 numeric columns X and Y (Y is the target), produce the following report:

```
=== DATA QUALITY ===
Rows: n
Missing X: <count>
Missing Y: <count>

=== UNIVARIATE (X) ===
Mean: <val>
Std: <val>  (sample)
Skewness: <val>  (Fisher)
Kurtosis: <val>  (excess)
Outliers (IQR): <count>

=== UNIVARIATE (Y) ===
Mean: <val>
Std: <val>  (sample)
Skewness: <val>  (Fisher)
Kurtosis: <val>  (excess)
Outliers (IQR): <count>

=== BIVARIATE ===
Pearson r: <val>
Spearman rs: <val>
Slope: <val>
Intercept: <val>
R-squared: <val>
RMSE: <val>
AIC: <val>
BIC: <val>

=== STATIONARITY (Y) ===
Var(Y): <val>
Var(dY): <val>
Likely stationary: <yes/no>
```

Exclude rows where X or Y is `NA` from calculations. Round all numeric values to 4 dp. Skewness/Kurtosis undefined for n<3/n<4 → print `undefined`.

Read `n`, then `n` lines of `x y` (may contain `NA`).

Example input results in a full formatted report. (Test cases verify the complete output.)
MD,
                'starter_code'        => "import math\n\nn = int(input())\nraw = []\nfor _ in range(n):\n    parts = input().split()\n    x = None if parts[0] == 'NA' else float(parts[0])\n    y = None if parts[1] == 'NA' else float(parts[1])\n    raw.append((x, y))\n# Full ML preprocessing and diagnostic report\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],
        ];

        // ─────────────────────────────────────────────────────────────────
        // 3. INSERT QUESTIONS
        // ─────────────────────────────────────────────────────────────────

        $questions = [];

        foreach ($questionDefs as $def) {
            DB::table('coding_questions')->updateOrInsert(
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

        // ── Q1: Shannon entropy ───────────────────────────────────────────
        $seed(1, [
            ['input' => "2\n0.5\n0.5",          'expected_output' => "1.0",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.5\n0.25\n0.25",   'expected_output' => "1.5",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1.0\n0.0",           'expected_output' => "0.0",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0.25\n0.25\n0.25\n0.25", 'expected_output' => "2.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: KL divergence ─────────────────────────────────────────────
        $seed(2, [
            ['input' => "2\n0.4\n0.6\n0.5\n0.5",   'expected_output' => "0.029049",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.5\n0.5\n0.5\n0.5",   'expected_output' => "0.0",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1.0\n0.0\n0.5\n0.5",   'expected_output' => "undefined",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0.3\n0.3\n0.4\n0.2\n0.4\n0.4",'expected_output' => "0.018194",'is_hidden' => true,'order_index' => 4],
        ]);

        // ── Q3: mutual information ────────────────────────────────────────
        $seed(3, [
            ['input' => "2 2\n0.4 0.1\n0.1 0.4",       'expected_output' => "0.278088",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n0.25 0.25\n0.25 0.25",   'expected_output' => "0.0",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n0.5 0.0\n0.0 0.5",       'expected_output' => "1.0",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 3\n0.1 0.2 0.2\n0.2 0.1 0.2",'expected_output'=> "0.005525",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: PCA 2D→1D ─────────────────────────────────────────────────
        $seed(4, [
            ['input' => "4\n2 0\n0 2\n-2 0\n0 -2",     'expected_output' => "Eigenvalue 1: 2.0\nEigenvalue 2: 2.0\nPC1: [1.0, 0.0]\n-2.0\n0.0\n2.0\n0.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 0\n2 0\n3 0",            'expected_output' => "Eigenvalue 1: 0.6667\nEigenvalue 2: 0.0\nPC1: [1.0, 0.0]\n-1.0\n0.0\n1.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1\n2 2\n3 3\n4 4",       'expected_output' => "Eigenvalue 1: 1.25\nEigenvalue 2: 0.0\nPC1: [0.7071, 0.7071]\n-2.1213\n-0.7071\n0.7071\n2.1213", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n0 1\n0 2\n0 3\n0 4",       'expected_output' => "Eigenvalue 1: 1.25\nEigenvalue 2: 0.0\nPC1: [0.0, 1.0]\n-1.5\n-0.5\n0.5\n1.5", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q5: Mahalanobis distance ──────────────────────────────────────
        $seed(5, [
            ['input' => "0 0\n1 0\n0 1\n3 4",   'expected_output' => "5.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 0\n1 0\n0 1\n0 0",   'expected_output' => "0.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2\n2 0\n0 1\n4 5",   'expected_output' => "3.6056",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0 0\n4 0\n0 9\n2 3",   'expected_output' => "1.6036",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Winsorisation ─────────────────────────────────────────────
        $seed(6, [
            ['input' => "6\n1\n2\n3\n4\n5\n100\n10",  'expected_output' => "1.5\n2.0\n3.0\n4.0\n5.0\n9.5",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n0",           'expected_output' => "1.0\n2.0\n3.0\n4.0",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0\n10\n20\n30\n100\n20",  'expected_output' => "4.0\n10.0\n20.0\n30.0\n64.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1\n2\n3\n4\n5\n6\n25",    'expected_output' => "1.375\n2.0\n3.0\n4.0\n5.0\n5.625",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Huber M-estimator ─────────────────────────────────────────
        $seed(7, [
            ['input' => "5\n1\n2\n3\n4\n100",   'expected_output' => "2.5",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1\n2\n3\n4\n5",     'expected_output' => "3.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n1\n1\n100",      'expected_output' => "1.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n10\n10\n10\n11\n12\n1000", 'expected_output' => "10.5", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q8: breakdown points ──────────────────────────────────────────
        $seed(8, [
            ['input' => "10",   'expected_output' => "Median: 0.5\nTrimmed Mean: 0.1\nMean: 0.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "20",   'expected_output' => "Median: 0.5\nTrimmed Mean: 0.1\nMean: 0.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "7",    'expected_output' => "Median: 0.4286\nTrimmed Mean: 0.0\nMean: 0.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "100",  'expected_output' => "Median: 0.5\nTrimmed Mean: 0.1\nMean: 0.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: biweight location ─────────────────────────────────────────
        $seed(9, [
            ['input' => "5\n1\n2\n3\n4\n100",   'expected_output' => "2.5",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1\n2\n3\n4\n5",     'expected_output' => "3.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n10\n10\n1000",      'expected_output' => "10.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4",        'expected_output' => "2.5",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: LMS regression ───────────────────────────────────────────
        $seed(10, [
            ['input' => "5\n1 2\n2 4\n3 6\n4 8\n5 1000",   'expected_output' => "Slope: 2.0\nIntercept: 0.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 1\n2 2\n3 3\n4 400",         'expected_output' => "Slope: 1.0\nIntercept: 0.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 0\n1 2\n2 4\n3 100",         'expected_output' => "Slope: 2.0\nIntercept: 0.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 3\n2 5\n3 7\n4 9\n5 -100",   'expected_output' => "Slope: 2.0\nIntercept: 1.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: MGF ──────────────────────────────────────────────────────
        $seed(11, [
            ['input' => "3\n1 0.3\n2 0.4\n3 0.3\n2\n0.5",  'expected_output' => "4.884312",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0.5\n1 0.5\n1\n1.0",         'expected_output' => "1.859141",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 0.5\n1 0.5\n1\n0.0",         'expected_output' => "1.0",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0 0.2\n1 0.5\n2 0.3\n1\n1.0",  'expected_output' => "3.069321",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: CLT / Chebyshev ──────────────────────────────────────────
        $seed(12, [
            ['input' => "2\n0 0.5\n1 0.5\n100\n0.1",   'expected_output' => "SE: 0.05\nChebyshev bound: 0.25",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0.5\n1 0.5\n400\n0.05",  'expected_output' => "SE: 0.025\nChebyshev bound: 0.25",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0.2\n2 0.5\n3 0.3\n50\n0.2", 'expected_output' => "SE: 0.068557\nChebyshev bound: 0.117841", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0 0.3\n1 0.7\n100\n0.1",   'expected_output' => "SE: 0.045826\nChebyshev bound: 0.21",  'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q13: cumulants ────────────────────────────────────────────────
        $seed(13, [
            ['input' => "2\n0 0.5\n1 0.5",             'expected_output' => "k1: 0.5\nk2: 0.25\nk3: 0.0\nk4: -0.125",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 0.3\n2 0.4\n3 0.3",      'expected_output' => "k1: 2.0\nk2: 0.6\nk3: 0.0\nk4: -0.18",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 0.2\n1 0.8",             'expected_output' => "k1: 0.8\nk2: 0.16\nk3: -0.096\nk4: -0.0384",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0 0.25\n1 0.5\n2 0.25",    'expected_output' => "k1: 1.0\nk2: 0.5\nk3: 0.0\nk4: -0.25",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Jensen-Shannon divergence ────────────────────────────────
        $seed(14, [
            ['input' => "2\n0.4\n0.6\n0.6\n0.4",     'expected_output' => "0.012255",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.5\n0.5\n0.5\n0.5",     'expected_output' => "0.0",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1.0\n0.0\n0.0\n1.0",     'expected_output' => "1.0",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0.3\n0.3\n0.4\n0.4\n0.3\n0.3",'expected_output'=> "0.000799",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: TV and Hellinger ─────────────────────────────────────────
        $seed(15, [
            ['input' => "2\n0.7\n0.3\n0.4\n0.6",     'expected_output' => "TV: 0.3\nH: 0.230940",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.5\n0.5\n0.5\n0.5",     'expected_output' => "TV: 0.0\nH: 0.0",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1.0\n0.0\n0.0\n1.0",     'expected_output' => "TV: 1.0\nH: 1.0",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0.3\n0.3\n0.4\n0.2\n0.3\n0.5",'expected_output'=> "TV: 0.1\nH: 0.076394",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: bivariate normal PDF ─────────────────────────────────────
        $seed(16, [
            ['input' => "0 0 1 1 0\n0 0",         'expected_output' => "1.591549e-01",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 0 1 1 0\n1 0",         'expected_output' => "9.653235e-02",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 1 2 0.5\n1 2",       'expected_output' => "7.957747e-02",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0 0 2 2 0\n2 2",         'expected_output' => "1.967435e-02",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Bayesian Beta-Binomial update ────────────────────────────
        $seed(17, [
            ['input' => "1 1 7 10",     'expected_output' => "Posterior Mean: 0.666667\nPosterior Mode: 0.666667\nCI Lower: 0.378\nCI Upper: 0.955333",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1 0 10",     'expected_output' => "Posterior Mean: 0.083333\nPosterior Mode: undefined\nCI Lower: -0.062933\nCI Upper: 0.229600", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2 8 10",     'expected_output' => "Posterior Mean: 0.625\nPosterior Mode: 0.642857\nCI Lower: 0.360\nCI Upper: 0.89",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 5 50 100",   'expected_output' => "Posterior Mean: 0.5\nPosterior Mode: 0.5\nCI Lower: 0.402\nCI Upper: 0.598",                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Gaussian MAP ─────────────────────────────────────────────
        $seed(18, [
            ['input' => "0 1 1\n3\n1\n2\n3",         'expected_output' => "MAP: 1.714286\nPosterior SD: 0.5",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 10 1\n3\n1\n2\n3",        'expected_output' => "MAP: 1.9891\nPosterior SD: 0.5774",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5 2 3\n4\n4\n5\n6\n7",      'expected_output' => "MAP: 5.3529\nPosterior SD: 1.323",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "0 1 0.5\n2\n0\n0",          'expected_output' => "MAP: 0.0\nPosterior SD: 0.2236",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Naive Bayes ──────────────────────────────────────────────
        $seed(19, [
            ['input' => "4\n2\n0 0 1\n0 1 0\n1 1 1\n1 0 0\n2\n0 1\n1 0",   'expected_output' => "0\n1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n2\n0 0 0\n0 0 0\n1 1 1\n1 1 1\n1\n1 1",        'expected_output' => "1",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n3\n0 1 0 1\n0 0 1 0\n0 1 1 0\n1 0 0 1\n1 1 0 0\n1 0 1 1\n2\n0 1 1\n1 0 0", 'expected_output' => "0\n1", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n2\n0 1 0\n1 0 1\n1\n1 1",                      'expected_output' => "1",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Dirichlet-Multinomial ────────────────────────────────────
        $seed(20, [
            ['input' => "3\n1 1 1\n3 5 2",   'expected_output' => "0.266667\n0.4\n0.333333",            'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 1\n5 5",       'expected_output' => "0.5\n0.5",                            'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2 2 2\n0 0 0",   'expected_output' => "0.333333\n0.333333\n0.333333",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0.5 0.5 0.5 0.5\n10 20 5 5", 'expected_output' => "0.258333\n0.508333\n0.141667\n0.141667", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q21: ACF ─────────────────────────────────────────────────────
        $seed(21, [
            ['input' => "6\n1\n2\n3\n2\n1\n2\n3",   'expected_output' => "1.0\n0.2\n-0.4667\n-0.4667\n-0.0667\n0.2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1\n2\n3\n4\n5\n2",      'expected_output' => "1.0\n0.4\n-0.2",                               'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n1\n1\n1\n2",         'expected_output' => "1.0\n1.0\n1.0",                                'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1\n-1\n1\n-1\n1\n-1\n2",'expected_output' => "1.0\n-1.0\n1.0",                              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: differencing & stationarity ─────────────────────────────
        $seed(22, [
            ['input' => "5\n1\n3\n6\n10\n15",    'expected_output' => "Var(x): 24.64\nVar(dx): 0.8\nVar(d2x): 0.0\nstationary",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1\n2\n3\n4\n5",      'expected_output' => "Var(x): 2.0\nVar(dx): 0.0\nVar(d2x): 0.0\nstationary",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n3\n7\n2\n8",      'expected_output' => "Var(x): 4.24\nVar(dx): 14.5\nVar(d2x): 29.5\nnon-stationary",'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n10\n10\n10",     'expected_output' => "Var(x): 0.0\nVar(dx): 0.0\nVar(d2x): 0.0\nstationary",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Ljung-Box ────────────────────────────────────────────────
        $seed(23, [
            ['input' => "6\n1\n2\n3\n2\n1\n2\n3",   'expected_output' => "Q: 5.1571\nFail to reject H0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n1\n1\n1\n1\n1\n1\n3",   'expected_output' => "Q: 0.0\nFail to reject H0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "8\n1\n2\n3\n4\n5\n6\n7\n8\n2",'expected_output'=> "Q: 7.2727\nReject H0",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n10\n8\n11\n9\n10\n1",   'expected_output' => "Q: 2.5316\nFail to reject H0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: AR(1) ────────────────────────────────────────────────────
        $seed(24, [
            ['input' => "5\n1\n2\n3\n4\n5",    'expected_output' => "phi: 1.0\nc: 0.0\nForecast: 6.0",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n2\n4\n8\n16",      'expected_output' => "phi: 2.4286\nc: -2.8571\nForecast: 35.9717", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n5\n5\n5\n5",    'expected_output' => "phi: 1.0\nc: 0.0\nForecast: 5.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n8\n6\n4",      'expected_output' => "phi: 1.0\nc: -2.0\nForecast: 2.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Holt-Winters ─────────────────────────────────────────────
        $seed(25, [
            ['input' => "4\n10\n12\n13\n15\n0.8\n0.2\n2",   'expected_output' => "17.2864",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n10\n15\n1.0\n1.0\n1",        'expected_output' => "20.0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n100\n100\n100\n100\n0.5\n0.5\n3",'expected_output'=> "100.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1\n3\n5\n7\n9\n0.5\n0.5\n2",    'expected_output' => "13.0",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Ridge regression ─────────────────────────────────────────
        $seed(26, [
            ['input' => "4\n1 2\n2 4\n3 6\n4 8\n1.0",    'expected_output' => "b0: 0.0\nb1: 1.8",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2\n2 4\n3 6\n0.0",         'expected_output' => "b0: 0.0\nb1: 2.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2\n2 4\n3 6\n4 8\n10.0",   'expected_output' => "b0: 0.0\nb1: 1.25",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0 1\n1 3\n2 5\n3 7\n2.0",    'expected_output' => "b0: 1.0\nb1: 1.7143",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Lasso regression ─────────────────────────────────────────
        $seed(27, [
            ['input' => "4\n1 2\n2 4\n3 6\n4 8\n2.0",    'expected_output' => "b0: 0.5\nb1: 1.75",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2\n2 4\n3 6\n0.0",         'expected_output' => "b0: 0.0\nb1: 2.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2\n2 4\n3 6\n4 8\n100.0",  'expected_output' => "b0: 5.0\nb1: 0.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0 0\n1 2\n2 4\n3 6\n1.0",    'expected_output' => "b0: 0.25\nb1: 1.875",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Elastic Net ──────────────────────────────────────────────
        $seed(28, [
            ['input' => "4\n1 2\n2 4\n3 6\n4 8\n1.0\n1.0",    'expected_output' => "b0: 0.25\nb1: 1.8125",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2\n2 4\n3 6\n0.0\n0.0",         'expected_output' => "b0: 0.0\nb1: 2.0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2\n2 4\n3 6\n4 8\n2.0\n2.0",    'expected_output' => "b0: 0.5\nb1: 1.625",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2\n2 4\n3 6\n4 8\n0.0\n4.0",    'expected_output' => "b0: 0.0\nb1: 1.5",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: LOOCV-RMSE ───────────────────────────────────────────────
        $seed(29, [
            ['input' => "4\n1 2\n2 4\n3 6\n4 9",         'expected_output' => "1.5617",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2\n2 4\n3 6\n4 8",         'expected_output' => "0.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 1\n2 4\n3 9\n4 15\n5 24",  'expected_output' => "3.2154",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 5\n2 5\n3 5\n4 5",         'expected_output' => "0.0",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: AIC and BIC ──────────────────────────────────────────────
        $seed(30, [
            ['input' => "4\n2\n4\n6\n8\n2.1\n3.9\n6.1\n7.9\n1",   'expected_output' => "AIC: -15.0745\nBIC: -15.5564",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n2\n4\n6\n8\n2\n4\n6\n8\n1",            'expected_output' => "AIC: -inf\nBIC: -inf",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1\n2\n3\n4\n5\n6\n1.1\n1.9\n3.1\n3.9\n5.1\n5.9\n1",'expected_output'=> "AIC: -18.2476\nBIC: -18.8398",'is_hidden' => true, 'order_index' => 3],
            ['input' => "5\n10\n20\n30\n40\n50\n11\n19\n31\n39\n51\n1",'expected_output'=> "AIC: -15.5897\nBIC: -16.1609", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q31: Mann-Whitney U ───────────────────────────────────────────
        $seed(31, [
            ['input' => "3\n1\n3\n5\n3\n2\n4\n6",          'expected_output' => "U1: 4.0\nU2: 5.0\nU: 4.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n3\n4\n5\n6",          'expected_output' => "U1: 0.0\nU2: 9.0\nU: 0.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4\n4\n5\n6\n7\n8",    'expected_output' => "U1: 0.0\nU2: 16.0\nU: 0.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2\n3\n3\n1\n2\n3",          'expected_output' => "U1: 4.5\nU2: 4.5\nU: 4.5",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Kruskal-Wallis ───────────────────────────────────────────
        $seed(32, [
            ['input' => "3\n3 1 2 3\n3 4 5 6\n3 7 8 9",       'expected_output' => "H: 8.0\ndf: 2\nReject H0",            'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3 1 2 3\n3 1 2 3\n3 1 2 3",       'expected_output' => "H: 0.0\ndf: 2\nFail to reject H0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n4 1 2 3 4\n4 5 6 7 8",            'expected_output' => "H: 6.6\ndf: 1\nReject H0",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2 1 2\n2 2 3\n2 3 4",             'expected_output' => "H: 3.4286\ndf: 2\nFail to reject H0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Wilcoxon signed-rank ─────────────────────────────────────
        $seed(33, [
            ['input' => "5\n1\n3\n5\n7\n9\n5",       'expected_output' => "W+: 9.0\nW-: 6.0\nW: 6.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n2.5",        'expected_output' => "W+: 4.5\nW-: 5.5\nW: 4.5",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n10\n12\n9\n11\n13\n10",  'expected_output' => "W+: 7.0\nW-: 8.0\nW: 7.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n5\n5\n5\n5\n5",          'expected_output' => "W+: 0.0\nW-: 0.0\nW: 0.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Spearman with p-value ─────────────────────────────────────
        $seed(34, [
            ['input' => "6\n1\n2\n3\n4\n5\n6\n2\n1\n4\n3\n6\n5",   'expected_output' => "rs: 0.8286\nt: 3.0\nReject H0",            'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n1\n2\n3\n4\n5\n6\n1\n2\n3\n4\n5\n6",   'expected_output' => "rs: 1.0\nt: inf\nReject H0",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1\n2\n3\n4\n5\n6\n6\n5\n4\n3\n2\n1",   'expected_output' => "rs: -1.0\nt: -inf\nReject H0",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4\n2\n1\n4\n3",               'expected_output' => "rs: 0.4\nt: 0.6547\nFail to reject H0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Fisher exact test ────────────────────────────────────────
        $seed(35, [
            ['input' => "1 9 11 3",   'expected_output' => "P(observed): 0.001346\np-value: 0.002691",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5 5 5 5",    'expected_output' => "P(observed): 0.347222\np-value: 1.0",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "0 10 10 0",  'expected_output' => "P(observed): 0.0\np-value: 0.0",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 1 1 3",    'expected_output' => "P(observed): 0.228571\np-value: 0.485714",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: permutation test ─────────────────────────────────────────
        $seed(36, [
            ['input' => "2\n1\n2\n2\n3\n4\n3\n0011\n1010\n1001",           'expected_output' => "Observed diff: -2.0\np-value: 1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n2\n2\n10\n20\n3\n0011\n1010\n1001",         'expected_output' => "Observed diff: -13.5\np-value: 0.6667", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3\n3\n4\n5\n6\n4\n000111\n010101\n100110\n011100", 'expected_output' => "Observed diff: -3.0\np-value: 0.25", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0\n0\n2\n0\n0\n2\n0011\n1100", 'expected_output' => "Observed diff: 0.0\np-value: 1.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

// ── Q37: Bias-corrected bootstrap CI ──────────────────────────────
        $seed(37, [
            ['input' => "5.0\n5\n4.5\n4.8\n5.0\n5.2\n5.5",       'expected_output' => "BC CI: [4.5, 5.2]",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.5\n4\n2.1\n2.4\n2.8\n3.0",           'expected_output' => "BC CI: [2.1, 3.0]",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0\n7\n8.0\n8.5\n9.0\n9.5\n10.5\n11.0\n12.0", 'expected_output' => "BC CI: [9.5, 12.0]", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.0\n3\n-1.0\n0.0\n1.0",               'expected_output' => "BC CI: [-1.0, 1.0]",   'is_hidden' => true,  'order_index' => 4],
        ]);

// ── Q38: Subsampling confidence interval ──────────────────────────
        $seed(38, [
            ['input' => "5\n1 2 3 4 5\n4\n0 1 2 3\n1 2 3 4\n0 2 3 4\n0 1 3 4", 'expected_output' => "CI: [2.075, 3.425]", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n10 20 30 40\n2\n0 1\n2 3",                        'expected_output' => "CI: [15.5, 34.5]",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1 1 2 2 3 3\n3\n0 1 2\n3 4 5\n1 3 5",             'expected_output' => "CI: [1.05, 2.95]",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0 0 0\n2\n0 1\n1 2",                              'expected_output' => "CI: [0.0, 0.0]",     'is_hidden' => true,  'order_index' => 4],
        ]);

// ── Q39: Cross-validated R² ───────────────────────────────────────
        $seed(39, [
            ['input' => "6\n1 2\n2 4\n3 6\n4 8\n5 10\n6 12\n3",    'expected_output' => "1.0",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2\n2 4\n3 6\n4 9\n4",                'expected_output' => "0.776625", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1\n2 1\n3 1\n4 1\n2",                'expected_output' => "0.0",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 3\n2 5\n3 7\n4 9\n5 11\n5",          'expected_output' => "1.0",       'is_hidden' => true,  'order_index' => 4],
        ]);

// ── Q40: Markov chain stationary distribution ─────────────────────
        $seed(40, [
            ['input' => "2\n0.7 0.3\n0.4 0.6",          'expected_output' => "0.571429\n0.428571",            'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.5 0.5 0\n0 0.5 0.5\n0.5 0 0.5", 'expected_output' => "0.333333\n0.333333\n0.333333", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1.0 0.0\n0.1 0.9",          'expected_output' => "1.0\n0.0",                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0.8 0.1 0.1\n0.2 0.7 0.1\n0.3 0.3 0.4", 'expected_output' => "0.548387\n0.322581\n0.129032", 'is_hidden' => true, 'order_index' => 4],
        ]);

// ── Q41: Bonferroni & BH Correction ───────────────────────────────
        $seed(41, [
            ['input' => "4\n0.01\n0.04\n0.03\n0.20\n0.05",    'expected_output' => "0.01: Bonferroni=Reject, BH=Reject\n0.04: Bonferroni=Accept, BH=Reject\n0.03: Bonferroni=Accept, BH=Reject\n0.20: Bonferroni=Accept, BH=Accept", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.001\n0.01\n0.05\n0.05",       'expected_output' => "0.001: Bonferroni=Reject, BH=Reject\n0.01: Bonferroni=Reject, BH=Reject\n0.05: Bonferroni=Accept, BH=Accept", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0.1\n0.2\n0.3\n0.4\n0.5\n0.05", 'expected_output' => "0.1: Bonferroni=Accept, BH=Accept\n0.2: Bonferroni=Accept, BH=Accept\n0.3: Bonferroni=Accept, BH=Accept\n0.4: Bonferroni=Accept, BH=Accept\n0.5: Bonferroni=Accept, BH=Accept", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0.02\n0.03\n0.05",              'expected_output' => "0.02: Bonferroni=Reject, BH=Reject\n0.03: Bonferroni=Accept, BH=Accept", 'is_hidden' => true, 'order_index' => 4],
        ]);

// ── Q42: Bayes Factor ─────────────────────────────────────────────
        $seed(42, [
            ['input' => "3.5\n20",   'expected_output' => "BF10: 18.9059\nStrong evidence for H1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n50",   'expected_output' => "BF10: 0.2299\nEvidence for H0",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.5\n30",   'expected_output' => "BF10: 2.7663\nAnecdotal evidence for H1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.8\n100",  'expected_output' => "BF10: 3.9984\nModerate evidence for H1", 'is_hidden' => true,  'order_index' => 4],
        ]);

// ── Q43: SPRT ─────────────────────────────────────────────────────
        $seed(43, [
            ['input' => "1.0\n0.05\n0.20\n1.5\n2.0\nEND",        'expected_output' => "Reject H0 at n=2",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n0.05\n0.20\n-1.0\n-2.0\nEND",     'expected_output' => "Accept H0 at n=2",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n0.01\n0.01\n0.1\n0.2\nEND",       'expected_output' => "No decision",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.0\n0.10\n0.10\n1.5\nEND",            'expected_output' => "Reject H0 at n=1",       'is_hidden' => true,  'order_index' => 4],
        ]);

// ── Q44: Sample size calculation ──────────────────────────────────
        $seed(44, [
            ['input' => "10\n5\n0.80",   'expected_output' => "63",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "15\n2\n0.95",   'expected_output' => "1468", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n0.90",    'expected_output' => "22",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "20\n10\n0.85",  'expected_output' => "72",  'is_hidden' => true,  'order_index' => 4],
        ]);

// ── Q45: AICc ─────────────────────────────────────────────────────
        $seed(45, [
            ['input' => "20\n2\n10.5 1\n8.0 2",      'expected_output' => "Model 1 AICc: -28.0263\nModel 2 AICc: -31.5547\nBest model: 2\nWeight 1: 0.1462\nWeight 2: 0.8538", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "15\n3\n5.0 1\n4.8 2\n4.7 3", 'expected_output' => "Model 1 AICc: -10.4578\nModel 2 AICc: -6.9142\nBest model: 1\nWeight 1: 0.8354\nWeight 2: 0.1425\nWeight 3: 0.0221", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "50\n2\n100.0 5\n105.0 2",  'expected_output' => "Model 1 AICc: 58.6277\nModel 2 AICc: 53.6441\nBest model: 2\nWeight 1: 0.0764\nWeight 2: 0.9236", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "30\n2\n20.0 1\n20.0 2",    'expected_output' => "Model 1 AICc: -6.6341\nModel 2 AICc: -3.7317\nBest model: 1\nWeight 1: 0.8102\nWeight 2: 0.1898", 'is_hidden' => true, 'order_index' => 4],
        ]);

// ── Q46: Yeo-Johnson ──────────────────────────────────────────────
        $seed(46, [
            ['input' => "4\n-2\n0\n1\n4\n0.5",   'expected_output' => "-1.707107\n0.0\n0.828427\n2.472136", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n0.0",      'expected_output' => "0.693147\n1.098612\n1.386294",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n-5\n-1\n2.0",       'expected_output' => "-1.791759\n-0.693147",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n-1\n0\n1\n1.0",     'expected_output' => "-0.5\n0.0\n1.0",                    'is_hidden' => true,  'order_index' => 4],
        ]);

// ── Q47: Optimal Box-Cox Lambda ───────────────────────────────────
        $seed(47, [
            ['input' => "4\n1\n2\n4\n8",        'expected_output' => "Optimal lambda: 0.0\nLL: -0.8047",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n4\n9",          'expected_output' => "Optimal lambda: 0.5\nLL: -1.3322",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n0.5\n0.25\n0.125", 'expected_output' => "Optimal lambda: 0.0\nLL: 0.5816",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2\n4\n6",          'expected_output' => "Optimal lambda: 1.0\nLL: -2.3155",  'is_hidden' => true,  'order_index' => 4],
        ]);

// ── Q48: Multivariate outlier detection ───────────────────────────
        $seed(48, [
            ['input' => "2\n5\n1 2\n2 3\n3 4\n4 5\n10 20",    'expected_output' => "1.6: inlier\n0.0: inlier\n0.0: inlier\n1.6: inlier\n37.44: outlier", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n4\n1 1\n2 2\n3 3\n4 4",          'expected_output' => "2.25: inlier\n0.25: inlier\n0.25: inlier\n2.25: inlier", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n4\n0 0 0\n1 0 0\n0 1 0\n0 0 1",  'expected_output' => "2.25: inlier\n2.25: inlier\n2.25: inlier\n2.25: inlier", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n4\n0 0\n10 0\n0 10\n100 100",    'expected_output' => "0.3333: inlier\n0.3333: inlier\n0.3333: inlier\n9.3333: outlier", 'is_hidden' => true, 'order_index' => 4],
        ]);

// ── Q49: Iterative Imputation ─────────────────────────────────────
        $seed(49, [
            ['input' => "4\n1 2\n2 4\n3 NA\n4 8",           'expected_output' => "1.0 2.0\n2.0 4.0\n3.0 6.0\n4.0 8.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1 3\n2 5\n3 7\n4 NA\n5 11",    'expected_output' => "1.0 3.0\n2.0 5.0\n3.0 7.0\n4.0 9.0\n5.0 11.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 0\n1 1\n2 NA\n3 NA",         'expected_output' => "0.0 0.0\n1.0 1.0\n2.0 2.0\n3.0 3.0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n1 10\n2 20\n3 NA",             'expected_output' => "1.0 10.0\n2.0 20.0\n3.0 30.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

// ── Q50: ML-Ready Report ──────────────────────────────────────────
        $seed(50, [
            ['input' => "5\n1 2\n2 4\n3 6\n4 8\n5 10", 'expected_output' => "=== DATA QUALITY ===\nRows: 5\nMissing X: 0\nMissing Y: 0\n\n=== UNIVARIATE (X) ===\nMean: 3.0000\nStd: 1.5811\nSkewness: 0.0000\nKurtosis: -1.2000\nOutliers (IQR): 0\n\n=== UNIVARIATE (Y) ===\nMean: 6.0000\nStd: 3.1623\nSkewness: 0.0000\nKurtosis: -1.2000\nOutliers (IQR): 0\n\n=== BIVARIATE ===\nPearson r: 1.0000\nSpearman rs: 1.0000\nSlope: 2.0000\nIntercept: 0.0000\nR-squared: 1.0000\nRMSE: 0.0000\nAIC: -inf\nBIC: -inf\n\n=== STATIONARITY (Y) ===\nVar(Y): 10.0000\nVar(dY): 0.0000\nLikely stationary: stationary", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 10\n2 NA\n3 30\nNA 40", 'expected_output' => "=== DATA QUALITY ===\nRows: 4\nMissing X: 1\nMissing Y: 1\n\n=== UNIVARIATE (X) ===\nMean: 2.0000\nStd: 1.4142\nSkewness: undefined\nKurtosis: undefined\nOutliers (IQR): 0\n\n=== UNIVARIATE (Y) ===\nMean: 20.0000\nStd: 14.1421\nSkewness: undefined\nKurtosis: undefined\nOutliers (IQR): 0\n\n=== BIVARIATE ===\nPearson r: 1.0000\nSpearman rs: 1.0000\nSlope: 10.0000\nIntercept: 0.0000\nR-squared: 1.0000\nRMSE: 0.0000\nAIC: -inf\nBIC: -inf\n\n=== STATIONARITY (Y) ===\nVar(Y): 200.0000\nVar(dY): 400.0000\nLikely stationary: non-stationary", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2\n2 2\n3 2", 'expected_output' => "=== DATA QUALITY ===\nRows: 3\nMissing X: 0\nMissing Y: 0\n\n=== UNIVARIATE (X) ===\nMean: 2.0000\nStd: 1.0000\nSkewness: 0.0000\nKurtosis: undefined\nOutliers (IQR): 0\n\n=== UNIVARIATE (Y) ===\nMean: 2.0000\nStd: 0.0000\nSkewness: undefined\nKurtosis: undefined\nOutliers (IQR): 0\n\n=== BIVARIATE ===\nPearson r: 0.0000\nSpearman rs: 0.0000\nSlope: 0.0000\nIntercept: 2.0000\nR-squared: 0.0000\nRMSE: 0.0000\nAIC: -inf\nBIC: -inf\n\n=== STATIONARITY (Y) ===\nVar(Y): 0.0000\nVar(dY): 0.0000\nLikely stationary: stationary", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "6\n10 5\n20 4\n30 3\n40 2\n50 1\n60 0", 'expected_output' => "=== DATA QUALITY ===\nRows: 6\nMissing X: 0\nMissing Y: 0\n\n=== UNIVARIATE (X) ===\nMean: 35.0000\nStd: 18.7083\nSkewness: 0.0000\nKurtosis: -1.2000\nOutliers (IQR): 0\n\n=== UNIVARIATE (Y) ===\nMean: 2.5000\nStd: 1.8708\nSkewness: 0.0000\nKurtosis: -1.2000\nOutliers (IQR): 0\n\n=== BIVARIATE ===\nPearson r: -1.0000\nSpearman rs: -1.0000\nSlope: -0.1000\nIntercept: 6.0000\nR-squared: 1.0000\nRMSE: 0.0000\nAIC: -inf\nBIC: -inf\n\n=== STATIONARITY (Y) ===\nVar(Y): 3.5000\nVar(dY): 0.0000\nLikely stationary: stationary", 'is_hidden' => true, 'order_index' => 4],
        ]);

$this->command->info('Module 2 coding questions & test cases seeded successfully!');
    }
}