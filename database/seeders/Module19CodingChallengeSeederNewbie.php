<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 19 — Artificial Intelligence (Newbie / Level 1) — CODING variant
 *
 * Seeds in one pass:
 * 1. challenges          — one coding challenge for the Newbie tier
 * 2. coding_questions    — 50 questions covering beginner AI concepts
 * 3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mapped to lessons 457–466):
 * 19.1 What Is Artificial Intelligence? History, Definitions & Landscape
 * 19.2 Machine Learning Fundamentals: Supervised, Unsupervised & Reinforcement
 * 19.3 Neural Networks: From Perceptron to Deep Learning
 * 19.4 Computer Vision: CNNs, Object Detection & Image AI
 * 19.5 Natural Language Processing: From Bag-of-Words to Transformers
 * 19.6 Large Language Models: Architecture, Training & Capabilities
 * 19.7 Generative AI: GANs, Diffusion Models & Multimodal Systems
 * 19.8 Reinforcement Learning: Agents, Rewards & Policy Optimisation
 * 19.9 AI Ethics, Bias, Fairness & Responsible AI
 * 19.10 AI in the Real World: Applications, Careers & the Future
 *
 * Difficulty: Newbie — all problems solvable with pure Python, no third-party
 * libraries (like sklearn or numpy) required. Learners build intuition for 
 * core AI algorithms and metrics from scratch.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module19CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 19 — Artificial Intelligence (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Artificial Intelligence Foundations',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Master the broad landscape of Artificial Intelligence. Implement core concepts from Machine Learning metrics, simple Perceptrons, NLP tokenization, Generative AI interpolation, Reinforcement Learning rewards, and AI Fairness metrics using pure Python.',
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
            // TOPIC 1: What Is Artificial Intelligence? (Q1–Q5) → L457
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
**The Turing Test Basic Logic:** A judge assigns a score from 0.0 to 1.0 based on how human-like a response is. If the score is > 0.5, the judge flags it as `Human`, otherwise `Bot`. Read `n` scores. Print `Human` or `Bot` for each, one per line.

Example:
Input:
3
0.8
0.2
0.51
Output:
Human
Bot
Human

MD,
                'starter_code'        => "n = int(input())\nscores = [float(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
**Rule-Based AI:** Before ML, AI relied heavily on explicit rules. Write a rule-based expert system that grants a loan. Read `income` and `credit_score`. Print `Approved` if income >= 50000 AND credit_score >= 700, otherwise print `Denied`.

Example:
Input:
60000 750
Output:
Approved

MD,
                'starter_code'        => "income, credit = map(int, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
**Heuristic Search (Manhattan Distance):** Early AI used heuristics like A* search. Calculate the Manhattan distance between an agent at (x1, y1) and a goal at (x2, y2). Distance = |x1 - x2| + |y1 - y2|. Print the integer distance.

Example:
Input:
1 1
4 5
Output:
7

MD,
                'starter_code'        => "x1, y1 = map(int, input().split())\nx2, y2 = map(int, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
**Agent State Space:** An AI agent is on a 1D line at position 0. It receives a string of commands: `L` (left -1) and `R` (right +1). Read the string. Print the final integer position.

Example:
Input:
RRLLR
Output:
1

MD,
                'starter_code'        => "commands = input().strip()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
**ANI vs AGI Classification:** Narrow AI (ANI) excels at one task; General AI (AGI) can generalize across many domains. Read the number of capabilities `k` a system has. If `k` < 3, print `ANI`. If `k` >= 3, print `AGI`.

Example:
Input:
1
Output:
ANI

MD,
                'starter_code'        => "k = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Machine Learning Fundamentals (Q6–Q10) → L458
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
**Supervised Learning - Accuracy:** Calculate model accuracy. Read `n`, then `n` true labels, then `n` predicted labels. Accuracy = (correct / total). Print rounded to 4 decimal places.

Example:
Input:
4
1 0 1 1
1 0 0 1
Output:
0.7500

MD,
                'starter_code'        => "n = int(input())\ny_true = list(map(int, input().split()))\ny_pred = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
**Unsupervised Learning - Centroid:** In clustering, you find the center of a group of points. Read `n` 2D points (x y). Calculate the mean x and mean y. Print the centroid space-separated, rounded to 4 decimals.

Example:
Input:
3
0.0 0.0
2.0 0.0
1.0 3.0
Output:
1.0000 1.0000

MD,
                'starter_code'        => "n = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
**Train/Test Split Logic:** You have `N` samples. You need an 80/20 train/test split. Calculate the number of training samples `floor(0.8 * N)`. The rest are test samples. Print `Train: <train_count> Test: <test_count>`.

Example:
Input:
10
Output:
Train: 8 Test: 2

MD,
                'starter_code'        => "import math\nN = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
**Precision Calculation:** Precision = True Positives / (True Positives + False Positives). Read `TP` and `FP`. Print the precision rounded to 4 decimals. If denominator is 0, print `0.0000`.

Example:
Input:
8 2
Output:
0.8000

MD,
                'starter_code'        => "TP, FP = map(int, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
**Recall Calculation:** Recall = True Positives / (True Positives + False Negatives). Read `TP` and `FN`. Print the recall rounded to 4 decimals. If denominator is 0, print `0.0000`.

Example:
Input:
8 2
Output:
0.8000

MD,
                'starter_code'        => "TP, FN = map(int, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Neural Networks Fundamentals (Q11–Q15) → L459
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
**Simple Perceptron:** A perceptron fires (returns 1) if `(w1*x1 + w2*x2 + b) > 0`, else it returns 0. Read `w1, w2, b`, then `x1, x2`. Print `1` or `0`.

Example:
Input:
0.5 0.5 -1.0
1.0 1.0
Output:
0

MD,
                'starter_code'        => "w1, w2, b = map(float, input().split())\nx1, x2 = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
**Vector Dot Product (Forward Pass):** Neural networks rely heavily on dot products. Read vector size `n`, then vector `X`, then vector `W`. Print the dot product rounded to 4 decimals.

Example:
Input:
3
1.0 2.0 3.0
0.1 0.2 0.3
Output:
1.4000

MD,
                'starter_code'        => "n = int(input())\nX = list(map(float, input().split()))\nW = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
**ReLU Activation:** The Rectified Linear Unit applies `max(0, x)`. Read vector length `n`, then `n` values. Print the activated vector, space-separated, rounded to 4 decimals.

Example:
Input:
3
-1.5 0.0 2.5
Output:
0.0000 0.0000 2.5000

MD,
                'starter_code'        => "n = int(input())\nvalues = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
**Sigmoid Activation:** Sigmoid squashes output between 0 and 1: `1 / (1 + exp(-x))`. Read vector length `n`, then `n` values. Apply sigmoid and print space-separated, rounded to 4 decimals.

Example:
Input:
2
0.0 2.0
Output:
0.5000 0.8808

MD,
                'starter_code'        => "import math\nn = int(input())\nvalues = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
**Mean Squared Error (MSE):** Used to compute network loss. `MSE = Sum((y_true - y_pred)²) / n`. Read `n`, vector `y_true`, and vector `y_pred`. Print MSE rounded to 4 decimals.

Example:
Input:
2
1.0 0.0
0.8 0.2
Output:
0.0400

MD,
                'starter_code'        => "n = int(input())\ny_true = list(map(float, input().split()))\ny_pred = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Computer Vision (Q16–Q20) → L460
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
**Image Inversion (Grayscale):** Pixels range from 0 to 255. Inverting an image means new_pixel = 255 - pixel. Read `n` pixels. Print the inverted pixels space-separated as integers.

Example:
Input:
4
0 255 100 50
Output:
255 0 155 205

MD,
                'starter_code'        => "n = int(input())\npixels = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
**Basic 1D Edge Detection:** Find edges by taking the absolute difference between adjacent pixels in a 1D array. Output array length will be `n-1`. Read `n` and the pixels. Print the differences space-separated.

Example:
Input:
5
10 10 50 50 10
Output:
0 40 0 40

MD,
                'starter_code'        => "n = int(input())\npixels = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
**Intersection over Union (1D):** Object detection uses IoU. For two 1D line segments A=[x1, x2] and B=[y1, y2], calculate `Intersection Length / Union Length`. Read x1, x2, then y1, y2. Print IoU rounded to 4 decimals. If they don't intersect, IoU is 0.0000.

Example:
Input:
0 5
3 8
Output:
0.2500

MD,
                'starter_code'        => "x1, x2 = map(float, input().split())\ny1, y2 = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
**Bounding Box Center:** A 2D bounding box is defined by top-left (x1, y1) and bottom-right (x2, y2). Calculate the center coordinates. Read `x1 y1 x2 y2`. Print `cx cy` rounded to 2 decimals.

Example:
Input:
10 10 20 30
Output:
15.00 20.00

MD,
                'starter_code'        => "x1, y1, x2, y2 = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
**1D Max Pooling:** CNNs use max pooling to downsample. Read `n` values. Slide a window of size 2, stride 2, taking the max of each pair. (Assume `n` is even). Print the pooled values space-separated.

Example:
Input:
6
1 5 2 8 9 3
Output:
5 8 9

MD,
                'starter_code'        => "n = int(input())\nvals = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Natural Language Processing (Q21–Q25) → L461
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
**Basic Tokenization:** Read a string. Convert it to lowercase and split by spaces into tokens. Print the number of resulting tokens.

Example:
Input:
The quick brown fox
Output:
4

MD,
                'starter_code'        => "text = input().strip()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
**Bag of Words (Frequency):** Read a target word (lowercase), then a sentence. Convert the sentence to lowercase, split by space, and count how many times the target word appears. Print the count.

Example:
Input:
cat
The cat chased the other cat
Output:
2

MD,
                'starter_code'        => "target = input().strip().lower()\nsentence = input().strip()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
**Stop-Word Removal:** Common NLP preprocessing. Read a list of stop words (space-separated), then read a sentence. Remove any stop words from the sentence (case-sensitive for simplicity). Print the cleaned sentence.

Example:
Input:
the is and
the cat is black and white
Output:
cat black white

MD,
                'starter_code'        => "stopwords = input().split()\nsentence = input().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
**Jaccard Similarity:** Compares document similarity. J = (Intersection size) / (Union size) of unique words. Read sentence A, then sentence B (split by spaces). Compute Jaccard Similarity and print rounded to 4 decimals.

Example:
Input:
apple banana orange
apple banana kiwi
Output:
0.5000

MD,
                'starter_code'        => "setA = set(input().split())\nsetB = set(input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
**Term Frequency (TF):** TF = (Number of times term t appears in a document) / (Total words in document). Read target word, then the document text. Case-sensitive. Print TF rounded to 4 decimals.

Example:
Input:
AI
AI is the future of AI
Output:
0.3333

MD,
                'starter_code'        => "target = input().strip()\ndoc = input().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Large Language Models (Q26–Q30) → L462
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
**Greedy Next-Word Prediction:** LLMs output a probability distribution over a vocabulary. Read `n` (vocab size). Then read `n` lines of `word probability`. Print the word with the highest probability.

Example:
Input:
3
apple 0.1
banana 0.7
cherry 0.2
Output:
banana

MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
**Temperature Scaling Logic:** Temperature modifies logits before softmax. `new_logit = logit / T`. Read Temperature `T`, `n`, and `n` logits. Print the scaled logits space-separated, rounded to 4 decimals.

Example:
Input:
2.0
3
2.0 4.0 6.0
Output:
1.0000 2.0000 3.0000

MD,
                'starter_code'        => "T = float(input())\nn = int(input())\nlogits = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
**Top-K Filtering:** LLMs use Top-K to filter out low-probability words. Read `K`, `n`, and `n` probabilities. Print the values of the Top `K` probabilities, sorted in descending order, space-separated, rounded to 4 decimals.

Example:
Input:
2
4
0.1 0.6 0.2 0.05
Output:
0.6000 0.2000

MD,
                'starter_code'        => "K = int(input())\nn = int(input())\nprobs = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
**Context Window Truncation:** LLMs have a maximum context length. Read `max_tokens` and a string of space-separated words. If the string has more words than `max_tokens`, keep only the LAST `max_tokens` words (LLMs drop old context, keeping recent). Print the new string.

Example:
Input:
3
one two three four five
Output:
three four five

MD,
                'starter_code'        => "max_tokens = int(input())\ntokens = input().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
**Perplexity Concept:** A measure of how well a probability distribution predicts a sample. Simplification: `Perplexity = exp( - (1/N) * Sum(ln(P_i)) )`. Read `N`, and `N` probabilities of the correct next words. Print perplexity rounded to 4 decimals.

Example:
Input:
2
0.5 0.5
Output:
2.0000

MD,
                'starter_code'        => "import math\nN = int(input())\nprobs = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Generative AI (Q31–Q35) → L463
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
**GAN Discriminator Scoring:** A discriminator outputs a probability that an image is real. Read `n`. Then `n` lines of `type` (Real/Fake) and `score` (0.0 to 1.0). For Real, it's correct if score > 0.5. For Fake, correct if score <= 0.5. Print the number of correct classifications.

Example:
Input:
3
Real 0.8
Fake 0.2
Fake 0.9
Output:
2

MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
**Latent Space Interpolation:** Generative models blend concepts by interpolating latent vectors. `Z = (1 - alpha) * Z1 + alpha * Z2`. Read `alpha`, then vectors `Z1` and `Z2` of length `n`. Print `Z` space-separated, rounded to 4 decimals.

Example:
Input:
0.5
2
0.0 10.0
10.0 0.0
Output:
5.0000 5.0000

MD,
                'starter_code'        => "alpha = float(input())\nn = int(input())\nZ1 = list(map(float, input().split()))\nZ2 = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
**Diffusion Forward Process (Adding Noise):** At step `t`, the image vector `X` gets noise added: `X_t = X * sqrt(1 - beta) + noise * sqrt(beta)`. Read `beta`, vector `X` (length `n`), and `noise` vector. Print `X_t` space-separated, rounded to 4 decimals.

Example:
Input:
0.19
2
1.0 1.0
0.5 -0.5
Output:
0.9000 0.9000
... wait, sqrt(0.81)1 + sqrt(0.19)0.5 = 0.91 + 0.4358890.5 = 0.9 + 0.2179 = 1.1179.
Wait, let's use exact math: sqrt(1 - 0.19) = 0.9. sqrt(0.19) = 0.435889.
Output:
1.1179 0.6821

MD,
                'starter_code'        => "import math\nbeta = float(input())\nn = int(input())\nX = list(map(float, input().split()))\nnoise = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
**Diffusion Denoising Concept:** The model predicts the noise added to an image. To denoise, you subtract it: `X_clean = X_noisy - predicted_noise`. Read length `n`, vector `X_noisy`, and `predicted_noise`. Print `X_clean` space-separated, rounded to 4 decimals.

Example:
Input:
2
1.5 0.8
0.5 -0.2
Output:
1.0000 1.0000

MD,
                'starter_code'        => "n = int(input())\nX_noisy = list(map(float, input().split()))\npredicted_noise = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
**Multimodal Embedding Similarity:** In CLIP, text and image vectors are compared using Cosine Similarity. `CosSim = (A dot B) / (norm(A) * norm(B))`. Read `n`, vector `A`, and vector `B`. Print similarity rounded to 4 decimals.

Example:
Input:
3
1 0 0
1 1 0
Output:
0.7071

MD,
                'starter_code'        => "import math\nn = int(input())\nA = list(map(float, input().split()))\nB = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Reinforcement Learning (Q36–Q40) → L464
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
**Discounted Cumulative Reward:** In RL, future rewards are discounted by `gamma`. Total = R_0 + gamma*R_1 + gamma²*R_2... Read `gamma`, `n`, and a list of `n` rewards over time. Print the total discounted reward rounded to 4 decimals.

Example:
Input:
0.9
3
10 10 10
Output:
27.1000

MD,
                'starter_code'        => "gamma = float(input())\nn = int(input())\nrewards = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
**Epsilon-Greedy Action Selection:** An agent explores with probability `epsilon`, or exploits the best known action. Read `epsilon`, a random number `r` (0 to 1), and `n` Q-values for actions. If `r < epsilon`, print `Explore`. Else, print the 0-based index of the max Q-value.

Example:
Input:
0.1 0.5
3
1.0 5.0 2.0
Output:
1

MD,
                'starter_code'        => "epsilon, r = map(float, input().split())\nn = int(input())\nq_values = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
**Q-Value Update (TD Learning):** `New_Q = Old_Q + alpha * (Reward + gamma * Max_Next_Q - Old_Q)`. Read `alpha`, `gamma`, `Old_Q`, `Reward`, and `Max_Next_Q`. Print `New_Q` rounded to 4 decimals.

Example:
Input:
0.1 0.9 5.0 10.0 8.0
Output:
6.2200

MD,
                'starter_code'        => "alpha, gamma, old_q, reward, max_next_q = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
**State Transition Tracking:** An agent is in a 2D grid. Read starting `x y`. Read `n` actions (`U`=y+1, `D`=y-1, `L`=x-1, `R`=x+1). Print final `x y`.

Example:
Input:
0 0
3
U R R
Output:
2 1

MD,
                'starter_code'        => "x, y = map(int, input().split())\nn = int(input())\nactions = input().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
**Policy Evaluation (Expected Value):** The value of a state is the expected reward across all possible actions. `V = Sum( prob_i * reward_i )`. Read `n` actions, then `n` pairs of `prob reward`. Print the expected value rounded to 4 decimals.

Example:
Input:
2
0.8 10.0
0.2 -5.0
Output:
7.0000

MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: AI Ethics, Bias & Fairness (Q41–Q45) → L465
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
**Demographic Parity:** A fairness metric. Does the model accept Group A and Group B at the same rate? Read Total_A, Accepted_A, Total_B, Accepted_B. Calculate Rate_A and Rate_B. Print the absolute difference `|Rate_A - Rate_B|` rounded to 4 decimals.

Example:
Input:
100 20 200 40
Output:
0.0000

MD,
                'starter_code'        => "ta, aa, tb, ab = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
**False Positive Rate (FPR) Disparity:** FPR = False Positives / Actual Negatives. An ethical AI should have similar FPR across groups. Read FP_A, Actual_Negatives_A, FP_B, Actual_Negatives_B. Print `|FPR_A - FPR_B|` rounded to 4 decimals.

Example:
Input:
10 50 20 50
Output:
0.2000

MD,
                'starter_code'        => "fp_a, an_a, fp_b, an_b = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
**Toxicity Score Thresholding:** Content moderation AI returns a toxicity score. Read a threshold `T`, `n`, and `n` scores. Count how many messages are flagged as toxic (score >= T).

Example:
Input:
0.75
4
0.1 0.8 0.9 0.5
Output:
2

MD,
                'starter_code'        => "T = float(input())\nn = int(input())\nscores = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
**Data Bias Mitigation (Downsampling):** You have `N_majority` and `N_minority` samples. To balance the dataset via downsampling, you drop samples from the majority class until it matches the minority class. Read both values. Print the number of samples dropped.

Example:
Input:
1000 200
Output:
800

MD,
                'starter_code'        => "N_maj, N_min = map(int, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
**Equal Opportunity Metric:** Requires the True Positive Rate (TPR) to be equal across groups. TPR = TP / (TP + FN). Read TP_A, FN_A, TP_B, FN_B. Print `|TPR_A - TPR_B|` rounded to 4 decimals.

Example:
Input:
40 10 30 20
Output:
0.2000

MD,
                'starter_code'        => "tp_a, fn_a, tp_b, fn_b = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: AI in the Real World (Q46–Q50) → L466
            // ═══════════════════════════════════════════════════════════════

            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
**Recommendation System Score:** A collaborative filter uses dot products of User vectors and Item vectors. Read `n`, User vector, Item vector. Print the dot product rounded to 4 decimals.

Example:
Input:
3
1.0 0.5 0.2
0.5 1.0 0.0
Output:
1.0000

MD,
                'starter_code'        => "n = int(input())\nuser = list(map(float, input().split()))\nitem = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
**Predictive Maintenance (Moving Average):** Machine sensors predict failure if the moving average of the last `K` readings exceeds threshold `T`. Read `K`, `T`, `n`, and `n` readings. Calculate the average of the LAST `K` readings. Print `Alert` or `Normal`.

Example:
Input:
3 50.0
5
10 20 60 70 80
Output:
Alert

MD,
                'starter_code'        => "K, T = input().split()\nK = int(K)\nT = float(T)\nn = int(input())\nreadings = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
**Ensemble Voting (Classification):** 3 AI models classify an image (1=Dog, 0=Cat). Real-world systems use majority voting. Read 3 integers. Print the majority vote (1 or 0).

Example:
Input:
1 0 1
Output:
1

MD,
                'starter_code'        => "v1, v2, v3 = map(int, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
**Fraud Detection Scoring:** Compute a weighted risk score. Risk = (Amount * W1) + (Distance * W2). If Risk > Threshold, flag as `Fraud`, else `Safe`. Read W1, W2, Threshold, Amount, Distance. Print the decision.

Example:
Input:
0.5 2.0 100.0
100.0 30.0
Output:
Fraud

MD,
                'starter_code'        => "W1, W2, T = map(float, input().split())\namt, dist = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**A/B Testing Lift:** In marketing AI, we calculate the percentage "lift" of model B over model A. Lift = ((ConvRate_B - ConvRate_A) / ConvRate_A) * 100. Read ConvRate_A and ConvRate_B. Print Lift rounded to 2 decimals, followed by `%`.

Example:
Input:
0.10 0.12
Output:
20.00%

MD,
                'starter_code'        => "cr_a, cr_b = map(float, input().split())\n# Write your solution below\n",
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

        // Q1: Turing Test
        $seed(1, [
            ['input' => "3\n0.8\n0.2\n0.51", 'expected_output' => "Human\nBot\nHuman", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0.5", 'expected_output' => "Bot", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1.0\n0.0", 'expected_output' => "Human\nBot", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n0.49\n0.55\n0.1\n0.9", 'expected_output' => "Bot\nHuman\nBot\nHuman", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q2: Rule-Based AI
        $seed(2, [
            ['input' => "60000 750", 'expected_output' => "Approved", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "40000 800", 'expected_output' => "Denied", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "50000 700", 'expected_output' => "Approved", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "100000 699", 'expected_output' => "Denied", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q3: Manhattan Distance
        $seed(3, [
            ['input' => "1 1\n4 5", 'expected_output' => "7", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 0\n0 0", 'expected_output' => "0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-5 -5\n5 5", 'expected_output' => "20", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "10 20\n-10 40", 'expected_output' => "40", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q4: Agent State Space
        $seed(4, [
            ['input' => "RRLLR", 'expected_output' => "1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "LLLLL", 'expected_output' => "-5", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "R", 'expected_output' => "1", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "RLRLRL", 'expected_output' => "0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q5: ANI vs AGI
        $seed(5, [
            ['input' => "1", 'expected_output' => "ANI", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5", 'expected_output' => "AGI", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3", 'expected_output' => "AGI", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2", 'expected_output' => "ANI", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q6: Accuracy
        $seed(6, [
            ['input' => "4\n1 0 1 1\n1 0 0 1", 'expected_output' => "0.7500", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 1\n0 0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0 1 2 3 4\n0 1 2 3 4", 'expected_output' => "1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n1 0 1\n1 1 1", 'expected_output' => "0.6667", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q7: Centroid
        $seed(7, [
            ['input' => "3\n0.0 0.0\n2.0 0.0\n1.0 3.0", 'expected_output' => "1.0000 1.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n-1.0 -1.0\n1.0 1.0", 'expected_output' => "0.0000 0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1\n1 1\n1 1\n1 1", 'expected_output' => "1.0000 1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n10 20\n0 5", 'expected_output' => "5.0000 12.5000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q8: Train/Test Split
        $seed(8, [
            ['input' => "10", 'expected_output' => "Train: 8 Test: 2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "100", 'expected_output' => "Train: 80 Test: 20", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5", 'expected_output' => "Train: 4 Test: 1", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "25", 'expected_output' => "Train: 20 Test: 5", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q9: Precision
        $seed(9, [
            ['input' => "8 2", 'expected_output' => "0.8000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 5", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10 0", 'expected_output' => "1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0 0", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q10: Recall
        $seed(10, [
            ['input' => "8 2", 'expected_output' => "0.8000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 5", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10 0", 'expected_output' => "1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0 0", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q11: Simple Perceptron
        $seed(11, [
            ['input' => "0.5 0.5 -1.0\n1.0 1.0", 'expected_output' => "0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0 1.0 0.0\n0.5 0.5", 'expected_output' => "1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1.0 -1.0 5.0\n2.0 2.0", 'expected_output' => "1", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.0 0.0 0.0\n10 10", 'expected_output' => "0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q12: Vector Dot Product
        $seed(12, [
            ['input' => "3\n1.0 2.0 3.0\n0.1 0.2 0.3", 'expected_output' => "1.4000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n-1.0 1.0\n1.0 1.0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1 1 1\n2 2 2 2", 'expected_output' => "8.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n5.5\n2.0", 'expected_output' => "11.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q13: ReLU Activation
        $seed(13, [
            ['input' => "3\n-1.5 0.0 2.5", 'expected_output' => "0.0000 0.0000 2.5000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10 -10", 'expected_output' => "10.0000 0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n-1 -2 -3 -4", 'expected_output' => "0.0000 0.0000 0.0000 0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n0.001", 'expected_output' => "0.0010", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q14: Sigmoid Activation
        $seed(14, [
            ['input' => "2\n0.0 2.0", 'expected_output' => "0.5000 0.8808", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n-10.0 10.0", 'expected_output' => "0.0000 1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n-1.0 0.0 1.0", 'expected_output' => "0.2689 0.5000 0.7311", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n5.0", 'expected_output' => "0.9933", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q15: MSE
        $seed(15, [
            ['input' => "2\n1.0 0.0\n0.8 0.2", 'expected_output' => "0.0400", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n1 2 3", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 0 0 0\n1 1 1 1", 'expected_output' => "1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n10.0\n5.0", 'expected_output' => "25.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q16: Image Inversion
        $seed(16, [
            ['input' => "4\n0 255 100 50", 'expected_output' => "255 0 155 205", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n127 128", 'expected_output' => "128 127", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0 0 0", 'expected_output' => "255 255 255", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5\n255 255 255 255 255", 'expected_output' => "0 0 0 0 0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q17: Basic 1D Edge
        $seed(17, [
            ['input' => "5\n10 10 50 50 10", 'expected_output' => "0 40 0 40", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0 255 0", 'expected_output' => "255 255", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4", 'expected_output' => "1 1 1", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n100 100", 'expected_output' => "0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q18: Intersection over Union (1D)
        $seed(18, [
            ['input' => "0 5\n3 8", 'expected_output' => "0.2500", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 2\n5 8", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0 10\n2 5", 'expected_output' => "0.3000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0 5\n0 5", 'expected_output' => "1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q19: Bounding Box Center
        $seed(19, [
            ['input' => "10 10 20 30", 'expected_output' => "15.00 20.00", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 0 100 100", 'expected_output' => "50.00 50.00", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "-10 -20 10 20", 'expected_output' => "0.00 0.00", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5 5 6 6", 'expected_output' => "5.50 5.50", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q20: 1D Max Pooling
        $seed(20, [
            ['input' => "6\n1 5 2 8 9 3", 'expected_output' => "5 8 9", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n10 10 5 0", 'expected_output' => "10 5", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n-5 5", 'expected_output' => "5", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "8\n1 2 3 4 5 6 7 8", 'expected_output' => "2 4 6 8", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q21: Basic Tokenization
        $seed(21, [
            ['input' => "The quick brown fox", 'expected_output' => "4", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "AI is the future", 'expected_output' => "4", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "Hello", 'expected_output' => "1", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "One two three four five six", 'expected_output' => "6", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q22: Bag of Words
        $seed(22, [
            ['input' => "cat\nThe cat chased the other cat", 'expected_output' => "2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "ai\nAI is AI", 'expected_output' => "2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "dog\nThe cat ran", 'expected_output' => "0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "the\nthe The THE tHe", 'expected_output' => "4", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q23: Stop-Word Removal
        $seed(23, [
            ['input' => "the is and\nthe cat is black and white", 'expected_output' => "cat black white", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "a an\na apple an orange", 'expected_output' => "apple orange", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "hello\nhello world", 'expected_output' => "world", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "remove\nkeep this", 'expected_output' => "keep this", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q24: Jaccard Similarity
        $seed(24, [
            ['input' => "apple banana orange\napple banana kiwi", 'expected_output' => "0.5000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "ai is cool\nai is cool", 'expected_output' => "1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "hello world\ngoodbye earth", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "a b c\nb c d e", 'expected_output' => "0.4000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q25: Term Frequency
        $seed(25, [
            ['input' => "AI\nAI is the future of AI", 'expected_output' => "0.3333", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "cat\ndog bird fish", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "test\ntest test test", 'expected_output' => "1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "the\nthe cat in the hat", 'expected_output' => "0.4000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q26: Greedy Next-Word
        $seed(26, [
            ['input' => "3\napple 0.1\nbanana 0.7\ncherry 0.2", 'expected_output' => "banana", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nyes 0.4\nno 0.6", 'expected_output' => "no", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\na 0.1\nb 0.2\nc 0.3\nd 0.4", 'expected_output' => "d", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\nonly 1.0", 'expected_output' => "only", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q27: Temperature Scaling
        $seed(27, [
            ['input' => "2.0\n3\n2.0 4.0 6.0", 'expected_output' => "1.0000 2.0000 3.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n2\n1.0 -1.0", 'expected_output' => "2.0000 -2.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0\n3\n10 20 30", 'expected_output' => "1.0000 2.0000 3.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0\n2\n5.5 2.2", 'expected_output' => "5.5000 2.2000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q28: Top-K Filtering
        $seed(28, [
            ['input' => "2\n4\n0.1 0.6 0.2 0.05", 'expected_output' => "0.6000 0.2000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n3\n0.3 0.3 0.4", 'expected_output' => "0.4000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5\n0.1 0.2 0.3 0.4 0.5", 'expected_output' => "0.5000 0.4000 0.3000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n4\n1 2 3 4", 'expected_output' => "4.0000 3.0000 2.0000 1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q29: Context Window
        $seed(29, [
            ['input' => "3\none two three four five", 'expected_output' => "three four five", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\nshort context", 'expected_output' => "short context", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\na b c d e", 'expected_output' => "e", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\nkeep this", 'expected_output' => "keep this", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q30: Perplexity
        $seed(30, [
            ['input' => "2\n0.5 0.5", 'expected_output' => "2.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1.0", 'expected_output' => "1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.1 0.1 0.1", 'expected_output' => "10.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0.25 0.25", 'expected_output' => "4.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q31: GAN Discriminator
        $seed(31, [
            ['input' => "3\nReal 0.8\nFake 0.2\nFake 0.9", 'expected_output' => "2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nReal 0.4\nFake 0.6", 'expected_output' => "0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nReal 0.9\nReal 0.9\nFake 0.1\nFake 0.1", 'expected_output' => "4", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\nReal 0.51", 'expected_output' => "1", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q32: Latent Interpolation
        $seed(32, [
            ['input' => "0.5\n2\n0.0 10.0\n10.0 0.0", 'expected_output' => "5.0000 5.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n3\n1 2 3\n4 5 6", 'expected_output' => "1.0000 2.0000 3.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n3\n1 2 3\n4 5 6", 'expected_output' => "4.0000 5.0000 6.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.2\n2\n0 0\n10 20", 'expected_output' => "2.0000 4.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q33: Diffusion Noise
        $seed(33, [
            ['input' => "0.19\n2\n1.0 1.0\n0.5 -0.5", 'expected_output' => "1.1179 0.6821", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n2\n5.0 5.0\n1.0 1.0", 'expected_output' => "5.0000 5.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n2\n10.0 10.0\n2.0 3.0", 'expected_output' => "2.0000 3.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.5\n1\n1.0\n1.0", 'expected_output' => "1.4142", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q34: Denoising Concept
        $seed(34, [
            ['input' => "2\n1.5 0.8\n0.5 -0.2", 'expected_output' => "1.0000 1.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0 0 0\n0 0 0", 'expected_output' => "0.0000 0.0000 0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5.5\n2.5", 'expected_output' => "3.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n-1 -1\n-2 -2", 'expected_output' => "1.0000 1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q35: Multimodal Sim
        $seed(35, [
            ['input' => "3\n1 0 0\n1 1 0", 'expected_output' => "0.7071", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1 1 1\n2 2 2 2", 'expected_output' => "1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n1 1\n-1 -1", 'expected_output' => "-1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q36: Discounted Reward
        $seed(36, [
            ['input' => "0.9\n3\n10 10 10", 'expected_output' => "27.1000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n4\n16 8 4 2", 'expected_output' => "21.2500", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n5\n100 200 300 400 500", 'expected_output' => "100.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0\n3\n1 1 1", 'expected_output' => "3.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q37: Epsilon-Greedy
        $seed(37, [
            ['input' => "0.1 0.5\n3\n1.0 5.0 2.0", 'expected_output' => "1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5 0.1\n3\n1.0 5.0 2.0", 'expected_output' => "Explore", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0 0.5\n4\n0 0 10 0", 'expected_output' => "2", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0 0.99\n2\n10 20", 'expected_output' => "Explore", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q38: Q-Value Update
        $seed(38, [
            ['input' => "0.1 0.9 5.0 10.0 8.0", 'expected_output' => "6.2200", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5 1.0 0.0 10.0 10.0", 'expected_output' => "10.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0 0.9 5.0 100.0 100.0", 'expected_output' => "5.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0 0.0 5.0 10.0 50.0", 'expected_output' => "10.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q39: State Transition
        $seed(39, [
            ['input' => "0 0\n3\nU R R", 'expected_output' => "2 1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5 5\n4\nD D L L", 'expected_output' => "3 3", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0 0\n4\nU D L R", 'expected_output' => "0 0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "10 10\n1\nU", 'expected_output' => "10 11", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q40: Policy Eval
        $seed(40, [
            ['input' => "2\n0.8 10.0\n0.2 -5.0", 'expected_output' => "7.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.5 2.0\n0.5 4.0\n0.0 100.0", 'expected_output' => "3.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1.0 50.0", 'expected_output' => "50.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n0.25 1\n0.25 2\n0.25 3\n0.25 4", 'expected_output' => "2.5000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q41: Demographic Parity
        $seed(41, [
            ['input' => "100 20 200 40", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "100 50 100 10", 'expected_output' => "0.4000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "50 25 50 25", 'expected_output' => "0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1000 100 100 50", 'expected_output' => "0.4000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q42: FPR Disparity
        $seed(42, [
            ['input' => "10 50 20 50", 'expected_output' => "0.2000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5 100 5 100", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0 50 10 20", 'expected_output' => "0.5000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "10 10 10 100", 'expected_output' => "0.9000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q43: Toxicity Threshold
        $seed(43, [
            ['input' => "0.75\n4\n0.1 0.8 0.9 0.5", 'expected_output' => "2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n3\n0.5 0.6 0.1", 'expected_output' => "2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.99\n4\n0.98 0.97 0.9 0.1", 'expected_output' => "0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.0\n2\n0.1 0.2", 'expected_output' => "2", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q44: Downsampling
        $seed(44, [
            ['input' => "1000 200", 'expected_output' => "800", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "500 500", 'expected_output' => "0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10 5", 'expected_output' => "5", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "9999 1", 'expected_output' => "9998", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q45: Equal Opportunity
        $seed(45, [
            ['input' => "40 10 30 20", 'expected_output' => "0.2000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "10 0 20 0", 'expected_output' => "0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5 5 10 0", 'expected_output' => "0.5000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0 10 10 0", 'expected_output' => "1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q46: RecSys Dot Product
        $seed(46, [
            ['input' => "3\n1.0 0.5 0.2\n0.5 1.0 0.0", 'expected_output' => "1.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10 10\n0 1", 'expected_output' => "10.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1 1 1\n2 2 2 2", 'expected_output' => "8.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n5\n-5", 'expected_output' => "-25.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q47: Predictive Maintenance
        $seed(47, [
            ['input' => "3 50.0\n5\n10 20 60 70 80", 'expected_output' => "Alert", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 10.0\n4\n100 100 5 5", 'expected_output' => "Normal", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 100.0\n3\n1000 1000 0", 'expected_output' => "Normal", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4 20.0\n4\n20 20 20 21", 'expected_output' => "Alert", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q48: Ensemble Vote
        $seed(48, [
            ['input' => "1 0 1", 'expected_output' => "1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 0 0", 'expected_output' => "0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0 1 0", 'expected_output' => "0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 1 1", 'expected_output' => "1", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q49: Fraud Detection
        $seed(49, [
            ['input' => "0.5 2.0 100.0\n100.0 30.0", 'expected_output' => "Fraud", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0 1.0 500.0\n100 100", 'expected_output' => "Safe", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0 0.0 100.0\n11.0 1000", 'expected_output' => "Fraud", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.1 0.1 10.0\n10 10", 'expected_output' => "Safe", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // Q50: A/B Test Lift
        $seed(50, [
            ['input' => "0.10 0.12", 'expected_output' => "20.00%", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.50 0.25", 'expected_output' => "-50.00%", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.20 0.20", 'expected_output' => "0.00%", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.05 0.10", 'expected_output' => "100.00%", 'is_hidden' => true, 'order_index' => 4],
        ]);

        $this->command->info('✅ Module 19 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}