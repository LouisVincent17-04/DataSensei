<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module5LessonsSeeder
 * Seeds lessons for Module 5: Methods of Proof.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module5LessonsSeeder
 */
class Module5LessonsSeeder extends Seeder
{
    public function run()
    {
        $module = Module::where('order_index', 5)->firstOrFail();
        Lesson::where('module_id', $module->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 5.1 — Introduction to Mathematical Proof
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What Is a Mathematical Proof?</h2>
<p>A <strong>mathematical proof</strong> is a rigorous, logical argument that establishes the truth of a mathematical statement beyond any doubt. Unlike scientific experiments — which provide evidence — a proof provides <em>certainty</em>. Once a statement is proven, it is true forever, in every possible universe where the axioms hold. This is what distinguishes mathematics from all other disciplines.</p>

<h3>Why Proofs Matter</h3>
<p>Proofs are not merely academic exercises. They are the foundation of all reliable software, cryptography, database design, algorithm analysis, and formal verification. When you prove that a sorting algorithm runs in O(n log n) worst case, you are not guessing — you are <em>guaranteeing</em> it. Every theorem your programs rely on was once proven by someone using the exact techniques in this module.</p>

<h3>Key Terminology</h3>
<p>Before writing any proof, you must be fluent in the vocabulary mathematicians use:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DEFINITIONS — Core Proof Vocabulary</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">Axiom</span>       — A statement accepted as true without proof (the starting rules).
<span style="color:#c4b5fd;">Definition</span>   — A precise, agreed-upon meaning for a mathematical term.
<span style="color:#c4b5fd;">Theorem</span>      — An important statement that has been proven to be true.
<span style="color:#c4b5fd;">Lemma</span>        — A "helper" theorem proven specifically to support a larger theorem.
<span style="color:#c4b5fd;">Corollary</span>    — A theorem that follows easily and directly from another theorem.
<span style="color:#c4b5fd;">Conjecture</span>   — A statement believed to be true but not yet proven.
<span style="color:#c4b5fd;">Proposition</span>  — A smaller, self-contained true statement (less grand than a theorem).
<span style="color:#c4b5fd;">Proof</span>        — A logical argument that a statement is universally true.</div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Example: Fermat's Last Theorem</span>
Conjecture (1637): No integers a, b, c > 0 satisfy aⁿ + bⁿ = cⁿ for n > 2.
Theorem   (1995): Andrew Wiles proved it after 358 years.
A conjecture becomes a theorem ONLY after a complete proof exists.</div>
  </div>
</div>

<h3>The Anatomy of a Proof</h3>
<p>Every well-written proof contains three parts: the <strong>statement</strong> (what you are proving), the <strong>hypotheses</strong> (what you are allowed to assume), and the <strong>conclusion</strong> (what you are establishing). The logical chain connecting hypotheses to conclusion — using definitions, axioms, and previously proven theorems — is the proof itself.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">ANATOMY — A Simple Proof Dissected</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span>  If n is an even integer, then n² is also an even integer.

<span style="color:#a7f3d0;">Proof:</span>
  <span style="color:#6b7280;">/* Step 1: Unpack the hypothesis using the definition of "even" */</span>
  Assume n is an even integer.
  By definition, there exists an integer k such that n = 2k.

  <span style="color:#6b7280;">/* Step 2: Perform algebra to reach the conclusion */</span>
  Then  n² = (2k)² = 4k² = 2(2k²).

  <span style="color:#6b7280;">/* Step 3: Recognize the conclusion matches the definition */</span>
  Let m = 2k². Then n² = 2m, where m is an integer.
  By definition, n² is even.                              <span style="color:#fcd34d;">∎</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Key Observations</span>
• The symbol ∎ (or QED) marks the end of a proof.
• Every step must be justified by a definition, axiom, or prior theorem.
• We introduced a NEW variable k to represent what "even" means.
• The final sentence explicitly ties back to the definition.</div>
  </div>
</div>

<h3>What Counts as Valid Justification?</h3>
<p>In a formal proof, you may not write a step unless you can cite <em>why</em> it is true. Valid citations include: definitions (e.g., "by definition of odd"), axioms (e.g., closure of integers under multiplication), previously proven theorems (e.g., "by the Distributive Law"), and algebraic identities. Saying "it is obvious" or "you can see that" is <strong>never</strong> valid justification in a rigorous proof.</p>

<h3>The Five Major Methods of Proof</h3>
<p>This module covers five fundamental strategies. Each is appropriate for different types of statements:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">OVERVIEW — The Five Methods</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">1. Direct Proof</span>          Assume p, derive q step-by-step.
<span style="color:#93c5fd;">2. Proof by Contrapositive</span> Prove ¬q → ¬p instead of p → q.
<span style="color:#93c5fd;">3. Proof by Contradiction</span>  Assume ¬p, derive a contradiction → p must be true.
<span style="color:#93c5fd;">4. Proof by Cases</span>          Divide domain into exhaustive cases, prove each.
<span style="color:#93c5fd;">5. Mathematical Induction</span>  Base case + inductive step → true for all naturals.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '5.1 Introduction to Mathematical Proof',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L5_1', [
                ['q' => 'What is the difference between a theorem and a conjecture?', 'opts' => ['A theorem is believed true; a conjecture is proven true', 'A conjecture is believed true but unproven; a theorem has a complete proof', 'A theorem applies only to numbers; a conjecture applies to all math', 'There is no difference — the terms are interchangeable'], 'ans' => 1, 'exp' => 'A conjecture is a statement believed to be true but lacking a formal proof. Once a proof is provided, it becomes a theorem. Fermat\'s Last Theorem was a conjecture for 358 years.'],
                ['q' => 'What role does a lemma play in mathematics?', 'opts' => ['It is a statement that disproves a theorem', 'It is a helper theorem used to support a larger proof', 'It is an axiom that cannot be proven', 'It is a synonym for corollary'], 'ans' => 1, 'exp' => 'A lemma is a proven statement whose primary purpose is to serve as a stepping stone toward a larger, more important theorem. It is proven separately to keep the main proof clean and readable.'],
                ['q' => 'In the proof that "if n is even, n² is even," why is a new variable k introduced?', 'opts' => ['To confuse the reader', 'To instantiate the definition of "even" and allow algebraic manipulation', 'Because n² requires a new name', 'To apply the axiom of choice'], 'ans' => 1, 'exp' => 'The definition of an even integer states n = 2k for some integer k. Introducing k converts an abstract property ("even") into a concrete algebraic expression that can be squared and manipulated.'],
                ['q' => 'Which of the following is NOT a valid justification in a formal proof?', 'opts' => ['By definition of prime number', 'By the Distributive Law of multiplication', 'It is obvious from inspection', 'By Theorem 3.2 (previously proven)'], 'ans' => 2, 'exp' => '"It is obvious" is never acceptable in a formal proof. Every step must cite a specific definition, axiom, or previously proven result. Rigor demands explicit justification.'],
                ['q' => 'What does the symbol ∎ (or QED) signify at the end of a proof?', 'opts' => ['The proof is incomplete and needs review', 'The proof is finished — the conclusion has been established', 'The statement is a conjecture', 'The author is uncertain about the result'], 'ans' => 1, 'exp' => '∎ (tombstone symbol) or QED (Latin: quod erat demonstrandum, "which was to be demonstrated") marks the formal end of a proof, signaling that the conclusion has been fully established.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 5.2 — Direct Proof
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Direct Proof</h2>
<p>The <strong>direct proof</strong> is the most natural and straightforward method of proof. To prove an implication of the form <em>p → q</em> ("if p then q") directly, you assume that the hypothesis <em>p</em> is true and then use a sequence of logical deductions — each justified by a definition, axiom, or theorem — to arrive at the conclusion <em>q</em>. It is the mathematical equivalent of following a recipe step by step to reach a guaranteed outcome.</p>

<h3>The Direct Proof Strategy</h3>
<p>The strategy is simple in principle but requires careful attention to definitions. The most common mistake students make is failing to <em>fully unpack</em> the definitions involved. The phrase "n is even" is not just a label — it carries a precise algebraic meaning (∃k ∈ ℤ such that n = 2k) that you must write explicitly before doing any algebra.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — Direct Proof Template</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Goal:</span> Prove that p → q.

<span style="color:#a7f3d0;">Step 1:</span> <span style="color:#6b7280;">"Assume p is true."</span>
<span style="color:#a7f3d0;">Step 2:</span> Unpack the definition(s) embedded in p.
<span style="color:#a7f3d0;">Step 3:</span> Perform algebra / logical deductions.
<span style="color:#a7f3d0;">Step 4:</span> Arrive at q.
<span style="color:#a7f3d0;">Step 5:</span> <span style="color:#6b7280;">"Therefore q is true." ∎</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Key Definitions to Know</span>
Even integer n:  ∃ k ∈ ℤ  such that  n = 2k
Odd integer n:   ∃ k ∈ ℤ  such that  n = 2k + 1
Divisibility:    a | b  means  ∃ k ∈ ℤ  such that  b = ak</div>
  </div>
</div>

<h3>Worked Example 1: Sum of Two Even Integers</h3>
<p>Let's build a complete, rigorous direct proof from scratch. Notice how each step references the definitions above.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — Sum of Two Even Integers Is Even</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span> If m and n are even integers, then m + n is even.

<span style="color:#a7f3d0;">Proof:</span>
  Assume m and n are even integers.                    <span style="color:#6b7280;">[Assume p]</span>

  By definition of even, there exist integers j and k  <span style="color:#6b7280;">[Unpack definitions]</span>
  such that   m = 2j   and   n = 2k.

  Then:
    m + n  =  2j + 2k                                  <span style="color:#6b7280;">[Substitution]</span>
           =  2(j + k)                                 <span style="color:#6b7280;">[Factor — Distributive Law]</span>

  Let p = j + k. Since j and k are integers, p is      <span style="color:#6b7280;">[Closure of ℤ under +]</span>
  also an integer (integers are closed under addition).

  Therefore m + n = 2p, where p is an integer.
  By definition of even, m + n is even.                <span style="color:#6b7280;">[Conclude q]</span> <span style="color:#fcd34d;">∎</span></div>
  </div>
</div>

<h3>Worked Example 2: Product of Two Odd Integers</h3>
<p>This proof requires tracking two separate arbitrary odd integers. We must use <em>different</em> variable names (j and k, not both k) to avoid an error that would assume the integers are equal.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — Product of Two Odd Integers Is Odd</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span> If m and n are odd integers, then mn is odd.

<span style="color:#a7f3d0;">Proof:</span>
  Assume m and n are odd integers.

  By definition of odd:
    m = 2j + 1   for some integer j
    n = 2k + 1   for some integer k         <span style="color:#6b7280;">[DIFFERENT variables!]</span>

  Then:
    mn = (2j + 1)(2k + 1)
       = 4jk + 2j + 2k + 1                 <span style="color:#6b7280;">[FOIL / expand]</span>
       = 2(2jk + j + k) + 1                <span style="color:#6b7280;">[Factor out 2]</span>

  Let p = 2jk + j + k. Since j and k are integers,
  p is an integer (closed under + and ×).

  Therefore mn = 2p + 1, where p ∈ ℤ.
  By definition of odd, mn is odd.                     <span style="color:#fcd34d;">∎</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Common Mistake to Avoid</span>
Writing m = 2k+1 AND n = 2k+1 with the SAME k
implies m = n. Always use different variable names
for independent arbitrary integers.</div>
  </div>
</div>

<h3>Worked Example 3: Divisibility Transitivity</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — Transitivity of Divisibility</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span> If a | b and b | c, then a | c.
            (Where a, b, c are integers and a ≠ 0.)

<span style="color:#a7f3d0;">Proof:</span>
  Assume a | b and b | c.

  By definition of divisibility:
    b = aj   for some integer j    (from a | b)
    c = bk   for some integer k    (from b | c)

  Substitute the first equation into the second:
    c = bk = (aj)k = a(jk)

  Let m = jk. Since j and k are integers, m ∈ ℤ
  (closed under multiplication).

  Therefore c = am, where m ∈ ℤ.
  By definition of divisibility, a | c.              <span style="color:#fcd34d;">∎</span></div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '5.2 Direct Proof',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L5_2', [
                ['q' => 'In a direct proof of p → q, what is the very first step?', 'opts' => ['Assume q is false', 'Assume p is true', 'Derive a contradiction', 'Prove the contrapositive instead'], 'ans' => 1, 'exp' => 'A direct proof begins by assuming the hypothesis p is true. You then use definitions, axioms, and theorems to logically deduce the conclusion q.'],
                ['q' => 'When proving "if m and n are odd, then mn is odd," why must you use DIFFERENT variable names (e.g., j and k) for each odd number?', 'opts' => ['It is just style — the same variable is fine', 'Using the same variable would imply m = n, which is an unintended restriction', 'The definition of odd requires two variables', 'You must use consecutive integers'], 'ans' => 1, 'exp' => 'If you write m = 2k+1 and n = 2k+1 with the same k, you are asserting m = n. Since m and n are independent arbitrary odd integers, they require independent variables j and k.'],
                ['q' => 'After expanding mn = (2j+1)(2k+1), what algebraic step is needed to show mn is odd?', 'opts' => ['Show the result is divisible by 4', 'Factor the result as 2(something) + 1', 'Show the result has no remainder when divided by 3', 'Apply the quadratic formula'], 'ans' => 1, 'exp' => 'An odd integer is one of the form 2p+1. After expanding, mn = 4jk+2j+2k+1 = 2(2jk+j+k)+1. This matches the definition of odd with p = 2jk+j+k.'],
                ['q' => 'In the divisibility proof (a|b and b|c implies a|c), why is the step "let m = jk" necessary?', 'opts' => ['To avoid division by zero', 'To give a name to the integer that witnesses a | c per the definition', 'To introduce a new variable with no purpose', 'To apply the Euclidean algorithm'], 'ans' => 1, 'exp' => 'The definition of a|c requires demonstrating an integer m such that c = am. Writing c = a(jk) shows such an m exists; naming it explicitly (m = jk) completes the link back to the definition.'],
                ['q' => 'What property of integers justifies that "jk is an integer" when j and k are integers?', 'opts' => ['The Archimedean property', 'Closure of integers under multiplication', 'The Well-Ordering Principle', 'Commutativity of addition'], 'ans' => 1, 'exp' => 'The integers are closed under multiplication: the product of any two integers is again an integer. This closure property is what allows us to assert jk ∈ ℤ and therefore name it as a valid witness.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 5.3 — Proof by Contrapositive
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Proof by Contrapositive</h2>
<p>The <strong>contrapositive</strong> of the implication <em>p → q</em> is the logically equivalent statement <em>¬q → ¬p</em>. This means: "if q is false, then p must also be false." Because an implication and its contrapositive are <em>always</em> logically equivalent, proving the contrapositive is a completely legitimate way to prove the original statement. This method is powerful when the negations of p and q are easier to work with algebraically than p and q themselves.</p>

<h3>Logical Equivalence: Why It Works</h3>
<p>The truth table below confirms that <em>p → q</em> and <em>¬q → ¬p</em> have identical truth values in every row — they are tautologically equivalent. This equivalence is not a trick; it is a fundamental law of propositional logic.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">TRUTH TABLE — p → q  ≡  ¬q → ¬p</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"> p  | q  | ¬p | ¬q | p → q | ¬q → ¬p
----+----+----+----+-------+---------
 T  | T  | F  | F  |   <span style="color:#a7f3d0;">T</span>   |    <span style="color:#a7f3d0;">T</span>
 T  | F  | F  | T  |   <span style="color:#fca5a5;">F</span>   |    <span style="color:#fca5a5;">F</span>
 F  | T  | T  | F  |   <span style="color:#a7f3d0;">T</span>   |    <span style="color:#a7f3d0;">T</span>
 F  | F  | T  | T  |   <span style="color:#a7f3d0;">T</span>   |    <span style="color:#a7f3d0;">T</span>

<span style="color:#6b7280;">The last two columns are IDENTICAL — proving the equivalence.</span></div>
  </div>
</div>

<h3>When to Use Contrapositive vs Direct Proof</h3>
<p>Use the contrapositive when the <em>conclusion q is about something NOT happening</em> (e.g., "n is not divisible," "the number is not rational") — negating such statements often gives you something positive to work with. A good heuristic: if the hypothesis is hard to unpack but the negation of the conclusion is easy, go contrapositive.</p>

<h3>Worked Example 1: Odd Square Implies Odd Integer</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — If n² is odd, then n is odd (Contrapositive)</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span>  For any integer n, if n² is odd, then n is odd.

<span style="color:#a7f3d0;">Proof by Contrapositive:</span>
  We prove the contrapositive: <span style="color:#c4b5fd;">if n is NOT odd (i.e., n is even),
  then n² is NOT odd (i.e., n² is even).</span>

  Assume n is even.
  Then there exists an integer k such that n = 2k.   <span style="color:#6b7280;">[Def. of even]</span>

  n² = (2k)² = 4k² = 2(2k²)

  Let m = 2k² ∈ ℤ. Then n² = 2m.
  By definition, n² is even (not odd).

  We have proven: n even → n² even.
  By contrapositive, n² odd → n odd.                 <span style="color:#fcd34d;">∎</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Why Not Direct?</span>
A direct proof would start "assume n² is odd" — but
then we'd need to take a square root and argue about
its parity, which is much messier. The contrapositive
"assume n is even" gives us a clean algebraic start.</div>
  </div>
</div>

<h3>Worked Example 2: Divisibility and Evenness</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — If 3n + 5 is even, then n is odd (Contrapositive)</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span>  For integer n, if 3n + 5 is even, then n is odd.

<span style="color:#a7f3d0;">Contrapositive:</span>  If n is even, then 3n + 5 is odd.

<span style="color:#a7f3d0;">Proof:</span>
  Assume n is even.
  Then n = 2k for some integer k.

  3n + 5 = 3(2k) + 5 = 6k + 5 = 6k + 4 + 1 = 2(3k + 2) + 1.

  Let m = 3k + 2 ∈ ℤ. Then 3n + 5 = 2m + 1.
  By definition, 3n + 5 is odd.

  By contrapositive: if 3n + 5 is even, then n is odd. <span style="color:#fcd34d;">∎</span></div>
  </div>
</div>

<h3>Do Not Confuse With the Converse</h3>
<p>The <strong>converse</strong> of p → q is q → p. The converse is <em>not</em> logically equivalent to the original. A statement and its converse can have different truth values. Only the <em>contrapositive</em> (¬q → ¬p) is always equivalent to the original. This is one of the most common sources of logical errors in mathematics and programming.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">COMPARISON — Original, Converse, Contrapositive, Inverse</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Original:</span>      p → q         "If n is even, then n² is even."     <span style="color:#a7f3d0;">TRUE</span>
<span style="color:#c4b5fd;">Contrapositive:</span> ¬q → ¬p       "If n² is odd, then n is odd."       <span style="color:#a7f3d0;">TRUE</span>  (≡ original)
<span style="color:#fca5a5;">Converse:</span>       q → p         "If n² is even, then n is even."     <span style="color:#a7f3d0;">TRUE</span>  (happens to be, but NOT guaranteed)
<span style="color:#fca5a5;">Inverse:</span>        ¬p → ¬q       "If n is odd, then n² is odd."       <span style="color:#a7f3d0;">TRUE</span>  (≡ converse, not original)

<span style="color:#6b7280;">RULE: Only the CONTRAPOSITIVE is always equivalent to the original.</span></div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '5.3 Proof by Contrapositive',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L5_3', [
                ['q' => 'What is the contrapositive of "if n is even, then n² is divisible by 4"?', 'opts' => ['If n² is divisible by 4, then n is even', 'If n is odd, then n² is not divisible by 4', 'If n² is NOT divisible by 4, then n is NOT even (i.e., n is odd)', 'If n is not odd, then n² is divisible by 4'], 'ans' => 2, 'exp' => 'The contrapositive of p → q is ¬q → ¬p. Here p = "n is even" and q = "n² is divisible by 4," so ¬q → ¬p = "if n² is not divisible by 4, then n is not even (i.e., n is odd)."'],
                ['q' => 'Why is the contrapositive logically equivalent to the original implication?', 'opts' => ['Because both use the same variables', 'Because they have identical truth tables — they are true and false in exactly the same cases', 'Because mathematicians defined them to be equivalent', 'Because the conclusion is always true'], 'ans' => 1, 'exp' => 'The truth table for p → q and ¬q → ¬p produces identical results in all four cases (TT, TF, FT, FF). This tautological equivalence means proving one is exactly as valid as proving the other.'],
                ['q' => 'What is the logical relationship between an implication and its CONVERSE?', 'opts' => ['They are always equivalent', 'They are always opposite in truth value', 'They are independent — the converse can be true or false regardless of the original', 'The converse is always false'], 'ans' => 2, 'exp' => 'The converse (q → p) is NOT logically equivalent to the original (p → q). A statement can be true while its converse is false, or both can be true simultaneously. They must be proven independently.'],
                ['q' => 'In proving "if 3n + 5 is even then n is odd" by contrapositive, what do you actually prove?', 'opts' => ['If n is odd, then 3n + 5 is odd', 'If n is even, then 3n + 5 is odd', 'If 3n + 5 is odd, then n is even', 'If n is even, then 3n + 5 is even'], 'ans' => 1, 'exp' => 'The contrapositive of "if 3n+5 is even then n is odd" is "if n is NOT odd (i.e., even) then 3n+5 is NOT even (i.e., odd)." You assume n is even and show 3n+5 must be odd.'],
                ['q' => 'A student writes: "I proved the converse, so I have proven the theorem." What is wrong?', 'opts' => ['Nothing is wrong — converse and original are equivalent', 'The converse is equivalent to the inverse, not the original theorem', 'The converse is not logically equivalent to the original; a separate proof is required', 'The student should have used the contrapositive of the converse instead'], 'ans' => 2, 'exp' => 'The converse (q → p) is logically independent of the original (p → q). Proving the converse gives you no information about the truth of the original. Only the contrapositive (¬q → ¬p) is equivalent.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 5.4 — Proof by Contradiction
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Proof by Contradiction (Reductio ad Absurdum)</h2>
<p><strong>Proof by contradiction</strong> — also called <em>reductio ad absurdum</em> (Latin: "reduction to absurdity") — is one of the most powerful and elegant tools in mathematics. The idea: to prove a proposition P, you temporarily assume that P is <em>false</em>. Then, using strict logical reasoning, you derive a statement that is demonstrably false — a <em>contradiction</em>. Since all your reasoning was valid, the only possible culprit is your assumption that P was false. Therefore P must be true.</p>

<h3>The Logical Foundation</h3>
<p>This method relies on the <strong>Law of Non-Contradiction</strong> (a statement cannot be both true and false) and the <strong>Law of Excluded Middle</strong> (every proposition is either true or false — there is no middle ground). Together, these laws guarantee that if assuming ¬P leads to a contradiction, then P must be true.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — Contradiction Proof Template</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Goal:</span> Prove proposition P.

<span style="color:#a7f3d0;">Step 1:</span> <span style="color:#6b7280;">"Assume for contradiction that ¬P is true."</span>
<span style="color:#a7f3d0;">Step 2:</span> Reason logically from ¬P using definitions/theorems.
<span style="color:#a7f3d0;">Step 3:</span> Derive a statement C that is CLEARLY FALSE
          (violates a known theorem, definition, or prior assumption).
<span style="color:#a7f3d0;">Step 4:</span> <span style="color:#6b7280;">"This is a contradiction. Therefore our assumption was wrong,
          and P must be true." ∎</span>

<span style="color:#fca5a5;">Common contradictions used:</span>
  • An integer is both even and odd
  • r > r  (irreflexivity of <)
  • p/q is in lowest terms AND both p and q are even
  • A set is both empty and non-empty
  • A proved theorem is false</div>
  </div>
</div>

<h3>Classic Example: √2 is Irrational</h3>
<p>This is arguably the most famous proof by contradiction in history, attributed to the ancient Greeks. It is breathtaking in its simplicity and finality.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — √2 is Irrational</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span> √2 is irrational (cannot be expressed as p/q with p,q ∈ ℤ, q≠0).

<span style="color:#a7f3d0;">Proof by Contradiction:</span>
  Assume for contradiction that √2 IS rational.
  Then √2 = p/q for integers p, q with q ≠ 0,
  and where the fraction p/q is in <span style="color:#c4b5fd;">lowest terms</span>      <span style="color:#6b7280;">[gcd(p,q)=1]</span>
  (i.e., p and q share no common factors).

  Squaring both sides:
    2 = p²/q²   →   p² = 2q²

  Since p² = 2q², p² is even.
  By Lemma (if n² is even, n is even): p is even.  <span style="color:#6b7280;">[Previously proven]</span>

  Since p is even, p = 2k for some integer k.
    p² = (2k)² = 4k²
    But also p² = 2q²

  Therefore: 2q² = 4k²   →   q² = 2k²

  So q² is even → q is even (same lemma).

  <span style="color:#fca5a5;">CONTRADICTION:</span> p is even AND q is even.
  But we assumed gcd(p, q) = 1 (lowest terms).
  If both are even, gcd(p,q) ≥ 2. ↯

  Our assumption was wrong. Therefore √2 is irrational. <span style="color:#fcd34d;">∎</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Historical Note</span>
The Pythagoreans discovered this proof around 500 BCE
and reportedly found it so disturbing — it proved their
universe of ratios was incomplete — that they swore
members to secrecy. The discoverer, Hippasus, is said
to have been drowned for revealing it.</div>
  </div>
</div>

<h3>Example 2: There Are Infinitely Many Primes</h3>
<p>Euclid's proof from ~300 BCE remains one of the most elegant in all of mathematics.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — Infinitely Many Primes (Euclid)</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span> There are infinitely many prime numbers.

<span style="color:#a7f3d0;">Proof by Contradiction:</span>
  Assume for contradiction there are finitely many primes.
  List them all: p₁, p₂, p₃, ..., pₙ.

  Construct the number:  N = (p₁ · p₂ · p₃ · ... · pₙ) + 1

  Case 1: N is prime.
    N is a prime not on our list (N > all pᵢ). ↯

  Case 2: N is composite.
    N has a prime factor, call it p.
    p must be one of p₁, ..., pₙ (our "complete" list).
    But p | N and p | (p₁·p₂·...·pₙ).
    Therefore p | N − (p₁·...·pₙ) = 1.
    So p | 1, which is impossible for a prime p ≥ 2. ↯

  Both cases lead to contradiction.
  Therefore infinitely many primes exist.               <span style="color:#fcd34d;">∎</span></div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '5.4 Proof by Contradiction',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L5_4', [
                ['q' => 'What is the first step in a proof by contradiction?', 'opts' => ['Prove the contrapositive', 'Assume the proposition P is true and derive the conclusion', 'Assume the NEGATION of the proposition (¬P) and derive a contradiction', 'Find a counterexample to P'], 'ans' => 2, 'exp' => 'In a proof by contradiction, you begin by assuming the proposition P is FALSE (i.e., assuming ¬P). You then reason logically until you reach an absurd or impossible result, proving that ¬P cannot hold, so P must be true.'],
                ['q' => 'In the proof that √2 is irrational, what is the actual CONTRADICTION reached?', 'opts' => ['√2 is shown to equal exactly 1.41421...', 'p and q are both even, contradicting that p/q was in lowest terms (gcd = 1)', 'The equation p² = 2q² has no integer solutions', 'p is shown to be negative'], 'ans' => 1, 'exp' => 'We assumed p/q is in lowest terms (gcd(p,q) = 1). The proof derives that both p and q must be even, meaning gcd(p,q) ≥ 2. This directly contradicts our assumption of lowest terms.'],
                ['q' => 'In Euclid\'s prime proof, what is the number N = (p₁·p₂·...·pₙ) + 1 designed to show?', 'opts' => ['N is always prime itself', 'N cannot be divided by any prime on the finite list, contradicting that the list is complete', 'N is the largest prime', 'N proves the list has exactly n primes'], 'ans' => 1, 'exp' => 'N is constructed so that dividing it by any prime pᵢ on our "complete" list always leaves a remainder of 1. Therefore no prime on the list divides N — but every integer > 1 must have a prime factor. This contradiction shows the list cannot be complete.'],
                ['q' => 'Which two laws of classical logic underpin proof by contradiction?', 'opts' => ['The Distributive Law and the Associative Law', 'The Law of Non-Contradiction and the Law of Excluded Middle', 'De Morgan\'s Laws and the Commutative Law', 'The Pigeonhole Principle and the Well-Ordering Principle'], 'ans' => 1, 'exp' => 'Proof by contradiction relies on: (1) the Law of Non-Contradiction — a statement cannot be both true and false simultaneously; and (2) the Law of Excluded Middle — every proposition is either true or false. Together they guarantee that contradicting ¬P forces P to be true.'],
                ['q' => 'Can proof by contradiction be used to prove existential statements (e.g., "there EXISTS an integer with property X")?', 'opts' => ['No — contradiction only works for universal statements', 'Yes — assume no such integer exists, then derive a contradiction', 'Only if you also use mathematical induction', 'Only if X is a divisibility property'], 'ans' => 1, 'exp' => 'Yes. To prove "there exists an object with property X," assume for contradiction that NO such object exists. Reasoning from this assumption often leads to an impossible conclusion. Euclid\'s proof of infinitely many primes is exactly this: assume finitely many exist, reach a contradiction.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 5.5 — Proof by Cases (Exhaustion)
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Proof by Cases (Proof by Exhaustion)</h2>
<p><strong>Proof by cases</strong> — also called <em>proof by exhaustion</em> — is used when the domain of a statement can be partitioned into a finite number of <em>exhaustive</em> and <em>mutually exclusive</em> cases. You prove the statement holds in <em>every</em> case separately. Since the cases together cover all possibilities, the theorem holds universally. The critical requirement: your list of cases must be truly exhaustive — no scenario can be left out.</p>

<h3>When to Use Proof by Cases</h3>
<p>Use this method when a universal statement involves integers that can be classified by parity (even/odd), by remainder classes (mod n), by sign (positive/negative/zero), or by any finite partition of the domain. The key insight is that you do not need a single unified argument — you need a correct argument for each individual case.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — Proof by Cases Template</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Goal:</span> Prove that P(x) holds for all x in domain D.

<span style="color:#a7f3d0;">Step 1:</span> Partition D into cases C₁, C₂, ..., Cₙ such that:
          • Every x ∈ D belongs to at least one Cᵢ   <span style="color:#6b7280;">[Exhaustive]</span>
          • No x ∈ D belongs to two different Cᵢ       <span style="color:#6b7280;">[Mutually exclusive]</span>

<span style="color:#a7f3d0;">Step 2:</span> For each case Cᵢ:
          "Case i: Assume x ∈ Cᵢ."
          Prove P(x) holds for all x in Cᵢ.

<span style="color:#a7f3d0;">Step 3:</span> "Since all cases are exhausted, P(x) holds for all x ∈ D." ∎</div>
  </div>
</div>

<h3>Worked Example 1: n² + n is Always Even</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — n² + n is even for all integers n</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span> For all integers n, n² + n is even.

<span style="color:#a7f3d0;">Proof by Cases:</span>
  Every integer is either even or odd. We consider both cases.

  <span style="color:#c4b5fd;">Case 1: n is even.</span>
    Then n = 2k for some integer k.
    n² + n = (2k)² + 2k = 4k² + 2k = 2(2k² + k).
    This is even. ✓

  <span style="color:#c4b5fd;">Case 2: n is odd.</span>
    Then n = 2k + 1 for some integer k.
    n² + n = (2k+1)² + (2k+1)
           = 4k² + 4k + 1 + 2k + 1
           = 4k² + 6k + 2
           = 2(2k² + 3k + 1).
    This is even. ✓

  In both exhaustive cases, n² + n is even.
  Therefore n² + n is even for all integers n.        <span style="color:#fcd34d;">∎</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Alternate Insight</span>
n² + n = n(n + 1). This is the product of two consecutive
integers. One of any two consecutive integers must be even,
so their product is always even — this gives a slicker proof!</div>
  </div>
</div>

<h3>Worked Example 2: Three Cases with Modular Arithmetic</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — n³ − n is divisible by 3 for all integers n</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span> For all integers n, 3 | (n³ − n).

<span style="color:#a7f3d0;">Proof by Cases:</span>
  By the Division Algorithm, any integer n satisfies
  exactly one of:  n ≡ 0 (mod 3), n ≡ 1 (mod 3), n ≡ 2 (mod 3).

  <span style="color:#c4b5fd;">Case 1: n = 3k</span>  (n ≡ 0 mod 3)
    n³ − n = (3k)³ − 3k = 27k³ − 3k = 3(9k³ − k). ✓

  <span style="color:#c4b5fd;">Case 2: n = 3k + 1</span>  (n ≡ 1 mod 3)
    n³ − n = n(n²−1) = n(n−1)(n+1)
           = (3k+1)(3k)(3k+2)
           = 3k(3k+1)(3k+2).
    Factor of 3 is explicit. ✓

  <span style="color:#c4b5fd;">Case 3: n = 3k + 2</span>  (n ≡ 2 mod 3)
    n(n−1)(n+1) = (3k+2)(3k+1)(3k+3)
                = (3k+2)(3k+1) · 3(k+1).
    Factor of 3 is explicit. ✓

  All cases are covered. Therefore 3 | (n³ − n) for all n ∈ ℤ. <span style="color:#fcd34d;">∎</span></div>
  </div>
</div>

<h3>The Danger: Forgetting a Case</h3>
<p>The most critical error in proof by cases is leaving out a case. If your partition does not cover all possibilities, the proof is invalid — you have only proven the theorem for part of the domain. Always explicitly verify that your cases are exhaustive before beginning the proof, and state the partition clearly at the outset.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '5.5 Proof by Cases (Exhaustion)',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L5_5', [
                ['q' => 'What are the two essential requirements for the cases in a proof by cases?', 'opts' => ['They must be equal in number and use the same algebra', 'They must be exhaustive (cover all possibilities) and mutually exclusive (no overlap)', 'They must each be proven by contradiction', 'They must use integers only'], 'ans' => 1, 'exp' => 'A valid proof by cases requires that the cases (1) be exhaustive — together they cover every element of the domain — and (2) be mutually exclusive — no element belongs to two different cases simultaneously. Missing either property invalidates the proof.'],
                ['q' => 'In the proof that n² + n is always even, why is it sufficient to consider ONLY two cases (n even and n odd)?', 'opts' => ['Because n can only be positive', 'Because every integer is either even or odd — these two cases are exhaustive and mutually exclusive', 'Because the problem says to use two cases', 'Because n² + n is only defined for small n'], 'ans' => 1, 'exp' => 'The integers partition exactly into even and odd numbers — no integer is both, and every integer is one or the other. This makes {even, odd} an exhaustive, mutually exclusive partition, making two cases sufficient.'],
                ['q' => 'In the n³ − n divisibility proof, why are THREE cases (mod 3) needed instead of two?', 'opts' => ['Because n³ has three terms', 'Because the Division Algorithm states every integer leaves remainder 0, 1, or 2 when divided by 3 — three possible residues', 'Because n − 1, n, and n + 1 are three numbers', 'Because the problem involves cubics'], 'ans' => 1, 'exp' => 'When proving a statement about divisibility by 3, you need to cover all residue classes modulo 3: n ≡ 0, n ≡ 1, and n ≡ 2 (mod 3). The Division Algorithm guarantees these three cases are exhaustive and mutually exclusive.'],
                ['q' => 'What is the elegant "alternate insight" for why n² + n = n(n+1) is always even?', 'opts' => ['Because n is always positive', 'One of any two consecutive integers must be even, making their product even', 'Because n² is always a perfect square', 'Because n+1 is always prime'], 'ans' => 1, 'exp' => 'n(n+1) is the product of two consecutive integers. In any pair of consecutive integers, one is even and one is odd. The product of an even number with any integer is even, so n(n+1) is always even — no algebra needed!'],
                ['q' => 'What is the fatal flaw if a proof by cases misses one scenario?', 'opts' => ['The proof is merely less elegant but still valid', 'The proof is INVALID — the theorem has only been shown for part of the domain, not universally', 'The proof must be restarted from contradiction', 'Only a minor revision is needed — add the missing case as a footnote'], 'ans' => 1, 'exp' => 'A proof by cases is only valid when ALL cases are covered. If even one possible scenario is omitted, the proof establishes the theorem for a proper subset of the domain — it says nothing about the missing cases. The entire proof is invalid as a universal result.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 5.6 — Mathematical Induction
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Mathematical Induction</h2>
<p><strong>Mathematical induction</strong> is the primary method for proving statements of the form "for all natural numbers n ≥ n₀, property P(n) holds." It is the proof technique of choice for formulas, algorithms, data structures, and recursive definitions — anywhere a pattern continues indefinitely across the natural numbers. Induction is not guessing; it is an airtight logical argument that works by a chain reaction of truth.</p>

<h3>The Domino Analogy</h3>
<p>Imagine an infinite line of dominoes. To guarantee that <em>all</em> dominoes will fall, you need to establish exactly two things: (1) the first domino falls, and (2) whenever any domino falls, the next one must fall too. These two facts together guarantee every domino falls — no matter how far down the line. Mathematical induction is precisely this argument, with dominoes replaced by propositions about natural numbers.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — Mathematical Induction Template</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Goal:</span> Prove P(n) is true for all n ≥ n₀ (usually n₀ = 0 or 1).

<span style="color:#c4b5fd;">BASE CASE:</span>
  Prove P(n₀) directly.
  "When n = n₀, [compute both sides / verify property directly]."

<span style="color:#c4b5fd;">INDUCTIVE STEP:</span>
  "Assume P(k) is true for some arbitrary k ≥ n₀."   <span style="color:#6b7280;">[Inductive Hypothesis]</span>
  "We must show P(k+1) is true."
  [Use P(k) — the inductive hypothesis — somewhere in the algebra]
  "Therefore P(k+1) holds."

<span style="color:#c4b5fd;">CONCLUSION:</span>
  "By the Principle of Mathematical Induction,
   P(n) is true for all integers n ≥ n₀." <span style="color:#fcd34d;">∎</span>

<span style="color:#fca5a5;">The cardinal rule:</span> You MUST USE the inductive hypothesis P(k)
in the proof of P(k+1). If you never use it, your proof is circular.</div>
  </div>
</div>

<h3>Worked Example 1: Gauss's Summation Formula</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — 1 + 2 + 3 + ... + n = n(n+1)/2</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span> For all n ≥ 1,  1 + 2 + 3 + ... + n = n(n+1)/2.
Let P(n) denote this statement.

<span style="color:#c4b5fd;">Base Case:</span>  n = 1.
  LHS = 1.
  RHS = 1(1+1)/2 = 2/2 = 1.
  LHS = RHS. ✓  P(1) holds.

<span style="color:#c4b5fd;">Inductive Step:</span>
  Assume P(k) is true for some k ≥ 1:
    1 + 2 + ... + k = k(k+1)/2.          <span style="color:#6b7280;">[Inductive Hypothesis]</span>

  We must show P(k+1):
    1 + 2 + ... + k + (k+1) = (k+1)(k+2)/2.

  Starting from the left side:
    1 + 2 + ... + k + (k+1)
      = [1 + 2 + ... + k] + (k+1)        <span style="color:#6b7280;">[Group first k terms]</span>
      = k(k+1)/2 + (k+1)                 <span style="color:#6b7280;">[Apply Inductive Hypothesis]</span>
      = k(k+1)/2 + 2(k+1)/2              <span style="color:#6b7280;">[Common denominator]</span>
      = [k(k+1) + 2(k+1)] / 2
      = (k+1)(k+2) / 2.                  <span style="color:#6b7280;">[Factor out (k+1)]</span>

  This is exactly P(k+1). ✓

<span style="color:#c4b5fd;">Conclusion:</span> By induction, P(n) holds for all n ≥ 1.    <span style="color:#fcd34d;">∎</span></div>
  </div>
</div>

<h3>Worked Example 2: Powers of 2</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — 1 + 2 + 4 + ... + 2ⁿ = 2ⁿ⁺¹ − 1</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span> For all n ≥ 0,  Σᵢ₌₀ⁿ 2ⁱ = 2ⁿ⁺¹ − 1.

<span style="color:#c4b5fd;">Base Case:</span>  n = 0.
  LHS = 2⁰ = 1.
  RHS = 2¹ − 1 = 1.  ✓

<span style="color:#c4b5fd;">Inductive Step:</span>
  Assume  1 + 2 + 4 + ... + 2ᵏ = 2ᵏ⁺¹ − 1.  <span style="color:#6b7280;">[IH]</span>
  Prove  1 + 2 + ... + 2ᵏ + 2ᵏ⁺¹ = 2ᵏ⁺² − 1.

  LHS = (1 + 2 + ... + 2ᵏ) + 2ᵏ⁺¹
      = (2ᵏ⁺¹ − 1) + 2ᵏ⁺¹               <span style="color:#6b7280;">[IH]</span>
      = 2 · 2ᵏ⁺¹ − 1
      = 2ᵏ⁺² − 1.  ✓

By induction, the formula holds for all n ≥ 0.         <span style="color:#fcd34d;">∎</span></div>
  </div>
</div>

<h3>Common Induction Errors</h3>
<p>The two most common mistakes: (1) <strong>not actually using the inductive hypothesis</strong> in the inductive step — if P(k) never appears in your algebra, you have not used induction at all; and (2) <strong>skipping the base case</strong> — without it, the "domino chain" has no starting point and the proof is vacuous. Both errors produce formally invalid proofs even if the theorem happens to be true.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '5.6 Mathematical Induction',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L5_6', [
                ['q' => 'What are the two required components of every proof by mathematical induction?', 'opts' => ['A direct proof and a contradiction', 'The Base Case (verify P(n₀)) and the Inductive Step (P(k) → P(k+1))', 'A universal and an existential statement', 'Proof by cases and proof by contrapositive'], 'ans' => 1, 'exp' => 'Every induction proof must include: (1) the Base Case — verifying the property for the smallest value n₀; and (2) the Inductive Step — showing that if P(k) holds, then P(k+1) must also hold.'],
                ['q' => 'In the inductive step, what is the "inductive hypothesis"?', 'opts' => ['The statement we are trying to prove', 'The assumption that P(k) is true for some arbitrary k ≥ n₀', 'The base case verified for n₀', 'A contradiction assumed to be false'], 'ans' => 1, 'exp' => 'The inductive hypothesis is the assumption "P(k) is true for some arbitrary fixed k ≥ n₀." It is a temporary assumption you make at the start of the inductive step and then USE algebraically to derive P(k+1).'],
                ['q' => 'When proving 1+2+...+n = n(n+1)/2, the inductive step must show what?', 'opts' => ['The formula holds for n = 100', '1 + 2 + ... + k + (k+1) = (k+1)(k+2)/2, using the fact that 1+2+...+k = k(k+1)/2', 'k(k+1)/2 is an integer', 'The sum is always divisible by 2'], 'ans' => 1, 'exp' => 'The inductive step requires proving P(k+1): that 1+2+...+k+(k+1) = (k+1)(k+2)/2. The key move is replacing the bracket [1+2+...+k] with k(k+1)/2 — this is where the inductive hypothesis P(k) is used.'],
                ['q' => 'Why is a proof by induction with a missing base case invalid?', 'opts' => ['Base cases are optional for strong induction', 'Without a starting domino falling, the chain never begins — there is nothing to propagate through the inductive step', 'The base case is only needed for divisibility proofs', 'Missing the base case only makes the proof informal, not invalid'], 'ans' => 1, 'exp' => 'The inductive step only proves "if P(k), then P(k+1)" — a conditional. Without a base case establishing that P(n₀) is actually true, the chain has no anchor. You have proven an implication with nothing to imply from, making the entire argument vacuous.'],
                ['q' => 'What is the "cardinal rule" of the inductive step — the one thing that MUST happen?', 'opts' => ['The algebra must be at least 5 lines long', 'You must use the inductive hypothesis P(k) somewhere in deriving P(k+1)', 'You must verify the formula for k = 1, 2, and 3', 'You must state the theorem in quantifier notation'], 'ans' => 1, 'exp' => 'The inductive hypothesis P(k) must appear explicitly in the algebraic derivation of P(k+1). If P(k) is never used, you have not performed induction — you have written an independent proof of P(k+1) that tells you nothing about P(k+2), P(k+3), etc.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 5.7 — Strong Induction
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Strong Induction (Complete Induction)</h2>
<p><strong>Strong induction</strong> — also called <em>complete induction</em> — is a variant of mathematical induction that gives you a more powerful inductive hypothesis. In regular (weak) induction, the inductive step assumes P(k) and proves P(k+1). In strong induction, the inductive step assumes P(n₀), P(n₀+1), ..., P(k) — <em>all</em> preceding cases — and uses that entire history to prove P(k+1). Strong induction is equivalent to weak induction (they prove the same things), but strong induction is far more convenient when P(k+1) depends on values further back than just P(k).</p>

<h3>Why Strong Induction Is No Stronger (Logically)</h3>
<p>Despite the name, strong induction is not logically more powerful — it is equivalent to weak induction. Any theorem provable by strong induction can also be proven by weak induction (just with more bookkeeping). The advantage is purely practical: when a recursive definition looks back more than one step (like the Fibonacci sequence), strong induction makes the proof dramatically cleaner.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">COMPARISON — Weak vs Strong Induction</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">WEAK INDUCTION:</span>
  Inductive Hypothesis: Assume P(k) is true.
  Goal: Prove P(k+1).
  (Only the immediately preceding case is assumed.)

<span style="color:#93c5fd;">STRONG INDUCTION:</span>
  Inductive Hypothesis: Assume P(n₀), P(n₀+1), ..., P(k) are ALL true.
  Goal: Prove P(k+1).
  (ALL preceding cases up to k are assumed simultaneously.)

<span style="color:#6b7280;">USE STRONG INDUCTION WHEN:</span>
  • P(k+1) depends on P(k-1), P(k-2), ..., not just P(k).
  • The recursion is not just "one step back."
  • Examples: Fibonacci, prime factorization, game theory.</div>
  </div>
</div>

<h3>Worked Example 1: Every Integer ≥ 2 Has a Prime Factor</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — Every integer n ≥ 2 has a prime factor (Strong Induction)</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span>  Every integer n ≥ 2 has at least one prime factor.

<span style="color:#c4b5fd;">Base Case:</span>  n = 2.
  2 is prime, and every prime is its own prime factor. ✓

<span style="color:#c4b5fd;">Inductive Step (Strong):</span>
  Assume the theorem holds for ALL integers from 2 to k.   <span style="color:#6b7280;">[IH]</span>
  We prove it holds for k+1.

  <span style="color:#93c5fd;">Case A: k+1 is prime.</span>
    Then k+1 is its own prime factor. ✓

  <span style="color:#93c5fd;">Case B: k+1 is composite.</span>
    Then k+1 = a · b where 2 ≤ a, b < k+1.
    Since 2 ≤ a ≤ k, by the Inductive Hypothesis,
    a has a prime factor p.
    Since p | a and a | (k+1), we have p | (k+1). ✓

  In both cases, k+1 has a prime factor.

By strong induction, every integer n ≥ 2 has a prime factor. <span style="color:#fcd34d;">∎</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Why Strong Induction Was Needed</span>
When k+1 = a·b, we apply IH to "a" — which could be any
integer from 2 to k, not necessarily k itself. Weak induction
only gives us P(k), not P(a). Strong induction gives us
P(a) for ANY a ≤ k.</div>
  </div>
</div>

<h3>Worked Example 2: Fibonacci Numbers and Divisibility</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — Every 3rd Fibonacci Number is Even (Strong Induction)</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Recall:</span>  F₁=1, F₂=1, F₃=2, F₄=3, F₅=5, F₆=8, F₇=13, F₈=21, F₉=34,...
         Fₙ = Fₙ₋₁ + Fₙ₋₂  for n ≥ 3.

<span style="color:#a7f3d0;">Theorem:</span>  Fₙ is even if and only if 3 | n.
          (Equivalently: F₃, F₆, F₉, F₁₂, ... are even; others are odd.)

<span style="color:#c4b5fd;">Base Cases:</span>  n = 1, 2, 3.
  F₁ = 1 (odd), F₂ = 1 (odd), F₃ = 2 (even). ✓
  Pattern: odd, odd, EVEN matches 3 | n iff even.

<span style="color:#c4b5fd;">Inductive Step (Strong):</span>
  Assume the theorem holds for all Fibonacci numbers up to Fₖ.
  Prove it for Fₖ₊₁ = Fₖ + Fₖ₋₁.

  The parity of Fₖ₊₁ depends on parity of Fₖ and Fₖ₋₁,
  both known by the strong inductive hypothesis.
  The pattern (odd, odd, even) repeats every 3 steps,
  confirming Fₙ is even ⟺ 3 | n.                        <span style="color:#fcd34d;">∎</span></div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '5.7 Strong Induction',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L5_7', [
                ['q' => 'What distinguishes strong induction from weak (regular) induction?', 'opts' => ['Strong induction has no base case', 'Strong induction assumes ALL preceding cases P(n₀),...,P(k) are true, not just P(k)', 'Strong induction can only be used for divisibility proofs', 'Strong induction proves a stronger (more general) result than weak induction'], 'ans' => 1, 'exp' => 'In weak induction the IH is "P(k) is true." In strong induction the IH is "P(n₀), P(n₀+1), ..., P(k) are ALL true." This richer assumption is needed when proving P(k+1) requires knowing cases further back than just k.'],
                ['q' => 'Is strong induction logically more powerful than weak induction?', 'opts' => ['Yes — it can prove theorems that weak induction cannot', 'No — they are logically equivalent; strong induction is merely more convenient in certain situations', 'Yes — because it assumes more cases are true', 'No — weak induction is always preferred by convention'], 'ans' => 1, 'exp' => 'Strong and weak induction are logically equivalent — any statement provable by one is provable by the other. Strong induction is preferred purely for convenience when the recursive structure looks back more than one step, avoiding messy auxiliary variables.'],
                ['q' => 'In the proof that every n ≥ 2 has a prime factor, why was STRONG induction needed?', 'opts' => ['Because we needed to consider negative integers', 'Because when k+1 = a·b is composite, a can be any value from 2 to k — not necessarily k itself', 'Because the theorem involves prime numbers which require special induction', 'Because the base case was n = 2, not n = 1'], 'ans' => 1, 'exp' => 'When k+1 is composite, we write k+1 = a·b and apply the IH to a, where 2 ≤ a ≤ k. This a could be anywhere from 2 to k. Weak induction only gives P(k), so if a < k we cannot use it. Strong induction gives P(a) for all a ≤ k, making the argument valid.'],
                ['q' => 'Why does the Fibonacci sequence naturally call for strong induction?', 'opts' => ['Because Fibonacci numbers grow too fast for weak induction', 'Because Fₙ = Fₙ₋₁ + Fₙ₋₂ depends on TWO previous terms, so proving P(n) requires knowing BOTH P(n-1) and P(n-2)', 'Because Fibonacci numbers are defined for all integers', 'Because the sequence has two base cases'], 'ans' => 1, 'exp' => 'The Fibonacci recurrence Fₙ = Fₙ₋₁ + Fₙ₋₂ means proving a property of Fₙ requires knowing properties of BOTH Fₙ₋₁ and Fₙ₋₂. Weak induction only provides P(k), not P(k-1). Strong induction provides both, making Fibonacci proofs natural.'],
                ['q' => 'How many base cases are typically needed for a strong induction proof about Fibonacci numbers?', 'opts' => ['One (n = 1 only)', 'Three (n = 1, 2, 3) to establish the full initial pattern', 'As many as the highest index referenced', 'None — the inductive step is self-contained'], 'ans' => 2, 'exp' => 'Since Fibonacci proofs often involve a period-3 pattern (odd, odd, even), you need base cases for n = 1, 2, and 3 to establish the full repeating unit. In general, you need as many base cases as the "look-back" depth of the recurrence — for Fₙ = Fₙ₋₁ + Fₙ₋₂, two base cases (n=1, n=2) are the formal minimum, but three help expose the pattern.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 5.8 — Existence and Uniqueness Proofs
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Existence and Uniqueness Proofs</h2>
<p>Many important theorems in mathematics assert that something <strong>exists</strong>, or that something exists and is <strong>unique</strong>. These are two separate claims requiring different proof strategies. An existence proof shows that at least one object with a given property exists. A uniqueness proof shows there can be at most one such object. Together they establish the powerful "there exists exactly one" statement written ∃! in mathematical logic.</p>

<h3>Existence Proofs: Two Strategies</h3>
<p>There are two fundamental ways to prove something exists:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">STRATEGIES — Existence Proof Methods</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">CONSTRUCTIVE PROOF:</span>
  Explicitly build or exhibit the object.
  "Let x = [formula]. Then x has property P."
  Advantage: Gives you the actual object.
  Example: Proving ∃ even prime → "Let p = 2. Then p is prime
            and p = 2·1, so p is even."

<span style="color:#93c5fd;">NON-CONSTRUCTIVE PROOF:</span>
  Prove existence without constructing the object.
  Often uses contradiction or pigeonhole principle.
  Advantage: Sometimes the only feasible approach.
  Example: Proving irrational numbers exist between any two rationals
            without naming the specific irrational.</div>
  </div>
</div>

<h3>Constructive Existence: Example</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — There exists an integer that is both a perfect square and a perfect cube</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span>  ∃ n ∈ ℤ⁺ such that n is both a perfect square and a perfect cube.

<span style="color:#a7f3d0;">Proof (Constructive):</span>
  Let n = 64.
  64 = 8² → n is a perfect square.
  64 = 4³ → n is a perfect cube.

  Therefore n = 64 is an integer that is both a perfect square
  and a perfect cube.                                       <span style="color:#fcd34d;">∎</span>

<span style="color:#6b7280;">Note: We could also use n = 1 or n = 729 = 27² = 9³. Many witnesses exist.</span></div>
  </div>
</div>

<h3>Uniqueness Proofs: The Standard Technique</h3>
<p>To prove uniqueness, assume that <em>two</em> objects x and y both satisfy the given property, then show algebraically that x = y. This technique is called the "assume two, show equal" method.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PROOF — Uniqueness of Additive Identity in ℝ</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem:</span>  The additive identity in ℝ is unique.
            (There is exactly ONE real number e such that x + e = x for all x ∈ ℝ.)

<span style="color:#a7f3d0;">Existence:</span>  e = 0 satisfies x + 0 = x for all x. ✓

<span style="color:#a7f3d0;">Uniqueness:</span>
  Suppose e and e' are both additive identities.

  Since e is an additive identity:   e' + e  = e'     <span style="color:#6b7280;">[apply e to e']</span>
  Since e' is an additive identity:  e' + e  = e      <span style="color:#6b7280;">[apply e' to e]</span>

  Therefore:  e' = e' + e = e.
  So e = e'.

  The additive identity is unique.                       <span style="color:#fcd34d;">∎</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">The Standard Uniqueness Template</span>
1. Assume x and y BOTH satisfy property P.
2. Use the property definition on both objects.
3. Derive x = y algebraically.
4. Conclude: "therefore the object is unique."</div>
  </div>
</div>

<h3>Existence + Uniqueness: Division Algorithm</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">THEOREM — Division Algorithm (Statement)</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Theorem (Division Algorithm):</span>
  For any integers a and d with d > 0,
  there exist UNIQUE integers q (quotient) and r (remainder)
  such that:
        a = dq + r    and    0 ≤ r < d.

<span style="color:#6b7280;">This theorem contains two distinct claims:
  EXISTENCE:  q and r with the stated property exist.
  UNIQUENESS: Only ONE such pair (q, r) exists.

Each claim requires its own separate argument.
The full proof uses the Well-Ordering Principle
for existence and algebraic comparison for uniqueness.</span></div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '5.8 Existence and Uniqueness Proofs',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L5_8', [
                ['q' => 'What is the difference between a constructive and a non-constructive existence proof?', 'opts' => ['A constructive proof uses contradiction; a non-constructive proof uses induction', 'A constructive proof explicitly exhibits the object; a non-constructive proof confirms existence without building it', 'A constructive proof is always shorter', 'There is no mathematical difference — they are equivalent styles'], 'ans' => 1, 'exp' => 'A constructive existence proof explicitly produces or exhibits a specific object satisfying the property. A non-constructive existence proof shows an object must exist — often via contradiction or counting — without identifying it explicitly.'],
                ['q' => 'What is the standard technique for proving uniqueness?', 'opts' => ['Show the object can be constructed in only one way', 'Assume two objects x and y BOTH satisfy the property, then derive x = y', 'Prove existence first, then use induction', 'Find all objects satisfying the property and count them'], 'ans' => 1, 'exp' => 'The standard uniqueness technique is: assume x and y both satisfy property P, then use P\'s definition applied to both to algebraically derive x = y. This shows any two objects satisfying P must be the same object.'],
                ['q' => 'In the uniqueness proof for additive identity, why is e applied to e\' AND e\' applied to e?', 'opts' => ['To create two equations that can be set equal, revealing e = e\'', 'Because the real numbers require both directions', 'To avoid using subtraction', 'Because the additive identity is defined for both sides of an equation'], 'ans' => 0, 'exp' => 'By applying each identity to the other number, we get two expressions for e\' + e: from one side it equals e\', from the other it equals e. Chaining these equalities gives e\' = e\'+e = e, completing the uniqueness argument.'],
                ['q' => 'The Division Algorithm states "there exist UNIQUE q and r such that a = dq + r, 0 ≤ r < d." What are the two separate things that must be proven?', 'opts' => ['That q is positive and r is non-negative', 'Existence of such q and r, and Uniqueness of such q and r — as two separate arguments', 'That the algorithm terminates and runs in polynomial time', 'That d divides a and that a is positive'], 'ans' => 1, 'exp' => 'A theorem of the form "there exists a UNIQUE object with property P" bundles two claims: (1) Existence — at least one such pair (q, r) exists; and (2) Uniqueness — at most one such pair exists. Both must be proven separately.'],
                ['q' => 'To prove ∃ n ∈ ℤ⁺ that is both a perfect square and a perfect cube, what is the minimal valid proof?', 'opts' => ['Show that all perfect squares are also perfect cubes', 'Exhibit a single specific integer (e.g., 64 = 8² = 4³) that satisfies both conditions', 'Use induction over all perfect squares', 'Prove no such n can exist, then take the contrapositive'], 'ans' => 1, 'exp' => 'For an existential claim, exhibiting a single witness is sufficient. n = 64 = 8² = 4³ is a concrete, verifiable example. One valid witness is all that is needed to prove "there exists at least one."'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 5.9 — Disproving Statements: Counterexamples
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Disproving Statements: Counterexamples</h2>
<p>Not all mathematical statements are true — and disproving a false universal statement is just as important as proving a true one. The primary tool for disproving a universal statement of the form "for all x, P(x)" is the <strong>counterexample</strong>: a single specific value of x for which P(x) is demonstrably false. Because universal quantification requires P(x) to hold for <em>every</em> x in the domain, a single exception destroys the claim entirely.</p>

<h3>The Logic of Counterexamples</h3>
<p>The negation of "∀x P(x)" is "∃x ¬P(x)" — there exists at least one x for which P(x) fails. Finding such an x is a constructive existence proof of the negation, which simultaneously disproves the original statement. This is why disproving a universal claim is often much easier than proving one: you need only one failure, while a proof must handle all cases.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — Counterexample Strategy</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">To disprove:</span>  "For all x ∈ D, P(x) is true."

<span style="color:#a7f3d0;">Method:</span>
  1. Find a specific x₀ ∈ D such that P(x₀) is FALSE.
  2. Verify that x₀ ∈ D (it must be in the claimed domain).
  3. Verify that P(x₀) is actually false.
  4. State: "x₀ is a counterexample. Therefore the statement is false." ∎

<span style="color:#fca5a5;">Common hunting grounds for counterexamples:</span>
  • Extreme values: x = 0, x = 1, x = -1, x = -2
  • Boundary cases: smallest/largest values in domain
  • Special structures: primes, perfect squares, fractions
  • Values that "break" assumptions: denominators = 0, etc.</div>
  </div>
</div>

<h3>Example 1: Disproving a Number Theory Claim</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DISPROOF — "For all n ∈ ℕ, n² + n + 41 is prime"</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Claim:</span>  For all n ∈ ℕ, n² + n + 41 is prime.

<span style="color:#6b7280;">This formula actually produces primes for n = 0, 1, 2, ..., 39.
It was discovered by Euler and fooled many mathematicians.
But:</span>

<span style="color:#a7f3d0;">Counterexample:</span>  Let n = 40.

  n² + n + 41  =  40² + 40 + 41
               =  1600 + 40 + 41
               =  1681
               =  41²
               =  41 × 41.

  1681 is divisible by 41 — it is NOT prime.

Therefore the claim "n² + n + 41 is always prime" is FALSE.  <span style="color:#fcd34d;">∎</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Lesson</span>
Empirical evidence — even 40 consecutive successes —
is NOT proof. A universal statement is false the moment
ONE counterexample exists, regardless of how many cases
the pattern holds for.</div>
  </div>
</div>

<h3>Example 2: Disproving an Algebraic Claim</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DISPROOF — "For all real a, b: (a + b)² = a² + b²"</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">Claim:</span>  For all real numbers a and b, (a + b)² = a² + b².

<span style="color:#a7f3d0;">Counterexample:</span>  Let a = 1, b = 1.

  (1 + 1)² = 2² = 4.
  1² + 1² = 1 + 1 = 2.
  4 ≠ 2.

The claim is FALSE for a = b = 1.                          <span style="color:#fcd34d;">∎</span>

<span style="color:#6b7280;">The correct identity is: (a+b)² = a² + 2ab + b².</span></div>
  </div>
</div>

<h3>When Counterexamples Fail: You May Need a Proof</h3>
<p>If you cannot find a counterexample after searching boundary cases, small values, and special structures, that is strong evidence the statement might be true — at which point you should attempt a proof. But remember: failing to find a counterexample is never a proof. You must produce a formal argument. This is the heart of the distinction between <em>evidence</em> and <em>proof</em>.</p>

<h3>Disproving Existential Statements</h3>
<p>To disprove "∃x P(x)" — to show NO object with property P exists — you must prove the universal statement "∀x ¬P(x)." This requires a full proof, not a counterexample. One of the most important examples: proving √2 is irrational is exactly a disproof of the existential claim "there exist integers p and q such that (p/q)² = 2."</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '5.9 Disproving Statements: Counterexamples',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L5_9', [
                ['q' => 'How many counterexamples are needed to disprove a universal statement "∀x P(x)"?', 'opts' => ['At least 3 — one is considered a fluke', 'Exactly one — a single specific x for which P(x) is false', 'A majority of cases must fail', 'You cannot disprove universal statements — you can only fail to prove them'], 'ans' => 1, 'exp' => 'Because a universal statement claims P(x) holds for ALL x, finding even one x₀ for which P(x₀) is false completely destroys the claim. One verified counterexample is logically sufficient and constitutes a complete disproof.'],
                ['q' => 'The formula n² + n + 41 produces primes for n = 0 through 39 (40 consecutive successes). Does this prove it always produces primes?', 'opts' => ['Yes — 40 cases is strong empirical evidence', 'No — empirical success is not proof; n = 40 gives 41², which is composite, disproving it', 'Yes — if it holds for the first 40 values, it must hold for all n by induction', 'No — but you would need 1000 failures, not just one'], 'ans' => 1, 'exp' => 'At n = 40: 40² + 40 + 41 = 1681 = 41², which is not prime. Regardless of how many cases verify the pattern, one counterexample is a complete and final disproof. Empirical evidence — even extensive — never constitutes mathematical proof.'],
                ['q' => 'To disprove "∃x P(x)" (an existential statement), what must you do?', 'opts' => ['Find one x for which P(x) is false', 'Prove the universal statement ∀x ¬P(x) — that P(x) fails for ALL x', 'Find two counterexamples', 'Use strong induction on the domain'], 'ans' => 1, 'exp' => 'To disprove ∃x P(x), you must show that NO x satisfies P — i.e., ∀x ¬P(x). This requires a complete proof for all x in the domain, not just a single example. One "failure" example would only show P fails for one x, not for all x.'],
                ['q' => 'What makes n = 40 such a clever counterexample to the Euler prime formula?', 'opts' => ['It is a perfect square', 'When n = 41, the formula gives 41·43, which is clearly composite — but n = 40 gives 41², a perfect square, which is also composite and structurally elegant', 'It is the first even number after 39', 'It shows the formula fails for all multiples of 8'], 'ans' => 1, 'exp' => 'At n = 40: n² + n + 41 = 1600 + 40 + 41 = 1681 = 41². This is 41 squared — a perfect square — and therefore divisible by 41 (and by 41 again). The counterexample is elegant because the coefficient 41 appears naturally as the factor.'],
                ['q' => 'A student tests the claim "√n is always irrational" for n = 2, 3, 5, 6, 7, 8 — all irrational — and concludes the claim is proven. What is the error?', 'opts' => ['The student should have tested more values', 'The student confused evidence with proof; n = 4 is a counterexample since √4 = 2 is rational', 'The student should have used induction instead', 'The claim is actually true — the student is correct'], 'ans' => 1, 'exp' => 'n = 4 is a counterexample: √4 = 2, which is rational. Testing cases — even many cases — is not a proof of a universal claim. The student accidentally skipped perfect squares, which are exactly the cases that break the pattern.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 5.10 — Choosing the Right Proof Strategy
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Choosing the Right Proof Strategy</h2>
<p>Knowing five proof methods is only half the skill. The other half — arguably the more important half — is reading a mathematical statement and quickly diagnosing which method is most appropriate. Expert mathematicians develop this instinct over years of practice. This lesson gives you a systematic framework for making that choice deliberately, even before intuition has fully developed.</p>

<h3>The Diagnostic Decision Tree</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DECISION TREE — Which Method to Use?</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;">Is the statement FALSE?
  └─ YES → Find a <span style="color:#fca5a5;">COUNTEREXAMPLE</span>. Done.
  └─ NO  → Proceed:

Is it "∃x P(x)" (existential)?
  └─ YES → <span style="color:#93c5fd;">EXISTENCE PROOF</span> (constructive or non-constructive).

Is it "∀n ∈ ℕ, P(n)" (holds for all naturals)?
  └─ YES → Try <span style="color:#a7f3d0;">INDUCTION</span>.
            Does P(n+1) need more than just P(n)?
              └─ YES → <span style="color:#a7f3d0;">STRONG INDUCTION</span>.
              └─ NO  → <span style="color:#a7f3d0;">WEAK INDUCTION</span>.

Is it "p → q" (an implication)?
  └─ Can you chain definitions from p to q?
      └─ YES → <span style="color:#93c5fd;">DIRECT PROOF</span>.
  └─ Is ¬q easier to work with than p?
      └─ YES → <span style="color:#c4b5fd;">CONTRAPOSITIVE</span>.
  └─ Is p a negative / impossibility claim?
      └─ YES → <span style="color:#fca5a5;">CONTRADICTION</span>.

Can the domain be split into finite cases?
  └─ YES → <span style="color:#a7f3d0;">PROOF BY CASES</span>.</div>
  </div>
</div>

<h3>Key Diagnostic Signals</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SIGNALS — Reading the Statement</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">Direct Proof:</span>
  Signal: "If [definition-friendly condition], then [conclusion]."
  Example: "If m and n are odd, then mn is odd."
  Why: The definitions of odd unpack cleanly into algebra.

<span style="color:#c4b5fd;">Contrapositive:</span>
  Signal: Conclusion is a NEGATIVE or the hypothesis is hard to unpack.
  Example: "If n² is odd, then n is odd."
  Why: Assuming n even (¬q) is cleaner than assuming n² odd (p).

<span style="color:#fca5a5;">Contradiction:</span>
  Signal: "Prove there is NO X," or "X cannot be Y," or irrationality.
  Example: "√2 is irrational," "infinitely many primes exist."
  Why: Assuming the opposite leads to a structural impossibility.

<span style="color:#a7f3d0;">Cases:</span>
  Signal: "For ALL integers n," parity split, or modular classes.
  Example: "n² + n is always even."
  Why: Even/odd covers all cases cleanly.

<span style="color:#a7f3d0;">Induction:</span>
  Signal: Formula or property indexed by ℕ, recursive definitions.
  Example: "1 + 2 + ... + n = n(n+1)/2 for all n ≥ 1."
  Why: The formula builds on previous values.</div>
  </div>
</div>

<h3>Practice: Match the Method</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PRACTICE — Identify the Best Method</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#fcd34d;">Statement 1:</span> "For all n ∈ ℕ, 2ⁿ ≥ n + 1."
<span style="color:#a7f3d0;">→ INDUCTION</span>. Indexed by ℕ, recursive exponential pattern.

<span style="color:#fcd34d;">Statement 2:</span> "If n³ is even, then n is even."
<span style="color:#c4b5fd;">→ CONTRAPOSITIVE</span>. Assume n is odd, show n³ is odd.

<span style="color:#fcd34d;">Statement 3:</span> "There is no rational number whose square is 3."
<span style="color:#fca5a5;">→ CONTRADICTION</span>. Assume √3 = p/q in lowest terms → contradiction.

<span style="color:#fcd34d;">Statement 4:</span> "For all n, n(n+1)(n+2) is divisible by 6."
<span style="color:#a7f3d0;">→ CASES</span>. Split by n mod 6 (six cases) or use factorial argument.

<span style="color:#fcd34d;">Statement 5:</span> "There exists a prime p such that p+2 is also prime."
<span style="color:#93c5fd;">→ EXISTENCE (Constructive)</span>. Exhibit p = 3 (twin prime: 3, 5).</div>
  </div>
</div>

<h3>A Final Word: Proof Writing Is a Skill</h3>
<p>Choosing the right method is the first step. Writing the proof clearly, rigorously, and readably is the craft. Good mathematical writing has the following properties: every step is justified, definitions are unpacked explicitly, new variables are named and defined when introduced, and the conclusion is stated clearly and tied back to the original claim. Proofs should be written for a human reader — not just for correctness, but for understanding. A proof that is correct but incomprehensible has failed half its purpose.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '5.10 Choosing the Right Proof Strategy',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L5_10', [
                ['q' => 'A theorem states: "For all n ∈ ℕ, 1³ + 2³ + ... + n³ = [n(n+1)/2]²." Which method is most appropriate?', 'opts' => ['Proof by Contradiction', 'Proof by Cases (even/odd)', 'Mathematical Induction', 'Proof by Counterexample'], 'ans' => 2, 'exp' => 'The statement is universally quantified over ℕ and involves a formula with a recursive structure (each term builds on the previous). This is the signature of induction. The base case n=1 and inductive step P(k) → P(k+1) map cleanly onto this structure.'],
                ['q' => 'A theorem states: "If n² is divisible by 5, then n is divisible by 5." Which method is most appropriate?', 'opts' => ['Direct Proof', 'Proof by Contrapositive', 'Proof by Contradiction', 'Mathematical Induction'], 'ans' => 1, 'exp' => 'The conclusion "n is divisible by 5" is tricky to derive directly from "n² is divisible by 5" (requires number theory). The contrapositive "if n is NOT divisible by 5, then n² is NOT divisible by 5" is easier: write n = 5k+r for r ∈ {1,2,3,4} and show n² is not divisible by 5 in each case.'],
                ['q' => 'You want to prove "log₂(3) is irrational." Which method is most appropriate?', 'opts' => ['Direct Proof', 'Mathematical Induction', 'Proof by Contradiction', 'Proof by Cases'], 'ans' => 2, 'exp' => 'Irrationality proofs are the classic application of contradiction. Assume log₂(3) = p/q in lowest terms (rational). Then 2^(p/q) = 3 → 2^p = 3^q. But the left side is a power of 2 (even) and the right side is a power of 3 (odd) — impossible for p,q > 0. Contradiction.'],
                ['q' => 'You want to prove "For all integers n, n² ≡ 0 or 1 (mod 4)." Which method is most natural?', 'opts' => ['Direct Proof using one generic case', 'Proof by Cases (n even / n odd)', 'Mathematical Induction over all integers', 'Proof by Contradiction'], 'ans' => 1, 'exp' => 'The claim involves parity of n, naturally splitting into two cases. Case 1: n is even → n = 2k → n² = 4k² ≡ 0 (mod 4). Case 2: n is odd → n = 2k+1 → n² = 4k²+4k+1 ≡ 1 (mod 4). Both cases are clean and exhaustive.'],
                ['q' => 'A student claims "For all n ≥ 1, n² ≤ 2ⁿ." After testing n = 1 through 5, they write "Proven." What is wrong and what should they do?', 'opts' => ['Nothing is wrong — 5 cases is sufficient for small theorems', 'Testing cases is not a proof; they should either find a counterexample (n = 2 shows 4 ≤ 4, OK) or write an induction proof for the universal claim', 'They should test 10 more cases to be certain', 'They should use contradiction instead of cases'], 'ans' => 1, 'exp' => 'Testing finitely many cases never proves a universal statement. n=2: 4 ≤ 4 ✓. n=3: 9 ≤ 8 ✗ — this is actually FALSE! So the student should first have found this counterexample at n=3, disproving the claim entirely. Checking cases can find disproofs, but never provide proofs.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 5.11 — FINAL EXAM
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            ['q' => 'Which proof method begins by assuming the NEGATION of what you want to prove?', 'opts' => ['Direct Proof', 'Proof by Cases', 'Proof by Contradiction', 'Mathematical Induction'], 'ans' => 2, 'exp' => 'In proof by contradiction (reductio ad absurdum), you assume ¬P and derive a logical impossibility, forcing the conclusion that P must be true.'],
            ['q' => 'What is the contrapositive of "If a | b and b | c, then a | c"?', 'opts' => ['If a ∤ c, then a ∤ b or b ∤ c', 'If a | c, then a | b and b | c', 'If a ∤ b, then a ∤ c', 'If b ∤ c, then a | b'], 'ans' => 0, 'exp' => 'The contrapositive of p ∧ q → r is ¬r → ¬p ∨ ¬q. Here r is "a|c," so ¬r is "a∤c," and ¬(a|b ∧ b|c) = (a∤b) ∨ (b∤c). The contrapositive is: if a∤c, then a∤b or b∤c.'],
            ['q' => 'In the proof that √2 is irrational, the contradiction arises because:', 'opts' => ['√2 is shown to be larger than any rational number', 'Both numerator p and denominator q are even, contradicting that p/q was in lowest terms', 'The equation p² = 2q² has no solutions', 'p and q must both be odd, which is impossible'], 'ans' => 1, 'exp' => 'We assumed gcd(p,q) = 1. The algebra forces both p and q to be even, implying gcd(p,q) ≥ 2. This direct contradiction of the lowest-terms assumption completes the proof.'],
            ['q' => 'What is the BASE CASE for proving "For all n ≥ 1, n! ≥ 2^(n-1)" by induction?', 'opts' => ['Verify the formula for n = 0', 'Verify the formula for n = 1: 1! ≥ 2⁰ = 1 ✓', 'Verify the formula for n = 2 and n = 3', 'There is no base case — the inductive step alone suffices'], 'ans' => 1, 'exp' => 'The base case n = 1 gives: 1! = 1 and 2^(1-1) = 2⁰ = 1. Since 1 ≥ 1, the base case holds. This anchors the induction chain.'],
            ['q' => 'When proving a theorem by mathematical induction, where MUST the inductive hypothesis appear?', 'opts' => ['In the base case calculation', 'In the conclusion of the theorem', 'In the algebraic derivation of P(k+1) within the inductive step', 'Anywhere in the proof — order does not matter'], 'ans' => 2, 'exp' => 'The inductive hypothesis P(k) must be explicitly used in the algebraic/logical derivation of P(k+1) within the inductive step. If it never appears, no induction has been performed.'],
            ['q' => 'To prove "there exists a prime between 10 and 20," the most efficient method is:', 'opts' => ['Proof by contradiction', 'A constructive existence proof — exhibit 11 (which is prime and satisfies 10 < 11 < 20)', 'Mathematical induction over all primes', 'Proof by contrapositive'], 'ans' => 1, 'exp' => 'For an existential claim, exhibiting a single witness is sufficient. p = 11 is prime and satisfies 10 < 11 < 20. This single exhibit constitutes a complete proof. No further argument is needed.'],
            ['q' => 'Why is testing that a formula holds for n = 1, 2, 3, ..., 100 NOT a valid proof of "∀n ∈ ℕ, P(n)"?', 'opts' => ['Because only odd values of n need to be tested', 'Because testing finitely many cases leaves infinitely many untested; a proof must cover all n universally', 'Because induction requires at least 200 base cases', 'It IS valid — 100 cases is industry standard for mathematics'], 'ans' => 1, 'exp' => 'The natural numbers are infinite. Verifying P(n) for a finite list, however large, leaves infinitely many n unchecked. A universal proof must hold for ALL n, requiring a general argument (direct, inductive, etc.) rather than case-by-case verification.'],
            ['q' => 'A proof by cases for the statement "For all n ∈ ℤ, n² is either divisible by 4 or leaves remainder 1 mod 4" should split into which cases?', 'opts' => ['n > 0 and n ≤ 0', 'n prime and n composite', 'n even (n = 2k) and n odd (n = 2k+1)', 'n divisible by 4, by 2 but not 4, and not by 2'], 'ans' => 2, 'exp' => 'The statement is about the parity of n since divisibility by 4 of n² is determined by whether n is even or odd. Splitting into n even (n = 2k) and n odd (n = 2k+1) gives two exhaustive, mutually exclusive cases that directly connect to the claim.'],
            ['q' => 'In strong induction, the inductive hypothesis for P(k+1) is:', 'opts' => ['Only P(k)', 'P(0) and P(k)', 'ALL of P(n₀), P(n₀+1), ..., P(k)', 'P(k+1) itself (which would be circular)'], 'ans' => 2, 'exp' => 'In strong (complete) induction, the inductive hypothesis is that ALL of P(n₀), P(n₀+1), ..., P(k) are true simultaneously. This richer hypothesis allows you to reference any earlier case, not just the immediately preceding one.'],
            ['q' => 'To disprove the claim "For all integers n ≥ 1, 2n + 1 is prime," the most efficient approach is:', 'opts' => ['Prove its contrapositive', 'Find one value of n for which 2n+1 is composite — e.g., n = 4 gives 9 = 3×3', 'Prove by induction that composites exist', 'Use proof by contradiction'], 'ans' => 1, 'exp' => 'Disproving a universal statement requires only one counterexample. n = 4: 2(4)+1 = 9 = 3 × 3, which is composite. This single witness disproves the universal claim completely and is far more efficient than any other approach.'],
            ['q' => 'What is the correct structure of a proof that "the multiplicative identity in ℝ is unique"?', 'opts' => ['Use induction over all real numbers', 'Assume e and e\' are both multiplicative identities; apply each to the other to derive e = e\'', 'Show by contradiction that 1 is the only real number', 'Use cases: e > 0, e = 0, e < 0'], 'ans' => 1, 'exp' => 'Uniqueness proofs use the "assume two, show equal" technique: assume e and e\' both satisfy x·e = x and x·e\' = x for all x. Then e = e·e\' = e\', since e acts as identity on e\' and e\' acts as identity on e. This shows they must be the same object.'],
            ['q' => 'Which of the following statements about the Principle of Mathematical Induction is TRUE?', 'opts' => ['Induction proves a statement for one specific value of n', 'The inductive step alone (without a base case) is sufficient for a valid proof', 'Induction proves a statement holds for ALL n in an infinite set, based on a finite argument', 'Induction can only be applied to statements about even integers'], 'ans' => 2, 'exp' => 'Mathematical induction leverages a finite two-part argument — one base case and one conditional step — to conclude a statement holds for every element of an infinite set (e.g., all natural numbers). This is its defining power: finite argument, infinite conclusion.'],
        ];

        $finalContent = <<<'HTML'
<div id="org-lock-screen" style="display:none;">
    <h2>Final Exam Locked</h2>
    <p>You must be enrolled in an organization to access the final exam.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 5: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 5.1 through 5.10 — introduction to proof, direct proof, contrapositive, contradiction, cases, induction, strong induction, existence & uniqueness, counterexamples, and choosing the right strategy. Good luck!</p>
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
            'title'       => '5.11 Final Exam: Methods of Proof Mastery',
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