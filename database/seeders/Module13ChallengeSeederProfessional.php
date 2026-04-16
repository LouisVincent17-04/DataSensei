<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module13ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 13 — Introduction to Optimization Techniques (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Optimization Techniques',
            'description'           => 'Production-grade optimization: training instability diagnosis, optimizer selection under real constraints, large-scale LP pipelines, distributed training trade-offs, and edge cases that break standard methods.',
            'time_limit_seconds'    => 2100,
            'base_xp'               => 1400,
            'order_index'           => 13,
        ]);

        $this->command->info("Seeding Professional optimization questions...");

        $qaData = [

            // ── PRODUCTION TRAINING FAILURES ──────────────────────────────
            [
                'q' => "```python\n# Training a 20-layer ResNet from scratch\n# Optimizer: SGD, lr=0.1, no lr schedule\n# Observation after 50 epochs:\n#   train_loss = nan\n#   grad_norm logged at epoch 1: 0.8\n#   grad_norm logged at epoch 5: 847.3\n#   grad_norm logged at epoch 8: 4.2e6\n#   grad_norm at epoch 12: nan\n```\n\nThe training collapsed due to exploding gradients. Which combination of fixes is most appropriate?",
                'opts' => [
                    ['Increase learning rate and remove batch normalization', false],
                    ['Add gradient clipping (e.g. max_norm=1.0), use a warmup schedule, and ensure proper weight initialization (e.g. He init)', true],
                    ['Switch to Adam and increase batch size', false],
                    ['Reduce model depth to 2 layers', false],
                ],
            ],
            [
                'q' => "```python\n# Fine-tuning BERT on a classification task\n# Optimizer: Adam, lr=1e-3\n# Epoch 1: train_loss=0.65, val_loss=0.68\n# Epoch 2: train_loss=0.45, val_loss=1.82\n# Epoch 3: train_loss=0.21, val_loss=3.44\n```\n\nThe validation loss explodes while training loss drops — a clear sign of catastrophic forgetting / severe overfitting from an aggressive learning rate on a pre-trained model.\n\nThe correct fix for fine-tuning large pre-trained models is:",
                'opts' => [
                    ['Increase the learning rate further to escape the local minimum', false],
                    ['Use a much smaller learning rate (1e-5 to 5e-5) with layer-wise learning rate decay, reducing updates to pre-trained layers', true],
                    ['Remove dropout from the pre-trained layers', false],
                    ['Switch to full batch gradient descent', false],
                ],
            ],
            [
                'q' => "```python\nscheduler = torch.optim.lr_scheduler.OneCycleLR(\n    optimizer,\n    max_lr=0.1,\n    steps_per_epoch=len(dataloader),\n    epochs=30,\n    pct_start=0.3\n)\n```\n\nThe OneCycleLR policy uses a warmup phase (pct_start=0.3 = first 30% of training).\nDuring warmup, the learning rate:",
                'opts' => [
                    ['Decreases from max_lr to near zero', false],
                    ['Increases from a small value up to max_lr', true],
                    ['Stays constant at max_lr', false],
                    ['Is determined by the gradient norm', false],
                ],
            ],
            [
                'q' => "```python\n# Model: Transformer, 500M parameters\n# Hardware: 4× A100 GPUs\n# Observation: GPU utilization = 30%, training is bottlenecked\n# Gradient synchronization time: 8.2s per step\n# Forward+backward time: 1.1s per step\n```\n\nThe training bottleneck is gradient synchronization across GPUs.\nThe best optimization strategy is:",
                'opts' => [
                    ['Reduce the batch size to speed up each step', false],
                    ['Use gradient compression (e.g. 1-bit SGD) or increase gradient accumulation steps to amortize communication cost', true],
                    ['Switch from data parallelism to a single GPU', false],
                    ['Use a higher learning rate to compensate', false],
                ],
            ],

            // ── OPTIMIZER SELECTION TRADE-OFFS ────────────────────────────
            [
                'q' => "A production NLP team compares two optimizer configurations:\n\n  Config A: Adam, lr=3e-4, β=(0.9, 0.999)\n  Config B: SGD + Momentum, lr=0.1, momentum=0.9, with cosine annealing\n\nConfig B achieves 0.3% better final test accuracy but requires 3× more hyperparameter tuning trials.\n\nIn a tight 2-week deployment deadline, the recommended choice is:",
                'opts' => [
                    ['Config B — always optimize for final accuracy', false],
                    ['Config A — Adam\'s robustness to learning rate choice reduces tuning cost, meeting the deadline', true],
                    ['Neither — use L-BFGS for the fastest convergence', false],
                    ['Config B — SGD is always better than Adam', false],
                ],
            ],
            [
                'q' => "For a convex optimization problem (e.g., logistic regression with L2 regularization):\n\n  Option A: L-BFGS (quasi-Newton)\n  Option B: Adam\n  Option C: SGD with learning rate decay\n\nFor small-to-medium datasets (< 100K samples), the theoretically and empirically preferred optimizer is:",
                'opts' => [
                    ['Adam — it works best everywhere', false],
                    ['SGD — simplest is always best for convex problems', false],
                    ['L-BFGS — exploits convexity with second-order info for fast, accurate convergence', true],
                    ['Grid Search over all three', false],
                ],
            ],
            [
                'q' => "```python\n# Benchmark results on ImageNet training (ResNet-50, 90 epochs):\n# SGD+Momentum:  Top-1 = 76.1%  (industry standard)\n# Adam:          Top-1 = 74.3%  (−1.8%)\n# AdamW:         Top-1 = 75.5%  (−0.6%)\n```\n\nDespite Adam's widespread use, SGD+Momentum often outperforms it on image classification.\nThe primary reason is:",
                'opts' => [
                    ['Adam requires more GPU memory', false],
                    ['Adam\'s adaptive learning rates can converge to sharper (worse-generalizing) minima; SGD with proper tuning finds flatter, more generalizable minima', true],
                    ['Adam cannot be used with batch normalization', false],
                    ['SGD uses second-order information that Adam lacks', false],
                ],
            ],

            // ── LARGE-SCALE LINEAR PROGRAMMING ────────────────────────────
            [
                'q' => "```python\nfrom scipy.optimize import linprog\nimport time\n\n# Problem: 10,000 variables, 5,000 constraints\nstart = time.time()\nres = linprog(c, A_ub=A, b_ub=b, method='highs')\nprint(f'{time.time()-start:.2f}s')  # 0.18s\n\n# Same problem with method='revised simplex':\nres2 = linprog(c, A_ub=A, b_ub=b, method='revised simplex')\nprint(f'{time.time()-start:.2f}s')  # 42.3s\n```\n\nHiGHS solver is 235× faster than revised simplex here because HiGHS uses:",
                'opts' => [
                    ['A smaller feasible region', false],
                    ['Interior-point methods that scale polynomially with problem size, unlike the Simplex\'s exponential worst-case', true],
                    ['Parallel GPU computation', false],
                    ['Random sampling of the feasible region', false],
                ],
            ],
            [
                'q' => "A logistics company solves a daily vehicle routing optimization with:\n  - 500 delivery locations\n  - 20 vehicles\n  - Time window constraints per delivery\n\nThis problem is NP-hard (a variant of TSP). The production system uses a metaheuristic.\nWhich is the most appropriate professional approach?",
                'opts' => [
                    ['Exact branch-and-bound solver — always use exact methods', false],
                    ['A hybrid approach: construction heuristic for initial solution + local search (e.g. LKH3 or OR-Tools) within a time budget', true],
                    ['Simulated Annealing from scratch with random initialization', false],
                    ['Grid Search over all 500! permutations', false],
                ],
            ],
            [
                'q' => "```python\nfrom ortools.linear_solver import pywraplp\n\nsolver = pywraplp.Solver.CreateSolver('SCIP')\n# SCIP is used for Mixed Integer Programming (MIP)\n\nx = solver.IntVar(0, 100, 'x')  # Integer variable\ny = solver.NumVar(0, 100, 'y')  # Continuous variable\n```\n\nMixed Integer Programming (MIP) is harder than LP because:",
                'opts' => [
                    ['The objective function is nonlinear', false],
                    ['Integer variables make the feasible region discrete — branch-and-bound must enumerate, making it NP-hard in general', true],
                    ['MIP has more constraints than LP', false],
                    ['MIP requires more memory than LP always', false],
                ],
            ],

            // ── METAHEURISTICS — PROFESSIONAL ────────────────────────────
            [
                'q' => "```python\nclass GeneticAlgorithm:\n    def __init__(self, pop_size, elite_ratio=0.1):\n        self.elite_size = int(pop_size * elite_ratio)\n\n    def evolve(self, population, fitness_fn):\n        sorted_pop = sorted(population, key=fitness_fn, reverse=True)\n        elites = sorted_pop[:self.elite_size]\n        offspring = self.crossover_and_mutate(sorted_pop)\n        return elites + offspring[:len(population)-self.elite_size]\n```\n\nElitism (preserving top 10%) prevents premature convergence loss. However, a known risk of high elite_ratio is:",
                'opts' => [
                    ['The algorithm becomes too random', false],
                    ['The population converges prematurely (loss of diversity), trapping the search in a local optimum', true],
                    ['Elite individuals are always the global optimum', false],
                    ['The algorithm requires more crossover operations', false],
                ],
            ],
            [
                'q' => "In production deployment of Simulated Annealing for a combinatorial scheduling problem:\n\n  - Problem size: 10,000 jobs\n  - Time budget: 60 seconds\n  - Required: near-optimal solution\n\nThe cooling schedule T(t) = T₀ · αᵗ with α = 0.9999 and T₀ = 1000.\n\nAfter t = 100,000 iterations: T = 1000 × 0.9999^100000 ≈ ?",
                'opts' => [
                    ['≈ 1000 (no cooling happened)', false],
                    ['≈ 4.5e-5 (effectively frozen)', true],
                    ['≈ 100 (10× reduction)', false],
                    ['≈ 0.9999', false],
                ],
            ],
            [
                'q' => "A genetic algorithm for neural architecture search (NAS) encodes architectures as chromosomes.\nFitness evaluation requires training each architecture for 10 epochs on GPU.\n\nWith population=50, generations=100, this requires:\n  50 × 100 = 5,000 architecture evaluations × 10 epochs each = 50,000 GPU-epochs\n\nThe professional solution to reduce this cost is:",
                'opts' => [
                    ['Reduce population size to 2', false],
                    ['Use a performance predictor (surrogate model) or weight sharing (e.g. ENAS/DARTS) to estimate fitness without full training', true],
                    ['Increase the mutation rate to find solutions faster', false],
                    ['Run only 1 generation', false],
                ],
            ],

            // ── BAYESIAN / HYPERPARAMETER — PROFESSIONAL ──────────────────
            [
                'q' => "```python\nfrom optuna import create_study\n\ndef objective(trial):\n    lr = trial.suggest_float('lr', 1e-5, 1e-1, log=True)\n    dropout = trial.suggest_float('dropout', 0.1, 0.5)\n    n_layers = trial.suggest_int('n_layers', 1, 5)\n    # ... train model ...\n    return val_loss\n\nstudy = create_study(direction='minimize',\n                     sampler=optuna.samplers.TPESampler())\nstudy.optimize(objective, n_trials=100)\n```\n\nTPE (Tree-structured Parzen Estimator) models p(x|good) and p(x|bad) separately.\nIt selects the next trial by maximizing:",
                'opts' => [
                    ['p(x|bad) / p(x|good) — choosing configurations similar to bad trials', false],
                    ['p(x|good) / p(x|bad) — choosing configurations more likely to be good than bad', true],
                    ['A random sample from p(x|good) only', false],
                    ['The gradient of the surrogate model', false],
                ],
            ],
            [
                'q' => "```python\n# Optuna pruning with MedianPruner:\nstudy = create_study(pruner=optuna.pruners.MedianPruner())\n```\n\nPruning in hyperparameter optimization refers to:",
                'opts' => [
                    ['Removing hyperparameters from the search space', false],
                    ['Early termination of unpromising trials based on intermediate results, saving compute', true],
                    ['Reducing the neural network depth', false],
                    ['Applying L1 regularization to model weights', false],
                ],
            ],
            [
                'q' => "A team runs 200 Bayesian Optimization trials for a hyperparameter search.\nAfter trial 50, the validation accuracy has plateaued at 94.1% and trials 51-200 all return values between 93.8% and 94.2%.\n\nFrom a professional resource management perspective, the correct action is:",
                'opts' => [
                    ['Always complete all 200 trials as planned', false],
                    ['Stop early — the search has converged; invest the remaining compute in longer training or ensembling', true],
                    ['Increase n_iter to 500 and continue', false],
                    ['Switch to Grid Search to be thorough', false],
                ],
            ],

            // ── SECOND-ORDER / L-BFGS — PROFESSIONAL ──────────────────────
            [
                'q' => "```python\nfrom scipy.optimize import minimize\nimport numpy as np\n\n# Fitting a neural network with scipy L-BFGS on 1M samples\n# Observation: each function evaluation takes 4.2 seconds\n# L-BFGS requires ~50 function evaluations to converge\n# Total time: ~210 seconds\n\n# Mini-batch Adam on same problem: 30 seconds for equivalent accuracy\n```\n\nDespite L-BFGS's superior convergence rate per iteration, Adam wins here because:",
                'opts' => [
                    ['Adam uses second-order information too', false],
                    ['Each L-BFGS function evaluation requires the full dataset; Adam uses mini-batches, making each step vastly cheaper', true],
                    ['L-BFGS cannot handle non-convex problems', false],
                    ['Adam\'s adaptive learning rate makes it always superior', false],
                ],
            ],
            [
                'q' => "```python\n# Quasi-Newton update (BFGS):\n# B_{k+1} = B_k + (y_k y_k^T)/(y_k^T s_k) - (B_k s_k s_k^T B_k)/(s_k^T B_k s_k)\n# where s_k = x_{k+1} - x_k  (step)\n#       y_k = ∇f_{k+1} - ∇f_k  (gradient change)\n```\n\nThe BFGS update fails (produces NaN or non-positive-definite B) when:\n\n  y_k^T s_k ≤ 0",
                'opts' => [
                    ['The gradient is very large', false],
                    ['The curvature condition is violated — the function is not locally convex in the step direction (non-positive curvature)', true],
                    ['The learning rate is too small', false],
                    ['The Hessian has zero eigenvalues', false],
                ],
            ],

            // ── EDGE CASES & FAILURE MODES ────────────────────────────────
            [
                'q' => "```python\noptimizer = torch.optim.Adam(model.parameters(), lr=1e-3)\n\nfor epoch in range(100):\n    for batch in dataloader:\n        loss = criterion(model(batch['x']), batch['y'])\n        optimizer.zero_grad()\n        loss.backward()\n        optimizer.step()\n\n# After 100 epochs: val_loss = 0.23 (good)\n# Model deployed to production\n# After 3 months: production accuracy drops by 8%\n```\n\nThis production accuracy drop after deployment is called:",
                'opts' => [
                    ['Gradient vanishing', false],
                    ['Model drift / distribution shift — the production data distribution has changed from the training distribution', true],
                    ['Learning rate decay', false],
                    ['Overfitting to the validation set', false],
                ],
            ],
            [
                'q' => "```python\n# Two optimizers tested on the same problem:\n# Optimizer A (Adam): val_loss reaches 0.18 in 50 epochs\n# Optimizer B (SGD+cosine): val_loss reaches 0.15 in 200 epochs\n\n# Test set results after deployment:\n# Optimizer A model: test_acc = 88.2%\n# Optimizer B model: test_acc = 91.4%\n```\n\nOptimizer B is better despite slower training. This is consistent with research showing SGD finds _____ minima than Adam.",
                'opts' => [
                    ['Sharper, more overfit', false],
                    ['Flatter, better-generalizing', true],
                    ['Deeper (lower training loss)', false],
                    ['Local rather than global', false],
                ],
            ],
            [
                'q' => "```python\n# Constrained optimization with scipy:\nfrom scipy.optimize import minimize\n\ncons = [{'type': 'ineq', 'fun': lambda x: x[0] - 2},   # x[0] >= 2\n        {'type': 'ineq', 'fun': lambda x: -x[0] + 1}]  # x[0] <= 1\n\nres = minimize(lambda x: x[0]**2, x0=[1.5], constraints=cons)\nprint(res.status)  # 4 — optimization failed\n```\n\nThe optimizer fails because:",
                'opts' => [
                    ['The objective function is nonlinear', false],
                    ['The constraints x[0] ≥ 2 and x[0] ≤ 1 are contradictory — the feasible region is empty', true],
                    ['The initial point x0=1.5 is outside bounds', false],
                    ['scipy cannot handle inequality constraints', false],
                ],
            ],
            [
                'q' => "```python\n# Mixed precision training:\nfrom torch.cuda.amp import autocast, GradScaler\n\nscaler = GradScaler()\nfor batch in dataloader:\n    with autocast():\n        loss = model(batch)\n    scaler.scale(loss).backward()\n    scaler.unscale_(optimizer)\n    torch.nn.utils.clip_grad_norm_(model.parameters(), 1.0)\n    scaler.step(optimizer)\n    scaler.update()\n```\n\nThe `GradScaler` is needed in FP16 training because:",
                'opts' => [
                    ['FP16 requires larger batch sizes', false],
                    ['FP16 gradients can underflow to zero (values < 6e-8 become 0); scaling prevents this by amplifying gradients before the backward pass', true],
                    ['The optimizer cannot handle float32 gradients', false],
                    ['Gradient clipping does not work without scaling', false],
                ],
            ],
            [
                'q' => "```python\n# Federated learning optimization:\n# - 1000 client devices, each with local data\n# - Each round: select 10 clients, run K=5 local SGD steps, average weights\n# - FedAvg algorithm\n\n# Observation: After 100 rounds, global model has higher loss than\n#              centralized training on the same total data\n```\n\nThe performance gap in Federated Learning compared to centralized training is primarily caused by:",
                'opts' => [
                    ['FedAvg uses gradient descent instead of Adam', false],
                    ['Client data is non-IID (heterogeneous) — local optima on client data diverge from the global optimum; client drift accumulates', true],
                    ['The global model is averaged incorrectly', false],
                    ['Only 10 clients are selected each round', false],
                ],
            ],
            [
                'q' => "A data scientist has trained a model with excellent offline metrics (test RMSE = 2.1) using Adam optimizer.\n\nIn an A/B test, the model underperforms the existing rule-based system.\n\nFrom an optimization perspective, the most important next question to ask is:",
                'opts' => [
                    ['Should I switch to L-BFGS for better convergence?', false],
                    ['Is the offline test set representative of the production distribution, and is RMSE the right metric aligned with the business objective?', true],
                    ['Should I increase the number of training epochs?', false],
                    ['Should I add more hidden layers to the model?', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 13 — Introduction to Optimization Techniques (Professional).");
    }
}