<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module13ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 13 — Introduction to Optimization Techniques (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Optimization Techniques',
            'description'           => 'Apply multi-step gradient calculations, trace optimizer behavior, interpret convergence diagnostics, and reason through constrained problem setups. Expect calculation traces and method-selection problems.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 900,
            'order_index'           => 13,
        ]);

        $this->command->info("Seeding Intermediate optimization questions...");

        $qaData = [

            // ── GRADIENT DESCENT TRACES ───────────────────────────────────
            [
                'q' => "Perform 3 steps of gradient descent on f(x) = x² starting at x₀ = 10, α = 0.1.\n\nUpdate rule: x_{t+1} = xₜ − α · f'(xₜ),  f'(x) = 2x\n\nWhat are x₁, x₂, x₃?",
                'opts' => [
                    ['x₁=8, x₂=6.4, x₃=5.12', true],
                    ['x₁=8, x₂=6, x₃=4', false],
                    ['x₁=9, x₂=8.1, x₃=7.29', false],
                    ['x₁=8, x₂=6.4, x₃=4.8', false],
                ],
            ],
            [
                'q' => "For f(x, y) = x² + 4y², starting at (3, 2), α = 0.1.\n\n∇f = (2x, 8y)\n\nCompute one gradient descent step: (x₁, y₁) = ?",
                'opts' => [
                    ['(2.4, 0.4)', true],
                    ['(2.6, 0.4)', false],
                    ['(2.4, 1.6)', false],
                    ['(3.6, 3.6)', false],
                ],
            ],
            [
                'q' => "Gradient descent on f(x) = (x − 5)²:\n  f'(x) = 2(x − 5)\n  α = 0.2, x₀ = 9\n\nAfter 2 steps, x₂ = ?",
                'opts' => [
                    ['6.44', false],
                    ['5.96', false],
                    ['6.2', false],
                    ['5.72', true],
                ],
            ],
            [
                'q' => "You run gradient descent on a loss function and record:\n  Epoch 1: loss = 10.0\n  Epoch 2: loss = 9.8\n  Epoch 3: loss = 9.78\n  Epoch 4: loss = 9.775\n\nWhat is the most likely diagnosis?",
                'opts' => [
                    ['The model is diverging', false],
                    ['The model is converging but the learning rate may be too small — progress is very slow', true],
                    ['The model has perfectly converged', false],
                    ['The batch size is too large', false],
                ],
            ],
            [
                'q' => "The learning rate schedule: α_t = α₀ / (1 + decay · t) is called:\n\nAt t=0: α = α₀; at large t: α → 0",
                'opts' => [
                    ['Momentum scheduling', false],
                    ['Step decay', false],
                    ['Time-based learning rate decay', true],
                    ['Warm restart scheduling', false],
                ],
            ],

            // ── MOMENTUM & ADAM TRACES ────────────────────────────────────
            [
                'q' => "Momentum update rule:\n  vₜ = β·vₜ₋₁ + α·gₜ\n  θₜ = θₜ₋₁ − vₜ\n\nβ = 0.9, α = 0.01, θ₀ = 5, v₀ = 0, g₁ = 2, g₂ = 2\n\nCompute θ₂.",
                'opts' => [
                    ['4.962', true],
                    ['4.98', false],
                    ['4.96', false],
                    ['4.942', false],
                ],
            ],
            [
                'q' => "Adam first-moment update:\n  mₜ = β₁·mₜ₋₁ + (1 − β₁)·gₜ\n\nβ₁ = 0.9, m₀ = 0, g₁ = 10\n\nCompute m₁.",
                'opts' => [
                    ['10', false],
                    ['9', false],
                    ['1', true],
                    ['0.9', false],
                ],
            ],
            [
                'q' => "Adam bias-corrected first moment:\n  m̂₁ = m₁ / (1 − β₁ᵗ)\n\nWith β₁ = 0.9, t = 1, m₁ = 1:\n\nm̂₁ = ?",
                'opts' => [
                    ['1', false],
                    ['10', true],
                    ['0.9', false],
                    ['0.1', false],
                ],
            ],
            [
                'q' => "Why does Adam use bias correction (dividing by 1 − βᵗ)?",
                'opts' => [
                    ['To prevent the learning rate from increasing', false],
                    ['To compensate for the moment estimates being close to zero at the start of training', true],
                    ['To add regularization to the weights', false],
                    ['To normalize the batch size', false],
                ],
            ],

            // ── CONVEXITY & MINIMA ────────────────────────────────────────
            [
                'q' => "For f(x, y) = 2x² + 3y² − 4x + 6y + 1:\n\nSet ∂f/∂x = 0 and ∂f/∂y = 0 to find the critical point.",
                'opts' => [
                    ['(x=2, y=−1)', false],
                    ['(x=1, y=−1)', true],
                    ['(x=−1, y=1)', false],
                    ['(x=2, y=1)', false],
                ],
            ],
            [
                'q' => "The Hessian matrix of f(x, y) = 2x² + 3y² is:\n\n  H = [[∂²f/∂x², ∂²f/∂x∂y],\n       [∂²f/∂y∂x, ∂²f/∂y²]]",
                'opts' => [
                    ['[[4, 0], [0, 6]]', true],
                    ['[[2, 3], [2, 3]]', false],
                    ['[[4, 6], [0, 0]]', false],
                    ['[[2, 0], [0, 3]]', false],
                ],
            ],
            [
                'q' => "A Hessian matrix H is positive definite if all eigenvalues are positive.\n\nFor H = [[4, 0], [0, 6]], eigenvalues are 4 and 6. This means the critical point is a:",
                'opts' => [
                    ['Local maximum', false],
                    ['Saddle point', false],
                    ['Local (and global) minimum', true],
                    ['Inflection point', false],
                ],
            ],
            [
                'q' => "A non-convex function f has a local minimum at x = 2 with f(2) = 5 and a global minimum at x = 7 with f(7) = 1.\n\nGradient descent starting at x = 3 will most likely converge to:",
                'opts' => [
                    ['x = 7, the global minimum', false],
                    ['x = 2, the nearby local minimum', true],
                    ['x = 0', false],
                    ['It will diverge', false],
                ],
            ],

            // ── CONSTRAINED OPTIMIZATION ──────────────────────────────────
            [
                'q' => "Minimize f(x, y) = x² + y²\nSubject to: 2x + y = 10\n\nLagrangian: L = x² + y² − λ(2x + y − 10)\n\nConditions:\n  ∂L/∂x = 2x − 2λ = 0  →  x = λ\n  ∂L/∂y = 2y − λ = 0   →  y = λ/2\n  2x + y = 10\n\nSolve for x and y.",
                'opts' => [
                    ['x = 5, y = 0', false],
                    ['x = 4, y = 2', true],
                    ['x = 2, y = 6', false],
                    ['x = 3, y = 4', false],
                ],
            ],
            [
                'q' => "A factory makes products A and B:\n  Profit: 4A + 3B\n  Constraints: A + B ≤ 10,  A ≤ 6,  B ≤ 8,  A,B ≥ 0\n\nCorner points: (0,0), (6,0), (6,4), (2,8), (0,8)\n\nMaximum profit occurs at:",
                'opts' => [
                    ['(0, 8): profit = 24', false],
                    ['(6, 0): profit = 24', false],
                    ['(6, 4): profit = 36', true],
                    ['(2, 8): profit = 32', false],
                ],
            ],
            [
                'q' => "KKT condition for the inequality constraint g(x) ≤ 0 requires μ ≥ 0.\n\nIf the constraint is inactive (g(x) < 0), what must μ equal?",
                'opts' => [
                    ['μ = 1', false],
                    ['μ > 0', false],
                    ['μ = 0', true],
                    ['μ < 0', false],
                ],
            ],
            [
                'q' => "In a linear program with 2 decision variables and 3 constraints, the feasible region is a polygon. The Simplex Method evaluates corner points. If there are 4 corner points, how many profit evaluations are needed in the worst case?",
                'opts' => [
                    ['2', false],
                    ['3', false],
                    ['4', true],
                    ['6', false],
                ],
            ],

            // ── LINEAR PROGRAMMING WITH SCIPY ─────────────────────────────
            [
                'q' => "```python\nfrom scipy.optimize import linprog\nc = [-3, -5]        # negate for maximization\nA = [[1, 2]]\nb = [12]\nres = linprog(c, A_ub=A, b_ub=b,\n              bounds=[(0, None), (0, None)])\nprint(-res.fun)\n```\n\nWhat does `-res.fun` compute?",
                'opts' => [
                    ['The minimum of 3x + 5y', false],
                    ['The maximum of 3x + 5y subject to x + 2y ≤ 12, x,y ≥ 0', true],
                    ['The gradient at the optimal point', false],
                    ['The constraint violation', false],
                ],
            ],
            [
                'q' => "In the scipy linprog call above, why are the coefficients negated (c = [-3, -5])?",
                'opts' => [
                    ['Because scipy uses a different unit system', false],
                    ['Because linprog always minimizes, so negating converts maximization to minimization', true],
                    ['To handle the inequality constraints properly', false],
                    ['Because the bounds require positive coefficients', false],
                ],
            ],
            [
                'q' => "```python\nres = linprog(c, A_ub=A, b_ub=b, bounds=[(0,None),(0,None)])\nprint(res.x)   # [12.  0.]\nprint(-res.fun) # 36.0\n```\n\nThe optimal solution x=12, y=0 gives max profit 36. Adding a new constraint x ≤ 8 would:",
                'opts' => [
                    ['Increase the maximum profit', false],
                    ['Leave the maximum profit unchanged', false],
                    ['Reduce the feasible region and potentially reduce the maximum profit', true],
                    ['Remove all feasible solutions', false],
                ],
            ],

            // ── METAHEURISTICS — INTERMEDIATE ────────────────────────────
            [
                'q' => "In a Genetic Algorithm with population size 100:\n  - Fitness evaluation: 1 ms per individual\n  - 500 generations\n\nApproximate total fitness evaluations and time:",
                'opts' => [
                    ['500 evaluations, 0.5 seconds', false],
                    ['50,000 evaluations, 50 seconds', true],
                    ['100 evaluations, 0.1 seconds', false],
                    ['500,000 evaluations, 500 seconds', false],
                ],
            ],
            [
                'q' => "Simulated Annealing accepts a worse solution with probability:\n\n  P = exp(−ΔE / T)\n\nIf ΔE = 5 (worse by 5) and T = 10:\n\nP = exp(−0.5) ≈ ?",
                'opts' => [
                    ['0.99', false],
                    ['0.61', true],
                    ['0.37', false],
                    ['0.05', false],
                ],
            ],
            [
                'q' => "At T = 1 (low temperature), same ΔE = 5:\n\nP = exp(−5/1) = exp(−5) ≈ 0.0067\n\nCompared to T = 10, the algorithm at T = 1 is:",
                'opts' => [
                    ['More likely to explore worse solutions', false],
                    ['Much less likely to accept worse solutions — more exploitative', true],
                    ['Equally likely to explore', false],
                    ['Guaranteed to find the global optimum', false],
                ],
            ],
            [
                'q' => "A Genetic Algorithm uses tournament selection, which:\n\n  - Picks k random individuals from the population\n  - Selects the best among them as a parent\n\nA higher tournament size k tends to:",
                'opts' => [
                    ['Increase diversity by selecting weaker individuals', false],
                    ['Increase selection pressure — fitter individuals are more likely to be chosen', true],
                    ['Reduce the number of generations needed', false],
                    ['Eliminate the need for mutation', false],
                ],
            ],

            // ── HYPERPARAMETER OPTIMIZATION — INTERMEDIATE ────────────────
            [
                'q' => "Grid Search over:\n  - learning_rate: [0.001, 0.01, 0.1]\n  - n_estimators: [50, 100, 200]\n  - max_depth: [3, 5]\n\nWith 5-fold cross-validation, how many total model fits are required?",
                'opts' => [
                    ['18', false],
                    ['30', false],
                    ['90', true],
                    ['45', false],
                ],
            ],
            [
                'q' => "The Expected Improvement (EI) acquisition function in Bayesian Optimization balances:\n\n  EI(x) = E[max(f(x) − f*, 0)]\n\nwhere f* is the current best value. EI is HIGH when:",
                'opts' => [
                    ['The surrogate model predicts a low mean AND low uncertainty', false],
                    ['The surrogate model predicts a high mean OR high uncertainty near the current best', true],
                    ['The learning rate is large', false],
                    ['The gradient is zero', false],
                ],
            ],
            [
                'q' => "After 20 Bayesian Optimization trials, the surrogate model has high uncertainty in region A and low predicted value in region B.\n\nThe acquisition function will likely suggest evaluating:",
                'opts' => [
                    ['Region B — already proven to be low-performing', false],
                    ['Region A — high uncertainty means potential for discovery', true],
                    ['A random region unrelated to A or B', false],
                    ['The point already evaluated with the best result', false],
                ],
            ],

            // ── SECOND-ORDER METHODS — INTERMEDIATE ──────────────────────
            [
                'q' => "Newton's Method in 1D:\n  x_new = x_old − f'(x) / f''(x)\n\nFor f(x) = x³ − 3x, starting at x = 2:\n  f'(2) = 3(4) − 3 = 9\n  f''(2) = 6(2) = 12\n\nx_new = ?",
                'opts' => [
                    ['1.25', true],
                    ['1.5', false],
                    ['2.75', false],
                    ['0.75', false],
                ],
            ],
            [
                'q' => "The condition number of the Hessian matrix affects gradient descent convergence.\n\nA poorly conditioned Hessian (very large condition number) causes gradient descent to:",
                'opts' => [
                    ['Converge in one step', false],
                    ['Zig-zag slowly in a narrow valley (slow convergence)', true],
                    ['Immediately find the global minimum', false],
                    ['Produce negative gradients only', false],
                ],
            ],
            [
                'q' => "L-BFGS stores the last m gradient vectors to approximate the inverse Hessian.\n\nTypical values of m are:",
                'opts' => [
                    ['m = 1000+', false],
                    ['m = 3 to 20', true],
                    ['m = 1 always', false],
                    ['m = the number of parameters n', false],
                ],
            ],
            [
                'q' => "For a neural network with 1 million parameters, why is the full Newton step (using exact Hessian) infeasible?",
                'opts' => [
                    ['The Hessian matrix would be 1,000,000 × 1,000,000 (10¹² entries) — storing and inverting it is impossible', true],
                    ['Newton\'s Method only works in 1D', false],
                    ['The gradient cannot be computed for 1M parameters', false],
                    ['Newton\'s Method requires labeled data', false],
                ],
            ],

            // ── APPLIED MULTI-STEP REASONING ──────────────────────────────
            [
                'q' => "You train a neural network and observe:\n  - Training loss: 0.05 (very low)\n  - Validation loss: 1.8 (very high)\n\nFrom an optimization perspective, the model has:",
                'opts' => [
                    ['Converged to the global minimum', false],
                    ['Overfitted — the optimizer found a minimum specific to training data that does not generalize', true],
                    ['A learning rate that is too small', false],
                    ['Not started training yet', false],
                ],
            ],
            [
                'q' => "Early stopping in neural network training is an optimization technique that:\n\nStops training when validation loss stops improving, acting as a form of:",
                'opts' => [
                    ['Faster convergence', false],
                    ['Implicit regularization, preventing the optimizer from overfitting the training set', true],
                    ['Learning rate increase', false],
                    ['Gradient clipping', false],
                ],
            ],
            [
                'q' => "Gradient clipping sets a maximum norm for the gradient:\n\n  if ||g|| > threshold: g = g × (threshold / ||g||)\n\nThis is most important for preventing:",
                'opts' => [
                    ['Vanishing gradients in shallow networks', false],
                    ['Exploding gradients, especially in RNNs and deep networks', true],
                    ['Overfitting on small datasets', false],
                    ['Slow convergence due to a small learning rate', false],
                ],
            ],
        ];

        foreach ($qaData as $data) {
            $question = ChallengeQuestion::create([
                'challenge_id'          => $challenge->id,
                'question_text'         => $data['q'],
                'challenge_category_id' => $category->id,
            ]);

            foreach ($data['opts'] as $opt) {
                ChallengeOption::create([
                    'challenge_question_id' => $question->id,
                    'option_text'           => $opt[0],
                    'is_correct'            => $opt[1],
                ]);
            }
        }

        $this->command->info("✅ Done! Questions seeded for Module 13 — Introduction to Optimization Techniques (Intermediate).");
    }
}