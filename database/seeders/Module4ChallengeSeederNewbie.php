<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module4ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Mathematical Analysis I')
                 ->delete();

        $this->command->info("Creating Module 4 — Mathematical Analysis I (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Mathematical Analysis I',
            'description'           => 'Test your grasp of the very basics of mathematical analysis — number lines, simple functions, limits in plain English, and the idea behind a derivative. No prior calculus experience required.',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 4,
        ]);

        $this->command->info("Seeding 50 newbie-level Mathematical Analysis I questions...");

        $qaData = [

            // ── REAL NUMBERS & THE NUMBER LINE ───────────────────────────
            [
                'q' => 'Which of the following is a real number?',
                'opts' => [
                    ['√(-1)', false],
                    ['3.14', true],
                    ['∞', false],
                    ['undefined', false],
                ],
            ],
            [
                'q' => 'Which set of numbers includes all integers AND all fractions and decimals?',
                'opts' => [
                    ['Natural Numbers', false],
                    ['Integers', false],
                    ['Real Numbers', true],
                    ['Whole Numbers', false],
                ],
            ],
            [
                'q' => 'What is the absolute value of -7?',
                'opts' => [
                    ['-7', false],
                    ['7', true],
                    ['0', false],
                    ['49', false],
                ],
            ],
            [
                'q' => 'Which of the following correctly represents the interval "all numbers between 2 and 5, including both endpoints"?',
                'opts' => [
                    ['(2, 5)', false],
                    ['[2, 5]', true],
                    ['[2, 5)', false],
                    ['(2, 5]', false],
                ],
            ],
            [
                'q' => 'What does the notation (−∞, 3) mean on the number line?',
                'opts' => [
                    ['All numbers greater than 3', false],
                    ['All numbers less than 3, not including 3', true],
                    ['All numbers less than or equal to 3', false],
                    ['Only the number 3', false],
                ],
            ],
            [
                'q' => 'Which of the following is an irrational number?',
                'opts' => [
                    ['0.5', false],
                    ['−4', false],
                    ['√2', true],
                    ['3/4', false],
                ],
            ],
            [
                'q' => 'What is the distance between −3 and 5 on the number line?',
                'opts' => [
                    ['2', false],
                    ['8', true],
                    ['−8', false],
                    ['15', false],
                ],
            ],
            [
                'q' => 'Which inequality is true?',
                'opts' => [
                    ['−5 > −2', false],
                    ['−5 < −2', true],
                    ['−5 = −2', false],
                    ['−5 ≥ 0', false],
                ],
            ],

            // ── BASIC FUNCTIONS ───────────────────────────────────────────
            [
                'q' => 'A function f(x) = 2x + 1. What is f(3)?',
                'opts' => [
                    ['5', false],
                    ['6', false],
                    ['7', true],
                    ['8', false],
                ],
            ],
            [
                'q' => 'What is the domain of a function?',
                'opts' => [
                    ['All possible output (y) values', false],
                    ['All possible input (x) values', true],
                    ['The slope of the function', false],
                    ['The area under the function', false],
                ],
            ],
            [
                'q' => 'What is the range of a function?',
                'opts' => [
                    ['All possible input (x) values', false],
                    ['The x-intercept of the graph', false],
                    ['All possible output (y) values', true],
                    ['The maximum value only', false],
                ],
            ],
            [
                'q' => 'For f(x) = x², what is f(−4)?',
                'opts' => [
                    ['−16', false],
                    ['8', false],
                    ['16', true],
                    ['−8', false],
                ],
            ],
            [
                'q' => 'Which of the following is a linear function?',
                'opts' => [
                    ['f(x) = x²', false],
                    ['f(x) = 3x + 2', true],
                    ['f(x) = √x', false],
                    ['f(x) = 1/x', false],
                ],
            ],
            [
                'q' => 'What does it mean for a function to be "one-to-one"?',
                'opts' => [
                    ['Every output has exactly one unique input', true],
                    ['The function only has one term', false],
                    ['The graph is a straight line', false],
                    ['The domain has only one value', false],
                ],
            ],
            [
                'q' => 'What is the y-intercept of f(x) = 5x − 3?',
                'opts' => [
                    ['5', false],
                    ['3', false],
                    ['−3', true],
                    ['0', false],
                ],
            ],
            [
                'q' => 'What is the slope of the line y = −2x + 7?',
                'opts' => [
                    ['7', false],
                    ['2', false],
                    ['−7', false],
                    ['−2', true],
                ],
            ],

            // ── LIMITS (CONCEPTUAL) ───────────────────────────────────────
            [
                'q' => 'In plain English, what does lim(x→2) f(x) = 5 mean?',
                'opts' => [
                    ['f(2) is always exactly 5', false],
                    ['As x gets closer and closer to 2, f(x) gets closer and closer to 5', true],
                    ['x will never equal 2', false],
                    ['f(x) = 5 for all values of x', false],
                ],
            ],
            [
                'q' => 'If f(x) = 3 for all values of x, what is lim(x→10) f(x)?',
                'opts' => [
                    ['10', false],
                    ['0', false],
                    ['3', true],
                    ['Undefined', false],
                ],
            ],
            [
                'q' => 'What is lim(x→0) of the constant function f(x) = 7?',
                'opts' => [
                    ['0', false],
                    ['7', true],
                    ['1', false],
                    ['Undefined', false],
                ],
            ],
            [
                'q' => 'A limit "does not exist" when:',
                'opts' => [
                    ['The function value at that point is 0', false],
                    ['The left-side and right-side values are different as x approaches the point', true],
                    ['The function is a straight line', false],
                    ['x is a large number', false],
                ],
            ],
            [
                'q' => 'What is lim(x→4) of f(x) = x + 1?',
                'opts' => [
                    ['4', false],
                    ['1', false],
                    ['5', true],
                    ['0', false],
                ],
            ],
            [
                'q' => 'What does lim(x→∞) (1/x) equal?',
                'opts' => [
                    ['1', false],
                    ['∞', false],
                    ['Undefined', false],
                    ['0', true],
                ],
            ],

            // ── CONTINUITY ────────────────────────────────────────────────
            [
                'q' => 'A function is continuous at a point if:',
                'opts' => [
                    ['The function is always increasing', false],
                    ['The limit at the point equals the function value at that point, and the function is defined there', true],
                    ['The function has no negative values', false],
                    ['The derivative exists everywhere', false],
                ],
            ],
            [
                'q' => 'Which of the following functions has a discontinuity?',
                'opts' => [
                    ['f(x) = 2x + 3', false],
                    ['f(x) = x²', false],
                    ['f(x) = 1/x at x = 0', true],
                    ['f(x) = 5', false],
                ],
            ],
            [
                'q' => 'A "hole" in a graph at a single point is called what type of discontinuity?',
                'opts' => [
                    ['Jump discontinuity', false],
                    ['Infinite discontinuity', false],
                    ['Removable discontinuity', true],
                    ['Essential discontinuity', false],
                ],
            ],

            // ── DERIVATIVES (CONCEPTUAL) ──────────────────────────────────
            [
                'q' => 'In simple terms, what does a derivative tell you?',
                'opts' => [
                    ['The area under a curve', false],
                    ['The total value of a function', false],
                    ['The rate of change (slope) of a function at a point', true],
                    ['The x-intercept of a function', false],
                ],
            ],
            [
                'q' => 'What is the derivative of a constant function f(x) = 10?',
                'opts' => [
                    ['10', false],
                    ['1', false],
                    ['100', false],
                    ['0', true],
                ],
            ],
            [
                'q' => 'Using the power rule, what is the derivative of f(x) = x³?',
                'opts' => [
                    ['x²', false],
                    ['3x²', true],
                    ['3x', false],
                    ['x⁴/4', false],
                ],
            ],
            [
                'q' => 'What is the derivative of f(x) = x?',
                'opts' => [
                    ['x', false],
                    ['0', false],
                    ['2x', false],
                    ['1', true],
                ],
            ],
            [
                'q' => 'If f(x) = 5x², what is f\'(x)?',
                'opts' => [
                    ['5x', false],
                    ['10x', true],
                    ['2x', false],
                    ['5x³/3', false],
                ],
            ],
            [
                'q' => 'A positive derivative at a point means the function is:',
                'opts' => [
                    ['Decreasing at that point', false],
                    ['Flat (horizontal) at that point', false],
                    ['Increasing at that point', true],
                    ['At a minimum at that point', false],
                ],
            ],
            [
                'q' => 'At a local maximum, the derivative of the function is:',
                'opts' => [
                    ['Positive', false],
                    ['Negative', false],
                    ['Zero', true],
                    ['Undefined', false],
                ],
            ],

            // ── SEQUENCES & SERIES (INTRO) ────────────────────────────────
            [
                'q' => 'What is the next number in the sequence: 2, 4, 6, 8, …?',
                'opts' => [
                    ['9', false],
                    ['10', true],
                    ['12', false],
                    ['16', false],
                ],
            ],
            [
                'q' => 'What type of sequence is: 3, 6, 12, 24, …?',
                'opts' => [
                    ['Arithmetic', false],
                    ['Fibonacci', false],
                    ['Geometric', true],
                    ['Harmonic', false],
                ],
            ],
            [
                'q' => 'In an arithmetic sequence, consecutive terms differ by a constant called the:',
                'opts' => [
                    ['Ratio', false],
                    ['Common difference', true],
                    ['Exponent', false],
                    ['Limit', false],
                ],
            ],
            [
                'q' => 'What is the 5th term of the arithmetic sequence starting at 1 with common difference 3?',
                'opts' => [
                    ['12', false],
                    ['13', true],
                    ['15', false],
                    ['10', false],
                ],
            ],
            [
                'q' => 'A sequence converges when:',
                'opts' => [
                    ['Its terms grow without bound', false],
                    ['Its terms approach a fixed value as n → ∞', true],
                    ['It has an infinite number of terms', false],
                    ['All terms are positive', false],
                ],
            ],

            // ── BASIC INTEGRATION (CONCEPT) ───────────────────────────────
            [
                'q' => 'In simple terms, what does an integral calculate?',
                'opts' => [
                    ['The slope at a point', false],
                    ['The maximum value of a function', false],
                    ['The area under a curve', true],
                    ['The x-intercept of a function', false],
                ],
            ],
            [
                'q' => 'Integration and differentiation are said to be:',
                'opts' => [
                    ['Identical operations', false],
                    ['Inverse operations', true],
                    ['Both about finding slopes', false],
                    ['Unrelated to each other', false],
                ],
            ],
            [
                'q' => 'What is the integral of f(x) = 1 with respect to x?',
                'opts' => [
                    ['0', false],
                    ['x + C', true],
                    ['1/x + C', false],
                    ['x²/2 + C', false],
                ],
            ],
            [
                'q' => 'What does the constant "C" represent in an indefinite integral?',
                'opts' => [
                    ['The slope of the function', false],
                    ['An arbitrary constant, since the derivative of any constant is 0', true],
                    ['The area under the curve', false],
                    ['The limit of integration', false],
                ],
            ],

            // ── GENERAL MATH ANALYSIS VOCABULARY ─────────────────────────
            [
                'q' => 'What does it mean for a function to be "increasing" on an interval?',
                'opts' => [
                    ['The function values go down as x increases', false],
                    ['The function values go up as x increases', true],
                    ['The function stays constant', false],
                    ['The function is always positive', false],
                ],
            ],
            [
                'q' => 'A function that mirrors across the y-axis (f(−x) = f(x)) is called:',
                'opts' => [
                    ['Odd', false],
                    ['Linear', false],
                    ['Even', true],
                    ['Inverse', false],
                ],
            ],
            [
                'q' => 'A function where f(−x) = −f(x) is called:',
                'opts' => [
                    ['Even', false],
                    ['Odd', true],
                    ['Symmetric', false],
                    ['Constant', false],
                ],
            ],
            [
                'q' => 'What is a critical point of a function?',
                'opts' => [
                    ['A point where the function is not defined', false],
                    ['A point where the derivative equals zero or is undefined', true],
                    ['The highest point on any graph', false],
                    ['The point where the function equals zero', false],
                ],
            ],
            [
                'q' => 'Which of the following best describes a "monotonically increasing" function?',
                'opts' => [
                    ['A function that only decreases', false],
                    ['A function that goes up, then down, then up again', false],
                    ['A function that never decreases — it always goes up or stays flat', true],
                    ['A function with no critical points', false],
                ],
            ],
            [
                'q' => 'What is the x-intercept of f(x) = 2x − 6?',
                'opts' => [
                    ['6', false],
                    ['−6', false],
                    ['3', true],
                    ['2', false],
                ],
            ],
            [
                'q' => 'What does it mean when we say lim(x→a) f(x) = L?',
                'opts' => [
                    ['f(a) must equal L', false],
                    ['f(x) is undefined at x = a', false],
                    ['As x approaches a (but not necessarily equals a), f(x) approaches L', true],
                    ['L is the maximum of f(x)', false],
                ],
            ],
            [
                'q' => 'The process of finding a derivative is called:',
                'opts' => [
                    ['Integration', false],
                    ['Differentiation', true],
                    ['Extrapolation', false],
                    ['Interpolation', false],
                ],
            ],
            [
                'q' => 'What is the power rule for derivatives? For f(x) = xⁿ, f\'(x) = ?',
                'opts' => [
                    ['xⁿ⁺¹ / (n+1)', false],
                    ['n · xⁿ⁺¹', false],
                    ['n · xⁿ⁻¹', true],
                    ['(n−1) · xⁿ', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 4 — Mathematical Analysis I (Newbie).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Newbie");
    }
}