<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 9 — Linear Algebra (Newbie / Level 1) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering beginner Linear Algebra concepts
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
 * Difficulty: Newbie — all problems solvable with pure Python, no third-party
 * libraries required. Learners build intuition for core linear algebra concepts
 * through direct computation.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module9CodingChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (! $category) {
            $this->command->error('Newbie category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 9 — Linear Algebra (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Linear Algebra',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Explore the fundamentals of Linear Algebra using pure Python. Work with vectors and scalars, perform matrix operations, solve systems of linear equations via Gaussian elimination, compute determinants, find eigenvalues and eigenvectors, apply orthogonality and projections, and interpret SVD and quadratic forms — all from scratch.',
                'time_limit_seconds' => 1200,
                'base_xp'            => 800,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Vectors, Scalars & the Geometry of Linear Algebra (Q1–Q5) → L360
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Read two vectors **u** and **v** of length `n` and a scalar `c`. Compute and print:
- Line 1: `u + v` (element-wise addition), space-separated
- Line 2: `c * u` (scalar multiplication), space-separated

All values are integers.

Example:
```
Input:
3
1 2 3
4 5 6
2
Output:
5 7 9
2 4 6
```
MD,
                'starter_code'        => "n = int(input())\nu = list(map(int, input().split()))\nv = list(map(int, input().split()))\nc = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Compute the **dot product** of two vectors **u** and **v** of length `n`. Read `n`, then the two vectors. Print the dot product as an integer.

Example:
```
Input:
3
1 2 3
4 5 6
Output:
32
```
MD,
                'starter_code'        => "n = int(input())\nu = list(map(int, input().split()))\nv = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Compute the **magnitude (Euclidean norm)** of a vector **v** of length `n`. Read `n` and the vector. Print the magnitude rounded to 4 decimal places.

Example:
```
Input:
3
3 4 0
Output:
5.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nv = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Compute the **angle in degrees** between two non-zero vectors **u** and **v** of length `n` using the dot product formula. Read `n`, then the two vectors. Print the angle rounded to 4 decimal places.

Recall: `cos(θ) = (u · v) / (|u| * |v|)`

Example:
```
Input:
2
1 0
0 1
Output:
90.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nu = list(map(int, input().split()))\nv = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Given two 3-dimensional vectors **u** and **v**, compute their **cross product**. Read `u` and `v` (each as 3 space-separated integers). Print the resulting 3D vector, space-separated.

Recall: `u × v = (u2*v3 - u3*v2, u3*v1 - u1*v3, u1*v2 - u2*v1)`

Example:
```
Input:
1 0 0
0 1 0
Output:
0 0 1
```
MD,
                'starter_code'        => "u = list(map(int, input().split()))\nv = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Matrix Fundamentals: Operations & Special Structures (Q6–Q10) → L361
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Read two `n x n` matrices **A** and **B** and print their **sum** (element-wise). Each matrix is given row by row, each row space-separated. Print the result matrix in the same format.

Example:
```
Input:
2
1 2
3 4
5 6
7 8
Output:
6 8
10 12
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(int, input().split())) for _ in range(n)]\nB = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Read an `n x n` matrix **A** and print its **transpose**. Each row of the input is space-separated. Print the transposed matrix in the same format.

Example:
```
Input:
3
1 2 3
4 5 6
7 8 9
Output:
1 4 7
2 5 8
3 6 9
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Read two matrices: **A** of size `m x n` and **B** of size `n x p`. Compute and print their **matrix product** **C = A * B**. Print `C` row by row, each row space-separated.

Read `m n p`, then `m` rows of `A`, then `n` rows of `B`.

Example:
```
Input:
2 3 2
1 2 3
4 5 6
7 8
9 10
11 12
Output:
58 64
139 154
```
MD,
                'starter_code'        => "m, n, p = map(int, input().split())\nA = [list(map(int, input().split())) for _ in range(m)]\nB = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read an `n x n` matrix and determine its **special structure**. Print all that apply (one per line, in this order):
- `symmetric` if A == A^T
- `identity` if it is the identity matrix
- `diagonal` if all off-diagonal elements are zero
- `zero` if all elements are zero

If none apply, print `none`.

Example:
```
Input:
3
1 0 0
0 1 0
0 0 1
Output:
symmetric
identity
diagonal
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read an `n x n` matrix **A** and a scalar `c`. Print the result of **scalar multiplication** `c * A`. Each row space-separated.

Example:
```
Input:
2
1 2
3 4
3
Output:
3 6
9 12
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(int, input().split())) for _ in range(n)]\nc = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Systems of Linear Equations & Gaussian Elimination (Q11–Q15) → L362
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Solve a **2x2 system of linear equations** using substitution or Cramer's rule:
```
a1*x + b1*y = c1
a2*x + b2*y = c2
```
Read `a1 b1 c1` and `a2 b2 c2`. Print `x` and `y` each rounded to 4 decimal places, or `no solution` if the determinant is zero.

Example:
```
Input:
2 1 5
1 -1 1
Output:
x = 2.0000
y = 1.0000
```
MD,
                'starter_code'        => "a1, b1, c1 = map(int, input().split())\na2, b2, c2 = map(int, input().split())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Perform **forward elimination** (Gaussian Elimination without back-substitution) on a 3x3 augmented matrix. Read 3 rows, each with 4 space-separated values (coefficients + RHS). Print the resulting upper-triangular augmented matrix, each value rounded to 4 decimal places, space-separated per row.

