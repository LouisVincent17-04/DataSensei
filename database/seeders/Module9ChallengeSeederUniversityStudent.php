<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module9ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 9 — Applied Matrix Analysis (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Applied Matrix Analysis',
            'description'           => 'Deepen your understanding of matrix operations — including matrix multiplication, determinants of 2×2 matrices, basic inverses, and the connection between linear systems and matrices. Analytical thinking required!',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 1000,
            'order_index'           => 9,
        ]);

        $this->command->info("Seeding 50 university-level questions...");

        $qaData = [

            // ── MATRIX MULTIPLICATION ─────────────────────────────────────
            [
                'q' => 'For the product AB to be defined, what condition must the matrices A and B satisfy?',
                'opts' => [
                    ['A and B must be the same size', false],
                    ['The number of columns of A must equal the number of rows of B', true],
                    ['Both must be square', false],
                    ['A must be larger than B', false],
                ],
            ],
            [
                'q' => 'If A is a 2×3 matrix and B is a 3×4 matrix, what is the size of the product AB?',
                'opts' => [
                    ['3×3', false],
                    ['2×4', true],
                    ['3×4', false],
                    ['2×3', false],
                ],
            ],
            [
                'q' => "Compute AB where A = [[1, 2], [3, 4]] and B = [[2, 0], [1, 3]].\nWhat is the (1,1) entry of AB?",
                'opts' => [
                    ['2', false],
                    ['4', true],
                    ['3', false],
                    ['5', false],
                ],
            ],
            [
                'q' => "For A = [[1, 2], [3, 4]] and B = [[2, 0], [1, 3]], what is the full product AB?",
                'opts' => [
                    ['[[4, 6], [10, 12]]', true],
                    ['[[2, 0], [3, 12]]', false],
                    ['[[4, 8], [10, 4]]', false],
                    ['[[3, 2], [7, 4]]', false],
                ],
            ],
            [
                'q' => 'Is matrix multiplication commutative in general? (Does AB = BA always?)',
                'opts' => [
                    ['Yes, always', false],
                    ['No, in general AB ≠ BA', true],
                    ['Yes, but only for square matrices', false],
                    ['Yes, but only for diagonal matrices', false],
                ],
            ],
            [
                'q' => "Multiply [[2, 1], [0, 3]] by [[1, 4], [2, 1]].\nWhat is the (2,2) entry of the result?",
                'opts' => [
                    ['3', false],
                    ['12', false],
                    ['7', true],
                    ['4', false],
                ],
            ],
            [
                'q' => "What is the product of the identity matrix I and any matrix A (where the sizes are compatible)?",
                'opts' => [
                    ['The zero matrix', false],
                    ['The transpose of A', false],
                    ['A itself', true],
                    ['The inverse of A', false],
                ],
            ],
            [
                'q' => "What is [[1, 0], [0, 1]] × [[5, 3], [2, 7]]?",
                'opts' => [
                    ['[[0, 0], [0, 0]]', false],
                    ['[[5, 3], [2, 7]]', true],
                    ['[[6, 3], [2, 8]]', false],
                    ['[[5, 0], [0, 7]]', false],
                ],
            ],

            // ── DETERMINANTS (2×2) ────────────────────────────────────────
            [
                'q' => 'For a 2×2 matrix [[a, b], [c, d]], the determinant is calculated as:',
                'opts' => [
                    ['a + d − b − c', false],
                    ['ad − bc', true],
                    ['ab − cd', false],
                    ['ac − bd', false],
                ],
            ],
            [
                'q' => "What is the determinant of [[3, 1], [2, 4]]?",
                'opts' => [
                    ['14', false],
                    ['10', true],
                    ['5', false],
                    ['11', false],
                ],
            ],
            [
                'q' => "What is det([[5, 2], [3, 4]])?",
                'opts' => [
                    ['26', false],
                    ['14', true],
                    ['11', false],
                    ['23', false],
                ],
            ],
            [
                'q' => 'If det(A) = 0, what does that tell you about matrix A?',
                'opts' => [
                    ['A is the identity matrix', false],
                    ['A is invertible', false],
                    ['A is singular (not invertible)', true],
                    ['A must be the zero matrix', false],
                ],
            ],
            [
                'q' => "det([[4, 2], [2, 1]]) = ?",
                'opts' => [
                    ['4', false],
                    ['0', true],
                    ['2', false],
                    ['6', false],
                ],
            ],
            [
                'q' => 'What is det(I₂) — the determinant of the 2×2 identity matrix?',
                'opts' => [
                    ['0', false],
                    ['2', false],
                    ['1', true],
                    ['4', false],
                ],
            ],

            // ── INVERSE OF A 2×2 MATRIX ──────────────────────────────────
            [
                'q' => 'The inverse of a 2×2 matrix [[a, b], [c, d]] (when it exists) is (1/det) × ___.',
                'opts' => [
                    ['[[a, b], [c, d]]', false],
                    ['[[d, −b], [−c, a]]', true],
                    ['[[d, b], [c, a]]', false],
                    ['[[−a, −b], [−c, −d]]', false],
                ],
            ],
            [
                'q' => "What is the inverse of [[2, 1], [1, 1]]? (det = 1)",
                'opts' => [
                    ['[[1, −1], [−1, 2]]', true],
                    ['[[1, 1], [1, 2]]', false],
                    ['[[2, 1], [1, 1]]', false],
                    ['[[−1, 1], [1, −2]]', false],
                ],
            ],
            [
                'q' => 'A matrix has an inverse if and only if its determinant is:',
                'opts' => [
                    ['Greater than 1', false],
                    ['Equal to 0', false],
                    ['Not equal to 0', true],
                    ['A whole number', false],
                ],
            ],
            [
                'q' => 'If A is an invertible matrix, what is A × A⁻¹?',
                'opts' => [
                    ['The zero matrix', false],
                    ['2A', false],
                    ['The identity matrix I', true],
                    ['A²', false],
                ],
            ],

            // ── SYSTEMS OF LINEAR EQUATIONS & MATRICES ───────────────────
            [
                'q' => 'A system of linear equations Ax = b can be written in matrix form. What is x (the solution)?',
                'opts' => [
                    ['x = Ab', false],
                    ['x = A⁻¹b', true],
                    ['x = bA', false],
                    ['x = b − A', false],
                ],
            ],
            [
                'q' => "The augmented matrix [A | b] for the system x + 2y = 5, 3x + 4y = 11 is:",
                'opts' => [
                    ['[[1, 2, 5], [3, 4, 11]]', true],
                    ['[[1, 3, 5], [2, 4, 11]]', false],
                    ['[[5, 2, 1], [11, 4, 3]]', false],
                    ['[[1, 2], [3, 4]]', false],
                ],
            ],
            [
                'q' => 'Which row operation is NOT valid in Gaussian elimination?',
                'opts' => [
                    ['Swapping two rows', false],
                    ['Multiplying a row by a non-zero scalar', false],
                    ['Adding a multiple of one row to another', false],
                    ['Multiplying two rows together', true],
                ],
            ],
            [
                'q' => 'A system Ax = b has no solution when the two equations represent:',
                'opts' => [
                    ['The same line', false],
                    ['Two parallel lines (that never intersect)', true],
                    ['Two perpendicular lines', false],
                    ['A line and a circle', false],
                ],
            ],
            [
                'q' => 'A system Ax = b has infinitely many solutions when:',
                'opts' => [
                    ['det(A) ≠ 0', false],
                    ['The two equations represent the same line', true],
                    ['The matrix has no zeros', false],
                    ['The matrix is diagonal', false],
                ],
            ],

            // ── RANK ─────────────────────────────────────────────────────
            [
                'q' => 'What is the rank of a matrix?',
                'opts' => [
                    ['The number of rows times the number of columns', false],
                    ['The number of linearly independent rows (or columns)', true],
                    ['The value of the determinant', false],
                    ['The number of zero entries', false],
                ],
            ],
            [
                'q' => "What is the rank of [[1, 2], [2, 4]]? (Notice the second row is twice the first.)",
                'opts' => [
                    ['2', false],
                    ['0', false],
                    ['1', true],
                    ['4', false],
                ],
            ],
            [
                'q' => 'For a 3×3 matrix, what is the maximum possible rank?',
                'opts' => [
                    ['9', false],
                    ['6', false],
                    ['3', true],
                    ['1', false],
                ],
            ],

            // ── LINEAR INDEPENDENCE ──────────────────────────────────────
            [
                'q' => 'Two vectors are linearly dependent when:',
                'opts' => [
                    ['They point in perpendicular directions', false],
                    ['One is a scalar multiple of the other', true],
                    ['They have the same length', false],
                    ['Their entries sum to the same value', false],
                ],
            ],
            [
                'q' => "Are the vectors [1, 2] and [2, 4] linearly independent?",
                'opts' => [
                    ['Yes, they are independent', false],
                    ['No, [2, 4] = 2 × [1, 2], so they are dependent', true],
                    ['Yes, because they have different entries', false],
                    ['Cannot be determined', false],
                ],
            ],
            [
                'q' => 'If the columns of a square matrix A are linearly independent, then:',
                'opts' => [
                    ['det(A) = 0', false],
                    ['A is singular', false],
                    ['A is invertible and det(A) ≠ 0', true],
                    ['A must be the identity', false],
                ],
            ],

            // ── EIGENVALUES (INTRO) ──────────────────────────────────────
            [
                'q' => 'The eigenvalues of a matrix A are the values λ that satisfy which equation?',
                'opts' => [
                    ['Ax = λ + x', false],
                    ['Ax = λx', true],
                    ['Ax = x/λ', false],
                    ['λA = x', false],
                ],
            ],
            [
                'q' => 'To find eigenvalues of A, you solve the characteristic equation:',
                'opts' => [
                    ['det(A) = 0', false],
                    ['det(A − λI) = 0', true],
                    ['det(A + λI) = 1', false],
                    ['trace(A) = λ', false],
                ],
            ],
            [
                'q' => "For A = [[3, 0], [0, 5]], what are the eigenvalues?",
                'opts' => [
                    ['3 and 5', true],
                    ['0 and 15', false],
                    ['8 and 2', false],
                    ['1 and 1', false],
                ],
            ],

            // ── TRACE ────────────────────────────────────────────────────
            [
                'q' => 'The trace of a square matrix is defined as:',
                'opts' => [
                    ['The product of all elements', false],
                    ['The determinant', false],
                    ['The sum of all diagonal entries', true],
                    ['The largest entry', false],
                ],
            ],
            [
                'q' => "What is the trace of [[2, 5], [3, 7]]?",
                'opts' => [
                    ['5', false],
                    ['9', true],
                    ['14', false],
                    ['7', false],
                ],
            ],
            [
                'q' => 'The trace of a matrix equals the sum of its ___.',
                'opts' => [
                    ['Singular values', false],
                    ['Eigenvalues', true],
                    ['Columns', false],
                    ['Row norms', false],
                ],
            ],

            // ── TRANSPOSE PROPERTIES ─────────────────────────────────────
            [
                'q' => "What is (Aᵀ)ᵀ (the transpose of the transpose of A)?",
                'opts' => [
                    ['The inverse of A', false],
                    ['The zero matrix', false],
                    ['A itself', true],
                    ['The identity matrix', false],
                ],
            ],
            [
                'q' => "What is (AB)ᵀ?",
                'opts' => [
                    ['AᵀBᵀ', false],
                    ['BᵀAᵀ', true],
                    ['ABᵀ', false],
                    ['AᵀB', false],
                ],
            ],
            [
                'q' => 'A matrix satisfying A = Aᵀ is called symmetric. Which of the following is symmetric?',
                'opts' => [
                    ['[[1, 2], [3, 4]]', false],
                    ['[[1, 3], [3, 5]]', true],
                    ['[[0, 1], [0, 0]]', false],
                    ['[[2, 0], [1, 2]]', false],
                ],
            ],

            // ── NORMS (INTRO) ────────────────────────────────────────────
            [
                'q' => 'The Euclidean (L2) norm of vector v = [3, 4] is:',
                'opts' => [
                    ['7', false],
                    ['5', true],
                    ['12', false],
                    ['25', false],
                ],
            ],
            [
                'q' => "The general formula for the Euclidean norm of vector [a, b] is:",
                'opts' => [
                    ['a + b', false],
                    ['a × b', false],
                    ['√(a² + b²)', true],
                    ['(a + b)²', false],
                ],
            ],
            [
                'q' => 'What is the norm of the zero vector [0, 0]?',
                'opts' => [
                    ['1', false],
                    ['Undefined', false],
                    ['0', true],
                    ['∞', false],
                ],
            ],

            // ── MISC ANALYTICAL ──────────────────────────────────────────
            [
                'q' => 'What is the determinant of any triangular matrix (upper or lower)?',
                'opts' => [
                    ['The sum of all entries', false],
                    ['Always 0', false],
                    ['The product of its diagonal entries', true],
                    ['The sum of its diagonal entries', false],
                ],
            ],
            [
                'q' => 'For square matrices A and B of the same size, det(AB) = ?',
                'opts' => [
                    ['det(A) + det(B)', false],
                    ['det(A) × det(B)', true],
                    ['det(A) − det(B)', false],
                    ['det(A) / det(B)', false],
                ],
            ],
            [
                'q' => "If you swap two rows of a matrix, the determinant is:",
                'opts' => [
                    ['Unchanged', false],
                    ['Multiplied by 2', false],
                    ['Negated (multiplied by −1)', true],
                    ['Set to zero', false],
                ],
            ],
            [
                'q' => 'A matrix is singular when:',
                'opts' => [
                    ['Its trace is 0', false],
                    ['Its determinant is 0', true],
                    ['It has more rows than columns', false],
                    ['All its entries are positive', false],
                ],
            ],
            [
                'q' => "What does it mean for a matrix to be 'full rank'?",
                'opts' => [
                    ['Its rank equals the larger of its row/column count', false],
                    ['Its rank equals the smaller of its row/column count', true],
                    ['Its rank is 0', false],
                    ['All entries are non-zero', false],
                ],
            ],
            [
                'q' => "The nullspace of a matrix A consists of all vectors x such that:",
                'opts' => [
                    ['Ax = I', false],
                    ['Ax = b', false],
                    ['Ax = 0', true],
                    ['Ax = A', false],
                ],
            ],
            [
                'q' => "What is (A⁻¹)⁻¹?",
                'opts' => [
                    ['The zero matrix', false],
                    ['The identity matrix', false],
                    ['A itself', true],
                    ['2A', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 9 — Applied Matrix Analysis (University Student).");
    }
}