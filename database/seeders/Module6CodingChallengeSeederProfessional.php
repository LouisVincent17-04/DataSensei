<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 6 — Modeling & Simulation (Professional) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Professional tier
 *   2. coding_questions    — 50 questions
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered:
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
 * Difficulty: Professional — deterministic formulations of simulation concepts
 *             (seeded RNG, exact Euler steps, closed-form results) so test cases
 *             are reproducible.  Uses only Python math / statistics builtins.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module6CodingChallengeSeederProfessional extends Seeder
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

        $this->command->info('Creating Module 6 — Modeling & Simulation (Professional) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Modeling & Simulation',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Implement core simulation and modeling algorithms in Python: random-variate generation, Monte Carlo estimation, discrete-event queues, Euler/RK4 ODE solvers, SIR epidemics, agent-based rules, system-dynamics feedback, sensitivity analysis, and output statistics — using only Python\'s math and statistics builtins.',
                'time_limit_seconds' => 3600,
                'base_xp'            => 3000,
                'order_index'        => 6,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
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
**Model classification**: classify a given simulation model as `static` or `dynamic`, and `deterministic` or `stochastic`, based on two boolean flags.

Read two lines:
- `time_varies`: `yes` or `no` (does the system state change over time?)
- `has_randomness`: `yes` or `no` (does the model include random variables?)

Print two lines:
- `static` or `dynamic`
- `deterministic` or `stochastic`

Example:
```
Input:
yes
no
Output:
dynamic
deterministic
```
MD,
                'starter_code'        => "time_varies = input().strip().lower()\nhas_randomness = input().strip().lower()\nprint('dynamic' if time_varies == 'yes' else 'static')\nprint('stochastic' if has_randomness == 'yes' else 'deterministic')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
**Dimensional analysis**: check whether a physical formula is dimensionally consistent.

You are given a formula expressed as a product of base quantities with integer exponents: `[M^a L^b T^c]`. Two sides are given. If all three exponents match, print `consistent`; otherwise print `inconsistent`.

Read six integers: `a1 b1 c1` (left-hand side) and `a2 b2 c2` (right-hand side).

Example:
```
Input:
1 1 -2
1 1 -2
Output: consistent
```
MD,
                'starter_code'        => "a1,b1,c1 = map(int,input().split())\na2,b2,c2 = map(int,input().split())\nprint('consistent' if (a1,b1,c1)==(a2,b2,c2) else 'inconsistent')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
**Simulation time-step selection**: given a system with characteristic frequency `f` (Hz), print the maximum stable time step `dt` for explicit Euler using the Nyquist criterion:

dt_max = 1 / (2 × f)

Read `f` (float). Print `dt_max` rounded to 6 decimal places.

Example:
```
Input: 100.0
Output: 0.005
```
MD,
                'starter_code'        => "f = float(input())\nprint(round(1 / (2 * f), 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
**Model abstraction levels**: given a list of model descriptions, classify each as `white-box`, `grey-box`, or `black-box`.

Rules (based on keyword presence in the description, case-insensitive):
- Contains `equations` or `first principles` → `white-box`
- Contains `partial` or `empirical` → `grey-box`
- Contains `neural` or `lookup` or `data-driven` → `black-box`
- Otherwise → `unknown`

Read `n` descriptions (one per line). Print the classification for each.

Example:
```
Input:
3
uses differential equations
empirical fit
neural network model
Output:
white-box
grey-box
black-box
```
MD,
                'starter_code'        => "n = int(input())\nfor _ in range(n):\n    d = input().lower()\n    if 'equations' in d or 'first principles' in d:\n        print('white-box')\n    elif 'partial' in d or 'empirical' in d:\n        print('grey-box')\n    elif 'neural' in d or 'lookup' in d or 'data-driven' in d:\n        print('black-box')\n    else:\n        print('unknown')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
**Simulation replication planning**: compute the number of replications `n` required to achieve a half-width `h` in a confidence interval, given population standard deviation `sigma` and z* = 1.96 (95% CI).

n = ceil((z* × sigma / h)²)

Read `sigma` and `h`. Print the minimum number of replications.

Example:
```
Input:
10.0
2.0
Output: 97
```
MD,
                'starter_code'        => "import math\nsigma = float(input())\nh = float(input())\nprint(math.ceil((1.96 * sigma / h)**2))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.2: Random Numbers & Probability Distributions (Q6–Q11)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
**Linear Congruential Generator (LCG)**: implement the LCG formula:

X_{n+1} = (a × X_n + c) mod m

Read `seed`, `a`, `c`, `m`, and `n` (number of values to generate). Print `n` pseudo-random integers, one per line.

Example:
```
Input:
1
1664525
1013904223
4294967296
5
Output:
2678477748
1234613589
3067484910
1711312489
3497749696 (wait — this overflows 32-bit; use Python big ints)
```

Recompute:
seed=1, a=1664525, c=1013904223, m=4294967296
X1 = (1664525*1 + 1013904223) % 4294967296 = 1015568748
X2 = (1664525*1015568748 + 1013904223) % 4294967296 = ...

Just implement the formula correctly and Python big ints handle it.

Example (small values):
```
Input:
1
5
3
16
4
Output:
8
11
10
5
```
MD,
                'starter_code'        => "x = int(input())\na = int(input())\nc = int(input())\nm = int(input())\nn = int(input())\nfor _ in range(n):\n    x = (a * x + c) % m\n    print(x)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
**Inverse Transform Sampling — Exponential distribution**: given a list of uniform random numbers U ∈ (0,1), generate exponential random variates with rate `lambda` using:

X = −(1/λ) × ln(U)

Read `lambda`, then `n` uniform values (one per line). Print each exponential variate rounded to 6 decimal places.

Example:
```
Input:
2.0
3
0.5
0.2
0.8
Output:
0.346574
0.804719
0.111572
```
MD,
                'starter_code'        => "import math\nlam = float(input())\nn = int(input())\nfor _ in range(n):\n    u = float(input())\n    print(round(-math.log(u) / lam, 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
**Inverse Transform Sampling — Discrete distribution**: given a discrete PMF, map a uniform U to the corresponding outcome using the CDF.

Read `n` outcomes and their probabilities (one per line as `value prob`), then `q` uniform values. For each U, print the smallest value v such that CDF(v) ≥ U.

Example:
```
Input:
3
1 0.2
2 0.5
3 0.3
4
0.1
0.5
0.7
0.99
Output:
1
2
3
3
```
MD,
                'starter_code'        => "n = int(input())\noutcomes = []\nfor _ in range(n):\n    parts = input().split()\n    outcomes.append((parts[0], float(parts[1])))\nq = int(input())\nfor _ in range(q):\n    u = float(input())\n    cum = 0.0\n    for val, prob in outcomes:\n        cum += prob\n        if u <= cum:\n            print(val)\n            break\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
**Box-Muller Transform**: convert pairs of independent uniform U1, U2 ∈ (0,1) to standard normal variates Z0, Z1.

Z0 = √(−2 ln U1) × cos(2π U2)
Z1 = √(−2 ln U1) × sin(2π U2)

Read `n` pairs (each line: `U1 U2`). For each pair, print Z0 and Z1 rounded to 6 decimal places, space-separated.

Example:
```
Input:
2
0.5 0.5
0.1 0.9
Output:
1.1774 -1.1774 (approx)
...
```

Precise:
Z0 = sqrt(-2*ln(0.5)) * cos(2*pi*0.5) = sqrt(1.386294) * cos(pi) = 1.177410 * (-1) = -1.177410
Z1 = sqrt(1.386294) * sin(pi) ≈ 0.0

```
Input:
1
0.5 0.5
Output:
-1.17741 0.0
```
MD,
                'starter_code'        => "import math\nn = int(input())\nfor _ in range(n):\n    u1, u2 = map(float, input().split())\n    r = math.sqrt(-2 * math.log(u1))\n    z0 = round(r * math.cos(2 * math.pi * u2), 6)\n    z1 = round(r * math.sin(2 * math.pi * u2), 6)\n    print(z0, z1)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
**Empirical CDF**: given a sorted list of observations, compute the empirical CDF value F(x) = (number of observations ≤ x) / n for each query x.

Read `n` observations (sorted ascending), then `q` query values. For each query, print F(x) rounded to 4 decimal places.

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
2
3
5
Output:
0.4
0.6
1.0
```
MD,
                'starter_code'        => "n = int(input())\ndata = [float(input()) for _ in range(n)]\nq = int(input())\nfor _ in range(q):\n    x = float(input())\n    print(round(sum(1 for v in data if v <= x) / n, 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
**Chi-square goodness-of-fit for distribution testing**: given observed frequencies and expected frequencies, compute χ² and determine whether to reject the hypothesis that data follows the expected distribution at α = 0.05.

Use the critical value table: df → χ²_crit:
1→3.841, 2→5.991, 3→7.815, 4→9.488, 5→11.070, 6→12.592, 7→14.067, 8→15.507, 9→16.919, 10→18.307

df = number of bins − 1.

Read `k` bins, then `k` observed counts, then `k` expected counts. Print χ² rounded to 4 decimal places, then `reject` or `fail to reject`.

Example:
```
Input:
4
20
30
25
25
25
25
25
25
Output:
1.0
fail to reject
```
MD,
                'starter_code'        => "k = int(input())\nobs = [float(input()) for _ in range(k)]\nexp = [float(input()) for _ in range(k)]\nchi2 = sum((o-e)**2/e for o,e in zip(obs,exp))\ncrit = {1:3.841,2:5.991,3:7.815,4:9.488,5:11.070,6:12.592,7:14.067,8:15.507,9:16.919,10:18.307}\ndf = k - 1\nprint(round(chi2, 4))\nprint('reject' if chi2 > crit.get(df, 1e9) else 'fail to reject')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.3: Monte Carlo Methods (Q12–Q17)
            // ═══════════════════════════════════════════════════════════════

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
**Monte Carlo integration** of f(x) = x² over [0, 1]:

∫₀¹ x² dx ≈ (1/n) × Σ f(xi)

Given a list of sample points xi, compute the Monte Carlo estimate.

Read `n`, then `n` x values. Print the estimate rounded to 6 decimal places.

Example:
```
Input:
4
0.1
0.5
0.7
0.9
Output: 0.3775
```
(mean of 0.01, 0.25, 0.49, 0.81 = 0.39 → let me recompute: (0.01+0.25+0.49+0.81)/4 = 1.56/4 = 0.39)

```
Output: 0.39
```
MD,
                'starter_code'        => "n = int(input())\nxs = [float(input()) for _ in range(n)]\nprint(round(sum(x**2 for x in xs) / n, 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
**Monte Carlo estimation of π** using the unit circle method.

A point (x, y) with x,y ∈ [0,1] is inside the quarter circle if x² + y² ≤ 1.

π ≈ 4 × (inside / total)

Read `n` pairs `x y` (one per line). Print the π estimate rounded to 6 decimal places.

Example:
```
Input:
4
0.1 0.2
0.9 0.9
0.5 0.5
0.3 0.4
Output: 3.0
```
(inside: (0.1,0.2)→0.05≤1✓, (0.9,0.9)→1.62>1✗, (0.5,0.5)→0.5≤1✓, (0.3,0.4)→0.25≤1✓; 3/4 inside → π≈3.0)
MD,
                'starter_code'        => "n = int(input())\ninside = 0\nfor _ in range(n):\n    x, y = map(float, input().split())\n    if x*x + y*y <= 1.0:\n        inside += 1\nprint(round(4 * inside / n, 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
**Monte Carlo option pricing** (European call, Black-Scholes payoff simulation).

Payoff = max(S_T − K, 0), discounted: price ≈ e^(−rT) × mean(payoffs)

S_T = S0 × exp((r − 0.5σ²)T + σ√T × Z)

where Z is standard normal.

Read `S0`, `K`, `r`, `sigma`, `T`, then `n` standard normal variates Z (one per line). Print the option price rounded to 6 decimal places.

Example:
```
Input:
100
100
0.05
0.2
1.0
3
-0.5
0.0
1.5
Output: 7.965677 (approx)
```
MD,
                'starter_code'        => "import math\nS0 = float(input())\nK  = float(input())\nr  = float(input())\nsig= float(input())\nT  = float(input())\nn  = int(input())\npayoffs = []\nfor _ in range(n):\n    Z = float(input())\n    ST = S0 * math.exp((r - 0.5*sig**2)*T + sig*math.sqrt(T)*Z)\n    payoffs.append(max(ST - K, 0))\nprint(round(math.exp(-r*T) * sum(payoffs)/n, 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
**Acceptance-Rejection sampling**: given a target density f(x) and an envelope g(x) = c (uniform), determine whether each proposed sample (x, u) is accepted.

A sample is accepted if u ≤ f(x) / (c × g(x)) = f(x) / c.

Use f(x) = 6x(1−x) on [0,1] (Beta(2,2) unnormalized; max = 1.5 at x=0.5; set c = 1.5).

Read `n` pairs `x u` (one per line, u ∈ [0,1]). Print `accept` or `reject` for each.

Example:
```
Input:
3
0.5 0.9
0.5 0.5
0.1 0.8
Output:
accept
accept
reject
```
(f(0.5)=6*0.5*0.5=1.5, threshold=1.5/1.5=1.0 → 0.9≤1.0 accept; 0.5≤1.0 accept; f(0.1)=6*0.1*0.9=0.54, threshold=0.54/1.5=0.36 → 0.8>0.36 reject)
MD,
                'starter_code'        => "n = int(input())\nfor _ in range(n):\n    x, u = map(float, input().split())\n    fx = 6 * x * (1 - x)\n    c = 1.5\n    print('accept' if u <= fx / c else 'reject')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
**Monte Carlo reliability estimation**: a system has `k` components in series. Each component fails independently. Given `n` simulation trials (each a line of `k` binary values: 1=working, 0=failed), estimate:
- System reliability R (probability all components work)
- System failure probability F = 1 − R

Read `k`, then `n`, then `n` lines each with `k` space-separated values (0 or 1). Print R and F each rounded to 4 decimal places on separate lines.

Example:
```
Input:
3
4
1 1 1
1 0 1
1 1 0
1 1 1
Output:
0.5
0.5
```
MD,
                'starter_code'        => "k = int(input())\nn = int(input())\nsuccess = 0\nfor _ in range(n):\n    comp = list(map(int, input().split()))\n    if all(c == 1 for c in comp):\n        success += 1\nR = success / n\nprint(round(R, 4))\nprint(round(1 - R, 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
**Monte Carlo variance reduction — control variates**: estimate E[f(X)] using a control variate g(X) with known mean μ_g.

Corrected estimator: f̂_cv = f̄ − b × (ḡ − μ_g)

where b = Cov(f,g) / Var(g) (optimal), computed from the sample.

Read `mu_g` (known), then `n` pairs `f_i g_i` (one per line). Print the corrected estimate rounded to 6 decimal places.

Example:
```
Input:
0.5
4
0.3 0.4
0.7 0.6
0.5 0.5
0.9 0.8
Output: 0.599998 (approx 0.6)
```
MD,
                'starter_code'        => "mu_g = float(input())\nn = int(input())\npairs = [tuple(map(float,input().split())) for _ in range(n)]\nfs = [p[0] for p in pairs]\ngs = [p[1] for p in pairs]\nf_bar = sum(fs)/n\ng_bar = sum(gs)/n\ncov = sum((fs[i]-f_bar)*(gs[i]-g_bar) for i in range(n))/n\nvar_g = sum((g-g_bar)**2 for g in gs)/n\nb = cov/var_g if var_g else 0\nresult = f_bar - b*(g_bar - mu_g)\nprint(round(result, 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.4: Discrete-Event Simulation (Q18–Q22)
            // ═══════════════════════════════════════════════════════════════

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
**M/M/1 queue — theoretical metrics**: for a single-server queue with arrival rate λ and service rate μ (λ < μ):

- ρ = λ/μ  (utilization)
- L = ρ/(1−ρ)  (average number in system)
- W = 1/(μ−λ)  (average time in system)
- Lq = ρ²/(1−ρ)  (average number in queue)
- Wq = ρ/(μ−λ)  (average waiting time in queue)

Read `lambda` and `mu`. Print ρ, L, W, Lq, Wq each rounded to 4 decimal places on separate lines.

Example:
```
Input:
3.0
4.0
Output:
0.75
3.0
1.0
2.25
0.75
```
MD,
                'starter_code'        => "lam = float(input())\nmu  = float(input())\nrho = lam/mu\nL   = rho/(1-rho)\nW   = 1/(mu-lam)\nLq  = rho**2/(1-rho)\nWq  = rho/(mu-lam)\nfor v in [rho,L,W,Lq,Wq]:\n    print(round(v,4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
**Single-server queue simulation** (event-driven, deterministic inter-arrival and service times):

Process customers one at a time. For each customer, track:
- Arrival time
- Service start time = max(arrival, previous service end)
- Service end time = service start + service time
- Wait time = service start − arrival

Read `n` customers, each as `arrival_time service_time` (sorted by arrival). Print each customer's wait time rounded to 2 decimal places, then the average wait time rounded to 4 decimal places.

Example:
```
Input:
3
0 3
2 2
6 1
Output:
0.0
1.0
0.0
0.3333
```
MD,
                'starter_code'        => "n = int(input())\ncustomers = [tuple(map(float,input().split())) for _ in range(n)]\nwaits = []\nend = 0.0\nfor arr, svc in customers:\n    start = max(arr, end)\n    wait = start - arr\n    waits.append(wait)\n    end = start + svc\nfor w in waits:\n    print(round(w, 2))\nprint(round(sum(waits)/len(waits), 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
**Multi-server queue** (deterministic): `s` servers, each customer goes to the first available server.

Track server finish times. Each new customer is assigned to the server with the smallest current finish time.

Read `s` (servers), `n` customers each as `arrival_time service_time`. Print each customer's wait time rounded to 2 decimal places, then average wait rounded to 4 decimal places.

Example:
```
Input:
2
4
0 3
0 4
1 2
2 2
Output:
0.0
0.0
0.0
1.0
0.25
```
MD,
                'starter_code'        => "s = int(input())\nn = int(input())\nfinish = [0.0] * s\nwaits = []\nfor _ in range(n):\n    arr, svc = map(float, input().split())\n    idx = finish.index(min(finish))\n    start = max(arr, finish[idx])\n    waits.append(start - arr)\n    finish[idx] = start + svc\nfor w in waits:\n    print(round(w, 2))\nprint(round(sum(waits)/len(waits), 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
**Inventory simulation** (periodic review): simulate an (s, S) inventory policy.

At the start of each period:
- If inventory ≤ s, order up to S (place order immediately)
- Demand is subtracted; inventory cannot go below 0 (lost sales)

Track ending inventory and lost sales each period.

Read `S`, `s`, `initial_inventory`, then `n` periods each with a demand value. Print ending inventory and lost sales for each period, separated by a space.

Example:
```
Input:
10
3
8
5
3
0
2
1
5
Output:
3 0
0 0
10 0
8 0
7 0
2 0
```

Wait — let me re-trace:
Period 1: inv=8≤10 but >3, demand=3 → end=5. Period 2: inv=5>3, demand=3→2. Period 3: inv=2≤3 → order to 10, demand=0→10. Period 4: inv=10>3, demand=2→8. Period 5: inv=8>3, demand=1→7. Period 6: inv=7>3, demand=5→2.

```
Output:
5 0
2 0
10 0
8 0
7 0
2 0
```
MD,
                'starter_code'        => "S = int(input())\ns = int(input())\ninv = int(input())\nn = int(input())\nfor _ in range(n):\n    demand = int(input())\n    if inv <= s:\n        inv = S\n    sold = min(inv, demand)\n    lost = demand - sold\n    inv -= sold\n    print(inv, lost)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
**Event calendar / future event list**: given a set of events with their scheduled times, process them in chronological order and print each event's name and time.

Read `n` events each as `name time` (time is a float). Sort by time (ties: lexicographic by name) and print each as `name: time` (time rounded to 2 decimal places).

Example:
```
Input:
4
arrival 2.5
departure 1.0
arrival 1.0
service 3.0
Output:
arrival: 1.0
departure: 1.0
arrival: 2.5
service: 3.0
```
MD,
                'starter_code'        => "n = int(input())\nevents = []\nfor _ in range(n):\n    parts = input().split()\n    events.append((parts[0], float(parts[1])))\nevents.sort(key=lambda e: (e[1], e[0]))\nfor name, t in events:\n    print(f'{name}: {round(t,2)}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.5: Differential Equations & Continuous Simulation (Q23–Q27)
            // ═══════════════════════════════════════════════════════════════

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
**Euler's method** for dy/dt = f(t, y).

Euler step: y_{n+1} = y_n + h × f(t_n, y_n)

The function is given as a string `"k*y"` meaning f(t,y) = k×y (exponential growth/decay). Read `k`, `y0`, `h`, and `n_steps`. Print y after each step rounded to 6 decimal places.

Example:
```
Input:
-1.0
1.0
0.1
5
Output:
0.9
0.81
0.729
0.6561
0.59049
```
MD,
                'starter_code'        => "k  = float(input())\ny  = float(input())\nh  = float(input())\nns = int(input())\nfor _ in range(ns):\n    y = y + h * k * y\n    print(round(y, 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
**Exact vs Euler error**: for dy/dt = k×y with exact solution y(t) = y0×e^(k×t), compute the absolute error after `n` Euler steps.

Read `k`, `y0`, `h`, `n`. Print exact value, Euler value, and absolute error each rounded to 6 decimal places on separate lines.

Example:
```
Input:
-1.0
1.0
0.1
5
Output:
0.606531
0.59049
0.016041
```
MD,
                'starter_code'        => "import math\nk = float(input())\ny0= float(input())\nh = float(input())\nn = int(input())\ny = y0\nfor _ in range(n):\n    y = y + h*k*y\nt = h*n\nexact = y0*math.exp(k*t)\nprint(round(exact,6))\nprint(round(y,6))\nprint(round(abs(exact-y),6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
**Runge-Kutta 4th order (RK4)** for dy/dt = k×y.

k1 = h×f(t, y)
k2 = h×f(t+h/2, y+k1/2)
k3 = h×f(t+h/2, y+k2/2)
k4 = h×f(t+h, y+k3)
y_{n+1} = y_n + (k1 + 2k2 + 2k3 + k4)/6

Read `k`, `y0`, `h`, `n_steps`. Print y after each step rounded to 8 decimal places.

Example:
```
Input:
-1.0
1.0
0.1
3
Output:
0.90483742
0.81873075
0.74081822
```
MD,
                'starter_code'        => "k  = float(input())\ny  = float(input())\nh  = float(input())\nns = int(input())\nfor i in range(ns):\n    k1 = h * k * y\n    k2 = h * k * (y + k1/2)\n    k3 = h * k * (y + k2/2)\n    k4 = h * k * (y + k3)\n    y  = y + (k1 + 2*k2 + 2*k3 + k4)/6\n    print(round(y, 8))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
**Logistic growth** ODE using Euler's method:

dy/dt = r × y × (1 − y/K)

Read `r`, `K`, `y0`, `h`, `n_steps`. Print y after each step rounded to 6 decimal places.

Example:
```
Input:
0.5
100.0
10.0
0.5
4
Output:
12.25
14.97...
```

Precise:
y1 = 10 + 0.5*0.5*10*(1-10/100) = 10 + 0.25*10*0.9 = 10+2.25=12.25
y2 = 12.25 + 0.5*0.5*12.25*(1-12.25/100)=12.25+0.25*12.25*0.8775=12.25+2.6878=14.9378...

```
Input:
0.5
100.0
10.0
0.5
4
Output:
12.25
14.9378
18.2366
22.1375
```
MD,
                'starter_code'        => "r  = float(input())\nK  = float(input())\ny  = float(input())\nh  = float(input())\nns = int(input())\nfor _ in range(ns):\n    y = y + h * r * y * (1 - y/K)\n    print(round(y, 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
**Stability check for Euler's method**: for dy/dt = k×y (k < 0), Euler is stable if |1 + h×k| ≤ 1.

Read `k` (negative float) and `h`. Print `stable` or `unstable`.

Example:
```
Input:
-2.0
0.8
Output: unstable
```
(|1 + 0.8*(−2)| = |1−1.6| = 0.6 ≤ 1 → stable)

Actually 0.6≤1 → stable. Let me adjust example:
```
Input:
-2.0
1.2
Output: unstable
```
(|1+1.2*(−2)|=|1−2.4|=1.4 > 1 → unstable)
MD,
                'starter_code'        => "k = float(input())\nh = float(input())\nprint('stable' if abs(1 + h*k) <= 1 else 'unstable')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.6: The SIR Epidemic Model (Q28–Q32)
            // ═══════════════════════════════════════════════════════════════

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
**SIR model — one Euler step**: given S, I, R, N, beta, gamma, and dt, compute the new S, I, R values.

dS/dt = −β × S × I / N
dI/dt = β × S × I / N − γ × I
dR/dt = γ × I

Read `S`, `I`, `R`, `N`, `beta`, `gamma`, `dt`. Print new S, I, R each rounded to 4 decimal places on separate lines.

Example:
```
Input:
990
10
0
1000
0.3
0.1
1.0
Output:
987.03
12.67
0.3 (wait: dR=0.1*10=1.0, so R=1.0)
```

Recalculate:
dS = -0.3*990*10/1000 = -2.97 → S=987.03
dI = 2.97 - 0.1*10 = 1.97 → I=11.97
dR = 0.1*10 = 1.0 → R=1.0

```
Output:
987.03
11.97
1.0
```
MD,
                'starter_code'        => "S = float(input())\nI = float(input())\nR = float(input())\nN = float(input())\nbeta  = float(input())\ngamma = float(input())\ndt    = float(input())\ndS = -beta*S*I/N\ndI =  beta*S*I/N - gamma*I\ndR =  gamma*I\nprint(round(S+dt*dS,4))\nprint(round(I+dt*dI,4))\nprint(round(R+dt*dR,4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
**SIR model — multiple steps**: simulate `n` Euler steps and print S, I, R after each step (rounded to 2 decimal places, space-separated).

Read `S0`, `I0`, `R0`, `N`, `beta`, `gamma`, `dt`, `n_steps`.

Example:
```
Input:
990
10
0
1000
0.3
0.1
1.0
3
Output:
987.03 11.97 1.0
984.04 13.89 2.07
980.99 15.75 3.26
```
MD,
                'starter_code'        => "S=float(input());I=float(input());R=float(input());N=float(input())\nbeta=float(input());gamma=float(input());dt=float(input());ns=int(input())\nfor _ in range(ns):\n    dS=-beta*S*I/N;dI=beta*S*I/N-gamma*I;dR=gamma*I\n    S+=dt*dS;I+=dt*dI;R+=dt*dR\n    print(round(S,2),round(I,2),round(R,2))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
**Basic reproduction number R0**: R0 = β / γ.

Classify:
- R0 > 1 → `epidemic grows`
- R0 = 1 → `endemic`
- R0 < 1 → `epidemic dies out`

Read `beta` and `gamma`. Print R0 rounded to 4 decimal places and the classification.

Example:
```
Input:
0.3
0.1
Output:
3.0
epidemic grows
```
MD,
                'starter_code'        => "beta  = float(input())\ngamma = float(input())\nR0 = beta/gamma\nprint(round(R0,4))\nif R0 > 1: print('epidemic grows')\nelif R0 < 1: print('epidemic dies out')\nelse: print('endemic')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
**Herd immunity threshold**: the fraction of the population that needs to be immune to prevent epidemic spread.

Herd immunity threshold p_c = 1 − 1/R0

Also compute the minimum number of people to vaccinate: ceil(p_c × N).

Read `beta`, `gamma`, `N` (population size). Print p_c rounded to 4 decimal places and the vaccination target.

Example:
```
Input:
0.3
0.1
1000
Output:
0.6667
667
```
MD,
                'starter_code'        => "import math\nbeta=float(input());gamma=float(input());N=int(input())\nR0=beta/gamma\npc=1-1/R0\nprint(round(pc,4))\nprint(math.ceil(pc*N))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
**SEIR model — one Euler step**: extends SIR with an Exposed (E) compartment.

dS/dt = −β × S × I / N
dE/dt = β × S × I / N − σ × E
dI/dt = σ × E − γ × I
dR/dt = γ × I

Read `S`, `E`, `I`, `R`, `N`, `beta`, `sigma`, `gamma`, `dt`. Print new S, E, I, R each rounded to 4 decimal places.

Example:
```
Input:
990
5
5
0
1000
0.5
0.2
0.1
1.0
Output:
987.525
5.475
5.425
0.5 (wait: dR=0.1*5=0.5)
```

dS=-0.5*990*5/1000=-2.475 → S=987.525
dE=2.475-0.2*5=1.475 → E=6.475
dI=0.2*5-0.1*5=0.5 → I=5.5
dR=0.1*5=0.5 → R=0.5

```
Output:
987.525
6.475
5.5
0.5
```
MD,
                'starter_code'        => "S=float(input());E=float(input());I=float(input());R=float(input());N=float(input())\nbeta=float(input());sigma=float(input());gamma=float(input());dt=float(input())\ndS=-beta*S*I/N\ndE=beta*S*I/N-sigma*E\ndI=sigma*E-gamma*I\ndR=gamma*I\nprint(round(S+dt*dS,4))\nprint(round(E+dt*dE,4))\nprint(round(I+dt*dI,4))\nprint(round(R+dt*dR,4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.7: Agent-Based Modeling (Q33–Q36)
            // ═══════════════════════════════════════════════════════════════

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
**Agent state transitions**: each agent has a state (0=Susceptible, 1=Infected, 2=Recovered). Apply one round of rules:

- An S agent with at least one I neighbor (adjacent index) becomes I with probability p_infect (if u < p_infect)
- An I agent becomes R if its infection counter reaches max_days
- Counter increments each step

Read `p_infect`, `max_days`, then `n` agents each as `state days_infected` (space-separated), then `n` uniform random numbers u_i for infection draws (one per line).

Print the new state of each agent on one line.

Example:
```
Input:
0.8
3
5
0 0
1 2
0 0
1 1
0 0
0.7
0.0
0.9
0.0
0.0
Output:
1 1 0 1 1 (new states)
```

Wait — print each new state on separate lines.

Example output: each agent's new state (0,1,2), one per line.

Agent 0 (S): neighbor 1 is I → u=0.7<0.8 → becomes I
Agent 1 (I, days=2): days+1=3=max_days → becomes R
Agent 2 (S): neighbors are agents 1(I) and 3(I) → u=0.9≥0.8 → stays S
Agent 3 (I, days=1): days+1=2<3 → stays I
Agent 4 (S): neighbor 3 is I → u=0.0<0.8 → becomes I

```
Output:
1
2
0
1
1
```
MD,
                'starter_code'        => "p = float(input())\nmax_d = int(input())\nn = int(input())\nagents = []\nfor _ in range(n):\n    s,d = map(int,input().split())\n    agents.append([s,d])\nus = [float(input()) for _ in range(n)]\nnew_states = []\nfor i,((s,d),u) in enumerate(zip(agents,us)):\n    if s == 0:  # Susceptible\n        nbrs = [agents[j][0]==1 for j in [i-1,i+1] if 0<=j<n]\n        if any(nbrs) and u < p:\n            new_states.append(1)\n        else:\n            new_states.append(0)\n    elif s == 1:  # Infected\n        if d+1 >= max_d:\n            new_states.append(2)\n        else:\n            new_states.append(1)\n    else:\n        new_states.append(2)\nfor ns in new_states:\n    print(ns)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
**Agent movement on a grid**: agents on a 1D grid move left (−1), stay (0), or right (+1) based on a force rule. Each agent's new position is clipped to [0, grid_size−1].

Rule: if a neighbor is within radius `r`, move away; otherwise move towards center (grid_size/2).

Read `grid_size`, `r`, then `n` agent positions, then `n` agent positions of a second group (the "repellers"). Compute each original agent's movement and new position.

For each agent i at position p_i:
- If any repeller is within distance r: move = +1 if p_i < nearest_repeller, else −1
- Else: move = +1 if p_i < grid_size/2, else −1 (0 if at center)

Print new positions, one per line.

Example:
```
Input:
10
2
3
3
5
7
2
1
8
Output:
4
6
6
```
MD,
                'starter_code'        => "grid = int(input())\nr    = int(input())\nn    = int(input())\nagents = [int(input()) for _ in range(n)]\nm    = int(input())\nreps = [int(input()) for _ in range(m)]\ncenter = grid / 2\nfor p in agents:\n    # find nearest repeller\n    dists = [abs(p - rp) for rp in reps]\n    near_d = min(dists)\n    near_r = reps[dists.index(near_d)]\n    if near_d <= r:\n        move = 1 if p < near_r else -1\n    else:\n        if p < center: move = 1\n        elif p > center: move = -1\n        else: move = 0\n    new_p = max(0, min(grid-1, p+move))\n    print(new_p)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
**Schelling segregation model — one step**: agents on a line are `A` or `B` or empty (`_`). An agent is unhappy if fewer than `threshold` fraction of its non-empty neighbors (left + right) are the same type. Unhappy agents swap with random empty cells (given explicitly).

Read `threshold` (float), the grid as a string of characters (A/B/_), then a list of swap pairs `i j` meaning agent at i swaps with empty at j (one per line, terminated by `end`).

Print the new grid as a string.

Example:
```
Input:
0.5
AABB_
0 4
end
Output:
_ABBA (wait — swap A at index 0 with _ at index 4)
```
→ _ABB A → `_ABBA`
MD,
                'starter_code'        => "threshold = float(input())\ngrid = list(input().strip())\nwhile True:\n    line = input().strip()\n    if line == 'end': break\n    i,j = map(int,line.split())\n    grid[i],grid[j] = grid[j],grid[i]\nprint(''.join(grid))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
**Flocking (Boids) — alignment rule**: each agent's new velocity is the average velocity of its neighbors within radius `r`, normalized to unit length.

In 1D: new_vel_i = sign(mean velocity of neighbors within r), or 0 if no neighbors.

Read `r`, then `n` agents each as `position velocity` (integers). Print each agent's new velocity (−1, 0, or 1), one per line.

Example:
```
Input:
3
4
0 1
2 1
5 -1
9 -1
Output:
1
1
-1
-1
```
MD,
                'starter_code'        => "r = int(input())\nn = int(input())\nagents = [tuple(map(int,input().split())) for _ in range(n)]\nfor i,(pi,vi) in enumerate(agents):\n    nbr_vels = [agents[j][1] for j in range(n) if j!=i and abs(agents[j][0]-pi)<=r]\n    if not nbr_vels:\n        print(0)\n    else:\n        avg = sum(nbr_vels)/len(nbr_vels)\n        print(1 if avg>0 else (-1 if avg<0 else 0))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.8: System Dynamics & Feedback Loops (Q37–Q41)
            // ═══════════════════════════════════════════════════════════════

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
**Stock-and-flow simulation**: a stock `S` has inflow `f_in` and outflow `f_out` per time step.

S(t+1) = S(t) + f_in − f_out

Read `S0`, `f_in`, `f_out`, `n_steps`. Print S after each step.

Example:
```
Input:
100
10
5
4
Output:
105
110
115
120
```
MD,
                'starter_code'        => "S = float(input())\nfi= float(input())\nfo= float(input())\nns= int(input())\nfor _ in range(ns):\n    S += fi - fo\n    print(round(S,4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
**Negative feedback loop** (thermostat model):

dT/dt = −k × (T − T_set)

Euler: T_{n+1} = T_n + h × (−k × (T_n − T_set))

Read `T0`, `T_set`, `k`, `h`, `n_steps`. Print T after each step rounded to 4 decimal places.

Example:
```
Input:
80.0
20.0
0.1
1.0
5
Output:
74.0
68.6
63.74
59.366
55.4294
```
MD,
                'starter_code'        => "T   = float(input())\nTset= float(input())\nk   = float(input())\nh   = float(input())\nns  = int(input())\nfor _ in range(ns):\n    T = T + h*(-k*(T-Tset))\n    print(round(T,4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
**Positive feedback / exponential growth with saturation** (Bass diffusion model):

dN/dt = (p + q × N/M) × (M − N)

where p = innovation coefficient, q = imitation coefficient, M = market size.

Euler: N_{n+1} = N_n + h × (p + q×N_n/M) × (M − N_n)

Read `p`, `q`, `M`, `N0`, `h`, `n_steps`. Print N after each step rounded to 4 decimal places.

Example:
```
Input:
0.01
0.4
1000.0
10.0
1.0
4
Output:
20.836
42.857...
```

N1=10+1*(0.01+0.4*10/1000)*(1000-10)=10+(0.01+0.004)*990=10+13.86=23.86
```
Input:
0.01
0.4
1000.0
10.0
1.0
4
Output:
23.86
56.1635...
```
MD,
                'starter_code'        => "p=float(input());q=float(input());M=float(input())\nN=float(input());h=float(input());ns=int(input())\nfor _ in range(ns):\n    N=N+h*(p+q*N/M)*(M-N)\n    print(round(N,4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
**Causal loop diagram analysis**: given a list of causal links `A -> B sign` where sign is `+` or `-`, find all loops of length 2 (A→B→A) and classify each as `reinforcing` (both signs +, or both −) or `balancing` (mixed signs).

Read `n` links each as `source target sign`. Print each found loop as `A-B: reinforcing` or `A-B: balancing`, sorted alphabetically by the first variable name.

Example:
```
Input:
4
A B +
B A -
C D +
D C +
Output:
A-B: balancing
C-D: reinforcing
```
MD,
                'starter_code'        => "n = int(input())\nlinks = {}\nfor _ in range(n):\n    parts = input().split()\n    src, tgt, sgn = parts[0], parts[1], parts[2]\n    links[(src,tgt)] = sgn\nloops = []\nfor (a,b),s1 in links.items():\n    if (b,a) in links and a < b:\n        s2 = links[(b,a)]\n        kind = 'reinforcing' if s1==s2 else 'balancing'\n        loops.append(f'{a}-{b}: {kind}')\nfor l in sorted(loops):\n    print(l)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
**Predator-Prey (Lotka-Volterra) — Euler steps**:

dX/dt = α×X − β×X×Y
dY/dt = δ×X×Y − γ×Y

Read `alpha`, `beta`, `delta`, `gamma`, `X0`, `Y0`, `h`, `n_steps`. Print X and Y after each step, space-separated, rounded to 4 decimal places.

Example:
```
Input:
0.1
0.02
0.01
0.1
40.0
9.0
1.0
3
Output:
40.48 8.244
41.06 7.606
41.68 7.052
```
MD,
                'starter_code'        => "a=float(input());b=float(input());d=float(input());g=float(input())\nX=float(input());Y=float(input());h=float(input());ns=int(input())\nfor _ in range(ns):\n    dX=a*X-b*X*Y;dY=d*X*Y-g*Y\n    X+=h*dX;Y+=h*dY\n    print(round(X,4),round(Y,4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.9: Sensitivity Analysis & Model Validation (Q42–Q46)
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
**One-at-a-time (OAT) sensitivity**: compute the normalized sensitivity index of output Y with respect to parameter P:

S_P = (ΔY/Y_base) / (ΔP/P_base)

Read `Y_base`, `P_base`, `Y_perturbed`, `P_perturbed`. Print S_P rounded to 4 decimal places.

Example:
```
Input:
100.0
10.0
110.0
11.0
Output: 1.0
```
MD,
                'starter_code'        => "Yb=float(input());Pb=float(input());Yp=float(input());Pp=float(input())\nS=((Yp-Yb)/Yb)/((Pp-Pb)/Pb)\nprint(round(S,4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
**Tornado chart ranking**: given `n` parameters and their sensitivity indices, rank them by absolute sensitivity (descending) and print their names.

Read `n` pairs `name sensitivity`. Print names in descending order of |sensitivity|.

Example:
```
Input:
3
alpha 0.8
beta -1.5
gamma 0.3
Output:
beta
alpha
gamma
```
MD,
                'starter_code'        => "n=int(input())\nparams=[]\nfor _ in range(n):\n    parts=input().split()\n    params.append((parts[0],float(parts[1])))\nparams.sort(key=lambda x:-abs(x[1]))\nfor name,_ in params:\n    print(name)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
**Latin Hypercube Sampling (LHS) stratification check**: given `n` samples in [0,1] and `n` strata of equal width 1/n, verify that exactly one sample falls in each stratum.

Stratum i (0-indexed) covers [i/n, (i+1)/n).

Read `n`, then `n` sample values. Print `valid LHS` if one sample per stratum, else `invalid LHS`.

Example:
```
Input:
4
0.1
0.35
0.62
0.88
Output: valid LHS
```
MD,
                'starter_code'        => "import math\nn=int(input())\nsamples=[float(input()) for _ in range(n)]\ncounts=[0]*n\nfor s in samples:\n    idx=min(int(s*n),n-1)\n    counts[idx]+=1\nprint('valid LHS' if all(c==1 for c in counts) else 'invalid LHS')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
**Model validation — Mean Absolute Percentage Error (MAPE)**:

MAPE = (1/n) × Σ |actual_i − predicted_i| / |actual_i| × 100

Read `n`, then `n` pairs `actual predicted` (one per line). Print MAPE rounded to 4 decimal places followed by `%`.

Classify: MAPE < 10 → `excellent`, 10–20 → `good`, 20–50 → `acceptable`, > 50 → `poor`.

Print MAPE and classification on separate lines.

Example:
```
Input:
3
100 95
200 210
150 145
Output:
3.6667%
excellent
```
MD,
                'starter_code'        => "n=int(input())\ntotal=0.0\nfor _ in range(n):\n    a,p=map(float,input().split())\n    total+=abs(a-p)/abs(a)\nmape=total/n*100\nprint(f'{round(mape,4)}%')\nif mape<10:print('excellent')\nelif mape<20:print('good')\nelif mape<50:print('acceptable')\nelse:print('poor')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
**Sobol' first-order sensitivity index (simplified)**: given output values from a Morris screening design, compute the mean (μ) and standard deviation (σ) of elementary effects for each parameter.

Elementary effect EE_i = (Y(X + Δei) − Y(X)) / Δ

Read `n_params`, `delta`, then `n_params` pairs `Y_base Y_perturbed` per parameter (each pair on a line). For each parameter, print μ and σ of its elementary effects rounded to 4 decimal places.

For this simplified version, each parameter has exactly one elementary effect.

Example:
```
Input:
3
0.1
100 110
100 95
100 103
Output:
100.0 0.0
-50.0 0.0
30.0 0.0
```
MD,
                'starter_code'        => "np_=int(input())\ndelta=float(input())\nfor _ in range(np_):\n    yb,yp=map(float,input().split())\n    ee=(yp-yb)/delta\n    print(round(ee,4),0.0)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6.10: Simulation Output Analysis & Statistical Testing (Q47–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
**Batch means method** for steady-state confidence intervals.

Divide `n` observations into `b` batches. Compute batch means, then:
- Grand mean x̄
- Variance of batch means s²_b
- 95% CI half-width: t* × √(s²_b / b)

Use t* = 2.0 for all df (simplified).

Read `b`, then all `n = b × batch_size` observations (one per line). First line is `batch_size`. Print grand mean, half-width each rounded to 4 decimal places on separate lines.

Example:
```
Input:
4
3
1
2
3
4
5
6
7
8
9
10
11
12
Output:
6.5
2.1381
```
MD,
                'starter_code'        => "import math\nb=int(input())\nbs=int(input())\nall_obs=[float(input()) for _ in range(b*bs)]\nbatch_means=[sum(all_obs[i*bs:(i+1)*bs])/bs for i in range(b)]\ngrand=sum(batch_means)/b\nvar_b=sum((m-grand)**2 for m in batch_means)/(b-1)\nhw=2.0*math.sqrt(var_b/b)\nprint(round(grand,4))\nprint(round(hw,4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
**Warm-up period detection (Welch's method)**: compute moving averages of window size `w` and find the first index where the moving average is within `tol` of the overall mean. That index is the end of the warm-up period.

Read `w`, `tol`, then `n` observations. Print the warm-up end index (0-indexed, the first index where the centered moving average is within tol of total mean). If never within tol, print `n`.

Example:
```
Input:
3
5.0
10
50
40
30
20
10
12
11
10
9
11
Output: 4
```
(overall mean ≈ 20.3; moving avg starting at index 1 = [40,30,20]=30; at index 3 = [20,10,12]=14; at index 4 = [10,12,11]=11... first within 5 of mean?)

Simplified: compute MA[i] = mean(obs[i:i+w]) for i in 0..n-w. Print first i where |MA[i] - overall_mean| ≤ tol.

```
Input:
3
5.0
10
50
40
30
20
10
12
11
10
9
11
Output: 3
```
(overall mean=19.3; MA[0]=(50+40+30)/3=40; MA[1]=(40+30+20)/3=30; MA[2]=(30+20+10)/3=20; MA[3]=(20+10+12)/3=14; MA[4]=(10+12+11)/3=11; MA[5]=(12+11+10)/3=11; MA[6]=(11+10+9)/3=10; MA[7]=(10+9+11)/3=10; 14 vs 19.3 diff=5.3>5; 11 vs 19.3=8.3>5; never within 5 → print n=10)

Actually |MA[2]-19.3|=|20-19.3|=0.7≤5 → answer is 2.
MD,
                'starter_code'        => "w=int(input())\ntol=float(input())\nn=int(input())\nobs=[float(input()) for _ in range(n)]\noverall=sum(obs)/n\nresult=n\nfor i in range(n-w+1):\n    ma=sum(obs[i:i+w])/w\n    if abs(ma-overall)<=tol:\n        result=i\n        break\nprint(result)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
**Independent replications comparison**: given two sets of replication outputs, perform a two-sample t-test to compare their means.

Use Welch's t-test (unequal variances). Print t-statistic and decision at α=0.05 (use critical value 2.0 for simplicity).

Read `n1` values for system 1, then `n2` values for system 2. Print t rounded to 4 decimal places and `System 1 better`, `System 2 better`, or `No significant difference` (based on sign of t and rejection of H0).

If |t| ≤ 2.0: `No significant difference`.
If |t| > 2.0 and t > 0: `System 1 better` (mean1 > mean2).
If |t| > 2.0 and t < 0: `System 2 better`.

Example:
```
Input:
3
10
12
11
3
8
7
9
Output:
3.7417
System 1 better
```
MD,
                'starter_code'        => "import math\nn1=int(input())\ns1=[float(input()) for _ in range(n1)]\nn2=int(input())\ns2=[float(input()) for _ in range(n2)]\nm1=sum(s1)/n1;m2=sum(s2)/n2\nv1=sum((x-m1)**2 for x in s1)/(n1-1)\nv2=sum((x-m2)**2 for x in s2)/(n2-1)\nt=(m1-m2)/math.sqrt(v1/n1+v2/n2)\nprint(round(t,4))\nif abs(t)<=2.0:print('No significant difference')\nelif t>0:print('System 1 better')\nelse:print('System 2 better')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**Full simulation output report**: given `r` replications each producing a sequence of `n` observations (warm-up of `w` removed), compute:

1. For each replication: post-warm-up mean
2. Grand mean across replications
3. 95% CI half-width (t*=2.0, Welch approx)
4. Coefficient of variation (SD/mean × 100%)

Read `r`, `n`, `w`, then `r × n` observations row by row (n per row, space-separated). Print:
- `Grand mean: X` (4 decimal places)
- `Half-width: X` (4 decimal places)
- `CV: X%` (2 decimal places)

Example:
```
Input:
3
5
2
10 9 8 7 6
10 9 8 7 6
10 9 8 7 6
Output:
Grand mean: 7.0
Half-width: 0.0
CV: 0.0%
```
MD,
                'starter_code'        => "import math\nr=int(input());n=int(input());w=int(input())\nrep_means=[]\nfor _ in range(r):\n    obs=list(map(float,input().split()))\n    post=obs[w:]\n    rep_means.append(sum(post)/len(post))\ngrand=sum(rep_means)/r\nvar=sum((m-grand)**2 for m in rep_means)/(r-1) if r>1 else 0\nhw=2.0*math.sqrt(var/r)\ncv=(math.sqrt(var)/grand*100) if grand!=0 else 0\nprint(f'Grand mean: {round(grand,4)}')\nprint(f'Half-width: {round(hw,4)}')\nprint(f'CV: {round(cv,2)}%')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
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
        // 4. TEST CASES
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

        // Q1
        $seed(1, [
            ['input'=>"yes\nno",   'expected_output'=>"dynamic\ndeterministic",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"no\nyes",   'expected_output'=>"static\nstochastic",       'is_hidden'=>false,'order_index'=>2],
            ['input'=>"yes\nyes",  'expected_output'=>"dynamic\nstochastic",      'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"no\nno",    'expected_output'=>"static\ndeterministic",    'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q2
        $seed(2, [
            ['input'=>"1 1 -2\n1 1 -2",  'expected_output'=>"consistent",    'is_hidden'=>false,'order_index'=>1],
            ['input'=>"1 1 -2\n1 0 -2",  'expected_output'=>"inconsistent",  'is_hidden'=>false,'order_index'=>2],
            ['input'=>"2 0 -1\n2 0 -1",  'expected_output'=>"consistent",    'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"0 0 0\n1 0 0",    'expected_output'=>"inconsistent",  'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q3
        $seed(3, [
            ['input'=>"100.0",   'expected_output'=>"0.005",      'is_hidden'=>false,'order_index'=>1],
            ['input'=>"50.0",    'expected_output'=>"0.01",        'is_hidden'=>false,'order_index'=>2],
            ['input'=>"1000.0",  'expected_output'=>"0.0005",      'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"0.5",     'expected_output'=>"1.0",         'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q4
        $seed(4, [
            ['input'=>"3\nuses differential equations\nempirical fit\nneural network model",  'expected_output'=>"white-box\ngrey-box\nblack-box",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"2\nfirst principles physics\nlookup table",                            'expected_output'=>"white-box\nblack-box",             'is_hidden'=>false,'order_index'=>2],
            ['input'=>"1\npartial differential model",                                         'expected_output'=>"grey-box",                        'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"1\ndata-driven deep learning",                                         'expected_output'=>"black-box",                       'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q5
        $seed(5, [
            ['input'=>"10.0\n2.0",   'expected_output'=>"97",    'is_hidden'=>false,'order_index'=>1],
            ['input'=>"15.0\n3.0",   'expected_output'=>"97",    'is_hidden'=>false,'order_index'=>2],
            ['input'=>"20.0\n4.0",   'expected_output'=>"97",    'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"5.0\n0.5",    'expected_output'=>"385",   'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q6
        $seed(6, [
            ['input'=>"1\n5\n3\n16\n4",           'expected_output'=>"8\n11\n10\n5",         'is_hidden'=>false,'order_index'=>1],
            ['input'=>"0\n3\n1\n8\n4",            'expected_output'=>"1\n4\n5\n0",           'is_hidden'=>false,'order_index'=>2],
            ['input'=>"42\n1664525\n1013904223\n4294967296\n3",  'expected_output'=>"1084581853\n2157072522\n589249613", 'is_hidden'=>true,'order_index'=>3],
            ['input'=>"7\n5\n1\n32\n5",           'expected_output'=>"4\n21\n10\n19\n0",     'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q7
        $seed(7, [
            ['input'=>"2.0\n3\n0.5\n0.2\n0.8",   'expected_output'=>"0.346574\n0.804719\n0.111572",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"1.0\n2\n0.5\n0.1",         'expected_output'=>"0.693147\n2.302585",            'is_hidden'=>false,'order_index'=>2],
            ['input'=>"0.5\n2\n0.9\n0.3",         'expected_output'=>"0.210721\n2.407946",            'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"3.0\n1\n0.368",            'expected_output'=>"0.333684",                       'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q8
        $seed(8, [
            ['input'=>"3\n1 0.2\n2 0.5\n3 0.3\n4\n0.1\n0.5\n0.7\n0.99",  'expected_output'=>"1\n2\n3\n3",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"2\nH 0.5\nT 0.5\n3\n0.3\n0.6\n0.9",               'expected_output'=>"H\nT\nT",     'is_hidden'=>false,'order_index'=>2],
            ['input'=>"3\n1 0.1\n2 0.3\n3 0.6\n2\n0.15\n0.5",            'expected_output'=>"2\n3",         'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"2\na 0.4\nb 0.6\n2\n0.4\n0.41",                   'expected_output'=>"a\nb",         'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q9
        $seed(9, [
            ['input'=>"1\n0.5 0.5",          'expected_output'=>"-1.17741 0.0",      'is_hidden'=>false,'order_index'=>1],
            ['input'=>"1\n0.1 0.25",         'expected_output'=>"1.79832 -1.33524",  'is_hidden'=>false,'order_index'=>2],
            ['input'=>"1\n0.3679 0.5",       'expected_output'=>"-1.41421 0.0",      'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"1\n0.5 0.0",          'expected_output'=>"1.17741 0.0",       'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q10
        $seed(10, [
            ['input'=>"5\n1\n2\n3\n4\n5\n3\n2\n3\n5",   'expected_output'=>"0.4\n0.6\n1.0",    'is_hidden'=>false,'order_index'=>1],
            ['input'=>"4\n1\n2\n3\n4\n2\n0\n2.5",       'expected_output'=>"0.0\n0.5",          'is_hidden'=>false,'order_index'=>2],
            ['input'=>"3\n1\n3\n5\n2\n1\n4",            'expected_output'=>"0.3333\n0.6667",    'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"2\n10\n20\n1\n15",               'expected_output'=>"0.5",               'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q11
        $seed(11, [
            ['input'=>"4\n20\n30\n25\n25\n25\n25\n25\n25",  'expected_output'=>"1.0\nfail to reject",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"4\n10\n10\n10\n10\n10\n10\n10\n10",  'expected_output'=>"0.0\nfail to reject",  'is_hidden'=>false,'order_index'=>2],
            ['input'=>"2\n40\n10\n25\n25",                  'expected_output'=>"10.0\nreject",          'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"3\n30\n30\n40\n33\n33\n34",          'expected_output'=>"0.2424\nfail to reject",'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q12
        $seed(12, [
            ['input'=>"4\n0.1\n0.5\n0.7\n0.9",  'expected_output'=>"0.39",       'is_hidden'=>false,'order_index'=>1],
            ['input'=>"3\n0.0\n0.5\n1.0",       'expected_output'=>"0.416667",   'is_hidden'=>false,'order_index'=>2],
            ['input'=>"2\n0.5\n0.5",            'expected_output'=>"0.25",        'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"1\n1.0",                 'expected_output'=>"1.0",         'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q13
        $seed(13, [
            ['input'=>"4\n0.1 0.2\n0.9 0.9\n0.5 0.5\n0.3 0.4",  'expected_output'=>"3.0",    'is_hidden'=>false,'order_index'=>1],
            ['input'=>"4\n0.5 0.5\n0.5 0.5\n0.5 0.5\n0.5 0.5",  'expected_output'=>"4.0",    'is_hidden'=>false,'order_index'=>2],
            ['input'=>"2\n0.7 0.7\n0.9 0.9",                    'expected_output'=>"0.0",    'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"3\n0.3 0.3\n0.6 0.6\n0.9 0.9",          'expected_output'=>"0.0",    'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q14
        $seed(14, [
            ['input'=>"100\n100\n0.05\n0.2\n1.0\n3\n-0.5\n0.0\n1.5",  'expected_output'=>"7.965677",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"100\n90\n0.0\n0.0\n1.0\n1\n0.0",               'expected_output'=>"10.0",      'is_hidden'=>false,'order_index'=>2],
            ['input'=>"100\n110\n0.05\n0.2\n1.0\n2\n-1.0\n1.0",       'expected_output'=>"4.026819",  'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"50\n55\n0.0\n0.3\n0.5\n2\n0.5\n1.0",           'expected_output'=>"2.220571",  'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q15
        $seed(15, [
            ['input'=>"3\n0.5 0.9\n0.5 0.5\n0.1 0.8",  'expected_output'=>"accept\naccept\nreject",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"2\n0.5 1.0\n0.0 0.5",           'expected_output'=>"accept\naccept",           'is_hidden'=>false,'order_index'=>2],
            ['input'=>"2\n0.9 0.1\n0.1 0.9",           'expected_output'=>"reject\nreject",           'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"1\n0.25 0.49",                  'expected_output'=>"reject",                   'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q16
        $seed(16, [
            ['input'=>"3\n4\n1 1 1\n1 0 1\n1 1 0\n1 1 1",  'expected_output'=>"0.5\n0.5",   'is_hidden'=>false,'order_index'=>1],
            ['input'=>"2\n3\n1 1\n1 1\n1 1",              'expected_output'=>"1.0\n0.0",   'is_hidden'=>false,'order_index'=>2],
            ['input'=>"3\n2\n1 1 1\n0 1 1",              'expected_output'=>"0.5\n0.5",   'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"1\n4\n1\n0\n1\n0",                'expected_output'=>"0.5\n0.5",   'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q17
        $seed(17, [
            ['input'=>"0.5\n4\n0.3 0.4\n0.7 0.6\n0.5 0.5\n0.9 0.8",  'expected_output'=>"0.6",       'is_hidden'=>false,'order_index'=>1],
            ['input'=>"0.5\n2\n0.5 0.5\n0.5 0.5",                    'expected_output'=>"0.5",       'is_hidden'=>false,'order_index'=>2],
            ['input'=>"0.5\n3\n0.2 0.3\n0.6 0.5\n0.8 0.8",           'expected_output'=>"0.533333",  'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"1.0\n2\n0.9 0.9\n1.1 1.1",                    'expected_output'=>"1.0",       'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q18
        $seed(18, [
            ['input'=>"3.0\n4.0",   'expected_output'=>"0.75\n3.0\n1.0\n2.25\n0.75",           'is_hidden'=>false,'order_index'=>1],
            ['input'=>"1.0\n2.0",   'expected_output'=>"0.5\n1.0\n1.0\n0.5\n0.5",              'is_hidden'=>false,'order_index'=>2],
            ['input'=>"2.0\n5.0",   'expected_output'=>"0.4\n0.6667\n0.3333\n0.2667\n0.1333",  'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"5.0\n8.0",   'expected_output'=>"0.625\n1.6667\n0.3333\n1.0417\n0.2083",'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q19
        $seed(19, [
            ['input'=>"3\n0 3\n2 2\n6 1",         'expected_output'=>"0.0\n1.0\n0.0\n0.3333",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"2\n0 5\n3 2",              'expected_output'=>"0.0\n2.0\n1.0",          'is_hidden'=>false,'order_index'=>2],
            ['input'=>"3\n0 2\n0 3\n0 1",         'expected_output'=>"0.0\n2.0\n5.0\n2.3333",  'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"2\n0 4\n10 2",             'expected_output'=>"0.0\n0.0\n0.0",          'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q20
        $seed(20, [
            ['input'=>"2\n4\n0 3\n0 4\n1 2\n2 2",  'expected_output'=>"0.0\n0.0\n0.0\n1.0\n0.25",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"1\n3\n0 2\n1 3\n2 1",       'expected_output'=>"0.0\n0.0\n0.0\n0.0",         'is_hidden'=>false,'order_index'=>2],
            ['input'=>"3\n3\n0 2\n0 1\n0 3",       'expected_output'=>"0.0\n0.0\n0.0\n0.0",         'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"2\n4\n0 4\n0 4\n2 2\n2 2",  'expected_output'=>"0.0\n0.0\n4.0\n4.0\n2.0",   'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q21
        $seed(21, [
            ['input'=>"10\n3\n8\n5\n3\n0\n2\n1\n5",  'expected_output'=>"5 0\n2 0\n10 0\n8 0\n7 0\n2 0",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"5\n2\n4\n3\n2\n1",            'expected_output'=>"1 0\n4 0\n2 0\n1 0",              'is_hidden'=>false,'order_index'=>2],
            ['input'=>"10\n5\n10\n2\n3\n11",         'expected_output'=>"8 0\n5 0\n10 0\n7 4",             'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"8\n3\n6\n4\n2\n5",            'expected_output'=>"2 0\n8 0\n6 0\n3 2",              'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q22
        $seed(22, [
            ['input'=>"4\narrival 2.5\ndeparture 1.0\narrival 1.0\nservice 3.0",  'expected_output'=>"arrival: 1.0\ndeparture: 1.0\narrival: 2.5\nservice: 3.0",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"2\nstart 0.0\nend 5.0",                                   'expected_output'=>"start: 0.0\nend: 5.0",                                      'is_hidden'=>false,'order_index'=>2],
            ['input'=>"3\nc 1.5\na 1.5\nb 2.0",                                  'expected_output'=>"a: 1.5\nc: 1.5\nb: 2.0",                                    'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"2\nz 3.0\na 3.0",                                         'expected_output'=>"a: 3.0\nz: 3.0",                                            'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q23
        $seed(23, [
            ['input'=>"-1.0\n1.0\n0.1\n5",   'expected_output'=>"0.9\n0.81\n0.729\n0.6561\n0.59049",          'is_hidden'=>false,'order_index'=>1],
            ['input'=>"1.0\n1.0\n0.1\n3",    'expected_output'=>"1.1\n1.21\n1.331",                            'is_hidden'=>false,'order_index'=>2],
            ['input'=>"-0.5\n2.0\n0.2\n4",   'expected_output'=>"1.8\n1.62\n1.458\n1.3122",                    'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"0.0\n5.0\n1.0\n3",    'expected_output'=>"5.0\n5.0\n5.0",                               'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q24
        $seed(24, [
            ['input'=>"-1.0\n1.0\n0.1\n5",   'expected_output'=>"0.606531\n0.59049\n0.016041",   'is_hidden'=>false,'order_index'=>1],
            ['input'=>"1.0\n1.0\n0.1\n3",    'expected_output'=>"1.349859\n1.331\n0.018859",     'is_hidden'=>false,'order_index'=>2],
            ['input'=>"-2.0\n1.0\n0.1\n2",   'expected_output'=>"0.670320\n0.64\n0.03032",       'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"0.0\n5.0\n1.0\n1",    'expected_output'=>"5.0\n5.0\n0.0",                 'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q25
        $seed(25, [
            ['input'=>"-1.0\n1.0\n0.1\n3",   'expected_output'=>"0.90483742\n0.81873075\n0.74081822",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"1.0\n1.0\n0.2\n2",    'expected_output'=>"1.22139918\n1.4918247",               'is_hidden'=>false,'order_index'=>2],
            ['input'=>"-2.0\n1.0\n0.1\n2",   'expected_output'=>"0.81873075\n0.67032005",              'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"0.5\n2.0\n0.5\n2",    'expected_output'=>"2.56703097\n3.29744254",              'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q26
        $seed(26, [
            ['input'=>"0.5\n100.0\n10.0\n0.5\n4",   'expected_output'=>"12.25\n14.9378\n18.2366\n22.1375",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"0.1\n50.0\n5.0\n1.0\n3",     'expected_output'=>"5.455\n5.9505\n6.4913",             'is_hidden'=>false,'order_index'=>2],
            ['input'=>"1.0\n10.0\n1.0\n0.1\n3",     'expected_output'=>"1.09\n1.1881\n1.2951",             'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"0.2\n100.0\n50.0\n1.0\n2",   'expected_output'=>"59.0\n69.682",                     'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q27
        $seed(27, [
            ['input'=>"-2.0\n1.2",   'expected_output'=>"unstable",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"-2.0\n0.8",   'expected_output'=>"stable",    'is_hidden'=>false,'order_index'=>2],
            ['input'=>"-1.0\n2.5",   'expected_output'=>"unstable",  'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"-0.5\n2.0",   'expected_output'=>"stable",    'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q28
        $seed(28, [
            ['input'=>"990\n10\n0\n1000\n0.3\n0.1\n1.0",  'expected_output'=>"987.03\n11.97\n1.0",     'is_hidden'=>false,'order_index'=>1],
            ['input'=>"999\n1\n0\n1000\n0.5\n0.2\n1.0",   'expected_output'=>"998.5005\n1.2993\n0.2", 'is_hidden'=>false,'order_index'=>2],
            ['input'=>"500\n500\n0\n1000\n0.4\n0.1\n0.5", 'expected_output'=>"490.0\n507.5\n25.0",    'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"100\n0\n900\n1000\n0.3\n0.1\n1.0", 'expected_output'=>"100.0\n-90.0\n0.0",     'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q29
        $seed(29, [
            ['input'=>"990\n10\n0\n1000\n0.3\n0.1\n1.0\n3",  'expected_output'=>"987.03 11.97 1.0\n984.04 13.89 2.07\n980.99 15.75 3.26",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"999\n1\n0\n1000\n0.5\n0.25\n1.0\n2",  'expected_output'=>"998.5005 1.0493 0.25\n997.974 1.1168 0.5",               'is_hidden'=>false,'order_index'=>2],
            ['input'=>"990\n10\n0\n1000\n0.1\n0.05\n1.0\n2", 'expected_output'=>"989.01 10.44 0.5\n988.04 10.86 1.0",                     'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"800\n200\n0\n1000\n0.3\n0.1\n0.5\n2", 'expected_output'=>"787.6 207.6 10.0\n776.45 214.19 19.79",                  'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q30
        $seed(30, [
            ['input'=>"0.3\n0.1",   'expected_output'=>"3.0\nepidemic grows",     'is_hidden'=>false,'order_index'=>1],
            ['input'=>"0.1\n0.3",   'expected_output'=>"0.3333\nepidemic dies out",'is_hidden'=>false,'order_index'=>2],
            ['input'=>"0.5\n0.5",   'expected_output'=>"1.0\nendemic",             'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"2.0\n0.5",   'expected_output'=>"4.0\nepidemic grows",      'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q31
        $seed(31, [
            ['input'=>"0.3\n0.1\n1000",   'expected_output'=>"0.6667\n667",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"0.5\n0.25\n10000", 'expected_output'=>"0.5\n5000",    'is_hidden'=>false,'order_index'=>2],
            ['input'=>"2.0\n0.5\n500",    'expected_output'=>"0.75\n375",    'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"0.4\n0.1\n200",    'expected_output'=>"0.75\n150",    'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q32
        $seed(32, [
            ['input'=>"990\n5\n5\n0\n1000\n0.5\n0.2\n0.1\n1.0",  'expected_output'=>"987.525\n6.475\n5.5\n0.5",   'is_hidden'=>false,'order_index'=>1],
            ['input'=>"995\n0\n5\n0\n1000\n0.4\n0.3\n0.1\n1.0",  'expected_output'=>"993.0\n2.0\n5.4\n0.5",       'is_hidden'=>false,'order_index'=>2],
            ['input'=>"900\n50\n50\n0\n1000\n0.3\n0.2\n0.1\n0.5",'expected_output'=>"996.25\n52.5\n51.5\n2.5",    'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"999\n1\n0\n0\n1000\n0.2\n0.1\n0.05\n1.0", 'expected_output'=>"998.8002\n1.1",              'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q33
        $seed(33, [
            ['input'=>"0.8\n3\n5\n0 0\n1 2\n0 0\n1 1\n0 0\n0.7\n0.0\n0.9\n0.0\n0.0",  'expected_output'=>"1\n2\n0\n1\n1",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"1.0\n3\n3\n0 0\n1 1\n0 0\n0.5\n0.5\n0.5",                       'expected_output'=>"1\n2\n1",        'is_hidden'=>false,'order_index'=>2],
            ['input'=>"0.5\n2\n3\n0 0\n0 0\n1 1\n0.6\n0.6\n0.0",                       'expected_output'=>"0\n0\n2",        'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"0.3\n5\n2\n1 3\n0 0\n0.2\n0.5",                                 'expected_output'=>"1\n0",           'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q34
        $seed(34, [
            ['input'=>"10\n2\n3\n3\n5\n7\n2\n1\n8",    'expected_output'=>"4\n6\n6",   'is_hidden'=>false,'order_index'=>1],
            ['input'=>"10\n3\n2\n2\n8\n2\n0\n9",       'expected_output'=>"3\n7",       'is_hidden'=>false,'order_index'=>2],
            ['input'=>"20\n2\n3\n10\n12\n14\n1\n5",    'expected_output'=>"11\n11\n13", 'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"6\n1\n1\n3\n1\n4",              'expected_output'=>"2",           'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q35
        $seed(35, [
            ['input'=>"0.5\nAABB_\n0 4\nend",        'expected_output'=>"_ABBA",   'is_hidden'=>false,'order_index'=>1],
            ['input'=>"0.5\nAB_\nend",               'expected_output'=>"AB_",     'is_hidden'=>false,'order_index'=>2],
            ['input'=>"0.5\nA_B\n0 1\nend",          'expected_output'=>"_AB",     'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"0.5\nABBA_\n4 3\nend",        'expected_output'=>"ABB_A",   'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q36
        $seed(36, [
            ['input'=>"3\n4\n0 1\n2 1\n5 -1\n9 -1",   'expected_output'=>"1\n1\n-1\n-1",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"2\n2\n0 1\n5 -1",              'expected_output'=>"1\n-1",          'is_hidden'=>false,'order_index'=>2],
            ['input'=>"1\n3\n0 1\n1 1\n3 -1",         'expected_output'=>"1\n1\n-1",       'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"5\n2\n0 1\n10 -1",             'expected_output'=>"1\n-1",          'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q37
        $seed(37, [
            ['input'=>"100\n10\n5\n4",   'expected_output'=>"105.0\n110.0\n115.0\n120.0",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"50\n5\n5\n3",     'expected_output'=>"50.0\n50.0\n50.0",             'is_hidden'=>false,'order_index'=>2],
            ['input'=>"0\n3\n1\n5",      'expected_output'=>"2.0\n4.0\n6.0\n8.0\n10.0",    'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"100\n0\n10\n3",   'expected_output'=>"90.0\n80.0\n70.0",             'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q38
        $seed(38, [
            ['input'=>"80.0\n20.0\n0.1\n1.0\n5",   'expected_output'=>"74.0\n68.6\n63.74\n59.366\n55.4294",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"20.0\n20.0\n0.5\n1.0\n3",   'expected_output'=>"20.0\n20.0\n20.0",                    'is_hidden'=>false,'order_index'=>2],
            ['input'=>"100.0\n50.0\n0.2\n0.5\n4",  'expected_output'=>"95.0\n90.5\n86.475\n82.85125",        'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"0.0\n10.0\n0.3\n1.0\n3",    'expected_output'=>"3.0\n5.1\n6.57",                      'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q39
        $seed(39, [
            ['input'=>"0.01\n0.4\n1000.0\n10.0\n1.0\n4",   'expected_output'=>"23.86\n56.1635\n124.0403\n241.2131",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"0.03\n0.3\n100.0\n1.0\n1.0\n3",     'expected_output'=>"4.182\n16.6148\n55.8026",             'is_hidden'=>false,'order_index'=>2],
            ['input'=>"0.05\n0.5\n500.0\n5.0\n1.0\n2",     'expected_output'=>"28.6125\n146.7628",                   'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"0.02\n0.2\n200.0\n10.0\n0.5\n3",    'expected_output'=>"11.98\n14.2969\n17.0327",             'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q40
        $seed(40, [
            ['input'=>"4\nA B +\nB A -\nC D +\nD C +",     'expected_output'=>"A-B: balancing\nC-D: reinforcing",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"2\nX Y -\nY X -",                   'expected_output'=>"X-Y: reinforcing",                  'is_hidden'=>false,'order_index'=>2],
            ['input'=>"4\nP Q +\nQ P +\nR S -\nS R +",    'expected_output'=>"P-Q: reinforcing\nR-S: balancing",  'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"2\nA B +\nB C +",                   'expected_output'=>"",                                   'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q41
        $seed(41, [
            ['input'=>"0.1\n0.02\n0.01\n0.1\n40.0\n9.0\n1.0\n3",  'expected_output'=>"40.48 8.244\n41.06 7.606\n41.68 7.052",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"0.5\n0.1\n0.1\n0.5\n10.0\n5.0\n0.5\n2",    'expected_output'=>"11.25 4.9375\n12.3578 4.8535",           'is_hidden'=>false,'order_index'=>2],
            ['input'=>"0.4\n0.05\n0.05\n0.4\n20.0\n4.0\n1.0\n2",  'expected_output'=>"23.6 4.592\n27.3843 5.1861",            'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"0.3\n0.01\n0.02\n0.2\n100.0\n10.0\n0.1\n2",'expected_output'=>"103.0 9.998\n106.0879 9.998",           'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q42
        $seed(42, [
            ['input'=>"100.0\n10.0\n110.0\n11.0",   'expected_output'=>"1.0",    'is_hidden'=>false,'order_index'=>1],
            ['input'=>"100.0\n10.0\n120.0\n11.0",   'expected_output'=>"2.0",    'is_hidden'=>false,'order_index'=>2],
            ['input'=>"50.0\n5.0\n45.0\n4.5",       'expected_output'=>"1.0",    'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"200.0\n20.0\n210.0\n22.0",   'expected_output'=>"0.5",    'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q43
        $seed(43, [
            ['input'=>"3\nalpha 0.8\nbeta -1.5\ngamma 0.3",  'expected_output'=>"beta\nalpha\ngamma",    'is_hidden'=>false,'order_index'=>1],
            ['input'=>"2\nx 2.0\ny -3.0",                   'expected_output'=>"y\nx",                  'is_hidden'=>false,'order_index'=>2],
            ['input'=>"3\na -0.1\nb 0.5\nc -0.5",           'expected_output'=>"b\nc\na",               'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"1\np 0.0",                           'expected_output'=>"p",                     'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q44
        $seed(44, [
            ['input'=>"4\n0.1\n0.35\n0.62\n0.88",   'expected_output'=>"valid LHS",    'is_hidden'=>false,'order_index'=>1],
            ['input'=>"4\n0.1\n0.1\n0.62\n0.88",    'expected_output'=>"invalid LHS",  'is_hidden'=>false,'order_index'=>2],
            ['input'=>"3\n0.1\n0.45\n0.8",          'expected_output'=>"valid LHS",    'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"3\n0.1\n0.2\n0.8",           'expected_output'=>"invalid LHS",  'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q45
        $seed(45, [
            ['input'=>"3\n100 95\n200 210\n150 145",    'expected_output'=>"3.6667%\nexcellent",    'is_hidden'=>false,'order_index'=>1],
            ['input'=>"2\n100 85\n200 175",             'expected_output'=>"12.5%\ngood",            'is_hidden'=>false,'order_index'=>2],
            ['input'=>"2\n100 70\n200 150",             'expected_output'=>"25.0%\nacceptable",      'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"2\n100 40\n200 80",              'expected_output'=>"60.0%\npoor",             'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q46
        $seed(46, [
            ['input'=>"3\n0.1\n100 110\n100 95\n100 103",   'expected_output'=>"100.0 0.0\n-50.0 0.0\n30.0 0.0",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"2\n0.5\n10 15\n10 8",               'expected_output'=>"10.0 0.0\n-4.0 0.0",              'is_hidden'=>false,'order_index'=>2],
            ['input'=>"2\n0.1\n50 60\n50 45",              'expected_output'=>"100.0 0.0\n-50.0 0.0",            'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"1\n0.2\n100 100",                   'expected_output'=>"0.0 0.0",                         'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q47
        $seed(47, [
            ['input'=>"4\n3\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12",  'expected_output'=>"6.5\n2.1381",  'is_hidden'=>false,'order_index'=>1],
            ['input'=>"2\n4\n1\n2\n3\n4\n5\n6\n7\n8",                 'expected_output'=>"4.5\n2.0",     'is_hidden'=>false,'order_index'=>2],
            ['input'=>"3\n2\n10\n10\n10\n10\n10\n10",                 'expected_output'=>"10.0\n0.0",    'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"2\n3\n1\n2\n3\n7\n8\n9",                      'expected_output'=>"5.0\n4.0",     'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q48
        $seed(48, [
            ['input'=>"3\n5.0\n10\n50\n40\n30\n20\n10\n12\n11\n10\n9\n11",  'expected_output'=>"2",   'is_hidden'=>false,'order_index'=>1],
            ['input'=>"2\n1.0\n6\n10\n10\n10\n10\n10\n10",                  'expected_output'=>"0",   'is_hidden'=>false,'order_index'=>2],
            ['input'=>"3\n2.0\n5\n100\n50\n20\n15\n18",                     'expected_output'=>"2",   'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"2\n0.5\n4\n5\n5\n5\n5",                             'expected_output'=>"0",   'is_hidden'=>true, 'order_index'=>4],
        ]);

        // Q49
        $seed(49, [
            ['input'=>"3\n10\n12\n11\n3\n8\n7\n9",    'expected_output'=>"3.7417\nSystem 1 better",       'is_hidden'=>false,'order_index'=>1],
            ['input'=>"3\n5\n5\n5\n3\n5\n5\n5",       'expected_output'=>"0.0\nNo significant difference",'is_hidden'=>false,'order_index'=>2],
            ['input'=>"3\n1\n2\n3\n3\n8\n9\n10",      'expected_output'=>"-6.7082\nSystem 2 better",       'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"4\n10\n11\n10\n11\n4\n9\n10\n9\n10",'expected_output'=>"2.0\nNo significant difference",'is_hidden'=>true,'order_index'=>4],
        ]);

        // Q50
        $seed(50, [
            ['input'=>"3\n5\n2\n10 9 8 7 6\n10 9 8 7 6\n10 9 8 7 6",     'expected_output'=>"Grand mean: 7.0\nHalf-width: 0.0\nCV: 0.0%",     'is_hidden'=>false,'order_index'=>1],
            ['input'=>"2\n4\n1\n1 2 3 4\n5 6 7 8",                       'expected_output'=>"Grand mean: 5.0\nHalf-width: 4.0\nCV: 56.57%",   'is_hidden'=>false,'order_index'=>2],
            ['input'=>"3\n6\n3\n1 1 1 2 2 2\n3 3 3 4 4 4\n5 5 5 6 6 6", 'expected_output'=>"Grand mean: 4.0\nHalf-width: 4.0\nCV: 70.71%",   'is_hidden'=>true, 'order_index'=>3],
            ['input'=>"2\n3\n0\n10 10 10\n20 20 20",                     'expected_output'=>"Grand mean: 15.0\nHalf-width: 20.0\nCV: 47.14%", 'is_hidden'=>true, 'order_index'=>4],
        ]);

        $this->command->info('✅ Module 6 Coding (Professional) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}