Example:
```
Input:
2 1 -1 8
-3 -1 2 -11
-2 1 2 -3
Output:
2.0000 1.0000 -1.0000 8.0000
0.0000 0.5000 0.5000 1.0000
0.0000 0.0000 1.0000 -1.0000
```
MD,
                'starter_code'        => "M = [list(map(float, input().split())) for _ in range(3)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Solve a **3x3 system of linear equations** using Gaussian Elimination with back-substitution. Read 3 rows of the augmented matrix `[A|b]` (4 values per row). Print `x1`, `x2`, `x3` each rounded to 4 decimal places on separate lines. If no unique solution exists, print `no unique solution`.

Example:
```
Input:
2 1 -1 8
-3 -1 2 -11
-2 1 2 -3
Output:
2.0000
3.0000
-1.0000
```
MD,
                'starter_code'        => "M = [list(map(float, input().split())) for _ in range(3)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Check if a system of equations is **consistent or inconsistent** by examining the augmented matrix. Read `n` (number of equations) and `n+1` columns per row (coefficients + RHS). After row reduction, check for rows of the form `[0 0 ... 0 | c]` where `c ≠ 0`. Print `consistent` or `inconsistent`.

Example:
```
Input:
2
1 2 3
2 4 7
Output:
inconsistent
```
MD,
                'starter_code'        => "n = int(input())\nM = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Given an `n x n` system `Ax = b`, use **Gaussian Elimination** to compute `x`. Read `n`, then `n` rows of the augmented matrix `[A|b]`. Print each solution value rounded to 4 decimal places, one per line, labeled `x1 = ...`, `x2 = ...`, etc. If no unique solution, print `no unique solution`.

Example:
```
Input:
2
3 2 12
1 4 10
Output:
x1 = 2.0000
x2 = 2.0000
```
MD,
                'starter_code'        => "n = int(input())\nM = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Matrix Invertibility, Rank & the Four Fundamental Subspaces (Q16–Q20) → L363
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Compute the **rank** of a matrix using row reduction. Read `m` (rows), `n` (columns), then `m` rows of the matrix. Print the rank (number of non-zero rows after full row echelon form).

Example:
```
Input:
3 3
1 2 3
4 5 6
7 8 9
Output:
2
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Determine if an `n x n` matrix is **invertible**. Read `n` and the matrix. Print `invertible` if its rank equals `n`, otherwise print `not invertible`.

Example:
```
Input:
2
1 2
3 4
Output:
invertible
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Compute the **inverse of a 2x2 matrix** using the formula:
```
inv([[a,b],[c,d]]) = (1/det) * [[d,-b],[-c,a]]
```
Read the 4 values of the matrix (2 rows of 2). Print the inverse matrix row by row, each value rounded to 4 decimal places. If the matrix is not invertible, print `not invertible`.

Example:
```
Input:
1 2
3 4
Output:
-2.0000 1.0000
1.5000 -0.5000
```
MD,
                'starter_code'        => "row1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\na, b = row1\nc, d = row2\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Compute the **nullity** of a matrix. Read `m`, `n`, then the matrix. The nullity = n - rank. Print the nullity.

Example:
```
Input:
3 4
1 2 3 4
2 4 6 8
3 6 9 12
Output:
3
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Given an `m x n` matrix, print the **dimensions of the four fundamental subspaces**:
- Column space dimension (= rank)
- Null space dimension (= n - rank)
- Row space dimension (= rank)
- Left null space dimension (= m - rank)

Read `m n` and the matrix. Print each on a separate line in the format `column space: k`, `null space: k`, `row space: k`, `left null space: k`.

Example:
```
Input:
3 4
1 2 3 4
2 4 6 8
3 6 9 12
Output:
column space: 1
null space: 3
row space: 1
left null space: 2
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Determinants: Theory, Computation & Geometric Meaning (Q21–Q25) → L364
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Compute the **determinant of a 2x2 matrix**. Read 2 rows of 2 integers each. Print the determinant as an integer.

Recall: `det([[a,b],[c,d]]) = a*d - b*c`

Example:
```
Input:
3 8
4 6
Output:
-14
```
MD,
                'starter_code'        => "row1 = list(map(int, input().split()))\nrow2 = list(map(int, input().split()))\na, b = row1\nc, d = row2\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Compute the **determinant of a 3x3 matrix** using cofactor expansion along the first row. Read 3 rows of 3 integers. Print the determinant as an integer.

Example:
```
Input:
1 2 3
4 5 6
7 8 9
Output:
0
```
MD,
                'starter_code'        => "A = [list(map(int, input().split())) for _ in range(3)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Compute the **determinant of an `n x n` matrix** using Gaussian elimination (row reduction to upper triangular form, tracking sign changes from row swaps). Read `n` and the matrix. Print the determinant rounded to 4 decimal places.

Example:
```
Input:
3
2 1 3
4 5 6
1 2 1
Output:
-8.0000
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Given an `n x n` matrix, compute its determinant and then determine the **geometric meaning**:
- If `|det| > 1`: print `expands`
- If `|det| == 1`: print `preserves`
- If `0 < |det| < 1`: print `contracts`
- If `det == 0`: print `collapses`

Read `n` and the matrix. Print the determinant rounded to 4 decimal places on line 1, and the geometric meaning on line 2.

Example:
```
Input:
2
2 0
0 3
Output:
6.0000
expands
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Given an `n x n` matrix **A**, verify the property: **det(A^T) = det(A)**. Read `n` and the matrix. Compute both determinants and print `verified` if they are equal (within 1e-6 tolerance), otherwise print `failed`. Also print `det(A) = k` rounded to 4 decimal places.

