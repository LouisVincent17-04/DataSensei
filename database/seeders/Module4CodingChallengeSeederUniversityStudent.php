<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 4 — Real Analysis & Calculus (UniversityStudent) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the UniversityStudent tier
 *   2. coding_questions    — 50 questions (algorithmic & analytical implementations)
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
class Module4CodingChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (! $category) {
            $this->command->error('UniversityStudent category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 4 — Real Analysis & Calculus (UniversityStudent) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Real Analysis & Calculus — Algorithmic Implementations',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Implement algorithms rooted in real analysis and calculus — from epsilon-delta proofs to numerical ODE solvers, root-finding methods, Taylor series, convergence analysis, and optimisation. Problems demand both mathematical understanding and clean code.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 1500,
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
Write a function `epsilon_close(a, b, epsilon)` that returns `True` if `|a - b| < epsilon`, i.e., `a` and `b` are within `epsilon` of each other.

Read `a`, `b`, and `epsilon` from input (one per line). Print the result.

This formalises the notion of two numbers being "close" on the real number line.

Example:
```
Input:
1.0
1.0005
0.001
Output: True
```
MD,
                'starter_code'        => "def epsilon_close(a, b, epsilon):\n    pass\n\na = float(input())\nb = float(input())\nepsilon = float(input())\nprint(epsilon_close(a, b, epsilon))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Write a function `is_in_interval(x, a, b, closed=True)` that returns `True` if `x` belongs to the interval `[a, b]` (when `closed=True`) or `(a, b)` (when `closed=False`).

Read `x`, `a`, `b`, and a string `closed` (`"True"` or `"False"`) from input (one per line).

Example:
```
Input:
3.0
1.0
5.0
True
Output: True
```
MD,
                'starter_code'        => "def is_in_interval(x, a, b, closed=True):\n    pass\n\nx = float(input())\na = float(input())\nb = float(input())\nclosed = input().strip() == 'True'\nprint(is_in_interval(x, a, b, closed))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
The triangle inequality states that `|a + b| ≤ |a| + |b|` for all real `a`, `b`.

Read `n` pairs of floats from input (first line is `n`, then one pair per line space-separated). For each pair, print `True` if the inequality holds strictly (`<`), `Equal` if it holds with equality, and — since the inequality always holds — never print `False`.

Example:
```
Input:
3
3 -4
2 2
-5 0
Output:
True
Equal
Equal
```
MD,
                'starter_code'        => "n = int(input())\nfor _ in range(n):\n    a, b = map(float, input().split())\n    # Check triangle inequality\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Write a function `supremum(nums)` that returns the least upper bound (supremum) of a finite list of real numbers — i.e., the maximum.

Then write `infimum(nums)` that returns the greatest lower bound — i.e., the minimum.

Read `n` floats from input (one per line). Print the supremum then the infimum, each on its own line, rounded to 4 decimal places.

Example:
```
Input:
5
3.1
-1.5
4.7
2.0
0.0
Output:
4.7
-1.5
```
MD,
                'starter_code'        => "def supremum(nums):\n    pass\n\ndef infimum(nums):\n    pass\n\nn = int(input())\nnums = [float(input()) for _ in range(n)]\nprint(round(supremum(nums), 4))\nprint(round(infimum(nums), 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Write a function `density_check(a, b, n)` that returns `n` rational numbers strictly between `a` and `b` (exclusive), evenly spaced.

This illustrates the density of rational numbers in the real line.

Read `a`, `b`, and `n` from input (one per line). Print each rational number rounded to 6 decimal places, one per line.

Example:
```
Input:
0
1
4
Output:
0.2
0.4
0.6
0.8
```
MD,
                'starter_code'        => "def density_check(a, b, n):\n    pass\n\na = float(input())\nb = float(input())\nn = int(input())\nfor x in density_check(a, b, n):\n    print(round(x, 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.2  Sequences & Their Limits  (Q6–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Write a function `sequence_limit(seq_fn, epsilon, max_terms=10000)` that finds the smallest index `N` such that `|a(n) - L| < epsilon` for all `n ≥ N`, given a proposed limit `L`.

Use the sequence `a(n) = 1/n` and `L = 0`.

Read `epsilon` from input. Print `N` (1-indexed), or `Did not converge` if not found within `max_terms`.

Example:
```
Input:  0.01
Output: 101
```
MD,
                'starter_code'        => "def sequence_limit(seq_fn, L, epsilon, max_terms=10000):\n    pass\n\nepsilon = float(input())\nseq_fn = lambda n: 1/n\nL = 0\nresult = sequence_limit(seq_fn, L, epsilon)\nprint(result)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Write a function `is_cauchy(seq_fn, epsilon, N)` that checks whether the sequence is Cauchy at index `N`: `|a(m) - a(n)| < epsilon` for all `m, n ≥ N`.

Test using `a(n) = 1/n` by checking 100 random pairs `(m, n)` both ≥ N with `m, n ≤ N + 100`.

Read `epsilon` and `N` from input (one per line). Print `Cauchy` or `Not Cauchy`.

Example:
```
Input:
0.01
200
Output: Cauchy
```
MD,
                'starter_code'        => "def is_cauchy(seq_fn, epsilon, N):\n    pass\n\nepsilon = float(input())\nN = int(input())\nseq_fn = lambda n: 1/n\nprint(is_cauchy(seq_fn, epsilon, N))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Write a function `monotone_type(seq_fn, n_terms)` that returns `Increasing`, `Decreasing`, or `Neither` based on the first `n_terms` terms of the sequence.

Read `n_terms` from input. Test it on `a(n) = n / (n + 1)` for `n = 1, 2, ..., n_terms`.

Example:
```
Input:  10
Output: Increasing
```
MD,
                'starter_code'        => "def monotone_type(seq_fn, n_terms):\n    pass\n\nn_terms = int(input())\nseq_fn = lambda n: n / (n + 1)\nprint(monotone_type(seq_fn, n_terms))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Write a function `subsequence(seq_fn, indices)` that returns the subsequence of `a(n)` at the given 1-indexed positions.

Use `a(n) = (-1)^n / n`. Read `k` indices from input (first line is `k`, then one index per line). Print each subsequence value rounded to 6 decimal places.

Example:
```
Input:
3
1
2
4
Output:
-1.0
0.5
0.25
```
MD,
                'starter_code'        => "def subsequence(seq_fn, indices):\n    pass\n\nk = int(input())\nindices = [int(input()) for _ in range(k)]\nseq_fn = lambda n: ((-1)**n) / n\nfor v in subsequence(seq_fn, indices):\n    print(round(v, 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Write a function `squeeze_theorem_check(lower_fn, upper_fn, target_fn, n_terms)` that verifies the squeeze theorem numerically:

For each `n` from 1 to `n_terms`, check that `lower(n) ≤ target(n) ≤ upper(n)`. Print `Squeeze holds` if true for all n, otherwise `Squeeze fails at n=<n>`.

Use:
- `lower(n) = -1/n`
- `upper(n) = 1/n`
- `target(n) = sin(n*pi) / n` (which is 0 for all integer n)

Read `n_terms` from input.

Example:
```
Input:  100
Output: Squeeze holds
```
MD,
                'starter_code'        => "import math\n\ndef squeeze_theorem_check(lower_fn, upper_fn, target_fn, n_terms):\n    pass\n\nn_terms = int(input())\nlower_fn = lambda n: -1/n\nupper_fn = lambda n: 1/n\ntarget_fn = lambda n: math.sin(n * math.pi) / n\nprint(squeeze_theorem_check(lower_fn, upper_fn, target_fn, n_terms))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.3  Limits of Functions & Continuity  (Q11–Q15)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Write a function `numerical_limit(f, c, h_values)` that estimates `lim_{x→c} f(x)` by computing `f(c + h)` for decreasing values of `h` in `h_values`, and returning the last computed value.

Read `c` and a list of `h` values (first line is `c`, second line is space-separated `h` values). Apply it to `f(x) = (x^3 - 8) / (x - 2)`. Print the result rounded to 4 decimal places.

(The true limit as x→2 is 12.)

Example:
```
Input:
2.0
0.1 0.01 0.001 0.0001
Output: 12.0
```
MD,
                'starter_code'        => "def numerical_limit(f, c, h_values):\n    pass\n\nc = float(input())\nh_values = list(map(float, input().split()))\nf = lambda x: (x**3 - 8) / (x - 2)\nprint(round(numerical_limit(f, c, h_values), 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Write a function `is_continuous_at(f, c, epsilon=1e-5, delta=1e-5)` that checks continuity using the epsilon-delta definition numerically:

`|f(x) - f(c)| < epsilon` whenever `|x - c| < delta`

Test at 1000 random `x` values in `(c - delta, c + delta)`.

Read `c` from input. Apply to `f(x) = x^2`. Print `Continuous` or `Not Continuous`.

Example:
```
Input:  2.0
Output: Continuous
```
MD,
                'starter_code'        => "import random\n\ndef is_continuous_at(f, c, epsilon=1e-5, delta=1e-5):\n    pass\n\nc = float(input())\nf = lambda x: x**2\nprint(is_continuous_at(f, c))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
The Intermediate Value Theorem (IVT) states that if `f` is continuous on `[a, b]` and `f(a)` and `f(b)` have opposite signs, there exists a root in `(a, b)`.

Write a function `ivt_root_exists(f, a, b)` that returns `True` if the conditions hold.

Read `a` and `b` from input (one per line). Apply to `f(x) = x^3 - x - 2`. Print `True` or `False` and the root's approximate bracket.

Format: `True [a, b]` or `False`.

Example:
```
Input:
1.0
2.0
Output: True [1.0, 2.0]
```
MD,
                'starter_code'        => "def ivt_root_exists(f, a, b):\n    pass\n\na = float(input())\nb = float(input())\nf = lambda x: x**3 - x - 2\nresult = ivt_root_exists(f, a, b)\nif result:\n    print(f'True [{a}, {b}]')\nelse:\n    print('False')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Write a function `bisection(f, a, b, tol=1e-6, max_iter=1000)` that finds a root of `f` in `[a, b]` using the bisection method.

Apply to `f(x) = x^3 - x - 2`. Read `a` and `b` from input (one per line). Print the root rounded to 6 decimal places.

Example:
```
Input:
1.0
2.0
Output: 1.521379
```
MD,
                'starter_code'        => "def bisection(f, a, b, tol=1e-6, max_iter=1000):\n    pass\n\na = float(input())\nb = float(input())\nf = lambda x: x**3 - x - 2\nprint(round(bisection(f, a, b), 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Write a function `lipschitz_constant(f, a, b, n=1000)` that estimates the Lipschitz constant `L` of `f` on `[a, b]`:

`L = max |f(x) - f(y)| / |x - y|` over a grid of `n` equally spaced pairs.

Read `a` and `b` from input (one per line). Apply to `f(x) = sin(x)`. Print the estimate rounded to 4 decimal places.

(For sin(x), L ≤ 1.)

Example:
```
Input:
0.0
3.141593
Output: 1.0
```
MD,
                'starter_code'        => "import math\n\ndef lipschitz_constant(f, a, b, n=1000):\n    pass\n\na = float(input())\nb = float(input())\nf = lambda x: math.sin(x)\nprint(round(lipschitz_constant(f, a, b), 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.4  The Derivative: Definition & Interpretation  (Q16–Q20)
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Write a function `derivative(f, x, h=1e-7)` that computes the derivative of `f` at `x` using the central difference formula.

Write `second_derivative(f, x, h=1e-5)` using: `(f(x+h) - 2*f(x) + f(x-h)) / h^2`.

Read `x` from input. Apply to `f(x) = x^4 - 3x^2 + 2`. Print first then second derivative, each rounded to 4 decimal places.

(`f'(x) = 4x^3 - 6x`, `f''(x) = 12x^2 - 6`)

Example:
```
Input:  2.0
Output:
20.0
42.0
```
MD,
                'starter_code'        => "def derivative(f, x, h=1e-7):\n    pass\n\ndef second_derivative(f, x, h=1e-5):\n    pass\n\nx = float(input())\nf = lambda x: x**4 - 3*x**2 + 2\nprint(round(derivative(f, x), 4))\nprint(round(second_derivative(f, x), 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Write a function `gradient_descent_step(f, x, lr)` that returns `x - lr * f'(x)` (one step of gradient descent).

Use `derivative(f, x)` from the central difference formula.

Read `x0` (start), `lr` (learning rate), and `n_steps` from input (one per line). Apply to `f(x) = (x - 3)^2`. Print `x` after `n_steps` steps, rounded to 6 decimal places.

Example:
```
Input:
0.0
0.1
50
Output: 2.999988
```
MD,
                'starter_code'        => "def derivative(f, x, h=1e-7):\n    return (f(x + h) - f(x - h)) / (2 * h)\n\ndef gradient_descent_step(f, x, lr):\n    pass\n\nx = float(input())\nlr = float(input())\nn_steps = int(input())\nf = lambda x: (x - 3)**2\nfor _ in range(n_steps):\n    x = gradient_descent_step(f, x, lr)\nprint(round(x, 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Write a function `chain_rule(f, g, x, h=1e-7)` that numerically computes the derivative of `f(g(x))` using the chain rule formula:

`(f ∘ g)'(x) ≈ f'(g(x)) · g'(x)`

Read `x` from input. Use `f(u) = u^2` and `g(x) = sin(x)`. Print the result rounded to 6 decimal places.

(Exact: `2*sin(x)*cos(x) = sin(2x)`)

Example:
```
Input:  1.0
Output: 0.909297
```
MD,
                'starter_code'        => "import math\n\ndef derivative(f, x, h=1e-7):\n    return (f(x + h) - f(x - h)) / (2 * h)\n\ndef chain_rule(f, g, x, h=1e-7):\n    pass\n\nx = float(input())\nf = lambda u: u**2\ng = lambda x: math.sin(x)\nprint(round(chain_rule(f, g, x), 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Write a function `newtons_method(f, f_prime, x0, tol=1e-9, max_iter=1000)` implementing Newton's method for root finding.

Read `x0` from input. Apply to `f(x) = x^3 - 2` (cube root of 2), `f'(x) = 3x^2`. Print the root rounded to 8 decimal places.

Example:
```
Input:  1.0
Output: 1.25992105
```
MD,
                'starter_code'        => "def newtons_method(f, f_prime, x0, tol=1e-9, max_iter=1000):\n    pass\n\nx0 = float(input())\nf = lambda x: x**3 - 2\nf_prime = lambda x: 3*x**2\nprint(round(newtons_method(f, f_prime, x0), 8))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Write a function `taylor_polynomial(f, a, n, x)` that approximates `f(x)` using the Taylor polynomial of degree `n` centred at `a`:

`T_n(x) = Σ f^(k)(a) * (x-a)^k / k!` for `k = 0..n`

Approximate derivatives numerically. Read `a`, `n`, and `x` from input (one per line). Apply to `f(x) = e^x`. Print the result rounded to 6 decimal places.

Example:
```
Input:
0.0
5
1.0
Output: 2.716667
```
MD,
                'starter_code'        => "import math\n\ndef derivative_n(f, x, order, h=1e-4):\n    \"\"\"Numerically estimate the n-th derivative using finite differences.\"\"\"\n    if order == 0:\n        return f(x)\n    return (derivative_n(f, x + h, order - 1, h) - derivative_n(f, x - h, order - 1, h)) / (2 * h)\n\ndef taylor_polynomial(f, a, n, x):\n    pass\n\na = float(input())\nn = int(input())\nx = float(input())\nf = lambda t: math.exp(t)\nprint(round(taylor_polynomial(f, a, n, x), 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.5  Applications of Derivatives  (Q21–Q25)
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Implement the Mean Value Theorem verifier: given `f`, `a`, `b`, find a `c` in `(a, b)` such that `f'(c) ≈ (f(b) - f(a)) / (b - a)`.

Use a grid of 10000 points. Print the first `c` found rounded to 4 decimal places, or `Not found` if none is within tolerance `1e-3`.

Read `a` and `b` from input (one per line). Apply to `f(x) = x^3`.

Example:
```
Input:
0.0
3.0
Output: 1.7321
```
MD,
                'starter_code'        => "def derivative(f, x, h=1e-7):\n    return (f(x + h) - f(x - h)) / (2 * h)\n\na = float(input())\nb = float(input())\nf = lambda x: x**3\n# Find c in (a, b) where f'(c) ≈ (f(b)-f(a))/(b-a)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Write a function `find_extrema(f, a, b, n=10000)` that finds all local minima and maxima of `f` on `[a, b]` by sampling `n` points and detecting sign changes in `f'`.

Return a list of `(x, type)` tuples where `type` is `"min"` or `"max"`.

Read `a` and `b` from input (one per line). Apply to `f(x) = x^3 - 3x`. Print each extremum as `x: type`, rounded to 4 decimal places.

Example:
```
Input:
-3.0
3.0
Output:
-1.0: max
1.0: min
```
MD,
                'starter_code'        => "def derivative(f, x, h=1e-7):\n    return (f(x + h) - f(x - h)) / (2 * h)\n\ndef find_extrema(f, a, b, n=10000):\n    pass\n\na = float(input())\nb = float(input())\nf = lambda x: x**3 - 3*x\nfor x, t in find_extrema(f, a, b):\n    print(f'{round(x, 4)}: {t}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Solve the optimisation problem: find the dimensions of a rectangle with perimeter `P` that maximise area.

For a rectangle with perimeter `P`, the optimal dimensions are both `P/4` (a square), giving area `(P/4)^2`.

Read `P` from input. Print the optimal side length and maximum area, each on its own line, rounded to 4 decimal places.

Example:
```
Input:  20.0
Output:
5.0
25.0
```
MD,
                'starter_code'        => "P = float(input())\n# Optimal rectangle is a square\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Write a function `concavity(f, a, b, n=1000)` that classifies each sub-interval of `[a, b]` as `Concave Up` (f'' > 0) or `Concave Down` (f'' < 0) by checking the second derivative at `n` sample points.

Return a list of distinct inflection regions as `(x, type)` pairs where the sign of `f''` changes.

Read `a` and `b` from input (one per line). Apply to `f(x) = x^3 - 3x`. Print the approximate inflection point rounded to 2 decimal places.

Example:
```
Input:
-3.0
3.0
Output: 0.0
```
MD,
                'starter_code'        => "def derivative(f, x, h=1e-7):\n    return (f(x + h) - f(x - h)) / (2 * h)\n\ndef second_derivative(f, x, h=1e-5):\n    return (f(x + h) - 2*f(x) + f(x - h)) / h**2\n\na = float(input())\nb = float(input())\nf = lambda x: x**3 - 3*x\n# Find where f'' changes sign\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Implement gradient descent to minimise `f(x, y) = (x - 2)^2 + (y + 1)^2`.

Read `x0`, `y0` (starting point), `lr` (learning rate), and `n_steps` from input (one per line).

The gradient is `∇f = (2(x-2), 2(y+1))`. Update: `x -= lr * df/dx`, `y -= lr * df/dy`.

Print the final `(x, y)` rounded to 4 decimal places.

Example:
```
Input:
0.0
0.0
0.1
100
Output:
2.0
-1.0
```
MD,
                'starter_code'        => "x = float(input())\ny = float(input())\nlr = float(input())\nn_steps = int(input())\n# Gradient descent\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.6  L'Hôpital's Rule & Indeterminate Forms  (Q26–Q30)
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Write a function `lhopital(f, g, c, h=1e-7)` that applies L'Hôpital's rule numerically to estimate `lim_{x→c} f(x)/g(x)` by computing `f'(c) / g'(c)`.

Read `c` from input. Apply to `f(x) = sin(x)`, `g(x) = x`. Print the result rounded to 6 decimal places.

Example:
```
Input:  0.0
Output: 1.0
```
MD,
                'starter_code'        => "import math\n\ndef derivative(f, x, h=1e-7):\n    return (f(x + h) - f(x - h)) / (2 * h)\n\ndef lhopital(f, g, c, h=1e-7):\n    pass\n\nc = float(input())\nf = lambda x: math.sin(x)\ng = lambda x: x\nprint(round(lhopital(f, g, c), 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Apply L'Hôpital's rule iteratively (up to `max_iter` times) until the form is no longer `0/0`.

Write `iterated_lhopital(f, g, c, max_iter=5)` that returns `f^(k)'(c) / g^(k)'(c)` for the first `k` where the denominator is non-zero.

Read `c` from input. Apply to `f(x) = 1 - cos(x)`, `g(x) = x^2`. Print result rounded to 6 decimal places. (Limit = 0.5)

Example:
```
Input:  0.0
Output: 0.5
```
MD,
                'starter_code'        => "import math\n\ndef derivative(f, x, h=1e-5):\n    return (f(x + h) - f(x - h)) / (2 * h)\n\ndef iterated_lhopital(f, g, c, max_iter=5):\n    pass\n\nc = float(input())\nf = lambda x: 1 - math.cos(x)\ng = lambda x: x**2\nprint(round(iterated_lhopital(f, g, c), 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Classify indeterminate forms. Read two strings `f_form` and `g_form` from input (each being `"zero"`, `"inf"`, or `"one"`). Print the indeterminate form type:

- `zero / zero` → `0/0`
- `inf / inf` → `inf/inf`
- `zero * inf` → `0*inf`
- `inf - inf` → `inf-inf`
- `zero ^ zero` → `0^0`
- `inf ^ zero` → `inf^0`
- `one ^ inf` → `1^inf`
- Otherwise → `Not indeterminate`

Example:
```
Input:
zero
zero
Output: 0/0
```
MD,
                'starter_code'        => "f_form = input().strip()\ng_form = input().strip()\n# Classify the indeterminate form\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Evaluate `lim_{x→∞} x^(1/x)` numerically using the equivalent form `exp(ln(x)/x)`.

Read a large `n` from input. Compute `math.exp(math.log(n) / n)` and print rounded to 6 decimal places. The limit is 1.

Example:
```
Input:  1000000
Output: 1.000014
```
MD,
                'starter_code'        => "import math\nn = float(input())\n# Compute exp(ln(n)/n)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Write a function `growth_comparison(f, g, n_values)` that, for each `n` in `n_values`, computes `f(n)/g(n)` and prints whether `f` grows faster, slower, or at the same rate as `g`.

Print `f dominates` if `f(n)/g(n) → ∞`, `g dominates` if `→ 0`, `Same rate` if `→ c` (finite nonzero).

Read `n` large values from input (first line is count, then one per line). Apply to `f(n) = n^2`, `g(n) = n*log(n)`.

Example:
```
Input:
3
100
1000
1000000
Output: f dominates
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nvalues = [float(input()) for _ in range(n)]\nf = lambda n: n**2\ng = lambda n: n * math.log(n)\n# Check growth rate\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.7  The Definite Integral: Definition & Properties  (Q31–Q35)
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Write a general `riemann_sum(f, a, b, n, method)` function that supports `"left"`, `"right"`, and `"midpoint"` methods.

Read `a`, `b`, `n`, and `method` from input (one per line). Apply to `f(x) = x^2`. Print the result rounded to 6 decimal places.

Example:
```
Input:
0
1
1000
midpoint
Output: 0.333333
```
MD,
                'starter_code'        => "def riemann_sum(f, a, b, n, method):\n    pass\n\na = float(input())\nb = float(input())\nn = int(input())\nmethod = input().strip()\nf = lambda x: x**2\nprint(round(riemann_sum(f, a, b, n, method), 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Write a function `simpsons_rule(f, a, b, n)` (n must be even) implementing Simpson's 1/3 rule.

`∫ₐᵇ f dx ≈ (h/3)[f(x0) + 4f(x1) + 2f(x2) + 4f(x3) + ... + f(xn)]`

Read `a`, `b`, and `n` from input (one per line). Apply to `f(x) = sin(x)`. Print the result rounded to 6 decimal places.

(∫₀^π sin(x)dx = 2.0)

Example:
```
Input:
0
3.141593
100
Output: 2.0
```
MD,
                'starter_code'        => "import math\n\ndef simpsons_rule(f, a, b, n):\n    pass\n\na = float(input())\nb = float(input())\nn = int(input())\nf = lambda x: math.sin(x)\nprint(round(simpsons_rule(f, a, b, n), 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Verify the additive property of integrals: `∫ₐᶜ f = ∫ₐᵇ f + ∫ᵦᶜ f`.

Read `a`, `b`, `c`, and `n` from input (one per line). Compute each integral using Simpson's rule for `f(x) = x^3`. Print the left side and right side, each rounded to 4 decimal places, then `True` if they match within `1e-4`.

Example:
```
Input:
0
1
2
100
Output:
4.0
4.0
True
```
MD,
                'starter_code'        => "def simpsons_rule(f, a, b, n):\n    h = (b - a) / n\n    x = [a + i * h for i in range(n + 1)]\n    return (h / 3) * sum(\n        f(x[i]) * (1 if i == 0 or i == n else (4 if i % 2 == 1 else 2))\n        for i in range(n + 1)\n    )\n\na = float(input())\nb = float(input())\nc = float(input())\nn = int(input())\nf = lambda x: x**3\n# Verify additive property\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Monte Carlo integration: estimate `∫₀¹ f(x) dx` by sampling `n` random points in `[0, 1]` and averaging `f` values.

Read `n` from input. Use a fixed seed of 42 (`random.seed(42)`). Apply to `f(x) = x^2`. Print the result rounded to 4 decimal places.

Example:
```
Input:  100000
Output: 0.3334
```
MD,
                'starter_code'        => "import random\nrandom.seed(42)\n\nn = int(input())\nf = lambda x: x**2\n# Monte Carlo integration\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Write a function `integral_error(f, a, b, exact, n_values)` that computes the absolute error of the trapezoidal rule compared to the exact value, for each `n` in `n_values`.

Print each error in scientific notation with 2 decimal places (e.g., `1.23e-04`).

Read `n` values from input (first line is count, then one per line). Apply to `f(x) = x^2` on `[0, 1]`, exact = `1/3`.

Example:
```
Input:
3
10
100
1000
Output:
8.33e-03
8.33e-05
8.33e-07
```
MD,
                'starter_code'        => "def trapezoidal(f, a, b, n):\n    h = (b - a) / n\n    return h * (f(a)/2 + sum(f(a + i*h) for i in range(1, n)) + f(b)/2)\n\nk = int(input())\nn_values = [int(input()) for _ in range(k)]\nf = lambda x: x**2\nexact = 1/3\n# Compute errors\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.8  The Fundamental Theorem of Calculus  (Q36–Q40)
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Write a function `antiderivative_at(f, a, x, n=10000)` that numerically computes `F(x) = ∫ₐˣ f(t) dt` using Simpson's rule.

Read `a` and `x` from input (one per line). Apply to `f(t) = e^(-t^2)` (the Gaussian). Print the result rounded to 6 decimal places.

Example:
```
Input:
0.0
1.0
Output: 0.746824
```
MD,
                'starter_code'        => "import math\n\ndef simpsons_rule(f, a, b, n=10000):\n    if n % 2 == 1:\n        n += 1\n    h = (b - a) / n\n    x = [a + i * h for i in range(n + 1)]\n    return (h / 3) * sum(\n        f(x[i]) * (1 if i == 0 or i == n else (4 if i % 2 == 1 else 2))\n        for i in range(n + 1)\n    )\n\ndef antiderivative_at(f, a, x, n=10000):\n    pass\n\na = float(input())\nx = float(input())\nf = lambda t: math.exp(-t**2)\nprint(round(antiderivative_at(f, a, x), 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Write a function `ftc_derivative_check(f, a, x, h=1e-5)` that numerically verifies FTC Part 1:

`d/dx ∫ₐˣ f(t) dt ≈ f(x)`

Compute `(F(x+h) - F(x-h)) / (2h)` where `F(x) = ∫ₐˣ f(t) dt` using the trapezoidal rule.

Read `a` and `x` from input. Apply to `f(t) = t^2`. Print `f(x)` and the numerical derivative, each on its own line, rounded to 4 decimal places.

Example:
```
Input:
0.0
3.0
Output:
9.0
9.0
```
MD,
                'starter_code'        => "def trapezoidal(f, a, b, n=1000):\n    h = (b - a) / n\n    return h * (f(a)/2 + sum(f(a + i*h) for i in range(1, n)) + f(b)/2)\n\ndef ftc_derivative_check(f, a, x, h=1e-5):\n    pass\n\na = float(input())\nx = float(input())\nf = lambda t: t**2\nprint(round(f(x), 4))\nprint(round(ftc_derivative_check(f, a, x), 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Write a function `net_displacement(v, t0, t1, n=10000)` that computes the net displacement of a particle with velocity function `v(t)` from `t0` to `t1` using Simpson's rule.

Also compute the total distance: `∫|v(t)| dt`.

Read `t0` and `t1` from input. Apply to `v(t) = t^2 - 4` (particle changes direction). Print net displacement then total distance, each rounded to 4 decimal places.

Example:
```
Input:
0.0
4.0
Output:
5.3333
10.6667
```
MD,
                'starter_code'        => "import math\n\ndef simpsons_rule(f, a, b, n=10000):\n    if n % 2 == 1:\n        n += 1\n    h = (b - a) / n\n    xs = [a + i * h for i in range(n + 1)]\n    return (h / 3) * sum(\n        f(xs[i]) * (1 if i == 0 or i == n else (4 if i % 2 == 1 else 2))\n        for i in range(n + 1)\n    )\n\nt0 = float(input())\nt1 = float(input())\nv = lambda t: t**2 - 4\n# Compute net displacement and total distance\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Solve the initial value problem using Euler's method (a consequence of FTC):

`dy/dx = f(x)`, `y(a) = y0`

`y(x_{n+1}) = y(x_n) + h * f(x_n)`

Read `a`, `b`, `y0`, and `n` (steps) from input (one per line). Apply to `f(x) = 2x` (exact solution: `y = x^2 + C`). Print `y(b)` rounded to 4 decimal places.

Example:
```
Input:
0.0
3.0
0.0
1000
Output: 9.0
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\ny0 = float(input())\nn = int(input())\nf = lambda x: 2*x\n# Euler's method\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Compute the arc length of `f(x) = x^2` from `x = a` to `x = b` using:

`L = ∫ₐᵇ √(1 + (f'(x))^2) dx`

`f'(x) = 2x`, so the integrand is `√(1 + 4x^2)`.

Read `a` and `b` from input (one per line). Use Simpson's rule with `n = 10000`. Print the result rounded to 6 decimal places.

Example:
```
Input:
0.0
1.0
Output: 1.478943
```
MD,
                'starter_code'        => "import math\n\ndef simpsons_rule(f, a, b, n=10000):\n    if n % 2 == 1:\n        n += 1\n    h = (b - a) / n\n    xs = [a + i * h for i in range(n + 1)]\n    return (h / 3) * sum(\n        f(xs[i]) * (1 if i == 0 or i == n else (4 if i % 2 == 1 else 2))\n        for i in range(n + 1)\n    )\n\na = float(input())\nb = float(input())\nintegrand = lambda x: math.sqrt(1 + (2*x)**2)\n# Compute arc length\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.9  Integration Techniques  (Q41–Q45)
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Write a function `u_substitution_verify(f, F, a, b)` that verifies a u-substitution numerically:

Compute `∫ₐᵇ f(x) dx` numerically and compare to `F(b) - F(a)` (the antiderivative form).

Apply to: `f(x) = 2x * e^(x^2)`, `F(x) = e^(x^2)`.

Read `a` and `b` from input. Print the numerical integral and the antiderivative result, each rounded to 4 decimal places, then `Match` or `Mismatch`.

Example:
```
Input:
0.0
1.0
Output:
1.7183
1.7183
Match
```
MD,
                'starter_code'        => "import math\n\ndef simpsons_rule(f, a, b, n=10000):\n    if n % 2 == 1:\n        n += 1\n    h = (b - a) / n\n    xs = [a + i * h for i in range(n + 1)]\n    return (h / 3) * sum(\n        f(xs[i]) * (1 if i == 0 or i == n else (4 if i % 2 == 1 else 2))\n        for i in range(n + 1)\n    )\n\na = float(input())\nb = float(input())\nf = lambda x: 2 * x * math.exp(x**2)\nF = lambda x: math.exp(x**2)\n# Verify\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Write a function `integration_by_parts_verify(u, v_prime, a, b)` that verifies integration by parts numerically:

`∫ u·v' dx = [u·v]ₐᵇ - ∫ v·u' dx`

Apply to `u(x) = x`, `v'(x) = e^x` (so `v(x) = e^x`, `u'(x) = 1`).

Read `a` and `b` from input. Compute both sides numerically and print them, each rounded to 4 decimal places, then `Match` or `Mismatch`.

Example:
```
Input:
0.0
1.0
Output:
1.0
1.0
Match
```
MD,
                'starter_code'        => "import math\n\ndef derivative(f, x, h=1e-7):\n    return (f(x + h) - f(x - h)) / (2 * h)\n\ndef simpsons_rule(f, a, b, n=10000):\n    if n % 2 == 1:\n        n += 1\n    h = (b - a) / n\n    xs = [a + i * h for i in range(n + 1)]\n    return (h / 3) * sum(\n        f(xs[i]) * (1 if i == 0 or i == n else (4 if i % 2 == 1 else 2))\n        for i in range(n + 1)\n    )\n\na = float(input())\nb = float(input())\nu = lambda x: x\nv_prime = lambda x: math.exp(x)\nv = lambda x: math.exp(x)\nu_prime = lambda x: 1\n# Verify IBP\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Compute the volume of solid of revolution generated by rotating `f(x) = sqrt(x)` around the x-axis from `x = a` to `x = b`:

`V = π ∫ₐᵇ [f(x)]^2 dx`

Read `a` and `b` from input (one per line). Use Simpson's rule with `n = 10000`. Print the result rounded to 4 decimal places.

Example:
```
Input:
0.0
4.0
Output: 25.1327
```
MD,
                'starter_code'        => "import math\n\ndef simpsons_rule(f, a, b, n=10000):\n    if n % 2 == 1:\n        n += 1\n    h = (b - a) / n\n    xs = [a + i * h for i in range(n + 1)]\n    return (h / 3) * sum(\n        f(xs[i]) * (1 if i == 0 or i == n else (4 if i % 2 == 1 else 2))\n        for i in range(n + 1)\n    )\n\na = float(input())\nb = float(input())\nf = lambda x: math.sqrt(x)\n# Volume = pi * integral of f(x)^2\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Implement adaptive Simpson's rule that recursively refines the integration until the error tolerance is met.

Write `adaptive_simpsons(f, a, b, tol=1e-6)`. Read `a`, `b` from input and apply to `f(x) = sin(x^2)`. Print the result rounded to 6 decimal places.

Example:
```
Input:
0.0
3.0
Output: 0.773
```
MD,
                'starter_code'        => "import math\n\ndef simpsons_single(f, a, b):\n    c = (a + b) / 2\n    return (b - a) / 6 * (f(a) + 4 * f(c) + f(b))\n\ndef adaptive_simpsons(f, a, b, tol=1e-6, depth=0):\n    pass\n\na = float(input())\nb = float(input())\nf = lambda x: math.sin(x**2)\nprint(round(adaptive_simpsons(f, a, b), 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Compute the improper integral `∫₁^∞ 1/x^p dx` numerically by truncating at a large upper bound `M`.

The integral converges to `1/(p-1)` for `p > 1` and diverges for `p ≤ 1`.

Read `p` and `M` from input (one per line). Compute the integral on `[1, M]` using Simpson's rule with `n = 10000`. Print the numerical result and the exact value `1/(p-1)` (if `p > 1`), each rounded to 4 decimal places. Print `Diverges` instead of the exact value if `p ≤ 1`.

Example:
```
Input:
2.0
10000.0
Output:
0.9999
1.0
```
MD,
                'starter_code'        => "def simpsons_rule(f, a, b, n=10000):\n    if n % 2 == 1:\n        n += 1\n    h = (b - a) / n\n    xs = [a + i * h for i in range(n + 1)]\n    return (h / 3) * sum(\n        f(xs[i]) * (1 if i == 0 or i == n else (4 if i % 2 == 1 else 2))\n        for i in range(n + 1)\n    )\n\np = float(input())\nM = float(input())\nf = lambda x: 1 / x**p\n# Compute and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // 4.10  Infinite Series & Convergence Tests  (Q46–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Implement the **ratio test**: given a series `a(n)`, compute `L = lim_{n→∞} |a(n+1)/a(n)|`.

Estimate `L` using `|a(N+1)/a(N)|` for a large `N`. Print:
- `Converges absolutely` if `L < 1`
- `Diverges` if `L > 1`
- `Inconclusive` if `L = 1`

Read `N` from input. Apply to `a(n) = n! / 3^n`. Print the ratio rounded to 6 decimal places and then the conclusion.

Example:
```
Input:  20
Output:
7.049698
Diverges
```
MD,
                'starter_code'        => "import math\n\nN = int(input())\na = lambda n: math.factorial(n) / 3**n\n# Compute ratio and classify\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Implement the **integral test**: estimate whether `Σ a(n)` converges by comparing the partial sums to `∫₁^N a(x) dx`.

For `a(n) = 1/n^2`, compute both the partial sum `Σ_{n=1}^{N} 1/n^2` and the integral `∫₁^N 1/x^2 dx = 1 - 1/N`.

Read `N` from input. Print the partial sum, the integral, and `Converges` or `Diverges` based on whether the integral is finite as `N→∞`.

Each value rounded to 6 decimal places.

Example:
```
Input:  100
Output:
1.634984
0.99
Converges
```
MD,
                'starter_code'        => "N = int(input())\n# Compute partial sum of 1/n^2\n# Compute integral 1 - 1/N\n# Print both and conclusion\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Implement the **alternating series test**: a series `Σ (-1)^n b(n)` converges if `b(n)` is decreasing and `b(n) → 0`.

Write a function `alternating_series_test(b_fn, n_terms)` that returns `Converges` or `Does not satisfy conditions`.

Read `n_terms` from input. Apply to `b(n) = 1/n`. Print the conclusion and the partial sum rounded to 6 decimal places.

Example:
```
Input:  1000
Output:
Converges
0.692647
```
MD,
                'starter_code'        => "def alternating_series_test(b_fn, n_terms):\n    pass\n\nn_terms = int(input())\nb_fn = lambda n: 1/n\nconclusion = alternating_series_test(b_fn, n_terms)\npartial_sum = sum(((-1)**(n+1)) / n for n in range(1, n_terms + 1))\nprint(conclusion)\nprint(round(partial_sum, 6))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Compute the power series for `sin(x)` using the Taylor series:

`sin(x) = Σ (-1)^k * x^(2k+1) / (2k+1)!` for `k = 0, 1, 2, ...`

Read `x` (in radians) and `n_terms` from input (one per line). Sum `n_terms` terms and print the result alongside `math.sin(x)`. Both rounded to 8 decimal places.

Example:
```
Input:
1.0
10
Output:
0.84147098
0.84147098
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\nn_terms = int(input())\n# Compute power series for sin(x)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Find the **radius of convergence** of a power series using the ratio test:

`R = 1 / lim_{n→∞} |a_{n+1}/a_n|`

For the series `Σ x^n / n!`, the coefficients are `a_n = 1/n!`. Estimate the ratio for a given large `N`.

Read `N` from input. Print:
1. The ratio `|a_{N+1}/a_N|` rounded to 8 decimal places
2. The radius of convergence `R` (which should be `∞` → print `inf`)

Example:
```
Input:  20
Output:
0.04761905
inf
```
MD,
                'starter_code'        => "import math\n\nN = int(input())\n# a_n = 1 / n!\n# ratio = a(N+1)/a(N)\n# R = 1/limit (limit → 0, so R = inf)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
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
            ['input' => "1.0\n1.0005\n0.001",   'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n1.1\n0.001",       'expected_output' => 'False', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.0\n0.0001",       'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5.0\n5.002\n0.001",      'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(2, [
            ['input' => "3.0\n1.0\n5.0\nTrue",    'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n1.0\n5.0\nFalse",   'expected_output' => 'False', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n1.0\n5.0\nTrue",    'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5.0\n1.0\n5.0\nFalse",   'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(3, [
            ['input' => "3\n3 -4\n2 2\n-5 0",   'expected_output' => "True\nEqual\nEqual", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1 1",                'expected_output' => 'Equal',             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n3 4\n-2 -3",         'expected_output' => "Equal\nEqual",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1 -1",               'expected_output' => 'True',              'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(4, [
            ['input' => "5\n3.1\n-1.5\n4.7\n2.0\n0.0",   'expected_output' => "4.7\n-1.5", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1.0\n2.0\n3.0",               'expected_output' => "3.0\n1.0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5.5",                          'expected_output' => "5.5\n5.5",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n-1.0\n-2.0\n0.0\n1.0",        'expected_output' => "1.0\n-2.0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(5, [
            ['input' => "0\n1\n4",    'expected_output' => "0.2\n0.4\n0.6\n0.8",               'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n2\n3",    'expected_output' => "0.5\n1.0\n1.5",                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1\n1\n2",   'expected_output' => "-0.333333\n0.333333",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n10\n4",   'expected_output' => "2.0\n4.0\n6.0\n8.0",               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.2 ───────────────────────────────────────────────────────────

        $seed(6, [
            ['input' => '0.01',    'expected_output' => '101',              'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.1',     'expected_output' => '11',               'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.001',   'expected_output' => '1001',             'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.5',     'expected_output' => '3',                'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(7, [
            ['input' => "0.01\n200",   'expected_output' => 'Cauchy', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.1\n20",     'expected_output' => 'Cauchy', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.001\n2000", 'expected_output' => 'Cauchy', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.001\n500",  'expected_output' => 'Cauchy', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(8, [
            ['input' => '10',  'expected_output' => 'Increasing', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '5',   'expected_output' => 'Increasing', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '100', 'expected_output' => 'Increasing', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '2',   'expected_output' => 'Increasing', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(9, [
            ['input' => "3\n1\n2\n4",   'expected_output' => "-1.0\n0.5\n0.25",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2",         'expected_output' => '0.5',               'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n3",      'expected_output' => "-1.0\n-0.333333",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n10",        'expected_output' => '0.1',               'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(10, [
            ['input' => '100',   'expected_output' => 'Squeeze holds', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',    'expected_output' => 'Squeeze holds', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '1000',  'expected_output' => 'Squeeze holds', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '50',    'expected_output' => 'Squeeze holds', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.3 ───────────────────────────────────────────────────────────

        $seed(11, [
            ['input' => "2.0\n0.1 0.01 0.001 0.0001",  'expected_output' => '12.0', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n0.01 0.001",              'expected_output' => '12.0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n0.001 0.0001 0.00001",    'expected_output' => '12.0', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.0\n0.1",                     'expected_output' => '12.61','is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(12, [
            ['input' => '2.0',   'expected_output' => 'Continuous', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0',   'expected_output' => 'Continuous', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '-3.0',  'expected_output' => 'Continuous', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '10.0',  'expected_output' => 'Continuous', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(13, [
            ['input' => "1.0\n2.0",   'expected_output' => 'True [1.0, 2.0]',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "-2.0\n0.0",  'expected_output' => 'False',             'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n3.0",   'expected_output' => 'True [1.0, 3.0]',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n1.0",   'expected_output' => 'False',             'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(14, [
            ['input' => "1.0\n2.0",   'expected_output' => '1.521379', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n3.0",   'expected_output' => '1.521379', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.5\n2.0",   'expected_output' => '1.521379', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n1.6",   'expected_output' => '1.521379', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(15, [
            ['input' => "0.0\n3.141593",  'expected_output' => '1.0',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0",       'expected_output' => '1.0',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1.0\n1.0",      'expected_output' => '1.0',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n6.283185",  'expected_output' => '1.0',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.4 ───────────────────────────────────────────────────────────

        $seed(16, [
            ['input' => '2.0',   'expected_output' => "20.0\n42.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0',   'expected_output' => "0.0\n-6.0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => '1.0',   'expected_output' => "2.0\n6.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '-1.0',  'expected_output' => "-2.0\n6.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(17, [
            ['input' => "0.0\n0.1\n50",   'expected_output' => '2.999988', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n0.1\n100",  'expected_output' => '3.0',      'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0\n0.1\n50",  'expected_output' => '3.0',      'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n0.01\n200", 'expected_output' => '3.0',      'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(18, [
            ['input' => '1.0',   'expected_output' => '0.909297', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0',   'expected_output' => '0.0',      'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.5',   'expected_output' => '0.841471', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '3.14',  'expected_output' => '-0.0',     'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(19, [
            ['input' => '1.0',   'expected_output' => '1.25992105', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2.0',   'expected_output' => '1.25992105', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.5',   'expected_output' => '1.25992105', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '3.0',   'expected_output' => '1.25992105', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(20, [
            ['input' => "0.0\n5\n1.0",   'expected_output' => '2.716667', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n3\n1.0",   'expected_output' => '2.666667', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n10\n1.0",  'expected_output' => '2.718282', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n1\n1.0",   'expected_output' => '2.0',      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.5 ───────────────────────────────────────────────────────────

        $seed(21, [
            ['input' => "0.0\n3.0",   'expected_output' => '1.7321', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n2.0",   'expected_output' => '1.1547', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n4.0",   'expected_output' => '2.6458', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n1.0",   'expected_output' => '0.5774', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(22, [
            ['input' => "-3.0\n3.0",   'expected_output' => "-1.0: max\n1.0: min", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "-2.0\n2.0",   'expected_output' => "-1.0: max\n1.0: min", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-3.0\n0.0",   'expected_output' => '-1.0: max',           'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n3.0",    'expected_output' => '1.0: min',            'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(23, [
            ['input' => '20.0',  'expected_output' => "5.0\n25.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '40.0',  'expected_output' => "10.0\n100.0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => '12.0',  'expected_output' => "3.0\n9.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => '8.0',   'expected_output' => "2.0\n4.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(24, [
            ['input' => "-3.0\n3.0",  'expected_output' => '0.0', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "-2.0\n2.0",  'expected_output' => '0.0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-5.0\n5.0",  'expected_output' => '0.0', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "-1.0\n1.0",  'expected_output' => '0.0', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(25, [
            ['input' => "0.0\n0.0\n0.1\n100",    'expected_output' => "2.0\n-1.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5.0\n5.0\n0.1\n200",    'expected_output' => "2.0\n-1.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.0\n0.01\n500",   'expected_output' => "2.0\n-1.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "-5.0\n-5.0\n0.1\n300",  'expected_output' => "2.0\n-1.0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.6 ───────────────────────────────────────────────────────────

        $seed(26, [
            ['input' => '0.0',          'expected_output' => '1.0',      'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.001',        'expected_output' => '1.0',      'is_hidden' => false, 'order_index' => 2],
            ['input' => '1.5707963',    'expected_output' => '0.0',      'is_hidden' => true,  'order_index' => 3],
            ['input' => '3.1415927',    'expected_output' => '-1.0',     'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(27, [
            ['input' => '0.0',    'expected_output' => '0.5', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.001',  'expected_output' => '0.5', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.0001', 'expected_output' => '0.5', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.01',   'expected_output' => '0.5', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(28, [
            ['input' => "zero\nzero",   'expected_output' => '0/0',              'is_hidden' => false, 'order_index' => 1],
            ['input' => "inf\ninf",     'expected_output' => 'inf/inf',          'is_hidden' => false, 'order_index' => 2],
            ['input' => "one\ninf",     'expected_output' => '1^inf',            'is_hidden' => true,  'order_index' => 3],
            ['input' => "zero\none",    'expected_output' => 'Not indeterminate', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(29, [
            ['input' => '1000000',      'expected_output' => '1.000014', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '1000000000',   'expected_output' => '1.000000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '1000',         'expected_output' => '1.006909', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '1000000000000','expected_output' => '1.000000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(30, [
            ['input' => "3\n100\n1000\n1000000",      'expected_output' => 'f dominates', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10000\n100000",            'expected_output' => 'f dominates', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1000000000",               'expected_output' => 'f dominates', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n100\n10000\n1000000000",   'expected_output' => 'f dominates', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.7 ───────────────────────────────────────────────────────────

        $seed(31, [
            ['input' => "0\n1\n1000\nmidpoint",  'expected_output' => '0.333333', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n1\n1000\nleft",      'expected_output' => '0.332834', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n1\n1000\nright",     'expected_output' => '0.333834', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n2\n1000\nmidpoint",  'expected_output' => '2.666667', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(32, [
            ['input' => "0\n3.141593\n100",    'expected_output' => '2.0',      'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n3.141593\n1000",   'expected_output' => '2.0',      'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n1.570796\n100",    'expected_output' => '1.0',      'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n6.283185\n100",    'expected_output' => '0.0',      'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(33, [
            ['input' => "0\n1\n2\n100",   'expected_output' => "4.0\n4.0\nTrue",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n1\n3\n100",   'expected_output' => "20.25\n20.25\nTrue",'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2\n3\n100",   'expected_output' => "19.25\n19.25\nTrue",'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n0\n2\n100",   'expected_output' => "4.0\n4.0\nTrue",   'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(34, [
            ['input' => '100000',   'expected_output' => '0.3334', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '1000000',  'expected_output' => '0.3333', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '10000',    'expected_output' => '0.333',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '500000',   'expected_output' => '0.3333', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(35, [
            ['input' => "3\n10\n100\n1000",  'expected_output' => "8.33e-03\n8.33e-05\n8.33e-07", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n10",             'expected_output' => '8.33e-03',                      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n100\n1000",      'expected_output' => "8.33e-05\n8.33e-07",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1000",           'expected_output' => '8.33e-07',                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.8 ───────────────────────────────────────────────────────────

        $seed(36, [
            ['input' => "0.0\n1.0",    'expected_output' => '0.746824', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n0.5",    'expected_output' => '0.461281', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n2.0",    'expected_output' => '0.882081', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n0.0",    'expected_output' => '0.0',      'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(37, [
            ['input' => "0.0\n3.0",   'expected_output' => "9.0\n9.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n2.0",   'expected_output' => "4.0\n4.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n1.0",   'expected_output' => "1.0\n1.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n0.0",   'expected_output' => "0.0\n0.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(38, [
            ['input' => "0.0\n4.0",   'expected_output' => "5.3333\n10.6667", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n2.0",   'expected_output' => "-5.3333\n5.3333", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n4.0",   'expected_output' => "10.6667\n10.6667",'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n3.0",   'expected_output' => "0.0\n6.0",        'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(39, [
            ['input' => "0.0\n3.0\n0.0\n1000",   'expected_output' => '9.0',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n2.0\n0.0\n1000",   'expected_output' => '4.0',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n1.0\n0.0\n1000",   'expected_output' => '1.0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n3.0\n1.0\n1000",   'expected_output' => '9.0',  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(40, [
            ['input' => "0.0\n1.0",   'expected_output' => '1.478943', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n2.0",   'expected_output' => '4.646861', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.5",   'expected_output' => '0.548184', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n2.0",   'expected_output' => '3.167918', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.9 ───────────────────────────────────────────────────────────

        $seed(41, [
            ['input' => "0.0\n1.0",   'expected_output' => "1.7183\n1.7183\nMatch", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n2.0",   'expected_output' => "53.5982\n53.5982\nMatch",'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.5",   'expected_output' => "1.2974\n1.2974\nMatch", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n2.0",   'expected_output' => "52.3799\n52.3799\nMatch",'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(42, [
            ['input' => "0.0\n1.0",   'expected_output' => "1.0\n1.0\nMatch", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n2.0",   'expected_output' => "4.7183\n4.7183\nMatch",'is_hidden' => false,'order_index' => 2],
            ['input' => "1.0\n2.0",   'expected_output' => "3.7183\n3.7183\nMatch",'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.0\n0.5",   'expected_output' => "0.8244\n0.8244\nMatch",'is_hidden' => true, 'order_index' => 4],
        ]);

        $seed(43, [
            ['input' => "0.0\n4.0",   'expected_output' => '25.1327', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0",   'expected_output' => '1.5708',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n9.0",   'expected_output' => '127.2345','is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n4.0",   'expected_output' => '23.5619', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(44, [
            ['input' => "0.0\n3.0",   'expected_output' => '0.773',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0",   'expected_output' => '0.31',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n2.0",   'expected_output' => '0.8048', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n0.5",   'expected_output' => '0.0416', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(45, [
            ['input' => "2.0\n10000.0",   'expected_output' => "0.9999\n1.0",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n10000.0",   'expected_output' => "9.2103\nDiverges",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0\n10000.0",   'expected_output' => "0.5\n0.5",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n10000.0",   'expected_output' => "197.98\nDiverges",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── 4.10 ──────────────────────────────────────────────────────────

        $seed(46, [
            ['input' => '20',   'expected_output' => "7.049698\nDiverges",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',   'expected_output' => "3.508553\nDiverges",  'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',    'expected_output' => "1.646091\nDiverges",  'is_hidden' => true,  'order_index' => 3],
            ['input' => '30',   'expected_output' => "10.333333\nDiverges", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(47, [
            ['input' => '100',   'expected_output' => "1.634984\n0.99\nConverges",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',    'expected_output' => "1.197532\n0.9\nConverges",     'is_hidden' => false, 'order_index' => 2],
            ['input' => '1000',  'expected_output' => "1.643935\n0.999\nConverges",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '5',     'expected_output' => "1.463611\n0.8\nConverges",     'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(48, [
            ['input' => '1000',    'expected_output' => "Converges\n0.692647",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '100',     'expected_output' => "Converges\n0.688172",  'is_hidden' => false, 'order_index' => 2],
            ['input' => '10000',   'expected_output' => "Converges\n0.693097",  'is_hidden' => true,  'order_index' => 3],
            ['input' => '500',     'expected_output' => "Converges\n0.691647",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(49, [
            ['input' => "1.0\n10",   'expected_output' => "0.84147098\n0.84147098", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n5",    'expected_output' => "0.0\n0.0",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.14159\n15",'expected_output' => "0.00000265\n0.00000265",'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.5\n8",    'expected_output' => "0.47942554\n0.47942554", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(50, [
            ['input' => '20',   'expected_output' => "0.04761905\ninf", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',   'expected_output' => "0.09090909\ninf", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',    'expected_output' => "0.16666667\ninf", 'is_hidden' => true,  'order_index' => 3],
            ['input' => '50',   'expected_output' => "0.01960784\ninf", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 4 Coding (UniversityStudent) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}