<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\User;

class CurriculumSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Lesson::truncate();
        Module::truncate();
        Schema::enableForeignKeyConstraints();

        $curriculum = [
            ['title' => 'Basics of Python Programming', 'year' => 'Year 1'],
            ['title' => 'Basics of Statistics', 'year' => 'Year 1'],
            ['title' => 'Introduction to Data Science', 'year' => 'Year 1'],
            ['title' => 'Mathematical Analysis I', 'year' => 'Year 1'],
            ['title' => 'Methods of Proof', 'year' => 'Year 1'],
            ['title' => 'Modeling and Simulation', 'year' => 'Year 1'], 
            ['title' => 'Algorithms & Data Structures for Data Scientists', 'year' => 'Year 2'],
            ['title' => 'Statistical Methods & Experimental Design', 'year' => 'Year 2'],
            ['title' => 'Applied Matrix Analysis', 'year' => 'Year 2'],
            ['title' => 'Database Management for Data Science', 'year' => 'Year 2'],
            ['title' => 'Introduction to Bayesian Data Analysis', 'year' => 'Year 2'],
            ['title' => 'Introductory Forecasting', 'year' => 'Year 2'],
            ['title' => 'Introduction to Optimization Techniques', 'year' => 'Year 3'],
            ['title' => 'Machine Learning 1: Supervised Learning', 'year' => 'Year 3'],
            ['title' => 'Data Visualization', 'year' => 'Year 3'],
            ['title' => 'Multivariate Analysis', 'year' => 'Year 3'],
            ['title' => 'Deep Learning', 'year' => 'Year 3'],
            ['title' => 'Privacy, Ethics & Data Governance', 'year' => 'Year 3'],
            ['title' => 'Introduction to Artificial Intelligence', 'year' => 'Year 4'],
            ['title' => 'Analysis of Unstructured Data', 'year' => 'Year 4'],
            ['title' => 'Machine Learning 2: Unsupervised Learning', 'year' => 'Year 4'],
            ['title' => 'Big Data & Cloud Computing', 'year' => 'Year 4'],
            ['title' => 'Data Warehousing', 'year' => 'Year 4'],
            ['title' => 'Sequential Decision Making', 'year' => 'Year 4'],
        ];

        $createdModules = [];
        $order = 1;

        foreach ($curriculum as $index => $data) {
            $isBoss = (($index + 1) % 6 === 0); 
            $xp = $isBoss ? 2500 : 1000; 

            $createdModules[] = Module::create([
                'title' => $data['title'],
                'description' => 'Master the core concepts of ' . $data['title'] . ' through rigorous interactive modules.',
                'xp_reward' => $xp,
                'year_level' => $data['year'],
                'is_boss' => $isBoss,
                'order_index' => $order++
            ]);
        }

        $pythonModule = $createdModules[0]; 

        // ---------------------------------------------------------
        // LESSON 1.1: Foundations
        // ---------------------------------------------------------
        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.1 Foundations: Syntax, Output, Comments',
            'order_index' => 1,
            'content' => <<<'HTML'
                <h2>Python Foundations</h2>
                <p>Python is an interpreted, high-level, general-purpose programming language. Let's start with getting data in and out of your scripts.</p>

                <h3>Output: The <code>print()</code> Function</h3>
                <p>The <code>print()</code> function outputs data to the standard output device (your screen/console).</p>
                
                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
<span style="color: #6b7280;"># Basic string output</span>
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Hello, Data Scientist!"</span>)

<span style="color: #6b7280;"># Printing multiple items</span>
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Model Accuracy:"</span>, <span style="color: #fcd34d;">98.5</span>, <span style="color: #a7f3d0;">"%"</span>)
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
Hello, Data Scientist!
Model Accuracy: 98.5 %
</div>
                    </div>
                </div>

                <h3>User Input</h3>
                <p>You can pause execution and wait for the user to type something using the <code>input()</code> function.</p>
                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
<span style="color: #93c5fd;">username</span> = <span style="color: #93c5fd;">input</span>(<span style="color: #a7f3d0;">"Enter your username: "</span>)
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Welcome,"</span>, username)
</div>
                    </div>
                </div>
