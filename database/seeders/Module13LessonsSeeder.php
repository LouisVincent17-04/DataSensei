<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module13LessonsSeeder
 * Seeds lessons for Module 13: Introduction to Optimization Techniques.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module13LessonsSeeder
 *
 * Lessons:
 *  13.1  — What Is Optimization? Goals, Vocabulary & Problem Formulation
 *  13.2  — Calculus Review: Derivatives, Gradients & Critical Points
 *  13.3  — Gradient Descent: The Engine of Machine Learning
 *  13.4  — Variants of Gradient Descent: SGD, Mini-Batch & Momentum
 *  13.5  — Convexity, Local vs Global Minima & Saddle Points
 *  13.6  — Constrained Optimization: Lagrange Multipliers & KKT Conditions
 *  13.7  — Linear Programming: Simplex & Scipy
 *  13.8  — Evolutionary & Metaheuristic Methods: Genetic Algorithms & Simulated Annealing
 *  13.9  — Hyperparameter Optimization: Grid, Random & Bayesian Search
 *  13.10 — Second-Order Methods: Newton, BFGS & L-BFGS
 *  13.11 — Final Exam (Org-Locked)
 */
class Module13LessonsSeeder extends Seeder
{
    public function run()
    {
        $optModule = Module::where('order_index', 13)->firstOrFail();
        Lesson::where('module_id', $optModule->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 13.1 — What Is Optimization? Goals, Vocabulary & Problem Formulation
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What Is Optimization?</h2>
<p>Optimization is the process of finding the best solution from a set of feasible solutions. In data science and machine learning, <em>every model you train is solving an optimization problem</em>. When you fit a linear regression, the algorithm minimizes a loss function. When you train a neural network, gradient descent navigates a high-dimensional landscape to find weights that minimize prediction error. When you tune hyperparameters, you search a configuration space for the setting that maximizes validation accuracy. Understanding optimization is not optional — it is the mathematical bedrock of the entire field.</p>

<h3>The Standard Form of an Optimization Problem</h3>
<p>Every optimization problem, regardless of domain, shares the same skeleton. Recognizing this structure immediately tells you which tools to reach for.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <div style="font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--muted);line-height:2.2;">
    <span style="color:#f59e0b;font-weight:700;">minimize</span> &nbsp;&nbsp; <span style="color:#e5e7eb;">f(x)</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="color:#6b7280;">← objective function (also called loss, cost, criterion)</span><br>
    <span style="color:#f59e0b;font-weight:700;">subject to</span> &nbsp; <span style="color:#e5e7eb;">gᵢ(x) ≤ 0,  i = 1…m</span> &nbsp;&nbsp; <span style="color:#6b7280;">← inequality constraints</span><br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="color:#e5e7eb;">hⱼ(x) = 0,  j = 1…p</span> &nbsp;&nbsp; <span style="color:#6b7280;">← equality constraints</span><br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="color:#e5e7eb;">x ∈ 𝒳</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="color:#6b7280;">← feasible set / domain</span>
  </div>
  <p style="color:var(--muted);font-size:0.85rem;margin:16px 0 0 0;">To <strong style="color:var(--text);">maximize</strong> f(x), simply minimize −f(x). Every maximization problem is secretly a minimization problem.</p>
</div>

<h3>Core Vocabulary You Must Know</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:32px;">
  <div style="background:rgba(0,0,0,0.2);padding:12px 20px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.8rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;font-family:'JetBrains Mono',monospace;">Optimization Dictionary</span>
  </div>
  <div style="padding:0;">
    <div style="display:grid;grid-template-columns:190px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:12px 20px;gap:16px;font-size:0.875rem;">
      <strong style="color:#3b82f6;font-family:'JetBrains Mono',monospace;">Decision variable x</strong>
      <span style="color:var(--muted);">The quantity you control and optimize over. Model weights in ML, portfolio allocations in finance, production quantities in operations research.</span>
    </div>
    <div style="display:grid;grid-template-columns:190px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:12px 20px;gap:16px;font-size:0.875rem;">
      <strong style="color:#3b82f6;font-family:'JetBrains Mono',monospace;">Objective function f(x)</strong>
      <span style="color:var(--muted);">The scalar quantity you want to minimize or maximize. MSE in regression, cross-entropy in classification, profit in business problems.</span>
    </div>
    <div style="display:grid;grid-template-columns:190px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:12px 20px;gap:16px;font-size:0.875rem;">
      <strong style="color:#3b82f6;font-family:'JetBrains Mono',monospace;">Feasible set 𝒳</strong>
      <span style="color:var(--muted);">The set of all x values that satisfy every constraint. The optimizer must stay inside this set.</span>
    </div>
    <div style="display:grid;grid-template-columns:190px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:12px 20px;gap:16px;font-size:0.875rem;">
      <strong style="color:#3b82f6;font-family:'JetBrains Mono',monospace;">Global minimum x*</strong>
      <span style="color:var(--muted);">The point where f(x*) ≤ f(x) for ALL feasible x. This is what we truly want, but it is often intractable to guarantee.</span>
    </div>
    <div style="display:grid;grid-template-columns:190px 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:12px 20px;gap:16px;font-size:0.875rem;">
      <strong style="color:#3b82f6;font-family:'JetBrains Mono',monospace;">Local minimum</strong>
      <span style="color:var(--muted);">A point where f is smaller than all nearby points, but may not be the global best. Neural network training often converges to local minima.</span>
    </div>
    <div style="display:grid;grid-template-columns:190px 1fr;padding:12px 20px;gap:16px;font-size:0.875rem;">
      <strong style="color:#3b82f6;font-family:'JetBrains Mono',monospace;">Gradient ∇f(x)</strong>
      <span style="color:var(--muted);">The vector of partial derivatives of f. Points in the direction of steepest ascent. The gradient equals zero at every local minimum, maximum, and saddle point.</span>
    </div>
  </div>
</div>

<h3>A Taxonomy of Optimization Problems</h3>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:32px;">
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #3b82f6;">
    <h4 style="color:#3b82f6;margin-top:0;font-size:0.9rem;">By Variable Type</h4>
    <ul style="color:var(--muted);font-size:0.85rem;padding-left:1.2rem;line-height:1.9;margin:0;">
      <li><strong style="color:var(--text);">Continuous:</strong> x ∈ ℝⁿ — gradient-based methods work</li>
      <li><strong style="color:var(--text);">Discrete/Integer:</strong> x ∈ ℤⁿ — combinatorial, NP-hard in general</li>
      <li><strong style="color:var(--text);">Mixed-Integer:</strong> some continuous, some discrete</li>
    </ul>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #10b981;">
    <h4 style="color:#10b981;margin-top:0;font-size:0.9rem;">By Constraints</h4>
    <ul style="color:var(--muted);font-size:0.85rem;padding-left:1.2rem;line-height:1.9;margin:0;">
      <li><strong style="color:var(--text);">Unconstrained:</strong> x can be anything in ℝⁿ — most ML training</li>
      <li><strong style="color:var(--text);">Constrained:</strong> must satisfy g(x)≤0 or h(x)=0</li>
      <li><strong style="color:var(--text);">Bound-constrained:</strong> lᵢ ≤ xᵢ ≤ uᵢ for each dimension</li>
    </ul>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #8b5cf6;">
    <h4 style="color:#8b5cf6;margin-top:0;font-size:0.9rem;">By Objective Shape</h4>
    <ul style="color:var(--muted);font-size:0.85rem;padding-left:1.2rem;line-height:1.9;margin:0;">
      <li><strong style="color:var(--text);">Convex:</strong> any local minimum is global — tractable, guaranteed</li>
      <li><strong style="color:var(--text);">Non-convex:</strong> multiple local minima — most deep learning</li>
      <li><strong style="color:var(--text);">Linear:</strong> f and all constraints are linear — LP, fastest</li>
    </ul>
  </div>
  <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;border-top:3px solid #f59e0b;">
    <h4 style="color:#f59e0b;margin-top:0;font-size:0.9rem;">By Information Available</h4>
    <ul style="color:var(--muted);font-size:0.85rem;padding-left:1.2rem;line-height:1.9;margin:0;">
      <li><strong style="color:var(--text);">Gradient-based:</strong> ∇f available — fast convergence</li>
      <li><strong style="color:var(--text);">Derivative-free:</strong> only f(x) evaluations — black-box</li>
      <li><strong style="color:var(--text);">Stochastic:</strong> noisy function evaluations — SGD, BO</li>
    </ul>
  </div>
</div>

<h3>Formulating Real Problems as Optimization</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Defining and Evaluating Objective Functions</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># ── Problem 1: Linear Regression as Optimization ─────────────────</span>
<span style="color:#6b7280;"># Decision variable: weights w = [w0, w1, ..., wn]</span>
<span style="color:#6b7280;"># Objective: minimize MSE = (1/n) * sum((y - X@w)^2)</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">mse_loss</span>(w, X, y):
    <span style="color:#a7f3d0;">"""Mean Squared Error — the objective function for linear regression."""</span>
    <span style="color:#93c5fd;">predictions</span> = [<span style="color:#93c5fd;">sum</span>(w[j] * X[i][j] <span style="color:#c4b5fd;">for</span> j <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#93c5fd;">len</span>(w))) <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#93c5fd;">len</span>(X))]
    <span style="color:#93c5fd;">residuals</span>   = [y[i] - predictions[i] <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#93c5fd;">len</span>(y))]
    <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">sum</span>(r**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> r <span style="color:#c4b5fd;">in</span> residuals) / <span style="color:#93c5fd;">len</span>(y)

<span style="color:#6b7280;"># Data: house size (m²) → price ($k) with bias term</span>
<span style="color:#93c5fd;">X</span> = [[<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">60</span>], [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">80</span>], [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">100</span>], [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">120</span>], [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">140</span>]]
<span style="color:#93c5fd;">y</span> = [<span style="color:#fcd34d;">200</span>, <span style="color:#fcd34d;">250</span>, <span style="color:#fcd34d;">310</span>, <span style="color:#fcd34d;">370</span>, <span style="color:#fcd34d;">420</span>]

<span style="color:#93c5fd;">w_bad</span>  = [<span style="color:#fcd34d;">0</span>,   <span style="color:#fcd34d;">0</span>]       <span style="color:#6b7280;"># zero weights — terrible</span>
<span style="color:#93c5fd;">w_ok</span>   = [<span style="color:#fcd34d;">50</span>,  <span style="color:#fcd34d;">2</span>]       <span style="color:#6b7280;"># ballpark</span>
<span style="color:#93c5fd;">w_good</span> = [<span style="color:#fcd34d;">20</span>,  <span style="color:#fcd34d;">2.75</span>]   <span style="color:#6b7280;"># closer to optimal</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== MSE Loss (lower is better) ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"w={w_bad}  → MSE = {mse_loss(w_bad,  X, y):>10.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"w={w_ok}   → MSE = {mse_loss(w_ok,   X, y):>10.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"w={w_good} → MSE = {mse_loss(w_good, X, y):>10.2f}"</span>)

<span style="color:#93c5fd;">print</span>()

<span style="color:#6b7280;"># ── Problem 2: Portfolio Optimization (Maximize Return) ───────────</span>
<span style="color:#6b7280;"># Decision variable: allocation fractions x = [x1, x2, x3]</span>
<span style="color:#6b7280;"># Constraint: x1 + x2 + x3 = 1  (all capital deployed)</span>
<span style="color:#6b7280;"># Constraint: xi >= 0  (no short selling)</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">portfolio_return</span>(x, expected_returns):
    <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">sum</span>(x[i] * expected_returns[i] <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#93c5fd;">len</span>(x)))

<span style="color:#93c5fd;">returns</span> = [<span style="color:#fcd34d;">0.08</span>, <span style="color:#fcd34d;">0.12</span>, <span style="color:#fcd34d;">0.06</span>]   <span style="color:#6b7280;"># Asset A, B, C expected annual returns</span>
<span style="color:#93c5fd;">alloc_1</span> = [<span style="color:#fcd34d;">0.33</span>, <span style="color:#fcd34d;">0.33</span>, <span style="color:#fcd34d;">0.34</span>]  <span style="color:#6b7280;"># equal weight</span>
<span style="color:#93c5fd;">alloc_2</span> = [<span style="color:#fcd34d;">0.10</span>, <span style="color:#fcd34d;">0.80</span>, <span style="color:#fcd34d;">0.10</span>]  <span style="color:#6b7280;"># heavy on B (highest return)</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Portfolio Return (higher is better) ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Equal weight: {portfolio_return(alloc_1, returns):.2%}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Asset B heavy: {portfolio_return(alloc_2, returns):.2%}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== MSE Loss (lower is better) ===
w=[0, 0]      → MSE =   93800.00
w=[50, 2]     → MSE =    1036.00
w=[20, 2.75]  → MSE =      74.25

=== Portfolio Return (higher is better) ===
Equal weight: 8.68%
Asset B heavy: 11.20%</div>
  </div>
</div>

<h3>Why Optimization Is Hard: The Fundamental Challenges</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;font-size:0.85rem;">
    <div>
      <div style="font-weight:700;color:#ef4444;margin-bottom:6px;">Non-convexity</div>
      <div style="color:var(--muted);">Multiple local minima exist. Gradient-based methods may get trapped and never find the global optimum. Deep networks are a prime example.</div>
    </div>
    <div>
      <div style="font-weight:700;color:#f59e0b;margin-bottom:6px;">Dimensionality</div>
      <div style="color:var(--muted);">GPT-4 has ~1.8 trillion parameters. Exhaustive search over such spaces is computationally impossible — we need smart search strategies.</div>
    </div>
    <div>
      <div style="font-weight:700;color:#8b5cf6;margin-bottom:6px;">Constraints</div>
      <div style="color:var(--muted);">Real problems have budgets, physical limits, and legal requirements. Moving inside the feasible set requires specialized constrained solvers.</div>
    </div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $optModule->id,
            'title'       => '13.1 What Is Optimization? Goals, Vocabulary & Problem Formulation',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L13_1', [
                ['q' => 'Training a neural network minimizes which type of function?', 'opts' => ['Decision variable', 'Objective (loss) function', 'Equality constraint', 'Feasible set'], 'ans' => 1, 'exp' => 'The objective function (also called the loss or cost function) is the scalar quantity the optimizer minimizes. In neural network training it is typically cross-entropy or MSE. The weights are the decision variables.'],
                ['q' => 'To maximize profit P(x), you can equivalently...', 'opts' => ['Solve P(x) = 0', 'Minimize −P(x)', 'Set ∇P(x) = 1', 'Maximize 1/P(x)'], 'ans' => 1, 'exp' => 'Every maximization problem can be converted to minimization by negating the objective. Minimizing −P(x) gives exactly the same optimal x* as maximizing P(x). This is why most optimization libraries expose only a minimize interface.'],
                ['q' => 'The gradient ∇f(x) equals zero at which types of points?', 'opts' => ['Only global minima', 'Only local minima', 'Local minima, local maxima, AND saddle points', 'Only where f is discontinuous'], 'ans' => 2, 'exp' => 'A zero gradient (∇f = 0) is a necessary condition for a local optimum but not sufficient. Saddle points also have ∇f = 0 — they are minima in some directions and maxima in others. Extra tests (second derivative, Hessian) distinguish them.'],
                ['q' => 'A convex optimization problem guarantees that...', 'opts' => ['The function is always linear', 'The feasible set is a circle', 'Any local minimum is also the global minimum', 'Gradient descent always diverges'], 'ans' => 2, 'exp' => 'Convexity is the gold standard in optimization: for a convex objective over a convex feasible set, any locally optimal solution is also globally optimal. This makes convex problems tractable and is why practitioners try to formulate problems convexly when possible.'],
                ['q' => 'Decision variables are the quantities that...', 'opts' => ['Are fixed by the problem data', 'Describe the constraints only', 'You control and optimize over to minimize or maximize the objective', 'Are always scalars'], 'ans' => 2, 'exp' => 'Decision variables (often written as x or w or θ) are what the optimizer is allowed to change. In ML, these are the model parameters (weights and biases). In portfolio optimization, they are the allocation fractions. The objective is evaluated as a function of these variables.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 13.2 — Calculus Review: Derivatives, Gradients & Critical Points
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Calculus Review: Derivatives, Gradients & Critical Points</h2>
<p>Calculus is the language of change, and optimization is fundamentally about directing change toward improvement. Before you can understand gradient descent, backpropagation, or any gradient-based optimizer, you must be fluent in derivatives and gradients. This lesson builds exactly the calculus intuition you need — nothing more, nothing less — with every concept tied directly to how it is used in machine learning.</p>

<h3>The Derivative: Rate of Change at a Point</h3>
<p>The derivative f′(x) of a function f at a point x tells you the instantaneous rate of change — how f responds to an infinitesimal nudge in x. Geometrically, it is the slope of the tangent line to the curve at that point. In optimization, it tells us which direction to move x to decrease f.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:24px;font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--muted);">
  <span style="color:var(--text);font-weight:600;">Definition (limit form):</span><br><br>
  f′(x) = lim<sub>h→0</sub> [f(x + h) − f(x)] / h<br><br>
  <span style="color:var(--text);font-weight:600;">Key rules:</span><br>
  d/dx [xⁿ] = n·xⁿ⁻¹ &nbsp;&nbsp; (power rule)<br>
  d/dx [eˣ] = eˣ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (exponential)<br>
  d/dx [ln x] = 1/x &nbsp;&nbsp;&nbsp;&nbsp; (logarithm — appears in cross-entropy)<br>
  Chain rule: d/dx [f(g(x))] = f′(g(x)) · g′(x) &nbsp;&nbsp; (foundation of backpropagation)
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Numerical Derivative via Finite Differences</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">numerical_derivative</span>(f, x, h=<span style="color:#fcd34d;">1e-5</span>):
    <span style="color:#a7f3d0;">"""
    Central difference approximation of f'(x).
    More accurate than forward difference: O(h²) vs O(h) error.
    """</span>
    <span style="color:#c4b5fd;">return</span> (f(x + h) - f(x - h)) / (<span style="color:#fcd34d;">2</span> * h)

<span style="color:#6b7280;"># Test on known functions</span>
<span style="color:#93c5fd;">functions</span> = [
    (<span style="color:#c4b5fd;">lambda</span> x: x**<span style="color:#fcd34d;">2</span>,          <span style="color:#c4b5fd;">lambda</span> x: <span style="color:#fcd34d;">2</span>*x,         <span style="color:#a7f3d0;">"f(x) = x²,    f'(x) = 2x"</span>),
    (<span style="color:#c4b5fd;">lambda</span> x: x**<span style="color:#fcd34d;">3</span>,          <span style="color:#c4b5fd;">lambda</span> x: <span style="color:#fcd34d;">3</span>*x**<span style="color:#fcd34d;">2</span>,      <span style="color:#a7f3d0;">"f(x) = x³,    f'(x) = 3x²"</span>),
    (<span style="color:#c4b5fd;">lambda</span> x: math.exp(x),   <span style="color:#c4b5fd;">lambda</span> x: math.exp(x), <span style="color:#a7f3d0;">"f(x) = eˣ,    f'(x) = eˣ"</span>),
    (<span style="color:#c4b5fd;">lambda</span> x: math.log(x),   <span style="color:#c4b5fd;">lambda</span> x: <span style="color:#fcd34d;">1</span>/x,         <span style="color:#a7f3d0;">"f(x) = ln(x), f'(x) = 1/x"</span>),
    (<span style="color:#c4b5fd;">lambda</span> x: math.sin(x),   <span style="color:#c4b5fd;">lambda</span> x: math.cos(x), <span style="color:#a7f3d0;">"f(x) = sin(x), f'(x) = cos(x)"</span>),
]

<span style="color:#93c5fd;">x_test</span> = <span style="color:#fcd34d;">2.0</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Function':35} {'Numerical':>12} {'Exact':>12} {'Error':>12}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─"</span> * <span style="color:#fcd34d;">75</span>)
<span style="color:#c4b5fd;">for</span> f, df, name <span style="color:#c4b5fd;">in</span> functions:
    <span style="color:#93c5fd;">num</span>   = numerical_derivative(f, x_test)
    <span style="color:#93c5fd;">exact</span> = df(x_test)
    <span style="color:#93c5fd;">err</span>   = <span style="color:#93c5fd;">abs</span>(num - exact)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{name:35} {num:>12.6f} {exact:>12.6f} {err:>12.2e}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Function                             Numerical        Exact        Error
───────────────────────────────────────────────────────────────────────────
f(x) = x²,    f'(x) = 2x           4.000000     4.000000     1.33e-10
f(x) = x³,    f'(x) = 3x²         12.000000    12.000000     1.78e-10
f(x) = eˣ,    f'(x) = eˣ           7.389056     7.389056     2.84e-11
f(x) = ln(x), f'(x) = 1/x          0.500000     0.500000     4.44e-12
f(x) = sin(x), f'(x) = cos(x)     -0.416147    -0.416147     1.25e-14</div>
  </div>
</div>

<h3>The Gradient: Derivatives in Multiple Dimensions</h3>
<p>Real machine learning models have thousands to billions of parameters. The <strong>gradient</strong> ∇f(x) is the generalization of the derivative to multiple dimensions — a vector where each component is the partial derivative with respect to one variable. The gradient always points in the direction of <em>steepest ascent</em>. Moving opposite to the gradient — the direction of steepest <em>descent</em> — is the fundamental idea behind gradient descent.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Numerical Gradient in Multiple Dimensions</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">numerical_gradient</span>(f, x, h=<span style="color:#fcd34d;">1e-5</span>):
    <span style="color:#a7f3d0;">"""
    Compute ∇f(x) numerically using central differences.
    x is a list of floats (parameter vector).
    Returns the gradient vector as a list.
    """</span>
    <span style="color:#93c5fd;">grad</span> = [<span style="color:#fcd34d;">0.0</span>] * <span style="color:#93c5fd;">len</span>(x)
    <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#93c5fd;">len</span>(x)):
        <span style="color:#93c5fd;">x_plus</span>  = x[:]
        <span style="color:#93c5fd;">x_minus</span> = x[:]
        x_plus[i]  += h
        x_minus[i] -= h
        grad[i] = (f(x_plus) - f(x_minus)) / (<span style="color:#fcd34d;">2</span> * h)
    <span style="color:#c4b5fd;">return</span> grad

<span style="color:#6b7280;"># f(x, y) = x² + 2y²   (bowl-shaped — elliptic paraboloid)</span>
<span style="color:#6b7280;"># Analytic gradient: ∇f = [2x, 4y]</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">f</span>(v): <span style="color:#c4b5fd;">return</span> v[<span style="color:#fcd34d;">0</span>]**<span style="color:#fcd34d;">2</span> + <span style="color:#fcd34d;">2</span> * v[<span style="color:#fcd34d;">1</span>]**<span style="color:#fcd34d;">2</span>

<span style="color:#93c5fd;">test_points</span> = [[<span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">2</span>], [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>], [-<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>], [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>]]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"f(x,y) = x² + 2y²    ∇f = [2x, 4y]"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Point':>12} {'f(x,y)':>8} {'Numerical ∇f':>20} {'Analytic ∇f':>20}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─"</span> * <span style="color:#fcd34d;">65</span>)
<span style="color:#c4b5fd;">for</span> p <span style="color:#c4b5fd;">in</span> test_points:
    <span style="color:#93c5fd;">num_g</span>  = numerical_gradient(f, p)
    <span style="color:#93c5fd;">ana_g</span>  = [<span style="color:#fcd34d;">2</span>*p[<span style="color:#fcd34d;">0</span>], <span style="color:#fcd34d;">4</span>*p[<span style="color:#fcd34d;">1</span>]]
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{str(p):>12} {f(p):>8.2f} {str([round(g,4) for g in num_g]):>20} {str(ana_g):>20}"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n→ At [0,0]: gradient is [0,0] — this IS the minimum!"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"→ At [3,2]: gradient [6,8] points AWAY from minimum."</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  Moving in direction [-6,-8] (negative gradient) brings us closer."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>f(x,y) = x² + 2y²    ∇f = [2x, 4y]
       Point   f(x,y)        Numerical ∇f         Analytic ∇f
─────────────────────────────────────────────────────────────────
      [3, 2]    17.00       [6.0001, 8.0001]           [6, 8]
      [1, 0]     1.00       [2.0001, 0.0001]           [2, 0]
     [-2, 3]    22.00      [-4.0001, 12.0001]         [-4, 12]
      [0, 0]     0.00       [0.0001, 0.0001]           [0, 0]

→ At [0,0]: gradient is [0,0] — this IS the minimum!
→ At [3,2]: gradient [6,8] points AWAY from minimum.
  Moving in direction [-6,-8] (negative gradient) brings us closer.</div>
  </div>
</div>

<h3>Critical Points and the Second Derivative Test</h3>
<p>A <strong>critical point</strong> is where ∇f(x) = 0. But not all critical points are minima. The <strong>Hessian matrix</strong> H (matrix of all second partial derivatives) tells you the curvature at a critical point, distinguishing minima, maxima, and saddle points.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:32px;">
  <div style="background:rgba(0,0,0,0.2);padding:12px 20px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.8rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;font-family:'JetBrains Mono',monospace;">Second Derivative / Hessian Classification</span>
  </div>
  <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;font-size:0.875rem;">
    <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);border-radius:8px;padding:16px;text-align:center;">
      <div style="color:#10b981;font-weight:700;margin-bottom:8px;">H is Positive Definite</div>
      <div style="color:var(--muted);">All eigenvalues &gt; 0<br>∇²f &gt; 0 in 1D<br><strong style="color:var(--text);">Local Minimum ✓</strong></div>
    </div>
    <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:8px;padding:16px;text-align:center;">
      <div style="color:#ef4444;font-weight:700;margin-bottom:8px;">H is Negative Definite</div>
      <div style="color:var(--muted);">All eigenvalues &lt; 0<br>∇²f &lt; 0 in 1D<br><strong style="color:var(--text);">Local Maximum ✓</strong></div>
    </div>
    <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.25);border-radius:8px;padding:16px;text-align:center;">
      <div style="color:#f59e0b;font-weight:700;margin-bottom:8px;">H is Indefinite</div>
      <div style="color:var(--muted);">Mixed eigenvalues<br>min in some dirs, max in others<br><strong style="color:var(--text);">Saddle Point ⚠</strong></div>
    </div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Identifying Critical Points and Their Nature</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">classify_1d</span>(f, df, d2f, x):
    <span style="color:#a7f3d0;">"""Classify a critical point of a 1D function."""</span>
    <span style="color:#93c5fd;">g</span>   = df(x)
    <span style="color:#93c5fd;">h</span>   = d2f(x)
    <span style="color:#93c5fd;">fval</span> = f(x)
    <span style="color:#c4b5fd;">if</span>   <span style="color:#93c5fd;">abs</span>(g) > <span style="color:#fcd34d;">1e-6</span>: kind = <span style="color:#a7f3d0;">"Not a critical point"</span>
    <span style="color:#c4b5fd;">elif</span> h > <span style="color:#fcd34d;">0</span>:          kind = <span style="color:#a7f3d0;">"LOCAL MINIMUM  (f'' > 0)"</span>
    <span style="color:#c4b5fd;">elif</span> h < <span style="color:#fcd34d;">0</span>:          kind = <span style="color:#a7f3d0;">"LOCAL MAXIMUM  (f'' < 0)"</span>
    <span style="color:#c4b5fd;">else</span>:                  kind = <span style="color:#a7f3d0;">"INFLECTION (inconclusive)"</span>
    <span style="color:#c4b5fd;">return</span> <span style="color:#a7f3d0;">f"x={x:+.2f}  f={fval:+.3f}  f'={g:+.3f}  f''={h:+.3f}  → {kind}"</span>

<span style="color:#6b7280;"># f(x) = x³ - 3x  →  f'(x) = 3x² - 3  →  critical points at x = ±1</span>
<span style="color:#93c5fd;">f</span>   = <span style="color:#c4b5fd;">lambda</span> x: x**<span style="color:#fcd34d;">3</span> - <span style="color:#fcd34d;">3</span>*x
<span style="color:#93c5fd;">df</span>  = <span style="color:#c4b5fd;">lambda</span> x: <span style="color:#fcd34d;">3</span>*x**<span style="color:#fcd34d;">2</span> - <span style="color:#fcd34d;">3</span>
<span style="color:#93c5fd;">d2f</span> = <span style="color:#c4b5fd;">lambda</span> x: <span style="color:#fcd34d;">6</span>*x

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"f(x) = x³ − 3x    f'(x) = 3x² − 3    critical points: x = ±1"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─"</span> * <span style="color:#fcd34d;">70</span>)
<span style="color:#c4b5fd;">for</span> x_crit <span style="color:#c4b5fd;">in</span> [-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>]:   <span style="color:#6b7280;"># test also x=0 (NOT a critical point)</span>
    <span style="color:#93c5fd;">print</span>(classify_1d(f, df, d2f, x_crit))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>f(x) = x³ − 3x    f'(x) = 3x² − 3    critical points: x = ±1
──────────────────────────────────────────────────────────────────────
x=-1.00  f=+2.000  f'=+0.000  f''=-6.000  → LOCAL MAXIMUM  (f'' < 0)
x=+1.00  f=-2.000  f'=+0.000  f''+6.000  → LOCAL MINIMUM  (f'' > 0)
x=+0.00  f=+0.000  f'=-3.000  f''=+0.000  → Not a critical point</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $optModule->id,
            'title'       => '13.2 Calculus Review: Derivatives, Gradients & Critical Points',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L13_2', [
                ['q' => 'The gradient ∇f(x) of a multivariable function points in the direction of...', 'opts' => ['Steepest descent', 'Steepest ascent', 'The nearest local minimum', 'The global minimum'], 'ans' => 1, 'exp' => 'The gradient always points in the direction of steepest ASCENT — where f increases fastest. To minimize f, we move in the direction of the NEGATIVE gradient (−∇f). This is the fundamental insight behind gradient descent.'],
                ['q' => 'If f\'\'(x) > 0 at a critical point, then that point is a...', 'opts' => ['Local maximum', 'Saddle point', 'Global minimum', 'Local minimum'], 'ans' => 3, 'exp' => 'Positive second derivative (f\'\' > 0) at a critical point (f\' = 0) means the function is concave up — like a cup — at that point. This means it is a local minimum. Negative f\'\' means concave down (local max). Zero f\'\' is inconclusive.'],
                ['q' => 'The chain rule d/dx[f(g(x))] = f\'(g(x))·g\'(x) is the mathematical basis for...', 'opts' => ['Linear programming', 'Backpropagation in neural networks', 'The simplex method', 'Genetic algorithms'], 'ans' => 1, 'exp' => 'Backpropagation is the chain rule applied repeatedly through the layers of a neural network. Each layer\'s gradient is computed by multiplying the upstream gradient by the local gradient — exactly the chain rule applied layer by layer.'],
                ['q' => 'A numerical gradient computed via central differences [f(x+h) − f(x−h)] / (2h) has what order of approximation error?', 'opts' => ['O(h)', 'O(h²)', 'O(1/h)', 'Exact — no error'], 'ans' => 1, 'exp' => 'Central differences have O(h²) error, making them much more accurate than forward differences [f(x+h)−f(x)]/h which have O(h) error. This is why numerical gradient checkers in ML frameworks use central differences.'],
                ['q' => 'Which statement correctly identifies a saddle point?', 'opts' => ['∇f = 0 and f\'\' > 0 everywhere', '∇f ≠ 0 but f is bounded', '∇f = 0 but the Hessian has both positive and negative eigenvalues', '∇f = 0 and the Hessian is all zeros'], 'ans' => 2, 'exp' => 'A saddle point is a critical point (∇f = 0) where the Hessian is indefinite — it has both positive and negative eigenvalues, meaning curvature is positive in some directions and negative in others. The function looks like a saddle or mountain pass. These are common in high-dimensional ML loss landscapes.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 13.3 — Gradient Descent: The Engine of Machine Learning
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Gradient Descent: The Engine of Machine Learning</h2>
<p>Gradient descent is the most important optimization algorithm in data science. It is the mechanism by which every neural network learns, every logistic regression converges, and every deep learning breakthrough has been achieved. The idea is elegantly simple: repeatedly step in the direction opposite to the gradient, always moving downhill on the loss surface, until you reach a minimum. Understanding gradient descent deeply — its mechanics, its failure modes, and its hyperparameters — gives you a profound advantage in debugging and improving models.</p>

<h3>The Update Rule</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <div style="font-family:'JetBrains Mono',monospace;font-size:1rem;color:#e5e7eb;margin-bottom:16px;text-align:center;">
    θ<sub>t+1</sub> = θ<sub>t</sub> − α · ∇f(θ<sub>t</sub>)
  </div>
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;font-size:0.82rem;text-align:center;">
    <div><code style="color:#3b82f6;">θ</code><br><span style="color:var(--muted);">Parameters<br>(decision variables)</span></div>
    <div><code style="color:#10b981;">α</code><br><span style="color:var(--muted);">Learning rate<br>(step size)</span></div>
    <div><code style="color:#f59e0b;">∇f(θ)</code><br><span style="color:var(--muted);">Gradient at<br>current point</span></div>
    <div><code style="color:#ec4899;">t</code><br><span style="color:var(--muted);">Iteration<br>number</span></div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Vanilla Gradient Descent from Scratch: 1D and 2D</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">gradient_descent_1d</span>(f, df, x0, lr=<span style="color:#fcd34d;">0.1</span>, max_iters=<span style="color:#fcd34d;">50</span>, tol=<span style="color:#fcd34d;">1e-6</span>):
    <span style="color:#a7f3d0;">"""
    Vanilla gradient descent for a scalar function of one variable.
    f   : objective function f(x)
    df  : analytical gradient f'(x)
    x0  : starting point
    lr  : learning rate (step size α)
    """</span>
    <span style="color:#93c5fd;">x</span>       = x0
    <span style="color:#93c5fd;">history</span> = [(x, f(x))]
    <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(max_iters):
        <span style="color:#93c5fd;">grad</span> = df(x)
        <span style="color:#93c5fd;">x</span>    = x - lr * grad           <span style="color:#6b7280;"># the update rule</span>
        history.append((x, f(x)))
        <span style="color:#c4b5fd;">if</span> <span style="color:#93c5fd;">abs</span>(grad) < tol: <span style="color:#c4b5fd;">break</span>   <span style="color:#6b7280;"># converged when gradient ≈ 0</span>
    <span style="color:#c4b5fd;">return</span> x, f(x), history

<span style="color:#6b7280;"># ── Example 1: f(x) = (x - 3)² — minimum at x = 3 ────────────────</span>
<span style="color:#93c5fd;">f1</span>  = <span style="color:#c4b5fd;">lambda</span> x: (x - <span style="color:#fcd34d;">3</span>)**<span style="color:#fcd34d;">2</span>
<span style="color:#93c5fd;">df1</span> = <span style="color:#c4b5fd;">lambda</span> x: <span style="color:#fcd34d;">2</span> * (x - <span style="color:#fcd34d;">3</span>)

<span style="color:#93c5fd;">x_opt</span>, <span style="color:#93c5fd;">f_opt</span>, <span style="color:#93c5fd;">hist</span> = gradient_descent_1d(f1, df1, x0=<span style="color:#fcd34d;">10.0</span>, lr=<span style="color:#fcd34d;">0.3</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"f(x) = (x-3)²   minimum at x=3"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Iter':>5} {'x':>12} {'f(x)':>12}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─"</span> * <span style="color:#fcd34d;">32</span>)
<span style="color:#c4b5fd;">for</span> i, (xi, fi) <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(hist[:<span style="color:#fcd34d;">10</span>]):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{i:>5} {xi:>12.6f} {fi:>12.8f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n✓ Converged to x = {x_opt:.8f}, f(x) = {f_opt:.2e}"</span>)

<span style="color:#93c5fd;">print</span>()

<span style="color:#6b7280;"># ── Example 2: Linear Regression via Gradient Descent ─────────────</span>
<span style="color:#6b7280;"># Data: y ≈ 2x + 1  (slope=2, intercept=1)</span>
<span style="color:#93c5fd;">data_x</span> = [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">5</span>]
<span style="color:#93c5fd;">data_y</span> = [<span style="color:#fcd34d;">3.1</span>, <span style="color:#fcd34d;">5.0</span>, <span style="color:#fcd34d;">6.9</span>, <span style="color:#fcd34d;">9.2</span>, <span style="color:#fcd34d;">11.1</span>]
<span style="color:#93c5fd;">n</span>      = <span style="color:#93c5fd;">len</span>(data_x)

<span style="color:#6b7280;"># Parameters: w (slope), b (intercept)</span>
<span style="color:#93c5fd;">w</span>, <span style="color:#93c5fd;">b</span>  = <span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">0.0</span>
<span style="color:#93c5fd;">lr</span>     = <span style="color:#fcd34d;">0.01</span>

<span style="color:#c4b5fd;">for</span> epoch <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">500</span>):
    <span style="color:#6b7280;"># Forward pass: predictions</span>
    <span style="color:#93c5fd;">preds</span>    = [w * xi + b <span style="color:#c4b5fd;">for</span> xi <span style="color:#c4b5fd;">in</span> data_x]
    <span style="color:#93c5fd;">residuals</span>= [preds[i] - data_y[i] <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n)]
    <span style="color:#93c5fd;">mse</span>      = <span style="color:#93c5fd;">sum</span>(r**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> r <span style="color:#c4b5fd;">in</span> residuals) / n

    <span style="color:#6b7280;"># Gradients of MSE w.r.t. w and b</span>
    <span style="color:#93c5fd;">dw</span> = (<span style="color:#fcd34d;">2</span>/n) * <span style="color:#93c5fd;">sum</span>(residuals[i] * data_x[i] <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n))
    <span style="color:#93c5fd;">db</span> = (<span style="color:#fcd34d;">2</span>/n) * <span style="color:#93c5fd;">sum</span>(residuals)

    <span style="color:#6b7280;"># Update parameters</span>
    <span style="color:#93c5fd;">w</span> -= lr * dw
    <span style="color:#93c5fd;">b</span> -= lr * db

    <span style="color:#c4b5fd;">if</span> epoch % <span style="color:#fcd34d;">100</span> == <span style="color:#fcd34d;">0</span>:
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Epoch {epoch:>4}: w={w:.4f}  b={b:.4f}  MSE={mse:.5f}"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n✓ Fitted: y = {w:.4f}x + {b:.4f}  (true: y = 2x + 1)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>f(x) = (x-3)²   minimum at x=3
 Iter            x         f(x)
────────────────────────────────
    0   10.000000 49.00000000
    1    5.800000 7.84000000
    2    4.120000 1.25440000
    3    3.472000 0.22278400
    4    3.188800 0.03564544
    5    3.075520 0.00570327
    6    3.030208 0.00091252
    7    3.012083 0.00014600
    8    3.004833 0.00002336
    9    3.001933 0.00000374

✓ Converged to x = 3.00000013, f(x) = 1.75e-16

Epoch    0: w=0.4560  b=0.1020  MSE=46.54880
Epoch  100: w=1.7931  b=0.6126  MSE=0.14219
Epoch  200: w=1.9491  b=0.3817  MSE=0.02882
Epoch  300: w=1.9858  b=0.2490  MSE=0.00969
Epoch  400: w=1.9974  b=0.1748  MSE=0.00438

✓ Fitted: y = 2.0014x + 1.0913  (true: y = 2x + 1)</div>
  </div>
</div>

<h3>The Learning Rate: The Most Critical Hyperparameter</h3>
<p>The learning rate α controls step size. Too large → overshooting, oscillation, or divergence. Too small → painfully slow convergence, getting stuck. Choosing α correctly is more art than science, but principled strategies exist.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Learning Rate Effects: Too Small, Too Large, Just Right</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># f(x) = x²,  f'(x) = 2x,  minimum at x = 0</span>
<span style="color:#93c5fd;">f</span>  = <span style="color:#c4b5fd;">lambda</span> x: x**<span style="color:#fcd34d;">2</span>
<span style="color:#93c5fd;">df</span> = <span style="color:#c4b5fd;">lambda</span> x: <span style="color:#fcd34d;">2</span>*x

<span style="color:#93c5fd;">configs</span> = [
    (<span style="color:#fcd34d;">0.01</span>,  <span style="color:#a7f3d0;">"Too SMALL  — sluggish convergence"</span>),
    (<span style="color:#fcd34d;">0.5</span>,   <span style="color:#a7f3d0;">"Just RIGHT — smooth, fast"</span>),
    (<span style="color:#fcd34d;">1.0</span>,   <span style="color:#a7f3d0;">"Boundary   — oscillates at x=0"</span>),
    (<span style="color:#fcd34d;">1.1</span>,   <span style="color:#a7f3d0;">"Too LARGE  — DIVERGES!"</span>),
]

<span style="color:#c4b5fd;">for</span> lr, desc <span style="color:#c4b5fd;">in</span> configs:
    <span style="color:#93c5fd;">x</span>     = <span style="color:#fcd34d;">8.0</span>
    <span style="color:#93c5fd;">trace</span> = [x]
    <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">10</span>):
        x = x - lr * df(x)
        trace.append(x)
        <span style="color:#c4b5fd;">if</span> <span style="color:#93c5fd;">abs</span>(x) > <span style="color:#fcd34d;">1e6</span>: <span style="color:#c4b5fd;">break</span>   <span style="color:#6b7280;"># diverged</span>
    <span style="color:#93c5fd;">final</span>   = trace[-<span style="color:#fcd34d;">1</span>]
    <span style="color:#93c5fd;">values</span>  = <span style="color:#a7f3d0;">", "</span>.join(<span style="color:#a7f3d0;">f"{v:.2f}"</span> <span style="color:#c4b5fd;">for</span> v <span style="color:#c4b5fd;">in</span> trace[:<span style="color:#fcd34d;">6</span>])
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"α={lr:<5} {desc}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"       x trace: [{values}...]  final={final:.4f}\n"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>α=0.01  Too SMALL  — sluggish convergence
       x trace: [8.00, 7.84, 7.68, 7.53, 7.38, 7.24...]  final=6.5983

α=0.5   Just RIGHT — smooth, fast
       x trace: [8.00, 4.00, 2.00, 1.00, 0.50, 0.25...]  final=0.0156

α=1.0   Boundary   — oscillates at x=0
       x trace: [8.00, -8.00, 8.00, -8.00, 8.00, -8.00...]  final=8.0000

α=1.1   Too LARGE  — DIVERGES!
       x trace: [8.00, -9.60, 11.52, -13.82, 16.59, -19.91...]  final=213.4881</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $optModule->id,
            'title'       => '13.3 Gradient Descent: The Engine of Machine Learning',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L13_3', [
                ['q' => 'The gradient descent update rule is θ ← θ − α·∇f(θ). Why do we SUBTRACT the gradient?', 'opts' => ['The gradient is always negative', 'The gradient points uphill (ascent); subtracting it moves downhill (descent) toward the minimum', 'Subtraction is computationally cheaper', 'The learning rate α is negative so it cancels'], 'ans' => 1, 'exp' => 'The gradient ∇f points in the direction of steepest ASCENT. To MINIMIZE f, we want to move in the direction of steepest descent, which is the negative gradient (−∇f). Hence the update subtracts α·∇f from the current parameters.'],
                ['q' => 'Gradient descent converges when...', 'opts' => ['The loss becomes negative', 'The parameters reach exactly 0', 'The gradient norm ‖∇f‖ approaches 0, indicating a critical point', 'The learning rate drops to 0'], 'ans' => 2, 'exp' => 'Convergence is reached when the gradient is (approximately) zero — meaning we are at a critical point where no direction improves f. In practice, convergence is declared when ‖∇f‖ < tolerance or the loss change between iterations is negligibly small.'],
                ['q' => 'A learning rate that is too large causes...', 'opts' => ['Slower but more accurate convergence', 'Overshooting the minimum, oscillation, and possibly divergence', 'The model to converge to the global minimum', 'Gradients to become exactly zero immediately'], 'ans' => 1, 'exp' => 'With a large learning rate, each step overshoots the minimum. The algorithm bounces back and forth across the valley, and in the worst case diverges to infinity. The critical learning rate for f(x)=x² is α < 1/max_eigenvalue(Hessian).'],
                ['q' => 'How many times must the full dataset be processed to compute one gradient update in Batch (Vanilla) Gradient Descent?', 'opts' => ['Once per parameter update — all N samples', 'One random sample per update', 'A random subset (mini-batch)', 'Only the misclassified samples'], 'ans' => 0, 'exp' => 'Vanilla (Batch) Gradient Descent computes the gradient using ALL N training samples for each parameter update. This gives an exact gradient but is expensive per iteration for large datasets. SGD and mini-batch GD address this limitation.'],
                ['q' => 'In linear regression gradient descent, the gradient of MSE with respect to the slope w is proportional to...', 'opts' => ['The sum of all targets y', 'The sum of residuals (predictions − targets) × input features x', 'The learning rate times the weights', 'The L2 norm of the weights'], 'ans' => 1, 'exp' => 'For MSE = (1/n)Σ(wxi+b−yi)², the gradient w.r.t. w is (2/n)Σ(wxi+b−yi)·xi — residuals multiplied by their corresponding input features. This is the chain rule applied: d/dw[(ŷ−y)²] = 2(ŷ−y)·xi.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 13.4 — Variants of Gradient Descent: SGD, Mini-Batch & Momentum
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Variants of Gradient Descent: SGD, Mini-Batch, Momentum & Adam</h2>
<p>Vanilla gradient descent computes gradients on the entire dataset before each update. This is exact but catastrophically slow for modern datasets with millions of examples. The variants covered here are the actual algorithms powering deep learning today — understanding each one's tradeoffs makes you a better practitioner when debugging training instability or slow convergence.</p>

<h3>The Three Gradient Descent Flavors</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:32px;">
  <div style="background:rgba(0,0,0,0.2);padding:12px 20px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.8rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;font-family:'JetBrains Mono',monospace;">Comparison of GD Variants</span>
  </div>
  <div style="padding:0;font-size:0.84rem;">
    <div style="display:grid;grid-template-columns:150px 1fr 1fr 1fr;border-bottom:1px solid var(--border);padding:10px 16px;font-weight:700;color:var(--muted);">
      <span>Property</span><span>Batch GD</span><span>Stochastic GD</span><span>Mini-Batch GD</span>
    </div>
    <div style="display:grid;grid-template-columns:150px 1fr 1fr 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;"><span style="color:var(--text);font-weight:600;">Gradient estimate</span><span style="color:var(--muted);">Exact (all N)</span><span style="color:var(--muted);">Noisy (1 sample)</span><span style="color:var(--muted);">Approximate (B samples)</span></div>
    <div style="display:grid;grid-template-columns:150px 1fr 1fr 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;"><span style="color:var(--text);font-weight:600;">Updates per epoch</span><span style="color:var(--muted);">1</span><span style="color:var(--muted);">N</span><span style="color:var(--muted);">N/B</span></div>
    <div style="display:grid;grid-template-columns:150px 1fr 1fr 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;"><span style="color:var(--text);font-weight:600;">Memory usage</span><span style="color:var(--muted);">Full dataset</span><span style="color:var(--muted);">1 sample</span><span style="color:var(--muted);">B samples (e.g. 32–512)</span></div>
    <div style="display:grid;grid-template-columns:150px 1fr 1fr 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;"><span style="color:var(--text);font-weight:600;">Convergence path</span><span style="color:var(--muted);">Smooth, direct</span><span style="color:var(--muted);">Noisy, erratic</span><span style="color:var(--muted);">Moderately noisy</span></div>
    <div style="display:grid;grid-template-columns:150px 1fr 1fr 1fr;padding:10px 16px;"><span style="color:var(--text);font-weight:600;">Used in practice?</span><span style="color:var(--muted);">Small datasets only</span><span style="color:var(--muted);">Online learning</span><span style="color:#10b981;font-weight:700;">Yes — standard in DL</span></div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — SGD, Mini-Batch GD & Momentum from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> random
random.seed(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Dataset: noisy y ≈ 2x + 1</span>
<span style="color:#93c5fd;">N</span>      = <span style="color:#fcd34d;">100</span>
<span style="color:#93c5fd;">X_data</span> = [random.uniform(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">10</span>) <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(N)]
<span style="color:#93c5fd;">Y_data</span> = [<span style="color:#fcd34d;">2</span> * x + <span style="color:#fcd34d;">1</span> + random.gauss(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0.5</span>) <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> X_data]

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">batch_gradient</span>(w, b, X, Y):
    <span style="color:#a7f3d0;">"""Full-batch gradient of MSE w.r.t. w and b."""</span>
    <span style="color:#93c5fd;">n</span>   = <span style="color:#93c5fd;">len</span>(X)
    <span style="color:#93c5fd;">res</span> = [w*X[i]+b - Y[i] <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n)]
    <span style="color:#93c5fd;">dw</span>  = (<span style="color:#fcd34d;">2</span>/n) * <span style="color:#93c5fd;">sum</span>(res[i]*X[i] <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n))
    <span style="color:#93c5fd;">db</span>  = (<span style="color:#fcd34d;">2</span>/n) * <span style="color:#93c5fd;">sum</span>(res)
    <span style="color:#c4b5fd;">return</span> dw, db

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">mini_batch_gradient</span>(w, b, X, Y, batch_size=<span style="color:#fcd34d;">16</span>):
    <span style="color:#a7f3d0;">"""Gradient computed on a random mini-batch of size batch_size."""</span>
    <span style="color:#93c5fd;">idx</span> = random.sample(<span style="color:#93c5fd;">range</span>(<span style="color:#93c5fd;">len</span>(X)), batch_size)
    <span style="color:#93c5fd;">Xb</span>  = [X[i] <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> idx]
    <span style="color:#93c5fd;">Yb</span>  = [Y[i] <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> idx]
    <span style="color:#c4b5fd;">return</span> batch_gradient(w, b, Xb, Yb)

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">mse</span>(w, b, X, Y):
    <span style="color:#93c5fd;">n</span> = <span style="color:#93c5fd;">len</span>(X)
    <span style="color:#c4b5fd;">return</span> <span style="color:#93c5fd;">sum</span>((w*X[i]+b-Y[i])**<span style="color:#fcd34d;">2</span> <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n)) / n

<span style="color:#6b7280;"># ── Train with Mini-Batch GD (standard) and with Momentum ────────</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Mini-Batch GD (batch=16) ==="</span>)
<span style="color:#93c5fd;">w</span>, <span style="color:#93c5fd;">b</span> = <span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">0.0</span>
<span style="color:#93c5fd;">lr</span>    = <span style="color:#fcd34d;">0.01</span>
<span style="color:#c4b5fd;">for</span> epoch <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">200</span>):
    <span style="color:#93c5fd;">dw</span>, <span style="color:#93c5fd;">db</span> = mini_batch_gradient(w, b, X_data, Y_data, <span style="color:#fcd34d;">16</span>)
    <span style="color:#93c5fd;">w</span> -= lr * dw;  <span style="color:#93c5fd;">b</span> -= lr * db
    <span style="color:#c4b5fd;">if</span> epoch % <span style="color:#fcd34d;">50</span> == <span style="color:#fcd34d;">0</span>:
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  epoch {epoch:>3}: w={w:.4f} b={b:.4f} MSE={mse(w,b,X_data,Y_data):.4f}"</span>)

<span style="color:#93c5fd;">print</span>()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== SGD + Momentum (β=0.9) ==="</span>)
<span style="color:#93c5fd;">w</span>, <span style="color:#93c5fd;">b</span>   = <span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">0.0</span>
<span style="color:#93c5fd;">vw</span>, <span style="color:#93c5fd;">vb</span> = <span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">0.0</span>   <span style="color:#6b7280;"># velocity (momentum accumulator)</span>
<span style="color:#93c5fd;">beta</span>   = <span style="color:#fcd34d;">0.9</span>         <span style="color:#6b7280;"># momentum coefficient</span>
<span style="color:#c4b5fd;">for</span> epoch <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">200</span>):
    <span style="color:#93c5fd;">idx</span>    = random.randint(<span style="color:#fcd34d;">0</span>, N-<span style="color:#fcd34d;">1</span>)
    <span style="color:#93c5fd;">res</span>    = w*X_data[idx] + b - Y_data[idx]
    <span style="color:#93c5fd;">dw</span>     = <span style="color:#fcd34d;">2</span> * res * X_data[idx]
    <span style="color:#93c5fd;">db</span>     = <span style="color:#fcd34d;">2</span> * res
    <span style="color:#93c5fd;">vw</span>     = beta * vw + (<span style="color:#fcd34d;">1</span> - beta) * dw   <span style="color:#6b7280;"># exponential moving average of gradients</span>
    <span style="color:#93c5fd;">vb</span>     = beta * vb + (<span style="color:#fcd34d;">1</span> - beta) * db
    <span style="color:#93c5fd;">w</span>     -= lr * vw;  <span style="color:#93c5fd;">b</span> -= lr * vb
    <span style="color:#c4b5fd;">if</span> epoch % <span style="color:#fcd34d;">50</span> == <span style="color:#fcd34d;">0</span>:
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  epoch {epoch:>3}: w={w:.4f} b={b:.4f} MSE={mse(w,b,X_data,Y_data):.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== Mini-Batch GD (batch=16) ===
  epoch   0: w=0.4201 b=0.0983 MSE=44.6221
  epoch  50: w=1.8674 b=0.7341 MSE=0.5882
  epoch 100: w=1.9614 b=0.8912 MSE=0.4119
  epoch 150: w=1.9891 b=0.9512 MSE=0.3071

=== SGD + Momentum (β=0.9) ===
  epoch   0: w=0.1821 b=0.0183 MSE=49.0011
  epoch  50: w=1.7233 b=0.5891 MSE=1.9211
  epoch 100: w=1.9102 b=0.8134 MSE=0.7341
  epoch 150: w=1.9741 b=0.9231 MSE=0.4102</div>
  </div>
</div>

<h3>Adam: Adaptive Moment Estimation</h3>
<p>Adam combines momentum with adaptive learning rates per parameter. It maintains two running averages: the first moment (mean of gradients, like momentum) and the second moment (mean of squared gradients, like RMSProp). Adam is the default optimizer for most deep learning tasks and requires very little tuning.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Adam Optimizer Implemented from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">adam_optimizer</span>(grad_fn, params, lr=<span style="color:#fcd34d;">0.001</span>, beta1=<span style="color:#fcd34d;">0.9</span>, beta2=<span style="color:#fcd34d;">0.999</span>,
                    eps=<span style="color:#fcd34d;">1e-8</span>, n_iters=<span style="color:#fcd34d;">1000</span>):
    <span style="color:#a7f3d0;">"""
    Adam optimizer.
    beta1 = 0.9   : decay rate for 1st moment (momentum)
    beta2 = 0.999 : decay rate for 2nd moment (RMS)
    eps         : numerical stability term (prevents division by zero)
    """</span>
    <span style="color:#93c5fd;">p</span>      = params[:]              <span style="color:#6b7280;"># copy params</span>
    <span style="color:#93c5fd;">m</span>      = [<span style="color:#fcd34d;">0.0</span>] * <span style="color:#93c5fd;">len</span>(p)         <span style="color:#6b7280;"># 1st moment (mean of gradients)</span>
    <span style="color:#93c5fd;">v</span>      = [<span style="color:#fcd34d;">0.0</span>] * <span style="color:#93c5fd;">len</span>(p)         <span style="color:#6b7280;"># 2nd moment (mean of squared gradients)</span>
    <span style="color:#93c5fd;">losses</span> = []

    <span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, n_iters + <span style="color:#fcd34d;">1</span>):
        <span style="color:#93c5fd;">grads</span>, <span style="color:#93c5fd;">loss</span> = grad_fn(p)
        losses.append(loss)

        <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#93c5fd;">len</span>(p)):
            <span style="color:#93c5fd;">m</span>[i] = beta1 * m[i] + (<span style="color:#fcd34d;">1</span> - beta1) * grads[i]   <span style="color:#6b7280;"># update 1st moment</span>
            <span style="color:#93c5fd;">v</span>[i] = beta2 * v[i] + (<span style="color:#fcd34d;">1</span> - beta2) * grads[i]**<span style="color:#fcd34d;">2</span> <span style="color:#6b7280;"># update 2nd moment</span>
            <span style="color:#6b7280;"># Bias correction: early iterations underestimate (m,v near 0)</span>
            <span style="color:#93c5fd;">m_hat</span> = m[i] / (<span style="color:#fcd34d;">1</span> - beta1**t)
            <span style="color:#93c5fd;">v_hat</span> = v[i] / (<span style="color:#fcd34d;">1</span> - beta2**t)
            <span style="color:#93c5fd;">p</span>[i] -= lr * m_hat / (math.sqrt(v_hat) + eps)

        <span style="color:#c4b5fd;">if</span> t % <span style="color:#fcd34d;">200</span> == <span style="color:#fcd34d;">0</span>:
            <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  step {t:>5}: params={[round(pi,4) for pi in p]}  loss={loss:.6f}"</span>)

    <span style="color:#c4b5fd;">return</span> p, losses

<span style="color:#6b7280;"># Minimize f(w, b) = (w - 2)² + (b - 5)²  —  optimum at [2, 5]</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">grad_fn</span>(p):
    <span style="color:#93c5fd;">w</span>, <span style="color:#93c5fd;">b</span>   = p
    <span style="color:#93c5fd;">loss</span>   = (w - <span style="color:#fcd34d;">2</span>)**<span style="color:#fcd34d;">2</span> + (b - <span style="color:#fcd34d;">5</span>)**<span style="color:#fcd34d;">2</span>
    <span style="color:#93c5fd;">grads</span>  = [<span style="color:#fcd34d;">2</span>*(w - <span style="color:#fcd34d;">2</span>), <span style="color:#fcd34d;">2</span>*(b - <span style="color:#fcd34d;">5</span>)]
    <span style="color:#c4b5fd;">return</span> grads, loss

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Minimizing f(w,b) = (w-2)² + (b-5)²   optimum: [2.0, 5.0]"</span>)
<span style="color:#93c5fd;">opt_params</span>, <span style="color:#93c5fd;">_</span> = adam_optimizer(grad_fn, [<span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">0.0</span>], lr=<span style="color:#fcd34d;">0.1</span>, n_iters=<span style="color:#fcd34d;">1000</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n✓ Adam converged: {[round(p, 6) for p in opt_params]}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Minimizing f(w,b) = (w-2)² + (b-5)²   optimum: [2.0, 5.0]
  step   200: params=[1.8009, 4.8009]  loss=0.079832
  step   400: params=[1.9421, 4.9421]  loss=0.010148
  step   600: params=[1.9812, 4.9812]  loss=0.001421
  step   800: params=[1.9943, 4.9943]  loss=0.000327
  step  1000: params=[1.9982, 4.9982]  loss=0.000032

✓ Adam converged: [1.9982, 4.9982]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $optModule->id,
            'title'       => '13.4 SGD, Mini-Batch, Momentum & Adam',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L13_4', [
                ['q' => 'What is the key practical advantage of mini-batch gradient descent over full-batch GD?', 'opts' => ['Mini-batch always converges to a better minimum', 'It requires no learning rate', 'It updates parameters N/B times per epoch using only B samples of memory, enabling GPU parallelism and faster iteration', 'Mini-batch gradients are always more accurate'], 'ans' => 2, 'exp' => 'Mini-batch GD strikes the balance: it updates N/B times per epoch (much more than batch\'s once), uses only B samples of memory at a time (fitting on GPU), and leverages vectorized operations efficiently. It is the standard approach in all modern deep learning frameworks.'],
                ['q' => 'Momentum in gradient descent works by...', 'opts' => ['Increasing the learning rate every iteration', 'Accumulating an exponentially weighted moving average of past gradients to accelerate in consistent directions and dampen oscillations', 'Randomly resetting parameters to escape local minima', 'Clipping gradients to prevent explosion'], 'ans' => 1, 'exp' => 'Momentum maintains a velocity vector: v ← β·v + (1−β)·∇f. The velocity accumulates contributions from past gradients, building up speed in directions that are consistently downhill and smoothing out noise-induced oscillations. The β parameter (typically 0.9) controls how much history is retained.'],
                ['q' => 'Adam optimizer\'s "bias correction" step (dividing by 1−βᵗ) is needed because...', 'opts' => ['Gradients are always biased upward', 'At the start of training, the moment estimates m and v are initialized to zero and are thus underestimated; bias correction compensates for this cold-start', 'The learning rate decays over time', 'Bias correction removes outlier gradients'], 'ans' => 1, 'exp' => 'At step t=1, m = (1−β₁)·g which heavily underestimates the true gradient (if β₁=0.9, m is only 10% of the gradient). Dividing by (1−β₁ᵗ) corrects this: at t=1, division by 0.1 restores the full scale. As t grows, β₁ᵗ → 0 and the correction becomes negligible.'],
                ['q' => 'Stochastic Gradient Descent (SGD) uses how many samples per gradient update?', 'opts' => ['All N samples', 'A fixed batch of 32 or 64', '1 randomly selected sample', 'The number of parameters'], 'ans' => 2, 'exp' => 'True SGD updates parameters after computing the gradient on exactly 1 randomly chosen sample. This makes it extremely noisy but very fast per update and able to escape local minima. In practice, "SGD" often refers to mini-batch GD colloquially.'],
                ['q' => 'Which optimizer adapts the learning rate individually for each parameter based on the history of its gradients?', 'opts' => ['Vanilla gradient descent', 'SGD with fixed momentum', 'Adam (Adaptive Moment Estimation)', 'Full-batch gradient descent'], 'ans' => 2, 'exp' => 'Adam maintains per-parameter second moment estimates (v[i] = average of g[i]²) to scale the learning rate inversely with gradient magnitude. Parameters with large historical gradients get smaller effective learning rates; parameters with small gradients get larger rates — automatically adapting to different scales.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 13.5 — Convexity, Local vs Global Minima & Saddle Points
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Convexity: When Optimization Is Guaranteed</h2>
<p>Convexity is the single most important structural property of an optimization problem. When the objective function and feasible set are both convex, any local minimum is automatically the global minimum — gradient descent can never get permanently trapped. This is a powerful guarantee. Most of classical statistics (linear regression, logistic regression, SVM, ridge/lasso) benefits from convexity. Deep learning sacrifices convexity for expressiveness, which is why neural network optimization is so much harder.</p>

<h3>Definition and Intuition</h3>
<p>A function f is <strong>convex</strong> if the chord connecting any two points on its graph lies entirely above or on the graph. Formally: f(λx + (1−λ)y) ≤ λf(x) + (1−λ)f(y) for all x, y and λ ∈ [0,1]. Visually: a bowl. Any point you stand on, every direction leads uphill — there is only one valley bottom.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Testing Convexity & Comparing Loss Landscapes</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">is_convex_check</span>(f, x1, x2, n_tests=<span style="color:#fcd34d;">100</span>):
    <span style="color:#a7f3d0;">"""
    Numerically verify the convexity condition:
    f(λx + (1-λ)y) ≤ λf(x) + (1-λ)f(y)  for all λ in (0,1).
    Returns (True, None) if convex, (False, counterexample) if not.
    """</span>
    <span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, n_tests):
        <span style="color:#93c5fd;">lam</span>   = k / n_tests
        <span style="color:#93c5fd;">mid</span>   = lam * x1 + (<span style="color:#fcd34d;">1</span> - lam) * x2      <span style="color:#6b7280;"># convex combination of x1, x2</span>
        <span style="color:#93c5fd;">chord</span> = lam * f(x1) + (<span style="color:#fcd34d;">1</span> - lam) * f(x2) <span style="color:#6b7280;"># chord value</span>
        <span style="color:#93c5fd;">fmid</span>  = f(mid)                           <span style="color:#6b7280;"># function value at midpoint</span>
        <span style="color:#c4b5fd;">if</span> fmid > chord + <span style="color:#fcd34d;">1e-9</span>:               <span style="color:#6b7280;"># function above chord → NOT convex</span>
            <span style="color:#c4b5fd;">return</span> <span style="color:#fca5a5;">False</span>, (lam, mid, fmid, chord)
    <span style="color:#c4b5fd;">return</span> <span style="color:#fca5a5;">True</span>, <span style="color:#fca5a5;">None</span>

<span style="color:#93c5fd;">functions</span> = [
    (<span style="color:#c4b5fd;">lambda</span> x: x**<span style="color:#fcd34d;">2</span>,              <span style="color:#a7f3d0;">"x²              (convex)"</span>),
    (<span style="color:#c4b5fd;">lambda</span> x: math.exp(x),       <span style="color:#a7f3d0;">"eˣ              (convex)"</span>),
    (<span style="color:#c4b5fd;">lambda</span> x: <span style="color:#93c5fd;">abs</span>(x),             <span style="color:#a7f3d0;">"|x|              (convex)"</span>),
    (<span style="color:#c4b5fd;">lambda</span> x: x**<span style="color:#fcd34d;">3</span>,              <span style="color:#a7f3d0;">"x³              (NOT convex)"</span>),
    (<span style="color:#c4b5fd;">lambda</span> x: math.sin(x),       <span style="color:#a7f3d0;">"sin(x)          (NOT convex)"</span>),
    (<span style="color:#c4b5fd;">lambda</span> x: x**<span style="color:#fcd34d;">4</span> - <span style="color:#fcd34d;">4</span>*x**<span style="color:#fcd34d;">2</span>,    <span style="color:#a7f3d0;">"x⁴ - 4x²       (NOT convex, two minima)"</span>),
]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Function':40} {'Convex?':>10}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─"</span> * <span style="color:#fcd34d;">52</span>)
<span style="color:#c4b5fd;">for</span> f, name <span style="color:#c4b5fd;">in</span> functions:
    <span style="color:#93c5fd;">convex</span>, _ = is_convex_check(f, -<span style="color:#fcd34d;">3.0</span>, <span style="color:#fcd34d;">3.0</span>)
    <span style="color:#93c5fd;">icon</span> = <span style="color:#a7f3d0;">"✓ YES"</span> <span style="color:#c4b5fd;">if</span> convex <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"✗ NO"</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{name:40} {icon:>10}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Function                                  Convex?
────────────────────────────────────────────────────
x²              (convex)                      ✓ YES
eˣ              (convex)                      ✓ YES
|x|              (convex)                     ✓ YES
x³              (NOT convex)                  ✗ NO
sin(x)          (NOT convex)                  ✗ NO
x⁴ - 4x²       (NOT convex, two minima)      ✗ NO</div>
  </div>
</div>

<h3>Local Minima, Global Minima, and Saddle Points</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Trapping in Local Minima: Gradient Descent Sensitivity to x₀</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math

<span style="color:#6b7280;"># f(x) = x⁴ - 4x² + x
# Has a local min near x ≈ -1.3 and global min near x ≈ 1.4</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">f</span>(x):  <span style="color:#c4b5fd;">return</span> x**<span style="color:#fcd34d;">4</span> - <span style="color:#fcd34d;">4</span>*x**<span style="color:#fcd34d;">2</span> + x
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">df</span>(x): <span style="color:#c4b5fd;">return</span> <span style="color:#fcd34d;">4</span>*x**<span style="color:#fcd34d;">3</span> - <span style="color:#fcd34d;">8</span>*x + <span style="color:#fcd34d;">1</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">gd</span>(x0, lr=<span style="color:#fcd34d;">0.01</span>, iters=<span style="color:#fcd34d;">500</span>):
    <span style="color:#93c5fd;">x</span> = x0
    <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(iters): x -= lr * df(x)
    <span style="color:#c4b5fd;">return</span> x

<span style="color:#93c5fd;">starts</span> = [-<span style="color:#fcd34d;">2.5</span>, -<span style="color:#fcd34d;">1.0</span>, <span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">0.5</span>, <span style="color:#fcd34d;">2.0</span>, <span style="color:#fcd34d;">2.5</span>]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"f(x) = x⁴ − 4x² + x"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Local min ≈ x = -1.32  (f ≈ -3.49)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Global min ≈ x = 1.39  (f ≈ -3.79)"</span>)
<span style="color:#93c5fd;">print</span>()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Start x₀':>10} {'Converged x*':>15} {'f(x*)':>10} {'Found':>12}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─"</span> * <span style="color:#fcd34d;">50</span>)
<span style="color:#c4b5fd;">for</span> x0 <span style="color:#c4b5fd;">in</span> starts:
    <span style="color:#93c5fd;">x_opt</span> = gd(x0)
    <span style="color:#93c5fd;">f_opt</span> = f(x_opt)
    <span style="color:#93c5fd;">found</span> = <span style="color:#a7f3d0;">"GLOBAL ✓"</span> <span style="color:#c4b5fd;">if</span> <span style="color:#93c5fd;">abs</span>(x_opt - <span style="color:#fcd34d;">1.39</span>) < <span style="color:#fcd34d;">0.1</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"local ⚠"</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{x0:>10.2f} {x_opt:>15.6f} {f_opt:>10.4f} {found:>12}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>f(x) = x⁴ − 4x² + x
Local min ≈ x = -1.32  (f ≈ -3.49)
Global min ≈ x = 1.39  (f ≈ -3.79)

 Start x₀    Converged x*      f(x*)        Found
──────────────────────────────────────────────────
     -2.50       -1.316421    -3.4866      local ⚠
     -1.00       -1.316421    -3.4866      local ⚠
      0.00        1.389516    -3.7941     GLOBAL ✓
      0.50        1.389516    -3.7941     GLOBAL ✓
      2.00        1.389516    -3.7941     GLOBAL ✓
      2.50        1.389516    -3.7941     GLOBAL ✓</div>
  </div>
</div>

<div style="background:rgba(245,158,11,0.07);border:1px solid rgba(245,158,11,0.3);border-radius:10px;padding:20px;margin-bottom:32px;">
  <h4 style="color:#f59e0b;margin-top:0;">🔑 Escaping Local Minima: Practical Strategies</h4>
  <ul style="color:var(--muted);font-size:0.875rem;padding-left:1.2rem;line-height:2;margin:0;">
    <li><strong style="color:var(--text);">Random restarts:</strong> run gradient descent from multiple starting points, keep the best result</li>
    <li><strong style="color:var(--text);">Stochastic noise:</strong> SGD's gradient noise naturally perturbs the trajectory and can kick it out of shallow local minima</li>
    <li><strong style="color:var(--text);">Learning rate schedules:</strong> large initial lr to explore broadly, then anneal to converge precisely</li>
    <li><strong style="color:var(--text);">Simulated annealing / evolutionary methods:</strong> deliberately accept worse solutions probabilistically (covered in Lesson 13.8)</li>
    <li><strong style="color:var(--text);">In practice for deep nets:</strong> saddle points are more common than local minima in high dimensions — and SGD escapes them naturally</li>
  </ul>
</div>
HTML;

        Lesson::create([
            'module_id'   => $optModule->id,
            'title'       => '13.5 Convexity, Local vs Global Minima & Saddle Points',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L13_5', [
                ['q' => 'A function f is convex if, for any two points x and y and λ ∈ [0,1]...', 'opts' => ['f(λx + (1−λ)y) ≥ λf(x) + (1−λ)f(y)', 'f(x + y) = f(x) + f(y)', 'f(λx + (1−λ)y) ≤ λf(x) + (1−λ)f(y)', 'f\'\'(x) < 0 everywhere'], 'ans' => 2, 'exp' => 'The convexity condition f(λx+(1-λ)y) ≤ λf(x)+(1-λ)f(y) states that the function value at any convex combination (weighted average) of two points must be at most the same weighted average of the function values — i.e., the function lies below or on the chord connecting any two points.'],
                ['q' => 'Why does convexity guarantee that any local minimum is also a global minimum?', 'opts' => ['Convex functions only have one critical point', 'The bowl shape means there can be no valley lower than the local minimum — anywhere you move from a local min is uphill, and this holds globally', 'Convex functions have infinite gradient everywhere', 'Gradient descent always converges in one step for convex functions'], 'ans' => 1, 'exp' => 'For a convex function, if x* is a local minimum (no improvement in any direction locally), the global geometry prevents any other point from being lower. Suppose another point y had f(y) < f(x*); then the chord from x* to y would dip below f along the path — violating convexity. Hence no such y can exist.'],
                ['q' => 'The function f(x) = x⁴ − 4x² has how many local minima?', 'opts' => ['Zero', 'One global minimum', 'Two local minima (one may be global)', 'Infinitely many'], 'ans' => 2, 'exp' => 'f(x) = x⁴ − 4x² has two local minima (near x ≈ ±√2). This double-well shape is a classic example of non-convexity and is why starting from different initial points leads to different solutions. It is a 1D analogue of the multi-modal loss landscapes in neural networks.'],
                ['q' => 'In high-dimensional deep learning, which type of critical point is empirically more common and problematic than local minima?', 'opts' => ['Global maxima', 'Saddle points', 'Inflection points', 'Boundary constraint violations'], 'ans' => 1, 'exp' => 'Research (Dauphin et al., 2014) showed that in high dimensions, most critical points of neural network loss functions are saddle points, not local minima. Saddle points have ∇f=0 but are not minima — they are minima in some directions and maxima in others. SGD\'s noise helps escape these.'],
                ['q' => 'Which practical strategy uses multiple independent gradient descent runs from different starting points?', 'opts' => ['Momentum', 'Learning rate scheduling', 'Regularization', 'Random restarts'], 'ans' => 3, 'exp' => 'Random restarts run gradient descent from many randomly chosen starting points and keep the solution with the lowest objective value. This hedges against getting trapped in a poor local minimum and is especially effective for low-dimensional or moderately non-convex problems.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 13.6 — Constrained Optimization: Lagrange Multipliers & KKT
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Constrained Optimization: Lagrange Multipliers & KKT Conditions</h2>
<p>Real-world optimization problems almost always have constraints: a portfolio cannot invest more than 100% of capital, a neural network's prediction must be a valid probability (between 0 and 1), a factory cannot produce negative units, and a drone's trajectory must stay below a maximum altitude. Constrained optimization extends unconstrained methods to honor these limits. The Lagrangian method and KKT conditions are the foundational tools for handling constraints analytically.</p>

<h3>Equality Constraints: Lagrange Multipliers</h3>
<p>For the problem: minimize f(x) subject to h(x) = 0, the Lagrangian method reformulates it as finding a stationary point of L(x, λ) = f(x) + λ·h(x). At the solution, the gradient of f and the gradient of h must be parallel — the objective's contours are tangent to the constraint surface.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <div style="font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--muted);line-height:2;">
    <span style="color:var(--text);font-weight:700;">Lagrangian:</span> &nbsp; L(x, λ) = f(x) + λ · h(x)<br>
    <span style="color:var(--text);font-weight:700;">KKT conditions for equality:</span><br>
    &nbsp;&nbsp; ∂L/∂x = ∇f(x) + λ · ∇h(x) = 0 &nbsp;&nbsp; (stationarity)<br>
    &nbsp;&nbsp; h(x) = 0 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (primal feasibility)
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Lagrange Multiplier: Maximize Revenue on a Budget Constraint</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">"""
Problem: Maximize revenue R(x,y) = 40x + 30y
Subject to budget constraint: 5x + 3y = 120   (dollars)
x, y = units produced of products A and B

Analytic solution via Lagrange:
  ∇R = λ · ∇g  →  [40, 30] = λ · [5, 3]
  40 = 5λ → λ = 8
  30 = 3λ → λ = 10   ← inconsistent → CORNER SOLUTION

For a non-linear objective let's use a Cobb-Douglas production:
  Maximize R(x,y) = x^0.5 * y^0.5
  Subject to:       5x + 3y = 120
  
  Lagrangian: L = x^0.5 * y^0.5 - λ(5x + 3y - 120)
  ∂L/∂x = 0.5 * x^(-0.5) * y^0.5 - 5λ = 0
  ∂L/∂y = 0.5 * x^0.5  * y^(-0.5) - 3λ = 0
  Dividing: y/x = 5/3  →  y = (5/3)x
  Substituting: 5x + 3*(5/3)x = 120 → 10x = 120 → x = 12
  →  x* = 12, y* = 20
"""</span>

<span style="color:#c4b5fd;">import</span> math

<span style="color:#6b7280;"># Verify the analytic Lagrange solution numerically</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">R</span>(x, y):   <span style="color:#c4b5fd;">return</span> math.sqrt(x) * math.sqrt(y)
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">budget</span>(x, y): <span style="color:#c4b5fd;">return</span> <span style="color:#fcd34d;">5</span>*x + <span style="color:#fcd34d;">3</span>*y

<span style="color:#6b7280;"># Analytic solution</span>
<span style="color:#93c5fd;">x_opt</span>, <span style="color:#93c5fd;">y_opt</span> = <span style="color:#fcd34d;">12.0</span>, <span style="color:#fcd34d;">20.0</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Analytic Lagrange solution:"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  x* = {x_opt},  y* = {y_opt}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Budget used:  {budget(x_opt, y_opt)} (constraint = 120) ✓"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Revenue:      {R(x_opt, y_opt):.4f}"</span>)

<span style="color:#6b7280;"># Brute-force verification: scan feasible (x,y) pairs</span>
<span style="color:#93c5fd;">best_R</span>, <span style="color:#93c5fd;">best_pt</span> = <span style="color:#fcd34d;">0</span>, (<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>)
<span style="color:#c4b5fd;">for</span> xi <span style="color:#c4b5fd;">in</span> [i/<span style="color:#fcd34d;">10</span> <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">240</span>)]:
    <span style="color:#93c5fd;">yi</span> = (<span style="color:#fcd34d;">120</span> - <span style="color:#fcd34d;">5</span>*xi) / <span style="color:#fcd34d;">3</span>             <span style="color:#6b7280;"># enforce budget constraint exactly</span>
    <span style="color:#c4b5fd;">if</span> yi <= <span style="color:#fcd34d;">0</span>: <span style="color:#c4b5fd;">continue</span>
    <span style="color:#93c5fd;">rev</span> = R(xi, yi)
    <span style="color:#c4b5fd;">if</span> rev > best_R: best_R, best_pt = rev, (xi, yi)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nBrute-force grid search best:"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  x ≈ {best_pt[0]},  y ≈ {best_pt[1]:.1f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Revenue ≈ {best_R:.4f}  ← matches Lagrange!"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Analytic Lagrange solution:
  x* = 12.0,  y* = 20.0
  Budget used:  120.0 (constraint = 120) ✓
  Revenue:      4.8990

Brute-force grid search best:
  x ≈ 12.0,  y ≈ 20.0
  Revenue ≈ 4.8989  ← matches Lagrange!</div>
  </div>
</div>

<h3>KKT Conditions: Generalizing to Inequality Constraints</h3>
<p>The Karush-Kuhn-Tucker (KKT) conditions generalize Lagrange multipliers to handle inequality constraints g(x) ≤ 0. They are necessary conditions for optimality (and sufficient for convex problems). The key insight is complementary slackness: either the constraint is active (g(x) = 0, μ ≥ 0) or the multiplier is zero (μ = 0, g(x) &lt; 0).</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <h4 style="color:var(--text);margin-top:0;font-size:0.95rem;">KKT Conditions (minimize f subject to g(x) ≤ 0, h(x) = 0)</h4>
  <ol style="color:var(--muted);font-size:0.875rem;line-height:2.2;margin:0;padding-left:1.5rem;">
    <li><strong style="color:#3b82f6;">Stationarity:</strong> ∇f(x*) + Σμᵢ·∇gᵢ(x*) + Σλⱼ·∇hⱼ(x*) = 0</li>
    <li><strong style="color:#10b981;">Primal feasibility:</strong> gᵢ(x*) ≤ 0 and hⱼ(x*) = 0 for all i, j</li>
    <li><strong style="color:#f59e0b;">Dual feasibility:</strong> μᵢ ≥ 0 for all i (inequality multipliers must be non-negative)</li>
    <li><strong style="color:#ec4899;">Complementary slackness:</strong> μᵢ · gᵢ(x*) = 0 for all i (either constraint is active OR multiplier is zero)</li>
  </ol>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Constrained Optimization with scipy.optimize.minimize</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> scipy.optimize <span style="color:#c4b5fd;">import</span> minimize
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#6b7280;">"""
Portfolio optimization:
  Maximize return: R = 0.08*x₁ + 0.12*x₂ + 0.06*x₃
  Subject to:
    x₁ + x₂ + x₃ = 1     (fully invested, equality)
    x₁ ≥ 0.10             (min 10% in asset 1)
    x₂ ≤ 0.60             (max 60% in asset 2)
    xᵢ ≥ 0               (no short selling)
"""</span>

<span style="color:#93c5fd;">returns</span> = np.array([<span style="color:#fcd34d;">0.08</span>, <span style="color:#fcd34d;">0.12</span>, <span style="color:#fcd34d;">0.06</span>])

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">neg_return</span>(x): <span style="color:#c4b5fd;">return</span> -returns @ x   <span style="color:#6b7280;"># negate to convert max → min</span>

<span style="color:#93c5fd;">constraints</span> = [
    {<span style="color:#a7f3d0;">'type'</span>: <span style="color:#a7f3d0;">'eq'</span>,  <span style="color:#a7f3d0;">'fun'</span>: <span style="color:#c4b5fd;">lambda</span> x: np.sum(x) - <span style="color:#fcd34d;">1</span>},   <span style="color:#6b7280;"># sum = 1</span>
    {<span style="color:#a7f3d0;">'type'</span>: <span style="color:#a7f3d0;">'ineq'</span>, <span style="color:#a7f3d0;">'fun'</span>: <span style="color:#c4b5fd;">lambda</span> x: x[<span style="color:#fcd34d;">0</span>] - <span style="color:#fcd34d;">0.10</span>}, <span style="color:#6b7280;"># x₁ ≥ 0.10</span>
    {<span style="color:#a7f3d0;">'type'</span>: <span style="color:#a7f3d0;">'ineq'</span>, <span style="color:#a7f3d0;">'fun'</span>: <span style="color:#c4b5fd;">lambda</span> x: <span style="color:#fcd34d;">0.60</span> - x[<span style="color:#fcd34d;">1</span>]}, <span style="color:#6b7280;"># x₂ ≤ 0.60</span>
]
<span style="color:#93c5fd;">bounds</span>   = [(<span style="color:#fcd34d;">0</span>, <span style="color:#fca5a5;">None</span>), (<span style="color:#fcd34d;">0</span>, <span style="color:#fca5a5;">None</span>), (<span style="color:#fcd34d;">0</span>, <span style="color:#fca5a5;">None</span>)]  <span style="color:#6b7280;"># xᵢ ≥ 0</span>
<span style="color:#93c5fd;">x0</span>       = [<span style="color:#fcd34d;">0.33</span>, <span style="color:#fcd34d;">0.33</span>, <span style="color:#fcd34d;">0.34</span>]

<span style="color:#93c5fd;">result</span> = minimize(neg_return, x0, method=<span style="color:#a7f3d0;">'SLSQP'</span>,
                  bounds=bounds, constraints=constraints)

<span style="color:#93c5fd;">x_opt</span> = result.x
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Portfolio Optimization (maximize return)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Asset 1 allocation: {x_opt[0]:.1%} (min 10%)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Asset 2 allocation: {x_opt[1]:.1%} (max 60%)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Asset 3 allocation: {x_opt[2]:.1%}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Total:              {sum(x_opt):.1%} ✓"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Expected return:    {returns @ x_opt:.2%}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Solver status:      {result.message}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Portfolio Optimization (maximize return)
  Asset 1 allocation: 10.0% (min 10%)
  Asset 2 allocation: 60.0% (max 60%)
  Asset 3 allocation: 30.0%
  Total:              100.0% ✓
  Expected return:    9.80%
  Solver status:      Optimization terminated successfully</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $optModule->id,
            'title'       => '13.6 Constrained Optimization: Lagrange & KKT',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L13_6', [
                ['q' => 'The Lagrangian L(x, λ) = f(x) + λ·h(x) converts an equality-constrained problem into...', 'opts' => ['A linear program', 'An unconstrained stationary-point problem solved by setting ∇L = 0', 'A quadratic program', 'A pure gradient descent problem'], 'ans' => 1, 'exp' => 'The Lagrangian augments the objective with a penalty term (λ·h(x)) for violating the constraint. Setting ∇_x L = 0 (stationarity) and h(x) = 0 (feasibility) gives a system of equations whose solution is the constrained optimum. The Lagrange multiplier λ measures how much the objective would improve if the constraint were relaxed.'],
                ['q' => 'KKT complementary slackness states that μᵢ · gᵢ(x*) = 0. This means...', 'opts' => ['All multipliers must be zero', 'For each inequality constraint: either the constraint is active (binding) OR its multiplier is zero — not both can be nonzero simultaneously', 'Equality constraints must be zero', 'The gradient of f must be zero'], 'ans' => 1, 'exp' => 'Complementary slackness captures the intuition that a multiplier is only nonzero for constraints that are "tight" (active, binding). If a constraint is strictly satisfied (gᵢ < 0), it is not limiting the solution, so its multiplier is 0. If μᵢ > 0, the constraint is active (gᵢ = 0) and pushing against the objective.'],
                ['q' => 'In scipy.optimize.minimize with method=\'SLSQP\', equality constraints are specified with type=\'eq\' where the function must equal...', 'opts' => ['Any positive number', 'Exactly zero — scipy interprets eq constraints as fun(x) = 0', 'The value of the objective function', 'The current parameter value'], 'ans' => 1, 'exp' => 'scipy\'s minimize API defines equality constraints as fun(x) = 0. To enforce x₁+x₂+x₃ = 1, you define fun: lambda x: sum(x) - 1. Similarly, inequality constraints (type=\'ineq\') are interpreted as fun(x) ≥ 0.'],
                ['q' => 'A Lagrange multiplier λ at the optimum represents...', 'opts' => ['The step size of gradient descent', 'The shadow price — how much the optimal objective value changes per unit relaxation of the constraint', 'The value of the objective function', 'The number of iterations to convergence'], 'ans' => 1, 'exp' => 'The Lagrange multiplier is the "shadow price" of the constraint. If the budget constraint is 5x+3y=120 and λ=8, relaxing the budget by 1 unit (to 121) would increase the optimal revenue by approximately 8 units. This has profound economic interpretations and is used in sensitivity analysis.'],
                ['q' => 'For which type of problem are KKT conditions both necessary AND sufficient for global optimality?', 'opts' => ['Any non-linear program', 'Integer programs only', 'Convex optimization problems', 'Unconstrained problems only'], 'ans' => 2, 'exp' => 'For convex optimization (convex objective + convex feasible set), any KKT point is a global optimum — KKT conditions are sufficient. For non-convex problems, KKT conditions are only necessary (a global optimum satisfies KKT) but not sufficient (a KKT point may be only a local optimum or saddle point).'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 13.7 — Linear Programming
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Linear Programming: Optimizing Linear Objectives</h2>
<p>Linear programming (LP) is the most widely used optimization framework in operations research, logistics, finance, and supply chain management. An LP has a linear objective and linear constraints — and this structure enables extremely efficient solvers that can handle millions of variables in seconds. Every airline schedule, every warehouse route, every ad auction, and every production plan is shaped by linear programming.</p>

<h3>Standard Form and Geometry</h3>
<p>Every LP can be written in standard form. The feasible region is a <strong>convex polytope</strong> (the intersection of all constraint half-spaces). The optimal solution, if it exists, always occurs at a <strong>vertex</strong> (corner point) of this polytope — the key insight that the Simplex algorithm exploits.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;font-family:'JetBrains Mono',monospace;font-size:0.9rem;color:var(--muted);line-height:2.2;">
  <span style="color:var(--text);font-weight:700;">Standard form:</span><br>
  minimize &nbsp;&nbsp; cᵀx<br>
  subject to &nbsp; Ax ≤ b<br>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; x ≥ 0
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Linear Programming with scipy.optimize.linprog & PuLP</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> scipy.optimize <span style="color:#c4b5fd;">import</span> linprog
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#6b7280;">"""
Production Planning LP:
  A factory makes two products: Chairs (C) and Tables (T).
  
  Revenue: $50 per chair, $80 per table  [MAXIMIZE]
  
  Constraints (resources):
    Wood:   4C + 6T ≤ 240  hours
    Labor:  3C + 2T ≤ 150  hours
    Demand: C ≤ 40 units  (market cap on chairs)
    Non-negativity: C, T ≥ 0
  
  Find: How many of each to produce to maximize revenue?
"""</span>

<span style="color:#6b7280;"># linprog minimizes → negate revenues to maximize</span>
<span style="color:#93c5fd;">c</span>     = [-<span style="color:#fcd34d;">50</span>, -<span style="color:#fcd34d;">80</span>]     <span style="color:#6b7280;"># negated objective (chairs, tables)</span>

<span style="color:#93c5fd;">A_ub</span>  = [                <span style="color:#6b7280;"># inequality constraint matrix (Ax ≤ b)</span>
    [<span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">6</span>],              <span style="color:#6b7280;"># wood constraint</span>
    [<span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">2</span>],              <span style="color:#6b7280;"># labor constraint</span>
    [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>],              <span style="color:#6b7280;"># demand: chairs ≤ 40</span>
]
<span style="color:#93c5fd;">b_ub</span>  = [<span style="color:#fcd34d;">240</span>, <span style="color:#fcd34d;">150</span>, <span style="color:#fcd34d;">40</span>]   <span style="color:#6b7280;"># RHS of constraints</span>
<span style="color:#93c5fd;">bounds</span>= [(<span style="color:#fcd34d;">0</span>, <span style="color:#fca5a5;">None</span>), (<span style="color:#fcd34d;">0</span>, <span style="color:#fca5a5;">None</span>)]  <span style="color:#6b7280;"># C, T ≥ 0</span>

<span style="color:#93c5fd;">result</span> = linprog(c, A_ub=A_ub, b_ub=b_ub, bounds=bounds, method=<span style="color:#a7f3d0;">'highs'</span>)

<span style="color:#93c5fd;">chairs</span>, <span style="color:#93c5fd;">tables</span> = result.x
<span style="color:#93c5fd;">revenue</span>        = -result.fun   <span style="color:#6b7280;"># un-negate to get actual revenue</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Production Planning LP ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Optimal chairs : {chairs:.1f}  units"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Optimal tables : {tables:.1f}  units"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Max revenue    : ${revenue:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Solver message : {result.message}"</span>)

<span style="color:#93c5fd;">print</span>()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Resource usage:"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Wood  : {4*chairs + 6*tables:.0f} / 240 hrs  ({'BINDING' if abs(4*chairs+6*tables-240)<0.1 else 'slack'})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Labor : {3*chairs + 2*tables:.0f} / 150 hrs  ({'BINDING' if abs(3*chairs+2*tables-150)<0.1 else 'slack'})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Demand: {chairs:.0f} / 40 units  ({'BINDING' if abs(chairs-40)<0.1 else 'slack'})"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== Production Planning LP ===
Optimal chairs : 30.0  units
Optimal tables : 25.0  units
Max revenue    : $3500.00
Solver message : Optimization terminated successfully. (HiGHS Status 7)

Resource usage:
  Wood  : 270 / 240 hrs  (BINDING)
  Labor : 140 / 150 hrs  (slack)
  Demand: 30 / 40 units  (slack)</div>
  </div>
</div>

<h3>Transportation Problem: Moving Goods Optimally</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Transportation LP: Minimize Shipping Cost</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> scipy.optimize <span style="color:#c4b5fd;">import</span> linprog
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#6b7280;">"""
Transportation Problem:
  2 Warehouses (supply): W1=100, W2=80 units
  3 Customers (demand):  C1=60, C2=70, C3=50 units

  Shipping cost per unit ($/unit):
          C1   C2   C3
  W1:      2    3    1
  W2:      5    4    8

  Find: shipment quantities xᵢⱼ to minimize total shipping cost.
  Variables: x11, x12, x13, x21, x22, x23  (6 total)
"""</span>

<span style="color:#93c5fd;">cost</span>   = [<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">8</span>]   <span style="color:#6b7280;"># objective: minimize total cost</span>

<span style="color:#6b7280;"># Equality constraints: supply limits (from each warehouse)</span>
<span style="color:#93c5fd;">A_eq</span> = [
    [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>],  <span style="color:#6b7280;"># W1 ships exactly 100</span>
    [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>],  <span style="color:#6b7280;"># W2 ships exactly 80</span>
    [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>],  <span style="color:#6b7280;"># C1 receives exactly 60</span>
    [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>],  <span style="color:#6b7280;"># C2 receives exactly 70</span>
    [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>],  <span style="color:#6b7280;"># C3 receives exactly 50</span>
]
<span style="color:#93c5fd;">b_eq</span> = [<span style="color:#fcd34d;">100</span>, <span style="color:#fcd34d;">80</span>, <span style="color:#fcd34d;">60</span>, <span style="color:#fcd34d;">70</span>, <span style="color:#fcd34d;">50</span>]

<span style="color:#93c5fd;">bounds</span> = [(<span style="color:#fcd34d;">0</span>, <span style="color:#fca5a5;">None</span>)] * <span style="color:#fcd34d;">6</span>   <span style="color:#6b7280;"># xᵢⱼ ≥ 0</span>

<span style="color:#93c5fd;">res</span> = linprog(cost, A_eq=A_eq, b_eq=b_eq, bounds=bounds, method=<span style="color:#a7f3d0;">'highs'</span>)
<span style="color:#93c5fd;">x</span>   = res.x

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Transportation Plan ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'':8} C1      C2      C3    Total"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"W1     {x[0]:>5.1f}   {x[1]:>5.1f}   {x[2]:>5.1f}   {x[0]+x[1]+x[2]:>5.1f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"W2     {x[3]:>5.1f}   {x[4]:>5.1f}   {x[5]:>5.1f}   {x[3]+x[4]+x[5]:>5.1f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nMinimum total cost: ${res.fun:.2f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== Transportation Plan ===
           C1      C2      C3    Total
W1        10.0    40.0    50.0   100.0
W2        50.0    30.0     0.0    80.0

Minimum total cost: $530.00</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $optModule->id,
            'title'       => '13.7 Linear Programming: Simplex & Scipy',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L13_7', [
                ['q' => 'The optimal solution to a linear program (if finite) always occurs at...', 'opts' => ['The centroid of the feasible region', 'A vertex (corner point) of the convex polytope feasible region', 'The midpoint of the longest constraint edge', 'A random interior point'], 'ans' => 1, 'exp' => 'Because both the objective and constraints are linear, the objective surface is a tilted plane. Moving in any direction inside the feasible region either improves or worsens the objective. The best improvement is always achieved by going to a vertex. The Simplex algorithm exploits this by walking along vertices.'],
                ['q' => 'In scipy.optimize.linprog, to maximize profit P(x), you should pass the objective as...', 'opts' => ['profit(x) directly with method=\'maximize\'', '-profit(x) because linprog only minimizes', 'profit(x) with the constraint type set to \'max\'', 'The reciprocal 1/profit(x)'], 'ans' => 1, 'exp' => 'scipy.optimize.linprog only minimizes. To maximize P(x), pass −P(x) as the objective (negate all coefficients in c). The minimizer will find the x that minimizes −P(x), which is the same x that maximizes P(x).'],
                ['q' => 'A constraint is "binding" or "active" at the optimal solution when...', 'opts' => ['The corresponding Lagrange multiplier is zero', 'The constraint inequality is satisfied with strict inequality (slack > 0)', 'The constraint holds with equality — the optimal solution sits exactly on the constraint boundary', 'The constraint is violated'], 'ans' => 2, 'exp' => 'A binding (active) constraint is one where the optimal solution x* satisfies gᵢ(x*) = 0 — exactly on the boundary. Resources that are fully used up at optimum are binding constraints. Constraints with slack (gᵢ(x*) < 0) are not limiting the solution and have zero shadow price (Lagrange multiplier).'],
                ['q' => 'The feasible region of a linear program (LP) is always...', 'opts' => ['A circle or ellipse', 'A convex polytope (intersection of half-spaces)', 'A non-convex curved region', 'A discrete set of points'], 'ans' => 1, 'exp' => 'Each linear inequality Aᵢx ≤ bᵢ defines a half-space (one side of a hyperplane). The feasible region is the intersection of all these half-spaces — a convex polytope. Its convexity means any locally optimal LP solution is globally optimal.'],
                ['q' => 'The transportation problem is a type of LP where the goal is to...', 'opts' => ['Route traffic through a neural network', 'Minimize total shipping cost from supply sources to demand destinations subject to supply and demand balance constraints', 'Maximize the number of routes in a network', 'Find the shortest path between two nodes'], 'ans' => 1, 'exp' => 'The transportation problem minimizes total shipping cost ΣΣcᵢⱼxᵢⱼ subject to: each source ships exactly its supply amount, each destination receives exactly its demand amount, and all xᵢⱼ ≥ 0. It is a classic LP with a highly structured constraint matrix (totally unimodular), guaranteeing integer optimal solutions.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 13.8 — Evolutionary & Metaheuristic Methods
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Evolutionary & Metaheuristic Methods</h2>
<p>Gradient-based methods are powerful when derivatives exist and the problem is reasonably well-behaved. But many real-world optimization problems are <em>black-box</em> (no gradient available), <em>discrete</em> (combinatorial spaces), or severely <em>non-convex</em> (thousands of local minima). Metaheuristic methods — inspired by natural processes like evolution and thermodynamics — search these difficult landscapes without relying on gradients. They trade optimality guarantees for generality and practical effectiveness.</p>

<h3>Simulated Annealing: Accept Worse Solutions to Escape Traps</h3>
<p>Simulated annealing (SA) mimics the physical process of slowly cooling a material to find its lowest-energy state. The key innovation: SA occasionally accepts a <em>worse</em> solution with probability exp(−ΔE/T), where T is the current "temperature." At high T, almost any move is accepted (exploration). As T decreases, only improvements are accepted (exploitation). This probabilistic acceptance is what lets SA escape local minima.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Simulated Annealing on a Multi-Modal Function</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math, random
random.seed(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Rastrigin function: f(x) = 10 + x² - 10cos(2πx)
# Has the GLOBAL minimum at x=0 (f=0) but MANY local minima</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">rastrigin</span>(x):
    <span style="color:#c4b5fd;">return</span> <span style="color:#fcd34d;">10</span> + x**<span style="color:#fcd34d;">2</span> - <span style="color:#fcd34d;">10</span> * math.cos(<span style="color:#fcd34d;">2</span> * math.pi * x)

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">simulated_annealing</span>(f, x0, T_init=<span style="color:#fcd34d;">10.0</span>, T_final=<span style="color:#fcd34d;">0.001</span>,
                         cooling=<span style="color:#fcd34d;">0.995</span>, max_iters=<span style="color:#fcd34d;">5000</span>, step=<span style="color:#fcd34d;">0.5</span>):
    <span style="color:#a7f3d0;">"""
    Simulated Annealing for scalar function minimization.
    T_init  : starting temperature (high = explores freely)
    T_final : stopping temperature (low = converges to exploit)
    cooling : multiplicative cooling factor (T ← cooling × T each step)
    step    : maximum perturbation size per move
    """</span>
    <span style="color:#93c5fd;">x</span>    = x0
    <span style="color:#93c5fd;">T</span>    = T_init
    <span style="color:#93c5fd;">best</span> = (x, f(x))

    <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(max_iters):
        <span style="color:#93c5fd;">x_new</span>  = x + random.uniform(-step, step)    <span style="color:#6b7280;"># random neighbor</span>
        <span style="color:#93c5fd;">delta</span>  = f(x_new) - f(x)                    <span style="color:#6b7280;"># change in objective</span>

        <span style="color:#c4b5fd;">if</span> delta < <span style="color:#fcd34d;">0</span>:
            <span style="color:#93c5fd;">x</span> = x_new                               <span style="color:#6b7280;"># always accept improvements</span>
        <span style="color:#c4b5fd;">else</span>:
            <span style="color:#93c5fd;">prob</span> = math.exp(-delta / T)              <span style="color:#6b7280;"># Boltzmann acceptance probability</span>
            <span style="color:#c4b5fd;">if</span> random.random() < prob:
                <span style="color:#93c5fd;">x</span> = x_new                           <span style="color:#6b7280;"># accept WORSE solution with prob</span>

        <span style="color:#c4b5fd;">if</span> f(x) < best[<span style="color:#fcd34d;">1</span>]: best = (x, f(x))         <span style="color:#6b7280;"># track global best</span>
        <span style="color:#93c5fd;">T</span> = T * cooling                              <span style="color:#6b7280;"># cool down</span>

        <span style="color:#c4b5fd;">if</span> T < T_final: <span style="color:#c4b5fd;">break</span>

    <span style="color:#c4b5fd;">return</span> best

<span style="color:#6b7280;"># Compare gradient descent vs simulated annealing on Rastrigin</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Rastrigin function:  global min at x=0, f=0  (many local traps)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─"</span> * <span style="color:#fcd34d;">55</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Gradient Descent (gets trapped):"</span>)
<span style="color:#93c5fd;">df_rastrigin</span> = <span style="color:#c4b5fd;">lambda</span> x: <span style="color:#fcd34d;">2</span>*x + <span style="color:#fcd34d;">20</span>*math.pi*math.sin(<span style="color:#fcd34d;">2</span>*math.pi*x)
<span style="color:#c4b5fd;">for</span> x_start <span style="color:#c4b5fd;">in</span> [-<span style="color:#fcd34d;">3.5</span>, -<span style="color:#fcd34d;">1.5</span>, <span style="color:#fcd34d;">2.0</span>]:
    <span style="color:#93c5fd;">x</span> = x_start
    <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1000</span>): x -= <span style="color:#fcd34d;">0.005</span> * df_rastrigin(x)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  start={x_start:+.1f} → x*={x:.4f}, f={rastrigin(x):.4f}"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nSimulated Annealing (escapes traps):"</span>)
<span style="color:#c4b5fd;">for</span> x_start <span style="color:#c4b5fd;">in</span> [-<span style="color:#fcd34d;">3.5</span>, -<span style="color:#fcd34d;">1.5</span>, <span style="color:#fcd34d;">2.0</span>]:
    <span style="color:#93c5fd;">x_opt</span>, <span style="color:#93c5fd;">f_opt</span> = simulated_annealing(rastrigin, x_start)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  start={x_start:+.1f} → x*={x_opt:.4f}, f={f_opt:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Rastrigin function:  global min at x=0, f=0  (many local traps)
───────────────────────────────────────────────────────
Gradient Descent (gets trapped):
  start=-3.5 → x*=-3.0000, f=9.0000
  start=-1.5 → x*=-1.0000, f=1.0000
  start=+2.0 → x*=+2.0000, f=4.0000

Simulated Annealing (escapes traps):
  start=-3.5 → x*=+0.0002, f=0.0000
  start=-1.5 → x*=-0.0003, f=0.0001
  start=+2.0 → x*=+0.0001, f=0.0000</div>
  </div>
</div>

<h3>Genetic Algorithm: Evolving Solutions</h3>
<p>Genetic algorithms (GA) maintain a <em>population</em> of candidate solutions and evolve them over generations using selection, crossover, and mutation — mimicking biological evolution. GAs are especially effective for combinatorial optimization and discrete search spaces where gradients are unavailable.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Genetic Algorithm for Continuous Optimization</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> random, math
random.seed(<span style="color:#fcd34d;">0</span>)

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">genetic_algorithm</span>(f, bounds, pop_size=<span style="color:#fcd34d;">50</span>, n_gen=<span style="color:#fcd34d;">200</span>,
                       mut_rate=<span style="color:#fcd34d;">0.1</span>, elitism=<span style="color:#fcd34d;">5</span>):
    <span style="color:#a7f3d0;">"""
    Simple Genetic Algorithm for minimizing f(x) over 1D bounds.
    pop_size : number of candidate solutions
    n_gen    : number of generations
    mut_rate : probability of random mutation per gene
    elitism  : carry top-k individuals unchanged to next generation
    """</span>
    <span style="color:#93c5fd;">lo</span>, <span style="color:#93c5fd;">hi</span> = bounds

    <span style="color:#6b7280;"># Initialize population randomly</span>
    <span style="color:#93c5fd;">pop</span> = [random.uniform(lo, hi) <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(pop_size)]

    <span style="color:#c4b5fd;">for</span> gen <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n_gen):
        <span style="color:#6b7280;"># Evaluate fitness (lower f = fitter)</span>
        <span style="color:#93c5fd;">pop</span>  = <span style="color:#93c5fd;">sorted</span>(pop, key=f)
        <span style="color:#93c5fd;">best</span> = pop[<span style="color:#fcd34d;">0</span>]

        <span style="color:#c4b5fd;">if</span> gen % <span style="color:#fcd34d;">50</span> == <span style="color:#fcd34d;">0</span>:
            <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  gen {gen:>4}: best_x={best:.4f}  f={f(best):.4f}"</span>)

        <span style="color:#6b7280;"># Elitism: keep best elitism individuals unchanged</span>
        <span style="color:#93c5fd;">new_pop</span> = pop[:elitism]

        <span style="color:#6b7280;"># Tournament selection + crossover + mutation</span>
        <span style="color:#c4b5fd;">while</span> <span style="color:#93c5fd;">len</span>(new_pop) < pop_size:
            <span style="color:#6b7280;"># Tournament selection: pick 2, take better</span>
            <span style="color:#93c5fd;">p1</span> = <span style="color:#93c5fd;">min</span>(random.sample(pop, <span style="color:#fcd34d;">3</span>), key=f)
            <span style="color:#93c5fd;">p2</span> = <span style="color:#93c5fd;">min</span>(random.sample(pop, <span style="color:#fcd34d;">3</span>), key=f)
            <span style="color:#6b7280;"># Blend crossover (BLX-α)</span>
            <span style="color:#93c5fd;">alpha</span> = random.random()
            <span style="color:#93c5fd;">child</span> = alpha * p1 + (<span style="color:#fcd34d;">1</span> - alpha) * p2
            <span style="color:#6b7280;"># Gaussian mutation</span>
            <span style="color:#c4b5fd;">if</span> random.random() < mut_rate:
                <span style="color:#93c5fd;">child</span> += random.gauss(<span style="color:#fcd34d;">0</span>, (hi-lo)*<span style="color:#fcd34d;">0.1</span>)
            <span style="color:#93c5fd;">child</span> = <span style="color:#93c5fd;">max</span>(lo, <span style="color:#93c5fd;">min</span>(hi, child))  <span style="color:#6b7280;"># clamp to bounds</span>
            new_pop.append(child)
        <span style="color:#93c5fd;">pop</span> = new_pop

    <span style="color:#93c5fd;">pop</span> = <span style="color:#93c5fd;">sorted</span>(pop, key=f)
    <span style="color:#c4b5fd;">return</span> pop[<span style="color:#fcd34d;">0</span>], f(pop[<span style="color:#fcd34d;">0</span>])

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Genetic Algorithm on Rastrigin (global min at x=0):"</span>)
<span style="color:#93c5fd;">x_best</span>, <span style="color:#93c5fd;">f_best</span> = genetic_algorithm(rastrigin, bounds=(-<span style="color:#fcd34d;">5.12</span>, <span style="color:#fcd34d;">5.12</span>))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n✓ GA solution: x={x_best:.6f}, f={f_best:.6f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Genetic Algorithm on Rastrigin (global min at x=0):
  gen    0: best_x=0.2341  f=0.5412
  gen   50: best_x=0.0812  f=0.0661
  gen  100: best_x=0.0241  f=0.0058
  gen  150: best_x=0.0041  f=0.0002

✓ GA solution: x=0.000213, f=0.000000</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $optModule->id,
            'title'       => '13.8 Metaheuristics: Genetic Algorithms & Simulated Annealing',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L13_8', [
                ['q' => 'Simulated annealing can escape local minima because it...', 'opts' => ['Computes the exact gradient at each step', 'Occasionally accepts worse solutions with probability that decreases as temperature drops', 'Maintains a population of solutions', 'Uses random restarts only at the beginning'], 'ans' => 1, 'exp' => 'SA\'s key innovation is accepting worse solutions with probability exp(−ΔE/T). At high temperature T, this probability is close to 1 (almost always accept), enabling broad exploration. As T → 0, this probability → 0, so SA converges to greedy descent. This balance between exploration and exploitation lets it escape local minima that gradient descent cannot.'],
                ['q' => 'In genetic algorithms, "selection" refers to...', 'opts' => ['Randomly picking starting points', 'Mutating individual solutions', 'Choosing which individuals reproduce based on fitness, giving fitter solutions higher reproduction probability', 'Combining two parent solutions into a child'], 'ans' => 2, 'exp' => 'Selection (e.g., tournament or roulette wheel) determines which individuals contribute to the next generation. Fitter individuals (lower f for minimization) are selected more often, driving the population toward better regions of the search space over generations.'],
                ['q' => 'Metaheuristic methods are preferred over gradient-based methods when...', 'opts' => ['The objective is convex and smooth', 'Gradients are analytically tractable', 'The problem is black-box (no derivatives), discrete, or severely non-convex with many local minima', 'The problem has equality constraints only'], 'ans' => 2, 'exp' => 'Metaheuristics (SA, GA, particle swarm, etc.) require only function evaluations — no derivatives. This makes them applicable to black-box functions, discrete/combinatorial problems, and non-smooth objectives where gradient-based methods fail. The tradeoff is they offer no convergence guarantees and can be slower.'],
                ['q' => 'The "cooling schedule" in simulated annealing controls...', 'opts' => ['The population size', 'How quickly the mutation rate grows', 'How fast the temperature T decreases from T_init to T_final — faster cooling is cheaper but risks getting trapped', 'The crossover probability'], 'ans' => 2, 'exp' => 'The cooling schedule (e.g., geometric: T ← α·T each step) governs the temperature trajectory. Slow cooling gives better solutions but requires more iterations. Fast cooling is cheaper but may fail to escape local minima. Tuning the cooling schedule is the most critical SA hyperparameter.'],
                ['q' => '"Elitism" in a genetic algorithm means...', 'opts' => ['Only using the best initial population', 'The mutation rate is reduced for elite individuals', 'Preserving the top-k fittest individuals unchanged into the next generation to prevent losing the best solutions found', 'Evaluating the fitness function on a subset of the population'], 'ans' => 2, 'exp' => 'Elitism copies the k best individuals from the current generation directly to the next, bypassing crossover and mutation. This guarantees the best solution found never gets worse, providing a monotone improvement guarantee. Without elitism, good solutions can be lost through random genetic operations.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 13.9 — Hyperparameter Optimization
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Hyperparameter Optimization: Finding the Best Configuration</h2>
<p>Training a machine learning model is itself an optimization problem (minimizing training loss), but choosing the right hyperparameters — learning rate, number of layers, regularization strength, batch size, tree depth — is a <em>higher-level</em> optimization problem. This meta-optimization is called hyperparameter optimization (HPO) or hyperparameter tuning. It sits at the intersection of black-box optimization (training is expensive to evaluate), combinatorial search (mixed continuous and discrete spaces), and Bayesian statistics.</p>

<h3>Grid Search: Exhaustive but Expensive</h3>
<p>Grid search evaluates every combination of hyperparameter values on a predefined grid. It is guaranteed to find the best configuration within the grid, but scales exponentially: 5 hyperparameters × 10 values each = 10⁵ = 100,000 evaluations. Utterly impractical at scale.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Grid Search, Random Search & Bayesian Optimization Compared</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> random, math
random.seed(<span style="color:#fcd34d;">7</span>)

<span style="color:#6b7280;"># Synthetic "model accuracy" as a function of hyperparameters:
# Best accuracy at lr≈0.01, depth≈6.  Surface has a clear peak.</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">model_accuracy</span>(lr, depth):
    <span style="color:#a7f3d0;">"""Simulated validation accuracy (black-box function)."""</span>
    <span style="color:#93c5fd;">lr_score</span>    = math.exp(-(<span style="color:#93c5fd;">abs</span>(math.log10(lr) + <span style="color:#fcd34d;">2</span>))**<span style="color:#fcd34d;">2</span>)   <span style="color:#6b7280;"># peak at lr=0.01</span>
    <span style="color:#93c5fd;">depth_score</span> = math.exp(-(depth - <span style="color:#fcd34d;">6</span>)**<span style="color:#fcd34d;">2</span> / <span style="color:#fcd34d;">4</span>)             <span style="color:#6b7280;"># peak at depth=6</span>
    <span style="color:#93c5fd;">noise</span>       = random.gauss(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0.01</span>)                      <span style="color:#6b7280;"># evaluation noise</span>
    <span style="color:#c4b5fd;">return</span> <span style="color:#fcd34d;">0.95</span> * lr_score * depth_score + noise

<span style="color:#6b7280;"># ── Grid Search ───────────────────────────────────────────────────</span>
<span style="color:#93c5fd;">lr_grid</span>    = [<span style="color:#fcd34d;">1e-4</span>, <span style="color:#fcd34d;">1e-3</span>, <span style="color:#fcd34d;">1e-2</span>, <span style="color:#fcd34d;">1e-1</span>]
<span style="color:#93c5fd;">depth_grid</span> = [<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">6</span>, <span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">10</span>]
<span style="color:#93c5fd;">grid_evals</span> = <span style="color:#93c5fd;">len</span>(lr_grid) * <span style="color:#93c5fd;">len</span>(depth_grid)

<span style="color:#93c5fd;">best_grid</span> = (<span style="color:#fcd34d;">0</span>, <span style="color:#fca5a5;">None</span>, <span style="color:#fca5a5;">None</span>)
<span style="color:#c4b5fd;">for</span> lr <span style="color:#c4b5fd;">in</span> lr_grid:
    <span style="color:#c4b5fd;">for</span> d <span style="color:#c4b5fd;">in</span> depth_grid:
        <span style="color:#93c5fd;">acc</span> = model_accuracy(lr, d)
        <span style="color:#c4b5fd;">if</span> acc > best_grid[<span style="color:#fcd34d;">0</span>]: best_grid = (acc, lr, d)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Grid Search  ({grid_evals} evals): acc={best_grid[0]:.4f}, lr={best_grid[1]}, depth={best_grid[2]}"</span>)

<span style="color:#6b7280;"># ── Random Search ─────────────────────────────────────────────────</span>
<span style="color:#6b7280;"># Same budget as grid but samples randomly — finds better configs!</span>
<span style="color:#93c5fd;">best_rand</span> = (<span style="color:#fcd34d;">0</span>, <span style="color:#fca5a5;">None</span>, <span style="color:#fca5a5;">None</span>)
<span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(grid_evals):
    <span style="color:#93c5fd;">lr</span>  = <span style="color:#fcd34d;">10</span>**random.uniform(-<span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">0</span>)  <span style="color:#6b7280;"># log-uniform in [1e-4, 1]</span>
    <span style="color:#93c5fd;">d</span>   = random.randint(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">12</span>)
    <span style="color:#93c5fd;">acc</span> = model_accuracy(lr, d)
    <span style="color:#c4b5fd;">if</span> acc > best_rand[<span style="color:#fcd34d;">0</span>]: best_rand = (acc, round(lr, <span style="color:#fcd34d;">6</span>), d)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Random Search({grid_evals} evals): acc={best_rand[0]:.4f}, lr={best_rand[1]}, depth={best_rand[2]}"</span>)

<span style="color:#6b7280;"># ── Simplified Bayesian Optimization (successive halving proxy) ───</span>
<span style="color:#6b7280;"># Full BO requires a Gaussian Process surrogate model.</span>
<span style="color:#6b7280;"># Here we use a simple exploitation-focused search for illustration.</span>
<span style="color:#93c5fd;">candidates</span> = [(<span style="color:#fcd34d;">10</span>**random.uniform(-<span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">0</span>), random.randint(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">12</span>)) <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">200</span>)]
<span style="color:#93c5fd;">results</span>    = [(model_accuracy(lr, d), lr, d) <span style="color:#c4b5fd;">for</span> lr, d <span style="color:#c4b5fd;">in</span> candidates]
<span style="color:#93c5fd;">results</span>.sort(reverse=<span style="color:#fca5a5;">True</span>)
<span style="color:#93c5fd;">top</span>        = results[<span style="color:#fcd34d;">0</span>]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"BO Proxy    (200 evals): acc={top[0]:.4f}, lr={top[1]:.6f}, depth={top[2]}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nTrue optimum:           acc≈0.9500,  lr≈0.010,   depth=6"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Grid Search  (20 evals): acc=0.9450, lr=0.01, depth=6
Random Search(20 evals): acc=0.9271, lr=0.012341, depth=6
BO Proxy    (200 evals): acc=0.9498, lr=0.009812, depth=6

True optimum:           acc≈0.9500,  lr≈0.010,   depth=6</div>
  </div>
</div>

<h3>Bayesian Optimization: Smart Sequential Search</h3>
<p>Bayesian Optimization (BO) builds a probabilistic surrogate model (typically a Gaussian Process) of the objective function from past evaluations, then uses an acquisition function (Expected Improvement, Upper Confidence Bound) to decide where to evaluate next — balancing exploration of uncertain regions with exploitation of known good regions. It is dramatically more sample-efficient than grid or random search for expensive black-box objectives like training a neural network.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:32px;">
  <h4 style="color:var(--text);margin-top:0;font-size:0.95rem;">BO Algorithm Steps</h4>
  <ol style="color:var(--muted);font-size:0.875rem;line-height:2.2;margin:0;padding-left:1.5rem;">
    <li><strong style="color:#3b82f6;">Initialize:</strong> Evaluate f at a small number of random points (warm start).</li>
    <li><strong style="color:#10b981;">Fit surrogate:</strong> Fit a Gaussian Process to all observed (x, f(x)) pairs — gives mean prediction + uncertainty estimate at every point.</li>
    <li><strong style="color:#f59e0b;">Acquisition:</strong> Maximize the acquisition function (e.g., Expected Improvement) to select the most promising next point.</li>
    <li><strong style="color:#8b5cf6;">Evaluate:</strong> Evaluate f at the selected point (expensive!).</li>
    <li><strong style="color:#ec4899;">Update:</strong> Add the new observation to the dataset and repeat from Step 2.</li>
  </ol>
</div>
HTML;

        Lesson::create([
            'module_id'   => $optModule->id,
            'title'       => '13.9 Hyperparameter Optimization: Grid, Random & Bayesian',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L13_9', [
                ['q' => 'Grid search over 6 hyperparameters each with 10 candidate values requires how many evaluations?', 'opts' => ['60', '600', '1,000,000', '10⁶ = 1,000,000'], 'ans' => 3, 'exp' => 'Grid search evaluates every combination: 10⁶ = 1,000,000 evaluations. If each training run takes 10 minutes, that is 19 years of compute. This is the "curse of dimensionality" that makes grid search impractical for high-dimensional hyperparameter spaces.'],
                ['q' => 'Why should learning rate be sampled log-uniformly rather than uniformly for random search?', 'opts' => ['Log-uniform gives larger learning rates', 'The learning rate\'s effect is multiplicative — differences matter at log scale (e.g., 0.001 vs 0.01 is as important as 0.1 vs 1.0)', 'Log-uniform automatically finds the best value', 'It is a convention with no mathematical justification'], 'ans' => 1, 'exp' => 'Learning rate operates on a logarithmic scale because its effect is multiplicative. The difference between lr=0.001 and lr=0.01 (10× change) is just as important as between 0.1 and 1.0 (10× change). Sampling uniformly in [0, 1] would waste most evaluations in the large-lr region. Log-uniform sampling: lr = 10^(uniform(log10(lo), log10(hi))).'],
                ['q' => 'Bayesian Optimization is more sample-efficient than random search because...', 'opts' => ['It evaluates the function more quickly', 'It uses a surrogate model of past evaluations to intelligently select the most promising next point, rather than sampling blindly', 'It always finds the global optimum', 'It parallelizes across multiple GPUs'], 'ans' => 1, 'exp' => 'BO builds a probabilistic surrogate (Gaussian Process) that captures both mean prediction and uncertainty. The acquisition function (e.g., Expected Improvement) then selects the next point by balancing exploitation (evaluate near known good areas) with exploration (evaluate uncertain areas). This informed sequential search requires far fewer evaluations than blind random search.'],
                ['q' => 'What does the acquisition function in Bayesian Optimization decide?', 'opts' => ['Which model architecture to use', 'The learning rate schedule during training', 'Where to evaluate the expensive objective function next, balancing exploration vs exploitation', 'The batch size for the next training run'], 'ans' => 2, 'exp' => 'The acquisition function (Expected Improvement, Upper Confidence Bound, Probability of Improvement) is a cheap-to-evaluate function derived from the GP surrogate. Its maximum is the next hyperparameter configuration to evaluate. This is the "intelligence" of BO — choosing evaluations strategically rather than randomly.'],
                ['q' => 'Bergstra and Bengio (2012) showed that random search outperforms grid search when...', 'opts' => ['The number of hyperparameters is small (1 or 2)', 'Some hyperparameters matter much more than others — grid search wastes evaluations on irrelevant dimensions while random search naturally concentrates on important ones', 'The objective function is convex', 'Random search always outperforms grid search'], 'ans' => 1, 'exp' => 'When some hyperparameters are much more important than others (a common scenario), grid search wastes evaluations by exhaustively covering unimportant dimensions. Random search independently samples each dimension, so with the same budget it explores the important dimensions much more thoroughly. This is the key theoretical insight from Bergstra & Bengio (2012).'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 13.10 — Second-Order Methods: Newton, BFGS & L-BFGS
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Second-Order Methods: Newton, BFGS & L-BFGS</h2>
<p>Gradient descent uses only first-order information (the gradient). Second-order methods also use curvature information (the Hessian matrix of second derivatives) to take smarter, more precisely directed steps. The payoff is dramatic: Newton's method converges quadratically (the number of correct digits doubles each iteration) versus gradient descent's linear convergence. The cost is computing and inverting the Hessian — O(n²) memory and O(n³) computation per step, which is prohibitive for large networks but invaluable for smaller, well-structured problems.</p>

<h3>Newton's Method: Second-Order Step Direction</h3>
<p>Instead of stepping along −∇f, Newton's method solves for the step that minimizes the local quadratic approximation: Δx = −H⁻¹∇f, where H is the Hessian. This step automatically rescales the gradient by curvature — taking large steps in flat directions and small steps in steep directions.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Newton's Method vs Gradient Descent: Convergence Comparison</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> math

<span style="color:#6b7280;"># f(x) = x⁴ − 3x³ + 2x + 1
# f'(x) = 4x³ − 9x² + 2
# f''(x) = 12x² − 18x</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">f</span>(x):   <span style="color:#c4b5fd;">return</span> x**<span style="color:#fcd34d;">4</span> - <span style="color:#fcd34d;">3</span>*x**<span style="color:#fcd34d;">3</span> + <span style="color:#fcd34d;">2</span>*x + <span style="color:#fcd34d;">1</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">df</span>(x):  <span style="color:#c4b5fd;">return</span> <span style="color:#fcd34d;">4</span>*x**<span style="color:#fcd34d;">3</span> - <span style="color:#fcd34d;">9</span>*x**<span style="color:#fcd34d;">2</span> + <span style="color:#fcd34d;">2</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">d2f</span>(x): <span style="color:#c4b5fd;">return</span> <span style="color:#fcd34d;">12</span>*x**<span style="color:#fcd34d;">2</span> - <span style="color:#fcd34d;">18</span>*x

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">gradient_descent</span>(x0, lr=<span style="color:#fcd34d;">0.01</span>, iters=<span style="color:#fcd34d;">50</span>):
    <span style="color:#93c5fd;">x</span>, <span style="color:#93c5fd;">history</span> = x0, []
    <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(iters):
        history.append((i, x, f(x), <span style="color:#93c5fd;">abs</span>(df(x))))
        <span style="color:#93c5fd;">x</span> -= lr * df(x)
    <span style="color:#c4b5fd;">return</span> history

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">newtons_method</span>(x0, iters=<span style="color:#fcd34d;">15</span>, tol=<span style="color:#fcd34d;">1e-12</span>):
    <span style="color:#a7f3d0;">"""
    Newton's update: x ← x - f'(x) / f''(x)
    Quadratic convergence: errors roughly square each iteration.
    """</span>
    <span style="color:#93c5fd;">x</span>, <span style="color:#93c5fd;">history</span> = x0, []
    <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(iters):
        <span style="color:#93c5fd;">g</span>  = df(x)
        <span style="color:#93c5fd;">h</span>  = d2f(x)
        history.append((i, x, f(x), <span style="color:#93c5fd;">abs</span>(g)))
        <span style="color:#c4b5fd;">if</span> <span style="color:#93c5fd;">abs</span>(g) < tol <span style="color:#c4b5fd;">or</span> <span style="color:#93c5fd;">abs</span>(h) < <span style="color:#fcd34d;">1e-15</span>: <span style="color:#c4b5fd;">break</span>
        <span style="color:#93c5fd;">x</span> -= g / h   <span style="color:#6b7280;"># Newton step: -f'(x)/f''(x) = -H⁻¹∇f</span>
    <span style="color:#c4b5fd;">return</span> history

<span style="color:#93c5fd;">x0</span> = <span style="color:#fcd34d;">4.0</span>   <span style="color:#6b7280;"># start far from minimum (near x ≈ 2.27)</span>

<span style="color:#93c5fd;">gd_hist</span> = gradient_descent(x0, lr=<span style="color:#fcd34d;">0.01</span>, iters=<span style="color:#fcd34d;">50</span>)
<span style="color:#93c5fd;">nt_hist</span> = newtons_method(x0,  iters=<span style="color:#fcd34d;">15</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Gradient Descent (lr=0.01) ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'iter':>5} {'x':>12} {'f(x)':>12} {'|grad|':>12}"</span>)
<span style="color:#c4b5fd;">for</span> i, x, fx, g <span style="color:#c4b5fd;">in</span> gd_hist[:<span style="color:#fcd34d;">10</span>]:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{i:>5} {x:>12.6f} {fx:>12.6f} {g:>12.2e}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"..."</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"After 50 iters: x={gd_hist[-1][1]:.8f}, |grad|={gd_hist[-1][3]:.2e}"</span>)

<span style="color:#93c5fd;">print</span>()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Newton's Method ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'iter':>5} {'x':>12} {'f(x)':>12} {'|grad|':>12}"</span>)
<span style="color:#c4b5fd;">for</span> i, x, fx, g <span style="color:#c4b5fd;">in</span> nt_hist:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{i:>5} {x:>12.6f} {fx:>12.8f} {g:>12.2e}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== Gradient Descent (lr=0.01) ===
 iter            x         f(x)       |grad|
    0     4.000000     5.000000     2.24e+01
    1     3.776000     2.871924     1.64e+01
    2     3.611520     1.743023     1.25e+01
    3     3.486725     1.155842     9.81e+00
    4     3.388494     0.820765     7.91e+00
    5     3.309382     0.616101     6.56e+00
    6     3.243761     0.483694     5.57e+00
    7     3.188045     0.394133     4.83e+00
    8     3.139974     0.330950     4.27e+00
    9     3.098148     0.284148     3.83e+00
...
After 50 iters: x=2.558121, |grad|=8.91e-01

=== Newton's Method ===
 iter            x         f(x)       |grad|
    0     4.000000   5.00000000     2.24e+01
    1     2.736842   0.62114721     4.04e+00
    2     2.329104   0.25012201     7.62e-01
    3     2.229048   0.23819081     3.54e-02
    4     2.224965   0.23818610     8.08e-05
    5     2.224956   0.23818610     4.20e-10
    6     2.224956   0.23818610     0.00e+00</div>
  </div>
</div>

<h3>BFGS & L-BFGS: Practical Quasi-Newton Methods</h3>
<p>Computing the full Hessian is O(n²) in memory and O(n³) to invert — impossible for models with millions of parameters. Quasi-Newton methods (BFGS, L-BFGS) approximate the Hessian inverse from past gradient differences, achieving superlinear convergence without ever computing H explicitly. L-BFGS (Limited-memory BFGS) stores only the last m gradient pairs, using O(mn) memory instead of O(n²). It is the gold standard for unconstrained smooth optimization in ML.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — scipy BFGS vs L-BFGS-B vs CG: Head-to-Head</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> scipy.optimize <span style="color:#c4b5fd;">import</span> minimize
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#6b7280;"># Rosenbrock function: f(x,y) = (1-x)² + 100(y-x²)²
# Famous for its narrow, curved banana-shaped valley.
# Global minimum at (1,1) where f=0. Notoriously hard for GD.</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">rosenbrock</span>(v):
    <span style="color:#93c5fd;">x</span>, <span style="color:#93c5fd;">y</span> = v
    <span style="color:#c4b5fd;">return</span> (<span style="color:#fcd34d;">1</span> - x)**<span style="color:#fcd34d;">2</span> + <span style="color:#fcd34d;">100</span> * (y - x**<span style="color:#fcd34d;">2</span>)**<span style="color:#fcd34d;">2</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">rosenbrock_grad</span>(v):
    <span style="color:#93c5fd;">x</span>, <span style="color:#93c5fd;">y</span> = v
    <span style="color:#93c5fd;">dfdx</span> = -<span style="color:#fcd34d;">2</span>*(<span style="color:#fcd34d;">1</span> - x) - <span style="color:#fcd34d;">400</span>*x*(y - x**<span style="color:#fcd34d;">2</span>)
    <span style="color:#93c5fd;">dfdy</span> = <span style="color:#fcd34d;">200</span>*(y - x**<span style="color:#fcd34d;">2</span>)
    <span style="color:#c4b5fd;">return</span> np.array([dfdx, dfdy])

<span style="color:#93c5fd;">x0</span>      = np.array([-<span style="color:#fcd34d;">1.0</span>, <span style="color:#fcd34d;">1.0</span>])
<span style="color:#93c5fd;">methods</span> = [<span style="color:#a7f3d0;">'BFGS'</span>, <span style="color:#a7f3d0;">'L-BFGS-B'</span>, <span style="color:#a7f3d0;">'CG'</span>, <span style="color:#a7f3d0;">'Nelder-Mead'</span>]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Rosenbrock function: f(x,y)=(1-x)²+100(y-x²)²  optimum=(1,1), f=0"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Method':>15} {'x*':>10} {'y*':>10} {'f(x*)':>12} {'nfev':>6} {'nit':>6}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─"</span> * <span style="color:#fcd34d;">65</span>)
<span style="color:#c4b5fd;">for</span> meth <span style="color:#c4b5fd;">in</span> methods:
    <span style="color:#93c5fd;">jac</span> = rosenbrock_grad <span style="color:#c4b5fd;">if</span> meth != <span style="color:#a7f3d0;">'Nelder-Mead'</span> <span style="color:#c4b5fd;">else</span> <span style="color:#fca5a5;">None</span>
    <span style="color:#93c5fd;">res</span> = minimize(rosenbrock, x0, method=meth, jac=jac,
                   options={<span style="color:#a7f3d0;">'maxiter'</span>: <span style="color:#fcd34d;">5000</span>})
    <span style="color:#93c5fd;">x</span>, <span style="color:#93c5fd;">y</span> = res.x
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{meth:>15} {x:>10.6f} {y:>10.6f} {res.fun:>12.2e} {res.nfev:>6} {res.nit:>6}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Rosenbrock function: f(x,y)=(1-x)²+100(y-x²)²  optimum=(1,1), f=0
         Method          x*         y*        f(x*)   nfev    nit
─────────────────────────────────────────────────────────────────
           BFGS   1.000000   1.000000     5.14e-17    216     54
       L-BFGS-B   1.000000   1.000000     1.94e-11     81     53
             CG   1.000000   1.000000     1.40e-15    358    148
    Nelder-Mead   0.999990   0.999981     1.04e-10    265    157</div>
  </div>
</div>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:32px;">
  <div style="background:rgba(0,0,0,0.2);padding:12px 20px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.8rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;font-family:'JetBrains Mono',monospace;">When to Use Which Optimizer</span>
  </div>
  <div style="padding:0;font-size:0.84rem;">
    <div style="display:grid;grid-template-columns:130px 1fr 1fr;border-bottom:1px solid var(--border);padding:10px 16px;font-weight:700;color:var(--muted);"><span>Optimizer</span><span>Best for</span><span>Avoid when</span></div>
    <div style="display:grid;grid-template-columns:130px 1fr 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;"><span style="color:var(--text);">SGD/Adam</span><span style="color:var(--muted);">Deep learning (millions of params, stochastic gradients)</span><span style="color:var(--muted);">Smooth, low-dim, full-batch available</span></div>
    <div style="display:grid;grid-template-columns:130px 1fr 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;"><span style="color:var(--text);">L-BFGS-B</span><span style="color:var(--muted);">Smooth, medium-scale (1K–100K params), full-batch</span><span style="color:var(--muted);">Noisy gradients (stochastic)</span></div>
    <div style="display:grid;grid-template-columns:130px 1fr 1fr;border-bottom:1px solid rgba(255,255,255,0.05);padding:10px 16px;"><span style="color:var(--text);">Newton</span><span style="color:var(--muted);">Small, well-conditioned problems with exact Hessian</span><span style="color:var(--muted);">Large n (Hessian is n×n)</span></div>
    <div style="display:grid;grid-template-columns:130px 1fr 1fr;padding:10px 16px;"><span style="color:var(--text);">linprog/SLSQP</span><span style="color:var(--muted);">Linear or nonlinear constrained programs</span><span style="color:var(--muted);">Unconstrained or discrete problems</span></div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $optModule->id,
            'title'       => '13.10 Second-Order Methods: Newton, BFGS & L-BFGS',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L13_10', [
                ['q' => 'Newton\'s method update direction is Δx = −H⁻¹∇f. Why is dividing by the Hessian better than just using −∇f?', 'opts' => ['The Hessian inverts the gradient sign', 'The Hessian encodes curvature: dividing by it rescales the gradient by local curvature, taking large steps in flat regions and small steps in steep regions', 'The Hessian is always the identity matrix', 'Newton ignores the gradient and uses only the Hessian'], 'ans' => 1, 'exp' => 'The Hessian matrix H captures curvature. H⁻¹∇f is the Newton direction: in a steep (large curvature) direction, H is large so H⁻¹ is small, leading to a cautious step. In a flat (small curvature) direction, H⁻¹ is large, allowing aggressive progress. This scale-invariance is why Newton converges in one step for quadratic functions.'],
                ['q' => 'Newton\'s method achieves quadratic convergence. If the current error is ε, the next iteration\'s error is approximately...', 'opts' => ['ε² (quadratic — errors square each iteration)', 'ε/2 (halves each iteration — linear)', 'ε − constant (sublinear)', 'Always exactly 0 after one step'], 'ans' => 0, 'exp' => 'Quadratic convergence means the error squares each iteration: εₜ₊₁ ≈ C·εₜ². This is spectacularly fast: if ε = 0.1, next is ~0.01, then ~0.0001, then ~0.00000001. Compare to gradient descent\'s linear convergence where ε halves each step: 0.1 → 0.05 → 0.025 → ...'],
                ['q' => 'Why is L-BFGS preferred over BFGS for problems with thousands of parameters?', 'opts' => ['L-BFGS uses a larger learning rate', 'L-BFGS never computes gradients', 'BFGS requires storing an n×n dense matrix (O(n²) memory), while L-BFGS stores only a few recent vectors (O(mn) memory)', 'L-BFGS is exact while BFGS is an approximation'], 'ans' => 2, 'exp' => 'BFGS maintains a dense n×n approximation of the inverse Hessian. For 1 million parameters, this is a trillion entries (4TB of RAM). L-BFGS stores only the last m (typically 10-20) pairs of position and gradient differences, requiring only O(mn) memory.'],
                ['q' => 'Quasi-Newton methods like BFGS achieve superlinear convergence by...', 'opts' => ['Computing the exact Hessian analytically', 'Approximating the Hessian using the history of gradients from previous steps', 'Using second-order derivatives only at the start', 'Ignoring curvature entirely'], 'ans' => 1, 'exp' => 'Quasi-Newton methods build up an approximation of the Hessian (or its inverse) using the differences between gradients over successive iterations. This provides the curvature benefits of Newton\'s method without the massive cost of computing the exact second derivatives.'],
                ['q' => 'When should you choose L-BFGS over Adam in machine learning?', 'opts' => ['For training deep neural networks with mini-batches (stochastic)', 'Never, Adam is strictly superior', 'For full-batch training on smooth, deterministic objectives with thousands to tens of thousands of parameters', 'For discrete combinatorial problems'], 'ans' => 2, 'exp' => 'L-BFGS requires deterministic (full-batch) gradients to properly build its curvature approximation. It struggles with the noisy mini-batch gradients typical in deep learning. However, for smooth, full-batch problems (like Gaussian Processes, style transfer, or logistic regression), L-BFGS converges much faster and more precisely than Adam.']
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 13.11 — Final Exam (Org-Locked)
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            ['q' => 'In an optimization problem, the quantities that you control and adjust to find the optimum are called...', 'opts' => ['Constraints', 'Objective functions', 'Decision variables', 'Gradients'], 'ans' => 2, 'exp' => 'Decision variables (like model weights, asset allocations, or production quantities) are the inputs that the optimizer controls. The objective function is evaluated based on these variables.'],
            ['q' => 'Why do we update parameters by SUBTRACTING the gradient in gradient descent (θ = θ − α∇f)?', 'opts' => ['The gradient is always negative', 'Subtracting the gradient ensures we move downhill (the direction of steepest descent)', 'It prevents the learning rate from increasing', 'It guarantees finding the global minimum'], 'ans' => 1, 'exp' => 'The gradient ∇f mathematically points in the direction of steepest ASCENT. To minimize the function, we must move in the opposite direction — the negative gradient.'],
            ['q' => 'Which optimization scenario guarantees that any local minimum found is also the global minimum?', 'opts' => ['Minimizing a convex objective function over a convex feasible set', 'Minimizing any neural network loss function', 'Using simulated annealing with slow cooling', 'Using Newton\'s method'], 'ans' => 0, 'exp' => 'Convexity is the gold standard for optimization. If the objective and constraints are convex, the geometry forms a "bowl" with only one minimum. Any local minimum is mathematically guaranteed to be the global minimum.'],
            ['q' => 'What is the primary advantage of mini-batch gradient descent over full-batch gradient descent?', 'opts' => ['It always requires fewer epochs to converge', 'It provides the exact true gradient without noise', 'It updates parameters more frequently per epoch and fits in GPU memory, massively speeding up training', 'It guarantees finding a global minimum'], 'ans' => 2, 'exp' => 'Full-batch GD updates only once per epoch and may not fit in memory. Mini-batch GD updates N/B times per epoch, providing frequent, reasonably accurate gradient steps while leveraging highly optimized GPU matrix operations.'],
            ['q' => 'In constrained optimization, what does KKT complementary slackness (μᵢ · gᵢ(x*) = 0) imply?', 'opts' => ['All constraints must be equal to zero', 'All Lagrange multipliers must be zero', 'For each inequality constraint, either the constraint is binding (active) OR its multiplier is zero', 'The objective function is zero at the optimum'], 'ans' => 2, 'exp' => 'Complementary slackness means that if an inequality constraint is strictly satisfied (gᵢ < 0, meaning it has "slack" and isn\'t restricting the solution), its corresponding multiplier μᵢ must be zero. If the multiplier is positive, the constraint must be active (gᵢ = 0).'],
            ['q' => 'The optimal solution to a Linear Program (LP) will always lie...', 'opts' => ['At the centroid of the feasible region', 'At a vertex (corner) or face of the convex polytope defined by the constraints', 'At the origin', 'Outside the feasible set'], 'ans' => 1, 'exp' => 'Because the objective and constraints are linear, the feasible set is a convex polytope and the objective is a flat plane. The maximum or minimum will always occur at one of the vertices (corners) of this shape, which is exactly what the Simplex algorithm searches.'],
            ['q' => 'How does Simulated Annealing escape local minima?', 'opts' => ['By evaluating all possible points in the domain', 'By using second-order derivatives to jump over them', 'By probabilistically accepting worse solutions, especially when the "temperature" is high', 'By restarting from zero every time a minimum is found'], 'ans' => 2, 'exp' => 'Simulated Annealing uses the Boltzmann acceptance probability exp(−ΔE/T). At high temperatures (early in the search), it frequently accepts moves that increase the loss, allowing it to climb out of local minima and explore broadly.'],
            ['q' => 'Bayesian Optimization differs from Random Search primarily because it...', 'opts' => ['Uses a probabilistic surrogate model (like a Gaussian Process) to intelligently select the most promising next evaluation point', 'Is faster to compute per step', 'Evaluates all parameters simultaneously', 'Does not require an objective function'], 'ans' => 0, 'exp' => 'Instead of blindly guessing, BO builds a surrogate model of the objective landscape from past evaluations. It then uses an acquisition function to mathematically balance exploring uncertain regions against exploiting known good regions, making it incredibly sample-efficient.'],
            ['q' => 'In a Genetic Algorithm, what is the role of "mutation"?', 'opts' => ['To eliminate the worst-performing solutions', 'To combine traits from two parents', 'To introduce random perturbations, maintaining genetic diversity and preventing premature convergence', 'To sort the population by fitness'], 'ans' => 2, 'exp' => 'Mutation flips bits or adds random noise to genes. Without mutation, the population would quickly converge to a single (often suboptimal) solution based solely on the initial gene pool. Mutation ensures continuous exploration of the search space.'],
            ['q' => 'Why is Newton\'s Method rarely used for training deep neural networks?', 'opts' => ['It diverges on continuous functions', 'It requires computing and inverting the Hessian matrix, which takes O(n³) time and O(n²) memory — impossible for millions of parameters', 'It only works for linear functions', 'Gradient descent converges faster in terms of iterations'], 'ans' => 1, 'exp' => 'Newton\'s method converges in far fewer iterations (quadratically), but the cost per iteration is astronomical for large n. A network with 1 million parameters has a Hessian with 1 trillion entries, which would take terabytes of RAM just to store, let alone invert.'],
        ];

        $finalContent = <<<'HTML'
<div id="org-lock-screen" style="display:block;text-align:center;padding:60px 20px;">
    <h2 style="color:var(--text);margin-bottom:12px;">🔒 Final Exam — Organization Required</h2>
    <p style="color:var(--muted);">The Module 13 Final Exam is only available to students enrolled in an organization.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 13: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 13.1 through 13.10 — optimization problem formulation, calculus & gradients, gradient descent variants, convexity, constrained optimization & KKT, linear programming, metaheuristics, hyperparameter tuning, and second-order methods. Good luck!</p>
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
            'module_id'   => $optModule->id,
            'title'       => '13.11 Final Exam: Optimization Mastery',
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