Example:
```
Input:
2
1 2
3 4
Output:
det(A) = -2.0000
verified
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Eigenvalues & Eigenvectors (Q26–Q30) → L365
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Compute the **eigenvalues of a 2x2 matrix** using the characteristic polynomial `det(A - λI) = 0`. For a 2x2 matrix, solve the quadratic `λ^2 - trace(A)*λ + det(A) = 0`. Read 2 rows. Print the eigenvalues sorted in descending order, each rounded to 4 decimal places, one per line. If eigenvalues are complex, print `complex eigenvalues`.

Example:
```
Input:
4 1
2 3
Output:
5.0000
2.0000
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Given a **2x2 matrix** and one of its **eigenvalues** λ, find an **eigenvector** by solving `(A - λI)v = 0`. Read the 2x2 matrix (2 rows) and λ. Print the eigenvector as two values rounded to 4 decimal places, space-separated. Normalise so the first non-zero component is 1. If trivial solution only, print `no eigenvector`.

Example:
```
Input:
4 1
2 3
5
Output:
1.0000 1.0000
```
MD,
                'starter_code'        => "row1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nlam = float(input())\nA = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Compute the **trace** and **determinant** of an `n x n` matrix and use them to verify the eigenvalue relationships:
- Sum of eigenvalues = trace
- Product of eigenvalues = determinant

Read `n` and the matrix. Print `trace = k` and `det = k` (rounded to 4 decimal places) on separate lines.

Example:
```
Input:
2
4 1
2 3
Output:
trace = 7.0000
det = 10.0000
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Given a **2x2 matrix** and a vector **v**, check if **v is an eigenvector** of **A**. Compute **Av** and check if it is a scalar multiple of **v** (i.e., Av = λv for some λ). Read 2 rows of A, then vector v (2 values). Print `yes` and the eigenvalue λ rounded to 4 decimal places if it is an eigenvector, otherwise print `no`.

Example:
```
Input:
4 1
2 3
1 1
Output:
yes
5.0000
```
MD,
                'starter_code'        => "row1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nv = list(map(float, input().split()))\nA = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Given a **diagonal matrix** of size `n x n`, print its eigenvalues (the diagonal entries) sorted in descending order, one per line.

Read `n` and the matrix (only the diagonal is guaranteed non-zero but full matrix is given). Print each eigenvalue as an integer.

Example:
```
Input:
3
5 0 0
0 2 0
0 0 8
Output:
8
5
2
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Diagonalization & Matrix Powers (Q31–Q35) → L366
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Check if a **2x2 matrix is diagonalizable** by verifying it has two distinct real eigenvalues. Read 2 rows. Print `diagonalizable` if it has 2 distinct real eigenvalues, otherwise print `not diagonalizable`.

Example:
```
Input:
4 1
2 3
Output:
diagonalizable
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Compute **A^k** for a **diagonal matrix** of size `n x n` and integer power `k`. Read `n`, the matrix, and `k`. Print the result matrix (each diagonal entry raised to the power `k`, off-diagonals remain 0), row by row, space-separated.

Example:
```
Input:
2
3 0
0 2
3
Output:
27 0
0 8
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(int, input().split())) for _ in range(n)]\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Given a **2x2 diagonalizable matrix A** with known eigenvalues λ1, λ2 and a power `k`, compute **A^k** using diagonalization: `A^k = P * D^k * P^-1`. Read 2 rows of A and integer `k`. Print A^k row by row, each element rounded to 4 decimal places.

Example:
```
Input:
4 1
2 3
2
Output:
20.0000 7.0000
14.0000 11.0000
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Given the **Fibonacci recurrence** F(n) = F(n-1) + F(n-2) with F(0)=0, F(1)=1, compute F(n) using the matrix power method:
```
[[1,1],[1,0]]^n = [[F(n+1), F(n)], [F(n), F(n-1)]]
```
Read `n`. Print F(n).

Example:
```
Input:
10
Output:
55
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Given a **2x2 matrix A** and its eigenvalues, verify the **Cayley-Hamilton theorem**: every matrix satisfies its own characteristic equation. For a 2x2 matrix, the characteristic equation is `λ^2 - trace(A)*λ + det(A) = 0`, so `A^2 - trace(A)*A + det(A)*I = 0`.

Read 2 rows of A. Compute `A^2 - trace(A)*A + det(A)*I` and print `verified` if all entries are 0 (within 1e-6 tolerance), otherwise print `failed`.

Example:
```
Input:
4 1
2 3
Output:
verified
```
MD,
                'starter_code'        => "row1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Orthogonality, Projections & Gram-Schmidt (Q36–Q40) → L367
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Check if two vectors **u** and **v** of length `n` are **orthogonal** (dot product = 0). Read `n`, then the two vectors. Print `orthogonal` if their dot product is 0, otherwise print `not orthogonal` and the dot product as an integer.

Example:
```
Input:
3
1 0 0
0 1 0
Output:
orthogonal
```
MD,
                'starter_code'        => "n = int(input())\nu = list(map(int, input().split()))\nv = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Compute the **projection of vector u onto vector v**. Read `n`, then vectors u and v. Print the projection vector, each component rounded to 4 decimal places, space-separated.

Recall: `proj_v(u) = (u·v / v·v) * v`

Example:
```
Input:
2
3 4
1 0
Output:
3.0000 0.0000
```
MD,
                'starter_code'        => "n = int(input())\nu = list(map(float, input().split()))\nv = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Apply the **Gram-Schmidt process** to two linearly independent vectors **v1** and **v2** of length `n` to produce orthonormal vectors **e1** and **e2**. Read `n`, then v1 and v2. Print e1 and e2 each rounded to 4 decimal places, space-separated, one per line.

Example:
```
Input:
2
3 0
1 1
Output:
1.0000 0.0000
0.0000 1.0000
```
MD,
                'starter_code'        => "import math\nn = int(input())\nv1 = list(map(float, input().split()))\nv2 = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Compute the **orthogonal complement** of a vector subspace spanned by a single vector **v** in R^n. The component of vector **u** orthogonal to **v** is: `u_perp = u - proj_v(u)`. Read `n`, then u and v. Print u_perp rounded to 4 decimal places, space-separated.

