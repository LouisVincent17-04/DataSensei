<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module19ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 19 — Introduction to Artificial Intelligence (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Artificial Intelligence',
            'description'           => 'Apply AI concepts analytically — compare learning paradigms, trace algorithm behaviour, reason about model performance metrics, and connect real-world AI applications to their underlying techniques. Light logic and formula tracing required.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 750,
            'order_index'           => 19,
        ]);

        $this->command->info("Seeding 30 university-level questions...");

        $qaData = [

            // ── AI HISTORY & LANDSCAPE ────────────────────────────────────
            [
                'q' => 'The AI "winters" of the 1970s and 1980s were periods when AI research stalled. What was the primary cause?',
                'opts' => [
                    ['A lack of electricity to power early computers', false],
                    ['Unrealistic expectations followed by limited hardware, data, and algorithm capabilities', true],
                    ['Government bans on AI research in most countries', false],
                    ['A virus that destroyed all AI programs at the time', false],
                ],
            ],
            [
                'q' => 'Which development most directly contributed to the AI boom starting around 2012?',
                'opts' => [
                    ['The invention of the transistor', false],
                    ['The convergence of big data, powerful GPUs, and improved deep learning algorithms', true],
                    ['The release of the World Wide Web', false],
                    ['The creation of the first relational database', false],
                ],
            ],
            [
                'q' => 'Narrow AI (Weak AI) differs from General AI (Strong AI) in that Narrow AI:',
                'opts' => [
                    ['Can solve any problem a human can solve', false],
                    ['Is designed for a specific task and cannot generalize beyond it', true],
                    ['Has consciousness and emotions', false],
                    ['Learns continuously from all human experience simultaneously', false],
                ],
            ],
            [
                'q' => 'Which of the following is the BEST example of Narrow AI today?',
                'opts' => [
                    ['A human-like robot that can perform any job', false],
                    ['A recommendation algorithm that suggests movies on Netflix', true],
                    ['An AI that fully understands and experiences human emotions', false],
                    ['A system that independently invents new scientific theories', false],
                ],
            ],

            // ── ML FUNDAMENTALS ───────────────────────────────────────────
            [
                'q' => 'A model that performs very well on training data but poorly on new unseen data is said to be:',
                'opts' => [
                    ['Underfitting', false],
                    ['Overfitting', true],
                    ['Well-generalised', false],
                    ['Underpowered', false],
                ],
            ],
            [
                'q' => 'A model that is too simple to capture the underlying pattern in the data is said to be:',
                'opts' => [
                    ['Overfitting', false],
                    ['Regularized', false],
                    ['Underfitting', true],
                    ['Over-parameterized', false],
                ],
            ],
            [
                'q' => 'The train/validation/test split in machine learning serves what purpose?\n\n- Training set: fit the model\n- Validation set: tune hyperparameters\n- Test set: ?',
                'opts' => [
                    ['Train the model a second time with better parameters', false],
                    ['Provide a final unbiased evaluation of model performance on unseen data', true],
                    ['Balance the class distribution in the dataset', false],
                    ['Store backup copies of the training data', false],
                ],
            ],
            [
                'q' => 'Which metric is MOST appropriate for evaluating a binary classifier on a highly imbalanced dataset (e.g., 99% negative, 1% positive)?',
                'opts' => [
                    ['Accuracy — it is always the best metric', false],
                    ['F1-score or AUC-ROC, which account for class imbalance', true],
                    ['Mean Squared Error', false],
                    ['R-squared', false],
                ],
            ],
            [
                'q' => 'A k-Nearest Neighbors (k-NN) classifier with k=1 will always have:\n\n(Assuming the test point is from the training set)',
                'opts' => [
                    ['High bias and low variance', false],
                    ['Zero training error but potentially high test error (overfitting)', true],
                    ['High bias and high variance', false],
                    ['The same error on training and test data', false],
                ],
            ],
            [
                'q' => 'A Decision Tree algorithm splits data by choosing the feature that maximizes which quantity?',
                'opts' => [
                    ['Accuracy on the test set', false],
                    ['Information Gain (or equivalently minimizes impurity like Gini Index)', true],
                    ['The mean of the feature values', false],
                    ['The number of missing values in the feature', false],
                ],
            ],

            // ── NEURAL NETWORKS ───────────────────────────────────────────
            [
                'q' => 'In a feedforward neural network with:\n- Input: 4 features\n- Hidden layer: 8 neurons\n- Output: 1 neuron\n\nHow many weight parameters exist in the input-to-hidden connection (excluding biases)?',
                'opts' => [
                    ['4 + 8 = 12', false],
                    ['4 × 8 = 32', true],
                    ['8 × 1 = 8', false],
                    ['4 × 8 × 1 = 32 — same but for the wrong reason', false],
                ],
            ],
            [
                'q' => 'The sigmoid activation outputs σ(0) = 0.5 and σ(∞) → 1. When used in deep networks, the sigmoid has a key disadvantage:\n\nIts derivative σ\'(z) = σ(z)(1−σ(z)) has a maximum of:',
                'opts' => [
                    ['1.0 — no disadvantage', false],
                    ['0.25 — causing vanishing gradients in deep networks', true],
                    ['0.5 — useful for most tasks', false],
                    ['Infinity — causing exploding gradients', false],
                ],
            ],
            [
                'q' => 'Which activation function is preferred in hidden layers of deep networks to avoid the vanishing gradient problem?',
                'opts' => [
                    ['Sigmoid', false],
                    ['Tanh', false],
                    ['ReLU (Rectified Linear Unit)', true],
                    ['Softmax', false],
                ],
            ],

            // ── COMPUTER VISION ───────────────────────────────────────────
            [
                'q' => 'A CNN applies a convolution operation using a learned filter. What property of CNNs makes them efficient for images compared to fully-connected networks?',
                'opts' => [
                    ['CNNs process all pixels simultaneously in one step', false],
                    ['CNNs share weights across spatial locations — the same filter detects features anywhere in the image', true],
                    ['CNNs use fewer data points for training', false],
                    ['CNNs do not require labelled images', false],
                ],
            ],
            [
                'q' => 'Object detection models like YOLO differ from image classification models in that they:',
                'opts' => [
                    ['Only output a single class label for the whole image', false],
                    ['Output bounding box coordinates AND class labels for multiple objects in one pass', true],
                    ['Cannot process colour images', false],
                    ['Require 10× more training data than classifiers', false],
                ],
            ],
            [
                'q' => 'Image segmentation differs from object detection in that segmentation:',
                'opts' => [
                    ['Only draws bounding boxes around objects', false],
                    ['Assigns a class label to every individual pixel in the image', true],
                    ['Only works on grayscale images', false],
                    ['Is faster but less accurate than detection', false],
                ],
            ],

            // ── NLP ───────────────────────────────────────────────────────
            [
                'q' => 'The "Bag of Words" (BoW) model represents text as:',
                'opts' => [
                    ['A sequence that preserves the original word order', false],
                    ['A count (or frequency) of each word, ignoring order and grammar', true],
                    ['A list of words with their grammatical role', false],
                    ['A graph of relationships between words', false],
                ],
            ],
            [
                'q' => 'Word embeddings (like Word2Vec) represent words as:',
                'opts' => [
                    ['Integer IDs unique to each word', false],
                    ['Dense vectors in a continuous space where similar words are close together', true],
                    ['Boolean flags for each word in the vocabulary', false],
                    ['Compressed images of the word spelled out', false],
                ],
            ],
            [
                'q' => 'The attention mechanism in Transformers allows the model to:',
                'opts' => [
                    ['Process only one word at a time, left to right', false],
                    ['Weigh the relevance of all other words when encoding each word in a sequence', true],
                    ['Avoid using any positional information', false],
                    ['Replace the need for training data entirely', false],
                ],
            ],

            // ── LARGE LANGUAGE MODELS ─────────────────────────────────────
            [
                'q' => 'GPT-style LLMs are trained using which primary objective?',
                'opts' => [
                    ['Predicting whether a sentence is grammatically correct', false],
                    ['Predicting the next token (word/subword) given all previous tokens', true],
                    ['Classifying text into a fixed set of categories', false],
                    ['Translating every sentence into multiple languages simultaneously', false],
                ],
            ],
            [
                'q' => 'BERT (Bidirectional Encoder Representations from Transformers) differs from GPT in that BERT:',
                'opts' => [
                    ['Only processes text from left to right', false],
                    ['Is trained to predict masked tokens using context from both left AND right', true],
                    ['Cannot be fine-tuned on downstream tasks', false],
                    ['Was designed exclusively for image captioning', false],
                ],
            ],
            [
                'q' => '"Hallucination" in LLMs refers to:',
                'opts' => [
                    ['The model generating creative metaphors', false],
                    ['The model confidently producing factually incorrect or fabricated information', true],
                    ['The model taking too long to respond', false],
                    ['The model refusing to answer certain questions', false],
                ],
            ],

            // ── GENERATIVE AI ─────────────────────────────────────────────
            [
                'q' => 'In a Generative Adversarial Network (GAN), the two networks are:',
                'opts' => [
                    ['Encoder and Decoder', false],
                    ['Generator (creates fake data) and Discriminator (distinguishes real from fake)', true],
                    ['Teacher and Student', false],
                    ['Classifier and Regressor', false],
                ],
            ],
            [
                'q' => 'Diffusion models generate images by learning to:',
                'opts' => [
                    ['Combine two existing images into a new one', false],
                    ['Reverse a gradual noising process — starting from noise and recovering a clean image step by step', true],
                    ['Search a database of real images and return the closest match', false],
                    ['Classify images first, then modify them based on class labels', false],
                ],
            ],

            // ── REINFORCEMENT LEARNING ────────────────────────────────────
            [
                'q' => 'In reinforcement learning, the "reward signal" tells the agent:',
                'opts' => [
                    ['Which weights to use in its neural network', false],
                    ['How good or bad its last action was, guiding it to maximise long-term reward', true],
                    ['The exact correct action to take at each step', false],
                    ['How much memory to allocate for computation', false],
                ],
            ],
            [
                'q' => 'The "exploration vs. exploitation" trade-off in RL means:\n\n- Exploration: try new actions to discover better strategies\n- Exploitation: use the best known strategy',
                'opts' => [
                    ['Always exploiting gives the best long-term performance', false],
                    ['A balance is needed — too much exploitation misses better strategies; too much exploration wastes resources on bad actions', true],
                    ['Always exploring gives the best long-term performance', false],
                    ['This trade-off only applies to supervised learning', false],
                ],
            ],

            // ── AI ETHICS ─────────────────────────────────────────────────
            [
                'q' => 'An AI hiring tool is found to reject candidates from certain universities more often. This is most likely caused by:',
                'opts' => [
                    ['The model being too complex for the task', false],
                    ['Historical bias in the training data — if past hiring data reflects discrimination, the model learns it', true],
                    ['Using supervised learning instead of unsupervised learning', false],
                    ['Having too large a training dataset', false],
                ],
            ],
            [
                'q' => '"Explainability" in AI refers to the ability to:',
                'opts' => [
                    ['Make AI systems run faster by explaining the computation to the hardware', false],
                    ['Understand and communicate why a model made a specific prediction or decision', true],
                    ['Train models on smaller datasets by explaining patterns manually', false],
                    ['Export model weights in a human-readable format', false],
                ],
            ],
            [
                'q' => 'GDPR (General Data Protection Regulation) affects AI systems in the EU because it:',
                'opts' => [
                    ['Bans the use of AI in commercial applications', false],
                    ['Grants individuals the right to explanation for automated decisions that affect them', true],
                    ['Requires all AI models to be open source', false],
                    ['Limits AI processing speed to reduce energy consumption', false],
                ],
            ],

            // ── AI IN THE REAL WORLD ──────────────────────────────────────
            [
                'q' => 'A recommendation system that shows you content based on what people similar to you liked is using:',
                'opts' => [
                    ['Object detection', false],
                    ['Collaborative filtering', true],
                    ['Language translation', false],
                    ['Genetic algorithms', false],
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

        $this->command->info("✅ Done! 30 questions seeded for Module 19 — Introduction to Artificial Intelligence (University Student).");
    }
}