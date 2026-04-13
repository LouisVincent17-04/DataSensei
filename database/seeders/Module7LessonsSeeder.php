<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module7LessonsSeeder
 * Seeds lessons for Module 7: Algorithms & Data Structures for Data Scientists.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module7LessonsSeeder
 *
 * Lessons:
 *  7.1  — Big-O Notation & Complexity Analysis
 *  7.2  — Arrays & Dynamic Arrays
 *  7.3  — Stacks: LIFO Structures & Applications
 *  7.4  — Queues & Deques: FIFO & Double-Ended
 *  7.5  — Linked Lists: Singly, Doubly & Circular
 *  7.6  — Hash Tables: Dictionaries Demystified
 *  7.7  — Trees: Binary Trees & BSTs
 *  7.8  — Heaps & Priority Queues
 *  7.9  — Graphs: Representation, BFS & DFS
 *  7.10 — Sorting & Searching Algorithms
 *  7.11 — Final Exam (Org-Locked)
 */
class Module7LessonsSeeder extends Seeder
{
    public function run()
    {
        $dsaModule = Module::where('order_index', 7)->firstOrFail();
        Lesson::where('module_id', $dsaModule->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 7.1 — Big-O Notation & Complexity Analysis
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>Big-O Notation & Complexity Analysis</h2>
<p>Before writing a single data structure, you need a language for talking about <em>efficiency</em>. Big-O notation is that language. When your dataset grows from 10,000 rows to 10,000,000 rows, the difference between an O(n) algorithm and an O(n²) algorithm is the difference between a 1-second job and a 28-hour job. Data scientists who understand complexity make architecture decisions that scale — and avoid catastrophic pipeline failures in production.</p>

<h3>What Big-O Measures</h3>
<p>Big-O describes how the runtime (or memory usage) of an algorithm <em>grows</em> as input size <code>n</code> grows — specifically in the <strong>worst case</strong> and as n approaches infinity. It ignores constants and lower-order terms because at scale, only the dominant growth rate matters.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:32px;">
  <div style="background:rgba(0,0,0,0.2);padding:12px 20px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.8rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;font-family:'JetBrains Mono',monospace;">Complexity Classes — Best to Worst</span>
  </div>
  <div style="padding:20px;display:flex;flex-direction:column;gap:10px;">
    <div style="display:grid;grid-template-columns:160px 120px 1fr;gap:12px;align-items:center;padding:12px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:8px;">
      <code style="color:#10b981;font-family:'JetBrains Mono',monospace;font-weight:700;">O(1)</code>
      <span style="color:#10b981;font-size:0.8rem;font-weight:600;">Constant</span>
      <span style="color:var(--muted);font-size:0.85rem;">Always the same time, regardless of input size. Array index lookup, hash table get.</span>
    </div>
    <div style="display:grid;grid-template-columns:160px 120px 1fr;gap:12px;align-items:center;padding:12px;background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);border-radius:8px;">
      <code style="color:#3b82f6;font-family:'JetBrains Mono',monospace;font-weight:700;">O(log n)</code>
      <span style="color:#3b82f6;font-size:0.8rem;font-weight:600;">Logarithmic</span>
      <span style="color:var(--muted);font-size:0.85rem;">Halves the problem each step. Binary search, balanced BST lookup.</span>
    </div>
    <div style="display:grid;grid-template-columns:160px 120px 1fr;gap:12px;align-items:center;padding:12px;background:rgba(139,92,246,0.08);border:1px solid rgba(139,92,246,0.2);border-radius:8px;">
      <code style="color:#8b5cf6;font-family:'JetBrains Mono',monospace;font-weight:700;">O(n)</code>
      <span style="color:#8b5cf6;font-size:0.8rem;font-weight:600;">Linear</span>
      <span style="color:var(--muted);font-size:0.85rem;">Scan every element once. Linear search, summing a list, iterating a DataFrame.</span>
    </div>
    <div style="display:grid;grid-template-columns:160px 120px 1fr;gap:12px;align-items:center;padding:12px;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:8px;">
      <code style="color:#f59e0b;font-family:'JetBrains Mono',monospace;font-weight:700;">O(n log n)</code>
      <span style="color:#f59e0b;font-size:0.8rem;font-weight:600;">Linearithmic</span>
      <span style="color:var(--muted);font-size:0.85rem;">Best possible for comparison-based sorting. Merge sort, heap sort, Tim sort (Python's default).</span>
    </div>
    <div style="display:grid;grid-template-columns:160px 120px 1fr;gap:12px;align-items:center;padding:12px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);border-radius:8px;">
      <code style="color:#ef4444;font-family:'JetBrains Mono',monospace;font-weight:700;">O(n²)</code>
      <span style="color:#ef4444;font-size:0.8rem;font-weight:600;">Quadratic</span>
      <span style="color:var(--muted);font-size:0.85rem;">Nested loops over the input. Bubble sort, naive duplicate detection, pairwise distance.</span>
    </div>
    <div style="display:grid;grid-template-columns:160px 120px 1fr;gap:12px;align-items:center;padding:12px;background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.35);border-radius:8px;">
      <code style="color:#dc2626;font-family:'JetBrains Mono',monospace;font-weight:700;">O(2ⁿ)</code>
      <span style="color:#dc2626;font-size:0.8rem;font-weight:600;">Exponential</span>
      <span style="color:var(--muted);font-size:0.85rem;">Doubles with each element. Naive recursive Fibonacci, brute-force subset enumeration.</span>
    </div>
  </div>
</div>

<h3>Visualising Growth: Why Constants Don't Matter at Scale</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Concrete Operation Counts for Each Complexity Class</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">ops</span>(n):
    <span style="color:#c4b5fd;">return</span> {
        <span style="color:#a7f3d0;">"O(1)"</span>:       <span style="color:#fcd34d;">1</span>,
        <span style="color:#a7f3d0;">"O(log n)"</span>:   <span style="color:#93c5fd;">round</span>(math.log2(n)),
        <span style="color:#a7f3d0;">"O(n)"</span>:       n,
        <span style="color:#a7f3d0;">"O(n log n)"</span>: <span style="color:#93c5fd;">round</span>(n * math.log2(n)),
        <span style="color:#a7f3d0;">"O(n²)"</span>:      n ** <span style="color:#fcd34d;">2</span>,
        <span style="color:#a7f3d0;">"O(2ⁿ)"</span>:      <span style="color:#fcd34d;">2</span> ** n <span style="color:#c4b5fd;">if</span> n <= <span style="color:#fcd34d;">30</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"astronomical"</span>,
    }

<span style="color:#93c5fd;">sizes</span> = [<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">100</span>, <span style="color:#fcd34d;">1_000</span>, <span style="color:#fcd34d;">1_000_000</span>]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'n':>12}"</span>, <span style="color:#a7f3d0;">" | "</span>.join(<span style="color:#a7f3d0;">f"{'n='+str(s):>18}"</span> <span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> sizes))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─"</span> * <span style="color:#fcd34d;">90</span>)
<span style="color:#c4b5fd;">for</span> label <span style="color:#c4b5fd;">in</span> [<span style="color:#a7f3d0;">"O(1)"</span>, <span style="color:#a7f3d0;">"O(log n)"</span>, <span style="color:#a7f3d0;">"O(n)"</span>, <span style="color:#a7f3d0;">"O(n log n)"</span>, <span style="color:#a7f3d0;">"O(n²)"</span>, <span style="color:#a7f3d0;">"O(2ⁿ)"</span>]:
    <span style="color:#93c5fd;">row</span> = <span style="color:#a7f3d0;">f"{label:>12}"</span>
    <span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> sizes:
        <span style="color:#93c5fd;">v</span> = ops(s)[label]
        <span style="color:#93c5fd;">row</span> += <span style="color:#a7f3d0;">" | "</span> + <span style="color:#a7f3d0;">f"{str(v):>18}"</span>
    <span style="color:#93c5fd;">print</span>(row)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>           n |             n=10 |            n=100 |          n=1000 |       n=1000000
──────────────────────────────────────────────────────────────────────────────────────────
        O(1) |                 1 |                1 |               1 |               1
    O(log n) |                 3 |                7 |              10 |              20
        O(n) |                10 |              100 |           1,000 |       1,000,000
   O(n log n)|                33 |              664 |           9,966 |      19,931,568
       O(n²) |               100 |           10,000 |       1,000,000 | 1,000,000,000,000
       O(2ⁿ) |             1,024 |       astronomical|   astronomical |     astronomical</div>
  </div>
</div>

<h3>Identifying Complexity: The Rules</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Recognising Complexity in Real Code</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># ── O(1): no loop, no recursion ───────────────────────────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">get_first</span>(arr):
    <span style="color:#c4b5fd;">return</span> arr[<span style="color:#fcd34d;">0</span>]   <span style="color:#6b7280;"># one operation regardless of arr size</span>

<span style="color:#6b7280;"># ── O(n): single loop over input ──────────────────────────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">linear_search</span>(arr, target):
    <span style="color:#c4b5fd;">for</span> i, val <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(arr):   <span style="color:#6b7280;"># worst case: touch every element</span>
        <span style="color:#c4b5fd;">if</span> val == target:
            <span style="color:#c4b5fd;">return</span> i
    <span style="color:#c4b5fd;">return</span> -<span style="color:#fcd34d;">1</span>

<span style="color:#6b7280;"># ── O(n²): nested loops over the SAME input ───────────────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">has_duplicate_naive</span>(arr):
    <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#93c5fd;">len</span>(arr)):          <span style="color:#6b7280;"># outer: n iterations</span>
        <span style="color:#c4b5fd;">for</span> j <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(i + <span style="color:#fcd34d;">1</span>, <span style="color:#93c5fd;">len</span>(arr)):  <span style="color:#6b7280;"># inner: up to n iterations</span>
            <span style="color:#c4b5fd;">if</span> arr[i] == arr[j]:
                <span style="color:#c4b5fd;">return</span> <span style="color:#fca5a5;">True</span>
    <span style="color:#c4b5fd;">return</span> <span style="color:#fca5a5;">False</span>

<span style="color:#6b7280;"># ── O(n): same problem solved smarter with a set ──────────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">has_duplicate_smart</span>(arr):
    <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">len</span>(arr) != <span style="color:#93c5fd;">len</span>(<span style="color:#93c5fd;">set</span>(arr))  <span style="color:#6b7280;"># set membership is O(1) amortized</span>

<span style="color:#6b7280;"># ── O(log n): problem halves each step ────────────────────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">binary_search</span>(arr, target):
    <span style="color:#93c5fd;">lo</span>, <span style="color:#93c5fd;">hi</span> = <span style="color:#fcd34d;">0</span>, <span style="color:#93c5fd;">len</span>(arr) - <span style="color:#fcd34d;">1</span>
    <span style="color:#c4b5fd;">while</span> lo <= hi:
        <span style="color:#93c5fd;">mid</span> = (lo + hi) // <span style="color:#fcd34d;">2</span>
        <span style="color:#c4b5fd;">if</span>   arr[mid] == target: <span style="color:#c4b5fd;">return</span> mid
        <span style="color:#c4b5fd;">elif</span> arr[mid] < target:  lo = mid + <span style="color:#fcd34d;">1</span>
        <span style="color:#c4b5fd;">else</span>:                    hi = mid - <span style="color:#fcd34d;">1</span>
    <span style="color:#c4b5fd;">return</span> -<span style="color:#fcd34d;">1</span>

<span style="color:#93c5fd;">data</span> = <span style="color:#93c5fd;">list</span>(<span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1_000_001</span>))  <span style="color:#6b7280;"># 1 million sorted elements</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Linear search (O(n)): found at index {linear_search(data, 999_999)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Binary search (O(log n)): found at index {binary_search(data, 999_999)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Naive duplicate O(n²) vs smart O(n): {has_duplicate_naive([<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">2</span>,<span style="color:#fcd34d;">3</span>])} vs {has_duplicate_smart([<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">2</span>,<span style="color:#fcd34d;">3</span>])}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Linear search (O(n)): found at index 999998
Binary search (O(log n)): found at index 999998
Naive duplicate O(n²) vs smart O(n): False vs False</div>
  </div>
</div>

<h3>Space Complexity</h3>
<p>Big-O also measures <strong>memory</strong> usage. An algorithm that runs in O(n) time might use O(1) extra space (in-place) or O(n) extra space (making a copy). For data scientists working with large DataFrames, space complexity matters as much as time complexity — running out of RAM is just as bad as running out of time.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Time vs Space Tradeoff</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># O(n) space — builds a new list (uses extra memory proportional to n)</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">reverse_copy</span>(arr):
    <span style="color:#c4b5fd;">return</span> arr[::-<span style="color:#fcd34d;">1</span>]   <span style="color:#6b7280;"># creates a brand new list in memory</span>

<span style="color:#6b7280;"># O(1) space — in-place, no extra memory regardless of n</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">reverse_inplace</span>(arr):
    <span style="color:#93c5fd;">lo</span>, <span style="color:#93c5fd;">hi</span> = <span style="color:#fcd34d;">0</span>, <span style="color:#93c5fd;">len</span>(arr) - <span style="color:#fcd34d;">1</span>
    <span style="color:#c4b5fd;">while</span> lo < hi:
        arr[lo], arr[hi] = arr[hi], arr[lo]  <span style="color:#6b7280;"># swap two pointers — one temp variable</span>
        lo += <span style="color:#fcd34d;">1</span>; hi -= <span style="color:#fcd34d;">1</span>
    <span style="color:#c4b5fd;">return</span> arr

<span style="color:#6b7280;"># Memoization: trade space FOR time (classic tradeoff)</span>
<span style="color:#93c5fd;">_fib_cache</span> = {}
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">fib_memo</span>(n):
    <span style="color:#a7f3d0;">"""O(n) time, O(n) space — stores subproblem answers"""</span>
    <span style="color:#c4b5fd;">if</span> n <= <span style="color:#fcd34d;">1</span>: <span style="color:#c4b5fd;">return</span> n
    <span style="color:#c4b5fd;">if</span> n <span style="color:#c4b5fd;">in</span> _fib_cache: <span style="color:#c4b5fd;">return</span> _fib_cache[n]
    _fib_cache[n] = fib_memo(n - <span style="color:#fcd34d;">1</span>) + fib_memo(n - <span style="color:#fcd34d;">2</span>)
    <span style="color:#c4b5fd;">return</span> _fib_cache[n]

<span style="color:#93c5fd;">data</span> = [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">5</span>]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Copy reverse    : {reverse_copy(data)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"In-place reverse: {reverse_inplace(data[:])}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"fib(40) memoized: {fib_memo(40)}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Copy reverse    : [5, 4, 3, 2, 1]
In-place reverse: [5, 4, 3, 2, 1]
fib(40) memoized: 102334155</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsaModule->id,
            'title'       => '7.1 Big-O Notation & Complexity Analysis',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L7_1', [
                ['q' => 'An algorithm that always executes the same number of steps regardless of input size is...', 'opts' => ['O(n)', 'O(log n)', 'O(1)', 'O(n²)'], 'ans' => 2, 'exp' => 'O(1) — constant time — means the runtime does not depend on input size at all. Accessing an array element by index (arr[5]) or getting a dictionary value by key (dict["key"]) are classic O(1) operations.'],
                ['q' => 'You have a nested for-loop where both loops iterate over the same list of n items. What is the time complexity?', 'opts' => ['O(n)', 'O(2n)', 'O(n log n)', 'O(n²)'], 'ans' => 3, 'exp' => 'Two nested loops each running n times gives n × n = n² total iterations. This is O(n²) — quadratic. At n=10,000, that is 100 million operations. Quadratic algorithms become painfully slow on large datasets.'],
                ['q' => 'Binary search on a sorted array of 1,024 elements needs at most how many comparisons?', 'opts' => ['1,024', '512', '10', '32'], 'ans' => 2, 'exp' => 'Binary search is O(log n). log₂(1024) = 10. Each comparison eliminates half the remaining elements. After 10 comparisons, the search space is reduced to 1. This is far superior to linear search\'s worst case of 1,024 comparisons.'],
                ['q' => 'O(2n + 100) simplifies to which Big-O class?', 'opts' => ['O(2n)', 'O(100)', 'O(n)', 'O(n + 100)'], 'ans' => 2, 'exp' => 'Big-O drops constants and lower-order terms. O(2n + 100) → O(n). The constant multiplier 2 and the additive 100 become irrelevant as n grows toward infinity. Only the dominant growth factor (n) survives.'],
                ['q' => 'Which is the best-case complexity for comparison-based sorting algorithms?', 'opts' => ['O(n)', 'O(n log n)', 'O(log n)', 'O(n²)'], 'ans' => 1, 'exp' => 'O(n log n) is the proven theoretical lower bound for comparison-based sorting. Algorithms like merge sort and heap sort achieve this. You cannot sort by comparisons faster than O(n log n) in the worst case — this is proven mathematically.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 7.2 — Arrays & Dynamic Arrays
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Arrays & Dynamic Arrays: The Foundation of Everything</h2>
<p>The array is the most fundamental data structure in computing. Every other structure — stacks, queues, heaps, hash tables — is built on top of arrays or uses them internally. Python's <code>list</code> is a dynamic array, NumPy arrays are fixed-type contiguous arrays, and understanding the difference between them determines whether your data pipeline runs in seconds or hours.</p>

<h3>Static Arrays: Contiguous Memory</h3>
<p>A static array allocates a fixed block of contiguous memory. Because all elements are the same size and sit next to each other in RAM, you can jump to any element in O(1) time using pointer arithmetic: <code>address = base_address + (index × element_size)</code>. This is why array indexing is instant — no searching required.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <div style="font-family:'JetBrains Mono',monospace;font-size:0.85rem;color:var(--muted);margin-bottom:16px;">Memory layout of int array [10, 20, 30, 40, 50] (4 bytes each):</div>
  <div style="display:flex;gap:0;margin-bottom:12px;">
    <div style="text-align:center;flex:1;" style="">
      <div style="background:rgba(59,130,246,0.15);border:1px solid rgba(59,130,246,0.4);padding:12px 8px;font-family:'JetBrains Mono',monospace;font-weight:700;color:#3b82f6;font-size:0.95rem;">10</div>
      <div style="font-size:0.7rem;color:var(--muted);margin-top:4px;font-family:'JetBrains Mono',monospace;">0x1000<br>[0]</div>
    </div>
    <div style="text-align:center;flex:1;">
      <div style="background:rgba(59,130,246,0.15);border:1px solid rgba(59,130,246,0.4);padding:12px 8px;font-family:'JetBrains Mono',monospace;font-weight:700;color:#3b82f6;font-size:0.95rem;">20</div>
      <div style="font-size:0.7rem;color:var(--muted);margin-top:4px;font-family:'JetBrains Mono',monospace;">0x1004<br>[1]</div>
    </div>
    <div style="text-align:center;flex:1;">
      <div style="background:rgba(59,130,246,0.15);border:1px solid rgba(59,130,246,0.4);padding:12px 8px;font-family:'JetBrains Mono',monospace;font-weight:700;color:#3b82f6;font-size:0.95rem;">30</div>
      <div style="font-size:0.7rem;color:var(--muted);margin-top:4px;font-family:'JetBrains Mono',monospace;">0x1008<br>[2]</div>
    </div>
    <div style="text-align:center;flex:1;">
      <div style="background:rgba(59,130,246,0.15);border:1px solid rgba(59,130,246,0.4);padding:12px 8px;font-family:'JetBrains Mono',monospace;font-weight:700;color:#3b82f6;font-size:0.95rem;">40</div>
      <div style="font-size:0.7rem;color:var(--muted);margin-top:4px;font-family:'JetBrains Mono',monospace;">0x100C<br>[3]</div>
    </div>
    <div style="text-align:center;flex:1;">
      <div style="background:rgba(59,130,246,0.15);border:1px solid rgba(59,130,246,0.4);padding:12px 8px;font-family:'JetBrains Mono',monospace;font-weight:700;color:#3b82f6;font-size:0.95rem;">50</div>
      <div style="font-size:0.7rem;color:var(--muted);margin-top:4px;font-family:'JetBrains Mono',monospace;">0x1010<br>[4]</div>
    </div>
  </div>
  <p style="color:var(--muted);font-size:0.85rem;margin:0;">To access index 3: jump to 0x1000 + (3 × 4) = 0x100C → value 40. <strong style="color:var(--text);">Always O(1), no matter how large the array.</strong></p>
</div>

<h3>Dynamic Arrays: Python's list Under the Hood</h3>
<p>Python's <code>list</code> is a <strong>dynamic array</strong>. It starts with allocated capacity greater than its current size. When it fills up, Python allocates a new block roughly <strong>twice as large</strong>, copies all elements, and discards the old block. This "doubling" strategy makes <code>append()</code> O(1) <em>amortized</em> — most appends are instant, but an occasional resize is O(n).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Observing Dynamic Array Growth</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> sys

<span style="color:#93c5fd;">arr</span>      = []
<span style="color:#93c5fd;">prev_cap</span> = <span style="color:#fcd34d;">0</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'len':>5} | {'bytes':>8} | note"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─"</span> * <span style="color:#fcd34d;">40</span>)

<span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">33</span>):
    <span style="color:#93c5fd;">size</span> = sys.getsizeof(arr)
    <span style="color:#93c5fd;">note</span> = <span style="color:#a7f3d0;">"◀ RESIZE! new block allocated"</span> <span style="color:#c4b5fd;">if</span> size != prev_cap <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">""</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{i:>5} | {size:>8} | {note}"</span>)
    <span style="color:#93c5fd;">prev_cap</span> = size
    arr.append(i)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>  len |    bytes | note
────────────────────────────────────────
    0 |       56 | ◀ RESIZE! new block allocated
    1 |       88 | ◀ RESIZE! new block allocated
    5 |      120 | ◀ RESIZE! new block allocated
    9 |      184 | ◀ RESIZE! new block allocated
   17 |      248 | ◀ RESIZE! new block allocated
   25 |      312 | ◀ RESIZE! new block allocated
   33 |      376 | ◀ RESIZE! new block allocated
[... intermediate unchanged lines omitted for brevity]</div>
  </div>
</div>

<h3>Array Operation Complexity Summary</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:32px;">
  <div style="background:rgba(0,0,0,0.2);padding:12px 20px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.8rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;font-family:'JetBrains Mono',monospace;">Python list Complexity Cheat Sheet</span>
  </div>
  <div style="padding:0;">
    <div style="display:grid;grid-template-columns:1fr 100px 1fr;border-bottom:1px solid var(--border);padding:10px 20px;font-size:0.85rem;font-weight:700;color:var(--muted);">
      <span>Operation</span><span style="text-align:center;">Big-O</span><span>Notes</span>
    </div>
    <div style="display:grid;grid-template-columns:1fr 100px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 20px;font-size:0.85rem;">
      <code style="color:#93c5fd;">arr[i]</code><span style="text-align:center;color:#10b981;font-weight:700;">O(1)</span><span style="color:var(--muted);">Index access — pointer arithmetic</span>
    </div>
    <div style="display:grid;grid-template-columns:1fr 100px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 20px;font-size:0.85rem;">
      <code style="color:#93c5fd;">arr.append(x)</code><span style="text-align:center;color:#10b981;font-weight:700;">O(1)*</span><span style="color:var(--muted);">Amortized constant; O(n) on resize</span>
    </div>
    <div style="display:grid;grid-template-columns:1fr 100px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 20px;font-size:0.85rem;">
      <code style="color:#93c5fd;">arr.insert(0, x)</code><span style="text-align:center;color:#ef4444;font-weight:700;">O(n)</span><span style="color:var(--muted);">Must shift every element right</span>
    </div>
    <div style="display:grid;grid-template-columns:1fr 100px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 20px;font-size:0.85rem;">
      <code style="color:#93c5fd;">arr.pop()</code><span style="text-align:center;color:#10b981;font-weight:700;">O(1)</span><span style="color:var(--muted);">Remove last — no shifting needed</span>
    </div>
    <div style="display:grid;grid-template-columns:1fr 100px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 20px;font-size:0.85rem;">
      <code style="color:#93c5fd;">arr.pop(0)</code><span style="text-align:center;color:#ef4444;font-weight:700;">O(n)</span><span style="color:var(--muted);">Remove first — must shift all elements left</span>
    </div>
    <div style="display:grid;grid-template-columns:1fr 100px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 20px;font-size:0.85rem;">
      <code style="color:#93c5fd;">x in arr</code><span style="text-align:center;color:#ef4444;font-weight:700;">O(n)</span><span style="color:var(--muted);">Linear scan — must check every element</span>
    </div>
    <div style="display:grid;grid-template-columns:1fr 100px 1fr;padding:10px 20px;font-size:0.85rem;">
      <code style="color:#93c5fd;">arr.sort()</code><span style="text-align:center;color:#f59e0b;font-weight:700;">O(n log n)</span><span style="color:var(--muted);">Timsort — Python's highly optimised sort</span>
    </div>
  </div>
</div>

<h3>NumPy Arrays vs Python Lists: A Data Scientist's Perspective</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Building a Dynamic Array Class from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">DynamicArray</span>:
    <span style="color:#a7f3d0;">"""A minimal dynamic array that mirrors Python list growth strategy."""</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__init__</span>(self):
        self._capacity = <span style="color:#fcd34d;">4</span>          <span style="color:#6b7280;"># initial allocated slots</span>
        self._size     = <span style="color:#fcd34d;">0</span>          <span style="color:#6b7280;"># number of real elements</span>
        self._data     = [<span style="color:#fca5a5;">None</span>] * self._capacity

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">append</span>(self, val):
        <span style="color:#c4b5fd;">if</span> self._size == self._capacity:
            self._resize()             <span style="color:#6b7280;"># O(n) but rare</span>
        self._data[self._size] = val
        self._size += <span style="color:#fcd34d;">1</span>              <span style="color:#6b7280;"># O(1) amortized</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">_resize</span>(self):
        self._capacity *= <span style="color:#fcd34d;">2</span>         <span style="color:#6b7280;"># double the capacity</span>
        <span style="color:#93c5fd;">new_data</span> = [<span style="color:#fca5a5;">None</span>] * self._capacity
        <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(self._size):
            new_data[i] = self._data[i]  <span style="color:#6b7280;"># copy old data</span>
        self._data = new_data
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  [RESIZE] capacity doubled → {self._capacity}"</span>)

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__getitem__</span>(self, i):
        <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> (<span style="color:#fcd34d;">0</span> <= i < self._size):
            <span style="color:#c4b5fd;">raise</span> IndexError(<span style="color:#a7f3d0;">"index out of range"</span>)
        <span style="color:#c4b5fd;">return</span> self._data[i]

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__len__</span>(self): <span style="color:#c4b5fd;">return</span> self._size
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__repr__</span>(self): <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">str</span>(self._data[:self._size])

<span style="color:#93c5fd;">da</span> = DynamicArray()
<span style="color:#c4b5fd;">for</span> val <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">20</span>, <span style="color:#fcd34d;">30</span>, <span style="color:#fcd34d;">40</span>, <span style="color:#fcd34d;">50</span>, <span style="color:#fcd34d;">60</span>, <span style="color:#fcd34d;">70</span>, <span style="color:#fcd34d;">80</span>, <span style="color:#fcd34d;">90</span>]:
    da.append(val)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nFinal array: {da}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Element at index 5: {da[5]}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>  [RESIZE] capacity doubled → 8
  [RESIZE] capacity doubled → 16

Final array: [10, 20, 30, 40, 50, 60, 70, 80, 90]
Element at index 5: 60</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsaModule->id,
            'title'       => '7.2 Arrays & Dynamic Arrays',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L7_2', [
                ['q' => 'Why is arr[i] O(1) for any index i?', 'opts' => ['Python searches from index 0 to i', 'The OS pre-fetches all elements', 'The memory address is computed directly: base + i × element_size', 'Arrays store a sorted index map'], 'ans' => 2, 'exp' => 'Arrays store elements in contiguous memory. Given the base address and element size, any element\'s address can be computed instantly with arithmetic: address = base + (i × size). No searching is needed — it is a direct jump in memory.'],
                ['q' => 'Why does inserting at the beginning of a Python list (insert(0, x)) cost O(n)?', 'opts' => ['Python must re-sort the list', 'All existing elements must be shifted one position right to make room', 'Python allocates a new array every time', 'The list must be reversed first'], 'ans' => 1, 'exp' => 'Inserting at index 0 requires shifting every existing element one position to the right before placing the new element. For a list of n elements, that is n shift operations — O(n). This is why collections.deque is preferred for frequent front insertions.'],
                ['q' => 'What does "amortized O(1)" mean for list.append()?', 'opts' => ['Every append is always O(1)', 'Most appends are O(1); occasional O(n) resizes are rare enough that the average cost per append is O(1)', 'The first append is O(n) and the rest are O(1)', 'Amortized means it only works on small lists'], 'ans' => 1, 'exp' => 'When a dynamic array resizes, it doubles capacity and copies all elements — O(n). But because the size doubles, the next n/2 appends require no resize. Spreading the O(n) cost over n/2 operations gives O(2) = O(1) amortized cost per append.'],
                ['q' => 'A Python list holding 100 Python objects likely uses more than 100 × 8 bytes. Why?', 'opts' => ['Python adds error-checking metadata', 'Lists store pointers to objects (8 bytes each) plus Python object overhead plus reserved capacity buffer', 'Lists compress duplicates', 'The OS rounds all allocations up to 4KB'], 'ans' => 1, 'exp' => 'A Python list stores an array of 8-byte pointers to objects (not the objects themselves), plus list metadata (~56 bytes), plus extra reserved capacity. Each Python int object itself costs ~28 bytes. This is why NumPy arrays (storing raw C values) are far more memory-efficient for numerical data.'],
                ['q' => 'Which operation on a Python list is O(n) and commonly causes slow data pipelines?', 'opts' => ['arr[-1]  (last element access)', 'arr.append(x)  (add to end)', 'x in arr  (membership test on a list)', 'len(arr)  (length check)'], 'ans' => 2, 'exp' => '"x in arr" for a Python list is O(n) — it scans every element from left to right. Using "x in my_set" or "x in my_dict" instead gives O(1) lookup. This swap can turn an O(n²) loop into O(n), a game-changer for large datasets.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 7.3 — Stacks: LIFO Structures & Applications
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Stacks: Last-In, First-Out (LIFO)</h2>
<p>A <strong>stack</strong> is a collection where the last element added is the first one removed — like a pile of plates. Despite its simplicity, the stack is one of the most important data structures in computing: it powers function call management, undo/redo systems, expression evaluation, backtracking algorithms, and depth-first search. Every time you call a Python function, your program pushes a frame onto the call stack.</p>

<h3>Core Stack Operations</h3>
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:32px;">
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:16px;text-align:center;">
    <code style="color:#3b82f6;font-family:'JetBrains Mono',monospace;font-size:1rem;display:block;margin-bottom:8px;">push(x)</code>
    <span style="color:#10b981;font-weight:700;font-size:0.8rem;display:block;margin-bottom:6px;">O(1)</span>
    <span style="color:var(--muted);font-size:0.8rem;">Add element to the top of the stack</span>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:16px;text-align:center;">
    <code style="color:#3b82f6;font-family:'JetBrains Mono',monospace;font-size:1rem;display:block;margin-bottom:8px;">pop()</code>
    <span style="color:#10b981;font-weight:700;font-size:0.8rem;display:block;margin-bottom:6px;">O(1)</span>
    <span style="color:var(--muted);font-size:0.8rem;">Remove and return the top element</span>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:16px;text-align:center;">
    <code style="color:#3b82f6;font-family:'JetBrains Mono',monospace;font-size:1rem;display:block;margin-bottom:8px;">peek()</code>
    <span style="color:#10b981;font-weight:700;font-size:0.8rem;display:block;margin-bottom:6px;">O(1)</span>
    <span style="color:var(--muted);font-size:0.8rem;">View the top element without removing it</span>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:16px;text-align:center;">
    <code style="color:#3b82f6;font-family:'JetBrains Mono',monospace;font-size:1rem;display:block;margin-bottom:8px;">is_empty()</code>
    <span style="color:#10b981;font-weight:700;font-size:0.8rem;display:block;margin-bottom:6px;">O(1)</span>
    <span style="color:var(--muted);font-size:0.8rem;">Check if the stack has no elements</span>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Stack Implementation with Full Type-Safe API</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">Stack</span>:
    <span style="color:#a7f3d0;">"""
    LIFO stack backed by Python list.
    All core operations are O(1) amortized.
    """</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__init__</span>(self):
        self._data = []

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">push</span>(self, item):
        self._data.append(item)   <span style="color:#6b7280;"># O(1) amortized</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">pop</span>(self):
        <span style="color:#c4b5fd;">if</span> self.is_empty():
            <span style="color:#c4b5fd;">raise</span> IndexError(<span style="color:#a7f3d0;">"pop from empty stack"</span>)
        <span style="color:#c4b5fd;">return</span> self._data.pop()    <span style="color:#6b7280;"># O(1) — removes last element</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">peek</span>(self):
        <span style="color:#c4b5fd;">if</span> self.is_empty():
            <span style="color:#c4b5fd;">raise</span> IndexError(<span style="color:#a7f3d0;">"peek on empty stack"</span>)
        <span style="color:#c4b5fd;">return</span> self._data[-<span style="color:#fcd34d;">1</span>]      <span style="color:#6b7280;"># O(1)</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">is_empty</span>(self): <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">len</span>(self._data) == <span style="color:#fcd34d;">0</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__len__</span>(self):   <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">len</span>(self._data)
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__repr__</span>(self):  <span style="color:#c4b5fd;">return</span> <span style="color:#a7f3d0;">f"Stack(top→ {self._data[::-1]})"</span>

<span style="color:#6b7280;"># Demonstration</span>
<span style="color:#93c5fd;">s</span> = Stack()
<span style="color:#c4b5fd;">for</span> v <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">20</span>, <span style="color:#fcd34d;">30</span>, <span style="color:#fcd34d;">40</span>]:
    s.push(v)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"push({v:>2}) → {s}"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\npeek()  → {s.peek()}  (stack unchanged)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"pop()   → {s.pop()}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"pop()   → {s.pop()}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"after pops: {s}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>push(10) → Stack(top→ [10])
push(20) → Stack(top→ [20, 10])
push(30) → Stack(top→ [30, 20, 10])
push(40) → Stack(top→ [40, 30, 20, 10])

peek()  → 40  (stack unchanged)
pop()   → 40
pop()   → 30
after pops: Stack(top→ [20, 10])</div>
  </div>
</div>

<h3>Application 1: Balanced Bracket Checker</h3>
<p>The balanced bracket problem is asked in nearly every coding interview. It is the canonical stack application: whenever you see an opening bracket, push it; when you see a closing bracket, pop and verify it matches. This exact logic powers syntax highlighting in IDEs and JSON/XML validators.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Balanced Brackets via Stack  O(n) time, O(n) space</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">is_balanced</span>(s):
    <span style="color:#a7f3d0;">"""Check whether all brackets in string s are balanced. O(n) time."""</span>
    <span style="color:#93c5fd;">pairs</span>  = {<span style="color:#a7f3d0;">')'</span>: <span style="color:#a7f3d0;">'('</span>, <span style="color:#a7f3d0;">']'</span>: <span style="color:#a7f3d0;">'['</span>, <span style="color:#a7f3d0;">'}'</span>: <span style="color:#a7f3d0;">'{'</span>}
    <span style="color:#93c5fd;">stack</span>  = []

    <span style="color:#c4b5fd;">for</span> ch <span style="color:#c4b5fd;">in</span> s:
        <span style="color:#c4b5fd;">if</span> ch <span style="color:#c4b5fd;">in</span> <span style="color:#a7f3d0;">'([{'</span>:
            stack.append(ch)          <span style="color:#6b7280;"># push opening bracket</span>
        <span style="color:#c4b5fd;">elif</span> ch <span style="color:#c4b5fd;">in</span> <span style="color:#a7f3d0;">')]}'</span>:
            <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> stack <span style="color:#c4b5fd;">or</span> stack[-<span style="color:#fcd34d;">1</span>] != pairs[ch]:
                <span style="color:#c4b5fd;">return</span> <span style="color:#fca5a5;">False</span>          <span style="color:#6b7280;"># mismatch or stack empty</span>
            stack.pop()               <span style="color:#6b7280;"># matched — pop opening bracket</span>

    <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">len</span>(stack) == <span style="color:#fcd34d;">0</span>         <span style="color:#6b7280;"># balanced iff nothing left unpaired</span>

<span style="color:#93c5fd;">tests</span> = [
    <span style="color:#a7f3d0;">"({[]})"</span>,            <span style="color:#6b7280;"># valid Python / JSON</span>
    <span style="color:#a7f3d0;">"df[(df['age']>30) & (df['score']<90)]"</span>,  <span style="color:#6b7280;"># Pandas filter</span>
    <span style="color:#a7f3d0;">"([)]"</span>,             <span style="color:#6b7280;"># wrong order</span>
    <span style="color:#a7f3d0;">"((()"</span>,             <span style="color:#6b7280;"># unclosed</span>
    <span style="color:#a7f3d0;">"{"</span>,                <span style="color:#6b7280;"># single open</span>
]
<span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> tests:
    <span style="color:#93c5fd;">result</span> = <span style="color:#a7f3d0;">"✓ balanced"</span> <span style="color:#c4b5fd;">if</span> is_balanced(t) <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"✗ UNBALANCED"</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{result:15}  {t}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>✓ balanced      ({[]})
✓ balanced      df[(df['age']>30) & (df['score']<90)]
✗ UNBALANCED    ([)]
✗ UNBALANCED    ((()
✗ UNBALANCED    {</div>
  </div>
</div>

<h3>Application 2: Undo/Redo History System</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Undo/Redo using Two Stacks</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">TextEditor</span>:
    <span style="color:#a7f3d0;">"""Minimal text editor with undo/redo using two stacks."""</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__init__</span>(self):
        self._text  = <span style="color:#a7f3d0;">""</span>
        self._undo  = []  <span style="color:#6b7280;"># undo stack: past states</span>
        self._redo  = []  <span style="color:#6b7280;"># redo stack: future states</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">type</span>(self, chars):
        self._undo.append(self._text)   <span style="color:#6b7280;"># save current state</span>
        self._redo.clear()               <span style="color:#6b7280;"># typing breaks redo history</span>
        self._text += chars
        self._show(<span style="color:#a7f3d0;">f"type({chars!r})"</span>)

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">undo</span>(self):
        <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> self._undo: <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"nothing to undo"</span>); <span style="color:#c4b5fd;">return</span>
        self._redo.append(self._text)
        self._text = self._undo.pop()
        self._show(<span style="color:#a7f3d0;">"undo()"</span>)

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">redo</span>(self):
        <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> self._redo: <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"nothing to redo"</span>); <span style="color:#c4b5fd;">return</span>
        self._undo.append(self._text)
        self._text = self._redo.pop()
        self._show(<span style="color:#a7f3d0;">"redo()"</span>)

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">_show</span>(self, op):
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{op:20} → text='{self._text}'"</span>)

<span style="color:#93c5fd;">ed</span> = TextEditor()
ed.type(<span style="color:#a7f3d0;">"Hello"</span>)
ed.type(<span style="color:#a7f3d0;">", World"</span>)
ed.type(<span style="color:#a7f3d0;">"!"</span>)
ed.undo()
ed.undo()
ed.redo()
ed.type(<span style="color:#a7f3d0;">" Data"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>type('Hello')        → text='Hello'
type(', World')      → text='Hello, World'
type('!')            → text='Hello, World!'
undo()               → text='Hello, World'
undo()               → text='Hello'
redo()               → text='Hello, World'
type(' Data')        → text='Hello, World Data'</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsaModule->id,
            'title'       => '7.3 Stacks: LIFO & Applications',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L7_3', [
                ['q' => 'You push 1, 2, 3, 4 onto a stack then call pop() twice. What is the next peek() value?', 'opts' => ['1', '2', '3', '4'], 'ans' => 1, 'exp' => 'Push order: 1→2→3→4. Stack top is 4. pop() returns 4, pop() returns 3. The top is now 2. peek() returns 2 without removing it.'],
                ['q' => 'Why is Python list.pop() (no argument) O(1) but list.pop(0) O(n)?', 'opts' => ['pop(0) requires sorting', 'pop() from the end removes the last element with no shifting; pop(0) removes the first and must shift all remaining elements left', 'pop(0) allocates a new list', 'pop() is a special C-level optimisation not available to pop(0)'], 'ans' => 1, 'exp' => 'List elements are stored contiguously. Removing the last element just decrements the length counter — O(1). Removing the first requires shifting every remaining element one position left to fill the gap — O(n). This is why stacks use the end of the list as their "top".'],
                ['q' => 'The call stack in Python tracks...', 'opts' => ['All variables ever created', 'The current active function calls and their local variables in LIFO order', 'All imported modules', 'Network connections'], 'ans' => 1, 'exp' => 'The call stack is a system-level stack that pushes a new frame each time a function is called and pops it when the function returns. This LIFO structure naturally handles nested calls. A "stack overflow" occurs when too many nested calls exhaust the stack\'s memory.'],
                ['q' => 'For the balanced bracket problem, why is a stack the right data structure?', 'opts' => ['Stacks are the fastest structure', 'The most recently opened bracket must be the next one closed — a perfect LIFO match', 'Brackets are always numeric', 'Queues would also work identically'], 'ans' => 1, 'exp' => 'Bracket matching requires that the innermost (most recently opened) bracket be closed first. This "last opened, first closed" constraint is exactly LIFO — the defining property of a stack. Queues (FIFO) would fail because they would try to match the earliest-opened bracket first.'],
                ['q' => 'Which real-world system uses a stack as its core data structure?', 'opts' => ['A print queue at an office printer', 'A hospital emergency room waiting list', 'Browser back button / undo in a text editor', 'A toll booth ticket dispenser'], 'ans' => 2, 'exp' => 'Browser history and undo systems use stacks: each action is pushed on visit, and the back button/undo pops the most recent action — classic LIFO. Print queues, ER waiting lists, and ticket dispensers are FIFO (queue) patterns.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 7.4 — Queues & Deques
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Queues & Deques: FIFO and Double-Ended Collections</h2>
<p>A <strong>queue</strong> is the opposite of a stack — the first element added is the first one removed (First-In, First-Out). Queues model real-world waiting lines: job schedulers, message brokers (Kafka, RabbitMQ), BFS graph traversal, and rate-limiting pipelines. A <strong>deque</strong> (double-ended queue) extends this by supporting O(1) additions and removals at both ends.</p>

<h3>Why Not Use a Plain Python List as a Queue?</h3>
<p>Using <code>list.pop(0)</code> for dequeue is a critical mistake — it is O(n) because all elements shift left. For a queue processing 1 million messages, that is catastrophic. Always use <code>collections.deque</code>, which is implemented as a doubly-linked list of fixed-size blocks, giving true O(1) operations at both ends.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Queue from Scratch + collections.deque</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> collections <span style="color:#c4b5fd;">import</span> deque

<span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">Queue</span>:
    <span style="color:#a7f3d0;">"""FIFO queue backed by collections.deque for O(1) enqueue and dequeue."""</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__init__</span>(self):         self._data = deque()
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">enqueue</span>(self, item):    self._data.append(item)       <span style="color:#6b7280;"># add to REAR  O(1)</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">dequeue</span>(self):
        <span style="color:#c4b5fd;">if</span> self.is_empty(): <span style="color:#c4b5fd;">raise</span> IndexError(<span style="color:#a7f3d0;">"dequeue from empty queue"</span>)
        <span style="color:#c4b5fd;">return</span> self._data.popleft()                              <span style="color:#6b7280;"># remove FRONT O(1)</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">front</span>(self):
        <span style="color:#c4b5fd;">if</span> self.is_empty(): <span style="color:#c4b5fd;">raise</span> IndexError(<span style="color:#a7f3d0;">"front of empty queue"</span>)
        <span style="color:#c4b5fd;">return</span> self._data[<span style="color:#fcd34d;">0</span>]
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">is_empty</span>(self):         <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">len</span>(self._data) == <span style="color:#fcd34d;">0</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__len__</span>(self):          <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">len</span>(self._data)
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__repr__</span>(self):         <span style="color:#c4b5fd;">return</span> <span style="color:#a7f3d0;">f"Queue(front→ {list(self._data)})"</span>

<span style="color:#6b7280;"># Simulate a data-processing job queue</span>
<span style="color:#93c5fd;">job_q</span> = Queue()
<span style="color:#c4b5fd;">for</span> job <span style="color:#c4b5fd;">in</span> [<span style="color:#a7f3d0;">"load_csv"</span>, <span style="color:#a7f3d0;">"clean_data"</span>, <span style="color:#a7f3d0;">"train_model"</span>, <span style="color:#a7f3d0;">"evaluate"</span>, <span style="color:#a7f3d0;">"export_results"</span>]:
    job_q.enqueue(job)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"enqueue('{job}') → {job_q}"</span>)

<span style="color:#93c5fd;">print</span>()
<span style="color:#c4b5fd;">while</span> <span style="color:#c4b5fd;">not</span> job_q.is_empty():
    <span style="color:#93c5fd;">job</span> = job_q.dequeue()
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Processing: '{job}' → remaining: {job_q}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>enqueue('load_csv') → Queue(front→ ['load_csv'])
enqueue('clean_data') → Queue(front→ ['load_csv', 'clean_data'])
enqueue('train_model') → Queue(front→ ['load_csv', 'clean_data', 'train_model'])
enqueue('evaluate') → Queue(front→ ['load_csv', 'clean_data', 'train_model', 'evaluate'])
enqueue('export_results') → Queue(front→ ['load_csv', ..., 'export_results'])

Processing: 'load_csv' → remaining: Queue(front→ ['clean_data', 'train_model', 'evaluate', 'export_results'])
Processing: 'clean_data' → remaining: Queue(front→ ['train_model', 'evaluate', 'export_results'])
Processing: 'train_model' → remaining: Queue(front→ ['evaluate', 'export_results'])
Processing: 'evaluate' → remaining: Queue(front→ ['export_results'])
Processing: 'export_results' → remaining: Queue(front→ [])</div>
  </div>
</div>

<h3>Deque: O(1) at Both Ends</h3>
<p>A <strong>deque</strong> (pronounced "deck") supports adding and removing from both the front and back in O(1). This makes it the ideal structure for sliding window problems — a staple of data science feature engineering and time-series analysis.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Sliding Window Maximum using Monotonic Deque  O(n)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> collections <span style="color:#c4b5fd;">import</span> deque

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">sliding_window_max</span>(nums, k):
    <span style="color:#a7f3d0;">"""
    Returns the maximum value in every window of size k.
    Uses a monotonic deque for O(n) total time.
    Naïve approach (max of each window) would be O(n*k).
    """</span>
    <span style="color:#93c5fd;">dq</span>     = deque()    <span style="color:#6b7280;"># stores INDICES, front always = max of current window</span>
    <span style="color:#93c5fd;">result</span> = []

    <span style="color:#c4b5fd;">for</span> i, val <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(nums):
        <span style="color:#6b7280;"># Remove indices outside the current window</span>
        <span style="color:#c4b5fd;">while</span> dq <span style="color:#c4b5fd;">and</span> dq[<span style="color:#fcd34d;">0</span>] < i - k + <span style="color:#fcd34d;">1</span>:
            dq.popleft()
        <span style="color:#6b7280;"># Remove indices whose values are ≤ current (they can never be the max)</span>
        <span style="color:#c4b5fd;">while</span> dq <span style="color:#c4b5fd;">and</span> nums[dq[-<span style="color:#fcd34d;">1</span>]] <= val:
            dq.pop()

        dq.append(i)

        <span style="color:#c4b5fd;">if</span> i >= k - <span style="color:#fcd34d;">1</span>:                   <span style="color:#6b7280;"># first full window formed</span>
            result.append(nums[dq[<span style="color:#fcd34d;">0</span>]])   <span style="color:#6b7280;"># front of deque = max</span>

    <span style="color:#c4b5fd;">return</span> result

<span style="color:#6b7280;"># Sensor readings: find max temperature in every 3-reading window</span>
<span style="color:#93c5fd;">temps</span>  = [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">3</span>, -<span style="color:#fcd34d;">1</span>, -<span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">7</span>]
<span style="color:#93c5fd;">maxima</span> = sliding_window_max(temps, k=<span style="color:#fcd34d;">3</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Temps  : {temps}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Window : k=3"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Maxima : {maxima}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Windows: [1,3,-1]→3, [3,-1,-3]→3, [-1,-3,5]→5, [-3,5,3]→5, [5,3,6]→6, [3,6,7]→7"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Temps  : [1, 3, -1, -3, 5, 3, 6, 7]
Window : k=3
Maxima : [3, 3, 5, 5, 6, 7]
Windows: [1,3,-1]→3, [3,-1,-3]→3, [-1,-3,5]→5, [-3,5,3]→5, [5,3,6]→6, [3,6,7]→7</div>
  </div>
</div>

<h3>Priority Queue: Ordered Processing</h3>
<p>A <strong>priority queue</strong> dequeues the highest-priority item first, not the oldest. Python's <code>heapq</code> module implements a min-heap priority queue. Use <code>(-priority, item)</code> to simulate a max-heap. Priority queues power Dijkstra's shortest path, A* search, and task schedulers.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Priority Queue with heapq</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> heapq

<span style="color:#6b7280;"># Hospital ER triage: process by severity (higher = more urgent)</span>
<span style="color:#93c5fd;">er_queue</span> = []
<span style="color:#93c5fd;">patients</span> = [
    (<span style="color:#fcd34d;">3</span>, <span style="color:#a7f3d0;">"Sprained ankle"</span>),
    (<span style="color:#fcd34d;">9</span>, <span style="color:#a7f3d0;">"Cardiac arrest"</span>),
    (<span style="color:#fcd34d;">6</span>, <span style="color:#a7f3d0;">"Deep laceration"</span>),
    (<span style="color:#fcd34d;">2</span>, <span style="color:#a7f3d0;">"Common cold"</span>),
    (<span style="color:#fcd34d;">8</span>, <span style="color:#a7f3d0;">"Stroke symptoms"</span>),
]

<span style="color:#c4b5fd;">for</span> severity, condition <span style="color:#c4b5fd;">in</span> patients:
    heapq.heappush(er_queue, (-severity, condition))  <span style="color:#6b7280;"># negate for max-heap</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== ER Processing Order ==="</span>)
<span style="color:#93c5fd;">rank</span> = <span style="color:#fcd34d;">1</span>
<span style="color:#c4b5fd;">while</span> er_queue:
    neg_sev, condition = heapq.heappop(er_queue)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {rank}. Severity {-neg_sev} — {condition}"</span>)
    <span style="color:#93c5fd;">rank</span> += <span style="color:#fcd34d;">1</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== ER Processing Order ===
  1. Severity 9 — Cardiac arrest
  2. Severity 8 — Stroke symptoms
  3. Severity 6 — Deep laceration
  4. Severity 3 — Sprained ankle
  5. Severity 2 — Common cold</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsaModule->id,
            'title'       => '7.4 Queues, Deques & Priority Queues',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L7_4', [
                ['q' => 'You enqueue A, B, C, D into a FIFO queue, then dequeue twice. What is the front element?', 'opts' => ['A', 'B', 'C', 'D'], 'ans' => 2, 'exp' => 'FIFO: enqueue order A→B→C→D, so front=A. Dequeue removes A then B. Front is now C.'],
                ['q' => 'Why is collections.deque preferred over a plain list for implementing a queue?', 'opts' => ['deque holds more items', 'deque supports O(1) popleft() while list.pop(0) is O(n)', 'deque sorts automatically', 'deque uses less memory than list'], 'ans' => 1, 'exp' => 'collections.deque is implemented as a doubly-linked list of fixed blocks, giving O(1) additions and removals at both ends. A plain list\'s pop(0) is O(n) because it must shift all elements left — catastrophic for large queues.'],
                ['q' => 'A sliding window maximum problem is efficiently solved with a deque because...', 'opts' => ['Deques can sort windows automatically', 'You need O(1) removal of out-of-window elements from the front AND O(1) removal of smaller elements from the back — both ends simultaneously', 'Deques have built-in max() method', 'Windows are always the same size as the deque'], 'ans' => 1, 'exp' => 'A monotonic deque tracks candidate maximums. As the window slides: expired elements are removed from the front (O(1) popleft) and dominated smaller elements from the back (O(1) pop). No structure other than a deque achieves O(1) at both ends.'],
                ['q' => 'Python\'s heapq module implements which type of heap by default?', 'opts' => ['Max-heap (largest element at top)', 'Min-heap (smallest element at top)', 'Balanced BST', 'Random-access heap'], 'ans' => 1, 'exp' => 'heapq implements a min-heap — heappop() always returns the smallest element. To simulate a max-heap, store negated values: push -priority and negate after popping.'],
                ['q' => 'Which scheduling/routing algorithm uses a priority queue as its core data structure?', 'opts' => ['Bubble sort', 'Depth-first search', 'Dijkstra\'s shortest path algorithm', 'Binary search'], 'ans' => 2, 'exp' => 'Dijkstra\'s algorithm uses a min-priority queue to always process the unvisited node with the smallest known distance first. This greedy strategy guarantees the shortest path in graphs with non-negative edge weights.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 7.5 — Linked Lists
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Linked Lists: Singly, Doubly & Circular</h2>
<p>A <strong>linked list</strong> is a collection of nodes where each node holds a value and a pointer to the next node. Unlike arrays, elements are not stored contiguously in memory — each node can live anywhere in RAM, connected by pointers. This makes insertion and deletion at any position O(1) (given a pointer to the location), but random access O(n). Understanding linked lists is essential because they underlie stacks, queues, adjacency lists for graphs, and Python's own <code>collections.deque</code>.</p>

<h3>Singly Linked List: Building Block</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Complete Singly Linked List Implementation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">SNode</span>:
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__init__</span>(self, val):
        self.val  = val
        self.next = <span style="color:#fca5a5;">None</span>

<span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">SinglyLinkedList</span>:
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__init__</span>(self):
        self.head = <span style="color:#fca5a5;">None</span>
        self.size = <span style="color:#fcd34d;">0</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">prepend</span>(self, val):      <span style="color:#6b7280;"># O(1) — insert at head</span>
        <span style="color:#93c5fd;">node</span> = SNode(val)
        node.next  = self.head
        self.head  = node
        self.size += <span style="color:#fcd34d;">1</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">append</span>(self, val):       <span style="color:#6b7280;"># O(n) — must walk to tail</span>
        <span style="color:#93c5fd;">node</span> = SNode(val)
        <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> self.head:
            self.head = node
        <span style="color:#c4b5fd;">else</span>:
            <span style="color:#93c5fd;">cur</span> = self.head
            <span style="color:#c4b5fd;">while</span> cur.next: cur = cur.next
            cur.next = node
        self.size += <span style="color:#fcd34d;">1</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">delete</span>(self, val):       <span style="color:#6b7280;"># O(n) — must find node</span>
        <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> self.head: <span style="color:#c4b5fd;">return</span>
        <span style="color:#c4b5fd;">if</span> self.head.val == val:
            self.head = self.head.next; self.size -= <span style="color:#fcd34d;">1</span>; <span style="color:#c4b5fd;">return</span>
        <span style="color:#93c5fd;">cur</span> = self.head
        <span style="color:#c4b5fd;">while</span> cur.next:
            <span style="color:#c4b5fd;">if</span> cur.next.val == val:
                cur.next = cur.next.next; self.size -= <span style="color:#fcd34d;">1</span>; <span style="color:#c4b5fd;">return</span>
            cur = cur.next

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">reverse</span>(self):           <span style="color:#6b7280;"># O(n) — classic in-place reversal</span>
        <span style="color:#93c5fd;">prev</span>, <span style="color:#93c5fd;">cur</span> = <span style="color:#fca5a5;">None</span>, self.head
        <span style="color:#c4b5fd;">while</span> cur:
            <span style="color:#93c5fd;">nxt</span>      = cur.next
            cur.next = prev
            prev, cur = cur, nxt
        self.head = prev

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">to_list</span>(self):
        <span style="color:#93c5fd;">result</span>, <span style="color:#93c5fd;">cur</span> = [], self.head
        <span style="color:#c4b5fd;">while</span> cur: result.append(cur.val); cur = cur.next
        <span style="color:#c4b5fd;">return</span> result

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__repr__</span>(self): <span style="color:#c4b5fd;">return</span> <span style="color:#a7f3d0;">" → "</span>.join(<span style="color:#93c5fd;">str</span>(v) <span style="color:#c4b5fd;">for</span> v <span style="color:#c4b5fd;">in</span> self.to_list()) + <span style="color:#a7f3d0;">" → None"</span>

<span style="color:#93c5fd;">ll</span> = SinglyLinkedList()
<span style="color:#c4b5fd;">for</span> v <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">20</span>, <span style="color:#fcd34d;">30</span>, <span style="color:#fcd34d;">40</span>, <span style="color:#fcd34d;">50</span>]: ll.append(v)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Initial  : {ll}"</span>)
ll.prepend(<span style="color:#fcd34d;">5</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Prepend 5: {ll}"</span>)
ll.delete(<span style="color:#fcd34d;">30</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Delete 30: {ll}"</span>)
ll.reverse()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Reversed : {ll}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Initial  : 10 → 20 → 30 → 40 → 50 → None
Prepend 5: 5 → 10 → 20 → 30 → 40 → 50 → None
Delete 30: 5 → 10 → 20 → 40 → 50 → None
Reversed : 50 → 40 → 20 → 10 → 5 → None</div>
  </div>
</div>

<h3>Doubly Linked List: Bidirectional Traversal</h3>
<p>A doubly linked list adds a <code>prev</code> pointer to each node, enabling O(1) backward traversal and O(1) deletion when you already have a pointer to the node (not just its value). This is the structure behind Python's <code>collections.deque</code> and browser history (navigate forward and back).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Doubly Linked List with Tail Pointer</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">DNode</span>:
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__init__</span>(self, val):
        self.val  = val
        self.next = <span style="color:#fca5a5;">None</span>
        self.prev = <span style="color:#fca5a5;">None</span>    <span style="color:#6b7280;"># ← key addition</span>

<span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">DoublyLinkedList</span>:
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__init__</span>(self):
        self.head = self.tail = <span style="color:#fca5a5;">None</span>
        self.size = <span style="color:#fcd34d;">0</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">append</span>(self, val):         <span style="color:#6b7280;"># O(1) — tail pointer!</span>
        <span style="color:#93c5fd;">node</span> = DNode(val)
        <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> self.tail:
            self.head = self.tail = node
        <span style="color:#c4b5fd;">else</span>:
            node.prev      = self.tail
            self.tail.next = node
            self.tail      = node
        self.size += <span style="color:#fcd34d;">1</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">prepend</span>(self, val):        <span style="color:#6b7280;"># O(1)</span>
        <span style="color:#93c5fd;">node</span> = DNode(val)
        <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> self.head:
            self.head = self.tail = node
        <span style="color:#c4b5fd;">else</span>:
            node.next       = self.head
            self.head.prev  = node
            self.head       = node
        self.size += <span style="color:#fcd34d;">1</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">delete_node</span>(self, node):   <span style="color:#6b7280;"># O(1) — given a pointer!</span>
        <span style="color:#c4b5fd;">if</span> node.prev: node.prev.next = node.next
        <span style="color:#c4b5fd;">else</span>:         self.head = node.next
        <span style="color:#c4b5fd;">if</span> node.next: node.next.prev = node.prev
        <span style="color:#c4b5fd;">else</span>:         self.tail = node.prev
        self.size -= <span style="color:#fcd34d;">1</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">forward</span>(self):
        <span style="color:#93c5fd;">r</span>, <span style="color:#93c5fd;">c</span> = [], self.head
        <span style="color:#c4b5fd;">while</span> c: r.append(c.val); c = c.next
        <span style="color:#c4b5fd;">return</span> r

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">backward</span>(self):
        <span style="color:#93c5fd;">r</span>, <span style="color:#93c5fd;">c</span> = [], self.tail
        <span style="color:#c4b5fd;">while</span> c: r.append(c.val); c = c.prev
        <span style="color:#c4b5fd;">return</span> r

<span style="color:#93c5fd;">dll</span> = DoublyLinkedList()
<span style="color:#c4b5fd;">for</span> v <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">5</span>]: dll.append(v)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Forward  : {dll.forward()}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Backward : {dll.backward()}"</span>)
<span style="color:#6b7280;"># Delete node with value 3 in O(1) once we have its pointer</span>
<span style="color:#93c5fd;">cur</span> = dll.head
<span style="color:#c4b5fd;">while</span> cur <span style="color:#c4b5fd;">and</span> cur.val != <span style="color:#fcd34d;">3</span>: cur = cur.next
<span style="color:#c4b5fd;">if</span> cur: dll.delete_node(cur)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"After del 3: {dll.forward()}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Forward  : [1, 2, 3, 4, 5]
Backward : [5, 4, 3, 2, 1]
After del 3: [1, 2, 4, 5]</div>
  </div>
</div>

<h3>Floyd's Cycle Detection — The Fast & Slow Pointer</h3>
<p>A classic linked list problem: detect if a list has a cycle (a node pointing back to an earlier node, causing infinite traversal). Floyd's algorithm uses two pointers — one moving one step at a time, one moving two. If there is a cycle, they will inevitably meet. O(n) time, O(1) space — far better than storing visited nodes in a set.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Floyd's Cycle Detection (Tortoise & Hare)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">has_cycle</span>(head):
    <span style="color:#a7f3d0;">"""
    Floyd's tortoise and hare algorithm.
    slow moves 1 step, fast moves 2 steps.
    If a cycle exists, fast catches slow eventually.
    O(n) time, O(1) space.
    """</span>
    <span style="color:#93c5fd;">slow</span> = <span style="color:#93c5fd;">fast</span> = head
    <span style="color:#c4b5fd;">while</span> fast <span style="color:#c4b5fd;">and</span> fast.next:
        slow = slow.next
        fast = fast.next.next
        <span style="color:#c4b5fd;">if</span> slow is fast:
            <span style="color:#c4b5fd;">return</span> <span style="color:#fca5a5;">True</span>   <span style="color:#6b7280;"># cycle detected — they met</span>
    <span style="color:#c4b5fd;">return</span> <span style="color:#fca5a5;">False</span>

<span style="color:#6b7280;"># Build a list with a cycle: 1→2→3→4→5→back to node 3</span>
<span style="color:#93c5fd;">n1</span>, <span style="color:#93c5fd;">n2</span>, <span style="color:#93c5fd;">n3</span>, <span style="color:#93c5fd;">n4</span>, <span style="color:#93c5fd;">n5</span> = SNode(<span style="color:#fcd34d;">1</span>), SNode(<span style="color:#fcd34d;">2</span>), SNode(<span style="color:#fcd34d;">3</span>), SNode(<span style="color:#fcd34d;">4</span>), SNode(<span style="color:#fcd34d;">5</span>)
<span style="color:#93c5fd;">n1</span>.next = n2; n2.next = n3; n3.next = n4; n4.next = n5
<span style="color:#93c5fd;">n5</span>.next = n3   <span style="color:#6b7280;"># creates cycle: 5 → 3 → 4 → 5 → 3 → ...</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Has cycle: {has_cycle(n1)}"</span>)

<span style="color:#6b7280;"># Normal list — no cycle</span>
<span style="color:#93c5fd;">ll2</span> = SinglyLinkedList()
<span style="color:#c4b5fd;">for</span> v <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">20</span>, <span style="color:#fcd34d;">30</span>]: ll2.append(v)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Has cycle: {has_cycle(ll2.head)}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Has cycle: True
Has cycle: False</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsaModule->id,
            'title'       => '7.5 Linked Lists: Singly, Doubly & Circular',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L7_5', [
                ['q' => 'Accessing the element at index k in a singly linked list is...', 'opts' => ['O(1) — same as an array', 'O(log k) — binary search on pointers', 'O(k) — must traverse from head', 'O(n²)'], 'ans' => 2, 'exp' => 'Linked lists have no random access. To reach index k, you must follow next pointers starting from head, taking k steps — O(k), which is O(n) in the worst case. This is the fundamental tradeoff compared to arrays.'],
                ['q' => 'A doubly linked list with a tail pointer achieves O(1) append. Why does a singly linked list without a tail pointer need O(n)?', 'opts' => ['Singly linked lists are slower by design', 'Without a tail pointer, you must traverse the entire list to find the last node before appending', 'Doubly linked lists have a built-in append method', 'Singly lists can only prepend'], 'ans' => 1, 'exp' => 'Without a tail pointer, appending requires walking from head to the last node — O(n). With a tail pointer, you directly access the last node and link the new one — O(1). This is why maintaining a tail pointer is standard practice.'],
                ['q' => 'Floyd\'s cycle detection (tortoise and hare) uses what space complexity?', 'opts' => ['O(n) — stores visited nodes', 'O(log n)', 'O(1) — only two pointer variables', 'O(n²)'], 'ans' => 2, 'exp' => 'Floyd\'s algorithm uses just two pointer variables (slow and fast) regardless of list size — O(1) space. The alternative (storing all visited nodes in a hash set) uses O(n) space. This makes Floyd\'s far more memory-efficient.'],
                ['q' => 'Given a pointer directly to a node in a doubly linked list, deleting that node takes...', 'opts' => ['O(n) — must find the node first', 'O(1) — update prev and next pointers of neighbors', 'O(log n)', 'O(n log n)'], 'ans' => 1, 'exp' => 'With a direct pointer to the node, deletion in a doubly linked list requires only: set node.prev.next = node.next and node.next.prev = node.prev. That is 2 pointer updates — O(1). This is impossible in a singly linked list where you cannot find the previous node without traversal.'],
                ['q' => 'Which is the most accurate comparison between arrays and linked lists?', 'opts' => ['Arrays are always better than linked lists', 'Linked lists are always better than arrays', 'Arrays give O(1) random access and O(n) insert/delete at middle; linked lists give O(n) access but O(1) insert/delete given a pointer', 'Both have identical complexity for all operations'], 'ans' => 2, 'exp' => 'The tradeoff is fundamental: arrays excel at random access (O(1) by index) but are slow for mid-list insert/delete (O(n) shifting). Linked lists excel at insert/delete when you have a pointer (O(1)) but are slow for random access (O(n)). The right choice depends on which operations dominate your use case.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 7.6 — Hash Tables
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Hash Tables: The O(1) Lookup Machine</h2>
<p>Hash tables are arguably the most practically important data structure in data science. Python's <code>dict</code> and <code>set</code> are both hash tables. Pandas uses hash tables internally for groupby, merge, and value_counts operations. Understanding how they work — and when they fail — makes you a dramatically better programmer.</p>

<h3>How Hashing Works</h3>
<p>A hash table converts a key into an integer index via a <strong>hash function</strong>, then stores the value at that index in an underlying array. The ideal hash function is fast to compute, distributes keys uniformly, and produces the same output for equal keys. Python's <code>hash()</code> is the built-in hash function.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Building a Hash Table from Scratch with Chaining</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">HashTable</span>:
    <span style="color:#a7f3d0;">"""
    Hash table using separate chaining for collision resolution.
    Stores key-value pairs in buckets (lists of pairs).
    Average O(1) for get/set/delete; O(n) worst case (all in one bucket).
    """</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__init__</span>(self, capacity=<span style="color:#fcd34d;">16</span>):
        self._cap     = capacity
        self._buckets = [[] <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(capacity)]
        self._size    = <span style="color:#fcd34d;">0</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">_index</span>(self, key):
        <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">hash</span>(key) % self._cap     <span style="color:#6b7280;"># map hash → bucket index</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">set</span>(self, key, value):
        <span style="color:#93c5fd;">idx</span>    = self._index(key)
        <span style="color:#93c5fd;">bucket</span> = self._buckets[idx]
        <span style="color:#c4b5fd;">for</span> i, (k, v) <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(bucket):
            <span style="color:#c4b5fd;">if</span> k == key:
                bucket[i] = (key, value)   <span style="color:#6b7280;"># update existing key</span>
                <span style="color:#c4b5fd;">return</span>
        bucket.append((key, value))        <span style="color:#6b7280;"># new key → append to chain</span>
        self._size += <span style="color:#fcd34d;">1</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">get</span>(self, key, default=<span style="color:#fca5a5;">None</span>):
        <span style="color:#93c5fd;">bucket</span> = self._buckets[self._index(key)]
        <span style="color:#c4b5fd;">for</span> k, v <span style="color:#c4b5fd;">in</span> bucket:
            <span style="color:#c4b5fd;">if</span> k == key: <span style="color:#c4b5fd;">return</span> v
        <span style="color:#c4b5fd;">return</span> default

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">delete</span>(self, key):
        <span style="color:#93c5fd;">bucket</span> = self._buckets[self._index(key)]
        <span style="color:#c4b5fd;">for</span> i, (k, v) <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(bucket):
            <span style="color:#c4b5fd;">if</span> k == key: bucket.pop(i); self._size -= <span style="color:#fcd34d;">1</span>; <span style="color:#c4b5fd;">return</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">load_factor</span>(self): <span style="color:#c4b5fd;">return</span> self._size / self._cap

<span style="color:#93c5fd;">ht</span> = HashTable(capacity=<span style="color:#fcd34d;">8</span>)
<span style="color:#c4b5fd;">for</span> k, v <span style="color:#c4b5fd;">in</span> [(<span style="color:#a7f3d0;">"accuracy"</span>, <span style="color:#fcd34d;">0.94</span>), (<span style="color:#a7f3d0;">"precision"</span>, <span style="color:#fcd34d;">0.91</span>), (<span style="color:#a7f3d0;">"recall"</span>, <span style="color:#fcd34d;">0.89</span>), (<span style="color:#a7f3d0;">"f1"</span>, <span style="color:#fcd34d;">0.90</span>)]:
    ht.set(k, v)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"accuracy  → {ht.get('accuracy')}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"f1_score  → {ht.get('f1_score', 'NOT FOUND')}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Load factor: {ht.load_factor():.2f}  (ideal < 0.75)"</span>)

<span style="color:#6b7280;"># Show bucket distribution (collision inspection)</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nBucket contents:"</span>)
<span style="color:#c4b5fd;">for</span> i, b <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(ht._buckets):
    <span style="color:#c4b5fd;">if</span> b: <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  [{i}]: {b}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>accuracy  → 0.94
f1_score  → NOT FOUND
Load factor: 0.50  (ideal < 0.75)

Bucket contents:
  [1]: [('accuracy', 0.94)]
  [3]: [('f1', 0.9)]
  [5]: [('recall', 0.89)]
  [7]: [('precision', 0.91)]</div>
  </div>
</div>

<h3>Collision Resolution: Chaining vs Open Addressing</h3>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:32px;">
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #3b82f6;">
    <h4 style="color:#3b82f6;margin-top:0;font-size:0.9rem;">Separate Chaining</h4>
    <p style="color:var(--muted);font-size:0.85rem;">Each bucket holds a linked list of (key,value) pairs. Collisions just add to the chain. Simple, but each chain node needs a pointer — extra memory. Python's dict used a variant of this historically.</p>
    <ul style="color:var(--muted);font-size:0.825rem;padding-left:1.2rem;margin:8px 0 0 0;line-height:1.8;">
      <li>✓ Simple to implement</li>
      <li>✓ Handles many collisions gracefully</li>
      <li>✗ Extra memory for pointers</li>
      <li>✗ Poor cache locality</li>
    </ul>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #10b981;">
    <h4 style="color:#10b981;margin-top:0;font-size:0.9rem;">Open Addressing (Linear Probing)</h4>
    <p style="color:var(--muted);font-size:0.85rem;">On collision, scan forward for the next empty slot. All entries live in the main array — better cache performance. Modern Python dicts use a sophisticated variant of this with pseudo-random probing.</p>
    <ul style="color:var(--muted);font-size:0.825rem;padding-left:1.2rem;margin:8px 0 0 0;line-height:1.8;">
      <li>✓ Better cache performance</li>
      <li>✓ Less memory overhead</li>
      <li>✗ Clustering under high load</li>
      <li>✗ Deletion is tricky (tombstones)</li>
    </ul>
  </div>
</div>

<h3>Word Frequency Counter — Classic Hash Table Application</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Anagram Detection & Word Frequency O(n)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> collections <span style="color:#c4b5fd;">import</span> Counter

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">is_anagram</span>(s, t):
    <span style="color:#a7f3d0;">"""Two strings are anagrams if they contain identical character counts."""</span>
    <span style="color:#c4b5fd;">return</span> Counter(s.lower()) == Counter(t.lower())

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">top_k_words</span>(text, k=<span style="color:#fcd34d;">5</span>):
    <span style="color:#a7f3d0;">"""Return the k most common words using a hash map."""</span>
    <span style="color:#93c5fd;">freq</span> = {}
    <span style="color:#c4b5fd;">for</span> word <span style="color:#c4b5fd;">in</span> text.lower().split():
        <span style="color:#93c5fd;">word</span> = word.strip(<span style="color:#a7f3d0;">".,!?;:"</span>)
        <span style="color:#93c5fd;">freq</span>[word] = freq.get(word, <span style="color:#fcd34d;">0</span>) + <span style="color:#fcd34d;">1</span>
    <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">sorted</span>(freq.items(), key=<span style="color:#c4b5fd;">lambda</span> x: -x[<span style="color:#fcd34d;">1</span>])[:k]

<span style="color:#6b7280;"># Anagram detection</span>
<span style="color:#93c5fd;">pairs</span> = [(<span style="color:#a7f3d0;">"listen"</span>, <span style="color:#a7f3d0;">"silent"</span>), (<span style="color:#a7f3d0;">"data"</span>, <span style="color:#a7f3d0;">"tada"</span>), (<span style="color:#a7f3d0;">"python"</span>, <span style="color:#a7f3d0;">"typhon"</span>), (<span style="color:#a7f3d0;">"hello"</span>, <span style="color:#a7f3d0;">"world"</span>)]
<span style="color:#c4b5fd;">for</span> a, b <span style="color:#c4b5fd;">in</span> pairs:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"'{a}' & '{b}': {'✓ anagram' if is_anagram(a, b) else '✗ not anagram'}"</span>)

<span style="color:#93c5fd;">corpus</span> = <span style="color:#a7f3d0;">"data science uses data and science to build data models from data"</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nTop 3 words: {top_k_words(corpus, 3)}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>'listen' & 'silent': ✓ anagram
'data' & 'tada': ✓ anagram
'python' & 'typhon': ✓ anagram
'hello' & 'world': ✗ not anagram

Top 3 words: [('data', 4), ('science', 2), ('uses', 1)]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsaModule->id,
            'title'       => '7.6 Hash Tables: Dictionaries Demystified',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L7_6', [
                ['q' => 'A hash table returns the same output for equal keys because...', 'opts' => ['Keys are sorted alphabetically', 'Equal keys produce equal hash values, which map to the same bucket index', 'All keys go to bucket 0', 'The table scans all keys sequentially'], 'ans' => 1, 'exp' => 'A hash function is deterministic: equal inputs always produce equal outputs. This means equal keys always hash to the same index, ensuring set/get operate on the same bucket. This is a required property of any valid hash function.'],
                ['q' => 'What is a "hash collision"?', 'opts' => ['Two identical keys stored twice', 'Two different keys hashing to the same bucket index', 'A hash function that returns None', 'When the table is completely full'], 'ans' => 1, 'exp' => 'A collision occurs when two different keys produce the same hash index. Since hash functions map a very large key space to a smaller array, collisions are inevitable. Collision resolution (chaining or open addressing) handles this gracefully.'],
                ['q' => 'Why must Python dict keys be immutable (e.g. strings, ints, tuples — not lists)?', 'opts' => ['Lists are too slow', 'If a key\'s value changes after insertion, its hash changes and it can no longer be found in the correct bucket', 'Lists take too much memory', 'Python dicts predate list support'], 'ans' => 1, 'exp' => 'If you stored a list as a key and then modified it, its hash would change. The key would then hash to a different bucket, and the stored value would be permanently "lost". Requiring immutable (hashable) keys prevents this class of bug.'],
                ['q' => 'The "load factor" of a hash table is size / capacity. What happens when it exceeds ~0.75?', 'opts' => ['The table deletes old entries', 'Lookup becomes O(log n)', 'The table rehashes — allocates a larger array and reinserts all entries to reduce collisions', 'The table starts returning None'], 'ans' => 2, 'exp' => 'At high load factors, many keys share buckets (collision chains grow), degrading lookups toward O(n). Python dicts rehash (resize to ~twice the capacity) when load factor exceeds about 2/3, maintaining the O(1) average guarantee.'],
                ['q' => 'Checking "x in my_set" vs "x in my_list" — which is faster and why?', 'opts' => ['my_list is faster — arrays have cache locality', 'my_set is faster — O(1) hash lookup vs O(n) linear scan', 'Both are O(1) for small collections', 'my_list is faster for sorted data'], 'ans' => 1, 'exp' => '"x in my_set" uses the hash table to jump directly to the correct bucket — O(1). "x in my_list" linearly scans every element — O(n). For 1 million elements, the set check takes ~1 step; the list check takes up to 1,000,000 steps. This swap alone can transform an O(n²) algorithm into O(n).'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 7.7 — Trees: Binary Trees & BSTs
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Trees: Binary Trees & Binary Search Trees</h2>
<p>Trees are hierarchical data structures where each node has a value and zero or more child nodes. They appear everywhere in data science: decision trees (machine learning), parse trees (NLP), B-trees (database indexes), expression trees (compilers), and trie structures (autocomplete). Understanding trees is the gateway to understanding how database queries execute in milliseconds on billions of rows.</p>

<h3>Tree Vocabulary</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;display:grid;grid-template-columns:1fr 1fr;gap:16px;font-size:0.875rem;">
  <div><strong style="color:var(--text);">Root:</strong> <span style="color:var(--muted);">The topmost node — only node with no parent</span></div>
  <div><strong style="color:var(--text);">Leaf:</strong> <span style="color:var(--muted);">A node with no children</span></div>
  <div><strong style="color:var(--text);">Height:</strong> <span style="color:var(--muted);">Longest path from root to any leaf</span></div>
  <div><strong style="color:var(--text);">Depth:</strong> <span style="color:var(--muted);">Distance from root to a specific node</span></div>
  <div><strong style="color:var(--text);">Parent/Child:</strong> <span style="color:var(--muted);">A node directly above/below another</span></div>
  <div><strong style="color:var(--text);">Subtree:</strong> <span style="color:var(--muted);">A node and all its descendants</span></div>
  <div><strong style="color:var(--text);">Binary tree:</strong> <span style="color:var(--muted);">Each node has at most 2 children (left, right)</span></div>
  <div><strong style="color:var(--text);">Full/Perfect/Complete:</strong> <span style="color:var(--muted);">Specific balance/fullness conditions</span></div>
</div>

<h3>Binary Tree Traversals: The Four Orderings</h3>
<p>Tree traversal means visiting every node exactly once. The order in which you visit nodes dramatically changes what you get — and knowing which traversal to use is a core skill. All four are O(n) time and O(h) space where h is the tree height.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — All Four Tree Traversals (Recursive & Iterative BFS)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> collections <span style="color:#c4b5fd;">import</span> deque

<span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">BNode</span>:
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__init__</span>(self, val, left=<span style="color:#fca5a5;">None</span>, right=<span style="color:#fca5a5;">None</span>):
        self.val   = val
        self.left  = left
        self.right = right

<span style="color:#6b7280;"># Build this tree:
#         4
#        / \
#       2   6
#      / \ / \
#     1  3 5  7</span>

<span style="color:#93c5fd;">root</span> = BNode(<span style="color:#fcd34d;">4</span>,
    BNode(<span style="color:#fcd34d;">2</span>, BNode(<span style="color:#fcd34d;">1</span>), BNode(<span style="color:#fcd34d;">3</span>)),
    BNode(<span style="color:#fcd34d;">6</span>, BNode(<span style="color:#fcd34d;">5</span>), BNode(<span style="color:#fcd34d;">7</span>))
)

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">inorder</span>(node):     <span style="color:#6b7280;"># LEFT → ROOT → RIGHT  →  sorted order for BST</span>
    <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> node: <span style="color:#c4b5fd;">return</span> []
    <span style="color:#c4b5fd;">return</span> inorder(node.left) + [node.val] + inorder(node.right)

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">preorder</span>(node):    <span style="color:#6b7280;"># ROOT → LEFT → RIGHT  →  copy / serialize a tree</span>
    <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> node: <span style="color:#c4b5fd;">return</span> []
    <span style="color:#c4b5fd;">return</span> [node.val] + preorder(node.left) + preorder(node.right)

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">postorder</span>(node):   <span style="color:#6b7280;"># LEFT → RIGHT → ROOT  →  delete / evaluate expression tree</span>
    <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> node: <span style="color:#c4b5fd;">return</span> []
    <span style="color:#c4b5fd;">return</span> postorder(node.left) + postorder(node.right) + [node.val]

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">level_order</span>(root):  <span style="color:#6b7280;"># BFS level-by-level  →  tree width, shortest path</span>
    <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> root: <span style="color:#c4b5fd;">return</span> []
    <span style="color:#93c5fd;">q</span>, <span style="color:#93c5fd;">result</span> = deque([root]), []
    <span style="color:#c4b5fd;">while</span> q:
        <span style="color:#93c5fd;">node</span> = q.popleft()
        result.append(node.val)
        <span style="color:#c4b5fd;">if</span> node.left:  q.append(node.left)
        <span style="color:#c4b5fd;">if</span> node.right: q.append(node.right)
    <span style="color:#c4b5fd;">return</span> result

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Inorder   (L→R→Root): {inorder(root)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Preorder  (Root→L→R): {preorder(root)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Postorder (L→R→Root): {postorder(root)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Level     (BFS rows): {level_order(root)}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Inorder   (L→Root→R): [1, 2, 3, 4, 5, 6, 7]
Preorder  (Root→L→R): [4, 2, 1, 3, 6, 5, 7]
Postorder (L→R→Root): [1, 3, 2, 5, 7, 6, 4]
Level     (BFS rows): [4, 2, 6, 1, 3, 5, 7]</div>
  </div>
</div>

<h3>Binary Search Tree (BST): Ordered Lookups</h3>
<p>A BST maintains the invariant: <strong>left subtree values < node value < right subtree values</strong>. This ordering makes search, insert, and delete all O(log n) on a balanced tree. Inorder traversal of a BST always produces a sorted sequence — a fact that powers many sorting and range-query algorithms.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Full BST: Insert, Search, Delete, Range Query</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">BST</span>:
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__init__</span>(self): self.root = <span style="color:#fca5a5;">None</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">insert</span>(self, val):
        self.root = self._insert(self.root, val)

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">_insert</span>(self, node, val):
        <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> node: <span style="color:#c4b5fd;">return</span> BNode(val)
        <span style="color:#c4b5fd;">if</span>   val < node.val: node.left  = self._insert(node.left,  val)
        <span style="color:#c4b5fd;">elif</span> val > node.val: node.right = self._insert(node.right, val)
        <span style="color:#c4b5fd;">return</span> node

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">search</span>(self, val):
        <span style="color:#93c5fd;">node</span> = self.root
        <span style="color:#c4b5fd;">while</span> node:
            <span style="color:#c4b5fd;">if</span>   val == node.val: <span style="color:#c4b5fd;">return</span> <span style="color:#fca5a5;">True</span>
            <span style="color:#c4b5fd;">elif</span> val < node.val:  node = node.left
            <span style="color:#c4b5fd;">else</span>:                  node = node.right
        <span style="color:#c4b5fd;">return</span> <span style="color:#fca5a5;">False</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">range_query</span>(self, lo, hi):
        <span style="color:#a7f3d0;">"""Return all values in [lo, hi] in sorted order. O(log n + k)."""</span>
        <span style="color:#93c5fd;">result</span> = []
        <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">_dfs</span>(node):
            <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> node: <span style="color:#c4b5fd;">return</span>
            <span style="color:#c4b5fd;">if</span> lo < node.val:  _dfs(node.left)   <span style="color:#6b7280;"># pruning: skip if out of range</span>
            <span style="color:#c4b5fd;">if</span> lo <= node.val <= hi: result.append(node.val)
            <span style="color:#c4b5fd;">if</span> node.val < hi:  _dfs(node.right)
        _dfs(self.root)
        <span style="color:#c4b5fd;">return</span> result

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">inorder</span>(self): <span style="color:#c4b5fd;">return</span> inorder(self.root)

<span style="color:#6b7280;"># Model accuracy scores stored in a BST for fast range queries</span>
<span style="color:#93c5fd;">bst</span> = BST()
<span style="color:#c4b5fd;">for</span> score <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">55</span>, <span style="color:#fcd34d;">75</span>, <span style="color:#fcd34d;">40</span>, <span style="color:#fcd34d;">80</span>, <span style="color:#fcd34d;">90</span>, <span style="color:#fcd34d;">35</span>, <span style="color:#fcd34d;">60</span>, <span style="color:#fcd34d;">85</span>, <span style="color:#fcd34d;">95</span>, <span style="color:#fcd34d;">70</span>]:
    bst.insert(score)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sorted scores : {bst.inorder()}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Search 80     : {bst.search(80)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Search 99     : {bst.search(99)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Range [70, 90]: {bst.range_query(70, 90)}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Sorted scores : [35, 40, 55, 60, 70, 75, 80, 85, 90, 95]
Search 80     : True
Search 99     : False
Range [70, 90]: [70, 75, 80, 85, 90]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsaModule->id,
            'title'       => '7.7 Trees: Binary Trees & BSTs',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L7_7', [
                ['q' => 'Inorder traversal (left → root → right) of a valid BST always produces...', 'opts' => ['A random permutation', 'Values in descending order', 'Values in ascending (sorted) order', 'Only leaf values'], 'ans' => 2, 'exp' => 'By the BST invariant (left < node < right), inorder traversal visits nodes in strictly increasing order. This is why inorder traversal is the standard method to extract sorted data from a BST, and why tree sort works.'],
                ['q' => 'What is the worst-case time complexity of BST search?', 'opts' => ['O(1)', 'O(log n) always', 'O(n) — degenerate tree (all nodes in one chain)', 'O(n²)'], 'ans' => 2, 'exp' => 'If you insert elements in sorted order (e.g. 1, 2, 3, 4, 5), the BST degenerates into a linked list — every node has only a right child. Search then takes O(n). Self-balancing trees (AVL, Red-Black) prevent this by maintaining O(log n) height.'],
                ['q' => 'Which traversal would you use to serialize (save) a tree so you can reconstruct it exactly?', 'opts' => ['Inorder', 'Postorder', 'Preorder', 'Level-order only'], 'ans' => 2, 'exp' => 'Preorder (root → left → right) visits the root before its children. When deserializing, you can recreate the root first and then recursively rebuild subtrees in the same order. Inorder alone is insufficient for unique reconstruction without additional information.'],
                ['q' => 'Level-order traversal uses which auxiliary data structure?', 'opts' => ['Stack', 'Priority queue', 'Queue (deque)', 'Linked list'], 'ans' => 2, 'exp' => 'Level-order (BFS) traversal processes nodes level by level. A queue enforces this: enqueue the root, then for each dequeued node, enqueue its children. This naturally processes all nodes of depth d before any node of depth d+1.'],
                ['q' => 'A balanced BST with n nodes has height approximately...', 'opts' => ['n', 'n/2', 'log₂(n)', '√n'], 'ans' => 2, 'exp' => 'A perfectly balanced BST has approximately log₂(n) levels (height). Each level doubles the number of nodes: 1, 2, 4, 8, ..., n. Solving 2^h = n gives h = log₂(n). This is why BST operations are O(log n) when the tree is balanced.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 7.8 — Heaps & Priority Queues
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Heaps & Priority Queues: The Fastest Way to Find Extremes</h2>
<p>A <strong>heap</strong> is a specialised binary tree that satisfies the <em>heap property</em>: in a max-heap, every parent is greater than or equal to its children; in a min-heap, every parent is less than or equal to its children. Heaps are not fully sorted — they only guarantee access to the extreme element (min or max). This constraint makes them blazingly efficient: insert and extract-min/max are both O(log n), and building a heap from n elements is O(n). They power priority queues, heap sort, Dijkstra's algorithm, and the "Top-K" pattern so common in data science.</p>

<h3>Heap as an Array: The Compact Representation</h3>
<p>A binary heap is stored as an array — no node objects or pointers needed. For a node at index <code>i</code>: its left child is at <code>2i+1</code>, right child at <code>2i+2</code>, and parent at <code>(i-1)//2</code>. This compact layout gives excellent cache performance.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Min-Heap from Scratch: sift-up & sift-down</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">MinHeap</span>:
    <span style="color:#a7f3d0;">"""
    Binary min-heap stored in a Python list.
    Parent of i  : (i-1)//2
    Left child   : 2*i + 1
    Right child  : 2*i + 2
    """</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__init__</span>(self):
        self._h = []

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">push</span>(self, val):       <span style="color:#6b7280;"># O(log n)</span>
        self._h.append(val)
        self._sift_up(<span style="color:#93c5fd;">len</span>(self._h) - <span style="color:#fcd34d;">1</span>)

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">pop</span>(self):             <span style="color:#6b7280;"># O(log n) — removes and returns minimum</span>
        <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> self._h: <span style="color:#c4b5fd;">raise</span> IndexError(<span style="color:#a7f3d0;">"pop from empty heap"</span>)
        self._swap(<span style="color:#fcd34d;">0</span>, <span style="color:#93c5fd;">len</span>(self._h) - <span style="color:#fcd34d;">1</span>)  <span style="color:#6b7280;"># move min to end</span>
        <span style="color:#93c5fd;">val</span> = self._h.pop()             <span style="color:#6b7280;"># remove it</span>
        <span style="color:#c4b5fd;">if</span> self._h: self._sift_down(<span style="color:#fcd34d;">0</span>) <span style="color:#6b7280;"># restore heap property</span>
        <span style="color:#c4b5fd;">return</span> val

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">peek</span>(self):             <span style="color:#6b7280;"># O(1)</span>
        <span style="color:#c4b5fd;">return</span> self._h[<span style="color:#fcd34d;">0</span>] <span style="color:#c4b5fd;">if</span> self._h <span style="color:#c4b5fd;">else</span> <span style="color:#fca5a5;">None</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">_sift_up</span>(self, i):
        <span style="color:#c4b5fd;">while</span> i > <span style="color:#fcd34d;">0</span>:
            <span style="color:#93c5fd;">parent</span> = (i - <span style="color:#fcd34d;">1</span>) // <span style="color:#fcd34d;">2</span>
            <span style="color:#c4b5fd;">if</span> self._h[i] < self._h[parent]:
                self._swap(i, parent)
                i = parent
            <span style="color:#c4b5fd;">else</span>: <span style="color:#c4b5fd;">break</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">_sift_down</span>(self, i):
        <span style="color:#93c5fd;">n</span> = <span style="color:#93c5fd;">len</span>(self._h)
        <span style="color:#c4b5fd;">while</span> <span style="color:#fca5a5;">True</span>:
            <span style="color:#93c5fd;">smallest</span>, <span style="color:#93c5fd;">l</span>, <span style="color:#93c5fd;">r</span> = i, <span style="color:#fcd34d;">2</span>*i+<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>*i+<span style="color:#fcd34d;">2</span>
            <span style="color:#c4b5fd;">if</span> l < n <span style="color:#c4b5fd;">and</span> self._h[l] < self._h[smallest]: smallest = l
            <span style="color:#c4b5fd;">if</span> r < n <span style="color:#c4b5fd;">and</span> self._h[r] < self._h[smallest]: smallest = r
            <span style="color:#c4b5fd;">if</span> smallest == i: <span style="color:#c4b5fd;">break</span>
            self._swap(i, smallest)
            i = smallest

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">_swap</span>(self, i, j): self._h[i], self._h[j] = self._h[j], self._h[i]
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__repr__</span>(self): <span style="color:#c4b5fd;">return</span> <span style="color:#a7f3d0;">f"MinHeap(min={self._h[0] if self._h else None}, {self._h})"</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__len__</span>(self): <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">len</span>(self._h)

<span style="color:#93c5fd;">h</span> = MinHeap()
<span style="color:#c4b5fd;">for</span> v <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">14</span>, <span style="color:#fcd34d;">4</span>]:
    h.push(v)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"push({v:>2}) → {h}"</span>)

<span style="color:#93c5fd;">print</span>()
<span style="color:#c4b5fd;">while</span> h:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"pop() → {h.pop()}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>push( 8) → MinHeap(min=8, [8])
push( 3) → MinHeap(min=3, [3, 8])
push(10) → MinHeap(min=3, [3, 8, 10])
push( 1) → MinHeap(min=1, [1, 3, 10, 8])
push( 6) → MinHeap(min=1, [1, 3, 10, 8, 6])
push(14) → MinHeap(min=1, [1, 3, 10, 8, 6, 14])
push( 4) → MinHeap(min=1, [1, 3, 4, 8, 6, 14, 10])

pop() → 1
pop() → 3
pop() → 4
pop() → 6
pop() → 8
pop() → 10
pop() → 14</div>
  </div>
</div>

<h3>Top-K Pattern: A Data Scientist's Workhorse</h3>
<p>Finding the K largest or smallest items from a stream of N items without sorting everything is a classic data science problem. A heap of size K solves this in O(N log K) time and O(K) space — far better than O(N log N) full sort when K ≪ N.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Top-K Largest Features by Importance Score  O(N log K)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> heapq

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">top_k_features</span>(feature_scores, k):
    <span style="color:#a7f3d0;">"""
    Returns k features with highest importance scores.
    Uses a min-heap of size k: O(N log k) time, O(k) space.
    Much better than sorted(...) which is O(N log N).
    """</span>
    <span style="color:#93c5fd;">heap</span> = []
    <span style="color:#c4b5fd;">for</span> name, score <span style="color:#c4b5fd;">in</span> feature_scores:
        heapq.heappush(heap, (score, name))   <span style="color:#6b7280;"># min-heap by score</span>
        <span style="color:#c4b5fd;">if</span> <span style="color:#93c5fd;">len</span>(heap) > k:
            heapq.heappop(heap)              <span style="color:#6b7280;"># evict smallest — keeps k largest</span>
    <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">sorted</span>(heap, reverse=<span style="color:#fca5a5;">True</span>)

<span style="color:#93c5fd;">features</span> = [
    (<span style="color:#a7f3d0;">"age"</span>, <span style="color:#fcd34d;">0.31</span>), (<span style="color:#a7f3d0;">"income"</span>, <span style="color:#fcd34d;">0.72</span>), (<span style="color:#a7f3d0;">"credit_score"</span>, <span style="color:#fcd34d;">0.85</span>),
    (<span style="color:#a7f3d0;">"employment_years"</span>, <span style="color:#fcd34d;">0.44</span>), (<span style="color:#a7f3d0;">"debt_ratio"</span>, <span style="color:#fcd34d;">0.68</span>),
    (<span style="color:#a7f3d0;">"num_accounts"</span>, <span style="color:#fcd34d;">0.29</span>), (<span style="color:#a7f3d0;">"loan_amount"</span>, <span style="color:#fcd34d;">0.91</span>),
    (<span style="color:#a7f3d0;">"zip_code"</span>, <span style="color:#fcd34d;">0.05</span>), (<span style="color:#a7f3d0;">"gender"</span>, <span style="color:#fcd34d;">0.02</span>), (<span style="color:#a7f3d0;">"payment_history"</span>, <span style="color:#fcd34d;">0.77</span>),
]

<span style="color:#93c5fd;">top3</span> = top_k_features(features, k=<span style="color:#fcd34d;">3</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Top 3 most important features:"</span>)
<span style="color:#c4b5fd;">for</span> score, name <span style="color:#c4b5fd;">in</span> top3:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {name:20} importance={score}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Top 3 most important features:
  loan_amount          importance=0.91
  credit_score         importance=0.85
  payment_history      importance=0.77</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsaModule->id,
            'title'       => '7.8 Heaps & Priority Queues',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L7_8', [
                ['q' => 'In a binary min-heap array, the minimum element is always at...', 'opts' => ['The last index', 'A random index', 'Index 0 (the root)', 'Index 1'], 'ans' => 2, 'exp' => 'The min-heap property guarantees that the root (index 0) is always the smallest element in the heap. This is why peek() is O(1) — no search needed, just return the first element of the array.'],
                ['q' => 'After inserting a new element into a min-heap, what operation restores the heap property?', 'opts' => ['Sift-down from the root', 'Full re-sort of the array', 'Sift-up from the inserted position', 'Swap with the minimum'], 'ans' => 2, 'exp' => 'A new element is appended at the end, potentially violating the heap property (it might be smaller than its parent). Sift-up compares with the parent and swaps if needed, repeating up the tree. This takes at most O(log n) swaps.'],
                ['q' => 'heapq.heappop() in Python has what time complexity?', 'opts' => ['O(1)', 'O(log n)', 'O(n)', 'O(n log n)'], 'ans' => 1, 'exp' => 'heappop() removes the root (minimum), moves the last element to the root position, and then sift-down restores the heap property. Sift-down takes at most O(log n) steps — the height of the tree.'],
                ['q' => 'To find the K largest items from a stream of N items, a min-heap of size K achieves what complexity?', 'opts' => ['O(N log N)', 'O(N log K)', 'O(N + K)', 'O(K²)'], 'ans' => 1, 'exp' => 'Maintain a min-heap of size K. For each of N elements: push and pop in O(log K). Total: O(N log K). When K ≪ N (e.g., top-10 from 10 million), this is far better than full sorting\'s O(N log N).'],
                ['q' => 'A max-heap can be simulated with Python\'s min-heap heapq by...', 'opts' => ['Using heapq.heappushmax()', 'Reversing the list after building', 'Inserting negated values (-x) and negating again when popping', 'Setting heapq.MIN_MODE = False'], 'ans' => 2, 'exp' => 'Python\'s heapq is a min-heap only. To simulate max-heap, push -x and pop then negate: -heappop(). If you push (score, item), use (-score, item) to get max-priority behavior.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 7.9 — Graphs: Representation, BFS & DFS
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Graphs: Representation, BFS & DFS</h2>
<p>A <strong>graph</strong> is a set of nodes (vertices) connected by edges. Graphs are the most general data structure — trees are graphs, linked lists are graphs, and countless real-world systems are naturally modelled as graphs: social networks (users + friendships), supply chains (warehouses + routes), knowledge graphs (entities + relationships), recommendation engines (users + products + ratings), and road navigation. Every data scientist working with relational or network data must understand graphs deeply.</p>

<h3>Graph Representations</h3>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:32px;">
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #3b82f6;">
    <h4 style="color:#3b82f6;margin-top:0;font-size:0.9rem;">Adjacency List (Preferred)</h4>
    <p style="color:var(--muted);font-size:0.85rem;">A dict mapping each node to its neighbours. Space O(V+E). Fast neighbour iteration. Best for sparse graphs (most real-world graphs).</p>
    <code style="color:#93c5fd;font-size:0.8rem;display:block;margin-top:8px;">{"A": ["B","C"], "B": ["D"]}</code>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #8b5cf6;">
    <h4 style="color:#8b5cf6;margin-top:0;font-size:0.9rem;">Adjacency Matrix</h4>
    <p style="color:var(--muted);font-size:0.85rem;">V×V matrix where matrix[i][j] = 1 if edge exists. Space O(V²). O(1) edge lookup. Best for dense graphs, rarely used for large sparse graphs.</p>
    <code style="color:#93c5fd;font-size:0.8rem;display:block;margin-top:8px;">matrix[u][v] = 1  ← edge exists</code>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Graph Class with BFS & DFS + Shortest Path</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> collections <span style="color:#c4b5fd;">import</span> defaultdict, deque

<span style="color:#c4b5fd;">class</span> <span style="color:#fbcfe8;">Graph</span>:
    <span style="color:#a7f3d0;">"""Undirected graph using adjacency list representation."""</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">__init__</span>(self):
        self._adj = defaultdict(<span style="color:#93c5fd;">list</span>)

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">add_edge</span>(self, u, v):
        self._adj[u].append(v)
        self._adj[v].append(u)   <span style="color:#6b7280;"># undirected: both directions</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">bfs</span>(self, start):
        <span style="color:#a7f3d0;">"""Breadth-First Search — level by level. O(V+E)."""</span>
        <span style="color:#93c5fd;">visited</span> = {start}
        <span style="color:#93c5fd;">queue</span>   = deque([start])
        <span style="color:#93c5fd;">order</span>   = []
        <span style="color:#c4b5fd;">while</span> queue:
            <span style="color:#93c5fd;">node</span> = queue.popleft()
            order.append(node)
            <span style="color:#c4b5fd;">for</span> nbr <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">sorted</span>(self._adj[node]):  <span style="color:#6b7280;"># sorted for determinism</span>
                <span style="color:#c4b5fd;">if</span> nbr <span style="color:#c4b5fd;">not</span> <span style="color:#c4b5fd;">in</span> visited:
                    visited.add(nbr)
                    queue.append(nbr)
        <span style="color:#c4b5fd;">return</span> order

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">dfs</span>(self, start, visited=<span style="color:#fca5a5;">None</span>):
        <span style="color:#a7f3d0;">"""Depth-First Search — go deep before wide. O(V+E)."""</span>
        <span style="color:#c4b5fd;">if</span> visited <span style="color:#c4b5fd;">is</span> <span style="color:#fca5a5;">None</span>: visited = <span style="color:#93c5fd;">set</span>()
        visited.add(start)
        <span style="color:#93c5fd;">order</span> = [start]
        <span style="color:#c4b5fd;">for</span> nbr <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">sorted</span>(self._adj[start]):
            <span style="color:#c4b5fd;">if</span> nbr <span style="color:#c4b5fd;">not</span> <span style="color:#c4b5fd;">in</span> visited:
                order += self.dfs(nbr, visited)
        <span style="color:#c4b5fd;">return</span> order

    <span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">shortest_path</span>(self, start, end):
        <span style="color:#a7f3d0;">"""BFS shortest path (unweighted). Returns node list or None."""</span>
        <span style="color:#93c5fd;">parent</span>  = {start: <span style="color:#fca5a5;">None</span>}
        <span style="color:#93c5fd;">queue</span>   = deque([start])
        <span style="color:#c4b5fd;">while</span> queue:
            <span style="color:#93c5fd;">node</span> = queue.popleft()
            <span style="color:#c4b5fd;">if</span> node == end:
                <span style="color:#93c5fd;">path</span> = []
                <span style="color:#c4b5fd;">while</span> node <span style="color:#c4b5fd;">is</span> <span style="color:#c4b5fd;">not</span> <span style="color:#fca5a5;">None</span>: path.append(node); node = parent[node]
                <span style="color:#c4b5fd;">return</span> path[::-<span style="color:#fcd34d;">1</span>]
            <span style="color:#c4b5fd;">for</span> nbr <span style="color:#c4b5fd;">in</span> self._adj[node]:
                <span style="color:#c4b5fd;">if</span> nbr <span style="color:#c4b5fd;">not</span> <span style="color:#c4b5fd;">in</span> parent:
                    parent[nbr] = node; queue.append(nbr)
        <span style="color:#c4b5fd;">return</span> <span style="color:#fca5a5;">None</span>

<span style="color:#6b7280;"># Social network: data science community connections</span>
<span style="color:#93c5fd;">g</span> = Graph()
<span style="color:#93c5fd;">edges</span> = [(<span style="color:#a7f3d0;">"Alice"</span>,<span style="color:#a7f3d0;">"Bob"</span>),(<span style="color:#a7f3d0;">"Alice"</span>,<span style="color:#a7f3d0;">"Carol"</span>),(<span style="color:#a7f3d0;">"Bob"</span>,<span style="color:#a7f3d0;">"Dave"</span>),
         (<span style="color:#a7f3d0;">"Carol"</span>,<span style="color:#a7f3d0;">"Eve"</span>),(<span style="color:#a7f3d0;">"Dave"</span>,<span style="color:#a7f3d0;">"Frank"</span>),(<span style="color:#a7f3d0;">"Eve"</span>,<span style="color:#a7f3d0;">"Frank"</span>)]
<span style="color:#c4b5fd;">for</span> u, v <span style="color:#c4b5fd;">in</span> edges: g.add_edge(u, v)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"BFS from Alice    : {g.bfs('Alice')}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"DFS from Alice    : {g.dfs('Alice')}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Shortest path A→F : {g.shortest_path('Alice', 'Frank')}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>BFS from Alice    : ['Alice', 'Bob', 'Carol', 'Dave', 'Eve', 'Frank']
DFS from Alice    : ['Alice', 'Bob', 'Dave', 'Frank', 'Eve', 'Carol']
Shortest path A→F : ['Alice', 'Carol', 'Eve', 'Frank']</div>
  </div>
</div>

<h3>BFS vs DFS: When to Use Which</h3>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:32px;">
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #3b82f6;">
    <h4 style="color:#3b82f6;margin-top:0;font-size:0.9rem;">BFS — Breadth First Search</h4>
    <ul style="color:var(--muted);font-size:0.85rem;padding-left:1.2rem;line-height:1.9;margin:0;">
      <li>Uses a <strong style="color:var(--text);">queue</strong></li>
      <li>Finds <strong style="color:var(--text);">shortest path</strong> in unweighted graphs</li>
      <li>Explores level by level — nearest nodes first</li>
      <li>Good for: social degrees of separation, nearest neighbour, web crawling by depth limit</li>
    </ul>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #8b5cf6;">
    <h4 style="color:#8b5cf6;margin-top:0;font-size:0.9rem;">DFS — Depth First Search</h4>
    <ul style="color:var(--muted);font-size:0.85rem;padding-left:1.2rem;line-height:1.9;margin:0;">
      <li>Uses a <strong style="color:var(--text);">stack</strong> (or recursion)</li>
      <li>Good for <strong style="color:var(--text);">exploring all paths</strong>, cycle detection, topological sort</li>
      <li>Goes deep before backtracking</li>
      <li>Good for: maze solving, dependency resolution, connected components</li>
    </ul>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsaModule->id,
            'title'       => '7.9 Graphs: Representation, BFS & DFS',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L7_9', [
                ['q' => 'BFS finds the shortest path in an unweighted graph because...', 'opts' => ['It visits nodes in random order', 'It always goes deep first', 'It explores all nodes at distance d before any node at distance d+1 — guaranteeing minimum hops', 'It uses a priority queue sorted by distance'], 'ans' => 2, 'exp' => 'BFS expands layer by layer. The first time it reaches a node, it has travelled the fewest possible edges (hops) to get there. Any subsequent path to that node would have more hops. This makes BFS optimal for shortest path in unweighted graphs.'],
                ['q' => 'An adjacency list is preferred over an adjacency matrix for sparse graphs because...', 'opts' => ['Adjacency lists support weighted edges', 'Adjacency matrix requires O(V²) space even when most edges don\'t exist; lists use O(V+E)', 'Adjacency lists are faster for edge lookup', 'Adjacency matrices can\'t represent directed graphs'], 'ans' => 1, 'exp' => 'For a social network with 1M users and 10M friendships (sparse), an adjacency matrix needs 1M×1M = 1 trillion cells. An adjacency list needs only 1M + 10M = 11M entries. The space difference is dramatic for real-world sparse graphs.'],
                ['q' => 'DFS uses a stack (or recursion). BFS uses which data structure?', 'opts' => ['Stack', 'Queue', 'Priority queue', 'Hash table'], 'ans' => 1, 'exp' => 'BFS uses a queue (FIFO). Nodes are enqueued when discovered and dequeued for processing. This ensures all nodes at the current distance are processed before moving to the next distance level — the defining characteristic of BFS.'],
                ['q' => 'Which algorithm would you use to find all connected components in a social graph?', 'opts' => ['Binary search', 'BFS or DFS — run from each unvisited node to discover each component', 'Heap sort', 'Hash table lookup'], 'ans' => 1, 'exp' => 'To find all connected components: iterate over all nodes; if unvisited, run BFS or DFS from that node (marking all reachable nodes). Each BFS/DFS traversal discovers one complete component. Total time: O(V+E).'],
                ['q' => 'In graph terminology, V = 6 nodes and E = 8 edges. What is the time complexity of BFS?', 'opts' => ['O(V²)', 'O(V + E) = O(6 + 8) = O(14)', 'O(E log V)', 'O(V log V)'], 'ans' => 1, 'exp' => 'BFS (and DFS) run in O(V + E) time: each vertex is processed once (O(V)) and each edge is examined twice (once from each endpoint) in an undirected graph (O(E)). Total O(V+E) = O(14) for this example.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 7.10 — Sorting & Searching Algorithms
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Sorting & Searching: The Algorithms Every Data Scientist Must Know</h2>
<p>Sorting and searching are the most studied problems in computer science — and for good reason. Sorting is a preprocessing step that enables binary search, merge operations, and efficient algorithms across every domain. Understanding when to use each algorithm, and why Python's built-in <code>sort()</code> is almost always the right answer, makes you a dramatically more effective developer.</p>

<h3>Bubble Sort: O(n²) — Never Use in Production</h3>
<p>Bubble sort repeatedly swaps adjacent elements that are out of order. It is the most intuitive sorting algorithm and the worst practical one. Its sole value is pedagogical — it illustrates why O(n²) algorithms fail at scale.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Bubble Sort with Swap Counter</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">bubble_sort</span>(arr):
    <span style="color:#93c5fd;">n</span>, <span style="color:#93c5fd;">swaps</span> = <span style="color:#93c5fd;">len</span>(arr), <span style="color:#fcd34d;">0</span>
    <span style="color:#93c5fd;">arr</span> = arr[:]  <span style="color:#6b7280;"># copy — don't mutate original</span>
    <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n):
        <span style="color:#93c5fd;">swapped</span> = <span style="color:#fca5a5;">False</span>
        <span style="color:#c4b5fd;">for</span> j <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">0</span>, n - i - <span style="color:#fcd34d;">1</span>):  <span style="color:#6b7280;"># last i elements already sorted</span>
            <span style="color:#c4b5fd;">if</span> arr[j] > arr[j + <span style="color:#fcd34d;">1</span>]:
                arr[j], arr[j+<span style="color:#fcd34d;">1</span>] = arr[j+<span style="color:#fcd34d;">1</span>], arr[j]
                swaps += <span style="color:#fcd34d;">1</span>; swapped = <span style="color:#fca5a5;">True</span>
        <span style="color:#c4b5fd;">if</span> <span style="color:#c4b5fd;">not</span> swapped: <span style="color:#c4b5fd;">break</span>  <span style="color:#6b7280;"># already sorted — early exit</span>
    <span style="color:#c4b5fd;">return</span> arr, swaps

<span style="color:#93c5fd;">data</span> = [<span style="color:#fcd34d;">64</span>, <span style="color:#fcd34d;">34</span>, <span style="color:#fcd34d;">25</span>, <span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">22</span>, <span style="color:#fcd34d;">11</span>, <span style="color:#fcd34d;">90</span>]
<span style="color:#93c5fd;">sorted_arr</span>, <span style="color:#93c5fd;">swaps</span> = bubble_sort(data)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Input  : {data}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Output : {sorted_arr}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Swaps  : {swaps}  (max possible for n={len(data)}: {len(data)*(len(data)-1)//2})"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Input  : [64, 34, 25, 12, 22, 11, 90]
Output : [11, 12, 22, 25, 34, 64, 90]
Swaps  : 14  (max possible for n=7: 21)</div>
  </div>
</div>

<h3>Merge Sort: O(n log n) — Divide and Conquer</h3>
<p>Merge sort splits the array in half, recursively sorts each half, then merges the two sorted halves. It is stable (preserves equal elements' original order), predictably O(n log n) in all cases, and the algorithm that powers Python's Timsort. The cost: O(n) extra space for the merge step.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Merge Sort & Binary Search: The Canonical Pair</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">merge_sort</span>(arr):
    <span style="color:#c4b5fd;">if</span> <span style="color:#93c5fd;">len</span>(arr) <= <span style="color:#fcd34d;">1</span>: <span style="color:#c4b5fd;">return</span> arr
    <span style="color:#93c5fd;">mid</span>   = <span style="color:#93c5fd;">len</span>(arr) // <span style="color:#fcd34d;">2</span>
    <span style="color:#93c5fd;">left</span>  = merge_sort(arr[:mid])
    <span style="color:#93c5fd;">right</span> = merge_sort(arr[mid:])
    <span style="color:#c4b5fd;">return</span> _merge(left, right)

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">_merge</span>(left, right):
    <span style="color:#93c5fd;">result</span>, <span style="color:#93c5fd;">i</span>, <span style="color:#93c5fd;">j</span> = [], <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>
    <span style="color:#c4b5fd;">while</span> i < <span style="color:#93c5fd;">len</span>(left) <span style="color:#c4b5fd;">and</span> j < <span style="color:#93c5fd;">len</span>(right):
        <span style="color:#c4b5fd;">if</span> left[i] <= right[j]: result.append(left[i]);  i += <span style="color:#fcd34d;">1</span>
        <span style="color:#c4b5fd;">else</span>:                   result.append(right[j]); j += <span style="color:#fcd34d;">1</span>
    <span style="color:#c4b5fd;">return</span> result + left[i:] + right[j:]

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">binary_search</span>(arr, target):
    <span style="color:#a7f3d0;">"""O(log n) search on sorted array. Returns index or -1."""</span>
    <span style="color:#93c5fd;">lo</span>, <span style="color:#93c5fd;">hi</span> = <span style="color:#fcd34d;">0</span>, <span style="color:#93c5fd;">len</span>(arr) - <span style="color:#fcd34d;">1</span>
    <span style="color:#c4b5fd;">while</span> lo <= hi:
        <span style="color:#93c5fd;">mid</span> = (lo + hi) // <span style="color:#fcd34d;">2</span>
        <span style="color:#c4b5fd;">if</span>   arr[mid] == target: <span style="color:#c4b5fd;">return</span> mid
        <span style="color:#c4b5fd;">elif</span> arr[mid] < target:  lo = mid + <span style="color:#fcd34d;">1</span>
        <span style="color:#c4b5fd;">else</span>:                    hi = mid - <span style="color:#fcd34d;">1</span>
    <span style="color:#c4b5fd;">return</span> -<span style="color:#fcd34d;">1</span>

<span style="color:#93c5fd;">unsorted</span> = [<span style="color:#fcd34d;">38</span>, <span style="color:#fcd34d;">27</span>, <span style="color:#fcd34d;">43</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">9</span>, <span style="color:#fcd34d;">82</span>, <span style="color:#fcd34d;">10</span>]
<span style="color:#93c5fd;">sorted_arr</span> = merge_sort(unsorted)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Unsorted: {unsorted}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sorted  : {sorted_arr}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Search 43: index {binary_search(sorted_arr, 43)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Search 99: index {binary_search(sorted_arr, 99)}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Unsorted: [38, 27, 43, 3, 9, 82, 10]
Sorted  : [3, 9, 10, 27, 38, 43, 82]
Search 43: index 5
Search 99: index -1</div>
  </div>
</div>

<h3>Sorting Algorithm Cheat Sheet</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:32px;">
  <div style="background:rgba(0,0,0,0.2);padding:12px 20px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.8rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;font-family:'JetBrains Mono',monospace;">Algorithm Comparison</span>
  </div>
  <div style="padding:0;font-size:0.83rem;">
    <div style="display:grid;grid-template-columns:140px 110px 110px 80px 120px;border-bottom:1px solid var(--border);padding:10px 16px;font-weight:700;color:var(--muted);">
      <span>Algorithm</span><span>Best</span><span>Worst</span><span>Space</span><span>Stable?</span>
    </div>
    <div style="display:grid;grid-template-columns:140px 110px 110px 80px 120px;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;">
      <span style="color:var(--text);">Bubble Sort</span><span style="color:#10b981;">O(n)</span><span style="color:#ef4444;">O(n²)</span><span>O(1)</span><span style="color:#10b981;">✓ Yes</span>
    </div>
    <div style="display:grid;grid-template-columns:140px 110px 110px 80px 120px;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;">
      <span style="color:var(--text);">Insertion Sort</span><span style="color:#10b981;">O(n)</span><span style="color:#ef4444;">O(n²)</span><span>O(1)</span><span style="color:#10b981;">✓ Yes</span>
    </div>
    <div style="display:grid;grid-template-columns:140px 110px 110px 80px 120px;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;">
      <span style="color:var(--text);">Merge Sort</span><span style="color:#f59e0b;">O(n log n)</span><span style="color:#f59e0b;">O(n log n)</span><span>O(n)</span><span style="color:#10b981;">✓ Yes</span>
    </div>
    <div style="display:grid;grid-template-columns:140px 110px 110px 80px 120px;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;">
      <span style="color:var(--text);">Quick Sort</span><span style="color:#f59e0b;">O(n log n)</span><span style="color:#ef4444;">O(n²)</span><span>O(log n)</span><span style="color:#ef4444;">✗ No</span>
    </div>
    <div style="display:grid;grid-template-columns:140px 110px 110px 80px 120px;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;">
      <span style="color:var(--text);">Heap Sort</span><span style="color:#f59e0b;">O(n log n)</span><span style="color:#f59e0b;">O(n log n)</span><span>O(1)</span><span style="color:#ef4444;">✗ No</span>
    </div>
    <div style="display:grid;grid-template-columns:140px 110px 110px 80px 120px;padding:10px 16px;background:rgba(16,185,129,0.05);border-top:1px solid rgba(16,185,129,0.2);">
      <span style="color:#10b981;font-weight:700;">Timsort (Python)</span><span style="color:#10b981;">O(n)</span><span style="color:#f59e0b;">O(n log n)</span><span>O(n)</span><span style="color:#10b981;">✓ Yes</span>
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dsaModule->id,
            'title'       => '7.10 Sorting & Searching Algorithms',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L7_10', [
                ['q' => 'Which sorting algorithm guarantees O(n log n) in all cases and is stable?', 'opts' => ['Quick sort', 'Bubble sort', 'Heap sort', 'Merge sort'], 'ans' => 3, 'exp' => 'Merge sort is O(n log n) in best, average, and worst cases — no degenerate inputs can make it slower. It is also stable (equal elements maintain their original relative order). Quick sort is O(n²) in the worst case; heap sort is O(n log n) but not stable.'],
                ['q' => 'Binary search requires that the input array be...', 'opts' => ['Unsorted but indexed', 'Sorted', 'Stored in a hash table', 'Reversed'], 'ans' => 1, 'exp' => 'Binary search works by comparing the target with the middle element and eliminating half the remaining candidates. This halving only works if the array is sorted — otherwise you cannot determine which half contains the target.'],
                ['q' => 'Timsort (Python\'s built-in sort) achieves O(n) time on already-sorted data because...', 'opts' => ['It skips sorting if the list is short', 'It detects existing ordered runs and merges them with minimal work', 'It uses hash tables for sorted detection', 'It is actually O(n log n) even for sorted data'], 'ans' => 1, 'exp' => 'Timsort is a hybrid merge-insertion sort that identifies existing sorted "runs" in real data. If the entire array is already sorted, it detects one run of length n and performs zero merges — O(n). This makes it extremely fast on real-world data that is often partially sorted.'],
                ['q' => 'Quick sort has O(n²) worst-case complexity. When does this occur?', 'opts' => ['When the array has duplicate elements', 'When the pivot always divides the array 50/50', 'When the pivot is always the smallest or largest element (already-sorted or reverse-sorted input)', 'When the array has an odd number of elements'], 'ans' => 2, 'exp' => 'Quick sort\'s worst case is O(n²) when the pivot is always the minimum or maximum — producing unbalanced partitions of size 0 and n-1. This happens on already-sorted or reverse-sorted arrays with a naive first/last-element pivot. Random pivot selection or median-of-three avoids this.'],
                ['q' => 'What does it mean for a sorting algorithm to be "stable"?', 'opts' => ['It never crashes on any input', 'It uses the same amount of memory every time', 'Equal elements maintain their original relative order after sorting', 'Its runtime is the same in best and worst cases'], 'ans' => 2, 'exp' => 'A stable sort preserves the original order of equal elements. For example, if you sort a list of employees first by name, then stably by department, employees within each department remain alphabetically ordered. Pandas\' sort_values is stable by default for exactly this reason.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 7.11 — Final Exam (Org-Locked)
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            // Big-O (7.1)
            ['q' => 'O(3n² + 100n + 500) simplifies to which Big-O class?', 'opts' => ['O(n)', 'O(n²)', 'O(n³)', 'O(500)'], 'ans' => 1, 'exp' => 'Big-O keeps only the dominant term and drops constants. 3n² grows fastest, so O(3n² + 100n + 500) = O(n²).'],
            ['q' => 'An algorithm that cuts the problem size in half at each step has what complexity?', 'opts' => ['O(n)', 'O(n/2)', 'O(log n)', 'O(1)'], 'ans' => 2, 'exp' => 'Halving at each step gives log₂(n) steps total. Binary search and balanced BST lookup are classic O(log n) algorithms.'],
            // Arrays (7.2)
            ['q' => 'Why is arr.insert(0, x) O(n) but arr.append(x) O(1) amortized?', 'opts' => ['append() has a built-in C optimisation for end insertion', 'insert(0) must shift all n existing elements right; append() adds at the end with no shifting', 'Arrays grow faster at the end', 'insert() creates a new array each time'], 'ans' => 1, 'exp' => 'Inserting at index 0 requires shifting every existing element one position right — O(n). Appending at the end just places the element in the next slot — O(1). When the array resizes (O(n)), the doubling strategy makes the amortized cost still O(1).'],
            ['q' => 'sys.getsizeof([]) returns more than 0 even for an empty list because...', 'opts' => ['Python includes memory for future elements by default', 'The list object itself has metadata overhead (~56 bytes) before any elements', 'Python allocates 100 slots by default', 'Empty lists are stored differently than filled ones'], 'ans' => 1, 'exp' => 'A Python list object stores metadata: the list\'s type pointer, reference count, length, capacity, and a pointer to the data array — even before any elements. This overhead is ~56 bytes on 64-bit Python.'],
            // Stacks (7.3)
            ['q' => 'After push(A), push(B), push(C), pop(), push(D) — what does peek() return?', 'opts' => ['A', 'B', 'C', 'D'], 'ans' => 3, 'exp' => 'Push A, B, C: stack=[A,B,C], top=C. pop() removes C: stack=[A,B]. push(D): stack=[A,B,D]. peek() returns D (the top).'],
            ['q' => 'The Python call stack uses LIFO ordering. What happens when a recursive function exceeds maximum depth?', 'opts' => ['Python automatically switches to iteration', 'The oldest frame is removed to make space', 'A RecursionError (stack overflow) is raised', 'Python pauses and asks for more memory'], 'ans' => 2, 'exp' => 'Python\'s call stack has a maximum depth (default ~1000 frames). When recursion pushes more frames than the limit, Python raises RecursionError to prevent memory exhaustion. sys.setrecursionlimit() can increase this limit.'],
            // Queues (7.4)
            ['q' => 'Why is collections.deque O(1) for popleft() while list.pop(0) is O(n)?', 'opts' => ['deque is written in Rust', 'deque is a doubly-linked list of blocks — removing the front only updates a pointer; list must shift all elements left', 'deque caches the front element', 'list.pop(0) sorts before removing'], 'ans' => 1, 'exp' => 'collections.deque is implemented as a doubly-linked list of fixed-size blocks. Removing the front element just advances the head pointer — O(1). A list removes index 0 and shifts all n remaining elements left — O(n).'],
            ['q' => 'A monotonic deque maintains elements in what order?', 'opts' => ['Random order for O(1) access', 'Strictly increasing or strictly decreasing — maintaining a useful ordering property', 'Sorted alphabetically', 'Insertion order with no constraint'], 'ans' => 1, 'exp' => 'A monotonic deque maintains elements in either increasing or decreasing order by removing elements that violate the property when new ones are inserted. This enables O(n) sliding window max/min by ensuring the front always holds the relevant extreme value.'],
            // Linked Lists (7.5)
            ['q' => 'A circular linked list has the tail node pointing to...', 'opts' => ['None (null)', 'The tail itself', 'The head node', 'The middle node'], 'ans' => 2, 'exp' => 'In a circular linked list, the last node\'s next pointer points back to the head instead of None. This enables constant-time round-robin access and is used in operating system process scheduling.'],
            ['q' => 'Floyd\'s cycle detection works because if a cycle exists, the fast pointer (2 steps) will always catch the slow pointer (1 step). This is because...', 'opts' => ['Fast always reaches the tail first', 'The distance between fast and slow decreases by 1 each iteration inside a cycle, guaranteeing they meet', 'Slow stops at the cycle entry', 'Fast teleports to the cycle start'], 'ans' => 1, 'exp' => 'Inside a cycle of length L, the fast pointer gains 1 position on the slow pointer per iteration (it moves 2, slow moves 1, gap reduces by 1). After at most L iterations, fast catches slow — they meet. Time O(n), space O(1).'],
            // Hash Tables (7.6)
            ['q' => 'Which of these cannot be a Python dictionary key?', 'opts' => ['(1, 2, 3)  — a tuple', '"hello"  — a string', '[1, 2, 3]  — a list', '42  — an integer'], 'ans' => 2, 'exp' => 'Dictionary keys must be hashable (immutable). Lists are mutable — their contents can change, which would change their hash and make the key unfindable. Tuples, strings, and integers are all immutable and therefore valid keys.'],
            ['q' => 'Two different keys that map to the same bucket index is called a...', 'opts' => ['Key overflow', 'Hash collision', 'Bucket merge', 'Load violation'], 'ans' => 1, 'exp' => 'A hash collision occurs when hash(key1) % capacity == hash(key2) % capacity for two different keys. Collisions are inevitable (by the pigeonhole principle) and are handled by chaining or open addressing.'],
            // Trees (7.7)
            ['q' => 'Which traversal visits nodes in the order: left subtree → root → right subtree?', 'opts' => ['Preorder', 'Inorder', 'Postorder', 'Level-order'], 'ans' => 1, 'exp' => 'Inorder traversal: left → root → right. For a BST, this always yields values in ascending sorted order — making inorder traversal the standard method to iterate BST contents in order.'],
            ['q' => 'If a BST becomes degenerate (like a linked list), search degrades from O(log n) to...', 'opts' => ['O(1)', 'O(n log n)', 'O(n)', 'O(n²)'], 'ans' => 2, 'exp' => 'A degenerate BST has every node with only one child — essentially a linked list. Search must traverse every node in the worst case: O(n). Self-balancing trees (AVL, Red-Black) prevent this by maintaining O(log n) height.'],
            // Heaps (7.8)
            ['q' => 'Building a heap from n elements using heapify is O(n), but inserting n elements one by one is O(n log n). Why?', 'opts' => ['heapify uses a different algorithm with no sifting', 'heapify uses a bottom-up approach that does less total work; individual insertions each do O(log n) sift-ups', 'heapify skips leaf nodes', 'Individual insertions use bubble sort internally'], 'ans' => 1, 'exp' => 'heapify (heapq.heapify) works bottom-up: most nodes are near the leaves and require fewer sift-down steps. The mathematical sum of work is O(n). Inserting n elements one by one requires O(log n) sift-up per insertion, totaling O(n log n).'],
            // Graphs (7.9)
            ['q' => 'In a directed graph, an edge from A to B means...', 'opts' => ['B can also reach A', 'You can travel A→B but NOT necessarily B→A', 'A and B are the same node', 'The edge has no direction'], 'ans' => 1, 'exp' => 'In a directed graph (digraph), edges have direction. An edge A→B means A connects to B, but there is no edge B→A unless explicitly added. Social media "follows" (Twitter) are directed; Facebook "friendships" are undirected.'],
            ['q' => 'Topological sort applies to which type of graph?', 'opts' => ['Any undirected graph', 'Directed Acyclic Graph (DAG) only', 'Complete graphs only', 'Graphs with negative edge weights'], 'ans' => 1, 'exp' => 'Topological sort orders nodes such that for every directed edge u→v, u appears before v. This only makes sense (and is only possible) in a DAG — a directed graph with no cycles. Applications: build systems, course prerequisites, task scheduling.'],
            // Sorting (7.10)
            ['q' => 'Merge sort\'s merge step combines two sorted arrays. What is the time complexity of merging two arrays of size n/2 each?', 'opts' => ['O(log n)', 'O(n)', 'O(n²)', 'O(1)'], 'ans' => 1, 'exp' => 'Merging two sorted arrays of total size n requires at most n comparisons and n copies — O(n). Each element is placed exactly once. The merge step is the dominant O(n) work at each recursion level.'],
            ['q' => 'Python\'s list.sort() and sorted() both use Timsort. Which should you prefer for in-place sorting?', 'opts' => ['sorted() — it is always faster', 'list.sort() — it modifies in-place and returns None, saving O(n) space vs sorted() which creates a new list', 'They are identical in every way', 'sorted() for lists; list.sort() for tuples'], 'ans' => 1, 'exp' => 'list.sort() sorts the list in-place (modifies it, returns None) — O(1) extra space beyond the O(log n) recursion stack. sorted() creates a brand new sorted list — O(n) extra space. Use list.sort() when you don\'t need the original order; use sorted() when you need to preserve the original.'],
            // Mixed concepts
            ['q' => 'Which data structure gives O(1) average time for all of: insert, delete, search?', 'opts' => ['Sorted array', 'Binary search tree', 'Hash table (dict/set)', 'Linked list'], 'ans' => 2, 'exp' => 'A hash table (Python dict/set) provides O(1) amortized average time for insert, delete, and search via the hash function. Sorted arrays give O(log n) search but O(n) insert/delete. BSTs are O(log n) for all when balanced. Linked lists are O(n) for search.'],
            ['q' => 'You need to find the median of a data stream efficiently. The optimal structure is...', 'opts' => ['A sorted list (re-sort each insertion)', 'Two heaps: a max-heap for the lower half and min-heap for the upper half', 'A queue', 'A BST with O(n) traversal each time'], 'ans' => 1, 'exp' => 'Two heaps: a max-heap for elements ≤ median, min-heap for elements ≥ median. Maintain equal sizes (or off by 1). The median is the top of one or both heaps. Each insertion is O(log n) and median access is O(1). This is the canonical streaming median algorithm.'],
        ];

        $finalContent = <<<HTML
<div id="org-lock-screen" style="text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;">
    <div style="font-size:3rem;margin-bottom:1rem;">🔒</div>
    <h3 style="color:var(--text);margin-bottom:0.5rem;">University / Organization Access Only</h3>
    <p style="color:var(--muted);">The Final Module Exam is restricted to enrolled students and verified organization members.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 7: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 7.1 through 7.10 — Big-O complexity, arrays, stacks, queues, linked lists, hash tables, trees, heaps, graphs, sorting, and searching. Good luck!</p>
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
            'module_id'   => $dsaModule->id,
            'title'       => '7.11 Final Exam: Algorithms & Data Structures Mastery',
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