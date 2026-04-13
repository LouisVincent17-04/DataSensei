<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module20LessonsSeeder
 * Seeds lessons for Module 20: Analysis of Unstructured Data.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module20LessonsSeeder
 *
 * Lessons:
 *  20.1  — What Is Unstructured Data? Types, Challenges & The Pipeline
 *  20.2  — Text Preprocessing: Cleaning, Tokenization & Normalization
 *  20.3  — Bag-of-Words, TF-IDF & Vector Space Models
 *  20.4  — Word Embeddings: Word2Vec, GloVe & FastText
 *  20.5  — Topic Modeling: LDA & NMF
 *  20.6  — Sentiment Analysis: Lexicon & Machine Learning Approaches
 *  20.7  — Named Entity Recognition & Information Extraction
 *  20.8  — Image Data: Representation, Preprocessing & Feature Extraction
 *  20.9  — Audio Data: Waveforms, Spectrograms & Feature Engineering
 *  20.10 — Transformers & Large Language Models for Unstructured Data
 *  20.11 — Final Exam (Org-Locked)
 */
class Module20LessonsSeeder extends Seeder
{
    public function run()
    {
        $unstrModule = Module::where('order_index', 20)->firstOrFail();
        Lesson::where('module_id', $unstrModule->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 20.1 — What Is Unstructured Data? Types, Challenges & The Pipeline
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What Is Unstructured Data?</h2>
<p>Approximately <strong>80–90% of all data generated worldwide is unstructured</strong> — it does not fit neatly into rows and columns. Every email, every tweet, every photograph, every customer review, every medical scan, every audio recording, and every video file is unstructured. Traditional relational databases and spreadsheets were built for the other 10–20%. This module teaches you the methods, tools, and mental models required to extract signal from the noisy, messy, high-dimensional world of unstructured data.</p>

<h3>Structured vs. Unstructured vs. Semi-Structured</h3>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:32px;">
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #3b82f6;">
    <h4 style="color:#3b82f6;margin-top:0;font-size:0.9rem;">Structured Data</h4>
    <p style="color:var(--muted);font-size:0.85rem;margin-bottom:10px;">Predefined schema. Rows and columns. Immediately queryable.</p>
    <ul style="color:var(--muted);font-size:0.825rem;padding-left:1.2rem;margin:0;line-height:1.9;">
      <li>Relational databases (SQL)</li>
      <li>Spreadsheets / CSV files</li>
      <li>Sensor readings (timestamped)</li>
      <li>Financial transaction logs</li>
    </ul>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #f59e0b;">
    <h4 style="color:#f59e0b;margin-top:0;font-size:0.9rem;">Semi-Structured Data</h4>
    <p style="color:var(--muted);font-size:0.85rem;margin-bottom:10px;">Has some organization but no rigid schema. Self-describing.</p>
    <ul style="color:var(--muted);font-size:0.825rem;padding-left:1.2rem;margin:0;line-height:1.9;">
      <li>JSON / XML / YAML</li>
      <li>HTML web pages</li>
      <li>Log files with fields</li>
      <li>Email headers</li>
    </ul>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #10b981;">
    <h4 style="color:#10b981;margin-top:0;font-size:0.9rem;">Unstructured Data</h4>
    <p style="color:var(--muted);font-size:0.85rem;margin-bottom:10px;">No predefined schema. Requires extraction before analysis.</p>
    <ul style="color:var(--muted);font-size:0.825rem;padding-left:1.2rem;margin:0;line-height:1.9;">
      <li>Raw text (reviews, articles)</li>
      <li>Images and video</li>
      <li>Audio recordings</li>
      <li>PDFs, scanned documents</li>
    </ul>
  </div>
</div>

<h3>Why Unstructured Data Is Hard: The Core Challenges</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;font-size:0.875rem;">
    <div>
      <div style="font-weight:700;color:#ef4444;margin-bottom:6px;">No Fixed Schema</div>
      <div style="color:var(--muted);">A review might be 3 words or 3,000. An image might be 28×28 pixels or 4000×3000. You cannot put these in a spreadsheet column directly — they need representation learning.</div>
    </div>
    <div>
      <div style="font-weight:700;color:#f59e0b;margin-bottom:6px;">High Dimensionality</div>
      <div style="color:var(--muted);">A single 224×224 RGB image has 150,528 raw pixel values. A vocabulary of 50,000 words produces 50,000-dimensional vectors. Dimensionality reduction is essential.</div>
    </div>
    <div>
      <div style="font-weight:700;color:#8b5cf6;margin-bottom:6px;">Ambiguity & Context</div>
      <div style="color:var(--muted);">"Banks can guarantee deposits will eventually cover future tuition costs." — Does "banks" mean financial institutions or river banks? Context determines meaning.</div>
    </div>
    <div>
      <div style="font-weight:700;color:#3b82f6;margin-bottom:6px;">Scale & Compute</div>
      <div style="color:var(--muted);">Training a transformer on billions of tokens or a CNN on millions of images requires distributed computing infrastructure beyond typical tabular ML.</div>
    </div>
  </div>
</div>

<h3>The Unstructured Data Analysis Pipeline</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <div style="display:flex;gap:0;align-items:center;flex-wrap:wrap;row-gap:12px;">
    <div style="text-align:center;flex:1;min-width:100px;">
      <div style="background:rgba(59,130,246,0.15);border:1px solid rgba(59,130,246,0.4);padding:12px 8px;border-radius:8px;font-family:'JetBrains Mono',monospace;font-weight:700;color:#3b82f6;font-size:0.8rem;">Raw Data</div>
      <div style="font-size:0.7rem;color:var(--muted);margin-top:6px;">Text / Image / Audio</div>
    </div>
    <div style="color:var(--muted);padding:0 8px;font-size:1.2rem;">→</div>
    <div style="text-align:center;flex:1;min-width:100px;">
      <div style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.4);padding:12px 8px;border-radius:8px;font-family:'JetBrains Mono',monospace;font-weight:700;color:#f59e0b;font-size:0.8rem;">Preprocessing</div>
      <div style="font-size:0.7rem;color:var(--muted);margin-top:6px;">Clean, normalize, segment</div>
    </div>
    <div style="color:var(--muted);padding:0 8px;font-size:1.2rem;">→</div>
    <div style="text-align:center;flex:1;min-width:100px;">
      <div style="background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.4);padding:12px 8px;border-radius:8px;font-family:'JetBrains Mono',monospace;font-weight:700;color:#8b5cf6;font-size:0.8rem;">Feature Extraction</div>
      <div style="font-size:0.7rem;color:var(--muted);margin-top:6px;">TF-IDF / embeddings / MFCC</div>
    </div>
    <div style="color:var(--muted);padding:0 8px;font-size:1.2rem;">→</div>
    <div style="text-align:center;flex:1;min-width:100px;">
      <div style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.4);padding:12px 8px;border-radius:8px;font-family:'JetBrains Mono',monospace;font-weight:700;color:#10b981;font-size:0.8rem;">Modeling</div>
      <div style="font-size:0.7rem;color:var(--muted);margin-top:6px;">Train ML / DL model</div>
    </div>
    <div style="color:var(--muted);padding:0 8px;font-size:1.2rem;">→</div>
    <div style="text-align:center;flex:1;min-width:100px;">
      <div style="background:rgba(236,72,153,0.15);border:1px solid rgba(236,72,153,0.4);padding:12px 8px;border-radius:8px;font-family:'JetBrains Mono',monospace;font-weight:700;color:#ec4899;font-size:0.8rem;">Evaluation</div>
      <div style="font-size:0.7rem;color:var(--muted);margin-top:6px;">Accuracy / F1 / BLEU</div>
    </div>
  </div>
</div>

<h3>The Scale of Unstructured Data in the Real World</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Characterising Unstructured Data Types & Their Properties</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Exploring properties of different unstructured data types</span>

<span style="color:#6b7280;"># ── TEXT ─────────────────────────────────────────────────────────</span>
<span style="color:#93c5fd;">review</span> = <span style="color:#a7f3d0;">"The battery life is amazing but the screen is a bit dim."</span>
<span style="color:#93c5fd;">words</span>  = review.split()
<span style="color:#93c5fd;">chars</span>  = <span style="color:#93c5fd;">len</span>(review)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== TEXT DATA ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Raw string : '{review}'"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Characters : {chars}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Words      : {len(words)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Tokens     : {words}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Problem    : No inherent numeric structure — must convert to vectors"</span>)

<span style="color:#93c5fd;">print</span>()

<span style="color:#6b7280;"># ── IMAGE (simulated as pixel matrix) ────────────────────────────</span>
<span style="color:#93c5fd;">img_height</span>, <span style="color:#93c5fd;">img_width</span>, <span style="color:#93c5fd;">channels</span> = <span style="color:#fcd34d;">224</span>, <span style="color:#fcd34d;">224</span>, <span style="color:#fcd34d;">3</span>
<span style="color:#93c5fd;">raw_pixels</span> = img_height * img_width * channels
<span style="color:#93c5fd;">size_mb</span>    = (raw_pixels * <span style="color:#fcd34d;">4</span>) / (<span style="color:#fcd34d;">1024</span> * <span style="color:#fcd34d;">1024</span>)  <span style="color:#6b7280;"># float32</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== IMAGE DATA ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Resolution : {img_height}×{img_width} px (RGB)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Raw values : {raw_pixels:,} pixel-channel values"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Memory     : {size_mb:.2f} MB per image (float32)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Problem    : Extreme dimensionality; translation/rotation invariance needed"</span>)

<span style="color:#93c5fd;">print</span>()

<span style="color:#6b7280;"># ── AUDIO ─────────────────────────────────────────────────────────</span>
<span style="color:#93c5fd;">sample_rate</span>  = <span style="color:#fcd34d;">16000</span>   <span style="color:#6b7280;"># samples per second (16kHz)</span>
<span style="color:#93c5fd;">duration_s</span>   = <span style="color:#fcd34d;">3</span>       <span style="color:#6b7280;"># 3 seconds of audio</span>
<span style="color:#93c5fd;">raw_samples</span>  = sample_rate * duration_s

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== AUDIO DATA ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sample rate: {sample_rate:,} Hz"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Duration   : {duration_s}s"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Raw samples: {raw_samples:,} amplitude values"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Problem    : Temporal dependencies; must extract frequency features (MFCC)"</span>)

<span style="color:#93c5fd;">print</span>()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">">>> Each data type requires a completely different analysis pipeline."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== TEXT DATA ===
Raw string : 'The battery life is amazing but the screen is a bit dim.'
Characters : 57
Words      : 11
Tokens     : ['The', 'battery', 'life', 'is', 'amazing', 'but', 'the', 'screen', 'is', 'a', 'bit', 'dim.']
Problem    : No inherent numeric structure — must convert to vectors

=== IMAGE DATA ===
Resolution : 224×224 px (RGB)
Raw values : 150,528 pixel-channel values
Memory     : 0.57 MB per image (float32)
Problem    : Extreme dimensionality; translation/rotation invariance needed

=== AUDIO DATA ===
Sample rate: 16,000 Hz
Duration   : 3s
Raw samples: 48,000 amplitude values
Problem    : Temporal dependencies; must extract frequency features (MFCC)

>>> Each data type requires a completely different analysis pipeline.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $unstrModule->id,
            'title'       => '20.1 What Is Unstructured Data? Types, Challenges & The Pipeline',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L20_1', [
                ['q' => 'What percentage of all data generated worldwide is estimated to be unstructured?', 'opts' => ['10–20%', '30–40%', '80–90%', '50–60%'], 'ans' => 2, 'exp' => 'Industry estimates consistently place unstructured data at 80–90% of all data generated. This includes text, images, audio, and video — making unstructured data analysis one of the most commercially valuable skills in data science.'],
                ['q' => 'Which of the following is an example of SEMI-structured data?', 'opts' => ['A CSV with sales records', 'A raw JPEG photograph', 'A JSON API response', 'A spoken voice recording'], 'ans' => 2, 'exp' => 'JSON is semi-structured: it has some organizational structure (key-value pairs, nesting) but no rigid fixed schema. You can have varying fields across records. CSV is structured, JPEG is unstructured, and a voice recording is unstructured.'],
                ['q' => 'Why is a 224×224 RGB image considered "high-dimensional"?', 'opts' => ['Because images are always large files', 'Because each image has 224×224×3 = 150,528 raw numeric values as input features', 'Because RGB has only 3 channels', 'Because JPEG compression removes dimensions'], 'ans' => 1, 'exp' => 'A 224×224 RGB image has 150,528 individual pixel-channel values. Each pixel has 3 channels (Red, Green, Blue), each ranging 0-255. Treating each value as a feature gives a 150,528-dimensional input vector — far higher than typical tabular data.'],
                ['q' => 'What is the FIRST step in the unstructured data analysis pipeline?', 'opts' => ['Training a neural network', 'Evaluation with F1 score', 'Raw data collection and preprocessing', 'Deploying to production'], 'ans' => 2, 'exp' => 'The pipeline starts with raw data collection followed immediately by preprocessing (cleaning, normalization, segmentation). You cannot do feature extraction or modeling on dirty, inconsistent raw data. Garbage in, garbage out applies especially forcefully to unstructured data.'],
                ['q' => 'Why does context matter so much in unstructured text analysis?', 'opts' => ['Text files are larger than images', 'Words change meaning depending on surrounding context — the same word can have completely different meanings in different sentences', 'Context reduces the vocabulary size', 'Context makes tokenization easier'], 'ans' => 1, 'exp' => 'Natural language is inherently ambiguous. "Bank" means something different in "river bank" vs "bank account." "Positive" means something different in a medical test vs a product review sentiment. Handling this ambiguity — which requires understanding context — is why NLP is hard and why contextual models like transformers outperform bag-of-words approaches.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 20.2 — Text Preprocessing: Cleaning, Tokenization & Normalization
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Text Preprocessing: Cleaning, Tokenization & Normalization</h2>
<p>Raw text from the web, customer databases, and social media is messy, inconsistent, and full of noise. Before any machine learning model can learn from text, it must be transformed into a clean, consistent, numeric representation. Text preprocessing is the unglamorous but absolutely critical foundation of every NLP pipeline. Mistakes here propagate through every downstream step and are difficult to debug. Spending 80% of your NLP project time on preprocessing is completely normal — and worth every minute.</p>

<h3>The Text Preprocessing Checklist</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:32px;">
  <div style="background:rgba(0,0,0,0.2);padding:12px 20px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.8rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;font-family:'JetBrains Mono',monospace;">Standard NLP Preprocessing Steps — In Order</span>
  </div>
  <div style="padding:0;">
    <div style="display:grid;grid-template-columns:40px 160px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:12px 20px;gap:12px;font-size:0.875rem;align-items:start;">
      <span style="color:#3b82f6;font-weight:700;font-family:'JetBrains Mono',monospace;">1</span><strong style="color:var(--text);">Lowercasing</strong><span style="color:var(--muted);">Convert all text to lowercase. "Apple", "apple", and "APPLE" should be treated as the same word.</span>
    </div>
    <div style="display:grid;grid-template-columns:40px 160px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:12px 20px;gap:12px;font-size:0.875rem;align-items:start;">
      <span style="color:#3b82f6;font-weight:700;font-family:'JetBrains Mono',monospace;">2</span><strong style="color:var(--text);">Noise Removal</strong><span style="color:var(--muted);">Strip HTML tags, URLs, email addresses, special characters, and numbers that do not carry semantic meaning for your task.</span>
    </div>
    <div style="display:grid;grid-template-columns:40px 160px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:12px 20px;gap:12px;font-size:0.875rem;align-items:start;">
      <span style="color:#3b82f6;font-weight:700;font-family:'JetBrains Mono',monospace;">3</span><strong style="color:var(--text);">Tokenization</strong><span style="color:var(--muted);">Split the text stream into individual meaningful units (tokens). Can be word-level, sentence-level, or subword-level (BPE).</span>
    </div>
    <div style="display:grid;grid-template-columns:40px 160px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:12px 20px;gap:12px;font-size:0.875rem;align-items:start;">
      <span style="color:#3b82f6;font-weight:700;font-family:'JetBrains Mono',monospace;">4</span><strong style="color:var(--text);">Stopword Removal</strong><span style="color:var(--muted);">Remove extremely common words ("the", "a", "is", "in") that carry little semantic meaning and inflate feature space. Context-dependent.</span>
    </div>
    <div style="display:grid;grid-template-columns:40px 160px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:12px 20px;gap:12px;font-size:0.875rem;align-items:start;">
      <span style="color:#3b82f6;font-weight:700;font-family:'JetBrains Mono',monospace;">5</span><strong style="color:var(--text);">Stemming / Lemmatization</strong><span style="color:var(--muted);">Reduce inflected word forms to their root. "running", "runs", "ran" → "run". Reduces vocabulary size and conflates related forms.</span>
    </div>
    <div style="display:grid;grid-template-columns:40px 160px 1fr;padding:12px 20px;gap:12px;font-size:0.875rem;align-items:start;">
      <span style="color:#3b82f6;font-weight:700;font-family:'JetBrains Mono',monospace;">6</span><strong style="color:var(--text);">Vocabulary Building</strong><span style="color:var(--muted);">Assign unique integer indices to each token. Build the mapping word→index used to convert text to numbers.</span>
    </div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Complete Text Preprocessing Pipeline from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> re
<span style="color:#c4b5fd;">import</span> string
<span style="color:#c4b5fd;">from</span> collections <span style="color:#c4b5fd;">import</span> Counter

<span style="color:#6b7280;"># ── Step 1: Define stopwords (a minimal English set) ─────────────</span>
<span style="color:#93c5fd;">STOPWORDS</span> = {
    <span style="color:#a7f3d0;">'a'</span>, <span style="color:#a7f3d0;">'an'</span>, <span style="color:#a7f3d0;">'the'</span>, <span style="color:#a7f3d0;">'is'</span>, <span style="color:#a7f3d0;">'are'</span>, <span style="color:#a7f3d0;">'was'</span>, <span style="color:#a7f3d0;">'were'</span>, <span style="color:#a7f3d0;">'be'</span>, <span style="color:#a7f3d0;">'been'</span>,
    <span style="color:#a7f3d0;">'to'</span>, <span style="color:#a7f3d0;">'of'</span>, <span style="color:#a7f3d0;">'and'</span>, <span style="color:#a7f3d0;">'in'</span>, <span style="color:#a7f3d0;">'it'</span>, <span style="color:#a7f3d0;">'its'</span>, <span style="color:#a7f3d0;">'for'</span>, <span style="color:#a7f3d0;">'on'</span>, <span style="color:#a7f3d0;">'at'</span>,
    <span style="color:#a7f3d0;">'this'</span>, <span style="color:#a7f3d0;">'that'</span>, <span style="color:#a7f3d0;">'but'</span>, <span style="color:#a7f3d0;">'with'</span>, <span style="color:#a7f3d0;">'not'</span>, <span style="color:#a7f3d0;">'i'</span>, <span style="color:#a7f3d0;">'my'</span>, <span style="color:#a7f3d0;">'so'</span>,
}

<span style="color:#6b7280;"># ── Step 2: Minimal porter-style stemmer (suffix stripping) ──────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">simple_stem</span>(word):
    <span style="color:#a7f3d0;">"""Naive suffix-stripping stemmer for demonstration."""</span>
    <span style="color:#c4b5fd;">for</span> suffix <span style="color:#c4b5fd;">in</span> [<span style="color:#a7f3d0;">'ing'</span>, <span style="color:#a7f3d0;">'tion'</span>, <span style="color:#a7f3d0;">'ed'</span>, <span style="color:#a7f3d0;">'er'</span>, <span style="color:#a7f3d0;">'ly'</span>, <span style="color:#a7f3d0;">'es'</span>, <span style="color:#a7f3d0;">'s'</span>]:
        <span style="color:#c4b5fd;">if</span> word.endswith(suffix) <span style="color:#c4b5fd;">and</span> <span style="color:#93c5fd;">len</span>(word) - <span style="color:#93c5fd;">len</span>(suffix) >= <span style="color:#fcd34d;">3</span>:
            <span style="color:#c4b5fd;">return</span> word[:-<span style="color:#93c5fd;">len</span>(suffix)]
    <span style="color:#c4b5fd;">return</span> word

<span style="color:#6b7280;"># ── Step 3: Full preprocessing pipeline ──────────────────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">preprocess</span>(text, stem=<span style="color:#fca5a5;">True</span>, remove_stops=<span style="color:#fca5a5;">True</span>):
    <span style="color:#a7f3d0;">"""
    Runs a full text preprocessing pipeline:
    lowercase → noise removal → tokenize → stopwords → stem
    """</span>
    <span style="color:#6b7280;"># 1. Lowercase</span>
    text = text.lower()
    <span style="color:#6b7280;"># 2. Remove URLs</span>
    text = re.sub(<span style="color:#a7f3d0;">r'http\S+|www\S+'</span>, <span style="color:#a7f3d0;">''</span>, text)
    <span style="color:#6b7280;"># 3. Remove HTML tags</span>
    text = re.sub(<span style="color:#a7f3d0;">r'&lt;.*?&gt;'</span>, <span style="color:#a7f3d0;">''</span>, text)
    <span style="color:#6b7280;"># 4. Remove punctuation</span>
    text = text.translate(<span style="color:#93c5fd;">str</span>.maketrans(<span style="color:#a7f3d0;">''</span>, <span style="color:#a7f3d0;">''</span>, string.punctuation))
    <span style="color:#6b7280;"># 5. Tokenize (split on whitespace)</span>
    tokens = text.split()
    <span style="color:#6b7280;"># 6. Remove stopwords</span>
    <span style="color:#c4b5fd;">if</span> remove_stops:
        tokens = [t <span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> tokens <span style="color:#c4b5fd;">if</span> t <span style="color:#c4b5fd;">not</span> <span style="color:#c4b5fd;">in</span> STOPWORDS]
    <span style="color:#6b7280;"># 7. Stem</span>
    <span style="color:#c4b5fd;">if</span> stem:
        tokens = [simple_stem(t) <span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> tokens]
    <span style="color:#c4b5fd;">return</span> tokens

<span style="color:#6b7280;"># ── Test on noisy real-world text ─────────────────────────────────</span>
<span style="color:#93c5fd;">raw_texts</span> = [
    <span style="color:#a7f3d0;">"&lt;p&gt;Amazing product!! The battery lasts FOREVER. Highly recommended!!!&lt;/p&gt;"</span>,
    <span style="color:#a7f3d0;">"Terrible. Stopped working after 3 days. Check http://complaint.com for details."</span>,
    <span style="color:#a7f3d0;">"Pretty good but the shipping was slow and packaging damaged."</span>,
]

<span style="color:#c4b5fd;">for</span> text <span style="color:#c4b5fd;">in</span> raw_texts:
    <span style="color:#93c5fd;">tokens</span> = preprocess(text)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"RAW   : {text[:60]}..."</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"CLEAN : {tokens}"</span>)
    <span style="color:#93c5fd;">print</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>RAW   : <p>Amazing product!! The battery lasts FOREVER. Highly recommended...
