<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 5 — Introduction to Mathematical Proof (University Student / Tier 2) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the University Student tier
 *   2. coding_questions    — 50 questions at applied undergraduate proof level
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
 * Difficulty: University Student — multi-step logical verification,
 * number theory, modular arithmetic, divisibility, and induction over
 * non-trivial sequences. Problems require careful reasoning, not just
 * plugging in formulas.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module5CodingChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (! $category) {
            $this->command->error('University Student category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 5 — Introduction to Mathematical Proof (University Student) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Introduction to Mathematical Proof',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply proof techniques computationally at undergraduate level. Tasks involve modular arithmetic, divisibility chains, non-trivial induction, strong-induction recurrences, existence witnesses over integer domains, systematic counterexample search, and multi-step logical verification. Each problem mirrors the structure of a rigorous written proof.',
                'time_limit_seconds' => 1500,
                'base_xp'            => 1200,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Introduction to Proof — Logic & Quantifiers (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Read `n` integers. Evaluate both a **universal** statement (∀) and an **existential** statement (∃):
- Universal: "All numbers are positive"
- Existential: "There exists a number greater than 100"

Print `True` or `False` for each on separate lines.

Format:
```
universal: True/False
existential: True/False
```

Example:
```
Input:
4
50
200
30
10
Output:
universal: True
existential: True
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read a logical expression as two integers `p` and `q` (1 = True, 0 = False). Print the truth value of all five connectives:
- p AND q
- p OR q
- NOT p
- p → q  (implies)
- p ↔ q  (biconditional)

Format: one per line, label: True/False.

Example:
```
Input:
1
0
Output:
AND: False
OR: True
NOT p: False
IMPLIES: False
IFF: False
```
MD,
                'starter_code'        => "p = int(input())\nq = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read `n` integers and determine for each of the following **quantified statements** whether it is true over the given set. Print `True` or `False` for each.

1. ∀x: x² ≥ 0
2. ∃x: x² = x  (i.e. x is 0 or 1)
3. ∀x: x is divisible by 2 OR x is divisible by 3
4. ∃x: x > 1000

Format: one per line.

Example:
```
Input:
3
0
1
4
Output:
True
True
False
False
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read a proposition as a string in the form `P -> Q` where P and Q are each `T` or `F`. Evaluate:
1. The original implication P → Q
2. The converse Q → P
3. The inverse ¬P → ¬Q
4. The contrapositive ¬Q → ¬P

Print each on a separate line (True/False). Note: converse and inverse always have the same truth value; original and contrapositive always agree.

Example:
```
Input: T -> F
Output:
P->Q: False
Q->P: True
~P->~Q: True
~Q->~P: False
```
MD,
                'starter_code'        => "expr = input().split()\nP = expr[0] == 'T'\nQ = expr[2] == 'T'\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read `n` integers. Build and evaluate a **proof by example** vs **universal claim** distinction. Check whether the claim "n² + n + 1 is always odd" holds for all given values. If it holds for all, print `holds universally`. If it fails for some, print `counterexample: <first failing n>`.

Example:
```
Input:
4
1
2
3
4
Output: holds universally
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Check n*n + n + 1 is odd for all\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Direct Proof — Number Theory (Q6–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Direct proof that **if a | b and b | c then a | c** (divisibility is transitive). Read three integers `a`, `b`, `c`. Print `True` if whenever a divides b AND b divides c, then a divides c. Print `False` otherwise. Also print the **witness**: c / a (as integer if exact).

Format:
```
True/False
witness: <value>
```

Example:
```
Input:
2
6
18
Output:
True
witness: 9
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\nc = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Direct proof: **if a | b and a | c then a | (bx + cy)** for any integers x and y. Read `a`, `b`, `c`, `x`, `y`. Verify the claim. Print `True` if a divides (b*x + c*y), and print the value of (b*x + c*y).

Format:
```
True/False
value: <bx+cy>
```

Example:
```
Input:
3
6
9
2
1
Output:
True
value: 21
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\nc = int(input())\nx = int(input())\ny = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Prove directly: **for all integers n, n(n+1)(n+2) is divisible by 6** (product of 3 consecutive integers). Read `n` and verify. Print `True` and the quotient `n(n+1)(n+2) / 6`.

Format:
```
True
quotient: <value>
```

Example:
```
Input: 4
Output:
True
quotient: 20
```
MD,
                'starter_code'        => "n = int(input())\n# Check n*(n+1)*(n+2) % 6 == 0\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Direct proof using the **division algorithm**: for any integer n and divisor d > 0, there exist unique q and r such that n = dq + r, 0 ≤ r < d. Read `n` and `d`. Print q and r.

Format:
```
q: <value>
r: <value>
```

Example:
```
Input:
17
5
Output:
q: 3
r: 2
```
MD,
                'starter_code'        => "n = int(input())\nd = int(input())\n# Compute quotient and remainder\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Direct proof: **if n is odd then n² ≡ 1 (mod 8)**. Read `n` integers (one per line after `n`). For each odd n, verify n² mod 8 == 1. Print `True` for each odd n. For even n, print `skip`.

Example:
```
Input:
4
3
4
7
9
Output:
True
skip
True
True
```
MD,
                'starter_code'        => "k = int(input())\nnums = [int(input()) for _ in range(k)]\n# For odd n: verify n*n % 8 == 1\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Proof by Contrapositive (Q11–Q15)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Prove by contrapositive: **if n² is divisible by 3, then n is divisible by 3**. For a given `n`, verify the contrapositive: "if 3 ∤ n then 3 ∤ n²". Read `n`. Print the contrapositive truth value and both divisibility results.

Format:
```
3|n: True/False
3|n^2: True/False
contrapositive holds: True/False
```

Example:
```
Input: 6
Output:
3|n: True
3|n^2: True
contrapositive holds: True
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Prove by contrapositive: **if a·b is even, it doesn't mean both are even** — but prove the related statement: "if a·b is odd, then both a and b are odd." Use contrapositive: "if a is even or b is even, then a·b is even." Read `a` and `b`. Print both the original and contrapositive truth values.

Format:
```
original: True/False
contrapositive: True/False
```

Example:
```
Input:
3
5
Output:
original: True
contrapositive: True
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# original: (a*b odd) -> (a odd AND b odd)\n# contrapositive: (a even OR b even) -> (a*b even)\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Prove by contrapositive: **if n³ + n is even, then n is even**. Use the contrapositive: "if n is odd, then n³ + n is odd." Read `n`. Print `True` if the contrapositive holds, `False` otherwise. Also print n³ + n and its parity.

Format:
```
n^3+n: <value>
parity: even/odd
contrapositive holds: True/False
```

Example:
```
Input: 3
Output:
n^3+n: 30
parity: even
contrapositive holds: True
```
MD,
                'starter_code'        => "n = int(input())\n# Compute n**3 + n and check parity\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Prove by contrapositive: **if a and b are both irrational-candidates (not perfect squares), their sum is not zero** is FALSE — disprove it. Instead prove: "if a + b ≠ 0, we cannot conclude sign." Focus on the valid direction: "if x² − 5x + 6 = 0 then x = 2 or x = 3." Verify using contrapositive: "if x ≠ 2 and x ≠ 3, then x² − 5x + 6 ≠ 0." Read `x`. Print `contrapositive holds` or `contrapositive fails`.

Example:
```
Input: 5
Output: contrapositive holds
```
MD,
                'starter_code'        => "x = int(input())\n# contrapositive: if x != 2 and x != 3, then x**2 - 5*x + 6 != 0\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Read `n` integers. For each, verify all three equivalent forms of: "if 6 | n then both 2 | n and 3 | n":
1. Original: (6|n) → (2|n ∧ 3|n)
2. Contrapositive: ¬(2|n ∧ 3|n) → ¬(6|n)
3. Direct verification

Print for each n:
```
n=<v> original: True/False contra: True/False
```

Example:
```
Input:
3
6
4
9
Output:
n=6 original: True contra: True
n=4 original: True contra: True
n=9 original: True contra: True
```
MD,
                'starter_code'        => "k = int(input())\nnums = [int(input()) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Proof by Contradiction (Q16–Q20)
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Proof by contradiction: **√3 is irrational**. Assume √3 = p/q in lowest terms (gcd(p,q)=1). Then 3q² = p², so 3 | p², so 3 | p. Let p = 3k; then 3q² = 9k², so q² = 3k², so 3 | q — contradicting gcd = 1. Simulate: read `p` and `q`. Print `True` if gcd(p,q) == 1 AND p*p == 3*q*q (which is impossible — should always be False). Print `contradiction: no such fraction exists`.

Example:
```
Input:
3
2
Output: contradiction: no such fraction exists
```
MD,
                'starter_code'        => "import math\np = int(input())\nq = int(input())\n# Check if gcd(p,q)==1 and p*p == 3*q*q\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Proof by contradiction: **there is no rational number whose square is 2** (a different angle). Read an integer `N`. Search all fractions p/q with 1 ≤ p, q ≤ N and gcd(p,q) = 1. Print the fraction that comes **closest** to √2 without equaling it exactly. Print `closest: p/q = <decimal rounded to 8 dp>`.

Example:
```
Input: 10
Output: closest: 7/5 = 1.40000000
```
MD,
                'starter_code'        => "import math\nN = int(input())\n# Search fractions p/q with gcd=1, minimize |p/q - sqrt(2)|\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Proof by contradiction that **log₂(3) is irrational**: assume log₂(3) = p/q so 2^p = 3^q. Read `N`. Search all pairs (p, q) with 1 ≤ p, q ≤ N. Print all pairs where 2**p == 3**q, or print `no solution found` if none exist (proving irrationality).

Example:
```
Input: 20
Output: no solution found
```
MD,
                'starter_code'        => "N = int(input())\n# Search pairs where 2**p == 3**q\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Proof by contradiction: **if p is prime and p | a·b, then p | a or p | b** (Euclid's lemma). Read prime `p`, and integers `a`, `b`. Assume p | a·b but p ∤ a. Use contradiction: since p is prime and p ∤ a, gcd(p, a) = 1. By Bezout, there exist x, y with px + ay = 1. Multiply by b: pxb + aby = b. Since p | ab and p | pxb, p | b. Print the Bezout coefficients x, y (using extended GCD) and confirm p | b.

Format:
```
bezout: <x> <y>
p|b: True/False
```

Example:
```
Input:
5
3
10
Output:
bezout: 2 -1
p|b: True
```
MD,
                'starter_code'        => "p = int(input())\na = int(input())\nb = int(input())\n# Extended GCD for Bezout coefficients of (p, a)\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Proof by contradiction: **the sum of a rational and an irrational is irrational**. We can't represent irrational numbers in integers, so simulate: read a rational `p/q` (two integers) and an approximation of an irrational (`float`). Compute their sum. Then check: can this sum be expressed as a fraction with denominator ≤ 1000 and numerator ≤ 10000 that is **exact** to 6 decimal places? If not, print `irrational (no exact fraction found)`. If yes, print `rational: <p>/<q>`.

Example:
```
Input:
1
2
1.41421356
Output: irrational (no exact fraction found)
```
MD,
                'starter_code'        => "p = int(input())\nq = int(input())\nirrational = float(input())\nresult = p / q + irrational\n# Search fractions within tolerance 1e-6\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Proof by Cases (Q21–Q25)
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Prove by cases that **for all integers n, n² mod 4 ∈ {0, 1}**. Read `n` integers (one per line after `n`). For each, determine the case (n ≡ 0, 1, 2, 3 mod 4), compute n² mod 4, and verify it is 0 or 1.

Print for each: `n=<v> case=<r> n^2 mod 4=<result> valid=True/False`

Example:
```
Input:
3
5
6
7
Output:
n=5 case=1 n^2 mod 4=1 valid=True
n=6 case=2 n^2 mod 4=0 valid=True
n=7 case=3 n^2 mod 4=1 valid=True
```
MD,
                'starter_code'        => "k = int(input())\nnums = [int(input()) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Prove by cases: **for all n, n(n+1)/2 is always an integer** (the formula is exact). Read `n`. Distinguish the two cases (n even, n odd) and print which case applies and the result of n*(n+1)//2.

Format:
```
case: even/odd
result: <value>
```

Example:
```
Input: 7
Output:
case: odd
result: 28
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Prove by exhaustion of cases: **for any integer n, exactly one of the following holds: n ≡ 0, 1, 2, 3, 4 (mod 5)**. Read `n` integers. For each, determine which residue class it belongs to and compute n² mod 5. Print whether n² mod 5 is always in {0, 1, 4}.

Print for each: `n=<v> n mod 5=<r> n^2 mod 5=<s> in_set=True/False`

Example:
```
Input:
3
7
11
15
Output:
n=7 n mod 5=2 n^2 mod 5=4 in_set=True
n=11 n mod 5=1 n^2 mod 5=1 in_set=True
n=15 n mod 5=0 n^2 mod 5=0 in_set=True
```
MD,
                'starter_code'        => "k = int(input())\nnums = [int(input()) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Proof by cases: **the product of any two consecutive integers is even, and the product of any three consecutive integers is divisible by 6**. Read `n`. Verify both claims and print the case analysis.

Format:
```
n*(n+1) even: True/False
n*(n+1)*(n+2) div6: True/False
case for div6: <case description>
```

Case descriptions: `"3k"` if 3|n, `"3k+1"` if n≡1 mod 3, `"3k+2"` if n≡2 mod 3.

Example:
```
Input: 5
Output:
n*(n+1) even: True
n*(n+1)*(n+2) div6: True
case for div6: 3k+2
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Prove by cases: **for all integers n, 3 | n(n+1)(2n+1)**. (This is the denominator in the sum-of-squares formula.) Read `n`. Identify which of the three mod-3 cases (n ≡ 0, 1, 2) applies, verify divisibility, and print the case and result.

Format:
```
n mod 3: <r>
product: <n*(n+1)*(2*n+1)>
divisible by 3: True/False
```

Example:
```
Input: 4
Output:
n mod 3: 1
product: 60
divisible by 3: True
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Mathematical Induction (Q26–Q30)
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Mathematical induction: verify **∑ᵢ₌₁ⁿ i³ = [n(n+1)/2]²** for all n from 1 to `N`. Read `N`. For each n, print the left-hand side, right-hand side, and `True` if they match.

Format per line: `n=<v> LHS=<l> RHS=<r> match=True/False`

Example:
```
Input: 3
Output:
n=1 LHS=1 RHS=1 match=True
n=2 LHS=9 RHS=9 match=True
n=3 LHS=36 RHS=36 match=True
```
MD,
                'starter_code'        => "N = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Induction on **geometric series**: verify ∑ᵢ₌₀ⁿ rⁱ = (rⁿ⁺¹ − 1) / (r − 1) for r ≠ 1. Read `r` (integer ≥ 2) and `N`. For each n from 0 to N, print the partial sum and the formula value. They must match exactly as integers.

Format per line: `n=<v> sum=<s> formula=<f> match=True/False`

Example:
```
Input:
2
3
Output:
n=0 sum=1 formula=1 match=True
n=1 sum=3 formula=3 match=True
n=2 sum=7 formula=7 match=True
n=3 sum=15 formula=15 match=True
```
MD,
                'starter_code'        => "r = int(input())\nN = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Induction: prove **4ⁿ − 1 is divisible by 3** for all n ≥ 1. Simulate the inductive step: read `k`. Assume 3 | (4^k − 1). Show 4^(k+1) − 1 = 4·(4^k − 1) + 3, which is divisible by 3. Print the inductive step breakdown and verify.

Format:
```
4^k - 1: <value>
4*(4^k-1): <value>
4^(k+1)-1: <value>
3|4^(k+1)-1: True/False
```

Example:
```
Input: 2
Output:
4^k - 1: 15
4*(4^k-1): 60
4^(k+1)-1: 63
3|4^(k+1)-1: True
```
MD,
                'starter_code'        => "k = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Induction: verify **Bernoulli's inequality**: (1 + x)ⁿ ≥ 1 + nx for all n ≥ 0 and x ≥ −1. Read `x` (float) and `N` (int). For each n from 0 to N, print `True` if the inequality holds.

Format per line: `n=<v> LHS=<l> RHS=<r> holds=True/False` (round both to 4 dp).

Example:
```
Input:
0.5
3
Output:
n=0 LHS=1.0000 RHS=1.0000 holds=True
n=1 LHS=1.5000 RHS=1.5000 holds=True
n=2 LHS=2.2500 RHS=2.0000 holds=True
n=3 LHS=3.3750 RHS=2.5000 holds=True
```
MD,
                'starter_code'        => "x = float(input())\nN = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Prove by induction: **n! > 2ⁿ for all n ≥ 4** (note: base case is n = 4). Read `N` (≥ 4). For each n from 4 to N, verify n! > 2^n. Print each result.

Format per line: `n=<v> n!=<f> 2^n=<p> holds=True/False`

Example:
```
Input: 6
Output:
n=4 n!=24 2^n=16 holds=True
n=5 n!=120 2^n=32 holds=True
n=6 n!=720 2^n=64 holds=True
```
MD,
                'starter_code'        => "N = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Strong Induction (Q31–Q35)
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Strong induction: every integer n ≥ 2 can be written as a product of primes. Read `n` and output its **prime factorization** along with a count of prime factors (with multiplicity). Show the strong induction base case and recursive call depth by printing the factorization tree as a flat list with a factor count.

Format:
```
factors: <p1> <p2> ...
count: <k>
```

Example:
```
Input: 360
Output:
factors: 2 2 2 3 3 5
count: 6
```
MD,
                'starter_code'        => "n = int(input())\n# Prime factorize n\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Strong induction: compute the **Tribonacci sequence** T(1)=1, T(2)=1, T(3)=2, T(n)=T(n−1)+T(n−2)+T(n−3) for n ≥ 4. Read `N`. Print T(1) through T(N), one per line.

Example:
```
Input: 6
Output:
1
1
2
4
7
13
```
MD,
                'starter_code'        => "N = int(input())\n# T(1)=1, T(2)=1, T(3)=2, T(n)=T(n-1)+T(n-2)+T(n-3)\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Strong induction: prove that every amount of postage ≥ 12 cents can be formed using **4-cent and 5-cent stamps**. Read `N` (≥ 12). For each amount from 12 to N, print the combination (number of 4-cent and 5-cent stamps), or `impossible` if not achievable.

Format per line: `<amount>: <a>x4 + <b>x5`

Example:
```
Input: 15
Output:
12: 3x4 + 0x5
13: 2x4 + 1x5
14: 1x4 + 2x5
15: 0x4 + 3x5
```
MD,
                'starter_code'        => "N = int(input())\n# For each amount in range(12, N+1), find a,b>=0 s.t. 4a+5b==amount\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Strong induction on the **Fibonacci sequence**: prove F(n) < 2ⁿ for all n ≥ 1. Read `N`. For each n from 1 to N, print F(n), 2^n, and whether the inequality holds.

Format per line: `n=<v> F(n)=<f> 2^n=<p> holds=True/False`

Example:
```
Input: 5
Output:
n=1 F(n)=1 2^n=2 holds=True
n=2 F(n)=1 2^n=4 holds=True
n=3 F(n)=2 2^n=8 holds=True
n=4 F(n)=3 2^n=16 holds=True
n=5 F(n)=5 2^n=32 holds=True
```
MD,
                'starter_code'        => "N = int(input())\n# Compute Fibonacci and compare with 2**n\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Strong induction: prove the **Cassini identity** F(n−1)·F(n+1) − F(n)² = (−1)ⁿ. Read `N`. For each n from 2 to N, print whether the identity holds (True/False).

Format per line: `n=<v> LHS=<l> RHS=<r> holds=True/False`

Example:
```
Input: 5
Output:
n=2 LHS=-1 RHS=-1 holds=True
n=3 LHS=1 RHS=1 holds=True
n=4 LHS=-1 RHS=-1 holds=True
n=5 LHS=1 RHS=1 holds=True
```
MD,
                'starter_code'        => "N = int(input())\n# Fibonacci: F(1)=1,F(2)=1,...\n# For n in range(2, N+1): check F(n-1)*F(n+1) - F(n)**2 == (-1)**n\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Existence & Uniqueness (Q36–Q40)
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
**Existence proof** via Bezout's identity: for any integers `a` and `b` with gcd(a, b) = d, there exist integers x and y such that ax + by = d. Read `a` and `b`. Find x and y using the extended Euclidean algorithm. Print gcd, x, and y. Verify ax + by = gcd.

Format:
```
gcd: <d>
x: <x>
y: <y>
verify: <a>*<x> + <b>*<y> = <d>: True/False
```

Example:
```
Input:
35
15
Output:
gcd: 5
x: 1
y: -2
verify: 35*1 + 15*-2 = 5: True
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Extended Euclidean algorithm\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
**Uniqueness**: prove that every integer n ≥ 2 has a **unique smallest prime factor**. Read `n`. Print the smallest prime factor and verify it is prime.

Format:
```
smallest prime factor: <p>
is prime: True/False
```

Example:
```
Input: 84
Output:
smallest prime factor: 2
is prime: True
```
MD,
                'starter_code'        => "n = int(input())\n# Find smallest prime factor of n\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
**Existence and uniqueness** of the **modular inverse**: a has a unique inverse mod m if and only if gcd(a, m) = 1. Read `a` and `m`. If gcd(a,m) = 1, find x such that a·x ≡ 1 (mod m) with 0 < x < m. Print `inverse: <x>`. If gcd ≠ 1, print `no inverse exists`.

Example:
```
Input:
3
7
Output: inverse: 5
```
MD,
                'starter_code'        => "a = int(input())\nm = int(input())\n# Find modular inverse of a mod m\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
**Existence**: Chinese Remainder Theorem. Given two congruences x ≡ a (mod m) and x ≡ b (mod n) with gcd(m,n) = 1, a unique solution exists mod (m·n). Read `a`, `m`, `b`, `n`. Find the smallest positive x satisfying both. Print `x: <value>`.

Example:
```
Input:
2
3
3
5
Output: x: 8
```
MD,
                'starter_code'        => "a = int(input())\nm = int(input())\nb = int(input())\nn = int(input())\n# Find x in range(1, m*n+1) s.t. x%m==a and x%n==b\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
**Uniqueness** in linear algebra: the system ax = b has a unique solution iff a ≠ 0. Generalise: for a 2×2 system read `a b c d e f` (the system ax+by=e, cx+dy=f). Compute the determinant D = ad − bc. If D ≠ 0, print `unique solution` and x, y rounded to 4 decimal places. If D = 0 and the system is consistent, print `infinite solutions`. If inconsistent, print `no solution`.

Format:
```
unique solution
x: <value>
y: <value>
```

Example:
```
Input:
2
1
1
3
5
10
Output:
unique solution
x: 1.0000
y: 3.0000
```
MD,
                'starter_code'        => "a, b = map(float, input().split())\nc, d = map(float, input().split())\ne, f = map(float, input().split())\n# Solve 2x2 system using Cramer's rule\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Disproving — Counterexamples (Q41–Q45)
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Search for a **counterexample** to each of the following claims over integers 1 to `N`. For each claim, print the first counterexample found, or `none` if none exists in range.

1. "All primes are odd"
2. "n² − n + 11 is always prime"
3. "2ⁿ + 1 is always prime"

Format:
```
claim1: <n or 'none'>
claim2: <n or 'none'>
claim3: <n or 'none'>
```

Example:
```
Input: 15
Output:
claim1: 2
claim2: none
claim3: 5
```
MD,
                'starter_code'        => "N = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Disprove: **"if n is composite, then n has a factor ≤ √n"** — this is actually TRUE, so your task is to CONFIRM it holds for all composite n from 2 to `N`, and print the smallest factor ≤ √n for each composite. Skip primes. Print `all confirmed` at the end.

Format per line: `<n>: factor=<f>`

Example:
```
Input: 10
Output:
4: factor=2
6: factor=2
8: factor=2
9: factor=3
10: factor=2
all confirmed
```
MD,
                'starter_code'        => "import math\nN = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Find a **counterexample** to: "For all real x, if x > 0 then x² > x." Read `N` pairs of integers (a, b) representing rationals a/b. For each fraction, check if it's a counterexample (0 < a/b < 1 gives (a/b)² < a/b). Print `counterexample: <a>/<b>` for the first one found, else `none`.

Example:
```
Input:
3
1 2
3 1
4 3
Output: counterexample: 1/2
```
MD,
                'starter_code'        => "n = int(input())\nfracs = [tuple(map(int, input().split())) for _ in range(n)]\n# Check if 0 < a/b and (a/b)**2 < a/b\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Disprove by counterexample: **"The sum of two irrational numbers is irrational."** Use the classic: √2 + (−√2) = 0 (rational). Simulate using a list of `n` float pairs. For each pair, check if their sum is within 1e-9 of an integer. Print `counterexample: <a> + <b> = <sum>` for the first such pair, else `none found`.

Example:
```
Input:
2
1.41421356 -1.41421356
1.73205081 0.26794919
Output: counterexample: 1.41421356 + -1.41421356 = 0.0
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(float, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Read `N`. Find the **first counterexample** to Goldbach's conjecture (every even integer > 2 is the sum of two primes) in the range 4 to N, or print `no counterexample found` (the conjecture has been verified up to 4×10¹⁸, so expect no counterexample for small N). Also print how many even numbers you checked.

Format:
```
checked: <k>
counterexample: <n or 'no counterexample found'>
```

Example:
```
Input: 20
Output:
checked: 9
counterexample: no counterexample found
```
MD,
                'starter_code'        => "N = int(input())\n# Check all even numbers from 4 to N\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Choosing Strategy & Mixed (Q46–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Read a statement code (1–5) and an integer `n`. Apply the specified technique and print the result:

1. Direct: verify n(n+1)(n+2) % 6 == 0
2. Contrapositive: if n² % 4 ≠ 0, verify n is odd
3. Contradiction: if n > 0, show n + 1 > 1 (always True, contradiction assumed its negation)
4. Induction step: verify (n+1)³ − (n+1) is divisible by 6 given n³ − n is
5. Counterexample search: find first k in 1..n where k² < k

Print `True`/`False` for 1–4, or the counterexample value for 5 (`none` if not found).

Example:
```
Input:
4
5
Output: True
```
MD,
                'starter_code'        => "stmt = int(input())\nn = int(input())\n# Route to the appropriate technique\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Prove the **AM-GM inequality**: for non-negative a, b, (a + b)/2 ≥ √(ab). Read `n` pairs of non-negative integers. For each, print AM, GM, and whether AM ≥ GM. Also print the **equality case** (when a == b).

Format per pair: `AM=<v> GM=<v> holds=True/False equality=True/False`

Example:
```
Input:
3
4 4
9 1
4 9
Output:
AM=4.0000 GM=4.0000 holds=True equality=True
AM=5.0000 GM=3.0000 holds=True equality=False
AM=6.5000 GM=6.0000 holds=True equality=False
```
MD,
                'starter_code'        => "import math\nn = int(input())\npairs = [tuple(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Prove by strong induction: **every n ≥ 8 can be expressed as 3a + 5b** for non-negative integers a, b. Read `N` (≥ 8). For each n from 8 to N, find the lexicographically smallest (a, b) with 3a + 5b = n (smallest a first). Print `<n>: a=<a> b=<b>`.

Example:
```
Input: 14
Output:
8: a=1 b=1
9: a=3 b=0
10: a=0 b=2
11: a=2 b=1
12: a=4 b=0
13: a=1 b=2
14: a=3 b=1
```
MD,
                'starter_code'        => "N = int(input())\n# For n in range(8, N+1), find smallest a>=0 s.t. (n - 3*a) % 5 == 0 and (n-3*a)//5 >= 0\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 300,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Mixed proof strategies: read `n` integers and for each apply the **best strategy** to prove or disprove the claim "n is a perfect square":

- If n is a perfect square: print `<n>: direct proof (sqrt=<k>)`
- If n is not a perfect square: print `<n>: counterexample (not a perfect square)`

Also print at the end: total perfect squares found.

Example:
```
Input:
4
9
7
16
25
Output:
9: direct proof (sqrt=3)
7: counterexample (not a perfect square)
16: direct proof (sqrt=4)
25: direct proof (sqrt=5)
total: 3
```
MD,
                'starter_code'        => "import math\nn = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**Proof portfolio**: read integer `n` and verify five distinct results using five different techniques. Print `True` or `False` for each (all should be True for any valid n):

1. Direct: 6 | n(n+1)(n+2)
2. Contrapositive: (n odd) → (n² odd) — check both sides
3. Contradiction: assume n and n+1 are both even → show contradiction (they differ by 1, so cannot both be even)
4. Induction step: n³ − n + (n+1)³ − (n+1) is the sum of two multiples of 6 (each term is divisible by 6)
5. Existence: there exists a prime p with n < p ≤ 2n+2

Print each result on a separate line, labelled.

Format:
```
direct: True/False
contrapositive: True/False
contradiction: True/False
induction: True/False
existence: True/False
```

Example:
```
Input: 5
Output:
direct: True
contrapositive: True
contradiction: True
induction: True
existence: True
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 350,
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

        // ── Q1: Universal & existential ───────────────────────────────────
        $seed(1, [
            ['input' => "4\n50\n200\n30\n10",         'expected_output' => "universal: True\nexistential: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",                 'expected_output' => "universal: True\nexistential: False",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n-1\n5\n200",              'expected_output' => "universal: False\nexistential: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n-5\n-10",                 'expected_output' => "universal: False\nexistential: False",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Five connectives ──────────────────────────────────────────
        $seed(2, [
            ['input' => "1\n0",    'expected_output' => "AND: False\nOR: True\nNOT p: False\nIMPLIES: False\nIFF: False",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1",    'expected_output' => "AND: True\nOR: True\nNOT p: False\nIMPLIES: True\nIFF: True",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0",    'expected_output' => "AND: False\nOR: False\nNOT p: True\nIMPLIES: True\nIFF: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n1",    'expected_output' => "AND: False\nOR: True\nNOT p: True\nIMPLIES: True\nIFF: False",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Four quantified statements ────────────────────────────────
        $seed(3, [
            ['input' => "3\n0\n1\n4",         'expected_output' => "True\nTrue\nFalse\nFalse",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2\n4",            'expected_output' => "True\nFalse\nTrue\nFalse",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0\n1\n2001",      'expected_output' => "True\nTrue\nFalse\nTrue",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n6\n15\n30",       'expected_output' => "True\nFalse\nTrue\nFalse",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Four forms of implication ─────────────────────────────────
        $seed(4, [
            ['input' => 'T -> F',    'expected_output' => "P->Q: False\nQ->P: True\n~P->~Q: True\n~Q->~P: False",    'is_hidden' => false, 'order_index' => 1],
            ['input' => 'T -> T',    'expected_output' => "P->Q: True\nQ->P: True\n~P->~Q: True\n~Q->~P: True",      'is_hidden' => false, 'order_index' => 2],
            ['input' => 'F -> T',    'expected_output' => "P->Q: True\nQ->P: False\n~P->~Q: False\n~Q->~P: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => 'F -> F',    'expected_output' => "P->Q: True\nQ->P: True\n~P->~Q: True\n~Q->~P: True",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: n²+n+1 always odd ────────────────────────────────────────
        $seed(5, [
            ['input' => "4\n1\n2\n3\n4",     'expected_output' => 'holds universally',           'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n6\n7",        'expected_output' => 'holds universally',           'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0",              'expected_output' => 'holds universally',           'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n10\n11\n12",     'expected_output' => 'holds universally',          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Divisibility transitivity ─────────────────────────────────
        $seed(6, [
            ['input' => "2\n6\n18",     'expected_output' => "True\nwitness: 9",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n9\n27",     'expected_output' => "True\nwitness: 9",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n8\n32",     'expected_output' => "True\nwitness: 8",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n15\n60",    'expected_output' => "True\nwitness: 12",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: a|b and a|c → a|(bx+cy) ──────────────────────────────────
        $seed(7, [
            ['input' => "3\n6\n9\n2\n1",     'expected_output' => "True\nvalue: 21",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n4\n6\n3\n2",     'expected_output' => "True\nvalue: 24",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n10\n15\n1\n1",   'expected_output' => "True\nvalue: 25",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "7\n14\n21\n2\n-1",  'expected_output' => "True\nvalue: 7",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: n(n+1)(n+2) divisible by 6 ───────────────────────────────
        $seed(8, [
            ['input' => '4',    'expected_output' => "True\nquotient: 20",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',    'expected_output' => "True\nquotient: 1",     'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',    'expected_output' => "True\nquotient: 35",    'is_hidden' => true,  'order_index' => 3],
            ['input' => '10',   'expected_output' => "True\nquotient: 220",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Division algorithm ─────────────────────────────────────────
        $seed(9, [
            ['input' => "17\n5",     'expected_output' => "q: 3\nr: 2",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n7",      'expected_output' => "q: 0\nr: 0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n13",   'expected_output' => "q: 7\nr: 9",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "29\n4",     'expected_output' => "q: 7\nr: 1",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: odd n → n² ≡ 1 (mod 8) ─────────────────────────────────
        $seed(10, [
            ['input' => "4\n3\n4\n7\n9",        'expected_output' => "True\nskip\nTrue\nTrue",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n5",           'expected_output' => "True\nskip\nTrue",                'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n11\n12\n13\n14",    'expected_output' => "True\nskip\nTrue\nskip",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n15\n16",            'expected_output' => "True\nskip",                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: 3|n² → 3|n ─────────────────────────────────────────────
        $seed(11, [
            ['input' => '6',    'expected_output' => "3|n: True\n3|n^2: True\ncontrapositive holds: True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '7',    'expected_output' => "3|n: False\n3|n^2: False\ncontrapositive holds: True",   'is_hidden' => false, 'order_index' => 2],
            ['input' => '9',    'expected_output' => "3|n: True\n3|n^2: True\ncontrapositive holds: True",     'is_hidden' => true,  'order_index' => 3],
            ['input' => '5',    'expected_output' => "3|n: False\n3|n^2: False\ncontrapositive holds: True",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: a·b odd → both odd ───────────────────────────────────────
        $seed(12, [
            ['input' => "3\n5",    'expected_output' => "original: True\ncontrapositive: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5",    'expected_output' => "original: True\ncontrapositive: True",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n6",    'expected_output' => "original: True\ncontrapositive: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "7\n9",    'expected_output' => "original: True\ncontrapositive: True",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: n³+n parity ──────────────────────────────────────────────
        $seed(13, [
            ['input' => '3',     'expected_output' => "n^3+n: 30\nparity: even\ncontrapositive holds: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',     'expected_output' => "n^3+n: 68\nparity: even\ncontrapositive holds: True",    'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',     'expected_output' => "n^3+n: 130\nparity: even\ncontrapositive holds: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '7',     'expected_output' => "n^3+n: 350\nparity: even\ncontrapositive holds: True",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: x²-5x+6 roots ───────────────────────────────────────────
        $seed(14, [
            ['input' => '5',    'expected_output' => 'contrapositive holds',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',    'expected_output' => 'contrapositive holds',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',    'expected_output' => 'contrapositive holds',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '10',   'expected_output' => 'contrapositive holds',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: 6|n → 2|n and 3|n ───────────────────────────────────────
        $seed(15, [
            ['input' => "3\n6\n4\n9",       'expected_output' => "n=6 original: True contra: True\nn=4 original: True contra: True\nn=9 original: True contra: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n12\n7",         'expected_output' => "n=12 original: True contra: True\nn=7 original: True contra: True",                                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n18\n10",        'expected_output' => "n=18 original: True contra: True\nn=10 original: True contra: True",                                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n30",            'expected_output' => "n=30 original: True contra: True",                                                                       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: √3 irrational simulation ────────────────────────────────
        $seed(16, [
            ['input' => "3\n2",     'expected_output' => 'contradiction: no such fraction exists',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "7\n4",     'expected_output' => 'contradiction: no such fraction exists',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n3",     'expected_output' => 'contradiction: no such fraction exists',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1",     'expected_output' => 'contradiction: no such fraction exists',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Closest fraction to √2 ──────────────────────────────────
        $seed(17, [
            ['input' => '10',    'expected_output' => 'closest: 7/5 = 1.40000000',    'is_hidden' => false, 'order_index' => 1],
            ['input' => '5',     'expected_output' => 'closest: 3/2 = 1.50000000',    'is_hidden' => false, 'order_index' => 2],
            ['input' => '20',    'expected_output' => 'closest: 17/12 = 1.41666667',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '3',     'expected_output' => 'closest: 3/2 = 1.50000000',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: log₂3 irrational ─────────────────────────────────────────
        $seed(18, [
            ['input' => '20',    'expected_output' => 'no solution found',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '50',    'expected_output' => 'no solution found',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',    'expected_output' => 'no solution found',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '100',   'expected_output' => 'no solution found',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Euclid's lemma + Bezout ─────────────────────────────────
        $seed(19, [
            ['input' => "5\n3\n10",    'expected_output' => "bezout: 2 -1\np|b: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "7\n3\n14",    'expected_output' => "bezout: -2 5\np|b: True",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n7\n9",     'expected_output' => "bezout: -2 1\np|b: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "11\n4\n22",   'expected_output' => "bezout: 3 -1\np|b: True",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Rational + irrational = irrational ───────────────────────
        $seed(20, [
            ['input' => "1\n2\n1.41421356",    'expected_output' => 'irrational (no exact fraction found)',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n3\n1.73205081",    'expected_output' => 'irrational (no exact fraction found)',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n1\n3.14159265",    'expected_output' => 'irrational (no exact fraction found)',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n3\n1.41421356",    'expected_output' => 'irrational (no exact fraction found)',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: n² mod 4 cases ───────────────────────────────────────────
        $seed(21, [
            ['input' => "3\n5\n6\n7",         'expected_output' => "n=5 case=1 n^2 mod 4=1 valid=True\nn=6 case=2 n^2 mod 4=0 valid=True\nn=7 case=3 n^2 mod 4=1 valid=True",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n4\n9",            'expected_output' => "n=4 case=0 n^2 mod 4=0 valid=True\nn=9 case=1 n^2 mod 4=1 valid=True",                                            'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n10\n11\n12",      'expected_output' => "n=10 case=2 n^2 mod 4=0 valid=True\nn=11 case=3 n^2 mod 4=1 valid=True\nn=12 case=0 n^2 mod 4=0 valid=True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1",               'expected_output' => "n=1 case=1 n^2 mod 4=1 valid=True",                                                                               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: n(n+1)/2 integer ─────────────────────────────────────────
        $seed(22, [
            ['input' => '7',     'expected_output' => "case: odd\nresult: 28",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',     'expected_output' => "case: even\nresult: 10",   'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',    'expected_output' => "case: even\nresult: 55",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',     'expected_output' => "case: odd\nresult: 1",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: n² mod 5 ∈ {0,1,4} ──────────────────────────────────────
        $seed(23, [
            ['input' => "3\n7\n11\n15",        'expected_output' => "n=7 n mod 5=2 n^2 mod 5=4 in_set=True\nn=11 n mod 5=1 n^2 mod 5=1 in_set=True\nn=15 n mod 5=0 n^2 mod 5=0 in_set=True",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3\n4",             'expected_output' => "n=3 n mod 5=3 n^2 mod 5=4 in_set=True\nn=4 n mod 5=4 n^2 mod 5=1 in_set=True",                                                'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n6\n8",             'expected_output' => "n=6 n mod 5=1 n^2 mod 5=1 in_set=True\nn=8 n mod 5=3 n^2 mod 5=4 in_set=True",                                                'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5",                'expected_output' => "n=5 n mod 5=0 n^2 mod 5=0 in_set=True",                                                                                         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Products of consecutive integers ─────────────────────────
        $seed(24, [
            ['input' => '5',     'expected_output' => "n*(n+1) even: True\nn*(n+1)*(n+2) div6: True\ncase for div6: 3k+2",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',     'expected_output' => "n*(n+1) even: True\nn*(n+1)*(n+2) div6: True\ncase for div6: 3k+1",    'is_hidden' => false, 'order_index' => 2],
            ['input' => '6',     'expected_output' => "n*(n+1) even: True\nn*(n+1)*(n+2) div6: True\ncase for div6: 3k",      'is_hidden' => true,  'order_index' => 3],
            ['input' => '9',     'expected_output' => "n*(n+1) even: True\nn*(n+1)*(n+2) div6: True\ncase for div6: 3k",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: 3 | n(n+1)(2n+1) ────────────────────────────────────────
        $seed(25, [
            ['input' => '4',     'expected_output' => "n mod 3: 1\nproduct: 60\ndivisible by 3: True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',     'expected_output' => "n mod 3: 0\nproduct: 42\ndivisible by 3: True",     'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',     'expected_output' => "n mod 3: 2\nproduct: 110\ndivisible by 3: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => '7',     'expected_output' => "n mod 3: 1\nproduct: 280\ndivisible by 3: True",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Sum of cubes formula ─────────────────────────────────────
        $seed(26, [
            ['input' => '3',    'expected_output' => "n=1 LHS=1 RHS=1 match=True\nn=2 LHS=9 RHS=9 match=True\nn=3 LHS=36 RHS=36 match=True",                                              'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',    'expected_output' => "n=1 LHS=1 RHS=1 match=True",                                                                                                         'is_hidden' => false, 'order_index' => 2],
            ['input' => '4',    'expected_output' => "n=1 LHS=1 RHS=1 match=True\nn=2 LHS=9 RHS=9 match=True\nn=3 LHS=36 RHS=36 match=True\nn=4 LHS=100 RHS=100 match=True",            'is_hidden' => true,  'order_index' => 3],
            ['input' => '2',    'expected_output' => "n=1 LHS=1 RHS=1 match=True\nn=2 LHS=9 RHS=9 match=True",                                                                            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Geometric series ──────────────────────────────────────────
        $seed(27, [
            ['input' => "2\n3",    'expected_output' => "n=0 sum=1 formula=1 match=True\nn=1 sum=3 formula=3 match=True\nn=2 sum=7 formula=7 match=True\nn=3 sum=15 formula=15 match=True",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2",    'expected_output' => "n=0 sum=1 formula=1 match=True\nn=1 sum=4 formula=4 match=True\nn=2 sum=13 formula=13 match=True",                                       'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1",    'expected_output' => "n=0 sum=1 formula=1 match=True\nn=1 sum=3 formula=3 match=True",                                                                          'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2",    'expected_output' => "n=0 sum=1 formula=1 match=True\nn=1 sum=5 formula=5 match=True\nn=2 sum=21 formula=21 match=True",                                       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: 4^n − 1 divisible by 3 ──────────────────────────────────
        $seed(28, [
            ['input' => '2',    'expected_output' => "4^k - 1: 15\n4*(4^k-1): 60\n4^(k+1)-1: 63\n3|4^(k+1)-1: True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',    'expected_output' => "4^k - 1: 3\n4*(4^k-1): 12\n4^(k+1)-1: 15\n3|4^(k+1)-1: True",      'is_hidden' => false, 'order_index' => 2],
            ['input' => '3',    'expected_output' => "4^k - 1: 63\n4*(4^k-1): 252\n4^(k+1)-1: 255\n3|4^(k+1)-1: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '4',    'expected_output' => "4^k - 1: 255\n4*(4^k-1): 1020\n4^(k+1)-1: 1023\n3|4^(k+1)-1: True",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Bernoulli's inequality ───────────────────────────────────
        $seed(29, [
            ['input' => "0.5\n3",    'expected_output' => "n=0 LHS=1.0000 RHS=1.0000 holds=True\nn=1 LHS=1.5000 RHS=1.5000 holds=True\nn=2 LHS=2.2500 RHS=2.0000 holds=True\nn=3 LHS=3.3750 RHS=2.5000 holds=True",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n2",    'expected_output' => "n=0 LHS=1.0000 RHS=1.0000 holds=True\nn=1 LHS=1.0000 RHS=1.0000 holds=True\nn=2 LHS=1.0000 RHS=1.0000 holds=True",                                          'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0\n3",    'expected_output' => "n=0 LHS=1.0000 RHS=1.0000 holds=True\nn=1 LHS=2.0000 RHS=2.0000 holds=True\nn=2 LHS=4.0000 RHS=3.0000 holds=True\nn=3 LHS=8.0000 RHS=4.0000 holds=True",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "-0.5\n2",   'expected_output' => "n=0 LHS=1.0000 RHS=1.0000 holds=True\nn=1 LHS=0.5000 RHS=0.5000 holds=True\nn=2 LHS=0.2500 RHS=0.0000 holds=True",                                          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: n! > 2^n for n≥4 ────────────────────────────────────────
        $seed(30, [
            ['input' => '6',     'expected_output' => "n=4 n!=24 2^n=16 holds=True\nn=5 n!=120 2^n=32 holds=True\nn=6 n!=720 2^n=64 holds=True",                                                                        'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',     'expected_output' => "n=4 n!=24 2^n=16 holds=True",                                                                                                                                     'is_hidden' => false, 'order_index' => 2],
            ['input' => '8',     'expected_output' => "n=4 n!=24 2^n=16 holds=True\nn=5 n!=120 2^n=32 holds=True\nn=6 n!=720 2^n=64 holds=True\nn=7 n!=5040 2^n=128 holds=True\nn=8 n!=40320 2^n=256 holds=True",     'is_hidden' => true,  'order_index' => 3],
            ['input' => '5',     'expected_output' => "n=4 n!=24 2^n=16 holds=True\nn=5 n!=120 2^n=32 holds=True",                                                                                                       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Prime factorization ──────────────────────────────────────
        $seed(31, [
            ['input' => '360',    'expected_output' => "factors: 2 2 2 3 3 5\ncount: 6",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '12',     'expected_output' => "factors: 2 2 3\ncount: 3",          'is_hidden' => false, 'order_index' => 2],
            ['input' => '7',      'expected_output' => "factors: 7\ncount: 1",              'is_hidden' => true,  'order_index' => 3],
            ['input' => '100',    'expected_output' => "factors: 2 2 5 5\ncount: 4",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Tribonacci ───────────────────────────────────────────────
        $seed(32, [
            ['input' => '6',     'expected_output' => "1\n1\n2\n4\n7\n13",                 'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',     'expected_output' => "1\n1\n2",                           'is_hidden' => false, 'order_index' => 2],
            ['input' => '8',     'expected_output' => "1\n1\n2\n4\n7\n13\n24\n44",        'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',     'expected_output' => "1",                                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Stamp problem ────────────────────────────────────────────
        $seed(33, [
            ['input' => '15',    'expected_output' => "12: 3x4 + 0x5\n13: 2x4 + 1x5\n14: 1x4 + 2x5\n15: 0x4 + 3x5",                      'is_hidden' => false, 'order_index' => 1],
            ['input' => '12',    'expected_output' => "12: 3x4 + 0x5",                                                                      'is_hidden' => false, 'order_index' => 2],
            ['input' => '17',    'expected_output' => "12: 3x4 + 0x5\n13: 2x4 + 1x5\n14: 1x4 + 2x5\n15: 0x4 + 3x5\n16: 4x4 + 0x5\n17: 3x4 + 1x5",  'is_hidden' => true, 'order_index' => 3],
            ['input' => '20',    'expected_output' => "12: 3x4 + 0x5\n13: 2x4 + 1x5\n14: 1x4 + 2x5\n15: 0x4 + 3x5\n16: 4x4 + 0x5\n17: 3x4 + 1x5\n18: 2x4 + 2x5\n19: 1x4 + 3x5\n20: 0x4 + 4x5",  'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q34: Fibonacci < 2^n ──────────────────────────────────────────
        $seed(34, [
            ['input' => '5',     'expected_output' => "n=1 F(n)=1 2^n=2 holds=True\nn=2 F(n)=1 2^n=4 holds=True\nn=3 F(n)=2 2^n=8 holds=True\nn=4 F(n)=3 2^n=16 holds=True\nn=5 F(n)=5 2^n=32 holds=True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',     'expected_output' => "n=1 F(n)=1 2^n=2 holds=True\nn=2 F(n)=1 2^n=4 holds=True",                                                                                                 'is_hidden' => false, 'order_index' => 2],
            ['input' => '7',     'expected_output' => "n=1 F(n)=1 2^n=2 holds=True\nn=2 F(n)=1 2^n=4 holds=True\nn=3 F(n)=2 2^n=8 holds=True\nn=4 F(n)=3 2^n=16 holds=True\nn=5 F(n)=5 2^n=32 holds=True\nn=6 F(n)=8 2^n=64 holds=True\nn=7 F(n)=13 2^n=128 holds=True",  'is_hidden' => true, 'order_index' => 3],
            ['input' => '1',     'expected_output' => "n=1 F(n)=1 2^n=2 holds=True",                                                                                                                               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Cassini identity ─────────────────────────────────────────
        $seed(35, [
            ['input' => '5',    'expected_output' => "n=2 LHS=-1 RHS=-1 holds=True\nn=3 LHS=1 RHS=1 holds=True\nn=4 LHS=-1 RHS=-1 holds=True\nn=5 LHS=1 RHS=1 holds=True",      'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',    'expected_output' => "n=2 LHS=-1 RHS=-1 holds=True",                                                                                              'is_hidden' => false, 'order_index' => 2],
            ['input' => '7',    'expected_output' => "n=2 LHS=-1 RHS=-1 holds=True\nn=3 LHS=1 RHS=1 holds=True\nn=4 LHS=-1 RHS=-1 holds=True\nn=5 LHS=1 RHS=1 holds=True\nn=6 LHS=-1 RHS=-1 holds=True\nn=7 LHS=1 RHS=1 holds=True",  'is_hidden' => true, 'order_index' => 3],
            ['input' => '3',    'expected_output' => "n=2 LHS=-1 RHS=-1 holds=True\nn=3 LHS=1 RHS=1 holds=True",                                                                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Bezout's identity ────────────────────────────────────────
        $seed(36, [
            ['input' => "35\n15",    'expected_output' => "gcd: 5\nx: 1\ny: -2\nverify: 35*1 + 15*-2 = 5: True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "12\n8",     'expected_output' => "gcd: 4\nx: 1\ny: -1\nverify: 12*1 + 8*-1 = 4: True",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "48\n18",    'expected_output' => "gcd: 6\nx: -1\ny: 3\nverify: 48*-1 + 18*3 = 6: True",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "17\n5",     'expected_output' => "gcd: 1\nx: -2\ny: 7\nverify: 17*-2 + 5*7 = 1: True",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Smallest prime factor ────────────────────────────────────
        $seed(37, [
            ['input' => '84',    'expected_output' => "smallest prime factor: 2\nis prime: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '49',    'expected_output' => "smallest prime factor: 7\nis prime: True",    'is_hidden' => false, 'order_index' => 2],
            ['input' => '97',    'expected_output' => "smallest prime factor: 97\nis prime: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '100',   'expected_output' => "smallest prime factor: 2\nis prime: True",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Modular inverse ──────────────────────────────────────────
        $seed(38, [
            ['input' => "3\n7",      'expected_output' => 'inverse: 5',              'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n6",      'expected_output' => 'no inverse exists',       'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n11",     'expected_output' => 'inverse: 9',              'is_hidden' => true,  'order_index' => 3],
            ['input' => "7\n13",     'expected_output' => 'inverse: 2',              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: CRT ──────────────────────────────────────────────────────
        $seed(39, [
            ['input' => "2\n3\n3\n5",    'expected_output' => 'x: 8',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n3\n1\n5",    'expected_output' => 'x: 1',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n3\n0\n7",    'expected_output' => 'x: 21',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n5\n3\n7",    'expected_output' => 'x: 17',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: 2×2 linear system ────────────────────────────────────────
        $seed(40, [
            ['input' => "2 1\n1 3\n5 10",     'expected_output' => "unique solution\nx: 1.0000\ny: 3.0000",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 0\n0 1\n3 5",      'expected_output' => "unique solution\nx: 3.0000\ny: 5.0000",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 4\n1 2\n6 3",      'expected_output' => "no solution",                              'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 -1\n2 3\n0 7",     'expected_output' => "unique solution\nx: 1.4000\ny: 1.4000",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Three counterexample searches ───────────────────────────
        $seed(41, [
            ['input' => '15',    'expected_output' => "claim1: 2\nclaim2: none\nclaim3: 5",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',     'expected_output' => "claim1: none\nclaim2: none\nclaim3: none", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',     'expected_output' => "claim1: 2\nclaim2: none\nclaim3: 5",     'is_hidden' => true,  'order_index' => 3],
            ['input' => '20',    'expected_output' => "claim1: 2\nclaim2: none\nclaim3: 5",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Composite factor ≤ √n ────────────────────────────────────
        $seed(42, [
            ['input' => '10',    'expected_output' => "4: factor=2\n6: factor=2\n8: factor=2\n9: factor=3\n10: factor=2\nall confirmed",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',     'expected_output' => "4: factor=2\nall confirmed",                                                           'is_hidden' => false, 'order_index' => 2],
            ['input' => '15',    'expected_output' => "4: factor=2\n6: factor=2\n8: factor=2\n9: factor=3\n10: factor=2\n12: factor=2\n14: factor=2\n15: factor=3\nall confirmed",  'is_hidden' => true, 'order_index' => 3],
            ['input' => '6',     'expected_output' => "4: factor=2\n6: factor=2\nall confirmed",                                              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Counterexample x²>x ─────────────────────────────────────
        $seed(43, [
            ['input' => "3\n1 2\n3 1\n4 3",    'expected_output' => 'counterexample: 1/2',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3 1\n5 1",         'expected_output' => 'none',                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 4\n2 1",         'expected_output' => 'counterexample: 1/4',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n2 3",              'expected_output' => 'counterexample: 2/3',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Irrational sum counterexample ────────────────────────────
        $seed(44, [
            ['input' => "2\n1.41421356 -1.41421356\n1.73205081 0.26794919",    'expected_output' => 'counterexample: 1.41421356 + -1.41421356 = 0.0',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1.73205081 0.26794919\n1.41421356 0.58578644",     'expected_output' => 'counterexample: 1.73205081 + 0.26794919 = 2.0',     'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1.41421356 1.41421356",                            'expected_output' => 'none found',                                         'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n3.14159265 2.0\n2.71828183 -1.71828183",          'expected_output' => 'counterexample: 2.71828183 + -1.71828183 = 1.0',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Goldbach verification ────────────────────────────────────
        $seed(45, [
            ['input' => '20',    'expected_output' => "checked: 9\ncounterexample: no counterexample found",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',    'expected_output' => "checked: 4\ncounterexample: no counterexample found",     'is_hidden' => false, 'order_index' => 2],
            ['input' => '50',    'expected_output' => "checked: 24\ncounterexample: no counterexample found",    'is_hidden' => true,  'order_index' => 3],
            ['input' => '4',     'expected_output' => "checked: 1\ncounterexample: no counterexample found",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Strategy router ──────────────────────────────────────────
        $seed(46, [
            ['input' => "4\n5",    'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n6",    'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n10",   'expected_output' => 'none',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n9",    'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: AM-GM ───────────────────────────────────────────────────
        $seed(47, [
            ['input' => "3\n4 4\n9 1\n4 9",     'expected_output' => "AM=4.0000 GM=4.0000 holds=True equality=True\nAM=5.0000 GM=3.0000 holds=True equality=False\nAM=6.5000 GM=6.0000 holds=True equality=False",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 0\n1 1",          'expected_output' => "AM=0.0000 GM=0.0000 holds=True equality=True\nAM=1.0000 GM=1.0000 holds=True equality=True",                                                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n16 4\n25 1",        'expected_output' => "AM=10.0000 GM=8.0000 holds=True equality=False\nAM=13.0000 GM=5.0000 holds=True equality=False",                                               'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n3 3",               'expected_output' => "AM=3.0000 GM=3.0000 holds=True equality=True",                                                                                                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: 3a+5b representation ────────────────────────────────────
        $seed(48, [
            ['input' => '14',    'expected_output' => "8: a=1 b=1\n9: a=3 b=0\n10: a=0 b=2\n11: a=2 b=1\n12: a=4 b=0\n13: a=1 b=2\n14: a=3 b=1",      'is_hidden' => false, 'order_index' => 1],
            ['input' => '8',     'expected_output' => "8: a=1 b=1",                                                                                        'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',    'expected_output' => "8: a=1 b=1\n9: a=3 b=0\n10: a=0 b=2",                                                              'is_hidden' => true,  'order_index' => 3],
            ['input' => '12',    'expected_output' => "8: a=1 b=1\n9: a=3 b=0\n10: a=0 b=2\n11: a=2 b=1\n12: a=4 b=0",                                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Perfect squares ──────────────────────────────────────────
        $seed(49, [
            ['input' => "4\n9\n7\n16\n25",      'expected_output' => "9: direct proof (sqrt=3)\n7: counterexample (not a perfect square)\n16: direct proof (sqrt=4)\n25: direct proof (sqrt=5)\ntotal: 3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n4\n5",              'expected_output' => "4: direct proof (sqrt=2)\n5: counterexample (not a perfect square)\ntotal: 1",                                                           'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3",           'expected_output' => "1: direct proof (sqrt=1)\n2: counterexample (not a perfect square)\n3: counterexample (not a perfect square)\ntotal: 1",                'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n36",                'expected_output' => "36: direct proof (sqrt=6)\ntotal: 1",                                                                                                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Proof portfolio ──────────────────────────────────────────
        $seed(50, [
            ['input' => '5',     'expected_output' => "direct: True\ncontrapositive: True\ncontradiction: True\ninduction: True\nexistence: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',     'expected_output' => "direct: True\ncontrapositive: True\ncontradiction: True\ninduction: True\nexistence: True",    'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',    'expected_output' => "direct: True\ncontrapositive: True\ncontradiction: True\ninduction: True\nexistence: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',     'expected_output' => "direct: True\ncontrapositive: True\ncontradiction: True\ninduction: True\nexistence: True",    'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 5 Coding (University Student) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}