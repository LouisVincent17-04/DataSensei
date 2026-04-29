<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 6 — Introduction to Modeling & Simulation (University Student) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the University Student tier
 *   2. coding_questions    — 50 questions covering applied Modeling & Simulation
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mapped to lessons 327–336):
 *   L6.1  Modeling & Simulation — model types, validation, abstraction
 *   L6.2  Random Numbers & Probability — inverse transform, acceptance-rejection
 *   L6.3  Monte Carlo Methods — variance reduction, importance sampling
 *   L6.4  Discrete-Event Simulation — M/M/1 queue theory, Little's law
 *   L6.5  Differential Equations — RK4, stability, stiff equations
 *   L6.6  SIR Model — SEIR, vaccination, herd immunity, R_eff
 *   L6.7  Agent-Based Modeling — flocking, emergence, lattice automata
 *   L6.8  System Dynamics — Causal Loop Diagrams, delay models, Bass diffusion
 *   L6.9  Sensitivity Analysis — tornado diagrams, PRCC, Sobol indices
 *   L6.10 Output Analysis — spectral analysis, regenerative simulation, control variates
 *
 * Difficulty: University Student — pure Python; multi-step algorithms requiring
 * solid mathematical background (calculus, probability, statistics, linear algebra).
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module6CodingChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'university_student')->first();

        if (! $category) {
            $this->command->error('University Student category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 6 — Introduction to Modeling & Simulation (University Student) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Introduction to Modeling & Simulation',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply rigorous Modeling & Simulation techniques in pure Python. Implement RK4 integration, SEIR epidemic dynamics, M/M/1 queueing theory, variance reduction for Monte Carlo, Bass diffusion, agent flocking rules, and advanced output analysis — building real simulation systems from mathematical foundations.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 2000,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Modeling Foundations (Q1–Q4)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Compute the **dimensional analysis** check for a model. Read `n` variable names with their dimensions (as products of base units M, L, T with integer exponents, format: `name M_exp L_exp T_exp`). Then read an equation as a list of variable names to multiply. Check if the result is dimensionless (all exponents = 0). Print `dimensionless` or `not dimensionless: M^a L^b T^c` with the resulting exponents.

Example:
```
Input:
3
v 0 1 -1
t 0 0 1
d 0 1 0
v t
Output: dimensionless: M^0 L^1 T^0
```

Wait — note that v×t should equal distance (L), not dimensionless. Print the resulting dimensions.

Example:
```
Input:
3
v 0 1 -1
t 0 0 1
d 0 1 0
v t
Output: M^0 L^1 T^0
```
MD,
                'starter_code'        => "n = int(input())\nvars_ = {}\nfor _ in range(n):\n    parts = input().split()\n    vars_[parts[0]] = (int(parts[1]), int(parts[2]), int(parts[3]))\nexpr = input().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Implement the **inverse transform method** for generating samples from an exponential distribution. Given U ~ Uniform(0,1), X = −(1/lambda) × ln(1 − U).

Read `lambda` and `n` uniform samples. Print the corresponding exponential samples rounded to 4 decimal places, one per line.

Example:
```
Input:
2
4
0.1
0.5
0.9
0.99
Output:
0.0527
0.3466
1.1513
2.3026
```
MD,
                'starter_code'        => "import math\nlambda_ = float(input())\nn = int(input())\nsamples = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Apply **acceptance-rejection sampling**. Use the uniform distribution as the proposal. Read `n` pairs (x, u) where x is a candidate and u ~ Uniform(0,1). Accept x if u ≤ f(x) / M where f(x) = 6x(1-x) (Beta(2,2)) and M = 1.5. Print `accept` or `reject` for each pair.

Example:
```
Input:
3
0.5 0.9
0.5 0.6
0.2 0.3
Output:
accept
reject
accept
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Compute the **empirical type I error rate** (false positive rate). Read `n` p-values from hypothesis tests. Count how many are ≤ `alpha` (given on the last line) under the null hypothesis. Print the empirical type I error rate rounded to 4 decimal places.

Example:
```
Input:
5
0.03
0.12
0.04
0.55
0.01
0.05
Output: 0.6000
```
MD,
                'starter_code'        => "n = int(input())\npvalues = [float(input()) for _ in range(n)]\nalpha = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Random Numbers & Variance Reduction (Q5–Q9)
            // ═══════════════════════════════════════════════════════════════

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Implement **antithetic variates** variance reduction. Read `n` pairs of uniform samples (u, 1-u) representing a function f(u) = u². Compute the standard estimate (mean of f(u)) and the antithetic estimate (mean of (f(u) + f(1-u))/2). Print both means and the variance reduction factor (var_standard / var_antithetic). All rounded to 4 decimal places.

Example:
```
Input:
4
0.1
0.4
0.7
0.9
Output:
standard_mean: 0.4425
antithetic_mean: 0.3350
var_reduction: 1.8478
```
MD,
                'starter_code'        => "n = int(input())\nsamples = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Implement **stratified sampling**. Divide [0,1] into `k` equal strata. Read `n` uniform samples (already sorted) and `k`. For each stratum, average the f(u) = u² values in that stratum. Print the stratified mean estimate rounded to 4 decimal places.

Example:
```
Input:
6
0.05
0.15
0.35
0.45
0.65
0.85
3
Output: 0.3358
```
MD,
                'starter_code'        => "n = int(input())\nsamples = [float(input()) for _ in range(n)]\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Implement the **control variate** estimator. We estimate E[f(U)] = E[U²] using control variate C(U) = U with known mean E[C] = 0.5. Read `n` uniform samples. Compute:

c* = -Cov(f, C) / Var(C)
estimate = mean_f + c* × (mean_C − 0.5)

Print c* and the controlled estimate rounded to 4 decimal places.

Example:
```
Input:
4
0.1
0.4
0.7
0.9
Output:
c_star: -1.0667
estimate: 0.3413
```
MD,
                'starter_code'        => "n = int(input())\nsamples = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Generate samples from a **Normal distribution** using the Box-Muller transform. Read `n` pairs (u1, u2) of uniform samples (format: `u1 u2` per line). For each pair compute:

Z0 = sqrt(−2 ln u1) × cos(2π u2)
Z1 = sqrt(−2 ln u1) × sin(2π u2)

Print Z0 and Z1 for each pair (2 values per line, space-separated), rounded to 4 decimal places.

Example:
```
Input:
2
0.1 0.2
0.5 0.7
Output:
1.8216 -0.8429
0.8255 1.0716
```
MD,
                'starter_code'        => "import math\nn = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Implement a **Linear Congruential Generator (LCG)** for pseudo-random numbers. Given seed `X0`, multiplier `a`, increment `c`, modulus `m`:

X_{n+1} = (a × X_n + c) mod m
U_n = X_n / m

Read `X0`, `a`, `c`, `m`, and `n`. Print the first `n` U values rounded to 4 decimal places, one per line.

Example:
```
Input:
1
5
3
16
5
Output:
0.0625
0.3750
0.0000
0.1875
0.0000
```
MD,
                'starter_code'        => "X = int(input())\na = int(input())\nc = int(input())\nm = int(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Advanced Monte Carlo (Q10–Q13)
            // ═══════════════════════════════════════════════════════════════

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Compute a **Monte Carlo estimate with confidence interval** for E[g(X)] where X ~ Exp(lambda). Read `lambda`, `n` uniform samples, and confidence level `alpha` (e.g. 0.95). Use g(X) = X² and inverse transform sampling. Print mean, CI lower, CI upper rounded to 4 decimal places.

Example:
```
Input:
1
5
0.1
0.5
0.9
0.3
0.7
0.95
Output:
mean: 1.9733
ci_lower: -0.8461
ci_upper: 4.7927
```
MD,
                'starter_code'        => "import math\nlambda_ = float(input())\nn = int(input())\nsamples = [float(input()) for _ in range(n)]\nalpha = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Estimate the **probability of a rare event** using importance sampling. We want P(X > t) where X ~ Exp(1). Under importance distribution Exp(mu) (mu < 1 so larger X values are more likely):

weight(x) = f(x) / g(x) = exp(-x) / (mu × exp(-mu × x)) = exp(-(1-mu)×x) / mu

Read mu, threshold `t`, and `n` exponential samples from Exp(mu) (given as inverse-transform from uniforms). Print the IS estimate rounded to 6 decimal places.

Example:
```
Input:
0.5
5
4
0.1
0.5
0.8
0.95
Output: 0.009876
```
MD,
                'starter_code'        => "import math\nmu = float(input())\nt = float(input())\nn = int(input())\nuniforms = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Compute the **effective sample size (ESS)** for importance sampling. ESS = (Σwᵢ)² / Σwᵢ² where wᵢ are unnormalized importance weights. Read `n` importance weights. Print ESS rounded to 4 decimal places.

Example:
```
Input:
4
1.0
1.0
1.0
1.0
Output: 4.0000
```
MD,
                'starter_code'        => "n = int(input())\nweights = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Implement **quasi-Monte Carlo** integration using the Van der Corput sequence in base 2. The n-th term is obtained by reversing the binary digits of n and placing them after the decimal point.

Read `n` (number of terms). Print the first `n` Van der Corput values in base 2 (starting from n=1), rounded to 4 decimal places, one per line.

Example:
```
Input:
6
Output:
0.5000
0.2500
0.7500
0.1250
0.6250
0.3750
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Discrete-Event Simulation & Queueing Theory (Q14–Q18)
            // ═══════════════════════════════════════════════════════════════

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Compute **M/M/1 queue theoretical metrics**. Read arrival rate `lambda` and service rate `mu`. Compute:

rho = lambda / mu  (traffic intensity)
L = rho / (1 - rho)  (mean customers in system)
Lq = rho^2 / (1 - rho)  (mean in queue)
W = 1 / (mu - lambda)  (mean system time)
Wq = lambda / (mu × (mu - lambda))  (mean wait time)

Print each rounded to 4 decimal places. If rho ≥ 1, print `unstable`.

Example:
```
Input:
3
5
Output:
rho: 0.6000
L: 1.5000
Lq: 0.9000
W: 0.5000
Wq: 0.3000
```
MD,
                'starter_code'        => "lambda_ = float(input())\nmu = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Verify **Little's Law** L = λ × W from simulation data. Read `n` customers (format: `arrival departure`). Compute:
- Lambda_hat = n / total_time  (arrival rate, total_time = last departure - first arrival)
- W_hat = mean sojourn time  (departure - arrival per customer)
- L_hat = lambda_hat × W_hat

Print lambda_hat, W_hat, L_hat all rounded to 4 decimal places.

Example:
```
Input:
4
0 3
1 5
3 6
5 8
Output:
lambda: 0.5000
W: 2.7500
L: 1.3750
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Simulate an **M/M/c queue** (c servers). Read `c`, and `n` customers with (arrival, service_time). Process customers: assign to first available server. Print the mean waiting time and mean system time rounded to 4 decimal places.

Example:
```
Input:
2
4
0 3
0 4
1 2
2 1
Output:
avg_wait: 0.2500
avg_system: 2.7500
```
MD,
                'starter_code'        => "c = int(input())\nn = int(input())\ncustomers = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Compute the **Erlang C formula** (probability that a customer must wait) for an M/M/c queue:

P(wait) = C(c, lambda/mu) = (((lambda/mu)^c / c!) × (1/(1-rho))) / (Σ_{k=0}^{c-1} (lambda/mu)^k / k! + (lambda/mu)^c / c! × 1/(1-rho))

where rho = lambda / (c × mu).

Read `lambda`, `mu`, `c`. Print P(wait) rounded to 4 decimal places. If rho ≥ 1, print `unstable`.

Example:
```
Input:
3
2
3
Output: 0.1739
```
MD,
                'starter_code'        => "import math\nlambda_ = float(input())\nmu = float(input())\nc = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Implement a **priority queue simulation** (2 priority classes). Read `n` customers, each with (arrival, service_time, priority) where priority is 1 (high) or 2 (low). High-priority customers are served first regardless of arrival order; within the same priority, FIFO. Print the waiting time for each customer in original order.

Example:
```
Input:
4
0 2 2
0 3 1
2 1 2
3 1 1
Output:
3
0
3
1
```
MD,
                'starter_code'        => "n = int(input())\ncustomers = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: ODE Solvers (Q19–Q23)
            // ═══════════════════════════════════════════════════════════════

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Implement the **Runge-Kutta 4 (RK4)** method for dy/dt = r × y. Read `y0`, `r`, `h`, `n`. Print y after each step rounded to 6 decimal places.

k1 = h × f(t, y)
k2 = h × f(t + h/2, y + k1/2)
k3 = h × f(t + h/2, y + k2/2)
k4 = h × f(t + h, y + k3)
y_new = y + (k1 + 2k2 + 2k3 + k4) / 6

Example:
```
Input:
1.0
1.0
0.5
3
Output:
1.648721
2.718282
4.481689
```
MD,
                'starter_code'        => "y = float(input())\nr = float(input())\nh = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Compare **Euler vs RK4** accuracy for dy/dt = y, y(0) = 1. Read `h` and final time `T`. Print:
```
euler: <value>
rk4: <value>
exact: <value>
euler_error: <value>
rk4_error: <value>
```
All rounded to 6 decimal places. Number of steps = T / h.

Example:
```
Input:
0.5
2.0
Output:
euler: 2.250000
rk4: 7.388281
exact: 7.389056
euler_error: 5.139056
rk4_error: 0.000775
```
MD,
                'starter_code'        => "import math\nh = float(input())\nT = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Solve the **harmonic oscillator** using RK4. System:

dx/dt = v
dv/dt = −omega² × x

Read `x0`, `v0`, `omega`, `h`, `n`. Print x and v after each step rounded to 4 decimal places on one line, space-separated.

Example:
```
Input:
1.0
0.0
1.0
0.1
5
Output:
0.9950 -0.0998
0.9801 -0.1987
0.9554 -0.2958
0.9212 -0.3894
0.8778 -0.4785
```
MD,
                'starter_code'        => "x = float(input())\nv = float(input())\nomega = float(input())\nh = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Implement the **adaptive step-size Euler method**. Use step doubling: take one step of size h and two steps of size h/2; if |difference| > tolerance `tol`, halve h and retry. Read `y0`, `r` (dy/dt = r×y), initial `h`, `tol`, and target time `T`. Print the final y and the number of accepted steps rounded to 4 decimal places.

Example:
```
Input:
1.0
1.0
0.5
0.01
2.0
Output:
y: 7.3891
steps: 5
```
MD,
                'starter_code'        => "import math\ny = float(input())\nr = float(input())\nh = float(input())\ntol = float(input())\nT = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Solve the **Van der Pol oscillator** (mu=1) using RK4:

dx/dt = v
dv/dt = mu × (1 − x²) × v − x   (mu=1)

Read `x0`, `v0`, `h`, `n`. Print x and v after each step rounded to 4 decimal places.

Example:
```
Input:
2.0
0.0
0.1
4
Output:
1.9800 -0.2040
1.9396 -0.4195
1.8795 -0.6400
1.8008 -0.8531
```
MD,
                'starter_code'        => "x = float(input())\nv = float(input())\nh = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: SEIR & Epidemic Models (Q24–Q28)
            // ═══════════════════════════════════════════════════════════════

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Implement the **SEIR model** using Euler's method.

dS/dt = −beta × S × I / N
dE/dt = beta × S × I / N − sigma × E
dI/dt = sigma × E − gamma × I
dR/dt = gamma × I

Read `S0`, `E0`, `I0`, `R0`, `beta`, `sigma`, `gamma`, step `h`, and `n` steps. Print S, E, I, R after each step rounded to 2 decimal places, space-separated.

Example:
```
Input:
990
5
5
0
0.3
0.2
0.1
1
3
Output:
984.25 5.98 6.05 0.50
978.26 6.89 7.07 1.28
972.03 7.70 8.09 2.18
```
MD,
                'starter_code'        => "S = float(input())\nE = float(input())\nI = float(input())\nR = float(input())\nbeta = float(input())\nsigma = float(input())\ngamma = float(input())\nh = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Compute the **effective reproduction number** R_eff(t) at each time step of an SIR simulation:

R_eff(t) = R0 × S(t) / N

Read `S0`, `I0`, `R0_val` (R0), `N`, and `n` steps of S values (one per line after the main params). Print R_eff at each step rounded to 4 decimal places.

Example:
```
Input:
990
10
0
3.0
1000
3
987
984
981
Output:
2.9610
2.9520
2.9430
```
MD,
                'starter_code'        => "S0 = float(input())\nI0 = float(input())\nR_val = float(input())\nR0 = float(input())\nN = float(input())\nn = int(input())\nS_values = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Model **vaccination in the SIR model**. A fraction `v` of susceptibles is vaccinated at time step 0 (moved to R). Then run `n` steps of Euler's method. Read `S0`, `I0`, `R0`, `v`, `beta`, `gamma`, `h`, `n`. Print S, I, R after each step rounded to 2 decimal places.

Example:
```
Input:
1000
10
0
0.5
0.3
0.1
1
3
Output:
494.20 11.58 494.22
488.46 13.19 498.35
482.56 14.91 502.53
```
MD,
                'starter_code'        => "S = float(input())\nI = float(input())\nR = float(input())\nv = float(input())\nbeta = float(input())\ngamma = float(input())\nh = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Compute the **final epidemic size** of an SIR model analytically. The final fraction r∞ satisfies:

r∞ = 1 − exp(−R0 × r∞)

Use fixed-point iteration starting from r∞ = 0.5. Run until |r∞_new − r∞| < 1e-6 (max 10000 iters). Read `R0`. Print r∞ rounded to 4 decimal places.

Example:
```
Input:
3.0
Output: 0.9401
```
MD,
                'starter_code'        => "import math\nR0 = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Simulate the **stochastic SIR model** (discrete, fixed seed). At each step:
- Infections: binomial-like: new_I = round(beta × S × I / N × h), but cap at S
- Recoveries: new_R = round(gamma × I × h), but cap at I

Read `S0`, `I0`, `R0`, `beta`, `gamma`, `h`, `n`. Use these deterministic round-based updates (no actual randomness). Print S, I, R after each step rounded to 0 decimal places (integers).

Example:
```
Input:
990
10
0
0.3
0.1
1
3
Output:
987 12 1
984 14 2
981 16 3
```
MD,
                'starter_code'        => "S = int(input())\nI = int(input())\nR = int(input())\nbeta = float(input())\ngamma = float(input())\nh = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Agent-Based Modeling (Q29–Q33)
            // ═══════════════════════════════════════════════════════════════

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Implement **Schelling's segregation model** (1D). Each cell is 0 (empty), 1 (type A), or 2 (type B). An agent is unhappy if fewer than fraction `t` of its non-empty neighbours (looking left and right, up to k=2) are the same type. In each step, all unhappy agents move to the nearest empty cell to their right (wrapping). Read initial states (space-separated), `t`, and `n` steps. Print final states space-separated.

Example:
```
Input:
1 0 2 1 2 0 1 2
0.5
2
Output: 1 1 2 0 2 1 0 2
```
MD,
                'starter_code'        => "states = list(map(int, input().split()))\nt = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Implement **Boids alignment rule** in 1D. Each boid has a velocity. At each step, each boid's new velocity is the average velocity of all boids within radius `r` (including itself). Read `n` boids with (position, velocity), `r`, and number of steps. Print final velocities rounded to 4 decimal places, one per line.

Example:
```
Input:
4
0 1
1 2
5 3
6 4
2
3
Output:
1.5000
1.5000
3.5000
3.5000
```
MD,
                'starter_code'        => "n = int(input())\nboids = [tuple(map(float, input().split())) for _ in range(n)]\nr = float(input())\nsteps = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Simulate **Conway's Game of Life** for one step on a given grid. Read `rows`, `cols`, then the grid (0 or 1, space-separated). Apply the rules:
- Live cell with 2 or 3 neighbours → stays alive
- Dead cell with exactly 3 neighbours → becomes alive
- All others → die/stay dead

Print the new grid, each row space-separated.

Example:
```
Input:
3 3
0 1 0
1 1 0
0 0 1
Output:
1 1 0
1 1 0
0 1 0
```
MD,
                'starter_code'        => "rows, cols = map(int, input().split())\ngrid = [list(map(int, input().split())) for _ in range(rows)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Simulate a **random walk** in 2D for `n` steps starting at (0, 0). Read `n` steps (each step is a direction: N, S, E, W). Print the final (x, y) position and the Euclidean distance from origin rounded to 4 decimal places.

Example:
```
Input:
5
N
E
E
S
N
Output:
position: 2 1
distance: 2.2361
```
MD,
                'starter_code'        => "import math\nn = int(input())\nsteps = [input().strip() for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Compute the **order parameter** for a 1D flocking simulation. The order parameter Φ = |mean velocity| / mean |velocity| measures alignment (1 = fully aligned, 0 = chaotic). Read `n` velocities. Print Φ rounded to 4 decimal places.

Example:
```
Input:
4
1.0
1.0
-1.0
-1.0
Output: 0.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nvels = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: System Dynamics (Q34–Q38)
            // ═══════════════════════════════════════════════════════════════

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Simulate the **Bass diffusion model**. At each step, new adopters = (p + q × A/N) × (N − A) × h where A = cumulative adopters, N = market size, p = innovation coefficient, q = imitation coefficient. Read `N`, `p`, `q`, `h`, and `n` steps. Print cumulative adopters A after each step rounded to 2 decimal places.

Example:
```
Input:
1000
0.01
0.4
1
5
Output:
14.60
30.89
49.58
70.65
94.12
```
MD,
                'starter_code'        => "N = float(input())\np = float(input())\nq = float(input())\nh = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Model a **first-order information delay**. A pipeline delay of mean time `T` approximated as:

dO/dt = (I − O) / T

where I is input rate, O is output rate. Read `O0` (initial output), a sequence of `n` input values (constant over each step of size `h`), `T`, `h`. Print O after each step rounded to 4 decimal places.

Example:
```
Input:
0.0
4
10
10
10
10
2.0
1.0
Output:
5.0000
7.5000
8.7500
9.3750
```
MD,
                'starter_code'        => "O = float(input())\nn = int(input())\ninputs = [float(input()) for _ in range(n)]\nT = float(input())\nh = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Simulate the **Lotka-Volterra predator-prey** model using RK4 for `n` steps. Read `X0`, `Y0`, `alpha`, `beta`, `delta`, `gamma`, `h`, `n`. Print X and Y after each step rounded to 4 decimal places, space-separated.

Example:
```
Input:
40
9
0.1
0.02
0.01
0.1
1
3
Output:
39.2847 8.6789
38.3952 8.3824
37.3468 8.1099
```
MD,
                'starter_code'        => "X = float(input())\nY = float(input())\nalpha = float(input())\nbeta = float(input())\ndelta = float(input())\ngamma = float(input())\nh = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Compute the **period of oscillation** of the Lotka-Volterra system. Run RK4 for up to `max_steps` steps and detect the first time X returns close (within `tol`) to its initial value X0 (from above, i.e., X decreases then increases back). Read `X0`, `Y0`, `alpha`, `beta`, `delta`, `gamma`, `h`, `max_steps`, `tol`. Print the period (step count × h) rounded to 2 decimal places, or `not found` if no period detected.

Example:
```
Input:
40
9
0.1
0.02
0.01
0.1
0.1
2000
0.5
Output: 62.80
```
MD,
                'starter_code'        => "X = float(input())\nY = float(input())\nalpha = float(input())\nbeta = float(input())\ndelta = float(input())\ngamma = float(input())\nh = float(input())\nmax_steps = int(input())\ntol = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Compute the **equilibrium point** of a system dynamics model. Given dx/dt = a − b×x (linear), the equilibrium is x* = a/b. Also compute the **time constant** τ = 1/b. Read `a`, `b`. Print x* and τ rounded to 4 decimal places.

Example:
```
Input:
10
2
Output:
equilibrium: 5.0000
time_constant: 0.5000
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Sensitivity Analysis (Q39–Q43)
            // ═══════════════════════════════════════════════════════════════

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Compute **partial rank correlation coefficients (PRCC)** between each input parameter and the output. Read `n` parameter sets, each with `k` parameters and one output (last value on line). Rank each column (1-based, ties get average rank). Compute Pearson correlation of each parameter rank with output rank. Print PRCC for each parameter rounded to 4 decimal places, one per line.

Example:
```
Input:
4 2
10 1 5
20 2 8
30 3 11
40 4 14
Output:
1.0000
1.0000
```
MD,
                'starter_code'        => "import math\nfirst_line = input().split()\nn, k = int(first_line[0]), int(first_line[1])\ndata = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Build a **tornado diagram** (sensitivity ranking). Read `n` parameters, each with a name, base output, low output, and high output. Rank parameters by the total swing (high − low) in descending order. Print each parameter and its swing rounded to 2 decimal places. Format: `name: swing`

Example:
```
Input:
3
beta 10 6 16
gamma 10 9 12
N 10 8 14
Output:
beta: 10.00
N: 6.00
gamma: 3.00
```
MD,
                'starter_code'        => "n = int(input())\nparams = []\nfor _ in range(n):\n    parts = input().split()\n    params.append((parts[0], float(parts[1]), float(parts[2]), float(parts[3])))\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Compute the **first-order Sobol index** S1 for a single input. Using the Saltelli estimator with matrices A, B (sample matrices), and AB (B with column i replaced by A's column i):

S1_i ≈ (1/N) × Σ f(B) × (f(AB_i) − f(A)) / Var(Y)

For simplicity: read `n` triplets `(fA, fB, fAB)` and compute S1 = mean(fB × (fAB − fA)) / (Var of all fA values). Print S1 rounded to 4 decimal places.

Example:
```
Input:
4
1 2 1
4 3 4
9 6 9
16 8 16
Output: 0.9880
```
MD,
                'starter_code'        => "n = int(input())\ntriplets = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Perform a **one-at-a-time (OAT) sensitivity analysis** for the SIR final size model. The final size r∞ satisfies r∞ = 1 − exp(−R0 × r∞). Read base `beta`, `gamma`, and perturbation fraction `delta`. Compute final size for (beta, gamma), (beta×(1+delta), gamma), and (beta, gamma×(1+delta)). Print each final size rounded to 4 decimal places.

Use fixed-point iteration starting from 0.5 with tolerance 1e-6.

Example:
```
Input:
0.3
0.1
0.1
Output:
base: 0.9401
beta_up: 0.9551
gamma_up: 0.9213
```
MD,
                'starter_code'        => "import math\nbeta = float(input())\ngamma = float(input())\ndelta = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Compute the **elasticity** of a model output with respect to a parameter. Elasticity = (ΔY/Y) / (ΔP/P) ≈ (dY/dP) × (P/Y). Read base parameter `P`, base output `Y`, and the output at P × (1 + 0.01) (a 1% increase). Compute and print elasticity rounded to 4 decimal places.

Example:
```
Input:
3.0
0.9401
0.9551
Output: 4.7832
```
MD,
                'starter_code'        => "P = float(input())\nY_base = float(input())\nY_up = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Advanced Output Analysis (Q44–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Compute the **autocorrelation function** of a time series up to lag `L`. Read `n` observations and `L`. Print ACF(k) for k = 1 to L rounded to 4 decimal places.

ACF(k) = Σ(xₜ − x̄)(xₜ₋ₖ − x̄) / Σ(xₜ − x̄)²  for t = k+1..n

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
0.4000
-0.1333
-0.6000
```
MD,
                'starter_code'        => "n = int(input())\nobs = [float(input()) for _ in range(n)]\nL = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Implement the **regenerative simulation** mean estimator. A regenerative cycle is defined by a trigger value (the first time the series goes above `threshold`). Read `n` observations, `threshold`, and number of expected cycles to use. Print the regenerative mean estimate rounded to 4 decimal places.

Regenerative mean = Σ(sum per cycle) / Σ(length per cycle)

Example:
```
Input:
10
3
5
10
2
1
4
8
3
2
5
1
3
3
Output: 3.9000
```
MD,
                'starter_code'        => "n = int(input())\nobs = [float(input()) for _ in range(n)]\nthreshold = float(input())\nmax_cycles = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Implement the **spectral variance estimator** (Bartlett's method). Split the series into `b` non-overlapping batches, compute each batch mean, and estimate variance as var(batch_means) / b × n (where n is total length). Read `n` observations and `b` batches. Print the spectral variance estimate rounded to 4 decimal places.

Example:
```
Input:
6
10
12
11
20
22
21
3
Output: 0.3333
```
MD,
                'starter_code'        => "n = int(input())\nobs = [float(input()) for _ in range(n)]\nb = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Compute the **standardised time series** variance estimator. For a time series of length `n`, the STS estimator is:

σ² = (π² / (6 × n)) × Σₜ (Y_t)²

where Y_t = (1/√n) × Σₛ₌₁ᵗ (xₛ − x̄) (partial sums of mean-adjusted series).

Read `n` observations. Print the STS variance estimate rounded to 4 decimal places.

Example:
```
Input:
4
10
12
11
13
Output: 1.6449
```
MD,
                'starter_code'        => "import math\nn = int(input())\nobs = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Implement the **common random numbers (CRN)** variance reduction. Two systems A and B are run with the same random inputs. Read `n` paired (A_i, B_i) observations. Compute:
- mean of A, mean of B
- variance of differences D = A − B
- 95% CI for mean difference: mean_D ± 1.96 × sqrt(var_D/n)

Print mean_D, ci_lower, ci_upper rounded to 4 decimal places.

Example:
```
Input:
4
10 9
12 10
11 10
13 11
Output:
mean_diff: 1.5000
ci_lower: 0.5377
ci_upper: 2.4623
```
MD,
                'starter_code'        => "import math\nn = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Compute the **Akaike Information Criterion (AIC)** to compare simulation models. AIC = 2k − 2 × ln(L) where k = number of parameters, L = likelihood. Use L = exp(−n × MSE / 2) as a Gaussian likelihood approximation. Read `n` observation pairs (obs, sim), and `k`. Print AIC rounded to 4 decimal places.

Example:
```
Input:
4
10 10
20 20
15 15
30 30
2
Output: 4.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Perform a full **simulation comparison** pipeline. Read two sets of `n` simulation results each (system A then system B). Compute:
1. Mean and 95% CI for each system
2. Paired t-statistic and whether difference is significant (|t| > 2.0)
3. Cohen's d effect size

Print all results rounded to 4 decimal places.

Format:
```
A_mean: X  A_ci: [L, U]
B_mean: X  B_ci: [L, U]
t: X  significant: True/False
cohens_d: X
```

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
A_mean: 11.5000  A_ci: [10.0386, 12.9614]
B_mean: 21.5000  B_ci: [20.0386, 22.9614]
t: -10.0000  significant: True
cohens_d: -5.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nA = [float(input()) for _ in range(n)]\nm = int(input())\nB = [float(input()) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 500,
            ],

        ]; // end $questionDefs

        // ─────────────────────────────────────────────────────────────────
        // 3. PERSIST QUESTIONS
        // ─────────────────────────────────────────────────────────────────

        $questions = [];

        foreach ($questionDefs as $def) {
            $q = DB::table('coding_questions')->where([
                'challenge_id' => $challenge->id,
                'order_index'  => $def['order_index'],
            ])->first();

            if (! $q) {
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
                $questions[$def['order_index']] = $id;
            } else {
                $questions[$def['order_index']] = $q->id;
            }
        }

        // ─────────────────────────────────────────────────────────────────
        // 4. TEST CASES
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $seed = function (int $qIdx, array $cases) use ($questions): void {
            $qId = $questions[$qIdx] ?? null;
            if (! $qId) return;
            foreach ($cases as $case) {
                $exists = DB::table('test_cases')->where([
                    'coding_question_id' => $qId,
                    'order_index'        => $case['order_index'],
                ])->exists();
                if (! $exists) {
                    DB::table('test_cases')->insert([
                        'coding_question_id' => $qId,
                        'input'              => $case['input'],
                        'expected_output'    => $case['expected_output'],
                        'is_hidden'          => $case['is_hidden'],
                        'order_index'        => $case['order_index'],
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
            }
        };

        // ── Q1: Dimensional analysis ───────────────────────────────────────
        $seed(1, [
            ['input' => "3\nv 0 1 -1\nt 0 0 1\nd 0 1 0\nv t",     'expected_output' => 'M^0 L^1 T^0',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nm 1 0 0\na 0 1 -2\nm a",               'expected_output' => 'M^1 L^1 T^-2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nF 1 1 -2\nd 0 1 0\nt 0 0 1\nF d t",   'expected_output' => 'M^1 L^2 T^-1', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nE 1 2 -2\nE",                           'expected_output' => 'M^1 L^2 T^-2', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Inverse transform exponential ────────────────────────────
        $seed(2, [
            ['input' => "2\n4\n0.1\n0.5\n0.9\n0.99",    'expected_output' => "0.0527\n0.3466\n1.1513\n2.3026",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2\n0.0\n0.5\n1.0\n0.0",     'expected_output' => "0.0000\n0.6931\ninf\n0.0000",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n3\n0.2\n0.6\n0.8",        'expected_output' => "0.4463\n1.8326\n3.2189",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n2\n0.1\n0.9",              'expected_output' => "0.0105\n0.2303",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Acceptance-rejection ──────────────────────────────────────
        $seed(3, [
            ['input' => "3\n0.5 0.9\n0.5 0.6\n0.2 0.3",   'expected_output' => "accept\nreject\naccept",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0 0.01\n1.0 0.01",          'expected_output' => "accept\naccept",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.5 0.01\n0.5 0.67\n0.5 0.68",'expected_output' => "accept\naccept\nreject",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0.3 0.5\n0.7 0.5",            'expected_output' => "accept\naccept",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Empirical type I error ────────────────────────────────────
        $seed(4, [
            ['input' => "5\n0.03\n0.12\n0.04\n0.55\n0.01\n0.05",    'expected_output' => '0.6000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0.10\n0.20\n0.30\n0.40\n0.05",          'expected_output' => '0.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0.01\n0.02\n0.03\n0.04\n0.05\n0.05",    'expected_output' => '1.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0.05\n0.05\n0.05\n0.05\n0.05",          'expected_output' => '0.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Antithetic variates ───────────────────────────────────────
        $seed(5, [
            ['input' => "4\n0.1\n0.4\n0.7\n0.9",    'expected_output' => "standard_mean: 0.4425\nantithetic_mean: 0.3350\nvar_reduction: 1.8478",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0\n1.0",              'expected_output' => "standard_mean: 0.5000\nantithetic_mean: 0.5000\nvar_reduction: nan",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0.2\n0.4\n0.6\n0.8",    'expected_output' => "standard_mean: 0.3600\nantithetic_mean: 0.3400\nvar_reduction: 6.3500",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0.5",                    'expected_output' => "standard_mean: 0.2500\nantithetic_mean: 0.2500\nvar_reduction: nan",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Stratified sampling ───────────────────────────────────────
        $seed(6, [
            ['input' => "6\n0.05\n0.15\n0.35\n0.45\n0.65\n0.85\n3",   'expected_output' => '0.3358',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0.1\n0.3\n0.6\n0.9\n2",                   'expected_output' => '0.3350',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.0\n0.5\n1.0\n1",                        'expected_output' => '0.4167',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0.25\n0.75\n2",                            'expected_output' => '0.3125',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Control variate ───────────────────────────────────────────
        $seed(7, [
            ['input' => "4\n0.1\n0.4\n0.7\n0.9",    'expected_output' => "c_star: -1.0667\nestimate: 0.3413",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.0\n0.5\n1.0",         'expected_output' => "c_star: -1.0000\nestimate: 0.3333",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0.25\n0.75",             'expected_output' => "c_star: -1.0000\nestimate: 0.3125",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0.2\n0.4\n0.6\n0.8",    'expected_output' => "c_star: -1.0000\nestimate: 0.3400",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Box-Muller ────────────────────────────────────────────────
        $seed(8, [
            ['input' => "2\n0.1 0.2\n0.5 0.7",   'expected_output' => "1.8216 -0.8429\n0.8255 1.0716",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0.5 0.5",            'expected_output' => "1.1774 0.0000",                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0.1 0.5",            'expected_output' => "2.1460 0.0000",                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0.3 0.4\n0.7 0.8",   'expected_output' => "0.8084 0.8084\n0.5344 0.5344",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: LCG ──────────────────────────────────────────────────────
        $seed(9, [
            ['input' => "1\n5\n3\n16\n5",     'expected_output' => "0.0625\n0.3750\n0.0000\n0.1875\n0.0000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n3\n1\n8\n4",      'expected_output' => "0.1250\n0.5000\n0.8750\n0.2500",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n7\n7\n100\n3",    'expected_output' => "0.0600\n0.4900\n0.5000",                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n2\n0\n32\n4",     'expected_output' => "0.0625\n0.1250\n0.2500\n0.5000",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: MC CI for E[X^2] ─────────────────────────────────────────
        $seed(10, [
            ['input' => "1\n5\n0.1\n0.5\n0.9\n0.3\n0.7\n0.95",    'expected_output' => "mean: 1.9733\nci_lower: -0.8461\nci_upper: 4.7927",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2\n0.5\n0.5\n0.95",                    'expected_output' => "mean: 0.1201\nci_lower: 0.1201\nci_upper: 0.1201",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n3\n0.2\n0.6\n0.8\n0.90",            'expected_output' => "mean: 10.3083\nci_lower: -8.8568\nci_upper: 29.4733",'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n4\n0.1\n0.2\n0.3\n0.4\n0.95",         'expected_output' => "mean: 0.1096\nci_lower: 0.0424\nci_upper: 0.1769",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Importance sampling rare event ───────────────────────────
        $seed(11, [
            ['input' => "0.5\n5\n4\n0.1\n0.5\n0.8\n0.95",   'expected_output' => '0.009876',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n3\n3\n2\n0.1\n0.5\n0.9",      'expected_output' => '0.102685',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.1\n10\n3\n0.1\n0.5\n0.9",        'expected_output' => '0.081393',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n5\n2\n0.5\n0.9",              'expected_output' => '0.074927',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: ESS ──────────────────────────────────────────────────────
        $seed(12, [
            ['input' => "4\n1.0\n1.0\n1.0\n1.0",      'expected_output' => '4.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1.0\n0.0\n0.0\n0.0",      'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2.0\n1.0\n0.5",           'expected_output' => '2.4706',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n3.0\n1.0",                'expected_output' => '1.6000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Van der Corput ───────────────────────────────────────────
        $seed(13, [
            ['input' => '6',   'expected_output' => "0.5000\n0.2500\n0.7500\n0.1250\n0.6250\n0.3750",   'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',   'expected_output' => "0.5000\n0.2500\n0.7500\n0.1250",                   'is_hidden' => false, 'order_index' => 2],
            ['input' => '8',   'expected_output' => "0.5000\n0.2500\n0.7500\n0.1250\n0.6250\n0.3750\n0.8750\n0.0625", 'is_hidden' => true, 'order_index' => 3],
            ['input' => '1',   'expected_output' => "0.5000",                                            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: M/M/1 theory ─────────────────────────────────────────────
        $seed(14, [
            ['input' => "3\n5",    'expected_output' => "rho: 0.6000\nL: 1.5000\nLq: 0.9000\nW: 0.5000\nWq: 0.3000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2",    'expected_output' => "rho: 0.5000\nL: 1.0000\nLq: 0.5000\nW: 2.0000\nWq: 1.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5",    'expected_output' => "unstable",                                                       'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10",   'expected_output' => "rho: 0.2000\nL: 0.2500\nLq: 0.0500\nW: 0.1250\nWq: 0.0250",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Little's law ─────────────────────────────────────────────
        $seed(15, [
            ['input' => "4\n0 3\n1 5\n3 6\n5 8",    'expected_output' => "lambda: 0.5000\nW: 2.7500\nL: 1.3750",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 5\n5 10",             'expected_output' => "lambda: 0.2000\nW: 5.0000\nL: 1.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0 2\n1 4\n3 5",         'expected_output' => "lambda: 0.6000\nW: 2.3333\nL: 1.4000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0 10",                  'expected_output' => "lambda: 0.1000\nW: 10.0000\nL: 1.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: M/M/c simulation ──────────────────────────────────────────
        $seed(16, [
            ['input' => "2\n4\n0 3\n0 4\n1 2\n2 1",    'expected_output' => "avg_wait: 0.2500\navg_system: 2.7500",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n3\n0 3\n2 2\n4 1",         'expected_output' => "avg_wait: 0.3333\navg_system: 2.3333",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n3\n0 2\n0 3\n0 4",         'expected_output' => "avg_wait: 0.0000\navg_system: 3.0000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n2\n0 5\n3 2",              'expected_output' => "avg_wait: 1.0000\navg_system: 4.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Erlang C ─────────────────────────────────────────────────
        $seed(17, [
            ['input' => "3\n2\n3",    'expected_output' => '0.1739',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2\n1",    'expected_output' => '0.5000',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2\n1",    'expected_output' => 'unstable', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n4\n2",    'expected_output' => '0.1818',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Priority queue ───────────────────────────────────────────
        $seed(18, [
            ['input' => "4\n0 2 2\n0 3 1\n2 1 2\n3 1 1",    'expected_output' => "3\n0\n3\n1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0 5 1\n0 3 2\n4 2 1",           'expected_output' => "0\n5\n3",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0 2 1\n0 2 1\n0 2 1",           'expected_output' => "0\n2\n4",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0 3 2\n0 2 1",                   'expected_output' => "2\n0",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: RK4 exponential ──────────────────────────────────────────
        $seed(19, [
            ['input' => "1.0\n1.0\n0.5\n3",    'expected_output' => "1.648721\n2.718282\n4.481689",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n0.5\n1.0\n3",    'expected_output' => "3.297443\n5.436564\n8.963379",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n0.0\n1.0\n3",    'expected_output' => "1.000000\n1.000000\n1.000000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n-1.0\n0.5\n4",   'expected_output' => "0.606531\n0.367879\n0.223130\n0.135335",  'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q20: Euler vs RK4 ──────────────────────────────────────────────
        $seed(20, [
            ['input' => "0.5\n2.0",   'expected_output' => "euler: 2.250000\nrk4: 7.388281\nexact: 7.389056\neuler_error: 5.139056\nrk4_error: 0.000775",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n1.0",   'expected_output' => "euler: 2.000000\nrk4: 2.708333\nexact: 2.718282\neuler_error: 0.718282\nrk4_error: 0.009948",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.1\n1.0",   'expected_output' => "euler: 2.593742\nrk4: 2.718282\nexact: 2.718282\neuler_error: 0.124540\nrk4_error: 0.000000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n1.0",   'expected_output' => "euler: 1.500000\nrk4: 2.717773\nexact: 2.718282\neuler_error: 1.218282\nrk4_error: 0.000509",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Harmonic oscillator ──────────────────────────────────────
        $seed(21, [
            ['input' => "1.0\n0.0\n1.0\n0.1\n5",    'expected_output' => "0.9950 -0.0998\n0.9801 -0.1987\n0.9554 -0.2958\n0.9212 -0.3894\n0.8778 -0.4785",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0\n1.0\n0.1\n3",    'expected_output' => "0.0998 0.9950\n0.1983 0.9800\n0.2955 0.9553",                                      'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n0.0\n2.0\n0.1\n3",    'expected_output' => "0.9800 -0.1987\n0.9212 -0.3895\n0.8270 -0.5659",                                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n0.0\n1.0\n0.1\n2",    'expected_output' => "0.0000 0.0000\n0.0000 0.0000",                                                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Adaptive Euler ───────────────────────────────────────────
        $seed(22, [
            ['input' => "1.0\n1.0\n0.5\n0.01\n2.0",    'expected_output' => "y: 7.3891\nsteps: 5",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n0.0\n1.0\n0.001\n1.0",   'expected_output' => "y: 1.0000\nsteps: 1",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n0.5\n0.5\n0.01\n2.0",    'expected_output' => "y: 5.4366\nsteps: 5",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n1.0\n0.1\n0.001\n1.0",   'expected_output' => "y: 2.7183\nsteps: 10",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Van der Pol ──────────────────────────────────────────────
        $seed(23, [
            ['input' => "2.0\n0.0\n0.1\n4",   'expected_output' => "1.9800 -0.2040\n1.9396 -0.4195\n1.8795 -0.6400\n1.8008 -0.8531",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n0.0\n0.1\n3",   'expected_output' => "0.0000 0.0000\n0.0000 0.0000\n0.0000 0.0000",                       'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n0.0\n0.1\n3",   'expected_output' => "0.9900 -0.1000\n0.9802 -0.1980\n0.9607 -0.2921",                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.0\n1.0\n0.1\n2",   'expected_output' => "2.0890 0.7620\n2.1586 0.5019",                                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: SEIR model ───────────────────────────────────────────────
        $seed(24, [
            ['input' => "990\n5\n5\n0\n0.3\n0.2\n0.1\n1\n3",    'expected_output' => "984.25 5.98 6.05 0.50\n978.26 6.89 7.07 1.28\n972.03 7.70 8.09 2.18",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1000\n0\n0\n0\n0.3\n0.2\n0.1\n1\n2",   'expected_output' => "1000.00 0.00 0.00 0.00\n1000.00 0.00 0.00 0.00",                        'is_hidden' => false, 'order_index' => 2],
            ['input' => "950\n30\n20\n0\n0.5\n0.25\n0.1\n1\n2", 'expected_output' => "940.05 31.75 23.50 4.70\n929.86 33.27 26.75 10.12",                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "999\n0\n1\n0\n0.3\n0.2\n0.1\n1\n1",    'expected_output' => "998.70 0.20 1.10 0.10",                                                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: R_eff ────────────────────────────────────────────────────
        $seed(25, [
            ['input' => "990\n10\n0\n3.0\n1000\n3\n987\n984\n981",    'expected_output' => "2.9610\n2.9520\n2.9430",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1000\n0\n0\n2.5\n1000\n2\n900\n800",         'expected_output' => "2.2500\n2.0000",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "500\n0\n0\n4.0\n1000\n3\n400\n300\n200",     'expected_output' => "1.6000\n1.2000\n0.8000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1000\n0\n0\n1.0\n1000\n1\n1000",             'expected_output' => "1.0000",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: SIR with vaccination ─────────────────────────────────────
        $seed(26, [
            ['input' => "1000\n10\n0\n0.5\n0.3\n0.1\n1\n3",    'expected_output' => "494.20 11.58 494.22\n488.46 13.19 498.35\n482.56 14.91 502.53",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1000\n0\n0\n0.5\n0.3\n0.1\n1\n2",     'expected_output' => "500.00 0.00 500.00\n500.00 0.00 500.00",                          'is_hidden' => false, 'order_index' => 2],
            ['input' => "500\n10\n0\n0.0\n0.3\n0.1\n1\n2",     'expected_output' => "498.50 10.90 0.60\n496.97 11.84 1.19",                            'is_hidden' => true,  'order_index' => 3],
            ['input' => "1000\n10\n0\n1.0\n0.3\n0.1\n1\n1",    'expected_output' => "0.00 9.97 990.03",                                                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Final epidemic size ──────────────────────────────────────
        $seed(27, [
            ['input' => '3.0',   'expected_output' => '0.9401',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '1.5',   'expected_output' => '0.5831',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.5',   'expected_output' => '0.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '5.0',   'expected_output' => '0.9933',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Stochastic SIR ───────────────────────────────────────────
        $seed(28, [
            ['input' => "990\n10\n0\n0.3\n0.1\n1\n3",     'expected_output' => "987 12 1\n984 14 2\n981 16 3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1000\n0\n0\n0.3\n0.1\n1\n2",     'expected_output' => "1000 0 0\n1000 0 0",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "500\n100\n0\n0.5\n0.2\n1\n2",    'expected_output' => "475 95 30\n452 90 58",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "999\n1\n0\n0.3\n0.1\n1\n1",      'expected_output' => "999 1 0",                         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Schelling 1D ─────────────────────────────────────────────
        $seed(29, [
            ['input' => "1 0 2 1 2 0 1 2\n0.5\n2",    'expected_output' => '1 1 2 0 2 1 0 2',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1 1 1\n0.5\n1",            'expected_output' => '1 1 1 1',            'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 0 1 2\n0.5\n1",          'expected_output' => '1 2 1 0 2',          'is_hidden' => true,  'order_index' => 3],
            ['input' => "0 0 0 0\n0.5\n3",            'expected_output' => '0 0 0 0',            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Boids alignment ──────────────────────────────────────────
        $seed(30, [
            ['input' => "4\n0 1\n1 2\n5 3\n6 4\n2\n3",     'expected_output' => "1.5000\n1.5000\n3.5000\n3.5000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 1\n10 -1\n0.5\n2",           'expected_output' => "1.0000\n-1.0000",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0 1\n1 1\n2 1\n1.5\n2",        'expected_output' => "1.0000\n1.0000\n1.0000",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0 3\n1 5\n2\n1",               'expected_output' => "4.0000\n4.0000",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Game of Life ─────────────────────────────────────────────
        $seed(31, [
            ['input' => "3 3\n0 1 0\n1 1 0\n0 0 1",     'expected_output' => "1 1 0\n1 1 0\n0 1 0",            'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 3\n0 0 0\n0 0 0\n0 0 0",     'expected_output' => "0 0 0\n0 0 0\n0 0 0",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 3\n1 1 1\n1 1 1\n1 1 1",     'expected_output' => "1 0 1\n0 0 0\n1 0 1",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 4\n0 1 1 0\n0 1 1 0\n0 0 0 0\n0 0 0 0", 'expected_output' => "0 1 1 0\n0 1 1 0\n0 0 0 0\n0 0 0 0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q32: Random walk 2D ───────────────────────────────────────────
        $seed(32, [
            ['input' => "5\nN\nE\nE\nS\nN",       'expected_output' => "position: 2 1\ndistance: 2.2361",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nN\nS\nE\nW",          'expected_output' => "position: 0 0\ndistance: 0.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nN\nN\nN",             'expected_output' => "position: 0 3\ndistance: 3.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nE\nE\nN\nN",          'expected_output' => "position: 2 2\ndistance: 2.8284",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Order parameter ──────────────────────────────────────────
        $seed(33, [
            ['input' => "4\n1.0\n1.0\n-1.0\n-1.0",   'expected_output' => '0.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2.0\n2.0\n2.0",           'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1.0\n2.0\n3.0\n4.0",     'expected_output' => '1.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1.0\n-1.0\n2.0\n-2.0",   'expected_output' => '0.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Bass diffusion ───────────────────────────────────────────
        $seed(34, [
            ['input' => "1000\n0.01\n0.4\n1\n5",    'expected_output' => "14.60\n30.89\n49.58\n70.65\n94.12",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1000\n0.01\n0.0\n1\n3",    'expected_output' => "10.00\n19.90\n29.70",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "500\n0.02\n0.3\n1\n3",     'expected_output' => "16.00\n32.86\n50.44",                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1000\n0.0\n0.5\n1\n3",     'expected_output' => "0.00\n0.00\n0.00",                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: First-order delay ────────────────────────────────────────
        $seed(35, [
            ['input' => "0.0\n4\n10\n10\n10\n10\n2.0\n1.0",      'expected_output' => "5.0000\n7.5000\n8.7500\n9.3750",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "10.0\n3\n10\n10\n10\n2.0\n1.0",         'expected_output' => "10.0000\n10.0000\n10.0000",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n4\n0\n0\n0\n0\n1.0\n1.0",          'expected_output' => "0.0000\n0.0000\n0.0000\n0.0000",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n3\n20\n20\n20\n4.0\n2.0",          'expected_output' => "10.0000\n15.0000\n17.5000",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Lotka-Volterra RK4 ───────────────────────────────────────
        $seed(36, [
            ['input' => "40\n9\n0.1\n0.02\n0.01\n0.1\n1\n3",   'expected_output' => "39.2847 8.6789\n38.3952 8.3824\n37.3468 8.1099",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n5\n0.5\n0.1\n0.1\n0.5\n0.1\n3",  'expected_output' => "9.7750 4.8050\n9.5601 4.6237\n9.3552 4.4556",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n0\n0.1\n0.02\n0.01\n0.1\n1\n2",  'expected_output' => "110.0000 0.0000\n121.0000 0.0000",                'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n0\n0.1\n0.02\n0.01\n0.1\n1\n2",    'expected_output' => "0.0000 0.0000\n0.0000 0.0000",                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: LV period ────────────────────────────────────────────────
        $seed(37, [
            ['input' => "40\n9\n0.1\n0.02\n0.01\n0.1\n0.1\n2000\n0.5",   'expected_output' => '62.80',      'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n0\n0.1\n0.02\n0.01\n0.1\n0.1\n100\n0.1",     'expected_output' => 'not found',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "10\n5\n0.5\n0.1\n0.1\n0.5\n0.1\n2000\n0.5",     'expected_output' => '12.50',      'is_hidden' => true,  'order_index' => 3],
            ['input' => "100\n0\n0.1\n0.02\n0.01\n0.1\n0.1\n100\n0.5",   'expected_output' => 'not found',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Equilibrium ─────────────────────────────────────────────
        $seed(38, [
            ['input' => "10\n2",    'expected_output' => "equilibrium: 5.0000\ntime_constant: 0.5000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n3",     'expected_output' => "equilibrium: 2.0000\ntime_constant: 0.3333",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n5",     'expected_output' => "equilibrium: 0.0000\ntime_constant: 0.2000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "100\n10",  'expected_output' => "equilibrium: 10.0000\ntime_constant: 0.1000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: PRCC ─────────────────────────────────────────────────────
        $seed(39, [
            ['input' => "4 2\n10 1 5\n20 2 8\n30 3 11\n40 4 14",    'expected_output' => "1.0000\n1.0000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 3 5\n2 2 4\n3 1 3",                  'expected_output' => "1.0000\n-1.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 2\n1 1 5\n2 2 5\n3 3 5\n4 4 5",          'expected_output' => "1.0000\n0.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 1\n1 10\n2 5\n3 1",                       'expected_output' => "-1.0000",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Tornado diagram ──────────────────────────────────────────
        $seed(40, [
            ['input' => "3\nbeta 10 6 16\ngamma 10 9 12\nN 10 8 14",    'expected_output' => "beta: 10.00\nN: 6.00\ngamma: 3.00",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\na 5 3 9\nb 5 4 7",                          'expected_output' => "a: 6.00\nb: 3.00",                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nx 1 0 2\ny 1 0.5 2\nz 1 0 3",              'expected_output' => "z: 3.00\nx: 2.00\ny: 1.50",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nonly 10 5 15",                              'expected_output' => "only: 10.00",                        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Sobol S1 ─────────────────────────────────────────────────
        $seed(41, [
            ['input' => "4\n1 2 1\n4 3 4\n9 6 9\n16 8 16",   'expected_output' => '0.9880',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1 1\n4 4 4\n9 9 9",            'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 1 0\n0 1 0\n0 1 0\n0 1 0",     'expected_output' => '0.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 4 4\n9 0 0",                   'expected_output' => '-1.9600', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: OAT sensitivity for SIR ─────────────────────────────────
        $seed(42, [
            ['input' => "0.3\n0.1\n0.1",    'expected_output' => "base: 0.9401\nbeta_up: 0.9551\ngamma_up: 0.9213",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.1\n0.2\n0.1",    'expected_output' => "base: 0.0000\nbeta_up: 0.0000\ngamma_up: 0.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n0.1\n0.2",    'expected_output' => "base: 0.9933\nbeta_up: 0.9951\ngamma_up: 0.9907",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.2\n0.1\n0.1",    'expected_output' => "base: 0.7968\nbeta_up: 0.8234\ngamma_up: 0.7623",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Elasticity ───────────────────────────────────────────────
        $seed(43, [
            ['input' => "3.0\n0.9401\n0.9551",   'expected_output' => '4.7832',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n1.0\n2.0",         'expected_output' => '100.0000','is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n4.0\n4.08",        'expected_output' => '0.4000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "10.0\n100.0\n101.0",    'expected_output' => '0.1000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: ACF ─────────────────────────────────────────────────────
        $seed(44, [
            ['input' => "6\n1\n2\n3\n4\n5\n6\n3",    'expected_output' => "0.4000\n-0.1333\n-0.6000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n1\n1\n1\n2",          'expected_output' => "0.0000\n0.0000",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n2\n3\n4\n5\n1",       'expected_output' => "0.4000",                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1\n-1\n1\n-1\n1\n-1\n2", 'expected_output' => "-1.0000\n1.0000",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Regenerative mean ────────────────────────────────────────
        $seed(45, [
            ['input' => "10\n3\n5\n10\n2\n1\n4\n8\n3\n2\n5\n1\n3\n3",   'expected_output' => '3.9000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n1\n2\n3\n4\n5\n6\n3\n2",                     'expected_output' => '3.5000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "8\n10\n1\n2\n3\n10\n1\n2\n3\n5\n2",             'expected_output' => '4.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n5\n1\n5\n1\n3\n2",                           'expected_output' => '3.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Spectral variance ────────────────────────────────────────
        $seed(46, [
            ['input' => "6\n10\n12\n11\n20\n22\n21\n3",   'expected_output' => '0.3333',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n5\n5\n5\n5\n5\n5\n3",         'expected_output' => '0.0000',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4\n2",               'expected_output' => '0.2500',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n10\n10\n20\n20\n30\n30\n2",   'expected_output' => '1.6667',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: STS variance ────────────────────────────────────────────
        $seed(47, [
            ['input' => "4\n10\n12\n11\n13",    'expected_output' => '1.6449',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n5\n5\n5\n5",        'expected_output' => '0.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3",           'expected_output' => '1.6449',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n10\n10\n10\n10\n10",'expected_output' => '0.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: CRN ─────────────────────────────────────────────────────
        $seed(48, [
            ['input' => "4\n10 9\n12 10\n11 10\n13 11",     'expected_output' => "mean_diff: 1.5000\nci_lower: 0.5377\nci_upper: 2.4623",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5 5\n10 10\n15 15",             'expected_output' => "mean_diff: 0.0000\nci_lower: 0.0000\nci_upper: 0.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n20 10\n20 10\n20 10\n20 10",    'expected_output' => "mean_diff: 10.0000\nci_lower: 10.0000\nci_upper: 10.0000",'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 0\n3 2",                      'expected_output' => "mean_diff: 1.0000\nci_lower: -5.8242\nci_upper: 7.8242",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: AIC ─────────────────────────────────────────────────────
        $seed(49, [
            ['input' => "4\n10 10\n20 20\n15 15\n30 30\n2",   'expected_output' => '4.0000',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2\n2 3\n3 4\n1",               'expected_output' => '3.7726',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n5 5\n5 5\n5 5\n5 5\n3",          'expected_output' => '6.0000',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0 5\n10 5\n2",                   'expected_output' => '11.8413',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Full comparison ──────────────────────────────────────────
        $seed(50, [
            ['input' => "4\n10\n12\n11\n13\n4\n20\n22\n21\n23",    'expected_output' => "A_mean: 11.5000  A_ci: [10.0386, 12.9614]\nB_mean: 21.5000  B_ci: [20.0386, 22.9614]\nt: -10.0000  significant: True\ncohens_d: -5.0000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5\n3\n5\n5\n5",                  'expected_output' => "A_mean: 5.0000  A_ci: [5.0000, 5.0000]\nB_mean: 5.0000  B_ci: [5.0000, 5.0000]\nt: 0.0000  significant: False\ncohens_d: 0.0000",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4\n4\n5\n6\n7\n8",            'expected_output' => "A_mean: 2.5000  A_ci: [1.0386, 3.9614]\nB_mean: 6.5000  B_ci: [5.0386, 7.9614]\nt: -4.0000  significant: True\ncohens_d: -2.8284",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10\n20\n2\n11\n21",                    'expected_output' => "A_mean: 15.0000  A_ci: [-38.5550, 68.5550]\nB_mean: 16.0000  B_ci: [-37.5550, 69.5550]\nt: -0.1414  significant: False\ncohens_d: -0.1000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 6 Coding (University Student) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}