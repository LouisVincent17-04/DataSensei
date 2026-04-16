<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module17ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 17 — Introduction to Deep Learning (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Deep Learning',
            'description'           => 'Debug and optimize deep learning code — identify training failures from code snippets, diagnose architecture flaws, reason through custom loss functions, and fix common PyTorch and Keras implementation mistakes.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1500,
            'order_index'           => 17,
        ]);

        $this->command->info("Seeding 30 advanced questions...");

        $qaData = [

            // ── CODE DEBUGGING: TRAINING LOOP ─────────────────────────────
            [
                'q' => "Find the bug in this PyTorch training loop:\n\n```python\nfor epoch in range(100):\n    for X_batch, y_batch in dataloader:\n        optimizer.zero_grad()\n        y_pred = model(X_batch)\n        loss = criterion(y_pred, y_batch)\n        loss.backward()\n        # BUG HERE\n        loss.backward()  # called twice\n        optimizer.step()\n```",
                'opts' => [
                    ['optimizer.zero_grad() should be called after optimizer.step()', false],
                    ['loss.backward() is called twice — gradients will be accumulated and doubled, corrupting the update', true],
                    ['criterion should be called after optimizer.step()', false],
                    ['The dataloader loop should be outside the epoch loop', false],
                ],
            ],
            [
                'q' => "This PyTorch model always outputs the same value regardless of input. Identify the bug:\n\n```python\nclass MyModel(nn.Module):\n    def __init__(self):\n        super().__init__()\n        self.fc1 = nn.Linear(10, 64)\n        self.fc2 = nn.Linear(64, 1)\n\n    def forward(self, x):\n        x = self.fc1(x)\n        x = torch.relu(x)\n        x = self.fc2(x)\n        return torch.sigmoid(x).detach()  # BUG\n```",
                'opts' => [
                    ['torch.sigmoid is the wrong activation for the output layer', false],
                    ['.detach() removes the tensor from the computation graph — gradients cannot flow, so weights never update', true],
                    ['self.fc2 should have 64 output features, not 1', false],
                    ['relu should not be used between linear layers', false],
                ],
            ],
            [
                'q' => "Identify the bug in this binary classification training script:\n\n```python\ncriterion = nn.BCELoss()\n\nfor X, y in dataloader:\n    pred = model(X)      # model output: raw logits (no sigmoid)\n    loss = criterion(pred, y.float())\n    loss.backward()\n    optimizer.step()\n    optimizer.zero_grad()\n```",
                'opts' => [
                    ['optimizer.zero_grad() should be before loss.backward()', false],
                    ['BCELoss expects probabilities (0–1), but model outputs raw logits — use BCEWithLogitsLoss instead', true],
                    ['y.float() is unnecessary since y is already a float', false],
                    ['criterion should be defined inside the loop', false],
                ],
            ],
            [
                'q' => "A convolutional model trains correctly but fails at inference time. The bug is:\n\n```python\ndef predict(model, x):\n    pred = model(x)  # No model.eval() called\n    return pred\n```\n\nWhy does this cause incorrect predictions?",
                'opts' => [
                    ['model() requires a batch dimension but predict passes a single sample', false],
                    ['Without model.eval(), Dropout remains active and BatchNorm uses batch statistics — predictions are stochastic and wrong', true],
                    ['model(x) should use torch.no_grad() wrapper only', false],
                    ['pred should be detached before returning', false],
                ],
            ],

            // ── GRADIENT ISSUES ───────────────────────────────────────────
            [
                'q' => "A deep network with 20 sigmoid layers is trained and the early layers show near-zero gradients. What is happening and why?\n\n```\nSigmoid derivative: σ'(z) = σ(z)(1 − σ(z))\nMaximum value of σ'(z) = 0.25 (at z=0)\n```",
                'opts' => [
                    ['Exploding gradients — sigmoid amplifies gradients beyond 1', false],
                    ['Vanishing gradients — multiplying 20 values ≤ 0.25 gives an extremely small number (≤ 0.25²⁰ ≈ 10⁻¹²)', true],
                    ['The learning rate is too high for sigmoid layers', false],
                    ['Sigmoid layers cannot be used in networks deeper than 5 layers', false],
                ],
            ],
            [
                'q' => "Gradient clipping is applied in this training step:\n\n```python\nloss.backward()\ntorch.nn.utils.clip_grad_norm_(model.parameters(), max_norm=1.0)\noptimizer.step()\n```\n\nWhat problem does this solve?",
                'opts' => [
                    ['Vanishing gradients — it amplifies small gradients', false],
                    ['Exploding gradients — if the gradient norm exceeds 1.0, all gradients are scaled down proportionally', true],
                    ['Dying ReLU — it prevents neurons from outputting zero', false],
                    ['Overfitting — it acts as an implicit regularizer', false],
                ],
            ],
            [
                'q' => "Weight initialization matters. This initialization will cause training to fail:\n\n```python\nnn.Linear(256, 256).weight.data.fill_(0.0)  # All weights = 0\n```\n\nWhy?",
                'opts' => [
                    ['Zero weights cause overflow in the forward pass', false],
                    ['All neurons compute identical values and receive identical gradients — symmetry is never broken', true],
                    ['Zero initialization is only a problem for the output layer', false],
                    ['The learning rate becomes zero when weights are zero', false],
                ],
            ],

            // ── CNN CODE ISSUES ───────────────────────────────────────────
            [
                'q' => "This CNN model crashes with a shape mismatch. Find the bug:\n\n```python\nmodel = nn.Sequential(\n    nn.Conv2d(3, 32, kernel_size=3),   # Input: (B, 3, 32, 32)\n    nn.ReLU(),\n    nn.MaxPool2d(2),\n    nn.Flatten(),\n    nn.Linear(32 * 15 * 15, 10)  # BUG\n)\n```\n\nInput: 32×32 image, 3 channels.",
                'opts' => [
                    ['MaxPool2d should use kernel_size=3', false],
                    ['After Conv2d(no padding): 32→30, after MaxPool2d(2): 30→15. Linear input should be 32×15×15 = 7200. This is correct — no bug', false],
                    ['After Conv2d(no padding): 32→30, after MaxPool2d(2): 30→15. Linear should be 32×15×15 — the code is actually correct', false],
                    ['After Conv2d(no padding, stride 1): output is 30×30. After MaxPool2d(2): 15×15. Linear input = 32×15×15 = 7200. The linear layer IS correct', true],
                ],
            ],
            [
                'q' => "This CNN uses 'same' padding to preserve spatial dimensions. What padding value achieves this for a 3×3 kernel with stride 1?",
                'opts' => [
                    ['padding = 0', false],
                    ['padding = 1', true],
                    ['padding = 2', false],
                    ['padding = 3', false],
                ],
            ],
            [
                'q' => "A model's training accuracy is 99% but test accuracy is 62%. What is the MOST LIKELY cause and fix?\n\n```python\nmodel = nn.Sequential(\n    nn.Linear(100, 4096),\n    nn.ReLU(),\n    nn.Linear(4096, 4096),\n    nn.ReLU(),\n    nn.Linear(4096, 10)\n)\n# No regularization, trained on 500 samples\n```",
                'opts' => [
                    ['The model is too small — add more layers', false],
                    ['Severe overfitting — the model is far too large for 500 samples. Add Dropout, L2 regularization, or reduce model size', true],
                    ['The learning rate is too low', false],
                    ['The output layer should use Sigmoid instead of no activation', false],
                ],
            ],

            // ── LOSS FUNCTIONS ────────────────────────────────────────────
            [
                'q' => "This custom loss function has a bug:\n\n```python\ndef focal_loss(pred, target, gamma=2):\n    bce = F.binary_cross_entropy(pred, target)\n    pt = torch.exp(-bce)  # BUG\n    return ((1 - pt) ** gamma * bce).mean()\n```\n\nThe correct focal loss uses pt = probability of the TRUE class. What is wrong?",
                'opts' => [
                    ['gamma should be set to 1, not 2', false],
                    ['pt should be pred (the model\'s output probability for the true class), not exp(−bce)', true],
                    ['F.binary_cross_entropy should be replaced with F.mse_loss', false],
                    ['.mean() should be .sum()', false],
                ],
            ],
            [
                'q' => "For a multi-class classification problem with 5 classes, which combination of output activation and loss is correct?\n\n```python\n# Option A:\noutput = F.softmax(logits, dim=1)\nloss = F.cross_entropy(output, targets)\n\n# Option B:\noutput = logits  # raw logits\nloss = F.cross_entropy(output, targets)\n```",
                'opts' => [
                    ['Option A — softmax should always be applied before cross_entropy', false],
                    ['Option B — F.cross_entropy already applies log-softmax internally; applying softmax first causes double-softmax', true],
                    ['Both are equivalent and produce the same gradients', false],
                    ['Option A is correct because cross_entropy requires probabilities', false],
                ],
            ],

            // ── TRANSFORMER IMPLEMENTATION ────────────────────────────────
            [
                'q' => "This self-attention implementation has a dimension bug:\n\n```python\ndef attention(Q, K, V):\n    d_k = Q.shape[-1]\n    scores = torch.matmul(Q, K)  # BUG\n    scores = scores / math.sqrt(d_k)\n    weights = F.softmax(scores, dim=-1)\n    return torch.matmul(weights, V)\n```",
                'opts' => [
                    ['The softmax should be applied along dim=0, not dim=-1', false],
                    ['K should be transposed: torch.matmul(Q, K.transpose(-2, -1)) — otherwise dimensions don\'t match for Q×K', true],
                    ['The scaling factor should be d_k, not sqrt(d_k)', false],
                    ['V should be transposed before the final matmul', false],
                ],
            ],
            [
                'q' => "In a Transformer encoder, the order of operations in each block is:\n\n```\nBlock A:  x → LayerNorm → Attention → Add(x) → LayerNorm → FFN → Add\nBlock B:  x → Attention → Add(x) → LayerNorm → FFN → Add(x) → LayerNorm\n```\n\nWhich is the 'Pre-LN' (pre-layer normalization) variant, and why is it preferred for very deep Transformers?",
                'opts' => [
                    ['Block B (Post-LN) — normalizing after the residual provides more stable gradients', false],
                    ['Block A (Pre-LN) — normalizing before the sub-layer keeps gradient magnitudes stable across many layers', true],
                    ['Both are equivalent in practice', false],
                    ['Block A is only preferred for encoder, Block B for decoder', false],
                ],
            ],
            [
                'q' => "Masked self-attention in the Transformer decoder applies a causal mask. This mask sets attention scores to −∞ for future positions before the softmax. Why?",
                'opts' => [
                    ['To prevent the model from attending to padding tokens', false],
                    ['To enforce autoregressive generation — position i can only attend to positions ≤ i, not future tokens', true],
                    ['To improve computational efficiency by attending to fewer tokens', false],
                    ['To implement dropout in the attention mechanism', false],
                ],
            ],

            // ── GAN DEBUGGING ─────────────────────────────────────────────
            [
                'q' => "A GAN generator produces blurry, low-quality images and the discriminator loss drops to near zero. This is a sign of:\n\n```\nDiscriminator loss: 0.002\nGenerator loss: 12.4 (and rising)\n```",
                'opts' => [
                    ['Mode collapse — the generator is producing diverse outputs the discriminator can\'t handle', false],
                    ['The discriminator has become too strong too fast — the generator can\'t improve because the feedback signal saturates', true],
                    ['The generator\'s learning rate is too low', false],
                    ['The generator architecture has too few layers', false],
                ],
            ],
            [
                'q' => "The Wasserstein GAN (WGAN) replaces the standard GAN loss to address training instability. The key change is:\n\n```python\n# Standard GAN discriminator loss:\nloss_D = -torch.mean(torch.log(D(real))) - torch.mean(torch.log(1 - D(fake)))\n\n# WGAN critic loss:\nloss_D = -torch.mean(D(real)) + torch.mean(D(fake))\n```\n\nWGAN requires the critic to be:",
                'opts' => [
                    ['A sigmoid output bounded between 0 and 1', false],
                    ['A 1-Lipschitz function — enforced via weight clipping or gradient penalty', true],
                    ['Trained with cross-entropy loss, not Wasserstein distance', false],
                    ['Frozen during generator training', false],
                ],
            ],

            // ── RNN / LSTM DEBUGGING ──────────────────────────────────────
            [
                'q' => "This LSTM training loop gives NaN losses after a few steps:\n\n```python\nfor X, y in sequence_loader:\n    h0 = torch.zeros(1, batch_size, hidden_size)\n    c0 = torch.zeros(1, batch_size, hidden_size)\n    out, _ = lstm(X, (h0, c0))\n    loss = criterion(out[:, -1, :], y)\n    loss.backward()\n    optimizer.step()  # No gradient clipping\n    optimizer.zero_grad()\n```\n\nWhat is the most likely cause?",
                'opts' => [
                    ['h0 and c0 should be initialized with random values, not zeros', false],
                    ['Exploding gradients through time — add torch.nn.utils.clip_grad_norm_() before optimizer.step()', true],
                    ['out[:, -1, :] selects the wrong time step — use out[:, 0, :]', false],
                    ['LSTM does not support batch processing', false],
                ],
            ],

            [
                'q' => "A bidirectional LSTM has hidden_size = 128. The output at each time step has dimension:",
                'opts' => [
                    ['128 — same as hidden size', false],
                    ['256 — forward (128) + backward (128) concatenated', true],
                    ['64 — the hidden size is halved for each direction', false],
                    ['512 — doubled for each direction and each gate', false],
                ],
            ],

            // ── REGULARIZATION ADVANCED ───────────────────────────────────
            [
                'q' => "Label smoothing replaces hard one-hot labels (e.g., [0, 1, 0]) with soft labels (e.g., [0.05, 0.9, 0.05] with ε=0.1). The benefit is:\n\nPreventing the model from becoming:",
                'opts' => [
                    ['Too regularized — label smoothing makes the model less confident overall', false],
                    ['Overconfident — the model is penalized for assigning 100% probability to any class, improving calibration', true],
                    ['Underfitted — the model sees fewer examples of the correct class', false],
                    ['Too dependent on the learning rate scheduler', false],
                ],
            ],
            [
                'q' => "MixUp data augmentation creates training samples as:\n\nx̃ = λ·xᵢ + (1−λ)·xⱼ\nỹ = λ·yᵢ + (1−λ)·yⱼ\n\nwhere λ ~ Beta(α, α). This technique primarily improves:",
                'opts' => [
                    ['Training speed by reducing the number of unique samples needed', false],
                    ['Generalization by training on convex combinations of examples, discouraging sharp decision boundaries', true],
                    ['Memory efficiency by mixing two samples into one', false],
                    ['Gradient stability by reducing the loss landscape curvature', false],
                ],
            ],

            // ── ARCHITECTURE DECISIONS ────────────────────────────────────
            [
                'q' => "You are building an image classifier for 10 classes with 50,000 training images. Which architecture is the best starting point?\n\n```\nA) 3-layer fully connected network (MLP)\nB) VGG16 pretrained on ImageNet, fine-tuned\nC) A custom CNN from scratch with 20 layers\nD) A single convolutional layer + dense head\n```",
                'opts' => [
                    ['A — MLPs are the simplest and most interpretable', false],
                    ['B — pretrained CNNs provide strong learned features and dramatically reduce training data requirements', true],
                    ['C — deep custom CNNs always outperform pretrained models on new tasks', false],
                    ['D — a single conv layer is sufficient for most classification tasks', false],
                ],
            ],
            [
                'q' => "ViT (Vision Transformer) patches an input image of 224×224 into 16×16 patches. How many patch tokens are fed into the Transformer?",
                'opts' => [
                    ['14', false],
                    ['196', true],
                    ['256', false],
                    ['784', false],
                ],
            ],
            [
                'q' => "Depthwise Separable Convolution (used in MobileNet) reduces computation by:\n\n```\nStandard conv: in_C × out_C × K × K (multiply-adds per output element)\nDepthwise sep: in_C × K × K + in_C × out_C\n```\n\nFor in_C=256, out_C=256, K=3, the reduction factor is approximately:",
                'opts' => [
                    ['2×', false],
                    ['9×', true],
                    ['256×', false],
                    ['0.5×', false],
                ],
            ],

            // ── OPTIMIZATION DEBUGGING ────────────────────────────────────
            [
                'q' => "A model's loss oscillates wildly and never converges:\n\n```python\noptimizer = torch.optim.SGD(model.parameters(), lr=10.0)\n```\n\nThe most direct fix is:",
                'opts' => [
                    ['Switch to a larger batch size', false],
                    ['Reduce the learning rate to a range like 1e-3 to 1e-2 — the current lr=10.0 causes the optimizer to overshoot', true],
                    ['Add more layers to the model', false],
                    ['Use a different loss function', false],
                ],
            ],
            [
                'q' => "The loss plateaus very early in training and does not decrease further:\n\n```python\noptimizer = torch.optim.Adam(model.parameters(), lr=1e-8)\n```\n\nThe problem is:",
                'opts' => [
                    ['The learning rate is too high — reduce it further', false],
                    ['The learning rate is too low — updates are so tiny that the optimizer is essentially frozen', true],
                    ['Adam is not compatible with this model architecture', false],
                    ['The model needs gradient clipping to escape the plateau', false],
                ],
            ],
            [
                'q' => "Warm-up in learning rate scheduling means:\n\nStarting with a very low learning rate and linearly increasing it to the target rate over the first N steps.\n\nThis is most important for Transformer training because:",
                'opts' => [
                    ['Transformers require more epochs than CNNs, so warm-up saves time', false],
                    ['At initialization, large unstable gradients can corrupt Adam\'s moment estimates — warm-up prevents destructive early updates', true],
                    ['Transformers use different activation functions that need lower gradients initially', false],
                    ['Warm-up prevents positional encoding from dominating early training', false],
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

        $this->command->info("✅ Done! 30 questions seeded for Module 17 — Introduction to Deep Learning (Advanced).");
    }
}