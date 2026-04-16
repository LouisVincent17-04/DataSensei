<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module13ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 13 — Introduction to Optimization Techniques (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Optimization Techniques',
            'description'           => 'Test your analytical understanding of optimization — derivatives, gradient descent mechanics, convexity, and basic constrained problems. Light calculations and tracing required.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 700,
            'order_index'           => 13,
        ]);

        $this->command->info("Seeding University Student optimization questions...");

        $qaData = [

            // ── CALCULUS REVIEW ───────────────────────────────────────────
            [
                'q' => 'Find the derivative of f(x) = 4x³ − 2x² + 7x − 5.',
                'opts' => [
                    ['12x² − 4x + 7', true],
                    ['4x² − 2x + 7', false],
                    ['12x² − 4x', false],
                    ['12x³ − 4x² + 7', false],
                ],
            ],
            [
                'q' => 'For f(x) = x² − 6x + 9, find the critical point by setting f\'(x) = 0.',
                'opts' => [
                    ['x = 9', false],
                    ['x = 6', false],
                    ['x = 3', true],
                    ['x = 0', false],
                ],
            ],
            [
                'q' => 'For f(x) = x² − 6x + 9, is the critical point x = 3 a minimum or maximum?\n\nHint: check the second derivative f\'\'(x).',
                'opts' => [
                    ['Maximum, because f\'\'(3) = 2 > 0', false],
                    ['Minimum, because f\'\'(3) = 2 > 0', true],
                    ['Saddle point, because f\'\'(3) = 0', false],
                    ['Maximum, because f\'\'(3) < 0', false],
                ],
            ],
            [
                'q' => 'Find the gradient of f(x, y) = x² + 3y².\n\n∇f = (∂f/∂x, ∂f/∂y)',
                'opts' => [
                    ['(2x, 3y)', false],
                    ['(x², 3y²)', false],
                    ['(2x, 6y)', true],
                    ['(2, 6)', false],
                ],
            ],
            [
                'q' => 'At the point (1, 2), the gradient of f(x, y) = x² + 3y² equals:',
                'opts' => [
                    ['(1, 6)', false],
                    ['(2, 12)', true],
                    ['(2, 6)', false],
                    ['(1, 2)', false],
                ],
            ],
            [
                'q' => 'The gradient ∇f always points in the direction of:',
                'opts' => [
                    ['Steepest descent (downhill)', false],
                    ['Steepest ascent (uphill)', true],
                    ['The nearest local minimum', false],
                    ['Zero', false],
                ],
            ],
            [
                'q' => 'The second derivative test: if f\'\'(x) > 0 at a critical point, the point is a:',
                'opts' => [
                    ['Local maximum', false],
                    ['Saddle point', false],
                    ['Local minimum', true],
                    ['Global maximum', false],
                ],
            ],
            [
                'q' => 'For f(x) = −x² + 4x − 3, the critical point is at x = 2. Since f\'\'(x) = −2 < 0, this point is a:',
                'opts' => [
                    ['Local minimum', false],
                    ['Saddle point', false],
                    ['Local maximum', true],
                    ['Inflection point only', false],
                ],
            ],

            // ── GRADIENT DESCENT MECHANICS ────────────────────────────────
            [
                'q' => 'The gradient descent update rule is:\n\n  θ_new = θ_old − α · ∇f(θ_old)\n\nWhat does α represent?',
                'opts' => [
                    ['The gradient direction', false],
                    ['The learning rate (step size)', true],
                    ['The current function value', false],
                    ['The number of iterations', false],
                ],
            ],
            [
                'q' => 'Starting at x = 4, with f\'(x) = 2x and learning rate α = 0.1:\n\nx_new = x − α · f\'(x)\n\nWhat is x_new?',
                'opts' => [
                    ['3.2', true],
                    ['4.8', false],
                    ['3.6', false],
                    ['3.0', false],
                ],
            ],
            [
                'q' => 'After one more step from x = 3.2 (using f\'(x) = 2x, α = 0.1):\n\nx_new = 3.2 − 0.1 × 2(3.2)',
                'opts' => [
                    ['2.56', true],
                    ['3.0', false],
                    ['2.88', false],
                    ['2.4', false],
                ],
            ],
            [
                'q' => 'For f(x) = (x − 3)², gradient descent will converge toward:',
                'opts' => [
                    ['x = 0', false],
                    ['x = 3 (the global minimum)', true],
                    ['x = 6', false],
                    ['x = 9', false],
                ],
            ],
            [
                'q' => 'Learning rate α = 2.0 is used on f(x) = x². Starting at x = 1:\n\nx_new = 1 − 2.0 × 2(1) = 1 − 4 = −3\n\nWhat is likely happening?',
                'opts' => [
                    ['The algorithm is converging quickly', false],
                    ['The algorithm is diverging — overshooting the minimum', true],
                    ['The algorithm found the minimum at x = −3', false],
                    ['The algorithm is moving toward the maximum', false],
                ],
            ],

            // ── SGD, MINI-BATCH, MOMENTUM, ADAM ───────────────────────────
            [
                'q' => 'Compared to Batch Gradient Descent, Stochastic Gradient Descent (SGD) is:',
                'opts' => [
                    ['Smoother in its update path', false],
                    ['Noisier but can escape local minima more easily', true],
                    ['Always slower', false],
                    ['Uses second-order information', false],
                ],
            ],
            [
                'q' => 'Mini-Batch Gradient Descent with batch size = 1 is equivalent to:',
                'opts' => [
                    ['Batch Gradient Descent', false],
                    ['Stochastic Gradient Descent (SGD)', true],
                    ['Newton\'s Method', false],
                    ['Adam optimizer', false],
                ],
            ],
            [
                'q' => 'Momentum adds a fraction of the previous update to the current one. The update rule is:\n\n  v_new = β·v_old + α·∇f\n  θ_new = θ_old − v_new\n\nWhat does a high β (close to 1) mean?',
                'opts' => [
                    ['Previous gradients have very little influence', false],
                    ['Previous gradients contribute strongly — the optimizer has high momentum', true],
                    ['The learning rate is effectively increased to 1', false],
                    ['The gradient is ignored', false],
                ],
            ],
            [
                'q' => 'The Adam optimizer combines which two ideas?',
                'opts' => [
                    ['Momentum and RMSProp (adaptive learning rates)', true],
                    ['Simulated Annealing and Gradient Descent', false],
                    ['Newton\'s Method and Grid Search', false],
                    ['Batch Gradient Descent and Bayesian Optimization', false],
                ],
            ],
            [
                'q' => 'In Adam, the parameter β₁ ≈ 0.9 controls the decay of the:\n\n  m_t = β₁·m_{t-1} + (1−β₁)·g_t',
                'opts' => [
                    ['Second moment (variance of gradients)', false],
                    ['Learning rate schedule', false],
                    ['First moment (mean of gradients)', true],
                    ['Weight decay regularization', false],
                ],
            ],
            [
                'q' => 'Which optimizer is known for working well "out of the box" with minimal learning rate tuning?',
                'opts' => [
                    ['Batch Gradient Descent', false],
                    ['Adam', true],
                    ['The Simplex Method', false],
                    ['Simulated Annealing', false],
                ],
            ],

            // ── CONVEXITY ─────────────────────────────────────────────────
            [
                'q' => 'Which of the following functions is convex?',
                'opts' => [
                    ['f(x) = −x²', false],
                    ['f(x) = x²', true],
                    ['f(x) = sin(x)', false],
                    ['f(x) = x³', false],
                ],
            ],
            [
                'q' => 'A function f is convex if for any two points x₁ and x₂:\n\n  f(λx₁ + (1−λ)x₂) ≤ λf(x₁) + (1−λ)f(x₂)  for λ ∈ [0,1]\n\nThis means the function lies _____ the chord connecting any two points.',
                'opts' => [
                    ['Above', false],
                    ['Below or on', true],
                    ['Exactly on', false],
                    ['To the left of', false],
                ],
            ],
            [
                'q' => 'A loss landscape in deep learning is generally:',
                'opts' => [
                    ['Perfectly convex with one global minimum', false],
                    ['Non-convex with many local minima and saddle points', true],
                    ['Always flat (no minima)', false],
                    ['A straight line', false],
                ],
            ],
            [
                'q' => 'For a convex function, every local minimum is also:',
                'opts' => [
                    ['A saddle point', false],
                    ['A local maximum', false],
                    ['The global minimum', true],
                    ['A critical point but not a minimum', false],
                ],
            ],
            [
                'q' => 'A saddle point in 2D has the property that it is:\n\n  - A minimum in one direction\n  - A maximum in another direction\n\nWhich function has a saddle point at the origin?',
                'opts' => [
                    ['f(x, y) = x² + y²', false],
                    ['f(x, y) = x² − y²', true],
                    ['f(x, y) = x² + 2y²', false],
                    ['f(x, y) = −x² − y²', false],
                ],
            ],

            // ── CONSTRAINED OPTIMIZATION / LAGRANGE ───────────────────────
            [
                'q' => 'The method of Lagrange multipliers converts a constrained problem:\n\n  Minimize f(x, y) subject to g(x, y) = 0\n\ninto which condition?',
                'opts' => [
                    ['f(x, y) = 0', false],
                    ['∇f = λ·∇g', true],
                    ['f(x, y) = g(x, y)', false],
                    ['∇f = 0 and ∇g = 0 separately', false],
                ],
            ],
            [
                'q' => 'You want to minimize f(x, y) = x² + y² subject to x + y = 4.\n\nSetting up Lagrange conditions:\n  2x = λ,  2y = λ,  x + y = 4\n\nWhat are x and y?',
                'opts' => [
                    ['x = 1, y = 3', false],
                    ['x = 2, y = 2', true],
                    ['x = 0, y = 4', false],
                    ['x = 4, y = 0', false],
                ],
            ],
            [
                'q' => 'KKT (Karush-Kuhn-Tucker) conditions extend Lagrange multipliers to handle:',
                'opts' => [
                    ['Only equality constraints', false],
                    ['Both equality and inequality constraints', true],
                    ['Only minimization problems', false],
                    ['Problems with no constraints', false],
                ],
            ],
            [
                'q' => 'In a KKT solution, the complementary slackness condition states:\n\n  μᵢ · gᵢ(x) = 0\n\nThis means for each inequality constraint, either:',
                'opts' => [
                    ['Both μᵢ > 0 and gᵢ(x) > 0 must hold', false],
                    ['The multiplier μᵢ = 0 OR the constraint is active (gᵢ(x) = 0)', true],
                    ['All constraints must be active simultaneously', false],
                    ['The multiplier equals the gradient', false],
                ],
            ],

            // ── LINEAR PROGRAMMING ────────────────────────────────────────
            [
                'q' => 'A bakery maximizes profit:\n  Maximize: 3x + 5y\n  Subject to: x + 2y ≤ 12, x ≥ 0, y ≥ 0\n\nThe feasible region corner points are (0,0), (12,0), (0,6).\nWhich point gives maximum profit?',
                'opts' => [
                    ['(0, 0): profit = 0', false],
                    ['(12, 0): profit = 36', false],
                    ['(0, 6): profit = 30', false],
                    ['(0, 6): profit = 30 — wait, check (12, 0): 3(12)+5(0) = 36', true],
                ],
            ],
            [
                'q' => 'Linear programming assumes the objective function and all constraints are:',
                'opts' => [
                    ['Quadratic', false],
                    ['Linear (no squared or product terms)', true],
                    ['Exponential', false],
                    ['Probabilistic', false],
                ],
            ],
            [
                'q' => 'In the Simplex Method, the algorithm moves from one _____ of the feasible region to another.',
                'opts' => [
                    ['Interior point', false],
                    ['Random point', false],
                    ['Vertex (corner point)', true],
                    ['Center point', false],
                ],
            ],
            [
                'q' => 'scipy.optimize.linprog minimizes by default. To maximize 3x + 5y using linprog, you minimize:',
                'opts' => [
                    ['3x + 5y', false],
                    ['−3x − 5y', true],
                    ['1/(3x + 5y)', false],
                    ['5x + 3y', false],
                ],
            ],

            // ── METAHEURISTICS ────────────────────────────────────────────
            [
                'q' => 'In a Genetic Algorithm, "crossover" refers to:',
                'opts' => [
                    ['Randomly flipping bits in a solution', false],
                    ['Combining parts of two parent solutions to create offspring', true],
                    ['Evaluating how good a solution is', false],
                    ['Eliminating the worst solutions', false],
                ],
            ],
            [
                'q' => 'In a Genetic Algorithm, "mutation" refers to:',
                'opts' => [
                    ['Combining two parent solutions', false],
                    ['A small random change to a solution, maintaining diversity', true],
                    ['Selecting the best solutions for reproduction', false],
                    ['Evaluating the fitness function', false],
                ],
            ],
            [
                'q' => 'In Simulated Annealing, the "temperature" parameter controls:',
                'opts' => [
                    ['The learning rate of gradient descent', false],
                    ['The probability of accepting a worse solution (exploration vs exploitation)', true],
                    ['The number of solutions in the population', false],
                    ['The speed of the CPU', false],
                ],
            ],
            [
                'q' => 'As temperature decreases in Simulated Annealing, the algorithm:',
                'opts' => [
                    ['Becomes more willing to accept worse solutions', false],
                    ['Becomes less likely to accept worse solutions (more greedy)', true],
                    ['Restarts with a new random solution', false],
                    ['Converges to a random point', false],
                ],
            ],

            // ── HYPERPARAMETER OPTIMIZATION ───────────────────────────────
            [
                'q' => 'Grid Search over 4 learning rate values and 3 regularization values requires:\n\n  How many total model evaluations?',
                'opts' => [
                    ['4', false],
                    ['3', false],
                    ['7', false],
                    ['12', true],
                ],
            ],
            [
                'q' => 'Random Search over the same grid explores 12 combinations but samples them:',
                'opts' => [
                    ['In a fixed order from best to worst', false],
                    ['Randomly, potentially finding good solutions faster than exhaustive search', true],
                    ['Based on gradient information', false],
                    ['Only from the best-performing region', false],
                ],
            ],
            [
                'q' => 'Bayesian Optimization builds a _____ of the objective function to guide the search.',
                'opts' => [
                    ['Decision tree', false],
                    ['Probabilistic surrogate model (e.g., Gaussian Process)', true],
                    ['Linear regression model', false],
                    ['Confusion matrix', false],
                ],
            ],
            [
                'q' => 'The "acquisition function" in Bayesian Optimization decides:',
                'opts' => [
                    ['How to tune the learning rate', false],
                    ['Which hyperparameter configuration to evaluate next', true],
                    ['How many epochs to train', false],
                    ['Which features to remove', false],
                ],
            ],

            // ── SECOND-ORDER METHODS ──────────────────────────────────────
            [
                'q' => 'Newton\'s update rule for optimization is:\n\n  x_new = x_old − [f\'\'(x)]⁻¹ · f\'(x)\n\nFor f(x) = x², starting at x = 4:\n  f\'(4) = 8,  f\'\'(4) = 2\n\nWhat is x_new?',
                'opts' => [
                    ['4', false],
                    ['0', true],
                    ['2', false],
                    ['−4', false],
                ],
            ],
            [
                'q' => 'Why is Newton\'s Method computationally expensive for large neural networks?',
                'opts' => [
                    ['It requires computing and inverting the Hessian matrix, which is O(n³)', true],
                    ['It only works for one-dimensional functions', false],
                    ['It needs too many random restarts', false],
                    ['It does not use gradient information', false],
                ],
            ],
            [
                'q' => 'BFGS is a "quasi-Newton" method because it:',
                'opts' => [
                    ['Uses the exact Hessian matrix', false],
                    ['Approximates the Hessian using gradient information, avoiding its direct computation', true],
                    ['Only works for linear problems', false],
                    ['Applies Newton\'s method to only one variable at a time', false],
                ],
            ],
            [
                'q' => 'L-BFGS is preferred over BFGS for large-scale problems because it:',
                'opts' => [
                    ['Uses the full Hessian approximation', false],
                    ['Stores only a limited number of past gradient vectors instead of the full Hessian approximation', true],
                    ['Ignores second-order information completely', false],
                    ['Uses random restarts to avoid local minima', false],
                ],
            ],

            // ── APPLIED INTERPRETATION ────────────────────────────────────
            [
                'q' => 'A neural network\'s training loss decreases very slowly over 100 epochs. The most likely cause is:',
                'opts' => [
                    ['The learning rate is too high', false],
                    ['The learning rate is too low', true],
                    ['The model has too many layers', false],
                    ['The batch size is too large', false],
                ],
            ],
            [
                'q' => 'A loss curve oscillates wildly up and down during training without decreasing overall. This suggests:',
                'opts' => [
                    ['The learning rate is too small', false],
                    ['The learning rate is too large', true],
                    ['The model is underfitting', false],
                    ['The batch size is too small only', false],
                ],
            ],
            [
                'q' => 'Which scenario benefits MOST from using a constrained optimization approach rather than unconstrained?',
                'opts' => [
                    ['Fitting a regression line to data with no restrictions', false],
                    ['Maximizing profit when raw material supply and budget are limited', true],
                    ['Finding the derivative of a polynomial', false],
                    ['Choosing a learning rate for gradient descent', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 13 — Introduction to Optimization Techniques (University Student).");
    }
}