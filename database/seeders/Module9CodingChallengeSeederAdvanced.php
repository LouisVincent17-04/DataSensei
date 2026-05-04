<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 9 — Linear Algebra (Advanced / Level 4) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Advanced tier
 *   2. coding_questions    — 50 questions covering advanced Linear Algebra concepts
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
 * Difficulty: Advanced — problems require multi-step algorithmic implementations,
 * numerical methods, and deep understanding of linear algebra theory. No library
 * hints are provided; learners must build algorithms from scratch.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module9CodingChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (! $category) {
            $this->command->error('Advanced category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 9 — Linear Algebra (Advanced) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Linear Algebra',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Master advanced Linear Algebra through rigorous algorithmic problem solving. Implement LU and Cholesky decompositions, QR factorization, power iteration, the QR algorithm, SVD-based pseudoinverses, Gram-Schmidt in higher dimensions, Householder reflections, generalized eigenvalue problems, Schur complement analysis, and matrix exponentiation — all from scratch in pure Python.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 2000,
                'order_index'        => 4,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 advanced coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Vectors, Scalars & the Geometry of Linear Algebra (Q1–Q5) → L360
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Given `k` vectors in **R^n**, determine whether they are **linearly independent** by computing the rank of the matrix formed by stacking them as rows.

Read `k n` on the first line, then `k` rows each with `n` space-separated floats.

- If `rank == k`: print `independent`
- Otherwise: print `dependent` on line 1, then `rank: r` on line 2

Example:
```
Input:
3 2
1 0
0 1
1 1
Output:
dependent
rank: 2
```
MD,
                'starter_code'        => "k, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(k)]\n# Compute rank via Gaussian elimination and determine independence\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
A **Householder reflection** maps a vector **x** to `±‖x‖ e₁` using the matrix `H = I − 2vvᵀ/vᵀv`, where `v = x + sign(x₁)‖x‖e₁`.

Given vector **x** of length `n` and a second vector **u** of length `n`, compute and print **H u** (the reflection of **u**), each component rounded to **4 decimal places**, space-separated on one line.

Read `n`, then **x** (n floats), then **u** (n floats).

Example:
```
Input:
2
3 4
1 0
Output:
-0.6000 -0.8000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nx = list(map(float, input().split()))\nu = list(map(float, input().split()))\n# Build Householder vector v, then compute Hu = u - 2*(v·u / v·v)*v\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Compute the **shortest distance from a point Q to a parametric line** in **R³**. The line passes through point **P** with direction **d**.

Formula: `distance = ‖(Q − P) × d‖ / ‖d‖`

Read three lines:
- **P**: 3 space-separated floats (point on line)
- **d**: 3 space-separated floats (direction vector)
- **Q**: 3 space-separated floats (query point)

Print the distance rounded to **4 decimal places**.

Example:
```
Input:
0 0 0
1 0 0
0 1 0
Output:
1.0000
```
MD,
                'starter_code'        => "import math\nP = list(map(float, input().split()))\nd = list(map(float, input().split()))\nQ = list(map(float, input().split()))\n# Compute cross product of (Q-P) and d, then distance = norm(cross) / norm(d)\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Apply the **Gram-Schmidt process** to three linearly independent vectors **v1**, **v2**, **v3** in **R^n** to produce an orthonormal set **e1**, **e2**, **e3**.

Read `n`, then three rows (each n space-separated floats). Print the three orthonormal vectors, one per line, each component rounded to **4 decimal places**, space-separated.

Example:
```
Input:
3
1 0 0
1 1 0
1 1 1
Output:
1.0000 0.0000 0.0000
0.0000 1.0000 0.0000
0.0000 0.0000 1.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nvecs = [list(map(float, input().split())) for _ in range(3)]\n# Apply Gram-Schmidt: for each vector subtract projections onto all previous basis vectors, then normalise\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
The **scalar triple product** of three 3D vectors **a**, **b**, **c** is `a · (b × c)`. Its absolute value equals the **volume of the parallelepiped** they define.

Read three lines, each with 3 space-separated floats (vectors **a**, **b**, **c**).

Print:
- Line 1: `triple: X` — the scalar triple product rounded to 4 decimal places
- Line 2: `volume: Y` — the absolute value rounded to 4 decimal places

Example:
```
Input:
1 0 0
0 1 0
0 0 1
Output:
triple: 1.0000
volume: 1.0000
```
MD,
                'starter_code'        => "import math\na = list(map(float, input().split()))\nb = list(map(float, input().split()))\nc = list(map(float, input().split()))\n# Compute cross(b, c) then dot a with the result\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Matrix Fundamentals: Operations & Special Structures (Q6–Q10) → L361
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Compute the **LU decomposition** of an `n × n` matrix using **Doolittle's method** (L has 1s on the diagonal, no row pivoting). Assume no zero pivot will be encountered.

Read `n`, then `n` rows.

Print **L** row by row (n rows), then **U** row by row (n rows). Each value rounded to **4 decimal places**, space-separated.

Example:
```
Input:
2
4 3
6 3
Output:
1.0000 0.0000
1.5000 1.0000
4.0000 3.0000
0.0000 -1.5000
```
MD,
                'starter_code'        => "import copy\nn = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Compute L and U via Doolittle's method in-place\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Solve **Ax = b** using **LU decomposition** (Doolittle, no pivoting). Decompose A = LU, then solve Ly = b (forward substitution), then Ux = y (back substitution).

Read `n`, then `n` rows of **A**, then **b** (n space-separated floats on one line).

Print each component of **x** rounded to **4 decimal places**, one per line.

Example:
```
Input:
2
4 3
6 3
10 12
Output:
1.0000
2.0000
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\nb = list(map(float, input().split()))\n# LU decompose A, forward sub for y, back sub for x\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Compute the **inverse of an n × n matrix** using **Gauss-Jordan elimination** (augment with identity, row-reduce to get [I | A⁻¹]).

Read `n`, then `n` rows.

If the matrix is singular, print `singular matrix`. Otherwise, print the inverse row by row, each value rounded to **4 decimal places**, space-separated.

Example:
```
Input:
2
2 1
4 3
Output:
1.5000 -0.5000
-2.0000 1.0000
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Augment A with I, apply Gauss-Jordan, extract right half\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Compute **A^k mod (10⁹ + 7)** for a **2 × 2 integer matrix** A and large integer k using **matrix exponentiation by repeated squaring**.

Read two rows of A (integers), then k on a third line.

Print the result matrix (2 rows, 2 values each), each entry modulo `10^9 + 7`.

Example:
```
Input:
1 1
1 0
10
Output:
89 55
55 34
```
MD,
                'starter_code'        => "MOD = 10**9 + 7\nrow1 = list(map(int, input().split()))\nrow2 = list(map(int, input().split()))\nk = int(input())\nA = [row1, row2]\n# Implement matrix multiplication mod MOD, then fast exponentiation\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Compute the **commutator** `[A, B] = AB − BA` of two `n × n` matrices. If the result is the zero matrix (all entries zero within 1e-9 tolerance), print `commutes`; otherwise print `does not commute` and print the commutator matrix row by row (integers, since inputs are integer matrices).

Read `n`, then `n` rows of **A**, then `n` rows of **B**.

Example:
```
Input:
2
1 0
0 1
2 3
4 5
Output:
commutes
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(int, input().split())) for _ in range(n)]\nB = [list(map(int, input().split())) for _ in range(n)]\n# Compute AB, BA, then C = AB - BA\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Systems of Linear Equations & Gaussian Elimination (Q11–Q15) → L362
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Compute the **Reduced Row Echelon Form (RREF)** of an augmented matrix `[A|b]`.

Read `m n` on the first line (m equations, n-1 variables so the matrix is m×n), then `m` rows of `n` floats (last column is b).

Print the RREF, each value rounded to **4 decimal places**, space-separated, row by row.

Example:
```
Input:
2 3
1 2 3
4 5 6
Output:
1.0000 0.0000 -1.0000
0.0000 1.0000 2.0000
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nM = [list(map(float, input().split())) for _ in range(m)]\n# Apply full Gauss-Jordan (forward elimination + back substitution) to get RREF\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Solve an **overdetermined system Ax = b** (m equations, n unknowns, m > n) in the **least-squares sense** using the **normal equations**: `AᵀAx = Aᵀb`.

Read `m n`, then `m` rows of A (n floats each), then `b` (m floats on one line).

Print the least-squares solution **x*** rounded to **4 decimal places**, one component per line.

Example:
```
Input:
3 1
1
1
1
1 2 3
Output:
2.0000
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\nb = list(map(float, input().split()))\n# Form ATA and ATb, then solve the normal equations (ATA is n×n)\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Find a **basis for the null space** of matrix A (solutions to Ax = 0) using RREF. Identify free variables and express pivot variables in terms of them.

Read `m n`, then `m` rows.

If the null space contains only the zero vector, print `trivial null space`. Otherwise print each basis vector (one per line), components rounded to **4 decimal places**, space-separated.

Example:
```
Input:
2 3
1 2 3
4 5 6
Output:
1.0000 -2.0000 1.0000
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\n# Compute RREF, identify pivot and free columns, build null space basis vectors\n",
                'time_limit_seconds'  => 1000,
                'base_xp'             => 350,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Solve an `n × n` system Ax = b using **Gaussian elimination with partial pivoting** (swap rows to put the largest absolute value in the pivot column at the top).

Read `n`, then `n` rows of the augmented matrix `[A|b]`.

If a unique solution exists, print each xᵢ rounded to **4 decimal places**, one per line (no label). Otherwise print `no unique solution`.

Example:
```
Input:
3
0 1 1 2
1 0 1 2
1 1 0 2
Output:
1.0000
1.0000
1.0000
```
MD,
                'starter_code'        => "n = int(input())\nM = [list(map(float, input().split())) for _ in range(n)]\n# Gaussian elimination with partial pivoting, then back substitution\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Apply **k iterations of the Jacobi method** to approximate the solution of Ax = b, starting from initial guess x₀.

At each iteration: `xᵢ_new = (bᵢ − Σⱼ≠ᵢ aᵢⱼ xⱼ_old) / aᵢᵢ`

Read `n`, then `n` rows of A, then b (n floats on one line), then x₀ (n floats on one line), then k (integer).

Print the result after k iterations, each component rounded to **4 decimal places**, one per line.

Example:
```
Input:
2
4 1
1 3
9 7
0 0
3
Output:
1.8542
1.7778
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\nb = list(map(float, input().split()))\nx = list(map(float, input().split()))\nk = int(input())\n# Perform k Jacobi iterations\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Matrix Invertibility, Rank & the Four Fundamental Subspaces (Q16–Q20) → L363
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Determine whether a vector **b** is in the **column space** of matrix A (i.e., Ax = b is consistent) by checking if `rank([A|b]) == rank(A)`.

Read `m n`, then `m` rows of A, then **b** (m floats on one line).

Print `yes` if b is in col(A), otherwise `no`.

Example:
```
Input:
3 2
1 0
0 1
0 0
1 2 0
Output:
yes
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\nb = list(map(float, input().split()))\n# Compare rank of A vs rank of [A|b]\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Compute the **orthogonal projection of vector b onto the column space of A**: `p = A(AᵀA)⁻¹Aᵀb`. This is the least-squares fitted value.

Read `m n`, then `m` rows of A (full column rank guaranteed), then **b** (m floats on one line).

Print **p** rounded to **4 decimal places**, one component per line.

Example:
```
Input:
3 1
1
1
1
1 2 3
Output:
2.0000
2.0000
2.0000
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\nb = list(map(float, input().split()))\n# Compute ATA, ATb, solve for x_ls, then p = A * x_ls\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Perform a **change of basis**: given n basis vectors (rows of matrix B in standard coordinates) and coordinates **v** expressed in that basis, compute the **standard coordinate representation**.

Formula: `v_std[j] = Σᵢ v[i] * B[i][j]`

Read `n`, then n rows of B (each row is a basis vector in standard coords), then **v** (n floats on one line).

Print `v_std` rounded to **4 decimal places**, space-separated on one line.

Example:
```
Input:
2
1 1
1 -1
2 1
Output:
3.0000 1.0000
```
MD,
                'starter_code'        => "n = int(input())\nB = [list(map(float, input().split())) for _ in range(n)]\nv = list(map(float, input().split()))\n# v_std[j] = sum(v[i]*B[i][j] for i in range(n))\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Compute the **component of vector u orthogonal to a subspace W** spanned by k vectors in R^n. First apply Gram-Schmidt to the k vectors to get an orthonormal basis, then subtract the projection of u onto each basis vector.

`u_perp = u − Σᵢ <u, eᵢ> eᵢ`

Read `n k`, then k rows (the spanning vectors), then **u** (n floats on one line).

Print `u_perp` rounded to **4 decimal places**, space-separated on one line.

Example:
```
Input:
3 1
1 0 0
2 3 4
Output:
0.0000 3.0000 4.0000
```
MD,
                'starter_code'        => "import math\nn, k = map(int, input().split())\nvecs = [list(map(float, input().split())) for _ in range(k)]\nu = list(map(float, input().split()))\n# Gram-Schmidt on vecs, then subtract projection of u onto each basis vector\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Verify the **rank-nullity theorem** and report all four fundamental subspace dimensions for an `m × n` matrix A.

Read `m n`, then `m` rows.

Print (one per line):
```
rank: r
nullity: n-r
row space dim: r
left null space dim: m-r
rank-nullity verified: yes
```

Example:
```
Input:
3 4
1 2 3 4
2 4 6 8
3 6 9 12
Output:
rank: 1
nullity: 3
row space dim: 1
left null space dim: 2
rank-nullity verified: yes
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\n# Compute rank via Gaussian elimination, derive all dimensions\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Determinants: Theory, Computation & Geometric Meaning (Q21–Q25) → L364
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Compute the **adjugate (classical adjoint)** of a **3 × 3 matrix** A. The adjugate is the transpose of the cofactor matrix. It satisfies `A · adj(A) = det(A) · I`.

Read 3 rows of 3 integers.

Print the adjugate matrix as integers, row by row, space-separated.

Example:
```
Input:
1 2 3
4 5 6
7 8 9
Output:
-3 6 -3
6 -12 6
-3 6 -3
```
MD,
                'starter_code'        => "A = [list(map(int, input().split())) for _ in range(3)]\n# Compute each cofactor Cij = (-1)^(i+j) * det(minor(A,i,j)), then transpose\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Verify the **multiplicative property of determinants**: `det(AB) = det(A) · det(B)` for two `n × n` matrices.

Read `n`, then `n` rows of **A**, then `n` rows of **B**.

Print (each rounded to **4 decimal places**):
```
det(A) = X
det(B) = Y
det(AB) = Z
verified
```
(Print `failed` instead of `verified` if the property does not hold within 1e-4 tolerance.)

Example:
```
Input:
2
1 2
3 4
5 6
7 8
Output:
det(A) = -2.0000
det(B) = -2.0000
det(AB) = 4.0000
verified
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\nB = [list(map(float, input().split())) for _ in range(n)]\n# Compute det(A), det(B), product AB, det(AB), compare\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Given `n` (matrix size), `d` (value of det(A)), and scalar `c`, compute the following **determinant identities** without constructing A:

- `det(cA) = c^n · det(A)`
- `det(Aᵀ) = det(A)`
- `det(A²) = det(A)²`
- `det(A⁻¹) = 1 / det(A)` (or `singular` if d = 0)

Read `n d c` on a single line (n is int, d and c are floats).

Print each result rounded to **4 decimal places** (or `singular` for A⁻¹ when det = 0):
```
det(cA) = X
det(AT) = Y
det(A2) = Z
det(A-1) = W
```

Example:
```
Input:
2 3 2
Output:
det(cA) = 12.0000
det(AT) = 3.0000
det(A2) = 9.0000
det(A-1) = 0.3333
```
MD,
                'starter_code'        => "parts = input().split()\nn, d, c = int(parts[0]), float(parts[1]), float(parts[2])\n# Apply each determinant identity formula\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Compute the **area of a triangle** in **R³** with vertices **A**, **B**, **C** using the cross product formula:

`area = 0.5 · ‖(B − A) × (C − A)‖`

Read three lines, each with 3 space-separated floats.

Print the area rounded to **4 decimal places**.

Example:
```
Input:
0 0 0
2 0 0
0 3 0
Output:
3.0000
```
MD,
                'starter_code'        => "import math\nA = list(map(float, input().split()))\nB = list(map(float, input().split()))\nC = list(map(float, input().split()))\n# Compute AB = B-A, AC = C-A, cross product, then 0.5 * norm\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
For a **block lower-triangular matrix** of the form:
```
M = | A  0 |
    | C  D |
```
the determinant equals `det(A) · det(D)`.

Read `p q` (sizes of A and D), then `p+q` rows each with `p+q` floats (the full matrix M). A occupies the top-left `p×p` block, D the bottom-right `q×q` block.

Print `det(A)`, `det(D)`, `det(M)` each rounded to **4 decimal places**, one per line with labels:
```
det(A) = X
det(D) = Y
det(M) = Z
```

Example:
```
Input:
2 2
1 2 0 0
3 4 0 0
0 0 5 6
0 0 7 8
Output:
det(A) = -2.0000
det(D) = -2.0000
det(M) = 4.0000
```
MD,
                'starter_code'        => "p, q = map(int, input().split())\nM = [list(map(float, input().split())) for _ in range(p + q)]\n# Extract A (top-left p×p), D (bottom-right q×q), compute determinants\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Eigenvalues & Eigenvectors (Q26–Q30) → L365
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Implement **power iteration** to find the dominant eigenvalue and its unit eigenvector.

At each step: compute `Ax`, normalise by L2 norm to get the new `x`, and use the Rayleigh quotient `xᵀAx` as the eigenvalue estimate.

Read `n`, then `n` rows of A, then initial vector `x0` (n floats on one line), then `k` (number of iterations).

Print:
```
lambda: X
eigenvector: v1 v2 ... vn
```
All values rounded to **4 decimal places**.

Example:
```
Input:
2
4 1
2 3
1 1
5
Output:
lambda: 5.0000
eigenvector: 0.7071 0.7071
```
MD,
                'starter_code'        => "import math\nn = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\nx = list(map(float, input().split()))\nk = int(input())\n# Perform k power iterations, then compute Rayleigh quotient\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Compute the **spectral radius** ρ(A) = max|λᵢ| of a **2 × 2 matrix** A. For complex eigenvalues, the modulus is `sqrt(real² + imag²)`.

Use the characteristic polynomial to find both eigenvalues exactly.

Read 2 rows of 2 floats.

Print the spectral radius rounded to **4 decimal places**.

Example:
```
Input:
4 1
2 3
Output:
5.0000
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Use trace and det to solve the characteristic quadratic; take max |λ|\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Apply **Gershgorin's Circle Theorem**: for each row i of an n×n matrix A, the disc has centre `aᵢᵢ` and radius `Rᵢ = Σⱼ≠ᵢ |aᵢⱼ|`. Every eigenvalue lies in at least one disc.

Read `n`, then `n` rows.

For each disc i (1-indexed), print:
```
disc i: center=X, radius=Y, bounds=[X-Y, X+Y]
```
All values rounded to **4 decimal places**.

Example:
```
Input:
2
4 1
2 3
Output:
disc 1: center=4.0000, radius=1.0000, bounds=[3.0000, 5.0000]
disc 2: center=3.0000, radius=2.0000, bounds=[1.0000, 5.0000]
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# For each row compute center and radius, then output bounds\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Compute the **characteristic polynomial** of a **3 × 3 symmetric matrix** A.

`p(λ) = λ³ − trace(A)·λ² + (M₁₁+M₂₂+M₃₃)·λ − det(A)`

where Mᵢᵢ is the determinant of the 2×2 submatrix obtained by deleting row i and column i.

Read 3 rows.

Print the four coefficients (for λ³, λ², λ, constant) rounded to **4 decimal places**, space-separated on one line.

Example:
```
Input:
1 0 0
0 2 0
0 0 3
Output:
1.0000 -6.0000 11.0000 -6.0000
```
MD,
                'starter_code'        => "A = [list(map(float, input().split())) for _ in range(3)]\n# Compute trace, sum of 2x2 principal minors, and determinant\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Compute **A^k mod (10⁹ + 7)** for a **2 × 2 integer matrix** and potentially large k using fast matrix exponentiation.

Read two rows of A (integers), then k on a third line.

Print the 2 × 2 result matrix mod (10⁹ + 7), row by row, space-separated.

Example:
```
Input:
1 2
0 1
4
Output:
1 8
0 1
```
MD,
                'starter_code'        => "MOD = 10**9 + 7\nrow1 = list(map(int, input().split()))\nrow2 = list(map(int, input().split()))\nk = int(input())\nA = [row1, row2]\n# Fast matrix exponentiation: square-and-multiply approach\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Diagonalization & Matrix Powers (Q31–Q35) → L366
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
**Diagonalize** a 2 × 2 matrix with distinct real eigenvalues. Find the two eigenvalues sorted in **descending order**, and the corresponding **unit eigenvectors** (normalised so the first non-zero component is positive).

Read 2 rows.

Print:
- Line 1: two eigenvalues (descending), rounded to 4 decimal places
- Lines 2–3: unit eigenvectors (one per line), rounded to 4 decimal places
- Line 4: `verified` (always, if computation is correct)

Example:
```
Input:
4 1
2 3
Output:
5.0000 2.0000
0.7071 0.7071
0.4472 -0.8944
verified
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Solve characteristic quadratic, find eigenvectors, normalise\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 350,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Find the **minimal polynomial** of a 2 × 2 matrix and determine if it is **diagonalizable**.

- If eigenvalues are distinct: minimal polynomial = characteristic polynomial (degree 2), diagonalizable.
- If a repeated eigenvalue λ: check if A = λI. If so, minimal polynomial = (λ − λ), degree 1, diagonalizable. Otherwise degree 2, not diagonalizable.

Read 2 rows.

Print:
- Line 1: degree of minimal polynomial
- Line 2: coefficients (highest to lowest), rounded to 4 decimal places, space-separated
- Line 3: `diagonalizable` or `not diagonalizable`

Example:
```
Input:
4 1
2 3
Output:
2
1.0000 -7.0000 10.0000
diagonalizable
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Compute trace and det to find eigenvalues; check discriminant and identity case\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 350,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Solve the **linear recurrence** `a_n = p · a_{n-1} + q · a_{n-2}` using **matrix exponentiation**:

`[[p, q], [1, 0]]^(n-1) · [a₁, a₀]ᵀ` gives `[a_n, a_{n-1}]ᵀ`

Read `p q` on line 1, `a0 a1` on line 2, `n` on line 3.

Print `a_n` as an integer.

Example:
```
Input:
1 1
0 1
10
Output:
55
```
MD,
                'starter_code'        => "p, q = map(int, input().split())\na0, a1 = map(int, input().split())\nn = int(input())\n# Build 2x2 transition matrix, raise to (n-1), multiply by [a1, a0]\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Two matrices A and B are **similar** (B = P⁻¹AP for some invertible P) if and only if they share the same characteristic polynomial **and** the same minimal polynomial.

Read 2 rows of A, then 2 rows of B.

Print `similar` or `not similar`.

Example:
```
Input:
1 2
3 4
4 2
3 1
Output:
similar
```
MD,
                'starter_code'        => "import math\nA = [list(map(float, input().split())) for _ in range(2)]\nB = [list(map(float, input().split())) for _ in range(2)]\n# Compare trace, det (char poly) and min poly for both matrices\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Compute the **matrix logarithm** of a 2 × 2 diagonalizable matrix A with positive real eigenvalues. Using eigendecomposition A = PDP⁻¹, the logarithm is `log(A) = P · diag(ln λ₁, ln λ₂) · P⁻¹`.

Read 2 rows (all eigenvalues guaranteed to be real and positive).

Print `log(A)` rounded to **4 decimal places**, row by row, space-separated.

Example:
```
Input:
4 0
0 9
Output:
1.3863 0.0000
0.0000 2.1972
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Find eigenvalues/eigenvectors, compute log of eigenvalues, reconstruct P*diag(log)*P_inv\n",
                'time_limit_seconds'  => 1000,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Orthogonality, Projections & Gram-Schmidt (Q36–Q40) → L367
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Compute the **QR decomposition** of an `n × n` matrix A using Gram-Schmidt. The columns of A are the vectors to orthonormalise.

Note: the input matrix is given row-by-row; its **columns** form the vectors to decompose.

Compute Q (orthonormal columns, given as an n×n matrix) and R = QᵀA (upper triangular).

Print Q row by row, then R row by row, each value rounded to **4 decimal places**, space-separated.

Example:
```
Input:
2
3 1
4 0
Output:
0.6000 0.8000
0.8000 -0.6000
5.0000 0.6000
0.0000 0.8000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Extract columns of A, apply Gram-Schmidt, build Q and compute R = Q^T * A\n",
                'time_limit_seconds'  => 1000,
                'base_xp'             => 350,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Solve the **least-squares problem** min ‖Ax − b‖ using **QR decomposition**: decompose A = QR, then solve Rx = Qᵀb by back substitution.

Read `m n` (m > n), then `m` rows of A, then **b** (m floats on one line).

Print **x*** rounded to **4 decimal places**, one component per line.

Example:
```
Input:
3 1
1
2
2
2 3 1
Output:
1.1111
```
MD,
                'starter_code'        => "import math\nm, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\nb = list(map(float, input().split()))\n# QR via Gram-Schmidt on columns of A, then solve Rx = Q^T b by back sub\n",
                'time_limit_seconds'  => 1000,
                'base_xp'             => 350,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Check if an `n × n` matrix Q is **orthogonal** (QᵀQ = I within 1e-6 tolerance). If it is, print `orthogonal` and then print Q⁻¹ = Qᵀ row by row. If not, print `not orthogonal`.

Read `n`, then `n` rows.

Example:
```
Input:
2
0.6 0.8
-0.8 0.6
Output:
orthogonal
0.6000 -0.8000
0.8000 0.6000
```
MD,
                'starter_code'        => "n = int(input())\nQ = [list(map(float, input().split())) for _ in range(n)]\n# Check if Q^T Q ≈ I, then print Q^T as the inverse\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Given matrix A of size `m × n` (full column rank) and vector **b**, compute the **projection matrix** `P = A(AᵀA)⁻¹Aᵀ` and apply it to **b** to get `p = Pb`.

Also compute the **residual** `r = b − p` and verify `Aᵀr ≈ 0` (normal equations hold).

Read `m n`, then `m` rows of A, then **b** (m floats on one line).

Print:
```
projection: p1 p2 ... pm
residual: r1 r2 ... rm
normal equations: satisfied
```
All values rounded to **4 decimal places**.

Example:
```
Input:
3 1
1
1
1
1 2 3
Output:
projection: 2.0000 2.0000 2.0000
residual: -1.0000 0.0000 1.0000
normal equations: satisfied
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\nb = list(map(float, input().split()))\n# Compute x_ls via normal equations, p = A*x_ls, r = b - p, check A^T * r ≈ 0\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Perform **one step of the QR algorithm**: decompose A = QR (via Gram-Schmidt), then compute `A₁ = RQ`. This shifts the eigenvalues toward the diagonal.

Read `n`, then `n` rows.

Print `A₁` rounded to **4 decimal places**, row by row, space-separated.

Example:
```
Input:
2
4 1
2 3
Output:
5.0000 0.0000
1.0000 2.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# QR decompose A (columns → Gram-Schmidt), then compute A1 = R * Q\n",
                'time_limit_seconds'  => 1000,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Singular Value Decomposition (SVD) (Q41–Q45) → L368
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Compute the **singular values** of a **2 × 2 matrix** A. Singular values are the square roots of the eigenvalues of `AᵀA`.

Read 2 rows. Print singular values sorted in **descending order**, each rounded to **4 decimal places**, one per line.

Example:
```
Input:
3 0
0 4
Output:
4.0000
3.0000
```
MD,
                'starter_code'        => "import math\nA = [list(map(float, input().split())) for _ in range(2)]\n# Compute A^T A, find eigenvalues via char poly, take square roots\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Given `r` singular values and a truncation rank `k`, compute the **rank-k approximation error** and **energy retained**.

- Error: `‖A − A_k‖_F = sqrt(σ_{k+1}² + ... + σ_r²)`
- Energy retained: `(σ₁² + ... + σ_k²) / (σ₁² + ... + σ_r²)`

Read `r k` on line 1, then the `r` singular values (descending order) on line 2.

Print:
```
error: X
energy: Y
```
Both rounded to **4 decimal places**.

Example:
```
Input:
3 1
5 3 1
Output:
error: 3.1623
energy: 0.7143
```
MD,
                'starter_code'        => "r, k = map(int, input().split())\nsingular = list(map(float, input().split()))\n# Compute Frobenius error for dropped singular values and energy fraction retained\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Compute the **Moore-Penrose pseudoinverse** A⁺ = V Σ⁺ Uᵀ from a given full SVD.

For a 2 × 2 matrix: Σ⁺ is the transpose of Σ with each nonzero σᵢ replaced by 1/σᵢ.

Read (for a 2 × 2 case):
- 2 rows of **U** (2 × 2)
- 1 line with 2 sigma values
- 2 rows of **Vᵀ** (2 × 2)

Print A⁺ rounded to **4 decimal places**, row by row, space-separated.

Example:
```
Input:
1 0
0 1
2 3
1 0
0 1
Output:
0.5000 0.0000
0.0000 0.3333
```
MD,
                'starter_code'        => "U = [list(map(float, input().split())) for _ in range(2)]\nsigma = list(map(float, input().split()))\nVT = [list(map(float, input().split())) for _ in range(2)]\n# V = VT^T, Sigma_plus = transpose with 1/sigma, then A+ = V * Sigma_plus * U^T\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Solve the **least-squares problem min ‖Ax − b‖** using the **pseudoinverse** x⁺ = A⁺b. Compute A⁺ from scratch (A⁺ = (AᵀA)⁻¹Aᵀ for full-column-rank A, or AᵀA is formed and solved).

Read `m n`, then `m` rows of A, then **b** (m floats on one line).

Print **x⁺** rounded to **4 decimal places**, one component per line.

Example:
```
Input:
3 2
1 0
0 1
0 0
1 2 3
Output:
1.0000
2.0000
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\nb = list(map(float, input().split()))\n# Compute pseudoinverse via normal equations; x+ = (A^T A)^{-1} A^T b\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Given a list of singular values (descending), compute the **effective rank**, **condition number**, and **energy fraction** at rank k.

- Effective rank: count of σᵢ > 1e-10
- Condition number: σ_max / σ_min_nonzero, or `infinite` if any σ = 0 and effective rank < total count
- Energy at k: (σ₁² + ... + σ_k²) / total energy

Read `r k` on line 1, then `r` sigma values on line 2.

Print:
```
effective rank: E
condition number: X (or infinite)
energy at k=K: Y
```
All floats rounded to **4 decimal places**.

Example:
```
Input:
3 2
5 3 1
Output:
effective rank: 3
condition number: 5.0000
energy at k=2: 0.9714
```
MD,
                'starter_code'        => "r, k = map(int, input().split())\nsingular = list(map(float, input().split()))\n# Count nonzero sigmas, compute condition number, compute energy fraction up to k\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Positive Definite Matrices & Quadratic Forms (Q46–Q50) → L369
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Compute the **Cholesky decomposition** A = LLᵀ of a symmetric **3 × 3** matrix, where L is lower triangular.

Use the Cholesky-Banachiewicz algorithm:
- `Lᵢᵢ = sqrt(Aᵢᵢ − Σₖ<ᵢ Lᵢₖ²)`
- `Lᵢⱼ = (Aᵢⱼ − Σₖ<ⱼ LᵢₖLⱼₖ) / Lⱼⱼ`

Read 3 rows. Print L rounded to **4 decimal places**, row by row, or `not positive definite` if a negative under the square root occurs.

Example:
```
Input:
4 2 2
2 10 5
2 5 15
Output:
2.0000 0.0000 0.0000
1.0000 3.0000 0.0000
1.0000 1.3333 3.4960
```
MD,
                'starter_code'        => "import math\nA = [list(map(float, input().split())) for _ in range(3)]\n# Apply the Cholesky-Banachiewicz formulas row by row\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 350,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Solve **Ax = b** for a symmetric positive definite `n × n` matrix using **Cholesky decomposition**: compute A = LLᵀ, solve Ly = b by forward substitution, then Lᵀx = y by back substitution.

Read `n` (2 or 3), then `n` rows of A, then **b** (n floats on one line).

Print **x** rounded to **4 decimal places**, one component per line.

Example:
```
Input:
2
4 2
2 3
10 9
Output:
1.5000
2.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\nb = list(map(float, input().split()))\n# Cholesky decompose A, forward sub for y, back sub for x\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 350,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Use the **Schur complement** to test if a block matrix M is positive definite.

For `M = [[A, B], [Bᵀ, D]]` (where A is p×p and D is q×q), M is PD iff A is PD **and** the Schur complement `S = D − BᵀA⁻¹B` is PD.

Read `p q`, then `(p+q)` rows of M.

Print:
```
A is PD: yes/no
Schur complement:
<S row by row, rounded to 4 dp>
M is PD: yes/no
```

Example:
```
Input:
1 2
4 2 1
2 3 1
1 1 2
Output:
A is PD: yes
Schur complement:
2.0000 0.5000
0.5000 1.7500
M is PD: yes
```
MD,
                'starter_code'        => "p, q = map(int, input().split())\nM = [list(map(float, input().split())) for _ in range(p + q)]\n# Extract A, B, D from M; check A PD; compute Schur S = D - B^T A^{-1} B; check S PD\n",
                'time_limit_seconds'  => 1000,
                'base_xp'             => 400,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Test **positive definiteness** of an `n × n` matrix using **Sylvester's criterion**: a symmetric matrix is PD iff all leading principal minors (M₁, M₂, ..., Mₙ) are strictly positive.

Read `n` (up to 4), then `n` rows.

Print each minor `Mᵢ = X` rounded to **4 decimal places**, then `positive definite` or `not positive definite`.

Example:
```
Input:
3
2 1 0
1 2 1
0 1 2
Output:
M1 = 2.0000
M2 = 3.0000
M3 = 4.0000
positive definite
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# For k=1..n, compute det of the top-left k×k submatrix; check all > 0\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
For a **symmetric 2 × 2 matrix** A, the quadratic form `f(x) = xᵀAx` on the unit sphere `‖x‖ = 1` achieves its minimum at the smallest eigenvalue and its maximum at the largest eigenvalue.

Read 2 rows of a symmetric matrix.

Print:
```
min eigenvalue: X
max eigenvalue: Y
classification: <type>
```
Where type is one of: `positive definite`, `positive semi-definite`, `negative definite`, `negative semi-definite`, or `indefinite`. All floats rounded to **4 decimal places**.

Example:
```
Input:
2 1
1 2
Output:
min eigenvalue: 1.0000
max eigenvalue: 3.0000
classification: positive definite
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Characteristic polynomial: solve λ^2 - trace*λ + det = 0 for min and max eigenvalue\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

        ];

        foreach ($questionDefs as $qDef) {
            DB::table('coding_questions')->insertOrIgnore([
                'challenge_id'        => $challenge->id,
                'order_index'         => $qDef['order_index'],
                'problem_description' => $qDef['problem_description'],
                'starter_code'        => $qDef['starter_code'],
                'time_limit_seconds'  => $qDef['time_limit_seconds'],
                'base_xp'             => $qDef['base_xp'],
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        // ─────────────────────────────────────────────────────────────────
        // 3. TEST CASES (4 per question: 2 visible + 2 hidden)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $seed = function (int $orderIndex, array $cases) use ($challenge) {
            $question = DB::table('coding_questions')
                ->where('challenge_id', $challenge->id)
                ->where('order_index', $orderIndex)
                ->first();

            if (! $question) {
                return;
            }

            foreach ($cases as $case) {
                DB::table('test_cases')->insertOrIgnore([
                    'coding_question_id' => $question->id,
                    'input'              => $case['input'],
                    'expected_output'    => $case['expected_output'],
                    'is_hidden'          => $case['is_hidden'],
                    'order_index'        => $case['order_index'],
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        };

        // ── Q1: Linear independence via rank ─────────────────────────────
        $seed(1, [
            ['input' => "2 2\n1 0\n0 1",              'expected_output' => "independent",             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 0\n0 1\n1 1",         'expected_output' => "dependent\nrank: 2",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3\n1 2 3\n2 4 6",          'expected_output' => "dependent\nrank: 1",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 3\n1 0 0\n0 1 0\n0 0 1",   'expected_output' => "independent",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Householder reflection ────────────────────────────────────
        $seed(2, [
            ['input' => "2\n3 4\n1 0",    'expected_output' => "-0.6000 -0.8000",         'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3 4\n0 1",    'expected_output' => "-0.8000 0.6000",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2 2 1\n1 0 0", 'expected_output' => "-0.6667 -0.6667 -0.3333", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n1 0 0\n2 3 4", 'expected_output' => "-2.0000 3.0000 4.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Distance from point to line in R3 ────────────────────────
        $seed(3, [
            ['input' => "0 0 0\n1 0 0\n0 1 0",     'expected_output' => "1.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0 0\n0 1 0\n4 3 0",      'expected_output' => "3.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0 0 0\n1 1 0\n1 0 0",      'expected_output' => "0.7071",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 2 3\n1 0 0\n5 6 7",      'expected_output' => "5.6569",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Gram-Schmidt on 3 vectors ─────────────────────────────────
        $seed(4, [
            ['input' => "3\n1 0 0\n1 1 0\n1 1 1",
             'expected_output' => "1.0000 0.0000 0.0000\n0.0000 1.0000 0.0000\n0.0000 0.0000 1.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1 0\n1 0 1\n0 1 1",
             'expected_output' => "0.7071 0.7071 0.0000\n0.4082 -0.4082 0.8165\n-0.5774 0.5774 0.5774",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0 0\n2 1 0\n3 2 1",
             'expected_output' => "1.0000 0.0000 0.0000\n0.0000 1.0000 0.0000\n0.0000 0.0000 1.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 0 0 0\n1 1 0 0\n1 1 1 0",
             'expected_output' => "1.0000 0.0000 0.0000 0.0000\n0.0000 1.0000 0.0000 0.0000\n0.0000 0.0000 1.0000 0.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Scalar triple product ──────────────────────────────────────
        $seed(5, [
            ['input' => "1 0 0\n0 1 0\n0 0 1",     'expected_output' => "triple: 1.0000\nvolume: 1.0000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3\n4 5 6\n7 8 9",      'expected_output' => "triple: 0.0000\nvolume: 0.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 0 0\n0 3 0\n0 0 4",      'expected_output' => "triple: 24.0000\nvolume: 24.0000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 1 0\n1 0 1\n0 1 1",      'expected_output' => "triple: -2.0000\nvolume: 2.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: LU decomposition ──────────────────────────────────────────
        $seed(6, [
            ['input' => "2\n4 3\n6 3",
             'expected_output' => "1.0000 0.0000\n1.5000 1.0000\n4.0000 3.0000\n0.0000 -1.5000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n3 4",
             'expected_output' => "1.0000 0.0000\n3.0000 1.0000\n1.0000 2.0000\n0.0000 -2.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2 4 -2\n4 9 -3\n-2 -3 7",
             'expected_output' => "1.0000 0.0000 0.0000\n2.0000 1.0000 0.0000\n-1.0000 1.0000 1.0000\n2.0000 4.0000 -2.0000\n0.0000 1.0000 1.0000\n0.0000 0.0000 4.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 1 1\n2 4 2\n3 9 2",
             'expected_output' => "1.0000 0.0000 0.0000\n2.0000 1.0000 0.0000\n3.0000 3.0000 1.0000\n1.0000 1.0000 1.0000\n0.0000 2.0000 0.0000\n0.0000 0.0000 -1.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Solve Ax=b via LU ─────────────────────────────────────────
        $seed(7, [
            ['input' => "2\n4 3\n6 3\n10 12",       'expected_output' => "1.0000\n2.0000",              'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n3 4\n5 6",          'expected_output' => "-4.0000\n4.5000",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2 4 -2\n4 9 -3\n-2 -3 7\n2 8 10",
             'expected_output' => "-1.0000\n2.0000\n2.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 1 1\n2 4 2\n3 9 2\n6 10 10",
             'expected_output' => "5.0000\n-1.0000\n2.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Matrix inverse via Gauss-Jordan ───────────────────────────
        $seed(8, [
            ['input' => "2\n2 1\n4 3",
             'expected_output' => "1.5000 -0.5000\n-2.0000 1.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n2 4",
             'expected_output' => "singular matrix",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0 0\n0 2 0\n0 0 4",
             'expected_output' => "1.0000 0.0000 0.0000\n0.0000 0.5000 0.0000\n0.0000 0.0000 0.2500",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2 1 0\n1 3 1\n0 1 2",
             'expected_output' => "0.6250 -0.2500 0.1250\n-0.2500 0.5000 -0.2500\n0.1250 -0.2500 0.6250",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Matrix exponentiation A^k mod (10^9+7) ───────────────────
        $seed(9, [
            ['input' => "1 1\n1 0\n10",      'expected_output' => "89 55\n55 34",            'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 0\n0 3\n10",      'expected_output' => "1024 0\n0 59049",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 1\n1 0\n30",      'expected_output' => "1346269 832040\n832040 514229", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 2\n0 1\n1000000", 'expected_output' => "1 2000000\n0 1",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Commutator [A,B] ─────────────────────────────────────────
        $seed(10, [
            ['input' => "2\n1 0\n0 1\n2 3\n4 5",      'expected_output' => "commutes",                          'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n0 0\n0 1\n1 0",       'expected_output' => "does not commute\n2 1\n-1 -2",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n2 0\n0 3\n4 0\n0 5",       'expected_output' => "commutes",                          'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 1\n0 1\n1 0\n1 1",       'expected_output' => "does not commute\n1 0\n-1 -1",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: RREF ─────────────────────────────────────────────────────
        $seed(11, [
            ['input' => "2 3\n1 2 3\n4 5 6",
             'expected_output' => "1.0000 0.0000 -1.0000\n0.0000 1.0000 2.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 3\n2 4 6\n1 2 3",
             'expected_output' => "1.0000 2.0000 3.0000\n0.0000 0.0000 0.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 4\n1 2 0 3\n2 4 1 7\n3 6 1 10",
             'expected_output' => "1.0000 2.0000 0.0000 3.0000\n0.0000 0.0000 1.0000 1.0000\n0.0000 0.0000 0.0000 0.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 4\n1 0 1 2\n0 1 1 3\n1 1 2 5",
             'expected_output' => "1.0000 0.0000 1.0000 2.0000\n0.0000 1.0000 1.0000 3.0000\n0.0000 0.0000 0.0000 0.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Least squares via normal equations ───────────────────────
        $seed(12, [
            ['input' => "3 1\n1\n1\n1\n1 2 3",       'expected_output' => "2.0000",              'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 1\n1 2\n1 3\n1 2 2",  'expected_output' => "0.6667\n0.5000",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 2\n1 0\n0 1\n1 1\n1 -1\n2 1 3 -1",
             'expected_output' => "1.0000\n1.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 2\n2 1\n1 2\n1 1\n3 3 2",
             'expected_output' => "1.0000\n1.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Null space basis ─────────────────────────────────────────
        $seed(13, [
            ['input' => "2 3\n1 2 3\n4 5 6",           'expected_output' => "1.0000 -2.0000 1.0000",                               'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n1 0\n0 1",                'expected_output' => "trivial null space",                                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 4\n1 2 0 1\n0 0 1 2",
             'expected_output' => "-2.0000 1.0000 0.0000 0.0000\n-1.0000 0.0000 -2.0000 1.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 3\n1 2 3\n4 5 6\n7 8 9",    'expected_output' => "1.0000 -2.0000 1.0000",                               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Gaussian elimination with partial pivoting ───────────────
        $seed(14, [
            ['input' => "3\n0 1 1 2\n1 0 1 2\n1 1 0 2",  'expected_output' => "1.0000\n1.0000\n1.0000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2 3 1 6\n4 1 2 7\n1 2 5 8",  'expected_output' => "1.0000\n1.0000\n1.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3 14\n4 5 6 32\n1 1 1 4",
             'expected_output' => "no unique solution",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0 2 1 5\n1 -1 0 0\n2 3 1 8",
             'expected_output' => "1.0000\n1.0000\n3.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Jacobi iteration ─────────────────────────────────────────
        $seed(15, [
            ['input' => "2\n4 1\n1 3\n9 7\n0 0\n3",    'expected_output' => "1.8542\n1.7778",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n4 1\n1 3\n9 7\n0 0\n1",    'expected_output' => "2.2500\n2.3333",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n10 1 1\n1 10 1\n1 1 10\n12 12 12\n0 0 0\n5",
             'expected_output' => "1.0000\n1.0000\n1.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n4 0\n0 5\n8 10\n0 0\n4",   'expected_output' => "2.0000\n2.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Is b in col(A)? ──────────────────────────────────────────
        $seed(16, [
            ['input' => "3 2\n1 0\n0 1\n0 0\n1 2 0",   'expected_output' => "yes",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 0\n0 1\n0 0\n1 2 1",   'expected_output' => "no",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 3\n1 2 3\n4 5 6\n7 8 9\n1 2 3", 'expected_output' => "yes",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 3\n1 2 3\n4 5 6\n7 8 9\n0 1 0", 'expected_output' => "no",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Projection onto col(A) ───────────────────────────────────
        $seed(17, [
            ['input' => "3 1\n1\n1\n1\n1 2 3",
             'expected_output' => "2.0000\n2.0000\n2.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 0\n0 1\n0 0\n1 2 3",
             'expected_output' => "1.0000\n2.0000\n0.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2\n1 1\n1 0\n0 1\n2 1 1",
             'expected_output' => "2.0000\n1.0000\n1.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 2\n1 0\n0 1\n1 1\n1 -1\n2 1 3 1",
             'expected_output' => "2.0000\n1.0000\n3.0000\n1.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Change of basis ──────────────────────────────────────────
        $seed(18, [
            ['input' => "2\n1 0\n0 1\n2 3",         'expected_output' => "2.0000 3.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 1\n1 -1\n2 1",         'expected_output' => "3.0000 1.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0 0\n0 1 0\n0 0 1\n5 3 2", 'expected_output' => "5.0000 3.0000 2.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n2 0\n0 3\n1 2",          'expected_output' => "2.0000 6.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Orthogonal complement projection ─────────────────────────
        $seed(19, [
            ['input' => "3 1\n1 0 0\n2 3 4",          'expected_output' => "0.0000 3.0000 4.0000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 0 0\n0 1 0\n3 4 5",   'expected_output' => "0.0000 0.0000 5.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2\n1 1 0\n0 1 1\n1 2 3",   'expected_output' => "0.6667 -0.6667 0.6667", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 2\n1 0 0 0\n0 1 0 0\n1 2 3 4", 'expected_output' => "0.0000 0.0000 3.0000 4.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q20: Rank-nullity theorem ─────────────────────────────────────
        $seed(20, [
            ['input' => "3 4\n1 2 3 4\n2 4 6 8\n3 6 9 12",
             'expected_output' => "rank: 1\nnullity: 3\nrow space dim: 1\nleft null space dim: 2\nrank-nullity verified: yes",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 3\n1 0 0\n0 1 0\n0 0 1",
             'expected_output' => "rank: 3\nnullity: 0\nrow space dim: 3\nleft null space dim: 0\nrank-nullity verified: yes",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 3\n1 2 3\n0 1 2\n0 0 1\n0 0 0",
             'expected_output' => "rank: 3\nnullity: 0\nrow space dim: 3\nleft null space dim: 1\nrank-nullity verified: yes",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 5\n1 0 2 0 3\n0 1 0 2 0\n0 0 0 0 0",
             'expected_output' => "rank: 2\nnullity: 3\nrow space dim: 2\nleft null space dim: 1\nrank-nullity verified: yes",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Adjugate of 3x3 ──────────────────────────────────────────
        $seed(21, [
            ['input' => "1 2 3\n4 5 6\n7 8 9",      'expected_output' => "-3 6 -3\n6 -12 6\n-3 6 -3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 0 0\n0 3 0\n0 0 4",       'expected_output' => "12 0 0\n0 8 0\n0 0 6",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 0 1\n0 1 0\n1 0 0",       'expected_output' => "0 0 -1\n0 -1 0\n-1 0 1",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 1 2\n2 1 1\n1 0 1",       'expected_output' => "1 -1 -1\n-1 1 1\n-1 1 1",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Verify det(AB)=det(A)*det(B) ────────────────────────────
        $seed(22, [
            ['input' => "2\n1 2\n3 4\n5 6\n7 8",
             'expected_output' => "det(A) = -2.0000\ndet(B) = -2.0000\ndet(AB) = 4.0000\nverified",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2 0\n0 3\n4 0\n0 5",
             'expected_output' => "det(A) = 6.0000\ndet(B) = 20.0000\ndet(AB) = 120.0000\nverified",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3\n0 1 4\n5 6 0\n1 0 0\n0 1 0\n0 0 1",
             'expected_output' => "det(A) = 1.0000\ndet(B) = 1.0000\ndet(AB) = 1.0000\nverified",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 0 0\n2 1 0\n3 0 1\n1 2 3\n0 1 0\n0 0 1",
             'expected_output' => "det(A) = 1.0000\ndet(B) = 1.0000\ndet(AB) = 1.0000\nverified",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Determinant identities ────────────────────────────────────
        $seed(23, [
            ['input' => "2 3 2",
             'expected_output' => "det(cA) = 12.0000\ndet(AT) = 3.0000\ndet(A2) = 9.0000\ndet(A-1) = 0.3333",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 4 -1",
             'expected_output' => "det(cA) = -4.0000\ndet(AT) = 4.0000\ndet(A2) = 16.0000\ndet(A-1) = 0.2500",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 0 5",
             'expected_output' => "det(cA) = 0.0000\ndet(AT) = 0.0000\ndet(A2) = 0.0000\ndet(A-1) = singular",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 2 3",
             'expected_output' => "det(cA) = 162.0000\ndet(AT) = 2.0000\ndet(A2) = 4.0000\ndet(A-1) = 0.5000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Area of triangle ──────────────────────────────────────────
        $seed(24, [
            ['input' => "0 0 0\n1 0 0\n0 1 0",      'expected_output' => "0.5000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 0 0\n2 0 0\n0 3 0",      'expected_output' => "3.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 1 0\n4 1 0\n1 5 0",      'expected_output' => "6.0000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0 0 0\n1 1 0\n0 1 1",      'expected_output' => "0.8660",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Block diagonal determinant ───────────────────────────────
        $seed(25, [
            ['input' => "2 2\n1 2 0 0\n3 4 0 0\n0 0 5 6\n0 0 7 8",
             'expected_output' => "det(A) = -2.0000\ndet(D) = -2.0000\ndet(M) = 4.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2\n3 0 0\n0 1 2\n0 4 5",
             'expected_output' => "det(A) = 3.0000\ndet(D) = -3.0000\ndet(M) = -9.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 1\n2 1 0\n0 3 0\n0 0 4",
             'expected_output' => "det(A) = 6.0000\ndet(D) = 4.0000\ndet(M) = 24.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n1 0 0 0\n0 2 0 0\n0 0 2 1\n0 0 1 3",
             'expected_output' => "det(A) = 2.0000\ndet(D) = 5.0000\ndet(M) = 10.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Power iteration ──────────────────────────────────────────
        $seed(26, [
            ['input' => "2\n4 1\n2 3\n1 1\n5",   'expected_output' => "lambda: 5.0000\neigenvector: 0.7071 0.7071",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5 0\n0 2\n1 0\n5",   'expected_output' => "lambda: 5.0000\neigenvector: 1.0000 0.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n6 0 0\n0 2 0\n0 0 1\n1 0 0\n5",
             'expected_output' => "lambda: 6.0000\neigenvector: 1.0000 0.0000 0.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n4 1\n2 3\n1 0\n20",  'expected_output' => "lambda: 5.0000\neigenvector: 0.7071 0.7071",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Spectral radius ──────────────────────────────────────────
        $seed(27, [
            ['input' => "4 1\n2 3",    'expected_output' => "5.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5 0\n0 0.3", 'expected_output' => "0.5000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2\n2 1",    'expected_output' => "3.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 -1\n1 2",   'expected_output' => "2.2361",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Gershgorin circles ───────────────────────────────────────
        $seed(28, [
            ['input' => "2\n4 1\n2 3",
             'expected_output' => "disc 1: center=4.0000, radius=1.0000, bounds=[3.0000, 5.0000]\ndisc 2: center=3.0000, radius=2.0000, bounds=[1.0000, 5.0000]",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10 1 1\n1 5 1\n1 1 2",
             'expected_output' => "disc 1: center=10.0000, radius=2.0000, bounds=[8.0000, 12.0000]\ndisc 2: center=5.0000, radius=2.0000, bounds=[3.0000, 7.0000]\ndisc 3: center=2.0000, radius=2.0000, bounds=[0.0000, 4.0000]",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n4 1 0\n1 3 1\n0 1 2",
             'expected_output' => "disc 1: center=4.0000, radius=1.0000, bounds=[3.0000, 5.0000]\ndisc 2: center=3.0000, radius=2.0000, bounds=[1.0000, 5.0000]\ndisc 3: center=2.0000, radius=1.0000, bounds=[1.0000, 3.0000]",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n6 2 0 1\n2 8 1 0\n0 1 4 1\n1 0 1 5",
             'expected_output' => "disc 1: center=6.0000, radius=3.0000, bounds=[3.0000, 9.0000]\ndisc 2: center=8.0000, radius=3.0000, bounds=[5.0000, 11.0000]\ndisc 3: center=4.0000, radius=2.0000, bounds=[2.0000, 6.0000]\ndisc 4: center=5.0000, radius=2.0000, bounds=[3.0000, 7.0000]",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Characteristic polynomial of 3x3 ─────────────────────────
        $seed(29, [
            ['input' => "1 0 0\n0 2 0\n0 0 3",   'expected_output' => "1.0000 -6.0000 11.0000 -6.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 1 0\n1 3 1\n0 1 2",   'expected_output' => "1.0000 -9.0000 24.0000 -18.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 1 0\n1 2 1\n0 1 2",   'expected_output' => "1.0000 -6.0000 10.0000 -4.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 2 3\n2 5 4\n3 4 9",   'expected_output' => "1.0000 -15.0000 30.0000 4.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: A^k mod (10^9+7) for 2x2 ───────────────────────────────
        $seed(30, [
            ['input' => "1 1\n1 0\n10",       'expected_output' => "89 55\n55 34",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2\n0 1\n4",        'expected_output' => "1 8\n0 1",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 1\n1 0\n30",       'expected_output' => "1346269 832040\n832040 514229", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 2\n0 1\n1000000",  'expected_output' => "1 2000000\n0 1",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Full diagonalization ──────────────────────────────────────
        $seed(31, [
            ['input' => "4 1\n2 3",
             'expected_output' => "5.0000 2.0000\n0.7071 0.7071\n0.4472 -0.8944\nverified",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 0\n0 1",
             'expected_output' => "3.0000 1.0000\n1.0000 0.0000\n0.0000 1.0000\nverified",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 4\n1 2",
             'expected_output' => "4.0000 0.0000\n0.8944 0.4472\n0.8944 -0.4472\nverified",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 4\n1 2",
             'expected_output' => "6.0000 1.0000\n0.9701 0.2425\n0.7071 -0.7071\nverified",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Minimal polynomial ────────────────────────────────────────
        $seed(32, [
            ['input' => "4 1\n2 3",
             'expected_output' => "2\n1.0000 -7.0000 10.0000\ndiagonalizable",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 1\n0 2",
             'expected_output' => "2\n1.0000 -4.0000 4.0000\nnot diagonalizable",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 0\n0 3",
             'expected_output' => "1\n1.0000 -3.0000\ndiagonalizable",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 4\n1 2",
             'expected_output' => "2\n1.0000 -7.0000 6.0000\ndiagonalizable",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Linear recurrence via matrix method ──────────────────────
        $seed(33, [
            ['input' => "1 1\n0 1\n10",    'expected_output' => "55",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1\n0 1\n20",    'expected_output' => "6765",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 -1\n1 2\n10",   'expected_output' => "11",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 2\n1 1\n6",     'expected_output' => "43",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Similarity of matrices ───────────────────────────────────
        $seed(34, [
            ['input' => "1 2\n3 4\n4 2\n3 1",    'expected_output' => "similar",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0\n0 2\n1 0\n0 3",    'expected_output' => "not similar",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 1\n0 3\n3 0\n0 3",    'expected_output' => "not similar",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0 1\n1 0\n1 0\n0 -1",   'expected_output' => "similar",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Matrix logarithm ──────────────────────────────────────────
        $seed(35, [
            ['input' => "1 0\n0 1",
             'expected_output' => "0.0000 0.0000\n0.0000 0.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 0\n0 9",
             'expected_output' => "1.3863 0.0000\n0.0000 2.1972",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 0\n0 2",
             'expected_output' => "0.6931 0.0000\n0.0000 0.6931",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 1\n2 3",
             'expected_output' => "1.3040 0.3054\n0.6109 0.9986",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: QR decomposition ──────────────────────────────────────────
        $seed(36, [
            ['input' => "2\n3 1\n4 0",
             'expected_output' => "0.6000 0.8000\n0.8000 -0.6000\n5.0000 0.6000\n0.0000 0.8000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1",
             'expected_output' => "1.0000 0.0000\n0.0000 1.0000\n1.0000 0.0000\n0.0000 1.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n3 0\n4 5",
             'expected_output' => "0.6000 -0.8000\n0.8000 0.6000\n5.0000 4.0000\n0.0000 3.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n2 1\n2 3",
             'expected_output' => "0.7071 -0.7071\n0.7071 0.7071\n2.8284 2.8284\n0.0000 1.4142",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Least squares via QR ──────────────────────────────────────
        $seed(37, [
            ['input' => "3 1\n1\n2\n2\n2 3 1",          'expected_output' => "1.1111",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 1\n1 2\n1 3\n1 2 2",     'expected_output' => "0.6667\n0.5000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 2\n1 1\n1 2\n1 3\n1 4\n2 3 5 7",
             'expected_output' => "0.5000\n1.5000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 2\n1 0\n0 1\n1 1\n2 3 4",
             'expected_output' => "1.6667\n2.6667",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Orthogonal matrix check ──────────────────────────────────
        $seed(38, [
            ['input' => "2\n0.6 0.8\n-0.8 0.6",
             'expected_output' => "orthogonal\n0.6000 -0.8000\n0.8000 0.6000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 1\n0 1",
             'expected_output' => "not orthogonal",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0 0\n0 0 -1\n0 1 0",
             'expected_output' => "orthogonal\n1.0000 0.0000 0.0000\n0.0000 0.0000 1.0000\n0.0000 -1.0000 0.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0.7071 0.7071\n0.7071 -0.7071",
             'expected_output' => "orthogonal\n0.7071 0.7071\n0.7071 -0.7071",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Projection matrix and residual ───────────────────────────
        $seed(39, [
            ['input' => "3 1\n1\n1\n1\n1 2 3",
             'expected_output' => "projection: 2.0000 2.0000 2.0000\nresidual: -1.0000 0.0000 1.0000\nnormal equations: satisfied",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n1 0\n0 1\n0 0\n3 4 5",
             'expected_output' => "projection: 3.0000 4.0000 0.0000\nresidual: 0.0000 0.0000 5.0000\nnormal equations: satisfied",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 2\n1 0\n0 1\n1 1\n1 -1\n2 1 3 1",
             'expected_output' => "projection: 2.0000 1.0000 3.0000 1.0000\nresidual: 0.0000 0.0000 0.0000 0.0000\nnormal equations: satisfied",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 1\n1\n2\n2\n2 3 1",
             'expected_output' => "projection: 1.2222 2.4444 2.4444\nresidual: 0.7778 0.5556 -1.4444\nnormal equations: satisfied",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: QR algorithm step (A1 = RQ) ──────────────────────────────
        $seed(40, [
            ['input' => "2\n4 1\n2 3",
             'expected_output' => "5.0000 0.0000\n1.0000 2.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3 0\n0 3",
             'expected_output' => "3.0000 0.0000\n0.0000 3.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n2 1\n1 2",
             'expected_output' => "2.8000 0.6000\n0.6000 1.2000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 2\n0 3",
             'expected_output' => "1.0000 2.0000\n0.0000 3.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Singular values of 2x2 ──────────────────────────────────
        $seed(41, [
            ['input' => "3 0\n0 4",     'expected_output' => "4.0000\n3.0000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1\n0 0",     'expected_output' => "1.4142\n0.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 4\n0 5",     'expected_output' => "6.7082\n2.2361",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 2\n3 4",     'expected_output' => "5.4650\n0.3660",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Truncated SVD error and energy ───────────────────────────
        $seed(42, [
            ['input' => "3 1\n5 3 1",          'expected_output' => "error: 3.1623\nenergy: 0.7143",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 1\n4 3",            'expected_output' => "error: 3.0000\nenergy: 0.6400",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 2\n10 5 2 1",       'expected_output' => "error: 2.2361\nenergy: 0.9615",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 2\n6 4 1",          'expected_output' => "error: 1.0000\nenergy: 0.9811",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Pseudoinverse from SVD ───────────────────────────────────
        $seed(43, [
            ['input' => "1 0\n0 1\n2 3\n1 0\n0 1",
             'expected_output' => "0.5000 0.0000\n0.0000 0.3333",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 -1\n-1 0\n3 2\n0 -1\n-1 0",
             'expected_output' => "0.0000 0.3333\n0.5000 0.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 0\n0 1\n4 0\n1 0\n0 1",
             'expected_output' => "0.2500 0.0000\n0.0000 0.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.7071 0.7071\n0.7071 -0.7071\n3 1\n0.7071 0.7071\n0.7071 -0.7071",
             'expected_output' => "0.6667 -0.3333\n-0.3333 0.6667",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Least squares via pseudoinverse ──────────────────────────
        $seed(44, [
            ['input' => "3 2\n1 0\n0 1\n0 0\n1 2 3",
             'expected_output' => "1.0000\n2.0000",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 3\n1 0 0\n0 1 0\n3 4",
             'expected_output' => "3.0000\n4.0000\n0.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 1\n1\n2\n2\n2 3 1",
             'expected_output' => "1.1111",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 2\n1 1\n1 2\n1 3\n1 2 3",
             'expected_output' => "0.0000\n1.0000",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Effective rank, condition number, energy ─────────────────
        $seed(45, [
            ['input' => "3 2\n5 3 1",
             'expected_output' => "effective rank: 3\ncondition number: 5.0000\nenergy at k=2: 0.9714",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 1\n6 0 0",
             'expected_output' => "effective rank: 1\ncondition number: infinite\nenergy at k=1: 1.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 2\n10 4 2 0",
             'expected_output' => "effective rank: 3\ncondition number: infinite\nenergy at k=2: 0.9667",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 3\n8 6 4 2",
             'expected_output' => "effective rank: 4\ncondition number: 4.0000\nenergy at k=3: 0.9667",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Cholesky decomposition of 3x3 ───────────────────────────
        $seed(46, [
            ['input' => "4 2 2\n2 10 5\n2 5 15",
             'expected_output' => "2.0000 0.0000 0.0000\n1.0000 3.0000 0.0000\n1.0000 1.3333 3.4960",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "9 0 0\n0 4 0\n0 0 1",
             'expected_output' => "3.0000 0.0000 0.0000\n0.0000 2.0000 0.0000\n0.0000 0.0000 1.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 -2 2\n-2 2 -4\n2 -4 11",
             'expected_output' => "2.0000 0.0000 0.0000\n-1.0000 1.0000 0.0000\n1.0000 -3.0000 1.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 2 3\n2 8 12\n3 12 20",
             'expected_output' => "1.0000 0.0000 0.0000\n2.0000 2.0000 0.0000\n3.0000 3.0000 1.4142",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Solve Ax=b via Cholesky ──────────────────────────────────
        $seed(47, [
            ['input' => "2\n4 2\n2 3\n10 9",          'expected_output' => "1.5000\n2.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n4 2 2\n2 10 5\n2 5 15\n8 17 22",
             'expected_output' => "1.0000\n1.0000\n1.0000",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n4 -2 2\n-2 2 -4\n2 -4 11\n4 -4 9",
             'expected_output' => "1.0000\n1.0000\n1.0000",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n9 3\n3 2\n12 7",           'expected_output' => "0.3333\n3.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Schur complement block PD test ───────────────────────────
        $seed(48, [
            ['input' => "1 2\n4 2 1\n2 3 1\n1 1 2",
             'expected_output' => "A is PD: yes\nSchur complement:\n2.0000 0.5000\n0.5000 1.7500\nM is PD: yes",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 1\n4 2 1\n2 3 1\n1 1 1",
             'expected_output' => "A is PD: yes\nSchur complement:\n0.6250\nM is PD: yes",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2\n1 3 4\n3 10 5\n4 5 10",
             'expected_output' => "A is PD: yes\nSchur complement:\n1.0000 -7.0000\n-7.0000 -6.0000\nM is PD: no",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n4 0 1 1\n0 4 1 1\n1 1 3 0\n1 1 0 3",
             'expected_output' => "A is PD: yes\nSchur complement:\n2.5000 -0.5000\n-0.5000 2.5000\nM is PD: yes",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Sylvester's criterion ─────────────────────────────────────
        $seed(49, [
            ['input' => "3\n2 1 0\n1 2 1\n0 1 2",
             'expected_output' => "M1 = 2.0000\nM2 = 3.0000\nM3 = 4.0000\npositive definite",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n2 5 4\n3 4 1",
             'expected_output' => "M1 = 1.0000\nM2 = 1.0000\nM3 = -12.0000\nnot positive definite",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4 2 0 0\n2 3 1 0\n0 1 2 1\n0 0 1 2",
             'expected_output' => "M1 = 4.0000\nM2 = 8.0000\nM3 = 12.0000\nM4 = 36.0000\npositive definite",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 0 2\n0 2 0\n2 0 1",
             'expected_output' => "M1 = 1.0000\nM2 = 2.0000\nM3 = -6.0000\nnot positive definite",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Quadratic form min/max on unit sphere ────────────────────
        $seed(50, [
            ['input' => "2 1\n1 2",
             'expected_output' => "min eigenvalue: 1.0000\nmax eigenvalue: 3.0000\nclassification: positive definite",
             'is_hidden' => false, 'order_index' => 1],
            ['input' => "5 0\n0 3",
             'expected_output' => "min eigenvalue: 3.0000\nmax eigenvalue: 5.0000\nclassification: positive definite",
             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 1\n1 3",
             'expected_output' => "min eigenvalue: 2.0000\nmax eigenvalue: 4.0000\nclassification: positive definite",
             'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 2\n2 1",
             'expected_output' => "min eigenvalue: -1.0000\nmax eigenvalue: 3.0000\nclassification: indefinite",
             'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 9 Coding (Advanced) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}