HTML
        ]);

        // ---------------------------------------------------------
        // LESSON 1.2: Variables
        // ---------------------------------------------------------
        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.2 Memory: Variables, Types, Casting',
            'order_index' => 2,
            'content' => <<<'HTML'
                <h2>Data & System Memory</h2>
                <p>Variables are containers for storing data values. Python assigns types dynamically based on what you put inside the variable.</p>

                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
<span style="color: #93c5fd;">temperature</span> = <span style="color: #fcd34d;">98.6</span>        <span style="color: #6b7280;"># Float (float): Decimal numbers</span>
<span style="color: #93c5fd;">name</span> = <span style="color: #a7f3d0;">"Louis"</span>            <span style="color: #6b7280;"># String (str): Text</span>
<span style="color: #93c5fd;">is_active</span> = <span style="color: #fca5a5;">True</span>          <span style="color: #6b7280;"># Boolean (bool): True or False</span>

<span style="color: #93c5fd;">print</span>(<span style="color: #93c5fd;">type</span>(temperature))
<span style="color: #93c5fd;">print</span>(<span style="color: #93c5fd;">type</span>(name))
<span style="color: #93c5fd;">print</span>(<span style="color: #93c5fd;">type</span>(is_active))
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
&lt;class 'float'&gt;
&lt;class 'str'&gt;
&lt;class 'bool'&gt;
</div>
                    </div>
                </div>

                <h3>Type Casting</h3>
                <p>Sometimes you need to enforce a specific type. You do this using casting functions like <code>int()</code>, <code>float()</code>, or <code>str()</code>.</p>
                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
y = <span style="color: #93c5fd;">int</span>(<span style="color: #fcd34d;">2.8</span>)     <span style="color: #6b7280;"># Decimals are truncated, NOT rounded</span>
b = <span style="color: #93c5fd;">str</span>(<span style="color: #fcd34d;">3.0</span>)     

<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"y is:"</span>, y)
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"b is a string:"</span>, b + <span style="color: #a7f3d0;">" points"</span>)
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
y is: 2
b is a string: 3.0 points
</div>
                    </div>
                </div>
HTML
        ]);

        // ---------------------------------------------------------
        // LESSON 1.3: Strings
        // ---------------------------------------------------------
        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.3 Text Processing: Strings & Formatting',
            'order_index' => 3,
            'content' => <<<'HTML'
                <h2>Deep Dive: Strings</h2>
                <p>Strings in Python are essentially arrays of characters. Because they are arrays, you can loop through them, slice them, and manipulate them extensively.</p>

                <h3>String Slicing <code>[start:stop:step]</code></h3>
                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
<span style="color: #93c5fd;">text</span> = <span style="color: #a7f3d0;">"Data Science"</span>

<span style="color: #93c5fd;">print</span>(text[<span style="color: #fcd34d;">0:4</span>])     <span style="color: #6b7280;"># Starts at 0, STOPS before 4</span>
<span style="color: #93c5fd;">print</span>(text[<span style="color: #fcd34d;">-7:</span>])     <span style="color: #6b7280;"># Negative index counts from the end</span>
<span style="color: #93c5fd;">print</span>(text[<span style="color: #fcd34d;">::-1</span>])    <span style="color: #6b7280;"># Reverses a string!</span>
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
Data
Science
ecneicS ataD
</div>
                    </div>
                </div>

                <h3>String Methods & F-Strings</h3>
                <p>String methods return <strong>new</strong> values. They do not change the original string. F-Strings (Python 3.6+) are the modern way to inject variables into text.</p>
                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
<span style="color: #93c5fd;">dirty_data</span> = <span style="color: #a7f3d0;">"   Machine Learning, AI   "</span>
<span style="color: #93c5fd;">clean</span> = dirty_data.strip().upper()

<span style="color: #93c5fd;">print</span>(clean.replace(<span style="color: #a7f3d0;">"AI"</span>, <span style="color: #a7f3d0;">"ARTIFICIAL INTELLIGENCE"</span>))
<span style="color: #93c5fd;">print</span>(clean.split(<span style="color: #a7f3d0;">","</span>))

