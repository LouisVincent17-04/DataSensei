<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module4ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Mathematical Analysis I')
                 ->delete();

        $this->command->info("Creating Module 4 — Mathematical Analysis I (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Mathematical Analysis I',
            'description'           => 'Deep analysis problems: uniform continuity, measure-theoretic reasoning, advanced series, multivariable derivatives, and debugging mathematical implementations in Python. Requires strong proof intuition and optimization skills.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1500,
            'order_index'           => 4,
        ]);

        $this->command->info("Seeding 50 advanced-level Mathematical Analysis I questions...");

        $qaData = [

            // ── UNIFORM CONTINUITY & ANALYSIS THEORY ─────────────────────
            [
                'q' => "What distinguishes UNIFORM continuity from pointwise continuity?",
                'opts' => [
                    ['Uniform continuity requires f to be differentiable', false],
                    ['In uniform continuity, the same δ works for ALL x in the domain, not just at a specific point', true],
                    ['Pointwise continuity is stronger than uniform continuity', false],
                    ['Uniform continuity only applies on unbounded intervals', false],
                ],
            ],
            [
                'q' => "Is f(x) = x² uniformly continuous on ℝ?",
                'opts' => [
                    ['Yes, because it is differentiable everywhere', false],
                    ['Yes, because its derivative is bounded', false],
                    ['No, because |f(x) − f(y)| = |x+y||x−y| can be arbitrarily large for large x,y', true],
                    ['No, because it is not monotone', false],
                ],
            ],
            [
                'q' => "Cantor's theorem states that a continuous function on a closed bounded interval [a,b] is:",
                'opts' => [
                    ['Differentiable', false],
                    ['Uniformly continuous', true],
                    ['Monotone', false],
                    ['Integrable in the Riemann sense only', false],
                ],
            ],
            [
                'q' => "For the Banach fixed-point theorem, a contraction mapping T on a complete metric space satisfies d(Tx, Ty) ≤ k·d(x,y) for k < 1. What does this guarantee?",
                'opts' => [
                    ['T has infinitely many fixed points', false],
                    ['T has exactly one fixed point and the iteration xₙ₊₁ = T(xₙ) converges to it', true],
                    ['T is differentiable everywhere', false],
                    ['T preserves distances exactly', false],
                ],
            ],
            [
                'q' => "The Heine-Borel theorem states that a subset of ℝⁿ is compact if and only if it is:",
                'opts' => [
                    ['Open and bounded', false],
                    ['Connected and bounded', false],
                    ['Closed and bounded', true],
                    ['Open and unbounded', false],
                ],
            ],

            // ── ADVANCED LIMITS & L'HÔPITAL ───────────────────────────────
            [
                'q' => "Evaluate lim(x→0) (x − sin x) / x³ using L'Hôpital's Rule repeatedly.",
                'opts' => [
                    ['0', false],
                    ['1/6', true],
                    ['1/2', false],
                    ['1', false],
                ],
            ],
            [
                'q' => "lim(x→0⁺) (sin x)^x = ? (form 0⁰ — use ln technique)",
                'opts' => [
                    ['0', false],
                    ['∞', false],
                    ['e', false],
                    ['1', true],
                ],
            ],
            [
                'q' => "Evaluate lim(n→∞) n · [e^(1/n) − 1].",
                'opts' => [
                    ['0', false],
                    ['e', false],
                    ['1', true],
                    ['∞', false],
                ],
            ],
            [
                'q' => "Evaluate lim(x→∞) (1 + 1/x)^x.",
                'opts' => [
                    ['1', false],
                    ['∞', false],
                    ['0', false],
                    ['e', true],
                ],
            ],

            // ── MULTIVARIABLE DERIVATIVES ─────────────────────────────────
            [
                'q' => "For f(x, y) = x²y + y³, what is ∂f/∂x?",
                'opts' => [
                    ['2xy + 3y²', false],
                    ['2xy', true],
                    ['x² + 3y²', false],
                    ['2x + y³', false],
                ],
            ],
            [
                'q' => "For f(x, y) = x²y + y³, what is ∂²f/∂y∂x (mixed partial)?",
                'opts' => [
                    ['2y', false],
                    ['2x', true],
                    ['2xy', false],
                    ['6y', false],
                ],
            ],
            [
                'q' => "What does the gradient ∇f(x, y) represent geometrically?",
                'opts' => [
                    ['The area under f', false],
                    ['The direction of steepest ascent of f', true],
                    ['The curvature of f', false],
                    ['The second derivative of f', false],
                ],
            ],
            [
                'q' => "For f(x, y) = x² + y², what is the Hessian matrix at any point?",
                'opts' => [
                    ['[[2, 0], [0, 2]]', true],
                    ['[[2x, 0], [0, 2y]]', false],
                    ['[[1, 0], [0, 1]]', false],
                    ['[[2, 2], [2, 2]]', false],
                ],
            ],
            [
                'q' => "The directional derivative of f in direction u (unit vector) is:",
                'opts' => [
                    ['|∇f|', false],
                    ['∇f · u', true],
                    ['∇f × u', false],
                    ['∂f/∂x + ∂f/∂y', false],
                ],
            ],

            // ── ADVANCED INTEGRATION ──────────────────────────────────────
            [
                'q' => "Evaluate the improper integral ∫₁^∞ 1/x² dx.",
                'opts' => [
                    ['∞', false],
                    ['0', false],
                    ['2', false],
                    ['1', true],
                ],
            ],
            [
                'q' => "Does ∫₀^∞ e^(−x²) dx converge? If so, what is its value?",
                'opts' => [
                    ['Diverges', false],
                    ['Converges to √π / 2', true],
                    ['Converges to 1', false],
                    ['Converges to π', false],
                ],
            ],
            [
                'q' => "Evaluate ∫₀^(π/2) sin^4(x) dx using the reduction formula.",
                'opts' => [
                    ['π/4', false],
                    ['3π/16', true],
                    ['π/8', false],
                    ['3/8', false],
                ],
            ],
            [
                'q' => "What is the Riemann–Stieltjes integral ∫ₐᵇ f dg a generalization of?",
                'opts' => [
                    ['The Lebesgue integral', false],
                    ['The standard Riemann integral (when g(x) = x)', true],
                    ['Double integration', false],
                    ['The Fourier transform', false],
                ],
            ],

            // ── PYTHON CODE SNIPPETS — DEBUGGING & OPTIMIZATION ──────────
            [
                'q' => "Find the bug in this numerical integration code:\n\ndef simpson(f, a, b, n):\n    if n % 2 != 0:\n        n += 1  # Simpson's requires even n\n    h = (b - a) / n\n    total = f(a) + f(b)\n    for i in range(1, n):\n        coeff = 4 if i % 2 != 0 else 2\n        total += coeff * f(a + i * h)\n    return total * h / 3\n\nresult = simpson(lambda x: x**2, 0, 1, 4)\nprint(result)  # Should be exactly 1/3",
                'opts' => [
                    ['The coefficient for even i should be 4, not 2', false],
                    ['The formula is correct — no bug', true],
                    ['h should be (b - a) / (n + 1)', false],
                    ['total should start at f(a) + 4*f(b)', false],
                ],
            ],
            [
                'q' => "This code approximates a derivative but has a numerical stability issue. What is it?\n\ndef derivative(f, x, h=1e-15):\n    return (f(x + h) - f(x)) / h\n\nprint(derivative(lambda x: x**2, 2.0))",
                'opts' => [
                    ['The formula should be (f(x+h) − f(x−h)) / (2h)', false],
                    ['h = 1e-15 is too small; floating-point cancellation makes f(x+h) − f(x) ≈ 0', true],
                    ['lambda functions cannot be used here', false],
                    ['The result would be exactly 4.0', false],
                ],
            ],
            [
                'q' => "What is the time complexity of this series sum, and how would you improve it?\n\ndef exp_series(x, n_terms=100):\n    result = 0\n    for k in range(n_terms):\n        term = 1\n        for j in range(k):   # computing k! from scratch each time\n            term *= x / (j + 1)\n        result += term\n    return result",
                'opts' => [
                    ['O(n²) — improve by computing each term from the previous: term *= x/k', true],
                    ['O(n log n) — already optimal', false],
                    ['O(n) — no improvement needed', false],
                    ['O(n³) — use matrix multiplication instead', false],
                ],
            ],
            [
                'q' => "What does this code compute and what is its mathematical significance?\n\nimport numpy as np\n\ndef f(x): return np.exp(-x**2)\n\nx = np.linspace(-10, 10, 100000)\ndx = x[1] - x[0]\nresult = np.sum(f(x)) * dx\nprint(result)",
                'opts' => [
                    ['The maximum of e^(−x²)', false],
                    ['A numerical approximation of ∫₋∞^∞ e^(−x²) dx ≈ √π', true],
                    ['The derivative of e^(−x²) at x = 0', false],
                    ['The Fourier transform of a Gaussian', false],
                ],
            ],
            [
                'q' => "This Newton's method implementation converges slowly. Why?\n\ndef slow_newton(x0=10.0):\n    for _ in range(100):\n        x0 = x0 - (x0**3 - 2) / (3 * x0**2)\n        print(x0)\n    return x0\n\n# Finding ∛2",
                'opts' => [
                    ['The derivative 3x² is wrong', false],
                    ['The formula is correct — Newton\'s method converges quadratically here, not slowly', true],
                    ['x0 = 10 is too far from the root', false],
                    ['Should use f(x) = x³ + 2 instead', false],
                ],
            ],

            // ── FOURIER & TRANSFORMS ──────────────────────────────────────
            [
                'q' => "The Fourier series of a function f(x) with period 2π uses which orthogonal basis?",
                'opts' => [
                    ['{1, x, x², x³, …}', false],
                    ['{1, sin(x), cos(x), sin(2x), cos(2x), …}', true],
                    ['{e^x, e^(2x), e^(3x), …}', false],
                    ['{ln(x), ln(2x), ln(3x), …}', false],
                ],
            ],
            [
                'q' => "The Parseval's theorem for Fourier series states that:",
                'opts' => [
                    ['The sum of Fourier coefficients equals the integral of f', false],
                    ['∫|f|² dx = ∑|cₙ|² (energy in time domain equals energy in frequency domain)', true],
                    ['All Fourier coefficients converge to zero', false],
                    ['f can be perfectly reconstructed with finitely many terms', false],
                ],
            ],
            [
                'q' => "Gibbs phenomenon refers to:",
                'opts' => [
                    ['A divergent Fourier series', false],
                    ['The ~9% overshoot near jump discontinuities in Fourier series approximations', true],
                    ['The inability to represent smooth functions with Fourier series', false],
                    ['Numerical errors in FFT algorithms', false],
                ],
            ],

            // ── ADVANCED SERIES & CONVERGENCE ────────────────────────────
            [
                'q' => "Prove ∑(n=1 to ∞) 1/n² = π²/6 is known as:",
                'opts' => [
                    ['Euler\'s product formula', false],
                    ['The Basel problem, solved by Euler', true],
                    ['The Riemann hypothesis', false],
                    ['The Dirichlet series', false],
                ],
            ],
            [
                'q' => "What is the radius of convergence of ∑ n! · xⁿ?",
                'opts' => [
                    ['1', false],
                    ['e', false],
                    ['∞', false],
                    ['0', true],
                ],
            ],
            [
                'q' => "For the p-series ∑ 1/nᵖ, convergence requires:",
                'opts' => [
                    ['p > 0', false],
                    ['p ≥ 1', false],
                    ['p > 1', true],
                    ['p = 2', false],
                ],
            ],
            [
                'q' => "Abel's theorem states that if ∑ aₙ converges to S, then lim(x→1⁻) ∑ aₙxⁿ = ?",
                'opts' => [
                    ['0', false],
                    ['aₙ', false],
                    ['S', true],
                    ['∞', false],
                ],
            ],

            // ── MEASURE THEORY INTRO ──────────────────────────────────────
            [
                'q' => "The Lebesgue integral differs from the Riemann integral primarily in:",
                'opts' => [
                    ['Being defined only for continuous functions', false],
                    ['Partitioning the range rather than the domain, allowing integration of more general functions', true],
                    ['Using infinite series instead of limits', false],
                    ['Requiring the function to be bounded', false],
                ],
            ],
            [
                'q' => "A set has Lebesgue measure zero if:",
                'opts' => [
                    ['It contains finitely many points', false],
                    ['It can be covered by open intervals whose total length is arbitrarily small', true],
                    ['It is a closed set', false],
                    ['It has no interior points', false],
                ],
            ],
            [
                'q' => "The Cantor set is notable because it is:",
                'opts' => [
                    ['Countably infinite and has positive measure', false],
                    ['Uncountably infinite yet has Lebesgue measure zero', true],
                    ['A subset of rational numbers', false],
                    ['Open and dense in [0,1]', false],
                ],
            ],

            // ── OPTIMIZATION & APPLIED ANALYSIS ──────────────────────────
            [
                'q' => "The method of Lagrange multipliers finds extrema of f(x,y) subject to g(x,y) = 0 by solving:",
                'opts' => [
                    ['∇f = 0 and ∇g = 0 simultaneously', false],
                    ['∇f = λ∇g for some scalar λ', true],
                    ['f/g = constant', false],
                    ['The Hessian of f − g', false],
                ],
            ],
            [
                'q' => "For the optimization: maximize xy subject to x + y = 10,\nwhat is the maximum value of xy?",
                'opts' => [
                    ['20', false],
                    ['25', true],
                    ['50', false],
                    ['100', false],
                ],
            ],
            [
                'q' => "Gradient descent update rule for minimizing f(x) is:",
                'opts' => [
                    ['xₙ₊₁ = xₙ + α∇f(xₙ)', false],
                    ['xₙ₊₁ = xₙ − α∇f(xₙ)', true],
                    ['xₙ₊₁ = xₙ / ∇f(xₙ)', false],
                    ['xₙ₊₁ = xₙ · ∇f(xₙ)', false],
                ],
            ],
            [
                'q' => "In gradient descent, a learning rate α that is too large can cause:",
                'opts' => [
                    ['Convergence to a local minimum guaranteed', false],
                    ['Overshooting — the algorithm diverges or oscillates', true],
                    ['Convergence to the global minimum', false],
                    ['Zero gradient at every step', false],
                ],
            ],
            [
                'q' => "The condition number of a matrix A in numerical analysis affects:",
                'opts' => [
                    ['The rank of A', false],
                    ['How sensitive the solution of Ax = b is to small perturbations in b', true],
                    ['Whether A is symmetric', false],
                    ['The trace of A', false],
                ],
            ],

            // ── DIFFERENTIAL EQUATIONS (INTRO TO ANALYSIS) ───────────────
            [
                'q' => "The general solution of dy/dx = ky (exponential growth/decay) is:",
                'opts' => [
                    ['y = kx + C', false],
                    ['y = Ce^(kx)', true],
                    ['y = C · ln(kx)', false],
                    ['y = k · e^x', false],
                ],
            ],
            [
                'q' => "For the ODE y'' + y = 0, the general solution is:",
                'opts' => [
                    ['y = Ce^x', false],
                    ['y = C₁x + C₂', false],
                    ['y = C₁cos(x) + C₂sin(x)', true],
                    ['y = e^(−x)(C₁ + C₂x)', false],
                ],
            ],
            [
                'q' => "The Picard–Lindelöf theorem guarantees existence and uniqueness of solutions to dy/dx = f(x, y) when:",
                'opts' => [
                    ['f is continuous and bounded', false],
                    ['f is continuous and Lipschitz continuous in y', true],
                    ['f is differentiable twice', false],
                    ['The initial condition y(0) = 0', false],
                ],
            ],
            [
                'q' => "Euler's numerical method for ODEs: yₙ₊₁ = yₙ + h·f(xₙ, yₙ) has what order of local truncation error?",
                'opts' => [
                    ['O(h)', false],
                    ['O(h²)', true],
                    ['O(h³)', false],
                    ['O(1)', false],
                ],
            ],

            // ── COMPLEX ANALYSIS INTRO ────────────────────────────────────
            [
                'q' => "Euler's formula states e^(iθ) = ?",
                'opts' => [
                    ['sin θ + i cos θ', false],
                    ['cos θ + i sin θ', true],
                    ['i(cos θ + sin θ)', false],
                    ['e^θ + i·e^(iθ)', false],
                ],
            ],
            [
                'q' => "The Cauchy-Riemann equations for f(z) = u(x,y) + i·v(x,y) to be analytic are:",
                'opts' => [
                    ['∂u/∂x = ∂v/∂y and ∂u/∂y = ∂v/∂x', false],
                    ['∂u/∂x = ∂v/∂y and ∂u/∂y = −∂v/∂x', true],
                    ['∂u/∂x = −∂v/∂y and ∂u/∂y = ∂v/∂x', false],
                    ['u = v and ∂u/∂x = 0', false],
                ],
            ],

        ];

        foreach ($qaData as $data) {
            $question = ChallengeQuestion::create([
                'challenge_id'          => $challenge->id,
                'question_text'         => $data['q'],
                'challenge_category_id' => $category->id,
            ]);

            foreach ($data['opts'] as $opt) {
                ChallengeOption::create([
                    'challenge_question_id' => $question->id,
                    'option_text'           => $opt[0],
                    'is_correct'            => $opt[1],
                ]);
            }
        }

        $this->command->info("✅ Done! 50 questions seeded for Module 4 — Mathematical Analysis I (Advanced).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Advanced");
    }
}