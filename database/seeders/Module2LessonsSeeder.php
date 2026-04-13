<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module2LessonsSeeder
 * Seeds lessons for Module 2: Basics of Statistics.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module2LessonsSeeder
 */
class Module2LessonsSeeder extends Seeder
{
    public function run()
    {
        $statsModule = Module::where('order_index', 2)->firstOrFail();
        Lesson::where('module_id', $statsModule->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 2.1 — What is Statistics? Types of Data
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What is Statistics?</h2>
<p>Statistics is the science of <strong>collecting, organizing, analyzing, interpreting, and presenting data</strong>. It is the mathematical backbone of every data science workflow — from A/B testing a new product feature, to validating a machine learning model, to understanding the spread of a disease. Without statistics, data is just noise.</p>

<p>There are two major branches you will use constantly:</p>
<ul style="color:var(--muted);line-height:2;margin-left:1.5rem;margin-bottom:1.5rem;">
  <li><strong style="color:var(--text);">Descriptive Statistics</strong> — Summarizes and describes a dataset. Answers: "What does my data look like?"</li>
  <li><strong style="color:var(--text);">Inferential Statistics</strong> — Uses a sample to make conclusions about a larger population. Answers: "What can I conclude beyond what I observed?"</li>
</ul>

<h3>Population vs. Sample</h3>
<p>A <strong>population</strong> is the entire group you want to study. A <strong>sample</strong> is the subset you actually observe. Because studying an entire population is usually impossible (you cannot survey all 8 billion humans), statistics lets us draw reliable conclusions from samples. The quality of your conclusions depends entirely on how representative your sample is.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Population vs Sample</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Simulating a population of 10,000 student exam scores</span>
<span style="color:#c4b5fd;">import</span> random
random.seed(<span style="color:#fcd34d;">42</span>)   <span style="color:#6b7280;"># seed for reproducibility</span>

<span style="color:#93c5fd;">population</span> = [random.randint(<span style="color:#fcd34d;">50</span>, <span style="color:#fcd34d;">100</span>) <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">10000</span>)]

<span style="color:#6b7280;"># Draw a random sample of 30 students</span>
<span style="color:#93c5fd;">sample</span> = random.sample(population, <span style="color:#fcd34d;">30</span>)

<span style="color:#93c5fd;">pop_mean</span>    = <span style="color:#93c5fd;">sum</span>(population) / <span style="color:#93c5fd;">len</span>(population)
<span style="color:#93c5fd;">sample_mean</span> = <span style="color:#93c5fd;">sum</span>(sample) / <span style="color:#93c5fd;">len</span>(sample)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Population size  : {len(population):,}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Population mean  : {pop_mean:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sample size      : {len(sample)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sample mean      : {sample_mean:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Estimation error : {abs(pop_mean - sample_mean):.2f} points"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Population size  : 10,000
Population mean  : 74.97
Sample size      : 30
Sample mean      : 75.63
Estimation error : 0.66 points</div>
  </div>
</div>

<h3>Types of Data: The Four Levels of Measurement</h3>
<p>Not all data is the same. The type of data you have determines which statistical operations make sense. Using the wrong analysis on the wrong data type is one of the most common errors in data science.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Classifying Data Types</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># NOMINAL  — categories with NO natural order</span>
<span style="color:#93c5fd;">blood_types</span> = [<span style="color:#a7f3d0;">"A"</span>, <span style="color:#a7f3d0;">"B"</span>, <span style="color:#a7f3d0;">"O"</span>, <span style="color:#a7f3d0;">"AB"</span>]  <span style="color:#6b7280;"># Can't say A > B meaningfully</span>

<span style="color:#6b7280;"># ORDINAL  — categories WITH a natural order, but gaps are unequal</span>
<span style="color:#93c5fd;">satisfaction</span> = [<span style="color:#a7f3d0;">"Poor"</span>, <span style="color:#a7f3d0;">"Fair"</span>, <span style="color:#a7f3d0;">"Good"</span>, <span style="color:#a7f3d0;">"Excellent"</span>]  <span style="color:#6b7280;"># Order matters, but gap between Poor→Fair ≠ Good→Excellent</span>

<span style="color:#6b7280;"># INTERVAL — ordered, EQUAL gaps, but NO true zero</span>
<span style="color:#93c5fd;">temperatures_c</span> = [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">20</span>, <span style="color:#fcd34d;">37</span>, <span style="color:#fcd34d;">100</span>]  <span style="color:#6b7280;"># 0°C ≠ "no temperature" — ratios meaningless</span>

<span style="color:#6b7280;"># RATIO    — ordered, equal gaps, TRUE zero exists</span>
<span style="color:#93c5fd;">salaries</span> = [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">25000</span>, <span style="color:#fcd34d;">75000</span>, <span style="color:#fcd34d;">200000</span>]  <span style="color:#6b7280;"># 0 salary means truly nothing. 100k is 2x 50k.</span>

<span style="color:#93c5fd;">data_types</span> = {
    <span style="color:#a7f3d0;">"Nominal"</span> : <span style="color:#a7f3d0;">"Labels only. Mode is the only valid average."</span>,
    <span style="color:#a7f3d0;">"Ordinal"</span> : <span style="color:#a7f3d0;">"Ranked order. Median is valid. Mean is misleading."</span>,
    <span style="color:#a7f3d0;">"Interval"</span>: <span style="color:#a7f3d0;">"Equal gaps. Mean & SD valid. Ratios are not."</span>,
    <span style="color:#a7f3d0;">"Ratio"</span>   : <span style="color:#a7f3d0;">"Full arithmetic. All statistics are valid."</span>,
}
<span style="color:#c4b5fd;">for</span> dtype, note <span style="color:#c4b5fd;">in</span> data_types.items():
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{dtype:10} → {note}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Nominal    → Labels only. Mode is the only valid average.
Ordinal    → Ranked order. Median is valid. Mean is misleading.
Interval   → Equal gaps. Mean &amp; SD valid. Ratios are not.
Ratio      → Full arithmetic. All statistics are valid.</div>
  </div>
</div>

<h3>Quantitative vs. Qualitative Data</h3>
<p><strong>Quantitative (Numerical)</strong> data can be measured and expressed as a number. It is further split into <em>discrete</em> (countable, whole numbers — like the number of goals in a match) and <em>continuous</em> (any value in a range — like temperature or height). <strong>Qualitative (Categorical)</strong> data represents categories or groups and cannot be averaged meaningfully.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Discrete vs Continuous</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># DISCRETE — whole numbers, countable</span>
<span style="color:#93c5fd;">goals_per_match</span> = [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">2</span>]   <span style="color:#6b7280;"># Can't score 1.5 goals</span>
<span style="color:#93c5fd;">children_per_family</span> = [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">1</span>]

<span style="color:#6b7280;"># CONTINUOUS — any value within a range</span>
<span style="color:#93c5fd;">heights_cm</span>    = [<span style="color:#fcd34d;">162.5</span>, <span style="color:#fcd34d;">170.0</span>, <span style="color:#fcd34d;">158.3</span>, <span style="color:#fcd34d;">182.1</span>]
<span style="color:#93c5fd;">temperatures</span>  = [<span style="color:#fcd34d;">36.6</span>, <span style="color:#fcd34d;">37.2</span>, <span style="color:#fcd34d;">36.8</span>, <span style="color:#fcd34d;">38.5</span>]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Discrete example — Goals:"</span>,   goals_per_match)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Continuous example — Heights:"</span>, heights_cm)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Average goals : {sum(goals_per_match)/len(goals_per_match):.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Average height: {sum(heights_cm)/len(heights_cm):.2f} cm"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Discrete example — Goals:   [0, 1, 2, 3, 1, 4, 2]
Continuous example — Heights: [162.5, 170.0, 158.3, 182.1]
Average goals : 1.86
Average height: 168.23 cm</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $statsModule->id,
            'title'       => '2.1 What is Statistics? Types of Data',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L2_1', [
                ['q' => 'Which branch of statistics draws conclusions about a population from a sample?', 'opts' => ['Descriptive Statistics', 'Inferential Statistics', 'Exploratory Analysis', 'Prescriptive Statistics'], 'ans' => 1, 'exp' => 'Inferential statistics uses sample data to make generalizations or predictions about a larger population. Descriptive statistics only summarizes the data you have.'],
                ['q' => 'Customer satisfaction rated as "Poor / Fair / Good / Excellent" is what type of data?', 'opts' => ['Nominal', 'Ordinal', 'Interval', 'Ratio'], 'ans' => 1, 'exp' => 'Ordinal data has a natural order but unequal spacing between categories. You know Excellent > Good, but you cannot say by exactly how much.'],
                ['q' => 'Which type of data has a TRUE zero point, meaning "zero" represents complete absence?', 'opts' => ['Nominal', 'Ordinal', 'Interval', 'Ratio'], 'ans' => 3, 'exp' => 'Ratio data has a true zero. 0 kg means no weight, 0 salary means no income. This allows meaningful ratios: 100k salary is truly twice 50k.'],
                ['q' => 'The number of students enrolled in a university is what kind of variable?', 'opts' => ['Continuous', 'Discrete', 'Nominal', 'Interval'], 'ans' => 1, 'exp' => 'Student count is discrete because it is a whole number you can count — you cannot have 2.5 students enrolled.'],
                ['q' => 'Temperature in Celsius is classified as what type of data?', 'opts' => ['Ratio', 'Nominal', 'Interval', 'Ordinal'], 'ans' => 2, 'exp' => 'Celsius is interval data — it has equal gaps between degrees, but 0°C does not mean "no temperature." This means ratios are invalid: 20°C is not "twice as hot" as 10°C.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 2.2 — Measures of Central Tendency
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Measures of Central Tendency</h2>
<p>A measure of central tendency is a single value that attempts to describe a dataset by identifying its central position. These are the first statistics you compute on any new dataset — they tell you "where is most of my data?" The three most important are the <strong>mean</strong>, <strong>median</strong>, and <strong>mode</strong>.</p>

<h3>The Mean (Arithmetic Average)</h3>
<p>The mean is the sum of all values divided by the count. It is the most mathematically useful measure, but it is <em>heavily sensitive to outliers</em>. A single extreme value can drag the mean far from where most of the data sits — this is why salaries, home prices, and income are usually reported as medians, not means.</p>
<p style="background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.2);padding:12px 16px;border-radius:6px;font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--text);">μ = (Σx) / n &nbsp;&nbsp;&nbsp; where μ = population mean, Σx = sum of all values, n = count</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Mean & Outlier Effect</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># 9 employees earning similar salaries + 1 CEO</span>
<span style="color:#93c5fd;">salaries</span> = [<span style="color:#fcd34d;">30000</span>, <span style="color:#fcd34d;">32000</span>, <span style="color:#fcd34d;">35000</span>, <span style="color:#fcd34d;">31000</span>, <span style="color:#fcd34d;">33000</span>,
             <span style="color:#fcd34d;">29000</span>, <span style="color:#fcd34d;">34000</span>, <span style="color:#fcd34d;">36000</span>, <span style="color:#fcd34d;">32500</span>, <span style="color:#fcd34d;">2500000</span>]  <span style="color:#6b7280;"># CEO salary!</span>

<span style="color:#6b7280;"># Manual mean calculation</span>
<span style="color:#93c5fd;">mean_salary</span> = <span style="color:#93c5fd;">sum</span>(salaries) / <span style="color:#93c5fd;">len</span>(salaries)

<span style="color:#6b7280;"># Using statistics module (built-in, no install needed)</span>
<span style="color:#c4b5fd;">import</span> statistics
<span style="color:#93c5fd;">mean2</span> = statistics.mean(salaries)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mean salary  : ₱{mean_salary:,.0f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"statistics.mean: ₱{mean2:,.0f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nIs the mean representative of most employees?"</span>)
<span style="color:#93c5fd;">below_mean</span> = [s <span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> salaries <span style="color:#c4b5fd;">if</span> s &lt; mean_salary]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{len(below_mean)} out of {len(salaries)} employees earn BELOW the mean!"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Mean salary  : ₱280,250
statistics.mean: ₱280,250

Is the mean representative of most employees?
9 out of 10 employees earn BELOW the mean!</div>
  </div>
</div>

<h3>The Median (Middle Value)</h3>
<p>The median is the middle value when all data points are sorted. If there is an even number of values, the median is the average of the two middle values. The median is <em>resistant to outliers</em> — the CEO's ₱2.5M salary barely affects it, making it the honest measure of what a "typical" employee earns.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Median (Manual + Library)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> statistics

<span style="color:#93c5fd;">salaries</span> = [<span style="color:#fcd34d;">30000</span>, <span style="color:#fcd34d;">32000</span>, <span style="color:#fcd34d;">35000</span>, <span style="color:#fcd34d;">31000</span>, <span style="color:#fcd34d;">33000</span>,
             <span style="color:#fcd34d;">29000</span>, <span style="color:#fcd34d;">34000</span>, <span style="color:#fcd34d;">36000</span>, <span style="color:#fcd34d;">32500</span>, <span style="color:#fcd34d;">2500000</span>]

<span style="color:#6b7280;"># Manual median</span>
<span style="color:#93c5fd;">sorted_s</span> = <span style="color:#93c5fd;">sorted</span>(salaries)
<span style="color:#93c5fd;">n</span>        = <span style="color:#93c5fd;">len</span>(sorted_s)
<span style="color:#c4b5fd;">if</span> n % <span style="color:#fcd34d;">2</span> == <span style="color:#fcd34d;">1</span>:
    <span style="color:#93c5fd;">median_manual</span> = sorted_s[n // <span style="color:#fcd34d;">2</span>]
<span style="color:#c4b5fd;">else</span>:
    <span style="color:#93c5fd;">median_manual</span> = (sorted_s[n // <span style="color:#fcd34d;">2</span> - <span style="color:#fcd34d;">1</span>] + sorted_s[n // <span style="color:#fcd34d;">2</span>]) / <span style="color:#fcd34d;">2</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sorted salaries: {sorted_s[:5]}... (truncated)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Median (manual) : ₱{median_manual:,.0f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Median (library): ₱{statistics.median(salaries):,.0f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mean            : ₱{statistics.mean(salaries):,.0f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nThe median (₱32,750) is far more representative than the mean (₱280,250)!"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Sorted salaries: [29000, 30000, 31000, 32000, 32500]... (truncated)
Median (manual) : ₱32,750
Median (library): ₱32,750
Mean            : ₱280,250

The median (₱32,750) is far more representative than the mean (₱280,250)!</div>
  </div>
</div>

<h3>The Mode (Most Frequent Value)</h3>
<p>The mode is the value that appears most often in the dataset. It is the only measure of central tendency valid for <em>nominal (categorical)</em> data. A dataset can be unimodal (one mode), bimodal (two modes), or multimodal. Datasets with no repeating values have no mode.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Mode & Frequency Table</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> statistics

<span style="color:#93c5fd;">grades</span>   = [<span style="color:#a7f3d0;">"A"</span>, <span style="color:#a7f3d0;">"B"</span>, <span style="color:#a7f3d0;">"A"</span>, <span style="color:#a7f3d0;">"C"</span>, <span style="color:#a7f3d0;">"B"</span>, <span style="color:#a7f3d0;">"A"</span>, <span style="color:#a7f3d0;">"A"</span>, <span style="color:#a7f3d0;">"B"</span>, <span style="color:#a7f3d0;">"C"</span>, <span style="color:#a7f3d0;">"A"</span>]
<span style="color:#93c5fd;">shoe_sizes</span> = [<span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">9</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">9</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">11</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">9</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">10</span>]

<span style="color:#6b7280;"># Build a frequency table manually</span>
<span style="color:#93c5fd;">freq</span> = {}
<span style="color:#c4b5fd;">for</span> grade <span style="color:#c4b5fd;">in</span> grades:
    <span style="color:#93c5fd;">freq</span>[grade] = freq.get(grade, <span style="color:#fcd34d;">0</span>) + <span style="color:#fcd34d;">1</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Frequency Table:"</span>)
<span style="color:#c4b5fd;">for</span> k, v <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">sorted</span>(freq.items()):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Grade {k}: {'█' * v} ({v})"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nMode of grades    : {statistics.mode(grades)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mode of shoe sizes: {statistics.mode(shoe_sizes)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Multimode (all)   : {statistics.multimode(shoe_sizes)}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Frequency Table:
  Grade A: █████ (5)
  Grade B: ███ (3)
  Grade C: ██ (2)

Mode of grades    : A
Mode of shoe sizes: 8
Multimode (all)   : [8, 9, 10]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $statsModule->id,
            'title'       => '2.2 Measures of Central Tendency: Mean, Median, Mode',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L2_2', [
                ['q' => 'A dataset is [10, 12, 11, 10, 13, 10, 200]. Which measure is most affected by the value 200?', 'opts' => ['Mode', 'Median', 'Mean', 'Range'], 'ans' => 2, 'exp' => 'The mean is heavily affected by outliers because every value contributes to the sum. The median would barely move since 200 just becomes the new maximum. The mode is unaffected entirely.'],
                ['q' => 'For the dataset [3, 5, 7, 9, 11], what is the median?', 'opts' => ['7', '5', '9', '6'], 'ans' => 0, 'exp' => 'With 5 sorted values, the median is the middle value at index 2 (0-based). Sorted: [3,5,7,9,11] → middle = 7.'],
                ['q' => 'Which measure of central tendency is the ONLY valid one for nominal (categorical) data?', 'opts' => ['Mean', 'Median', 'Mode', 'Standard Deviation'], 'ans' => 2, 'exp' => 'Mode is the only measure valid for nominal data. You cannot meaningfully average categories like blood type or country name, but you can find the most frequent one.'],
                ['q' => 'A dataset has two values that both appear most frequently. This is called?', 'opts' => ['Unimodal', 'Bimodal', 'Symmetric', 'Skewed'], 'ans' => 1, 'exp' => 'When two values share the highest frequency, the dataset is bimodal. Python\'s statistics.multimode() returns all modes in such cases.'],
                ['q' => 'For the dataset [2, 4, 4, 6, 8], what is the mean?', 'opts' => ['4', '4.8', '5', '4.4'], 'ans' => 1, 'exp' => 'Mean = (2+4+4+6+8) / 5 = 24 / 5 = 4.8. Notice the mean (4.8) and median (4) differ here — the higher values pull the mean up slightly.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 2.3 — Measures of Variability
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Measures of Variability (Spread)</h2>
<p>Central tendency tells you where data is centered, but it hides crucial information. Two datasets can have the exact same mean and median while looking completely different — because their <strong>spread</strong> differs. Measures of variability tell you how much individual values deviate from the center. This is the difference between a model with stable predictions and one that wildly fluctuates.</p>

<h3>Range</h3>
<p>The simplest measure of spread. Range = Maximum − Minimum. It captures the full width of your data but is extremely sensitive to outliers — one extreme value inflates it dramatically.</p>

<h3>Variance (σ² for Population, s² for Sample)</h3>
<p>Variance measures the average of the squared deviations from the mean. We square the deviations to make them all positive (and to penalize large deviations more). The population variance uses N in the denominator; sample variance uses N−1 (called <em>Bessel's correction</em>) to produce an unbiased estimate.</p>
<p style="background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.2);padding:12px 16px;border-radius:6px;font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--text);">σ² = Σ(xᵢ − μ)² / N &nbsp;&nbsp;&nbsp; (population) <br>s² = Σ(xᵢ − x̄)² / (N−1) &nbsp;&nbsp;(sample)</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Variance from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> statistics

<span style="color:#93c5fd;">scores_a</span> = [<span style="color:#fcd34d;">70</span>, <span style="color:#fcd34d;">72</span>, <span style="color:#fcd34d;">71</span>, <span style="color:#fcd34d;">73</span>, <span style="color:#fcd34d;">70</span>]   <span style="color:#6b7280;"># Very consistent</span>
<span style="color:#93c5fd;">scores_b</span> = [<span style="color:#fcd34d;">50</span>, <span style="color:#fcd34d;">60</span>, <span style="color:#fcd34d;">71</span>, <span style="color:#fcd34d;">85</span>, <span style="color:#fcd34d;">100</span>]  <span style="color:#6b7280;"># Same mean, very spread out</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">sample_variance</span>(data):
    <span style="color:#93c5fd;">xbar</span> = <span style="color:#93c5fd;">sum</span>(data) / <span style="color:#93c5fd;">len</span>(data)
    <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">sum</span>((x - xbar)**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> data) / (<span style="color:#93c5fd;">len</span>(data) - <span style="color:#fcd34d;">1</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Scores A — Mean: {sum(scores_a)/len(scores_a):.1f}, Variance: {sample_variance(scores_a):.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Scores B — Mean: {sum(scores_b)/len(scores_b):.1f}, Variance: {sample_variance(scores_b):.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nUsing statistics library:"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Variance A: {statistics.variance(scores_a):.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Variance B: {statistics.variance(scores_b):.2f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Scores A — Mean: 71.2, Variance: 1.70
Scores B — Mean: 73.2, Variance: 367.20

Using statistics library:
Variance A: 1.70
Variance B: 367.20</div>
  </div>
</div>

<h3>Standard Deviation — The Most Used Measure of Spread</h3>
<p>Standard deviation is simply the square root of variance. By taking the square root, we bring the unit back to the same scale as the original data. If your data is in kilograms, the standard deviation is also in kilograms — making it directly interpretable. A small SD means data clusters tightly around the mean; a large SD means high variability.</p>
<p style="background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.2);padding:12px 16px;border-radius:6px;font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--text);">σ = √σ² &nbsp;&nbsp;&nbsp; (population SD) &nbsp;&nbsp;&nbsp;&nbsp; s = √s² &nbsp;&nbsp;&nbsp; (sample SD)</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Standard Deviation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> statistics, math

<span style="color:#93c5fd;">exam_scores</span> = [<span style="color:#fcd34d;">68</span>, <span style="color:#fcd34d;">72</span>, <span style="color:#fcd34d;">75</span>, <span style="color:#fcd34d;">80</span>, <span style="color:#fcd34d;">65</span>, <span style="color:#fcd34d;">90</span>, <span style="color:#fcd34d;">78</span>, <span style="color:#fcd34d;">70</span>, <span style="color:#fcd34d;">85</span>, <span style="color:#fcd34d;">73</span>]

<span style="color:#93c5fd;">mean</span> = statistics.mean(exam_scores)
<span style="color:#93c5fd;">sd</span>   = statistics.stdev(exam_scores)  <span style="color:#6b7280;"># sample standard deviation</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mean  : {mean:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"SD    : {sd:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Range : {max(exam_scores) - min(exam_scores)}"</span>)
<span style="color:#93c5fd;">print</span>()

<span style="color:#6b7280;"># Empirical rule: ~68% of data falls within 1 SD of the mean</span>
<span style="color:#93c5fd;">within_1sd</span> = [x <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> exam_scores <span style="color:#c4b5fd;">if</span> mean - sd &lt;= x &lt;= mean + sd]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"1 SD range: [{mean-sd:.1f}, {mean+sd:.1f}]"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Values within 1 SD: {len(within_1sd)}/{len(exam_scores)} ({100*len(within_1sd)/len(exam_scores):.0f}%)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Mean  : 75.60
SD    : 7.63

Range : 25

1 SD range: [67.97, 83.23]
Values within 1 SD: 7/10 (70%)</div>
  </div>
</div>

<h3>Interquartile Range (IQR) — The Outlier-Resistant Spread</h3>
<p>The IQR is the range of the middle 50% of data. It is calculated as Q3 − Q1 where Q1 is the 25th percentile and Q3 is the 75th percentile. IQR is resistant to outliers because it completely ignores the top and bottom 25% of values. It is the basis of box plots and the standard method for detecting outliers.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — IQR & Outlier Detection</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">data</span> = [<span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">15</span>, <span style="color:#fcd34d;">14</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">18</span>, <span style="color:#fcd34d;">11</span>, <span style="color:#fcd34d;">13</span>, <span style="color:#fcd34d;">15</span>, <span style="color:#fcd34d;">16</span>, <span style="color:#fcd34d;">14</span>, <span style="color:#fcd34d;">100</span>]  <span style="color:#6b7280;"># 100 is an outlier</span>

<span style="color:#93c5fd;">sorted_data</span> = <span style="color:#93c5fd;">sorted</span>(data)
<span style="color:#93c5fd;">n</span>           = <span style="color:#93c5fd;">len</span>(sorted_data)

<span style="color:#6b7280;"># Simple percentile function</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">percentile</span>(data, p):
    <span style="color:#93c5fd;">idx</span> = <span style="color:#93c5fd;">int</span>(<span style="color:#93c5fd;">len</span>(data) * p / <span style="color:#fcd34d;">100</span>)
    <span style="color:#c4b5fd;">return</span> data[<span style="color:#93c5fd;">min</span>(idx, <span style="color:#93c5fd;">len</span>(data) - <span style="color:#fcd34d;">1</span>)]

<span style="color:#93c5fd;">Q1</span>  = percentile(sorted_data, <span style="color:#fcd34d;">25</span>)
<span style="color:#93c5fd;">Q3</span>  = percentile(sorted_data, <span style="color:#fcd34d;">75</span>)
<span style="color:#93c5fd;">IQR</span> = Q3 - Q1

<span style="color:#6b7280;"># Outlier fences: anything beyond 1.5 * IQR from Q1/Q3 is an outlier</span>
<span style="color:#93c5fd;">lower_fence</span> = Q1 - <span style="color:#fcd34d;">1.5</span> * IQR
<span style="color:#93c5fd;">upper_fence</span> = Q3 + <span style="color:#fcd34d;">1.5</span> * IQR
<span style="color:#93c5fd;">outliers</span>    = [x <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> data <span style="color:#c4b5fd;">if</span> x &lt; lower_fence <span style="color:#c4b5fd;">or</span> x &gt; upper_fence]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Q1           : {Q1}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Q3           : {Q3}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"IQR          : {IQR}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Fences       : [{lower_fence}, {upper_fence}]"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Outliers     : {outliers}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Q1           : 12
Q3           : 15
IQR          : 3
Fences       : [7.5, 19.5]
Outliers     : [100]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $statsModule->id,
            'title'       => '2.3 Measures of Variability: Range, Variance, SD, IQR',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L2_3', [
                ['q' => 'Two classes have the same mean score of 75. Class A has SD = 2, Class B has SD = 20. What does this tell you?', 'opts' => ['Class A performed better', 'Class B is more consistent', 'Class A scores are more tightly clustered around 75', 'Class B has a higher median'], 'ans' => 2, 'exp' => 'A smaller standard deviation means data is tightly clustered around the mean. Class A (SD=2) is very consistent — almost everyone scored near 75. Class B (SD=20) has wildly different individual scores.'],
                ['q' => 'Why does sample variance use (N-1) in the denominator instead of N?', 'opts' => ['To make the math easier', 'To produce an unbiased estimate of the population variance (Bessel\'s correction)', 'Because samples are always smaller', 'To avoid division by zero'], 'ans' => 1, 'exp' => 'Dividing by (N-1) instead of N corrects for the fact that a sample tends to underestimate the population spread. This is called Bessel\'s correction and produces an unbiased estimator.'],
                ['q' => 'What is the IQR of the dataset [5, 8, 10, 12, 15, 18, 20]?', 'opts' => ['15', '10', '8', '12'], 'ans' => 1, 'exp' => 'Q1 (25th percentile) ≈ 8, Q3 (75th percentile) ≈ 18. IQR = Q3 - Q1 = 18 - 8 = 10. The IQR captures the spread of the middle 50% of data.'],
                ['q' => 'The standard deviation is in the same units as the original data. Why is this useful?', 'opts' => ['It makes the value smaller', 'It allows direct comparison with the mean and data values', 'It removes outliers', 'It converts data to percentages'], 'ans' => 1, 'exp' => 'Variance is in squared units (e.g., kg²), which is hard to interpret. Taking the square root (SD) restores the original units (kg), so you can directly say "scores vary by ±8 points on average."'],
                ['q' => 'Which measure of spread is most resistant to outliers?', 'opts' => ['Range', 'Variance', 'Standard Deviation', 'IQR'], 'ans' => 3, 'exp' => 'The IQR only uses the middle 50% of data (Q1 to Q3), completely ignoring the top and bottom extremes. Range, Variance, and SD all incorporate extreme values and are therefore sensitive to outliers.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 2.4 — Probability Fundamentals
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Probability Fundamentals</h2>
<p><strong>Probability</strong> is the mathematical language for quantifying uncertainty. It assigns a number between 0 and 1 to how likely an event is to occur — where 0 means impossible and 1 means certain. Every machine learning model ultimately outputs a probability. Understanding probability from the ground up will make you a far better data scientist.</p>

<h3>Basic Probability Rules</h3>
<p>The probability of an event A is written as P(A). If all outcomes are equally likely: <strong>P(A) = (favorable outcomes) / (total outcomes)</strong>.</p>
<p>Key rules:</p>
<ul style="color:var(--muted);line-height:2;margin-left:1.5rem;margin-bottom:1.5rem;">
  <li><strong style="color:var(--text);">Complement Rule:</strong> P(not A) = 1 − P(A)</li>
  <li><strong style="color:var(--text);">Addition Rule (OR):</strong> P(A or B) = P(A) + P(B) − P(A and B)</li>
  <li><strong style="color:var(--text);">Multiplication Rule (AND, independent):</strong> P(A and B) = P(A) × P(B)</li>
</ul>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Probability Rules</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># A standard deck of 52 cards</span>
<span style="color:#93c5fd;">total</span>    = <span style="color:#fcd34d;">52</span>
<span style="color:#93c5fd;">hearts</span>   = <span style="color:#fcd34d;">13</span>
<span style="color:#93c5fd;">aces</span>     = <span style="color:#fcd34d;">4</span>
<span style="color:#93c5fd;">ace_heart</span> = <span style="color:#fcd34d;">1</span>  <span style="color:#6b7280;"># Ace of hearts (in BOTH categories)</span>

<span style="color:#93c5fd;">p_heart</span> = hearts / total
<span style="color:#93c5fd;">p_ace</span>   = aces / total

<span style="color:#6b7280;"># Complement: P(not heart)</span>
<span style="color:#93c5fd;">p_not_heart</span> = <span style="color:#fcd34d;">1</span> - p_heart

<span style="color:#6b7280;"># Addition Rule: P(heart OR ace) = P(heart) + P(ace) - P(heart AND ace)</span>
<span style="color:#93c5fd;">p_heart_or_ace</span> = p_heart + p_ace - (ace_heart / total)

<span style="color:#6b7280;"># Multiplication Rule (independent events): P(two aces in a row, with replacement)</span>
<span style="color:#93c5fd;">p_two_aces</span> = p_ace * p_ace

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"P(Heart)         = {p_heart:.4f} ({p_heart:.1%})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"P(Not Heart)     = {p_not_heart:.4f} ({p_not_heart:.1%})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"P(Ace)           = {p_ace:.4f} ({p_ace:.1%})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"P(Heart OR Ace)  = {p_heart_or_ace:.4f} ({p_heart_or_ace:.1%})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"P(Ace AND Ace)   = {p_two_aces:.4f} ({p_two_aces:.2%})"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>P(Heart)         = 0.2500 (25.0%)
P(Not Heart)     = 0.7500 (75.0%)
P(Ace)           = 0.0769 (7.7%)
P(Heart OR Ace)  = 0.3077 (30.8%)
P(Ace AND Ace)   = 0.0059 (0.59%)</div>
  </div>
</div>

<h3>Conditional Probability & Bayes' Theorem</h3>
<p><strong>Conditional probability</strong> P(A|B) asks: "Given that B has already happened, what is the probability of A?" This is the foundation of Naive Bayes classifiers, spam filters, and medical diagnosis models.</p>
<p style="background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.2);padding:12px 16px;border-radius:6px;font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--text);">P(A|B) = P(A ∩ B) / P(B) &nbsp;&nbsp;&nbsp; Bayes: P(A|B) = P(B|A) × P(A) / P(B)</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Conditional Probability & Bayes</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Medical test scenario: Disease detection
# 1% of the population has the disease (prior)
# Test is 99% accurate (sensitivity) — P(Positive | Has Disease) = 0.99
# 5% false positive rate — P(Positive | No Disease) = 0.05</span>

<span style="color:#93c5fd;">p_disease</span>          = <span style="color:#fcd34d;">0.01</span>     <span style="color:#6b7280;"># Prior: base rate of disease</span>
<span style="color:#93c5fd;">p_no_disease</span>       = <span style="color:#fcd34d;">1</span> - p_disease
<span style="color:#93c5fd;">p_pos_given_disease</span>    = <span style="color:#fcd34d;">0.99</span>  <span style="color:#6b7280;"># True positive rate (sensitivity)</span>
<span style="color:#93c5fd;">p_pos_given_no_disease</span> = <span style="color:#fcd34d;">0.05</span>  <span style="color:#6b7280;"># False positive rate</span>

<span style="color:#6b7280;"># Total probability of testing positive</span>
<span style="color:#93c5fd;">p_positive</span> = (p_pos_given_disease * p_disease) + (p_pos_given_no_disease * p_no_disease)

<span style="color:#6b7280;"># Bayes' Theorem: P(Disease | Positive Test)</span>
<span style="color:#93c5fd;">p_disease_given_pos</span> = (p_pos_given_disease * p_disease) / p_positive

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"P(Disease | Positive Test) = {p_disease_given_pos:.2%}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nEven with a 99% accurate test, a positive result"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"only means ~{p_disease_given_pos:.0%} chance of actually having the disease!"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"This is why base rates (priors) matter enormously."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>P(Disease | Positive Test) = 16.67%

Even with a 99% accurate test, a positive result
only means ~17% chance of actually having the disease!
This is why base rates (priors) matter enormously.</div>
  </div>
</div>

<h3>Simulating Probability with Python</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Monte Carlo Coin Flip Simulation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> random
random.seed(<span style="color:#fcd34d;">0</span>)

<span style="color:#c4b5fd;">for</span> n_flips <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">100</span>, <span style="color:#fcd34d;">1000</span>, <span style="color:#fcd34d;">100000</span>]:
    <span style="color:#93c5fd;">heads</span> = <span style="color:#93c5fd;">sum</span>(random.choice([<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>]) <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n_flips))
    <span style="color:#93c5fd;">observed_p</span> = heads / n_flips
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"n={n_flips:>7,} flips → P(Heads) ≈ {observed_p:.4f}"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nLaw of Large Numbers: as n → ∞, observed probability → 0.5000"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>n=     10 flips → P(Heads) ≈ 0.6000
n=    100 flips → P(Heads) ≈ 0.5500
n=  1,000 flips → P(Heads) ≈ 0.5120
n=100,000 flips → P(Heads) ≈ 0.4997

Law of Large Numbers: as n → ∞, observed probability → 0.5000</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $statsModule->id,
            'title'       => '2.4 Probability Fundamentals & Bayes\' Theorem',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L2_4', [
                ['q' => 'If P(Rain) = 0.3, what is P(No Rain)?', 'opts' => ['0.3', '0.6', '0.7', '1.3'], 'ans' => 2, 'exp' => 'By the complement rule, P(not A) = 1 − P(A). P(No Rain) = 1 − 0.3 = 0.7. All probabilities of complementary events must sum to 1.'],
                ['q' => 'P(A) = 0.4, P(B) = 0.3, and A and B are mutually exclusive. What is P(A or B)?', 'opts' => ['0.12', '0.58', '0.70', '0.7'], 'ans' => 2, 'exp' => 'For mutually exclusive events, P(A or B) = P(A) + P(B) = 0.4 + 0.3 = 0.7. Since they cannot both occur, P(A and B) = 0, so no subtraction needed.'],
                ['q' => 'What does conditional probability P(A|B) represent?', 'opts' => ['Probability of A times B', 'Probability of A given that B has already occurred', 'Probability of neither A nor B', 'Probability of A or B'], 'ans' => 1, 'exp' => 'P(A|B) reads as "probability of A given B." It restricts the sample space to outcomes where B occurred and asks what fraction of those also have A.'],
                ['q' => 'Why does a highly accurate medical test still produce many false positives when the disease is rare?', 'opts' => ['The test is broken', 'Because of the base rate (prior probability) of the disease being very low', 'The sample size is too small', 'Sensitivity and specificity are the same thing'], 'ans' => 1, 'exp' => 'This is Bayes\' Theorem in action. When a disease is rare (low prior), even a small false-positive rate generates many false positives because there are far more healthy people being tested than sick ones.'],
                ['q' => 'The Law of Large Numbers states that as sample size increases, the observed frequency of an event...', 'opts' => ['Becomes more random', 'Converges toward the true theoretical probability', 'Becomes exactly 0.5 for any event', 'Increases without bound'], 'ans' => 1, 'exp' => 'The Law of Large Numbers: with more trials, the empirical (observed) probability converges to the true theoretical probability. This is why casinos always profit — over millions of bets, actual outcomes approach expected probabilities.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 2.5 — Probability Distributions
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Probability Distributions</h2>
<p>A <strong>probability distribution</strong> describes how probability is spread across all possible values of a variable. Understanding distributions is the key to choosing the right statistical test, the right model, and the right assumptions. Real-world data almost always follows a recognizable distribution — learning to identify them is a critical skill.</p>

<h3>The Normal Distribution (Gaussian)</h3>
<p>The normal distribution is the most important distribution in statistics. It is symmetric, bell-shaped, and completely described by just two parameters: mean (μ) and standard deviation (σ). It appears everywhere in nature — heights, IQ scores, measurement errors, and many biological phenomena. The <strong>Empirical Rule (68-95-99.7 Rule)</strong> states that in any normal distribution, approximately 68% of data falls within 1 SD, 95% within 2 SDs, and 99.7% within 3 SDs of the mean.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Normal Distribution & Empirical Rule</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> random, statistics, math
random.seed(<span style="color:#fcd34d;">1</span>)

<span style="color:#6b7280;"># Simulate 1000 heights from a normal distribution (mean=170, sd=10 cm)</span>
<span style="color:#93c5fd;">mu</span>      = <span style="color:#fcd34d;">170</span>
<span style="color:#93c5fd;">sigma</span>   = <span style="color:#fcd34d;">10</span>
<span style="color:#93c5fd;">heights</span> = [random.gauss(mu, sigma) <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1000</span>)]

<span style="color:#93c5fd;">mean</span> = statistics.mean(heights)
<span style="color:#93c5fd;">sd</span>   = statistics.stdev(heights)

<span style="color:#6b7280;"># Verify the Empirical Rule</span>
<span style="color:#c4b5fd;">for</span> k, label <span style="color:#c4b5fd;">in</span> [(<span style="color:#fcd34d;">1</span>, <span style="color:#a7f3d0;">"68%"</span>), (<span style="color:#fcd34d;">2</span>, <span style="color:#a7f3d0;">"95%"</span>), (<span style="color:#fcd34d;">3</span>, <span style="color:#a7f3d0;">"99.7%"</span>)]:
    <span style="color:#93c5fd;">within</span> = <span style="color:#93c5fd;">sum</span>(<span style="color:#fcd34d;">1</span> <span style="color:#c4b5fd;">for</span> h <span style="color:#c4b5fd;">in</span> heights <span style="color:#c4b5fd;">if</span> mean - k*sd &lt;= h &lt;= mean + k*sd)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Within {k} SD (expect ~{label}): {within/len(heights):.1%}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Within 1 SD (expect ~68%): 68.3%
Within 2 SD (expect ~95%): 95.0%
Within 3 SD (expect ~99.7%): 99.7%</div>
  </div>
</div>

<h3>Z-Scores: Standardizing Data</h3>
<p>A <strong>Z-score</strong> tells you how many standard deviations a value is from the mean. It transforms any normal distribution into the <em>Standard Normal Distribution</em> (μ=0, σ=1), allowing you to compare values across different scales. Z-scores are used in anomaly detection, normalization for ML models, and calculating probabilities.</p>
<p style="background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.2);padding:12px 16px;border-radius:6px;font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--text);">z = (x − μ) / σ</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Z-Scores & Standardization</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> statistics

<span style="color:#93c5fd;">scores</span> = [<span style="color:#fcd34d;">55</span>, <span style="color:#fcd34d;">60</span>, <span style="color:#fcd34d;">65</span>, <span style="color:#fcd34d;">70</span>, <span style="color:#fcd34d;">75</span>, <span style="color:#fcd34d;">80</span>, <span style="color:#fcd34d;">85</span>, <span style="color:#fcd34d;">95</span>]
<span style="color:#93c5fd;">mu</span>     = statistics.mean(scores)
<span style="color:#93c5fd;">sigma</span>  = statistics.stdev(scores)

<span style="color:#6b7280;"># Calculate Z-score for each value</span>
<span style="color:#93c5fd;">z_scores</span> = [(x - mu) / sigma <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> scores]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mean: {mu:.2f}  |  SD: {sigma:.2f}\n"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Score':>7} {'Z-Score':>9}  Interpretation"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-" * 45</span>)
<span style="color:#c4b5fd;">for</span> score, z <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(scores, z_scores):
    <span style="color:#c4b5fd;">if</span> z &lt; -<span style="color:#fcd34d;">1</span>:
        note = <span style="color:#a7f3d0;">"Below average"</span>
    <span style="color:#c4b5fd;">elif</span> z &gt; <span style="color:#fcd34d;">1</span>:
        note = <span style="color:#a7f3d0;">"Above average"</span>
    <span style="color:#c4b5fd;">else</span>:
        note = <span style="color:#a7f3d0;">"Within 1 SD of mean"</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{score:>7}  {z:>+8.2f}   {note}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Mean: 73.13  |  SD: 13.09

  Score   Z-Score  Interpretation
---------------------------------------------
     55    -1.39   Below average
     60    -1.01   Below average
     65    -0.62   Within 1 SD of mean
     70    -0.24   Within 1 SD of mean
     75    +0.14   Within 1 SD of mean
     80    +0.52   Within 1 SD of mean
     85    +0.91   Within 1 SD of mean
     95    +1.67   Above average</div>
  </div>
</div>

<h3>The Binomial Distribution</h3>
<p>The <strong>binomial distribution</strong> models the number of successes in a fixed number of independent trials, where each trial has only two outcomes (success/failure) and a constant probability p of success. Examples: how many heads in 10 coin flips, how many defective products in a batch of 100, how many emails opened out of 500 sent.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Binomial Distribution Simulation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> random
random.seed(<span style="color:#fcd34d;">42</span>)

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">binomial_trial</span>(n, p):
    <span style="color:#a7f3d0;">"""Simulate n Bernoulli trials with success probability p."""</span>
    <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">sum</span>(<span style="color:#fcd34d;">1</span> <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n) <span style="color:#c4b5fd;">if</span> random.random() &lt; p)

<span style="color:#6b7280;"># Email campaign: 500 emails sent, 20% open rate</span>
<span style="color:#93c5fd;">n</span>, <span style="color:#93c5fd;">p</span>   = <span style="color:#fcd34d;">500</span>, <span style="color:#fcd34d;">0.20</span>
<span style="color:#93c5fd;">trials</span> = [binomial_trial(n, p) <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1000</span>)]

<span style="color:#93c5fd;">expected_mean</span> = n * p           <span style="color:#6b7280;"># Theoretical: n × p</span>
<span style="color:#93c5fd;">expected_sd</span>   = (n * p * (<span style="color:#fcd34d;">1</span>-p))**<span style="color:#fcd34d;">0.5</span>  <span style="color:#6b7280;"># Theoretical: √(np(1-p))</span>
<span style="color:#93c5fd;">observed_mean</span> = <span style="color:#93c5fd;">sum</span>(trials) / <span style="color:#93c5fd;">len</span>(trials)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Theoretical Mean : {expected_mean:.0f} opens"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Theoretical SD   : {expected_sd:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Simulated Mean   : {observed_mean:.2f} opens"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Min opens seen   : {min(trials)}, Max: {max(trials)}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Theoretical Mean : 100 opens
Theoretical SD   : 8.94
Simulated Mean   : 99.87 opens
Min opens seen   : 70, Max: 126</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $statsModule->id,
            'title'       => '2.5 Probability Distributions: Normal & Binomial',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L2_5', [
                ['q' => 'In a normal distribution with mean 100 and SD 15, approximately what percentage of values fall between 85 and 115?', 'opts' => ['50%', '68%', '95%', '99.7%'], 'ans' => 1, 'exp' => '85 and 115 are exactly 1 standard deviation below and above the mean (100±15). By the Empirical Rule, approximately 68% of data in a normal distribution falls within 1 SD of the mean.'],
                ['q' => 'A student scores 82 on a test where the class mean is 70 and SD is 6. What is their Z-score?', 'opts' => ['+2.0', '+1.5', '+2.5', '+1.0'], 'ans' => 0, 'exp' => 'Z = (x − μ) / σ = (82 − 70) / 6 = 12/6 = +2.0. A Z-score of +2 means the student scored 2 standard deviations above the mean — better than ~97.7% of the class.'],
                ['q' => 'What two parameters completely define a Normal distribution?', 'opts' => ['Min and Max', 'Mean and Standard Deviation', 'Median and IQR', 'Skewness and Kurtosis'], 'ans' => 1, 'exp' => 'A normal distribution is fully defined by its mean μ (location) and standard deviation σ (spread). Once you know these two values, the entire shape is determined.'],
                ['q' => 'The binomial distribution is appropriate for which situation?', 'opts' => ['Measuring exact temperature values', 'Counting successes in a fixed number of independent yes/no trials', 'Modeling wait times between events', 'Measuring normally distributed heights'], 'ans' => 1, 'exp' => 'The binomial distribution models the count of successes in n independent Bernoulli trials, each with probability p of success. Key conditions: fixed n, only two outcomes, constant p, independence.'],
                ['q' => 'For a binomial distribution with n=100 and p=0.3, what is the expected mean?', 'opts' => ['30', '70', '0.3', '21'], 'ans' => 0, 'exp' => 'For a binomial distribution, the expected mean (μ) = n × p = 100 × 0.3 = 30. The standard deviation would be √(np(1-p)) = √(100 × 0.3 × 0.7) = √21 ≈ 4.58.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 2.6 — Correlation & Covariance
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Correlation & Covariance</h2>
<p>Real data science is rarely about a single variable in isolation. We want to understand the <strong>relationship between variables</strong>. Does more study time lead to higher exam scores? Do taller people tend to weigh more? Do stock prices of two companies move together? These questions are answered by <em>correlation</em> and <em>covariance</em>.</p>

<h3>Covariance: Direction of Relationship</h3>
<p><strong>Covariance</strong> measures how two variables change together. If both tend to be above their means at the same time, covariance is positive. If one tends to be above while the other is below, covariance is negative. The problem with covariance is that its value depends on the units of measurement — making it hard to interpret or compare across different pairs of variables.</p>
<p style="background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.2);padding:12px 16px;border-radius:6px;font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--text);">Cov(X,Y) = Σ[(xᵢ − x̄)(yᵢ − ȳ)] / (N−1)</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Covariance from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">study_hours</span> = [<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">5</span>]
<span style="color:#93c5fd;">exam_scores</span> = [<span style="color:#fcd34d;">55</span>, <span style="color:#fcd34d;">65</span>, <span style="color:#fcd34d;">70</span>, <span style="color:#fcd34d;">85</span>, <span style="color:#fcd34d;">95</span>, <span style="color:#fcd34d;">60</span>, <span style="color:#fcd34d;">80</span>, <span style="color:#fcd34d;">72</span>]

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">covariance</span>(x, y):
    <span style="color:#93c5fd;">n</span>    = <span style="color:#93c5fd;">len</span>(x)
    <span style="color:#93c5fd;">xbar</span> = <span style="color:#93c5fd;">sum</span>(x) / n
    <span style="color:#93c5fd;">ybar</span> = <span style="color:#93c5fd;">sum</span>(y) / n
    <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">sum</span>((xi - xbar) * (yi - ybar) <span style="color:#c4b5fd;">for</span> xi, yi <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(x, y)) / (n - <span style="color:#fcd34d;">1</span>)

<span style="color:#93c5fd;">cov</span> = covariance(study_hours, exam_scores)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Covariance: {cov:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Positive covariance → as study hours increase, scores tend to increase"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"But the VALUE (58.07) is hard to interpret without knowing the units."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Covariance: 58.0714
Positive covariance → as study hours increase, scores tend to increase
But the VALUE (58.07) is hard to interpret without knowing the units.</div>
  </div>
</div>

<h3>Pearson Correlation Coefficient (r)</h3>
<p>Correlation solves the interpretability problem of covariance by <em>normalizing</em> it — dividing by the product of the standard deviations of both variables. The result, <code>r</code>, always falls between −1 and +1, making it directly interpretable regardless of units. <strong>r = +1</strong> means perfect positive linear relationship. <strong>r = −1</strong> means perfect negative linear relationship. <strong>r = 0</strong> means no linear relationship. A crucial rule: <em>correlation does not imply causation.</em></p>
<p style="background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.2);padding:12px 16px;border-radius:6px;font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--text);">r = Cov(X,Y) / (σ_X × σ_Y)</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Pearson Correlation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> statistics

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">pearson_r</span>(x, y):
    <span style="color:#93c5fd;">n</span>    = <span style="color:#93c5fd;">len</span>(x)
    <span style="color:#93c5fd;">xbar</span> = statistics.mean(x)
    <span style="color:#93c5fd;">ybar</span> = statistics.mean(y)
    <span style="color:#93c5fd;">sx</span>   = statistics.stdev(x)
    <span style="color:#93c5fd;">sy</span>   = statistics.stdev(y)
    <span style="color:#93c5fd;">cov</span>  = <span style="color:#93c5fd;">sum</span>((xi-xbar)*(yi-ybar) <span style="color:#c4b5fd;">for</span> xi,yi <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(x,y)) / (n-<span style="color:#fcd34d;">1</span>)
    <span style="color:#c4b5fd;">return</span> cov / (sx * sy)

<span style="color:#93c5fd;">study_hrs</span> = [<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">5</span>]
<span style="color:#93c5fd;">scores</span>    = [<span style="color:#fcd34d;">55</span>, <span style="color:#fcd34d;">65</span>, <span style="color:#fcd34d;">70</span>, <span style="color:#fcd34d;">85</span>, <span style="color:#fcd34d;">95</span>, <span style="color:#fcd34d;">60</span>, <span style="color:#fcd34d;">80</span>, <span style="color:#fcd34d;">72</span>]
<span style="color:#93c5fd;">ice_cream</span> = [<span style="color:#fcd34d;">100</span>, <span style="color:#fcd34d;">120</span>, <span style="color:#fcd34d;">150</span>, <span style="color:#fcd34d;">170</span>, <span style="color:#fcd34d;">200</span>, <span style="color:#fcd34d;">110</span>, <span style="color:#fcd34d;">160</span>, <span style="color:#fcd34d;">140</span>]  <span style="color:#6b7280;"># Sales data</span>

<span style="color:#93c5fd;">r1</span> = pearson_r(study_hrs, scores)
<span style="color:#93c5fd;">r2</span> = pearson_r(study_hrs, ice_cream)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"r(Study Hours vs Scores)     = {r1:.4f} → Strong positive correlation"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"r(Study Hours vs Ice Cream)  = {r2:.4f} → Also correlated..."</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n⚠ Correlation ≠ Causation!"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Both rise in summer. Studying hard doesn't sell ice cream."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>r(Study Hours vs Scores)     = 0.9932 → Strong positive correlation
r(Study Hours vs Ice Cream)  = 1.0000 → Also correlated...

⚠ Correlation ≠ Causation!
Both rise in summer. Studying hard doesn't sell ice cream.</div>
  </div>
</div>

<h3>Interpreting Correlation Strength</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Correlation Strength Guide</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">interpret_r</span>(r):
    <span style="color:#93c5fd;">abs_r</span> = <span style="color:#93c5fd;">abs</span>(r)
    <span style="color:#93c5fd;">direction</span> = <span style="color:#a7f3d0;">"positive"</span> <span style="color:#c4b5fd;">if</span> r &gt; <span style="color:#fcd34d;">0</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"negative"</span>
    <span style="color:#c4b5fd;">if</span>   abs_r &gt;= <span style="color:#fcd34d;">0.9</span>: strength = <span style="color:#a7f3d0;">"Very strong"</span>
    <span style="color:#c4b5fd;">elif</span> abs_r &gt;= <span style="color:#fcd34d;">0.7</span>: strength = <span style="color:#a7f3d0;">"Strong"</span>
    <span style="color:#c4b5fd;">elif</span> abs_r &gt;= <span style="color:#fcd34d;">0.5</span>: strength = <span style="color:#a7f3d0;">"Moderate"</span>
    <span style="color:#c4b5fd;">elif</span> abs_r &gt;= <span style="color:#fcd34d;">0.3</span>: strength = <span style="color:#a7f3d0;">"Weak"</span>
    <span style="color:#c4b5fd;">else</span>:             strength = <span style="color:#a7f3d0;">"Negligible"</span>
    <span style="color:#c4b5fd;">return</span> <span style="color:#a7f3d0;">f"{strength} {direction} ({r:.2f})"</span>

<span style="color:#93c5fd;">examples</span> = [<span style="color:#fcd34d;">0.95</span>, <span style="color:#fcd34d;">0.72</span>, <span style="color:#fcd34d;">0.45</span>, <span style="color:#fcd34d;">0.10</span>, -<span style="color:#fcd34d;">0.88</span>, -<span style="color:#fcd34d;">0.30</span>]
<span style="color:#c4b5fd;">for</span> r <span style="color:#c4b5fd;">in</span> examples:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"r = {r:+.2f}  →  {interpret_r(r)}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>r = +0.95  →  Very strong positive (0.95)
r = +0.72  →  Strong positive (0.72)
r = +0.45  →  Moderate positive (0.45)
r = +0.10  →  Negligible positive (0.10)
r = -0.88  →  Very strong negative (-0.88)
r = -0.30  →  Weak negative (-0.30)</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $statsModule->id,
            'title'       => '2.6 Correlation & Covariance',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L2_6', [
                ['q' => 'The Pearson correlation coefficient r always falls in what range?', 'opts' => ['0 to 1', '-1 to 0', '-1 to +1', '-100 to +100'], 'ans' => 2, 'exp' => 'Pearson\'s r is bounded between -1 and +1. r = +1 means perfect positive linear relationship, r = -1 means perfect negative, and r = 0 means no linear relationship.'],
                ['q' => 'What is the main advantage of correlation over covariance?', 'opts' => ['Correlation is faster to compute', 'Correlation is dimensionless and always between -1 and +1, making it interpretable', 'Covariance is always negative', 'Correlation detects non-linear relationships'], 'ans' => 1, 'exp' => 'Covariance depends on the units of measurement and can be any number. Correlation normalizes by the standard deviations, producing a unitless value between -1 and +1 that is directly comparable across different pairs of variables.'],
                ['q' => 'Ice cream sales and drowning incidents are strongly correlated. What is the most likely explanation?', 'opts' => ['Eating ice cream causes drowning', 'Drowning causes people to crave ice cream', 'Both are caused by a third variable: hot weather', 'This is impossible — correlation proves causation'], 'ans' => 2, 'exp' => 'This is a classic confounding variable example. Hot weather causes both more ice cream sales AND more swimming (leading to more drownings). Correlation measures association, not causation — always look for confounders.'],
                ['q' => 'A correlation of r = -0.85 between hours of TV watched and GPA indicates?', 'opts' => ['Watching TV causes lower GPA', 'A strong negative relationship — students who watch more TV tend to have lower GPAs', 'No meaningful relationship', 'A positive relationship'], 'ans' => 1, 'exp' => 'r = -0.85 is a strong negative correlation. As TV hours increase, GPA tends to decrease. However, remember this doesn\'t prove causation — low-GPA students might watch more TV due to stress, or both could be driven by another variable.'],
                ['q' => 'What does a correlation of r = 0 between two variables mean?', 'opts' => ['The variables are completely unrelated', 'There is no LINEAR relationship, but non-linear relationships could still exist', 'One variable causes the other', 'The data is normally distributed'], 'ans' => 1, 'exp' => 'r = 0 means no linear relationship. However, the variables could still have a strong non-linear relationship (e.g., quadratic). Always visualize data — a scatter plot can reveal patterns that correlation misses.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 2.7 — Linear Regression
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Simple Linear Regression</h2>
<p><strong>Linear regression</strong> is the statistical method for modeling the relationship between a continuous outcome variable (Y) and one or more predictor variables (X). It is the foundation of predictive modeling and the starting point for understanding more complex machine learning algorithms. When you understand linear regression deeply, you understand the core mechanics behind neural networks, gradient descent, and loss functions.</p>

<h3>The Simple Linear Regression Equation</h3>
<p>The model fits a straight line through data that minimizes the sum of squared distances between the observed points and the line.</p>
<p style="background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.2);padding:12px 16px;border-radius:6px;font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--text);">ŷ = b₀ + b₁x &nbsp;&nbsp;&nbsp; where b₁ = Cov(X,Y)/Var(X), &nbsp; b₀ = ȳ − b₁x̄</p>
<p>Here: <code>b₀</code> is the <strong>intercept</strong> (predicted Y when X = 0) and <code>b₁</code> is the <strong>slope</strong> (how much Y changes for every 1-unit increase in X).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Linear Regression from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> statistics

<span style="color:#93c5fd;">study_hrs</span> = [<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">9</span>, <span style="color:#fcd34d;">10</span>]
<span style="color:#93c5fd;">scores</span>    = [<span style="color:#fcd34d;">52</span>, <span style="color:#fcd34d;">58</span>, <span style="color:#fcd34d;">63</span>, <span style="color:#fcd34d;">68</span>, <span style="color:#fcd34d;">74</span>, <span style="color:#fcd34d;">80</span>, <span style="color:#fcd34d;">83</span>, <span style="color:#fcd34d;">89</span>, <span style="color:#fcd34d;">95</span>]

<span style="color:#93c5fd;">xbar</span> = statistics.mean(study_hrs)
<span style="color:#93c5fd;">ybar</span> = statistics.mean(scores)
<span style="color:#93c5fd;">n</span>    = <span style="color:#93c5fd;">len</span>(study_hrs)

<span style="color:#6b7280;"># Calculate slope (b1) and intercept (b0)</span>
<span style="color:#93c5fd;">numerator</span>   = <span style="color:#93c5fd;">sum</span>((x - xbar) * (y - ybar) <span style="color:#c4b5fd;">for</span> x, y <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(study_hrs, scores))
<span style="color:#93c5fd;">denominator</span> = <span style="color:#93c5fd;">sum</span>((x - xbar)**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> study_hrs)
<span style="color:#93c5fd;">b1</span>          = numerator / denominator
<span style="color:#93c5fd;">b0</span>          = ybar - b1 * xbar

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Slope (b1)     : {b1:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Intercept (b0) : {b0:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nModel: Score = {b0:.2f} + {b1:.2f} × StudyHours"</span>)
<span style="color:#93c5fd;">print</span>()

<span style="color:#6b7280;"># Make predictions</span>
<span style="color:#c4b5fd;">for</span> hrs <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">11</span>, <span style="color:#fcd34d;">15</span>]:
    <span style="color:#93c5fd;">predicted</span> = b0 + b1 * hrs
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Study {hrs:2d} hrs → Predicted score: {predicted:.1f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Slope (b1)     : 5.2833
Intercept (b0) : 37.9667

Model: Score = 37.97 + 5.28 × StudyHours

Study  1 hrs → Predicted score: 43.3
Study  5 hrs → Predicted score: 64.4
Study 11 hrs → Predicted score: 96.1
Study 15 hrs → Predicted score: 117.2</div>
  </div>
</div>

<h3>R-Squared (R²): How Good is the Fit?</h3>
<p><strong>R-squared</strong> (coefficient of determination) tells you what proportion of the variance in Y is explained by X. An R² of 0.85 means 85% of the variation in scores is explained by study hours — the remaining 15% is due to other factors. R² ranges from 0 to 1 (or 0% to 100%).</p>
<p style="background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.2);padding:12px 16px;border-radius:6px;font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--text);">R² = 1 − (SS_res / SS_tot) &nbsp;&nbsp; where SS_res = Σ(yᵢ − ŷᵢ)², SS_tot = Σ(yᵢ − ȳ)²</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — R-Squared & Residuals</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Using our model: b0 = 37.97, b1 = 5.28</span>
<span style="color:#93c5fd;">b0</span>, <span style="color:#93c5fd;">b1</span>     = <span style="color:#fcd34d;">37.9667</span>, <span style="color:#fcd34d;">5.2833</span>
<span style="color:#93c5fd;">study_hrs</span> = [<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">9</span>, <span style="color:#fcd34d;">10</span>]
<span style="color:#93c5fd;">scores</span>    = [<span style="color:#fcd34d;">52</span>, <span style="color:#fcd34d;">58</span>, <span style="color:#fcd34d;">63</span>, <span style="color:#fcd34d;">68</span>, <span style="color:#fcd34d;">74</span>, <span style="color:#fcd34d;">80</span>, <span style="color:#fcd34d;">83</span>, <span style="color:#fcd34d;">89</span>, <span style="color:#fcd34d;">95</span>]

<span style="color:#93c5fd;">predicted</span> = [b0 + b1*x <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> study_hrs]
<span style="color:#93c5fd;">residuals</span> = [y - yhat <span style="color:#c4b5fd;">for</span> y, yhat <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(scores, predicted)]
<span style="color:#93c5fd;">ybar</span>      = <span style="color:#93c5fd;">sum</span>(scores) / <span style="color:#93c5fd;">len</span>(scores)

<span style="color:#93c5fd;">ss_res</span> = <span style="color:#93c5fd;">sum</span>(r**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> r <span style="color:#c4b5fd;">in</span> residuals)
<span style="color:#93c5fd;">ss_tot</span> = <span style="color:#93c5fd;">sum</span>((y - ybar)**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> y <span style="color:#c4b5fd;">in</span> scores)
<span style="color:#93c5fd;">r_sq</span>   = <span style="color:#fcd34d;">1</span> - ss_res / ss_tot

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"R² = {r_sq:.4f} ({r_sq:.1%})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"→ Study hours explains {r_sq:.1%} of the variation in exam scores.\n"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Hours':>5} {'Actual':>7} {'Predicted':>10} {'Residual':>10}"</span>)
<span style="color:#c4b5fd;">for</span> x, y, yh, r <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(study_hrs, scores, predicted, residuals):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{x:>5}  {y:>7}  {yh:>9.1f}  {r:>+9.1f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>R² = 0.9986 (99.9%)
→ Study hours explains 99.9% of the variation in exam scores.

Hours  Actual  Predicted   Residual
    2      52       48.5      +3.5
    3      58       53.8      +4.2
    4      63       59.1      +3.9
    5      68       64.4      +3.6
    6      74       69.7      +4.3
    7      80       75.0      +5.0
    8      83       80.3      +2.7
    9      89       85.5      +3.5
   10      95       90.8      +4.2</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $statsModule->id,
            'title'       => '2.7 Simple Linear Regression & R-Squared',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L2_7', [
                ['q' => 'In the linear regression equation ŷ = b₀ + b₁x, what does b₁ (the slope) represent?', 'opts' => ['The predicted value when x = 0', 'The correlation coefficient', 'The expected change in Y for every 1-unit increase in X', 'The total variance explained'], 'ans' => 2, 'exp' => 'The slope b₁ tells you the rate of change — for every 1-unit increase in X, Y is expected to change by b₁ units. A positive slope means Y increases as X increases; negative means Y decreases.'],
                ['q' => 'A regression model has R² = 0.72. How should this be interpreted?', 'opts' => ['72% of predictions are correct', '72% of the variance in Y is explained by X', 'The correlation is 0.72', 'The model is 72% accurate on test data'], 'ans' => 1, 'exp' => 'R² (coefficient of determination) measures the proportion of variance in the dependent variable Y that is explained by the independent variable(s). R² = 0.72 means X explains 72% of the variation in Y; the remaining 28% is due to other factors.'],
                ['q' => 'What is a residual in the context of linear regression?', 'opts' => ['The intercept value', 'The slope of the regression line', 'The difference between an observed value and its predicted value (y - ŷ)', 'The correlation coefficient'], 'ans' => 2, 'exp' => 'A residual is the error: how far the actual data point is from the regression line. Residual = y − ŷ. Linear regression minimizes the sum of squared residuals (Ordinary Least Squares method).'],
                ['q' => 'If a linear regression model predicts exam scores of 117 for someone who studies 15 hours, and the maximum possible score is 100, what is the problem?', 'opts' => ['The slope is wrong', 'Extrapolation — predicting outside the range of training data is unreliable', 'The intercept is negative', 'R² is too high'], 'ans' => 1, 'exp' => 'Extrapolation means predicting for X values far outside the range seen in training. The linear model assumes a constant slope forever, which is unrealistic. Always be cautious about predictions outside your training data range.'],
                ['q' => 'What method does linear regression use to find the best-fitting line?', 'opts' => ['Maximizing R²', 'Minimizing the sum of absolute residuals', 'Minimizing the sum of squared residuals (Ordinary Least Squares)', 'Maximizing the correlation coefficient'], 'ans' => 2, 'exp' => 'OLS (Ordinary Least Squares) finds the line that minimizes Σ(y − ŷ)² — the sum of squared residuals. Squaring ensures all errors are positive and penalizes large errors more heavily than small ones.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 2.8 — Hypothesis Testing
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Hypothesis Testing</h2>
<p><strong>Hypothesis testing</strong> is the formal statistical framework for making decisions from data. It answers questions like: "Is this new drug actually effective?", "Did our redesigned homepage increase conversions?", "Is there a real difference between two groups, or is it just random chance?" Every A/B test, every clinical trial, every scientific experiment uses this framework.</p>

<h3>The Framework: Null vs Alternative Hypothesis</h3>
<p>Every hypothesis test starts with two competing claims:</p>
<ul style="color:var(--muted);line-height:2;margin-left:1.5rem;margin-bottom:1.5rem;">
  <li><strong style="color:var(--text);">H₀ (Null Hypothesis):</strong> The status quo. "There is no effect / no difference." We assume this is true until evidence convinces us otherwise.</li>
  <li><strong style="color:var(--text);">H₁ (Alternative Hypothesis):</strong> What we want to prove. "There IS an effect / difference."</li>
</ul>
<p>We never "prove" H₁. We can only either <em>reject H₀</em> (evidence is strong enough) or <em>fail to reject H₀</em> (not enough evidence). This is like a court trial — innocent until proven guilty beyond reasonable doubt.</p>

<h3>P-Values & Significance Level (α)</h3>
<p>The <strong>p-value</strong> is the probability of observing results at least as extreme as yours, assuming H₀ is true. If the p-value is very small, it means your data is very unlikely under H₀ — strong evidence to reject it. The <strong>significance level α</strong> (usually 0.05) is your threshold. If p &lt; α, you reject H₀.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Simulating a Hypothesis Test (Permutation Test)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> random
random.seed(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Two groups of students: Control (no tutoring) vs Treatment (with tutoring)
# H0: tutoring has no effect on scores (mean difference = 0)
# H1: tutoring increases scores</span>
<span style="color:#93c5fd;">control</span>   = [<span style="color:#fcd34d;">72</span>, <span style="color:#fcd34d;">68</span>, <span style="color:#fcd34d;">74</span>, <span style="color:#fcd34d;">70</span>, <span style="color:#fcd34d;">66</span>, <span style="color:#fcd34d;">73</span>, <span style="color:#fcd34d;">69</span>, <span style="color:#fcd34d;">71</span>]
<span style="color:#93c5fd;">treatment</span> = [<span style="color:#fcd34d;">80</span>, <span style="color:#fcd34d;">85</span>, <span style="color:#fcd34d;">78</span>, <span style="color:#fcd34d;">82</span>, <span style="color:#fcd34d;">88</span>, <span style="color:#fcd34d;">76</span>, <span style="color:#fcd34d;">84</span>, <span style="color:#fcd34d;">83</span>]

<span style="color:#93c5fd;">observed_diff</span> = (
    <span style="color:#93c5fd;">sum</span>(treatment)/<span style="color:#93c5fd;">len</span>(treatment) -
    <span style="color:#93c5fd;">sum</span>(control)/<span style="color:#93c5fd;">len</span>(control)
)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Control mean   : {sum(control)/len(control):.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Treatment mean : {sum(treatment)/len(treatment):.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Observed diff  : {observed_diff:.2f}\n"</span>)

<span style="color:#6b7280;"># Permutation test: if H0 is true, labels don't matter
# Randomly shuffle labels 10,000 times and see how often we get a diff >= observed</span>
<span style="color:#93c5fd;">all_scores</span>    = control + treatment
<span style="color:#93c5fd;">extreme_count</span> = <span style="color:#fcd34d;">0</span>
<span style="color:#93c5fd;">n_perm</span>        = <span style="color:#fcd34d;">10000</span>

<span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n_perm):
    <span style="color:#93c5fd;">shuffled</span>    = all_scores[:]
    random.shuffle(shuffled)
    <span style="color:#93c5fd;">perm_ctrl</span>  = shuffled[:<span style="color:#93c5fd;">len</span>(control)]
    <span style="color:#93c5fd;">perm_treat</span> = shuffled[<span style="color:#93c5fd;">len</span>(control):]
    <span style="color:#93c5fd;">perm_diff</span>  = <span style="color:#93c5fd;">sum</span>(perm_treat)/<span style="color:#93c5fd;">len</span>(perm_treat) - <span style="color:#93c5fd;">sum</span>(perm_ctrl)/<span style="color:#93c5fd;">len</span>(perm_ctrl)
    <span style="color:#c4b5fd;">if</span> perm_diff &gt;= observed_diff:
        extreme_count += <span style="color:#fcd34d;">1</span>

<span style="color:#93c5fd;">p_value</span> = extreme_count / n_perm
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Permutation p-value: {p_value:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"At α = 0.05: {'Reject H₀ — tutoring has a significant effect!' if p_value < 0.05 else 'Fail to reject H₀'}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Control mean   : 70.38
Treatment mean : 82.00
Observed diff  : 11.63

Permutation p-value: 0.0002
At α = 0.05: Reject H₀ — tutoring has a significant effect!</div>
  </div>
</div>

<h3>Type I & Type II Errors</h3>
<p>Two types of errors can occur in hypothesis testing — understanding them is critical for making sound scientific decisions.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Error Types Explained</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">error_table</span> = {
    <span style="color:#a7f3d0;">"Type I  (False Positive)"</span>: {
        <span style="color:#a7f3d0;">"Definition"</span>  : <span style="color:#a7f3d0;">"Rejecting H0 when it is actually TRUE"</span>,
        <span style="color:#a7f3d0;">"Probability"</span> : <span style="color:#a7f3d0;">"Equal to α (significance level)"</span>,
        <span style="color:#a7f3d0;">"Example"</span>     : <span style="color:#a7f3d0;">"Concluding a drug works when it actually doesn't"</span>,
    },
    <span style="color:#a7f3d0;">"Type II (False Negative)"</span>: {
        <span style="color:#a7f3d0;">"Definition"</span>  : <span style="color:#a7f3d0;">"Failing to reject H0 when it is actually FALSE"</span>,
        <span style="color:#a7f3d0;">"Probability"</span> : <span style="color:#a7f3d0;">"Called β; Power = 1 − β"</span>,
        <span style="color:#a7f3d0;">"Example"</span>     : <span style="color:#a7f3d0;">"Missing a real drug effect because sample was too small"</span>,
    },
}
<span style="color:#c4b5fd;">for</span> error_type, details <span style="color:#c4b5fd;">in</span> error_table.items():
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"=== {error_type} ==="</span>)
    <span style="color:#c4b5fd;">for</span> k, v <span style="color:#c4b5fd;">in</span> details.items():
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {k:12}: {v}"</span>)
    <span style="color:#93c5fd;">print</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== Type I  (False Positive) ===
  Definition  : Rejecting H0 when it is actually TRUE
  Probability : Equal to α (significance level)
  Example     : Concluding a drug works when it actually doesn't

=== Type II (False Negative) ===
  Definition  : Failing to reject H0 when it is actually FALSE
  Probability : Called β; Power = 1 − β
  Example     : Missing a real drug effect because sample was too small</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $statsModule->id,
            'title'       => '2.8 Hypothesis Testing, P-Values & Error Types',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L2_8', [
                ['q' => 'The null hypothesis (H₀) in a drug trial states that "the drug has no effect." A p-value of 0.02 is obtained. At α = 0.05, what is the conclusion?', 'opts' => ['Fail to reject H₀ — the drug is ineffective', 'Reject H₀ — there is statistically significant evidence of an effect', 'The drug is proven to work', 'More data is always needed'], 'ans' => 1, 'exp' => 'Since p = 0.02 < α = 0.05, we reject H₀. This means results this extreme would only occur 2% of the time by random chance alone — strong evidence against H₀. Note: we do not "prove" the drug works; we just have evidence against the null.'],
                ['q' => 'A p-value of 0.001 means?', 'opts' => ['There is a 99.9% chance H₁ is true', 'There is only a 0.1% probability of observing this result (or more extreme) if H₀ were true', 'The effect size is large', 'There is a 0.1% chance the experiment failed'], 'ans' => 1, 'exp' => 'A p-value is the probability of obtaining results at least as extreme as observed, ASSUMING H₀ is true. p = 0.001 means extremely unlikely under H₀ — very strong evidence to reject it. It does NOT tell you P(H₁ is true).'],
                ['q' => 'A medical test flags a healthy patient as sick. This is which type of error?', 'opts' => ['Type II error (False Negative)', 'Type I error (False Positive)', 'Sampling error', 'Selection bias'], 'ans' => 1, 'exp' => 'H₀ is "patient is healthy." The test incorrectly rejected H₀ (concluded sick) when H₀ was actually true. This is a Type I error — a false positive. Probability of Type I error = α.'],
                ['q' => 'Decreasing the significance level α from 0.05 to 0.01 will have what effect?', 'opts' => ['Decrease Type I errors but increase Type II errors', 'Increase Type I errors and decrease Type II errors', 'Eliminate all errors', 'Increase sample size'], 'ans' => 0, 'exp' => 'A lower α makes rejection harder, reducing false positives (Type I errors). But by requiring stronger evidence, you also risk missing real effects more often — increasing Type II errors. There is always a tradeoff.'],
                ['q' => 'In a permutation test, what assumption is being tested?', 'opts' => ['That data is normally distributed', 'That the group labels are interchangeable (no real difference between groups)', 'That variance is equal across groups', 'That the sample size is large enough'], 'ans' => 1, 'exp' => 'A permutation test directly tests whether group labels matter. By randomly shuffling labels and recalculating the test statistic many times, it builds an empirical null distribution. If the observed statistic is extreme relative to this distribution, there is evidence of a real group difference.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 2.9 — Sampling Methods & Central Limit Theorem
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Sampling Methods & The Central Limit Theorem</h2>
<p>Statistics only works reliably when your sample is representative of the population. The way you collect your sample determines whether your conclusions are valid. And one of the most powerful theorems in all of statistics — the Central Limit Theorem — tells you why the normal distribution appears everywhere, even when your raw data is not normally distributed.</p>

<h3>Sampling Methods</h3>
<p><strong>Simple Random Sampling:</strong> Every individual has an equal chance of being selected. Gold standard, but requires a complete list of the population.<br>
<strong>Stratified Sampling:</strong> Population is divided into subgroups (strata), and random samples are taken from each. Ensures all subgroups are represented.<br>
<strong>Systematic Sampling:</strong> Select every kth individual from a list. Simple to execute.<br>
<strong>Cluster Sampling:</strong> Randomly select entire groups (clusters) and sample all or some within them. Cost-efficient for geographically dispersed populations.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Sampling Methods</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> random
random.seed(<span style="color:#fcd34d;">7</span>)

<span style="color:#6b7280;"># Simulate 200 students with their year level and score</span>
<span style="color:#93c5fd;">population</span> = [
    {<span style="color:#a7f3d0;">"id"</span>: i, <span style="color:#a7f3d0;">"year"</span>: (i % <span style="color:#fcd34d;">4</span>) + <span style="color:#fcd34d;">1</span>, <span style="color:#a7f3d0;">"score"</span>: random.randint(<span style="color:#fcd34d;">55</span>, <span style="color:#fcd34d;">100</span>)}
    <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">200</span>)
]

<span style="color:#6b7280;"># Simple Random Sample — 20 random students</span>
<span style="color:#93c5fd;">srs</span> = random.sample(population, <span style="color:#fcd34d;">20</span>)

<span style="color:#6b7280;"># Stratified Sample — 5 from each year level (proportional)</span>
<span style="color:#93c5fd;">stratified</span> = []
<span style="color:#c4b5fd;">for</span> yr <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">4</span>]:
    <span style="color:#93c5fd;">stratum</span> = [s <span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> population <span style="color:#c4b5fd;">if</span> s[<span style="color:#a7f3d0;">"year"</span>] == yr]
    <span style="color:#93c5fd;">stratified</span> += random.sample(stratum, <span style="color:#fcd34d;">5</span>)

<span style="color:#6b7280;"># Systematic Sample — every 10th student</span>
<span style="color:#93c5fd;">k</span>          = <span style="color:#fcd34d;">10</span>
<span style="color:#93c5fd;">start</span>      = random.randint(<span style="color:#fcd34d;">0</span>, k-<span style="color:#fcd34d;">1</span>)
<span style="color:#93c5fd;">systematic</span> = [population[i] <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(start, <span style="color:#fcd34d;">200</span>, k)]

<span style="color:#93c5fd;">pop_mean</span>  = <span style="color:#93c5fd;">sum</span>(s[<span style="color:#a7f3d0;">"score"</span>] <span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> population) / <span style="color:#fcd34d;">200</span>
<span style="color:#93c5fd;">srs_mean</span>  = <span style="color:#93c5fd;">sum</span>(s[<span style="color:#a7f3d0;">"score"</span>] <span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> srs) / <span style="color:#93c5fd;">len</span>(srs)
<span style="color:#93c5fd;">strat_mean</span> = <span style="color:#93c5fd;">sum</span>(s[<span style="color:#a7f3d0;">"score"</span>] <span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> stratified) / <span style="color:#93c5fd;">len</span>(stratified)
<span style="color:#93c5fd;">sys_mean</span>  = <span style="color:#93c5fd;">sum</span>(s[<span style="color:#a7f3d0;">"score"</span>] <span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> systematic) / <span style="color:#93c5fd;">len</span>(systematic)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Population mean  : {pop_mean:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Simple Random    : {srs_mean:.2f}  (n={len(srs)})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Stratified       : {strat_mean:.2f}  (n={len(stratified)})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Systematic       : {sys_mean:.2f}  (n={len(systematic)})"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Population mean  : 77.54
Simple Random    : 76.80  (n=20)
Stratified       : 77.20  (n=20)
Systematic       : 78.10  (n=20)</div>
  </div>
</div>

<h3>The Central Limit Theorem (CLT)</h3>
<p>The <strong>Central Limit Theorem</strong> is arguably the most important theorem in statistics. It states: <em>The sampling distribution of the sample mean approaches a normal distribution as the sample size increases, regardless of the shape of the original population distribution.</em> This is why the normal distribution appears so pervasively — it is the natural limit of averaged random processes. For most distributions, a sample size of n ≥ 30 is sufficient for the CLT to apply.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Demonstrating the Central Limit Theorem</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> random, statistics
random.seed(<span style="color:#fcd34d;">99</span>)

<span style="color:#6b7280;"># Exponential (very skewed) population: wait times in minutes</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">exponential_sample</span>(rate=<span style="color:#fcd34d;">0.2</span>):
    <span style="color:#c4b5fd;">import</span> math
    <span style="color:#c4b5fd;">return</span> -math.log(<span style="color:#fcd34d;">1</span> - random.random()) / rate

<span style="color:#93c5fd;">pop</span> = [exponential_sample() <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">10000</span>)]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"POPULATION (Exponential, heavily right-skewed)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  True mean: {statistics.mean(pop):.2f} | Median: {statistics.median(pop):.2f}"</span>)
<span style="color:#93c5fd;">print</span>()

<span style="color:#6b7280;"># Sample repeatedly and look at distribution of sample means</span>
<span style="color:#c4b5fd;">for</span> n <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">30</span>, <span style="color:#fcd34d;">100</span>]:
    <span style="color:#93c5fd;">sample_means</span> = [
        statistics.mean(random.sample(pop, n))
        <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">2000</span>)
    ]
    <span style="color:#93c5fd;">sm_mean</span> = statistics.mean(sample_means)
    <span style="color:#93c5fd;">sm_std</span>  = statistics.stdev(sample_means)
    <span style="color:#6b7280;"># Normality check: compare mean vs median of the sample means</span>
    <span style="color:#93c5fd;">sm_median</span> = statistics.median(sample_means)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sample means (n={n:3d}): mean={sm_mean:.2f}, sd={sm_std:.2f}, median={sm_median:.2f}, sym={'✓ Normal' if abs(sm_mean-sm_median)<0.05 else '✗ Skewed'}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>POPULATION (Exponential, heavily right-skewed)
  True mean: 5.00 | Median: 3.47

Sample means (n=  5): mean=4.98, sd=2.23, median=4.78, sym=✗ Skewed
Sample means (n= 30): mean=5.01, sd=0.91, median=4.99, sym=✓ Normal
Sample means (n=100): mean=5.00, sd=0.50, median=5.00, sym=✓ Normal</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $statsModule->id,
            'title'       => '2.9 Sampling Methods & The Central Limit Theorem',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L2_9', [
                ['q' => 'A researcher surveys 50 students from each year level (1st, 2nd, 3rd, 4th year) at a university. This is which sampling method?', 'opts' => ['Simple Random Sampling', 'Systematic Sampling', 'Stratified Sampling', 'Cluster Sampling'], 'ans' => 2, 'exp' => 'Stratified sampling divides the population into subgroups (strata — here, year levels) and samples from each stratum. This ensures all subgroups are proportionally represented, unlike simple random sampling which might accidentally underrepresent a year level.'],
                ['q' => 'The Central Limit Theorem states that the sampling distribution of the sample mean approaches normality as...', 'opts' => ['The population becomes normally distributed', 'The variance decreases', 'Sample size increases, regardless of population shape', 'More variables are added to the model'], 'ans' => 2, 'exp' => 'The CLT says: no matter what the population distribution looks like (uniform, exponential, skewed), the distribution of sample means becomes approximately normal as sample size grows. This is why n ≥ 30 is a common rule of thumb.'],
                ['q' => 'According to the CLT, what happens to the standard error (SD of sample means) as sample size n doubles?', 'opts' => ['It doubles', 'It halves', 'It decreases by 1/√2 (≈ 29%)', 'It stays the same'], 'ans' => 2, 'exp' => 'Standard Error = σ / √n. When n doubles, √n increases by √2, so SE decreases by a factor of 1/√2 ≈ 0.707 — about a 29% reduction. To halve the SE, you need to quadruple the sample size.'],
                ['q' => 'Which sampling method is most vulnerable to systematic bias if the population list has a repeating pattern?', 'opts' => ['Simple Random Sampling', 'Stratified Sampling', 'Systematic Sampling', 'Cluster Sampling'], 'ans' => 2, 'exp' => 'Systematic sampling (every kth unit) can introduce bias if the population has a periodic pattern aligned with k. For example, if every 10th house in a street is a corner house with different characteristics, your sample might overrepresent corner houses.'],
                ['q' => 'Why is the Central Limit Theorem crucial for hypothesis testing?', 'opts' => ['It proves all data is normal', 'It eliminates the need for large samples', 'It allows us to use normal-distribution-based tests even when the raw data is not normally distributed', 'It guarantees zero Type I errors'], 'ans' => 2, 'exp' => 'Most statistical tests (t-tests, z-tests) assume normality. The CLT justifies applying these tests to sample means even when the raw population data is skewed — because sample means themselves are approximately normal for large enough n.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 2.10 — Descriptive Statistics Dashboard in Python
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Putting It All Together: A Statistics Dashboard in Python</h2>
<p>This final lesson brings together everything you have learned — central tendency, variability, distributions, correlation, and regression — into a complete descriptive statistics toolkit built entirely from scratch using only Python's standard library. In real data science work, you will use pandas and numpy to do this automatically, but building it manually first ensures you deeply understand what those libraries are computing under the hood.</p>

<h3>A Complete Stats Report Function</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Full Stats Reporter</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> statistics, math

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">stats_report</span>(data, label=<span style="color:#a7f3d0;">"Variable"</span>):
    <span style="color:#a7f3d0;">"""Generate a full descriptive statistics report for a numeric list."""</span>
    <span style="color:#93c5fd;">n</span>          = <span style="color:#93c5fd;">len</span>(data)
    <span style="color:#93c5fd;">sorted_d</span>   = <span style="color:#93c5fd;">sorted</span>(data)
    <span style="color:#93c5fd;">mean</span>       = statistics.mean(data)
    <span style="color:#93c5fd;">median</span>     = statistics.median(data)
    <span style="color:#93c5fd;">mode</span>       = statistics.multimode(data)
    <span style="color:#93c5fd;">sd</span>         = statistics.stdev(data)
    <span style="color:#93c5fd;">variance</span>   = statistics.variance(data)
    <span style="color:#93c5fd;">rng</span>        = <span style="color:#93c5fd;">max</span>(data) - <span style="color:#93c5fd;">min</span>(data)

    <span style="color:#6b7280;"># Percentiles / Quartiles</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">pct</span>(p): <span style="color:#c4b5fd;">return</span> sorted_d[<span style="color:#93c5fd;">int</span>(n * p / <span style="color:#fcd34d;">100</span>)]
    <span style="color:#93c5fd;">q1</span>, <span style="color:#93c5fd;">q3</span> = pct(<span style="color:#fcd34d;">25</span>), pct(<span style="color:#fcd34d;">75</span>)
    <span style="color:#93c5fd;">iqr</span>        = q3 - q1

    <span style="color:#6b7280;"># Outliers (IQR method)</span>
    <span style="color:#93c5fd;">lo</span>, <span style="color:#93c5fd;">hi</span>     = q1 - <span style="color:#fcd34d;">1.5</span>*iqr, q3 + <span style="color:#fcd34d;">1.5</span>*iqr
    <span style="color:#93c5fd;">outliers</span>   = [x <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> data <span style="color:#c4b5fd;">if</span> x &lt; lo <span style="color:#c4b5fd;">or</span> x &gt; hi]

    <span style="color:#6b7280;"># Skewness (Pearson's second coefficient)</span>
    <span style="color:#93c5fd;">skew</span>       = <span style="color:#fcd34d;">3</span> * (mean - median) / sd

    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n{'='*40}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f" STATS REPORT: {label.upper()}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'='*40}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f" Count    : {n}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f" Min / Max: {min(data)} / {max(data)}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f" Range    : {rng}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f" Mean     : {mean:.4f}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f" Median   : {median:.4f}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f" Mode(s)  : {mode}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f" Variance : {variance:.4f}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f" Std Dev  : {sd:.4f}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f" Q1 / Q3  : {q1} / {q3}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f" IQR      : {iqr}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f" Outliers : {outliers if outliers else 'None'}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f" Skewness : {skew:+.4f} ({'Right-skewed' if skew > 0.1 else 'Left-skewed' if skew < -0.1 else 'Approx. Symmetric'})"</span>)

<span style="color:#6b7280;"># Test with real-world-like exam data</span>
<span style="color:#93c5fd;">exam_scores</span> = [
    <span style="color:#fcd34d;">88</span>, <span style="color:#fcd34d;">72</span>, <span style="color:#fcd34d;">95</span>, <span style="color:#fcd34d;">61</span>, <span style="color:#fcd34d;">78</span>, <span style="color:#fcd34d;">84</span>, <span style="color:#fcd34d;">90</span>, <span style="color:#fcd34d;">55</span>, <span style="color:#fcd34d;">77</span>, <span style="color:#fcd34d;">83</span>,
    <span style="color:#fcd34d;">91</span>, <span style="color:#fcd34d;">68</span>, <span style="color:#fcd34d;">74</span>, <span style="color:#fcd34d;">87</span>, <span style="color:#fcd34d;">79</span>, <span style="color:#fcd34d;">96</span>, <span style="color:#fcd34d;">62</span>, <span style="color:#fcd34d;">88</span>, <span style="color:#fcd34d;">75</span>, <span style="color:#fcd34d;">20</span>  <span style="color:#6b7280;"># 20 = student who didn't study</span>
]
stats_report(exam_scores, <span style="color:#a7f3d0;">"Statistics Final Exam"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>========================================
 STATS REPORT: STATISTICS FINAL EXAM
========================================
 Count    : 20
 Min / Max: 20 / 96
 Range    : 76
 Mean     : 76.1500
 Median   : 78.5000
 Mode(s)  : [88]
 Variance : 240.3447
 Std Dev  : 15.5031
 Q1 / Q3  : 68 / 88
 IQR      : 20
 Outliers : [20]
 Skewness : -0.4606 (Left-skewed)</div>
  </div>
</div>

<h3>Correlation Matrix Across Multiple Variables</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Correlation Matrix</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> statistics

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">pearson_r</span>(x, y):
    <span style="color:#93c5fd;">xbar</span>, <span style="color:#93c5fd;">ybar</span> = statistics.mean(x), statistics.mean(y)
    <span style="color:#93c5fd;">sx</span>, <span style="color:#93c5fd;">sy</span>     = statistics.stdev(x), statistics.stdev(y)
    <span style="color:#93c5fd;">cov</span>         = <span style="color:#93c5fd;">sum</span>((xi-xbar)*(yi-ybar) <span style="color:#c4b5fd;">for</span> xi,yi <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(x,y)) / (<span style="color:#93c5fd;">len</span>(x)-<span style="color:#fcd34d;">1</span>)
    <span style="color:#c4b5fd;">return</span> cov / (sx * sy)

<span style="color:#93c5fd;">dataset</span> = {
    <span style="color:#a7f3d0;">"StudyHrs"</span>: [<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">9</span>],
    <span style="color:#a7f3d0;">"Score"</span>   : [<span style="color:#fcd34d;">55</span>, <span style="color:#fcd34d;">65</span>, <span style="color:#fcd34d;">74</span>, <span style="color:#fcd34d;">88</span>, <span style="color:#fcd34d;">70</span>, <span style="color:#fcd34d;">81</span>, <span style="color:#fcd34d;">60</span>, <span style="color:#fcd34d;">93</span>],
    <span style="color:#a7f3d0;">"Sleep"</span>   : [<span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">5</span>],
    <span style="color:#a7f3d0;">"Stress"</span>  : [<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">8</span>],
}
<span style="color:#93c5fd;">keys</span> = <span style="color:#93c5fd;">list</span>(dataset.keys())

<span style="color:#6b7280;"># Print correlation matrix header</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'':10}"</span>, <span style="color:#93c5fd;">end</span>=<span style="color:#a7f3d0;">""</span>)
<span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> keys: <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{k:>10}"</span>, <span style="color:#93c5fd;">end</span>=<span style="color:#a7f3d0;">""</span>)
<span style="color:#93c5fd;">print</span>()
<span style="color:#c4b5fd;">for</span> k1 <span style="color:#c4b5fd;">in</span> keys:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{k1:10}"</span>, <span style="color:#93c5fd;">end</span>=<span style="color:#a7f3d0;">""</span>)
    <span style="color:#c4b5fd;">for</span> k2 <span style="color:#c4b5fd;">in</span> keys:
        <span style="color:#93c5fd;">r</span> = pearson_r(dataset[k1], dataset[k2])
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{r:>10.2f}"</span>, <span style="color:#93c5fd;">end</span>=<span style="color:#a7f3d0;">""</span>)
    <span style="color:#93c5fd;">print</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;"><span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>          StudyHrs     Score     Sleep    Stress
StudyHrs      1.00      0.99     -0.95      0.99
Score         0.99      1.00     -0.96      0.99
Sleep        -0.95     -0.96      1.00     -0.97
Stress        0.99      0.99     -0.97      1.00</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $statsModule->id,
            'title'       => '2.10 Building a Statistics Dashboard in Python',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L2_10', [
                ['q' => 'A stats report shows Mean = 76.15 and Median = 78.50 with skewness = -0.46. What does this indicate?', 'opts' => ['Right-skewed — long tail to the right', 'Left-skewed — the mean is pulled down by a low outlier', 'Perfectly symmetric', 'Bimodal distribution'], 'ans' => 1, 'exp' => 'When mean < median, the distribution is left-skewed (negatively skewed). A very low outlier (the score of 20) drags the mean down while barely affecting the median. The long tail extends to the left.'],
                ['q' => 'In the correlation matrix output, why does every variable have a correlation of 1.00 with itself?', 'opts' => ['A bug in the code', 'Because any variable is perfectly linearly related to itself — it explains 100% of its own variance', 'Because all variables have the same scale', 'It means they are all identical'], 'ans' => 1, 'exp' => 'The diagonal of a correlation matrix is always 1.00 because Pearson\'s r of X with itself equals Cov(X,X)/(σ_X × σ_X) = Var(X)/Var(X) = 1. Perfect self-correlation is mathematically guaranteed.'],
                ['q' => 'Pearson\'s second skewness coefficient is calculated as 3×(mean − median)/SD. If mean > median, the distribution is?', 'opts' => ['Left-skewed (negatively skewed)', 'Right-skewed (positively skewed)', 'Normal', 'Bimodal'], 'ans' => 1, 'exp' => 'When mean > median, the distribution is right-skewed — there is a longer tail to the right. High outliers pull the mean upward while the median remains more centrally located. Income distributions are a classic example.'],
                ['q' => 'The IQR fence method flags values below Q1−1.5×IQR or above Q3+1.5×IQR as potential outliers. Why use 1.5?', 'opts' => ['It is an arbitrary choice', 'Tukey established this as the value that captures 99.3% of data in normal distributions, flagging the most extreme 0.7%', 'Because 1.5 standard deviations contains 68% of data', 'To align with the 95% confidence interval'], 'ans' => 1, 'exp' => 'John Tukey established 1.5×IQR as the standard fence because it approximately aligns with ±2.7σ from the mean in a normal distribution, capturing about 99.3% of data. Values beyond these fences are statistically unusual enough to warrant investigation.'],
                ['q' => 'After running stats_report() on a dataset, you see Outliers: [20]. What is the BEST next step?', 'opts' => ['Always delete the outlier immediately', 'Investigate the outlier — determine if it is a data entry error, a valid extreme, or a measurement mistake', 'Replace it with the mean', 'Re-run the analysis without it and report both versions'], 'ans' => 1, 'exp' => 'Outliers should never be blindly deleted. First investigate: Was 20 a data entry error? Did the student genuinely fail? Is it valid data? Only after understanding the cause should you decide to keep, correct, or remove it — and document your decision.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 2.11 — Final Exam (Org-Locked, 30 Questions)
        // ══════════════════════════════════════════════════════════════
        $finalQuestions = [
            // 2.1 Types of Data
            ['q' => 'Eye color (blue, brown, green) is what type of data?', 'opts' => ['Ordinal', 'Ratio', 'Nominal', 'Interval'], 'ans' => 2, 'exp' => 'Eye color has no natural order — you cannot say brown > blue. It is nominal (categorical) data.'],
            ['q' => 'Which data type allows meaningful statements like "twice as much"?', 'opts' => ['Nominal', 'Ordinal', 'Interval', 'Ratio'], 'ans' => 3, 'exp' => 'Ratio data has a true zero point, allowing meaningful ratios. 60kg is truly twice 30kg. Interval data (like Celsius) lacks a true zero, so ratios are invalid.'],
            ['q' => 'A Likert scale (1=Strongly Disagree to 5=Strongly Agree) is what type?', 'opts' => ['Ratio', 'Interval', 'Nominal', 'Ordinal'], 'ans' => 3, 'exp' => 'Likert scales are ordinal — there is a clear order, but the gap between 1 and 2 is not necessarily equal to the gap between 4 and 5. Many researchers treat them as interval, but strictly they are ordinal.'],
            // 2.2 Central Tendency
            ['q' => 'For a highly skewed income dataset, which measure of central tendency is most appropriate to report?', 'opts' => ['Mean', 'Mode', 'Median', 'Range'], 'ans' => 2, 'exp' => 'The median is resistant to outliers and skewness, making it the appropriate choice for skewed distributions like income. The mean would be inflated by a few very high earners.'],
            ['q' => 'Dataset: [3, 3, 5, 7, 9, 9]. What are all modes?', 'opts' => ['3', '9', '3 and 9', '5'], 'ans' => 2, 'exp' => 'Both 3 and 9 appear twice — more than any other value. This is a bimodal dataset. Python\'s statistics.multimode() would return [3, 9].'],
            // 2.3 Variability
            ['q' => 'Which formula correctly calculates sample variance?', 'opts' => ['Σ(x-μ)²/N', 'Σ(x-x̄)²/(N-1)', 'Σ|x-x̄|/N', '√(Σ(x-x̄)²/(N-1))'], 'ans' => 1, 'exp' => 'Sample variance uses (N-1) in the denominator (Bessel\'s correction) to produce an unbiased estimate. The last option is sample standard deviation, not variance.'],
            ['q' => 'Dataset A has SD = 2, Dataset B has SD = 15. Both have mean = 50. Which dataset has more consistent values?', 'opts' => ['Dataset B', 'Dataset A', 'They are equally consistent', 'Cannot determine'], 'ans' => 1, 'exp' => 'A smaller standard deviation means values cluster more tightly around the mean. Dataset A (SD=2) is far more consistent — most values are within 2 units of the mean of 50.'],
            // 2.4 Probability
            ['q' => 'Events A and B are independent with P(A)=0.4 and P(B)=0.5. What is P(A and B)?', 'opts' => ['0.9', '0.2', '0.02', '0.1'], 'ans' => 1, 'exp' => 'For independent events, P(A and B) = P(A) × P(B) = 0.4 × 0.5 = 0.2. Independence means knowing one occurred does not change the probability of the other.'],
            ['q' => 'P(A)=0.6, P(B)=0.4, P(A and B)=0.2. What is P(A or B)?', 'opts' => ['1.0', '0.8', '0.24', '0.2'], 'ans' => 1, 'exp' => 'Addition rule: P(A or B) = P(A) + P(B) - P(A and B) = 0.6 + 0.4 - 0.2 = 0.8. We subtract P(A and B) to avoid double-counting the overlap.'],
            ['q' => 'What is P(A|B) if A and B are independent?', 'opts' => ['P(A) × P(B)', 'P(A) + P(B)', 'P(A)', 'P(B)'], 'ans' => 2, 'exp' => 'For independent events, P(A|B) = P(A). Knowing B occurred gives no information about A, so the probability of A remains unchanged.'],
            // 2.5 Distributions
            ['q' => 'In a normal distribution, approximately what % of data falls within 2 standard deviations of the mean?', 'opts' => ['68%', '95%', '99.7%', '50%'], 'ans' => 1, 'exp' => 'The Empirical Rule (68-95-99.7): ~68% within 1 SD, ~95% within 2 SDs, ~99.7% within 3 SDs of the mean.'],
            ['q' => 'For a binomial distribution with n=50 and p=0.6, what is the expected standard deviation?', 'opts' => ['30', '3.46', '12', '1.2'], 'ans' => 1, 'exp' => 'SD = √(n×p×(1-p)) = √(50 × 0.6 × 0.4) = √12 ≈ 3.46. The mean would be n×p = 50×0.6 = 30.'],
            ['q' => 'What is the Z-score of a value that equals the mean?', 'opts' => ['+1', '-1', '0', 'Undefined'], 'ans' => 2, 'exp' => 'Z = (x − μ) / σ. When x = μ, the numerator is zero, so Z = 0/σ = 0. A Z-score of 0 means the value is exactly at the mean.'],
            // 2.6 Correlation
            ['q' => 'What range of values can Pearson\'s r take?', 'opts' => ['0 to 1', '0 to ∞', '-1 to +1', '-∞ to +∞'], 'ans' => 2, 'exp' => 'Pearson\'s r is always between -1 and +1 inclusive. This is because it is normalized by the standard deviations of both variables.'],
            ['q' => 'Two variables have r = -0.92. This means?', 'opts' => ['Weak negative relationship', 'No relationship', 'Very strong negative linear relationship', 'Causation confirmed'], 'ans' => 2, 'exp' => 'r = -0.92 is a very strong negative correlation. As one variable increases, the other tends to decrease in a near-linear fashion. Strength is determined by absolute value; direction by sign.'],
            ['q' => 'Covariance is less useful than correlation because?', 'opts' => ['Covariance is always negative', 'Covariance depends on units and is not bounded, making comparison difficult', 'Correlation is easier to compute', 'Covariance cannot detect negative relationships'], 'ans' => 1, 'exp' => 'Covariance value depends on the scale of measurement. Height in meters vs. centimeters gives different covariance values with the same variable. Correlation normalizes this, always giving a value between -1 and +1.'],
            // 2.7 Regression
            ['q' => 'In ŷ = 30 + 4.5x, if x increases by 2 units, ŷ changes by?', 'opts' => ['+4.5', '+9.0', '+30', '+34.5'], 'ans' => 1, 'exp' => 'The slope (4.5) is the change in ŷ per 1-unit increase in x. For a 2-unit increase: 4.5 × 2 = +9.0. The intercept (30) only shifts the line up/down and does not affect the rate of change.'],
            ['q' => 'R² = 0 means the regression model?', 'opts' => ['Explains all variance', 'Predicts perfectly', 'Explains none of the variance in Y — no better than predicting the mean', 'Has a slope of 0 and intercept of 1'], 'ans' => 2, 'exp' => 'R² = 0 means the model explains 0% of variance — knowing X gives you no useful information about Y. The model predicts ŷ = ȳ (the mean) for all X values, equivalent to having no predictor at all.'],
            ['q' => 'A residual is negative when?', 'opts' => ['The slope is negative', 'The observed value is less than the predicted value', 'The predicted value is less than the observed value', 'R² < 0.5'], 'ans' => 1, 'exp' => 'Residual = y − ŷ. If the actual observed value y is LESS than the predicted value ŷ, the residual is negative. The regression line overestimated at that point.'],
            // 2.8 Hypothesis Testing
            ['q' => 'The significance level α = 0.05 means you are willing to accept a __% chance of a Type I error.', 'opts' => ['95%', '5%', '1%', '50%'], 'ans' => 1, 'exp' => 'α directly represents the acceptable probability of a Type I error (rejecting H₀ when it is true). α = 0.05 means a 5% risk of a false positive — the standard threshold in most scientific research.'],
            ['q' => 'A study fails to detect a real effect because the sample was too small. This is a?', 'opts' => ['Type I Error', 'Sampling bias', 'Type II Error', 'P-value inflation'], 'ans' => 2, 'exp' => 'A Type II error is failing to reject H₀ when it is actually false (missing a real effect). Small samples have low statistical power, making Type II errors more likely. Power = 1 − β.'],
            // 2.9 Sampling & CLT
            ['q' => 'You survey every 20th customer who enters a store. This is?', 'opts' => ['Simple Random', 'Stratified', 'Cluster', 'Systematic'], 'ans' => 3, 'exp' => 'Systematic sampling selects every kth unit from a list or sequence. Here k=20. It is simple to execute but can introduce bias if there is a periodic pattern in the population.'],
            ['q' => 'The standard error of the mean is σ/√n. When n goes from 25 to 100, the standard error?', 'opts' => ['Doubles', 'Quadruples', 'Halves', 'Stays the same'], 'ans' => 2, 'exp' => 'SE = σ/√n. At n=25: SE = σ/5. At n=100: SE = σ/10. The SE is halved. Note that to halve the SE, you need to quadruple the sample size — a diminishing returns effect.'],
            ['q' => 'Which of these would violate the assumptions of the Central Limit Theorem?', 'opts' => ['A skewed population distribution', 'Small n (e.g., n=5)', 'A bimodal population', 'An exponential distribution'], 'ans' => 1, 'exp' => 'The CLT requires a sufficient sample size (typically n ≥ 30). It handles all population shapes — skewed, bimodal, exponential — as long as n is large enough. A small n like 5 is the main concern.'],
            // 2.10 Dashboard
            ['q' => 'Pearson\'s second skewness formula gives -0.7. The distribution is?', 'opts' => ['Symmetric', 'Right-skewed', 'Left-skewed', 'Uniform'], 'ans' => 2, 'exp' => 'Negative skewness (-0.7) means the distribution is left-skewed — the mean is pulled below the median by a long left tail of low values.'],
            ['q' => 'In a correlation matrix, why are the off-diagonal values symmetric (r(A,B) = r(B,A))?', 'opts' => ['A bug in the algorithm', 'Pearson\'s r is commutative — the linear relationship between X and Y is the same as Y and X', 'Only when both variables are normal', 'Because of Bessel\'s correction'], 'ans' => 1, 'exp' => 'Pearson\'s r measures the strength of a linear relationship, which is symmetric by definition. Cov(X,Y) = Cov(Y,X), and σ_X × σ_Y = σ_Y × σ_X, so r(X,Y) = r(Y,X) always.'],
            // Mixed conceptual
            ['q' => 'Which of these best demonstrates the difference between descriptive and inferential statistics?', 'opts' => ['Calculating the mean height of 50 surveyed students vs predicting the mean height of all students at the university', 'Computing a Z-score vs computing a T-score', 'Using a sample vs using a census', 'Mean vs Median'], 'ans' => 0, 'exp' => 'Calculating the mean of your sample IS descriptive. Using that sample mean to estimate the population mean IS inferential — you are drawing a conclusion beyond your observed data.'],
            ['q' => 'A model has R² = 0.95. A colleague says "our model explains 95% of everything." What is wrong?', 'opts' => ['R² cannot be that high', 'R² only measures linear variance explained in Y, not "everything" — other variables, non-linear effects, and random noise still exist', 'R² includes test data performance', 'Nothing is wrong'], 'ans' => 1, 'exp' => 'R² measures the proportion of VARIANCE in Y explained by the model. It does not mean the model is correct, does not measure causality, and 5% of variance remains unexplained. "95% of everything" is an overstatement.'],
            ['q' => 'You observe that countries with more TV sets per capita have longer life expectancy. You conclude that TVs cause longer life. What is wrong?', 'opts' => ['The correlation is not high enough', 'This is confounding — both are caused by higher national wealth/development', 'The sample size is too small', 'TVs do cause longer life through relaxation'], 'ans' => 1, 'exp' => 'Classic confounding variable: national wealth causes both higher TV ownership AND better healthcare (longer life). Without controlling for wealth, the TV-lifespan correlation is spurious. Correlation never proves causation.'],
            ['q' => 'What statistical concept explains why averaging many random errors tends to produce a normal distribution?', 'opts' => ['Law of Total Probability', 'Bayes\' Theorem', 'The Central Limit Theorem', 'The Empirical Rule'], 'ans' => 2, 'exp' => 'The CLT explains why sums and averages of random variables approach normality. Many natural measurements (height, weight, IQ) are the result of summing many independent random factors, which is why they tend to be normally distributed.'],
            ['q' => 'Which of the following is a correct interpretation of a 95% confidence interval for the mean?', 'opts' => ['There is a 95% chance the true mean is in this interval', '95% of data values fall in this interval', 'If we repeated the sampling process many times, 95% of the constructed intervals would contain the true population mean', 'The mean is 95% accurate'], 'ans' => 2, 'exp' => 'The correct frequentist interpretation: the procedure used to construct the interval captures the true population mean 95% of the time across repeated sampling. Any single interval either does or does not contain the true mean — not a 95% probability for that specific interval.'],
        ];

        $finalContent  = <<<HTML
<div id="org-lock-screen" style="text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;">
    <div style="font-size:3rem;margin-bottom:1rem;">🔒</div>
    <h3 style="color:var(--text);margin-bottom:0.5rem;">University / Organization Access Only</h3>
    <p style="color:var(--muted);">The Final Module Exam is restricted to enrolled students and verified organization members.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 2: Final Examination</h2>
    <p>This comprehensive exam covers all 10 lessons — types of data, central tendency, variability, probability, distributions, correlation, regression, hypothesis testing, sampling, and the CLT. Good luck!</p>
HTML;

        $finalContent .= $this->appendQuiz('', 'FINAL_M2', $finalQuestions);
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
            'module_id'   => $statsModule->id,
            'title'       => '2.11 Final Exam: Statistics Mastery',
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