<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 17 — Deep Learning (Newbie / Level 1) — CODING variant
 *
 * Seeds in one pass:
 * 1. challenges          — one coding challenge for the Newbie tier
 * 2. coding_questions    — 50 questions covering beginner Deep Learning concepts
 * 3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mapped to lessons 447–456):
 * L17.1  What Is Deep Learning?
 * L17.2  The Neuron & Feedforward Networks
 * L17.3  Backpropagation & Gradient Descent
 * L17.4  Optimizers & Learning Rate Schedules
 * L17.5  Regularization: Preventing Overfitting
 * L17.6  Convolutional Neural Networks (CNNs)
 * L17.7  Transfer Learning & Pretrained Models
 * L17.8  Recurrent Neural Networks & LSTMs
 * L17.9  Transformers & the Attention Mechanism
 * L17.10 Generative Models: GANs & VAEs
 *
 * Difficulty: Newbie — all problems solvable with pure Python, no third-party
 * libraries required. Learners build intuition for tensor operations, activations,
 * forward/backward passes, and modern architectures from scratch.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module17CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 17 — Deep Learning (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Deep Learning Fundamentals',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Master the building blocks of Deep Learning using pure Python. Implement core operations like activation functions, forward passes, gradient updates, convolution steps, attention scores, and sequence modeling without relying on external libraries like PyTorch or TensorFlow.',
                'time_limit_seconds' => 1200,
                'base_xp'            => 800,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: What Is Deep Learning? (Q1–Q5) → L447
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
**Scalar Multiplication (Broadcasting Concept):** Deep learning often scales arrays by a single value. Read a scalar `c`, then an array of `n` values. Multiply every element by `c` and print the resulting space-separated array, rounded to 4 decimal places.

Example:
Input:
2.0
1.0 2.5 3.0
Output:
2.0000 5.0000 6.0000

MD,
                'starter_code'        => "c = float(input())\narr = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
**Element-wise Addition:** Add two vectors of the same length element-by-element. Read `n`, then read vector A and vector B on separate lines. Print the result space-separated, rounded to 4 decimal places.

Example:
Input:
3
1 2 3
4 5 6
Output:
5.0000 7.0000 9.0000

MD,
                'starter_code'        => "n = int(input())\nA = list(map(float, input().split()))\nB = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
**Flattening a Matrix:** Neural networks often require unrolling 2D matrices into 1D vectors (e.g., flattening an image). Read `rows` and `cols`, followed by the matrix elements row by row. Print the 1D flattened array space-separated.

Example:
Input:
2 3
1 2 3
4 5 6
Output:
1.0000 2.0000 3.0000 4.0000 5.0000 6.0000

MD,
                'starter_code'        => "rows, cols = map(int, input().split())\nmatrix = [list(map(float, input().split())) for _ in range(rows)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
**Matrix Transposition:** Transposing weights is crucial in backpropagation. Read `rows` and `cols`, followed by the 2D matrix. Compute the transpose (shape `cols × rows`) and print it row by row, space-separated, rounded to 4 decimals.

Example:
Input:
2 3
1 2 3
4 5 6
Output:
1.0000 4.0000
2.0000 5.0000
3.0000 6.0000

MD,
                'starter_code'        => "rows, cols = map(int, input().split())\nmatrix = [list(map(float, input().split())) for _ in range(rows)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
**One-Hot Encoding:** Categorical targets are converted to one-hot vectors. Read the number of classes `C` and a target integer `y` (0-indexed). Create a vector of length `C` with 1.0 at index `y` and 0.0 elsewhere. Print space-separated, rounded to 1 decimal.

Example:
Input:
5
2
Output:
0.0 0.0 1.0 0.0 0.0

MD,
                'starter_code'        => "C = int(input())\ny = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: The Neuron & Feedforward Networks (Q6–Q10) → L448
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
**Linear Neuron (Dot Product + Bias):** The core operation of a neuron: `z = (x · w) + b`. Read vector length `n`, input vector `x`, weight vector `w`, and scalar bias `b`. Print the output `z` rounded to 4 decimal places.

Example:
Input:
3
1 2 3
0.5 0.5 0.5
2.0
Output:
5.0000

MD,
                'starter_code'        => "n = int(input())\nx = list(map(float, input().split()))\nw = list(map(float, input().split()))\nb = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
**ReLU Activation:** The Rectified Linear Unit applies `max(0, z)` to each element. Read an array of `n` values. Apply ReLU to each and print the result space-separated, rounded to 4 decimals.

Example:
Input:
-2.5 0.0 3.1
Output:
0.0000 0.0000 3.1000

MD,
                'starter_code'        => "arr = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
**Sigmoid Activation:** Sigmoid squashes values between 0 and 1: `σ(z) = 1 / (1 + e^(-z))`. Read a vector `z`. Apply Sigmoid element-wise and print rounded to 4 decimal places.

Example:
Input:
0.0 2.0
Output:
0.5000 0.8808

MD,
                'starter_code'        => "import math\nz = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
**Softmax Activation (Stable):** Softmax turns raw logits into probabilities. To prevent overflow, subtract the maximum logit before exponentiating: `s_i = exp(z_i - max) / sum(exp(z - max))`. Read a vector `z`. Print probabilities space-separated, rounded to 4 decimals.

Example:
Input:
1.0 2.0 3.0
Output:
0.0900 0.2447 0.6652

MD,
                'starter_code'        => "import math\nz = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
**Forward Pass (Single Layer):** A feedforward layer processes input `X` (shape 1xM) with a Weight matrix `W` (shape MxN) and bias `B` (shape 1xN): `Z = X·W + B`. Read `M` (input size) and `N` (output size), vector `X`, the `M × N` weight matrix row by row, and vector `B`. Print output vector `Z` rounded to 4 decimals.

Example:
Input:
2 2
1.0 2.0
0.5 0.1
0.2 0.8
0.1 0.1
Output:
1.0000 1.8000

MD,
                'starter_code'        => "M, N = map(int, input().split())\nX = list(map(float, input().split()))\nW = [list(map(float, input().split())) for _ in range(M)]\nB = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Backpropagation & Gradient Descent (Q11–Q15) → L449
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
**Derivative of ReLU:** During backprop, the gradient passes through ReLU. The local derivative is 1 if the original input `z > 0`, and 0 if `z <= 0`. Read vector `z`. Print the derivative vector space-separated, rounded to 1 decimal.

Example:
Input:
-1.5 0.0 2.5
Output:
0.0 0.0 1.0

MD,
                'starter_code'        => "z = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
**Derivative of Sigmoid:** If `a = σ(z)`, the local derivative with respect to `z` is `a * (1 - a)`. Read the activated outputs `a` (not `z`). Print the local derivatives space-separated, rounded to 4 decimal places.

Example:
Input:
0.5 0.8808
Output:
0.2500 0.1050

MD,
                'starter_code'        => "a = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
**Mean Squared Error (MSE):** A standard loss function for regression. `MSE = Sum((y_true - y_pred)²) / N`. Read `N`, vector `y_true`, and vector `y_pred`. Print the MSE rounded to 4 decimal places.

Example:
Input:
3
1 2 3
1 2.5 4
Output:
0.4167

MD,
                'starter_code'        => "N = int(input())\ny_true = list(map(float, input().split()))\ny_pred = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
**Gradient of MSE Loss:** The derivative of MSE with respect to `y_pred` for a single sample is `(2/N) * (y_pred - y_true)`. Read `N`, `y_true`, and `y_pred`. Print the gradient vector space-separated, rounded to 4 decimals.

Example:
Input:
2
1.0 0.0
0.5 0.5
Output:
-0.5000 0.5000

MD,
                'starter_code'        => "N = int(input())\ny_true = list(map(float, input().split()))\ny_pred = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
**Basic Gradient Descent Step:** Update weights in the opposite direction of the gradient: `w_new = w - lr * grad`. Read learning rate `lr`, weight vector `w`, and gradient vector `grad`. Print `w_new` rounded to 4 decimals.

Example:
Input:
0.1
1.0 1.0
0.5 -0.5
Output:
0.9500 1.0500

MD,
                'starter_code'        => "lr = float(input())\nw = list(map(float, input().split()))\ngrad = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Optimizers & Learning Rate Schedules (Q16–Q20) → L450
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
**SGD with Momentum:** Momentum accumulates past gradients to accelerate convergence: `v = beta * v + grad`, `w = w - lr * v`. Read `lr`, `beta`, vector `w`, velocity `v`, and `grad`. Print the updated `w` rounded to 4 decimals.

Example:
Input:
0.1 0.9
1.0 1.0
0.2 0.0
0.5 0.5
Output:
0.9320 0.9500

MD,
                'starter_code'        => "lr, beta = map(float, input().split())\nw = list(map(float, input().split()))\nv = list(map(float, input().split()))\ngrad = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
**RMSprop Optimizer:** Scales learning rate by an exponentially decaying average of squared gradients: `S = beta * S + (1-beta) * grad²`, `w = w - lr * grad / (sqrt(S) + eps)`. (eps = 1e-8). Read `lr`, `beta`, `w`, `S`, `grad`. Print updated `w` rounded to 4 decimals.

Example:
Input:
0.01 0.9
1.0 1.0
0.01 0.01
0.5 -0.5
Output:
0.9543 1.0457

MD,
                'starter_code'        => "import math\nlr, beta = map(float, input().split())\nw = list(map(float, input().split()))\nS = list(map(float, input().split()))\ngrad = list(map(float, input().split()))\neps = 1e-8\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
**Adam Optimizer (Simplified):** Combines Momentum and RMSprop. `m = b1*m + (1-b1)*g`, `v = b2*v + (1-b2)*g²`. We'll skip bias correction here. `w = w - lr * m / (sqrt(v) + eps)`. Read `lr`, `b1`, `b2`, `w`, `m`, `v`, `grad`. Print updated `w` rounded to 4 decimals (eps = 1e-8).

Example:
Input:
0.01 0.9 0.999
1.0 1.0
0.0 0.0
0.0 0.0
0.5 -0.5
Output:
0.9968 1.0032

MD,
                'starter_code'        => "import math\nlr, b1, b2 = map(float, input().split())\nw = list(map(float, input().split()))\nm = list(map(float, input().split()))\nv = list(map(float, input().split()))\ngrad = list(map(float, input().split()))\neps = 1e-8\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
**Step Decay Learning Rate:** Reduces LR by a `factor` every `step_size` epochs. Read `initial_lr`, `factor`, `step_size`, and the current `epoch`. Print the active learning rate rounded to 6 decimal places. Formula: `lr = initial_lr * (factor ^ floor(epoch / step_size))`.

Example:
Input:
0.1
0.5
10
25
Output:
0.025000

MD,
                'starter_code'        => "import math\ninitial_lr = float(input())\nfactor = float(input())\nstep_size = int(input())\nepoch = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
**Cosine Annealing (Simplified):** Gradually decreases learning rate to an `eta_min` over `T_max` epochs using a cosine curve. Formula: `lr = eta_min + 0.5*(eta_max - eta_min)*(1 + cos(pi * epoch / T_max))`. Read `eta_min`, `eta_max`, `T_max`, and `epoch`. Print `lr` rounded to 6 decimals.

Example:
Input:
0.001
0.1
100
50
Output:
0.050500

MD,
                'starter_code'        => "import math\neta_min = float(input())\neta_max = float(input())\nT_max = float(input())\nepoch = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Regularization: Preventing Overfitting (Q21–Q25) → L451
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
**L1 Regularization Penalty:** L1 adds the sum of absolute values of weights to the loss: `Penalty = lambda * Sum(|w|).` Read `lambda` and weight vector `w`. Print the penalty rounded to 4 decimals.

Example:
Input:
0.1
1.5 -2.0 0.5
Output:
0.4000

MD,
                'starter_code'        => "lam = float(input())\nw = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
**L2 Regularization Penalty:** L2 (Ridge) adds the sum of squared weights to the loss: `Penalty = lambda * Sum(w²).` Read `lambda` and weight vector `w`. Print the penalty rounded to 4 decimals.

Example:
Input:
0.1
1.0 -2.0 3.0
Output:
1.4000

MD,
                'starter_code'        => "lam = float(input())\nw = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
**Weight Decay Update (L2 backprop):** Applying L2 is equivalent to weight decay in standard SGD. The gradient becomes `grad + 2 * lambda * w`. Update: `w = w - lr * (grad + 2 * lam * w)`. Read `lr`, `lam`, `w`, `grad`. Print updated `w` rounded to 4 decimals.

Example:
Input:
0.1 0.01
1.0 1.0
0.5 0.0
Output:
0.9480 0.9980

MD,
                'starter_code'        => "lr, lam = map(float, input().split())\nw = list(map(float, input().split()))\ngrad = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
**Inverted Dropout (Forward Pass):** To maintain expected values, active neurons are scaled by `1 / (1 - p)`, where `p` is the drop probability. Read `p`, a vector `x`, and a binary `mask` (1 = keep, 0 = drop). Compute the scaled output and print rounded to 4 decimals.

Example:
Input:
0.5
10.0 20.0 30.0
1 0 1
Output:
20.0000 0.0000 60.0000

MD,
                'starter_code'        => "p = float(input())\nx = list(map(float, input().split()))\nmask = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
**Early Stopping Check:** Stop training if validation loss hasn't improved for `patience` epochs. Read `patience` and a sequence of `N` validation losses. Determine if training should stop. Print `Stop at epoch E` (where E is 1-indexed) or `Continue`. Assume strict improvement (new_loss < best_loss).

Example:
Input:
2
1.5 1.2 1.3 1.4 1.0
Output:
Stop at epoch 4

MD,
                'starter_code'        => "patience = int(input())\nlosses = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Convolutional Neural Networks (CNNs) (Q26–Q30) → L452
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
**1D Convolution (Valid Padding, Stride 1):** Slide a kernel over a 1D sequence and compute the dot product at each step. Read vector `x` and `kernel`. Compute the 1D convolution result. Print space-separated, rounded to 4 decimals.

Example:
Input:
1 2 3 4
1 0 -1
Output:
-2.0000 -2.0000

MD,
                'starter_code'        => "x = list(map(float, input().split()))\nkernel = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
**1D Max Pooling:** Reduces sequence dimension. Read vector `x`, pool size `K`, and stride `S`. Slide window of size `K` by step `S` and take the max. Print result space-separated, rounded to 4 decimals.

Example:
Input:
1 5 2 8 3
2 2
Output:
5.0000 8.0000

MD,
                'starter_code'        => "x = list(map(float, input().split()))\nK, S = map(int, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
**2D Convolution Output Dimension:** Calculate the output shape of a 2D Conv layer. Formula for width/height: `O = floor((W - K + 2*P) / S) + 1`. Read Input size `W` (assume square), Kernel size `K`, Padding `P`, and Stride `S`. Print `O`.

Example:
Input:
32 3 1 2
Output:
16

MD,
                'starter_code'        => "import math\nW, K, P, S = map(int, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
**2D Convolution (Valid, Stride 1):** Compute a 2D convolution. Read a 3x3 input matrix (row by row), then a 2x2 kernel. The output will be 2x2. Print the output matrix row by row, space-separated, rounded to 4 decimals.

Example:
Input:
1 2 3
4 5 6
7 8 9
1 0
0 -1
Output:
-4.0000 -4.0000
-4.0000 -4.0000

MD,
                'starter_code'        => "matrix = [list(map(float, input().split())) for _ in range(3)]\nkernel = [list(map(float, input().split())) for _ in range(2)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
**2D Max Pooling (2x2):** Read a 4x4 matrix. Apply a 2x2 Max Pooling operation with stride 2. The output will be 2x2. Print the output matrix row by row, space-separated, rounded to 4 decimals.

Example:
Input:
1 2 3 4
5 6 7 8
9 0 1 2
3 4 5 6
Output:
6.0000 8.0000
9.0000 6.0000

MD,
                'starter_code'        => "matrix = [list(map(float, input().split())) for _ in range(4)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Transfer Learning & Pretrained Models (Q31–Q35) → L453
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
**Freezing Weights:** In transfer learning, early layers are "frozen." Read learning rate `lr`, weight vector `w`, gradient vector `grad`, and a boolean mask `requires_grad` (1=update, 0=freeze). Perform standard SGD only on unfrozen weights. Print updated `w` rounded to 4 decimals.

Example:
Input:
0.1
1.0 1.0 1.0
0.5 0.5 0.5
1 0 1
Output:
0.9500 1.0000 0.9500

MD,
                'starter_code'        => "lr = float(input())\nw = list(map(float, input().split()))\ngrad = list(map(float, input().split()))\nreq_grad = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
**Feature Extraction Projection:** Pass an image vector through a pretrained dense layer to extract features. Read input `x` (1xM) and pre-trained weights `W` (MxN). `Features = x · W`. Read M and N, then `x`, then W row by row. Print Features rounded to 4 decimals.

Example:
Input:
2 2
1.0 2.0
0.5 0.1
-0.2 0.8
Output:
0.1000 1.7000

MD,
                'starter_code'        => "M, N = map(int, input().split())\nx = list(map(float, input().split()))\nW = [list(map(float, input().split())) for _ in range(M)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
**Cosine Similarity:** Compare two extracted feature vectors to see if they belong to the same class without further training. `Sim = dot(A,B) / (norm(A)*norm(B))`. Read vectors A and B. Print similarity rounded to 4 decimals.

Example:
Input:
1 0 0
0 1 0
Output:
0.0000

MD,
                'starter_code'        => "import math\nA = list(map(float, input().split()))\nB = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
**Classifier Head Substitution:** A pretrained model outputs a 1000-D vector. We replace the last layer to output 10 classes. Concept: Initialize a new weight matrix randomly. Here, initialize an `M × N` matrix with zeros. Read `M` (input dims) and `N` (output classes). Print the shape and the total number of new parameters (M * N). Format: `Shape: MxN`, `Params: P`.

Example:
Input:
1000 10
Output:
Shape: 1000x10
Params: 10000

MD,
                'starter_code'        => "M, N = map(int, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
**Weight Averaging (Ensembling):** Combine weights of two finetuned models for better robustness: `w_new = alpha * w1 + (1 - alpha) * w2`. Read `alpha`, vector `w1`, vector `w2`. Print `w_new` rounded to 4 decimals.

Example:
Input:
0.5
1.0 2.0
3.0 0.0
Output:
2.0000 1.0000

MD,
                'starter_code'        => "alpha = float(input())\nw1 = list(map(float, input().split()))\nw2 = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Recurrent Neural Networks & LSTMs (Q36–Q40) → L454
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
**Basic RNN Step:** `h_t = tanh(W_x * x_t + W_h * h_{t-1} + b)`. Read scalars `W_x`, `W_h`, `b`, input `x_t`, and previous hidden state `h_prev`. Compute `h_t` and print rounded to 4 decimals. Use `math.tanh`.

Example:
Input:
0.5 0.8 0.1
1.0
0.5
Output:
0.7616

MD,
                'starter_code'        => "import math\nW_x, W_h, b = map(float, input().split())\nx_t = float(input())\nh_prev = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
**LSTM Gate Computation (Sigmoid):** Gates in LSTMs output values between 0 and 1. Compute `f_t = sigmoid(W_f * x_t + U_f * h_prev + b_f)`. Read all 5 scalars in order: `W_f`, `U_f`, `b_f`, `x_t`, `h_prev`. Print `f_t` rounded to 4 decimals.

Example:
Input:
0.1 0.2 0.0 1.0 0.5
Output:
0.5498

MD,
                'starter_code'        => "import math\nW_f, U_f, b_f, x_t, h_prev = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
**LSTM Cell State Update:** `C_t = f_t * C_prev + i_t * C_tilde`. Read scalars `f_t`, `C_prev`, `i_t`, `C_tilde`. Print the new cell state `C_t` rounded to 4 decimals.

Example:
Input:
0.9 1.0 0.5 0.2
Output:
1.0000

MD,
                'starter_code'        => "f_t, C_prev, i_t, C_tilde = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
**Sequence Padding:** RNNs process batches of sequences which must be the same length. Read `max_length`, then a sequence array. If the array is shorter than `max_length`, append `0.0`s to the end. If longer, truncate to `max_length`. Print the sequence space-separated, rounded to 1 decimal.

Example:
Input:
4
1.0 2.0
Output:
1.0 2.0 0.0 0.0

MD,
                'starter_code'        => "max_length = int(input())\nseq = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
**Char-to-Int Mapping:** Before embedding, text sequences are converted to integers. Read a string `vocab` (no spaces), then a string `sequence`. Assign each char in `vocab` an index (0 to len-1) based on its appearance. Convert `sequence` to an array of integers. If a char is not in vocab, map it to `-1`. Print space-separated.

Example:
Input:
abc
cabx
Output:
2 0 1 -1

MD,
                'starter_code'        => "vocab = input().strip()\nsequence = input().strip()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Transformers & the Attention Mechanism (Q41–Q45) → L455
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
**Q, K, V Linear Projections:** A transformer generates Query, Key, and Value vectors from an input embedding `x`. `Q = x·W_q`, `K = x·W_k`, `V = x·W_v`. All matrices are shape (M x D). Read M, D. Read `x`. Then read `W_q`, `W_k`, `W_v` row by row sequentially. Print `Q`, `K`, and `V` vectors on separate lines, rounded to 4 decimals.

Example (Mocked Input for size 1x2 and 2x1 weights):
Input:
2 1
1.0 1.0
0.5
0.5
0.2
0.2
0.1
0.1
Output:
1.0000
0.4000
0.2000

MD,
                'starter_code'        => "M, D = map(int, input().split())\nx = list(map(float, input().split()))\nW_q = [list(map(float, input().split())) for _ in range(M)]\nW_k = [list(map(float, input().split())) for _ in range(M)]\nW_v = [list(map(float, input().split())) for _ in range(M)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
**Scaled Dot-Product Attention Score:** Before softmax, the attention score between a Query and a Key is `(Q · K) / sqrt(d_k)`. Read `d_k`, vector `Q`, and vector `K`. Print the scalar score rounded to 4 decimals.

Example:
Input:
4
1.0 1.0
2.0 2.0
Output:
2.0000

MD,
                'starter_code'        => "import math\nd_k = float(input())\nQ = list(map(float, input().split()))\nK = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
**Attention Masking (Decoder):** In decoders, future tokens are masked out with `-infinity` before softmax so their probabilities become 0. Read an attention score vector of length `N`. Given current sequence position `t` (0-indexed), mask out all indices `> t` by setting them to `-1e9`. Print the masked vector space-separated.

Example:
Input:
4 1
1.5 2.0 3.0 4.0
Output:
1.5000 2.0000 -1000000000.0000 -1000000000.0000

MD,
                'starter_code'        => "N, t = map(int, input().split())\nscores = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
**Attention Context Vector:** The context vector is the weighted sum of Value vectors. `C = Sum(weight_i * V_i)`. Read `N` (number of tokens) and `D` (dimension). Read `N` attention weights (probabilities summing to 1). Then read `N` Value vectors of length `D` row by row. Print the context vector `C` rounded to 4 decimals.

Example:
Input:
2 2
0.8 0.2
1.0 0.0
0.0 1.0
Output:
0.8000 0.2000

MD,
                'starter_code'        => "N, D = map(int, input().split())\nweights = list(map(float, input().split()))\nV = [list(map(float, input().split())) for _ in range(N)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
**Positional Encoding Computation:** Transformers use sine and cosine for position information. For a given position `pos`, dimension index `i`, and model dimension `d_model`:
If `i` is even: `PE = sin(pos / 10000^(i / d_model))`
If `i` is odd: `PE = cos(pos / 10000^((i - 1) / d_model))`
Read `pos`, `i`, `d_model`. Print `PE` rounded to 4 decimals.

Example:
Input:
1 0 512
Output:
0.8415

MD,
                'starter_code'        => "import math\npos = int(input())\ni = int(input())\nd_model = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Generative Models: GANs & VAEs (Q46–Q50) → L456
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
**Binary Cross-Entropy Loss (BCE):** Used in GAN discriminator training. `L = -[y * log(p) + (1-y) * log(1-p)]`. Read true label `y` (0 or 1) and prediction probability `p`. Print `L` rounded to 4 decimals. Assume `p` is safely bounded in (0, 1).

Example:
Input:
1 0.9
Output:
0.1054

MD,
                'starter_code'        => "import math\ny = float(input())\np = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
**Reparameterization Trick (VAE):** To allow backprop through stochastic sampling, VAEs use `z = mu + sigma * epsilon`. Given a network outputs `mu` and `log_var`, compute `sigma = exp(0.5 * log_var)`. Read `mu`, `log_var`, and sampled `epsilon`. Print `z` rounded to 4 decimals.

Example:
Input:
0.0 0.0 1.5
Output:
1.5000

MD,
                'starter_code'        => "import math\nmu, log_var, eps = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
**KL Divergence Loss (VAE Prior):** The regularizer forcing the latent distribution to match a standard normal. Formula for a single dimension: `KL = -0.5 * (1 + log_var - mu² - exp(log_var))`. Read vectors `mu` and `log_var`. Compute the sum of KL over all dimensions. Print rounded to 4 decimals.

Example:
Input:
0.0 1.0
0.0 0.0
Output:
0.5000

MD,
                'starter_code'        => "import math\nmu = list(map(float, input().split()))\nlog_var = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
**GAN Generator Update:** The generator tries to maximize `D(G(z))`, equivalent to minimizing `-log(D(G(z)))`. Read learning rate `lr`, generator weight `w`, the gradient of Discriminator score w.r.t `w` (which is `dD/dw`). Update: `w_new = w + lr * dD/dw` (gradient ascent to maximize score). Print `w_new` rounded to 4 decimals.

Example:
Input:
0.1 1.0 0.5
Output:
1.0500

MD,
                'starter_code'        => "lr, w, dD_dw = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**Latent Space Interpolation (SLERP Basics):** Simple linear interpolation in latent space allows smooth transitions between generated outputs. `z = (1 - alpha) * z1 + alpha * z2`. Read `alpha`, vector `z1`, vector `z2`. Print `z` rounded to 4 decimals.

Example:
Input:
0.5
-1.0 1.0
1.0 3.0
Output:
0.0000 2.0000

MD,
                'starter_code'        => "alpha = float(input())\nz1 = list(map(float, input().split()))\nz2 = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

        ];

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

        // Q1: Scalar Multiplication
        $seed(1, [
            ['input' => "2.0\n1.0 2.5 3.0", 'expected_output' => "2.0000 5.0000 6.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "-0.5\n4.0 -2.0 0.0", 'expected_output' => "-2.0000 1.0000 0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n100 200", 'expected_output' => "0.0000 0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.5\n-1 -3 5", 'expected_output' => "-1.5000 -4.5000 7.5000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q2: Element-wise Addition
        $seed(2, [
            ['input' => "3\n1 2 3\n4 5 6", 'expected_output' => "5.0000 7.0000 9.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.5 -0.5\n-0.5 0.5", 'expected_output' => "0.0000 0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1 1 1\n0 0 0 0", 'expected_output' => "1.0000 1.0000 1.0000 1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n100\n-50", 'expected_output' => "50.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q3: Flatten Matrix
        $seed(3, [
            ['input' => "2 3\n1 2 3\n4 5 6", 'expected_output' => "1.0000 2.0000 3.0000 4.0000 5.0000 6.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 1\n1\n2\n3", 'expected_output' => "1.0000 2.0000 3.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 4\n0.5 0.5 1.5 2.5", 'expected_output' => "0.5000 0.5000 1.5000 2.5000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 2\n0 0\n1 1", 'expected_output' => "0.0000 0.0000 1.0000 1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q4: Matrix Transposition
        $seed(4, [
            ['input' => "2 3\n1 2 3\n4 5 6", 'expected_output' => "1.0000 4.0000\n2.0000 5.0000\n3.0000 6.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 1\n1\n2\n3", 'expected_output' => "1.0000 2.0000 3.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 3\n1 2 3", 'expected_output' => "1.0000\n2.0000\n3.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 2\n1 0\n0 1", 'expected_output' => "1.0000 0.0000\n0.0000 1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q5: One-Hot Encoding
        $seed(5, [
            ['input' => "5\n2", 'expected_output' => "0.0 0.0 1.0 0.0 0.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0", 'expected_output' => "1.0 0.0 0.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1", 'expected_output' => "0.0 1.0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "10\n9", 'expected_output' => "0.0 0.0 0.0 0.0 0.0 0.0 0.0 0.0 0.0 1.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q6: Linear Neuron
        $seed(6, [
            ['input' => "3\n1 2 3\n0.5 0.5 0.5\n2.0", 'expected_output' => "5.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1.0 -1.0\n1.0 1.0\n0.0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5.0\n2.0\n-5.0", 'expected_output' => "5.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n0 0 0 0\n1 2 3 4\n10.0", 'expected_output' => "10.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q7: ReLU
        $seed(7, [
            ['input' => "-2.5 0.0 3.1", 'expected_output' => "0.0000 0.0000 3.1000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "10 -10", 'expected_output' => "10.0000 0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1 -2 -3", 'expected_output' => "0.0000 0.0000 0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.5", 'expected_output' => "0.5000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q8: Sigmoid
        $seed(8, [
            ['input' => "0.0 2.0", 'expected_output' => "0.5000 0.8808", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "-10.0 10.0", 'expected_output' => "0.0000 1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1.0 0.0 1.0", 'expected_output' => "0.2689 0.5000 0.7311", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5.0", 'expected_output' => "0.9933", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q9: Softmax
        $seed(9, [
            ['input' => "1.0 2.0 3.0", 'expected_output' => "0.0900 0.2447 0.6652", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 0", 'expected_output' => "0.5000 0.5000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "100 100 100", 'expected_output' => "0.3333 0.3333 0.3333", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "-10 -9", 'expected_output' => "0.2689 0.7311", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q10: Forward Pass (Single Layer)
        $seed(10, [
            ['input' => "2 2\n1.0 2.0\n0.5 0.1\n0.2 0.8\n0.1 0.1", 'expected_output' => "1.0000 1.8000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 1\n1 1 1\n1\n1\n1\n0", 'expected_output' => "3.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 3\n2\n1 0.5 0\n1 1 1", 'expected_output' => "3.0000 2.0000 1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 2\n0 0\n1 1\n1 1\n5 5", 'expected_output' => "5.0000 5.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q11: Derivative of ReLU
        $seed(11, [
            ['input' => "-1.5 0.0 2.5", 'expected_output' => "0.0 0.0 1.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "10 5 -5", 'expected_output' => "1.0 1.0 0.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-0.1", 'expected_output' => "0.0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.1", 'expected_output' => "1.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q12: Derivative of Sigmoid
        $seed(12, [
            ['input' => "0.5 0.8808", 'expected_output' => "0.2500 0.1050", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0 1.0", 'expected_output' => "0.0000 0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.1 0.9", 'expected_output' => "0.0900 0.0900", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.2689", 'expected_output' => "0.1966", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q13: Mean Squared Error
        $seed(13, [
            ['input' => "3\n1 2 3\n1 2.5 4", 'expected_output' => "0.4167", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0\n1 1", 'expected_output' => "1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5\n5", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n1 1 1 1\n0 2 0 2", 'expected_output' => "1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q14: Gradient of MSE
        $seed(14, [
            ['input' => "2\n1.0 0.0\n0.5 0.5", 'expected_output' => "-0.5000 0.5000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n5.0\n10.0", 'expected_output' => "10.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 1 1\n1 1 1", 'expected_output' => "0.0000 0.0000 0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n1 0 1 0\n0 1 0 1", 'expected_output' => "-0.5000 0.5000 -0.5000 0.5000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q15: Gradient Descent Step
        $seed(15, [
            ['input' => "0.1\n1.0 1.0\n0.5 -0.5", 'expected_output' => "0.9500 1.0500", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.01\n0 0\n100 50", 'expected_output' => "-1.0000 -0.5000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n5.0\n2.0", 'expected_output' => "3.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.5\n-1 -1\n-2 -2", 'expected_output' => "0.0000 0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q16: SGD with Momentum
        $seed(16, [
            ['input' => "0.1 0.9\n1.0 1.0\n0.2 0.0\n0.5 0.5", 'expected_output' => "0.9320 0.9500", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.1 0.0\n1.0 1.0\n0.2 0.0\n0.5 0.5", 'expected_output' => "0.9500 0.9500", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.01 0.99\n0.0\n10.0\n0.1", 'expected_output' => "-0.1000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0 0.5\n5 5\n-2 -2\n1 1", 'expected_output' => "5.0000 5.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q17: RMSprop
        $seed(17, [
            ['input' => "0.01 0.9\n1.0 1.0\n0.01 0.01\n0.5 -0.5", 'expected_output' => "0.9543 1.0457", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.1 0.99\n0.0\n0.0\n1.0", 'expected_output' => "-1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.001 0.999\n5.0\n0.1\n0.0", 'expected_output' => "5.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.5 0.5\n1.0\n1.0\n1.0", 'expected_output' => "0.5000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q18: Adam Optimizer
        $seed(18, [
            ['input' => "0.01 0.9 0.999\n1.0 1.0\n0.0 0.0\n0.0 0.0\n0.5 -0.5", 'expected_output' => "0.9968 1.0032", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.1 0.0 0.0\n0.0\n0.0\n0.0\n10.0", 'expected_output' => "-0.1000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.001 0.9 0.999\n5.0\n1.0\n1.0\n0.0", 'expected_output' => "4.9991", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.01 0.5 0.5\n0.0\n0.0\n0.0\n1.0", 'expected_output' => "-0.0071", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q19: Step Decay
        $seed(19, [
            ['input' => "0.1\n0.5\n10\n25", 'expected_output' => "0.025000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.1\n0.1\n5\n4", 'expected_output' => "0.100000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.5\n0.5\n1\n3", 'expected_output' => "0.062500", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.01\n0.2\n100\n200", 'expected_output' => "0.000400", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q20: Cosine Annealing
        $seed(20, [
            ['input' => "0.001\n0.1\n100\n50", 'expected_output' => "0.050500", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0\n10\n0", 'expected_output' => "1.000000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n1.0\n10\n10", 'expected_output' => "0.000000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.01\n0.05\n200\n100", 'expected_output' => "0.030000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q21: L1 Penalty
        $seed(21, [
            ['input' => "0.1\n1.5 -2.0 0.5", 'expected_output' => "0.4000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n10 20 30", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n-1 -1 -1", 'expected_output' => "3.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.5\n0 0 0", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q22: L2 Penalty
        $seed(22, [
            ['input' => "0.1\n1.0 -2.0 3.0", 'expected_output' => "1.4000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n2 2", 'expected_output' => "4.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n10 10", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0\n-1 1 -1 1", 'expected_output' => "4.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q23: Weight Decay Update
        $seed(23, [
            ['input' => "0.1 0.01\n1.0 1.0\n0.5 0.0", 'expected_output' => "0.9480 0.9980", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.1 0.0\n1.0 1.0\n0.5 0.0", 'expected_output' => "0.9500 1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0 0.5\n2.0\n0.0", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.5 0.1\n1 2\n-1 -2", 'expected_output' => "1.4000 2.8000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q24: Inverted Dropout
        $seed(24, [
            ['input' => "0.5\n10.0 20.0 30.0\n1 0 1", 'expected_output' => "20.0000 0.0000 60.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1 2 3\n1 1 1", 'expected_output' => "1.0000 2.0000 3.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.8\n5.0 5.0\n0 1", 'expected_output' => "0.0000 25.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.2\n8.0 8.0 8.0\n1 1 0", 'expected_output' => "10.0000 10.0000 0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q25: Early Stopping
        $seed(25, [
            ['input' => "2\n1.5 1.2 1.3 1.4 1.0", 'expected_output' => "Stop at epoch 4", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2.0 1.8 1.6 1.4 1.2", 'expected_output' => "Continue", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5.0 5.0", 'expected_output' => "Stop at epoch 2", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5\n10 9 8 7 8 8 8", 'expected_output' => "Continue", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q26: 1D Conv
        $seed(26, [
            ['input' => "1 2 3 4\n1 0 -1", 'expected_output' => "-2.0000 -2.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1 1 1\n0.5 0.5", 'expected_output' => "1.0000 1.0000 1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5 10 15\n1", 'expected_output' => "5.0000 10.0000 15.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0 1 0 1\n-1 1", 'expected_output' => "1.0000 -1.0000 1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q27: 1D Max Pool
        $seed(27, [
            ['input' => "1 5 2 8 3\n2 2", 'expected_output' => "5.0000 8.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3 4 5\n3 1", 'expected_output' => "3.0000 4.0000 5.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10 5 -5 20\n2 2", 'expected_output' => "10.0000 20.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0 0 0 1\n4 1", 'expected_output' => "1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q28: 2D Conv Dimension
        $seed(28, [
            ['input' => "32 3 1 2", 'expected_output' => "16", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "28 5 2 1", 'expected_output' => "28", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "64 7 0 2", 'expected_output' => "29", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "10 3 0 1", 'expected_output' => "8", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q29: 2D Convolution (Valid)
        $seed(29, [
            ['input' => "1 2 3\n4 5 6\n7 8 9\n1 0\n0 -1", 'expected_output' => "-4.0000 -4.0000\n-4.0000 -4.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1 1\n1 1 1\n1 1 1\n0.5 0.5\n0.5 0.5", 'expected_output' => "2.0000 2.0000\n2.0000 2.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0 0 0\n0 1 0\n0 0 0\n1 1\n1 1", 'expected_output' => "1.0000 1.0000\n1.0000 1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 0 1\n0 1 0\n1 0 1\n1 0\n0 1", 'expected_output' => "2.0000 0.0000\n0.0000 2.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q30: 2D Max Pooling
        $seed(30, [
            ['input' => "1 2 3 4\n5 6 7 8\n9 0 1 2\n3 4 5 6", 'expected_output' => "6.0000 8.0000\n9.0000 6.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 0 1 1\n0 0 1 1\n2 2 3 3\n2 2 3 3", 'expected_output' => "0.0000 1.0000\n2.0000 3.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1 -2 -3 -4\n-5 -6 -7 -8\n-9 0 -1 -2\n-3 -4 -5 -6", 'expected_output' => "-1.0000 -3.0000\n0.0000 -1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 1 1 1\n1 1 1 1\n1 1 1 1\n1 1 1 1", 'expected_output' => "1.0000 1.0000\n1.0000 1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q31: Freezing Weights
        $seed(31, [
            ['input' => "0.1\n1.0 1.0 1.0\n0.5 0.5 0.5\n1 0 1", 'expected_output' => "0.9500 1.0000 0.9500", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n0 0 0\n1 2 3\n0 0 0", 'expected_output' => "0.0000 0.0000 0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n5.0 5.0\n-1 -1\n1 1", 'expected_output' => "6.0000 6.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.1\n1 2\n10 10\n0 1", 'expected_output' => "1.0000 1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q32: Feature Extraction Projection
        $seed(32, [
            ['input' => "2 2\n1.0 2.0\n0.5 0.1\n-0.2 0.8", 'expected_output' => "0.1000 1.7000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 1\n1 1 1\n1\n2\n3", 'expected_output' => "6.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 3\n5.0\n1 0 2", 'expected_output' => "5.0000 0.0000 10.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 2\n0 0\n100 100\n100 100", 'expected_output' => "0.0000 0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q33: Cosine Similarity
        $seed(33, [
            ['input' => "1 0 0\n0 1 0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1\n2 2", 'expected_output' => "1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 1\n-1 -1", 'expected_output' => "-1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3 4\n-3 -4", 'expected_output' => "-1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q34: Classifier Substitution
        $seed(34, [
            ['input' => "1000 10", 'expected_output' => "Shape: 1000x10\nParams: 10000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "512 2", 'expected_output' => "Shape: 512x2\nParams: 1024", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "768 100", 'expected_output' => "Shape: 768x100\nParams: 76800", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "10 5", 'expected_output' => "Shape: 10x5\nParams: 50", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q35: Weight Averaging
        $seed(35, [
            ['input' => "0.5\n1.0 2.0\n3.0 0.0", 'expected_output' => "2.0000 1.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n5.0 5.0\n0.0 0.0", 'expected_output' => "5.0000 5.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n5.0 5.0\n1.0 1.0", 'expected_output' => "1.0000 1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.8\n10 0\n0 10", 'expected_output' => "8.0000 2.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q36: Basic RNN Step
        $seed(36, [
            ['input' => "0.5 0.8 0.1\n1.0\n0.5", 'expected_output' => "0.7616", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0 0.0 0.0\n0.0\n1.0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0 1.0 0.0\n10.0\n0.5", 'expected_output' => "0.4621", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0 1.0 -2.0\n1.0\n1.0", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q37: LSTM Sigmoid Gate
        $seed(37, [
            ['input' => "0.1 0.2 0.0\n1.0\n0.5", 'expected_output' => "0.5498", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0 0.0 0.0\n10.0\n10.0", 'expected_output' => "0.5000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0 1.0 1.0\n-1.0\n-1.0", 'expected_output' => "0.2689", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5.0 0.0 0.0\n1.0\n0.0", 'expected_output' => "0.9933", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q38: LSTM Cell State Update
        $seed(38, [
            ['input' => "0.9 1.0 0.5 0.2", 'expected_output' => "1.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0 10.0 1.0 5.0", 'expected_output' => "5.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0 5.0 0.0 10.0", 'expected_output' => "5.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.5 0.5 0.5 0.5", 'expected_output' => "0.5000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q39: Sequence Padding
        $seed(39, [
            ['input' => "4\n1.0 2.0", 'expected_output' => "1.0 2.0 0.0 0.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1.0 2.0 3.0", 'expected_output' => "1.0 2.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5.0 5.0 5.0", 'expected_output' => "5.0 5.0 5.0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n9.0", 'expected_output' => "9.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q40: Char-to-Int
        $seed(40, [
            ['input' => "abc\ncabx", 'expected_output' => "2 0 1 -1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "helo\nhello", 'expected_output' => "0 1 2 2 3", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "xyz\nzzzyyyxxx", 'expected_output' => "2 2 2 1 1 1 0 0 0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "a\nbbb", 'expected_output' => "-1 -1 -1", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q41: QKV Projections
        $seed(41, [
            ['input' => "2 1\n1.0 1.0\n0.5\n0.5\n0.2\n0.2\n0.1\n0.1", 'expected_output' => "1.0000\n0.4000\n0.2000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2\n2.0\n1 0\n0 1\n0.5 0.5", 'expected_output' => "2.0000 0.0000\n0.0000 2.0000\n1.0000 1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n0.0 0.0\n1 1\n1 1\n1 1\n1 1\n1 1\n1 1", 'expected_output' => "0.0000 0.0000\n0.0000 0.0000\n0.0000 0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 1\n10.0\n0.1\n0.2\n0.3", 'expected_output' => "1.0000\n2.0000\n3.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q42: Scaled Dot-Product
        $seed(42, [
            ['input' => "4\n1.0 1.0\n2.0 2.0", 'expected_output' => "2.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "100\n10.0 10.0\n1.0 1.0", 'expected_output' => "2.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5.0\n5.0", 'expected_output' => "25.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "9\n0.0 1.0 0.0\n1.0 0.0 0.0", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q43: Attention Masking
        $seed(43, [
            ['input' => "4 1\n1.5 2.0 3.0 4.0", 'expected_output' => "1.5000 2.0000 -1000000000.0000 -1000000000.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 0\n0 0 0", 'expected_output' => "0.0000 -1000000000.0000 -1000000000.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 1\n5 5", 'expected_output' => "5.0000 5.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4 3\n1 2 3 4", 'expected_output' => "1.0000 2.0000 3.0000 4.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q44: Attention Context
        $seed(44, [
            ['input' => "2 2\n0.8 0.2\n1.0 0.0\n0.0 1.0", 'expected_output' => "0.8000 0.2000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 1\n0.3333 0.3333 0.3333\n3\n3\n3", 'expected_output' => "2.9997", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3\n1.0 0.0\n1 2 3\n4 5 6", 'expected_output' => "1.0000 2.0000 3.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 2\n1.0\n5 10", 'expected_output' => "5.0000 10.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q45: Positional Encoding
        $seed(45, [
            ['input' => "1 0 512", 'expected_output' => "0.8415", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1 512", 'expected_output' => "0.5403", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0 10 512", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0 11 512", 'expected_output' => "1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q46: BCE Loss
        $seed(46, [
            ['input' => "1 0.9", 'expected_output' => "0.1054", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 0.1", 'expected_output' => "0.1054", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 0.5", 'expected_output' => "0.6931", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0 0.5", 'expected_output' => "0.6931", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q47: Reparameterization Trick
        $seed(47, [
            ['input' => "0.0 0.0 1.5", 'expected_output' => "1.5000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0 1.38629436 0.5", 'expected_output' => "3.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1.0 -2.0 0.0", 'expected_output' => "-1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.0 2.0 -1.0", 'expected_output' => "-2.7183", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q48: KL Divergence Loss
        $seed(48, [
            ['input' => "0.0 1.0\n0.0 0.0", 'expected_output' => "0.5000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n0.0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n0.0", 'expected_output' => "0.5000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.0 0.0\n1.0 1.0", 'expected_output' => "0.7183", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q49: GAN Generator Update
        $seed(49, [
            ['input' => "0.1\n1.0\n0.5", 'expected_output' => "1.0500", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n0.0\n-2.0", 'expected_output' => "-1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.01\n5.0\n0.0", 'expected_output' => "5.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0\n-1.0\n1.0", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q50: Latent Space Interpolation
        $seed(50, [
            ['input' => "0.5\n-1.0 1.0\n1.0 3.0", 'expected_output' => "0.0000 2.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1 2 3\n4 5 6", 'expected_output' => "1.0000 2.0000 3.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n1 2 3\n4 5 6", 'expected_output' => "4.0000 5.0000 6.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.25\n0 0\n4 8", 'expected_output' => "1.0000 2.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        $this->command->info('✅ Module 17 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}