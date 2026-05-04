<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 9 — Linear Algebra (Professional / Level 5) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Professional tier
 *   2. coding_questions    — 50 questions covering advanced Linear Algebra
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mapped to lessons 360–369):
 *   L9.1  Vectors, Scalars & the Geometry of Linear Algebra
 *   L9.2  Matrix Fundamentals: Operations & Special Structures
 *   L9.3  Systems of Linear Equations & Gaussian Elimination
 *   L9.4  Matrix Invertibility, Rank & the Four Fundamental Subspaces
 *   L9.5  Determinants: Theory, Computation & Geometric Meaning
 *   L9.6  Eigenvalues & Eigenvectors
 *   L9.7  Diagonalization & Matrix Powers
 *   L9.8  Orthogonality, Projections & Gram-Schmidt
 *   L9.9  Singular Value Decomposition (SVD)
 *   L9.10 Positive Definite Matrices & Quadratic Forms
 *
 * Difficulty: Professional — problems require implementing non-trivial numerical
 * algorithms from scratch in pure Python: LU/QR/Cholesky/LDL^T factorizations,
 * power iteration, inverse iteration, full SVD pipeline, Thomas algorithm,
 * Gauss-Seidel iteration, fast matrix exponentiation, Gershgorin analysis,
 * deflation, pseudoinverse computation, and multi-concept synthesis.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module9CodingChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (! $category) {
            $this->command->error('Professional category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 9 — Linear Algebra (Professional) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Linear Algebra',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Master the full depth of Linear Algebra through rigorous algorithmic implementation in pure Python. Design and build numerical methods from scratch: LU decomposition, QR factorisation via Gram-Schmidt, full SVD via eigendecomposition of A^TA, Cholesky and LDL^T factorisations, the Thomas tridiagonal algorithm, Gauss-Seidel iterative refinement, fast matrix exponentiation by repeated squaring, power iteration and inverse power iteration for eigenvalues, Gershgorin circle analysis, deflation, Kronecker products, commutator algebra, RREF with general solution extraction, Moore-Penrose pseudoinverse, Givens rotations, polynomial least-squares fitting, and comprehensive multi-step matrix analysis. Problems demand mastery of numerical stability, rank-deficiency handling, general n×n arithmetic, and the ability to synthesise concepts from across the entire linear algebra curriculum.',
                'time_limit_seconds' => 3600,
                'base_xp'            => 5000,
                'order_index'        => 5,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 professional coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Vectors, Scalars & the Geometry of Linear Algebra (Q1–Q5) → L360
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Implement **Modified Gram-Schmidt** orthonormalization on `k` vectors in `R^n`.

Read `n` and `k`, then `k` vectors (each `n` space-separated floats). Apply Modified Gram-Schmidt to produce an orthonormal set. Print each resulting orthonormal vector rounded to **4 decimal places**, space-separated, one per line.

If a vector becomes a zero vector during the process (linearly dependent), **skip it** and continue. Only print vectors that survived.

Example:
```
Input:
2 2
3 0
1 1
Output:
1.0000 0.0000
0.0000 1.0000
```
MD,
                'starter_code'        => "import math\nn, k = map(int, input().split())\nvecs = [list(map(float, input().split())) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 200,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Implement the **Householder reflection**. Given a vector `a` of length `n`, compute the Householder matrix `H` such that `H * a = ‖a‖ * e₁` (where `e₁` is the first standard basis vector). Then apply `H` to a given vector `b`.

Read `n`, then vector `a` (n floats), then vector `b` (n floats). Print the vector `H * b` rounded to **4 decimal places**, space-separated.

Construction: let `u = a - ‖a‖ * e₁`, then `H = I - 2 * u * uᵀ / (uᵀ * u)`.

Special case: if `a = ‖a‖ * e₁` already (i.e., `u` is the zero vector), then `H = I` and `H * b = b`.

Example:
```
Input:
2
3 4
1 0
Output:
0.6000 0.8000
```
MD,
                'starter_code'        => "import math\nn = int(input())\na = list(map(float, input().split()))\nb = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 250,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Compute the **orthogonal projection** of a vector `b` onto the column space of a matrix `A`, and report the projection and the residual norm.

Read `m n` (rows, cols of A), then `m` rows of `A` (floats), then vector `b` (m floats).

Compute: `proj = A * (AᵀA)⁻¹ * Aᵀ * b` (assumes A has full column rank).

Print:
- Line 1: the projection vector (m values, **4 decimal places**, space-separated).
- Line 2: `‖b − proj‖₂` rounded to **4 decimal places**.

Example:
```
Input:
3 1
1
1
1
1 2 3
Output:
2.0000 2.0000 2.0000
1.4142
```
MD,
                'starter_code'        => "import math\nm, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\nb = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 250,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Perform an **orthogonal decomposition** of vector `b` with respect to a subspace `W` spanned by `k` given vectors in `R^n` (not necessarily orthonormal).

Read `n k`, then `k` vectors (the basis of W), then vector `b`.

Decompose: `b = b_W + b_perp`, where `b_W` is the projection of `b` onto `W`, and `b_perp = b − b_W` is orthogonal to `W`.

First orthonormalize the basis using Gram-Schmidt, then project. Print `b_W` on line 1 and `b_perp` on line 2 (each n values, **4 decimal places**, space-separated).

Example:
```
Input:
2 1
1 0
3 4
Output:
3.0000 0.0000
0.0000 4.0000
```
MD,
                'starter_code'        => "import math\nn, k = map(int, input().split())\nW = [list(map(float, input().split())) for _ in range(k)]\nb = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 250,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Implement one step of **power iteration** on a symmetric matrix, reporting the Rayleigh quotient before and after.

Read `n`, then the `n × n` matrix `A` (floats), then initial vector `x` (n floats).

Compute:
1. `R₀ = xᵀAx / xᵀx` — the initial Rayleigh quotient.
2. `y = A * x` — one matrix-vector product.
3. `‖y‖₂` — the norm of `y`.
4. `x_new = y / ‖y‖₂` — normalized new vector.
5. `R₁ = x_newᵀ * A * x_new` — the new Rayleigh quotient.

Print each on its own line, rounded to **4 decimal places**: `R₀`, `‖y‖₂`, `x_new` (space-separated), then `R₁`.

Example:
```
Input:
2
4 1
1 3
1 1
Output:
4.5000
6.4031
0.7809 0.6247
4.5854
```
MD,
                'starter_code'        => "import math\nn = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\nx = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Matrix Fundamentals: Operations & Special Structures (Q6–Q10) → L361
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Compute the **LU decomposition** `A = L * U` (without pivoting) for a given `n × n` matrix, where `L` is unit lower-triangular and `U` is upper-triangular.

Read `n`, then `n` rows of matrix `A` (floats). Print `L` (n rows) then a blank line then `U` (n rows). All values rounded to **4 decimal places**.

You may assume the matrix is invertible and no zero pivot is encountered.

Example:
```
Input:
3
2 1 1
4 3 3
8 7 9
Output:
1.0000 0.0000 0.0000
2.0000 1.0000 0.0000
4.0000 3.0000 1.0000

2.0000 1.0000 1.0000
0.0000 1.0000 1.0000
0.0000 0.0000 2.0000
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 250,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Compute **A^k** for a given `n × n` integer matrix `A` and integer `k ≥ 0` using **repeated squaring** (fast matrix exponentiation).

Read `n`, then `n` rows of `A` (integers), then `k`. Print `A^k` row by row, space-separated (integer values — no floating point).

Complexity must be O(n³ log k). Direct repeated multiplication for large k will time out.

Example:
```
Input:
2
1 1
1 0
10
Output:
89 55
55 34
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(int, input().split())) for _ in range(n)]\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 250,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Compute the **Kronecker product** (tensor product) `A ⊗ B` of two integer matrices.

Read `m1 n1` (dimensions of A), then `m1` rows of `A`; then `m2 n2` (dimensions of B), then `m2` rows of `B`. Print the resulting `(m1·m2) × (n1·n2)` matrix row by row, space-separated (integers).

Recall: `(A ⊗ B)[i·m2+p, j·n2+q] = A[i,j] * B[p,q]`.

Example:
```
Input:
2 2
1 2
3 4
2 2
0 5
6 7
Output:
0 5 0 10
6 7 12 14
0 15 0 20
18 21 24 28
```
MD,
                'starter_code'        => "m1, n1 = map(int, input().split())\nA = [list(map(int, input().split())) for _ in range(m1)]\nm2, n2 = map(int, input().split())\nB = [list(map(int, input().split())) for _ in range(m2)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 200,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Compute the **matrix commutator** `[A, B] = AB − BA` for two `n × n` integer matrices. Then classify the result:
- Print `commute` if `[A, B]` is the zero matrix.
- Print `nilpotent` if `[A, B] ≠ 0` but `[A, B]^2 = 0` (for 2×2: trace = 0 and det = 0).
- Print `non-zero` otherwise.

