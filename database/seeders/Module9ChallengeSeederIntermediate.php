<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module9ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 9 — Applied Matrix Analysis (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Applied Matrix Analysis',
            'description'           => 'Tackle multi-step matrix problems involving LU decomposition, eigenvalue computation, diagonalization, orthogonality, and the four fundamental subspaces. Multi-step reasoning and calculation required.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1500,
            'order_index'           => 9,
        ]);

        $this->command->info("Seeding 50 intermediate-level questions...");

        $qaData = [

            // ── LU DECOMPOSITION ─────────────────────────────────────────
            [
                'q' => 'In LU decomposition A = LU, what kind of matrix is L and what kind is U?',
                'opts' => [
                    ['L is upper triangular, U is lower triangular', false],
                    ['L is lower triangular (with 1s on diagonal), U is upper triangular', true],
                    ['Both are diagonal', false],
                    ['L is orthogonal, U is symmetric', false],
                ],
            ],
            [
                'q' => "Given A = [[2, 4], [1, 3]], find L and U in the LU decomposition (no row swaps).\nWhat is the multiplier l₂₁ used to eliminate the (2,1) entry?",
                'opts' => [
                    ['2', false],
                    ['1/2', true],
                    ['1', false],
                    ['4', false],
                ],
            ],
            [
                'q' => "After performing elimination on A = [[2, 4], [1, 3]] to get U, what is U?",
                'opts' => [
                    ['[[2, 4], [0, 1]]', true],
                    ['[[2, 4], [1, 3]]', false],
                    ['[[1, 2], [0, 1]]', false],
                    ['[[2, 0], [1, 3]]', false],
                ],
            ],
            [
                'q' => 'What is the main advantage of LU decomposition when solving multiple systems Ax = b with different b vectors?',
                'opts' => [
                    ['You only need to factor A once, then solve Ly = b and Ux = y for each b', true],
                    ['You can skip computing U entirely', false],
                    ['The inverse of A is computed automatically', false],
                    ['It removes the need for back substitution', false],
                ],
            ],

            // ── EIGENVALUES & EIGENVECTORS ────────────────────────────────
            [
                'q' => "Find the eigenvalues of A = [[4, 1], [2, 3]].\nThe characteristic polynomial is λ² − 7λ + 10 = 0. What are λ₁ and λ₂?",
                'opts' => [
                    ['λ = 1 and λ = 10', false],
                    ['λ = 5 and λ = 2', true],
                    ['λ = 7 and λ = 0', false],
                    ['λ = 4 and λ = 3', false],
                ],
            ],
            [
                'q' => "For A = [[4, 1], [2, 3]] with eigenvalue λ = 5, find the eigenvector by solving (A − 5I)x = 0.\nWhich vector satisfies this?",
                'opts' => [
                    ['[1, 1]', true],
                    ['[1, 2]', false],
                    ['[2, 1]', false],
                    ['[0, 1]', false],
                ],
            ],
            [
                'q' => 'The product of all eigenvalues of a square matrix A equals:',
                'opts' => [
                    ['The trace of A', false],
                    ['The rank of A', false],
                    ['The determinant of A', true],
                    ['The norm of A', false],
                ],
            ],
            [
                'q' => 'The sum of all eigenvalues of a square matrix A equals:',
                'opts' => [
                    ['The determinant of A', false],
                    ['The trace of A (sum of diagonal entries)', true],
                    ['The rank of A', false],
                    ['The largest eigenvalue', false],
                ],
            ],
            [
                'q' => "A = [[0, 1], [−1, 0]]. The characteristic polynomial is λ² + 1 = 0.\nWhat can you say about the eigenvalues over the real numbers?",
                'opts' => [
                    ['λ = 1 and λ = −1', false],
                    ['No real eigenvalues (the roots are complex: ±i)', true],
                    ['λ = 0 (repeated)', false],
                    ['λ = 1 (repeated)', false],
                ],
            ],

            // ── DIAGONALIZATION ──────────────────────────────────────────
            [
                'q' => 'A matrix A is diagonalizable if and only if it has:',
                'opts' => [
                    ['A determinant of 1', false],
                    ['n linearly independent eigenvectors (where A is n×n)', true],
                    ['All positive eigenvalues', false],
                    ['All distinct diagonal entries', false],
                ],
            ],
            [
                'q' => 'In the diagonalization A = PDP⁻¹, what do the columns of P represent?',
                'opts' => [
                    ['Rows of A', false],
                    ['Eigenvectors of A', true],
                    ['Singular values of A', false],
                    ['Diagonal entries of A', false],
                ],
            ],
            [
                'q' => 'What does the diagonal matrix D contain in A = PDP⁻¹?',
                'opts' => [
                    ['The singular values of A', false],
                    ['The entries of A rearranged', false],
                    ['The eigenvalues of A on its diagonal', true],
                    ['The norms of the eigenvectors', false],
                ],
            ],
            [
                'q' => "If A = PDP⁻¹, what is a simple formula for A³?",
                'opts' => [
                    ['3PDP⁻¹', false],
                    ['PD³P⁻¹', true],
                    ['P³D³P⁻³', false],
                    ['3P³D', false],
                ],
            ],

            // ── ORTHOGONALITY & GRAM-SCHMIDT ─────────────────────────────
            [
                'q' => 'Two vectors u and v are orthogonal when:',
                'opts' => [
                    ['They have the same length', false],
                    ['Their dot product uᵀv = 0', true],
                    ['u = −v', false],
                    ['They lie on the same line', false],
                ],
            ],
            [
                'q' => "Are vectors [1, 2, 3] and [1, −1, 1/3] orthogonal?\n(Hint: compute their dot product.)",
                'opts' => [
                    ['No, their dot product is 6', false],
                    ['Yes, their dot product is 0', true],
                    ['No, their dot product is 3', false],
                    ['No, their dot product is 2', false],
                ],
            ],
            [
                'q' => 'A matrix Q is called orthogonal (or unitary) when:',
                'opts' => [
                    ['Q = Qᵀ', false],
                    ['QᵀQ = I (its columns are orthonormal)', true],
                    ['Q has all positive entries', false],
                    ['Q is diagonal', false],
                ],
            ],
            [
                'q' => "The Gram-Schmidt process takes a set of linearly independent vectors and produces:",
                'opts' => [
                    ['A set of eigenvectors', false],
                    ['An orthonormal set of vectors spanning the same space', true],
                    ['The LU decomposition', false],
                    ['A diagonal matrix', false],
                ],
            ],
            [
                'q' => "In Gram-Schmidt, the projection of vector b onto vector a is:\n\nproj_a(b) = ?",
                'opts' => [
                    ['(aᵀb / aᵀa) × a', true],
                    ['(aᵀa / aᵀb) × b', false],
                    ['aᵀb × a', false],
                    ['a / ‖b‖', false],
                ],
            ],

            // ── FOUR FUNDAMENTAL SUBSPACES ────────────────────────────────
            [
                'q' => 'The four fundamental subspaces of an m×n matrix A are:',
                'opts' => [
                    ['Row space, Column space, Nullspace, Left Nullspace', true],
                    ['Eigenspace, Orthogonal complement, Kernel, Image', false],
                    ['Domain, Range, Nullspace, Diagonal space', false],
                    ['Row space, Inverse space, Identity space, Zero space', false],
                ],
            ],
            [
                'q' => 'The column space of A consists of:',
                'opts' => [
                    ['All solutions x to Ax = 0', false],
                    ['All possible outputs Ax for any input x', true],
                    ['All rows of A', false],
                    ['All eigenvalues of A', false],
                ],
            ],
            [
                'q' => "The rank-nullity theorem states: rank(A) + nullity(A) = ?",
                'opts' => [
                    ['The number of rows (m)', false],
                    ['The number of columns (n)', true],
                    ['The determinant of A', false],
                    ['The trace of A', false],
                ],
            ],
            [
                'q' => "For a 4×6 matrix A with rank 3, what is the nullity (dimension of the nullspace)?",
                'opts' => [
                    ['3', false],
                    ['4', false],
                    ['1', false],
                    ['3', true],
                ],
            ],
            [
                'q' => "For a 4×6 matrix A with rank 3, what is the nullity (dimension of the nullspace)?",
                'opts' => [
                    ['1', false],
                    ['4', false],
                    ['3', true],
                    ['6', false],
                ],
            ],

            // ── SINGULAR VALUE DECOMPOSITION (INTRO) ─────────────────────
            [
                'q' => 'The Singular Value Decomposition (SVD) of a matrix A is written as:',
                'opts' => [
                    ['A = PDP⁻¹', false],
                    ['A = LU', false],
                    ['A = UΣVᵀ', true],
                    ['A = QR', false],
                ],
            ],
            [
                'q' => 'In the SVD A = UΣVᵀ, what does the matrix Σ contain?',
                'opts' => [
                    ['Eigenvalues of A', false],
                    ['Non-negative singular values on its diagonal', true],
                    ['Eigenvectors of A', false],
                    ['The LU factors of A', false],
                ],
            ],
            [
                'q' => 'The singular values of A are the square roots of the eigenvalues of:',
                'opts' => [
                    ['A itself', false],
                    ['AᵀA (or AAᵀ)', true],
                    ['A + Aᵀ', false],
                    ['A⁻¹', false],
                ],
            ],
            [
                'q' => 'A key advantage of SVD over eigendecomposition is:',
                'opts' => [
                    ['SVD only works for square matrices', false],
                    ['SVD works for any m×n matrix, not just square ones', true],
                    ['SVD is always faster to compute', false],
                    ['SVD gives integer results', false],
                ],
            ],

            // ── POSITIVE DEFINITE MATRICES ────────────────────────────────
            [
                'q' => 'A symmetric matrix A is positive definite when:',
                'opts' => [
                    ['All entries are positive', false],
                    ['xᵀAx > 0 for all non-zero vectors x', true],
                    ['Its determinant is 0', false],
                    ['It has at least one positive eigenvalue', false],
                ],
            ],
            [
                'q' => 'A symmetric matrix is positive definite if and only if all its eigenvalues are:',
                'opts' => [
                    ['Zero', false],
                    ['Greater than zero (strictly positive)', true],
                    ['Less than zero', false],
                    ['Equal to 1', false],
                ],
            ],
            [
                'q' => 'Which of the following symmetric matrices is positive definite?',
                'opts' => [
                    ['[[2, 3], [3, 2]] (eigenvalues 5 and −1)', false],
                    ['[[3, 1], [1, 2]] (eigenvalues ≈ 3.73 and 1.27)', true],
                    ['[[1, 2], [2, 1]] (eigenvalues 3 and −1)', false],
                    ['[[0, 0], [0, 0]]', false],
                ],
            ],

            // ── DETERMINANTS (3×3) ────────────────────────────────────────
            [
                'q' => "Use cofactor expansion to find det([[1,2,3],[0,1,4],[5,6,0]]).\nWhat is the determinant?",
                'opts' => [
                    ['1', true],
                    ['−5', false],
                    ['12', false],
                    ['0', false],
                ],
            ],
            [
                'q' => 'The cofactor Cᵢⱼ of matrix A is defined as:',
                'opts' => [
                    ['The (i,j) entry of A', false],
                    ['(−1)^(i+j) × det of the submatrix obtained by removing row i and column j', true],
                    ['The determinant of A divided by aᵢⱼ', false],
                    ['The transpose of the minor Mᵢⱼ', false],
                ],
            ],
            [
                'q' => 'If every row of a square matrix sums to the same value k, then k is:',
                'opts' => [
                    ['An eigenvalue corresponding to eigenvector [1,1,...,1]', true],
                    ['The determinant of A', false],
                    ['The trace of A divided by n', false],
                    ['The rank of A', false],
                ],
            ],

            // ── MATRIX POWERS & POLYNOMIALS ──────────────────────────────
            [
                'q' => "For a diagonal matrix D = [[2,0],[0,3]], what is D⁴?",
                'opts' => [
                    ['[[8,0],[0,12]]', false],
                    ['[[16,0],[0,81]]', true],
                    ['[[4,0],[0,9]]', false],
                    ['[[2,0],[0,3]]', false],
                ],
            ],
            [
                'q' => 'The Cayley-Hamilton theorem states that every square matrix satisfies:',
                'opts' => [
                    ['Its own transpose equation', false],
                    ['Its own characteristic polynomial (p(A) = 0)', true],
                    ['Its own inverse equation', false],
                    ['The identity A² = I', false],
                ],
            ],

            // ── QR DECOMPOSITION ─────────────────────────────────────────
            [
                'q' => 'In the QR decomposition A = QR, what is Q?',
                'opts' => [
                    ['A lower triangular matrix', false],
                    ['A matrix with orthonormal columns', true],
                    ['A diagonal matrix', false],
                    ['The inverse of A', false],
                ],
            ],
            [
                'q' => 'QR decomposition is closely related to which process?',
                'opts' => [
                    ['LU decomposition', false],
                    ['Gaussian elimination', false],
                    ['The Gram-Schmidt orthogonalization process', true],
                    ['SVD', false],
                ],
            ],

            // ── PROJECTIONS ──────────────────────────────────────────────
            [
                'q' => 'The projection matrix P that projects onto the column space of A is:',
                'opts' => [
                    ['P = A(AᵀA)⁻¹Aᵀ', true],
                    ['P = AᵀA', false],
                    ['P = (AᵀA)⁻¹', false],
                    ['P = AAᵀ', false],
                ],
            ],
            [
                'q' => 'If b is already in the column space of A, what is its projection Pb?',
                'opts' => [
                    ['0', false],
                    ['b itself', true],
                    ['Aᵀb', false],
                    ['Ab', false],
                ],
            ],
            [
                'q' => 'The error vector (b − Pb) in a projection is always _____ to the column space of A.',
                'opts' => [
                    ['Parallel', false],
                    ['Equal', false],
                    ['Orthogonal (perpendicular)', true],
                    ['Anti-parallel', false],
                ],
            ],

            // ── NORM & CONDITION NUMBER ───────────────────────────────────
            [
                'q' => 'The condition number of a matrix A is defined as:',
                'opts' => [
                    ['det(A) / trace(A)', false],
                    ['σ_max / σ_min (largest singular value divided by smallest)', true],
                    ['‖A‖ + ‖A⁻¹‖', false],
                    ['rank(A) / n', false],
                ],
            ],
            [
                'q' => 'A matrix with a very large condition number is called:',
                'opts' => [
                    ['Orthogonal', false],
                    ['Ill-conditioned (small errors in b can cause large errors in x)', true],
                    ['Positive definite', false],
                    ['Full rank', false],
                ],
            ],
            [
                'q' => 'The condition number of an orthogonal matrix Q is always:',
                'opts' => [
                    ['0', false],
                    ['∞', false],
                    ['1', true],
                    ['n (the size of the matrix)', false],
                ],
            ],

            // ── MISC INTERMEDIATE ────────────────────────────────────────
            [
                'q' => 'The least squares solution to an overdetermined system Ax ≈ b (no exact solution) is:',
                'opts' => [
                    ['x = A⁻¹b', false],
                    ['x = (AᵀA)⁻¹Aᵀb', true],
                    ['x = Aᵀb', false],
                    ['x = Ab / ‖b‖', false],
                ],
            ],
            [
                'q' => 'The normal equations for least squares are:',
                'opts' => [
                    ['Ax = b', false],
                    ['Aᵀb = x', false],
                    ['AᵀAx = Aᵀb', true],
                    ['AAᵀx = b', false],
                ],
            ],
            [
                'q' => "In the Gram-Schmidt process, after computing orthogonal vectors q₁ and q₂, you normalize them by:",
                'opts' => [
                    ['Multiplying by their determinant', false],
                    ['Dividing each by its own Euclidean norm', true],
                    ['Adding the identity matrix', false],
                    ['Subtracting the mean of entries', false],
                ],
            ],
            [
                'q' => "Which statement about eigenvalues of symmetric matrices is ALWAYS true?",
                'opts' => [
                    ['All eigenvalues are complex', false],
                    ['All eigenvalues are real', true],
                    ['All eigenvalues are positive', false],
                    ['All eigenvalues are integers', false],
                ],
            ],
            [
                'q' => "Eigenvectors of a symmetric matrix corresponding to different eigenvalues are always:",
                'opts' => [
                    ['Parallel', false],
                    ['Equal in length', false],
                    ['Orthogonal', true],
                    ['Negative of each other', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 9 — Applied Matrix Analysis (Intermediate).");
    }
}