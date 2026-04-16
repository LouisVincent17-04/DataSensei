<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module4ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Mathematical Analysis I')
                 ->delete();

        $this->command->info("Creating Module 4 — Mathematical Analysis I (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Mathematical Analysis I',
            'description'           => 'Multi-step problems involving limits, epsilon-delta reasoning, advanced differentiation, integration techniques, and convergence of series. Includes Python code snippets requiring mathematical trace-through.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 1000,
            'order_index'           => 4,
        ]);

        $this->command->info("Seeding 50 intermediate-level Mathematical Analysis I questions...");

        $qaData = [

            // ── EPSILON-DELTA LIMITS ──────────────────────────────────────
            [
                'q' => "In the formal ε-δ definition of a limit, lim(x→a) f(x) = L means:\nFor every ε > 0, there exists δ > 0 such that if 0 < |x − a| < δ, then |f(x) − L| < ε.\n\nFor f(x) = 2x and the limit at x = 3 equaling 6, which δ works for a given ε?",
                'opts' => [
                    ['δ = ε', false],
                    ['δ = ε/2', true],
                    ['δ = 2ε', false],
                    ['δ = ε²', false],
                ],
            ],
            [
                'q' => "Using the formal definition, prove lim(x→1) (3x − 1) = 2.\nWhat is the relationship between δ and ε?",
                'opts' => [
                    ['δ = ε/3', true],
                    ['δ = 3ε', false],
                    ['δ = ε', false],
                    ['δ = ε + 1', false],
                ],
            ],
            [
                'q' => "Evaluate lim(x→0) (e^x − 1 − x) / x². What technique applies here?",
                'opts' => [
                    ['Factoring', false],
                    ['Direct substitution gives 0/0; apply L\'Hôpital\'s Rule twice → result is 1/2', true],
                    ['The limit does not exist', false],
                    ['Multiply by conjugate → result is 1', false],
                ],
            ],
            [
                'q' => "Evaluate lim(x→∞) x · sin(1/x).",
                'opts' => [
                    ['0', false],
                    ['∞', false],
                    ['1', true],
                    ['sin(∞)', false],
                ],
            ],
            [
                'q' => "lim(x→0⁺) x^x = ? (Indeterminate form 0⁰)",
                'opts' => [
                    ['0', false],
                    ['∞', false],
                    ['e', false],
                    ['1', true],
                ],
            ],

            // ── ADVANCED DIFFERENTIATION ──────────────────────────────────
            [
                'q' => "Differentiate f(x) = x^x. What is f'(x)?",
                'opts' => [
                    ['x · x^(x−1)', false],
                    ['x^x · (ln x + 1)', true],
                    ['x^x · ln x', false],
                    ['x^(x+1)', false],
                ],
            ],
            [
                'q' => "Find dy/dx by implicit differentiation: sin(xy) = x + y.",
                'opts' => [
                    ['[1 − y·cos(xy)] / [x·cos(xy) − 1]', true],
                    ['cos(xy) / (x + y)', false],
                    ['[y·cos(xy)] / [1 − x·cos(xy)]', false],
                    ['1 / cos(xy)', false],
                ],
            ],
            [
                'q' => "What is the derivative of f(x) = log₃(x²)?",
                'opts' => [
                    ['2 / (x · ln 3)', true],
                    ['2x / ln 3', false],
                    ['2 / x', false],
                    ['2x / (x² · ln 3)', false],
                ],
            ],
            [
                'q' => "A particle moves with position s(t) = t³ − 6t² + 9t. At what time(s) is the particle at rest?",
                'opts' => [
                    ['t = 0 and t = 3', false],
                    ['t = 1 and t = 3', true],
                    ['t = 2 and t = 4', false],
                    ['t = 3 only', false],
                ],
            ],
            [
                'q' => "What is the derivative of f(x) = (sin x)^(cos x)?",
                'opts' => [
                    ['(sin x)^(cos x) · [cos x · (cos x / sin x) − sin x · ln(sin x)]', true],
                    ['cos x · (sin x)^(cos x − 1) · cos x', false],
                    ['−sin x · (sin x)^(cos x)', false],
                    ['(cos x)^(sin x)', false],
                ],
            ],

            // ── INTEGRATION TECHNIQUES ────────────────────────────────────
            [
                'q' => "Evaluate ∫ x · e^x dx using integration by parts.\nLet u = x, dv = e^x dx. What is the result?",
                'opts' => [
                    ['x · e^x + C', false],
                    ['e^x (x − 1) + C', true],
                    ['x² · e^x / 2 + C', false],
                    ['e^x + C', false],
                ],
            ],
            [
                'q' => "Evaluate ∫ sin²(x) dx using the identity sin²(x) = (1 − cos 2x)/2.",
                'opts' => [
                    ['sin(2x)/2 + C', false],
                    ['x/2 − sin(2x)/4 + C', true],
                    ['−cos²(x) + C', false],
                    ['x − sin(2x)/2 + C', false],
                ],
            ],
            [
                'q' => "What substitution is used to evaluate ∫ √(1 − x²) dx?",
                'opts' => [
                    ['x = tan θ', false],
                    ['x = sec θ', false],
                    ['x = sin θ', true],
                    ['u = 1 − x²', false],
                ],
            ],
            [
                'q' => "Evaluate ∫ 1/(x² − 1) dx using partial fractions.",
                'opts' => [
                    ['ln|x − 1| + ln|x + 1| + C', false],
                    ['(1/2) ln|(x−1)/(x+1)| + C', true],
                    ['1/(2x) + C', false],
                    ['arctan(x) + C', false],
                ],
            ],
            [
                'q' => "Which of the following correctly applies integration by parts to ∫ ln(x) dx?",
                'opts' => [
                    ['u = ln(x), dv = dx → result: x·ln(x) − x + C', true],
                    ['u = x, dv = ln(x) dx → result: x²/2 · ln(x) + C', false],
                    ['u = 1/x, dv = dx → result: ln(x) + C', false],
                    ['Cannot be done with integration by parts', false],
                ],
            ],

            // ── PYTHON CODE SNIPPETS — TRACE & ANALYSIS ───────────────────
            [
                'q' => "What does this code compute?\n\nimport math\n\ndef numerical_derivative(f, x, h=1e-5):\n    return (f(x + h) - f(x - h)) / (2 * h)\n\nresult = numerical_derivative(lambda x: x**3, x=2)\nprint(round(result, 4))",
                'opts' => [
                    ['6.0', false],
                    ['8.0', false],
                    ['12.0', true],
                    ['3.0', false],
                ],
            ],
            [
                'q' => "What is wrong with this limit approximation?\n\ndef limit_approx(f, a, h=1):\n    return (f(a + h) - f(a)) / h\n\n# Attempting lim(x→0) sin(x)/x\nresult = limit_approx(lambda x: math.sin(x)/x if x != 0 else 1, 0)\nprint(result)",
                'opts' => [
                    ['The lambda function is incorrect', false],
                    ['h = 1 is too large; h must approach 0 for an accurate limit approximation', true],
                    ['You cannot use lambda for this', false],
                    ['sin(x)/x is always undefined at 0', false],
                ],
            ],
            [
                'q' => "Trace this code. What is the final value of `total`?\n\ndef riemann_sum(f, a, b, n):\n    h = (b - a) / n\n    total = 0\n    for i in range(n):\n        total += f(a + i * h) * h\n    return total\n\nresult = riemann_sum(lambda x: x, 0, 2, 4)\nprint(result)",
                'opts' => [
                    ['2.0', false],
                    ['1.5', true],
                    ['4.0', false],
                    ['1.0', false],
                ],
            ],
            [
                'q' => "What mathematical concept does this Python function implement?\n\ndef f(n):\n    total = 0\n    for k in range(1, n + 1):\n        total += 1 / k\n    return total\n\nprint(f(1000))",
                'opts' => [
                    ['The exponential series', false],
                    ['The partial sum of the harmonic series', true],
                    ['The Fibonacci sequence', false],
                    ['The geometric series with ratio 1/k', false],
                ],
            ],
            [
                'q' => "This code uses Newton's method. What does it find?\n\ndef newtons_method(f, df, x0, iterations=10):\n    x = x0\n    for _ in range(iterations):\n        x = x - f(x) / df(x)\n    return x\n\nroot = newtons_method(lambda x: x**2 - 2, lambda x: 2*x, x0=1.0)\nprint(round(root, 6))",
                'opts' => [
                    ['The derivative of x² − 2', false],
                    ['The value of x where x² − 2 = 0, i.e., √2 ≈ 1.414214', true],
                    ['The integral of x² − 2', false],
                    ['The minimum of x² − 2', false],
                ],
            ],

            // ── SERIES CONVERGENCE ────────────────────────────────────────
            [
                'q' => "Apply the ratio test to ∑ n! / nⁿ. The ratio aₙ₊₁/aₙ approaches:",
                'opts' => [
                    ['e', false],
                    ['0', false],
                    ['1/e', true],
                    ['∞', false],
                ],
            ],
            [
                'q' => "Does ∑ (−1)ⁿ / n converge absolutely, conditionally, or diverge?",
                'opts' => [
                    ['Absolutely convergent', false],
                    ['Conditionally convergent', true],
                    ['Divergent', false],
                    ['Cannot be determined', false],
                ],
            ],
            [
                'q' => "What is the interval of convergence for the power series ∑ xⁿ/n (n=1 to ∞)?",
                'opts' => [
                    ['(−1, 1)', false],
                    ['[−1, 1)', true],
                    ['[−1, 1]', false],
                    ['(−1, 1]', false],
                ],
            ],
            [
                'q' => "The Maclaurin series for sin(x) is:\nsin(x) = x − x³/3! + x⁵/5! − …\n\nUsing just the first two non-zero terms, approximate sin(0.1).",
                'opts' => [
                    ['0.0998', false],
                    ['0.09983', true],
                    ['0.1', false],
                    ['0.0990', false],
                ],
            ],
            [
                'q' => "What does the root test say when L = lim(n→∞) |aₙ|^(1/n) = 0.5?",
                'opts' => [
                    ['The series diverges', false],
                    ['The test is inconclusive', false],
                    ['The series converges absolutely', true],
                    ['The series converges conditionally', false],
                ],
            ],

            // ── MULTI-STEP PROBLEMS ───────────────────────────────────────
            [
                'q' => "A rectangle is inscribed in a circle of radius 5. Maximize its area.\nLet the sides be 2x and 2y where x² + y² = 25. The maximum area is:",
                'opts' => [
                    ['25', false],
                    ['50', true],
                    ['100', false],
                    ['10π', false],
                ],
            ],
            [
                'q' => "Find the area bounded by f(x) = x² and g(x) = 2x on [0, 2].",
                'opts' => [
                    ['2/3', false],
                    ['4/3', true],
                    ['2', false],
                    ['8/3', false],
                ],
            ],
            [
                'q' => "The position of an object is s(t) = t³ − 9t. At t = 3, the object is at rest.\nWhat is the acceleration at t = 3?",
                'opts' => [
                    ['0', false],
                    ['9', false],
                    ['18', true],
                    ['27', false],
                ],
            ],
            [
                'q' => "Using the trapezoidal rule with n = 2 subintervals, approximate ∫₀² x² dx.",
                'opts' => [
                    ['8/3', false],
                    ['3', true],
                    ['4', false],
                    ['2', false],
                ],
            ],
            [
                'q' => "What is the arc length of f(x) = (2/3) x^(3/2) on [0, 3]?",
                'opts' => [
                    ['3√3 − 1', false],
                    ['14/3', true],
                    ['4√3', false],
                    ['5', false],
                ],
            ],
            [
                'q' => "The error in Taylor polynomial approximation of degree n around x = 0 is bounded by the (n+1)th derivative. For f(x) = e^x approximated by 1 + x + x²/2 on [0, 0.5], the remainder R₂ satisfies R₂ ≤:",
                'opts' => [
                    ['e^0.5 · 0.5³/6', true],
                    ['0.5²/2', false],
                    ['e · 0.5', false],
                    ['1/(3!)', false],
                ],
            ],
            [
                'q' => "A ladder 10m long leans against a wall. The base slides away at 2 m/s. How fast is the top sliding DOWN when the base is 6m from the wall?",
                'opts' => [
                    ['−3/2 m/s', true],
                    ['−2 m/s', false],
                    ['3/2 m/s', false],
                    ['−4 m/s', false],
                ],
            ],
            [
                'q' => "For the function f(x) = x/(x² + 1), find its global maximum.",
                'opts' => [
                    ['1/2 at x = 1', true],
                    ['1 at x = 1', false],
                    ['2 at x = 2', false],
                    ['No maximum exists', false],
                ],
            ],
            [
                'q' => "What is ∫ x² · e^x dx (integration by parts applied twice)?",
                'opts' => [
                    ['e^x(x² − 2x + 2) + C', true],
                    ['e^x(x² + 2x + 2) + C', false],
                    ['x² · e^x − 2e^x + C', false],
                    ['e^x · x² + C', false],
                ],
            ],
            [
                'q' => "Evaluate ∫₀^(π/2) sin²(x) cos(x) dx using the substitution u = sin(x).",
                'opts' => [
                    ['1/2', false],
                    ['1/3', true],
                    ['π/4', false],
                    ['1', false],
                ],
            ],
            [
                'q' => "What is the value of ∑(n=0 to ∞) (1/2)ⁿ?",
                'opts' => [
                    ['1', false],
                    ['∞', false],
                    ['3', false],
                    ['2', true],
                ],
            ],
            [
                'q' => "For f(x) = x³ − 3x² + 3x − 1, what is the multiplicity of the root at x = 1?",
                'opts' => [
                    ['1', false],
                    ['2', false],
                    ['3', true],
                    ['0', false],
                ],
            ],
            [
                'q' => "What is the volume of the solid formed by rotating f(x) = √x around the x-axis on [0, 4] (disk method)?",
                'opts' => [
                    ['4π', false],
                    ['8π', true],
                    ['16π', false],
                    ['2π', false],
                ],
            ],
            [
                'q' => "Which of the following correctly applies the comparison test to determine if ∑ 1/(n² + 1) converges?",
                'opts' => [
                    ['Compare to ∑ 1/n which diverges → series diverges', false],
                    ['Compare to ∑ 1/n² which converges → series converges', true],
                    ['Compare to ∑ 1/n³ which converges → inconclusive', false],
                    ['The comparison test cannot be applied here', false],
                ],
            ],
            [
                'q' => "What is the Cauchy-Schwarz inequality for integrals?\n(∫ₐᵇ f·g dx)² ≤ ?",
                'opts' => [
                    ['(∫ₐᵇ f dx) · (∫ₐᵇ g dx)', false],
                    ['(∫ₐᵇ f² dx) · (∫ₐᵇ g² dx)', true],
                    ['(∫ₐᵇ f² dx) + (∫ₐᵇ g² dx)', false],
                    ['(b − a) · max(f · g)', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 4 — Mathematical Analysis I (Intermediate).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Intermediate");
    }
}