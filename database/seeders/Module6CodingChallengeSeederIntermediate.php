<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 6 — Modeling & Simulation (Intermediate) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Intermediate tier
 *   2. coding_questions    — 50 questions
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (Lessons 327–336):
 *   6.1  Introduction to Modeling & Simulation
 *   6.2  Random Numbers & Probability Distributions
 *   6.3  Monte Carlo Methods
 *   6.4  Discrete-Event Simulation
 *   6.5  Differential Equations & Continuous Simulation
 *   6.6  The SIR Epidemic Model
 *   6.7  Agent-Based Modeling
 *   6.8  System Dynamics & Feedback Loops
 *   6.9  Sensitivity Analysis & Model Validation
 *   6.10 Simulation Output Analysis & Statistical Testing
 *
 * Difficulty: Intermediate — multi-step algorithm implementations using only
 * Python builtins + math/random. Problems require implementing simulation loops,
 * numerical integration, and statistical analysis from scratch.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module6CodingChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (! $category) {
            $this->command->error('Intermediate category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 6 — Modeling & Simulation (Intermediate) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Modeling & Simulation',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Implement core simulation and modeling algorithms from scratch in pure Python. Build Monte Carlo estimators, Euler integrators, SIR epidemic solvers, discrete-event queues, and statistical output analyzers — no third-party libraries required.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 1000,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.1: Introduction to Modeling & Simulation (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
A **simulation model** runs for `T` time steps. At each step, a quantity `x` changes by a fixed rate `r` (i.e., `x_{t+1} = x_t + r`).

Read the initial value `x0`, rate `r`, and number of steps `T`. Print the value of `x` at each time step from 1 to T (inclusive), one per line, rounded to 4 decimal places.

Example:
```
Input:
10
2.5
4
Output:
12.5
15.0
17.5
20.0
```
MD,
                'starter_code'        => "x0 = float(input())\nr = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Implement **exponential growth/decay** simulation.

`x_{t+1} = x_t * (1 + r)`

Read `x0` (initial value), `r` (growth rate, can be negative), and `T` (steps). Print the value at each step 1..T rounded to 4 decimal places.

Example:
```
Input:
100
0.1
3
Output:
110.0
121.0
133.1
```
MD,
                'starter_code'        => "x0 = float(input())\nr = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 100,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
A **logistic growth** model is given by:

`x_{t+1} = x_t + r * x_t * (1 - x_t / K)`

where `r` is the growth rate and `K` is the carrying capacity.

Read `x0`, `r`, `K`, and `T`. Print `x` at each step 1..T rounded to 4 decimal places.

Example:
```
Input:
10
0.5
100
5
Output:
14.5
20.5525
28.3424
37.8332
48.2695
```
MD,
                'starter_code'        => "x0 = float(input())\nr = float(input())\nK = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
A model has state variables `x` and `y`. At each step:
- `x_{t+1} = a * x_t - b * x_t * y_t`
- `y_{t+1} = -c * y_t + d * x_t * y_t`

(Discrete Lotka-Volterra predator-prey)

Read `x0`, `y0`, `a`, `b`, `c`, `d` (each on its own line), then `T`. Print `x` and `y` at each step 1..T, space-separated, rounded to 4 decimal places.

Example:
```
Input:
10
5
1.1
0.04
0.4
0.01
3
Output:
10.9 2.05
11.6733 0.9803
12.5099 0.4997
```
MD,
                'starter_code'        => "x0 = float(input())\ny0 = float(input())\na = float(input())\nb = float(input())\nc = float(input())\nd = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Compute the **steady state** of a linear recurrence `x_{t+1} = a * x_t + b`. The steady state is `x* = b / (1 - a)` (when `a ≠ 1`).

Simulate for `T` steps. Print each step value (rounded to 4 dp) and on the final line print `Steady state: X` (4 dp).

Read `x0`, `a`, `b`, `T`.

Example:
```
Input:
0
0.5
5
4
Output:
5.0
7.5
8.75
9.375
Steady state: 10.0
```
MD,
                'starter_code'        => "x0 = float(input())\na = float(input())\nb = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.2: Random Numbers & Probability Distributions (Q6–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Implement the **Linear Congruential Generator (LCG)** for pseudo-random numbers.

`X_{n+1} = (a * X_n + c) mod m`

The uniform random number is `X_{n+1} / m`.

Read `seed`, `a`, `c`, `m`, and `n` (number of values to generate). Print each uniform random number rounded to 6 decimal places, one per line.

Example:
```
Input:
1
1664525
1013904223
4294967296
5
Output:
0.250062
0.185898
0.296027
0.474945
0.284256
```
MD,
                'starter_code'        => "seed = int(input())\na = int(input())\nc = int(input())\nm = int(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Use the **inverse transform method** to generate samples from an **Exponential distribution** with rate `lambda`.

Formula: `X = -ln(U) / lambda`  where U is uniform in (0,1).

Use `random.seed(seed)` and `random.random()` for U. Read `seed`, `lambda`, and `n`. Print each sample rounded to 6 decimal places.

Example:
```
Input:
42
2.0
3
Output:
0.279768
0.203905
0.029299
```
MD,
                'starter_code'        => "import random\nimport math\nrandom.seed(int(input()))\nlam = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Generate samples from a **Normal distribution** using the **Box-Muller transform**.

Given two uniform random numbers U1 and U2:
- `Z0 = sqrt(-2 * ln(U1)) * cos(2π * U2)`
- `Z1 = sqrt(-2 * ln(U1)) * sin(2π * U2)`

Then `X = mu + sigma * Z`.

Use `random.seed(seed)`, draw pairs (U1, U2) with `random.random()`. Generate `n` normal samples (use Z0, Z1 alternately from each pair). Read `seed`, `mu`, `sigma`, `n`. Print each sample rounded to 6 decimal places.

Example:
```
Input:
42
0
1
4
Output:
0.304717
-1.039984
0.750451
0.940565
```
MD,
                'starter_code'        => "import random\nimport math\nrandom.seed(int(input()))\nmu = float(input())\nsigma = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 175,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Fit a **Poisson distribution** to observed count data. The MLE estimate of lambda is the sample mean.

Read `n` observed counts (one per line). Print:
- `lambda_hat: X` (4 dp)
- The PMF P(X=k) for k = 0, 1, 2, ..., up to the observed maximum, rounded to 6 dp.

PMF: `P(X=k) = e^(-λ) * λ^k / k!`

Example:
```
Input:
5
2
3
1
2
2
Output:
lambda_hat: 2.0
P(0): 0.135335
P(1): 0.270671
P(2): 0.270671
P(3): 0.180447
```
MD,
                'starter_code'        => "import math\nn = int(input())\ncounts = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 175,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Implement **rejection sampling** to sample from a target distribution using a proposal distribution.

Target: triangular distribution on [0,1]: `f(x) = 2x`
Proposal: uniform on [0,1]: `g(x) = 1`, with `M = 2` (since `f(x)/g(x) ≤ 2`)

Algorithm:
1. Draw `x ~ Uniform(0,1)` and `u ~ Uniform(0,1)`
2. Accept if `u ≤ f(x) / (M * g(x))` = `u ≤ x`
3. Repeat until `n` samples accepted

Use `random.seed(seed)`. Read `seed` and `n`. Print each accepted sample rounded to 6 dp.

Example:
```
Input:
42
5
Output:
0.773956
0.458650
0.602764
0.943748
0.886001
```
MD,
                'starter_code'        => "import random\nrandom.seed(int(input()))\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.3: Monte Carlo Methods (Q11–Q16)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Estimate **π using Monte Carlo** simulation.

Throw `n` random darts at a unit square [0,1]×[0,1]. A dart lands inside the quarter-circle of radius 1 if `x² + y² ≤ 1`. Then `π ≈ 4 * inside / n`.

Use `random.seed(seed)`. Read `seed` and `n`. Print the estimate of π rounded to 6 decimal places.

Example:
```
Input:
42
10000
Output:
3.141200
```
MD,
                'starter_code'        => "import random\nrandom.seed(int(input()))\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 125,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Use Monte Carlo integration to estimate `∫₀¹ f(x) dx` for `f(x) = e^(-x²)`.

Sample `n` uniform points in [0,1] and estimate the integral as the mean of `f(x)`.

Use `random.seed(seed)`. Read `seed` and `n`. Print the estimate rounded to 6 decimal places.

Example:
```
Input:
42
10000
Output:
0.746824
```
MD,
                'starter_code'        => "import random\nimport math\nrandom.seed(int(input()))\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Simulate a **Monte Carlo option pricing** (European call option, simplified).

At expiry, payoff = `max(S_T - K, 0)` where `S_T = S0 * exp((r - 0.5*sigma²)*T + sigma*sqrt(T)*Z)` and `Z ~ N(0,1)`.

Price = `exp(-r*T) * mean(payoff)` over `n` simulations.

Read `S0`, `K`, `r`, `sigma`, `T`, `n`, `seed`. Print the option price rounded to 6 dp.

Example:
```
Input:
100
100
0.05
0.2
1.0
10000
42
Output:
10.449311
```
MD,
                'starter_code'        => "import random\nimport math\nS0 = float(input())\nK = float(input())\nr = float(input())\nsigma = float(input())\nT = float(input())\nn = int(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Use Monte Carlo to estimate the **probability that a random walk** (starting at 0, each step ±1 with equal probability) reaches `+b` before `-a` in at most `max_steps` steps.

Run `n` simulations. Print the estimated probability rounded to 4 dp.

Read `a`, `b`, `max_steps`, `n`, `seed`.

Example:
```
Input:
3
3
100
10000
42
Output:
0.5020
```
MD,
                'starter_code'        => "import random\na = int(input())\nb = int(input())\nmax_steps = int(input())\nn = int(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 175,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
**Monte Carlo reliability estimation**: a system has `k` components in series. Each component fails independently with probability `p`. The system fails if **any** component fails. Simulate `n` runs and estimate:
1. P(system failure) rounded to 4 dp
2. Mean number of failed components (among failed runs) rounded to 4 dp. If no failed runs, print `N/A`.

Read `k`, `p`, `n`, `seed`.

Example:
```
Input:
3
0.1
10000
42
Output:
P(failure): 0.2716
Mean failed components: 1.1041
```
MD,
                'starter_code'        => "import random\nk = int(input())\np = float(input())\nn = int(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Use **importance sampling** to estimate `P(X > c)` where `X ~ N(0,1)` and `c` is large (rare event).

Proposal: `N(c, 1)`. Importance weight: `w(x) = f(x)/g(x)` where `f` is N(0,1) pdf and `g` is N(c,1) pdf.

Estimate: `(1/n) * Σ w(xᵢ) * 1(xᵢ > c)`

Use `random.seed(seed)`. Samples from N(c,1) via Box-Muller + shift. Read `c`, `n`, `seed`. Print the estimate in scientific notation with 6 significant figures (e.g., `1.234567e-04`).

Example:
```
Input:
3.0
10000
42
Output:
1.350013e-03
```
MD,
                'starter_code'        => "import random\nimport math\nc = float(input())\nn = int(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.4: Discrete-Event Simulation (Q17–Q21)
            // ═══════════════════════════════════════════════════════════════

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Simulate an **M/M/1 queue**. Customers arrive with exponential inter-arrival times (rate `lambda`) and are served with exponential service times (rate `mu`). Simulate `n` customers.

For each customer record: arrival time, service start time, departure time. Print summary:
- `mean_wait: X` (time waiting in queue, 4 dp)
- `mean_service: X` (service time, 4 dp)
- `mean_sojourn: X` (total time in system, 4 dp)
- `max_queue: N` (maximum queue length observed)

Use `random.seed(seed)`. Read `lambda_`, `mu`, `n`, `seed`.

Example:
```
Input:
2.0
3.0
5
42
Output:
mean_wait: 0.3349
mean_service: 0.3382
mean_sojourn: 0.6730
max_queue: 2
```
MD,
                'starter_code'        => "import random\nimport math\nrandom.seed(42)\nlambda_ = float(input())\nmu = float(input())\nn = int(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Simulate a **multi-server queue (M/M/c)**. There are `c` servers. Customers join the single queue and are served by the first free server.

Simulate `n` customers. Print:
- `mean_wait: X` (4 dp)
- `server_utilization: X` (fraction of time servers busy, averaged over all servers, 4 dp)

Use `random.seed(seed)`. Read `lambda_`, `mu`, `c`, `n`, `seed`.

Example:
```
Input:
4.0
3.0
2
100
42
Output:
mean_wait: 0.2815
server_utilization: 0.6633
```
MD,
                'starter_code'        => "import random\nimport math\nlambda_ = float(input())\nmu = float(input())\nc = int(input())\nn = int(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 275,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Simulate a **finite-capacity queue (M/M/1/K)**. The system holds at most `K` customers (including the one being served). Arriving customers who find K customers are lost.

Simulate until `n` arrivals have been generated. Print:
- `accepted: N` (customers who entered system)
- `lost: N`
- `mean_wait: X` (4 dp, of accepted customers only)

Use `random.seed(seed)`. Read `lambda_`, `mu`, `K`, `n`, `seed`.

Example:
```
Input:
5.0
3.0
3
100
42
Output:
accepted: 79
lost: 21
mean_wait: 0.1968
```
MD,
                'starter_code'        => "import random\nimport math\nlambda_ = float(input())\nmu = float(input())\nK = int(input())\nn = int(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 275,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Simulate a **priority queue**. Customers arrive with exponential inter-arrivals (rate `lambda`). Each customer is assigned priority class 1 (prob `p1`) or class 2 (prob 1-p1). Class 1 has preemptive priority. Service times are exponential with rate `mu` for both classes.

Simulate `n` customers. Print mean sojourn time for each class (4 dp):
```
class1_sojourn: X
class2_sojourn: X
```

Use `random.seed(seed)`. Read `lambda_`, `mu`, `p1`, `n`, `seed`.

Example:
```
Input:
3.0
4.0
0.3
100
42
Output:
class1_sojourn: 0.2891
class2_sojourn: 0.4523
```
MD,
                'starter_code'        => "import random\nimport math\nlambda_ = float(input())\nmu = float(input())\np1 = float(input())\nn = int(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Simulate a **machine repair shop**. There are `M` machines and `R` repairers. Machines run for exponential time (rate `alpha`) then break down and wait for a repairer (service rate `mu`). Simulate until `T` total time units and count:
- `total_breakdowns`
- `mean_repair_wait: X` (4 dp)
- `machine_availability: X` (fraction of time machines are running, 4 dp)

Use `random.seed(seed)`. Read `M`, `R`, `alpha`, `mu`, `T`, `seed`.

Example:
```
Input:
5
2
0.5
2.0
100
42
Output:
total_breakdowns: 238
mean_repair_wait: 0.0726
machine_availability: 0.7943
```
MD,
                'starter_code'        => "import random\nimport math\nM = int(input())\nR = int(input())\nalpha = float(input())\nmu = float(input())\nT = float(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.5: Differential Equations & Continuous Simulation (Q22–Q26)
            // ═══════════════════════════════════════════════════════════════

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Implement **Euler's method** for the ODE `dy/dt = f(t, y)`.

For this problem, `f(t, y) = -k * y` (exponential decay).

`y_{n+1} = y_n + h * f(t_n, y_n)`

Read `y0`, `k`, `h` (step size), `T` (end time). Print `y` at each step (t = h, 2h, ..., T) rounded to 6 dp.

Example:
```
Input:
1.0
1.0
0.5
2.0
Output:
0.500000
0.250000
0.125000
0.062500
```
MD,
                'starter_code'        => "y0 = float(input())\nk = float(input())\nh = float(input())\nT = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Implement the **Runge-Kutta 4th order (RK4)** method for the ODE `dy/dt = f(t, y)`.

For `f(t, y) = r * y * (1 - y/K)` (logistic growth):

```
k1 = h * f(t, y)
k2 = h * f(t + h/2, y + k1/2)
k3 = h * f(t + h/2, y + k2/2)
k4 = h * f(t + h, y + k3)
y_next = y + (k1 + 2k2 + 2k3 + k4) / 6
```

Read `y0`, `r`, `K`, `h`, `T`. Print `y` at each step rounded to 6 dp.

Example:
```
Input:
10.0
0.5
100.0
1.0
4
Output:
14.625000
20.758056
28.640974
38.219875
```
MD,
                'starter_code'        => "y0 = float(input())\nr = float(input())\nK = float(input())\nh = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Solve a **system of ODEs** using Euler's method. The harmonic oscillator:

`dx/dt = v`
`dv/dt = -omega² * x`

Read `x0`, `v0`, `omega`, `h`, `T`. Print `x` and `v` at each step, space-separated, rounded to 6 dp.

Example:
```
Input:
1.0
0.0
1.0
0.1
5
Output:
1.000000 -0.100000
0.990000 -0.199000
0.970100 -0.296010
0.940497 -0.390111
0.901486 -0.480200
```
MD,
                'starter_code'        => "x0 = float(input())\nv0 = float(input())\nomega = float(input())\nh = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 175,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Compute the **global truncation error** of Euler's method vs the exact solution for `dy/dt = -y`, `y(0) = 1` (exact: `y(t) = e^(-t)`).

Read `h` and `T`. Print the absolute error `|y_euler - y_exact|` at each step rounded to 8 dp.

Example:
```
Input:
0.5
2.0
Output:
0.10653066
0.18393972
0.23745296
0.27327021
```
MD,
                'starter_code'        => "import math\nh = float(input())\nT = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 175,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Implement the **trapezoidal method (Heun's method)** for `dy/dt = f(t, y)` where `f(t,y) = sin(t) - y`.

```
y_predict = y_n + h * f(t_n, y_n)
y_{n+1} = y_n + h/2 * (f(t_n, y_n) + f(t_{n+1}, y_predict))
```

Read `y0`, `h`, `T` (integer steps). Print `y` at each step rounded to 6 dp.

Example:
```
Input:
0.0
0.5
4
Output:
0.124675
0.422856
0.757453
1.029561
```
MD,
                'starter_code'        => "import math\ny0 = float(input())\nh = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.6: The SIR Epidemic Model (Q27–Q31)
            // ═══════════════════════════════════════════════════════════════

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Implement the **discrete-time SIR model**:

```
S_{t+1} = S_t - beta * S_t * I_t / N
I_{t+1} = I_t + beta * S_t * I_t / N - gamma * I_t
R_{t+1} = R_t + gamma * I_t
```

where `N = S + I + R` (constant).

Read `S0`, `I0`, `R0`, `beta`, `gamma`, `T`. Print `S`, `I`, `R` at each step 1..T rounded to 2 dp, space-separated.

Example:
```
Input:
990
10
0
0.3
0.1
5
Output:
987.03 12.97 0.0
983.46 16.4 0.14
978.96 20.37 0.67
973.15 24.89 1.96
965.6 29.79 4.61
```
MD,
                'starter_code'        => "S0 = float(input())\nI0 = float(input())\nR0 = float(input())\nbeta = float(input())\ngamma = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 175,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Simulate the **SIR model using RK4** (continuous time). The ODEs are:

```
dS/dt = -beta * S * I / N
dI/dt = beta * S * I / N - gamma * I
dR/dt = gamma * I
```

Read `S0`, `I0`, `R0`, `beta`, `gamma`, `h` (step), `T` (end time). Print `S`, `I`, `R` at each step rounded to 4 dp, space-separated.

Example:
```
Input:
990
10
0
0.3
0.1
1.0
3
Output:
987.0372 12.9328 0.03
983.478 16.3682 0.1538
978.949 20.2696 0.7814
```
MD,
                'starter_code'        => "S0 = float(input())\nI0 = float(input())\nR0 = float(input())\nbeta = float(input())\ngamma = float(input())\nh = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Compute the **basic reproduction number R₀** and predict epidemic outcome.

R₀ = beta / gamma

If R₀ > 1: epidemic grows. If R₀ ≤ 1: epidemic dies out.

Simulate the discrete SIR model for `T` steps. Print:
- `R0: X` (4 dp)
- `outcome: epidemic` or `outcome: dies out`
- `peak_infected: X` (maximum I value observed, 2 dp)
- `peak_day: N` (day when peak occurs)

Read `S0`, `I0`, `R0_param=0`, `beta`, `gamma`, `T`.

Example:
```
Input:
990
10
0
0.3
0.1
100
Output:
R0: 3.0
outcome: epidemic
peak_infected: 248.88
peak_day: 31
```
MD,
                'starter_code'        => "S0 = float(input())\nI0 = float(input())\nR_init = float(input())\nbeta = float(input())\ngamma = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Implement the **SEIR model** (adds Exposed compartment):

```
dS/dt = -beta * S * I / N
dE/dt = beta * S * I / N - sigma * E
dI/dt = sigma * E - gamma * I
dR/dt = gamma * I
```

Use Euler's method with step `h`. Read `S0`, `E0`, `I0`, `R0`, `beta`, `sigma`, `gamma`, `h`, `T`. Print `S`, `E`, `I`, `R` at each step rounded to 2 dp.

Example:
```
Input:
990
0
10
0
0.3
0.2
0.1
1.0
3
Output:
987.03 2.91 11.97 0.1
983.52 5.36 13.58 0.55
979.29 7.41 15.63 1.68
```
MD,
                'starter_code'        => "S0 = float(input())\nE0 = float(input())\nI0 = float(input())\nR0 = float(input())\nbeta = float(input())\nsigma = float(input())\ngamma = float(input())\nh = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Implement **vaccination in the SIR model**. A fraction `v` of susceptibles is vaccinated at t=0 (moved immediately to R). Then run the discrete SIR model.

Print:
- `vaccinated: N` (number vaccinated, integer)
- `peak_infected: X` (2 dp)
- `total_infected: X` (total who passed through I, 2 dp = final R - initial R - vaccinated)

Read `S0`, `I0`, `R0`, `beta`, `gamma`, `v`, `T`.

Example:
```
Input:
990
10
0
0.3
0.1
0.5
100
Output:
vaccinated: 495
peak_infected: 20.39
total_infected: 76.55
```
MD,
                'starter_code'        => "S0 = float(input())\nI0 = float(input())\nR0 = float(input())\nbeta = float(input())\ngamma = float(input())\nv = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.7: Agent-Based Modeling (Q32–Q35)
            // ═══════════════════════════════════════════════════════════════

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Simulate a simple **1D agent-based random walk** with `n` agents. Each agent starts at position 0 and at each step moves +1 or -1 with equal probability. Simulate `T` steps.

Print at each step: the mean position (4 dp) and the standard deviation of positions (4 dp), space-separated.

Use `random.seed(seed)`. Read `n`, `T`, `seed`.

Example:
```
Input:
100
5
42
Output:
0.0200 0.9998
0.0400 1.4067
-0.0200 1.7179
0.0600 1.9974
0.1400 2.2264
```
MD,
                'starter_code'        => "import random\nimport math\nn = int(input())\nT = int(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Simulate a **Schelling segregation model** on a 1D array.

Agents are placed on a grid of size `N`. Each cell is `0` (empty), `1` (type A), or `2` (type B). An agent is **unhappy** if fewer than `threshold` fraction of its non-empty neighbors (within radius 1) are the same type.

Unhappy agents move to a random empty cell (using `random.seed(seed)`).

Simulate for `T` rounds or until no unhappy agents. Print:
- The final grid (space-separated)
- `rounds: N`
- `segregation: X` (fraction of neighboring same-type pairs, 4 dp)

Read `N`, array (space-separated ints), `threshold`, `T`, `seed`.

Example:
```
Input:
8
1 2 1 0 2 1 2 0
0.5
10
42
Output:
1 1 1 2 2 2 0 0
rounds: 3
segregation: 1.0
```
MD,
                'starter_code'        => "import random\nN = int(input())\ngrid = list(map(int, input().split()))\nthreshold = float(input())\nT = int(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 275,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Simulate **agent-based SIR** on a small population. Each agent is S, I, or R. At each step:
1. Each infected agent contacts `k` random agents
2. Each susceptible contact becomes infected with probability `p_infect`
3. Each infected agent recovers with probability `p_recover`

Use `random.seed(seed)`. Simulate `T` steps. Print `S`, `I`, `R` counts at each step.

Read `N` (population), `I0` (initial infected), `k`, `p_infect`, `p_recover`, `T`, `seed`.

Example:
```
Input:
100
5
3
0.1
0.2
5
42
Output:
94 6 0
92 6 2
90 6 4
88 5 7
87 4 9
```
MD,
                'starter_code'        => "import random\nN = int(input())\nI0 = int(input())\nk = int(input())\np_infect = float(input())\np_recover = float(input())\nT = int(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Simulate a **flocking model** (simplified Boids, 1D velocity alignment). `n` agents each have a position and velocity. At each step:
1. Each agent's new velocity = weighted average of its own velocity and the mean velocity of its `r`-radius neighbors (weight `w` for self, `1-w` for neighbors mean)
2. Position updates: `x += v`

Use `random.seed(seed)` to initialise positions (uniform 0..100) and velocities (uniform -1..1). Print mean velocity and velocity std at each step (4 dp), space-separated.

Read `n`, `r`, `w`, `T`, `seed`.

Example:
```
Input:
20
10
0.5
5
42
Output:
0.0403 0.4652
0.0403 0.2787
0.0403 0.2133
0.0403 0.1851
0.0403 0.1649
```
MD,
                'starter_code'        => "import random\nimport math\nn = int(input())\nr = float(input())\nw = float(input())\nT = int(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 275,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.8: System Dynamics & Feedback Loops (Q36–Q39)
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Model a **stock-and-flow system** (inventory management). Stock = inventory level. Inflow = production rate. Outflow = demand rate. A **negative feedback loop** adjusts production toward target.

```
gap_t = target - stock_t
production_t = demand + gap_t / adjustment_time
stock_{t+1} = stock_t + production_t - demand
```

Read `stock0`, `target`, `demand`, `adjustment_time`, `T`. Print stock at each step 1..T rounded to 4 dp.

Example:
```
Input:
50
100
10
5
5
Output:
50.0
60.0
70.0
80.0
90.0
```
MD,
                'starter_code'        => "stock0 = float(input())\ntarget = float(input())\ndemand = float(input())\nadj_time = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 175,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Simulate a **two-stock system with feedback** (predator-prey using system dynamics):

```
dPrey/dt = r_prey * Prey - alpha * Prey * Predator
dPredator/dt = beta * Prey * Predator - d_pred * Predator
```

Use Euler's method with step `h`. Read `Prey0`, `Predator0`, `r_prey`, `alpha`, `beta`, `d_pred`, `h`, `T`. Print `Prey` and `Predator` at each step, space-separated, rounded to 4 dp.

Example:
```
Input:
100
10
0.1
0.01
0.001
0.05
1.0
5
Output:
100.0 9.95
100.4975 9.8503
100.9951 9.7511
101.4913 9.6523
101.9840 9.5541
```
MD,
                'starter_code'        => "Prey0 = float(input())\nPredator0 = float(input())\nr_prey = float(input())\nalpha = float(input())\nbeta = float(input())\nd_pred = float(input())\nh = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Model a **bass diffusion model** for product adoption:

```
dA/dt = (p + q * A/N) * (N - A)
```

where `A` = adopters, `N` = total population, `p` = innovation coefficient, `q` = imitation coefficient.

Use Euler with step `h`. Read `N`, `A0`, `p`, `q`, `h`, `T`. Print `A` at each step rounded to 4 dp. Also print `peak_adoption_rate: X` (4 dp) and `peak_day: N`.

Example:
```
Input:
1000
1
0.01
0.4
1.0
20
Output:
14.9 ...
...
peak_adoption_rate: 107.6534
peak_day: 11
```
MD,
                'starter_code'        => "N = float(input())\nA0 = float(input())\np = float(input())\nq = float(input())\nh = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Simulate a **delayed feedback system**. Stock accumulates based on net flow, but the correction responds to a delayed observation (delay `d` steps):

```
observed_t = stock_{t-d}  (or stock_0 if t < d)
flow_t = target - observed_t
stock_{t+1} = stock_t + flow_t * h
```

Read `stock0`, `target`, `h`, `d`, `T`. Print stock at each step 1..T rounded to 4 dp.

Example:
```
Input:
0
10
0.5
2
6
Output:
5.0
10.0
12.5
12.5
10.0
8.75
```
MD,
                'starter_code'        => "stock0 = float(input())\ntarget = float(input())\nh = float(input())\nd = int(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.9: Sensitivity Analysis & Model Validation (Q40–Q44)
            // ═══════════════════════════════════════════════════════════════

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Perform **one-at-a-time (OAT) sensitivity analysis** on the SIR model's peak infected.

Vary each parameter (`beta` and `gamma`) by ±10% while holding others fixed. Report the **sensitivity index** for each:

`SI = (ΔOutput / Output_base) / (ΔInput / Input_base)`

Read `S0`, `I0`, `R0`, `beta`, `gamma`, `T`. Print:
```
SI_beta: X
SI_gamma: X
```
(4 dp, use the average of +10% and −10% perturbations)

Example:
```
Input:
990
10
0
0.3
0.1
100
Output:
SI_beta: 1.2341
SI_gamma: -0.8765
```
MD,
                'starter_code'        => "S0 = float(input())\nI0 = float(input())\nR0_init = float(input())\nbeta = float(input())\ngamma = float(input())\nT = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Implement **Latin Hypercube Sampling (LHS)** for 2 parameters, each with `n` strata.

For each parameter:
1. Divide [0,1] into `n` equal strata
2. Sample one point uniformly from each stratum
3. Randomly permute the samples

Use `random.seed(seed)`. Read `n`, `seed`. Print the `n` sample pairs (param1, param2), each row space-separated, rounded to 6 dp.

Example:
```
Input:
4
42
Output:
0.158951 0.919999
0.434498 0.377540
0.617472 0.626908
0.804481 0.046450
```
MD,
                'starter_code'        => "import random\nn = int(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Compute the **RMSE and MAE** between model output and observed data, and the **Nash-Sutcliffe Efficiency (NSE)**:

`NSE = 1 - Σ(obs - sim)² / Σ(obs - obs_mean)²`

NSE = 1 is perfect; NSE < 0 means model is worse than mean.

Read `n` observed values (one per line), then `n` simulated values. Print:
```
RMSE: X
MAE: X
NSE: X
```
All rounded to 4 dp.

Example:
```
Input:
4
10
20
30
40
11
19
32
38
Output:
RMSE: 1.5811
MAE: 1.5
NSE: 0.9767
```
MD,
                'starter_code'        => "import math\nn = int(input())\nobs = [float(input()) for _ in range(n)]\nsim = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Implement **Sobol' first-order sensitivity indices** (simplified, using `n` samples each for A, B, and AB matrices) for a model `f(x1, x2) = x1 + x2 + x1*x2`.

Use `random.seed(seed)`. Generate matrices A and B of shape (n, 2) with uniform samples. AB_i = A with i-th column replaced by B's i-th column.

`S_i = Var(E[Y|Xi]) / Var(Y) ≈ (1/n * Σ f(B) * (f(AB_i) - f(A))) / Var(Y)`

Read `n`, `seed`. Print `S1: X` and `S2: X` (4 dp).

Example:
```
Input:
10000
42
Output:
S1: 0.4123
S2: 0.4098
```
MD,
                'starter_code'        => "import random\nn = int(input())\nrandom.seed(int(input()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Perform **cross-validation of a simulation model**. Given `k` sets of observed time-series data (each of length `L`), for each fold use k-1 sets to calibrate parameters and the remaining fold to validate.

The model is linear growth: `x_t = x0 + r * t`. Calibrate by fitting `r` via OLS (minimising sum of squared errors). Report the mean NSE across folds.

Read `k`, `L`, then `k` series (each on one line, space-separated). Print `mean_NSE: X` (4 dp).

Example:
```
Input:
3
4
0 1 2 3
0 2 4 6
0 3 6 9
Output:
mean_NSE: 1.0
```
MD,
                'starter_code'        => "k = int(input())\nL = int(input())\nseries = [list(map(float, input().split())) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 275,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.10: Simulation Output Analysis (Q45–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Compute a **confidence interval for the simulation mean** using the t-distribution (independent replications).

Given `n` replication outputs, compute:
- Sample mean
- Sample std (ddof=1)
- 95% CI using t critical value (use t = 2.776 for df=4, 2.262 for df=9, 2.101 for df=19, 2.045 for df=29, 1.96 for df≥30 as approximation)

Print `mean: X`, `std: X`, `CI: [lo, hi]` (all 4 dp).

Read `n` values (one per line).

Example:
```
Input:
5
10
12
11
13
9
Output:
mean: 11.0
std: 1.5811
CI: [9.0379, 12.9621]
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndata = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Compute the **batch means** estimator for the variance of a simulation output. Divide a single long run of `n` observations into `b` batches of size `m = n // b`. Compute the mean of each batch and the variance of batch means.

`Var_est = Var(batch_means) / b`  (use population variance of batch means)

Print:
- `batch_means: X X X ...` (each rounded to 4 dp)
- `variance_estimate: X` (6 dp)

Read `n`, `b`, then `n` values (one per line).

Example:
```
Input:
10
2
1 3 2 4 5 7 6 8 9 10
Output:
batch_means: 2.0 3.5 6.0 7.0 9.5
variance_estimate: 7.25
```
MD,
                'starter_code'        => "n = int(input())\nb = int(input())\ndata = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Perform a **two-sample t-test** to compare outputs from two simulation configurations.

`t = (mean1 - mean2) / sqrt(s1²/n1 + s2²/n2)`

Use Welch's approximation for degrees of freedom:
`df = (s1²/n1 + s2²/n2)² / ((s1²/n1)²/(n1-1) + (s2²/n2)²/(n2-1))`

Use t-critical value lookup (same table as Q45). Print `t: X`, `df: N`, and `Reject H0` or `Fail to reject H0` at α=0.05 (two-tailed).

Read `n1` values for config 1 (space-separated), then `n2` values for config 2.

Example:
```
Input:
10 12 11 13 9
20 22 21 23 19
Output:
t: -10.0
df: 8
Reject H0
```
MD,
                'starter_code'        => "import math\ns1 = list(map(float, input().split()))\ns2 = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Determine the **required number of replications** to achieve a desired precision.

Given a pilot run of `n0` replications, target half-width `e`, and α=0.05, compute:

`n_required = ceil((t_{α/2, n0-1} * s / e)²)`

where `s` is the sample std of the pilot (use ddof=1) and `t` is the t-critical value (from the same table).

Read the pilot data (space-separated) and target `e`. Print `n_required: N`.

Example:
```
Input:
10 12 11 13 9 11 10 12 11 13
1.0
Output:
n_required: 12
```
MD,
                'starter_code'        => "import math\npilot = list(map(float, input().split()))\ne = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 225,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Compute the **autocorrelation function (ACF)** of a simulation output time series at lags 1..L and determine the **warm-up period** (first lag where |ACF| < 0.2).

ACF(k) = Cov(xₜ, xₜ₋ₖ) / Var(x)

Read `n` values (space-separated), then `L`. Print ACF at each lag (4 dp) and `warmup: N` (the first lag where |ACF| < 0.2, or `warmup: >L` if never).

Example:
```
Input:
1 2 3 4 5 6 7 8 9 10
3
Output:
lag 1: 0.7000
lag 2: 0.4121
lag 3: 0.1333
warmup: 3
```
MD,
                'starter_code'        => "data = list(map(float, input().split()))\nL = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Conduct a complete **simulation study**: run the M/M/1 queue simulation for `R` replications, compute the 95% CI for mean sojourn time, and determine if theoretical value (1/(mu-lambda)) is within the CI.

For each replication, simulate `n` customers. Use seeds `seed`, `seed+1`, ..., `seed+R-1`.

Print:
- `theoretical: X` (4 dp)
- `simulated_mean: X` (4 dp)
- `CI: [lo, hi]` (4 dp)
- `contains_theoretical: True` or `False`

Read `lambda_`, `mu`, `n`, `R`, `seed`.

Example:
```
Input:
2.0
3.0
200
10
42
Output:
theoretical: 1.0
simulated_mean: 0.9823
CI: [0.8941, 1.0705]
contains_theoretical: True
```
MD,
                'starter_code'        => "import random\nimport math\nlambda_ = float(input())\nmu = float(input())\nn = int(input())\nR = int(input())\nseed = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
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

        // ── Q1: linear growth ──────────────────────────────────────────────
        $seed(1, [
            ['input' => "10\n2.5\n4",     'expected_output' => "12.5\n15.0\n17.5\n20.0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n1\n3",        'expected_output' => "1.0\n2.0\n3.0",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n-5\n4",     'expected_output' => "95.0\n90.0\n85.0\n80.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n0\n2",        'expected_output' => "5.0\n5.0",                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: exponential growth ─────────────────────────────────────────
        $seed(2, [
            ['input' => "100\n0.1\n3",    'expected_output' => "110.0\n121.0\n133.1",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1.0\n3",      'expected_output' => "2.0\n4.0\n8.0",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n-0.5\n3",   'expected_output' => "50.0\n25.0\n12.5",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "1000\n0.01\n2",  'expected_output' => "1010.0\n1020.1",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: logistic growth ────────────────────────────────────────────
        $seed(3, [
            ['input' => "10\n0.5\n100\n5",  'expected_output' => "14.5\n20.5525\n28.3424\n37.8332\n48.2695",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "50\n0.2\n100\n3",  'expected_output' => "60.0\n68.8\n75.7696",                       'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1.0\n10\n4",   'expected_output' => "1.9\n3.498\n5.7842\n8.0395",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "99\n0.1\n100\n2", 'expected_output' => "99.099\n99.1971",                            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Lotka-Volterra ─────────────────────────────────────────────
        $seed(4, [
            ['input' => "10\n5\n1.1\n0.04\n0.4\n0.01\n3",  'expected_output' => "10.9 2.05\n11.6733 0.9803\n12.5099 0.4997",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "50\n10\n1.0\n0.02\n0.01\n0.1\n2", 'expected_output' => "45.0 10.4\n40.638 10.6328",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n5\n1.2\n0.05\n0.001\n0.1\n2",'expected_output' => "94.75 5.415\n89.9118 5.8572",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n10\n1.0\n0.1\n0.1\n1.0\n2",  'expected_output' => "0.0 1.0\n0.0 0.0",                           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: steady state ───────────────────────────────────────────────
        $seed(5, [
            ['input' => "0\n0.5\n5\n4",    'expected_output' => "5.0\n7.5\n8.75\n9.375\nSteady state: 10.0",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n0.0\n5\n3",   'expected_output' => "5.0\n5.0\n5.0\nSteady state: 5.0",                 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0.9\n1\n5",    'expected_output' => "1.0\n1.9\n2.71\n3.439\n4.0951\nSteady state: 10.0",'is_hidden' => true,  'order_index' => 3],
            ['input' => "20\n0.5\n0\n3",   'expected_output' => "10.0\n5.0\n2.5\nSteady state: 0.0",                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: LCG ───────────────────────────────────────────────────────
        $seed(6, [
            ['input' => "1\n1664525\n1013904223\n4294967296\n5",  'expected_output' => "0.250062\n0.185898\n0.296027\n0.474945\n0.284256",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n3\n1\n16\n4",                         'expected_output' => "0.0625\n0.25\n0.8125\n0.5",                        'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n5\n3\n16\n3",                         'expected_output' => "0.625\n0.25\n0.5625",                              'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1664525\n1013904223\n4294967296\n3",  'expected_output' => "0.250062\n0.185898\n0.296027",                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: inverse transform exponential ─────────────────────────────
        $seed(7, [
            ['input' => "42\n2.0\n3",   'expected_output' => "0.279768\n0.203905\n0.029299",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1.0\n3",    'expected_output' => "0.315844\n1.600374\n0.581672",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "99\n0.5\n2",   'expected_output' => "1.042091\n0.431507",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "42\n5.0\n2",   'expected_output' => "0.111907\n0.081562",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Box-Muller ─────────────────────────────────────────────────
        $seed(8, [
            ['input' => "42\n0\n1\n4",   'expected_output' => "0.304717\n-1.039984\n0.750451\n0.940565",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0\n1\n2",    'expected_output' => "1.560316\n-0.076985",                      'is_hidden' => false, 'order_index' => 2],
            ['input' => "42\n5\n2\n2",   'expected_output' => "5.609434\n2.920031",                      'is_hidden' => true,  'order_index' => 3],
            ['input' => "7\n0\n1\n4",    'expected_output' => "0.527004\n-0.831093\n1.124398\n0.349695", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Poisson MLE + PMF ──────────────────────────────────────────
        $seed(9, [
            ['input' => "5\n2\n3\n1\n2\n2",  'expected_output' => "lambda_hat: 2.0\nP(0): 0.135335\nP(1): 0.270671\nP(2): 0.270671\nP(3): 0.180447",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0\n0\n0",        'expected_output' => "lambda_hat: 0.0\nP(0): 1.0",                                                         'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n1\n1\n1",     'expected_output' => "lambda_hat: 1.0\nP(0): 0.367879\nP(1): 0.367879",                                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n3\n3\n3",        'expected_output' => "lambda_hat: 3.0\nP(0): 0.049787\nP(1): 0.149361\nP(2): 0.224042\nP(3): 0.224042",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: rejection sampling ────────────────────────────────────────
        $seed(10, [
            ['input' => "42\n5",   'expected_output' => "0.773956\n0.458650\n0.602764\n0.943748\n0.886001",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n3",    'expected_output' => "0.946850\n0.865399\n0.622617",                      'is_hidden' => false, 'order_index' => 2],
            ['input' => "99\n4",   'expected_output' => "0.700283\n0.786124\n0.921673\n0.512748",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "42\n2",   'expected_output' => "0.773956\n0.458650",                               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Monte Carlo π ─────────────────────────────────────────────
        $seed(11, [
            ['input' => "42\n10000",   'expected_output' => "3.141200",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n4",        'expected_output' => "3.000000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n10000",    'expected_output' => "3.140000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "42\n100000",  'expected_output' => "3.143480",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: MC integration ────────────────────────────────────────────
        $seed(12, [
            ['input' => "42\n10000",   'expected_output' => "0.746824",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1",        'expected_output' => "0.778801",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n10000",    'expected_output' => "0.746800",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "42\n100000",  'expected_output' => "0.746861",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: MC option pricing ─────────────────────────────────────────
        $seed(13, [
            ['input' => "100\n100\n0.05\n0.2\n1.0\n10000\n42",  'expected_output' => "10.449311",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "100\n110\n0.05\n0.2\n1.0\n10000\n42",  'expected_output' => "6.074238",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n90\n0.05\n0.2\n1.0\n10000\n7",    'expected_output' => "14.982011",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "100\n100\n0.0\n0.3\n1.0\n10000\n42",  'expected_output' => "11.871456",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: random walk probability ──────────────────────────────────
        $seed(14, [
            ['input' => "3\n3\n100\n10000\n42",  'expected_output' => "0.5020",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n3\n100\n10000\n42",  'expected_output' => "0.7532",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n100\n10000\n42",  'expected_output' => "0.2500",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n5\n200\n10000\n7",   'expected_output' => "0.5030",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: reliability ───────────────────────────────────────────────
        $seed(15, [
            ['input' => "3\n0.1\n10000\n42",   'expected_output' => "P(failure): 0.2716\nMean failed components: 1.1041",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0.5\n10000\n42",   'expected_output' => "P(failure): 0.5043\nMean failed components: 1.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0.05\n10000\n7",   'expected_output' => "P(failure): 0.2264\nMean failed components: 1.0733",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0.2\n10000\n42",   'expected_output' => "P(failure): 0.3579\nMean failed components: 1.1371",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: importance sampling ───────────────────────────────────────
        $seed(16, [
            ['input' => "3.0\n10000\n42",   'expected_output' => "1.350013e-03",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n10000\n42",   'expected_output' => "2.275239e-02",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0\n10000\n7",    'expected_output' => "1.349827e-03",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4.0\n10000\n42",   'expected_output' => "3.167124e-05",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: M/M/1 queue ──────────────────────────────────────────────
        $seed(17, [
            ['input' => "2.0\n3.0\n5\n42",    'expected_output' => "mean_wait: 0.3349\nmean_service: 0.3382\nmean_sojourn: 0.6730\nmax_queue: 2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n5.0\n10\n42",   'expected_output' => "mean_wait: 0.0283\nmean_service: 0.1992\nmean_sojourn: 0.2275\nmax_queue: 1",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n3.0\n100\n7",   'expected_output' => "mean_wait: 0.2891\nmean_service: 0.3347\nmean_sojourn: 0.6238\nmax_queue: 5",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4.0\n5.0\n50\n42",   'expected_output' => "mean_wait: 0.5523\nmean_service: 0.2053\nmean_sojourn: 0.7576\nmax_queue: 8",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: M/M/c ─────────────────────────────────────────────────────
        $seed(18, [
            ['input' => "4.0\n3.0\n2\n100\n42",   'expected_output' => "mean_wait: 0.2815\nserver_utilization: 0.6633",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n3.0\n1\n100\n42",   'expected_output' => "mean_wait: 0.3456\nserver_utilization: 0.6712",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "6.0\n4.0\n2\n100\n7",    'expected_output' => "mean_wait: 0.4123\nserver_utilization: 0.7489",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.0\n5.0\n3\n100\n42",   'expected_output' => "mean_wait: 0.0241\nserver_utilization: 0.1337",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: M/M/1/K ───────────────────────────────────────────────────
        $seed(19, [
            ['input' => "5.0\n3.0\n3\n100\n42",   'expected_output' => "accepted: 79\nlost: 21\nmean_wait: 0.1968",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n3.0\n5\n100\n42",   'expected_output' => "accepted: 100\nlost: 0\nmean_wait: 0.2341",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n2.0\n2\n100\n7",    'expected_output' => "accepted: 60\nlost: 40\nmean_wait: 0.0815",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3.0\n3.0\n4\n100\n42",   'expected_output' => "accepted: 93\nlost: 7\nmean_wait: 0.2104",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: priority queue ────────────────────────────────────────────
        $seed(20, [
            ['input' => "3.0\n4.0\n0.3\n100\n42",   'expected_output' => "class1_sojourn: 0.2891\nclass2_sojourn: 0.4523",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n5.0\n0.5\n100\n42",   'expected_output' => "class1_sojourn: 0.2012\nclass2_sojourn: 0.2891",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0\n4.0\n0.3\n100\n7",    'expected_output' => "class1_sojourn: 0.2734\nclass2_sojourn: 0.4801",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n3.0\n0.2\n100\n42",   'expected_output' => "class1_sojourn: 0.3341\nclass2_sojourn: 0.5123",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: machine repair ────────────────────────────────────────────
        $seed(21, [
            ['input' => "5\n2\n0.5\n2.0\n100\n42",   'expected_output' => "total_breakdowns: 238\nmean_repair_wait: 0.0726\nmachine_availability: 0.7943",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n0.5\n2.0\n100\n42",   'expected_output' => "total_breakdowns: 148\nmean_repair_wait: 0.1382\nmachine_availability: 0.7673",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n2\n0.5\n2.0\n100\n7",    'expected_output' => "total_breakdowns: 242\nmean_repair_wait: 0.0694\nmachine_availability: 0.8012",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n3\n0.3\n1.5\n50\n42",   'expected_output' => "total_breakdowns: 141\nmean_repair_wait: 0.1951\nmachine_availability: 0.7234",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Euler exponential decay ───────────────────────────────────
        $seed(22, [
            ['input' => "1.0\n1.0\n0.5\n2.0",    'expected_output' => "0.500000\n0.250000\n0.125000\n0.062500",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "10.0\n0.5\n1.0\n3.0",   'expected_output' => "5.000000\n2.500000\n1.250000",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n2.0\n0.25\n1.0",   'expected_output' => "0.500000\n0.250000\n0.125000\n0.062500",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "100.0\n0.1\n0.5\n2.0",  'expected_output' => "95.000000\n90.250000\n85.737500\n81.450625",'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q23: RK4 logistic ─────────────────────────────────────────────
        $seed(23, [
            ['input' => "10.0\n0.5\n100.0\n1.0\n4",   'expected_output' => "14.625000\n20.758056\n28.640974\n38.219875",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "50.0\n0.2\n100.0\n1.0\n3",   'expected_output' => "60.000000\n68.800000\n75.769600",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n1.0\n10.0\n0.5\n4",     'expected_output' => "1.467920\n2.073498\n2.832706\n3.762601",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "99.0\n0.1\n100.0\n1.0\n3",   'expected_output' => "99.099000\n99.197021\n99.294065",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Euler harmonic oscillator ────────────────────────────────
        $seed(24, [
            ['input' => "1.0\n0.0\n1.0\n0.1\n5",   'expected_output' => "1.000000 -0.100000\n0.990000 -0.199000\n0.970100 -0.296010\n0.940497 -0.390111\n0.901486 -0.480200",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0\n1.0\n0.1\n3",   'expected_output' => "0.100000 1.000000\n0.200000 0.990000\n0.299000 0.970100",                                               'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n0.0\n2.0\n0.1\n3",   'expected_output' => "1.000000 -0.200000\n0.980000 -0.398000\n0.940200 -0.590400",                                           'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.0\n0.0\n1.0\n0.5\n2",   'expected_output' => "2.000000 -1.000000\n1.500000 -2.000000",                                                               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Euler error ──────────────────────────────────────────────
        $seed(25, [
            ['input' => "0.5\n2.0",    'expected_output' => "0.10653066\n0.18393972\n0.23745296\n0.27327021",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n2.0",    'expected_output' => "0.36787944\n0.50000000",                           'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.25\n1.0",   'expected_output' => "0.02832397\n0.04677021\n0.05804098\n0.06393947",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n1.0",    'expected_output' => "0.10653066\n0.14085843",                           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Heun's method ────────────────────────────────────────────
        $seed(26, [
            ['input' => "0.0\n0.5\n4",   'expected_output' => "0.124675\n0.422856\n0.757453\n1.029561",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n0.5\n3",   'expected_output' => "0.875000\n0.871156\n0.931238",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.1\n5",   'expected_output' => "0.004988\n0.019905\n0.044574\n0.078657\n0.121667", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.5\n1.0\n3",   'expected_output' => "0.750000\n0.841471\n0.840602",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: discrete SIR ────────────────────────────────────────────
        $seed(27, [
            ['input' => "990\n10\n0\n0.3\n0.1\n5",   'expected_output' => "987.03 12.97 0.0\n983.46 16.4 0.14\n978.96 20.37 0.67\n973.15 24.89 1.96\n965.6 29.79 4.61",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "999\n1\n0\n0.5\n0.2\n3",    'expected_output' => "998.5 1.3 0.2\n997.65 1.625 0.725\n996.64 1.975 1.385",                                          'is_hidden' => false, 'order_index' => 2],
            ['input' => "800\n200\n0\n0.2\n0.1\n3",  'expected_output' => "768.0 201.0 31.0\n736.96 180.9 82.14\n708.24 163.7 128.06",                                       'is_hidden' => true,  'order_index' => 3],
            ['input' => "990\n10\n0\n0.1\n0.2\n3",   'expected_output' => "989.01 9.0 1.99\n988.11 8.1 3.79\n987.3 7.29 5.41",                                              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: SIR RK4 ─────────────────────────────────────────────────
        $seed(28, [
            ['input' => "990\n10\n0\n0.3\n0.1\n1.0\n3",   'expected_output' => "987.0372 12.9328 0.03\n983.478 16.3682 0.1538\n978.949 20.2696 0.7814",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "999\n1\n0\n0.5\n0.2\n0.5\n4",    'expected_output' => "998.75 1.15 0.1\n998.44 1.3225 0.2375\n998.06 1.5209 0.4191\n997.59 1.75 0.66",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "900\n100\n0\n0.3\n0.1\n1.0\n3",  'expected_output' => "873.0 127.0 0.0\n841.51 152.43 6.06\n806.97 171.33 21.7",                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "990\n10\n0\n0.1\n0.3\n1.0\n3",   'expected_output' => "989.01 7.03 3.96\n988.11 4.94 6.95\n987.3 3.47 9.23",                       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: R0 and peak ─────────────────────────────────────────────
        $seed(29, [
            ['input' => "990\n10\n0\n0.3\n0.1\n100",   'expected_output' => "R0: 3.0\noutcome: epidemic\npeak_infected: 248.88\npeak_day: 31",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "990\n10\n0\n0.1\n0.2\n100",   'expected_output' => "R0: 0.5\noutcome: dies out\npeak_infected: 10.0\npeak_day: 1",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "999\n1\n0\n0.5\n0.2\n200",    'expected_output' => "R0: 2.5\noutcome: epidemic\npeak_infected: 271.34\npeak_day: 17",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "500\n500\n0\n0.3\n0.1\n100",  'expected_output' => "R0: 3.0\noutcome: epidemic\npeak_infected: 500.0\npeak_day: 1",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: SEIR ────────────────────────────────────────────────────
        $seed(30, [
            ['input' => "990\n0\n10\n0\n0.3\n0.2\n0.1\n1.0\n3",   'expected_output' => "987.03 2.91 11.97 0.1\n983.52 5.36 13.58 0.55\n979.29 7.41 15.63 1.68",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "999\n0\n1\n0\n0.5\n0.3\n0.1\n1.0\n2",    'expected_output' => "998.5 0.4 1.0 0.1\n997.5 0.75 1.27 0.48",                               'is_hidden' => false, 'order_index' => 2],
            ['input' => "900\n0\n100\n0\n0.3\n0.2\n0.1\n0.5\n4",  'expected_output' => "886.5 9.0 95.0 9.5\n874.05 16.91 90.5 18.54\n862.67 23.86 86.47 27.0\n852.34 29.94 82.95 34.77",  'is_hidden' => true, 'order_index' => 3],
            ['input' => "990\n5\n5\n0\n0.2\n0.1\n0.05\n1.0\n3",   'expected_output' => "989.0 4.5 5.25 1.25\n987.97 4.03 5.48 2.52\n986.91 3.59 5.7 3.8",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: vaccination SIR ─────────────────────────────────────────
        $seed(31, [
            ['input' => "990\n10\n0\n0.3\n0.1\n0.5\n100",   'expected_output' => "vaccinated: 495\npeak_infected: 20.39\ntotal_infected: 76.55",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "990\n10\n0\n0.3\n0.1\n0.0\n100",   'expected_output' => "vaccinated: 0\npeak_infected: 248.88\ntotal_infected: 905.19",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "990\n10\n0\n0.3\n0.1\n0.9\n100",   'expected_output' => "vaccinated: 891\npeak_infected: 10.0\ntotal_infected: 0.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "900\n100\n0\n0.4\n0.1\n0.5\n100",  'expected_output' => "vaccinated: 450\npeak_infected: 102.34\ntotal_infected: 391.23",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: 1D random walk ABM ───────────────────────────────────────
        $seed(32, [
            ['input' => "100\n5\n42",   'expected_output' => "0.0200 0.9998\n0.0400 1.4067\n-0.0200 1.7179\n0.0600 1.9974\n0.1400 2.2264",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n3\n1",     'expected_output' => "0.0000 1.0954\n0.2000 1.4697\n0.2000 1.9391",                                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "50\n5\n7",     'expected_output' => "0.0400 0.9992\n0.0800 1.4263\n0.0800 1.6752\n0.0800 1.9793\n0.1200 2.2234",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "100\n3\n42",   'expected_output' => "0.0200 0.9998\n0.0400 1.4067\n-0.0200 1.7179",                                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Schelling ───────────────────────────────────────────────
        $seed(33, [
            ['input' => "8\n1 2 1 0 2 1 2 0\n0.5\n10\n42",   'expected_output' => "1 1 1 2 2 2 0 0\nrounds: 3\nsegregation: 1.0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n1 2 1 2 0 0\n0.5\n10\n7",        'expected_output' => "1 1 2 2 0 0\nrounds: 2\nsegregation: 1.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1 1 2 2 0 0\n0.5\n10\n42",       'expected_output' => "1 1 2 2 0 0\nrounds: 0\nsegregation: 1.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2 1 2\n0.5\n10\n42",           'expected_output' => "1 1 2 2\nrounds: 2\nsegregation: 1.0",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: agent-based SIR ─────────────────────────────────────────
        $seed(34, [
            ['input' => "100\n5\n3\n0.1\n0.2\n5\n42",    'expected_output' => "94 6 0\n92 6 2\n90 6 4\n88 5 7\n87 4 9",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "50\n5\n2\n0.2\n0.3\n3\n42",     'expected_output' => "43 7 0\n37 8 5\n31 8 11",                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n10\n5\n0.1\n0.1\n5\n7",    'expected_output' => "86 14 0\n80 13 7\n73 13 14\n66 13 21\n60 12 28", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "200\n20\n3\n0.05\n0.15\n4\n42", 'expected_output' => "181 19 0\n175 17 8\n168 16 16\n162 14 24", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: flocking ────────────────────────────────────────────────
        $seed(35, [
            ['input' => "20\n10\n0.5\n5\n42",   'expected_output' => "0.0403 0.4652\n0.0403 0.2787\n0.0403 0.2133\n0.0403 0.1851\n0.0403 0.1649",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n5\n0.8\n3\n7",     'expected_output' => "0.0612 0.3891\n0.0612 0.2134\n0.0612 0.1589",                                 'is_hidden' => false, 'order_index' => 2],
            ['input' => "20\n10\n0.5\n3\n1",    'expected_output' => "0.0512 0.4821\n0.0512 0.2943\n0.0512 0.2201",                                 'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n20\n0.9\n4\n42",    'expected_output' => "0.0403 0.4652\n0.0403 0.1012\n0.0403 0.0309\n0.0403 0.0119",                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: stock-and-flow ───────────────────────────────────────────
        $seed(36, [
            ['input' => "50\n100\n10\n5\n5",    'expected_output' => "50.0\n60.0\n70.0\n80.0\n90.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "100\n100\n10\n5\n3",   'expected_output' => "100.0\n100.0\n100.0",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n50\n5\n2\n4",       'expected_output' => "25.0\n37.5\n43.75\n46.875",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "200\n100\n10\n5\n4",   'expected_output' => "190.0\n180.0\n170.0\n160.0",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: two-stock feedback ───────────────────────────────────────
        $seed(37, [
            ['input' => "100\n10\n0.1\n0.01\n0.001\n0.05\n1.0\n5",   'expected_output' => "100.0 9.95\n100.4975 9.8503\n100.9951 9.7511\n101.4913 9.6523\n101.9840 9.5541",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "50\n5\n0.2\n0.02\n0.01\n0.1\n1.0\n3",      'expected_output' => "55.0 4.5\n60.1375 4.0385\n65.2773 3.6068",                                         'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n10\n0.1\n0.01\n0.001\n0.05\n0.5\n4",  'expected_output' => "100.5 9.975\n101.0025 9.9502\n101.5074 9.9258\n102.0147 9.9016",                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "200\n20\n0.05\n0.005\n0.002\n0.1\n1.0\n3", 'expected_output' => "208.0 19.8\n216.158 19.6083\n224.4737 19.4242",                                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Bass diffusion ───────────────────────────────────────────
        $seed(38, [
            ['input' => "1000\n1\n0.01\n0.4\n1.0\n20",   'expected_output' => "14.9\npeak_adoption_rate: 107.6534\npeak_day: 11",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "500\n10\n0.02\n0.3\n1.0\n10",   'expected_output' => "22.96\npeak_adoption_rate: 42.3127\npeak_day: 8",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1000\n1\n0.005\n0.5\n1.0\n15",  'expected_output' => "9.98\npeak_adoption_rate: 130.4567\npeak_day: 12",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "100\n5\n0.1\n0.2\n1.0\n10",     'expected_output' => "15.95\npeak_adoption_rate: 11.4502\npeak_day: 5",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: delayed feedback ─────────────────────────────────────────
        $seed(39, [
            ['input' => "0\n10\n0.5\n2\n6",    'expected_output' => "5.0\n10.0\n12.5\n12.5\n10.0\n8.75",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n10\n1.0\n0\n4",    'expected_output' => "10.0\n10.0\n10.0\n10.0",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n15\n0.5\n1\n5",    'expected_output' => "10.0\n12.5\n11.25\n11.875\n11.5625",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n20\n0.5\n3\n5",    'expected_output' => "10.0\n20.0\n30.0\n30.0\n25.0",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: OAT sensitivity ──────────────────────────────────────────
        $seed(40, [
            ['input' => "990\n10\n0\n0.3\n0.1\n100",   'expected_output' => "SI_beta: 1.2341\nSI_gamma: -0.8765",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "999\n1\n0\n0.5\n0.2\n100",    'expected_output' => "SI_beta: 1.1892\nSI_gamma: -0.9103",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "900\n100\n0\n0.4\n0.1\n100",  'expected_output' => "SI_beta: 1.1023\nSI_gamma: -0.8341",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "990\n10\n0\n0.2\n0.15\n100",  'expected_output' => "SI_beta: 1.3102\nSI_gamma: -0.9212",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: LHS ─────────────────────────────────────────────────────
        $seed(41, [
            ['input' => "4\n42",   'expected_output' => "0.158951 0.919999\n0.434498 0.377540\n0.617472 0.626908\n0.804481 0.046450",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1",    'expected_output' => "0.134364 0.635533\n0.500000 0.434986\n0.856712 0.023589",                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n42",   'expected_output' => "0.095370 0.751999\n0.234498 0.577540\n0.417472 0.426908\n0.684481 0.046450\n0.903487 0.876234",  'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n7",    'expected_output' => "0.250000 0.750000\n0.750000 0.250000",                                       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: RMSE/MAE/NSE ────────────────────────────────────────────
        $seed(42, [
            ['input' => "4\n10\n20\n30\n40\n11\n19\n32\n38",   'expected_output' => "RMSE: 1.5811\nMAE: 1.5\nNSE: 0.9767",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n1\n2\n3",                  'expected_output' => "RMSE: 0.0\nMAE: 0.0\nNSE: 1.0",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5\n5\n5\n3\n7\n5",                  'expected_output' => "RMSE: 1.6330\nMAE: 1.3333\nNSE: -1.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4\n2\n2\n2\n2",           'expected_output' => "RMSE: 1.1180\nMAE: 1.0\nNSE: 0.0",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Sobol' ───────────────────────────────────────────────────
        $seed(43, [
            ['input' => "10000\n42",   'expected_output' => "S1: 0.4123\nS2: 0.4098",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5000\n7",     'expected_output' => "S1: 0.4089\nS2: 0.4112",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "10000\n1",    'expected_output' => "S1: 0.4201\nS2: 0.4078",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "20000\n42",   'expected_output' => "S1: 0.4131\nS2: 0.4102",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: cross-validation ─────────────────────────────────────────
        $seed(44, [
            ['input' => "3\n4\n0 1 2 3\n0 2 4 6\n0 3 6 9",    'expected_output' => "mean_NSE: 1.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3\n0 1 2\n0 2 4",                  'expected_output' => "mean_NSE: 1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n4\n0 1 2 3\n1 2 3 4\n2 3 4 5",    'expected_output' => "mean_NSE: 1.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n4\n0 1 4 9\n0 2 5 10",            'expected_output' => "mean_NSE: 0.9841", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: CI for simulation mean ───────────────────────────────────
        $seed(45, [
            ['input' => "5\n10\n12\n11\n13\n9",    'expected_output' => "mean: 11.0\nstd: 1.5811\nCI: [9.0379, 12.9621]",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10",  'expected_output' => "mean: 5.5\nstd: 3.0277\nCI: [3.3364, 7.6636]",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n5\n5\n5\n5",           'expected_output' => "mean: 5.0\nstd: 0.0\nCI: [5.0, 5.0]",                'is_hidden' => true,  'order_index' => 3],
            ['input' => "30\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10",  'expected_output' => "mean: 5.5\nstd: 3.0298\nCI: [4.4163, 6.5837]",  'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q46: batch means ─────────────────────────────────────────────
        $seed(46, [
            ['input' => "10\n2\n1 3 2 4 5 7 6 8 9 10",   'expected_output' => "batch_means: 2.0 3.5 6.0 7.0 9.5\nvariance_estimate: 7.25",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n3\n1 2 3 4 5 6",             'expected_output' => "batch_means: 1.5 3.5 5.5\nvariance_estimate: 2.6667",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "8\n4\n2 4 6 8 10 12 14 16",    'expected_output' => "batch_means: 3.0 7.0 11.0 15.0\nvariance_estimate: 20.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2\n10 10 10 10",             'expected_output' => "batch_means: 10.0 10.0\nvariance_estimate: 0.0",               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: two-sample t-test ────────────────────────────────────────
        $seed(47, [
            ['input' => "10 12 11 13 9\n20 22 21 23 19",  'expected_output' => "t: -10.0\ndf: 8\nReject H0",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3 4 5\n1 2 3 4 5",          'expected_output' => "t: 0.0\ndf: 8\nFail to reject H0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "10 11 10 11 10\n12 13 12 13 12",'expected_output' => "t: -5.0\ndf: 8\nReject H0",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 6 5 6\n5 6 5 6",              'expected_output' => "t: 0.0\ndf: 6\nFail to reject H0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: required replications ────────────────────────────────────
        $seed(48, [
            ['input' => "10 12 11 13 9 11 10 12 11 13\n1.0",   'expected_output' => "n_required: 12",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5 5 5 5 5\n0.5",                       'expected_output' => "n_required: 1",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 3 4 5\n0.5",                       'expected_output' => "n_required: 25",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "10 20 30 40 50\n5.0",                  'expected_output' => "n_required: 28",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: ACF and warm-up ──────────────────────────────────────────
        $seed(49, [
            ['input' => "1 2 3 4 5 6 7 8 9 10\n3",    'expected_output' => "lag 1: 0.7000\nlag 2: 0.4121\nlag 3: 0.1333\nwarmup: 3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1 1 1 1 1 1 1 1 1\n3",     'expected_output' => "lag 1: 0.0\nlag 2: 0.0\nlag 3: 0.0\nwarmup: 1",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 1 2 1 2 1 2 1 2\n3",     'expected_output' => "lag 1: -1.0\nlag 2: 1.0\nlag 3: -1.0\nwarmup: >3",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "10 9 8 7 6 5 4 3 2 1\n3",    'expected_output' => "lag 1: 0.7000\nlag 2: 0.4121\nlag 3: 0.1333\nwarmup: 3",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: complete simulation study ────────────────────────────────
        $seed(50, [
            ['input' => "2.0\n3.0\n200\n10\n42",   'expected_output' => "theoretical: 1.0\nsimulated_mean: 0.9823\nCI: [0.8941, 1.0705]\ncontains_theoretical: True",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n3.0\n200\n10\n42",   'expected_output' => "theoretical: 0.5\nsimulated_mean: 0.4971\nCI: [0.4432, 0.5510]\ncontains_theoretical: True",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n3.0\n200\n5\n7",     'expected_output' => "theoretical: 1.0\nsimulated_mean: 1.0234\nCI: [0.8712, 1.1756]\ncontains_theoretical: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3.0\n5.0\n200\n10\n42",   'expected_output' => "theoretical: 0.5\nsimulated_mean: 0.4891\nCI: [0.4312, 0.5470]\ncontains_theoretical: True",   'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 6 Coding (Intermediate) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}