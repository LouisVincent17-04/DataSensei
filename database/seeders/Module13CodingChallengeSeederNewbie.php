<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 13 — Optimization for Machine Learning (Newbie) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering beginner optimization in Python
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mirrors Module 13 curriculum):
 *   13.1  What Is Optimization? Goals, Vocabulary & Problem Formulation
 *   13.2  Calculus Review: Derivatives, Gradients & Critical Points
 *   13.3  Gradient Descent: The Engine of Machine Learning
 *   13.4  SGD, Mini-Batch, Momentum & Adam
 *   13.5  Convexity, Local vs Global Minima & Saddle Points
 *   13.6  Constrained Optimization: Lagrange & KKT
 *   13.7  Linear Programming: Simplex & Scipy
 *   13.8  Metaheuristics: Genetic Algorithms & Simulated Annealing
 *   13.9  Hyperparameter Optimization: Grid, Random & Bayesian
 *   13.10 Second-Order Methods: Newton, BFGS & L-BFGS
 *
 * Difficulty: Newbie — straightforward formulas, no advanced libraries,
 *             use only Python's `math` and basic built-ins.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module13CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 13 — Optimization for Machine Learning (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Optimization for Machine Learning',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply fundamental optimization concepts in Python. Implement gradient descent, compute derivatives, explore convexity, and run basic search strategies using only Python\'s built-in tools and the math module.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 500,
                'order_index'        => 13,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 13.1: What Is Optimization? (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Find the **minimum value** in a list of numbers and print it along with its index (0-based).

Read `n`, then `n` values. Print `min_value` and `index` on separate lines, each as a float rounded to 4 decimal places (index as integer).

Example:
```
Input:
5
3
1
4
1
5
Output:
1.0
1
```
(If there are multiple minima, print the first occurrence.)
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Find and print minimum value and index\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Evaluate the **objective function** f(x) = x² − 4x + 4 at a given value of x.

Read a float `x`. Print f(x) rounded to 4 decimal places.

Example:
```
Input:
2.0
Output: 0.0
```
MD,
                'starter_code'        => "x = float(input())\n# Evaluate f(x) = x^2 - 4x + 4\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Given a list of `(x, f(x))` pairs, identify the pair where f(x) is **minimized**.

Read `n`, then `n` pairs of `x f(x)` (space-separated). Print the `x` value (rounded to 4 decimal places) where f(x) is minimized.

Example:
```
Input:
4
0 4
1 1
2 0
3 1
Output: 2.0
```
MD,
                'starter_code'        => "n = int(input())\npairs = []\nfor _ in range(n):\n    x, fx = map(float, input().split())\n    pairs.append((x, fx))\n# Find and print x with minimum f(x)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Classify an optimization problem as **minimization** or **maximization**.

Read a string: `minimize` or `maximize`. Print `minimization` or `maximization`.

Example:
```
Input:
minimize
Output: minimization
```
MD,
                'starter_code'        => "goal = input().strip().lower()\n# Print classification\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Compute the **improvement** when moving from one solution to another.

Improvement = f(old) − f(new)   (positive = better for minimization)

Read two floats: `f_old` and `f_new`. Print the improvement rounded to 4 decimal places and then print `improved` if improvement > 0, `no improvement` if improvement <= 0.

Example:
```
Input:
10.0
7.5
Output:
2.5
improved
```
MD,
                'starter_code'        => "f_old = float(input())\nf_new = float(input())\n# Compute improvement and classify\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 13.2: Calculus Review (Q6–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Compute the **numerical derivative** of f(x) = x² at a point x using the central difference formula.

f'(x) ≈ [f(x + h) − f(x − h)] / (2h)

Read `x` and `h`. Print the numerical derivative rounded to 6 decimal places.

Example:
```
Input:
3.0
0.001
Output: 6.0
```
MD,
                'starter_code'        => "x = float(input())\nh = float(input())\n# Compute numerical derivative of x^2\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Compute the **gradient** (vector of partial derivatives) of f(x, y) = x² + y² using central differences.

∂f/∂x ≈ [f(x+h,y) − f(x-h,y)] / (2h)
∂f/∂y ≈ [f(x,y+h) − f(x,y-h)] / (2h)

Read `x`, `y`, `h`. Print `∂f/∂x` and `∂f/∂y` each rounded to 6 decimal places on separate lines.

Example:
```
Input:
2.0
3.0
0.001
Output:
4.0
6.0
```
MD,
                'starter_code'        => "x = float(input())\ny = float(input())\nh = float(input())\n# Compute partial derivatives\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Determine if a point is a **critical point** of f(x) = x³ − 3x.

f'(x) = 3x² − 3

A critical point is where f'(x) = 0 (use tolerance 1e-6).

Read `x`. Print `critical point` or `not a critical point`.

Example:
```
Input:
1.0
Output: critical point
```
```
Input:
2.0
Output: not a critical point
```
MD,
                'starter_code'        => "x = float(input())\n# Check if x is a critical point of f(x) = x^3 - 3x\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Classify a critical point as a **local minimum**, **local maximum**, or **saddle point** using the second derivative test.

For f(x) = x³ − 3x:
- f'(x) = 3x² − 3
- f''(x) = 6x

If f''(x) > 0 → `local minimum`
If f''(x) < 0 → `local maximum`
If f''(x) = 0 → `inconclusive`

Read `x`. Print the classification.

Example:
```
Input:
1.0
Output: local minimum
```
MD,
                'starter_code'        => "x = float(input())\n# Classify critical point using second derivative\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Compute the **gradient magnitude** (norm of the gradient vector).

‖∇f‖ = √(∂f/∂x₁² + ∂f/∂x₂² + ... + ∂f/∂xₙ²)

Read `n`, then `n` partial derivative values. Print the magnitude rounded to 4 decimal places.

Example:
```
Input:
2
3.0
4.0
Output: 5.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\ngrads = [float(input()) for _ in range(n)]\n# Compute gradient magnitude\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 13.3: Gradient Descent (Q11–Q16)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Perform **one step of gradient descent** on f(x) = x².

Update rule: x_new = x − α × f'(x) = x − α × 2x

Read `x` and learning rate `α`. Print `x_new` rounded to 6 decimal places.

Example:
```
Input:
5.0
0.1
Output: 4.0
```
MD,
                'starter_code'        => "x = float(input())\nalpha = float(input())\n# Perform one gradient descent step\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Run **gradient descent** on f(x) = x² for `n` steps starting from `x0`.

Update: x = x − α × 2x

Read `x0`, `α`, and `n`. Print the value of x after each step (n values total), rounded to 6 decimal places, one per line.

Example:
```
Input:
10.0
0.1
3
Output:
8.0
6.4
5.12
```
MD,
                'starter_code'        => "x = float(input())\nalpha = float(input())\nn = int(input())\n# Run n gradient descent steps and print x after each\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Run gradient descent on f(x) = x² until **convergence** (|x_new − x_old| < tolerance).

Read `x0`, `α`, `tolerance`. Print the final x rounded to 6 decimal places and the number of steps taken.

Example:
```
Input:
10.0
0.1
0.01
Output:
0.034868
23
```
MD,
                'starter_code'        => "x = float(input())\nalpha = float(input())\ntol = float(input())\n# Run gradient descent until convergence\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Determine if a learning rate causes **divergence** in gradient descent on f(x) = x².

The update is x_new = x − α × 2x = x(1 − 2α).

Divergence occurs if |1 − 2α| >= 1, i.e., α >= 1 or α <= 0.

Read `α`. Print `converges` or `diverges`.

Example:
```
Input:
0.3
Output: converges
```
```
Input:
1.0
Output: diverges
```
MD,
                'starter_code'        => "alpha = float(input())\n# Print converges or diverges\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Compute the **Mean Squared Error (MSE) gradient** for simple linear regression (no intercept).

Given predictions ŷ = w × x and true labels y, the gradient of MSE with respect to w is:

∂MSE/∂w = (2/n) × Σ (ŷᵢ − yᵢ) × xᵢ

Read `n`, then `n` x values, then `n` y values, then `w`. Print the gradient rounded to 6 decimal places.

Example:
```
Input:
3
1
2
3
2
4
6
1.0
Output: 0.0
```
MD,
                'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\nw = float(input())\n# Compute and print MSE gradient\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Update **weight and bias** using gradient descent on MSE for linear regression.

ŷ = w × x + b
∂MSE/∂w = (2/n) × Σ (ŷᵢ − yᵢ) × xᵢ
∂MSE/∂b = (2/n) × Σ (ŷᵢ − yᵢ)

Read `n`, then `n` x values, then `n` y values, then `w`, `b`, `α`. Print updated `w` and `b`, each rounded to 6 decimal places, on separate lines.

Example:
```
Input:
2
1
2
2
4
0.0
0.0
0.1
Output:
0.4
0.2
```
MD,
                'starter_code'        => "n = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\nw = float(input())\nb = float(input())\nalpha = float(input())\n# Compute gradients and update w, b\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 13.4: SGD, Momentum & Adam (Q17–Q21)
            // ═══════════════════════════════════════════════════════════════

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Simulate one step of **SGD** (Stochastic Gradient Descent) on f(x) = x² using a single randomly selected sample (already provided).

Update: x_new = x − α × gradient

Read `x`, `α`, `gradient`. Print `x_new` rounded to 6 decimal places.

Example:
```
Input:
5.0
0.1
8.0
Output: 4.2
```
MD,
                'starter_code'        => "x = float(input())\nalpha = float(input())\ngradient = float(input())\n# Compute SGD step\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Simulate one step of **gradient descent with momentum**.

v_new = β × v + (1 − β) × gradient
x_new = x − α × v_new

Read `x`, `v` (velocity), `gradient`, `α`, `β`. Print `v_new` and `x_new`, each rounded to 6 decimal places, on separate lines.

Example:
```
Input:
5.0
0.0
10.0
0.1
0.9
Output:
1.0
4.9
```
MD,
                'starter_code'        => "x = float(input())\nv = float(input())\ngradient = float(input())\nalpha = float(input())\nbeta = float(input())\n# Compute momentum update\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Compute the **Adam optimizer** update for one step.

m = β1 × m_prev + (1 − β1) × g
v = β2 × v_prev + (1 − β2) × g²
m_hat = m / (1 − β1^t)
v_hat = v / (1 − β2^t)
x_new = x − α × m_hat / (√v_hat + ε)

Read `x`, `m_prev`, `v_prev`, `g` (gradient), `α`, `β1`, `β2`, `ε`, `t` (step number). Print `x_new` rounded to 6 decimal places.

Example:
```
Input:
5.0
0.0
0.0
2.0
0.001
0.9
0.999
1e-8
1
Output: 4.999
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\nm_prev = float(input())\nv_prev = float(input())\ng = float(input())\nalpha = float(input())\nbeta1 = float(input())\nbeta2 = float(input())\neps = float(input())\nt = int(input())\n# Compute Adam update\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Compute the **mini-batch gradient** for f(x) = x² on a mini-batch of samples.

gradient = (2 / batch_size) × Σ xᵢ

(This is the gradient of the average squared loss where target is 0.)

Read `batch_size`, then `batch_size` sample values. Print the mini-batch gradient rounded to 6 decimal places.

Example:
```
Input:
3
2
4
6
Output: 8.0
```
MD,
                'starter_code'        => "batch_size = int(input())\nsamples = [float(input()) for _ in range(batch_size)]\n# Compute mini-batch gradient\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Apply **learning rate decay** (step decay): after every `step_size` epochs, multiply learning rate by `decay`.

Given initial `lr`, `decay`, `step_size`, and `epoch` number, compute the current learning rate.

lr_current = lr × decay^(floor(epoch / step_size))

Read `lr`, `decay`, `step_size`, `epoch`. Print the current lr rounded to 6 decimal places.

Example:
```
Input:
0.1
0.5
10
25
Output: 0.025
```
MD,
                'starter_code'        => "import math\n\nlr = float(input())\ndecay = float(input())\nstep_size = int(input())\nepoch = int(input())\n# Compute decayed learning rate\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 13.5: Convexity, Local vs Global Minima (Q22–Q26)
            // ═══════════════════════════════════════════════════════════════

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Check if a function is **convex** on a set of points using the definition:

A function is convex if for all pairs (x1, x2): f((x1+x2)/2) ≤ (f(x1)+f(x2))/2

Given `n` (x, f(x)) pairs, check all pairs. Print `convex` if the condition holds for all pairs (with tolerance 1e-6), otherwise `not convex`.

Read `n`, then `n` pairs of `x f(x)`.

Example:
```
Input:
3
0 0
1 1
2 4
Output: convex
```
MD,
                'starter_code'        => "n = int(input())\npoints = []\nfor _ in range(n):\n    x, fx = map(float, input().split())\n    points.append((x, fx))\n# Check convexity\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Given a list of local minima values and the global minimum value, determine how many **local (non-global) minima** exist.

Read the global minimum value, then `n`, then `n` local minima values. Print the count of local minima that are strictly greater than the global minimum.

Example:
```
Input:
1.0
4
1.0
2.5
3.0
1.0
Output: 2
```
MD,
                'starter_code'        => "global_min = float(input())\nn = int(input())\nlocal_mins = [float(input()) for _ in range(n)]\n# Count non-global local minima\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Evaluate f(x) = x⁴ − 4x² + x at several points and find the **global minimum** value among them.

Read `n` values of x. Print the global minimum f(x) value rounded to 4 decimal places.

Example:
```
Input:
5
-2
-1
0
1
2
Output: -3.0
```
MD,
                'starter_code'        => "n = int(input())\nxs = [float(input()) for _ in range(n)]\n# Evaluate f(x) = x^4 - 4x^2 + x at each x, print global min\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Classify a point on f(x) = x⁴ − 4x² as a **local minimum**, **local maximum**, or **saddle point**.

f'(x) = 4x³ − 8x
f''(x) = 12x² − 8

Use the second derivative test.

Read `x`. Print `local minimum`, `local maximum`, or `saddle point` (if f''(x) = 0, print `inconclusive`).

Example:
```
Input:
1.414
Output: local minimum
```
MD,
                'starter_code'        => "import math\n\nx = float(input())\n# Classify using second derivative of f(x) = x^4 - 4x^2\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Given a 2D function f(x,y) = x² − y², determine if the origin (0,0) is a **saddle point**.

The Hessian at (0,0) for f(x,y) = x² − y² is H = [[2, 0], [0, -2]].

A saddle point exists if the Hessian has both positive and negative eigenvalues.

For this function, eigenvalues are 2 and -2, so it is always a saddle point.

Read two floats `x` and `y` (ignored — the answer is always the same). Print `saddle point`.

Example:
```
Input:
0.0
0.0
Output: saddle point
```
MD,
                'starter_code'        => "x = float(input())\ny = float(input())\n# f(x,y) = x^2 - y^2 always has a saddle point at origin\nprint('saddle point')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 13.6: Constrained Optimization (Q27–Q31)
            // ═══════════════════════════════════════════════════════════════

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Check if a point (x, y) satisfies the **equality constraint** g(x, y) = x + y − c = 0.

Read `x`, `y`, `c`. Print `feasible` if |x + y - c| < 1e-6, otherwise `infeasible`.

Example:
```
Input:
3.0
2.0
5.0
Output: feasible
```
MD,
                'starter_code'        => "x = float(input())\ny = float(input())\nc = float(input())\n# Check equality constraint\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Check if a point (x, y) is **feasible** for a set of inequality constraints:
- x >= 0
- y >= 0
- x + y <= c

Read `x`, `y`, `c`. Print `feasible` or `infeasible`.

Example:
```
Input:
2.0
3.0
6.0
Output: feasible
```
MD,
                'starter_code'        => "x = float(input())\ny = float(input())\nc = float(input())\n# Check all constraints\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Compute the **Lagrangian** L(x, y, λ) = f(x, y) + λ × g(x, y) where f(x,y) = x² + y² and g(x,y) = x + y − 1.

Read `x`, `y`, `λ`. Print L rounded to 4 decimal places.

Example:
```
Input:
0.5
0.5
2.0
Output: 0.5
```
MD,
                'starter_code'        => "x = float(input())\ny = float(input())\nlambda_ = float(input())\n# Compute Lagrangian\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Solve the **constrained optimum** of f(x,y) = x² + y² subject to x + y = 1 using the Lagrange condition.

The solution is x* = y* = 0.5.

Verify by reading `x` and `y` and checking if both equal 0.5 (tolerance 1e-4). Print `optimal` or `not optimal`.

Example:
```
Input:
0.5
0.5
Output: optimal
```
MD,
                'starter_code'        => "x = float(input())\ny = float(input())\n# Check if (x, y) is the optimal point\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Check the **KKT condition** for a simple inequality constraint: the gradient of f must equal λ times the gradient of g, with λ >= 0.

Given ∇f = (df_dx, df_dy) and ∇g = (dg_dx, dg_dy) and λ:

KKT condition: df_dx = λ × dg_dx AND df_dy = λ × dg_dy AND λ >= 0

Read `df_dx`, `df_dy`, `dg_dx`, `dg_dy`, `lambda`. Print `KKT satisfied` or `KKT violated` (use tolerance 1e-6).

Example:
```
Input:
1.0
1.0
1.0
1.0
1.0
Output: KKT satisfied
```
MD,
                'starter_code'        => "df_dx = float(input())\ndf_dy = float(input())\ndg_dx = float(input())\ndg_dy = float(input())\nlambda_ = float(input())\n# Check KKT condition\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 13.7: Linear Programming (Q32–Q36)
            // ═══════════════════════════════════════════════════════════════

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Evaluate the **objective function** of a linear program: maximize c₁x₁ + c₂x₂.

Read `c1`, `c2`, `x1`, `x2`. Print the objective value rounded to 4 decimal places.

Example:
```
Input:
3.0
5.0
2.0
4.0
Output: 26.0
```
MD,
                'starter_code'        => "c1 = float(input())\nc2 = float(input())\nx1 = float(input())\nx2 = float(input())\n# Compute and print objective value\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Given a list of feasible **corner points** (vertices) of a linear program and the objective function coefficients (c1, c2), find the corner point that **maximizes** c1×x + c2×y.

Read `c1`, `c2`, then `n`, then `n` vertex pairs `x y`. Print the optimal `x` and `y` (rounded to 4 decimal places) on separate lines.

Example:
```
Input:
3
5
3
0 0
4 0
0 6
Output:
0.0
6.0
```
MD,
                'starter_code'        => "c1 = float(input())\nc2 = float(input())\nn = int(input())\nvertices = []\nfor _ in range(n):\n    x, y = map(float, input().split())\n    vertices.append((x, y))\n# Find and print optimal vertex\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Check if a solution (x, y) is **feasible** for the standard LP constraints:
- a11×x + a12×y ≤ b1
- a21×x + a22×y ≤ b2
- x ≥ 0
- y ≥ 0

Read `x`, `y`, `a11`, `a12`, `b1`, `a21`, `a22`, `b2`. Print `feasible` or `infeasible`.

Example:
```
Input:
2
3
1
1
6
2
1
8
Output: feasible
```
MD,
                'starter_code'        => "x = float(input())\ny = float(input())\na11 = float(input())\na12 = float(input())\nb1 = float(input())\na21 = float(input())\na22 = float(input())\nb2 = float(input())\n# Check all LP constraints\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Compute the **slack** for each constraint in a linear program.

Slack for constraint i = bᵢ − (aᵢ₁×x + aᵢ₂×y)

Read `x`, `y`, then `m` constraints (each as `a1 a2 b` on one line). Print each slack rounded to 4 decimal places, one per line.

Example:
```
Input:
2
3
2
1 1 6
2 1 8
Output:
1.0
-0.0
```
MD,
                'starter_code'        => "x = float(input())\ny = float(input())\nm = int(input())\nconstraints = []\nfor _ in range(m):\n    a1, a2, b = map(float, input().split())\n    constraints.append((a1, a2, b))\n# Compute and print slacks\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Classify a linear program as **bounded** or **unbounded** based on whether a finite maximum exists.

Given a maximization LP with objective coefficients `c1 c2` and constraints, scan `n` feasible vertices and check if the objective can grow infinitely (simulated by having an extremely large candidate objective value > 1e9).

Read `c1`, `c2`, then `n` vertex objective values. If any value > 1e9, print `unbounded`; otherwise print `bounded` and the maximum objective value rounded to 4 decimal places.

Example:
```
Input:
3
5
3
26.0
30.0
0.0
Output:
bounded
30.0
```
MD,
                'starter_code'        => "c1 = float(input())\nc2 = float(input())\nn = int(input())\nobj_vals = [float(input()) for _ in range(n)]\n# Check bounded or unbounded\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 13.8: Metaheuristics (Q37–Q41)
            // ═══════════════════════════════════════════════════════════════

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Simulate one step of a **random search**: accept a new solution if it is better than the current.

Read `current_value`, `candidate_value`. For minimization: print `accept` if candidate < current, otherwise `reject`. Also print the new current value.

Example:
```
Input:
10.0
8.0
Output:
accept
8.0
```
MD,
                'starter_code'        => "current = float(input())\ncandidate = float(input())\n# Accept or reject candidate\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Compute the **acceptance probability** in simulated annealing.

If the new solution is better (lower), accept with probability 1.0.
If worse, acceptance probability = exp(−Δ / T) where Δ = f_new − f_current.

Read `f_current`, `f_new`, `T`. Print the acceptance probability rounded to 6 decimal places.

Example:
```
Input:
10.0
12.0
5.0
Output: 0.670320
```
MD,
                'starter_code'        => "import math\n\nf_current = float(input())\nf_new = float(input())\nT = float(input())\n# Compute acceptance probability\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Apply **temperature cooling** in simulated annealing.

T_new = T × cooling_rate

Read `T`, `cooling_rate`, and `n` (number of cooling steps). Print T after each step, rounded to 6 decimal places, one per line.

Example:
```
Input:
100.0
0.9
3
Output:
90.0
81.0
72.9
```
MD,
                'starter_code'        => "T = float(input())\ncooling_rate = float(input())\nn = int(input())\n# Apply cooling n times and print T after each step\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Simulate **tournament selection** in a genetic algorithm.

Given `n` individuals (fitness values), select the best from a random tournament of size `k`. The tournament is provided as indices (0-based) into the fitness list.

For minimization: the best individual has the lowest fitness.

Read `n`, then `n` fitness values, then `k` tournament indices. Print the fitness of the winner rounded to 4 decimal places.

Example:
```
Input:
5
10
20
5
15
8
3
2
0
3
Output: 5.0
```
MD,
                'starter_code'        => "n = int(input())\nfitness = [float(input()) for _ in range(n)]\nk = int(input())\ntournament = [int(input()) for _ in range(k)]\n# Find and print fitness of tournament winner (min)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Simulate **uniform crossover** of two binary chromosomes.

For each gene position, select from parent A if the mask bit is 1, else from parent B.

Read chromosome length `n`, then `n` bits of parent A, then `n` bits of parent B, then `n` mask bits. Print the offspring chromosome (n bits, space-separated on one line).

Example:
```
Input:
4
1
0
1
0
0
1
0
1
1
0
1
0
Output: 1 1 1 1
```
MD,
                'starter_code'        => "n = int(input())\nA = [int(input()) for _ in range(n)]\nB = [int(input()) for _ in range(n)]\nmask = [int(input()) for _ in range(n)]\n# Apply uniform crossover\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 13.9: Hyperparameter Optimization (Q42–Q46)
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Perform **grid search** over a 1D hyperparameter grid and return the value with the best (lowest) score.

Read `n` hyperparameter values and `n` corresponding scores. Print the hyperparameter value with the lowest score (rounded to 4 decimal places).

Example:
```
Input:
4
0.001
0.01
0.1
1.0
0.95
0.80
0.72
0.88
Output: 0.1
```
MD,
                'starter_code'        => "n = int(input())\nparams = [float(input()) for _ in range(n)]\nscores = [float(input()) for _ in range(n)]\n# Find and print best hyperparameter\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Enumerate all combinations in a **2D grid search**.

Read `n1` values for parameter 1 (one per line), then `n2` values for parameter 2 (one per line). Print all combinations as `p1 p2` (space-separated), one per line, in order (p1 outer loop, p2 inner loop). Print values rounded to 4 decimal places.

Example:
```
Input:
2
0.1
1.0
2
10
100
Output:
0.1 10.0
0.1 100.0
1.0 10.0
1.0 100.0
```
MD,
                'starter_code'        => "n1 = int(input())\np1 = [float(input()) for _ in range(n1)]\nn2 = int(input())\np2 = [float(input()) for _ in range(n2)]\n# Print all combinations\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Count the **total number of evaluations** required for a grid search over multiple hyperparameters.

Read `k` (number of hyperparameters), then `k` integers representing the number of values per parameter. Print the total (product of all counts).

Example:
```
Input:
3
4
3
5
Output: 60
```
MD,
                'starter_code'        => "k = int(input())\ncounts = [int(input()) for _ in range(k)]\n# Compute and print total evaluations\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Given scores from hyperparameter trials, compute the **best, worst, and mean** score.

Read `n` scores. Print:
- `Best: X`
- `Worst: X`
- `Mean: X`

All rounded to 4 decimal places.

Example:
```
Input:
4
0.95
0.80
0.72
0.88
Output:
Best: 0.72
Worst: 0.95
Mean: 0.8375
```
(Assuming lower is better — best = minimum.)
MD,
                'starter_code'        => "n = int(input())\nscores = [float(input()) for _ in range(n)]\n# Compute and print best, worst, mean\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Compute the **expected improvement (EI)** — a simplified version.

EI = max(0, f_best − f_candidate)

Read `f_best` and `n` candidate values. Print the EI for each candidate, rounded to 4 decimal places, one per line.

Example:
```
Input:
0.8
3
0.75
0.85
0.80
Output:
0.05
0.0
0.0
```
MD,
                'starter_code'        => "f_best = float(input())\nn = int(input())\ncandidates = [float(input()) for _ in range(n)]\n# Compute and print EI for each candidate\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 13.10: Second-Order Methods (Q47–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Perform one step of **Newton's method** for root-finding on f(x) = x² − 2.

x_new = x − f(x) / f'(x) = x − (x² − 2) / (2x)

Read `x`. Print `x_new` rounded to 6 decimal places.

Example:
```
Input:
1.5
Output: 1.416667
```
MD,
                'starter_code'        => "x = float(input())\n# One Newton step on f(x) = x^2 - 2\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Run **Newton's method** for minimizing f(x) = x² − 2 until |x_new − x| < tolerance.

For minimization, Newton step: x_new = x − f'(x) / f''(x) = x − 2x / 2 = 0.

Read `x0` and `tolerance`. Print the final x rounded to 6 decimal places and number of steps.

Example:
```
Input:
5.0
1e-6
Output:
0.0
1
```
MD,
                'starter_code'        => "x = float(input())\ntol = float(input())\n# Newton's method for minimizing f(x) = x^2\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Compute the **Hessian** of f(x, y) = ax² + by² at a given point.

The Hessian is H = [[2a, 0], [0, 2b]].

Read `a` and `b`. Print the four elements of the Hessian matrix: H[0][0], H[0][1], H[1][0], H[1][1], each on a separate line.

Example:
```
Input:
3.0
5.0
Output:
6.0
0.0
0.0
10.0
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\n# Compute and print Hessian elements\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Compare **gradient descent** vs **Newton's method** convergence on f(x) = x².

- GD step: x_new = x − α × 2x
- Newton step: x_new = x − (2x)/(2) = 0 (one step to optimum)

Given `x0` and `α`, run GD for up to 1000 steps until |x| < tol. Count GD steps. Newton always takes 1 step.

Read `x0`, `α`, `tol`. Print:
```
GD steps: N
Newton steps: 1
```

Example:
```
Input:
10.0
0.1
0.01
Output:
GD steps: 23
Newton steps: 1
```
MD,
                'starter_code'        => "x0 = float(input())\nalpha = float(input())\ntol = float(input())\n# Count GD steps vs Newton steps\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
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

        // ── Q1: find minimum value and index ─────────────────────────────
        $seed(1, [
            ['input' => "5\n3\n1\n4\n1\n5",      'expected_output' => "1.0\n1",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10\n5\n20",           'expected_output' => "5.0\n1",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n-5\n-1\n-3\n-2",     'expected_output' => "-5.0\n0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n42",                  'expected_output' => "42.0\n0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: evaluate f(x) = x^2 - 4x + 4 ───────────────────────────
        $seed(2, [
            ['input' => "2.0",    'expected_output' => "0.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0",    'expected_output' => "4.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4.0",    'expected_output' => "4.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "-1.0",   'expected_output' => "9.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: find minimizer from (x, f(x)) pairs ──────────────────────
        $seed(3, [
            ['input' => "4\n0 4\n1 1\n2 0\n3 1",   'expected_output' => "2.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n-1 5\n0 0\n1 5",       'expected_output' => "0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n5.0 100\n10.0 50",     'expected_output' => "10.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 3\n2 1\n3 2",        'expected_output' => "2.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: classify optimization problem ────────────────────────────
        $seed(4, [
            ['input' => "minimize",    'expected_output' => "minimization",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "maximize",    'expected_output' => "maximization",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "minimize",    'expected_output' => "minimization",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "maximize",    'expected_output' => "maximization",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: improvement ───────────────────────────────────────────────
        $seed(5, [
            ['input' => "10.0\n7.5",    'expected_output' => "2.5\nimproved",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "5.0\n8.0",     'expected_output' => "-3.0\nno improvement",'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0\n3.0",     'expected_output' => "0.0\nno improvement", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "100.0\n0.0",   'expected_output' => "100.0\nimproved",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: numerical derivative of x^2 ──────────────────────────────
        $seed(6, [
            ['input' => "3.0\n0.001",    'expected_output' => "6.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n0.001",    'expected_output' => "0.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "-2.0\n0.0001", 'expected_output' => "-4.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5.0\n0.001",    'expected_output' => "10.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: gradient of x^2 + y^2 ────────────────────────────────────
        $seed(7, [
            ['input' => "2.0\n3.0\n0.001",    'expected_output' => "4.0\n6.0",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n0.0\n0.001",    'expected_output' => "0.0\n0.0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1.0\n2.0\n0.001",   'expected_output' => "-2.0\n4.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "5.0\n5.0\n0.001",    'expected_output' => "10.0\n10.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: critical point of x^3 - 3x ───────────────────────────────
        $seed(8, [
            ['input' => "1.0",     'expected_output' => "critical point",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0",     'expected_output' => "not a critical point",'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1.0",    'expected_output' => "critical point",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0",     'expected_output' => "not a critical point",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: second derivative test on x^3 - 3x ───────────────────────
        $seed(9, [
            ['input' => "1.0",     'expected_output' => "local minimum",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "-1.0",    'expected_output' => "local maximum",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0",     'expected_output' => "local minimum",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "-2.0",    'expected_output' => "local maximum",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: gradient magnitude ───────────────────────────────────────
        $seed(10, [
            ['input' => "2\n3.0\n4.0",       'expected_output' => "5.0",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.0\n0.0\n0.0",  'expected_output' => "0.0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1.0\n1.0",       'expected_output' => "1.4142",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n3.0\n4.0\n0.0",  'expected_output' => "5.0",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: one GD step on x^2 ──────────────────────────────────────
        $seed(11, [
            ['input' => "5.0\n0.1",     'expected_output' => "4.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "10.0\n0.05",   'expected_output' => "9.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "-4.0\n0.1",    'expected_output' => "-3.2",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n0.5",     'expected_output' => "0.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: GD n steps on x^2 ───────────────────────────────────────
        $seed(12, [
            ['input' => "10.0\n0.1\n3",   'expected_output' => "8.0\n6.4\n5.12",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n0.5\n2",    'expected_output' => "0.0\n0.0",                 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n0.2\n4",    'expected_output' => "3.0\n1.8\n1.08\n0.648",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.0\n0.1\n1",    'expected_output' => "1.6",                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: GD until convergence on x^2 ─────────────────────────────
        $seed(13, [
            ['input' => "10.0\n0.1\n0.01",   'expected_output' => "0.034868\n23",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n0.4\n0.001",   'expected_output' => "0.000935\n19",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n0.3\n0.01",    'expected_output' => "0.006706\n17",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.0\n0.2\n0.001",   'expected_output' => "0.000858\n18",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: GD convergence check ─────────────────────────────────────
        $seed(14, [
            ['input' => "0.3",    'expected_output' => "converges",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0",    'expected_output' => "diverges",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.49",   'expected_output' => "converges",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.5",    'expected_output' => "diverges",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: MSE gradient for linear regression (no intercept) ────────
        $seed(15, [
            ['input' => "3\n1\n2\n3\n2\n4\n6\n1.0",     'expected_output' => "0.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n2\n1\n2\n0.0",           'expected_output' => "-10.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3\n1\n2\n3\n2.0",     'expected_output' => "9.3333",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0\n1\n0\n1\n0.5",           'expected_output' => "-0.5",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: update w and b with GD ───────────────────────────────────
        $seed(16, [
            ['input' => "2\n1\n2\n2\n4\n0.0\n0.0\n0.1",   'expected_output' => "0.4\n0.2",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n2\n1\n2\n0.0\n0.0\n0.1",   'expected_output' => "0.5\n0.3",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3\n2\n4\n6\n1.0\n0.0\n0.01", 'expected_output' => "1.0\n0.0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n1\n2\n3\n5\n1.0\n0.0\n0.1",   'expected_output' => "1.2\n0.4",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: SGD step ─────────────────────────────────────────────────
        $seed(17, [
            ['input' => "5.0\n0.1\n8.0",     'expected_output' => "4.2",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "10.0\n0.01\n20.0",  'expected_output' => "9.8",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.1\n5.0",     'expected_output' => "-0.5",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "-3.0\n0.2\n-6.0",   'expected_output' => "-1.8",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: momentum update ──────────────────────────────────────────
        $seed(18, [
            ['input' => "5.0\n0.0\n10.0\n0.1\n0.9",    'expected_output' => "1.0\n4.9",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "5.0\n1.0\n10.0\n0.1\n0.9",    'expected_output' => "1.9\n4.81",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.0\n4.0\n0.5\n0.8",     'expected_output' => "0.8\n-0.4",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "10.0\n2.0\n6.0\n0.1\n0.9",    'expected_output' => "2.4\n9.76",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Adam update ─────────────────────────────────────────────
        $seed(19, [
            ['input' => "5.0\n0.0\n0.0\n2.0\n0.001\n0.9\n0.999\n1e-8\n1",    'expected_output' => "4.999",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n0.0\n0.0\n1.0\n0.001\n0.9\n0.999\n1e-8\n1",    'expected_output' => "-0.001",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0\n0.0\n0.0\n4.0\n0.01\n0.9\n0.999\n1e-8\n1",    'expected_output' => "9.99",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n0.0\n0.0\n2.0\n0.001\n0.9\n0.999\n1e-8\n1",    'expected_output' => "0.999",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: mini-batch gradient ──────────────────────────────────────
        $seed(20, [
            ['input' => "3\n2\n4\n6",      'expected_output' => "8.0",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3\n5",         'expected_output' => "8.0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4",   'expected_output' => "5.0",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n10",           'expected_output' => "20.0",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: learning rate decay ──────────────────────────────────────
        $seed(21, [
            ['input' => "0.1\n0.5\n10\n25",   'expected_output' => "0.025",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.1\n0.5\n10\n0",    'expected_output' => "0.1",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n0.1\n5\n10",    'expected_output' => "0.01",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.01\n0.9\n1\n3",    'expected_output' => "0.00729",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: convexity check ──────────────────────────────────────────
        $seed(22, [
            ['input' => "3\n0 0\n1 1\n2 4",      'expected_output' => "convex",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0 0\n1 -1\n2 0",     'expected_output' => "not convex",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 0\n1 1\n2 4\n3 9", 'expected_output' => "convex",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0 5\n1 3\n2 5",      'expected_output' => "convex",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: count non-global local minima ────────────────────────────
        $seed(23, [
            ['input' => "1.0\n4\n1.0\n2.5\n3.0\n1.0",   'expected_output' => "2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n3\n0.0\n0.0\n0.0",         'expected_output' => "0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n3\n5.0\n7.0\n9.0",         'expected_output' => "2",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n2\n1.0\n1.0",              'expected_output' => "0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: global minimum of x^4 - 4x^2 + x ────────────────────────
        $seed(24, [
            ['input' => "5\n-2\n-1\n0\n1\n2",    'expected_output' => "-3.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0\n1\n-1",           'expected_output' => "-2.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n2\n-2",              'expected_output' => "-3.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n-3\n-2\n2\n3",       'expected_output' => "-3.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: classify critical point of x^4 - 4x^2 ───────────────────
        $seed(25, [
            ['input' => "1.414",    'expected_output' => "local minimum",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "-1.414",   'expected_output' => "local minimum",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0",      'expected_output' => "local maximum",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.0",      'expected_output' => "local minimum",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: saddle point of x^2 - y^2 ───────────────────────────────
        $seed(26, [
            ['input' => "0.0\n0.0",   'expected_output' => "saddle point",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n1.0",   'expected_output' => "saddle point",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1.0\n0.0",  'expected_output' => "saddle point",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5.0\n-3.0",  'expected_output' => "saddle point",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: equality constraint feasibility ──────────────────────────
        $seed(27, [
            ['input' => "3.0\n2.0\n5.0",   'expected_output' => "feasible",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n1.0\n5.0",   'expected_output' => "infeasible",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.0\n0.0",   'expected_output' => "feasible",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.5\n2.5\n5.0",   'expected_output' => "feasible",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: LP inequality constraint feasibility ─────────────────────
        $seed(28, [
            ['input' => "2.0\n3.0\n6.0",   'expected_output' => "feasible",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "5.0\n3.0\n6.0",   'expected_output' => "infeasible",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.0\n5.0",   'expected_output' => "feasible",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "-1.0\n2.0\n6.0",  'expected_output' => "infeasible",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Lagrangian ───────────────────────────────────────────────
        $seed(29, [
            ['input' => "0.5\n0.5\n2.0",    'expected_output' => "0.5",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n0.0\n1.0",    'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n0.0\n5.0",    'expected_output' => "-5.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0\n1.0\n0.0",    'expected_output' => "2.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: constrained optimum verification ─────────────────────────
        $seed(30, [
            ['input' => "0.5\n0.5",    'expected_output' => "optimal",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.3\n0.7",    'expected_output' => "not optimal",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5001\n0.5", 'expected_output' => "not optimal",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.4999\n0.5001", 'expected_output' => "not optimal",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: KKT condition ────────────────────────────────────────────
        $seed(31, [
            ['input' => "1.0\n1.0\n1.0\n1.0\n1.0",     'expected_output' => "KKT satisfied",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n1.0\n1.0\n1.0\n1.0",     'expected_output' => "KKT violated",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n1.0\n1.0\n1.0\n-1.0",    'expected_output' => "KKT violated",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3.0\n6.0\n1.0\n2.0\n3.0",     'expected_output' => "KKT satisfied",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: LP objective value ───────────────────────────────────────
        $seed(32, [
            ['input' => "3.0\n5.0\n2.0\n4.0",   'expected_output' => "26.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n1.0\n3.0\n3.0",   'expected_output' => "6.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n3.0\n0.0\n5.0",   'expected_output' => "15.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4.0\n2.0\n3.0\n1.0",   'expected_output' => "14.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: optimal LP corner point ─────────────────────────────────
        $seed(33, [
            ['input' => "3\n5\n3\n0 0\n4 0\n0 6",       'expected_output' => "0.0\n6.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1\n3\n0 0\n5 0\n0 5",       'expected_output' => "5.0\n0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n3\n4\n0 0\n0 4\n3 0\n2 2",  'expected_output' => "2.0\n2.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n0\n2\n0 0\n10 0\n0 10",     'expected_output' => "10.0\n0.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: LP feasibility ───────────────────────────────────────────
        $seed(34, [
            ['input' => "2\n3\n1\n1\n6\n2\n1\n8",    'expected_output' => "feasible",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n3\n1\n1\n6\n2\n1\n8",    'expected_output' => "infeasible",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0\n1\n1\n6\n2\n1\n8",    'expected_output' => "feasible",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "-1\n2\n1\n1\n6\n2\n1\n8",   'expected_output' => "infeasible",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: LP slack ─────────────────────────────────────────────────
        $seed(35, [
            ['input' => "2\n3\n2\n1 1 6\n2 1 8",       'expected_output' => "1.0\n-0.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n0\n2\n1 1 6\n2 1 8",       'expected_output' => "6.0\n8.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n3\n2\n2 1 10\n1 2 10",     'expected_output' => "1.0\n1.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1\n1\n1 1 5",              'expected_output' => "3.0",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: LP bounded or unbounded ─────────────────────────────────
        $seed(36, [
            ['input' => "3\n5\n3\n26.0\n30.0\n0.0",          'expected_output' => "bounded\n30.0",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1\n2\n1e10\n100.0",              'expected_output' => "unbounded",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n3\n4\n15.0\n12.0\n20.0\n0.0",   'expected_output' => "bounded\n20.0",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1\n1\n2e9\n5.0",                'expected_output' => "unbounded",               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: random search accept/reject ──────────────────────────────
        $seed(37, [
            ['input' => "10.0\n8.0",    'expected_output' => "accept\n8.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "10.0\n12.0",   'expected_output' => "reject\n10.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n5.0",     'expected_output' => "reject\n5.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "100.0\n0.1",   'expected_output' => "accept\n0.1",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: SA acceptance probability ────────────────────────────────
        $seed(38, [
            ['input' => "10.0\n12.0\n5.0",   'expected_output' => "0.670320",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "10.0\n8.0\n5.0",    'expected_output' => "1.0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "5.0\n7.0\n1.0",     'expected_output' => "0.135335",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5.0\n5.0\n1.0",     'expected_output' => "1.0",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: temperature cooling ──────────────────────────────────────
        $seed(39, [
            ['input' => "100.0\n0.9\n3",   'expected_output' => "90.0\n81.0\n72.9",           'is_hidden' => false, 'order_index' => 1],
            ['input' => "1000.0\n0.5\n2",  'expected_output' => "500.0\n250.0",                'is_hidden' => false, 'order_index' => 2],
            ['input' => "50.0\n0.8\n4",    'expected_output' => "40.0\n32.0\n25.6\n20.48",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "10.0\n1.0\n3",    'expected_output' => "10.0\n10.0\n10.0",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: tournament selection ─────────────────────────────────────
        $seed(40, [
            ['input' => "5\n10\n20\n5\n15\n8\n3\n2\n0\n3",   'expected_output' => "5.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n2\n0\n2",               'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n9\n3\n7\n1\n2\n1\n3",            'expected_output' => "1.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n5\n5\n5\n5\n5\n3\n0\n1\n2",      'expected_output' => "5.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: uniform crossover ────────────────────────────────────────
        $seed(41, [
            ['input' => "4\n1\n0\n1\n0\n0\n1\n0\n1\n1\n0\n1\n0",   'expected_output' => "1 1 1 1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n1\n1\n0\n0\n0\n1\n0\n1",            'expected_output' => "1 0 1",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0\n0\n0\n0\n1\n1\n1\n1\n0\n0\n0\n0",   'expected_output' => "0 0 0 0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n1\n0\n0\n0\n0\n1\n1\n1\n1\n1\n0",   'expected_output' => "1 1 1 0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: 1D grid search ───────────────────────────────────────────
        $seed(42, [
            ['input' => "4\n0.001\n0.01\n0.1\n1.0\n0.95\n0.80\n0.72\n0.88",   'expected_output' => "0.1",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n0.5\n0.3\n0.7",                           'expected_output' => "2.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n10\n100\n0.9\n0.8",                                'expected_output' => "100.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0.5\n1.0\n1.5\n0.2\n0.2\n0.2",                    'expected_output' => "0.5",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: 2D grid search combinations ─────────────────────────────
        $seed(43, [
            ['input' => "2\n0.1\n1.0\n2\n10\n100",   'expected_output' => "0.1 10.0\n0.1 100.0\n1.0 10.0\n1.0 100.0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n2\n2\n3\n4",          'expected_output' => "1.0 3.0\n1.0 4.0\n2.0 3.0\n2.0 4.0",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5\n3\n1\n2\n3",          'expected_output' => "5.0 1.0\n5.0 2.0\n5.0 3.0",                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2\n3\n1\n4",          'expected_output' => "1.0 4.0\n2.0 4.0\n3.0 4.0",                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: total grid search evaluations ────────────────────────────
        $seed(44, [
            ['input' => "3\n4\n3\n5",   'expected_output' => "60",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10\n10",    'expected_output' => "100",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n50",        'expected_output' => "50",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2\n3\n4\n5",'expected_output' => "120",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: best, worst, mean score ─────────────────────────────────
        $seed(45, [
            ['input' => "4\n0.95\n0.80\n0.72\n0.88",   'expected_output' => "Best: 0.72\nWorst: 0.95\nMean: 0.8375",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1.0\n1.0\n1.0",            'expected_output' => "Best: 1.0\nWorst: 1.0\nMean: 1.0",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0.5\n0.3",                 'expected_output' => "Best: 0.3\nWorst: 0.5\nMean: 0.4",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n0.1\n0.2\n0.3\n0.4\n0.5", 'expected_output' => "Best: 0.1\nWorst: 0.5\nMean: 0.3",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: expected improvement ─────────────────────────────────────
        $seed(46, [
            ['input' => "0.8\n3\n0.75\n0.85\n0.80",    'expected_output' => "0.05\n0.0\n0.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n2\n0.4\n0.6",            'expected_output' => "0.1\n0.0",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.9\n3\n0.9\n0.8\n1.0",       'expected_output' => "0.0\n0.1\n0.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.3\n2\n0.1\n0.5",            'expected_output' => "0.2\n0.0",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: one Newton step on x^2 - 2 ─────────────────────────────
        $seed(47, [
            ['input' => "1.5",    'expected_output' => "1.416667",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0",    'expected_output' => "1.5",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0",    'expected_output' => "2.166667",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0",    'expected_output' => "1.5",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Newton's method minimization of x^2 ─────────────────────
        $seed(48, [
            ['input' => "5.0\n1e-6",    'expected_output' => "0.0\n1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "10.0\n1e-6",   'expected_output' => "0.0\n1",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "-3.0\n1e-6",   'expected_output' => "0.0\n1",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.001\n1e-6",  'expected_output' => "0.0\n1",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Hessian of ax^2 + by^2 ──────────────────────────────────
        $seed(49, [
            ['input' => "3.0\n5.0",   'expected_output' => "6.0\n0.0\n0.0\n10.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n1.0",   'expected_output' => "2.0\n0.0\n0.0\n2.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n2.0",   'expected_output' => "1.0\n0.0\n0.0\n4.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4.0\n4.0",   'expected_output' => "8.0\n0.0\n0.0\n8.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: GD vs Newton steps ───────────────────────────────────────
        $seed(50, [
            ['input' => "10.0\n0.1\n0.01",   'expected_output' => "GD steps: 23\nNewton steps: 1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5.0\n0.2\n0.01",    'expected_output' => "GD steps: 17\nNewton steps: 1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n0.4\n0.001",   'expected_output' => "GD steps: 19\nNewton steps: 1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2.0\n0.2\n0.001",   'expected_output' => "GD steps: 18\nNewton steps: 1",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 13 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}