<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module8LessonsSeeder
 * Seeds lessons for Module 8: Statistical Methods & Experimental Design.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module8LessonsSeeder
 *
 * Lessons:
 * 8.1  — Introduction to Statistical Thinking
 * 8.2  — Descriptive Statistics & Data Summarization
 * 8.3  — Probability Distributions in Practice
 * 8.4  — Sampling Theory & Survey Design
 * 8.5  — Hypothesis Testing: Framework & One-Sample Tests
 * 8.6  — Two-Sample Tests & ANOVA
 * 8.7  — Chi-Square Tests & Non-Parametric Methods
 * 8.8  — Correlation, Regression & Model Diagnostics
 * 8.9  — Experimental Design: Principles & Layouts
 * 8.10 — Effect Size, Power Analysis & Sample Size Planning
 * 8.11 — Final Exam (Org-locked)
 */
class Module8LessonsSeeder extends Seeder
{
    public function run()
    {
        $module = Module::where('order_index', 8)->firstOrFail();
        Lesson::where('module_id', $module->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 8.1 — Introduction to Statistical Thinking
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>Introduction to Statistical Thinking</h2>
<p>Statistics is the science of learning from data under uncertainty. It provides a disciplined, principled framework for converting raw observations into reliable knowledge. Whether you are designing a clinical trial to test a new drug, evaluating whether a website redesign increases conversions, or determining whether two manufacturing processes produce different defect rates, statistical thinking is the lens that keeps your conclusions honest.</p>

<h3>What Is Statistical Thinking?</h3>
<p>Statistical thinking is more than knowing formulas — it is a mindset. It requires acknowledging that data is always a sample of a larger reality, that randomness and variability are natural, and that evidence must be weighed against the possibility of chance. The statistician's job is to extract signal from noise, knowing that perfect certainty is rarely achievable.</p>

<p>Statistical thinking rests on three pillars:</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Variability is everywhere.</strong> No two measurements of the same thing are identical. Understanding the source and magnitude of variability is the heart of statistical analysis.</li>
  <li><strong style="color:var(--text);">Data are a sample.</strong> We observe a subset of reality and reason about the whole. Every conclusion comes with uncertainty that must be quantified.</li>
  <li><strong style="color:var(--text);">Context matters.</strong> Numbers without context are meaningless. A 5% difference means nothing without knowing the scale, the variability, and the practical stakes.</li>
</ul>

<h3>Population vs. Sample</h3>
<p>One of the most fundamental distinctions in statistics is between a <strong>population</strong> and a <strong>sample</strong>. The population is the complete set of all elements you are interested in — every patient with a specific disease, every unit of product from a factory, every voter in an election. Studying an entire population is almost never feasible, so we study a <strong>sample</strong> — a carefully chosen subset — and use that data to <em>infer</em> things about the population.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <div>
      <h4 style="color:#3b82f6;margin-top:0;">Population (N)</h4>
      <ul style="color:var(--muted);font-size:0.875rem;line-height:2;padding-left:1.2rem;margin:0;">
        <li>The entire group of interest</li>
        <li>Parameters: μ (mean), σ (std dev), π (proportion)</li>
        <li>Usually unknown and unobservable in full</li>
        <li>Example: All 50,000 employees at a company</li>
      </ul>
    </div>
    <div>
      <h4 style="color:#10b981;margin-top:0;">Sample (n)</h4>
      <ul style="color:var(--muted);font-size:0.875rem;line-height:2;padding-left:1.2rem;margin:0;">
        <li>A subset drawn from the population</li>
        <li>Statistics: x̄ (mean), s (std dev), p̂ (proportion)</li>
        <li>Observed and measured directly</li>
        <li>Example: 500 randomly selected employees</li>
      </ul>
    </div>
  </div>
</div>

<h3>Types of Statistical Studies</h3>
<p>Before collecting a single data point, you must decide what kind of study you are running. This decision determines what conclusions you can — and cannot — draw.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:32px;display:grid;gap:12px;">
  <div style="background:rgba(99,102,241,0.08);border-left:3px solid #6366f1;padding:14px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#6366f1;">Observational Study</strong> — The researcher observes and records without intervening. Can establish association but <em>cannot</em> establish causation. Example: surveying patients about their diet and recording whether they develop heart disease.
  </div>
  <div style="background:rgba(16,185,129,0.08);border-left:3px solid #10b981;padding:14px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#10b981;">Experiment (Randomized Controlled Trial)</strong> — The researcher deliberately assigns treatments to subjects. Randomization controls confounding. Can establish causation. Example: randomly assigning patients to drug vs. placebo and measuring outcomes.
  </div>
  <div style="background:rgba(245,158,11,0.08);border-left:3px solid #f59e0b;padding:14px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#f59e0b;">Quasi-Experiment</strong> — Resembles an experiment but lacks true random assignment. Example: comparing test scores before and after a new teaching method in a school where students were not randomly assigned to classes.
  </div>
  <div style="background:rgba(239,68,68,0.08);border-left:3px solid #ef4444;padding:14px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#ef4444;">Survey / Census</strong> — Systematic collection of data from a population or sample, typically through questionnaires. Excellent for measuring prevalence and attitudes; prone to non-response and response bias.
  </div>
</div>

<h3>Levels of Measurement</h3>
<p>Not all data are created equal. The level of measurement determines which statistical techniques are appropriate. Using the wrong technique for a given data type produces meaningless — or worse, misleading — results.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:32px;">
  <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
    <thead>
      <tr style="background:rgba(0,0,0,0.2);">
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Level</th>
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Properties</th>
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Examples</th>
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Permitted Statistics</th>
      </tr>
    </thead>
    <tbody>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:#6366f1;font-weight:700;">Nominal</td>
        <td style="padding:10px 16px;color:var(--muted);">Categories, no order</td>
        <td style="padding:10px 16px;color:var(--muted);">Blood type, eye color, country</td>
        <td style="padding:10px 16px;color:var(--muted);">Mode, frequency, chi-square</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:#f59e0b;font-weight:700;">Ordinal</td>
        <td style="padding:10px 16px;color:var(--muted);">Ordered, unequal intervals</td>
        <td style="padding:10px 16px;color:var(--muted);">Likert scale, education level, ranking</td>
        <td style="padding:10px 16px;color:var(--muted);">Median, percentile, Spearman r</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:#10b981;font-weight:700;">Interval</td>
        <td style="padding:10px 16px;color:var(--muted);">Equal intervals, no true zero</td>
        <td style="padding:10px 16px;color:var(--muted);">Temperature (°C/°F), IQ score, calendar year</td>
        <td style="padding:10px 16px;color:var(--muted);">Mean, std dev, Pearson r</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:#3b82f6;font-weight:700;">Ratio</td>
        <td style="padding:10px 16px;color:var(--muted);">Equal intervals, true zero</td>
        <td style="padding:10px 16px;color:var(--muted);">Height, weight, salary, reaction time</td>
        <td style="padding:10px 16px;color:var(--muted);">All statistics, geometric mean, CV</td>
      </tr>
    </tbody>
  </table>
</div>

<h3>The Research Process: A Statistical Roadmap</h3>
<p>Every rigorous study follows a systematic process. Skipping steps — especially planning sample size before collecting data — is one of the most common and costly mistakes in research.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;">
    <div style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.3);border-radius:8px;padding:14px;text-align:center;">
      <div style="font-size:1.4rem;margin-bottom:6px;">❓</div>
      <div style="font-weight:700;font-size:0.8rem;color:var(--text);margin-bottom:4px;">1. Research Question</div>
      <div style="font-size:0.72rem;color:var(--muted);">Define the problem precisely</div>
    </div>
    <div style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);border-radius:8px;padding:14px;text-align:center;">
      <div style="font-size:1.4rem;margin-bottom:6px;">📐</div>
      <div style="font-weight:700;font-size:0.8rem;color:var(--text);margin-bottom:4px;">2. Study Design</div>
      <div style="font-size:0.72rem;color:var(--muted);">Choose method, plan sample</div>
    </div>
    <div style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.3);border-radius:8px;padding:14px;text-align:center;">
      <div style="font-size:1.4rem;margin-bottom:6px;">📊</div>
      <div style="font-weight:700;font-size:0.8rem;color:var(--text);margin-bottom:4px;">3. Data Collection</div>
      <div style="font-size:0.72rem;color:var(--muted);">Execute sampling protocol</div>
    </div>
    <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:8px;padding:14px;text-align:center;">
      <div style="font-size:1.4rem;margin-bottom:6px;">🔍</div>
      <div style="font-weight:700;font-size:0.8rem;color:var(--text);margin-bottom:4px;">4. EDA</div>
      <div style="font-size:0.72rem;color:var(--muted);">Explore, visualize, summarize</div>
    </div>
    <div style="background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.3);border-radius:8px;padding:14px;text-align:center;">
      <div style="font-size:1.4rem;margin-bottom:6px;">⚙️</div>
      <div style="font-weight:700;font-size:0.8rem;color:var(--text);margin-bottom:4px;">5. Analysis</div>
      <div style="font-size:0.72rem;color:var(--muted);">Apply chosen tests</div>
    </div>
    <div style="background:rgba(168,85,247,0.1);border:1px solid rgba(168,85,247,0.3);border-radius:8px;padding:14px;text-align:center;">
      <div style="font-size:1.4rem;margin-bottom:6px;">📝</div>
      <div style="font-weight:700;font-size:0.8rem;color:var(--text);margin-bottom:4px;">6. Interpret & Report</div>
      <div style="font-size:0.72rem;color:var(--muted);">Contextualize findings</div>
    </div>
  </div>
</div>

<h3>Coding: Statistical Thinking in Python</h3>
<p>Let's put statistical thinking into practice by simulating the difference between a population and a sample, and observing how sample statistics vary around the true population parameter. This is the core intuition behind inferential statistics.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Population vs. Sample Simulation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt

