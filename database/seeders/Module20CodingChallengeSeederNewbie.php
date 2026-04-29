<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 20 — Unstructured Data & NLP Fundamentals (Newbie) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering all unstructured-data topics
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered:
 *   - What Is Unstructured Data?
 *   - Text Preprocessing: Cleaning, Tokenization & Normalization
 *   - Bag-of-Words, TF-IDF & Vector Space Models
 *   - Word Embeddings: Word2Vec, GloVe & FastText
 *   - Topic Modeling: LDA & NMF
 *   - Sentiment Analysis: Lexicon & ML Approaches
 *   - Named Entity Recognition & Information Extraction
 *   - Image Data: Representation, Preprocessing & Feature Extraction
 *   - Audio Data: Waveforms, Spectrograms & Feature Engineering
 *   - Transformers & Large Language Models for Unstructured Data
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module20CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 20 — Unstructured Data & NLP Fundamentals (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Unstructured Data & NLP Fundamentals',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Work with text, images, and audio data using Python. Tasks cover cleaning and tokenizing text, building bag-of-words vectors, computing TF-IDF, performing basic sentiment analysis, extracting named entities, working with pixel arrays, computing spectrograms, and using pre-trained transformer models. Each task is short and self-contained.',
                'time_limit_seconds' => 900,
                'base_xp'            => 500,
                'order_index'        => 20,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: What Is Unstructured Data? (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Given a list of data items, classify each as `"structured"` or `"unstructured"` and print the results one per line.

Use these rules:
- Items that are a plain sentence or paragraph → `unstructured`
- Items that are a number, date, or key-value pair → `structured`

Input (one item per line, terminated by `END`):
```
age: 25
This is a review of the product.
2024-01-15
The sky is blue and the sun shines.
END
```

Expected output:
```
structured
unstructured
structured
unstructured
```
MD,
                'starter_code'        => "import sys\n\nfor line in sys.stdin:\n    line = line.strip()\n    if line == 'END':\n        break\n    # Classify and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read a sentence from input and count how many words it contains. Print the word count.

Example:
```
Input:  Hello world this is Python
Output: 5
```
MD,
                'starter_code'        => "sentence = input()\n# Count and print the number of words\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read a string from input and print whether it is `"empty"` (only whitespace or zero length) or `"has content"`.

Example:
```
Input:    (three spaces)
Output: empty

Input:  Hello
Output: has content
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read an integer `n` followed by `n` lines. Count how many lines contain at least one digit and print that count.

Example:
```
Input:
4
Hello world
I have 3 cats
No digits here
2024 is the year
Output: 2
```
MD,
                'starter_code'        => "n = int(input())\ncount = 0\nfor _ in range(n):\n    line = input()\n    # Check if any character is a digit\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read a sentence from input and print the unique characters it contains (excluding spaces), sorted alphabetically, joined with no separator.

Example:
```
Input:  banana split
Output: abeilnpst
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Text Preprocessing (Q6–Q12)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Read a sentence from input and print it converted to lowercase with all leading/trailing whitespace removed.

Example:
```
Input:   Hello World!  
Output: hello world!
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Read a sentence and print its tokens (words) one per line after splitting on whitespace.

Example:
```
Input:  NLP is fun
Output:
NLP
is
fun
```
MD,
                'starter_code'        => "sentence = input()\n# Split and print each token\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Read a sentence from input. Remove all punctuation characters (`,`, `.`, `!`, `?`, `;`, `:`, `'`, `"`) and print the cleaned sentence.

Example:
```
Input:  Hello, world! How's it going?
Output: Hello world Hows it going
```
MD,
                'starter_code'        => "import string\nsentence = input()\n# Remove punctuation and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read a sentence and a list of stop words (comma-separated on the second line). Remove the stop words from the sentence (case-insensitive) and print the remaining words separated by spaces.

Example:
```
Input:
the cat sat on the mat
the,on,a
Output: cat sat mat
```
MD,
                'starter_code'        => "sentence = input().lower().split()\nstop_words = set(input().split(','))\n# Filter and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Simple stemming: read a word from input and strip a trailing `"ing"` or `"ed"` suffix (if present) and print the result. If neither suffix is present, print the word unchanged.

Example:
```
Input:  running
Output: runn

Input:  jumped
Output: jump

Input:  happy
Output: happy
```
MD,
                'starter_code'        => "word = input()\n# Strip suffix and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Read a sentence from input and print the number of unique words (case-insensitive, ignore punctuation `. , ! ?`).

Example:
```
Input:  To be or not to be, that is the question!
Output: 8
```
MD,
                'starter_code'        => "import re\nsentence = input().lower()\n# Count unique words\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Read an integer `n` followed by `n` sentences. Normalize each sentence by lowercasing and stripping leading/trailing spaces, then print each normalized sentence on its own line.

Example:
```
Input:
3
  Hello World  
NLP IS GREAT
  Python rocks! 
Output:
hello world
nlp is great
python rocks!
```
MD,
                'starter_code'        => "n = int(input())\nfor _ in range(n):\n    line = input()\n    # Normalize and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Bag-of-Words, TF-IDF & Vector Space Models (Q13–Q18)
            // ═══════════════════════════════════════════════════════════════

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Build a Bag-of-Words count vector for a sentence. Read a sentence from input, split into words (lowercase), and print each unique word and its count, sorted alphabetically, one per line in the format `word:count`.

Example:
```
Input:  the cat sat on the mat the cat
Output:
cat:2
mat:1
on:1
sat:1
the:3
```
MD,
                'starter_code'        => "sentence = input().lower().split()\n# Build BoW and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Read two sentences (one per line). Print the vocabulary (sorted alphabetically, one word per line) that is the union of all unique words from both sentences (lowercase).

Example:
```
Input:
I love cats
I love dogs
Output:
cats
dogs
i
love
```
MD,
                'starter_code'        => "s1 = input().lower().split()\ns2 = input().lower().split()\n# Build union vocab and print sorted\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Read a sentence and a query word from input (one per line). Print the term frequency (TF) of the query word in the sentence, rounded to 4 decimal places.

TF = (count of query word) / (total words in sentence)

Example:
```
Input:
the cat sat on the mat the cat
the
Output: 0.3750
```
MD,
                'starter_code'        => "words = input().lower().split()\nquery = input().lower()\n# Calculate and print TF\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Read an integer `n`, then `n` documents (one per line). For each document, print the number of words it contains (simple whitespace split), one per line.

Example:
```
Input:
3
hello world
NLP is really fun and useful
Python
Output:
2
6
1
```
MD,
                'starter_code'        => "n = int(input())\nfor _ in range(n):\n    doc = input()\n    # Print word count\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Compute cosine similarity between two binary BoW vectors. Read two sentences (one per line). Build a shared vocabulary (sorted). Represent each sentence as a binary vector (1 if word present, 0 if not). Compute cosine similarity and print rounded to 4 decimal places.

Cosine similarity = dot(A, B) / (|A| * |B|)

Example:
```
Input:
I love cats
I love dogs
Output: 0.6667
```
MD,
                'starter_code'        => "import math\ns1 = set(input().lower().split())\ns2 = set(input().lower().split())\n# Compute cosine similarity\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Read an integer `n` followed by `n` documents. Find and print the document index (0-based) that has the most unique words. If there is a tie, print the smallest index.

Example:
```
Input:
3
the cat sat
the quick brown fox jumps
dog runs
Output: 1
```
MD,
                'starter_code'        => "n = int(input())\ndocs = [input() for _ in range(n)]\n# Find and print index with most unique words\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Word Embeddings: Word2Vec, GloVe & FastText (Q19–Q23)
            // ═══════════════════════════════════════════════════════════════

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Given two word vectors as space-separated floats (one vector per line), compute and print the dot product rounded to 4 decimal places.

Example:
```
Input:
0.1 0.3 0.5
0.2 0.4 0.6
Output: 0.4400
```
MD,
                'starter_code'        => "v1 = list(map(float, input().split()))\nv2 = list(map(float, input().split()))\n# Compute dot product\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Read a vector of floats (space-separated on one line) and print its L2 norm (Euclidean magnitude) rounded to 4 decimal places.

Example:
```
Input:  3.0 4.0
Output: 5.0000
```
MD,
                'starter_code'        => "import math\nv = list(map(float, input().split()))\n# Compute L2 norm and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Compute cosine similarity between two word vectors. Read two vectors (one per line, space-separated floats). Print cosine similarity rounded to 4 decimal places.

Cosine similarity = dot(A,B) / (|A| * |B|)

Example:
```
Input:
1.0 0.0
0.0 1.0
Output: 0.0000
```
MD,
                'starter_code'        => "import math\nv1 = list(map(float, input().split()))\nv2 = list(map(float, input().split()))\n# Compute cosine similarity\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Read an integer `n` then `n` vectors (one per line, space-separated floats). Print the index (0-based) of the vector with the highest L2 norm. If tied, print the smallest index.

Example:
```
Input:
3
1.0 0.0
3.0 4.0
1.0 1.0
Output: 1
```
MD,
                'starter_code'        => "import math\nn = int(input())\nvectors = [list(map(float, input().split())) for _ in range(n)]\n# Find and print index of max norm\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Analogy arithmetic: given three vectors A, B, C (space-separated floats, one per line), compute D = B - A + C element-wise and print each element of D rounded to 4 decimal places, space-separated.

Example:
```
Input:
1.0 2.0
3.0 4.0
0.5 0.5
Output: 2.5000 2.5000
```
MD,
                'starter_code'        => "A = list(map(float, input().split()))\nB = list(map(float, input().split()))\nC = list(map(float, input().split()))\n# Compute D = B - A + C\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Topic Modeling: LDA & NMF (Q24–Q28)
            // ═══════════════════════════════════════════════════════════════

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Given a topic-word distribution as a list of `word:probability` pairs (space-separated on one line), print the top-2 words by probability in descending order, one per line.

Example:
```
Input:  cat:0.4 dog:0.1 fish:0.35 bird:0.15
Output:
cat
fish
```
MD,
                'starter_code'        => "pairs = input().split()\n# Parse, sort by probability desc, print top 2 words\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Read an integer `n` then `n` documents (one per line). Print the document index (0-based) that contains the most distinct words. If tied, print the smallest index.

Example:
```
Input:
3
apple banana apple
dog cat fish bird
hello world
Output: 1
```
MD,
                'starter_code'        => "n = int(input())\ndocs = [input() for _ in range(n)]\n# Print index with most distinct words\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Read two topic distributions as comma-separated floats (one per line). Compute and print the KL divergence of P from Q (KL(P||Q)), rounded to 4 decimal places.

KL(P||Q) = sum(P[i] * log(P[i] / Q[i])) for all i where P[i] > 0

Example:
```
Input:
0.4,0.6
0.5,0.5
Output: 0.0201
```
MD,
                'starter_code'        => "import math\nP = list(map(float, input().split(',')))\nQ = list(map(float, input().split(',')))\n# Compute KL divergence\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Read a document-topic matrix: first line is `n_docs k_topics`, then `n_docs` lines each with `k_topics` space-separated probabilities. Print the assigned topic (0-based index of max probability) for each document, one per line.

Example:
```
Input:
3 2
0.8 0.2
0.3 0.7
0.5 0.5
Output:
0
1
0
```
MD,
                'starter_code'        => "n, k = map(int, input().split())\nfor _ in range(n):\n    probs = list(map(float, input().split()))\n    # Print index of max probability\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Read an integer `n` followed by `n` topic labels (one per line). Print the most frequent topic label. If tied, print the one that appears first alphabetically.

Example:
```
Input:
5
sports
politics
sports
tech
politics
Output: sports
```
MD,
                'starter_code'        => "n = int(input())\nlabels = [input() for _ in range(n)]\n# Print most frequent label\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Sentiment Analysis (Q29–Q33)
            // ═══════════════════════════════════════════════════════════════

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Lexicon-based sentiment: read a sentence and count positive vs negative words from these fixed sets:

- Positive: `good`, `great`, `excellent`, `happy`, `love`
- Negative: `bad`, `terrible`, `awful`, `sad`, `hate`

Print `Positive` if positive count > negative count, `Negative` if negative > positive, else `Neutral`.

Example:
```
Input:  I love this great product but it is bad
Output: Positive
```
MD,
                'starter_code'        => "pos = {'good','great','excellent','happy','love'}\nneg = {'bad','terrible','awful','sad','hate'}\nwords = input().lower().split()\n# Count and classify\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Read an integer `n` then `n` sentences. For each sentence, print `Positive`, `Negative`, or `Neutral` using the same lexicon:

- Positive words: `good`, `great`, `excellent`, `happy`, `love`
- Negative words: `bad`, `terrible`, `awful`, `sad`, `hate`

Example:
```
Input:
3
I love this
This is terrible and awful
The weather is okay
Output:
Positive
Negative
Neutral
```
MD,
                'starter_code'        => "pos = {'good','great','excellent','happy','love'}\nneg = {'bad','terrible','awful','sad','hate'}\nn = int(input())\nfor _ in range(n):\n    words = input().lower().split()\n    # Classify and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Read a sentence and a sentiment label (`positive` or `negative`) from input (one per line). Print `correct` if the sentence contains at least one matching sentiment word (using the same lexicon), otherwise print `incorrect`.

- Positive words: `good`, `great`, `excellent`, `happy`, `love`
- Negative words: `bad`, `terrible`, `awful`, `sad`, `hate`

Example:
```
Input:
I feel happy today
positive
Output: correct
```
MD,
                'starter_code'        => "pos = {'good','great','excellent','happy','love'}\nneg = {'bad','terrible','awful','sad','hate'}\nsentence = input().lower().split()\nlabel = input().lower()\n# Evaluate and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Read a sentence and print the sentiment score: +1 for each positive word, -1 for each negative word, 0 for others. Print the total score.

- Positive words: `good`, `great`, `excellent`, `happy`, `love`
- Negative words: `bad`, `terrible`, `awful`, `sad`, `hate`

Example:
```
Input:  I love great food but it is terrible and sad
Output: 1
```
MD,
                'starter_code'        => "pos = {'good','great','excellent','happy','love'}\nneg = {'bad','terrible','awful','sad','hate'}\nwords = input().lower().split()\n# Compute and print score\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Read an integer `n` then `n` sentences. Print the percentage of `Positive` sentences (rounded to 2 decimal places) among all sentences, using the same lexicon rules.

- Positive words: `good`, `great`, `excellent`, `happy`, `love`
- Negative words: `bad`, `terrible`, `awful`, `sad`, `hate`

Example:
```
Input:
4
I love this
This is bad
Great day
Terrible news
Output: 50.00
```
MD,
                'starter_code'        => "pos_words = {'good','great','excellent','happy','love'}\nneg_words = {'bad','terrible','awful','sad','hate'}\nn = int(input())\nresults = []\nfor _ in range(n):\n    words = input().lower().split()\n    # Classify each sentence\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Named Entity Recognition & Information Extraction (Q34–Q38)
            // ═══════════════════════════════════════════════════════════════

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Simple rule-based NER: read a sentence from input. Print all words that start with an uppercase letter (that are not the first word of the sentence) as potential named entities, one per line. If none found, print `NONE`.

Example:
```
Input:  Alice went to Paris with Bob last Monday
Output:
Paris
Bob
Monday
```
MD,
                'starter_code'        => "words = input().split()\n# Print capitalized words (skip index 0)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Extract all 4-digit numbers from a sentence (potential years). Read a sentence from input and print each 4-digit number found, one per line. If none found, print `NONE`.

Example:
```
Input:  The war started in 1939 and ended in 1945
Output:
1939
1945
```
MD,
                'starter_code'        => "import re\nsentence = input()\n# Find and print 4-digit numbers\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Extract email addresses from a line of text. Read a line from input and print all email addresses found (pattern: `word@word.word`), one per line. If none found, print `NONE`.

Example:
```
Input:  Contact us at hello@example.com or support@test.org
Output:
hello@example.com
support@test.org
```
MD,
                'starter_code'        => "import re\ntext = input()\n# Extract and print emails\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Read a sentence from input. Count how many words are in ALL CAPS (all uppercase, at least 2 characters). Print that count.

Example:
```
Input:  The NASA and FBI are US agencies
Output: 3
```
MD,
                'starter_code'        => "words = input().split()\n# Count and print all-caps words (length >= 2)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Read an integer `n` then `n` sentences. Print the sentence that contains the most capitalized words (words starting with uppercase, not counting the first word). If tied, print the one with the smaller index. Print the full sentence.

Example:
```
Input:
3
Alice met Bob
John went to London to meet Maria
The weather is nice
Output: John went to London to meet Maria
```
MD,
                'starter_code'        => "n = int(input())\nsentences = [input() for _ in range(n)]\n# Find and print sentence with most capitalized words (skip word[0])\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Image Data: Representation, Preprocessing & Features (Q39–Q43)
            // ═══════════════════════════════════════════════════════════════

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Read a grayscale pixel grid: first line is `rows cols`, then `rows` lines each with `cols` space-separated integers (0–255). Print the average pixel value rounded to 2 decimal places.

Example:
```
Input:
2 3
10 20 30
40 50 60
Output: 35.00
```
MD,
                'starter_code'        => "rows, cols = map(int, input().split())\npixels = []\nfor _ in range(rows):\n    pixels.extend(map(int, input().split()))\n# Compute and print average\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Normalize a grayscale pixel grid to the range [0, 1]. Read `rows cols` then the pixel grid (integers 0–255). Print the normalized grid row by row, values rounded to 4 decimal places and space-separated.

Example:
```
Input:
2 2
0 255
128 64
Output:
0.0000 1.0000
0.5020 0.2510
```
MD,
                'starter_code'        => "rows, cols = map(int, input().split())\nfor _ in range(rows):\n    row = list(map(int, input().split()))\n    # Normalize and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Convert an RGB pixel to grayscale using the formula:
`gray = 0.299*R + 0.587*G + 0.114*B`

Read three integers R, G, B (space-separated) and print the grayscale value as an integer (truncate, do not round).

Example:
```
Input:  100 150 200
Output: 140
```
MD,
                'starter_code'        => "r, g, b = map(int, input().split())\n# Compute and print grayscale value\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Read a grayscale pixel grid (`rows cols` then pixel values). Apply a threshold: pixels >= threshold → 255, else → 0. The threshold is given on the last line. Print the resulting binary grid.

Example:
```
Input:
2 3
10 120 200
50 130 80
128
Output:
0 0 255
0 255 0
```
MD,
                'starter_code'        => "rows, cols = map(int, input().split())\ngrid = []\nfor _ in range(rows):\n    grid.append(list(map(int, input().split())))\nthreshold = int(input())\n# Apply threshold and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Read a grayscale pixel grid (`rows cols` then pixel values). Print the minimum and maximum pixel values on one line, space-separated.

Example:
```
Input:
2 3
10 20 30
5 200 150
Output: 5 200
```
MD,
                'starter_code'        => "rows, cols = map(int, input().split())\npixels = []\nfor _ in range(rows):\n    pixels.extend(map(int, input().split()))\n# Print min and max\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Audio Data: Waveforms, Spectrograms & Feature Eng. (Q44–Q47)
            // ═══════════════════════════════════════════════════════════════

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Read a waveform as space-separated floats on one line. Compute and print the Root Mean Square (RMS) energy, rounded to 4 decimal places.

RMS = sqrt(mean(x^2))

Example:
```
Input:  1.0 -1.0 1.0 -1.0
Output: 1.0000
```
MD,
                'starter_code'        => "import math\nsamples = list(map(float, input().split()))\n# Compute and print RMS\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Read a waveform as space-separated floats. Print the number of zero crossings (times the signal changes from positive to negative or vice versa, ignoring zeros).

Example:
```
Input:  1.0 -1.0 1.0 -1.0 1.0
Output: 4
```
MD,
                'starter_code'        => "samples = list(map(float, input().split()))\n# Count and print zero crossings\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Read a waveform as space-separated floats. Normalize the waveform so its maximum absolute value is 1.0. Print each normalized value rounded to 4 decimal places, space-separated.

Example:
```
Input:  2.0 -4.0 1.0
Output: 0.5000 -1.0000 0.2500
```
MD,
                'starter_code'        => "samples = list(map(float, input().split()))\n# Normalize and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Read a sample rate (integer, samples per second) and a duration (float, in seconds) from input (one per line). Print the total number of samples as an integer.

Example:
```
Input:
44100
2.5
Output: 110250
```
MD,
                'starter_code'        => "sample_rate = int(input())\nduration = float(input())\n# Compute and print total samples\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Transformers & LLMs for Unstructured Data (Q48–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Simulate attention scores. Read an integer `n` then `n` floats (the raw attention logits, space-separated on one line). Apply softmax and print each attention weight rounded to 4 decimal places, space-separated.

Softmax: exp(x_i) / sum(exp(x_j))

Example:
```
Input:
3
1.0 2.0 3.0
Output: 0.0900 0.2447 0.6652
```
MD,
                'starter_code'        => "import math\nn = int(input())\nlogits = list(map(float, input().split()))\n# Apply softmax and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Token subword splitting: given a word and a list of known subwords (comma-separated), greedily split the word left-to-right into the longest matching subwords. Print the resulting tokens separated by spaces. If a character cannot be matched, print it as-is.

Example:
```
Input:
unhappiness
un,happy,happiness,ness,un,hap,pi
Output: un happiness
```
MD,
                'starter_code'        => "word = input()\nsubwords = input().split(',')\n# Greedy longest-match tokenization\ni = 0\ntokens = []\nwhile i < len(word):\n    matched = False\n    for length in range(len(word) - i, 0, -1):\n        chunk = word[i:i+length]\n        if chunk in subwords:\n            tokens.append(chunk)\n            i += length\n            matched = True\n            break\n    if not matched:\n        tokens.append(word[i])\n        i += 1\nprint(' '.join(tokens))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Read an integer `n` then `n` lines each containing a text classification label (`positive`, `negative`, or `neutral`). Print a summary in the format:
```
positive: X
negative: Y
neutral: Z
```
where X, Y, Z are the counts of each label.

Example:
```
Input:
5
positive
negative
positive
neutral
positive
Output:
positive: 3
negative: 1
neutral: 1
```
MD,
                'starter_code'        => "n = int(input())\ncounts = {'positive': 0, 'negative': 0, 'neutral': 0}\nfor _ in range(n):\n    label = input().strip()\n    if label in counts:\n        counts[label] += 1\n# Print summary\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

        ];

        // ─────────────────────────────────────────────────────────────────
        // Persist questions
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
        // 3. TEST CASES (4 per question)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $seed = function (int $qIndex, array $cases) use ($questions): void {
            $questionId = $questions[$qIndex];
            foreach ($cases as $case) {
                $exists = DB::table('test_cases')->where([
                    'coding_question_id' => $questionId,
                    'order_index'        => $case['order_index'],
                ])->exists();

                if (! $exists) {
                    DB::table('test_cases')->insert([
                        'coding_question_id' => $questionId,
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

        // ── Q1: Classify structured vs unstructured ────────────────────
        $seed(1, [
            ['input' => "age: 25\nThis is a review of the product.\n2024-01-15\nThe sky is blue and the sun shines.\nEND", 'expected_output' => "structured\nunstructured\nstructured\nunstructured", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "name: John\nOnce upon a time in a land far away.\nEND", 'expected_output' => "structured\nunstructured", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2023-07-04\nThe quick brown fox jumps.\nscore: 99\nEND", 'expected_output' => "structured\nunstructured\nstructured", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "price: 9.99\nEND", 'expected_output' => "structured", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q2: Word count ─────────────────────────────────────────────
        $seed(2, [
            ['input' => 'Hello world this is Python', 'expected_output' => '5', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'one', 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'the quick brown fox jumps over the lazy dog', 'expected_output' => '9', 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'NLP is amazing and powerful', 'expected_output' => '5', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q3: Empty or has content ───────────────────────────────────
        $seed(3, [
            ['input' => '   ', 'expected_output' => 'empty', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'Hello', 'expected_output' => 'has content', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '', 'expected_output' => 'empty', 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'Data science', 'expected_output' => 'has content', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q4: Count lines with digits ────────────────────────────────
        $seed(4, [
            ['input' => "4\nHello world\nI have 3 cats\nNo digits here\n2024 is the year", 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nno digits\nstill none", 'expected_output' => '0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n100 items\n200 boxes\nnothing", 'expected_output' => '2', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\nonly1digit", 'expected_output' => '1', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q5: Unique chars sorted ────────────────────────────────────
        $seed(5, [
            ['input' => 'banana split', 'expected_output' => 'abeilnpst', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'hello', 'expected_output' => 'ehlo', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'python rocks', 'expected_output' => 'chknoprsty', 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'aabbcc', 'expected_output' => 'abc', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q6: Lowercase and strip ────────────────────────────────────
        $seed(6, [
            ['input' => '  Hello World!  ', 'expected_output' => 'hello world!', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'NLP IS FUN', 'expected_output' => 'nlp is fun', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '  Python  ', 'expected_output' => 'python', 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'DATA SCIENCE', 'expected_output' => 'data science', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q7: Tokenize ───────────────────────────────────────────────
        $seed(7, [
            ['input' => 'NLP is fun', 'expected_output' => "NLP\nis\nfun", 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'hello world', 'expected_output' => "hello\nworld", 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'the quick brown fox', 'expected_output' => "the\nquick\nbrown\nfox", 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'one', 'expected_output' => 'one', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q8: Remove punctuation ─────────────────────────────────────
        $seed(8, [
            ['input' => "Hello, world! How's it going?", 'expected_output' => 'Hello world Hows it going', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'No punctuation here', 'expected_output' => 'No punctuation here', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "Wait: really? Yes!", 'expected_output' => 'Wait really Yes', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "Good morning; how are you?", 'expected_output' => 'Good morning how are you', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q9: Remove stop words ──────────────────────────────────────
        $seed(9, [
            ['input' => "the cat sat on the mat\nthe,on,a", 'expected_output' => 'cat sat mat', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "I love NLP and Python\ni,and,the", 'expected_output' => 'love NLP Python', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "a rose is a rose\na,is", 'expected_output' => 'rose rose', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "hello world\nfoo,bar", 'expected_output' => 'hello world', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q10: Simple stemming ───────────────────────────────────────
        $seed(10, [
            ['input' => 'running', 'expected_output' => 'runn', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'jumped', 'expected_output' => 'jump', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'happy', 'expected_output' => 'happy', 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'played', 'expected_output' => 'play', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q11: Unique word count ─────────────────────────────────────
        $seed(11, [
            ['input' => 'To be or not to be, that is the question!', 'expected_output' => '8', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'hello hello hello', 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'I love NLP and I love Python!', 'expected_output' => '5', 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'one two three four five', 'expected_output' => '5', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q12: Normalize n sentences ────────────────────────────────
        $seed(12, [
            ['input' => "3\n  Hello World  \nNLP IS GREAT\n  Python rocks! ", 'expected_output' => "hello world\nnlp is great\npython rocks!", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n  UPPER CASE  \nlower case", 'expected_output' => "upper case\nlower case", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n  TRIM ME  ", 'expected_output' => 'trim me', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\nA\nBB\nCCC\nDDDD", 'expected_output' => "a\nbb\nccc\ndddd", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q13: Bag of Words count ────────────────────────────────────
        $seed(13, [
            ['input' => 'the cat sat on the mat the cat', 'expected_output' => "cat:2\nmat:1\non:1\nsat:1\nthe:3", 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'hello world hello', 'expected_output' => "hello:2\nworld:1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'a b c a b a', 'expected_output' => "a:3\nb:2\nc:1", 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'nlp is nlp', 'expected_output' => "is:1\nnlp:2", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q14: Union vocabulary ──────────────────────────────────────
        $seed(14, [
            ['input' => "I love cats\nI love dogs", 'expected_output' => "cats\ndogs\ni\nlove", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "hello world\nhello python", 'expected_output' => "hello\npython\nworld", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "apple banana\nbanana cherry", 'expected_output' => "apple\nbanana\ncherry", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "one two\nthree four", 'expected_output' => "four\none\nthree\ntwo", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q15: Term frequency ────────────────────────────────────────
        $seed(15, [
            ['input' => "the cat sat on the mat the cat\nthe", 'expected_output' => '0.3750', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "hello hello world\nhello", 'expected_output' => '0.6667', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "a b c d\na", 'expected_output' => '0.2500', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "nlp is fun\nis", 'expected_output' => '0.3333', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q16: Document word counts ──────────────────────────────────
        $seed(16, [
            ['input' => "3\nhello world\nNLP is really fun and useful\nPython", 'expected_output' => "2\n6\n1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\none two three\nfour", 'expected_output' => "3\n1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nthe quick brown fox jumps", 'expected_output' => '5', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\na\nbb cc\nddd eee fff\ngggg", 'expected_output' => "1\n2\n3\n1", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q17: Cosine similarity binary vectors ──────────────────────
        $seed(17, [
            ['input' => "I love cats\nI love dogs", 'expected_output' => '0.6667', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "hello world\nhello world", 'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "cat dog\nfish bird", 'expected_output' => '0.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "a b c\na b d", 'expected_output' => '0.6667', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q18: Document with most unique words ───────────────────────
        $seed(18, [
            ['input' => "3\nthe cat sat\nthe quick brown fox jumps\ndog runs", 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nhello hello hello\nhello world", 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\na b c d\na b\na b c d e", 'expected_output' => '2', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\none one one\none two two", 'expected_output' => '1', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q19: Dot product of two vectors ───────────────────────────
        $seed(19, [
            ['input' => "0.1 0.3 0.5\n0.2 0.4 0.6", 'expected_output' => '0.4400', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0 0.0\n0.0 1.0", 'expected_output' => '0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0 3.0\n4.0 5.0", 'expected_output' => '23.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0 2.0 3.0\n1.0 2.0 3.0", 'expected_output' => '14.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q20: L2 norm ───────────────────────────────────────────────
        $seed(20, [
            ['input' => '3.0 4.0', 'expected_output' => '5.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '1.0 0.0 0.0', 'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '2.0 2.0', 'expected_output' => '2.8284', 'is_hidden' => true, 'order_index' => 3],
            ['input' => '0.0 0.0 0.0', 'expected_output' => '0.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q21: Cosine similarity float vectors ───────────────────────
        $seed(21, [
            ['input' => "1.0 0.0\n0.0 1.0", 'expected_output' => '0.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0 1.0\n1.0 1.0", 'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0 4.0\n4.0 3.0", 'expected_output' => '0.9600', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0 0.0 0.0\n0.0 1.0 0.0", 'expected_output' => '0.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q22: Index of vector with max norm ─────────────────────────
        $seed(22, [
            ['input' => "3\n1.0 0.0\n3.0 4.0\n1.0 1.0", 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0 0.0\n1.0 0.0", 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5.0\n3.0\n4.0", 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n1.0 1.0\n1.0 1.0", 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q23: Analogy arithmetic D = B - A + C ─────────────────────
        $seed(23, [
            ['input' => "1.0 2.0\n3.0 4.0\n0.5 0.5", 'expected_output' => '2.5000 2.5000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0 0.0\n1.0 1.0\n0.0 0.0", 'expected_output' => '1.0000 1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0 3.0 1.0\n5.0 7.0 2.0\n1.0 1.0 1.0", 'expected_output' => '4.0000 5.0000 2.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0\n2.0\n3.0", 'expected_output' => '4.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q24: Top-2 topic words ─────────────────────────────────────
        $seed(24, [
            ['input' => 'cat:0.4 dog:0.1 fish:0.35 bird:0.15', 'expected_output' => "cat\nfish", 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'apple:0.5 banana:0.3 cherry:0.2', 'expected_output' => "apple\nbanana", 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'war:0.6 peace:0.25 love:0.15', 'expected_output' => "war\npeace", 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'x:0.1 y:0.5 z:0.4', 'expected_output' => "y\nz", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q25: Document with most distinct words ─────────────────────
        $seed(25, [
            ['input' => "3\napple banana apple\ndog cat fish bird\nhello world", 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\na b c\na a a", 'expected_output' => '0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\none\none two\none two three", 'expected_output' => '2', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\nalpha beta gamma\nalpha alpha alpha", 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q26: KL divergence ─────────────────────────────────────────
        $seed(26, [
            ['input' => "0.4,0.6\n0.5,0.5", 'expected_output' => '0.0201', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5,0.5\n0.5,0.5", 'expected_output' => '0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.9,0.1\n0.5,0.5", 'expected_output' => '0.5310', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.25,0.75\n0.5,0.5", 'expected_output' => '0.1438', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q27: Assign topic by max probability ───────────────────────
        $seed(27, [
            ['input' => "3 2\n0.8 0.2\n0.3 0.7\n0.5 0.5", 'expected_output' => "0\n1\n0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 3\n0.1 0.6 0.3\n0.7 0.2 0.1", 'expected_output' => "1\n0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2\n0.4 0.6", 'expected_output' => '1', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4 2\n0.9 0.1\n0.2 0.8\n0.6 0.4\n0.3 0.7", 'expected_output' => "0\n1\n0\n1", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q28: Most frequent topic label ─────────────────────────────
        $seed(28, [
            ['input' => "5\nsports\npolitics\nsports\ntech\npolitics", 'expected_output' => 'sports', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\ntech\ntech\npolitics", 'expected_output' => 'tech', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\na\nb\na\nb", 'expected_output' => 'a', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\nsports", 'expected_output' => 'sports', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q29: Lexicon sentiment single sentence ─────────────────────
        $seed(29, [
            ['input' => 'I love this great product but it is bad', 'expected_output' => 'Positive', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'This is terrible and awful', 'expected_output' => 'Negative', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'The weather is okay', 'expected_output' => 'Neutral', 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'I hate bad and terrible things', 'expected_output' => 'Negative', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q30: Lexicon sentiment n sentences ─────────────────────────
        $seed(30, [
            ['input' => "3\nI love this\nThis is terrible and awful\nThe weather is okay", 'expected_output' => "Positive\nNegative\nNeutral", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\ngreat excellent happy\nbad sad", 'expected_output' => "Positive\nNegative", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nhello world", 'expected_output' => 'Neutral', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\nlove love love\nhate hate\ngood bad\ngood good bad", 'expected_output' => "Positive\nNegative\nNeutral\nPositive", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q31: Sentiment label correct/incorrect ─────────────────────
        $seed(31, [
            ['input' => "I feel happy today\npositive", 'expected_output' => 'correct', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "This is terrible\nnegative", 'expected_output' => 'correct', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "The sky is blue\npositive", 'expected_output' => 'incorrect', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "I love it\nnegative", 'expected_output' => 'incorrect', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q32: Sentiment score ───────────────────────────────────────
        $seed(32, [
            ['input' => 'I love great food but it is terrible and sad', 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'good excellent happy', 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'bad terrible awful', 'expected_output' => '-3', 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'the weather today', 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q33: Percentage positive ───────────────────────────────────
        $seed(33, [
            ['input' => "4\nI love this\nThis is bad\nGreat day\nTerrible news", 'expected_output' => '50.00', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\ngood day\nbad day", 'expected_output' => '50.00', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nlove great happy\nhello world\ngood morning", 'expected_output' => '66.67', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\nbad news", 'expected_output' => '0.00', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q34: Capitalized named entities ────────────────────────────
        $seed(34, [
            ['input' => 'Alice went to Paris with Bob last Monday', 'expected_output' => "Paris\nBob\nMonday", 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'Hello world', 'expected_output' => 'NONE', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'John met Maria in London', 'expected_output' => "Maria\nLondon", 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'The president visited Washington and met Obama', 'expected_output' => "Washington\nObama", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q35: Extract years ─────────────────────────────────────────
        $seed(35, [
            ['input' => 'The war started in 1939 and ended in 1945', 'expected_output' => "1939\n1945", 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'No years here', 'expected_output' => 'NONE', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'Python was created in 1991 and version 3 in 2008', 'expected_output' => "1991\n2008", 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'Founded in 2024', 'expected_output' => '2024', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q36: Extract emails ────────────────────────────────────────
        $seed(36, [
            ['input' => 'Contact us at hello@example.com or support@test.org', 'expected_output' => "hello@example.com\nsupport@test.org", 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'No emails here', 'expected_output' => 'NONE', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'Send to admin@company.net', 'expected_output' => 'admin@company.net', 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'a@b.c and x@y.z', 'expected_output' => "a@b.c\nx@y.z", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q37: Count all-caps words ──────────────────────────────────
        $seed(37, [
            ['input' => 'The NASA and FBI are US agencies', 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'no caps here', 'expected_output' => '0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'I love NLP and ML techniques', 'expected_output' => '2', 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'UN UNESCO WHO are international organizations', 'expected_output' => '3', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q38: Sentence with most capitalized words ──────────────────
        $seed(38, [
            ['input' => "3\nAlice met Bob\nJohn went to London to meet Maria\nThe weather is nice", 'expected_output' => 'John went to London to meet Maria', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nhello world\nHi Alice", 'expected_output' => 'Hi Alice', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nMeet John Smith today\nHi there", 'expected_output' => 'Meet John Smith today', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\nhello there\ngreat day\nno caps either", 'expected_output' => 'hello there', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q39: Average pixel value ───────────────────────────────────
        $seed(39, [
            ['input' => "2 3\n10 20 30\n40 50 60", 'expected_output' => '35.00', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2\n100 200", 'expected_output' => '150.00', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n0 255\n255 0", 'expected_output' => '127.50', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 4\n100 100 100 100", 'expected_output' => '100.00', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q40: Normalize pixels to [0,1] ────────────────────────────
        $seed(40, [
            ['input' => "2 2\n0 255\n128 64", 'expected_output' => "0.0000 1.0000\n0.5020 0.2510", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2\n0 255", 'expected_output' => '0.0000 1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n255 255\n0 0", 'expected_output' => "1.0000 1.0000\n0.0000 0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 3\n128 64 192", 'expected_output' => '0.5020 0.2510 0.7529', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q41: RGB to grayscale ──────────────────────────────────────
        $seed(41, [
            ['input' => '100 150 200', 'expected_output' => '140', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '0 0 0', 'expected_output' => '0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '255 255 255', 'expected_output' => '255', 'is_hidden' => true, 'order_index' => 3],
            ['input' => '200 100 50', 'expected_output' => '134', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q42: Threshold binarization ────────────────────────────────
        $seed(42, [
            ['input' => "2 3\n10 120 200\n50 130 80\n128", 'expected_output' => "0 0 255\n0 255 0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 3\n0 128 255\n128", 'expected_output' => '0 255 255', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n50 100\n200 30\n100", 'expected_output' => "0 255\n255 0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 4\n10 20 30 40\n25", 'expected_output' => '0 0 255 255', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q43: Min and max pixel ─────────────────────────────────────
        $seed(43, [
            ['input' => "2 3\n10 20 30\n5 200 150", 'expected_output' => '5 200', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 3\n100 50 200", 'expected_output' => '50 200', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n0 255\n128 64", 'expected_output' => '0 255', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 1\n127", 'expected_output' => '127 127', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q44: RMS energy ────────────────────────────────────────────
        $seed(44, [
            ['input' => '1.0 -1.0 1.0 -1.0', 'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '3.0 4.0', 'expected_output' => '3.5355', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.0 0.0 0.0', 'expected_output' => '0.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => '1.0 2.0 3.0 4.0', 'expected_output' => '2.7386', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q45: Zero crossings ────────────────────────────────────────
        $seed(45, [
            ['input' => '1.0 -1.0 1.0 -1.0 1.0', 'expected_output' => '4', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '1.0 2.0 3.0', 'expected_output' => '0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '1.0 -1.0 -1.0 1.0', 'expected_output' => '2', 'is_hidden' => true, 'order_index' => 3],
            ['input' => '-1.0 1.0', 'expected_output' => '1', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q46: Waveform normalization ────────────────────────────────
        $seed(46, [
            ['input' => '2.0 -4.0 1.0', 'expected_output' => '0.5000 -1.0000 0.2500', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '3.0 0.0 -3.0', 'expected_output' => '1.0000 0.0000 -1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '5.0', 'expected_output' => '1.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => '1.0 2.0 4.0 -2.0', 'expected_output' => '0.2500 0.5000 1.0000 -0.5000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q47: Total samples ─────────────────────────────────────────
        $seed(47, [
            ['input' => "44100\n2.5", 'expected_output' => '110250', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "16000\n1.0", 'expected_output' => '16000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "22050\n4.0", 'expected_output' => '88200', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "8000\n0.5", 'expected_output' => '4000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q48: Softmax attention ─────────────────────────────────────
        $seed(48, [
            ['input' => "3\n1.0 2.0 3.0", 'expected_output' => '0.0900 0.2447 0.6652', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0 0.0", 'expected_output' => '0.5000 0.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1.0 1.0 1.0 1.0", 'expected_output' => '0.2500 0.2500 0.2500 0.2500', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n2.0 0.0", 'expected_output' => '0.8808 0.1192', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q49: Greedy subword tokenization ──────────────────────────
        $seed(49, [
            ['input' => "unhappiness\nun,happy,happiness,ness,un,hap,pi", 'expected_output' => 'un happiness', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "playing\nplay,ing,play,ed", 'expected_output' => 'play ing', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "abc\na,b,c,ab", 'expected_output' => 'ab c', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "hello\nhell,o,he,llo", 'expected_output' => 'hell o', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q50: Label summary ─────────────────────────────────────────
        $seed(50, [
            ['input' => "5\npositive\nnegative\npositive\nneutral\npositive", 'expected_output' => "positive: 3\nnegative: 1\nneutral: 1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nneutral\nneutral\nneutral", 'expected_output' => "positive: 0\nnegative: 0\nneutral: 3", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\npositive\npositive\nnegative\nnegative\nneutral\npositive", 'expected_output' => "positive: 3\nnegative: 2\nneutral: 1", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\nnegative", 'expected_output' => "positive: 0\nnegative: 1\nneutral: 0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        $this->command->info('✅ Module 20 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}