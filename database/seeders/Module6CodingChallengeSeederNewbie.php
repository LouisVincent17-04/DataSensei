<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 6 — Introduction to Modeling & Simulation (Newbie / Level 1) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering beginner Modeling & Simulation concepts
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mapped to lessons 327–336):
 *   L6.1  Introduction to Modeling & Simulation
 *   L6.2  Random Numbers & Probability Distributions
 *   L6.3  Monte Carlo Methods
 *   L6.4  Discrete-Event Simulation
 *   L6.5  Differential Equations & Continuous Simulation
 *   L6.6  The SIR Epidemic Model
 *   L6.7  Agent-Based Modeling
 *   L6.8  System Dynamics & Feedback Loops
 *   L6.9  Sensitivity Analysis & Model Validation
 *   L6.10 Simulation Output Analysis & Statistical Testing
 *
 * Difficulty: Newbie — all problems solvable with pure Python, no third-party
 * libraries required. Learners build intuition for the core computations that
 * simulation frameworks perform under the hood.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module6CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 6 — Introduction to Modeling & Simulation (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Introduction to Modeling & Simulation',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Explore the fundamentals of Modeling & Simulation using pure Python. Compute probability distributions, run basic Monte Carlo estimates, simulate simple queues, step through Euler\'s method, track SIR model states, and analyse simulation output — all from scratch.',
                'time_limit_seconds' => 1200,
                'base_xp'            => 800,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Introduction to Modeling & Simulation (Q1–Q4) → L327
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
A simple **linear model** predicts output as `y = m * x + b`. Read `m`, `b`, and `n` input values (one per line after the first two). Print the predicted output for each input value rounded to 2 decimal places, one per line.

Example:
```
Input:
2
3
3
0
1
2
Output:
3.00
5.00
7.00
```
MD,
                'starter_code'        => "m = float(input())\nb = float(input())\nn = int(input())\nxs = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
A model has `n` parameters. Read `n` parameter names and their values (format: `name value` per line). Print each parameter and its value, one per line, sorted alphabetically by name. Format: `name: value`

Example:
```
Input:
3
rate 0.5
capacity 100
threshold 10
Output:
capacity: 100.0
rate: 0.5
threshold: 10.0
```
MD,
                'starter_code'        => "n = int(input())\nparams = {}\nfor _ in range(n):\n    name, val = input().split()\n    params[name] = float(val)\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Simulate a **simple counter model**. Read an initial value `v`, a step size `s`, and number of steps `n`. At each step add `s` to `v`. Print the value after each step, rounded to 2 decimal places, one per line.

Example:
```
Input:
0
5
4
Output:
5.00
10.00
15.00
20.00
```
MD,
                'starter_code'        => "v = float(input())\ns = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Compute the **absolute error** and **relative error** between a simulated value and the true value. Read `simulated` and `true_val` (one per line). Print:
```
absolute_error: <value>
relative_error: <value>
```
Both rounded to 4 decimal places. Relative error = |simulated − true| / |true|. If true = 0, print `undefined` for relative error.

Example:
```
Input:
9.8
10.0
Output:
absolute_error: 0.2000
relative_error: 0.0200
```
MD,
                'starter_code'        => "simulated = float(input())\ntrue_val = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Random Numbers & Probability Distributions (Q5–Q9) → L328
            // ═══════════════════════════════════════════════════════════════

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read `n` numbers drawn from a distribution. Print the **sample mean** and **sample variance** (ddof=1), each rounded to 4 decimal places on separate lines.

Example:
```
Input:
4
2
4
4
6
Output:
mean: 4.0000
variance: 2.6667
```
MD,
                'starter_code'        => "n = int(input())\nsamples = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Compute the **probability mass function** of a discrete uniform distribution over integers from `a` to `b` inclusive. Read `a` and `b`. Print P(X = k) for each k from a to b, rounded to 4 decimal places. Format: `k: p`

Example:
```
Input:
1
4
Output:
1: 0.2500
2: 0.2500
3: 0.2500
4: 0.2500
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Compute the **CDF** of a discrete uniform distribution over integers from `a` to `b` inclusive. Read `a`, `b`, and query value `k`. Print P(X ≤ k) rounded to 4 decimal places.

Example:
```
Input:
1
6
3
Output: 0.5000
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Compute the **Poisson probability** P(X = k) given mean `lambda`. Formula:

P(X = k) = (e^(−λ) × λ^k) / k!

Read `lambda` and `k`. Print P(X = k) rounded to 4 decimal places.

Example:
```
Input:
3
2
Output: 0.2240
```
MD,
                'starter_code'        => "import math\nlambda_ = float(input())\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Compute the **exponential distribution PDF** at value `x` given rate `lambda`.

PDF(x) = lambda × e^(−lambda × x)  for x ≥ 0

Read `lambda` and `x`. Print the PDF value rounded to 4 decimal places.

Example:
```
Input:
2
1
Output: 0.2707
```
MD,
                'starter_code'        => "import math\nlambda_ = float(input())\nx = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Monte Carlo Methods (Q10–Q14) → L329
            // ═══════════════════════════════════════════════════════════════

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
**Monte Carlo π estimate**: Read `n` pairs of (x, y) coordinates (each between 0 and 1). Count how many fall inside the unit circle (x² + y² ≤ 1). Print the estimate of π = 4 × (inside / n) rounded to 4 decimal places.

Example:
```
Input:
4
0.1 0.2
0.9 0.9
0.5 0.5
0.3 0.4
Output: 3.0000
```
MD,
                'starter_code'        => "n = int(input())\npoints = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
**Monte Carlo integration**: Estimate ∫[0,1] f(x) dx using the sample mean method. Read `n` (x, f(x)) pairs. Print the estimate (mean of f(x) values) rounded to 4 decimal places.

Example:
```
Input:
4
0.1 0.01
0.4 0.16
0.7 0.49
0.9 0.81
Output: 0.3675
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
**Expected value estimation**: Read `n` (value, probability) pairs where probabilities sum to 1. Compute the expected value E[X] = Σ(value × probability). Print rounded to 4 decimal places.

Example:
```
Input:
3
1 0.2
2 0.5
3 0.3
Output: 2.1000
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
**Buffon's needle**: Read `n` results (1 if needle crosses a line, 0 if not) and the ratio `l/d` (needle length / line spacing). Estimate π using:

π ≈ (2 × l/d × n) / crossings

Print π estimate rounded to 4 decimal places.

Example:
```
Input:
4
1
0
1
1
0.5
Output: 2.6667
```
MD,
                'starter_code'        => "n = int(input())\nresults = [int(input()) for _ in range(n)]\nratio = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
**Monte Carlo risk**: Read `n` profit values from a simulation. Print the **Value at Risk (VaR)** at the 5th percentile (i.e., the value such that 5% of outcomes are worse). Use the sorted array: VaR = value at index floor(0.05 × n).

Print the VaR value rounded to 2 decimal places.

Example:
```
Input:
10
-50
-30
-20
-10
0
10
20
30
40
50
Output: -30.00
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Discrete-Event Simulation (Q15–Q19) → L330
            // ═══════════════════════════════════════════════════════════════

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Simulate a **single-server queue** (FIFO). Read `n` customers with arrival time and service time (format: `arrival service` per line, sorted by arrival). Process each customer: if server is free, service starts immediately; otherwise customer waits. Print the **waiting time** for each customer, one per line.

Example:
```
Input:
3
0 3
2 2
4 1
Output:
0
1
0
```
MD,
                'starter_code'        => "n = int(input())\ncustomers = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
For a single-server queue simulation, read `n` customers (format: `arrival service`). Compute and print the **average waiting time** rounded to 2 decimal places and the **average system time** (wait + service) rounded to 2 decimal places.

Example:
```
Input:
3
0 3
2 2
4 1
Output:
avg_wait: 0.33
avg_system: 2.33
```
MD,
                'starter_code'        => "n = int(input())\ncustomers = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Simulate a **finite buffer queue**. The buffer can hold at most `B` customers (including the one in service). Read `B` and `n` customers (format: `arrival service`). If a customer arrives when the buffer is full, they are **rejected**. Print the number of rejected customers.

Example:
```
Input:
2
4
0 5
1 5
2 5
3 5
Output: 2
```
MD,
                'starter_code'        => "B = int(input())\nn = int(input())\ncustomers = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Compute the **server utilisation** for a single-server queue. Utilisation = total busy time / total simulation time (time from first arrival to last departure). Read `n` customers (format: `arrival service`). Print utilisation rounded to 4 decimal places.

Example:
```
Input:
3
0 3
2 2
6 2
Output: 0.8571
```
MD,
                'starter_code'        => "n = int(input())\ncustomers = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Simulate an **event list** processor. Read `n` events (format: `time type`). Process events in time order (ties broken alphabetically by type). Print each event as it is processed: `time: type`

Example:
```
Input:
4
3 arrive
1 depart
2 arrive
1 arrive
Output:
1: arrive
1: depart
2: arrive
3: arrive
```
MD,
                'starter_code'        => "n = int(input())\nevents = []\nfor _ in range(n):\n    parts = input().split()\n    events.append((float(parts[0]), parts[1]))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Differential Equations & Continuous Simulation (Q20–Q24) → L331
            // ═══════════════════════════════════════════════════════════════

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Apply **Euler's method** for dy/dt = f(t, y). Read initial value `y0`, step size `h`, number of steps `n`, and rate constant `r` (so f(t, y) = r × y). Print y after each step rounded to 4 decimal places, one per line.

Example:
```
Input:
1.0
0.1
4
0.5
Output:
1.0500
1.1025
1.1576
1.2155
```
MD,
                'starter_code'        => "y = float(input())\nh = float(input())\nn = int(input())\nr = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Solve **exponential decay** using Euler's method. dy/dt = −lambda × y. Read `y0`, `lambda`, step size `h`, and number of steps `n`. Print y after each step rounded to 4 decimal places.

Example:
```
Input:
100.0
0.1
1.0
5
Output:
90.0000
81.0000
72.9000
65.6100
59.0490
```
MD,
                'starter_code'        => "y = float(input())\nlambda_ = float(input())\nh = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Compute the **analytical solution** of exponential growth y(t) = y0 × e^(r×t). Read `y0`, `r`, and `n` time values (one per line after the first two). Print y(t) for each time value rounded to 4 decimal places.

Example:
```
Input:
1.0
0.5
3
0
1
2
Output:
1.0000
1.6487
2.7183
```
MD,
                'starter_code'        => "import math\ny0 = float(input())\nr = float(input())\nn = int(input())\ntimes = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Compute the **global truncation error** of Euler's method for dy/dt = r × y. Read `y0`, `r`, step size `h`, final time `T`. Compare Euler's final value against the analytical y(T) = y0 × e^(r×T). Print:
```
euler: <value>
exact: <value>
error: <value>
```
All rounded to 4 decimal places. Number of steps = T / h (integer).

Example:
```
Input:
1.0
1.0
0.5
2.0
Output:
euler: 2.2500
exact: 7.3891
error: 5.1391
```
MD,
                'starter_code'        => "import math\ny0 = float(input())\nr = float(input())\nh = float(input())\nT = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Simulate **logistic growth** using Euler's method. dy/dt = r × y × (1 − y/K). Read `y0`, `r`, `K` (carrying capacity), step size `h`, and steps `n`. Print y after each step rounded to 4 decimal places.

Example:
```
Input:
10.0
0.5
100.0
1.0
4
Output:
14.5000
20.6275
28.6869
38.7660
```
MD,
                'starter_code'        => "y = float(input())\nr = float(input())\nK = float(input())\nh = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: The SIR Epidemic Model (Q25–Q29) → L332
            // ═══════════════════════════════════════════════════════════════

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Simulate **one step of the SIR model** using Euler's method.

dS/dt = −beta × S × I / N
dI/dt = beta × S × I / N − gamma × I
dR/dt = gamma × I

Read `S`, `I`, `R`, `beta`, `gamma`, and step size `h`. Print new S, I, R rounded to 4 decimal places, one per line.

Example:
```
Input:
990
10
0
0.3
0.1
1
Output:
S: 987.0300
I: 12.0000
R: 0.9700
```
MD,
                'starter_code'        => "S = float(input())\nI = float(input())\nR = float(input())\nbeta = float(input())\ngamma = float(input())\nh = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Run the SIR model for `n` steps. Read `S0`, `I0`, `R0`, `beta`, `gamma`, step size `h`, and `n`. Print S, I, R after every step rounded to 2 decimal places on one line separated by spaces.

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
987.03 12.00 0.97
983.77 14.12 2.11
980.16 16.34 3.50
```
MD,
                'starter_code'        => "S = float(input())\nI = float(input())\nR = float(input())\nbeta = float(input())\ngamma = float(input())\nh = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Compute the **basic reproduction number** R₀ for the SIR model. R₀ = beta / gamma. Read `beta` and `gamma`. Print R₀ rounded to 4 decimal places and whether an epidemic occurs (`epidemic` if R₀ > 1, otherwise `no epidemic`).

Example:
```
Input:
0.3
0.1
Output:
R0: 3.0000
epidemic
```
MD,
                'starter_code'        => "beta = float(input())\ngamma = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Find the **peak infected** count in an SIR simulation. Run the model for `n` steps and print the maximum I value (rounded to 2 decimal places) and the step at which it occurs (1-indexed).

Read `S0`, `I0`, `R0`, `beta`, `gamma`, `h`, `n`.

Example:
```
Input:
990
10
0
0.3
0.1
1
20
Output:
peak_I: 271.15
peak_step: 17
```
MD,
                'starter_code'        => "S = float(input())\nI = float(input())\nR = float(input())\nbeta = float(input())\ngamma = float(input())\nh = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Compute the **herd immunity threshold**: p = 1 − 1/R₀ where R₀ = beta/gamma. Read `beta` and `gamma`. Print p as a percentage rounded to 2 decimal places.

Example:
```
Input:
0.3
0.1
Output: 66.67%
```
MD,
                'starter_code'        => "beta = float(input())\ngamma = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Agent-Based Modeling (Q30–Q33) → L333
            // ═══════════════════════════════════════════════════════════════

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Simulate **agent movement** on a 1D number line. Read `n` agents with their positions and velocities (format: `position velocity` per line). After `t` time steps, print the final position of each agent rounded to 2 decimal places.

Example:
```
Input:
3
0 1
5 -1
10 2
3
Output:
3.00
2.00
16.00
```
MD,
                'starter_code'        => "n = int(input())\nagents = [tuple(map(float, input().split())) for _ in range(n)]\nt = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Simulate a **susceptible/infected** agent system. Read `n` agents each with a state (S or I) and a position (integer). At each step, any S agent adjacent (distance ≤ 1) to an I agent becomes I. Read the number of steps and print the count of I agents after all steps.

Example:
```
Input:
5
S 0
S 1
I 5
S 6
S 10
2
Output: 3
```
MD,
                'starter_code'        => "n = int(input())\nagents = []\nfor _ in range(n):\n    parts = input().split()\n    agents.append([parts[0], int(parts[1])])\nsteps = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Count the **neighbours** of each agent within a given radius `r` (using Euclidean distance on a 2D grid). Read `n` agents (format: `x y` per line) and `r`. Print the neighbour count for each agent, one per line.

Example:
```
Input:
4
0 0
1 0
0 1
5 5
1.5
Output:
2
2
2
0
```
MD,
                'starter_code'        => "import math\nn = int(input())\nagents = [tuple(map(float, input().split())) for _ in range(n)]\nr = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Simulate a **simple voting model**. Each agent has a state (0 or 1). At each step, every agent adopts the **majority state** among itself and its two neighbours (circular list). Ties keep the current state. Read `n` states and number of steps. Print the final states space-separated.

Example:
```
Input:
5
1 0 0 0 1
3
Output: 0 0 0 0 0
```
MD,
                'starter_code'        => "states = list(map(int, input().split()))\nn = len(states)\nsteps = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: System Dynamics & Feedback Loops (Q34–Q37) → L334
            // ═══════════════════════════════════════════════════════════════

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Simulate a **stock-and-flow** model. A stock `S` has inflow rate `in_rate` and outflow rate `out_rate` per step. Read `S0`, `in_rate`, `out_rate`, and `n` steps. Print S after each step rounded to 2 decimal places. Ensure S never goes below 0.

Example:
```
Input:
100
10
5
4
Output:
105.00
110.00
115.00
120.00
```
MD,
                'starter_code'        => "S = float(input())\nin_rate = float(input())\nout_rate = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Simulate a **negative feedback loop** (thermostat model). A temperature T approaches a target temp `T_target` at rate `k` per step: ΔT = k × (T_target − T). Read `T0`, `T_target`, `k`, and `n` steps. Print T after each step rounded to 4 decimal places.

Example:
```
Input:
20
100
0.1
5
Output:
28.0000
35.2000
41.6800
47.5120
52.7608
```
MD,
                'starter_code'        => "T = float(input())\nT_target = float(input())\nk = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Simulate **predator-prey** (Lotka-Volterra) for one Euler step.

dX/dt = alpha × X − beta × X × Y
dY/dt = delta × X × Y − gamma × Y

Read `X`, `Y`, `alpha`, `beta`, `delta`, `gamma`, and step `h`. Print new X and Y rounded to 4 decimal places.

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
Output:
X: 39.2800
Y: 8.6760
```
MD,
                'starter_code'        => "X = float(input())\nY = float(input())\nalpha = float(input())\nbeta = float(input())\ndelta = float(input())\ngamma = float(input())\nh = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Simulate a **positive feedback loop** (compound interest). A value V grows at fractional rate `r` per step: V(t+1) = V(t) × (1 + r). Read `V0`, `r`, and `n` steps. Print V after each step rounded to 2 decimal places.

Example:
```
Input:
1000
0.05
4
Output:
1050.00
1102.50
1157.63
1215.51
```
MD,
                'starter_code'        => "V = float(input())\nr = float(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Sensitivity Analysis & Model Validation (Q38–Q41) → L335
            // ═══════════════════════════════════════════════════════════════

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Compute a **one-way sensitivity**: vary one parameter and record model outputs. Read a base output `y_base`, `n` parameter values, and corresponding model outputs (format: `param output` per line). Print the **sensitivity index** = (max_output − min_output) / y_base rounded to 4 decimal places.

Example:
```
Input:
10.0
3
1.0 8.0
2.0 10.0
3.0 14.0
Output: 0.6000
```
MD,
                'starter_code'        => "y_base = float(input())\nn = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Compute the **Root Mean Square Error (RMSE)** between simulated and observed values. Read `n` pairs (format: `simulated observed` per line). Print RMSE rounded to 4 decimal places.

Example:
```
Input:
4
10 9
20 22
15 14
30 31
Output: 1.2247
```
MD,
                'starter_code'        => "import math\nn = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Perform **model calibration** by finding the parameter value from a discrete set that minimises RMSE against observed data. Read `n` observed values, then `k` candidate parameter sets (format: first line `k`, then `k` lines each with `param_value` followed by `n` model outputs). Print the best parameter value and its RMSE rounded to 4 decimal places.

Example:
```
Input:
3
1 2 3
2
0.5 1.1 2.1 3.1
1.0 1.0 2.0 3.0
Output:
best_param: 1.0
rmse: 0.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nobserved = list(map(float, input().split()))\nk = int(input())\ncandidates = []\nfor _ in range(k):\n    vals = list(map(float, input().split()))\n    candidates.append((vals[0], vals[1:]))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Compute the **Nash-Sutcliffe efficiency** (NSE) to validate a model:

NSE = 1 − Σ(obs − sim)² / Σ(obs − mean_obs)²

NSE = 1 is perfect, NSE < 0 means the mean is a better predictor. Read `n` (observed, simulated) pairs. Print NSE rounded to 4 decimal places. If denominator = 0, print `1.0000`.

Example:
```
Input:
4
10 10
20 20
15 15
30 30
Output: 1.0000
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Simulation Output Analysis (Q42–Q50) → L336
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Compute the **sample mean** and **95% confidence interval** for simulation output. Read `n` run results. CI = mean ± 1.96 × (std / sqrt(n)) where std is sample std (ddof=1). Print:
```
mean: <value>
ci_lower: <value>
ci_upper: <value>
```
All rounded to 4 decimal places.

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
mean: 11.0000
ci_lower: 9.2843
ci_upper: 12.7157
```
MD,
                'starter_code'        => "import math\nn = int(input())\nresults = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Compute the **warm-up period** removal. Read `n` observations and a warm-up period `w`. Discard the first `w` observations and print the mean of the remaining values rounded to 4 decimal places.

Example:
```
Input:
8
1
2
3
10
11
12
13
14
3
Output: 12.0000
```
MD,
                'starter_code'        => "n = int(input())\nobs = [float(input()) for _ in range(n)]\nw = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Compute the **batch means** from a simulation run. Read `n` observations and batch size `b` (ignore any remaining observations that don't form a complete batch). Print the mean of each batch rounded to 4 decimal places, one per line.

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
Output:
11.0000
21.0000
```
MD,
                'starter_code'        => "n = int(input())\nobs = [float(input()) for _ in range(n)]\nb = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Compute a **two-sample t-test statistic** to compare two simulation configurations (equal variances, pooled). Read `nA` values for system A, then `nB` values for system B. Print the t-statistic rounded to 4 decimal places.

t = (mean_A − mean_B) / (sp × sqrt(1/nA + 1/nB))
sp = sqrt(((nA-1)×sA² + (nB-1)×sB²) / (nA+nB−2))  (sample variance)

Example:
```
Input:
3
10
12
11
3
20
22
21
Output: -10.0000
```
MD,
                'starter_code'        => "import math\nnA = int(input())\nA = [float(input()) for _ in range(nA)]\nnB = int(input())\nB = [float(input()) for _ in range(nB)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Compute the **coefficient of variation (CV)** = std / mean (population std). Read `n` simulation run results. Print CV rounded to 4 decimal places.

Example:
```
Input:
5
10
12
11
13
9
Output: 0.1284
```
MD,
                'starter_code'        => "import math\nn = int(input())\nresults = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Compute the **steady-state mean** by running a simulation until the rolling mean changes by less than tolerance `tol`. Read `n` observations and `tol`. Print the step at which steady state is reached and the steady-state mean rounded to 4 decimal places.

Steady state is reached when |mean(obs[:t+1]) − mean(obs[:t])| < tol for t ≥ 2.

Example:
```
Input:
6
10
12
11
10
11
10
0.5
Output:
step: 3
mean: 11.0000
```
MD,
                'starter_code'        => "n = int(input())\nobs = [float(input()) for _ in range(n)]\ntol = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Compute the **relative half-width** of a confidence interval. Read `n` simulation results. half_width = 1.96 × (sample_std / sqrt(n)), relative = half_width / mean. Print relative half-width rounded to 4 decimal places.

Example:
```
Input:
5
10
12
11
13
9
Output: 0.1568
```
MD,
                'starter_code'        => "import math\nn = int(input())\nresults = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Determine the **minimum number of replications** needed so that the relative half-width of the CI is below target `epsilon`. Use the pilot sample formula:

n_min = ceil((1.96 × cv / epsilon)²)

where cv = sample_std / sample_mean (ddof=1). Read `n` pilot samples and `epsilon`. Print n_min.

Example:
```
Input:
5
10
12
11
13
9
0.1
Output: 7
```
MD,
                'starter_code'        => "import math\nn = int(input())\npilot = [float(input()) for _ in range(n)]\nepsilon = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Compare two simulation systems using **paired t-test**. Read `n` paired differences (d_i = A_i − B_i). Compute t = mean_d / (std_d / sqrt(n)) where std_d is sample std (ddof=1). Print t rounded to 4 decimal places and `significant` if |t| > 2.0 else `not significant`.

Example:
```
Input:
4
-10
-12
-9
-11
Output:
t: -20.6777
significant
```
MD,
                'starter_code'        => "import math\nn = int(input())\ndiffs = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
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

        // ── Q1: Linear model ──────────────────────────────────────────────
        $seed(1, [
            ['input' => "2\n3\n3\n0\n1\n2",               'expected_output' => "3.00\n5.00\n7.00",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n1\n2\n0\n4",                'expected_output' => "1.00\n3.00",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1\n10\n3\n0\n5\n10",            'expected_output' => "10.00\n5.00\n0.00",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0\n1\n7",                     'expected_output' => "21.00",                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Parameter table ───────────────────────────────────────────
        $seed(2, [
            ['input' => "3\nrate 0.5\ncapacity 100\nthreshold 10",   'expected_output' => "capacity: 100.0\nrate: 0.5\nthreshold: 10.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nbeta 0.3\ngamma 0.1",                    'expected_output' => "beta: 0.3\ngamma: 0.1",                         'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nz 99",                                    'expected_output' => "z: 99.0",                                       'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nc 3\na 1\nb 2",                          'expected_output' => "a: 1.0\nb: 2.0\nc: 3.0",                        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Counter model ─────────────────────────────────────────────
        $seed(3, [
            ['input' => "0\n5\n4",    'expected_output' => "5.00\n10.00\n15.00\n20.00",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n-2\n3",  'expected_output' => "8.00\n6.00\n4.00",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0.5\n4",  'expected_output' => "0.50\n1.00\n1.50\n2.00",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "100\n10\n2", 'expected_output' => "110.00\n120.00",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Absolute & relative error ─────────────────────────────────
        $seed(4, [
            ['input' => "9.8\n10.0",   'expected_output' => "absolute_error: 0.2000\nrelative_error: 0.0200",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "5.0\n5.0",    'expected_output' => "absolute_error: 0.0000\nrelative_error: 0.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.0",    'expected_output' => "absolute_error: 0.0000\nrelative_error: undefined", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "12.0\n10.0",  'expected_output' => "absolute_error: 2.0000\nrelative_error: 0.2000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Sample mean & variance ────────────────────────────────────
        $seed(5, [
            ['input' => "4\n2\n4\n4\n6",      'expected_output' => "mean: 4.0000\nvariance: 2.6667",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",         'expected_output' => "mean: 2.0000\nvariance: 1.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n5\n5",            'expected_output' => "mean: 5.0000\nvariance: 0.0000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1\n2\n3\n4\n5",   'expected_output' => "mean: 3.0000\nvariance: 2.5000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Discrete uniform PMF ──────────────────────────────────────
        $seed(6, [
            ['input' => "1\n4",   'expected_output' => "1: 0.2500\n2: 0.2500\n3: 0.2500\n4: 0.2500",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2",   'expected_output' => "1: 0.5000\n2: 0.5000",                         'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n4",   'expected_output' => "0: 0.2000\n1: 0.2000\n2: 0.2000\n3: 0.2000\n4: 0.2000",  'is_hidden' => true, 'order_index' => 3],
            ['input' => "5\n5",   'expected_output' => "5: 1.0000",                                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Discrete uniform CDF ──────────────────────────────────────
        $seed(7, [
            ['input' => "1\n6\n3",   'expected_output' => '0.5000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n4\n4",   'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n9\n4",   'expected_output' => '0.5000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n10\n1",  'expected_output' => '0.1000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Poisson PMF ───────────────────────────────────────────────
        $seed(8, [
            ['input' => "3\n2",   'expected_output' => '0.2240',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0",   'expected_output' => '0.3679',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5",   'expected_output' => '0.1755',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0",   'expected_output' => '0.1353',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Exponential PDF ───────────────────────────────────────────
        $seed(9, [
            ['input' => "2\n1",    'expected_output' => '0.2707',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0",    'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n2",  'expected_output' => '0.1839',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0",    'expected_output' => '3.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Monte Carlo π ────────────────────────────────────────────
        $seed(10, [
            ['input' => "4\n0.1 0.2\n0.9 0.9\n0.5 0.5\n0.3 0.4",   'expected_output' => '3.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0.1 0.1",                                'expected_output' => '4.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0.9 0.9\n0.8 0.8\n0.7 0.7\n0.6 0.6",   'expected_output' => '0.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0.0 0.0\n0.5 0.0\n0.0 0.5",             'expected_output' => '4.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: MC integration ───────────────────────────────────────────
        $seed(11, [
            ['input' => "4\n0.1 0.01\n0.4 0.16\n0.7 0.49\n0.9 0.81",  'expected_output' => '0.3675',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0 0.0\n1.0 1.0",                         'expected_output' => '0.5000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.0 1.0\n0.5 1.0\n1.0 1.0",               'expected_output' => '1.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0.5 0.25",                                  'expected_output' => '0.2500',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Expected value ───────────────────────────────────────────
        $seed(12, [
            ['input' => "3\n1 0.2\n2 0.5\n3 0.3",    'expected_output' => '2.1000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0.5\n10 0.5",           'expected_output' => '5.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n7 1.0",                   'expected_output' => '7.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 0.25\n2 0.25\n3 0.25\n4 0.25", 'expected_output' => '2.5000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q13: Buffon's needle ──────────────────────────────────────────
        $seed(13, [
            ['input' => "4\n1\n0\n1\n1\n0.5",     'expected_output' => '2.6667',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n1\n1\n1\n0.5",     'expected_output' => '4.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n1\n1.0",            'expected_output' => '4.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1\n1\n0\n1\n1\n1\n0.5", 'expected_output' => '2.4000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q14: VaR ─────────────────────────────────────────────────────
        $seed(14, [
            ['input' => "10\n-50\n-30\n-20\n-10\n0\n10\n20\n30\n40\n50",  'expected_output' => '-30.00',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n-10\n-5\n5\n10",                               'expected_output' => '-10.00',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "20\n-100\n-90\n-80\n-70\n-60\n-50\n-40\n-30\n-20\n-10\n0\n10\n20\n30\n40\n50\n60\n70\n80\n90", 'expected_output' => '-90.00', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5\n-20\n-10\n0\n10\n20",                          'expected_output' => '-20.00',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Queue waiting times ──────────────────────────────────────
        $seed(15, [
            ['input' => "3\n0 3\n2 2\n4 1",      'expected_output' => "0\n1\n0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 5\n3 2",           'expected_output' => "0\n2",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 2\n1 2\n2 2\n3 2", 'expected_output' => "0\n1\n2\n3", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n0 1\n5 1\n10 1",     'expected_output' => "0\n0\n0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Avg wait & system time ───────────────────────────────────
        $seed(16, [
            ['input' => "3\n0 3\n2 2\n4 1",     'expected_output' => "avg_wait: 0.33\navg_system: 2.33",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 5\n3 2",          'expected_output' => "avg_wait: 1.00\navg_system: 4.00",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0 1\n5 1\n10 1",    'expected_output' => "avg_wait: 0.00\navg_system: 1.00",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0 10",              'expected_output' => "avg_wait: 0.00\navg_system: 10.00",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Finite buffer ────────────────────────────────────────────
        $seed(17, [
            ['input' => "2\n4\n0 5\n1 5\n2 5\n3 5",     'expected_output' => '2',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n3\n0 2\n1 2\n3 2",          'expected_output' => '0',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n3\n0 10\n1 2\n2 2",         'expected_output' => '2',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n4\n0 5\n1 1\n3 5\n6 1",     'expected_output' => '0',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Server utilisation ───────────────────────────────────────
        $seed(18, [
            ['input' => "3\n0 3\n2 2\n6 2",    'expected_output' => '0.8750',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 5\n5 5",         'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0 1\n5 1\n10 1",   'expected_output' => '0.2727',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0 10",             'expected_output' => '1.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Event list ───────────────────────────────────────────────
        $seed(19, [
            ['input' => "4\n3 arrive\n1 depart\n2 arrive\n1 arrive",      'expected_output' => "1: arrive\n1: depart\n2: arrive\n3: arrive",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5 start\n5 end\n1 begin",                     'expected_output' => "1: begin\n5: end\n5: start",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n10 b\n10 a",                                   'expected_output' => "10: a\n10: b",                               'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 z\n1 a\n1 m",                               'expected_output' => "1: a\n1: m\n1: z",                           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Euler exponential growth ─────────────────────────────────
        $seed(20, [
            ['input' => "1.0\n0.1\n4\n0.5",     'expected_output' => "1.0500\n1.1025\n1.1576\n1.2155",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n0.5\n2\n0.0",     'expected_output' => "2.0000\n2.0000",                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0\n1.0\n3\n0.1",    'expected_output' => "11.0000\n12.1000\n13.3100",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n0.01\n3\n1.0",    'expected_output' => "2.0000\n3.0000\n4.0000",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Exponential decay ────────────────────────────────────────
        $seed(21, [
            ['input' => "100.0\n0.1\n1.0\n5",   'expected_output' => "90.0000\n81.0000\n72.9000\n65.6100\n59.0490",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "50.0\n0.5\n1.0\n3",    'expected_output' => "25.0000\n12.5000\n6.2500",                       'is_hidden' => false, 'order_index' => 2],
            ['input' => "1000.0\n0.1\n0.5\n4",  'expected_output' => "950.0000\n902.5000\n857.3750\n814.5063",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "100.0\n1.0\n1.0\n2",   'expected_output' => "0.0000\n0.0000",                                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Analytical exponential growth ────────────────────────────
        $seed(22, [
            ['input' => "1.0\n0.5\n3\n0\n1\n2",    'expected_output' => "1.0000\n1.6487\n2.7183",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n0.0\n2\n0\n10",      'expected_output' => "2.0000\n2.0000",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n1.0\n3\n0\n1\n2",    'expected_output' => "1.0000\n2.7183\n7.3891",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "100.0\n-0.1\n2\n0\n10",   'expected_output' => "100.0000\n36.7879",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Euler truncation error ───────────────────────────────────
        $seed(23, [
            ['input' => "1.0\n1.0\n0.5\n2.0",   'expected_output' => "euler: 2.2500\nexact: 7.3891\nerror: 5.1391",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n0.0\n1.0\n2.0",   'expected_output' => "euler: 1.0000\nexact: 1.0000\nerror: 0.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n1.0\n0.1\n1.0",   'expected_output' => "euler: 5.1117\nexact: 5.4366\nerror: 0.3249",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n-1.0\n0.5\n1.0",  'expected_output' => "euler: 0.2500\nexact: 0.3679\nerror: 0.1179",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Logistic growth ──────────────────────────────────────────
        $seed(24, [
            ['input' => "10.0\n0.5\n100.0\n1.0\n4",   'expected_output' => "14.5000\n20.6275\n28.6869\n38.7660",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "50.0\n0.5\n100.0\n1.0\n3",   'expected_output' => "62.5000\n73.0469\n81.0474",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n1.0\n10.0\n0.5\n4",     'expected_output' => "1.4500\n2.0753\n2.9120\n3.9828",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "100.0\n0.5\n100.0\n1.0\n2",  'expected_output' => "100.0000\n100.0000",                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: One SIR step ─────────────────────────────────────────────
        $seed(25, [
            ['input' => "990\n10\n0\n0.3\n0.1\n1",    'expected_output' => "S: 987.0300\nI: 12.0000\nR: 0.9700",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "999\n1\n0\n0.5\n0.1\n1",     'expected_output' => "S: 998.4995\nI: 1.4005\nR: 0.1000",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "500\n500\n0\n0.3\n0.1\n1",   'expected_output' => "S: 425.0000\nI: 525.0000\nR: 50.0000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1000\n0\n0\n0.3\n0.1\n1",    'expected_output' => "S: 1000.0000\nI: 0.0000\nR: 0.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: SIR n steps ──────────────────────────────────────────────
        $seed(26, [
            ['input' => "990\n10\n0\n0.3\n0.1\n1\n3",   'expected_output' => "987.03 12.00 0.97\n983.77 14.12 2.11\n980.16 16.34 3.50",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1000\n0\n0\n0.3\n0.1\n1\n2",   'expected_output' => "1000.00 0.00 0.00\n1000.00 0.00 0.00",                       'is_hidden' => false, 'order_index' => 2],
            ['input' => "900\n100\n0\n0.2\n0.05\n1\n2", 'expected_output' => "882.00 113.50 4.50\n862.84 127.91 9.25",                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "999\n1\n0\n0.5\n0.2\n1\n1",    'expected_output' => "998.5005 1.0995 0.2000",                                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: R0 ───────────────────────────────────────────────────────
        $seed(27, [
            ['input' => "0.3\n0.1",    'expected_output' => "R0: 3.0000\nepidemic",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.1\n0.2",    'expected_output' => "R0: 0.5000\nno epidemic", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n0.5",    'expected_output' => "R0: 1.0000\nno epidemic", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.8\n0.1",    'expected_output' => "R0: 8.0000\nepidemic",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Peak infected ────────────────────────────────────────────
        $seed(28, [
            ['input' => "990\n10\n0\n0.3\n0.1\n1\n20",   'expected_output' => "peak_I: 271.15\npeak_step: 17",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1000\n0\n0\n0.3\n0.1\n1\n10",   'expected_output' => "peak_I: 0.00\npeak_step: 1",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "990\n10\n0\n0.5\n0.1\n1\n30",   'expected_output' => "peak_I: 606.06\npeak_step: 16", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "500\n500\n0\n0.3\n0.1\n1\n5",   'expected_output' => "peak_I: 525.00\npeak_step: 1",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Herd immunity threshold ──────────────────────────────────
        $seed(29, [
            ['input' => "0.3\n0.1",   'expected_output' => '66.67%',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n0.25",  'expected_output' => '50.00%',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.8\n0.1",   'expected_output' => '87.50%',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.2\n0.2",   'expected_output' => '0.00%',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Agent movement ───────────────────────────────────────────
        $seed(30, [
            ['input' => "3\n0 1\n5 -1\n10 2\n3",   'expected_output' => "3.00\n2.00\n16.00",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0\n10 0\n5",         'expected_output' => "0.00\n10.00",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n-5 2\n4",              'expected_output' => "3.00",                 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0 1\n10 -1\n5 0\n10",  'expected_output' => "10.00\n0.00\n5.00",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: SI spread ────────────────────────────────────────────────
        $seed(31, [
            ['input' => "5\nS 0\nS 1\nI 5\nS 6\nS 10\n2",  'expected_output' => '3',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nS 0\nI 1\nS 2\n1",             'expected_output' => '3',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nS 0\nS 5\nI 100\n1",           'expected_output' => '1',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nI 0\nI 10\n5",                 'expected_output' => '2',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Count neighbours ─────────────────────────────────────────
        $seed(32, [
            ['input' => "4\n0 0\n1 0\n0 1\n5 5\n1.5",   'expected_output' => "2\n2\n2\n0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0 0\n3 4\n6 8\n5.1",        'expected_output' => "1\n2\n1",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 0\n10 10\n1",             'expected_output' => "0\n0",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0 0\n1 0\n2 0\n3 0\n1.5",  'expected_output' => "1\n2\n2\n1",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Voting model ─────────────────────────────────────────────
        $seed(33, [
            ['input' => "1 0 0 0 1\n3",      'expected_output' => '0 0 0 0 0',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1 1 0 0\n2",      'expected_output' => '1 1 1 1 0',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0 0 0 0 0\n5",      'expected_output' => '0 0 0 0 0',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 1 1 1 1\n3",      'expected_output' => '1 1 1 1 1',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Stock and flow ───────────────────────────────────────────
        $seed(34, [
            ['input' => "100\n10\n5\n4",    'expected_output' => "105.00\n110.00\n115.00\n120.00",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "50\n0\n10\n4",     'expected_output' => "40.00\n30.00\n20.00\n10.00",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0\n10\n3",      'expected_output' => "0.00\n0.00\n0.00",                'is_hidden' => true,  'order_index' => 3],
            ['input' => "1000\n5\n5\n3",    'expected_output' => "1000.00\n1000.00\n1000.00",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Negative feedback ────────────────────────────────────────
        $seed(35, [
            ['input' => "20\n100\n0.1\n5",   'expected_output' => "28.0000\n35.2000\n41.6800\n47.5120\n52.7608",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "100\n100\n0.5\n3",  'expected_output' => "100.0000\n100.0000\n100.0000",                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n10\n0.5\n3",     'expected_output' => "5.0000\n7.5000\n8.7500",                         'is_hidden' => true,  'order_index' => 3],
            ['input' => "50\n0\n0.2\n3",     'expected_output' => "40.0000\n32.0000\n25.6000",                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Lotka-Volterra step ──────────────────────────────────────
        $seed(36, [
            ['input' => "40\n9\n0.1\n0.02\n0.01\n0.1\n1",    'expected_output' => "X: 39.2800\nY: 8.6760",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "100\n10\n0.1\n0.02\n0.01\n0.1\n1",  'expected_output' => "X: 90.0000\nY: 9.9000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "50\n5\n0.2\n0.01\n0.01\n0.2\n0.5",  'expected_output' => "X: 53.7375\nY: 4.8625",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n0\n0.5\n0.1\n0.1\n0.5\n1",       'expected_output' => "X: 0.0000\nY: 0.0000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Positive feedback ────────────────────────────────────────
        $seed(37, [
            ['input' => "1000\n0.05\n4",   'expected_output' => "1050.00\n1102.50\n1157.63\n1215.51",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "100\n0.1\n3",     'expected_output' => "110.00\n121.00\n133.10",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1.0\n3",       'expected_output' => "2.00\n4.00\n8.00",                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "500\n0.0\n2",     'expected_output' => "500.00\n500.00",                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Sensitivity index ────────────────────────────────────────
        $seed(38, [
            ['input' => "10.0\n3\n1.0 8.0\n2.0 10.0\n3.0 14.0",   'expected_output' => '0.6000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5.0\n2\n0.5 5.0\n1.5 5.0",               'expected_output' => '0.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "100.0\n4\n1 80\n2 90\n3 110\n4 120",      'expected_output' => '0.4000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n2\n0.1 0.5\n0.9 1.5",               'expected_output' => '1.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: RMSE ─────────────────────────────────────────────────────
        $seed(39, [
            ['input' => "4\n10 9\n20 22\n15 14\n30 31",    'expected_output' => '1.2247',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5 5\n10 10\n15 15",            'expected_output' => '0.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 10\n10 0",                   'expected_output' => '10.0000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 2\n2 3\n3 4",                'expected_output' => '1.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Model calibration ────────────────────────────────────────
        $seed(40, [
            ['input' => "3\n1 2 3\n2\n0.5 1.1 2.1 3.1\n1.0 1.0 2.0 3.0",   'expected_output' => "best_param: 1.0\nrmse: 0.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5 10\n2\n1.0 4 9\n2.0 5 10",                    'expected_output' => "best_param: 2.0\nrmse: 0.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 2\n3\n0.1 0 0\n0.5 1 2\n1.0 2 4",            'expected_output' => "best_param: 0.5\nrmse: 0.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n10\n2\n5.0 5\n10.0 10",                         'expected_output' => "best_param: 10.0\nrmse: 0.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: NSE ──────────────────────────────────────────────────────
        $seed(41, [
            ['input' => "4\n10 10\n20 20\n15 15\n30 30",   'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10 15\n20 25\n30 35",          'expected_output' => '-2.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5 5\n5 5\n5 5",                'expected_output' => '1.0000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10 12\n20 18\n30 32\n40 38",   'expected_output' => '0.8788',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: CI for simulation output ─────────────────────────────────
        $seed(42, [
            ['input' => "5\n10\n12\n11\n13\n9",     'expected_output' => "mean: 11.0000\nci_lower: 9.2843\nci_upper: 12.7157",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n5\n5\n5\n5",            'expected_output' => "mean: 5.0000\nci_lower: 5.0000\nci_upper: 5.0000",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0\n10\n20",             'expected_output' => "mean: 10.0000\nci_lower: -4.2009\nci_upper: 24.2009",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n100\n200",              'expected_output' => "mean: 150.0000\nci_lower: 11.5050\nci_upper: 288.4950", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Warm-up removal ──────────────────────────────────────────
        $seed(43, [
            ['input' => "8\n1\n2\n3\n10\n11\n12\n13\n14\n3",   'expected_output' => '12.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n100\n200\n10\n10\n2",               'expected_output' => '10.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n2\n3\n4\n5\n0",                  'expected_output' => '3.0000',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0\n0\n10\n2",                        'expected_output' => '10.0000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Batch means ──────────────────────────────────────────────
        $seed(44, [
            ['input' => "6\n10\n12\n11\n20\n22\n21\n3",   'expected_output' => "11.0000\n21.0000",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n2",               'expected_output' => "1.5000\n3.5000",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n1\n2\n3\n4\n5\n6\n7\n3",      'expected_output' => "2.0000\n5.0000",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n5\n5\n5\n10\n10\n10\n2",      'expected_output' => "5.0000\n5.0000\n10.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Two-sample t ─────────────────────────────────────────────
        $seed(45, [
            ['input' => "3\n10\n12\n11\n3\n20\n22\n21",    'expected_output' => '-10.0000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5\n3\n5\n5\n5",         'expected_output' => '0.0000',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n3\n2\n5\n9",               'expected_output' => '-3.0000',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2\n3\n3\n4\n5\n6",         'expected_output' => '-3.0000',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: CV ───────────────────────────────────────────────────────
        $seed(46, [
            ['input' => "5\n10\n12\n11\n13\n9",   'expected_output' => '0.1284',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5",             'expected_output' => '0.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4",          'expected_output' => '0.4564',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10\n20",              'expected_output' => '0.3333',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Steady-state detection ───────────────────────────────────
        $seed(47, [
            ['input' => "6\n10\n12\n11\n10\n11\n10\n0.5",    'expected_output' => "step: 3\nmean: 11.0000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n10\n10\n10\n10\n0.1",            'expected_output' => "step: 2\nmean: 10.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n100\n50\n50\n50\n1.0",        'expected_output' => "step: 4\nmean: 50.2500",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2\n3\n0.1",                   'expected_output' => "step: 3\nmean: 2.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Relative half-width ──────────────────────────────────────
        $seed(48, [
            ['input' => "5\n10\n12\n11\n13\n9",   'expected_output' => '0.1568',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5",             'expected_output' => '0.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4",          'expected_output' => '0.4453',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10\n20",              'expected_output' => '0.4534',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Minimum replications ─────────────────────────────────────
        $seed(49, [
            ['input' => "5\n10\n12\n11\n13\n9\n0.1",    'expected_output' => '7',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5\n0.05",             'expected_output' => '1',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4\n0.2",           'expected_output' => '5',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10\n20\n0.1",               'expected_output' => '21',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Paired t-test ────────────────────────────────────────────
        $seed(50, [
            ['input' => "4\n-10\n-12\n-9\n-11",    'expected_output' => "t: -20.6777\nsignificant",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0\n0\n0",              'expected_output' => "t: 0.0000\nnot significant",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4",           'expected_output' => "t: 3.8730\nsignificant",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n-1\n0\n1",             'expected_output' => "t: 0.0000\nnot significant",    'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 6 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}