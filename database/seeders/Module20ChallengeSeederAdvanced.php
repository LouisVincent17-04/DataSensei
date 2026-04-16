<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module20ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Analysis of Unstructured Data')
                 ->delete();

        $this->command->info("Creating Module 20 — Analysis of Unstructured Data (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Analysis of Unstructured Data',
            'description'           => 'Advanced problems on debugging NLP pipelines, transformer internals, contrastive learning, multimodal models, and optimising large-scale unstructured data systems. Requires deep understanding of model mechanics and production code.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1500,
            'order_index'           => 20,
        ]);

        $this->command->info("Seeding 50 advanced-level Analysis of Unstructured Data questions...");

        $qaData = [

            // ── TEXT PIPELINES — DEBUGGING ───────────────────────────────
            [
                'q' => "This TF-IDF pipeline gives unexpectedly poor classifier performance. Find the bug:\n\nfrom sklearn.pipeline import Pipeline\nfrom sklearn.feature_extraction.text import TfidfVectorizer\nfrom sklearn.linear_model import LogisticRegression\nfrom sklearn.model_selection import train_test_split\n\nX_train, X_test, y_train, y_test = train_test_split(docs, labels)\n\nvectorizer = TfidfVectorizer()\nX_train_tfidf = vectorizer.fit_transform(X_train)\nX_test_tfidf  = vectorizer.fit_transform(X_test)  # BUG\n\nclf = LogisticRegression()\nclf.fit(X_train_tfidf, y_train)\nprint(clf.score(X_test_tfidf, y_test))",
                'opts' => [
                    ['LogisticRegression is the wrong model for text', false],
                    ['vectorizer.fit_transform(X_test) re-fits the vocabulary on test data — should be vectorizer.transform(X_test), causing train/test vocabulary mismatch', true],
                    ['train_test_split should stratify by label', false],
                    ['TfidfVectorizer requires pre-tokenised input', false],
                ],
            ],
            [
                'q' => "A text classifier achieves 95% accuracy on training data but 52% on the test set. The vocabulary size is 200,000 with only 500 training documents. What is the primary problem and fix?",
                'opts' => [
                    ['Underfitting — use a deeper model', false],
                    ['Severe overfitting due to extremely high-dimensional sparse features with few examples. Fix: reduce vocabulary (max_features), use regularisation (C in LR), or switch to pre-trained embeddings', true],
                    ['The test set is corrupted', false],
                    ['The learning rate is too high', false],
                ],
            ],
            [
                'q' => "What is wrong with this custom tokeniser used in a production NLP pipeline?\n\ndef tokenise(text):\n    return text.split(' ')\n\n# Applied to: 'Hello,world  how  are you?'\n# Result: ['Hello,world', '', 'how', '', 'are', 'you?']",
                'opts' => [
                    ['It should use text.split() with no argument — splits on any whitespace and removes empty strings, also punctuation must be handled separately', true],
                    ['The function name is reserved in Python', false],
                    ['text.split is deprecated', false],
                    ['It works correctly — the output is expected', false],
                ],
            ],

            // ── WORD EMBEDDINGS — ADVANCED ────────────────────────────────
            [
                'q' => "Contextualised word embeddings (ELMo, BERT) differ from static embeddings (Word2Vec, GloVe) because:",
                'opts' => [
                    ['They are always larger in dimension', false],
                    ['The same word gets a DIFFERENT vector depending on its context in the sentence — "bank" in "river bank" vs "bank account" gets different representations', true],
                    ['They are trained unsupervised only', false],
                    ['They cannot handle out-of-vocabulary words', false],
                ],
            ],
            [
                'q' => "What is the computational complexity of the standard self-attention mechanism with respect to sequence length n?",
                'opts' => [
                    ['O(n)', false],
                    ['O(n log n)', false],
                    ['O(n²)', true],
                    ['O(n³)', false],
                ],
            ],
            [
                'q' => "Longformer and BigBird models address the O(n²) attention bottleneck in transformers using:",
                'opts' => [
                    ['Distillation to make the model smaller', false],
                    ['Sparse attention patterns (local sliding window + global tokens) that scale as O(n) while retaining most modelling capacity', true],
                    ['Replacing attention with recurrent layers', false],
                    ['Quantising the attention weights to 8-bit', false],
                ],
            ],
            [
                'q' => "What is the 'dead neuron' problem in ReLU-based NLP models and how is GELU activation different?",
                'opts' => [
                    ['ReLU always outputs 0 for all inputs; GELU does not', false],
                    ['ReLU neurons can become permanently inactive (always outputting 0) for negative inputs. GELU is a smooth probabilistic approximation that has non-zero gradients everywhere', true],
                    ['Dead neurons only occur in CNNs not transformers', false],
                    ['GELU is slower but otherwise identical to ReLU', false],
                ],
            ],

            // ── TRANSFORMER INTERNALS — DEBUGGING ────────────────────────
            [
                'q' => "This BERT fine-tuning code trains but the validation loss never improves. Identify the most likely issue:\n\noptimizer = torch.optim.Adam(model.parameters(), lr=1e-2)\n\nfor epoch in range(10):\n    for batch in train_loader:\n        outputs = model(**batch)\n        loss = outputs.loss\n        loss.backward()\n        optimizer.step()\n        optimizer.zero_grad()",
                'opts' => [
                    ['optimizer.zero_grad() should be called before loss.backward()', false],
                    ['The learning rate 1e-2 is far too high for fine-tuning BERT — transformer fine-tuning requires a much smaller lr (e.g. 2e-5 to 5e-5), causing training instability', true],
                    ['model(**batch) is the wrong calling convention', false],
                    ['Should use SGD not Adam for BERT', false],
                ],
            ],
            [
                'q' => "This attention mask handling is wrong. What is the bug?\n\nfrom transformers import BertTokenizer, BertModel\nimport torch\n\ntokenizer = BertTokenizer.from_pretrained('bert-base-uncased')\nmodel = BertModel.from_pretrained('bert-base-uncased')\n\ntexts = ['Hello world', 'This is a longer sentence that needs padding']\nencoded = tokenizer(texts, padding=True, return_tensors='pt')\n\n# Bug: passing only input_ids, ignoring attention mask\noutput = model(input_ids=encoded['input_ids'])",
                'opts' => [
                    ['The tokenizer does not support batching', false],
                    ['The attention_mask is not passed — BERT will attend to padding tokens as if they were real tokens, corrupting the representations', true],
                    ['return_tensors=\'pt\' should be \'tf\'', false],
                    ['padding=True is not supported for batches', false],
                ],
            ],
            [
                'q' => "What does gradient checkpointing do in transformer training and what is the tradeoff?",
                'opts' => [
                    ['Clips gradients to prevent explosion — no memory tradeoff', false],
                    ['Recomputes activations during the backward pass instead of storing them — reduces GPU memory usage at the cost of ~30% slower training', true],
                    ['Stores all gradients in a checkpoint file', false],
                    ['Skips gradient updates for frozen layers', false],
                ],
            ],
            [
                'q' => "What is label smoothing in text classification training and why is it used?",
                'opts' => [
                    ['It rounds predicted probabilities to 0 or 1', false],
                    ['It replaces hard one-hot labels with soft targets (e.g. 0.9 for correct class, 0.1/(k-1) for others) — prevents overconfidence and improves calibration and generalisation', true],
                    ['It smooths the loss curve by averaging over epochs', false],
                    ['It reduces vocabulary size gradually', false],
                ],
            ],

            // ── TOPIC MODELING — ADVANCED ────────────────────────────────
            [
                'q' => "You train an LDA model and evaluate with coherence score. Plotting coherence vs k (number of topics) shows it peaks at k=7 and then decreases. What does this tell you?",
                'opts' => [
                    ['The model with k=7 is overfit', false],
                    ['k=7 is the optimal number of topics — fewer topics are too broad, more topics become repetitive or fragmented', true],
                    ['The corpus has exactly 7 categories', false],
                    ['You should always use k=7 regardless of corpus size', false],
                ],
            ],
            [
                'q' => "BERTopic differs from LDA because it:",
                'opts' => [
                    ['Uses raw word counts instead of embeddings', false],
                    ['Clusters sentence embeddings (from BERT/SBERT) using UMAP + HDBSCAN to discover topics, then represents topics via class-based TF-IDF — no bag-of-words assumption', true],
                    ['Requires labelled topic data for training', false],
                    ['Is limited to 10 topics maximum', false],
                ],
            ],
            [
                'q' => "What is the main advantage of HDBSCAN over K-Means for topic clustering in BERTopic?",
                'opts' => [
                    ['HDBSCAN is always faster', false],
                    ['HDBSCAN discovers clusters of arbitrary shape and density and can label points as noise (outliers) — it does not require specifying the number of clusters in advance', true],
                    ['HDBSCAN uses cosine similarity natively', false],
                    ['K-Means cannot handle text data', false],
                ],
            ],

            // ── NER & INFORMATION EXTRACTION — ADVANCED ──────────────────
            [
                'q' => "A CRF (Conditional Random Field) layer on top of a BiLSTM for NER provides what advantage over using softmax directly on token representations?",
                'opts' => [
                    ['CRF is faster at inference time', false],
                    ['CRF models transition scores between labels, enforcing globally consistent label sequences (e.g. preventing I-PER after B-ORG) — jointly decodes the full sequence', true],
                    ['CRF eliminates the need for BIO tagging', false],
                    ['CRF can handle longer sequences', false],
                ],
            ],
            [
                'q' => "What is the difference between closed-domain and open-domain information extraction?",
                'opts' => [
                    ['Closed-domain uses ML; open-domain uses rules', false],
                    ['Closed-domain targets predefined relation types in a specific domain; open-domain extracts any relation without pre-specifying types', true],
                    ['They are identical approaches with different names', false],
                    ['Open-domain requires labelled data; closed-domain does not', false],
                ],
            ],

            // ── IMAGE DATA — ADVANCED ────────────────────────────────────
            [
                'q' => "This image augmentation code has a subtle bug for a CLASSIFICATION task:\n\nfrom torchvision import transforms\n\ntransform = transforms.Compose([\n    transforms.RandomHorizontalFlip(),\n    transforms.RandomRotation(30),\n    transforms.ToTensor(),\n    transforms.Normalize(mean=[0.485,0.456,0.406],\n                         std=[0.229,0.224,0.225]),\n])\n\n# Applied to BOTH train and test sets",
                'opts' => [
                    ['RandomHorizontalFlip changes the label', false],
                    ['Augmentation transforms (RandomHorizontalFlip, RandomRotation) should only be applied to the TRAINING set — the test set should only use ToTensor + Normalize', true],
                    ['Normalize values are incorrect for this dataset', false],
                    ['ToTensor must come before Normalize always', false],
                ],
            ],
            [
                'q' => "What is the receptive field of a CNN and why does it matter?",
                'opts' => [
                    ['The number of filters in the final layer', false],
                    ['The region of the original input image that influences a particular neuron\'s activation — deeper layers have larger receptive fields, allowing detection of larger structures', true],
                    ['The range of pixel values the model can process', false],
                    ['The spatial resolution of the output feature map', false],
                ],
            ],
            [
                'q' => "In object detection (YOLO, Faster R-CNN), the Intersection over Union (IoU) metric is used to:",
                'opts' => [
                    ['Compare feature map activations', false],
                    ['Measure the overlap between a predicted bounding box and the ground truth box — used for both evaluation and NMS (non-maximum suppression)', true],
                    ['Normalise anchor box dimensions', false],
                    ['Compute classification loss', false],
                ],
            ],
            [
                'q' => "What is a Vision Transformer (ViT) and how does it process images?",
                'opts' => [
                    ['A CNN trained on visual text (OCR)', false],
                    ['An image is split into fixed-size patches, each patch is linearly embedded, and a standard transformer encoder processes the sequence of patch embeddings', true],
                    ['A transformer that generates image captions', false],
                    ['A GAN architecture for image synthesis', false],
                ],
            ],

            // ── AUDIO DATA — ADVANCED ────────────────────────────────────
            [
                'q' => "This audio feature extraction code is inefficient for a batch of 10,000 files. What is the performance issue?\n\nimport librosa\n\nfeatures = []\nfor path in audio_paths:\n    y, sr = librosa.load(path, sr=22050)\n    mfcc = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=13)\n    features.append(mfcc.mean(axis=1))\n\nprint(len(features))",
                'opts' => [
                    ['librosa.load is not thread-safe', false],
                    ['Sequential single-threaded loading of 10,000 audio files is I/O bound — should use multiprocessing or concurrent.futures.ThreadPoolExecutor to parallelise file loading', true],
                    ['mfcc.mean(axis=1) returns the wrong shape', false],
                    ['sr=22050 is too high a sample rate', false],
                ],
            ],
            [
                'q' => "Wav2Vec 2.0 is a self-supervised audio model that learns representations by:",
                'opts' => [
                    ['Predicting the next audio frame like a language model', false],
                    ['Masking portions of the raw waveform and learning to identify the correct quantised speech unit from distractors — like BERT\'s MLM but for audio', true],
                    ['Classifying audio into predefined phoneme categories', false],
                    ['Reconstructing the spectrogram from MFCCs', false],
                ],
            ],
            [
                'q' => "The Whisper model for ASR (Automatic Speech Recognition) uses which architecture?",
                'opts' => [
                    ['A CTC-based recurrent model', false],
                    ['A transformer encoder-decoder trained on weakly supervised multilingual speech data', true],
                    ['A CNN-only pipeline on Mel spectrograms', false],
                    ['A Wav2Vec 2.0 fine-tuned model', false],
                ],
            ],

            // ── MULTIMODAL & CONTRASTIVE LEARNING ────────────────────────
            [
                'q' => "CLIP (Contrastive Language-Image Pre-training) is trained using:",
                'opts' => [
                    ['Supervised image classification on ImageNet labels', false],
                    ['Contrastive learning on image-text pairs — matching images to their correct captions while pushing apart mismatched pairs in a shared embedding space', true],
                    ['An autoencoder that reconstructs images from text', false],
                    ['A GAN trained on image-caption pairs', false],
                ],
            ],
            [
                'q' => "What makes CLIP zero-shot capable for image classification?",
                'opts' => [
                    ['It memorises every ImageNet class', false],
                    ['It encodes candidate class labels as text and finds the image embedding closest to each text embedding — no task-specific training needed', true],
                    ['It uses a lookup table of class embeddings', false],
                    ['Its image encoder outputs class probabilities directly', false],
                ],
            ],
            [
                'q' => "Contrastive loss (InfoNCE) in self-supervised learning works by:",
                'opts' => [
                    ['Minimising the L2 distance between all pairs', false],
                    ['Pulling together representations of augmented views of the same sample (positives) and pushing apart representations of different samples (negatives)', true],
                    ['Classifying samples into predefined similarity buckets', false],
                    ['Maximising the variance of embeddings', false],
                ],
            ],
            [
                'q' => "In SimCLR (a contrastive learning framework), the projection head added on top of the encoder is:",
                'opts' => [
                    ['Used for the final downstream task', false],
                    ['A small MLP used only during contrastive pre-training — discarded when fine-tuning for downstream tasks; its purpose is to map to a space where contrastive loss is better applied', true],
                    ['A linear layer that replaces the encoder', false],
                    ['The classification output layer', false],
                ],
            ],

            // ── LLM INTERNALS — ADVANCED ─────────────────────────────────
            [
                'q' => "What is the purpose of the feed-forward sublayer in each transformer block?",
                'opts' => [
                    ['To aggregate information from different attention heads', false],
                    ['To apply a position-wise non-linear transformation to each token independently — providing the network\'s capacity to store factual knowledge', true],
                    ['To apply positional encoding', false],
                    ['To normalise the attention output', false],
                ],
            ],
            [
                'q' => "What is LoRA (Low-Rank Adaptation) and why is it used for LLM fine-tuning?",
                'opts' => [
                    ['A new attention mechanism that reduces computation', false],
                    ['A parameter-efficient fine-tuning method that freezes original weights and adds small trainable low-rank decomposition matrices — dramatically reducing trainable parameters while preserving most performance', true],
                    ['A distillation technique to create smaller models', false],
                    ['A quantisation scheme for reducing model precision', false],
                ],
            ],
            [
                'q' => "What is catastrophic forgetting in the context of fine-tuning LLMs, and how does continual learning address it?",
                'opts' => [
                    ['The model forgets its training data after deployment', false],
                    ['Fine-tuning on a new task causes the model to lose performance on previously learned tasks. Continual learning uses techniques like elastic weight consolidation (EWC) or replay to mitigate this', true],
                    ['The model forgets rare words over training iterations', false],
                    ['Gradient vanishing during backpropagation through many layers', false],
                ],
            ],
            [
                'q' => "What is the key difference between instruction fine-tuning and RLHF (Reinforcement Learning from Human Feedback)?",
                'opts' => [
                    ['They are identical techniques', false],
                    ['Instruction fine-tuning trains on (instruction, response) pairs via standard supervised learning. RLHF uses a reward model trained on human preferences to optimise the LLM via PPO, aligning it more closely with human values', true],
                    ['RLHF uses labelled data; instruction fine-tuning does not', false],
                    ['Instruction fine-tuning requires more compute than RLHF', false],
                ],
            ],
            [
                'q' => "Chain-of-thought prompting improves LLM reasoning by:",
                'opts' => [
                    ['Adding more training data', false],
                    ['Prompting the model to generate intermediate reasoning steps before the final answer, improving performance on multi-step arithmetic, logic, and commonsense tasks', true],
                    ['Reducing the temperature to force deterministic outputs', false],
                    ['Fine-tuning the model on reasoning datasets', false],
                ],
            ],

            // ── EVALUATION & METRICS ─────────────────────────────────────
            [
                'q' => "BLEU score is used to evaluate:",
                'opts' => [
                    ['Sentiment classification accuracy', false],
                    ['Machine translation quality by comparing n-gram overlap between generated and reference translations', true],
                    ['Topic model coherence', false],
                    ['Named entity recognition F1', false],
                ],
            ],
            [
                'q' => "A BLEU score limitation is that it:",
                'opts' => [
                    ['Cannot handle multiple reference translations', false],
                    ['Measures surface n-gram overlap and ignores semantic similarity — a valid paraphrase with different wording gets a low score', true],
                    ['Only works for English text', false],
                    ['Requires human annotators for every evaluation', false],
                ],
            ],
            [
                'q' => "BERTScore evaluates text generation quality by:",
                'opts' => [
                    ['Counting exact n-gram matches like BLEU', false],
                    ['Computing token-level cosine similarity between BERT embeddings of the generated and reference text — capturing semantic similarity beyond surface overlap', true],
                    ['Using a human preference model', false],
                    ['Measuring perplexity of the generated text', false],
                ],
            ],
            [
                'q' => "In information retrieval evaluation, Mean Average Precision (MAP) measures:",
                'opts' => [
                    ['The average F1 score across all queries', false],
                    ['The mean of Average Precision (AP) scores across queries, where AP rewards systems that rank relevant documents higher', true],
                    ['The accuracy of the top-1 retrieved document', false],
                    ['The recall at K across all queries', false],
                ],
            ],
            [
                'q' => "What does perplexity measure for a language model?",
                'opts' => [
                    ['The model\'s accuracy on a classification task', false],
                    ['How well the model predicts a held-out text sample — lower perplexity means the model assigns higher probability to the actual text', true],
                    ['The number of unique tokens in the vocabulary', false],
                    ['The training loss only', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 20 — Analysis of Unstructured Data (Advanced).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Advanced");
    }
}