<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 4 — Real Analysis & Calculus (Newbie) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering all Module 4 lessons
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Lessons covered:
 *   4.1  Real Numbers, Absolute Value & the Number Line      Q1–Q5
 *   4.2  Sequences & Their Limits                            Q6–Q10
 *   4.3  Limits of Functions & Continuity                    Q11–Q15
 *   4.4  The Derivative: Definition & Interpretation         Q16–Q20
 *   4.5  Applications of Derivatives: MVT, Extrema & Opt.   Q21–Q25
 *   4.6  L'Hôpital's Rule & Indeterminate Forms              Q26–Q30
 *   4.7  The Definite Integral: Definition & Properties      Q31–Q35
 *   4.8  The Fundamental Theorem of Calculus                 Q36–Q40
 *   4.9  Integration Techniques                              Q41–Q45
 *   4.10 Infinite Series & Convergence Tests                 Q46–Q50
 *
 * Safe to re-run: guarded by existence checks.
 */
class Module4CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 4 — Real Analysis & Calculus (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Real Analysis & Calculus — Fundamentals',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Write Python programs that explore the building blocks of real analysis and calculus — absolute value, sequences, numerical limits, derivatives, integrals, and series. All tasks are short and computational; no advanced math library knowledge required.',
                'time_limit_seconds' => 900,
                'base_xp'            => 500,
                'order_index'        => 4,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // 4.1  Real Numbers, Absolute Value & the Number Line  (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Read a number from input and print its absolute value.

Use Python's built-in `abs()`.

Example:
```
Input:  -7
Output: 7
```
MD,
                'starter_code'        => "n = float(input())\n# Print the absolute value\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read two numbers `a` and `b` from input (one per line). Print the distance between them on the number line.

Distance = `|a - b|`

Example:
```
Input:
3
-5
Output: 8.0
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\n# Print |a - b|\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read a number `x` from input. Print `Positive`, `Negative`, or `Zero` based on its sign.

Example:
```
Input:  -3.5
Output: Negative
```
MD,
                'starter_code'        => "x = float(input())\n# Check the sign\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read three numbers from input (one per line) and print them sorted in ascending order on one line, separated by spaces.

Example:
```
Input:
5
-2
3
Output: -2.0 3.0 5.0
```
MD,
                'starter_code'        => "nums = [float(input()) for _ in range(3)]\n# Sort and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read a number `x` and a tolerance `e` from input (one per line). Print `True` if `|x| < e`, otherwise `False`.

This checks whether `x` is within `e` of 0 on the number line.

Example:
```
Input:
0.001
0.01
Output: True
```
MD,
                'starter_code'        => "x = float(input())\ne = float(input())\n# Check if |x| < e\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.2  Sequences & Their Limits  (Q6–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Read an integer `n` from input and print the first `n` terms of the sequence `a(k) = 1/k` for `k = 1, 2, ..., n`.

Print each term rounded to 4 decimal places on its own line.

Example:
```
Input:  4
Output:
1.0
0.5
0.3333
0.25
```
MD,
                'starter_code'        => "n = int(input())\n# Print 1/k for k = 1..n\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Read an integer `n` from input. Compute and print the `n`-th term of the sequence `a(n) = (1 + 1/n)^n` rounded to 6 decimal places.

This sequence approaches Euler's number `e ≈ 2.718281...` as `n` grows.

Example:
```
Input:  100
Output: 2.704814
```
MD,
                'starter_code'        => "n = int(input())\n# Compute (1 + 1/n)^n\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Read an integer `n` from input and print the first `n` terms of the arithmetic sequence starting at `a = 2` with common difference `d = 3`.

`a(k) = 2 + (k-1)*3` for `k = 1, 2, ..., n`

Example:
```
Input:  5
Output:
2
5
8
11
14
```
MD,
                'starter_code'        => "n = int(input())\n# Print arithmetic sequence\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read an integer `n` from input and print the first `n` terms of the geometric sequence starting at `a = 1` with common ratio `r = 0.5`.

`a(k) = 0.5^(k-1)` for `k = 1, 2, ..., n`

Print each term rounded to 4 decimal places.

Example:
```
Input:  5
Output:
1.0
0.5
0.25
0.125
0.0625
```
MD,
                'starter_code'        => "n = int(input())\n# Print geometric sequence\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read an integer `n` from input. Check whether the sequence `a(k) = 1/k` appears to converge by computing `|a(n) - a(n-1)|` for the given `n`.

Print the absolute difference rounded to 6 decimal places.

Example:
```
Input:  10
Output: 0.011111
```

(`a(10) = 0.1`, `a(9) ≈ 0.111111`, difference ≈ `0.011111`)
MD,
                'starter_code'        => "n = int(input())\n# Compute |a(n) - a(n-1)| where a(k) = 1/k\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.3  Limits of Functions & Continuity  (Q11–Q15)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Estimate the limit of `f(x) = (x^2 - 1) / (x - 1)` as `x → 1` numerically.

Read a small value `h` from input (e.g., `0.001`). Compute `f(1 + h)` and `f(1 - h)`, then print their average rounded to 4 decimal places.

The true limit is 2.

Example:
```
Input:  0.001
Output: 2.0
```
MD,
                'starter_code'        => "h = float(input())\n# f(x) = (x**2 - 1) / (x - 1)\n# Compute f(1+h) and f(1-h), print average\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Estimate the limit of `f(x) = sin(x) / x` as `x → 0` numerically.

Read a small value `h` from input. Compute `f(h)` using `math.sin`, and print it rounded to 6 decimal places.

The true limit is 1.

Example:
```
Input:  0.001
Output: 1.0
```
MD,
                'starter_code'        => "import math\nh = float(input())\n# Compute sin(h)/h\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Read a float `x` from input. Check whether `f(x) = x^2` is continuous at `x` by verifying that the left-hand and right-hand limits (using `h = 0.0001`) both equal `f(x)`.

Print `Continuous` if `|f(x+h) - f(x-h)| < 0.001`, otherwise print `Not Continuous`.

Example:
```
Input:  3.0
Output: Continuous
```
MD,
                'starter_code'        => "x = float(input())\nh = 0.0001\n# Check continuity of f(x) = x**2\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Evaluate `f(x) = 3x^2 - 2x + 1` at the value `x` read from input. Print the result rounded to 4 decimal places.

Example:
```
Input:  2.0
Output: 9.0
```
MD,
                'starter_code'        => "x = float(input())\n# Evaluate 3x^2 - 2x + 1\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Read two floats `a` and `b` from input (one per line), where `a < b`. Print 5 evenly spaced values in the closed interval `[a, b]`, each rounded to 4 decimal places, one per line.

Example:
```
Input:
0
1
Output:
0.0
0.25
0.5
0.75
1.0
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\n# Print 5 evenly spaced points in [a, b]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.4  The Derivative: Definition & Interpretation  (Q16–Q20)
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Approximate the derivative of `f(x) = x^2` at a point `x` using the definition:

`f'(x) ≈ (f(x + h) - f(x)) / h`

Read `x` and `h` from input (one per line). Print the result rounded to 4 decimal places.

Example:
```
Input:
3.0
0.001
Output: 6.001
```
MD,
                'starter_code'        => "x = float(input())\nh = float(input())\n# f(x) = x**2, compute forward difference\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Approximate the derivative of `f(x) = x^3 - 2x` at a point `x` using the central difference:

`f'(x) ≈ (f(x + h) - f(x - h)) / (2h)`

Read `x` and `h` from input (one per line). Print the result rounded to 4 decimal places.

Example:
```
Input:
2.0
0.001
Output: 10.0
```
MD,
                'starter_code'        => "x = float(input())\nh = float(input())\n# f(x) = x**3 - 2*x, compute central difference\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
The slope of a tangent line to `f(x) = x^2` at point `x` is `f'(x) = 2x`.

Read `x` from input. Print the equation of the tangent line in the form `y = mx + b`, where `m` and `b` are rounded to 2 decimal places.

Recall: `b = f(x) - m*x`

Example:
```
Input:  3.0
Output: y = 6.0x + -9.0
```
MD,
                'starter_code'        => "x = float(input())\n# Compute slope m = 2x, intercept b = f(x) - m*x\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Read `x` and `h` from input (one per line). Approximate the derivative of `f(x) = sin(x)` at `x` using the central difference formula. Print the result rounded to 6 decimal places.

The exact derivative is `cos(x)`.

Example:
```
Input:
0.0
0.001
Output: 1.0
```
MD,
                'starter_code'        => "import math\nx = float(input())\nh = float(input())\n# f(x) = math.sin(x), central difference\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Read a polynomial coefficient list from input. The first line is `n` (degree), followed by `n+1` coefficients from highest to lowest degree.

Evaluate the polynomial and its derivative (using the power rule) at `x = 2`. Print the polynomial value then the derivative value, each on its own line, rounded to 2 decimal places.

Example:
```
Input:
2
1 -3 2
Output:
0.0
1.0
```
(Polynomial: x^2 - 3x + 2, at x=2: 4-6+2=0; Derivative: 2x-3, at x=2: 1)
MD,
                'starter_code'        => "n = int(input())\ncoeffs = list(map(float, input().split()))\nx = 2.0\n# Evaluate polynomial and derivative at x\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.5  Applications of Derivatives  (Q21–Q25)
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Find a critical point of `f(x) = x^2 - 4x + 3` by solving `f'(x) = 0`.

The derivative is `f'(x) = 2x - 4`. Print the critical point `x` and the value `f(x)` at that point, each on its own line.

No input needed.

Expected output:
```
2.0
-1.0
```
MD,
                'starter_code'        => "# f(x) = x**2 - 4*x + 3\n# f'(x) = 2*x - 4 = 0 => x = ?\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Use Newton's method to find a root of `f(x) = x^2 - 2` (i.e., approximate √2).

Start with `x0 = 1.0`. Iterate 10 times using:
`x_new = x - f(x)/f'(x)`

where `f'(x) = 2x`. Print the final result rounded to 8 decimal places.

No input needed.

Expected output:
```
1.41421356
```
MD,
                'starter_code'        => "x = 1.0\n# Iterate Newton's method 10 times\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Read three floats `a`, `b`, and `c` from input (one per line), representing the quadratic `f(x) = ax^2 + bx + c`.

Find and print the x-value of the vertex (minimum or maximum), rounded to 4 decimal places.

`x_vertex = -b / (2a)`

Example:
```
Input:
1
-4
3
Output: 2.0
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\nc = float(input())\n# Compute vertex x = -b/(2a)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Read `n` and a step size `h` from input (one per line). Sample the function `f(x) = -x^2 + 4x` at `n` evenly spaced points from `x = 0` to `x = 4` (inclusive) using the step.

Print the x value and f(x) where f(x) is the maximum among all sampled points. Round both to 4 decimal places.

Example:
```
Input:
5
1.0
Output:
2.0
4.0
```
MD,
                'starter_code'        => "n = int(input())\nh = float(input())\n# Sample f(x) = -x**2 + 4*x at n points from x=0 to x=4\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Read a float `x` from input. Determine whether `f(x) = x^3 - 3x` is increasing or decreasing at `x` using the sign of its derivative `f'(x) = 3x^2 - 3`.

Print `Increasing` if `f'(x) > 0`, `Decreasing` if `f'(x) < 0`, or `Critical Point` if `f'(x) = 0`.

Example:
```
Input:  2.0
Output: Increasing
```
MD,
                'starter_code'        => "x = float(input())\n# f'(x) = 3x**2 - 3\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.6  L'Hôpital's Rule & Indeterminate Forms  (Q26–Q30)
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Numerically verify L'Hôpital's Rule for `lim x→0 sin(x)/x`.

Read a small `h` from input. Compute `sin(h)/h` and print it rounded to 6 decimal places. The limit is 1.

Example:
```
Input:  0.0001
Output: 1.0
```
MD,
                'starter_code'        => "import math\nh = float(input())\n# Compute sin(h)/h\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Estimate `lim x→0 (e^x - 1) / x` numerically.

Read a small `h` from input. Compute `(math.exp(h) - 1) / h` and print it rounded to 6 decimal places. The true limit is 1.

Example:
```
Input:  0.001
Output: 1.0005
```
MD,
                'starter_code'        => "import math\nh = float(input())\n# Compute (exp(h) - 1) / h\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Estimate `lim x→∞ (2x + 1) / x` numerically.

Read a large number `n` from input. Compute `(2*n + 1) / n` and print it rounded to 6 decimal places. The true limit is 2.

Example:
```
Input:  1000000
Output: 2.000001
```
MD,
                'starter_code'        => "n = float(input())\n# Compute (2*n + 1) / n\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Estimate `lim x→0 (1 - cos(x)) / x^2` numerically.

Read a small `h` from input. Compute and print the value rounded to 6 decimal places. The true limit is 0.5.

Example:
```
Input:  0.001
Output: 0.5
```
MD,
                'starter_code'        => "import math\nh = float(input())\n# Compute (1 - cos(h)) / h**2\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Read a small `h` from input. Estimate `lim x→0 ln(1 + x) / x` numerically using `math.log`.

Print the result rounded to 6 decimal places. The true limit is 1.

Example:
```
Input:  0.001
Output: 0.9995
```
MD,
                'starter_code'        => "import math\nh = float(input())\n# Compute log(1 + h) / h\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.7  The Definite Integral: Definition & Properties  (Q31–Q35)
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Approximate `∫₀¹ x² dx` using a left Riemann sum with `n` rectangles.

Read `n` from input. Print the result rounded to 6 decimal places. (The exact value is 1/3 ≈ 0.333333.)

Example:
```
Input:  1000
Output: 0.332834
```
MD,
                'starter_code'        => "n = int(input())\n# Left Riemann sum for x**2 on [0, 1]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Approximate `∫₀¹ x² dx` using a right Riemann sum with `n` rectangles.

Read `n` from input. Print the result rounded to 6 decimal places. (Exact value ≈ 0.333333.)

Example:
```
Input:  1000
Output: 0.333834
```
MD,
                'starter_code'        => "n = int(input())\n# Right Riemann sum for x**2 on [0, 1]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Approximate `∫₀¹ x² dx` using the midpoint rule with `n` rectangles.

Read `n` from input. Print the result rounded to 6 decimal places.

Example:
```
Input:  1000
Output: 0.333333
```
MD,
                'starter_code'        => "n = int(input())\n# Midpoint rule for x**2 on [0, 1]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Read two floats `a` and `b` and an integer `n` from input (one per line). Use the trapezoidal rule to approximate `∫ₐᵇ x dx`.

Print the result rounded to 4 decimal places. (Exact value = `(b^2 - a^2) / 2`.)

Example:
```
Input:
0
4
100
Output: 8.0
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\nn = int(input())\n# Trapezoidal rule for f(x) = x on [a, b]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Read a float `c` from input. Use the property that `∫ₐᵇ c·f(x) dx = c · ∫ₐᵇ f(x) dx`.

Given `∫₀¹ x² dx = 1/3`, compute and print `c * (1/3)` rounded to 6 decimal places.

Example:
```
Input:  6
Output: 2.0
```
MD,
                'starter_code'        => "c = float(input())\n# Use linearity: c * (1/3)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.8  The Fundamental Theorem of Calculus  (Q36–Q40)
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Using the FTC, evaluate `∫₀ᵇ 2x dx = [x²]₀ᵇ = b²`.

Read `b` from input and print the result.

Example:
```
Input:  5
Output: 25.0
```
MD,
                'starter_code'        => "b = float(input())\n# FTC: integral of 2x from 0 to b = b**2\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Using the FTC, evaluate `∫ₐᵇ 3x² dx = [x³]ₐᵇ = b³ - a³`.

Read `a` and `b` from input (one per line). Print the result.

Example:
```
Input:
1
3
Output: 26.0
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\n# FTC: integral of 3x^2 from a to b = b^3 - a^3\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Using the FTC, evaluate `∫₀ᵇ cos(x) dx = sin(b) - sin(0) = sin(b)`.

Read `b` (in radians) from input. Print the result rounded to 6 decimal places.

Example:
```
Input:  1.5707963
Output: 1.0
```
MD,
                'starter_code'        => "import math\nb = float(input())\n# FTC: integral of cos(x) from 0 to b = sin(b)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Numerically verify the FTC by computing `∫₀² x³ dx` two ways:

1. Using the antiderivative: `[x⁴/4]₀² = 4.0`
2. Using the midpoint rule with `n = 10000`

Read `n` from input. Print both values on separate lines, each rounded to 4 decimal places.

Example:
```
Input:  10000
Output:
4.0
4.0
```
MD,
                'starter_code'        => "n = int(input())\n# Method 1: antiderivative\n# Method 2: midpoint rule for x**3 on [0, 2]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Using the FTC, evaluate `∫₁ᵉ (1/x) dx = ln(e) - ln(1) = 1`.

Read a float `upper` from input (use it as the upper bound instead of `e`). Compute `math.log(upper) - math.log(1)` and print rounded to 6 decimal places.

Example:
```
Input:  2.718281828
Output: 1.0
```
MD,
                'starter_code'        => "import math\nupper = float(input())\n# Compute ln(upper) - ln(1)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.9  Integration Techniques  (Q41–Q45)
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Evaluate `∫₀¹ 2x(x² + 1)⁴ dx` using u-substitution analytically.

Let `u = x² + 1`, `du = 2x dx`. The integral becomes `∫₁² u⁴ du = [u⁵/5]₁² = 32/5 - 1/5 = 31/5`.

No input needed. Print the exact decimal result.

Expected output:
```
6.2
```
MD,
                'starter_code'        => "# Compute 31/5 and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Numerically approximate `∫₀¹ 2x(x² + 1)⁴ dx` using Simpson's rule with `n` subintervals (n must be even).

Read `n` from input. Print the result rounded to 4 decimal places. (Exact = 6.2)

Example:
```
Input:  100
Output: 6.2
```
MD,
                'starter_code'        => "n = int(input())\n# Simpson's rule for f(x) = 2*x*(x**2+1)**4 on [0,1]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Evaluate `∫₀^π x·sin(x) dx` using integration by parts analytically.

The result is `[-x·cos(x) + sin(x)]₀^π = π`.

No input needed. Print the result rounded to 6 decimal places.

Expected output:
```
3.141593
```
MD,
                'starter_code'        => "import math\n# Print math.pi rounded to 6 decimal places\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Numerically approximate `∫₀^π x·sin(x) dx` using the trapezoidal rule with `n` subintervals.

Read `n` from input. Print the result rounded to 4 decimal places. (Exact = π ≈ 3.1416)

Example:
```
Input:  1000
Output: 3.1416
```
MD,
                'starter_code'        => "import math\nn = int(input())\n# Trapezoidal rule for f(x) = x*sin(x) on [0, pi]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Read floats `a`, `b` and integer `n` from input (one per line). Use Simpson's rule to approximate `∫ₐᵇ e^x dx`.

The exact value is `e^b - e^a`. Print both the Simpson approximation and the exact value on separate lines, each rounded to 4 decimal places.

Example:
```
Input:
0
1
100
Output:
1.7183
1.7183
```
MD,
                'starter_code'        => "import math\na = float(input())\nb = float(input())\nn = int(input())\n# Simpson's rule for e^x on [a, b]\n# Also print exact value: math.exp(b) - math.exp(a)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.10  Infinite Series & Convergence Tests  (Q46–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Compute the partial sum of the geometric series `Σ (1/2)^k` for `k = 0` to `n-1`.

Read `n` from input. Print the result rounded to 6 decimal places.

The series converges to 2 as `n → ∞`.

Example:
```
Input:  10
Output: 1.998047
```
MD,
                'starter_code'        => "n = int(input())\n# Sum (0.5)**k for k = 0..n-1\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Compute the partial sum of the harmonic series `Σ 1/k` for `k = 1` to `n`.

Read `n` from input. Print the result rounded to 6 decimal places.

(The harmonic series diverges, but grows slowly.)

Example:
```
Input:  10
Output: 2.928968
```
MD,
                'starter_code'        => "n = int(input())\n# Sum 1/k for k = 1..n\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Approximate `e` using the Taylor series:

`e = Σ 1/k!` for `k = 0` to `n`

Read `n` from input. Print the result rounded to 8 decimal places.

Example:
```
Input:  10
Output: 2.71828183
```
MD,
                'starter_code'        => "import math\nn = int(input())\n# Sum 1/k! for k = 0..n\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Approximate `π/4` using the Leibniz formula:

`π/4 = Σ (-1)^k / (2k+1)` for `k = 0` to `n-1`

Read `n` from input. Print `4 * partial_sum` rounded to 6 decimal places.

Example:
```
Input:  10000
Output: 3.141493
```
MD,
                'starter_code'        => "n = int(input())\n# Leibniz formula for pi\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Apply the ratio test to the series `Σ n!/n^n` for a given term `n`.

Compute the ratio `a(n+1) / a(n)` where `a(n) = n! / n^n`.

Read `n` from input. Print the ratio rounded to 6 decimal places.

If the ratio < 1, print `Converges`; if > 1, print `Diverges`; if = 1, print `Inconclusive`.

Example:
```
Input:  5
Output:
0.331776
Converges
```
MD,
                'starter_code'        => "import math\nn = int(input())\n# a(n) = math.factorial(n) / n**n\n# Compute ratio a(n+1)/a(n)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
        ];

        $questionIds = [];

        foreach ($questionDefs as $def) {
            $row = DB::table('coding_questions')->where([
                'challenge_id' => $challenge->id,
                'order_index'  => $def['order_index'],
            ])->first();

            if (! $row) {
                $id = DB::table('coding_questions')->insertGetId(array_merge(
                    ['challenge_id' => $challenge->id, 'language' => 'python'],
                    $def,
                    ['created_at' => now(), 'updated_at' => now()]
                ));
            } else {
                $id = $row->id;
            }

            $questionIds[$def['order_index']] = $id;
        }

        // ─────────────────────────────────────────────────────────────────
        // 3. TEST CASES
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $now = now()->toDateTimeString();

        $seed = function (int $ord, array $cases) use ($questionIds, $now): void {
            $qid = $questionIds[$ord] ?? null;
            if (! $qid) return;

            if (DB::table('test_cases')->where('coding_question_id', $qid)->exists()) {
                $this->command->warn("  test_cases for Q{$ord} already exist — skipping.");
                return;
            }

            $rows = array_map(fn ($c) => array_merge(
                ['coding_question_id' => $qid, 'created_at' => $now, 'updated_at' => $now],
                $c
            ), $cases);

            DB::table('test_cases')->insert($rows);
        };

        // ── 4.1 ───────────────────────────────────────────────────────────

        $seed(1, [
            ['input' => '-7',    'expected_output' => '7.0',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '3.5',   'expected_output' => '3.5',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',     'expected_output' => '0.0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '-100',  'expected_output' => '100.0','is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(2, [
            ['input' => "3\n-5",   'expected_output' => '8.0',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n0",    'expected_output' => '0.0',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1\n-4",  'expected_output' => '3.0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n3",   'expected_output' => '7.0',  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(3, [
            ['input' => '-3.5',  'expected_output' => 'Negative', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '4.2',   'expected_output' => 'Positive', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',     'expected_output' => 'Zero',     'is_hidden' => true,  'order_index' => 3],
            ['input' => '-0.01', 'expected_output' => 'Negative', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(4, [
            ['input' => "5\n-2\n3",    'expected_output' => '-2.0 3.0 5.0',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2\n3",     'expected_output' => '1.0 2.0 3.0',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0\n0",     'expected_output' => '0.0 0.0 0.0',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "-1\n-2\n-3",  'expected_output' => '-3.0 -2.0 -1.0', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(5, [
            ['input' => "0.001\n0.01",   'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.1\n0.01",     'expected_output' => 'False', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0.0001",     'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "-0.05\n0.01",   'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.2 ───────────────────────────────────────────────────────────

        $seed(6, [
            ['input' => '4',  'expected_output' => "1.0\n0.5\n0.3333\n0.25",                       'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',  'expected_output' => "1.0\n0.5",                                      'is_hidden' => false, 'order_index' => 2],
            ['input' => '1',  'expected_output' => '1.0',                                           'is_hidden' => true,  'order_index' => 3],
            ['input' => '5',  'expected_output' => "1.0\n0.5\n0.3333\n0.25\n0.2",                  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(7, [
            ['input' => '100',    'expected_output' => '2.704814', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',      'expected_output' => '2.0',      'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',     'expected_output' => '2.593742', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '1000',   'expected_output' => '2.716924', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(8, [
            ['input' => '5',  'expected_output' => "2\n5\n8\n11\n14",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',  'expected_output' => "2\n5\n8",          'is_hidden' => false, 'order_index' => 2],
            ['input' => '1',  'expected_output' => '2',                'is_hidden' => true,  'order_index' => 3],
            ['input' => '6',  'expected_output' => "2\n5\n8\n11\n14\n17",'is_hidden' => true,'order_index' => 4],
        ]);

        $seed(9, [
            ['input' => '5',  'expected_output' => "1.0\n0.5\n0.25\n0.125\n0.0625",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',  'expected_output' => "1.0\n0.5\n0.25",                 'is_hidden' => false, 'order_index' => 2],
            ['input' => '1',  'expected_output' => '1.0',                            'is_hidden' => true,  'order_index' => 3],
            ['input' => '4',  'expected_output' => "1.0\n0.5\n0.25\n0.125",         'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(10, [
            ['input' => '10',  'expected_output' => '0.011111', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',   'expected_output' => '0.5',      'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',   'expected_output' => '0.05',     'is_hidden' => true,  'order_index' => 3],
            ['input' => '100', 'expected_output' => '0.0001',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.3 ───────────────────────────────────────────────────────────

        $seed(11, [
            ['input' => '0.001',   'expected_output' => '2.0',    'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0001',  'expected_output' => '2.0',    'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.1',     'expected_output' => '2.1',    'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.01',    'expected_output' => '2.0',    'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(12, [
            ['input' => '0.001',   'expected_output' => '1.0',      'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0001',  'expected_output' => '1.0',      'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.1',     'expected_output' => '0.998334', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.01',    'expected_output' => '0.999983', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(13, [
            ['input' => '3.0',  'expected_output' => 'Continuous', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0',  'expected_output' => 'Continuous', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '-2.0', 'expected_output' => 'Continuous', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '10.0', 'expected_output' => 'Continuous', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(14, [
            ['input' => '2.0',  'expected_output' => '9.0',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0',  'expected_output' => '1.0',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '1.0',  'expected_output' => '2.0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '-1.0', 'expected_output' => '6.0',  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(15, [
            ['input' => "0\n1",   'expected_output' => "0.0\n0.25\n0.5\n0.75\n1.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n4",   'expected_output' => "0.0\n1.0\n2.0\n3.0\n4.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n6",   'expected_output' => "2.0\n3.0\n4.0\n5.0\n6.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "-1\n1",  'expected_output' => "-1.0\n-0.5\n0.0\n0.5\n1.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.4 ───────────────────────────────────────────────────────────

        $seed(16, [
            ['input' => "3.0\n0.001",  'expected_output' => '6.001',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n0.001",  'expected_output' => '2.001',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.001",  'expected_output' => '0.001',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5.0\n0.001",  'expected_output' => '10.001', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(17, [
            ['input' => "2.0\n0.001",   'expected_output' => '10.0',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n0.001",   'expected_output' => '-2.0',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n0.001",   'expected_output' => '1.0',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "-1.0\n0.001",  'expected_output' => '1.0',   'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(18, [
            ['input' => '3.0',  'expected_output' => 'y = 6.0x + -9.0',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0',  'expected_output' => 'y = 0.0x + 0.0',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '1.0',  'expected_output' => 'y = 2.0x + -1.0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '-2.0', 'expected_output' => 'y = -4.0x + -4.0', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(19, [
            ['input' => "0.0\n0.001",          'expected_output' => '1.0',      'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.5707963\n0.001",    'expected_output' => '0.0',      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.1415927\n0.001",    'expected_output' => '-1.0',     'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.7853982\n0.001",    'expected_output' => '0.707107', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(20, [
            ['input' => "2\n1 -3 2",    'expected_output' => "0.0\n1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2 -4",      'expected_output' => "0.0\n2.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 0 0",     'expected_output' => "4.0\n4.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 0 0 0",   'expected_output' => "8.0\n12.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.5 ───────────────────────────────────────────────────────────

        $seed(21, [
            ['input' => null,  'expected_output' => "2.0\n-1.0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => null,  'expected_output' => "2.0\n-1.0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => null,  'expected_output' => "2.0\n-1.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => null,  'expected_output' => "2.0\n-1.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(22, [
            ['input' => null,  'expected_output' => '1.41421356', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null,  'expected_output' => '1.41421356', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null,  'expected_output' => '1.41421356', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null,  'expected_output' => '1.41421356', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(23, [
            ['input' => "1\n-4\n3",    'expected_output' => '2.0',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n-8\n6",    'expected_output' => '2.0',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0\n0",     'expected_output' => '0.0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n-6\n1",    'expected_output' => '1.0',  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(24, [
            ['input' => "5\n1.0",  'expected_output' => "2.0\n4.0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1.0",  'expected_output' => "2.0\n4.0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1.0",  'expected_output' => "2.0\n4.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1.0",  'expected_output' => "2.0\n4.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(25, [
            ['input' => '2.0',   'expected_output' => 'Increasing',     'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0',   'expected_output' => 'Decreasing',     'is_hidden' => false, 'order_index' => 2],
            ['input' => '1.0',   'expected_output' => 'Critical Point', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '-2.0',  'expected_output' => 'Increasing',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.6 ───────────────────────────────────────────────────────────

        $seed(26, [
            ['input' => '0.0001',   'expected_output' => '1.0',      'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.001',    'expected_output' => '1.0',      'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.01',     'expected_output' => '0.999983', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.1',      'expected_output' => '0.998334', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(27, [
            ['input' => '0.001',   'expected_output' => '1.0005',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0001',  'expected_output' => '1.00005',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.1',     'expected_output' => '1.05171',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.01',    'expected_output' => '1.005017', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(28, [
            ['input' => '1000000',     'expected_output' => '2.000001', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '1000000000',  'expected_output' => '2.0',      'is_hidden' => false, 'order_index' => 2],
            ['input' => '100',         'expected_output' => '2.01',     'is_hidden' => true,  'order_index' => 3],
            ['input' => '10000',       'expected_output' => '2.0001',   'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(29, [
            ['input' => '0.001',   'expected_output' => '0.5',      'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0001',  'expected_output' => '0.5',      'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.01',    'expected_output' => '0.5',      'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.1',     'expected_output' => '0.499583', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(30, [
            ['input' => '0.001',   'expected_output' => '0.9995',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0001',  'expected_output' => '0.99995',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.1',     'expected_output' => '0.953102', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.01',    'expected_output' => '0.995033', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.7 ───────────────────────────────────────────────────────────

        $seed(31, [
            ['input' => '1000',  'expected_output' => '0.332834', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '100',   'expected_output' => '0.32835',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',    'expected_output' => '0.285',    'is_hidden' => true,  'order_index' => 3],
            ['input' => '10000', 'expected_output' => '0.333283', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(32, [
            ['input' => '1000',  'expected_output' => '0.333834', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '100',   'expected_output' => '0.33835',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',    'expected_output' => '0.385',    'is_hidden' => true,  'order_index' => 3],
            ['input' => '10000', 'expected_output' => '0.333383', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(33, [
            ['input' => '1000',  'expected_output' => '0.333333', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '100',   'expected_output' => '0.333325', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',    'expected_output' => '0.3325',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '10000', 'expected_output' => '0.333333', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(34, [
            ['input' => "0\n4\n100",  'expected_output' => '8.0',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n2\n100",  'expected_output' => '2.0',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n3\n100",  'expected_output' => '4.0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n1\n100",  'expected_output' => '0.5',  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(35, [
            ['input' => '6',    'expected_output' => '2.0',      'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',    'expected_output' => '0.333333', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '3',    'expected_output' => '1.0',      'is_hidden' => true,  'order_index' => 3],
            ['input' => '0',    'expected_output' => '0.0',      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.8 ───────────────────────────────────────────────────────────

        $seed(36, [
            ['input' => '5',   'expected_output' => '25.0', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',   'expected_output' => '9.0',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',   'expected_output' => '0.0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '10',  'expected_output' => '100.0','is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(37, [
            ['input' => "1\n3",   'expected_output' => '26.0',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n2",   'expected_output' => '8.0',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2",   'expected_output' => '7.0',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n1",   'expected_output' => '1.0',   'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(38, [
            ['input' => '1.5707963',  'expected_output' => '1.0',      'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',          'expected_output' => '0.0',      'is_hidden' => false, 'order_index' => 2],
            ['input' => '3.1415927',  'expected_output' => '0.0',      'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.5',        'expected_output' => '0.479426', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(39, [
            ['input' => '10000',  'expected_output' => "4.0\n4.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '1000',   'expected_output' => "4.0\n4.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '100',    'expected_output' => "4.0\n4.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => '10',     'expected_output' => "4.0\n4.0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(40, [
            ['input' => '2.718281828',  'expected_output' => '1.0',      'is_hidden' => false, 'order_index' => 1],
            ['input' => '1.0',          'expected_output' => '0.0',      'is_hidden' => false, 'order_index' => 2],
            ['input' => '2.0',          'expected_output' => '0.693147', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '10.0',         'expected_output' => '2.302585', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.9 ───────────────────────────────────────────────────────────

        $seed(41, [
            ['input' => null,  'expected_output' => '6.2',  'is_hidden' => false, 'order_index' => 1],
            ['input' => null,  'expected_output' => '6.2',  'is_hidden' => false, 'order_index' => 2],
            ['input' => null,  'expected_output' => '6.2',  'is_hidden' => true,  'order_index' => 3],
            ['input' => null,  'expected_output' => '6.2',  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(42, [
            ['input' => '100',   'expected_output' => '6.2',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '1000',  'expected_output' => '6.2',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',    'expected_output' => '6.2',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '500',   'expected_output' => '6.2',  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(43, [
            ['input' => null,  'expected_output' => '3.141593', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null,  'expected_output' => '3.141593', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null,  'expected_output' => '3.141593', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null,  'expected_output' => '3.141593', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(44, [
            ['input' => '1000',   'expected_output' => '3.1416',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '10000',  'expected_output' => '3.1416',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '100',    'expected_output' => '3.1416',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '500',    'expected_output' => '3.1416',  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(45, [
            ['input' => "0\n1\n100",   'expected_output' => "1.7183\n1.7183", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n2\n100",   'expected_output' => "6.3891\n6.3891", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2\n100",   'expected_output' => "4.6708\n4.6708", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n3\n100",   'expected_output' => "19.0855\n19.0855",'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── 4.10 ──────────────────────────────────────────────────────────

        $seed(46, [
            ['input' => '10',   'expected_output' => '1.998047', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '5',    'expected_output' => '1.9375',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '1',    'expected_output' => '1.0',      'is_hidden' => true,  'order_index' => 3],
            ['input' => '20',   'expected_output' => '2.0',      'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(47, [
            ['input' => '10',   'expected_output' => '2.928968', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '5',    'expected_output' => '2.283333', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '1',    'expected_output' => '1.0',      'is_hidden' => true,  'order_index' => 3],
            ['input' => '100',  'expected_output' => '5.187378', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(48, [
            ['input' => '10',   'expected_output' => '2.71828183', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '5',    'expected_output' => '2.71666667', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '3',    'expected_output' => '2.66666667', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '15',   'expected_output' => '2.71828183', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(49, [
            ['input' => '10000',   'expected_output' => '3.141493', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '100000',  'expected_output' => '3.141583', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '1000',    'expected_output' => '3.140593', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '1000000', 'expected_output' => '3.141592', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(50, [
            ['input' => '5',   'expected_output' => "0.331776\nConverges", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',   'expected_output' => "0.444444\nConverges", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',  'expected_output' => "0.385543\nConverges", 'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',   'expected_output' => "0.5\nConverges",      'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 4 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}