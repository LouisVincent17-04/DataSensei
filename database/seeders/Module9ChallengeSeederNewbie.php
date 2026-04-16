<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module9ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 9 — Applied Matrix Analysis (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Applied Matrix Analysis',
            'description'           => 'Test your knowledge of the very basics of matrices — what they are, how to read them, and simple operations like addition and scalar multiplication. No advanced math assumed!',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 9,
        ]);

        $this->command->info("Seeding 50 newbie-friendly questions...");

        $qaData = [

            // ── WHAT IS A MATRIX ──────────────────────────────────────────
            [
                'q' => 'What is a matrix in mathematics?',
                'opts' => [
                    ['A single number', false],
                    ['A rectangular array of numbers arranged in rows and columns', true],
                    ['A type of graph', false],
                    ['A list of words', false],
                ],
            ],
            [
                'q' => 'What do we call the horizontal lines of numbers in a matrix?',
                'opts' => [
                    ['Columns', false],
                    ['Rows', true],
                    ['Diagonals', false],
                    ['Layers', false],
                ],
            ],
            [
                'q' => 'What do we call the vertical lines of numbers in a matrix?',
                'opts' => [
                    ['Rows', false],
                    ['Levels', false],
                    ['Columns', true],
                    ['Stacks', false],
                ],
            ],
            [
                'q' => 'A matrix with 3 rows and 2 columns has what size (dimensions)?',
                'opts' => [
                    ['2 × 3', false],
                    ['3 × 2', true],
                    ['6 × 1', false],
                    ['3 × 3', false],
                ],
            ],
            [
                'q' => 'Which notation correctly refers to the element in row 2, column 3 of matrix A?',
                'opts' => [
                    ['A(3,2)', false],
                    ['A[2,3]', false],
                    ['A₂₃', true],
                    ['A(2+3)', false],
                ],
            ],
            [
                'q' => 'A matrix that has the same number of rows and columns is called a _____ matrix.',
                'opts' => [
                    ['Rectangular matrix', false],
                    ['Square matrix', true],
                    ['Identity matrix', false],
                    ['Zero matrix', false],
                ],
            ],
            [
                'q' => 'What is a matrix called if all of its entries are zero?',
                'opts' => [
                    ['Identity matrix', false],
                    ['Diagonal matrix', false],
                    ['Zero matrix', true],
                    ['Unit matrix', false],
                ],
            ],
            [
                'q' => 'How many elements (entries) does a 2 × 4 matrix have in total?',
                'opts' => [
                    ['6', false],
                    ['8', true],
                    ['4', false],
                    ['2', false],
                ],
            ],

            // ── READING A MATRIX ─────────────────────────────────────────
            [
                'q' => "Given matrix A = [[1, 2], [3, 4]], what is the value of A₁₂ (row 1, column 2)?",
                'opts' => [
                    ['1', false],
                    ['3', false],
                    ['2', true],
                    ['4', false],
                ],
            ],
            [
                'q' => "Given matrix A = [[5, 6], [7, 8]], what is A₂₁ (row 2, column 1)?",
                'opts' => [
                    ['6', false],
                    ['5', false],
                    ['8', false],
                    ['7', true],
                ],
            ],
            [
                'q' => "A = [[4, 0], [0, 9]]. What is the value of A₁₁?",
                'opts' => [
                    ['0', false],
                    ['9', false],
                    ['4', true],
                    ['1', false],
                ],
            ],
            [
                'q' => "A = [[2, 5, 1], [3, 0, 4]]. What is A₂₃ (row 2, column 3)?",
                'opts' => [
                    ['3', false],
                    ['0', false],
                    ['5', false],
                    ['4', true],
                ],
            ],

            // ── MATRIX ADDITION ──────────────────────────────────────────
            [
                'q' => 'To add two matrices, what must be true about their sizes?',
                'opts' => [
                    ['One must be square', false],
                    ['They must be the same size (same dimensions)', true],
                    ['They can be any size', false],
                    ['One must be all zeros', false],
                ],
            ],
            [
                'q' => "What is [[1, 2], [3, 4]] + [[5, 6], [7, 8]]?",
                'opts' => [
                    ['[[6, 8], [10, 12]]', true],
                    ['[[5, 12], [21, 32]]', false],
                    ['[[6, 6], [10, 10]]', false],
                    ['[[4, 4], [4, 4]]', false],
                ],
            ],
            [
                'q' => "What is [[3, 1], [0, 2]] + [[2, 4], [5, 1]]?",
                'opts' => [
                    ['[[5, 4], [5, 4]]', false],
                    ['[[5, 5], [5, 3]]', true],
                    ['[[1, 3], [5, 1]]', false],
                    ['[[6, 4], [0, 2]]', false],
                ],
            ],
            [
                'q' => 'If A and B are both 2×3 matrices, what is the size of A + B?',
                'opts' => [
                    ['3×2', false],
                    ['4×6', false],
                    ['2×3', true],
                    ['1×1', false],
                ],
            ],
            [
                'q' => 'Can you add a 2×2 matrix to a 3×3 matrix?',
                'opts' => [
                    ['Yes, always', false],
                    ['Yes, if one is all zeros', false],
                    ['No, they must be the same size', true],
                    ['Yes, but only if both are square', false],
                ],
            ],

            // ── SCALAR MULTIPLICATION ────────────────────────────────────
            [
                'q' => 'What does "scalar multiplication" mean for a matrix?',
                'opts' => [
                    ['Adding two matrices together', false],
                    ['Multiplying two matrices together', false],
                    ['Multiplying every element in the matrix by a single number', true],
                    ['Flipping the matrix upside down', false],
                ],
            ],
            [
                'q' => "What is 3 × [[1, 2], [3, 4]]?",
                'opts' => [
                    ['[[3, 2], [3, 4]]', false],
                    ['[[3, 6], [9, 12]]', true],
                    ['[[1, 6], [9, 4]]', false],
                    ['[[4, 5], [6, 7]]', false],
                ],
            ],
            [
                'q' => "What is 2 × [[0, 5], [3, 1]]?",
                'opts' => [
                    ['[[0, 10], [6, 2]]', true],
                    ['[[2, 5], [3, 2]]', false],
                    ['[[0, 25], [9, 1]]', false],
                    ['[[2, 7], [5, 3]]', false],
                ],
            ],
            [
                'q' => "If you multiply any matrix by the scalar 0, what do you get?",
                'opts' => [
                    ['The same matrix unchanged', false],
                    ['The identity matrix', false],
                    ['A zero matrix', true],
                    ['An error', false],
                ],
            ],
            [
                'q' => "If you multiply a matrix by the scalar 1, what do you get?",
                'opts' => [
                    ['A zero matrix', false],
                    ['The same matrix unchanged', true],
                    ['The transpose of the matrix', false],
                    ['The inverse of the matrix', false],
                ],
            ],

            // ── MATRIX SUBTRACTION ───────────────────────────────────────
            [
                'q' => "What is [[5, 3], [2, 8]] − [[1, 1], [1, 1]]?",
                'opts' => [
                    ['[[4, 2], [1, 7]]', true],
                    ['[[6, 4], [3, 9]]', false],
                    ['[[4, 3], [2, 8]]', false],
                    ['[[5, 3], [2, 7]]', false],
                ],
            ],
            [
                'q' => "What is [[7, 2], [4, 6]] − [[3, 2], [4, 5]]?",
                'opts' => [
                    ['[[10, 4], [8, 11]]', false],
                    ['[[3, 0], [0, 1]]', false],
                    ['[[4, 0], [0, 1]]', true],
                    ['[[4, 0], [0, 6]]', false],
                ],
            ],

            // ── TRANSPOSE ────────────────────────────────────────────────
            [
                'q' => 'What does "transposing" a matrix mean?',
                'opts' => [
                    ['Multiplying all elements by -1', false],
                    ['Swapping the rows and columns', true],
                    ['Adding 1 to every element', false],
                    ['Reversing the order of rows', false],
                ],
            ],
            [
                'q' => "What is the transpose of [[1, 2], [3, 4]]?",
                'opts' => [
                    ['[[4, 3], [2, 1]]', false],
                    ['[[2, 1], [4, 3]]', false],
                    ['[[1, 3], [2, 4]]', true],
                    ['[[1, 2], [3, 4]]', false],
                ],
            ],
            [
                'q' => "If A has size 2×3, what is the size of its transpose Aᵀ?",
                'opts' => [
                    ['2×3', false],
                    ['3×3', false],
                    ['3×2', true],
                    ['2×2', false],
                ],
            ],
            [
                'q' => 'A matrix that is equal to its own transpose (A = Aᵀ) is called a _____ matrix.',
                'opts' => [
                    ['Identity matrix', false],
                    ['Symmetric matrix', true],
                    ['Diagonal matrix', false],
                    ['Triangular matrix', false],
                ],
            ],

            // ── IDENTITY MATRIX ──────────────────────────────────────────
            [
                'q' => 'What is special about the identity matrix?',
                'opts' => [
                    ['All entries are 1', false],
                    ['All entries are 0', false],
                    ['1s on the main diagonal and 0s everywhere else', true],
                    ['It has only one row', false],
                ],
            ],
            [
                'q' => "What is the 2×2 identity matrix?",
                'opts' => [
                    ['[[0, 1], [1, 0]]', false],
                    ['[[1, 1], [1, 1]]', false],
                    ['[[1, 0], [0, 1]]', true],
                    ['[[0, 0], [0, 0]]', false],
                ],
            ],
            [
                'q' => 'If you multiply any matrix A by the identity matrix I of the right size, what do you get?',
                'opts' => [
                    ['The zero matrix', false],
                    ['The transpose of A', false],
                    ['A itself (unchanged)', true],
                    ['The inverse of A', false],
                ],
            ],

            // ── VECTORS ──────────────────────────────────────────────────
            [
                'q' => 'A matrix with only one column is called a _____ vector.',
                'opts' => [
                    ['Row vector', false],
                    ['Column vector', true],
                    ['Zero vector', false],
                    ['Unit vector', false],
                ],
            ],
            [
                'q' => 'A matrix with only one row is called a _____ vector.',
                'opts' => [
                    ['Column vector', false],
                    ['Row vector', true],
                    ['Square vector', false],
                    ['Null vector', false],
                ],
            ],
            [
                'q' => "What is the dimension (size) of the column vector [[3], [5], [7]]?",
                'opts' => [
                    ['1×3', false],
                    ['3×3', false],
                    ['3×1', true],
                    ['1×1', false],
                ],
            ],

            // ── SCALAR vs VECTOR vs MATRIX ───────────────────────────────
            [
                'q' => 'Which of these is a scalar (a single number, not a matrix)?',
                'opts' => [
                    ['[[3, 4]]', false],
                    ['[[1], [2]]', false],
                    ['7', true],
                    ['[[0, 0], [0, 0]]', false],
                ],
            ],
            [
                'q' => 'How many rows does the matrix [[9, 2, 1], [4, 5, 6], [7, 8, 3]] have?',
                'opts' => [
                    ['2', false],
                    ['9', false],
                    ['3', true],
                    ['1', false],
                ],
            ],
            [
                'q' => 'What is the total number of entries in a 4×3 matrix?',
                'opts' => [
                    ['7', false],
                    ['12', true],
                    ['4', false],
                    ['3', false],
                ],
            ],

            // ── DIAGONAL MATRIX ──────────────────────────────────────────
            [
                'q' => 'What defines a diagonal matrix?',
                'opts' => [
                    ['Every entry is a 1', false],
                    ['Non-zero entries only on the main diagonal, zeros elsewhere', true],
                    ['All rows are the same', false],
                    ['It has no inverse', false],
                ],
            ],
            [
                'q' => "Which of these is a diagonal matrix?",
                'opts' => [
                    ['[[1, 2], [0, 3]]', false],
                    ['[[5, 0], [0, 3]]', true],
                    ['[[1, 1], [1, 1]]', false],
                    ['[[0, 2], [2, 0]]', false],
                ],
            ],

            // ── UPPER / LOWER TRIANGULAR ─────────────────────────────────
            [
                'q' => 'An upper triangular matrix has all zeros _____ the main diagonal.',
                'opts' => [
                    ['Above', false],
                    ['On', false],
                    ['Below', true],
                    ['To the right of', false],
                ],
            ],
            [
                'q' => "Which of these is an upper triangular matrix?",
                'opts' => [
                    ['[[0, 0], [3, 5]]', false],
                    ['[[1, 4], [0, 2]]', true],
                    ['[[1, 0], [3, 2]]', false],
                    ['[[3, 3], [3, 3]]', false],
                ],
            ],

            // ── BASIC PROPERTIES ─────────────────────────────────────────
            [
                'q' => 'Is matrix addition commutative? (Does A + B = B + A?)',
                'opts' => [
                    ['No, never', false],
                    ['Only if both are square', false],
                    ['Yes, always', true],
                    ['Only if both are diagonal', false],
                ],
            ],
            [
                'q' => 'If A is a 3×3 matrix, what is A + 0 (where 0 is the 3×3 zero matrix)?',
                'opts' => [
                    ['The zero matrix', false],
                    ['The identity matrix', false],
                    ['A (unchanged)', true],
                    ['Twice A', false],
                ],
            ],
            [
                'q' => 'What operation "flips" a matrix so that element aᵢⱼ becomes aⱼᵢ?',
                'opts' => [
                    ['Scalar multiplication', false],
                    ['Matrix addition', false],
                    ['Transpose', true],
                    ['Inversion', false],
                ],
            ],

            // ── MISC ─────────────────────────────────────────────────────
            [
                'q' => 'Matrices are widely used in which of the following real-world applications?',
                'opts' => [
                    ['Cooking recipes', false],
                    ['Computer graphics, engineering, and data science', true],
                    ['Only in pure math classrooms', false],
                    ['Only in statistics textbooks', false],
                ],
            ],
            [
                'q' => 'What letter is most commonly used to represent a matrix in math notation?',
                'opts' => [
                    ['x', false],
                    ['n', false],
                    ['A (or other capital letters)', true],
                    ['f', false],
                ],
            ],
            [
                'q' => 'A 1×1 matrix has exactly _____ element.',
                'opts' => [
                    ['0', false],
                    ['2', false],
                    ['1', true],
                    ['4', false],
                ],
            ],
            [
                'q' => 'Matrices are said to be "equal" when what condition is met?',
                'opts' => [
                    ['They have the same number of rows', false],
                    ['They have the same size AND every corresponding entry is equal', true],
                    ['All their entries sum to the same number', false],
                    ['They are both square', false],
                ],
            ],
            [
                'q' => 'What is the main diagonal of a matrix?',
                'opts' => [
                    ['The bottom row', false],
                    ['The entries aᵢⱼ where i = j (top-left to bottom-right)', true],
                    ['The last column', false],
                    ['Any row chosen by the user', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 9 — Applied Matrix Analysis (Newbie).");
    }
}