Example:
```
Input:
3
1 2 3
1 0 0
Output:
0.0000 2.0000 3.0000
```
MD,
                'starter_code'        => "n = int(input())\nu = list(map(float, input().split()))\nv = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Check if an `n x n` matrix **Q** is **orthogonal** (Q^T * Q = I, within 1e-6 tolerance). Read `n` and the matrix. Print `orthogonal` or `not orthogonal`.

Example:
```
Input:
2
1 0
0 1
Output:
orthogonal
```
MD,
                'starter_code'        => "n = int(input())\nQ = [list(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Singular Value Decomposition (SVD) (Q41–Q45) → L368
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Compute **A^T * A** for a given matrix A of size `m x n`. Read `m n`, then the matrix row by row. Print the resulting `n x n` matrix, each value as an integer, row by row space-separated.

Example:
```
Input:
2 2
1 2
3 4
Output:
10 14
14 20
```
MD,
                'starter_code'        => "m, n = map(int, input().split())\nA = [list(map(int, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Given a **2x2 symmetric matrix** (which is A^T * A for some A), find its **singular values** by computing the square roots of its eigenvalues. Read 2 rows. Print the singular values sorted in descending order, each rounded to 4 decimal places, one per line.

Example:
```
Input:
5 2
2 2
Output:
6.0000
1.0000
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nB = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Compute the **Frobenius norm** of a matrix. The Frobenius norm is the square root of the sum of squares of all elements. Read `m n` and the matrix. Print the Frobenius norm rounded to 4 decimal places.

Example:
```
Input:
2 2
1 2
3 4
Output:
5.4772
```
MD,
                'starter_code'        => "import math\nm, n = map(int, input().split())\nA = [list(map(float, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Given the singular values σ1, σ2, ..., σr of a matrix, compute the **rank** (number of non-zero singular values, treating values < 1e-6 as zero) and the **condition number** (σ_max / σ_min of non-zero singular values). Read `k` singular values. Print `rank: r` and `condition number: c` rounded to 4 decimal places on separate lines.

Example:
```
Input:
3
6.0 2.0 0.0
Output:
rank: 2
condition number: 3.0000
```
MD,
                'starter_code'        => "k = int(input())\nsingular_values = list(map(float, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Given the **full SVD** of a matrix A = U * Σ * V^T (for a 2x2 case), reconstruct **A** and verify it matches the original. Read the 2x2 matrices U, Σ (diagonal matrix given as 2 diagonal values), and V^T. Compute A = U * diag(σ1,σ2) * V^T. Print A row by row, each value rounded to 2 decimal places, space-separated.

Input format: 2 rows for U, then σ1 and σ2 on one line, then 2 rows for V^T.

Example:
```
Input:
0 -1
-1 0
3 2
0 -1
-1 0
Output:
0.00 3.00
2.00 0.00
```
MD,
                'starter_code'        => "U = [list(map(float, input().split())) for _ in range(2)]\nSigma = list(map(float, input().split()))\nVT = [list(map(float, input().split())) for _ in range(2)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Positive Definite Matrices & Quadratic Forms (Q46–Q50) → L369
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Evaluate a **quadratic form** x^T * A * x for a symmetric `n x n` matrix **A** and a vector **x**. Read `n`, then the matrix A (n rows), then the vector x. Print the scalar result as an integer.

Example:
```
Input:
2
2 1
1 3
1 2
Output:
18
```
MD,
                'starter_code'        => "n = int(input())\nA = [list(map(int, input().split())) for _ in range(n)]\nx = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Determine if a **2x2 symmetric matrix** is **positive definite**, **positive semi-definite**, **negative definite**, **negative semi-definite**, or **indefinite** using the eigenvalue criterion. Read 2 rows. Print the classification.

Example:
```
Input:
2 1
1 3
Output:
positive definite
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Verify that a **2x2 positive definite matrix** satisfies the **leading principal minors** test: a symmetric matrix is positive definite if and only if all leading principal minors are positive. Read 2 rows. Print `M1 = a` (the (1,1) entry) and `M2 = det(A)` each rounded to 4 decimal places, then print `positive definite` or `not positive definite`.

Example:
```
Input:
4 2
2 3
Output:
M1 = 4.0000
M2 = 8.0000
positive definite
```
MD,
                'starter_code'        => "row1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Given a **positive definite 2x2 matrix**, compute the **Cholesky decomposition** A = L * L^T where L is a lower triangular matrix. Read 2 rows of A. Print L row by row, each value rounded to 4 decimal places, space-separated. If A is not positive definite, print `not positive definite`.

Recall for 2x2:
```
L[0][0] = sqrt(A[0][0])
L[1][0] = A[1][0] / L[0][0]
L[1][1] = sqrt(A[1][1] - L[1][0]^2)
```

Example:
```
Input:
4 2
2 3
Output:
2.0000 0.0000
1.0000 1.4142
```
MD,
                'starter_code'        => "import math\nrow1 = list(map(float, input().split()))\nrow2 = list(map(float, input().split()))\nA = [row1, row2]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Given a **quadratic form** `f(x, y) = ax^2 + bxy + cy^2` (represented as a 2x2 symmetric matrix), classify it and find the **minimum value** subject to the constraint `x^2 + y^2 = 1` (which equals the smallest eigenvalue of the matrix). Read `a`, `b`, `c` on one line. Print the classification (`positive definite`, `positive semi-definite`, `negative definite`, `negative semi-definite`, or `indefinite`) on line 1, and the minimum value on the unit circle rounded to 4 decimal places on line 2.

