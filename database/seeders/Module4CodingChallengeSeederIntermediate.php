<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 4 — Calculus for Machine Learning (Intermediate) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Intermediate tier
 *   2. coding_questions    — 50 questions covering foundational calculus in Python
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mirrors Module 4 curriculum):
 *   4.1  Real Numbers, Absolute Value & the Number Line
 *   4.2  Sequences & Their Limits
 *   4.3  Limits of Functions & Continuity
 *   4.4  The Derivative: Definition & Interpretation
 *   4.5  Applications of Derivatives: MVT, Extrema & Optimisation
 *   4.6  L'Hôpital's Rule & Indeterminate Forms
 *   4.7  The Definite Integral: Definition & Properties
 *   4.8  The Fundamental Theorem of Calculus
 *   4.9  Integration Techniques: u-Substitution & Integration by Parts
 *   4.10 Infinite Series & Convergence Tests
 *
 * Difficulty: Intermediate — numerical methods, finite differences, Riemann
 *             sums, root-finding, gradient descent; use only math + pure Python.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module4CodingChallengeSeederIntermediate extends Seeder
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

        $this->command->info('Creating Module 4 — Calculus for Machine Learning (Intermediate) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Calculus for Machine Learning',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply calculus concepts computationally in Python. Implement numerical differentiation, integration, limit estimation, root-finding, gradient descent, and series convergence — using only Python\'s built-in tools and the math module.',
                'time_limit_seconds' => 2700,
                'base_xp'            => 750,
                'order_index'        => 4,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.1: Real Numbers, Absolute Value & Number Line (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Compute the **absolute value** of a number and determine its position on the number line relative to zero.

Read a float `x`. Print:
- `|x|` rounded to 4 decimal places
- `positive`, `negative`, or `zero`

Example:
```
Input: -3.5
Output:
3.5
negative
```
MD,
                'starter_code'        => "x = float(input())\n# Print |x| and sign\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Compute the **distance** between two real numbers on the number line.

Distance = |a − b|

Read two floats `a` and `b`. Print the distance rounded to 4 decimal places.

Example:
```
Input:
-3
5
Output: 8.0
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\nprint(round(abs(a - b), 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Solve the **absolute value inequality** |x − c| ≤ r and print the solution interval [a, b].

|x − c| ≤ r  ⟺  c − r ≤ x ≤ c + r

Read `c` and `r`. Print `[<a>, <b>]` with values rounded to 4 decimal places.

Example:
```
Input:
3
2
Output: [1.0, 5.0]
```
MD,
                'starter_code'        => "c = float(input())\nr = float(input())\nprint(f'[{round(c-r,4)}, {round(c+r,4)}]')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Determine whether a number is **rational** (can be expressed as p/q with integers p, q, q≠0) or **irrational** by checking if it equals a simple fraction within a given tolerance.

Read a float `x` and tolerance `eps`. Check all denominators q from 1 to 1000: if any |x − round(x×q)/q| < eps, print `rational: <p>/<q>` (smallest such q). Else print `irrational`.

Example:
```
Input:
0.3333333333
1e-6
Output: rational: 1/3
```
MD,
                'starter_code'        => "x = float(input())\neps = float(input())\n# Check rationality via denominator search\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Compute the **supremum** and **infimum** of a finite set of real numbers and determine if each is attained (i.e., is a max/min).

Read `n` real numbers (one per line). Print:
- `Sup: <val>` and `Attained: yes/no`
- `Inf: <val>` and `Attained: yes/no`

(For finite sets, sup = max and inf = min, always attained.)

Example:
```
Input:
4
-1
3
0
-5
Output:
Sup: 3.0
Attained: yes
Inf: -5.0
Attained: yes
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Print sup and inf\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.2: Sequences & Their Limits (Q6–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Compute the first `n` terms of an **arithmetic sequence** and determine if it converges.

a_k = a₀ + k × d

An arithmetic sequence converges only if d = 0.

Read `a0`, `d`, and `n`. Print each term rounded to 4 dp on its own line, then `Converges` or `Diverges`.

Example:
```
Input:
1
2
5
Output:
1.0
3.0
5.0
7.0
9.0
Diverges
```
MD,
                'starter_code'        => "a0 = float(input())\nd = float(input())\nn = int(input())\nfor k in range(n):\n    print(round(a0 + k * d, 4))\nprint('Converges' if d == 0 else 'Diverges')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Compute the first `n` terms of a **geometric sequence** and estimate its limit.

a_k = a₀ × r^k

Rules:
- If |r| < 1: converges to 0
- If r = 1: converges to a₀
- Otherwise: diverges

Read `a0`, `r`, and `n`. Print each term rounded to 6 dp, then the limit or `Diverges`.

Example:
```
Input:
1
0.5
5
Output:
1.0
0.5
0.25
0.125
0.0625
Limit: 0
```
MD,
                'starter_code'        => "a0 = float(input())\nr = float(input())\nn = int(input())\nfor k in range(n):\n    print(round(a0 * r**k, 6))\n# Determine and print limit\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Estimate the limit of a sequence **numerically** by computing enough terms until consecutive terms differ by less than `eps`.

a_n is given as a Python-evaluable expression using `n` as the variable. You are given a safe set: the expression is one of these forms:
- `(n**2 + n) / n**2`
- `(3*n + 1) / (2*n - 1)`
- `(n**3) / (n**3 + n**2)`

Read the expression index (1, 2, or 3) and `eps`. Print the estimated limit rounded to 6 decimal places, or `Diverges` if the sequence grows without bound after 10000 terms.

Example:
```
Input:
1
1e-6
Output: 1.0
```
MD,
                'starter_code'        => "idx = int(input())\neps = float(input())\n\ndef a(n):\n    if idx == 1: return (n**2 + n) / n**2\n    if idx == 2: return (3*n + 1) / (2*n - 1)\n    return (n**3) / (n**3 + n**2)\n\n# Estimate limit numerically\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Compute the limit of the **Squeeze Theorem** example numerically.

The sequence is: a_n = sin(n) / n

Compute |a_n| for n = 1, 10, 100, 1000, 10000 and verify that it approaches 0.

Read nothing (fixed). Print each |a_n| rounded to 8 decimal places and then `Limit: 0`.

Example:
```
Output:
|a(1)|: 0.84147098
|a(10)|: 0.05440211
|a(100)|: 0.00506366
|a(1000)|: 0.00082688
|a(10000)|: 0.00030561
Limit: 0
```
MD,
                'starter_code'        => "import math\n\nfor exp in [0, 1, 2, 3, 4]:\n    n = 10**exp\n    print(f'|a({n})|: {abs(math.sin(n)/n):.8f}')\nprint('Limit: 0')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Determine whether a sequence is **monotonically increasing**, **monotonically decreasing**, or **neither**, given its first `n` terms.

Read `n` values (one per line). Print `increasing`, `decreasing`, or `neither`.

Example:
```
Input:
5
1
2
4
8
16
Output: increasing
```
MD,
                'starter_code'        => "n = int(input())\nterms = [float(input()) for _ in range(n)]\n# Classify monotonicity\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.3: Limits of Functions & Continuity (Q11–Q15)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Estimate the **limit of a function** at a point numerically using two-sided approach.

Evaluate f(x) at x = c ± h for h = 0.1, 0.01, 0.001, 0.0001. If left and right limits agree to within 1e-4, print the limit rounded to 4 dp; otherwise print `Limit does not exist`.

Function choices (by index):
1. f(x) = (x² − 1) / (x − 1), c = 1
2. f(x) = sin(x) / x, c = 0
3. f(x) = (sqrt(x) − 1) / (x − 1), c = 1

Read function index and `c` (ignored, fixed per function). Print the estimated limit.

Example:
```
Input:
1
1
Output: 2.0
```
MD,
                'starter_code'        => "import math\n\nidx = int(input())\nc = float(input())\n\ndef f(x):\n    if idx == 1: return (x**2 - 1) / (x - 1)\n    if idx == 2: return math.sin(x) / x\n    return (math.sqrt(x) - 1) / (x - 1)\n\n# Estimate limit numerically\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Check **continuity** of a piecewise function at a point.

f(x) = { x² + 1    if x < c
        { a         if x = c
        { 2x − 1   if x > c

f is continuous at c if lim_{x→c⁻} f(x) = f(c) = lim_{x→c⁺} f(x).

Read `c` and `a`. Print `Continuous` or `Discontinuous`, and if discontinuous, print the type: `removable`, `jump`, or `infinite`.

Example:
```
Input:
1
2
Output:
Continuous
```
MD,
                'starter_code'        => "c = float(input())\na = float(input())\n# Check continuity of piecewise function\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Verify the **Intermediate Value Theorem**: given a continuous function f on [a, b] and a target value y, find an x ∈ [a, b] such that f(x) ≈ y using bisection.

Function: f(x) = x³ − x − 2

Read `a`, `b`, and `y`. Print x rounded to 6 decimal places where f(x) ≈ y, or `IVT not applicable` if y is not between f(a) and f(b).

Example:
```
Input:
1
2
0
Output: 1.521380
```
MD,
                'starter_code'        => "def f(x): return x**3 - x - 2\n\na = float(input())\nb = float(input())\ny = float(input())\n# Bisection to find f(x) = y\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Identify the type of **discontinuity** of f(x) = 1/x at x = 0 by evaluating one-sided limits numerically.

Evaluate f(x) for x → 0⁺ (x = 0.1, 0.01, 0.001) and x → 0⁻ (x = −0.1, −0.01, −0.001).

Print the values for each approach (rounded to 2 dp), then classify: `removable`, `jump`, or `infinite`.

Example:
```
Output:
Right: 10.0 100.0 1000.0
Left: -10.0 -100.0 -1000.0
Type: infinite
```
MD,
                'starter_code'        => "def f(x): return 1 / x\n\nright = [0.1, 0.01, 0.001]\nleft = [-0.1, -0.01, -0.001]\n# Print one-sided limit values and classify\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Evaluate the **epsilon-delta definition** of a limit numerically.

Given f(x) = 2x + 1 and the claim that lim_{x→3} f(x) = 7, verify: for a given ε, find the largest δ (to 4 decimal places, checking δ = 0.5, 0.1, 0.01, 0.001) such that |x − 3| < δ ⟹ |f(x) − 7| < ε for a sample of x values.

Read `eps`. Print `delta: <val>` (the largest δ from the list that works) or `No delta found`.

Example:
```
Input: 0.2
Output: delta: 0.1
```
MD,
                'starter_code'        => "eps = float(input())\ndef f(x): return 2*x + 1\nc, L = 3.0, 7.0\n# Find largest delta from [0.5, 0.1, 0.01, 0.001]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.4: The Derivative: Definition & Interpretation (Q16–Q21)
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Compute the **derivative numerically** using the central difference formula.

f'(x) ≈ (f(x + h) − f(x − h)) / (2h)

Function choices (by index):
1. f(x) = x³
2. f(x) = sin(x)
3. f(x) = e^x
4. f(x) = ln(x)

Read function index, `x`, and `h`. Print the numerical derivative rounded to 6 decimal places.

Example:
```
Input:
1
2
0.001
Output: 12.0
```
MD,
                'starter_code'        => "import math\n\nidx = int(input())\nx = float(input())\nh = float(input())\n\ndef f(t):\n    if idx == 1: return t**3\n    if idx == 2: return math.sin(t)\n    if idx == 3: return math.exp(t)\n    return math.log(t)\n\n# Compute central difference derivative\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Compute the **slope of the tangent line** to f(x) = x² at a point and print the tangent line equation y = mx + b.

f'(x) = 2x (analytical), but compute it numerically using central difference with h = 1e-5.

Read `x0`. Print `y = <m>x + <b>` with m and b rounded to 4 dp.

Example:
```
Input: 3
Output: y = 6.0x + -9.0
```
MD,
                'starter_code'        => "x0 = float(input())\ndef f(x): return x**2\nh = 1e-5\n# Compute tangent line at x0\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Compute the **second derivative** numerically using the second-order central difference.

f''(x) ≈ (f(x+h) − 2f(x) + f(x−h)) / h²

Classify the point:
- f''(x) > 0 → `concave up`
- f''(x) < 0 → `concave down`
- f''(x) ≈ 0 → `inflection point (possible)`

Function choices: 1=x³, 2=sin(x), 3=e^x, 4=−x²

Read function index, `x`, `h`. Print the numerical second derivative (6 dp) and the classification.

Example:
```
Input:
4
2
0.001
Output:
-2.0
concave down
```
MD,
                'starter_code'        => "import math\n\nidx = int(input())\nx = float(input())\nh = float(input())\n\ndef f(t):\n    if idx == 1: return t**3\n    if idx == 2: return math.sin(t)\n    if idx == 3: return math.exp(t)\n    return -t**2\n\n# Compute second derivative and classify\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Estimate the **derivative from first principles** (limit definition) by computing the difference quotient for decreasing h values.

[f(x+h) − f(x)] / h  for h = 0.1, 0.01, 0.001, 0.0001

Use f(x) = x² and a given x.

Read `x`. Print the difference quotient for each h rounded to 6 dp, then print the exact derivative `Exact: <val>`.

Example:
```
Input: 3
Output:
h=0.1: 6.1
h=0.01: 6.01
h=0.001: 6.001
h=0.0001: 6.0001
Exact: 6.0
```
MD,
                'starter_code'        => "x = float(input())\ndef f(t): return t**2\nfor h in [0.1, 0.01, 0.001, 0.0001]:\n    dq = (f(x + h) - f(x)) / h\n    print(f'h={h}: {round(dq, 6)}')\nprint(f'Exact: {round(2*x, 4)}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Compute the **gradient** (vector of partial derivatives) of a multivariate function numerically.

f(x, y) = x² + 3xy + y²

∂f/∂x ≈ (f(x+h, y) − f(x−h, y)) / (2h)
∂f/∂y ≈ (f(x, y+h) − f(x, y−h)) / (2h)

Read `x`, `y`, `h`. Print `∂f/∂x: <val>` and `∂f/∂y: <val>` each rounded to 4 dp.

Example:
```
Input:
1
2
0.001
Output:
df/dx: 8.0
df/dy: 7.0
```
MD,
                'starter_code'        => "x = float(input())\ny = float(input())\nh = float(input())\n\ndef f(a, b): return a**2 + 3*a*b + b**2\n\n# Compute partial derivatives numerically\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Apply the **chain rule** numerically to compute the derivative of a composite function.

g(x) = f(u(x)) where u(x) = x² and f(u) = sin(u).

g'(x) = f'(u(x)) × u'(x)

Compute g'(x) numerically using central differences (h = 1e-5) and compare with the analytical value 2x × cos(x²).

Read `x`. Print numerical g'(x) and analytical g'(x), both rounded to 6 dp.

Example:
```
Input: 1
Output:
Numerical: 1.080604
Analytical: 1.080604
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\nh = 1e-5\ndef g(t): return math.sin(t**2)\n# Compute numerical and analytical g'(x)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.5: Applications of Derivatives (Q22–Q27)
            // ═══════════════════════════════════════════════════════════════

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Find a **local extremum** of f(x) = x³ − 3x using Newton's method on f'(x) = 0.

f'(x) = 3x² − 3,  f''(x) = 6x

x_{n+1} = x_n − f'(x_n) / f''(x_n)

Read initial guess `x0` and `n_iter` (number of iterations). Print the root of f' rounded to 6 dp and classify: `local minimum` or `local maximum` based on the sign of f''.

Example:
```
Input:
0.5
10
Output:
x: 1.0
local minimum
```
MD,
                'starter_code'        => "x = float(input())\nn_iter = int(input())\ndef fp(t): return 3*t**2 - 3\ndef fpp(t): return 6*t\n# Newton's method on f'\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Apply the **Mean Value Theorem**: find `c` ∈ (a, b) such that f'(c) = [f(b)−f(a)]/(b−a).

f(x) = x²

Find c numerically (the MVT guarantees c = (a+b)/2 for quadratics; verify with numerical derivative).

Read `a` and `b`. Print the MVT slope, the value `c`, and f'(c) rounded to 4 dp each.

Example:
```
Input:
1
3
Output:
MVT slope: 4.0
c: 2.0
f'(c): 4.0
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\ndef f(x): return x**2\ndef fp(x): return 2*x\n# Compute MVT slope and find c\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Implement **gradient descent** to minimise f(x) = (x − 3)².

Update: x_{n+1} = x_n − α × f'(x_n)

f'(x) = 2(x − 3) (computed numerically with h = 1e-5)

Read `x0` (initial guess), `alpha` (learning rate), and `n_iter`. Print x after each step rounded to 6 dp. Stop early if |f'(x)| < 1e-8.

Example:
```
Input:
0
0.1
5
Output:
x1: 0.6
x2: 1.08
x3: 1.464
x4: 1.7712
x5: 2.01696
```
MD,
                'starter_code'        => "x = float(input())\nalpha = float(input())\nn_iter = int(input())\nh = 1e-5\ndef f(t): return (t - 3)**2\ndef fp(t): return (f(t+h) - f(t-h)) / (2*h)\n# Gradient descent loop\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Solve an **optimisation problem**: find the dimensions of a rectangle with fixed perimeter P that maximises area.

Area = x × (P/2 − x), maximised at x = P/4 (a square).

Read `P`. Print the optimal width x, height y, and maximum area — each rounded to 4 dp.

Example:
```
Input: 20
Output:
Width: 5.0
Height: 5.0
Max Area: 25.0
```
MD,
                'starter_code'        => "P = float(input())\n# Optimal rectangle dimensions\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Use **Rolle's Theorem**: given f(a) = f(b), verify that f'(c) = 0 for some c ∈ (a, b).

f(x) = x² − 4x + 3  (roots at x=1 and x=3)

Find c numerically using bisection on f'(x) = 2x − 4 = 0.

Read `a` and `b` (where f(a) = f(b)). Print the value of c rounded to 6 dp and confirm `f'(c) ≈ 0`.

Example:
```
Input:
1
3
Output:
c: 2.0
f'(c) ≈ 0
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\ndef f(x): return x**2 - 4*x + 3\ndef fp(x): return 2*x - 4\n# Find c where f'(c) = 0\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Implement **Newton-Raphson root finding** for f(x) = x³ − 2.

x_{n+1} = x_n − f(x_n) / f'(x_n)

f'(x) = 3x² (computed analytically here, but implement with numerical derivative h=1e-7 for generality)

Read `x0` and maximum iterations `max_iter`. Stop when |f(x)| < 1e-9. Print the root rounded to 8 dp and the number of iterations taken.

Example:
```
Input:
1.5
20
Output:
Root: 1.25992105
Iterations: 5
```
MD,
                'starter_code'        => "x = float(input())\nmax_iter = int(input())\ndef f(t): return t**3 - 2\nh = 1e-7\ndef fp(t): return (f(t+h) - f(t-h)) / (2*h)\n# Newton-Raphson iteration\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.6: L'Hôpital's Rule & Indeterminate Forms (Q28–Q32)
            // ═══════════════════════════════════════════════════════════════

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Apply **L'Hôpital's Rule** numerically to evaluate a 0/0 indeterminate form.

Estimate lim_{x→c} f(x)/g(x) by computing f'(c)/g'(c) using central differences (h=1e-5).

Function pairs (index):
1. f(x)=sin(x), g(x)=x, c=0
2. f(x)=x²−1, g(x)=x−1, c=1
3. f(x)=ln(x), g(x)=x−1, c=1

Read the index. Print the L'Hôpital limit rounded to 4 dp.

Example:
```
Input: 1
Output: 1.0
```
MD,
                'starter_code'        => "import math\n\nidx = int(input())\n\nif idx == 1:\n    f = lambda x: math.sin(x)\n    g = lambda x: x\n    c = 0.0\nelif idx == 2:\n    f = lambda x: x**2 - 1\n    g = lambda x: x - 1\n    c = 1.0\nelse:\n    f = lambda x: math.log(x)\n    g = lambda x: x - 1\n    c = 1.0\n\nh = 1e-5\n# Compute f'(c)/g'(c)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Evaluate the **∞/∞ indeterminate form** numerically.

lim_{x→∞} (3x² + 2x) / (5x² − x)

Compute the expression for x = 100, 1000, 10000, 100000. Print each value (6 dp) and the estimated limit rounded to 4 dp.

Example:
```
Output:
x=100: 0.612348
x=1000: 0.601200
x=10000: 0.60012
x=100000: 0.600012
Limit: 0.6
```
MD,
                'starter_code'        => "def f(x): return (3*x**2 + 2*x) / (5*x**2 - x)\nfor exp in [2, 3, 4, 5]:\n    x = 10**exp\n    print(f'x={x}: {round(f(x), 6)}')\nprint(f'Limit: {round(3/5, 4)}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Evaluate the **0 × ∞** indeterminate form: lim_{x→0⁺} x × ln(x).

Rewrite as lim_{x→0⁺} ln(x) / (1/x), then apply L'Hôpital: lim = (1/x) / (−1/x²) = −x → 0.

Compute x × ln(x) for x = 0.1, 0.01, 0.001, 0.0001. Print each rounded to 8 dp, then `Limit: 0`.

Example:
```
Output:
x=0.1: -0.23025851
x=0.01: -0.04605170
x=0.001: -0.00690776
x=0.0001: -0.00092103
Limit: 0
```
MD,
                'starter_code'        => "import math\n\nfor exp in [1, 2, 3, 4]:\n    x = 10**(-exp)\n    print(f'x={x}: {round(x * math.log(x), 8)}')\nprint('Limit: 0')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Evaluate **lim_{x→∞} (1 + 1/x)^x = e** numerically.

Compute (1 + 1/x)^x for x = 10, 100, 1000, 10000, 100000.

Print each value rounded to 8 dp, then `Limit: 2.71828183` (e rounded to 8 dp).

Example:
```
Output:
x=10: 2.59374246
x=100: 2.70481383
x=1000: 2.71692393
x=10000: 2.71814593
x=100000: 2.71826824
Limit: 2.71828183
```
MD,
                'starter_code'        => "import math\n\nfor exp in [1, 2, 3, 4, 5]:\n    x = 10**exp\n    print(f'x={x}: {round((1 + 1/x)**x, 8)}')\nprint(f'Limit: {round(math.e, 8)}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Apply **L'Hôpital's Rule twice** to evaluate lim_{x→0} (x − sin(x)) / x³.

L'Hôpital → lim (1 − cos(x)) / (3x²)
L'Hôpital → lim sin(x) / (6x) = 1/6.

Compute numerically for x = 0.1, 0.01, 0.001. Print each value (8 dp) and `Limit: 0.16666667`.

Example:
```
Output:
x=0.1: 0.16658341
x=0.01: 0.16666658
x=0.001: 0.16666667
Limit: 0.16666667
```
MD,
                'starter_code'        => "import math\n\nfor exp in [1, 2, 3]:\n    x = 10**(-exp)\n    val = (x - math.sin(x)) / x**3\n    print(f'x={x}: {round(val, 8)}')\nprint('Limit: 0.16666667')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.7: The Definite Integral: Definition & Properties (Q33–Q37)
            // ═══════════════════════════════════════════════════════════════

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Approximate the definite integral using **Left, Right, and Midpoint Riemann sums** with `n` subintervals.

∫_a^b f(x) dx  where f(x) = x²

Read `a`, `b`, and `n`. Print Left, Right, and Midpoint sums rounded to 6 dp.

Example:
```
Input:
0
1
100
Output:
Left: 0.32835
Right: 0.33835
Midpoint: 0.333325
```
MD,
                'starter_code'        => "def f(x): return x**2\n\na = float(input())\nb = float(input())\nn = int(input())\ndx = (b - a) / n\n# Compute Left, Right, Midpoint sums\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Approximate the definite integral using the **Trapezoidal Rule** and **Simpson's Rule**.

∫_a^b f(x) dx  where f(x) = sin(x)

Trapezoidal: (dx/2) × [f(x₀) + 2Σf(xᵢ) + f(xₙ)]
Simpson's: (dx/3) × [f(x₀) + 4f(x₁) + 2f(x₂) + ... + f(xₙ)]  (n must be even)

Read `a`, `b`, `n` (even). Print Trapezoidal and Simpson results rounded to 8 dp.

Example:
```
Input:
0
3.141592653589793
100
Output:
Trapezoidal: 1.99998355
Simpson: 2.0
```
MD,
                'starter_code'        => "import math\n\ndef f(x): return math.sin(x)\n\na = float(input())\nb = float(input())\nn = int(input())\ndx = (b - a) / n\nx = [a + i * dx for i in range(n + 1)]\n# Compute Trapezoidal and Simpson\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Verify **properties of definite integrals** numerically.

Use f(x) = x² on [a, b] with n=1000 subintervals (midpoint rule):

1. Reversal: ∫_a^b f = −∫_b^a f
2. Additivity: ∫_a^c f + ∫_c^b f = ∫_a^b f (with c ∈ (a,b))
3. Constant multiple: ∫_a^b k×f = k × ∫_a^b f

Read `a`, `b`, `c` (midpoint), `k`. Print the three identities verified: `Reversal: yes`, `Additivity: yes`, `Constant: yes`.

Example:
```
Input:
0
2
1
3
Output:
Reversal: yes
Additivity: yes
Constant: yes
```
MD,
                'starter_code'        => "def f(x): return x**2\n\ndef midpoint(a, b, n=1000):\n    dx = (b - a) / n\n    return sum(f(a + (i + 0.5) * dx) for i in range(n)) * dx\n\na = float(input())\nb = float(input())\nc = float(input())\nk = float(input())\n# Verify three properties\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Compute the **area between two curves** f(x) = x² and g(x) = x on [0, 1].

Area = ∫_0^1 |f(x) − g(x)| dx

Use Simpson's Rule with n=1000 subintervals.

Print the area rounded to 6 decimal places.

Example:
```
Output: 0.166667
```
MD,
                'starter_code'        => "def f(x): return x**2\ndef g(x): return x\n\nn = 1000\na, b = 0.0, 1.0\ndx = (b - a) / n\nx = [a + i * dx for i in range(n + 1)]\n# Simpson's rule for |f-g|\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Compute the **average value** of f(x) = cos(x) on [a, b].

Average value = (1/(b−a)) × ∫_a^b f(x) dx

Use Simpson's Rule with n=100.

Read `a` and `b`. Print the average value rounded to 6 dp.

Example:
```
Input:
0
1.5707963267948966
Output: 0.636620
```
MD,
                'starter_code'        => "import math\n\ndef f(x): return math.cos(x)\n\na = float(input())\nb = float(input())\nn = 100\ndx = (b - a) / n\n# Average value via Simpson's Rule\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.8: The Fundamental Theorem of Calculus (Q38–Q41)
            // ═══════════════════════════════════════════════════════════════

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Verify **FTC Part 1**: d/dx [∫_a^x f(t) dt] = f(x).

F(x) = ∫_0^x t² dt  →  F'(x) = x²

Numerically compute F(x) using Simpson's rule, then F'(x) via central difference, and compare with f(x).

Read `x`. Print F(x), F'(x), f(x) — all rounded to 6 dp — and `FTC verified: yes`.

Example:
```
Input: 2
Output:
F(x): 2.666667
F'(x): 4.0
f(x): 4.0
FTC verified: yes
```
MD,
                'starter_code'        => "import math\n\ndef f(t): return t**2\n\ndef F(x, n=1000):\n    if x == 0: return 0.0\n    dx = x / n\n    pts = [0 + i * dx for i in range(n + 1)]\n    return sum((f(pts[i]) + f(pts[i+1])) / 2 * dx for i in range(n))\n\nx = float(input())\nh = 1e-5\n# Print F(x), F'(x), f(x)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Apply **FTC Part 2**: ∫_a^b f(x) dx = F(b) − F(a).

Given:
- f(x) = 3x²  →  F(x) = x³

Read `a` and `b`. Print:
- `F(b) - F(a): <val>` (analytical)
- `Numerical: <val>` (Simpson with n=1000)
- `Match: yes/no` (within 1e-4)

Example:
```
Input:
1
3
Output:
F(b) - F(a): 26.0
Numerical: 26.0
Match: yes
```
MD,
                'starter_code'        => "def f(x): return 3 * x**2\ndef F(x): return x**3\n\na = float(input())\nb = float(input())\nn = 1000\ndx = (b - a) / n\npts = [a + i * dx for i in range(n + 1)]\n# Compute analytical and numerical\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Compute the **antiderivative numerically** using cumulative trapezoidal integration (cumulative sum of areas).

Given f(x) = 2x on an evenly spaced grid from `a` to `b` with `n` steps, compute F(xᵢ) = ∫_a^{xᵢ} f(t) dt for each grid point.

Read `a`, `b`, `n`. Print F(xᵢ) for i=0,1,...,n rounded to 4 dp (one per line).

Example:
```
Input:
0
2
4
Output:
0.0
0.25
1.0
2.25
4.0
```
MD,
                'starter_code'        => "def f(x): return 2 * x\n\na = float(input())\nb = float(input())\nn = int(input())\ndx = (b - a) / n\n# Cumulative trapezoidal integration\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Compute a **definite integral with variable upper limit** using FTC numerically.

G(x) = ∫_1^x (1/t) dt = ln(x)

Verify for x = 2, 3, 5, 10 by comparing numerical integration with math.log(x).

Print for each x: `G(x)=<numerical>  ln(x)=<exact>  diff=<|diff|>` all rounded to 6 dp.

Example:
```
Output:
x=2: G(x)=0.693147  ln(x)=0.693147  diff=0.0
x=3: G(x)=1.098612  ln(x)=1.098612  diff=0.0
x=5: G(x)=1.609438  ln(x)=1.609438  diff=0.0
x=10: G(x)=2.302585  ln(x)=2.302585  diff=0.0
```
MD,
                'starter_code'        => "import math\n\ndef f(t): return 1 / t\n\ndef G(x, n=10000):\n    a = 1.0\n    dx = (x - a) / n\n    pts = [a + i * dx for i in range(n + 1)]\n    return sum((f(pts[i]) + f(pts[i+1])) / 2 * dx for i in range(n))\n\nfor x in [2, 3, 5, 10]:\n    g = round(G(x), 6)\n    l = round(math.log(x), 6)\n    print(f'x={x}: G(x)={g}  ln(x)={l}  diff={round(abs(g-l), 6)}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.9: Integration Techniques (Q42–Q46)
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Apply **u-substitution** numerically.

∫_a^b f(g(x)) × g'(x) dx = ∫_{g(a)}^{g(b)} f(u) du

Compute both sides numerically using Simpson's rule (n=1000) and verify they match.

Use: f(u) = u², g(x) = x³, g'(x) = 3x²

Read `a` and `b`. Print the left integral, the right integral, and `Match: yes/no` (within 1e-4).

Example:
```
Input:
0
1
Output:
Left: 0.428571
Right: 0.428571
Match: yes
```
MD,
                'starter_code'        => "import math\n\ndef f(u): return u**2\ndef g(x): return x**3\ndef gp(x): return 3*x**2\n\na = float(input())\nb = float(input())\nn = 1000\n# Compute both sides of u-substitution\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Apply **integration by parts** numerically.

∫_a^b u(x) v'(x) dx = [u(x)v(x)]_a^b − ∫_a^b u'(x) v(x) dx

Use: u(x) = x, v'(x) = e^x (so v(x) = e^x, u'(x) = 1)

∫_a^b x e^x dx = [xe^x]_a^b − ∫_a^b e^x dx = [xe^x − e^x]_a^b = [(x−1)e^x]_a^b

Read `a` and `b`. Print the analytical result and numerical Simpson result (n=1000), each rounded to 6 dp, and `Match: yes/no`.

Example:
```
Input:
0
1
Output:
Analytical: 1.0
Numerical: 1.0
Match: yes
```
MD,
                'starter_code'        => "import math\n\ndef f(x): return x * math.exp(x)\n\na = float(input())\nb = float(input())\nanalytical = round((b - 1) * math.exp(b) - (a - 1) * math.exp(a), 6)\nn = 1000\ndx = (b - a) / n\n# Compute Simpson's and print results\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Compute an **improper integral** numerically by truncating the infinite limit.

∫_1^∞ 1/x² dx = 1  (converges)

Approximate by ∫_1^M 1/x² dx using Simpson's rule with n=10000 for M = 10, 100, 1000, 10000.

Print each approximation rounded to 6 dp and `True value: 1.0`.

Example:
```
Output:
M=10: 0.9
M=100: 0.99
M=1000: 0.999
M=10000: 0.9999
True value: 1.0
```
MD,
                'starter_code'        => "def f(x): return 1 / x**2\n\nfor M in [10, 100, 1000, 10000]:\n    n = 10000\n    a, b = 1.0, float(M)\n    dx = (b - a) / n\n    pts = [a + i * dx for i in range(n + 1)]\n    # Simpson's Rule\n    s = f(pts[0]) + f(pts[-1])\n    for i in range(1, n):\n        s += 4 * f(pts[i]) if i % 2 else 2 * f(pts[i])\n    result = s * dx / 3\n    print(f'M={M}: {round(result, 6)}')\nprint('True value: 1.0')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Compute the **arc length** of a curve y = f(x) from a to b.

L = ∫_a^b √(1 + [f'(x)]²) dx

Use f(x) = x² and numerical differentiation + Simpson's rule (n=1000).

Read `a` and `b`. Print the arc length rounded to 6 dp.

Example:
```
Input:
0
1
Output: 1.478943
```
MD,
                'starter_code'        => "import math\n\ndef f(x): return x**2\nh = 1e-5\ndef fp(x): return (f(x+h) - f(x-h)) / (2*h)\ndef integrand(x): return math.sqrt(1 + fp(x)**2)\n\na = float(input())\nb = float(input())\nn = 1000\ndx = (b - a) / n\n# Simpson's Rule for arc length\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Compute the **volume of a solid of revolution** using the disk method.

V = π × ∫_a^b [f(x)]² dx

Use f(x) = √x (so V = π∫_a^b x dx = π[x²/2]_a^b).

Read `a` and `b`. Print:
- `Analytical: <val>`
- `Numerical: <val>` (Simpson with n=1000)

Both rounded to 6 dp.

Example:
```
Input:
0
4
Output:
Analytical: 25.132741
Numerical: 25.132741
```
MD,
                'starter_code'        => "import math\n\ndef f(x): return math.sqrt(x)\ndef integrand(x): return math.pi * f(x)**2\n\na = float(input())\nb = float(input())\nanalytical = round(math.pi * (b**2 - a**2) / 2, 6)\nn = 1000\ndx = (b - a) / n\n# Simpson's Rule for volume\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.10: Infinite Series & Convergence Tests (Q47–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Compute the partial sums of the **geometric series** and estimate the sum.

S = Σ_{k=0}^{∞} r^k = 1/(1−r) for |r| < 1

Read `r` and `n` (number of terms). Print:
- Partial sum S_n rounded to 8 dp
- Exact sum (if |r|<1) or `Diverges`

Example:
```
Input:
0.5
10
Output:
S_n: 1.99804688
Exact: 2.0
```
MD,
                'starter_code'        => "r = float(input())\nn = int(input())\ns = sum(r**k for k in range(n))\nprint(f'S_n: {round(s, 8)}')\nif abs(r) < 1:\n    print(f'Exact: {round(1/(1-r), 4)}')\nelse:\n    print('Diverges')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Apply the **ratio test** to determine series convergence.

L = lim_{n→∞} |a_{n+1} / a_n|

- L < 1: Converges absolutely
- L > 1: Diverges
- L = 1: Inconclusive

Series choices (index):
1. a_n = 1/n!  (L = 0, converges)
2. a_n = n!/n^n  (L = 1/e, converges)
3. a_n = n^n/n!  (L = e, diverges)

Read the index. Compute the ratio |a_{n+1}/a_n| for n = 10, 100. Print both ratios (6 dp), L (4 dp), and the conclusion.

Example:
```
Input: 1
Output:
Ratio at n=10: 0.090909
Ratio at n=100: 0.009901
L: 0.0
Converges absolutely
```
MD,
                'starter_code'        => "import math\n\nidx = int(input())\n\ndef a(n):\n    if idx == 1: return 1 / math.factorial(n)\n    if idx == 2: return math.factorial(n) / n**n\n    return n**n / math.factorial(n)\n\nfor n in [10, 100]:\n    ratio = abs(a(n+1) / a(n))\n    print(f'Ratio at n={n}: {round(ratio, 6)}')\n# Determine L and conclusion\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Compute the **Taylor series** approximation of e^x around x=0 and measure the error.

e^x ≈ Σ_{k=0}^{n} x^k / k!

Read `x` and `n` (number of terms). Print:
- Taylor approximation rounded to 8 dp
- Exact value math.exp(x) rounded to 8 dp
- Absolute error rounded to 8 dp

Example:
```
Input:
1
10
Output:
Taylor: 2.71828153
Exact: 2.71828183
Error: 0.00000030
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\nn = int(input())\ntaylor = sum(x**k / math.factorial(k) for k in range(n))\nexact = math.exp(x)\nprint(f'Taylor: {round(taylor, 8)}')\nprint(f'Exact: {round(exact, 8)}')\nprint(f'Error: {round(abs(exact - taylor), 8)}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Apply the **integral test** to determine convergence of the p-series Σ 1/n^p.

∫_1^∞ 1/x^p dx converges iff p > 1.

For given `p`, numerically integrate ∫_1^{10000} 1/x^p dx using the trapezoidal rule with n=100000 subintervals.

Read `p`. Print the numerical integral (6 dp), and `Converges` (p > 1) or `Diverges` (p ≤ 1).

Example:
```
Input: 2
Output:
Integral: 0.9999
Converges
```
MD,
                'starter_code'        => "p = float(input())\ndef f(x): return x**(-p)\n\na, b = 1.0, 10000.0\nn = 100000\ndx = (b - a) / n\n# Trapezoidal rule\ntotal = (f(a) + f(b)) / 2\nfor i in range(1, n):\n    total += f(a + i * dx)\ntotal *= dx\nprint(f'Integral: {round(total, 6)}')\nprint('Converges' if p > 1 else 'Diverges')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
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

        // ── Q1: absolute value & sign ─────────────────────────────────────
        $seed(1, [
            ['input' => "-3.5",   'expected_output' => "3.5\nnegative",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4.2",    'expected_output' => "4.2\npositive",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0",    'expected_output' => "0.0\nzero",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "-0.001", 'expected_output' => "0.001\nnegative", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: distance on number line ───────────────────────────────────
        $seed(2, [
            ['input' => "-3\n5",    'expected_output' => "8.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n7",     'expected_output' => "5.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "-4\n-1",   'expected_output' => "3.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n0",     'expected_output' => "0.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: absolute value inequality ─────────────────────────────────
        $seed(3, [
            ['input' => "3\n2",    'expected_output' => "[1.0, 5.0]",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n1",    'expected_output' => "[-1.0, 1.0]",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n3",    'expected_output' => "[2.0, 8.0]",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "-2\n0.5", 'expected_output' => "[-2.5, -1.5]",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: rational/irrational ───────────────────────────────────────
        $seed(4, [
            ['input' => "0.3333333333\n1e-6",   'expected_output' => "rational: 1/3",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n1e-6",            'expected_output' => "rational: 1/2",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.4142135624\n1e-4",   'expected_output' => "irrational",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.25\n1e-6",           'expected_output' => "rational: 1/4",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: sup and inf ───────────────────────────────────────────────
        $seed(5, [
            ['input' => "4\n-1\n3\n0\n-5",   'expected_output' => "Sup: 3.0\nAttained: yes\nInf: -5.0\nAttained: yes",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",        'expected_output' => "Sup: 3.0\nAttained: yes\nInf: 1.0\nAttained: yes",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n42",             'expected_output' => "Sup: 42.0\nAttained: yes\nInf: 42.0\nAttained: yes", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n-3\n-1\n0\n1\n3",'expected_output'=> "Sup: 3.0\nAttained: yes\nInf: -3.0\nAttained: yes",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: arithmetic sequence ───────────────────────────────────────
        $seed(6, [
            ['input' => "1\n2\n5",     'expected_output' => "1.0\n3.0\n5.0\n7.0\n9.0\nDiverges",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n0\n3",     'expected_output' => "0.0\n0.0\n0.0\nConverges",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "10\n-2\n4",   'expected_output' => "10.0\n8.0\n6.0\n4.0\nDiverges",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n0\n1",     'expected_output' => "5.0\nConverges",                          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: geometric sequence ────────────────────────────────────────
        $seed(7, [
            ['input' => "1\n0.5\n5",   'expected_output' => "1.0\n0.5\n0.25\n0.125\n0.0625\nLimit: 0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n3",     'expected_output' => "2.0\n2.0\n2.0\nLimit: 2.0",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2\n4",     'expected_output' => "1.0\n2.0\n4.0\n8.0\nDiverges",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n-0.5\n4",  'expected_output' => "3.0\n-1.5\n0.75\n-0.375\nLimit: 0",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: sequence limit estimation ─────────────────────────────────
        $seed(8, [
            ['input' => "1\n1e-6",   'expected_output' => "1.0",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1e-6",   'expected_output' => "1.5",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1e-6",   'expected_output' => "1.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1e-8",   'expected_output' => "1.5",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: squeeze theorem (fixed output) ────────────────────────────
        $seed(9, [
            ['input' => "",   'expected_output' => "|a(1)|: 0.84147098\n|a(10)|: 0.05440211\n|a(100)|: 0.00506366\n|a(1000)|: 0.00082688\n|a(10000)|: 0.00030561\nLimit: 0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "",   'expected_output' => "|a(1)|: 0.84147098\n|a(10)|: 0.05440211\n|a(100)|: 0.00506366\n|a(1000)|: 0.00082688\n|a(10000)|: 0.00030561\nLimit: 0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "",   'expected_output' => "|a(1)|: 0.84147098\n|a(10)|: 0.05440211\n|a(100)|: 0.00506366\n|a(1000)|: 0.00082688\n|a(10000)|: 0.00030561\nLimit: 0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "",   'expected_output' => "|a(1)|: 0.84147098\n|a(10)|: 0.05440211\n|a(100)|: 0.00506366\n|a(1000)|: 0.00082688\n|a(10000)|: 0.00030561\nLimit: 0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: monotonicity ─────────────────────────────────────────────
        $seed(10, [
            ['input' => "5\n1\n2\n4\n8\n16",   'expected_output' => "increasing",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n10\n7\n4\n1",      'expected_output' => "decreasing",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n3\n2\n4",       'expected_output' => "neither",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n5\n5\n5",          'expected_output' => "neither",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: limit estimation ─────────────────────────────────────────
        $seed(11, [
            ['input' => "1\n1",   'expected_output' => "2.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0",   'expected_output' => "1.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1",   'expected_output' => "0.5",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1",   'expected_output' => "2.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: continuity of piecewise function ─────────────────────────
        $seed(12, [
            ['input' => "1\n2",    'expected_output' => "Continuous",                     'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n5",    'expected_output' => "Discontinuous\njump",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n5",    'expected_output' => "Discontinuous\njump",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n1",    'expected_output' => "Continuous",                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: IVT bisection ────────────────────────────────────────────
        $seed(13, [
            ['input' => "1\n2\n0",    'expected_output' => "1.521380",              'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n2\n0",    'expected_output' => "1.521380",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n3\n20",   'expected_output' => "3.0",                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n3\n22",   'expected_output' => "3.0",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: discontinuity type ───────────────────────────────────────
        $seed(14, [
            ['input' => "",   'expected_output' => "Right: 10.0 100.0 1000.0\nLeft: -10.0 -100.0 -1000.0\nType: infinite",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "",   'expected_output' => "Right: 10.0 100.0 1000.0\nLeft: -10.0 -100.0 -1000.0\nType: infinite",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "",   'expected_output' => "Right: 10.0 100.0 1000.0\nLeft: -10.0 -100.0 -1000.0\nType: infinite",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "",   'expected_output' => "Right: 10.0 100.0 1000.0\nLeft: -10.0 -100.0 -1000.0\nType: infinite",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: epsilon-delta ────────────────────────────────────────────
        $seed(15, [
            ['input' => "0.2",   'expected_output' => "delta: 0.1",         'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0",   'expected_output' => "delta: 0.5",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.02",  'expected_output' => "delta: 0.01",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.002", 'expected_output' => "delta: 0.001",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: central difference derivative ───────────────────────────
        $seed(16, [
            ['input' => "1\n2\n0.001",     'expected_output' => "12.0",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0\n0.001",     'expected_output' => "1.0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n0.001",     'expected_output' => "2.718282",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n0.001",     'expected_output' => "1.0",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: tangent line ─────────────────────────────────────────────
        $seed(17, [
            ['input' => "3",    'expected_output' => "y = 6.0x + -9.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0",    'expected_output' => "y = 0.0x + 0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2",    'expected_output' => "y = 4.0x + -4.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "-1",   'expected_output' => "y = -2.0x + -1.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: second derivative & concavity ────────────────────────────
        $seed(18, [
            ['input' => "4\n2\n0.001",   'expected_output' => "-2.0\nconcave down",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0\n0.001",   'expected_output' => "1.0\nconcave up",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0\n0.001",   'expected_output' => "0.0\ninflection point (possible)",'is_hidden' => true,'order_index' => 3],
            ['input' => "2\n1.5708\n0.001",'expected_output'=> "-1.0\nconcave down",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: first principles derivative ─────────────────────────────
        $seed(19, [
            ['input' => "3",   'expected_output' => "h=0.1: 6.1\nh=0.01: 6.01\nh=0.001: 6.001\nh=0.0001: 6.0001\nExact: 6.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0",   'expected_output' => "h=0.1: 0.1\nh=0.01: 0.01\nh=0.001: 0.001\nh=0.0001: 0.0001\nExact: 0.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2",   'expected_output' => "h=0.1: 4.1\nh=0.01: 4.01\nh=0.001: 4.001\nh=0.0001: 4.0001\nExact: 4.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5",   'expected_output' => "h=0.1: 10.1\nh=0.01: 10.01\nh=0.001: 10.001\nh=0.0001: 10.0001\nExact: 10.0",'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q20: gradient ─────────────────────────────────────────────────
        $seed(20, [
            ['input' => "1\n2\n0.001",   'expected_output' => "df/dx: 8.0\ndf/dy: 7.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n0\n0.001",   'expected_output' => "df/dx: 0.0\ndf/dy: 0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n3\n0.001",   'expected_output' => "df/dx: 13.0\ndf/dy: 10.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1\n0.001",   'expected_output' => "df/dx: 5.0\ndf/dy: 5.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: chain rule ───────────────────────────────────────────────
        $seed(21, [
            ['input' => "1",    'expected_output' => "Numerical: 1.080604\nAnalytical: 1.080604",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "0",    'expected_output' => "Numerical: 0.0\nAnalytical: 0.0",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5",  'expected_output' => "Numerical: 0.877583\nAnalytical: 0.877583",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2",    'expected_output' => "Numerical: -1.513605\nAnalytical: -1.513605",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Newton's method for extremum ─────────────────────────────
        $seed(22, [
            ['input' => "0.5\n10",   'expected_output' => "x: 1.0\nlocal minimum",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "-0.5\n10",  'expected_output' => "x: -1.0\nlocal maximum",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n10",     'expected_output' => "x: 1.0\nlocal minimum",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "-2\n10",    'expected_output' => "x: -1.0\nlocal maximum",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Mean Value Theorem ───────────────────────────────────────
        $seed(23, [
            ['input' => "1\n3",   'expected_output' => "MVT slope: 4.0\nc: 2.0\nf'(c): 4.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n2",   'expected_output' => "MVT slope: 2.0\nc: 1.0\nf'(c): 2.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n4",   'expected_output' => "MVT slope: 6.0\nc: 3.0\nf'(c): 6.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n4",   'expected_output' => "MVT slope: 4.0\nc: 2.0\nf'(c): 4.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: gradient descent ─────────────────────────────────────────
        $seed(24, [
            ['input' => "0\n0.1\n5",     'expected_output' => "x1: 0.6\nx2: 1.08\nx3: 1.464\nx4: 1.7712\nx5: 2.01696",                       'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.1\n3",     'expected_output' => "x1: 3.0\nx2: 3.0\nx3: 3.0",                                                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n0.2\n4",     'expected_output' => "x1: 4.8\nx2: 3.96\nx3: 3.372\nx4: 3.0432",                                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n0.5\n4",     'expected_output' => "x1: 3.0\nx2: 3.0\nx3: 3.0\nx4: 3.0",                                           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: optimisation rectangle ───────────────────────────────────
        $seed(25, [
            ['input' => "20",   'expected_output' => "Width: 5.0\nHeight: 5.0\nMax Area: 25.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "40",   'expected_output' => "Width: 10.0\nHeight: 10.0\nMax Area: 100.0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "12",   'expected_output' => "Width: 3.0\nHeight: 3.0\nMax Area: 9.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "8",    'expected_output' => "Width: 2.0\nHeight: 2.0\nMax Area: 4.0",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Rolle's theorem ──────────────────────────────────────────
        $seed(26, [
            ['input' => "1\n3",   'expected_output' => "c: 2.0\nf'(c) ≈ 0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n3",   'expected_output' => "c: 2.0\nf'(c) ≈ 0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n3",   'expected_output' => "c: 2.0\nf'(c) ≈ 0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n3",   'expected_output' => "c: 2.0\nf'(c) ≈ 0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Newton-Raphson ───────────────────────────────────────────
        $seed(27, [
            ['input' => "1.5\n20",   'expected_output' => "Root: 1.25992105\nIterations: 5",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n20",   'expected_output' => "Root: 1.25992105\nIterations: 7",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n20",   'expected_output' => "Root: 1.25992105\nIterations: 5",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n20",   'expected_output' => "Root: 1.25992105\nIterations: 10",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: L'Hôpital 0/0 ───────────────────────────────────────────
        $seed(28, [
            ['input' => "1",   'expected_output' => "1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2",   'expected_output' => "2.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3",   'expected_output' => "1.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1",   'expected_output' => "1.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: L'Hôpital ∞/∞ (fixed output) ────────────────────────────
        $seed(29, [
            ['input' => "",   'expected_output' => "x=100: 0.612348\nx=1000: 0.601200\nx=10000: 0.60012\nx=100000: 0.600012\nLimit: 0.6",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "",   'expected_output' => "x=100: 0.612348\nx=1000: 0.601200\nx=10000: 0.60012\nx=100000: 0.600012\nLimit: 0.6",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "",   'expected_output' => "x=100: 0.612348\nx=1000: 0.601200\nx=10000: 0.60012\nx=100000: 0.600012\nLimit: 0.6",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "",   'expected_output' => "x=100: 0.612348\nx=1000: 0.601200\nx=10000: 0.60012\nx=100000: 0.600012\nLimit: 0.6",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: 0×∞ (fixed output) ───────────────────────────────────────
        $seed(30, [
            ['input' => "",   'expected_output' => "x=0.1: -0.23025851\nx=0.01: -0.04605170\nx=0.001: -0.00690776\nx=0.0001: -0.00092103\nLimit: 0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "",   'expected_output' => "x=0.1: -0.23025851\nx=0.01: -0.04605170\nx=0.001: -0.00690776\nx=0.0001: -0.00092103\nLimit: 0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "",   'expected_output' => "x=0.1: -0.23025851\nx=0.01: -0.04605170\nx=0.001: -0.00690776\nx=0.0001: -0.00092103\nLimit: 0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "",   'expected_output' => "x=0.1: -0.23025851\nx=0.01: -0.04605170\nx=0.001: -0.00690776\nx=0.0001: -0.00092103\nLimit: 0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: (1+1/x)^x → e (fixed output) ────────────────────────────
        $seed(31, [
            ['input' => "",   'expected_output' => "x=10: 2.59374246\nx=100: 2.70481383\nx=1000: 2.71692393\nx=10000: 2.71814593\nx=100000: 2.71826824\nLimit: 2.71828183",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "",   'expected_output' => "x=10: 2.59374246\nx=100: 2.70481383\nx=1000: 2.71692393\nx=10000: 2.71814593\nx=100000: 2.71826824\nLimit: 2.71828183",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "",   'expected_output' => "x=10: 2.59374246\nx=100: 2.70481383\nx=1000: 2.71692393\nx=10000: 2.71814593\nx=100000: 2.71826824\nLimit: 2.71828183",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "",   'expected_output' => "x=10: 2.59374246\nx=100: 2.70481383\nx=1000: 2.71692393\nx=10000: 2.71814593\nx=100000: 2.71826824\nLimit: 2.71828183",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: L'Hôpital twice (fixed output) ───────────────────────────
        $seed(32, [
            ['input' => "",   'expected_output' => "x=0.1: 0.16658341\nx=0.01: 0.16666658\nx=0.001: 0.16666667\nLimit: 0.16666667",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "",   'expected_output' => "x=0.1: 0.16658341\nx=0.01: 0.16666658\nx=0.001: 0.16666667\nLimit: 0.16666667",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "",   'expected_output' => "x=0.1: 0.16658341\nx=0.01: 0.16666658\nx=0.001: 0.16666667\nLimit: 0.16666667",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "",   'expected_output' => "x=0.1: 0.16658341\nx=0.01: 0.16666658\nx=0.001: 0.16666667\nLimit: 0.16666667",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Riemann sums ─────────────────────────────────────────────
        $seed(33, [
            ['input' => "0\n1\n100",    'expected_output' => "Left: 0.32835\nRight: 0.33835\nMidpoint: 0.333325",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n2\n100",    'expected_output' => "Left: 2.6268\nRight: 2.7068\nMidpoint: 2.6668",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n3\n1000",   'expected_output' => "Left: 8.672\nRight: 8.696\nMidpoint: 8.6667",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n1\n1000",   'expected_output' => "Left: 0.3328335\nRight: 0.3338335\nMidpoint: 0.3333325",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Trapezoidal & Simpson ────────────────────────────────────
        $seed(34, [
            ['input' => "0\n3.141592653589793\n100",   'expected_output' => "Trapezoidal: 1.99998355\nSimpson: 2.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n1.5707963267948966\n100",  'expected_output' => "Trapezoidal: 0.99998355\nSimpson: 1.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n3.141592653589793\n1000",  'expected_output' => "Trapezoidal: 1.9999998355\nSimpson: 2.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.5707963267948966\n3.141592653589793\n100",'expected_output'=> "Trapezoidal: 0.99998355\nSimpson: 1.0",'is_hidden' => true,'order_index' => 4],
        ]);

        // ── Q35: integral properties ──────────────────────────────────────
        $seed(35, [
            ['input' => "0\n2\n1\n3",   'expected_output' => "Reversal: yes\nAdditivity: yes\nConstant: yes",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n3\n2\n5",   'expected_output' => "Reversal: yes\nAdditivity: yes\nConstant: yes",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n4\n2\n2",   'expected_output' => "Reversal: yes\nAdditivity: yes\nConstant: yes",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n1\n0.5\n10",'expected_output'=> "Reversal: yes\nAdditivity: yes\nConstant: yes",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: area between curves ──────────────────────────────────────
        $seed(36, [
            ['input' => "",   'expected_output' => "0.166667",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "",   'expected_output' => "0.166667",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "",   'expected_output' => "0.166667",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "",   'expected_output' => "0.166667",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: average value ────────────────────────────────────────────
        $seed(37, [
            ['input' => "0\n1.5707963267948966",   'expected_output' => "0.636620",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n3.141592653589793",    'expected_output' => "0.0",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0.5235987755982988",   'expected_output' => "0.954930",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5235987755982988\n1.5707963267948966",'expected_output'=> "0.477465",'is_hidden' => true,'order_index' => 4],
        ]);

        // ── Q38: FTC Part 1 ───────────────────────────────────────────────
        $seed(38, [
            ['input' => "2",   'expected_output' => "F(x): 2.666667\nF'(x): 4.0\nf(x): 4.0\nFTC verified: yes",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1",   'expected_output' => "F(x): 0.333333\nF'(x): 1.0\nf(x): 1.0\nFTC verified: yes",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3",   'expected_output' => "F(x): 9.0\nF'(x): 9.0\nf(x): 9.0\nFTC verified: yes",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "4",   'expected_output' => "F(x): 21.333333\nF'(x): 16.0\nf(x): 16.0\nFTC verified: yes",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: FTC Part 2 ───────────────────────────────────────────────
        $seed(39, [
            ['input' => "1\n3",   'expected_output' => "F(b) - F(a): 26.0\nNumerical: 26.0\nMatch: yes",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n2",   'expected_output' => "F(b) - F(a): 8.0\nNumerical: 8.0\nMatch: yes",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n1",   'expected_output' => "F(b) - F(a): 1.0\nNumerical: 1.0\nMatch: yes",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n4",   'expected_output' => "F(b) - F(a): 56.0\nNumerical: 56.0\nMatch: yes",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: cumulative antiderivative ────────────────────────────────
        $seed(40, [
            ['input' => "0\n2\n4",   'expected_output' => "0.0\n0.25\n1.0\n2.25\n4.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n1\n2",   'expected_output' => "0.0\n0.25\n1.0",                 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n3\n3",   'expected_output' => "0.0\n0.75\n3.0\n6.75",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n3\n4",   'expected_output' => "0.0\n1.5\n4.0\n7.5\n12.0",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: variable upper limit / ln(x) verification (fixed) ────────
        $seed(41, [
            ['input' => "",   'expected_output' => "x=2: G(x)=0.693147  ln(x)=0.693147  diff=0.0\nx=3: G(x)=1.098612  ln(x)=1.098612  diff=0.0\nx=5: G(x)=1.609438  ln(x)=1.609438  diff=0.0\nx=10: G(x)=2.302585  ln(x)=2.302585  diff=0.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "",   'expected_output' => "x=2: G(x)=0.693147  ln(x)=0.693147  diff=0.0\nx=3: G(x)=1.098612  ln(x)=1.098612  diff=0.0\nx=5: G(x)=1.609438  ln(x)=1.609438  diff=0.0\nx=10: G(x)=2.302585  ln(x)=2.302585  diff=0.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "",   'expected_output' => "x=2: G(x)=0.693147  ln(x)=0.693147  diff=0.0\nx=3: G(x)=1.098612  ln(x)=1.098612  diff=0.0\nx=5: G(x)=1.609438  ln(x)=1.609438  diff=0.0\nx=10: G(x)=2.302585  ln(x)=2.302585  diff=0.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "",   'expected_output' => "x=2: G(x)=0.693147  ln(x)=0.693147  diff=0.0\nx=3: G(x)=1.098612  ln(x)=1.098612  diff=0.0\nx=5: G(x)=1.609438  ln(x)=1.609438  diff=0.0\nx=10: G(x)=2.302585  ln(x)=2.302585  diff=0.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: u-substitution ───────────────────────────────────────────
        $seed(42, [
            ['input' => "0\n1",   'expected_output' => "Left: 0.428571\nRight: 0.428571\nMatch: yes",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n2",   'expected_output' => "Left: 25.6\nRight: 25.6\nMatch: yes",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2",   'expected_output' => "Left: 25.1714\nRight: 25.1714\nMatch: yes",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n0.5", 'expected_output' => "Left: 0.015625\nRight: 0.015625\nMatch: yes",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: integration by parts ─────────────────────────────────────
        $seed(43, [
            ['input' => "0\n1",   'expected_output' => "Analytical: 1.0\nNumerical: 1.0\nMatch: yes",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n2",   'expected_output' => "Analytical: 2.718282\nNumerical: 2.718282\nMatch: yes",'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2",   'expected_output' => "Analytical: 2.718282\nNumerical: 2.718282\nMatch: yes",'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n3",   'expected_output' => "Analytical: 2.0×e^3\nNumerical: 40.171074\nMatch: yes",'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q44: improper integral (fixed output) ─────────────────────────
        $seed(44, [
            ['input' => "",   'expected_output' => "M=10: 0.9\nM=100: 0.99\nM=1000: 0.999\nM=10000: 0.9999\nTrue value: 1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "",   'expected_output' => "M=10: 0.9\nM=100: 0.99\nM=1000: 0.999\nM=10000: 0.9999\nTrue value: 1.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "",   'expected_output' => "M=10: 0.9\nM=100: 0.99\nM=1000: 0.999\nM=10000: 0.9999\nTrue value: 1.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "",   'expected_output' => "M=10: 0.9\nM=100: 0.99\nM=1000: 0.999\nM=10000: 0.9999\nTrue value: 1.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: arc length ───────────────────────────────────────────────
        $seed(45, [
            ['input' => "0\n1",   'expected_output' => "1.478943",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n2",   'expected_output' => "4.646815",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2",   'expected_output' => "3.167872",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n3",   'expected_output' => "9.747093",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: volume of revolution ─────────────────────────────────────
        $seed(46, [
            ['input' => "0\n4",   'expected_output' => "Analytical: 25.132741\nNumerical: 25.132741",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n1",   'expected_output' => "Analytical: 1.570796\nNumerical: 1.570796",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n4",   'expected_output' => "Analytical: 23.561945\nNumerical: 23.561945",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n9",   'expected_output' => "Analytical: 127.234502\nNumerical: 127.234502", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: geometric series ─────────────────────────────────────────
        $seed(47, [
            ['input' => "0.5\n10",   'expected_output' => "S_n: 1.99804688\nExact: 2.0",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.9\n20",   'expected_output' => "S_n: 8.78423213\nExact: 10.0",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n5",      'expected_output' => "S_n: 31.0\nDiverges",                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.1\n5",    'expected_output' => "S_n: 1.1111\nExact: 1.1111",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: ratio test ───────────────────────────────────────────────
        $seed(48, [
            ['input' => "1",   'expected_output' => "Ratio at n=10: 0.090909\nRatio at n=100: 0.009901\nL: 0.0\nConverges absolutely",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2",   'expected_output' => "Ratio at n=10: 0.367879\nRatio at n=100: 0.367879\nL: 0.3679\nConverges absolutely",'is_hidden' => false, 'order_index' => 2],
            ['input' => "3",   'expected_output' => "Ratio at n=10: 2.718282\nRatio at n=100: 2.718282\nL: 2.7183\nDiverges",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "1",   'expected_output' => "Ratio at n=10: 0.090909\nRatio at n=100: 0.009901\nL: 0.0\nConverges absolutely",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Taylor series e^x ────────────────────────────────────────
        $seed(49, [
            ['input' => "1\n10",    'expected_output' => "Taylor: 2.71828153\nExact: 2.71828183\nError: 0.00000030",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n15",    'expected_output' => "Taylor: 7.38905610\nExact: 7.38905610\nError: 0.0",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n8",   'expected_output' => "Taylor: 1.64872127\nExact: 1.64872127\nError: 0.0",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5",     'expected_output' => "Taylor: 2.70833333\nExact: 2.71828183\nError: 0.00994849",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: p-series integral test ───────────────────────────────────
        $seed(50, [
            ['input' => "2",    'expected_output' => "Integral: 0.9999\nConverges",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1",    'expected_output' => "Integral: 9.2103\nDiverges",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5",  'expected_output' => "Integral: 197.98\nDiverges",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3",    'expected_output' => "Integral: 0.49999975\nConverges",'is_hidden' => true, 'order_index' => 4],
        ]);

        $this->command->info('✅ Module 4 Coding (Intermediate) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}