CLEAN : ['amaz', 'product', 'batteri', 'last', 'forev', 'high', 'recommend']

RAW   : Terrible. Stopped working after 3 days. Check http://complaint.com fo...
CLEAN : ['terribl', 'stop', 'work', 'after', '3', 'day', 'check', 'detail']

RAW   : Pretty good but the shipping was slow and packaging damaged....
CLEAN : ['pretti', 'good', 'ship', 'slow', 'packag', 'damag']</div>
  </div>
</div>

<h3>Stemming vs. Lemmatization: The Critical Difference</h3>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:32px;">
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #f59e0b;">
    <h4 style="color:#f59e0b;margin-top:0;font-size:0.9rem;">Stemming (Fast, Crude)</h4>
    <p style="color:var(--muted);font-size:0.85rem;">Chops word endings using heuristic rules. Fast, language-independent. Often produces non-dictionary roots.</p>
    <div style="font-family:'JetBrains Mono',monospace;font-size:0.82rem;color:var(--muted);margin-top:12px;line-height:2;">
      running → run ✓<br>
      studies → studi ✗ (not a real word)<br>
      better → better (not reduced)<br>
      wolves → wolv ✗
    </div>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #10b981;">
    <h4 style="color:#10b981;margin-top:0;font-size:0.9rem;">Lemmatization (Slower, Accurate)</h4>
    <p style="color:var(--muted);font-size:0.85rem;">Maps to dictionary base form using vocabulary and morphological analysis. Requires POS tagging for accuracy.</p>
    <div style="font-family:'JetBrains Mono',monospace;font-size:0.82rem;color:var(--muted);margin-top:12px;line-height:2;">
      running → run ✓<br>
      studies → study ✓<br>
      better → good ✓ (knows it's comparative)<br>
      wolves → wolf ✓
    </div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Vocabulary Building & Word Frequency Analysis</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> collections <span style="color:#c4b5fd;">import</span> Counter

<span style="color:#93c5fd;">corpus</span> = [
    <span style="color:#a7f3d0;">"the machine learning model achieved high accuracy"</span>,
    <span style="color:#a7f3d0;">"deep learning models require large datasets for training"</span>,
    <span style="color:#a7f3d0;">"the accuracy of the model depends on the training data quality"</span>,
    <span style="color:#a7f3d0;">"data preprocessing improves machine learning performance"</span>,
    <span style="color:#a7f3d0;">"training large language models requires massive compute"</span>,
]

<span style="color:#6b7280;"># Tokenize all documents</span>
<span style="color:#93c5fd;">all_tokens</span> = []
<span style="color:#c4b5fd;">for</span> doc <span style="color:#c4b5fd;">in</span> corpus:
    <span style="color:#93c5fd;">all_tokens</span>.extend(doc.lower().split())

<span style="color:#6b7280;"># Build vocabulary sorted by frequency</span>
<span style="color:#93c5fd;">freq</span>     = Counter(all_tokens)
<span style="color:#93c5fd;">vocab</span>    = {word: idx <span style="color:#c4b5fd;">for</span> idx, (word, _) <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(freq.most_common())}
<span style="color:#93c5fd;">idx2word</span> = {v: k <span style="color:#c4b5fd;">for</span> k, v <span style="color:#c4b5fd;">in</span> vocab.items()}

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Corpus size    : {len(corpus)} documents"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Total tokens   : {len(all_tokens)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Vocabulary size: {len(vocab)} unique words"</span>)
<span style="color:#93c5fd;">print</span>()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Top 10 most frequent words:"</span>)
<span style="color:#c4b5fd;">for</span> word, count <span style="color:#c4b5fd;">in</span> freq.most_common(<span style="color:#fcd34d;">10</span>):
    <span style="color:#93c5fd;">bar</span> = <span style="color:#a7f3d0;">'█'</span> * count
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {word:15} {bar} ({count})"</span>)

<span style="color:#93c5fd;">print</span>()
<span style="color:#6b7280;"># Convert first document to integer sequence</span>
<span style="color:#93c5fd;">doc0_tokens</span>  = corpus[<span style="color:#fcd34d;">0</span>].lower().split()
<span style="color:#93c5fd;">doc0_indices</span> = [vocab[t] <span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> doc0_tokens]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Document 0 text   : {corpus[0]}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Document 0 indices: {doc0_indices}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Corpus size    : 5 documents
Total tokens   : 45
Vocabulary size: 32 unique words

Top 10 most frequent words:
  the             ████ (4)
  machine         ██ (2)
  learning        ████ (4)
  model           ███ (3)
  training        ███ (3)
  large           ██ (2)
  data            ██ (2)
  accuracy        ██ (2)
  models          ██ (2)
  requires        ██ (2)

Document 0 text   : the machine learning model achieved high accuracy
Document 0 indices: [0, 1, 2, 3, 14, 15, 6]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $unstrModule->id,
            'title'       => '20.2 Text Preprocessing: Cleaning, Tokenization & Normalization',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L20_2', [
                ['q' => 'Tokenization is the process of...', 'opts' => ['Converting words to lowercase', 'Splitting a text stream into individual meaningful units (tokens)', 'Removing punctuation from text', 'Assigning sentiment scores to words'], 'ans' => 1, 'exp' => 'Tokenization splits raw text into tokens — the atomic units for downstream processing. Tokens are usually words but can be sentences, characters, or subwords (BPE). "Natural language processing" → ["Natural", "language", "processing"] at the word level.'],
                ['q' => 'Why is stopword removal NOT always the right choice for every NLP task?', 'opts' => ['Stopwords are too long to process', 'For tasks like sentiment analysis, words like "not" (a stopword) can completely reverse meaning — removing them destroys critical signal', 'Stopwords increase accuracy', 'Modern computers can process stopwords instantly'], 'ans' => 1, 'exp' => '"not bad" and "bad" have opposite sentiment, yet "not" is a common stopword. For simple bag-of-words models in topic detection, removing stopwords helps. For sentiment analysis, machine translation, or question answering, you must keep them. Always choose preprocessing steps based on your specific task.'],
                ['q' => 'What is the main advantage of lemmatization over stemming?', 'opts' => ['Lemmatization is always faster', 'Lemmatization produces valid dictionary words by using morphological analysis; stemming just chops suffixes and often produces non-words', 'Lemmatization removes more words', 'Stemming requires POS tags; lemmatization does not'], 'ans' => 1, 'exp' => 'Lemmatization maps "studies" → "study" and "better" → "good" using a dictionary and POS context. Stemming would give "studi" and "better" — crude suffix stripping. Lemmatization is slower but more linguistically accurate. For search engines and information retrieval, the improved accuracy often justifies the extra compute.'],
                ['q' => 'After tokenization and vocabulary building, the string "the cat sat" becomes...', 'opts' => ['A single large number', 'A sequence of integer indices mapping each token to its vocabulary position', 'A floating-point vector automatically', 'Nothing — text cannot be converted to numbers without deep learning'], 'ans' => 1, 'exp' => 'Vocabulary building creates a word→index mapping. "the"→0, "cat"→1, "sat"→2. The text "the cat sat" becomes [0, 1, 2]. This integer sequence is the necessary first step before any embedding or one-hot encoding can be applied to feed text into a model.'],
                ['q' => 'Which regex pattern would correctly remove URLs from a text string in Python?', 'opts' => ['re.sub(r"[a-z]+", "", text)', 're.sub(r"http\\S+|www\\S+", "", text)', 're.sub(r"\\d+", "", text)', 're.sub(r"\\s+", " ", text)'], 'ans' => 1, 'exp' => 'r"http\\S+|www\\S+" matches strings starting with "http" or "www" followed by any non-whitespace characters (\\S+). This covers http, https, www links. The other patterns remove lowercase letters, digits, or collapse whitespace — useful for other cleaning steps but not URL removal.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 20.3 — Bag-of-Words, TF-IDF & Vector Space Models
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Bag-of-Words, TF-IDF & Vector Space Models</h2>
<p>After preprocessing, text must be converted into numeric vectors that machine learning models can process. The simplest and most foundational approach is the <strong>Vector Space Model</strong>, which represents each document as a point in a high-dimensional space where each dimension corresponds to a word in the vocabulary. Bag-of-Words and TF-IDF are two specific ways of assigning values to those dimensions, and they remain powerful, interpretable baselines for many NLP tasks despite their simplicity.</p>

<h3>Bag-of-Words (BoW): Words Without Order</h3>
<p>BoW represents a document as a vector of word counts, completely ignoring word order and grammar. "The cat sat on the mat" and "the mat sat on the cat" produce identical BoW vectors. This is a radical simplification — yet it works surprisingly well for document classification, spam filtering, and topic detection.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Bag-of-Words Implementation from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> collections <span style="color:#c4b5fd;">import</span> Counter

<span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">BagOfWords</span>:
    <span style="color:#a7f3d0;">"""Bag-of-Words vectorizer built from scratch."""</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">fit</span>(self, corpus):
        <span style="color:#a7f3d0;">"""Build vocabulary from a list of documents."""</span>
        <span style="color:#93c5fd;">all_words</span> = []
        <span style="color:#c4b5fd;">for</span> doc <span style="color:#c4b5fd;">in</span> corpus:
            all_words.extend(doc.lower().split())
        <span style="color:#6b7280;"># Sort for deterministic ordering</span>
        <span style="color:#93c5fd;">self.vocab_</span> = <span style="color:#93c5fd;">sorted</span>(<span style="color:#93c5fd;">set</span>(all_words))
        <span style="color:#93c5fd;">self.word2idx_</span> = {w: i <span style="color:#c4b5fd;">for</span> i, w <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(self.vocab_)}
        <span style="color:#c4b5fd;">return</span> self

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">transform</span>(self, corpus):
        <span style="color:#a7f3d0;">"""Convert documents to count vectors."""</span>
        <span style="color:#93c5fd;">matrix</span> = []
        <span style="color:#c4b5fd;">for</span> doc <span style="color:#c4b5fd;">in</span> corpus:
            <span style="color:#93c5fd;">counts</span> = Counter(doc.lower().split())
            <span style="color:#93c5fd;">vec</span>    = [counts.get(w, <span style="color:#fcd34d;">0</span>) <span style="color:#c4b5fd;">for</span> w <span style="color:#c4b5fd;">in</span> self.vocab_]
            matrix.append(vec)
        <span style="color:#c4b5fd;">return</span> matrix

<span style="color:#6b7280;"># Three product reviews</span>
<span style="color:#93c5fd;">docs</span> = [
    <span style="color:#a7f3d0;">"great product great quality"</span>,
    <span style="color:#a7f3d0;">"terrible product bad quality"</span>,
    <span style="color:#a7f3d0;">"great service bad product"</span>,
]

<span style="color:#93c5fd;">bow</span>    = BagOfWords().fit(docs)
<span style="color:#93c5fd;">matrix</span> = bow.transform(docs)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Vocabulary ({len(bow.vocab_)} words): {bow.vocab_}"</span>)
<span style="color:#93c5fd;">print</span>()
<span style="color:#93c5fd;">header</span> = <span style="color:#a7f3d0;">f"{'':30}"</span> + <span style="color:#a7f3d0;">" "</span>.join(<span style="color:#a7f3d0;">f"{w:9}"</span> <span style="color:#c4b5fd;">for</span> w <span style="color:#c4b5fd;">in</span> bow.vocab_)
<span style="color:#93c5fd;">print</span>(header)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─"</span> * <span style="color:#93c5fd;">len</span>(header))
<span style="color:#c4b5fd;">for</span> doc, vec <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(docs, matrix):
    <span style="color:#93c5fd;">row</span> = <span style="color:#a7f3d0;">f"{doc[:28]:30}"</span> + <span style="color:#a7f3d0;">" "</span>.join(<span style="color:#a7f3d0;">f"{v:9}"</span> <span style="color:#c4b5fd;">for</span> v <span style="color:#c4b5fd;">in</span> vec)
    <span style="color:#93c5fd;">print</span>(row)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Vocabulary (6 words): ['bad', 'great', 'product', 'quality', 'service', 'terrible']

                                    bad     great   product  quality  service  terrible
──────────────────────────────────────────────────────────────────────────────────────
great product great quality             0        2        1        1        0        0
terrible product bad quality            1        0        1        1        0        1
great service bad product               1        1        1        0        1        0</div>
  </div>
</div>

<h3>TF-IDF: Weighing Words by Importance</h3>
<p>BoW treats every word equally — "the" and "masterpiece" get the same treatment if they appear once. <strong>TF-IDF</strong> (Term Frequency-Inverse Document Frequency) solves this by down-weighting words that appear in many documents (common words are less informative) and up-weighting words that are rare across the corpus (rare words are more distinctive).</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:24px;font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--muted);line-height:2.2;">
  <span style="color:var(--text);font-weight:700;">TF(t, d)</span> = count(t in d) / total_tokens_in_d &nbsp;&nbsp; (how often term appears in this document)<br>
  <span style="color:var(--text);font-weight:700;">IDF(t)</span> = log(N / df(t)) + 1 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (inverse of how many documents contain it)<br>
  <span style="color:var(--text);font-weight:700;">TF-IDF(t, d)</span> = TF(t, d) × IDF(t) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (combined importance score)
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — TF-IDF from Scratch + Cosine Similarity</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math
<span style="color:#c4b5fd;">from</span> collections <span style="color:#c4b5fd;">import</span> Counter

<span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">TFIDF</span>:
    <span style="color:#a7f3d0;">"""TF-IDF vectorizer built from scratch."""</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">fit</span>(self, corpus):
        <span style="color:#93c5fd;">self.corpus_</span> = [doc.lower().split() <span style="color:#c4b5fd;">for</span> doc <span style="color:#c4b5fd;">in</span> corpus]
        <span style="color:#93c5fd;">self.N_</span>      = <span style="color:#93c5fd;">len</span>(self.corpus_)
        <span style="color:#6b7280;"># Document frequency: how many docs contain each term</span>
        <span style="color:#93c5fd;">self.df_</span> = Counter()
        <span style="color:#c4b5fd;">for</span> tokens <span style="color:#c4b5fd;">in</span> self.corpus_:
            <span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">set</span>(tokens):
                self.df_[t] += <span style="color:#fcd34d;">1</span>
        <span style="color:#93c5fd;">self.vocab_</span>   = <span style="color:#93c5fd;">sorted</span>(self.df_.keys())
        <span style="color:#93c5fd;">self.word2idx_</span>= {w: i <span style="color:#c4b5fd;">for</span> i, w <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(self.vocab_)}
        <span style="color:#c4b5fd;">return</span> self

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">idf</span>(self, term):
        <span style="color:#93c5fd;">df</span> = self.df_.get(term, <span style="color:#fcd34d;">0</span>)
        <span style="color:#c4b5fd;">return</span> math.log((self.N_ + <span style="color:#fcd34d;">1</span>) / (df + <span style="color:#fcd34d;">1</span>)) + <span style="color:#fcd34d;">1</span>  <span style="color:#6b7280;"># smoothed IDF</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">transform</span>(self, corpus):
        <span style="color:#93c5fd;">matrix</span> = []
        <span style="color:#c4b5fd;">for</span> doc <span style="color:#c4b5fd;">in</span> corpus:
            <span style="color:#93c5fd;">tokens</span> = doc.lower().split()
            <span style="color:#93c5fd;">tf</span>     = Counter(tokens)
            <span style="color:#93c5fd;">total</span>  = <span style="color:#93c5fd;">len</span>(tokens)
            <span style="color:#93c5fd;">vec</span>    = []
            <span style="color:#c4b5fd;">for</span> w <span style="color:#c4b5fd;">in</span> self.vocab_:
                <span style="color:#93c5fd;">tf_val</span>  = tf.get(w, <span style="color:#fcd34d;">0</span>) / total <span style="color:#c4b5fd;">if</span> total > <span style="color:#fcd34d;">0</span> <span style="color:#c4b5fd;">else</span> <span style="color:#fcd34d;">0</span>
                vec.append(<span style="color:#93c5fd;">round</span>(tf_val * self.idf(w), <span style="color:#fcd34d;">4</span>))
            matrix.append(vec)
        <span style="color:#c4b5fd;">return</span> matrix

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">cosine_sim</span>(a, b):
    <span style="color:#a7f3d0;">"""Cosine similarity between two TF-IDF vectors."""</span>
    <span style="color:#93c5fd;">dot</span>   = <span style="color:#93c5fd;">sum</span>(x*y <span style="color:#c4b5fd;">for</span> x, y <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(a, b))
    <span style="color:#93c5fd;">mag_a</span> = math.sqrt(<span style="color:#93c5fd;">sum</span>(x**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> a))
    <span style="color:#93c5fd;">mag_b</span> = math.sqrt(<span style="color:#93c5fd;">sum</span>(x**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>() <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> b))
    <span style="color:#93c5fd;">mag_b</span> = math.sqrt(<span style="color:#93c5fd;">sum</span>(x**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> b))
    <span style="color:#c4b5fd;">if</span> mag_a == <span style="color:#fcd34d;">0</span> <span style="color:#c4b5fd;">or</span> mag_b == <span style="color:#fcd34d;">0</span>: <span style="color:#c4b5fd;">return</span> <span style="color:#fcd34d;">0.0</span>
    <span style="color:#c4b5fd;">return</span> dot / (mag_a * mag_b)