<span style="color: #6b7280;"># F-Strings allow inline logic</span>
<span style="color: #93c5fd;">params</span> = <span style="color: #fcd34d;">1.76</span>
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">f"GPT-4 has {params * 1000} billion parameters."</span>)
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
MACHINE LEARNING, ARTIFICIAL INTELLIGENCE
['MACHINE LEARNING', ' AI']
GPT-4 has 1760.0 billion parameters.
</div>
                    </div>
                </div>
HTML
        ]);

        // ---------------------------------------------------------
        // LESSON 1.4: Logic
        // ---------------------------------------------------------
        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.4 Logic: Booleans & Operators',
            'order_index' => 4,
            'content' => <<<'HTML'
                <h2>Booleans & Operators</h2>
                <p>You can evaluate any expression in Python and get a Boolean result: <code>True</code> or <code>False</code>.</p>

                <h3>Arithmetic Operators</h3>
                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
x = <span style="color: #fcd34d;">10</span>
y = <span style="color: #fcd34d;">3</span>

<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Float Division:"</span>, x / y)
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Floor Division:"</span>, x // y)
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Modulo (Remainder):"</span>, x % y)
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Exponentiation:"</span>, x ** y)
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
Float Division: 3.3333333333333335
Floor Division: 3
Modulo (Remainder): 1
Exponentiation: 1000
</div>
                    </div>
                </div>

                <h3>Logical, Identity, & Membership Operators</h3>
                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
<span style="color: #6b7280;"># Logical (and, or, not)</span>
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Logical AND:"</span>, <span style="color: #fcd34d;">10</span> > <span style="color: #fcd34d;">5</span> <span style="color: #c4b5fd;">and</span> <span style="color: #fcd34d;">10</span> &lt; <span style="color: #fcd34d;">50</span>)

<span style="color: #6b7280;"># Identity (is, is not) - Compares memory location!</span>
a = [<span style="color: #a7f3d0;">"apple"</span>]
b = [<span style="color: #a7f3d0;">"apple"</span>]
c = a
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Is exact object?:"</span>, a <span style="color: #c4b5fd;">is</span> c)
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Is exact object?:"</span>, a <span style="color: #c4b5fd;">is</span> b) <span style="color: #6b7280;"># False, 'b' is a new object in memory</span>

<span style="color: #6b7280;"># Membership (in, not in)</span>
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"In list?:"</span>, <span style="color: #a7f3d0;">"apple"</span> <span style="color: #c4b5fd;">in</span> a)
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
Logical AND: True
Is exact object?: True
Is exact object?: False
In list?: True
</div>
                    </div>
                </div>
HTML
        ]);

        // ---------------------------------------------------------
        // LESSON 1.5: Lists
        // ---------------------------------------------------------
        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.5 Collections I: Lists & Arrays',
            'order_index' => 5,
            'content' => <<<'HTML'
                <h2>Lists (Arrays)</h2>
                <p>Lists are ordered, changeable (mutable), and allow duplicate values.</p>

                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
<span style="color: #93c5fd;">models</span> = [<span style="color: #a7f3d0;">"Linear"</span>, <span style="color: #a7f3d0;">"Logistics"</span>, <span style="color: #a7f3d0;">"SVM"</span>]

<span style="color: #6b7280;"># Adding Items</span>
models.append(<span style="color: #a7f3d0;">"Random Forest"</span>)

<span style="color: #6b7280;"># Removing Items</span>
models.remove(<span style="color: #a7f3d0;">"SVM"</span>)
popped = models.pop() <span style="color: #6b7280;"># Removes the last item</span>

<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Popped:"</span>, popped)
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Final List:"</span>, models)
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
Popped: Random Forest
Final List: ['Linear', 'Logistics']
</div>
                    </div>
                </div>

                <h3>List Comprehension (Python Magic)</h3>
                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
<span style="color: #93c5fd;">prices</span> = [<span style="color: #fcd34d;">100</span>, <span style="color: #fcd34d;">250</span>, <span style="color: #fcd34d;">50</span>, <span style="color: #fcd34d;">400</span>]

<span style="color: #6b7280;"># Syntax: [expression for item in iterable if condition]</span>
<span style="color: #93c5fd;">taxed_prices</span> = [p * <span style="color: #fcd34d;">1.12</span> <span style="color: #c4b5fd;">for</span> p <span style="color: #c4b5fd;">in</span> prices]
<span style="color: #93c5fd;">expensive</span> = [p <span style="color: #c4b5fd;">for</span> p <span style="color: #c4b5fd;">in</span> prices <span style="color: #c4b5fd;">if</span> p > <span style="color: #fcd34d;">100</span>]

