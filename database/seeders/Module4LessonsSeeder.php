<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module4LessonsSeeder
 * Seeds lessons for Module 4: Mathematical Analysis I.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module4LessonsSeeder
 */
class Module4LessonsSeeder extends Seeder
{
    public function run()
    {
        $mathModule = Module::where('order_index', 4)->firstOrFail();
        Lesson::where('module_id', $mathModule->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 4.1 — Real Numbers, Absolute Value & the Number Line
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>Real Numbers & the Number Line</h2>
<p>Mathematical Analysis — sometimes called <strong>Real Analysis</strong> or <strong>Calculus I</strong> at the university level — is the rigorous study of real numbers, limits, continuity, differentiation, and integration. Before we can speak meaningfully about limits or derivatives, we must have an absolutely solid understanding of the <em>real number system</em> itself: what numbers exist, how they are ordered, and how we measure distance between them.</p>

<h3>The Hierarchy of Number Sets</h3>
<p>The real numbers ℝ are built from progressively richer number sets. Each set extends the previous one to solve limitations — for example, the integers extend the naturals so that subtraction is always possible, and the rationals extend the integers so that division is always possible (except by zero).</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — The Number Hierarchy</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2;">
      <div style="margin-bottom:8px;"><span style="color:#a7f3d0;">ℕ</span> = {1, 2, 3, 4, ...}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Natural Numbers (counting numbers)</span></div>
      <div style="margin-bottom:8px;"><span style="color:#93c5fd;">ℤ</span> = {..., −2, −1, 0, 1, 2, ...}  &nbsp;<span style="color:#6b7280;">← Integers (naturals + zero + negatives)</span></div>
      <div style="margin-bottom:8px;"><span style="color:#fcd34d;">ℚ</span> = {p/q : p,q ∈ ℤ, q ≠ 0}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Rationals (all fractions)</span></div>
      <div style="margin-bottom:8px;"><span style="color:#f9a8d4;">ℝ\ℚ</span> = {√2, π, e, ...}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Irrationals (non-repeating, non-terminating)</span></div>
      <div style="margin-bottom:8px;"><span style="color:#c4b5fd;">ℝ</span> = ℚ ∪ (ℝ\ℚ)  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Real Numbers (the entire number line)</span></div>
    </div>
    <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);color:#9ca3af;font-size:0.85rem;">
      <strong style="color:var(--text);">Key Fact:</strong> Between any two distinct rational numbers, there exists another rational number (the rationals are <em>dense</em> in ℝ). Yet the irrationals are so numerous that the rationals have "measure zero" on the real line — a profound result from Measure Theory.
    </div>
  </div>
</div>

<h3>The Completeness Axiom: What Makes ℝ Special</h3>
<p>The single property that distinguishes ℝ from ℚ is the <strong>Completeness Axiom</strong> (also called the <em>Least Upper Bound Property</em>). Informally: the real number line has <em>no gaps</em>. Every sequence that "should" converge actually converges to a real number. This axiom is the foundation on which every theorem in analysis is built.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">THEOREM — Completeness Axiom (Least Upper Bound Property)</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:1.8;">
      <p style="color:#a7f3d0;font-style:italic;margin-bottom:12px;">"Every non-empty subset S ⊆ ℝ that is bounded above has a least upper bound (supremum) in ℝ."</p>
      <p style="color:#9ca3af;margin-bottom:8px;"><strong style="color:var(--text);">Upper bound:</strong> A number M such that x ≤ M for all x ∈ S.</p>
      <p style="color:#9ca3af;margin-bottom:8px;"><strong style="color:var(--text);">Supremum (sup S):</strong> The <em>smallest</em> such upper bound. Written sup S or lub S.</p>
      <p style="color:#9ca3af;margin-bottom:8px;"><strong style="color:var(--text);">Why ℚ fails:</strong> Consider S = {q ∈ ℚ : q² &lt; 2}. The supremum of S is √2, but √2 ∉ ℚ. In ℚ, S has no least upper bound — a "gap" exists at √2. In ℝ, that gap is filled.</p>
    </div>
  </div>
</div>

<h3>Absolute Value & Distance</h3>
<p>The <strong>absolute value</strong> of a real number x, written |x|, is defined as its distance from zero on the number line. It is always non-negative. Absolute value is the metric (distance function) on ℝ, and it generalises to norms in higher-dimensional spaces — making it fundamental to analysis.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DEFINITION & PROPERTIES — Absolute Value</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2.2;">
      <div><span style="color:#c4b5fd;">|x|</span> = x &nbsp;&nbsp;if x ≥ 0</div>
      <div><span style="color:#c4b5fd;">|x|</span> = −x  if x &lt; 0</div>
      <div style="margin-top:16px;border-top:1px solid var(--border);padding-top:16px;">
        <div><span style="color:#fcd34d;">P1:</span> |x| ≥ 0, and |x| = 0 ⟺ x = 0 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Non-negativity</span></div>
        <div><span style="color:#fcd34d;">P2:</span> |xy| = |x||y| &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Multiplicativity</span></div>
        <div><span style="color:#fcd34d;">P3:</span> |x + y| ≤ |x| + |y| &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Triangle Inequality (CRITICAL)</span></div>
        <div><span style="color:#fcd34d;">P4:</span> ||x| − |y|| ≤ |x − y| &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Reverse Triangle Inequality</span></div>
      </div>
    </div>
    <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);color:#9ca3af;font-size:0.85rem;">
      <strong style="color:var(--text);">Distance Interpretation:</strong> |x − y| = the distance between x and y on the number line. For example, |3 − 7| = |−4| = 4, meaning 3 and 7 are 4 units apart. This interpretation generalises to ε-δ definitions of limits.
    </div>
  </div>
</div>

<h3>Intervals: Open, Closed & Half-Open</h3>
<p>An <strong>interval</strong> is a connected subset of ℝ. Understanding interval notation is essential for stating domains, ranges, and the inputs to limit and continuity definitions. The parenthesis/bracket notation encodes whether the endpoints are included.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">NOTATION — Types of Intervals</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2.2;">
      <div><span style="color:#93c5fd;">(a, b)</span> = {x ∈ ℝ : a &lt; x &lt; b} &nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Open interval; endpoints NOT included</span></div>
      <div><span style="color:#a7f3d0;">[a, b]</span> = {x ∈ ℝ : a ≤ x ≤ b} &nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Closed interval; endpoints ARE included</span></div>
      <div><span style="color:#fcd34d;">[a, b)</span> = {x ∈ ℝ : a ≤ x &lt; b} &nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Half-open; left endpoint included</span></div>
      <div><span style="color:#fcd34d;">(a, b]</span> = {x ∈ ℝ : a &lt; x ≤ b} &nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Half-open; right endpoint included</span></div>
      <div><span style="color:#f9a8d4;">(a, +∞)</span> = {x ∈ ℝ : x &gt; a} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Infinity is NEVER included (always open)</span></div>
      <div><span style="color:#c4b5fd;">(−∞, +∞)</span> = ℝ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← The entire real line</span></div>
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mathModule->id,
            'title'       => '4.1 Real Numbers, Absolute Value & the Number Line',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L4_1', [
                ['q' => 'Which axiom guarantees that every bounded non-empty subset of ℝ has a supremum in ℝ?', 'opts' => ['The Archimedean Property', 'The Completeness Axiom', 'The Well-Ordering Principle', 'The Axiom of Choice'], 'ans' => 1, 'exp' => 'The Completeness Axiom (Least Upper Bound Property) is the defining property of ℝ that fills the "gaps" left in ℚ. It is the foundation of all convergence theorems in analysis.'],
                ['q' => 'What is |−7|?', 'opts' => ['−7', '7', '0', '49'], 'ans' => 1, 'exp' => '|x| = −x when x < 0, so |−7| = −(−7) = 7. Absolute value always returns a non-negative result.'],
                ['q' => 'Which of the following is the Triangle Inequality?', 'opts' => ['|x − y| ≥ |x| − |y|', '|x + y| ≤ |x| + |y|', '|xy| = |x| · |y|', '|x + y| = |x| + |y|'], 'ans' => 1, 'exp' => 'The Triangle Inequality states |x + y| ≤ |x| + |y|. It is one of the most-used inequalities in all of analysis and generalises to metric spaces and norms.'],
                ['q' => 'The interval [2, 5) contains which of the following endpoints?', 'opts' => ['Neither 2 nor 5', 'Both 2 and 5', 'Only 2', 'Only 5'], 'ans' => 2, 'exp' => '[2, 5) is a half-open interval. The square bracket [ means 2 IS included; the parenthesis ) means 5 is NOT included.'],
                ['q' => 'Why is √2 irrational?', 'opts' => ['Because it is negative', 'Because its decimal expansion terminates', 'Because it cannot be expressed as p/q for integers p, q with q ≠ 0', 'Because it is greater than 1'], 'ans' => 2, 'exp' => 'A number is irrational if and only if it cannot be written as a ratio of two integers. The classic proof by contradiction shows that assuming √2 = p/q in lowest terms leads to both p and q being even — a contradiction.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 4.2 — Sequences & Their Limits
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Sequences & Their Limits</h2>
<p>A <strong>sequence</strong> is an ordered, infinite list of real numbers: a₁, a₂, a₃, … . Sequences are the simplest objects on which the concept of a <em>limit</em> can be defined precisely. Every major theorem in analysis — from the convergence of series to the definition of the definite integral — is ultimately built on the theory of sequences. Mastering sequences is not optional; it is the gateway to all of calculus.</p>

<h3>Formal Definition of a Sequence</h3>
<p>Formally, a sequence is a function f : ℕ → ℝ. We write the sequence as (aₙ) where aₙ = f(n). The subscript n is called the <em>index</em>. Common examples include arithmetic sequences (constant difference), geometric sequences (constant ratio), and sequences defined by recursive formulas.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">EXAMPLES — Common Sequences</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2.2;">
      <div><span style="color:#a7f3d0;">aₙ = 1/n</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; → 1, 1/2, 1/3, 1/4, ... &nbsp;&nbsp;<span style="color:#6b7280;">← Converges to 0</span></div>
      <div><span style="color:#93c5fd;">aₙ = (−1)ⁿ</span> &nbsp;&nbsp; → −1, 1, −1, 1, ...  &nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Diverges (oscillates)</span></div>
      <div><span style="color:#fcd34d;">aₙ = n²</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; → 1, 4, 9, 16, ...  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Diverges to +∞</span></div>
      <div><span style="color:#c4b5fd;">aₙ = (1 + 1/n)ⁿ</span> → 2, 2.25, 2.37, ...  &nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Converges to e ≈ 2.718...</span></div>
      <div><span style="color:#f9a8d4;">aₙ = n/(n+1)</span> &nbsp; → 1/2, 2/3, 3/4, ...  &nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Converges to 1</span></div>
    </div>
  </div>
</div>

<h3>The Epsilon-N Definition of a Limit (The Heart of Analysis)</h3>
<p>The informal statement "aₙ approaches L as n → ∞" is made rigorous by the <strong>ε-N definition</strong>. This definition is the most important definition in all of undergraduate mathematics. Every time you use a limit — in derivatives, integrals, series — you are invoking this definition, implicitly or explicitly.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DEFINITION — Convergence of a Sequence (ε-N Definition)</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:1.9;">
      <p style="color:#a7f3d0;font-style:italic;margin-bottom:16px;">We say lim(n→∞) aₙ = L if and only if:</p>
      <p style="color:#c4b5fd;font-family:'JetBrains Mono',monospace;font-size:1rem;margin-bottom:16px;">∀ ε &gt; 0, ∃ N ∈ ℕ such that ∀ n &gt; N : |aₙ − L| &lt; ε</p>
      <p style="color:#9ca3af;margin-bottom:8px;"><strong style="color:var(--text);">Reading it aloud:</strong> "For every positive tolerance ε (no matter how tiny), there exists a point N in the sequence beyond which every term aₙ is within ε of the limit L."</p>
      <p style="color:#9ca3af;margin-bottom:8px;"><strong style="color:var(--text);">Intuition:</strong> The challenger picks ε (a tiny margin of error). You must find N that forces all subsequent terms inside the band (L − ε, L + ε). If you can always do this, the sequence converges to L.</p>
      <p style="color:#9ca3af;"><strong style="color:var(--text);">Key insight:</strong> N is allowed to depend on ε. Smaller ε typically requires a larger N — you must go further out in the sequence to stay within a tighter tolerance.</p>
    </div>
  </div>
</div>

<h3>Worked Proof: lim(n→∞) 1/n = 0</h3>
<p>We will prove this fundamental limit rigorously using the ε-N definition. This is a model proof — learn its structure, because you will adapt it to dozens of other sequences.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — lim(n→∞) 1/n = 0</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2;">
      <p style="color:#6b7280;margin-bottom:12px;">// We want to show: ∀ ε > 0, ∃ N ∈ ℕ s.t. ∀ n > N, |1/n − 0| &lt; ε</p>
      <p><strong style="color:#a7f3d0;">Step 1 (Scratch work — find N):</strong></p>
      <p style="font-family:'JetBrains Mono',monospace;">|1/n − 0| = |1/n| = 1/n &lt; ε</p>
      <p style="font-family:'JetBrains Mono',monospace;">⟺  n &gt; 1/ε</p>
      <p style="color:#9ca3af;margin-bottom:16px;">So we should choose N to be any natural number greater than 1/ε. By the Archimedean Property, such an N always exists.</p>
      <p><strong style="color:#a7f3d0;">Step 2 (Formal proof — write it up):</strong></p>
      <p style="color:#9ca3af;">Let ε &gt; 0 be given. By the Archimedean Property, choose N ∈ ℕ such that N &gt; 1/ε. Then for all n &gt; N:</p>
      <p style="font-family:'JetBrains Mono',monospace;">|1/n − 0| = 1/n &lt; 1/N &lt; ε  ∎</p>
    </div>
  </div>
</div>

<h3>Key Theorems on Convergent Sequences</h3>
<p>Once we have the ε-N definition, we can prove powerful theorems that let us compute limits algebraically without going back to ε-N every time. These are the <em>limit laws</em> for sequences.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">THEOREMS — Limit Laws for Sequences</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2.2;">
      <div><span style="color:#fcd34d;">T1 (Sum):</span> &nbsp;&nbsp;lim(aₙ + bₙ) = lim aₙ + lim bₙ</div>
      <div><span style="color:#fcd34d;">T2 (Product):</span> lim(aₙ · bₙ) = lim aₙ · lim bₙ</div>
      <div><span style="color:#fcd34d;">T3 (Quotient):</span> lim(aₙ/bₙ) = lim aₙ / lim bₙ &nbsp;(if lim bₙ ≠ 0)</div>
      <div><span style="color:#fcd34d;">T4 (Scalar):</span> &nbsp;lim(c · aₙ) = c · lim aₙ</div>
      <div><span style="color:#fcd34d;">T5 (Squeeze):</span> If aₙ ≤ bₙ ≤ cₙ and lim aₙ = lim cₙ = L, then lim bₙ = L</div>
    </div>
    <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);color:#9ca3af;font-size:0.85rem;">
      <strong style="color:var(--text);">The Squeeze Theorem</strong> (T5) is especially powerful. It lets us find limits of sequences that are "trapped" between two simpler sequences. Classic example: lim(sin n / n) = 0 because −1/n ≤ sin n/n ≤ 1/n and both bounds converge to 0.
    </div>
  </div>
</div>

<h3>Monotone Sequences & Bounded Sequences</h3>
<p>A sequence (aₙ) is <strong>monotone increasing</strong> if aₙ₊₁ ≥ aₙ for all n, and <strong>monotone decreasing</strong> if aₙ₊₁ ≤ aₙ for all n. A sequence is <strong>bounded above</strong> if there exists M with aₙ ≤ M for all n, and <strong>bounded below</strong> if there exists m with aₙ ≥ m for all n.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">THEOREM — Monotone Convergence Theorem</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:1.9;">
      <p style="color:#a7f3d0;font-style:italic;margin-bottom:12px;">"Every monotone increasing sequence that is bounded above converges. Every monotone decreasing sequence that is bounded below converges."</p>
      <p style="color:#9ca3af;">This theorem is a consequence of the Completeness Axiom. It is enormously useful because it tells you a sequence converges WITHOUT needing to know the limit in advance. You first show monotone + bounded, then separately compute the limit (often by assuming limit = L and solving an equation).</p>
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mathModule->id,
            'title'       => '4.2 Sequences & Their Limits',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L4_2', [
                ['q' => 'What is the formal ε-N definition of lim(n→∞) aₙ = L?', 'opts' => ['For some ε > 0, there exists N such that |aₙ − L| < ε for all n > N', 'For all ε > 0, there exists N such that |aₙ − L| < ε for all n > N', 'For all ε > 0, there exists N such that aₙ = L for all n > N', 'For all N, there exists ε > 0 such that |aₙ − L| < ε'], 'ans' => 1, 'exp' => 'The ε-N definition requires the condition to hold for ALL ε > 0 (not just some). For each such ε, you must find an N beyond which every term is within ε of L.'],
                ['q' => 'Which of the following sequences diverges?', 'opts' => ['aₙ = 1/n', 'aₙ = n/(n+1)', 'aₙ = (−1)ⁿ', 'aₙ = 1/n²'], 'ans' => 2, 'exp' => 'aₙ = (−1)ⁿ oscillates between −1 and 1 forever and never settles to a single value. The other three all converge (to 0, 1, and 0 respectively).'],
                ['q' => 'What does the Squeeze Theorem require?', 'opts' => ['That all three sequences converge to different limits', 'That aₙ ≤ bₙ ≤ cₙ and lim aₙ = lim cₙ', 'That bₙ is monotone increasing', 'That lim bₙ = 0'], 'ans' => 1, 'exp' => 'The Squeeze Theorem requires that bₙ is trapped between aₙ and cₙ, AND that the outer sequences share the same limit L. Then bₙ is forced to converge to L as well.'],
                ['q' => 'The Monotone Convergence Theorem guarantees convergence when a sequence is:', 'opts' => ['Monotone and unbounded', 'Monotone and bounded', 'Bounded but not monotone', 'Positive and decreasing'], 'ans' => 1, 'exp' => 'Both conditions are required: the sequence must be monotone (always increasing or always decreasing) AND bounded (there is an upper or lower barrier). Either condition alone is insufficient.'],
                ['q' => 'What is lim(n→∞) n/(n+1)?', 'opts' => ['0', '∞', '1/2', '1'], 'ans' => 3, 'exp' => 'Divide numerator and denominator by n: lim n/(n+1) = lim 1/(1 + 1/n) = 1/(1 + 0) = 1. This is a standard technique for rational sequences.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 4.3 — Limits of Functions & Continuity
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Limits of Functions & Continuity</h2>
<p>The limit of a <em>function</em> is a natural extension of the limit of a sequence, but now we allow the input variable to approach a point from a continuous range — not just through integers. The function limit and the notion of <strong>continuity</strong> are the twin pillars on which differential and integral calculus rests. A function that is continuous is well-behaved enough to be analysed; a discontinuous function presents challenges that we must handle with care.</p>

<h3>The Epsilon-Delta Definition of a Function Limit</h3>
<p>Informally, lim(x→a) f(x) = L means "f(x) can be made as close to L as desired by taking x sufficiently close to a (but not equal to a)." The rigorous formulation replaces "as close as desired" and "sufficiently close" with quantified bounds ε and δ.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DEFINITION — Function Limit (ε-δ Definition)</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:1.9;">
      <p style="color:#a7f3d0;font-style:italic;margin-bottom:12px;">lim(x→a) f(x) = L if and only if:</p>
      <p style="color:#c4b5fd;font-family:'JetBrains Mono',monospace;font-size:1rem;margin-bottom:16px;">∀ ε &gt; 0, ∃ δ &gt; 0 such that 0 &lt; |x − a| &lt; δ ⟹ |f(x) − L| &lt; ε</p>
      <p style="color:#9ca3af;margin-bottom:8px;"><strong style="color:var(--text);">The condition 0 &lt; |x − a|</strong> means x ≠ a. The value (or even existence) of f(a) is completely irrelevant to the limit. The limit is about what f(x) <em>approaches</em> as x nears a.</p>
      <p style="color:#9ca3af;"><strong style="color:var(--text);">δ depends on ε and a:</strong> For each tolerance ε, you must produce a δ that works. Smaller ε typically forces smaller δ.</p>
    </div>
  </div>
</div>

<h3>Worked Proof: lim(x→3) (2x − 1) = 5</h3>
<p>This is an ε-δ proof for a linear function. Linear functions are the easiest case because |f(x) − L| simplifies cleanly. Master this model proof before tackling harder functions.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — lim(x→3)(2x − 1) = 5</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2;">
      <p style="color:#6b7280;margin-bottom:12px;">// Goal: given ε > 0, find δ > 0 such that 0 &lt; |x − 3| &lt; δ ⟹ |(2x−1) − 5| &lt; ε</p>
      <p><strong style="color:#a7f3d0;">Scratch work:</strong></p>
      <p style="font-family:'JetBrains Mono',monospace;">|(2x − 1) − 5| = |2x − 6| = 2|x − 3|</p>
      <p style="font-family:'JetBrains Mono',monospace;">We need: 2|x − 3| &lt; ε, i.e. |x − 3| &lt; ε/2</p>
      <p style="color:#9ca3af;margin-bottom:16px;">So choose δ = ε/2.</p>
      <p><strong style="color:#a7f3d0;">Formal Proof:</strong></p>
      <p style="color:#9ca3af;">Let ε &gt; 0. Set δ = ε/2. Suppose 0 &lt; |x − 3| &lt; δ. Then:</p>
      <p style="font-family:'JetBrains Mono',monospace;">|(2x−1) − 5| = 2|x − 3| &lt; 2δ = 2(ε/2) = ε  ∎</p>
    </div>
  </div>
</div>

<h3>One-Sided Limits</h3>
<p>Sometimes f(x) approaches different values depending on the <em>direction</em> from which x approaches a. We define the <strong>left-hand limit</strong> (x approaches a from below) and the <strong>right-hand limit</strong> (x approaches a from above) separately. The two-sided limit exists if and only if both one-sided limits exist AND are equal.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DEFINITION — One-Sided Limits & Existence Criterion</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2.2;">
      <div><span style="color:#93c5fd;">lim(x→a⁻) f(x) = L</span> &nbsp;← Left-hand limit (x approaches a from below, x &lt; a)</div>
      <div><span style="color:#a7f3d0;">lim(x→a⁺) f(x) = L</span> &nbsp;← Right-hand limit (x approaches a from above, x &gt; a)</div>
      <div style="margin-top:12px;border-top:1px solid var(--border);padding-top:12px;">
        <span style="color:#fcd34d;">lim(x→a) f(x) exists ⟺ lim(x→a⁻) f(x) = lim(x→a⁺) f(x)</span>
      </div>
    </div>
    <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);color:#9ca3af;font-size:0.85rem;">
      <strong style="color:var(--text);">Example — Floor function:</strong> For f(x) = ⌊x⌋ at x = 2: lim(x→2⁻) ⌊x⌋ = 1 and lim(x→2⁺) ⌊x⌋ = 2. Since 1 ≠ 2, the two-sided limit at x = 2 does not exist.
    </div>
  </div>
</div>

<h3>Continuity: The Formal Definition</h3>
<p>A function is <strong>continuous at a point a</strong> if three conditions are simultaneously satisfied: f(a) is defined, the limit as x→a exists, and they are equal. Continuity means "you can draw the graph through the point without lifting your pen." While intuitive, the formal definition is what lets us prove theorems.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DEFINITION — Continuity at a Point</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:1.9;">
      <p style="color:#a7f3d0;margin-bottom:12px;">f is continuous at a if ALL THREE conditions hold:</p>
      <div style="font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2.2;">
        <div><span style="color:#fcd34d;">(1)</span> f(a) is defined (a is in the domain of f)</div>
        <div><span style="color:#fcd34d;">(2)</span> lim(x→a) f(x) exists (both one-sided limits exist and are equal)</div>
        <div><span style="color:#fcd34d;">(3)</span> lim(x→a) f(x) = f(a) (the limit equals the function value)</div>
      </div>
      <p style="color:#9ca3af;margin-top:12px;"><strong style="color:var(--text);">Types of discontinuity:</strong> Removable (hole in graph, limit exists but ≠ f(a) or f(a) undefined), Jump (one-sided limits exist but differ), Infinite (limit is ±∞).</p>
    </div>
  </div>
</div>

<h3>The Intermediate Value Theorem (IVT)</h3>
<p>The IVT is the first of several major theorems that follow from continuity. It formalises the intuition that a continuous function cannot "jump" over a value — it must pass through every intermediate value. This theorem is used to prove the existence of roots, fixed points, and equilibria throughout mathematics and applied science.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">THEOREM — Intermediate Value Theorem (IVT)</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:1.9;">
      <p style="color:#a7f3d0;font-style:italic;margin-bottom:12px;">"Let f be continuous on the closed interval [a, b]. If k is any value strictly between f(a) and f(b), then there exists at least one c ∈ (a, b) such that f(c) = k."</p>
      <p style="color:#9ca3af;margin-bottom:8px;"><strong style="color:var(--text);">Corollary (Root Finding):</strong> If f is continuous on [a, b] and f(a) and f(b) have opposite signs, then ∃ c ∈ (a, b) with f(c) = 0. This is the basis of the bisection method for numerical root finding.</p>
      <p style="color:#9ca3af;"><strong style="color:var(--text);">Example:</strong> Show x³ − x − 1 = 0 has a root in [1, 2]. Let f(x) = x³ − x − 1. Then f(1) = −1 &lt; 0 and f(2) = 5 &gt; 0. Since f is continuous and changes sign, by IVT ∃ c ∈ (1, 2) with f(c) = 0.</p>
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mathModule->id,
            'title'       => '4.3 Limits of Functions & Continuity',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L4_3', [
                ['q' => 'In the ε-δ definition, the condition 0 < |x − a| means:', 'opts' => ['x must be positive', 'x must not equal a', 'x must be greater than a', 'x must be within δ of a'], 'ans' => 1, 'exp' => 'The condition 0 < |x − a| ensures x ≠ a. The value of f at the point a itself is irrelevant to the limit — only the behavior of f near a matters.'],
                ['q' => 'The two-sided limit lim(x→a) f(x) exists if and only if:', 'opts' => ['f(a) is defined', 'Both one-sided limits exist and are equal', 'f is differentiable at a', 'The graph has no vertical asymptote at a'], 'ans' => 1, 'exp' => 'The two-sided limit exists iff lim(x→a⁻) f(x) = lim(x→a⁺) f(x). The function does not even need to be defined at a.'],
                ['q' => 'For f to be continuous at x = a, which conditions must ALL hold?', 'opts' => ['f(a) defined, limit exists, and limit = f(a)', 'f(a) defined and f is differentiable at a', 'The limit exists and f is bounded near a', 'f(a) = 0 and the limit equals 0'], 'ans' => 0, 'exp' => 'All three conditions are necessary: (1) f(a) is defined, (2) lim(x→a) f(x) exists, (3) the limit equals f(a). Failure of any one gives a discontinuity.'],
                ['q' => 'The Intermediate Value Theorem requires that f is:', 'opts' => ['Differentiable on (a, b)', 'Continuous on the closed interval [a, b]', 'Monotone on [a, b]', 'Bounded and positive on [a, b]'], 'ans' => 1, 'exp' => 'The IVT requires continuity on the closed interval [a, b]. Differentiability is not required — continuity is the key property that prevents "jumping over" values.'],
                ['q' => 'What type of discontinuity occurs when lim(x→a⁻) f(x) ≠ lim(x→a⁺) f(x)?', 'opts' => ['Removable discontinuity', 'Infinite discontinuity', 'Jump discontinuity', 'Oscillatory discontinuity'], 'ans' => 2, 'exp' => 'A jump discontinuity occurs when both one-sided limits exist but are unequal. The graph literally "jumps" from one value to another at x = a. The floor function ⌊x⌋ has jump discontinuities at every integer.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 4.4 — The Derivative: Definition & Interpretation
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>The Derivative: Definition & Interpretation</h2>
<p>The <strong>derivative</strong> is the central object of differential calculus. It measures the <em>instantaneous rate of change</em> of a function — how fast the output is changing relative to the input at a precise moment. Geometrically, the derivative at a point is the slope of the tangent line to the graph at that point. Physically, the derivative of position is velocity; the derivative of velocity is acceleration. The derivative is everywhere in science and engineering.</p>

<h3>From Secant Lines to the Tangent Line</h3>
<p>The slope of a secant line connecting two points (a, f(a)) and (a+h, f(a+h)) on a curve is the <strong>difference quotient</strong>. As we let h → 0, the secant line "rotates" and approaches the tangent line at (a, f(a)). The slope of this limiting tangent line is the derivative.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DEFINITION — The Derivative at a Point</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:1.9;">
      <p style="color:#a7f3d0;font-style:italic;margin-bottom:12px;">The derivative of f at x = a, written f′(a) or df/dx|ₓ₌ₐ, is:</p>
      <p style="color:#c4b5fd;font-family:'JetBrains Mono',monospace;font-size:1rem;margin-bottom:16px;">f′(a) = lim(h→0) [f(a + h) − f(a)] / h</p>
      <p style="color:#9ca3af;">provided this limit exists. If it does, f is said to be <strong style="color:var(--text);">differentiable</strong> at a. If the limit does not exist, f is not differentiable at a (e.g., at a corner, cusp, or vertical tangent).</p>
    </div>
  </div>
</div>

<h3>Worked Example: Derivative of f(x) = x² from First Principles</h3>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — f′(x) where f(x) = x²</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2;">
      <p style="font-family:'JetBrains Mono',monospace;">f′(x) = lim(h→0) [(x+h)² − x²] / h</p>
      <p style="font-family:'JetBrains Mono',monospace;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= lim(h→0) [x² + 2xh + h² − x²] / h</p>
      <p style="font-family:'JetBrains Mono',monospace;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= lim(h→0) [2xh + h²] / h</p>
      <p style="font-family:'JetBrains Mono',monospace;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= lim(h→0) h(2x + h) / h</p>
      <p style="font-family:'JetBrains Mono',monospace;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= lim(h→0) (2x + h)</p>
      <p style="font-family:'JetBrains Mono',monospace;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= 2x  ∎</p>
      <p style="color:#9ca3af;margin-top:12px;">So the derivative of x² is 2x. At x = 3, the tangent line has slope 2(3) = 6. At x = 0, the tangent is horizontal (slope 0) — the vertex of the parabola.</p>
    </div>
  </div>
</div>

<h3>Differentiability Implies Continuity</h3>
<p>There is a deep relationship between differentiability and continuity. Every differentiable function is continuous — but the converse is false. Continuity is necessary but not sufficient for differentiability. A function can be continuous at a point but fail to be differentiable there (e.g., |x| at x = 0 — it has a corner).</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">THEOREM — Differentiable ⟹ Continuous</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:1.9;">
      <p style="color:#a7f3d0;font-style:italic;margin-bottom:12px;">"If f is differentiable at a, then f is continuous at a."</p>
      <div style="font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2;">
        <div style="color:#9ca3af;">Proof sketch: lim(x→a)[f(x) − f(a)]</div>
        <div style="color:#9ca3af;">&nbsp;= lim(x→a) [f(x)−f(a)]/(x−a) · (x−a)</div>
        <div style="color:#9ca3af;">&nbsp;= f′(a) · 0 = 0</div>
        <div style="color:#9ca3af;">So lim(x→a) f(x) = f(a), i.e. f is continuous at a.  ∎</div>
      </div>
      <p style="color:#9ca3af;margin-top:12px;"><strong style="color:var(--text);">Counterexample (converse fails):</strong> f(x) = |x| is continuous at 0, but f′(0) does not exist because the left-hand limit of the difference quotient is −1 and the right-hand limit is +1.</p>
    </div>
  </div>
</div>

<h3>Standard Differentiation Rules</h3>
<p>Rather than computing every derivative from first principles, we use a set of <em>differentiation rules</em> — theorems proved from the limit definition — that let us differentiate algebraic combinations of functions quickly.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">RULES — Core Differentiation Rules</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2.3;">
      <div><span style="color:#fcd34d;">Power Rule:</span> &nbsp;&nbsp; d/dx[xⁿ] = nxⁿ⁻¹</div>
      <div><span style="color:#fcd34d;">Constant:</span> &nbsp;&nbsp;&nbsp;&nbsp; d/dx[c] = 0</div>
      <div><span style="color:#fcd34d;">Sum:</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d/dx[f + g] = f′ + g′</div>
      <div><span style="color:#fcd34d;">Product:</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d/dx[fg] = f′g + fg′</div>
      <div><span style="color:#fcd34d;">Quotient:</span> &nbsp;&nbsp;&nbsp;&nbsp; d/dx[f/g] = (f′g − fg′) / g²</div>
      <div><span style="color:#fcd34d;">Chain:</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d/dx[f(g(x))] = f′(g(x)) · g′(x)</div>
      <div><span style="color:#fcd34d;">Exponential:</span> &nbsp; d/dx[eˣ] = eˣ</div>
      <div><span style="color:#fcd34d;">Logarithm:</span> &nbsp;&nbsp;&nbsp; d/dx[ln x] = 1/x &nbsp;(x &gt; 0)</div>
      <div><span style="color:#fcd34d;">Sine:</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d/dx[sin x] = cos x</div>
      <div><span style="color:#fcd34d;">Cosine:</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; d/dx[cos x] = −sin x</div>
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mathModule->id,
            'title'       => '4.4 The Derivative: Definition & Interpretation',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L4_4', [
                ['q' => 'The derivative f′(a) is defined as:', 'opts' => ['lim(h→0) f(a) / h', 'lim(h→0) [f(a+h) − f(a)] / h', 'lim(h→0) [f(a+h) + f(a)] / h', 'lim(h→a) [f(h) − f(a)] / (h − a)'], 'ans' => 1, 'exp' => 'The derivative at a is the limit of the difference quotient [f(a+h) − f(a)]/h as h→0. This limit (when it exists) gives the slope of the tangent line at x = a.'],
                ['q' => 'Using the Power Rule, what is d/dx[x⁵]?', 'opts' => ['x⁴', '5x⁴', '5x⁵', '4x⁴'], 'ans' => 1, 'exp' => 'The Power Rule states d/dx[xⁿ] = nxⁿ⁻¹. With n = 5: d/dx[x⁵] = 5x⁴.'],
                ['q' => 'If f is differentiable at a, then:', 'opts' => ['f may or may not be continuous at a', 'f is necessarily continuous at a', 'f has a local maximum at a', 'f is continuous everywhere'], 'ans' => 1, 'exp' => 'The theorem "differentiable implies continuous" guarantees that if f′(a) exists, then f is continuous at a. The converse is false: |x| is continuous but not differentiable at 0.'],
                ['q' => 'The Product Rule for d/dx[f(x)g(x)] is:', 'opts' => ['f′(x)g′(x)', 'f′(x)g(x) + f(x)g′(x)', 'f′(x)/g(x) + f(x)/g′(x)', '[f(x) + g(x)]′'], 'ans' => 1, 'exp' => 'The Product Rule is d/dx[fg] = f′g + fg′. A common memory aid: "derivative of first times second, plus first times derivative of second."'],
                ['q' => 'What is d/dx[sin(x²)] using the Chain Rule?', 'opts' => ['cos(x²)', '2x·cos(x)', '2x·cos(x²)', 'cos(2x)'], 'ans' => 2, 'exp' => 'Chain Rule: d/dx[f(g(x))] = f′(g(x))·g′(x). Here f(u) = sin(u) and g(x) = x². So f′(u) = cos(u) and g′(x) = 2x. Result: cos(x²)·2x = 2x cos(x²).'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 4.5 — Applications of Derivatives (MVT, Extrema, Optimisation)
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Applications of Derivatives: MVT, Extrema & Optimisation</h2>
<p>The derivative is not merely a theoretical construct — it is one of the most powerful tools for <em>optimisation</em> and <em>analysis</em> in all of mathematics. In this lesson we study the central theorems connecting derivatives to the geometry and behaviour of functions: Rolle's Theorem, the Mean Value Theorem, and the First and Second Derivative Tests for extrema. These tools are the mathematical foundation behind machine learning loss minimisation, economic cost analysis, and engineering design.</p>

<h3>Rolle's Theorem</h3>
<p>Rolle's Theorem is a special case of the Mean Value Theorem that applies when a function has the same value at both endpoints. It guarantees at least one "flat spot" — a point where the tangent is horizontal — between the endpoints.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">THEOREM — Rolle's Theorem</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:1.9;">
      <p style="color:#a7f3d0;font-style:italic;margin-bottom:12px;">"Let f be continuous on [a,b], differentiable on (a,b), and f(a) = f(b). Then there exists at least one c ∈ (a,b) such that f′(c) = 0."</p>
      <p style="color:#9ca3af;"><strong style="color:var(--text);">Geometric meaning:</strong> If a smooth curve starts and ends at the same height, it must have at least one horizontal tangent somewhere in between. Intuitively: if you throw a ball and it lands at the same height, it must have had zero vertical velocity at some point (the peak).</p>
    </div>
  </div>
</div>

<h3>The Mean Value Theorem (MVT)</h3>
<p>The MVT is arguably the most important theorem in differential calculus. It generalises Rolle's Theorem by removing the requirement that f(a) = f(b). It says that for any smooth function, there is some point where the <em>instantaneous</em> rate of change equals the <em>average</em> rate of change over the interval.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">THEOREM — Mean Value Theorem (MVT)</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:1.9;">
      <p style="color:#a7f3d0;font-style:italic;margin-bottom:12px;">"Let f be continuous on [a,b] and differentiable on (a,b). Then ∃ c ∈ (a,b) such that:</p>
      <p style="color:#c4b5fd;font-family:'JetBrains Mono',monospace;font-size:1rem;margin-bottom:16px;">f′(c) = [f(b) − f(a)] / (b − a)"</p>
      <p style="color:#9ca3af;margin-bottom:8px;"><strong style="color:var(--text);">Interpretation:</strong> [f(b) − f(a)]/(b − a) is the slope of the secant line joining (a, f(a)) to (b, f(b)) — the average rate of change. The MVT says the instantaneous rate f′(c) must equal this average somewhere on (a, b).</p>
      <p style="color:#9ca3af;"><strong style="color:var(--text);">Real-world analogy:</strong> If you drive 120 km in 1 hour, your average speed is 120 km/h. The MVT guarantees that at some instant your speedometer read exactly 120 km/h.</p>
    </div>
  </div>
</div>

<h3>Critical Points & the First Derivative Test</h3>
<p>A <strong>critical point</strong> of f is any x in the domain where f′(x) = 0 or f′(x) does not exist. Local maxima and minima can only occur at critical points (by Fermat's Theorem). The First Derivative Test classifies critical points by examining the sign of f′ on either side.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">TEST — First Derivative Test for Local Extrema</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2.2;">
      <p style="color:#9ca3af;margin-bottom:12px;">Let c be a critical point of f (f is continuous near c):</p>
      <div style="font-family:'JetBrains Mono',monospace;line-height:2.2;">
        <div><span style="color:#a7f3d0;">f′ changes − to + at c</span> &nbsp;⟹ &nbsp;Local MINIMUM at c</div>
        <div><span style="color:#f9a8d4;">f′ changes + to − at c</span> &nbsp;⟹ &nbsp;Local MAXIMUM at c</div>
        <div><span style="color:#fcd34d;">f′ does not change sign</span> ⟹ &nbsp;Neither (saddle/inflection point)</div>
      </div>
    </div>
  </div>
</div>

<h3>The Second Derivative Test</h3>
<p>The <strong>second derivative</strong> f″(x) measures the rate of change of f′(x) — in other words, the <em>concavity</em> of f. If f″ > 0, the graph curves upward (concave up, like a bowl). If f″ < 0, it curves downward (concave down, like a hill). At a critical point where f′(c) = 0, the sign of f″(c) tells us the type of extremum.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">TEST — Second Derivative Test</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2.2;">
      <p style="color:#9ca3af;margin-bottom:12px;">Suppose f′(c) = 0 and f″ is continuous near c:</p>
      <div style="font-family:'JetBrains Mono',monospace;line-height:2.2;">
        <div><span style="color:#a7f3d0;">f″(c) &gt; 0</span> ⟹ Local MINIMUM (concave up at c)</div>
        <div><span style="color:#f9a8d4;">f″(c) &lt; 0</span> ⟹ Local MAXIMUM (concave down at c)</div>
        <div><span style="color:#fcd34d;">f″(c) = 0</span> ⟹ Test INCONCLUSIVE (use 1st derivative test)</div>
      </div>
      <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);color:#9ca3af;font-size:0.85rem;">
        <strong style="color:var(--text);">Example:</strong> f(x) = x³ − 3x. Then f′(x) = 3x² − 3 = 3(x−1)(x+1). Critical points at x = ±1. f″(x) = 6x. At x = 1: f″(1) = 6 &gt; 0 → local min. At x = −1: f″(−1) = −6 &lt; 0 → local max.
      </div>
    </div>
  </div>
</div>

<h3>Optimisation: Finding Global Extrema on a Closed Interval</h3>
<p>On a closed interval [a, b], the <strong>Extreme Value Theorem</strong> guarantees that a continuous function attains its global maximum and minimum. The procedure to find them is: evaluate f at all critical points in (a, b) and at the endpoints a and b, then compare all values.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROCEDURE — Global Extrema on [a, b]</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2.2;">
      <div><span style="color:#fcd34d;">Step 1:</span> Find all critical points of f in (a, b): solve f′(x) = 0 and find where f′ is undefined.</div>
      <div><span style="color:#fcd34d;">Step 2:</span> Evaluate f at every critical point and at the endpoints x = a and x = b.</div>
      <div><span style="color:#fcd34d;">Step 3:</span> The largest value is the global maximum; the smallest is the global minimum.</div>
      <div style="margin-top:12px;color:#9ca3af;font-size:0.85rem;border-top:1px solid var(--border);padding-top:12px;">
        <strong style="color:var(--text);">Example:</strong> Find global extrema of f(x) = x³ − 3x on [−2, 2].<br>
        f′(x) = 3x² − 3 = 0 → x = ±1 (both in (−2,2)).<br>
        f(−2) = −2,&nbsp; f(−1) = 2,&nbsp; f(1) = −2,&nbsp; f(2) = 2.<br>
        Global max = 2 (at x = −1 and x = 2). Global min = −2 (at x = 1 and x = −2).
      </div>
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mathModule->id,
            'title'       => '4.5 Applications of Derivatives: MVT, Extrema & Optimisation',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L4_5', [
                ['q' => 'The Mean Value Theorem guarantees a point c where f′(c) equals:', 'opts' => ['f(b) − f(a)', 'f(a+b)/2', '[f(b) − f(a)] / (b − a)', 'f′(a) + f′(b)'], 'ans' => 2, 'exp' => 'The MVT states there exists c ∈ (a,b) where the instantaneous rate f′(c) equals the average rate [f(b) − f(a)]/(b − a), which is the slope of the secant line.'],
                ['q' => 'Rolle\'s Theorem requires which additional condition beyond continuity on [a,b] and differentiability on (a,b)?', 'opts' => ['f is monotone', 'f(a) = f(b)', 'f(a) = 0', 'f is bounded'], 'ans' => 1, 'exp' => "Rolle's Theorem is the special case of the MVT where f(a) = f(b). This ensures the secant slope is zero, so the guaranteed tangent is horizontal: f′(c) = 0."],
                ['q' => 'By the First Derivative Test, if f′ changes from positive to negative at c, then c is a:', 'opts' => ['Local minimum', 'Local maximum', 'Inflection point', 'Saddle point'], 'ans' => 1, 'exp' => 'If f′ changes from + to − at c, the function increases before c and decreases after c — creating a peak. This is a local maximum.'],
                ['q' => 'The Second Derivative Test is INCONCLUSIVE when:', 'opts' => ['f″(c) > 0', 'f″(c) < 0', 'f″(c) = 0', 'f′(c) ≠ 0'], 'ans' => 2, 'exp' => 'When f″(c) = 0, the second derivative test gives no information. The point could be a local max, local min, or inflection point. The First Derivative Test must be used instead.'],
                ['q' => 'To find global extrema of a continuous function on [a, b], you must check:', 'opts' => ['Only interior critical points', 'Only the endpoints a and b', 'All critical points in (a,b) AND the endpoints a and b', 'Only where f″ = 0'], 'ans' => 2, 'exp' => 'The Extreme Value Theorem guarantees extrema exist on [a,b]. To find them: evaluate f at all critical points inside (a,b) AND at both endpoints. Compare all values — the largest and smallest are the global extrema.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 4.6 — L'Hôpital's Rule & Indeterminate Forms
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>L'Hôpital's Rule & Indeterminate Forms</h2>
<p>When computing limits, we often encounter <strong>indeterminate forms</strong> — expressions like 0/0 or ∞/∞ — where the limit is not immediately obvious and naive substitution fails. <strong>L'Hôpital's Rule</strong> (sometimes written L'Hospital's Rule) is a powerful theorem that resolves these indeterminate forms using derivatives. It is one of the most practically useful tools in all of calculus for evaluating difficult limits.</p>

<h3>Indeterminate Forms: A Taxonomy</h3>
<p>An indeterminate form is one where the limit cannot be determined from the limiting values of the parts alone. The seven classical indeterminate forms are listed below. Each requires a specific algebraic or calculus-based technique to resolve.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — The Seven Indeterminate Forms</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2.3;">
      <div><span style="color:#f9a8d4;">0/0</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;← Classic; L'Hôpital applies directly</div>
      <div><span style="color:#f9a8d4;">∞/∞</span> &nbsp;&nbsp;&nbsp;&nbsp;← Also direct L'Hôpital application</div>
      <div><span style="color:#fcd34d;">0 · ∞</span> &nbsp;&nbsp;&nbsp;← Rewrite as 0/(1/∞) or ∞/(1/0) first</div>
      <div><span style="color:#fcd34d;">∞ − ∞</span> &nbsp;&nbsp;← Combine fractions or rationalise first</div>
      <div><span style="color:#a7f3d0;">0⁰</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;← Take logarithm first → 0·∞ form</div>
      <div><span style="color:#a7f3d0;">1^∞</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;← Take logarithm first → 0·∞ form</div>
      <div><span style="color:#a7f3d0;">∞⁰</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;← Take logarithm first → 0·∞ form</div>
    </div>
  </div>
</div>

<h3>L'Hôpital's Rule: Statement & Conditions</h3>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">THEOREM — L'Hôpital's Rule</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:1.9;">
      <p style="color:#9ca3af;margin-bottom:12px;"><strong style="color:var(--text);">Conditions:</strong> Suppose lim(x→a) f(x)/g(x) produces the form 0/0 or ±∞/±∞, and both f and g are differentiable near a with g′(x) ≠ 0 near a. Then:</p>
      <p style="color:#c4b5fd;font-family:'JetBrains Mono',monospace;font-size:1rem;margin-bottom:16px;">lim(x→a) f(x)/g(x) = lim(x→a) f′(x)/g′(x)</p>
      <p style="color:#9ca3af;margin-bottom:8px;"><strong style="color:var(--text);">CRITICAL WARNING:</strong> You differentiate the numerator and denominator SEPARATELY. This is NOT the Quotient Rule. You do not compute (f/g)′.</p>
      <p style="color:#9ca3af;"><strong style="color:var(--text);">Iterated application:</strong> If f′(a)/g′(a) is still indeterminate, apply L'Hôpital again — as many times as needed, provided the conditions are re-verified each time.</p>
    </div>
  </div>
</div>

<h3>Worked Examples</h3>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">EXAMPLES — L'Hôpital Applications</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2;">
      <p><strong style="color:#a7f3d0;">Example 1 (0/0 form):</strong></p>
      <p style="font-family:'JetBrains Mono',monospace;">lim(x→0) sin(x)/x</p>
      <p style="font-family:'JetBrains Mono',monospace;">→ 0/0 form → apply L'Hôpital</p>
      <p style="font-family:'JetBrains Mono',monospace;">= lim(x→0) cos(x)/1 = cos(0)/1 = 1</p>
      <p style="color:#9ca3af;margin-bottom:16px;">This is the fundamental trigonometric limit; L'Hôpital gives it elegantly.</p>
      <p><strong style="color:#a7f3d0;">Example 2 (∞/∞ form):</strong></p>
      <p style="font-family:'JetBrains Mono',monospace;">lim(x→∞) ln(x)/x</p>
      <p style="font-family:'JetBrains Mono',monospace;">→ ∞/∞ form → apply L'Hôpital</p>
      <p style="font-family:'JetBrains Mono',monospace;">= lim(x→∞) (1/x)/1 = lim(x→∞) 1/x = 0</p>
      <p style="color:#9ca3af;margin-bottom:16px;">This proves that x grows much faster than ln(x) — logarithms are "slow."</p>
      <p><strong style="color:#a7f3d0;">Example 3 (1^∞ form):</strong></p>
      <p style="font-family:'JetBrains Mono',monospace;">lim(x→∞) (1 + 1/x)^x</p>
      <p style="font-family:'JetBrains Mono',monospace;">Let y = (1+1/x)^x → ln y = x·ln(1+1/x)</p>
      <p style="font-family:'JetBrains Mono',monospace;">= ln(1+1/x)/(1/x) → 0/0 → L'Hôpital → 1</p>
      <p style="font-family:'JetBrains Mono',monospace;">So lim y = e¹ = e</p>
      <p style="color:#9ca3af;">This is the definition of Euler's number e ≈ 2.71828...</p>
    </div>
  </div>
</div>

<h3>Growth Rate Hierarchy</h3>
<p>One of the most important consequences of L'Hôpital's Rule is that it allows us to establish a precise <em>growth rate hierarchy</em> among common function families. This hierarchy is essential for algorithm analysis (Big-O notation), series convergence, and asymptotic analysis.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">HIERARCHY — Growth Rates as x → ∞</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2.2;">
      <div style="text-align:center;font-size:1rem;color:#c4b5fd;">ln(x) ≪ xᵅ ≪ xⁿ ≪ eˣ ≪ xˣ</div>
      <div style="margin-top:12px;color:#6b7280;">(α &gt; 0, n is a positive integer)</div>
    </div>
    <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);color:#9ca3af;font-size:0.85rem;">
      <strong style="color:var(--text);">Reading "≪":</strong> f ≪ g means lim(x→∞) f(x)/g(x) = 0, i.e. g grows much faster than f. For example, lim(x→∞) x¹⁰⁰⁰/eˣ = 0: exponentials eventually dominate any polynomial, no matter how high the degree.
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mathModule->id,
            'title'       => "4.6 L'Hôpital's Rule & Indeterminate Forms",
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L4_6', [
                ['q' => "L'Hôpital's Rule may be applied when a limit produces:", 'opts' => ['Only the 0/0 form', 'The 0/0 or ∞/∞ form', 'Any form including 1/0', 'Only when the limit equals 0'], 'ans' => 1, 'exp' => "L'Hôpital's Rule directly applies to 0/0 and ±∞/±∞ indeterminate forms. Other forms (0·∞, ∞−∞, 0⁰, 1^∞, ∞⁰) must first be algebraically converted into one of these two forms."],
                ['q' => "When applying L'Hôpital's Rule to lim f(x)/g(x), you compute:", 'opts' => ['The derivative of f(x)/g(x) using the Quotient Rule', 'f′(x)/g′(x) by differentiating numerator and denominator separately', 'f(x)/g′(x)', "The limit of f′(x)·g(x)"], 'ans' => 1, 'exp' => "L'Hôpital's Rule replaces f/g with f′/g′ — differentiating top and bottom separately. It is NOT the Quotient Rule, which gives (f/g)′ = (f′g − fg′)/g²."],
                ['q' => 'What is lim(x→0) sin(x)/x?', 'opts' => ['0', '∞', 'Undefined', '1'], 'ans' => 3, 'exp' => "This is the 0/0 indeterminate form. Applying L'Hôpital: lim cos(x)/1 = cos(0) = 1. This is the fundamental trigonometric limit."],
                ['q' => 'Which function grows fastest as x → ∞?', 'opts' => ['x¹⁰⁰⁰', 'ln(x)', 'eˣ', 'x!'], 'ans' => 3, 'exp' => 'The growth hierarchy is: ln(x) ≪ polynomial ≪ exponential ≪ factorial. Among the listed options, eˣ grows faster than any polynomial. (x! grows faster than eˣ but may be considered in discrete analysis.)'],
                ['q' => 'To evaluate lim(x→∞)(1 + 1/x)^x using logarithms, the expression ln y = x·ln(1+1/x) produces which indeterminate form?', 'opts' => ['∞/∞', '0/0', '0·∞ → rewritten as 0/0', '1^∞'], 'ans' => 2, 'exp' => 'ln y = x·ln(1+1/x) is ∞·0 (a 0·∞ form). Rewriting: ln(1+1/x)/(1/x) gives 0/0 as x→∞. Applying L\'Hôpital then gives lim = 1, so y = e¹ = e.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 4.7 — The Definite Integral: Definition & Properties
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>The Definite Integral: Definition & Properties</h2>
<p>The <strong>definite integral</strong> is the second great concept of calculus, complementing the derivative. Geometrically, the definite integral ∫ₐᵇ f(x)dx represents the <em>signed area</em> between the graph of f and the x-axis on the interval [a, b]. Physically, if f(x) is velocity, then ∫ f dx is displacement. If f(x) is a probability density function, ∫ f dx is probability. The integral appears in physics, economics, probability, and throughout science.</p>

<h3>Riemann Sums: Building the Integral from Rectangles</h3>
<p>The foundational idea is to approximate the area under a curve using rectangles, then take the limit as the rectangles become infinitely thin. This is the <strong>Riemann sum</strong> construction, named after Bernhard Riemann who formalised it in the 19th century.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DEFINITION — Riemann Sum & the Definite Integral</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2;">
      <p style="color:#9ca3af;margin-bottom:12px;"><strong style="color:var(--text);">Partition:</strong> Divide [a, b] into n subintervals. Let Δxᵢ = xᵢ − xᵢ₋₁ be the width of the i-th subinterval and xᵢ* be any sample point in [xᵢ₋₁, xᵢ].</p>
      <p style="color:#c4b5fd;font-family:'JetBrains Mono',monospace;margin-bottom:12px;">Rₙ = Σᵢ₌₁ⁿ f(xᵢ*) · Δxᵢ &nbsp;&nbsp;← Riemann Sum</p>
      <p style="color:#a7f3d0;font-family:'JetBrains Mono',monospace;margin-bottom:16px;">∫ₐᵇ f(x)dx = lim(n→∞) Σᵢ₌₁ⁿ f(xᵢ*) · Δxᵢ &nbsp;&nbsp;← Definite Integral</p>
      <p style="color:#9ca3af;">For a uniform partition with n equal subintervals of width Δx = (b−a)/n, using right endpoints xᵢ = a + iΔx:</p>
      <p style="color:#fcd34d;font-family:'JetBrains Mono',monospace;">∫ₐᵇ f(x)dx = lim(n→∞) (b−a)/n · Σᵢ₌₁ⁿ f(a + i(b−a)/n)</p>
    </div>
  </div>
</div>

<h3>Properties of the Definite Integral</h3>
<p>These properties follow directly from the Riemann sum definition and allow us to manipulate integrals algebraically. They are analogous to the limit laws for sequences and functions.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROPERTIES — Definite Integral Rules</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2.3;">
      <div><span style="color:#fcd34d;">P1:</span> ∫ₐᵃ f(x)dx = 0</div>
      <div><span style="color:#fcd34d;">P2:</span> ∫ₐᵇ f(x)dx = −∫ᵦₐ f(x)dx &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Reversing limits negates</span></div>
      <div><span style="color:#fcd34d;">P3:</span> ∫ₐᵇ c·f(x)dx = c · ∫ₐᵇ f(x)dx &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Constant Multiple</span></div>
      <div><span style="color:#fcd34d;">P4:</span> ∫ₐᵇ [f±g]dx = ∫ₐᵇ f dx ± ∫ₐᵇ g dx &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Sum/Difference</span></div>
      <div><span style="color:#fcd34d;">P5:</span> ∫ₐᵇ f dx = ∫ₐᶜ f dx + ∫ᶜᵇ f dx &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Interval Additivity</span></div>
      <div><span style="color:#fcd34d;">P6:</span> If f(x) ≥ 0 on [a,b], then ∫ₐᵇ f dx ≥ 0 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#6b7280;">← Positivity</span></div>
      <div><span style="color:#fcd34d;">P7:</span> If f ≤ g on [a,b], then ∫ₐᵇ f dx ≤ ∫ₐᵇ g dx &nbsp;<span style="color:#6b7280;">← Monotonicity</span></div>
    </div>
  </div>
</div>

<h3>Signed Area: When f(x) is Negative</h3>
<p>The definite integral computes <em>signed area</em>. When f(x) &lt; 0 on an interval, the "area" below the x-axis contributes a <em>negative</em> amount to the integral. This sign convention is essential: it means ∫₀²π sin(x)dx = 0 because the positive area on [0,π] is exactly cancelled by the negative area on [π, 2π].</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — Signed vs Total Area</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2;">
      <p style="color:#a7f3d0;margin-bottom:12px;">Signed Area = ∫ₐᵇ f(x)dx &nbsp;&nbsp;(can be positive, negative, or zero)</p>
      <p style="color:#fcd34d;margin-bottom:16px;">Total Area &nbsp;= ∫ₐᵇ |f(x)|dx &nbsp;(always non-negative)</p>
      <p style="color:#9ca3af;"><strong style="color:var(--text);">Strategy for total area:</strong> Find all zeros of f on [a,b] (where f changes sign). Split the integral at each zero. Take the absolute value of each sub-integral. Sum them. This is necessary whenever you are asked for "the area of the region enclosed by the curve and the x-axis."</p>
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mathModule->id,
            'title'       => '4.7 The Definite Integral: Definition & Properties',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L4_7', [
                ['q' => 'The definite integral ∫ₐᵇ f(x)dx is defined as the limit of:', 'opts' => ['The derivative of f(x) as x → a', 'A Riemann sum as the number of subintervals n → ∞', 'The antiderivative of f evaluated at b', 'The average value of f on [a, b]'], 'ans' => 1, 'exp' => 'The definite integral is defined as lim(n→∞) Σ f(xᵢ*)Δxᵢ — the limit of Riemann sums as the partition is refined. This limit (when it exists) defines the integral.'],
                ['q' => 'Which property states ∫ₐᵇ f dx = ∫ₐᶜ f dx + ∫ᶜᵇ f dx?', 'opts' => ['Constant Multiple Rule', 'Interval Additivity', 'Monotonicity of Integrals', 'Linearity'], 'ans' => 1, 'exp' => 'The Interval Additivity property allows us to split (or join) integrals at intermediate points. This is fundamental for computing integrals over piecewise-defined functions and for changing limits of integration.'],
                ['q' => 'What is ∫₀²π sin(x) dx?', 'opts' => ['2', '4', '−2', '0'], 'ans' => 3, 'exp' => 'sin(x) ≥ 0 on [0,π] and sin(x) ≤ 0 on [π, 2π]. The positive area on [0,π] is 2 and the negative area on [π,2π] is −2. The signed areas cancel: ∫₀²π sin x dx = 0.'],
                ['q' => 'If f(x) ≤ g(x) on [a,b], which property holds?', 'opts' => ['∫ₐᵇ f dx ≥ ∫ₐᵇ g dx', '∫ₐᵇ f dx ≤ ∫ₐᵇ g dx', '∫ₐᵇ f dx = ∫ₐᵇ g dx', '∫ₐᵇ (g−f) dx = 0'], 'ans' => 1, 'exp' => 'This is the Monotonicity of Integrals: if f ≤ g pointwise on [a,b], then their integrals satisfy the same inequality ∫f ≤ ∫g. This follows from the positivity property applied to g − f ≥ 0.'],
                ['q' => 'To compute the TOTAL (not signed) area between f(x) and the x-axis on [a,b], you compute:', 'opts' => ['∫ₐᵇ f(x) dx', '|∫ₐᵇ f(x) dx|', '∫ₐᵇ |f(x)| dx', '(∫ₐᵇ f(x) dx)²'], 'ans' => 2, 'exp' => 'Total area = ∫ₐᵇ |f(x)| dx. This is NOT the same as |∫ₐᵇ f dx|. For example, for sin on [0, 2π]: |∫₀²π sin dx| = |0| = 0, but ∫₀²π |sin x| dx = 4 (the actual total area).'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 4.8 — The Fundamental Theorem of Calculus
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>The Fundamental Theorem of Calculus (FTC)</h2>
<p>The <strong>Fundamental Theorem of Calculus</strong> is the most important theorem in all of calculus. It establishes the profound and surprising connection between the two great operations of calculus — differentiation and integration — revealing them to be inverse operations. Before the FTC was discovered (independently by Newton and Leibniz in the 17th century), computing definite integrals required painstaking Riemann sum calculations. The FTC makes most integral computations trivial by reducing them to finding antiderivatives.</p>

<h3>Antiderivatives (Indefinite Integrals)</h3>
<p>A function F is an <strong>antiderivative</strong> of f on an interval I if F′(x) = f(x) for all x ∈ I. If F is one antiderivative of f, then the general antiderivative is F(x) + C, where C is an arbitrary constant. The indefinite integral notation ∫ f(x)dx = F(x) + C represents the family of all antiderivatives.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">TABLE — Standard Antiderivative Formulas</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2.3;">
      <div><span style="color:#fcd34d;">∫ xⁿ dx</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= xⁿ⁺¹/(n+1) + C &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(n ≠ −1)</div>
      <div><span style="color:#fcd34d;">∫ 1/x dx</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= ln|x| + C</div>
      <div><span style="color:#fcd34d;">∫ eˣ dx</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= eˣ + C</div>
      <div><span style="color:#fcd34d;">∫ aˣ dx</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= aˣ/ln(a) + C &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(a &gt; 0, a ≠ 1)</div>
      <div><span style="color:#fcd34d;">∫ sin(x) dx</span> &nbsp;&nbsp;&nbsp;&nbsp;= −cos(x) + C</div>
      <div><span style="color:#fcd34d;">∫ cos(x) dx</span> &nbsp;&nbsp;&nbsp;&nbsp;= sin(x) + C</div>
      <div><span style="color:#fcd34d;">∫ sec²(x) dx</span> &nbsp;&nbsp;&nbsp;= tan(x) + C</div>
      <div><span style="color:#fcd34d;">∫ 1/√(1−x²) dx</span> = arcsin(x) + C</div>
      <div><span style="color:#fcd34d;">∫ 1/(1+x²) dx</span> &nbsp;= arctan(x) + C</div>
    </div>
  </div>
</div>

<h3>The Fundamental Theorem of Calculus — Part 1</h3>
<p>Part 1 of the FTC deals with <em>differentiation of an integral with variable upper limit</em>. It tells us that integration and differentiation are inverse operations in a precise sense. This part is used to differentiate functions defined as integrals.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">THEOREM — FTC Part 1</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:1.9;">
      <p style="color:#9ca3af;margin-bottom:12px;">Let f be continuous on [a, b] and define the accumulation function:</p>
      <p style="color:#c4b5fd;font-family:'JetBrains Mono',monospace;font-size:1rem;margin-bottom:12px;">G(x) = ∫ₐˣ f(t) dt</p>
      <p style="color:#a7f3d0;font-style:italic;margin-bottom:12px;">Then G is differentiable on (a, b) and G′(x) = f(x).</p>
      <p style="color:#9ca3af;"><strong style="color:var(--text);">With Chain Rule:</strong> If the upper limit is a function u(x): d/dx ∫ₐᵘ⁽ˣ⁾ f(t)dt = f(u(x)) · u′(x).</p>
    </div>
  </div>
</div>

<h3>The Fundamental Theorem of Calculus — Part 2</h3>
<p>Part 2 is the evaluation theorem — the workhorse that makes definite integral computation practical. It says: to compute ∫ₐᵇ f(x)dx, find any antiderivative F of f and evaluate F(b) − F(a). The Riemann sum construction is bypassed entirely.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">THEOREM — FTC Part 2 (Evaluation Theorem)</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:1.9;">
      <p style="color:#9ca3af;margin-bottom:12px;">Let f be continuous on [a, b] and let F be any antiderivative of f. Then:</p>
      <p style="color:#c4b5fd;font-family:'JetBrains Mono',monospace;font-size:1rem;margin-bottom:16px;">∫ₐᵇ f(x)dx = F(b) − F(a) = [F(x)]ₐᵇ</p>
      <p style="color:#9ca3af;margin-bottom:8px;"><strong style="color:var(--text);">Why C disappears:</strong> If we use F(x) + C as the antiderivative: [F(b) + C] − [F(a) + C] = F(b) − F(a). The arbitrary constant always cancels in a definite integral.</p>
    </div>
  </div>
</div>

<h3>Worked Examples Using FTC Part 2</h3>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">EXAMPLES — Definite Integral Evaluation</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2.2;">
      <p><strong style="color:#a7f3d0;">Example 1:</strong> ∫₀³ x² dx</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">F(x) = x³/3. &nbsp;[x³/3]₀³ = 27/3 − 0 = 9</p>
      <p style="margin-top:12px;"><strong style="color:#a7f3d0;">Example 2:</strong> ∫₁ᵉ 1/x dx</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">F(x) = ln|x|. &nbsp;[ln x]₁ᵉ = ln(e) − ln(1) = 1 − 0 = 1</p>
      <p style="margin-top:12px;"><strong style="color:#a7f3d0;">Example 3:</strong> ∫₀^(π/2) cos(x) dx</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">F(x) = sin(x). &nbsp;[sin x]₀^(π/2) = sin(π/2) − sin(0) = 1 − 0 = 1</p>
      <p style="margin-top:12px;"><strong style="color:#a7f3d0;">Example 4:</strong> ∫₋₁¹ (3x² − 2x + 1) dx</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">F(x) = x³ − x² + x. &nbsp;F(1)−F(−1) = (1−1+1) − (−1−1−1) = 1 − (−3) = 4</p>
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mathModule->id,
            'title'       => '4.8 The Fundamental Theorem of Calculus',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L4_8', [
                ['q' => 'FTC Part 2 states that ∫ₐᵇ f(x)dx equals:', 'opts' => ['F′(b) − F′(a)', 'F(a) − F(b)', 'F(b) − F(a)', 'f(b) − f(a)'], 'ans' => 2, 'exp' => 'FTC Part 2: ∫ₐᵇ f(x)dx = F(b) − F(a) where F is any antiderivative of f. This is the evaluation theorem that makes computing definite integrals practical.'],
                ['q' => 'What is the antiderivative of f(x) = xⁿ (n ≠ −1)?', 'opts' => ['nxⁿ⁻¹ + C', 'xⁿ⁺¹ + C', 'xⁿ⁺¹/(n+1) + C', 'xⁿ/n + C'], 'ans' => 2, 'exp' => 'The Power Rule for integration (reverse of the Power Rule for differentiation): ∫xⁿ dx = xⁿ⁺¹/(n+1) + C. Verify by differentiating: d/dx[xⁿ⁺¹/(n+1)] = (n+1)xⁿ/(n+1) = xⁿ. ✓'],
                ['q' => 'By FTC Part 1, if G(x) = ∫₀ˣ sin(t)dt, then G′(x) =', 'opts' => ['cos(x)', '−cos(x)', 'sin(x)', 'cos(x) − 1'], 'ans' => 2, 'exp' => 'FTC Part 1: d/dx ∫ₐˣ f(t)dt = f(x). Here f(t) = sin(t), so G′(x) = sin(x). The antiderivative is not needed — just evaluate the integrand at the upper limit.'],
                ['q' => 'What is ∫₀¹ eˣ dx?', 'opts' => ['e', 'e − 1', 'e + 1', '1'], 'ans' => 1, 'exp' => 'The antiderivative of eˣ is eˣ. [eˣ]₀¹ = e¹ − e⁰ = e − 1.'],
                ['q' => 'Why does the constant C disappear when evaluating a definite integral using F(x) + C?', 'opts' => ['Because C = 0 by definition', 'Because [F(b)+C] − [F(a)+C] = F(b) − F(a)', 'Because definite integrals have no antiderivatives', 'Because C only applies to open intervals'], 'ans' => 1, 'exp' => 'When you subtract F(a)+C from F(b)+C, the C terms cancel: (F(b)+C) − (F(a)+C) = F(b) − F(a). This is why any antiderivative works — the choice of C is irrelevant for definite integrals.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 4.9 — Integration Techniques: Substitution & Parts
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Integration Techniques: u-Substitution & Integration by Parts</h2>
<p>While the FTC tells us that computing a definite integral reduces to finding an antiderivative, actually <em>finding</em> antiderivatives is often non-trivial. Unlike differentiation — which has a mechanical algorithm — integration is an art that requires recognising patterns and choosing the right technique. The two most important techniques in Mathematical Analysis I are <strong>u-substitution</strong> (the integral analogue of the Chain Rule) and <strong>Integration by Parts</strong> (the integral analogue of the Product Rule).</p>

<h3>U-Substitution: The Reverse Chain Rule</h3>
<p>If the integrand contains a function and its derivative (possibly up to a constant), u-substitution "undoes" the Chain Rule. The key skill is recognising which part to call u — it should be the "inner function" of a composition, and its derivative should appear (or be achievable by adjusting a constant) elsewhere in the integrand.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROCEDURE — U-Substitution</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2.2;">
      <div><span style="color:#fcd34d;">Step 1:</span> Identify u = g(x), the inner function or the part making the integrand complicated.</div>
      <div><span style="color:#fcd34d;">Step 2:</span> Compute du = g′(x)dx. Solve for dx if needed: dx = du/g′(x).</div>
      <div><span style="color:#fcd34d;">Step 3:</span> Substitute. The integrand should become a function of u only.</div>
      <div><span style="color:#fcd34d;">Step 4:</span> Integrate with respect to u using standard formulas.</div>
      <div><span style="color:#fcd34d;">Step 5:</span> Back-substitute u = g(x) to express the answer in terms of x.</div>
      <div><span style="color:#fcd34d;">Definite:</span> Alternatively, change the limits of integration to u-values and never back-substitute.</div>
    </div>
  </div>
</div>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">EXAMPLES — U-Substitution</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2.1;">
      <p><strong style="color:#a7f3d0;">Example 1:</strong> ∫ 2x·e^(x²) dx</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">u = x², &nbsp;du = 2x dx</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">= ∫ eᵘ du = eᵘ + C = e^(x²) + C</p>
      <p style="margin-top:12px;"><strong style="color:#a7f3d0;">Example 2:</strong> ∫ cos(3x) dx</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">u = 3x, &nbsp;du = 3dx &nbsp;→ dx = du/3</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">= (1/3)∫ cos(u) du = (1/3)sin(u) + C = (1/3)sin(3x) + C</p>
      <p style="margin-top:12px;"><strong style="color:#a7f3d0;">Example 3 (Definite):</strong> ∫₀¹ x/(x²+1) dx</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">u = x²+1, &nbsp;du = 2x dx &nbsp;→ x dx = du/2</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">Limits: x=0→u=1, &nbsp;x=1→u=2</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">= (1/2)∫₁² (1/u)du = (1/2)[ln u]₁² = (1/2)ln2</p>
    </div>
  </div>
</div>

<h3>Integration by Parts: The Reverse Product Rule</h3>
<p>Integration by Parts (IBP) handles integrals of products of functions that u-substitution cannot simplify. It is derived from the Product Rule: d/dx[uv] = u'v + uv', rearranged and integrated to give the IBP formula. The mnemonic LIATE helps choose which factor to call u.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FORMULA — Integration by Parts</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:1.9;">
      <p style="color:#c4b5fd;font-family:'JetBrains Mono',monospace;font-size:1rem;margin-bottom:16px;">∫ u dv = uv − ∫ v du</p>
      <p style="color:#9ca3af;margin-bottom:8px;"><strong style="color:var(--text);">LIATE Priority for choosing u</strong> (choose the first type that appears):</p>
      <div style="font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2.2;color:#e5e7eb;">
        <div><span style="color:#fcd34d;">L</span>ogarithms: ln(x), log(x)</div>
        <div><span style="color:#fcd34d;">I</span>nverse trig: arcsin(x), arctan(x)</div>
        <div><span style="color:#fcd34d;">A</span>lgebraic: polynomials, xⁿ</div>
        <div><span style="color:#fcd34d;">T</span>rigonometric: sin(x), cos(x)</div>
        <div><span style="color:#fcd34d;">E</span>xponential: eˣ, aˣ</div>
      </div>
    </div>
  </div>
</div>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">EXAMPLES — Integration by Parts</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2.1;">
      <p><strong style="color:#a7f3d0;">Example 1:</strong> ∫ x·eˣ dx</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">u = x &nbsp;&nbsp;&nbsp;(Algebraic first) &nbsp;&nbsp; dv = eˣ dx</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">du = dx &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;v  = eˣ</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">= x·eˣ − ∫ eˣ dx = x·eˣ − eˣ + C = eˣ(x − 1) + C</p>
      <p style="margin-top:12px;"><strong style="color:#a7f3d0;">Example 2:</strong> ∫ ln(x) dx</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">u = ln(x) &nbsp;(Logarithm first) &nbsp;dv = dx</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">du = (1/x)dx &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;v  = x</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">= x·ln(x) − ∫ x·(1/x)dx = x·ln(x) − ∫ 1 dx = x·ln(x) − x + C</p>
      <p style="margin-top:12px;"><strong style="color:#a7f3d0;">Example 3 (Cyclic IBP):</strong> ∫ eˣ·sin(x) dx</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">Apply IBP twice → get I = eˣ·sin(x) − eˣ·cos(x) − I</p>
      <p style="font-family:'JetBrains Mono',monospace;color:#9ca3af;">→ 2I = eˣ(sin x − cos x) → I = eˣ(sin x − cos x)/2 + C</p>
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mathModule->id,
            'title'       => '4.9 Integration Techniques: u-Substitution & Integration by Parts',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L4_9', [
                ['q' => 'U-substitution is the integral analogue of which differentiation rule?', 'opts' => ['The Power Rule', 'The Product Rule', 'The Chain Rule', 'The Quotient Rule'], 'ans' => 2, 'exp' => 'U-substitution reverses the Chain Rule. If you see a composition f(g(x)) where g′(x) also appears in the integrand, set u = g(x) and the Chain Rule structure unwinds.'],
                ['q' => 'To evaluate ∫ 2x·e^(x²) dx, the best substitution is:', 'opts' => ['u = 2x', 'u = e^(x²)', 'u = x²', 'u = x² + 1'], 'ans' => 2, 'exp' => "u = x² gives du = 2x dx, so 2x dx = du. The integral becomes ∫eᵘ du = eᵘ + C = e^(x²) + C. The key is that the derivative of u = x² is 2x, which appears in the integrand."],
                ['q' => 'The Integration by Parts formula is:', 'opts' => ['∫ u dv = uv + ∫ v du', '∫ u dv = u′v − uv′', '∫ u dv = uv − ∫ v du', '∫ uv dx = u′v − uv′'], 'ans' => 2, 'exp' => 'Integration by Parts: ∫ u dv = uv − ∫ v du. This is derived from the Product Rule: d(uv) = u dv + v du → u dv = d(uv) − v du → integrate both sides.'],
                ['q' => 'Using LIATE, for ∫ x·ln(x) dx, which factor should be u?', 'opts' => ['x (Algebraic)', 'ln(x) (Logarithm)', 'x·ln(x) (Both)', 'Neither; use substitution'], 'ans' => 1, 'exp' => 'LIATE: Logarithm comes before Algebraic. So u = ln(x) (higher priority) and dv = x dx. Then du = (1/x)dx and v = x²/2. The integral becomes (x²/2)ln(x) − ∫(x²/2)(1/x)dx = (x²/2)ln(x) − x²/4 + C.'],
                ['q' => 'What happens when Integration by Parts is applied twice to ∫ eˣ sin(x) dx?', 'opts' => ['The integral simplifies to eˣ + C', 'The original integral reappears, allowing us to solve algebraically', 'The integral becomes ∫ eˣ cos(x) dx permanently', 'The method fails and substitution must be used'], 'ans' => 1, 'exp' => "Applying IBP twice to ∫ eˣ sin(x) dx produces the original integral I on the right side: I = eˣsin(x) − eˣcos(x) − I. Solving: 2I = eˣ(sin x − cos x), so I = eˣ(sin x − cos x)/2 + C. This 'cyclic' technique is standard."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 4.10 — Infinite Series & Convergence Tests
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Infinite Series & Convergence Tests</h2>
<p>An <strong>infinite series</strong> is a sum with infinitely many terms: Σₙ₌₁^∞ aₙ = a₁ + a₂ + a₃ + ⋯. Does such a sum make sense? Can infinitely many numbers add up to a finite result? The answer is: sometimes yes, sometimes no. Determining which case applies — <em>convergence</em> or <em>divergence</em> — is the central problem of series theory. Series appear throughout analysis: Taylor series represent functions as polynomials, Fourier series represent functions as trigonometric sums, and power series are the language of complex analysis.</p>

<h3>Partial Sums & the Definition of Series Convergence</h3>
<p>The rigorous definition of an infinite series uses the sequence of <em>partial sums</em>. The n-th partial sum Sₙ is the sum of the first n terms. The series converges if this sequence converges.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DEFINITION — Series Convergence via Partial Sums</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2;">
      <p style="font-family:'JetBrains Mono',monospace;color:#c4b5fd;">Sₙ = Σₖ₌₁ⁿ aₖ = a₁ + a₂ + ⋯ + aₙ &nbsp;&nbsp;← n-th partial sum</p>
      <p style="color:#9ca3af;margin-bottom:12px;">The series Σaₙ <strong style="color:#a7f3d0;">converges</strong> to S if lim(n→∞) Sₙ = S (i.e. the partial sums converge as a sequence).</p>
      <p style="color:#9ca3af;">The series Σaₙ <strong style="color:#f9a8d4;">diverges</strong> if lim(n→∞) Sₙ does not exist (is ∞ or oscillates).</p>
    </div>
  </div>
</div>

<h3>The Geometric Series: The Only Series with a Closed-Form Sum</h3>
<p>The geometric series is the most important series in all of analysis. It converges when |r| &lt; 1 and the sum is given by an elegant formula. Nearly every other convergence result is ultimately compared to or derived from the geometric series.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">THEOREM — Geometric Series</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.95rem;line-height:2;">
      <p style="color:#c4b5fd;font-family:'JetBrains Mono',monospace;font-size:1rem;margin-bottom:12px;">Σₙ₌₀^∞ arⁿ = a/(1−r) &nbsp;&nbsp;&nbsp;if |r| &lt; 1</p>
      <p style="color:#9ca3af;margin-bottom:8px;"><strong style="color:var(--text);">Diverges</strong> if |r| ≥ 1.</p>
      <p style="color:#9ca3af;margin-bottom:8px;"><strong style="color:var(--text);">Example:</strong> Σₙ₌₀^∞ (1/2)ⁿ = 1 + 1/2 + 1/4 + 1/8 + ⋯ = 1/(1−1/2) = 2</p>
      <p style="color:#9ca3af;"><strong style="color:var(--text);">Intuition:</strong> If you keep adding half the remaining distance, you eventually cover the full distance — but only if you are shrinking fast enough (|r| &lt; 1).</p>
    </div>
  </div>
</div>

<h3>The Divergence Test (n-th Term Test)</h3>
<p>The Divergence Test is the first thing to check. If the terms don't go to zero, the series cannot converge. This test only proves divergence — passing it (terms → 0) does not prove convergence. The harmonic series Σ 1/n is the classic counterexample: terms → 0 but the series still diverges.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">TESTS — Major Convergence Tests Summary</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-size:0.9rem;line-height:2.2;">
      <div style="margin-bottom:16px;">
        <strong style="color:#a7f3d0;">Divergence Test:</strong>
        <div style="color:#9ca3af;font-family:'JetBrains Mono',monospace;">If lim aₙ ≠ 0, then Σaₙ DIVERGES. (Converse is FALSE)</div>
      </div>
      <div style="margin-bottom:16px;">
        <strong style="color:#a7f3d0;">Integral Test:</strong>
        <div style="color:#9ca3af;font-family:'JetBrains Mono',monospace;">If f is positive, decreasing, continuous and aₙ = f(n), then Σaₙ and ∫₁^∞ f(x)dx converge/diverge together.</div>
      </div>
      <div style="margin-bottom:16px;">
        <strong style="color:#a7f3d0;">p-Series Test:</strong>
        <div style="color:#9ca3af;font-family:'JetBrains Mono',monospace;">Σ 1/nᵖ converges iff p &gt; 1. Diverges if p ≤ 1.</div>
        <div style="color:#9ca3af;font-family:'JetBrains Mono',monospace;">(p=1: harmonic series, diverges. p=2: π²/6, converges.)</div>
      </div>
      <div style="margin-bottom:16px;">
        <strong style="color:#a7f3d0;">Ratio Test:</strong>
        <div style="color:#9ca3af;font-family:'JetBrains Mono',monospace;">Let L = lim |aₙ₊₁/aₙ|. If L &lt; 1 → converges. L &gt; 1 → diverges. L = 1 → inconclusive.</div>
        <div style="color:#9ca3af;font-size:0.8rem;">(Best for series with n! or nⁿ)</div>
      </div>
      <div style="margin-bottom:16px;">
        <strong style="color:#a7f3d0;">Comparison Test:</strong>
        <div style="color:#9ca3af;font-family:'JetBrains Mono',monospace;">If 0 ≤ aₙ ≤ bₙ: Σbₙ converges → Σaₙ converges. Σaₙ diverges → Σbₙ diverges.</div>
      </div>
      <div>
        <strong style="color:#a7f3d0;">Alternating Series Test (Leibniz):</strong>
        <div style="color:#9ca3af;font-family:'JetBrains Mono',monospace;">Σ(−1)ⁿbₙ converges if: bₙ &gt; 0, bₙ is decreasing, and lim bₙ = 0.</div>
      </div>
    </div>
  </div>
</div>

<h3>Absolute vs Conditional Convergence</h3>
<p>A series Σaₙ is <strong>absolutely convergent</strong> if Σ|aₙ| converges. It is <strong>conditionally convergent</strong> if Σaₙ converges but Σ|aₙ| diverges. Absolute convergence is stronger — the famous Riemann Rearrangement Theorem shows that a conditionally convergent series can be rearranged to converge to any real number (or diverge), which is why absolute convergence is the "safe" form.</p>

<div class="math-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — Absolute vs Conditional Convergence</span>
  </div>
  <div style="padding:20px;">
    <div style="color:#e5e7eb;font-family:'JetBrains Mono',monospace;font-size:0.9rem;line-height:2.2;">
      <div><span style="color:#a7f3d0;">Absolutely Convergent:</span> Σ|aₙ| converges → Σaₙ converges absolutely</div>
      <div><span style="color:#fcd34d;">Conditionally Convergent:</span> Σaₙ converges but Σ|aₙ| diverges</div>
      <div style="margin-top:12px;color:#9ca3af;font-size:0.85rem;border-top:1px solid var(--border);padding-top:12px;">
        <strong style="color:var(--text);">Example:</strong> The alternating harmonic series Σ(−1)ⁿ⁺¹/n = 1 − 1/2 + 1/3 − 1/4 + ⋯ converges to ln(2) [conditionally]. But Σ 1/n (the harmonic series) diverges. So the alternating harmonic series is conditionally convergent, not absolutely convergent.
      </div>
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mathModule->id,
            'title'       => '4.10 Infinite Series & Convergence Tests',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L4_10', [
                ['q' => 'A series Σaₙ converges if and only if:', 'opts' => ['aₙ → 0 as n → ∞', 'The partial sums Sₙ form a convergent sequence', 'aₙ is always positive', 'The terms are decreasing'], 'ans' => 1, 'exp' => 'By definition, a series converges iff the sequence of partial sums Sₙ = a₁ + ⋯ + aₙ converges to a finite limit S. The sum of the series is defined to be that limit.'],
                ['q' => 'For the geometric series Σ arⁿ, which condition guarantees convergence?', 'opts' => ['|r| > 1', 'r > 0', '|r| < 1', 'a > 0'], 'ans' => 2, 'exp' => 'The geometric series converges iff |r| < 1, and its sum is a/(1−r). If |r| ≥ 1 the terms do not go to zero so the series diverges.'],
                ['q' => 'The Divergence Test says: if lim aₙ ≠ 0, then the series:', 'opts' => ['Converges absolutely', 'Diverges', 'Converges conditionally', 'Has no conclusion'], 'ans' => 1, 'exp' => 'The Divergence Test: lim aₙ ≠ 0 ⟹ Σaₙ diverges. IMPORTANT: The converse is FALSE. If lim aₙ = 0, the series might still diverge (e.g., the harmonic series Σ1/n has terms → 0 but diverges).'],
                ['q' => 'The p-series Σ 1/nᵖ converges when:', 'opts' => ['p < 1', 'p = 1', 'p > 1', 'p > 0'], 'ans' => 2, 'exp' => 'The p-series test: Σ 1/nᵖ converges iff p > 1. The boundary case p = 1 gives the harmonic series, which diverges. For p = 2: Σ 1/n² = π²/6 (Euler\'s famous Basel problem).'],
                ['q' => 'A series that converges but does NOT converge absolutely is called:', 'opts' => ['Divergent', 'Absolutely convergent', 'Conditionally convergent', 'Alternating convergent'], 'ans' => 2, 'exp' => 'Conditional convergence means Σaₙ converges but Σ|aₙ| diverges. The alternating harmonic series Σ(−1)ⁿ⁺¹/n is the classic example: it converges to ln(2), but the absolute value series Σ1/n (harmonic) diverges.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 4.11 — Final Exam: Mathematical Analysis I
        // ══════════════════════════════════════════════════════════════

        $allFinalQuestions = [
            // Real Numbers
            ['q' => 'The Completeness Axiom states that every non-empty subset of ℝ that is bounded above has a:', 'opts' => ['Minimum', 'Maximum', 'Supremum in ℝ', 'Limit in ℚ'], 'ans' => 2, 'exp' => 'The Completeness (Least Upper Bound) Axiom: every non-empty set in ℝ bounded above has a supremum (least upper bound) IN ℝ. This is what distinguishes ℝ from ℚ and is the foundation of all analysis.'],
            ['q' => 'Which inequality is the Triangle Inequality?', 'opts' => ['|x − y| = |x| − |y|', '|x + y| ≤ |x| + |y|', '|x + y| ≥ |x| + |y|', '|xy| = |x| + |y|'], 'ans' => 1, 'exp' => 'The Triangle Inequality: |x + y| ≤ |x| + |y|. It is named so because in geometry, the length of one side of a triangle is at most the sum of the other two sides.'],
            // Sequences
            ['q' => 'The ε-N definition of lim aₙ = L requires: for all ε > 0, there exists N such that:', 'opts' => ['aₙ = L for all n > N', '|aₙ − L| < ε for all n > N', '|aₙ| < ε for all n > N', '|aₙ − L| > ε for some n > N'], 'ans' => 1, 'exp' => 'The ε-N definition: ∀ε>0, ∃N ∈ ℕ s.t. ∀n>N: |aₙ − L| < ε. The absolute value condition |aₙ − L| < ε means the term is within ε of the limit L.'],
            ['q' => 'The Monotone Convergence Theorem guarantees convergence when a sequence is:', 'opts' => ['Monotone and unbounded', 'Bounded but oscillating', 'Monotone and bounded', 'Positive and bounded'], 'ans' => 2, 'exp' => 'MCT: monotone AND bounded together guarantee convergence. Monotone alone does not suffice (e.g., 1,2,3,… is increasing but diverges). Bounded alone does not suffice (e.g., (−1)ⁿ is bounded but diverges).'],
            // Limits of Functions
            ['q' => 'The ε-δ definition of lim(x→a) f(x) = L requires:', 'opts' => ['∀ε>0, ∃δ>0 s.t. 0<|x−a|<δ ⟹ |f(x)−L|<ε', '∀δ>0, ∃ε>0 s.t. |f(x)−L|<δ ⟹ |x−a|<ε', '|x−a|<δ ⟹ f(x)=L', '|f(x)−L|<ε for all x near a'], 'ans' => 0, 'exp' => 'The correct ε-δ definition: for every ε>0, there exists δ>0 such that whenever 0<|x−a|<δ (x is near a but ≠a), we have |f(x)−L|<ε (f(x) is near L). The quantifier order ∀ε ∃δ is critical.'],
            ['q' => 'The Intermediate Value Theorem applies when f is:', 'opts' => ['Differentiable on (a,b)', 'Continuous on [a,b]', 'Monotone on [a,b]', 'Positive on (a,b)'], 'ans' => 1, 'exp' => 'IVT requires continuity on the CLOSED interval [a,b]. Differentiability is stronger than needed and monotonicity is not required. The theorem fails if continuity fails (e.g., a function with a jump discontinuity can skip values).'],
            // Derivatives
            ['q' => 'f is differentiable at a implies f is:', 'opts' => ['Monotone near a', 'Continuous at a', 'Bounded on ℝ', 'Differentiable everywhere'], 'ans' => 1, 'exp' => 'Differentiability at a implies continuity at a (but not vice versa). This is proved by showing lim(x→a)[f(x)−f(a)] = f′(a)·0 = 0, which gives lim f(x) = f(a).'],
            ['q' => 'Using the Chain Rule, d/dx[cos(x³)] =', 'opts' => ['−sin(x³)', '3x²cos(x³)', '−3x²sin(x³)', 'cos(3x²)'], 'ans' => 2, 'exp' => 'Chain Rule: d/dx[f(g(x))] = f′(g(x))·g′(x). Here f(u) = cos(u) → f′(u) = −sin(u), and g(x) = x³ → g′(x) = 3x². So: −sin(x³)·3x² = −3x²sin(x³).'],
            // MVT and Applications
            ['q' => 'The Mean Value Theorem guarantees c ∈ (a,b) such that:', 'opts' => ['f′(c) = f(a) + f(b)', 'f(c) = [f(a)+f(b)]/2', 'f′(c) = [f(b)−f(a)]/(b−a)', 'f′(c) = 0'], 'ans' => 2, 'exp' => 'MVT: ∃c ∈ (a,b) s.t. f′(c) = [f(b)−f(a)]/(b−a). The right side is the slope of the secant line (average rate of change). The MVT guarantees the instantaneous rate equals the average rate somewhere.'],
            ['q' => 'By the Second Derivative Test, if f′(c) = 0 and f″(c) < 0, then c is a:', 'opts' => ['Local minimum', 'Local maximum', 'Saddle point', 'Inflection point'], 'ans' => 1, 'exp' => 'If f′(c) = 0 (critical point) and f″(c) < 0 (concave down), then c is a local maximum. The graph curves downward at c, creating a peak. If f″(c) > 0 (concave up), it would be a local minimum.'],
            // L'Hopital
            ["q" => "L'Hôpital's Rule converts lim f(x)/g(x) [0/0 form] to:", 'opts' => ["lim f(x)·g′(x)", "lim (f/g)′(x)", "lim f′(x)/g′(x)", "lim f′(x)·g(x)"], 'ans' => 2, 'exp' => "L'Hôpital's Rule: lim f/g = lim f′/g′ (under the 0/0 or ∞/∞ condition). This is NOT the Quotient Rule derivative (f/g)′. You differentiate numerator and denominator separately."],
            ['q' => 'What is lim(x→∞) ln(x)/x?', 'opts' => ['1', '∞', '0', 'ln(∞)'], 'ans' => 2, 'exp' => "This is ∞/∞ form. L'Hôpital: lim (1/x)/1 = lim 1/x = 0. This proves x grows faster than ln(x) — the logarithm always 'loses' to any positive power of x."],
            // Integrals
            ['q' => 'The definite integral ∫ₐᵇ f(x)dx is defined as:', 'opts' => ['F(a) − F(b) for any antiderivative F', 'The limit of Riemann sums as n → ∞', 'The average value of f on [a,b]', 'f′(b) − f′(a)'], 'ans' => 1, 'exp' => 'The definite integral is defined as lim(n→∞) Σf(xᵢ*)Δxᵢ — the limit of Riemann sums. The FTC Part 2 then gives us a practical way to compute this limit using antiderivatives.'],
            ['q' => 'FTC Part 2 states: ∫ₐᵇ f(x)dx =', 'opts' => ['F′(b) − F′(a)', 'F(b) + F(a)', 'F(b) − F(a)', 'f(b) − f(a)'], 'ans' => 2, 'exp' => 'FTC Part 2 (Evaluation Theorem): ∫ₐᵇ f(x)dx = F(b) − F(a) where F is any antiderivative of f. The constant C always cancels in the subtraction.'],
            // Integration techniques
            ['q' => 'For ∫ sin(x)·cos(x) dx, which substitution simplifies the integral?', 'opts' => ['u = sin(x) + cos(x)', 'u = sin(x)', 'u = x', 'u = sin(x)·cos(x)'], 'ans' => 1, 'exp' => 'Let u = sin(x), then du = cos(x)dx. The integral becomes ∫ u du = u²/2 + C = sin²(x)/2 + C. Alternatively u = cos(x) works too, giving −cos²(x)/2 + C (these differ by a constant).'],
            ['q' => 'Integration by Parts is derived from which differentiation rule?', 'opts' => ['Chain Rule', 'Quotient Rule', 'Power Rule', 'Product Rule'], 'ans' => 3, 'exp' => 'IBP is derived from the Product Rule: d(uv) = u dv + v du → ∫u dv = uv − ∫v du. The Product Rule differentiates products; Integration by Parts integrates products.'],
            // Series
            ['q' => 'The geometric series Σₙ₌₀^∞ rⁿ converges to 1/(1−r) when:', 'opts' => ['r > 0', 'r < 1', '|r| < 1', '|r| > 1'], 'ans' => 2, 'exp' => 'The geometric series converges iff |r| < 1 (the common ratio has absolute value less than 1). The sum is 1/(1−r). If |r| ≥ 1, the terms do not decrease to zero and the series diverges.'],
            ['q' => 'The p-series Σ 1/n² (p = 2):', 'opts' => ['Diverges', 'Converges to π²/6', 'Converges to 1', 'Converges to ln(2)'], 'ans' => 1, 'exp' => 'For the p-series, p = 2 > 1 so it converges. The exact sum Σ 1/n² = π²/6 was first proved by Euler in 1734 (the Basel problem). This is a famous result in number theory and analysis.'],
            ['q' => 'The Ratio Test: L = lim |aₙ₊₁/aₙ|. Which conclusion is correct?', 'opts' => ['L < 1 → diverges; L > 1 → converges', 'L < 1 → converges; L > 1 → diverges; L = 1 → inconclusive', 'L = 0 → diverges; L = 1 → converges', 'L < 1 → conditionally converges'], 'ans' => 1, 'exp' => 'Ratio Test: L < 1 → absolute convergence; L > 1 → divergence; L = 1 → inconclusive (need another test). The Ratio Test is best for series involving factorials or exponentials.'],
            ['q' => 'A series is conditionally convergent if:', 'opts' => ['Σaₙ diverges and Σ|aₙ| diverges', 'Σaₙ converges and Σ|aₙ| converges', 'Σaₙ converges but Σ|aₙ| diverges', 'Σ|aₙ| converges but Σaₙ diverges'], 'ans' => 2, 'exp' => 'Conditional convergence: Σaₙ converges (the series with signs converges) BUT Σ|aₙ| diverges (removing signs makes it diverge). The alternating harmonic series Σ(−1)ⁿ⁺¹/n = ln(2) is the canonical example.'],
            ['q' => 'The Divergence Test concludes divergence ONLY when:', 'opts' => ['lim aₙ = 0', 'lim aₙ ≠ 0', 'The series is alternating', 'aₙ is always positive'], 'ans' => 1, 'exp' => 'The Divergence Test: if lim aₙ ≠ 0, the series diverges. If lim aₙ = 0, the test is INCONCLUSIVE — the series may converge or diverge. The harmonic series (terms → 0, series diverges) shows the converse is false.'],
        ];

        $finalContent = <<<'HTML'
<div id="org-lock-screen" style="text-align:center;padding:60px 20px;">
    <h2 style="color:var(--text);">🔒 Final Examination</h2>
    <p style="color:var(--muted);max-width:400px;margin:0 auto 16px;">The Module 4 Final Exam is available exclusively to enrolled students.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organisation.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 4: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 4.1 through 4.10 — real numbers, sequences, limits of functions, continuity, derivatives, the MVT, L'Hôpital's Rule, the definite integral, the Fundamental Theorem of Calculus, integration techniques, and infinite series. Good luck!</p>
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
            'module_id'   => $mathModule->id,
            'title'       => '4.11 Final Exam: Mathematical Analysis I Mastery',
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