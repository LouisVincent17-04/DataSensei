<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module1DataScienceLessonsSeeder
 * Seeds lessons for Module 1: Introduction to Data Science.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module3LessonsSeeder
 */
class Module3LessonsSeeder extends Seeder
{
    public function run()
    {
        $dsModule = Module::where('order_index', 3)->firstOrFail();
        Lesson::where('module_id', $dsModule->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.1 — What is Data Science?
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What is Data Science?</h2>
<p>Data Science is an interdisciplinary field that uses scientific methods, processes, algorithms, and systems to extract knowledge and insights from structured and unstructured data. It sits at the intersection of three core domains: <strong>statistics</strong>, <strong>computer science</strong>, and <strong>domain expertise</strong>. This combination — often depicted as the "Data Science Venn diagram" — is what makes a true data scientist different from a pure statistician or a pure programmer.</p>

<h3>The Data Science Venn Diagram</h3>
<p>Drew Conway's classic 2010 Venn diagram describes data science as the intersection of:</p>
<ul style="color:var(--muted);line-height:2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Math & Statistics</strong> — understanding probability, distributions, hypothesis testing, and model evaluation</li>
  <li><strong style="color:var(--text);">Hacking Skills (CS & Programming)</strong> — writing code, building pipelines, working with databases and APIs</li>
  <li><strong style="color:var(--text);">Substantive Expertise (Domain Knowledge)</strong> — knowing what questions matter in healthcare, finance, retail, etc.</li>
</ul>
<p>Missing any one of these produces a "danger zone": great statistics + domain knowledge without code = someone who can't scale. Great code + domain knowledge without math = a "machine learning engineer" who doesn't know when models fail. Great code + statistics without domain knowledge = someone who answers the wrong question perfectly.</p>

<h3>The Data Science Pipeline</h3>
<p>Every real-world data science project follows a lifecycle. Understanding this pipeline is the single most important conceptual framework you can internalize:</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;">
    <div style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.3);border-radius:8px;padding:16px;text-align:center;">
      <div style="font-size:1.5rem;margin-bottom:8px;">🎯</div>
      <div style="font-weight:700;font-size:0.85rem;color:var(--text);margin-bottom:4px;">1. Define</div>
      <div style="font-size:0.75rem;color:var(--muted);">Business problem → data question</div>
    </div>
    <div style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);border-radius:8px;padding:16px;text-align:center;">
      <div style="font-size:1.5rem;margin-bottom:8px;">🗄️</div>
      <div style="font-weight:700;font-size:0.85rem;color:var(--text);margin-bottom:4px;">2. Collect</div>
      <div style="font-size:0.75rem;color:var(--muted);">APIs, scraping, databases, sensors</div>
    </div>
    <div style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.3);border-radius:8px;padding:16px;text-align:center;">
      <div style="font-size:1.5rem;margin-bottom:8px;">🧹</div>
      <div style="font-weight:700;font-size:0.85rem;color:var(--text);margin-bottom:4px;">3. Clean</div>
      <div style="font-size:0.75rem;color:var(--muted);">Missing values, outliers, encoding</div>
    </div>
    <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:8px;padding:16px;text-align:center;">
      <div style="font-size:1.5rem;margin-bottom:8px;">🔍</div>
      <div style="font-weight:700;font-size:0.85rem;color:var(--text);margin-bottom:4px;">4. Explore</div>
      <div style="font-size:0.75rem;color:var(--muted);">EDA, distributions, correlations</div>
    </div>
    <div style="background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.3);border-radius:8px;padding:16px;text-align:center;">
      <div style="font-size:1.5rem;margin-bottom:8px;">⚙️</div>
      <div style="font-weight:700;font-size:0.85rem;color:var(--text);margin-bottom:4px;">5. Model</div>
      <div style="font-size:0.75rem;color:var(--muted);">Train, validate, tune</div>
    </div>
    <div style="background:rgba(168,85,247,0.1);border:1px solid rgba(168,85,247,0.3);border-radius:8px;padding:16px;text-align:center;">
      <div style="font-size:1.5rem;margin-bottom:8px;">📊</div>
      <div style="font-weight:700;font-size:0.85rem;color:var(--text);margin-bottom:4px;">6. Communicate</div>
      <div style="font-size:0.75rem;color:var(--muted);">Visualize, present, deploy</div>
    </div>
  </div>
</div>

<h3>Types of Data Science Problems</h3>
<p>Data science problems fall into broad categories. Knowing which type you're facing determines which algorithms and evaluation metrics to use:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">TAXONOMY — Problem Types in Data Science</span>
  </div>
  <div style="padding:20px;display:grid;gap:12px;">
    <div style="background:rgba(16,185,129,0.07);border-left:3px solid #10b981;padding:14px 16px;border-radius:0 6px 6px 0;">
      <strong style="color:#10b981;">Supervised Learning</strong> — You have labelled data. Examples: predicting house prices (regression), classifying spam email (classification).
    </div>
    <div style="background:rgba(59,130,246,0.07);border-left:3px solid #3b82f6;padding:14px 16px;border-radius:0 6px 6px 0;">
      <strong style="color:#3b82f6;">Unsupervised Learning</strong> — No labels. Find hidden patterns. Examples: customer segmentation (clustering), dimensionality reduction (PCA).
    </div>
    <div style="background:rgba(245,158,11,0.07);border-left:3px solid #f59e0b;padding:14px 16px;border-radius:0 6px 6px 0;">
      <strong style="color:#f59e0b;">Semi-Supervised Learning</strong> — Small labelled dataset + large unlabelled dataset. Common in medical imaging where labelling is expensive.
    </div>
    <div style="background:rgba(168,85,247,0.07);border-left:3px solid #a855f7;padding:14px 16px;border-radius:0 6px 6px 0;">
      <strong style="color:#a855f7;">Reinforcement Learning</strong> — Agent learns from rewards. Examples: game-playing AI (AlphaGo), recommendation systems, autonomous vehicles.
    </div>
    <div style="background:rgba(239,68,68,0.07);border-left:3px solid #ef4444;padding:14px 16px;border-radius:0 6px 6px 0;">
      <strong style="color:#ef4444;">Time Series Analysis</strong> — Data indexed by time. Examples: stock price forecasting, demand prediction, anomaly detection in server logs.
    </div>
  </div>
</div>

<h3>The Python Data Science Ecosystem</h3>
<p>Python dominates data science because of its ecosystem. Here are the core libraries you will master in this course:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — The Data Science Stack</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># The canonical data science imports — you will write these thousands of times</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np              <span style="color:#6b7280;"># Numerical computing — arrays, math, linear algebra</span>
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd             <span style="color:#6b7280;"># Data manipulation — DataFrames, Series, CSV/JSON</span>
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt  <span style="color:#6b7280;"># Plotting — line charts, histograms, scatter plots</span>
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns            <span style="color:#6b7280;"># Statistical visualization — built on matplotlib</span>
<span style="color:#c4b5fd;">from</span> sklearn <span style="color:#c4b5fd;">import</span> ...          <span style="color:#6b7280;"># Machine learning — models, preprocessing, evaluation</span>
<span style="color:#c4b5fd;">import</span> scipy.stats <span style="color:#c4b5fd;">as</span> stats      <span style="color:#6b7280;"># Scientific computing — distributions, hypothesis tests</span>

<span style="color:#6b7280;"># Check your environment versions</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"NumPy:      {np.__version__}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Pandas:     {pd.__version__}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Matplotlib: {plt.matplotlib.__version__}"</span>)</div>
  </div>
</div>

<h3>Structured vs Unstructured Data</h3>
<p>Understanding what kind of data you have determines what tools you reach for:</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Structured Data</strong> — rows and columns, lives in databases and CSVs. Pandas is your primary tool.</li>
  <li><strong style="color:var(--text);">Unstructured Data</strong> — images, audio, video, free text. Requires deep learning and specialized preprocessing.</li>
  <li><strong style="color:var(--text);">Semi-structured Data</strong> — JSON, XML, HTML. Has some structure but doesn't fit neatly into a table without transformation.</li>
