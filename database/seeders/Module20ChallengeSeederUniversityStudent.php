<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module20ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Analysis of Unstructured Data')
                 ->delete();

        $this->command->info("Creating Module 20 — Analysis of Unstructured Data (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Analysis of Unstructured Data',
            'description'           => 'Analytical questions spanning text preprocessing pipelines, TF-IDF calculations, word embedding geometry, LDA topic modeling mechanics, CNN image features, and transformer attention. Requires tracing logic and interpreting model outputs.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 750,
            'order_index'           => 20,
        ]);

        $this->command->info("Seeding 50 university-level Analysis of Unstructured Data questions...");

        $qaData = [

            // ── 20.2 TEXT PREPROCESSING ───────────────────────────────────
            [
                'q' => 'After applying the standard preprocessing pipeline (lowercase → remove punctuation → remove stop words → stem) to the sentence:\n"The DOGS are running quickly!"\nWhat tokens remain?',
                'opts' => [
                    ['the, dogs, are, running, quickly', false],
                    ['dog, run, quickli', true],
                    ['dogs, running, quickly', false],
                    ['DOGS, RUNNING, QUICKLY', false],
                ],
            ],
            [
                'q' => 'What is the difference between a word tokeniser and a sentence tokeniser?',
                'opts' => [
                    ['They are identical — both split on whitespace', false],
                    ['A word tokeniser splits text into individual words/tokens; a sentence tokeniser splits text into sentences', true],
                    ['A sentence tokeniser removes stop words; a word tokeniser does not', false],
                    ['Word tokenisers only work on English; sentence tokenisers are language-agnostic', false],
                ],
            ],
            [
                'q' => 'Which stemming algorithm is known to be more aggressive (producing shorter, less recognisable stems) than the Lancaster stemmer?',
                'opts' => [
                    ['Porter Stemmer', false],
                    ['Snowball Stemmer', false],
                    ['Lancaster Stemmer is the most aggressive of the three', true],
                    ['WordNet Lemmatiser', false],
                ],
            ],
            [
                'q' => 'Why is lemmatisation generally preferred over stemming for high-accuracy NLP tasks?',
                'opts' => [
                    ['Lemmatisation is always faster', false],
                    ['Lemmatisation produces real dictionary words using vocabulary and morphological analysis, whereas stems can be non-words', true],
                    ['Stemming cannot handle verbs', false],
                    ['Lemmatisation removes stop words automatically', false],
                ],
            ],
            [
                'q' => 'A character-level n-gram with n=3 (trigram) for the word "data" produces which set of substrings?',
                'opts' => [
                    ['"dat", "ata"', true],
                    ['"d", "a", "t", "a"', false],
                    ['"data"', false],
                    ['"da", "at", "ta"', false],
                ],
            ],
            [
                'q' => 'Regular expressions in text preprocessing are used to:',
                'opts' => [
                    ['Train language models', false],
                    ['Match and extract patterns in text (e.g. removing URLs, emails, or special characters)', true],
                    ['Compute word frequencies', false],
                    ['Measure sentence similarity', false],
                ],
            ],

            // ── 20.3 BAG-OF-WORDS & TF-IDF ───────────────────────────────
            [
                'q' => 'Given the corpus:\n  Doc1: "cat sat mat"\n  Doc2: "cat mat"\n  Doc3: "dog sat"\n\nWhat is the TF of "cat" in Doc1?',
                'opts' => [
                    ['1', false],
                    ['1/3', true],
                    ['2/3', false],
                    ['3', false],
                ],
            ],
            [
                'q' => 'Using the same corpus (3 documents), what is the IDF of "cat"?\nIDF = log(N / df) where N=3 and df = number of docs containing "cat".',
                'opts' => [
                    ['log(3/3) = 0', false],
                    ['log(3/2) ≈ 0.405', true],
                    ['log(3/1) ≈ 1.099', false],
                    ['log(2/3) ≈ −0.405', false],
                ],
            ],
            [
                'q' => 'The TF-IDF score for word "cat" in Doc1 (TF=1/3, IDF≈0.405) is approximately:',
                'opts' => [
                    ['0.135', true],
                    ['0.405', false],
                    ['0.333', false],
                    ['1.215', false],
                ],
            ],
            [
                'q' => 'A document-term matrix for a corpus of 1,000 documents and a vocabulary of 50,000 words is likely to be:',
                'opts' => [
                    ['Dense — most cells are filled with non-zero values', false],
                    ['Sparse — most cells are zero because each document only contains a small fraction of all vocabulary words', true],
                    ['Symmetric', false],
                    ['A square matrix', false],
                ],
            ],
            [
                'q' => 'Which scikit-learn class builds a TF-IDF matrix from a list of text documents?',
                'opts' => [
                    ['CountVectorizer', false],
                    ['TfidfVectorizer', true],
                    ['HashingVectorizer', false],
                    ['LabelEncoder', false],
                ],
            ],

            // ── 20.4 WORD EMBEDDINGS ──────────────────────────────────────
            [
                'q' => 'In Word2Vec, the CBOW (Continuous Bag of Words) architecture:',
                'opts' => [
                    ['Predicts surrounding context words given the target word', false],
                    ['Predicts the target word given its surrounding context words', true],
                    ['Predicts the topic of a document', false],
                    ['Predicts the next sentence', false],
                ],
            ],
            [
                'q' => 'In Word2Vec, the Skip-gram architecture:',
                'opts' => [
                    ['Predicts the target word given its context', false],
                    ['Predicts the surrounding context words given the target word', true],
                    ['Uses character-level n-grams', false],
                    ['Requires labelled training data', false],
                ],
            ],
            [
                'q' => 'Cosine similarity between two word vectors is used to measure:',
                'opts' => [
                    ['The Euclidean distance between two points in space', false],
                    ['The angle between two vectors — capturing semantic similarity regardless of magnitude', true],
                    ['The sum of all vector components', false],
                    ['The L2 norm of the difference', false],
                ],
            ],
            [
                'q' => 'The famous word analogy "king − man + woman = ?" in Word2Vec embeddings gives:',
                'opts' => [
                    ['princess', false],
                    ['queen', true],
                    ['female', false],
                    ['crown', false],
                ],
            ],
            [
                'q' => 'Why are pre-trained word embeddings (like GloVe) useful for downstream NLP tasks?',
                'opts' => [
                    ['They eliminate the need for any further training', false],
                    ['They provide a rich initialisation that captures general semantic knowledge, reducing the data and compute needed for a specific task', true],
                    ['They are always more accurate than task-trained embeddings', false],
                    ['They can replace labelled data entirely', false],
                ],
            ],

            // ── 20.5 TOPIC MODELING ───────────────────────────────────────
            [
                'q' => 'In LDA, the two key distributions learned are:',
                'opts' => [
                    ['Word-sentence and sentence-document', false],
                    ['Topic-document distribution (θ) and word-topic distribution (φ)', true],
                    ['TF and IDF per topic', false],
                    ['A prior and a likelihood only', false],
                ],
            ],
            [
                'q' => 'The number of topics k in LDA is:',
                'opts' => [
                    ['Automatically determined from the data', false],
                    ['A hyperparameter that must be set by the user before training', true],
                    ['Always equal to the number of documents', false],
                    ['Equal to the vocabulary size', false],
                ],
            ],
            [
                'q' => 'Perplexity in the context of topic models measures:',
                'opts' => [
                    ['How many topics were discovered', false],
                    ['How well the model predicts held-out documents — lower perplexity means better generalisation', true],
                    ['The average document length', false],
                    ['The number of unique words per topic', false],
                ],
            ],
            [
                'q' => 'NMF (Non-negative Matrix Factorization) decomposes a document-term matrix V (n×m) into:',
                'opts' => [
                    ['Two matrices V = W × H where W (n×k) contains document-topic weights and H (k×m) contains topic-word weights', true],
                    ['Three matrices like SVD', false],
                    ['One diagonal matrix', false],
                    ['A matrix and its inverse', false],
                ],
            ],
            [
                'q' => 'Which evaluation metric assesses topic coherence by measuring how often high-probability topic words co-occur in real documents?',
                'opts' => [
                    ['BLEU score', false],
                    ['Coherence score (e.g. C_v or NPMI)', true],
                    ['F1 score', false],
                    ['AUC-ROC', false],
                ],
            ],

            // ── 20.6 SENTIMENT ANALYSIS ───────────────────────────────────
            [
                'q' => 'VADER sentiment analysis returns four scores: pos, neg, neu, and compound. The compound score:',
                'opts' => [
                    ['Is the average of pos, neg, and neu', false],
                    ['Is a normalised score between −1 (most negative) and +1 (most positive) representing overall sentiment', true],
                    ['Is always between 0 and 1', false],
                    ['Represents the probability of being neutral', false],
                ],
            ],
            [
                'q' => 'Aspect-based sentiment analysis (ABSA) differs from standard sentiment analysis because it:',
                'opts' => [
                    ['Only works on product reviews', false],
                    ['Identifies sentiment toward specific aspects of an entity (e.g. "battery life is great but screen is terrible")', true],
                    ['Uses lexicons instead of machine learning', false],
                    ['Only classifies text as positive or negative (binary)', false],
                ],
            ],
            [
                'q' => 'The sentence "The phone is not bad" would likely be misclassified by a naive Bag-of-Words sentiment model because:',
                'opts' => [
                    ['It contains the word "phone" which is neutral', false],
                    ['The BoW model may treat "bad" as negative without understanding the negation "not"', true],
                    ['BoW cannot handle 5-word sentences', false],
                    ['Stop word removal would delete all meaningful words', false],
                ],
            ],

            // ── 20.7 NAMED ENTITY RECOGNITION ────────────────────────────
            [
                'q' => 'The BIO tagging scheme in NER labels tokens as:',
                'opts' => [
                    ['Bold, Italic, Other', false],
                    ['B (beginning of entity), I (inside entity), O (outside / non-entity)', true],
                    ['Binary, Integer, Object', false],
                    ['Begin, Internal, Output', false],
                ],
            ],
            [
                'q' => 'For the sentence "New York City is crowded", the BIO tags for a LOCATION entity would be:',
                'opts' => [
                    ['B-LOC I-LOC B-LOC O O', false],
                    ['B-LOC I-LOC I-LOC O O', true],
                    ['O B-LOC I-LOC O O', false],
                    ['B-LOC B-LOC B-LOC O O', false],
                ],
            ],
            [
                'q' => 'Coreference resolution in NLP is the task of:',
                'opts' => [
                    ['Finding named entities in text', false],
                    ['Identifying when different mentions in text (e.g. "Obama", "he", "the president") refer to the same real-world entity', true],
                    ['Linking entities to a knowledge base', false],
                    ['Classifying entity types (PERSON, ORG, etc.)', false],
                ],
            ],

            // ── 20.8 IMAGE DATA ───────────────────────────────────────────
            [
                'q' => 'A Convolutional Neural Network (CNN) learns to detect features in images using:',
                'opts' => [
                    ['Fully connected layers applied to raw pixel vectors', false],
                    ['Learnable filters (kernels) that slide over the image to detect local spatial features like edges and textures', true],
                    ['TF-IDF on image metadata', false],
                    ['Word embeddings applied to image captions', false],
                ],
            ],
            [
                'q' => 'Max pooling in a CNN is used to:',
                'opts' => [
                    ['Increase the spatial resolution of feature maps', false],
                    ['Downsample feature maps by taking the maximum value in each region, reducing size while retaining dominant features', true],
                    ['Add more filters to the convolutional layer', false],
                    ['Normalise pixel values between 0 and 1', false],
                ],
            ],
            [
                'q' => 'Transfer learning in image classification uses a model pre-trained on a large dataset (e.g. ImageNet) because:',
                'opts' => [
                    ['The model can never make mistakes on new data', false],
                    ['Lower layers have already learned general features (edges, textures) that are useful across many visual tasks', true],
                    ['ImageNet covers every possible visual category', false],
                    ['Pre-trained weights are always better than random initialisation for every dataset', false],
                ],
            ],
            [
                'q' => 'HOG (Histogram of Oriented Gradients) is a feature extraction method for images that captures:',
                'opts' => [
                    ['Colour distribution across the image', false],
                    ['The distribution of gradient directions in local regions, useful for detecting object shape and structure', true],
                    ['Average pixel intensity', false],
                    ['The frequency content via Fourier transform', false],
                ],
            ],

            // ── 20.9 AUDIO DATA ───────────────────────────────────────────
            [
                'q' => 'The Fourier Transform applied to an audio signal converts it from:',
                'opts' => [
                    ['Time domain to frequency domain', true],
                    ['Frequency domain to time domain', false],
                    ['Amplitude to phase', false],
                    ['Waveform to spectrogram (they are different transforms)', false],
                ],
            ],
            [
                'q' => 'A Mel spectrogram differs from a standard spectrogram because it uses:',
                'opts' => [
                    ['Linear frequency spacing', false],
                    ['The Mel scale — a perceptual frequency scale that approximates how humans hear pitch differences', true],
                    ['Amplitude instead of frequency', false],
                    ['Only the first 20 frequency bins', false],
                ],
            ],
            [
                'q' => 'MFCCs are widely used as audio features because they:',
                'opts' => [
                    ['Represent the raw waveform directly', false],
                    ['Compact the most perceptually relevant spectral information into a small number of coefficients (typically 13–40)', true],
                    ['Always produce exactly 256 features per frame', false],
                    ['Work only for music, not speech', false],
                ],
            ],
            [
                'q' => 'Zero-crossing rate (ZCR) in audio signal processing measures:',
                'opts' => [
                    ['The average amplitude of the signal', false],
                    ['How often the signal changes sign (crosses zero) — useful for distinguishing voiced/unvoiced speech and music/silence', true],
                    ['The number of beats per minute', false],
                    ['The length of the audio file in seconds', false],
                ],
            ],

            // ── 20.10 TRANSFORMERS & LLMs ────────────────────────────────
            [
                'q' => 'In the transformer self-attention mechanism, Query (Q), Key (K), and Value (V) matrices are computed from:',
                'opts' => [
                    ['External word embeddings only', false],
                    ['The input embeddings via learned linear projections', true],
                    ['A fixed lookup table', false],
                    ['The output of the previous layer\'s pooling', false],
                ],
            ],
            [
                'q' => 'Scaled dot-product attention is computed as:\nAttention(Q,K,V) = softmax(QKᵀ / √dₖ) · V\nWhy is the scaling factor √dₖ used?',
                'opts' => [
                    ['To increase the magnitude of the attention scores', false],
                    ['To prevent the dot products from growing too large in high dimensions, which would push softmax into regions with very small gradients', true],
                    ['To normalise the Value matrix', false],
                    ['To convert the output to probabilities', false],
                ],
            ],
            [
                'q' => 'Multi-head attention in transformers uses multiple attention heads to:',
                'opts' => [
                    ['Process multiple documents simultaneously', false],
                    ['Attend to different representation subspaces at different positions simultaneously, capturing diverse types of relationships', true],
                    ['Reduce the number of parameters', false],
                    ['Replace positional encoding', false],
                ],
            ],
            [
                'q' => 'Positional encoding is added to transformer input embeddings because:',
                'opts' => [
                    ['It replaces the embedding layer', false],
                    ['Self-attention has no built-in notion of word order — positional encoding injects sequence position information', true],
                    ['It reduces the dimensionality of embeddings', false],
                    ['It is required for multi-head attention to work', false],
                ],
            ],
            [
                'q' => 'BERT uses a MASKED LANGUAGE MODEL (MLM) pre-training objective, which means:',
                'opts' => [
                    ['BERT generates the next word in a sequence', false],
                    ['Random tokens are masked and the model learns to predict them from the surrounding context on BOTH sides', true],
                    ['BERT only processes text from left to right', false],
                    ['The model learns to classify entire documents', false],
                ],
            ],
            [
                'q' => 'GPT models are pre-trained using a CAUSAL LANGUAGE MODEL (CLM) objective, which means:',
                'opts' => [
                    ['Bidirectional context is used for prediction', false],
                    ['The model predicts the next token given only the previous tokens — it is autoregressive and left-to-right', true],
                    ['Random tokens are masked and predicted', false],
                    ['The model classifies entire sequences', false],
                ],
            ],
            [
                'q' => 'The Hugging Face `transformers` library is commonly used in Python to:',
                'opts' => [
                    ['Build and train neural networks from scratch only', false],
                    ['Load, fine-tune, and run inference with pre-trained transformer models via a unified API', true],
                    ['Process audio files into spectrograms', false],
                    ['Visualise word frequencies', false],
                ],
            ],
            [
                'q' => 'What is tokenisation in the context of transformer models (e.g. BERT, GPT)?',
                'opts' => [
                    ['Splitting text into sentences', false],
                    ['Converting text into subword token IDs using a vocabulary learned during pre-training (e.g. WordPiece, BPE)', true],
                    ['Lowercasing all text', false],
                    ['Removing punctuation and stop words', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 20 — Analysis of Unstructured Data (University Student).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: University Student");
    }
}