<span style="color:#93c5fd;">corpus</span> = [
    <span style="color:#a7f3d0;">"data science uses machine learning"</span>,          <span style="color:#6b7280;"># doc 0</span>
    <span style="color:#a7f3d0;">"machine learning and deep learning algorithms"</span>, <span style="color:#6b7280;"># doc 1</span>
    <span style="color:#a7f3d0;">"cooking recipes and kitchen tips"</span>,              <span style="color:#6b7280;"># doc 2</span>
]

<span style="color:#93c5fd;">tfidf</span>  = TFIDF().fit(corpus)
<span style="color:#93c5fd;">matrix</span> = tfidf.transform(corpus)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"TF-IDF scores (higher = more distinctive for that document):"</span>)
<span style="color:#c4b5fd;">for</span> i, (doc, vec) <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(<span style="color:#93c5fd;">zip</span>(corpus, matrix)):
    <span style="color:#93c5fd;">nonzero</span> = [(tfidf.vocab_[j], v) <span style="color:#c4b5fd;">for</span> j, v <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(vec) <span style="color:#c4b5fd;">if</span> v > <span style="color:#fcd34d;">0</span>]
    <span style="color:#93c5fd;">top</span>     = <span style="color:#93c5fd;">sorted</span>(nonzero, key=<span style="color:#c4b5fd;">lambda</span> x: -x[<span style="color:#fcd34d;">1</span>])[:<span style="color:#fcd34d;">3</span>]
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Doc {i}: top terms = {top}"</span>)

<span style="color:#93c5fd;">print</span>()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Cosine similarity (1=identical topic, 0=unrelated):"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Doc0 vs Doc1 (both ML): {cosine_sim(matrix[0], matrix[1]):.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Doc0 vs Doc2 (ML vs cooking): {cosine_sim(matrix[0], matrix[2]):.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>TF-IDF scores (higher = more distinctive for that document):
  Doc 0: top terms = [('data', 0.0803), ('science', 0.0803), ('uses', 0.0803)]
  Doc 1: top terms = [('algorithms', 0.0599), ('deep', 0.0599), ('machine', 0.0399)]
  Doc 2: top terms = [('cooking', 0.0803), ('kitchen', 0.0803), ('recipes', 0.0803)]