<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Taxed:"</span>, taxed_prices)
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Expensive:"</span>, expensive)
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
Taxed: [112.0, 280.0, 56.0, 448.0]
Expensive: [250, 400]
</div>
                    </div>
                </div>
HTML
        ]);

        // ---------------------------------------------------------
        // LESSON 1.6: Tuples & Sets
        // ---------------------------------------------------------
        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.6 Collections II: Tuples & Sets',
            'order_index' => 6,
            'content' => <<<'HTML'
                <h2>Tuples & Sets</h2>
                <p>Tuples are <strong>immutable</strong> lists. Sets are <strong>unique</strong>, unindexed collections.</p>
                
                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
<span style="color: #6b7280;"># Unpacking a Tuple</span>
<span style="color: #93c5fd;">fruits</span> = (<span style="color: #a7f3d0;">"apple"</span>, <span style="color: #a7f3d0;">"banana"</span>, <span style="color: #a7f3d0;">"cherry"</span>, <span style="color: #a7f3d0;">"strawberry"</span>)
(green, yellow, *red) = fruits

<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Green:"</span>, green)
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Red List:"</span>, red)

<span style="color: #6b7280;"># The instant duplicate remover trick</span>
<span style="color: #93c5fd;">messy_list</span> = [<span style="color: #fcd34d;">1</span>, <span style="color: #fcd34d;">1</span>, <span style="color: #fcd34d;">2</span>, <span style="color: #fcd34d;">3</span>, <span style="color: #fcd34d;">3</span>]
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Cleaned List:"</span>, <span style="color: #93c5fd;">list</span>(<span style="color: #93c5fd;">set</span>(messy_list)))
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
Green: apple
Red List: ['cherry', 'strawberry']
Cleaned List: [1, 2, 3]
</div>
                    </div>
                </div>
HTML
        ]);

        // ---------------------------------------------------------
        // LESSON 1.7: Dictionaries
        // ---------------------------------------------------------
        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.7 Collections III: Dictionaries',
            'order_index' => 7,
            'content' => <<<'HTML'
                <h2>Dictionaries (Key:Value Maps)</h2>
                <p>Dictionaries are used to store data values in <code>key:value</code> pairs.</p>

                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
<span style="color: #93c5fd;">car</span> = {
  <span style="color: #a7f3d0;">"brand"</span>: <span style="color: #a7f3d0;">"Ford"</span>,
  <span style="color: #a7f3d0;">"model"</span>: <span style="color: #a7f3d0;">"Mustang"</span>
}

<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Color:"</span>, car.get(<span style="color: #a7f3d0;">"color"</span>, <span style="color: #a7f3d0;">"Red (Fallback)"</span>))

car.update({<span style="color: #a7f3d0;">"year"</span>: <span style="color: #fcd34d;">2020</span>})

<span style="color: #c4b5fd;">for</span> key, value <span style="color: #c4b5fd;">in</span> car.items():
    <span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">f"{key.capitalize()}: {value}"</span>)
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
Color: Red (Fallback)
Brand: Ford
Model: Mustang
Year: 2020
</div>
                    </div>
                </div>
HTML
        ]);

        // ---------------------------------------------------------
        // LESSON 1.8: Match & Try/Except
        // ---------------------------------------------------------
        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.8 Advanced Flow: Match & Try/Except',
            'order_index' => 8,
            'content' => <<<'HTML'
                <h2>Exception Handling</h2>
                <p>To prevent your data scripts from crashing when they hit bad data, use `try...except` blocks.</p>
                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
<span style="color: #c4b5fd;">try</span>:
    <span style="color: #93c5fd;">print</span>(<span style="color: #fcd34d;">10</span> / <span style="color: #fcd34d;">0</span>)
<span style="color: #c4b5fd;">except</span> ZeroDivisionError:
    <span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"You can't divide by zero!"</span>)
<span style="color: #c4b5fd;">finally</span>:
    <span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Cleanup complete."</span>)
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
You can't divide by zero!
Cleanup complete.
</div>
                    </div>
                </div>
