<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 5 — Introduction to Mathematical Proof (Advanced / Level 3) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Advanced tier
 *   2. coding_questions    — 50 questions covering advanced proof concepts in Python
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (lessons 316–325):
 *   L5.1  Introduction to Mathematical Proof        Q1–Q5
 *   L5.2  Direct Proof                              Q6–Q10
 *   L5.3  Proof by Contrapositive                   Q11–Q15
 *   L5.4  Proof by Contradiction                    Q16–Q20
 *   L5.5  Proof by Cases (Exhaustion)               Q21–Q25
 *   L5.6  Mathematical Induction                    Q26–Q30
 *   L5.7  Strong Induction                          Q31–Q35
 *   L5.8  Existence and Uniqueness Proofs           Q36–Q40
 *   L5.9  Disproving Statements: Counterexamples    Q41–Q45
 *   L5.10 Choosing the Right Proof Strategy         Q46–Q50
 *
 * Difficulty: Advanced — problems require algorithmic design, number theory,
 * combinatorics, and multi-step logical reasoning implemented in clean Python.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module5CodingChallengeSeederAdvanced extends Seeder
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

        $this->command->info('Creating Module 5 — Introduction to Mathematical Proof (Advanced) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Mathematical Proof — Advanced Implementations',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Implement sophisticated proof verification engines, automated counterexample finders, and induction validators. Each problem encodes a non-trivial mathematical argument — from Euclid\'s proof of infinitely many primes to Cantor\'s diagonalisation idea — into runnable Python that must handle edge cases and large inputs efficiently.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 1200,
                'order_index'        => 5,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // L5.1  Introduction to Mathematical Proof  (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
A **proposition** is a statement that is either true or false.

Write a function `evaluate_proposition(n)` that, given an integer `n`, evaluates and returns the truth value (as `True`/`False`) of ALL five propositions simultaneously:
1. `n > 0`
2. `n % 2 == 0`
3. `n ** 2 > n` (for n > 1)
4. `n == n * 1`
5. `n + (-n) == 0`

Read `n` from input. Print each result on its own line.

Example:
```
Input:  3
Output:
True
False
True
True
True
```
MD,
                'starter_code'        => "def evaluate_proposition(n):\n    pass\n\nn = int(input())\nfor result in evaluate_proposition(n):\n    print(result)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Build a **truth table evaluator** for the logical connectives: AND, OR, NOT, IMPLIES, IFF.

Read two truth values `p` and `q` (as `1`/`0`) from input (one per line). Print a complete truth table row:

```
p AND q: <result>
p OR q: <result>
NOT p: <result>
p IMPLIES q: <result>
p IFF q: <result>
```

Example:
```
Input:
1
0
Output:
p AND q: False
p OR q: True
NOT p: False
p IMPLIES q: False
p IFF q: False
```
MD,
                'starter_code'        => "p = int(input())\nq = int(input())\n# Evaluate all connectives\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
A **tautology** is a proposition that is true for all truth values of its variables.

Write a function `is_tautology(formula_fn, num_vars)` that checks all `2^num_vars` combinations of truth values.

Check whether `(p → q) → ((q → r) → (p → r))` (hypothetical syllogism) is a tautology.

No input needed. Print `Tautology` or `Not a tautology`.

Expected output:
```
Tautology
```
MD,
                'starter_code'        => "from itertools import product\n\ndef is_tautology(formula_fn, num_vars):\n    pass\n\n# formula: (p→q) → ((q→r) → (p→r))\nimpl = lambda a, b: (not a) or b\nformula = lambda p, q, r: impl(impl(p, q), impl(impl(q, r), impl(p, r)))\n# Check tautology\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Two propositions are **logically equivalent** if they have the same truth value for every assignment.

Read a number `n` from input. Verify De Morgan's Law:
`NOT (p AND q) ≡ (NOT p) OR (NOT q)`

Check all 4 combinations of `p`, `q ∈ {True, False}`. Print `Equivalent` if they always agree, otherwise print `Not equivalent` and the first counterexample as `p=<v> q=<v>`.

(It is always equivalent — the interesting part is building the checker.)

Example:
```
Input:  1
Output: Equivalent
```
MD,
                'starter_code'        => "n = int(input())  # unused seed, just triggers the check\n# Verify De Morgan's Law over all p, q\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
A **predicate** `P(x)` becomes a proposition when `x` is fixed.

Write a function `quantifier_check(pred, domain)` that:
- Returns `("forall", True)` if `pred(x)` holds for ALL x in domain
- Returns `("exists", True, witness)` if `pred(x)` holds for SOME x, reporting the first witness
- Returns `("forall", False, counterexample)` otherwise

Read `n` (domain size) from input. Domain = {1, 2, ..., n}. Use `pred(x) = (x**2 >= x)`.

Print the quantifier result. Format: `forall: True` or `exists: True, witness=<w>` or `forall: False, counterexample=<c>`.

Example:
```
Input:  5
Output: forall: True
```
MD,
                'starter_code'        => "def quantifier_check(pred, domain):\n    pass\n\nn = int(input())\ndomain = range(1, n + 1)\npred = lambda x: x**2 >= x\nresult = quantifier_check(pred, domain)\n# Print result\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // L5.2  Direct Proof  (Q6–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
**Direct proof**: if `n` is odd, then `n² - 1` is divisible by 8.

Write a function `verify_direct_proof(n)` that:
1. Checks `n` is odd.
2. Computes `n² - 1`.
3. Checks divisibility by 8.
4. Returns a dict with keys `is_odd`, `value`, `divisible_by_8`, `quotient`.

Read `n` from input. Print each key-value pair on its own line as `key: value`.

Example:
```
Input:  7
Output:
is_odd: True
value: 48
divisible_by_8: True
quotient: 6
```
MD,
                'starter_code'        => "def verify_direct_proof(n):\n    pass\n\nn = int(input())\nresult = verify_direct_proof(n)\nfor k, v in result.items():\n    print(f'{k}: {v}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
**Direct proof**: the product of any two consecutive integers is even.

Write `consecutive_product_proof(n)` that, for each `k = n, n+1, ..., n+4`, computes `k * (k+1)`, verifies it is even, and prints:

`k*(k+1) = <product>, even: <True/False>`

Read `n` from input.

Example:
```
Input:  3
Output:
3*(3+1) = 12, even: True
4*(4+1) = 20, even: True
5*(5+1) = 30, even: True
6*(6+1) = 42, even: True
7*(7+1) = 56, even: True
```
MD,
                'starter_code'        => "n = int(input())\nfor k in range(n, n + 5):\n    # Compute product, check even, print\n    pass\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
**Direct proof by algebra**: if `a ≡ b (mod m)` and `c ≡ d (mod m)`, then `a + c ≡ b + d (mod m)`.

Write `modular_addition_proof(a, b, c, d, m)` that:
1. Verifies `a ≡ b (mod m)` and `c ≡ d (mod m)`.
2. Computes `(a + c) mod m` and `(b + d) mod m`.
3. Verifies they are equal.
4. Prints `premise1: True/False`, `premise2: True/False`, `conclusion: True/False`.

Read `a b c d m` from input (space-separated).

Example:
```
Input:  7 3 11 7 4
Output:
premise1: True
premise2: True
conclusion: True
```
MD,
                'starter_code'        => "a, b, c, d, m = map(int, input().split())\n# Verify modular addition property\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
**Direct proof**: for any integer `n`, `n(n+1)(n+2)` is divisible by 6 (product of 3 consecutive integers).

Write a function `three_consecutive_proof(start, end)` that checks this for all integers in `[start, end]`. Print `All verified` if it holds for every n, otherwise print the first failure as `fails at n=<n>`.

Read `start` and `end` from input (one per line).

Example:
```
Input:
1
100
Output: All verified
```
MD,
                'starter_code'        => "start = int(input())\nend = int(input())\n# Check n*(n+1)*(n+2) % 6 == 0 for all n in [start, end]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
**Direct proof using divisibility chains**: if `a | b` and `b | c`, then `a | c`.

Read `n` triples `(a, b, c)` from input (first line is `n`, then one triple per line space-separated). For each triple, print `True` if the chain holds (verify all three conditions), otherwise print `False` and which condition fails first.

Format: `True` or `False: a|b=<T/F>, b|c=<T/F>`.

Example:
```
Input:
2
2 4 8
3 5 15
Output:
True
False: a|b=False, b|c=True
```
MD,
                'starter_code'        => "n = int(input())\nfor _ in range(n):\n    a, b, c = map(int, input().split())\n    # Check divisibility chain\n    pass\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // L5.3  Proof by Contrapositive  (Q11–Q15)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
The **contrapositive** of `P → Q` is `¬Q → ¬P`. They are logically equivalent.

Write `verify_contrapositive(p_fn, q_fn, domain)` that checks, for every element `x` in domain:
- `p_fn(x) → q_fn(x)` (original)
- `not q_fn(x) → not p_fn(x)` (contrapositive)

Verify both always agree. Use: `P(n) = "n² is even"`, `Q(n) = "n is even"` on domain `[1..n]`.

Read `n` from input. Print `Equivalent` or `Not equivalent at x=<x>`.

Example:
```
Input:  20
Output: Equivalent
```
MD,
                'starter_code'        => "def verify_contrapositive(p_fn, q_fn, domain):\n    pass\n\nn = int(input())\ndomain = range(1, n + 1)\np_fn = lambda x: (x**2) % 2 == 0\nq_fn = lambda x: x % 2 == 0\nprint(verify_contrapositive(p_fn, q_fn, domain))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Use the contrapositive to prove: "if `n²` is odd, then `n` is odd."

Write a function `contrapositive_odd_square(limit)` that checks all integers from 1 to `limit`:
- For each `n`, evaluate the original statement: `n² odd → n odd`.
- Also evaluate the contrapositive: `n even → n² even`.
- Confirm they always match.

Read `limit` from input. Print `Verified for all n in [1, limit]` or `Mismatch at n=<n>`.

Example:
```
Input:  50
Output: Verified for all n in [1, 50]
```
MD,
                'starter_code'        => "limit = int(input())\n# Check both directions for all n in [1, limit]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Write a general **implication equivalence checker** that, given a formula string and its contrapositive string (encoded as lambda-compatible expressions), evaluates them over a finite domain and confirms equivalence.

More concretely: read `n` (domain size), then `k` implication pairs as lines of two space-separated integers (p_val and q_val). For each pair, print whether `p→q` equals `¬q→¬p`. Print `Match` or `Mismatch`.

Example:
```
Input:
3
1 1
1 0
0 1
Output:
Match
Match
Match
```
MD,
                'starter_code'        => "n = int(input())\nfor _ in range(n):\n    p, q = map(int, input().split())\n    original = (not p) or q\n    contra = q or (not p)  # NOT q → NOT p = q OR NOT p ... same\n    # Fix: contrapositive of p→q is ¬q→¬p\n    contra = (not (not q)) or (not p)  # simplifies to q or not p — same\n    # Actually implement correctly\n    pass\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Prove by contrapositive: "if `gcd(a, b) > 1`, then `a` and `b` are not both prime."

Write `gcd_prime_contrapositive(pairs)`: for each `(a, b)`:
- Original: if `gcd(a,b) > 1` → `not (is_prime(a) and is_prime(b))`
- Contrapositive: if `is_prime(a) and is_prime(b)` → `gcd(a,b) == 1`

Read `n` pairs from input (first line is `n`, then one pair per line). Print `holds` or `fails` for each.

Example:
```
Input:
3
4 6
2 3
5 10
Output:
holds
holds
holds
```
MD,
                'starter_code'        => "import math\n\ndef is_prime(n):\n    if n < 2: return False\n    for i in range(2, int(n**0.5) + 1):\n        if n % i == 0: return False\n    return True\n\nn = int(input())\nfor _ in range(n):\n    a, b = map(int, input().split())\n    # Check contrapositive\n    pass\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Build a **contrapositive proof automator** for divisibility statements.

The statement: "if `n` is NOT divisible by 3, then `n²` is NOT divisible by 3."

Contrapositive: "if `n²` is divisible by 3, then `n` is divisible by 3."

Read `limit` from input. Test both statement and contrapositive on all `n` from 1 to `limit`. Count how many times each form was tested (hypothesis true) and confirm all conclusions held.

Print:
```
original tested: <k>
contrapositive tested: <k>
all hold: True
```

Example:
```
Input:  30
Output:
original tested: 20
contrapositive tested: 10
all hold: True
```
MD,
                'starter_code'        => "limit = int(input())\norig_tested = 0\ncontra_tested = 0\nall_hold = True\nfor n in range(1, limit + 1):\n    # Original: NOT div by 3 → n^2 NOT div by 3\n    # Contrapositive: n^2 div by 3 → n div by 3\n    pass\nprint(f'original tested: {orig_tested}')\nprint(f'contrapositive tested: {contra_tested}')\nprint(f'all hold: {all_hold}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // L5.4  Proof by Contradiction  (Q16–Q20)
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Simulate **proof by contradiction** for: "there is no largest even integer."

Write `no_largest_even(candidate)`: given a proposed "largest even" candidate, show it leads to contradiction by finding a larger even integer.

Read `candidate` from input. Print:
```
candidate: <n>
larger even found: <n+2>
contradiction: True
```

Example:
```
Input:  100
Output:
candidate: 100
larger even found: 102
contradiction: True
```
MD,
                'starter_code'        => "candidate = int(input())\n# Find a larger even integer and show contradiction\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Simulate the **proof that √2 is irrational** computationally.

Assume `√2 = p/q` in lowest terms. Write `sqrt2_irrationality_check(limit)` that searches all pairs `(p, q)` with `1 ≤ q ≤ p ≤ limit` and `gcd(p, q) = 1` to see if `p² = 2 * q²` ever holds exactly.

Read `limit` from input. Print `No rational found (consistent with irrationality)` or `Found: <p>/<q>` if one exists (it won't).

Example:
```
Input:  1000
Output: No rational found (consistent with irrationality)
```
MD,
                'starter_code'        => "import math\n\nlimit = int(input())\n# Search all (p, q) with gcd=1 and p^2 == 2*q^2\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
**Proof by contradiction**: there are infinitely many primes (Euclid's proof).

Simulate Euclid's construction: given a finite list of primes `[p1, p2, ..., pk]`, compute `N = p1*p2*...*pk + 1`. Show that `N` has a prime factor not in the list.

Read `k` primes from input (first line is `k`, then one prime per line). Print:
```
N: <value>
new prime factor: <p>
in original list: False
```

Example:
```
Input:
3
2
3
5
Output:
N: 31
new prime factor: 31
in original list: False
```
MD,
                'starter_code'        => "def smallest_prime_factor(n):\n    for i in range(2, int(n**0.5) + 1):\n        if n % i == 0:\n            return i\n    return n\n\nk = int(input())\nprimes = [int(input()) for _ in range(k)]\n# Compute N and find new prime factor\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
**Proof by contradiction** for unique prime factorisation (partial):

Given an integer `n`, compute its prime factorisation. Then verify that no two DIFFERENT factorisations exist by checking that the canonical form is unique.

Write `unique_factorisation(n)` that returns the sorted list of prime factors (with multiplicity).

Read `n` from input. Print the factorisation as space-separated integers, then print `Unique: True`.

Example:
```
Input:  360
Output:
2 2 2 3 3 5
Unique: True
```
MD,
                'starter_code'        => "def prime_factors(n):\n    factors = []\n    d = 2\n    while d * d <= n:\n        while n % d == 0:\n            factors.append(d)\n            n //= d\n        d += 1\n    if n > 1:\n        factors.append(n)\n    return factors\n\nn = int(input())\n# Print factors and uniqueness\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
**Proof by contradiction** for: "if `n²` is even, then `n` is even."

Assume `n` is odd and `n²` is even. Show this leads to contradiction:
- If `n = 2k + 1`, then `n² = 4k² + 4k + 1 = 2(2k²+2k) + 1`, which is ODD — contradiction!

Write `contradiction_even_square(start, end)`: for each ODD `n` in `[start, end]`, verify `n² is odd`. Count contradictions found (should be 0).

Read `start end` (space-separated) from input.

Print: `Odd n tested: <count>`, `n² odd confirmed: <count>`, `Contradiction with assumption: 0 found`.

Example:
```
Input:  1 20
Output:
Odd n tested: 10
n² odd confirmed: 10
Contradiction with assumption: 0 found
```
MD,
                'starter_code'        => "start, end = map(int, input().split())\ntested = 0\nconfirmed = 0\nfor n in range(start, end + 1):\n    if n % 2 == 1:\n        # n is odd\n        pass\nprint(f'Odd n tested: {tested}')\nprint(f'n\\u00b2 odd confirmed: {confirmed}')\nprint(f'Contradiction with assumption: 0 found')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // L5.5  Proof by Cases (Exhaustion)  (Q21–Q25)
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Prove by cases that `n² mod 4 ∈ {0, 1}` for all integers `n`.

Write `proof_by_cases_square_mod4(limit)`: for each `n` from `-limit` to `limit`, compute `n² mod 4` and record which residues appear. Print each residue found (sorted) and confirm the set equals `{0, 1}`.

Read `limit` from input.

Print:
```
residues found: {0, 1}
only 0 and 1: True
```

Example:
```
Input:  100
Output:
residues found: {0, 1}
only 0 and 1: True
```
MD,
                'starter_code'        => "limit = int(input())\nresidues = set()\nfor n in range(-limit, limit + 1):\n    residues.add((n**2) % 4)\nprint(f'residues found: {sorted(residues)}')\nprint(f'only 0 and 1: {residues == {0, 1}}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Prove by exhaustion: every integer is in exactly one of four residue classes mod 4 (0, 1, 2, 3).

Write `exhaustive_residue_classes(limit)` that:
1. Partitions integers from 0 to `limit` into 4 classes by `n mod 4`.
2. Verifies each class is non-empty.
3. Verifies the classes are disjoint (no number in two classes).
4. Verifies the union equals {0, 1, ..., limit}.

Read `limit` from input. Print `Partition valid: True` or describe the failure.

Example:
```
Input:  19
Output: Partition valid: True
```
MD,
                'starter_code'        => "limit = int(input())\nclasses = {r: [] for r in range(4)}\nfor n in range(limit + 1):\n    classes[n % 4].append(n)\n# Verify partition properties\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
**Case exhaustion**: prove that for any integer `n`, exactly one of `n`, `n+1` is even.

Write `exactly_one_even(start, end)` that checks every integer `n` in `[start, end]`. For each `n`, verify that exactly one of `{n, n+1}` is even. Print `All verified` or `Fails at n=<n>`.

Read `start` and `end` (space-separated) from input.

Example:
```
Input:  -50 50
Output: All verified
```
MD,
                'starter_code'        => "start, end = map(int, input().split())\n# Check for each n in [start, end]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Prove by cases: the square of any integer ends in digit 0, 1, 4, 5, 6, or 9 (never 2, 3, 7, or 8).

Write `last_digit_square_proof(limit)` that:
1. Computes `n²` for `n = 0..9` (all residues mod 10).
2. Collects the set of last digits of squares.
3. Verifies it equals `{0, 1, 4, 5, 6, 9}`.
4. Also verifies none of `{2, 3, 7, 8}` appears.

Read `limit` from input (verify up to `limit` as well). Print:
```
valid last digits: {0, 1, 4, 5, 6, 9}
invalid digits absent: True
```

Example:
```
Input:  1000
Output:
valid last digits: {0, 1, 4, 5, 6, 9}
invalid digits absent: True
```
MD,
                'starter_code'        => "limit = int(input())\n# Check last digits of squares\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
**Case-based divisibility proof**: for any `n`, exactly one of `n`, `n+2`, `n+4` is divisible by 3.

Write `divisible_by_3_in_triple(limit)` that checks this for all `n` from 1 to `limit`. For each `n`, count how many of `{n, n+2, n+4}` are divisible by 3. Print `All verified` if it's always exactly 1, else print the first failure.

Read `limit` from input.

Example:
```
Input:  100
Output: All verified
```
MD,
                'starter_code'        => "limit = int(input())\n# For each n in [1, limit], check {n, n+2, n+4}\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // L5.6  Mathematical Induction  (Q26–Q30)
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Verify the **induction structure** for: `Σ_{k=1}^{n} k = n(n+1)/2`.

Write `induction_sum_verifier(n)` that:
1. Checks the base case (`n=1`).
2. For each step from 1 to `n`, verifies the inductive step: if the formula holds for `k`, it holds for `k+1`.
3. Reports whether all steps passed.

Read `n` from input. Print:
```
base case: True
inductive steps verified: <n>
all hold: True
```

Example:
```
Input:  50
Output:
base case: True
inductive steps verified: 50
all hold: True
```
MD,
                'starter_code'        => "n = int(input())\n# Verify base case and each inductive step\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Verify by induction: `Σ_{k=1}^{n} k² = n(n+1)(2n+1)/6`.

Write `sum_of_squares_induction(n)`:
1. Compute the LHS (actual sum) and RHS (formula) for all `k` from 1 to `n`.
2. Report the first discrepancy or `All match`.
3. Also print `LHS at n=<n>: <value>` and `RHS at n=<n>: <value>`.

Read `n` from input.

Example:
```
Input:  20
Output:
LHS at n=20: 2870
RHS at n=20: 2870
All match
```
MD,
                'starter_code'        => "n = int(input())\n# Compute LHS and RHS for k=1..n\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Verify by induction: `2^n > n` for all `n ≥ 1`.

Write `power_vs_linear_induction(limit)`:
1. Base case: n=1, 2^1=2 > 1. ✓
2. Inductive step: if `2^k > k`, show `2^(k+1) > k+1` (since `2^(k+1) = 2*2^k > 2k ≥ k+1` for k≥1).
3. Verify computationally for all `n` in `[1, limit]`.

Read `limit` from input. Print each `n: 2^n=<v> > n: True` then `All verified`.

Example:
```
Input:  5
Output:
1: 2^1=2 > 1: True
2: 2^2=4 > 2: True
3: 2^3=8 > 3: True
4: 2^4=16 > 4: True
5: 2^5=32 > 5: True
All verified
```
MD,
                'starter_code'        => "limit = int(input())\n# Verify 2^n > n for each n in [1, limit]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Verify by induction: `n! > 2^n` for all `n ≥ 4`.

Write `factorial_vs_power_induction(limit)`:
1. Check the base case at `n=4`.
2. For each `n` from 4 to `limit`, verify `n! > 2^n`.
3. Print each result as `n=<n>: <n!> > <2^n>: True/False`.
4. Print `All verified` or `Fails at n=<n>`.

Read `limit` from input.

Example:
```
Input:  8
Output:
n=4: 24 > 16: True
n=5: 120 > 32: True
n=6: 720 > 64: True
n=7: 5040 > 128: True
n=8: 40320 > 256: True
All verified
```
MD,
                'starter_code'        => "import math\nlimit = int(input())\n# Verify n! > 2^n for each n in [4, limit]\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Implement a general **induction verifier framework**.

Write `induction_verify(base, base_val, formula_fn, inductive_fn, limit)`:
- `base`: the starting integer
- `base_val`: expected value at base
- `formula_fn(n)`: closed-form formula
- `inductive_fn(prev, n)`: computes next value from previous
- Verifies base case and all inductive steps up to `limit`

Apply it to geometric series: `Σ_{k=0}^{n} 2^k = 2^(n+1) - 1`.

Read `limit` from input. Print `base case ok`, `steps verified: <n>`, `all correct: True`.

Example:
```
Input:  30
Output:
base case ok
steps verified: 30
all correct: True
```
MD,
                'starter_code'        => "def induction_verify(base, base_val, formula_fn, inductive_fn, limit):\n    pass\n\nlimit = int(input())\nformula_fn = lambda n: 2**(n+1) - 1\nbase_val = 1  # sum when n=0: 2^0 = 1\ninductive_fn = lambda prev, n: prev + 2**n\nresult = induction_verify(0, base_val, formula_fn, inductive_fn, limit)\n# Print results\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // L5.7  Strong Induction  (Q31–Q35)
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
**Strong induction** allows assuming the statement holds for ALL `k < n` (not just `k = n-1`).

Prove: every integer `n ≥ 2` has a prime factor.

Write `prime_factor_strong_induction(limit)`:
- Base: `n=2` is prime (its own prime factor).
- Inductive step: if `n` is prime, done. Else `n = a*b`, and by strong IH, `a` has a prime factor, which divides `n`.
- Verify by finding a prime factor for every `n` in `[2, limit]` using this recursive logic.

Read `limit` from input. Print `All n in [2, limit] have a prime factor: True`.

Example:
```
Input:  100
Output: All n in [2, 100] have a prime factor: True
```
MD,
                'starter_code'        => "def smallest_prime_factor(n):\n    for i in range(2, int(n**0.5) + 1):\n        if n % i == 0:\n            return i\n    return n\n\nlimit = int(input())\n# Verify every n in [2, limit] has a prime factor\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Use **strong induction** to prove the Fibonacci sequence grows at most exponentially.

Claim: `F(n) ≤ 2^n` for all `n ≥ 0`.

Write `fibonacci_strong_induction(limit)`:
1. Compute `F(n)` for all `n` from 0 to `limit`.
2. Verify `F(n) ≤ 2^n` at every step.
3. For each step, print `F(<n>) = <val> ≤ 2^<n> = <bound>: True/False`.
4. End with `All hold: True`.

Read `limit` from input.

Example:
```
Input:  8
Output:
F(0) = 0 ≤ 2^0 = 1: True
F(1) = 1 ≤ 2^1 = 2: True
F(2) = 1 ≤ 2^2 = 4: True
F(3) = 2 ≤ 2^3 = 8: True
F(4) = 3 ≤ 2^4 = 16: True
F(5) = 5 ≤ 2^5 = 32: True
F(6) = 8 ≤ 2^6 = 64: True
F(7) = 13 ≤ 2^7 = 128: True
F(8) = 21 ≤ 2^8 = 256: True
All hold: True
```
MD,
                'starter_code'        => "limit = int(input())\nfib = [0, 1]\nfor i in range(2, limit + 1):\n    fib.append(fib[-1] + fib[-2])\n# Verify F(n) <= 2^n and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
**Strong induction** for the Chicken McNugget theorem (partial):

Every integer `n ≥ 6` (that is NOT prime) can be expressed as a sum of 2s and 3s.

Write `sum_of_2s_and_3s(limit)`: for each `n` from 6 to `limit`, find a decomposition `n = 2a + 3b` with `a, b ≥ 0` using strong induction logic (try subtracting 2 or 3 and recursing). Print `n: 2*<a> + 3*<b>` for each.

Read `limit` from input.

Example:
```
Input:  10
Output:
6: 2*3 + 3*0
7: 2*2 + 3*1
8: 2*4 + 3*0
9: 2*0 + 3*3
10: 2*5 + 3*0
```
MD,
                'starter_code'        => "def decompose(n):\n    # Try all b from 0 up\n    for b in range(n // 3 + 1):\n        rem = n - 3 * b\n        if rem >= 0 and rem % 2 == 0:\n            return rem // 2, b\n    return None\n\nlimit = int(input())\nfor n in range(6, limit + 1):\n    a, b = decompose(n)\n    print(f'{n}: 2*{a} + 3*{b}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Verify strong induction for the recurrence `T(n) = T(⌊n/2⌋) + T(⌊n/3⌋) + n` with `T(0) = T(1) = 1`.

Claim: `T(n) ≤ 10n` for all `n ≥ 1`.

Read `limit` from input. Compute `T(n)` bottom-up. For each `n` from 1 to `limit`, print `T(<n>) = <val> ≤ 10*<n> = <bound>: True/False`. End with `All hold: True` or `Fails at n=<n>`.

Example:
```
Input:  10
Output:
T(1) = 1 ≤ 10*1 = 10: True
T(2) = 4 ≤ 10*2 = 20: True
...
All hold: True
```
MD,
                'starter_code'        => "limit = int(input())\nT = [0] * (limit + 1)\nT[0] = 1\nif limit >= 1:\n    T[1] = 1\nfor n in range(2, limit + 1):\n    T[n] = T[n // 2] + T[n // 3] + n\nall_hold = True\nfor n in range(1, limit + 1):\n    holds = T[n] <= 10 * n\n    print(f'T({n}) = {T[n]} \\u2264 10*{n} = {10*n}: {holds}')\n    if not holds:\n        all_hold = False\n        print(f'Fails at n={n}')\n        break\nif all_hold:\n    print('All hold: True')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
**Strong induction** for the Fundamental Theorem of Arithmetic (constructive part):

Every integer `n ≥ 2` can be written as a product of primes.

Write a recursive function `prime_factorisation_strong_induction(n)`:
- If `n` is prime, return `[n]`.
- Else find a factor `2 ≤ f < n`, then recurse on both `f` and `n//f` (strong IH applies since both are smaller).
- Return the combined sorted list.

Read `n` from input. Print the factorisation (space-separated) and `Verified: True`.

Example:
```
Input:  84
Output:
2 2 3 7
Verified: True
```
MD,
                'starter_code'        => "def is_prime(n):\n    if n < 2: return False\n    for i in range(2, int(n**0.5) + 1):\n        if n % i == 0: return False\n    return True\n\ndef prime_factorisation_strong_induction(n):\n    pass\n\nn = int(input())\nfactors = prime_factorisation_strong_induction(n)\nprint(' '.join(map(str, factors)))\nprint('Verified: True')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // L5.8  Existence and Uniqueness Proofs  (Q36–Q40)
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
**Existence proof** (constructive): prove there exists an integer `n` such that `n² - n - 1 = 0` has a solution in the reals by finding an approximation.

Use binary search on `[1, 2]` to find `φ ≈ (1+√5)/2 ≈ 1.618` (the golden ratio), the positive root of `x² - x - 1 = 0`.

Read `tol` (tolerance) from input. Run binary search until `|f(mid)| < tol`. Print `Exists: True` and the approximation rounded to 8 decimal places.

Example:
```
Input:  0.000001
Output:
Exists: True
approx: 1.61803399
```
MD,
                'starter_code'        => "tol = float(input())\nf = lambda x: x**2 - x - 1\na, b = 1.0, 2.0\n# Binary search for root\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
**Uniqueness proof** for the division algorithm: given integers `a` and `b > 0`, there exist UNIQUE `q` and `r` such that `a = bq + r` and `0 ≤ r < b`.

Write `division_algorithm_unique(a, b)`:
1. Compute `q, r` using Python's `divmod`.
2. Verify `a == b*q + r` and `0 ≤ r < b`.
3. Attempt to find a second pair `(q2, r2)` with different values — show it's impossible by exhaustive small search.

Read `a` and `b` from input (one per line). Print:
```
q: <q>
r: <r>
verified: True
unique: True
```

Example:
```
Input:
17
5
Output:
q: 3
r: 2
verified: True
unique: True
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Compute and verify uniqueness\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
**Existence and uniqueness** of GCD: for any `a, b` (not both 0), there exists a unique positive integer `d = gcd(a, b)` such that `d | a`, `d | b`, and any common divisor of `a` and `b` also divides `d`.

Write `gcd_existence_uniqueness(a, b)` that:
1. Computes `d = gcd(a, b)`.
2. Finds ALL common divisors of `a` and `b` up to `min(|a|, |b|)`.
3. Verifies every common divisor divides `d`.
4. Prints `gcd: <d>`, `all common divisors: [...]`, `all divide gcd: True`.

Read `a` and `b` from input (one per line).

Example:
```
Input:
24
36
Output:
gcd: 12
all common divisors: [1, 2, 3, 4, 6, 12]
all divide gcd: True
```
MD,
                'starter_code'        => "import math\n\na = int(input())\nb = int(input())\n# Compute gcd and verify uniqueness property\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
**Non-constructive existence proof** via the pigeonhole principle:

Among any `n+1` integers chosen from `{1, 2, ..., 2n}`, at least two must share the same ratio of the form `odd * 2^k` (i.e., they are in the same "odd part" class).

Simulate: read `n` then a list of `n+1` integers from `{1..2n}`. Group them by their odd part `(num // 2^k where num/2^k is odd)`. Print the first pair sharing a group.

Format: `pair found: <a> <b> (odd part: <d>)` or `no pair (unexpected)`.

Read `n` then `n+1` space-separated integers from input (two lines).

Example:
```
Input:
4
1 2 3 4 5
Output: pair found: 1 2 (odd part: 1)
```
MD,
                'starter_code'        => "def odd_part(n):\n    while n % 2 == 0:\n        n //= 2\n    return n\n\nn = int(input())\nnums = list(map(int, input().split()))\n# Group by odd part and find collision\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
**Uniqueness of multiplicative inverse mod p** (where p is prime):

For any `a` with `1 ≤ a < p`, there exists a unique `b` with `1 ≤ b < p` such that `ab ≡ 1 (mod p)`.

Write `mod_inverse_unique(p)` that:
1. For each `a` in `[1, p-1]`, finds its unique inverse `b`.
2. Verifies uniqueness: no two distinct `b1, b2` satisfy `ab ≡ 1 (mod p)`.
3. Prints each `a: inverse=<b>`.

Read `p` (prime) from input. Verify and print all inverses, then `All unique: True`.

Example:
```
Input:  7
Output:
1: inverse=1
2: inverse=4
3: inverse=5
4: inverse=2
5: inverse=3
6: inverse=6
All unique: True
```
MD,
                'starter_code'        => "p = int(input())\n# Find and verify unique inverses mod p\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // L5.9  Disproving Statements: Counterexamples  (Q41–Q45)
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Disprove: "all primes are odd."

Write `find_even_prime(limit)` that searches `[2, limit]` for an even prime. Print the counterexample and an explanation, or `none found` if limit < 2.

Format: `counterexample: <n> (even and prime)`.

Read `limit` from input.

Example:
```
Input:  10
Output: counterexample: 2 (even and prime)
```
MD,
                'starter_code'        => "def is_prime(n):\n    if n < 2: return False\n    for i in range(2, int(n**0.5) + 1):\n        if n % i == 0: return False\n    return True\n\nlimit = int(input())\n# Find even prime\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Disprove: "if `p` and `q` are prime, then `p + q` is prime."

Write `disprove_prime_sum(limit)` that finds the first counterexample `(p, q)` where both `p, q ≤ limit` are prime but `p + q` is NOT prime. Print the counterexample.

Format: `counterexample: <p> + <q> = <sum> (not prime)`.

Read `limit` from input.

Example:
```
Input:  10
Output: counterexample: 2 + 3 = 5 (not prime)
```
(Wait — 5 IS prime. The first real counterexample is `3 + 5 = 8`.)
MD,
                'starter_code'        => "def is_prime(n):\n    if n < 2: return False\n    for i in range(2, int(n**0.5) + 1):\n        if n % i == 0: return False\n    return True\n\nlimit = int(input())\n# Find first p, q prime where p+q is not prime\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Disprove: "for all integers `n ≥ 0`, `2^(2^n) + 1` is prime" (Fermat numbers).

Write `fermat_counterexample(limit_n)`: for `n = 0, 1, 2, ...` up to `limit_n`, compute `F_n = 2^(2^n) + 1` and check primality. Print `prime` or `composite (factor=<f>)` for each.

Read `limit_n` from input. (F5 = 2^32 + 1 = 4294967297 = 641 × 6700417 — the first composite Fermat number, but skip if too slow; use limit_n ≤ 5.)

Example:
```
Input:  4
Output:
F0 = 3: prime
F1 = 5: prime
F2 = 17: prime
F3 = 257: prime
F4 = 65537: prime
```
MD,
                'starter_code'        => "def smallest_factor(n):\n    if n < 2: return None\n    for i in range(2, min(int(n**0.5) + 1, 10**6)):\n        if n % i == 0: return i\n    return None  # treat as prime for large n\n\nlimit_n = int(input())\nfor n in range(limit_n + 1):\n    F = 2**(2**n) + 1\n    f = smallest_factor(F)\n    if f:\n        print(f'F{n} = {F}: composite (factor={f})')\n    else:\n        print(f'F{n} = {F}: prime')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Disprove: "if `a² = b²`, then `a = b`."

Write `disprove_square_equality(limit)` that finds all pairs `(a, b)` in `[-limit, limit]` where `a² = b²` but `a ≠ b`. Print the first such pair.

Format: `counterexample: a=<a>, b=<b>, a²=b²=<val>`.

Read `limit` from input.

Example:
```
Input:  5
Output: counterexample: a=-1, b=1, a²=b²=1
```
MD,
                'starter_code'        => "limit = int(input())\n# Find first (a, b) where a^2 == b^2 but a != b\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Disprove: "the sum of two irrational numbers is always irrational."

Represent irrationals as `sqrt(n)` for non-square `n`. Find a pair `(sqrt(a), sqrt(b))` where both are irrational but `sqrt(a) + sqrt(b)` is rational (i.e., a perfect integer).

Write `irrational_sum_counterexample(limit)`: search `a, b ≤ limit`, both non-perfect-squares. Print the counterexample.

Format: `counterexample: sqrt(<a>) + sqrt(<b>) = <rational>`.

Read `limit` from input.

Example:
```
Input:  10
Output: counterexample: sqrt(2) + sqrt(2) = 2.8284...
```
(Note: `√2 + (-√2) = 0` is the classic one, but since we use positive sqrt, look for `√a + √b = integer`, e.g., `√2 + √8 = 3√2` — not rational. The simplest is `√n + (-√n)`; since we only do positive, print a note.)

Hint: the expected answer is `sqrt(2) + sqrt(2) is irrational`; show any case where both are irrational but sum equals a known value. If no integer sum found, print `no integer sum found in range`.
MD,
                'starter_code'        => "import math\n\ndef is_perfect_square(n):\n    r = int(math.isqrt(n))\n    return r * r == n\n\nlimit = int(input())\n# Search for sqrt(a) + sqrt(b) = integer\nfound = False\nfor a in range(2, limit + 1):\n    for b in range(2, limit + 1):\n        if not is_perfect_square(a) and not is_perfect_square(b):\n            s = math.sqrt(a) + math.sqrt(b)\n            if abs(s - round(s)) < 1e-9:\n                print(f'counterexample: sqrt({a}) + sqrt({b}) = {round(s)}')\n                found = True\n                break\n    if found:\n        break\nif not found:\n    print('no integer sum found in range')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // L5.10  Choosing the Right Proof Strategy  (Q46–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Build a **proof strategy classifier**: given a mathematical statement encoded as a lambda, classify which strategy is most appropriate.

For each of the following, determine: `direct`, `contrapositive`, `contradiction`, `induction`, or `cases`.

Read `n` integers from input (first line is `n`, then one per line). For each integer `k`, apply the rules:
- If `k % 2 == 0` and `k > 0`: classify as `direct` (even numbers have straightforward proofs)
- If `k < 0`: classify as `contradiction` (negative numbers often need contradiction proofs)
- If `k % 3 == 0` and `k > 0`: classify as `cases`
- If `k > 100`: classify as `induction`
- Otherwise: classify as `contrapositive`

Print `<k>: <strategy>` for each.

Example:
```
Input:
4
4
-2
9
200
Output:
4: direct
-2: contradiction
9: cases
200: induction
```
MD,
                'starter_code'        => "n = int(input())\nfor _ in range(n):\n    k = int(input())\n    # Classify strategy\n    pass\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
**Strategy selection**: determine the best proof technique for a given statement type.

Read `n` statement descriptions (one per line). Each is one of:
- `"forall n, f(n) holds"` → `induction`
- `"there exists n such that"` → `existence`
- `"if P then Q"` → `direct or contrapositive`
- `"P and not P"` → `contradiction`
- `"cases: n mod k"` → `cases`

Print the recommended strategy for each.

Read `n` then `n` lines from input.

Example:
```
Input:
3
forall n, f(n) holds
there exists n such that
P and not P
Output:
induction
existence
contradiction
```
MD,
                'starter_code'        => "n = int(input())\nfor _ in range(n):\n    stmt = input().strip()\n    if 'forall' in stmt:\n        print('induction')\n    elif 'there exists' in stmt:\n        print('existence')\n    elif 'P and not P' in stmt:\n        print('contradiction')\n    elif 'cases' in stmt:\n        print('cases')\n    else:\n        print('direct or contrapositive')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
**Full proof pipeline**: apply all five proof strategies to five mini-theorems about a given integer `n`.

Read `n` from input. For each theorem, execute the appropriate strategy and print `Theorem <k>: True` (or the result):

1. **Direct**: `n(n+1)` is even.
2. **Contrapositive**: if `n²` is odd, then `n` is odd.
3. **Contradiction**: `n + 1 > n` (assume not, derive contradiction).
4. **Cases**: `n mod 3 ∈ {0, 1, 2}`.
5. **Induction witness**: `Σ_{k=1}^{n} k = n(n+1)/2`.

Example:
```
Input:  7
Output:
Theorem 1: True
Theorem 2: True
Theorem 3: True
Theorem 4: True
Theorem 5: True
```
MD,
                'starter_code'        => "n = int(input())\n# Theorem 1: direct\n# Theorem 2: contrapositive\n# Theorem 3: contradiction\n# Theorem 4: cases\n# Theorem 5: induction\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
**Proof complexity estimator**: for a given claim about integers up to `n`, estimate which proof strategy minimises the number of cases to check.

Given `n`, compute:
- **Exhaustion cost**: `n` (check all values directly)
- **Induction cost**: `log2(n)` rounded up (steps in induction)
- **Contradiction cost**: `1` (find a single contradiction)
- **Direct cost**: `1` (apply algebra once)

Read `n` from input. Print each strategy with its cost, sorted by cost ascending.

Format: `<strategy>: cost=<c>` (use integer costs).

Example:
```
Input:  64
Output:
contradiction: cost=1
direct: cost=1
induction: cost=6
exhaustion: cost=64
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\ncosts = {\n    'exhaustion': n,\n    'induction': math.ceil(math.log2(n)) if n > 1 else 1,\n    'contradiction': 1,\n    'direct': 1,\n}\n# Sort and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**Grand proof challenge**: implement a single function `auto_prove(statement_type, n)` that selects and executes the right proof strategy.

Read `statement_type` (a string) and `n` (an integer) from input (one per line).

| statement_type | What to verify |
|---|---|
| `direct` | `n * (n+1)` is even |
| `contrapositive` | `n² odd → n odd` (for this n) |
| `contradiction` | `√2` is not `a/b` for any `a/b` with `b ≤ n` |
| `induction` | `Σ_{k=1}^{n} k == n*(n+1)//2` |
| `cases` | `n mod 4 ∈ {0,1,2,3}` |

Print `strategy: <type>` then `result: True`.

Example:
```
Input:
induction
10
Output:
strategy: induction
result: True
```
MD,
                'starter_code'        => "import math\n\nstatement_type = input().strip()\nn = int(input())\n\nprint(f'strategy: {statement_type}')\n# Execute the appropriate proof strategy\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],
        ];

        // ─────────────────────────────────────────────────────────────────
        // Insert questions
        // ─────────────────────────────────────────────────────────────────

        $questionIds = [];

        foreach ($questionDefs as $def) {
            $row = DB::table('coding_questions')->where([
                'challenge_id' => $challenge->id,
                'order_index'  => $def['order_index'],
            ])->first();

            if (! $row) {
                $id = DB::table('coding_questions')->insertGetId(array_merge(
                    ['challenge_id' => $challenge->id, 'language' => 'python'],
                    $def,
                    ['created_at' => now(), 'updated_at' => now()]
                ));
            } else {
                $id = $row->id;
            }

            $questionIds[$def['order_index']] = $id;
        }

        // ─────────────────────────────────────────────────────────────────
        // 3. TEST CASES  (4 per question: 2 visible + 2 hidden)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $now = now()->toDateTimeString();

        $seed = function (int $ord, array $cases) use ($questionIds, $now): void {
            $qid = $questionIds[$ord] ?? null;
            if (! $qid) return;
            if (DB::table('test_cases')->where('coding_question_id', $qid)->exists()) {
                $this->command->warn("  test_cases for Q{$ord} already exist — skipping.");
                return;
            }
            $rows = array_map(fn ($c) => array_merge(
                ['coding_question_id' => $qid, 'created_at' => $now, 'updated_at' => $now],
                $c
            ), $cases);
            DB::table('test_cases')->insert($rows);
        };

        // ── L5.1 ──────────────────────────────────────────────────────────
        $seed(1, [
            ['input' => '3',   'expected_output' => "True\nFalse\nTrue\nTrue\nTrue",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',   'expected_output' => "True\nTrue\nTrue\nTrue\nTrue",   'is_hidden' => false, 'order_index' => 2],
            ['input' => '-2',  'expected_output' => "False\nTrue\nTrue\nTrue\nTrue",  'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',   'expected_output' => "True\nFalse\nFalse\nTrue\nTrue", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(2, [
            ['input' => "1\n0",  'expected_output' => "p AND q: False\np OR q: True\nNOT p: False\np IMPLIES q: False\np IFF q: False",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1",  'expected_output' => "p AND q: True\np OR q: True\nNOT p: False\np IMPLIES q: True\np IFF q: True",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0",  'expected_output' => "p AND q: False\np OR q: False\nNOT p: True\np IMPLIES q: True\np IFF q: True",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n1",  'expected_output' => "p AND q: False\np OR q: True\nNOT p: True\np IMPLIES q: True\np IFF q: False",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(3, [
            ['input' => '1',  'expected_output' => 'Tautology', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',  'expected_output' => 'Tautology', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '3',  'expected_output' => 'Tautology', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '0',  'expected_output' => 'Tautology', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(4, [
            ['input' => '1',  'expected_output' => 'Equivalent', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',  'expected_output' => 'Equivalent', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',  'expected_output' => 'Equivalent', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '0',  'expected_output' => 'Equivalent', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(5, [
            ['input' => '5',   'expected_output' => 'forall: True', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',  'expected_output' => 'forall: True', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '1',   'expected_output' => 'forall: True', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '100', 'expected_output' => 'forall: True', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── L5.2 ──────────────────────────────────────────────────────────
        $seed(6, [
            ['input' => '7',   'expected_output' => "is_odd: True\nvalue: 48\ndivisible_by_8: True\nquotient: 6",   'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',   'expected_output' => "is_odd: True\nvalue: 8\ndivisible_by_8: True\nquotient: 1",    'is_hidden' => false, 'order_index' => 2],
            ['input' => '11',  'expected_output' => "is_odd: True\nvalue: 120\ndivisible_by_8: True\nquotient: 15", 'is_hidden' => true,  'order_index' => 3],
            ['input' => '15',  'expected_output' => "is_odd: True\nvalue: 224\ndivisible_by_8: True\nquotient: 28", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(7, [
            ['input' => '3',  'expected_output' => "3*(3+1) = 12, even: True\n4*(4+1) = 20, even: True\n5*(5+1) = 30, even: True\n6*(6+1) = 42, even: True\n7*(7+1) = 56, even: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',  'expected_output' => "1*(1+1) = 2, even: True\n2*(2+1) = 6, even: True\n3*(3+1) = 12, even: True\n4*(4+1) = 20, even: True\n5*(5+1) = 30, even: True",    'is_hidden' => false, 'order_index' => 2],
            ['input' => '10', 'expected_output' => "10*(10+1) = 110, even: True\n11*(11+1) = 132, even: True\n12*(12+1) = 156, even: True\n13*(13+1) = 182, even: True\n14*(14+1) = 210, even: True", 'is_hidden' => true, 'order_index' => 3],
            ['input' => '0',  'expected_output' => "0*(0+1) = 0, even: True\n1*(1+1) = 2, even: True\n2*(2+1) = 6, even: True\n3*(3+1) = 12, even: True\n4*(4+1) = 20, even: True",    'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(8, [
            ['input' => '7 3 11 7 4',   'expected_output' => "premise1: True\npremise2: True\nconclusion: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '5 1 9 5 4',    'expected_output' => "premise1: True\npremise2: True\nconclusion: True",    'is_hidden' => false, 'order_index' => 2],
            ['input' => '3 1 7 5 2',    'expected_output' => "premise1: True\npremise2: False\nconclusion: False",  'is_hidden' => true,  'order_index' => 3],
            ['input' => '10 4 15 9 6',  'expected_output' => "premise1: True\npremise2: True\nconclusion: True",   'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(9, [
            ['input' => "1\n100",    'expected_output' => 'All verified', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "-50\n50",   'expected_output' => 'All verified', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n200",    'expected_output' => 'All verified', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "-100\n100", 'expected_output' => 'All verified', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(10, [
            ['input' => "2\n2 4 8\n3 5 15",   'expected_output' => "True\nFalse: a|b=False, b|c=True",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2 6 12\n4 8 16",  'expected_output' => "True\nTrue",                         'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5 10 20",          'expected_output' => 'True',                              'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n6 9 18",           'expected_output' => 'False: a|b=False, b|c=True',        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── L5.3 ──────────────────────────────────────────────────────────
        $seed(11, [
            ['input' => '20',   'expected_output' => 'Equivalent', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '50',   'expected_output' => 'Equivalent', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '100',  'expected_output' => 'Equivalent', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '10',   'expected_output' => 'Equivalent', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(12, [
            ['input' => '50',   'expected_output' => 'Verified for all n in [1, 50]',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '100',  'expected_output' => 'Verified for all n in [1, 100]',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '30',   'expected_output' => 'Verified for all n in [1, 30]',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '200',  'expected_output' => 'Verified for all n in [1, 200]',  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(13, [
            ['input' => "3\n1 1\n1 0\n0 1",    'expected_output' => "Match\nMatch\nMatch",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0 0",               'expected_output' => 'Match',               'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1\n1 0\n0 0\n0 1",'expected_output' => "Match\nMatch\nMatch\nMatch", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n1 1\n0 0",          'expected_output' => "Match\nMatch",        'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(14, [
            ['input' => "3\n4 6\n2 3\n5 10",   'expected_output' => "holds\nholds\nholds", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n6 10\n7 11",        'expected_output' => "holds\nholds",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2 5",               'expected_output' => 'holds',              'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n3 9\n5 7\n11 13",   'expected_output' => "holds\nholds\nholds",'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(15, [
            ['input' => '30',   'expected_output' => "original tested: 20\ncontrapositive tested: 10\nall hold: True", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '9',    'expected_output' => "original tested: 6\ncontrapositive tested: 3\nall hold: True",  'is_hidden' => false, 'order_index' => 2],
            ['input' => '60',   'expected_output' => "original tested: 40\ncontrapositive tested: 20\nall hold: True",'is_hidden' => true,  'order_index' => 3],
            ['input' => '15',   'expected_output' => "original tested: 10\ncontrapositive tested: 5\nall hold: True", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── L5.4 ──────────────────────────────────────────────────────────
        $seed(16, [
            ['input' => '100',   'expected_output' => "candidate: 100\nlarger even found: 102\ncontradiction: True",      'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',     'expected_output' => "candidate: 0\nlarger even found: 2\ncontradiction: True",          'is_hidden' => false, 'order_index' => 2],
            ['input' => '1000',  'expected_output' => "candidate: 1000\nlarger even found: 1002\ncontradiction: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => '-4',    'expected_output' => "candidate: -4\nlarger even found: -2\ncontradiction: True",        'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(17, [
            ['input' => '1000',  'expected_output' => 'No rational found (consistent with irrationality)', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '500',   'expected_output' => 'No rational found (consistent with irrationality)', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '100',   'expected_output' => 'No rational found (consistent with irrationality)', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '200',   'expected_output' => 'No rational found (consistent with irrationality)', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(18, [
            ['input' => "3\n2\n3\n5",     'expected_output' => "N: 31\nnew prime factor: 31\nin original list: False",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2\n3",        'expected_output' => "N: 7\nnew prime factor: 7\nin original list: False",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2",           'expected_output' => "N: 3\nnew prime factor: 3\nin original list: False",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2\n3\n5\n7",  'expected_output' => "N: 211\nnew prime factor: 211\nin original list: False", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(19, [
            ['input' => '360',   'expected_output' => "2 2 2 3 3 5\nUnique: True",   'is_hidden' => false, 'order_index' => 1],
            ['input' => '12',    'expected_output' => "2 2 3\nUnique: True",          'is_hidden' => false, 'order_index' => 2],
            ['input' => '97',    'expected_output' => "97\nUnique: True",             'is_hidden' => true,  'order_index' => 3],
            ['input' => '1024',  'expected_output' => "2 2 2 2 2 2 2 2 2 2\nUnique: True", 'is_hidden' => true, 'order_index' => 4],
        ]);

        $seed(20, [
            ['input' => '1 20',    'expected_output' => "Odd n tested: 10\nn² odd confirmed: 10\nContradiction with assumption: 0 found", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '1 100',   'expected_output' => "Odd n tested: 50\nn² odd confirmed: 50\nContradiction with assumption: 0 found", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '1 10',    'expected_output' => "Odd n tested: 5\nn² odd confirmed: 5\nContradiction with assumption: 0 found",  'is_hidden' => true,  'order_index' => 3],
            ['input' => '1 50',    'expected_output' => "Odd n tested: 25\nn² odd confirmed: 25\nContradiction with assumption: 0 found",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── L5.5 ──────────────────────────────────────────────────────────
        $seed(21, [
            ['input' => '100',  'expected_output' => "residues found: [0, 1]\nonly 0 and 1: True", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '50',   'expected_output' => "residues found: [0, 1]\nonly 0 and 1: True", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',   'expected_output' => "residues found: [0, 1]\nonly 0 and 1: True", 'is_hidden' => true,  'order_index' => 3],
            ['input' => '200',  'expected_output' => "residues found: [0, 1]\nonly 0 and 1: True", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(22, [
            ['input' => '19',   'expected_output' => 'Partition valid: True', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '100',  'expected_output' => 'Partition valid: True', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '3',    'expected_output' => 'Partition valid: True', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '50',   'expected_output' => 'Partition valid: True', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(23, [
            ['input' => '-50 50',    'expected_output' => 'All verified', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '1 100',     'expected_output' => 'All verified', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '-100 100',  'expected_output' => 'All verified', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '0 200',     'expected_output' => 'All verified', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(24, [
            ['input' => '1000',  'expected_output' => "valid last digits: {0, 1, 4, 5, 6, 9}\ninvalid digits absent: True", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '100',   'expected_output' => "valid last digits: {0, 1, 4, 5, 6, 9}\ninvalid digits absent: True", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '500',   'expected_output' => "valid last digits: {0, 1, 4, 5, 6, 9}\ninvalid digits absent: True", 'is_hidden' => true,  'order_index' => 3],
            ['input' => '10',    'expected_output' => "valid last digits: {0, 1, 4, 5, 6, 9}\ninvalid digits absent: True", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(25, [
            ['input' => '100',   'expected_output' => 'All verified', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '50',    'expected_output' => 'All verified', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '300',   'expected_output' => 'All verified', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '10',    'expected_output' => 'All verified', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── L5.6 ──────────────────────────────────────────────────────────
        $seed(26, [
            ['input' => '50',   'expected_output' => "base case: True\ninductive steps verified: 50\nall hold: True",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '100',  'expected_output' => "base case: True\ninductive steps verified: 100\nall hold: True", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',   'expected_output' => "base case: True\ninductive steps verified: 10\nall hold: True",  'is_hidden' => true,  'order_index' => 3],
            ['input' => '200',  'expected_output' => "base case: True\ninductive steps verified: 200\nall hold: True", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(27, [
            ['input' => '20',   'expected_output' => "LHS at n=20: 2870\nRHS at n=20: 2870\nAll match",   'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',   'expected_output' => "LHS at n=10: 385\nRHS at n=10: 385\nAll match",     'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',    'expected_output' => "LHS at n=5: 55\nRHS at n=5: 55\nAll match",         'is_hidden' => true,  'order_index' => 3],
            ['input' => '50',   'expected_output' => "LHS at n=50: 44200\nRHS at n=50: 44200\nAll match",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(28, [
            ['input' => '5',   'expected_output' => "1: 2^1=2 > 1: True\n2: 2^2=4 > 2: True\n3: 2^3=8 > 3: True\n4: 2^4=16 > 4: True\n5: 2^5=32 > 5: True\nAll verified",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',   'expected_output' => "1: 2^1=2 > 1: True\n2: 2^2=4 > 2: True\n3: 2^3=8 > 3: True\nAll verified",                                             'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',  'expected_output' => "1: 2^1=2 > 1: True\n2: 2^2=4 > 2: True\n3: 2^3=8 > 3: True\n4: 2^4=16 > 4: True\n5: 2^5=32 > 5: True\n6: 2^6=64 > 6: True\n7: 2^7=128 > 7: True\n8: 2^8=256 > 8: True\n9: 2^9=512 > 9: True\n10: 2^10=1024 > 10: True\nAll verified", 'is_hidden' => true, 'order_index' => 3],
            ['input' => '1',   'expected_output' => "1: 2^1=2 > 1: True\nAll verified",                                                                                      'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(29, [
            ['input' => '8',   'expected_output' => "n=4: 24 > 16: True\nn=5: 120 > 32: True\nn=6: 720 > 64: True\nn=7: 5040 > 128: True\nn=8: 40320 > 256: True\nAll verified",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '6',   'expected_output' => "n=4: 24 > 16: True\nn=5: 120 > 32: True\nn=6: 720 > 64: True\nAll verified",                                                 'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',  'expected_output' => "n=4: 24 > 16: True\nn=5: 120 > 32: True\nn=6: 720 > 64: True\nn=7: 5040 > 128: True\nn=8: 40320 > 256: True\nn=9: 362880 > 512: True\nn=10: 3628800 > 1024: True\nAll verified", 'is_hidden' => true, 'order_index' => 3],
            ['input' => '4',   'expected_output' => "n=4: 24 > 16: True\nAll verified",                                                                                           'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(30, [
            ['input' => '30',   'expected_output' => "base case ok\nsteps verified: 30\nall correct: True", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',   'expected_output' => "base case ok\nsteps verified: 10\nall correct: True", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '50',   'expected_output' => "base case ok\nsteps verified: 50\nall correct: True", 'is_hidden' => true,  'order_index' => 3],
            ['input' => '5',    'expected_output' => "base case ok\nsteps verified: 5\nall correct: True",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── L5.7 ──────────────────────────────────────────────────────────
        $seed(31, [
            ['input' => '100',  'expected_output' => 'All n in [2, 100] have a prime factor: True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '50',   'expected_output' => 'All n in [2, 50] have a prime factor: True',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '200',  'expected_output' => 'All n in [2, 200] have a prime factor: True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '10',   'expected_output' => 'All n in [2, 10] have a prime factor: True',   'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(32, [
            ['input' => '8',   'expected_output' => "F(0) = 0 ≤ 2^0 = 1: True\nF(1) = 1 ≤ 2^1 = 2: True\nF(2) = 1 ≤ 2^2 = 4: True\nF(3) = 2 ≤ 2^3 = 8: True\nF(4) = 3 ≤ 2^4 = 16: True\nF(5) = 5 ≤ 2^5 = 32: True\nF(6) = 8 ≤ 2^6 = 64: True\nF(7) = 13 ≤ 2^7 = 128: True\nF(8) = 21 ≤ 2^8 = 256: True\nAll hold: True", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',   'expected_output' => "F(0) = 0 ≤ 2^0 = 1: True\nF(1) = 1 ≤ 2^1 = 2: True\nF(2) = 1 ≤ 2^2 = 4: True\nF(3) = 2 ≤ 2^3 = 8: True\nAll hold: True", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',  'expected_output' => "F(0) = 0 ≤ 2^0 = 1: True\nF(1) = 1 ≤ 2^1 = 2: True\nF(2) = 1 ≤ 2^2 = 4: True\nF(3) = 2 ≤ 2^3 = 8: True\nF(4) = 3 ≤ 2^4 = 16: True\nF(5) = 5 ≤ 2^5 = 32: True\nF(6) = 8 ≤ 2^6 = 64: True\nF(7) = 13 ≤ 2^7 = 128: True\nF(8) = 21 ≤ 2^8 = 256: True\nF(9) = 34 ≤ 2^9 = 512: True\nF(10) = 55 ≤ 2^10 = 1024: True\nAll hold: True", 'is_hidden' => true, 'order_index' => 3],
            ['input' => '0',   'expected_output' => "F(0) = 0 ≤ 2^0 = 1: True\nAll hold: True", 'is_hidden' => true, 'order_index' => 4],
        ]);

        $seed(33, [
            ['input' => '10',  'expected_output' => "6: 2*3 + 3*0\n7: 2*2 + 3*1\n8: 2*4 + 3*0\n9: 2*0 + 3*3\n10: 2*5 + 3*0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '8',   'expected_output' => "6: 2*3 + 3*0\n7: 2*2 + 3*1\n8: 2*4 + 3*0",                                 'is_hidden' => false, 'order_index' => 2],
            ['input' => '12',  'expected_output' => "6: 2*3 + 3*0\n7: 2*2 + 3*1\n8: 2*4 + 3*0\n9: 2*0 + 3*3\n10: 2*5 + 3*0\n11: 2*4 + 3*1\n12: 2*6 + 3*0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => '6',   'expected_output' => "6: 2*3 + 3*0",                                                              'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(34, [
            ['input' => '10',  'expected_output' => "T(1) = 1 ≤ 10*1 = 10: True\nT(2) = 4 ≤ 10*2 = 20: True\nT(3) = 6 ≤ 10*3 = 30: True\nT(4) = 9 ≤ 10*4 = 40: True\nT(5) = 12 ≤ 10*5 = 50: True\nT(6) = 16 ≤ 10*6 = 60: True\nT(7) = 19 ≤ 10*7 = 70: True\nT(8) = 22 ≤ 10*8 = 80: True\nT(9) = 27 ≤ 10*9 = 90: True\nT(10) = 31 ≤ 10*10 = 100: True\nAll hold: True", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '5',   'expected_output' => "T(1) = 1 ≤ 10*1 = 10: True\nT(2) = 4 ≤ 10*2 = 20: True\nT(3) = 6 ≤ 10*3 = 30: True\nT(4) = 9 ≤ 10*4 = 40: True\nT(5) = 12 ≤ 10*5 = 50: True\nAll hold: True", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '1',   'expected_output' => "T(1) = 1 ≤ 10*1 = 10: True\nAll hold: True",  'is_hidden' => true, 'order_index' => 3],
            ['input' => '3',   'expected_output' => "T(1) = 1 ≤ 10*1 = 10: True\nT(2) = 4 ≤ 10*2 = 20: True\nT(3) = 6 ≤ 10*3 = 30: True\nAll hold: True", 'is_hidden' => true, 'order_index' => 4],
        ]);

        $seed(35, [
            ['input' => '84',    'expected_output' => "2 2 3 7\nVerified: True",            'is_hidden' => false, 'order_index' => 1],
            ['input' => '360',   'expected_output' => "2 2 2 3 3 5\nVerified: True",        'is_hidden' => false, 'order_index' => 2],
            ['input' => '97',    'expected_output' => "97\nVerified: True",                 'is_hidden' => true,  'order_index' => 3],
            ['input' => '1000',  'expected_output' => "2 2 2 5 5 5\nVerified: True",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── L5.8 ──────────────────────────────────────────────────────────
        $seed(36, [
            ['input' => '0.000001',   'expected_output' => "Exists: True\napprox: 1.61803399", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0001',     'expected_output' => "Exists: True\napprox: 1.61803436", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.00001',    'expected_output' => "Exists: True\napprox: 1.61803436", 'is_hidden' => true,  'order_index' => 3],
            ['input' => '0.001',      'expected_output' => "Exists: True\napprox: 1.61816406", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(37, [
            ['input' => "17\n5",   'expected_output' => "q: 3\nr: 2\nverified: True\nunique: True",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "20\n7",   'expected_output' => "q: 2\nr: 6\nverified: True\nunique: True",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n13", 'expected_output' => "q: 7\nr: 9\nverified: True\nunique: True",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n3",    'expected_output' => "q: 0\nr: 0\nverified: True\nunique: True",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(38, [
            ['input' => "24\n36",   'expected_output' => "gcd: 12\nall common divisors: [1, 2, 3, 4, 6, 12]\nall divide gcd: True",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "12\n8",    'expected_output' => "gcd: 4\nall common divisors: [1, 2, 4]\nall divide gcd: True",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n13",    'expected_output' => "gcd: 1\nall common divisors: [1]\nall divide gcd: True",                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "100\n75",  'expected_output' => "gcd: 25\nall common divisors: [1, 5, 25]\nall divide gcd: True",            'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(39, [
            ['input' => "4\n1 2 3 4 5",          'expected_output' => 'pair found: 1 2 (odd part: 1)',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3 6 9 4",             'expected_output' => 'pair found: 3 6 (odd part: 3)',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 3 5 7 9 11",        'expected_output' => 'pair found: 1 3 (odd part: 1)',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n2 4 6",               'expected_output' => 'pair found: 2 4 (odd part: 1)',  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(40, [
            ['input' => '7',   'expected_output' => "1: inverse=1\n2: inverse=4\n3: inverse=5\n4: inverse=2\n5: inverse=3\n6: inverse=6\nAll unique: True",         'is_hidden' => false, 'order_index' => 1],
            ['input' => '5',   'expected_output' => "1: inverse=1\n2: inverse=3\n3: inverse=2\n4: inverse=4\nAll unique: True",                                    'is_hidden' => false, 'order_index' => 2],
            ['input' => '11',  'expected_output' => "1: inverse=1\n2: inverse=6\n3: inverse=4\n4: inverse=3\n5: inverse=9\n6: inverse=2\n7: inverse=8\n8: inverse=7\n9: inverse=5\n10: inverse=10\nAll unique: True", 'is_hidden' => true, 'order_index' => 3],
            ['input' => '3',   'expected_output' => "1: inverse=1\n2: inverse=2\nAll unique: True",                                                                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── L5.9 ──────────────────────────────────────────────────────────
        $seed(41, [
            ['input' => '10',   'expected_output' => 'counterexample: 2 (even and prime)', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '100',  'expected_output' => 'counterexample: 2 (even and prime)', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '2',    'expected_output' => 'counterexample: 2 (even and prime)', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '50',   'expected_output' => 'counterexample: 2 (even and prime)', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(42, [
            ['input' => '10',   'expected_output' => 'counterexample: 3 + 5 = 8 (not prime)', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '50',   'expected_output' => 'counterexample: 3 + 5 = 8 (not prime)', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '100',  'expected_output' => 'counterexample: 3 + 5 = 8 (not prime)', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '20',   'expected_output' => 'counterexample: 3 + 5 = 8 (not prime)', 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(43, [
            ['input' => '4',   'expected_output' => "F0 = 3: prime\nF1 = 5: prime\nF2 = 17: prime\nF3 = 257: prime\nF4 = 65537: prime", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',   'expected_output' => "F0 = 3: prime\nF1 = 5: prime\nF2 = 17: prime",                                     'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',   'expected_output' => 'F0 = 3: prime',                                                                    'is_hidden' => true,  'order_index' => 3],
            ['input' => '3',   'expected_output' => "F0 = 3: prime\nF1 = 5: prime\nF2 = 17: prime\nF3 = 257: prime",                    'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(44, [
            ['input' => '5',    'expected_output' => 'counterexample: a=-1, b=1, a²=b²=1',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',   'expected_output' => 'counterexample: a=-1, b=1, a²=b²=1',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '100',  'expected_output' => 'counterexample: a=-1, b=1, a²=b²=1',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',    'expected_output' => 'counterexample: a=-1, b=1, a²=b²=1',   'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(45, [
            ['input' => '10',   'expected_output' => 'no integer sum found in range', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '50',   'expected_output' => 'no integer sum found in range', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '100',  'expected_output' => 'no integer sum found in range', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '5',    'expected_output' => 'no integer sum found in range', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── L5.10 ─────────────────────────────────────────────────────────
        $seed(46, [
            ['input' => "4\n4\n-2\n9\n200",         'expected_output' => "4: direct\n-2: contradiction\n9: cases\n200: induction",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n6\n-5\n101",             'expected_output' => "6: direct\n-5: contradiction\n101: induction",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n2\n3\n-1\n500",          'expected_output' => "2: direct\n3: contrapositive\n-1: contradiction\n500: induction",'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n12\n50",                 'expected_output' => "12: cases\n50: contrapositive",                                  'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(47, [
            ['input' => "3\nforall n, f(n) holds\nthere exists n such that\nP and not P",  'expected_output' => "induction\nexistence\ncontradiction",              'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\ncases: n mod k\nif P then Q",                                  'expected_output' => "cases\ndirect or contrapositive",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nforall n, f(n) holds\ncases: n mod k\nP and not P",           'expected_output' => "induction\ncases\ncontradiction",                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nthere exists n such that",                                     'expected_output' => 'existence',                                         'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(48, [
            ['input' => '7',    'expected_output' => "Theorem 1: True\nTheorem 2: True\nTheorem 3: True\nTheorem 4: True\nTheorem 5: True", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',    'expected_output' => "Theorem 1: True\nTheorem 2: True\nTheorem 3: True\nTheorem 4: True\nTheorem 5: True", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '100',  'expected_output' => "Theorem 1: True\nTheorem 2: True\nTheorem 3: True\nTheorem 4: True\nTheorem 5: True", 'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',    'expected_output' => "Theorem 1: True\nTheorem 2: True\nTheorem 3: True\nTheorem 4: True\nTheorem 5: True", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(49, [
            ['input' => '64',    'expected_output' => "contradiction: cost=1\ndirect: cost=1\ninduction: cost=6\nexhaustion: cost=64",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '1024',  'expected_output' => "contradiction: cost=1\ndirect: cost=1\ninduction: cost=10\nexhaustion: cost=1024", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '100',   'expected_output' => "contradiction: cost=1\ndirect: cost=1\ninduction: cost=7\nexhaustion: cost=100",   'is_hidden' => true,  'order_index' => 3],
            ['input' => '2',     'expected_output' => "contradiction: cost=1\ndirect: cost=1\ninduction: cost=1\nexhaustion: cost=2",     'is_hidden' => true,  'order_index' => 4],
        ]);

        $seed(50, [
            ['input' => "induction\n10",        'expected_output' => "strategy: induction\nresult: True",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "direct\n7",            'expected_output' => "strategy: direct\nresult: True",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "contrapositive\n5",    'expected_output' => "strategy: contrapositive\nresult: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "cases\n9",             'expected_output' => "strategy: cases\nresult: True",            'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 5 Coding (Advanced) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}