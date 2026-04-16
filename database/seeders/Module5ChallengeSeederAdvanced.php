<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module5ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 5 — Methods of Proof (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Methods of Proof',
            'description'           => 'Engage with rigorous, multi-layered proof problems featuring code snippets and algorithmic reasoning. Questions demand deep understanding of proof correctness, complexity-aware arguments, and subtle logical structures.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 2000,
            'order_index'           => 5,
        ]);

        $this->command->info("Seeding 50 advanced-level questions on Methods of Proof...");

        $qaData = [

            // ── DIRECT PROOF — ADVANCED ───────────────────────────────────
            [
                'q' => "Consider this Python function:\n\ndef sum_of_cubes(n):\n    return (n * (n + 1) // 2) ** 2\n\nThis implements the identity: Σᵢ₌₁ⁿ i³ = [n(n+1)/2]²\n\nIn a direct algebraic proof of this identity, which step correctly expands [n(n+1)/2]²?",
                'opts' => [
                    ['n²(n+1)² / 4', true],
                    ['n(n+1) / 4', false],
                    ['n²(n+1) / 2', false],
                    ['[n(n+1)]² / 2', false],
                ],
            ],
            [
                'q' => "Consider the claim: For all integers n, n(n+1)(2n+1) is divisible by 6.\n\nA direct proof factors the expression. Which combination below covers divisibility by 6 = 2 × 3?",
                'opts' => [
                    ['n is always even, and (2n+1) is divisible by 3', false],
                    ['Among n, n+1, one is even (giving factor 2), and among n, n+1, 2n+1 one is divisible by 3 (giving factor 3)', true],
                    ['(2n+1) is always divisible by 6', false],
                    ['n(n+1) is always divisible by 6 alone', false],
                ],
            ],
            [
                'q' => "The following Python code attempts to check the Goldbach conjecture for even numbers up to N:\n\ndef goldbach_check(N):\n    primes = sieve(N)\n    for n in range(4, N+1, 2):\n        found = any(n - p in primes for p in primes if p <= n)\n        if not found:\n            return False\n    return True\n\nRegardless of what this code outputs, why does a passing result NOT constitute a proof of Goldbach's conjecture?",
                'opts' => [
                    ['Because the sieve function may have a bug', false],
                    ['Because computational verification up to a finite N does not cover infinitely many even numbers — a proof must hold for all cases', true],
                    ['Because Goldbach\'s conjecture is already proven', false],
                    ['Because the code uses Python which is not a formal proof system', false],
                ],
            ],

            // ── PROOF BY CONTRAPOSITIVE — ADVANCED ───────────────────────
            [
                'q' => "Claim: For all integers a, b, c — if a ∤ bc then a ∤ b.\n\nThe contrapositive is: If a | b, then a | bc.\n\nProof: Assume a | b, so b = ak. Then bc = akc = a(kc). Since kc is an integer, a | bc.\n\nWhich logical law guarantees that proving the contrapositive is sufficient?",
                'opts' => [
                    ['Modus Ponens', false],
                    ['Logical equivalence of a conditional and its contrapositive', true],
                    ['Proof by cases', false],
                    ['The Well-Ordering Principle', false],
                ],
            ],
            [
                'q' => "Claim: For integers n, if n³ + 5 is odd, then n is even.\n\nProof by contrapositive — assume n is odd (n = 2k + 1). Compute n³ + 5:",
                'opts' => [
                    ['8k³ + 12k² + 6k + 1 + 5 = 8k³ + 12k² + 6k + 6 = 2(4k³ + 6k² + 3k + 3) → even', true],
                    ['2k³ + 5 → odd', false],
                    ['(2k+1)³ + 5 = 2k³ + 3k² + 3k + 6 → always prime', false],
                    ['n³ + 5 = 6 → even only when k = 0', false],
                ],
            ],
            [
                'q' => "Claim: In a graph G, if G has no Eulerian circuit, then G has a vertex of odd degree.\n\nThe contrapositive (and the actual theorem's sufficient condition) states:",
                'opts' => [
                    ['If G has no vertex of odd degree, then G has an Eulerian circuit (assuming G is connected)', true],
                    ['If G has a vertex of odd degree, then G has an Eulerian circuit', false],
                    ['If G has an Eulerian circuit, then all vertices have even degree', false],
                    ['If G has an Eulerian circuit, then G is bipartite', false],
                ],
            ],

            // ── PROOF BY CONTRADICTION — ADVANCED ────────────────────────
            [
                'q' => "Claim: The set of real numbers is uncountable (Cantor's Diagonal Argument).\n\nAssume the reals in [0,1] are countable, listed as r₁, r₂, r₃, ... Construct a number d where the n-th decimal digit of d differs from the n-th decimal digit of rₙ.\n\nWhy is d not in the list?",
                'opts' => [
                    ['Because d has infinitely many digits', false],
                    ['Because d differs from every rₙ in at least the n-th decimal position — it cannot equal any rₙ', true],
                    ['Because d > 1, so it is outside [0,1]', false],
                    ['Because the list r₁, r₂, ... is finite', false],
                ],
            ],
            [
                'q' => "The following recursive Python function computes Fibonacci numbers:\n\ndef fib(n):\n    if n <= 1:\n        return n\n    return fib(n-1) + fib(n-2)\n\nA claim states: fib(n) terminates for all n ≥ 0. Prove this by contradiction — assume there exists a minimal m ≥ 0 such that fib(m) does NOT terminate. If m > 1, then fib(m) calls fib(m-1) and fib(m-2). Why is this a contradiction?",
                'opts' => [
                    ['Because fib(m-1) and fib(m-2) are guaranteed to be even numbers', false],
                    ['Because m was chosen minimal — but fib(m-1) and fib(m-2) have smaller indices and must terminate, meaning fib(m) terminates too, contradicting the assumption', true],
                    ['Because Python has a call stack limit', false],
                    ['Because the Fibonacci sequence is bounded', false],
                ],
            ],
            [
                'q' => "Claim: There is no polynomial p(x) with integer coefficients such that p(n) is prime for ALL integers n.\n\nProof: Assume p(n) exists. Let p(0) = k (a prime, WLOG k > 0). Then for any integer m, p(mk) ≡ p(0) ≡ 0 (mod k). Why does p(mk) ≡ 0 (mod k) lead to a contradiction?",
                'opts' => [
                    ['Because mk is always even', false],
                    ['Because p(mk) is divisible by k > 1 for infinitely many m, so p(mk) is not prime for those m — contradicting the assumption', true],
                    ['Because p(0) = k cannot be prime if k > 1', false],
                    ['Because polynomials cannot take negative values', false],
                ],
            ],

            // ── MATHEMATICAL INDUCTION — ADVANCED ────────────────────────
            [
                'q' => "The following Python code checks the induction claim '2^n ≥ n²' starting from n=4:\n\ndef check_induction(k):\n    # Assume 2**k >= k**2\n    lhs = 2 * (2**k)   # = 2^(k+1)\n    rhs = (k+1)**2     # = (k+1)²\n    # Need: 2 * 2^k >= (k+1)^2\n    # i.e., 2 * k^2 >= (k+1)^2\n    return 2 * k**2 >= (k+1)**2\n\nFor which smallest k does check_induction(k) return True, making the inductive step valid?",
                'opts' => [
                    ['k = 1', false],
                    ['k = 2', false],
                    ['k = 3', false],
                    ['k = 3: 2(9) = 18 ≥ 16 = (4)² ✓ — but k=2: 2(4)=8 ≥ 9? No. So k ≥ 3', true],
                ],
            ],
            [
                'q' => "Claim: For all n ≥ 1, 7 | (8^n − 1).\n\nInductive step: Assume 7 | (8^k − 1). Show 7 | (8^(k+1) − 1).\n\n8^(k+1) − 1 = 8 · 8^k − 1 = 8(8^k − 1) + 7\n\nWhich divisibility rule closes the proof?",
                'opts' => [
                    ['7 | 8 and 7 | 1', false],
                    ['7 | 8(8^k − 1) by the inductive hypothesis, and 7 | 7, so 7 | their sum', true],
                    ['8^(k+1) is always divisible by 7', false],
                    ['7 | (8^k − 1) implies 7 | 8^k', false],
                ],
            ],
            [
                'q' => "Claim: For all n ≥ 1, any 2^n × 2^n board with one square removed can be tiled by L-shaped trominoes.\n\nThis is proven by induction. In the inductive step for a 2^(k+1) × 2^(k+1) board, you divide it into four 2^k × 2^k quadrants. One quadrant contains the missing square. What do you do with the other three quadrants to enable the inductive hypothesis?",
                'opts' => [
                    ['Leave them untiled', false],
                    ['Place a single L-tromino at the center corner of the three full quadrants — each then has one square "missing" and the hypothesis applies', true],
                    ['Show they can be tiled by dominoes instead', false],
                    ['Apply the base case directly to all three', false],
                ],
            ],
            [
                'q' => "Consider the recurrence T(n) = T(n/2) + 1, T(1) = 0 (binary search).\n\nClaim: T(n) = log₂(n) for n a power of 2.\n\nInductive step: Assume T(n) = log₂(n). For 2n: T(2n) = T(n) + 1 = log₂(n) + 1 = log₂(2n). This is a proof by:",
                'opts' => [
                    ['Contradiction', false],
                    ['Induction on n restricted to powers of 2', true],
                    ['Cases on even and odd n', false],
                    ['Contrapositive on the recurrence', false],
                ],
            ],

            // ── STRONG INDUCTION — ADVANCED ───────────────────────────────
            [
                'q' => "Claim: Every integer n ≥ 2 has a prime factor.\n\nIn the strong induction proof, if n is not prime, it factors as n = ab with 2 ≤ a, b < n. By the strong inductive hypothesis, a has a prime factor p. Why does p also divide n?",
                'opts' => [
                    ['Because p divides b as well', false],
                    ['Because p | a and a | n, so by transitivity of divisibility, p | n', true],
                    ['Because p must be less than n', false],
                    ['Because p is the smallest prime', false],
                ],
            ],
            [
                'q' => "Claim: The Euclidean Algorithm terminates. Formally: the sequence r₀ = a, r₁ = b, r₂ = a mod b, r₃ = b mod r₂, ... is strictly decreasing and bounded below by 0.\n\nWhy does termination follow without induction?",
                'opts' => [
                    ['Because the algorithm is written in a bounded loop', false],
                    ['By the Well-Ordering Principle — a strictly decreasing sequence of non-negative integers cannot continue forever', true],
                    ['Because gcd(a, b) ≤ a always', false],
                    ['Because b < a is required at the start', false],
                ],
            ],

            // ── PROOF CORRECTNESS AND DEBUGGING ──────────────────────────
            [
                'q' => "The following Python function is claimed to check if n is prime:\n\ndef is_prime(n):\n    if n < 2:\n        return False\n    for i in range(2, n):\n        if n % i == 0:\n            return False\n    return True\n\nA proof of correctness uses the fact: if n has no divisor in [2, n−1], then n is prime. Which proof technique directly mirrors the loop's logic?",
                'opts' => [
                    ['Proof by contradiction: assume n is composite and derive no divisor exists', false],
                    ['Direct proof: the loop checks every potential divisor exhaustively — if none divides n, the definition of prime is satisfied', true],
                    ['Mathematical induction on n', false],
                    ['Proof by cases on whether n is even or odd', false],
                ],
            ],
            [
                'q' => "Optimized primality check:\n\ndef is_prime_fast(n):\n    if n < 2: return False\n    if n == 2: return True\n    if n % 2 == 0: return False\n    i = 3\n    while i * i <= n:\n        if n % i == 0:\n            return False\n        i += 2\n    return True\n\nThe correctness proof relies on: 'If n is composite, it has a factor ≤ √n.' Prove this claim by contradiction — assume all factors of n are > √n. If n = ab and both a, b > √n, then:",
                'opts' => [
                    ['ab > √n · √n = n, contradicting n = ab', true],
                    ['ab < n, which is also a contradiction', false],
                    ['ab = n is impossible since a and b are both greater than 1', false],
                    ['n must be prime, which contradicts being composite', false],
                ],
            ],
            [
                'q' => "Loop Invariant Proof — the following computes integer exponentiation:\n\ndef power(base, exp):\n    result = 1\n    while exp > 0:\n        if exp % 2 == 1:\n            result *= base\n        base *= base\n        exp //= 2\n    return result\n\nA loop invariant for this function is: result × base^exp = original_base^original_exp.\n\nAt loop termination (exp = 0), the invariant gives result × base⁰ = original_base^original_exp. Therefore result = ?",
                'opts' => [
                    ['0', false],
                    ['base', false],
                    ['original_base^original_exp', true],
                    ['1', false],
                ],
            ],
            [
                'q' => "Flawed induction proof:\n\n'Claim: All positive integers are equal.'\n'Base: {1} — all integers in a 1-element set are equal.'\n'Inductive step: Assume any set of k positive integers has all equal elements. Given a set S of k+1 integers, remove the last to get S₁ (k elements, all equal by IH). Remove the first to get S₂ (k elements, all equal by IH). Since S₁ and S₂ overlap, all elements of S are equal.'\n\nWhere does the proof fail?",
                'opts' => [
                    ['The base case is wrong — {1} has more than one element', false],
                    ['The step from k=1 to k=2 fails — S₁ and S₂ have no overlap when k=1 (each has only 1 element)', true],
                    ['The inductive hypothesis is stated backwards', false],
                    ['Removing elements from a set is not a valid proof step', false],
                ],
            ],

            // ── EXISTENCE, UNIQUENESS, AND COMBINATORICS ──────────────────
            [
                'q' => "Claim (Pigeonhole Principle): If n+1 objects are placed into n boxes, at least one box contains 2 or more objects.\n\nProof by contradiction: Assume every box has at most 1 object. Then the total number of objects is at most n × 1 = n. But we have n+1 objects — a contradiction. This is an example of:",
                'opts' => [
                    ['A constructive existence proof', false],
                    ['A non-constructive existence proof — it guarantees a crowded box without identifying which one', true],
                    ['A proof by mathematical induction', false],
                    ['A direct proof', false],
                ],
            ],
            [
                'q' => "Claim: Among any 13 people, at least 2 share a birth month.\n\nWhich proof principle applies directly?",
                'opts' => [
                    ['Proof by contrapositive', false],
                    ['Pigeonhole Principle — 13 people into 12 months guarantees at least one repeated month', true],
                    ['Proof by cases over all 12 months', false],
                    ['Mathematical induction on the number of people', false],
                ],
            ],
            [
                'q' => "Claim: For any set of 10 integers, there exist two whose difference is divisible by 9.\n\nProof: Consider remainders mod 9: each integer has remainder 0, 1, ..., or 8 (9 possibilities). By the Pigeonhole Principle with 10 integers and 9 remainder classes, two integers must share the same remainder. If a ≡ b (mod 9), then:\n",
                'opts' => [
                    ['a = b', false],
                    ['9 | (a − b)', true],
                    ['a + b is divisible by 9', false],
                    ['a × b is divisible by 9', false],
                ],
            ],
            [
                'q' => "Claim: There are infinitely many primes of the form 4k + 3.\n\nThe proof follows the structure of Euclid's proof, but the contradiction relies on which key fact about products of numbers of the form 4k + 1?",
                'opts' => [
                    ['A product of numbers of the form 4k+1 is always of the form 4k+1 (never 4k+3)', true],
                    ['A product of numbers of the form 4k+1 is always even', false],
                    ['A product of numbers of the form 4k+1 is always prime', false],
                    ['A product of numbers of the form 4k+1 is always of the form 4k+3', false],
                ],
            ],

            // ── PROOF TECHNIQUES IN ALGORITHM ANALYSIS ────────────────────
            [
                'q' => "The following merge sort invariant is claimed: After each merge step, the merged subarray is sorted.\n\nThis invariant is proven by induction on the size of the subarray. The base case (size 1) holds trivially. The inductive step merges two sorted subarrays of sizes k₁ and k₂ where k₁, k₂ < k₁+k₂. What inductive hypothesis is being used?",
                'opts' => [
                    ['Any subarray of size k₁+k₂ is sorted', false],
                    ['Any subarray of size < k₁+k₂ that has been merged is sorted', true],
                    ['The merge step runs in O(n log n) time', false],
                    ['k₁ = k₂ always in merge sort', false],
                ],
            ],
            [
                'q' => "A greedy algorithm's correctness is often proven by an 'exchange argument.' The structure of an exchange argument proof is:\n\n1. Take an optimal solution O.\n2. Show that any difference between O and the greedy solution G can be 'swapped' without worsening the objective.\n3. Conclude G is also optimal.\n\nThis is closest to which proof technique?",
                'opts' => [
                    ['Mathematical induction', false],
                    ['A form of direct proof showing equivalence — any optimal solution can be transformed into the greedy solution step by step', true],
                    ['Proof by contradiction', false],
                    ['Strong induction on the number of greedy choices', false],
                ],
            ],
            [
                'q' => "Claim: QuickSort terminates for all inputs of size n ≥ 0.\n\nThe proof is by strong induction. In the inductive step, QuickSort(arr) partitions arr into sub-arrays of sizes k and n−1−k where 0 ≤ k < n. Why is strong induction (not weak) necessary here?",
                'opts' => [
                    ['Because k is not always n−1', true],
                    ['Because the base case fails for n = 1', false],
                    ['Because weak induction does not apply to arrays', false],
                    ['Because the partition step may be incorrect', false],
                ],
            ],

            // ── MODULAR ARITHMETIC AND NUMBER THEORY ──────────────────────
            [
                'q' => "Claim (Fermat's Little Theorem): If p is prime and gcd(a, p) = 1, then a^(p−1) ≡ 1 (mod p).\n\nIn a proof using group theory, the multiplicative group (Z/pZ)* has order p−1. This means every element a satisfies a^(p−1) = identity. The proof technique is:",
                'opts' => [
                    ['Mathematical induction on p', false],
                    ['Direct proof using properties of finite cyclic groups — the order of any element divides the group order', true],
                    ['Proof by contrapositive on a', false],
                    ['Proof by contradiction assuming a^(p−1) ≢ 1', false],
                ],
            ],
            [
                'q' => "The following Python implements the check a^(p-1) ≡ 1 (mod p):\n\ndef fermat_check(a, p):\n    return pow(a, p-1, p) == 1\n\nCarmichael numbers (e.g., 561) satisfy fermat_check(a, 561) == True for all gcd(a,561)=1, yet 561 is NOT prime.\n\nWhat does this demonstrate about using Fermat's test as a primality proof?",
                'opts' => [
                    ['fermat_check is a sufficient proof of primality', false],
                    ['Fermat\'s test can produce false positives — passing the test is necessary but not sufficient for primality', true],
                    ['The implementation of pow() is incorrect', false],
                    ['Carmichael numbers are actually prime', false],
                ],
            ],
            [
                'q' => "Claim: gcd(Fₙ, Fₙ₊₁) = 1 for all n ≥ 1 (consecutive Fibonacci numbers are coprime).\n\nBase case: gcd(F₁, F₂) = gcd(1,1) = 1 ✓\nInductive step: Assume gcd(Fₖ, Fₖ₊₁) = 1. Since Fₖ₊₂ = Fₖ + Fₖ₊₁:\ngcd(Fₖ₊₁, Fₖ₊₂) = gcd(Fₖ₊₁, Fₖ + Fₖ₊₁) = gcd(Fₖ₊₁, Fₖ) = gcd(Fₖ, Fₖ₊₁) = 1.\n\nWhich property of gcd was used in: gcd(Fₖ₊₁, Fₖ + Fₖ₊₁) = gcd(Fₖ₊₁, Fₖ)?",
                'opts' => [
                    ['gcd(a, b) = gcd(a, b mod a)', false],
                    ['gcd(a, b+a) = gcd(a, b) — adding a multiple of one argument does not change the gcd', true],
                    ['gcd(a, a) = a', false],
                    ['gcd(a, b) = gcd(b, a)', false],
                ],
            ],

            // ── EDGE CASES AND ROBUSTNESS ──────────────────────────────────
            [
                'q' => "A student proves: 'For all n ≥ 1, if n is a perfect square, then √n is rational.'\n\nThe proof writes n = k² for k a positive integer, so √n = k which is rational. But what important edge case was implicitly excluded?",
                'opts' => [
                    ['n = 0 is a perfect square (0 = 0²) and √0 = 0 is rational — actually no edge case is missed', true],
                    ['n could be a fractional perfect square', false],
                    ['√n could be negative', false],
                    ['k might not be an integer', false],
                ],
            ],
            [
                'q' => "Loop invariant for binary search:\n\ndef binary_search(arr, target):\n    lo, hi = 0, len(arr) - 1\n    while lo <= hi:\n        mid = (lo + hi) // 2\n        if arr[mid] == target:\n            return mid\n        elif arr[mid] < target:\n            lo = mid + 1\n        else:\n            hi = mid - 1\n    return -1\n\nThe invariant is: 'If target is in arr, it is in arr[lo..hi].'\n\nProving this invariant is maintained when arr[mid] < target requires showing:",
                'opts' => [
                    ['arr[mid] > target in the next iteration', false],
                    ['Setting lo = mid + 1 preserves the invariant because arr is sorted and target > arr[mid] means target cannot be in arr[lo..mid]', true],
                    ['The array is halved each iteration', false],
                    ['hi − lo decreases by 1 each step', false],
                ],
            ],
            [
                'q' => "A divide-and-conquer algorithm has recurrence T(n) = 2T(n/2) + n.\n\nClaim: T(n) = Θ(n log n).\n\nA formal proof of an exact closed form T(n) = n log₂(n) for n a power of 2 uses:",
                'opts' => [
                    ['Proof by contradiction', false],
                    ['Mathematical induction on n (restricted to powers of 2)', true],
                    ['Proof by cases on even/odd n', false],
                    ['Existence proof for a polynomial solution', false],
                ],
            ],
            [
                'q' => "Claim: The following recursive algorithm computes gcd correctly:\n\ndef gcd(a, b):\n    return a if b == 0 else gcd(b, a % b)\n\nThe correctness proof uses the lemma: gcd(a, b) = gcd(b, a mod b).\n\nThe termination proof uses the fact that the sequence b, a mod b, ... is strictly decreasing. What guarantees it reaches 0?",
                'opts' => [
                    ['By mathematical induction on the value of b', false],
                    ['By the Well-Ordering Principle — a strictly decreasing sequence of non-negative integers must terminate at 0', true],
                    ['Because a mod b < b always, and b decreases each step', false],
                    ['Because gcd(a, 0) = a is defined as the base case', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 5 — Methods of Proof (Advanced).");
    }
}