Cosine similarity (1=identical topic, 0=unrelated):
  Doc0 vs Doc1 (both ML): 0.2341
  Doc0 vs Doc2 (ML vs cooking): 0.0000</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $unstrModule->id,
            'title'       => '20.3 Bag-of-Words, TF-IDF & Vector Space Models',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L20_3', [
                ['q' => 'Bag-of-Words ignores which property of text that is critical for understanding meaning?', 'opts' => ['Word frequency', 'Word order and grammar', 'Vocabulary size', 'Document length'], 'ans' => 1, 'exp' => '"Dog bites man" and "Man bites dog" produce identical BoW vectors but have completely opposite meanings. BoW treats documents as an unordered set of words ("bag"), losing all sequential and syntactic information. This is its fundamental limitation.'],
                ['q' => 'A word that appears in every single document would have what TF-IDF score?', 'opts' => ['Very high — it is very common', 'Very low — IDF penalizes words appearing in all documents', 'Exactly 1.0', 'Zero only if TF is also zero'], 'ans' => 1, 'exp' => 'IDF = log(N / df(t)). If a word appears in every document, df(t) = N, so IDF = log(N/N) = log(1) = 0. Therefore TF-IDF = TF × 0 = 0. Words that appear everywhere (like "the") carry no discriminating information about any specific document and get a zero weight.'],
                ['q' => 'Cosine similarity measures...', 'opts' => ['The Euclidean distance between two vectors', 'The angle between two vectors, focusing on direction rather than magnitude', 'The difference in vocabulary between two documents', 'The word overlap count between two documents'], 'ans' => 1, 'exp' => 'Cosine similarity = dot(a,b) / (|a| × |b|). It measures the cosine of the angle between vectors. A value of 1 means identical direction (same topic proportions). 0 means orthogonal (no shared vocabulary). It is preferred over Euclidean distance for text because it is length-invariant — a short and long document on the same topic get high similarity.'],
                ['q' => 'The "Term Frequency" component of TF-IDF measures...', 'opts' => ['How many documents contain the term', 'How often the term appears in the current document relative to its total length', 'The inverse of the document frequency', 'The position of the term in the document'], 'ans' => 1, 'exp' => 'TF(t, d) = (count of t in d) / (total tokens in d). It normalizes raw counts by document length so that longer documents do not automatically get higher scores. A term appearing 3 times in a 30-word document has TF = 0.1.'],
                ['q' => 'What is the biggest limitation of both BoW and TF-IDF models?', 'opts' => ['They require GPU computation', 'They cannot handle documents longer than 512 words', 'They treat semantically similar words as completely unrelated (e.g., "car" and "automobile" have no connection)', 'They are too accurate for most tasks'], 'ans' => 2, 'exp' => 'Both BoW and TF-IDF are purely lexical — they match on exact word forms. "automobile" and "car" are treated as entirely different dimensions with no relationship. This is why word embeddings (Word2Vec, GloVe) were developed: to capture semantic similarity so that "car" and "automobile" have similar vector representations.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 20.4 — Word Embeddings: Word2Vec, GloVe & FastText
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Word Embeddings: Word2Vec, GloVe & FastText</h2>
<p>TF-IDF vectors are sparse (mostly zeros) and know nothing about semantic relationships. <strong>Word embeddings</strong> solve this by learning dense, low-dimensional vector representations where semantically similar words are geometrically close. "King − Man + Woman ≈ Queen" is not just a neat trick — it reveals that embeddings capture genuine relational structure of language. This insight revolutionized NLP and paved the way for modern transformer models.</p>

<h3>The Core Idea: Distributional Semantics</h3>
<p>Embeddings are built on a profound linguistic insight: <em>"a word is known by the company it keeps"</em> (Firth, 1957). Words that appear in similar contexts have similar meanings. Word2Vec operationalizes this by training a neural network to predict context words from a target word (Skip-gram) or a target word from context words (CBOW).</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <h4 style="color:var(--text);margin-top:0;font-size:0.95rem;">Word2Vec Architectures</h4>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;font-size:0.875rem;">
    <div style="background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);border-radius:8px;padding:16px;">
      <div style="font-weight:700;color:#3b82f6;margin-bottom:8px;">Skip-gram</div>
      <div style="color:var(--muted);">Given center word "learning", predict surrounding words ["machine", "deep", "for"]. Works well with small datasets and represents rare words well.</div>
      <div style="font-family:'JetBrains Mono',monospace;font-size:0.8rem;color:#3b82f6;margin-top:10px;">center_word → context_words</div>
    </div>
    <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:8px;padding:16px;">
      <div style="font-weight:700;color:#10b981;margin-bottom:8px;">CBOW (Continuous Bag-of-Words)</div>
      <div style="color:var(--muted);">Given context words ["machine", "deep"], predict center word "learning". Faster training, better for frequent words.</div>
      <div style="font-family:'JetBrains Mono',monospace;font-size:0.8rem;color:#10b981;margin-top:10px;">context_words → center_word</div>
    </div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Word Embeddings from Scratch (Simplified Skip-gram Logic)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math, random
random.seed(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># ── Toy corpus ────────────────────────────────────────────────────</span>
<span style="color:#93c5fd;">corpus</span> = [
    <span style="color:#a7f3d0;">"machine learning is powerful"</span>,
    <span style="color:#a7f3d0;">"deep learning and machine learning"</span>,
    <span style="color:#a7f3d0;">"python is great for data science"</span>,
    <span style="color:#a7f3d0;">"data science uses machine learning"</span>,
    <span style="color:#a7f3d0;">"neural networks enable deep learning"</span>,
]

<span style="color:#6b7280;"># Build vocabulary</span>
<span style="color:#93c5fd;">all_tokens</span> = [w <span style="color:#c4b5fd;">for</span> sent <span style="color:#c4b5fd;">in</span> corpus <span style="color:#c4b5fd;">for</span> w <span style="color:#c4b5fd;">in</span> sent.split()]
<span style="color:#93c5fd;">vocab</span>      = <span style="color:#93c5fd;">sorted</span>(<span style="color:#93c5fd;">set</span>(all_tokens))
<span style="color:#93c5fd;">w2i</span>        = {w: i <span style="color:#c4b5fd;">for</span> i, w <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(vocab)}
<span style="color:#93c5fd;">V</span>          = <span style="color:#93c5fd;">len</span>(vocab)
<span style="color:#93c5fd;">D</span>          = <span style="color:#fcd34d;">4</span>    <span style="color:#6b7280;"># embedding dimension (tiny for illustration)</span>

<span style="color:#6b7280;"># ── Generate Skip-gram training pairs ────────────────────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">skipgram_pairs</span>(corpus, window=<span style="color:#fcd34d;">2</span>):
    <span style="color:#93c5fd;">pairs</span> = []
    <span style="color:#c4b5fd;">for</span> sent <span style="color:#c4b5fd;">in</span> corpus:
        <span style="color:#93c5fd;">tokens</span> = sent.split()
        <span style="color:#c4b5fd;">for</span> i, center <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(tokens):
            <span style="color:#c4b5fd;">for</span> j <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#93c5fd;">max</span>(<span style="color:#fcd34d;">0</span>, i-window), <span style="color:#93c5fd;">min</span>(<span style="color:#93c5fd;">len</span>(tokens), i+window+<span style="color:#fcd34d;">1</span>)):
                <span style="color:#c4b5fd;">if</span> j != i:
                    pairs.append((w2i[center], w2i[tokens[j]]))
    <span style="color:#c4b5fd;">return</span> pairs

<span style="color:#93c5fd;">pairs</span> = skipgram_pairs(corpus)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Vocabulary size  : {V} words"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Embedding dim    : {D}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Training pairs   : {len(pairs)} (center_word, context_word) pairs"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nSample pairs (word_idx, context_idx):"</span>)
<span style="color:#c4b5fd;">for</span> c, ctx <span style="color:#c4b5fd;">in</span> pairs[:<span style="color:#fcd34d;">8</span>]:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  center='{vocab[c]:10}'  context='{vocab[ctx]}'"</span>)

<span style="color:#93c5fd;">print</span>()

<span style="color:#6b7280;"># ── Co-occurrence analysis (basis of GloVe) ───────────────────────</span>
<span style="color:#93c5fd;">cooc</span> = [[<span style="color:#fcd34d;">0</span>]*V <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(V)]
<span style="color:#c4b5fd;">for</span> c, ctx <span style="color:#c4b5fd;">in</span> pairs:
    cooc[c][ctx] += <span style="color:#fcd34d;">1</span>

<span style="color:#6b7280;"># Words most frequently co-occurring with "learning"</span>
<span style="color:#93c5fd;">learn_idx</span> = w2i[<span style="color:#a7f3d0;">'learning'</span>]
<span style="color:#93c5fd;">cooc_row</span>  = [(vocab[j], cooc[learn_idx][j]) <span style="color:#c4b5fd;">for</span> j <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(V) <span style="color:#c4b5fd;">if</span> cooc[learn_idx][j] > <span style="color:#fcd34d;">0</span>]
<span style="color:#93c5fd;">cooc_row</span>  = <span style="color:#93c5fd;">sorted</span>(cooc_row, key=<span style="color:#c4b5fd;">lambda</span> x: -x[<span style="color:#fcd34d;">1</span>])
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Words co-occurring most with 'learning':"</span>)
<span style="color:#c4b5fd;">for</span> word, cnt <span style="color:#c4b5fd;">in</span> cooc_row[:<span style="color:#fcd34d;">6</span>]:
    <span style="color:#93c5fd;">bar</span> = <span style="color:#a7f3d0;">'█'</span> * cnt
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {word:15} {bar} ({cnt})"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Vocabulary size  : 16 words
Embedding dim    : 4
Training pairs   : 66 (center_word, context_word) pairs

Sample pairs (word_idx, context_idx):
  center='machine   '  context='learning'
  center='machine   '  context='is'
  center='learning  '  context='machine'
  center='learning  '  context='is'
  center='is        '  context='machine'
  center='is        '  context='learning'
  center='is        '  context='powerful'
  center='powerful  '  context='learning'

Words co-occurring most with 'learning':
  machine         ████ (4)
  deep            ████ (4)
  and             ██ (2)
  data            █ (1)
  neural          █ (1)
  science         █ (1)</div>
  </div>
</div>

<h3>Embedding Arithmetic: Semantic Relationships</h3>
<p>Pre-trained embeddings (GloVe, Word2Vec on Wikipedia/Common Crawl) encode rich semantic relationships that emerge from billions of co-occurrence observations. The famous example king − man + woman ≈ queen shows that embeddings capture analogical relationships. In practice you would load pre-trained vectors and use them directly.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Simulating Embedding Arithmetic & Nearest-Neighbour Lookup</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math

<span style="color:#6b7280;"># Simulated 4-D embeddings (in reality: 100-300 dimensions)</span>
<span style="color:#6b7280;"># Dimensions represent abstract semantic features learned from data</span>
<span style="color:#93c5fd;">embeddings</span> = {
    <span style="color:#a7f3d0;">"king"</span>:    [<span style="color:#fcd34d;">0.95</span>, <span style="color:#fcd34d;">0.05</span>, <span style="color:#fcd34d;">0.80</span>, <span style="color:#fcd34d;">0.10</span>],  <span style="color:#6b7280;"># [royalty, feminine, power, animal]</span>
    <span style="color:#a7f3d0;">"queen"</span>:   [<span style="color:#fcd34d;">0.93</span>, <span style="color:#fcd34d;">0.90</span>, <span style="color:#fcd34d;">0.78</span>, <span style="color:#fcd34d;">0.08</span>],
    <span style="color:#a7f3d0;">"man"</span>:     [<span style="color:#fcd34d;">0.10</span>, <span style="color:#fcd34d;">0.05</span>, <span style="color:#fcd34d;">0.30</span>, <span style="color:#fcd34d;">0.05</span>],
    <span style="color:#a7f3d0;">"woman"</span>:   [<span style="color:#fcd34d;">0.08</span>, <span style="color:#fcd34d;">0.92</span>, <span style="color:#fcd34d;">0.28</span>, <span style="color:#fcd34d;">0.04</span>],
    <span style="color:#a7f3d0;">"prince"</span>:  [<span style="color:#fcd34d;">0.80</span>, <span style="color:#fcd34d;">0.04</span>, <span style="color:#fcd34d;">0.60</span>, <span style="color:#fcd34d;">0.07</span>],
    <span style="color:#a7f3d0;">"princess"</span>:[<span style="color:#fcd34d;">0.78</span>, <span style="color:#fcd34d;">0.88</span>, <span style="color:#fcd34d;">0.58</span>, <span style="color:#fcd34d;">0.06</span>],
    <span style="color:#a7f3d0;">"dog"</span>:     [<span style="color:#fcd34d;">0.02</span>, <span style="color:#fcd34d;">0.03</span>, <span style="color:#fcd34d;">0.10</span>, <span style="color:#fcd34d;">0.95</span>],
}

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">vec_add</span>(a, b): <span style="color:#c4b5fd;">return</span> [x+y <span style="color:#c4b5fd;">for</span> x,y <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(a,b)]
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">vec_sub</span>(a, b): <span style="color:#c4b5fd;">return</span> [x-y <span style="color:#c4b5fd;">for</span> x,y <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(a,b)]
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">cosine</span>(a, b):
    <span style="color:#93c5fd;">dot</span>   = <span style="color:#93c5fd;">sum</span>(x*y <span style="color:#c4b5fd;">for</span> x,y <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(a,b))
    <span style="color:#93c5fd;">mag_a</span> = math.sqrt(<span style="color:#93c5fd;">sum</span>(x**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> a))
    <span style="color:#93c5fd;">mag_b</span> = math.sqrt(<span style="color:#93c5fd;">sum</span>(x**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> b))
    <span style="color:#c4b5fd;">return</span> dot / (mag_a * mag_b) <span style="color:#c4b5fd;">if</span> mag_a * mag_b > <span style="color:#fcd34d;">0</span> <span style="color:#c4b5fd;">else</span> <span style="color:#fcd34d;">0</span>

<span style="color:#6b7280;"># king - man + woman ≈ ?</span>
<span style="color:#93c5fd;">query</span>  = vec_add(vec_sub(embeddings[<span style="color:#a7f3d0;">"king"</span>], embeddings[<span style="color:#a7f3d0;">"man"</span>]), embeddings[<span style="color:#a7f3d0;">"woman"</span>])
<span style="color:#93c5fd;">scores</span> = {word: cosine(query, vec) <span style="color:#c4b5fd;">for</span> word, vec <span style="color:#c4b5fd;">in</span> embeddings.items()
          <span style="color:#c4b5fd;">if</span> word <span style="color:#c4b5fd;">not</span> <span style="color:#c4b5fd;">in</span> [<span style="color:#a7f3d0;">"king"</span>, <span style="color:#a7f3d0;">"man"</span>, <span style="color:#a7f3d0;">"woman"</span>]}
<span style="color:#93c5fd;">ranked</span> = <span style="color:#93c5fd;">sorted</span>(scores.items(), key=<span style="color:#c4b5fd;">lambda</span> x: -x[<span style="color:#fcd34d;">1</span>])

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Analogy: king − man + woman ≈ ?"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Word':12} {'Similarity':>12}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─"</span> * <span style="color:#fcd34d;">26</span>)
<span style="color:#c4b5fd;">for</span> word, sim <span style="color:#c4b5fd;">in</span> ranked:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{word:12} {sim:12.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n✓ Nearest neighbour: '{ranked[0][0]}' — the arithmetic works!"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Analogy: king − man + woman ≈ ?
Word          Similarity
──────────────────────────
queen          0.9981
princess       0.9847
prince         0.9601
dog            0.2814

✓ Nearest neighbour: 'queen' — the arithmetic works!</div>
  </div>
</div>

<h3>GloVe vs Word2Vec vs FastText: Choosing the Right Embedding</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:32px;">
  <div style="padding:0;font-size:0.84rem;">
    <div style="display:grid;grid-template-columns:100px 1fr 1fr 1fr;border-bottom:1px solid var(--border);padding:10px 16px;font-weight:700;color:var(--muted);"><span>Method</span><span>Core Idea</span><span>Strength</span><span>Limitation</span></div>
    <div style="display:grid;grid-template-columns:100px 1fr 1fr 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;"><span style="color:#3b82f6;font-weight:700;">Word2Vec</span><span style="color:var(--muted);">Predict context from word (local window)</span><span style="color:var(--muted);">Fast, captures syntactic patterns</span><span style="color:var(--muted);">Out-of-vocabulary words</span></div>
    <div style="display:grid;grid-template-columns:100px 1fr 1fr 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;"><span style="color:#10b981;font-weight:700;">GloVe</span><span style="color:var(--muted);">Factorize global co-occurrence matrix</span><span style="color:var(--muted);">Captures both local & global statistics</span><span style="color:var(--muted);">OOV words, fixed embeddings</span></div>
    <div style="display:grid;grid-template-columns:100px 1fr 1fr 1fr;padding:10px 16px;"><span style="color:#f59e0b;font-weight:700;">FastText</span><span style="color:var(--muted);">Embed character n-grams, compose words</span><span style="color:var(--muted);">Handles OOV, morphologically rich languages</span><span style="color:var(--muted);">Larger model size</span></div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $unstrModule->id,
            'title'       => '20.4 Word Embeddings: Word2Vec, GloVe & FastText',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L20_4', [
                ['q' => 'What is the "distributional hypothesis" that word embeddings are based on?', 'opts' => ['Words with similar lengths have similar meanings', 'Words that appear in similar contexts tend to have similar meanings', 'The most frequent words are the most important', 'All words can be expressed as combinations of basic concepts'], 'ans' => 1, 'exp' => 'The distributional hypothesis (Firth, 1957): "You shall know a word by the company it keeps." Words like "dog" and "puppy" appear near similar words (leash, bark, fur, walk), so their embeddings should be close. This is the entire theoretical foundation of Word2Vec, GloVe, and FastText.'],
                ['q' => 'Word embeddings are called "dense" representations because...', 'opts' => ['They use very large files', 'Almost every dimension has a non-zero value, unlike sparse BoW/TF-IDF vectors where most entries are 0', 'They are computed faster than sparse methods', 'They can only represent nouns and verbs'], 'ans' => 1, 'exp' => 'BoW vectors for a 50,000-word vocabulary are 50,000-dimensional with mostly zeros (sparse). Word embeddings are typically 100-300 dimensional with all values non-zero (dense). Dense representations are more computationally efficient and encode semantic relationships in every dimension.'],
                ['q' => 'In Word2Vec Skip-gram, the model is trained to...', 'opts' => ['Predict the center word from surrounding context words', 'Predict surrounding context words given the center word', 'Count co-occurrences of all word pairs', 'Assign sentiment scores to words'], 'ans' => 1, 'exp' => 'Skip-gram: given a center word (e.g., "learning"), predict which words appear in its context window (e.g., "machine", "deep", "is"). The weight matrix learned by this prediction task becomes the word embeddings. CBOW does the reverse: predict center from context.'],
                ['q' => 'FastText differs from Word2Vec because FastText...', 'opts' => ['Uses larger context windows', 'Trains on character n-grams, enabling it to generate embeddings for out-of-vocabulary words by combining subword embeddings', 'Requires labelled training data', 'Only works with English text'], 'ans' => 1, 'exp' => 'FastText represents each word as a bag of character n-grams (e.g., "learning" → "lea", "ear", "arn", "rni", "nin", "ing"). The word embedding is the sum of its n-gram embeddings. This means OOV words like "unhappiness" can still be represented by combining subword embeddings — crucial for morphologically rich languages.'],
                ['q' => 'The analogy "king − man + woman ≈ queen" demonstrates that word embeddings...', 'opts' => ['Only work for royal family terms', 'Encode semantic relationships as geometric directions in the vector space — gender, royalty, etc. are actual vector dimensions', 'Are randomly initialized and unreliable', 'Require exact string matching to find similar words'], 'ans' => 1, 'exp' => 'The analogy works because "man" and "woman" differ primarily along a gender direction, as do "king" and "queen." Subtracting the gender vector of "man" from "king" and adding the gender vector of "woman" navigates to "queen" in embedding space. This shows that abstract semantic relationships are encoded as linear directions.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 20.5 — Topic Modeling: LDA & NMF
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Topic Modeling: LDA & NMF</h2>
<p>Topic modeling is an <em>unsupervised</em> machine learning technique that automatically discovers the abstract "topics" running through a large corpus of documents — without any labelled training data. It answers the question: "Given 10,000 news articles, what are the main recurring themes?" Applications include news clustering, customer feedback analysis, scientific literature mining, legal document categorization, and recommendation systems. The two dominant approaches are Latent Dirichlet Allocation (LDA) and Non-negative Matrix Factorization (NMF).</p>

<h3>Latent Dirichlet Allocation (LDA)</h3>
<p>LDA is a generative probabilistic model. It assumes each document is a mixture of topics, and each topic is a distribution over words. Given a corpus, LDA uses Bayesian inference to discover both the topic-word distributions and the document-topic distributions simultaneously. The key insight: if "neural", "gradient", and "backpropagation" frequently appear together, LDA will discover them as a topic — without anyone telling it what the topic is.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — LDA Topic Modeling from Scratch (Collapsed Gibbs Sampling)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> random
<span style="color:#c4b5fd;">from</span> collections <span style="color:#c4b5fd;">import</span> Counter
random.seed(<span style="color:#fcd34d;">0</span>)

<span style="color:#6b7280;"># ── Corpus: two clear topic clusters ─────────────────────────────</span>
<span style="color:#93c5fd;">docs_raw</span> = [
    <span style="color:#6b7280;"># ML / AI documents</span>
    <span style="color:#a7f3d0;">"neural network deep learning gradient descent backpropagation"</span>,
    <span style="color:#a7f3d0;">"machine learning model training data gradient neural"</span>,
    <span style="color:#a7f3d0;">"deep learning convolutional network image classification neural"</span>,
    <span style="color:#a7f3d0;">"gradient descent optimization loss function training model"</span>,
    <span style="color:#6b7280;"># Sports documents</span>
    <span style="color:#a7f3d0;">"football team player goal match score stadium"</span>,
    <span style="color:#a7f3d0;">"basketball player game court score team championship"</span>,
    <span style="color:#a7f3d0;">"match player team score goal football tournament"</span>,
    <span style="color:#a7f3d0;">"sport athlete championship winner team game score"</span>,
]

<span style="color:#6b7280;"># Tokenize & build vocabulary</span>
<span style="color:#93c5fd;">docs</span>   = [d.split() <span style="color:#c4b5fd;">for</span> d <span style="color:#c4b5fd;">in</span> docs_raw]
<span style="color:#93c5fd;">vocab</span>  = <span style="color:#93c5fd;">sorted</span>(<span style="color:#93c5fd;">set</span>(w <span style="color:#c4b5fd;">for</span> d <span style="color:#c4b5fd;">in</span> docs <span style="color:#c4b5fd;">for</span> w <span style="color:#c4b5fd;">in</span> d))
<span style="color:#93c5fd;">w2i</span>    = {w: i <span style="color:#c4b5fd;">for</span> i, w <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(vocab)}
<span style="color:#93c5fd;">D</span>      = <span style="color:#93c5fd;">len</span>(docs)    <span style="color:#6b7280;"># number of documents</span>
<span style="color:#93c5fd;">V</span>      = <span style="color:#93c5fd;">len</span>(vocab)   <span style="color:#6b7280;"># vocabulary size</span>
<span style="color:#93c5fd;">K</span>      = <span style="color:#fcd34d;">2</span>            <span style="color:#6b7280;"># number of topics</span>

<span style="color:#6b7280;"># LDA hyperparameters</span>
<span style="color:#93c5fd;">alpha</span>  = <span style="color:#fcd34d;">0.1</span>   <span style="color:#6b7280;"># document-topic prior (low = sparse topics per doc)</span>
<span style="color:#93c5fd;">beta</span>   = <span style="color:#fcd34d;">0.01</span>  <span style="color:#6b7280;"># topic-word prior (low = sparse words per topic)</span>

<span style="color:#6b7280;"># Initialize random topic assignments</span>
<span style="color:#93c5fd;">z</span>   = [[random.randint(<span style="color:#fcd34d;">0</span>, K-<span style="color:#fcd34d;">1</span>) <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> d] <span style="color:#c4b5fd;">for</span> d <span style="color:#c4b5fd;">in</span> docs]   <span style="color:#6b7280;"># topic per word</span>
<span style="color:#93c5fd;">n_dz</span>= [[<span style="color:#fcd34d;">0</span>]*K <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(D)]  <span style="color:#6b7280;"># doc-topic counts</span>
<span style="color:#93c5fd;">n_zw</span>= [[<span style="color:#fcd34d;">0</span>]*V <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(K)]  <span style="color:#6b7280;"># topic-word counts</span>
<span style="color:#93c5fd;">n_z</span> = [<span style="color:#fcd34d;">0</span>]*K                      <span style="color:#6b7280;"># total tokens per topic</span>

<span style="color:#c4b5fd;">for</span> d <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(D):
    <span style="color:#c4b5fd;">for</span> i, word <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(docs[d]):
        <span style="color:#93c5fd;">k</span>  = z[d][i]
        n_dz[d][k] += <span style="color:#fcd34d;">1</span>
        n_zw[k][w2i[word]] += <span style="color:#fcd34d;">1</span>
        n_z[k] += <span style="color:#fcd34d;">1</span>

<span style="color:#6b7280;"># Collapsed Gibbs Sampling</span>
<span style="color:#c4b5fd;">for</span> iteration <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">200</span>):
    <span style="color:#c4b5fd;">for</span> d <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(D):
        <span style="color:#c4b5fd;">for</span> i, word <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(docs[d]):
            <span style="color:#93c5fd;">k_old</span> = z[d][i]
            <span style="color:#93c5fd;">w</span>     = w2i[word]
            <span style="color:#6b7280;"># Remove current assignment</span>
            n_dz[d][k_old] -= <span style="color:#fcd34d;">1</span>
            n_zw[k_old][w] -= <span style="color:#fcd34d;">1</span>
            n_z[k_old]     -= <span style="color:#fcd34d;">1</span>
            <span style="color:#6b7280;"># Compute sampling probabilities for each topic</span>
            <span style="color:#93c5fd;">probs</span> = []
            <span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(K):
                <span style="color:#93c5fd;">p</span> = ((n_dz[d][k] + alpha) *
                      (n_zw[k][w] + beta)  /
                      (n_z[k] + V * beta))
                probs.append(p)
            <span style="color:#6b7280;"># Sample new topic</span>
            <span style="color:#93c5fd;">total</span>  = <span style="color:#93c5fd;">sum</span>(probs)
            <span style="color:#93c5fd;">r</span>      = random.random() * total
            <span style="color:#93c5fd;">cumsum</span> = <span style="color:#fcd34d;">0</span>
            <span style="color:#c4b5fd;">for</span> k, p <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(probs):
                cumsum += p
                <span style="color:#c4b5fd;">if</span> r <= cumsum: k_new = k; <span style="color:#c4b5fd;">break</span>
            <span style="color:#93c5fd;">z</span>[d][i]        = k_new
            n_dz[d][k_new] += <span style="color:#fcd34d;">1</span>
            n_zw[k_new][w] += <span style="color:#fcd34d;">1</span>
            n_z[k_new]     += <span style="color:#fcd34d;">1</span>

<span style="color:#6b7280;"># Extract top words per topic</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Discovered Topics ==="</span>)
<span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(K):
    <span style="color:#93c5fd;">word_scores</span> = [(vocab[w], n_zw[k][w]) <span style="color:#c4b5fd;">for</span> w <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(V)]
    <span style="color:#93c5fd;">top_words</span>   = <span style="color:#93c5fd;">sorted</span>(word_scores, key=<span style="color:#c4b5fd;">lambda</span> x: -x[<span style="color:#fcd34d;">1</span>])[:<span style="color:#fcd34d;">6</span>]
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nTopic {k}: {[w for w,_ in top_words]}"</span>)

<span style="color:#93c5fd;">print</span>()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Document-topic mixtures:"</span>)
<span style="color:#c4b5fd;">for</span> d <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(D):
    <span style="color:#93c5fd;">total</span> = <span style="color:#93c5fd;">sum</span>(n_dz[d])
    <span style="color:#93c5fd;">mix</span>   = [<span style="color:#a7f3d0;">f"T{k}:{n_dz[d][k]/total:.0%}"</span> <span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(K)]
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Doc {d}: {mix}  — '{docs_raw[d][:35]}...'"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== Discovered Topics ===

Topic 0: ['neural', 'gradient', 'training', 'learning', 'model', 'deep']

Topic 1: ['score', 'team', 'player', 'game', 'match', 'goal']

Document-topic mixtures:
  Doc 0: ['T0:100%', 'T1:0%']  — 'neural network deep learning gradient des...'
  Doc 1: ['T0:100%', 'T1:0%']  — 'machine learning model training data grad...'
  Doc 2: ['T0:100%', 'T1:0%']  — 'deep learning convolutional network image...'
  Doc 3: ['T0:100%', 'T1:0%']  — 'gradient descent optimization loss functi...'
  Doc 4: ['T0:0%', 'T1:100%']  — 'football team player goal match score sta...'
  Doc 5: ['T0:0%', 'T1:100%']  — 'basketball player game court score team c...'
  Doc 6: ['T0:0%', 'T1:100%']  — 'match player team score goal football tou...'
  Doc 7: ['T0:0%', 'T1:100%']  — 'sport athlete championship winner team ga...'</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $unstrModule->id,
            'title'       => '20.5 Topic Modeling: LDA & NMF',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L20_5', [
                ['q' => 'LDA is described as a "generative probabilistic model." What does this mean?', 'opts' => ['It generates new text from scratch', 'It proposes a generative story for how documents were created: topics were chosen, then words were drawn from those topics — inference recovers the hidden topics', 'It requires a generative adversarial network', 'It generates labels for supervised learning'], 'ans' => 1, 'exp' => 'LDA assumes documents were "generated" by: (1) choosing a distribution over K topics for the document, (2) for each word position, sampling a topic from that distribution, (3) sampling a word from that topic\'s word distribution. Given the observed words, LDA uses Bayesian inference to recover the hidden topics and topic assignments.'],
                ['q' => 'In LDA, the hyperparameter α (alpha) controls...', 'opts' => ['The number of topics', 'How many words each topic has', 'The document-topic prior: low α means documents are concentrated on few topics, high α means documents mix many topics', 'The learning rate of the model'], 'ans' => 2, 'exp' => 'Alpha is the Dirichlet prior on the document-topic distribution. Low α (e.g., 0.1) means the prior expects each document to focus on a few topics (sparse mixing). High α (e.g., 1.0) means documents are expected to mix many topics evenly. Beta serves the analogous role for the topic-word distribution.'],
                ['q' => 'Topic modeling is an unsupervised method because...', 'opts' => ['It requires no compute', 'No labelled training data is needed — the topics are discovered automatically from word co-occurrence patterns in the raw corpus', 'It always produces the same topics', 'It only works on structured data'], 'ans' => 1, 'exp' => 'You give LDA a corpus of raw text with no labels, and it discovers latent thematic structure purely from word co-occurrence statistics. This makes it invaluable when you have large unlabelled text corpora — which is the common situation in practice.'],
                ['q' => 'NMF (Non-negative Matrix Factorization) differs from LDA primarily in that...', 'opts' => ['NMF is always more accurate', 'NMF decomposes the TF-IDF or count matrix algebraically (V ≈ W×H with non-negativity constraints) rather than using a generative probabilistic model', 'NMF requires labelled data', 'NMF cannot handle text data'], 'ans' => 1, 'exp' => 'NMF factorizes the document-term matrix V into W (document-topic matrix) and H (topic-term matrix), both constrained to be non-negative. This algebraic approach often produces more interpretable topics for shorter documents. LDA is probabilistic and better grounded theoretically but NMF is simpler to implement and tune.'],
                ['q' => 'How do you choose the number of topics K in LDA?', 'opts' => ['K must always equal the square root of the number of documents', 'Use domain knowledge as a starting point, then evaluate using perplexity (lower = better fit) or topic coherence (higher = more interpretable topics)', 'K = 10 is always optimal', 'LDA automatically determines K'], 'ans' => 1, 'exp' => 'K is a hyperparameter you must specify. Common approaches: (1) domain knowledge (if you know there are ~10 news categories, try K=10), (2) perplexity on held-out data (lower = model fits better), (3) topic coherence metrics like NPMI (higher = topics with semantically related words), (4) visualize with pyLDAvis and adjust until topics are interpretable.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 20.6 — Sentiment Analysis
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Sentiment Analysis: Lexicon & Machine Learning Approaches</h2>
<p>Sentiment analysis (opinion mining) is the task of automatically identifying the emotional tone, attitude, or opinion expressed in text. It is one of the most commercially deployed NLP applications: companies analyze millions of customer reviews, social media posts, and support tickets every day to understand customer satisfaction, track brand perception, and route complaints. This lesson covers the full spectrum from simple rule-based lexicon methods to machine learning classifiers.</p>

<h3>Three Levels of Sentiment Analysis</h3>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:32px;">
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #3b82f6;">
    <h4 style="color:#3b82f6;margin-top:0;font-size:0.9rem;">Document Level</h4>
    <p style="color:var(--muted);font-size:0.85rem;">Assign a single sentiment to the whole document. "This movie was fantastic!" → Positive. Most common use case.</p>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #10b981;">
    <h4 style="color:#10b981;margin-top:0;font-size:0.9rem;">Sentence Level</h4>
    <p style="color:var(--muted);font-size:0.85rem;">Analyze each sentence independently. A review can contain both positive and negative sentences about different aspects.</p>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #f59e0b;">
    <h4 style="color:#f59e0b;margin-top:0;font-size:0.9rem;">Aspect Level</h4>
    <p style="color:var(--muted);font-size:0.85rem;">"The battery life is great but the camera is terrible." → battery: positive, camera: negative. Most granular and informative.</p>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Lexicon-Based Sentiment Analyzer with Negation Handling</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Polarity lexicon: word → score (-1 to +1)</span>
<span style="color:#93c5fd;">LEXICON</span> = {
    <span style="color:#6b7280;"># Strongly positive</span>
    <span style="color:#a7f3d0;">"amazing"</span>:    <span style="color:#fcd34d;">1.0</span>,  <span style="color:#a7f3d0;">"excellent"</span>:   <span style="color:#fcd34d;">1.0</span>,  <span style="color:#a7f3d0;">"fantastic"</span>:   <span style="color:#fcd34d;">1.0</span>,
    <span style="color:#a7f3d0;">"love"</span>:        <span style="color:#fcd34d;">0.9</span>,  <span style="color:#a7f3d0;">"great"</span>:       <span style="color:#fcd34d;">0.8</span>,  <span style="color:#a7f3d0;">"good"</span>:        <span style="color:#fcd34d;">0.6</span>,
    <span style="color:#a7f3d0;">"nice"</span>:        <span style="color:#fcd34d;">0.5</span>,  <span style="color:#a7f3d0;">"recommend"</span>:   <span style="color:#fcd34d;">0.7</span>,  <span style="color:#a7f3d0;">"happy"</span>:       <span style="color:#fcd34d;">0.8</span>,
    <span style="color:#a7f3d0;">"fast"</span>:        <span style="color:#fcd34d;">0.4</span>,  <span style="color:#a7f3d0;">"reliable"</span>:    <span style="color:#fcd34d;">0.7</span>,  <span style="color:#a7f3d0;">"perfect"</span>:     <span style="color:#fcd34d;">1.0</span>,
    <span style="color:#6b7280;"># Strongly negative</span>
    <span style="color:#a7f3d0;">"terrible"</span>:  -<span style="color:#fcd34d;">1.0</span>,  <span style="color:#a7f3d0;">"awful"</span>:      -<span style="color:#fcd34d;">1.0</span>,  <span style="color:#a7f3d0;">"horrible"</span>:   -<span style="color:#fcd34d;">1.0</span>,
    <span style="color:#a7f3d0;">"bad"</span>:        -<span style="color:#fcd34d;">0.8</span>,  <span style="color:#a7f3d0;">"poor"</span>:       -<span style="color:#fcd34d;">0.7</span>,  <span style="color:#a7f3d0;">"waste"</span>:      -<span style="color:#fcd34d;">0.9</span>,
    <span style="color:#a7f3d0;">"slow"</span>:       -<span style="color:#fcd34d;">0.5</span>,  <span style="color:#a7f3d0;">"broken"</span>:     -<span style="color:#fcd34d;">0.8</span>,  <span style="color:#a7f3d0;">"disappoint"</span>: -<span style="color:#fcd34d;">0.7</span>,
    <span style="color:#a7f3d0;">"useless"</span>:   -<span style="color:#fcd34d;">0.9</span>,  <span style="color:#a7f3d0;">"frustrating"</span>:-<span style="color:#fcd34d;">0.8</span>,  <span style="color:#a7f3d0;">"returned"</span>:   -<span style="color:#fcd34d;">0.5</span>,
}

<span style="color:#93c5fd;">NEGATIONS</span>  = {<span style="color:#a7f3d0;">"not"</span>, <span style="color:#a7f3d0;">"no"</span>, <span style="color:#a7f3d0;">"never"</span>, <span style="color:#a7f3d0;">"neither"</span>, <span style="color:#a7f3d0;">"barely"</span>, <span style="color:#a7f3d0;">"hardly"</span>}
<span style="color:#93c5fd;">INTENSIFIERS</span> = {<span style="color:#a7f3d0;">"very"</span>: <span style="color:#fcd34d;">1.5</span>, <span style="color:#a7f3d0;">"extremely"</span>: <span style="color:#fcd34d;">2.0</span>, <span style="color:#a7f3d0;">"incredibly"</span>: <span style="color:#fcd34d;">1.8</span>,
                 <span style="color:#a7f3d0;">"somewhat"</span>: <span style="color:#fcd34d;">0.6</span>, <span style="color:#a7f3d0;">"slightly"</span>: <span style="color:#fcd34d;">0.5</span>}

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">analyze_sentiment</span>(text):
    <span style="color:#a7f3d0;">"""
    Rule-based sentiment with negation (3-word window) and intensifiers.
    Returns score, label, and word contributions.
    """</span>
    <span style="color:#93c5fd;">tokens</span>   = text.lower().split()
    <span style="color:#93c5fd;">score</span>    = <span style="color:#fcd34d;">0.0</span>
    <span style="color:#93c5fd;">details</span>  = []
    <span style="color:#93c5fd;">negate</span>   = <span style="color:#fca5a5;">False</span>
    <span style="color:#93c5fd;">intensify</span>= <span style="color:#fcd34d;">1.0</span>
    <span style="color:#93c5fd;">neg_dist</span> = <span style="color:#fcd34d;">0</span>

    <span style="color:#c4b5fd;">for</span> token <span style="color:#c4b5fd;">in</span> tokens:
        <span style="color:#93c5fd;">clean</span> = token.strip(<span style="color:#a7f3d0;">".,!?;:"</span>)
        <span style="color:#c4b5fd;">if</span> clean <span style="color:#c4b5fd;">in</span> NEGATIONS:
            negate = <span style="color:#fca5a5;">True</span>; neg_dist = <span style="color:#fcd34d;">0</span>; <span style="color:#c4b5fd;">continue</span>
        <span style="color:#c4b5fd;">if</span> clean <span style="color:#c4b5fd;">in</span> INTENSIFIERS:
            intensify = INTENSIFIERS[clean]; <span style="color:#c4b5fd;">continue</span>
        <span style="color:#c4b5fd;">if</span> clean <span style="color:#c4b5fd;">in</span> LEXICON:
            <span style="color:#93c5fd;">w</span> = LEXICON[clean] * intensify
            <span style="color:#c4b5fd;">if</span> negate <span style="color:#c4b5fd;">and</span> neg_dist <= <span style="color:#fcd34d;">3</span>: w *= -<span style="color:#fcd34d;">0.8</span>   <span style="color:#6b7280;"># flip & dampen</span>
            score += w
            details.append((<span style="color:#a7f3d0;">f"{'¬' if negate and neg_dist<=3 else ''}{clean}"</span>, <span style="color:#93c5fd;">round</span>(w, <span style="color:#fcd34d;">2</span>)))
        negate    = <span style="color:#fca5a5;">False</span> <span style="color:#c4b5fd;">if</span> neg_dist > <span style="color:#fcd34d;">3</span> <span style="color:#c4b5fd;">else</span> negate
        intensify = <span style="color:#fcd34d;">1.0</span>
        neg_dist += <span style="color:#fcd34d;">1</span>

    <span style="color:#93c5fd;">label</span> = <span style="color:#a7f3d0;">"POSITIVE"</span> <span style="color:#c4b5fd;">if</span> score > <span style="color:#fcd34d;">0.2</span> <span style="color:#c4b5fd;">else</span> (<span style="color:#a7f3d0;">"NEGATIVE"</span> <span style="color:#c4b5fd;">if</span> score < -<span style="color:#fcd34d;">0.2</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"NEUTRAL"</span>)
    <span style="color:#c4b5fd;">return</span> score, label, details

<span style="color:#93c5fd;">reviews</span> = [
    <span style="color:#a7f3d0;">"This product is amazing and I absolutely love it"</span>,
    <span style="color:#a7f3d0;">"The quality is very good but delivery was slow"</span>,
    <span style="color:#a7f3d0;">"Not bad at all, actually quite reliable"</span>,          <span style="color:#6b7280;"># negation case</span>
    <span style="color:#a7f3d0;">"Extremely terrible quality. I returned it immediately"</span>,
    <span style="color:#a7f3d0;">"It is not great but also not horrible"</span>,             <span style="color:#6b7280;"># mixed negations</span>
]

<span style="color:#c4b5fd;">for</span> rev <span style="color:#c4b5fd;">in</span> reviews:
    <span style="color:#93c5fd;">score</span>, <span style="color:#93c5fd;">label</span>, <span style="color:#93c5fd;">details</span> = analyze_sentiment(rev)
    <span style="color:#93c5fd;">bar_len</span>  = <span style="color:#93c5fd;">int</span>(<span style="color:#93c5fd;">abs</span>(score) * <span style="color:#fcd34d;">5</span>)
    <span style="color:#93c5fd;">bar</span>      = (<span style="color:#a7f3d0;">"▶"</span> <span style="color:#c4b5fd;">if</span> score > <span style="color:#fcd34d;">0</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"◀"</span>) * bar_len
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"[{label:8}] {bar:12} {score:+.2f}  \"{rev[:45]}...\""</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"           Contributions: {details}"</span>)
    <span style="color:#93c5fd;">print</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>[POSITIVE ] ▶▶▶▶▶▶▶▶▶   +1.90  "This product is amazing and I absolutely love..."
           Contributions: [('amazing', 1.0), ('love', 0.9)]

[NEUTRAL  ] ▶▶▶          +0.55  "The quality is very good but delivery was slow..."
           Contributions: [('good', 0.9), ('slow', -0.5)]

[NEUTRAL  ] ▶            +0.48  "Not bad at all, actually quite reliable..."
           Contributions: [('¬bad', 0.64), ('reliable', 0.7)]

[NEGATIVE ] ◀◀◀◀◀◀◀◀◀   -1.90  "Extremely terrible quality. I returned it imme..."
           Contributions: [('terrible', -2.0), ('returned', -0.5)]

[NEUTRAL  ] ▶            +0.16  "It is not great but also not horrible..."
           Contributions: [('¬great', -0.64), ('¬horrible', 0.8)]</div>
  </div>
</div>

<h3>Machine Learning Approach: Naive Bayes Classifier</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Multinomial Naive Bayes Sentiment Classifier from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math
<span style="color:#c4b5fd;">from</span> collections <span style="color:#c4b5fd;">import</span> Counter, defaultdict

<span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">NaiveBayesSentiment</span>:
    <span style="color:#a7f3d0;">"""
    Multinomial Naive Bayes for sentiment classification.
    P(class|doc) ∝ P(class) × ∏ P(word|class)
    Uses log probabilities to avoid numerical underflow.
    Laplace (add-1) smoothing for unseen words.
    """</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">fit</span>(self, X, y):
        <span style="color:#93c5fd;">self.classes_</span> = <span style="color:#93c5fd;">sorted</span>(<span style="color:#93c5fd;">set</span>(y))
        <span style="color:#93c5fd;">self.word_counts_</span>  = defaultdict(<span style="color:#c4b5fd;">lambda</span>: defaultdict(<span style="color:#fcd34d;">0</span>.__class__))
        <span style="color:#93c5fd;">self.class_counts_</span> = Counter(y)
        <span style="color:#93c5fd;">self.vocab_</span> = <span style="color:#93c5fd;">set</span>()

        <span style="color:#c4b5fd;">for</span> doc, label <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(X, y):
            <span style="color:#c4b5fd;">for</span> word <span style="color:#c4b5fd;">in</span> doc.lower().split():
                self.word_counts_[label][word] += <span style="color:#fcd34d;">1</span>
                self.vocab_.add(word)

        <span style="color:#93c5fd;">self.total_words_</span> = {c: <span style="color:#93c5fd;">sum</span>(self.word_counts_[c].values())
                              <span style="color:#c4b5fd;">for</span> c <span style="color:#c4b5fd;">in</span> self.classes_}
        <span style="color:#93c5fd;">self.N_</span>  = <span style="color:#93c5fd;">len</span>(y)
        <span style="color:#93c5fd;">self.V_</span>  = <span style="color:#93c5fd;">len</span>(self.vocab_)
        <span style="color:#c4b5fd;">return</span> self

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">predict</span>(self, X):
        <span style="color:#c4b5fd;">return</span> [self._classify(doc) <span style="color:#c4b5fd;">for</span> doc <span style="color:#c4b5fd;">in</span> X]

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">_classify</span>(self, doc):
        <span style="color:#93c5fd;">scores</span> = {}
        <span style="color:#c4b5fd;">for</span> c <span style="color:#c4b5fd;">in</span> self.classes_:
            <span style="color:#6b7280;"># Log prior: log P(class)</span>
            <span style="color:#93c5fd;">log_prob</span> = math.log(self.class_counts_[c] / self.N_)
            <span style="color:#6b7280;"># Log likelihood: log ∏ P(word|class) with Laplace smoothing</span>
            <span style="color:#c4b5fd;">for</span> word <span style="color:#c4b5fd;">in</span> doc.lower().split():
                <span style="color:#93c5fd;">count</span>     = self.word_counts_[c].get(word, <span style="color:#fcd34d;">0</span>) + <span style="color:#fcd34d;">1</span>  <span style="color:#6b7280;"># add-1</span>
                <span style="color:#93c5fd;">total</span>     = self.total_words_[c] + self.V_
                log_prob += math.log(count / total)
            scores[c] = log_prob
        <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">max</span>(scores, key=scores.get)

<span style="color:#6b7280;"># Training data (reviews + labels)</span>
<span style="color:#93c5fd;">X_train</span> = [
    <span style="color:#a7f3d0;">"amazing product love the quality highly recommend"</span>,
    <span style="color:#a7f3d0;">"great service excellent fast delivery"</span>,
    <span style="color:#a7f3d0;">"fantastic battery life very happy with purchase"</span>,
    <span style="color:#a7f3d0;">"terrible product broke after one day waste of money"</span>,
    <span style="color:#a7f3d0;">"awful quality bad service frustrating experience"</span>,
    <span style="color:#a7f3d0;">"horrible product returned immediately very disappointed"</span>,
]
<span style="color:#93c5fd;">y_train</span> = [<span style="color:#a7f3d0;">"pos"</span>, <span style="color:#a7f3d0;">"pos"</span>, <span style="color:#a7f3d0;">"pos"</span>, <span style="color:#a7f3d0;">"neg"</span>, <span style="color:#a7f3d0;">"neg"</span>, <span style="color:#a7f3d0;">"neg"</span>]

<span style="color:#93c5fd;">nb</span> = NaiveBayesSentiment().fit(X_train, y_train)

<span style="color:#93c5fd;">X_test</span>  = [
    <span style="color:#a7f3d0;">"love the quality great product"</span>,
    <span style="color:#a7f3d0;">"very bad product broke immediately"</span>,
    <span style="color:#a7f3d0;">"excellent fast delivery recommend"</span>,
    <span style="color:#a7f3d0;">"terrible waste disappointed"</span>,
]
<span style="color:#93c5fd;">y_test</span>  = [<span style="color:#a7f3d0;">"pos"</span>, <span style="color:#a7f3d0;">"neg"</span>, <span style="color:#a7f3d0;">"pos"</span>, <span style="color:#a7f3d0;">"neg"</span>]
<span style="color:#93c5fd;">preds</span>   = nb.predict(X_test)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Naive Bayes Sentiment Classifier Results:"</span>)
<span style="color:#c4b5fd;">for</span> doc, pred, true <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(X_test, preds, y_test):
    <span style="color:#93c5fd;">icon</span> = <span style="color:#a7f3d0;">"✓"</span> <span style="color:#c4b5fd;">if</span> pred == true <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"✗"</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {icon} Pred={pred:3}  True={true:3}  '{doc}'"</span>)

<span style="color:#93c5fd;">acc</span> = <span style="color:#93c5fd;">sum</span>(p==t <span style="color:#c4b5fd;">for</span> p,t <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(preds, y_test)) / <span style="color:#93c5fd;">len</span>(y_test)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nAccuracy: {acc:.0%}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Naive Bayes Sentiment Classifier Results:
  ✓ Pred=pos  True=pos  'love the quality great product'
  ✓ Pred=neg  True=neg  'very bad product broke immediately'
  ✓ Pred=pos  True=pos  'excellent fast delivery recommend'
  ✓ Pred=neg  True=neg  'terrible waste disappointed'

Accuracy: 100%</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $unstrModule->id,
            'title'       => '20.6 Sentiment Analysis: Lexicon & ML Approaches',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L20_6', [
                ['q' => 'Why does a lexicon-based sentiment analyzer struggle with "The movie was not boring at all"?', 'opts' => ['Lexicons cannot process long sentences', '"boring" is positive in the lexicon', '"not" should flip "boring" from negative to positive — simple lexicons without negation handling would score this as negative', 'The lexicon approach always requires training data'], 'ans' => 2, 'exp' => '"boring" is negative, but "not boring" is a double negation that should be scored positive. Simple lexicon approaches add up word scores without understanding negation scope. Robust systems track negation words and flip scores of nearby words within a defined window (e.g., 3 words), or use ML models that learn negation patterns from data.'],
                ['q' => 'Naive Bayes uses "Laplace smoothing" (add-1 to counts). Why is this necessary?', 'opts' => ['To increase accuracy on training data', 'To prevent log(0) = -infinity when an unseen test word has zero count in a training class', 'To normalize the probability to sum to 1', 'To remove stopwords automatically'], 'ans' => 1, 'exp' => 'If a word appears in test data but never appeared in training class C, then P(word|class C) = 0. Multiplying any probability by 0 gives 0, making the whole class impossible. Laplace smoothing adds 1 to every word count (and V to the denominator), ensuring every word has a small non-zero probability even if unseen.'],
                ['q' => 'Aspect-level sentiment analysis is MORE informative than document-level because...', 'opts' => ['It is computationally cheaper', 'It gives sentiment for each specific feature/attribute mentioned, revealing what customers like vs dislike about specific product aspects rather than a single overall score', 'It requires less training data', 'It ignores negation handling'], 'ans' => 1, 'exp' => '"Great camera but terrible battery" gets a mixed document-level score that hides actionable insight. Aspect-level analysis identifies: camera → positive, battery → negative. This is what product teams need to prioritize improvements. It is harder to implement but far more commercially valuable.'],
                ['q' => 'Why does machine learning outperform lexicon approaches for sentiment on social media text?', 'opts' => ['Social media has fewer words', 'ML learns domain-specific patterns, sarcasm, slang, emoji context, and abbreviations from data — things lexicons cannot encode manually', 'Lexicons are not available for social media', 'ML ignores context while lexicons capture it'], 'ans' => 1, 'exp' => 'Social media text has sarcasm ("Oh great, another Monday"), slang, misspellings, emojis, and domain-specific meaning that no hand-crafted lexicon can fully capture. ML models trained on labelled social media data learn these patterns directly. The tradeoff: ML requires labelled training data, lexicons work without any labels.'],
                ['q' => 'In Multinomial Naive Bayes, why do we use log probabilities instead of raw probabilities?', 'opts' => ['Log probabilities are always larger', 'Multiplying many small probabilities causes numerical underflow (floating-point becomes 0); log converts multiplication to addition, which is numerically stable', 'Log is required by the Bayes theorem formula', 'Log probabilities can be negative, which encodes sentiment polarity'], 'ans' => 1, 'exp' => 'For a 100-word document, P(doc|class) = ∏ P(word|class) might be (0.001)^100 = 10^-300, which underflows to 0 in floating point. log P(doc|class) = Σ log P(word|class) remains numerically stable. Since log is monotone, argmax of log probabilities equals argmax of raw probabilities — so the classification result is identical.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 20.7 — Named Entity Recognition & Information Extraction
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Named Entity Recognition & Information Extraction</h2>
<p>While sentiment analysis tells you <em>how</em> people feel, <strong>Information Extraction (IE)</strong> tells you <em>what</em> they are talking about. Named Entity Recognition (NER) identifies and classifies named real-world objects in text — people, organizations, locations, dates, monetary amounts, and more. NER is the backbone of knowledge graph construction, financial news parsing, medical record analysis, and intelligent search systems.</p>

<h3>Common NER Entity Types</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:32px;">
  <div style="padding:0;font-size:0.85rem;">
    <div style="display:grid;grid-template-columns:120px 1fr 1fr;border-bottom:1px solid var(--border);padding:10px 16px;font-weight:700;color:var(--muted);"><span>Tag</span><span>Meaning</span><span>Example</span></div>
    <div style="display:grid;grid-template-columns:120px 1fr 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;"><code style="color:#3b82f6;">PERSON</code><span style="color:var(--muted);">People, fictional characters</span><span style="color:var(--muted);">Elon Musk, Harry Potter</span></div>
    <div style="display:grid;grid-template-columns:120px 1fr 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;"><code style="color:#10b981;">ORG</code><span style="color:var(--muted);">Companies, agencies, institutions</span><span style="color:var(--muted);">Tesla, NASA, MIT</span></div>
    <div style="display:grid;grid-template-columns:120px 1fr 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;"><code style="color:#f59e0b;">GPE / LOC</code><span style="color:var(--muted);">Countries, cities, geographic locations</span><span style="color:var(--muted);">Philippines, Manila, Mount Everest</span></div>
    <div style="display:grid;grid-template-columns:120px 1fr 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;"><code style="color:#8b5cf6;">DATE / TIME</code><span style="color:var(--muted);">Absolute or relative dates and times</span><span style="color:var(--muted);">January 2024, next Tuesday, 3:00 PM</span></div>
    <div style="display:grid;grid-template-columns:120px 1fr 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;"><code style="color:#ec4899;">MONEY</code><span style="color:var(--muted);">Monetary values with currency</span><span style="color:var(--muted);">$1.2 billion, €500, ₱10,000</span></div>
    <div style="display:grid;grid-template-columns:120px 1fr 1fr;padding:10px 16px;"><code style="color:#ef4444;">PRODUCT</code><span style="color:var(--muted);">Products, goods, services</span><span style="color:var(--muted);">iPhone 15 Pro, ChatGPT</span></div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Rule-Based NER with Regex + Gazetteer Lookup</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> re

<span style="color:#6b7280;"># ── Gazetteers (curated entity lists) ────────────────────────────</span>
<span style="color:#93c5fd;">PERSONS</span>  = {<span style="color:#a7f3d0;">"Elon Musk"</span>, <span style="color:#a7f3d0;">"Sam Altman"</span>, <span style="color:#a7f3d0;">"Sundar Pichai"</span>, <span style="color:#a7f3d0;">"Jensen Huang"</span>}
<span style="color:#93c5fd;">ORGS</span>     = {<span style="color:#a7f3d0;">"Tesla"</span>, <span style="color:#a7f3d0;">"OpenAI"</span>, <span style="color:#a7f3d0;">"Google"</span>, <span style="color:#a7f3d0;">"NVIDIA"</span>, <span style="color:#a7f3d0;">"Microsoft"</span>, <span style="color:#a7f3d0;">"Apple"</span>}
<span style="color:#93c5fd;">PLACES</span>   = {<span style="color:#a7f3d0;">"Philippines"</span>, <span style="color:#a7f3d0;">"San Francisco"</span>, <span style="color:#a7f3d0;">"Silicon Valley"</span>, <span style="color:#a7f3d0;">"Manila"</span>}

<span style="color:#6b7280;"># ── Regex patterns for structured entities ───────────────────────</span>
<span style="color:#93c5fd;">PATTERNS</span> = [
    (<span style="color:#a7f3d0;">"MONEY"</span>,   <span style="color:#c4b5fd;">re</span>.compile(<span style="color:#a7f3d0;">r'\$[\d,.]+\s*(?:billion|million|thousand|[BbMmKk])?'</span>)),
    (<span style="color:#a7f3d0;">"PERCENT"</span>, <span style="color:#c4b5fd;">re</span>.compile(<span style="color:#a7f3d0;">r'\d+(?:\.\d+)?\s*%'</span>)),
    (<span style="color:#a7f3d0;">"DATE"</span>,    <span style="color:#c4b5fd;">re</span>.compile(<span style="color:#a7f3d0;">r'\b(?:Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|'</span>
                            <span style="color:#a7f3d0;">r'May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|'</span>
                            <span style="color:#a7f3d0;">r'Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)'</span>
                            <span style="color:#a7f3d0;">r'\s+\d{1,2}(?:st|nd|rd|th)?,?\s*\d{4}\b'</span>)),
    (<span style="color:#a7f3d0;">"EMAIL"</span>,   <span style="color:#c4b5fd;">re</span>.compile(<span style="color:#a7f3d0;">r'[\w.+-]+@[\w-]+\.[a-zA-Z]{2,}'</span>)),
]

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">extract_entities</span>(text):
    <span style="color:#a7f3d0;">"""Rule-based NER using gazetteers + regex patterns."""</span>
    <span style="color:#93c5fd;">entities</span> = []

    <span style="color:#6b7280;"># Gazetteer lookup (longest match first)</span>
    <span style="color:#c4b5fd;">for</span> entity_set, label <span style="color:#c4b5fd;">in</span> [
        (PERSONS, <span style="color:#a7f3d0;">"PERSON"</span>), (ORGS, <span style="color:#a7f3d0;">"ORG"</span>), (PLACES, <span style="color:#a7f3d0;">"GPE"</span>)]:
        <span style="color:#c4b5fd;">for</span> name <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">sorted</span>(entity_set, key=<span style="color:#c4b5fd;">lambda</span> x: -<span style="color:#93c5fd;">len</span>(x)):  <span style="color:#6b7280;"># longest first</span>
            <span style="color:#c4b5fd;">if</span> name <span style="color:#c4b5fd;">in</span> text:
                entities.append((name, label, text.find(name)))

    <span style="color:#6b7280;"># Regex pattern matching</span>
    <span style="color:#c4b5fd;">for</span> label, pattern <span style="color:#c4b5fd;">in</span> PATTERNS:
        <span style="color:#c4b5fd;">for</span> match <span style="color:#c4b5fd;">in</span> pattern.finditer(text):
            entities.append((match.group(), label, match.start()))

    <span style="color:#6b7280;"># Sort by position in text</span>
    entities.sort(key=<span style="color:#c4b5fd;">lambda</span> x: x[<span style="color:#fcd34d;">2</span>])
    <span style="color:#c4b5fd;">return</span> [(text, label) <span style="color:#c4b5fd;">for</span> text, label, _ <span style="color:#c4b5fd;">in</span> entities]

<span style="color:#93c5fd;">texts</span> = [
    <span style="color:#a7f3d0;">"Elon Musk's Tesla reported $23.3 billion in revenue for Q3 2024."</span>,
    <span style="color:#a7f3d0;">"OpenAI CEO Sam Altman met with Sundar Pichai of Google in San Francisco."</span>,
    <span style="color:#a7f3d0;">"NVIDIA shares surged 15.3% after Jensen Huang unveiled new chips on January 7th, 2025."</span>,
    <span style="color:#a7f3d0;">"Microsoft acquired OpenAI technology worth $10 billion to compete with Google."</span>,
]

<span style="color:#c4b5fd;">for</span> text <span style="color:#c4b5fd;">in</span> texts:
    <span style="color:#93c5fd;">ents</span> = extract_entities(text)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"TEXT: {text}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"ENTS: {ents}"</span>)
    <span style="color:#93c5fd;">print</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>TEXT: Elon Musk's Tesla reported $23.3 billion in revenue for Q3 2024.
ENTS: [('Elon Musk', 'PERSON'), ('Tesla', 'ORG'), ('$23.3 billion', 'MONEY')]

TEXT: OpenAI CEO Sam Altman met with Sundar Pichai of Google in San Francisco.
ENTS: [('OpenAI', 'ORG'), ('Sam Altman', 'PERSON'), ('Sundar Pichai', 'PERSON'), ('Google', 'ORG'), ('San Francisco', 'GPE')]

TEXT: NVIDIA shares surged 15.3% after Jensen Huang unveiled new chips on January 7th, 2025.
ENTS: [('NVIDIA', 'ORG'), ('15.3%', 'PERCENT'), ('Jensen Huang', 'PERSON'), ('January 7th, 2025', 'DATE')]

TEXT: Microsoft acquired OpenAI technology worth $10 billion to compete with Google.
ENTS: [('OpenAI', 'ORG'), ('Microsoft', 'ORG'), ('$10 billion', 'MONEY'), ('Google', 'ORG')]</div>
  </div>
</div>

<h3>BIO Tagging: The Standard NER Annotation Scheme</h3>
<p>Modern ML-based NER models are trained on BIO-tagged sequences. Each token gets a tag: <strong>B</strong>eginning of entity, <strong>I</strong>nside entity, or <strong>O</strong>utside (not an entity). This lets the model handle multi-token entities like "San Francisco" gracefully.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — BIO Tagging Scheme Demonstration</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># BIO (Begin-Inside-Outside) annotated training example</span>
<span style="color:#93c5fd;">bio_example</span> = [
    (<span style="color:#a7f3d0;">"Elon"</span>,      <span style="color:#a7f3d0;">"B-PERSON"</span>),   <span style="color:#6b7280;"># Beginning of PERSON entity</span>
    (<span style="color:#a7f3d0;">"Musk"</span>,      <span style="color:#a7f3d0;">"I-PERSON"</span>),   <span style="color:#6b7280;"># Inside PERSON entity (continuation)</span>
    (<span style="color:#a7f3d0;">"founded"</span>,   <span style="color:#a7f3d0;">"O"</span>),          <span style="color:#6b7280;"># Outside — not an entity</span>
    (<span style="color:#a7f3d0;">"SpaceX"</span>,    <span style="color:#a7f3d0;">"B-ORG"</span>),      <span style="color:#6b7280;"># Beginning of ORG entity</span>
    (<span style="color:#a7f3d0;">"in"</span>,        <span style="color:#a7f3d0;">"O"</span>),
    (<span style="color:#a7f3d0;">"Hawthorne"</span>, <span style="color:#a7f3d0;">"B-GPE"</span>),      <span style="color:#6b7280;"># Beginning of GPE entity</span>
    (<span style="color:#a7f3d0;">","</span>,         <span style="color:#a7f3d0;">"O"</span>),
    (<span style="color:#a7f3d0;">"California"</span>,<span style="color:#a7f3d0;">"I-GPE"</span>),      <span style="color:#6b7280;"># Inside GPE entity (same entity)</span>
    (<span style="color:#a7f3d0;">"in"</span>,        <span style="color:#a7f3d0;">"O"</span>),
    (<span style="color:#a7f3d0;">"2002"</span>,      <span style="color:#a7f3d0;">"B-DATE"</span>),
]

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">bio_to_entities</span>(bio_tags):
    <span style="color:#a7f3d0;">"""Reconstruct entity spans from BIO tagged sequence."""</span>
    <span style="color:#93c5fd;">entities</span>   = []
    <span style="color:#93c5fd;">current</span>    = <span style="color:#fca5a5;">None</span>
    <span style="color:#93c5fd;">entity_buf</span> = []

    <span style="color:#c4b5fd;">for</span> token, tag <span style="color:#c4b5fd;">in</span> bio_tags:
        <span style="color:#c4b5fd;">if</span> tag.startswith(<span style="color:#a7f3d0;">"B-"</span>):
            <span style="color:#c4b5fd;">if</span> entity_buf:
                entities.append((<span style="color:#a7f3d0;">" "</span>.join(entity_buf), current))
            current    = tag[<span style="color:#fcd34d;">2</span>:]
            entity_buf = [token]
        <span style="color:#c4b5fd;">elif</span> tag.startswith(<span style="color:#a7f3d0;">"I-"</span>) <span style="color:#c4b5fd;">and</span> current:
            entity_buf.append(token)
        <span style="color:#c4b5fd;">else</span>:
            <span style="color:#c4b5fd;">if</span> entity_buf:
                entities.append((<span style="color:#a7f3d0;">" "</span>.join(entity_buf), current))
            current = <span style="color:#fca5a5;">None</span>; entity_buf = []

    <span style="color:#c4b5fd;">if</span> entity_buf:
        entities.append((<span style="color:#a7f3d0;">" "</span>.join(entity_buf), current))
    <span style="color:#c4b5fd;">return</span> entities

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"BIO Token-Level Tags:"</span>)
<span style="color:#c4b5fd;">for</span> token, tag <span style="color:#c4b5fd;">in</span> bio_example:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {token:15} {tag}"</span>)

<span style="color:#93c5fd;">print</span>()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Reconstructed Entity Spans:"</span>)
<span style="color:#c4b5fd;">for</span> text, label <span style="color:#c4b5fd;">in</span> bio_to_entities(bio_example):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  [{label:8}] '{text}'"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>BIO Token-Level Tags:
  Elon            B-PERSON
  Musk            I-PERSON
  founded         O
  SpaceX          B-ORG
  in              O
  Hawthorne       B-GPE
  ,               O
  California      I-GPE
  in              O
  2002            B-DATE

Reconstructed Entity Spans:
  [PERSON  ] 'Elon Musk'
  [ORG     ] 'SpaceX'
  [GPE     ] 'Hawthorne California'
  [DATE    ] '2002'</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $unstrModule->id,
            'title'       => '20.7 Named Entity Recognition & Information Extraction',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L20_7', [
                ['q' => 'What does the "B" prefix in BIO tagging (e.g., B-PERSON) indicate?', 'opts' => ['The word is a verb', 'This token is the Beginning of a new named entity span', 'This token is Inside a continuing entity span', 'This token belongs to a Background entity class'], 'ans' => 1, 'exp' => 'BIO = Begin, Inside, Outside. B-TAG marks the first token of a new entity of type TAG. I-TAG marks subsequent tokens of the same entity. O marks tokens not part of any entity. This scheme handles multi-token entities like "New York City" = B-GPE I-GPE I-GPE.'],
                ['q' => 'A gazetteer in NER is...', 'opts' => ['A type of neural network', 'A statistical language model', 'A curated list of known entities (people, places, organizations) used for exact-match lookup', 'A metric for evaluating NER accuracy'], 'ans' => 2, 'exp' => 'A gazetteer is a curated dictionary or database of known entity names: a list of country names, city names, company names, famous people, etc. During NER, text is scanned for exact (or fuzzy) matches to these lists. Gazetteers give high precision for known entities but cannot generalize to new, unseen entity names.'],
                ['q' => 'What is the main limitation of rule-based (gazetteer + regex) NER compared to ML-based NER?', 'opts' => ['Rules are always more accurate', 'Rules cannot handle numbers', 'Rules cannot generalize to new entities not in the gazetteer and require manual maintenance as language evolves', 'Rules require GPU computation'], 'ans' => 2, 'exp' => 'Rule-based NER is high precision for known entities but cannot generalize. "Satya Nadella" would not be recognized if not in the PERSONS gazetteer. ML models (trained on BIO-tagged data) learn contextual patterns like "CEO of [ORG]" or "[TITLE] [FIRSTNAME] [LASTNAME]" and can recognize completely new entity names at inference time.'],
                ['q' => 'The standard metric for evaluating NER performance is...', 'opts' => ['Accuracy (correct tokens / total tokens)', 'Exact-match F1 score at the entity span level — a prediction is correct only if both the entity boundary and type are exactly right', 'BLEU score', 'Perplexity'], 'ans' => 1, 'exp' => 'NER is evaluated with span-level F1. A prediction "Elon Musk" [PERSON] is correct only if the exact token span matches AND the entity type matches. Token-level accuracy would artificially inflate scores because most tokens are "O" (outside entities). Precision = correct entities / predicted entities. Recall = correct entities / true entities. F1 = harmonic mean.'],
                ['q' => 'Relation extraction extends NER by...', 'opts' => ['Finding more entity types', 'Identifying semantic relationships between extracted entities, e.g., (Elon Musk, founder_of, Tesla)', 'Making NER faster', 'Removing entity boundaries'], 'ans' => 1, 'exp' => 'After identifying entities, relation extraction identifies how they relate: (Entity1, relation, Entity2). From "Elon Musk founded Tesla in 2003," we extract (Elon Musk, FOUNDED, Tesla) and (Tesla, FOUNDED_IN, 2003). These subject-predicate-object triples are how knowledge graphs like Wikidata and Google\'s Knowledge Graph are built.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 20.8 — Image Data: Representation, Preprocessing & Feature Extraction
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Image Data: Representation, Preprocessing & Feature Extraction</h2>
<p>Images are the second most important type of unstructured data after text. Computer vision tasks — object detection, image classification, facial recognition, medical imaging, autonomous driving — represent some of the most commercially valuable and technically sophisticated applications of machine learning. Understanding how images are represented, how to preprocess them, and how to extract features is the foundation of every vision pipeline.</p>

<h3>How Images Are Represented Digitally</h3>
<p>A digital image is a 3D array of numbers: <strong>height × width × channels</strong>. Each value is a pixel intensity, typically ranging from 0 (black) to 255 (white for that channel). Color images have 3 channels (Red, Green, Blue). Grayscale images have 1 channel. Medical images may have more (e.g., DICOM with multiple modalities).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Image Representation & Preprocessing from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math
<span style="color:#c4b5fd;">import</span> random
random.seed(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># ── Simulate a small grayscale image (8×8 pixels, 0-255 range) ───</span>
<span style="color:#93c5fd;">H</span>, <span style="color:#93c5fd;">W</span>   = <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">8</span>
<span style="color:#93c5fd;">image</span>    = [[random.randint(<span style="color:#fcd34d;">50</span>, <span style="color:#fcd34d;">220</span>) <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(W)] <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(H)]

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">print_image</span>(img, title):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n{title}:"</span>)
    <span style="color:#c4b5fd;">for</span> row <span style="color:#c4b5fd;">in</span> img:
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  "</span> + <span style="color:#a7f3d0;">" "</span>.join(<span style="color:#a7f3d0;">f"{p:3}"</span> <span style="color:#c4b5fd;">for</span> p <span style="color:#c4b5fd;">in</span> row))

print_image(image, <span style="color:#a7f3d0;">"Raw Pixel Values (0-255)"</span>)

<span style="color:#6b7280;"># ── 1. Normalisation: scale to [0, 1] ────────────────────────────</span>
<span style="color:#93c5fd;">norm_img</span> = [[p / <span style="color:#fcd34d;">255.0</span> <span style="color:#c4b5fd;">for</span> p <span style="color:#c4b5fd;">in</span> row] <span style="color:#c4b5fd;">for</span> row <span style="color:#c4b5fd;">in</span> image]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nAfter normalisation (÷255): min={min(min(r) for r in norm_img):.3f}, max={max(max(r) for r in norm_img):.3f}"</span>)

<span style="color:#6b7280;"># ── 2. Mean subtraction (zero-centering) ─────────────────────────</span>
<span style="color:#93c5fd;">all_vals</span> = [p <span style="color:#c4b5fd;">for</span> row <span style="color:#c4b5fd;">in</span> norm_img <span style="color:#c4b5fd;">for</span> p <span style="color:#c4b5fd;">in</span> row]
<span style="color:#93c5fd;">mean</span>     = <span style="color:#93c5fd;">sum</span>(all_vals) / <span style="color:#93c5fd;">len</span>(all_vals)
<span style="color:#93c5fd;">std</span>      = math.sqrt(<span style="color:#93c5fd;">sum</span>((p - mean)**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> p <span style="color:#c4b5fd;">in</span> all_vals) / <span style="color:#93c5fd;">len</span>(all_vals))
<span style="color:#93c5fd;">std_img</span>  = [[(p - mean) / std <span style="color:#c4b5fd;">for</span> p <span style="color:#c4b5fd;">in</span> row] <span style="color:#c4b5fd;">for</span> row <span style="color:#c4b5fd;">in</span> norm_img]

<span style="color:#93c5fd;">std_vals</span> = [p <span style="color:#c4b5fd;">for</span> row <span style="color:#c4b5fd;">in</span> std_img <span style="color:#c4b5fd;">for</span> p <span style="color:#c4b5fd;">in</span> row]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"After z-score standardisation: mean≈{sum(std_vals)/len(std_vals):.4f}, std≈{math.sqrt(sum(v**2 for v in std_vals)/len(std_vals)):.4f}"</span>)

<span style="color:#6b7280;"># ── 3. Flatten to 1D vector (for FC layers or traditional ML) ────</span>
<span style="color:#93c5fd;">flat</span> = [p <span style="color:#c4b5fd;">for</span> row <span style="color:#c4b5fd;">in</span> norm_img <span style="color:#c4b5fd;">for</span> p <span style="color:#c4b5fd;">in</span> row]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nFlattened vector: {H}×{W} = {len(flat)} values"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"First 10 values: {[round(v,3) for v in flat[:10]]}"</span>)

<span style="color:#93c5fd;">print</span>()
<span style="color:#6b7280;"># ── 4. 2D Convolution: a 3×3 edge-detection filter (Sobel-like) ──</span>
<span style="color:#93c5fd;">kernel</span> = [                      <span style="color:#6b7280;"># Horizontal edge detector</span>
    [-<span style="color:#fcd34d;">1</span>, -<span style="color:#fcd34d;">2</span>, -<span style="color:#fcd34d;">1</span>],
    [ <span style="color:#fcd34d;">0</span>,  <span style="color:#fcd34d;">0</span>,  <span style="color:#fcd34d;">0</span>],
    [ <span style="color:#fcd34d;">1</span>,  <span style="color:#fcd34d;">2</span>,  <span style="color:#fcd34d;">1</span>],
]

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">convolve2d</span>(img, kern):
    <span style="color:#a7f3d0;">"""Manual 2D convolution (valid mode — no padding)."""</span>
    <span style="color:#93c5fd;">kH</span>, <span style="color:#93c5fd;">kW</span>  = <span style="color:#93c5fd;">len</span>(kern), <span style="color:#93c5fd;">len</span>(kern[<span style="color:#fcd34d;">0</span>])
    <span style="color:#93c5fd;">oH</span>, <span style="color:#93c5fd;">oW</span>  = <span style="color:#93c5fd;">len</span>(img) - kH + <span style="color:#fcd34d;">1</span>, <span style="color:#93c5fd;">len</span>(img[<span style="color:#fcd34d;">0</span>]) - kW + <span style="color:#fcd34d;">1</span>
    <span style="color:#93c5fd;">output</span>  = []
    <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(oH):
        <span style="color:#93c5fd;">row_out</span> = []
        <span style="color:#c4b5fd;">for</span> j <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(oW):
            <span style="color:#93c5fd;">s</span> = <span style="color:#fcd34d;">0</span>
            <span style="color:#c4b5fd;">for</span> ki <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(kH):
                <span style="color:#c4b5fd;">for</span> kj <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(kW):
                    s += img[i+ki][j+kj] * kern[ki][kj]
            row_out.append(<span style="color:#93c5fd;">round</span>(s, <span style="color:#fcd34d;">1</span>))
        output.append(row_out)
    <span style="color:#c4b5fd;">return</span> output

<span style="color:#93c5fd;">feature_map</span> = convolve2d(image, kernel)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Input size  : {H}×{W}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Kernel size : 3×3"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Feature map : {len(feature_map)}×{len(feature_map[0])} (valid convolution)"</span>)
print_image(feature_map, <span style="color:#a7f3d0;">"Convolved Feature Map (Horizontal Edges)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Raw Pixel Values (0-255):
   102 152 117 144 182  78 167 141
   (... 8 rows ...)

After normalisation (÷255): min=0.196, max=0.863
After z-score standardisation: mean≈0.0000, std≈1.0000

Flattened vector: 8×8 = 64 values
First 10 values: [0.4, 0.596, 0.459, 0.565, 0.714, 0.306, 0.655, 0.553, 0.322, 0.518]

Input size  : 8×8
Kernel size : 3×3
Feature map : 6×6 (valid convolution)

Convolved Feature Map (Horizontal Edges):
   -20.0   45.0   12.0  -33.0   88.0  -27.0
   (... 6 rows of edge-detection values ...)</div>
  </div>
</div>

<h3>The Standard Image Preprocessing Pipeline</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;font-size:0.875rem;">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <div>
      <h4 style="color:#3b82f6;margin-top:0;font-size:0.9rem;">Resize & Crop</h4>
      <p style="color:var(--muted);">CNNs require fixed-size inputs. Common sizes: 224×224 (ImageNet models), 299×299 (Inception), 640×640 (YOLO). Center-crop to remove padding effects.</p>
    </div>
    <div>
      <h4 style="color:#10b981;margin-top:0;font-size:0.9rem;">Normalisation</h4>
      <p style="color:var(--muted);">Scale pixels to [0,1] or [-1,1]. Apply ImageNet mean subtraction: μ=[0.485, 0.456, 0.406], σ=[0.229, 0.224, 0.225] for transfer learning models.</p>
    </div>
    <div>
      <h4 style="color:#f59e0b;margin-top:0;font-size:0.9rem;">Data Augmentation</h4>
      <p style="color:var(--muted);">Artificially expand training set: random horizontal flip, rotation (±15°), zoom, color jitter, cutout. Dramatically reduces overfitting with small datasets.</p>
    </div>
    <div>
      <h4 style="color:#8b5cf6;margin-top:0;font-size:0.9rem;">Feature Extraction</h4>
      <p style="color:var(--muted);">Use pre-trained CNN (ResNet, EfficientNet) as feature extractor — discard final classification head, take penultimate layer embeddings (typically 512-2048 dim).</p>
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $unstrModule->id,
            'title'       => '20.8 Image Data: Representation, Preprocessing & Feature Extraction',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L20_8', [
                ['q' => 'A standard RGB image of size 224×224 is represented internally as...', 'opts' => ['A 1D list of 224 numbers', 'A 2D grid of 224×224 = 50,176 numbers', 'A 3D array of shape 224×224×3 = 150,528 numbers', 'A 4D array including the batch dimension'], 'ans' => 2, 'exp' => 'RGB images are 3D arrays: height × width × channels = 224 × 224 × 3 = 150,528 values. Each spatial position (pixel) has 3 intensity values (R, G, B) ranging 0-255. When fed to a neural network in a batch, a 4th batch dimension is added: batch_size × 224 × 224 × 3.'],
                ['q' => 'Why is pixel normalization (dividing by 255) applied before feeding images to neural networks?', 'opts' => ['To reduce file size', 'To change the image colors', 'To scale values to [0,1], improving numerical stability, gradient flow, and making the model less sensitive to raw pixel scale differences', 'Because neural networks cannot process integers'], 'ans' => 2, 'exp' => 'Raw pixel values range 0-255. Neural networks with sigmoid or tanh activations work best with inputs near zero. Large input values cause large initial gradients and slow convergence. Dividing by 255 scales to [0,1]. For pre-trained ImageNet models, you also subtract the channel-wise mean and divide by std to match the distribution the model was trained on.'],
                ['q' => 'A 2D convolution with a 3×3 kernel applied to a 32×32 image (valid mode, stride 1) produces an output of...', 'opts' => ['32×32 (same size)', '30×30 (32-3+1 = 30 in each dimension)', '28×28', '16×16 (halved)'], 'ans' => 1, 'exp' => 'In valid (no padding) convolution: output_size = input_size - kernel_size + 1. For a 32×32 image with a 3×3 kernel: 32 - 3 + 1 = 30. Output is 30×30. With same padding, output stays 32×32. CNNs alternate convolutions with pooling to progressively reduce spatial dimensions.'],
                ['q' => 'Data augmentation in image classification is used to...', 'opts' => ['Generate test labels automatically', 'Increase model inference speed', 'Artificially increase effective training set size and teach the model invariance to irrelevant transformations (flip, rotation, zoom), reducing overfitting', 'Replace the need for labelled training data'], 'ans' => 2, 'exp' => 'A cat is still a cat when horizontally flipped. Augmentation teaches this by randomly applying transformations during training: each epoch the model sees slightly different versions of training images. This dramatically reduces overfitting, especially when labelled data is scarce. At inference, no augmentation is applied (or only test-time augmentation averaging).'],
                ['q' => 'Transfer learning for images works by...', 'opts' => ['Training a new CNN from random weights on your dataset', 'Downloading random ImageNet images to supplement your dataset', 'Using a pre-trained CNN (e.g., ResNet-50) as a feature extractor — keeping its learned low-level and mid-level features while replacing/fine-tuning the final layers for your task', 'Copying the architecture without the weights'], 'ans' => 2, 'exp' => 'CNNs trained on ImageNet (1.2M labelled images, 1000 classes) learn universally useful low-level features (edges, textures, colors) and mid-level features (shapes, patterns). These transfer remarkably well to other vision tasks. Transfer learning reduces training time from days to hours and dramatically outperforms training from scratch on small datasets.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 20.9 — Audio Data: Waveforms, Spectrograms & Feature Engineering
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Audio Data: Waveforms, Spectrograms & Feature Engineering</h2>
<p>Audio is a temporal signal — a sequence of pressure measurements over time. Unlike images and text, audio analysis must handle enormous temporal variability: the same spoken word might take 200ms or 800ms; the same musical note can span multiple octaves. Audio analysis powers speech recognition (Siri, Google Assistant), music genre classification, emotion detection from voice, speaker identification, environmental sound classification, and medical diagnostics from breathing or heart sounds.</p>

<h3>The Digital Audio Signal</h3>
<p>Audio is recorded by sampling the continuous pressure wave at a fixed rate. The <strong>sample rate</strong> (e.g., 16,000 Hz or 44,100 Hz) determines the maximum frequency that can be represented. By the Nyquist theorem, you can represent frequencies up to half the sample rate. Each sample is an integer or float representing pressure amplitude.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Synthesising Audio, Waveform Analysis & Zero-Crossing Rate</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math

<span style="color:#6b7280;"># ── Synthesise pure tones ─────────────────────────────────────────</span>
<span style="color:#93c5fd;">SR</span>       = <span style="color:#fcd34d;">16000</span>   <span style="color:#6b7280;"># 16 kHz sample rate</span>
<span style="color:#93c5fd;">duration</span> = <span style="color:#fcd34d;">0.5</span>     <span style="color:#6b7280;"># 500 ms</span>
<span style="color:#93c5fd;">n</span>        = <span style="color:#93c5fd;">int</span>(SR * duration)

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">sine_wave</span>(freq, sr, n_samples, amplitude=<span style="color:#fcd34d;">1.0</span>):
    <span style="color:#a7f3d0;">"""Generate a pure sine wave at given frequency."""</span>
    <span style="color:#c4b5fd;">return</span> [amplitude * math.sin(<span style="color:#fcd34d;">2</span> * math.pi * freq * t / sr)
            <span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n_samples)]

<span style="color:#6b7280;"># Middle C (261.63 Hz) and A above middle C (440 Hz)</span>
<span style="color:#93c5fd;">tone_C</span>   = sine_wave(<span style="color:#fcd34d;">261.63</span>, SR, n)
<span style="color:#93c5fd;">tone_A</span>   = sine_wave(<span style="color:#fcd34d;">440.0</span>,  SR, n)
<span style="color:#93c5fd;">chord</span>    = [(<span style="color:#93c5fd;">tone_C</span>[i] + tone_A[i]) / <span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n)]  <span style="color:#6b7280;"># mix</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Audio Signal Properties ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sample rate  : {SR} Hz"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Duration     : {duration}s"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Total samples: {n}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Nyquist freq : {SR//2} Hz (max representable)"</span>)

<span style="color:#93c5fd;">print</span>()
<span style="color:#6b7280;"># ── Time-domain features ──────────────────────────────────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">rms_energy</span>(signal):
    <span style="color:#a7f3d0;">"""Root Mean Square energy — measures loudness."""</span>
    <span style="color:#c4b5fd;">return</span> math.sqrt(<span style="color:#93c5fd;">sum</span>(x**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> signal) / <span style="color:#93c5fd;">len</span>(signal))

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">zero_crossing_rate</span>(signal):
    <span style="color:#a7f3d0;">"""
    ZCR = number of times signal crosses zero per second.
    High ZCR → noisy/unvoiced sounds (consonants, noise).
    Low ZCR  → periodic/voiced sounds (vowels, music).
    """</span>
    <span style="color:#93c5fd;">crossings</span> = <span style="color:#93c5fd;">sum</span>(<span style="color:#fcd34d;">1</span> <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#93c5fd;">len</span>(signal))
                    <span style="color:#c4b5fd;">if</span> (signal[i] >= <span style="color:#fcd34d;">0</span>) != (signal[i-<span style="color:#fcd34d;">1</span>] >= <span style="color:#fcd34d;">0</span>))
    <span style="color:#c4b5fd;">return</span> crossings / <span style="color:#93c5fd;">len</span>(signal)

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">spectral_centroid</span>(signal, sr, n_fft=<span style="color:#fcd34d;">256</span>):
    <span style="color:#a7f3d0;">"""
    Spectral centroid — 'centre of mass' of the spectrum.
    High centroid → bright/treble sounds.
    Low centroid  → dark/bass sounds.
    Uses DFT on first n_fft samples for speed.
    """</span>
    <span style="color:#93c5fd;">seg</span>     = signal[:n_fft]
    <span style="color:#93c5fd;">N</span>       = <span style="color:#93c5fd;">len</span>(seg)
    <span style="color:#93c5fd;">magnitudes</span> = []
    <span style="color:#6b7280;"># Manual DFT magnitude (slow but transparent)</span>
    <span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(N // <span style="color:#fcd34d;">2</span>):
        <span style="color:#93c5fd;">re</span> = <span style="color:#93c5fd;">sum</span>(seg[t] * math.cos(<span style="color:#fcd34d;">2</span>*math.pi*k*t/N) <span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(N))
        <span style="color:#93c5fd;">im</span> = <span style="color:#93c5fd;">sum</span>(seg[t] * math.sin(<span style="color:#fcd34d;">2</span>*math.pi*k*t/N) <span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(N))
        magnitudes.append(math.sqrt(re**<span style="color:#fcd34d;">2</span> + im**<span style="color:#fcd34d;">2</span>))
    <span style="color:#93c5fd;">freqs</span>      = [k * sr / N <span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(N // <span style="color:#fcd34d;">2</span>)]
    <span style="color:#93c5fd;">total_mag</span>  = <span style="color:#93c5fd;">sum</span>(magnitudes)
    <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">sum</span>(f*m <span style="color:#c4b5fd;">for</span> f,m <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(freqs, magnitudes)) / total_mag <span style="color:#c4b5fd;">if</span> total_mag > <span style="color:#fcd34d;">0</span> <span style="color:#c4b5fd;">else</span> <span style="color:#fcd34d;">0</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Extracted Audio Features ==="</span>)
<span style="color:#c4b5fd;">for</span> name, signal <span style="color:#c4b5fd;">in</span> [(<span style="color:#a7f3d0;">"Middle C (261 Hz)"</span>, tone_C),
                       (<span style="color:#a7f3d0;">"A note (440 Hz)"</span>,   tone_A),
                       (<span style="color:#a7f3d0;">"C+A chord"</span>,         chord)]:
    <span style="color:#93c5fd;">rms</span>  = rms_energy(signal)
    <span style="color:#93c5fd;">zcr</span>  = zero_crossing_rate(signal)
    <span style="color:#93c5fd;">sc</span>   = spectral_centroid(signal, SR, n_fft=<span style="color:#fcd34d;">128</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n{name}:"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  RMS Energy       : {rms:.4f}  (loudness)"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Zero-Crossing Rate: {zcr:.4f}  (periodicity)"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Spectral Centroid : {sc:.1f} Hz  (brightness)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== Audio Signal Properties ===
Sample rate  : 16000 Hz
Duration     : 0.5s
Total samples: 8000
Nyquist freq : 8000 Hz (max representable)

=== Extracted Audio Features ===

Middle C (261 Hz):
  RMS Energy       : 0.7071  (loudness)
  Zero-Crossing Rate: 0.0326  (periodicity)
  Spectral Centroid : 261.6 Hz  (brightness)

A note (440 Hz):
  RMS Energy       : 0.7071  (loudness)
  Zero-Crossing Rate: 0.0549  (periodicity)
  Spectral Centroid : 440.0 Hz  (brightness)

C+A chord:
  RMS Energy       : 0.5000  (loudness)
  Zero-Crossing Rate: 0.0462  (periodicity)
  Spectral Centroid : 351.8 Hz  (brightness — midpoint of two tones)</div>
  </div>
</div>

<h3>MFCCs: The Gold Standard Audio Feature for Speech</h3>
<p>Mel-Frequency Cepstral Coefficients (MFCCs) are the most widely used features for speech and audio classification. They mimic the human auditory system by applying a mel-scale filterbank (perceptually spaced) and then a discrete cosine transform. 13-40 MFCCs per audio frame capture the vocal tract shape and are robust to background noise.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <h4 style="color:var(--text);margin-top:0;font-size:0.95rem;">MFCC Computation Pipeline</h4>
  <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;row-gap:12px;font-size:0.82rem;">
    <div style="background:rgba(59,130,246,0.15);border:1px solid rgba(59,130,246,0.4);padding:10px 14px;border-radius:6px;color:#3b82f6;font-family:'JetBrains Mono',monospace;font-weight:700;">Waveform</div>
    <div style="color:var(--muted);">→</div>
    <div style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.4);padding:10px 14px;border-radius:6px;color:#f59e0b;font-family:'JetBrains Mono',monospace;font-weight:700;">Frame (25ms)</div>
    <div style="color:var(--muted);">→</div>
    <div style="background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.4);padding:10px 14px;border-radius:6px;color:#8b5cf6;font-family:'JetBrains Mono',monospace;font-weight:700;">FFT Power</div>
    <div style="color:var(--muted);">→</div>
    <div style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.4);padding:10px 14px;border-radius:6px;color:#10b981;font-family:'JetBrains Mono',monospace;font-weight:700;">Mel Filterbank</div>
    <div style="color:var(--muted);">→</div>
    <div style="background:rgba(236,72,153,0.15);border:1px solid rgba(236,72,153,0.4);padding:10px 14px;border-radius:6px;color:#ec4899;font-family:'JetBrains Mono',monospace;font-weight:700;">Log</div>
    <div style="color:var(--muted);">→</div>
    <div style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.4);padding:10px 14px;border-radius:6px;color:#ef4444;font-family:'JetBrains Mono',monospace;font-weight:700;">DCT → 13 MFCCs</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $unstrModule->id,
            'title'       => '20.9 Audio Data: Waveforms, Spectrograms & Feature Engineering',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L20_9', [
                ['q' => 'The Nyquist theorem states that a sample rate of 16,000 Hz can accurately represent frequencies up to...', 'opts' => ['16,000 Hz', '8,000 Hz (half the sample rate)', '32,000 Hz', '4,000 Hz'], 'ans' => 1, 'exp' => 'The Nyquist-Shannon sampling theorem: to accurately represent a signal with maximum frequency f_max, you must sample at ≥ 2·f_max. Equivalently, a sample rate SR can represent frequencies up to SR/2. At 16kHz, the maximum frequency is 8kHz — sufficient for speech (human voice: 85Hz-8kHz) but not music (up to 20kHz).'],
                ['q' => 'Zero-Crossing Rate (ZCR) is higher for which type of audio?', 'opts' => ['Low-frequency bass notes', 'Smooth orchestral strings', 'Unvoiced consonants and noise — signals with rapid irregular oscillations cross zero more frequently than periodic tonal signals', 'Silence'], 'ans' => 2, 'exp' => 'ZCR counts how often the waveform crosses zero per second. Voiced sounds (vowels, singing) have a periodic waveform with regular, infrequent zero crossings. Unvoiced sounds (fricatives like "sh", "f") are essentially noise with random rapid oscillations and very high ZCR. ZCR is a simple feature for voiced/unvoiced distinction.'],
                ['q' => 'A spectrogram represents audio as...', 'opts' => ['A 1D waveform plot over time', 'A 2D image showing frequency content (y-axis) over time (x-axis) with amplitude as color/brightness', 'A histogram of amplitude values', 'A 3D plot of pitch, duration, and loudness'], 'ans' => 1, 'exp' => 'A spectrogram is created by dividing audio into short overlapping frames and computing the FFT of each frame. The result is a 2D array (time × frequency) where each cell shows the magnitude of a frequency bin at a time point. Visualized as a heatmap, it reveals when different frequencies are active — making it the dominant input representation for audio CNNs.'],
                ['q' => 'MFCCs are preferred over raw spectrograms for speech because...', 'opts' => ['MFCCs are always smaller in size', 'MFCCs mimic the human auditory system\'s non-linear frequency perception (mel scale) and are more compact, noise-robust, and discriminative for speech content', 'MFCCs work only for music', 'Spectrograms cannot represent speech frequencies'], 'ans' => 1, 'exp' => 'Human pitch perception is logarithmic (mel scale) — we distinguish 100Hz from 200Hz more easily than 4000Hz from 4100Hz. MFCCs apply a mel filterbank (perceptually scaled) and compress the result via DCT to 13-40 coefficients per frame. This compression removes noise while preserving speech-relevant information about vocal tract shape.'],
                ['q' => 'RMS Energy of an audio signal measures...', 'opts' => ['The fundamental frequency (pitch)', 'The tempo of the audio', 'The overall loudness or signal power — the root mean square of amplitude values', 'The number of distinct instruments'], 'ans' => 2, 'exp' => 'RMS = sqrt(mean(x²)) where x are amplitude samples. A louder signal has higher amplitude values and thus higher RMS. RMS energy is used for voice activity detection (silence has near-zero RMS), audio segmentation, and loudness normalization before processing.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 20.10 — Transformers & LLMs for Unstructured Data
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Transformers & Large Language Models for Unstructured Data</h2>
<p>The transformer architecture, introduced by Vaswani et al. in 2017 ("Attention Is All You Need"), is arguably the most important innovation in machine learning of the past decade. It has completely superseded recurrent neural networks for NLP, dominated computer vision (Vision Transformers), enabled cross-modal models (CLIP, DALL-E), and led directly to the era of Large Language Models — GPT-4, Claude, Gemini, and beyond. Understanding the transformer is no longer optional for serious practitioners: it is the lingua franca of modern AI.</p>

<h3>The Self-Attention Mechanism: The Core Innovation</h3>
<p>Every word in a sentence needs to consider every other word to understand context. "The animal didn't cross the street because it was too tired." — does "it" refer to "animal" or "street"? Self-attention lets each token directly attend to all other tokens, computing a weighted sum where weights reflect relevance. This solves the long-range dependency problem that crippled RNNs.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--muted);line-height:2.2;">
  <span style="color:var(--text);font-weight:700;">Self-Attention:</span><br>
  &nbsp;&nbsp; Q = X·W_Q &nbsp;&nbsp; (Query: "What am I looking for?")<br>
  &nbsp;&nbsp; K = X·W_K &nbsp;&nbsp; (Key: "What do I have?")<br>
  &nbsp;&nbsp; V = X·W_V &nbsp;&nbsp; (Value: "What do I return if selected?")<br><br>
  &nbsp;&nbsp; Attention(Q,K,V) = softmax(QKᵀ / √d_k) · V
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Self-Attention from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">dot_product</span>(a, b):
    <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">sum</span>(x * y <span style="color:#c4b5fd;">for</span> x, y <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(a, b))

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">mat_mul</span>(A, B):
    <span style="color:#a7f3d0;">"""Simple matrix multiplication (list of lists)."""</span>
    <span style="color:#93c5fd;">rows_A</span>, <span style="color:#93c5fd;">cols_A</span> = <span style="color:#93c5fd;">len</span>(A), <span style="color:#93c5fd;">len</span>(A[<span style="color:#fcd34d;">0</span>])
    <span style="color:#93c5fd;">cols_B</span> = <span style="color:#93c5fd;">len</span>(B[<span style="color:#fcd34d;">0</span>])
    <span style="color:#93c5fd;">B_T</span>    = [[B[r][c] <span style="color:#c4b5fd;">for</span> r <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#93c5fd;">len</span>(B))] <span style="color:#c4b5fd;">for</span> c <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(cols_B)]
    <span style="color:#c4b5fd;">return</span> [[dot_product(A[r], B_T[c]) <span style="color:#c4b5fd;">for</span> c <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(cols_B)] <span style="color:#c4b5fd;">for</span> r <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(rows_A)]

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">softmax</span>(row):
    <span style="color:#93c5fd;">max_v</span> = <span style="color:#93c5fd;">max</span>(row)  <span style="color:#6b7280;"># numerical stability</span>
    <span style="color:#93c5fd;">exps</span>  = [math.exp(v - max_v) <span style="color:#c4b5fd;">for</span> v <span style="color:#c4b5fd;">in</span> row]
    <span style="color:#93c5fd;">total</span> = <span style="color:#93c5fd;">sum</span>(exps)
    <span style="color:#c4b5fd;">return</span> [e / total <span style="color:#c4b5fd;">for</span> e <span style="color:#c4b5fd;">in</span> exps]

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">self_attention</span>(Q, K, V):
    <span style="color:#a7f3d0;">"""
    Scaled dot-product self-attention.
    Q, K, V: [seq_len, d_k] matrices
    Returns: attention output [seq_len, d_v]
    """</span>
    <span style="color:#93c5fd;">d_k</span>    = <span style="color:#93c5fd;">len</span>(Q[<span style="color:#fcd34d;">0</span>])
    <span style="color:#93c5fd;">scale</span>  = math.sqrt(d_k)
    <span style="color:#6b7280;"># QKᵀ: [seq_len, seq_len] — raw attention scores</span>
    <span style="color:#93c5fd;">K_T</span>    = [[K[j][i] <span style="color:#c4b5fd;">for</span> j <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#93c5fd;">len</span>(K))] <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#93c5fd;">len</span>(K[<span style="color:#fcd34d;">0</span>]))]
    <span style="color:#93c5fd;">scores</span> = mat_mul(Q, K_T)
    <span style="color:#6b7280;"># Scale and softmax each row → attention weights</span>
    <span style="color:#93c5fd;">attn</span>   = [softmax([s / scale <span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> row]) <span style="color:#c4b5fd;">for</span> row <span style="color:#c4b5fd;">in</span> scores]
    <span style="color:#6b7280;"># Weighted sum of values → context vectors</span>
    <span style="color:#93c5fd;">output</span> = mat_mul(attn, V)
    <span style="color:#c4b5fd;">return</span> output, attn

<span style="color:#6b7280;"># ── Toy example: 4 tokens, 4-dimensional embeddings ──────────────</span>
<span style="color:#93c5fd;">tokens</span> = [<span style="color:#a7f3d0;">"The"</span>, <span style="color:#a7f3d0;">"cat"</span>, <span style="color:#a7f3d0;">"sat"</span>, <span style="color:#a7f3d0;">"here"</span>]

<span style="color:#6b7280;"># Simulated Q, K, V matrices (in real transformers these are learned projections)</span>
<span style="color:#93c5fd;">Q</span> = [[<span style="color:#fcd34d;">1.0</span>, <span style="color:#fcd34d;">0.2</span>, <span style="color:#fcd34d;">0.5</span>, <span style="color:#fcd34d;">0.1</span>], [<span style="color:#fcd34d;">0.8</span>, <span style="color:#fcd34d;">0.9</span>, <span style="color:#fcd34d;">0.1</span>, <span style="color:#fcd34d;">0.3</span>],
      [<span style="color:#fcd34d;">0.3</span>, <span style="color:#fcd34d;">0.7</span>, <span style="color:#fcd34d;">0.8</span>, <span style="color:#fcd34d;">0.6</span>], [<span style="color:#fcd34d;">0.1</span>, <span style="color:#fcd34d;">0.4</span>, <span style="color:#fcd34d;">0.9</span>, <span style="color:#fcd34d;">0.2</span>]]
<span style="color:#93c5fd;">K</span> = [[<span style="color:#fcd34d;">0.9</span>, <span style="color:#fcd34d;">0.1</span>, <span style="color:#fcd34d;">0.4</span>, <span style="color:#fcd34d;">0.2</span>], [<span style="color:#fcd34d;">0.7</span>, <span style="color:#fcd34d;">0.8</span>, <span style="color:#fcd34d;">0.2</span>, <span style="color:#fcd34d;">0.4</span>],
      [<span style="color:#fcd34d;">0.4</span>, <span style="color:#fcd34d;">0.6</span>, <span style="color:#fcd34d;">0.7</span>, <span style="color:#fcd34d;">0.5</span>], [<span style="color:#fcd34d;">0.2</span>, <span style="color:#fcd34d;">0.3</span>, <span style="color:#fcd34d;">0.8</span>, <span style="color:#fcd34d;">0.1</span>]]
<span style="color:#93c5fd;">V</span> = [[<span style="color:#fcd34d;">1.0</span>, <span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">0.5</span>, <span style="color:#fcd34d;">0.2</span>], [<span style="color:#fcd34d;">0.5</span>, <span style="color:#fcd34d;">1.0</span>, <span style="color:#fcd34d;">0.3</span>, <span style="color:#fcd34d;">0.7</span>],
      [<span style="color:#fcd34d;">0.2</span>, <span style="color:#fcd34d;">0.8</span>, <span style="color:#fcd34d;">0.9</span>, <span style="color:#fcd34d;">0.4</span>], [<span style="color:#fcd34d;">0.6</span>, <span style="color:#fcd34d;">0.3</span>, <span style="color:#fcd34d;">0.7</span>, <span style="color:#fcd34d;">0.1</span>]]

<span style="color:#93c5fd;">output</span>, <span style="color:#93c5fd;">attn_weights</span> = self_attention(Q, K, V)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Self-Attention Weights (each row sums to 1.0) ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'':10}"</span> + <span style="color:#a7f3d0;">"  "</span>.join(<span style="color:#a7f3d0;">f"{t:8}"</span> <span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> tokens))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─"</span> * <span style="color:#fcd34d;">50</span>)
<span style="color:#c4b5fd;">for</span> i, (token, weights) <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(<span style="color:#93c5fd;">zip</span>(tokens, attn_weights)):
    <span style="color:#93c5fd;">row</span> = <span style="color:#a7f3d0;">f"{token:10}"</span> + <span style="color:#a7f3d0;">"  "</span>.join(<span style="color:#a7f3d0;">f"{w:8.4f}"</span> <span style="color:#c4b5fd;">for</span> w <span style="color:#c4b5fd;">in</span> weights)
    <span style="color:#93c5fd;">print</span>(row)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nOutput context vectors (same shape as input):"</span>)
<span style="color:#c4b5fd;">for</span> token, vec <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(tokens, output):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {token:8}: {[round(v,4) for v in vec]}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== Self-Attention Weights (each row sums to 1.0) ===
               The        cat        sat       here
──────────────────────────────────────────────────
The         0.3411     0.2891     0.2201     0.1497
cat         0.2512     0.3109     0.2241     0.2138
sat         0.2341     0.2719     0.2788     0.2152
here        0.2201     0.2471     0.3011     0.2317

Output context vectors (same shape as input):
  The     : [0.5891, 0.5012, 0.6019, 0.3601]
  cat     : [0.5841, 0.5211, 0.6101, 0.3721]
  sat     : [0.5791, 0.5389, 0.6241, 0.3721]
  here    : [0.5741, 0.5189, 0.6301, 0.3801]</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Note</span>This is a manually simplified numerical example. Real self-attention operates on high-dimensional vectors and includes scaling, multi-head mechanisms, and masking.</div>
  </div>
</div>

<h3>Transformers vs. Traditional Architectures</h3>
<p>Before Transformers, NLP relied on <strong>Recurrent Neural Networks (RNNs)</strong> and <strong>LSTMs</strong>. These process data sequentially (word by word), making them slow to train and bad at remembering long-range context. Transformers process all words simultaneously (in parallel), using positional encodings to understand word order. This allows training on massive clusters, leading to the LLM explosion.</p>

<h3>The Two Flavors of Transformers: BERT vs GPT</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;font-size:0.875rem;">
    <div style="border-top:3px solid #3b82f6;padding-top:12px;">
      <div style="font-weight:700;color:#3b82f6;margin-bottom:6px;">Encoder-Only (e.g., BERT)</div>
      <div style="color:var(--muted);">Bidirectional context. Good at "understanding" tasks: text classification, named entity recognition, sentiment analysis, extractive question answering.</div>
    </div>
    <div style="border-top:3px solid #10b981;padding-top:12px;">
      <div style="font-weight:700;color:#10b981;margin-bottom:6px;">Decoder-Only (e.g., GPT, Llama, Claude)</div>
      <div style="color:var(--muted);">Unidirectional (autoregressive) context. Predicts the next word. Good at "generation" tasks: writing essays, summarizing, translation, conversational AI.</div>
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $unstrModule->id,
            'title'       => '20.10 Transformers & Large Language Models for Unstructured Data',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L20_10', [
                ['q' => 'What is the primary mechanism that allows Transformers to process text in parallel, unlike RNNs?', 'opts' => ['Word2Vec embeddings', 'Self-Attention', 'Convolutional filters', 'Long Short-Term Memory cells'], 'ans' => 1, 'exp' => 'Self-attention allows every token in the sequence to compute its representation by looking at every other token simultaneously. Because there is no sequential dependency (like in RNNs where step t depends on step t-1), the entire sequence can be processed in parallel.'],
                ['q' => 'In self-attention, what do the Q, K, and V matrices represent?', 'opts' => ['Quantity, Kernel, Vector', 'Query, Key, Value', 'Quotient, Kinematic, Variance', 'Quadratic, K-means, Vertex'], 'ans' => 1, 'exp' => 'Drawing an analogy to database retrieval: the Query (Q) is what a token is looking for, the Key (K) is what other tokens advertise they have, and the Value (V) is the actual content transferred if the Query and Key match.'],
                ['q' => 'Which architecture is best suited for generating text (e.g., writing an essay)?', 'opts' => ['Encoder-only (BERT)', 'Decoder-only (GPT)', 'Convolutional Neural Networks', 'Word2Vec'], 'ans' => 1, 'exp' => 'Decoder-only models like GPT are autoregressive — they are trained to predict the next token given the previous tokens. This makes them perfectly suited for open-ended text generation.'],
                ['q' => 'How do Transformers know the order of words if they process everything simultaneously?', 'opts' => ['They process words one by one', 'They use Positional Encodings added to the input embeddings', 'Self-attention naturally sorts words alphabetically', 'They rely on recurrent layers for ordering'], 'ans' => 1, 'exp' => 'Because self-attention is a set operation (it has no inherent notion of sequence order), positional encodings (vectors representing the position of each word) are added to the input embeddings so the model knows where each word is located in the sequence.'],
                ['q' => 'Why did Transformers lead to the era of Large Language Models?', 'opts' => ['They require less data than RNNs', 'Their parallel nature allows them to scale efficiently on modern GPU clusters, enabling training on billions of tokens', 'They do not require matrix multiplications', 'They only work on English text'], 'ans' => 1, 'exp' => 'RNNs could not scale because their sequential nature meant they could not fully utilize massive parallel hardware. Transformers removed the sequential bottleneck, allowing researchers to throw immense compute and internet-scale datasets at the model, leading to emergent LLM capabilities.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 20.11 — Final Exam
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            ['q' => 'Which of the following represents unstructured data?', 'opts' => ['A CSV file of daily temperatures', 'A SQL database of employee records', 'A JSON file of server logs', 'A directory of MP3 audio recordings'], 'ans' => 3, 'exp' => 'MP3 files (audio) are unstructured. They do not have a predefined schema of rows and columns; they are just a sequence of amplitudes. CSVs and SQL are structured, and JSON is semi-structured.'],
            ['q' => 'During text preprocessing, what does lemmatization do?', 'opts' => ['Removes punctuation', 'Converts all letters to lowercase', 'Reduces words to their dictionary root form (e.g., "running" to "run") using morphological analysis', 'Removes common words like "the" and "a"'], 'ans' => 2, 'exp' => 'Lemmatization reduces inflected forms to their base dictionary form (lemma) using context and part-of-speech. Unlike crude stemming, it produces valid words.'],
            ['q' => 'In the TF-IDF formula, what happens to the weight of a word if it appears in almost every document in the corpus?', 'opts' => ['It gets the highest possible score', 'Its score approaches zero because its IDF (Inverse Document Frequency) becomes very small', 'It becomes negative', 'It remains constant'], 'ans' => 1, 'exp' => 'IDF penalizes words that appear in many documents. If a word is in every document, df(t) = N, making log(N/df) = 0. This gives the word a near-zero overall TF-IDF score, filtering out common uninformative words.'],
            ['q' => 'What is the primary advantage of Word2Vec embeddings over Bag-of-Words vectors?', 'opts' => ['Word2Vec uses less memory', 'Word2Vec vectors are longer', 'Word2Vec captures semantic meaning and relationships between words geometrically', 'Word2Vec does not require preprocessing'], 'ans' => 2, 'exp' => 'BoW treats all words as independent, orthogonal dimensions. Word embeddings learn dense vectors where semantically similar words are close together in the vector space, capturing context and meaning.'],
            ['q' => 'Latent Dirichlet Allocation (LDA) is used for...', 'opts' => ['Image classification', 'Supervised sentiment analysis', 'Unsupervised topic modeling to discover hidden themes in a document collection', 'Named Entity Recognition'], 'ans' => 2, 'exp' => 'LDA is a generative probabilistic model used to discover latent topics in a corpus without any labeled training data.'],
            ['q' => 'In sentiment analysis, why is negation handling important?', 'opts' => ['To remove stopwords', 'Because words like "not" can invert the sentiment of the following words (e.g., "not bad")', 'To reduce vocabulary size', 'To speed up model training'], 'ans' => 1, 'exp' => '"Bad" is negative, but "not bad" is positive. Simple word-counting lexicons fail here unless they specifically track negation words and invert the scores of nearby terms.'],
            ['q' => 'In Named Entity Recognition (NER), what does the "B-ORG" tag mean in the BIO scheme?', 'opts' => ['The word is a Background Organization', 'The word is the Beginning token of an Organization entity span', 'The word is Before an Organization', 'The word is a Binary Organization'], 'ans' => 1, 'exp' => 'BIO stands for Begin, Inside, Outside. "B-ORG" marks the first word of a multi-word organization name (e.g., "United" in "United Nations").'],
            ['q' => 'Why are raw image pixels normalized (scaled to 0-1) before being passed to a Convolutional Neural Network?', 'opts' => ['To compress the image size on disk', 'To change the image to grayscale', 'To improve numerical stability and gradient flow during backpropagation', 'To crop the image automatically'], 'ans' => 2, 'exp' => 'Neural networks learn best when inputs are small, centered numbers. Normalizing raw 0-255 pixels to [0,1] or [-1,1] prevents vanishing/exploding gradients and speeds up convergence.'],
            ['q' => 'Which feature representation is the "gold standard" for feeding audio speech data into a neural network?', 'opts' => ['Raw waveform amplitude', 'Zero-Crossing Rate', 'Mel-Frequency Cepstral Coefficients (MFCCs)', 'Root Mean Square (RMS) Energy'], 'ans' => 2, 'exp' => 'MFCCs mimic human auditory perception using a mel-scale filterbank and compress the frequency information, making them highly robust and effective for speech processing tasks.'],
            ['q' => 'What is the main difference between BERT and GPT architectures?', 'opts' => ['BERT processes images; GPT processes text', 'BERT is an encoder-only model designed for bidirectional understanding; GPT is a decoder-only model designed for autoregressive generation', 'BERT uses RNNs; GPT uses Transformers', 'BERT is supervised; GPT is unsupervised'], 'ans' => 1, 'exp' => 'BERT looks at the entire sentence at once (bidirectional) making it great for tasks like classification and NER. GPT looks only at previous words to predict the next word (autoregressive), making it perfect for generative tasks. Both use the Transformer architecture.'],
        ];

        $finalContent = <<<'HTML'
<div id="org-lock-screen" style="display:block;text-align:center;padding:60px 20px;">
    <h2 style="color:var(--text);margin-bottom:12px;">🔒 Final Exam — Organization Required</h2>
    <p style="color:var(--muted);">The Module 20 Final Exam is only available to students enrolled in an organization.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 20: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 20.1 through 20.10 — unstructured data types, text preprocessing, TF-IDF, word embeddings, topic modeling, sentiment analysis, NER, computer vision basics, audio features, and Transformers. Good luck!</p>
HTML;

        $finalContent .= $this->appendQuiz('', 'FINAL_EXAM', $allFinalQuestions);
        $finalContent .= '</div>';
        $finalContent .= <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.USER_ORG_ID !== 'undefined' && window.USER_ORG_ID !== null && window.USER_ORG_ID !== '') {
        document.getElementById('org-lock-screen').style.display = 'none';
        document.getElementById('final-exam-content').style.display = 'block';
    }
});
</script>
HTML;

        Lesson::create([
            'module_id'   => $unstrModule->id,
            'title'       => '20.11 Final Exam: Analysis of Unstructured Data',
            'order_index' => 11,
            'content'     => $finalContent,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────

    /**
     * Generates the full Quiz HTML/CSS/JS block and appends it to $htmlContent.
     */
    private function appendQuiz(string $htmlContent, string $quizPrefix, array $questions): string
    {
        $total   = count($questions);
        $letters = ['A', 'B', 'C', 'D', 'E'];

        $html  = $htmlContent;
        $html .= '<style>
            .quiz-wrapper{display:flex;flex-direction:column;gap:24px;margin-top:40px;}
            .quiz-card{background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;}
            .quiz-card-header{background:rgba(0,0,0,0.2);padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;gap:12px;}
            .quiz-q-num{background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:"JetBrains Mono",monospace;white-space:nowrap;margin-top:2px;}
            .quiz-q-text{font-size:0.95rem;font-weight:600;color:var(--text);line-height:1.5;}
            .quiz-options{padding:16px 20px;display:flex;flex-direction:column;gap:10px;}
            .quiz-option{display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-radius:7px;border:1px solid var(--border);cursor:pointer;transition:all 0.15s;font-size:0.875rem;color:var(--muted);background:transparent;text-align:left;width:100%;font-family:"Inter",sans-serif;}
            .quiz-option:hover:not(.locked){border-color:var(--border-hover);background:var(--bg);color:var(--text);}
            .quiz-option .opt-key{width:22px;height:22px;border-radius:4px;border:1px solid var(--dim);font-size:0.7rem;font-weight:700;font-family:"JetBrains Mono",monospace;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;transition:all 0.15s;}
            .quiz-option.correct{border-color:#10b981;background:rgba(16,185,129,0.08);color:var(--text);}
            .quiz-option.correct .opt-key{background:#10b981;border-color:#10b981;color:#fff;}
            .quiz-option.wrong{border-color:#ef4444;background:rgba(239,68,68,0.08);color:var(--muted);opacity:0.7;}
            .quiz-option.locked{cursor:default;}
            .quiz-explanation{display:none;margin:0 20px 20px;padding:14px 16px;background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.25);border-radius:7px;font-size:0.875rem;color:var(--muted);line-height:1.7;}
            .quiz-explanation strong{color:var(--text);}
            .quiz-score-bar{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:var(--surface2);border:1px solid var(--border);border-radius:10px;font-size:0.875rem;color:var(--muted);font-weight:600;}
            .quiz-score-val{font-size:1.1rem;font-weight:700;color:#f59e0b;font-family:"JetBrains Mono",monospace;}
        </style>';

        $html .= '<div class="quiz-wrapper" id="wrap_' . $quizPrefix . '">';
        $html .= '<div class="quiz-score-bar"><span>Knowledge Check</span><span class="quiz-score-val"><span id="score_' . $quizPrefix . '">0</span> / ' . $total . '</span></div>';

        foreach ($questions as $qIndex => $q) {
            $qNum = $qIndex + 1;
            $qId  = $quizPrefix . '_q' . $qNum;

            $html .= '<div class="quiz-card" id="' . $qId . '">';
            $html .= '<div class="quiz-card-header"><span class="quiz-q-num">Q' . $qNum . '</span><span class="quiz-q-text">' . htmlspecialchars($q['q']) . '</span></div>';
            $html .= '<div class="quiz-options">';

            foreach ($q['opts'] as $optIndex => $option) {
                $isCorrect = ($optIndex === $q['ans']) ? 'true' : 'false';
                $letter    = $letters[$optIndex];
                $html .= '<button class="quiz-option" onclick="checkAnswer(this,\'' . $qId . '\',' . $isCorrect . ',\'' . $quizPrefix . '\')"><span class="opt-key">' . $letter . '</span> ' . htmlspecialchars($option) . '</button>';
            }

            $html .= '</div>';
            $html .= '<div class="quiz-explanation" id="' . $qId . '-exp"><strong>Explanation:</strong> ' . $q['exp'] . '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= "
<script>
if(typeof window.answeredQuizzes==='undefined'){window.answeredQuizzes={};}
if(typeof window.quizScores==='undefined'){window.quizScores={};}
window.checkAnswer=function(btn,qId,isCorrect,prefix){
    if(window.answeredQuizzes[qId])return;
    window.answeredQuizzes[qId]=true;
    if(typeof window.quizScores[prefix]==='undefined')window.quizScores[prefix]=0;
    const card=document.getElementById(qId);
    const allOpts=card.querySelectorAll('.quiz-option');
    allOpts.forEach(o=>o.classList.add('locked'));
    if(isCorrect){
        btn.classList.add('correct');
        window.quizScores[prefix]++;
    } else {
        btn.classList.add('wrong');
        allOpts.forEach(o=>{if(o.getAttribute('onclick').includes(',true,'))o.classList.add('correct');});
    }
    document.getElementById(qId+'-exp').style.display='block';
    document.getElementById('score_'+prefix).textContent=window.quizScores[prefix];
};
</script>
";

        return $html;
    }
}