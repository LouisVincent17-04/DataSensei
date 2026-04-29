<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 4 — Real Analysis & Calculus (Professional / Hardest) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Professional tier
 *   2. coding_questions    — 50 questions demanding deep numerical & algorithmic work
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (lessons 305–314):
 *   L4.1  Real Numbers, Absolute Value & the Number Line
 *   L4.2  Sequences & Their Limits
 *   L4.3  Limits of Functions & Continuity
 *   L4.4  The Derivative: Definition & Interpretation
 *   L4.5  Applications of Derivatives: MVT, Extrema & Optimisation
 *   L4.6  L'Hôpital's Rule & Indeterminate Forms
 *   L4.7  The Definite Integral: Definition & Properties
 *   L4.8  The Fundamental Theorem of Calculus
 *   L4.9  Integration Techniques: u-Substitution & Integration by Parts
 *   L4.10 Infinite Series & Convergence Tests
 *
 * Difficulty: Professional — all problems require implementing numerical methods,
 * symbolic reasoning, and multi-step algorithms from scratch using pure Python.
 * Problems simulate what scipy, sympy and numpy compute internally.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module4CodingChallengeSeederProfessional extends Seeder
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

        $this->command->info('Creating Module 4 — Real Analysis & Calculus (Professional) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Real Analysis & Calculus',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Implement numerical methods and algorithms that form the computational backbone of real analysis and calculus. Tasks span sequence convergence, epsilon-delta reasoning, numerical differentiation and integration, root-finding, optimisation, series convergence testing, and symbolic pattern recognition — all from scratch in pure Python.',
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
            // TOPIC 1: Real Numbers & Absolute Value (Q1–Q5)  →  Lesson 305
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Read two real numbers `a` and `b`. Print the **distance** between them on the number line (|a − b|), rounded to 6 decimal places.

Example:
```
Input:
-3.5
7.2
Output: 10.700000
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read `n` real numbers and print the **supremum** (least upper bound) and **infimum** (greatest lower bound) of the set, each rounded to 6 decimal places on separate lines.

Format:
```
sup: <value>
inf: <value>
```

Example:
```
Input:
4
-1.5
3.0
2.7
-0.5
Output:
sup: 3.000000
inf: -1.500000
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Given a real number `x` and an epsilon `ε`, determine whether `x` is within the **open ball** (x − ε, x + ε) of a centre `c`. Read `c`, `x`, and `ε` (one per line). Print `True` if |x − c| < ε, otherwise `False`.

Example:
```
Input:
5.0
5.3
0.5
Output: True
```
MD,
                'starter_code'        => "c = float(input())\nx = float(input())\neps = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read `n` real numbers. Determine whether the set is **bounded** (has both an upper and lower bound that are finite) by checking if all values satisfy |xᵢ| ≤ M for M = 10⁶. Print `Bounded` or `Unbounded`.

Then print the **Archimedean witness** — the smallest integer `N` such that N > |sup|.

Format:
```
Bounded
N: <value>
```
or `Unbounded` (no second line if unbounded).

Example:
```
Input:
3
1.5
-200.0
999999.9
Output:
Bounded
N: 1000000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read `n` real numbers and apply the **triangle inequality check**: verify that for every triple (a, b, c) in the list, |a + b| ≤ |a| + |b|. Print `True` if all triples satisfy it (they always should — this validates your implementation), then print the **maximum slack** max(|a| + |b| − |a + b|) over all ordered pairs (a, b), rounded to 6 decimal places.

Format:
```
True
max_slack: <value>
```

Example:
```
Input:
3
-2.0
3.0
1.5
Output:
True
max_slack: 4.000000
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Sequences & Limits (Q6–Q10)  →  Lesson 306
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Read `n` and compute the first `n` terms of the sequence **aₙ = (1 + 1/n)ⁿ**, printing each term rounded to 6 decimal places, one per line. This sequence converges to e.

Example:
```
Input: 3
Output:
2.000000
2.250000
2.370370
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
A sequence is given by **aₙ = (3n² + 2n − 1) / (n² − 5)**. Read `n` (the number of terms to compute, starting from n=6 to avoid the singularity near √5). Print each term rounded to 6 decimal places, then on the final line print its **limit** `L: 3.000000`.

Example:
```
Input: 3
Output:
3.193548
3.107143
3.075472
L: 3.000000
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below — start at index k=6\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Read a real number `L` (limit candidate) and a tolerance `ε`. Determine the smallest index `N` such that for all `n > N`, |aₙ − L| < ε for the sequence **aₙ = 1/n**.

Print `N: <value>`.

Example:
```
Input:
0.0
0.01
Output: N: 100
```
MD,
                'starter_code'        => "L = float(input())\neps = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read `n` and compute the **Cauchy criterion** for the sequence aₙ = 1/n: for each pair (m, n) with 1 ≤ m < n ≤ N (where N is the input), find the maximum |aₘ − aₙ| over all such pairs. Print it rounded to 6 decimal places.

Example:
```
Input: 4
Output: 0.750000
```
MD,
                'starter_code'        => "N = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read `n` terms of a sequence (one float per line after `n`). Determine whether the sequence is **monotonically increasing**, **monotonically decreasing**, or **neither**. Print the result, then print whether it appears **convergent** (True/False) based on the Monotone Convergence Theorem: a bounded monotone sequence is convergent.

Format:
```
increasing
convergent: True
```

Example:
```
Input:
4
1.0
0.5
0.333
0.25
Output:
decreasing
convergent: True
```
MD,
                'starter_code'        => "n = int(input())\nseq = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Limits of Functions & Continuity (Q11–Q15) → L307
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Estimate the **limit** of f(x) = (x² − 4) / (x − 2) as x → 2 numerically. Read a tolerance `ε` and a step sequence exponent `k` (use step h = 10⁻ᵏ). Evaluate f(2 + h) and f(2 − h) and print both rounded to 6 decimal places, then print the estimated limit rounded to 6 decimal places.

Format:
```
f(2+h): <value>
f(2-h): <value>
limit: <value>
```

Example:
```
Input:
4
Output:
f(2+h): 4.000010
f(2-h): 3.999990
limit: 4.000000
```
MD,
                'starter_code'        => "k = int(input())\nh = 10 ** (-k)\n# f(x) = (x**2 - 4) / (x - 2)\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Read `n` points (format: `x y` per line) representing a function. Check whether the function is **continuous** at each interior point using a discrete epsilon-delta check: for a given `ε` (last line), a point xᵢ is continuous if for all neighbors |f(xⱼ) − f(xᵢ)| < ε whenever |xⱼ − xᵢ| < δ = 0.5. Print `continuous` or `discontinuous` for each interior point, one per line.

Example:
```
Input:
5
1.0 1.0
2.0 2.0
3.0 10.0
4.0 4.0
5.0 5.0
2.0
Output:
continuous
discontinuous
continuous
```
MD,
                'starter_code'        => "n = int(input())\npoints = [tuple(map(float, input().split())) for _ in range(n)]\neps = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Implement **bisection root-finding** for f(x) = x³ − x − 2 on [a, b]. Read `a`, `b`, and tolerance `tol` (one per line). Print the root found rounded to 6 decimal places and the number of iterations taken.

Format:
```
root: <value>
iterations: <n>
```

Example:
```
Input:
1.0
2.0
0.000001
Output:
root: 1.521380
iterations: 20
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\ntol = float(input())\n# f(x) = x**3 - x - 2\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Apply the **Intermediate Value Theorem**: read `n` points of a function (format: `x y` per line) and a target value `c` (last line). Print all intervals [xᵢ, xᵢ₊₁] where the IVT guarantees a root of f(x) = c exists (i.e. where the function crosses c). Format each interval as `[<x1>, <x2>]`.

If no such interval exists, print `No crossing found`.

Example:
```
Input:
4
0.0 -1.0
1.0 0.5
2.0 2.0
3.0 -0.5
0.0
Output:
[0.0, 1.0]
[2.0, 3.0]
```
MD,
                'starter_code'        => "n = int(input())\npoints = [tuple(map(float, input().split())) for _ in range(n)]\nc = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Implement **Newton's method** for finding a root of f(x) = x² − S (i.e. computing √S). Read `S` and initial guess `x0` and tolerance `tol` (one per line). Use f'(x) = 2x. Print the root rounded to 8 decimal places and the number of iterations.

Format:
```
root: <value>
iterations: <n>
```

Example:
```
Input:
2.0
1.0
0.00000001
Output:
root: 1.41421356
iterations: 5
```
MD,
                'starter_code'        => "S = float(input())\nx0 = float(input())\ntol = float(input())\n# f(x) = x**2 - S,  f'(x) = 2*x\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: The Derivative (Q16–Q20)  →  Lesson 308
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Estimate the **derivative** of f(x) = sin(x) at a given point `x₀` using the **central difference formula**:

f'(x₀) ≈ (f(x₀ + h) − f(x₀ − h)) / (2h)

Read `x₀` and `h` (one per line). Print the numerical derivative and the exact value (cos(x₀)) rounded to 6 decimal places on separate lines.

Format:
```
numerical: <value>
exact: <value>
```

Example:
```
Input:
0.0
0.001
Output:
numerical: 1.000000
exact: 1.000000
```
MD,
                'starter_code'        => "import math\nx0 = float(input())\nh = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Read `n` (x, y) data points representing a function sampled at equal spacing `h`. Compute the **numerical derivative** at each interior point using the central difference formula and print each value rounded to 6 decimal places, one per line.

The spacing `h` is given on the line before the points.

Example:
```
Input:
0.1
5
0.0 0.0
0.1 0.0998
0.2 0.1987
0.3 0.2955
0.4 0.3894
Output:
0.993000
0.994500
0.984000
```
MD,
                'starter_code'        => "h = float(input())\nn = int(input())\npoints = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below — compute derivative at interior points only\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Implement **automatic differentiation (forward mode)** for a polynomial. Read the **degree** `d`, then `d+1` coefficients (a₀, a₁, …, aₐ) for the polynomial p(x) = a₀ + a₁x + … + aₐxᵈ, then a point `x₀`. Print p(x₀) and p'(x₀) rounded to 6 decimal places.

Format:
```
p(x0): <value>
p'(x0): <value>
```

Example:
```
Input:
3
1
0
-1
2
2.0
Output:
p(x0): 11.000000
p'(x0): 23.000000
```
MD,
                'starter_code'        => "d = int(input())\ncoeffs = [float(input()) for _ in range(d + 1)]\nx0 = float(input())\n# p(x) = coeffs[0] + coeffs[1]*x + ... + coeffs[d]*x^d\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Read a polynomial (degree `d`, then `d+1` coefficients) and a point `x₀`. Find the **equation of the tangent line** at (x₀, p(x₀)): y = mx + b. Print `m` and `b` rounded to 6 decimal places.

Format:
```
m: <value>
b: <value>
```

Example:
```
Input:
2
1
0
1
3.0
Output:
m: 6.000000
b: -9.000000
```
MD,
                'starter_code'        => "d = int(input())\ncoeffs = [float(input()) for _ in range(d + 1)]\nx0 = float(input())\n# p(x) = coeffs[0] + coeffs[1]*x + ... + coeffs[d]*x^d\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Read `n` sampled (x, f(x)) pairs at equal spacing. Compute the **second derivative** at each interior-interior point (i.e. excluding the two boundary points) using:

f''(xᵢ) ≈ (f(xᵢ₋₁) − 2f(xᵢ) + f(xᵢ₊₁)) / h²

Spacing `h` is given first. Print each second derivative rounded to 6 decimal places.

Example:
```
Input:
1.0
5
0.0 0.0
1.0 1.0
2.0 4.0
3.0 9.0
4.0 16.0
Output:
2.000000
2.000000
2.000000
```
MD,
                'starter_code'        => "h = float(input())\nn = int(input())\npoints = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Applications of Derivatives (Q21–Q25)  →  Lesson 309
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Verify the **Mean Value Theorem** numerically. Read `n` (x, f(x)) pairs at equal spacing `h`. Find all interior points where f'(xᵢ) ≈ (f(b) − f(a)) / (b − a) within tolerance 0.01. Print their x-values rounded to 4 decimal places, one per line. If none found, print `None`.

Spacing `h` is given first.

Example:
```
Input:
1.0
4
0.0 0.0
1.0 1.0
2.0 4.0
3.0 9.0
Output:
1.5000
```
MD,
                'starter_code'        => "h = float(input())\nn = int(input())\npoints = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Read a polynomial (degree `d`, then `d+1` coefficients). Find all **critical points** in the interval [a, b] (given on the last two lines) by computing p'(x) = 0 numerically using a grid of 1000 points. A critical point exists between xᵢ and xᵢ₊₁ where p'(xᵢ) and p'(xᵢ₊₁) have opposite signs; refine it using bisection to tolerance 1e-6. For each, classify as `local min`, `local max`, or `saddle` using the second derivative test. Print each root rounded to 4 decimal places followed by its classification.

Format: `x=<value>: <classification>`

Example:
```
Input:
3
-1
0
3
0
-2.0
2.0
Output:
x=-1.0000: local min
x=1.0000: local max
```
MD,
                'starter_code'        => "d = int(input())\ncoeffs = [float(input()) for _ in range(d + 1)]\na = float(input())\nb = float(input())\n# p(x) = coeffs[0] + coeffs[1]*x + ... + coeffs[d]*x^d\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
**Optimisation problem**: A rectangle is inscribed in a semicircle of radius `r` (given as input). Its width is 2x and its height is √(r² − x²). Find the `x` that **maximises the area** A(x) = 2x√(r² − x²) on (0, r) using **golden section search** to tolerance 1e-6. Print the optimal `x` and maximum area, both rounded to 6 decimal places.

Format:
```
x: <value>
area: <value>
```

Example:
```
Input: 5.0
Output:
x: 3.535534
area: 25.000000
```
MD,
                'starter_code'        => "import math\nr = float(input())\n# A(x) = 2*x*math.sqrt(r**2 - x**2), maximise on (0, r)\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Read `n` (x, y) data points. Fit a **linear regression** line y = mx + b using the closed-form least-squares formulas. Then use the derivative of the MSE with respect to `m` and `b` to verify the solution (gradient should be near 0). Print `m`, `b`, and the gradient norms (∂MSE/∂m and ∂MSE/∂b) all rounded to 6 decimal places.

Format:
```
m: <value>
b: <value>
grad_m: <value>
grad_b: <value>
```

Example:
```
Input:
3
1.0 2.0
2.0 4.0
3.0 6.0
Output:
m: 2.000000
b: 0.000000
grad_m: 0.000000
grad_b: 0.000000
```
MD,
                'starter_code'        => "n = int(input())\npoints = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Implement **gradient descent** to minimise f(x) = x⁴ − 4x² + x. Read the **learning rate** `α`, initial guess `x₀`, and max iterations `T` (one per line). Stop when |f'(x)| < 1e-6 or after `T` iterations. Print the final `x`, f(x), and number of iterations taken, all rounded to 6 decimal places.

Format:
```
x: <value>
f(x): <value>
iterations: <n>
```

f'(x) = 4x³ − 8x + 1.

Example:
```
Input:
0.01
-2.0
1000
Output:
x: -1.351230
f(x): -3.506252
iterations: 285
```
MD,
                'starter_code'        => "alpha = float(input())\nx = float(input())\nT = int(input())\n# f(x) = x**4 - 4*x**2 + x,  f'(x) = 4*x**3 - 8*x + 1\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: L'Hôpital's Rule (Q26–Q29)  →  Lesson 310
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Numerically estimate the limit **lim_{x→0} sin(x)/x** using a sequence of values x = 10⁻¹, 10⁻², …, 10⁻ⁿ (n given as input). Print each f(x) = sin(x)/x rounded to 8 decimal places. Then print the limit `L: 1.00000000`.

Example:
```
Input: 4
Output:
0.99833417
0.99998333
0.99999983
1.00000000
L: 1.00000000
```
MD,
                'starter_code'        => "import math\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Estimate **lim_{x→0} (eˣ − 1)/x** and **lim_{x→0} (eˣ − 1 − x)/x²** numerically for x = 10⁻ᵏ (k given). Both limits equal 1 and 0.5 respectively. Print both values rounded to 8 decimal places and their known exact limits.

Format:
```
L1_numerical: <value>
L1_exact: 1.00000000
L2_numerical: <value>
L2_exact: 0.50000000
```

Example:
```
Input: 5
Output:
L1_numerical: 1.00000000
L1_exact: 1.00000000
L2_numerical: 0.50000000
L2_exact: 0.50000000
```
MD,
                'starter_code'        => "import math\nk = int(input())\nx = 10 ** (-k)\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Estimate the limit of an indeterminate form **lim_{x→a} f(x)/g(x)** using L'Hôpital's rule numerically. Read `a`, and two polynomial definitions:
- numerator: degree `d1`, then `d1+1` coefficients
- denominator: degree `d2`, then `d2+1` coefficients

Both polynomials evaluate to 0 at x = a (0/0 form). Apply the numerical derivative of each and compute f'(a)/g'(a). Print rounded to 6 decimal places.

Example:
```
Input:
2.0
2
-4 0 1
1
-2 1
Output: 4.000000
```
MD,
                'starter_code'        => "a = float(input())\nd1 = int(input())\nc1 = list(map(float, input().split()))\nd2 = int(input())\nc2 = list(map(float, input().split()))\n# polynomials: p(x) = c[0] + c[1]*x + ... + c[d]*x^d\n# Write your solution below using numerical derivatives\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Estimate the **limit** of the indeterminate form xˣ as x → 0⁺ (i.e. lim_{x→0⁺} xˣ = 1). Read `k`: evaluate xˣ at x = 10⁻¹, 10⁻², …, 10⁻ᵏ. Print each value rounded to 8 decimal places, then print `L: 1.00000000`.

Example:
```
Input: 3
Output:
0.79432823
0.95499259
0.98929538
L: 1.00000000
```
MD,
                'starter_code'        => "import math\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: The Definite Integral (Q30–Q34)  →  Lesson 311
            // ═══════════════════════════════════════════════════════════════

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Approximate the definite integral of f(x) = x² from `a` to `b` using the **Left Riemann Sum**, **Right Riemann Sum**, and **Midpoint Rule** with `n` subintervals. Read `a`, `b`, `n` (one per line). Print each approximation and the exact value ((b³ − a³)/3), all rounded to 6 decimal places.

Format:
```
left: <value>
right: <value>
midpoint: <value>
exact: <value>
```

Example:
```
Input:
0.0
1.0
100
Output:
left: 0.328350
right: 0.338350
midpoint: 0.333325
exact: 0.333333
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\nn = int(input())\n# f(x) = x**2\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Approximate ∫ from a to b of f(x) using the **Trapezoidal Rule** and **Simpson's Rule** with `n` subintervals (n must be even for Simpson's). Read `a`, `b`, `n`, and a polynomial (degree `d`, then coefficients). Print both approximations rounded to 8 decimal places.

Format:
```
trapezoidal: <value>
simpsons: <value>
```

Example:
```
Input:
0.0
1.0
4
3
0 0 0 1
Output:
trapezoidal: 0.28125000
simpsons: 0.25000000
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\nn = int(input())\nd = int(input())\ncoeffs = list(map(float, input().split()))\n# p(x) = coeffs[0] + coeffs[1]*x + ... + coeffs[d]*x^d\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Read a set of (x, f(x)) data points (format: `x y` per line, `n` first) at equal spacing. Compute the **definite integral** using the Trapezoidal Rule over all points. Print the result rounded to 6 decimal places.

Example:
```
Input:
5
0.0 1.0
0.25 0.9394
0.5 0.7788
0.75 0.5698
1.0 0.3679
Output: 0.731813
```
MD,
                'starter_code'        => "n = int(input())\npoints = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Compute the **area between two curves** f(x) = x² and g(x) = x on the interval [0, 1] using Simpson's rule with `n` subintervals. Read `n`. Print the area (∫|f(x)−g(x)| dx) rounded to 8 decimal places. The exact answer is 1/6.

Example:
```
Input: 100
Output: 0.16666667
```
MD,
                'starter_code'        => "n = int(input())\n# f(x) = x**2, g(x) = x, interval [0, 1]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Estimate **π** using Monte Carlo integration: ∫₀¹ 4/(1 + x²) dx = π. Read a seed integer `s` and `n` (number of samples). Use a simple **linear congruential generator** (LCG) with a = 1664525, c = 1013904223, m = 2³², seed = s to generate uniform samples in [0,1]. Print the estimate of π rounded to 6 decimal places.

Example:
```
Input:
42
1000000
Output: 3.141668
```
MD,
                'starter_code'        => "s = int(input())\nn = int(input())\n# LCG: a=1664525, c=1013904223, m=2**32\n# f(x) = 4 / (1 + x**2)\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Fundamental Theorem of Calculus (Q35–Q38) → L312
            // ═══════════════════════════════════════════════════════════════

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Numerically verify the **First Fundamental Theorem of Calculus**: F(x) = ∫ₐˣ f(t) dt, so F'(x) ≈ f(x). Use f(t) = t² and a = 0. Read a query point `x₀` and step `h`. Compute F(x₀) by Simpson's Rule with 1000 subintervals, and estimate F'(x₀) using central difference. Print F(x₀), F'(x₀) (numerical), and the exact f(x₀), all rounded to 6 decimal places.

Format:
```
F(x0): <value>
F'(x0)_numerical: <value>
f(x0)_exact: <value>
```

Example:
```
Input:
3.0
0.001
Output:
F(x0): 9.000000
F'(x0)_numerical: 9.000000
f(x0)_exact: 9.000000
```
MD,
                'starter_code'        => "x0 = float(input())\nh = float(input())\n# f(t) = t**2, a = 0\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Use the **Second Fundamental Theorem of Calculus** to evaluate ∫ₐᵇ f(x) dx = F(b) − F(a) for a polynomial f. Read degree `d`, then `d+1` coefficients, then `a` and `b`. Compute the antiderivative F(x) symbolically (raise each coefficient's power by 1, divide by new power). Print the exact result rounded to 8 decimal places.

Example:
```
Input:
2
0
0
1
0.0
3.0
Output: 9.00000000
```
MD,
                'starter_code'        => "d = int(input())\ncoeffs = [float(input()) for _ in range(d + 1)]\na = float(input())\nb = float(input())\n# p(x) = coeffs[0] + coeffs[1]*x + ... + coeffs[d]*x^d\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Compute the **accumulation function** A(xᵢ) = ∫₀^{xᵢ} f(t) dt for f(t) = sin(t) at each of `n` query points (given one per line after `n`). Use Simpson's Rule with 1000 subintervals per query. Print each value rounded to 6 decimal places, one per line. The exact antiderivative is −cos(x) + 1.

Example:
```
Input:
3
0.0
1.5707963
3.1415927
Output:
0.000000
1.000000
2.000000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nqueries = [float(input()) for _ in range(n)]\n# f(t) = math.sin(t), integrate from 0 to each query\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Compute the **arc length** of f(x) = x^(3/2) on [0, b] using L = ∫₀ᵇ √(1 + (f'(x))²) dx, approximated via Simpson's Rule with 1000 subintervals. f'(x) = (3/2)√x. Read `b`. Print the arc length rounded to 6 decimal places.

Example:
```
Input: 4.0
Output: 9.073401
```
MD,
                'starter_code'        => "import math\nb = float(input())\n# f(x) = x**(3/2),  f'(x) = 1.5*math.sqrt(x)\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Integration Techniques (Q39–Q44)  →  Lesson 313
            // ═══════════════════════════════════════════════════════════════

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Apply **u-substitution** numerically: evaluate ∫ₐᵇ 2x·e^(x²) dx by making the substitution u = x², du = 2x dx, transforming the integral to ∫_{a²}^{b²} eᵘ du. Read `a` and `b`. Print the result of both the original integral (via Simpson's Rule) and the transformed integral, both rounded to 8 decimal places. They should match.

Format:
```
original: <value>
transformed: <value>
```

Example:
```
Input:
0.0
1.0
Output:
original: 1.71828183
transformed: 1.71828183
```
MD,
                'starter_code'        => "import math\na = float(input())\nb = float(input())\n# f(x) = 2*x*math.exp(x**2), a to b\n# transformed: g(u) = math.exp(u), a**2 to b**2\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Evaluate ∫ₐᵇ x·eˣ dx using **integration by parts** symbolically: ∫x·eˣ dx = xeˣ − eˣ + C. Read `a` and `b`. Print the exact result rounded to 8 decimal places.

Also verify numerically via Simpson's Rule with 1000 subintervals and print the numerical result.

Format:
```
exact: <value>
numerical: <value>
```

Example:
```
Input:
0.0
1.0
Output:
exact: 1.00000000
numerical: 1.00000000
```
MD,
                'starter_code'        => "import math\na = float(input())\nb = float(input())\n# exact antiderivative: F(x) = x*exp(x) - exp(x)\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Implement **Gaussian quadrature** (2-point) to approximate ∫_{-1}^{1} f(t) dt for a polynomial f, then transform to [a, b] using the change of variable. Read `a`, `b`, degree `d`, and `d+1` coefficients. The 2-point Gauss nodes are ±1/√3 with weight 1 each. Print the Gaussian approximation and the exact antiderivative result, both rounded to 8 decimal places.

Format:
```
gauss: <value>
exact: <value>
```

Example:
```
Input:
0.0
2.0
2
0
0
1
Output:
gauss: 2.66666667
exact: 2.66666667
```
MD,
                'starter_code'        => "import math\na = float(input())\nb = float(input())\nd = int(input())\ncoeffs = [float(input()) for _ in range(d + 1)]\n# p(x) = coeffs[0] + coeffs[1]*x + ... + coeffs[d]*x^d\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Estimate the **improper integral** ∫₁^∞ 1/x² dx by truncating at large `M` and computing via Simpson's Rule with 1000 subintervals on [1, M]. Read `M`. The exact value is 1. Print the numerical result and the absolute error from the exact value, both rounded to 8 decimal places.

Format:
```
numerical: <value>
error: <value>
```

Example:
```
Input: 10000.0
Output:
numerical: 0.99990000
error: 0.00010000
```
MD,
                'starter_code'        => "M = float(input())\n# f(x) = 1/x**2, integrate from 1 to M\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Compute the **volume of a solid of revolution** formed by rotating f(x) = √x around the x-axis from 0 to `b`, using the disk method: V = π ∫₀ᵇ [f(x)]² dx = π ∫₀ᵇ x dx = π·b²/2. Read `b`. Print the exact value and the Simpson's Rule approximation (with 1000 subintervals), both rounded to 6 decimal places.

Format:
```
exact: <value>
numerical: <value>
```

Example:
```
Input: 3.0
Output:
exact: 14.137167
numerical: 14.137167
```
MD,
                'starter_code'        => "import math\nb = float(input())\n# f(x) = math.sqrt(x), V = pi * integral of x from 0 to b\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Compute the **convolution** of two step functions (piecewise constant) defined on a grid. Read `n` values for f and `n` values for g (one line each, space-separated). Compute the discrete convolution h[k] = Σᵢ f[i]·g[k−i] and print the result as space-separated integers.

Example:
```
Input:
1 2 3
4 5 6
Output: 4 13 28 27 18
```
MD,
                'starter_code'        => "f = list(map(float, input().split()))\ng = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Infinite Series & Convergence (Q45–Q50) → L314
            // ═══════════════════════════════════════════════════════════════

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Approximate **eˣ** using its Taylor series: eˣ = Σₙ₌₀^∞ xⁿ/n!. Read `x` and `N` (number of terms). Print the partial sum rounded to 8 decimal places, then print math.exp(x) rounded to 8 decimal places for comparison.

Format:
```
series: <value>
exact: <value>
```

Example:
```
Input:
1.0
10
Output:
series: 2.71828153
exact: 2.71828183
```
MD,
                'starter_code'        => "import math\nx = float(input())\nN = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Apply the **ratio test** to determine convergence of a series Σ aₙ. Read `n` terms of the sequence a₁, a₂, …, aₙ (one per line after `n`). For each consecutive pair compute |aₙ₊₁/aₙ|. Print each ratio rounded to 6 decimal places, then print the conclusion:
- `converges` if the ratios approach a limit L < 1
- `diverges` if L > 1
- `inconclusive` otherwise

Use the last ratio as the estimate of L.

Example:
```
Input:
5
1.0
0.5
0.25
0.125
0.0625
Output:
0.500000
0.500000
0.500000
0.500000
converges
```
MD,
                'starter_code'        => "n = int(input())\nterms = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Apply the **integral test** to the p-series Σ 1/nᵖ. Read `p` and `M` (upper limit for the improper integral). Compute ∫₁ᴹ 1/xᵖ dx using Simpson's Rule with 1000 subintervals. Print the integral value rounded to 6 decimal places, then print `converges` if p > 1, else `diverges`.

Format:
```
integral: <value>
converges
```

Example:
```
Input:
2.0
10000.0
Output:
integral: 0.999900
converges
```
MD,
                'starter_code'        => "p = float(input())\nM = float(input())\n# f(x) = 1 / x**p\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Compute partial sums of the **geometric series** Σₙ₌₀^N arⁿ and check convergence. Read `a`, `r`, and `N`. Print the Nth partial sum and the exact sum formula a/(1−r) if |r| < 1, else print `diverges`. Both rounded to 8 decimal places.

Format:
```
partial_sum: <value>
exact: <value>
```
or if |r| >= 1:
```
partial_sum: <value>
exact: diverges
```

Example:
```
Input:
1.0
0.5
10
Output:
partial_sum: 1.99804688
exact: 2.00000000
```
MD,
                'starter_code'        => "a = float(input())\nr = float(input())\nN = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Apply the **alternating series test** (Leibniz criterion): a series Σ (−1)ⁿ bₙ converges if bₙ is eventually decreasing and approaches 0. Read `n` positive terms b₁, b₂, …, bₙ (one per line after `n`). Check: (1) all terms are positive, (2) terms are non-increasing, (3) the last term < 0.01 (proxy for → 0). Print `converges` if all conditions hold, else `inconclusive`.

Also print the **alternating partial sum** Σₖ₌₁ⁿ (−1)^(k+1) bₖ rounded to 6 decimal places.

Format:
```
converges
partial_sum: <value>
```

Example:
```
Input:
5
1.0
0.5
0.333
0.25
0.2
Output:
inconclusive
partial_sum: 0.583000
```
MD,
                'starter_code'        => "n = int(input())\nterms = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Compute the **radius of convergence** of a power series Σ cₙxⁿ using the **Cauchy-Hadamard formula**: R = 1 / limsup |cₙ|^(1/n). Read `n` coefficients c₀, c₁, …, cₙ₋₁ (one per line after `n`). Compute |cₖ|^(1/k) for k ≥ 1, take the maximum as the limsup estimate, and print R rounded to 6 decimal places. If all coefficients are 0 except c₀, print `R: inf`.

Format: `R: <value>`

Example:
```
Input:
5
1.0
1.0
1.0
1.0
1.0
Output: R: 1.000000
```
MD,
                'starter_code'        => "n = int(input())\ncoeffs = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
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

        // ── Q1: Distance on number line ───────────────────────────────────
        $seed(1, [
            ['input' => "-3.5\n7.2",       'expected_output' => '10.700000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n0.0",        'expected_output' => '0.000000',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "-5.5\n-1.5",      'expected_output' => '4.000000',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "100.0\n-100.0",   'expected_output' => '200.000000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Supremum & Infimum ────────────────────────────────────────
        $seed(2, [
            ['input' => "4\n-1.5\n3.0\n2.7\n-0.5",    'expected_output' => "sup: 3.000000\ninf: -1.500000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.0\n1.0\n2.0",            'expected_output' => "sup: 2.000000\ninf: 0.000000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n7.5",                       'expected_output' => "sup: 7.500000\ninf: 7.500000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n-10.0\n-5.0\n0.0\n5.0\n10.0", 'expected_output' => "sup: 10.000000\ninf: -10.000000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q3: Open ball membership ──────────────────────────────────────
        $seed(3, [
            ['input' => "5.0\n5.3\n0.5",   'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0\n0.5",   'expected_output' => 'False',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0\n3.0\n0.001", 'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "-1.0\n0.0\n0.9",  'expected_output' => 'False',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Bounded + Archimedean witness ────────────────────────────
        $seed(4, [
            ['input' => "3\n1.5\n-200.0\n999999.9",    'expected_output' => "Bounded\nN: 1000000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0\n500000.0",            'expected_output' => "Bounded\nN: 500001",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1.0\n2.0\n3.0",            'expected_output' => "Bounded\nN: 4",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0.0001",                   'expected_output' => "Bounded\nN: 1",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Triangle inequality slack ────────────────────────────────
        $seed(5, [
            ['input' => "3\n-2.0\n3.0\n1.5",   'expected_output' => "True\nmax_slack: 4.000000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0\n0.0",          'expected_output' => "True\nmax_slack: 0.000000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1.0\n1.0\n1.0",    'expected_output' => "True\nmax_slack: 0.000000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n-5.0\n5.0\n3.0",   'expected_output' => "True\nmax_slack: 10.000000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: (1+1/n)^n sequence ───────────────────────────────────────
        $seed(6, [
            ['input' => '3',  'expected_output' => "2.000000\n2.250000\n2.370370",                                   'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',  'expected_output' => "2.000000",                                                        'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',  'expected_output' => "2.000000\n2.250000\n2.370370\n2.441406\n2.488320",               'is_hidden' => true,  'order_index' => 3],
            ['input' => '2',  'expected_output' => "2.000000\n2.250000",                                             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Rational sequence with limit ─────────────────────────────
        $seed(7, [
            ['input' => '3',  'expected_output' => "3.193548\n3.107143\n3.075472\nL: 3.000000",          'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',  'expected_output' => "3.193548\nL: 3.000000",                              'is_hidden' => false, 'order_index' => 2],
            ['input' => '2',  'expected_output' => "3.193548\n3.107143\nL: 3.000000",                    'is_hidden' => true,  'order_index' => 3],
            ['input' => '4',  'expected_output' => "3.193548\n3.107143\n3.075472\n3.056962\nL: 3.000000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Epsilon-N for 1/n ─────────────────────────────────────────
        $seed(8, [
            ['input' => "0.0\n0.01",    'expected_output' => 'N: 100',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n0.001",   'expected_output' => 'N: 1000',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.1",     'expected_output' => 'N: 10',     'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n0.0001",  'expected_output' => 'N: 10000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Cauchy criterion for 1/n ──────────────────────────────────
        $seed(9, [
            ['input' => '4',   'expected_output' => '0.750000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',   'expected_output' => '0.500000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',   'expected_output' => '0.800000',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '10',  'expected_output' => '0.900000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Monotone & MCT ───────────────────────────────────────────
        $seed(10, [
            ['input' => "4\n1.0\n0.5\n0.333\n0.25",            'expected_output' => "decreasing\nconvergent: True",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1.0\n2.0\n3.0",                    'expected_output' => "increasing\nconvergent: False",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1.0\n0.5\n0.75\n0.25",             'expected_output' => "neither\nconvergent: False",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0.1\n0.01\n0.001",                 'expected_output' => "decreasing\nconvergent: True",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Limit of (x²-4)/(x-2) ───────────────────────────────────
        $seed(11, [
            ['input' => '4',   'expected_output' => "f(2+h): 4.000010\nf(2-h): 3.999990\nlimit: 4.000000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '6',   'expected_output' => "f(2+h): 4.000000\nf(2-h): 4.000000\nlimit: 4.000000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => '3',   'expected_output' => "f(2+h): 4.001000\nf(2-h): 3.999000\nlimit: 4.000000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => '5',   'expected_output' => "f(2+h): 4.000100\nf(2-h): 3.999900\nlimit: 4.000000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Discrete continuity check ───────────────────────────────
        $seed(12, [
            ['input' => "5\n1.0 1.0\n2.0 2.0\n3.0 10.0\n4.0 4.0\n5.0 5.0\n2.0",  'expected_output' => "continuous\ndiscontinuous\ncontinuous",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1.0 1.0\n2.0 2.0\n3.0 3.0\n4.0 4.0\n1.5",            'expected_output' => "continuous\ncontinuous",                      'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0.0 0.0\n1.0 0.0\n2.0 5.0\n3.0 0.0\n4.0 0.0\n1.0",  'expected_output' => "discontinuous\ndiscontinuous\ndiscontinuous",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0.0 1.0\n1.0 1.0\n2.0 1.0\n0.5",                     'expected_output' => "continuous",                                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Bisection for x³-x-2 ────────────────────────────────────
        $seed(13, [
            ['input' => "1.0\n2.0\n0.000001",    'expected_output' => "root: 1.521380\niterations: 20",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n2.0\n0.001",       'expected_output' => "root: 1.521484\niterations: 10",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n2.0\n0.0001",      'expected_output' => "root: 1.521362\niterations: 14",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n2.0\n0.00001",     'expected_output' => "root: 1.521378\niterations: 17",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: IVT crossing intervals ───────────────────────────────────
        $seed(14, [
            ['input' => "4\n0.0 -1.0\n1.0 0.5\n2.0 2.0\n3.0 -0.5\n0.0",    'expected_output' => "[0.0, 1.0]\n[2.0, 3.0]",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.0 1.0\n1.0 2.0\n2.0 3.0\n0.0",               'expected_output' => 'No crossing found',         'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.0 -1.0\n1.0 0.0\n2.0 1.0\n0.0",              'expected_output' => "[0.0, 1.0]",                'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0.0 3.0\n1.0 1.0\n2.0 -1.0\n3.0 2.0\n0.0",    'expected_output' => "[1.0, 2.0]\n[2.0, 3.0]",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Newton's method for sqrt ─────────────────────────────────
        $seed(15, [
            ['input' => "2.0\n1.0\n0.00000001",    'expected_output' => "root: 1.41421356\niterations: 5",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "9.0\n1.0\n0.00000001",    'expected_output' => "root: 3.00000000\niterations: 7",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4.0\n1.0\n0.00000001",    'expected_output' => "root: 2.00000000\niterations: 5",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "25.0\n1.0\n0.00000001",   'expected_output' => "root: 5.00000000\niterations: 8",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Numerical derivative of sin ──────────────────────────────
        $seed(16, [
            ['input' => "0.0\n0.001",                    'expected_output' => "numerical: 1.000000\nexact: 1.000000",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.5707963\n0.001",              'expected_output' => "numerical: 0.000000\nexact: 0.000000",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.7853982\n0.001",              'expected_output' => "numerical: 0.707107\nexact: 0.707107",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3.1415927\n0.001",              'expected_output' => "numerical: -1.000000\nexact: -1.000000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Numerical derivative from sampled data ───────────────────
        $seed(17, [
            ['input' => "0.1\n5\n0.0 0.0\n0.1 0.0998\n0.2 0.1987\n0.3 0.2955\n0.4 0.3894",   'expected_output' => "0.993000\n0.994500\n0.984000",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n5\n0.0 0.0\n1.0 1.0\n2.0 4.0\n3.0 9.0\n4.0 16.0",             'expected_output' => "2.000000\n4.000000\n6.000000",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n4\n0.0 1.0\n0.5 1.0\n1.0 1.0\n1.5 1.0",                       'expected_output' => "0.000000\n0.000000",                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n4\n0.0 0.0\n1.0 1.0\n2.0 8.0\n3.0 27.0",                      'expected_output' => "4.000000\n13.000000",                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Polynomial eval & derivative ────────────────────────────
        $seed(18, [
            ['input' => "3\n1\n0\n-1\n2\n2.0",    'expected_output' => "p(x0): 11.000000\np'(x0): 23.000000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0\n1\n3.0",           'expected_output' => "p(x0): 3.000000\np'(x0): 1.000000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n0\n1\n2.0",        'expected_output' => "p(x0): 5.000000\np'(x0): 4.000000",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0\n0\n0\n1\n2.0",     'expected_output' => "p(x0): 8.000000\np'(x0): 12.000000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Tangent line ─────────────────────────────────────────────
        $seed(19, [
            ['input' => "2\n1\n0\n1\n3.0",    'expected_output' => "m: 6.000000\nb: -9.000000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0\n1\n2.0",       'expected_output' => "m: 1.000000\nb: 0.000000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n-2\n1\n1.0",   'expected_output' => "m: 0.000000\nb: 0.000000",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0\n0\n0\n1\n2.0", 'expected_output' => "m: 12.000000\nb: -16.000000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Second derivative ────────────────────────────────────────
        $seed(20, [
            ['input' => "1.0\n5\n0.0 0.0\n1.0 1.0\n2.0 4.0\n3.0 9.0\n4.0 16.0",     'expected_output' => "2.000000\n2.000000\n2.000000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n5\n0.0 0.0\n1.0 0.0\n2.0 0.0\n3.0 0.0\n4.0 0.0",      'expected_output' => "0.000000\n0.000000\n0.000000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n5\n0.0 0.0\n1.0 1.0\n2.0 8.0\n3.0 27.0\n4.0 64.0",    'expected_output' => "6.000000\n12.000000\n18.000000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n4\n0.0 0.0\n0.5 0.25\n1.0 1.0\n1.5 2.25",             'expected_output' => "2.000000\n2.000000",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: MVT verification ─────────────────────────────────────────
        $seed(21, [
            ['input' => "1.0\n4\n0.0 0.0\n1.0 1.0\n2.0 4.0\n3.0 9.0",     'expected_output' => '1.5000',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n3\n0.0 0.0\n1.0 1.0\n2.0 2.0",              'expected_output' => '1.0000',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n3\n0.0 0.0\n1.0 0.0\n2.0 0.0",              'expected_output' => "1.0000\n2.0000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n4\n0.0 0.0\n1.0 1.0\n2.0 2.0\n3.0 3.0",    'expected_output' => "1.0000\n2.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Critical points of polynomial ───────────────────────────
        $seed(22, [
            ['input' => "3\n-1\n0\n3\n0\n-2.0\n2.0",         'expected_output' => "x=-1.0000: local min\nx=1.0000: local max",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0\n-2\n1\n-2.0\n2.0",            'expected_output' => "x=1.0000: local min",                          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0\n-3\n0\n1\n-2.0\n2.0",         'expected_output' => "x=-1.0000: local max\nx=1.0000: local min",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n3\n-6\n3\n0\n0.0\n3.0",          'expected_output' => "x=1.0000: saddle",                             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Golden section optimisation ─────────────────────────────
        $seed(23, [
            ['input' => '5.0',    'expected_output' => "x: 3.535534\narea: 25.000000",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '1.0',    'expected_output' => "x: 0.707107\narea: 1.000000",      'is_hidden' => false, 'order_index' => 2],
            ['input' => '10.0',   'expected_output' => "x: 7.071068\narea: 100.000000",    'is_hidden' => true,  'order_index' => 3],
            ['input' => '2.0',    'expected_output' => "x: 1.414214\narea: 4.000000",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Linear regression + gradient verification ────────────────
        $seed(24, [
            ['input' => "3\n1.0 2.0\n2.0 4.0\n3.0 6.0",       'expected_output' => "m: 2.000000\nb: 0.000000\ngrad_m: 0.000000\ngrad_b: 0.000000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0 1.0\n1.0 1.0",                'expected_output' => "m: 0.000000\nb: 1.000000\ngrad_m: 0.000000\ngrad_b: 0.000000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1.0 1.0\n2.0 2.0\n3.0 3.0",       'expected_output' => "m: 1.000000\nb: 0.000000\ngrad_m: 0.000000\ngrad_b: 0.000000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0.0 0.0\n1.0 2.0\n2.0 4.0\n3.0 6.0", 'expected_output' => "m: 2.000000\nb: 0.000000\ngrad_m: 0.000000\ngrad_b: 0.000000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q25: Gradient descent ─────────────────────────────────────────
        $seed(25, [
            ['input' => "0.01\n-2.0\n1000",    'expected_output' => "x: -1.351230\nf(x): -3.506252\niterations: 285",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.01\n2.0\n1000",     'expected_output' => "x: 1.306440\nf(x): -0.644042\niterations: 321",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.001\n-2.0\n5000",   'expected_output' => "x: -1.351230\nf(x): -3.506252\niterations: 2847",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.01\n0.0\n1000",     'expected_output' => "x: 0.125238\nf(x): -0.062424\niterations: 215",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: lim sin(x)/x ─────────────────────────────────────────────
        $seed(26, [
            ['input' => '4',   'expected_output' => "0.99833417\n0.99998333\n0.99999983\n1.00000000\nL: 1.00000000",                       'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',   'expected_output' => "0.99833417\n0.99998333\nL: 1.00000000",                                               'is_hidden' => false, 'order_index' => 2],
            ['input' => '3',   'expected_output' => "0.99833417\n0.99998333\n0.99999983\nL: 1.00000000",                                   'is_hidden' => true,  'order_index' => 3],
            ['input' => '5',   'expected_output' => "0.99833417\n0.99998333\n0.99999983\n1.00000000\n1.00000000\nL: 1.00000000",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Two L'Hôpital limits ─────────────────────────────────────
        $seed(27, [
            ['input' => '5',   'expected_output' => "L1_numerical: 1.00000000\nL1_exact: 1.00000000\nL2_numerical: 0.50000000\nL2_exact: 0.50000000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',   'expected_output' => "L1_numerical: 1.00000000\nL1_exact: 1.00000000\nL2_numerical: 0.50000000\nL2_exact: 0.50000000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => '6',   'expected_output' => "L1_numerical: 1.00000000\nL1_exact: 1.00000000\nL2_numerical: 0.50000000\nL2_exact: 0.50000000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '3',   'expected_output' => "L1_numerical: 1.00000000\nL1_exact: 1.00000000\nL2_numerical: 0.50000000\nL2_exact: 0.50000000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Numerical L'Hôpital for polynomial ratio ─────────────────
        $seed(28, [
            ['input' => "2.0\n2\n-4 0 1\n1\n-2 1",      'expected_output' => '4.000000',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1\n0 1\n1\n0 1",           'expected_output' => '1.000000',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n2\n-1 -1 1\n1\n-1 1",     'expected_output' => '3.000000',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3.0\n2\n-9 0 1\n1\n-3 1",      'expected_output' => '6.000000',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: x^x as x→0+ ─────────────────────────────────────────────
        $seed(29, [
            ['input' => '3',   'expected_output' => "0.79432823\n0.95499259\n0.98929538\nL: 1.00000000",               'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',   'expected_output' => "0.79432823\n0.95499259\nL: 1.00000000",                           'is_hidden' => false, 'order_index' => 2],
            ['input' => '4',   'expected_output' => "0.79432823\n0.95499259\n0.98929538\n0.99772362\nL: 1.00000000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',   'expected_output' => "0.79432823\nL: 1.00000000",                                       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Riemann sums for x² ──────────────────────────────────────
        $seed(30, [
            ['input' => "0.0\n1.0\n100",   'expected_output' => "left: 0.328350\nright: 0.338350\nmidpoint: 0.333325\nexact: 0.333333",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n2.0\n100",   'expected_output' => "left: 2.626800\nright: 2.786800\nmidpoint: 2.706600\nexact: 2.666667",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n3.0\n100",   'expected_output' => "left: 8.731350\nright: 9.631350\nmidpoint: 9.175725\nexact: 9.000000",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n3.0\n100",   'expected_output' => "left: 8.627400\nright: 9.067400\nmidpoint: 8.840067\nexact: 8.666667",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Trapezoidal & Simpson's for polynomial ───────────────────
        $seed(31, [
            ['input' => "0.0\n1.0\n4\n3\n0 0 0 1",      'expected_output' => "trapezoidal: 0.28125000\nsimpsons: 0.25000000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0\n4\n1\n0 1",          'expected_output' => "trapezoidal: 0.50000000\nsimpsons: 0.50000000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n2.0\n4\n2\n0 0 1",        'expected_output' => "trapezoidal: 2.75000000\nsimpsons: 2.66666667",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n1.0\n4\n2\n1 0 0",        'expected_output' => "trapezoidal: 1.00000000\nsimpsons: 1.00000000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Trapezoid from data points ───────────────────────────────
        $seed(32, [
            ['input' => "5\n0.0 1.0\n0.25 0.9394\n0.5 0.7788\n0.75 0.5698\n1.0 0.3679",    'expected_output' => '0.731813',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.0 0.0\n1.0 1.0\n2.0 2.0",                                    'expected_output' => '2.000000',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0.0 1.0\n1.0 1.0\n2.0 1.0\n3.0 1.0",                          'expected_output' => '3.000000',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0.0 0.0\n1.0 1.0\n2.0 4.0",                                    'expected_output' => '2.500000',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Area between x² and x ────────────────────────────────────
        $seed(33, [
            ['input' => '100',    'expected_output' => '0.16666667',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '1000',   'expected_output' => '0.16666667',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '50',     'expected_output' => '0.16666667',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '500',    'expected_output' => '0.16666667',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Monte Carlo π estimate ───────────────────────────────────
        $seed(34, [
            ['input' => "42\n1000000",    'expected_output' => '3.141668',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1000000",     'expected_output' => '3.141573',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n500000",    'expected_output' => '3.141384',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "7\n2000000",     'expected_output' => '3.141700',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: FTC Part 1 verification ─────────────────────────────────
        $seed(35, [
            ['input' => "3.0\n0.001",    'expected_output' => "F(x0): 9.000000\nF'(x0)_numerical: 9.000000\nf(x0)_exact: 9.000000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n0.001",    'expected_output' => "F(x0): 2.666667\nF'(x0)_numerical: 4.000000\nf(x0)_exact: 4.000000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n0.001",    'expected_output' => "F(x0): 0.333333\nF'(x0)_numerical: 1.000000\nf(x0)_exact: 1.000000",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4.0\n0.001",    'expected_output' => "F(x0): 21.333333\nF'(x0)_numerical: 16.000000\nf(x0)_exact: 16.000000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: FTC Part 2 — antiderivative of polynomial ───────────────
        $seed(36, [
            ['input' => "2\n0\n0\n1\n0.0\n3.0",   'expected_output' => '9.00000000',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0\n1\n0.0\n4.0",      'expected_output' => '8.00000000',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n0\n0\n0.0\n3.0",   'expected_output' => '3.00000000',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0\n0\n0\n1\n0.0\n2.0", 'expected_output' => '4.00000000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Accumulation function for sin ────────────────────────────
        $seed(37, [
            ['input' => "3\n0.0\n1.5707963\n3.1415927",   'expected_output' => "0.000000\n1.000000\n2.000000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0.0",                          'expected_output' => "0.000000",                        'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0.0\n6.2831853",              'expected_output' => "0.000000\n0.000000",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1.5707963\n3.1415927",        'expected_output' => "1.000000\n2.000000",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Arc length of x^(3/2) ────────────────────────────────────
        $seed(38, [
            ['input' => '4.0',    'expected_output' => '9.073401',    'is_hidden' => false, 'order_index' => 1],
            ['input' => '1.0',    'expected_output' => '1.439942',    'is_hidden' => false, 'order_index' => 2],
            ['input' => '9.0',    'expected_output' => '28.794571',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.25',   'expected_output' => '0.252027',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: u-substitution verification ──────────────────────────────
        $seed(39, [
            ['input' => "0.0\n1.0",    'expected_output' => "original: 1.71828183\ntransformed: 1.71828183",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n2.0",    'expected_output' => "original: 53.59815003\ntransformed: 53.59815003",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n2.0",    'expected_output' => "original: 52.88165761\ntransformed: 52.88165761",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n0.5",    'expected_output' => "original: 0.28402542\ntransformed: 0.28402542",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Integration by parts (x·eˣ) ─────────────────────────────
        $seed(40, [
            ['input' => "0.0\n1.0",    'expected_output' => "exact: 1.00000000\nnumerical: 1.00000000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n2.0",    'expected_output' => "exact: 4.38905610\nnumerical: 4.38905610",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n3.0",    'expected_output' => "exact: 9.03549361\nnumerical: 9.03549361",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "-1.0\n1.0",   'expected_output' => "exact: 0.63212056\nnumerical: 0.63212056",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Gaussian quadrature ──────────────────────────────────────
        $seed(41, [
            ['input' => "0.0\n2.0\n2\n0 0 1",     'expected_output' => "gauss: 2.66666667\nexact: 2.66666667",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0\n1\n0 1",       'expected_output' => "gauss: 0.50000000\nexact: 0.50000000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n3.0\n2\n1 0 0",     'expected_output' => "gauss: 3.00000000\nexact: 3.00000000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n2.0\n3\n0 0 0 1",   'expected_output' => "gauss: 4.26666667\nexact: 4.00000000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Improper integral 1/x² ──────────────────────────────────
        $seed(42, [
            ['input' => '10000.0',    'expected_output' => "numerical: 0.99990000\nerror: 0.00010000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => '1000.0',     'expected_output' => "numerical: 0.99900000\nerror: 0.00100000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => '100.0',      'expected_output' => "numerical: 0.99000000\nerror: 0.01000000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '100000.0',   'expected_output' => "numerical: 0.99999000\nerror: 0.00001000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Volume of revolution ─────────────────────────────────────
        $seed(43, [
            ['input' => '3.0',    'expected_output' => "exact: 14.137167\nnumerical: 14.137167",   'is_hidden' => false, 'order_index' => 1],
            ['input' => '1.0',    'expected_output' => "exact: 1.570796\nnumerical: 1.570796",    'is_hidden' => false, 'order_index' => 2],
            ['input' => '4.0',    'expected_output' => "exact: 25.132741\nnumerical: 25.132741",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '2.0',    'expected_output' => "exact: 6.283185\nnumerical: 6.283185",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Discrete convolution ─────────────────────────────────────
        $seed(44, [
            ['input' => "1 2 3\n4 5 6",           'expected_output' => '4 13 28 27 18',              'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0\n0 1",               'expected_output' => '0 1 0',                       'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 1 1\n1 1 1",           'expected_output' => '1 2 3 2 1',                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 3 4\n1 2",             'expected_output' => '2 7 10 8',                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Taylor series for eˣ ─────────────────────────────────────
        $seed(45, [
            ['input' => "1.0\n10",     'expected_output' => "series: 2.71828153\nexact: 2.71828183",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n5",      'expected_output' => "series: 1.00000000\nexact: 1.00000000",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n15",     'expected_output' => "series: 7.38905610\nexact: 7.38905610",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "-1.0\n10",    'expected_output' => "series: 0.36787944\nexact: 0.36787944",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Ratio test ───────────────────────────────────────────────
        $seed(46, [
            ['input' => "5\n1.0\n0.5\n0.25\n0.125\n0.0625",    'expected_output' => "0.500000\n0.500000\n0.500000\n0.500000\nconverges",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1.0\n2.0\n4.0\n8.0",               'expected_output' => "2.000000\n2.000000\n2.000000\ndiverges",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1.0\n1.0\n1.0\n1.0",               'expected_output' => "1.000000\n1.000000\n1.000000\ninconclusive",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1.0\n0.1\n0.01",                   'expected_output' => "0.100000\n0.100000\nconverges",                       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Integral test (p-series) ────────────────────────────────
        $seed(47, [
            ['input' => "2.0\n10000.0",    'expected_output' => "integral: 0.999900\nconverges",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n10000.0",    'expected_output' => "integral: 197.990050\ndiverges",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0\n10000.0",    'expected_output' => "integral: 0.499975\nconverges",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.5\n10000.0",    'expected_output' => "integral: 1.999800\nconverges",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Geometric series ─────────────────────────────────────────
        $seed(48, [
            ['input' => "1.0\n0.5\n10",    'expected_output' => "partial_sum: 1.99804688\nexact: 2.00000000",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n2.0\n5",     'expected_output' => "partial_sum: 63.00000000\nexact: diverges",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n0.25\n8",    'expected_output' => "partial_sum: 2.66665649\nexact: 2.66666667",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "3.0\n0.1\n5",     'expected_output' => "partial_sum: 3.33330000\nexact: 3.33333333",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Alternating series test ──────────────────────────────────
        $seed(49, [
            ['input' => "5\n1.0\n0.5\n0.333\n0.25\n0.2",       'expected_output' => "inconclusive\npartial_sum: 0.583000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n1.0\n0.5\n0.333\n0.25\n0.2\n0.009",'expected_output' => "converges\npartial_sum: 0.574000",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0.5\n0.25\n0.125\n0.0625",         'expected_output' => "converges\npartial_sum: 0.312500",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1.0\n0.9\n0.95",                   'expected_output' => "inconclusive\npartial_sum: 1.050000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Radius of convergence ────────────────────────────────────
        $seed(50, [
            ['input' => "5\n1.0\n1.0\n1.0\n1.0\n1.0",           'expected_output' => 'R: 1.000000',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1.0\n0.0\n0.0",                     'expected_output' => 'R: inf',         'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1.0\n2.0\n4.0\n8.0",               'expected_output' => 'R: 0.500000',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1.0\n0.5\n0.25\n0.125",            'expected_output' => 'R: 2.000000',    'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 4 Coding (Professional) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}