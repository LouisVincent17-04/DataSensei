<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module15LessonsSeeder
 * Seeds lessons for Module 15: Data Visualization.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module15LessonsSeeder
 */
class Module15LessonsSeeder extends Seeder
{
    public function run()
    {
        $module = Module::where('order_index', 15)->firstOrFail();
        Lesson::where('module_id', $module->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 15.1 — Introduction to Data Visualization
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>Introduction to Data Visualization</h2>
<p>Data visualization is the art and science of representing data graphically so that patterns, trends, outliers, and relationships become immediately visible to the human eye. A well-crafted chart can communicate in seconds what a table of thousands of numbers cannot communicate in hours. As a data scientist, your job is not just to <em>find</em> insights — it is to <em>communicate</em> them. Visualization is your primary communication tool.</p>

<h3>Why Visualization Matters</h3>
<p>The human brain processes visual information roughly 60,000 times faster than text. When you hand a stakeholder a CSV of 50,000 rows, they see noise. When you hand them a line chart showing revenue climbing 40% quarter-over-quarter, they immediately understand the story. Visualization bridges the gap between raw computation and human decision-making.</p>
<p>Beyond communication, visualization is essential during <strong>Exploratory Data Analysis (EDA)</strong>. Before building any model, you plot your data to understand its distribution, spot missing values, detect outliers, and discover hidden correlations. A scatter plot can reveal a non-linear relationship that would completely break a linear regression model before you even train it.</p>

<h3>The Python Visualization Ecosystem</h3>
<p>Python has a rich and layered ecosystem for visualization. Understanding which library to reach for — and when — is one of the most practical skills in data science:</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:20px;margin-bottom:24px;">
  <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
    <thead>
      <tr style="border-bottom:1px solid var(--border);">
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Library</th>
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Layer</th>
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Best For</th>
      </tr>
    </thead>
    <tbody>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:#93c5fd;font-family:'JetBrains Mono',monospace;font-weight:600;">Matplotlib</td>
        <td style="padding:10px 12px;color:var(--muted);">Foundation</td>
        <td style="padding:10px 12px;color:var(--muted);">Full control, publication-quality static charts, customizing every pixel</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:#93c5fd;font-family:'JetBrains Mono',monospace;font-weight:600;">Seaborn</td>
        <td style="padding:10px 12px;color:var(--muted);">High-level (Matplotlib)</td>
        <td style="padding:10px 12px;color:var(--muted);">Statistical plots, beautiful defaults, works directly with DataFrames</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:#93c5fd;font-family:'JetBrains Mono',monospace;font-weight:600;">Plotly</td>
        <td style="padding:10px 12px;color:var(--muted);">Interactive</td>
        <td style="padding:10px 12px;color:var(--muted);">Web dashboards, hover tooltips, zoom/pan interactivity, 3D charts</td>
      </tr>
      <tr>
        <td style="padding:10px 12px;color:#93c5fd;font-family:'JetBrains Mono',monospace;font-weight:600;">Pandas .plot()</td>
        <td style="padding:10px 12px;color:var(--muted);">Convenience</td>
        <td style="padding:10px 12px;color:var(--muted);">Quick EDA charts directly from a DataFrame in one line</td>
      </tr>
    </tbody>
  </table>
</div>

<h3>Choosing the Right Chart Type</h3>
<p>Picking the wrong chart is one of the most common mistakes in data communication. Every chart type was designed to answer a specific type of question. Before you open your code editor, ask: <em>what relationship am I trying to show?</em></p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:20px;margin-bottom:24px;">
  <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
    <thead>
      <tr style="border-bottom:1px solid var(--border);">
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Question / Goal</th>
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Best Chart Type</th>
      </tr>
    </thead>
    <tbody>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:var(--muted);">How does a value change over time?</td>
        <td style="padding:10px 12px;color:#a7f3d0;font-weight:600;">Line Chart</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:var(--muted);">How do categories compare to each other?</td>
        <td style="padding:10px 12px;color:#a7f3d0;font-weight:600;">Bar Chart</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:var(--muted);">What is the distribution of a single variable?</td>
        <td style="padding:10px 12px;color:#a7f3d0;font-weight:600;">Histogram / Box Plot</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:var(--muted);">Is there a correlation between two numeric variables?</td>
        <td style="padding:10px 12px;color:#a7f3d0;font-weight:600;">Scatter Plot</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:var(--muted);">What share does each category make up?</td>
        <td style="padding:10px 12px;color:#a7f3d0;font-weight:600;">Pie / Donut Chart</td>
      </tr>
      <tr>
        <td style="padding:10px 12px;color:var(--muted);">How do many variables correlate with each other at once?</td>
        <td style="padding:10px 12px;color:#a7f3d0;font-weight:600;">Heatmap / Pair Plot</td>
      </tr>
    </tbody>
  </table>
</div>

<h3>Setting Up Your Environment</h3>
<p>All examples in this module use Matplotlib and Seaborn. Install them with pip, then import them with the conventional aliases the entire Python community uses — do not rename them, as teammates and online resources will always use these aliases.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Standard Imports for Visualization</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Install once in terminal: pip install matplotlib seaborn pandas numpy</span>

<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt    <span style="color:#6b7280;"># The de facto standard alias — always use 'plt'</span>
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns               <span style="color:#6b7280;"># Always use 'sns'</span>
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd                <span style="color:#6b7280;"># Always use 'pd'</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np                 <span style="color:#6b7280;"># Always use 'np'</span>

<span style="color:#6b7280;"># Set Seaborn's visual theme — run once at the top of every notebook</span>
<span style="color:#6b7280;"># Options: 'darkgrid', 'whitegrid', 'dark', 'white', 'ticks'</span>
sns.<span style="color:#93c5fd;">set_theme</span>(style=<span style="color:#a7f3d0;">"whitegrid"</span>, palette=<span style="color:#a7f3d0;">"muted"</span>)

<span style="color:#6b7280;"># Verify library versions</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Matplotlib: {plt.matplotlib.__version__}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Seaborn:    {sns.__version__}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Pandas:     {pd.__version__}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"NumPy:      {np.__version__}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Matplotlib: 3.8.0
Seaborn:    0.13.0
Pandas:     2.1.0
NumPy:      1.26.0</div>
  </div>
</div>

<h3>Your First Plot: The Anatomy of a Matplotlib Figure</h3>
<p>Every Matplotlib visualization is built from two core objects: the <strong>Figure</strong> (the entire canvas, like a blank sheet of paper) and the <strong>Axes</strong> (the actual plot area inside the figure, including axes, labels, and data). Understanding this distinction is critical — most customization happens at the Axes level, not the Figure level.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Figure & Axes Anatomy</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#6b7280;"># Step 1: Create the Figure and a single Axes object</span>
fig, ax = plt.<span style="color:#93c5fd;">subplots</span>(figsize=(<span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">4</span>))   <span style="color:#6b7280;"># figsize=(width, height) in inches</span>

<span style="color:#6b7280;"># Step 2: Generate some data</span>
x = np.<span style="color:#93c5fd;">linspace</span>(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">100</span>)   <span style="color:#6b7280;"># 100 evenly spaced points from 0 to 10</span>
y = np.<span style="color:#93c5fd;">sin</span>(x)

<span style="color:#6b7280;"># Step 3: Plot on the Axes (NOT plt.plot — use ax.plot for proper control)</span>
ax.<span style="color:#93c5fd;">plot</span>(x, y, color=<span style="color:#a7f3d0;">"steelblue"</span>, linewidth=<span style="color:#fcd34d;">2</span>, label=<span style="color:#a7f3d0;">"sin(x)"</span>)

<span style="color:#6b7280;"># Step 4: Add labels, title, and legend — always label your axes</span>
ax.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"My First Matplotlib Plot"</span>, fontsize=<span style="color:#fcd34d;">14</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"X Values"</span>, fontsize=<span style="color:#fcd34d;">12</span>)
ax.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"sin(x)"</span>, fontsize=<span style="color:#fcd34d;">12</span>)
ax.<span style="color:#93c5fd;">legend</span>()

<span style="color:#6b7280;"># Step 5: Display the figure</span>
plt.<span style="color:#93c5fd;">tight_layout</span>()   <span style="color:#6b7280;"># Prevent labels from getting cut off</span>
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>A smooth sine wave chart is rendered on screen.
Labels: Title, X axis, Y axis, and Legend are all visible.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '15.1 Introduction to Data Visualization',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L15_1', [
                ['q' => 'What is the primary purpose of data visualization in data science?', 'opts' => ['To make code run faster', 'To replace the need for statistics', 'To communicate insights and patterns visually', 'To store data more efficiently'], 'ans' => 2, 'exp' => 'Data visualization translates complex data into visual form so patterns, trends, and relationships can be understood quickly. It bridges computation and human decision-making.'],
                ['q' => 'Which library serves as the foundational layer for most Python visualization?', 'opts' => ['Seaborn', 'Plotly', 'Matplotlib', 'Pandas'], 'ans' => 2, 'exp' => 'Matplotlib is the foundational visualization library in Python. Seaborn is built on top of it, and Pandas .plot() also uses Matplotlib as its backend.'],
                ['q' => 'Which chart type is best for showing how a value changes over time?', 'opts' => ['Bar Chart', 'Pie Chart', 'Scatter Plot', 'Line Chart'], 'ans' => 3, 'exp' => 'Line charts connect data points in order and are ideal for showing trends over time — such as stock prices, temperature over months, or model accuracy across training epochs.'],
                ['q' => 'What are the two core Matplotlib objects created by plt.subplots()?', 'opts' => ['Graph and Plot', 'Canvas and Layer', 'Figure and Axes', 'Window and Frame'], 'ans' => 2, 'exp' => 'plt.subplots() returns a Figure (the entire canvas) and an Axes object (the actual plotting area with coordinate systems). Most customization happens on the Axes object.'],
                ['q' => 'What does sns.set_theme() do?', 'opts' => ['Installs a new Seaborn version', 'Sets the global visual style and color palette for all subsequent Seaborn plots', 'Connects to an online theme database', 'Changes Python\'s color output in the terminal'], 'ans' => 1, 'exp' => 'sns.set_theme() applies a global visual style (like "whitegrid" or "darkgrid") and a color palette to all plots created after it is called. It is typically called once at the top of a notebook.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 15.2 — Matplotlib Deep Dive: Line & Bar Charts
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Matplotlib Deep Dive: Line & Bar Charts</h2>
<p>Matplotlib is the bedrock of Python visualization — every other library is built on top of it or takes inspiration from it. Mastering Matplotlib means you can customize any chart to pixel-perfect precision: change every color, font, axis tick, gridline, and annotation. In this lesson we go deep on the two most common chart types: <strong>line charts</strong> for time series and trends, and <strong>bar charts</strong> for category comparisons.</p>

<h3>Line Charts: Visualizing Trends Over Time</h3>
<p>A line chart draws a continuous line connecting data points in sequence. It is the go-to for time series data — anything that changes over an ordered sequence of values. Key parameters: <code>color</code>, <code>linewidth</code>, <code>linestyle</code> (<code>'-'</code>, <code>'--'</code>, <code>':'</code>, <code>'-.'</code>), and <code>marker</code>.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Multi-Line Chart with Styling</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

months = [<span style="color:#a7f3d0;">"Jan"</span>, <span style="color:#a7f3d0;">"Feb"</span>, <span style="color:#a7f3d0;">"Mar"</span>, <span style="color:#a7f3d0;">"Apr"</span>, <span style="color:#a7f3d0;">"May"</span>, <span style="color:#a7f3d0;">"Jun"</span>,
          <span style="color:#a7f3d0;">"Jul"</span>, <span style="color:#a7f3d0;">"Aug"</span>, <span style="color:#a7f3d0;">"Sep"</span>, <span style="color:#a7f3d0;">"Oct"</span>, <span style="color:#a7f3d0;">"Nov"</span>, <span style="color:#a7f3d0;">"Dec"</span>]
revenue_2023 = [<span style="color:#fcd34d;">120</span>, <span style="color:#fcd34d;">135</span>, <span style="color:#fcd34d;">148</span>, <span style="color:#fcd34d;">162</span>, <span style="color:#fcd34d;">178</span>, <span style="color:#fcd34d;">195</span>,
                <span style="color:#fcd34d;">210</span>, <span style="color:#fcd34d;">225</span>, <span style="color:#fcd34d;">198</span>, <span style="color:#fcd34d;">215</span>, <span style="color:#fcd34d;">240</span>, <span style="color:#fcd34d;">280</span>]
revenue_2024 = [<span style="color:#fcd34d;">145</span>, <span style="color:#fcd34d;">160</span>, <span style="color:#fcd34d;">175</span>, <span style="color:#fcd34d;">190</span>, <span style="color:#fcd34d;">210</span>, <span style="color:#fcd34d;">235</span>,
                <span style="color:#fcd34d;">255</span>, <span style="color:#fcd34d;">270</span>, <span style="color:#fcd34d;">248</span>, <span style="color:#fcd34d;">265</span>, <span style="color:#fcd34d;">295</span>, <span style="color:#fcd34d;">340</span>]

fig, ax = plt.<span style="color:#93c5fd;">subplots</span>(figsize=(<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">5</span>))

<span style="color:#6b7280;"># Plot two lines with distinct styles</span>
ax.<span style="color:#93c5fd;">plot</span>(months, revenue_2023,
       color=<span style="color:#a7f3d0;">"steelblue"</span>, linewidth=<span style="color:#fcd34d;">2</span>, linestyle=<span style="color:#a7f3d0;">"--"</span>,
       marker=<span style="color:#a7f3d0;">"o"</span>, markersize=<span style="color:#fcd34d;">5</span>, label=<span style="color:#a7f3d0;">"2023 Revenue"</span>)

ax.<span style="color:#93c5fd;">plot</span>(months, revenue_2024,
       color=<span style="color:#a7f3d0;">"#e74c3c"</span>, linewidth=<span style="color:#fcd34d;">2.5</span>, linestyle=<span style="color:#a7f3d0;">"-"</span>,
       marker=<span style="color:#a7f3d0;">"s"</span>, markersize=<span style="color:#fcd34d;">5</span>, label=<span style="color:#a7f3d0;">"2024 Revenue"</span>)

<span style="color:#6b7280;"># Shade the area between the two lines to emphasize growth</span>
ax.<span style="color:#93c5fd;">fill_between</span>(months, revenue_2023, revenue_2024,
                alpha=<span style="color:#fcd34d;">0.1</span>, color=<span style="color:#a7f3d0;">"green"</span>)

ax.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Monthly Revenue: 2023 vs 2024 (in $K)"</span>, fontsize=<span style="color:#fcd34d;">14</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Month"</span>)
ax.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Revenue ($K)"</span>)
ax.<span style="color:#93c5fd;">legend</span>(loc=<span style="color:#a7f3d0;">"upper left"</span>)
ax.<span style="color:#93c5fd;">grid</span>(True, linestyle=<span style="color:#a7f3d0;">"--"</span>, alpha=<span style="color:#fcd34d;">0.4</span>)

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Two overlapping line charts with markers.
A green shaded region shows the year-over-year growth gap.
2024 consistently outperforms 2023, especially in Q4.</div>
  </div>
</div>

<h3>Bar Charts: Comparing Categories</h3>
<p>Bar charts display rectangular bars whose lengths are proportional to the values they represent. They are ideal for comparing discrete categories. Horizontal bars (<code>ax.barh()</code>) work better when category labels are long — they prevent the text from rotating awkwardly.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Vertical & Horizontal Bar Charts</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

departments    = [<span style="color:#a7f3d0;">"Engineering"</span>, <span style="color:#a7f3d0;">"Marketing"</span>, <span style="color:#a7f3d0;">"Sales"</span>, <span style="color:#a7f3d0;">"HR"</span>, <span style="color:#a7f3d0;">"Product"</span>]
headcount      = [<span style="color:#fcd34d;">45</span>, <span style="color:#fcd34d;">18</span>, <span style="color:#fcd34d;">32</span>, <span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">25</span>]
colors         = [<span style="color:#a7f3d0;">"#3498db"</span>, <span style="color:#a7f3d0;">"#e74c3c"</span>, <span style="color:#a7f3d0;">"#2ecc71"</span>, <span style="color:#a7f3d0;">"#f39c12"</span>, <span style="color:#a7f3d0;">"#9b59b6"</span>]

fig, (ax1, ax2) = plt.<span style="color:#93c5fd;">subplots</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">5</span>))

<span style="color:#6b7280;"># --- Left: Vertical Bar Chart ---</span>
bars = ax1.<span style="color:#93c5fd;">bar</span>(departments, headcount, color=colors, edgecolor=<span style="color:#a7f3d0;">"white"</span>, linewidth=<span style="color:#fcd34d;">0.8</span>)

<span style="color:#6b7280;"># Add value labels ON TOP of each bar</span>
<span style="color:#c4b5fd;">for</span> bar <span style="color:#c4b5fd;">in</span> bars:
    ax1.<span style="color:#93c5fd;">text</span>(bar.<span style="color:#93c5fd;">get_x</span>() + bar.<span style="color:#93c5fd;">get_width</span>() / <span style="color:#fcd34d;">2</span>,
             bar.<span style="color:#93c5fd;">get_height</span>() + <span style="color:#fcd34d;">0.5</span>,
             <span style="color:#93c5fd;">str</span>(<span style="color:#93c5fd;">int</span>(bar.<span style="color:#93c5fd;">get_height</span>())),
             ha=<span style="color:#a7f3d0;">"center"</span>, va=<span style="color:#a7f3d0;">"bottom"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>, fontsize=<span style="color:#fcd34d;">10</span>)

ax1.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Headcount by Department"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax1.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Number of Employees"</span>)

<span style="color:#6b7280;"># --- Right: Horizontal Bar Chart (better for long labels) ---</span>
ax2.<span style="color:#93c5fd;">barh</span>(departments, headcount, color=colors, edgecolor=<span style="color:#a7f3d0;">"white"</span>)
ax2.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Same Data — Horizontal Layout"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax2.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Number of Employees"</span>)
ax2.<span style="color:#93c5fd;">invert_yaxis</span>()   <span style="color:#6b7280;"># Largest bar at top for readability</span>

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Left panel: Vertical colored bars with value labels on top.
Right panel: Same data as horizontal bars sorted largest-first.
Engineering has the highest headcount (45), HR the lowest (12).</div>
  </div>
</div>

<h3>Grouped Bar Charts: Comparing Multiple Groups Side by Side</h3>
<p>When you need to compare two or more sub-groups across categories, group bars side by side. The trick is using NumPy to manually offset the x positions of each group. This technique is fundamental and gives you precise control over bar width and spacing.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Grouped Bar Chart</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

quarters = [<span style="color:#a7f3d0;">"Q1"</span>, <span style="color:#a7f3d0;">"Q2"</span>, <span style="color:#a7f3d0;">"Q3"</span>, <span style="color:#a7f3d0;">"Q4"</span>]
product_a = [<span style="color:#fcd34d;">85</span>, <span style="color:#fcd34d;">92</span>, <span style="color:#fcd34d;">78</span>, <span style="color:#fcd34d;">110</span>]
product_b = [<span style="color:#fcd34d;">60</span>, <span style="color:#fcd34d;">75</span>, <span style="color:#fcd34d;">95</span>, <span style="color:#fcd34d;">88</span>]

x     = np.<span style="color:#93c5fd;">arange</span>(<span style="color:#93c5fd;">len</span>(quarters))   <span style="color:#6b7280;"># [0, 1, 2, 3] — center positions</span>
width = <span style="color:#fcd34d;">0.35</span>                          <span style="color:#6b7280;"># Width of each bar</span>

fig, ax = plt.<span style="color:#93c5fd;">subplots</span>(figsize=(<span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">5</span>))

<span style="color:#6b7280;"># Offset each group left and right of center by half the width</span>
ax.<span style="color:#93c5fd;">bar</span>(x - width/<span style="color:#fcd34d;">2</span>, product_a, width, label=<span style="color:#a7f3d0;">"Product A"</span>, color=<span style="color:#a7f3d0;">"#3498db"</span>)
ax.<span style="color:#93c5fd;">bar</span>(x + width/<span style="color:#fcd34d;">2</span>, product_b, width, label=<span style="color:#a7f3d0;">"Product B"</span>, color=<span style="color:#a7f3d0;">"#e74c3c"</span>)

ax.<span style="color:#93c5fd;">set_xticks</span>(x)
ax.<span style="color:#93c5fd;">set_xticklabels</span>(quarters)
ax.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Quarterly Sales by Product ($K)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Sales ($K)"</span>)
ax.<span style="color:#93c5fd;">legend</span>()

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Four pairs of grouped bars, one blue (Product A) and one red (Product B).
Viewer can immediately compare product performance within each quarter.
Product A peaks in Q4; Product B peaks in Q3.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '15.2 Matplotlib: Line & Bar Charts',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L15_2', [
                ['q' => 'Which linestyle parameter draws a dashed line in Matplotlib?', 'opts' => ['"-"', '"."', '"--"', '"o"'], 'ans' => 2, 'exp' => '"--" produces a dashed line. "-" is solid, ":" is dotted, and "-." is dash-dot. The marker parameter (like "o") controls the point shape, not the line style.'],
                ['q' => 'What does ax.fill_between() do in a line chart?', 'opts' => ['Fills the chart background with a color', 'Shades the area between two y-value lines', 'Adds a gradient to bar charts', 'Fills missing data points automatically'], 'ans' => 1, 'exp' => 'fill_between(x, y1, y2) shades the region between two lines, making it easy to visualize the gap or difference between two series — such as year-over-year growth.'],
                ['q' => 'When should you prefer ax.barh() over ax.bar()?', 'opts' => ['When values are negative', 'When you have fewer than 3 categories', 'When category labels are long and would overlap horizontally', 'When comparing time series data'], 'ans' => 2, 'exp' => 'Horizontal bars (barh) are preferred when category names are long. They display labels along the y-axis where there is more space, preventing the 45° rotated text that is common in vertical bar charts.'],
                ['q' => 'In a grouped bar chart, why is numpy.arange() used for x positions?', 'opts' => ['To generate random bar heights', 'To create evenly spaced numeric positions that can be offset per group', 'To sort the categories alphabetically', 'To set the chart width automatically'], 'ans' => 1, 'exp' => 'np.arange() creates evenly spaced integers [0, 1, 2, ...] as center positions. You then offset each group by +/- half the bar width to place them side by side at the same x category.'],
                ['q' => 'What method adds a text label on top of a bar using its height as the y position?', 'opts' => ['ax.annotate()', 'ax.text()', 'ax.label()', 'ax.title()'], 'ans' => 1, 'exp' => 'ax.text(x, y, s) places text at coordinates (x, y) with string s. You use bar.get_x() + bar.get_width()/2 for horizontal center and bar.get_height() for vertical position above the bar.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 15.3 — Histograms, Box Plots & Distribution Visualizations
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Histograms, Box Plots & Distribution Visualization</h2>
<p>Before you model your data, you must <em>understand</em> your data. Distribution visualizations reveal the shape, spread, and quirks of your variables. Is your data normally distributed or heavily skewed? Are there extreme outliers? Is the distribution bimodal, suggesting two distinct subgroups? These are questions you answer with histograms, box plots, and violin plots — and you answer them <em>before</em> choosing a model.</p>

<h3>Histograms: Visualizing a Single Variable's Distribution</h3>
<p>A histogram divides a continuous variable into equal-width <strong>bins</strong> and counts how many data points fall into each bin. The height of each bar represents frequency (count) or density (probability). Choosing the right number of bins is critical — too few hides structure, too many creates noise. Seaborn's <code>histplot</code> also overlays a <strong>KDE (Kernel Density Estimate)</strong> curve, which smooths the histogram into a continuous probability distribution estimate.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Histogram with KDE</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#93c5fd;">np.random.seed</span>(<span style="color:#fcd34d;">42</span>)
<span style="color:#6b7280;"># Simulate exam scores: mostly normal, slightly skewed</span>
scores = np.random.<span style="color:#93c5fd;">normal</span>(loc=<span style="color:#fcd34d;">72</span>, scale=<span style="color:#fcd34d;">12</span>, size=<span style="color:#fcd34d;">500</span>)
scores = np.<span style="color:#93c5fd;">clip</span>(scores, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">100</span>)   <span style="color:#6b7280;"># Keep scores in 0-100 range</span>

fig, axes = plt.<span style="color:#93c5fd;">subplots</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">3</span>, figsize=(<span style="color:#fcd34d;">15</span>, <span style="color:#fcd34d;">4</span>))

<span style="color:#6b7280;"># Too few bins (5): loses shape detail</span>
axes[<span style="color:#fcd34d;">0</span>].<span style="color:#93c5fd;">hist</span>(scores, bins=<span style="color:#fcd34d;">5</span>, color=<span style="color:#a7f3d0;">"steelblue"</span>, edgecolor=<span style="color:#a7f3d0;">"white"</span>)
axes[<span style="color:#fcd34d;">0</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"5 bins — Too Few"</span>)

<span style="color:#6b7280;"># Goldilocks: 20-30 bins works well for 500 data points</span>
sns.<span style="color:#93c5fd;">histplot</span>(scores, bins=<span style="color:#fcd34d;">25</span>, kde=<span style="color:#fca5a5;">True</span>,
             color=<span style="color:#a7f3d0;">"steelblue"</span>, ax=axes[<span style="color:#fcd34d;">1</span>])
axes[<span style="color:#fcd34d;">1</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"25 bins + KDE — Just Right"</span>)
axes[<span style="color:#fcd34d;">1</span>].<span style="color:#93c5fd;">axvline</span>(np.<span style="color:#93c5fd;">mean</span>(scores), color=<span style="color:#a7f3d0;">"red"</span>,
               linestyle=<span style="color:#a7f3d0;">"--"</span>, label=<span style="color:#a7f3d0;">f"Mean: {np.mean(scores):.1f}"</span>)
axes[<span style="color:#fcd34d;">1</span>].<span style="color:#93c5fd;">legend</span>()

<span style="color:#6b7280;"># Too many bins (100): noisy, hard to read</span>
axes[<span style="color:#fcd34d;">2</span>].<span style="color:#93c5fd;">hist</span>(scores, bins=<span style="color:#fcd34d;">100</span>, color=<span style="color:#a7f3d0;">"steelblue"</span>, edgecolor=<span style="color:#a7f3d0;">"white"</span>)
axes[<span style="color:#fcd34d;">2</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"100 bins — Too Many"</span>)

<span style="color:#c4b5fd;">for</span> ax <span style="color:#c4b5fd;">in</span> axes:
    ax.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Exam Score"</span>)

plt.<span style="color:#93c5fd;">suptitle</span>(<span style="color:#a7f3d0;">"Effect of Bin Count on Histogram Readability"</span>, fontsize=<span style="color:#fcd34d;">14</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Three histograms side by side showing the same data.
5 bins: A rough 5-bar shape — no nuance.
25 bins + KDE: Bell curve shape clearly visible, mean line at ~72.
100 bins: Jagged, noisy — too granular to read the shape.</div>
  </div>
</div>

<h3>Box Plots: The Five-Number Summary in One Chart</h3>
<p>A box plot compresses an entire distribution into five statistics: the <strong>minimum</strong>, <strong>Q1 (25th percentile)</strong>, <strong>median</strong>, <strong>Q3 (75th percentile)</strong>, and <strong>maximum</strong>. The box spans the <strong>IQR (Interquartile Range)</strong> from Q1 to Q3. Points beyond 1.5× the IQR from either hinge are plotted individually as <strong>outliers</strong>. Box plots are exceptional for comparing distributions across multiple categories in a single compact view.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Box Plot & Violin Plot Comparison</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

np.random.<span style="color:#93c5fd;">seed</span>(<span style="color:#fcd34d;">0</span>)
<span style="color:#6b7280;"># Simulate salaries for 3 departments</span>
df = pd.<span style="color:#93c5fd;">DataFrame</span>({
    <span style="color:#a7f3d0;">"salary"</span>: np.<span style="color:#93c5fd;">concatenate</span>([
        np.random.<span style="color:#93c5fd;">normal</span>(<span style="color:#fcd34d;">95000</span>, <span style="color:#fcd34d;">15000</span>, <span style="color:#fcd34d;">200</span>),   <span style="color:#6b7280;"># Engineering</span>
        np.random.<span style="color:#93c5fd;">normal</span>(<span style="color:#fcd34d;">65000</span>, <span style="color:#fcd34d;">10000</span>, <span style="color:#fcd34d;">200</span>),   <span style="color:#6b7280;"># Marketing</span>
        np.random.<span style="color:#93c5fd;">normal</span>(<span style="color:#fcd34d;">75000</span>, <span style="color:#fcd34d;">20000</span>, <span style="color:#fcd34d;">200</span>),   <span style="color:#6b7280;"># Sales</span>
    ]),
    <span style="color:#a7f3d0;">"department"</span>: [<span style="color:#a7f3d0;">"Engineering"</span>]*<span style="color:#fcd34d;">200</span> + [<span style="color:#a7f3d0;">"Marketing"</span>]*<span style="color:#fcd34d;">200</span> + [<span style="color:#a7f3d0;">"Sales"</span>]*<span style="color:#fcd34d;">200</span>
})

fig, (ax1, ax2) = plt.<span style="color:#93c5fd;">subplots</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">5</span>))

<span style="color:#6b7280;"># Box Plot: concise five-number summary, clear outlier dots</span>
sns.<span style="color:#93c5fd;">boxplot</span>(data=df, x=<span style="color:#a7f3d0;">"department"</span>, y=<span style="color:#a7f3d0;">"salary"</span>,
            palette=<span style="color:#a7f3d0;">"Set2"</span>, ax=ax1)
ax1.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Box Plot: Salary by Department"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax1.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Annual Salary ($)"</span>)

<span style="color:#6b7280;"># Violin Plot: shows FULL distribution shape + box plot inside</span>
sns.<span style="color:#93c5fd;">violinplot</span>(data=df, x=<span style="color:#a7f3d0;">"department"</span>, y=<span style="color:#a7f3d0;">"salary"</span>,
               palette=<span style="color:#a7f3d0;">"Set2"</span>, inner=<span style="color:#a7f3d0;">"box"</span>, ax=ax2)
ax2.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Violin Plot: Same Data"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax2.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Annual Salary ($)"</span>)

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Left: Box plots showing Engineering salaries are highest and widest.
Right: Violin plots reveal Engineering has a symmetric bell shape;
       Sales has a wider spread, visible in the violin's wider body.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '15.3 Histograms, Box Plots & Distributions',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L15_3', [
                ['q' => 'What does the height of a histogram bar represent?', 'opts' => ['The mean of data in that bin', 'The maximum value in that bin', 'The number (or proportion) of data points falling within that bin', 'The standard deviation'], 'ans' => 2, 'exp' => 'Each bar in a histogram shows the frequency (count) or relative frequency (density) of observations whose values fall within that bin\'s range. Height = how many data points are in that interval.'],
                ['q' => 'What does the KDE curve added by seaborn.histplot(kde=True) represent?', 'opts' => ['A line connecting the tops of each bar', 'A smoothed continuous estimate of the probability distribution', 'The cumulative sum of all bars', 'The standard error of the mean'], 'ans' => 1, 'exp' => 'KDE (Kernel Density Estimate) places a smooth kernel (usually Gaussian) over each data point and sums them to produce a continuous curve approximating the underlying probability distribution.'],
                ['q' => 'In a box plot, what does the IQR (Interquartile Range) represent?', 'opts' => ['The range from minimum to maximum', 'The distance from Q1 to Q3 — the middle 50% of data', 'The standard deviation', 'The distance from mean to median'], 'ans' => 1, 'exp' => 'The IQR is Q3 minus Q1 — the span of the box itself. It contains the middle 50% of all observations and is resistant to outliers, making it a robust measure of spread.'],
                ['q' => 'How does a violin plot differ from a box plot?', 'opts' => ['Violin plots only show the median', 'Violin plots use bars instead of boxes', 'Violin plots show the full distribution shape using KDE, not just five summary statistics', 'Violin plots are only for categorical data'], 'ans' => 2, 'exp' => 'Violin plots combine a box plot with a mirrored KDE, showing the full shape of the distribution. You can see multimodal distributions (two bumps) that a box plot would hide.'],
                ['q' => 'What does ax.axvline() add to a histogram?', 'opts' => ['A vertical dashed line at a specified x value', 'A horizontal grid line', 'An arrow annotation', 'A filled vertical bar'], 'ans' => 0, 'exp' => 'axvline(x) draws a vertical line spanning the entire y-axis at position x. It is commonly used to mark the mean, median, or a threshold on a histogram.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 15.4 — Scatter Plots & Correlation Analysis
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Scatter Plots & Correlation Analysis</h2>
<p>A scatter plot places each data point as a dot at coordinates <code>(x, y)</code>, making it the primary tool for examining the <strong>relationship between two continuous variables</strong>. Do they increase together (positive correlation)? Does one increase as the other decreases (negative correlation)? Is there no relationship at all? Does the relationship curve rather than being linear? These are questions a scatter plot answers in seconds — and that a Pearson correlation coefficient alone would mislead you about.</p>

<h3>Basic Scatter Plot with Regression Line</h3>
<p>Seaborn's <code>regplot</code> and <code>lmplot</code> overlay a linear regression line with a confidence interval band — letting you simultaneously see the data cloud and the best-fit line. The shaded region around the line is the 95% confidence interval for the regression estimate.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Scatter Plot with Regression Line</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

np.random.<span style="color:#93c5fd;">seed</span>(<span style="color:#fcd34d;">7</span>)
<span style="color:#6b7280;"># Simulate: study hours vs exam score</span>
hours   = np.random.<span style="color:#93c5fd;">uniform</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">120</span>)
scores  = <span style="color:#fcd34d;">50</span> + <span style="color:#fcd34d;">4.5</span> * hours + np.random.<span style="color:#93c5fd;">normal</span>(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">120</span>)
df = pd.<span style="color:#93c5fd;">DataFrame</span>({<span style="color:#a7f3d0;">"Study Hours"</span>: hours, <span style="color:#a7f3d0;">"Exam Score"</span>: scores})

fig, ax = plt.<span style="color:#93c5fd;">subplots</span>(figsize=(<span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">5</span>))

<span style="color:#6b7280;"># regplot: scatter + linear regression + 95% CI band</span>
sns.<span style="color:#93c5fd;">regplot</span>(data=df, x=<span style="color:#a7f3d0;">"Study Hours"</span>, y=<span style="color:#a7f3d0;">"Exam Score"</span>,
            scatter_kws={<span style="color:#a7f3d0;">"alpha"</span>: <span style="color:#fcd34d;">0.5</span>, <span style="color:#a7f3d0;">"color"</span>: <span style="color:#a7f3d0;">"steelblue"</span>},
            line_kws={<span style="color:#a7f3d0;">"color"</span>: <span style="color:#a7f3d0;">"red"</span>, <span style="color:#a7f3d0;">"linewidth"</span>: <span style="color:#fcd34d;">2</span>},
            ax=ax)

<span style="color:#6b7280;"># Compute and display correlation coefficient</span>
corr = df[<span style="color:#a7f3d0;">"Study Hours"</span>].<span style="color:#93c5fd;">corr</span>(df[<span style="color:#a7f3d0;">"Exam Score"</span>])
ax.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">f"Study Hours vs Exam Score  (r = {corr:.2f})"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Daily Study Hours"</span>)
ax.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Exam Score (%)"</span>)

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>A scatter of blue dots with a red regression line trending upward.
Shaded region = 95% CI. Title shows r ≈ 0.82 — strong positive correlation.
Interpretation: Students who study more generally score higher.</div>
  </div>
</div>

<h3>Encoding a Third Variable with Color & Size</h3>
<p>A standard scatter plot encodes two variables (x and y). You can encode a <em>third</em> variable using point <strong>color</strong> (hue) for categories, and a <em>fourth</em> variable using point <strong>size</strong>. This is called a <strong>bubble chart</strong> when size is used. Seaborn's <code>scatterplot</code> handles this elegantly with <code>hue</code> and <code>size</code> parameters.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Multi-Variable Scatter (Bubble Chart)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns

<span style="color:#6b7280;"># Load Seaborn's built-in 'tips' dataset (restaurant tips)</span>
tips = sns.<span style="color:#93c5fd;">load_dataset</span>(<span style="color:#a7f3d0;">"tips"</span>)

fig, ax = plt.<span style="color:#93c5fd;">subplots</span>(figsize=(<span style="color:#fcd34d;">9</span>, <span style="color:#fcd34d;">6</span>))

<span style="color:#6b7280;"># x = total bill, y = tip, color = time of day, size = party size</span>
sns.<span style="color:#93c5fd;">scatterplot</span>(data=tips,
                x=<span style="color:#a7f3d0;">"total_bill"</span>, y=<span style="color:#a7f3d0;">"tip"</span>,
                hue=<span style="color:#a7f3d0;">"time"</span>,        <span style="color:#6b7280;"># Color by Lunch / Dinner</span>
                size=<span style="color:#a7f3d0;">"size"</span>,        <span style="color:#6b7280;"># Point size = party size</span>
                sizes=(<span style="color:#fcd34d;">30</span>, <span style="color:#fcd34d;">250</span>),   <span style="color:#6b7280;"># Min/max bubble size in points²</span>
                palette=<span style="color:#a7f3d0;">"Set1"</span>,
                alpha=<span style="color:#fcd34d;">0.6</span>, ax=ax)

ax.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Restaurant Tips: Bill vs Tip Amount"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Total Bill ($)"</span>)
ax.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Tip ($)"</span>)

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Scatter with blue dots (Lunch) and red dots (Dinner).
Bubble sizes vary by party size — larger parties leave larger tips.
A positive trend is visible: higher bills correlate with higher tips.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '15.4 Scatter Plots & Correlation Analysis',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L15_4', [
                ['q' => 'What does a scatter plot primarily reveal?', 'opts' => ['Category frequencies', 'The relationship or correlation between two continuous variables', 'The distribution of a single variable', 'Time series trends'], 'ans' => 1, 'exp' => 'Scatter plots plot each observation as a point at (x, y), making the relationship between two continuous variables immediately visible — positive, negative, non-linear, or no correlation.'],
                ['q' => 'What does the shaded band around a seaborn.regplot() regression line represent?', 'opts' => ['The standard deviation of y', 'The prediction interval for future observations', 'The 95% confidence interval for the regression line estimate', 'The range of all x values'], 'ans' => 2, 'exp' => 'The shaded band is the 95% confidence interval (CI) for the mean regression line — it shows how uncertain we are about the line\'s true position. A wider band means more uncertainty.'],
                ['q' => 'In seaborn.scatterplot(), what does the "hue" parameter control?', 'opts' => ['The size of each point', 'The transparency of points', 'The color of points, mapped to a categorical or continuous variable', 'The shape of each marker'], 'ans' => 2, 'exp' => 'hue maps a variable to point color. For categorical variables it assigns distinct colors per category; for continuous variables it uses a color gradient. This encodes a third dimension of data.'],
                ['q' => 'What is a correlation coefficient of r = -0.85 telling you?', 'opts' => ['No relationship', 'A weak positive relationship', 'A moderate positive relationship', 'A strong negative relationship — as x increases, y tends to decrease'], 'ans' => 3, 'exp' => 'r ranges from -1 to +1. Values near -1 indicate a strong negative linear correlation. r = -0.85 means as x increases, y strongly tends to decrease — about 72% of y\'s variance is explained by x (r²).'],
                ['q' => 'Why should you look at a scatter plot BEFORE computing a correlation coefficient?', 'opts' => ['Scatter plots are faster to compute', 'Correlation only works on scatter data', 'Anscombe\'s quartet shows four datasets with identical r values but completely different visual patterns — a single number can be misleading', 'Python requires plotting before computing statistics'], 'ans' => 2, 'exp' => 'Anscombe\'s quartet (1973) demonstrated four datasets with nearly identical means, variances, and correlation coefficients but wildly different scatter plot shapes — one linear, one curved, one with an outlier. Always visualize first.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 15.5 — Heatmaps & Correlation Matrices
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Heatmaps & Correlation Matrices</h2>
<p>When you have a dataset with many numeric columns, computing pairwise correlations produces a matrix — and a matrix of 10 or more columns becomes impossible to read as numbers. A <strong>heatmap</strong> encodes each cell value as a color, turning an unreadable number table into an immediately scannable visual. Data scientists use correlation heatmaps in every EDA to identify which features are strongly related before feature engineering or model building.</p>

<h3>Correlation Matrix Heatmap</h3>
<p>The workflow is: load your DataFrame → call <code>.corr()</code> to compute pairwise Pearson correlations → pass the result to <code>sns.heatmap()</code>. Values near +1 (dark red/warm) indicate strong positive correlation; values near -1 (dark blue/cold) indicate strong negative correlation; values near 0 (white/neutral) indicate little linear relationship.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Correlation Heatmap</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#6b7280;"># Load Seaborn's built-in dataset</span>
penguins = sns.<span style="color:#93c5fd;">load_dataset</span>(<span style="color:#a7f3d0;">"penguins"</span>).<span style="color:#93c5fd;">dropna</span>()

<span style="color:#6b7280;"># Select only numeric columns and compute pairwise Pearson correlations</span>
corr_matrix = penguins.<span style="color:#93c5fd;">select_dtypes</span>(include=<span style="color:#a7f3d0;">"number"</span>).<span style="color:#93c5fd;">corr</span>()

<span style="color:#6b7280;"># Mask the upper triangle to avoid redundant information</span>
mask = np.<span style="color:#93c5fd;">triu</span>(np.<span style="color:#93c5fd;">ones_like</span>(corr_matrix, dtype=<span style="color:#93c5fd;">bool</span>))

fig, ax = plt.<span style="color:#93c5fd;">subplots</span>(figsize=(<span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">6</span>))

sns.<span style="color:#93c5fd;">heatmap</span>(
    corr_matrix,
    mask=mask,               <span style="color:#6b7280;"># Hide the upper triangle (mirror of lower)</span>
    annot=<span style="color:#fca5a5;">True</span>,             <span style="color:#6b7280;"># Show numeric values inside cells</span>
    fmt=<span style="color:#a7f3d0;">".2f"</span>,              <span style="color:#6b7280;"># 2 decimal places</span>
    cmap=<span style="color:#a7f3d0;">"coolwarm"</span>,        <span style="color:#6b7280;"># Red = positive, Blue = negative</span>
    center=<span style="color:#fcd34d;">0</span>,              <span style="color:#6b7280;"># 0 correlation = white (neutral)</span>
    linewidths=<span style="color:#fcd34d;">0.5</span>,
    square=<span style="color:#fca5a5;">True</span>,
    ax=ax
)

ax.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Penguin Dataset — Pearson Correlation Matrix"</span>,
            fontweight=<span style="color:#a7f3d0;">"bold"</span>, pad=<span style="color:#fcd34d;">12</span>)

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Lower-triangle heatmap with colored cells.
Flipper length and body mass show r ≈ 0.87 (deep red — strong positive).
Bill depth and flipper length show r ≈ -0.58 (blue — moderate negative).
Diagonal and upper triangle are hidden to reduce clutter.</div>
  </div>
</div>

<h3>Custom Heatmaps: Pivot Tables & Frequency Matrices</h3>
<p>Heatmaps are not limited to correlation matrices. Any 2D matrix of values — sales by region and product, customer activity by hour and day, model performance by hyperparameter — can be visualized as a heatmap. The key is reshaping your DataFrame into a pivot table first.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Website Traffic Heatmap by Hour & Day</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd

np.random.<span style="color:#93c5fd;">seed</span>(<span style="color:#fcd34d;">1</span>)
days  = [<span style="color:#a7f3d0;">"Mon"</span>, <span style="color:#a7f3d0;">"Tue"</span>, <span style="color:#a7f3d0;">"Wed"</span>, <span style="color:#a7f3d0;">"Thu"</span>, <span style="color:#a7f3d0;">"Fri"</span>, <span style="color:#a7f3d0;">"Sat"</span>, <span style="color:#a7f3d0;">"Sun"</span>]
hours = [<span style="color:#93c5fd;">str</span>(h) + <span style="color:#a7f3d0;">"h"</span> <span style="color:#c4b5fd;">for</span> h <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">23</span>)]

<span style="color:#6b7280;"># Simulate hourly page views — higher on weekdays 9am–5pm</span>
traffic = np.random.<span style="color:#93c5fd;">randint</span>(<span style="color:#fcd34d;">100</span>, <span style="color:#fcd34d;">500</span>, size=(<span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">17</span>))
traffic[<span style="color:#fcd34d;">0</span>:<span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">3</span>:<span style="color:#fcd34d;">11</span>] += <span style="color:#fcd34d;">800</span>   <span style="color:#6b7280;"># Boost weekday business hours</span>

df_traffic = pd.<span style="color:#93c5fd;">DataFrame</span>(traffic, index=days, columns=hours)

fig, ax = plt.<span style="color:#93c5fd;">subplots</span>(figsize=(<span style="color:#fcd34d;">14</span>, <span style="color:#fcd34d;">5</span>))

sns.<span style="color:#93c5fd;">heatmap</span>(df_traffic,
            cmap=<span style="color:#a7f3d0;">"YlOrRd"</span>,      <span style="color:#6b7280;"># Yellow → Orange → Red gradient</span>
            annot=<span style="color:#fca5a5;">False</span>,        <span style="color:#6b7280;"># Too many cells to annotate</span>
            linewidths=<span style="color:#fcd34d;">0.3</span>,
            linecolor=<span style="color:#a7f3d0;">"white"</span>,
            cbar_kws={<span style="color:#a7f3d0;">"label"</span>: <span style="color:#a7f3d0;">"Page Views"</span>},
            ax=ax)

ax.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Website Traffic by Day & Hour"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Hour of Day"</span>)
ax.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Day of Week"</span>)

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>A 7×17 grid heatmap. Weekday rows (Mon–Fri) glow bright red/orange
during 9am–6pm. Weekend rows and early morning are pale yellow.
A color bar on the right shows 100–1300+ page view scale.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '15.5 Heatmaps & Correlation Matrices',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L15_5', [
                ['q' => 'What does calling df.corr() return?', 'opts' => ['A single correlation value', 'A square matrix of pairwise Pearson correlation coefficients between all numeric columns', 'A sorted list of the most correlated columns', 'A scatter plot matrix'], 'ans' => 1, 'exp' => 'df.corr() computes the pairwise Pearson correlation between every pair of numeric columns, returning a square DataFrame where cell [i][j] is the correlation between column i and column j.'],
                ['q' => 'Why is the upper triangle often masked in a correlation heatmap?', 'opts' => ['It contains invalid values', 'The upper triangle is always zero', 'It is a mirror image of the lower triangle — showing both is redundant', 'Seaborn cannot render the upper triangle'], 'ans' => 2, 'exp' => 'A correlation matrix is symmetric: corr(A, B) == corr(B, A). Masking the upper triangle removes the redundant mirror half, making the chart cleaner and easier to read.'],
                ['q' => 'In sns.heatmap(), what does the "center=0" parameter do?', 'opts' => ['Moves the chart to the center of the figure', 'Centers the color scale so that 0 maps to the neutral midpoint color', 'Removes all cells with value 0', 'Sets the minimum value to 0'], 'ans' => 1, 'exp' => 'center=0 tells the colormap that 0 should map to the neutral mid-color (white in "coolwarm"). This ensures positive values appear warm (red) and negative values appear cool (blue), providing immediate visual polarity.'],
                ['q' => 'What must you do to your DataFrame before passing it to sns.heatmap() for a frequency analysis?', 'opts' => ['Sort it alphabetically', 'Reshape it into a 2D pivot table using pd.pivot_table() or df.pivot()', 'Convert all strings to integers first', 'Drop all NaN values'], 'ans' => 1, 'exp' => 'sns.heatmap() expects a 2D matrix (rows × columns). For frequency or aggregation data, you first reshape your long-format DataFrame into a pivot table using pd.pivot_table(values, index, columns, aggfunc).'],
                ['q' => 'Which cmap is most appropriate for diverging data that ranges from negative to positive?', 'opts' => ['viridis', 'YlOrRd', 'coolwarm', 'Blues'], 'ans' => 2, 'exp' => '"coolwarm" is a diverging colormap — cool blues for negative values, warm reds for positive, white at zero. It is ideal for correlation matrices. "YlOrRd" and "Blues" are sequential and do not convey the positive/negative polarity.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 15.6 — Seaborn Statistical Plots: Pair Plots & FacetGrids
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Seaborn Statistical Plots: Pair Plots & FacetGrids</h2>
<p>When you move beyond two variables and need to explore the relationships between <em>all</em> numeric features simultaneously, Seaborn's higher-level functions become indispensable. <strong>Pair plots</strong> create a grid of scatter plots for every combination of numeric columns in one function call. <strong>FacetGrids</strong> let you create the same chart type repeated across subgroups of a categorical variable — a small-multiples technique that is one of the most powerful tools in exploratory analysis.</p>

<h3>Pair Plots: The EDA Swiss Army Knife</h3>
<p><code>sns.pairplot()</code> creates an n×n grid where n is your number of numeric columns. Off-diagonal cells are scatter plots showing the relationship between two variables. Diagonal cells show the distribution of that variable (histogram or KDE). Adding <code>hue</code> colors points by a category, making cluster separation immediately visible.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Pair Plot with Hue</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt

penguins = sns.<span style="color:#93c5fd;">load_dataset</span>(<span style="color:#a7f3d0;">"penguins"</span>).<span style="color:#93c5fd;">dropna</span>()

<span style="color:#6b7280;"># One function call — produces a complete grid of plots</span>
g = sns.<span style="color:#93c5fd;">pairplot</span>(
    penguins,
    hue=<span style="color:#a7f3d0;">"species"</span>,         <span style="color:#6b7280;"># Color by penguin species</span>
    diag_kind=<span style="color:#a7f3d0;">"kde"</span>,      <span style="color:#6b7280;"># Diagonal: KDE instead of histogram</span>
    plot_kws={<span style="color:#a7f3d0;">"alpha"</span>: <span style="color:#fcd34d;">0.5</span>},
    palette=<span style="color:#a7f3d0;">"Set2"</span>
)

g.<span style="color:#93c5fd;">fig</span>.<span style="color:#93c5fd;">suptitle</span>(<span style="color:#a7f3d0;">"Penguin Dataset — All Pairwise Relationships by Species"</span>,
              y=<span style="color:#fcd34d;">1.02</span>, fontsize=<span style="color:#fcd34d;">14</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>A 4×4 grid. Diagonal cells: overlapping KDE curves per species.
Off-diagonal: scatter plots colored by Adelie, Chinstrap, Gentoo.
Gentoo penguins are clearly larger — fully separated in flipper/body mass panels.</div>
  </div>
</div>

<h3>FacetGrid: Small Multiples Across Categories</h3>
<p>A <strong>FacetGrid</strong> creates a grid of subplots, one per level of a categorical variable, and maps the same plotting function to each. This "small multiples" technique allows direct visual comparison across subgroups without overplotting everything on a single chart.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — FacetGrid: Distribution per Subgroup</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt

tips = sns.<span style="color:#93c5fd;">load_dataset</span>(<span style="color:#a7f3d0;">"tips"</span>)

<span style="color:#6b7280;"># Create a grid with one column per 'time' category (Lunch / Dinner)</span>
g = sns.<span style="color:#93c5fd;">FacetGrid</span>(tips, col=<span style="color:#a7f3d0;">"time"</span>, row=<span style="color:#a7f3d0;">"sex"</span>,
                    height=<span style="color:#fcd34d;">3.5</span>, aspect=<span style="color:#fcd34d;">1.2</span>, palette=<span style="color:#a7f3d0;">"Set2"</span>)

<span style="color:#6b7280;"># Map a histogram of tip amount to every subplot in the grid</span>
g.<span style="color:#93c5fd;">map</span>(sns.histplot, <span style="color:#a7f3d0;">"tip"</span>, bins=<span style="color:#fcd34d;">12</span>, kde=<span style="color:#fca5a5;">True</span>, color=<span style="color:#a7f3d0;">"steelblue"</span>)

g.<span style="color:#93c5fd;">set_axis_labels</span>(<span style="color:#a7f3d0;">"Tip Amount ($)"</span>, <span style="color:#a7f3d0;">"Count"</span>)
g.<span style="color:#93c5fd;">set_titles</span>(<span style="color:#a7f3d0;">"{row_name} | {col_name}"</span>)
g.<span style="color:#93c5fd;">fig</span>.<span style="color:#93c5fd;">suptitle</span>(<span style="color:#a7f3d0;">"Tip Distribution by Sex and Meal Time"</span>,
              fontsize=<span style="color:#fcd34d;">14</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>, y=<span style="color:#fcd34d;">1.04</span>)
g.<span style="color:#93c5fd;">add_legend</span>()

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>A 2×2 grid: Male/Female rows × Lunch/Dinner columns.
Each subplot shows a histogram + KDE of tip amounts for that subgroup.
Direct comparison reveals dinner tips are slightly higher and more variable.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '15.6 Pair Plots & FacetGrids',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L15_6', [
                ['q' => 'What do the diagonal cells of a sns.pairplot() show by default?', 'opts' => ['Scatter plots of a variable against itself', 'The histogram or KDE distribution of that single variable', 'The correlation coefficient', 'A blank cell'], 'ans' => 1, 'exp' => 'Diagonal cells in a pair plot show the univariate distribution of each variable — either a histogram or KDE. Off-diagonal cells show bivariate scatter plots between pairs of variables.'],
                ['q' => 'What does adding hue="species" to sns.pairplot() do?', 'opts' => ['Filters the data to only show one species', 'Colors all points by species, making cluster separation visible', 'Draws one pair plot per species', 'Changes the background color'], 'ans' => 1, 'exp' => 'hue assigns a distinct color to each category level, coloring every scatter and KDE in the grid. This lets you immediately see how the classes cluster and separate across feature dimensions.'],
                ['q' => 'In a FacetGrid, what does the g.map() method do?', 'opts' => ['Applies a geographic map projection', 'Applies a given plotting function to every subplot in the grid', 'Renames axis labels across the grid', 'Creates a color map legend'], 'ans' => 1, 'exp' => 'g.map(plot_func, variable) applies the plotting function to the specified column in every subplot panel of the FacetGrid. For example, g.map(sns.histplot, "tip") draws a histogram of tips in each facet.'],
                ['q' => 'What is the "small multiples" technique?', 'opts' => ['Reducing chart size to fit more on screen', 'Displaying the same chart type for each subgroup side by side to enable direct comparison', 'Combining multiple variables into a single axis', 'Using small font sizes to pack more labels'], 'ans' => 1, 'exp' => 'Small multiples (also called trellis charts or faceted plots) repeat the same visualization across subgroups, arranged in a grid. This enables viewers to compare patterns across groups without overplotting everything on one chart.'],
                ['q' => 'Why might you prefer a FacetGrid over plotting all groups on one chart?', 'opts' => ['FacetGrids load faster', 'Overlapping all groups on one chart causes visual clutter and overplotting — FacetGrids separate them for cleaner comparison', 'FacetGrids automatically compute statistics', 'Matplotlib cannot handle multiple groups'], 'ans' => 1, 'exp' => 'When multiple groups are plotted on a single axes with many overlapping points or distributions, the chart becomes unreadable. FacetGrids separate each group into its own panel, maintaining the same scales for direct visual comparison.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 15.7 — Pie Charts, Donut Charts & Part-to-Whole Visualization
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Pie Charts, Donut Charts & Part-to-Whole Visualization</h2>
<p>Part-to-whole visualizations show how individual components add up to a total. The most familiar is the pie chart — but it is also one of the most misused. Humans are notoriously bad at comparing angles and arc lengths. Yet for a small number of categories where one dominant segment is the story, a well-designed pie or donut chart communicates instantly. This lesson covers when to use them, when to avoid them, and how to build them properly.</p>

<h3>When to Use (and Avoid) Pie Charts</h3>
<p><strong>Use a pie chart when:</strong> you have 2–5 categories, they sum to a meaningful whole (like 100% market share), and the story is about one segment dominating. <strong>Avoid a pie chart when:</strong> you have 6+ categories (use a bar chart instead), the differences between slices are small (angles are hard to compare), or you need to show change over time.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Pie Chart & Donut Chart</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt

labels  = [<span style="color:#a7f3d0;">"Direct"</span>, <span style="color:#a7f3d0;">"Organic Search"</span>, <span style="color:#a7f3d0;">"Social Media"</span>, <span style="color:#a7f3d0;">"Email"</span>, <span style="color:#a7f3d0;">"Referral"</span>]
sizes   = [<span style="color:#fcd34d;">35</span>, <span style="color:#fcd34d;">28</span>, <span style="color:#fcd34d;">18</span>, <span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">7</span>]
colors  = [<span style="color:#a7f3d0;">"#3498db"</span>, <span style="color:#a7f3d0;">"#2ecc71"</span>, <span style="color:#a7f3d0;">"#e74c3c"</span>, <span style="color:#a7f3d0;">"#f39c12"</span>, <span style="color:#a7f3d0;">"#9b59b6"</span>]
explode = (<span style="color:#fcd34d;">0.05</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>)   <span style="color:#6b7280;"># Slightly pull out the largest slice</span>

fig, (ax1, ax2) = plt.<span style="color:#93c5fd;">subplots</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">13</span>, <span style="color:#fcd34d;">5</span>))

<span style="color:#6b7280;"># --- Standard Pie Chart ---</span>
ax1.<span style="color:#93c5fd;">pie</span>(sizes, labels=labels, colors=colors,
        explode=explode, autopct=<span style="color:#a7f3d0;">"%1.1f%%"</span>,
        startangle=<span style="color:#fcd34d;">140</span>, pctdistance=<span style="color:#fcd34d;">0.75</span>)
ax1.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Website Traffic Sources — Pie Chart"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)

<span style="color:#6b7280;"># --- Donut Chart: a pie with a circle cut from the center ---</span>
wedges, texts, autotexts = ax2.<span style="color:#93c5fd;">pie</span>(
    sizes, labels=labels, colors=colors,
    autopct=<span style="color:#a7f3d0;">"%1.1f%%"</span>, startangle=<span style="color:#fcd34d;">140</span>, pctdistance=<span style="color:#fcd34d;">0.80</span>
)
<span style="color:#6b7280;"># Create the "hole" in the center with a white circle</span>
centre_circle = plt.<span style="color:#93c5fd;">Circle</span>((<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>), <span style="color:#fcd34d;">0.55</span>, fc=<span style="color:#a7f3d0;">"white"</span>)
ax2.<span style="color:#93c5fd;">add_artist</span>(centre_circle)
<span style="color:#6b7280;"># Add a central label inside the donut hole</span>
ax2.<span style="color:#93c5fd;">text</span>(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#a7f3d0;">"Traffic\nSources"</span>, ha=<span style="color:#a7f3d0;">"center"</span>, va=<span style="color:#a7f3d0;">"center"</span>,
         fontsize=<span style="color:#fcd34d;">11</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>, color=<span style="color:#a7f3d0;">"#333"</span>)
ax2.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Same Data — Donut Chart"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Left: Pie chart with the "Direct" slice slightly exploded outward.
Right: Donut with a white hole center labeled "Traffic Sources".
Percentage labels are visible on each slice. Direct (35%) dominates both.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '15.7 Pie, Donut & Part-to-Whole Charts',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L15_7', [
                ['q' => 'What is the main weakness of pie charts compared to bar charts?', 'opts' => ['Pie charts cannot show percentages', 'Humans are worse at comparing angles and arc lengths than bar lengths — making small differences hard to spot', 'Pie charts take more code to create', 'Bar charts can show more colors'], 'ans' => 1, 'exp' => 'Research on perceptual accuracy shows humans are much better at judging length (bar charts) than angle or area (pie charts). For close values like 28% vs 32%, a pie chart makes them nearly indistinguishable; a bar chart shows the difference clearly.'],
                ['q' => 'How is a donut chart created in Matplotlib?', 'opts' => ['Using the special donut() function', 'Drawing a pie chart and overlaying a white filled circle in the center using ax.add_artist()', 'Setting a "hole" parameter in ax.pie()', 'Using sns.donutplot()'], 'ans' => 1, 'exp' => 'Matplotlib has no built-in donut function. The standard technique is to draw a regular pie chart, then overlay a white plt.Circle((0,0), radius) on top using ax.add_artist(), which creates the visual hole in the center.'],
                ['q' => 'What does the autopct="%1.1f%%" parameter do?', 'opts' => ['Sets the font size of labels', 'Displays percentage values formatted to 1 decimal place inside each slice', 'Rotates the chart by 1.1 degrees', 'Filters slices below 1%'], 'ans' => 1, 'exp' => 'autopct applies a format string to each slice\'s percentage and prints it inside the slice. "%1.1f%%" means: at least 1 digit, 1 decimal place, followed by the % sign. So 35.0%, 28.0%, etc.'],
                ['q' => 'When should you replace a pie chart with a bar chart?', 'opts' => ['When all slices are equal', 'When you have 6 or more categories, or when differences between slices are small', 'When the data sums to 100', 'When using Seaborn instead of Matplotlib'], 'ans' => 1, 'exp' => 'With 6+ slices, a pie becomes cluttered and angles become impossible to distinguish. A bar chart with sorted bars communicates the same information far more clearly and allows immediate comparison of even similar values.'],
                ['q' => 'What does the "explode" parameter do in ax.pie()?', 'opts' => ['Deletes a slice from the chart', 'Offsets one or more slices outward from the center to draw attention to them', 'Adds an explosion animation', 'Separates all slices equally'], 'ans' => 1, 'exp' => 'explode is a tuple of offset fractions, one per slice. A value of 0.05 pulls that slice 5% of the radius away from the center, visually emphasizing it — commonly used for the most important or largest slice.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 15.8 — Subplots, Layouts & Figure Customization
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Subplots, Layouts & Figure Customization</h2>
<p>Professional data scientists rarely produce a single chart in isolation. A complete analysis tells a visual story through a <strong>dashboard</strong> — a multi-panel figure where every chart complements the others. Matplotlib's subplot system gives you the tools to arrange multiple plots with full control over their positions, sizes, and shared axes. This lesson teaches you to compose multi-panel figures like a professional.</p>

<h3>plt.subplots() Grid Layouts</h3>
<p><code>plt.subplots(nrows, ncols)</code> creates a grid of Axes objects. For a 2×2 grid it returns a 2D array of axes you index with <code>axes[row][col]</code>. Setting <code>sharex=True</code> or <code>sharey=True</code> links axes so zooming one panel zooms all — invaluable for time series dashboards.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — 2×2 Dashboard Layout</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

tips = sns.<span style="color:#93c5fd;">load_dataset</span>(<span style="color:#a7f3d0;">"tips"</span>)
np.random.<span style="color:#93c5fd;">seed</span>(<span style="color:#fcd34d;">42</span>)
data = np.random.<span style="color:#93c5fd;">normal</span>(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">300</span>)

<span style="color:#6b7280;"># Create a 2×2 grid — axes is a 2D NumPy array of Axes objects</span>
fig, axes = plt.<span style="color:#93c5fd;">subplots</span>(<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">9</span>))

<span style="color:#6b7280;"># Top-left: Histogram</span>
axes[<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>].<span style="color:#93c5fd;">hist</span>(data, bins=<span style="color:#fcd34d;">25</span>, color=<span style="color:#a7f3d0;">"steelblue"</span>, edgecolor=<span style="color:#a7f3d0;">"white"</span>)
axes[<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Normal Distribution"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)

<span style="color:#6b7280;"># Top-right: Scatter</span>
sns.<span style="color:#93c5fd;">scatterplot</span>(data=tips, x=<span style="color:#a7f3d0;">"total_bill"</span>, y=<span style="color:#a7f3d0;">"tip"</span>,
               hue=<span style="color:#a7f3d0;">"sex"</span>, alpha=<span style="color:#fcd34d;">0.6</span>, ax=axes[<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>])
axes[<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Bill vs Tip by Sex"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)

<span style="color:#6b7280;"># Bottom-left: Box Plot</span>
sns.<span style="color:#93c5fd;">boxplot</span>(data=tips, x=<span style="color:#a7f3d0;">"day"</span>, y=<span style="color:#a7f3d0;">"total_bill"</span>,
            palette=<span style="color:#a7f3d0;">"Set3"</span>, ax=axes[<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>])
axes[<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Bill by Day of Week"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)

<span style="color:#6b7280;"># Bottom-right: Bar Chart</span>
day_avg = tips.<span style="color:#93c5fd;">groupby</span>(<span style="color:#a7f3d0;">"day"</span>)[<span style="color:#a7f3d0;">"tip"</span>].<span style="color:#93c5fd;">mean</span>()
axes[<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>].<span style="color:#93c5fd;">bar</span>(day_avg.index, day_avg.values, color=<span style="color:#a7f3d0;">"#e74c3c"</span>)
axes[<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Average Tip by Day"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>].<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Avg Tip ($)"</span>)

<span style="color:#6b7280;"># Add a main title for the entire figure</span>
fig.<span style="color:#93c5fd;">suptitle</span>(<span style="color:#a7f3d0;">"Restaurant Tips — Exploratory Dashboard"</span>,
             fontsize=<span style="color:#fcd34d;">16</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>, y=<span style="color:#fcd34d;">1.01</span>)

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>A 2×2 professional dashboard.
Top-left: Bell curve histogram.
Top-right: Bill vs Tip scatter colored by sex.
Bottom-left: Box plots for each day showing spread.
Bottom-right: Bar chart of average tip by day of week.</div>
  </div>
</div>

<h3>Saving Figures for Reports & Presentations</h3>
<p>Use <code>plt.savefig()</code> to export charts to PNG, PDF, SVG, or other formats. The <code>dpi</code> parameter controls resolution — 150 dpi for screen, 300 dpi for print. Always save <em>before</em> <code>plt.show()</code>, as <code>show()</code> clears the figure buffer.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Saving Charts</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># After building your chart...</span>
plt.<span style="color:#93c5fd;">tight_layout</span>()

<span style="color:#6b7280;"># Save BEFORE show() — show() clears the figure buffer</span>
plt.<span style="color:#93c5fd;">savefig</span>(<span style="color:#a7f3d0;">"dashboard.png"</span>,
            dpi=<span style="color:#fcd34d;">300</span>,            <span style="color:#6b7280;"># 300 DPI for print quality</span>
            bbox_inches=<span style="color:#a7f3d0;">"tight"</span>,  <span style="color:#6b7280;"># Prevent labels from being cut off</span>
            facecolor=<span style="color:#a7f3d0;">"white"</span>)    <span style="color:#6b7280;"># Force white background (avoids transparency issues)</span>

<span style="color:#6b7280;"># Save as vector PDF for presentations (infinitely scalable)</span>
plt.<span style="color:#93c5fd;">savefig</span>(<span style="color:#a7f3d0;">"dashboard.pdf"</span>, bbox_inches=<span style="color:#a7f3d0;">"tight"</span>)

plt.<span style="color:#93c5fd;">show</span>()   <span style="color:#6b7280;"># Show AFTER saving</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Saved: dashboard.png and dashboard.pdf"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Saved: dashboard.png and dashboard.pdf</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '15.8 Subplots, Layouts & Customization',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L15_8', [
                ['q' => 'When plt.subplots(2, 2) is called, what does the returned axes variable contain?', 'opts' => ['A single Axes object', 'A list of 4 Axes objects', 'A 2D NumPy array of Axes objects indexed as axes[row, col]', 'A dictionary of labeled Axes'], 'ans' => 2, 'exp' => 'plt.subplots(2, 2) returns a 2D array of shape (2, 2). You access individual subplots with axes[0, 0] (top-left), axes[0, 1] (top-right), axes[1, 0] (bottom-left), axes[1, 1] (bottom-right).'],
                ['q' => 'What is fig.suptitle() used for?', 'opts' => ['Setting the title of one specific subplot', 'Adding a super-title to the entire figure above all subplots', 'Setting the window title of the OS', 'Adding a subtitle below the x-axis'], 'ans' => 1, 'exp' => 'fig.suptitle() adds a single title spanning the entire figure — useful for multi-panel dashboards where each panel has its own ax.set_title(), and you want an overarching title for the whole figure.'],
                ['q' => 'Why must plt.savefig() be called BEFORE plt.show()?', 'opts' => ['plt.show() is slower to execute', 'plt.show() renders the figure to screen and clears the buffer — saving after show() produces a blank file', 'plt.savefig() needs the screen to render first', 'This is a Python syntax requirement'], 'ans' => 1, 'exp' => 'plt.show() finalizes and displays the figure, then clears the figure from memory. If you call savefig() after show(), you save a blank canvas. Always save, then show.'],
                ['q' => 'What does bbox_inches="tight" do in plt.savefig()?', 'opts' => ['Crops the image to a tight square', 'Automatically expands the bounding box to include all labels and titles, preventing cut-off', 'Compresses the file size', 'Sets fixed pixel dimensions'], 'ans' => 1, 'exp' => 'Without bbox_inches="tight", axis labels, titles, and legends near the edge of the figure are often cropped in the saved file. "tight" automatically adjusts the bounding box to include all elements.'],
                ['q' => 'Which dpi value is recommended for print-quality figure exports?', 'opts' => ['72 dpi', '96 dpi', '150 dpi', '300 dpi'], 'ans' => 3, 'exp' => '300 dpi (dots per inch) is the standard for print-quality images — suitable for reports, papers, and presentations printed at full size. 72-96 dpi is adequate for screen-only viewing; 150 dpi is a good middle ground.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 15.9 — Interactive Charts with Plotly
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Interactive Charts with Plotly</h2>
<p>Matplotlib and Seaborn produce <em>static</em> images — perfect for papers, PDFs, and printed reports. But when your audience is viewing charts in a web browser or a live dashboard, <strong>interactivity</strong> unlocks an entirely new level of insight. With Plotly, every chart becomes explorable: hover to see exact values, click to filter, zoom in on a region, pan across a time series, and toggle categories on and off. Plotly Express is the high-level API that creates these charts in a single function call.</p>

<h3>Plotly Express: Interactive in One Line</h3>
<p>Plotly Express (<code>px</code>) mirrors Seaborn's philosophy — pass a DataFrame and column names, get a beautiful interactive chart. The output is an HTML object that renders in Jupyter notebooks and can be saved as a standalone HTML file for sharing.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Plotly Express Interactive Charts</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> plotly.express <span style="color:#c4b5fd;">as</span> px
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd

<span style="color:#6b7280;"># Load Plotly's built-in gapminder dataset</span>
<span style="color:#6b7280;"># Contains GDP, life expectancy, population by country/year</span>
df = px.<span style="color:#93c5fd;">data</span>.<span style="color:#93c5fd;">gapminder</span>()

<span style="color:#6b7280;"># --- Interactive Scatter Plot ---</span>
<span style="color:#6b7280;"># One line. Hover to see country name, GDP, life expectancy.</span>
fig1 = px.<span style="color:#93c5fd;">scatter</span>(
    df[df[<span style="color:#a7f3d0;">"year"</span>] == <span style="color:#fcd34d;">2007</span>],     <span style="color:#6b7280;"># Filter to year 2007</span>
    x=<span style="color:#a7f3d0;">"gdpPercap"</span>,
    y=<span style="color:#a7f3d0;">"lifeExp"</span>,
    size=<span style="color:#a7f3d0;">"pop"</span>,               <span style="color:#6b7280;"># Bubble size = population</span>
    color=<span style="color:#a7f3d0;">"continent"</span>,         <span style="color:#6b7280;"># Color by continent</span>
    hover_name=<span style="color:#a7f3d0;">"country"</span>,      <span style="color:#6b7280;"># Show country on hover</span>
    log_x=<span style="color:#fca5a5;">True</span>,               <span style="color:#6b7280;"># Log scale for GDP (right-skewed)</span>
    size_max=<span style="color:#fcd34d;">60</span>,
    title=<span style="color:#a7f3d0;">"Life Expectancy vs GDP Per Capita (2007)"</span>
)
fig1.<span style="color:#93c5fd;">show</span>()

<span style="color:#6b7280;"># --- Animated Scatter: Watch the world change from 1952 to 2007 ---</span>
fig2 = px.<span style="color:#93c5fd;">scatter</span>(
    df,
    x=<span style="color:#a7f3d0;">"gdpPercap"</span>, y=<span style="color:#a7f3d0;">"lifeExp"</span>,
    size=<span style="color:#a7f3d0;">"pop"</span>, color=<span style="color:#a7f3d0;">"continent"</span>,
    hover_name=<span style="color:#a7f3d0;">"country"</span>, log_x=<span style="color:#fca5a5;">True</span>,
    animation_frame=<span style="color:#a7f3d0;">"year"</span>,       <span style="color:#6b7280;"># Play button animates through years</span>
    animation_group=<span style="color:#a7f3d0;">"country"</span>,    <span style="color:#6b7280;"># Keep each country's bubble consistent</span>
    size_max=<span style="color:#fcd34d;">55</span>,
    range_x=[<span style="color:#fcd34d;">200</span>, <span style="color:#fcd34d;">100000</span>],
    range_y=[<span style="color:#fcd34d;">25</span>, <span style="color:#fcd34d;">90</span>],
    title=<span style="color:#a7f3d0;">"Animated: Global Development 1952–2007"</span>
)
fig2.<span style="color:#93c5fd;">show</span>()

<span style="color:#6b7280;"># Save as standalone HTML — shareable without Python</span>
fig1.<span style="color:#93c5fd;">write_html</span>(<span style="color:#a7f3d0;">"gapminder_2007.html"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>fig1: Interactive bubble scatter — hover shows country/GDP/life expectancy.
  Click continent in legend to hide/show. Zoom in on Europe cluster.
fig2: Same chart with Play button — watch bubbles move right+up over 55 years.
  China and India bubbles grow enormously in size (population growth).
  File saved: gapminder_2007.html</div>
  </div>
</div>

<h3>Interactive Bar & Line Charts with Plotly</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Plotly Interactive Bar & Line</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> plotly.express <span style="color:#c4b5fd;">as</span> px
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd

months  = [<span style="color:#a7f3d0;">"Jan"</span>,<span style="color:#a7f3d0;">"Feb"</span>,<span style="color:#a7f3d0;">"Mar"</span>,<span style="color:#a7f3d0;">"Apr"</span>,<span style="color:#a7f3d0;">"May"</span>,<span style="color:#a7f3d0;">"Jun"</span>]
sales_a = [<span style="color:#fcd34d;">120</span>, <span style="color:#fcd34d;">145</span>, <span style="color:#fcd34d;">132</span>, <span style="color:#fcd34d;">167</span>, <span style="color:#fcd34d;">189</span>, <span style="color:#fcd34d;">210</span>]
sales_b = [<span style="color:#fcd34d;">95</span>,  <span style="color:#fcd34d;">110</span>, <span style="color:#fcd34d;">155</span>, <span style="color:#fcd34d;">143</span>, <span style="color:#fcd34d;">160</span>, <span style="color:#fcd34d;">178</span>]

df = pd.<span style="color:#93c5fd;">DataFrame</span>({
    <span style="color:#a7f3d0;">"Month"</span>:   months * <span style="color:#fcd34d;">2</span>,
    <span style="color:#a7f3d0;">"Sales"</span>:   sales_a + sales_b,
    <span style="color:#a7f3d0;">"Product"</span>: [<span style="color:#a7f3d0;">"Product A"</span>]*<span style="color:#fcd34d;">6</span> + [<span style="color:#a7f3d0;">"Product B"</span>]*<span style="color:#fcd34d;">6</span>
})

<span style="color:#6b7280;"># Interactive grouped bar — hover shows exact values</span>
fig = px.<span style="color:#93c5fd;">bar</span>(df, x=<span style="color:#a7f3d0;">"Month"</span>, y=<span style="color:#a7f3d0;">"Sales"</span>, color=<span style="color:#a7f3d0;">"Product"</span>,
            barmode=<span style="color:#a7f3d0;">"group"</span>,
            title=<span style="color:#a7f3d0;">"Monthly Sales by Product"</span>,
            color_discrete_map={<span style="color:#a7f3d0;">"Product A"</span>: <span style="color:#a7f3d0;">"#3498db"</span>,
                                <span style="color:#a7f3d0;">"Product B"</span>: <span style="color:#a7f3d0;">"#e74c3c"</span>})
fig.<span style="color:#93c5fd;">update_layout</span>(yaxis_title=<span style="color:#a7f3d0;">"Sales ($K)"</span>, hovermode=<span style="color:#a7f3d0;">"x unified"</span>)
fig.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Interactive grouped bar chart.
Hovering over any month shows a unified tooltip with both product values.
Clicking "Product A" in the legend hides/shows that series.
Both products trend upward — Product A leads in Jan-Feb, B gains in Mar.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '15.9 Interactive Charts with Plotly',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L15_9', [
                ['q' => 'What is the key difference between Plotly and Matplotlib charts?', 'opts' => ['Plotly is faster to compute', 'Plotly charts are static images; Matplotlib charts are interactive', 'Matplotlib charts are static images; Plotly charts are interactive HTML with zoom, hover, and filter', 'Plotly only works in Jupyter notebooks'], 'ans' => 2, 'exp' => 'Matplotlib produces static raster/vector images (PNG, PDF). Plotly produces interactive HTML — viewers can hover for tooltips, zoom into regions, click legend items to toggle series, and pan across the chart.'],
                ['q' => 'What does the animation_frame parameter do in px.scatter()?', 'opts' => ['Sets the frame rate of the chart', 'Adds a Play button that animates the chart through values of that column, like a slideshow', 'Enables 60fps rendering', 'Applies a transition animation when the chart loads'], 'ans' => 1, 'exp' => 'animation_frame="year" creates a slider and Play button that steps through each unique year value, updating the chart positions accordingly — making it easy to visualize change over time.'],
                ['q' => 'How do you save a Plotly chart as a shareable file that works in any web browser?', 'opts' => ['fig.save("chart.png")', 'plt.savefig("chart.html")', 'fig.write_html("chart.html")', 'fig.export("chart.pdf")'], 'ans' => 2, 'exp' => 'fig.write_html() saves the chart as a self-contained HTML file with all JavaScript embedded. Anyone with a modern browser can open it and interact with it without needing Python or Plotly installed.'],
                ['q' => 'What does hovermode="x unified" do in fig.update_layout()?', 'opts' => ['Disables hover tooltips', 'Shows a single unified tooltip for all traces at the same x value when hovering', 'Changes the mouse cursor to a crosshair', 'Locks the x-axis from zooming'], 'ans' => 1, 'exp' => '"x unified" mode shows a single tooltip box that includes values from all series at the same x position simultaneously — useful for multi-series comparisons where you want to see all values at once when hovering.'],
                ['q' => 'Which Plotly Express parameter controls the size of bubbles in a bubble chart?', 'opts' => ['radius=', 'scale=', 'size=', 'weight='], 'ans' => 2, 'exp' => 'size= maps a DataFrame column to bubble area. size_max= sets the maximum bubble diameter in pixels. Larger values in the column produce larger bubbles — ideal for encoding a third numeric variable like population.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 15.10 — Best Practices, Color Theory & Storytelling
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Best Practices, Color Theory & Visual Storytelling</h2>
<p>Creating technically correct charts is only half the job. The other half is making them <em>communicate clearly and honestly</em>. This lesson covers the principles that separate a chart that confuses from one that compels — including color theory, avoiding deceptive visualizations, annotation strategies, and how to structure a visual narrative that drives decisions.</p>

<h3>The Five Cardinal Rules of Data Visualization</h3>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:24px;margin-bottom:24px;">
  <ol style="color:var(--muted);line-height:2;font-size:0.9rem;margin:0;padding-left:20px;">
    <li><strong style="color:var(--text);">Always label your axes</strong> — A chart without axis labels forces the viewer to guess what they are looking at. Every axis needs a label and units.</li>
    <li><strong style="color:var(--text);">Start bar charts at zero</strong> — Truncating the y-axis of a bar chart exaggerates differences. A bar starting at 95 and reaching 100 looks like a 100% change. Starting at zero makes it 5%.</li>
    <li><strong style="color:var(--text);">Choose colors intentionally</strong> — Use qualitative palettes for categories, sequential palettes for ordered data, and diverging palettes for data with a meaningful midpoint (like correlation).</li>
    <li><strong style="color:var(--text);">Maximize the data-ink ratio</strong> — Edward Tufte's principle: every pixel of ink should encode data. Remove gridlines, borders, and decorations that do not carry information.</li>
    <li><strong style="color:var(--text);">Design for color blindness</strong> — ~8% of men have red-green color blindness. Use the "colorblind" or "viridis" palette in Seaborn, or verify your palette with a color blindness simulator.</li>
  </ol>
</div>

<h3>Color Palette Selection</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Choosing the Right Palette</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt

fig, axes = plt.<span style="color:#93c5fd;">subplots</span>(<span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">1</span>, figsize=(<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">6</span>))

<span style="color:#6b7280;"># 1. Qualitative: distinct colors for unordered categories</span>
<span style="color:#6b7280;">#    Use when: department names, product types, continents</span>
sns.<span style="color:#93c5fd;">palplot</span>(sns.<span style="color:#93c5fd;">color_palette</span>(<span style="color:#a7f3d0;">"Set2"</span>, <span style="color:#fcd34d;">8</span>))
axes[<span style="color:#fcd34d;">0</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Qualitative: 'Set2' — for unordered categories"</span>)

<span style="color:#6b7280;"># 2. Sequential: light→dark gradient for ordered numeric values</span>
<span style="color:#6b7280;">#    Use when: sales volume, age, temperature, risk scores</span>
sns.<span style="color:#93c5fd;">palplot</span>(sns.<span style="color:#93c5fd;">color_palette</span>(<span style="color:#a7f3d0;">"Blues"</span>, <span style="color:#fcd34d;">8</span>))
axes[<span style="color:#fcd34d;">1</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Sequential: 'Blues' — for ordered numeric data"</span>)

<span style="color:#6b7280;"># 3. Diverging: two-color gradient with neutral midpoint</span>
<span style="color:#6b7280;">#    Use when: correlation (-1 to +1), sentiment (-/+), temperature (below/above avg)</span>
sns.<span style="color:#93c5fd;">palplot</span>(sns.<span style="color:#93c5fd;">color_palette</span>(<span style="color:#a7f3d0;">"coolwarm"</span>, <span style="color:#fcd34d;">8</span>))
axes[<span style="color:#fcd34d;">2</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Diverging: 'coolwarm' — for data with a neutral midpoint"</span>)

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()

<span style="color:#6b7280;"># Colorblind-safe alternative to red/green</span>
safe_palette = sns.<span style="color:#93c5fd;">color_palette</span>(<span style="color:#a7f3d0;">"colorblind"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Colorblind palette hex codes:"</span>, [<span style="color:#a7f3d0;">f"#{int(r*255):02x}{int(g*255):02x}{int(b*255):02x}"</span>
                                         <span style="color:#c4b5fd;">for</span> r,g,b <span style="color:#c4b5fd;">in</span> safe_palette])</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Three palette strips rendered side by side.
Row 1 (Set2): 8 distinct, well-separated pastel colors.
Row 2 (Blues): Gradient from light sky blue to deep navy.
Row 3 (coolwarm): Blue → White → Red diverging gradient.
Console: Colorblind palette hex codes: ['#0072b2', '#e69f00', ...]</div>
  </div>
</div>

<h3>Annotations: Directing the Viewer's Attention</h3>
<p>An unlabeled chart makes the viewer find the story themselves. An <strong>annotated</strong> chart directs attention to the most important insight. Use <code>ax.annotate()</code> to add arrows, text, and callouts that say <em>"this is what matters."</em></p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Annotations & Storytelling</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

months = np.<span style="color:#93c5fd;">arange</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">13</span>)
revenue = [<span style="color:#fcd34d;">120</span>, <span style="color:#fcd34d;">115</span>, <span style="color:#fcd34d;">98</span>, <span style="color:#fcd34d;">130</span>, <span style="color:#fcd34d;">145</span>, <span style="color:#fcd34d;">162</span>,
           <span style="color:#fcd34d;">88</span>,  <span style="color:#fcd34d;">175</span>, <span style="color:#fcd34d;">190</span>, <span style="color:#fcd34d;">210</span>, <span style="color:#fcd34d;">225</span>, <span style="color:#fcd34d;">270</span>]

fig, ax = plt.<span style="color:#93c5fd;">subplots</span>(figsize=(<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">5</span>))
ax.<span style="color:#93c5fd;">plot</span>(months, revenue, color=<span style="color:#a7f3d0;">"#3498db"</span>, linewidth=<span style="color:#fcd34d;">2.5</span>, marker=<span style="color:#a7f3d0;">"o"</span>)

<span style="color:#6b7280;"># Annotate the July dip with an arrow and explanation</span>
ax.<span style="color:#93c5fd;">annotate</span>(
    <span style="color:#a7f3d0;">"July dip: server\noutage (3 days)"</span>,
    xy=(<span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">88</span>),             <span style="color:#6b7280;"># Arrow tip points HERE</span>
    xytext=(<span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">60</span>),          <span style="color:#6b7280;"># Text is placed HERE</span>
    arrowprops=<span style="color:#93c5fd;">dict</span>(arrowstyle=<span style="color:#a7f3d0;">"->"</span>, color=<span style="color:#a7f3d0;">"red"</span>, lw=<span style="color:#fcd34d;">1.5</span>),
    color=<span style="color:#a7f3d0;">"red"</span>, fontsize=<span style="color:#fcd34d;">9</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>
)

<span style="color:#6b7280;"># Mark Q4 record as a shaded region</span>
ax.<span style="color:#93c5fd;">axvspan</span>(<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">12</span>, alpha=<span style="color:#fcd34d;">0.1</span>, color=<span style="color:#a7f3d0;">"green"</span>, label=<span style="color:#a7f3d0;">"Q4 Record High"</span>)
ax.<span style="color:#93c5fd;">text</span>(<span style="color:#fcd34d;">10.2</span>, <span style="color:#fcd34d;">255</span>, <span style="color:#a7f3d0;">"Q4 Record\n$270K"</span>, color=<span style="color:#a7f3d0;">"green"</span>, fontsize=<span style="color:#fcd34d;">9</span>)

month_labels = [<span style="color:#a7f3d0;">"Jan"</span>,<span style="color:#a7f3d0;">"Feb"</span>,<span style="color:#a7f3d0;">"Mar"</span>,<span style="color:#a7f3d0;">"Apr"</span>,<span style="color:#a7f3d0;">"May"</span>,<span style="color:#a7f3d0;">"Jun"</span>,
                <span style="color:#a7f3d0;">"Jul"</span>,<span style="color:#a7f3d0;">"Aug"</span>,<span style="color:#a7f3d0;">"Sep"</span>,<span style="color:#a7f3d0;">"Oct"</span>,<span style="color:#a7f3d0;">"Nov"</span>,<span style="color:#a7f3d0;">"Dec"</span>]
ax.<span style="color:#93c5fd;">set_xticks</span>(months)
ax.<span style="color:#93c5fd;">set_xticklabels</span>(month_labels)
ax.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"2024 Monthly Revenue — Annotated"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Revenue ($K)"</span>)
ax.<span style="color:#93c5fd;">legend</span>()

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Line chart with a red arrow pointing to the July dip at $88K.
Annotation text reads "July dip: server outage (3 days)".
Oct–Dec shaded green with "Q4 Record $270K" labeled.
Viewer immediately understands both the anomaly and the achievement.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '15.10 Best Practices, Color & Storytelling',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L15_10', [
                ['q' => 'According to Tufte\'s data-ink ratio principle, what should you do to improve a chart?', 'opts' => ['Add more colors to make it visually interesting', 'Remove non-data elements like decorative gridlines, borders, and chartjunk that do not encode information', 'Use 3D effects to make bars appear more prominent', 'Add a detailed legend for every element'], 'ans' => 1, 'exp' => 'Tufte\'s principle states that every drop of ink should convey data. Non-data ink — heavy grid lines, background fills, decorative borders, 3D effects — reduces the signal-to-noise ratio and should be minimized or removed.'],
                ['q' => 'Why is it deceptive to start a bar chart\'s y-axis at a value other than zero?', 'opts' => ['Matplotlib cannot render non-zero y-axis starts', 'It makes the chart look smaller', 'It visually exaggerates differences between bars because the bar length no longer represents proportional magnitude from zero', 'It reduces the data-ink ratio'], 'ans' => 2, 'exp' => 'Bar length encodes value relative to the baseline. If bars start at 95 instead of 0, a difference of 2 units fills the entire bar height — making a 2% difference look like a 100% difference. Line charts allow non-zero baselines; bar charts should not.'],
                ['q' => 'Which palette type should you use for a categorical variable like continent names?', 'opts' => ['Sequential (e.g., Blues)', 'Diverging (e.g., coolwarm)', 'Qualitative (e.g., Set2)', 'Monochrome (shades of gray)'], 'ans' => 2, 'exp' => 'Qualitative palettes use distinct, perceptually separated colors with no implied ordering — ideal for nominal categories like continent, product type, or department. Sequential palettes imply ordering; diverging palettes imply a meaningful midpoint.'],
                ['q' => 'What does ax.annotate() allow you to add to a chart?', 'opts' => ['Only plain text labels', 'Text with an optional arrow pointing to a specific data coordinate', 'Footnotes below the figure', 'Interactive hover tooltips'], 'ans' => 1, 'exp' => 'ax.annotate(text, xy=point, xytext=label_position, arrowprops=...) places text at xytext and draws an arrow toward xy. It is the standard tool for calling out specific data points, anomalies, or key insights on a chart.'],
                ['q' => 'Why should you consider colorblind-safe palettes in your visualizations?', 'opts' => ['They are required by Matplotlib', 'Approximately 8% of men have red-green color blindness — charts using red and green to convey meaning are unreadable to them', 'Colorblind palettes have higher contrast ratios', 'They are mandatory for publication'], 'ans' => 1, 'exp' => 'Red-green color blindness (deuteranopia/protanopia) affects roughly 8% of men and 0.5% of women. Charts that encode meaning through red vs green — like a confusion matrix — are uninterpretable for these viewers. The "colorblind" or "viridis" palette in Seaborn are designed to be distinguishable for all forms of color vision deficiency.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 15.11 — Final Exam: Data Visualization Mastery
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            ['q' => 'Which chart type is best for showing how a continuous variable is distributed across a population?', 'opts' => ['Bar Chart', 'Line Chart', 'Scatter Plot', 'Histogram'], 'ans' => 3, 'exp' => 'Histograms bin continuous data into intervals and show frequency per bin, revealing the shape of the distribution — whether it is normal, skewed, bimodal, or uniform.'],
            ['q' => 'What does plt.subplots(figsize=(10, 5)) control?', 'opts' => ['The number of subplots', 'The resolution (DPI) of the figure', 'The physical width and height of the figure canvas in inches', 'The font size of all text'], 'ans' => 2, 'exp' => 'figsize=(width, height) sets the canvas size in inches. This directly determines the aspect ratio and the physical size when saved to file. It does not affect DPI.'],
            ['q' => 'In Seaborn, what does the "palette" parameter control?', 'opts' => ['The background color of the chart', 'The set of colors used for the hue-encoded variable', 'The font palette used for labels', 'The grid line pattern'], 'ans' => 1, 'exp' => 'palette selects the color scheme applied to the variable mapped by hue=. Seaborn accepts named palettes ("Set2", "viridis"), lists of hex colors, or matplotlib colormaps.'],
            ['q' => 'What does sns.pairplot(df, hue="species") produce?', 'opts' => ['A single scatter plot colored by species', 'An n×n grid of plots — scatter plots between each pair of numeric columns, with KDEs on the diagonal, all colored by species', 'A box plot for each species', 'A correlation heatmap'], 'ans' => 1, 'exp' => 'pairplot creates a complete grid of bivariate scatter plots and univariate distributions for all numeric columns, with each species rendered in a distinct color.'],
            ['q' => 'What is the purpose of plt.tight_layout()?', 'opts' => ['Increases the figure resolution', 'Automatically adjusts subplot spacing to prevent labels and titles from overlapping or being cut off', 'Saves the figure to disk', 'Locks the axes scale'], 'ans' => 1, 'exp' => 'tight_layout() automatically adjusts padding between and around subplots so that tick labels, axis labels, and titles do not overlap each other or get clipped at the figure edge.'],
            ['q' => 'You have a dataset with 12 product categories and you want to compare their revenue. Which chart is most appropriate?', 'opts' => ['Pie Chart', 'Scatter Plot', 'Horizontal Bar Chart', 'Violin Plot'], 'ans' => 2, 'exp' => 'With 12 categories, a pie chart would be unreadable — too many slices. A horizontal bar chart sorted by value allows viewers to quickly compare all 12 categories and identify the top and bottom performers.'],
            ['q' => 'What does the "mask" parameter in sns.heatmap() do?', 'opts' => ['Hides the color bar', 'Masks (hides) cells where the mask array is True — commonly used to hide the upper triangle of a symmetric correlation matrix', 'Removes outlier cells', 'Changes the text color of masked cells'], 'ans' => 1, 'exp' => 'mask is a boolean array the same shape as the data. Cells where mask=True are not plotted. A common use is np.triu(ones) to mask the upper triangle of a correlation matrix, since it mirrors the lower triangle.'],
            ['q' => 'What happens if you call plt.show() before plt.savefig()?', 'opts' => ['The chart is saved with a watermark', 'show() clears the figure buffer — savefig() will save a blank image', 'Nothing changes — both work in any order', 'The file is saved but with lower DPI'], 'ans' => 1, 'exp' => 'plt.show() renders and then clears the current figure from memory. Calling savefig() after show() writes a blank file because the figure has been cleared. Always call savefig() first, then show().'],
            ['q' => 'Which Plotly Express parameter creates an animated chart that plays through time?', 'opts' => ['animate=', 'play=', 'animation_frame=', 'time_axis='], 'ans' => 2, 'exp' => 'animation_frame="year" (or any column name) creates a Play button and slider that steps through each unique value of that column, animating the chart — effectively showing change over time.'],
            ['q' => 'A KDE (Kernel Density Estimate) curve on a histogram represents:', 'opts' => ['The cumulative sum of bars', 'A bar connecting all histogram peaks', 'A smoothed continuous probability density curve estimated from the data', 'The standard error of the frequency'], 'ans' => 2, 'exp' => 'KDE places a smooth kernel (usually Gaussian) at each data point, then sums them into a single smooth curve. This estimates the underlying continuous probability density function — more informative than the jagged histogram alone.'],
        ];

        $finalContent = <<<HTML
<div id="org-lock-screen" style="display:block;text-align:center;padding:60px 20px;">
    <div style="font-size:3rem;margin-bottom:16px;">🔒</div>
    <h2 style="color:var(--text);margin-bottom:8px;">Organization Access Required</h2>
    <p style="color:var(--muted);max-width:400px;margin:0 auto;">The Final Exam is available exclusively to learners enrolled through a verified organization.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 15: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 15.1 through 15.10 — visualization principles, Matplotlib, Seaborn, distributions, scatter plots, heatmaps, pair plots, pie/donut charts, subplots, Plotly interactivity, color theory, and visual storytelling. Good luck!</p>
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
            'module_id'   => $module->id,
            'title'       => '15.11 Final Exam: Data Visualization Mastery',
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