<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module20ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Analysis of Unstructured Data')
                 ->delete();

        $this->command->info("Creating Module 20 — Analysis of Unstructured Data (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Analysis of Unstructured Data',
            'description'           => 'Multi-step problems combining Python code tracing, pipeline design, model evaluation, and implementation choices across text, image, and audio analysis. Requires applying and combining concepts to interpret real model behaviour.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 1000,
            'order_index'           => 20,
        ]);

        $this->command->info("Seeding 50 intermediate-level Analysis of Unstructured Data questions...");

        $qaData = [

            // ── 20.2 TEXT PREPROCESSING — CODE TRACING ────────────────────
            [
                'q' => "What is the output of this code?\n\nimport re\ntext = 'Hello World! Visit https://example.com for more info.'\ntext = re.sub(r'http\\S+', '', text)\ntext = re.sub(r'[^a-zA-Z\\s]', '', text)\ntext = text.lower().strip()\nprint(text)",
                'opts' => [
                    ['"hello world visit httpexamplecom for more info"', false],
                    ['"hello world  visit  for more info"', true],
                    ['"hello world"', false],
                    ['"hello world visit for more info"', false],
                ],
            ],
            [
                'q' => "What does this preprocessing pipeline produce for input 'Studies studying studied'?\n\nfrom nltk.stem import PorterStemmer\nps = PorterStemmer()\ntokens = 'Studies studying studied'.lower().split()\nresult = [ps.stem(t) for t in tokens]\nprint(result)",
                'opts' => [
                    ["['study', 'study', 'study']", true],
                    ["['studies', 'studying', 'studied']", false],
                    ["['studi', 'studi', 'studi']", false],
                    ["['studiesing', 'studyied', 'studyed']", false],
                ],
            ],
            [
                'q' => "What does this code count?\n\nfrom collections import Counter\nfrom nltk.corpus import stopwords\n\ntext = 'the cat sat on the mat the cat'\nstop = set(stopwords.words('english'))\ntokens = [w for w in text.split() if w not in stop]\nprint(Counter(tokens).most_common(2))",
                'opts' => [
                    ["[('the', 3), ('cat', 2)]", false],
                    ["[('cat', 2), ('sat', 1)]", true],
                    ["[('cat', 2), ('mat', 1)]", false],
                    ["[('the', 3), ('sat', 1)]", false],
                ],
            ],
            [
                'q' => "A pipeline applies these steps in order to a tweet:\n1. Lowercase\n2. Remove URLs (regex)\n3. Remove @mentions\n4. Remove punctuation\n5. Tokenise on whitespace\n6. Remove stop words\n\nFor input '@JohnDoe Check https://t.co/abc this out!!!'\nHow many tokens remain after all steps?",
                'opts' => [
                    ['0', false],
                    ['1', false],
                    ['2', true],
                    ['4', false],
                ],
            ],

            // ── 20.3 TF-IDF — MULTI-STEP CALCULATIONS ────────────────────
            [
                'q' => "A corpus has 5 documents. The word 'neural' appears in 2 of them.\nIDF (smooth) = log((1 + N) / (1 + df)) + 1\n\nWhat is the smooth IDF for 'neural'?",
                'opts' => [
                    ['log(6/3) + 1 ≈ 1.693', false],
                    ['log(6/3) + 1 ≈ 1.405 + 1 = 2.0 (using natural log: ln(2) + 1 ≈ 1.693)', true],
                    ['log(5/2) ≈ 0.916', false],
                    ['0', false],
                ],
            ],
            [
                'q' => "You have a 1000-document corpus. After building a TF-IDF matrix with max_features=500, you get a sparse matrix of shape (1000, 500). What is the maximum possible number of non-zero entries, and why is the actual number much smaller?",
                'opts' => [
                    ['500,000 max; smaller because each document only uses a small subset of the vocabulary', true],
                    ['1,000 max; limited by the number of documents', false],
                    ['500 max; limited by vocabulary size', false],
                    ['The matrix is always dense', false],
                ],
            ],
            [
                'q' => "Which scikit-learn parameter in TfidfVectorizer would you set to IGNORE words appearing in more than 80% of documents?",
                'opts' => [
                    ['min_df=0.8', false],
                    ['max_df=0.8', true],
                    ['max_features=0.8', false],
                    ['stop_words=0.8', false],
                ],
            ],

            // ── 20.4 WORD EMBEDDINGS — CODE & GEOMETRY ───────────────────
            [
                'q' => "What does this code compute?\n\nfrom gensim.models import Word2Vec\nimport numpy as np\n\nmodel = Word2Vec.load('word2vec.model')\nv1 = model.wv['doctor']\nv2 = model.wv['nurse']\ncos_sim = np.dot(v1, v2) / (np.linalg.norm(v1) * np.linalg.norm(v2))\nprint(cos_sim)",
                'opts' => [
                    ['The Euclidean distance between the word vectors for "doctor" and "nurse"', false],
                    ['The cosine similarity between the word vectors for "doctor" and "nurse"', true],
                    ['The dot product magnitude of both vectors combined', false],
                    ['The probability that "doctor" and "nurse" appear together', false],
                ],
            ],
            [
                'q' => "Two word vectors have cosine similarity = −0.85. What does this indicate?",
                'opts' => [
                    ['The words are semantically similar', false],
                    ['The words are semantically unrelated', false],
                    ['The words are semantically opposite or strongly dissimilar', true],
                    ['The vectors are perpendicular (orthogonal)', false],
                ],
            ],
            [
                'q' => "FastText generates an embedding for an OUT-OF-VOCABULARY word 'NLPython' by:",
                'opts' => [
                    ['Returning a zero vector', false],
                    ['Averaging the embeddings of its character n-grams (e.g. \"NLP\", \"LPy\", \"Pyt\", etc.)', true],
                    ['Using the nearest known word embedding', false],
                    ['Raising a KeyError exception', false],
                ],
            ],
            [
                'q' => "What is a potential bias issue with pre-trained word embeddings trained on web text?",
                'opts' => [
                    ['They are too small dimensionally', false],
                    ['They encode social biases present in the training data (e.g. gender stereotypes in word analogies)', true],
                    ['They cannot represent rare words', false],
                    ['They are specific to one language only', false],
                ],
            ],

            // ── 20.5 TOPIC MODELING — CODE & INTERPRETATION ───────────────
            [
                'q' => "After training an LDA model with k=3 topics on a news corpus, Topic 0 has top words:\n['election', 'vote', 'president', 'campaign', 'poll']\n\nWhat label would you assign to this topic?",
                'opts' => [
                    ['Sports', false],
                    ['Technology', false],
                    ['Politics / Elections', true],
                    ['Entertainment', false],
                ],
            ],
            [
                'q' => "What does this LDA code do?\n\nfrom sklearn.decomposition import LatentDirichletAllocation\nfrom sklearn.feature_extraction.text import CountVectorizer\n\nvectorizer = CountVectorizer(max_df=0.9, min_df=2)\nX = vectorizer.fit_transform(docs)\nlda = LatentDirichletAllocation(n_components=5, random_state=42)\nlda.fit(X)\ntopic_dist = lda.transform(X[0:1])\nprint(topic_dist)",
                'opts' => [
                    ['Prints the top 5 words for each topic', false],
                    ['Prints the topic probability distribution for the first document', true],
                    ['Prints the vocabulary of the model', false],
                    ['Prints the perplexity score', false],
                ],
            ],
            [
                'q' => "Why is CountVectorizer (raw counts) used for LDA instead of TfidfVectorizer?",
                'opts' => [
                    ['CountVectorizer is always faster', false],
                    ['LDA is a probabilistic model based on word counts — TF-IDF reweighting distorts the word count distribution that LDA needs to model', true],
                    ['TfidfVectorizer cannot handle large corpora', false],
                    ['LDA requires binary features only', false],
                ],
            ],

            // ── 20.6 SENTIMENT ANALYSIS — INTERMEDIATE ────────────────────
            [
                'q' => "A logistic regression sentiment classifier is trained on 10,000 movie reviews.\nTest set: 1,000 reviews — 700 positive, 300 negative.\nThe model predicts ALL as positive.\n\nWhat is the accuracy, and why is it misleading?",
                'opts' => [
                    ['50% — always predicts the majority class', false],
                    ['70% — but this is misleading because the model has zero recall for negative reviews', true],
                    ['100% — trivially perfect', false],
                    ['30% — based on minority class performance', false],
                ],
            ],
            [
                'q' => "Which evaluation metric is most appropriate for imbalanced sentiment classification datasets?",
                'opts' => [
                    ['Raw accuracy', false],
                    ['Macro-averaged F1 score, which treats all classes equally regardless of frequency', true],
                    ['Mean Squared Error', false],
                    ['Perplexity', false],
                ],
            ],
            [
                'q' => "A transformer-based sentiment model returns logits [2.1, −1.3, 0.4] for classes [positive, negative, neutral].\nAfter applying softmax, which class is predicted?",
                'opts' => [
                    ['Negative', false],
                    ['Neutral', false],
                    ['Positive (logit 2.1 is highest)', true],
                    ['Cannot determine without labels', false],
                ],
            ],

            // ── 20.7 NER — CODE TRACING ───────────────────────────────────
            [
                'q' => "What does this spaCy code print?\n\nimport spacy\nnlp = spacy.load('en_core_web_sm')\ndoc = nlp('Elon Musk founded SpaceX in 2002 in California.')\nfor ent in doc.ents:\n    print(ent.text, ent.label_)",
                'opts' => [
                    ['"Elon Musk PERSON, SpaceX ORG, 2002 DATE, California GPE"', true],
                    ['"Elon PERSON, Musk PERSON, SpaceX ORG, 2002 CARDINAL"', false],
                    ['"Elon Musk ORG, SpaceX PERSON, 2002 GPE"', false],
                    ['No output — spaCy does not detect entities in this sentence', false],
                ],
            ],
            [
                'q' => "Entity Linking (EL) extends NER by:",
                'opts' => [
                    ['Tagging more entity types', false],
                    ['Linking detected entities to canonical entries in a knowledge base (e.g. Wikidata, Wikipedia)', true],
                    ['Resolving pronouns to their referents', false],
                    ['Detecting relationships between two entities', false],
                ],
            ],
            [
                'q' => "In Relation Extraction, the goal is to identify:",
                'opts' => [
                    ['The entity type of each named entity', false],
                    ['Semantic relationships between pairs of entities in text (e.g. "founded_by", "located_in")', true],
                    ['The sentiment of sentences containing entities', false],
                    ['Co-referring entity mentions', false],
                ],
            ],

            // ── 20.8 IMAGE DATA — INTERMEDIATE ────────────────────────────
            [
                'q' => "A CNN has a convolutional layer with 32 filters of size 3×3 applied to a 28×28×1 input (no padding, stride=1).\nWhat is the output feature map size?",
                'opts' => [
                    ['28×28×32', false],
                    ['26×26×32', true],
                    ['14×14×32', false],
                    ['26×26×1', false],
                ],
            ],
            [
                'q' => "What is the purpose of batch normalisation in a CNN?",
                'opts' => [
                    ['Reducing the number of filters', false],
                    ['Normalising layer activations to have zero mean and unit variance, stabilising training and allowing higher learning rates', true],
                    ['Augmenting the training data', false],
                    ['Preventing gradient explosion by clipping', false],
                ],
            ],
            [
                'q' => "What does this image preprocessing code produce?\n\nimport numpy as np\nfrom PIL import Image\n\nimg = Image.open('photo.jpg').convert('L').resize((64, 64))\narr = np.array(img) / 255.0\nprint(arr.shape, arr.max())",
                'opts' => [
                    ['(64, 64, 3) 1.0', false],
                    ['(64, 64) 1.0', true],
                    ['(64, 64, 1) 255.0', false],
                    ['(3, 64, 64) 1.0', false],
                ],
            ],
            [
                'q' => "Which technique would you use to visualise WHICH parts of an image a CNN focuses on when making a classification decision?",
                'opts' => [
                    ['PCA on the pixel values', false],
                    ['Grad-CAM (Gradient-weighted Class Activation Mapping)', true],
                    ['Histogram of Oriented Gradients', false],
                    ['SIFT feature detection', false],
                ],
            ],
            [
                'q' => "In transfer learning, 'freezing' the base model layers means:",
                'opts' => [
                    ['Preventing the model from being saved', false],
                    ['Setting those layer weights to non-trainable so they are not updated during fine-tuning', true],
                    ['Converting the model to a static graph', false],
                    ['Removing the top classification layers', false],
                ],
            ],

            // ── 20.9 AUDIO DATA — INTERMEDIATE ────────────────────────────
            [
                'q' => "What does this librosa code extract?\n\nimport librosa\ny, sr = librosa.load('speech.wav', sr=22050)\nmfcc = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=13)\nprint(mfcc.shape)",
                'opts' => [
                    ['A (13,) vector of mean MFCC values', false],
                    ['A (13, T) matrix where T is the number of time frames', true],
                    ['A (22050, 13) matrix of raw samples', false],
                    ['A (1, 13) array for the entire file', false],
                ],
            ],
            [
                'q' => "Why is it common to compute the DELTA and DELTA-DELTA of MFCCs as additional audio features?",
                'opts' => [
                    ['To double the number of features artificially', false],
                    ['Delta captures the rate of change of MFCCs (velocity), delta-delta captures acceleration — together they describe temporal dynamics in speech', true],
                    ['To reduce the dimensionality of MFCC features', false],
                    ['Delta removes noise from the MFCC coefficients', false],
                ],
            ],
            [
                'q' => "A voice activity detection (VAD) system needs to distinguish speech from silence. Which simple feature is most useful?",
                'opts' => [
                    ['MFCCs', false],
                    ['Root Mean Square Energy (RMSE) and Zero-Crossing Rate', true],
                    ['Mel spectrogram frequency bins', false],
                    ['Fundamental frequency (F0)', false],
                ],
            ],

            // ── 20.10 TRANSFORMERS & LLMs — INTERMEDIATE ──────────────────
            [
                'q' => "What does this Hugging Face pipeline code do?\n\nfrom transformers import pipeline\nnlp = pipeline('sentiment-analysis')\nresult = nlp('I absolutely loved this movie!')\nprint(result)",
                'opts' => [
                    ['Prints the tokenised version of the input sentence', false],
                    ['Prints [{ \"label\": \"POSITIVE\", \"score\": ~0.99 }] — the predicted sentiment and confidence', true],
                    ['Prints the attention weights of the transformer', false],
                    ['Prints the embeddings of each token', false],
                ],
            ],
            [
                'q' => "What is the difference between encoder-only models (BERT) and decoder-only models (GPT)?",
                'opts' => [
                    ['BERT generates text; GPT classifies text', false],
                    ['BERT uses bidirectional context (sees full input) — ideal for classification/understanding tasks. GPT uses left-to-right context — ideal for text generation', true],
                    ['BERT uses character tokenisation; GPT uses word tokenisation', false],
                    ['They are architecturally identical but trained on different data', false],
                ],
            ],
            [
                'q' => "BPE (Byte-Pair Encoding) tokenisation works by:",
                'opts' => [
                    ['Splitting text into individual characters only', false],
                    ['Iteratively merging the most frequent adjacent byte/character pairs in the training corpus to build a subword vocabulary', true],
                    ['Using a fixed vocabulary of the top 50,000 words', false],
                    ['Tokenising on whitespace and punctuation only', false],
                ],
            ],
            [
                'q' => "Prompt engineering for an LLM involves:",
                'opts' => [
                    ['Modifying the model weights', false],
                    ['Crafting input text carefully to guide the model toward a desired output without changing model parameters', true],
                    ['Retraining the model on new data', false],
                    ['Adding new layers to the transformer', false],
                ],
            ],
            [
                'q' => "In fine-tuning BERT for text classification, what is typically added on top of the pre-trained model?",
                'opts' => [
                    ['An additional transformer encoder block', false],
                    ['A simple linear (fully connected) classification layer on top of the [CLS] token representation', true],
                    ['A convolutional layer', false],
                    ['A softmax layer on every token', false],
                ],
            ],
            [
                'q' => "What does the [CLS] token in BERT represent after fine-tuning for classification?",
                'opts' => [
                    ['The classification of the first word only', false],
                    ['An aggregate sequence representation used as input to the classification head', true],
                    ['A separator between two input sentences', false],
                    ['The padding token', false],
                ],
            ],
            [
                'q' => "Sentence-BERT (SBERT) modifies BERT to produce:",
                'opts' => [
                    ['Better token-level representations for NER', false],
                    ['Fixed-size sentence embeddings that can be efficiently compared using cosine similarity for semantic search and similarity tasks', true],
                    ['Character-level embeddings for spell checking', false],
                    ['Audio representations aligned with text', false],
                ],
            ],
            [
                'q' => "What is RAG (Retrieval-Augmented Generation) in the context of LLMs?",
                'opts' => [
                    ['A method to reduce LLM hallucinations by randomly augmenting training data', false],
                    ['A technique that retrieves relevant documents from an external knowledge base and feeds them as context to the LLM before generation, grounding responses in retrieved facts', true],
                    ['A regularisation technique to prevent overfitting in fine-tuning', false],
                    ['A way to compress LLM weights for deployment', false],
                ],
            ],
            [
                'q' => "What is the primary purpose of the [SEP] token in BERT?",
                'opts' => [
                    ['To mark the end of the vocabulary', false],
                    ['To separate two segments (sentences) in the input, enabling tasks like natural language inference that take sentence pairs', true],
                    ['To replace masked tokens during pre-training', false],
                    ['To signal the beginning of a document', false],
                ],
            ],
            [
                'q' => "In the context of LLMs, 'hallucination' refers to:",
                'opts' => [
                    ['Visualising intermediate attention layers', false],
                    ['The model confidently generating text that is factually incorrect or entirely fabricated', true],
                    ['The model refusing to answer a question', false],
                    ['Repetitive outputs that loop endlessly', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 20 — Analysis of Unstructured Data (Intermediate).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Intermediate");
    }
}