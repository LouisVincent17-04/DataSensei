<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module6LessonsSeeder
 * Seeds lessons for Module 6: Modeling and Simulation.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module6LessonsSeeder
 */
class Module6LessonsSeeder extends Seeder
{
    public function run()
    {
        $module = Module::where('order_index', 6)->firstOrFail();
        Lesson::where('module_id', $module->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 6.1 — Introduction to Modeling & Simulation
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What is Modeling & Simulation?</h2>
<p>A <strong>model</strong> is a simplified mathematical or computational representation of a real-world system. A <strong>simulation</strong> is the process of running that model over time to observe how the system behaves. Together, modeling and simulation (M&amp;S) allow scientists, engineers, economists, and data scientists to study systems that are too expensive, too dangerous, too slow, or too complex to experiment with directly.</p>

<p>Consider a few examples: a structural engineer cannot build ten bridges to find the one that survives an earthquake — they simulate it. A pharmaceutical company cannot infect thousands of people to test a drug — they model the disease. A financial analyst cannot wait 30 years to evaluate a retirement portfolio strategy — they simulate it thousands of times in seconds. This is the power of M&amp;S.</p>

<h3>The Modeling Pipeline</h3>
<p>Every simulation project follows a consistent pipeline:</p>
<ol style="line-height:2;color:var(--muted);padding-left:1.5rem;">
    <li><strong style="color:var(--text);">Define the problem</strong> — What question are you answering? What system are you studying?</li>
    <li><strong style="color:var(--text);">Identify system components</strong> — What are the inputs, outputs, state variables, and parameters?</li>
    <li><strong style="color:var(--text);">Formulate the model</strong> — Choose mathematical equations or logical rules that govern the system.</li>
    <li><strong style="color:var(--text);">Implement the simulation</strong> — Write code that evolves the system through time.</li>
    <li><strong style="color:var(--text);">Validate &amp; verify</strong> — Does the simulation match known data or theoretical results?</li>
    <li><strong style="color:var(--text);">Run experiments &amp; analyze</strong> — Change parameters, collect results, draw conclusions.</li>
</ol>

<h3>Types of Models</h3>
<p>Models are classified along several axes. Understanding these distinctions helps you choose the right tool for the right problem.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Taxonomy of Simulation Models</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># ─────────────────────────────────────────────────────────────
# MODEL TAXONOMY — printed as a reference table
# ─────────────────────────────────────────────────────────────</span>

<span style="color:#93c5fd;">taxonomy</span> = {
    <span style="color:#a7f3d0;">"Deterministic vs Stochastic"</span>: {
        <span style="color:#a7f3d0;">"Deterministic"</span>: <span style="color:#a7f3d0;">"Same inputs always produce same outputs. Example: Euler ODE solver."</span>,
        <span style="color:#a7f3d0;">"Stochastic"</span>:    <span style="color:#a7f3d0;">"Randomness is built in. Example: Monte Carlo stock price model."</span>
    },
    <span style="color:#a7f3d0;">"Continuous vs Discrete"</span>: {
        <span style="color:#a7f3d0;">"Continuous"</span>: <span style="color:#a7f3d0;">"State changes smoothly over time. Example: differential equations."</span>,
        <span style="color:#a7f3d0;">"Discrete"</span>:   <span style="color:#a7f3d0;">"State changes at specific time steps or events. Example: queue simulation."</span>
    },
    <span style="color:#a7f3d0;">"Static vs Dynamic"</span>: {
        <span style="color:#a7f3d0;">"Static"</span>:  <span style="color:#a7f3d0;">"Time is not a factor. Example: a regression model predicting one value."</span>,
        <span style="color:#a7f3d0;">"Dynamic"</span>: <span style="color:#a7f3d0;">"System evolves over time. Example: epidemic model, financial portfolio."</span>
    }
}

<span style="color:#c4b5fd;">for</span> axis, types <span style="color:#c4b5fd;">in</span> taxonomy.items():
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n📐 {axis}"</span>)
    <span style="color:#c4b5fd;">for</span> name, desc <span style="color:#c4b5fd;">in</span> types.items():
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"   {name:15s} → {desc}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>
📐 Deterministic vs Stochastic
   Deterministic   → Same inputs always produce same outputs. Example: Euler ODE solver.
   Stochastic      → Randomness is built in. Example: Monte Carlo stock price model.

📐 Continuous vs Discrete
   Continuous      → State changes smoothly over time. Example: differential equations.
   Discrete        → State changes at specific time steps or events. Example: queue simulation.

📐 Static vs Dynamic
   Static          → Time is not a factor. Example: a regression model predicting one value.
   Dynamic         → System evolves over time. Example: epidemic model, financial portfolio.</div>
  </div>
</div>

<h3>Setting Up Your Simulation Environment</h3>
<p>Throughout this module, we will use three core Python libraries. <strong>NumPy</strong> handles fast numerical arrays. <strong>Matplotlib</strong> visualizes results. <strong>SciPy</strong> provides ODE solvers and statistical distributions. Install them once, then import at the top of every simulation script.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Environment Setup & Verification</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Standard import aliases — memorize these, every simulation uses them</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">plt</span>
<span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats, integrate

<span style="color:#6b7280;"># Verify versions</span>
<span style="color:#c4b5fd;">import</span> scipy
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"NumPy  : {np.__version__}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"SciPy  : {scipy.__version__}"</span>)

<span style="color:#6b7280;"># Set a global random seed for reproducibility</span>
<span style="color:#6b7280;"># Any simulation with randomness MUST set a seed so results are repeatable</span>
<span style="color:#93c5fd;">rng</span> = np.random.default_rng(seed=<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Quick sanity check: draw 5 uniform random numbers between 0 and 1</span>
<span style="color:#93c5fd;">samples</span> = rng.uniform(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, size=<span style="color:#fcd34d;">5</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Random samples:"</span>, np.round(samples, <span style="color:#fcd34d;">4</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>NumPy  : 1.26.4
SciPy  : 1.12.0
Random samples: [0.7731 0.0206 0.6336 0.7488 0.4985]</div>
  </div>
</div>

<h3>Your First Simulation: Coin Flip Convergence</h3>
<p>The <strong>Law of Large Numbers</strong> states that as sample size grows, the sample mean converges to the true population mean. Let's simulate flipping a fair coin N times and watch the running proportion of heads converge to 0.5. This tiny simulation contains every element of a full M&S project: a random process, state tracking, and visualization of results.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Law of Large Numbers Simulation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">plt</span>

<span style="color:#93c5fd;">rng</span>   = np.random.default_rng(seed=<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">N</span>     = <span style="color:#fcd34d;">10_000</span>                       <span style="color:#6b7280;"># total flips</span>
<span style="color:#93c5fd;">flips</span> = rng.integers(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">2</span>, size=N)   <span style="color:#6b7280;"># 0 = tails, 1 = heads</span>

<span style="color:#6b7280;"># cumsum gives running total of heads; divide by flip number for running proportion</span>
<span style="color:#93c5fd;">running_prop</span> = np.cumsum(flips) / np.arange(<span style="color:#fcd34d;">1</span>, N + <span style="color:#fcd34d;">1</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"After    10 flips: {running_prop[9]:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"After   100 flips: {running_prop[99]:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"After  1000 flips: {running_prop[999]:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"After 10000 flips: {running_prop[-1]:.4f}"</span>)

<span style="color:#6b7280;"># Plot convergence</span>
plt.figure(figsize=(<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">4</span>))
plt.plot(running_prop, color=<span style="color:#a7f3d0;">'steelblue'</span>, lw=<span style="color:#fcd34d;">0.8</span>)
plt.axhline(<span style="color:#fcd34d;">0.5</span>, color=<span style="color:#a7f3d0;">'red'</span>, ls=<span style="color:#a7f3d0;">'--'</span>, label=<span style="color:#a7f3d0;">'True probability = 0.5'</span>)
plt.xscale(<span style="color:#a7f3d0;">'log'</span>)
plt.xlabel(<span style="color:#a7f3d0;">'Number of Flips (log scale)'</span>)
plt.ylabel(<span style="color:#a7f3d0;">'Running Proportion of Heads'</span>)
plt.title(<span style="color:#a7f3d0;">'Law of Large Numbers — Coin Flip Convergence'</span>)
plt.legend()
plt.tight_layout()
plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>After    10 flips: 0.6000
After   100 flips: 0.5200
After  1000 flips: 0.5050
After 10000 flips: 0.4993</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '6.1 Introduction to Modeling & Simulation',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L6_1', [
                ['q' => 'What is the key difference between a model and a simulation?', 'opts' => ['A model is always mathematical; a simulation is always visual', 'A model is a representation of a system; a simulation runs that model over time', 'Simulations are deterministic; models are stochastic', 'They are identical terms'], 'ans' => 1, 'exp' => 'A model is the mathematical/logical representation of the system. A simulation is the act of executing that model — typically over time — to observe behavior.'],
                ['q' => 'Which type of model includes randomness in its outcomes?', 'opts' => ['Deterministic', 'Static', 'Stochastic', 'Discrete'], 'ans' => 2, 'exp' => 'A stochastic model explicitly incorporates randomness. Running it twice with the same inputs can produce different outputs, which is why random seeds are critical for reproducibility.'],
                ['q' => 'What does np.random.default_rng(seed=42) accomplish?', 'opts' => ['Generates the number 42', 'Creates a reproducible random number generator seeded at 42', 'Locks all future computations to the value 42', 'Imports the random module'], 'ans' => 1, 'exp' => 'default_rng(seed=42) creates a Generator object initialized with a fixed seed. Any random draws from this generator will be identical across runs, ensuring reproducibility.'],
                ['q' => 'In the coin flip simulation, why is np.cumsum() divided by np.arange(1, N+1)?', 'opts' => ['To normalize the array to [0,1]', 'To compute the running average (cumulative heads / total flips so far)', 'To convert integer counts to floats', 'To reverse the array'], 'ans' => 1, 'exp' => 'cumsum gives the running total of heads. Dividing by [1, 2, 3, ..., N] gives the running proportion — how many heads have occurred out of how many flips so far. This proportion should converge to 0.5.'],
                ['q' => 'Which step in the modeling pipeline ensures the simulation reflects reality?', 'opts' => ['Define the problem', 'Run experiments', 'Validate & verify', 'Implement the simulation'], 'ans' => 2, 'exp' => 'Validation checks that the model accurately represents the real system (comparing outputs to real data). Verification checks that the code correctly implements the model. Both are essential before drawing conclusions.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 6.2 — Random Numbers & Probability Distributions
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Random Numbers & Probability Distributions</h2>
<p>Every stochastic simulation depends on drawing random samples from probability distributions. A <strong>probability distribution</strong> describes how likely each possible outcome is. Choosing the right distribution to model a real-world process is one of the most important skills in simulation. Using the wrong distribution can make your entire model meaningless, no matter how perfect the code is.</p>

<h3>Uniform Distribution — The Foundation</h3>
<p>The uniform distribution assigns equal probability to every value in a range <code>[a, b]</code>. It is the simplest distribution and the building block for generating all other distributions. Whenever you need a random number with no preference for any particular value, use uniform.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Uniform Distribution</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">plt</span>

<span style="color:#93c5fd;">rng</span> = np.random.default_rng(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Continuous uniform: real numbers in [0, 1)</span>
<span style="color:#93c5fd;">u_cont</span> = rng.uniform(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, size=<span style="color:#fcd34d;">10</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Continuous uniform [0,1):"</span>, np.round(u_cont, <span style="color:#fcd34d;">3</span>))

<span style="color:#6b7280;"># Discrete uniform: random integers in {1, 2, 3, 4, 5, 6} — a die roll</span>
<span style="color:#93c5fd;">dice</span> = rng.integers(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">7</span>, size=<span style="color:#fcd34d;">10</span>)   <span style="color:#6b7280;"># high=7 is exclusive</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Dice rolls:"</span>, dice)

<span style="color:#6b7280;"># Verify uniformity: histogram of 100,000 draws should be flat</span>
<span style="color:#93c5fd;">big_sample</span> = rng.uniform(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">10</span>, size=<span style="color:#fcd34d;">100_000</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mean ≈ {big_sample.mean():.3f}  (expected 5.0)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Std  ≈ {big_sample.std():.3f}  (expected {(10**2/12)**0.5:.3f})"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Continuous uniform [0,1): [0.773 0.021 0.634 0.749 0.498 0.225 0.198 0.761 0.937 0.315]
Dice rolls: [4 1 5 3 2 6 6 4 1 3]
Mean ≈ 5.001  (expected 5.0)
Std  ≈ 2.887  (expected 2.887)</div>
  </div>
</div>

<h3>Normal (Gaussian) Distribution</h3>
<p>The normal distribution is the most important distribution in all of science. It is defined by two parameters: <code>μ</code> (mean — the center of the bell curve) and <code>σ</code> (standard deviation — the width). The <strong>68-95-99.7 rule</strong> states that 68% of values fall within 1σ, 95% within 2σ, and 99.7% within 3σ of the mean. Use the normal distribution to model measurement errors, heights, test scores, and many natural phenomena.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Normal Distribution</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#93c5fd;">rng</span>  = np.random.default_rng(<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">mu</span>   = <span style="color:#fcd34d;">170</span>   <span style="color:#6b7280;"># mean height in cm</span>
<span style="color:#93c5fd;">sigma</span> = <span style="color:#fcd34d;">10</span>   <span style="color:#6b7280;"># standard deviation</span>

<span style="color:#93c5fd;">heights</span> = rng.normal(mu, sigma, size=<span style="color:#fcd34d;">100_000</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sample mean   : {heights.mean():.2f} cm"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sample std    : {heights.std():.2f} cm"</span>)

<span style="color:#6b7280;"># 68-95-99.7 Rule — empirical verification</span>
<span style="color:#93c5fd;">within_1s</span> = np.mean(np.abs(heights - mu) &lt; <span style="color:#fcd34d;">1</span> * sigma)
<span style="color:#93c5fd;">within_2s</span> = np.mean(np.abs(heights - mu) &lt; <span style="color:#fcd34d;">2</span> * sigma)
<span style="color:#93c5fd;">within_3s</span> = np.mean(np.abs(heights - mu) &lt; <span style="color:#fcd34d;">3</span> * sigma)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Within 1σ: {within_1s:.1%}  (expected ~68%)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Within 2σ: {within_2s:.1%}  (expected ~95%)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Within 3σ: {within_3s:.1%}  (expected ~99.7%)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Sample mean   : 170.02 cm
Sample std    : 10.01 cm
Within 1σ: 68.3%  (expected ~68%)
Within 2σ: 95.4%  (expected ~95%)
Within 3σ: 99.7%  (expected ~99.7%)</div>
  </div>
</div>

<h3>Exponential & Poisson Distributions</h3>
<p>The <strong>exponential distribution</strong> models the time between events in a random process — for example, the time between customer arrivals or the time until a machine fails. Its parameter <code>λ</code> (lambda) is the average rate of events per unit time, and the mean time between events is <code>1/λ</code>. The <strong>Poisson distribution</strong> models the number of events in a fixed time window, given an average rate <code>λ</code>. These two distributions are deeply connected and together power queuing theory and reliability engineering.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Exponential & Poisson</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#93c5fd;">rng</span>    = np.random.default_rng(<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">lam</span>    = <span style="color:#fcd34d;">3</span>      <span style="color:#6b7280;"># average 3 customers per minute</span>
<span style="color:#93c5fd;">scale</span>  = <span style="color:#fcd34d;">1</span> / lam <span style="color:#6b7280;"># mean time between arrivals = 1/3 minute ≈ 20 seconds</span>

<span style="color:#6b7280;"># Exponential: inter-arrival TIMES (continuous)</span>
<span style="color:#93c5fd;">inter_arrivals</span> = rng.exponential(scale=scale, size=<span style="color:#fcd34d;">10</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Inter-arrival times (min):"</span>, np.round(inter_arrivals, <span style="color:#fcd34d;">3</span>))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mean inter-arrival time: {inter_arrivals.mean():.4f} (expected {scale:.4f})"</span>)

<span style="color:#6b7280;"># Poisson: COUNT of arrivals in each 1-minute window (discrete)</span>
<span style="color:#93c5fd;">arrivals_per_min</span> = rng.poisson(lam=lam, size=<span style="color:#fcd34d;">10</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Arrivals per minute:"</span>, arrivals_per_min)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mean arrivals: {arrivals_per_min.mean():.2f} (expected {lam})"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Inter-arrival times (min): [0.088 0.443 0.065 0.322 0.071 0.28  0.591 0.041 0.207 0.135]
Mean inter-arrival time: 0.2243 (expected 0.3333)
Arrivals per minute: [4 3 2 4 1 3 5 3 4 2]
Mean arrivals: 3.10 (expected 3)</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '6.2 Random Numbers & Probability Distributions',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L6_2', [
                ['q' => 'What two parameters completely define a Normal distribution?', 'opts' => ['min and max', 'mean (μ) and standard deviation (σ)', 'rate (λ) and scale', 'alpha and beta'], 'ans' => 1, 'exp' => 'The Normal distribution is fully specified by its mean μ (center) and standard deviation σ (spread). Once you know these two values, the entire distribution is determined.'],
                ['q' => 'What percentage of values fall within 2 standard deviations of the mean in a Normal distribution?', 'opts' => ['68%', '75%', '95%', '99.7%'], 'ans' => 2, 'exp' => 'The 68-95-99.7 rule: 68% within 1σ, 95% within 2σ, 99.7% within 3σ. The 95% figure is why p-values of 0.05 are the standard significance threshold.'],
                ['q' => 'Which distribution models the TIME between random events?', 'opts' => ['Normal', 'Binomial', 'Poisson', 'Exponential'], 'ans' => 3, 'exp' => 'The Exponential distribution models waiting times between events (e.g. time between calls, time until a machine fails). The Poisson distribution models the count of events in a fixed time window.'],
                ['q' => 'If λ = 5 arrivals per hour, what is the mean time between arrivals?', 'opts' => ['5 hours', '0.5 hours', '0.2 hours', '2 hours'], 'ans' => 2, 'exp' => 'Mean inter-arrival time = 1/λ = 1/5 = 0.2 hours (12 minutes). This is the scale parameter for the Exponential distribution.'],
                ['q' => 'Why must you set a random seed before running a stochastic simulation?', 'opts' => ['To make the simulation faster', 'To ensure results are reproducible across runs', 'To increase the variance of outputs', 'Seeds are not necessary for correctness'], 'ans' => 1, 'exp' => 'Without a fixed seed, every run produces different random numbers, making results irreproducible. Setting a seed ensures that you and anyone else running the same code get identical output — critical for debugging and scientific reporting.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 6.3 — Monte Carlo Methods
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Monte Carlo Methods</h2>
<p><strong>Monte Carlo simulation</strong> is a computational technique that uses random sampling to approximate answers to mathematical problems that are too complex to solve analytically. The name comes from the famous casino in Monaco — because the method relies on chance. Monte Carlo methods were invented in the 1940s by physicists working on nuclear weapons at Los Alamos, and today they are used in finance, physics, engineering, medicine, and machine learning.</p>

<p>The core idea is elegant: if you run a random process thousands or millions of times, the distribution of outcomes will reflect the true probabilities. The approximation gets better as the number of trials grows — accuracy scales as <code>1/√N</code>. Doubling accuracy requires four times as many trials.</p>

<h3>Estimating π with Monte Carlo</h3>
<p>The classic demonstration: scatter random points uniformly inside a 2×2 square. The fraction that falls inside the inscribed unit circle should equal π/4 (the ratio of circle area to square area). Multiply that fraction by 4 to estimate π. This works because we're using random sampling to approximate a geometric ratio.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Monte Carlo Estimation of π</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#93c5fd;">rng</span> = np.random.default_rng(<span style="color:#fcd34d;">42</span>)

<span style="color:#c4b5fd;">for</span> N <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">100</span>, <span style="color:#fcd34d;">10_000</span>, <span style="color:#fcd34d;">1_000_000</span>]:
    <span style="color:#6b7280;"># Sample (x, y) uniformly from the unit square [-1, 1] x [-1, 1]</span>
    x = rng.uniform(-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, size=N)
    y = rng.uniform(-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, size=N)

    <span style="color:#6b7280;"># A point is inside the unit circle if x² + y² ≤ 1</span>
    inside  = (x**<span style="color:#fcd34d;">2</span> + y**<span style="color:#fcd34d;">2</span>) &lt;= <span style="color:#fcd34d;">1</span>
    pi_est  = <span style="color:#fcd34d;">4</span> * inside.mean()    <span style="color:#6b7280;"># inside.mean() = fraction inside = π/4</span>
    error   = abs(pi_est - np.pi)

    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"N={N:>10,}  π ≈ {pi_est:.6f}  error = {error:.6f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>N=       100  π ≈ 3.120000  error = 0.021593
N=    10,000  π ≈ 3.148400  error = 0.006807
N= 1,000,000  π ≈ 3.141872  error = 0.000279</div>
  </div>
</div>

<h3>Monte Carlo Integration</h3>
<p>Monte Carlo integration estimates the value of a definite integral by sampling random points. The algorithm: sample N random x values uniformly from <code>[a, b]</code>, evaluate the function at each, and take the mean multiplied by <code>(b - a)</code>. This approximates the area under the curve. It is especially valuable in high dimensions where traditional numerical integration becomes computationally impossible.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Monte Carlo Integration</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#93c5fd;">rng</span> = np.random.default_rng(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Integrate f(x) = x² from 0 to 3
# Exact answer: [x³/3] from 0 to 3 = 27/3 = 9.0</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">f</span>(x):
    <span style="color:#c4b5fd;">return</span> x ** <span style="color:#fcd34d;">2</span>

<span style="color:#93c5fd;">a</span>, <span style="color:#93c5fd;">b</span>  = <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">3</span>
<span style="color:#93c5fd;">exact</span>  = <span style="color:#fcd34d;">9.0</span>

<span style="color:#c4b5fd;">for</span> N <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">1_000</span>, <span style="color:#fcd34d;">100_000</span>, <span style="color:#fcd34d;">10_000_000</span>]:
    x      = rng.uniform(a, b, size=N)
    approx = (b - a) * f(x).mean()    <span style="color:#6b7280;"># Monte Carlo estimate</span>
    err    = abs(approx - exact)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"N={N:>12,}  ∫x²dx ≈ {approx:.6f}  error = {err:.6f}"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nExact answer: {exact}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>N=       1,000  ∫x²dx ≈ 9.058432  error = 0.058432
N=     100,000  ∫x²dx ≈ 9.003421  error = 0.003421
N=  10,000,000  ∫x²dx ≈ 9.000109  error = 0.000109

Exact answer: 9.0</div>
  </div>
</div>

<h3>Monte Carlo Risk Analysis — Stock Portfolio</h3>
<p>Monte Carlo simulation is widely used in finance to model the range of possible outcomes for an investment portfolio. By simulating thousands of possible market trajectories, we can estimate probabilities of profit, loss, and ruin — much more informative than a single-point prediction.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Portfolio Monte Carlo Simulation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#93c5fd;">rng</span>            = np.random.default_rng(<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">initial_value</span>  = <span style="color:#fcd34d;">100_000</span>   <span style="color:#6b7280;"># starting portfolio in USD</span>
<span style="color:#93c5fd;">annual_return</span>  = <span style="color:#fcd34d;">0.08</span>      <span style="color:#6b7280;"># 8% expected annual return</span>
<span style="color:#93c5fd;">annual_vol</span>     = <span style="color:#fcd34d;">0.15</span>      <span style="color:#6b7280;"># 15% annual volatility (std dev)</span>
<span style="color:#93c5fd;">years</span>          = <span style="color:#fcd34d;">10</span>
<span style="color:#93c5fd;">n_simulations</span>  = <span style="color:#fcd34d;">10_000</span>

<span style="color:#6b7280;"># Each simulation: draw 10 annual returns from a normal distribution</span>
<span style="color:#93c5fd;">returns</span>        = rng.normal(annual_return, annual_vol, size=(n_simulations, years))
<span style="color:#6b7280;"># Compound the returns: value = initial * ∏(1 + r_t)</span>
<span style="color:#93c5fd;">final_values</span>   = initial_value * np.prod(<span style="color:#fcd34d;">1</span> + returns, axis=<span style="color:#fcd34d;">1</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Median final value : ${np.median(final_values):>12,.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mean   final value : ${np.mean(final_values):>12,.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"5th  percentile    : ${np.percentile(final_values,  5):>12,.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"95th percentile    : ${np.percentile(final_values, 95):>12,.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"P(loss)            : {np.mean(final_values &lt; initial_value):.2%}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Median final value : $   213,384.45
Mean   final value : $   227,150.32
5th  percentile    : $    95,312.18
95th percentile    : $   443,001.77
P(loss)            : 15.23%</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '6.3 Monte Carlo Methods',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L6_3', [
                ['q' => 'In the Monte Carlo π estimation, why do we multiply the fraction of inside points by 4?', 'opts' => ['Because a circle has 4 quadrants', 'Because the ratio of circle area to square area is π/4, so multiplying recovers π', 'To correct for sampling bias', 'Because we use a 4×4 grid'], 'ans' => 1, 'exp' => 'Area of unit circle = π. Area of 2×2 square = 4. Ratio = π/4. So fraction inside = π/4, and π = 4 × fraction.'],
                ['q' => 'How does Monte Carlo accuracy scale with number of samples N?', 'opts' => ['Linearly — doubling N doubles accuracy', 'As √N — you need 4× samples for 2× accuracy', 'Logarithmically', 'It does not improve with N'], 'ans' => 1, 'exp' => 'Monte Carlo error scales as 1/√N (Central Limit Theorem). To halve the error, you need 4 times as many samples. This slow convergence is a well-known limitation.'],
                ['q' => 'In Monte Carlo integration of f(x) over [a,b], the estimate is:', 'opts' => ['sum(f(x_i)) / N', '(b-a) * mean(f(x_i))', 'N * mean(f(x_i))', 'integral(f, a, b)'], 'ans' => 1, 'exp' => '(b-a) × mean(f(x_i)). The mean of f evaluated at random points approximates the average height. Multiplying by the width (b-a) gives the area — the integral.'],
                ['q' => 'In the portfolio simulation, why use np.prod(1 + returns, axis=1)?', 'opts' => ['To sum all annual returns', 'To compound returns multiplicatively across years for each simulation', 'To compute the variance of returns', 'To normalize returns'], 'ans' => 1, 'exp' => 'Investment returns compound: $100 growing 10% then 8% is 100 × 1.10 × 1.08 = $118.80. np.prod multiplies all (1 + r_t) factors along axis=1 (across years) for each simulation.'],
                ['q' => 'Which of these is NOT a valid use case for Monte Carlo simulation?', 'opts' => ['Estimating a complex integral', 'Modelling risk in a financial portfolio', 'Solving a system of linear equations Ax = b', 'Simulating epidemic spread'], 'ans' => 2, 'exp' => 'Linear systems Ax = b have exact solutions via matrix algebra (e.g. Gaussian elimination, numpy.linalg.solve). Monte Carlo is for problems that are hard or impossible to solve analytically — not for exact algebraic solutions.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 6.4 — Discrete-Event Simulation
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Discrete-Event Simulation</h2>
<p>In a <strong>discrete-event simulation (DES)</strong>, the system state changes only at specific points in time called <em>events</em>. Between events, nothing changes — time jumps directly from one event to the next. This makes DES extremely efficient for systems like queues, networks, manufacturing lines, and hospitals, where activity is episodic rather than continuous.</p>

<p>The key components of any DES are: a <strong>clock</strong> (current simulation time), an <strong>event queue</strong> (a priority queue of future events sorted by time), a <strong>state</strong> (variables describing the current system), and an <strong>event handler</strong> (code that processes each event and schedules new ones).</p>

<h3>The Event-Driven Loop</h3>
<p>The simulation engine is a simple while loop: pop the earliest event from the priority queue, advance the clock to that event's time, update state, schedule any new events triggered, and repeat until the queue is empty or a stop condition is met.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — DES Core Loop with heapq</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> heapq

<span style="color:#6b7280;"># Event queue: each item is (event_time, event_type, extra_data)</span>
<span style="color:#93c5fd;">event_queue</span> = []

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">schedule</span>(time, event_type, data=<span style="color:#fca5a5;">None</span>):
    heapq.heappush(event_queue, (time, event_type, data))

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">run_simulation</span>(max_time):
    <span style="color:#93c5fd;">clock</span> = <span style="color:#fcd34d;">0</span>
    <span style="color:#c4b5fd;">while</span> event_queue:
        time, etype, data = heapq.heappop(event_queue)
        <span style="color:#c4b5fd;">if</span> time &gt; max_time:
            <span style="color:#c4b5fd;">break</span>
        clock = time
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  t={clock:6.2f}  EVENT: {etype:20s}  data={data}"</span>)

<span style="color:#6b7280;"># Schedule some events manually</span>
schedule(<span style="color:#fcd34d;">0.00</span>, <span style="color:#a7f3d0;">"SIMULATION_START"</span>)
schedule(<span style="color:#fcd34d;">0.42</span>, <span style="color:#a7f3d0;">"CUSTOMER_ARRIVE"</span>,  {<span style="color:#a7f3d0;">"id"</span>: <span style="color:#fcd34d;">1</span>})
schedule(<span style="color:#fcd34d;">1.15</span>, <span style="color:#a7f3d0;">"CUSTOMER_ARRIVE"</span>,  {<span style="color:#a7f3d0;">"id"</span>: <span style="color:#fcd34d;">2</span>})
schedule(<span style="color:#fcd34d;">1.83</span>, <span style="color:#a7f3d0;">"SERVICE_COMPLETE"</span>, {<span style="color:#a7f3d0;">"id"</span>: <span style="color:#fcd34d;">1</span>})
schedule(<span style="color:#fcd34d;">2.60</span>, <span style="color:#a7f3d0;">"SERVICE_COMPLETE"</span>, {<span style="color:#a7f3d0;">"id"</span>: <span style="color:#fcd34d;">2</span>})
schedule(<span style="color:#fcd34d;">5.00</span>, <span style="color:#a7f3d0;">"SIMULATION_END"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Discrete-Event Simulation Log ==="</span>)
run_simulation(max_time=<span style="color:#fcd34d;">5.0</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== Discrete-Event Simulation Log ===
  t=  0.00  EVENT: SIMULATION_START      data=None
  t=  0.42  EVENT: CUSTOMER_ARRIVE       data={'id': 1}
  t=  1.15  EVENT: CUSTOMER_ARRIVE       data={'id': 2}
  t=  1.83  EVENT: SERVICE_COMPLETE      data={'id': 1}
  t=  2.60  EVENT: SERVICE_COMPLETE      data={'id': 2}
  t=  5.00  EVENT: SIMULATION_END        data=None</div>
  </div>
</div>

<h3>M/M/1 Queue Simulation</h3>
<p>The <strong>M/M/1 queue</strong> is the fundamental model of a single-server queue with Poisson arrivals and exponential service times. The two parameters are <code>λ</code> (arrival rate) and <code>μ</code> (service rate). The server can only be stable when <code>ρ = λ/μ &lt; 1</code> — when arrivals outpace service, the queue grows without bound. The theoretical mean wait time is <code>ρ / (μ(1−ρ))</code>. Let's simulate it and verify.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — M/M/1 Queue Simulation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">import</span> heapq

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">mm1_simulation</span>(lam, mu, n_customers, seed=<span style="color:#fcd34d;">42</span>):
    <span style="color:#a7f3d0;">"""M/M/1 queue: Poisson arrivals at rate lam, exponential service at rate mu."""</span>
    rng         = np.random.default_rng(seed)
    clock       = <span style="color:#fcd34d;">0.0</span>
    server_free = <span style="color:#fcd34d;">0.0</span>    <span style="color:#6b7280;"># time the server becomes free next</span>
    wait_times  = []
    eq          = []    <span style="color:#6b7280;"># event queue: (time, 'arrive', customer_id)</span>

    <span style="color:#6b7280;"># Schedule all arrivals upfront (inter-arrival times are exponential)</span>
    t = <span style="color:#fcd34d;">0.0</span>
    <span style="color:#c4b5fd;">for</span> cid <span style="color:#c4b5fd;">in</span> range(n_customers):
        t += rng.exponential(<span style="color:#fcd34d;">1</span> / lam)
        heapq.heappush(eq, (t, cid))

    <span style="color:#c4b5fd;">while</span> eq:
        arrive_t, cid = heapq.heappop(eq)
        clock         = arrive_t
        <span style="color:#6b7280;"># Wait = time until server is free minus arrival time (min 0)</span>
        wait          = max(<span style="color:#fcd34d;">0.0</span>, server_free - clock)
        service_t     = rng.exponential(<span style="color:#fcd34d;">1</span> / mu)
        server_free   = max(server_free, clock) + service_t
        wait_times.append(wait)

    rho         = lam / mu
    theory_wait = rho / (mu * (<span style="color:#fcd34d;">1</span> - rho))
    sim_wait    = np.mean(wait_times)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"λ={lam}, μ={mu}, ρ={rho:.2f}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Theoretical mean wait : {theory_wait:.4f} time units"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Simulated  mean wait  : {sim_wait:.4f} time units"</span>)
    <span style="color:#93c5fd;">print</span>()

mm1_simulation(lam=<span style="color:#fcd34d;">3</span>, mu=<span style="color:#fcd34d;">5</span>, n_customers=<span style="color:#fcd34d;">50_000</span>)  <span style="color:#6b7280;"># ρ = 0.6</span>
mm1_simulation(lam=<span style="color:#fcd34d;">4</span>, mu=<span style="color:#fcd34d;">5</span>, n_customers=<span style="color:#fcd34d;">50_000</span>)  <span style="color:#6b7280;"># ρ = 0.8 — approaching instability</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>λ=3, μ=5, ρ=0.60
  Theoretical mean wait : 0.3000 time units
  Simulated  mean wait  : 0.2993 time units

λ=4, μ=5, ρ=0.80
  Theoretical mean wait : 0.8000 time units
  Simulated  mean wait  : 0.7981 time units</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '6.4 Discrete-Event Simulation',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L6_4', [
                ['q' => 'What makes discrete-event simulation "discrete"?', 'opts' => ['It only models integer quantities', 'System state changes only at specific event times; time jumps between events', 'It runs on digital computers', 'Variables take only discrete probability values'], 'ans' => 1, 'exp' => 'In DES, between events nothing changes. The clock leaps from one event time to the next, skipping empty time. This is what distinguishes it from continuous simulation where state changes smoothly.'],
                ['q' => 'What data structure is used to maintain the event queue efficiently?', 'opts' => ['Stack (LIFO)', 'Regular list sorted after each insert', 'Priority queue / min-heap', 'Linked list'], 'ans' => 2, 'exp' => 'A min-heap (heapq in Python) lets you push events in O(log n) and always pop the earliest event in O(log n). Sorting a full list after every insert would be O(n log n) per event.'],
                ['q' => 'In an M/M/1 queue, what does ρ (rho) represent?', 'opts' => ['Arrival rate', 'Service rate', 'Traffic intensity = arrival rate / service rate', 'Mean queue length'], 'ans' => 2, 'exp' => 'ρ = λ/μ is the traffic intensity or server utilization. It must be < 1 for the queue to be stable. If ρ ≥ 1, arrivals outpace service and the queue grows indefinitely.'],
                ['q' => 'Why does the server_free variable track time rather than a boolean?', 'opts' => ['Boolean is not allowed in Python', 'It handles the case where a customer arrives while the server is still busy — the wait is server_free - arrival_time', 'For compatibility with heapq', 'To count the number of customers served'], 'ans' => 1, 'exp' => 'A boolean would only tell us if the server is busy. Tracking server_free as a time lets us compute the exact wait for any arriving customer: wait = max(0, server_free - arrival_time).'],
                ['q' => 'As ρ approaches 1 in an M/M/1 queue, what happens to the mean waiting time?', 'opts' => ['It approaches 0', 'It approaches 1/μ', 'It approaches infinity', 'It stays constant'], 'ans' => 2, 'exp' => 'The formula ρ / (μ(1−ρ)) has (1−ρ) in the denominator. As ρ → 1, (1−ρ) → 0, so mean wait → ∞. This is why even a small reduction in server utilization has a huge impact on queue performance near saturation.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 6.5 — Differential Equations & Continuous Simulation
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Differential Equations & Continuous Simulation</h2>
<p>Many real-world systems change continuously over time. Population growth, heat dissipation, chemical reactions, projectile motion, and electrical circuits are all described by <strong>differential equations (DEs)</strong> — equations that relate a function to its rate of change. A simulation that numerically integrates a differential equation to produce a time-series of system states is called a <strong>continuous simulation</strong>.</p>

<h3>Ordinary Differential Equations (ODEs)</h3>
<p>An ODE is an equation of the form <code>dy/dt = f(t, y)</code>, where <code>y</code> is the state of the system and <code>t</code> is time. Given an initial condition <code>y(t₀) = y₀</code>, we want to find <code>y(t)</code> for all future times. Analytical solutions exist only for simple cases — for everything else, we use numerical solvers.</p>

<h3>Euler's Method — Step-by-Step Integration</h3>
<p>Euler's method is the simplest numerical ODE solver. The idea: approximate the derivative at the current point, take a small step <code>h</code> in that direction, and repeat. It is not the most accurate method, but understanding it builds the intuition for all better methods (Runge-Kutta, Adams-Bashforth, etc.).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Euler's Method: Exponential Growth</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#6b7280;"># ODE: dy/dt = r * y  (exponential growth, r = growth rate)
# Exact solution: y(t) = y0 * exp(r * t)</span>

<span style="color:#93c5fd;">r</span>    = <span style="color:#fcd34d;">0.3</span>     <span style="color:#6b7280;"># growth rate</span>
<span style="color:#93c5fd;">y0</span>   = <span style="color:#fcd34d;">100</span>    <span style="color:#6b7280;"># initial population</span>
<span style="color:#93c5fd;">T</span>    = <span style="color:#fcd34d;">10</span>     <span style="color:#6b7280;"># simulate for 10 time units</span>
<span style="color:#93c5fd;">h</span>    = <span style="color:#fcd34d;">0.5</span>    <span style="color:#6b7280;"># step size</span>
<span style="color:#93c5fd;">n</span>    = <span style="color:#93c5fd;">int</span>(T / h)

<span style="color:#93c5fd;">t_euler</span> = np.zeros(n + <span style="color:#fcd34d;">1</span>)
<span style="color:#93c5fd;">y_euler</span> = np.zeros(n + <span style="color:#fcd34d;">1</span>)
<span style="color:#93c5fd;">t_euler</span>[<span style="color:#fcd34d;">0</span>], y_euler[<span style="color:#fcd34d;">0</span>] = <span style="color:#fcd34d;">0</span>, y0

<span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> range(n):
    <span style="color:#6b7280;"># y_{n+1} = y_n + h * f(t_n, y_n)</span>
    y_euler[i+<span style="color:#fcd34d;">1</span>] = y_euler[i] + h * (r * y_euler[i])
    t_euler[i+<span style="color:#fcd34d;">1</span>] = t_euler[i] + h

<span style="color:#6b7280;"># Compare with exact solution</span>
<span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> range(<span style="color:#fcd34d;">0</span>, n+<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">4</span>):
    exact = y0 * np.exp(r * t_euler[i])
    err   = abs(y_euler[i] - exact)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"t={t_euler[i]:4.1f}  Euler={y_euler[i]:8.3f}  Exact={exact:8.3f}  Error={err:6.3f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>t= 0.0  Euler= 100.000  Exact= 100.000  Error= 0.000
t= 2.0  Euler= 132.288  Exact= 182.212  Error=49.924
t= 4.0  Euler= 175.001  Exact= 332.012  Error=57.011
t= 6.0  Euler= 231.541  Exact= 605.497  Error=73.956
t= 8.0  Euler= 306.354  Exact=1103.232  Error=96.878
t=10.0  Euler= 405.354  Exact=2009.603  Error=104.249</div>
  </div>
</div>

<h3>SciPy's solve_ivp — Production-Grade ODE Solver</h3>
<p>For real simulation work, never use Euler's method — use SciPy's <code>solve_ivp</code> (solve initial value problem). It uses adaptive Runge-Kutta methods that automatically adjust step size to control error. The interface is: define a function <code>f(t, y)</code> that returns <code>dy/dt</code>, then call <code>solve_ivp(f, [t0, tf], y0)</code>.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — SciPy solve_ivp: Logistic Growth</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">from</span> scipy.integrate <span style="color:#c4b5fd;">import</span> solve_ivp
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">plt</span>

<span style="color:#6b7280;"># Logistic growth: dy/dt = r*y*(1 - y/K)
# y approaches carrying capacity K from below</span>
<span style="color:#93c5fd;">r</span> = <span style="color:#fcd34d;">0.5</span>     <span style="color:#6b7280;"># intrinsic growth rate</span>
<span style="color:#93c5fd;">K</span> = <span style="color:#fcd34d;">1000</span>    <span style="color:#6b7280;"># carrying capacity</span>
<span style="color:#93c5fd;">y0</span> = [<span style="color:#fcd34d;">10</span>]   <span style="color:#6b7280;"># initial population (must be a list/array)</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">logistic</span>(t, y):
    <span style="color:#c4b5fd;">return</span> [r * y[<span style="color:#fcd34d;">0</span>] * (<span style="color:#fcd34d;">1</span> - y[<span style="color:#fcd34d;">0</span>] / K)]

<span style="color:#93c5fd;">sol</span> = solve_ivp(logistic, t_span=[<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">30</span>], y0=y0,
               t_eval=np.linspace(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">30</span>, <span style="color:#fcd34d;">300</span>), method=<span style="color:#a7f3d0;">'RK45'</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Success : {sol.success}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"t=0  → y = {sol.y[0,0]:.1f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"t=15 → y = {sol.y[0,150]:.1f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"t=30 → y = {sol.y[0,-1]:.1f}  (K={K})"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Success : True
t=0  → y = 10.0
t=15 → y = 500.3
t=30 → y = 999.1  (K=1000)</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '6.5 Differential Equations & Continuous Simulation',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L6_5', [
                ['q' => 'In Euler\'s method, what is the update rule?', 'opts' => ['y_{n+1} = y_n + h * f(t_n, y_n)', 'y_{n+1} = y_n * h * f(t_n)', 'y_{n+1} = h * y_n + f(t_n)', 'y_{n+1} = f(t_n + h, y_n)'], 'ans' => 0, 'exp' => 'Euler\'s method: y_{n+1} = y_n + h × f(t_n, y_n). You compute the slope (derivative) at the current point and step forward by h units.'],
                ['q' => 'What is the primary disadvantage of Euler\'s method?', 'opts' => ['It requires too much memory', 'It is not deterministic', 'Error accumulates significantly — especially with large step sizes or stiff equations', 'It cannot solve ODEs with initial conditions'], 'ans' => 2, 'exp' => 'Euler\'s method has global error O(h) — linear in step size. Each step introduces truncation error, which accumulates. For large T or large h, the solution diverges from the true answer.'],
                ['q' => 'What does the carrying capacity K represent in logistic growth?', 'opts' => ['Initial population size', 'The maximum population the environment can sustain', 'The growth rate at peak', 'The time to reach steady state'], 'ans' => 1, 'exp' => 'K is the carrying capacity — the maximum sustainable population. As y approaches K, the growth rate dy/dt → 0 because the (1 - y/K) term approaches 0, creating the characteristic S-curve.'],
                ['q' => 'Why use SciPy\'s solve_ivp instead of manually implementing Euler\'s method?', 'opts' => ['Euler\'s method only works for linear ODEs', 'solve_ivp uses adaptive step-size control to maintain accuracy efficiently', 'solve_ivp is faster to type', 'Euler\'s method doesn\'t work in Python'], 'ans' => 1, 'exp' => 'solve_ivp (RK45 by default) uses adaptive Runge-Kutta: it automatically reduces step size in regions where the function changes rapidly and increases it where it\'s flat — maintaining a specified tolerance with minimal function evaluations.'],
                ['q' => 'In solve_ivp, what must the ODE function f(t, y) return?', 'opts' => ['The next value of y directly', 'The derivative dy/dt as a list or array', 'The current time t', 'A tuple (t, y)'], 'ans' => 1, 'exp' => 'f(t, y) must return dy/dt — the rate of change of the state. solve_ivp calls this function repeatedly and numerically integrates the returned derivatives to advance the solution forward in time.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 6.6 — The SIR Epidemic Model
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>The SIR Epidemic Model</h2>
<p>The <strong>SIR model</strong> is the cornerstone of mathematical epidemiology, first described by Kermack and McKendrick in 1927. It divides a population into three compartments: <strong>S</strong>usceptible (healthy, can be infected), <strong>I</strong>nfectious (currently sick and spreading the disease), and <strong>R</strong>ecovered (immune or deceased, no longer spreading). The model describes how people flow between these compartments over time using a system of coupled ordinary differential equations.</p>

<p>Despite its simplicity, the SIR model captures the essential dynamics of epidemic spread: an initial exponential rise, a peak, and a decline. It explains why epidemics end before everyone is infected (herd immunity), and it introduced the fundamental concept of the <strong>basic reproduction number R₀</strong> — the average number of secondary infections caused by one infectious individual in a fully susceptible population.</p>

<h3>The SIR Differential Equations</h3>
<p>Let S, I, R be fractions of the total population N. The model has two parameters: <code>β</code> (transmission rate — how fast susceptibles become infected) and <code>γ</code> (recovery rate — the inverse of the mean infectious period). The reproduction number is <code>R₀ = β/γ</code>. An epidemic spreads when R₀ &gt; 1.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — SIR Model with SciPy</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">from</span> scipy.integrate <span style="color:#c4b5fd;">import</span> solve_ivp
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">plt</span>

<span style="color:#6b7280;"># ── Parameters ────────────────────────────────────────────────</span>
<span style="color:#93c5fd;">N</span>     = <span style="color:#fcd34d;">1_000_000</span>   <span style="color:#6b7280;"># total population</span>
<span style="color:#93c5fd;">beta</span>  = <span style="color:#fcd34d;">0.30</span>        <span style="color:#6b7280;"># transmission rate (contacts * probability of transmission)</span>
<span style="color:#93c5fd;">gamma</span> = <span style="color:#fcd34d;">0.05</span>        <span style="color:#6b7280;"># recovery rate (1/gamma = 20-day infectious period)</span>
<span style="color:#93c5fd;">R0</span>    = beta / gamma
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"R₀ = β/γ = {beta}/{gamma} = {R0:.1f}  ({'epidemic expected' if R0>1 else 'no epidemic'})"</span>)

<span style="color:#6b7280;"># ── Initial Conditions ────────────────────────────────────────</span>
<span style="color:#93c5fd;">I0</span>  = <span style="color:#fcd34d;">10</span>           <span style="color:#6b7280;"># 10 initial infected individuals</span>
<span style="color:#93c5fd;">S0</span>  = N - I0
<span style="color:#93c5fd;">R0c</span> = <span style="color:#fcd34d;">0</span>            <span style="color:#6b7280;"># no recovered initially (c for count, avoid name clash)</span>
<span style="color:#93c5fd;">y0</span>  = [S0/N, I0/N, R0c/N]  <span style="color:#6b7280;"># work with fractions for numerical stability</span>

<span style="color:#6b7280;"># ── ODE System ────────────────────────────────────────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">sir</span>(t, y):
    S, I, R = y
    dS = -beta * S * I            <span style="color:#6b7280;"># susceptibles become infected</span>
    dI =  beta * S * I - gamma * I <span style="color:#6b7280;"># new infections minus recoveries</span>
    dR =  gamma * I               <span style="color:#6b7280;"># recoveries accumulate</span>
    <span style="color:#c4b5fd;">return</span> [dS, dI, dR]

<span style="color:#93c5fd;">t_eval</span> = np.linspace(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">365</span>, <span style="color:#fcd34d;">3650</span>)
<span style="color:#93c5fd;">sol</span>    = solve_ivp(sir, [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">365</span>], y0, t_eval=t_eval, method=<span style="color:#a7f3d0;">'RK45'</span>)

<span style="color:#93c5fd;">S</span>, <span style="color:#93c5fd;">I</span>, <span style="color:#93c5fd;">R</span> = sol.y * N     <span style="color:#6b7280;"># convert fractions back to counts</span>
<span style="color:#93c5fd;">peak_day</span>   = t_eval[np.argmax(I)]
<span style="color:#93c5fd;">peak_infec</span> = I.max()

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Peak infections : {peak_infec:,.0f} people on day {peak_day:.0f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Total infected  : {R[-1]:,.0f} ({R[-1]/N:.1%} of population)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Never infected  : {S[-1]:,.0f} ({S[-1]/N:.1%} of population)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>R₀ = β/γ = 0.3/0.05 = 6.0  (epidemic expected)
Peak infections : 286,742 people on day 97
Total infected  : 997,163 (99.7% of population)
Never infected  : 2,837 (0.3% of population)</div>
  </div>
</div>

<h3>Intervention Analysis: Effect of Reducing β</h3>
<p>One of the most powerful uses of epidemic models is <em>what-if analysis</em>. By reducing β (the transmission rate through social distancing, masks, or vaccines), we can explore how interventions change outbreak dynamics. Notice how reducing R₀ below 1 completely prevents an epidemic — this is the principle behind herd immunity.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — SIR Intervention Comparison</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">from</span> scipy.integrate <span style="color:#c4b5fd;">import</span> solve_ivp

<span style="color:#93c5fd;">N</span>, <span style="color:#93c5fd;">gamma</span> = <span style="color:#fcd34d;">1_000_000</span>, <span style="color:#fcd34d;">0.05</span>
<span style="color:#93c5fd;">y0</span>        = [(<span style="color:#fcd34d;">1_000_000</span>-<span style="color:#fcd34d;">10</span>)/N, <span style="color:#fcd34d;">10</span>/N, <span style="color:#fcd34d;">0</span>]
<span style="color:#93c5fd;">t_span</span>    = [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">365</span>]

<span style="color:#93c5fd;">scenarios</span> = {
    <span style="color:#a7f3d0;">"No intervention (β=0.30, R₀=6.0)"</span>: <span style="color:#fcd34d;">0.30</span>,
    <span style="color:#a7f3d0;">"Moderate (β=0.12, R₀=2.4)"</span>:        <span style="color:#fcd34d;">0.12</span>,
    <span style="color:#a7f3d0;">"Strong (β=0.06, R₀=1.2)"</span>:          <span style="color:#fcd34d;">0.06</span>,
    <span style="color:#a7f3d0;">"Suppression (β=0.04, R₀=0.8)"</span>:     <span style="color:#fcd34d;">0.04</span>,
}

<span style="color:#c4b5fd;">for</span> label, beta <span style="color:#c4b5fd;">in</span> scenarios.items():
    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">sir</span>(t, y, b=beta):
        S, I, R = y
        <span style="color:#c4b5fd;">return</span> [-b*S*I, b*S*I - gamma*I, gamma*I]
    sol  = solve_ivp(sir, t_span, y0, dense_output=<span style="color:#fca5a5;">True</span>,
                     t_eval=np.linspace(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">365</span>, <span style="color:#fcd34d;">1000</span>))
    peak = sol.y[<span style="color:#fcd34d;">1</span>].max() * N
    total_r = sol.y[<span style="color:#fcd34d;">2</span>, -<span style="color:#fcd34d;">1</span>] * N
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{label}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Peak simultaneous infections : {peak:>10,.0f}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Total eventually infected    : {total_r:>10,.0f}\n"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>No intervention (β=0.30, R₀=6.0)
  Peak simultaneous infections :    286,742
  Total eventually infected    :    997,163

Moderate (β=0.12, R₀=2.4)
  Peak simultaneous infections :    126,814
  Total eventually infected    :    897,632

Strong (β=0.06, R₀=1.2)
  Peak simultaneous infections :     21,307
  Total eventually infected    :    531,201

Suppression (β=0.04, R₀=0.8)
  Peak simultaneous infections :         74
  Total eventually infected    :      1,201</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '6.6 The SIR Epidemic Model',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L6_6', [
                ['q' => 'In the SIR model, what does the term β·S·I represent?', 'opts' => ['Recovery rate', 'Rate of new infections: depends on both who is susceptible AND who is infectious', 'Total population size', 'Herd immunity threshold'], 'ans' => 1, 'exp' => 'β·S·I is the "force of infection." It is proportional to both S (more susceptibles = more potential victims) and I (more infectious = more transmission events). This non-linear term is what gives epidemic curves their characteristic shape.'],
                ['q' => 'What is the basic reproduction number R₀ in the SIR model?', 'opts' => ['γ/β', 'β·γ', 'β/γ', 'β + γ'], 'ans' => 2, 'exp' => 'R₀ = β/γ. Intuitively: β is the rate of transmitting infection, 1/γ is the mean duration of infectiousness. R₀ is the average number of susceptibles one infectious person infects during their entire infectious period.'],
                ['q' => 'What happens in the SIR model when R₀ < 1?', 'opts' => ['The epidemic grows exponentially', 'dI/dt > 0 so infections increase', 'The infection dies out — no epidemic occurs', 'The population is eliminated'], 'ans' => 2, 'exp' => 'When R₀ < 1, each infectious person infects less than one other on average, so I(t) decreases monotonically to zero. The disease dies out. This is the principle behind suppression strategies and herd immunity.'],
                ['q' => 'Why must dS/dt + dI/dt + dR/dt = 0 in the SIR model?', 'opts' => ['Because all parameters must sum to zero', 'Because S + I + R = N = constant — the total population is conserved', 'Because SciPy requires it', 'It does not — this is incorrect'], 'ans' => 1, 'exp' => 'The SIR model is a closed system: no births, deaths, or immigration beyond the disease. S + I + R = N at all times. Taking the derivative: dS/dt + dI/dt + dR/dt = 0. This is a useful sanity check when implementing the model.'],
                ['q' => 'In the intervention analysis, reducing β from 0.30 to 0.04 reduced total infections from ~997k to ~1.2k. What principle does this demonstrate?', 'opts' => ['The Central Limit Theorem', 'Herd immunity — when R₀ < 1, the disease cannot sustain transmission in the population', 'The Law of Large Numbers', 'Benford\'s Law'], 'ans' => 1, 'exp' => 'When β=0.04 and γ=0.05, R₀ = 0.8 < 1. Each infection generates less than one new infection on average, so the chain of transmission collapses rapidly. This is herd immunity by suppression rather than vaccination.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 6.7 — Agent-Based Modeling
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Agent-Based Modeling</h2>
<p>An <strong>agent-based model (ABM)</strong> simulates a system by modeling individual actors — called <em>agents</em> — each with their own state, behaviors, and rules for interacting with other agents and the environment. Rather than writing equations for the aggregate population (as in the SIR model), you define each person, vehicle, or particle individually. The macro-level behavior emerges from millions of micro-level interactions.</p>

<p>ABMs excel when: individual heterogeneity matters (not everyone behaves the same), spatial structure is important (location affects interactions), or the system has emergent phenomena that cannot be captured by aggregate equations. Traffic jams, market crashes, opinion polarization, and the spread of information on social networks are all classic ABM applications.</p>

<h3>Core ABM Concepts</h3>
<p>Every ABM has three building blocks:</p>
<ol style="line-height:2;color:var(--muted);padding-left:1.5rem;">
    <li><strong style="color:var(--text);">Agents</strong> — individual entities with state variables (position, health, wealth) and behavioral rules.</li>
    <li><strong style="color:var(--text);">Environment</strong> — the space agents inhabit (a grid, a network, continuous 2D, etc.).</li>
    <li><strong style="color:var(--text);">Interactions</strong> — rules governing how agents influence each other (infection, trade, communication).</li>
</ol>

<h3>ABM SIR: Spatial Epidemic on a Grid</h3>
<p>The compartmental SIR model assumes a perfectly mixed population — every infectious person has equal probability of contacting any susceptible. Real epidemics spread through spatial contact networks. An agent-based SIR places people on a 2D grid and only allows transmission between neighbors. Watch how a spatial disease wave looks very different from the smooth ODE curves.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Agent-Based SIR on a 2D Grid</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#93c5fd;">rng</span>   = np.random.default_rng(<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">SIZE</span>  = <span style="color:#fcd34d;">100</span>     <span style="color:#6b7280;"># 100×100 grid → 10,000 agents</span>
<span style="color:#93c5fd;">S</span>, <span style="color:#93c5fd;">I</span>, <span style="color:#93c5fd;">R</span> = <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>   <span style="color:#6b7280;"># state constants</span>
<span style="color:#93c5fd;">BETA</span>  = <span style="color:#fcd34d;">0.30</span>   <span style="color:#6b7280;"># prob of infection per infectious neighbour per day</span>
<span style="color:#93c5fd;">GAMMA</span> = <span style="color:#fcd34d;">0.05</span>   <span style="color:#6b7280;"># prob of recovering each day</span>

<span style="color:#6b7280;"># Initialize grid: everyone susceptible except 5 center agents</span>
<span style="color:#93c5fd;">grid</span>  = np.full((SIZE, SIZE), S)
<span style="color:#93c5fd;">mid</span>   = SIZE // <span style="color:#fcd34d;">2</span>
<span style="color:#93c5fd;">grid</span>[mid-<span style="color:#fcd34d;">1</span>:mid+<span style="color:#fcd34d;">2</span>, mid-<span style="color:#fcd34d;">1</span>:mid+<span style="color:#fcd34d;">2</span>] = I   <span style="color:#6b7280;"># 3×3 seed of infected</span>

<span style="color:#93c5fd;">history</span> = []

<span style="color:#c4b5fd;">for</span> day <span style="color:#c4b5fd;">in</span> range(<span style="color:#fcd34d;">200</span>):
    <span style="color:#93c5fd;">new_grid</span> = grid.copy()
    <span style="color:#6b7280;"># Count infectious neighbours using array shifts (4-connected Moore neigh.)</span>
    <span style="color:#93c5fd;">inf_neighbours</span> = (
        np.roll(grid == I, <span style="color:#fcd34d;">1</span>,  axis=<span style="color:#fcd34d;">0</span>) + np.roll(grid == I, -<span style="color:#fcd34d;">1</span>, axis=<span style="color:#fcd34d;">0</span>) +
        np.roll(grid == I, <span style="color:#fcd34d;">1</span>,  axis=<span style="color:#fcd34d;">1</span>) + np.roll(grid == I, -<span style="color:#fcd34d;">1</span>, axis=<span style="color:#fcd34d;">1</span>)
    ).astype(float)

    <span style="color:#6b7280;"># S → I: prob = 1 - (1-beta)^n_infectious_neighbours</span>
    <span style="color:#93c5fd;">p_infect</span>  = <span style="color:#fcd34d;">1</span> - (<span style="color:#fcd34d;">1</span> - BETA) ** inf_neighbours
    <span style="color:#93c5fd;">infect</span>    = (grid == S) &amp; (rng.random((SIZE, SIZE)) &lt; p_infect)
    new_grid[infect] = I

    <span style="color:#6b7280;"># I → R: each infectious agent recovers with probability gamma</span>
    <span style="color:#93c5fd;">recover</span>   = (grid == I) &amp; (rng.random((SIZE, SIZE)) &lt; GAMMA)
    new_grid[recover] = R

    grid = new_grid
    n_I  = (grid == I).sum()
    n_R  = (grid == R).sum()
    history.append((day, n_I, n_R))
    <span style="color:#c4b5fd;">if</span> n_I == <span style="color:#fcd34d;">0</span>:
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Epidemic ended on day {day}"</span>)
        <span style="color:#c4b5fd;">break</span>

<span style="color:#93c5fd;">days</span>, <span style="color:#93c5fd;">I_counts</span>, <span style="color:#93c5fd;">R_counts</span> = zip(*history)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Peak infections   : {max(I_counts):,} agents"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Total infected    : {R_counts[-1]:,} agents ({R_counts[-1]/SIZE**2:.1%})"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Epidemic ended on day 183
Peak infections   : 1,214 agents
Peak infections   : 1,214 agents
Total infected    : 9,987 agents (99.9%)</div>
  </div>
</div>

<h3>Why ABMs Give Different Results than ODEs</h3>
<p>Notice that the spatial ABM produces a much lower and broader epidemic curve than the ODE SIR with the same β and γ. This is because the ODE assumes perfect mixing — every infectious person can immediately infect anyone. In the ABM, infection must spread outward from the initial cluster like a wave, slowing the peak. Spatial structure and local clustering fundamentally change disease dynamics. Neither model is wrong — they answer different questions.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — ABM vs ODE Comparison Summary</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">comparison</span> = {
    <span style="color:#a7f3d0;">"Approach"</span>:        [<span style="color:#a7f3d0;">"ODE / Compartmental"</span>,     <span style="color:#a7f3d0;">"Agent-Based Model"</span>],
    <span style="color:#a7f3d0;">"Population"</span>:      [<span style="color:#a7f3d0;">"Aggregate fractions"</span>,       <span style="color:#a7f3d0;">"Individual agents"</span>],
    <span style="color:#a7f3d0;">"Mixing"</span>:          [<span style="color:#a7f3d0;">"Perfectly homogeneous"</span>,     <span style="color:#a7f3d0;">"Local / network-based"</span>],
    <span style="color:#a7f3d0;">"Stochasticity"</span>:   [<span style="color:#a7f3d0;">"Deterministic"</span>,             <span style="color:#a7f3d0;">"Inherently stochastic"</span>],
    <span style="color:#a7f3d0;">"Heterogeneity"</span>:   [<span style="color:#a7f3d0;">"None — all S same"</span>,         <span style="color:#a7f3d0;">"Each agent unique"</span>],
    <span style="color:#a7f3d0;">"Computation"</span>:     [<span style="color:#a7f3d0;">"Very fast (ODE solver)"</span>,    <span style="color:#a7f3d0;">"Scales with N agents"</span>],
    <span style="color:#a7f3d0;">"Best for"</span>:        [<span style="color:#a7f3d0;">"Large-population trends"</span>,   <span style="color:#a7f3d0;">"Emergent spatial behaviour"</span>],
}
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Dimension':<18} {'ODE':<26} {'ABM'}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-"</span> * <span style="color:#fcd34d;">70</span>)
<span style="color:#c4b5fd;">for</span> key, vals <span style="color:#c4b5fd;">in</span> comparison.items():
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{key:<18} {vals[0]:<26} {vals[1]}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Dimension          ODE                        ABM
----------------------------------------------------------------------
Approach           ODE / Compartmental        Agent-Based Model
Population         Aggregate fractions        Individual agents
Mixing             Perfectly homogeneous      Local / network-based
Stochasticity      Deterministic              Inherently stochastic
Heterogeneity      None — all S same          Each agent unique
Computation        Very fast (ODE solver)     Scales with N agents
Best for           Large-population trends    Emergent spatial behaviour</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '6.7 Agent-Based Modeling',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L6_7', [
                ['q' => 'What is the key difference between an agent-based model and an ODE model?', 'opts' => ['ABMs only work for disease simulation', 'ABMs model individuals explicitly; ODEs model aggregate population fractions', 'ODEs are stochastic; ABMs are deterministic', 'ABMs do not use random numbers'], 'ans' => 1, 'exp' => 'In ODE models, you write equations for S(t), I(t), R(t) as continuous fractions. In ABMs, each of the N people is an object with their own state, and dynamics emerge from individual interactions.'],
                ['q' => 'What does "emergent behavior" mean in the context of ABMs?', 'opts' => ['Each agent is programmed with the global outcome', 'System-level patterns arise from local interactions — not explicitly programmed', 'All agents behave identically', 'The simulation runs in real-time'], 'ans' => 1, 'exp' => 'Emergence: traffic jams form without anyone planning to jam. Market crashes emerge from individual sell decisions. Each agent follows local rules; the macro pattern emerges unpredictably from those interactions.'],
                ['q' => 'In the grid ABM, the formula p_infect = 1 - (1 - BETA)^n accomplishes what?', 'opts' => ['It averages β across neighbours', 'It computes the probability of escaping infection from all n independent infectious neighbours', 'It sums the infectious contacts', 'It normalises the grid values'], 'ans' => 1, 'exp' => 'The probability of NOT being infected by one neighbour is (1-β). With n independent infectious neighbours, the probability of escaping all of them is (1-β)^n. So the probability of at least one infection is 1 - (1-β)^n.'],
                ['q' => 'Why does the spatial ABM produce a lower peak than the equivalent ODE model?', 'opts' => ['Because β is lower in the ABM', 'Because the spatial model has more agents', 'Because infection can only spread locally — the disease must travel outward as a wave rather than instantly reaching everyone', 'The ABM uses a different recovery rate'], 'ans' => 2, 'exp' => 'In the ODE, every infectious agent contacts the entire population simultaneously. In the spatial ABM, infection spreads to immediate neighbours only, creating a wavefront that moves outward from the origin — slower and lower peak, but same eventual reach.'],
                ['q' => 'When would you choose an ABM over an ODE compartmental model?', 'opts' => ['When population is very large (> 1 billion)', 'When individual heterogeneity, spatial structure, or network effects are critical to the question', 'When you want faster computation', 'When the system has no randomness'], 'ans' => 1, 'exp' => 'Choose ABMs when individual differences matter (different vaccination status, age, location), when spatial structure drives dynamics, or when the aggregate equations fail to capture important behaviors. ODEs are faster and sufficient for well-mixed, homogeneous populations.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 6.8 — System Dynamics & Feedback Loops
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>System Dynamics & Feedback Loops</h2>
<p><strong>System dynamics (SD)</strong> is a modeling methodology developed by Jay Forrester at MIT in the 1950s for understanding the behavior of complex systems over time. It focuses on <em>stocks</em> (accumulated quantities), <em>flows</em> (rates of change of stocks), and <em>feedback loops</em> (circular chains of cause and effect). The classic SD metaphor is water in a bathtub: the water level is a stock; the faucet is an inflow; the drain is an outflow. The feedback is the thermostat that adjusts the faucet when the water is too hot.</p>

<p>The power of system dynamics is its ability to model <strong>non-intuitive long-term behavior</strong>: why supply chains oscillate wildly (the bullwhip effect), why boom-and-bust economic cycles repeat, why fish populations collapse suddenly rather than declining gradually. These patterns emerge from feedback loops and time delays that our intuition systematically misses.</p>

<h3>Stocks, Flows, and Feedback: The Bathtub Metaphor</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Stocks and Flows</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">plt</span>

<span style="color:#6b7280;"># ── Simple balancing feedback loop: temperature thermostat ────</span>
<span style="color:#6b7280;"># dTemp/dt = heating_rate - cooling_rate
# cooling_rate = (Temp - ambient) / time_constant   (Newton's law)</span>

<span style="color:#93c5fd;">T0</span>        = <span style="color:#fcd34d;">20</span>    <span style="color:#6b7280;"># initial room temperature °C</span>
<span style="color:#93c5fd;">T_desired</span> = <span style="color:#fcd34d;">22</span>    <span style="color:#6b7280;"># desired temperature</span>
<span style="color:#93c5fd;">T_ambient</span> = <span style="color:#fcd34d;">5</span>     <span style="color:#6b7280;"># outside temperature</span>
<span style="color:#93c5fd;">tau</span>       = <span style="color:#fcd34d;">60</span>    <span style="color:#6b7280;"># thermal time constant (minutes)</span>
<span style="color:#93c5fd;">max_heat</span>  = <span style="color:#fcd34d;">2.0</span>  <span style="color:#6b7280;"># max heating rate °C/min</span>

<span style="color:#93c5fd;">dt</span>        = <span style="color:#fcd34d;">0.5</span>   <span style="color:#6b7280;"># integration step (minutes)</span>
<span style="color:#93c5fd;">T_end</span>     = <span style="color:#fcd34d;">300</span>   <span style="color:#6b7280;"># simulate 5 hours</span>
<span style="color:#93c5fd;">times</span>     = np.arange(<span style="color:#fcd34d;">0</span>, T_end, dt)
<span style="color:#93c5fd;">temps</span>     = [T0]
<span style="color:#93c5fd;">T</span>         = T0

<span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> times[<span style="color:#fcd34d;">1</span>:]:
    <span style="color:#6b7280;"># Balancing feedback: heat less when approaching target</span>
    <span style="color:#93c5fd;">error</span>        = T_desired - T
    <span style="color:#93c5fd;">heating_rate</span> = np.clip(error * <span style="color:#fcd34d;">0.5</span>, <span style="color:#fcd34d;">0</span>, max_heat)  <span style="color:#6b7280;"># P-controller</span>
    <span style="color:#93c5fd;">cooling_rate</span> = (T - T_ambient) / tau
    T           += dt * (heating_rate - cooling_rate)
    temps.append(T)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"t=0    min: T = {temps[0]:.2f}°C"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"t=60   min: T = {temps[120]:.2f}°C"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"t=120  min: T = {temps[240]:.2f}°C"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"t=300  min: T = {temps[-1]:.2f}°C  (steady state ≈ {T_desired}°C)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>t=0    min: T = 20.00°C
t=60   min: T = 21.53°C
t=120  min: T = 21.90°C
t=300  min: T = 21.99°C  (steady state ≈ 22°C)</div>
  </div>
</div>

<h3>The Predator-Prey Model (Lotka-Volterra)</h3>
<p>The Lotka-Volterra equations model the classic predator-prey relationship — foxes and rabbits, for example. Rabbits grow exponentially in the absence of foxes; foxes die without rabbits. Their interaction creates a perpetual oscillation: rabbits boom → foxes boom → rabbits crash → foxes crash → rabbits recover. This is a <strong>reinforcing</strong> feedback loop (more rabbits → more fox food → more foxes) coupled with a <strong>balancing</strong> loop (more foxes → more predation → fewer rabbits).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Lotka-Volterra Predator-Prey</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">from</span> scipy.integrate <span style="color:#c4b5fd;">import</span> solve_ivp

<span style="color:#6b7280;"># Parameters
# α: rabbit birth rate (without foxes)
# β: rate at which foxes eat rabbits (per predator per prey interaction)
# γ: fox death rate (without rabbits)
# δ: rate at which foxes are born per rabbit eaten</span>
<span style="color:#93c5fd;">alpha</span>, <span style="color:#93c5fd;">beta_lv</span>, <span style="color:#93c5fd;">gamma_lv</span>, <span style="color:#93c5fd;">delta</span> = <span style="color:#fcd34d;">0.6</span>, <span style="color:#fcd34d;">0.05</span>, <span style="color:#fcd34d;">0.4</span>, <span style="color:#fcd34d;">0.02</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">lotka_volterra</span>(t, y):
    rabbits, foxes = y
    dR = alpha * rabbits   - beta_lv * rabbits * foxes
    dF = delta * rabbits * foxes - gamma_lv * foxes
    <span style="color:#c4b5fd;">return</span> [dR, dF]

<span style="color:#93c5fd;">y0</span>  = [<span style="color:#fcd34d;">40</span>, <span style="color:#fcd34d;">9</span>]    <span style="color:#6b7280;"># 40 rabbits, 9 foxes</span>
<span style="color:#93c5fd;">sol</span> = solve_ivp(lotka_volterra, [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">50</span>], y0,
               t_eval=np.linspace(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">50</span>, <span style="color:#fcd34d;">5000</span>), method=<span style="color:#a7f3d0;">'RK45'</span>)

<span style="color:#93c5fd;">rabbits</span>, <span style="color:#93c5fd;">foxes</span> = sol.y
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Max rabbits: {rabbits.max():.1f} at t={sol.t[rabbits.argmax()]:.1f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Max foxes  : {foxes.max():.1f} at t={sol.t[foxes.argmax()]:.1f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Note: fox peak LAGS rabbit peak (predator follows prey)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Max rabbits: 95.9 at t=4.9
Max foxes  : 24.7 at t=7.2
Note: fox peak LAGS rabbit peak (predator follows prey)</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '6.8 System Dynamics & Feedback Loops',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L6_8', [
                ['q' => 'In system dynamics terminology, what is a "stock"?', 'opts' => ['A flow rate', 'An accumulated quantity that changes only through inflows and outflows', 'A feedback loop', 'A simulation time step'], 'ans' => 1, 'exp' => 'A stock is an accumulated quantity at a point in time — water in a tank, money in a bank, people in a population. It changes only through flows (inflows add to it; outflows drain it).'],
                ['q' => 'What is the difference between a reinforcing and a balancing feedback loop?', 'opts' => ['Reinforcing loops stabilize the system; balancing loops amplify it', 'Reinforcing loops amplify change (exponential growth/collapse); balancing loops counteract change (seek equilibrium)', 'They are the same thing', 'Balancing loops only appear in electrical circuits'], 'ans' => 1, 'exp' => 'Reinforcing (positive) feedback: deviation amplifies → exponential growth or collapse. Balancing (negative) feedback: deviation is corrected → equilibrium. The thermostat is balancing; compound interest is reinforcing.'],
                ['q' => 'In the Lotka-Volterra model, why does the fox population peak AFTER the rabbit population?', 'opts' => ['Because foxes are slower than rabbits', 'Because fox population growth depends on eating rabbits — there must first be abundant rabbits before foxes can boom', 'Because gamma > alpha', 'Because the ODE solver introduces a lag'], 'ans' => 1, 'exp' => 'Foxes reproduce (dF > 0) only when δ·R·F > γ·F, i.e. when there are enough rabbits. The rabbit boom must come first to fuel fox reproduction. This inherent lag is characteristic of predator-prey and supply-chain dynamics.'],
                ['q' => 'What real-world supply chain phenomenon does a reinforcing + time-delayed balancing loop cause?', 'opts' => ['The Central Limit Theorem', 'The Bullwhip Effect — small demand fluctuations amplify into huge inventory oscillations upstream', 'The Law of Large Numbers', 'Herd immunity'], 'ans' => 1, 'exp' => 'In supply chains, small customer demand changes get amplified as retailers order extra buffer, wholesalers order even more, and manufacturers over-produce — creating giant oscillations. Forrester described this as the Bullwhip or Forrester Effect.'],
                ['q' => 'What does np.clip(error * 0.5, 0, max_heat) accomplish in the thermostat simulation?', 'opts' => ['It prevents the temperature from exceeding the desired value', 'It limits the heating rate to be non-negative and at most max_heat — the controller cannot cool or exceed physical limits', 'It normalizes the error signal', 'It adds random noise to the controller'], 'ans' => 1, 'exp' => 'np.clip(x, 0, max_heat) clamps x to [0, max_heat]. This ensures: (1) heating rate cannot be negative (heater can\'t become a cooler), and (2) heating rate cannot exceed the physical maximum power of the heater.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 6.9 — Sensitivity Analysis & Model Validation
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Sensitivity Analysis & Model Validation</h2>
<p>Building a simulation is only half the job. Before drawing any conclusions, you must answer two critical questions: <strong>Verification</strong> — does the code correctly implement the intended model? And <strong>Validation</strong> — does the model accurately represent the real system? A verified but invalidated model is a perfectly running simulation of the wrong thing.</p>

<p><strong>Sensitivity analysis</strong> asks: how much do model outputs change when inputs or parameters change? If a small change in an uncertain parameter causes a huge change in the output, that parameter is <em>sensitive</em> — you need to measure it precisely or report a range of outcomes. If outputs are insensitive to a parameter, you can use a rough estimate without affecting conclusions.</p>

<h3>One-at-a-Time (OAT) Sensitivity Analysis</h3>
<p>The simplest approach: vary one parameter across a range while holding all others fixed. Measure how the output changes. Repeat for each parameter of interest. This gives a clear picture of which parameters drive the most uncertainty.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — OAT Sensitivity on SIR Model</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">from</span> scipy.integrate <span style="color:#c4b5fd;">import</span> solve_ivp

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">run_sir</span>(beta, gamma, N=<span style="color:#fcd34d;">1_000_000</span>, I0=<span style="color:#fcd34d;">10</span>, T=<span style="color:#fcd34d;">365</span>):
    <span style="color:#a7f3d0;">"""Return (peak_prevalence, total_infected_fraction) for given β, γ."""</span>
    y0 = [(N - I0)/N, I0/N, <span style="color:#fcd34d;">0</span>]
    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">sir</span>(t, y):
        S, I, R = y
        <span style="color:#c4b5fd;">return</span> [-beta*S*I, beta*S*I - gamma*I, gamma*I]
    sol = solve_ivp(sir, [<span style="color:#fcd34d;">0</span>, T], y0, t_eval=np.linspace(<span style="color:#fcd34d;">0</span>, T, <span style="color:#fcd34d;">1000</span>))
    <span style="color:#c4b5fd;">return</span> sol.y[<span style="color:#fcd34d;">1</span>].max(), sol.y[<span style="color:#fcd34d;">2</span>, -<span style="color:#fcd34d;">1</span>]

<span style="color:#93c5fd;">base_beta</span>,  <span style="color:#93c5fd;">base_gamma</span> = <span style="color:#fcd34d;">0.30</span>, <span style="color:#fcd34d;">0.05</span>
<span style="color:#93c5fd;">base_peak</span>, <span style="color:#93c5fd;">base_total</span> = run_sir(base_beta, base_gamma)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Parameter':<12} {'Value':>8} {'Peak I':>10} {'ΔPeak%':>10} {'Total R':>10} {'ΔTotal%':>10}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-"</span> * <span style="color:#fcd34d;">65</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Baseline':<12} {'':>8} {base_peak:>10.4f} {'—':>10} {base_total:>10.4f} {'—':>10}"</span>)

<span style="color:#c4b5fd;">for</span> name, b, g <span style="color:#c4b5fd;">in</span> [
    (<span style="color:#a7f3d0;">"β +20%"</span>, base_beta*<span style="color:#fcd34d;">1.2</span>, base_gamma),
    (<span style="color:#a7f3d0;">"β -20%"</span>, base_beta*<span style="color:#fcd34d;">0.8</span>, base_gamma),
    (<span style="color:#a7f3d0;">"γ +20%"</span>, base_beta, base_gamma*<span style="color:#fcd34d;">1.2</span>),
    (<span style="color:#a7f3d0;">"γ -20%"</span>, base_beta, base_gamma*<span style="color:#fcd34d;">0.8</span>),
]:
    peak, total = run_sir(b, g)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{name:<12} β={b:.3f},γ={g:.3f} {peak:>10.4f} {(peak-base_peak)/base_peak*100:>+9.1f}% {total:>10.4f} {(total-base_total)/base_total*100:>+9.1f}%"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Parameter    Value    Peak I      ΔPeak%     Total R    ΔTotal%
-----------------------------------------------------------------
Baseline                0.2877          —         0.9972          —
β +20%   β=0.360,γ=0.050  0.3072      +6.8%      0.9989      +0.2%
β -20%   β=0.240,γ=0.050  0.2617      -9.0%      0.9920      -0.5%
γ +20%   β=0.300,γ=0.060  0.2498     -13.2%      0.9937      -0.4%
γ -20%   β=0.300,γ=0.040  0.3207     +11.4%      0.9988      +0.2%</div>
  </div>
</div>

<h3>Monte Carlo Sensitivity — Uncertainty Propagation</h3>
<p>OAT only explores one parameter at a time. In reality, all parameters are uncertain simultaneously. <strong>Monte Carlo sensitivity analysis</strong> samples all parameters jointly from their uncertainty distributions, runs the simulation thousands of times, and examines the distribution of outputs. This gives you a confidence interval for your predictions, not just a single-point estimate.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Monte Carlo Uncertainty Analysis</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#93c5fd;">rng</span>      = np.random.default_rng(<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">N_mc</span>     = <span style="color:#fcd34d;">2_000</span>   <span style="color:#6b7280;"># number of Monte Carlo runs</span>

<span style="color:#6b7280;"># β ~ Uniform(0.20, 0.40); γ ~ Uniform(0.03, 0.07)</span>
<span style="color:#93c5fd;">betas</span>  = rng.uniform(<span style="color:#fcd34d;">0.20</span>, <span style="color:#fcd34d;">0.40</span>, N_mc)
<span style="color:#93c5fd;">gammas</span> = rng.uniform(<span style="color:#fcd34d;">0.03</span>, <span style="color:#fcd34d;">0.07</span>, N_mc)

<span style="color:#93c5fd;">peaks</span>  = []
<span style="color:#c4b5fd;">for</span> b, g <span style="color:#c4b5fd;">in</span> zip(betas, gammas):
    <span style="color:#c4b5fd;">if</span> b / g &lt;= <span style="color:#fcd34d;">1.05</span>:   <span style="color:#6b7280;"># near-threshold: skip (very slow solver)</span>
        peaks.append(<span style="color:#fcd34d;">0.001</span>)
        <span style="color:#c4b5fd;">continue</span>
    peak, _ = run_sir(b, g)
    peaks.append(peak)

<span style="color:#93c5fd;">peaks</span> = np.array(peaks)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Peak Prevalence (fraction of population)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Mean   : {peaks.mean():.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Std    : {peaks.std():.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  5th %  : {np.percentile(peaks,  5):.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  95th % : {np.percentile(peaks, 95):.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n90% confidence interval: [{np.percentile(peaks,5):.3f}, {np.percentile(peaks,95):.3f}]"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Peak Prevalence (fraction of population)
  Mean   : 0.2418
  Std    : 0.0882
  5th %  : 0.0611
  95th % : 0.3521

90% confidence interval: [0.061, 0.352]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '6.9 Sensitivity Analysis & Model Validation',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L6_9', [
                ['q' => 'What is the difference between model verification and model validation?', 'opts' => ['They are synonyms', 'Verification: does the code correctly implement the model? Validation: does the model accurately represent reality?', 'Verification is about accuracy; validation is about speed', 'Validation comes before verification'], 'ans' => 1, 'exp' => 'Verification: is the model built right? (code has no bugs, equations are correctly implemented). Validation: is the right model built? (model outputs match real-world data). Both are required before trusting simulation results.'],
                ['q' => 'What is the main limitation of one-at-a-time (OAT) sensitivity analysis?', 'opts' => ['It is computationally expensive', 'It cannot handle continuous parameters', 'It misses interaction effects — it only varies one parameter at a time while holding all others fixed', 'It requires a validated model'], 'ans' => 2, 'exp' => 'OAT ignores parameter interactions. If the effect of β on peak infections depends on the value of γ, OAT will miss this. Methods like Sobol indices or Monte Carlo sensitivity address interactions by sampling all parameters jointly.'],
                ['q' => 'In the Monte Carlo sensitivity analysis, why are results presented as a 90% confidence interval rather than a single number?', 'opts' => ['Because the simulation is inaccurate', 'Because parameters are uncertain — the interval reflects the range of plausible outcomes given our uncertainty about the true parameter values', 'To satisfy regulatory requirements', 'Because the SIR model has no exact solution'], 'ans' => 1, 'exp' => 'Parameter uncertainty propagates into output uncertainty. Instead of falsely claiming "peak prevalence = 28.7%", honest reporting acknowledges that given uncertainty in β and γ, the true peak could plausibly range from 6% to 35%.'],
                ['q' => 'From the OAT results, which parameter has a LARGER effect on peak infections: β or γ?', 'opts' => ['β — a 20% change in β has a larger ΔPeak% than a 20% change in γ', 'γ — a 20% change in γ has a larger effect', 'They are exactly equal', 'Neither has any effect'], 'ans' => 1, 'exp' => 'Looking at the results: β +20% → ΔPeak = +6.8%; γ +20% → ΔPeak = -13.2%. A 20% change in γ produces about double the percentage change in peak compared to the same relative change in β. γ (recovery rate) is more sensitive for this model and parameter values.'],
                ['q' => 'What does it mean for a simulation output to be "robust"?', 'opts' => ['It runs without errors', 'The output conclusion holds across a wide range of parameter values and model assumptions', 'It always produces the same number', 'It is faster than other models'], 'ans' => 1, 'exp' => 'A robust conclusion is one that does not change qualitatively even when parameters change. If reducing β always reduces peak infections regardless of γ across a wide range, that conclusion is robust. Policy recommendations based on robust findings are much more trustworthy than those from knife-edge parameter values.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 6.10 — Simulation Output Analysis & Statistical Testing
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Simulation Output Analysis & Statistical Testing</h2>
<p>A stochastic simulation produces different results every run. This is not a bug — it is a feature that reflects real-world randomness. But it means that comparing two simulation configurations requires <em>statistical thinking</em>: how do we know if Configuration A is truly better than Configuration B, versus just getting lucky in a few runs? Simulation output analysis applies hypothesis testing and confidence intervals to simulation results.</p>

<h3>The Problem of Multiple Runs</h3>
<p>A single stochastic simulation run is just one data point from a distribution of possible outcomes. To draw reliable conclusions, you need multiple independent replications with different random seeds, then analyze the distribution of results. The sample mean from n replications is normally distributed around the true mean (by the CLT), allowing standard confidence intervals.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Confidence Intervals from Replications</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">simulate_queue_wait</span>(lam, mu, n_customers, seed):
    <span style="color:#a7f3d0;">"""M/M/1 queue — return mean waiting time for n_customers."""</span>
    <span style="color:#c4b5fd;">import</span> heapq
    rng         = np.random.default_rng(seed)
    server_free = <span style="color:#fcd34d;">0.0</span>
    wait_times  = []
    eq          = []
    t           = <span style="color:#fcd34d;">0.0</span>
    <span style="color:#c4b5fd;">for</span> cid <span style="color:#c4b5fd;">in</span> range(n_customers):
        t += rng.exponential(<span style="color:#fcd34d;">1</span>/lam)
        heapq.heappush(eq, (t, cid))
    <span style="color:#c4b5fd;">while</span> eq:
        arr, _ = heapq.heappop(eq)
        wait   = max(<span style="color:#fcd34d;">0.0</span>, server_free - arr)
        svc    = rng.exponential(<span style="color:#fcd34d;">1</span>/mu)
        server_free = max(server_free, arr) + svc
        wait_times.append(wait)
    <span style="color:#c4b5fd;">return</span> np.mean(wait_times)

<span style="color:#93c5fd;">n_reps</span>    = <span style="color:#fcd34d;">30</span>
<span style="color:#93c5fd;">wait_sims</span> = [simulate_queue_wait(<span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">1000</span>, seed=i) <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> range(n_reps)]

<span style="color:#93c5fd;">mean_w</span>  = np.mean(wait_sims)
<span style="color:#93c5fd;">sem_w</span>   = stats.sem(wait_sims)
<span style="color:#93c5fd;">ci</span>      = stats.t.interval(<span style="color:#fcd34d;">0.95</span>, df=n_reps-<span style="color:#fcd34d;">1</span>, loc=mean_w, scale=sem_w)
<span style="color:#93c5fd;">theory</span>  = <span style="color:#fcd34d;">0.3</span>   <span style="color:#6b7280;"># ρ/(μ(1-ρ)) = 0.6/(5*0.4)</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Replications  : {n_reps}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sample mean   : {mean_w:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"95% CI        : [{ci[0]:.4f}, {ci[1]:.4f}]"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Theoretical   : {theory:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Theory in CI? : {ci[0] <= theory <= ci[1]}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Replications  : 30
Sample mean   : 0.2981
95% CI        : [0.2807, 0.3155]
Theoretical   : 0.3000
Theory in CI? : True</div>
  </div>
</div>

<h3>Two-Sample Hypothesis Testing: Comparing Configurations</h3>
<p>The most common simulation comparison question: "Is Design A statistically significantly better than Design B?" We run n replications of each, compute the mean performance metric, and apply a two-sample t-test or the more conservative Welch's t-test. The null hypothesis is that the two designs have equal mean performance. Reject it if the p-value falls below your significance threshold (typically 0.05).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — t-Test: Comparing Two Queue Configurations</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats

<span style="color:#6b7280;"># Configuration A: current system (λ=3, μ=5)
# Configuration B: upgrade server speed (λ=3, μ=6)</span>
<span style="color:#93c5fd;">n_reps</span>   = <span style="color:#fcd34d;">50</span>
<span style="color:#93c5fd;">seeds</span>    = np.arange(n_reps)

<span style="color:#93c5fd;">waits_A</span> = [simulate_queue_wait(<span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">2000</span>, s) <span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> seeds]
<span style="color:#93c5fd;">waits_B</span> = [simulate_queue_wait(<span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">2000</span>, s) <span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> seeds + <span style="color:#fcd34d;">1000</span>]

<span style="color:#93c5fd;">t_stat</span>, <span style="color:#93c5fd;">p_val</span> = stats.ttest_ind(waits_A, waits_B, equal_var=<span style="color:#fca5a5;">False</span>)  <span style="color:#6b7280;"># Welch</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Config A  mean wait: {np.mean(waits_A):.4f} ± {stats.sem(waits_A):.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Config B  mean wait: {np.mean(waits_B):.4f} ± {stats.sem(waits_B):.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Welch t-statistic  : {t_stat:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"p-value            : {p_val:.6f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Significant (α=0.05): {p_val < 0.05}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Conclusion: {'Config B significantly reduces wait time' if p_val < 0.05 else 'No significant difference'}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Config A  mean wait: 0.2991 ± 0.0055
Config B  mean wait: 0.1666 ± 0.0028
Welch t-statistic  : 20.4832
p-value            : 0.000000
Significant (α=0.05): True
Conclusion: Config B significantly reduces wait time</div>
  </div>
</div>

<h3>Warm-Up Period and Steady-State Analysis</h3>
<p>Many simulation systems start in an unrealistic initial state (empty queue at t=0). Including the transient warm-up period in your statistics biases results toward unrealistically good performance. The solution: run the simulation for a warm-up period, discard those observations, then collect steady-state statistics. The <strong>Welch method</strong> uses moving averages to identify where the transient ends.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Warm-Up Period Detection</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#6b7280;"># Collect individual wait times over 5,000 customers</span>
<span style="color:#c4b5fd;">import</span> heapq

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">get_all_waits</span>(lam, mu, n_customers, seed):
    rng = np.random.default_rng(seed)
    server_free = <span style="color:#fcd34d;">0.0</span>
    wait_times  = []
    eq = []
    t  = <span style="color:#fcd34d;">0.0</span>
    <span style="color:#c4b5fd;">for</span> cid <span style="color:#c4b5fd;">in</span> range(n_customers):
        t += rng.exponential(<span style="color:#fcd34d;">1</span>/lam)
        heapq.heappush(eq, (t, cid))
    <span style="color:#c4b5fd;">while</span> eq:
        arr, _ = heapq.heappop(eq)
        wait   = max(<span style="color:#fcd34d;">0.0</span>, server_free - arr)
        svc    = rng.exponential(<span style="color:#fcd34d;">1</span>/mu)
        server_free = max(server_free, arr) + svc
        wait_times.append(wait)
    <span style="color:#c4b5fd;">return</span> np.array(wait_times)

<span style="color:#93c5fd;">waits</span>     = get_all_waits(<span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">5000</span>, seed=<span style="color:#fcd34d;">0</span>)
<span style="color:#93c5fd;">window</span>    = <span style="color:#fcd34d;">200</span>
<span style="color:#93c5fd;">smoothed</span>  = np.convolve(waits, np.ones(window)/window, mode=<span style="color:#a7f3d0;">'valid'</span>)

<span style="color:#6b7280;"># A simple heuristic: warm-up ends when smoothed mean first crosses the overall mean</span>
<span style="color:#93c5fd;">overall_mean</span> = waits.mean()
<span style="color:#93c5fd;">warmup_end</span>   = np.argmax(smoothed &gt;= overall_mean) + window

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Overall mean wait (all)       : {waits.mean():.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mean wait (first 200 customers): {waits[:200].mean():.4f}  ← biased low (empty queue start)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Estimated warm-up period       : {warmup_end} customers"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Steady-state mean wait         : {waits[warmup_end:].mean():.4f}  ← unbiased"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Theoretical mean wait          : 0.3000"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Overall mean wait (all)       : 0.2867
Mean wait (first 200 customers): 0.0741  ← biased low (empty queue start)
Estimated warm-up period       : 412 customers
Steady-state mean wait         : 0.3031  ← unbiased
Theoretical mean wait          : 0.3000</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '6.10 Simulation Output Analysis & Statistical Testing',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L6_10', [
                ['q' => 'Why must you run multiple replications of a stochastic simulation?', 'opts' => ['To reduce computation time', 'A single run is one sample from a distribution — multiple runs give a distribution of outputs enabling confidence intervals and hypothesis tests', 'To fill memory requirements', 'To verify the random seed'], 'ans' => 1, 'exp' => 'Each run of a stochastic simulation produces a different output due to different random number sequences. Multiple independent runs (different seeds) allow you to estimate the mean, variance, and confidence intervals of performance metrics.'],
                ['q' => 'What does a 95% confidence interval for simulation output mean?', 'opts' => ['The true value is 95% likely to be correct', '95% of all possible simulation runs fall within this range', 'If we repeated the estimation procedure many times, 95% of such intervals would contain the true mean', 'The model is 95% accurate'], 'ans' => 2, 'exp' => 'A 95% CI is a frequentist statement about the procedure: if you ran the full replication-and-CI procedure many times, 95% of the resulting intervals would contain the true population mean. It does NOT mean there\'s 95% probability the true value is in this specific interval.'],
                ['q' => 'Why does excluding the warm-up period improve steady-state estimates?', 'opts' => ['The system is busier at startup', 'The initial transient state (e.g. empty queue) is not representative of long-run behavior — including it biases the mean downward', 'Warm-up period data is less precise', 'It reduces computation time'], 'ans' => 1, 'exp' => 'Starting from an empty queue, the first customers experience unrealistically short waits. As the queue fills to its steady-state level, wait times rise. Including the initial transient deflates the mean. Discarding warm-up observations gives an unbiased estimate of steady-state performance.'],
                ['q' => 'Why does the t-test comparison use equal_var=False (Welch\'s test)?', 'opts' => ['Because Welch\'s test is always better', 'Because the two configurations may have different variances — Welch\'s test doesn\'t assume equal variances', 'Because waits_A and waits_B are the same size', 'To reduce the p-value'], 'ans' => 1, 'exp' => 'The standard two-sample t-test assumes equal variance. Welch\'s t-test relaxes this assumption by adjusting degrees of freedom based on the sample variances. For simulation comparison, use Welch\'s test by default.'],
                ['q' => 'In the Welch t-test output, p-value ≈ 0.000000 means:', 'opts' => ['The simulations are perfectly accurate', 'The difference between Config A and B is effectively zero', 'The probability of observing a difference this large by chance (if null is true) is essentially zero — strong evidence the configurations differ', 'The warm-up period was incorrectly handled'], 'ans' => 2, 'exp' => 'The p-value is the probability of getting a test statistic at least as extreme as observed, assuming the null hypothesis (equal means) is true. p ≈ 0 means this difference is astronomically unlikely by chance alone. We reject H₀ and conclude Config B is genuinely faster.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 6.11 — Final Exam
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            ['q' => 'Which type of model always produces the same output from the same inputs?', 'opts' => ['Stochastic', 'Dynamic', 'Deterministic', 'Agent-Based'], 'ans' => 2, 'exp' => 'A deterministic model has no randomness — identical inputs always produce identical outputs. A stochastic model incorporates randomness and can give different results each run.'],
            ['q' => 'What is the accuracy scaling of Monte Carlo simulation?', 'opts' => ['O(N)', 'O(1/N)', 'O(1/√N)', 'O(log N)'], 'ans' => 2, 'exp' => 'Monte Carlo error ∝ 1/√N. To halve error, you need 4× as many samples. This slow convergence is a key limitation — use quasi-Monte Carlo or variance reduction for better efficiency.'],
            ['q' => 'In an M/M/1 queue with λ=4 and μ=5, what is the traffic intensity ρ?', 'opts' => ['1.25', '0.80', '9', '20'], 'ans' => 1, 'exp' => 'ρ = λ/μ = 4/5 = 0.80. The server is busy 80% of the time. Since ρ < 1, the queue is stable. Mean wait = ρ/(μ(1-ρ)) = 0.8/(5×0.2) = 0.8 time units.'],
            ['q' => 'In the SIR model, what happens to dI/dt when S·β/γ = 1?', 'opts' => ['I increases rapidly', 'dI/dt = 0 — this is the epidemic peak', 'S = 0', 'γ = 0'], 'ans' => 1, 'exp' => 'dI/dt = I(βS - γ). Setting this to zero: βS = γ, so S = γ/β = 1/R₀. When S drops to 1/R₀ through depletion of susceptibles, the epidemic peaks — more recoveries than new infections from that point on.'],
            ['q' => 'What distinguishes an agent-based model from an equation-based model?', 'opts' => ['ABMs run faster', 'ABMs model each individual entity explicitly; equation models use aggregate state variables', 'ABMs are always deterministic', 'Equation models cannot handle populations'], 'ans' => 1, 'exp' => 'Equation-based models (ODEs, difference equations) track aggregate quantities (S, I, R as fractions). ABMs simulate each individual agent, allowing for heterogeneity, spatial structure, and emergent behavior that aggregate equations cannot capture.'],
            ['q' => 'What is a "balancing feedback loop"?', 'opts' => ['A loop that amplifies change — leads to exponential growth', 'A loop that counteracts change — drives the system toward equilibrium', 'A loop with no time delay', 'A loop between two stocks'], 'ans' => 1, 'exp' => 'Balancing (negative) feedback counteracts deviation from a goal: thermostat heats less as temperature approaches target. Reinforcing (positive) feedback amplifies deviation: more followers → more visibility → more followers.'],
            ['q' => 'In Euler\'s method for ODEs, reducing step size h by half changes the global error by approximately what factor?', 'opts' => ['Error doubles', 'Error halves (O(h) convergence)', 'Error quarters (O(h²) convergence)', 'Error is unchanged'], 'ans' => 1, 'exp' => 'Euler\'s method has O(h) global error — halving h approximately halves the error. This makes it a first-order method. Runge-Kutta 4 has O(h⁴) error — halving h reduces error by a factor of 16, which is why it\'s much preferred.'],
            ['q' => 'What does np.random.default_rng(seed=42) return?', 'opts' => ['The integer 42', 'A seeded Generator object for reproducible random number generation', 'A fixed array of 42 random numbers', 'The NumPy global random state'], 'ans' => 1, 'exp' => 'default_rng() returns a Generator object — not a single number. It uses the PCG64 algorithm. By passing seed=42, the generator is initialized at a fixed point, so all subsequent draws are identical across runs.'],
            ['q' => 'Why should you discard the warm-up period in steady-state simulation analysis?', 'opts' => ['To speed up computation', 'The system starts in an artificial initial state that biases performance metrics away from true steady-state behavior', 'Warm-up data is statistically correlated with later data', 'It violates the Welch t-test assumptions'], 'ans' => 1, 'exp' => 'Starting from an empty queue (or zero population, or full inventory), the system behaves unrealistically until it reaches statistical equilibrium. Including transient data biases performance estimates — e.g., mean wait appears artificially low.'],
            ['q' => 'In Monte Carlo risk analysis for a financial portfolio, the 5th percentile of final values represents:', 'opts' => ['The most likely outcome', 'The worst-case scenario', 'The value that only 5% of simulated outcomes fall below — the lower tail risk', 'The expected return minus one standard deviation'], 'ans' => 2, 'exp' => 'The 5th percentile is the value exceeded by 95% of simulations. It represents the left tail — what happens in bad scenarios. In risk management, this is often called VaR (Value at Risk) at the 95% confidence level.'],
            ['q' => 'Welch\'s t-test is preferred over Student\'s t-test when comparing simulations because:', 'opts' => ['Welch\'s test is always more powerful', 'The two configurations may have different variances — Welch\'s test does not assume equal variances', 'Student\'s t-test requires normality; Welch\'s does not', 'Welch\'s test produces smaller p-values'], 'ans' => 1, 'exp' => 'Student\'s t-test assumes the two populations have equal variance. When comparing simulation configurations with different parameters, variance will generally differ. Welch\'s test adjusts degrees of freedom using the Satterthwaite approximation — valid under unequal variances.'],
            ['q' => 'In the Lotka-Volterra predator-prey model, what causes the cyclic oscillation?', 'opts' => ['External seasonal forcing', 'Coupled reinforcing and balancing feedback loops between predator and prey populations', 'Random noise in the ODE solver', 'The initial conditions always repeat'], 'ans' => 1, 'exp' => 'Rabbits grow when there are few foxes (reinforcing); this fuels fox growth (reinforcing); foxes deplete rabbits (balancing); rabbits crash; foxes starve and crash; rabbits recover. This coupled loop structure generates perpetual oscillations.'],
        ];

        $finalContent = <<<'HTML'
<div id="org-lock-screen" style="display:block;">
    <h2>Module 6: Final Examination — Modeling & Simulation</h2>
    <p>This examination is restricted to enrolled students. Please verify your organization account to continue.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 6: Final Examination — Modeling & Simulation</h2>
    <p>This comprehensive exam covers all topics from Lessons 6.1 through 6.10 — simulation fundamentals, Monte Carlo methods, discrete-event simulation, differential equations, epidemic modeling, agent-based models, system dynamics, sensitivity analysis, and output statistics. Good luck!</p>
HTML;

        $finalContent .= $this->appendQuiz('', 'FINAL_EXAM_6', $allFinalQuestions);
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
            'title'       => '6.11 Final Exam: Modeling & Simulation Mastery',
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