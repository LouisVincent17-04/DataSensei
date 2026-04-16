<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module19ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 19 — Introduction to Artificial Intelligence (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Artificial Intelligence',
            'description'           => 'Debug and optimize real AI code — identify bugs in training loops, ML pipelines, and model definitions across computer vision, NLP, RL, and generative AI. Deep architectural understanding and code-level reasoning required.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1500,
            'order_index'           => 19,
        ]);

        $this->command->info("Seeding 30 advanced questions...");

        $qaData = [

            // ── ML PIPELINE BUGS ──────────────────────────────────────────
            [
                'q' => "Find the data leakage bug in this preprocessing pipeline:\n\n```python\nfrom sklearn.preprocessing import StandardScaler\nfrom sklearn.model_selection import train_test_split\n\nscaler = StandardScaler()\nX_scaled = scaler.fit_transform(X)  # BUG\n\nX_train, X_test, y_train, y_test = train_test_split(\n    X_scaled, y, test_size=0.2\n)\n```",
                'opts' => [
                    ['StandardScaler should not be used for this type of data', false],
                    ['The scaler is fit on the FULL dataset before splitting — test set statistics leak into the scaler, inflating performance estimates', true],
                    ['train_test_split should be called before importing sklearn', false],
                    ['X_scaled should be transposed before splitting', false],
                ],
            ],
            [
                'q' => "This cross-validation loop has a critical bug:\n\n```python\nfrom sklearn.model_selection import KFold\nfrom sklearn.preprocessing import StandardScaler\n\nscores = []\nkf = KFold(n_splits=5)\nscaler = StandardScaler()\nX_scaled = scaler.fit_transform(X)  # BUG\n\nfor train_idx, val_idx in kf.split(X_scaled):\n    X_tr, X_val = X_scaled[train_idx], X_scaled[val_idx]\n    model.fit(X_tr, y[train_idx])\n    scores.append(model.score(X_val, y[val_idx]))\n```",
                'opts' => [
                    ['KFold should use shuffle=True for stratification', false],
                    ['The scaler is fit before the CV loop — validation folds\' statistics contaminate the scaler. Fit the scaler inside each fold on training data only', true],
                    ['model.score should use the training set, not validation set', false],
                    ['KFold does not support n_splits=5', false],
                ],
            ],
            [
                'q' => "This logistic regression training code produces identical predictions for all inputs:\n\n```python\nimport numpy as np\n\ndef sigmoid(z):\n    return 1 / (1 + np.exp(-z))\n\nW = np.zeros((X.shape[1], 1))  # BUG\nb = 0\n\nfor epoch in range(1000):\n    z = X @ W + b\n    y_pred = sigmoid(z)\n    loss = -np.mean(y * np.log(y_pred) + (1-y) * np.log(1 - y_pred))\n    dW = X.T @ (y_pred - y) / len(y)\n    W -= 0.01 * dW\n```",
                'opts' => [
                    ['The sigmoid function is incorrect', false],
                    ['Initializing W with all zeros means all neurons compute identical outputs and receive identical gradients — symmetry never breaks. Use random initialization', true],
                    ['The learning rate 0.01 is too high', false],
                    ['dW should be divided by X.shape[1], not len(y)', false],
                ],
            ],

            // ── COMPUTER VISION BUGS ──────────────────────────────────────
            [
                'q' => "A CNN classifier always predicts class 0 after the first epoch. Identify the bug:\n\n```python\nmodel = nn.Sequential(\n    nn.Conv2d(3, 32, 3, padding=1),\n    nn.ReLU(),\n    nn.Flatten(),\n    nn.Linear(32 * 32 * 32, 10),\n    nn.Softmax(dim=1)  # BUG\n)\n\ncriterion = nn.CrossEntropyLoss()\n```",
                'opts' => [
                    ['Conv2d should use kernel_size=5, not 3', false],
                    ['nn.CrossEntropyLoss already applies log-softmax internally — applying Softmax first causes double-softmax, saturating gradients and collapsing predictions', true],
                    ['The Flatten layer cannot follow a Conv2d layer', false],
                    ['ReLU should not be used in convolutional layers', false],
                ],
            ],
            [
                'q' => "This data augmentation pipeline causes training to fail silently — the model sees identical images every epoch:\n\n```python\nfrom torchvision import transforms\n\ntransform = transforms.Compose([\n    transforms.Resize((224, 224)),\n    transforms.ToTensor(),\n    transforms.RandomHorizontalFlip(),  # BUG LOCATION\n    transforms.Normalize([0.5], [0.5])\n])\n\n# Same transform used for both train and val\nval_dataset = ImageFolder('val/', transform=transform)\ntrain_dataset = ImageFolder('train/', transform=transform)\n```",
                'opts' => [
                    ['Resize should come after ToTensor', false],
                    ['RandomHorizontalFlip is applied to the validation set too — validation augmentation introduces randomness that should not exist. Validation needs a deterministic transform', true],
                    ['Normalize should be the first transform, not the last', false],
                    ['ImageFolder requires transforms to be passed separately for each split', false],
                ],
            ],

            // ── NLP BUGS ─────────────────────────────────────────────────
            [
                'q' => "This text classification training loop has a bug causing all loss values to be NaN:\n\n```python\ncriterion = nn.CrossEntropyLoss()\n\nfor texts, labels in dataloader:\n    logits = model(texts)          # shape: (batch, num_classes)\n    loss = criterion(labels, logits)  # BUG\n    loss.backward()\n    optimizer.step()\n    optimizer.zero_grad()\n```",
                'opts' => [
                    ['CrossEntropyLoss cannot be used for text classification', false],
                    ['The argument order is wrong — nn.CrossEntropyLoss expects (input, target) = (logits, labels), not (labels, logits)', true],
                    ['optimizer.zero_grad() must be called before loss.backward()', false],
                    ['logits should be passed through softmax before CrossEntropyLoss', false],
                ],
            ],
            [
                'q' => "A BERT fine-tuning script gives poor accuracy despite a low training loss. The bug is:\n\n```python\nfrom transformers import BertTokenizer, BertForSequenceClassification\n\ntokenizer = BertTokenizer.from_pretrained('bert-base-uncased')\nmodel = BertForSequenceClassification.from_pretrained('bert-base-uncased', num_labels=3)\n\nfor text, label in dataset:\n    inputs = tokenizer(text, return_tensors='pt',\n                       max_length=512, truncation=True)\n    # attention_mask not passed to model  -- BUG\n    output = model(input_ids=inputs['input_ids'])\n    loss = output.loss\n```",
                'opts' => [
                    ['BertTokenizer should be replaced with AutoTokenizer', false],
                    ['The attention_mask is tokenized but never passed to model() — BERT attends to padding tokens as if they are real, corrupting representations', true],
                    ['max_length=512 is too short for BERT', false],
                    ['num_labels=3 requires a different BERT variant', false],
                ],
            ],

            // ── LLM / TRANSFORMER BUGS ────────────────────────────────────
            [
                'q' => "This transformer self-attention has two bugs. Identify the PRIMARY one:\n\n```python\ndef scaled_dot_product_attention(Q, K, V, mask=None):\n    d_k = Q.size(-1)\n    scores = torch.matmul(Q, K)              # Bug 1: missing K transpose\n    scores = scores / math.sqrt(d_k)\n    if mask is not None:\n        scores = scores.masked_fill(mask == 1, float('-inf'))  # Bug 2\n    weights = F.softmax(scores, dim=-1)\n    return torch.matmul(weights, V)\n```",
                'opts' => [
                    ['The scaling factor should be d_k not sqrt(d_k)', false],
                    ['K must be transposed: torch.matmul(Q, K.transpose(-2, -1)) — without transposition the matrix dimensions are incompatible', true],
                    ['Softmax should be applied along dim=0', false],
                    ['V should be transposed in the final matmul', false],
                ],
            ],
            [
                'q' => "A language model generates repetitive text — it keeps looping the same phrase. Looking at the sampling code:\n\n```python\ndef generate(model, input_ids, max_new_tokens=100):\n    for _ in range(max_new_tokens):\n        logits = model(input_ids).logits[:, -1, :]\n        next_token = torch.argmax(logits, dim=-1)  # Greedy\n        input_ids = torch.cat([input_ids, next_token.unsqueeze(0)], dim=1)\n    return input_ids\n```\n\nWhat causes the repetition and how is it fixed?",
                'opts' => [
                    ['input_ids grows too long — truncate after each step', false],
                    ['Greedy argmax always picks the highest probability token, leading to repetitive loops. Use temperature sampling or top-k/top-p (nucleus) sampling for diversity', true],
                    ['The logits slice [:, -1, :] is incorrect — use [:, 0, :]', false],
                    ['torch.cat should concatenate along dim=0, not dim=1', false],
                ],
            ],

            // ── GENERATIVE AI DEBUGGING ───────────────────────────────────
            [
                'q' => "A GAN's generator loss keeps increasing while the discriminator loss drops to near zero. Training looks like:\n\n```\nEpoch  1: D_loss=0.693, G_loss=0.693\nEpoch 10: D_loss=0.021, G_loss=8.432\nEpoch 20: D_loss=0.003, G_loss=14.7\n```\n\nDiagnose the problem and the correct fix:",
                'opts' => [
                    ['The generator learning rate is too low — increase it', false],
                    ['The discriminator is too strong and overwhelms the generator. Fix: train the generator more steps per discriminator step, or reduce discriminator capacity', true],
                    ['The loss function is wrong — use MSE instead of cross-entropy', false],
                    ['The batch size is too large for GAN training', false],
                ],
            ],
            [
                'q' => "This VAE sampling code produces only a single point in latent space regardless of the input:\n\n```python\nclass VAE(nn.Module):\n    def encode(self, x):\n        h = self.encoder(x)\n        mu = self.fc_mu(h)\n        log_var = self.fc_var(h)\n        return mu, log_var\n\n    def reparameterize(self, mu, log_var):\n        std = torch.exp(log_var)           # BUG\n        eps = torch.randn_like(std)\n        return mu + eps * std\n```",
                'opts' => [
                    ['torch.randn_like should be torch.rand_like', false],
                    ['std = torch.exp(log_var) should be std = torch.exp(0.5 * log_var) — the model outputs log variance, so std = exp(½ log σ²) = σ', true],
                    ['mu should be detached before reparameterization', false],
                    ['The encoder should output sigma directly, not log_var', false],
                ],
            ],

            // ── REINFORCEMENT LEARNING BUGS ───────────────────────────────
            [
                'q' => "A DQN implementation always selects the same action after training. Find the bug:\n\n```python\nclass DQN(nn.Module):\n    def select_action(self, state, epsilon):\n        if random.random() < epsilon:\n            return random.randint(0, self.n_actions - 1)\n        with torch.no_grad():\n            q_values = self.forward(state)\n            return q_values.argmax().item()\n\n# During training:\nfor step in range(10000):\n    action = agent.select_action(state, epsilon=0.0)  # BUG\n```",
                'opts' => [
                    ['argmax should be replaced with softmax sampling', false],
                    ['epsilon=0.0 means pure exploitation — the agent never explores. Start with epsilon close to 1.0 and decay it over training (epsilon-greedy schedule)', true],
                    ['torch.no_grad() prevents the Q-network from learning', false],
                    ['DQN should not use argmax — use random.choice instead', false],
                ],
            ],
            [
                'q' => "A policy gradient agent's training is unstable — rewards oscillate wildly:\n\n```python\ndef policy_gradient_update(log_probs, rewards):\n    returns = []\n    R = 0\n    for r in reversed(rewards):\n        R = r + 0.99 * R\n        returns.insert(0, R)\n    returns = torch.tensor(returns)\n    # Missing normalization  -- BUG\n    loss = -torch.sum(torch.stack(log_probs) * returns)\n    loss.backward()\n    optimizer.step()\n```",
                'opts' => [
                    ['The discount factor 0.99 is too high', false],
                    ['returns are not normalized — large varying return magnitudes cause unstable gradients. Add: returns = (returns - returns.mean()) / (returns.std() + 1e-8)', true],
                    ['log_probs should be stacked before the loop', false],
                    ['torch.sum should be torch.mean for policy gradients', false],
                ],
            ],

            // ── AI ETHICS: CODE & SYSTEM ISSUES ──────────────────────────
            [
                'q' => "This fairness evaluation finds a bias issue:\n\n```python\nfrom sklearn.metrics import accuracy_score\n\ny_pred = model.predict(X_test)\n\nprint('Overall accuracy:', accuracy_score(y_test, y_pred))\n# Output: 0.91\n\nprint('Group A accuracy:', accuracy_score(y_test[group_A], y_pred[group_A]))\n# Output: 0.94\n\nprint('Group B accuracy:', accuracy_score(y_test[group_B], y_pred[group_B]))\n# Output: 0.71  # BUG / fairness issue\n```\n\nWhat does this reveal and what should be done?",
                'opts' => [
                    ['The model is fine — 71% accuracy is acceptable for any group', false],
                    ['Significant accuracy disparity across groups — the model is biased. Investigate training data representation for Group B and apply fairness-aware resampling or post-processing calibration', true],
                    ['Overall accuracy of 91% means the model is fair by definition', false],
                    ['The issue is in the accuracy_score function — use F1-score everywhere', false],
                ],
            ],
            [
                'q' => "A production model returns confidence 0.99 for every prediction regardless of input, even on clearly out-of-distribution inputs:\n\n```python\nclass Classifier(nn.Module):\n    def forward(self, x):\n        logits = self.layers(x)\n        return F.softmax(logits, dim=1).max(dim=1).values\n```\n\nThis overconfidence is a known neural network problem. The fix is:",
                'opts' => [
                    ['Remove the softmax layer entirely', false],
                    ['Apply temperature scaling calibration: softmax(logits / T) where T > 1 reduces overconfidence, or use Monte Carlo Dropout for uncertainty estimation', true],
                    ['Increase the number of training epochs', false],
                    ['Replace softmax with sigmoid for multi-class classification', false],
                ],
            ],

            // ── ARCHITECTURE DESIGN CHOICES ───────────────────────────────
            [
                'q' => "You are building a model to classify 10 seconds of audio into 50 categories. The audio is sampled at 16kHz (160,000 samples). Which architecture is best suited and why?\n\n```\nA) 1D CNN on raw waveform\nB) CNN on Mel-spectrogram (time-frequency representation)\nC) Simple MLP on the raw waveform\nD) RNN on raw waveform samples\n```",
                'opts' => [
                    ['C — MLPs are the most general architecture', false],
                    ['B — Mel-spectrograms convert audio to a 2D image-like representation that captures frequency patterns; CNNs excel at spatial feature extraction', true],
                    ['D — RNNs can process any sequence', false],
                    ['A — 1D CNNs on raw waveforms are always better than spectrograms', false],
                ],
            ],
            [
                'q' => "For a sentiment analysis task on product reviews (average length: 50 words), which model architecture offers the BEST trade-off between accuracy and compute efficiency in production?\n\n```\nA) GPT-4 with zero-shot prompting\nB) Fine-tuned DistilBERT (66M parameters)\nC) Bag-of-Words + Logistic Regression\nD) A 24-layer Transformer trained from scratch\n```",
                'opts' => [
                    ['A — GPT-4 is always most accurate', false],
                    ['B — DistilBERT is 40% smaller than BERT with 97% of its performance; fine-tuning leverages pre-trained language understanding efficiently', true],
                    ['C — BoW ignores word order and context, missing nuanced sentiment', false],
                    ['D — training from scratch wastes resources when pre-trained models are available', false],
                ],
            ],
            [
                'q' => "A self-driving car system must detect pedestrians in real time at 30 FPS on an embedded processor (ARM Cortex-A). The production model is:\n\nOriginal: ResNet-50 (25M params, 4.1 GFLOPs, 48ms latency)\nRequired: <33ms latency\n\nThe best optimization strategy WITHOUT retraining from scratch is:",
                'opts' => [
                    ['Increase batch size to 32 to improve GPU throughput', false],
                    ['Apply structured pruning to remove low-magnitude filters, then fine-tune — reduces FLOPs proportionally while preserving accuracy', true],
                    ['Switch from FP32 to FP64 for better precision and speed', false],
                    ['Reduce input image resolution by 10× (from 224px to 22px)', false],
                ],
            ],

            // ── ADVANCED ML CONCEPTS ──────────────────────────────────────
            [
                'q' => "Gradient Boosting builds an ensemble by:\n\n```\nF_0(x) = initial prediction (e.g., mean of y)\nFor m = 1 to M:\n    r_m = y - F_{m-1}(x)  # Residuals\n    h_m = fit a tree to r_m\n    F_m(x) = F_{m-1}(x) + η × h_m(x)\n```\n\nEach tree h_m learns the:",
                'opts' => [
                    ['Original target y directly', false],
                    ['Residuals (negative gradient of the loss) — the remaining error not yet explained by the ensemble', true],
                    ['Previous tree\'s predictions', false],
                    ['Random subset of the training data', false],
                ],
            ],
            [
                'q' => "This XGBoost evaluation shows the model overfits heavily:\n\n```python\nimport xgboost as xgb\n\nmodel = xgb.XGBClassifier(\n    n_estimators=2000,\n    max_depth=10,\n    learning_rate=0.3,\n    subsample=1.0\n)\nmodel.fit(X_train, y_train)\n\n# Train AUC: 0.999  |  Val AUC: 0.712\n```\n\nWhich hyperparameter changes will most directly reduce overfitting?",
                'opts' => [
                    ['Increase n_estimators and max_depth further', false],
                    ['Reduce max_depth (e.g., 4-6), lower learning_rate (e.g., 0.05), add subsample < 1.0 and colsample_bytree < 1.0', true],
                    ['Set learning_rate=1.0 for faster convergence', false],
                    ['Switch to a linear kernel', false],
                ],
            ],
            [
                'q' => "SHAP (SHapley Additive exPlanations) values explain a model's output by:\n\nφᵢ = Σ (|S|!(|F|−|S|−1)!/|F|!) × [f(S∪{i}) − f(S)]\n\nIn practical terms, SHAP assigns each feature a value that represents:",
                'opts' => [
                    ['The feature\'s rank in terms of overall dataset importance', false],
                    ['The feature\'s marginal contribution to the prediction, averaged over all possible feature subsets (coalitions)', true],
                    ['The correlation between the feature and the target variable', false],
                    ['The feature\'s weight in the final layer of a neural network', false],
                ],
            ],

            // ── REAL-WORLD AI SYSTEMS ─────────────────────────────────────
            [
                'q' => "A recommendation system based on collaborative filtering returns stale recommendations after a new product launch because:\n\n```python\n# Model trained once monthly\nuser_item_matrix = build_matrix(interactions_last_90_days)\nmodel = SVD().fit(user_item_matrix)\n# New products have no interaction history\n```",
                'opts' => [
                    ['SVD cannot handle more than 1000 items', false],
                    ['The cold-start problem — new items have no interaction history so collaborative filtering cannot represent them. Use content-based features for new items as a fallback', true],
                    ['The model should be trained on 365 days of data instead of 90', false],
                    ['SVD should be replaced with a neural network', false],
                ],
            ],
            [
                'q' => "An A/B test runs a new ML model (B) against the production model (A). After 2 weeks:\n\n```\nModel A (control): CTR = 4.2%  (n=500,000 users)\nModel B (treatment): CTR = 4.4%  (n=500,000 users)\np-value = 0.041\n```\n\nThe team wants to ship Model B. What additional consideration is critical before deploying?",
                'opts' => [
                    ['p < 0.05 is sufficient — ship immediately', false],
                    ['Evaluate practical significance — a 0.2% CTR lift must justify the increased infrastructure cost, latency, and maintenance burden of the new model', true],
                    ['The experiment needs to run for exactly 30 days regardless of sample size', false],
                    ['p-value tests are irrelevant for ML model A/B tests', false],
                ],
            ],
            [
                'q' => "Monitoring a deployed classification model, you observe:\n\n```\nWeek 1:  Accuracy = 0.91, Prediction Distribution: {0: 45%, 1: 55%}\nWeek 8:  Accuracy = 0.91, Prediction Distribution: {0: 78%, 1: 22%}\n```\n\nAccuracy is unchanged but the prediction distribution shifted significantly. This is a warning sign because:",
                'opts' => [
                    ['Accuracy of 0.91 is too low for production', false],
                    ['Silent covariate shift — input data distribution changed. The model still appears accurate (possibly due to imbalanced ground truth shifting too) but may be making predictions for the wrong reasons', true],
                    ['The model is improving — predicting class 0 more is always better', false],
                    ['This is normal behavior and requires no action', false],
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

        $this->command->info("✅ Done! 30 questions seeded for Module 19 — Introduction to Artificial Intelligence (Advanced).");
    }
}