</ul>
HTML;

        Lesson::create([
            'module_id'   => $dsModule->id,
            'title'       => '1.1 What is Data Science? The Full Landscape',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L1_1', [
                ['q' => 'What are the three domains at the intersection of the Data Science Venn diagram?', 'opts' => ['Code, Data, Models', 'Statistics, Computer Science, Domain Expertise', 'Python, SQL, Machine Learning', 'Data Collection, Cleaning, Visualization'], 'ans' => 1, 'exp' => 'Drew Conway\'s Venn diagram identifies Math & Statistics, Hacking Skills (CS/Programming), and Substantive Expertise (Domain Knowledge) as the three overlapping areas.'],
                ['q' => 'What type of problem involves finding hidden patterns in data WITHOUT labels?', 'opts' => ['Supervised Learning', 'Reinforcement Learning', 'Unsupervised Learning', 'Time Series Analysis'], 'ans' => 2, 'exp' => 'Unsupervised learning works with unlabelled data to discover hidden structure — clustering customers, compressing dimensions via PCA, finding anomalies.'],
                ['q' => 'Which step in the Data Science Pipeline involves handling missing values and outliers?', 'opts' => ['Define', 'Collect', 'Clean', 'Model'], 'ans' => 2, 'exp' => 'The Clean step (Step 3) covers data preprocessing: handling nulls, encoding categoricals, removing or capping outliers.'],
                ['q' => 'What kind of data does a CSV file typically contain?', 'opts' => ['Unstructured', 'Structured', 'Semi-structured', 'Raw binary'], 'ans' => 1, 'exp' => 'CSV files contain structured data — rows and columns with consistent field names. Pandas is the standard tool for reading them.'],
                ['q' => 'Which library is the primary tool for numerical array computation in Python data science?', 'opts' => ['Pandas', 'Matplotlib', 'NumPy', 'Scikit-Learn'], 'ans' => 2, 'exp' => 'NumPy (Numerical Python) provides the ndarray data structure and mathematical operations that underpin the entire Python data science stack.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.2 — NumPy Foundations
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>NumPy: The Foundation of Numerical Computing</h2>
<p>NumPy (Numerical Python) is the most fundamental library in the Python data science ecosystem. Every major library — Pandas, Scikit-Learn, TensorFlow, PyTorch — is built on top of NumPy arrays. Understanding NumPy deeply is essential because it teaches you the vectorized thinking that makes data science code fast and readable.</p>

<h3>Why NumPy over Python Lists?</h3>
<p>Python lists are flexible but slow for numerical work because they store heterogeneous objects and execute operations element-by-element in Python bytecode. NumPy arrays store homogeneous data in contiguous memory blocks and delegate computations to highly optimized C/Fortran libraries. The performance difference is often <strong>100–1000x</strong>.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">NUMPY — Creating Arrays</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#6b7280;"># From a Python list</span>
<span style="color:#93c5fd;">arr1d</span> = np.array([<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">5</span>])
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"1D array:"</span>, arr1d)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"dtype:"</span>, arr1d.dtype)   <span style="color:#6b7280;"># int64</span>

<span style="color:#6b7280;"># 2D array (matrix) — list of lists</span>
<span style="color:#93c5fd;">arr2d</span> = np.array([[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">2</span>,<span style="color:#fcd34d;">3</span>],[<span style="color:#fcd34d;">4</span>,<span style="color:#fcd34d;">5</span>,<span style="color:#fcd34d;">6</span>]])
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Shape:"</span>, arr2d.shape)   <span style="color:#6b7280;"># (2, 3) → 2 rows, 3 cols</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Size:"</span>,  arr2d.size)    <span style="color:#6b7280;"># 6 total elements</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Ndim:"</span>,  arr2d.ndim)    <span style="color:#6b7280;"># 2 dimensions</span>

<span style="color:#6b7280;"># Factory functions — the most-used ways to build arrays</span>
<span style="color:#93c5fd;">print</span>(np.zeros((<span style="color:#fcd34d;">2</span>,<span style="color:#fcd34d;">3</span>)))             <span style="color:#6b7280;"># 2x3 matrix of 0.0</span>
<span style="color:#93c5fd;">print</span>(np.ones((<span style="color:#fcd34d;">3</span>,<span style="color:#fcd34d;">3</span>)))              <span style="color:#6b7280;"># 3x3 matrix of 1.0</span>
<span style="color:#93c5fd;">print</span>(np.eye(<span style="color:#fcd34d;">4</span>))                  <span style="color:#6b7280;"># 4x4 identity matrix</span>
<span style="color:#93c5fd;">print</span>(np.arange(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">2</span>))         <span style="color:#6b7280;"># [0 2 4 6 8] — like range() but returns array</span>
<span style="color:#93c5fd;">print</span>(np.linspace(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">5</span>))        <span style="color:#6b7280;"># 5 evenly spaced values from 0 to 1</span>
<span style="color:#93c5fd;">print</span>(np.full((<span style="color:#fcd34d;">2</span>,<span style="color:#fcd34d;">4</span>), <span style="color:#fcd34d;">7</span>))           <span style="color:#6b7280;"># 2x4 array filled with 7</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>1D array: [1 2 3 4 5]
dtype: int64
Shape: (2, 3)
Size: 6
Ndim: 2
[[0. 0. 0.]
 [0. 0. 0.]]
... (truncated for brevity)</div>
  </div>
</div>

<h3>Vectorized Operations — No Loops Needed</h3>
<p>NumPy's superpower is <em>vectorization</em>: operations on arrays are applied to every element simultaneously without a Python for-loop. This is both faster and more readable.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">NUMPY — Vectorized Math</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">prices</span> = np.array([<span style="color:#fcd34d;">120.0</span>, <span style="color:#fcd34d;">85.5</span>, <span style="color:#fcd34d;">300.0</span>, <span style="color:#fcd34d;">45.0</span>, <span style="color:#fcd34d;">200.0</span>])

<span style="color:#6b7280;"># Arithmetic on every element — no for loop!</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"With 10% discount:"</span>, prices * <span style="color:#fcd34d;">0.9</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"After ₱50 rebate:"</span>,  prices - <span style="color:#fcd34d;">50</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Price squared:"</span>,      prices ** <span style="color:#fcd34d;">2</span>)

<span style="color:#6b7280;"># Element-wise operations between two arrays</span>
<span style="color:#93c5fd;">quantities</span> = np.array([<span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">25</span>, <span style="color:#fcd34d;">5</span>])
<span style="color:#93c5fd;">revenue</span>    = prices * quantities
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Revenue per product:"</span>, revenue)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Total revenue:"</span>,      revenue.sum())

<span style="color:#6b7280;"># Universal functions (ufuncs) — element-wise math functions</span>
<span style="color:#93c5fd;">angles</span> = np.array([<span style="color:#fcd34d;">0</span>, np.pi/<span style="color:#fcd34d;">2</span>, np.pi])
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"sin values:"</span>, np.round(np.sin(angles), <span style="color:#fcd34d;">4</span>))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Log of prices:"</span>, np.log(prices).round(<span style="color:#fcd34d;">3</span>))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Square roots:"</span>,  np.sqrt(prices).round(<span style="color:#fcd34d;">3</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>With 10% discount: [108.   76.95 270.   40.5  180. ]
After ₱50 rebate:  [ 70.   35.5 250.   -5.  150. ]
Revenue per product: [ 360.   855.   300.  1125.  1000.]
Total revenue: 3640.0
sin values: [ 0.  1. -0.]
Log of prices: [4.787 4.448 5.704 3.807 5.298]
Square roots: [10.954  9.247 17.321  6.708 14.142]</div>
  </div>
</div>

<h3>Indexing, Slicing & Boolean Masking</h3>
<p>NumPy's indexing is the template for all Pandas operations. Boolean masking — filtering by condition — is one of the most powerful patterns in data science.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">NUMPY — Indexing & Boolean Masking</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">scores</span> = np.array([<span style="color:#fcd34d;">78</span>, <span style="color:#fcd34d;">92</span>, <span style="color:#fcd34d;">61</span>, <span style="color:#fcd34d;">85</span>, <span style="color:#fcd34d;">54</span>, <span style="color:#fcd34d;">95</span>, <span style="color:#fcd34d;">73</span>, <span style="color:#fcd34d;">88</span>])

<span style="color:#6b7280;"># Standard slicing — same syntax as lists</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"First 3:"</span>,  scores[:<span style="color:#fcd34d;">3</span>])
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Every 2nd:"</span>, scores[::<span style="color:#fcd34d;">2</span>])

<span style="color:#6b7280;"># Boolean mask — creates a True/False array for each element</span>
<span style="color:#93c5fd;">mask</span> = scores >= <span style="color:#fcd34d;">80</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Boolean mask:"</span>, mask)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Scores ≥ 80:"</span>, scores[mask])       <span style="color:#6b7280;"># Fancy indexing with boolean array</span>

<span style="color:#6b7280;"># Combining conditions with & (and) and | (or) — use parentheses!</span>
<span style="color:#93c5fd;">passing_and_excellent</span> = scores[(scores >= <span style="color:#fcd34d;">80</span>) & (scores < <span style="color:#fcd34d;">95</span>)]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"80-94 range:"</span>, passing_and_excellent)

<span style="color:#6b7280;"># Replacing values using a mask</span>
<span style="color:#93c5fd;">adjusted</span> = scores.copy()
<span style="color:#93c5fd;">adjusted</span>[adjusted < <span style="color:#fcd34d;">60</span>] = <span style="color:#fcd34d;">60</span>    <span style="color:#6b7280;"># Minimum score of 60</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"After floor of 60:"</span>, adjusted)

<span style="color:#6b7280;"># np.where — vectorized if-else</span>
<span style="color:#93c5fd;">grades</span> = np.where(scores >= <span style="color:#fcd34d;">75</span>, <span style="color:#a7f3d0;">"PASS"</span>, <span style="color:#a7f3d0;">"FAIL"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Grades:"</span>, grades)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>First 3:  [78 92 61]
Every 2nd: [78 61 54 73]
Boolean mask: [ True  True False  True False  True False  True]
Scores ≥ 80: [92 85 95 88]
80-94 range: [92 85 88]
After floor of 60: [78 92 61 85 60 95 73 88]
Grades: ['PASS' 'PASS' 'FAIL' 'PASS' 'FAIL' 'PASS' 'FAIL' 'PASS']</div>
  </div>
</div>

<h3>Statistical Aggregations</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">NUMPY — Descriptive Statistics</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">data</span> = np.array([<span style="color:#fcd34d;">23</span>, <span style="color:#fcd34d;">45</span>, <span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">67</span>, <span style="color:#fcd34d;">34</span>, <span style="color:#fcd34d;">89</span>, <span style="color:#fcd34d;">56</span>, <span style="color:#fcd34d;">78</span>, <span style="color:#fcd34d;">90</span>, <span style="color:#fcd34d;">11</span>])

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mean:     {data.mean():.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Median:   {np.median(data):.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Std Dev:  {data.std():.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Variance: {data.var():.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Min/Max:  {data.min()} / {data.max()}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Range:    {data.max() - data.min()}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sum:      {data.sum()}"</span>)

<span style="color:#6b7280;"># Percentiles — crucial for outlier detection</span>
<span style="color:#93c5fd;">p25</span>, <span style="color:#93c5fd;">p75</span> = np.percentile(data, [<span style="color:#fcd34d;">25</span>, <span style="color:#fcd34d;">75</span>])
<span style="color:#93c5fd;">iqr</span>       = p75 - p25
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Q1={p25}, Q3={p75}, IQR={iqr}"</span>)

<span style="color:#6b7280;"># Axis-wise aggregation on 2D arrays</span>
<span style="color:#93c5fd;">matrix</span> = np.array([[<span style="color:#fcd34d;">10</span>,<span style="color:#fcd34d;">20</span>,<span style="color:#fcd34d;">30</span>],[<span style="color:#fcd34d;">40</span>,<span style="color:#fcd34d;">50</span>,<span style="color:#fcd34d;">60</span>]])
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Col means (axis=0):"</span>, matrix.mean(axis=<span style="color:#fcd34d;">0</span>))   <span style="color:#6b7280;"># average down rows</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Row means (axis=1):"</span>, matrix.mean(axis=<span style="color:#fcd34d;">1</span>))   <span style="color:#6b7280;"># average across columns</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>Mean:     50.50
Median:   50.50
Std Dev:  27.50
Variance: 756.25
Min/Max:  11 / 90
Range:    79
Sum:      505
Q1=23.25, Q3=72.25, IQR=49.0
Col means (axis=0): [25. 35. 45.]
Row means (axis=1): [20. 50.]</div>
  </div>
</div>

<h3>Random Number Generation</h3>
<p>Generating random data is fundamental to data science — for simulations, train/test splits, initializing model weights, and creating synthetic datasets for testing.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">NUMPY — Random Number Generation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">rng</span> = np.random.default_rng(seed=<span style="color:#fcd34d;">42</span>)   <span style="color:#6b7280;"># Reproducible: seed locks the sequence</span>

<span style="color:#6b7280;"># Uniform distribution: values between low and high</span>
<span style="color:#93c5fd;">uniform</span> = rng.uniform(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">100</span>, size=<span style="color:#fcd34d;">5</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Uniform [0,100]:"</span>, uniform.round(<span style="color:#fcd34d;">1</span>))

<span style="color:#6b7280;"># Normal (Gaussian) distribution: mean=0, std=1</span>
<span style="color:#93c5fd;">normal</span> = rng.normal(loc=<span style="color:#fcd34d;">170</span>, scale=<span style="color:#fcd34d;">10</span>, size=<span style="color:#fcd34d;">6</span>)  <span style="color:#6b7280;"># height distribution</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Heights (cm):"</span>, normal.round(<span style="color:#fcd34d;">1</span>))

<span style="color:#6b7280;"># Integer random values</span>
<span style="color:#93c5fd;">dice</span> = rng.integers(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">7</span>, size=<span style="color:#fcd34d;">10</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"10 dice rolls:"</span>, dice)

<span style="color:#6b7280;"># Random choice — sampling from an array</span>
<span style="color:#93c5fd;">classes</span>  = [<span style="color:#a7f3d0;">"cat"</span>, <span style="color:#a7f3d0;">"dog"</span>, <span style="color:#a7f3d0;">"bird"</span>]
<span style="color:#93c5fd;">sample</span>   = rng.choice(classes, size=<span style="color:#fcd34d;">5</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Random sample:"</span>, sample)

<span style="color:#6b7280;"># Shuffling</span>
<span style="color:#93c5fd;">indices</span> = np.arange(<span style="color:#fcd34d;">10</span>)
rng.shuffle(indices)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Shuffled indices:"</span>, indices)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>Uniform [0,100]: [77.4 43.0 20.6 96.9 83.2]
Heights (cm): [174.8 165.3 178.2 162.7 180.1 168.9]
10 dice rolls: [3 1 5 4 6 2 3 4 1 6]
Random sample: ['dog' 'cat' 'dog' 'bird' 'cat']
Shuffled indices: [7 2 0 9 4 1 5 3 8 6]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsModule->id,
            'title'       => '1.2 NumPy: Arrays, Vectorization & Linear Algebra',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L1_2', [
                ['q' => 'What does np.arange(0, 10, 2) produce?', 'opts' => ['[0,2,4,6,8,10]', '[0,2,4,6,8]', '[2,4,6,8]', '[0,10,2]'], 'ans' => 1, 'exp' => 'np.arange(start, stop, step) generates values from 0 up to but not including 10, stepping by 2 → [0,2,4,6,8].'],
                ['q' => 'What does the .shape attribute return for a 2D array with 3 rows and 4 columns?', 'opts' => ['12', '(4,3)', '(3,4)', '[3,4]'], 'ans' => 2, 'exp' => '.shape returns a tuple (rows, columns). For a 3-row, 4-column array it returns (3, 4).'],
                ['q' => 'What does np.where(arr > 50, "high", "low") do?', 'opts' => ['Counts elements over 50', 'Returns a boolean array', 'Returns "high" or "low" for each element based on condition', 'Filters the array'], 'ans' => 2, 'exp' => 'np.where(condition, a, b) returns a new array with "a" where condition is True and "b" where it is False — vectorized if-else.'],
                ['q' => 'Why must you use & instead of "and" when combining NumPy boolean masks?', 'opts' => ['Performance reasons', '"and" does not exist in Python', '"and" operates on whole arrays not element-by-element', 'np.and() is preferred'], 'ans' => 2, 'exp' => 'Python\'s "and" checks the truthiness of an entire array (ambiguous), raising an error. & is the element-wise bitwise AND that works correctly with boolean arrays.'],
                ['q' => 'What does setting a seed in np.random.default_rng(seed=42) guarantee?', 'opts' => ['Same values always', 'Faster generation', 'Cryptographically secure numbers', 'Uniform distribution'], 'ans' => 0, 'exp' => 'Setting a seed makes the random number sequence reproducible. Anyone running the same code with seed=42 gets the same "random" values — crucial for experiment reproducibility.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.3 — Pandas DataFrames
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Pandas: The Data Scientist's Spreadsheet</h2>
<p>Pandas is the primary library for structured data manipulation in Python. Its two core data structures — <strong>Series</strong> (1D labelled array) and <strong>DataFrame</strong> (2D labelled table) — let you load, clean, transform, and analyse tabular data with SQL-like expressiveness. Every data scientist spends enormous amounts of time in Pandas.</p>

<h3>Series: The 1D Building Block</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PANDAS — Series</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd

<span style="color:#6b7280;"># A Series is a 1D array with a named index</span>
<span style="color:#93c5fd;">temps</span> = pd.Series(
    [<span style="color:#fcd34d;">32.5</span>, <span style="color:#fcd34d;">34.1</span>, <span style="color:#fcd34d;">30.8</span>, <span style="color:#fcd34d;">35.2</span>, <span style="color:#fcd34d;">28.9</span>],
    index=[<span style="color:#a7f3d0;">"Mon"</span>, <span style="color:#a7f3d0;">"Tue"</span>, <span style="color:#a7f3d0;">"Wed"</span>, <span style="color:#a7f3d0;">"Thu"</span>, <span style="color:#a7f3d0;">"Fri"</span>],
    name=<span style="color:#a7f3d0;">"Temperature_C"</span>
)
<span style="color:#93c5fd;">print</span>(temps)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nAccess by label:"</span>, temps[<span style="color:#a7f3d0;">"Wed"</span>])
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Access by position:"</span>, temps.iloc[<span style="color:#fcd34d;">2</span>])

<span style="color:#6b7280;"># Series has all NumPy-style aggregations</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nMean: {temps.mean():.2f}°C"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Max day: {temps.idxmax()}"</span>)   <span style="color:#6b7280;"># Returns the label of the max value</span>

<span style="color:#6b7280;"># Vectorized string operations on Series of strings</span>
<span style="color:#93c5fd;">cities</span> = pd.Series([<span style="color:#a7f3d0;">"  Cebu City  "</span>, <span style="color:#a7f3d0;">"manila"</span>, <span style="color:#a7f3d0;">"DAVAO"</span>])
<span style="color:#93c5fd;">print</span>(cities.str.strip().str.title())</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>Mon    32.5
Tue    34.1
Wed    30.8
Thu    35.2
Fri    28.9
Name: Temperature_C, dtype: float64

Access by label: 30.8
Access by position: 30.8
Mean: 32.30°C
Max day: Thu
0    Cebu City
1       Manila
2        Davao
dtype: object</div>
  </div>
</div>

<h3>Creating DataFrames</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PANDAS — DataFrame Creation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Method 1: From a dict of lists (most common)</span>
<span style="color:#93c5fd;">df</span> = pd.DataFrame({
    <span style="color:#a7f3d0;">"student"</span>  : [<span style="color:#a7f3d0;">"Ana"</span>, <span style="color:#a7f3d0;">"Ben"</span>, <span style="color:#a7f3d0;">"Clara"</span>, <span style="color:#a7f3d0;">"Diego"</span>, <span style="color:#a7f3d0;">"Eve"</span>],
    <span style="color:#a7f3d0;">"math"</span>     : [<span style="color:#fcd34d;">88</span>, <span style="color:#fcd34d;">72</span>, <span style="color:#fcd34d;">95</span>, <span style="color:#fcd34d;">61</span>, <span style="color:#fcd34d;">83</span>],
    <span style="color:#a7f3d0;">"science"</span>  : [<span style="color:#fcd34d;">91</span>, <span style="color:#fcd34d;">68</span>, <span style="color:#fcd34d;">89</span>, <span style="color:#fcd34d;">74</span>, <span style="color:#fcd34d;">77</span>],
    <span style="color:#a7f3d0;">"grade"</span>    : [<span style="color:#a7f3d0;">"A"</span>, <span style="color:#a7f3d0;">"C"</span>, <span style="color:#a7f3d0;">"A"</span>, <span style="color:#a7f3d0;">"D"</span>, <span style="color:#a7f3d0;">"B"</span>]
})
<span style="color:#93c5fd;">print</span>(df)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nShape:"</span>, df.shape)
<span style="color:#93c5fd;">print</span>(df.dtypes)

<span style="color:#6b7280;"># Method 2: From a CSV file (real-world standard)</span>
<span style="color:#6b7280;"># df = pd.read_csv("data.csv")</span>
<span style="color:#6b7280;"># df = pd.read_csv("data.csv", index_col="id", parse_dates=["created_at"])</span>

<span style="color:#6b7280;"># Quick overview methods — use these at the start of every analysis</span>
<span style="color:#93c5fd;">print</span>(df.head(<span style="color:#fcd34d;">3</span>))          <span style="color:#6b7280;"># First 3 rows</span>
<span style="color:#93c5fd;">print</span>(df.describe())       <span style="color:#6b7280;"># Statistical summary of numeric columns</span>
<span style="color:#93c5fd;">print</span>(df.info())           <span style="color:#6b7280;"># Column types, null counts</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>  student  math  science grade
0     Ana    88       91     A
1     Ben    72       68     C
2   Clara    95       89     A
3   Diego    61       74     D
4     Eve    83       77     B
Shape: (5, 4)
student    object
math        int64
science     int64
grade      object</div>
  </div>
</div>

<h3>Selecting, Filtering & Adding Columns</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PANDAS — Selection & Filtering</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Select a single column (returns a Series)</span>
<span style="color:#93c5fd;">print</span>(df[<span style="color:#a7f3d0;">"math"</span>].values)

<span style="color:#6b7280;"># Select multiple columns (returns a DataFrame)</span>
<span style="color:#93c5fd;">print</span>(df[[<span style="color:#a7f3d0;">"student"</span>, <span style="color:#a7f3d0;">"math"</span>]])

<span style="color:#6b7280;"># .loc — label-based selection [rows, cols]</span>
<span style="color:#93c5fd;">print</span>(df.loc[<span style="color:#fcd34d;">1</span>:<span style="color:#fcd34d;">3</span>, [<span style="color:#a7f3d0;">"student"</span>, <span style="color:#a7f3d0;">"math"</span>]])   <span style="color:#6b7280;"># rows 1-3, two columns</span>

<span style="color:#6b7280;"># .iloc — integer position-based selection</span>
<span style="color:#93c5fd;">print</span>(df.iloc[<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>])    <span style="color:#6b7280;"># Row 0, Column 1 → 88</span>

<span style="color:#6b7280;"># Boolean filter — same as NumPy masking</span>
<span style="color:#93c5fd;">top_math</span> = df[df[<span style="color:#a7f3d0;">"math"</span>] >= <span style="color:#fcd34d;">85</span>]
<span style="color:#93c5fd;">print</span>(top_math[[<span style="color:#a7f3d0;">"student"</span>, <span style="color:#a7f3d0;">"math"</span>]])

<span style="color:#6b7280;"># .query() — more readable for complex filters</span>
<span style="color:#93c5fd;">high_achievers</span> = df.query(<span style="color:#a7f3d0;">"math >= 80 and science >= 80"</span>)
<span style="color:#93c5fd;">print</span>(high_achievers)

<span style="color:#6b7280;"># Adding a new calculated column</span>
<span style="color:#93c5fd;">df</span>[<span style="color:#a7f3d0;">"average"</span>] = (df[<span style="color:#a7f3d0;">"math"</span>] + df[<span style="color:#a7f3d0;">"science"</span>]) / <span style="color:#fcd34d;">2</span>
<span style="color:#93c5fd;">df</span>[<span style="color:#a7f3d0;">"passed"</span>]  = df[<span style="color:#a7f3d0;">"average"</span>] >= <span style="color:#fcd34d;">75</span>
<span style="color:#93c5fd;">print</span>(df)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>[88 72 95 61 83]
  student  math
0     Ana    88
...
  student  math  science grade  average  passed
0     Ana    88       91     A     89.5    True
1     Ben    72       68     C     70.0   False
...</div>
  </div>
</div>

<h3>Handling Missing Data</h3>
<p>Real-world datasets always have missing values. Pandas represents them as <code>NaN</code> (Not a Number). Knowing how to detect and handle them is one of the most important data cleaning skills.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PANDAS — Missing Data</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#93c5fd;">messy</span> = pd.DataFrame({
    <span style="color:#a7f3d0;">"name"</span>  : [<span style="color:#a7f3d0;">"Alice"</span>, <span style="color:#a7f3d0;">"Bob"</span>, np.nan, <span style="color:#a7f3d0;">"Diana"</span>, <span style="color:#a7f3d0;">"Eve"</span>],
    <span style="color:#a7f3d0;">"age"</span>   : [<span style="color:#fcd34d;">25</span>, np.nan, <span style="color:#fcd34d;">30</span>, np.nan, <span style="color:#fcd34d;">28</span>],
    <span style="color:#a7f3d0;">"salary"</span>: [<span style="color:#fcd34d;">50000</span>, <span style="color:#fcd34d;">60000</span>, np.nan, <span style="color:#fcd34d;">55000</span>, <span style="color:#fcd34d;">70000</span>]
})

<span style="color:#6b7280;"># Detect nulls</span>
<span style="color:#93c5fd;">print</span>(messy.isnull())              <span style="color:#6b7280;"># Boolean DataFrame</span>
<span style="color:#93c5fd;">print</span>(messy.isnull().sum())        <span style="color:#6b7280;"># Count nulls per column</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Total nulls: {messy.isnull().sum().sum()}"</span>)

<span style="color:#6b7280;"># Strategy 1: Drop rows/cols with nulls</span>
<span style="color:#93c5fd;">clean_drop</span> = messy.dropna()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nAfter dropna():"</span>, clean_drop)

<span style="color:#6b7280;"># Strategy 2: Fill nulls with a value</span>
<span style="color:#93c5fd;">clean_fill</span> = messy.copy()
<span style="color:#93c5fd;">clean_fill</span>[<span style="color:#a7f3d0;">"age"</span>]    = messy[<span style="color:#a7f3d0;">"age"</span>].fillna(messy[<span style="color:#a7f3d0;">"age"</span>].mean())
<span style="color:#93c5fd;">clean_fill</span>[<span style="color:#a7f3d0;">"salary"</span>] = messy[<span style="color:#a7f3d0;">"salary"</span>].fillna(messy[<span style="color:#a7f3d0;">"salary"</span>].median())
<span style="color:#93c5fd;">clean_fill</span>[<span style="color:#a7f3d0;">"name"</span>]   = messy[<span style="color:#a7f3d0;">"name"</span>].fillna(<span style="color:#a7f3d0;">"Unknown"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nAfter fillna:"</span>)
<span style="color:#93c5fd;">print</span>(clean_fill)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>name      age  salary
0  False  False   False
1  False   True   False
2   True  False    True
3  False   True   False
4  False  False   False

name      1
age       2
salary    1
Total nulls: 4</div>
  </div>
</div>

<h3>GroupBy: Split-Apply-Combine</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PANDAS — GroupBy Aggregation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">sales</span> = pd.DataFrame({
    <span style="color:#a7f3d0;">"region"</span>  : [<span style="color:#a7f3d0;">"North"</span>, <span style="color:#a7f3d0;">"South"</span>, <span style="color:#a7f3d0;">"North"</span>, <span style="color:#a7f3d0;">"East"</span>, <span style="color:#a7f3d0;">"South"</span>, <span style="color:#a7f3d0;">"East"</span>],
    <span style="color:#a7f3d0;">"product"</span> : [<span style="color:#a7f3d0;">"A"</span>, <span style="color:#a7f3d0;">"B"</span>, <span style="color:#a7f3d0;">"B"</span>, <span style="color:#a7f3d0;">"A"</span>, <span style="color:#a7f3d0;">"A"</span>, <span style="color:#a7f3d0;">"B"</span>],
    <span style="color:#a7f3d0;">"revenue"</span> : [<span style="color:#fcd34d;">5200</span>, <span style="color:#fcd34d;">3100</span>, <span style="color:#fcd34d;">4800</span>, <span style="color:#fcd34d;">6200</span>, <span style="color:#fcd34d;">2900</span>, <span style="color:#fcd34d;">5500</span>],
    <span style="color:#a7f3d0;">"units"</span>   : [<span style="color:#fcd34d;">52</span>, <span style="color:#fcd34d;">31</span>, <span style="color:#fcd34d;">48</span>, <span style="color:#fcd34d;">62</span>, <span style="color:#fcd34d;">29</span>, <span style="color:#fcd34d;">55</span>]
})

<span style="color:#6b7280;"># Total revenue by region</span>
<span style="color:#93c5fd;">print</span>(sales.groupby(<span style="color:#a7f3d0;">"region"</span>)[<span style="color:#a7f3d0;">"revenue"</span>].sum())

<span style="color:#6b7280;"># Multiple aggregations at once</span>
<span style="color:#93c5fd;">summary</span> = sales.groupby(<span style="color:#a7f3d0;">"region"</span>).agg(
    total_revenue=(<span style="color:#a7f3d0;">"revenue"</span>, <span style="color:#a7f3d0;">"sum"</span>),
    avg_units    =(<span style="color:#a7f3d0;">"units"</span>,   <span style="color:#a7f3d0;">"mean"</span>),
    count        =(<span style="color:#a7f3d0;">"product"</span>, <span style="color:#a7f3d0;">"count"</span>)
)
<span style="color:#93c5fd;">print</span>(summary.sort_values(<span style="color:#a7f3d0;">"total_revenue"</span>, ascending=<span style="color:#fca5a5;">False</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>region
East     11700
North    10000
South     6000

         total_revenue  avg_units  count
region
East             11700       58.5      2
North            10000       50.0      2
South             6000       30.0      2</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsModule->id,
            'title'       => '1.3 Pandas: DataFrames, Cleaning & GroupBy',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L1_3', [
                ['q' => 'What is the difference between .loc and .iloc?', 'opts' => ['.loc is faster', '.loc uses label-based indexing, .iloc uses integer-position indexing', '.iloc supports boolean masks', 'There is no difference'], 'ans' => 1, 'exp' => '.loc selects by label (index name, column name). .iloc selects by integer position (row 0, column 2). For integer-indexed DataFrames they look similar but behave differently at edges.'],
                ['q' => 'What does df.isnull().sum() return?', 'opts' => ['A single number', 'A boolean array', 'Count of null values per column', 'Row numbers with nulls'], 'ans' => 2, 'exp' => 'isnull() returns a boolean DataFrame. Calling .sum() aggregates True values (treating True as 1) down columns, giving the null count per column.'],
                ['q' => 'What strategy does fillna(df["col"].mean()) implement?', 'opts' => ['Drop missing rows', 'Mean imputation', 'Forward fill', 'Mode imputation'], 'ans' => 1, 'exp' => 'Replacing NaN with the column mean is called mean imputation — a common baseline strategy. Use median for skewed data.'],
                ['q' => 'What does df.query("math >= 80 and science >= 80") do?', 'opts' => ['Selects only those columns', 'Filters rows where both conditions are True', 'Raises an error', 'Creates new columns'], 'ans' => 1, 'exp' => '.query() evaluates a string expression as a row filter. It is equivalent to df[(df["math"]>=80) & (df["science"]>=80)] but more readable.'],
                ['q' => 'What does groupby("region")["revenue"].sum() produce?', 'opts' => ['A DataFrame with all columns', 'A Series with total revenue per region', 'A boolean mask', 'Sorted region names'], 'ans' => 1, 'exp' => 'groupby().sum() is the split-apply-combine pattern: split data into groups by "region", apply sum to the "revenue" column, and combine results into a Series indexed by region.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.4 — Matplotlib & Seaborn
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Data Visualization: Matplotlib & Seaborn</h2>
<p>Data visualization is one of the most powerful communication tools a data scientist has. A well-crafted chart can reveal patterns invisible in raw numbers, expose outliers instantly, and make your findings accessible to non-technical stakeholders. Matplotlib is the foundational plotting library; Seaborn builds on top of it with a high-level API for statistical graphics.</p>

<h3>Matplotlib Architecture</h3>
<p>Matplotlib has two interfaces: the <strong>pyplot state machine</strong> (quick, procedural) and the <strong>object-oriented interface</strong> (precise, reproducible). Always use the OO interface for publication-quality charts.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">MATPLOTLIB — Line Chart (OO Interface)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#93c5fd;">epochs</span>      = np.arange(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">21</span>)
<span style="color:#93c5fd;">train_loss</span>  = <span style="color:#fcd34d;">1.0</span> / (epochs * <span style="color:#fcd34d;">0.4</span>) + np.random.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0.02</span>, <span style="color:#fcd34d;">20</span>)
<span style="color:#93c5fd;">val_loss</span>    = <span style="color:#fcd34d;">1.0</span> / (epochs * <span style="color:#fcd34d;">0.35</span>) + np.random.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0.03</span>, <span style="color:#fcd34d;">20</span>)

<span style="color:#6b7280;"># Object-oriented interface — create figure and axes explicitly</span>
<span style="color:#93c5fd;">fig</span>, <span style="color:#93c5fd;">ax</span> = plt.subplots(figsize=(<span style="color:#fcd34d;">9</span>, <span style="color:#fcd34d;">4</span>))

ax.plot(epochs, train_loss, label=<span style="color:#a7f3d0;">"Training Loss"</span>,   color=<span style="color:#a7f3d0;">"#3b82f6"</span>, linewidth=<span style="color:#fcd34d;">2</span>)
ax.plot(epochs, val_loss,   label=<span style="color:#a7f3d0;">"Validation Loss"</span>, color=<span style="color:#a7f3d0;">"#ef4444"</span>, linewidth=<span style="color:#fcd34d;">2</span>, linestyle=<span style="color:#a7f3d0;">"--"</span>)

<span style="color:#6b7280;"># Always label your plots!</span>
ax.set_title(<span style="color:#a7f3d0;">"Model Training Curves"</span>, fontsize=<span style="color:#fcd34d;">14</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax.set_xlabel(<span style="color:#a7f3d0;">"Epoch"</span>)
ax.set_ylabel(<span style="color:#a7f3d0;">"Loss"</span>)
ax.legend()
ax.grid(alpha=<span style="color:#fcd34d;">0.3</span>)

plt.tight_layout()
plt.savefig(<span style="color:#a7f3d0;">"training_curves.png"</span>, dpi=<span style="color:#fcd34d;">150</span>)
plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;padding:12px;font-size:0.8rem;">→ Outputs a line chart saved as training_curves.png</div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">MATPLOTLIB — Multiple Subplots in a Grid</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">rng</span>  = np.random.default_rng(<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">data</span> = rng.normal(<span style="color:#fcd34d;">100</span>, <span style="color:#fcd34d;">15</span>, <span style="color:#fcd34d;">500</span>)
<span style="color:#93c5fd;">cats</span> = [<span style="color:#a7f3d0;">"Dogs"</span>, <span style="color:#a7f3d0;">"Cats"</span>, <span style="color:#a7f3d0;">"Birds"</span>, <span style="color:#a7f3d0;">"Fish"</span>]
<span style="color:#93c5fd;">vals</span> = [<span style="color:#fcd34d;">45</span>, <span style="color:#fcd34d;">30</span>, <span style="color:#fcd34d;">15</span>, <span style="color:#fcd34d;">10</span>]

<span style="color:#6b7280;"># 2x2 grid of subplots — all on one canvas</span>
<span style="color:#93c5fd;">fig</span>, <span style="color:#93c5fd;">axes</span> = plt.subplots(<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">8</span>))

<span style="color:#6b7280;"># Histogram</span>
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>].hist(data, bins=<span style="color:#fcd34d;">30</span>, color=<span style="color:#a7f3d0;">"#6366f1"</span>, edgecolor=<span style="color:#a7f3d0;">"white"</span>, alpha=<span style="color:#fcd34d;">0.8</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Distribution Histogram"</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>].set_xlabel(<span style="color:#a7f3d0;">"Value"</span>)

<span style="color:#6b7280;"># Bar chart</span>
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].bar(cats, vals, color=[<span style="color:#a7f3d0;">"#3b82f6"</span>,<span style="color:#a7f3d0;">"#f59e0b"</span>,<span style="color:#a7f3d0;">"#10b981"</span>,<span style="color:#a7f3d0;">"#ef4444"</span>])
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Pets Survey"</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].set_ylabel(<span style="color:#a7f3d0;">"Percentage %"</span>)

<span style="color:#6b7280;"># Scatter plot</span>
x = rng.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">100</span>)
y = x * <span style="color:#fcd34d;">2.5</span> + rng.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">100</span>)
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>].scatter(x, y, alpha=<span style="color:#fcd34d;">0.5</span>, color=<span style="color:#a7f3d0;">"#8b5cf6"</span>)
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Scatter Plot"</span>)

<span style="color:#6b7280;"># Boxplot</span>
datasets = [rng.normal(mu, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">50</span>) <span style="color:#c4b5fd;">for</span> mu <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">60</span>, <span style="color:#fcd34d;">70</span>, <span style="color:#fcd34d;">80</span>, <span style="color:#fcd34d;">90</span>]]
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>].boxplot(datasets, labels=[<span style="color:#a7f3d0;">"G1"</span>,<span style="color:#a7f3d0;">"G2"</span>,<span style="color:#a7f3d0;">"G3"</span>,<span style="color:#a7f3d0;">"G4"</span>])
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Group Boxplots"</span>)

plt.suptitle(<span style="color:#a7f3d0;">"EDA Dashboard"</span>, fontsize=<span style="color:#fcd34d;">16</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout()
plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;padding:12px;font-size:0.8rem;">→ Outputs a 2×2 subplot grid: histogram, bar chart, scatter, and boxplots</div>
  </div>
</div>

<h3>Seaborn: Statistical Visualization</h3>
<p>Seaborn handles the statistical complexity automatically. Its most powerful charts for EDA are <code>heatmap</code> (correlations), <code>pairplot</code> (pairwise relationships), <code>violinplot</code> (distribution + IQR), and <code>regplot</code> (scatter + regression line).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SEABORN — Correlation Heatmap & Pairplot</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns
<span style="color:#c4b5fd;">import</span> pandas  <span style="color:#c4b5fd;">as</span> pd

<span style="color:#6b7280;"># Load a built-in dataset (seaborn ships with famous datasets)</span>
<span style="color:#93c5fd;">iris</span> = sns.load_dataset(<span style="color:#a7f3d0;">"iris"</span>)     <span style="color:#6b7280;"># 150 rows: 4 numeric features + species</span>

<span style="color:#6b7280;"># ── Correlation Heatmap ──────────────────────────────</span>
<span style="color:#93c5fd;">corr</span> = iris.drop(columns=<span style="color:#a7f3d0;">"species"</span>).corr()

<span style="color:#93c5fd;">fig</span>, <span style="color:#93c5fd;">ax</span> = plt.subplots(figsize=(<span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">5</span>))
sns.heatmap(
    corr,
    annot=<span style="color:#fca5a5;">True</span>,       <span style="color:#6b7280;"># Show values in each cell</span>
    fmt=<span style="color:#a7f3d0;">".2f"</span>,         <span style="color:#6b7280;"># 2 decimal places</span>
    cmap=<span style="color:#a7f3d0;">"coolwarm"</span>,   <span style="color:#6b7280;"># Blue = negative, Red = positive</span>
    vmin=<span style="color:#fcd34d;">-1</span>, vmax=<span style="color:#fcd34d;">1</span>,  <span style="color:#6b7280;"># Fix color scale to [-1, 1]</span>
    ax=ax
)
ax.set_title(<span style="color:#a7f3d0;">"Feature Correlation Matrix — Iris"</span>)
plt.show()

<span style="color:#6b7280;"># ── Pairplot — every feature vs every other feature ──</span>
sns.pairplot(iris, hue=<span style="color:#a7f3d0;">"species"</span>, diag_kind=<span style="color:#a7f3d0;">"kde"</span>)
plt.suptitle(<span style="color:#a7f3d0;">"Iris Pairplot by Species"</span>, y=<span style="color:#fcd34d;">1.02</span>)
plt.show()

<span style="color:#6b7280;"># ── Violin + Swarm combo ─────────────────────────────</span>
<span style="color:#93c5fd;">fig</span>, <span style="color:#93c5fd;">ax</span> = plt.subplots(figsize=(<span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">5</span>))
sns.violinplot(data=iris, x=<span style="color:#a7f3d0;">"species"</span>, y=<span style="color:#a7f3d0;">"petal_length"</span>, ax=ax, inner=<span style="color:#fca5a5;">None</span>, alpha=<span style="color:#fcd34d;">0.4</span>)
sns.stripplot(data=iris,  x=<span style="color:#a7f3d0;">"species"</span>, y=<span style="color:#a7f3d0;">"petal_length"</span>, ax=ax, size=<span style="color:#fcd34d;">3</span>, alpha=<span style="color:#fcd34d;">0.7</span>)
ax.set_title(<span style="color:#a7f3d0;">"Petal Length Distribution by Species"</span>)
plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;padding:12px;font-size:0.8rem;">→ Outputs correlation heatmap, pairplot grid, and violin+swarm chart</div>
  </div>
</div>

<h3>Chart Selection Guide</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:32px;">
  <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
    <thead>
      <tr style="background:rgba(0,0,0,0.2);">
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Goal</th>
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Best Chart</th>
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Function</th>
      </tr>
    </thead>
    <tbody>
      <tr style="border-top:1px solid var(--border);"><td style="padding:10px 16px;color:var(--text);">Show distribution</td><td style="padding:10px 16px;color:var(--muted);">Histogram / KDE</td><td style="padding:10px 16px;color:#a7f3d0;font-family:monospace;">ax.hist() / sns.histplot(kde=True)</td></tr>
      <tr style="border-top:1px solid var(--border);"><td style="padding:10px 16px;color:var(--text);">Compare categories</td><td style="padding:10px 16px;color:var(--muted);">Bar chart</td><td style="padding:10px 16px;color:#a7f3d0;font-family:monospace;">ax.bar() / sns.barplot()</td></tr>
      <tr style="border-top:1px solid var(--border);"><td style="padding:10px 16px;color:var(--text);">Trend over time</td><td style="padding:10px 16px;color:var(--muted);">Line chart</td><td style="padding:10px 16px;color:#a7f3d0;font-family:monospace;">ax.plot()</td></tr>
      <tr style="border-top:1px solid var(--border);"><td style="padding:10px 16px;color:var(--text);">Two numeric features</td><td style="padding:10px 16px;color:var(--muted);">Scatter plot</td><td style="padding:10px 16px;color:#a7f3d0;font-family:monospace;">ax.scatter() / sns.scatterplot()</td></tr>
      <tr style="border-top:1px solid var(--border);"><td style="padding:10px 16px;color:var(--text);">Outliers & spread</td><td style="padding:10px 16px;color:var(--muted);">Boxplot / Violin</td><td style="padding:10px 16px;color:#a7f3d0;font-family:monospace;">ax.boxplot() / sns.violinplot()</td></tr>
      <tr style="border-top:1px solid var(--border);"><td style="padding:10px 16px;color:var(--text);">Feature correlations</td><td style="padding:10px 16px;color:var(--muted);">Heatmap</td><td style="padding:10px 16px;color:#a7f3d0;font-family:monospace;">sns.heatmap(df.corr())</td></tr>
      <tr style="border-top:1px solid var(--border);"><td style="padding:10px 16px;color:var(--text);">All pairwise relationships</td><td style="padding:10px 16px;color:var(--muted);">Pairplot</td><td style="padding:10px 16px;color:#a7f3d0;font-family:monospace;">sns.pairplot(df, hue="label")</td></tr>
      <tr style="border-top:1px solid var(--border);"><td style="padding:10px 16px;color:var(--text);">Part of whole</td><td style="padding:10px 16px;color:var(--muted);">Pie / Donut</td><td style="padding:10px 16px;color:#a7f3d0;font-family:monospace;">ax.pie()</td></tr>
    </tbody>
  </table>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsModule->id,
            'title'       => '1.4 Visualization: Matplotlib & Seaborn Mastery',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L1_4', [
                ['q' => 'What does plt.subplots(2, 2) return?', 'opts' => ['A single Axes object', 'A list of 4 figures', 'A Figure and a 2x2 numpy array of Axes', '2 figures with 2 subplots each'], 'ans' => 2, 'exp' => 'plt.subplots(rows, cols) returns a Figure object and a numpy array of Axes. For (2,2) you get fig, axes where axes[0,0] is the top-left subplot.'],
                ['q' => 'What does sns.heatmap(df.corr(), annot=True) display?', 'opts' => ['Raw data heatmap', 'A correlation matrix with values annotated in each cell', 'A histogram for each column', 'A categorical frequency map'], 'ans' => 1, 'exp' => 'df.corr() computes pairwise Pearson correlations. sns.heatmap() visualizes it as a color matrix; annot=True writes the correlation values in each cell.'],
                ['q' => 'Which chart is best for showing the distribution AND outliers of a numeric variable?', 'opts' => ['Line chart', 'Bar chart', 'Pie chart', 'Boxplot or Violin plot'], 'ans' => 3, 'exp' => 'Boxplots show the median, IQR, and outliers. Violin plots additionally show the full probability distribution shape using KDE.'],
                ['q' => 'What is the difference between ax.set_title() and plt.suptitle()?', 'opts' => ['No difference', 'set_title() is for the whole figure, suptitle() is per subplot', 'suptitle() adds a title above the entire figure, set_title() is per-axis', 'suptitle() is only for seaborn'], 'ans' => 2, 'exp' => 'ax.set_title() sets the title of a specific subplot (axis). plt.suptitle() sets a super-title for the entire figure above all subplots.'],
                ['q' => 'What does plt.tight_layout() do?', 'opts' => ['Saves the figure', 'Automatically adjusts subplot spacing to prevent overlapping labels', 'Sets figure resolution', 'Increases font size'], 'ans' => 1, 'exp' => 'tight_layout() adjusts subplot parameters automatically so subplots, titles, and labels do not overlap. Always call it before savefig() or show().'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.5 — Statistics for Data Science
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Statistics for Data Science</h2>
<p>Statistics is the mathematical language of data science. Without it, you are just running code blindly. This lesson covers the statistical concepts that appear in every data science project: descriptive statistics, probability distributions, hypothesis testing, and correlation. These are not abstract — every time you evaluate a model or compare two groups, you are applying these concepts.</p>

<h3>Descriptive Statistics: Measuring Center & Spread</h3>
<p>Descriptive statistics summarize a dataset's key properties. The two fundamental questions are: <em>Where is the data centered?</em> and <em>How spread out is it?</em></p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">STATS — Center, Spread & Shape</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> scipy.stats <span style="color:#c4b5fd;">as</span> stats

<span style="color:#93c5fd;">salaries</span> = np.array([<span style="color:#fcd34d;">35000</span>, <span style="color:#fcd34d;">42000</span>, <span style="color:#fcd34d;">38000</span>, <span style="color:#fcd34d;">55000</span>, <span style="color:#fcd34d;">48000</span>,
                      <span style="color:#fcd34d;">62000</span>, <span style="color:#fcd34d;">41000</span>, <span style="color:#fcd34d;">250000</span>, <span style="color:#fcd34d;">39000</span>, <span style="color:#fcd34d;">44000</span>])
<span style="color:#6b7280;"># Note: 250,000 is an outlier — a CEO salary in the dataset</span>

<span style="color:#6b7280;"># Measures of Center</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mean:   {salaries.mean():,.0f}"</span>)     <span style="color:#6b7280;"># Pulled up by outlier</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Median: {np.median(salaries):,.0f}"</span>)  <span style="color:#6b7280;"># Robust to outlier</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mode:   {stats.mode(salaries).mode}"</span>)

<span style="color:#6b7280;"># Measures of Spread</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nStd Dev:  {salaries.std():,.0f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Variance: {salaries.var():,.0f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Range:    {salaries.ptp():,.0f}"</span>)

<span style="color:#93c5fd;">q1</span>, <span style="color:#93c5fd;">q3</span> = np.percentile(salaries, [<span style="color:#fcd34d;">25</span>, <span style="color:#fcd34d;">75</span>])
<span style="color:#93c5fd;">iqr</span>    = q3 - q1
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"IQR:      {iqr:,.0f}  (Q1={q1:,.0f}, Q3={q3:,.0f})"</span>)

<span style="color:#6b7280;"># Outlier detection using 1.5×IQR rule</span>
<span style="color:#93c5fd;">lower_fence</span> = q1 - <span style="color:#fcd34d;">1.5</span> * iqr
<span style="color:#93c5fd;">upper_fence</span> = q3 + <span style="color:#fcd34d;">1.5</span> * iqr
<span style="color:#93c5fd;">outliers</span>    = salaries[(salaries < lower_fence) | (salaries > upper_fence)]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nOutliers: {outliers}"</span>)

<span style="color:#6b7280;"># Shape — skewness and kurtosis</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Skewness: {stats.skew(salaries):.3f}"</span>)    <span style="color:#6b7280;"># >0 = right skewed</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Kurtosis: {stats.kurtosis(salaries):.3f}"</span>)  <span style="color:#6b7280;"># >0 = heavy tails</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>Mean:   65,400
Median: 43,000
Mode:   35000

Std Dev:  62,783
Variance: 3,941,740,000
Range:    215,000
IQR:      14,250  (Q1=39,000, Q3=53,250)

Outliers: [250000]
Skewness: 2.989
Kurtosis: 7.142</div>
  </div>
</div>

<h3>Probability Distributions</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">STATS — Key Distributions with scipy.stats</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats

<span style="color:#6b7280;"># Normal distribution: 68-95-99.7 rule</span>
<span style="color:#93c5fd;">norm</span> = stats.norm(loc=<span style="color:#fcd34d;">170</span>, scale=<span style="color:#fcd34d;">10</span>)  <span style="color:#6b7280;"># mean=170cm height, std=10</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"P(height > 180cm) = {1 - norm.cdf(180):.4f}"</span>)  <span style="color:#6b7280;"># CDF = area to left</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"P(160 < h < 180)  = {norm.cdf(180) - norm.cdf(160):.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"90th percentile   = {norm.ppf(0.90):.1f}cm"</span>)  <span style="color:#6b7280;"># PPF = inverse CDF</span>

<span style="color:#6b7280;"># Z-score: how many std devs from the mean?</span>
<span style="color:#93c5fd;">heights</span> = np.array([<span style="color:#fcd34d;">155</span>, <span style="color:#fcd34d;">170</span>, <span style="color:#fcd34d;">185</span>, <span style="color:#fcd34d;">195</span>])
<span style="color:#93c5fd;">z_scores</span> = (heights - <span style="color:#fcd34d;">170</span>) / <span style="color:#fcd34d;">10</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Z-scores: {z_scores}"</span>)   <span style="color:#6b7280;"># Z > 2 or < -2 → potential outlier</span>

<span style="color:#6b7280;"># Binomial: P(X successes in n trials with probability p)</span>
<span style="color:#93c5fd;">binom</span> = stats.binom(n=<span style="color:#fcd34d;">10</span>, p=<span style="color:#fcd34d;">0.3</span>)        <span style="color:#6b7280;"># 10 coin flips, 30% chance each</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"P(exactly 3 successes) = {binom.pmf(3):.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"P(≤3 successes)        = {binom.cdf(3):.4f}"</span>)

<span style="color:#6b7280;"># Poisson: events per unit time/space (λ = average rate)</span>
<span style="color:#93c5fd;">poisson</span> = stats.poisson(mu=<span style="color:#fcd34d;">5</span>)           <span style="color:#6b7280;"># avg 5 support tickets/hour</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"P(8 tickets in one hour) = {poisson.pmf(8):.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>P(height > 180cm) = 0.1587
P(160 < h < 180)  = 0.6827
90th percentile   = 182.8cm
Z-scores: [-1.5  0.   1.5  2.5]
P(exactly 3 successes) = 0.2668
P(≤3 successes)        = 0.6496
P(8 tickets in one hour) = 0.0653</div>
  </div>
</div>

<h3>Hypothesis Testing</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">STATS — t-Test, ANOVA, Chi-Square</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">rng</span> = np.random.default_rng(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Scenario: Do Model A and Model B have different accuracy distributions?</span>
<span style="color:#93c5fd;">model_a</span> = rng.normal(<span style="color:#fcd34d;">0.88</span>, <span style="color:#fcd34d;">0.03</span>, <span style="color:#fcd34d;">30</span>)   <span style="color:#6b7280;"># 30 cross-val runs</span>
<span style="color:#93c5fd;">model_b</span> = rng.normal(<span style="color:#fcd34d;">0.91</span>, <span style="color:#fcd34d;">0.03</span>, <span style="color:#fcd34d;">30</span>)

<span style="color:#6b7280;"># Independent samples t-test</span>
<span style="color:#93c5fd;">t_stat</span>, <span style="color:#93c5fd;">p_value</span> = stats.ttest_ind(model_a, model_b)
<span style="color:#93c5fd;">alpha</span> = <span style="color:#fcd34d;">0.05</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"t = {t_stat:.3f}, p = {p_value:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Reject H0 (significant difference)? {p_value < alpha}"</span>)

<span style="color:#6b7280;"># Pearson correlation coefficient</span>
<span style="color:#93c5fd;">hours_studied</span> = np.array([<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">7</span>])
<span style="color:#93c5fd;">exam_scores</span>   = np.array([<span style="color:#fcd34d;">55</span>, <span style="color:#fcd34d;">62</span>, <span style="color:#fcd34d;">74</span>, <span style="color:#fcd34d;">81</span>, <span style="color:#fcd34d;">92</span>, <span style="color:#fcd34d;">58</span>, <span style="color:#fcd34d;">69</span>, <span style="color:#fcd34d;">78</span>])
<span style="color:#93c5fd;">r</span>, <span style="color:#93c5fd;">p</span> = stats.pearsonr(hours_studied, exam_scores)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nPearson r = {r:.4f}, p = {p:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"R² = {r**2:.4f} — {r**2:.1%} of variance explained"</span>)

<span style="color:#6b7280;"># Chi-Square test for independence (categorical variables)</span>
<span style="color:#6b7280;"># Observed: [clicked_ad, not_clicked] × [bought, not_bought]</span>
<span style="color:#93c5fd;">contingency</span> = np.array([[<span style="color:#fcd34d;">250</span>, <span style="color:#fcd34d;">750</span>],  <span style="color:#6b7280;"># clicked ad</span>
                          [<span style="color:#fcd34d;">100</span>, <span style="color:#fcd34d;">900</span>]]) <span style="color:#6b7280;"># did not click</span>
<span style="color:#93c5fd;">chi2</span>, <span style="color:#93c5fd;">p_chi</span>, <span style="color:#93c5fd;">dof</span>, <span style="color:#93c5fd;">expected</span> = stats.chi2_contingency(contingency)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nChi² = {chi2:.2f}, p = {p_chi:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Ad click and purchase are related? {p_chi < 0.05}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>t = -4.021, p = 0.0002
Reject H0 (significant difference)? True

Pearson r = 0.9936, p = 0.0000
R² = 0.9872 — 98.7% of variance explained

Chi² = 47.56, p = 0.0000
Ad click and purchase are related? True</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsModule->id,
            'title'       => '1.5 Statistics: Distributions, Hypothesis Testing & Correlation',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L1_5', [
                ['q' => 'Why is the median more robust to outliers than the mean?', 'opts' => ['It is always smaller than the mean', 'It only uses the middle value(s), not all extreme values', 'It uses the mode internally', 'It is calculated using standard deviation'], 'ans' => 1, 'exp' => 'The median is simply the middle value when sorted. Extreme outliers cannot pull it like they pull the mean, which uses all values arithmetically.'],
                ['q' => 'A Z-score of -2.5 means a data point is...', 'opts' => ['2.5 units below the mean', '2.5 standard deviations below the mean', '2.5% below average', 'In the top 2.5%'], 'ans' => 1, 'exp' => 'Z = (x - mean) / std. A Z-score tells you how many standard deviations a value is from the mean. -2.5 means 2.5 std devs BELOW. Values with |Z| > 2 are often flagged as outliers.'],
                ['q' => 'In a hypothesis test, what does a p-value of 0.02 with α=0.05 mean?', 'opts' => ['Accept the null hypothesis', 'The result is 2% accurate', 'Reject the null hypothesis — result is statistically significant', 'Run more tests'], 'ans' => 2, 'exp' => 'p < α (0.02 < 0.05) means we reject H0. The probability of observing this data if H0 were true is only 2% — low enough to conclude the effect is real.'],
                ['q' => 'What does a Pearson r of 0.99 mean?', 'opts' => ['No correlation', 'Weak positive correlation', 'Very strong positive linear correlation', 'Perfect inverse correlation'], 'ans' => 2, 'exp' => 'Pearson r ranges from -1 to 1. Values near ±1 indicate near-perfect linear relationships. r=0.99 means nearly all variance in Y is explained by a linear trend with X.'],
                ['q' => 'What is the IQR used for in outlier detection?', 'opts' => ['Measuring skewness', 'The 1.5×IQR rule flags points beyond Q1-1.5×IQR or Q3+1.5×IQR as outliers', 'Normalizing data', 'Testing hypothesis'], 'ans' => 1, 'exp' => 'The IQR (Q3-Q1) captures the middle 50% of data. The 1.5×IQR fence rule (Tukey\'s) is the standard boxplot outlier detection method.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.6 — Feature Engineering & Preprocessing
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Feature Engineering & Data Preprocessing</h2>
<p>Feature engineering is the art of transforming raw data into representations that machine learning algorithms can learn from effectively. It is widely considered the most impactful step in a data science pipeline — better features beat better algorithms. This lesson covers the full preprocessing toolkit.</p>

<h3>Scaling: Normalization vs Standardization</h3>
<p>Many ML algorithms (linear regression, neural networks, SVMs, k-NN) are sensitive to feature scale. A salary feature in the range [30,000–200,000] will dominate an age feature in [18–80] if left unscaled.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PREPROCESSING — Scaling Methods</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> MinMaxScaler, StandardScaler, RobustScaler

<span style="color:#93c5fd;">salaries</span> = np.array([[<span style="color:#fcd34d;">35000</span>], [<span style="color:#fcd34d;">75000</span>], [<span style="color:#fcd34d;">120000</span>], [<span style="color:#fcd34d;">50000</span>], [<span style="color:#fcd34d;">95000</span>]])

<span style="color:#6b7280;"># Min-Max Normalization: scales to [0, 1]
# Formula: (x - min) / (max - min)
# Use when: you need bounded output, neural networks</span>
<span style="color:#93c5fd;">minmax</span> = MinMaxScaler()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"MinMax scaled:"</span>, minmax.fit_transform(salaries).flatten())

<span style="color:#6b7280;"># Standard Scaling (Z-score): mean=0, std=1
# Formula: (x - mean) / std
# Use when: algorithm assumes Gaussian features, most ML models</span>
<span style="color:#93c5fd;">stdscaler</span> = StandardScaler()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"StandardScaler:"</span>, stdscaler.fit_transform(salaries).flatten().round(<span style="color:#fcd34d;">3</span>))

<span style="color:#6b7280;"># Robust Scaler: uses median and IQR — resistant to outliers
# Use when: data has significant outliers</span>
<span style="color:#93c5fd;">robust</span> = RobustScaler()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"RobustScaler: "</span>, robust.fit_transform(salaries).flatten().round(<span style="color:#fcd34d;">3</span>))

<span style="color:#6b7280;"># Manual implementation — understanding the math</span>
<span style="color:#93c5fd;">x</span> = salaries.flatten().astype(float)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Manual MinMax:"</span>, ((x - x.min()) / (x.max() - x.min())).round(<span style="color:#fcd34d;">3</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>MinMax scaled:  [0.     0.471  1.     0.176  0.706]
StandardScaler: [-1.297  0.162  1.568 -0.729  0.297]
RobustScaler:  [-0.727  0.364  1.636 -0.364  0.909]
Manual MinMax:  [0.    0.471 1.    0.176 0.706]</div>
  </div>
</div>

<h3>Encoding Categorical Variables</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PREPROCESSING — Label & One-Hot Encoding</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> LabelEncoder, OrdinalEncoder

<span style="color:#93c5fd;">df</span> = pd.DataFrame({
    <span style="color:#a7f3d0;">"city"</span>     : [<span style="color:#a7f3d0;">"Cebu"</span>, <span style="color:#a7f3d0;">"Manila"</span>, <span style="color:#a7f3d0;">"Davao"</span>, <span style="color:#a7f3d0;">"Cebu"</span>, <span style="color:#a7f3d0;">"Manila"</span>],
    <span style="color:#a7f3d0;">"education"</span>: [<span style="color:#a7f3d0;">"High School"</span>, <span style="color:#a7f3d0;">"College"</span>, <span style="color:#a7f3d0;">"Masters"</span>, <span style="color:#a7f3d0;">"College"</span>, <span style="color:#a7f3d0;">"PhD"</span>],
    <span style="color:#a7f3d0;">"salary"</span>   : [<span style="color:#fcd34d;">30000</span>, <span style="color:#fcd34d;">55000</span>, <span style="color:#fcd34d;">75000</span>, <span style="color:#fcd34d;">35000</span>, <span style="color:#fcd34d;">95000</span>]
})

<span style="color:#6b7280;"># One-Hot Encoding (OHE) — for NOMINAL categories (no order)
# Creates a binary column per category. Best for "city" (no order)</span>
<span style="color:#93c5fd;">df_ohe</span> = pd.get_dummies(df, columns=[<span style="color:#a7f3d0;">"city"</span>], drop_first=<span style="color:#fca5a5;">True</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"After OHE:\n"</span>, df_ohe)

<span style="color:#6b7280;"># Ordinal Encoding — for ORDINAL categories (meaningful order)
# Education has a clear order: HS < College < Masters < PhD</span>
<span style="color:#93c5fd;">enc</span> = OrdinalEncoder(categories=[[<span style="color:#a7f3d0;">"High School"</span>, <span style="color:#a7f3d0;">"College"</span>, <span style="color:#a7f3d0;">"Masters"</span>, <span style="color:#a7f3d0;">"PhD"</span>]])
<span style="color:#93c5fd;">df</span>[<span style="color:#a7f3d0;">"edu_encoded"</span>] = enc.fit_transform(df[[<span style="color:#a7f3d0;">"education"</span>]])
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nOrdinal encoded education:"</span>)
<span style="color:#93c5fd;">print</span>(df[[<span style="color:#a7f3d0;">"education"</span>, <span style="color:#a7f3d0;">"edu_encoded"</span>]])</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>After OHE:
   education  salary  city_Cebu  city_Manila
0  High School   30000       True       False
1      College   55000      False        True
...
Ordinal encoded education:
   education  edu_encoded
0  High School          0.0
1      College          1.0
2      Masters          2.0
3      College          1.0
4          PhD          3.0</div>
  </div>
</div>

<h3>Feature Engineering Techniques</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FEATURE ENGINEERING — Creating Powerful Features</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">df</span> = pd.DataFrame({
    <span style="color:#a7f3d0;">"birth_year"</span>  : [<span style="color:#fcd34d;">1990</span>, <span style="color:#fcd34d;">1985</span>, <span style="color:#fcd34d;">2000</span>, <span style="color:#fcd34d;">1978</span>, <span style="color:#fcd34d;">1995</span>],
    <span style="color:#a7f3d0;">"height_cm"</span>   : [<span style="color:#fcd34d;">170</span>, <span style="color:#fcd34d;">165</span>, <span style="color:#fcd34d;">182</span>, <span style="color:#fcd34d;">158</span>, <span style="color:#fcd34d;">175</span>],
    <span style="color:#a7f3d0;">"weight_kg"</span>   : [<span style="color:#fcd34d;">70</span>, <span style="color:#fcd34d;">85</span>, <span style="color:#fcd34d;">78</span>, <span style="color:#fcd34d;">60</span>, <span style="color:#fcd34d;">90</span>],
    <span style="color:#a7f3d0;">"income"</span>      : [<span style="color:#fcd34d;">40000</span>, <span style="color:#fcd34d;">55000</span>, <span style="color:#fcd34d;">30000</span>, <span style="color:#fcd34d;">70000</span>, <span style="color:#fcd34d;">45000</span>],
    <span style="color:#a7f3d0;">"expenses"</span>    : [<span style="color:#fcd34d;">30000</span>, <span style="color:#fcd34d;">42000</span>, <span style="color:#fcd34d;">28000</span>, <span style="color:#fcd34d;">38000</span>, <span style="color:#fcd34d;">50000</span>]
})

<span style="color:#6b7280;"># Derived features from existing ones</span>
<span style="color:#93c5fd;">df</span>[<span style="color:#a7f3d0;">"age"</span>]          = <span style="color:#fcd34d;">2024</span> - df[<span style="color:#a7f3d0;">"birth_year"</span>]
<span style="color:#93c5fd;">df</span>[<span style="color:#a7f3d0;">"bmi"</span>]          = df[<span style="color:#a7f3d0;">"weight_kg"</span>] / (df[<span style="color:#a7f3d0;">"height_cm"</span>] / <span style="color:#fcd34d;">100</span>) ** <span style="color:#fcd34d;">2</span>
<span style="color:#93c5fd;">df</span>[<span style="color:#a7f3d0;">"savings"</span>]      = df[<span style="color:#a7f3d0;">"income"</span>] - df[<span style="color:#a7f3d0;">"expenses"</span>]
<span style="color:#93c5fd;">df</span>[<span style="color:#a7f3d0;">"savings_rate"</span>] = df[<span style="color:#a7f3d0;">"savings"</span>] / df[<span style="color:#a7f3d0;">"income"</span>]

<span style="color:#6b7280;"># Binning — convert continuous to ordinal category</span>
<span style="color:#93c5fd;">df</span>[<span style="color:#a7f3d0;">"age_group"</span>] = pd.cut(
    df[<span style="color:#a7f3d0;">"age"</span>],
    bins=[<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">30</span>, <span style="color:#fcd34d;">40</span>, <span style="color:#fcd34d;">55</span>, <span style="color:#fcd34d;">100</span>],
    labels=[<span style="color:#a7f3d0;">"Young"</span>, <span style="color:#a7f3d0;">"Mid"</span>, <span style="color:#a7f3d0;">"Senior"</span>, <span style="color:#a7f3d0;">"Elder"</span>]
)

<span style="color:#6b7280;"># Log transform — compress right-skewed distributions</span>
<span style="color:#93c5fd;">df</span>[<span style="color:#a7f3d0;">"log_income"</span>] = np.log1p(df[<span style="color:#a7f3d0;">"income"</span>])  <span style="color:#6b7280;"># log(1+x) to handle zeros</span>

<span style="color:#93c5fd;">print</span>(df[[<span style="color:#a7f3d0;">"age"</span>, <span style="color:#a7f3d0;">"bmi"</span>, <span style="color:#a7f3d0;">"savings_rate"</span>, <span style="color:#a7f3d0;">"age_group"</span>]].round(<span style="color:#fcd34d;">3</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>   age     bmi  savings_rate age_group
0   34  24.221         0.250       Mid
1   39  31.225         0.236       Mid
2   24  23.543         0.067     Young
3   46  24.040         0.457    Senior
4   29  29.388        -0.111     Young</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsModule->id,
            'title'       => '1.6 Feature Engineering: Scaling, Encoding & Transformation',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L1_6', [
                ['q' => 'When should you use StandardScaler over MinMaxScaler?', 'opts' => ['When output must be in [0,1]', 'When data has outliers', 'When your algorithm assumes Gaussian-distributed features', 'When all values are positive'], 'ans' => 2, 'exp' => 'StandardScaler (Z-score normalization) works best when your algorithm assumes normally distributed inputs (e.g., logistic regression, SVM, PCA). MinMaxScaler is preferred for neural networks.'],
                ['q' => 'Why is One-Hot Encoding used for nominal categorical features?', 'opts' => ['It is faster', 'It compresses memory', 'Nominal categories have no natural order, so numeric labels would imply false ordering', 'It always improves accuracy'], 'ans' => 2, 'exp' => 'If you label-encode "Cebu=0, Manila=1, Davao=2", the model might infer Manila > Cebu, which is meaningless. OHE creates binary flags that impose no ordering.'],
                ['q' => 'What does np.log1p(x) compute and why use it?', 'opts' => ['log(x-1), to remove negatives', 'log(1+x), to safely handle zeros and compress right-skewed data', 'log2(x), for binary logarithm', '10^x, the inverse log'], 'ans' => 1, 'exp' => 'log1p computes log(1+x) which handles x=0 gracefully (log(0) is undefined). Log transforms compress large values and bring right-skewed distributions closer to normal.'],
                ['q' => 'What is "binning" in feature engineering?', 'opts' => ['Removing outliers', 'Scaling to [0,1]', 'Converting a continuous variable into ordered categorical ranges', 'Removing duplicate rows'], 'ans' => 2, 'exp' => 'Binning (discretization) converts continuous numeric values into categorical bins. pd.cut() for custom ranges or pd.qcut() for equal-frequency quantile bins.'],
                ['q' => 'Why does RobustScaler outperform StandardScaler in datasets with outliers?', 'opts' => ['It is more accurate', 'It uses the median and IQR instead of mean and std — resistant to extreme values', 'It is faster to compute', 'It outputs values in [0,1]'], 'ans' => 1, 'exp' => 'The mean and std are heavily influenced by outliers. RobustScaler uses the median and IQR, which represent the central tendency of the data without being pulled by extremes.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.7 — Exploratory Data Analysis (EDA)
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Exploratory Data Analysis (EDA)</h2>
<p>Exploratory Data Analysis is the process of investigating a dataset to summarize its main characteristics, uncover patterns, detect anomalies, and test hypotheses — primarily through visual methods. EDA should be done <strong>before</strong> any modelling. Rushing to models without EDA is one of the most common mistakes beginners make. You must understand your data before you ask algorithms to learn from it.</p>

<h3>The EDA Checklist</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:20px;margin-bottom:32px;">
  <ol style="color:var(--muted);line-height:2.5;margin-left:1.5rem;list-style:decimal;">
    <li><strong style="color:var(--text);">Shape & Types</strong> — df.shape, df.dtypes, df.info()</li>
    <li><strong style="color:var(--text);">Missing Values</strong> — df.isnull().sum(), heatmap of nulls</li>
    <li><strong style="color:var(--text);">Descriptive Statistics</strong> — df.describe(include="all")</li>
    <li><strong style="color:var(--text);">Target Variable</strong> — distribution, class balance</li>
    <li><strong style="color:var(--text);">Univariate Analysis</strong> — histogram/KDE for each numeric feature</li>
    <li><strong style="color:var(--text);">Bivariate Analysis</strong> — feature vs target scatter, boxplot per class</li>
    <li><strong style="color:var(--text);">Multivariate Analysis</strong> — correlation heatmap, pairplot</li>
    <li><strong style="color:var(--text);">Outlier Detection</strong> — boxplots, Z-score > 3, IQR rule</li>
    <li><strong style="color:var(--text);">Feature Distributions</strong> — identify skewed features, check normality</li>
    <li><strong style="color:var(--text);">Categorical Analysis</strong> — value_counts(), bar charts by category</li>
  </ol>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">EDA — Complete Exploratory Analysis on Titanic Dataset</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> pandas  <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> numpy   <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt

<span style="color:#6b7280;"># Load the famous Titanic dataset</span>
<span style="color:#93c5fd;">titanic</span> = sns.load_dataset(<span style="color:#a7f3d0;">"titanic"</span>)

<span style="color:#6b7280;"># ── STEP 1: Shape & Overview ─────────────────────────</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Shape:"</span>, titanic.shape)
<span style="color:#93c5fd;">print</span>(titanic.dtypes)

<span style="color:#6b7280;"># ── STEP 2: Missing Values ───────────────────────────</span>
<span style="color:#93c5fd;">null_pct</span> = (titanic.isnull().sum() / <span style="color:#93c5fd;">len</span>(titanic) * <span style="color:#fcd34d;">100</span>).round(<span style="color:#fcd34d;">1</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nMissing % per column:"</span>)
<span style="color:#93c5fd;">print</span>(null_pct[null_pct > <span style="color:#fcd34d;">0</span>])

<span style="color:#6b7280;"># ── STEP 3: Target distribution ─────────────────────</span>
<span style="color:#93c5fd;">survival_counts</span> = titanic[<span style="color:#a7f3d0;">"survived"</span>].value_counts(normalize=<span style="color:#fca5a5;">True</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nSurvival rate:"</span>, survival_counts.to_dict())

<span style="color:#6b7280;"># ── STEP 4: Bivariate — Survival by sex & class ──────</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nSurvival rate by sex:"</span>)
<span style="color:#93c5fd;">print</span>(titanic.groupby(<span style="color:#a7f3d0;">"sex"</span>)[<span style="color:#a7f3d0;">"survived"</span>].mean().round(<span style="color:#fcd34d;">3</span>))

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nSurvival rate by pclass:"</span>)
<span style="color:#93c5fd;">print</span>(titanic.groupby(<span style="color:#a7f3d0;">"pclass"</span>)[<span style="color:#a7f3d0;">"survived"</span>].mean().round(<span style="color:#fcd34d;">3</span>))

<span style="color:#6b7280;"># ── STEP 5: Visualize ────────────────────────────────</span>
<span style="color:#93c5fd;">fig</span>, <span style="color:#93c5fd;">axes</span> = plt.subplots(<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">8</span>))

<span style="color:#6b7280;"># Age distribution by survival</span>
sns.histplot(data=titanic, x=<span style="color:#a7f3d0;">"age"</span>, hue=<span style="color:#a7f3d0;">"survived"</span>, kde=<span style="color:#fca5a5;">True</span>, ax=axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>], bins=<span style="color:#fcd34d;">30</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Age Distribution by Survival"</span>)

<span style="color:#6b7280;"># Survival by sex</span>
sns.barplot(data=titanic, x=<span style="color:#a7f3d0;">"sex"</span>, y=<span style="color:#a7f3d0;">"survived"</span>, ax=axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>])
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Survival Rate by Sex"</span>)

<span style="color:#6b7280;"># Fare distribution</span>
sns.boxplot(data=titanic, x=<span style="color:#a7f3d0;">"pclass"</span>, y=<span style="color:#a7f3d0;">"fare"</span>, ax=axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>])
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Fare by Passenger Class"</span>)

<span style="color:#6b7280;"># Correlation heatmap of numeric features</span>
<span style="color:#93c5fd;">num_cols</span> = titanic[[<span style="color:#a7f3d0;">"survived"</span>,<span style="color:#a7f3d0;">"pclass"</span>,<span style="color:#a7f3d0;">"age"</span>,<span style="color:#a7f3d0;">"fare"</span>,<span style="color:#a7f3d0;">"sibsp"</span>,<span style="color:#a7f3d0;">"parch"</span>]]
sns.heatmap(num_cols.corr(), annot=<span style="color:#fca5a5;">True</span>, fmt=<span style="color:#a7f3d0;">".2f"</span>, cmap=<span style="color:#a7f3d0;">"coolwarm"</span>, ax=axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>])
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Correlation Heatmap"</span>)

plt.suptitle(<span style="color:#a7f3d0;">"Titanic EDA Dashboard"</span>, fontsize=<span style="color:#fcd34d;">16</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout()
plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>Shape: (891, 15)
Missing % per column:
age        19.9
embarked    0.2
deck       77.2

Survival rate: {0: 0.616, 1: 0.384}

Survival rate by sex:
sex
female    0.742
male      0.189

Survival rate by pclass:
pclass
1    0.630
2    0.473
3    0.242</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsModule->id,
            'title'       => '1.7 EDA: Exploratory Data Analysis in Practice',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L1_7', [
                ['q' => 'What should you do BEFORE building any machine learning model?', 'opts' => ['Deploy to production', 'Tune hyperparameters', 'Perform EDA to understand the data', 'Select the deepest neural network'], 'ans' => 2, 'exp' => 'EDA is the essential first step. You must understand distributions, missing values, outliers, correlations, and class imbalance before any modelling.'],
                ['q' => 'What does value_counts(normalize=True) return?', 'opts' => ['Raw counts per category', 'Relative frequencies (proportions) per category', 'Sorted unique values', 'A boolean mask'], 'ans' => 1, 'exp' => 'normalize=True converts raw counts to proportions that sum to 1.0, giving you the relative frequency / percentage of each category.'],
                ['q' => 'A dataset has 77% missing values in a column. What should you do?', 'opts' => ['Mean impute all', 'Use KNN imputation', 'Most likely drop the column — too little data to reliably impute', 'Forward fill'], 'ans' => 2, 'exp' => 'As a rule of thumb, columns with >50-70% missing data are often dropped. Imputing from 23% real values introduces too much synthetic information. Context matters — always investigate why the data is missing.'],
                ['q' => 'What does a correlation of -0.87 between pclass and fare tell us?', 'opts' => ['No relationship', 'Strong positive: higher class = higher fare', 'Strong negative: higher pclass number (lower class) = lower fare', 'Weak inverse relationship'], 'ans' => 2, 'exp' => 'pclass uses 1=First, 2=Second, 3=Third. A strong negative correlation with fare means pclass=1 has the highest fares — which makes intuitive sense.'],
                ['q' => 'How do you get descriptive statistics for ALL columns including categoricals?', 'opts' => ['df.stats()', 'df.describe()', 'df.describe(include="all")', 'df.summary()'], 'ans' => 2, 'exp' => 'df.describe() by default only shows numeric columns. df.describe(include="all") adds count, unique, top, and freq stats for object/categorical columns too.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.8 — Machine Learning Fundamentals
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Machine Learning Fundamentals</h2>
<p>Machine learning is the process of training a computational model to make predictions or decisions from data, rather than following explicitly programmed rules. This lesson covers the conceptual and practical foundations that underlie every ML algorithm you will use.</p>

<h3>The Bias-Variance Tradeoff</h3>
<p>The most important theoretical concept in machine learning. Every model makes errors, and those errors decompose into three components:</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:#ef4444;">Bias</strong> — Error from wrong assumptions (underfitting). A model too simple to capture the true relationship.</li>
  <li><strong style="color:#3b82f6;">Variance</strong> — Error from sensitivity to small fluctuations in training data (overfitting). A model that memorizes noise.</li>
  <li><strong style="color:#10b981;">Irreducible Error</strong> — Noise inherent in the data that no model can eliminate.</li>
</ul>
<p>The goal is to find the sweet spot where total error is minimized — complex enough to capture patterns, simple enough to generalize.</p>

<h3>Train/Validation/Test Split</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">ML — Data Splitting & Cross-Validation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> train_test_split, cross_val_score
<span style="color:#c4b5fd;">from</span> sklearn.datasets       <span style="color:#c4b5fd;">import</span> make_classification
<span style="color:#c4b5fd;">from</span> sklearn.tree           <span style="color:#c4b5fd;">import</span> DecisionTreeClassifier
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#6b7280;"># Generate a synthetic classification dataset</span>
<span style="color:#93c5fd;">X</span>, <span style="color:#93c5fd;">y</span> = make_classification(
    n_samples=<span style="color:#fcd34d;">1000</span>, n_features=<span style="color:#fcd34d;">10</span>, n_informative=<span style="color:#fcd34d;">5</span>,
    random_state=<span style="color:#fcd34d;">42</span>
)

<span style="color:#6b7280;"># Standard 80/20 split</span>
<span style="color:#93c5fd;">X_train</span>, <span style="color:#93c5fd;">X_test</span>, <span style="color:#93c5fd;">y_train</span>, <span style="color:#93c5fd;">y_test</span> = train_test_split(
    X, y, test_size=<span style="color:#fcd34d;">0.2</span>, random_state=<span style="color:#fcd34d;">42</span>, stratify=y
)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Train: {X_train.shape}, Test: {X_test.shape}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Train class balance: {np.bincount(y_train) / len(y_train)}"</span>)

<span style="color:#6b7280;"># K-Fold Cross-Validation — the gold standard for model evaluation</span>
<span style="color:#93c5fd;">clf</span>    = DecisionTreeClassifier(random_state=<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">scores</span> = cross_val_score(clf, X, y, cv=<span style="color:#fcd34d;">5</span>, scoring=<span style="color:#a7f3d0;">"accuracy"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n5-Fold CV Scores: {scores.round(3)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mean: {scores.mean():.3f} ± {scores.std():.3f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>Train: (800, 10), Test: (200, 10)
Train class balance: [0.5 0.5]
5-Fold CV Scores: [0.875 0.915 0.895 0.885 0.900]
Mean: 0.894 ± 0.013</div>
  </div>
</div>

<h3>Classification: Logistic Regression, Decision Tree & Random Forest</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SKLEARN — Classification Pipeline</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.linear_model    <span style="color:#c4b5fd;">import</span> LogisticRegression
<span style="color:#c4b5fd;">from</span> sklearn.ensemble        <span style="color:#c4b5fd;">import</span> RandomForestClassifier
<span style="color:#c4b5fd;">from</span> sklearn.metrics         <span style="color:#c4b5fd;">import</span> (accuracy_score, classification_report,
                                          confusion_matrix, roc_auc_score)
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing   <span style="color:#c4b5fd;">import</span> StandardScaler

<span style="color:#6b7280;"># Scale features for logistic regression</span>
<span style="color:#93c5fd;">scaler</span>    = StandardScaler()
<span style="color:#93c5fd;">X_train_s</span> = scaler.fit_transform(X_train)   <span style="color:#6b7280;"># Fit on train, transform train</span>
<span style="color:#93c5fd;">X_test_s</span>  = scaler.transform(X_test)         <span style="color:#6b7280;"># Transform test (never fit!)</span>

<span style="color:#6b7280;"># Train models</span>
<span style="color:#93c5fd;">models</span> = {
    <span style="color:#a7f3d0;">"Logistic Regression"</span>: LogisticRegression(max_iter=<span style="color:#fcd34d;">1000</span>),
    <span style="color:#a7f3d0;">"Decision Tree"</span>      : DecisionTreeClassifier(max_depth=<span style="color:#fcd34d;">5</span>),
    <span style="color:#a7f3d0;">"Random Forest"</span>      : RandomForestClassifier(n_estimators=<span style="color:#fcd34d;">100</span>, random_state=<span style="color:#fcd34d;">42</span>),
}

<span style="color:#c4b5fd;">for</span> name, model <span style="color:#c4b5fd;">in</span> models.items():
    <span style="color:#93c5fd;">Xt</span> = X_train_s <span style="color:#c4b5fd;">if</span> name == <span style="color:#a7f3d0;">"Logistic Regression"</span> <span style="color:#c4b5fd;">else</span> X_train
    <span style="color:#93c5fd;">Xv</span> = X_test_s  <span style="color:#c4b5fd;">if</span> name == <span style="color:#a7f3d0;">"Logistic Regression"</span> <span style="color:#c4b5fd;">else</span> X_test
    model.fit(Xt, y_train)
    <span style="color:#93c5fd;">y_pred</span> = model.predict(Xv)
    <span style="color:#93c5fd;">auc</span>    = roc_auc_score(y_test, model.predict_proba(Xv)[:,<span style="color:#fcd34d;">1</span>])
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{name:22} | Acc: {accuracy_score(y_test,y_pred):.3f} | AUC: {auc:.3f}"</span>)

<span style="color:#6b7280;"># Detailed classification report for best model</span>
<span style="color:#93c5fd;">rf</span>     = models[<span style="color:#a7f3d0;">"Random Forest"</span>]
<span style="color:#93c5fd;">y_pred</span> = rf.predict(X_test)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nRandom Forest Report:"</span>)
<span style="color:#93c5fd;">print</span>(classification_report(y_test, y_pred))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>Logistic Regression    | Acc: 0.880 | AUC: 0.952
Decision Tree          | Acc: 0.875 | AUC: 0.875
Random Forest          | Acc: 0.925 | AUC: 0.976

Random Forest Report:
              precision    recall  f1-score   support
           0       0.93      0.91      0.92       100
           1       0.91      0.94      0.93       100
    accuracy                           0.92       200</div>
  </div>
</div>

<h3>Regression: Linear & Polynomial</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SKLEARN — Regression & Evaluation Metrics</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.linear_model  <span style="color:#c4b5fd;">import</span> LinearRegression
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> PolynomialFeatures
<span style="color:#c4b5fd;">from</span> sklearn.pipeline      <span style="color:#c4b5fd;">import</span> Pipeline
<span style="color:#c4b5fd;">from</span> sklearn.metrics       <span style="color:#c4b5fd;">import</span> mean_squared_error, r2_score
<span style="color:#c4b5fd;">from</span> sklearn.datasets      <span style="color:#c4b5fd;">import</span> make_regression

<span style="color:#93c5fd;">X</span>, <span style="color:#93c5fd;">y</span> = make_regression(n_samples=<span style="color:#fcd34d;">200</span>, n_features=<span style="color:#fcd34d;">1</span>, noise=<span style="color:#fcd34d;">20</span>, random_state=<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">X_tr</span>, <span style="color:#93c5fd;">X_te</span>, <span style="color:#93c5fd;">y_tr</span>, <span style="color:#93c5fd;">y_te</span> = train_test_split(X, y, test_size=<span style="color:#fcd34d;">0.2</span>, random_state=<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Linear Regression</span>
<span style="color:#93c5fd;">lr</span> = LinearRegression()
<span style="color:#93c5fd;">lr</span>.fit(X_tr, y_tr)
<span style="color:#93c5fd;">y_pred_lr</span> = lr.predict(X_te)

<span style="color:#93c5fd;">rmse</span> = mean_squared_error(y_te, y_pred_lr, squared=<span style="color:#fca5a5;">False</span>)
<span style="color:#93c5fd;">r2</span>   = r2_score(y_te, y_pred_lr)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Linear Regression:  RMSE={rmse:.2f}, R²={r2:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Coefficient: {lr.coef_[0]:.3f}, Intercept: {lr.intercept_:.3f}"</span>)

<span style="color:#6b7280;"># Polynomial Regression via Pipeline — degree=2 adds x² term</span>
<span style="color:#93c5fd;">poly_pipe</span> = Pipeline([
    (<span style="color:#a7f3d0;">"poly"</span>, PolynomialFeatures(degree=<span style="color:#fcd34d;">2</span>, include_bias=<span style="color:#fca5a5;">False</span>)),
    (<span style="color:#a7f3d0;">"lr"</span>,   LinearRegression())
])
<span style="color:#93c5fd;">poly_pipe</span>.fit(X_tr, y_tr)
<span style="color:#93c5fd;">rmse_p</span> = mean_squared_error(y_te, poly_pipe.predict(X_te), squared=<span style="color:#fca5a5;">False</span>)
<span style="color:#93c5fd;">r2_p</span>   = r2_score(y_te, poly_pipe.predict(X_te))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Polynomial Degree 2: RMSE={rmse_p:.2f}, R²={r2_p:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>Linear Regression:  RMSE=19.84, R²=0.9638
Coefficient: 42.517, Intercept: 0.821
Polynomial Degree 2: RMSE=19.52, R²=0.9650</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsModule->id,
            'title'       => '1.8 Machine Learning: Classification, Regression & Evaluation',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L1_8', [
                ['q' => 'What is "overfitting"?', 'opts' => ['Model is too simple to learn patterns', 'Model learns training data including noise and generalizes poorly to new data', 'Model uses too much memory', 'Model takes too long to train'], 'ans' => 1, 'exp' => 'Overfitting = high variance. The model memorizes training data (even noise) and performs well on train but poorly on unseen test data.'],
                ['q' => 'Why must you fit the scaler ONLY on training data and then transform test data?', 'opts' => ['The scaler needs more data', 'To prevent data leakage — test statistics must not influence training preprocessing', 'Test data does not need scaling', 'It is faster'], 'ans' => 1, 'exp' => 'Fitting on test data is "data leakage" — your preprocessing learns information from the future. Always fit_transform on train, then only transform on test/validation.'],
                ['q' => 'What does K-Fold Cross-Validation do?', 'opts' => ['Splits data into K random batches', 'Trains K models and takes the best', 'Rotates K different train/validation splits to get a more reliable accuracy estimate', 'Tests on K different test sets'], 'ans' => 2, 'exp' => 'K-Fold CV splits data into K folds. Each fold takes a turn as the validation set while the remaining K-1 folds form the training set. The K scores are averaged for a more reliable estimate.'],
                ['q' => 'What does R² = 0.96 mean in regression?', 'opts' => ['96% of predictions are correct', '96% of the variance in y is explained by the model', 'Model has 4% error', 'RMSE is 0.04'], 'ans' => 1, 'exp' => 'R² (coefficient of determination) measures the proportion of variance in y explained by the model. R²=0.96 means the model accounts for 96% of the variance in the target variable.'],
                ['q' => 'What metric is most appropriate for imbalanced classification (99% class 0, 1% class 1)?', 'opts' => ['Accuracy', 'MSE', 'F1-score or AUC-ROC', 'R²'], 'ans' => 2, 'exp' => 'Accuracy is misleading on imbalanced data — predicting class 0 always gives 99% accuracy. F1-score balances precision and recall; AUC-ROC measures discrimination ability across thresholds.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.9 — Unsupervised Learning
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Unsupervised Learning: Clustering & Dimensionality Reduction</h2>
<p>Unsupervised learning works on data without labels. It finds hidden structure — natural groupings, low-dimensional representations, and anomalies. It is used constantly in marketing (customer segmentation), biology (gene expression clustering), recommendation systems, and anomaly detection.</p>

<h3>K-Means Clustering</h3>
<p>K-Means is the most widely used clustering algorithm. It partitions n observations into k clusters where each observation belongs to the cluster with the nearest centroid.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CLUSTERING — K-Means with Elbow Method</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.cluster  <span style="color:#c4b5fd;">import</span> KMeans
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> make_blobs
<span style="color:#c4b5fd;">from</span> sklearn.metrics  <span style="color:#c4b5fd;">import</span> silhouette_score
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#6b7280;"># Generate synthetic customer data (spend vs frequency)</span>
<span style="color:#93c5fd;">X</span>, <span style="color:#93c5fd;">_</span> = make_blobs(n_samples=<span style="color:#fcd34d;">300</span>, centers=<span style="color:#fcd34d;">4</span>, cluster_std=<span style="color:#fcd34d;">0.8</span>, random_state=<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Elbow Method — find optimal k by plotting inertia vs k</span>
<span style="color:#93c5fd;">inertias</span>    = []
<span style="color:#93c5fd;">sil_scores</span>  = []
<span style="color:#93c5fd;">k_range</span>     = range(<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">9</span>)

<span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> k_range:
    <span style="color:#93c5fd;">km</span> = KMeans(n_clusters=k, random_state=<span style="color:#fcd34d;">42</span>, n_init=<span style="color:#fcd34d;">10</span>)
    <span style="color:#93c5fd;">km</span>.fit(X)
    inertias.append(km.inertia_)
    sil_scores.append(silhouette_score(X, km.labels_))

<span style="color:#6b7280;"># Plot Elbow Curve</span>
<span style="color:#93c5fd;">fig</span>, <span style="color:#93c5fd;">axes</span> = plt.subplots(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">3</span>, figsize=(<span style="color:#fcd34d;">15</span>, <span style="color:#fcd34d;">4</span>))

axes[<span style="color:#fcd34d;">0</span>].plot(<span style="color:#93c5fd;">list</span>(k_range), inertias, <span style="color:#a7f3d0;">"bo-"</span>, linewidth=<span style="color:#fcd34d;">2</span>)
axes[<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Elbow Curve"</span>); axes[<span style="color:#fcd34d;">0</span>].set_xlabel(<span style="color:#a7f3d0;">"k"</span>); axes[<span style="color:#fcd34d;">0</span>].set_ylabel(<span style="color:#a7f3d0;">"Inertia"</span>)

axes[<span style="color:#fcd34d;">1</span>].plot(<span style="color:#93c5fd;">list</span>(k_range), sil_scores, <span style="color:#a7f3d0;">"go-"</span>, linewidth=<span style="color:#fcd34d;">2</span>)
axes[<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Silhouette Scores"</span>); axes[<span style="color:#fcd34d;">1</span>].set_xlabel(<span style="color:#a7f3d0;">"k"</span>)

<span style="color:#6b7280;"># Train final K-Means with k=4</span>
<span style="color:#93c5fd;">km_final</span>    = KMeans(n_clusters=<span style="color:#fcd34d;">4</span>, random_state=<span style="color:#fcd34d;">42</span>, n_init=<span style="color:#fcd34d;">10</span>)
<span style="color:#93c5fd;">labels</span>      = km_final.fit_predict(X)
<span style="color:#93c5fd;">centroids</span>   = km_final.cluster_centers_

axes[<span style="color:#fcd34d;">2</span>].scatter(X[:,<span style="color:#fcd34d;">0</span>], X[:,<span style="color:#fcd34d;">1</span>], c=labels, cmap=<span style="color:#a7f3d0;">"viridis"</span>, alpha=<span style="color:#fcd34d;">0.6</span>, s=<span style="color:#fcd34d;">30</span>)
axes[<span style="color:#fcd34d;">2</span>].scatter(centroids[:,<span style="color:#fcd34d;">0</span>], centroids[:,<span style="color:#fcd34d;">1</span>], c=<span style="color:#a7f3d0;">"red"</span>, s=<span style="color:#fcd34d;">200</span>, marker=<span style="color:#a7f3d0;">"X"</span>)
axes[<span style="color:#fcd34d;">2</span>].set_title(<span style="color:#a7f3d0;">"K-Means k=4 Clusters"</span>)

plt.tight_layout(); plt.show()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Final Silhouette Score: {silhouette_score(X, labels):.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;padding:12px;font-size:0.8rem;">→ Elbow curve, silhouette scores, and scatter plot with cluster centroids marked in red.</div>
  </div>
</div>

<h3>PCA: Principal Component Analysis</h3>
<p>PCA is the most important dimensionality reduction technique. It projects high-dimensional data onto a lower-dimensional space by finding the directions (principal components) of maximum variance.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PCA — Dimensionality Reduction</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.decomposition <span style="color:#c4b5fd;">import</span> PCA
<span style="color:#c4b5fd;">from</span> sklearn.datasets      <span style="color:#c4b5fd;">import</span> load_breast_cancer
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot   <span style="color:#c4b5fd;">as</span> plt

<span style="color:#93c5fd;">bc</span>     = load_breast_cancer()
<span style="color:#93c5fd;">X</span>, <span style="color:#93c5fd;">y</span>  = bc.data, bc.target          <span style="color:#6b7280;"># 569 samples, 30 features</span>

<span style="color:#6b7280;"># PCA MUST be done on scaled data</span>
<span style="color:#93c5fd;">scaler</span> = StandardScaler()
<span style="color:#93c5fd;">X_sc</span>   = scaler.fit_transform(X)

<span style="color:#6b7280;"># Step 1: Full PCA to see explained variance</span>
<span style="color:#93c5fd;">pca_full</span>      = PCA()
<span style="color:#93c5fd;">pca_full</span>.fit(X_sc)
<span style="color:#93c5fd;">cumvar</span>        = np.cumsum(pca_full.explained_variance_ratio_)
<span style="color:#93c5fd;">n_for_95</span>      = np.argmax(cumvar >= <span style="color:#fcd34d;">0.95</span>) + <span style="color:#fcd34d;">1</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Components needed for 95% variance: {n_for_95}"</span>)

<span style="color:#6b7280;"># Step 2: Reduce to 2D for visualization</span>
<span style="color:#93c5fd;">pca_2d</span>  = PCA(n_components=<span style="color:#fcd34d;">2</span>)
<span style="color:#93c5fd;">X_2d</span>    = pca_2d.fit_transform(X_sc)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Variance explained by PC1+PC2: {pca_2d.explained_variance_ratio_.sum():.2%}"</span>)

<span style="color:#6b7280;"># Plot</span>
<span style="color:#93c5fd;">fig</span>, <span style="color:#93c5fd;">axes</span> = plt.subplots(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">5</span>))
axes[<span style="color:#fcd34d;">0</span>].plot(<span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">31</span>), cumvar, <span style="color:#a7f3d0;">"b-o"</span>, markersize=<span style="color:#fcd34d;">4</span>)
axes[<span style="color:#fcd34d;">0</span>].axhline(<span style="color:#fcd34d;">0.95</span>, color=<span style="color:#a7f3d0;">"red"</span>, linestyle=<span style="color:#a7f3d0;">"--"</span>, label=<span style="color:#a7f3d0;">"95% threshold"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Cumulative Explained Variance"</span>); axes[<span style="color:#fcd34d;">0</span>].legend()

colors = [<span style="color:#a7f3d0;">"#3b82f6"</span> <span style="color:#c4b5fd;">if</span> c == <span style="color:#fcd34d;">0</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"#ef4444"</span> <span style="color:#c4b5fd;">for</span> c <span style="color:#c4b5fd;">in</span> y]
axes[<span style="color:#fcd34d;">1</span>].scatter(X_2d[:,<span style="color:#fcd34d;">0</span>], X_2d[:,<span style="color:#fcd34d;">1</span>], c=colors, alpha=<span style="color:#fcd34d;">0.6</span>, s=<span style="color:#fcd34d;">20</span>)
axes[<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"PCA 2D Projection (blue=malignant, red=benign)"</span>)
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>Components needed for 95% variance: 10
Variance explained by PC1+PC2: 63.24%</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsModule->id,
            'title'       => '1.9 Unsupervised Learning: K-Means & PCA',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L1_9', [
                ['q' => 'What does the "Elbow Method" help you find in K-Means?', 'opts' => ['The best feature', 'The optimal number of clusters k', 'The cluster with most variance', 'The centroid positions'], 'ans' => 1, 'exp' => 'The elbow method plots inertia (within-cluster sum of squares) for different k values. The "elbow" — where adding more clusters gives diminishing returns — suggests the optimal k.'],
                ['q' => 'What is the Silhouette Score and what does a value near 1 mean?', 'opts' => ['Model accuracy — near 1 is poor', 'A measure of cluster cohesion and separation — near 1 means well-separated clusters', 'Number of misclassified points', 'Inertia normalized to [0,1]'], 'ans' => 1, 'exp' => 'Silhouette score ranges from -1 to 1. Near 1 = data point is well-matched to its own cluster and far from neighbouring clusters. Near 0 = on cluster boundaries. Negative = possibly misclustered.'],
                ['q' => 'Why must you standardize features before PCA?', 'opts' => ['PCA only works on integers', 'Features with larger scales dominate the variance, distorting principal components', 'It makes computation faster', 'PCA requires normally distributed data'], 'ans' => 1, 'exp' => 'PCA finds directions of maximum variance. Without scaling, features with large ranges (salary: 0-200,000) dominate over features with small ranges (age: 0-100), producing meaningless components.'],
                ['q' => 'What does "explained variance ratio" tell you in PCA?', 'opts' => ['How accurate the model is', 'What proportion of the total variance each principal component captures', 'The loading of each feature', 'The number of clusters'], 'ans' => 1, 'exp' => 'explained_variance_ratio_[0] tells you what fraction of total data variance PC1 explains. Cumulative sum tells you how many components you need to retain X% of information.'],
                ['q' => 'K-Means requires you to specify k upfront. What is the main risk of choosing k too large?', 'opts' => ['Algorithm runs slower', 'Lower inertia always', 'Overclustering — splitting natural groups into meaningless sub-groups', 'PCA fails'], 'ans' => 2, 'exp' => 'Increasing k always decreases inertia (more clusters = smaller within-cluster distances), but too many clusters fragment natural groups. Use the elbow method + silhouette score + domain knowledge.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.10 — Time Series Analysis
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Time Series Analysis</h2>
<p>Time series data is a sequence of observations indexed by time. It is ubiquitous in data science — stock prices, website traffic, sensor readings, weather data, sales forecasts. Time series analysis requires special techniques because observations are NOT independent — each value is correlated with past values (<em>autocorrelation</em>).</p>

<h3>Time Series Components</h3>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Trend</strong> — Long-term increase or decrease in the data</li>
  <li><strong style="color:var(--text);">Seasonality</strong> — Regular periodic patterns (daily, weekly, yearly cycles)</li>
  <li><strong style="color:var(--text);">Cyclicity</strong> — Irregular longer-term fluctuations (business cycles)</li>
  <li><strong style="color:var(--text);">Residual (Noise)</strong> — Random variation after trend and seasonality are removed</li>
</ul>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">TIME SERIES — Pandas DatetimeIndex & Resampling</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> numpy  <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt

<span style="color:#6b7280;"># Create a synthetic daily sales time series (1 year)</span>
<span style="color:#93c5fd;">rng</span>     = np.random.default_rng(<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">dates</span>   = pd.date_range(start=<span style="color:#a7f3d0;">"2023-01-01"</span>, periods=<span style="color:#fcd34d;">365</span>, freq=<span style="color:#a7f3d0;">"D"</span>)
<span style="color:#93c5fd;">trend</span>   = np.linspace(<span style="color:#fcd34d;">1000</span>, <span style="color:#fcd34d;">1500</span>, <span style="color:#fcd34d;">365</span>)                  <span style="color:#6b7280;"># upward trend</span>
<span style="color:#93c5fd;">seasonal</span>= <span style="color:#fcd34d;">200</span> * np.sin(<span style="color:#fcd34d;">2</span> * np.pi * np.arange(<span style="color:#fcd34d;">365</span>) / <span style="color:#fcd34d;">30</span>) <span style="color:#6b7280;"># monthly cycle</span>
<span style="color:#93c5fd;">noise</span>   = rng.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">50</span>, <span style="color:#fcd34d;">365</span>)
<span style="color:#93c5fd;">sales</span>   = pd.Series(trend + seasonal + noise, index=dates, name=<span style="color:#a7f3d0;">"sales"</span>)

<span style="color:#6b7280;"># Datetime indexing</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"March sales:"</span>)
<span style="color:#93c5fd;">print</span>(sales[<span style="color:#a7f3d0;">"2023-03"</span>].describe())

<span style="color:#6b7280;"># Resampling — aggregate to different frequencies</span>
<span style="color:#93c5fd;">weekly</span>  = sales.resample(<span style="color:#a7f3d0;">"W"</span>).mean()
<span style="color:#93c5fd;">monthly</span> = sales.resample(<span style="color:#a7f3d0;">"ME"</span>).sum()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nMonthly totals:"</span>)
<span style="color:#93c5fd;">print</span>(monthly.head(<span style="color:#fcd34d;">4</span>).round(<span style="color:#fcd34d;">0</span>))

<span style="color:#6b7280;"># Rolling statistics — smooth the noise</span>
<span style="color:#93c5fd;">rolling_mean</span> = sales.rolling(window=<span style="color:#fcd34d;">7</span>).mean()   <span style="color:#6b7280;"># 7-day moving average</span>
<span style="color:#93c5fd;">rolling_std</span>  = sales.rolling(window=<span style="color:#fcd34d;">7</span>).std()

<span style="color:#6b7280;"># Lag features — for forecasting models</span>
<span style="color:#93c5fd;">df_ts</span> = pd.DataFrame({<span style="color:#a7f3d0;">"sales"</span>: sales})
<span style="color:#93c5fd;">df_ts</span>[<span style="color:#a7f3d0;">"lag_1"</span>]  = df_ts[<span style="color:#a7f3d0;">"sales"</span>].shift(<span style="color:#fcd34d;">1</span>)    <span style="color:#6b7280;"># yesterday's sales</span>
<span style="color:#93c5fd;">df_ts</span>[<span style="color:#a7f3d0;">"lag_7"</span>]  = df_ts[<span style="color:#a7f3d0;">"sales"</span>].shift(<span style="color:#fcd34d;">7</span>)    <span style="color:#6b7280;"># same day last week</span>
<span style="color:#93c5fd;">df_ts</span>[<span style="color:#a7f3d0;">"pct_chg"</span>] = df_ts[<span style="color:#a7f3d0;">"sales"</span>].pct_change() * <span style="color:#fcd34d;">100</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nDataFrame with lag features:"</span>)
<span style="color:#93c5fd;">print</span>(df_ts.dropna().head(<span style="color:#fcd34d;">3</span>).round(<span style="color:#fcd34d;">1</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>March sales:
count     31.000
mean    1092.4
max     1479.2
...
Monthly totals:
2023-01-31    34521.0
2023-02-28    31874.0
2023-03-31    35148.0
2023-04-30    34902.0

DataFrame with lag features:
           sales    lag_1    lag_7  pct_chg
2023-01-08  1023.4   987.5   1012.3      3.6</div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">TIME SERIES — Seasonal Decomposition & Visualization</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> statsmodels.tsa.seasonal <span style="color:#c4b5fd;">import</span> seasonal_decompose

<span style="color:#6b7280;"># Decompose into trend + seasonal + residual</span>
<span style="color:#93c5fd;">decomposition</span> = seasonal_decompose(sales, model=<span style="color:#a7f3d0;">"additive"</span>, period=<span style="color:#fcd34d;">30</span>)

<span style="color:#93c5fd;">fig</span>, <span style="color:#93c5fd;">axes</span> = plt.subplots(<span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">1</span>, figsize=(<span style="color:#fcd34d;">14</span>, <span style="color:#fcd34d;">10</span>), sharex=<span style="color:#fca5a5;">True</span>)

decomposition.observed.plot(ax=axes[<span style="color:#fcd34d;">0</span>], title=<span style="color:#a7f3d0;">"Observed"</span>,  color=<span style="color:#a7f3d0;">"#3b82f6"</span>)
decomposition.trend.plot(ax=axes[<span style="color:#fcd34d;">1</span>],    title=<span style="color:#a7f3d0;">"Trend"</span>,     color=<span style="color:#a7f3d0;">"#10b981"</span>)
decomposition.seasonal.plot(ax=axes[<span style="color:#fcd34d;">2</span>], title=<span style="color:#a7f3d0;">"Seasonal"</span>,  color=<span style="color:#a7f3d0;">"#f59e0b"</span>)
decomposition.resid.plot(ax=axes[<span style="color:#fcd34d;">3</span>],    title=<span style="color:#a7f3d0;">"Residual"</span>,  color=<span style="color:#a7f3d0;">"#ef4444"</span>)

plt.suptitle(<span style="color:#a7f3d0;">"Time Series Decomposition"</span>, fontsize=<span style="color:#fcd34d;">14</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout(); plt.show()

<span style="color:#6b7280;"># Autocorrelation check — is there serial correlation?</span>
<span style="color:#93c5fd;">autocorr_lag1</span> = sales.autocorr(lag=<span style="color:#fcd34d;">1</span>)
<span style="color:#93c5fd;">autocorr_lag7</span> = sales.autocorr(lag=<span style="color:#fcd34d;">7</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Autocorrelation lag-1: {autocorr_lag1:.3f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Autocorrelation lag-7: {autocorr_lag7:.3f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>Autocorrelation lag-1: 0.812
Autocorrelation lag-7: 0.571</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsModule->id,
            'title'       => '1.10 Time Series: Decomposition, Resampling & Forecasting Features',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L1_10', [
                ['q' => 'What does pd.date_range(start="2023-01-01", periods=365, freq="D") create?', 'opts' => ['A list of 365 random dates', 'A DatetimeIndex of 365 consecutive daily dates starting Jan 1 2023', '365 timestamps at random intervals', 'A range object'], 'ans' => 1, 'exp' => 'pd.date_range() generates a DatetimeIndex. freq="D" = daily, "W" = weekly, "ME" = month-end, "H" = hourly.'],
                ['q' => 'What does resample("ME").sum() do?', 'opts' => ['Calculates 7-day rolling sum', 'Groups data by month-end frequency and sums within each month', 'Removes seasonal component', 'Forward-fills missing monthly values'], 'ans' => 1, 'exp' => 'resample() is like groupby() for time series. "ME" = month-end frequency. .sum() aggregates all daily values within each calendar month.'],
                ['q' => 'What is a "lag feature" in time series forecasting?', 'opts' => ['A feature that lags behind the target', 'The value of the target variable at a previous time step, used as a predictor', 'Delayed computation', 'Missing data indicator'], 'ans' => 1, 'exp' => 'Lag features capture autocorrelation. lag_1 = yesterday\'s value, lag_7 = same day last week. They are the primary features in time series ML models.'],
                ['q' => 'What does rolling(window=7).mean() compute?', 'opts' => ['Mean of the entire series', 'Rolling average over each 7-day window — smooths short-term fluctuations', 'Sums every 7 days', 'Randomly samples 7 values'], 'ans' => 1, 'exp' => 'A rolling (moving) average smooths the time series. Each output point is the mean of the preceding 7 observations, revealing the underlying trend by reducing noise.'],
                ['q' => 'A high autocorrelation at lag-1 (near 1.0) indicates what?', 'opts' => ['Data is completely random', 'Each value is strongly correlated with the immediately preceding value — strong temporal dependence', 'Seasonal pattern of period 1', 'Data needs no transformation'], 'ans' => 1, 'exp' => 'High lag-1 autocorrelation means knowing yesterday\'s value tells you a lot about today\'s. This violates the i.i.d. assumption of standard ML and is why specialized time series models (ARIMA, LSTM) are used.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.11 — NLP Fundamentals
        // ══════════════════════════════════════════════════════════════
        $content11 = <<<'HTML'
<h2>Natural Language Processing (NLP) Fundamentals</h2>
<p>Natural Language Processing is the subfield of AI that deals with the interaction between computers and human language. NLP powers search engines, chatbots, spam filters, sentiment analysis, machine translation, and large language models. This lesson covers the foundational text preprocessing and feature extraction techniques every data scientist needs.</p>

<h3>Text Preprocessing Pipeline</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">NLP — Text Cleaning & Tokenization</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> re
<span style="color:#c4b5fd;">import</span> string
<span style="color:#c4b5fd;">from</span> collections <span style="color:#c4b5fd;">import</span> Counter

<span style="color:#93c5fd;">text</span> = <span style="color:#a7f3d0;">"""
Data Science is AMAZING! It's the fastest-growing field in 2024.
Machine learning, AI, and deep learning are transforming every industry.
From healthcare to finance — data is the new oil!!! #datascience #AI
"""</span>

<span style="color:#6b7280;"># Step 1: Lowercase</span>
<span style="color:#93c5fd;">text1</span> = text.lower()

<span style="color:#6b7280;"># Step 2: Remove URLs</span>
<span style="color:#93c5fd;">text2</span> = re.sub(<span style="color:#a7f3d0;">r"http\S+"</span>, <span style="color:#a7f3d0;">""</span>, text1)

<span style="color:#6b7280;"># Step 3: Remove punctuation and special characters</span>
<span style="color:#93c5fd;">text3</span> = re.sub(<span style="color:#a7f3d0;">r"[^a-z0-9\s]"</span>, <span style="color:#a7f3d0;">""</span>, text2)

<span style="color:#6b7280;"># Step 4: Tokenize (split into words)</span>
<span style="color:#93c5fd;">tokens</span> = text3.split()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Tokens:"</span>, tokens[:<span style="color:#fcd34d;">12</span>])

<span style="color:#6b7280;"># Step 5: Remove stopwords (common words with little meaning)</span>
<span style="color:#93c5fd;">stopwords</span> = {<span style="color:#a7f3d0;">"is"</span>, <span style="color:#a7f3d0;">"the"</span>, <span style="color:#a7f3d0;">"a"</span>, <span style="color:#a7f3d0;">"an"</span>, <span style="color:#a7f3d0;">"in"</span>, <span style="color:#a7f3d0;">"and"</span>, <span style="color:#a7f3d0;">"to"</span>, <span style="color:#a7f3d0;">"are"</span>, <span style="color:#a7f3d0;">"from"</span>, <span style="color:#a7f3d0;">"it"</span>, <span style="color:#a7f3d0;">"its"</span>}
<span style="color:#93c5fd;">filtered</span>  = [t <span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> tokens <span style="color:#c4b5fd;">if</span> t <span style="color:#c4b5fd;">not in</span> stopwords]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"After stopwords:"</span>, filtered[:<span style="color:#fcd34d;">12</span>])

<span style="color:#6b7280;"># Word frequency analysis</span>
<span style="color:#93c5fd;">freq</span> = Counter(filtered)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nTop 5 words:"</span>, freq.most_common(<span style="color:#fcd34d;">5</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>Tokens: ['data', 'science', 'is', 'amazing', 'its', 'the', 'fastest', 'growing', 'field', 'in', '2024', 'machine']
After stopwords: ['data', 'science', 'amazing', 'fastest', 'growing', 'field', '2024', 'machine', 'learning', ...]
Top 5 words: [('data', 2), ('learning', 2), ('science', 1), ('amazing', 1), ('fastest', 1)]</div>
  </div>
</div>

<h3>TF-IDF: Term Frequency-Inverse Document Frequency</h3>
<p>TF-IDF is the standard technique for converting text into numerical features. It weights words by how frequently they appear in a document (TF) but downweights words that appear in many documents (IDF), correctly identifying that "the" is less informative than "neural".</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">NLP — TF-IDF & Sentiment Classification</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.feature_extraction.text <span style="color:#c4b5fd;">import</span> TfidfVectorizer, CountVectorizer
<span style="color:#c4b5fd;">from</span> sklearn.naive_bayes              <span style="color:#c4b5fd;">import</span> MultinomialNB
<span style="color:#c4b5fd;">from</span> sklearn.pipeline                 <span style="color:#c4b5fd;">import</span> Pipeline
<span style="color:#c4b5fd;">from</span> sklearn.model_selection          <span style="color:#c4b5fd;">import</span> cross_val_score

<span style="color:#6b7280;"># Toy sentiment dataset</span>
<span style="color:#93c5fd;">docs</span>   = [
    <span style="color:#a7f3d0;">"This product is amazing and fantastic"</span>,
    <span style="color:#a7f3d0;">"I love this excellent product"</span>,
    <span style="color:#a7f3d0;">"Terrible product, completely useless"</span>,
    <span style="color:#a7f3d0;">"Awful quality, do not buy this"</span>,
    <span style="color:#a7f3d0;">"Great value and wonderful experience"</span>,
    <span style="color:#a7f3d0;">"Horrible, broken, waste of money"</span>
]
<span style="color:#93c5fd;">labels</span> = [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>]  <span style="color:#6b7280;"># 1=positive, 0=negative</span>

<span style="color:#6b7280;"># TF-IDF Matrix</span>
<span style="color:#93c5fd;">tfidf</span> = TfidfVectorizer(max_features=<span style="color:#fcd34d;">20</span>, stop_words=<span style="color:#a7f3d0;">"english"</span>)
<span style="color:#93c5fd;">X</span>     = tfidf.fit_transform(docs)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"TF-IDF shape:"</span>, X.shape)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Vocabulary:"</span>, tfidf.get_feature_names_out())

<span style="color:#6b7280;"># Text classification pipeline</span>
<span style="color:#93c5fd;">pipe</span> = Pipeline([
    (<span style="color:#a7f3d0;">"tfidf"</span>, TfidfVectorizer(stop_words=<span style="color:#a7f3d0;">"english"</span>)),
    (<span style="color:#a7f3d0;">"clf"</span>,  MultinomialNB())
])

<span style="color:#6b7280;"># Predict on new reviews</span>
<span style="color:#93c5fd;">pipe</span>.fit(docs, labels)
<span style="color:#93c5fd;">new_reviews</span> = [<span style="color:#a7f3d0;">"This is a wonderful and excellent product"</span>,
               <span style="color:#a7f3d0;">"Complete waste, terrible experience"</span>]
<span style="color:#93c5fd;">predictions</span> = pipe.predict(new_reviews)
<span style="color:#93c5fd;">proba</span>       = pipe.predict_proba(new_reviews)
<span style="color:#c4b5fd;">for</span> rev, pred, prob <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(new_reviews, predictions, proba):
    <span style="color:#93c5fd;">sentiment</span> = <span style="color:#a7f3d0;">"Positive"</span> <span style="color:#c4b5fd;">if</span> pred == <span style="color:#fcd34d;">1</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"Negative"</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{sentiment} ({prob[pred]:.0%}): '{rev[:40]}...'"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;">Console Output</span>TF-IDF shape: (6, 20)
Vocabulary: ['amazing' 'awful' 'broken' 'buy' ...]
Positive (89%): 'This is a wonderful and excellent product...'
Negative (92%): 'Complete waste, terrible experience...'</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsModule->id,
            'title'       => '1.11 NLP: Text Preprocessing, TF-IDF & Sentiment Analysis',
            'order_index' => 11,
            'content'     => $this->appendQuiz($content11, 'L1_11', [
                ['q' => 'What does "tokenization" mean in NLP?', 'opts' => ['Converting text to numbers', 'Splitting text into individual units (words/subwords)', 'Encrypting text data', 'Removing duplicate sentences'], 'ans' => 1, 'exp' => 'Tokenization breaks raw text into tokens — typically words but can be subwords or characters. It is the foundational first step in almost all NLP pipelines.'],
                ['q' => 'Why do we remove stopwords in NLP preprocessing?', 'opts' => ['They cause grammatical errors', 'They are numerically large', 'They are high-frequency, low-information words (the, is, a) that add noise', 'They make tokenization slower'], 'ans' => 2, 'exp' => 'Stopwords (the, is, a, and) appear constantly but carry little semantic meaning. Removing them reduces feature space noise and helps models focus on content-bearing words.'],
                ['q' => 'What is the IDF component of TF-IDF designed to downweight?', 'opts' => ['Rare words unique to one document', 'Words that appear in many documents across the corpus', 'Long words with many characters', 'Proper nouns'], 'ans' => 1, 'exp' => 'IDF = log(N/df) where df is document frequency. Words appearing in many documents get low IDF. This downweights common words like "the" and upweights rare, document-specific terms.'],
                ['q' => 'What does TfidfVectorizer.fit_transform(corpus) return?', 'opts' => ['A list of cleaned strings', 'A sparse matrix where rows=documents, columns=vocabulary terms', 'A dictionary of word counts', 'A list of probabilities'], 'ans' => 1, 'exp' => 'fit_transform returns a sparse matrix of shape (n_documents, n_terms). Each cell contains the TF-IDF weight of that term in that document. Most entries are 0 (sparse).'],
                ['q' => 'Why use a Pipeline([("tfidf", ...), ("clf", ...)]) for text classification?', 'opts' => ['Pipelines are mandatory in sklearn', 'It chains preprocessing and modelling into one object, preventing leakage and simplifying code', 'TF-IDF only works inside pipelines', 'It automatically tunes hyperparameters'], 'ans' => 1, 'exp' => 'Pipelines chain steps. In cross-validation, fit_transform happens only on training folds, preventing test data from leaking into TF-IDF vocabulary statistics — a crucial correctness guarantee.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.12 — Final Exam
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            // Landscape
            ['q' => 'Data Science is the intersection of which three domains?', 'opts' => ['Python, SQL, ML', 'Statistics, CS/Programming, Domain Expertise', 'Data, Models, Deployment', 'Visualization, Cleaning, Modelling'], 'ans' => 1, 'exp' => 'Drew Conway\'s Venn diagram: Math & Statistics, Hacking Skills, Substantive Expertise.'],
            // NumPy
            ['q' => 'What does np.linspace(0, 1, 5) produce?', 'opts' => ['[0, 0.2, 0.4, 0.6, 0.8]', '[0, 0.25, 0.5, 0.75, 1.0]', '[0, 1, 2, 3, 4]', '[0.2, 0.4, 0.6, 0.8, 1.0]'], 'ans' => 1, 'exp' => 'linspace(start, stop, num) generates num evenly spaced values between start and stop (inclusive). 5 values from 0 to 1 = [0, 0.25, 0.5, 0.75, 1.0].'],
            ['q' => 'scores[scores > 80] is an example of...', 'opts' => ['List comprehension', 'Boolean masking / fancy indexing', 'Slicing', 'A for loop'], 'ans' => 1, 'exp' => 'Boolean masking creates a True/False array from the condition and uses it to select matching elements. This is one of the most important NumPy/Pandas patterns.'],
            // Pandas
            ['q' => 'What is the difference between df.loc[2] and df.iloc[2]?', 'opts' => ['Same result always', 'loc uses index label, iloc uses integer position', 'iloc is faster', 'loc only works on strings'], 'ans' => 1, 'exp' => '.loc[2] selects the row with INDEX LABEL 2. .iloc[2] selects the row at POSITION 2. They differ when the index is not the default integer range.'],
            ['q' => 'Which pandas method fills NaN values with the column mean?', 'opts' => ['df.fill(mean=True)', 'df.fillna(df.mean())', 'df.impute("mean")', 'df.replace(np.nan, mean)'], 'ans' => 1, 'exp' => 'df.fillna(value) replaces NaN. Passing df.mean() fills each column\'s NaN with that column\'s mean — element-wise broadcasting.'],
            // Visualization
            ['q' => 'Which seaborn function creates a grid of all pairwise scatter plots?', 'opts' => ['sns.heatmap()', 'sns.pairplot()', 'sns.matplot()', 'sns.scatter_matrix()'], 'ans' => 1, 'exp' => 'sns.pairplot(df, hue="label") creates a scatter plot for every pair of numeric features, with KDE/histogram on the diagonal. Essential for quick multivariate EDA.'],
            ['q' => 'What does cmap="coolwarm" in sns.heatmap() control?', 'opts' => ['Font size', 'Annotation format', 'Color palette mapping values to colors', 'Figure size'], 'ans' => 2, 'exp' => 'cmap sets the colormap. "coolwarm" maps negative correlations to blue and positive to red, making a correlation heatmap immediately interpretable.'],
// Statistics (Continued)
            ['q' => 'The 68-95-99.7 rule applies to which distribution?', 'opts' => ['Uniform', 'Poisson', 'Binomial', 'Normal (Gaussian)'], 'ans' => 3, 'exp' => 'The Empirical Rule states: 68% of data falls within ±1σ, 95% within ±2σ, and 99.7% within ±3σ of the mean in a normal distribution.'],
            ['q' => 'What does a p-value represent?', 'opts' => ['The probability the hypothesis is true', 'The probability of observing data this extreme if H0 were true', 'Model accuracy', 'Effect size'], 'ans' => 1, 'exp' => 'p-value is the probability of obtaining test results at least as extreme as the results actually observed, under the assumption that the null hypothesis is correct.'],
            ['q' => 'Which metric is most robust to extreme outliers?', 'opts' => ['Mean', 'Standard Deviation', 'Median', 'Range'], 'ans' => 2, 'exp' => 'The median only cares about the middle position of the data, whereas the mean and range are heavily pulled by extreme values.'],
            
            // Feature Engineering & Preprocessing (1.6)
            ['q' => 'Which scaling method transforms data to have a mean of 0 and a standard deviation of 1?', 'opts' => ['MinMaxScaler', 'StandardScaler', 'RobustScaler', 'Log Transform'], 'ans' => 1, 'exp' => 'StandardScaler (Z-score normalization) centers data and scales it based on the standard deviation.'],
            ['q' => 'Why is One-Hot Encoding used instead of Label Encoding for nominal data like "City"?', 'opts' => ['It saves memory', 'To prevent the model from assuming a mathematical order (e.g., Manila > Cebu)', 'It is faster to compute', 'It handles missing values'], 'ans' => 1, 'exp' => 'Nominal data has no inherent order. Label encoding (0, 1, 2) implies a ranking that doesn\'t exist; One-Hot Encoding uses binary flags to avoid this.'],
            ['q' => 'What is the purpose of a Log Transform in data preprocessing?', 'opts' => ['To remove outliers', 'To handle categorical data', 'To compress right-skewed distributions and handle wide ranges', 'To increase the mean'], 'ans' => 2, 'exp' => 'Log transforms (like np.log1p) help normalize right-skewed data and reduce the impact of very large values.'],

            // ML Fundamentals (1.8)
            ['q' => 'What does "overfitting" look like in terms of Bias and Variance?', 'opts' => ['High Bias, Low Variance', 'Low Bias, High Variance', 'Low Bias, Low Variance', 'High Bias, High Variance'], 'ans' => 1, 'exp' => 'Overfitting is High Variance. The model "memorizes" the training data (low bias) but fails to generalize to new data because it is too sensitive to noise.'],
            ['q' => 'Why do we use "Stratify" when splitting imbalanced data?', 'opts' => ['To make the split faster', 'To ensure the train and test sets have the same proportion of classes as the original data', 'To remove outliers', 'To shuffle the data'], 'ans' => 1, 'exp' => 'Stratification ensures that if your data is 90% "No" and 10% "Yes", both your training and testing sets maintain that exact ratio.'],
            ['q' => 'Which metric is best for evaluating a classifier on a highly imbalanced dataset?', 'opts' => ['Accuracy', 'F1-Score / AUC-ROC', 'Mean Squared Error', 'R-Squared'], 'ans' => 1, 'exp' => 'Accuracy is deceptive for imbalanced data. F1-Score (precision/recall balance) or AUC-ROC are much better indicators of performance.'],
            ['q' => 'What does K-Fold Cross-Validation provide?', 'opts' => ['Faster training times', 'Automatic feature selection', 'A more robust estimate of model performance by training on different subsets of data', 'Reduced memory usage'], 'ans' => 2, 'exp' => 'K-Fold CV ensures every data point is used for both training and validation, reducing the risk that your results are due to a "lucky" random split.'],
            ['q' => 'In Linear Regression, what does R-squared (R²) represent?', 'opts' => ['The average error', 'The correlation between features', 'The proportion of variance in the target explained by the model', 'The slope of the line'], 'ans' => 2, 'exp' => 'R² ranges from 0 to 1 and tells you how much of the target\'s variation is captured by your independent variables.'],

            // Unsupervised Learning (1.9)
            ['q' => 'What is the goal of K-Means clustering?', 'opts' => ['Predict a continuous value', 'Find the best line of fit', 'Partition data into K distinct groups based on similarity', 'Reduce the number of columns'], 'ans' => 2, 'exp' => 'K-Means is an unsupervised algorithm that groups data points into clusters by minimizing the distance between points and their cluster centroid.'],
            ['q' => 'In PCA, what is a "Principal Component"?', 'opts' => ['The average of all features', 'A new direction in the data that captures the maximum possible variance', 'The most important raw column', 'An outlier'], 'ans' => 1, 'exp' => 'PCA transforms correlated features into a smaller set of uncorrelated components that retain as much information (variance) as possible.'],
            ['q' => 'What does the "Elbow Method" help determine?', 'opts' => ['The number of PCA components', 'The optimal number of clusters (K) for K-Means', 'The learning rate', 'The depth of a decision tree'], 'ans' => 1, 'exp' => 'The elbow method plots "Inertia" against "K". The point where the rate of decrease sharpens (the elbow) suggests the best K.'],

            // Time Series (1.10)
            ['q' => 'Which time series component represents a repeating pattern at fixed intervals (e.g., every December)?', 'opts' => ['Trend', 'Cyclicity', 'Seasonality', 'Noise'], 'ans' => 2, 'exp' => 'Seasonality refers to periodic fluctuations that happen at regular, known intervals.'],
            ['q' => 'What does the "resample" method in Pandas do?', 'opts' => ['Changes the data types', 'Aggregates time series data to a different frequency (e.g., daily to monthly)', 'Fills missing values', 'Shuffles the timeline'], 'ans' => 1, 'exp' => 'Resampling is like "GroupBy" for time. It allows you to change the frequency of your data points (Upsampling or Downsampling).'],
            ['q' => 'What is a "lag feature"?', 'opts' => ['A delayed model prediction', 'A value from a previous time step used as a predictor for the current step', 'A data error', 'A visualization technique'], 'ans' => 1, 'exp' => 'Lag features (e.g., yesterday\'s sales) are essential in time series because they capture temporal dependencies.'],

            // NLP (1.11)
            ['q' => 'In NLP, what is "Tokenization"?', 'opts' => ['Encoding text into binary', 'Breaking text into smaller units like words or characters', 'Removing vowels', 'Translating languages'], 'ans' => 1, 'exp' => 'Tokenization is the first step in NLP, turning a raw string into a list of "tokens" that a computer can count or process.'],
            ['q' => 'What does TF-IDF stand for?', 'opts' => ['Text Frequency - Input Data Format', 'Total Frequency - Inner Document Factor', 'Term Frequency - Inverse Document Frequency', 'Timed Feature - Integrated Data Flow'], 'ans' => 2, 'exp' => 'TF-IDF highlights words that are frequent in a specific document but rare across the whole collection, helping identify "keywords".'],
            ['q' => 'Why are "Stopwords" usually removed in text mining?', 'opts' => ['They are offensive', 'They are too rare', 'They are very common but carry little semantic meaning (e.g., "the", "is")', 'To make the text more formal'], 'ans' => 2, 'exp' => 'Removing high-frequency words that don\'t add unique information helps models focus on the significant words.'],
            
            // Integration / Ethics
            ['q' => 'What is the "Danger Zone" in the Data Science Venn Diagram?', 'opts' => ['Math + Code without Domain Knowledge', 'Code + Domain Knowledge without Math/Stats', 'Math + Domain Knowledge without Code', 'Too much data'], 'ans' => 1, 'exp' => 'Hacking skills + Domain knowledge without Math is the danger zone — you can build models and understand the business, but you won\'t understand when or why your models are wrong.'],
        ];

        $finalContent  = <<<HTML
            <div id="org-lock-screen" style="text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;">
                <div style="font-size:3rem;margin-bottom:1rem;">🔒</div>
                <h3 style="color:var(--text);margin-bottom:0.5rem;">University / Organization Access Only</h3>
                <p style="color:var(--muted);">The Final Module Exam is restricted to enrolled students and verified organization members.</p>
                <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
            </div>
            <div id="final-exam-content" style="display:none;">
                <h2>Module 3: Final Examination</h2>
                <p>This comprehensive exam covers all topics from Lesson 1.1 through 1.11 — the DS pipeline, NumPy, Pandas, Visualization, Statistics, Preprocessing, ML, Unsupervised Learning, Time Series, and NLP. Good luck!</p>
HTML;

        $finalContent .= $this->appendQuiz('', 'FINAL_EXAM_M3', $allFinalQuestions);
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
            'module_id'   => $dsModule->id,
            'title'       => '1.12 Final Exam: Data Science Proficiency',
            'order_index' => 12,
            'content'     => $finalContent,
        ]);
    }

    /**
     * Helper Function: Generates Quiz HTML, CSS, and JS
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