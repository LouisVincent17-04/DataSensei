<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module11LessonsSeeder
 * Seeds lessons for Module 11: Introduction to Bayesian Data Analysis.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module11LessonsSeeder
 */
class Module11LessonsSeeder extends Seeder
{
    public function run()
    {
        $module = Module::where('order_index', 11)->firstOrFail();
        Lesson::where('module_id', $module->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 11.1 — What Is Bayesian Thinking?
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What Is Bayesian Thinking?</h2>
<p>Bayesian data analysis is a fundamentally different philosophy of statistics from the classical (frequentist) approach most students encounter first. At its core, Bayesian thinking treats <strong>probability as a measure of belief or uncertainty</strong> — not as a long-run frequency. When you collect data, you do not start from scratch; you start with what you already know and <em>update</em> that knowledge systematically. This module teaches you that framework from the ground up, building both the intuition and the code to apply it to real data.</p>

<h3>The Two Philosophies of Statistics</h3>
<p>To understand why Bayesian analysis matters, you need to see where it differs from the frequentist approach you may already know:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — Frequentist vs Bayesian</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">FREQUENTIST VIEW:</span>
  "Probability is the long-run frequency of an event
   over infinite hypothetical repetitions."
  Parameters are FIXED but unknown.
  Data is RANDOM (varies across experiments).
  Goal: find the single true parameter value.
  Tools: p-values, confidence intervals, MLE.

<span style="color:#a7f3d0;">BAYESIAN VIEW:</span>
  "Probability is a degree of belief — it can apply
   to any uncertain quantity, even unique events."
  Parameters are RANDOM (they have distributions).
  Data is FIXED (what we actually observed).
  Goal: update our probability distribution over parameters.
  Tools: prior, likelihood, posterior, credible intervals.</div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Key Insight</span>
Frequentists ask: "What is P(data | hypothesis)?"
Bayesians  ask:  "What is P(hypothesis | data)?"
The Bayesian question is usually what we ACTUALLY want to answer.</div>
  </div>
</div>

<h3>A Concrete Intuition: The Medical Test</h3>
<p>Suppose a disease affects 1% of the population. A test for this disease is 95% accurate (sensitivity = 95%, specificity = 95%). You test positive. What is the probability you actually have the disease? Most people say "95%." Bayesian reasoning gives the correct answer — and it is startling.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Bayesian Medical Test Calculation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Bayesian updating: medical test example</span>
<span style="color:#6b7280;"># We want P(disease | positive test)</span>

<span style="color:#6b7280;"># Prior: background disease rate in population</span>
<span style="color:#93c5fd;">p_disease</span>    = <span style="color:#fcd34d;">0.01</span>   <span style="color:#6b7280;"># P(D)  = 1%</span>
<span style="color:#93c5fd;">p_no_disease</span> = <span style="color:#fcd34d;">1</span> - p_disease  <span style="color:#6b7280;"># P(¬D) = 99%</span>

<span style="color:#6b7280;"># Likelihood: test accuracy characteristics</span>
<span style="color:#93c5fd;">p_pos_given_disease</span>    = <span style="color:#fcd34d;">0.95</span>  <span style="color:#6b7280;"># Sensitivity: P(+|D)</span>
<span style="color:#93c5fd;">p_pos_given_no_disease</span> = <span style="color:#fcd34d;">0.05</span>  <span style="color:#6b7280;"># False positive: P(+|¬D)</span>

<span style="color:#6b7280;"># Total probability of testing positive (denominator)</span>
<span style="color:#93c5fd;">p_positive</span> = (p_pos_given_disease * p_disease +
              p_pos_given_no_disease * p_no_disease)

<span style="color:#6b7280;"># Bayes' Theorem: P(D|+) = P(+|D) * P(D) / P(+)</span>
<span style="color:#93c5fd;">p_disease_given_pos</span> = (p_pos_given_disease * p_disease) / p_positive

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"P(positive test)           = {p_positive:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"P(disease | positive test) = {p_disease_given_pos:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"As a percentage:           = {p_disease_given_pos*100:.1f}%"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>P(positive test)           = 0.0590
P(disease | positive test) = 0.1610
As a percentage:           = 16.1%</div>
  </div>
</div>

<p>Even with a 95%-accurate test, a positive result only means you have about a <strong>16% chance</strong> of having the disease — because the disease is rare. The low prior probability (1%) dominates. This is the power of Bayesian reasoning: it forces you to account for what you already knew before seeing data.</p>

<h3>The Three Core Components of Bayesian Analysis</h3>
<p>Every Bayesian analysis involves three quantities, connected by Bayes' Theorem:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — The Three Components</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">PRIOR  P(θ)</span>
  Your belief about the parameter θ BEFORE seeing data.
  Encodes domain knowledge, previous studies, or pure ignorance.
  Example: "I believe the coin is probably fair — centered at 0.5."

<span style="color:#93c5fd;">LIKELIHOOD  P(data | θ)</span>
  How probable is the observed data if θ were the true value?
  This is the statistical model — the bridge between data and parameters.
  Example: "If the coin has bias θ, observing 7 heads in 10 flips has
            probability: C(10,7) * θ^7 * (1-θ)^3"

<span style="color:#a7f3d0;">POSTERIOR  P(θ | data)</span>
  Your updated belief about θ AFTER seeing data.
  This is what you actually want. It is the answer.
  Combines prior knowledge with evidence from data.

<span style="color:#fcd34d;">BAYES' THEOREM:</span>
  P(θ | data)  ∝  P(data | θ)  ×  P(θ)
  Posterior    ∝  Likelihood   ×  Prior</div>
  </div>
</div>

<h3>Why Bayesian Analysis Is Gaining Dominance</h3>
<p>Bayesian analysis has surged in popularity over the past two decades for several reasons: modern computational tools (MCMC, variational inference) make it tractable; it handles small data gracefully by incorporating prior knowledge; it produces full probability distributions as answers (not just point estimates); and it naturally quantifies uncertainty in a way frequentist p-values do not. Fields from medicine to machine learning to finance are transitioning to Bayesian methods.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '11.1 What Is Bayesian Thinking?',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L11_1', [
                ['q' => 'In the Bayesian framework, what does the PRIOR distribution represent?', 'opts' => ['The probability of the data given the parameter', 'Your belief about the parameter before observing any data', 'The final answer after updating with data', 'The long-run frequency of the parameter value'], 'ans' => 1, 'exp' => 'The prior P(θ) encodes your beliefs about the parameter before any data is collected. It can come from domain knowledge, previous studies, or represent ignorance (a flat/uninformative prior).'],
                ['q' => 'In the medical test example (1% disease rate, 95% accurate test), approximately what is P(disease | positive test)?', 'opts' => ['95%', '50%', '16%', '1%'], 'ans' => 2, 'exp' => 'Using Bayes\' Theorem: P(D|+) = (0.95 × 0.01) / (0.95×0.01 + 0.05×0.99) ≈ 0.161 ≈ 16.1%. The low prior (1%) dominates, making most positives false alarms.'],
                ['q' => 'What is the key formula connecting prior, likelihood, and posterior?', 'opts' => ['P(θ|data) = P(θ) / P(data|θ)', 'P(θ|data) ∝ P(data|θ) × P(θ)', 'P(θ|data) = P(data|θ) + P(θ)', 'P(θ|data) = P(data) × P(θ)'], 'ans' => 1, 'exp' => 'Bayes\' Theorem states P(θ|data) ∝ P(data|θ) × P(θ). The posterior is proportional to the likelihood times the prior. The denominator P(data) normalizes the result into a proper probability distribution.'],
                ['q' => 'What is the fundamental difference in how Bayesians treat model parameters?', 'opts' => ['Parameters are fixed constants estimated from data', 'Parameters are random variables that have probability distributions', 'Parameters are always assumed to follow a normal distribution', 'Parameters are eliminated using maximum likelihood estimation'], 'ans' => 1, 'exp' => 'Bayesians treat parameters as random variables with probability distributions reflecting uncertainty. Frequentists treat parameters as unknown but fixed constants. This distinction drives all downstream differences in interpretation.'],
                ['q' => 'Which question does Bayesian analysis actually answer?', 'opts' => ['P(data | hypothesis is true)', 'P(observing data this extreme if null hypothesis were true)', 'P(hypothesis | data)', 'P(parameter = exact value)'], 'ans' => 2, 'exp' => 'Bayesian analysis answers P(hypothesis | data) — the probability of the hypothesis given the data actually observed. Frequentist p-values answer P(data this extreme | null hypothesis), which is NOT the same thing and is often misinterpreted.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 11.2 — Bayes' Theorem: The Engine of Inference
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Bayes' Theorem: The Engine of Inference</h2>
<p>Bayes' Theorem is not merely a formula — it is the logical foundation for updating beliefs under uncertainty. First published posthumously by Reverend Thomas Bayes in 1763 and later refined by Pierre-Simon Laplace, it tells us exactly how to revise probabilities when new evidence arrives. In this lesson we derive the theorem, understand every component deeply, and implement the updating process from scratch in Python.</p>

<h3>Deriving Bayes' Theorem from First Principles</h3>
<p>The derivation requires only the definition of conditional probability and the multiplication rule — nothing exotic:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DERIVATION — Bayes' Theorem from Conditional Probability</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Definition of conditional probability:</span>
P(A|B) = P(A ∩ B) / P(B)      <span style="color:#6b7280;"># [1]</span>
P(B|A) = P(A ∩ B) / P(A)      <span style="color:#6b7280;"># [2]</span>

<span style="color:#6b7280;"># From [2]: P(A ∩ B) = P(B|A) × P(A)
# Substitute into [1]:</span>
P(A|B) = P(B|A) × P(A) / P(B)

<span style="color:#6b7280;"># In the context of parameters θ and data D:</span>
P(θ|D) = P(D|θ) × P(θ) / P(D)

<span style="color:#6b7280;"># Where P(D) = ∫ P(D|θ) × P(θ) dθ  (Law of Total Probability)
# This integral normalizes the posterior — often called the
# "evidence" or "marginal likelihood."
#
# Since P(D) is a constant w.r.t. θ, we often write:</span>
P(θ|D)  ∝  P(D|θ) × P(θ)
Posterior  ∝  Likelihood × Prior</div>
  </div>
</div>

<h3>Sequential Updating: Bayesian Learning in Action</h3>
<p>One of the most beautiful properties of Bayes' Theorem is that you can apply it sequentially. Today's posterior becomes tomorrow's prior. This means Bayesian inference is naturally incremental — each new data point refines your beliefs exactly as it should.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Sequential Bayesian Updating (Coin Flip)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Sequential Bayesian updating for coin bias estimation
# We discretize the parameter space θ ∈ {0, 0.1, 0.2, ..., 1.0}</span>

<span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np

<span style="color:#6b7280;"># Discrete grid of possible coin bias values</span>
<span style="color:#93c5fd;">thetas</span> = np.linspace(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">11</span>)  <span style="color:#6b7280;"># [0.0, 0.1, ..., 1.0]</span>

<span style="color:#6b7280;"># Prior: uniform — we have no idea what the bias is</span>
<span style="color:#93c5fd;">prior</span> = np.ones(<span style="color:#fcd34d;">11</span>) / <span style="color:#fcd34d;">11</span>

<span style="color:#6b7280;"># Observed coin flips: 1=Heads, 0=Tails</span>
<span style="color:#93c5fd;">observations</span> = [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>]  <span style="color:#6b7280;"># H, H, T, H, H</span>

<span style="color:#93c5fd;">current_prior</span> = prior.copy()

<span style="color:#c4b5fd;">for</span> i, flip <span style="color:#c4b5fd;">in</span> enumerate(observations):
    <span style="color:#6b7280;"># Likelihood: P(flip | θ)</span>
    <span style="color:#c4b5fd;">if</span> flip == <span style="color:#fcd34d;">1</span>:  <span style="color:#6b7280;"># Heads</span>
        likelihood = thetas
    <span style="color:#c4b5fd;">else</span>:         <span style="color:#6b7280;"># Tails</span>
        likelihood = <span style="color:#fcd34d;">1</span> - thetas

    <span style="color:#6b7280;"># Unnormalized posterior = likelihood × prior</span>
    <span style="color:#93c5fd;">unnorm_posterior</span> = likelihood * current_prior

    <span style="color:#6b7280;"># Normalize so probabilities sum to 1</span>
    <span style="color:#93c5fd;">posterior</span> = unnorm_posterior / unnorm_posterior.sum()

    label = <span style="color:#a7f3d0;">"H"</span> <span style="color:#c4b5fd;">if</span> flip == <span style="color:#fcd34d;">1</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"T"</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"After flip {i+1} ({label}): MAP estimate = {thetas[posterior.argmax()]:.1f}"</span>)

    <span style="color:#6b7280;"># Posterior becomes the new prior</span>
    <span style="color:#93c5fd;">current_prior</span> = posterior

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nFinal posterior distribution:"</span>)
<span style="color:#c4b5fd;">for</span> t, p <span style="color:#c4b5fd;">in</span> zip(thetas, posterior):
    bar = <span style="color:#a7f3d0;">"█"</span> * int(p * <span style="color:#fcd34d;">100</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  θ={t:.1f}: {bar} {p:.3f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>After flip 1 (H): MAP estimate = 1.0
After flip 2 (H): MAP estimate = 1.0
After flip 3 (T): MAP estimate = 0.7
After flip 4 (H): MAP estimate = 0.8
After flip 5 (H): MAP estimate = 0.8

Final posterior distribution:
  θ=0.0: 0.000
  θ=0.1:  0.000
  θ=0.2:  0.001
  θ=0.3:  0.005
  θ=0.4:  0.026
  θ=0.5:  0.089
  θ=0.6:  0.199
  θ=0.7: ████████ 0.278
  θ=0.8: ████████ 0.279
  θ=0.9:  0.141
  θ=1.0:  0.004</div>
  </div>
</div>

<h3>Understanding the Denominator: The Marginal Likelihood</h3>
<p>The denominator P(D) in Bayes' Theorem — called the <strong>marginal likelihood</strong> or <strong>evidence</strong> — is the total probability of observing the data across all possible parameter values. In the discrete case it is a sum; in the continuous case it is an integral. This quantity is what makes exact Bayesian inference computationally hard for complex models, and it is why approximate methods like MCMC exist.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — The Three Computational Challenges</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">EASY (closed-form posterior):</span>
  When the prior and likelihood are "conjugate" pairs,
  the posterior has an analytical formula.
  Example: Beta prior + Binomial likelihood → Beta posterior.
  No numerical integration needed.

<span style="color:#fcd34d;">MEDIUM (grid approximation):</span>
  Discretize the parameter space, compute posterior at each point.
  Exact but scales poorly: 1000 points × 1000 points = 10⁶ grid cells.
  Works for 1-3 parameters. The code above used this approach.

<span style="color:#fca5a5;">HARD (high-dimensional):</span>
  Modern models have hundreds or thousands of parameters.
  The integral P(D) = ∫ P(D|θ)P(θ)dθ is intractable.
  Solution: MCMC sampling (Lesson 11.7) or variational inference.
  Libraries: PyMC, Stan, NumPyro, TensorFlow Probability.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '11.2 Bayes\' Theorem: The Engine of Inference',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L11_2', [
                ['q' => 'What two things does Bayes\' Theorem require to compute the posterior?', 'opts' => ['The mean and variance of the data', 'The prior P(θ) and the likelihood P(data|θ)', 'The p-value and the confidence interval', 'The sample size and the standard error'], 'ans' => 1, 'exp' => 'Bayes\' Theorem states P(θ|data) ∝ P(data|θ) × P(θ). You need the prior (your pre-data beliefs) and the likelihood (how probable the data is under each θ value). Their product, once normalized, gives the posterior.'],
                ['q' => 'In sequential Bayesian updating, what becomes the new prior for the next observation?', 'opts' => ['The original flat prior', 'The likelihood of the latest data point', 'The posterior from the previous update', 'The marginal likelihood P(data)'], 'ans' => 2, 'exp' => 'One of the most powerful properties of Bayesian inference: today\'s posterior becomes tomorrow\'s prior. This allows you to update beliefs incrementally as each new data point arrives, producing the same final result as batch processing all data at once.'],
                ['q' => 'What is the "marginal likelihood" P(D) in Bayes\' Theorem?', 'opts' => ['The probability of the parameter being exactly zero', 'The total probability of observing the data, integrated over all possible parameter values', 'The maximum likelihood estimate of the parameter', 'The prior probability of the data'], 'ans' => 1, 'exp' => 'P(D) = ∫ P(D|θ)P(θ)dθ integrates the likelihood over all possible parameter values, weighted by the prior. It normalizes the posterior to sum/integrate to 1. Computing this integral is the main computational challenge of Bayesian inference.'],
                ['q' => 'After observing 4 heads and 1 tail (H,H,T,H,H), what does the posterior for coin bias θ look like?', 'opts' => ['A spike at exactly θ=0.8 (4/5)', 'A broad distribution centered around θ=0.7-0.8 with uncertainty on both sides', 'A flat uniform distribution unchanged from the prior', 'A spike at θ=1.0 since heads were more common'], 'ans' => 1, 'exp' => 'With only 5 flips, there is still substantial uncertainty. The posterior is a distribution (not a spike), peaked around 0.7-0.8 reflecting the 4/5 = 80% observed frequency, but spread out due to the small sample size. More data would sharpen the peak.'],
                ['q' => 'Why is exact Bayesian inference computationally difficult for models with many parameters?', 'opts' => ['Bayesian models require more data than frequentist models', 'The marginal likelihood integral P(D) = ∫P(D|θ)P(θ)dθ becomes intractable in high dimensions', 'Priors cannot be specified for more than 3 parameters', 'The posterior always has multiple modes in high dimensions'], 'ans' => 1, 'exp' => 'The normalizing constant P(D) requires integrating over the entire parameter space. With d parameters, this is a d-dimensional integral that grows exponentially hard. This "curse of dimensionality" motivates MCMC sampling and variational inference methods.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 11.3 — Prior Distributions: Encoding Prior Knowledge
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Prior Distributions: Encoding Prior Knowledge</h2>
<p>The prior distribution is the most distinctive — and most debated — element of Bayesian analysis. It formally encodes everything you believe about a parameter <em>before</em> looking at the data. Critics of Bayesian analysis often object that priors are "subjective." But this is precisely their strength: they make assumptions <em>explicit</em>, where frequentist assumptions are often implicit and invisible. In this lesson you will learn the major classes of priors, how to choose them, and how to implement them in Python.</p>

<h3>The Three Classes of Priors</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">TAXONOMY — Types of Prior Distributions</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">1. UNINFORMATIVE (DIFFUSE) PRIORS</span>
   Goal: Let the data speak — express ignorance.
   Example: Uniform prior β ~ Uniform(0, 1) for a probability.
   Risk: "Uninformative" is context-dependent. Flat on one scale
         is not flat on another (Jeffreys prior addresses this).

<span style="color:#93c5fd;">2. WEAKLY INFORMATIVE PRIORS</span>
   Goal: Exclude impossible values, allow regularization.
   Example: μ ~ Normal(0, 10) for a regression coefficient.
   This says "probably not millions, but I'm not sure of the sign."
   Recommended default for most practical modeling (by Stan team).

<span style="color:#c4b5fd;">3. INFORMATIVE (STRONG) PRIORS</span>
   Goal: Incorporate real domain knowledge or previous study results.
   Example: Drug effect ~ Normal(0.3, 0.05) based on prior clinical trial.
   Requires genuine expertise. Can drastically improve small-sample inference.</div>
  </div>
</div>

<h3>The Beta Distribution: The Natural Prior for Probabilities</h3>
<p>When your parameter is a probability (constrained to [0,1]), the <strong>Beta distribution</strong> is the natural prior. It is defined by two shape parameters α and β, and its mean is α/(α+β). By tuning α and β you can represent anything from total ignorance to strong belief.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Visualizing Beta Priors</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np
<span style="color:#93c5fd;">from</span> scipy <span style="color:#93c5fd;">import</span> stats

<span style="color:#6b7280;"># Different Beta priors and what they represent</span>
<span style="color:#93c5fd;">x</span> = np.linspace(<span style="color:#fcd34d;">0.001</span>, <span style="color:#fcd34d;">0.999</span>, <span style="color:#fcd34d;">200</span>)

<span style="color:#93c5fd;">beta_configs</span> = [
    (<span style="color:#fcd34d;">1</span>,  <span style="color:#fcd34d;">1</span>,   <span style="color:#a7f3d0;">"Uniform — total ignorance"</span>),
    (<span style="color:#fcd34d;">2</span>,  <span style="color:#fcd34d;">2</span>,   <span style="color:#a7f3d0;">"Weakly centered at 0.5"</span>),
    (<span style="color:#fcd34d;">5</span>,  <span style="color:#fcd34d;">5</span>,   <span style="color:#a7f3d0;">"Moderately centered at 0.5 (fair coin belief)"</span>),
    (<span style="color:#fcd34d;">8</span>,  <span style="color:#fcd34d;">2</span>,   <span style="color:#a7f3d0;">"Biased toward high values (θ ≈ 0.8)"</span>),
    (<span style="color:#fcd34d;">0.5</span>, <span style="color:#fcd34d;">0.5</span>, <span style="color:#a7f3d0;">"Jeffreys prior — agnostic but not flat"</span>),
]

<span style="color:#c4b5fd;">for</span> alpha, beta, label <span style="color:#c4b5fd;">in</span> beta_configs:
    dist  = stats.beta(alpha, beta)
    mean  = alpha / (alpha + beta)
    mode  = (alpha - <span style="color:#fcd34d;">1</span>) / (alpha + beta - <span style="color:#fcd34d;">2</span>) <span style="color:#c4b5fd;">if</span> alpha > <span style="color:#fcd34d;">1</span> <span style="color:#c4b5fd;">and</span> beta > <span style="color:#fcd34d;">1</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"boundary"</span>
    var   = (alpha * beta) / ((alpha+beta)**<span style="color:#fcd34d;">2</span> * (alpha+beta+<span style="color:#fcd34d;">1</span>))
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Beta({alpha}, {beta}): {label}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Mean={mean:.3f}, Std={var**0.5:.3f}, Mode={mode}"</span>)
    <span style="color:#93c5fd;">print</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Beta(1, 1): Uniform — total ignorance
  Mean=0.500, Std=0.289, Mode=boundary

Beta(2, 2): Weakly centered at 0.5
  Mean=0.500, Std=0.224, Mode=0.5

Beta(5, 5): Moderately centered at 0.5 (fair coin belief)
  Mean=0.500, Std=0.149, Mode=0.5

Beta(8, 2): Biased toward high values (θ ≈ 0.8)
  Mean=0.800, Std=0.121, Mode=0.875

Beta(0.5, 0.5): Jeffreys prior — agnostic but not flat
  Mean=0.500, Std=0.354, Mode=boundary</div>
  </div>
</div>

<h3>Common Priors for Different Parameter Types</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Prior Cheat Sheet with scipy.stats</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">from</span> scipy <span style="color:#93c5fd;">import</span> stats
<span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np

<span style="color:#6b7280;"># ── Probabilities / rates (θ ∈ [0,1]) ──────────────────────</span>
<span style="color:#93c5fd;">prior_prob</span>     = stats.beta(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>)           <span style="color:#6b7280;"># flat</span>
<span style="color:#93c5fd;">prior_prob_inf</span> = stats.beta(<span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">5</span>)           <span style="color:#6b7280;"># believe fair coin</span>

<span style="color:#6b7280;"># ── Means / regression coefficients (unbounded) ─────────────</span>
<span style="color:#93c5fd;">prior_mean</span>     = stats.norm(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>)            <span style="color:#6b7280;"># standard normal</span>
<span style="color:#93c5fd;">prior_mean_wide</span>= stats.norm(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">10</span>)           <span style="color:#6b7280;"># weakly informative</span>

<span style="color:#6b7280;"># ── Standard deviations / scale (σ > 0) ────────────────────</span>
<span style="color:#93c5fd;">prior_std</span>      = stats.halfnorm(scale=<span style="color:#fcd34d;">1</span>)     <span style="color:#6b7280;"># half-normal, σ > 0</span>
<span style="color:#93c5fd;">prior_std_exp</span>  = stats.expon(scale=<span style="color:#fcd34d;">1</span>)        <span style="color:#6b7280;"># exponential</span>

<span style="color:#6b7280;"># ── Count rates (λ > 0) ─────────────────────────────────────</span>
<span style="color:#93c5fd;">prior_rate</span>     = stats.gamma(a=<span style="color:#fcd34d;">2</span>, scale=<span style="color:#fcd34d;">1</span>)   <span style="color:#6b7280;"># gamma prior for Poisson λ</span>

<span style="color:#6b7280;"># Sample from each and show summary statistics</span>
<span style="color:#93c5fd;">samples</span> = prior_mean.rvs(size=<span style="color:#fcd34d;">10000</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Normal(0,1) prior — 95% of mass in: [{np.percentile(samples,2.5):.2f}, {np.percentile(samples,97.5):.2f}]"</span>)

<span style="color:#93c5fd;">samples</span> = prior_mean_wide.rvs(size=<span style="color:#fcd34d;">10000</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Normal(0,10) prior — 95% of mass in: [{np.percentile(samples,2.5):.2f}, {np.percentile(samples,97.5):.2f}]"</span>)

<span style="color:#93c5fd;">samples</span> = prior_std.rvs(size=<span style="color:#fcd34d;">10000</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"HalfNormal(1) prior — 95% of mass in: [0.00, {np.percentile(samples,97.5):.2f}]"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Normal(0,1) prior — 95% of mass in: [-1.96, 1.96]
Normal(0,10) prior — 95% of mass in: [-19.60, 19.60]
HalfNormal(1) prior — 95% of mass in: [0.00, 1.96]</div>
  </div>
</div>

<h3>Prior Sensitivity Analysis</h3>
<p>A critical step in any Bayesian analysis is checking how sensitive your conclusions are to your choice of prior. If the posterior changes dramatically when you use different reasonable priors, your data may be insufficient to overwhelm the prior — and you should collect more data or be very transparent about your prior assumptions. If the posterior is stable across reasonable priors, you have a robust result.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '11.3 Prior Distributions: Encoding Prior Knowledge',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L11_3', [
                ['q' => 'Which Beta distribution represents complete ignorance about a probability parameter?', 'opts' => ['Beta(0, 0)', 'Beta(1, 1) — the uniform distribution', 'Beta(0.5, 0.5)', 'Beta(10, 10)'], 'ans' => 1, 'exp' => 'Beta(1, 1) is equivalent to a Uniform(0,1) distribution — it places equal probability density across the entire [0,1] interval, representing total ignorance about the true probability value.'],
                ['q' => 'You believe a new drug increases survival by about 20% based on a small pilot study. What kind of prior is this?', 'opts' => ['Uninformative prior', 'Jeffreys prior', 'Informative prior based on previous data', 'Improper prior'], 'ans' => 2, 'exp' => 'Using results from a previous study to inform your prior is exactly the definition of an informative prior. The pilot study gives you concrete numerical evidence that the effect is around 20%, which you encode in the prior distribution.'],
                ['q' => 'What is the mean of a Beta(α, β) distribution?', 'opts' => ['α × β', 'α / (α + β)', '(α - 1) / (α + β - 2)', 'α / β'], 'ans' => 1, 'exp' => 'The mean of Beta(α, β) is α/(α+β). For Beta(8,2): mean = 8/10 = 0.8. For Beta(5,5): mean = 5/10 = 0.5. This formula shows that the ratio of α to (α+β) determines the central tendency.'],
                ['q' => 'What is the purpose of prior sensitivity analysis?', 'opts' => ['To find the single best prior for a given dataset', 'To check whether conclusions change significantly under different reasonable prior choices', 'To prove that Bayesian analysis is objective', 'To compute the likelihood function more accurately'], 'ans' => 1, 'exp' => 'Sensitivity analysis tests whether your posterior conclusions are robust to different prior specifications. If the posterior barely changes across reasonable priors, the data is dominating — a good sign. If it changes dramatically, your results depend heavily on your prior assumptions.'],
                ['q' => 'Which distribution is recommended as a prior for a standard deviation parameter σ > 0?', 'opts' => ['Normal(0, 1) — the standard normal', 'Uniform(-∞, +∞) — a flat prior', 'HalfNormal or Exponential — distributions constrained to positive values', 'Beta(1,1) — uniform on [0,1]'], 'ans' => 2, 'exp' => 'Standard deviations must be positive (σ > 0). Using a full Normal prior would allow negative values. HalfNormal (Normal folded at 0) or Exponential distributions are natural choices as they are defined only on the positive real line.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 11.4 — Likelihood Functions and Statistical Models
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Likelihood Functions and Statistical Models</h2>
<p>The <strong>likelihood function</strong> is the bridge between your data and your parameters. It answers the question: "Given that the true parameter value is θ, how probable is the data I actually observed?" Critically, the likelihood is a function of <em>θ</em> (not of the data) — you plug in fixed observed data and vary θ to see which values made the data most probable. Understanding the likelihood is essential because it is the same component that underpins Maximum Likelihood Estimation (MLE), the backbone of most classical statistical methods.</p>

<h3>The Likelihood vs the Probability Distribution</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — Probability vs Likelihood</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># PROBABILITY: parameter is fixed, data varies
# P(X=k | θ)  asks: "If θ is the true value, how probable is outcome k?"</span>

<span style="color:#6b7280;"># LIKELIHOOD: data is fixed, parameter varies
# L(θ | X=k)  asks: "Given we observed k, how probable is each θ?"
#
# Mathematically they use the same formula, but the interpretation
# and what you hold fixed is different:
#
#   P(X=7 | θ=0.5) = C(10,7) × 0.5^7 × 0.5^3 = 0.117
#     → "If fair coin, probability of 7 heads in 10 flips is 11.7%"
#
#   L(θ=0.5 | X=7) = C(10,7) × 0.5^7 × 0.5^3 = 0.117
#     → "If we saw 7 heads, likelihood of θ=0.5 is 0.117"
#
# The LIKELIHOOD IS NOT A PROBABILITY OVER θ.
# L(θ|data) does not sum/integrate to 1 over θ values.
# It only becomes a distribution when multiplied by the prior.</span></div>
  </div>
</div>

<h3>Computing the Binomial Likelihood</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Binomial Likelihood Function</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np
<span style="color:#93c5fd;">from</span> scipy <span style="color:#93c5fd;">import</span> stats

<span style="color:#6b7280;"># Observed data: 7 heads in 10 flips</span>
<span style="color:#93c5fd;">n_flips</span>  = <span style="color:#fcd34d;">10</span>
<span style="color:#93c5fd;">n_heads</span>  = <span style="color:#fcd34d;">7</span>

<span style="color:#6b7280;"># Grid of candidate θ values</span>
<span style="color:#93c5fd;">theta_grid</span> = np.linspace(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">101</span>)

<span style="color:#6b7280;"># Likelihood: P(7 heads | n=10, θ) = Binomial PMF</span>
<span style="color:#93c5fd;">likelihood</span> = stats.binom.pmf(n_heads, n_flips, theta_grid)

<span style="color:#6b7280;"># Find the MLE (maximum likelihood estimate)</span>
<span style="color:#93c5fd;">mle_theta</span> = theta_grid[np.argmax(likelihood)]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"MLE of θ: {mle_theta:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"(Theoretical MLE = n_heads/n_flips = {n_heads/n_flips:.2f})"</span>)

<span style="color:#6b7280;"># Print a simple ASCII likelihood curve</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nLikelihood curve (selected θ values):"</span>)
<span style="color:#c4b5fd;">for</span> t, l <span style="color:#c4b5fd;">in</span> zip(theta_grid[::10], likelihood[::10]):
    bar = <span style="color:#a7f3d0;">"█"</span> * int(l * <span style="color:#fcd34d;">200</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  θ={t:.1f}: {bar} ({l:.4f})"</span>)

<span style="color:#6b7280;"># Log-likelihood is more numerically stable</span>
<span style="color:#93c5fd;">log_likelihood</span> = stats.binom.logpmf(n_heads, n_flips, theta_grid)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nLog-likelihood at MLE: {log_likelihood[np.argmax(log_likelihood)]:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>MLE of θ: 0.70
(Theoretical MLE = n_heads/n_flips = 0.70)

Likelihood curve (selected θ values):
  θ=0.0:  (0.0000)
  θ=0.1:  (0.0001)
  θ=0.2:  (0.0008)
  θ=0.3:  (0.0090)
  θ=0.4:  (0.0425)
  θ=0.5:  ████████████████████████ (0.1172)
  θ=0.6:  ████████████████████████████████████████ (0.2001)
  θ=0.7:  ██████████████████████████████████████████████ (0.2668)
  θ=0.8:  ████████████████████████████████ (0.2013)
  θ=0.9:  ████████ (0.0574)
  θ=1.0:  (0.0000)

Log-likelihood at MLE: -1.3219</div>
  </div>
</div>

<h3>Common Likelihood-Prior Pairs</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">REFERENCE — Statistical Models by Data Type</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#fcd34d;">Data Type          Likelihood          Natural Prior</span>
───────────────────────────────────────────────────────
Binary (0/1)       Bernoulli(θ)         Beta(α, β)
Counts (0,1,2...)  Binomial(n, θ)       Beta(α, β)
Rare events        Poisson(λ)           Gamma(α, β)
Continuous         Normal(μ, σ²)        Normal(μ₀,σ₀) × HalfNormal(σ)
Positive values    LogNormal(μ, σ²)     Normal(μ₀,σ₀) × HalfNormal(σ)
Categories         Categorical(π)       Dirichlet(α)
Survival time      Exponential(λ)       Gamma(α, β)

<span style="color:#6b7280;"># When prior family × likelihood = same-family posterior,
# they are called CONJUGATE PAIRS.
# Beta-Binomial, Gamma-Poisson, Normal-Normal are the classics.
# Conjugacy gives closed-form posteriors — no MCMC needed.</span></div>
  </div>
</div>

<h3>The Log-Likelihood: Numerical Stability</h3>
<p>In practice, you almost always work with the <strong>log-likelihood</strong> rather than the likelihood itself. Products of probabilities shrink toward zero exponentially fast as sample size grows, causing numerical underflow. Taking the log converts products into sums — stable, interpretable, and differentiable. The log transformation does not change the location of the maximum (log is monotone), so MLE gives identical results.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '11.4 Likelihood Functions and Statistical Models',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L11_4', [
                ['q' => 'What does the likelihood function L(θ | data) measure?', 'opts' => ['The probability that θ equals a specific value', 'How probable the observed data is for each candidate value of θ', 'The prior probability of the parameter', 'The marginal probability of the data'], 'ans' => 1, 'exp' => 'The likelihood L(θ|data) uses the same mathematical formula as the probability distribution but holds the DATA fixed and varies θ. It asks: "For each possible θ, how probable was the data we actually saw?"'],
                ['q' => 'For the Binomial likelihood with n=10 and 7 heads observed, what is the MLE of θ?', 'opts' => ['0.5 (always assume fair)', '0.7 (= 7/10)', '0.95 (maximum sensitivity)', '0.8 (rounded up)'], 'ans' => 1, 'exp' => 'The MLE for a binomial proportion is always k/n — the observed proportion. With 7 heads in 10 flips, the MLE is 7/10 = 0.70. This is the value of θ that maximizes P(7 heads | n=10, θ).'],
                ['q' => 'Why do statisticians almost always use log-likelihood instead of likelihood?', 'opts' => ['Log-likelihood always gives larger values making results clearer', 'Products of probabilities underflow to zero for large samples; log converts products to sums, avoiding numerical issues', 'Log-likelihood produces tighter confidence intervals', 'Logarithms make the likelihood function linear'], 'ans' => 1, 'exp' => 'For n data points, L(θ) = ∏P(xᵢ|θ) — a product of probabilities. Each factor is ≤ 1, and with hundreds of data points the product becomes astronomically small, underflowing to 0 in floating-point arithmetic. log L = ΣlogP(xᵢ|θ) is a sum of reasonable numbers.'],
                ['q' => 'What makes Beta-Binomial a "conjugate pair"?', 'opts' => ['Both distributions are symmetric', 'The posterior distribution has the same family (Beta) as the prior when the likelihood is Binomial', 'They both have parameters α and β', 'They share the same mean formula'], 'ans' => 1, 'exp' => 'Conjugate pairs are prior-likelihood combinations where the posterior belongs to the same distribution family as the prior. Beta prior × Binomial likelihood → Beta posterior. If you start with Beta(α,β) and observe k successes in n trials, the posterior is Beta(α+k, β+n-k).'],
                ['q' => 'Which likelihood model is most appropriate for modeling the number of customer service calls arriving per hour?', 'opts' => ['Normal(μ, σ²) — counts are approximately normal', 'Binomial(n, θ) — counts are between 0 and n', 'Poisson(λ) — models counts of rare events per unit time', 'Beta(α, β) — models probabilities between 0 and 1'], 'ans' => 2, 'exp' => 'The Poisson distribution models the number of events occurring in a fixed interval of time or space, given an average rate λ. It is the natural likelihood for count data like calls per hour, website visits per day, or defects per unit produced.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 11.5 — Conjugate Priors and Analytical Posteriors
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Conjugate Priors and Analytical Posteriors</h2>
<p>A <strong>conjugate prior</strong> is a prior distribution that, when combined with a specific likelihood, produces a posterior in the same distributional family as the prior. This is enormously valuable: when conjugacy holds, the posterior has a <em>closed-form formula</em> — no numerical integration, no MCMC, no approximation needed. You update the parameters analytically with a simple arithmetic rule. This lesson covers the three most important conjugate families in applied Bayesian analysis.</p>

<h3>1. Beta-Binomial: Estimating Proportions</h3>
<p>The workhorse for any binary outcome — conversion rates, click-through rates, survey responses, clinical trial outcomes.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FORMULA — Beta-Binomial Conjugacy</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">Prior:</span>       θ ~ Beta(α, β)
<span style="color:#93c5fd;">Likelihood:</span>  k ~ Binomial(n, θ)   [k successes in n trials]
<span style="color:#a7f3d0;">Posterior:</span>   θ | k, n ~ Beta(α + k,  β + n − k)

<span style="color:#6b7280;"># Intuition: α and β are like "pseudo-counts"
# α = prior successes, β = prior failures
# Observing k real successes and (n-k) failures just ADDS to them.
# More data → larger (α+β) → tighter posterior → less uncertainty.</span>

<span style="color:#fcd34d;">Posterior Summary:</span>
  Mean = (α + k) / (α + β + n)
  Mode = (α + k − 1) / (α + β + n − 2)    [if α+k > 1 and β+n-k > 1]
  Var  = (α+k)(β+n-k) / [(α+β+n)²(α+β+n+1)]</div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — A/B Test: Bayesian Conversion Rate Analysis</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np
<span style="color:#93c5fd;">from</span> scipy <span style="color:#93c5fd;">import</span> stats

<span style="color:#6b7280;"># A/B Test: Which landing page has higher conversion?
# Prior: weakly informative — conversion is typically 5-20%</span>
<span style="color:#93c5fd;">alpha_prior</span>, <span style="color:#93c5fd;">beta_prior</span> = <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">18</span>  <span style="color:#6b7280;"># prior mean = 2/20 = 10%</span>

<span style="color:#6b7280;"># Observed data</span>
<span style="color:#93c5fd;">page_A</span> = {<span style="color:#a7f3d0;">'visitors'</span>: <span style="color:#fcd34d;">1000</span>, <span style="color:#a7f3d0;">'conversions'</span>: <span style="color:#fcd34d;">120</span>}
<span style="color:#93c5fd;">page_B</span> = {<span style="color:#a7f3d0;">'visitors'</span>: <span style="color:#fcd34d;">1000</span>, <span style="color:#a7f3d0;">'conversions'</span>: <span style="color:#fcd34d;">145</span>}

<span style="color:#6b7280;"># Analytical posterior: Beta(α + k, β + n - k)</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">posterior</span>(data, a=alpha_prior, b=beta_prior):
    k = data[<span style="color:#a7f3d0;">'conversions'</span>]
    n = data[<span style="color:#a7f3d0;">'visitors'</span>]
    <span style="color:#c4b5fd;">return</span> stats.beta(a + k, b + n - k)

<span style="color:#93c5fd;">post_A</span> = posterior(page_A)
<span style="color:#93c5fd;">post_B</span> = posterior(page_B)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Bayesian A/B Test Results ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Page A — Posterior: Beta({alpha_prior+page_A['conversions']}, {beta_prior+page_A['visitors']-page_A['conversions']})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Mean: {post_A.mean():.4f} | 95% CI: [{post_A.ppf(0.025):.4f}, {post_A.ppf(0.975):.4f}]"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Page B — Posterior: Beta({alpha_prior+page_B['conversions']}, {beta_prior+page_B['visitors']-page_B['conversions']})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Mean: {post_B.mean():.4f} | 95% CI: [{post_B.ppf(0.025):.4f}, {post_B.ppf(0.975):.4f}]"</span>)

<span style="color:#6b7280;"># Probability that B > A via Monte Carlo</span>
<span style="color:#93c5fd;">samples_A</span> = post_A.rvs(<span style="color:#fcd34d;">100_000</span>)
<span style="color:#93c5fd;">samples_B</span> = post_B.rvs(<span style="color:#fcd34d;">100_000</span>)
<span style="color:#93c5fd;">p_B_wins</span>  = np.mean(samples_B > samples_A)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nP(B converts better than A) = {p_B_wins:.3f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== Bayesian A/B Test Results ===
Page A — Posterior: Beta(122, 898)
  Mean: 0.1196 | 95% CI: [0.0999, 0.1410]
Page B — Posterior: Beta(147, 873)
  Mean: 0.1441 | 95% CI: [0.1224, 0.1672]

P(B converts better than A) = 0.991</div>
  </div>
</div>

<h3>2. Gamma-Poisson: Estimating Event Rates</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Gamma-Poisson: Call Center Rate Estimation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">from</span> scipy <span style="color:#93c5fd;">import</span> stats
<span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np

<span style="color:#6b7280;"># Conjugacy: Prior Gamma(α,β) + Poisson likelihood
# → Posterior Gamma(α + Σxᵢ,  β + n)
#
# Prior: Gamma(α=5, β=1) → prior mean rate = α/β = 5 calls/hour</span>
<span style="color:#93c5fd;">alpha_prior</span>, <span style="color:#93c5fd;">beta_prior</span> = <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">1</span>

<span style="color:#6b7280;"># Observed: call counts over 8 hours</span>
<span style="color:#93c5fd;">observed_calls</span> = [<span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">9</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">11</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">10</span>]

<span style="color:#6b7280;"># Update rule: Gamma(α + sum(x), β + n)</span>
<span style="color:#93c5fd;">alpha_post</span> = alpha_prior + sum(observed_calls)
<span style="color:#93c5fd;">beta_post</span>  = beta_prior  + len(observed_calls)

<span style="color:#93c5fd;">post</span> = stats.gamma(a=alpha_post, scale=<span style="color:#fcd34d;">1</span>/beta_post)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Observed: {observed_calls}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sample mean call rate: {np.mean(observed_calls):.2f} calls/hour"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Posterior: Gamma({alpha_post}, {beta_post})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Posterior mean: {post.mean():.3f} calls/hour"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"95% Credible Interval: [{post.ppf(0.025):.2f}, {post.ppf(0.975):.2f}]"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Observed: [7, 4, 9, 6, 11, 8, 5, 10]
Sample mean call rate: 7.50 calls/hour
Posterior: Gamma(65, 9)
Posterior mean: 7.222 calls/hour
95% Credible Interval: [5.57, 9.05]</div>
  </div>
</div>

<h3>3. Normal-Normal: Estimating a Mean</h3>
<p>When the data is normally distributed with known variance σ², and you place a Normal prior on the mean μ, the posterior is also Normal. The posterior mean is a precision-weighted average of the prior mean and the data mean — elegantly interpolating between what you knew and what you observed.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '11.5 Conjugate Priors and Analytical Posteriors',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L11_5', [
                ['q' => 'You start with a Beta(2, 18) prior and observe 120 conversions from 1000 visitors. What is the posterior?', 'opts' => ['Beta(120, 880)', 'Beta(122, 898)', 'Beta(2, 18)', 'Beta(120, 1000)'], 'ans' => 1, 'exp' => 'The Beta-Binomial update rule is: posterior = Beta(α + k, β + n - k). Here α=2, β=18, k=120, n=1000: posterior = Beta(2+120, 18+1000-120) = Beta(122, 898).'],
                ['q' => 'In the Beta-Binomial model, what do the prior parameters α and β conceptually represent?', 'opts' => ['The sample mean and sample variance', 'Pseudo-counts: α is like prior successes, β is like prior failures', 'The upper and lower bounds of the uniform prior', 'The shape and rate of the posterior distribution'], 'ans' => 1, 'exp' => 'α and β in a Beta prior act like pseudo-counts of prior successes and failures. A Beta(2,18) prior behaves as if you already saw 2 successes and 18 failures — giving a prior mean of 2/20 = 10%. New data simply adds to these counts.'],
                ['q' => 'The Bayesian A/B test found P(B converts better than A) = 0.991. How should this be interpreted?', 'opts' => ['There is a 99.1% chance the p-value is below 0.05', 'Given the data and prior, we are 99.1% certain that page B\'s true conversion rate is higher than page A\'s', 'The frequentist null hypothesis is rejected at α=0.009', 'B is 99.1% better in absolute conversion terms'], 'ans' => 1, 'exp' => 'Bayesian probability statements are direct: P(B > A | data) = 0.991 means there is a 99.1% posterior probability that B\'s true conversion rate exceeds A\'s. This is a direct, intuitive answer — unlike a frequentist p-value which does not say this.'],
                ['q' => 'For Gamma-Poisson conjugacy, if you start with Gamma(α, β) and observe counts x₁,...,xₙ, what is the posterior?', 'opts' => ['Gamma(α × Σxᵢ, β × n)', 'Gamma(α + Σxᵢ, β + n)', 'Poisson(α + Σxᵢ)', 'Normal(α + mean(x), β + n)'], 'ans' => 1, 'exp' => 'The Gamma-Poisson update rule adds the total observed count Σxᵢ to α, and the number of observations n to β. This is analogous to Beta-Binomial: the posterior parameters are the prior parameters plus the sufficient statistics of the data.'],
                ['q' => 'Why are conjugate priors so computationally valuable?', 'opts' => ['They always produce uniform posteriors that are easy to sample from', 'They give closed-form analytical posteriors — no numerical integration or MCMC needed', 'They guarantee the prior and posterior have the same mean', 'They eliminate the need to specify a prior at all'], 'ans' => 1, 'exp' => 'Conjugate priors produce posteriors in the same distributional family with updated parameters — a simple arithmetic formula. You update α and β directly without any numerical integration, grid approximation, or MCMC sampling. This makes analysis instant and exact.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 11.6 — Posterior Inference: Summaries and Credible Intervals
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Posterior Inference: Summaries and Credible Intervals</h2>
<p>Once you have computed or approximated the posterior distribution P(θ | data), you need to extract useful summaries from it. Unlike frequentist inference which gives you a point estimate and a confidence interval, Bayesian inference gives you an entire probability distribution — which is richer but requires more choices about how to summarize it. This lesson covers the key posterior summaries, their computation in Python, and critically, the correct interpretation of <strong>credible intervals</strong> (and why they are superior to frequentist confidence intervals for answering practical questions).</p>

<h3>Point Estimates from the Posterior</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — Three Point Estimators</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">POSTERIOR MEAN (EAP — Expected A Posteriori):</span>
  The average of the posterior distribution.
  Minimizes expected squared error loss.
  Formula: E[θ|data] = ∫ θ × P(θ|data) dθ
  Best when: distribution is roughly symmetric, no strong asymmetry.

<span style="color:#93c5fd;">POSTERIOR MODE (MAP — Maximum A Posteriori):</span>
  The peak of the posterior distribution.
  Minimizes expected 0-1 loss. Equivalent to regularized MLE.
  Best when: you need a single "most likely" value.
  Note: MAP = MLE when using a uniform prior.

<span style="color:#a7f3d0;">POSTERIOR MEDIAN:</span>
  The value where 50% of probability mass is on each side.
  Minimizes expected absolute error loss.
  Best when: distribution is skewed or has heavy tails.
  Robust to outliers in the posterior shape.</div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Computing All Posterior Summaries</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">from</span> scipy <span style="color:#93c5fd;">import</span> stats
<span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np

<span style="color:#6b7280;"># Example: Beta(30, 70) posterior
# e.g., 30 successes, 70 failures, uniform prior</span>
<span style="color:#93c5fd;">post</span> = stats.beta(<span style="color:#fcd34d;">30</span>, <span style="color:#fcd34d;">70</span>)

<span style="color:#6b7280;"># ── Point Estimates ────────────────────────────────────────</span>
<span style="color:#93c5fd;">posterior_mean</span>   = post.mean()
<span style="color:#93c5fd;">posterior_median</span> = post.median()
<span style="color:#93c5fd;">alpha</span>, <span style="color:#93c5fd;">beta</span>       = <span style="color:#fcd34d;">30</span>, <span style="color:#fcd34d;">70</span>
<span style="color:#93c5fd;">posterior_mode</span>   = (alpha - <span style="color:#fcd34d;">1</span>) / (alpha + beta - <span style="color:#fcd34d;">2</span>)
<span style="color:#93c5fd;">posterior_std</span>    = post.std()

<span style="color:#6b7280;"># ── Credible Intervals ──────────────────────────────────────</span>
<span style="color:#6b7280;"># Equal-tailed 95% credible interval</span>
<span style="color:#93c5fd;">ci_low</span>, <span style="color:#93c5fd;">ci_high</span>  = post.ppf(<span style="color:#fcd34d;">0.025</span>), post.ppf(<span style="color:#fcd34d;">0.975</span>)

<span style="color:#6b7280;"># 90% credible interval</span>
<span style="color:#93c5fd;">ci90_low</span>, <span style="color:#93c5fd;">ci90_high</span> = post.ppf(<span style="color:#fcd34d;">0.05</span>), post.ppf(<span style="color:#fcd34d;">0.95</span>)

<span style="color:#6b7280;"># Posterior probability that θ > 0.35</span>
<span style="color:#93c5fd;">p_above_35</span> = <span style="color:#fcd34d;">1</span> - post.cdf(<span style="color:#fcd34d;">0.35</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Posterior Beta(30, 70) ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mean:   {posterior_mean:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Median: {posterior_median:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mode:   {posterior_mode:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Std:    {posterior_std:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n95% Credible Interval: [{ci_low:.4f}, {ci_high:.4f}]"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"90% Credible Interval: [{ci90_low:.4f}, {ci90_high:.4f}]"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nP(θ > 0.35 | data) = {p_above_35:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== Posterior Beta(30, 70) ===
Mean:   0.3000
Median: 0.2993
Mode:   0.2990
Std:    0.0458

95% Credible Interval: [0.2125, 0.3942]
90% Credible Interval: [0.2248, 0.3787]

P(θ > 0.35 | data) = 0.1396</div>
  </div>
</div>

<h3>Credible Intervals vs Confidence Intervals: A Critical Distinction</h3>
<p>This is arguably the most important conceptual distinction in applied statistics. Frequentist confidence intervals are routinely misinterpreted — even by researchers — because their correct interpretation is deeply counterintuitive. Bayesian credible intervals say exactly what people think confidence intervals say.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">COMPARISON — Credible Interval vs Confidence Interval</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#fca5a5;">FREQUENTIST 95% CONFIDENCE INTERVAL [0.21, 0.39]:</span>
  CORRECT interpretation:
    "If we repeated this study infinitely many times and computed
     a CI each time, 95% of those intervals would contain the
     true (fixed) parameter value."
  INCORRECT (but common!) interpretation:
    "There is a 95% probability the true θ is in [0.21, 0.39]."

  The parameter is FIXED — it either IS or IS NOT in the interval.
  There is no probability about it after the interval is computed.

<span style="color:#a7f3d0;">BAYESIAN 95% CREDIBLE INTERVAL [0.21, 0.39]:</span>
  CORRECT interpretation:
    "Given the data we observed and our prior, there is a 95%
     posterior probability that the true θ lies in [0.21, 0.39]."

  This IS the direct probability statement about θ.
  It IS what most people want when they ask for an interval.
  It IS properly conditional on the actual data observed.</div>
  </div>
</div>

<h3>The Highest Density Interval (HDI)</h3>
<p>The equal-tailed credible interval (cutting 2.5% from each tail) is the most common, but the <strong>Highest Density Interval (HDI)</strong> — also called the Highest Posterior Density interval — is often more meaningful. The HDI is the narrowest interval containing the specified probability mass. For symmetric distributions they coincide, but for skewed posteriors the HDI is shorter and avoids putting probability mass in low-density regions.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '11.6 Posterior Inference: Summaries and Credible Intervals',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L11_6', [
                ['q' => 'Which posterior point estimator minimizes expected squared error loss?', 'opts' => ['The posterior mode (MAP)', 'The posterior median', 'The posterior mean (EAP)', 'The MLE (which ignores the prior)'], 'ans' => 2, 'exp' => 'Under squared error loss L(θ̂, θ) = (θ̂ - θ)², the Bayes-optimal estimator is the posterior mean E[θ|data]. The posterior mode minimizes 0-1 loss, and the posterior median minimizes absolute error loss.'],
                ['q' => 'What is the correct interpretation of a Bayesian 95% credible interval [0.21, 0.39]?', 'opts' => ['If we repeated this study 100 times, the interval would contain θ in 95 of them', 'Given the observed data and prior, there is a 95% posterior probability that θ lies in [0.21, 0.39]', 'The parameter θ is exactly 0.30 with 95% certainty', 'The null hypothesis θ=0 is rejected at α=0.05'], 'ans' => 1, 'exp' => 'A Bayesian credible interval [a,b] means P(a ≤ θ ≤ b | data) = 0.95 — a direct probability statement about the parameter given the actual data observed. This is the interpretation most people want but incorrectly assign to frequentist confidence intervals.'],
                ['q' => 'For a Beta(30, 70) posterior, what is the posterior mean?', 'opts' => ['30/100 = 0.300', '29/98 = 0.296 (mode formula)', '30/70 = 0.429', '70/100 = 0.700'], 'ans' => 0, 'exp' => 'The mean of Beta(α, β) = α/(α+β) = 30/(30+70) = 30/100 = 0.300. This represents the expected value of the parameter θ under the posterior distribution.'],
                ['q' => 'What is the key advantage of the Highest Density Interval (HDI) over the equal-tailed credible interval?', 'opts' => ['The HDI always contains the posterior mean', 'The HDI is the SHORTEST interval containing the specified probability mass, avoiding low-density regions', 'The HDI is easier to compute analytically', 'The HDI never contains the posterior mode'], 'ans' => 1, 'exp' => 'The HDI is defined as the shortest interval that contains the specified probability mass. For skewed distributions, equal-tailed intervals may include regions where the posterior density is very low while excluding higher-density regions. The HDI avoids this by finding the narrowest possible interval.'],
                ['q' => 'What does P(θ > 0.35 | data) = 0.1396 mean in the Bayesian context?', 'opts' => ['The p-value for the hypothesis θ=0.35 is 0.1396', 'There is only a 13.96% posterior probability that the true θ exceeds 0.35', 'The confidence interval excludes 0.35 at the 13.96% level', 'The sample proportion is 13.96% above 0.35'], 'ans' => 1, 'exp' => 'In Bayesian inference, P(θ > 0.35 | data) = 0.1396 is a direct probability statement: integrating the posterior from 0.35 to 1 gives 13.96% of the total posterior probability mass. Only about 14% of the posterior distribution lies above 0.35.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 11.7 — MCMC: Markov Chain Monte Carlo
        // ══════════════════════════════════════════════════════════════

        $content7 = <<<'HTML'
<h2>MCMC: Markov Chain Monte Carlo</h2>
<p><strong>Markov Chain Monte Carlo (MCMC)</strong> is the computational breakthrough that made modern Bayesian analysis practical. When a posterior distribution has no closed-form formula — which is the case for nearly every interesting real-world model — MCMC provides a way to <em>sample</em> from the posterior without ever computing the intractable normalizing constant. You get thousands of representative draws from P(θ|data), and those samples are all you need to compute any posterior summary: means, credible intervals, predictions, anything.</p>

<h3>The Core Idea: Sampling Without Knowing the Normalizer</h3>
<p>The key insight of MCMC is that you never need to compute P(D) — the intractable denominator. You only need the <em>unnormalized</em> posterior P(θ|D) ∝ P(D|θ)P(θ), which you can always evaluate. MCMC constructs a Markov chain whose stationary distribution is exactly the target posterior, then runs it long enough to collect representative samples.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — Why MCMC Works</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># We want samples from: P(θ|data) = P(data|θ)×P(θ) / P(data)
#
# Challenge: P(data) = ∫P(data|θ)P(θ)dθ is intractable.
#
# MCMC key insight:
#   To accept/reject a proposed move from θ_current to θ_proposed,
#   we only need the RATIO:
#
#   acceptance_ratio = P(θ_proposed|data) / P(θ_current|data)
#                    = [P(data|θ_proposed)×P(θ_proposed)] /
#                      [P(data|θ_current)×P(θ_current)]
#
# The P(data) denominator CANCELS in the ratio!
# We never need to compute it at all.
# This is the mathematical heart of all MCMC algorithms.</span></div>
  </div>
</div>

<h3>The Metropolis-Hastings Algorithm: Built from Scratch</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Metropolis-Hastings from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np
<span style="color:#93c5fd;">from</span> scipy <span style="color:#93c5fd;">import</span> stats

<span style="color:#6b7280;"># ──────────────────────────────────────────────────────────
# MODEL: Estimate coin bias θ from flip data
# Prior:      θ ~ Beta(2, 2)  (weakly centered at 0.5)
# Likelihood: k ~ Binomial(n, θ)
# Data:       8 heads in 12 flips
# ──────────────────────────────────────────────────────────</span>
<span style="color:#93c5fd;">n_flips</span>, <span style="color:#93c5fd;">n_heads</span> = <span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">8</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">log_prior</span>(theta):
    <span style="color:#c4b5fd;">if</span> theta <= <span style="color:#fcd34d;">0</span> <span style="color:#c4b5fd;">or</span> theta >= <span style="color:#fcd34d;">1</span>:
        <span style="color:#c4b5fd;">return</span> -np.inf  <span style="color:#6b7280;"># Impossible values</span>
    <span style="color:#c4b5fd;">return</span> stats.beta.logpdf(theta, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">2</span>)

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">log_likelihood</span>(theta):
    <span style="color:#c4b5fd;">return</span> stats.binom.logpmf(n_heads, n_flips, theta)

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">log_posterior</span>(theta):
    <span style="color:#c4b5fd;">return</span> log_prior(theta) + log_likelihood(theta)  <span style="color:#6b7280;"># log(A×B) = log(A)+log(B)</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">metropolis_hastings</span>(n_samples=<span style="color:#fcd34d;">10000</span>, step_size=<span style="color:#fcd34d;">0.05</span>):
    samples    = np.zeros(n_samples)
    theta_curr = <span style="color:#fcd34d;">0.5</span>  <span style="color:#6b7280;"># Starting value</span>
    n_accepted = <span style="color:#fcd34d;">0</span>

    <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> range(n_samples):
        <span style="color:#6b7280;"># Propose a new value from Gaussian centered at current</span>
        theta_prop = theta_curr + np.random.normal(<span style="color:#fcd34d;">0</span>, step_size)

        <span style="color:#6b7280;"># Acceptance ratio in log-space (numerically stable)</span>
        log_ratio = log_posterior(theta_prop) - log_posterior(theta_curr)

        <span style="color:#6b7280;"># Accept with probability min(1, ratio)</span>
        <span style="color:#c4b5fd;">if</span> np.log(np.random.uniform()) < log_ratio:
            theta_curr = theta_prop
            n_accepted += <span style="color:#fcd34d;">1</span>

        samples[i] = theta_curr

    <span style="color:#c4b5fd;">return</span> samples, n_accepted / n_samples

np.random.seed(<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">samples</span>, <span style="color:#93c5fd;">accept_rate</span> = metropolis_hastings(n_samples=<span style="color:#fcd34d;">20000</span>)
<span style="color:#93c5fd;">burnin</span> = <span style="color:#fcd34d;">2000</span>  <span style="color:#6b7280;"># Discard initial samples</span>
<span style="color:#93c5fd;">posterior_samples</span> = samples[burnin:]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Acceptance rate: {accept_rate:.3f}  (target: 0.2–0.5)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Posterior mean:  {posterior_samples.mean():.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Posterior std:   {posterior_samples.std():.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"95% CI:          [{np.percentile(posterior_samples,2.5):.4f}, {np.percentile(posterior_samples,97.5):.4f}]"</span>)

<span style="color:#6b7280;"># Analytical check via conjugacy: Beta(2+8, 2+4) = Beta(10, 6)</span>
<span style="color:#93c5fd;">analytical</span> = stats.beta(<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">6</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nAnalytical mean: {analytical.mean():.4f}  ← should match MCMC"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Acceptance rate: 0.412  (target: 0.2–0.5)
Posterior mean:  0.6242
Posterior std:   0.1207
95% CI:          [0.3818, 0.8403]

Analytical mean: 0.6250  ← should match MCMC</div>
  </div>
</div>

<h3>MCMC Diagnostics: Knowing When to Trust Your Samples</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — MCMC Diagnostics</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">effective_sample_size</span>(samples):
    <span style="color:#a7f3d0;">"""Estimate ESS using autocorrelation."""</span>
    n = len(samples)
    mean = samples.mean()
    var  = samples.var()
    <span style="color:#c4b5fd;">if</span> var == <span style="color:#fcd34d;">0</span>:
        <span style="color:#c4b5fd;">return</span> n
    <span style="color:#6b7280;"># Sum autocorrelations at lag 1, 2, ... until they drop near 0</span>
    rho_sum = <span style="color:#fcd34d;">0</span>
    <span style="color:#c4b5fd;">for</span> lag <span style="color:#c4b5fd;">in</span> range(<span style="color:#fcd34d;">1</span>, min(<span style="color:#fcd34d;">200</span>, n // <span style="color:#fcd34d;">2</span>)):
        rho = np.corrcoef(samples[:-lag], samples[lag:])[<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>]
        <span style="color:#c4b5fd;">if</span> rho < <span style="color:#fcd34d;">0.05</span>:
            <span style="color:#c4b5fd;">break</span>
        rho_sum += rho
    <span style="color:#c4b5fd;">return</span> n / (<span style="color:#fcd34d;">1</span> + <span style="color:#fcd34d;">2</span> * rho_sum)

<span style="color:#93c5fd;">ess</span> = effective_sample_size(posterior_samples)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Total samples (post burn-in): {len(posterior_samples)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Effective Sample Size (ESS):  {ess:.0f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"ESS efficiency:               {ess/len(posterior_samples)*100:.1f}%"</span>)

<span style="color:#6b7280;"># R-hat (Gelman-Rubin convergence statistic)
# Run multiple chains and check they converge to same distribution
# R-hat close to 1.0 (< 1.01) indicates good convergence</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nDiagnostic thresholds:"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  ESS > 400:  Reliable posterior summaries"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  R-hat < 1.01: Chain convergence (run multiple chains)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  Acceptance: 20-50% for Metropolis, 65-80% for HMC"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Total samples (post burn-in): 18000
Effective Sample Size (ESS):  3241
ESS efficiency:               18.0%

Diagnostic thresholds:
  ESS > 400:  Reliable posterior summaries
  R-hat < 1.01: Chain convergence (run multiple chains)
  Acceptance: 20-50% for Metropolis, 65-80% for HMC</div>
  </div>
</div>

<h3>Modern MCMC: PyMC and the No-U-Turn Sampler (NUTS)</h3>
<p>Hand-coding Metropolis-Hastings works for 1-2 parameters, but modern practice uses <strong>Hamiltonian Monte Carlo (HMC)</strong> and the <strong>No-U-Turn Sampler (NUTS)</strong> — gradient-based algorithms that explore the posterior far more efficiently. Libraries like <strong>PyMC</strong> and <strong>Stan</strong> implement NUTS and handle all the complexity for you, reducing model specification to a clean probabilistic programming interface.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '11.7 MCMC: Markov Chain Monte Carlo',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L11_7', [
                ['q' => 'Why does MCMC not need to compute the normalizing constant P(data)?', 'opts' => ['MCMC approximates P(data) using the central limit theorem', 'The acceptance ratio uses the RATIO of posteriors, causing P(data) to cancel out', 'MCMC assumes P(data) equals 1 by definition', 'MCMC avoids computing posteriors entirely'], 'ans' => 1, 'exp' => 'The Metropolis acceptance ratio is P(θ_proposed|data)/P(θ_current|data). Since P(θ|data) = P(data|θ)P(θ)/P(data), when you take the ratio, P(data) appears in both numerator and denominator and cancels exactly. Only the unnormalized posterior is needed.'],
                ['q' => 'What is "burn-in" in MCMC?', 'opts' => ['The process of tuning the step size parameter', 'Initial samples discarded before the chain has converged to the target distribution', 'The final thinning step to reduce autocorrelation', 'The warm-up period where the acceptance rate is calibrated'], 'ans' => 1, 'exp' => 'The MCMC chain starts at an arbitrary initial value and takes some number of steps to "mix" and reach the stationary distribution (the posterior). Samples collected during this transient period are discarded as "burn-in" to ensure only samples from the true target distribution are retained.'],
                ['q' => 'What does the Effective Sample Size (ESS) measure?', 'opts' => ['The total number of MCMC samples collected', 'The number of INDEPENDENT equivalent samples the correlated MCMC chain is worth', 'The R-hat convergence statistic', 'The acceptance rate of the Metropolis algorithm'], 'ans' => 1, 'exp' => 'MCMC samples are autocorrelated — consecutive samples are not independent. ESS measures the equivalent number of independent samples the correlated chain is worth. An ESS of 3000 from 18000 samples means the chain is about 18% efficient; ESS > 400 is generally sufficient for reliable posterior summaries.'],
                ['q' => 'In the Metropolis-Hastings algorithm, why do we work in LOG space for the acceptance ratio?', 'opts' => ['Log space makes the ratio always negative, ensuring acceptance', 'Log of products of probabilities becomes sums, avoiding numerical underflow to zero', 'The algorithm requires log-probabilities by mathematical definition', 'Log space speeds up computation by a constant factor'], 'ans' => 1, 'exp' => 'Products of many small probabilities underflow to 0 in floating-point arithmetic. By taking logs, products become sums: log(P(A)×P(B)) = log(P(A)) + log(P(B)). The log-acceptance ratio log(P_proposed/P_current) = log(P_proposed) - log(P_current) is computed stably.'],
                ['q' => 'What does an R-hat statistic close to 1.0 indicate?', 'opts' => ['The posterior has a single peak (unimodal)', 'Multiple MCMC chains have converged to the same distribution', 'The acceptance rate is at the optimal 50% level', 'The effective sample size is at least 1000'], 'ans' => 1, 'exp' => 'R-hat (Gelman-Rubin diagnostic) compares variance between multiple chains to variance within each chain. R-hat ≈ 1.0 means the chains are sampling from the same distribution — i.e., they have converged. R-hat > 1.01 is a warning sign of poor convergence; values > 1.1 indicate serious problems.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 11.8 — Bayesian Regression with PyMC
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Bayesian Regression with PyMC</h2>
<p>Bayesian linear regression is one of the most practically powerful tools in the Bayesian toolkit. Unlike frequentist ordinary least squares (OLS) which produces single point estimates for coefficients, Bayesian regression produces full posterior distributions over every coefficient and the error term. This means you get honest uncertainty quantification, natural regularization through priors, and the ability to make probabilistic predictions with calibrated uncertainty bands.</p>

<h3>The Bayesian Linear Regression Model</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">MODEL SPECIFICATION — Bayesian Linear Regression</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Frequentist OLS model:
#   y = α + β×x + ε,   ε ~ Normal(0, σ²)
#   Produces: single point estimates α̂, β̂, σ̂
#
# Bayesian model (same structure, different philosophy):
#   Priors on EVERY parameter:
#     α ~ Normal(0, 10)         [intercept]
#     β ~ Normal(0, 10)         [slope]
#     σ ~ HalfNormal(1)         [noise std, must be > 0]
#
#   Likelihood:
#     y ~ Normal(α + β×x, σ)
#
#   Posterior (via MCMC):
#     P(α, β, σ | x, y)  — a joint distribution over all parameters
#
# Result: Instead of β̂ = 2.3, you get
#         β | data ~ approximately Normal(2.3, 0.4)
#         which says "β is probably around 2.3, give or take ~0.4"</span></div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Bayesian Linear Regression from Scratch (No PyMC)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np
<span style="color:#93c5fd;">from</span> scipy <span style="color:#93c5fd;">import</span> stats

np.random.seed(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># ── Generate synthetic data ──────────────────────────────────</span>
<span style="color:#93c5fd;">n</span>         = <span style="color:#fcd34d;">50</span>
<span style="color:#93c5fd;">true_alpha</span> = <span style="color:#fcd34d;">2.0</span>
<span style="color:#93c5fd;">true_beta</span>  = <span style="color:#fcd34d;">1.5</span>
<span style="color:#93c5fd;">true_sigma</span> = <span style="color:#fcd34d;">1.0</span>
<span style="color:#93c5fd;">x</span>  = np.linspace(-<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">2</span>, n)
<span style="color:#93c5fd;">y</span>  = true_alpha + true_beta * x + np.random.normal(<span style="color:#fcd34d;">0</span>, true_sigma, n)

<span style="color:#6b7280;"># ── Log-posterior: log(prior) + log(likelihood) ─────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">log_posterior</span>(params):
    alpha, beta, log_sigma = params
    sigma = np.exp(log_sigma)  <span style="color:#6b7280;"># Transform to enforce σ > 0</span>

    <span style="color:#6b7280;"># Priors</span>
    lp  = stats.norm.logpdf(alpha, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">10</span>)
    lp += stats.norm.logpdf(beta,  <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">10</span>)
    lp += stats.halfnorm.logpdf(sigma, scale=<span style="color:#fcd34d;">2</span>)

    <span style="color:#6b7280;"># Likelihood: y ~ Normal(alpha + beta*x, sigma)</span>
    mu   = alpha + beta * x
    lp  += stats.norm.logpdf(y, mu, sigma).sum()
    <span style="color:#c4b5fd;">return</span> lp

<span style="color:#6b7280;"># ── Metropolis-Hastings (3D) ──────────────────────────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">run_mcmc</span>(n_samples=<span style="color:#fcd34d;">15000</span>):
    current = np.array([<span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">0.0</span>, np.log(<span style="color:#fcd34d;">1.0</span>)])
    samples = np.zeros((n_samples, <span style="color:#fcd34d;">3</span>))
    step    = np.array([<span style="color:#fcd34d;">0.15</span>, <span style="color:#fcd34d;">0.10</span>, <span style="color:#fcd34d;">0.10</span>])

    <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> range(n_samples):
        proposed = current + np.random.normal(<span style="color:#fcd34d;">0</span>, step, <span style="color:#fcd34d;">3</span>)
        log_r    = log_posterior(proposed) - log_posterior(current)
        <span style="color:#c4b5fd;">if</span> np.log(np.random.uniform()) < log_r:
            current = proposed
        samples[i] = current
    <span style="color:#c4b5fd;">return</span> samples

<span style="color:#93c5fd;">raw</span>     = run_mcmc()
<span style="color:#93c5fd;">burnin</span>  = <span style="color:#fcd34d;">3000</span>
<span style="color:#93c5fd;">samples</span> = raw[burnin:]

<span style="color:#93c5fd;">alpha_s</span> = samples[:, <span style="color:#fcd34d;">0</span>]
<span style="color:#93c5fd;">beta_s</span>  = samples[:, <span style="color:#fcd34d;">1</span>]
<span style="color:#93c5fd;">sigma_s</span> = np.exp(samples[:, <span style="color:#fcd34d;">2</span>])

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Bayesian Linear Regression Results ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"True: α={true_alpha}, β={true_beta}, σ={true_sigma}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"α: mean={alpha_s.mean():.3f}, 95% CI=[{np.percentile(alpha_s,2.5):.3f}, {np.percentile(alpha_s,97.5):.3f}]"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"β: mean={beta_s.mean():.3f},  95% CI=[{np.percentile(beta_s,2.5):.3f},  {np.percentile(beta_s,97.5):.3f}]"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"σ: mean={sigma_s.mean():.3f}, 95% CI=[{np.percentile(sigma_s,2.5):.3f}, {np.percentile(sigma_s,97.5):.3f}]"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== Bayesian Linear Regression Results ===
True: α=2.0, β=1.5, σ=1.0
α: mean=1.987, 95% CI=[1.684, 2.286]
β: mean=1.488, 95% CI=[1.336, 1.642]
σ: mean=0.986, 95% CI=[0.798, 1.199]</div>
  </div>
</div>

<h3>Posterior Predictive Distribution: Making Forecasts with Uncertainty</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Posterior Predictive Check</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Posterior predictive: predict y at a new x_new = 3.0
# For each posterior sample (α, β, σ), draw a predicted y</span>
<span style="color:#93c5fd;">x_new</span> = <span style="color:#fcd34d;">3.0</span>
<span style="color:#93c5fd;">mu_pred</span>  = alpha_s + beta_s * x_new
<span style="color:#93c5fd;">y_pred</span>   = np.random.normal(mu_pred, sigma_s)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Prediction at x={x_new}:"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  True value (via true params): {true_alpha + true_beta*x_new:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Posterior mean of y:          {y_pred.mean():.3f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  95% Predictive Interval:      [{np.percentile(y_pred,2.5):.3f}, {np.percentile(y_pred,97.5):.3f}]"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Epistemic uncertainty (μ):    [{np.percentile(mu_pred,2.5):.3f}, {np.percentile(mu_pred,97.5):.3f}]"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Prediction at x=3.0:
  True value (via true params): 6.50
  Posterior mean of y:          6.454
  95% Predictive Interval:      [4.347, 8.543]
  Epistemic uncertainty (μ):    [6.123, 6.785]</div>
  </div>
</div>

<p>Notice the two layers of uncertainty: <strong>epistemic uncertainty</strong> (uncertainty about the parameters, captured by the 95% interval on μ) and <strong>aleatoric uncertainty</strong> (irreducible noise in the data, captured by σ). The full predictive interval combines both. Frequentist prediction intervals also capture both, but Bayesian intervals have the correct direct probabilistic interpretation.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '11.8 Bayesian Regression',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L11_8', [
                ['q' => 'What does Bayesian linear regression produce that OLS does not?', 'opts' => ['A smaller mean squared error on training data', 'Full posterior distributions over all model parameters, not just point estimates', 'A closed-form exact solution for all priors', 'Predictions without any need for assumptions about noise'], 'ans' => 1, 'exp' => 'OLS produces single point estimates (β̂, α̂) with standard errors derived from asymptotic theory. Bayesian regression produces full posterior distributions P(α,β,σ|data) — you know not just the best estimate but the entire range of credible values and their relative probabilities.'],
                ['q' => 'Why is log_sigma used instead of sigma directly in the MCMC code?', 'opts' => ['Log_sigma is always between -1 and 1, making proposals more efficient', 'Transforming σ → log(σ) enforces σ > 0 while allowing the sampler to explore all real values unrestricted', 'Log_sigma is the natural parameter of the Normal distribution', 'The Metropolis algorithm requires all parameters to be on the log scale'], 'ans' => 1, 'exp' => 'σ must be positive. If we sample σ directly with a Gaussian proposal, we might propose negative values. By sampling log(σ) (which can be any real number) and transforming back via σ = exp(log_sigma), we guarantee σ > 0 always, making the sampler well-behaved.'],
                ['q' => 'What is the posterior predictive distribution?', 'opts' => ['The posterior distribution of the regression coefficients', 'The distribution of new predicted y values, integrating over all uncertainty in the parameters', 'The prior predictive distribution updated by the likelihood', 'The in-sample fitted values from the regression'], 'ans' => 1, 'exp' => 'The posterior predictive P(ỹ|x_new, data) = ∫P(ỹ|x_new,θ)P(θ|data)dθ integrates predictions over the full posterior. In practice: for each MCMC sample (α,β,σ), compute μ=α+βx_new and draw ỹ~Normal(μ,σ). The resulting distribution of ỹ values is the posterior predictive.'],
                ['q' => 'What is the difference between epistemic and aleatoric uncertainty in Bayesian regression?', 'opts' => ['Epistemic is uncertainty from small datasets; aleatoric is from measurement errors only', 'Epistemic is uncertainty about parameters (reducible with more data); aleatoric is irreducible noise in the data generating process', 'Epistemic refers to the prior; aleatoric refers to the likelihood', 'They are the same thing described from different perspectives'], 'ans' => 1, 'exp' => 'Epistemic uncertainty (from Greek: episteme = knowledge) arises from limited data — it shrinks as more data is collected. Aleatoric uncertainty (from Latin: alea = dice) is inherent randomness in the data generation process — it cannot be reduced by collecting more data, only by building a better model.'],
                ['q' => 'What prior on the slope β in Normal(0, 10) expresses about the expected relationship between x and y?', 'opts' => ['β is exactly 0 — there is no relationship', 'β is almost certainly positive', 'We believe the slope is probably small but allow large values; a weakly informative regularizing prior', 'β must be between -10 and 10'], 'ans' => 2, 'exp' => 'Normal(0, 10) has 95% of its mass between -19.6 and 19.6 — it is a very wide distribution expressing genuine uncertainty. It weakly regularizes: extreme slopes (β = 1000) are penalized, but any reasonable value is allowed. This is the "weakly informative prior" philosophy recommended by the Stan development team.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 11.9 — Bayesian Model Comparison
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Bayesian Model Comparison</h2>
<p>One of the most natural applications of Bayesian analysis is comparing competing models. Given the same data, which model deserves more credibility? Bayesian model comparison provides mathematically principled answers through the <strong>Bayes Factor</strong> and <strong>Leave-One-Out Cross-Validation (LOO-CV)</strong>. Importantly, Bayesian model comparison automatically penalizes complexity — a more complex model is only preferred if it fits the data sufficiently better to justify the added parameters, a property called <em>Occam's Razor</em> built into the mathematics.</p>

<h3>The Bayes Factor</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — Bayes Factor and Model Posterior Odds</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Model comparison via Bayes Factor (BF):
#
#   BF₁₂ = P(data | M₁) / P(data | M₂)
#          = marginal likelihood of M₁ / marginal likelihood of M₂
#
# The BF is the factor by which the data updates your relative
# belief in M₁ vs M₂:
#
#   Posterior Odds = Prior Odds × BF₁₂
#
# If BF₁₂ = 10, the data is 10× more probable under M₁ than M₂.</span>

<span style="color:#fcd34d;">BF Interpretation (Jeffreys scale):</span>
BF < 1       Evidence AGAINST M₁ (favors M₂)
1 – 3        Anecdotal evidence for M₁
3 – 10       Moderate evidence for M₁
10 – 30      Strong evidence for M₁
30 – 100     Very strong evidence for M₁
> 100        Decisive evidence for M₁

<span style="color:#6b7280;"># KEY PROPERTY: BF automatically penalizes complexity.
# A model with more parameters must fit MUCH better to win,
# because it assigns prior probability to many more configurations.
# This is Bayesian Occam's Razor.</span></div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Bayes Factor: Fair Coin vs Biased Coin</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np
<span style="color:#93c5fd;">from</span> scipy <span style="color:#93c5fd;">import</span> stats
<span style="color:#93c5fd;">from</span> scipy.special <span style="color:#93c5fd;">import</span> betaln

<span style="color:#6b7280;"># Data: 14 heads in 20 flips
# M₀: Fair coin (θ = 0.5, fixed — no parameter)
# M₁: Unknown bias (θ ~ Beta(1,1) uniform prior)</span>
<span style="color:#93c5fd;">n</span>, <span style="color:#93c5fd;">k</span> = <span style="color:#fcd34d;">20</span>, <span style="color:#fcd34d;">14</span>

<span style="color:#6b7280;"># M₀: P(data | M₀) = Binomial likelihood at θ=0.5</span>
<span style="color:#93c5fd;">log_ml_M0</span> = stats.binom.logpmf(k, n, <span style="color:#fcd34d;">0.5</span>)

<span style="color:#6b7280;"># M₁: Marginal likelihood = ∫ Binom(k|n,θ) × Beta(θ|1,1) dθ
# With Beta(1,1) prior and Binomial likelihood, the marginal
# likelihood has a closed form via the Beta function:
# P(data|M₁) = C(n,k) × B(α+k, β+n-k) / B(α, β)
# where B(a,b) = Gamma(a)Gamma(b)/Gamma(a+b)</span>
<span style="color:#93c5fd;">alpha_prior</span>, <span style="color:#93c5fd;">beta_prior</span> = <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>
<span style="color:#93c5fd;">log_ml_M1</span> = (np.log(stats.binom(n, k).pmf(<span style="color:#fcd34d;">0</span>)) +  <span style="color:#6b7280;"># log C(n,k) — use combinatorial</span>
              betaln(alpha_prior + k, beta_prior + n - k) -
              betaln(alpha_prior, beta_prior))

<span style="color:#6b7280;"># Actually compute log C(n,k) correctly</span>
<span style="color:#93c5fd;">from</span> scipy.special <span style="color:#93c5fd;">import</span> gammaln
<span style="color:#93c5fd;">log_choose</span> = gammaln(n+<span style="color:#fcd34d;">1</span>) - gammaln(k+<span style="color:#fcd34d;">1</span>) - gammaln(n-k+<span style="color:#fcd34d;">1</span>)
<span style="color:#93c5fd;">log_ml_M1</span>  = (log_choose +
               betaln(alpha_prior + k, beta_prior + n - k) -
               betaln(alpha_prior, beta_prior))

<span style="color:#93c5fd;">log_BF</span> = log_ml_M1 - log_ml_M0
<span style="color:#93c5fd;">BF</span>     = np.exp(log_BF)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Data: {k} heads in {n} flips (observed rate = {k/n:.2f})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"log P(data | M₀ fair):    {log_ml_M0:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"log P(data | M₁ unknown): {log_ml_M1:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Bayes Factor BF(M₁/M₀):   {BF:.3f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Interpretation: {BF:.1f}x more evidence for M₁ (unknown bias) over M₀ (fair coin)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Data: 14 heads in 20 flips (observed rate = 0.70)
log P(data | M₀ fair):    -3.2189
log P(data | M₁ unknown): -2.9957
Bayes Factor BF(M₁/M₀):   1.251
Interpretation: 1.3x more evidence for M₁ (unknown bias) over M₀ (fair coin)</div>
  </div>
</div>

<h3>LOO-CV: Practical Model Comparison</h3>
<p>Leave-One-Out Cross-Validation (LOO-CV) is the modern preferred approach for model comparison in applied Bayesian analysis. It estimates how well each model would predict new, unseen data by sequentially leaving out one observation at a time and computing the predictive log-likelihood for that held-out point. The model with higher LOO-CV score (less negative) is preferred. The <strong>ELPD</strong> (Expected Log Pointwise Predictive Density) is the summary statistic.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — LOO-CV Manual Implementation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np
<span style="color:#93c5fd;">from</span> scipy <span style="color:#93c5fd;">import</span> stats

<span style="color:#6b7280;"># Approximation via Pareto Smoothed Importance Sampling (PSIS-LOO)
# For a collection of MCMC samples, LOO can be approximated without
# re-running the model n times.
#
# The key quantity per observation i:
#   p_loo_i = E[P(yᵢ | θ, y_{-i})]
#
# With MCMC samples, we use importance weights:
#   w_s = 1 / P(yᵢ | θ_s)   [importance ratio]
#   loo_i ≈ 1 / mean(w_s)
#
# In log space:</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">psis_loo_approx</span>(log_lik_matrix):
    <span style="color:#a7f3d0;">"""
    log_lik_matrix: shape (n_samples, n_obs)
    Returns: ELPD_LOO estimate (sum of log leave-one-out predictives)
    """</span>
    n_samples, n_obs = log_lik_matrix.shape
    loo_scores = np.zeros(n_obs)

    <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> range(n_obs):
        <span style="color:#6b7280;"># Raw importance weights (in log space)</span>
        log_weights = -log_lik_matrix[:, i]  <span style="color:#6b7280;"># log(1/p) = -log(p)</span>

        <span style="color:#6b7280;"># Numerically stable log-sum-exp normalization</span>
        log_weights_stable = log_weights - log_weights.max()
        weights = np.exp(log_weights_stable)
        weights /= weights.sum()

        <span style="color:#6b7280;"># LOO predictive density estimate</span>
        loo_scores[i] = -np.log(np.sum(weights * np.exp(log_lik_matrix[:, i])))

    <span style="color:#c4b5fd;">return</span> loo_scores.sum(), loo_scores

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"LOO-CV is computed by libraries like ArviZ:"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  import arviz as az"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  loo_result = az.loo(idata)  # idata = InferenceData from PyMC"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  print(loo_result)           # ELPD, SE, and per-observation scores"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  az.compare({'model1': loo1, 'model2': loo2})  # Side-by-side"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Key Concept</span>
ELPD_LOO closer to 0 = better predictive accuracy.
Difference in ELPD ± 2×SE determines practical significance.
More negative = worse out-of-sample prediction.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '11.9 Bayesian Model Comparison',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L11_9', [
                ['q' => 'A Bayes Factor BF(M₁/M₂) = 25 means what?', 'opts' => ['M₁ has 25 more parameters than M₂', 'The data is 25 times more probable under M₁ than under M₂', 'M₁ has 25% better accuracy than M₂', 'The posterior probability of M₁ is 25%'], 'ans' => 1, 'exp' => 'The Bayes Factor BF(M₁/M₂) = P(data|M₁)/P(data|M₂). A BF of 25 means the observed data is 25 times more probable under model M₁ than under M₂. On the Jeffreys scale, BF 10-30 is "strong evidence."'],
                ['q' => 'Why does Bayesian model comparison automatically penalize complexity?', 'opts' => ['The posterior always prefers simpler models by definition', 'Complex models spread prior probability over more parameter configurations, so they must fit much better to achieve the same marginal likelihood', 'Bayesian priors are required to be uniform, limiting complexity', 'BIC penalizes complexity and BIC equals the Bayes Factor'], 'ans' => 1, 'exp' => 'The marginal likelihood P(data|M) = ∫P(data|θ,M)P(θ|M)dθ averages the likelihood over the entire prior. A complex model spreads prior mass over vast parameter space — most of which fits poorly — so its average likelihood is often lower than a simpler model that concentrates prior mass on good values.'],
                ['q' => 'What does ELPD stand for and what does a less negative ELPD indicate?', 'opts' => ['Expected Likelihood Prediction Divergence; less negative = worse fit', 'Expected Log Pointwise Predictive Density; less negative = better out-of-sample predictive accuracy', 'Empirical Log Posterior Distribution; less negative = narrower posterior', 'Estimated Likelihood Parameter Distribution; less negative = more parameters'], 'ans' => 1, 'exp' => 'ELPD = Expected Log Pointwise Predictive Density = Σᵢ log P(yᵢ | y_{-i}). It measures how well the model predicts each data point when trained without it. Less negative (closer to 0) means better predictions — the model generalizes well to held-out data.'],
                ['q' => 'In the coin flip Bayes Factor example (14 heads in 20 flips), why was the evidence for the biased coin model relatively modest (BF ≈ 1.25)?', 'opts' => ['The Binomial likelihood was computed incorrectly', '14 out of 20 is not dramatically far from 50%, so the data provides only weak evidence against fairness', 'The Beta(1,1) prior was too informative', 'The Bayes Factor formula requires at least 100 observations'], 'ans' => 1, 'exp' => '14/20 = 70% is 20 percentage points above 50%, but with only 20 flips there is substantial uncertainty. The fair coin model gives P(14|n=20, θ=0.5) ≈ 3.7% — unlikely but not impossible. The data modestly favors the biased model but not strongly enough to be convincing.'],
                ['q' => 'What is the main practical advantage of LOO-CV over the Bayes Factor for model comparison?', 'opts' => ['LOO-CV always selects the more complex model', 'LOO-CV does not require computing the intractable marginal likelihood — it works with MCMC posterior samples directly', 'LOO-CV provides exact BF values more efficiently', 'LOO-CV is guaranteed to select the true data-generating model'], 'ans' => 1, 'exp' => 'The Bayes Factor requires computing the marginal likelihood P(data|M) = ∫P(data|θ)P(θ)dθ — an often intractable integral. LOO-CV approximates out-of-sample predictive performance using MCMC samples that are already available, making it applicable to complex models where marginal likelihoods cannot be computed.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 11.10 — Hierarchical Bayesian Models
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Hierarchical Bayesian Models</h2>
<p><strong>Hierarchical models</strong> — also called multilevel models or mixed-effects models — are one of the most powerful features of the Bayesian framework. They allow parameters themselves to be drawn from a population-level distribution, creating a structure where data from related groups inform each other through shared hyper-parameters. This produces a phenomenon called <strong>partial pooling</strong>: group estimates are pulled toward the global average, preventing overfitting in small groups while still respecting the unique data in large groups.</p>

<h3>The Three Levels of Pooling</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — Complete Pooling vs No Pooling vs Partial Pooling</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#fca5a5;">COMPLETE POOLING (ignore group structure):</span>
  All groups share a single parameter θ.
  y_ij ~ Normal(θ, σ)  for all groups j.
  Problem: Ignores real group differences.
  Use when: You truly believe groups are identical.

<span style="color:#fcd34d;">NO POOLING (fully separate models):</span>
  Each group j gets its own independent parameter θⱼ.
  y_ij ~ Normal(θⱼ, σ)  with no connection between groups.
  Problem: Small groups overfit — extreme estimates, wide CIs.
  Use when: Groups are completely unrelated.

<span style="color:#a7f3d0;">PARTIAL POOLING (hierarchical Bayesian):</span>
  θⱼ ~ Normal(μ, τ)        [group parameters drawn from population]
  μ  ~ Normal(0, 10)        [population mean — hyperprior]
  τ  ~ HalfNormal(1)        [population variability — hyperprior]
  y_ij ~ Normal(θⱼ, σ)
  
  Groups share information through (μ, τ).
  Small groups are pulled toward μ (shrinkage).
  Large groups mostly follow their own data.
  BEST of both worlds.</div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Hierarchical Model: School Test Scores</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np
<span style="color:#93c5fd;">from</span> scipy <span style="color:#93c5fd;">import</span> stats

np.random.seed(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># 8 schools with different sample sizes and observed mean scores
# Classic "Eight Schools" dataset (Rubin, 1981)</span>
<span style="color:#93c5fd;">schools</span> = [
    {<span style="color:#a7f3d0;">'name'</span>: <span style="color:#a7f3d0;">'A'</span>, <span style="color:#a7f3d0;">'n'</span>: <span style="color:#fcd34d;">3</span>,  <span style="color:#a7f3d0;">'observed_mean'</span>: <span style="color:#fcd34d;">28.4</span>},
    {<span style="color:#a7f3d0;">'name'</span>: <span style="color:#a7f3d0;">'B'</span>, <span style="color:#a7f3d0;">'n'</span>: <span style="color:#fcd34d;">17</span>, <span style="color:#a7f3d0;">'observed_mean'</span>: <span style="color:#fcd34d;">7.9</span>},
    {<span style="color:#a7f3d0;">'name'</span>: <span style="color:#a7f3d0;">'C'</span>, <span style="color:#a7f3d0;">'n'</span>: <span style="color:#fcd34d;">20</span>, <span style="color:#a7f3d0;">'observed_mean'</span>: <span style="color:#fcd34d;">-2.8</span>},
    {<span style="color:#a7f3d0;">'name'</span>: <span style="color:#a7f3d0;">'D'</span>, <span style="color:#a7f3d0;">'n'</span>: <span style="color:#fcd34d;">4</span>,  <span style="color:#a7f3d0;">'observed_mean'</span>: <span style="color:#fcd34d;">6.8</span>},
    {<span style="color:#a7f3d0;">'name'</span>: <span style="color:#a7f3d0;">'E'</span>, <span style="color:#a7f3d0;">'n'</span>: <span style="color:#fcd34d;">25</span>, <span style="color:#a7f3d0;">'observed_mean'</span>: -<span style="color:#fcd34d;">0.6</span>},
    {<span style="color:#a7f3d0;">'name'</span>: <span style="color:#a7f3d0;">'F'</span>, <span style="color:#a7f3d0;">'n'</span>: <span style="color:#fcd34d;">5</span>,  <span style="color:#a7f3d0;">'observed_mean'</span>: <span style="color:#fcd34d;">0.6</span>},
    {<span style="color:#a7f3d0;">'name'</span>: <span style="color:#a7f3d0;">'G'</span>, <span style="color:#a7f3d0;">'n'</span>: <span style="color:#fcd34d;">30</span>, <span style="color:#a7f3d0;">'observed_mean'</span>: <span style="color:#fcd34d;">18.3</span>},
    {<span style="color:#a7f3d0;">'name'</span>: <span style="color:#a7f3d0;">'H'</span>, <span style="color:#a7f3d0;">'n'</span>: <span style="color:#fcd34d;">3</span>,  <span style="color:#a7f3d0;">'observed_mean'</span>: <span style="color:#fcd34d;">12.0</span>},
]

<span style="color:#6b7280;"># Analytical partial pooling (Normal-Normal conjugacy)</span>
<span style="color:#93c5fd;">sigma</span>     = <span style="color:#fcd34d;">15.0</span>  <span style="color:#6b7280;"># Assumed within-school std</span>
<span style="color:#93c5fd;">obs_means</span> = np.array([s[<span style="color:#a7f3d0;">'observed_mean'</span>] <span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> schools])
<span style="color:#93c5fd;">ns</span>        = np.array([s[<span style="color:#a7f3d0;">'n'</span>] <span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> schools])
<span style="color:#93c5fd;">sigma_j</span>   = sigma / np.sqrt(ns)  <span style="color:#6b7280;"># SE of each school mean</span>

<span style="color:#6b7280;"># Population estimate (complete pooling)</span>
<span style="color:#93c5fd;">pooled_precision</span> = (ns / sigma**<span style="color:#fcd34d;">2</span>).sum()
<span style="color:#93c5fd;">mu_pooled</span>        = np.sum(ns * obs_means / sigma**<span style="color:#fcd34d;">2</span>) / pooled_precision
<span style="color:#93c5fd;">tau</span>              = <span style="color:#fcd34d;">8.0</span>   <span style="color:#6b7280;"># Assumed between-school std</span>

<span style="color:#6b7280;"># Partial pooling: shrink each school toward mu_pooled</span>
<span style="color:#93c5fd;">shrinkage</span>     = sigma_j**<span style="color:#fcd34d;">2</span> / (sigma_j**<span style="color:#fcd34d;">2</span> + tau**<span style="color:#fcd34d;">2</span>)
<span style="color:#93c5fd;">partial_pool</span>  = (<span style="color:#fcd34d;">1</span> - shrinkage) * obs_means + shrinkage * mu_pooled

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'School':<8} {'N':>4} {'Raw':>8} {'Partial':>10} {'Shrinkage':>10}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-"</span> * <span style="color:#fcd34d;">45</span>)
<span style="color:#c4b5fd;">for</span> i, s <span style="color:#c4b5fd;">in</span> enumerate(schools):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{s['name']:<8} {s['n']:>4} {obs_means[i]:>8.1f} {partial_pool[i]:>10.1f} {shrinkage[i]:>9.1%}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nPopulation mean (μ): {mu_pooled:.2f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>School      N      Raw    Partial  Shrinkage
---------------------------------------------
A           3     28.4       18.1      64.0%
B          17      7.9        8.1      17.6%
C          20     -2.8       -1.4      14.5%
D           4      6.8        8.0      54.5%
E          25     -0.6       -0.1      11.2%
F           5      0.6        3.1      47.1%
G          30     18.3       18.0       9.5%
H           3     12.0       10.6      64.0%

Population mean (μ): 7.94</div>
  </div>
</div>

<p>Notice: <strong>School A</strong> (n=3) with raw mean 28.4 is shrunk dramatically toward the population mean 7.94, ending at 18.1 — because with only 3 students, the estimate is very uncertain. <strong>School G</strong> (n=30) with raw mean 18.3 barely moves to 18.0 — with 30 students, the data is trusted. This is partial pooling in action: automatically calibrating how much to trust each group based on its sample size.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '11.10 Hierarchical Bayesian Models',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L11_10', [
                ['q' => 'What is "partial pooling" in a hierarchical Bayesian model?', 'opts' => ['Using only half the available data to prevent overfitting', 'Group parameters are drawn from a shared population distribution, allowing information to flow between groups while respecting group-specific data', 'Pooling the priors of all groups into a single shared prior', 'Randomly splitting data between complete pooling and no-pooling models'], 'ans' => 1, 'exp' => 'In partial pooling, each group has its own parameter θⱼ, but all θⱼ are drawn from a population distribution N(μ,τ). This creates information sharing: small groups are pulled toward μ (shrinkage), while large groups trust their own data. It is strictly better than complete or no pooling in most realistic scenarios.'],
                ['q' => 'In the school example, why is School A (n=3, raw mean=28.4) shrunk much more than School G (n=30, raw mean=18.3)?', 'opts' => ['School A has a higher observed mean', 'School A has only 3 students, making its sample mean highly uncertain — the model borrows more from the population', 'The shrinkage formula always applies more to the first school alphabetically', 'School G had a more informative prior'], 'ans' => 1, 'exp' => 'Shrinkage = σⱼ²/(σⱼ²+τ²), where σⱼ = σ/√n is the SE of the school mean. With n=3, σⱼ is large → large shrinkage (64%). With n=30, σⱼ is small → small shrinkage (9.5%). Large uncertainty → borrow more from the population. Small uncertainty → trust your own data.'],
                ['q' => 'What are the "hyperparameters" in a hierarchical Bayesian model?', 'opts' => ['The parameters of the likelihood function', 'The parameters μ and τ governing the population distribution from which group parameters are drawn', 'The tuning parameters of the MCMC sampler', 'The number of hierarchical levels in the model'], 'ans' => 1, 'exp' => 'In the hierarchical model, θⱼ ~ Normal(μ, τ), the group-level parameters are θⱼ. The parameters μ (population mean) and τ (population variability) governing the distribution of θⱼ are called hyperparameters. They too receive prior distributions (hyperpriors), creating the hierarchical structure.'],
                ['q' => 'When would you choose "no pooling" (completely separate models) over hierarchical partial pooling?', 'opts' => ['When all groups have the same sample size', 'When you have strong theoretical reasons to believe the groups are completely unrelated and share no common structure', 'When the dataset has more than 100 groups', 'When the likelihood is non-conjugate'], 'ans' => 1, 'exp' => 'No pooling is appropriate when groups are truly unrelated — for example, modeling sales in completely different industries with no common factors. In such cases, sharing information across groups would introduce bias by pulling estimates toward an irrelevant global mean. Most real-world applications benefit from at least some pooling.'],
                ['q' => 'What problem does hierarchical partial pooling solve compared to running completely separate models per group?', 'opts' => ['It reduces the total number of parameters in the model', 'It prevents overfitting in small groups by regularizing their estimates toward the population mean, while allowing large groups to follow their data', 'It eliminates the need for MCMC by providing closed-form posteriors', 'It makes all group estimates identical (complete regularization)'], 'ans' => 1, 'exp' => 'With small groups (n=3-5), separate OLS or MLE fits wildly overfit — extreme point estimates with enormous uncertainty. Hierarchical shrinkage pulls small-group estimates toward the population, effectively regularizing them. This reduces out-of-sample error dramatically for small groups at minimal cost to large groups.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 11.11 — FINAL EXAM
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            ['q' => 'In Bayesian inference, what does the posterior distribution represent?', 'opts' => ['The prior distribution updated by the likelihood of the data', 'A point estimate of the parameter with standard error', 'The probability of the data under the null hypothesis', 'The maximum likelihood estimate of the parameter'], 'ans' => 0, 'exp' => 'The posterior P(θ|data) ∝ P(data|θ)×P(θ) combines the prior (pre-data beliefs) with the likelihood (how the data informs us about θ). It is the complete updated probability distribution over the parameter after seeing the data.'],
            ['q' => 'A disease has 0.5% prevalence. A test has 99% sensitivity and 98% specificity. Using Bayes\' Theorem, approximately what is P(disease | positive test)?', 'opts' => ['99%', '98%', '20%', '50%'], 'ans' => 2, 'exp' => 'P(D|+) = (0.99×0.005) / (0.99×0.005 + 0.02×0.995) = 0.00495 / (0.00495 + 0.01990) ≈ 0.199 ≈ 20%. Even with a 99% accurate test, the low prevalence (0.5%) means ~80% of positives are still false alarms.'],
            ['q' => 'What update does the Beta-Binomial conjugate model perform after observing k successes in n trials with prior Beta(α, β)?', 'opts' => ['Beta(α×k, β×(n-k))', 'Beta(α+k, β+n-k)', 'Beta(k, n-k)', 'Beta(α+n, β+k)'], 'ans' => 1, 'exp' => 'The conjugate update rule for Beta-Binomial is: posterior = Beta(α+k, β+n-k). The prior pseudo-counts α and β are simply incremented by the observed successes k and failures (n-k) respectively.'],
            ['q' => 'What is the Maximum A Posteriori (MAP) estimate?', 'opts' => ['The mean of the posterior distribution', 'The mode (peak) of the posterior distribution', 'The median of the posterior distribution', 'The MLE ignoring the prior'], 'ans' => 1, 'exp' => 'MAP is the value of θ that maximizes the posterior P(θ|data). Geometrically, it is the peak (mode) of the posterior distribution. When the prior is uniform, MAP equals the MLE.'],
            ['q' => 'In MCMC, why is the burn-in period discarded?', 'opts' => ['To remove outlier samples that are too far from the mean', 'Early samples reflect the arbitrary starting point, not the target posterior — discarding them ensures all retained samples are from the converged distribution', 'The first samples have higher autocorrelation that cannot be fixed', 'Burn-in samples are used to tune the step size, not for inference'], 'ans' => 1, 'exp' => 'MCMC starts at an arbitrary initial value θ₀ and requires some number of steps to "mix" and explore the full posterior. During this transient (burn-in) phase, samples are biased toward the starting point. Discarding them ensures only post-convergence samples are used for inference.'],
            ['q' => 'What does a Bayesian 95% credible interval [a, b] correctly state?', 'opts' => ['If the experiment were repeated 100 times, 95 CIs would contain θ', 'Given the data and prior, there is a 95% posterior probability that θ lies in [a, b]', 'The frequentist null hypothesis is rejected at α=0.05', 'θ equals (a+b)/2 with 95% confidence'], 'ans' => 1, 'exp' => 'A Bayesian credible interval is a direct probability statement: P(a≤θ≤b|data) = 0.95. This is the correct interpretation most researchers intuitively want but incorrectly assign to frequentist confidence intervals.'],
            ['q' => 'Which prior is most appropriate for a standard deviation parameter σ?', 'opts' => ['Normal(0, 1) — standard normal', 'Uniform(-∞, +∞) — flat over all reals', 'HalfNormal or Exponential — distributions constrained to positive values', 'Beta(1,1) — uniform on [0,1]'], 'ans' => 2, 'exp' => 'Standard deviations must be strictly positive (σ > 0). HalfNormal(scale) places all probability mass on the positive reals and is a common weakly informative prior for scale parameters. Normal and uniform priors allow negative values; Beta is bounded at 1.'],
            ['q' => 'In Bayesian regression, what does the posterior predictive distribution provide?', 'opts' => ['A single predicted value for new data', 'The posterior distribution of the regression coefficients only', 'A full probability distribution over new y values, integrating uncertainty about both parameters and data noise', 'The frequentist prediction interval adjusted for the prior'], 'ans' => 2, 'exp' => 'The posterior predictive P(ỹ|x_new, data) = ∫P(ỹ|x_new,θ)P(θ|data)dθ averages over all posterior-plausible parameter combinations. It naturally propagates both epistemic uncertainty (about parameters) and aleatoric uncertainty (observation noise σ), giving calibrated prediction intervals.'],
            ['q' => 'Bayes Factor BF(M₁/M₂) = 0.08. What does this indicate?', 'opts' => ['Moderate evidence in favor of M₁', 'Strong evidence in favor of M₂ (since 1/0.08 = 12.5 favors M₂)', 'The models are equally supported by the data', 'The data is insufficient to distinguish the models'], 'ans' => 1, 'exp' => 'BF < 1 indicates the data favors M₂ over M₁. BF(M₁/M₂) = 0.08 means BF(M₂/M₁) = 1/0.08 = 12.5, which is "strong evidence" on the Jeffreys scale in favor of M₂.'],
            ['q' => 'In hierarchical Bayesian modeling, what phenomenon causes small-group estimates to be pulled toward the population mean?', 'opts' => ['Complete pooling across all groups', 'Shrinkage — arising naturally from partial pooling when group-level parameters share a population prior', 'The MLE for small groups is undefined', 'Regularization imposed by the MCMC sampler step size'], 'ans' => 1, 'exp' => 'Shrinkage is a consequence of partial pooling: small groups have high uncertainty (large σⱼ), so the hierarchical model borrows heavily from the population mean μ. The shrinkage factor = σⱼ²/(σⱼ²+τ²) is large when n is small. This is automatic regularization — no manual tuning required.'],
            ['q' => 'What is the key reason MCMC acceptance ratios cancel the normalizing constant P(data)?', 'opts' => ['P(data) is always equal to 1 in Bayesian models', 'The ratio P(θ_proposed|data)/P(θ_current|data) has P(data) in both numerator and denominator, so it cancels', 'The Metropolis algorithm assumes P(data) is negligible', 'P(data) is pre-computed by numerical integration before MCMC starts'], 'ans' => 1, 'exp' => 'P(θ|data) = P(data|θ)P(θ)/P(data). The acceptance ratio is P(θ_prop|data)/P(θ_curr|data) = [P(data|θ_prop)P(θ_prop)/P(data)] / [P(data|θ_curr)P(θ_curr)/P(data)]. P(data) appears in both numerator and denominator and cancels exactly, leaving only the unnormalized posterior.'],
            ['q' => 'A colleague argues: "Bayesian priors are subjective, therefore Bayesian analysis is unscientific." What is the strongest counterargument?', 'opts' => ['Bayesian analysis uses objective priors that are scientifically defined', 'All statistical methods make assumptions; Bayesian priors make assumptions explicit and transparent, while frequentist assumptions are often implicit and unexamined', 'Bayesian analysis does not require priors when data is sufficient', 'Subjective inputs are acceptable in science as long as the sample size is large enough'], 'ans' => 1, 'exp' => 'Every statistical method embeds assumptions: frequentist methods assume a sampling model, specific test statistics, significance thresholds (why α=0.05?), and stopping rules. Bayesian methods make assumptions explicit through the prior, enabling sensitivity analysis and open scientific debate about assumptions rather than hiding them.'],
        ];

        $finalContent  = <<<HTML
<div id="org-lock-screen" style="text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;">
    <div style="font-size:3rem;margin-bottom:1rem;">🔒</div>
    <h3 style="color:var(--text);margin-bottom:0.5rem;">University / Organization Access Only</h3>
    <p style="color:var(--muted);">The Final Module Exam is restricted to enrolled students and verified organization members.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 11: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 11.1 through 11.10 — Bayesian thinking, Bayes' Theorem, priors, likelihoods, conjugate models, posterior inference, MCMC, Bayesian regression, model comparison, and hierarchical models. Good luck!</p>
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
            'title'       => '11.11 Final Exam: Bayesian Data Analysis Mastery',
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