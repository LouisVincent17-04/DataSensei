<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module13ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 13 — Introduction to Optimization Techniques (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Optimization Techniques',
            'description'           => 'Test your very first knowledge of optimization — what it means, why we use it, and the basic vocabulary behind finding the best solution. No math background assumed!',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 13,
        ]);

        $this->command->info("Seeding 50 newbie-friendly optimization questions...");

        $qaData = [

            // ── WHAT IS OPTIMIZATION ──────────────────────────────────────
            [
                'q' => 'What does "optimization" mean in mathematics and data science?',
                'opts' => [
                    ['Making a computer run faster', false],
                    ['Finding the best possible solution by minimizing or maximizing an objective', true],
                    ['Cleaning messy data', false],
                    ['Drawing charts and graphs', false],
                ],
            ],
            [
                'q' => 'In optimization, the function we want to minimize or maximize is called the:',
                'opts' => [
                    ['Input function', false],
                    ['Objective function', true],
                    ['Error function', false],
                    ['Output variable', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of an optimization problem?',
                'opts' => [
                    ['Counting how many students are in a class', false],
                    ['Finding the shortest driving route between two cities', true],
                    ['Listing all items in a grocery store', false],
                    ['Reading a book', false],
                ],
            ],
            [
                'q' => 'When we want to make something as SMALL as possible (like cost or error), we call it:',
                'opts' => [
                    ['Maximization', false],
                    ['Minimization', true],
                    ['Normalization', false],
                    ['Regularization', false],
                ],
            ],
            [
                'q' => 'When we want to make something as LARGE as possible (like profit or accuracy), we call it:',
                'opts' => [
                    ['Minimization', false],
                    ['Differentiation', false],
                    ['Maximization', true],
                    ['Summation', false],
                ],
            ],
            [
                'q' => 'The values we adjust to find the best solution are called:',
                'opts' => [
                    ['Constants', false],
                    ['Decision variables', true],
                    ['Residuals', false],
                    ['Coefficients only', false],
                ],
            ],
            [
                'q' => 'A constraint in an optimization problem is:',
                'opts' => [
                    ['The answer to the problem', false],
                    ['A limit or rule that the solution must satisfy', true],
                    ['The objective function value', false],
                    ['A type of chart', false],
                ],
            ],
            [
                'q' => 'You want to maximize profit while spending no more than ₱10,000 on ingredients. The ₱10,000 limit is called a:',
                'opts' => [
                    ['Decision variable', false],
                    ['Constraint', true],
                    ['Gradient', false],
                    ['Loss value', false],
                ],
            ],

            // ── CALCULUS BASICS ───────────────────────────────────────────
            [
                'q' => 'A derivative tells us:',
                'opts' => [
                    ['The area under a curve', false],
                    ['How fast a function is changing at a given point', true],
                    ['The maximum value of a function', false],
                    ['The average of all values', false],
                ],
            ],
            [
                'q' => 'If the derivative of a function at a point equals zero, that point is called a:',
                'opts' => [
                    ['Random point', false],
                    ['Critical point', true],
                    ['Starting point', false],
                    ['Break point', false],
                ],
            ],
            [
                'q' => 'The derivative of f(x) = x² is:',
                'opts' => [
                    ['x²', false],
                    ['2x', true],
                    ['2', false],
                    ['x', false],
                ],
            ],
            [
                'q' => 'The derivative of f(x) = 5 (a constant) is:',
                'opts' => [
                    ['5', false],
                    ['1', false],
                    ['0', true],
                    ['10', false],
                ],
            ],
            [
                'q' => 'At a minimum of a smooth function, the derivative (slope) is:',
                'opts' => [
                    ['Positive', false],
                    ['Negative', false],
                    ['Zero', true],
                    ['Undefined', false],
                ],
            ],
            [
                'q' => 'A "gradient" is the generalization of a derivative for functions with:',
                'opts' => [
                    ['Only one variable', false],
                    ['Multiple variables', true],
                    ['Only integers', false],
                    ['Discrete values', false],
                ],
            ],
            [
                'q' => 'If the gradient of a function at a point points "uphill," moving in the OPPOSITE direction will:',
                'opts' => [
                    ['Increase the function value', false],
                    ['Decrease the function value (move downhill)', true],
                    ['Keep the function value the same', false],
                    ['Cause an error', false],
                ],
            ],
            [
                'q' => 'The derivative of f(x) = 3x is:',
                'opts' => [
                    ['3x', false],
                    ['x', false],
                    ['3', true],
                    ['0', false],
                ],
            ],

            // ── GRADIENT DESCENT ─────────────────────────────────────────
            [
                'q' => 'Gradient descent is an algorithm used to:',
                'opts' => [
                    ['Sort data alphabetically', false],
                    ['Iteratively find the minimum of a function by moving in the direction of steepest descent', true],
                    ['Draw gradient lines on a graph', false],
                    ['Calculate the maximum of a function only', false],
                ],
            ],
            [
                'q' => 'In gradient descent, the "learning rate" controls:',
                'opts' => [
                    ['How smart the algorithm is', false],
                    ['How large each step is when updating the parameters', true],
                    ['The number of variables in the function', false],
                    ['The final answer of the optimization', false],
                ],
            ],
            [
                'q' => 'If the learning rate is too large in gradient descent, the algorithm may:',
                'opts' => [
                    ['Converge too slowly', false],
                    ['Overshoot the minimum and never settle', true],
                    ['Find the minimum instantly', false],
                    ['Always find the global minimum', false],
                ],
            ],
            [
                'q' => 'If the learning rate is too small in gradient descent, the algorithm will:',
                'opts' => [
                    ['Converge very quickly', false],
                    ['Take a very long time to reach the minimum', true],
                    ['Diverge away from the minimum', false],
                    ['Skip the minimum entirely', false],
                ],
            ],
            [
                'q' => 'Gradient descent stops (converges) when:',
                'opts' => [
                    ['The function value reaches zero exactly', false],
                    ['The updates become very small or the gradient is close to zero', true],
                    ['All data has been processed once', false],
                    ['The learning rate is set to 1', false],
                ],
            ],
            [
                'q' => 'In machine learning, gradient descent is most commonly used to:',
                'opts' => [
                    ['Load data into a model', false],
                    ['Minimize the loss (error) function during training', true],
                    ['Split data into training and test sets', false],
                    ['Visualize the data', false],
                ],
            ],

            // ── LOCAL vs GLOBAL MINIMA ────────────────────────────────────
            [
                'q' => 'A "global minimum" is:',
                'opts' => [
                    ['Any point where the slope is zero', false],
                    ['The lowest point across the entire function', true],
                    ['A point that is lower than nearby points but not the lowest overall', false],
                    ['The starting point of gradient descent', false],
                ],
            ],
            [
                'q' => 'A "local minimum" is:',
                'opts' => [
                    ['The absolute lowest value of the function', false],
                    ['A point lower than its immediate neighbors but NOT the global lowest', true],
                    ['The starting point of the algorithm', false],
                    ['The point where the learning rate equals zero', false],
                ],
            ],
            [
                'q' => 'A "saddle point" is a critical point where:',
                'opts' => [
                    ['The function reaches its global maximum', false],
                    ['The gradient is zero but it is neither a minimum nor a maximum', true],
                    ['The function is undefined', false],
                    ['The learning rate must be reset', false],
                ],
            ],
            [
                'q' => 'A convex function has:',
                'opts' => [
                    ['Many local minima that are hard to find', false],
                    ['Exactly one global minimum with no other local minima', true],
                    ['No minimum at all', false],
                    ['A saddle point at the center', false],
                ],
            ],
            [
                'q' => 'For a convex function, gradient descent with a small enough learning rate is guaranteed to:',
                'opts' => [
                    ['Get stuck in a local minimum', false],
                    ['Converge to the global minimum', true],
                    ['Diverge away from the solution', false],
                    ['Oscillate forever', false],
                ],
            ],

            // ── TYPES OF GRADIENT DESCENT ─────────────────────────────────
            [
                'q' => 'Batch Gradient Descent uses _____ to compute each parameter update.',
                'opts' => [
                    ['One random data point', false],
                    ['A small random subset of data', false],
                    ['All training data', true],
                    ['Only the most recent data point', false],
                ],
            ],
            [
                'q' => 'Stochastic Gradient Descent (SGD) uses _____ per update.',
                'opts' => [
                    ['All training data', false],
                    ['One random training example', true],
                    ['Half of the training data', false],
                    ['The average of all gradients', false],
                ],
            ],
            [
                'q' => 'Mini-Batch Gradient Descent uses:',
                'opts' => [
                    ['All the data at once', false],
                    ['Exactly one data point', false],
                    ['A small random subset (batch) of the data', true],
                    ['Data sorted by label', false],
                ],
            ],
            [
                'q' => 'Which gradient descent variant is most commonly used in modern deep learning?',
                'opts' => [
                    ['Full Batch Gradient Descent', false],
                    ['Mini-Batch Gradient Descent', true],
                    ['Pure Stochastic Gradient Descent', false],
                    ['No gradient descent is used in deep learning', false],
                ],
            ],

            // ── CONSTRAINED OPTIMIZATION ──────────────────────────────────
            [
                'q' => 'An "unconstrained" optimization problem means:',
                'opts' => [
                    ['There are no decision variables', false],
                    ['The variables can take any value with no restrictions', true],
                    ['The objective function is linear', false],
                    ['Only one variable is optimized', false],
                ],
            ],
            [
                'q' => 'A "constrained" optimization problem means:',
                'opts' => [
                    ['The objective function has only one term', false],
                    ['The solution must satisfy one or more conditions (constraints)', true],
                    ['Gradient descent cannot be used', false],
                    ['The problem has no solution', false],
                ],
            ],
            [
                'q' => 'Lagrange multipliers are used to solve optimization problems that have:',
                'opts' => [
                    ['No objective function', false],
                    ['Equality constraints', true],
                    ['Only integer variables', false],
                    ['Multiple objective functions', false],
                ],
            ],
            [
                'q' => 'In linear programming, both the objective function and all constraints must be:',
                'opts' => [
                    ['Quadratic', false],
                    ['Exponential', false],
                    ['Linear (straight-line relationships)', true],
                    ['Trigonometric', false],
                ],
            ],
            [
                'q' => 'The "feasible region" in a constrained optimization problem is:',
                'opts' => [
                    ['The point where the objective function is minimized', false],
                    ['The set of all solutions that satisfy every constraint', true],
                    ['The gradient of the objective function', false],
                    ['The starting point for gradient descent', false],
                ],
            ],

            // ── LINEAR PROGRAMMING ────────────────────────────────────────
            [
                'q' => 'The Simplex Method is an algorithm used to solve:',
                'opts' => [
                    ['Deep learning problems', false],
                    ['Linear programming problems', true],
                    ['Nonlinear differential equations', false],
                    ['Clustering problems', false],
                ],
            ],
            [
                'q' => 'In linear programming, the optimal solution (if it exists) always occurs at:',
                'opts' => [
                    ['The center of the feasible region', false],
                    ['A corner (vertex) of the feasible region', true],
                    ['The point with the largest variable values', false],
                    ['Any random point in the feasible region', false],
                ],
            ],
            [
                'q' => 'SciPy in Python can be used to solve linear programming problems using:',
                'opts' => [
                    ['scipy.stats.linregress', false],
                    ['scipy.optimize.linprog', true],
                    ['scipy.linalg.solve', false],
                    ['scipy.signal.find_peaks', false],
                ],
            ],

            // ── METAHEURISTICS ────────────────────────────────────────────
            [
                'q' => 'A "metaheuristic" is best described as:',
                'opts' => [
                    ['An exact algorithm that always finds the global optimum', false],
                    ['A high-level strategy that guides a search for good solutions, especially for complex problems', true],
                    ['A calculus-based optimization method', false],
                    ['A method only used for linear problems', false],
                ],
            ],
            [
                'q' => 'Genetic Algorithms are inspired by:',
                'opts' => [
                    ['Physics and thermodynamics', false],
                    ['Ant colony behavior', false],
                    ['Biological evolution and natural selection', true],
                    ['Neural networks in the brain', false],
                ],
            ],
            [
                'q' => 'Simulated Annealing is inspired by:',
                'opts' => [
                    ['The movement of electrons', false],
                    ['The slow cooling of metals to reduce defects', true],
                    ['Human decision-making', false],
                    ['Swarm behavior of bees', false],
                ],
            ],
            [
                'q' => 'Metaheuristics are especially useful when:',
                'opts' => [
                    ['The problem is simple and convex', false],
                    ['An exact formula for the solution exists', false],
                    ['The problem is too complex or large for exact methods', true],
                    ['The objective function is linear', false],
                ],
            ],

            // ── HYPERPARAMETER OPTIMIZATION ───────────────────────────────
            [
                'q' => 'In machine learning, "hyperparameters" are:',
                'opts' => [
                    ['Parameters learned automatically from training data', false],
                    ['Settings defined before training that control how the model learns', true],
                    ['The weights of a neural network', false],
                    ['The rows in a dataset', false],
                ],
            ],
            [
                'q' => 'Grid Search for hyperparameter tuning works by:',
                'opts' => [
                    ['Randomly guessing hyperparameter values', false],
                    ['Exhaustively trying every combination in a predefined grid', true],
                    ['Using gradient descent to find optimal hyperparameters', false],
                    ['Letting the model choose its own hyperparameters', false],
                ],
            ],
            [
                'q' => 'Random Search for hyperparameter tuning randomly samples:',
                'opts' => [
                    ['Data points from the training set', false],
                    ['Hyperparameter combinations from a defined search space', true],
                    ['Gradient directions during training', false],
                    ['Features from the dataset', false],
                ],
            ],
            [
                'q' => 'Bayesian Optimization for hyperparameter tuning is smarter than Grid Search because it:',
                'opts' => [
                    ['Always finds the exact best hyperparameters', false],
                    ['Uses past evaluation results to choose the next most promising hyperparameter set', true],
                    ['Runs faster by using less data', false],
                    ['Requires no prior knowledge of the search space', false],
                ],
            ],

            // ── SECOND-ORDER METHODS ──────────────────────────────────────
            [
                'q' => 'Newton\'s Method for optimization uses both the first and second derivatives. The second derivative is also called the:',
                'opts' => [
                    ['Gradient', false],
                    ['Hessian', true],
                    ['Jacobian', false],
                    ['Lagrangian', false],
                ],
            ],
            [
                'q' => 'Compared to gradient descent (first-order), Newton\'s Method (second-order) typically:',
                'opts' => [
                    ['Takes more iterations to converge', false],
                    ['Converges in fewer iterations but is more expensive per step', true],
                    ['Is always slower in practice', false],
                    ['Cannot find minima', false],
                ],
            ],
            [
                'q' => 'L-BFGS is a popular optimization algorithm used in machine learning. The "L" stands for:',
                'opts' => [
                    ['Linear', false],
                    ['Large-scale', false],
                    ['Limited-memory', true],
                    ['Logarithmic', false],
                ],
            ],

            // ── GENERAL REVIEW ────────────────────────────────────────────
            [
                'q' => 'Which optimizer is most commonly associated with training deep neural networks today?',
                'opts' => [
                    ['The Simplex Method', false],
                    ['Adam (Adaptive Moment Estimation)', true],
                    ['Simulated Annealing', false],
                    ['Newton\'s Method', false],
                ],
            ],
            [
                'q' => 'The Adam optimizer is an extension of gradient descent that:',
                'opts' => [
                    ['Uses second-order derivatives only', false],
                    ['Adapts the learning rate for each parameter using estimates of first and second moments of gradients', true],
                    ['Only works for linear problems', false],
                    ['Requires no learning rate setting', false],
                ],
            ],
            [
                'q' => 'Momentum in gradient descent helps the optimizer:',
                'opts' => [
                    ['Restart from the beginning when stuck', false],
                    ['Accumulate velocity in directions of persistent gradient to speed up convergence', true],
                    ['Reduce the number of parameters', false],
                    ['Increase the loss function', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 13 — Introduction to Optimization Techniques (Newbie).");
    }
}