HTML
        ]);

        // ---------------------------------------------------------
        // LESSON 1.9: Loops
        // ---------------------------------------------------------
        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.9 Loops: For, While, Range & Iterators',
            'order_index' => 9,
            'content' => <<<'HTML'
                <h2>The Power of Iteration</h2>
                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
<span style="color: #6b7280;"># While Loop with 'continue' and 'break'</span>
<span style="color: #93c5fd;">i</span> = <span style="color: #fcd34d;">0</span>
<span style="color: #c4b5fd;">while</span> i &lt; <span style="color: #fcd34d;">4</span>:
    <span style="color: #93c5fd;">i</span> += <span style="color: #fcd34d;">1</span>
    <span style="color: #c4b5fd;">if</span> i == <span style="color: #fcd34d;">2</span>: <span style="color: #c4b5fd;">continue</span>
    <span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"While count:"</span>, i)

<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"---"</span>)

<span style="color: #6b7280;"># Range syntax: range(start, stop, step)</span>
<span style="color: #c4b5fd;">for</span> x <span style="color: #c4b5fd;">in</span> <span style="color: #93c5fd;">range</span>(<span style="color: #fcd34d;">0</span>, <span style="color: #fcd34d;">10</span>, <span style="color: #fcd34d;">3</span>):
    <span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"For range:"</span>, x)
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
While count: 1
While count: 3
While count: 4
---
For range: 0
For range: 3
For range: 6
For range: 9
</div>
                    </div>
                </div>
HTML
        ]);

        // ---------------------------------------------------------
        // LESSON 1.10: Functions
        // ---------------------------------------------------------
        Lesson::create([
            'module_id' => $pythonModule->id,
            'title' => '1.10 Functions: Def, Args, Kwargs & Lambdas',
            'order_index' => 10,
            'content' => <<<'HTML'
                <h2>Mastering Functions</h2>
                
                <h3>Arbitrary & Keyword Arguments</h3>
                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
<span style="color: #c4b5fd;">def</span> <span style="color: #fbcfe8;">calc_sum</span>(*args):
    <span style="color: #c4b5fd;">return</span> <span style="color: #93c5fd;">sum</span>(args)

<span style="color: #c4b5fd;">def</span> <span style="color: #fbcfe8;">build_profile</span>(**kwargs):
    <span style="color: #c4b5fd;">return</span> kwargs

<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Sum:"</span>, calc_sum(<span style="color: #fcd34d;">10</span>, <span style="color: #fcd34d;">20</span>, <span style="color: #fcd34d;">30</span>))
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Profile:"</span>, build_profile(name=<span style="color: #a7f3d0;">"Louis"</span>, role=<span style="color: #a7f3d0;">"Admin"</span>))
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
Sum: 60
Profile: {'name': 'Louis', 'role': 'Admin'}
</div>
                    </div>
                </div>

                <h3>Lambda Functions</h3>
                <div class="code-window" style="background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); margin-bottom: 32px; overflow: hidden;">
                    <div style="background: rgba(0,0,0,0.2); padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace;">PYTHON</span>
                        <button onclick="launchIDE(this)" style="background: var(--accent); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">Try in Compiler &rarr;</button>
                    </div>
                    <div style="padding: 16px;">
                        <div class="code-content" style="color: #e5e7eb; padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;">
<span style="color: #93c5fd;">multiply</span> = <span style="color: #c4b5fd;">lambda</span> a, b : a * b
<span style="color: #93c5fd;">print</span>(<span style="color: #a7f3d0;">"Lambda result:"</span>, multiply(<span style="color: #fcd34d;">5</span>, <span style="color: #fcd34d;">6</span>))
</div>
                        <div style="color: #9ca3af; font-size: 0.85rem; overflow-x: auto; white-space: pre; font-family: 'JetBrains Mono', monospace;">
<span style="color: var(--dim); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 8px; font-family: 'Inter', sans-serif; font-weight: 600;">Console Output</span>
Lambda result: 30
</div>
                    </div>
                </div>
HTML
        ]);

        $user = User::first();

        if ($user) {
            $user->update(['xp' => 100, 'streak' => 1]);
            // Purposely unmarking all lessons so user starts fresh
            $user->completedLessons()->detach();
        }
    }
}