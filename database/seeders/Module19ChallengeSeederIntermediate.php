<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module19ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 19 — Introduction to Artificial Intelligence (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Artificial Intelligence',
            'description'           => 'Solve multi-step AI problems — compute performance metrics, trace training dynamics, analyse architecture design choices, reason through RL value functions, and compare modelling approaches across all major AI subfields.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 1000,
            'order_index'           => 19,
        ]);

        $this->command->info("Seeding 30 intermediate-level questions...");

        $qaData = [

            // ── ML METRICS MULTI-STEP ─────────────────────────────────────
            [
                'q' => "A binary classifier produces the following confusion matrix:\n\n|                | Predicted Positive | Predicted Negative |\n|----------------|--------------------|--------------------|\n| Actual Positive | 90 (TP)            | 10 (FN)            |\n| Actual Negative | 20 (FP)            | 80 (TN)            |\n\nPrecision = TP / (TP + FP). What is the precision?",
                'opts' => [
                    ['90 / (90 + 10) = 0.90', false],
                    ['90 / (90 + 20) = 0.818', true],
                    ['80 / (80 + 20) = 0.80', false],
                    ['90 / (90 + 80) = 0.529', false],
                ],
            ],
            [
                'q' => "Using the same confusion matrix:\nRecall = TP / (TP + FN)\n\nCompute the Recall:",
                'opts' => [
                    ['90 / (90 + 20) = 0.818', false],
                    ['80 / (80 + 10) = 0.889', false],
                    ['90 / (90 + 10) = 0.90', true],
                    ['90 / (90 + 80) = 0.529', false],
                ],
            ],
            [
                'q' => "F1-score = 2 × (Precision × Recall) / (Precision + Recall)\n\nUsing Precision ≈ 0.818 and Recall = 0.90 from above:\nF1 ≈",
                'opts' => [
                    ['0.857', true],
                    ['0.859', false],
                    ['0.900', false],
                    ['0.818', false],
                ],
            ],
            [
                'q' => "A fraud detection model has 99% accuracy on a dataset where 99% of transactions are legitimate. A naive model that ALWAYS predicts 'legitimate' would achieve the same 99% accuracy. This demonstrates why accuracy alone is misleading when:\n\nWhat metric should be used instead?",
                'opts' => [
                    ['R-squared — it accounts for class probability', false],
                    ['Precision-Recall AUC or F1-score, which focus on the minority positive class', true],
                    ['Mean Absolute Error, since fraud involves monetary values', false],
                    ['Accuracy is always the right metric regardless of imbalance', false],
                ],
            ],

            // ── SUPERVISED LEARNING: BIAS-VARIANCE ────────────────────────
            [
                'q' => "The bias-variance trade-off states:\n\nExpected Error = Bias² + Variance + Irreducible Noise\n\nA high-degree polynomial model has low bias but high variance. Which regularization technique directly addresses high variance?",
                'opts' => [
                    ['Increasing the model complexity further', false],
                    ['Adding L2 (Ridge) regularization to penalize large weights and shrink the model', true],
                    ['Removing the training labels', false],
                    ['Switching from gradient descent to random search', false],
                ],
            ],
            [
                'q' => "Cross-validation (k-fold) improves over a single train/test split because:\n\nWith k=5 folds, each data point is used:\n- As training data in 4 folds\n- As validation data in exactly 1 fold",
                'opts' => [
                    ['It is always faster than a single split', false],
                    ['It provides a more reliable performance estimate by averaging over k different validation sets', true],
                    ['It prevents overfitting by training on more data', false],
                    ['It increases the size of the dataset by a factor of k', false],
                ],
            ],

            // ── UNSUPERVISED LEARNING ─────────────────────────────────────
            [
                'q' => "K-Means clustering assigns each data point to the nearest centroid. The algorithm iterates:\n1. Assign each point to nearest centroid\n2. Recompute centroids as mean of assigned points\n3. Repeat until convergence\n\nWhat does 'convergence' mean here?",
                'opts' => [
                    ['The total number of iterations reaches k', false],
                    ['Centroid positions no longer change between iterations', true],
                    ['All clusters have the same number of points', false],
                    ['The loss function exceeds a threshold', false],
                ],
            ],
            [
                'q' => "Principal Component Analysis (PCA) reduces dimensionality by:\n\nProjecting data onto directions of maximum variance (principal components).\n\nIf the first two principal components explain 85% of the variance in a 100-feature dataset, using them means:",
                'opts' => [
                    ['Losing all information — 100 features must always be used', false],
                    ['Retaining 85% of the data variance in 2 dimensions instead of 100 — a significant compression with moderate information loss', true],
                    ['Increasing the variance of the dataset', false],
                    ['The remaining 15% variance is always noise and can be ignored safely', false],
                ],
            ],

            // ── NEURAL NETWORK TRAINING ───────────────────────────────────
            [
                'q' => "A model is trained with gradient descent:\nw_new = w_old − η × ∂L/∂w\n\nIf w = 2.0, η = 0.1, and ∂L/∂w = −3.0, what is w_new?",
                'opts' => [
                    ['2.0 − 0.1 × (−3.0) = 2.3', true],
                    ['2.0 + 0.1 × 3.0 = 2.3 (same)', false],
                    ['2.0 − 0.1 × 3.0 = 1.7', false],
                    ['2.0 × 0.1 × (−3.0) = −0.6', false],
                ],
            ],
            [
                'q' => "Batch Normalization normalizes layer inputs to zero mean and unit variance, then applies learnable parameters γ and β. During TRAINING it uses batch statistics. During INFERENCE it uses:\n\nWhy does this matter?",
                'opts' => [
                    ['The current batch — inference should always mimic training', false],
                    ['Running averages of mean and variance computed during training — because inference may process a single sample with no batch statistics', true],
                    ['Random noise to simulate batch variation', false],
                    ['γ = 0 and β = 1 always during inference', false],
                ],
            ],
            [
                'q' => "The Adam optimizer updates weights using:\n- m = β₁m + (1−β₁)g  (first moment — mean of gradients)\n- v = β₂v + (1−β₂)g²  (second moment — mean of squared gradients)\n- w = w − η × m̂ / (√v̂ + ε)\n\nWhy is Adam often preferred over vanilla SGD?",
                'opts' => [
                    ['Adam ignores the gradient magnitude, making it more stable', false],
                    ['Adam adapts the learning rate per parameter using gradient history — faster convergence and less sensitivity to learning rate choice', true],
                    ['Adam always produces lower training loss than SGD', false],
                    ['Adam eliminates the need for any learning rate', false],
                ],
            ],

            // ── COMPUTER VISION ARCHITECTURE ─────────────────────────────
            [
                'q' => "A CNN applies a 3×3 convolution with 64 filters to a 224×224×3 input.\nOutput size formula: (W − F + 2P)/S + 1\n\nWith P=1 (same padding) and S=1:\nOutput spatial dimensions = ?",
                'opts' => [
                    ['222 × 222 × 64', false],
                    ['224 × 224 × 64', true],
                    ['226 × 226 × 64', false],
                    ['224 × 224 × 3', false],
                ],
            ],
            [
                'q' => "ResNet introduces skip (residual) connections:\nOutput = F(x, W) + x\n\nIf the residual branch F(x, W) learns the zero function, the block becomes an identity. This design specifically helps with which training problem?",
                'opts' => [
                    ['Overfitting in shallow networks', false],
                    ['Vanishing gradients in very deep networks — gradients can flow directly through the skip connection', true],
                    ['Class imbalance in image datasets', false],
                    ['Slow inference on CPU', false],
                ],
            ],
            [
                'q' => "Transfer learning with a pre-trained ResNet for a new classification task:\n\nYou freeze all layers except the final fully-connected head and train only the head on 500 labelled images.\n\nWhy freeze the early layers?",
                'opts' => [
                    ['Early layers are randomly initialized and would corrupt the features', false],
                    ['Early layers learned general features (edges, textures) on millions of images — fine-tuning them risks destroying valuable representations with only 500 examples', true],
                    ['Frozen layers train faster because their weights are cached', false],
                    ['Early layers always overfit when unfrozen', false],
                ],
            ],

            // ── NLP PIPELINE ──────────────────────────────────────────────
            [
                'q' => "TF-IDF (Term Frequency-Inverse Document Frequency) weights words by:\nTF-IDF(t, d) = TF(t, d) × log(N / df(t))\n\nA word that appears in almost every document will have a very LOW TF-IDF because:",
                'opts' => [
                    ['Its term frequency (TF) is always zero', false],
                    ['Its document frequency df(t) ≈ N, so log(N/df(t)) ≈ log(1) = 0 — the word carries little discriminating information', true],
                    ['N is always a negative number', false],
                    ['TF-IDF only counts words that appear in fewer than 10 documents', false],
                ],
            ],
            [
                'q' => "Self-attention computes:\nAttention(Q, K, V) = softmax(QKᵀ / √d_k) × V\n\nFor a sequence of length 10 with d_k = 64, the attention matrix QKᵀ has shape:",
                'opts' => [
                    ['10 × 64', false],
                    ['10 × 10', true],
                    ['64 × 64', false],
                    ['10 × 1', false],
                ],
            ],
            [
                'q' => "Named Entity Recognition (NER) is an NLP task that:\n\nGiven: 'Apple released the iPhone in San Francisco.'\nNER would identify:",
                'opts' => [
                    ['The grammatical structure (noun, verb, adjective) of each word', false],
                    ['"Apple" as ORG, "iPhone" as PRODUCT, "San Francisco" as LOC', true],
                    ['The sentiment of the sentence (positive/negative)', false],
                    ['Whether the sentence is a question or statement', false],
                ],
            ],

            // ── LLMs ─────────────────────────────────────────────────────
            [
                'q' => "Prompt engineering is the practice of:\n\nCarefully designing the input text given to an LLM to steer its output without changing the model's weights.\n\nWhich technique uses a few examples in the prompt itself to guide the model's response style?",
                'opts' => [
                    ['Zero-shot prompting', false],
                    ['Few-shot prompting', true],
                    ['Fine-tuning', false],
                    ['Retrieval-Augmented Generation', false],
                ],
            ],
            [
                'q' => "Retrieval-Augmented Generation (RAG) improves LLM factual accuracy by:\n\n1. Retrieving relevant documents from an external knowledge base\n2. Injecting those documents into the LLM's context\n3. Generating an answer grounded in retrieved facts\n\nRAG primarily addresses which LLM limitation?",
                'opts' => [
                    ['Slow inference speed on long sequences', false],
                    ['Knowledge cutoff — the model can access up-to-date information it was not trained on', true],
                    ['The model generating grammatically incorrect sentences', false],
                    ['The context window being too large', false],
                ],
            ],

            // ── GENERATIVE MODELS ─────────────────────────────────────────
            [
                'q' => "A GAN training loop has the following phases:\n1. Train Discriminator: maximize log D(x) + log(1 − D(G(z)))\n2. Train Generator: minimize log(1 − D(G(z)))\n\nIn practice, the generator is trained to maximize log(D(G(z))) instead. Why?",
                'opts' => [
                    ['Maximizing log D(G(z)) is mathematically equivalent but slower', false],
                    ['When G is weak, log(1−D(G(z))) saturates near 0 — maximizing log D(G(z)) gives stronger gradients early in training', true],
                    ['Minimizing log(1−D(G(z))) causes the discriminator to collapse', false],
                    ['The original formulation was a typo in the original GAN paper', false],
                ],
            ],
            [
                'q' => "The KL divergence in a VAE loss:\nKL(q(z|x) || p(z)) = KL(N(μ, σ²) || N(0, 1))\n\nThis term encourages the encoder to produce latent codes that:\n\n= −½ × Σ(1 + log(σ²) − μ² − σ²)",
                'opts' => [
                    ['Be as different from each other as possible to spread out in latent space', false],
                    ['Follow a standard Normal distribution — keeping the latent space smooth so that random sampling generates coherent outputs', true],
                    ['Have maximum variance to encode as much information as possible', false],
                    ['Match the exact distribution of the training images', false],
                ],
            ],

            // ── REINFORCEMENT LEARNING ────────────────────────────────────
            [
                'q' => "The Bellman equation defines the value of a state:\nV(s) = R(s) + γ × max_a[Σ P(s'|s,a) × V(s')]\n\nThe discount factor γ ∈ [0, 1] controls:",
                'opts' => [
                    ['How fast the agent learns', false],
                    ['How much the agent values future rewards relative to immediate rewards — γ close to 1 makes the agent far-sighted', true],
                    ['The probability of transitioning to the next state', false],
                    ['The number of actions available to the agent', false],
                ],
            ],
            [
                'q' => "Q-Learning updates the action-value function:\nQ(s, a) ← Q(s, a) + α[r + γ × max_a' Q(s', a') − Q(s, a)]\n\nThe term [r + γ × max_a' Q(s', a') − Q(s, a)] is called the:",
                'opts' => [
                    ['Policy gradient', false],
                    ['Temporal Difference (TD) error — the difference between estimated and bootstrapped value', true],
                    ['Entropy bonus', false],
                    ['Bellman residual norm', false],
                ],
            ],
            [
                'q' => "Deep Q-Networks (DQN) improved over tabular Q-Learning for games like Atari by:\n\nReplacing the Q-table with a neural network AND introducing which two key techniques?",
                'opts' => [
                    ['Dropout and Batch Normalization', false],
                    ['Experience Replay (storing past transitions) and a Target Network (a slowly-updated copy to stabilize training)', true],
                    ['L2 Regularization and Gradient Clipping', false],
                    ['Self-play and Monte Carlo Tree Search', false],
                ],
            ],

            // ── AI ETHICS & FAIRNESS ──────────────────────────────────────
            [
                'q' => "Demographic parity as a fairness metric requires:\n\nP(Ŷ=1 | A=0) = P(Ŷ=1 | A=1)\n\nwhere A is a protected attribute (e.g., gender). This means:",
                'opts' => [
                    ['The model must have the same accuracy for all groups', false],
                    ['The model must predict the positive outcome at the same rate for both groups regardless of actual outcome rates', true],
                    ['The model must use identical features for all groups', false],
                    ['The model must be retrained separately for each group', false],
                ],
            ],
            [
                'q' => "LIME (Local Interpretable Model-Agnostic Explanations) explains individual predictions by:\n\n1. Perturbing the input around the example of interest\n2. Fitting a simple interpretable model (e.g., linear) on the perturbed samples\n\nLIME provides explanations that are:",
                'opts' => [
                    ['Global — valid for the entire model across all inputs', false],
                    ['Local — valid only in the neighbourhood of the specific example being explained', true],
                    ['Causal — they prove what caused the prediction', false],
                    ['Exact — they perfectly replicate the black-box model globally', false],
                ],
            ],

            // ── AI IN THE REAL WORLD ──────────────────────────────────────
            [
                'q' => "A hospital uses an AI triage model trained on data from 2015–2020. In 2024, the model's accuracy drops significantly. The most likely cause is:",
                'opts' => [
                    ['The model was too small and needed more parameters', false],
                    ['Dataset shift — patient demographics, disease patterns, or treatment protocols changed between training and deployment', true],
                    ['The hospital switched from Linux to Windows servers', false],
                    ['The model was trained with too high a learning rate', false],
                ],
            ],
            [
                'q' => "MLOps (Machine Learning Operations) is the practice of:\n\nApplying DevOps principles to the ML lifecycle, including continuous integration, deployment, and monitoring of ML models in production.\n\nWhich step does NOT belong in a standard MLOps pipeline?",
                'opts' => [
                    ['Model versioning and experiment tracking', false],
                    ['Automated retraining when model performance degrades', false],
                    ['Replacing all engineers with AI agents permanently', true],
                    ['A/B testing new model versions against the production baseline', false],
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

        $this->command->info("✅ Done! 30 questions seeded for Module 19 — Introduction to Artificial Intelligence (Intermediate).");
    }
}