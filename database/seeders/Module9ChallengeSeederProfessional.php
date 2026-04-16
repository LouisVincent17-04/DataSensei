<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module9ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 9 — Applied Matrix Analysis (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Applied Matrix Analysis',
            'description'           => 'Real-world professional-grade matrix analysis — performance optimization, numerical edge cases, large-scale system design, randomized algorithms, Krylov subspace methods, structured matrices, and production-level numerical pitfalls. Designed for practitioners.',
            'time_limit_seconds'    => 3600,
            'base_xp'               => 3000,
            'order_index'           => 9,
        ]);

        $this->command->info("Seeding 50 professional-level questions...");

        $qaData = [

            // ── PRODUCTION NUMERICAL PITFALLS ────────────────────────────
            [
                'q' => "A production system computes a covariance matrix S from n=50 samples with p=500 features. The team calls np.linalg.inv(S) and gets garbage results. The most likely root cause is:\n\n(n=50 samples, p=500 features)",
                'opts' => [
                    ['numpy has a bug in matrix inversion for large matrices', false],
                    ['S is singular or near-singular because n < p (underdetermined), making the inversion numerically meaningless', true],
                    ['Covariance matrices cannot be inverted', false],
                    ['np.linalg.inv requires integer input arrays', false],
                ],
            ],
            [
                'q' => "You are solving a 10,000×10,000 dense linear system Ax = b. Which approach is most appropriate for production?",
                'opts' => [
                    ['Compute A⁻¹ explicitly, then multiply: x = A⁻¹b', false],
                    ['Use np.linalg.solve(A, b) which uses LAPACK\'s LU factorization with partial pivoting', true],
                    ['Use Cramer\'s Rule for guaranteed precision', false],
                    ['Enumerate all possible x vectors until Ax ≈ b', false],
                ],
            ],
            [
                'q' => "A real-time control system must solve a 500×500 symmetric positive definite system 1,000 times per second with DIFFERENT right-hand sides b but the SAME matrix A. What is the optimal production strategy?",
                'opts' => [
                    ['Call np.linalg.solve(A, b) fresh every time', false],
                    ['Pre-compute the Cholesky factor L once (A = LLᵀ), then solve Ly = b and Lᵀx = y each time', true],
                    ['Pre-compute A⁻¹ once and multiply by b each time', false],
                    ['Use the QR decomposition for each call', false],
                ],
            ],
            [
                'q' => "A floating-point computation returns a covariance matrix with a slightly negative eigenvalue (e.g., −1e-12). This is most likely caused by:\n\n(True covariance matrices are always positive semi-definite.)",
                'opts' => [
                    ['A bug in the eigenvalue solver', false],
                    ['Floating-point rounding errors breaking the theoretically guaranteed positive semi-definiteness', true],
                    ['The dataset contains outliers', false],
                    ['The matrix was transposed incorrectly', false],
                ],
            ],
            [
                'q' => "The production fix for the near-negative eigenvalue problem above (maintaining positive definiteness numerically) is:",
                'opts' => [
                    ['Delete the negative eigenvalue entirely', false],
                    ['Add a small regularization term εI (e.g., S + 1e-8 * I) before inversion', true],
                    ['Replace all negative entries of S with their absolute values', false],
                    ['Use a higher-precision float128 array', false],
                ],
            ],

            // ── LARGE-SCALE & PERFORMANCE ─────────────────────────────────
            [
                'q' => "You need to compute the top-10 singular values of a 100,000×50,000 matrix A. Which approach is appropriate for production?",
                'opts' => [
                    ['Compute the full SVD using np.linalg.svd and discard the rest', false],
                    ['Use a truncated/randomized SVD (e.g., scipy.sparse.linalg.svds or sklearn.utils.extmath.randomized_svd)', true],
                    ['Compute AᵀA (a 50,000×50,000 matrix) and find its eigenvalues', false],
                    ['Use Cramer\'s Rule on each column', false],
                ],
            ],
            [
                'q' => "A randomized SVD algorithm (e.g., Halko-Martinsson-Tropp) computes a rank-k approximation in O(mn log k) instead of O(mn min(m,n)). It achieves this speedup by:\n\n(A is m×n)",
                'opts' => [
                    ['Skipping the computation of U and Vᵀ entirely', false],
                    ['First projecting A onto a random low-dimensional subspace, then computing the exact SVD of the small projected matrix', true],
                    ['Approximating the matrix with a diagonal one', false],
                    ['Sampling random rows and columns of A', false],
                ],
            ],
            [
                'q' => "For a sparse matrix A with n=10⁶ and nnz=5×10⁶ non-zeros, the memory savings of CSR format over dense storage are roughly:\n\n(assume float64 = 8 bytes each)",
                'opts' => [
                    ['8 × 10⁶ × 10⁶ bytes ÷ (3 × 5 × 10⁶ × 8 bytes) ≈ factor of ~1.3×10⁵', true],
                    ['Only about 2×', false],
                    ['No savings — CSR uses the same memory as dense', false],
                    ['CSR uses more memory due to index arrays', false],
                ],
            ],
            [
                'q' => "Matrix-vector products for large sparse matrices should be implemented with:",
                'opts' => [
                    ['Dense numpy operations (A @ x) on the full n×n matrix', false],
                    ['scipy.sparse matrix-vector multiply which iterates only over non-zero entries', true],
                    ['A for-loop over every (i,j) entry', false],
                    ['The Cholesky factorization', false],
                ],
            ],

            // ── KRYLOV SUBSPACE METHODS ───────────────────────────────────
            [
                'q' => "The GMRES algorithm solves Ax = b by finding the best solution in the Krylov subspace Kₖ(A, r₀). Its main memory cost is:\n\n(r₀ = initial residual)",
                'opts' => [
                    ['O(1) — it uses fixed memory', false],
                    ['O(k²) — it stores k orthogonal basis vectors of size n each', false],
                    ['O(kn) — grows with iterations k, requiring restarts for large k', true],
                    ['O(n²) — same as dense LU', false],
                ],
            ],
            [
                'q' => "The Conjugate Gradient (CG) method converges in at most n iterations for an n×n SPD matrix, but in practice converges much faster when:",
                'opts' => [
                    ['The matrix has many zero entries', false],
                    ['The eigenvalues are clustered (low effective condition number)', true],
                    ['The right-hand side b is small', false],
                    ['The matrix is diagonal', false],
                ],
            ],
            [
                'q' => "Preconditioning transforms Ax = b into M⁻¹Ax = M⁻¹b. The best preconditioner M is one where:",
                'opts' => [
                    ['M = 0', false],
                    ['M = A (perfect preconditioner, but defeats the purpose)', false],
                    ['M ≈ A so that M⁻¹A ≈ I (reduces condition number) AND M⁻¹y is cheap to compute', true],
                    ['M is the zero matrix', false],
                ],
            ],
            [
                'q' => "The Lanczos algorithm is the symmetric analogue of Arnoldi iteration. It reduces a symmetric matrix to:\n\n(n×n SPD matrix A → ?)",
                'opts' => [
                    ['A diagonal matrix', false],
                    ['A tridiagonal matrix via orthogonal similarity transformations', true],
                    ['A lower triangular matrix', false],
                    ['An upper Hessenberg matrix', false],
                ],
            ],

            // ── STRUCTURED MATRICES ───────────────────────────────────────
            [
                'q' => "A Toeplitz matrix (each diagonal has a constant value) of size n×n can be multiplied by a vector in O(n log n) instead of O(n²) using:\n\n(What technique enables this?)",
                'opts' => [
                    ['LU decomposition', false],
                    ['The Fast Fourier Transform (FFT), exploiting circular convolution', true],
                    ['The Gram-Schmidt process', false],
                    ['Power iteration', false],
                ],
            ],
            [
                'q' => "A circulant matrix is diagonalized by the DFT matrix F:\n\nC = F* Λ F\n\nThis means the eigenvalues of C are:",
                'opts' => [
                    ['The diagonal entries of C', false],
                    ['The DFT of the first row of C', true],
                    ['The singular values of C', false],
                    ['Always equal to 1', false],
                ],
            ],
            [
                'q' => "For a Kronecker product A ⊗ B (where A is m×m and B is n×n), the eigenvalues are:\n\n(λᵢ are eigenvalues of A, μⱼ are eigenvalues of B)",
                'opts' => [
                    ['λᵢ + μⱼ for all i, j', false],
                    ['λᵢ × μⱼ for all i, j (mn eigenvalues total)', true],
                    ['λᵢ / μⱼ for all i, j', false],
                    ['Only the eigenvalues of A', false],
                ],
            ],

            // ── MATRIX CALCULUS & OPTIMIZATION ───────────────────────────
            [
                'q' => "In neural network training, backpropagation through a linear layer y = Wx computes the gradient of loss L with respect to W as:\n\n∂L/∂W = ?",
                'opts' => [
                    ['∂L/∂y × x', false],
                    ['(∂L/∂y)ᵀ × x (outer product of upstream gradient and input)', false],
                    ['∂L/∂y × xᵀ (this is the outer product — assuming ∂L/∂y is a column vector)', true],
                    ['W⁻¹ × ∂L/∂y', false],
                ],
            ],
            [
                'q' => "The gradient of ‖Ax − b‖² with respect to x is:\n\n(used in least squares and linear regression)",
                'opts' => [
                    ['2Ax', false],
                    ['2Aᵀ(Ax − b)', true],
                    ['2A(Ax − b)', false],
                    ['Ax − b', false],
                ],
            ],
            [
                'q' => "The Hessian matrix of a scalar function f(x) is the matrix of second partial derivatives ∂²f/∂xᵢ∂xⱼ. For a quadratic f(x) = xᵀAx (A symmetric), the Hessian is:",
                'opts' => [
                    ['A', false],
                    ['2A', true],
                    ['Aᵀ + A', false],
                    ['xᵀAx × I', false],
                ],
            ],
            [
                'q' => "In Newton's method for optimizing f(x), the update step is x ← x − H⁻¹∇f. What must be true about H for the method to converge to a minimum (not a saddle point)?",
                'opts' => [
                    ['H must be singular', false],
                    ['H must be positive definite (all eigenvalues > 0)', true],
                    ['H must be diagonal', false],
                    ['H must be orthogonal', false],
                ],
            ],

            // ── PRINCIPAL COMPONENT ANALYSIS ─────────────────────────────
            [
                'q' => "In PCA, you center data matrix X (n samples × p features) and compute the covariance matrix S = XᵀX / (n−1). The principal components are:\n\n(What are they mathematically?)",
                'opts' => [
                    ['The singular values of X', false],
                    ['The eigenvectors of S (columns of V in the SVD of X)', true],
                    ['The rows of X sorted by norm', false],
                    ['The diagonal entries of S', false],
                ],
            ],
            [
                'q' => "The proportion of variance explained by the k-th principal component is:\n\n(σᵢ are singular values of the centered data matrix)",
                'opts' => [
                    ['σₖ / ∑σᵢ', false],
                    ['σₖ² / ∑σᵢ²', true],
                    ['σₖ / max(σᵢ)', false],
                    ['1 / rank(S)', false],
                ],
            ],
            [
                'q' => "For large-scale PCA (e.g., 10⁶ × 10⁴ data matrix), computing S = XᵀX explicitly has the problem that:",
                'opts' => [
                    ['XᵀX is not symmetric', false],
                    ['S is a 10⁴ × 10⁴ matrix, which is manageable; the bigger problem is the O(10⁶ × 10⁴ × 10⁴) matrix multiply to form it', true],
                    ['PCA is undefined for rectangular matrices', false],
                    ['XᵀX always has rank 1', false],
                ],
            ],

            // ── MATRIX DECOMPOSITIONS IN PRACTICE ────────────────────────
            [
                'q' => "Given a tall-skinny matrix A (m >> n, e.g., 10⁶ × 100), the QR decomposition approach to solving the least squares problem ‖Ax − b‖ is preferred over the normal equations (AᵀA)x = Aᵀb because:",
                'opts' => [
                    ['QR is always faster for any matrix shape', false],
                    ['The normal equations square the condition number (κ(AᵀA) = κ(A)²), losing precision', true],
                    ['The normal equations do not have a unique solution', false],
                    ['QR can handle complex numbers while the normal equations cannot', false],
                ],
            ],
            [
                'q' => "The thin (economy) QR decomposition of an m×n matrix (m > n) returns Q of size _____ and R of size _____.",
                'opts' => [
                    ['m×m and m×n', false],
                    ['m×n and n×n', true],
                    ['n×n and m×n', false],
                    ['m×n and m×m', false],
                ],
            ],
            [
                'q' => "The incomplete LU (ILU) factorization is commonly used as a preconditioner for iterative methods. It differs from exact LU by:",
                'opts' => [
                    ['Stopping after half the elimination steps', false],
                    ['Dropping fill-in entries that fall below a threshold (keeping sparsity)', true],
                    ['Computing U first instead of L', false],
                    ['Using floating-point rounding on every step', false],
                ],
            ],

            // ── GENERALIZED EIGENPROBLEMS ─────────────────────────────────
            [
                'q' => "The generalized eigenvalue problem Av = λBv (A, B symmetric, B positive definite) arises in:\n\nWhich real-world application?",
                'opts' => [
                    ['Random matrix generation', false],
                    ['Structural mechanics (computing vibration modes: stiffness A, mass B)', true],
                    ['Image binarization', false],
                    ['Hash table collision resolution', false],
                ],
            ],
            [
                'q' => "To solve Av = λBv with B positive definite, the standard reduction approach is:\n\n(What transformation converts it to a standard eigenproblem?)",
                'opts' => [
                    ['Compute A − B and find standard eigenvalues', false],
                    ['Compute L (Cholesky of B), then solve (L⁻¹AL⁻ᵀ)u = λu where v = L⁻ᵀu', true],
                    ['Divide every entry of A by every entry of B element-wise', false],
                    ['Use power iteration on A/B', false],
                ],
            ],

            // ── MATRIX FUNCTIONS ─────────────────────────────────────────
            [
                'q' => "The matrix square root A^(1/2) of a symmetric positive definite matrix satisfies:\n\nA^(1/2) × A^(1/2) = A\n\nUsing eigendecomposition A = QΛQᵀ, A^(1/2) = ?",
                'opts' => [
                    ['Q√ΛQᵀ (where √Λ has √λᵢ on its diagonal)', true],
                    ['√(trace(A)) × I', false],
                    ['Q Λ Qᵀ / 2', false],
                    ['A / 2', false],
                ],
            ],
            [
                'q' => "Computing the matrix logarithm log(A) of a positive definite matrix A = QΛQᵀ gives:\n\nlog(A) = ?",
                'opts' => [
                    ['Q log(Λ) Qᵀ (where log(Λ) has log(λᵢ) on its diagonal)', true],
                    ['log(det(A)) × I', false],
                    ['log(trace(A)) × I', false],
                    ['The element-wise log of all entries', false],
                ],
            ],

            // ── RANDOMIZED ALGORITHMS ─────────────────────────────────────
            [
                'q' => "The Johnson-Lindenstrauss lemma guarantees that projecting n points from ℝᵈ into ℝᵏ (using a random matrix) approximately preserves pairwise distances when:\n\nk = O(?)",
                'opts' => [
                    ['k = O(d)', false],
                    ['k = O(n²)', false],
                    ['k = O(log n / ε²)', true],
                    ['k = O(1)', false],
                ],
            ],
            [
                'q' => "Randomized trace estimation (Hutchinson estimator) estimates trace(A) using:\n\ntrace(A) ≈ (1/s) ∑ᵢ zᵢᵀ A zᵢ\n\nwhere zᵢ are random vectors. What property must the random vectors have?",
                'opts' => [
                    ['They must be eigenvectors of A', false],
                    ['Each entry must be ±1 with equal probability (or from a zero-mean isotropic distribution)', true],
                    ['They must be orthonormal', false],
                    ['They must sum to zero', false],
                ],
            ],

            // ── TENSOR DECOMPOSITIONS ─────────────────────────────────────
            [
                'q' => "The CP (CANDECOMP/PARAFAC) decomposition expresses a 3-way tensor 𝒯 as a sum of rank-1 terms. This generalizes which matrix decomposition?",
                'opts' => [
                    ['LU decomposition', false],
                    ['Outer product (rank-1) decomposition of matrices (SVD)', true],
                    ['Cholesky decomposition', false],
                    ['QR decomposition', false],
                ],
            ],
            [
                'q' => "Unlike matrix SVD, computing the best rank-k approximation of a tensor (k > 1) is known to be:",
                'opts' => [
                    ['Easily solved via alternating least squares with guaranteed convergence to the global optimum', false],
                    ['NP-hard in general; alternating methods like ALS converge to local optima only', true],
                    ['Equivalent to the matrix SVD of the matricized tensor', false],
                    ['Always unique and stable', false],
                ],
            ],

            // ── MATRIX DIFFERENTIAL EQUATIONS ────────────────────────────
            [
                'q' => "The solution to the matrix ODE dX/dt = AX, X(0) = X₀ is:\n\nX(t) = ?",
                'opts' => [
                    ['X(t) = X₀ + tA', false],
                    ['X(t) = e^(At) X₀', true],
                    ['X(t) = A^t X₀', false],
                    ['X(t) = X₀ e^t', false],
                ],
            ],
            [
                'q' => "For a stable linear system ẋ = Ax, stability requires:",
                'opts' => [
                    ['All eigenvalues of A have positive real parts', false],
                    ['All eigenvalues of A have strictly negative real parts', true],
                    ['A is positive definite', false],
                    ['det(A) > 0', false],
                ],
            ],
            [
                'q' => "The Lyapunov equation AX + XAᵀ = −Q (Q positive definite) has a unique positive definite solution X if and only if:\n\n(condition on A?)",
                'opts' => [
                    ['A is symmetric', false],
                    ['A is stable (all eigenvalues have negative real parts)', true],
                    ['A is orthogonal', false],
                    ['det(A) = 1', false],
                ],
            ],

            // ── GRAPH LAPLACIANS ──────────────────────────────────────────
            [
                'q' => "The graph Laplacian L = D − W (D degree matrix, W adjacency matrix) is always:\n\n(What algebraic property does it have?)",
                'opts' => [
                    ['Negative definite', false],
                    ['Positive semi-definite and symmetric', true],
                    ['Orthogonal', false],
                    ['Invertible', false],
                ],
            ],
            [
                'q' => "In spectral clustering, you use the eigenvectors of the graph Laplacian corresponding to the k SMALLEST eigenvalues. The very smallest eigenvalue is always 0, with eigenvector:\n\n(What is the eigenvector for λ=0 of the Laplacian of a connected graph?)",
                'opts' => [
                    ['The zero vector', false],
                    ['The constant vector [1, 1, ..., 1]ᵀ', true],
                    ['A random unit vector', false],
                    ['The degree vector', false],
                ],
            ],
            [
                'q' => "The number of zero eigenvalues of the graph Laplacian equals:",
                'opts' => [
                    ['The number of edges', false],
                    ['The number of connected components in the graph', true],
                    ['The number of vertices', false],
                    ['The rank of the adjacency matrix', false],
                ],
            ],

            // ── EDGE CASES & ROBUSTNESS ───────────────────────────────────
            [
                'q' => "A production pipeline receives a user-supplied matrix A and attempts np.linalg.solve(A, b). Which edge cases MUST be handled before calling solve?",
                'opts' => [
                    ['Only check that A is square', false],
                    ['Check that A is square, non-singular (det ≠ 0), and that b has the correct shape — and handle LinAlgError gracefully', true],
                    ['Only check that b is non-zero', false],
                    ['No checks are needed; numpy handles all edge cases internally', false],
                ],
            ],
            [
                'q' => "After computing L in a Cholesky factorization via scipy.linalg.cholesky(A), the function raises a LinAlgError. The most likely cause is:\n\n(What does scipy check?)",
                'opts' => [
                    ['A is not square', false],
                    ['A is not symmetric positive definite (Cholesky requires SPD matrices)', true],
                    ['A has more rows than columns', false],
                    ['A contains NaN values only if they are on the diagonal', false],
                ],
            ],
            [
                'q' => "When should you prefer scipy.linalg over numpy.linalg in a production numerical application?",
                'opts' => [
                    ['Always use numpy.linalg; scipy.linalg is deprecated', false],
                    ['scipy.linalg provides more specialized routines (e.g., Cholesky with lower=True, LAPACK drivers) and is generally more feature-complete for numerical linear algebra', true],
                    ['scipy.linalg only works with sparse matrices', false],
                    ['There is no difference between the two', false],
                ],
            ],
            [
                'q' => "In a recommendation system using matrix factorization (A ≈ UV ᵀ, A is user-item matrix with many missing entries), the correct loss function sums only over:\n\n(How do you handle missing entries?)",
                'opts' => [
                    ['All mn entries, treating missing entries as 0', false],
                    ['Only the observed (non-missing) entries in the matrix', true],
                    ['A random 10% sample of all entries', false],
                    ['The diagonal entries only', false],
                ],
            ],
            [
                'q' => "The alternating least squares (ALS) algorithm for matrix factorization fixes U, solves for V in closed form, then fixes V and solves for U. Each closed-form solve is a:\n\n(What type of subproblem?)",
                'opts' => [
                    ['Non-convex optimization problem', false],
                    ['Least squares problem with a normal equations solution', true],
                    ['Eigenvalue problem', false],
                    ['Integer programming problem', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 9 — Applied Matrix Analysis (Professional).");
    }
}