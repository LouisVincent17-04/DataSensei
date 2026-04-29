<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 5 — Introduction to Mathematical Proof (Newbie / Level 1) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering beginner proof concepts in Python
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
 * Difficulty: Newbie — problems are solved with pure Python.
 * Each exercise computationally mirrors the reasoning used in the
 * corresponding proof technique, making abstract logic concrete and checkable.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module5CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 5 — Introduction to Mathematical Proof (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Introduction to Mathematical Proof',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Turn mathematical proof techniques into runnable Python code. Verify direct proofs, simulate contrapositive and contradiction reasoning, exhaustively check cases, build induction witnesses, and hunt for counterexamples — all from scratch. Every exercise mirrors the logical structure of a real proof.',
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
            // TOPIC 1: Introduction to Proof (Q1–Q4)  →  Lesson 316
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Read an integer `n`. Print `True` if the **proposition** "n is even" holds, otherwise print `False`.

Example:
```
Input:  4
Output: True
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read an integer `n`. A statement claims: "if n > 0 then n² > 0". **Verify** this for the given `n`: print `True` if the implication holds (i.e. the hypothesis is false OR the conclusion is true), otherwise `False`.

Example:
```
Input:  3
Output: True
```
MD,
                'starter_code'        => "n = int(input())\n# Implication: if n > 0 then n*n > 0\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read two integers `p` and `q` (representing truth values as 1 = True, 0 = False). Print the truth value of **p → q** (p implies q). Recall: p → q is False only when p is True and q is False.

Example:
```
Input:
1
0
Output: False
```
MD,
                'starter_code'        => "p = int(input())\nq = int(input())\n# p implies q: False only when p=1 and q=0\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read an integer `n`. Print the **negation** of each of the following propositions on separate lines (True/False):
1. n is positive
2. n is even
3. n equals 0

Example:
```
Input:  5
Output:
False
True
True
```
MD,
                'starter_code'        => "n = int(input())\n# Print the negation of each proposition\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Direct Proof (Q5–Q9)  →  Lesson 317
            // ═══════════════════════════════════════════════════════════════

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
**Direct proof** that the sum of two even numbers is even. Read two integers `a` and `b`. Print `True` if both are even and their sum is also even. Otherwise print `False`.

Example:
```
Input:
4
6
Output: True
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Check: both even AND sum is even\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Direct proof that the **product of two odd numbers is odd**. Read two integers `a` and `b`. Print `True` if both are odd and their product is also odd. Otherwise print `False`.

Example:
```
Input:
3
5
Output: True
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Check: both odd AND product is odd\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Direct proof that if `n` is even, then `n²` is even. Read an integer `n`. Print `True` if whenever n is even, n² is also even. Print `False` otherwise.

Example:
```
Input:  6
Output: True
```
MD,
                'starter_code'        => "n = int(input())\n# If n is even, verify n*n is even\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Direct proof that for any integer `n`, the expression **n(n + 1)** is always even (one of n or n+1 must be even). Read `n` and print `True` if n*(n+1) is even, else `False`.

Example:
```
Input:  7
Output: True
```
MD,
                'starter_code'        => "n = int(input())\n# Check n*(n+1) is always even\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Direct proof that the **sum of the first n positive integers** equals n(n+1)/2. Read `n`. Compute the sum both by adding 1 + 2 + ... + n and by the formula. Print `True` if they match, otherwise `False`.

Example:
```
Input:  10
Output: True
```
MD,
                'starter_code'        => "n = int(input())\n# Compare loop sum with formula n*(n+1)//2\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Proof by Contrapositive (Q10–Q14)  →  Lesson 318
            // ═══════════════════════════════════════════════════════════════

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
The **contrapositive** of "P → Q" is "¬Q → ¬P". Read two integers `p` and `q` (truth values: 1 or 0). Print the truth value of the original implication and its contrapositive on separate lines. They must always match.

Format:
```
original: True/False
contrapositive: True/False
```

Example:
```
Input:
1
0
Output:
original: False
contrapositive: False
```
MD,
                'starter_code'        => "p = int(input())\nq = int(input())\n# implication: not(p=1 and q=0)\n# contrapositive: not(q=0 and p=1) ... same logic\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Prove by contrapositive: "If n² is odd, then n is odd." Read an integer `n`. Using the contrapositive ("if n is even, then n² is even"), verify: print `True` if the reasoning holds for this `n`, else `False`.

(Hint: check that n being even implies n² is even — this is equivalent to the original claim.)

Example:
```
Input:  4
Output: True
```
MD,
                'starter_code'        => "n = int(input())\n# Contrapositive: if n is even then n*n is even\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Prove by contrapositive: "If the product a×b is odd, then both a and b are odd." Read two integers `a` and `b`. Check the **contrapositive**: "if a is even OR b is even, then a×b is even." Print `True` if the contrapositive holds for these values, `False` otherwise.

Example:
```
Input:
3
5
Output: True
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Contrapositive: if (a even or b even) then (a*b even)\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Read `n` integers and check the contrapositive of: "If all numbers are positive, then their product is positive." The contrapositive is: "If the product is **not** positive, then **not** all numbers are positive (at least one is ≤ 0)."

Print `True` if the contrapositive holds for the given list, otherwise `False`.

Example:
```
Input:
3
1
-2
3
Output: True
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Read an integer `n`. Verify the contrapositive: "If n is not divisible by 3, then n² is not divisible by 3." Check both the original statement and its contrapositive and print whether they agree.

Print `True` if both the original and contrapositive give the same truth value, otherwise `False`.

Format:
```
original: True/False
contrapositive: True/False
agree: True/False
```

Example:
```
Input:  7
Output:
original: True
contrapositive: True
agree: True
```
MD,
                'starter_code'        => "n = int(input())\n# original: (n % 3 != 0) -> (n*n % 3 != 0)\n# contrapositive: (n*n % 3 == 0) -> (n % 3 == 0)\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Proof by Contradiction (Q15–Q19)  →  Lesson 319
            // ═══════════════════════════════════════════════════════════════

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
**Proof by contradiction** of "√2 is irrational": assume √2 = p/q in lowest terms, then 2q² = p² forces p even, then q even — contradiction. Simulate this: read a fraction `p q` (integers). Print `True` if p²/q² equals 2 exactly (i.e., p*p == 2*q*q), which would be a contradiction (no such integers exist). Otherwise print `False`.

Example:
```
Input:
14
10
Output: False
```
MD,
                'starter_code'        => "p = int(input())\nq = int(input())\n# Check if p*p == 2*q*q\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Proof by contradiction that there is **no largest even integer**. Read an integer `n`. Assume `n` is the largest even integer. Show the contradiction: n+2 is also even and larger. Print `n+2` and `even` to demonstrate the contradiction, always.

Example:
```
Input:  100
Output:
102
even
```
MD,
                'starter_code'        => "n = int(input())\n# Assume n is the largest even integer -> contradiction: n+2 is even and larger\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Proof by contradiction: "If n² is even, then n is even." Assume n is **odd**, so n = 2k+1. Then n² = 4k²+4k+1, which is **odd** — contradicting n² being even. Read `n`. If n is odd, compute n² and verify it is odd (print `contradiction: n odd but n^2 odd`). If n is even, print `consistent`.

Example:
```
Input:  3
Output: contradiction: n odd but n^2 odd
```
MD,
                'starter_code'        => "n = int(input())\n# Check if n is odd; if so, n*n should also be odd -> contradiction with 'n^2 even'\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Proof by contradiction that there are **infinitely many primes** (Euclid). Given a list of `n` primes, compute P = (product of all primes) + 1. Check if P is divisible by any prime in the list. If none divides it, print `no prime in list divides P`, demonstrating the contradiction that P must have a new prime factor. If one does divide it (impossible for true primes), print `divisible by <prime>`.

Example:
```
Input:
3
2
3
5
Output: no prime in list divides P
```
MD,
                'starter_code'        => "n = int(input())\nprimes = [int(input()) for _ in range(n)]\n# Compute P = product + 1, check divisibility\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Proof by contradiction: "If a + b > 10, then a > 5 or b > 5." To prove this, assume both a ≤ 5 AND b ≤ 5, which gives a + b ≤ 10 — contradicting a + b > 10. Read two integers `a` and `b`. If a + b > 10, check whether assuming both a ≤ 5 and b ≤ 5 leads to contradiction. Print `contradiction` or `no contradiction`.

Example:
```
Input:
6
7
Output: contradiction
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# If a+b > 10, check if a<=5 AND b<=5 is contradictory\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Proof by Cases / Exhaustion (Q20–Q24)  →  Lesson 320
            // ═══════════════════════════════════════════════════════════════

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Proof by cases: for any integer n, n² mod 4 is either 0 or 1. Read `n`. Print the case: `even case: n^2 mod 4 = 0` if n is even, or `odd case: n^2 mod 4 = 1` if n is odd.

Example:
```
Input:  3
Output: odd case: n^2 mod 4 = 1
```
MD,
                'starter_code'        => "n = int(input())\n# Cases: n even or n odd\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Proof by cases that **|a + b| ≤ |a| + |b|** (triangle inequality). Read two integers `a` and `b`. Print the **case** that applies (based on signs of a and b) and verify the inequality. Use cases: `both non-negative`, `both negative`, `mixed signs`. Print the case label and `holds`.

Example:
```
Input:
-3
5
Output:
mixed signs
holds
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Determine the sign case and verify |a+b| <= |a|+|b|\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Proof by cases: for any integer n, exactly one of the following is true:
- n ≡ 0 (mod 3)
- n ≡ 1 (mod 3)
- n ≡ 2 (mod 3)

Read `n` and print which case applies and the value of `n mod 3`.

Format: `case <r>: n mod 3 = <r>`

Example:
```
Input:  7
Output: case 1: n mod 3 = 1
```
MD,
                'starter_code'        => "n = int(input())\n# Check n % 3 and print the case\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Proof by exhaustion for small cases: prove that all integers from 1 to `n` satisfy either "divisible by 2" OR "divisible by 3" OR "is prime". Read `n`. For each integer from 1 to n, classify it and print `<number>: <classification>`. Classification is `div2`, `div3`, `prime`, or `other`.

(1 is classified as `other`. For divisibility, check div2 first, then div3, then prime.)

Example:
```
Input: 6
Output:
1: other
2: div2
3: div3
4: div2
5: prime
6: div2
```
MD,
                'starter_code'        => "n = int(input())\n# For each i from 1 to n, classify as div2, div3, prime, or other\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Proof by cases that **n³ − n is always divisible by 6**. Note n³ − n = n(n−1)(n+1): three consecutive integers always include a multiple of 2 and a multiple of 3. Read `n`. Print `True` if (n³ − n) % 6 == 0, else `False`.

Example:
```
Input:  5
Output: True
```
MD,
                'starter_code'        => "n = int(input())\n# Check (n**3 - n) % 6 == 0\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Mathematical Induction (Q25–Q29)  →  Lesson 321
            // ═══════════════════════════════════════════════════════════════

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Mathematical induction: verify that **1 + 2 + ... + n = n(n+1)/2** for all n from 1 to `N`. Read `N`. For each n from 1 to N, print `True` if the formula holds, else `False`. (It always holds — you are building the induction table.)

Example:
```
Input: 4
Output:
True
True
True
True
```
MD,
                'starter_code'        => "N = int(input())\n# For each n from 1 to N, verify sum(1..n) == n*(n+1)//2\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Induction: verify that **1² + 2² + ... + n² = n(n+1)(2n+1)/6** for all n from 1 to `N`. Read `N` and print `True`/`False` for each n.

Example:
```
Input: 3
Output:
True
True
True
```
MD,
                'starter_code'        => "N = int(input())\n# For each n from 1 to N, verify sum of squares == n*(n+1)*(2*n+1)//6\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Induction: verify the **inductive step** for the sum formula. Given `k` (the current step), assume 1 + 2 + ... + k = k(k+1)/2 is True. Verify that 1 + 2 + ... + k + (k+1) = (k+1)(k+2)/2 is also True. Read `k`, print `inductive step holds` or `inductive step fails`.

Example:
```
Input:  5
Output: inductive step holds
```
MD,
                'starter_code'        => "k = int(input())\n# Assume S(k) = k*(k+1)//2. Verify S(k) + (k+1) == (k+1)*(k+2)//2\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Induction: prove that **2ⁿ > n** for all n ≥ 1. Read `N`. For each n from 1 to N, print `True` if 2**n > n, else `False`.

Example:
```
Input: 5
Output:
True
True
True
True
True
```
MD,
                'starter_code'        => "N = int(input())\n# For each n from 1 to N, check 2**n > n\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Induction: verify that for all n ≥ 0, **3 divides (n³ + 2n)**. Read `N`. For each n from 0 to N, print `True` if (n³ + 2n) % 3 == 0, else `False`.

Example:
```
Input: 4
Output:
True
True
True
True
True
```
MD,
                'starter_code'        => "N = int(input())\n# For each n from 0 to N, check (n**3 + 2*n) % 3 == 0\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Strong Induction (Q30–Q33)  →  Lesson 322
            // ═══════════════════════════════════════════════════════════════

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
**Strong induction** — Fibonacci: F(1) = 1, F(2) = 1, F(n) = F(n−1) + F(n−2). Read `n` and compute F(n) using the recurrence (bottom-up, not recursive). Print the result.

Example:
```
Input:  7
Output: 13
```
MD,
                'starter_code'        => "n = int(input())\n# Compute Fibonacci using strong induction (bottom-up)\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Strong induction: verify that every integer n ≥ 2 is either **prime** or a **product of primes** (Fundamental Theorem of Arithmetic). Read `n`. Print its **prime factorization** in ascending order, space-separated. If n itself is prime, print just n.

Example:
```
Input:  12
Output: 2 2 3
```
MD,
                'starter_code'        => "n = int(input())\nfactors = []\n# Find prime factorization\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Strong induction: compute the number of ways to make change for `n` cents using coins of denomination 1 cent and 2 cents (the **staircase problem**). Let f(n) = f(n-1) + f(n-2) with f(0) = 1, f(1) = 1. Read `n` and print f(n).

Example:
```
Input:  5
Output: 8
```
MD,
                'starter_code'        => "n = int(input())\n# f(0)=1, f(1)=1, f(n)=f(n-1)+f(n-2)\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Strong induction: verify that the Lucas sequence L(n) satisfies L(n) = L(n−1) + L(n−2) with L(1) = 1, L(2) = 3. Read `N` and print L(1), L(2), …, L(N), one per line.

Example:
```
Input: 5
Output:
1
3
4
7
11
```
MD,
                'starter_code'        => "N = int(input())\n# L(1)=1, L(2)=3, L(n)=L(n-1)+L(n-2)\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Existence & Uniqueness (Q34–Q38)  →  Lesson 323
            // ═══════════════════════════════════════════════════════════════

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
**Existence proof**: read `n` integers. Print `exists` and the first even number found. If no even number exists, print `does not exist`.

Example:
```
Input:
4
1
3
4
7
Output:
exists
4
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Find first even number\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
**Uniqueness proof**: a linear equation ax + b = 0 has a unique solution x = −b/a when a ≠ 0. Read `a` and `b`. If a == 0 and b == 0, print `infinite solutions`. If a == 0 and b ≠ 0, print `no solution`. Otherwise print `unique solution: x = <value>` rounded to 4 decimal places.

Example:
```
Input:
2
-6
Output: unique solution: x = 3.0000
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\n# Solve ax + b = 0\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
**Existence**: prove there exists a prime between n and 2n for n ≥ 1 (Bertrand's Postulate). Read `n`. Find and print the **smallest prime** strictly between n and 2n. If none found (only possible for very small n), print `none`.

Example:
```
Input:  5
Output: 7
```
MD,
                'starter_code'        => "n = int(input())\n# Find smallest prime in (n, 2*n)\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
**Uniqueness**: verify that the GCD of two positive integers is unique. Read `a` and `b`. Compute gcd(a, b) using the Euclidean algorithm. Print the GCD and verify that it divides both a and b. Print `True` if verified, else `False`.

Format:
```
gcd: <value>
divides both: True/False
```

Example:
```
Input:
48
18
Output:
gcd: 6
divides both: True
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Compute gcd using Euclidean algorithm\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
**Existence**: read `n` integers. Check whether there **exists** a pair (a, b) in the list with a + b = 0. Print `True` and the pair (smaller first) if found, else `False`.

Example:
```
Input:
4
-3
1
3
5
Output:
True
-3 3
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Find any pair summing to 0\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Counterexamples (Q39–Q43)  →  Lesson 324
            // ═══════════════════════════════════════════════════════════════

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Find a **counterexample** to the claim "n² + n + 41 is prime for all n ≥ 0." Read a range `N`. Search integers from 0 to N and print the **first** n where n² + n + 41 is **not** prime. If no counterexample found, print `none found`.

Example:
```
Input: 50
Output: none found
```
MD,
                'starter_code'        => "N = int(input())\n# Search n in range(0, N+1) where n*n + n + 41 is not prime\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Find a **counterexample** to the claim "if n is prime then n is odd." Read `n` integers (one per line after `n`). Print the first prime that is **even** (i.e., 2), or `none` if no even prime is in the list.

Example:
```
Input:
4
3
5
2
7
Output: 2
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Find first even prime in list\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
**Disprove by counterexample**: "For all integers a and b, (a + b)² = a² + b²." Read two integers `a` and `b`. Print `True` if the equation holds (only when a = 0 or b = 0) or `False` (counterexample found). Then print `2ab = <value>` to show the missing cross term.

Example:
```
Input:
2
3
Output:
False
2ab = 12
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Check (a+b)**2 == a**2 + b**2\n# Print 2ab regardless\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
**Disprove**: "For all n ≥ 1, n! > 2ⁿ." Find the smallest `n` in range 1 to `N` where this **fails** (n! ≤ 2ⁿ). Print that `n` and both values.

Format:
```
n: <value>
n!: <value>
2^n: <value>
```

Example:
```
Input: 10
Output:
n: 1
n!: 1
2^n: 2
```
MD,
                'starter_code'        => "N = int(input())\n# Find smallest n in 1..N where n! <= 2**n\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
**Disprove**: "For all real numbers a and b, |a + b| = |a| + |b|." Read two integers `a` and `b`. Print `True` if |a + b| == |a| + |b| (holds when same sign or one is zero), or `False`. Print the `deficit = |a| + |b| - |a+b|`.

Format:
```
True/False
deficit: <value>
```

Example:
```
Input:
3
-5
Output:
False
deficit: 4
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Check |a+b| == |a|+|b|\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Choosing the Right Strategy (Q44–Q50) → Lesson 325
            // ═══════════════════════════════════════════════════════════════

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Read a claim code (1–4) and an integer `n`. Apply the appropriate proof technique and print the result:
- 1: Direct — verify n(n+1) is even
- 2: Contrapositive — if n is odd, verify n² is odd
- 3: Contradiction — if n is odd, show n+1 is even (consistent, not contradiction)
- 4: Exhaustion — print n mod 2 (the case: even or odd)

Print `True`/`False` for 1–3, or `even`/`odd` for 4.

Example:
```
Input:
3
7
Output: True
```
MD,
                'starter_code'        => "claim = int(input())\nn = int(input())\n# Route to the appropriate proof technique\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Read `n` integers. For each, determine the **best proof strategy** to show it is composite (not prime):
- `trial division`: find a factor and print it
- `prime`: no factor found, print `prime`

Print for each integer: `<n>: prime` or `<n>: composite (factor=<f>)`.

Example:
```
Input:
3
7
9
15
Output:
7: prime
9: composite (factor=3)
15: composite (factor=3)
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# For each number, check if prime or composite\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Read `n` and verify the claim: **for all k from 1 to n**, k² − k + 1 is odd. Use exhaustion. Print `True` for each k if the claim holds, else `False`.

Example:
```
Input: 5
Output:
True
True
True
True
True
```
MD,
                'starter_code'        => "n = int(input())\n# For k in 1..n, check (k*k - k + 1) % 2 == 1\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Demonstrate the **well-ordering principle**: every non-empty set of positive integers has a least element. Read `n` positive integers. Print the **minimum** element and its index (0-based, first occurrence).

Format:
```
min: <value>
index: <value>
```

Example:
```
Input:
4
7
3
5
3
Output:
min: 3
index: 1
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Find minimum and its first index\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
**Choose the strategy**: read a number `n` and determine which single proof technique applies to show "n is divisible by 6 if and only if n is divisible by 2 and divisible by 3." Check both directions:

1. Forward (→): if n % 6 == 0, verify n % 2 == 0 and n % 3 == 0.
2. Backward (←): if n % 2 == 0 and n % 3 == 0, verify n % 6 == 0.

Print `True` for each direction on separate lines.

Format:
```
forward: True/False
backward: True/False
```

Example:
```
Input:  12
Output:
forward: True
backward: True
```
MD,
                'starter_code'        => "n = int(input())\n# Check both directions of the biconditional\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
**Proof selection**: read `n` integers from 1 to 100. For each, suggest the proof strategy to verify it is either prime or composite:
- If prime: print `<n>: direct proof (prime)`
- If composite: print `<n>: counterexample (factor=<smallest factor>)`

Example:
```
Input:
3
11
12
17
Output:
11: direct proof (prime)
12: counterexample (factor=2)
17: direct proof (prime)
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Classify and suggest strategy\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**Full proof pipeline**: read an integer `n` and verify all of the following properties using the matching technique. Print `True` or `False` for each:

1. Direct: n(n+1) is even
2. Induction step: sum(1..n) == n*(n+1)//2
3. Contradiction: if n > 1, then n + 1 > 2 (trivially always true)
4. Counterexample: n² ≠ n + 1 (True for most n, False when n=1+√1... i.e. only n≥2 are usually non-counterexamples)
5. Existence: there is a prime ≤ n+1

Print each result on a separate line.

Example:
```
Input: 5
Output:
True
True
True
True
True
```
MD,
                'starter_code'        => "n = int(input())\n# 1. n*(n+1) % 2 == 0\n# 2. sum(range(1,n+1)) == n*(n+1)//2\n# 3. (n <= 1) or (n+1 > 2)\n# 4. n*n != n+1\n# 5. any prime in range 2, n+2\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
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

        // ── Q1: n is even ─────────────────────────────────────────────────
        $seed(1, [
            ['input' => '4',   'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '7',   'expected_output' => 'False',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',   'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '-3',  'expected_output' => 'False',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Implication n>0 → n²>0 ───────────────────────────────────
        $seed(2, [
            ['input' => '3',    'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',    'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '-5',   'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '100',  'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: p → q truth value ─────────────────────────────────────────
        $seed(3, [
            ['input' => "1\n0",  'expected_output' => 'False',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1",  'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0",  'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n1",  'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Negation of three propositions ────────────────────────────
        $seed(4, [
            ['input' => '5',    'expected_output' => "False\nTrue\nTrue",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',    'expected_output' => "True\nFalse\nFalse",   'is_hidden' => false, 'order_index' => 2],
            ['input' => '-2',   'expected_output' => "True\nFalse\nTrue",    'is_hidden' => true,  'order_index' => 3],
            ['input' => '4',    'expected_output' => "False\nFalse\nTrue",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Sum of two evens ──────────────────────────────────────────
        $seed(5, [
            ['input' => "4\n6",    'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5",    'expected_output' => 'False',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0",    'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n7",    'expected_output' => 'False',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Product of two odds ───────────────────────────────────────
        $seed(6, [
            ['input' => "3\n5",    'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5",    'expected_output' => 'False',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1",    'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "7\n4",    'expected_output' => 'False',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: n even → n² even ─────────────────────────────────────────
        $seed(7, [
            ['input' => '6',    'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',    'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',    'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '10',   'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: n(n+1) always even ────────────────────────────────────────
        $seed(8, [
            ['input' => '7',    'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',    'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',    'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '99',   'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Sum formula check ─────────────────────────────────────────
        $seed(9, [
            ['input' => '10',   'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',    'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '100',  'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '0',    'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Original vs contrapositive ───────────────────────────────
        $seed(10, [
            ['input' => "1\n0",  'expected_output' => "original: False\ncontrapositive: False",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1",  'expected_output' => "original: True\ncontrapositive: True",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0",  'expected_output' => "original: True\ncontrapositive: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n1",  'expected_output' => "original: True\ncontrapositive: True",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Contrapositive: n² odd → n odd ──────────────────────────
        $seed(11, [
            ['input' => '4',    'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',    'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',    'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '11',   'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Contrapositive: a×b odd → both odd ───────────────────────
        $seed(12, [
            ['input' => "3\n5",   'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5",   'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n6",   'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "7\n9",   'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Contrapositive over list ─────────────────────────────────
        $seed(13, [
            ['input' => "3\n1\n-2\n3",      'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",       'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n-1\n-2\n-3\n-4",'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n5\n10",         'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Divisibility by 3 contrapositive ─────────────────────────
        $seed(14, [
            ['input' => '7',    'expected_output' => "original: True\ncontrapositive: True\nagree: True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '9',    'expected_output' => "original: True\ncontrapositive: True\nagree: True",     'is_hidden' => false, 'order_index' => 2],
            ['input' => '6',    'expected_output' => "original: True\ncontrapositive: True\nagree: True",     'is_hidden' => true,  'order_index' => 3],
            ['input' => '11',   'expected_output' => "original: True\ncontrapositive: True\nagree: True",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: √2 fraction check ────────────────────────────────────────
        $seed(15, [
            ['input' => "14\n10",   'expected_output' => 'False',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1",     'expected_output' => 'False',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n5",     'expected_output' => 'False',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2",     'expected_output' => 'False',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: No largest even ──────────────────────────────────────────
        $seed(16, [
            ['input' => '100',   'expected_output' => "102\neven",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',     'expected_output' => "2\neven",    'is_hidden' => false, 'order_index' => 2],
            ['input' => '1000',  'expected_output' => "1002\neven", 'is_hidden' => true,  'order_index' => 3],
            ['input' => '50',    'expected_output' => "52\neven",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Contradiction n odd → n² odd ────────────────────────────
        $seed(17, [
            ['input' => '3',   'expected_output' => 'contradiction: n odd but n^2 odd',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',   'expected_output' => 'consistent',                         'is_hidden' => false, 'order_index' => 2],
            ['input' => '7',   'expected_output' => 'contradiction: n odd but n^2 odd',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '10',  'expected_output' => 'consistent',                         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Euclid's infinitely many primes ─────────────────────────
        $seed(18, [
            ['input' => "3\n2\n3\n5",    'expected_output' => 'no prime in list divides P',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2\n3",       'expected_output' => 'no prime in list divides P',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2",          'expected_output' => 'no prime in list divides P',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2\n3\n5\n7", 'expected_output' => 'no prime in list divides P',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: a+b>10 contradiction ─────────────────────────────────────
        $seed(19, [
            ['input' => "6\n7",     'expected_output' => 'contradiction',     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n4",     'expected_output' => 'no contradiction',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n6",     'expected_output' => 'contradiction',     'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n10",   'expected_output' => 'contradiction',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: n² mod 4 cases ───────────────────────────────────────────
        $seed(20, [
            ['input' => '3',   'expected_output' => 'odd case: n^2 mod 4 = 1',    'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',   'expected_output' => 'even case: n^2 mod 4 = 0',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '1',   'expected_output' => 'odd case: n^2 mod 4 = 1',    'is_hidden' => true,  'order_index' => 3],
            ['input' => '0',   'expected_output' => 'even case: n^2 mod 4 = 0',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Triangle inequality cases ───────────────────────────────
        $seed(21, [
            ['input' => "-3\n5",    'expected_output' => "mixed signs\nholds",         'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5",     'expected_output' => "both non-negative\nholds",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "-3\n-5",   'expected_output' => "both negative\nholds",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n7",     'expected_output' => "both non-negative\nholds",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: n mod 3 cases ────────────────────────────────────────────
        $seed(22, [
            ['input' => '7',    'expected_output' => 'case 1: n mod 3 = 1',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '9',    'expected_output' => 'case 0: n mod 3 = 0',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',    'expected_output' => 'case 2: n mod 3 = 2',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '3',    'expected_output' => 'case 0: n mod 3 = 0',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Classification 1 to n ────────────────────────────────────
        $seed(23, [
            ['input' => '6',   'expected_output' => "1: other\n2: div2\n3: div3\n4: div2\n5: prime\n6: div2",     'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',   'expected_output' => "1: other\n2: div2\n3: div3",                                  'is_hidden' => false, 'order_index' => 2],
            ['input' => '7',   'expected_output' => "1: other\n2: div2\n3: div3\n4: div2\n5: prime\n6: div2\n7: prime", 'is_hidden' => true, 'order_index' => 3],
            ['input' => '1',   'expected_output' => "1: other",                                                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: n³ − n divisible by 6 ───────────────────────────────────
        $seed(24, [
            ['input' => '5',    'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',    'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',    'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '100',  'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Induction table sum ───────────────────────────────────────
        $seed(25, [
            ['input' => '4',   'expected_output' => "True\nTrue\nTrue\nTrue",                    'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',   'expected_output' => "True",                                      'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',   'expected_output' => "True\nTrue\nTrue\nTrue\nTrue",              'is_hidden' => true,  'order_index' => 3],
            ['input' => '3',   'expected_output' => "True\nTrue\nTrue",                          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Induction sum of squares ────────────────────────────────
        $seed(26, [
            ['input' => '3',   'expected_output' => "True\nTrue\nTrue",                          'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',   'expected_output' => "True",                                      'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',   'expected_output' => "True\nTrue\nTrue\nTrue\nTrue",              'is_hidden' => true,  'order_index' => 3],
            ['input' => '6',   'expected_output' => "True\nTrue\nTrue\nTrue\nTrue\nTrue",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Inductive step ───────────────────────────────────────────
        $seed(27, [
            ['input' => '5',    'expected_output' => 'inductive step holds',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',    'expected_output' => 'inductive step holds',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',   'expected_output' => 'inductive step holds',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '100',  'expected_output' => 'inductive step holds',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: 2^n > n ──────────────────────────────────────────────────
        $seed(28, [
            ['input' => '5',   'expected_output' => "True\nTrue\nTrue\nTrue\nTrue",             'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',   'expected_output' => "True",                                     'is_hidden' => false, 'order_index' => 2],
            ['input' => '3',   'expected_output' => "True\nTrue\nTrue",                         'is_hidden' => true,  'order_index' => 3],
            ['input' => '10',  'expected_output' => "True\nTrue\nTrue\nTrue\nTrue\nTrue\nTrue\nTrue\nTrue\nTrue", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q29: 3 | n³+2n ────────────────────────────────────────────────
        $seed(29, [
            ['input' => '4',   'expected_output' => "True\nTrue\nTrue\nTrue\nTrue",              'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',   'expected_output' => "True\nTrue\nTrue",                          'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',   'expected_output' => "True\nTrue\nTrue\nTrue\nTrue\nTrue",        'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',   'expected_output' => "True\nTrue",                               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Fibonacci ────────────────────────────────────────────────
        $seed(30, [
            ['input' => '7',   'expected_output' => '13',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',   'expected_output' => '1',    'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',  'expected_output' => '55',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '2',   'expected_output' => '1',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Prime factorization ──────────────────────────────────────
        $seed(31, [
            ['input' => '12',   'expected_output' => '2 2 3',       'is_hidden' => false, 'order_index' => 1],
            ['input' => '7',    'expected_output' => '7',           'is_hidden' => false, 'order_index' => 2],
            ['input' => '60',   'expected_output' => '2 2 3 5',     'is_hidden' => true,  'order_index' => 3],
            ['input' => '100',  'expected_output' => '2 2 5 5',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Staircase ways ───────────────────────────────────────────
        $seed(32, [
            ['input' => '5',   'expected_output' => '8',    'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',   'expected_output' => '1',    'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',  'expected_output' => '89',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',   'expected_output' => '1',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Lucas sequence ───────────────────────────────────────────
        $seed(33, [
            ['input' => '5',   'expected_output' => "1\n3\n4\n7\n11",            'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',   'expected_output' => "1\n3",                      'is_hidden' => false, 'order_index' => 2],
            ['input' => '7',   'expected_output' => "1\n3\n4\n7\n11\n18\n29",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',   'expected_output' => "1",                         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: First even ───────────────────────────────────────────────
        $seed(34, [
            ['input' => "4\n1\n3\n4\n7",    'expected_output' => "exists\n4",         'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n3\n5",       'expected_output' => "does not exist",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2",             'expected_output' => "exists\n2",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n7\n8\n9",       'expected_output' => "exists\n8",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Unique solution of ax+b=0 ────────────────────────────────
        $seed(35, [
            ['input' => "2\n-6",    'expected_output' => 'unique solution: x = 3.0000',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n5",     'expected_output' => 'no solution',                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0",     'expected_output' => 'infinite solutions',             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n-9",    'expected_output' => 'unique solution: x = 3.0000',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Bertrand's Postulate ─────────────────────────────────────
        $seed(36, [
            ['input' => '5',    'expected_output' => '7',    'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',   'expected_output' => '11',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '20',   'expected_output' => '23',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',    'expected_output' => 'none', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: GCD uniqueness ───────────────────────────────────────────
        $seed(37, [
            ['input' => "48\n18",   'expected_output' => "gcd: 6\ndivides both: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "12\n8",    'expected_output' => "gcd: 4\ndivides both: True",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n13",    'expected_output' => "gcd: 1\ndivides both: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "100\n25",  'expected_output' => "gcd: 25\ndivides both: True",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Pair summing to 0 ────────────────────────────────────────
        $seed(38, [
            ['input' => "4\n-3\n1\n3\n5",     'expected_output' => "True\n-3 3",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",         'expected_output' => "False",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n-5\n5",           'expected_output' => "True\n-5 5",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0\n1\n2",         'expected_output' => "False",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Counterexample n²+n+41 ──────────────────────────────────
        $seed(39, [
            ['input' => '50',    'expected_output' => 'none found',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '40',    'expected_output' => 'none found',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '41',    'expected_output' => '40',          'is_hidden' => true,  'order_index' => 3],
            ['input' => '100',   'expected_output' => '40',          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Even prime ───────────────────────────────────────────────
        $seed(40, [
            ['input' => "4\n3\n5\n2\n7",      'expected_output' => '2',     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3\n5\n7",         'expected_output' => 'none',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2",               'expected_output' => '2',     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n11\n13\n17\n19",  'expected_output' => 'none',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: (a+b)² ≠ a²+b² ──────────────────────────────────────────
        $seed(41, [
            ['input' => "2\n3",     'expected_output' => "False\n2ab = 12",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n5",     'expected_output' => "True\n2ab = 0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n6",     'expected_output' => "False\n2ab = 48",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n0",     'expected_output' => "True\n2ab = 0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: n! vs 2^n ────────────────────────────────────────────────
        $seed(42, [
            ['input' => '10',   'expected_output' => "n: 1\nn!: 1\n2^n: 2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '5',    'expected_output' => "n: 1\nn!: 1\n2^n: 2",  'is_hidden' => false, 'order_index' => 2],
            ['input' => '3',    'expected_output' => "n: 1\nn!: 1\n2^n: 2",  'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',    'expected_output' => "n: 1\nn!: 1\n2^n: 2",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: |a+b| = |a|+|b| disproof ────────────────────────────────
        $seed(43, [
            ['input' => "3\n-5",    'expected_output' => "False\ndeficit: 4",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5",     'expected_output' => "True\ndeficit: 0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "-4\n4",    'expected_output' => "False\ndeficit: 8",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n7",     'expected_output' => "True\ndeficit: 0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Proof strategy router ────────────────────────────────────
        $seed(44, [
            ['input' => "3\n7",    'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n6",    'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4",    'expected_output' => 'even',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n9",    'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Prime or composite strategy ─────────────────────────────
        $seed(45, [
            ['input' => "3\n7\n9\n15",          'expected_output' => "7: prime\n9: composite (factor=3)\n15: composite (factor=3)",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n11\n12",            'expected_output' => "11: prime\n12: composite (factor=2)",                            'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2\n4\n5",           'expected_output' => "2: prime\n4: composite (factor=2)\n5: prime",                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n25",               'expected_output' => "25: composite (factor=5)",                                        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: k²−k+1 is odd ────────────────────────────────────────────
        $seed(46, [
            ['input' => '5',   'expected_output' => "True\nTrue\nTrue\nTrue\nTrue",             'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',   'expected_output' => "True",                                     'is_hidden' => false, 'order_index' => 2],
            ['input' => '3',   'expected_output' => "True\nTrue\nTrue",                         'is_hidden' => true,  'order_index' => 3],
            ['input' => '4',   'expected_output' => "True\nTrue\nTrue\nTrue",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Well-ordering minimum ────────────────────────────────────
        $seed(47, [
            ['input' => "4\n7\n3\n5\n3",      'expected_output' => "min: 3\nindex: 1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10\n20\n30",      'expected_output' => "min: 10\nindex: 0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n42",              'expected_output' => "min: 42\nindex: 0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n5\n5\n5\n1",      'expected_output' => "min: 1\nindex: 3",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Biconditional divisibility by 6 ──────────────────────────
        $seed(48, [
            ['input' => '12',   'expected_output' => "forward: True\nbackward: True",   'is_hidden' => false, 'order_index' => 1],
            ['input' => '7',    'expected_output' => "forward: True\nbackward: True",   'is_hidden' => false, 'order_index' => 2],
            ['input' => '6',    'expected_output' => "forward: True\nbackward: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '9',    'expected_output' => "forward: True\nbackward: True",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Proof strategy labels ────────────────────────────────────
        $seed(49, [
            ['input' => "3\n11\n12\n17",   'expected_output' => "11: direct proof (prime)\n12: counterexample (factor=2)\n17: direct proof (prime)",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2\n4",         'expected_output' => "2: direct proof (prime)\n4: counterexample (factor=2)",                                 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n13\n14\n15",   'expected_output' => "13: direct proof (prime)\n14: counterexample (factor=2)\n15: counterexample (factor=3)", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n7",            'expected_output' => "7: direct proof (prime)",                                                                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Full proof pipeline ──────────────────────────────────────
        $seed(50, [
            ['input' => '5',    'expected_output' => "True\nTrue\nTrue\nTrue\nTrue",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',    'expected_output' => "True\nTrue\nTrue\nTrue\nTrue",  'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',   'expected_output' => "True\nTrue\nTrue\nTrue\nTrue",  'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',    'expected_output' => "True\nTrue\nTrue\nTrue\nTrue",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 5 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}