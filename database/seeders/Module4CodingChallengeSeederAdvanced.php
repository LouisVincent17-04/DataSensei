<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 4 — Calculus for Machine Learning (Advanced) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Advanced tier
 *   2. coding_questions    — 50 questions covering advanced calculus in Python
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (Module 4 curriculum — advanced depth):
 *   4.1  Real Analysis Foundations (Cauchy sequences, completeness, ε-δ proofs)
 *   4.2  Sequences: Convergence Rate & Acceleration (Aitken's Δ², Richardson)
 *   4.3  Advanced Limits (uniform continuity, Lipschitz constants, modulus)
 *   4.4  Higher-Order & Automatic Differentiation (dual numbers, Jacobians)
 *   4.5  Optimisation: Second-Order Methods (Newton, Quasi-Newton BFGS sketch)
 *   4.6  Asymptotic Analysis & Taylor Remainder Bounds
 *   4.7  Numerical Integration: Gaussian Quadrature & Adaptive Simpson
 *   4.8  FTC & Lebesgue / Improper Integrals (comparison, Dirichlet test)
 *   4.9  Multivariable Integration & Change of Variables (Jacobian)
 *   4.10 Series: Radius of Convergence, Power Series & Fourier Coefficients
 *
 * Difficulty: Advanced — multi-algorithm implementations, numerical analysis,
 *             error estimation, ML-relevant calculus; pure Python + math only.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module4CodingChallengeSeederAdvanced extends Seeder
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

        $this->command->info('Creating Module 4 — Calculus for Machine Learning (Advanced) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Calculus for Machine Learning',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Advanced computational calculus for ML practitioners. Implement automatic differentiation via dual numbers, adaptive integration, second-order optimisation, power series manipulation, Fourier coefficients, Jacobians, and convergence analysis — from scratch in pure Python.',
                'time_limit_seconds' => 3600,
                'base_xp'            => 1500,
                'order_index'        => 4,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.1: Real Analysis Foundations (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Verify a sequence is **Cauchy** by checking that for all ε > 0, there exists N such that for all m, n > N, |a_m − a_n| < ε.

Given the sequence a_n = 1/n, test the Cauchy condition numerically: for given ε, find the smallest N such that |a_m − a_n| < ε for all m, n > N (check pairs up to n=10000).

Read `eps`. Print `N: <val>` (the smallest such N).

Example:
```
Input: 0.01
Output: N: 100
```
MD,
                'starter_code'        => "eps = float(input())\n# Find smallest N for Cauchy condition on a_n = 1/n\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Compute the **Lipschitz constant** of a function on an interval by estimating:

L = max_{x ≠ y ∈ [a,b]} |f(x) − f(y)| / |x − y|

Sample uniformly with n=1000 points, check all pairs.

Function choices: 1=sin(x), 2=x², 3=e^x

Read function index, `a`, `b`. Print L rounded to 4 decimal places.

Example:
```
Input:
1
0
3.141592653589793
Output: 1.0
```
MD,
                'starter_code'        => "import math\n\nidx = int(input())\na = float(input())\nb = float(input())\n\ndef f(x):\n    if idx == 1: return math.sin(x)\n    if idx == 2: return x**2\n    return math.exp(x)\n\nn = 1000\npts = [a + i*(b-a)/n for i in range(n+1)]\n# Compute Lipschitz constant\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Determine **uniform continuity** of f on [a, b] by verifying: for given ε, find the smallest δ > 0 (to 4 dp, checking δ = 0.5, 0.1, 0.01, 0.001, 0.0001) such that |x−y| < δ ⟹ |f(x)−f(y)| < ε for all x, y sampled from 1000 uniformly spaced points.

Function choices: 1=sin(x), 2=x², 3=1/(x−0.5) (may fail on [0,1])

Read function index, `a`, `b`, `eps`. Print `delta: <val>` or `Not uniformly continuous on this interval` if no δ works.

Example:
```
Input:
1
0
6.283185307179586
0.1
Output: delta: 0.1
```
MD,
                'starter_code'        => "import math\n\nidx = int(input())\na = float(input())\nb = float(input())\neps = float(input())\n\ndef f(x):\n    if idx == 1: return math.sin(x)\n    if idx == 2: return x**2\n    return 1 / (x - 0.5)\n\nn = 1000\npts = [a + i*(b-a)/n for i in range(n+1)]\n# Test uniform continuity for decreasing delta\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Check the **Bolzano-Weierstrass theorem** numerically: every bounded sequence has a convergent subsequence.

Given a bounded sequence a_n = sin(n), extract the subsequence at indices n = 1, 2, ..., N where |a_n − L| < eps for a candidate limit L (use L = 0 as a test).

Read `N` and `eps`. Print the first 5 indices (1-based) where the subsequence is within eps of L, or `Not found` if fewer than 5 exist.

Example:
```
Input:
100
0.05
Output: 3 13 16 19 22
```
MD,
                'starter_code'        => "import math\n\nN = int(input())\neps = float(input())\nL = 0.0\nresult = []\nfor n in range(1, N+1):\n    if abs(math.sin(n) - L) < eps:\n        result.append(n)\n# Print first 5 indices\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Estimate the **modulus of continuity** ω(δ) of f(x) = √x on [0, 4].

ω(δ) = sup{|f(x) − f(y)| : |x − y| ≤ δ, x, y ∈ [0, 4]}

For √x, the exact modulus is √δ (since |√x − √y| ≤ √|x−y|).

Read `delta`. Compute ω(δ) numerically using 10000 sample points and print it rounded to 6 dp. Also print the analytical bound √δ rounded to 6 dp.

Example:
```
Input: 0.25
Output:
Numerical: 0.5
Analytical: 0.5
```
MD,
                'starter_code'        => "import math\n\ndelta = float(input())\npts = [i * 4 / 10000 for i in range(10001)]\n# Compute omega(delta) numerically\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.2: Sequence Convergence Rate & Acceleration (Q6–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Compute the **order of convergence** of a sequence to its limit.

Given consecutive errors e_n = |a_n − L|, the order p satisfies:
e_{n+1} ≈ C × e_n^p

Estimate p using: p ≈ log(e_{n+2}/e_{n+1}) / log(e_{n+1}/e_n)

Use sequence a_n = 1/n² (L = 0). Compute p using n = 10, 11, 12.

Print errors at n=10, 11, 12 (rounded to 8 dp) and the estimated order p (rounded to 4 dp).

Example:
```
Output:
e(10): 0.01
e(11): 0.00826446
e(12): 0.00694444
Order p: 1.0
```
MD,
                'starter_code'        => "import math\n\ndef a(n): return 1/n**2\nL = 0.0\ne = [abs(a(n) - L) for n in [10, 11, 12]]\nfor i, n in enumerate([10, 11, 12]):\n    print(f'e({n}): {round(e[i], 8)}')\n# Estimate order of convergence\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Apply **Aitken's Δ² method** to accelerate convergence of a sequence.

Given a_n, the accelerated sequence is:
A_n = a_n − (a_{n+1} − a_n)² / (a_{n+2} − 2a_{n+1} + a_n)

For the sequence a_n = Σ_{k=1}^{n} 1/k² (partial sums of π²/6), compute A_n for n = 5, 10, 15.

Print a_n, A_n, and π²/6 (true limit), each rounded to 8 dp.

Example:
```
Output:
n=5: a_n=1.46361111, A_n=1.64136498, true=1.64493407
n=10: a_n=1.54976773, A_n=1.64398636, true=1.64493407
n=15: a_n=1.58007848, A_n=1.64459694, true=1.64493407
```
MD,
                'starter_code'        => "import math\n\ndef partial_sum(n):\n    return sum(1/k**2 for k in range(1, n+1))\n\ntrue_limit = math.pi**2 / 6\nfor n in [5, 10, 15]:\n    an = partial_sum(n)\n    an1 = partial_sum(n+1)\n    an2 = partial_sum(n+2)\n    # Aitken acceleration\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Apply **Richardson extrapolation** to improve the accuracy of a finite difference derivative estimate.

For f(x), the central difference has error O(h²):
f'(x) ≈ D(h) = (f(x+h) − f(x−h)) / (2h)

Richardson extrapolation:
f'(x) ≈ (4D(h/2) − D(h)) / 3

Use f(x) = sin(x).

Read `x` and `h`. Print D(h), D(h/2), Richardson estimate, and exact cos(x) — all rounded to 8 dp.

Example:
```
Input:
1.0
0.1
Output:
D(h): 0.54030231
D(h/2): 0.54030231
Richardson: 0.54030231
Exact cos(x): 0.54030231
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\nh = float(input())\n\ndef f(t): return math.sin(t)\ndef D(t, step): return (f(t+step) - f(t-step)) / (2*step)\n\n# Print Richardson extrapolation results\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Implement **Steffensen's method** (a quadratically convergent fixed-point iteration that doesn't require a derivative).

For g(x) = x − f(x), where f(x) = x² − 2 (finding √2):

Steffensen iteration:
x_{n+1} = x_n − [g(x_n) − x_n]² / [g(g(x_n)) − 2g(x_n) + x_n]

Read `x0` and `max_iter`. Stop when |x_{n+1} − x_n| < 1e-10. Print the root rounded to 10 dp and iterations.

Example:
```
Input:
2.0
20
Output:
Root: 1.4142135624
Iterations: 3
```
MD,
                'starter_code'        => "x = float(input())\nmax_iter = int(input())\n\ndef g(t): return t - (t**2 - 2)\n\n# Steffensen's method\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Compute the **rate of convergence** of gradient descent on a strongly convex function.

For f(x) = x², gradient descent with step α converges as:
x_n = (1 − 2α)^n × x_0

The convergence rate is |1 − 2α| per step.

Read `x0`, `alpha`, and `n_iter`. Print:
- x after each of the first 5 steps (rounded to 6 dp)
- Theoretical rate: |1−2α| rounded to 4 dp
- Empirical rate: |x_{n}/x_{n-1}| for the last step (rounded to 4 dp)

Example:
```
Input:
10
0.1
5
Output:
x1: 8.0
x2: 6.4
x3: 5.12
x4: 4.096
x5: 3.2768
Rate (theory): 0.8
Rate (empirical): 0.8
```
MD,
                'starter_code'        => "x0 = float(input())\nalpha = float(input())\nn_iter = int(input())\n\ndef fp(t): return 2*t\n\nx = x0\nxprev = x0\n# Gradient descent and rate analysis\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.3: Advanced Limits & Continuity (Q11–Q15)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Compute the **multivariate limit** of f(x,y) = (x²y) / (x⁴ + y²) as (x,y) → (0,0).

Show that the limit does not exist by approaching along different paths:
- Path 1: y = x (substitute and compute limit as x→0)
- Path 2: y = x² (substitute and compute limit as x→0)

Read nothing (fixed). Print the limit along each path (6 dp), and `Limit does not exist`.

Example:
```
Output:
Path y=x: 0.0
Path y=x²: 0.5
Limit does not exist
```
MD,
                'starter_code'        => "# f(x,y) = x^2 * y / (x^4 + y^2)\n# Path 1: y = x => f(x, x) = x^3 / (x^4 + x^2) = x / (x^2 + 1) -> 0\n# Path 2: y = x^2 => f(x, x^2) = x^4 / (x^4 + x^4) = 0.5\nprint('Path y=x: 0.0')\nprint('Path y=x²: 0.5')\nprint('Limit does not exist')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Compute the **limit of a function at infinity** along with its **horizontal asymptote** and check if the function crosses the asymptote.

f(x) = (3x² + 2) / (x² + 1)

Horizontal asymptote: L = lim_{x→±∞} f(x) = 3.

Read `n` x-values (one per line). For each, print f(x) rounded to 6 dp. Then print `Asymptote: 3.0` and whether f ever equals 3: `Crosses: no`.

Example:
```
Input:
3
10
100
1000
Output:
10: 2.970297
100: 2.999700
1000: 2.999997
Asymptote: 3.0
Crosses: no
```
MD,
                'starter_code'        => "def f(x): return (3*x**2 + 2) / (x**2 + 1)\n\nn = int(input())\nfor _ in range(n):\n    x = float(input())\n    print(f'{int(x)}: {round(f(x), 6)}')\nprint('Asymptote: 3.0')\nprint('Crosses: no')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Implement the **bisection method** for root finding with full convergence tracking.

For f(x) = cos(x) − x, find the root in [0, π/2].

Perform exactly `n` bisection steps. Print:
- The midpoint after each step (rounded to 8 dp)
- The final root approximation
- The error bound: (b−a) / 2^n rounded to 8 dp

Read `n`.

Example:
```
Input: 5
Output:
Step 1: 0.78539816
Step 2: 0.39269908
Step 3: 0.58904862
Step 4: 0.73722389
Step 5: 0.66313626
Root: 0.66313626
Error bound: 0.04908739
```
MD,
                'starter_code'        => "import math\n\ndef f(x): return math.cos(x) - x\n\na, b = 0.0, math.pi / 2\nn = int(input())\n# Bisection with step-by-step output\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Compute the **fixed-point iteration** convergence for g(x) = cos(x) and analyse convergence.

x_{n+1} = cos(x_n) (converges to the Dottie number ≈ 0.739085...)

Read `x0` and `max_iter`. Iterate until |x_{n+1} − x_n| < 1e-10. Print:
- Final fixed point rounded to 10 dp
- Number of iterations
- Estimated convergence rate: |e_{n+1}| / |e_n| for the last two steps (rounded to 4 dp)

Example:
```
Input:
1.0
1000
Output:
Fixed point: 0.7390851332
Iterations: 86
Convergence rate: 0.6736
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\nmax_iter = int(input())\n\n# Fixed-point iteration for g(x) = cos(x)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Compute the **contraction mapping constant** of g(x) = cos(x) on [0, 1] and verify Banach's fixed-point theorem.

The contraction constant k = max_{x ∈ [0,1]} |g'(x)| = max |−sin(x)| = sin(1).

Verify: the iteration converges with rate ≤ k.

Read `x0` and `n_iter`. Print:
- Contraction constant k (6 dp)
- Theoretical bound on error after n iterations: k^n × |x_1 − x_0| / (1 − k) rounded to 6 dp
- Actual error |x_n − fixed_point| rounded to 6 dp

Example:
```
Input:
1.0
10
Output:
k: 0.841471
Theoretical bound: 0.064547
Actual error: 0.019703
```
MD,
                'starter_code'        => "import math\n\nx0 = float(input())\nn_iter = int(input())\nfixed_point = 0.7390851332151607\n\ndef g(x): return math.cos(x)\nk = math.sin(1.0)\n# Banach FPT analysis\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.4: Automatic Differentiation & Jacobians (Q16–Q20)
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Implement **forward-mode automatic differentiation** using dual numbers.

A dual number is x + ε·x' where ε² = 0.

Implement the Dual class with addition, multiplication, and sin/exp/log operations.

Compute the derivative of f(x) = x³ × sin(x) + e^x at a given x by setting the seed (x') = 1.

Read `x`. Print f(x) and f'(x) each rounded to 6 dp.

Example:
```
Input: 1.0
Output:
f(x): 3.559410
f'(x): 5.748738
```
MD,
                'starter_code'        => "import math\n\nclass Dual:\n    def __init__(self, val, deriv=0.0):\n        self.val = val\n        self.deriv = deriv\n    def __add__(self, other):\n        if isinstance(other, Dual):\n            return Dual(self.val + other.val, self.deriv + other.deriv)\n        return Dual(self.val + other, self.deriv)\n    def __radd__(self, other): return self.__add__(other)\n    def __mul__(self, other):\n        if isinstance(other, Dual):\n            return Dual(self.val*other.val, self.val*other.deriv + self.deriv*other.val)\n        return Dual(self.val*other, self.deriv*other)\n    def __rmul__(self, other): return self.__mul__(other)\n    def __pow__(self, n):\n        return Dual(self.val**n, n * self.val**(n-1) * self.deriv)\n\ndef dual_sin(d): return Dual(math.sin(d.val), math.cos(d.val)*d.deriv)\ndef dual_exp(d): return Dual(math.exp(d.val), math.exp(d.val)*d.deriv)\n\nx_val = float(input())\nx = Dual(x_val, 1.0)\n# Compute f(x) = x^3 * sin(x) + e^x using dual numbers\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Compute the **Jacobian matrix** of a vector-valued function numerically.

f: ℝ² → ℝ²
f₁(x, y) = x² + y
f₂(x, y) = sin(x) × y

J = [[∂f₁/∂x, ∂f₁/∂y],
     [∂f₂/∂x, ∂f₂/∂y]]

Use central differences with h = 1e-5.

Read `x` and `y`. Print the 2×2 Jacobian matrix (each entry rounded to 6 dp), row by row.

Example:
```
Input:
1
2
Output:
2.0 1.0
1.080604 1.0
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\ny = float(input())\nh = 1e-5\n\ndef f1(a, b): return a**2 + b\ndef f2(a, b): return math.sin(a) * b\n\n# Compute 2x2 Jacobian via central differences\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Compute the **Hessian matrix** of f(x, y) = x³ + y³ − 3xy numerically.

H = [[∂²f/∂x², ∂²f/∂x∂y],
     [∂²f/∂y∂x, ∂²f/∂y²]]

Use the finite difference formulas:
- ∂²f/∂x² ≈ (f(x+h,y) − 2f(x,y) + f(x−h,y)) / h²
- ∂²f/∂x∂y ≈ (f(x+h,y+h) − f(x+h,y−h) − f(x−h,y+h) + f(x−h,y−h)) / (4h²)

Use h = 1e-4.

Read `x` and `y`. Print the 2×2 Hessian (each entry rounded to 4 dp), classify the critical point if (x,y)=(1,1): `saddle`, `local min`, `local max`, or `inconclusive`.

Example:
```
Input:
1
1
Output:
6.0 -3.0
-3.0 6.0
local min
```
MD,
                'starter_code'        => "x = float(input())\ny = float(input())\nh = 1e-4\n\ndef f(a, b): return a**3 + b**3 - 3*a*b\n\n# Compute Hessian numerically\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Implement **reverse-mode automatic differentiation** (backpropagation) for a simple computational graph.

Compute the gradient of f(x, y) = (x + y) × sin(x × y) with respect to x and y.

Use the chain rule manually (no symbolic differentiation):
∂f/∂x = sin(xy) + (x+y) × cos(xy) × y
∂f/∂y = sin(xy) + (x+y) × cos(xy) × x

Read `x` and `y`. Print f(x,y), ∂f/∂x, ∂f/∂y all rounded to 6 dp.

Example:
```
Input:
1
2
Output:
f: 2.727892
df/dx: 0.091289
df/dy: 1.638948
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\ny = float(input())\n# Compute f and gradients analytically\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Compute the **directional derivative** of f(x, y) = x² + 2y² at point (x₀, y₀) in direction (u₁, u₂) (normalised).

D_u f = ∇f · û = (∂f/∂x × u₁ + ∂f/∂y × u₂) where û is the unit vector.

Read `x0`, `y0`, `u1`, `u2`. Print the directional derivative rounded to 6 dp.

Example:
```
Input:
1
2
1
1
Output: 6.363961
```
MD,
                'starter_code'        => "import math\n\nx0 = float(input())\ny0 = float(input())\nu1 = float(input())\nu2 = float(input())\n\n# Normalise direction and compute directional derivative\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.5: Second-Order Optimisation Methods (Q21–Q25)
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Implement **Newton's method for 2D optimisation** (find critical point of f(x,y)).

Update: [x, y]_{n+1} = [x, y]_n − H⁻¹ × ∇f

For f(x, y) = x² + 2y² − xy:
∇f = [2x−y, 4y−x]
H = [[2, −1], [−1, 4]]
H⁻¹ = (1/7) × [[4, 1], [1, 2]]

Read `x0`, `y0`, `n_iter`. Print (x, y) after each iteration (6 dp each) and the final point.

Example:
```
Input:
5
5
3
Output:
Iter 1: (2.142857, 1.428571)
Iter 2: (0.918367, 0.612245)
Iter 3: (0.393586, 0.262391)
Final: (0.393586, 0.262391)
```
MD,
                'starter_code'        => "x = float(input())\ny = float(input())\nn_iter = int(input())\n\n# H_inv = (1/7) * [[4,1],[1,2]]\nfor i in range(n_iter):\n    gx = 2*x - y\n    gy = 4*y - x\n    # Apply H_inv\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Implement **gradient descent with momentum**.

v_{n+1} = γ × v_n + α × ∇f(x_n)
x_{n+1} = x_n − v_{n+1}

For f(x) = x⁴ − 4x², ∇f = 4x³ − 8x (computed numerically).

Read `x0`, `alpha`, `gamma`, `n_iter`. Print x after each step (6 dp).

Example:
```
Input:
3
0.01
0.9
5
Output:
x1: 2.730000
x2: 2.463933
x3: 2.201876
x4: 1.947066
x5: 1.702978
```
MD,
                'starter_code'        => "x = float(input())\nalpha = float(input())\ngamma = float(input())\nn_iter = int(input())\nh = 1e-5\ndef f(t): return t**4 - 4*t**2\ndef fp(t): return (f(t+h) - f(t-h)) / (2*h)\nv = 0.0\n# Gradient descent with momentum\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Implement **Adam optimiser** for minimising f(x) = x⁴ − 3x³ + 2.

Adam update (using numerical gradient):
m_t = β₁ × m_{t-1} + (1−β₁) × g_t
v_t = β₂ × v_{t-1} + (1−β₂) × g_t²
m̂_t = m_t / (1 − β₁^t)
v̂_t = v_t / (1 − β₂^t)
x_{t+1} = x_t − α × m̂_t / (√v̂_t + ε)

Use β₁=0.9, β₂=0.999, ε=1e-8.

Read `x0`, `alpha`, `n_iter`. Print x after every 10 steps (rounded to 6 dp).

Example:
```
Input:
5
0.1
50
Output:
Step 10: 4.003399
Step 20: 3.121025
Step 30: 2.381826
Step 40: 2.298726
Step 50: 2.250004
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\nalpha = float(input())\nn_iter = int(input())\n\nh = 1e-5\ndef f(t): return t**4 - 3*t**3 + 2\ndef fp(t): return (f(t+h) - f(t-h)) / (2*h)\n\nbeta1, beta2, eps = 0.9, 0.999, 1e-8\nm, v = 0.0, 0.0\n# Adam optimiser loop\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Implement **line search** (backtracking Armijo condition) for gradient descent.

Armijo condition: f(x − α∇f(x)) ≤ f(x) − c × α × |∇f(x)|²

Start with α = 1, reduce by ρ = 0.5 until condition holds (c = 0.1).

For f(x) = x⁴ − 2x².

Read `x0` and `n_iter`. For each GD step, print the step size α used (6 dp) and new x (6 dp).

Example:
```
Input:
2.0
4
Output:
Step 1: alpha=0.0625, x=1.750000
Step 2: alpha=0.125, x=1.386581
Step 3: alpha=0.25, x=0.993987
Step 4: alpha=0.5, x=0.999996
```
MD,
                'starter_code'        => "h = 1e-5\ndef f(t): return t**4 - 2*t**2\ndef fp(t): return (f(t+h) - f(t-h)) / (2*h)\n\nx = float(input())\nn_iter = int(input())\nc, rho = 0.1, 0.5\n# Gradient descent with backtracking line search\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Implement **conjugate gradient descent** (linear CG) to solve Ax = b.

CG algorithm:
r₀ = b − Ax₀, p₀ = r₀
α_k = rₖᵀrₖ / (pₖᵀApₖ)
x_{k+1} = xₖ + αₖpₖ
r_{k+1} = rₖ − αₖApₖ
β_k = r_{k+1}ᵀr_{k+1} / rₖᵀrₖ
p_{k+1} = r_{k+1} + β_k pₖ

For 2D: A = [[4,1],[1,3]], b = [1,2].

Read `x0 y0` (initial guess). Run CG until |r| < 1e-10. Print the solution (x, y) rounded to 6 dp and iterations.

Example:
```
Input:
0 0
Output:
x: 0.090909
y: 0.636364
Iterations: 2
```
MD,
                'starter_code'        => "# A = [[4,1],[1,3]], b = [1,2]\nA = [[4,1],[1,3]]\nb = [1.0, 2.0]\nline = input().split()\nx = [float(line[0]), float(line[1])]\n\ndef matvec(M, v): return [sum(M[i][j]*v[j] for j in range(2)) for i in range(2)]\ndef dot(u, v): return sum(u[i]*v[i] for i in range(2))\ndef vadd(u, v): return [u[i]+v[i] for i in range(2)]\ndef vsub(u, v): return [u[i]-v[i] for i in range(2)]\ndef vscale(s, v): return [s*vi for vi in v]\n# CG algorithm\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.6: Taylor Remainder & Asymptotic Analysis (Q26–Q30)
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Compute the **Taylor polynomial** of degree n for f(x) = ln(1+x) around x=0.

ln(1+x) = x − x²/2 + x³/3 − ... + (−1)^{n+1} xⁿ/n

Read `x` and `n`. Print:
- Taylor approximation (8 dp)
- Exact value math.log(1+x) (8 dp)
- Taylor remainder bound: |x|^{n+1} / (n+1) if |x| ≤ 1 (8 dp)

Example:
```
Input:
0.5
5
Output:
Taylor: 0.40104167
Exact: 0.40546511
Remainder bound: 0.00520833
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\nn = int(input())\n\ntaylor = sum((-1)**(k+1) * x**k / k for k in range(1, n+1))\nexact = math.log(1 + x)\nremainder = abs(x)**(n+1) / (n+1)\nprint(f'Taylor: {round(taylor, 8)}')\nprint(f'Exact: {round(exact, 8)}')\nprint(f'Remainder bound: {round(remainder, 8)}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Apply **big-O notation** analysis: classify the growth rate of T(n) relative to standard functions.

For each input n, compute T(n) = n² × log(n) + 3n. Determine:
- Is T(n) = O(n²)? No (because log factor)
- Is T(n) = O(n² log n)? Yes
- Is T(n) = O(n³)? Yes

Read `n` (positive integer). Compute the ratio T(n) / (n²×log₂(n)) and T(n) / n³, print both rounded to 4 dp. Then print which tight bound applies: `Theta(n^2 log n)`.

Example:
```
Input: 1000
Output:
T/n2logn: 1.0
T/n3: 0.01
Tight bound: Theta(n^2 log n)
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nT = n**2 * math.log2(n) + 3*n\nr1 = round(T / (n**2 * math.log2(n)), 4)\nr2 = round(T / n**3, 4)\nprint(f'T/n2logn: {r1}')\nprint(f'T/n3: {r2}')\nprint('Tight bound: Theta(n^2 log n)')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Compute the **Lagrange remainder** of the Taylor series for sin(x).

sin(x) = x − x³/3! + x⁵/5! − ...

Remainder after n terms (last term is x^{2n-1}/(2n-1)!):
R_n(x) = (−1)^n × sin(c) × x^{2n+1} / (2n+1)! for some c between 0 and x.

Bound: |R_n(x)| ≤ |x|^{2n+1} / (2n+1)!

Read `x` and `n` (number of terms). Print Taylor, exact, and |R_n| bound (8 dp each).

Example:
```
Input:
1.0
5
Output:
Taylor: 0.84147098
Exact: 0.84147098
|R_n| bound: 0.00000028
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\nn = int(input())\ntaylor = sum((-1)**k * x**(2*k+1) / math.factorial(2*k+1) for k in range(n))\nexact = math.sin(x)\nbound = abs(x)**(2*n+1) / math.factorial(2*n+1)\nprint(f'Taylor: {round(taylor, 8)}')\nprint(f'Exact: {round(exact, 8)}')\nprint(f'|R_n| bound: {round(bound, 8)}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Compute the **asymptotic expansion** of Γ(n+1) = n! using **Stirling's approximation**.

n! ≈ √(2πn) × (n/e)^n × (1 + 1/(12n) + 1/(288n²) − 139/(51840n³))

Read `n`. Print:
- Exact n! (integer)
- Stirling approximation (6 dp)
- Relative error: |exact − approx| / exact (8 dp)

Example:
```
Input: 10
Output:
Exact: 3628800
Stirling: 3628800.410546
Relative error: 0.00000011
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nexact = math.factorial(n)\nstirling = math.sqrt(2*math.pi*n) * (n/math.e)**n * (1 + 1/(12*n) + 1/(288*n**2) - 139/(51840*n**3))\nprint(f'Exact: {exact}')\nprint(f'Stirling: {round(stirling, 6)}')\nprint(f'Relative error: {round(abs(exact - stirling)/exact, 8)}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Find the **minimum number of Taylor terms** needed for e^x to be accurate within ε.

The error of the n-term Taylor series for e^x at point x is bounded by:
|R_n(x)| ≤ e^|x| × |x|^n / n!

Find the smallest n such that this bound < ε.

Read `x` and `eps`. Print n and the actual error |e^x − Taylor_n(x)| rounded to 8 dp.

Example:
```
Input:
2.0
1e-6
Output:
n: 14
Actual error: 0.00000014
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\neps = float(input())\n\nexact = math.exp(x)\nbound = math.exp(abs(x))\n# Find smallest n such that bound * |x|^n / n! < eps\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.7: Gaussian Quadrature & Adaptive Integration (Q31–Q35)
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Implement **2-point Gaussian quadrature** on [−1, 1].

∫_{-1}^{1} f(x) dx ≈ f(−1/√3) + f(1/√3)

For general [a, b], change of variables: ∫_a^b f(x) dx ≈ (b−a)/2 × [f(m−s/√3) + f(m+s/√3)]
where m = (a+b)/2, s = (b−a)/2.

Function choices: 1=x², 2=e^x, 3=sin(x)

Read function index, `a`, `b`. Print the Gaussian quadrature result and exact value (both 8 dp).

Example:
```
Input:
1
0
1
Output:
Gauss: 0.33333333
Exact: 0.33333333
```
MD,
                'starter_code'        => "import math\n\nidx = int(input())\na = float(input())\nb = float(input())\n\ndef f(x):\n    if idx == 1: return x**2\n    if idx == 2: return math.exp(x)\n    return math.sin(x)\n\n# 2-point Gauss quadrature on [a,b]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Implement **5-point Gaussian quadrature** (Gauss-Legendre) on [−1, 1].

Nodes: x = {0, ±√(3/7), ±(1/3)√(3+4√(6/5))} (approximate: 0, ±0.538469, ±0.906180)
Weights: w = {8/9, (322+13√70)/900, (322−13√70)/900}

Read function index (1=sin, 2=e^x, 3=x^4), `a`, `b`. Print the 5-point estimate and exact integral (8 dp).

Example:
```
Input:
3
0
1
Output:
Gauss-5: 0.2
Exact: 0.2
```
MD,
                'starter_code'        => "import math\n\nidx = int(input())\na = float(input())\nb = float(input())\n\ndef f(x):\n    if idx == 1: return math.sin(x)\n    if idx == 2: return math.exp(x)\n    return x**4\n\n# Gauss-Legendre 5-point nodes and weights on [-1,1]\nnodes = [0.0, 0.538469310105683, -0.538469310105683, 0.906179845938664, -0.906179845938664]\nweights = [8/9, (322+13*math.sqrt(70))/900, (322+13*math.sqrt(70))/900,\n           (322-13*math.sqrt(70))/900, (322-13*math.sqrt(70))/900]\n# Change of variables to [a,b]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Implement **adaptive Simpson's rule** that recursively refines the interval when the error is too large.

Algorithm:
1. Compute S = Simpson on [a, b].
2. Compute S_L = Simpson on [a, mid] and S_R = Simpson on [mid, b].
3. If |S_L + S_R − S| < 15 × tol: return S_L + S_R + (S_L + S_R − S)/15 (Richardson).
4. Else: recurse on [a, mid] and [mid, b] with tol/2.

Use f(x) = 1/(1 + x²).

Read `a`, `b`, `tol`. Print the integral rounded to 8 dp and the exact value arctan(b) − arctan(a).

Example:
```
Input:
0
1
1e-6
Output:
Adaptive: 0.78539816
Exact: 0.78539816
```
MD,
                'starter_code'        => "import math\n\ndef f(x): return 1 / (1 + x**2)\n\ndef simpson(a, b):\n    mid = (a + b) / 2\n    return (b - a) / 6 * (f(a) + 4*f(mid) + f(b))\n\ndef adaptive_simpson(a, b, tol, whole):\n    mid = (a + b) / 2\n    left = simpson(a, mid)\n    right = simpson(mid, b)\n    if abs(left + right - whole) < 15 * tol:\n        return left + right + (left + right - whole) / 15\n    return adaptive_simpson(a, mid, tol/2, left) + adaptive_simpson(mid, b, tol/2, right)\n\na = float(input())\nb = float(input())\ntol = float(input())\n# Compute adaptive integral\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Compute a **Romberg integration** table for ∫_0^1 e^x dx.

Romberg table:
R(0,0) = Trapezoidal with h = 1
R(n,0) = Trapezoidal with h = 1/2^n
R(n,m) = (4^m × R(n,m−1) − R(n−1,m−1)) / (4^m − 1)

Read `n` (order, compute up to R(n,n)). Print the Romberg table, each entry rounded to 8 dp.

Example:
```
Input: 3
Output:
R[0][0]: 1.85914091
R[1][0]: 1.75393109
R[1][1]: 1.71828223
R[2][0]: 1.72722780
R[2][1]: 1.71828183
R[2][2]: 1.71828183
R[3][0]: 1.71933596
R[3][1]: 1.71828183
R[3][2]: 1.71828183
R[3][3]: 1.71828183
```
MD,
                'starter_code'        => "import math\n\ndef f(x): return math.exp(x)\na, b = 0.0, 1.0\nn = int(input())\n\nR = [[0.0]*(n+1) for _ in range(n+1)]\nfor i in range(n+1):\n    h = (b-a)/2**i\n    npts = 2**i\n    R[i][0] = h/2 * (f(a) + f(b) + 2*sum(f(a+k*h) for k in range(1,npts)))\n# Richardson extrapolation to fill table\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Estimate the **error of numerical integration** using the composite Simpson error formula.

Error ≤ (b−a)^5 × max|f⁴(x)| / (180 × n⁴)

For f(x) = sin(x), f⁴(x) = sin(x), max on [a,b] = 1.

Read `a`, `b`, `n`. Print:
- Simpson integral (8 dp)
- Error bound (8 dp)
- Exact integral (8 dp)
- Actual error (8 dp)

Example:
```
Input:
0
3.141592653589793
10
Output:
Simpson: 2.00000678
Error bound: 0.00000675
Exact: 2.0
Actual error: 0.00000678
```
MD,
                'starter_code'        => "import math\n\ndef f(x): return math.sin(x)\n\na = float(input())\nb = float(input())\nn = int(input())\ndx = (b - a) / n\nx = [a + i * dx for i in range(n+1)]\ns = f(x[0]) + f(x[-1])\nfor i in range(1, n):\n    s += 4*f(x[i]) if i%2 else 2*f(x[i])\nsimp = s * dx / 3\nerr_bound = (b-a)**5 / (180 * n**4)\nexact = -math.cos(b) + math.cos(a)\nprint(f'Simpson: {round(simp, 8)}')\nprint(f'Error bound: {round(err_bound, 8)}')\nprint(f'Exact: {round(exact, 8)}')\nprint(f'Actual error: {round(abs(simp - exact), 8)}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.8: FTC & Improper Integrals (Q36–Q39)
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Apply the **Dirichlet test** to determine convergence of ∫_1^∞ sin(x)/x dx.

The Dirichlet test: if f(x) monotonically decreases to 0 and g(x) has bounded partial integrals, then ∫ f×g converges.

Verify numerically: compute ∫_1^M sin(x)/x dx for M = 10, 100, 1000, 10000 using Simpson (n=10000).

Print each value (6 dp) and `Converges` (since the limit exists, ≈ π/2 − Si(1) ≈ 0.62).

Example:
```
Output:
M=10: 0.626512
M=100: 0.623706
M=1000: 0.623811
M=10000: 0.623817
Converges
```
MD,
                'starter_code'        => "import math\n\ndef f(x): return math.sin(x) / x\n\nfor M in [10, 100, 1000, 10000]:\n    n = 10000\n    a, b = 1.0, float(M)\n    dx = (b-a)/n\n    pts = [a + i*dx for i in range(n+1)]\n    s = f(pts[0]) + f(pts[-1])\n    for i in range(1, n):\n        s += 4*f(pts[i]) if i%2 else 2*f(pts[i])\n    print(f'M={M}: {round(s*dx/3, 6)}')\nprint('Converges')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Compute the **Gamma function** Γ(n) = (n−1)! numerically for non-integer n using:

Γ(x) = ∫_0^∞ t^{x-1} e^{-t} dt ≈ ∫_0^{50} t^{x-1} e^{-t} dt

Use Simpson's rule with n=100000 on [ε, 50] (ε=1e-6).

Read `x`. Print the numerical Γ(x) and the exact value math.gamma(x), both rounded to 6 dp.

Example:
```
Input: 2.5
Output:
Numerical: 1.329340
Exact: 1.329340
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\na, b = 1e-6, 50.0\nn = 100000\ndx = (b-a)/n\ndef f(t): return t**(x-1) * math.exp(-t)\n# Simpson's Rule for Gamma\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Apply the **comparison test** for improper integrals.

Determine convergence of ∫_1^∞ f(x) dx by comparing with ∫_1^∞ g(x) dx.

For each pair, state whether f converges (based on comparison):
1. f(x) = 1/(x² + 1) compared to g(x) = 1/x²  → converges
2. f(x) = 1/√x compared to g(x) = 1/x → diverges (but compare with 1/√x diverging)
3. f(x) = e^{-x} compared to g(x) = 1/x² → converges

Read comparison index (1, 2, or 3). Print the comparison analysis and conclusion.

Example:
```
Input: 1
Output:
f(x) = 1/(x^2+1), g(x) = 1/x^2
f(x) <= g(x) for x >= 1
integral of g converges (p-series p=2)
Conclusion: f converges
```
MD,
                'starter_code'        => "idx = int(input())\nif idx == 1:\n    print('f(x) = 1/(x^2+1), g(x) = 1/x^2')\n    print('f(x) <= g(x) for x >= 1')\n    print('integral of g converges (p-series p=2)')\n    print('Conclusion: f converges')\nelif idx == 2:\n    print('f(x) = 1/sqrt(x), g(x) = 1/x')\n    print('f(x) >= g(x) for x in (0,1]')\n    print('integral of g diverges (p-series p=1)')\n    print('Conclusion: f diverges')\nelse:\n    print('f(x) = e^-x, g(x) = 1/x^2')\n    print('f(x) <= g(x) for x >= 1')\n    print('integral of g converges (p-series p=2)')\n    print('Conclusion: f converges')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Compute the **Laplace transform** L{f}(s) = ∫_0^∞ e^{-st} f(t) dt numerically.

Use ∫_0^{50} e^{-st} f(t) dt with Simpson's rule (n=100000).

f(t) choices: 1=t (L=1/s²), 2=sin(t) (L=1/(s²+1)), 3=e^t (L=1/(s−1) for s>1)

Read function index and `s`. Print numerical result and exact formula value (6 dp each).

Example:
```
Input:
1
2
Output:
Numerical: 0.25
Exact: 0.25
```
MD,
                'starter_code'        => "import math\n\nidx = int(input())\ns = float(input())\n\ndef f(t):\n    if idx == 1: return t\n    if idx == 2: return math.sin(t)\n    return math.exp(t)\n\na, b = 0.0, 50.0\nn = 100000\ndx = (b-a)/n\npts = [a + i*dx for i in range(n+1)]\nintegrand = lambda t: math.exp(-s*t) * f(t)\n# Simpson's rule for Laplace transform\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.9: Multivariable Integration & Jacobian (Q40–Q44)
            // ═══════════════════════════════════════════════════════════════

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Compute a **double integral** ∫∫_R f(x,y) dA numerically using nested Simpson's rule.

∫_a^b ∫_c^d f(x,y) dy dx

Use f(x,y) = x²y and n=100 subintervals for each dimension.

Read `a b c d`. Print the result rounded to 6 dp.

Example:
```
Input:
0 1 0 2
Output: 0.666667
```
MD,
                'starter_code'        => "def f(x, y): return x**2 * y\n\nparams = input().split()\na, b, c, d = float(params[0]), float(params[1]), float(params[2]), float(params[3])\nn = 100\ndx = (b-a)/n\n# Nested Simpson's Rule\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Apply **change of variables** (Jacobian) for polar coordinates.

∫∫_R f(x,y) dA = ∫∫ f(r cos θ, r sin θ) × r dr dθ

Compute ∫∫_{x²+y²≤R²} (x²+y²) dA = ∫_0^{2π} ∫_0^R r² × r dr dθ = 2π × R⁴/4

Read `R`. Print numerically (using 1000×1000 grid in polar) and analytically (6 dp each).

Example:
```
Input: 2
Output:
Numerical: 25.132741
Analytical: 25.132741
```
MD,
                'starter_code'        => "import math\n\nR = float(input())\nnr, ntheta = 1000, 1000\ndr = R / nr\ndtheta = 2 * math.pi / ntheta\ntotal = 0.0\nfor i in range(nr):\n    r = (i + 0.5) * dr\n    for j in range(ntheta):\n        total += r**2 * r * dr * dtheta\nanalytical = 2 * math.pi * R**4 / 4\nprint(f'Numerical: {round(total, 6)}')\nprint(f'Analytical: {round(analytical, 6)}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Compute the **2D Jacobian** of a coordinate transformation.

For the transformation T(u, v) = (u×v, u²−v):
J = |∂(x,y)/∂(u,v)| = |det([[v, u],[2u, -1]])| = |−v − 2u²|

Read `u` and `v`. Print the 2×2 Jacobian matrix (4 dp each) and its determinant (4 dp).

Example:
```
Input:
1
2
Output:
2.0 1.0
2.0 -1.0
det: -4.0
```
MD,
                'starter_code'        => "u = float(input())\nv = float(input())\n# T(u,v) = (u*v, u^2 - v)\n# J = [[v, u],[2u, -1]]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Compute the **surface area** of z = f(x,y) = x² + y² over the region [0,1]×[0,1].

Surface area = ∫∫ √(1 + (∂f/∂x)² + (∂f/∂y)²) dA

∂f/∂x = 2x, ∂f/∂y = 2y, so integrand = √(1 + 4x² + 4y²).

Use nested Simpson's rule with n=100. Print the result rounded to 6 dp.

Example:
```
Output: 1.862184
```
MD,
                'starter_code'        => "import math\n\ndef integrand(x, y): return math.sqrt(1 + 4*x**2 + 4*y**2)\n\nn = 100\na, b = 0.0, 1.0\ndx = (b-a)/n\n# Nested Simpson's Rule for surface area\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Compute the **centroid** of a 2D region using double integrals.

For the region bounded by y = x² and y = x:

Area = ∫_0^1 (x − x²) dx = 1/6
x̄ = (1/A) ∫∫ x dA = (1/A) ∫_0^1 x(x−x²) dx = 1/2
ȳ = (1/A) ∫∫ y dA = (1/A) ∫_0^1 ∫_{x²}^x y dy dx = 2/5

Print A, x̄, ȳ each rounded to 6 dp (use numerical integration, n=1000).

Example:
```
Output:
Area: 0.166667
Centroid x: 0.5
Centroid y: 0.4
```
MD,
                'starter_code'        => "n = 1000\na, b = 0.0, 1.0\ndx = (b-a)/n\n\n# Numerical integration for Area, x-moment, y-moment\nA_sum = 0.0\nMx_sum = 0.0\nMy_sum = 0.0\nfor i in range(n+1):\n    x = a + i*dx\n    h_x = x - x**2  # height at this x\n    w = 1 if i==0 or i==n else (4 if i%2 else 2)\n    A_sum += w * h_x\n    Mx_sum += w * x * h_x\n    # y-centroid: average of bounds weighted by height\n    My_sum += w * (x + x**2) / 2 * h_x\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4.10: Power Series, Radius of Convergence & Fourier (Q45–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Compute the **radius of convergence** of a power series using the ratio test.

R = lim_{n→∞} |a_n / a_{n+1}|

For each series, compute the ratio for large n:
1. Σ x^n / n  →  R = 1
2. Σ x^n / n!  →  R = ∞
3. Σ n! x^n  →  R = 0
4. Σ x^n / n^2  →  R = 1

Read index. Print the ratios for n=10,100 (6 dp) and R.

Example:
```
Input: 1
Output:
ratio n=10: 1.1
ratio n=100: 1.01
R: 1.0
```
MD,
                'starter_code'        => "import math\n\nidx = int(input())\n\ndef a(n):\n    if idx == 1: return 1/n\n    if idx == 2: return 1/math.factorial(n)\n    if idx == 3: return float(math.factorial(n))\n    return 1/n**2\n\nfor n in [10, 100]:\n    ratio = round(abs(a(n)/a(n+1)), 6)\n    print(f'ratio n={n}: {ratio}')\n# Print R\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Evaluate a **power series at a given x** and check convergence using the ratio test result.

Σ_{n=0}^{N} x^n / n! (Taylor series for e^x)

Read `x` and `N` (number of terms). Print:
- Partial sum (8 dp)
- Next term magnitude |x^N / N!| (8 dp) — if < 1e-10, print `Converged`
- Comparison to math.exp(x) (8 dp)

Example:
```
Input:
0.5
15
Output:
Sum: 1.64872127
Next term: 0.00000000
Converged
Exact: 1.64872127
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\nN = int(input())\ntotal = sum(x**n / math.factorial(n) for n in range(N))\nnext_term = abs(x**N / math.factorial(N))\nprint(f'Sum: {round(total, 8)}')\nprint(f'Next term: {round(next_term, 8)}')\nif next_term < 1e-10:\n    print('Converged')\nprint(f'Exact: {round(math.exp(x), 8)}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Compute the **Fourier coefficients** of f(x) on [−π, π].

a₀ = (1/π) ∫_{-π}^{π} f(x) dx
aₙ = (1/π) ∫_{-π}^{π} f(x) cos(nx) dx
bₙ = (1/π) ∫_{-π}^{π} f(x) sin(nx) dx

For f(x) = x² (even, so bₙ = 0):
a₀ = 2π²/3, aₙ = 4(−1)^n / n²

Read `n_terms`. Print a₀, then aₙ and bₙ for n=1..n_terms (each rounded to 6 dp), using Simpson (N=10000 subintervals).

Example:
```
Input: 3
Output:
a0: 6.579736
a1: -4.0, b1: 0.0
a2: 1.0, b2: 0.0
a3: -0.444444, b3: 0.0
```
MD,
                'starter_code'        => "import math\n\ndef f(x): return x**2\n\na_pi, b_pi = -math.pi, math.pi\nN = 10000\ndx = (b_pi - a_pi) / N\npts = [a_pi + i*dx for i in range(N+1)]\n\nn_terms = int(input())\n# Compute a0 and an, bn for n=1..n_terms using Simpson\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Compute the **Fourier series approximation** and measure Gibbs phenomenon.

For f(x) = sign(x) on [−π, π] (= +1 for x>0, −1 for x<0, 0 at x=0):
bₙ = 4/(nπ) for odd n, 0 for even n
Fourier series: Σ bₙ sin(nx)

Read `n_terms` and `x`. Compute the Fourier approximation at x.

Print the approximation (6 dp) and sign(x) value.

Example:
```
Input:
50
0.1
Output:
Fourier approx: 1.117858
sign(x): 1
```
MD,
                'starter_code'        => "import math\n\nn_terms = int(input())\nx = float(input())\n\napprox = sum(4/(n*math.pi) * math.sin(n*x) for n in range(1, 2*n_terms, 2))\nprint(f'Fourier approx: {round(approx, 6)}')\nprint(f'sign(x): {1 if x > 0 else (-1 if x < 0 else 0)}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Apply **Parseval's theorem** to verify the Fourier series energy identity.

For f(x) = x on [−π, π]:
(1/π) ∫_{-π}^π f(x)² dx = a₀²/2 + Σ(aₙ² + bₙ²)

Since f(x) = x is odd: a₀ = aₙ = 0, bₙ = 2(−1)^{n+1}/n.
Σ bₙ² = 4 Σ 1/n² = 4 × π²/6 = 2π²/3.
And (1/π)∫_{-π}^π x² dx = 2π²/3. ✓

Read `n_terms`. Print:
- LHS: (1/π)∫ x² dx (6 dp)
- RHS: Σ bₙ² for n=1..n_terms (6 dp)
- Parseval verified: yes/no (within 1%)

Example:
```
Input: 100
Output:
LHS: 6.579736
RHS: 6.448789
Parseval verified: yes
```
MD,
                'starter_code'        => "import math\n\nn_terms = int(input())\nlhs = 2 * math.pi**2 / 3\nrhs = sum((2*(-1)**(n+1)/n)**2 for n in range(1, n_terms+1))\nprint(f'LHS: {round(lhs, 6)}')\nprint(f'RHS: {round(rhs, 6)}')\nprint('Parseval verified: yes' if abs(lhs - rhs)/lhs < 0.01 else 'Parseval verified: no')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 175,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Build a **complete calculus pipeline** for a function f(x) = x³ − 6x² + 11x − 6 on [0, 4].

Produce the following analysis report:

```
=== FUNCTION ANALYSIS: f(x) = x^3 - 6x^2 + 11x - 6 ===

Roots (Newton-Raphson from x=0.5, 2.5, 3.5):
  x1: <val>
  x2: <val>
  x3: <val>

Critical points (where f'(x)=0, Newton on f''):
  x_crit1: <val> → <local min/max>
  x_crit2: <val> → <local min/max>

Definite integral ∫_0^4 f(x) dx (Simpson, n=1000):
  I: <val>

Average value on [0,4]:
  avg: <val>

Taylor approx at x=2 (degree 3):
  f(x) ≈ <c0> + <c1>(x-2) + <c2>(x-2)^2 + <c3>(x-2)^3
```

All values rounded to 4 dp. Print the exact formatted report.

Example:
```
Output:
=== FUNCTION ANALYSIS: f(x) = x^3 - 6x^2 + 11x - 6 ===
...
```
MD,
                'starter_code'        => "import math\n\ndef f(x): return x**3 - 6*x**2 + 11*x - 6\nh = 1e-6\ndef fp(x): return (f(x+h) - f(x-h))/(2*h)\ndef fpp(x): return (f(x+h) - 2*f(x) + f(x-h))/h**2\n\n# Full calculus pipeline\n",
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

        // ── Q1: Cauchy condition ──────────────────────────────────────────
        $seed(1, [
            ['input' => "0.01",    'expected_output' => "N: 100",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.1",     'expected_output' => "N: 10",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.001",   'expected_output' => "N: 1000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5",     'expected_output' => "N: 2",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Lipschitz constant ────────────────────────────────────────
        $seed(2, [
            ['input' => "1\n0\n3.141592653589793",   'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0\n2",                   'expected_output' => "4.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0\n1",                   'expected_output' => "2.7183", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n-1\n1",                  'expected_output' => "2.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: uniform continuity ────────────────────────────────────────
        $seed(3, [
            ['input' => "1\n0\n6.283185307179586\n0.1",   'expected_output' => "delta: 0.1",                        'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0\n1\n0.1",                   'expected_output' => "delta: 0.1",                        'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0\n3.141592653589793\n0.01",  'expected_output' => "delta: 0.01",                       'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0\n1\n0.5",                   'expected_output' => "Not uniformly continuous on this interval", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q4: Bolzano-Weierstrass ───────────────────────────────────────
        $seed(4, [
            ['input' => "100\n0.05",   'expected_output' => "3 13 16 19 22",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "50\n0.1",     'expected_output' => "3 6 9 13 16",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "200\n0.02",   'expected_output' => "25 44 50 57 63",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "30\n0.2",     'expected_output' => "3 6 9 13 16",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: modulus of continuity ─────────────────────────────────────
        $seed(5, [
            ['input' => "0.25",   'expected_output' => "Numerical: 0.5\nAnalytical: 0.5",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0",    'expected_output' => "Numerical: 1.0\nAnalytical: 1.0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.01",   'expected_output' => "Numerical: 0.1\nAnalytical: 0.1",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "4.0",    'expected_output' => "Numerical: 2.0\nAnalytical: 2.0",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: order of convergence ──────────────────────────────────────
        $seed(6, [
            ['input' => "",   'expected_output' => "e(10): 0.01\ne(11): 0.00826446\ne(12): 0.00694444\nOrder p: 1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "",   'expected_output' => "e(10): 0.01\ne(11): 0.00826446\ne(12): 0.00694444\nOrder p: 1.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "",   'expected_output' => "e(10): 0.01\ne(11): 0.00826446\ne(12): 0.00694444\nOrder p: 1.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "",   'expected_output' => "e(10): 0.01\ne(11): 0.00826446\ne(12): 0.00694444\nOrder p: 1.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Aitken's Δ² acceleration ─────────────────────────────────
        $seed(7, [
            ['input' => "",   'expected_output' => "n=5: a_n=1.46361111, A_n=1.64136498, true=1.64493407\nn=10: a_n=1.54976773, A_n=1.64398636, true=1.64493407\nn=15: a_n=1.58007848, A_n=1.64459694, true=1.64493407",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "",   'expected_output' => "n=5: a_n=1.46361111, A_n=1.64136498, true=1.64493407\nn=10: a_n=1.54976773, A_n=1.64398636, true=1.64493407\nn=15: a_n=1.58007848, A_n=1.64459694, true=1.64493407",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "",   'expected_output' => "n=5: a_n=1.46361111, A_n=1.64136498, true=1.64493407\nn=10: a_n=1.54976773, A_n=1.64398636, true=1.64493407\nn=15: a_n=1.58007848, A_n=1.64459694, true=1.64493407",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "",   'expected_output' => "n=5: a_n=1.46361111, A_n=1.64136498, true=1.64493407\nn=10: a_n=1.54976773, A_n=1.64398636, true=1.64493407\nn=15: a_n=1.58007848, A_n=1.64459694, true=1.64493407",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Richardson extrapolation ──────────────────────────────────
        $seed(8, [
            ['input' => "1.0\n0.1",   'expected_output' => "D(h): 0.54030231\nD(h/2): 0.54030231\nRichardson: 0.54030231\nExact cos(x): 0.54030231",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n0.1",   'expected_output' => "D(h): 1.0\nD(h/2): 1.0\nRichardson: 1.0\nExact cos(x): 1.0",                               'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.5708\n0.01",'expected_output'=> "D(h): 0.0\nD(h/2): 0.0\nRichardson: 0.0\nExact cos(x): 0.0",                              'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n0.01",  'expected_output' => "D(h): 0.87758256\nD(h/2): 0.87758256\nRichardson: 0.87758256\nExact cos(x): 0.87758256",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Steffensen's method ───────────────────────────────────────
        $seed(9, [
            ['input' => "2.0\n20",   'expected_output' => "Root: 1.4142135624\nIterations: 3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.5\n20",   'expected_output' => "Root: 1.4142135624\nIterations: 3",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n20",   'expected_output' => "Root: 1.4142135624\nIterations: 4",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3.0\n20",   'expected_output' => "Root: 1.4142135624\nIterations: 4",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: GD convergence rate ──────────────────────────────────────
        $seed(10, [
            ['input' => "10\n0.1\n5",   'expected_output' => "x1: 8.0\nx2: 6.4\nx3: 5.12\nx4: 4.096\nx5: 3.2768\nRate (theory): 0.8\nRate (empirical): 0.8",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n0.1\n5",    'expected_output' => "x1: 4.0\nx2: 3.2\nx3: 2.56\nx4: 2.048\nx5: 1.6384\nRate (theory): 0.8\nRate (empirical): 0.8",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0.25\n5",   'expected_output' => "x1: 2.0\nx2: 1.0\nx3: 0.5\nx4: 0.25\nx5: 0.125\nRate (theory): 0.5\nRate (empirical): 0.5",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0.4\n5",    'expected_output' => "x1: 0.2\nx2: 0.04\nx3: 0.008\nx4: 0.0016\nx5: 0.00032\nRate (theory): 0.2\nRate (empirical): 0.2",'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q11: multivariate limit (fixed) ───────────────────────────────
        $seed(11, [
            ['input' => "",   'expected_output' => "Path y=x: 0.0\nPath y=x²: 0.5\nLimit does not exist",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "",   'expected_output' => "Path y=x: 0.0\nPath y=x²: 0.5\nLimit does not exist",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "",   'expected_output' => "Path y=x: 0.0\nPath y=x²: 0.5\nLimit does not exist",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "",   'expected_output' => "Path y=x: 0.0\nPath y=x²: 0.5\nLimit does not exist",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: horizontal asymptote ─────────────────────────────────────
        $seed(12, [
            ['input' => "3\n10\n100\n1000",         'expected_output' => "10: 2.970297\n100: 2.999700\n1000: 2.999997\nAsymptote: 3.0\nCrosses: no",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10\n100",               'expected_output' => "10: 2.970297\n100: 2.999700\nAsymptote: 3.0\nCrosses: no",                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1000",                  'expected_output' => "1000: 2.999997\nAsymptote: 3.0\nCrosses: no",                                'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n100\n1000\n10000",  'expected_output' => "10: 2.970297\n100: 2.999700\n1000: 2.999997\n10000: 3.0\nAsymptote: 3.0\nCrosses: no",'is_hidden'=>true,'order_index'=>4],
        ]);

        // ── Q13: bisection with convergence ───────────────────────────────
        $seed(13, [
            ['input' => "5",    'expected_output' => "Step 1: 0.78539816\nStep 2: 0.39269908\nStep 3: 0.58904862\nStep 4: 0.73722389\nStep 5: 0.66313626\nRoot: 0.66313626\nError bound: 0.04908739",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3",    'expected_output' => "Step 1: 0.78539816\nStep 2: 0.39269908\nStep 3: 0.58904862\nRoot: 0.58904862\nError bound: 0.19634954",                                            'is_hidden' => false, 'order_index' => 2],
            ['input' => "10",   'expected_output' => "Step 1: 0.78539816\nStep 2: 0.39269908\nStep 3: 0.58904862\nStep 4: 0.73722389\nStep 5: 0.66313626\nStep 6: 0.70018007\nStep 7: 0.68165817\nStep 8: 0.73722389\nStep 9: 0.72869603\nStep 10: 0.73296\nRoot: 0.73296\nError bound: 0.00153398",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "20",   'expected_output' => "Root: 0.73908513\nError bound: 0.0",   'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q14: fixed-point iteration ────────────────────────────────────
        $seed(14, [
            ['input' => "1.0\n1000",   'expected_output' => "Fixed point: 0.7390851332\nIterations: 86\nConvergence rate: 0.6736",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1000",   'expected_output' => "Fixed point: 0.7390851332\nIterations: 92\nConvergence rate: 0.6736",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n1000",   'expected_output' => "Fixed point: 0.7390851332\nIterations: 75\nConvergence rate: 0.6736",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.0\n1000",   'expected_output' => "Fixed point: 0.7390851332\nIterations: 90\nConvergence rate: 0.6736",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: contraction mapping ──────────────────────────────────────
        $seed(15, [
            ['input' => "1.0\n10",   'expected_output' => "k: 0.841471\nTheoretical bound: 0.064547\nActual error: 0.019703",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n10",   'expected_output' => "k: 0.841471\nTheoretical bound: 0.073415\nActual error: 0.027432",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n20",   'expected_output' => "k: 0.841471\nTheoretical bound: 0.018721\nActual error: 0.000393",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n15",   'expected_output' => "k: 0.841471\nTheoretical bound: 0.032483\nActual error: 0.003988",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: dual number AD ───────────────────────────────────────────
        $seed(16, [
            ['input' => "1.0",   'expected_output' => "f(x): 3.559410\nf'(x): 5.748738",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0",   'expected_output' => "f(x): 1.0\nf'(x): 1.0",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0",   'expected_output' => "f(x): 15.237164\nf'(x): 9.286627",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5",   'expected_output' => "f(x): 1.769736\nf'(x): 2.903148",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Jacobian ─────────────────────────────────────────────────
        $seed(17, [
            ['input' => "1\n2",   'expected_output' => "2.0 1.0\n1.080604 1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n0",   'expected_output' => "0.0 1.0\n0.0 0.0",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1",   'expected_output' => "2.0 1.0\n0.540302 1.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n3",   'expected_output' => "4.0 1.0\n-0.832294 2.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Hessian ─────────────────────────────────────────────────
        $seed(18, [
            ['input' => "1\n1",   'expected_output' => "6.0 -3.0\n-3.0 6.0\nlocal min",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n0",   'expected_output' => "0.0 -3.0\n-3.0 0.0\nsaddle",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n2",   'expected_output' => "12.0 -3.0\n-3.0 12.0\nlocal min", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0",   'expected_output' => "6.0 -3.0\n-3.0 0.0\nsaddle",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: reverse-mode AD ──────────────────────────────────────────
        $seed(19, [
            ['input' => "1\n2",     'expected_output' => "f: 2.727892\ndf/dx: 0.091289\ndf/dy: 1.638948",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n0",     'expected_output' => "f: 0.0\ndf/dx: 0.0\ndf/dy: 0.0",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1",     'expected_output' => "f: 1.682942\ndf/dx: 1.144741\ndf/dy: 1.144741",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n2",   'expected_output' => "f: 2.204751\ndf/dx: -0.636386\ndf/dy: 2.004751",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: directional derivative ───────────────────────────────────
        $seed(20, [
            ['input' => "1\n2\n1\n1",   'expected_output' => "6.363961",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n0\n1\n0",   'expected_output' => "0.0",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1\n1\n0",   'expected_output' => "2.0",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n3\n0\n1",   'expected_output' => "12.0",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Newton 2D optimisation ───────────────────────────────────
        $seed(21, [
            ['input' => "5\n5\n3",   'expected_output' => "Iter 1: (2.142857, 1.428571)\nIter 2: (0.918367, 0.612245)\nIter 3: (0.393586, 0.262391)\nFinal: (0.393586, 0.262391)",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n0\n1",   'expected_output' => "Iter 1: (0.0, 0.0)\nFinal: (0.0, 0.0)",                                                                                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n3\n5",   'expected_output' => "Iter 1: (1.285714, 0.857143)\nIter 2: (0.551020, 0.367347)\nIter 3: (0.236152, 0.157434)\nIter 4: (0.101208, 0.067472)\nIter 5: (0.043375, 0.028917)\nFinal: (0.043375, 0.028917)",'is_hidden' => true, 'order_index' => 3],
            ['input' => "10\n10\n2",'expected_output' => "Iter 1: (4.285714, 2.857143)\nIter 2: (1.836735, 1.224490)\nFinal: (1.836735, 1.224490)",'is_hidden' => true,'order_index' => 4],
        ]);

        // ── Q22: gradient descent with momentum ───────────────────────────
        $seed(22, [
            ['input' => "3\n0.01\n0.9\n5",   'expected_output' => "x1: 2.730000\nx2: 2.463933\nx3: 2.201876\nx4: 1.947066\nx5: 1.702978",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.01\n0.9\n5",   'expected_output' => "x1: 1.840000\nx2: 1.697600\nx3: 1.569248\nx4: 1.453696\nx5: 1.349617",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0.01\n0.0\n5",   'expected_output' => "x1: 0.0\nx2: 0.0\nx3: 0.0\nx4: 0.0\nx5: 0.0",                            'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n0.005\n0.8\n5",  'expected_output' => "x1: 4.500000\nx2: 4.020000\nx3: 3.557600\nx4: 3.116800\nx5: 2.700864",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Adam optimiser ───────────────────────────────────────────
        $seed(23, [
            ['input' => "5\n0.1\n50",   'expected_output' => "Step 10: 4.003399\nStep 20: 3.121025\nStep 30: 2.381826\nStep 40: 2.298726\nStep 50: 2.250004",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0.1\n50",   'expected_output' => "Step 10: 3.101060\nStep 20: 2.410956\nStep 30: 2.291512\nStep 40: 2.253006\nStep 50: 2.250001",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0.1\n50",   'expected_output' => "Step 10: 0.999986\nStep 20: 1.250000\nStep 30: 1.500000\nStep 40: 1.750000\nStep 50: 2.000000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n0.5\n50",  'expected_output' => "Step 10: 3.014316\nStep 20: 2.250199\nStep 30: 2.250000\nStep 40: 2.25\nStep 50: 2.25",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: backtracking line search ─────────────────────────────────
        $seed(24, [
            ['input' => "2.0\n4",   'expected_output' => "Step 1: alpha=0.0625, x=1.750000\nStep 2: alpha=0.125, x=1.386581\nStep 3: alpha=0.25, x=0.993987\nStep 4: alpha=0.5, x=0.999996",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.5\n3",   'expected_output' => "Step 1: alpha=0.125, x=1.136719\nStep 2: alpha=0.25, x=0.866974\nStep 3: alpha=0.25, x=0.999866",                                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0\n3",   'expected_output' => "Step 1: alpha=0.03125, x=2.695313\nStep 2: alpha=0.0625, x=2.278809\nStep 3: alpha=0.125, x=1.768066",                               'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n3",   'expected_output' => "Step 1: alpha=1.0, x=1.0\nStep 2: alpha=0.5, x=0.999999\nStep 3: alpha=0.25, x=0.999999",                                            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: conjugate gradient ───────────────────────────────────
        $seed(25, [
            ['input' => "0 0",   'expected_output' => "x: 0.090909\ny: 0.636364\nIterations: 2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1",   'expected_output' => "x: 0.090909\ny: 0.636364\nIterations: 2",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1 2",  'expected_output' => "x: 0.090909\ny: 0.636364\nIterations: 2",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5 0.5", 'expected_output' => "x: 0.090909\ny: 0.636364\nIterations: 2", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Taylor Remainder ─────────────────────────────────────────
        $seed(26, [
            ['input' => "0.5\n5",   'expected_output' => "Taylor: 0.40104167\nExact: 0.40546511\nRemainder bound: 0.00520833",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.1\n3",   'expected_output' => "Taylor: 0.09533333\nExact: 0.09531018\nRemainder bound: 0.00002500",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.2\n4",   'expected_output' => "Taylor: 0.18226667\nExact: 0.18232156\nRemainder bound: 0.00006400",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n10",  'expected_output' => "Taylor: 0.64563492\nExact: 0.69314718\nRemainder bound: 0.09090909",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Asymptotic Analysis ──────────────────────────────────────
        $seed(27, [
            ['input' => "1000",  'expected_output' => "T/n2logn: 1.0\nT/n3: 0.01\nTight bound: Theta(n^2 log n)",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "10",    'expected_output' => "T/n2logn: 1.0\nT/n3: 0.1\nTight bound: Theta(n^2 log n)",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "100",   'expected_output' => "T/n2logn: 1.0\nT/n3: 0.03\nTight bound: Theta(n^2 log n)",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5000",  'expected_output' => "T/n2logn: 1.0\nT/n3: 0.002\nTight bound: Theta(n^2 log n)",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Lagrange Remainder ───────────────────────────────────────
        $seed(28, [
            ['input' => "1.0\n5",   'expected_output' => "Taylor: 0.84147098\nExact: 0.84147098\n|R_n| bound: 0.00000028",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n3",   'expected_output' => "Taylor: 0.47942553\nExact: 0.47942554\n|R_n| bound: 0.00000155",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n5",   'expected_output' => "Taylor: 0.90934744\nExact: 0.90929743\n|R_n| bound: 0.00051307",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3.1415\n8",'expected_output' => "Taylor: 0.00009265\nExact: 0.00009265\n|R_n| bound: 0.00000080",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Stirling's Approximation ─────────────────────────────────
        $seed(29, [
            ['input' => "10",  'expected_output' => "Exact: 3628800\nStirling: 3628800.410546\nRelative error: 0.00000011",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5",   'expected_output' => "Exact: 120\nStirling: 120.000305\nRelative error: 0.00000254",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "12",  'expected_output' => "Exact: 479001600\nStirling: 479001601.76985\nRelative error: 0.00000004", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "15",  'expected_output' => "Exact: 1307674368000\nStirling: 1307674368003.1118\nRelative error: 0.00000000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q30: Taylor Terms Needed ──────────────────────────────────────
        $seed(30, [
            ['input' => "2.0\n1e-6",   'expected_output' => "n: 14\nActual error: 0.00000014",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n1e-4",   'expected_output' => "n: 8\nActual error: 0.00002453",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0\n1e-5",   'expected_output' => "n: 17\nActual error: 0.00000331",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n1e-8",   'expected_output' => "n: 9\nActual error: 0.00000000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: 2-point Gaussian Quadrature ──────────────────────────────
        $seed(31, [
            ['input' => "1\n0\n1",  'expected_output' => "Gauss: 0.33333333\nExact: 0.33333333",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0\n1",  'expected_output' => "Gauss: 1.71756610\nExact: 1.71828183",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0\n2",  'expected_output' => "Gauss: 1.41940989\nExact: 1.41614684",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n-1\n1", 'expected_output' => "Gauss: 0.66666667\nExact: 0.66666667",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: 5-point Gaussian Quadrature ──────────────────────────────
        $seed(32, [
            ['input' => "3\n0\n1",  'expected_output' => "Gauss-5: 0.2\nExact: 0.2",               'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0\n3.1415926535", 'expected_output' => "Gauss-5: 2.0\nExact: 2.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0\n1",  'expected_output' => "Gauss-5: 1.71828183\nExact: 1.71828183", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n-2\n2", 'expected_output' => "Gauss-5: 12.8\nExact: 12.8",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Adaptive Simpson's Rule ──────────────────────────────────
        $seed(33, [
            ['input' => "0\n1\n1e-6",   'expected_output' => "Adaptive: 0.78539816\nExact: 0.78539816",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n2\n1e-5",   'expected_output' => "Adaptive: 1.10714872\nExact: 1.10714872",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1\n1\n1e-6",  'expected_output' => "Adaptive: 1.57079633\nExact: 1.57079633",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n5\n1e-8",   'expected_output' => "Adaptive: 1.37340077\nExact: 1.37340077",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Romberg Integration ──────────────────────────────────────
        $seed(34, [
            ['input' => "3", 'expected_output' => "R[0][0]: 1.85914091\nR[1][0]: 1.75393109\nR[1][1]: 1.71828223\nR[2][0]: 1.72722780\nR[2][1]: 1.71832671\nR[2][2]: 1.71828300\nR[3][0]: 1.72051859\nR[3][1]: 1.71828219\nR[3][2]: 1.71827922\nR[3][3]: 1.71828183", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1", 'expected_output' => "R[0][0]: 1.85914091\nR[1][0]: 1.75393109\nR[1][1]: 1.71828223", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2", 'expected_output' => "R[0][0]: 1.85914091\nR[1][0]: 1.75393109\nR[1][1]: 1.71828223\nR[2][0]: 1.72722780\nR[2][1]: 1.71832671\nR[2][2]: 1.71828300", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0", 'expected_output' => "R[0][0]: 1.85914091", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Simpson Error Bound ──────────────────────────────────────
        $seed(35, [
            ['input' => "0\n3.141592653589793\n10", 'expected_output' => "Simpson: 2.00000678\nError bound: 0.00000675\nExact: 2.0\nActual error: 0.00000678",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n1.570796326794896\n4",  'expected_output' => "Simpson: 0.99992631\nError bound: 0.00020815\nExact: 1.0\nActual error: 0.00007369",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n3.141592653589793\n20", 'expected_output' => "Simpson: 2.00000042\nError bound: 0.00000042\nExact: 2.0\nActual error: 0.00000042",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n6.283185307179586\n10", 'expected_output' => "Simpson: 0.00021703\nError bound: 0.00021612\nExact: 0.0\nActual error: 0.00021703",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Dirichlet Test ───────────────────────────────────────────
        $seed(36, [
            ['input' => " ", 'expected_output' => "M=10: 0.626512\nM=100: 0.623706\nM=1000: 0.623811\nM=10000: 0.623817\nConverges", 'is_hidden' => false, 'order_index' => 1],
            ['input' => " ", 'expected_output' => "M=10: 0.626512\nM=100: 0.623706\nM=1000: 0.623811\nM=10000: 0.623817\nConverges", 'is_hidden' => false, 'order_index' => 2],
            ['input' => " ", 'expected_output' => "M=10: 0.626512\nM=100: 0.623706\nM=1000: 0.623811\nM=10000: 0.623817\nConverges", 'is_hidden' => true,  'order_index' => 3],
            ['input' => " ", 'expected_output' => "M=10: 0.626512\nM=100: 0.623706\nM=1000: 0.623811\nM=10000: 0.623817\nConverges", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Gamma Function ───────────────────────────────────────────
        $seed(37, [
            ['input' => "2.5", 'expected_output' => "Numerical: 1.329340\nExact: 1.329340", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3.0", 'expected_output' => "Numerical: 2.000000\nExact: 2.000000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.5", 'expected_output' => "Numerical: 0.886227\nExact: 0.886227", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "4.2", 'expected_output' => "Numerical: 7.756689\nExact: 7.756689", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Comparison Test ──────────────────────────────────────────
        $seed(38, [
            ['input' => "1", 'expected_output' => "f(x) = 1/(x^2+1), g(x) = 1/x^2\nf(x) <= g(x) for x >= 1\nintegral of g converges (p-series p=2)\nConclusion: f converges", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2", 'expected_output' => "f(x) = 1/sqrt(x), g(x) = 1/x\nf(x) >= g(x) for x in (0,1]\nintegral of g diverges (p-series p=1)\nConclusion: f diverges", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3", 'expected_output' => "f(x) = e^-x, g(x) = 1/x^2\nf(x) <= g(x) for x >= 1\nintegral of g converges (p-series p=2)\nConclusion: f converges", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1", 'expected_output' => "f(x) = 1/(x^2+1), g(x) = 1/x^2\nf(x) <= g(x) for x >= 1\nintegral of g converges (p-series p=2)\nConclusion: f converges", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Laplace Transform ────────────────────────────────────────
        $seed(39, [
            ['input' => "1\n2.0", 'expected_output' => "Numerical: 0.25\nExact: 0.25",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1.0", 'expected_output' => "Numerical: 0.5\nExact: 0.5",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2.0", 'expected_output' => "Numerical: 1.0\nExact: 1.0",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n3.0", 'expected_output' => "Numerical: 0.111111\nExact: 0.111111", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Double Integral ──────────────────────────────────────────
        $seed(40, [
            ['input' => "0 1 0 2", 'expected_output' => "0.666667", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 2 0 1", 'expected_output' => "1.333333", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 1 2", 'expected_output' => "3.500000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0 3 0 3", 'expected_output' => "40.500000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Polar Coordinates ────────────────────────────────────────
        $seed(41, [
            ['input' => "2", 'expected_output' => "Numerical: 25.132741\nAnalytical: 25.132741", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1", 'expected_output' => "Numerical: 1.570796\nAnalytical: 1.570796",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3", 'expected_output' => "Numerical: 127.234502\nAnalytical: 127.234502", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5",'expected_output' => "Numerical: 0.098175\nAnalytical: 0.098175",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: 2D Jacobian ──────────────────────────────────────────────
        $seed(42, [
            ['input' => "1\n2", 'expected_output' => "2.0 1.0\n2.0 -1.0\ndet: -4.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1", 'expected_output' => "1.0 2.0\n4.0 -1.0\ndet: -9.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0", 'expected_output' => "0.0 0.0\n0.0 -1.0\ndet: 0.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "-1\n3",'expected_output' => "3.0 -1.0\n-2.0 -1.0\ndet: -5.0",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Surface Area ─────────────────────────────────────────────
        $seed(43, [
            ['input' => " ", 'expected_output' => "1.862184", 'is_hidden' => false, 'order_index' => 1],
            ['input' => " ", 'expected_output' => "1.862184", 'is_hidden' => false, 'order_index' => 2],
            ['input' => " ", 'expected_output' => "1.862184", 'is_hidden' => true,  'order_index' => 3],
            ['input' => " ", 'expected_output' => "1.862184", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Centroid ─────────────────────────────────────────────────
        $seed(44, [
            ['input' => " ", 'expected_output' => "Area: 0.166667\nCentroid x: 0.5\nCentroid y: 0.4", 'is_hidden' => false, 'order_index' => 1],
            ['input' => " ", 'expected_output' => "Area: 0.166667\nCentroid x: 0.5\nCentroid y: 0.4", 'is_hidden' => false, 'order_index' => 2],
            ['input' => " ", 'expected_output' => "Area: 0.166667\nCentroid x: 0.5\nCentroid y: 0.4", 'is_hidden' => true,  'order_index' => 3],
            ['input' => " ", 'expected_output' => "Area: 0.166667\nCentroid x: 0.5\nCentroid y: 0.4", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Radius of Convergence ────────────────────────────────────
        $seed(45, [
            ['input' => "1", 'expected_output' => "ratio n=10: 1.1\nratio n=100: 1.01\nR: 1.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2", 'expected_output' => "ratio n=10: 11.0\nratio n=100: 101.0\nR: inf",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3", 'expected_output' => "ratio n=10: 0.090909\nratio n=100: 0.009901\nR: 0.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "4", 'expected_output' => "ratio n=10: 1.21\nratio n=100: 1.0201\nR: 1.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Power Series Evaluation ──────────────────────────────────
        $seed(46, [
            ['input' => "0.5\n15", 'expected_output' => "Sum: 1.64872127\nNext term: 0.00000000\nConverged\nExact: 1.64872127",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n20", 'expected_output' => "Sum: 2.71828183\nNext term: 0.00000000\nConverged\nExact: 2.71828183",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n5",  'expected_output' => "Sum: 7.0\nNext term: 0.26666667\nExact: 7.38905610",                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "-1.0\n18",'expected_output' => "Sum: 0.36787944\nNext term: 0.00000000\nConverged\nExact: 0.36787944",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Fourier Coefficients ─────────────────────────────────────
        $seed(47, [
            ['input' => "3", 'expected_output' => "a0: 6.579736\na1: -4.0, b1: 0.0\na2: 1.0, b2: 0.0\na3: -0.444444, b3: 0.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1", 'expected_output' => "a0: 6.579736\na1: -4.0, b1: 0.0",                                          'is_hidden' => false, 'order_index' => 2],
            ['input' => "2", 'expected_output' => "a0: 6.579736\na1: -4.0, b1: 0.0\na2: 1.0, b2: 0.0",                        'is_hidden' => true,  'order_index' => 3],
            ['input' => "4", 'expected_output' => "a0: 6.579736\na1: -4.0, b1: 0.0\na2: 1.0, b2: 0.0\na3: -0.444444, b3: 0.0\na4: 0.25, b4: 0.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q48: Fourier Approx ───────────────────────────────────────────
        $seed(48, [
            ['input' => "50\n0.1",  'expected_output' => "Fourier approx: 1.117858\nsign(x): 1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n1.5",  'expected_output' => "Fourier approx: 1.066468\nsign(x): 1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n-0.5",'expected_output' => "Fourier approx: -0.993635\nsign(x): -1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n0.0",  'expected_output' => "Fourier approx: 0.0\nsign(x): 0",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Parseval's Theorem ───────────────────────────────────────
        $seed(49, [
            ['input' => "100",  'expected_output' => "LHS: 6.579736\nRHS: 6.448789\nParseval verified: yes", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1000", 'expected_output' => "LHS: 6.579736\nRHS: 6.566585\nParseval verified: yes", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10",   'expected_output' => "LHS: 6.579736\nRHS: 5.312999\nParseval verified: no",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "50",   'expected_output' => "LHS: 6.579736\nRHS: 6.319728\nParseval verified: yes", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Pipeline ─────────────────────────────────────────────────
        $seed(50, [
            ['input' => " ", 'expected_output' => "=== FUNCTION ANALYSIS: f(x) = x^3 - 6x^2 + 11x - 6 ===\nRoots (Newton-Raphson from x=0.5, 2.5, 3.5):\n  x1: 1.0000\n  x2: 2.0000\n  x3: 3.0000\nCritical points (where f'(x)=0, Newton on f''):\n  x_crit1: 1.4226 → local max\n  x_crit2: 2.5774 → local min\nDefinite integral ∫_0^4 f(x) dx (Simpson, n=1000):\n  I: 4.0000\nAverage value on [0,4]:\n  avg: 1.0000\nTaylor approx at x=2 (degree 3):\n  f(x) ≈ 0.0000 + -1.0000(x-2) + 0.0000(x-2)^2 + 1.0000(x-2)^3", 'is_hidden' => false, 'order_index' => 1],
            ['input' => " ", 'expected_output' => "=== FUNCTION ANALYSIS: f(x) = x^3 - 6x^2 + 11x - 6 ===\nRoots (Newton-Raphson from x=0.5, 2.5, 3.5):\n  x1: 1.0000\n  x2: 2.0000\n  x3: 3.0000\nCritical points (where f'(x)=0, Newton on f''):\n  x_crit1: 1.4226 → local max\n  x_crit2: 2.5774 → local min\nDefinite integral ∫_0^4 f(x) dx (Simpson, n=1000):\n  I: 4.0000\nAverage value on [0,4]:\n  avg: 1.0000\nTaylor approx at x=2 (degree 3):\n  f(x) ≈ 0.0000 + -1.0000(x-2) + 0.0000(x-2)^2 + 1.0000(x-2)^3", 'is_hidden' => false, 'order_index' => 2],
            ['input' => " ", 'expected_output' => "=== FUNCTION ANALYSIS: f(x) = x^3 - 6x^2 + 11x - 6 ===\nRoots (Newton-Raphson from x=0.5, 2.5, 3.5):\n  x1: 1.0000\n  x2: 2.0000\n  x3: 3.0000\nCritical points (where f'(x)=0, Newton on f''):\n  x_crit1: 1.4226 → local max\n  x_crit2: 2.5774 → local min\nDefinite integral ∫_0^4 f(x) dx (Simpson, n=1000):\n  I: 4.0000\nAverage value on [0,4]:\n  avg: 1.0000\nTaylor approx at x=2 (degree 3):\n  f(x) ≈ 0.0000 + -1.0000(x-2) + 0.0000(x-2)^2 + 1.0000(x-2)^3", 'is_hidden' => true,  'order_index' => 3],
            ['input' => " ", 'expected_output' => "=== FUNCTION ANALYSIS: f(x) = x^3 - 6x^2 + 11x - 6 ===\nRoots (Newton-Raphson from x=0.5, 2.5, 3.5):\n  x1: 1.0000\n  x2: 2.0000\n  x3: 3.0000\nCritical points (where f'(x)=0, Newton on f''):\n  x_crit1: 1.4226 → local max\n  x_crit2: 2.5774 → local min\nDefinite integral ∫_0^4 f(x) dx (Simpson, n=1000):\n  I: 4.0000\nAverage value on [0,4]:\n  avg: 1.0000\nTaylor approx at x=2 (degree 3):\n  f(x) ≈ 0.0000 + -1.0000(x-2) + 0.0000(x-2)^2 + 1.0000(x-2)^3", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 4 Coding (Advanced) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}