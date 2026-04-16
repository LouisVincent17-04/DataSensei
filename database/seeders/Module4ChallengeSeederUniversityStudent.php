<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module4ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Mathematical Analysis I')
                 ->delete();

        $this->command->info("Creating Module 4 — Mathematical Analysis I (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Mathematical Analysis I',
            'description'           => 'Test your analytical understanding of limits, continuity, derivatives, and basic integration. Questions require tracing calculations, applying rules, and reasoning through multi-step problems.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 750,
            'order_index'           => 4,
        ]);

        $this->command->info("Seeding 50 university-level Mathematical Analysis I questions...");

        $qaData = [

            // ── LIMITS: ALGEBRAIC EVALUATION ─────────────────────────────
            [
                'q' => "Evaluate: lim(x→3) (x² − 9) / (x − 3)",
                'opts' => [
                    ['0', false],
                    ['3', false],
                    ['6', true],
                    ['Undefined', false],
                ],
            ],
            [
                'q' => "What technique is used to evaluate lim(x→2) (x² − 4) / (x − 2) when direct substitution gives 0/0?",
                'opts' => [
                    ['L\'Hôpital\'s Rule', false],
                    ['Factoring and cancelling the common factor', true],
                    ['Taking the square root of the numerator', false],
                    ['Multiplying by the conjugate', false],
                ],
            ],
            [
                'q' => "Evaluate: lim(x→∞) (3x² + 2x) / (x² − 5)",
                'opts' => [
                    ['0', false],
                    ['2', false],
                    ['3', true],
                    ['∞', false],
                ],
            ],
            [
                'q' => "lim(x→0) sin(x) / x = ?",
                'opts' => [
                    ['0', false],
                    ['∞', false],
                    ['Undefined', false],
                    ['1', true],
                ],
            ],
            [
                'q' => "What is lim(x→0⁺) ln(x)?",
                'opts' => [
                    ['0', false],
                    ['1', false],
                    ['−∞', true],
                    ['+∞', false],
                ],
            ],
            [
                'q' => "Using the Squeeze Theorem, if g(x) ≤ f(x) ≤ h(x) and lim g(x) = lim h(x) = L, then:",
                'opts' => [
                    ['lim f(x) = 0', false],
                    ['lim f(x) = L', true],
                    ['lim f(x) is undefined', false],
                    ['lim f(x) = g(x) + h(x)', false],
                ],
            ],
            [
                'q' => "Evaluate: lim(x→1) (x³ − 1) / (x − 1)",
                'opts' => [
                    ['1', false],
                    ['2', false],
                    ['3', true],
                    ['0', false],
                ],
            ],
            [
                'q' => "What is lim(x→∞) (5x³ + 2) / (2x³ − x)?",
                'opts' => [
                    ['0', false],
                    ['5', false],
                    ['5/2', true],
                    ['∞', false],
                ],
            ],

            // ── CONTINUITY ────────────────────────────────────────────────
            [
                'q' => "Which THREE conditions must hold for f to be continuous at x = a?",
                'opts' => [
                    ['f is defined at a, the limit exists at a, and the limit equals f(a)', true],
                    ['f is differentiable at a, increasing at a, and f(a) > 0', false],
                    ['The limit exists at a, f(a) = 0, and f is linear', false],
                    ['f is defined at a, f\'(a) exists, and f is bounded', false],
                ],
            ],
            [
                'q' => "f(x) = (x² − 4)/(x − 2). What type of discontinuity does this have at x = 2?",
                'opts' => [
                    ['Jump discontinuity', false],
                    ['Infinite discontinuity', false],
                    ['Removable discontinuity', true],
                    ['Oscillatory discontinuity', false],
                ],
            ],
            [
                'q' => "The Intermediate Value Theorem guarantees that if f is continuous on [a, b] and N is between f(a) and f(b), then:",
                'opts' => [
                    ['f(a) = f(b)', false],
                    ['There exists c in (a, b) such that f(c) = N', true],
                    ['f is differentiable on [a, b]', false],
                    ['The function has a minimum at c', false],
                ],
            ],
            [
                'q' => "Which function is continuous everywhere on ℝ?",
                'opts' => [
                    ['f(x) = 1/x', false],
                    ['f(x) = √x', false],
                    ['f(x) = tan(x)', false],
                    ['f(x) = e^x', true],
                ],
            ],

            // ── DERIVATIVES: RULES ────────────────────────────────────────
            [
                'q' => "Using the product rule, what is the derivative of f(x) = x² · sin(x)?",
                'opts' => [
                    ['2x · cos(x)', false],
                    ['2x · sin(x) + x² · cos(x)', true],
                    ['x² · cos(x)', false],
                    ['2x · sin(x) − x² · cos(x)', false],
                ],
            ],
            [
                'q' => "Using the quotient rule, what is d/dx [sin(x)/x]?",
                'opts' => [
                    ['cos(x)/x', false],
                    ['[x·cos(x) − sin(x)] / x²', true],
                    ['[sin(x) − x·cos(x)] / x²', false],
                    ['cos(x)/x − sin(x)/x²', false],
                ],
            ],
            [
                'q' => "Using the chain rule, what is the derivative of f(x) = (3x + 1)⁵?",
                'opts' => [
                    ['5(3x + 1)⁴', false],
                    ['15(3x + 1)⁴', true],
                    ['5(3x + 1)⁴ · 3x', false],
                    ['(3x + 1)⁵ · 3', false],
                ],
            ],
            [
                'q' => "What is the derivative of f(x) = ln(x)?",
                'opts' => [
                    ['ln(x) / x', false],
                    ['x · ln(x)', false],
                    ['1 / x', true],
                    ['e^x', false],
                ],
            ],
            [
                'q' => "What is the derivative of f(x) = e^(3x)?",
                'opts' => [
                    ['e^(3x)', false],
                    ['3e^x', false],
                    ['3e^(3x)', true],
                    ['e^(3x) / 3', false],
                ],
            ],
            [
                'q' => "What is the second derivative of f(x) = x⁴?",
                'opts' => [
                    ['4x³', false],
                    ['12x²', true],
                    ['24x', false],
                    ['4x', false],
                ],
            ],
            [
                'q' => "What is the derivative of f(x) = sin(x²)?",
                'opts' => [
                    ['cos(x²)', false],
                    ['2x · cos(x²)', true],
                    ['2x · sin(x²)', false],
                    ['cos(2x)', false],
                ],
            ],
            [
                'q' => "If f(x) = x³ − 6x² + 9x, what are the critical points?",
                'opts' => [
                    ['x = 0 and x = 3', false],
                    ['x = 1 and x = 3', true],
                    ['x = 2 and x = 4', false],
                    ['x = −1 and x = 3', false],
                ],
            ],

            // ── APPLICATIONS OF DERIVATIVES ───────────────────────────────
            [
                'q' => "The Mean Value Theorem states that for f continuous on [a, b] and differentiable on (a, b), there exists c such that:",
                'opts' => [
                    ["f'(c) = f(b) − f(a)", false],
                    ["f'(c) = [f(b) − f(a)] / (b − a)", true],
                    ["f'(c) = 0", false],
                    ["f(c) = [a + b] / 2", false],
                ],
            ],
            [
                'q' => "A function is concave up on an interval when:",
                'opts' => [
                    ["f'(x) < 0", false],
                    ["f'(x) > 0", false],
                    ["f''(x) > 0", true],
                    ["f''(x) < 0", false],
                ],
            ],
            [
                'q' => "At an inflection point of f(x):",
                'opts' => [
                    ["f'(x) = 0", false],
                    ["f(x) = 0", false],
                    ["f''(x) changes sign", true],
                    ["f(x) reaches its maximum", false],
                ],
            ],
            [
                'q' => "To find the absolute maximum of f(x) = −x² + 4x on [0, 4], you should:",
                'opts' => [
                    ['Evaluate f only at the endpoints', false],
                    ['Find critical points then compare f values at critical points and endpoints', true],
                    ['Set f(x) = 0 and solve', false],
                    ['Compute the second derivative only', false],
                ],
            ],
            [
                'q' => "What does the second derivative test conclude when f'(c) = 0 and f''(c) > 0?",
                'opts' => [
                    ['f has a local maximum at x = c', false],
                    ['f has an inflection point at x = c', false],
                    ['The test is inconclusive', false],
                    ['f has a local minimum at x = c', true],
                ],
            ],

            // ── BASIC INTEGRATION ─────────────────────────────────────────
            [
                'q' => "What is ∫ x³ dx?",
                'opts' => [
                    ['3x² + C', false],
                    ['x⁴ + C', false],
                    ['x⁴/4 + C', true],
                    ['4x⁴ + C', false],
                ],
            ],
            [
                'q' => "What is ∫ (2x + 5) dx?",
                'opts' => [
                    ['2 + C', false],
                    ['x² + 5x + C', true],
                    ['2x² + 5x + C', false],
                    ['x² + C', false],
                ],
            ],
            [
                'q' => "Evaluate the definite integral: ∫₀² x dx",
                'opts' => [
                    ['1', false],
                    ['4', false],
                    ['2', true],
                    ['0', false],
                ],
            ],
            [
                'q' => "What is ∫ e^x dx?",
                'opts' => [
                    ['x · e^x + C', false],
                    ['e^x / x + C', false],
                    ['e^(x+1) + C', false],
                    ['e^x + C', true],
                ],
            ],
            [
                'q' => "What is ∫ (1/x) dx?",
                'opts' => [
                    ['x² / 2 + C', false],
                    ['ln|x| + C', true],
                    ['1/x² + C', false],
                    ['e^x + C', false],
                ],
            ],
            [
                'q' => "The Fundamental Theorem of Calculus Part 1 states that if F(x) = ∫ₐˣ f(t) dt, then:",
                'opts' => [
                    ["F(x) = f(x) + C", false],
                    ["F'(x) = f(x)", true],
                    ["F'(x) = F(a) − F(x)", false],
                    ["F(x) = f(a) − f(x)", false],
                ],
            ],
            [
                'q' => "Evaluate: ∫₁³ (3x²) dx",
                'opts' => [
                    ['9', false],
                    ['18', false],
                    ['26', true],
                    ['27', false],
                ],
            ],

            // ── SEQUENCES & SERIES ────────────────────────────────────────
            [
                'q' => "Does the sequence aₙ = 1/n converge or diverge as n → ∞?",
                'opts' => [
                    ['Diverges to ∞', false],
                    ['Diverges to −∞', false],
                    ['Converges to 1', false],
                    ['Converges to 0', true],
                ],
            ],
            [
                'q' => "What is the sum of the geometric series: 1 + 1/2 + 1/4 + 1/8 + … ?",
                'opts' => [
                    ['1', false],
                    ['∞', false],
                    ['3/2', false],
                    ['2', true],
                ],
            ],
            [
                'q' => "The harmonic series 1 + 1/2 + 1/3 + 1/4 + … is known to:",
                'opts' => [
                    ['Converge to π', false],
                    ['Converge to 2', false],
                    ['Converge to ln(2)', false],
                    ['Diverge', true],
                ],
            ],
            [
                'q' => "What is the common ratio of the geometric sequence 5, 15, 45, 135, …?",
                'opts' => [
                    ['5', false],
                    ['10', false],
                    ['3', true],
                    ['1/3', false],
                ],
            ],

            // ── MULTI-STEP TRACING ────────────────────────────────────────
            [
                'q' => "Given f(x) = x³ − 3x, find the interval(s) where f is increasing.",
                'opts' => [
                    ['(−1, 1)', false],
                    ['(−∞, −1) and (1, ∞)', true],
                    ['(0, ∞)', false],
                    ['(−3, 3)', false],
                ],
            ],
            [
                'q' => "For f(x) = x² − 4x + 3, what is the vertex (minimum point)?",
                'opts' => [
                    ['(2, −1)', true],
                    ['(4, 3)', false],
                    ['(0, 3)', false],
                    ['(1, 0)', false],
                ],
            ],
            [
                'q' => "What is the linearization (tangent line approximation) of f(x) = √x at x = 9?",
                'opts' => [
                    ['L(x) = 3 + (1/6)(x − 9)', true],
                    ['L(x) = 9 + 3(x − 9)', false],
                    ['L(x) = 3 + 3(x − 9)', false],
                    ['L(x) = √9 + x', false],
                ],
            ],
            [
                'q' => "What is the Taylor series expansion of e^x about x = 0 (first three terms)?",
                'opts' => [
                    ['1 + x + x² + …', false],
                    ['x + x²/2 + x³/6 + …', false],
                    ['1 + x + x²/2! + …', true],
                    ['1 − x + x²/2! − …', false],
                ],
            ],
            [
                'q' => "Implicit differentiation: if x² + y² = 25, what is dy/dx?",
                'opts' => [
                    ['x/y', false],
                    ['−x/y', true],
                    ['y/x', false],
                    ['2x + 2y', false],
                ],
            ],
            [
                'q' => "Using L'Hôpital's Rule, evaluate lim(x→0) sin(x)/x when direct substitution gives 0/0.",
                'opts' => [
                    ['0', false],
                    ['∞', false],
                    ['sin(1)', false],
                    ['1', true],
                ],
            ],
            [
                'q' => "What is the area between f(x) = x² and g(x) = x on the interval [0, 1]?",
                'opts' => [
                    ['1/3', false],
                    ['1/6', true],
                    ['1/2', false],
                    ['1', false],
                ],
            ],
            [
                'q' => "For f(x) = e^x − x, at what x does the minimum occur?",
                'opts' => [
                    ['x = 0', true],
                    ['x = 1', false],
                    ['x = e', false],
                    ['x = −1', false],
                ],
            ],
            [
                'q' => "What is the derivative of f(x) = arctan(x)?",
                'opts' => [
                    ['−1/(1 + x²)', false],
                    ['1/√(1 − x²)', false],
                    ['1/(1 + x²)', true],
                    ['tan(x)', false],
                ],
            ],
            [
                'q' => "Which test determines if an alternating series ∑(−1)ⁿ bₙ converges?",
                'opts' => [
                    ['Ratio Test', false],
                    ['Root Test', false],
                    ['Alternating Series Test (Leibniz)', true],
                    ['Integral Test', false],
                ],
            ],
            [
                'q' => "For f(x) = x⁴ − 8x², how many inflection points does f have?",
                'opts' => [
                    ['0', false],
                    ['1', false],
                    ['2', true],
                    ['4', false],
                ],
            ],
            [
                'q' => "Which substitution simplifies ∫ 2x(x² + 1)⁵ dx?",
                'opts' => [
                    ['u = x² + 1, du = 2x dx', true],
                    ['u = x + 1, du = dx', false],
                    ['u = 2x, du = 2 dx', false],
                    ['u = (x² + 1)⁵, du = 5u⁴ dx', false],
                ],
            ],
            [
                'q' => "Evaluate ∫₀¹ 2x(x² + 1)⁵ dx after the substitution u = x² + 1.",
                'opts' => [
                    ['2⁶/6 − 1/6', false],
                    ['(2⁶ − 1)/6', true],
                    ['2⁵/5', false],
                    ['2⁶ − 1', false],
                ],
            ],
            [
                'q' => "What is the radius of convergence of the power series ∑ xⁿ/n! ?",
                'opts' => [
                    ['0', false],
                    ['1', false],
                    ['e', false],
                    ['∞', true],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 4 — Mathematical Analysis I (University Student).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: University Student");
    }
}