Example:
```
Input:
2 2 3
Output:
positive definite
1.0000
```
MD,
                'starter_code'        => "import math\nvals = list(map(float, input().split()))\na, b, c = vals[0], vals[1], vals[2]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],
        ];

        // ─────────────────────────────────────────────────────────────────
        // 3. PERSIST QUESTIONS
        // ─────────────────────────────────────────────────────────────────

        $questionIds = [];
        foreach ($questionDefs as $def) {
            $exists = DB::table('coding_questions')
                ->where('challenge_id', $challenge->id)
                ->where('order_index', $def['order_index'])
                ->first();

            if ($exists) {
                $questionIds[$def['order_index']] = $exists->id;
                continue;
            }

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

            $questionIds[$def['order_index']] = $id;
        }

        // ─────────────────────────────────────────────────────────────────
        // 4. TEST CASES
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $seed = function (int $qIndex, array $cases) use ($questionIds): void {
            $qId = $questionIds[$qIndex] ?? null;
            if (! $qId) return;

            if (DB::table('test_cases')->where('coding_question_id', $qId)->exists()) return;

            foreach ($cases as $case) {
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
        };

        // ── Q1: Vector addition & scalar mult ────────────────────────────
        $seed(1, [
            ['input' => "3\n1 2 3\n4 5 6\n2",        'expected_output' => "5 7 9\n2 4 6",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0\n1 1\n5",             'expected_output' => "1 1\n0 0",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1 1 1\n2 2 2 2\n3",    'expected_output' => "3 3 3 3\n3 3 3 3",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n-1 2\n3 -4\n-2",          'expected_output' => "2 -2\n2 -4",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Dot product ──────────────────────────────────────────────
        $seed(2, [
            ['input' => "3\n1 2 3\n4 5 6",    'expected_output' => "32",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1",         'expected_output' => "0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4\n4 3 2 1",'expected_output' => "20",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n-1 2 -3\n3 -2 1", 'expected_output' => "-10", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Vector magnitude ─────────────────────────────────────────
        $seed(3, [
            ['input' => "3\n3 4 0",    'expected_output' => "5.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 1",      'expected_output' => "1.4142",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0 0 0",    'expected_output' => "0.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2 3 6",    'expected_output' => "7.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Angle between vectors ─────────────────────────────────────
        $seed(4, [
            ['input' => "2\n1 0\n0 1",      'expected_output' => "90.0000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n1 0",      'expected_output' => "0.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0 0\n-1 0 0", 'expected_output' => "180.0000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 1\n1 -1",     'expected_output' => "90.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Cross product ─────────────────────────────────────────────
        $seed(5, [
            ['input' => "1 0 0\n0 1 0",   'expected_output' => "0 0 1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 1 0\n0 0 1",   'expected_output' => "1 0 0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3 4\n5 6 7",   'expected_output' => "-3 6 -3", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 1 1\n1 1 1",   'expected_output' => "0 0 0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Matrix addition ───────────────────────────────────────────
        $seed(6, [
            ['input' => "2\n1 2\n3 4\n5 6\n7 8",          'expected_output' => "6 8\n10 12",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0\n0 0\n1 2\n3 4",          'expected_output' => "1 2\n3 4",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0 0\n0 1 0\n0 0 1\n1 2 3\n4 5 6\n7 8 9", 'expected_output' => "2 2 3\n4 6 6\n7 8 10", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n-1 -2\n-3 -4\n1 2\n3 4",     'expected_output' => "0 0\n0 0",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Matrix transpose ──────────────────────────────────────────
        $seed(7, [
            ['input' => "3\n1 2 3\n4 5 6\n7 8 9",    'expected_output' => "1 4 7\n2 5 8\n3 6 9",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n3 4",                 'expected_output' => "1 3\n2 4",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0 0\n0 1 0\n0 0 1",     'expected_output' => "1 0 0\n0 1 0\n0 0 1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0 5\n-5 0",                'expected_output' => "0 -5\n5 0",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Matrix multiplication ─────────────────────────────────────
        $seed(8, [
            ['input' => "2 3 2\n1 2 3\n4 5 6\n7 8\n9 10\n11 12",  'expected_output' => "58 64\n139 154",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2 2\n1 0\n0 1\n3 4\n5 6",               'expected_output' => "3 4\n5 6",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 3 1\n1 2 3\n4\n5\n6",                   'expected_output' => "32",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2 2\n2 0\n0 3\n4 0\n0 5",               'expected_output' => "8 0\n0 15",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Special matrix structure ──────────────────────────────────
        $seed(9, [
            ['input' => "3\n1 0 0\n0 1 0\n0 0 1",         'expected_output' => "symmetric\nidentity\ndiagonal",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0\n0 0",                     'expected_output' => "symmetric\ndiagonal\nzero",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 2\n2 1",                     'expected_output' => "symmetric",                      'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n3 0\n0 5",                     'expected_output' => "symmetric\ndiagonal",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Scalar multiplication ────────────────────────────────────
        $seed(10, [
            ['input' => "2\n1 2\n3 4\n3",        'expected_output' => "3 6\n9 12",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n3 4\n0",        'expected_output' => "0 0\n0 0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0 0\n0 1 0\n0 0 1\n5", 'expected_output' => "5 0 0\n0 5 0\n0 0 5", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n2 4\n6 8\n-1",       'expected_output' => "-2 -4\n-6 -8", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: 2x2 system solver ────────────────────────────────────────
        $seed(11, [
            ['input' => "2 1 5\n1 -1 1",      'expected_output' => "x = 2.0000\ny = 1.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1 4\n1 -1 2",      'expected_output' => "x = 3.0000\ny = 1.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 3\n2 4 6",       'expected_output' => "no solution",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 2 12\n1 4 10",     'expected_output' => "x = 2.0000\ny = 2.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Forward elimination ──────────────────────────────────────
        $seed(12, [
            ['input' => "2 1 -1 8\n-3 -1 2 -11\n-2 1 2 -3",  'expected_output' => "2.0000 1.0000 -1.0000 8.0000\n0.0000 0.5000 0.5000 1.0000\n0.0000 0.0000 1.0000 -1.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0 0 1\n0 1 0 2\n0 0 1 3",          'expected_output' => "1.0000 0.0000 0.0000 1.0000\n0.0000 1.0000 0.0000 2.0000\n0.0000 0.0000 1.0000 3.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 4 2 8\n1 2 1 4\n3 6 3 12",         'expected_output' => "2.0000 4.0000 2.0000 8.0000\n0.0000 0.0000 0.0000 0.0000\n0.0000 0.0000 0.0000 0.0000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 1 1 6\n0 2 5 -4\n2 5 -1 27",       'expected_output' => "1.0000 1.0000 1.0000 6.0000\n0.0000 2.0000 5.0000 -4.0000\n0.0000 0.0000 -8.5000 23.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q13: Gaussian elimination 3x3 ────────────────────────────────
        $seed(13, [
            ['input' => "2 1 -1 8\n-3 -1 2 -11\n-2 1 2 -3",  'expected_output' => "2.0000\n3.0000\n-1.0000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0 0 4\n0 1 0 5\n0 0 1 6",           'expected_output' => "4.0000\n5.0000\n6.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 3 6\n2 4 6 12\n1 1 1 3",          'expected_output' => "no unique solution",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 1 1 6\n0 2 5 -4\n2 5 -1 27",        'expected_output' => "5.0000\n3.0000\n-2.0000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Consistent or inconsistent ──────────────────────────────
        $seed(14, [
            ['input' => "2\n1 2 3\n2 4 7",     'expected_output' => "inconsistent",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 1 2\n2 2 4",     'expected_output' => "consistent",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0 1\n0 1 1\n1 1 2", 'expected_output' => "consistent", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 1 1\n2 2 2\n3 3 4", 'expected_output' => "inconsistent",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: General n×n Gaussian solver ─────────────────────────────
        $seed(15, [
            ['input' => "2\n3 2 12\n1 4 10",               'expected_output' => "x1 = 2.0000\nx2 = 2.0000",               'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0 5\n0 1 3",                 'expected_output' => "x1 = 5.0000\nx2 = 3.0000",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2 1 -1 8\n-3 -1 2 -11\n-2 1 2 -3", 'expected_output' => "x1 = 2.0000\nx2 = 3.0000\nx3 = -1.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n2 2 2\n4 4 5",                 'expected_output' => "no unique solution",                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Matrix rank ──────────────────────────────────────────────
        $seed(16, [
            ['input' => "3 3\n1 2 3\n4 5 6\n7 8 9",   'expected_output' => "2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n1 2\n3 4",               'expected_output' => "2",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 4\n1 2 3 4\n2 4 6 8\n3 6 9 12", 'expected_output' => "1", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 3\n1 0 1\n0 1 1",           'expected_output' => "2",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Is invertible ────────────────────────────────────────────
        $seed(17, [
            ['input' => "2\n1 2\n3 4",        'expected_output' => "invertible",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n2 4",        'expected_output' => "not invertible",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3\n4 5 6\n7 8 9", 'expected_output' => "not invertible", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n5 0\n0 3",        'expected_output' => "invertible",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: 2x2 matrix inverse ───────────────────────────────────────
        $seed(18, [
            ['input' => "1 2\n3 4",    'expected_output' => "-2.0000 1.0000\n1.5000 -0.5000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 0\n0 4",    'expected_output' => "0.5000 0.0000\n0.0000 0.2500",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2\n2 4",    'expected_output' => "not invertible",                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 1\n5 2",    'expected_output' => "2.0000 -1.0000\n-5.0000 3.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Nullity ──────────────────────────────────────────────────
        $seed(19, [
            ['input' => "3 4\n1 2 3 4\n2 4 6 8\n3 6 9 12",  'expected_output' => "3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n1 2\n3 4",                     'expected_output' => "0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 3\n1 2 3\n4 5 6\n7 8 9",          'expected_output' => "1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 3\n1 0 1\n0 1 1",                 'expected_output' => "1",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Four fundamental subspaces ──────────────────────────────
        $seed(20, [
            ['input' => "3 4\n1 2 3 4\n2 4 6 8\n3 6 9 12",  'expected_output' => "column space: 1\nnull space: 3\nrow space: 1\nleft null space: 2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n1 2\n3 4",                     'expected_output' => "column space: 2\nnull space: 0\nrow space: 2\nleft null space: 0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 3\n1 2 3\n4 5 6\n7 8 9",          'expected_output' => "column space: 2\nnull space: 1\nrow space: 2\nleft null space: 1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 3\n1 0 1\n0 1 1",                 'expected_output' => "column space: 2\nnull space: 1\nrow space: 2\nleft null space: 0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: 2x2 determinant ─────────────────────────────────────────
        $seed(21, [
            ['input' => "3 8\n4 6",    'expected_output' => "-14",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2\n3 4",    'expected_output' => "-2",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5 0\n0 5",    'expected_output' => "25",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 3\n2 3",    'expected_output' => "0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: 3x3 determinant ─────────────────────────────────────────
        $seed(22, [
            ['input' => "1 2 3\n4 5 6\n7 8 9",   'expected_output' => "0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0 0\n0 1 0\n0 0 1",   'expected_output' => "1",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 1 3\n0 4 1\n5 0 2",   'expected_output' => "-1",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 2 0\n3 4 0\n5 6 0",   'expected_output' => "0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: nxn determinant via elimination ──────────────────────────
        $seed(23, [
            ['input' => "3\n2 1 3\n4 5 6\n1 2 1",   'expected_output' => "-8.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3 1\n5 2",               'expected_output' => "1.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3\n4 5 6\n7 8 9",   'expected_output' => "0.0000",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 0 0\n0 2 0\n0 0 3",   'expected_output' => "6.0000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Determinant + geometric meaning ──────────────────────────
        $seed(24, [
            ['input' => "2\n2 0\n0 3",    'expected_output' => "6.0000\nexpands",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1",    'expected_output' => "1.0000\npreserves", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 2\n3 4",    'expected_output' => "-2.0000\nexpands",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 2\n2 4",    'expected_output' => "0.0000\ncollapses", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: det(A^T) == det(A) verification ─────────────────────────
        $seed(25, [
            ['input' => "2\n1 2\n3 4",    'expected_output' => "det(A) = -2.0000\nverified",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2 0\n0 3",    'expected_output' => "det(A) = 6.0000\nverified",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3\n4 5 6\n7 8 9", 'expected_output' => "det(A) = 0.0000\nverified", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n3 1\n5 2",    'expected_output' => "det(A) = 1.0000\nverified",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: 2x2 eigenvalues ──────────────────────────────────────────
        $seed(26, [
            ['input' => "4 1\n2 3",    'expected_output' => "5.0000\n2.0000",         'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 0\n0 3",    'expected_output' => "3.0000\n2.0000",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "0 -1\n1 0",   'expected_output' => "complex eigenvalues",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 4\n1 2",    'expected_output' => "6.0000\n1.0000",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Eigenvector for given eigenvalue ─────────────────────────
        $seed(27, [
            ['input' => "4 1\n2 3\n5",    'expected_output' => "1.0000 1.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 1\n2 3\n2",    'expected_output' => "1.0000 -2.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 0\n0 3\n3",    'expected_output' => "1.0000 0.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 4\n1 2\n6",    'expected_output' => "1.0000 0.2500",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Trace and determinant ────────────────────────────────────
        $seed(28, [
            ['input' => "2\n4 1\n2 3",    'expected_output' => "trace = 7.0000\ndet = 10.0000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1",    'expected_output' => "trace = 2.0000\ndet = 1.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3\n4 5 6\n7 8 9", 'expected_output' => "trace = 15.0000\ndet = 0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n3 0\n0 5",    'expected_output' => "trace = 8.0000\ndet = 15.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Is eigenvector ───────────────────────────────────────────
        $seed(29, [
            ['input' => "4 1\n2 3\n1 1",    'expected_output' => "yes\n5.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 1\n2 3\n1 0",    'expected_output' => "no",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 0\n0 3\n0 1",    'expected_output' => "yes\n3.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 1\n2 3\n1 -2",   'expected_output' => "yes\n2.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Diagonal matrix eigenvalues ─────────────────────────────
        $seed(30, [
            ['input' => "3\n5 0 0\n0 2 0\n0 0 8",    'expected_output' => "8\n5\n2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3 0\n0 7",                'expected_output' => "7\n3",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 0 0\n0 1 0\n0 0 1",    'expected_output' => "1\n1\n1",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4 0 0 0\n0 3 0 0\n0 0 2 0\n0 0 0 1", 'expected_output' => "4\n3\n2\n1", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q31: Is diagonalizable ────────────────────────────────────────
        $seed(31, [
            ['input' => "4 1\n2 3",    'expected_output' => "diagonalizable",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 1\n0 2",    'expected_output' => "not diagonalizable",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0 -1\n1 0",   'expected_output' => "not diagonalizable",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 4\n1 2",    'expected_output' => "diagonalizable",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Diagonal matrix power ────────────────────────────────────
        $seed(32, [
            ['input' => "2\n3 0\n0 2\n3",    'expected_output' => "27 0\n0 8",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1\n10",   'expected_output' => "1 0\n0 1",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2 0 0\n0 3 0\n0 0 4\n2", 'expected_output' => "4 0 0\n0 9 0\n0 0 16", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n5 0\n0 2\n0",    'expected_output' => "1 0\n0 1",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: A^k via diagonalization ─────────────────────────────────
        $seed(33, [
            ['input' => "4 1\n2 3\n2",    'expected_output' => "20.0000 7.0000\n14.0000 11.0000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 1\n2 3\n1",    'expected_output' => "4.0000 1.0000\n2.0000 3.0000",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 1\n2 3\n0",    'expected_output' => "1.0000 0.0000\n0.0000 1.0000",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 4\n1 2\n2",    'expected_output' => "29.0000 24.0000\n6.0000 8.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Fibonacci via matrix power ──────────────────────────────
        $seed(34, [
            ['input' => "10",   'expected_output' => "55",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0",    'expected_output' => "0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "1",    'expected_output' => "1",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "20",   'expected_output' => "6765",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Cayley-Hamilton verification ────────────────────────────
        $seed(35, [
            ['input' => "4 1\n2 3",    'expected_output' => "verified",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0\n0 1",    'expected_output' => "verified",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3\n1 4",    'expected_output' => "verified",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 4\n1 2",    'expected_output' => "verified",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Orthogonality check ──────────────────────────────────────
        $seed(36, [
            ['input' => "3\n1 0 0\n0 1 0",    'expected_output' => "orthogonal",              'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n2 1",         'expected_output' => "not orthogonal\n4",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 1 0\n0 0 1",    'expected_output' => "orthogonal",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n3 4\n-4 3",        'expected_output' => "orthogonal",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Projection of u onto v ───────────────────────────────────
        $seed(37, [
            ['input' => "2\n3 4\n1 0",    'expected_output' => "3.0000 0.0000",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 1\n1 0",    'expected_output' => "1.0000 0.0000",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3\n0 0 1",'expected_output' => "0.0000 0.0000 3.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n2 3\n4 3",    'expected_output' => "2.0800 1.5600",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Gram-Schmidt ─────────────────────────────────────────────
        $seed(38, [
            ['input' => "2\n3 0\n1 1",    'expected_output' => "1.0000 0.0000\n0.0000 1.0000",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1",    'expected_output' => "1.0000 0.0000\n0.0000 1.0000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n2 0\n1 1",    'expected_output' => "1.0000 0.0000\n0.0000 1.0000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 1 0\n1 0 0\n0 1 0", 'expected_output' => "0.7071 0.7071 0.0000\n0.7071 -0.7071 0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q39: Orthogonal complement ────────────────────────────────────
        $seed(39, [
            ['input' => "3\n1 2 3\n1 0 0",    'expected_output' => "0.0000 2.0000 3.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3 4\n1 0",         'expected_output' => "0.0000 4.0000",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 1\n1 1",         'expected_output' => "0.0000 0.0000",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0 5 0\n1 0 0",    'expected_output' => "0.0000 5.0000 0.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Orthogonal matrix check ──────────────────────────────────
        $seed(40, [
            ['input' => "2\n1 0\n0 1",    'expected_output' => "orthogonal",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 1\n0 1",    'expected_output' => "not orthogonal",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 -1\n1 0",   'expected_output' => "orthogonal",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 0 0\n0 1 0\n0 0 1", 'expected_output' => "orthogonal", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q41: A^T * A ─────────────────────────────────────────────────
        $seed(41, [
            ['input' => "2 2\n1 2\n3 4",       'expected_output' => "10 14\n14 20",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n1 0\n0 1",        'expected_output' => "1 0\n0 1",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2\n1 0\n0 1\n1 1",  'expected_output' => "2 1\n1 2",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 3\n1 2 3\n4 5 6",   'expected_output' => "17 22 27\n22 29 36\n27 36 45", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q42: Singular values of 2x2 symmetric ────────────────────────
        $seed(42, [
            ['input' => "5 2\n2 2",    'expected_output' => "6.0000\n1.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 0\n0 9",    'expected_output' => "3.0000\n2.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 0\n0 1",    'expected_output' => "1.0000\n1.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "10 6\n6 5",   'expected_output' => "13.0000\n2.0000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Frobenius norm ───────────────────────────────────────────
        $seed(43, [
            ['input' => "2 2\n1 2\n3 4",    'expected_output' => "5.4772",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n1 0\n0 1",    'expected_output' => "1.4142",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3\n1 2 3\n4 5 6",'expected_output' => "9.5394",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 3\n3 4 0",       'expected_output' => "5.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Rank and condition number from singular values ────────────
        $seed(44, [
            ['input' => "3\n6.0 2.0 0.0",   'expected_output' => "rank: 2\ncondition number: 3.0000",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5.0 1.0",        'expected_output' => "rank: 2\ncondition number: 5.0000",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.0 0.0 0.0",   'expected_output' => "rank: 0\ncondition number: 0.0000",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n10.0 4.0 2.0",  'expected_output' => "rank: 3\ncondition number: 5.0000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: SVD reconstruction ───────────────────────────────────────
        $seed(45, [
            ['input' => "0 -1\n-1 0\n3 2\n0 -1\n-1 0",                   'expected_output' => "0.00 3.00\n2.00 0.00",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0\n0 1\n5 0\n1 0\n0 1",                        'expected_output' => "5.00 0.00\n0.00 0.00",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0 1\n1 0\n4 3\n0 1\n1 0",                        'expected_output' => "0.00 4.00\n3.00 0.00",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 0\n0 1\n3 4\n1 0\n0 1",                        'expected_output' => "3.00 0.00\n0.00 4.00",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Quadratic form ───────────────────────────────────────────
        $seed(46, [
            ['input' => "2\n2 1\n1 3\n1 2",    'expected_output' => "18",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1\n3 4",    'expected_output' => "25",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n4 2\n2 3\n1 1",    'expected_output' => "11",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 0\n0 1\n0 0",    'expected_output' => "0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Positive definiteness classification ─────────────────────
        $seed(47, [
            ['input' => "2 1\n1 3",     'expected_output' => "positive definite",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "-2 1\n1 -3",   'expected_output' => "negative definite",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2\n2 1",     'expected_output' => "indefinite",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 1\n1 1",     'expected_output' => "positive semi-definite", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Leading principal minors test ────────────────────────────
        $seed(48, [
            ['input' => "4 2\n2 3",     'expected_output' => "M1 = 4.0000\nM2 = 8.0000\npositive definite",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2\n2 1",     'expected_output' => "M1 = 1.0000\nM2 = -3.0000\nnot positive definite", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 1\n1 2",     'expected_output' => "M1 = 2.0000\nM2 = 3.0000\npositive definite",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "0 0\n0 1",     'expected_output' => "M1 = 0.0000\nM2 = 0.0000\nnot positive definite", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Cholesky decomposition ───────────────────────────────────
        $seed(49, [
            ['input' => "4 2\n2 3",    'expected_output' => "2.0000 0.0000\n1.0000 1.4142",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "9 0\n0 4",    'expected_output' => "3.0000 0.0000\n0.0000 2.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2\n2 1",    'expected_output' => "not positive definite",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 1\n1 2",    'expected_output' => "1.0000 0.0000\n1.0000 1.0000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Quadratic form classification + min on unit circle ───────
        $seed(50, [
            ['input' => "2 2 3",     'expected_output' => "positive definite\n1.0000",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0 1",     'expected_output' => "positive definite\n1.0000",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "-1 0 -1",   'expected_output' => "negative definite\n-1.0000",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 4 1",     'expected_output' => "indefinite\n-1.0000",             'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 9 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}