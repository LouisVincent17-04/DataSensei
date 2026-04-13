<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module1LessonsSeeder
 * Seeds lessons for Module 1: Basics of Python Programming.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module1LessonsSeeder
 */
class Module1LessonsSeeder extends Seeder
{
    public function run()
    {
        $pythonModule = Module::where('order_index', 1)->firstOrFail();
        Lesson::where('module_id', $pythonModule->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.1 — Foundations: Syntax, Output, Comments
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>Python Foundations</h2>
<p>Python is an <strong>interpreted, high-level, general-purpose</strong> programming language created by Guido van Rossum in 1991. It is the dominant language in data science because of its clean syntax, massive ecosystem of libraries, and ease of prototyping. Unlike compiled languages such as C++ or Java, Python executes line-by-line through an <em>interpreter</em> — which means you can test code instantly without a build step.</p>

<h3>Why Python for Data Science?</h3>
<p>Python dominates the data science world because of three reasons: readability (code looks like pseudocode), ecosystem (NumPy, Pandas, Scikit-Learn, TensorFlow all use Python as their primary API), and community (the largest open-source data science community on the planet). When you write Python, you spend more time thinking about your data — not fighting your language.</p>

<h3>Your First Script: The <code>print()</code> Function</h3>
<p>The <code>print()</code> function outputs data to the standard output device — your terminal or console. You will use this constantly for debugging, displaying results, and logging pipeline progress.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Basic Output</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># A hash symbol starts a comment — Python ignores everything after it</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Hello, Data Scientist!"</span>)

<span style="color:#6b7280;"># print() can output multiple items by separating them with commas</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Model Accuracy:"</span>, <span style="color:#fcd34d;">98.5</span>, <span style="color:#a7f3d0;">"%"</span>)

<span style="color:#6b7280;"># The sep= parameter changes the separator between items (default is a space)</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"2024"</span>, <span style="color:#a7f3d0;">"01"</span>, <span style="color:#a7f3d0;">"15"</span>, sep=<span style="color:#a7f3d0;">"-"</span>)

<span style="color:#6b7280;"># The end= parameter changes what is printed at the very end (default is a newline \n)</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Loading"</span>, end=<span style="color:#a7f3d0;">"... "</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Done!"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Hello, Data Scientist!
Model Accuracy: 98.5 %
2024-01-15
Loading... Done!</div>
  </div>
</div>

<h3>Reading User Input with <code>input()</code></h3>
<p>The <code>input()</code> function pauses script execution and waits for the user to type a value and press Enter. <strong>Important:</strong> <code>input()</code> always returns a <em>string</em>, even if the user types a number. You must cast it if you need a numeric value.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — User Input</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># input() always returns a STRING</span>
<span style="color:#93c5fd;">name</span> = <span style="color:#93c5fd;">input</span>(<span style="color:#a7f3d0;">"Enter your name: "</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Welcome to DataSensei, {name}!"</span>)

<span style="color:#6b7280;"># Cast to int to do math with the input</span>
<span style="color:#93c5fd;">age</span> = <span style="color:#93c5fd;">int</span>(<span style="color:#93c5fd;">input</span>(<span style="color:#a7f3d0;">"Enter your age: "</span>))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"In 10 years you will be {age + 10} years old."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Enter your name: Louis
Welcome to DataSensei, Louis!
Enter your age: 21
In 10 years you will be 31 years old.</div>
  </div>
</div>

<h3>Comments: Single-Line & Multi-Line</h3>
<p>Comments are non-executable notes in your code. They exist for <em>you</em> and your teammates — never for Python itself. Good data scientists write comments that explain <em>why</em> they made a decision, not just <em>what</em> the code does.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Comments</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Single-line comment — use # anywhere on a line</span>
<span style="color:#93c5fd;">x</span> = <span style="color:#fcd34d;">42</span>  <span style="color:#6b7280;"># Inline comment is fine too</span>

<span style="color:#6b7280;"># Python has no block comment syntax, but triple-quoted strings
# are conventionally used as multi-line comments or docstrings</span>
<span style="color:#a7f3d0;">"""
This function calculates the precision of a classification model.
Args:
    tp (int): True Positives
    fp (int): False Positives
Returns:
    float: Precision score between 0.0 and 1.0
"""</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Code still runs normally:"</span>, x)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Code still runs normally: 42</div>
  </div>
</div>

<h3>Python Indentation: The Rule That Defines Blocks</h3>
<p>Unlike most languages that use curly braces <code>{}</code> to define code blocks, Python uses <strong>indentation</strong> (whitespace). This is not optional — incorrect indentation causes an <code>IndentationError</code> and crashes your script. The standard is 4 spaces per level.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Indentation Rules</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">if</span> <span style="color:#fcd34d;">10</span> > <span style="color:#fcd34d;">5</span>:
    <span style="color:#6b7280;"># This line is INSIDE the if block — 4 spaces in</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Ten is greater than five"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"This is also inside the block"</span>)

<span style="color:#6b7280;"># Back at 0 indentation — this runs regardless of the if condition</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"This is outside the block"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Ten is greater than five
This is also inside the block
This is outside the block</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.1 Foundations: Syntax, Output, Comments',
            'order_index' => 1,
            'content' => $this->appendQuiz($content1, 'L1_1', [
                ['q' => 'Which function displays text to the console in Python?', 'opts' => ['echo()', 'console.log()', 'print()', 'display()'], 'ans' => 2, 'exp' => 'print() is the standard built-in function for output in Python.'],
                ['q' => 'How do you create a single-line comment in Python?', 'opts' => ['// comment', '/* comment */', '-- comment', '# comment'], 'ans' => 3, 'exp' => 'Python uses the hash symbol (#) for single-line comments. Other languages use // or /*, but not Python.'],
                ['q' => 'What does input() ALWAYS return, regardless of what the user types?', 'opts' => ['Integer', 'Float', 'String', 'Boolean'], 'ans' => 2, 'exp' => 'input() always returns a string. You must explicitly cast it (e.g. int(input())) if you need a number.'],
                ['q' => 'What is the default separator between items in print("A", "B", "C")?', 'opts' => ['Comma', 'Nothing', 'Newline', 'Space'], 'ans' => 3, 'exp' => 'By default, print() separates multiple arguments with a single space. Use sep= to change this.'],
                ['q' => 'What parameter changes what is printed at the end of a print() call?', 'opts' => ['sep=', 'end=', 'suffix=', 'tail='], 'ans' => 1, 'exp' => 'The end= parameter controls the trailing character. By default it is a newline (\\n).'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.2 — Variables, Types, Casting
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Data & Memory: Variables and Types</h2>
<p>A <strong>variable</strong> is a named location in your computer's memory that stores a value. In Python, you create a variable the moment you assign a value to it — no special declaration needed. Python is <em>dynamically typed</em>, meaning the type of the variable is determined automatically at runtime based on the value you assign.</p>

<h3>Core Data Types</h3>
<p>Python has four primary scalar types you will use constantly in data science:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — The Four Core Types</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># int — whole numbers, no decimal point</span>
<span style="color:#93c5fd;">row_count</span> = <span style="color:#fcd34d;">150000</span>

<span style="color:#6b7280;"># float — numbers with a decimal point</span>
<span style="color:#93c5fd;">accuracy</span> = <span style="color:#fcd34d;">0.9742</span>

<span style="color:#6b7280;"># str — text, enclosed in single or double quotes</span>
<span style="color:#93c5fd;">model_name</span> = <span style="color:#a7f3d0;">"Random Forest"</span>

<span style="color:#6b7280;"># bool — only two possible values: True or False</span>
<span style="color:#93c5fd;">is_trained</span> = <span style="color:#fca5a5;">True</span>

<span style="color:#6b7280;"># Use type() to inspect what Python thinks a variable is</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">type</span>(row_count))    <span style="color:#6b7280;"># &lt;class 'int'&gt;</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">type</span>(accuracy))     <span style="color:#6b7280;"># &lt;class 'float'&gt;</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">type</span>(model_name))   <span style="color:#6b7280;"># &lt;class 'str'&gt;</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">type</span>(is_trained))   <span style="color:#6b7280;"># &lt;class 'bool'&gt;</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>&lt;class 'int'&gt;
&lt;class 'float'&gt;
&lt;class 'str'&gt;
&lt;class 'bool'&gt;</div>
  </div>
</div>

<h3>Multiple Assignment & Variable Swapping</h3>
<p>Python allows you to assign multiple variables on a single line. This is frequently used in data science to unpack results from functions that return multiple values.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Multiple Assignment</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Assign multiple variables in one line</span>
<span style="color:#93c5fd;">x</span>, <span style="color:#93c5fd;">y</span>, <span style="color:#93c5fd;">z</span> = <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">20</span>, <span style="color:#fcd34d;">30</span>
<span style="color:#93c5fd;">print</span>(x, y, z)

<span style="color:#6b7280;"># Assign the same value to multiple variables</span>
<span style="color:#93c5fd;">a</span> = <span style="color:#93c5fd;">b</span> = <span style="color:#93c5fd;">c</span> = <span style="color:#fcd34d;">0</span>
<span style="color:#93c5fd;">print</span>(a, b, c)

<span style="color:#6b7280;"># Python's elegant variable swap — no temp variable needed!</span>
<span style="color:#93c5fd;">train_acc</span> = <span style="color:#fcd34d;">0.95</span>
<span style="color:#93c5fd;">test_acc</span>  = <span style="color:#fcd34d;">0.88</span>
<span style="color:#93c5fd;">train_acc</span>, <span style="color:#93c5fd;">test_acc</span> = <span style="color:#93c5fd;">test_acc</span>, <span style="color:#93c5fd;">train_acc</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Swapped:"</span>, train_acc, test_acc)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>10 20 30
0 0 0
Swapped: 0.88 0.95</div>
  </div>
</div>

<h3>Type Casting: Forcing a Conversion</h3>
<p>Sometimes your data arrives in the wrong type. Sensor data from a CSV might come in as strings. You need to <em>cast</em> it to the correct type before doing math. Python provides built-in functions: <code>int()</code>, <code>float()</code>, <code>str()</code>, and <code>bool()</code>.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Casting Functions</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># int() truncates — it does NOT round. 2.9 becomes 2, not 3.</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">int</span>(<span style="color:#fcd34d;">2.9</span>))       <span style="color:#6b7280;"># 2</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">int</span>(<span style="color:#fcd34d;">-3.7</span>))      <span style="color:#6b7280;"># -3 (truncates toward zero)</span>

<span style="color:#6b7280;"># float() converts to decimal</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">float</span>(<span style="color:#a7f3d0;">"3.14"</span>))  <span style="color:#6b7280;"># Reads a string "3.14" as a real number</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">float</span>(<span style="color:#fcd34d;">5</span>))       <span style="color:#6b7280;"># 5.0</span>

<span style="color:#6b7280;"># str() converts anything to its string representation</span>
<span style="color:#93c5fd;">version</span> = <span style="color:#93c5fd;">str</span>(<span style="color:#fcd34d;">3</span>) + <span style="color:#a7f3d0;">"."</span> + <span style="color:#93c5fd;">str</span>(<span style="color:#fcd34d;">11</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Python"</span>, version)

<span style="color:#6b7280;"># bool() — anything non-zero, non-empty is True</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">bool</span>(<span style="color:#fcd34d;">0</span>))         <span style="color:#6b7280;"># False</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">bool</span>(<span style="color:#fcd34d;">42</span>))        <span style="color:#6b7280;"># True</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">bool</span>(<span style="color:#a7f3d0;">""</span>))        <span style="color:#6b7280;"># False — empty string</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">bool</span>(<span style="color:#a7f3d0;">"hello"</span>))   <span style="color:#6b7280;"># True</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>2
-3
3.14
5.0
Python 3.11
False
True
False
True</div>
  </div>
</div>

<h3>Variable Naming Rules</h3>
<p>Variable names must start with a letter or underscore, can only contain letters, numbers, and underscores, and are <strong>case-sensitive</strong> (<code>Score</code> and <code>score</code> are different variables). By convention, Python uses <code>snake_case</code> for variable names.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Naming Conventions</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># ✓ Valid — snake_case is the Python standard</span>
<span style="color:#93c5fd;">model_accuracy</span> = <span style="color:#fcd34d;">0.97</span>
<span style="color:#93c5fd;">_private_var</span>   = <span style="color:#a7f3d0;">"internal use"</span>
<span style="color:#93c5fd;">x2</span>             = <span style="color:#fcd34d;">100</span>

<span style="color:#6b7280;"># Case sensitivity — these are THREE different variables</span>
<span style="color:#93c5fd;">score</span> = <span style="color:#fcd34d;">1</span>
<span style="color:#93c5fd;">Score</span> = <span style="color:#fcd34d;">2</span>
<span style="color:#93c5fd;">SCORE</span> = <span style="color:#fcd34d;">3</span>
<span style="color:#93c5fd;">print</span>(score, Score, SCORE)

<span style="color:#6b7280;"># ✗ Invalid names (would cause SyntaxError):
# 2model = "bad"       — can't start with a digit
# my-variable = "bad"  — hyphens not allowed
# class = "bad"        — reserved keyword</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>1 2 3</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.2 Memory: Variables, Types, Casting',
            'order_index' => 2,
            'content' => $this->appendQuiz($content2, 'L1_2', [
                ['q' => 'What is the data type of True in Python?', 'opts' => ['String', 'Integer', 'Boolean', 'Float'], 'ans' => 2, 'exp' => 'True and False are Python Boolean values (class bool).'],
                ['q' => 'What does int(4.9) return?', 'opts' => ['5', '4', '4.9', 'Error'], 'ans' => 1, 'exp' => 'int() truncates toward zero — it does NOT round. So 4.9 becomes 4.'],
                ['q' => 'Which function reveals the type of a variable?', 'opts' => ['datatype()', 'typeof()', 'type()', 'class()'], 'ans' => 2, 'exp' => 'type() is a built-in function that returns the class of any object.'],
                ['q' => 'What does str(10) + "5" evaluate to?', 'opts' => ['15', '"105"', 'Error', '"10 5"'], 'ans' => 1, 'exp' => 'str(10) converts 10 to the string "10". Then "10" + "5" is string concatenation, giving "105".'],
                ['q' => 'Do you need to declare a variable\'s type before assigning it in Python?', 'opts' => ['Yes, always', 'Only for integers', 'No — Python is dynamically typed', 'Only inside functions'], 'ans' => 2, 'exp' => 'Python determines the type automatically at runtime from the assigned value. This is called dynamic typing.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.3 — Strings & Formatting
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Deep Dive: Strings</h2>
<p>In Python, a <strong>string</strong> is an ordered sequence of characters. Because strings are sequences, they support indexing, slicing, and iteration — the same operations you will later use on lists and arrays. Mastering strings is critical for data science: raw data almost always arrives as text that needs to be cleaned, parsed, and transformed before analysis.</p>

<h3>String Indexing & Negative Indexing</h3>
<p>Each character in a string has a <em>zero-based index</em>. Python also supports <em>negative indexing</em> — counting from the end. Index <code>-1</code> is always the last character.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Indexing</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">lang</span> = <span style="color:#a7f3d0;">"Python"</span>
<span style="color:#6b7280;">#           P  y  t  h  o  n
#  index:    0  1  2  3  4  5
#  negative:-6 -5 -4 -3 -2 -1</span>

<span style="color:#93c5fd;">print</span>(lang[<span style="color:#fcd34d;">0</span>])    <span style="color:#6b7280;"># First character</span>
<span style="color:#93c5fd;">print</span>(lang[<span style="color:#fcd34d;">-1</span>])   <span style="color:#6b7280;"># Last character</span>
<span style="color:#93c5fd;">print</span>(lang[<span style="color:#fcd34d;">-3</span>])   <span style="color:#6b7280;"># Third from the end</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>P
n
h</div>
  </div>
</div>

<h3>Slicing: <code>[start : stop : step]</code></h3>
<p>Slicing extracts a <em>sub-string</em>. The result includes the character at <code>start</code> but <strong>excludes</strong> the character at <code>stop</code>. If you omit <code>start</code>, Python assumes 0. If you omit <code>stop</code>, Python goes to the end. A negative <code>step</code> reverses the iteration direction.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Slicing</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">text</span> = <span style="color:#a7f3d0;">"Data Science"</span>

<span style="color:#93c5fd;">print</span>(text[<span style="color:#fcd34d;">0</span>:<span style="color:#fcd34d;">4</span>])    <span style="color:#6b7280;"># "Data" — chars 0,1,2,3 (stops BEFORE 4)</span>
<span style="color:#93c5fd;">print</span>(text[<span style="color:#fcd34d;">5</span>:])     <span style="color:#6b7280;"># "Science" — from index 5 to end</span>
<span style="color:#93c5fd;">print</span>(text[:<span style="color:#fcd34d;">4</span>])     <span style="color:#6b7280;"># "Data" — from start to index 3</span>
<span style="color:#93c5fd;">print</span>(text[<span style="color:#fcd34d;">-7</span>:])    <span style="color:#6b7280;"># "Science" — last 7 characters</span>
<span style="color:#93c5fd;">print</span>(text[::<span style="color:#fcd34d;">2</span>])    <span style="color:#6b7280;"># Every 2nd character: "Dt cec"</span>
<span style="color:#93c5fd;">print</span>(text[::<span style="color:#fcd34d;">-1</span>])   <span style="color:#6b7280;"># Full reverse!</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Data
Science
Data
Science
Dt cec
ecneicS ataD</div>
  </div>
</div>

<h3>Essential String Methods</h3>
<p>String methods return <strong>new strings</strong> — they never modify the original because strings are <em>immutable</em> in Python. Here are the methods you will use constantly in data cleaning pipelines:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — String Methods</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">raw</span> = <span style="color:#a7f3d0;">"  machine learning, AI, NLP  "</span>

<span style="color:#93c5fd;">print</span>(raw.strip())              <span style="color:#6b7280;"># Remove leading/trailing whitespace</span>
<span style="color:#93c5fd;">print</span>(raw.strip().upper())      <span style="color:#6b7280;"># Convert to ALL CAPS</span>
<span style="color:#93c5fd;">print</span>(raw.strip().lower())      <span style="color:#6b7280;"># Convert to all lowercase</span>
<span style="color:#93c5fd;">print</span>(raw.strip().title())      <span style="color:#6b7280;"># Title Case Each Word</span>
<span style="color:#93c5fd;">print</span>(raw.replace(<span style="color:#a7f3d0;">"AI"</span>, <span style="color:#a7f3d0;">"Artificial Intelligence"</span>))
<span style="color:#93c5fd;">print</span>(raw.strip().split(<span style="color:#a7f3d0;">","</span>))   <span style="color:#6b7280;"># Split into a list on commas</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"ml"</span> <span style="color:#c4b5fd;">in</span> raw)             <span style="color:#6b7280;"># Membership check — True/False</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">len</span>(raw.strip()))        <span style="color:#6b7280;"># Number of characters after stripping</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>machine learning, AI, NLP
MACHINE LEARNING, AI, NLP
machine learning, ai, nlp
Machine Learning, Ai, Nlp
  machine learning, Artificial Intelligence, NLP  
['machine learning', ' AI', ' NLP']
False
28</div>
  </div>
</div>

<h3>F-Strings: Modern String Formatting</h3>
<p>Introduced in Python 3.6, <strong>f-strings</strong> (formatted string literals) let you embed any Python expression directly inside a string using <code>{}</code> curly braces. They are faster, more readable, and more powerful than older <code>format()</code> or <code>%</code> formatting.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — F-Strings</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">model</span>    = <span style="color:#a7f3d0;">"Random Forest"</span>
<span style="color:#93c5fd;">accuracy</span> = <span style="color:#fcd34d;">0.9742</span>
<span style="color:#93c5fd;">epochs</span>   = <span style="color:#fcd34d;">50</span>

<span style="color:#6b7280;"># Basic variable injection</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Model: {model}"</span>)

<span style="color:#6b7280;"># Inline math expressions inside {}</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Error rate: {1 - accuracy:.2%}"</span>)  <span style="color:#6b7280;"># :.2% formats as percentage</span>

<span style="color:#6b7280;"># Rounding with format spec</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Accuracy: {accuracy:.4f}"</span>)         <span style="color:#6b7280;"># 4 decimal places</span>

<span style="color:#6b7280;"># Conditional logic inside an f-string</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Training: {'Done' if epochs >= 50 else 'In Progress'}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Model: Random Forest
Error rate: 2.58%
Accuracy: 0.9742
Training: Done</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.3 Text Processing: Strings & Formatting',
            'order_index' => 3,
            'content' => $this->appendQuiz($content3, 'L1_3', [
                ['q' => 'Given text = "Python", what is text[-1]?', 'opts' => ['P', 'n', 'o', 'Error'], 'ans' => 1, 'exp' => 'Negative index -1 always refers to the last character. "Python"[-1] is "n".'],
                ['q' => 'What does strip() do?', 'opts' => ['Converts to lowercase', 'Removes leading and trailing whitespace', 'Splits into a list', 'Reverses the string'], 'ans' => 1, 'exp' => 'strip() removes whitespace (spaces, tabs, newlines) from both ends of the string.'],
                ['q' => 'What prefix is required for an f-string?', 'opts' => ['s"..."', 'x"..."', 'f"..."', 'format"..."'], 'ans' => 2, 'exp' => 'f-strings are prefixed with the letter f: f"Hello {name}". They require Python 3.6+.'],
                ['q' => '"Data Science"[5:] returns what?', 'opts' => ['Data', 'Science', 'Data S', 'cience'], 'ans' => 1, 'exp' => 'Index 5 is "S". Slicing from 5 to end extracts "Science".'],
                ['q' => 'Are strings mutable in Python?', 'opts' => ['Yes, you can change individual characters', 'No, strings are immutable', 'Only if enclosed in double quotes', 'Only after casting'], 'ans' => 1, 'exp' => 'Strings are immutable — you cannot change them in place. String methods always return a new string.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.4 — Booleans & Operators
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Booleans & Operators</h2>
<p>Operators are the symbols that tell Python how to compute, compare, and combine values. They are the building blocks of every conditional, every filter, and every mathematical transformation you will ever write in a data pipeline.</p>

<h3>Arithmetic Operators</h3>
<p>Python supports all standard math operations. Pay special attention to <code>//</code> (floor division) and <code>%</code> (modulo) — these appear constantly in data science for indexing, batching, and checking divisibility.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Arithmetic</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">x</span>, <span style="color:#93c5fd;">y</span> = <span style="color:#fcd34d;">17</span>, <span style="color:#fcd34d;">5</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Addition:"</span>,       x + y)   <span style="color:#6b7280;"># 22</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Subtraction:"</span>,    x - y)   <span style="color:#6b7280;"># 12</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Multiplication:"</span>, x * y)   <span style="color:#6b7280;"># 85</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Division:"</span>,       x / y)   <span style="color:#6b7280;"># 3.4  (always returns float)</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Floor Div:"</span>,      x // y)  <span style="color:#6b7280;"># 3    (integer quotient, rounds down)</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Modulo:"</span>,         x % y)   <span style="color:#6b7280;"># 2    (remainder after floor division)</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Exponent:"</span>,       x ** y)  <span style="color:#6b7280;"># 1419857 (17 to the power of 5)</span>

<span style="color:#6b7280;"># Augmented assignment operators</span>
<span style="color:#93c5fd;">counter</span> = <span style="color:#fcd34d;">0</span>
<span style="color:#93c5fd;">counter</span> += <span style="color:#fcd34d;">1</span>   <span style="color:#6b7280;"># Same as: counter = counter + 1</span>
<span style="color:#93c5fd;">counter</span> *= <span style="color:#fcd34d;">10</span>  <span style="color:#6b7280;"># Same as: counter = counter * 10</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Counter:"</span>, counter)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Addition: 22
Subtraction: 12
Multiplication: 85
Division: 3.4
Floor Div: 3
Modulo: 2
Exponent: 1419857
Counter: 10</div>
  </div>
</div>

<h3>Comparison Operators</h3>
<p>Comparison operators always return a Boolean (<code>True</code> or <code>False</code>). These are the engine of every <code>if</code> statement and every Pandas filter you will write.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Comparisons</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">score</span> = <span style="color:#fcd34d;">85</span>
<span style="color:#93c5fd;">print</span>(score == <span style="color:#fcd34d;">85</span>)   <span style="color:#6b7280;"># Equal to             → True</span>
<span style="color:#93c5fd;">print</span>(score != <span style="color:#fcd34d;">100</span>)  <span style="color:#6b7280;"># Not equal to         → True</span>
<span style="color:#93c5fd;">print</span>(score &gt; <span style="color:#fcd34d;">90</span>)    <span style="color:#6b7280;"># Greater than         → False</span>
<span style="color:#93c5fd;">print</span>(score &lt; <span style="color:#fcd34d;">90</span>)    <span style="color:#6b7280;"># Less than            → True</span>
<span style="color:#93c5fd;">print</span>(score &gt;= <span style="color:#fcd34d;">85</span>)   <span style="color:#6b7280;"># Greater than or equal → True</span>
<span style="color:#93c5fd;">print</span>(score &lt;= <span style="color:#fcd34d;">84</span>)   <span style="color:#6b7280;"># Less than or equal   → False</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>True
True
False
True
True
False</div>
  </div>
</div>

<h3>Logical, Identity & Membership Operators</h3>
<p><strong>Logical operators</strong> (<code>and</code>, <code>or</code>, <code>not</code>) combine conditions. <strong>Identity operators</strong> (<code>is</code>, <code>is not</code>) check whether two variables point to the exact same object in memory — not just equal values. <strong>Membership operators</strong> (<code>in</code>, <code>not in</code>) check if a value exists inside a sequence.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Logical, Identity, Membership</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Logical — combines conditions</span>
<span style="color:#93c5fd;">age</span>, <span style="color:#93c5fd;">score</span> = <span style="color:#fcd34d;">20</span>, <span style="color:#fcd34d;">88</span>
<span style="color:#93c5fd;">print</span>(age &gt;= <span style="color:#fcd34d;">18</span> <span style="color:#c4b5fd;">and</span> score &gt;= <span style="color:#fcd34d;">75</span>)  <span style="color:#6b7280;"># True (both conditions met)</span>
<span style="color:#93c5fd;">print</span>(age &lt; <span style="color:#fcd34d;">18</span> <span style="color:#c4b5fd;">or</span> score &gt; <span style="color:#fcd34d;">80</span>)    <span style="color:#6b7280;"># True (at least one is met)</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#c4b5fd;">not</span> (score &gt; <span style="color:#fcd34d;">90</span>))           <span style="color:#6b7280;"># True (negates False)</span>

<span style="color:#6b7280;"># Identity — checks if the SAME object in memory</span>
<span style="color:#93c5fd;">a</span> = [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>]
<span style="color:#93c5fd;">b</span> = [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>]
<span style="color:#93c5fd;">c</span> = a
<span style="color:#93c5fd;">print</span>(a <span style="color:#c4b5fd;">is</span> b)   <span style="color:#6b7280;"># False — same value but different objects in RAM</span>
<span style="color:#93c5fd;">print</span>(a <span style="color:#c4b5fd;">is</span> c)   <span style="color:#6b7280;"># True  — c points to the exact same object as a</span>

<span style="color:#6b7280;"># Membership — checks if value exists in a sequence</span>
<span style="color:#93c5fd;">models</span> = [<span style="color:#a7f3d0;">"SVM"</span>, <span style="color:#a7f3d0;">"KNN"</span>, <span style="color:#a7f3d0;">"XGBoost"</span>]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"KNN"</span> <span style="color:#c4b5fd;">in</span> models)        <span style="color:#6b7280;"># True</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"BERT"</span> <span style="color:#c4b5fd;">not in</span> models)   <span style="color:#6b7280;"># True</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>True
True
True
False
True
True
True</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.4 Logic: Booleans & Operators',
            'order_index' => 4,
            'content' => $this->appendQuiz($content4, 'L1_4', [
                ['q' => 'What is the result of 17 % 5?', 'opts' => ['3.4', '3', '2', '0'], 'ans' => 2, 'exp' => 'Modulo (%) returns the remainder. 17 ÷ 5 = 3 remainder 2. So 17 % 5 = 2.'],
                ['q' => 'What Python operator represents exponentiation (power)?', 'opts' => ['^', '**', '//', '%%'], 'ans' => 1, 'exp' => '** is the exponentiation operator in Python. 2 ** 10 = 1024. Note: ^ is bitwise XOR in Python, not power.'],
                ['q' => 'What does the "is" operator actually check?', 'opts' => ['Same value', 'Same object identity in memory', 'Value exists in a list', 'Strings match case-insensitively'], 'ans' => 1, 'exp' => '"is" checks identity (same memory address), not equality. Two lists with equal contents will pass == but fail "is" because they are different objects.'],
                ['q' => 'Which of these is a valid logical operator in Python?', 'opts' => ['&&', '||', '!', 'and'], 'ans' => 3, 'exp' => 'Python uses the English words "and", "or", "not" — not the C-style &&, ||, or ! symbols.'],
                ['q' => 'What is 10 // 3?', 'opts' => ['3.33', '3', '1', 'Error'], 'ans' => 1, 'exp' => 'Floor division (//) returns the integer quotient, rounding down. 10 ÷ 3 = 3.33..., floor gives 3.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.5 — Lists
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Collections I: Lists</h2>
<p>A <strong>list</strong> is Python's most versatile built-in data structure. It is an ordered, mutable sequence that can hold any mix of data types. In data science, lists are the foundation for building datasets before converting to NumPy arrays or Pandas DataFrames. Understanding them deeply will save you hours of debugging later.</p>

<h3>Creating & Accessing Lists</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — List Basics</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># A list of model names</span>
<span style="color:#93c5fd;">models</span> = [<span style="color:#a7f3d0;">"Linear Regression"</span>, <span style="color:#a7f3d0;">"Decision Tree"</span>, <span style="color:#a7f3d0;">"SVM"</span>, <span style="color:#a7f3d0;">"XGBoost"</span>]

<span style="color:#6b7280;"># Indexing (same as strings)</span>
<span style="color:#93c5fd;">print</span>(models[<span style="color:#fcd34d;">0</span>])   <span style="color:#6b7280;"># First item</span>
<span style="color:#93c5fd;">print</span>(models[<span style="color:#fcd34d;">-1</span>])  <span style="color:#6b7280;"># Last item</span>

<span style="color:#6b7280;"># Lists can hold mixed types</span>
<span style="color:#93c5fd;">record</span> = [<span style="color:#a7f3d0;">"Louis"</span>, <span style="color:#fcd34d;">21</span>, <span style="color:#fcd34d;">3.75</span>, <span style="color:#fca5a5;">True</span>]
<span style="color:#93c5fd;">print</span>(record)

<span style="color:#6b7280;"># len() gives the number of items</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Total models:"</span>, <span style="color:#93c5fd;">len</span>(models))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Linear Regression
XGBoost
['Louis', 21, 3.75, True]
Total models: 4</div>
  </div>
</div>

<h3>Modifying Lists: Add, Remove, Sort</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Mutating Lists</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">scores</span> = [<span style="color:#fcd34d;">88</span>, <span style="color:#fcd34d;">72</span>, <span style="color:#fcd34d;">95</span>, <span style="color:#fcd34d;">61</span>]

<span style="color:#93c5fd;">scores</span>.append(<span style="color:#fcd34d;">79</span>)           <span style="color:#6b7280;"># Adds to the END</span>
<span style="color:#93c5fd;">scores</span>.insert(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">100</span>)       <span style="color:#6b7280;"># Inserts 100 at index 0</span>
<span style="color:#93c5fd;">scores</span>.remove(<span style="color:#fcd34d;">61</span>)           <span style="color:#6b7280;"># Removes FIRST occurrence of 61</span>
<span style="color:#93c5fd;">popped</span> = scores.pop()       <span style="color:#6b7280;"># Removes & returns LAST item</span>
<span style="color:#93c5fd;">scores</span>.sort()               <span style="color:#6b7280;"># Sorts in-place, ascending</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Popped value:"</span>, popped)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Sorted list:"</span>, scores)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Min:"</span>, <span style="color:#93c5fd;">min</span>(scores), <span style="color:#a7f3d0;">"| Max:"</span>, <span style="color:#93c5fd;">max</span>(scores), <span style="color:#a7f3d0;">"| Sum:"</span>, <span style="color:#93c5fd;">sum</span>(scores))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Popped value: 79
Sorted list: [72, 88, 95, 100]
Min: 72 | Max: 100 | Sum: 355</div>
  </div>
</div>

<h3>List Comprehensions — Python's Superpower</h3>
<p>List comprehensions create a new list by applying an expression to each item in an iterable, optionally filtering with a condition. The syntax is: <code>[expression for item in iterable if condition]</code>. They replace 4-line <code>for</code> loop blocks with a single readable line and are significantly faster.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — List Comprehensions</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom: 16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">raw_prices</span> = [<span style="color:#fcd34d;">120</span>, <span style="color:#fcd34d;">45</span>, <span style="color:#fcd34d;">300</span>, <span style="color:#fcd34d;">80</span>, <span style="color:#fcd34d;">500</span>]

<span style="color:#6b7280;"># Apply 12% VAT to every price</span>
<span style="color:#93c5fd;">with_vat</span> = [p * <span style="color:#fcd34d;">1.12</span> <span style="color:#c4b5fd;">for</span> p <span style="color:#c4b5fd;">in</span> raw_prices]

<span style="color:#6b7280;"># Filter: only keep prices above 100</span>
<span style="color:#93c5fd;">expensive</span> = [p <span style="color:#c4b5fd;">for</span> p <span style="color:#c4b5fd;">in</span> raw_prices <span style="color:#c4b5fd;">if</span> p &gt; <span style="color:#fcd34d;">100</span>]

<span style="color:#6b7280;"># Transform AND filter: VAT on items over 100 only</span>
<span style="color:#93c5fd;">taxed_expensive</span> = [p * <span style="color:#fcd34d;">1.12</span> <span style="color:#c4b5fd;">for</span> p <span style="color:#c4b5fd;">in</span> raw_prices <span style="color:#c4b5fd;">if</span> p &gt; <span style="color:#fcd34d;">100</span>]

<span style="color:#6b7280;"># Squares of even numbers from 1 to 10</span>
<span style="color:#93c5fd;">even_squares</span> = [n**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> n <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">11</span>) <span style="color:#c4b5fd;">if</span> n % <span style="color:#fcd34d;">2</span> == <span style="color:#fcd34d;">0</span>]

<span style="color:#93c5fd;">print</span>(with_vat)
<span style="color:#93c5fd;">print</span>(expensive)
<span style="color:#93c5fd;">print</span>(taxed_expensive)
<span style="color:#93c5fd;">print</span>(even_squares)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>[134.4, 50.4, 336.0, 89.6, 560.0]
[120, 300, 500]
[134.4, 336.0, 560.0]
[4, 16, 36, 64, 100]</div>
  </div>
</div>

<h3>Nested Lists (2D Data)</h3>
<p>Lists can contain other lists — creating a matrix or table structure. This is the manual version of a 2D NumPy array, and understanding it helps you visualize what Pandas DataFrames do under the hood.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Nested Lists (Matrix)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># A 3x3 matrix (3 rows, 3 columns)</span>
<span style="color:#93c5fd;">matrix</span> = [
    [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>],
    [<span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">6</span>],
    [<span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">9</span>]
]

<span style="color:#6b7280;"># Access row 1, column 2 (zero-indexed)</span>
<span style="color:#93c5fd;">print</span>(matrix[<span style="color:#fcd34d;">1</span>][<span style="color:#fcd34d;">2</span>])   <span style="color:#6b7280;"># 6</span>

<span style="color:#6b7280;"># Flatten the matrix into a 1D list using a nested comprehension</span>
<span style="color:#93c5fd;">flat</span> = [val <span style="color:#c4b5fd;">for</span> row <span style="color:#c4b5fd;">in</span> matrix <span style="color:#c4b5fd;">for</span> val <span style="color:#c4b5fd;">in</span> row]
<span style="color:#93c5fd;">print</span>(flat)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>6
[1, 2, 3, 4, 5, 6, 7, 8, 9]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.5 Collections I: Lists & Arrays',
            'order_index' => 5,
            'content' => $this->appendQuiz($content5, 'L1_5', [
                ['q' => 'Which method adds an item to the END of a list?', 'opts' => ['add()', 'insert()', 'push()', 'append()'], 'ans' => 3, 'exp' => 'append() adds one item to the end of the list. insert(index, value) adds at a specific position.'],
                ['q' => 'What does pop() return by default?', 'opts' => ['Removes and returns the first item', 'Removes and returns the last item', 'Deletes the entire list', 'Returns a copy of the list'], 'ans' => 1, 'exp' => 'pop() with no argument removes and returns the last item. pop(0) would target the first item.'],
                ['q' => 'What is the syntax for a list comprehension?', 'opts' => ['(expr for x in list)', '{expr for x in list}', '[expr for x in list if cond]', 'list(expr for x)'], 'ans' => 2, 'exp' => 'List comprehensions use square brackets: [expression for item in iterable if condition]. The if condition is optional.'],
                ['q' => 'Are Python lists mutable?', 'opts' => ['Yes', 'No', 'Only for integers', 'Only in functions'], 'ans' => 0, 'exp' => 'Lists are mutable — you can change, add, or remove items after creation. This distinguishes them from tuples.'],
                ['q' => 'How do you access the element at row 2, column 1 of a nested list called grid?', 'opts' => ['grid[2,1]', 'grid[2][1]', 'grid.get(2,1)', 'grid(2)(1)'], 'ans' => 1, 'exp' => 'For nested lists, chain the index operators: grid[row][column]. grid[2][1] accesses row index 2, column index 1.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.6 — Tuples & Sets
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Collections II: Tuples & Sets</h2>
<p>Python has four built-in collection types: lists, <strong>tuples</strong>, <strong>sets</strong>, and dictionaries. Each has a distinct purpose. Choosing the wrong one leads to bugs that are hard to trace. This lesson covers tuples and sets — two structures with very different superpowers.</p>

<h3>Tuples — Immutable Ordered Sequences</h3>
<p>A <strong>tuple</strong> is like a list but <em>immutable</em> — once created, it cannot be changed. Use tuples for data that should never be modified: GPS coordinates, RGB color values, database primary keys, or function return values. Because tuples are immutable, Python can store them more efficiently than lists.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Tuples</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Parentheses define a tuple (parentheses are optional but conventional)</span>
<span style="color:#93c5fd;">coords</span> = (<span style="color:#fcd34d;">14.5995</span>, <span style="color:#fcd34d;">120.9842</span>)    <span style="color:#6b7280;"># Manila, Philippines GPS</span>
<span style="color:#93c5fd;">rgb_red</span> = (<span style="color:#fcd34d;">255</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>)

<span style="color:#6b7280;"># Indexing works exactly like lists</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Latitude:"</span>, coords[<span style="color:#fcd34d;">0</span>])
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Red channel:"</span>, rgb_red[<span style="color:#fcd34d;">0</span>])

<span style="color:#6b7280;"># This would raise a TypeError — tuples are IMMUTABLE
# coords[0] = 0.0  ← TypeError: 'tuple' object does not support item assignment</span>

<span style="color:#6b7280;"># Functions often return multiple values as a tuple</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">train_test_split</span>(data, ratio):
    split = <span style="color:#93c5fd;">int</span>(<span style="color:#93c5fd;">len</span>(data) * ratio)
    <span style="color:#c4b5fd;">return</span> data[:split], data[split:]    <span style="color:#6b7280;"># returns a tuple</span>

<span style="color:#93c5fd;">train</span>, <span style="color:#93c5fd;">test</span> = train_test_split([<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">2</span>,<span style="color:#fcd34d;">3</span>,<span style="color:#fcd34d;">4</span>,<span style="color:#fcd34d;">5</span>,<span style="color:#fcd34d;">6</span>,<span style="color:#fcd34d;">7</span>,<span style="color:#fcd34d;">8</span>,<span style="color:#fcd34d;">9</span>,<span style="color:#fcd34d;">10</span>], <span style="color:#fcd34d;">0.8</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Train:"</span>, train)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Test:"</span>, test)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Latitude: 14.5995
Red channel: 255
Train: [1, 2, 3, 4, 5, 6, 7, 8]
Test: [9, 10]</div>
  </div>
</div>

<h3>Tuple Unpacking & the Star Operator</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Unpacking</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">pipeline</span> = (<span style="color:#a7f3d0;">"Preprocess"</span>, <span style="color:#a7f3d0;">"Train"</span>, <span style="color:#a7f3d0;">"Validate"</span>, <span style="color:#a7f3d0;">"Deploy"</span>)

<span style="color:#6b7280;"># Unpack all values into named variables</span>
<span style="color:#93c5fd;">step1</span>, <span style="color:#93c5fd;">step2</span>, <span style="color:#93c5fd;">step3</span>, <span style="color:#93c5fd;">step4</span> = pipeline
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"First step:"</span>, step1)

<span style="color:#6b7280;"># Star (*) collects remaining items into a list</span>
<span style="color:#93c5fd;">first</span>, *<span style="color:#93c5fd;">middle</span>, <span style="color:#93c5fd;">last</span> = pipeline
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Middle steps:"</span>, middle)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Last step:"</span>, last)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>First step: Preprocess
Middle steps: ['Train', 'Validate']
Last step: Deploy</div>
  </div>
</div>

<h3>Sets — Unique & Unordered</h3>
<p>A <strong>set</strong> stores only <em>unique</em> values and has <em>no guaranteed order</em>. Sets have O(1) membership testing — checking <code>x in my_set</code> is dramatically faster than checking <code>x in my_list</code> for large datasets. Sets are ideal for deduplication and set algebra (union, intersection, difference).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Sets</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Duplicate removal — the classic set trick</span>
<span style="color:#93c5fd;">tags</span> = [<span style="color:#a7f3d0;">"python"</span>, <span style="color:#a7f3d0;">"ml"</span>, <span style="color:#a7f3d0;">"python"</span>, <span style="color:#a7f3d0;">"nlp"</span>, <span style="color:#a7f3d0;">"ml"</span>, <span style="color:#a7f3d0;">"deep-learning"</span>]
<span style="color:#93c5fd;">unique_tags</span> = <span style="color:#93c5fd;">list</span>(<span style="color:#93c5fd;">set</span>(tags))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Unique tags:"</span>, unique_tags)

<span style="color:#6b7280;"># Set algebra — crucial for data reconciliation</span>
<span style="color:#93c5fd;">group_a</span> = {<span style="color:#a7f3d0;">"Alice"</span>, <span style="color:#a7f3d0;">"Bob"</span>, <span style="color:#a7f3d0;">"Carol"</span>}
<span style="color:#93c5fd;">group_b</span> = {<span style="color:#a7f3d0;">"Bob"</span>, <span style="color:#a7f3d0;">"Dave"</span>, <span style="color:#a7f3d0;">"Carol"</span>}

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Union (all members):"</span>,        group_a | group_b)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Intersection (in both):"</span>,     group_a &amp; group_b)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Difference (A not in B):"</span>,    group_a - group_b)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Symmetric diff (not shared):"</span>, group_a ^ group_b)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Unique tags: ['deep-learning', 'python', 'nlp', 'ml']
Union (all members): {'Alice', 'Bob', 'Carol', 'Dave'}
Intersection (in both): {'Bob', 'Carol'}
Difference (A not in B): {'Alice'}
Symmetric diff (not shared): {'Alice', 'Dave'}</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.6 Collections II: Tuples & Sets',
            'order_index' => 6,
            'content' => $this->appendQuiz($content6, 'L1_6', [
                ['q' => 'What is the key difference between a list and a tuple?', 'opts' => ['Tuples hold strings only', 'Tuples are immutable — cannot be changed after creation', 'Lists are immutable', 'Tuples automatically remove duplicates'], 'ans' => 1, 'exp' => 'Tuples are immutable sequences. Once created, you cannot add, remove, or change their elements. Lists are mutable.'],
                ['q' => 'Which operator collects remaining unpacked items into a list?', 'opts' => ['@', '&', '#', '*'], 'ans' => 3, 'exp' => 'The star (*) operator in unpacking collects all remaining items into a list. Example: first, *rest = (1, 2, 3, 4) → rest becomes [2, 3, 4].'],
                ['q' => 'What happens when you add a duplicate to a set?', 'opts' => ['It raises a ValueError', 'It creates a nested set', 'The duplicate is silently ignored', 'It replaces the existing value'], 'ans' => 2, 'exp' => 'Sets only store unique values. Adding a duplicate is a no-op — Python silently ignores it without raising any error.'],
                ['q' => 'Which set operator gives you elements that exist in BOTH sets (intersection)?', 'opts' => ['|', '&', '-', '^'], 'ans' => 1, 'exp' => '& is the intersection operator. | is union (all from both), - is difference (in A not B), ^ is symmetric difference (not shared).'],
                ['q' => 'What does set() do when given a list with duplicates?', 'opts' => ['Raises an error', 'Keeps all duplicates', 'Returns only unique values', 'Sorts the list'], 'ans' => 2, 'exp' => 'set() converts an iterable to a set, automatically removing duplicates. list(set(my_list)) is the classic one-liner for deduplication.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.7 — Dictionaries
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Collections III: Dictionaries</h2>
<p>A <strong>dictionary</strong> stores data as <code>key: value</code> pairs. It is Python's most powerful built-in data structure and the closest native analog to a database row, a JSON object, or a Pandas row. Dictionary lookup by key is O(1) — instant, regardless of dictionary size.</p>

<h3>Creating & Accessing Dictionaries</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Dict Basics</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">model_stats</span> = {
    <span style="color:#a7f3d0;">"name"</span>:      <span style="color:#a7f3d0;">"XGBoost"</span>,
    <span style="color:#a7f3d0;">"accuracy"</span>:  <span style="color:#fcd34d;">0.9742</span>,
    <span style="color:#a7f3d0;">"epochs"</span>:    <span style="color:#fcd34d;">200</span>,
    <span style="color:#a7f3d0;">"trained"</span>:   <span style="color:#fca5a5;">True</span>
}

<span style="color:#6b7280;"># Bracket access — raises KeyError if key doesn't exist</span>
<span style="color:#93c5fd;">print</span>(model_stats[<span style="color:#a7f3d0;">"name"</span>])

<span style="color:#6b7280;"># .get() access — returns None (or a fallback) if key is missing</span>
<span style="color:#93c5fd;">print</span>(model_stats.get(<span style="color:#a7f3d0;">"f1_score"</span>, <span style="color:#a7f3d0;">"Not calculated"</span>))

<span style="color:#6b7280;"># Add a new key</span>
<span style="color:#93c5fd;">model_stats</span>[<span style="color:#a7f3d0;">"f1_score"</span>] = <span style="color:#fcd34d;">0.968</span>

<span style="color:#6b7280;"># Check if a key exists</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"f1_score"</span> <span style="color:#c4b5fd;">in</span> model_stats)   <span style="color:#6b7280;"># True now</span>

<span style="color:#6b7280;"># Delete a key</span>
<span style="color:#c4b5fd;">del</span> model_stats[<span style="color:#a7f3d0;">"trained"</span>]
<span style="color:#93c5fd;">print</span>(model_stats)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>XGBoost
Not calculated
True
{'name': 'XGBoost', 'accuracy': 0.9742, 'epochs': 200, 'f1_score': 0.968}</div>
  </div>
</div>

<h3>Iterating & Bulk Updates</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Dict Iteration & Update</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">hyperparams</span> = {<span style="color:#a7f3d0;">"lr"</span>: <span style="color:#fcd34d;">0.01</span>, <span style="color:#a7f3d0;">"batch_size"</span>: <span style="color:#fcd34d;">32</span>, <span style="color:#a7f3d0;">"dropout"</span>: <span style="color:#fcd34d;">0.3</span>}

<span style="color:#6b7280;"># Iterate over keys</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Keys:"</span>, <span style="color:#93c5fd;">list</span>(hyperparams.keys()))

<span style="color:#6b7280;"># Iterate over values</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Values:"</span>, <span style="color:#93c5fd;">list</span>(hyperparams.values()))

<span style="color:#6b7280;"># Iterate over key-value pairs (most common pattern)</span>
<span style="color:#c4b5fd;">for</span> param, val <span style="color:#c4b5fd;">in</span> hyperparams.items():
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {param:12} → {val}"</span>)

<span style="color:#6b7280;"># Merge another dict in (update existing keys, add new ones)</span>
<span style="color:#93c5fd;">hyperparams</span>.update({<span style="color:#a7f3d0;">"epochs"</span>: <span style="color:#fcd34d;">50</span>, <span style="color:#a7f3d0;">"lr"</span>: <span style="color:#fcd34d;">0.001</span>})
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"After update:"</span>, hyperparams)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Keys: ['lr', 'batch_size', 'dropout']
Values: [0.01, 32, 0.3]
  lr           → 0.01
  batch_size   → 32
  dropout      → 0.3
After update: {'lr': 0.001, 'batch_size': 32, 'dropout': 0.3, 'epochs': 50}</div>
  </div>
</div>

<h3>Dictionary Comprehensions</h3>
<p>Just like list comprehensions, you can build dictionaries in a single line. This is heavily used in data science for building lookup tables, label encodings, and transforming one dict into another.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Dict Comprehensions</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Build a label encoding dictionary from a list of class names</span>
<span style="color:#93c5fd;">classes</span> = [<span style="color:#a7f3d0;">"cat"</span>, <span style="color:#a7f3d0;">"dog"</span>, <span style="color:#a7f3d0;">"bird"</span>, <span style="color:#a7f3d0;">"fish"</span>]
<span style="color:#93c5fd;">label_enc</span> = {cls: idx <span style="color:#c4b5fd;">for</span> idx, cls <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(classes)}
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Label Encoding:"</span>, label_enc)

<span style="color:#6b7280;"># Invert the mapping: index → class name</span>
<span style="color:#93c5fd;">decode</span> = {v: k <span style="color:#c4b5fd;">for</span> k, v <span style="color:#c4b5fd;">in</span> label_enc.items()}
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Decoded label 2:"</span>, decode[<span style="color:#fcd34d;">2</span>])</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Label Encoding: {'cat': 0, 'dog': 1, 'bird': 2, 'fish': 3}
Decoded label 2: bird</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.7 Collections III: Dictionaries',
            'order_index' => 7,
            'content' => $this->appendQuiz($content7, 'L1_7', [
                ['q' => 'How do you safely access a key that might not exist in a dict?', 'opts' => ['dict.key', 'dict[key]', 'dict.get(key, fallback)', 'dict.find(key)'], 'ans' => 2, 'exp' => '.get(key, fallback) returns the fallback value instead of raising a KeyError if the key does not exist. This is the safe access pattern.'],
                ['q' => 'Which method returns key-value tuple pairs for iteration?', 'opts' => ['keys()', 'values()', 'pairs()', 'items()'], 'ans' => 3, 'exp' => '.items() returns view objects of (key, value) tuples. You typically unpack them: for k, v in my_dict.items().'],
                ['q' => 'What happens if you access dict["nonexistent_key"] with bracket notation?', 'opts' => ['Returns None', 'Returns an empty string', 'Raises a KeyError', 'Creates the key with value None'], 'ans' => 2, 'exp' => 'Direct bracket access raises a KeyError if the key does not exist. Use .get() for safe access with a default value.'],
                ['q' => 'Can dict keys be mutable objects like lists?', 'opts' => ['Yes', 'No — keys must be immutable (hashable)', 'Yes, if the list is empty', 'Yes, but not recommended'], 'ans' => 1, 'exp' => 'Dictionary keys must be hashable (immutable). Strings, numbers, and tuples work as keys. Lists cannot be keys because they are mutable and therefore unhashable.'],
                ['q' => 'What does the update() method do?', 'opts' => ['Deletes all keys', 'Returns a sorted dict', 'Merges another dict in, overwriting existing keys', 'Renames all keys'], 'ans' => 2, 'exp' => 'update() merges key-value pairs from another dict. If a key already exists, its value is overwritten. New keys are added.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.8 — Match & Try/Except
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Control Flow: If/Elif/Else, Match & Exception Handling</h2>
<p>Real data is messy. Pipelines encounter missing values, malformed inputs, and unexpected states. This lesson covers the tools Python gives you to handle <em>conditional logic</em> cleanly and <em>errors gracefully</em> — without crashing your entire analysis.</p>

<h3>If / Elif / Else Chains</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Conditionals</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">accuracy</span> = <span style="color:#fcd34d;">0.78</span>

<span style="color:#c4b5fd;">if</span> accuracy &gt;= <span style="color:#fcd34d;">0.95</span>:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Excellent — deploy to production"</span>)
<span style="color:#c4b5fd;">elif</span> accuracy &gt;= <span style="color:#fcd34d;">0.85</span>:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Good — needs minor tuning"</span>)
<span style="color:#c4b5fd;">elif</span> accuracy &gt;= <span style="color:#fcd34d;">0.70</span>:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Acceptable — consider more data"</span>)
<span style="color:#c4b5fd;">else</span>:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Poor — retrain from scratch"</span>)

<span style="color:#6b7280;"># Ternary (one-line) conditional expression</span>
<span style="color:#93c5fd;">label</span> = <span style="color:#a7f3d0;">"Pass"</span> <span style="color:#c4b5fd;">if</span> accuracy &gt;= <span style="color:#fcd34d;">0.75</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"Fail"</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Result:"</span>, label)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Acceptable — consider more data
Result: Pass</div>
  </div>
</div>

<h3>Try / Except / Else / Finally — Graceful Error Handling</h3>
<p>Every block serves a different role: <code>try</code> wraps risky code, <code>except</code> catches specific errors, <code>else</code> runs only if <em>no exception occurred</em>, and <code>finally</code> runs <em>always</em> — even if an exception was raised. This is the standard pattern for safe I/O and data parsing.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Full Try/Except Block</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">safe_divide</span>(a, b):
    <span style="color:#c4b5fd;">try</span>:
        <span style="color:#93c5fd;">result</span> = a / b
    <span style="color:#c4b5fd;">except</span> ZeroDivisionError:
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  [ERROR] Cannot divide by zero."</span>)
        <span style="color:#c4b5fd;">return</span> <span style="color:#fca5a5;">None</span>
    <span style="color:#c4b5fd;">except</span> TypeError <span style="color:#c4b5fd;">as</span> e:
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  [ERROR] Wrong type: {e}"</span>)
        <span style="color:#c4b5fd;">return</span> <span style="color:#fca5a5;">None</span>
    <span style="color:#c4b5fd;">else</span>:
        <span style="color:#6b7280;"># Runs ONLY if no exception occurred</span>
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  [OK] Result: {result:.4f}"</span>)
        <span style="color:#c4b5fd;">return</span> result
    <span style="color:#c4b5fd;">finally</span>:
        <span style="color:#6b7280;"># Always runs — cleanup, logging, etc.</span>
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  [LOG] Division attempt complete."</span>)

safe_divide(<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">3</span>)
safe_divide(<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">0</span>)
safe_divide(<span style="color:#fcd34d;">10</span>, <span style="color:#a7f3d0;">"x"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>  [OK] Result: 3.3333
  [LOG] Division attempt complete.
  [ERROR] Cannot divide by zero.
  [LOG] Division attempt complete.
  [ERROR] Wrong type: unsupported operand type(s) for /: 'int' and 'str'
  [LOG] Division attempt complete.</div>
  </div>
</div>

<h3>Match Statements (Python 3.10+)</h3>
<p>The <code>match</code> statement is Python's modern equivalent of a switch statement. It matches a value against a series of patterns and is far more powerful than chained <code>elif</code>s when routing on discrete values like API responses, command codes, or model types.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Match Statement</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">get_model_info</span>(model_type):
    <span style="color:#c4b5fd;">match</span> model_type:
        <span style="color:#c4b5fd;">case</span> <span style="color:#a7f3d0;">"linear"</span>:
            <span style="color:#c4b5fd;">return</span> <span style="color:#a7f3d0;">"Linear Regression — fast, interpretable, low complexity"</span>
        <span style="color:#c4b5fd;">case</span> <span style="color:#a7f3d0;">"tree"</span> | <span style="color:#a7f3d0;">"forest"</span>:
            <span style="color:#c4b5fd;">return</span> <span style="color:#a7f3d0;">"Tree-based — handles non-linearity, robust to outliers"</span>
        <span style="color:#c4b5fd;">case</span> <span style="color:#a7f3d0;">"nn"</span>:
            <span style="color:#c4b5fd;">return</span> <span style="color:#a7f3d0;">"Neural Network — powerful, requires large data & GPU"</span>
        <span style="color:#c4b5fd;">case</span> _:
            <span style="color:#c4b5fd;">return</span> <span style="color:#a7f3d0;">"Unknown model type"</span>

<span style="color:#93c5fd;">print</span>(get_model_info(<span style="color:#a7f3d0;">"linear"</span>))
<span style="color:#93c5fd;">print</span>(get_model_info(<span style="color:#a7f3d0;">"forest"</span>))
<span style="color:#93c5fd;">print</span>(get_model_info(<span style="color:#a7f3d0;">"svm"</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Linear Regression — fast, interpretable, low complexity
Tree-based — handles non-linearity, robust to outliers
Unknown model type</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.8 Advanced Flow: Match & Try/Except',
            'order_index' => 8,
            'content' => $this->appendQuiz($content8, 'L1_8', [
                ['q' => 'Which block always runs in a try/except, even when an error occurs?', 'opts' => ['try', 'except', 'else', 'finally'], 'ans' => 3, 'exp' => 'finally always executes regardless of whether an exception was raised or not. It is used for cleanup like closing files or database connections.'],
                ['q' => 'What exception does Python raise when you divide by zero?', 'opts' => ['MathError', 'ValueError', 'ZeroDivisionError', 'ArithmeticException'], 'ans' => 2, 'exp' => 'Python raises ZeroDivisionError when you attempt division by zero. Always catch this specifically in data pipelines that do calculations.'],
                ['q' => 'When does the "else" block in a try/except run?', 'opts' => ['When any exception is raised', 'Always at the end', 'Only when NO exception occurred', 'Only when TypeError occurs'], 'ans' => 2, 'exp' => 'The else block runs only when the try block completed without raising any exception. It is the "success" path.'],
                ['q' => 'Can you catch multiple different exception types in a single try block?', 'opts' => ['No', 'Yes, with multiple except clauses', 'Only same-type exceptions', 'Only in Python 3.10+'], 'ans' => 1, 'exp' => 'You can have multiple except clauses, each catching a different exception type. Python checks them top to bottom and executes the first matching one.'],
                ['q' => 'In a match statement, what does "case _:" mean?', 'opts' => ['Match nothing', 'Match the string "_"', 'Match private variables', 'The wildcard — matches anything not caught above'], 'ans' => 3, 'exp' => '"case _:" is the wildcard pattern, equivalent to a default case. It matches any value that was not caught by the preceding case patterns.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.9 — Loops
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>The Power of Iteration: Loops</h2>
<p>Loops are how you automate repetition. In data science, you will use loops to process rows in a dataset, train models across multiple epochs, iterate over hyperparameter combinations, and build data pipelines. Python has two primary loop types: <code>for</code> (definite iteration) and <code>while</code> (conditional iteration).</p>

<h3>For Loops & range()</h3>
<p>A <code>for</code> loop iterates over any sequence: list, string, tuple, dict, or a <code>range</code> object. <code>range(start, stop, step)</code> generates a lazy sequence of numbers — it is memory-efficient because it does not create a full list in memory.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — For Loop & range()</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># range(stop) — 0 to stop-1</span>
<span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">5</span>):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Epoch {i+1}/5"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"---"</span>)

<span style="color:#6b7280;"># range(start, stop, step) — count by 2s from 0 to 8</span>
<span style="color:#c4b5fd;">for</span> batch <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">2</span>):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Batch starting at index {batch}"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"---"</span>)

<span style="color:#6b7280;"># Iterating over a list</span>
<span style="color:#93c5fd;">features</span> = [<span style="color:#a7f3d0;">"age"</span>, <span style="color:#a7f3d0;">"income"</span>, <span style="color:#a7f3d0;">"credit_score"</span>]
<span style="color:#c4b5fd;">for</span> feat <span style="color:#c4b5fd;">in</span> features:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Processing feature: {feat}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Epoch 1/5
Epoch 2/5
Epoch 3/5
Epoch 4/5
Epoch 5/5
---
Batch starting at index 0
Batch starting at index 2
Batch starting at index 4
Batch starting at index 6
Batch starting at index 8
---
Processing feature: age
Processing feature: income
Processing feature: credit_score</div>
  </div>
</div>

<h3>enumerate() & zip() — Power Iterators</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — enumerate() & zip()</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">models</span>     = [<span style="color:#a7f3d0;">"LR"</span>, <span style="color:#a7f3d0;">"RF"</span>, <span style="color:#a7f3d0;">"XGB"</span>]
<span style="color:#93c5fd;">accuracies</span> = [<span style="color:#fcd34d;">0.82</span>, <span style="color:#fcd34d;">0.91</span>, <span style="color:#fcd34d;">0.96</span>]

<span style="color:#6b7280;"># enumerate() gives you index AND value — no need for a counter variable</span>
<span style="color:#c4b5fd;">for</span> i, model <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(models, start=<span style="color:#fcd34d;">1</span>):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{i}. {model}"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"---"</span>)

<span style="color:#6b7280;"># zip() pairs elements from two lists together</span>
<span style="color:#c4b5fd;">for</span> model, acc <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(models, accuracies):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{model:5} → {acc:.0%}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>1. LR
2. RF
3. XGB
---
LR    → 82%
RF    → 91%
XGB   → 96%</div>
  </div>
</div>

<h3>While Loops, break & continue</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — While, break, continue</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># While loop — training until loss is low enough</span>
<span style="color:#93c5fd;">loss</span>  = <span style="color:#fcd34d;">1.0</span>
<span style="color:#93c5fd;">epoch</span> = <span style="color:#fcd34d;">0</span>
<span style="color:#c4b5fd;">while</span> loss > <span style="color:#fcd34d;">0.2</span>:
    <span style="color:#93c5fd;">loss</span>  -= <span style="color:#fcd34d;">0.25</span>
    <span style="color:#93c5fd;">epoch</span> += <span style="color:#fcd34d;">1</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Epoch {epoch}: loss = {loss:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Training converged!"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"---"</span>)

<span style="color:#6b7280;"># continue skips to next iteration; break exits loop entirely</span>
<span style="color:#c4b5fd;">for</span> n <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">8</span>):
    <span style="color:#c4b5fd;">if</span> n % <span style="color:#fcd34d;">2</span> == <span style="color:#fcd34d;">0</span>: <span style="color:#c4b5fd;">continue</span>  <span style="color:#6b7280;"># skip even numbers</span>
    <span style="color:#c4b5fd;">if</span> n == <span style="color:#fcd34d;">7</span>:          <span style="color:#c4b5fd;">break</span>    <span style="color:#6b7280;"># stop before 7</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Processing odd: {n}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Epoch 1: loss = 0.75
Epoch 2: loss = 0.50
Epoch 3: loss = 0.25
Training converged!
---
Processing odd: 1
Processing odd: 3
Processing odd: 5</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.9 Loops: For, While, Range & Iterators',
            'order_index' => 9,
            'content' => $this->appendQuiz($content9, 'L1_9', [
                ['q' => 'What does "break" do inside a loop?', 'opts' => ['Skips the current iteration only', 'Exits the loop entirely', 'Pauses the loop', 'Restarts from the beginning'], 'ans' => 1, 'exp' => 'break terminates the entire loop immediately, regardless of the loop condition or remaining iterations.'],
                ['q' => 'What does "continue" do?', 'opts' => ['Exits the loop', 'Skips the rest of the current iteration and moves to the next', 'Continues to the next function', 'Prevents infinite loops'], 'ans' => 1, 'exp' => 'continue skips only the remaining statements in the current iteration and jumps to the next one. The loop itself does not stop.'],
                ['q' => 'What does range(2, 10, 3) generate?', 'opts' => ['2, 4, 6, 8', '2, 5, 8', '2, 5, 8, 11', '3, 6, 9'], 'ans' => 1, 'exp' => 'range(start=2, stop=10, step=3) generates 2, 5, 8. It stops before 10. 2+3=5, 5+3=8, 8+3=11 which is ≥10 so it stops.'],
                ['q' => 'What does enumerate(my_list, start=1) do?', 'opts' => ['Returns only indices', 'Returns only values', 'Returns (index, value) pairs starting the index at 1', 'Returns the length of the list'], 'ans' => 2, 'exp' => 'enumerate() yields (index, value) tuples. The start= parameter sets the starting value of the counter, defaulting to 0.'],
                ['q' => 'What happens if a while loop condition is never False?', 'opts' => ['SyntaxError is raised', 'Python automatically fixes it', 'The loop is skipped', 'An infinite loop runs until crashed or killed'], 'ans' => 3, 'exp' => 'A while loop with a condition that never becomes False runs forever (infinite loop). Always ensure your while loop has a termination condition or a break statement.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.10 — Functions
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Mastering Functions</h2>
<p>A <strong>function</strong> is a named, reusable block of code that performs a specific task. Functions are the single most important tool for writing clean, maintainable, and testable data science code. Every preprocessing pipeline, every model evaluation routine, every custom metric should live in a function.</p>

<h3>Defining Functions: def, Parameters & Return</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Function Basics</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Basic function with a default argument</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">evaluate_model</span>(accuracy, threshold=<span style="color:#fcd34d;">0.90</span>):
    <span style="color:#a7f3d0;">"""Returns a verdict based on accuracy vs threshold."""</span>
    <span style="color:#c4b5fd;">if</span> accuracy &gt;= threshold:
        <span style="color:#c4b5fd;">return</span> <span style="color:#a7f3d0;">f"PASS ({accuracy:.1%} ≥ {threshold:.1%})"</span>
    <span style="color:#c4b5fd;">return</span> <span style="color:#a7f3d0;">f"FAIL ({accuracy:.1%} &lt; {threshold:.1%})"</span>

<span style="color:#6b7280;"># Call with default threshold</span>
<span style="color:#93c5fd;">print</span>(evaluate_model(<span style="color:#fcd34d;">0.95</span>))

<span style="color:#6b7280;"># Override the default threshold</span>
<span style="color:#93c5fd;">print</span>(evaluate_model(<span style="color:#fcd34d;">0.85</span>, threshold=<span style="color:#fcd34d;">0.80</span>))

<span style="color:#6b7280;"># Functions can return multiple values as a tuple</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">summary_stats</span>(data):
    <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">min</span>(data), <span style="color:#93c5fd;">max</span>(data), <span style="color:#93c5fd;">sum</span>(data) / <span style="color:#93c5fd;">len</span>(data)

<span style="color:#93c5fd;">lo</span>, <span style="color:#93c5fd;">hi</span>, <span style="color:#93c5fd;">avg</span> = summary_stats([<span style="color:#fcd34d;">82</span>, <span style="color:#fcd34d;">90</span>, <span style="color:#fcd34d;">67</span>, <span style="color:#fcd34d;">95</span>, <span style="color:#fcd34d;">78</span>])
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Min:{lo}  Max:{hi}  Avg:{avg:.1f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em; display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>PASS (95.0% ≥ 90.0%)
PASS (85.0% ≥ 80.0%)
Min:67  Max:95  Avg:82.4</div>
  </div>
</div>

<h3>*args & **kwargs — Flexible Signatures</h3>
<p><code>*args</code> packs any number of positional arguments into a <em>tuple</em>. <code>**kwargs</code> packs any number of keyword arguments into a <em>dictionary</em>. Together they let you build highly flexible utility functions.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — *args & **kwargs</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># *args — accepts any number of positional arguments</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">weighted_average</span>(*scores):
    <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">sum</span>(scores) / <span style="color:#93c5fd;">len</span>(scores)

<span style="color:#93c5fd;">print</span>(weighted_average(<span style="color:#fcd34d;">88</span>, <span style="color:#fcd34d;">92</span>, <span style="color:#fcd34d;">79</span>))
<span style="color:#93c5fd;">print</span>(weighted_average(<span style="color:#fcd34d;">70</span>, <span style="color:#fcd34d;">85</span>, <span style="color:#fcd34d;">90</span>, <span style="color:#fcd34d;">95</span>, <span style="color:#fcd34d;">88</span>))

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"---"</span>)

<span style="color:#6b7280;"># **kwargs — accepts any number of keyword arguments</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">create_model_card</span>(**kwargs):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Model Card ==="</span>)
    <span style="color:#c4b5fd;">for</span> key, val <span style="color:#c4b5fd;">in</span> kwargs.items():
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {key:12}: {val}"</span>)

create_model_card(name=<span style="color:#a7f3d0;">"BERT"</span>, task=<span style="color:#a7f3d0;">"NLP"</span>, accuracy=<span style="color:#fcd34d;">0.94</span>, params=<span style="color:#a7f3d0;">"110M"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>86.33333333333333
85.6
---
=== Model Card ===
  name        : BERT
  task        : NLP
  accuracy    : 0.94
  params      : 110M</div>
  </div>
</div>

<h3>Lambda Functions & Higher-Order Functions</h3>
<p>A <strong>lambda</strong> is a small, anonymous function defined in one line. They are not replacements for <code>def</code> functions — they are used when you need a <em>throwaway function</em> to pass as an argument to another function, like <code>sorted()</code>, <code>map()</code>, or <code>filter()</code>.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Lambda, map(), filter(), sorted()</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Lambda syntax: lambda args: expression</span>
<span style="color:#93c5fd;">normalize</span> = <span style="color:#c4b5fd;">lambda</span> x, mn, mx: (x - mn) / (mx - mn)
<span style="color:#93c5fd;">print</span>(normalize(<span style="color:#fcd34d;">75</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">100</span>))    <span style="color:#6b7280;"># 0.75</span>

<span style="color:#93c5fd;">results</span> = [
    {<span style="color:#a7f3d0;">"model"</span>: <span style="color:#a7f3d0;">"LR"</span>,  <span style="color:#a7f3d0;">"acc"</span>: <span style="color:#fcd34d;">0.82</span>},
    {<span style="color:#a7f3d0;">"model"</span>: <span style="color:#a7f3d0;">"RF"</span>,  <span style="color:#a7f3d0;">"acc"</span>: <span style="color:#fcd34d;">0.91</span>},
    {<span style="color:#a7f3d0;">"model"</span>: <span style="color:#a7f3d0;">"XGB"</span>, <span style="color:#a7f3d0;">"acc"</span>: <span style="color:#fcd34d;">0.96</span>},
]

<span style="color:#6b7280;"># Sort a list of dicts by accuracy, descending</span>
<span style="color:#93c5fd;">ranked</span> = <span style="color:#93c5fd;">sorted</span>(results, key=<span style="color:#c4b5fd;">lambda</span> r: r[<span style="color:#a7f3d0;">"acc"</span>], reverse=<span style="color:#fca5a5;">True</span>)
<span style="color:#c4b5fd;">for</span> r <span style="color:#c4b5fd;">in</span> ranked:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{r['model']:5} → {r['acc']:.0%}"</span>)

<span style="color:#6b7280;"># map() applies a function to every item in a list</span>
<span style="color:#93c5fd;">raw</span> = [<span style="color:#fcd34d;">0.5</span>, <span style="color:#fcd34d;">1.5</span>, <span style="color:#fcd34d;">-0.3</span>, <span style="color:#fcd34d;">2.1</span>]
<span style="color:#93c5fd;">clipped</span> = <span style="color:#93c5fd;">list</span>(<span style="color:#93c5fd;">map</span>(<span style="color:#c4b5fd;">lambda</span> x: <span style="color:#93c5fd;">max</span>(<span style="color:#fcd34d;">0</span>, <span style="color:#93c5fd;">min</span>(<span style="color:#fcd34d;">1</span>, x)), raw))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Clipped to [0,1]:"</span>, clipped)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>0.75
XGB   → 96%
RF    → 91%
LR    → 82%
Clipped to [0,1]: [0.5, 1, 0, 1]</div>
  </div>
</div>

<h3>Variable Scope: Local vs Global</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Scope</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">threshold</span> = <span style="color:#fcd34d;">0.9</span>    <span style="color:#6b7280;"># Global variable</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">check</span>(acc):
    <span style="color:#93c5fd;">verdict</span> = <span style="color:#a7f3d0;">"pass"</span>   <span style="color:#6b7280;"># Local variable — only exists inside this function</span>
    <span style="color:#c4b5fd;">return</span> verdict <span style="color:#c4b5fd;">if</span> acc &gt;= threshold <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"fail"</span>

<span style="color:#93c5fd;">print</span>(check(<span style="color:#fcd34d;">0.95</span>))  <span style="color:#6b7280;"># "pass" — reads global threshold</span>
<span style="color:#93c5fd;">print</span>(check(<span style="color:#fcd34d;">0.88</span>))  <span style="color:#6b7280;"># "fail"</span>

<span style="color:#6b7280;"># print(verdict)  ← NameError: verdict is not defined outside the function</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>pass
fail</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.10 Functions: Def, Args, Kwargs & Lambdas',
            'order_index' => 10,
            'content' => $this->appendQuiz($content10, 'L1_10', [
                ['q' => 'Which keyword is used to define a function in Python?', 'opts' => ['function', 'def', 'fun', 'define'], 'ans' => 1, 'exp' => '"def" is the keyword used to define a function in Python. Short for "define".'],
                ['q' => 'What does *args allow a function to do?', 'opts' => ['Accept keyword arguments as a dict', 'Accept any number of positional arguments as a tuple', 'Enforce specific argument types', 'Return multiple values'], 'ans' => 1, 'exp' => '*args collects any extra positional arguments into a tuple inside the function. You can pass 2 or 20 arguments — it adapts.'],
                ['q' => 'What data structure does **kwargs pack arguments into?', 'opts' => ['Tuple', 'List', 'Dictionary', 'Set'], 'ans' => 2, 'exp' => '**kwargs packs named (keyword) arguments into a dictionary. Inside the function you can iterate over it with .items() just like any other dict.'],
                ['q' => 'What is a lambda function?', 'opts' => ['A function that calls itself recursively', 'A named function with multiple expressions', 'A small anonymous single-expression function', 'A function defined in a class'], 'ans' => 2, 'exp' => 'A lambda is a nameless, single-expression function. lambda x: x * 2 is equivalent to def f(x): return x * 2, but more concise for short throwaway operations.'],
                ['q' => 'If a variable is defined inside a function, where is it accessible?', 'opts' => ['Everywhere in the script', 'Only inside that function (local scope)', 'In all functions defined after it', 'Only in loops'], 'ans' => 1, 'exp' => 'Variables created inside a function are local — they only exist during the function call and are destroyed when it returns. Accessing them outside raises a NameError.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 1.11 — Final Exam (Org-Locked)
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            // Foundations (1.1)
            ['q' => 'Which parameter of print() changes the separator between multiple printed items?', 'opts' => ['end=', 'sep=', 'delim=', 'join='], 'ans' => 1, 'exp' => 'sep= controls the separator. Default is a space. Example: print("a","b", sep="-") outputs "a-b".'],
            ['q' => 'What type does input() always return?', 'opts' => ['int', 'float', 'str', 'bool'], 'ans' => 2, 'exp' => 'input() always returns a string, even if the user types a number. Always cast it explicitly.'],
            ['q' => 'Which of the following is a valid Python comment?', 'opts' => ['// this is a comment', '/* comment */', '# this is a comment', '-- comment'], 'ans' => 2, 'exp' => 'Python uses # for single-line comments. The others belong to C-family languages (//,/* */) or SQL (--)'],
            // Variables (1.2)
            ['q' => 'What is the result of bool(0)?', 'opts' => ['True', 'False', 'None', 'Error'], 'ans' => 1, 'exp' => 'Zero, empty strings, and empty collections evaluate to False in Python. Everything else is truthy.'],
            ['q' => 'int(-4.9) returns?', 'opts' => ['-5', '-4', '4', 'Error'], 'ans' => 1, 'exp' => 'int() truncates toward zero. -4.9 becomes -4, not -5.'],
            // Strings (1.3)
            ['q' => '"DataSensei"[4:] returns?', 'opts' => ['Data', 'Sensei', 'DataS', 'ensei'], 'ans' => 1, 'exp' => 'Index 4 is "S". Slicing from 4 to end gives "Sensei".'],
            ['q' => 'Which method joins a list of strings into one string?', 'opts' => ['concat()', 'merge()', '",".join(list)', 'append()'], 'ans' => 2, 'exp' => 'The join() method is called on the separator string and takes the iterable as argument: "-".join(["a","b","c"]) → "a-b-c"'],
            ['q' => 'What does "hello".upper() return?', 'opts' => ['hello', 'HELLO', 'Hello', 'hELLO'], 'ans' => 1, 'exp' => 'upper() converts all characters to uppercase. The original string is unchanged (strings are immutable).'],
            // Operators (1.4)
            ['q' => '2 ** 8 equals?', 'opts' => ['16', '64', '256', '512'], 'ans' => 2, 'exp' => '** is exponentiation. 2 to the power of 8 = 256.'],
            ['q' => 'What does "not True" evaluate to?', 'opts' => ['True', 'False', 'None', 'Error'], 'ans' => 1, 'exp' => '"not" negates a boolean. not True = False.'],
            // Lists (1.5)
            ['q' => 'Which method removes the first occurrence of a value from a list?', 'opts' => ['delete()', 'pop()', 'remove()', 'discard()'], 'ans' => 2, 'exp' => 'remove(value) removes the first occurrence of that value. pop(index) removes by index and returns the removed item.'],
            ['q' => '[x**2 for x in range(4)] produces?', 'opts' => ['[1,4,9,16]', '[0,1,4,9]', '[0,2,4,6]', '[1,2,3,4]'], 'ans' => 1, 'exp' => 'range(4) = 0,1,2,3. Squaring each: 0,1,4,9. So the result is [0,1,4,9].'],
            ['q' => 'What does list.sort() do?', 'opts' => ['Returns a sorted copy', 'Sorts the list in-place and returns None', 'Reverses the list', 'Returns the minimum'], 'ans' => 1, 'exp' => 'sort() modifies the list IN PLACE and returns None. Use sorted(list) if you want a new sorted copy.'],
            // Tuples & Sets (1.6)
            ['q' => 'Which collection type is unordered and only stores unique values?', 'opts' => ['List', 'Tuple', 'Set', 'Dictionary'], 'ans' => 2, 'exp' => 'Sets are unordered collections of unique values. Duplicates are silently ignored.'],
            ['q' => 'What symbol separates items in a set literal?', 'opts' => ['()', '[]', '{}', '<>'], 'ans' => 2, 'exp' => 'Sets use curly braces: {1, 2, 3}. Note: {} alone creates an empty dict, not a set — use set() for an empty set.'],
            // Dictionaries (1.7)
            ['q' => 'What does dict.keys() return?', 'opts' => ['A list of values', 'A list of (k,v) pairs', 'A view of all keys', 'The number of keys'], 'ans' => 2, 'exp' => '.keys() returns a view object of the dictionary\'s keys. Convert to list with list(dict.keys()) if needed.'],
            ['q' => 'How do you add or update a key in a dictionary named d?', 'opts' => ['d.add("key", val)', 'd.insert("key", val)', 'd["key"] = val', 'd.set("key", val)'], 'ans' => 2, 'exp' => 'Simply assign with bracket notation: d["key"] = value. If the key exists, it updates it. If not, it creates it.'],
            ['q' => 'Which is the safest way to access a potentially missing key?', 'opts' => ['d["key"]', 'd.get("key", default)', 'd.fetch("key")', 'd.find("key")'], 'ans' => 1, 'exp' => '.get(key, default) never raises KeyError. If the key is missing, it returns the default (or None if no default is given).'],
            // Try/Except (1.8)
            ['q' => 'What block runs regardless of whether an exception was raised?', 'opts' => ['try', 'except', 'else', 'finally'], 'ans' => 3, 'exp' => 'finally always runs — whether an exception occurred or not. Used for cleanup like closing file handles or DB connections.'],
            ['q' => 'Which exception is raised by int("hello")?', 'opts' => ['TypeError', 'ValueError', 'SyntaxError', 'NameError'], 'ans' => 1, 'exp' => 'int("hello") raises a ValueError because "hello" cannot be converted to an integer. TypeError would occur from a type mismatch.'],
            // Loops (1.9)
            ['q' => 'What does zip([1,2,3], ["a","b","c"]) produce?', 'opts' => ['A list of sums', 'A list of (1,"a"),(2,"b"),(3,"c") pairs', 'Two separate lists', 'A dictionary'], 'ans' => 1, 'exp' => 'zip() pairs up elements from two or more iterables. Each iteration yields a tuple with one element from each iterable.'],
            ['q' => 'What does enumerate(["a","b","c"], start=1) produce?', 'opts' => ['[1,2,3]', '[(1,"a"),(2,"b"),(3,"c")]', '["a","b","c"]', 'A dict {1:"a",...}'], 'ans' => 1, 'exp' => 'enumerate() yields (index, value) pairs. start=1 begins the counter at 1 instead of the default 0.'],
            // Functions (1.10)
            ['q' => 'What does a function return if it has no return statement?', 'opts' => ['0', 'False', 'None', 'Error'], 'ans' => 2, 'exp' => 'A function without a return statement implicitly returns None — Python\'s null value.'],
            ['q' => 'A default argument value is used when...', 'opts' => ['Always', 'The caller omits that argument', 'The caller passes None', 'Only in lambda functions'], 'ans' => 1, 'exp' => 'Default argument values are used when the caller does not supply that argument. Example: def f(x, n=10): — calling f(5) uses n=10 by default.'],
            ['q' => 'Which built-in applies a function to every element in a list?', 'opts' => ['apply()', 'each()', 'map()', 'transform()'], 'ans' => 2, 'exp' => 'map(func, iterable) applies func to each element lazily. Wrap in list() to get a concrete list: list(map(lambda x: x*2, [1,2,3]))'],
            // Mixed comprehension
            ['q' => '{k: v*2 for k,v in {"a":1,"b":2}.items()} produces?', 'opts' => ['{"a":1,"b":2}', '{"a":2,"b":4}', '[2,4]', 'Error'], 'ans' => 1, 'exp' => 'Dictionary comprehension doubles each value. {"a":1*2, "b":2*2} = {"a":2,"b":4}.'],
            ['q' => 'What is the output of print(type(3.0))?', 'opts' => ["<class 'int'>", "<class 'float'>", "<class 'double'>", "<class 'number'>"], 'ans' => 1, 'exp' => '3.0 has a decimal point, so Python classifies it as float, not int.'],
            ['q' => 'Which statement correctly swaps x and y in Python?', 'opts' => ['temp=x; x=y; y=temp', 'x,y = y,x', 'swap(x,y)', 'x=y; y=x'], 'ans' => 1, 'exp' => 'Python\'s tuple unpacking allows elegant simultaneous assignment: x, y = y, x swaps without a temp variable.'],
            ['q' => 'What is the time complexity of "key" in my_dict?', 'opts' => ['O(n)', 'O(log n)', 'O(n²)', 'O(1)'], 'ans' => 3, 'exp' => 'Dictionary key lookups are O(1) — constant time, regardless of dictionary size. This is one of the most important performance properties of dicts.'],
            ['q' => 'Which of these creates an empty set correctly?', 'opts' => ['{}', 'set()', '[]', '()'], 'ans' => 1, 'exp' => '{} creates an empty DICTIONARY, not an empty set. To create an empty set you must use the set() constructor.'],
        ];

        $finalContent  = <<<HTML
<div id="org-lock-screen" style="text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;">
    <div style="font-size:3rem;margin-bottom:1rem;">🔒</div>
    <h3 style="color:var(--text);margin-bottom:0.5rem;">University / Organization Access Only</h3>
    <p style="color:var(--muted);">The Final Module Exam is restricted to enrolled students and verified organization members.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 1: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 1.1 through 1.10 — syntax, variables, strings, operators, collections, control flow, loops, and functions. Good luck!</p>
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
            'module_id'   => $pythonModule->id,
            'title'       => '1.11 Final Exam: Python Mastery',
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