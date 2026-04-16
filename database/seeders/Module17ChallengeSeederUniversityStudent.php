<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module17ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 17 — Introduction to Deep Learning (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Deep Learning',
            'description'           => 'Apply deep learning concepts analytically — trace forward passes, reason about activation functions, interpret loss curves, and connect architecture choices to outcomes. Light math and formula tracing required.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 750,
            'order_index'           => 17,
        ]);

        $this->command->info("Seeding 30 university-level questions...");

        $qaData = [

            // ── NEURON COMPUTATIONS ───────────────────────────────────────
            [
                'q' => "A single neuron receives two inputs: x₁ = 2, x₂ = 3.\nWeights: w₁ = 0.5, w₂ = −1.0, bias b = 1.\n\nCompute the weighted sum z = w₁x₁ + w₂x₂ + b.",
                'opts' => [
                    ['z = −1.0', false],
                    ['z = 0.0', true],
                    ['z = 2.0', false],
                    ['z = −2.0', false],
                ],
            ],
            [
                'q' => "Using the neuron from above with z = 0.0, apply the ReLU activation:\nReLU(z) = max(0, z)\n\nWhat is the neuron's output?",
                'opts' => [
                    ['−1.0', false],
                    ['1.0', false],
                    ['0.0', true],
                    ['0.5', false],
                ],
            ],
            [
                'q' => "Apply the Sigmoid activation to z = 0:\nσ(z) = 1 / (1 + e^(−z))\n\nWhat is σ(0)?",
                'opts' => [
                    ['0.0', false],
                    ['1.0', false],
                    ['0.5', true],
                    ['0.73', false],
                ],
            ],
            [
                'q' => "The Sigmoid function outputs values in which range?",
                'opts' => [
                    ['(−∞, +∞)', false],
                    ['(−1, 1)', false],
                    ['(0, 1)', true],
                    ['(0, +∞)', false],
                ],
            ],
            [
                'q' => "The tanh activation function outputs values in which range?",
                'opts' => [
                    ['(0, 1)', false],
                    ['(−1, 1)', true],
                    ['(0, +∞)', false],
                    ['(−∞, +∞)', false],
                ],
            ],
            [
                'q' => "Which activation function suffers from the 'dying ReLU' problem — where neurons permanently output 0 for all inputs?",
                'opts' => [
                    ['Sigmoid', false],
                    ['tanh', false],
                    ['ReLU', true],
                    ['Softmax', false],
                ],
            ],

            // ── FORWARD PASS TRACING ──────────────────────────────────────
            [
                'q' => "A network has: Input layer (2 neurons) → Hidden layer (1 neuron, ReLU) → Output layer (1 neuron, Sigmoid).\n\nInput: [1, −2]\nHidden weights: [0.5, 0.5], bias = 0\nHidden z = 0.5×1 + 0.5×(−2) + 0 = ?\nHidden output after ReLU = ?",
                'opts' => [
                    ['z = −0.5, output = 0', true],
                    ['z = 0.5, output = 0.5', false],
                    ['z = −0.5, output = −0.5', false],
                    ['z = 1.0, output = 1.0', false],
                ],
            ],
            [
                'q' => "For a classification task with 3 classes, which activation function should be used in the OUTPUT layer?",
                'opts' => [
                    ['ReLU', false],
                    ['Sigmoid', false],
                    ['Softmax', true],
                    ['tanh', false],
                ],
            ],
            [
                'q' => "Softmax converts a vector of raw scores (logits) [2.0, 1.0, 0.1] into probabilities. What property do these output probabilities always satisfy?",
                'opts' => [
                    ['Each value is between −1 and 1', false],
                    ['All values sum to exactly 1.0', true],
                    ['Each value is greater than 0.5', false],
                    ['The largest value is always 1.0', false],
                ],
            ],

            // ── LOSS FUNCTIONS ────────────────────────────────────────────
            [
                'q' => "Mean Squared Error (MSE) is defined as:\nMSE = (1/n) × Σ(y_pred − y_true)²\n\nFor predictions [3, 5] and true values [2, 4], what is the MSE?",
                'opts' => [
                    ['0.5', false],
                    ['1.0', true],
                    ['2.0', false],
                    ['4.0', false],
                ],
            ],
            [
                'q' => "Which loss function is most appropriate for a binary classification task (e.g., spam vs. not spam)?",
                'opts' => [
                    ['Mean Squared Error (MSE)', false],
                    ['Binary Cross-Entropy Loss', true],
                    ['Hinge Loss', false],
                    ['Mean Absolute Error (MAE)', false],
                ],
            ],
            [
                'q' => "Cross-entropy loss for a single example is:\nL = −[y × log(p) + (1−y) × log(1−p)]\n\nIf y = 1 (true class) and p = 0.9 (predicted probability), the loss is:\nL = −log(0.9) ≈",
                'opts' => [
                    ['0.046', false],
                    ['0.105', true],
                    ['0.9', false],
                    ['1.0', false],
                ],
            ],

            // ── GRADIENT DESCENT ──────────────────────────────────────────
            [
                'q' => "In gradient descent, weights are updated using:\nw = w − η × ∂L/∂w\n\nIf w = 0.8, learning rate η = 0.1, and gradient ∂L/∂w = 0.5, what is the new weight?",
                'opts' => [
                    ['0.85', false],
                    ['0.75', true],
                    ['0.70', false],
                    ['0.80', false],
                ],
            ],
            [
                'q' => "A learning rate that is too HIGH can cause:",
                'opts' => [
                    ['Training to be very slow and stable', false],
                    ['The loss to oscillate wildly or diverge — never converging', true],
                    ['The model to underfit the training data', false],
                    ['Neurons to become permanently inactive', false],
                ],
            ],
            [
                'q' => "What is the difference between Stochastic Gradient Descent (SGD) and Batch Gradient Descent?",
                'opts' => [
                    ['SGD uses one training example per update; Batch GD uses the entire dataset per update', true],
                    ['SGD uses the entire dataset; Batch GD uses one example', false],
                    ['SGD updates weights backwards; Batch GD updates them forwards', false],
                    ['There is no difference — they are the same algorithm', false],
                ],
            ],
            [
                'q' => "Mini-batch gradient descent is preferred in practice because:",
                'opts' => [
                    ['It uses only one sample, making it the fastest possible', false],
                    ['It balances the efficiency of batch GD with the noise benefits of SGD', true],
                    ['It eliminates the need for a learning rate', false],
                    ['It only works with convolutional networks', false],
                ],
            ],

            // ── BACKPROPAGATION ───────────────────────────────────────────
            [
                'q' => "Backpropagation computes gradients using the chain rule. For a function L(f(g(w))), the chain rule gives:\n∂L/∂w = (∂L/∂f) × (∂f/∂g) × (∂g/∂w)\n\nThis is important because it allows gradients to flow:",
                'opts' => [
                    ['Only through the last layer', false],
                    ['From the output layer back through all layers to the input', true],
                    ['Only through hidden layers', false],
                    ['Randomly across layers', false],
                ],
            ],
            [
                'q' => "The 'vanishing gradient' problem occurs when:",
                'opts' => [
                    ['The learning rate is set too high', false],
                    ['Gradients become extremely small as they propagate backward, making early layers learn very slowly', true],
                    ['The model has too few layers', false],
                    ['The dataset is too large', false],
                ],
            ],
            [
                'q' => "Which activation function helps address the vanishing gradient problem compared to Sigmoid?",
                'opts' => [
                    ['tanh', false],
                    ['ReLU', true],
                    ['Softmax', false],
                    ['Linear', false],
                ],
            ],

            // ── REGULARIZATION ────────────────────────────────────────────
            [
                'q' => "L2 regularization adds a penalty term to the loss:\nL_total = L_original + λ × Σ(w²)\n\nThis penalty encourages the model to:",
                'opts' => [
                    ['Make weights as large as possible', false],
                    ['Keep weights small, reducing the risk of overfitting', true],
                    ['Set all weights to exactly 0', false],
                    ['Use fewer neurons', false],
                ],
            ],
            [
                'q' => "L1 regularization (Lasso) differs from L2 (Ridge) in that L1:",
                'opts' => [
                    ['Penalizes the square of weights', false],
                    ['Can drive some weights to exactly zero, producing sparse models', true],
                    ['Always produces worse results than L2', false],
                    ['Only applies to the output layer', false],
                ],
            ],
            [
                'q' => "Dropout with rate p = 0.5 during training means:",
                'opts' => [
                    ['50% of the training data is discarded', false],
                    ['Each neuron has a 50% chance of being set to zero on each forward pass', true],
                    ['The learning rate is halved', false],
                    ['The network is split into two halves that train separately', false],
                ],
            ],

            // ── CNNs ──────────────────────────────────────────────────────
            [
                'q' => "In a CNN, a convolution operation with a 3×3 filter applied to a 5×5 image (no padding, stride 1) produces an output of size:",
                'opts' => [
                    ['5×5', false],
                    ['3×3', true],
                    ['4×4', false],
                    ['6×6', false],
                ],
            ],
            [
                'q' => "Output size formula for a convolution:\nOutput = (Input − Filter + 2×Padding) / Stride + 1\n\nInput = 7, Filter = 3, Padding = 1, Stride = 1:\nOutput = ?",
                'opts' => [
                    ['5', false],
                    ['7', true],
                    ['6', false],
                    ['8', false],
                ],
            ],
            [
                'q' => "What is the purpose of a pooling layer in a CNN?",
                'opts' => [
                    ['To add more trainable parameters to the network', false],
                    ['To downsample feature maps, reducing spatial dimensions and computation', true],
                    ['To normalize the activations across channels', false],
                    ['To apply dropout to convolutional filters', false],
                ],
            ],

            // ── RNNs & LSTMS ──────────────────────────────────────────────
            [
                'q' => "What makes an RNN different from a standard feedforward network?",
                'opts' => [
                    ['RNNs have no hidden layers', false],
                    ['RNNs have recurrent connections that pass hidden state from one time step to the next', true],
                    ['RNNs only work with image data', false],
                    ['RNNs do not use backpropagation', false],
                ],
            ],
            [
                'q' => "LSTMs (Long Short-Term Memory networks) were designed to solve which RNN problem?",
                'opts' => [
                    ['RNNs being too slow to train on CPUs', false],
                    ['The vanishing gradient problem, which prevents RNNs from learning long-range dependencies', true],
                    ['RNNs being unable to process images', false],
                    ['RNNs using too much memory', false],
                ],
            ],

            // ── TRANSFORMERS ──────────────────────────────────────────────
            [
                'q' => "The key innovation of the Transformer architecture is:",
                'opts' => [
                    ['Using convolutional filters on text data', false],
                    ['The self-attention mechanism, which allows each token to attend to all other tokens simultaneously', true],
                    ['Replacing all weights with binary values', false],
                    ['Processing sequences strictly left-to-right like an RNN', false],
                ],
            ],
            [
                'q' => "Self-attention computes three vectors for each token. What are they?",
                'opts' => [
                    ['Input, Output, Gradient', false],
                    ['Query (Q), Key (K), Value (V)', true],
                    ['Gate, Cell, Hidden', false],
                    ['Encoder, Decoder, Bottleneck', false],
                ],
            ],

            // ── TRANSFER LEARNING & GENERATIVE MODELS ────────────────────
            [
                'q' => "Fine-tuning a pre-trained model means:",
                'opts' => [
                    ['Training a new model entirely from scratch using the same architecture', false],
                    ['Continuing training on new task-specific data, often with a low learning rate', true],
                    ['Freezing all layers and adding no new layers', false],
                    ['Deleting the original weights and replacing them randomly', false],
                ],
            ],
            [
                'q' => "A Variational Autoencoder (VAE) differs from a standard Autoencoder in that the VAE:",
                'opts' => [
                    ['Uses a discriminator to evaluate generated images', false],
                    ['Encodes inputs into a probability distribution (mean and variance), enabling random sampling', true],
                    ['Has no decoder — only an encoder', false],
                    ['Can only be used for text data', false],
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

        $this->command->info("✅ Done! 30 questions seeded for Module 17 — Introduction to Deep Learning (University Student).");
    }
}