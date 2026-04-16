<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module17ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 17 — Introduction to Deep Learning (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Deep Learning',
            'description'           => 'Solve multi-step deep learning problems — trace full forward and backward passes, compute parameter counts, reason about CNN and RNN architectures, and interpret attention mechanisms and training dynamics.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 1000,
            'order_index'           => 17,
        ]);

        $this->command->info("Seeding 30 intermediate questions...");

        $qaData = [

            // ── FULL FORWARD PASS ─────────────────────────────────────────
            [
                'q' => "Trace the full forward pass of this 2-layer network:\n\nInput: x = [1.0, 2.0]\nLayer 1: W = [[0.5, −0.5], [0.5, 0.5]], b = [0, 0], activation = ReLU\nLayer 2: W = [[1.0, 1.0]], b = [0], activation = None (linear)\n\nStep 1: z1 = W1 @ x + b1\nz1 = [0.5×1 + (−0.5)×2, 0.5×1 + 0.5×2] = [−0.5, 1.5]\nAfter ReLU: a1 = [0, 1.5]\n\nStep 2: z2 = W2 @ a1 = 1×0 + 1×1.5 = ?\nFinal output:",
                'opts' => [
                    ['0.0', false],
                    ['1.5', true],
                    ['3.0', false],
                    ['−0.5', false],
                ],
            ],
            [
                'q' => "For the same network above, if the true output is y = 2.0 and loss is MSE:\nL = (y_pred − y_true)² = (1.5 − 2.0)²\nWhat is the loss?",
                'opts' => [
                    ['0.5', false],
                    ['0.25', true],
                    ['1.0', false],
                    ['0.75', false],
                ],
            ],
            [
                'q' => "In the backward pass for the network above, the gradient of loss w.r.t. y_pred is:\n∂L/∂y_pred = 2 × (y_pred − y_true) = 2 × (1.5 − 2.0)\n\nWhat is this gradient?",
                'opts' => [
                    ['1.0', false],
                    ['−1.0', true],
                    ['0.5', false],
                    ['−0.5', false],
                ],
            ],

            // ── PARAMETER COUNTING ────────────────────────────────────────
            [
                'q' => "A fully connected (dense) layer has 128 input neurons and 64 output neurons. How many trainable parameters does this layer have (including biases)?",
                'opts' => [
                    ['128 × 64 = 8,192', false],
                    ['128 × 64 + 64 = 8,256', true],
                    ['128 + 64 = 192', false],
                    ['128 × 64 × 2 = 16,384', false],
                ],
            ],
            [
                'q' => "A CNN layer has:\n- Input channels: 3 (RGB)\n- Output channels (filters): 32\n- Filter size: 3×3\n\nHow many trainable weight parameters does this convolutional layer have (ignoring biases)?",
                'opts' => [
                    ['3 × 32 = 96', false],
                    ['3 × 3 × 32 = 288', false],
                    ['3 × 3 × 3 × 32 = 864', true],
                    ['3 × 3 = 9', false],
                ],
            ],
            [
                'q' => "Including biases for the CNN layer above (one bias per output channel), the total parameter count is:",
                'opts' => [
                    ['864', false],
                    ['864 + 32 = 896', true],
                    ['864 + 3 = 867', false],
                    ['864 × 2 = 1728', false],
                ],
            ],

            // ── OPTIMIZERS ───────────────────────────────────────────────
            [
                'q' => "The Adam optimizer maintains two moving averages per parameter. These are:",
                'opts' => [
                    ['The gradient and the loss', false],
                    ['The first moment (mean of gradients) and the second moment (mean of squared gradients)', true],
                    ['The weight and the bias', false],
                    ['The learning rate and the momentum coefficient', false],
                ],
            ],
            [
                'q' => "Momentum in SGD helps by:",
                'opts' => [
                    ['Increasing the learning rate after each epoch', false],
                    ['Accumulating a velocity in directions of persistent gradient, dampening oscillations', true],
                    ['Randomly shuffling the training data each iteration', false],
                    ['Adding L2 regularization automatically', false],
                ],
            ],
            [
                'q' => "A learning rate scheduler that reduces the learning rate by half every 10 epochs is called:",
                'opts' => [
                    ['Cosine annealing', false],
                    ['Step decay (StepLR)', true],
                    ['Warm restarts', false],
                    ['Cyclical learning rate', false],
                ],
            ],
            [
                'q' => "Cosine annealing learning rate schedules are preferred over fixed step decay because:",
                'opts' => [
                    ['They always use a higher learning rate throughout training', false],
                    ['They smoothly reduce the learning rate, allowing the optimizer to escape local minima via restarts', true],
                    ['They increase the learning rate as training progresses', false],
                    ['They eliminate the need for a momentum parameter', false],
                ],
            ],

            // ── BATCH NORMALIZATION ───────────────────────────────────────
            [
                'q' => "Batch Normalization (BN) normalizes activations to have:\n\nMean = 0 and Variance = 1 across the batch.\n\nBN then applies learnable parameters γ (scale) and β (shift). What is the output?\n\nBN(x) = γ × x̂ + β, where x̂ is the normalized input.",
                'opts' => [
                    ['The output is always zero-mean and unit variance', false],
                    ['The output is re-scaled and re-shifted by learned γ and β, restoring expressiveness', true],
                    ['The output is clipped between 0 and 1', false],
                    ['The output is the same as the input', false],
                ],
            ],
            [
                'q' => "Batch Normalization helps training by:",
                'opts' => [
                    ['Reducing the number of parameters in the model', false],
                    ['Reducing internal covariate shift, allowing higher learning rates and faster convergence', true],
                    ['Preventing gradient explosion by clipping gradients', false],
                    ['Replacing dropout for regularization in all cases', false],
                ],
            ],
            [
                'q' => "During inference (test time), Batch Normalization uses:",
                'opts' => [
                    ['The current batch statistics (mean and variance)', false],
                    ['Running averages of mean and variance computed during training', true],
                    ['Random noise as mean and variance', false],
                    ['γ = 1 and β = 0 always', false],
                ],
            ],

            // ── CNN ARCHITECTURE ──────────────────────────────────────────
            [
                'q' => "A ResNet (Residual Network) introduces 'skip connections' (shortcuts). What problem do these skip connections solve?",
                'opts' => [
                    ['They reduce the number of parameters in the network', false],
                    ['They allow gradients to flow directly to earlier layers, solving the vanishing gradient problem in very deep networks', true],
                    ['They speed up inference by skipping slow layers', false],
                    ['They replace the need for batch normalization', false],
                ],
            ],
            [
                'q' => "In a residual block, the output is computed as:\nOutput = F(x, W) + x\n\nIf F(x, W) = 0 (the block learns nothing), the output is:",
                'opts' => [
                    ['0', false],
                    ['x (the identity — input passes through unchanged)', true],
                    ['2x', false],
                    ['−x', false],
                ],
            ],
            [
                'q' => "Global Average Pooling (GAP) reduces a feature map of shape (H, W, C) to shape:",
                'opts' => [
                    ['(H/2, W/2, C)', false],
                    ['(1, 1, C)', true],
                    ['(H, W, 1)', false],
                    ['(C, C, C)', false],
                ],
            ],

            // ── RNNs & LSTMs ──────────────────────────────────────────────
            [
                'q' => "An LSTM cell has three gates. What are they?",
                'opts' => [
                    ['Read, Write, Execute', false],
                    ['Forget gate, Input gate, Output gate', true],
                    ['Encoder gate, Decoder gate, Attention gate', false],
                    ['Query gate, Key gate, Value gate', false],
                ],
            ],
            [
                'q' => "In an LSTM, the 'forget gate' decides:",
                'opts' => [
                    ['What new information to write into the cell state', false],
                    ['How much of the previous cell state to retain or discard', true],
                    ['What information to output from the cell', false],
                    ['How much the hidden state should influence the input', false],
                ],
            ],
            [
                'q' => "Backpropagation Through Time (BPTT) in RNNs means gradients are computed:\n\nAcross all time steps in the sequence, which for very long sequences causes:",
                'opts' => [
                    ['Faster convergence due to longer gradient paths', false],
                    ['The vanishing or exploding gradient problem, making it hard to learn long-range dependencies', true],
                    ['The model to forget all short-range dependencies', false],
                    ['The model to require more training data than feedforward networks', false],
                ],
            ],

            // ── ATTENTION & TRANSFORMERS ──────────────────────────────────
            [
                'q' => "Self-attention scores are computed as:\nAttention(Q, K, V) = softmax(QKᵀ / √d_k) × V\n\nWhy is the scaling factor √d_k used?",
                'opts' => [
                    ['To increase the magnitude of dot products for faster training', false],
                    ['To prevent dot products from becoming very large in high dimensions, which would push softmax into saturation', true],
                    ['To normalize Q and K to unit vectors', false],
                    ['To make the attention scores sum to the sequence length', false],
                ],
            ],
            [
                'q' => "Multi-head attention runs h attention functions in parallel. The outputs are then:",
                'opts' => [
                    ['Averaged together to get the final output', false],
                    ['Concatenated and projected through a linear layer', true],
                    ['Added together element-wise', false],
                    ['Passed through separate output layers for each head', false],
                ],
            ],
            [
                'q' => "Positional encoding is added to token embeddings in a Transformer because:",
                'opts' => [
                    ['Transformers have recurrent connections that need positional signals', false],
                    ['Self-attention is permutation-invariant — it has no inherent sense of word order', true],
                    ['Token embeddings are too small without positional information', false],
                    ['Positional encodings replace the need for multi-head attention', false],
                ],
            ],

            // ── TRANSFER LEARNING ─────────────────────────────────────────
            [
                'q' => "When fine-tuning a pre-trained ResNet for a new classification task with very few labelled examples, the recommended approach is:",
                'opts' => [
                    ['Train the entire network from scratch with a high learning rate', false],
                    ['Freeze early convolutional layers (which capture generic features) and only train the later layers and new head', true],
                    ['Freeze all layers and only change the output layer weights randomly', false],
                    ['Use a very high learning rate to quickly adapt all layers', false],
                ],
            ],
            [
                'q' => "Domain adaptation in transfer learning refers to:",
                'opts' => [
                    ['Adapting a model to process different file types', false],
                    ['Adjusting a model trained on one data distribution to perform well on a different (but related) distribution', true],
                    ['Adding more layers to the pre-trained model', false],
                    ['Reducing the number of classes in the output layer', false],
                ],
            ],

            // ── GENERATIVE MODELS ─────────────────────────────────────────
            [
                'q' => "In a GAN, training is a min-max game:\nmin_G max_D [ E[log D(x)] + E[log(1 − D(G(z)))] ]\n\nThe generator G is trained to:",
                'opts' => [
                    ['Maximize D(G(z)) — make the discriminator classify fake images as real', true],
                    ['Minimize D(x) — make the discriminator misclassify real images', false],
                    ['Maximize log(1 − D(G(z))) — make the discriminator confident fakes are fake', false],
                    ['Minimize the reconstruction loss between x and G(z)', false],
                ],
            ],
            [
                'q' => "Mode collapse in GAN training occurs when:",
                'opts' => [
                    ['The discriminator becomes perfect and stops improving', false],
                    ['The generator produces only a narrow variety of outputs, ignoring most of the target data distribution', true],
                    ['Both networks converge to the same loss value', false],
                    ['The training data is too small for the generator to learn', false],
                ],
            ],
            [
                'q' => "A VAE's loss function has two components:\n1. Reconstruction loss (how well the decoder recreates input)\n2. KL divergence term\n\nThe KL divergence term encourages the latent space to:",
                'opts' => [
                    ['Be as large as possible to store more information', false],
                    ['Follow a standard Normal distribution N(0,1), keeping the latent space smooth and continuous', true],
                    ['Match the output space exactly', false],
                    ['Be sparse — most values close to zero', false],
                ],
            ],

            // ── TRAINING DYNAMICS ─────────────────────────────────────────
            [
                'q' => "A training loss that decreases but a validation loss that increases over epochs is a classic sign of:",
                'opts' => [
                    ['Underfitting', false],
                    ['Overfitting', true],
                    ['A correct learning rate', false],
                    ['A batch size that is too small', false],
                ],
            ],
            [
                'q' => "Early stopping monitors validation loss and stops training when:",
                'opts' => [
                    ['Training loss reaches zero', false],
                    ['Validation loss stops improving (or starts worsening) for a set number of epochs (patience)', true],
                    ['The number of epochs equals the batch size', false],
                    ['The learning rate decays below a threshold', false],
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

        $this->command->info("✅ Done! 30 questions seeded for Module 17 — Introduction to Deep Learning (Intermediate).");
    }
}