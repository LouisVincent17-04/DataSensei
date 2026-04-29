<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 5 — Introduction to Mathematical Proof (Intermediate / Tier 3) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Intermediate tier
 *   2. coding_questions    — 50 questions at rigorous applied-proof level
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (lessons 316–325):
 *   L5.1  Introduction to Mathematical Proof
 *   L5.2  Direct Proof
 *   L5.3  Proof by Contrapositive
 *   L5.4  Proof by Contradiction
 *   L5.5  Proof by Cases (Exhaustion)
 *   L5.6  Mathematical Induction
 *   L5.7  Strong Induction
 *   L5.8  Existence and Uniqueness Proofs
 *   L5.9  Disproving Statements: Counterexamples
 *   L5.10 Choosing the Right Proof Strategy
 *
 * Difficulty: Intermediate — multi-technique problems, graph theory,
 * modular arithmetic proofs, non-trivial induction combinatorics,
 * algebraic structures, and proof-by-algorithm tasks. Problems require
 * constructing full proof artifacts (witnesses, certificates, invariants)
 * in code, not just yes/no verification.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module5CodingChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (! $category) {
            $this->command->error('Intermediate category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 5 — Introduction to Mathematical Proof (Intermediate) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Introduction to Mathematical Proof',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Construct computational proof artifacts — witnesses, Bezout certificates, induction invariants, and counterexample generators — across number theory, combinatorics, graph theory, and modular arithmetic. Each problem demands the full logical structure of a written proof, implemented as a working algorithm.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 1500,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Logic & Proof Structure (Q1–Q5)  →  Lesson 316
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Read a propositional formula over variables A, B, C given as a truth table. Input is 8 lines (one for each combination A=0/1, B=0/1, C=0/1 in order 000 to 111), each containing the formula's output (0 or 1). Determine whether the formula is a **tautology**, **contradiction**, or **contingency**. Print the result. Also print the number of satisfying assignments.

Format:
```
tautology/contradiction/contingency
satisfying: <k>
```

Example:
```
Input:
1
1
1
1
1
1
1
1
Output:
tautology
satisfying: 8
```
MD,
                'starter_code'        => "outputs = [int(input()) for _ in range(8)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read `n` clauses of a propositional formula in **Conjunctive Normal Form** (CNF). Each clause is a space-separated list of integers; positive integer k means variable xₖ, negative means ¬xₖ. Read the number of variables `m` first, then `n` clauses. Use **DPLL** (or brute-force for small m ≤ 10) to determine if the formula is **satisfiable**. If satisfiable, print `SAT` and a satisfying assignment (1 or 0 for each variable x1..xm). Otherwise print `UNSAT`.

Example:
```
Input:
2
2
1 -2
-1 2
Output:
SAT
x1=1 x2=1
```
MD,
                'starter_code'        => "m = int(input())\nn = int(input())\nclauses = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read a logical argument as `n` premises (propositional formulas as truth values 0/1) and one conclusion (also 0/1). Determine whether the argument is **valid** (every assignment making all premises true also makes the conclusion true). Since we're working with single truth values here, check: if all premises are 1 and conclusion is 0, the argument is **invalid**; otherwise **valid**. Print `valid` or `invalid`, and if invalid, print `counterexample found`.

Format:
```
valid/invalid
```
or
```
invalid
counterexample found
```

Example:
```
Input:
3
1
1
1
0
Output:
invalid
counterexample found
```
MD,
                'starter_code'        => "n = int(input())\npremises = [int(input()) for _ in range(n)]\nconclusion = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read `n` integers. Prove or disprove the claim: **"For all x in S, x is coprime to all other elements of S"** (i.e. gcd(xᵢ, xⱼ) = 1 for all i ≠ j). If the claim holds, print `pairwise coprime`. If not, print `not pairwise coprime` and a counterexample pair `(<a>, <b>) gcd=<g>`.

Example:
```
Input:
3
4
9
25
Output: pairwise coprime
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Implement a **proof checker** for a chain of divisibility. Read `n` integers a₁, a₂, …, aₙ. Verify that a₁ | a₂ | a₃ | … | aₙ (each divides the next). For each consecutive pair, print `a_i | a_{i+1}: True/False`. At the end print `chain valid: True/False`.

Example:
```
Input:
4
2
6
18
54
Output:
a1 | a2: True
a2 | a3: True
a3 | a4: True
chain valid: True
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Direct Proof — Advanced (Q6–Q10)  →  Lesson 317
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Direct proof of **Fermat's Little Theorem**: if p is prime and gcd(a, p) = 1, then aᵖ⁻¹ ≡ 1 (mod p). Read prime `p` and integer `a` with gcd(a, p) = 1. Compute aᵖ⁻¹ mod p using fast modular exponentiation. Print the result and verify it equals 1.

Format:
```
a^(p-1) mod p: <value>
theorem holds: True/False
```

Example:
```
Input:
7
3
Output:
a^(p-1) mod p: 1
theorem holds: True
```
MD,
                'starter_code'        => "p = int(input())\na = int(input())\n# Compute pow(a, p-1, p)\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Direct proof: **Wilson's Theorem** — p is prime if and only if (p−1)! ≡ −1 (mod p). Read `p`. Compute (p−1)! mod p. Print the result and whether it equals p−1 (which is −1 mod p).

Format:
```
(p-1)! mod p: <value>
wilson holds: True/False
```

Also print `p is prime: True/False` (verify independently).

Example:
```
Input: 7
Output:
(p-1)! mod p: 6
wilson holds: True
p is prime: True
```
MD,
                'starter_code'        => "p = int(input())\n# Compute factorial(p-1) mod p\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Direct proof using **modular arithmetic**: prove that a² + b² ≡ 0 (mod 4) implies both a and b are even. Read `n` pairs (a, b). For each pair, check the claim and its converse. Print for each:
```
a=<v> b=<v> a^2+b^2 mod4=<r> both_even=True/False claim_holds=True/False
```

Example:
```
Input:
2
2 4
1 3
Output:
a=2 b=4 a^2+b^2 mod4=0 both_even=True claim_holds=True
a=1 b=3 a^2+b^2 mod4=2 both_even=False claim_holds=True
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Direct proof of the **Pigeonhole Principle**: if n items are placed into k containers with n > k, at least one container has more than one item. Read `n` items and `k` containers as a list of `n` container assignments (integers 1..k). Find a container with more than one item and print it along with the items. If no collision, print `no pigeonhole` (impossible if n > k but possible if n ≤ k).

Format:
```
pigeonhole: container <c> has items <i1> <i2> ...
```

Example:
```
Input:
4
3
2
1
2
3
Output: pigeonhole: container 2 has items 2 4
```
MD,
                'starter_code'        => "n = int(input())\nk = int(input())\nassignments = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Direct proof: **Euler's theorem** — if gcd(a, n) = 1, then aᵠ⁽ⁿ⁾ ≡ 1 (mod n), where φ(n) is Euler's totient function. Read `a` and `n`. Compute φ(n) (count integers 1..n−1 coprime to n), then aᵠ⁽ⁿ⁾ mod n. Print φ(n), the modular power, and whether the theorem holds.

Format:
```
phi(n): <value>
a^phi(n) mod n: <value>
euler holds: True/False
```

Example:
```
Input:
3
10
Output:
phi(n): 4
a^phi(n) mod n: 1
euler holds: True
```
MD,
                'starter_code'        => "a = int(input())\nn = int(input())\n# Compute Euler totient phi(n) and verify theorem\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Proof by Contrapositive (Q11–Q14)  →  Lesson 318
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Prove by contrapositive: **if n is not divisible by any prime ≤ √n, then n is prime**. The contrapositive: "if n is composite, then n has a prime factor ≤ √n." Read `n` integers (one per line after `n`). For each composite number, find the smallest prime factor ≤ √n. For each prime, print `prime`. Print results one per line.

Format per number: `<n>: prime` or `<n>: composite, factor=<p>`

Example:
```
Input:
3
7
12
25
Output:
7: prime
12: composite, factor=2
25: composite, factor=5
```
MD,
                'starter_code'        => "import math\nk = int(input())\nnums = [int(input()) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Prove by contrapositive: **if a sequence has no two adjacent equal elements, then it is not constant**. More precisely: "if a sequence is constant then it has adjacent equal elements." Read `n` integers. Print `constant: True/False` and `has adjacent equal: True/False`. Verify they are logically consistent (constant implies has-adjacent-equal).

Format:
```
constant: True/False
has adjacent equal: True/False
consistent: True/False
```

Example:
```
Input:
4
3
3
3
3
Output:
constant: True
has adjacent equal: True
consistent: True
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Prove by contrapositive: **if a graph has an Eulerian circuit, then every vertex has even degree**. The contrapositive: "if some vertex has odd degree, then the graph has no Eulerian circuit." Read a graph as `n` vertices and `m` edges (format: `u v` per edge). Print each vertex's degree, identify any odd-degree vertices, and conclude whether an Eulerian circuit can exist.

Format:
```
degrees: v1=<d> v2=<d> ...
odd degree vertices: <list or 'none'>
eulerian circuit possible: True/False
```

Example:
```
Input:
4
4
1 2
2 3
3 4
4 1
Output:
degrees: v1=2 v2=2 v3=2 v4=2
odd degree vertices: none
eulerian circuit possible: True
```
MD,
                'starter_code'        => "n = int(input())\nm = int(input())\nedges = [tuple(map(int, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Prove by contrapositive: **if a number is not a perfect square, then its square root is irrational**. The contrapositive: "if √n is rational then n is a perfect square." Read `n` integers. For each, determine if it is a perfect square. If yes, print `<n>: perfect square (sqrt=<k>)`. If no, print `<n>: not perfect square (irrational sqrt)`.

Example:
```
Input:
4
9
7
25
15
Output:
9: perfect square (sqrt=3)
7: not perfect square (irrational sqrt)
25: perfect square (sqrt=5)
15: not perfect square (irrational sqrt)
```
MD,
                'starter_code'        => "import math\nk = int(input())\nnums = [int(input()) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Proof by Contradiction (Q15–Q19)  →  Lesson 319
            // ═══════════════════════════════════════════════════════════════

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Proof by contradiction: **there exist infinitely many primes of the form 4k + 3**. Dirichlet's theorem guarantees this. Simulate the Euclidean-style argument: given a finite list of primes of the form 4k+3, construct N = 4·(product of all such primes) − 1. N must have a prime factor of the form 4k+3 not in the list. Read `n` primes of the form 4k+3 (one per line after `n`). Compute N, find its prime factors of the form 4k+3, and print one that is not in the original list, or `all factors in list` if somehow all are (shouldn't happen).

Format:
```
N: <value>
new prime (4k+3): <p or 'all factors in list'>
```

Example:
```
Input:
2
3
7
Output:
N: 83
new prime (4k+3): 83
```
MD,
                'starter_code'        => "n = int(input())\nprimes = [int(input()) for _ in range(n)]\n# Compute N = 4 * product(primes) - 1\n# Find prime factors of N that are 4k+3 and not in list\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Proof by contradiction: **√p is irrational for any prime p**. Assume √p = a/b in lowest terms, so pb² = a², hence p | a² and (since p is prime) p | a. Let a = pk; then pb² = p²k², so b² = pk², hence p | b — contradicting gcd(a,b) = 1. Read a prime `p` and an integer `N`. Search all fractions a/b with 1 ≤ a, b ≤ N and gcd(a,b) = 1. Print whether any satisfies a² = p·b² (should always be `none` for prime p). Also print the fraction closest to √p.

Format:
```
exact fraction: none
closest: <a>/<b> = <decimal to 8dp>
```

Example:
```
Input:
5
20
Output:
exact fraction: none
closest: 9/4 = 2.25000000
```
MD,
                'starter_code'        => "import math\np = int(input())\nN = int(input())\n# Search fractions with gcd=1\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Proof by contradiction: **a tree with n vertices has exactly n−1 edges**. Read a graph as `n` vertices and `m` edges (format: `u v` per edge, 1-indexed). Check if the graph is a tree (connected + acyclic). If not a tree, print `not a tree: <reason>` where reason is `disconnected`, `cycle detected`, or `wrong edge count`. If it is a tree, print `tree: True` and verify `edges = n-1`.

Format:
```
tree: True/False
edges: <m>
vertices: <n>
n-1: <n-1>
```
or
```
not a tree: <reason>
```

Example:
```
Input:
4
3
1 2
2 3
3 4
Output:
tree: True
edges: 3
vertices: 4
n-1: 3
```
MD,
                'starter_code'        => "n = int(input())\nm = int(input())\nedges = [tuple(map(int, input().split())) for _ in range(m)]\n# Check connectivity and acyclicity using Union-Find or BFS\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Proof by contradiction: **the harmonic series diverges**. Assume it converges to sum S. Then S = 1 + 1/2 + (1/3+1/4) + (1/5+1/6+1/7+1/8) + … Each group of 2^k terms sums to more than 1/2. Read `n` (number of terms). Print the partial sum, the number of "half-sum" groups fully contained, and the lower bound from the grouping argument.

Format:
```
partial_sum: <value rounded to 6dp>
groups: <k>
lower_bound: <k/2 rounded to 6dp>
```

Example:
```
Input: 8
Output:
partial_sum: 2.717857
groups: 3
lower_bound: 1.500000
```
MD,
                'starter_code'        => "n = int(input())\n# Harmonic sum 1 + 1/2 + 1/3 + ... + 1/n\n# Groups: group 0 = {1}, group 1 = {2}, group k = {2^(k-1)+1,...,2^k}\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Proof by contradiction: **the decimal expansion of 1/n terminates iff n = 2^a · 5^b**. Read `n`. Find a and b (the largest powers of 2 and 5 dividing n). After removing all factors of 2 and 5, check if the remainder is 1. Print `terminates: True/False`, a, b, and the remainder.

Format:
```
a: <value>
b: <value>
remainder: <value>
terminates: True/False
```

Example:
```
Input: 40
Output:
a: 3
b: 1
remainder: 1
terminates: True
```
MD,
                'starter_code'        => "n = int(input())\n# Remove all factors of 2 and 5 from n\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Proof by Cases (Q20–Q24)  →  Lesson 320
            // ═══════════════════════════════════════════════════════════════

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Proof by cases: **for any integer n, n⁴ ≡ 0 or 1 (mod 5)**. Read `n` integers. For each, compute n mod 5, n⁴ mod 5, and verify the claim. Print a case table.

Format per line:
```
n=<v> n mod 5=<r> n^4 mod 5=<s> valid=True/False
```

Example:
```
Input:
3
3
4
5
Output:
n=3 n mod 5=3 n^4 mod 5=1 valid=True
n=4 n mod 5=4 n^4 mod 5=1 valid=True
n=5 n mod 5=0 n^4 mod 5=0 valid=True
```
MD,
                'starter_code'        => "k = int(input())\nnums = [int(input()) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Proof by cases: **the square of any integer is of the form 3k or 3k+1 (never 3k+2)**. Read `n` integers. For each, compute n mod 3 and n² mod 3, verify the claim. Print results.

Format per line:
```
n=<v> n mod 3=<r> n^2 mod 3=<s> valid=True/False
```

Example:
```
Input:
3
7
8
9
Output:
n=7 n mod 3=1 n^2 mod 3=1 valid=True
n=8 n mod 3=2 n^2 mod 3=1 valid=True
n=9 n mod 3=0 n^2 mod 3=0 valid=True
```
MD,
                'starter_code'        => "k = int(input())\nnums = [int(input()) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Proof by exhaustion of cases: **every integer n satisfies exactly one of: n ≡ 0, 1, 2, 3, 4, 5 (mod 6), and for each case, 6 | n(n+1)(n+2)(n+3)(n+4)(n+5)**. Read `n`. Identify its case mod 6, compute n(n+1)(n+2)(n+3)(n+4)(n+5), and verify divisibility by 720 (= 6!).

Format:
```
n mod 6: <r>
product: <value>
divisible by 720: True/False
```

Example:
```
Input: 7
Output:
n mod 6: 1
product: 20160
divisible by 720: True
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Proof by cases: **for integers a and b, max(a,b) + min(a,b) = a + b and max(a,b) − min(a,b) = |a − b|**. Read `n` pairs. For each pair verify both identities and print results.

Format per pair:
```
a=<v> b=<v> max+min=<s> a+b=<s> id1=True/False max-min=<d> |a-b|=<d> id2=True/False
```

Example:
```
Input:
2
3 7
5 2
Output:
a=3 b=7 max+min=10 a+b=10 id1=True max-min=4 |a-b|=4 id2=True
a=5 b=2 max+min=7 a+b=7 id1=True max-min=3 |a-b|=3 id2=True
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Proof by cases: **for all integers n, n² + n is always even and n² + n + 1 is always odd**. Read `n` integers. For each, verify both claims and identify whether n is even or odd.

Format per number:
```
n=<v> parity=even/odd n^2+n=<v> even=True n^2+n+1=<v> odd=True
```

Example:
```
Input:
3
4
7
0
Output:
n=4 parity=even n^2+n=20 even=True n^2+n+1=21 odd=True
n=7 parity=odd n^2+n=56 even=True n^2+n+1=57 odd=True
n=0 parity=even n^2+n=0 even=True n^2+n+1=1 odd=True
```
MD,
                'starter_code'        => "k = int(input())\nnums = [int(input()) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Mathematical Induction (Q25–Q29)  →  Lesson 321
            // ═══════════════════════════════════════════════════════════════

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Prove by induction that **the number of subsets of an n-element set is 2ⁿ**. Read `n`. For each k from 0 to n, compute C(n,k) (binomial coefficient) and print the cumulative sum. Verify the total equals 2ⁿ.

Format:
```
k=0: C(n,k)=<v>
k=1: C(n,k)=<v>
...
total: <2^n>
verified: True/False
```

Example:
```
Input: 3
Output:
k=0: C(3,k)=1
k=1: C(3,k)=3
k=2: C(3,k)=3
k=3: C(3,k)=1
total: 8
verified: True
```
MD,
                'starter_code'        => "n = int(input())\n# Compute binomial coefficients C(n,k) for k=0..n\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Induction proof: verify **Pascal's identity** C(n,k) = C(n−1,k−1) + C(n−1,k) for all valid n and k. Read `N`. For all n from 2 to N and all k from 1 to n−1, print `fail: n=<v> k=<v>` if the identity fails. If all pass, print `all pass`.

Example:
```
Input: 5
Output: all pass
```
MD,
                'starter_code'        => "N = int(input())\n# Verify Pascal's identity for all (n,k) with 2<=n<=N, 1<=k<=n-1\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Prove by induction: **the sum of angles in a convex polygon with n vertices is (n−2)×180 degrees** (triangulation induction). Read `n` (≥ 3). Print the angle sum, the number of triangles needed, and verify.

Format:
```
vertices: <n>
triangles: <n-2>
angle sum: <(n-2)*180>
formula: (n-2)*180 = <value>
verified: True/False
```

Example:
```
Input: 5
Output:
vertices: 5
triangles: 3
angle sum: 540
formula: (n-2)*180 = 540
verified: True
```
MD,
                'starter_code'        => "n = int(input())\n# Polygon with n vertices\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Prove by induction: **a complete graph Kₙ has n(n−1)/2 edges**. Read `n`. Print the number of edges by formula, verify by counting (generate all edges as pairs), and show both match.

Format:
```
formula: n*(n-1)/2 = <value>
counted: <value>
match: True/False
```

Example:
```
Input: 5
Output:
formula: n*(n-1)/2 = 10
counted: 10
match: True
```
MD,
                'starter_code'        => "n = int(input())\n# Count edges in K_n\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Prove by induction: **for all n ≥ 1, 1/(1·2) + 1/(2·3) + ... + 1/(n(n+1)) = n/(n+1)**. Read `N`. For each n from 1 to N, compute the partial sum and the formula value. Print any that fail. If all pass, print `all pass`.

Format per line (only failures): `fail at n=<v> sum=<s> formula=<f>`

Example:
```
Input: 10
Output: all pass
```
MD,
                'starter_code'        => "N = int(input())\n# For n in 1..N: check sum of 1/(k*(k+1)) for k=1..n equals n/(n+1)\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Strong Induction (Q30–Q34)  →  Lesson 322
            // ═══════════════════════════════════════════════════════════════

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Strong induction: **every integer n ≥ 2 can be written as a sum of distinct powers of 2** (binary representation). Read `n`. Print its binary representation as a sorted list of powers of 2 (the set bits), and verify their sum equals n.

Format:
```
powers: <p1> <p2> ...
sum: <n>
verified: True/False
```

Example:
```
Input: 13
Output:
powers: 1 4 8
sum: 13
verified: True
```
MD,
                'starter_code'        => "n = int(input())\n# Decompose n into distinct powers of 2\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Strong induction: compute the number of ways to tile a 2×n board with 1×2 dominoes. This follows the recurrence T(n) = T(n−1) + T(n−2), T(0) = 1, T(1) = 1. Read `n`. Print T(n) and verify it equals the (n+1)-th Fibonacci number.

Format:
```
T(n): <value>
F(n+1): <value>
match: True/False
```

Example:
```
Input: 5
Output:
T(n): 8
F(n+1): 8
match: True
```
MD,
                'starter_code'        => "n = int(input())\n# T(0)=1, T(1)=1, T(n)=T(n-1)+T(n-2)\n# Fib(1)=1,Fib(2)=1,...\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Strong induction: **Zeckendorf's theorem** — every positive integer has a unique representation as a sum of non-consecutive Fibonacci numbers. Read `n`. Find the Zeckendorf representation (greedy: always subtract the largest Fibonacci ≤ n). Print the Fibonacci numbers used and verify their sum equals n.

Format:
```
fibs: <f1> <f2> ...
sum: <n>
verified: True/False
```

Example:
```
Input: 11
Output:
fibs: 8 3
sum: 11
verified: True
```
MD,
                'starter_code'        => "n = int(input())\n# Greedy Zeckendorf decomposition\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Strong induction on the **Sprague-Grundy theorem** (simplified): for a Nim game with a single pile of `n` stones (players alternately remove 1, 2, or 3 stones; last to move wins), compute the Grundy number G(n) for n from 0 to N. G(0) = 0, G(n) = mex{G(n−1), G(n−2), G(n−3)} (mex = minimum excludant). Read `N`. Print G(0) through G(N), one per line.

Example:
```
Input: 7
Output:
0
1
2
3
0
1
2
3
```
MD,
                'starter_code'        => "N = int(input())\n# G(0)=0; G(n) = mex of {G(n-1), G(n-2), G(n-3)} for n>=1\n# mex = smallest non-negative integer not in the set\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Strong induction: **any amount of postage ≥ 12 cents can be formed with 4-cent and 5-cent stamps**. Prove by strong induction: base cases 12=3×4, 13=2×4+1×5, 14=1×4+2×5, 15=3×5. Inductive step: if n ≥ 16, use the representation for n−4 and add one 4-cent stamp. Read `N` (≥ 12). For each value from 12 to N, print `n: <a>x4 + <b>x5 (by induction from <n-4>)` if derived from the inductive step, or `n: <a>x4 + <b>x5 (base case)` for 12–15.

Example:
```
Input: 16
Output:
12: 3x4 + 0x5 (base case)
13: 2x4 + 1x5 (base case)
14: 1x4 + 2x5 (base case)
15: 0x4 + 3x5 (base case)
16: 4x4 + 0x5 (by induction from 12)
```
MD,
                'starter_code'        => "N = int(input())\n# Base cases 12-15, inductive step n -> use representation of n-4\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Existence & Uniqueness (Q35–Q39)  →  Lesson 323
            // ═══════════════════════════════════════════════════════════════

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
**Existence**: prove that for any integer `n`, there exists a prime `p` with n < p ≤ 2n (Bertrand's Postulate). Read `n`. Find ALL primes in the range (n, 2n]. Print them space-separated, then print `count: <k>`.

Format:
```
primes: <p1> <p2> ...
count: <k>
```

Example:
```
Input: 10
Output:
primes: 11 13
count: 2
```
MD,
                'starter_code'        => "n = int(input())\n# Find all primes in range (n, 2*n] inclusive\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
**Uniqueness** of prime factorization (FTA). Read `n`. Produce two different orderings of the prime factorization (sorted ascending and sorted descending) and verify they represent the same multiset (proving the factorization is unique up to ordering). Print both orderings.

Format:
```
ascending: <factors>
descending: <factors>
same multiset: True/False
```

Example:
```
Input: 360
Output:
ascending: 2 2 2 3 3 5
descending: 5 3 3 2 2 2
same multiset: True
```
MD,
                'starter_code'        => "n = int(input())\n# Factorize n and produce two orderings\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
**Existence and uniqueness**: prove that for each n, there is a unique way to write n = 2^k · m where m is odd. Read `n`. Find k and m. Print them and verify 2^k × m = n.

Format:
```
k: <value>
m: <value>
2^k * m: <value>
verified: True/False
```

Example:
```
Input: 24
Output:
k: 3
m: 3
2^k * m: 24
verified: True
```
MD,
                'starter_code'        => "n = int(input())\n# Factor out all 2s from n\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
**Uniqueness** of the greatest common divisor: prove that if d₁ and d₂ are both "greatest common divisors" of a and b (satisfy the same two conditions), then d₁ = d₂. Read `a` and `b`. Compute gcd using the Euclidean algorithm. Then verify that no other positive divisor d ≠ gcd satisfies d | a, d | b, AND for every c with c | a and c | b, c | d. Print the GCD and the count of common divisors, and confirm the GCD is the largest.

Format:
```
gcd: <value>
all common divisors: <list sorted ascending>
gcd is unique largest: True/False
```

Example:
```
Input:
48
36
Output:
gcd: 12
all common divisors: 1 2 3 4 6 12
gcd is unique largest: True
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Compute GCD and all common divisors\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
**Existence**: prove that every quadratic congruence x² ≡ a (mod p) for prime p and gcd(a,p)=1 has either 0 or 2 solutions in {0, 1, …, p−1} (Euler's criterion: a^((p-1)/2) ≡ 1 (mod p) iff a is a quadratic residue). Read prime `p` and `a`. Compute the Legendre symbol using Euler's criterion. Find all solutions to x² ≡ a (mod p). Print the solutions and the Legendre symbol.

Format:
```
legendre: <1, -1, or 0>
solutions: <x1 x2 or 'none'>
```

Example:
```
Input:
7
2
Output:
legendre: 1
solutions: 3 4
```
MD,
                'starter_code'        => "p = int(input())\na = int(input())\n# Compute pow(a, (p-1)//2, p) for Legendre symbol\n# Find all x in range(p) with x*x % p == a % p\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Counterexamples (Q40–Q44)  →  Lesson 324
            // ═══════════════════════════════════════════════════════════════

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Find counterexamples (or confirm) for these four claims over integers 2 to `N`. For each, print the first counterexample or `none`:

1. "All primes are of the form 6k ± 1"  (TRUE for primes > 3, look for exception ≤ 5)
2. "n² − n + 41 is prime for all n ≥ 0"
3. "2^(2^n) + 1 is always prime (Fermat primes)" — check for n=0..5
4. "The sum of two primes is always even"

Format:
```
claim1: <n or 'none'>
claim2: <n or 'none'>
claim3: <n or 'none'>
claim4: <pair or 'none'>
```

Example:
```
Input: 50
Output:
claim1: 2
claim2: none
claim3: 5
claim4: 2 3
```
MD,
                'starter_code'        => "N = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Find the **minimum counterexample** to the claim: "Every planar graph is 3-colorable." (In fact 4 colors suffice by the four-color theorem, but 3 are NOT always enough.) Read a graph as `n` vertices and `m` edges. Attempt to 3-color it using backtracking. Print `3-colorable: True` with an assignment, or `3-colorable: False` if it requires 4 colors. (For small graphs with n ≤ 10.)

Format:
```
3-colorable: True/False
coloring: v1=<c> v2=<c> ... (or 'N/A')
```

Example:
```
Input:
4
6
1 2
1 3
1 4
2 3
2 4
3 4
Output:
3-colorable: False
coloring: N/A
```
MD,
                'starter_code'        => "n = int(input())\nm = int(input())\nedges = [tuple(map(int, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Find a counterexample to the **converse of Fermat's Little Theorem**: "if aⁿ⁻¹ ≡ 1 (mod n) for all a coprime to n, then n is prime." Read `N`. Find the smallest **Carmichael number** (composite n that fools Fermat's test) ≤ N. Print it or `none found`.

The smallest Carmichael numbers are 561, 1105, 1729. A Carmichael number n satisfies: n is composite AND for all a with gcd(a,n)=1: aⁿ⁻¹ ≡ 1 (mod n).

Format: `carmichael: <n or 'none found'>`

Example:
```
Input: 600
Output: carmichael: 561
```
MD,
                'starter_code'        => "import math\nN = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Disprove the claim: **"In any graph, a vertex of maximum degree is adjacent to all other high-degree vertices."** Read a graph. Find the vertex with maximum degree. Check if there exists another high-degree vertex (degree ≥ max_degree − 1) not adjacent to it. Print the counterexample pair or `claim holds`.

Format:
```
max_degree_vertex: <v> (degree=<d>)
counterexample: <u> (degree=<d>) not adjacent to <v>
```
or `claim holds`.

Example:
```
Input:
5
4
1 2
1 3
1 4
2 5
Output:
max_degree_vertex: 1 (degree=3)
counterexample: 2 (degree=2) not adjacent to 1
```

Note: vertex 2 has degree 2 which is ≥ max_degree − 1 = 2, and we check non-adjacency among all pairs.
MD,
                'starter_code'        => "n = int(input())\nm = int(input())\nedges = [tuple(map(int, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Disprove: **"Every bijection from a finite set to itself is an involution (f∘f = identity)"**. Read `n` and a permutation p of {1..n} (one per line). Check if p is a bijection (it should be). Check if p∘p is the identity. Print `involution: True/False`. If False, find and print a cycle of length > 2 as a counterexample.

Format:
```
involution: True/False
cycle: <c1> <c2> ... (or 'N/A')
```

Example:
```
Input:
4
2
3
4
1
Output:
involution: False
cycle: 1 2 3 4
```
MD,
                'starter_code'        => "n = int(input())\nperm = [int(input()) for _ in range(n)]\n# perm[i] gives image of (i+1)\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Choosing Strategy & Capstone (Q45–Q50) → Lesson 325
            // ═══════════════════════════════════════════════════════════════

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
**Strategy selection**: Read `n` mathematical claims (each as a claim code and parameter). For each, apply the most appropriate proof technique and print the result and technique used.

Claims:
1. `DIRECT n` — verify 6|n(n+1)(n+2)
2. `CONTRA n` — verify contrapositive of n² even → n even
3. `CONTRA2 n` — verify contrapositive of 3|n² → 3|n
4. `INDUCT n` — verify sum formula for cubes up to n
5. `EXIST n`  — find a prime in (n, 2n]

For each claim print: `<type>: result=<True/value> technique=<name>`

Example:
```
Input:
2
DIRECT 5
EXIST 10
Output:
DIRECT: result=True technique=direct_proof
EXIST: result=11 technique=existence
```
MD,
                'starter_code'        => "n = int(input())\nclaims = [input().split() for _ in range(n)]\n# Route each claim to the correct technique\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Prove by induction that **a binary tree with n internal nodes has n+1 leaves**. Read `n` (number of internal nodes of a full binary tree). Build the tree structure (children of node i are 2i and 2i+1, root=1, nodes 1..n are internal, nodes n+1..2n+1 are leaves). Count leaves and verify the count equals n+1.

Format:
```
internal nodes: <n>
leaves counted: <value>
n+1: <value>
verified: True/False
```

Example:
```
Input: 4
Output:
internal nodes: 4
leaves counted: 5
n+1: 5
verified: True
```
MD,
                'starter_code'        => "n = int(input())\n# Build full binary tree: internal nodes 1..n, leaves (n+1)..(2n+1)\n# Count leaf nodes\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Prove by induction: **the chromatic polynomial of a tree Tₙ is k(k−1)^(n−1)**. Read `n` (vertices) and a tree as n−1 edges. For a given number of colours `k` (last input line), verify the chromatic polynomial by counting valid colourings via brute-force (for small n ≤ 8) and comparing to the formula.

Format:
```
formula: k*(k-1)^(n-1) = <value>
brute_force: <value>
match: True/False
```

Example:
```
Input:
4
1 2
2 3
3 4
3
Output:
formula: k*(k-1)^(n-1) = 18
brute_force: 18
match: True
```
MD,
                'starter_code'        => "n = int(input())\nedges = [tuple(map(int, input().split())) for _ in range(n - 1)]\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Mixed proof: **Handshaking lemma** — in any graph, the sum of all vertex degrees equals twice the number of edges. Read a graph as `n` vertices and `m` edges. Compute the degree of each vertex, sum the degrees, and verify sum = 2m. Also check the corollary: the number of odd-degree vertices is always even.

Format:
```
sum of degrees: <value>
2 * edges: <value>
handshaking holds: True/False
odd degree count: <value>
odd count even: True/False
```

Example:
```
Input:
4
5
1 2
1 3
2 3
2 4
3 4
Output:
sum of degrees: 10
2 * edges: 10
handshaking holds: True
odd degree count: 0
odd count even: True
```
MD,
                'starter_code'        => "n = int(input())\nm = int(input())\nedges = [tuple(map(int, input().split())) for _ in range(m)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
**Capstone — Proof by contradiction + strong induction**: prove that in any sequence of n² + 1 distinct real numbers, there is either an increasing subsequence of length n+1 or a decreasing subsequence of length n+1 (Erdős–Szekeres theorem). Read n² + 1 distinct integers. For each element, compute the length of the longest increasing subsequence (LIS) and longest decreasing subsequence (LDS) ending at that element. Print `increasing found: <subseq>` if LIS ≥ n+1, or `decreasing found: <subseq>` if LDS ≥ n+1.

Example:
```
Input:
5
3
1
4
2
5
Output: increasing found: 1 4 5
```
(Here n=2 since n²+1=5 → n=2, so we need length 3.)
MD,
                'starter_code'        => "import math\nnums = []\nwhile True:\n    try:\n        nums.append(int(input()))\n    except EOFError:\n        break\nn = int(math.isqrt(len(nums) - 1))\n# Compute LIS and LDS for each element\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**Grand proof portfolio**: read an integer `n` and run the following five proof-technique verifications, printing results for each:

1. **Direct** (Fermat): compute n^(p−1) mod p for the first prime p > n; verify ≡ 1
2. **Contrapositive**: if n is not squarefree (has a squared prime factor), print its squared factor; else print `squarefree`
3. **Contradiction**: show the harmonic series H(n) > ln(n)/2 (lower bound from grouping argument), print `H(n)=<v> > ln(n)/2=<v>: True`
4. **Induction**: verify ∑ᵢ₌₁ⁿ i³ = (n(n+1)/2)²
5. **Existence**: find a twin prime pair (p, p+2) with p > n, or `none in range` if none found within 2n of n

Format one per line, labelled.

Example:
```
Input: 5
Output:
direct: 5^6 mod 7 = 1 holds=True
contrapositive: squarefree
contradiction: H(5)=2.283333 > ln(5)/2=0.804719: True
induction: sum_cubes=225 formula=225 match=True
existence: twin prime (11, 13)
```
MD,
                'starter_code'        => "import math\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 500,
            ],

        ]; // end $questionDefs

        // ─────────────────────────────────────────────────────────────────
        // 3. PERSIST QUESTIONS
        // ─────────────────────────────────────────────────────────────────

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
        // 4. TEST CASES
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $seed = function (int $qIdx, array $cases) use ($questions): void {
            $qId = $questions[$qIdx] ?? null;
            if (! $qId) return;

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

        // ── Q1: Tautology/contradiction/contingency ───────────────────────
        $seed(1, [
            ['input' => "1\n1\n1\n1\n1\n1\n1\n1",    'expected_output' => "tautology\nsatisfying: 8",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n0\n0\n0\n0\n0\n0\n0",    'expected_output' => "contradiction\nsatisfying: 0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0\n1\n0\n1\n0\n1\n0",    'expected_output' => "contingency\nsatisfying: 4",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1\n1\n1\n0\n0\n0\n0",    'expected_output' => "contingency\nsatisfying: 4",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: SAT solver ────────────────────────────────────────────────
        $seed(2, [
            ['input' => "2\n2\n1 -2\n-1 2",       'expected_output' => "SAT\nx1=1 x2=1",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n2\n1\n-1",            'expected_output' => 'UNSAT',              'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n1 2",              'expected_output' => "SAT\nx1=1 x2=1",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n2\n1 2\n-1 -2",       'expected_output' => "SAT\nx1=1 x2=0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Argument validity ─────────────────────────────────────────
        $seed(3, [
            ['input' => "3\n1\n1\n1\n0",    'expected_output' => "invalid\ncounterexample found",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n1\n1",       'expected_output' => "valid",                           'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0\n1\n0",       'expected_output' => "valid",                           'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0\n1",          'expected_output' => "valid",                           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Pairwise coprime ──────────────────────────────────────────
        $seed(4, [
            ['input' => "3\n4\n9\n25",       'expected_output' => 'pairwise coprime',                              'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n4\n6\n9",        'expected_output' => 'not pairwise coprime\n(4, 6) gcd=2',            'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5\n7\n11",       'expected_output' => 'pairwise coprime',                              'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n6\n10\n15",      'expected_output' => 'not pairwise coprime\n(6, 10) gcd=2',           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Divisibility chain ────────────────────────────────────────
        $seed(5, [
            ['input' => "4\n2\n6\n18\n54",   'expected_output' => "a1 | a2: True\na2 | a3: True\na3 | a4: True\nchain valid: True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2\n4\n9",        'expected_output' => "a1 | a2: True\na2 | a3: False\nchain valid: False",                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n3\n9\n27",    'expected_output' => "a1 | a2: True\na2 | a3: True\na3 | a4: True\nchain valid: True",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n4\n8\n15",       'expected_output' => "a1 | a2: True\na2 | a3: False\nchain valid: False",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Fermat's Little Theorem ───────────────────────────────────
        $seed(6, [
            ['input' => "7\n3",     'expected_output' => "a^(p-1) mod p: 1\ntheorem holds: True",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "11\n5",    'expected_output' => "a^(p-1) mod p: 1\ntheorem holds: True",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "13\n7",    'expected_output' => "a^(p-1) mod p: 1\ntheorem holds: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "17\n4",    'expected_output' => "a^(p-1) mod p: 1\ntheorem holds: True",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Wilson's theorem ──────────────────────────────────────────
        $seed(7, [
            ['input' => '7',     'expected_output' => "(p-1)! mod p: 6\nwilson holds: True\np is prime: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '11',    'expected_output' => "(p-1)! mod p: 10\nwilson holds: True\np is prime: True",   'is_hidden' => false, 'order_index' => 2],
            ['input' => '4',     'expected_output' => "(p-1)! mod p: 2\nwilson holds: False\np is prime: False",  'is_hidden' => true,  'order_index' => 3],
            ['input' => '13',    'expected_output' => "(p-1)! mod p: 12\nwilson holds: True\np is prime: True",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: a²+b² mod 4 ──────────────────────────────────────────────
        $seed(8, [
            ['input' => "2\n2 4\n1 3",       'expected_output' => "a=2 b=4 a^2+b^2 mod4=0 both_even=True claim_holds=True\na=1 b=3 a^2+b^2 mod4=2 both_even=False claim_holds=True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n3 5",            'expected_output' => "a=3 b=5 a^2+b^2 mod4=2 both_even=False claim_holds=True",                                                               'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n6 8\n1 5",       'expected_output' => "a=6 b=8 a^2+b^2 mod4=0 both_even=True claim_holds=True\na=1 b=5 a^2+b^2 mod4=2 both_even=False claim_holds=True",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n4 6",            'expected_output' => "a=4 b=6 a^2+b^2 mod4=0 both_even=True claim_holds=True",                                                               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Pigeonhole ────────────────────────────────────────────────
        $seed(9, [
            ['input' => "4\n3\n2\n1\n2\n3",    'expected_output' => 'pigeonhole: container 2 has items 2 4',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2\n1\n2\n3",       'expected_output' => 'pigeonhole: container 1 has items 1 3',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5\n1\n2\n3",       'expected_output' => 'no pigeonhole',                            'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n2\n1\n2\n1\n2\n1", 'expected_output' => 'pigeonhole: container 1 has items 1 3 5', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Euler's theorem ──────────────────────────────────────────
        $seed(10, [
            ['input' => "3\n10",    'expected_output' => "phi(n): 4\na^phi(n) mod n: 1\neuler holds: True",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n6",     'expected_output' => "phi(n): 2\na^phi(n) mod n: 1\neuler holds: True",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n12",    'expected_output' => "phi(n): 4\na^phi(n) mod n: 1\neuler holds: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n9",     'expected_output' => "phi(n): 6\na^phi(n) mod n: 1\neuler holds: True",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Prime factor ≤ √n ────────────────────────────────────────
        $seed(11, [
            ['input' => "3\n7\n12\n25",     'expected_output' => "7: prime\n12: composite, factor=2\n25: composite, factor=5",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2\n9",          'expected_output' => "2: prime\n9: composite, factor=3",                                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n11\n49\n100",   'expected_output' => "11: prime\n49: composite, factor=7\n100: composite, factor=2",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n97",            'expected_output' => "97: prime",                                                            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Constant sequence ────────────────────────────────────────
        $seed(12, [
            ['input' => "4\n3\n3\n3\n3",    'expected_output' => "constant: True\nhas adjacent equal: True\nconsistent: True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",       'expected_output' => "constant: False\nhas adjacent equal: False\nconsistent: True",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5\n5\n6",       'expected_output' => "constant: False\nhas adjacent equal: True\nconsistent: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n7",             'expected_output' => "constant: True\nhas adjacent equal: False\nconsistent: True",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Eulerian circuit ─────────────────────────────────────────
        $seed(13, [
            ['input' => "4\n4\n1 2\n2 3\n3 4\n4 1",           'expected_output' => "degrees: v1=2 v2=2 v3=2 v4=2\nodd degree vertices: none\neulerian circuit possible: True",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3\n1 2\n2 3\n1 3",               'expected_output' => "degrees: v1=2 v2=2 v3=2\nodd degree vertices: none\neulerian circuit possible: True",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2\n1 2\n2 3",                    'expected_output' => "degrees: v1=1 v2=2 v3=1\nodd degree vertices: 1 3\neulerian circuit possible: False",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n6\n1 2\n1 3\n1 4\n2 3\n2 4\n3 4",'expected_output' => "degrees: v1=3 v2=3 v3=3 v4=3\nodd degree vertices: 1 2 3 4\neulerian circuit possible: False",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Perfect square vs irrational ────────────────────────────
        $seed(14, [
            ['input' => "4\n9\n7\n25\n15",      'expected_output' => "9: perfect square (sqrt=3)\n7: not perfect square (irrational sqrt)\n25: perfect square (sqrt=5)\n15: not perfect square (irrational sqrt)",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n4\n5",              'expected_output' => "4: perfect square (sqrt=2)\n5: not perfect square (irrational sqrt)",                                                                         'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3",           'expected_output' => "1: perfect square (sqrt=1)\n2: not perfect square (irrational sqrt)\n3: not perfect square (irrational sqrt)",                               'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n36\n37",            'expected_output' => "36: perfect square (sqrt=6)\n37: not perfect square (irrational sqrt)",                                                                       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Infinitely many primes 4k+3 ─────────────────────────────
        $seed(15, [
            ['input' => "2\n3\n7",     'expected_output' => "N: 83\nnew prime (4k+3): 83",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n3",        'expected_output' => "N: 11\nnew prime (4k+3): 11",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n3\n7\n11", 'expected_output' => "N: 923\nnew prime (4k+3): 71",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n3\n11",    'expected_output' => "N: 131\nnew prime (4k+3): 131",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: √p irrational ────────────────────────────────────────────
        $seed(16, [
            ['input' => "5\n20",    'expected_output' => "exact fraction: none\nclosest: 9/4 = 2.25000000",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10",    'expected_output' => "exact fraction: none\nclosest: 7/4 = 1.75000000",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n10",    'expected_output' => "exact fraction: none\nclosest: 8/3 = 2.66666667",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n20",    'expected_output' => "exact fraction: none\nclosest: 7/5 = 1.40000000",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Tree verification ────────────────────────────────────────
        $seed(17, [
            ['input' => "4\n3\n1 2\n2 3\n3 4",       'expected_output' => "tree: True\nedges: 3\nvertices: 4\nn-1: 3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3\n1 2\n2 3\n1 3",       'expected_output' => "not a tree: cycle detected",                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n2\n1 2\n3 4",            'expected_output' => "not a tree: disconnected",                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n4\n1 2\n2 3\n3 4\n4 5",  'expected_output' => "tree: True\nedges: 4\nvertices: 5\nn-1: 4",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Harmonic series lower bound ──────────────────────────────
        $seed(18, [
            ['input' => '8',      'expected_output' => "partial_sum: 2.717857\ngroups: 3\nlower_bound: 1.500000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',      'expected_output' => "partial_sum: 2.083333\ngroups: 2\nlower_bound: 1.000000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => '16',     'expected_output' => "partial_sum: 3.380728\ngroups: 4\nlower_bound: 2.000000",    'is_hidden' => true,  'order_index' => 3],
            ['input' => '2',      'expected_output' => "partial_sum: 1.500000\ngroups: 1\nlower_bound: 0.500000",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Terminating decimal ──────────────────────────────────────
        $seed(19, [
            ['input' => '40',    'expected_output' => "a: 3\nb: 1\nremainder: 1\nterminates: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '7',     'expected_output' => "a: 0\nb: 0\nremainder: 7\nterminates: False",   'is_hidden' => false, 'order_index' => 2],
            ['input' => '25',    'expected_output' => "a: 0\nb: 2\nremainder: 1\nterminates: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => '12',    'expected_output' => "a: 2\nb: 0\nremainder: 3\nterminates: False",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: n⁴ mod 5 ─────────────────────────────────────────────────
        $seed(20, [
            ['input' => "3\n3\n4\n5",         'expected_output' => "n=3 n mod 5=3 n^4 mod 5=1 valid=True\nn=4 n mod 5=4 n^4 mod 5=1 valid=True\nn=5 n mod 5=0 n^4 mod 5=0 valid=True",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n2",            'expected_output' => "n=1 n mod 5=1 n^4 mod 5=1 valid=True\nn=2 n mod 5=2 n^4 mod 5=1 valid=True",                                             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n6\n7\n10",        'expected_output' => "n=6 n mod 5=1 n^4 mod 5=1 valid=True\nn=7 n mod 5=2 n^4 mod 5=1 valid=True\nn=10 n mod 5=0 n^4 mod 5=0 valid=True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0",               'expected_output' => "n=0 n mod 5=0 n^4 mod 5=0 valid=True",                                                                                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: n² mod 3 ─────────────────────────────────────────────────
        $seed(21, [
            ['input' => "3\n7\n8\n9",          'expected_output' => "n=7 n mod 3=1 n^2 mod 3=1 valid=True\nn=8 n mod 3=2 n^2 mod 3=1 valid=True\nn=9 n mod 3=0 n^2 mod 3=0 valid=True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n2",             'expected_output' => "n=1 n mod 3=1 n^2 mod 3=1 valid=True\nn=2 n mod 3=2 n^2 mod 3=1 valid=True",                                            'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n10\n11\n12",       'expected_output' => "n=10 n mod 3=1 n^2 mod 3=1 valid=True\nn=11 n mod 3=2 n^2 mod 3=1 valid=True\nn=12 n mod 3=0 n^2 mod 3=0 valid=True", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0",                'expected_output' => "n=0 n mod 3=0 n^2 mod 3=0 valid=True",                                                                                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: 6-case exhaustion ─────────────────────────────────────────
        $seed(22, [
            ['input' => '7',     'expected_output' => "n mod 6: 1\nproduct: 20160\ndivisible by 720: True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '5',     'expected_output' => "n mod 6: 5\nproduct: 15120\ndivisible by 720: True",     'is_hidden' => false, 'order_index' => 2],
            ['input' => '12',    'expected_output' => "n mod 6: 0\nproduct: 3991680\ndivisible by 720: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',     'expected_output' => "n mod 6: 1\nproduct: 720\ndivisible by 720: True",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: max/min identities ───────────────────────────────────────
        $seed(23, [
            ['input' => "2\n3 7\n5 2",     'expected_output' => "a=3 b=7 max+min=10 a+b=10 id1=True max-min=4 |a-b|=4 id2=True\na=5 b=2 max+min=7 a+b=7 id1=True max-min=3 |a-b|=3 id2=True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0 0",          'expected_output' => "a=0 b=0 max+min=0 a+b=0 id1=True max-min=0 |a-b|=0 id2=True",                                                                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n-3 3\n10 1",   'expected_output' => "a=-3 b=3 max+min=0 a+b=0 id1=True max-min=6 |a-b|=6 id2=True\na=10 b=1 max+min=11 a+b=11 id1=True max-min=9 |a-b|=9 id2=True",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n7 7",          'expected_output' => "a=7 b=7 max+min=14 a+b=14 id1=True max-min=0 |a-b|=0 id2=True",                                                                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: n²+n always even ─────────────────────────────────────────
        $seed(24, [
            ['input' => "3\n4\n7\n0",        'expected_output' => "n=4 parity=even n^2+n=20 even=True n^2+n+1=21 odd=True\nn=7 parity=odd n^2+n=56 even=True n^2+n+1=57 odd=True\nn=0 parity=even n^2+n=0 even=True n^2+n+1=1 odd=True",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n2",           'expected_output' => "n=1 parity=odd n^2+n=2 even=True n^2+n+1=3 odd=True\nn=2 parity=even n^2+n=6 even=True n^2+n+1=7 odd=True",                                                             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n10\n11",         'expected_output' => "n=10 parity=even n^2+n=110 even=True n^2+n+1=111 odd=True\nn=11 parity=odd n^2+n=132 even=True n^2+n+1=133 odd=True",                                                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5",              'expected_output' => "n=5 parity=odd n^2+n=30 even=True n^2+n+1=31 odd=True",                                                                                                                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Subsets = 2^n ────────────────────────────────────────────
        $seed(25, [
            ['input' => '3',    'expected_output' => "k=0: C(3,k)=1\nk=1: C(3,k)=3\nk=2: C(3,k)=3\nk=3: C(3,k)=1\ntotal: 8\nverified: True",                 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',    'expected_output' => "k=0: C(2,k)=1\nk=1: C(2,k)=2\nk=2: C(2,k)=1\ntotal: 4\nverified: True",                                 'is_hidden' => false, 'order_index' => 2],
            ['input' => '4',    'expected_output' => "k=0: C(4,k)=1\nk=1: C(4,k)=4\nk=2: C(4,k)=6\nk=3: C(4,k)=4\nk=4: C(4,k)=1\ntotal: 16\nverified: True", 'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',    'expected_output' => "k=0: C(1,k)=1\nk=1: C(1,k)=1\ntotal: 2\nverified: True",                                                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Pascal's identity ────────────────────────────────────────
        $seed(26, [
            ['input' => '5',     'expected_output' => 'all pass',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',     'expected_output' => 'all pass',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',    'expected_output' => 'all pass',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '2',     'expected_output' => 'all pass',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Polygon angle sum ─────────────────────────────────────────
        $seed(27, [
            ['input' => '5',     'expected_output' => "vertices: 5\ntriangles: 3\nangle sum: 540\nformula: (n-2)*180 = 540\nverified: True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',     'expected_output' => "vertices: 3\ntriangles: 1\nangle sum: 180\nformula: (n-2)*180 = 180\nverified: True",     'is_hidden' => false, 'order_index' => 2],
            ['input' => '8',     'expected_output' => "vertices: 8\ntriangles: 6\nangle sum: 1080\nformula: (n-2)*180 = 1080\nverified: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '6',     'expected_output' => "vertices: 6\ntriangles: 4\nangle sum: 720\nformula: (n-2)*180 = 720\nverified: True",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: K_n edges ────────────────────────────────────────────────
        $seed(28, [
            ['input' => '5',     'expected_output' => "formula: n*(n-1)/2 = 10\ncounted: 10\nmatch: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',     'expected_output' => "formula: n*(n-1)/2 = 6\ncounted: 6\nmatch: True",     'is_hidden' => false, 'order_index' => 2],
            ['input' => '7',     'expected_output' => "formula: n*(n-1)/2 = 21\ncounted: 21\nmatch: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '2',     'expected_output' => "formula: n*(n-1)/2 = 1\ncounted: 1\nmatch: True",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Telescoping series ───────────────────────────────────────
        $seed(29, [
            ['input' => '10',    'expected_output' => 'all pass',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '5',     'expected_output' => 'all pass',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '20',    'expected_output' => 'all pass',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',     'expected_output' => 'all pass',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Binary representation ────────────────────────────────────
        $seed(30, [
            ['input' => '13',    'expected_output' => "powers: 1 4 8\nsum: 13\nverified: True",        'is_hidden' => false, 'order_index' => 1],
            ['input' => '7',     'expected_output' => "powers: 1 2 4\nsum: 7\nverified: True",         'is_hidden' => false, 'order_index' => 2],
            ['input' => '20',    'expected_output' => "powers: 4 16\nsum: 20\nverified: True",          'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',     'expected_output' => "powers: 1\nsum: 1\nverified: True",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Domino tiling ────────────────────────────────────────────
        $seed(31, [
            ['input' => '5',    'expected_output' => "T(n): 8\nF(n+1): 8\nmatch: True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',    'expected_output' => "T(n): 3\nF(n+1): 3\nmatch: True",     'is_hidden' => false, 'order_index' => 2],
            ['input' => '7',    'expected_output' => "T(n): 21\nF(n+1): 21\nmatch: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',    'expected_output' => "T(n): 1\nF(n+1): 1\nmatch: True",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Zeckendorf ───────────────────────────────────────────────
        $seed(32, [
            ['input' => '11',    'expected_output' => "fibs: 8 3\nsum: 11\nverified: True",      'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',    'expected_output' => "fibs: 8 2\nsum: 10\nverified: True",      'is_hidden' => false, 'order_index' => 2],
            ['input' => '20',    'expected_output' => "fibs: 13 5 2\nsum: 20\nverified: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',     'expected_output' => "fibs: 1\nsum: 1\nverified: True",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Sprague-Grundy / Nim ─────────────────────────────────────
        $seed(33, [
            ['input' => '7',    'expected_output' => "0\n1\n2\n3\n0\n1\n2\n3",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',    'expected_output' => "0\n1\n2\n3",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => '11',   'expected_output' => "0\n1\n2\n3\n0\n1\n2\n3\n0\n1\n2\n3",  'is_hidden' => true, 'order_index' => 3],
            ['input' => '0',    'expected_output' => "0",                            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Stamp induction ──────────────────────────────────────────
        $seed(34, [
            ['input' => '16',    'expected_output' => "12: 3x4 + 0x5 (base case)\n13: 2x4 + 1x5 (base case)\n14: 1x4 + 2x5 (base case)\n15: 0x4 + 3x5 (base case)\n16: 4x4 + 0x5 (by induction from 12)",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '12',    'expected_output' => "12: 3x4 + 0x5 (base case)",                                                                                                                                'is_hidden' => false, 'order_index' => 2],
            ['input' => '18',    'expected_output' => "12: 3x4 + 0x5 (base case)\n13: 2x4 + 1x5 (base case)\n14: 1x4 + 2x5 (base case)\n15: 0x4 + 3x5 (base case)\n16: 4x4 + 0x5 (by induction from 12)\n17: 3x4 + 1x5 (by induction from 13)\n18: 2x4 + 2x5 (by induction from 14)",  'is_hidden' => true, 'order_index' => 3],
            ['input' => '13',    'expected_output' => "12: 3x4 + 0x5 (base case)\n13: 2x4 + 1x5 (base case)",                                                                                                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Bertrand's Postulate (all primes) ─────────────────────────
        $seed(35, [
            ['input' => '10',    'expected_output' => "primes: 11 13\ncount: 2",      'is_hidden' => false, 'order_index' => 1],
            ['input' => '5',     'expected_output' => "primes: 7\ncount: 1",          'is_hidden' => false, 'order_index' => 2],
            ['input' => '20',    'expected_output' => "primes: 23 29 31 37\ncount: 4", 'is_hidden' => true, 'order_index' => 3],
            ['input' => '2',     'expected_output' => "primes: 3\ncount: 1",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Unique factorization ─────────────────────────────────────
        $seed(36, [
            ['input' => '360',    'expected_output' => "ascending: 2 2 2 3 3 5\ndescending: 5 3 3 2 2 2\nsame multiset: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '12',     'expected_output' => "ascending: 2 2 3\ndescending: 3 2 2\nsame multiset: True",                'is_hidden' => false, 'order_index' => 2],
            ['input' => '7',      'expected_output' => "ascending: 7\ndescending: 7\nsame multiset: True",                        'is_hidden' => true,  'order_index' => 3],
            ['input' => '100',    'expected_output' => "ascending: 2 2 5 5\ndescending: 5 5 2 2\nsame multiset: True",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: n = 2^k * m ─────────────────────────────────────────────
        $seed(37, [
            ['input' => '24',    'expected_output' => "k: 3\nm: 3\n2^k * m: 24\nverified: True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '12',    'expected_output' => "k: 2\nm: 3\n2^k * m: 12\nverified: True",     'is_hidden' => false, 'order_index' => 2],
            ['input' => '7',     'expected_output' => "k: 0\nm: 7\n2^k * m: 7\nverified: True",      'is_hidden' => true,  'order_index' => 3],
            ['input' => '32',    'expected_output' => "k: 5\nm: 1\n2^k * m: 32\nverified: True",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: GCD uniqueness ───────────────────────────────────────────
        $seed(38, [
            ['input' => "48\n36",    'expected_output' => "gcd: 12\nall common divisors: 1 2 3 4 6 12\ngcd is unique largest: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "12\n8",     'expected_output' => "gcd: 4\nall common divisors: 1 2 4\ngcd is unique largest: True",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n13",     'expected_output' => "gcd: 1\nall common divisors: 1\ngcd is unique largest: True",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "30\n18",    'expected_output' => "gcd: 6\nall common divisors: 1 2 3 6\ngcd is unique largest: True",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Quadratic residues ────────────────────────────────────────
        $seed(39, [
            ['input' => "7\n2",     'expected_output' => "legendre: 1\nsolutions: 3 4",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "7\n3",     'expected_output' => "legendre: -1\nsolutions: none",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "11\n4",    'expected_output' => "legendre: 1\nsolutions: 2 9",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "13\n5",    'expected_output' => "legendre: -1\nsolutions: none",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Four counterexample searches ────────────────────────────
        $seed(40, [
            ['input' => '50',    'expected_output' => "claim1: 2\nclaim2: none\nclaim3: 5\nclaim4: 2 3",      'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',     'expected_output' => "claim1: none\nclaim2: none\nclaim3: none\nclaim4: none", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',    'expected_output' => "claim1: 2\nclaim2: none\nclaim3: 5\nclaim4: 2 3",      'is_hidden' => true,  'order_index' => 3],
            ['input' => '3',     'expected_output' => "claim1: 2\nclaim2: none\nclaim3: none\nclaim4: 2 3",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: 3-coloring ───────────────────────────────────────────────
        $seed(41, [
            ['input' => "4\n6\n1 2\n1 3\n1 4\n2 3\n2 4\n3 4",  'expected_output' => "3-colorable: False\ncoloring: N/A",                    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3\n1 2\n2 3\n1 3",                  'expected_output' => "3-colorable: True\ncoloring: v1=1 v2=2 v3=3",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4\n1 2\n2 3\n3 4\n4 1",            'expected_output' => "3-colorable: True\ncoloring: v1=1 v2=2 v3=1 v4=2",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2\n1 2\n2 3",                       'expected_output' => "3-colorable: True\ncoloring: v1=1 v2=2 v3=1",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Carmichael number ─────────────────────────────────────────
        $seed(42, [
            ['input' => '600',     'expected_output' => 'carmichael: 561',        'is_hidden' => false, 'order_index' => 1],
            ['input' => '500',     'expected_output' => 'carmichael: none found', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '1200',    'expected_output' => 'carmichael: 561',        'is_hidden' => true,  'order_index' => 3],
            ['input' => '1110',    'expected_output' => 'carmichael: 561',        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Max degree adjacency ─────────────────────────────────────
        $seed(43, [
            ['input' => "5\n4\n1 2\n1 3\n1 4\n2 5",    'expected_output' => "max_degree_vertex: 1 (degree=3)\ncounterexample: 2 (degree=2) not adjacent to 1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3\n1 2\n2 3\n1 3",         'expected_output' => 'claim holds',                                                                        'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n3\n1 2\n1 3\n1 4",         'expected_output' => "max_degree_vertex: 1 (degree=3)\ncounterexample: 2 (degree=1) not adjacent to 1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1\n1 2",                    'expected_output' => 'claim holds',                                                                        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Involution ───────────────────────────────────────────────
        $seed(44, [
            ['input' => "4\n2\n3\n4\n1",     'expected_output' => "involution: False\ncycle: 1 2 3 4",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n2\n1\n4\n3",     'expected_output' => "involution: True\ncycle: N/A",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2\n3\n1",        'expected_output' => "involution: False\ncycle: 1 2 3",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n2\n1",           'expected_output' => "involution: True\ncycle: N/A",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Strategy selector ────────────────────────────────────────
        $seed(45, [
            ['input' => "2\nDIRECT 5\nEXIST 10",       'expected_output' => "DIRECT: result=True technique=direct_proof\nEXIST: result=11 technique=existence",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nCONTRA 4\nINDUCT 3",       'expected_output' => "CONTRA: result=True technique=contrapositive\nINDUCT: result=True technique=induction", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nDIRECT 7",                 'expected_output' => "DIRECT: result=True technique=direct_proof",                                             'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nEXIST 20",                 'expected_output' => "EXIST: result=23 technique=existence",                                                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Binary tree leaves ───────────────────────────────────────
        $seed(46, [
            ['input' => '4',    'expected_output' => "internal nodes: 4\nleaves counted: 5\nn+1: 5\nverified: True",      'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',    'expected_output' => "internal nodes: 1\nleaves counted: 2\nn+1: 2\nverified: True",      'is_hidden' => false, 'order_index' => 2],
            ['input' => '7',    'expected_output' => "internal nodes: 7\nleaves counted: 8\nn+1: 8\nverified: True",      'is_hidden' => true,  'order_index' => 3],
            ['input' => '3',    'expected_output' => "internal nodes: 3\nleaves counted: 4\nn+1: 4\nverified: True",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Chromatic polynomial of tree ─────────────────────────────
        $seed(47, [
            ['input' => "4\n1 2\n2 3\n3 4\n3",     'expected_output' => "formula: k*(k-1)^(n-1) = 18\nbrute_force: 18\nmatch: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2\n1 3\n2",          'expected_output' => "formula: k*(k-1)^(n-1) = 2\nbrute_force: 2\nmatch: True",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2\n2 3\n3 4\n4",     'expected_output' => "formula: k*(k-1)^(n-1) = 24\nbrute_force: 24\nmatch: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 2\n3",               'expected_output' => "formula: k*(k-1)^(n-1) = 6\nbrute_force: 6\nmatch: True",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Handshaking lemma ────────────────────────────────────────
        $seed(48, [
            ['input' => "4\n5\n1 2\n1 3\n2 3\n2 4\n3 4",    'expected_output' => "sum of degrees: 10\n2 * edges: 10\nhandshaking holds: True\nodd degree count: 0\nodd count even: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2\n1 2\n2 3",                   'expected_output' => "sum of degrees: 4\n2 * edges: 4\nhandshaking holds: True\nodd degree count: 2\nodd count even: True",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4\n1 2\n2 3\n3 4\n4 1",         'expected_output' => "sum of degrees: 8\n2 * edges: 8\nhandshaking holds: True\nodd degree count: 0\nodd count even: True",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1\n1 2",                        'expected_output' => "sum of degrees: 2\n2 * edges: 2\nhandshaking holds: True\nodd degree count: 2\nodd count even: True",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Erdős–Szekeres theorem ───────────────────────────────────
        $seed(49, [
            ['input' => "5\n3\n1\n4\n2\n5",                 'expected_output' => "increasing found: 1 4 5",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n5\n4\n3\n2\n1",                 'expected_output' => "decreasing found: 5 4 3",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "10\n10\n9\n8\n7\n6\n5\n4\n3\n2\n1",'expected_output' => "decreasing found: 10 9 8 7",'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1\n2\n3\n4\n5",                 'expected_output' => "increasing found: 1 2 3",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Grand proof portfolio ────────────────────────────────────
        $seed(50, [
            ['input' => "5",  'expected_output' => "direct: 5^6 mod 7 = 1 holds=True\ncontrapositive: squarefree\ncontradiction: H(5)=2.283333 > ln(5)/2=0.804719: True\ninduction: sum_cubes=225 formula=225 match=True\nexistence: twin prime (11, 13)", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2",  'expected_output' => "direct: 2^2 mod 3 = 1 holds=True\ncontrapositive: squarefree\ncontradiction: H(2)=1.500000 > ln(2)/2=0.346574: True\ninduction: sum_cubes=9 formula=9 match=True\nexistence: twin prime (3, 5)",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "4",  'expected_output' => "direct: 4^4 mod 5 = 1 holds=True\ncontrapositive: 4\ncontradiction: H(4)=2.083333 > ln(4)/2=0.693147: True\ninduction: sum_cubes=100 formula=100 match=True\nexistence: twin prime (5, 7)",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "12", 'expected_output' => "direct: 12^12 mod 13 = 1 holds=True\ncontrapositive: 4\ncontradiction: H(12)=3.103211 > ln(12)/2=1.242453: True\ninduction: sum_cubes=6084 formula=6084 match=True\nexistence: twin prime (17, 19)",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 5 Coding (Intermediate) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}