Read `n`, then `n` rows of `A`, then `n` rows of `B`. Print `[A, B]` row by row (integers), then the classification.

Example:
```
Input:
2
1 0
0 2
1 1
0 1
Output:
0 -1
0 0
nilpotent
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(int, input().split())) for _ in range(n)]\nB = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 200,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Compute **trace(Aᵏ)** for `k = 1, 2, 3` of a given `2 × 2` matrix using the Newton–Girard identity:
- `tr(A¹) = λ₁ + λ₂`
- `tr(A²) = λ₁² + λ₂²`
- `tr(A³) = λ₁³ + λ₂³`

where `λ₁, λ₂` are the (possibly complex) eigenvalues of `A`.

Read 2 rows of matrix `A` (floats). Print `trace(A)`, `trace(A²)`, `trace(A³)` each on a separate line as integers (they will always be integer-valued for the given inputs).

Example:
```
Input:
2 0
0 3
Output:
5
13
35
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Systems of Linear Equations & Gaussian Elimination (Q11–Q15) → L362
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Compute the **Reduced Row Echelon Form (RREF)** of an augmented matrix and identify the pivot columns.

Read `m n` (size of coefficient matrix A — NOT the augmented matrix), then `m` rows of the augmented matrix `[A | b]` (each row has `n+1` values).

Print the RREF (each value rounded to **4 decimal places**), then on the last line: `pivot columns: c₁ c₂ ...` (0-indexed, space-separated).