np.random.seed(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Simulate a POPULATION of 10,000 student exam scores (normally distributed)</span>
<span style="color:#93c5fd;">population</span> = np.random.normal(loc=<span style="color:#fcd34d;">72</span>, scale=<span style="color:#fcd34d;">12</span>, size=<span style="color:#fcd34d;">10_000</span>)
<span style="color:#93c5fd;">pop_mean</span>   = population.mean()
<span style="color:#93c5fd;">pop_std</span>    = population.std()

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"═══ POPULATION (N = 10,000) ═══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"True Population Mean (μ) : {pop_mean:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"True Population Std  (σ) : {pop_std:.4f}"</span>)

<span style="color:#6b7280;"># Draw 5 different samples of size n=30 — watch how x̄ varies</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n═══ SAMPLES (n = 30 each) ═══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Sample':>8}  {'x̄ (Sample Mean)':>18}  {'s (Sample Std)':>16}  {'Error from μ':>14}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─" * 64</span>)

<span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">6</span>):
    <span style="color:#93c5fd;">sample</span>     = np.random.choice(population, size=<span style="color:#fcd34d;">30</span>, replace=<span style="color:#fca5a5;">False</span>)
    <span style="color:#93c5fd;">samp_mean</span>  = sample.mean()
    <span style="color:#93c5fd;">samp_std</span>   = sample.std(ddof=<span style="color:#fcd34d;">1</span>)   <span style="color:#6b7280;"># ddof=1 → unbiased sample std</span>
    <span style="color:#93c5fd;">error</span>      = samp_mean - pop_mean
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Sample '+str(i):>8}  {samp_mean:>18.4f}  {samp_std:>16.4f}  {error:>+14.4f}"</span>)

<span style="color:#6b7280;"># Visualize sampling variability</span>
<span style="color:#93c5fd;">sample_means</span> = [np.random.choice(population, <span style="color:#fcd34d;">30</span>).mean() <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">500</span>)]

<span style="color:#93c5fd;">fig</span>, <span style="color:#93c5fd;">axes</span> = plt.subplots(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">4</span>))
axes[<span style="color:#fcd34d;">0</span>].hist(population, bins=<span style="color:#fcd34d;">60</span>, color=<span style="color:#a7f3d0;">"#6366f1"</span>, alpha=<span style="color:#fcd34d;">0.7</span>, edgecolor=<span style="color:#a7f3d0;">"white"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Population Distribution (N=10,000)"</span>)
axes[<span style="color:#fcd34d;">0</span>].axvline(pop_mean, color=<span style="color:#a7f3d0;">"#ef4444"</span>, linewidth=<span style="color:#fcd34d;">2</span>, label=<span style="color:#a7f3d0;">f"μ={pop_mean:.1f}"</span>)
axes[<span style="color:#fcd34d;">0</span>].legend()

axes[<span style="color:#fcd34d;">1</span>].hist(sample_means, bins=<span style="color:#fcd34d;">40</span>, color=<span style="color:#a7f3d0;">"#10b981"</span>, alpha=<span style="color:#fcd34d;">0.7</span>, edgecolor=<span style="color:#a7f3d0;">"white"</span>)
axes[<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Sampling Distribution of x̄ (500 samples, n=30)"</span>)
axes[<span style="color:#fcd34d;">1</span>].axvline(pop_mean, color=<span style="color:#a7f3d0;">"#ef4444"</span>, linewidth=<span style="color:#fcd34d;">2</span>, label=<span style="color:#a7f3d0;">f"μ={pop_mean:.1f}"</span>)
axes[<span style="color:#fcd34d;">1</span>].legend()

plt.suptitle(<span style="color:#a7f3d0;">"Population vs. Sampling Distribution"</span>, fontsize=<span style="color:#fcd34d;">13</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout()
plt.show()

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nStd Dev of sample means (SE observed): {np.std(sample_means):.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Predicted SE = σ/√n = {pop_std:.4f}/√30  = {pop_std/np.sqrt(30):.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>═══ POPULATION (N = 10,000) ═══
True Population Mean (μ) : 71.9873
True Population Std  (σ) : 11.9984

═══ SAMPLES (n = 30 each) ═══
  Sample    x̄ (Sample Mean)    s (Sample Std)    Error from μ
────────────────────────────────────────────────────────────────
Sample 1            70.4521          11.3247          -1.5352
Sample 2            74.2183          12.8841          +2.2310
Sample 3            71.0946          11.7623          -0.8927
Sample 4            73.6104          12.2489          +1.6231
Sample 5            70.8832          11.5517          -1.1041

Std Dev of sample means (SE observed): 2.1872
Predicted SE = σ/√n = 11.9984/√30  = 2.1901</div>
  </div>
</div>
<p>Notice that each sample gives a slightly different mean — this is <strong>sampling variability</strong>. The observed standard deviation of those sample means (2.19) matches almost exactly the theoretical Standard Error σ/√n. This is the Central Limit Theorem in action, and it is the entire justification for confidence intervals and hypothesis tests.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '8.1 Introduction to Statistical Thinking',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'M8_L1', [
                ['q' => 'A researcher surveys 400 randomly selected hospital patients to estimate the average waiting time for all 50,000 patients seen annually. The 50,000 patients represent the...', 'opts' => ['Sample', 'Parameter', 'Population', 'Statistic'], 'ans' => 2, 'exp' => 'The population is the complete group of interest — all 50,000 patients seen annually. The 400 surveyed are the sample. A parameter (like the true mean wait time) describes the population; a statistic (like the sample mean) describes the sample.'],
                ['q' => 'A company randomly assigns 500 employees to receive a new ergonomic chair, and 500 to keep standard chairs, then measures back-pain incidents after 6 months. This is best described as...', 'opts' => ['An observational study', 'A randomized experiment', 'A quasi-experiment', 'A census'], 'ans' => 1, 'exp' => 'Random assignment to treatment and control groups is the defining feature of a true randomized experiment (RCT). This design allows causal conclusions — the ergonomic chairs caused (or did not cause) the observed difference in back pain.'],
                ['q' => 'Customer satisfaction scores collected on a scale of 1 (very dissatisfied) to 5 (very satisfied) are measured on which scale?', 'opts' => ['Nominal', 'Interval', 'Ratio', 'Ordinal'], 'ans' => 3, 'exp' => 'Likert-type rating scales are ordinal — there is a clear order (5 > 4 > 3) but the intervals between ratings are not guaranteed to be equal. The difference between 4 and 5 may not equal the difference between 1 and 2 in psychological terms.'],
                ['q' => 'Which of the following is TRUE about the Standard Error (SE) of the sample mean?', 'opts' => ['SE increases as sample size increases', 'SE = σ × √n', 'SE = σ / √n — it decreases as sample size grows', 'SE equals the population standard deviation'], 'ans' => 2, 'exp' => 'Standard Error = σ/√n. As n grows, the denominator increases and SE shrinks — larger samples yield more precise estimates of the population mean. SE measures the variability of sample means, not individual observations.'],
                ['q' => 'Correlation between ice cream sales and drowning deaths is r = 0.87. The best conclusion is...', 'opts' => ['Ice cream causes drowning', 'Drowning increases ice cream appetite', 'Hot weather (a confound) drives both variables upward in summer', 'The correlation must be a calculation error'], 'ans' => 2, 'exp' => 'This is a classic spurious correlation caused by a confounding variable — summer heat. Both ice cream sales and outdoor swimming (which carries drowning risk) increase in summer. Correlation never implies causation without a controlled experiment.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 8.2 — Descriptive Statistics & Data Summarization
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Descriptive Statistics & Data Summarization</h2>
<p>Before running any inferential test, you must deeply understand your data through <strong>descriptive statistics</strong>. Descriptive statistics summarize and communicate the essential features of a dataset — its center, spread, shape, and extreme values. They are not merely a preliminary step; they are often the most informative part of an analysis. A surprising number of research errors are caught simply by carefully examining descriptive summaries and plots before proceeding.</p>

<h3>Measures of Central Tendency</h3>
<p>Central tendency describes where the "middle" of a distribution lies. Three measures are commonly used, each with different properties and appropriate contexts.</p>

<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:32px;">
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #3b82f6;">
    <h4 style="color:#3b82f6;margin-top:0;">Mean (Arithmetic Average)</h4>
    <p style="color:var(--muted);font-size:0.85rem;line-height:1.7;">Sum of all values divided by count. Uses every observation. Sensitive to outliers — one extreme value can pull the mean far from most data. Best for symmetric, outlier-free distributions.</p>
    <div style="font-family:'JetBrains Mono',monospace;font-size:0.85rem;color:var(--text);margin-top:8px;">x̄ = Σxᵢ / n</div>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #10b981;">
    <h4 style="color:#10b981;margin-top:0;">Median (Middle Value)</h4>
    <p style="color:var(--muted);font-size:0.85rem;line-height:1.7;">The value that splits sorted data exactly in half. Resistant to outliers — extreme values do not affect it. Best for skewed distributions (income, house prices, survival times) where outliers are common.</p>
    <div style="font-family:'JetBrains Mono',monospace;font-size:0.85rem;color:var(--text);margin-top:8px;">50th percentile</div>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #f59e0b;">
    <h4 style="color:#f59e0b;margin-top:0;">Mode (Most Frequent)</h4>
    <p style="color:var(--muted);font-size:0.85rem;line-height:1.7;">The value(s) occurring most frequently. The only measure of center appropriate for nominal data. A distribution can be unimodal, bimodal, or multimodal. May not be unique or particularly representative.</p>
    <div style="font-family:'JetBrains Mono',monospace;font-size:0.85rem;color:var(--text);margin-top:8px;">argmax frequency</div>
  </div>
</div>

<h3>Measures of Spread (Dispersion)</h3>
<p>Two datasets can have identical means but very different spreads. Spread measures quantify how much the data varies around the center — this is often just as important as the center itself.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <div style="display:grid;gap:16px;">
    <div>
      <strong style="color:var(--text);">Range</strong> = Max − Min. Simplest measure but highly sensitive to single outliers. Useful only when the distribution has no extreme values.
    </div>
    <div>
      <strong style="color:var(--text);">Interquartile Range (IQR)</strong> = Q3 − Q1. The range of the middle 50% of data. Completely unaffected by outliers. Used in boxplots and the 1.5×IQR outlier detection rule.
    </div>
    <div>
      <strong style="color:var(--text);">Variance (s²)</strong> = Σ(xᵢ − x̄)² / (n−1). Average squared deviation from the mean. Uses n−1 (Bessel's correction) to produce an unbiased estimate of σ². Squared units make it hard to interpret directly.
    </div>
    <div>
      <strong style="color:var(--text);">Standard Deviation (s)</strong> = √s². Same units as the original data. The most widely reported measure of spread. Approximately 68% of normally distributed data falls within ±1s of the mean.
    </div>
    <div>
      <strong style="color:var(--text);">Coefficient of Variation (CV)</strong> = (s / x̄) × 100%. A dimensionless measure of relative variability. Allows comparing spread across variables with different units or scales.
    </div>
  </div>
</div>

<h3>Five-Number Summary & Boxplots</h3>
<p>The five-number summary — Minimum, Q1, Median, Q3, Maximum — provides a compact description of an entire distribution. Boxplots visualize this summary, making it easy to compare distributions across groups and spot outliers (points beyond 1.5×IQR from the quartiles).</p>

<h3>Shape of a Distribution</h3>
<p>Beyond center and spread, the shape of a distribution matters for choosing appropriate statistical tests. Two key shape measures are <strong>skewness</strong> (asymmetry) and <strong>kurtosis</strong> (tail heaviness).</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Positive (Right) Skew:</strong> Tail extends to the right; mean > median. Common in income, wait times, biological assay data.</li>
  <li><strong style="color:var(--text);">Negative (Left) Skew:</strong> Tail extends to the left; mean &lt; median. Common in exam scores with a ceiling effect.</li>
  <li><strong style="color:var(--text);">Excess Kurtosis > 0 (Leptokurtic):</strong> Heavier tails than normal, more extreme outliers. Financial returns, measurement errors.</li>
  <li><strong style="color:var(--text);">Excess Kurtosis &lt; 0 (Platykurtic):</strong> Lighter tails than normal, fewer extremes. Uniform-like distributions.</li>
</ul>

<h3>Coding: Comprehensive Descriptive Statistics</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Full Descriptive Analysis with scipy & matplotlib</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats

np.random.seed(<span style="color:#fcd34d;">7</span>)

<span style="color:#6b7280;"># Simulate hospital patient blood pressure readings (mmHg)</span>
<span style="color:#6b7280;"># Right-skewed: some patients have very high BP</span>
<span style="color:#93c5fd;">bp</span> = np.concatenate([
    np.random.normal(<span style="color:#fcd34d;">120</span>, <span style="color:#fcd34d;">15</span>, <span style="color:#fcd34d;">180</span>),   <span style="color:#6b7280;"># Normal range patients</span>
    np.random.normal(<span style="color:#fcd34d;">160</span>, <span style="color:#fcd34d;">20</span>, <span style="color:#fcd34d;">20</span>),    <span style="color:#6b7280;"># Hypertensive patients</span>
])
<span style="color:#93c5fd;">bp</span> = np.clip(bp, <span style="color:#fcd34d;">80</span>, <span style="color:#fcd34d;">220</span>)   <span style="color:#6b7280;"># physiologically plausible range</span>

<span style="color:#6b7280;"># ── Central Tendency ──────────────────────────────────────────────</span>
<span style="color:#93c5fd;">mean_bp</span>   = np.mean(bp)
<span style="color:#93c5fd;">median_bp</span> = np.median(bp)
<span style="color:#93c5fd;">mode_bp</span>   = stats.mode(bp, keepdims=<span style="color:#fca5a5;">True</span>).mode[<span style="color:#fcd34d;">0</span>]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ CENTRAL TENDENCY ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Mean   : {mean_bp:.2f} mmHg"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Median : {median_bp:.2f} mmHg"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Mode   : {mode_bp:.2f} mmHg (rounded bin)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Mean > Median → positive (right) skew confirmed\n"</span>)

<span style="color:#6b7280;"># ── Spread ────────────────────────────────────────────────────────</span>
<span style="color:#93c5fd;">q1</span>, <span style="color:#93c5fd;">q3</span> = np.percentile(bp, [<span style="color:#fcd34d;">25</span>, <span style="color:#fcd34d;">75</span>])
<span style="color:#93c5fd;">iqr</span>     = q3 - q1
<span style="color:#93c5fd;">std_bp</span>  = np.std(bp, ddof=<span style="color:#fcd34d;">1</span>)
<span style="color:#93c5fd;">var_bp</span>  = np.var(bp, ddof=<span style="color:#fcd34d;">1</span>)
<span style="color:#93c5fd;">cv_bp</span>   = (std_bp / mean_bp) * <span style="color:#fcd34d;">100</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ SPREAD ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Range      : {bp.max()-bp.min():.2f} mmHg"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  IQR        : {iqr:.2f} mmHg  (Q1={q1:.1f}, Q3={q3:.1f})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Variance   : {var_bp:.2f} mmHg²"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Std Dev    : {std_bp:.2f} mmHg"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  CV         : {cv_bp:.1f}%\n"</span>)

<span style="color:#6b7280;"># ── Shape ────────────────────────────────────────────────────────</span>
<span style="color:#93c5fd;">skewness</span> = stats.skew(bp)
<span style="color:#93c5fd;">kurt</span>     = stats.kurtosis(bp)   <span style="color:#6b7280;"># excess kurtosis (normal=0)</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ SHAPE ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Skewness        : {skewness:+.4f}  (>0 = right-skewed)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Excess Kurtosis : {kurt:+.4f}  (>0 = heavier tails than normal)\n"</span>)

<span style="color:#6b7280;"># ── Outliers via 1.5×IQR rule ────────────────────────────────────</span>
<span style="color:#93c5fd;">lower_fence</span> = q1 - <span style="color:#fcd34d;">1.5</span> * iqr
<span style="color:#93c5fd;">upper_fence</span> = q3 + <span style="color:#fcd34d;">1.5</span> * iqr
<span style="color:#93c5fd;">outliers</span>    = bp[(bp < lower_fence) | (bp > upper_fence)]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ OUTLIERS (1.5×IQR rule) ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Fences          : [{lower_fence:.1f}, {upper_fence:.1f}]"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  # Outliers      : {len(outliers)}  → {outliers.round(1).tolist()}\n"</span>)

<span style="color:#6b7280;"># ── Visualization ────────────────────────────────────────────────</span>
<span style="color:#93c5fd;">fig</span>, <span style="color:#93c5fd;">axes</span> = plt.subplots(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">3</span>, figsize=(<span style="color:#fcd34d;">14</span>, <span style="color:#fcd34d;">4</span>))

axes[<span style="color:#fcd34d;">0</span>].hist(bp, bins=<span style="color:#fcd34d;">30</span>, color=<span style="color:#a7f3d0;">"#6366f1"</span>, edgecolor=<span style="color:#a7f3d0;">"white"</span>, alpha=<span style="color:#fcd34d;">0.85</span>)
axes[<span style="color:#fcd34d;">0</span>].axvline(mean_bp,   color=<span style="color:#a7f3d0;">"#ef4444"</span>, lw=<span style="color:#fcd34d;">2</span>, label=<span style="color:#a7f3d0;">f"Mean {mean_bp:.0f}"</span>)
axes[<span style="color:#fcd34d;">0</span>].axvline(median_bp, color=<span style="color:#a7f3d0;">"#10b981"</span>, lw=<span style="color:#fcd34d;">2</span>, label=<span style="color:#a7f3d0;">f"Median {median_bp:.0f}"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Histogram"</span>); axes[<span style="color:#fcd34d;">0</span>].legend(fontsize=<span style="color:#fcd34d;">8</span>)

axes[<span style="color:#fcd34d;">1</span>].boxplot(bp, patch_artist=<span style="color:#fca5a5;">True</span>,
               boxprops=<span style="color:#93c5fd;">dict</span>(facecolor=<span style="color:#a7f3d0;">"#a78bfa"</span>, alpha=<span style="color:#fcd34d;">0.6</span>))
axes[<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Boxplot"</span>); axes[<span style="color:#fcd34d;">1</span>].set_xticklabels([<span style="color:#a7f3d0;">"BP (mmHg)"</span>])

stats.probplot(bp, dist=<span style="color:#a7f3d0;">"norm"</span>, plot=axes[<span style="color:#fcd34d;">2</span>])
axes[<span style="color:#fcd34d;">2</span>].set_title(<span style="color:#a7f3d0;">"Normal Q-Q Plot"</span>)

plt.suptitle(<span style="color:#a7f3d0;">"Blood Pressure: Descriptive Analysis"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>══ CENTRAL TENDENCY ══
  Mean   : 124.83 mmHg
  Median : 121.47 mmHg
  Mode   : 120.00 mmHg (rounded bin)
  Mean > Median → positive (right) skew confirmed

══ SPREAD ══
  Range      : 130.24 mmHg
  IQR        : 21.34 mmHg  (Q1=111.3, Q3=132.6)
  Variance   : 392.18 mmHg²
  Std Dev    : 19.80 mmHg
  CV         : 15.9%

══ SHAPE ══
  Skewness        : +0.6821  (>0 = right-skewed)
  Excess Kurtosis : +0.4193  (>0 = heavier tails than normal)

══ OUTLIERS (1.5×IQR rule) ══
  Fences          : [79.3, 164.6]
  # Outliers      : 7  → [168.2, 171.3, 173.6, ...]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '8.2 Descriptive Statistics & Data Summarization',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'M8_L2', [
                ['q' => 'A dataset of annual salaries has mean = $95,000 and median = $62,000. What does this tell you?', 'opts' => ['The data is approximately symmetric', 'The data is negatively (left) skewed — most people earn more', 'The data is positively (right) skewed — a few very high earners pull the mean up', 'Both mean and median are wrong; use the mode instead'], 'ans' => 2, 'exp' => 'Mean > Median indicates positive (right) skew. A small number of very high earners (executives, outliers) pull the arithmetic mean well above the median, which is why income statistics use the median — it better represents the "typical" person.'],
                ['q' => 'Bessel\'s correction divides by (n−1) rather than n when computing sample variance. Why?', 'opts' => ['To make computation easier', 'To produce an unbiased estimate of the population variance σ²', 'Because sample sizes are always odd', 'To ensure variance cannot be negative'], 'ans' => 1, 'exp' => 'Sample variance computed with n in the denominator systematically underestimates the true population variance σ² because the sample mean is closer to the sample values than the true μ is. Dividing by (n−1) corrects this bias, yielding an unbiased estimator of σ².'],
                ['q' => 'In a dataset [3, 5, 7, 9, 200], which measure of center is most affected by the outlier 200?', 'opts' => ['Median', 'Mode', 'Mean', 'All three equally'], 'ans' => 2, 'exp' => 'The mean uses all values arithmetically: (3+5+7+9+200)/5 = 44.8, which is far from most data. The median is 7 — unchanged if 200 is replaced by any value above 7. The mean is highly sensitive to outliers; the median is resistant.'],
                ['q' => 'A quality control engineer reports CV = 3% for machine A and CV = 18% for machine B, both producing bolts of the same target diameter. What does this mean?', 'opts' => ['Machine A produces larger bolts', 'Machine A is more consistent — lower relative variability', 'Machine B is more accurate', 'CV cannot be compared between machines'], 'ans' => 1, 'exp' => 'The Coefficient of Variation (CV = s/x̄ × 100%) measures relative variability. CV = 3% means Machine A\'s output has very little variability relative to its mean. CV = 18% for Machine B means much higher relative inconsistency. CV allows meaningful comparison even if the machines have different mean diameters.'],
                ['q' => 'Points beyond Q1 − 1.5×IQR or Q3 + 1.5×IQR on a boxplot are classified as...', 'opts' => ['Influential observations', 'Leverage points', 'Outliers', 'High-variance points'], 'ans' => 2, 'exp' => 'Tukey\'s fence rule uses 1.5×IQR to define mild outliers (beyond 1.5×IQR) and extreme outliers (beyond 3×IQR). This rule is built into standard boxplots. Outliers should be investigated — they may be data errors, valid extreme values, or genuinely interesting observations.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 8.3 — Probability Distributions in Practice
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Probability Distributions in Practice</h2>
<p>A probability distribution is a mathematical function that describes the likelihood of every possible outcome of a random variable. Distributions are the theoretical backbone of virtually every statistical method. Understanding which distribution governs your data determines which tests to use, how to interpret results, and how to generate realistic simulations. In this lesson, we move from theory to application — focusing on the distributions you will actually encounter in experimental research.</p>

<h3>Discrete vs. Continuous Random Variables</h3>
<p>A <strong>discrete</strong> random variable takes countable values (0, 1, 2, 3...). A <strong>continuous</strong> random variable takes any value in an interval. This distinction determines the type of distribution — discrete variables use Probability Mass Functions (PMF), continuous variables use Probability Density Functions (PDF).</p>

<h3>The Normal Distribution: The Workhorse of Statistics</h3>
<p>The normal (Gaussian) distribution is defined by its mean μ and standard deviation σ: N(μ, σ²). Its symmetric, bell-shaped curve arises naturally from the Central Limit Theorem — whenever you average many independent random variables, the average tends toward normality regardless of the original distribution. This is why the normal distribution is the foundation of most parametric tests.</p>

<p>The <strong>Standard Normal</strong> is N(0, 1). Any normal variable X can be standardized: <strong>Z = (X − μ) / σ</strong>. Z-scores measure distance from the mean in standard deviation units.</p>

<p><strong>The 68-95-99.7 Rule:</strong></p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li>≈ <strong style="color:var(--text);">68.27%</strong> of data falls within ±1σ of the mean</li>
  <li>≈ <strong style="color:var(--text);">95.45%</strong> of data falls within ±2σ of the mean</li>
  <li>≈ <strong style="color:var(--text);">99.73%</strong> of data falls within ±3σ of the mean</li>
</ul>

<h3>The t-Distribution: When σ is Unknown</h3>
<p>In practice, the population standard deviation σ is almost never known. When you use the sample standard deviation s instead, the resulting test statistic follows a <strong>t-distribution</strong> with n−1 degrees of freedom. The t-distribution is symmetric and bell-shaped like the normal, but has heavier tails — reflecting the extra uncertainty from estimating σ. As n → ∞, the t-distribution approaches the normal.</p>

<h3>Chi-Square Distribution</h3>
<p>If Z₁, Z₂, ..., Zₖ are independent standard normal variables, then X = Z₁² + Z₂² + ... + Zₖ² follows a chi-square distribution with k degrees of freedom (χ²(k)). This distribution is right-skewed and used for variance tests, goodness-of-fit tests, and tests of independence in contingency tables.</p>

<h3>The F-Distribution</h3>
<p>The F-distribution arises as the ratio of two independent chi-square distributions divided by their respective degrees of freedom. It is right-skewed and always non-negative. It is used in ANOVA (comparing means across groups) and in regression (testing model significance).</p>

<h3>Key Discrete Distributions</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:32px;display:grid;gap:12px;">
  <div style="background:rgba(59,130,246,0.07);border-left:3px solid #3b82f6;padding:12px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#3b82f6;">Binomial B(n, p):</strong> Number of successes in n independent Bernoulli trials with success probability p. Mean = np, Variance = np(1−p). Example: number of defective items in a batch of n.
  </div>
  <div style="background:rgba(16,185,129,0.07);border-left:3px solid #10b981;padding:12px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#10b981;">Poisson(λ):</strong> Number of events in a fixed time/space interval when events occur at average rate λ independently. Mean = Variance = λ. Example: number of customer arrivals per hour.
  </div>
  <div style="background:rgba(168,85,247,0.07);border-left:3px solid #a855f7;padding:12px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#a855f7;">Hypergeometric:</strong> Number of successes when drawing without replacement. Used in quality control sampling when population is small.
  </div>
</div>

<h3>Coding: Working with Distributions in SciPy</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Probability Distributions: CDF, PDF, PPF & Visualization</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats

<span style="color:#6b7280;"># ── 1. Normal Distribution: P(X > 180) where X ~ N(170, 10²) ─────</span>
<span style="color:#93c5fd;">norm_dist</span> = stats.norm(loc=<span style="color:#fcd34d;">170</span>, scale=<span style="color:#fcd34d;">10</span>)
<span style="color:#93c5fd;">p_above_180</span> = <span style="color:#fcd34d;">1</span> - norm_dist.cdf(<span style="color:#fcd34d;">180</span>)
<span style="color:#93c5fd;">p_between</span>   = norm_dist.cdf(<span style="color:#fcd34d;">185</span>) - norm_dist.cdf(<span style="color:#fcd34d;">155</span>)
<span style="color:#93c5fd;">percentile_95</span> = norm_dist.ppf(<span style="color:#fcd34d;">0.95</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ NORMAL DISTRIBUTION N(170, 10²) ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  P(X > 180)      = {p_above_180:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  P(155 < X < 185) = {p_between:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  95th percentile  = {percentile_95:.2f} cm\n"</span>)

<span style="color:#6b7280;"># ── 2. t-Distribution: critical value for 95% CI, df=24 ──────────</span>
<span style="color:#93c5fd;">t_crit</span> = stats.t.ppf(<span style="color:#fcd34d;">0.975</span>, df=<span style="color:#fcd34d;">24</span>)   <span style="color:#6b7280;"># two-tailed α=0.05</span>
<span style="color:#93c5fd;">z_crit</span> = stats.norm.ppf(<span style="color:#fcd34d;">0.975</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ t-DISTRIBUTION (df=24) vs NORMAL ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  t critical (α=0.05, df=24) : {t_crit:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  z critical (α=0.05, ∞ df)  : {z_crit:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Difference                 : {t_crit - z_crit:.4f} (heavier tails!)\n"</span>)

<span style="color:#6b7280;"># ── 3. Binomial: P(X ≥ 15 defectives) when B(100, 0.10) ─────────</span>
<span style="color:#93c5fd;">binom_dist</span> = stats.binom(n=<span style="color:#fcd34d;">100</span>, p=<span style="color:#fcd34d;">0.10</span>)
<span style="color:#93c5fd;">p_15_plus</span>  = <span style="color:#fcd34d;">1</span> - binom_dist.cdf(<span style="color:#fcd34d;">14</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ BINOMIAL B(100, 0.10) ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Mean      = {binom_dist.mean():.1f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Std Dev   = {binom_dist.std():.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  P(X ≥ 15) = {p_15_plus:.4f}\n"</span>)

<span style="color:#6b7280;"># ── 4. Compare shapes: Normal vs t(df=5) vs t(df=30) ──────────────</span>
<span style="color:#93c5fd;">x</span> = np.linspace(<span style="color:#fcd34d;">-5</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">400</span>)
<span style="color:#93c5fd;">fig</span>, <span style="color:#93c5fd;">axes</span> = plt.subplots(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">13</span>, <span style="color:#fcd34d;">4</span>))

<span style="color:#c4b5fd;">for</span> df, color, label <span style="color:#c4b5fd;">in</span> [(<span style="color:#fcd34d;">2</span>, <span style="color:#a7f3d0;">"#ef4444"</span>, <span style="color:#a7f3d0;">"t(df=2)"</span>),
                           (<span style="color:#fcd34d;">5</span>, <span style="color:#a7f3d0;">"#f59e0b"</span>, <span style="color:#a7f3d0;">"t(df=5)"</span>),
                           (<span style="color:#fcd34d;">30</span>, <span style="color:#a7f3d0;">"#10b981"</span>, <span style="color:#a7f3d0;">"t(df=30)"</span>)]:
    axes[<span style="color:#fcd34d;">0</span>].plot(x, stats.t.pdf(x, df=df), color=color, label=label, lw=<span style="color:#fcd34d;">2</span>)
axes[<span style="color:#fcd34d;">0</span>].plot(x, stats.norm.pdf(x), color=<span style="color:#a7f3d0;">"#3b82f6"</span>, label=<span style="color:#a7f3d0;">"Normal"</span>, lw=<span style="color:#fcd34d;">2.5</span>, ls=<span style="color:#a7f3d0;">"--"</span>)
axes[<span style="color:#fcd34d;">0</span>].legend(); axes[<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"t vs Normal: Heavier Tails at Low df"</span>)

<span style="color:#c4b5fd;">for</span> df2, color <span style="color:#c4b5fd;">in</span> [(<span style="color:#fcd34d;">2</span>, <span style="color:#a7f3d0;">"#ef4444"</span>), (<span style="color:#fcd34d;">5</span>, <span style="color:#a7f3d0;">"#f59e0b"</span>),
                      (<span style="color:#fcd34d;">10</span>, <span style="color:#a7f3d0;">"#10b981"</span>), (<span style="color:#fcd34d;">20</span>, <span style="color:#a7f3d0;">"#6366f1"</span>)]:
    xc = np.linspace(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">40</span>, <span style="color:#fcd34d;">400</span>)
    axes[<span style="color:#fcd34d;">1</span>].plot(xc, stats.chi2.pdf(xc, df=df2), color=color, label=<span style="color:#a7f3d0;">f"χ²(df={df2})"</span>, lw=<span style="color:#fcd34d;">2</span>)
axes[<span style="color:#fcd34d;">1</span>].legend(); axes[<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Chi-Square Distribution by df"</span>)

plt.suptitle(<span style="color:#a7f3d0;">"Key Distributions in Inferential Statistics"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>══ NORMAL DISTRIBUTION N(170, 10²) ══
  P(X > 180)       = 0.1587
  P(155 < X < 185) = 0.8664
  95th percentile  = 186.45 cm

══ t-DISTRIBUTION (df=24) vs NORMAL ══
  t critical (α=0.05, df=24) : 2.0639
  z critical (α=0.05, ∞ df)  : 1.9600
  Difference                 : 0.1039 (heavier tails!)

══ BINOMIAL B(100, 0.10) ══
  Mean      = 10.0
  Std Dev   = 3.0000
  P(X ≥ 15) = 0.1285</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '8.3 Probability Distributions in Practice',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'M8_L3', [
                ['q' => 'IQ scores follow N(100, 15²). What is the probability a randomly chosen person has IQ > 130?', 'opts' => ['~50%', '~16%', '~2.3%', '~0.1%'], 'ans' => 2, 'exp' => 'IQ=130 is exactly 2 standard deviations above the mean [(130-100)/15 = 2]. By the 68-95-99.7 rule, 95.45% of data falls within ±2σ, so 4.55% falls outside — and 2.275% falls above +2σ ≈ 2.3%.'],
                ['q' => 'Why does the t-distribution have heavier tails than the standard normal?', 'opts' => ['It is designed for discrete data', 'It accounts for additional uncertainty from estimating σ with the sample s', 'It is only used for large samples', 'Its mean is not zero'], 'ans' => 1, 'exp' => 'When we substitute the unknown σ with the sample standard deviation s, we introduce extra uncertainty. The t-distribution has heavier tails to reflect this — making critical values larger (more conservative) than the normal, especially for small samples. As n→∞, s→σ and t→z.'],
                ['q' => 'A random variable X follows Poisson(λ=4). What is the variance of X?', 'opts' => ['2', '4', '16', 'Cannot be determined without n'], 'ans' => 1, 'exp' => 'A defining property of the Poisson distribution is that Mean = Variance = λ. So if λ=4, then E[X]=4 and Var(X)=4. This equality is a key diagnostic — if your count data has variance much larger than the mean (overdispersion), a Poisson model may be inappropriate.'],
                ['q' => 'The chi-square distribution with k degrees of freedom is formed by...', 'opts' => ['Summing k normal random variables', 'Summing k squared standard normal variables', 'Multiplying k normal variables', 'Dividing two normal distributions'], 'ans' => 1, 'exp' => 'χ²(k) = Z₁² + Z₂² + ... + Zₖ² where each Zᵢ ~ N(0,1). This construction explains why chi-square is always non-negative and right-skewed. It approaches normality as k increases (by CLT).'],
                ['q' => 'The PPF (Percent Point Function) in scipy.stats is the inverse of...', 'opts' => ['The PDF (probability density function)', 'The PMF (probability mass function)', 'The CDF (cumulative distribution function)', 'The survival function'], 'ans' => 2, 'exp' => 'PPF = Quantile Function = Inverse CDF. Given a probability p, PPF(p) returns the value x such that P(X ≤ x) = p. For example, norm.ppf(0.975) = 1.96, meaning 97.5% of the standard normal distribution lies below 1.96. This is how critical values are computed.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 8.4 — Sampling Theory & Survey Design
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Sampling Theory & Survey Design</h2>
<p>The quality of any statistical conclusion is fundamentally limited by the quality of the sample. A brilliant analysis of a biased sample produces biased conclusions. Sampling theory addresses the principles and methods for obtaining samples that validly represent the population so that inferences can be trusted.</p>

<h3>Why Sampling?</h3>
<p>Studying an entire population (a census) is usually impractical due to cost, time, and sometimes destructive testing (e.g., testing every battery until it fails). A properly designed sample allows us to estimate population parameters with quantifiable precision at a fraction of the cost.</p>

<h3>Probability Sampling Methods</h3>
<p>In probability sampling, every unit in the population has a known, non-zero probability of selection. This is essential for making valid statistical inferences.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:32px;display:grid;gap:14px;">
  <div style="background:rgba(99,102,241,0.08);border-left:3px solid #6366f1;padding:14px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#6366f1;">Simple Random Sampling (SRS):</strong> Every possible sample of size n has an equal probability of selection. The gold standard. Requires a complete sampling frame (list of all population members). Best when population is homogeneous.
  </div>
  <div style="background:rgba(16,185,129,0.08);border-left:3px solid #10b981;padding:14px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#10b981;">Stratified Random Sampling:</strong> Divide the population into non-overlapping subgroups (strata) defined by a relevant characteristic, then SRS within each stratum. More precise than SRS when strata are internally homogeneous but differ between strata. Guarantees representation of each stratum.
  </div>
  <div style="background:rgba(245,158,11,0.08);border-left:3px solid #f59e0b;padding:14px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#f59e0b;">Cluster Sampling:</strong> Divide population into clusters (natural groups like schools, neighborhoods), randomly select clusters, then survey all members of selected clusters. Cost-effective for geographically dispersed populations. Less precise than SRS (cluster members tend to be similar).
  </div>
  <div style="background:rgba(239,68,68,0.08);border-left:3px solid #ef4444;padding:14px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#ef4444;">Systematic Sampling:</strong> Select every kth unit from a list, starting at a random starting point. Simple to implement. Watch for periodicity — if the list has a pattern aligned with k, estimates can be badly biased.
  </div>
  <div style="background:rgba(168,85,247,0.08);border-left:3px solid #a855f7;padding:14px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#a855f7;">Multistage Sampling:</strong> Combines multiple stages of probability sampling. Example: randomly select states, then randomly select counties within states, then randomly select households. Used in large national surveys (Census, NHANES).
  </div>
</div>

<h3>Non-Probability Sampling (and Its Dangers)</h3>
<p>Non-probability sampling methods — convenience sampling, volunteer sampling, quota sampling, snowball sampling — do not give every unit a known selection probability. They are faster and cheaper but produce samples that may be systematically unrepresentative. Conclusions cannot be legitimately generalized to the population. The infamous 1936 Literary Digest poll predicted Roosevelt would lose to Landon in a landslide because it sampled from car registrations and telephone directories — dramatically overrepresenting wealthy Republicans.</p>

<h3>Sources of Bias in Surveys</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:32px;">
  <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
    <thead>
      <tr style="background:rgba(0,0,0,0.2);">
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Bias Type</th>
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Definition</th>
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Example</th>
      </tr>
    </thead>
    <tbody>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:#ef4444;font-weight:600;">Selection Bias</td>
        <td style="padding:10px 16px;color:var(--muted);">Sample systematically differs from population</td>
        <td style="padding:10px 16px;color:var(--muted);">Voluntary response poll (only strong opinions respond)</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:#f59e0b;font-weight:600;">Non-Response Bias</td>
        <td style="padding:10px 16px;color:var(--muted);">Non-respondents differ from respondents</td>
        <td style="padding:10px 16px;color:var(--muted);">Sicker patients too ill to complete health survey</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:#6366f1;font-weight:600;">Response Bias</td>
        <td style="padding:10px 16px;color:var(--muted);">Respondents give inaccurate answers</td>
        <td style="padding:10px 16px;color:var(--muted);">Social desirability: underreporting alcohol use to doctors</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:#10b981;font-weight:600;">Measurement Bias</td>
        <td style="padding:10px 16px;color:var(--muted);">Systematic error in data collection instrument</td>
        <td style="padding:10px 16px;color:var(--muted);">Uncalibrated scale consistently reads 2 kg high</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:#a855f7;font-weight:600;">Interviewer Bias</td>
        <td style="padding:10px 16px;color:var(--muted);">Interviewer characteristics affect responses</td>
        <td style="padding:10px 16px;color:var(--muted);">Respondents give different answers to male vs. female interviewers on gender equality questions</td>
      </tr>
    </tbody>
  </table>
</div>

<h3>The Central Limit Theorem and Confidence Intervals</h3>
<p>The CLT guarantees that for sufficiently large n (generally n ≥ 30), the sampling distribution of x̄ is approximately normal with mean μ and standard error SE = σ/√n. A <strong>confidence interval</strong> uses this to bound the unknown μ:</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:32px;font-family:'JetBrains Mono',monospace;text-align:center;">
  <div style="font-size:1rem;color:var(--text);margin-bottom:8px;">95% CI for μ: &nbsp; x̄ ± 1.96 × (s / √n)</div>
  <div style="font-size:0.85rem;color:var(--muted);">Use t* instead of 1.96 when σ is unknown and n is small (use t-distribution with df = n−1)</div>
</div>

<h3>Coding: Sampling Methods & Confidence Interval Simulation</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Stratified Sampling & Confidence Interval Coverage</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats

np.random.seed(<span style="color:#fcd34d;">99</span>)

<span style="color:#6b7280;"># ── Stratified Sampling Example ──────────────────────────────────</span>
<span style="color:#6b7280;"># Population: 3 departments with different salary distributions</span>
<span style="color:#93c5fd;">pop</span> = pd.DataFrame({
    <span style="color:#a7f3d0;">'salary'</span>: np.concatenate([
        np.random.normal(<span style="color:#fcd34d;">45000</span>, <span style="color:#fcd34d;">5000</span>, <span style="color:#fcd34d;">500</span>),   <span style="color:#6b7280;"># Admin (N=500)</span>
        np.random.normal(<span style="color:#fcd34d;">75000</span>, <span style="color:#fcd34d;">10000</span>, <span style="color:#fcd34d;">300</span>),  <span style="color:#6b7280;"># Engineering (N=300)</span>
        np.random.normal(<span style="color:#fcd34d;">120000</span>, <span style="color:#fcd34d;">20000</span>, <span style="color:#fcd34d;">200</span>), <span style="color:#6b7280;"># Executive (N=200)</span>
    ]),
    <span style="color:#a7f3d0;">'dept'</span>: [<span style="color:#a7f3d0;">'Admin'</span>]*<span style="color:#fcd34d;">500</span> + [<span style="color:#a7f3d0;">'Engineering'</span>]*<span style="color:#fcd34d;">300</span> + [<span style="color:#a7f3d0;">'Executive'</span>]*<span style="color:#fcd34d;">200</span>
})
<span style="color:#93c5fd;">true_mean</span> = pop[<span style="color:#a7f3d0;">'salary'</span>].mean()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"True population mean salary: ${true_mean:,.0f}"</span>)

<span style="color:#6b7280;"># Simple Random Sample (n=100)</span>
<span style="color:#93c5fd;">srs</span> = pop.sample(<span style="color:#fcd34d;">100</span>)[<span style="color:#a7f3d0;">'salary'</span>]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"SRS mean  (n=100): ${srs.mean():,.0f}  ± ${srs.std(ddof=1)/np.sqrt(100)*1.96:,.0f}"</span>)

<span style="color:#6b7280;"># Stratified Sample — proportional allocation</span>
<span style="color:#93c5fd;">strat_sample</span> = pop.groupby(<span style="color:#a7f3d0;">'dept'</span>, group_keys=<span style="color:#fca5a5;">False</span>).apply(
    <span style="color:#c4b5fd;">lambda</span> g: g.sample(<span style="color:#93c5fd;">int</span>(<span style="color:#93c5fd;">round</span>(<span style="color:#fcd34d;">100</span> * <span style="color:#93c5fd;">len</span>(g) / <span style="color:#93c5fd;">len</span>(pop))))).reset_index(drop=<span style="color:#fca5a5;">True</span>)
<span style="color:#93c5fd;">strat_sal</span> = strat_sample[<span style="color:#a7f3d0;">'salary'</span>]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Stratified (n=100): ${strat_sal.mean():,.0f}  ± ${strat_sal.std(ddof=1)/np.sqrt(len(strat_sal))*1.96:,.0f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"(Stratified is closer to truth because strata capture salary variation)\n"</span>)

<span style="color:#6b7280;"># ── Confidence Interval Coverage Simulation ───────────────────────</span>
<span style="color:#93c5fd;">n_sim</span>  = <span style="color:#fcd34d;">200</span>
<span style="color:#93c5fd;">n</span>      = <span style="color:#fcd34d;">30</span>
<span style="color:#93c5fd;">mu</span>     = <span style="color:#fcd34d;">50</span>
<span style="color:#93c5fd;">sigma</span>  = <span style="color:#fcd34d;">10</span>
<span style="color:#93c5fd;">t_crit</span> = stats.t.ppf(<span style="color:#fcd34d;">0.975</span>, df=n-<span style="color:#fcd34d;">1</span>)

<span style="color:#93c5fd;">lo_list</span>, <span style="color:#93c5fd;">hi_list</span>, <span style="color:#93c5fd;">contains</span> = [], [], []
<span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n_sim):
    <span style="color:#93c5fd;">s</span>  = np.random.normal(mu, sigma, n)
    <span style="color:#93c5fd;">lo</span> = s.mean() - t_crit * s.std(ddof=<span style="color:#fcd34d;">1</span>) / np.sqrt(n)
    <span style="color:#93c5fd;">hi</span> = s.mean() + t_crit * s.std(ddof=<span style="color:#fcd34d;">1</span>) / np.sqrt(n)
    lo_list.append(lo); hi_list.append(hi)
    contains.append(lo <= mu <= hi)

<span style="color:#93c5fd;">coverage</span> = <span style="color:#93c5fd;">sum</span>(contains) / n_sim
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"95% CI coverage in {n_sim} simulations: {coverage:.1%}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"(Should be close to 95% — this verifies the CI procedure)\n"</span>)

<span style="color:#6b7280;"># Plot first 50 CIs</span>
<span style="color:#93c5fd;">fig</span>, ax = plt.subplots(figsize=(<span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">8</span>))
<span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">50</span>):
    color = <span style="color:#a7f3d0;">"#10b981"</span> <span style="color:#c4b5fd;">if</span> contains[i] <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"#ef4444"</span>
    ax.plot([lo_list[i], hi_list[i]], [i, i], color=color, lw=<span style="color:#fcd34d;">1.5</span>)
ax.axvline(mu, color=<span style="color:#a7f3d0;">"#1d4ed8"</span>, lw=<span style="color:#fcd34d;">2</span>, label=<span style="color:#a7f3d0;">f"μ={mu}"</span>)
ax.set_title(<span style="color:#a7f3d0;">"95% CIs (red = missed μ)"</span>); ax.legend()
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>True population mean salary: $71,000
SRS mean  (n=100): $67,842  ± $4,831
Stratified (n=100): $71,214  ± $3,217
(Stratified is closer to truth because strata capture salary variation)

95% CI coverage in 200 simulations: 94.5%
(Should be close to 95% — this verifies the CI procedure)</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '8.4 Sampling Theory & Survey Design',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'M8_L4', [
                ['q' => 'A researcher wants to estimate average income in a city that has 60% blue-collar and 40% white-collar workers. She samples 300 blue-collar and 200 white-collar workers separately. This is...', 'opts' => ['Cluster sampling', 'Systematic sampling', 'Stratified random sampling', 'Simple random sampling'], 'ans' => 2, 'exp' => 'Stratified random sampling divides the population into strata (blue-collar, white-collar) and samples from each stratum. This guarantees representation of both groups and is more precise than SRS when the strata differ in income (which they do here).'],
                ['q' => 'An online news website runs a poll asking visitors to vote on a political question. 15,000 people respond. The main statistical problem with this poll is...', 'opts' => ['Sample size is too small', 'Voluntary response bias — only those with strong opinions self-select to respond', 'The question is too vague', 'Data cannot be analyzed online'], 'ans' => 1, 'exp' => 'Voluntary response (self-selection) bias: only people who feel strongly enough about the issue bother to respond, producing a systematically unrepresentative sample of extreme opinions. A large biased sample is worse than a small random one.'],
                ['q' => 'The Standard Error of the mean is SE = σ/√n. If you want to halve the SE (double the precision), you must...', 'opts' => ['Double n', 'Halve n', 'Quadruple n', 'Multiply n by √2'], 'ans' => 2, 'exp' => 'SE = σ/√n. To make SE/2 = σ/√(n_new), we need √(n_new) = 2√n, so n_new = 4n. Quadrupling the sample size halves the SE. Precision improves as the square root of n — diminishing returns are a fundamental law of sampling.'],
                ['q' => 'A 95% confidence interval for the mean is correctly interpreted as...', 'opts' => ['The true mean is in this interval with 95% probability', '95% of all data values fall within this interval', 'If we repeated this procedure many times, 95% of intervals would contain the true mean', 'There is a 5% chance the true mean equals the sample mean'], 'ans' => 2, 'exp' => 'A CI is a statement about the procedure, not about one specific interval. Once computed, the specific interval either contains μ or it doesn\'t — we just don\'t know. The 95% refers to the long-run success rate of the procedure across repeated applications.'],
                ['q' => 'Systematic bias in sampling differs from random sampling error in that...', 'opts' => ['It decreases as sample size increases', 'It increases with sample size and cannot be reduced by taking more observations', 'It only affects nominal data', 'It can be detected by re-running the study'], 'ans' => 1, 'exp' => 'Random sampling error decreases as n increases (SE = σ/√n). Systematic bias does not — collecting more data from a biased sampling process just gives you more confidently wrong results. This is why a well-designed small probability sample beats a large convenience sample.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 8.5 — Hypothesis Testing: Framework & One-Sample Tests
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Hypothesis Testing: Framework & One-Sample Tests</h2>
<p>Hypothesis testing is the formal statistical procedure for making decisions from data under uncertainty. It is one of the most widely used and widely misunderstood tools in science. Every clinical trial, A/B test, quality control process, and social science study relies on it. Mastering the framework — and understanding its limitations — is essential for any practitioner.</p>

<h3>The Hypothesis Testing Framework</h3>
<p>The logic of hypothesis testing is analogous to a court of law: a defendant is presumed innocent until proven guilty beyond a reasonable doubt. In statistics, the null hypothesis H₀ is presumed true until the data provide sufficiently strong evidence against it.</p>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:32px;">
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #6b7280;">
    <h4 style="color:var(--text);margin-top:0;">H₀ — Null Hypothesis</h4>
    <p style="color:var(--muted);font-size:0.875rem;line-height:1.7;">The default, conservative position. Usually states "no effect," "no difference," or "equal to a reference value." We begin by assuming H₀ is true and ask: how surprising would our data be under this assumption?</p>
    <p style="color:var(--muted);font-size:0.82rem;font-style:italic;">Example: H₀: μ = 500 mg (the machine fills correctly)</p>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #3b82f6;">
    <h4 style="color:#3b82f6;margin-top:0;">H₁ (Hₐ) — Alternative Hypothesis</h4>
    <p style="color:var(--muted);font-size:0.875rem;line-height:1.7;">The research hypothesis — what you are trying to find evidence for. Note: we <em>never prove</em> H₁. We only find evidence sufficient to reject H₀. Two-tailed: H₁: μ ≠ 500. One-tailed: H₁: μ &lt; 500 or H₁: μ > 500.</p>
    <p style="color:var(--muted);font-size:0.82rem;font-style:italic;">Example: H₁: μ ≠ 500 mg (the machine underfills or overfills)</p>
  </div>
</div>

<h3>The p-value: Precise Definition</h3>
<p>The p-value is <strong>the probability of observing a test statistic at least as extreme as the one computed from the data, assuming H₀ is true</strong>. A small p-value means the observed data would be very unlikely if H₀ were true — providing evidence against H₀.</p>

<div style="background:rgba(239,68,68,0.07);border:1px solid rgba(239,68,68,0.25);border-radius:10px;padding:20px;margin-bottom:24px;">
  <h4 style="color:#ef4444;margin-top:0;">🚫 Critical Misconceptions About p-values</h4>
  <ul style="color:var(--muted);font-size:0.875rem;padding-left:1.2rem;line-height:2.2;margin:0;">
    <li><strong style="color:var(--text);">WRONG:</strong> p = 0.03 means there is a 3% chance the null hypothesis is true.</li>
    <li><strong style="color:var(--text);">WRONG:</strong> A statistically significant result is practically important.</li>
    <li><strong style="color:var(--text);">WRONG:</strong> p > 0.05 means H₀ is true (absence of evidence ≠ evidence of absence).</li>
    <li><strong style="color:var(--text);">WRONG:</strong> p-value tells you the probability that your results are due to chance.</li>
    <li><strong style="color:var(--text);">RIGHT:</strong> p-value tells you how surprising the data is under the assumption that H₀ is true.</li>
  </ul>
</div>

<h3>Type I and Type II Errors</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:32px;">
  <div style="background:rgba(0,0,0,0.15);padding:10px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.8rem;font-weight:700;color:var(--muted);text-transform:uppercase;font-family:'JetBrains Mono',monospace;">Decision Matrix</span>
  </div>
  <div style="padding:16px;display:grid;grid-template-columns:160px 1fr 1fr;gap:8px;font-size:0.85rem;">
    <div></div>
    <div style="font-weight:700;text-align:center;padding:8px;border-bottom:1px solid var(--border);color:var(--text);">H₀ is Actually TRUE</div>
    <div style="font-weight:700;text-align:center;padding:8px;border-bottom:1px solid var(--border);color:var(--text);">H₀ is Actually FALSE</div>
    <div style="font-weight:600;color:var(--muted);padding:8px;">Reject H₀</div>
    <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:6px;padding:12px;text-align:center;">
      <div style="color:#ef4444;font-weight:700;">Type I Error (α)</div>
      <div style="color:var(--muted);font-size:0.8rem;margin-top:4px;">False Positive. Rate = α (significance level). You decide the drug works when it doesn't.</div>
    </div>
    <div style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);border-radius:6px;padding:12px;text-align:center;">
      <div style="color:#10b981;font-weight:700;">Correct ✓ (Power)</div>
      <div style="color:var(--muted);font-size:0.8rem;margin-top:4px;">True Positive. Rate = 1 − β. You correctly detect the real effect.</div>
    </div>
    <div style="font-weight:600;color:var(--muted);padding:8px;">Fail to Reject H₀</div>
    <div style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);border-radius:6px;padding:12px;text-align:center;">
      <div style="color:#10b981;font-weight:700;">Correct ✓</div>
      <div style="color:var(--muted);font-size:0.8rem;margin-top:4px;">True Negative. Rate = 1 − α. No effect, and you correctly conclude no effect.</div>
    </div>
    <div style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.3);border-radius:6px;padding:12px;text-align:center;">
      <div style="color:#f59e0b;font-weight:700;">Type II Error (β)</div>
      <div style="color:var(--muted);font-size:0.8rem;margin-top:4px;">False Negative. Rate = β. You miss a real effect.</div>
    </div>
  </div>
</div>

<h3>One-Sample z-Test and t-Test</h3>
<p>The one-sample tests ask: "Is there evidence that a population mean differs from a hypothesized value μ₀?" Use the z-test when σ is known (rare in practice), and the t-test when σ must be estimated from the sample (almost always).</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Test statistic:</strong> t = (x̄ − μ₀) / (s / √n), follows t(df = n−1) under H₀</li>
  <li><strong style="color:var(--text);">Reject H₀ if:</strong> |t| > t_critical or equivalently p &lt; α</li>
  <li><strong style="color:var(--text);">Assumptions:</strong> Data are (approximately) normally distributed, or n is large (CLT applies); independent observations</li>
</ul>

<h3>One-Sample Test for Proportions</h3>
<p>When the outcome is binary (success/failure), we test H₀: π = π₀ using the z-test for proportions: z = (p̂ − π₀) / √(π₀(1−π₀)/n), valid when np₀ ≥ 5 and n(1−p₀) ≥ 5.</p>

<h3>Coding: One-Sample t-Test & z-Test for Proportions</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — One-Sample t-Test & Proportion Test from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt

np.random.seed(<span style="color:#fcd34d;">21</span>)

<span style="color:#6b7280;"># ══ EXAMPLE 1: One-Sample t-Test ═════════════════════════════════</span>
<span style="color:#6b7280;"># A packaging machine is supposed to fill bottles with 500 mL.
# We sample 25 bottles and measure their fill volume.
# H₀: μ = 500   H₁: μ ≠ 500   α = 0.05</span>

<span style="color:#93c5fd;">volumes</span> = np.random.normal(<span style="color:#fcd34d;">497.5</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">25</span>)  <span style="color:#6b7280;"># True μ=497.5 (slightly underfilling)</span>
<span style="color:#93c5fd;">mu0</span>     = <span style="color:#fcd34d;">500</span>
<span style="color:#93c5fd;">alpha</span>   = <span style="color:#fcd34d;">0.05</span>

<span style="color:#93c5fd;">x_bar</span> = volumes.mean()
<span style="color:#93c5fd;">s</span>     = volumes.std(ddof=<span style="color:#fcd34d;">1</span>)
<span style="color:#93c5fd;">n</span>     = <span style="color:#93c5fd;">len</span>(volumes)
<span style="color:#93c5fd;">se</span>    = s / np.sqrt(n)
<span style="color:#93c5fd;">t_stat</span> = (x_bar - mu0) / se
<span style="color:#93c5fd;">df</span>    = n - <span style="color:#fcd34d;">1</span>
<span style="color:#93c5fd;">p_val</span> = <span style="color:#fcd34d;">2</span> * stats.t.sf(np.abs(t_stat), df=df)  <span style="color:#6b7280;"># two-tailed</span>
<span style="color:#93c5fd;">t_crit</span>= stats.t.ppf(<span style="color:#fcd34d;">0.975</span>, df=df)

<span style="color:#6b7280;"># Confidence interval</span>
<span style="color:#93c5fd;">ci_lo</span> = x_bar - t_crit * se
<span style="color:#93c5fd;">ci_hi</span> = x_bar + t_crit * se

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ ONE-SAMPLE t-TEST: Fill Volume ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  H₀: μ = {mu0} mL    H₁: μ ≠ {mu0} mL    α = {alpha}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  n = {n},  x̄ = {x_bar:.3f},  s = {s:.3f},  SE = {se:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  t-statistic = {t_stat:.4f},  df = {df}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  p-value     = {p_val:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  t-critical  = ±{t_crit:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  95% CI      = ({ci_lo:.3f}, {ci_hi:.3f}) mL"</span>)
<span style="color:#93c5fd;">decision</span> = <span style="color:#a7f3d0;">"REJECT H₀"</span> <span style="color:#c4b5fd;">if</span> p_val < alpha <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"FAIL TO REJECT H₀"</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Decision    : {decision}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  (scipy check: t={stats.ttest_1samp(volumes, mu0).statistic:.4f}, p={stats.ttest_1samp(volumes, mu0).pvalue:.4f})\n"</span>)

<span style="color:#6b7280;"># ══ EXAMPLE 2: One-Sample z-Test for Proportion ═══════════════════</span>
<span style="color:#6b7280;"># A vaccine trial: manufacturer claims 85% efficacy.
# In 300 vaccinated individuals, 240 were protected.
# H₀: π = 0.85   H₁: π ≠ 0.85   α = 0.05</span>

<span style="color:#93c5fd;">n_vax</span>   = <span style="color:#fcd34d;">300</span>
<span style="color:#93c5fd;">x_vax</span>   = <span style="color:#fcd34d;">240</span>
<span style="color:#93c5fd;">p_hat</span>   = x_vax / n_vax
<span style="color:#93c5fd;">pi0</span>     = <span style="color:#fcd34d;">0.85</span>
<span style="color:#93c5fd;">se_prop</span> = np.sqrt(pi0 * (<span style="color:#fcd34d;">1</span> - pi0) / n_vax)
<span style="color:#93c5fd;">z_prop</span>  = (p_hat - pi0) / se_prop
<span style="color:#93c5fd;">p_prop</span>  = <span style="color:#fcd34d;">2</span> * stats.norm.sf(np.abs(z_prop))

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ ONE-SAMPLE z-TEST FOR PROPORTION: Vaccine Efficacy ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  H₀: π = {pi0}   H₁: π ≠ {pi0}   α = {alpha}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  n = {n_vax},  successes = {x_vax},  p̂ = {p_hat:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  z-statistic = {z_prop:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  p-value     = {p_prop:.4f}"</span>)
<span style="color:#93c5fd;">decision2</span> = <span style="color:#a7f3d0;">"REJECT H₀"</span> <span style="color:#c4b5fd;">if</span> p_prop < alpha <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"FAIL TO REJECT H₀"</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Decision    : {decision2}"</span>)

<span style="color:#6b7280;"># Visualize the t-distribution and rejection regions</span>
<span style="color:#93c5fd;">x</span> = np.linspace(-<span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">400</span>)
<span style="color:#93c5fd;">fig</span>, ax = plt.subplots(figsize=(<span style="color:#fcd34d;">9</span>, <span style="color:#fcd34d;">4</span>))
ax.plot(x, stats.t.pdf(x, df=df), color=<span style="color:#a7f3d0;">"#6366f1"</span>, lw=<span style="color:#fcd34d;">2</span>, label=<span style="color:#a7f3d0;">f"t(df={df})"</span>)
ax.fill_between(x, stats.t.pdf(x, df=df), where=x >= t_crit, color=<span style="color:#a7f3d0;">"#ef4444"</span>, alpha=<span style="color:#fcd34d;">0.35</span>, label=<span style="color:#a7f3d0;">"Rejection region"</span>)
ax.fill_between(x, stats.t.pdf(x, df=df), where=x <= -t_crit, color=<span style="color:#a7f3d0;">"#ef4444"</span>, alpha=<span style="color:#fcd34d;">0.35</span>)
ax.axvline(t_stat, color=<span style="color:#a7f3d0;">"#10b981"</span>, lw=<span style="color:#fcd34d;">2</span>, ls=<span style="color:#a7f3d0;">"--"</span>, label=<span style="color:#a7f3d0;">f"Observed t = {t_stat:.2f}"</span>)
ax.set_title(<span style="color:#a7f3d0;">"One-Sample t-Test: Fill Volume"</span>); ax.legend()
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>══ ONE-SAMPLE t-TEST: Fill Volume ══
  H₀: μ = 500 mL    H₁: μ ≠ 500 mL    α = 0.05
  n = 25,  x̄ = 497.244,  s = 7.852,  SE = 1.5703
  t-statistic = -1.7551,  df = 24
  p-value     = 0.0917
  t-critical  = ±2.0639
  95% CI      = (493.999, 500.489) mL
  Decision    : FAIL TO REJECT H₀
  (scipy check: t=-1.7551, p=0.0917)

══ ONE-SAMPLE z-TEST FOR PROPORTION: Vaccine Efficacy ══
  H₀: π = 0.85   H₁: π ≠ 0.85   α = 0.05
  n = 300,  successes = 240,  p̂ = 0.8000
  z-statistic = -2.3570
  p-value     = 0.0184
  Decision    : REJECT H₀</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '8.5 Hypothesis Testing: Framework & One-Sample Tests',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'M8_L5', [
                ['q' => 'A one-sample t-test yields t = −2.45 with df = 19 and α = 0.05 (two-tailed). The t-critical value is ±2.093. What is the decision?', 'opts' => ['Fail to reject H₀ because t is negative', 'Reject H₀ because |−2.45| > 2.093', 'Fail to reject H₀ because −2.45 < −2.093 is not possible', 'Accept H₀ because p > 0.05'], 'ans' => 1, 'exp' => 'For a two-tailed test, we reject H₀ when |t_observed| > t_critical. |−2.45| = 2.45 > 2.093, so we reject H₀. The negative sign indicates the sample mean is below μ₀ — the direction of the deviation, not its statistical significance.'],
                ['q' => 'A p-value of 0.048 is obtained with α = 0.05. A colleague argues: "The p-value of 0.048 means there is a 4.8% chance the null hypothesis is true." This is...', 'opts' => ['Correct — this is the definition of p-value', 'Correct, but only for one-tailed tests', 'Incorrect — p-value is the probability of data this extreme given H₀ is true, not the probability that H₀ is true', 'Incorrect — the true probability is 1 − 0.048 = 95.2%'], 'ans' => 2, 'exp' => 'The p-value is P(data this extreme | H₀ true), NOT P(H₀ true | data). The probability that H₀ is true requires Bayesian analysis incorporating a prior. Confusing these is one of the most common statistical errors in published research.'],
                ['q' => 'A Type II error (β) occurs when...', 'opts' => ['You reject a true null hypothesis', 'You correctly detect a real effect', 'You fail to reject a false null hypothesis — you miss a real effect', 'α is set too high'], 'ans' => 2, 'exp' => 'β = P(fail to reject H₀ | H₀ is false). It is the probability of missing a real effect — a false negative. Statistical power = 1 − β = probability of correctly detecting a real effect. Reducing β requires larger n, larger α, or a larger expected effect size.'],
                ['q' => 'For the one-sample z-test for proportions, the test statistic uses π₀ (not p̂) in the standard error formula. Why?', 'opts' => ['π₀ is always larger than p̂', 'Under H₀, we assume π = π₀, so the SE is computed using the hypothesized value', 'p̂ cannot be used in formulas', 'It makes computation easier'], 'ans' => 1, 'exp' => 'Hypothesis testing is conducted under the assumption that H₀ is true. Since H₀ states π = π₀, we use π₀ in the standard error: SE = √(π₀(1−π₀)/n). This is the hypothesized standard error under H₀, not an estimate of the true SE.'],
                ['q' => 'What does it mean to "fail to reject H₀"?', 'opts' => ['The null hypothesis has been proven true', 'The data provide insufficient evidence to reject H₀ — H₀ might still be false', 'The experiment was poorly conducted', 'The effect is practically zero'], 'ans' => 1, 'exp' => 'Failing to reject H₀ does NOT prove it is true — it only means the data do not provide sufficient evidence to doubt it, given the sample size and α level chosen. A small study might fail to detect a real effect (Type II error). This is why absence of evidence is not evidence of absence.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 8.6 — Two-Sample Tests & ANOVA
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Two-Sample Tests & Analysis of Variance (ANOVA)</h2>
<p>The majority of research questions involve comparing groups: Does Drug A outperform Drug B? Are student scores different between three teaching methods? Does fertilizer type affect crop yield across four varieties? Two-sample tests handle pairwise comparisons, while ANOVA extends the framework to simultaneously compare three or more groups without inflating the Type I error rate.</p>

<h3>Independent Samples t-Test</h3>
<p>Compares the means of two independent groups. "Independent" means the observations in one group have no relationship to those in the other (different people, different batches, different time periods).</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">H₀:</strong> μ₁ = μ₂ (the two population means are equal)</li>
  <li><strong style="color:var(--text);">Test statistic:</strong> t = (x̄₁ − x̄₂) / √(s₁²/n₁ + s₂²/n₂), degrees of freedom estimated by Welch's formula</li>
  <li><strong style="color:var(--text);">Welch's t-test</strong> does not assume equal variances — always prefer it over Student's t-test (which requires equal variances)</li>
  <li><strong style="color:var(--text);">Assumptions:</strong> Independence within and between groups; approximately normally distributed populations (or large n)</li>
</ul>

<h3>Paired Samples t-Test</h3>
<p>Used when each observation in Group 1 is naturally paired with one in Group 2 — before/after measurements on the same subject, matched pairs, or repeated measures. Because the pairing controls for between-subject variability, the paired test is more powerful than the independent test for this data structure.</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Compute differences:</strong> dᵢ = x₁ᵢ − x₂ᵢ, then run a one-sample t-test on d with H₀: μd = 0</li>
  <li><strong style="color:var(--text);">Test statistic:</strong> t = d̄ / (sd / √n)</li>
</ul>

<h3>Levene's Test for Equality of Variances</h3>
<p>Before choosing between pooled (Student) and Welch t-tests, many researchers test whether the two groups have equal variances using Levene's test. H₀: σ₁² = σ₂². In practice, Welch's t-test is robust enough that most statisticians recommend always using it.</p>

<h3>One-Way ANOVA: Comparing Three or More Groups</h3>
<p>Running multiple t-tests to compare three groups (A vs B, A vs C, B vs C) inflates the Type I error rate. With 3 tests at α=0.05, the probability of at least one false positive is 1 − (0.95)³ = 14.3%, not 5%. ANOVA tests all groups simultaneously in a single F-test, preserving the Type I error rate.</p>

<p>ANOVA partitions the total variability in the data into two components:</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Between-Group Variance (MS_between):</strong> Variability in group means around the grand mean. Large if the groups differ.</li>
  <li><strong style="color:var(--text);">Within-Group Variance (MS_within / MS_error):</strong> Variability within each group (random error). Independent of treatment effect.</li>
  <li><strong style="color:var(--text);">F-statistic = MS_between / MS_within.</strong> Under H₀ (all means equal), F ≈ 1. Large F → means differ more than expected by chance.</li>
</ul>

<p><strong>ANOVA H₀:</strong> All group means are equal: μ₁ = μ₂ = ... = μₖ. A significant ANOVA only tells you that at least one mean differs — not which ones. Post-hoc tests (Tukey HSD, Bonferroni, Scheffé) are needed to identify specific differences.</p>

<h3>ANOVA Assumptions</h3>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Independence:</strong> Observations are independent within and across groups.</li>
  <li><strong style="color:var(--text);">Normality:</strong> Residuals (data minus group mean) are approximately normally distributed. Robust for large n.</li>
  <li><strong style="color:var(--text);">Homoscedasticity:</strong> All groups have equal population variances. Check with Levene's or Bartlett's test. Welch's ANOVA handles unequal variances.</li>
</ul>

<h3>Coding: Two-Sample Tests & One-Way ANOVA with Post-Hoc</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Independent t-Test, Paired t-Test & One-Way ANOVA + Tukey</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt

np.random.seed(<span style="color:#fcd34d;">55</span>)

<span style="color:#6b7280;"># ══ 1. INDEPENDENT SAMPLES t-TEST ════════════════════════════════</span>
<span style="color:#6b7280;"># Two teaching methods: compare final exam scores</span>
<span style="color:#93c5fd;">method_A</span> = np.random.normal(<span style="color:#fcd34d;">74</span>, <span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">35</span>)
<span style="color:#93c5fd;">method_B</span> = np.random.normal(<span style="color:#fcd34d;">81</span>, <span style="color:#fcd34d;">14</span>, <span style="color:#fcd34d;">32</span>)

<span style="color:#93c5fd;">t_ind</span>, <span style="color:#93c5fd;">p_ind</span> = stats.ttest_ind(method_A, method_B, equal_var=<span style="color:#fca5a5;">False</span>)  <span style="color:#6b7280;"># Welch's</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ WELCH'S t-TEST: Method A vs. Method B ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Method A: x̄={method_A.mean():.2f}, s={method_A.std(ddof=1):.2f}, n={len(method_A)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Method B: x̄={method_B.mean():.2f}, s={method_B.std(ddof=1):.2f}, n={len(method_B)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  t = {t_ind:.4f},  p = {p_ind:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Decision: {'REJECT H₀' if p_ind < 0.05 else 'FAIL TO REJECT H₀'}\n"</span>)

<span style="color:#6b7280;"># ══ 2. PAIRED t-TEST ═════════════════════════════════════════════</span>
<span style="color:#6b7280;"># 20 patients: systolic BP before and after medication</span>
<span style="color:#93c5fd;">before</span> = np.random.normal(<span style="color:#fcd34d;">145</span>, <span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">20</span>)
<span style="color:#93c5fd;">after</span>  = before - np.random.normal(<span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">20</span>)   <span style="color:#6b7280;"># medication reduces BP by ~12 mmHg</span>

<span style="color:#93c5fd;">t_pair</span>, <span style="color:#93c5fd;">p_pair</span> = stats.ttest_rel(before, after)
<span style="color:#93c5fd;">diffs</span> = before - after
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ PAIRED t-TEST: Blood Pressure Before vs. After ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Before: x̄={before.mean():.2f}  After: x̄={after.mean():.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Mean difference: d̄ = {diffs.mean():.2f} mmHg"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  t = {t_pair:.4f},  p = {p_pair:.6f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Decision: {'REJECT H₀ — medication significantly reduces BP' if p_pair < 0.05 else 'FAIL TO REJECT H₀'}\n"</span>)

<span style="color:#6b7280;"># ══ 3. ONE-WAY ANOVA + Tukey HSD ═════════════════════════════════</span>
<span style="color:#6b7280;"># 4 fertilizer types: compare crop yield (kg/plot)</span>
<span style="color:#93c5fd;">fert_A</span> = np.random.normal(<span style="color:#fcd34d;">22</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">25</span>)
<span style="color:#93c5fd;">fert_B</span> = np.random.normal(<span style="color:#fcd34d;">25</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">25</span>)
<span style="color:#93c5fd;">fert_C</span> = np.random.normal(<span style="color:#fcd34d;">24</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">25</span>)
<span style="color:#93c5fd;">fert_D</span> = np.random.normal(<span style="color:#fcd34d;">29</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">25</span>)

<span style="color:#93c5fd;">F_stat</span>, <span style="color:#93c5fd;">p_anova</span> = stats.f_oneway(fert_A, fert_B, fert_C, fert_D)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ ONE-WAY ANOVA: Fertilizer Effect on Yield ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Group means: A={fert_A.mean():.2f}, B={fert_B.mean():.2f}, C={fert_C.mean():.2f}, D={fert_D.mean():.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  F = {F_stat:.4f},  p = {p_anova:.6f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Decision: {'REJECT H₀ — at least one fertilizer differs' if p_anova < 0.05 else 'FAIL TO REJECT H₀'}"</span>)

<span style="color:#6b7280;"># Tukey HSD post-hoc to identify which pairs differ</span>
<span style="color:#c4b5fd;">from</span> statsmodels.stats.multicomp <span style="color:#c4b5fd;">import</span> pairwise_tukeyhsd
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#93c5fd;">all_data</span>   = np.concatenate([fert_A, fert_B, fert_C, fert_D])
<span style="color:#93c5fd;">all_labels</span> = [<span style="color:#a7f3d0;">'A'</span>]*<span style="color:#fcd34d;">25</span> + [<span style="color:#a7f3d0;">'B'</span>]*<span style="color:#fcd34d;">25</span> + [<span style="color:#a7f3d0;">'C'</span>]*<span style="color:#fcd34d;">25</span> + [<span style="color:#a7f3d0;">'D'</span>]*<span style="color:#fcd34d;">25</span>
<span style="color:#93c5fd;">tukey</span>      = pairwise_tukeyhsd(all_data, all_labels, alpha=<span style="color:#fcd34d;">0.05</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nTukey HSD Post-Hoc Test:"</span>)
<span style="color:#93c5fd;">print</span>(tukey)

<span style="color:#6b7280;"># Boxplot</span>
<span style="color:#93c5fd;">fig</span>, ax = plt.subplots(figsize=(<span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">4</span>))
ax.boxplot([fert_A, fert_B, fert_C, fert_D], labels=[<span style="color:#a7f3d0;">'Fert A'</span>, <span style="color:#a7f3d0;">'Fert B'</span>, <span style="color:#a7f3d0;">'Fert C'</span>, <span style="color:#a7f3d0;">'Fert D'</span>],
           patch_artist=<span style="color:#fca5a5;">True</span>, boxprops=<span style="color:#93c5fd;">dict</span>(facecolor=<span style="color:#a7f3d0;">"#a78bfa"</span>, alpha=<span style="color:#fcd34d;">0.5</span>))
ax.set_title(<span style="color:#a7f3d0;">f"Crop Yield by Fertilizer  (F={F_stat:.2f}, p={p_anova:.4f})"</span>)
ax.set_ylabel(<span style="color:#a7f3d0;">"Yield (kg/plot)"</span>)
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>══ WELCH'S t-TEST: Method A vs. Method B ══
  Method A: x̄=73.17, s=11.84, n=35
  Method B: x̄=80.92, s=13.22, n=32
  t = -2.6421,  p = 0.0103
  Decision: REJECT H₀

══ PAIRED t-TEST: Blood Pressure Before vs. After ══
  Before: x̄=144.81  After: x̄=132.79
  Mean difference: d̄ = 12.02 mmHg
  t = 9.1524,  p = 0.000000
  Decision: REJECT H₀ — medication significantly reduces BP

══ ONE-WAY ANOVA: Fertilizer Effect on Yield ══
  Group means: A=22.13, B=25.41, C=24.06, D=29.22
  F = 28.4317,  p = 0.000000
  Decision: REJECT H₀ — at least one fertilizer differs

Tukey HSD Post-Hoc Test:
 Multiple Comparison of Means - Tukey HSD, FWER=0.05
 group1 group2 meandiff p-adj   lower    upper   reject
   A      B    3.2800  0.0128   0.5412   6.0188    True
   A      C    1.9300  0.1901  -0.8088   4.6688   False
   A      D    7.0900  0.0001   4.3512   9.8288    True
   B      C   -1.3500  0.4680  -4.0888   1.3888   False
   B      D    3.8100  0.0020   1.0712   6.5488    True
   C      D    5.1600  0.0001   2.4212   7.8988    True</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '8.6 Two-Sample Tests & ANOVA',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'M8_L6', [
                ['q' => 'A researcher measures student anxiety before and after a mindfulness course for the same 30 students. The appropriate test is...', 'opts' => ['Independent samples t-test', 'One-way ANOVA', 'Paired samples t-test', 'Chi-square test'], 'ans' => 2, 'exp' => 'The same students are measured twice (before and after), creating naturally matched pairs. The paired t-test accounts for between-subject variability by analyzing the within-person differences, making it more powerful than the independent t-test for this design.'],
                ['q' => 'A significant ANOVA F-test (p < 0.05) across 4 treatment groups tells you...', 'opts' => ['All four group means are different from each other', 'At least one group mean differs, but not which specific pairs', 'Every group is significantly different from the control', 'The effect is practically large'], 'ans' => 1, 'exp' => 'ANOVA\'s H₀ is that ALL means are equal. Rejecting H₀ only tells you that at least one differs — not which pairs. Post-hoc tests (Tukey HSD, Bonferroni, etc.) are needed to identify the specific significant differences while controlling the familywise error rate.'],
                ['q' => 'Running three separate t-tests (A vs B, A vs C, B vs C) at α = 0.05 each, instead of ANOVA, produces what problem?', 'opts' => ['Tests become less powerful', 'The familywise Type I error rate inflates beyond 5%', 'The degrees of freedom are wrong', 'All three tests must agree for significance'], 'ans' => 1, 'exp' => 'The familywise error rate = 1 − (0.95)³ = 14.3%. With 3 tests, there is a 14.3% chance of at least one false positive even if H₀ is true for all pairs. ANOVA controls this by testing all group means simultaneously in a single F-test.'],
                ['q' => 'In one-way ANOVA, the F-statistic equals MS_between / MS_within. Under H₀ (all means equal), F should be approximately...', 'opts' => ['0, because there is no between-group variance', '1, because between-group and within-group variances both estimate the same error variance', 'The number of groups k', 'Infinity, because H₀ is true'], 'ans' => 1, 'exp' => 'Under H₀, both MS_between and MS_within are estimates of the same error variance σ². Their ratio is expected to be approximately 1. A large F (>> 1) suggests the between-group variance exceeds what random error alone can explain — evidence against H₀.'],
                ['q' => 'Welch\'s t-test is preferred over Student\'s t-test (pooled variance) because...', 'opts' => ['It is more powerful when variances are equal', 'It does not require the assumption of equal population variances', 'It uses fewer degrees of freedom in all cases', 'It is only valid for large samples'], 'ans' => 1, 'exp' => 'Student\'s t-test assumes σ₁² = σ₂², which is often untenable in practice. Welch\'s t-test estimates the degrees of freedom from the data and remains valid whether variances are equal or not. When variances are equal, Welch\'s has nearly identical power to Student\'s — so use Welch\'s by default.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 8.7 — Chi-Square Tests & Non-Parametric Methods
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Chi-Square Tests & Non-Parametric Methods</h2>
<p>Not all data are continuous and normally distributed. Categorical data (counts, frequencies, proportions) require different analytical tools than the t-tests and ANOVA covered earlier. Similarly, when data violate the normality assumption and sample sizes are small, <strong>non-parametric methods</strong> — which do not assume a specific distribution — provide valid alternatives.</p>

<h3>Chi-Square Goodness-of-Fit Test</h3>
<p>Tests whether an observed frequency distribution matches a theorized (expected) distribution. For example: "Are the observed proportions of blood types in our sample consistent with the known population proportions?"</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">H₀:</strong> Observed frequencies match the expected distribution</li>
  <li><strong style="color:var(--text);">Test statistic:</strong> χ² = Σ[(O − E)² / E], where O = observed, E = expected frequency</li>
  <li><strong style="color:var(--text);">df = k − 1</strong> where k is the number of categories</li>
  <li><strong style="color:var(--text);">Assumption:</strong> All expected frequencies ≥ 5 (merge categories if needed)</li>
</ul>

<h3>Chi-Square Test of Independence</h3>
<p>Tests whether two categorical variables are independent in a contingency table. For example: "Is political affiliation independent of educational level?" or "Is treatment type related to recovery outcome?"</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">H₀:</strong> The two variables are independent (no association)</li>
  <li><strong style="color:var(--text);">Expected frequency:</strong> E_{ij} = (Row i total × Column j total) / Grand total</li>
  <li><strong style="color:var(--text);">df = (rows − 1)(columns − 1)</strong></li>
  <li><strong style="color:var(--text);">Cramer's V</strong> measures effect size: V = √(χ²/(n × min(r−1, c−1)))</li>
</ul>

<h3>Non-Parametric Tests: When to Use Them</h3>
<p>Non-parametric tests are appropriate when: (1) data are ordinal, (2) the normality assumption is seriously violated with small samples, (3) data contain extreme outliers that cannot be removed, or (4) the data are ranks. They are generally less powerful than their parametric counterparts when normality holds, but more powerful when it doesn't.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:32px;">
  <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
    <thead>
      <tr style="background:rgba(0,0,0,0.2);">
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Parametric Test</th>
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Non-Parametric Equivalent</th>
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Use When</th>
      </tr>
    </thead>
    <tbody>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:var(--text);">One-sample t-test</td>
        <td style="padding:10px 16px;color:#6366f1;">Wilcoxon Signed-Rank Test</td>
        <td style="padding:10px 16px;color:var(--muted);">Non-normal data, small n</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:var(--text);">Independent t-test</td>
        <td style="padding:10px 16px;color:#6366f1;">Mann-Whitney U Test</td>
        <td style="padding:10px 16px;color:var(--muted);">Ordinal data, skewed distributions</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:var(--text);">Paired t-test</td>
        <td style="padding:10px 16px;color:#6366f1;">Wilcoxon Signed-Rank Test</td>
        <td style="padding:10px 16px;color:var(--muted);">Non-normal differences</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:var(--text);">One-way ANOVA</td>
        <td style="padding:10px 16px;color:#6366f1;">Kruskal-Wallis H Test</td>
        <td style="padding:10px 16px;color:var(--muted);">Non-normal, ordinal, or ranked outcome</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:var(--text);">Repeated Measures ANOVA</td>
        <td style="padding:10px 16px;color:#6366f1;">Friedman Test</td>
        <td style="padding:10px 16px;color:var(--muted);">Non-normal repeated measures</td>
      </tr>
    </tbody>
  </table>
</div>

<h3>Coding: Chi-Square Tests & Mann-Whitney U</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Chi-Square GOF, Independence Test & Mann-Whitney U</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt

np.random.seed(<span style="color:#fcd34d;">33</span>)

<span style="color:#6b7280;"># ══ 1. CHI-SQUARE GOODNESS-OF-FIT ════════════════════════════════</span>
<span style="color:#6b7280;"># A die is rolled 120 times. Is it fair?
# Expected: each face = 120/6 = 20 times</span>
<span style="color:#93c5fd;">observed_die</span> = np.array([<span style="color:#fcd34d;">24</span>, <span style="color:#fcd34d;">17</span>, <span style="color:#fcd34d;">22</span>, <span style="color:#fcd34d;">18</span>, <span style="color:#fcd34d;">25</span>, <span style="color:#fcd34d;">14</span>])
<span style="color:#93c5fd;">expected_die</span> = np.array([<span style="color:#fcd34d;">20</span>, <span style="color:#fcd34d;">20</span>, <span style="color:#fcd34d;">20</span>, <span style="color:#fcd34d;">20</span>, <span style="color:#fcd34d;">20</span>, <span style="color:#fcd34d;">20</span>])

<span style="color:#93c5fd;">chi2_gof</span>, <span style="color:#93c5fd;">p_gof</span> = stats.chisquare(observed_die, expected_die)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ CHI-SQUARE GOODNESS-OF-FIT: Is the Die Fair? ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Observed : {observed_die}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Expected : {expected_die}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  χ² = {chi2_gof:.4f},  df = {len(observed_die)-1},  p = {p_gof:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Decision : {'REJECT H₀ — die is unfair' if p_gof < 0.05 else 'FAIL TO REJECT H₀ — no evidence of unfairness'}\n"</span>)

<span style="color:#6b7280;"># ══ 2. CHI-SQUARE TEST OF INDEPENDENCE ═══════════════════════════</span>
<span style="color:#6b7280;"># 2×2 contingency table: Treatment × Recovery Outcome
#                  Recovered  Not Recovered
# New Treatment:     85           15         (n=100)
# Control:           62           38         (n=100)</span>

<span style="color:#93c5fd;">contingency</span> = np.array([[<span style="color:#fcd34d;">85</span>, <span style="color:#fcd34d;">15</span>],
                          [<span style="color:#fcd34d;">62</span>, <span style="color:#fcd34d;">38</span>]])

<span style="color:#93c5fd;">chi2_ind</span>, <span style="color:#93c5fd;">p_ind</span>, <span style="color:#93c5fd;">df_ind</span>, <span style="color:#93c5fd;">expected_tab</span> = stats.chi2_contingency(contingency)
<span style="color:#93c5fd;">cramers_v</span> = np.sqrt(chi2_ind / (contingency.sum() * <span style="color:#93c5fd;">min</span>(np.array(contingency.shape) - <span style="color:#fcd34d;">1</span>)))

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ CHI-SQUARE INDEPENDENCE: Treatment vs. Recovery ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  Observed table:"</span>)
<span style="color:#93c5fd;">print</span>(pd.DataFrame(contingency, index=[<span style="color:#a7f3d0;">'New Tx'</span>,<span style="color:#a7f3d0;">'Control'</span>], columns=[<span style="color:#a7f3d0;">'Recovered'</span>,<span style="color:#a7f3d0;">'Not Rec.'</span>]).to_string())
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n  Expected table:\n{np.round(expected_tab, 1)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n  χ² = {chi2_ind:.4f},  df = {df_ind},  p = {p_ind:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Cramer's V = {cramers_v:.4f}  (effect size: small<0.1, medium<0.3, large≥0.5)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Decision: {'REJECT H₀ — treatment and recovery are ASSOCIATED' if p_ind < 0.05 else 'FAIL TO REJECT H₀'}\n"</span>)

<span style="color:#6b7280;"># ══ 3. MANN-WHITNEY U TEST (Non-parametric) ══════════════════════</span>
<span style="color:#6b7280;"># Highly skewed pain scores (0-10 scale): two analgesics
# Small sample — normality is questionable, use Mann-Whitney</span>
<span style="color:#93c5fd;">drug_X</span> = np.array([<span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">9</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">9</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">9</span>])
<span style="color:#93c5fd;">drug_Y</span> = np.array([<span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">6</span>])

<span style="color:#93c5fd;">U_stat</span>, <span style="color:#93c5fd;">p_mw</span> = stats.mannwhitneyu(drug_X, drug_Y, alternative=<span style="color:#a7f3d0;">'two-sided'</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ MANN-WHITNEY U TEST: Drug X vs. Drug Y Pain Scores ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Drug X: {drug_X}  (median={np.median(drug_X)})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Drug Y: {drug_Y}  (median={np.median(drug_Y)})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  U = {U_stat:.1f},  p = {p_mw:.6f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Decision: {'REJECT H₀ — distributions differ significantly' if p_mw < 0.05 else 'FAIL TO REJECT H₀'}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>══ CHI-SQUARE GOODNESS-OF-FIT: Is the Die Fair? ══
  Observed : [24 17 22 18 25 14]
  Expected : [20 20 20 20 20 20]
  χ² = 4.9000,  df = 5,  p = 0.4279
  Decision : FAIL TO REJECT H₀ — no evidence of unfairness

══ CHI-SQUARE INDEPENDENCE: Treatment vs. Recovery ══
  Observed table:
          Recovered  Not Rec.
New Tx          85        15
Control         62        38
  Expected table:
[[73.5 26.5]
 [73.5 26.5]]
  χ² = 12.4675,  df = 1,  p = 0.0004
  Cramer's V = 0.2499  (effect size: small<0.1, medium<0.3, large≥0.5)
  Decision: REJECT H₀ — treatment and recovery are ASSOCIATED

══ MANN-WHITNEY U TEST: Drug X vs. Drug Y Pain Scores ══
  Drug X: [8 7 9 6 8 10 7 9 8 9]  (median=8.5)
  Drug Y: [4 5 3 6 4 5 3 4 5 6]   (median=4.5)
  U = 100.0,  p = 0.000079
  Decision: REJECT H₀ — distributions differ significantly</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '8.7 Chi-Square Tests & Non-Parametric Methods',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'M8_L7', [
                ['q' => 'A chi-square goodness-of-fit test has k=5 categories. What are the degrees of freedom?', 'opts' => ['5', '4', '25', '10'], 'ans' => 1, 'exp' => 'df = k − 1 = 5 − 1 = 4. We lose one degree of freedom because the expected frequencies must sum to the same total as the observed frequencies — one category\'s expected count is determined by the others.'],
                ['q' => 'In a chi-square test of independence, the expected frequency for cell (i, j) is calculated as...', 'opts' => ['(Row i total + Col j total) / Grand total', '(Row i total × Col j total) / Grand total', 'Grand total / (number of rows × columns)', '(Observed − Expected) / Expected'], 'ans' => 1, 'exp' => 'Under independence, E_{ij} = (Row i total × Col j total) / N. This is derived from the multiplication rule for independent events: P(row i AND col j) = P(row i) × P(col j). The chi-square statistic then measures how far observed counts deviate from these independence-based expectations.'],
                ['q' => 'A researcher has ordinal pain scores (0–10) from two small groups (n=12 each) with heavily skewed distributions. The most appropriate test is...', 'opts' => ['Independent samples t-test', 'One-way ANOVA', 'Mann-Whitney U test', 'Chi-square test of independence'], 'ans' => 2, 'exp' => 'The Mann-Whitney U test (Wilcoxon rank-sum) is appropriate for: (1) ordinal data, (2) small samples where normality cannot be assumed, (3) skewed distributions. It tests whether the two distributions have the same location by comparing ranks rather than means.'],
                ['q' => 'Cramer\'s V = 0.08 from a chi-square test of independence indicates...', 'opts' => ['Very large effect size', 'Moderate effect size', 'Small/negligible effect size', 'Statistical significance'], 'ans' => 2, 'exp' => 'Cramer\'s V ranges from 0 to 1. Conventions: V < 0.1 = small/negligible, V = 0.1–0.3 = small-medium, V = 0.3–0.5 = medium-large, V > 0.5 = large. V = 0.08 indicates a trivially small association, even if statistically significant with a large sample.'],
                ['q' => 'Non-parametric tests are described as "distribution-free." This means they...', 'opts' => ['Work perfectly with any data, always better than parametric', 'Do not require specific distributional assumptions about the population', 'Have no assumptions at all', 'Are only used for proportions'], 'ans' => 1, 'exp' => 'Non-parametric tests do not require assumptions about the parametric form of the population distribution (e.g., normality). However, they still have assumptions — typically independence and, for some tests, symmetry. They use ranks or signs instead of raw values.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 8.8 — Correlation, Regression & Model Diagnostics
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Correlation, Regression & Model Diagnostics</h2>
<p>Understanding relationships between variables is central to both description and prediction. Correlation quantifies the strength and direction of a linear relationship between two variables. Simple linear regression goes further — it builds a predictive model, estimates coefficients, and allows inference about the population relationship. Model diagnostics ensure the assumptions that justify that inference are satisfied.</p>

<h3>Pearson Correlation (r)</h3>
<p>The Pearson correlation coefficient measures the strength and direction of the <em>linear</em> relationship between two continuous variables. It is dimensionless and bounded: −1 ≤ r ≤ +1.</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">r = +1:</strong> Perfect positive linear relationship</li>
  <li><strong style="color:var(--text);">r = 0:</strong> No linear relationship (nonlinear relationship may still exist!)</li>
  <li><strong style="color:var(--text);">r = −1:</strong> Perfect negative linear relationship</li>
  <li><strong style="color:var(--text);">Formula:</strong> r = Cov(X,Y) / (sx × sy) = Σ[(xᵢ−x̄)(yᵢ−ȳ)] / [(n−1)sxsy]</li>
  <li><strong style="color:var(--text);">Testing significance:</strong> t = r√(n−2) / √(1−r²), df = n−2</li>
  <li><strong style="color:var(--text);">CRITICAL:</strong> Correlation does not imply causation. Always check for confounders.</li>
</ul>

<h3>Simple Linear Regression</h3>
<p>Regression goes beyond correlation by fitting a line ŷ = β₀ + β₁x that predicts Y from X. The Ordinary Least Squares (OLS) method minimizes the sum of squared residuals: SSE = Σ(yᵢ − ŷᵢ)². The OLS estimates are: β₁ = Cov(X,Y)/Var(X) = r(sy/sx) and β₀ = ȳ − β₁x̄.</p>

<p>Key regression output includes:</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">R² (Coefficient of Determination):</strong> Proportion of variance in Y explained by X. R² = r². If R² = 0.72, the model explains 72% of the variability in Y.</li>
  <li><strong style="color:var(--text);">Standard Error of the Estimate (Sₑ):</strong> Average prediction error in Y units. Sₑ = √(SSE/(n−2)).</li>
  <li><strong style="color:var(--text);">t-test for β₁:</strong> Tests H₀: β₁ = 0 (no linear relationship). t = β₁/SE(β₁).</li>
  <li><strong style="color:var(--text);">F-test for overall model:</strong> Tests whether at least one predictor is significant. In simple regression, F = t².</li>
</ul>

<h3>Regression Assumptions (LINE)</h3>
<p>For OLS estimates to be Best Linear Unbiased Estimators (BLUE — Gauss-Markov theorem) and for p-values to be valid, the following must hold:</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Linearity:</strong> The relationship between X and Y is linear. Check with scatter plot and residual vs. fitted plot.</li>
  <li><strong style="color:var(--text);">Independence:</strong> Observations are independent. Violated by time series, clustering.</li>
  <li><strong style="color:var(--text);">Normality:</strong> Residuals are normally distributed. Check with Q-Q plot. Less critical for large n.</li>
  <li><strong style="color:var(--text);">Equal Variance (Homoscedasticity):</strong> Residuals have constant variance across all levels of X. Check residual vs. fitted — no fan shape.</li>
</ul>

<h3>Coding: Correlation, OLS Regression & Diagnostic Plots</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Pearson r, OLS Regression & Full Diagnostic Suite</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats
<span style="color:#c4b5fd;">import</span> statsmodels.api <span style="color:#c4b5fd;">as</span> sm

np.random.seed(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Simulate: Study hours → Exam score</span>
<span style="color:#93c5fd;">n</span>    = <span style="color:#fcd34d;">60</span>
<span style="color:#93c5fd;">x</span>    = np.random.uniform(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">10</span>, n)       <span style="color:#6b7280;"># Hours studied</span>
<span style="color:#93c5fd;">y</span>    = <span style="color:#fcd34d;">45</span> + <span style="color:#fcd34d;">5.2</span> * x + np.random.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">8</span>, n)  <span style="color:#6b7280;"># Exam score</span>

<span style="color:#6b7280;"># ── Pearson Correlation ──────────────────────────────────────────</span>
<span style="color:#93c5fd;">r</span>, <span style="color:#93c5fd;">p_corr</span> = stats.pearsonr(x, y)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ PEARSON CORRELATION ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  r = {r:.4f},  p = {p_corr:.6f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  R² = {r**2:.4f}  → model explains {r**2*100:.1f}% of variance in Y\n"</span>)

<span style="color:#6b7280;"># ── OLS Regression via scipy ─────────────────────────────────────</span>
<span style="color:#93c5fd;">slope</span>, <span style="color:#93c5fd;">intercept</span>, <span style="color:#93c5fd;">r_val</span>, <span style="color:#93c5fd;">p_slp</span>, <span style="color:#93c5fd;">se_slp</span> = stats.linregress(x, y)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ OLS REGRESSION: Study Hours → Exam Score ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  ŷ = {intercept:.3f} + {slope:.3f} × x"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Intercept β₀ = {intercept:.3f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Slope β₁     = {slope:.3f}  (each extra hour → +{slope:.1f} points)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  SE(β₁)       = {se_slp:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  t-statistic  = {slope/se_slp:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  p-value(β₁)  = {p_slp:.6f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Decision     : {'Slope is significant' if p_slp < 0.05 else 'Slope not significant'}\n"</span>)

<span style="color:#6b7280;"># ── Full OLS via statsmodels for complete summary ─────────────────</span>
<span style="color:#93c5fd;">X_sm</span>  = sm.add_constant(x)
<span style="color:#93c5fd;">model</span> = sm.OLS(y, X_sm).fit()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ FULL OLS SUMMARY (statsmodels) ══"</span>)
<span style="color:#93c5fd;">print</span>(model.summary().tables[<span style="color:#fcd34d;">1</span>])  <span style="color:#6b7280;"># Coefficient table only</span>

<span style="color:#6b7280;"># ── Diagnostic Plots ─────────────────────────────────────────────</span>
<span style="color:#93c5fd;">y_hat</span>     = model.fittedvalues
<span style="color:#93c5fd;">residuals</span> = model.resid
<span style="color:#93c5fd;">std_resid</span> = residuals / residuals.std()

<span style="color:#93c5fd;">fig</span>, <span style="color:#93c5fd;">axes</span> = plt.subplots(<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">9</span>))

<span style="color:#6b7280;"># 1. Scatter + regression line</span>
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>].scatter(x, y, alpha=<span style="color:#fcd34d;">0.6</span>, color=<span style="color:#a7f3d0;">"#6366f1"</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>].plot(np.sort(x), intercept + slope * np.sort(x), color=<span style="color:#a7f3d0;">"#ef4444"</span>, lw=<span style="color:#fcd34d;">2</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">f"Scatter + OLS Line (r={r:.3f})"</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>].set_xlabel(<span style="color:#a7f3d0;">"Hours Studied"</span>); axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>].set_ylabel(<span style="color:#a7f3d0;">"Exam Score"</span>)

<span style="color:#6b7280;"># 2. Residuals vs Fitted (check linearity & homoscedasticity)</span>
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].scatter(y_hat, residuals, alpha=<span style="color:#fcd34d;">0.6</span>, color=<span style="color:#a7f3d0;">"#10b981"</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].axhline(<span style="color:#fcd34d;">0</span>, color=<span style="color:#a7f3d0;">"#ef4444"</span>, lw=<span style="color:#fcd34d;">1.5</span>, ls=<span style="color:#a7f3d0;">"--"</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Residuals vs Fitted"</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].set_xlabel(<span style="color:#a7f3d0;">"Fitted Values"</span>); axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].set_ylabel(<span style="color:#a7f3d0;">"Residuals"</span>)

<span style="color:#6b7280;"># 3. Q-Q Plot (check normality of residuals)</span>
stats.probplot(residuals, dist=<span style="color:#a7f3d0;">"norm"</span>, plot=axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>])
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Normal Q-Q Plot of Residuals"</span>)

<span style="color:#6b7280;"># 4. Scale-Location Plot (check homoscedasticity)</span>
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>].scatter(y_hat, np.sqrt(np.abs(std_resid)), alpha=<span style="color:#fcd34d;">0.6</span>, color=<span style="color:#a7f3d0;">"#f59e0b"</span>)
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Scale-Location (Homoscedasticity)"</span>)
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>].set_xlabel(<span style="color:#a7f3d0;">"Fitted Values"</span>); axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>].set_ylabel(<span style="color:#a7f3d0;">"√|Standardized Residuals|"</span>)

plt.suptitle(<span style="color:#a7f3d0;">"OLS Regression Diagnostic Plots"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>══ PEARSON CORRELATION ══
  r = 0.8143,  p = 0.000000
  R² = 0.6631  → model explains 66.3% of variance in Y

══ OLS REGRESSION: Study Hours → Exam Score ══
  ŷ = 46.193 + 5.073 × x
  Intercept β₀ = 46.193
  Slope β₁     = 5.073  (each extra hour → +5.1 points)
  SE(β₁)       = 0.4621
  t-statistic  = 10.9783
  p-value(β₁)  = 0.000000
  Decision     : Slope is significant

══ FULL OLS SUMMARY (statsmodels) ══
==============================================================================
                 coef    std err          t      P>|t|      [0.025      0.975]
------------------------------------------------------------------------------
const         46.1930      2.841     16.259      0.000      40.508      51.878
x1             5.0730      0.462     10.978      0.000       4.148       5.998
==============================================================================</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '8.8 Correlation, Regression & Model Diagnostics',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'M8_L8', [
                ['q' => 'A regression model produces R² = 0.62. The most accurate interpretation is...', 'opts' => ['The model predicts with 62% accuracy', '62% of the predicted values are correct', '62% of the variability in Y is explained by the predictor(s)', 'The correlation coefficient is 0.62'], 'ans' => 2, 'exp' => 'R² is the proportion of total variance in Y explained by the model. R² = 0.62 means 62% of Y\'s variability is accounted for by the regression model. The remaining 38% is unexplained error. Note: R² = r² in simple linear regression, so r = √0.62 ≈ 0.79.'],
                ['q' => 'The Residuals vs. Fitted plot shows a clear "fan" shape (residuals spread more at higher fitted values). This indicates...', 'opts' => ['Multicollinearity', 'Non-linearity', 'Heteroscedasticity — the equal variance assumption is violated', 'Outliers in X'], 'ans' => 2, 'exp' => 'A fan shape in Residuals vs. Fitted indicates heteroscedasticity — the variance of residuals increases with the fitted value. This violates the homoscedasticity assumption, making standard errors and p-values unreliable. Remedies include transforming Y (e.g., log) or using weighted least squares.'],
                ['q' => 'If β₁ = 3.7 in a regression of salary (₱) on years of experience, this means...', 'opts' => ['The intercept is 3.7', 'Each additional year of experience is associated with an average salary increase of ₱3.7', 'The correlation is 0.37', 'R² = 3.7%'], 'ans' => 1, 'exp' => 'β₁ is the slope — the estimated average change in Y for a one-unit increase in X, holding all other predictors constant (in multiple regression). Here, each additional year of experience is associated with a ₱3.7 increase in average salary.'],
                ['q' => 'The Normal Q-Q plot of residuals shows points systematically deviating from the reference line, especially in the tails. What assumption is violated?', 'opts' => ['Linearity', 'Independence', 'Equal variance', 'Normality of residuals'], 'ans' => 3, 'exp' => 'The Q-Q (quantile-quantile) plot compares the quantiles of residuals against quantiles from the theoretical normal distribution. If points fall on the reference line, residuals are normally distributed. Systematic deviation, especially in tails, indicates non-normality — potentially affecting hypothesis tests for small samples.'],
                ['q' => 'Testing H₀: β₁ = 0 is equivalent to testing...', 'opts' => ['H₀: R² = 0', 'H₀: the regression has no intercept', 'H₀: there is no linear relationship between X and Y', 'H₀: all residuals are equal'], 'ans' => 2, 'exp' => 'If β₁ = 0, the regression line is horizontal — X does not predict Y at all. The t-test for β₁ = 0 is equivalent to the test for r = 0 (in simple regression) and to the overall F-test. Rejecting this means there is statistically significant evidence of a linear relationship.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 8.9 — Experimental Design: Principles & Layouts
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Experimental Design: Principles & Layouts</h2>
<p>Experimental design is the art and science of planning experiments to obtain valid, interpretable, and efficient answers to research questions. A beautifully executed analysis cannot rescue a poorly designed experiment. Conversely, a well-designed experiment produces clean, unambiguous data that almost analyzes itself. The three fundamental principles — replication, randomization, and blocking — are the foundation of every experimental design.</p>

<h3>The Three Core Principles</h3>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:32px;display:grid;gap:16px;">
  <div style="background:rgba(99,102,241,0.08);border-left:4px solid #6366f1;padding:16px;border-radius:0 8px 8px 0;">
    <h4 style="color:#6366f1;margin:0 0 8px;">1. Replication</h4>
    <p style="color:var(--muted);font-size:0.875rem;line-height:1.7;margin:0;">Applying each treatment to multiple independent experimental units. Replication provides an estimate of experimental error (within-treatment variability), allows statistical tests, and increases precision. Without replication, you cannot separate the treatment effect from natural variability or from measurement error. Rule: if an experiment cannot be replicated, it cannot produce statistical conclusions.</p>
  </div>
  <div style="background:rgba(16,185,129,0.08);border-left:4px solid #10b981;padding:16px;border-radius:0 8px 8px 0;">
    <h4 style="color:#10b981;margin:0 0 8px;">2. Randomization</h4>
    <p style="color:var(--muted);font-size:0.875rem;line-height:1.7;margin:0;">Randomly assigning treatments to experimental units and/or randomly running experimental trials. Randomization protects against systematic bias from unknown confounding variables by distributing their effects equally across treatments on average. It is the only way to justify causal inference. Without randomization, observed differences may be due to pre-existing differences between groups, not the treatment.</p>
  </div>
  <div style="background:rgba(245,158,11,0.08);border-left:4px solid #f59e0b;padding:16px;border-radius:0 8px 8px 0;">
    <h4 style="color:#f59e0b;margin:0 0 8px;">3. Blocking (Local Control)</h4>
    <p style="color:var(--muted);font-size:0.875rem;line-height:1.7;margin:0;">Grouping experimental units with similar characteristics into blocks, then applying all treatments within each block. Blocking removes a known source of variability from the experimental error, increasing precision. For example, if you test fertilizers on plots in two fields with different soil types, blocking by field removes soil variability from the error, letting you detect fertilizer effects more clearly.</p>
  </div>
</div>

<h3>Basic Experimental Designs</h3>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:32px;display:grid;gap:14px;">
  <div>
    <h4 style="color:#3b82f6;margin:0 0 6px;">Completely Randomized Design (CRD)</h4>
    <p style="color:var(--muted);font-size:0.875rem;line-height:1.7;margin:0;">All experimental units are homogeneous and treatments are assigned completely at random. Simplest design. Analyzed by one-way ANOVA. Works well in controlled laboratory settings where units are truly interchangeable (e.g., cell culture experiments, machine tests).</p>
  </div>
  <div>
    <h4 style="color:#10b981;margin:0 0 6px;">Randomized Complete Block Design (RCBD)</h4>
    <p style="color:var(--muted);font-size:0.875rem;line-height:1.7;margin:0;">Experimental units are divided into blocks of homogeneous units. All treatments appear in every block, assigned randomly within blocks. Blocking removes one source of variability. Analyzed by two-way ANOVA (treatment + block effects). Example: comparing drug doses across different hospitals — block by hospital to remove center effects.</p>
  </div>
  <div>
    <h4 style="color:#f59e0b;margin:0 0 6px;">Latin Square Design</h4>
    <p style="color:var(--muted);font-size:0.875rem;line-height:1.7;margin:0;">Controls two sources of variability simultaneously (two blocking factors) with k treatments, k rows, and k columns — each treatment appears exactly once in each row and column. Highly efficient when two nuisance variables are known. Example: testing k car tires across k drivers (rows) and k track positions (columns).</p>
  </div>
  <div>
    <h4 style="color:#6366f1;margin:0 0 6px;">Factorial Designs</h4>
    <p style="color:var(--muted);font-size:0.875rem;line-height:1.7;margin:0;">Test all combinations of two or more factors simultaneously. A 2×3 factorial has 2 levels of Factor A and 3 levels of Factor B → 6 treatment combinations. Factorial designs are efficient (one experiment = multiple questions) and crucially reveal <strong>interactions</strong> — whether the effect of A depends on the level of B.</p>
  </div>
</div>

<h3>Interaction Effects: The Most Important Output of Factorial Designs</h3>
<p>An <strong>interaction</strong> exists when the effect of one factor changes depending on the level of another factor. Interpreting main effects alone in the presence of a significant interaction is misleading. Always check for interactions first.</p>
<p>Example: If a new teaching method improves scores for students with high prior achievement but harms those with low prior achievement, the method × achievement interaction means there is no single "main effect" worth reporting — the effect depends entirely on the student's background.</p>

<h3>Control Groups, Placebo, and Blinding</h3>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Control Group:</strong> A group that receives no treatment or a standard treatment, providing a baseline for comparison. Without a control, you cannot determine whether observed changes are due to treatment or to natural change over time.</li>
  <li><strong style="color:var(--text);">Placebo:</strong> A biologically inert treatment given to the control group to match the psychological effect of receiving a treatment (the placebo effect). Essential in clinical trials.</li>
  <li><strong style="color:var(--text);">Single-Blind:</strong> The participant does not know which treatment they receive. Prevents response bias.</li>
  <li><strong style="color:var(--text);">Double-Blind:</strong> Neither participant nor assessor knows treatment assignment. The gold standard for clinical trials — prevents both response and assessment bias.</li>
</ul>

<h3>Coding: Factorial Design Analysis & Interaction Plots</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — 2×2 Factorial Design: Two-Way ANOVA & Interaction Plot</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats
<span style="color:#c4b5fd;">import</span> statsmodels.formula.api <span style="color:#c4b5fd;">as</span> smf
<span style="color:#c4b5fd;">from</span> statsmodels.stats.anova <span style="color:#c4b5fd;">import</span> anova_lm

np.random.seed(<span style="color:#fcd34d;">77</span>)

<span style="color:#6b7280;"># ── 2×2 Factorial: Temperature × Pressure → Yield ──────────────
# Factor A: Temperature (Low=60°C, High=80°C)
# Factor B: Pressure    (Low=1atm, High=2atm)
# 5 replicates per cell → 20 total observations</span>

<span style="color:#93c5fd;">cell_means</span> = {
    (<span style="color:#a7f3d0;">'Low'</span>,  <span style="color:#a7f3d0;">'Low'</span>):  <span style="color:#fcd34d;">60</span>,   <span style="color:#6b7280;"># T=Low,  P=Low</span>
    (<span style="color:#a7f3d0;">'Low'</span>,  <span style="color:#a7f3d0;">'High'</span>): <span style="color:#fcd34d;">68</span>,   <span style="color:#6b7280;"># T=Low,  P=High</span>
    (<span style="color:#a7f3d0;">'High'</span>, <span style="color:#a7f3d0;">'Low'</span>):  <span style="color:#fcd34d;">72</span>,   <span style="color:#6b7280;"># T=High, P=Low</span>
    (<span style="color:#a7f3d0;">'High'</span>, <span style="color:#a7f3d0;">'High'</span>): <span style="color:#fcd34d;">90</span>,   <span style="color:#6b7280;"># T=High, P=High — interaction!</span>
}

<span style="color:#93c5fd;">rows</span> = []
<span style="color:#93c5fd;">n_rep</span> = <span style="color:#fcd34d;">5</span>
<span style="color:#c4b5fd;">for</span> (temp, pres), mu <span style="color:#c4b5fd;">in</span> cell_means.items():
    <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n_rep):
        rows.append({<span style="color:#a7f3d0;">'Temperature'</span>: temp, <span style="color:#a7f3d0;">'Pressure'</span>: pres,
                     <span style="color:#a7f3d0;">'Yield'</span>: mu + np.random.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">5</span>)})
<span style="color:#93c5fd;">df</span> = pd.DataFrame(rows)

<span style="color:#6b7280;"># ── Two-Way ANOVA ────────────────────────────────────────────────</span>
<span style="color:#93c5fd;">model</span>    = smf.ols(<span style="color:#a7f3d0;">'Yield ~ C(Temperature) + C(Pressure) + C(Temperature):C(Pressure)'</span>, data=df).fit()
<span style="color:#93c5fd;">anova_tb</span> = anova_lm(model, typ=<span style="color:#fcd34d;">2</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ TWO-WAY ANOVA TABLE ══"</span>)
<span style="color:#93c5fd;">print</span>(anova_tb.to_string())
<span style="color:#93c5fd;">print</span>()

<span style="color:#6b7280;"># Interpret interaction</span>
<span style="color:#93c5fd;">p_int</span> = anova_tb.loc[<span style="color:#a7f3d0;">'C(Temperature):C(Pressure)'</span>, <span style="color:#a7f3d0;">'PR(>F)'</span>]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Interaction p = {p_int:.4f} → {'SIGNIFICANT — interpret main effects cautiously' if p_int < 0.05 else 'Not significant'}"</span>)

<span style="color:#6b7280;"># Cell means summary</span>
<span style="color:#93c5fd;">means_table</span> = df.groupby([<span style="color:#a7f3d0;">'Temperature'</span>, <span style="color:#a7f3d0;">'Pressure'</span>])[<span style="color:#a7f3d0;">'Yield'</span>].mean().unstack()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nCell Means (Yield %):"</span>)
<span style="color:#93c5fd;">print</span>(means_table.round(<span style="color:#fcd34d;">2</span>))

<span style="color:#6b7280;"># ── Interaction Plot ─────────────────────────────────────────────</span>
<span style="color:#93c5fd;">fig</span>, <span style="color:#93c5fd;">axes</span> = plt.subplots(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">4</span>))

<span style="color:#c4b5fd;">for</span> pres, color, marker <span style="color:#c4b5fd;">in</span> [(<span style="color:#a7f3d0;">'Low'</span>, <span style="color:#a7f3d0;">'#3b82f6'</span>, <span style="color:#a7f3d0;">'o-'</span>), (<span style="color:#a7f3d0;">'High'</span>, <span style="color:#a7f3d0;">'#ef4444'</span>, <span style="color:#a7f3d0;">'s--'</span>)]:
    <span style="color:#93c5fd;">sub</span> = means_table[pres]
    axes[<span style="color:#fcd34d;">0</span>].plot(sub.index, sub.values, marker, color=color, lw=<span style="color:#fcd34d;">2</span>, label=<span style="color:#a7f3d0;">f"Pressure={pres}"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Interaction Plot\n(non-parallel lines → interaction exists)"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_xlabel(<span style="color:#a7f3d0;">"Temperature"</span>); axes[<span style="color:#fcd34d;">0</span>].set_ylabel(<span style="color:#a7f3d0;">"Mean Yield (%)"</span>)
axes[<span style="color:#fcd34d;">0</span>].legend()

<span style="color:#6b7280;"># Boxplot per cell</span>
<span style="color:#93c5fd;">df</span>[<span style="color:#a7f3d0;">'Group'</span>] = df[<span style="color:#a7f3d0;">'Temperature'</span>] + <span style="color:#a7f3d0;">"\nP="</span> + df[<span style="color:#a7f3d0;">'Pressure'</span>]
<span style="color:#93c5fd;">groups</span> = [<span style="color:#93c5fd;">list</span>(df[df[<span style="color:#a7f3d0;">'Group'</span>]==g][<span style="color:#a7f3d0;">'Yield'</span>]) <span style="color:#c4b5fd;">for</span> g <span style="color:#c4b5fd;">in</span> df[<span style="color:#a7f3d0;">'Group'</span>].unique()]
axes[<span style="color:#fcd34d;">1</span>].boxplot(groups, labels=df[<span style="color:#a7f3d0;">'Group'</span>].unique(),
               patch_artist=<span style="color:#fca5a5;">True</span>, boxprops=<span style="color:#93c5fd;">dict</span>(facecolor=<span style="color:#a7f3d0;">"#a78bfa"</span>, alpha=<span style="color:#fcd34d;">0.5</span>))
axes[<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Yield by Treatment Cell"</span>)
axes[<span style="color:#fcd34d;">1</span>].set_ylabel(<span style="color:#a7f3d0;">"Yield (%)"</span>)

plt.suptitle(<span style="color:#a7f3d0;">"2×2 Factorial Experiment: Temperature × Pressure"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>══ TWO-WAY ANOVA TABLE ══
                                    sum_sq    df          F    PR(>F)
C(Temperature)                   1024.2     1.0    35.1812  0.000014
C(Pressure)                       812.5     1.0    27.9052  0.000074
C(Temperature):C(Pressure)        367.2     1.0    12.6105  0.002184
Residual                          466.3    16.0        NaN       NaN

Interaction p = 0.0022 → SIGNIFICANT — interpret main effects cautiously

Cell Means (Yield %):
Pressure     High    Low
Temperature
High        90.47  71.82
Low         68.23  59.64</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '8.9 Experimental Design: Principles & Layouts',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'M8_L9', [
                ['q' => 'A researcher tests 3 fertilizer brands on a farm with 4 distinct soil types. He assigns each fertilizer randomly within each soil type. This design is called...', 'opts' => ['Completely Randomized Design', 'Randomized Complete Block Design', 'Latin Square Design', 'Factorial Design'], 'ans' => 1, 'exp' => 'RCBD: blocks are the 4 soil types (a known source of variability). All 3 fertilizers appear in every block (soil type), randomly assigned within blocks. This removes soil variability from the experimental error, making the fertilizer comparison more precise.'],
                ['q' => 'In a 2×3 factorial design (Factor A has 2 levels, Factor B has 3 levels), how many treatment combinations are there?', 'opts' => ['2', '3', '5', '6'], 'ans' => 3, 'exp' => 'Factorial designs test all combinations: 2 × 3 = 6 treatment combinations. The key advantage is that both main effects AND the A×B interaction can be estimated from a single experiment, whereas one-factor-at-a-time designs would require separate experiments and cannot detect interactions.'],
                ['q' => 'A significant interaction between teaching method (A/B) and student ability (high/low) means...', 'opts' => ['Teaching method A is always better', 'Student ability has no effect on outcomes', 'The effect of teaching method depends on student ability level — you cannot generalize across all students', 'Both factors are independently significant'], 'ans' => 2, 'exp' => 'An interaction means the effect of one factor (teaching method) changes depending on the level of the other factor (student ability). Perhaps method A is better for high-ability students but method B is better for low-ability students. Reporting only the main effect of "teaching method" would be misleading.'],
                ['q' => 'Randomization in an experiment is essential primarily because it...', 'opts' => ['Ensures equal sample sizes in each group', 'Distributes the effects of unknown confounders equally across groups, allowing causal inference', 'Makes the data normally distributed', 'Reduces the need for replication'], 'ans' => 1, 'exp' => 'Randomization protects against systematic bias from confounders — variables that affect the outcome but are not part of the study design. By randomly assigning treatments, any confounders are distributed roughly equally across groups on average, making the groups comparable and allowing causal conclusions.'],
                ['q' => 'Double-blind experimental design means...', 'opts' => ['Two independent researchers analyze the data', 'The experiment is conducted twice for validation', 'Neither participants nor assessors know the treatment assignment', 'Both the treatment and control receive the same dose'], 'ans' => 2, 'exp' => 'Double-blind means both the participant (preventing response bias/placebo effect) and the outcome assessor (preventing assessment bias) are unaware of treatment assignment. This is the gold standard for clinical trials. Single-blind means only the participant is blinded.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 8.10 — Effect Size, Power Analysis & Sample Size Planning
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Effect Size, Power Analysis & Sample Size Planning</h2>
<p>Statistical significance (p &lt; 0.05) tells you whether an effect is unlikely to be due to chance. It does <em>not</em> tell you how large that effect is, whether it matters in practice, or whether your study was adequately designed to detect it. Effect size, statistical power, and sample size planning complete the hypothesis testing framework — they are the difference between a study that produces reliable scientific knowledge and one that wastes resources or produces misleading conclusions.</p>

<h3>Effect Size: Quantifying the Magnitude</h3>
<p>Effect size measures the practical magnitude of an effect, independent of sample size. A study with n=10,000 can detect a trivially small effect with p &lt; 0.001, while the same tiny effect in a small study might yield p = 0.5. Effect sizes standardize the comparison.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:32px;">
  <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
    <thead>
      <tr style="background:rgba(0,0,0,0.2);">
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Measure</th>
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Formula</th>
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Context</th>
        <th style="padding:10px 16px;text-align:left;color:var(--muted);">Small/Medium/Large</th>
      </tr>
    </thead>
    <tbody>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:#6366f1;font-weight:600;">Cohen's d</td>
        <td style="padding:10px 16px;color:var(--muted);font-family:'JetBrains Mono',monospace;font-size:0.8rem;">(μ₁−μ₂) / σ_pooled</td>
        <td style="padding:10px 16px;color:var(--muted);">Comparing two means (t-tests)</td>
        <td style="padding:10px 16px;color:var(--muted);">0.2 / 0.5 / 0.8</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:#10b981;font-weight:600;">η² (Eta-squared)</td>
        <td style="padding:10px 16px;color:var(--muted);font-family:'JetBrains Mono',monospace;font-size:0.8rem;">SS_treatment / SS_total</td>
        <td style="padding:10px 16px;color:var(--muted);">ANOVA — proportion of variance explained</td>
        <td style="padding:10px 16px;color:var(--muted);">0.01 / 0.06 / 0.14</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:#f59e0b;font-weight:600;">Pearson r</td>
        <td style="padding:10px 16px;color:var(--muted);font-family:'JetBrains Mono',monospace;font-size:0.8rem;">Cov(X,Y)/(σX × σY)</td>
        <td style="padding:10px 16px;color:var(--muted);">Correlation / regression</td>
        <td style="padding:10px 16px;color:var(--muted);">0.1 / 0.3 / 0.5</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:#ef4444;font-weight:600;">Cramer's V</td>
        <td style="padding:10px 16px;color:var(--muted);font-family:'JetBrains Mono',monospace;font-size:0.8rem;">√(χ²/(n·min(r,c)−1))</td>
        <td style="padding:10px 16px;color:var(--muted);">Chi-square tests</td>
        <td style="padding:10px 16px;color:var(--muted);">0.1 / 0.3 / 0.5</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:#a855f7;font-weight:600;">Odds Ratio (OR)</td>
        <td style="padding:10px 16px;color:var(--muted);font-family:'JetBrains Mono',monospace;font-size:0.8rem;">(a/b)/(c/d)</td>
        <td style="padding:10px 16px;color:var(--muted);">Binary outcomes (logistic regression, 2×2 tables)</td>
        <td style="padding:10px 16px;color:var(--muted);">OR=1: no effect; OR>1: increased odds</td>
      </tr>
    </tbody>
  </table>
</div>

<h3>Statistical Power</h3>
<p>Statistical power = 1 − β = P(reject H₀ | H₀ is false) = the probability of correctly detecting a real effect. The conventional target is power ≥ 0.80 (80%). Low-power studies are problematic because they: (1) frequently miss real effects (Type II errors), (2) when they do find significance, often dramatically overestimate effect sizes ("winner's curse"), and (3) produce unreliable, non-replicable results.</p>

<p>Power is determined by four interrelated quantities — changing any one affects the others:</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Effect size (d, f, r):</strong> Larger effects are easier to detect → higher power</li>
  <li><strong style="color:var(--text);">Sample size (n):</strong> Larger n → smaller SE → easier to detect effects → higher power</li>
  <li><strong style="color:var(--text);">Significance level (α):</strong> Higher α → easier to reject H₀ → higher power (but more Type I errors)</li>
  <li><strong style="color:var(--text);">Population variability (σ):</strong> Higher σ → more noise → harder to detect signal → lower power</li>
</ul>

<h3>Sample Size Planning: The A Priori Approach</h3>
<p>Sample size should be calculated <em>before</em> data collection (a priori), not after (post hoc — this is statistically invalid and circular). The a priori approach requires you to specify: the desired power (typically 0.80), the significance level α (typically 0.05), and the minimum effect size you want to be able to detect (based on prior research, pilot data, or the smallest clinically/practically meaningful difference).</p>

<h3>Coding: Effect Size, Power Curves & Sample Size Calculation</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Cohen's d, Power Analysis & Sample Size via statsmodels</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats
<span style="color:#c4b5fd;">from</span> statsmodels.stats.power <span style="color:#c4b5fd;">import</span> TTestIndPower, TTestPower

np.random.seed(<span style="color:#fcd34d;">13</span>)

<span style="color:#6b7280;"># ── 1. Calculate Cohen's d from real data ────────────────────────</span>
<span style="color:#93c5fd;">group1</span> = np.random.normal(<span style="color:#fcd34d;">78</span>, <span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">30</span>)
<span style="color:#93c5fd;">group2</span> = np.random.normal(<span style="color:#fcd34d;">84</span>, <span style="color:#fcd34d;">13</span>, <span style="color:#fcd34d;">30</span>)

<span style="color:#93c5fd;">pooled_std</span> = np.sqrt((((<span style="color:#93c5fd;">len</span>(group1)-<span style="color:#fcd34d;">1</span>)*group1.var(ddof=<span style="color:#fcd34d;">1</span>) +
                        (<span style="color:#93c5fd;">len</span>(group2)-<span style="color:#fcd34d;">1</span>)*group2.var(ddof=<span style="color:#fcd34d;">1</span>)) /
                       (<span style="color:#93c5fd;">len</span>(group1)+<span style="color:#93c5fd;">len</span>(group2)-<span style="color:#fcd34d;">2</span>)))
<span style="color:#93c5fd;">cohens_d</span>   = (group2.mean() - group1.mean()) / pooled_std

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ EFFECT SIZE (Cohen's d) ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Group 1: x̄={group1.mean():.2f}, s={group1.std(ddof=1):.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Group 2: x̄={group2.mean():.2f}, s={group2.std(ddof=1):.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Pooled SD   : {pooled_std:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Cohen's d   : {cohens_d:.4f}"</span>)
<span style="color:#93c5fd;">magnitude</span> = (<span style="color:#a7f3d0;">"small"</span> <span style="color:#c4b5fd;">if</span> <span style="color:#93c5fd;">abs</span>(cohens_d) < <span style="color:#fcd34d;">0.5</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"medium"</span> <span style="color:#c4b5fd;">if</span> <span style="color:#93c5fd;">abs</span>(cohens_d) < <span style="color:#fcd34d;">0.8</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"large"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Magnitude   : {magnitude} (Cohen's convention: 0.2=small, 0.5=med, 0.8=large)\n"</span>)

<span style="color:#6b7280;"># ── 2. Power Analysis: What power do we have? ────────────────────</span>
<span style="color:#93c5fd;">analysis</span> = TTestIndPower()

<span style="color:#93c5fd;">power_achieved</span> = analysis.power(
    effect_size=cohens_d, nobs1=<span style="color:#fcd34d;">30</span>, alpha=<span style="color:#fcd34d;">0.05</span>, ratio=<span style="color:#fcd34d;">1.0</span>, alternative=<span style="color:#a7f3d0;">'two-sided'</span>
)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ POWER ANALYSIS ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  With n=30/group and d={cohens_d:.3f}:"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Achieved Power = {power_achieved:.4f}  ({power_achieved*100:.1f}%)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {'Adequately powered (≥80%)' if power_achieved >= 0.8 else 'UNDERPOWERED — risk of missing real effect'}\n"</span>)

<span style="color:#6b7280;"># ── 3. Sample Size Planning: required n for target power ─────────</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"══ SAMPLE SIZE REQUIREMENTS ══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Effect (d)':>12}  {'Power=0.70':>12}  {'Power=0.80':>12}  {'Power=0.90':>12}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─"</span> * <span style="color:#fcd34d;">54</span>)
<span style="color:#c4b5fd;">for</span> d <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">0.2</span>, <span style="color:#fcd34d;">0.3</span>, <span style="color:#fcd34d;">0.5</span>, <span style="color:#fcd34d;">0.8</span>, <span style="color:#fcd34d;">1.0</span>]:
    ns = []
    <span style="color:#c4b5fd;">for</span> pw <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">0.70</span>, <span style="color:#fcd34d;">0.80</span>, <span style="color:#fcd34d;">0.90</span>]:
        n_req = analysis.solve_power(effect_size=d, power=pw, alpha=<span style="color:#fcd34d;">0.05</span>, ratio=<span style="color:#fcd34d;">1.0</span>)
        ns.append(<span style="color:#93c5fd;">int</span>(np.ceil(n_req)))
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{d:>12.1f}  {ns[0]:>12}  {ns[1]:>12}  {ns[2]:>12}"</span>)

<span style="color:#6b7280;"># ── 4. Power Curve Plot ───────────────────────────────────────────</span>
<span style="color:#93c5fd;">sample_sizes</span> = np.arange(<span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">200</span>, <span style="color:#fcd34d;">5</span>)
<span style="color:#93c5fd;">fig</span>, axes = plt.subplots(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">13</span>, <span style="color:#fcd34d;">4</span>))

<span style="color:#c4b5fd;">for</span> d, color, label <span style="color:#c4b5fd;">in</span> [(<span style="color:#fcd34d;">0.2</span>,<span style="color:#a7f3d0;">"#6366f1"</span>,<span style="color:#a7f3d0;">"d=0.2 (small)"</span>),
                          (<span style="color:#fcd34d;">0.5</span>,<span style="color:#a7f3d0;">"#10b981"</span>,<span style="color:#a7f3d0;">"d=0.5 (medium)"</span>),
                          (<span style="color:#fcd34d;">0.8</span>,<span style="color:#a7f3d0;">"#ef4444"</span>,<span style="color:#a7f3d0;">"d=0.8 (large)"</span>)]:
    powers = [analysis.power(effect_size=d, nobs1=n, alpha=<span style="color:#fcd34d;">0.05</span>, ratio=<span style="color:#fcd34d;">1</span>) <span style="color:#c4b5fd;">for</span> n <span style="color:#c4b5fd;">in</span> sample_sizes]
    axes[<span style="color:#fcd34d;">0</span>].plot(sample_sizes, powers, color=color, label=label, lw=<span style="color:#fcd34d;">2</span>)
axes[<span style="color:#fcd34d;">0</span>].axhline(<span style="color:#fcd34d;">0.80</span>, ls=<span style="color:#a7f3d0;">"--"</span>, color=<span style="color:#a7f3d0;">"gray"</span>, label=<span style="color:#a7f3d0;">"80% power target"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Power Curves: Independent t-Test (α=0.05)"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_xlabel(<span style="color:#a7f3d0;">"n per group"</span>); axes[<span style="color:#fcd34d;">0</span>].set_ylabel(<span style="color:#a7f3d0;">"Statistical Power"</span>)
axes[<span style="color:#fcd34d;">0</span>].legend(fontsize=<span style="color:#fcd34d;">8</span>)

<span style="color:#6b7280;"># Type I vs Type II error tradeoff</span>
<span style="color:#93c5fd;">alpha_vals</span> = np.linspace(<span style="color:#fcd34d;">0.001</span>, <span style="color:#fcd34d;">0.2</span>, <span style="color:#fcd34d;">100</span>)
<span style="color:#93c5fd;">power_vals</span> = [analysis.power(effect_size=<span style="color:#fcd34d;">0.5</span>, nobs1=<span style="color:#fcd34d;">40</span>, alpha=a, ratio=<span style="color:#fcd34d;">1</span>) <span style="color:#c4b5fd;">for</span> a <span style="color:#c4b5fd;">in</span> alpha_vals]
axes[<span style="color:#fcd34d;">1</span>].plot(alpha_vals, power_vals, color=<span style="color:#a7f3d0;">"#f59e0b"</span>, lw=<span style="color:#fcd34d;">2</span>)
axes[<span style="color:#fcd34d;">1</span>].axvline(<span style="color:#fcd34d;">0.05</span>, ls=<span style="color:#a7f3d0;">"--"</span>, color=<span style="color:#a7f3d0;">"#ef4444"</span>, label=<span style="color:#a7f3d0;">"α=0.05"</span>)
axes[<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Power vs α (n=40/grp, d=0.5)"</span>)
axes[<span style="color:#fcd34d;">1</span>].set_xlabel(<span style="color:#a7f3d0;">"Significance Level α"</span>); axes[<span style="color:#fcd34d;">1</span>].set_ylabel(<span style="color:#a7f3d0;">"Power (1−β)"</span>)
axes[<span style="color:#fcd34d;">1</span>].legend()

plt.suptitle(<span style="color:#a7f3d0;">"Statistical Power Analysis"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>══ EFFECT SIZE (Cohen's d) ══
  Group 1: x̄=77.84, s=11.92
  Group 2: x̄=83.71, s=13.14
  Pooled SD   : 12.5536
  Cohen's d   : 0.4676
  Magnitude   : small (Cohen's convention: 0.2=small, 0.5=med, 0.8=large)

══ POWER ANALYSIS ══
  With n=30/group and d=0.468:
  Achieved Power = 0.5623  (56.2%)
  UNDERPOWERED — risk of missing real effect

══ SAMPLE SIZE REQUIREMENTS ══
  Effect (d)    Power=0.70    Power=0.80    Power=0.90
──────────────────────────────────────────────────────
         0.2           155           197           264
         0.3            70            88           118
         0.5            26            34            46
         0.8            11            15            20
         1.0             8            11            15</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '8.10 Effect Size, Power Analysis & Sample Size Planning',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'M8_L10', [
                ['q' => 'A drug trial finds p = 0.001 with Cohen\'s d = 0.04. What is the most appropriate conclusion?', 'opts' => ['The drug works very well — p is extremely small', 'The effect is statistically significant but practically negligible (d = 0.04 is trivially small)', 'The study needs more participants', 'd = 0.04 means the drug is 4% effective'], 'ans' => 1, 'exp' => 'Statistical significance ≠ practical significance. With very large n, even trivially small effects become statistically significant. d = 0.04 means the group means differ by only 0.04 standard deviations — a difference so small it is almost certainly clinically meaningless. Always report both p-value AND effect size.'],
                ['q' => 'A study has power = 0.45 (45%). This means...', 'opts' => ['45% of the data are correct', 'There is a 45% chance of rejecting H₀ when it is false — the study is severely underpowered', 'The probability of a Type I error is 45%', 'There is a 45% chance the null hypothesis is true'], 'ans' => 1, 'exp' => 'Statistical power (1 − β) is the probability of correctly detecting a real effect (rejecting a false null). At 45% power, the study is more likely to miss the effect (Type II error = 55%) than to find it. Studies should generally aim for at least 80% power.'],
                ['q' => 'To decrease the required sample size for a study while maintaining the same statistical power (e.g., 80%), you must...', 'opts' => ['Decrease alpha (e.g., from 0.05 to 0.01)', 'Target a larger minimum detectable effect size', 'Use a non-parametric test', 'Increase the expected population variance'], 'ans' => 1, 'exp' => 'A larger effect size (signal) is easier to detect through the noise, thus requiring fewer participants. Decreasing alpha makes it harder to reject H₀, requiring MORE participants. Higher variance adds noise, also requiring MORE participants.'],
                ['q' => 'Post hoc power analysis (calculating power using the observed effect size after getting a non-significant result) is considered...', 'opts' => ['The recommended way to justify a non-significant result', 'Statistically invalid and conceptually flawed', 'Required by most medical journals', 'Identical in utility to a priori power analysis'], 'ans' => 1, 'exp' => 'Post hoc power computed from the observed effect size is mathematically directly related to the p-value. If p > 0.05, post hoc power will ALWAYS be low. It provides no new information and is considered a flawed practice. Power must be calculated a priori based on the smallest effect size of practical interest.'],
                ['q' => 'Cohen\'s d for an independent samples t-test is calculated as the mean difference divided by...', 'opts' => ['The pooled standard deviation', 'The standard error of the mean difference', 'The sample size n', 'The pooled variance'], 'ans' => 0, 'exp' => 'Cohen\'s d = (μ₁ − μ₂) / σ_pooled. It represents the mean difference in standard deviation units. Using the standard error instead would make the effect size dependent on sample size (like the t-statistic), which defeats the entire purpose of a standardized effect size.']
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 8.11 — Final Exam (Org-Locked)
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            // 8.1 Intro
            ['q' => 'A parameter describes a population, whereas a statistic describes a...', 'opts' => ['Distribution', 'Sample', 'Variable', 'Matrix'], 'ans' => 1, 'exp' => 'Statistics (like sample mean x̄) describe samples and are used to estimate parameters (like population mean μ) which describe populations.'],
            ['q' => 'Temperature measured in Celsius is an example of which level of measurement?', 'opts' => ['Nominal', 'Ordinal', 'Interval', 'Ratio'], 'ans' => 2, 'exp' => 'Celsius is interval data. The intervals between degrees are equal, but there is no "true zero" (0°C does not mean the absence of temperature), so ratios are not meaningful (20°C is not "twice as hot" as 10°C).'],
            // 8.2 Descriptive
            ['q' => 'Which measure of central tendency is the ONLY appropriate choice for nominal (categorical) data?', 'opts' => ['Mean', 'Median', 'Mode', 'Variance'], 'ans' => 2, 'exp' => 'Nominal data has no inherent order, so you cannot calculate a mean or a median. You can only identify the mode — the most frequently occurring category.'],
            ['q' => 'A dataset has a mean of 150 and a median of 100. The distribution shape is likely...', 'opts' => ['Symmetric', 'Negatively (left) skewed', 'Positively (right) skewed', 'Uniform'], 'ans' => 2, 'exp' => 'When the mean is significantly larger than the median, it indicates the presence of large positive outliers pulling the mean upward. This is the definition of a positive (right) skew.'],
            // 8.3 Distributions
            ['q' => 'In a normal distribution, approximately what percentage of observations fall within ±3 standard deviations of the mean?', 'opts' => ['68%', '95%', '99.7%', '100%'], 'ans' => 2, 'exp' => 'According to the empirical rule (68-95-99.7 rule) for normal distributions, ~99.7% of data falls within 3 standard deviations of the mean.'],
            ['q' => 'Which distribution perfectly models the number of independent events occurring in a fixed interval of time or space (e.g., emails arriving per hour)?', 'opts' => ['Binomial', 'Normal', 'Poisson', 'Chi-square'], 'ans' => 2, 'exp' => 'The Poisson distribution models count data for rare, independent events over a continuous interval (time, area, volume).'],
            // 8.4 Sampling
            ['q' => 'Dividing a population into subgroups (e.g., by age bracket) and then taking a simple random sample from within each subgroup is called...', 'opts' => ['Cluster sampling', 'Stratified random sampling', 'Systematic sampling', 'Convenience sampling'], 'ans' => 1, 'exp' => 'This is stratified random sampling. It ensures that every subgroup (stratum) is properly represented in the final sample.'],
            // 8.5 Hypothesis Testing
            ['q' => 'A p-value is precisely defined as...', 'opts' => ['The probability that H₀ is true', 'The probability of observing data at least this extreme, assuming H₀ is true', 'The probability of a Type I error', 'The probability that the alternative hypothesis is true'], 'ans' => 1, 'exp' => 'The p-value is a conditional probability: P(data | H₀ is true). It is NOT the probability that H₀ is true given the data.'],
            ['q' => 'Failing to reject a false null hypothesis is known as a...', 'opts' => ['Type I error (α)', 'Type II error (β)', 'Type III error', 'Power error'], 'ans' => 1, 'exp' => 'A Type II error (false negative) occurs when a real effect exists in the population, but the sample data fails to detect it. Its probability is β.'],
            // 8.6 Two-Sample & ANOVA
            ['q' => 'When comparing the means of three or more independent groups, why is ANOVA preferred over running multiple t-tests?', 'opts' => ['ANOVA is faster to compute', 'Multiple t-tests inflate the familywise Type I error rate', 't-tests cannot handle continuous data', 'ANOVA does not require the normality assumption'], 'ans' => 1, 'exp' => 'Running many t-tests increases the chance of finding a false positive by chance. ANOVA controls the overall Type I error rate by testing all groups simultaneously.'],
            ['q' => 'You want to compare the weights of 50 people before and after a 6-week diet program. The correct test is...', 'opts' => ['Independent samples t-test', 'One-way ANOVA', 'Paired samples t-test', 'Chi-square test'], 'ans' => 2, 'exp' => 'Because the measurements are taken on the SAME individuals (before and after), the data points are paired/dependent. The paired t-test is the correct and most powerful choice.'],
            // 8.7 Chi-Square & Non-parametric
            ['q' => 'To test if gender (Male/Female) and promotion status (Promoted/Not Promoted) are associated, you should use...', 'opts' => ['Independent t-test', 'Pearson correlation', 'Chi-square test of independence', 'One-way ANOVA'], 'ans' => 2, 'exp' => 'Both variables are categorical. The chi-square test of independence evaluates whether the proportions in one categorical variable depend on the levels of another categorical variable.'],
            ['q' => 'The Mann-Whitney U test is the non-parametric alternative to which parametric test?', 'opts' => ['Paired t-test', 'Independent samples t-test', 'One-way ANOVA', 'Pearson correlation'], 'ans' => 1, 'exp' => 'The Mann-Whitney U (or Wilcoxon rank-sum) test compares two independent groups based on ranks, used when the normality assumption for the independent t-test is severely violated.'],
            // 8.8 Regression
            ['q' => 'In simple linear regression (ŷ = β₀ + β₁x), what does R² represent?', 'opts' => ['The slope of the line', 'The average error of the predictions', 'The proportion of variance in Y explained by X', 'The probability that β₁ = 0'], 'ans' => 2, 'exp' => 'R² (the coefficient of determination) quantifies the goodness of fit: the percentage of the total variability in the dependent variable (Y) that is accounted for by the regression model.'],
            // 8.9 Experimental Design
            ['q' => 'In experimental design, randomly assigning subjects to treatments protects against...', 'opts' => ['Type II errors', 'Systematic bias from confounding variables', 'The need for replication', 'Non-normal distributions'], 'ans' => 1, 'exp' => 'Randomization is the only way to ensure that unknown lurking variables (confounders) are distributed roughly equally among treatment groups, allowing for valid causal inference.']
        ];

        $finalContent  = <<<HTML
<div id="org-lock-screen" style="text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;">
    <div style="font-size:3rem;margin-bottom:1rem;">🔒</div>
    <h3 style="color:var(--text);margin-bottom:0.5rem;">University / Organization Access Only</h3>
    <p style="color:var(--muted);">The Final Module Exam is restricted to enrolled students and verified organization members.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 8: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 8.1 through 8.10 — statistical thinking, descriptive summaries, probability distributions, sampling, hypothesis testing, ANOVA, non-parametric methods, regression, and experimental design. Good luck!</p>
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
            'title'       => '8.11 Final Exam: Statistics & Experimental Design',
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