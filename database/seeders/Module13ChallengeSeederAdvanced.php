<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module13ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 13 — Introduction to Optimization Techniques (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Optimization Techniques',
            'description'           => 'Debug optimizer implementations, reason through convergence theory, analyze code-level optimizer configurations, and diagnose training failures. Code snippets and deeper mathematical reasoning required.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1100,
            'order_index'           => 13,
        ]);

        $this->command->info("Seeding Advanced optimization questions...");

        $qaData = [

            // ── GRADIENT DESCENT — ADVANCED CODE ─────────────────────────
            [
                'q' => "```python\ndef gradient_descent(f_prime, x0, alpha, n_iter):\n    x = x0\n    for _ in range(n_iter):\n        x = x - alpha * f_prime(x)\n    return x\n\nresult = gradient_descent(lambda x: 2*x, x0=10, alpha=0.9, n_iter=100)\nprint(result)\n```\n\nWith α = 0.9 and f'(x) = 2x, the update is x_new = x − 1.8x = −0.8x.\nWhat will `result` be after 100 iterations?",
                'opts' => [
                    ['Approximately 0 (converged to the minimum)', false],
                    ['Alternates near zero but converges since |−0.8| < 1', true],
                    ['Diverges to infinity', false],
                    ['Exactly 0 after 2 iterations', false],
                ],
            ],
            [
                'q' => "```python\ndef gradient_descent(f_prime, x0, alpha, n_iter):\n    x = x0\n    for _ in range(n_iter):\n        x = x - alpha * f_prime(x)\n    return x\n\nresult = gradient_descent(lambda x: 2*x, x0=10, alpha=1.1, n_iter=10)\nprint(result)\n```\n\nWith α = 1.1: x_new = x − 2.2x = −1.2x. After each step |x| grows by factor 1.2.\nWhat happens?",
                'opts' => [
                    ['Converges to 0', false],
                    ['Diverges — magnitude grows exponentially each iteration', true],
                    ['Oscillates symmetrically around 0 forever', false],
                    ['Converges slowly to the minimum', false],
                ],
            ],
            [
                'q' => "```python\nimport numpy as np\n\ndef sgd_step(X, y, w, lr):\n    idx = np.random.randint(0, len(X))\n    xi, yi = X[idx], y[idx]\n    grad = -2 * xi * (yi - xi @ w)\n    return w - lr * grad\n```\n\nThis function implements one step of SGD for a linear regression loss.\nWhat is the potential issue with this implementation in high-dimensional settings?",
                'opts' => [
                    ['It uses the wrong gradient formula', false],
                    ['It computes the gradient on a single sample, leading to high variance updates — convergence may be noisy', true],
                    ['The learning rate is not used correctly', false],
                    ['It will always converge in one step', false],
                ],
            ],
            [
                'q' => "```python\nfor epoch in range(100):\n    for X_batch, y_batch in dataloader:\n        optimizer.zero_grad()\n        loss = criterion(model(X_batch), y_batch)\n        loss.backward()\n        optimizer.step()\n```\n\nWhat happens if `optimizer.zero_grad()` is removed?",
                'opts' => [
                    ['Training becomes faster', false],
                    ['Gradients accumulate across batches, corrupting the update direction', true],
                    ['The model trains on only the first batch', false],
                    ['The loss function changes', false],
                ],
            ],

            // ── ADAM — ADVANCED ───────────────────────────────────────────
            [
                'q' => "```python\noptimizer = torch.optim.Adam(\n    model.parameters(),\n    lr=1e-3,\n    betas=(0.9, 0.999),\n    eps=1e-8,\n    weight_decay=1e-4\n)\n```\n\nThe `weight_decay=1e-4` parameter adds which term to the loss function?",
                'opts' => [
                    ['L1 regularization (absolute weights)', false],
                    ['L2 regularization (squared weights) — also called weight decay', true],
                    ['Dropout', false],
                    ['Gradient clipping', false],
                ],
            ],
            [
                'q' => "```python\n# AdamW update rule (decoupled weight decay):\n# θ_t = θ_{t-1} - lr * (m̂_t / (√v̂_t + ε) + λ·θ_{t-1})\n\n# vs Adam with L2 regularization:\n# gradient becomes: g_t = g_t + λ·θ_{t-1}\n# then Adam update applied to modified gradient\n```\n\nAdamW decouples weight decay from gradient adaptation. The key advantage over Adam + L2 is:",
                'opts' => [
                    ['AdamW is faster to compute per step', false],
                    ['AdamW applies weight decay uniformly; Adam+L2 scales decay by the adaptive learning rate, distorting regularization', true],
                    ['AdamW eliminates the need for the learning rate', false],
                    ['AdamW always converges in fewer epochs', false],
                ],
            ],
            [
                'q' => "Adam with β₂ = 0.999 and ε = 1e-8.\n\nIn early training (t=1), if gradient g₁ = 0.001 is very small:\n  v₁ = 0.999·0 + 0.001·(0.001)² = 1e-9\n  v̂₁ = 1e-9 / (1 − 0.999) = 1e-6\n  step ∝ lr / (√(1e-6) + 1e-8) ≈ lr / 1e-3 = 1000·lr\n\nThis means for very small gradients early in training, Adam takes:",
                'opts' => [
                    ['Very small steps', false],
                    ['Disproportionately large steps (learning rate amplification)', true],
                    ['No steps at all', false],
                    ['Steps equal to the gradient', false],
                ],
            ],

            // ── CONVEXITY & HESSIAN ───────────────────────────────────────
            [
                'q' => "For f(x, y) = x² − 2xy + y²:\n\nHessian H = [[2, -2], [-2, 2]]\n\nCompute the eigenvalues of H.\ndet(H − λI) = (2−λ)² − 4 = 0",
                'opts' => [
                    ['λ = 0 and λ = 4', true],
                    ['λ = 2 and λ = 2', false],
                    ['λ = 1 and λ = 3', false],
                    ['λ = −2 and λ = 2', false],
                ],
            ],
            [
                'q' => "Since the Hessian of f(x, y) = x² − 2xy + y² has eigenvalues 0 and 4 (one zero eigenvalue), the function is:",
                'opts' => [
                    ['Strictly convex', false],
                    ['Convex but not strictly convex (positive semi-definite Hessian)', true],
                    ['Non-convex', false],
                    ['Concave', false],
                ],
            ],
            [
                'q' => "```python\nimport numpy as np\nfrom numpy.linalg import eigvals\n\nH = np.array([[6, 2], [2, 4]])\neigs = eigvals(H)\nprint(eigs)  # [7.24, 2.76]\nprint(np.cond(H))  # condition number ≈ 2.62\n```\n\nA condition number of 2.62 is relatively small. This implies gradient descent will:",
                'opts' => [
                    ['Zig-zag severely and converge slowly', false],
                    ['Converge reasonably well without severe oscillation', true],
                    ['Diverge regardless of learning rate', false],
                    ['Require second-order methods to converge', false],
                ],
            ],
            [
                'q' => "```python\nH = np.array([[1000, 0], [0, 1]])\nprint(np.cond(H))  # 1000.0\n```\n\nA condition number of 1000 means gradient descent with a fixed learning rate will:",
                'opts' => [
                    ['Converge quickly in both directions', false],
                    ['Require α ≤ 1/1000 = 0.001 for stability, but will zig-zag along the narrow valley', true],
                    ['Always diverge', false],
                    ['Converge in one Newton step', false],
                ],
            ],

            // ── CONSTRAINED OPTIMIZATION — ADVANCED ──────────────────────
            [
                'q' => "```python\nfrom scipy.optimize import minimize\n\ndef objective(x):\n    return x[0]**2 + x[1]**2\n\nconstraints = {'type': 'eq', 'fun': lambda x: x[0] + x[1] - 4}\nbounds = [(0, None), (0, None)]\n\nres = minimize(objective, x0=[1, 1],\n               method='SLSQP',\n               constraints=constraints,\n               bounds=bounds)\nprint(res.x)\n```\n\nWhat does SLSQP stand for and what class of problems does it solve?",
                'opts' => [
                    ['Stochastic Least Squares Quadratic Programming — stochastic problems', false],
                    ['Sequential Least Squares Programming — nonlinear constrained optimization', true],
                    ['Simplex Least Squares Quadratic Problem — linear programming only', false],
                    ['Scaled L2 Squared Quadratic Programming — unconstrained problems', false],
                ],
            ],
            [
                'q' => "```python\nres = minimize(objective, x0=[1, 1], method='SLSQP',\n               constraints=constraints, bounds=bounds)\nprint(res.x)    # [2. 2.]\nprint(res.fun)  # 8.0\n```\n\nIf the initial guess x0 is changed to [0, 0] and the constraint is x + y = 4, will the result change?",
                'opts' => [
                    ['Yes — different initial points always give different solutions', false],
                    ['No — the problem is convex (quadratic objective, linear constraint), so the global minimum is unique', true],
                    ['Yes — x0 = [0,0] violates the constraint so the solver will fail', false],
                    ['No — because SLSQP ignores the initial point', false],
                ],
            ],
            [
                'q' => "A support vector machine (SVM) training is a constrained quadratic program:\n\n  Minimize: (1/2)||w||²\n  Subject to: yᵢ(w·xᵢ + b) ≥ 1  for all i\n\nThis is solved using which mathematical framework?",
                'opts' => [
                    ['Grid Search', false],
                    ['Gradient descent with no constraints', false],
                    ['KKT conditions and Lagrangian duality', true],
                    ['Simulated Annealing', false],
                ],
            ],

            // ── LINEAR PROGRAMMING — ADVANCED ─────────────────────────────
            [
                'q' => "```python\nfrom scipy.optimize import linprog\n\nc = [-5, -4, -3]\nA_ub = [[6, 4, 2], [1, 2, 1.5]]\nb_ub = [240, 60]\nbounds = [(0,None), (0,None), (0,None)]\n\nres = linprog(c, A_ub=A_ub, b_ub=b_ub, bounds=bounds)\nprint(res.x)\nprint(-res.fun)\n```\n\nThis problem maximizes profit 5x₁ + 4x₂ + 3x₃ subject to two resource constraints.\nIf `res.status = 0`, this means:",
                'opts' => [
                    ['The problem is infeasible (no solution exists)', false],
                    ['The problem is unbounded', false],
                    ['Optimization terminated successfully', true],
                    ['The solver hit the iteration limit', false],
                ],
            ],
            [
                'q' => "In the dual of a linear program:\n\n  Primal: min c·x s.t. Ax ≥ b, x ≥ 0\n  Dual:   max b·y s.t. Aᵀy ≤ c, y ≥ 0\n\nStrong duality states that at optimality, primal objective = dual objective.\nIf the primal is infeasible, then the dual is:",
                'opts' => [
                    ['Also infeasible', false],
                    ['Unbounded or infeasible', true],
                    ['Always feasible', false],
                    ['Has the same optimal value', false],
                ],
            ],

            // ── METAHEURISTICS — ADVANCED ─────────────────────────────────
            [
                'q' => "```python\ndef genetic_algorithm(pop_size, n_gen, mutation_rate):\n    population = initialize_random(pop_size)\n    for gen in range(n_gen):\n        fitness = [evaluate(ind) for ind in population]\n        parents = tournament_select(population, fitness)\n        offspring = [crossover(p1, p2) for p1, p2 in parents]\n        population = [mutate(ind, mutation_rate) for ind in offspring]\n    return max(population, key=evaluate)\n```\n\nThis GA replaces the entire population each generation (generational model). A known risk is:",
                'opts' => [
                    ['It always finds the global optimum', false],
                    ['Loss of the best solution found so far — elitism (preserving top individuals) is not implemented', true],
                    ['It cannot handle continuous variables', false],
                    ['Tournament selection makes it deterministic', false],
                ],
            ],
            [
                'q' => "```python\nimport math, random\n\ndef simulated_annealing(f, x0, T0, cooling, n_iter):\n    x = x0\n    T = T0\n    for i in range(n_iter):\n        x_new = x + random.gauss(0, 1)\n        dE = f(x_new) - f(x)\n        if dE < 0 or random.random() < math.exp(-dE / T):\n            x = x_new\n        T *= cooling\n    return x\n```\n\nWith cooling = 0.99 and T0 = 100, after 1000 iterations:\nT_final = 100 × 0.99^1000 ≈ ?",
                'opts' => [
                    ['≈ 99', false],
                    ['≈ 4.3e-5 (near zero)', true],
                    ['≈ 36.8', false],
                    ['≈ 0.99', false],
                ],
            ],
            [
                'q' => "In the SA code above, if cooling = 1.0 (no cooling), the algorithm becomes equivalent to:",
                'opts' => [
                    ['Gradient descent', false],
                    ['A random walk — always at constant temperature, never converging', true],
                    ['Greedy hill-climbing', false],
                    ['Bayesian Optimization', false],
                ],
            ],

            // ── HYPERPARAMETER OPTIMIZATION — ADVANCED ────────────────────
            [
                'q' => "```python\nfrom sklearn.model_selection import RandomizedSearchCV\nfrom scipy.stats import loguniform\n\nparam_dist = {\n    'C': loguniform(1e-3, 1e3),\n    'gamma': loguniform(1e-4, 1e1)\n}\nrs = RandomizedSearchCV(SVC(), param_dist, n_iter=50, cv=5)\nrs.fit(X_train, y_train)\n```\n\nUsing `loguniform` instead of `uniform` for C and gamma is preferred because:",
                'opts' => [
                    ['loguniform is always faster', false],
                    ['C and gamma span many orders of magnitude; log-scale sampling ensures good coverage across the full range', true],
                    ['uniform distributions are not supported by scikit-learn', false],
                    ['loguniform prevents overfitting automatically', false],
                ],
            ],
            [
                'q' => "```python\nfrom skopt import BayesSearchCV\n\nopt = BayesSearchCV(\n    estimator=RandomForestClassifier(),\n    search_spaces={'n_estimators': (10, 500),\n                   'max_depth': (1, 20)},\n    n_iter=30,\n    cv=3\n)\n```\n\nAfter 10 iterations, Bayesian Optimization has found n_estimators≈300, max_depth≈10 as promising.\nFor iteration 11, the acquisition function will likely suggest a point:",
                'opts' => [
                    ['Randomly anywhere in the search space', false],
                    ['Near the current best or in high-uncertainty unexplored regions', true],
                    ['At the extremes of the search space only', false],
                    ['With the lowest possible n_estimators', false],
                ],
            ],
            [
                'q' => "```python\n# Hyperband algorithm: allocates more resources to promising configs\n# Round 1: 81 configs × 1 epoch each → keep top 1/3 = 27\n# Round 2: 27 configs × 3 epochs each → keep top 1/3 = 9\n# Round 3: 9 configs × 9 epochs each → keep top 1/3 = 3\n# Round 4: 3 configs × 27 epochs each → keep top 1/3 = 1\n```\n\nTotal epochs used by Hyperband in this example:",
                'opts' => [
                    ['81', false],
                    ['243 (81×3)', false],
                    ['243 total: 81 + 81 + 81 + 81 = 324', false],
                    ['81×1 + 27×3 + 9×9 + 3×27 = 81+81+81+81 = 324', true],
                ],
            ],

            // ── SECOND-ORDER — ADVANCED ────────────────────────────────────
            [
                'q' => "```python\nfrom scipy.optimize import minimize\n\nres = minimize(rosenbrock, x0=[-1, 1], method='Newton-CG',\n               jac=rosenbrock_grad,\n               hess=rosenbrock_hess)\nprint(res.x)   # [1. 1.]\nprint(res.nit) # 23 iterations\n```\n\nThe Rosenbrock function f(x,y) = (1−x)² + 100(y−x²)² is notoriously:\n\nIt is non-convex and has a narrow curved valley. Newton-CG converges here because:",
                'opts' => [
                    ['Newton-CG ignores the curvature', false],
                    ['Second-order information (Hessian) allows Newton-CG to follow the curved valley precisely', true],
                    ['The Rosenbrock function is actually linear', false],
                    ['Newton-CG is identical to gradient descent', false],
                ],
            ],
            [
                'q' => "```python\nres_gd = minimize(f, x0, method='CG', jac=grad)     # Conjugate Gradient\nres_lbfgs = minimize(f, x0, method='L-BFGS-B', jac=grad) # L-BFGS\n\nprint(res_gd.nit)    # 850 iterations\nprint(res_lbfgs.nit) # 42 iterations\n```\n\nL-BFGS converged in far fewer iterations because:",
                'opts' => [
                    ['L-BFGS uses a smaller learning rate', false],
                    ['L-BFGS approximates second-order curvature information, enabling much larger effective steps', true],
                    ['Conjugate Gradient uses random restarts', false],
                    ['L-BFGS works only for convex problems', false],
                ],
            ],

            // ── DEBUGGING OPTIMIZERS ──────────────────────────────────────
            [
                'q' => "```python\nlosses = []\nfor epoch in range(200):\n    loss = train_one_epoch(model, optimizer, dataloader)\n    losses.append(loss)\n\n# losses = [2.3, 2.31, 2.29, 2.30, 2.32, ...] (no decrease)\n```\n\nThe loss is stuck near the initial value (≈2.3 for cross-entropy with 10 classes = ln(10)).\nThe most likely cause is:",
                'opts' => [
                    ['The model is already fully trained', false],
                    ['Gradients may be vanishing or the learning rate is effectively zero — check for weight initialization or dead neurons', true],
                    ['The batch size is too large', false],
                    ['The loss function is wrong', false],
                ],
            ],
            [
                'q' => "```python\noptimizer = torch.optim.SGD(model.parameters(), lr=0.1,\n                             momentum=0.9, nesterov=True)\n```\n\nNesterov momentum differs from standard momentum because it:\n\n  Standard: v_t = β·v_{t-1} + ∇f(θ)\n  Nesterov: v_t = β·v_{t-1} + ∇f(θ − β·v_{t-1})",
                'opts' => [
                    ['Nesterov looks ahead to where the parameter will be, computing the gradient there', true],
                    ['Nesterov removes the momentum term entirely', false],
                    ['Nesterov uses a larger learning rate automatically', false],
                    ['Nesterov is equivalent to Adam with β₁ = 0.9', false],
                ],
            ],
            [
                'q' => "```python\n# Training curve shows:\n# Epochs 1-50:   loss drops steadily\n# Epochs 51-60:  loss suddenly spikes upward\n# Epochs 61+:    loss is erratic\n\n# Optimizer: SGD with lr=0.1 (constant)\n```\n\nThe most likely cause of the spike at epoch 51:",
                'opts' => [
                    ['The model ran out of memory', false],
                    ['The learning rate is too large for the flatter region near the minimum — the optimizer overshoots', true],
                    ['The validation set was accidentally used for training', false],
                    ['Batch normalization was applied incorrectly', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 13 — Introduction to Optimization Techniques (Advanced).");
    }
}