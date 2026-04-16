<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module9ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 9 — Applied Matrix Analysis (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Applied Matrix Analysis',
            'description'           => 'Challenge yourself with advanced matrix analysis — debugging numerical code, interpreting algorithmic outputs, analyzing ill-conditioned systems, power iteration, Cholesky decomposition, and spectral analysis. Code snippets included.',
            'time_limit_seconds'    => 2400,
            'base_xp'               => 2000,
            'order_index'           => 9,
        ]);

        $this->command->info("Seeding 50 advanced-level questions...");

        $qaData = [

            // ── DEBUGGING NUMPY / MATRIX CODE ────────────────────────────
            [
                'q' => "The following Python code is supposed to multiply two matrices but throws an error:\n\nimport numpy as np\nA = np.array([[1, 2], [3, 4]])\nB = np.array([[1, 0], [0, 1], [1, 1]])\nC = A @ B\n\nWhat is wrong?",
                'opts' => [
                    ['A and B must be square', false],
                    ['A has 2 columns but B has 3 rows — dimensions are incompatible', true],
                    ['The @ operator is not valid in Python', false],
                    ['numpy does not support matrix multiplication', false],
                ],
            ],
            [
                'q' => "What does this code compute?\n\nimport numpy as np\nA = np.array([[3, 1], [1, 3]])\nvals, vecs = np.linalg.eig(A)\nprint(vals)",
                'opts' => [
                    ['The singular values of A', false],
                    ['The diagonal entries of A', false],
                    ['The eigenvalues of A (should print [4. 2.])', true],
                    ['The trace and determinant of A', false],
                ],
            ],
            [
                'q' => "This code attempts to solve Ax = b, but gives a wrong answer:\n\nA = np.array([[2, 1], [4, 2]])\nb = np.array([3, 6])\nx = np.linalg.solve(A, b)\n\nWhat is the actual problem?",
                'opts' => [
                    ['np.linalg.solve requires integer arrays', false],
                    ['A is singular (det = 0), so the system may have no unique solution', true],
                    ['The b vector has the wrong shape', false],
                    ['np.linalg.solve only works for 3×3 matrices', false],
                ],
            ],
            [
                'q' => "What does np.linalg.matrix_rank([[1,2,3],[2,4,6],[0,0,1]]) return?\n\n(Notice that row 2 = 2 × row 1.)",
                'opts' => [
                    ['3', false],
                    ['1', false],
                    ['2', true],
                    ['0', false],
                ],
            ],
            [
                'q' => "What is printed by the following code?\n\nimport numpy as np\nA = np.eye(4)\nprint(np.linalg.det(A))",
                'opts' => [
                    ['0.0', false],
                    ['4.0', false],
                    ['1.0', true],
                    ['16.0', false],
                ],
            ],

            // ── POWER ITERATION ──────────────────────────────────────────
            [
                'q' => "The power iteration algorithm finds which eigenvalue of a matrix?",
                'opts' => [
                    ['The smallest eigenvalue', false],
                    ['The eigenvalue closest to 1', false],
                    ['The largest (dominant) eigenvalue in absolute value', true],
                    ['The average of all eigenvalues', false],
                ],
            ],
            [
                'q' => "In power iteration, you repeatedly compute:\n\nv_{k+1} = A v_k / ‖A v_k‖\n\nWhat does the sequence v_k converge to?",
                'opts' => [
                    ['The zero vector', false],
                    ['The eigenvector corresponding to the dominant eigenvalue', true],
                    ['The column space of A', false],
                    ['The inverse of A', false],
                ],
            ],
            [
                'q' => "Power iteration fails to converge when:",
                'opts' => [
                    ['The matrix is symmetric', false],
                    ['Two eigenvalues have the same absolute value (e.g., λ₁ = 5, λ₂ = −5)', true],
                    ['The initial vector is random', false],
                    ['The matrix is positive definite', false],
                ],
            ],
            [
                'q' => "The inverse power iteration (shift-and-invert) finds:",
                'opts' => [
                    ['The largest eigenvalue', false],
                    ['The eigenvalue closest to a chosen shift σ', true],
                    ['The determinant of A', false],
                    ['The condition number of A', false],
                ],
            ],

            // ── ILL-CONDITIONED SYSTEMS ───────────────────────────────────
            [
                'q' => "The Hilbert matrix H_n has entries Hᵢⱼ = 1/(i+j−1). For n=5, its condition number is approximately 4.8 × 10⁵. What does this imply when solving Hx = b numerically?",
                'opts' => [
                    ['The solution will always be exact in floating point', false],
                    ['A small rounding error in b can lead to large errors in x', true],
                    ['The matrix is singular and cannot be solved', false],
                    ['The eigenvalues of H are all negative', false],
                ],
            ],
            [
                'q' => "Which of the following will BEST mitigate numerical errors when solving ill-conditioned linear systems?",
                'opts' => [
                    ['Using Gaussian elimination without pivoting', false],
                    ['Using partial pivoting or iterative refinement', true],
                    ['Increasing the number of unknowns', false],
                    ['Multiplying both sides by a large scalar', false],
                ],
            ],
            [
                'q' => "For a matrix with condition number κ ≈ 10^8 and machine epsilon ε ≈ 10^{-16}, approximately how many decimal digits of accuracy can you expect in the solution x?",
                'opts' => [
                    ['16 digits (full precision)', false],
                    ['About 8 digits', true],
                    ['0 digits (no accuracy)', false],
                    ['16 + 8 = 24 digits', false],
                ],
            ],

            // ── CHOLESKY DECOMPOSITION ────────────────────────────────────
            [
                'q' => "Cholesky decomposition A = LLᵀ is applicable when A is:",
                'opts' => [
                    ['Any invertible matrix', false],
                    ['Symmetric positive definite', true],
                    ['Upper triangular', false],
                    ['Orthogonal', false],
                ],
            ],
            [
                'q' => "Compared to LU decomposition, Cholesky decomposition is approximately:",
                'opts' => [
                    ['Twice as slow', false],
                    ['The same speed', false],
                    ['Twice as fast (exploits symmetry, about n³/6 ops vs n³/3)', true],
                    ['Ten times faster for large matrices', false],
                ],
            ],
            [
                'q' => "Given A = [[4, 2], [2, 3]], find L in the Cholesky decomposition A = LLᵀ.\nWhat is L₁₁?",
                'opts' => [
                    ['4', false],
                    ['2', true],
                    ['1', false],
                    ['√2', false],
                ],
            ],
            [
                'q' => "In the Cholesky decomposition of A = [[4, 2], [2, 3]], what is L₂₁?",
                'opts' => [
                    ['2', false],
                    ['1', true],
                    ['0.5', false],
                    ['√3', false],
                ],
            ],

            // ── SVD APPLICATIONS ─────────────────────────────────────────
            [
                'q' => "In a rank-k approximation using SVD (A ≈ UₖΣₖVₖᵀ), what is kept?",
                'opts' => [
                    ['All singular values', false],
                    ['Only the k largest singular values and their corresponding vectors', true],
                    ['Only the zero singular values', false],
                    ['The k smallest singular values', false],
                ],
            ],
            [
                'q' => "The pseudoinverse A⁺ of a matrix computed via SVD is:\n\nA⁺ = ?",
                'opts' => [
                    ['UΣVᵀ', false],
                    ['VΣ⁺Uᵀ (where Σ⁺ replaces each non-zero σᵢ with 1/σᵢ)', true],
                    ['UᵀΣV', false],
                    ['VUᵀ / ‖A‖', false],
                ],
            ],
            [
                'q' => "What does a near-zero singular value in the SVD of A indicate?",
                'opts' => [
                    ['A is positive definite', false],
                    ['A is nearly rank-deficient (close to singular)', true],
                    ['A has large eigenvalues', false],
                    ['A is orthogonal', false],
                ],
            ],
            [
                'q' => "In image compression using SVD, retaining k singular values produces an image with:\n\n(m×n original) ← stored as?",
                'opts' => [
                    ['m×n entries as before', false],
                    ['k(m + n + 1) entries (approximate storage)', true],
                    ['k² entries', false],
                    ['(m/k) × (n/k) entries', false],
                ],
            ],

            // ── SPECTRAL THEOREM & SYMMETRIC MATRICES ────────────────────
            [
                'q' => "The Spectral Theorem states that every real symmetric matrix A can be written as:\n\nA = QΛQᵀ\n\nwhere Q is _____ and Λ is _____.",
                'opts' => [
                    ['Q is upper triangular, Λ is symmetric', false],
                    ['Q is orthogonal, Λ is diagonal (with eigenvalues)', true],
                    ['Q is lower triangular, Λ is the identity', false],
                    ['Q is the identity, Λ = A', false],
                ],
            ],
            [
                'q' => "For a real symmetric matrix, the eigenvectors corresponding to DISTINCT eigenvalues are always:",
                'opts' => [
                    ['Parallel', false],
                    ['Orthogonal', true],
                    ['Negatives of each other', false],
                    ['Unit vectors', false],
                ],
            ],

            // ── MATRIX NORMS ─────────────────────────────────────────────
            [
                'q' => "The Frobenius norm of a matrix A is defined as:\n\n‖A‖_F = ?",
                'opts' => [
                    ['The largest singular value of A', false],
                    ['The sum of all entries of A', false],
                    ['√(sum of squares of all entries of A)', true],
                    ['The trace of A', false],
                ],
            ],
            [
                'q' => "The spectral norm (or 2-norm) of a matrix A equals:",
                'opts' => [
                    ['The Frobenius norm', false],
                    ['The largest singular value σ_max of A', true],
                    ['The largest eigenvalue of A', false],
                    ['The determinant of A', false],
                ],
            ],
            [
                'q' => "For an orthogonal matrix Q, ‖Q‖_2 = ?",
                'opts' => [
                    ['0', false],
                    ['n (size of matrix)', false],
                    ['1', true],
                    ['√n', false],
                ],
            ],

            // ── ITERATIVE SOLVERS ─────────────────────────────────────────
            [
                'q' => "The Jacobi iterative method for solving Ax = b splits A into:\n\nA = D + (L + U)\n\nwhere D is _____, L is _____, U is _____.",
                'opts' => [
                    ['D is diagonal, L is strictly lower triangular, U is strictly upper triangular', true],
                    ['D is determinant, L is left matrix, U is unit matrix', false],
                    ['D is identity, L and U are eigenmatrices', false],
                    ['D is dense, L is lower triangular with 1s, U is upper triangular', false],
                ],
            ],
            [
                'q' => "The Conjugate Gradient (CG) method is best suited for solving:",
                'opts' => [
                    ['Any general m×n system', false],
                    ['Large sparse symmetric positive definite systems', true],
                    ['Non-square systems only', false],
                    ['Dense lower triangular systems', false],
                ],
            ],
            [
                'q' => "The Gauss-Seidel method converges faster than Jacobi because:",
                'opts' => [
                    ['It uses more iterations', false],
                    ['It uses updated values of x immediately within each iteration', true],
                    ['It solves the system in a single pass', false],
                    ['It does not require an initial guess', false],
                ],
            ],

            // ── MATRIX EXPONENTIAL ────────────────────────────────────────
            [
                'q' => "The matrix exponential eᴬ is defined via:\n\neᴬ = I + A + A²/2! + A³/3! + ...\n\nIf A is diagonalizable (A = PDP⁻¹), then eᴬ = ?",
                'opts' => [
                    ['P eᴰ P⁻¹, where eᴰ has e^λᵢ on its diagonal', true],
                    ['e times A', false],
                    ['P + D + P⁻¹', false],
                    ['The identity matrix', false],
                ],
            ],
            [
                'q' => "For the diagonal matrix D = [[2, 0], [0, 3]], what is e^D?",
                'opts' => [
                    ['[[2, 0], [0, 3]]', false],
                    ['[[e², 0], [0, e³]]', true],
                    ['[[e+2, 0], [0, e+3]]', false],
                    ['[[1, 0], [0, 1]]', false],
                ],
            ],

            // ── SPARSE MATRICES ───────────────────────────────────────────
            [
                'q' => "A sparse matrix is a matrix where:\n\n(context: n = 10,000, non-zeros = 50,000)",
                'opts' => [
                    ['All entries are 0', false],
                    ['Most entries are zero (very few non-zero entries relative to total size)', true],
                    ['The matrix is triangular', false],
                    ['The matrix is symmetric', false],
                ],
            ],
            [
                'q' => "Storing the 10,000×10,000 sparse matrix above (50,000 non-zeros) in CSR (Compressed Sparse Row) format requires approximately:",
                'opts' => [
                    ['10,000² = 100,000,000 values (dense storage)', false],
                    ['~150,000 values (3 arrays: values, column indices, row pointers)', true],
                    ['50,000² values', false],
                    ['Just 50,000 values (the non-zeros only)', false],
                ],
            ],

            // ── SCHUR DECOMPOSITION ───────────────────────────────────────
            [
                'q' => "Every square matrix A has a Schur decomposition A = QTQ*, where:",
                'opts' => [
                    ['Q is diagonal and T is orthogonal', false],
                    ['Q is unitary and T is upper triangular', true],
                    ['Q is lower triangular and T is diagonal', false],
                    ['Q is the identity and T = A', false],
                ],
            ],
            [
                'q' => "The Schur decomposition is more general than diagonalization because:",
                'opts' => [
                    ['It requires the matrix to be symmetric', false],
                    ['It exists for ALL square matrices (even non-diagonalizable ones)', true],
                    ['It is faster to compute', false],
                    ['It always produces a real decomposition', false],
                ],
            ],

            // ── NUMERICAL STABILITY ───────────────────────────────────────
            [
                'q' => "Which of the following is a numerically UNSTABLE way to compute the inverse of A?",
                'opts' => [
                    ['Using np.linalg.solve(A, b) to solve Ax = b', false],
                    ['Computing np.linalg.inv(A) and then multiplying by b', true],
                    ['Using LU decomposition with partial pivoting', false],
                    ['Using the QR decomposition', false],
                ],
            ],
            [
                'q' => "Why should you generally avoid computing A⁻¹ explicitly in numerical applications?",
                'opts' => [
                    ['Inverses only exist for symmetric matrices', false],
                    ['Computing A⁻¹ accumulates more floating-point errors and is slower than solving the system directly', true],
                    ['numpy does not support matrix inversion', false],
                    ['The inverse is always the zero matrix numerically', false],
                ],
            ],

            // ── DEFLATION & MULTIPLE EIGENVALUES ─────────────────────────
            [
                'q' => "After finding the dominant eigenpair (λ₁, v₁) via power iteration, matrix deflation replaces A with:\n\nA' = A − λ₁v₁v₁ᵀ\n\nWhat does this achieve?",
                'opts' => [
                    ['It removes the eigenvalue λ₁ so the next power iteration finds λ₂', true],
                    ['It makes A symmetric', false],
                    ['It computes the inverse of A', false],
                    ['It normalizes all eigenvalues to 1', false],
                ],
            ],
            [
                'q' => "A matrix with a repeated eigenvalue λ (algebraic multiplicity 2) but only one linearly independent eigenvector is called:",
                'opts' => [
                    ['Diagonalizable', false],
                    ['Defective', true],
                    ['Positive definite', false],
                    ['Orthogonal', false],
                ],
            ],

            // ── TRACE & DETERMINANT RELATIONSHIPS ────────────────────────
            [
                'q' => "For A = [[a, b], [c, d]], which of the following is the characteristic polynomial?",
                'opts' => [
                    ['λ² + (a+d)λ + (ad−bc)', false],
                    ['λ² − (a+d)λ + (ad−bc)', true],
                    ['λ² − (a−d)λ + (ad+bc)', false],
                    ['λ² + (ad−bc)', false],
                ],
            ],
            [
                'q' => "If A is a 3×3 matrix with eigenvalues 1, 2, 3, what is det(A)?",
                'opts' => [
                    ['6', true],
                    ['3', false],
                    ['1', false],
                    ['9', false],
                ],
            ],
            [
                'q' => "If A is a 3×3 matrix with eigenvalues 1, 2, 3, what is det(A²)?",
                'opts' => [
                    ['6', false],
                    ['12', false],
                    ['36', true],
                    ['9', false],
                ],
            ],

            // ── LEAST SQUARES WITH CODE ───────────────────────────────────
            [
                'q' => "The following code fits a least-squares line to data:\n\nimport numpy as np\nA = np.column_stack([np.ones(5), [1,2,3,4,5]])\nb = np.array([2.1, 3.9, 6.2, 7.8, 10.1])\nx, _, _, _ = np.linalg.lstsq(A, b, rcond=None)\nprint(x)\n\nWhat does x[1] represent?",
                'opts' => [
                    ['The y-intercept of the fitted line', false],
                    ['The slope of the fitted line', true],
                    ['The residual error', false],
                    ['The rank of A', false],
                ],
            ],
            [
                'q' => "In np.linalg.lstsq(A, b), the residuals (third return value) represent:",
                'opts' => [
                    ['The eigenvalues of A', false],
                    ['‖b − Ax‖² — the sum of squared fitting errors', true],
                    ['The singular values of A', false],
                    ['The rank of A minus the nullity', false],
                ],
            ],

            // ── ADVANCED MISC ─────────────────────────────────────────────
            [
                'q' => "A matrix A is called nilpotent when:",
                'opts' => [
                    ['All eigenvalues are 1', false],
                    ['Aᵏ = 0 for some positive integer k', true],
                    ['A = Aᵀ', false],
                    ['det(A) = 1', false],
                ],
            ],
            [
                'q' => "A matrix A is idempotent when:",
                'opts' => [
                    ['A² = I', false],
                    ['A = Aᵀ', false],
                    ['A² = A', true],
                    ['Aᵀ = A⁻¹', false],
                ],
            ],
            [
                'q' => "The Jordan normal form of a defective matrix contains:",
                'opts' => [
                    ['Only diagonal blocks', false],
                    ['Jordan blocks with eigenvalues on the diagonal and 1s on the superdiagonal', true],
                    ['Only identity blocks', false],
                    ['All zeros above the diagonal', false],
                ],
            ],
            [
                'q' => "Given the following snippet:\n\nA = np.array([[1, 1], [0, 1]])\nprint(np.linalg.matrix_power(A, 3))\n\nWhat is A³ for this Jordan block?",
                'opts' => [
                    ['[[1, 1], [0, 1]]', false],
                    ['[[1, 3], [0, 1]]', true],
                    ['[[3, 3], [0, 3]]', false],
                    ['[[1, 0], [0, 1]]', false],
                ],
            ],
            [
                'q' => "The Perron-Frobenius theorem guarantees that a matrix with all positive entries has:",
                'opts' => [
                    ['All eigenvalues equal to 1', false],
                    ['A unique largest real eigenvalue (Perron root) with a positive eigenvector', true],
                    ['A determinant of 1', false],
                    ['All singular values equal', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 9 — Applied Matrix Analysis (Advanced).");
    }
}