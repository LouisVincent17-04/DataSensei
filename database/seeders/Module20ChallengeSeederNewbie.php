<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module20ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Analysis of Unstructured Data')
                 ->delete();

        $this->command->info("Creating Module 20 — Analysis of Unstructured Data (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Analysis of Unstructured Data',
            'description'           => 'Test your understanding of the very basics of unstructured data — what it is, common types, why it is challenging, and the key ideas behind text, image, and audio data. No prior coding experience required.',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 20,
        ]);

        $this->command->info("Seeding 50 newbie-level Analysis of Unstructured Data questions...");

        $qaData = [

            // ── 20.1 WHAT IS UNSTRUCTURED DATA ───────────────────────────
            [
                'q' => 'Which of the following is an example of UNSTRUCTURED data?',
                'opts' => [
                    ['A spreadsheet of employee salaries', false],
                    ['A database table of customer orders', false],
                    ['A collection of social media posts', true],
                    ['A CSV file of product prices', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of STRUCTURED data?',
                'opts' => [
                    ['A PDF document', false],
                    ['A recorded phone call', false],
                    ['A relational database table with rows and columns', true],
                    ['A folder of photos', false],
                ],
            ],
            [
                'q' => 'Approximately what percentage of all data in the world is estimated to be unstructured?',
                'opts' => [
                    ['10%', false],
                    ['30%', false],
                    ['80–90%', true],
                    ['50%', false],
                ],
            ],
            [
                'q' => 'Which of the following is NOT a common type of unstructured data?',
                'opts' => [
                    ['Images', false],
                    ['Audio recordings', false],
                    ['A SQL database with defined schemas', true],
                    ['Video files', false],
                ],
            ],
            [
                'q' => 'Why is unstructured data more difficult to analyse than structured data?',
                'opts' => [
                    ['It is always encrypted', false],
                    ['It does not fit neatly into rows and columns, making it harder to query and process', true],
                    ['It is always too small to be useful', false],
                    ['It can only be stored on paper', false],
                ],
            ],
            [
                'q' => 'What is a typical FIRST step in any unstructured data pipeline?',
                'opts' => [
                    ['Training a neural network', false],
                    ['Collecting and ingesting the raw unstructured data', true],
                    ['Visualising the final results', false],
                    ['Deploying the model to production', false],
                ],
            ],
            [
                'q' => 'Which field most commonly works with analysing unstructured text data?',
                'opts' => [
                    ['Network engineering', false],
                    ['Natural Language Processing (NLP)', true],
                    ['Hardware design', false],
                    ['Database administration', false],
                ],
            ],
            [
                'q' => 'Semi-structured data is best described as:',
                'opts' => [
                    ['Data with no organisation at all', false],
                    ['Data that has some organisational properties (like tags) but does not fit a strict schema — e.g. JSON, XML', true],
                    ['Data stored only in images', false],
                    ['Data that has been cleaned and structured', false],
                ],
            ],

            // ── 20.2 TEXT PREPROCESSING ───────────────────────────────────
            [
                'q' => 'What is "tokenisation" in text preprocessing?',
                'opts' => [
                    ['Encrypting text for security', false],
                    ['Splitting text into smaller units such as words or sentences', true],
                    ['Removing all vowels from text', false],
                    ['Converting text to numbers only', false],
                ],
            ],
            [
                'q' => 'What are "stop words" in NLP?',
                'opts' => [
                    ['Words that cause a program to stop running', false],
                    ['Very common words (like "the", "is", "and") that are often removed because they carry little meaning', true],
                    ['Words longer than 10 characters', false],
                    ['Words that appear only once in a document', false],
                ],
            ],
            [
                'q' => 'What does "lowercasing" do in text preprocessing?',
                'opts' => [
                    ['Removes all numbers from text', false],
                    ['Converts all characters to lower case so "Hello" and "hello" are treated the same', true],
                    ['Removes punctuation from text', false],
                    ['Splits sentences into words', false],
                ],
            ],
            [
                'q' => 'What is stemming in NLP?',
                'opts' => [
                    ['Adding prefixes to words', false],
                    ['Reducing a word to its root form — e.g. "running" → "run"', true],
                    ['Counting the number of words in a sentence', false],
                    ['Translating words to another language', false],
                ],
            ],
            [
                'q' => 'What is lemmatisation?',
                'opts' => [
                    ['A faster but cruder version of stemming', false],
                    ['Reducing a word to its dictionary base form (lemma), considering context — e.g. "better" → "good"', true],
                    ['Removing stop words', false],
                    ['Splitting text into paragraphs', false],
                ],
            ],
            [
                'q' => 'Which of the following would be removed during punctuation removal?',
                'opts' => [
                    ['"hello"', false],
                    ['123', false],
                    ['! , . ? ; :', true],
                    ['all vowels', false],
                ],
            ],
            [
                'q' => 'After tokenising the sentence "The cat sat on the mat", how many tokens are there?',
                'opts' => [
                    ['4', false],
                    ['5', false],
                    ['6', true],
                    ['7', false],
                ],
            ],
            [
                'q' => 'What does "normalisation" mean in the context of text preprocessing?',
                'opts' => [
                    ['Making all text the same font size', false],
                    ['Transforming text into a consistent, standard form — e.g. expanding contractions, fixing spelling', true],
                    ['Sorting words alphabetically', false],
                    ['Converting text to binary', false],
                ],
            ],

            // ── 20.3 BAG-OF-WORDS & TF-IDF ───────────────────────────────
            [
                'q' => 'The Bag-of-Words (BoW) model represents a document as:',
                'opts' => [
                    ['A sequence of words in order', false],
                    ['A count of how many times each word appears, ignoring word order', true],
                    ['A list of sentences', false],
                    ['A set of unique words only', false],
                ],
            ],
            [
                'q' => 'What does TF stand for in TF-IDF?',
                'opts' => [
                    ['Text Frequency', false],
                    ['Term Frequency', true],
                    ['Token Function', false],
                    ['Total Features', false],
                ],
            ],
            [
                'q' => 'What does IDF stand for in TF-IDF?',
                'opts' => [
                    ['Inverse Document Frequency', true],
                    ['Internal Data Filter', false],
                    ['Index of Document Features', false],
                    ['Integrated Data Format', false],
                ],
            ],
            [
                'q' => 'A word that appears in EVERY document in a corpus will have an IDF value of:',
                'opts' => [
                    ['A very high value', false],
                    ['1', false],
                    ['0 (or very close to 0)', true],
                    ['Equal to its term frequency', false],
                ],
            ],
            [
                'q' => 'The main weakness of the Bag-of-Words model is:',
                'opts' => [
                    ['It cannot handle long documents', false],
                    ['It ignores word order and context', true],
                    ['It always produces negative values', false],
                    ['It requires labelled training data', false],
                ],
            ],

            // ── 20.4 WORD EMBEDDINGS ──────────────────────────────────────
            [
                'q' => 'What is a word embedding?',
                'opts' => [
                    ['A way to hide secret messages in text', false],
                    ['A dense numerical vector representation of a word that captures its meaning', true],
                    ['A list of all synonyms for a word', false],
                    ['A method to compress text into fewer characters', false],
                ],
            ],
            [
                'q' => 'Word2Vec is a technique that trains word embeddings by:',
                'opts' => [
                    ['Counting word frequencies in a document', false],
                    ['Predicting a word from its surrounding context (or vice versa) in large text corpora', true],
                    ['Translating words between languages', false],
                    ['Removing stop words from text', false],
                ],
            ],
            [
                'q' => 'In a well-trained word embedding model, words with similar meanings will have:',
                'opts' => [
                    ['Vectors that are far apart', false],
                    ['Identical vectors', false],
                    ['Vectors that are close together (high similarity)', true],
                    ['Vectors that sum to zero', false],
                ],
            ],
            [
                'q' => 'GloVe word embeddings are trained using:',
                'opts' => [
                    ['Only the local context window (like Word2Vec)', false],
                    ['Global word co-occurrence statistics from the entire corpus', true],
                    ['Sentence-level labels', false],
                    ['Image pixel values', false],
                ],
            ],
            [
                'q' => 'FastText differs from Word2Vec because it:',
                'opts' => [
                    ['Uses sentences as the basic unit instead of words', false],
                    ['Represents words as the sum of their character n-grams, handling rare and misspelt words better', true],
                    ['Requires much larger training datasets', false],
                    ['Only works on English text', false],
                ],
            ],

            // ── 20.5 TOPIC MODELING ───────────────────────────────────────
            [
                'q' => 'What is topic modeling?',
                'opts' => [
                    ['Training a classifier to label emails as spam or not spam', false],
                    ['An unsupervised technique to discover hidden themes or topics in a collection of documents', true],
                    ['Counting the most frequent words in a document', false],
                    ['Translating documents into a different language', false],
                ],
            ],
            [
                'q' => 'LDA stands for:',
                'opts' => [
                    ['Linear Discriminant Analysis', false],
                    ['Latent Dirichlet Allocation', true],
                    ['Layered Data Abstraction', false],
                    ['Language Detection Algorithm', false],
                ],
            ],
            [
                'q' => 'In LDA topic modeling, each document is modelled as:',
                'opts' => [
                    ['A single topic', false],
                    ['A mixture of topics', true],
                    ['A list of sentences', false],
                    ['A single word', false],
                ],
            ],
            [
                'q' => 'NMF stands for:',
                'opts' => [
                    ['Normalised Matrix Factorisation', false],
                    ['Non-negative Matrix Factorization', true],
                    ['Natural Model Framework', false],
                    ['Numeric Matrix Features', false],
                ],
            ],

            // ── 20.6 SENTIMENT ANALYSIS ───────────────────────────────────
            [
                'q' => 'Sentiment analysis is used to:',
                'opts' => [
                    ['Identify the topic of a document', false],
                    ['Determine the emotional tone (positive, negative, neutral) expressed in text', true],
                    ['Count the number of sentences in a document', false],
                    ['Translate text between languages', false],
                ],
            ],
            [
                'q' => 'Which of the following sentences would most likely be classified as NEGATIVE sentiment?',
                'opts' => [
                    ['"The product arrived on time and works perfectly."', false],
                    ['"I love this app, it is so easy to use!"', false],
                    ['"This is the worst experience I have ever had."', true],
                    ['"The item was delivered yesterday."', false],
                ],
            ],
            [
                'q' => 'A lexicon-based sentiment approach works by:',
                'opts' => [
                    ['Training a neural network on labelled reviews', false],
                    ['Using a pre-built dictionary of words tagged as positive or negative to score text', true],
                    ['Counting the length of sentences', false],
                    ['Identifying named entities in text', false],
                ],
            ],
            [
                'q' => 'Which Python library provides a simple pre-built sentiment analyser called VADER?',
                'opts' => [
                    ['scikit-learn', false],
                    ['NLTK', true],
                    ['pandas', false],
                    ['matplotlib', false],
                ],
            ],

            // ── 20.7 NAMED ENTITY RECOGNITION ────────────────────────────
            [
                'q' => 'Named Entity Recognition (NER) is a task that:',
                'opts' => [
                    ['Generates new sentences from existing text', false],
                    ['Identifies and classifies proper nouns (people, places, organisations, dates) in text', true],
                    ['Counts the frequency of all nouns', false],
                    ['Translates text to a structured table', false],
                ],
            ],
            [
                'q' => 'In the sentence "Apple was founded in Cupertino by Steve Jobs", a NER model would identify:',
                'opts' => [
                    ['apple, cupertino, steve, jobs as verbs', false],
                    ['"Apple" as an ORG, "Cupertino" as a GPE (location), "Steve Jobs" as a PERSON', true],
                    ['Only "Apple" as a food item', false],
                    ['No entities — this sentence has none', false],
                ],
            ],
            [
                'q' => 'Which popular Python NLP library provides a fast, production-ready NER pipeline?',
                'opts' => [
                    ['NumPy', false],
                    ['spaCy', true],
                    ['SciPy', false],
                    ['Seaborn', false],
                ],
            ],

            // ── 20.8 IMAGE DATA ───────────────────────────────────────────
            [
                'q' => 'In a digital image, a pixel stores:',
                'opts' => [
                    ['A word from a caption', false],
                    ['A colour value — typically a combination of Red, Green, and Blue (RGB) intensities', true],
                    ['A sound frequency', false],
                    ['A timestamp', false],
                ],
            ],
            [
                'q' => 'A greyscale image with 28×28 pixels has how many total pixel values?',
                'opts' => [
                    ['56', false],
                    ['784', true],
                    ['2,352', false],
                    ['28', false],
                ],
            ],
            [
                'q' => 'An RGB image with dimensions 64×64 pixels has a total array shape of:',
                'opts' => [
                    ['(64, 64)', false],
                    ['(64, 64, 1)', false],
                    ['(64, 64, 3)', true],
                    ['(3, 64)', false],
                ],
            ],
            [
                'q' => 'What is image normalisation in preprocessing?',
                'opts' => [
                    ['Resizing the image to a square shape', false],
                    ['Scaling pixel values (e.g. from 0–255 to 0–1) to make training more stable', true],
                    ['Converting the image to greyscale', false],
                    ['Rotating the image by 90 degrees', false],
                ],
            ],
            [
                'q' => 'What is data augmentation in image processing?',
                'opts' => [
                    ['Adding more labels to a dataset', false],
                    ['Creating new training images by applying transformations (flipping, rotation, cropping) to existing ones', true],
                    ['Removing blurry images from a dataset', false],
                    ['Converting colour images to greyscale', false],
                ],
            ],

            // ── 20.9 AUDIO DATA ───────────────────────────────────────────
            [
                'q' => 'What is a waveform in audio data?',
                'opts' => [
                    ['A visual bar chart of word frequencies', false],
                    ['A representation of a sound signal showing amplitude over time', true],
                    ['A type of image compression', false],
                    ['A list of spoken words', false],
                ],
            ],
            [
                'q' => 'What is a spectrogram?',
                'opts' => [
                    ['A chart showing how many times each word appears', false],
                    ['A visual representation of the frequency content of a sound signal over time', true],
                    ['A type of neural network layer', false],
                    ['A tool for removing noise from images', false],
                ],
            ],
            [
                'q' => 'The sample rate of an audio file refers to:',
                'opts' => [
                    ['The volume of the recording', false],
                    ['The number of audio samples captured per second (e.g. 44,100 Hz = 44,100 samples/sec)', true],
                    ['The file size of the audio', false],
                    ['The number of speakers in the recording', false],
                ],
            ],
            [
                'q' => 'MFCC stands for:',
                'opts' => [
                    ['Mean Frequency Coefficient Compression', false],
                    ['Mel-Frequency Cepstral Coefficients', true],
                    ['Maximum Feature Count Classifier', false],
                    ['Multi-Function Convolutional Computation', false],
                ],
            ],

            // ── 20.10 TRANSFORMERS & LLMs ────────────────────────────────
            [
                'q' => 'What is a transformer in the context of NLP?',
                'opts' => [
                    ['An electrical device that converts voltage', false],
                    ['A deep learning architecture that uses attention mechanisms to process sequences in parallel', true],
                    ['A rule-based grammar checker', false],
                    ['A tool for converting audio to text only', false],
                ],
            ],
            [
                'q' => 'BERT is a transformer model that stands for:',
                'opts' => [
                    ['Binary Encoder Recurrent Transformer', false],
                    ['Bidirectional Encoder Representations from Transformers', true],
                    ['Baseline Embedding Retrieval Technique', false],
                    ['Batch Encoder for Recurrent Tasks', false],
                ],
            ],
            [
                'q' => 'What is the key innovation of the attention mechanism in transformers?',
                'opts' => [
                    ['It processes text one character at a time', false],
                    ['It allows the model to focus on relevant parts of the input when producing each output, regardless of distance', true],
                    ['It stores every word in a database', false],
                    ['It converts text to images for processing', false],
                ],
            ],
            [
                'q' => 'A Large Language Model (LLM) is pre-trained on:',
                'opts' => [
                    ['A small curated dataset of 1,000 sentences', false],
                    ['A massive corpus of text from the internet and books, learning general language patterns', true],
                    ['Only labelled classification data', false],
                    ['Audio recordings of human speech', false],
                ],
            ],
            [
                'q' => 'Fine-tuning a pre-trained LLM means:',
                'opts' => [
                    ['Training the model from scratch on a new dataset', false],
                    ['Further training the model on a smaller, task-specific dataset to adapt it for a particular use case', true],
                    ['Deleting the original model weights', false],
                    ['Making the model smaller by removing layers', false],
                ],
            ],
            [
                'q' => 'Which of the following is a well-known example of a Large Language Model?',
                'opts' => [
                    ['Random Forest', false],
                    ['K-Means Clustering', false],
                    ['GPT (Generative Pre-trained Transformer)', true],
                    ['Linear Regression', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 20 — Analysis of Unstructured Data (Newbie).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Newbie");
    }
}