Example:
```
Input:
2 2
1 2 3
4 5 6
Output:
1.0000 0.0000 -1.0000
0.0000 1.0000 2.0000
pivot columns: 0 1
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nM = [list(map(float, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 300,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Find the **general solution** of `Ax = b` (particular solution + null space basis).

Read `m n` (size of A), then `m` rows of the augmented matrix `[A | b]`.

- If the system is inconsistent, print `no solution`.
- If the solution is unique, print `unique solution: x₁ x₂ ... xₙ` (4 decimal places).
- If infinitely many solutions exist, print:
  ```
  particular: x₁ x₂ ... xₙ
  null basis:
  v₁
  v₂
  ...
  ```
  where each null basis vector is space-separated with 4 decimal places.

Example:
```
Input:
2 3
1 2 1 4
2 4 2 8
Output:
particular: 4.0000 0.0000 0.0000
null basis:
-2.0000 1.0000 0.0000
-1.0000 0.0000 1.0000
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nM = [list(map(float, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Solve a **tridiagonal linear system** using the **Thomas algorithm** (tridiagonal matrix algorithm).

Read `n`, then the lower diagonal (n−1 values), the main diagonal (n values), the upper diagonal (n−1 values), and the right-hand side `b` (n values) — each on a separate line.

Print the solution vector, one value per line, rounded to **4 decimal places**.

Example:
```
Input:
3
1 1
4 4 4
1 1
1 2 1
Output:
0.1429
0.4286
0.1429
```
MD,
                'starter_code'        => "n = int(input())\nlower = list(map(float, input().split()))\nmain  = list(map(float, input().split()))\nupper = list(map(float, input().split()))\nb     = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 300,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Solve an overdetermined system `Ax = b` via **least squares using the normal equations** `(AᵀA) x̂ = Aᵀb`.

Read `m n` (m > n), then `m` rows of the augmented matrix `[A | b]` (floats). Print the least-squares solution `x̂` (n values, **4 decimal places**, space-separated on one line).

You must implement the Gaussian elimination / inverse yourself — do not use built-in solvers.

Example:
```
Input:
3 2
1 1 2
2 1 3
3 1 5
Output:
1.5000 0.3333
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\ndata = [list(map(float, input().split())) for _ in range(m)]\nA = [row[:n] for row in data]\nb = [row[n] for row in data]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 300,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Run `k` steps of **Gauss-Seidel iteration** on the system `Ax = b`, starting from `x = 0`.

Read `n`, then `n` rows of matrix `A` (floats), then vector `b` (n floats), then integer `k`. The matrix `A` is guaranteed to be strictly diagonally dominant.

Print the solution vector after exactly `k` iterations, one value per line, rounded to **4 decimal places**.

Example:
```
Input:
2
4 1
1 3
1 2
5
Output:
0.0909
0.6364
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\nb = list(map(float, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Matrix Invertibility, Rank & the Four Fundamental Subspaces (Q16–Q20) → L363
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Compute the **inverse of an n × n matrix** using Gauss-Jordan elimination (augment with the identity and row-reduce to RREF).

Read `n`, then `n` rows of matrix `A` (floats). If `A` is singular (det = 0), print `singular`. Otherwise, print the inverse matrix row by row, each value rounded to **4 decimal places**.

Example:
```
Input:
3
1 0 0
0 2 0
0 0 3
Output:
1.0000 0.0000 0.0000
0.0000 0.5000 0.0000
0.0000 0.0000 0.3333
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 300,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Find a **basis for the null space** of an `m × n` matrix using RREF.

Read `m n`, then `m` rows of matrix `A` (floats). Compute RREF, identify free variables, and express each as a null space basis vector.

Print each basis vector on a separate line (n values, **4 decimal places**, space-separated). If the null space is trivial ({0}), print `trivial`.

Example:
```
Input:
3 3
1 2 3
4 5 6
7 8 9
Output:
1.0000 -2.0000 1.0000
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Find a **basis for the column space** of an `m × n` matrix by identifying pivot columns from RREF.

Read `m n`, then `m` rows of matrix `A` (floats). Compute the RREF, identify pivot columns, and return the corresponding **original** columns of `A` as the basis.

Print each basis vector (column of A) on a separate line (m values, **4 decimal places**, space-separated).

Example:
```
Input:
3 3
1 2 3
4 5 6
7 8 9
Output:
1.0000 4.0000 7.0000
2.0000 5.0000 8.0000
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 300,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Compute the **Moore-Penrose pseudoinverse** `A⁺ = (AᵀA)⁻¹Aᵀ` for a full-column-rank matrix `A`.

Read `m n` (m ≥ n), then `m` rows of matrix `A` (floats). The matrix is guaranteed to have full column rank.

Print the `n × m` pseudoinverse matrix row by row, each value rounded to **4 decimal places**.

Example:
```
Input:
3 2
1 0
0 1
0 0
Output:
1.0000 0.0000 0.0000
0.0000 1.0000 0.0000
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 300,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Perform **change of basis** in both directions.

Read `n`, then `n` basis vectors of `B` (each row is a basis vector), then vector `v` in **standard coordinates**, then vector `w` in **B-coordinates**.

Compute:
- `v_B`: coordinates of `v` in basis `B` (solve `B * x = v`).
- `w_std`: standard coordinates of `w` (compute `B * w`).

Print `v_B` on line 1 and `w_std` on line 2, each n values rounded to **4 decimal places**, space-separated.

Example:
```
Input:
2
1 1
1 -1
3 1
2 1
Output:
2.0000 1.0000
3.0000 1.0000
```
MD,
                'starter_code'        => "n = int(input())\nB = [list(map(float, input().split())) for _ in range(n)]\nv = list(map(float, input().split()))\nw = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Determinants: Theory, Computation & Geometric Meaning (Q21–Q25) → L364
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Compute the **determinant of an n × n matrix** via LU decomposition with partial pivoting. Track sign changes from row swaps and compute `det(A) = ± ∏ U[i,i]`.

Read `n`, then `n` rows of matrix `A` (floats). Print the determinant rounded to **4 decimal places**.

Example:
```
Input:
3
1 2 3
0 1 4
5 6 0
Output:
1.0000
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 300,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Compute the **cofactor matrix** `C` and the **adjugate** `adj(A) = Cᵀ` for a `3 × 3` integer matrix.

Read 3 rows of matrix `A`. Print the cofactor matrix `C` (3 rows, **4 decimal places**), then a blank line, then the adjugate `adj(A)` (3 rows, **4 decimal places**).

Recall: `C[i,j] = (−1)^(i+j) * M[i,j]` where `M[i,j]` is the `(i,j)` minor.

Example:
```
Input:
2 0 0
0 3 0
0 0 4
Output:
12.0000 0.0000 0.0000
0.0000 8.0000 0.0000
0.0000 0.0000 6.0000

12.0000 0.0000 0.0000
0.0000 8.0000 0.0000
0.0000 0.0000 6.0000
```
MD,
                'starter_code'        => "A = [list(map(int, input().split())) for _ in range(3)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 250,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Verify the **multiplicative property of determinants**: `det(AB) = det(A) · det(B)`.

Read `n`, then `n` rows of matrix `A`, then `n` rows of matrix `B` (all floats). Compute `det(A)`, `det(B)`, and `det(AB)`. Print them on separate lines (4 decimal places), then `verified` if `|det(AB) − det(A)·det(B)| < 1e-4`, else `failed`.

Example:
```
Input:
2
1 2
3 4
5 6
7 8
Output:
-2.0000
-2.0000
4.0000
verified
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\nB = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 250,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Compute the **characteristic polynomial** of a `3 × 3` matrix. The characteristic polynomial is:

`p(λ) = λ³ − c₂·λ² + c₁·λ − c₀`

where:
- `c₂ = tr(A)`
- `c₁ = (sum of all 2×2 principal minors of A)`
- `c₀ = det(A)`

Read 3 rows of integer matrix `A`. Print `c₂ c₁ c₀` as integers on one line, space-separated.

Example:
```
Input:
2 0 0
0 3 0
0 0 4
Output:
9 26 24
```
MD,
                'starter_code'        => "A = [list(map(int, input().split())) for _ in range(3)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 300,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Verify the **Cayley-Hamilton theorem**: every square matrix satisfies its own characteristic polynomial.

For an `n × n` matrix (n = 2 or 3), compute the characteristic polynomial `p(λ)`, then evaluate `p(A)` (matrix polynomial). Verify that `p(A) = 0` (zero matrix).

Read `n`, then `n` rows of matrix `A` (integers).

For n=2: `p(λ) = λ² − tr(A)·λ + det(A)`, print coefficients as `c₁ c₀` (the non-leading coefficients with their signs).
For n=3: print `c₂ c₁ c₀`.

Then print `p(A)` (rounded to **4 decimal places**), then `verified` or `failed`.

Example:
```
Input:
2
4 1
2 3
Output:
-7 10
0.0000 0.0000
0.0000 0.0000
verified
```
MD,
                'starter_code'        => "import math\nn = int(input())\nA = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Eigenvalues & Eigenvectors (Q26–Q30) → L365
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Implement **power iteration** to estimate the dominant eigenvalue and eigenvector of a matrix.

Read `n`, then the `n × n` matrix `A` (floats), then initial vector `x₀` (n floats), then integer `k` (number of iterations). Start with `x₀` normalised, iterate `k` times: `x_{t+1} = A·x_t / ‖A·x_t‖`. Report the Rayleigh quotient after the final iteration.

Print the eigenvalue estimate (`4 decimal places`) on line 1, and the final eigenvector (`4 decimal places`, space-separated) on line 2.

Example:
```
Input:
2
2 0
0 3
1 1
10
Output:
3.0000
0.0000 1.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\nx = list(map(float, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 300,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Compute the **characteristic polynomial of a 3 × 3 matrix** (with exact integer coefficients) and find its three roots (eigenvalues).

Read 3 rows of matrix `A` (integers). Use the formula:
`p(λ) = λ³ − c₂·λ² + c₁·λ − c₀`

Print `c₂ c₁ c₀` on line 1, then the three eigenvalues sorted in **descending** order, each rounded to **4 decimal places**, one per line.

Example:
```
Input:
2 0 0
0 3 0
0 0 5
Output:
10 31 30
5.0000
3.0000
2.0000
```
MD,
                'starter_code'        => "import math\nA = [list(map(int, input().split())) for _ in range(3)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Implement **inverse power iteration with shift** to find the eigenvalue of a matrix closest to a given shift `σ`.

Read `n`, then `n × n` matrix `A` (floats), then shift `σ` (float), then initial vector `x₀` (n floats), then integer `k`. Perform `k` iterations: solve `(A − σI)·y = x`, normalise to get `x_new`. The eigenvalue estimate is `σ + 1/μ` where `μ = x·y / ‖y‖ ... ` or use the Rayleigh quotient.

Each step: solve `(A − σI)y = x` via Gaussian elimination, set `x = y/‖y‖`, compute Rayleigh quotient `R = xᵀAx`.

Print the final eigenvalue estimate rounded to **4 decimal places** on line 1, and the eigenvector (normalised, 4 dp) on line 2.

Example:
```
Input:
2
5 0
0 2
0
1 1
10
Output:
2.0000
0.0000 1.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\nsigma = float(input())\nx = list(map(float, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 400,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Compute the **Gershgorin discs** for an `n × n` matrix and determine the union bound on eigenvalues.

For each row `i`, the Gershgorin disc has:
- Centre: `c_i = A[i][i]`
- Radius: `r_i = Σ_{j≠i} |A[i][j]|`

All eigenvalues lie in the union of discs `D(c_i, r_i)`.

Read `n`, then `n` rows of `A` (floats). Print each disc as `D(c, r)` with 4 decimal places, one per line. Then print `all eigenvalues in [lo, hi]` where `lo = min(c_i − r_i)` and `hi = max(c_i + r_i)`, rounded to **4 decimal places**.

Example:
```
Input:
2
10 1
2 5
Output:
D(10.0000, 1.0000)
D(5.0000, 2.0000)
all eigenvalues in [3.0000, 11.0000]
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 250,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Implement **matrix deflation**: given one known eigenvalue `λ₁` and its normalised eigenvector `v₁` of a **2 × 2** matrix `A`, compute the deflated matrix `A' = A − λ₁ · v₁ · v₁ᵀ`. Then find the eigenvalues of `A'`.

Read `n=2`, then the `2 × 2` matrix `A` (floats), then `λ₁` (float), then the normalised eigenvector `v₁` (2 floats).

Print `A'` row by row (**4 decimal places**), then `eigenvalues: e₁ e₂` sorted descending (4 dp).

Example:
```
Input:
2
5 0
0 2
5
1 0
Output:
0.0000 0.0000
0.0000 2.0000
eigenvalues: 2.0000 0.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\nlam1 = float(input())\nv1 = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Diagonalization & Matrix Powers (Q31–Q35) → L366
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
**Diagonalize** a `2 × 2` matrix with two distinct real eigenvalues. Find `P` (columns = normalised eigenvectors, ordered by descending eigenvalue) and diagonal `D`.

Read 2 rows of matrix `A` (floats).

Print:
```
P:
<row 0>
<row 1>
D:
<row 0>
<row 1>
verified
```
All values rounded to **4 decimal places**. Verify that `P⁻¹AP ≈ D` (within 1e-4) and print `verified` or `failed` on the last line.

If the matrix is not diagonalizable (repeated or complex eigenvalues), print `not diagonalizable`.

Example:
```
Input:
1 0
0 2
Output:
P:
0.0000 1.0000
1.0000 0.0000
D:
2.0000 0.0000
0.0000 1.0000
verified
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Compute the **matrix exponential** `e^A` for a `2 × 2` matrix `A`.

For a **diagonal** matrix `A = diag(a, d)`: `e^A = diag(eᵃ, eᵈ)`.
For a **diagonalizable** matrix `A = P D P⁻¹`: `e^A = P · e^D · P⁻¹`.

Read 2 rows of matrix `A` (floats). Print `e^A` row by row, rounded to **4 decimal places**.

Example:
```
Input:
1 0
0 2
Output:
2.7183 0.0000
0.0000 7.3891
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Solve a **linear recurrence** `aₙ = p·aₙ₋₁ + q·aₙ₋₂` with initial conditions `a₀` and `a₁` using the **matrix exponentiation** method:

```
[[aₙ₊₁], [aₙ]] = [[p, q], [1, 0]]ⁿ * [[a₁], [a₀]]
```

Read `p q a₀ a₁ n` (all integers) on one line. Print `aₙ` (an integer).

Example:
```
Input:
1 1 0 1 10
Output:
55
```
MD,
                'starter_code'        => "p, q, a0, a1, n = map(int, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 300,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Verify the **Cayley-Hamilton theorem** for a `2 × 2` matrix by explicitly computing `p(A) = A² − tr(A)·A + det(A)·I` and checking it equals the zero matrix.

Read 2 rows of matrix `A` (integers). Compute `p(A)`. Print `p(A)` row by row (**4 decimal places**), then `verified` if `‖p(A)‖_F < 1e-6`, else `failed`.

Example:
```
Input:
4 1
2 3
Output:
0.0000 0.0000
0.0000 0.0000
verified
```
MD,
                'starter_code'        => "row1 = list(map(int, input().split()))\nrow2 = list(map(int, input().split()))\nA = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 250,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Compute **A^n mod m** for a `2 × 2` integer matrix using fast matrix exponentiation with modular arithmetic.

Read 2 rows of matrix `A` (integers), then `n` (a potentially large integer, up to 10^18), then `m`. Print `A^n mod m` row by row (each entry reduced mod m), space-separated.

Example:
```
Input:
1 1
1 0
10
100
Output:
89 55
55 34
```
MD,
                'starter_code'        => "row1 = list(map(int, input().split()))\nrow2 = list(map(int, input().split()))\nn = int(input())\nm = int(input())\nA = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Orthogonality, Projections & Gram-Schmidt (Q36–Q40) → L367
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Compute the **full QR decomposition** `A = Q·R` via Gram-Schmidt, where `Q` is `m × n` with orthonormal columns and `R` is `n × n` upper triangular.

Read `m n` (m ≥ n), then `m` rows of matrix `A` (floats). Print `Q:` then the `Q` matrix (`m` rows, 4 dp), then a blank line, then `R:` then the `R` matrix (`n` rows, 4 dp).

Example:
```
Input:
3 2
1 0
0 1
0 0
Output:
Q:
1.0000 0.0000
0.0000 1.0000
0.0000 0.0000
R:
1.0000 0.0000
0.0000 1.0000
```
MD,
                'starter_code'        => "import math\nm, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Solve the least-squares problem `min ‖Ax − b‖₂` using **QR decomposition**: compute `A = QR`, then solve `Rx = Qᵀb` via back-substitution.

Read `m n` (m > n), then `m` rows of `[A | b]` (floats). Print the least-squares solution `x̂` (n values, **4 decimal places**, space-separated on one line).

Example:
```
Input:
4 2
1 0 2
0 1 3
1 0 4
0 1 5
Output:
3.0000 4.0000
```
MD,
                'starter_code'        => "import math\nm, n = map(int, input().split())\ndata = [list(map(float, input().split())) for _ in range(m)]\nA = [row[:n] for row in data]\nb = [row[n] for row in data]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Compute the **Givens rotation** parameters `c` and `s` that zero out the second component of a 2D vector.

Given vector `[a, b]`, find `c` and `s` such that:
```
[[c, s], [-s, c]] * [a, b]ᵀ = [r, 0]ᵀ
```
where `r = √(a² + b²)`, `c = a/r`, `s = b/r`.

Read `a b` (two floats). Print `c s r` on one line, rounded to **4 decimal places**.

Special case: if `a = b = 0`, print `0.0000 0.0000 0.0000`.

Example:
```
Input:
3 4
Output:
0.6000 0.8000 5.0000
```
MD,
                'starter_code'        => "import math\na, b = map(float, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 250,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Fit a **least-squares polynomial of degree `k`** to a set of data points `(xᵢ, yᵢ)`.

Read `m k` (number of points and degree), then `m` lines each with `xᵢ yᵢ` (floats). Build the Vandermonde design matrix, set up the normal equations, and solve for coefficients `a₀, a₁, …, aₖ` where the polynomial is `y = a₀ + a₁x + a₂x² + … + aₖxᵏ`.

Print `a₀ a₁ … aₖ` on one line, rounded to **4 decimal places**, space-separated.

Example:
```
Input:
3 1
0 1
1 2
2 3
Output:
1.0000 1.0000
```
MD,
                'starter_code'        => "m, k = map(int, input().split())\npts = [list(map(float, input().split())) for _ in range(m)]\nx = [p[0] for p in pts]\ny = [p[1] for p in pts]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Find a **basis for the orthogonal complement** `W^⊥` of a subspace `W` in `R^n`.

Read `n k`, then `k` vectors (the basis of `W`). Build the matrix whose rows are the basis vectors, compute RREF to find its null space (which is `W^⊥`).

Print each basis vector of `W^⊥` (normalised to unit length) on a separate line (**4 decimal places**, space-separated). If `W^⊥ = {0}` (W spans all of R^n), print `trivial`.

Example:
```
Input:
3 2
1 0 0
0 1 0
Output:
0.0000 0.0000 1.0000
```
MD,
                'starter_code'        => "import math\nn, k = map(int, input().split())\nW = [list(map(float, input().split())) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Singular Value Decomposition (SVD) (Q41–Q45) → L368
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Compute the **full SVD** `A = U Σ Vᵀ` of a `2 × 2` matrix from scratch.

Algorithm:
1. Compute `AᵀA` (2×2 symmetric), find its eigenvalues λ₁ ≥ λ₂ ≥ 0 and eigenvectors (V columns).
2. `σᵢ = √λᵢ` (singular values).
3. For non-zero σᵢ: `uᵢ = A·vᵢ / σᵢ`. Fill remaining `u` by orthogonalising.

Read 2 rows of matrix `A` (floats). Print:
```
U:
<2 rows>
sigma: s1 s2
VT:
<2 rows>
```
All values rounded to **4 decimal places**. Singular values sorted descending.

Example:
```
Input:
3 0
0 2
Output:
U:
1.0000 0.0000
0.0000 1.0000
sigma: 3.0000 2.0000
VT:
1.0000 0.0000
0.0000 1.0000
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 400,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Compute the **best rank-k approximation** of a matrix using the truncated SVD, and report the Frobenius approximation error.

Read `m n` (matrix size), then `m` rows of matrix `A` (floats), then integer `k`. Compute the SVD, keep the top-`k` singular values/vectors, reconstruct `Aₖ = Σᵢ₌₁ᵏ σᵢ · uᵢ · vᵢᵀ`. Print `Aₖ` row by row (**4 decimal places**), then `‖A − Aₖ‖_F` (**4 decimal places**) on the last line.

Example:
```
Input:
2 2
3 0
0 2
1
Output:
3.0000 0.0000
0.0000 0.0000
2.0000
```
MD,
                'starter_code'        => "import math\nm, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 400,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Compute the **Moore-Penrose pseudoinverse** `A⁺` using the SVD: `A⁺ = V Σ⁺ Uᵀ`, where `Σ⁺` replaces each non-zero singular value `σᵢ` by `1/σᵢ` and leaves zeros as zeros.

Read `m n`, then `m` rows of matrix `A` (floats). Print the `n × m` pseudoinverse `A⁺` row by row, rounded to **4 decimal places**.

Example:
```
Input:
2 2
3 0
0 2
Output:
0.3333 0.0000
0.0000 0.5000
```
MD,
                'starter_code'        => "import math\nm, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 400,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Compute the **spectral norm** (matrix 2-norm = largest singular value) and the **nuclear norm** (sum of all singular values) of a matrix.

Read `m n`, then `m` rows of matrix `A` (floats). Print the spectral norm on line 1 and the nuclear norm on line 2, both rounded to **4 decimal places**.

Example:
```
Input:
2 2
3 0
0 2
Output:
3.0000
5.0000
```
MD,
                'starter_code'        => "import math\nm, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Solve a least-squares problem using the **SVD**: `x̂ = V Σ⁺ Uᵀ b`.

Read `m n` (m > n), then `m` rows of `[A | b]` (floats). Use the SVD of `A` to compute the minimum-norm least-squares solution.

Print `x̂` (n values, **4 decimal places**, space-separated on one line).

Example:
```
Input:
3 2
1 1 2
2 1 3
3 1 5
Output:
1.5000 0.3333
```
MD,
                'starter_code'        => "import math\nm, n = map(int, input().split())\ndata = [list(map(float, input().split())) for _ in range(m)]\nA = [row[:n] for row in data]\nb = [row[n] for row in data]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Positive Definite Matrices & Quadratic Forms (Q46–Q50) → L369
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Compute the **LDLᵀ decomposition** of a symmetric positive definite matrix `A = L D Lᵀ`, where `L` is unit lower-triangular and `D` is diagonal.

Algorithm (Bunch-Kaufman variant for SPD):
- `D[j,j] = A[j,j] − Σᵢ<ⱼ L[j,i]² · D[i,i]`
- `L[i,j] = (A[i,j] − Σₖ<ⱼ L[i,k] · L[j,k] · D[k,k]) / D[j,j]`

Read `n`, then `n` rows of matrix `A` (floats, symmetric PD). Print `L:` then `L` (n rows, 4 dp), then `D:` then the diagonal of `D` as n space-separated values (4 dp) on one line.

Example:
```
Input:
2
4 2
2 3
Output:
L:
1.0000 0.0000
0.5000 1.0000
D: 4.0000 2.0000
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 400,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Classify a quadratic form `Q(x) = xᵀAx` by computing the eigenvalues of the symmetric matrix `A`.

Read `n`, then `n` rows of symmetric matrix `A` (floats). Compute all eigenvalues. Print the eigenvalues sorted in **descending** order (4 dp, one per line), then on the last line classify as:
- `positive definite` — all eigenvalues > 0
- `positive semi-definite` — all ≥ 0 with at least one = 0
- `negative definite` — all < 0
- `negative semi-definite` — all ≤ 0 with at least one = 0
- `indefinite` — mixed signs

Use tolerance `1e-9` to decide if an eigenvalue equals zero.

Example:
```
Input:
2
2 1
1 3
Output:
3.6180
1.3820
positive definite
```
MD,
                'starter_code'        => "import math\nn = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Find the **unconstrained minimiser** of the quadratic form `Q(x) = xᵀAx − bᵀx` where `A` is symmetric positive definite.

Setting the gradient to zero: `∇Q = 2Ax − b = 0 ⟹ x* = A⁻¹ · (b/2)`.

Read `n`, then `n` rows of symmetric PD matrix `A` (floats), then vector `b` (n floats).

Print the minimiser `x*` (n values, **4 decimal places**, space-separated) on line 1, and the minimum value `Q(x*)` (**4 decimal places**) on line 2.

Example:
```
Input:
2
4 0
0 2
4 2
Output:
0.5000 0.5000
-1.5000
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\nb = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 350,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Apply **Sylvester's criterion** to test whether a symmetric matrix is positive definite by checking the signs of all leading principal minors.

Read `n`, then `n` rows of symmetric matrix `A` (floats). Compute each leading principal minor `M_k = det(A[0:k, 0:k])` for `k = 1, …, n`. Print each as `M{k} = value` (4 dp). Then print `positive definite` if all are strictly positive, else `not positive definite`.

Example:
```
Input:
2
4 1
1 3
Output:
M1 = 4.0000
M2 = 11.0000
positive definite
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 300,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**Comprehensive matrix analysis**: given an `n × n` matrix `A` (n = 2 or 3), perform all of the following and print results in order:

1. `det: <value>` (4 dp)
2. `rank: <int>`
3. `eigenvalues: <e1> <e2> ...` (sorted descending, 4 dp)
4. Classification of `xᵀAx`: `positive definite`, `positive semi-definite`, `negative definite`, `negative semi-definite`, `indefinite`, or `non-symmetric` if `A ≠ Aᵀ`
5. If A is symmetric positive definite: `minimizer: <x*>` (4 dp, space-separated) for `Q(x) = xᵀAx − 2·1ᵀx` (i.e., `b = 2·[1,…,1]`)

Example:
```
Input:
2
2 1
1 2
Output:
det: 3.0000
rank: 2
eigenvalues: 3.0000 1.0000
positive definite
minimizer: 0.3333 0.3333
```
MD,
                'starter_code'        => "import math\nn = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1800,
                'base_xp'             => 500,
            ],
        ];

        // ── Insert questions ───────────────────────────────────────────────
        $questions = [];
        foreach ($questionDefs as $def) {
            $q = DB::table('coding_questions')->where([
                'challenge_id' => $challenge->id,
                'order_index'  => $def['order_index'],
            ])->first();

            if (! $q) {
                $id = DB::table('coding_questions')->insertGetId([
                    'challenge_id'        => $challenge->id,
                    'order_index'         => $def['order_index'],
                    'problem_description' => $def['problem_description'],
                    'starter_code'        => $def['starter_code'],
                    'time_limit_seconds'  => $def['time_limit_seconds'],
                    'base_xp'             => $def['base_xp'],
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
                $questions[$def['order_index']] = $id;
            } else {
                $questions[$def['order_index']] = $q->id;
            }
        }

        // ─────────────────────────────────────────────────────────────────
        // 3. TEST CASES
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $seed = function (int $qIndex, array $cases) use ($questions): void {
            $qId = $questions[$qIndex] ?? null;
            if (! $qId) {
                return;
            }
            foreach ($cases as $case) {
                $exists = DB::table('test_cases')->where([
                    'coding_question_id' => $qId,
                    'order_index'        => $case['order_index'],
                ])->exists();
                if (! $exists) {
                    DB::table('test_cases')->insert([
                        'coding_question_id' => $qId,
                        'input'              => $case['input'],
                        'expected_output'    => $case['expected_output'],
                        'is_hidden'          => $case['is_hidden'],
                        'order_index'        => $case['order_index'],
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
            }
        };

        // ── Q1: Modified Gram-Schmidt ─────────────────────────────────────
        $seed(1, [
            ['input' => "2 2\n3 0\n1 1",                      'expected_output' => "1.0000 0.0000\n0.0000 1.0000",                                            'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 1 0\n1 0 0",                  'expected_output' => "0.7071 0.7071 0.0000\n0.7071 -0.7071 0.0000",                             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 3\n1 0 0\n1 1 0\n1 1 1",           'expected_output' => "1.0000 0.0000 0.0000\n0.0000 1.0000 0.0000\n0.0000 0.0000 1.0000",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n4 3\n0 1",                       'expected_output' => "0.8000 0.6000\n-0.6000 0.8000",                                          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Householder reflector ────────────────────────────────────
        $seed(2, [
            ['input' => "2\n3 4\n1 0",       'expected_output' => "0.6000 0.8000",           'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 2\n3 0 0",   'expected_output' => "1.0000 2.0000 2.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 1\n2 3",        'expected_output' => "3.0000 2.0000",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0 0 1\n1 2 3",   'expected_output' => "3.0000 2.0000 1.0000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Projection onto Col(A) and residual norm ─────────────────
        $seed(3, [
            ['input' => "3 1\n1\n1\n1\n1 2 3",           'expected_output' => "2.0000 2.0000 2.0000\n1.4142",         'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 0\n0 1\n0 0\n3 4 5",     'expected_output' => "3.0000 4.0000 0.0000\n5.0000",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 2\n1 0\n0 1\n1 1\n0 0\n2 3 6 1", 'expected_output' => "2.3333 3.3333 5.6667 0.0000\n1.1547", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 2\n1 2\n3 4\n5 6",             'expected_output' => "5.0000 6.0000\n0.0000",               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Orthogonal decomposition b = b_W + b_perp ────────────────
        $seed(4, [
            ['input' => "2 1\n1 0\n3 4",                   'expected_output' => "3.0000 0.0000\n0.0000 4.0000",                        'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 0 0\n0 1 0\n1 2 3",        'expected_output' => "1.0000 2.0000 0.0000\n0.0000 0.0000 3.0000",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 1\n1 1 1\n3 0 0",               'expected_output' => "1.0000 1.0000 1.0000\n2.0000 -1.0000 -1.0000",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n1 0\n0 1\n5 3",              'expected_output' => "5.0000 3.0000\n0.0000 0.0000",                        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Rayleigh quotient + one power iteration step ─────────────
        $seed(5, [
            ['input' => "2\n2 0\n0 3\n1 0",   'expected_output' => "2.0000\n2.0000\n1.0000 0.0000\n2.0000",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n4 1\n1 3\n1 1",   'expected_output' => "4.5000\n6.4031\n0.7809 0.6247\n4.5854",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0 0\n0 2 0\n0 0 3\n0 1 0", 'expected_output' => "2.0000\n2.0000\n0.0000 1.0000 0.0000\n2.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n5 2\n2 2\n1 0",   'expected_output' => "5.0000\n5.3852\n0.9285 0.3714\n5.9655",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: LU decomposition ─────────────────────────────────────────
        $seed(6, [
            ['input' => "3\n2 1 1\n4 3 3\n8 7 9",
             'expected_output' => "1.0000 0.0000 0.0000\n2.0000 1.0000 0.0000\n4.0000 3.0000 1.0000\n\n2.0000 1.0000 1.0000\n0.0000 1.0000 1.0000\n0.0000 0.0000 2.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3 2\n6 5",
             'expected_output' => "1.0000 0.0000\n2.0000 1.0000\n\n3.0000 2.0000\n0.0000 1.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3\n2 5 4\n1 3 4",
             'expected_output' => "1.0000 0.0000 0.0000\n2.0000 1.0000 0.0000\n1.0000 1.0000 1.0000\n\n1.0000 2.0000 3.0000\n0.0000 1.0000 -2.0000\n0.0000 0.0000 3.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2 4 0 0\n1 5 2 0\n0 2 6 3\n0 0 1 4",
             'expected_output' => "1.0000 0.0000 0.0000 0.0000\n0.5000 1.0000 0.0000 0.0000\n0.0000 0.6667 1.0000 0.0000\n0.0000 0.0000 0.2143 1.0000\n\n2.0000 4.0000 0.0000 0.0000\n0.0000 3.0000 2.0000 0.0000\n0.0000 0.0000 4.6667 3.0000\n0.0000 0.0000 0.0000 3.3571",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Fast matrix exponentiation ───────────────────────────────
        $seed(7, [
            ['input' => "2\n1 1\n1 0\n10",    'expected_output' => "89 55\n55 34",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2 0\n0 3\n5",     'expected_output' => "32 0\n0 243",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 2\n3 4\n3",     'expected_output' => "37 54\n81 118",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 1\n0 1\n100",   'expected_output' => "1 100\n0 1",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Kronecker product ────────────────────────────────────────
        $seed(8, [
            ['input' => "2 2\n1 2\n3 4\n2 2\n0 5\n6 7",
             'expected_output' => "0 5 0 10\n6 7 12 14\n0 15 0 20\n18 21 24 28",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n1 0\n0 1\n2 2\n2 3\n4 5",
             'expected_output' => "2 3 0 0\n4 5 0 0\n0 0 2 3\n0 0 4 5",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 1\n2\n2 2\n1 2\n3 4",
             'expected_output' => "2 4\n6 8",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n1 2\n3 4\n2 2\n1 0\n0 1",
             'expected_output' => "1 0 2 0\n0 1 0 2\n3 0 4 0\n0 3 0 4",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Matrix commutator ────────────────────────────────────────
        $seed(9, [
            ['input' => "2\n1 0\n0 2\n1 1\n0 1",   'expected_output' => "0 -1\n0 0\nnilpotent",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1\n2 3\n4 5",   'expected_output' => "0 0\n0 0\ncommute",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 2\n3 4\n5 6\n7 8",   'expected_output' => "-4 -12\n12 4\nnon-zero",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n2 1\n0 2\n2 0\n1 2",   'expected_output' => "1 0\n-3 0\nnon-zero",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Trace of powers ─────────────────────────────────────────
        $seed(10, [
            ['input' => "2 0\n0 3",    'expected_output' => "5\n13\n35",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1\n0 2",    'expected_output' => "3\n5\n9",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 1\n2 3",    'expected_output' => "7\n29\n133",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0 1\n-1 0",   'expected_output' => "0\n-2\n0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: RREF with pivot columns ─────────────────────────────────
        $seed(11, [
            ['input' => "2 2\n1 2 3\n4 5 6",
             'expected_output' => "1.0000 0.0000 -1.0000\n0.0000 1.0000 2.0000\npivot columns: 0 1",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 3\n1 2 0 1\n2 4 1 3\n0 0 1 1",
             'expected_output' => "1.0000 2.0000 0.0000 1.0000\n0.0000 0.0000 1.0000 1.0000\n0.0000 0.0000 0.0000 0.0000\npivot columns: 0 2",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3\n1 0 2 3\n0 1 3 4",
             'expected_output' => "1.0000 0.0000 2.0000 3.0000\n0.0000 1.0000 3.0000 4.0000\npivot columns: 0 1",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 3\n2 4 6 8\n1 2 4 5\n3 6 9 12",
             'expected_output' => "1.0000 2.0000 0.0000 1.0000\n0.0000 0.0000 1.0000 1.0000\n0.0000 0.0000 0.0000 0.0000\npivot columns: 0 2",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: General solution ────────────────────────────────────────
        $seed(12, [
            ['input' => "2 2\n2 1 5\n1 -1 1",
             'expected_output' => "unique solution: 2.0000 1.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 3\n1 2 1 4\n2 4 2 8",
             'expected_output' => "particular: 4.0000 0.0000 0.0000\nnull basis:\n-2.0000 1.0000 0.0000\n-1.0000 0.0000 1.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n1 2 3\n2 4 7",
             'expected_output' => "no solution",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 4\n1 0 1 0 2\n0 1 0 1 3\n0 0 0 0 0",
             'expected_output' => "particular: 2.0000 3.0000 0.0000 0.0000\nnull basis:\n-1.0000 0.0000 1.0000 0.0000\n0.0000 -1.0000 0.0000 1.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Thomas algorithm ────────────────────────────────────────
        $seed(13, [
            ['input' => "3\n1 1\n4 4 4\n1 1\n1 2 1",   'expected_output' => "0.1429\n0.4286\n0.1429",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 1 1\n2 2 2 2\n1 1 1\n1 0 0 1", 'expected_output' => "0.6000\n-0.2000\n-0.2000\n0.6000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 1\n3 3 3\n1 1\n1 1 1",   'expected_output' => "0.2857\n0.1429\n0.2857",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2 2\n5 5 5\n2 2\n1 1 1",   'expected_output' => "0.1765\n0.0588\n0.1765",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Least squares via normal equations ───────────────────────
        $seed(14, [
            ['input' => "3 1\n1 1\n1 2\n1 3",           'expected_output' => "2.0000",               'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 1 2\n2 1 3\n3 1 5",     'expected_output' => "1.5000 0.3333",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 2\n1 0 1\n0 1 2\n1 0 3\n0 1 4", 'expected_output' => "2.0000 3.0000",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 2\n1 0 3\n0 1 4\n2 1 7\n1 2 8", 'expected_output' => "2.1000 3.1000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Gauss-Seidel iteration ──────────────────────────────────
        $seed(15, [
            ['input' => "2\n4 1\n1 3\n1 2\n5",                          'expected_output' => "0.0909\n0.6364",                'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n4 -1 0\n-1 4 -1\n0 -1 4\n1 2 1\n5",        'expected_output' => "0.4285\n0.7143\n0.4286",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n3 0\n0 2\n6 4\n5",                          'expected_output' => "2.0000\n2.0000",                'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n5 0 0\n0 5 0\n0 0 5\n5 10 15\n5",           'expected_output' => "1.0000\n2.0000\n3.0000",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Gauss-Jordan inverse ────────────────────────────────────
        $seed(16, [
            ['input' => "3\n1 0 0\n0 2 0\n0 0 3",
             'expected_output' => "1.0000 0.0000 0.0000\n0.0000 0.5000 0.0000\n0.0000 0.0000 0.3333",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2 1 0\n1 3 1\n0 1 2",
             'expected_output' => "0.6250 -0.2500 0.1250\n-0.2500 0.5000 -0.2500\n0.1250 -0.2500 0.6250",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 2\n3 4",
             'expected_output' => "-2.0000 1.0000\n1.5000 -0.5000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 2 0\n0 3 0\n1 4 1",
             'expected_output' => "1.0000 -0.6667 0.0000\n0.0000 0.3333 0.0000\n-1.0000 -0.6667 1.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Null space basis ────────────────────────────────────────
        $seed(17, [
            ['input' => "3 3\n1 2 3\n4 5 6\n7 8 9",   'expected_output' => "1.0000 -2.0000 1.0000",                              'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 3\n1 2 1\n2 4 2",           'expected_output' => "-2.0000 1.0000 0.0000\n-1.0000 0.0000 1.0000",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n1 2\n3 4",               'expected_output' => "trivial",                                            'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 4\n1 2 0 0\n0 0 1 0\n0 0 0 1", 'expected_output' => "-2.0000 1.0000 0.0000 0.0000",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Column space basis ──────────────────────────────────────
        $seed(18, [
            ['input' => "3 3\n1 2 3\n4 5 6\n7 8 9",   'expected_output' => "1.0000 4.0000 7.0000\n2.0000 5.0000 8.0000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 0\n0 1\n0 0",         'expected_output' => "1.0000 0.0000 0.0000\n0.0000 1.0000 0.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3\n1 0 2\n0 1 3",          'expected_output' => "1.0000 0.0000\n0.0000 1.0000",                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 3\n1 2 1\n2 4 2\n0 1 1\n0 2 2", 'expected_output' => "1.0000 2.0000 0.0000 0.0000\n2.0000 4.0000 1.0000 2.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q19: Pseudoinverse A+ = (A^TA)^{-1} A^T ─────────────────────
        $seed(19, [
            ['input' => "3 2\n1 0\n0 1\n0 0",           'expected_output' => "1.0000 0.0000 0.0000\n0.0000 1.0000 0.0000",                       'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 1\n1\n1\n1",                  'expected_output' => "0.3333 0.3333 0.3333",                                             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2\n1 1\n2 1\n3 1",            'expected_output' => "-0.5000 0.0000 0.5000\n1.3333 0.3333 -0.6667",                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 2\n1 0\n0 1\n2 0\n0 2",       'expected_output' => "0.2000 0.0000 0.4000 0.0000\n0.0000 0.2000 0.0000 0.4000",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Change of basis ─────────────────────────────────────────
        $seed(20, [
            ['input' => "2\n1 0\n0 1\n3 4\n2 5",         'expected_output' => "3.0000 4.0000\n2.0000 5.0000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 1\n1 -1\n3 1\n2 1",         'expected_output' => "2.0000 1.0000\n3.0000 1.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0 0\n0 1 0\n0 0 1\n5 -3 2\n1 2 3", 'expected_output' => "5.0000 -3.0000 2.0000\n1.0000 2.0000 3.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n3 0\n0 2\n6 4\n1 3",          'expected_output' => "2.0000 2.0000\n3.0000 6.0000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: n×n determinant via LU ──────────────────────────────────
        $seed(21, [
            ['input' => "3\n1 2 3\n0 1 4\n5 6 0",    'expected_output' => "1.0000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2 1 1\n4 3 3\n8 7 9",    'expected_output' => "4.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 0 0 0\n0 2 0 0\n0 0 3 0\n0 0 0 4", 'expected_output' => "24.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n1 2 3\n4 5 6\n7 8 9",    'expected_output' => "0.0000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Cofactor matrix and adjugate ────────────────────────────
        $seed(22, [
            ['input' => "2 0 0\n0 3 0\n0 0 4",
             'expected_output' => "12.0000 0.0000 0.0000\n0.0000 8.0000 0.0000\n0.0000 0.0000 6.0000\n\n12.0000 0.0000 0.0000\n0.0000 8.0000 0.0000\n0.0000 0.0000 6.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3\n0 1 4\n5 6 0",
             'expected_output' => "-24.0000 20.0000 -5.0000\n18.0000 -15.0000 4.0000\n5.0000 -4.0000 1.0000\n\n-24.0000 18.0000 5.0000\n20.0000 -15.0000 -4.0000\n-5.0000 4.0000 1.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 0 0\n0 1 0\n0 0 1",
             'expected_output' => "1.0000 0.0000 0.0000\n0.0000 1.0000 0.0000\n0.0000 0.0000 1.0000\n\n1.0000 0.0000 0.0000\n0.0000 1.0000 0.0000\n0.0000 0.0000 1.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 2 3\n4 5 6\n7 8 10",
             'expected_output' => "2.0000 2.0000 -3.0000\n4.0000 -11.0000 6.0000\n-3.0000 6.0000 -3.0000\n\n2.0000 4.0000 -3.0000\n2.0000 -11.0000 6.0000\n-3.0000 6.0000 -3.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Verify det(AB) = det(A)det(B) ───────────────────────────
        $seed(23, [
            ['input' => "2\n1 2\n3 4\n5 6\n7 8",           'expected_output' => "-2.0000\n-2.0000\n4.0000\nverified",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1\n3 1\n2 5",           'expected_output' => "1.0000\n13.0000\n13.0000\nverified",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3\n0 1 4\n5 6 0\n2 0 0\n0 3 0\n0 0 4", 'expected_output' => "1.0000\n24.0000\n24.0000\nverified", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n2 0\n0 3\n0 1\n-1 0",         'expected_output' => "6.0000\n1.0000\n6.0000\nverified",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Characteristic polynomial coefficients ───────────────────
        $seed(24, [
            ['input' => "2 0 0\n0 3 0\n0 0 4",    'expected_output' => "9 26 24",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0 0\n0 1 0\n0 0 1",    'expected_output' => "3 3 1",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 0\n0 3 0\n0 0 5",    'expected_output' => "9 23 15",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 1 0\n0 2 1\n0 0 2",    'expected_output' => "6 12 8",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Cayley-Hamilton verification ────────────────────────────
        $seed(25, [
            ['input' => "2\n4 1\n2 3",
             'expected_output' => "-7 10\n0.0000 0.0000\n0.0000 0.0000\nverified",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1",
             'expected_output' => "-2 1\n0.0000 0.0000\n0.0000 0.0000\nverified",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2 0 0\n0 3 0\n0 0 4",
             'expected_output' => "-9 26 -24\n0.0000 0.0000 0.0000\n0.0000 0.0000 0.0000\n0.0000 0.0000 0.0000\nverified",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0 1 0\n0 0 1\n0 0 0",
             'expected_output' => "0 0 0\n0.0000 0.0000 0.0000\n0.0000 0.0000 0.0000\n0.0000 0.0000 0.0000\nverified",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Power iteration ─────────────────────────────────────────
        $seed(26, [
            ['input' => "2\n2 0\n0 3\n1 1\n10",   'expected_output' => "3.0000\n0.0000 1.0000",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5 0\n0 2\n1 0\n5",    'expected_output' => "5.0000\n1.0000 0.0000",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n3 1\n0 2\n1 0\n5",    'expected_output' => "3.0000\n1.0000 0.0000",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n4 2\n1 3\n1 1\n20",   'expected_output' => "5.0000\n0.8944 0.4472",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Char poly of 3×3 + eigenvalues ─────────────────────────
        $seed(27, [
            ['input' => "2 0 0\n0 3 0\n0 0 5",
             'expected_output' => "10 31 30\n5.0000\n3.0000\n2.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0 0\n0 1 0\n0 0 1",
             'expected_output' => "3 3 1\n1.0000\n1.0000\n1.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 0\n0 3 0\n0 0 5",
             'expected_output' => "9 23 15\n5.0000\n3.0000\n1.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 1 0\n0 2 1\n0 0 2",
             'expected_output' => "6 12 8\n2.0000\n2.0000\n2.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Inverse power iteration ────────────────────────────────
        $seed(28, [
            ['input' => "2\n5 0\n0 2\n0\n1 1\n10",     'expected_output' => "2.0000\n0.0000 1.0000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5 0\n0 2\n4\n1 1\n10",     'expected_output' => "5.0000\n1.0000 0.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n4 0\n0 1\n0\n1 1\n5",      'expected_output' => "1.0000\n0.0000 1.0000",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n4 0\n0 1\n3\n1 0\n5",      'expected_output' => "4.0000\n1.0000 0.0000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Gershgorin circles ──────────────────────────────────────
        $seed(29, [
            ['input' => "2\n10 1\n2 5",
             'expected_output' => "D(10.0000, 1.0000)\nD(5.0000, 2.0000)\nall eigenvalues in [3.0000, 11.0000]",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n6 1 0\n1 4 1\n0 1 5",
             'expected_output' => "D(6.0000, 1.0000)\nD(4.0000, 2.0000)\nD(5.0000, 1.0000)\nall eigenvalues in [2.0000, 7.0000]",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n4 0\n0 3",
             'expected_output' => "D(4.0000, 0.0000)\nD(3.0000, 0.0000)\nall eigenvalues in [3.0000, 4.0000]",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 0 0\n0 2 0\n0 0 3",
             'expected_output' => "D(1.0000, 0.0000)\nD(2.0000, 0.0000)\nD(3.0000, 0.0000)\nall eigenvalues in [1.0000, 3.0000]",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Deflation ───────────────────────────────────────────────
        $seed(30, [
            ['input' => "2\n5 0\n0 2\n5\n1 0",
             'expected_output' => "0.0000 0.0000\n0.0000 2.0000\neigenvalues: 2.0000 0.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n4 2\n1 3\n5\n0.8944 0.4472",
             'expected_output' => "0.0000 -1.0000\n0.0000 2.0000\neigenvalues: 2.0000 0.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n3 0\n0 2\n3\n1 0",
             'expected_output' => "0.0000 0.0000\n0.0000 2.0000\neigenvalues: 2.0000 0.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 0\n0 4\n4\n0 1",
             'expected_output' => "1.0000 0.0000\n0.0000 0.0000\neigenvalues: 1.0000 0.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Full diagonalization of 2×2 ────────────────────────────
        $seed(31, [
            ['input' => "1 0\n0 2",
             'expected_output' => "P:\n0.0000 1.0000\n1.0000 0.0000\nD:\n2.0000 0.0000\n0.0000 1.0000\nverified",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 1\n2 3",
             'expected_output' => "P:\n0.7071 0.4472\n0.7071 -0.8944\nD:\n5.0000 0.0000\n0.0000 2.0000\nverified",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 0\n0 5",
             'expected_output' => "P:\n0.0000 1.0000\n1.0000 0.0000\nD:\n5.0000 0.0000\n0.0000 2.0000\nverified",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 1\n0 2",
             'expected_output' => "P:\n1.0000 -0.7071\n0.0000 0.7071\nD:\n3.0000 0.0000\n0.0000 2.0000\nverified",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Matrix exponential for 2×2 ─────────────────────────────
        $seed(32, [
            ['input' => "0 0\n0 0",    'expected_output' => "1.0000 0.0000\n0.0000 1.0000",            'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0\n0 2",    'expected_output' => "2.7183 0.0000\n0.0000 7.3891",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 0\n0 3",    'expected_output' => "7.3891 0.0000\n0.0000 20.0855",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 1\n2 3",    'expected_output' => "101.4052 47.0080\n94.0161 54.3971",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Recurrence via matrix method ────────────────────────────
        $seed(33, [
            ['input' => "1 1 0 1 10",    'expected_output' => "55",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 0 1 2 10",    'expected_output' => "1024",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 0 1 5",     'expected_output' => "11",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 -2 1 3 6",    'expected_output' => "127",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Cayley-Hamilton (2×2 explicit) ──────────────────────────
        $seed(34, [
            ['input' => "4 1\n2 3",    'expected_output' => "0.0000 0.0000\n0.0000 0.0000\nverified",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0\n0 2",    'expected_output' => "0.0000 0.0000\n0.0000 0.0000\nverified",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 1\n0 2",    'expected_output' => "0.0000 0.0000\n0.0000 0.0000\nverified",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "0 1\n-1 0",   'expected_output' => "0.0000 0.0000\n0.0000 0.0000\nverified",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Matrix power mod m ──────────────────────────────────────
        $seed(35, [
            ['input' => "1 1\n1 0\n10\n100",    'expected_output' => "89 55\n55 34",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 1\n1 1\n5\n10",      'expected_output' => "9 5\n5 4",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 1\n0 1\n50\n7",      'expected_output' => "1 1\n0 1",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 0\n0 2\n10\n1000",   'expected_output' => "1 0\n0 24",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Full QR decomposition ───────────────────────────────────
        $seed(36, [
            ['input' => "3 2\n1 0\n0 1\n0 0",
             'expected_output' => "Q:\n1.0000 0.0000\n0.0000 1.0000\n0.0000 0.0000\nR:\n1.0000 0.0000\n0.0000 1.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 1\n1 0\n0 1",
             'expected_output' => "Q:\n0.7071 0.4082\n0.7071 -0.4082\n0.0000 0.8165\nR:\n1.4142 0.7071\n0.0000 1.2247",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n3 0\n4 5",
             'expected_output' => "Q:\n0.6000 -0.8000\n0.8000 0.6000\nR:\n5.0000 4.0000\n0.0000 3.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 2\n1 0\n0 1\n1 0\n0 1",
             'expected_output' => "Q:\n0.7071 0.0000\n0.0000 0.7071\n0.7071 0.0000\n0.0000 0.7071\nR:\n1.4142 0.0000\n0.0000 1.4142",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Least squares via QR ────────────────────────────────────
        $seed(37, [
            ['input' => "4 2\n1 0 2\n0 1 3\n1 0 4\n0 1 5",   'expected_output' => "3.0000 4.0000",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 1 2\n2 1 3\n3 1 5",           'expected_output' => "1.5000 0.3333",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 1\n2 2\n2 2\n2 2",                 'expected_output' => "1.0000",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 3\n1 0 0 1\n0 1 0 2\n0 0 1 3\n1 1 1 7", 'expected_output' => "1.2500 2.2500 3.2500", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q38: Givens rotation parameters ─────────────────────────────
        $seed(38, [
            ['input' => "3 4",     'expected_output' => "0.6000 0.8000 5.0000",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0",     'expected_output' => "1.0000 0.0000 1.0000",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "0 1",     'expected_output' => "0.0000 1.0000 1.0000",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 12",    'expected_output' => "0.3846 0.9231 13.0000",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Polynomial least squares fit ────────────────────────────
        $seed(39, [
            ['input' => "3 1\n0 1\n1 2\n2 3",     'expected_output' => "1.0000 1.0000",           'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 2\n0 0\n1 1\n2 4\n3 9", 'expected_output' => "0.0000 0.0000 1.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 1\n1 2\n2 5\n3 8",      'expected_output' => "1.0000 2.0000" ,          'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 1\n0 1\n1 3\n2 5\n3 8", 'expected_output' => "0.2000 2.7000",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Orthogonal complement basis ─────────────────────────────
        $seed(40, [
            ['input' => "3 2\n1 0 0\n0 1 0",    'expected_output' => "0.0000 0.0000 1.0000",            'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 1\n1 1",              'expected_output' => "-0.7071 0.7071",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 1\n1 0 0",            'expected_output' => "0.0000 1.0000 0.0000\n0.0000 0.0000 1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 2\n1 0\n0 1",         'expected_output' => "trivial",                         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Full SVD of 2×2 ────────────────────────────────────────
        $seed(41, [
            ['input' => "3 0\n0 2",
             'expected_output' => "U:\n1.0000 0.0000\n0.0000 1.0000\nsigma: 3.0000 2.0000\nVT:\n1.0000 0.0000\n0.0000 1.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 2\n3 0",
             'expected_output' => "U:\n0.0000 1.0000\n1.0000 0.0000\nsigma: 3.0000 2.0000\nVT:\n1.0000 0.0000\n0.0000 1.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 0\n0 0",
             'expected_output' => "U:\n1.0000 0.0000\n0.0000 1.0000\nsigma: 1.0000 0.0000\nVT:\n1.0000 0.0000\n0.0000 1.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n1 1",
             'expected_output' => "U:\n0.8944 -0.4472\n0.4472 0.8944\nsigma: 3.1623 0.0000\nVT:\n0.7071 0.7071\n0.7071 -0.7071",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Best rank-k approximation ───────────────────────────────
        $seed(42, [
            ['input' => "2 2\n3 0\n0 2\n1",   'expected_output' => "3.0000 0.0000\n0.0000 0.0000\n2.0000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n3 0\n0 2\n2",   'expected_output' => "3.0000 0.0000\n0.0000 2.0000\n0.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n1 0\n0 1\n1",   'expected_output' => "1.0000 0.0000\n0.0000 0.0000\n1.0000",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n2 2\n1 1\n1",   'expected_output' => "2.0000 2.0000\n1.0000 1.0000\n0.0000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Pseudoinverse via SVD ────────────────────────────────────
        $seed(43, [
            ['input' => "2 2\n3 0\n0 2",    'expected_output' => "0.3333 0.0000\n0.0000 0.5000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n1 0\n0 0",    'expected_output' => "1.0000 0.0000\n0.0000 0.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n2 0\n0 4",    'expected_output' => "0.5000 0.0000\n0.0000 0.2500",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n0 2\n3 0",    'expected_output' => "0.0000 0.3333\n0.5000 0.0000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Spectral norm and nuclear norm ───────────────────────────
        $seed(44, [
            ['input' => "2 2\n3 0\n0 2",    'expected_output' => "3.0000\n5.0000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n1 0\n0 1",    'expected_output' => "1.0000\n2.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n5 0\n0 0",    'expected_output' => "5.0000\n5.0000",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n2 2\n1 1",    'expected_output' => "3.1623\n3.1623",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Least squares via SVD ────────────────────────────────────
        $seed(45, [
            ['input' => "3 2\n1 0 1\n0 1 2\n0 0 0",   'expected_output' => "1.0000 2.0000",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n2 0 4\n0 3 9",           'expected_output' => "2.0000 3.0000",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2\n1 1 2\n2 1 3\n3 1 5",   'expected_output' => "1.5000 0.3333",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 2\n1 0 1\n0 1 2\n1 1 4",   'expected_output' => "1.3333 2.3333",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: LDL^T decomposition ──────────────────────────────────────
        $seed(46, [
            ['input' => "2\n4 2\n2 3",
             'expected_output' => "L:\n1.0000 0.0000\n0.5000 1.0000\nD: 4.0000 2.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2 0 0\n0 3 0\n0 0 4",
             'expected_output' => "L:\n1.0000 0.0000 0.0000\n0.0000 1.0000 0.0000\n0.0000 0.0000 1.0000\nD: 2.0000 3.0000 4.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n4 2 0\n2 3 1\n0 1 2",
             'expected_output' => "L:\n1.0000 0.0000 0.0000\n0.5000 1.0000 0.0000\n0.0000 0.5000 1.0000\nD: 4.0000 2.0000 1.5000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n9 3\n3 2",
             'expected_output' => "L:\n1.0000 0.0000\n0.3333 1.0000\nD: 9.0000 1.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Quadratic form classification via eigenvalues ────────────
        $seed(47, [
            ['input' => "2\n2 1\n1 3",      'expected_output' => "3.6180\n1.3820\npositive definite",         'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n2 1",      'expected_output' => "3.0000\n-1.0000\nindefinite",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n-1 -1\n-1 -2",  'expected_output' => "-0.3820\n-2.6180\nnegative definite",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 0 0\n0 2 0\n0 0 0", 'expected_output' => "2.0000\n1.0000\n0.0000\npositive semi-definite", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q48: Minimise x^TAx - b^Tx ───────────────────────────────────
        $seed(48, [
            ['input' => "2\n4 0\n0 2\n4 2",     'expected_output' => "0.5000 0.5000\n-1.5000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1\n2 4",     'expected_output' => "1.0000 2.0000\n-5.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n2 1\n1 2\n3 3",     'expected_output' => "0.5000 0.5000\n-1.5000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 0 0\n0 2 0\n0 0 3\n2 4 6", 'expected_output' => "1.0000 1.0000 1.0000\n-6.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q49: Sylvester's criterion ────────────────────────────────────
        $seed(49, [
            ['input' => "2\n4 1\n1 3",
             'expected_output' => "M1 = 4.0000\nM2 = 11.0000\npositive definite",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n2 1",
             'expected_output' => "M1 = 1.0000\nM2 = -3.0000\nnot positive definite",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n4 2 1\n2 3 1\n1 1 2",
             'expected_output' => "M1 = 4.0000\nM2 = 8.0000\nM3 = 13.0000\npositive definite",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 0 0\n0 1 0\n0 0 -1",
             'expected_output' => "M1 = 1.0000\nM2 = 1.0000\nM3 = -1.0000\nnot positive definite",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Comprehensive analysis ───────────────────────────────────
        $seed(50, [
            ['input' => "2\n2 1\n1 2",
             'expected_output' => "det: 3.0000\nrank: 2\neigenvalues: 3.0000 1.0000\npositive definite\nminimizer: 0.3333 0.3333",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n3 4",
             'expected_output' => "det: -2.0000\nrank: 2\neigenvalues: 5.3723 -0.3723\nnon-symmetric",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0 0\n0 2 0\n0 0 3",
             'expected_output' => "det: 6.0000\nrank: 3\neigenvalues: 3.0000 2.0000 1.0000\npositive definite\nminimizer: 1.0000 0.5000 0.3333",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 2\n2 1",
             'expected_output' => "det: -3.0000\nrank: 2\neigenvalues: 3.0000 -1.0000\nindefinite",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 9 Coding (Professional) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}