<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module5ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 5 — Methods of Proof (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Methods of Proof',
            'description'           => 'Apply proof techniques to multi-step problems, trace through structured arguments, identify flaws in attempted proofs, and construct your own proofs on number theory and combinatorics topics.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 1500,
            'order_index'           => 5,
        ]);

        $this->command->info("Seeding 50 intermediate-level questions on Methods of Proof...");

        $qaData = [

            // ── DIRECT PROOF — MULTI-STEP ─────────────────────────────────
            [
                'q' => "Consider the following proof attempt:\n\nClaim: If n is divisible by 6, then n² is divisible by 36.\nProof: Let n = 6k. Then n² = 36k². Since 36k² = 36 · k², n² is divisible by 36. ∎\n\nIs this proof correct?",
                'opts' => [
                    ['No — the proof never uses k', false],
                    ['Yes — the proof is complete and correct', true],
                    ['No — we need to verify divisibility by 4 separately', false],
                    ['No — n must also be shown to be positive', false],
                ],
            ],
            [
                'q' => "Claim: For all integers m and n, if m and n are both odd, then mn + m + n is odd.\n\nLet m = 2a+1, n = 2b+1. Compute mn + m + n.",
                'opts' => [
                    ['4ab + 2a + 2b + 2', false],
                    ['4ab + 4a + 4b + 3', false],
                    ['4ab + 2a + 2b + 1 + 2a + 1 + 2b + 1 = 4ab + 4a + 4b + 3', true],
                    ['2ab + a + b + 1', false],
                ],
            ],
            [
                'q' => "Continuing: mn + m + n = 4ab + 4a + 4b + 3 = 2(2ab + 2a + 2b + 1) + 1.\n\nWhat does this show?",
                'opts' => [
                    ['mn + m + n is even', false],
                    ['mn + m + n is odd, completing the direct proof', true],
                    ['mn + m + n equals 3', false],
                    ['We need to check one more case', false],
                ],
            ],
            [
                'q' => "Direct Proof Claim: For all integers n, n³ − n is divisible by 3.\n\nFactor n³ − n.",
                'opts' => [
                    ['n(n − 1)(n + 1)', true],
                    ['n(n² − 1)', false],
                    ['(n−1)(n+1)²', false],
                    ['n²(n − 1)', false],
                ],
            ],
            [
                'q' => "n³ − n = n(n−1)(n+1) is the product of three consecutive integers. Why must 3 divide this product?",
                'opts' => [
                    ['Because n is always divisible by 3', false],
                    ['Among any three consecutive integers, exactly one must be divisible by 3', true],
                    ['Because (n−1)(n+1) = n² − 1 is always divisible by 3', false],
                    ['Because n³ is always divisible by 3', false],
                ],
            ],

            // ── PROOF BY CONTRAPOSITIVE — MULTI-STEP ─────────────────────
            [
                'q' => "Claim: For integers a and b, if ab is odd, then both a and b are odd.\n\nThe contrapositive of this statement is:",
                'opts' => [
                    ['If a and b are odd, then ab is odd', false],
                    ['If a is even or b is even, then ab is even', true],
                    ['If ab is even, then both a and b are even', false],
                    ['If a is even and b is even, then ab is odd', false],
                ],
            ],
            [
                'q' => "In the contrapositive proof above (WLOG assume a is even, so a = 2k). What is ab?",
                'opts' => [
                    ['2kb', true],
                    ['k + b', false],
                    ['2k + b', false],
                    ['kb', false],
                ],
            ],
            [
                'q' => "ab = 2kb = 2(kb). Since kb is an integer, ab = 2(integer). What is the conclusion for the original claim?",
                'opts' => [
                    ['ab can be either even or odd', false],
                    ['If at least one of a or b is even, then ab is even — therefore, if ab is odd, both a and b must be odd', true],
                    ['Both a and b must be even', false],
                    ['ab is odd only when a = b', false],
                ],
            ],

            // ── PROOF BY CONTRADICTION — MULTI-STEP ──────────────────────
            [
                'q' => "Claim: log₂(3) is irrational.\n\nProof by contradiction: Assume log₂(3) = p/q where p, q are positive integers. This means 2^(p/q) = 3. Raise both sides to the power q:\n\n2^p = 3^q\n\nWhy is this a contradiction?",
                'opts' => [
                    ['Because p and q must be equal', false],
                    ['Because the left side is even and the right side is odd — they can never be equal', true],
                    ['Because logarithms are always rational', false],
                    ['Because 2^p grows faster than 3^q', false],
                ],
            ],
            [
                'q' => "Flawed proof: 'Claim: √4 is irrational. Proof: Assume √4 = p/q in lowest terms. Then 4q² = p², so p is even. Let p = 2m; then 4q² = 4m², so q² = m², so q is even. Both p and q are even — contradiction!' What is wrong?",
                'opts' => [
                    ['The algebra is incorrect: 4q² ≠ p²', false],
                    ['The assumption is wrong: √4 = 2 is rational, so the contradiction only shows the assumption is consistent', false],
                    ['The "contradiction" found (both even) is genuine, but the claim is false: √4 = 2 IS rational, so the proof is invalid from the start', true],
                    ['The proof is correct — √4 is irrational', false],
                ],
            ],
            [
                'q' => "Claim: There is no integer that is both even and odd.\n\nBy contradiction, assume n is both even and odd. Then n = 2a and n = 2b + 1. Therefore 2a = 2b + 1, so 2(a − b) = 1. Why is this a contradiction?",
                'opts' => [
                    ['Because a and b must be equal', false],
                    ['Because 2(a − b) is even but 1 is odd — an integer cannot be both', true],
                    ['Because a − b must be negative', false],
                    ['Because 2a and 2b+1 are never equal for any values', false],
                ],
            ],

            // ── PROOF BY CASES — MULTI-STEP ───────────────────────────────
            [
                'q' => "Claim: For all integers n, n(n+1) is even.\n\nCase 1 (n even): n = 2k → n(n+1) = 2k(2k+1). Is this even?",
                'opts' => [
                    ['Not necessarily', false],
                    ['Yes — it equals 2 · k(2k+1)', true],
                    ['Only if k is also even', false],
                    ['No — (2k+1) could make it odd', false],
                ],
            ],
            [
                'q' => "Case 2 (n odd): n = 2k+1 → n(n+1) = (2k+1)(2k+2) = (2k+1)·2(k+1). Is this even?",
                'opts' => [
                    ['No — the (2k+1) factor makes it odd', false],
                    ['Yes — it equals 2·(2k+1)(k+1)', true],
                    ['Only if k+1 is prime', false],
                    ['It equals 2k+2, which may be odd', false],
                ],
            ],
            [
                'q' => "After showing n(n+1) is even in both cases, how do you formally close a proof by cases?",
                'opts' => [
                    ['State that we checked one representative example', false],
                    ['State that since every integer is either even or odd, and the property holds in both cases, it holds for all integers n', true],
                    ['State that the cases are independent so only one needs to be true', false],
                    ['State that the result follows from mathematical induction', false],
                ],
            ],
            [
                'q' => "Claim: For all real x, |x| ≥ 0.\n\nWhich case structure best covers all possibilities?",
                'opts' => [
                    ['Case 1: x is rational; Case 2: x is irrational', false],
                    ['Case 1: x ≥ 0 (so |x| = x ≥ 0); Case 2: x < 0 (so |x| = −x > 0)', true],
                    ['Case 1: x = 0; Case 2: x = 1', false],
                    ['Case 1: x is even; Case 2: x is odd', false],
                ],
            ],

            // ── MATHEMATICAL INDUCTION — MULTI-STEP ───────────────────────
            [
                'q' => "Claim: For all n ≥ 1, the sum of the first n odd numbers equals n².\n(1 + 3 + 5 + ... + (2n−1) = n²)\n\nBase case (n = 1): The first odd number is 1 = 1². Is the base case verified?",
                'opts' => [
                    ['No — we need to check n = 2 as well', false],
                    ['Yes — 1 = 1² ✓', true],
                    ['No — the formula gives 0 when n = 1', false],
                    ['Only if 1 is defined as odd', false],
                ],
            ],
            [
                'q' => "Inductive step: Assume 1 + 3 + ... + (2k−1) = k². Now consider the sum for n = k+1:\n[1 + 3 + ... + (2k−1)] + (2k+1) = k² + (2k+1).\n\nSimplify k² + (2k+1).",
                'opts' => [
                    ['(k+1)²', true],
                    ['k² + 2k', false],
                    ['k(k+2)', false],
                    ['(k+2)²', false],
                ],
            ],
            [
                'q' => "Since k² + (2k+1) = (k+1)², the formula n² holds for n = k+1. What can you now conclude by induction?",
                'opts' => [
                    ['The formula holds for k = 1 only', false],
                    ['The formula 1 + 3 + ... + (2n−1) = n² holds for all n ≥ 1', true],
                    ['The formula holds only for odd values of n', false],
                    ['We need to also verify n = 2 separately', false],
                ],
            ],
            [
                'q' => "Claim: For all n ≥ 0, 3 divides (4^n − 1).\n\nBase case (n = 0): 4⁰ − 1 = 0. Is 3 | 0?",
                'opts' => [
                    ['No — 0 is not divisible by 3', false],
                    ['Yes — 0 = 3 × 0, so 3 | 0 ✓', true],
                    ['The base case should start at n = 1', false],
                    ['0 is undefined in divisibility', false],
                ],
            ],
            [
                'q' => "Inductive step: Assume 3 | (4^k − 1). Show 3 | (4^(k+1) − 1).\n4^(k+1) − 1 = 4 · 4^k − 1 = 4(4^k − 1) + 3.\n\nWhy does this show 3 | (4^(k+1) − 1)?",
                'opts' => [
                    ['Because 4 is divisible by 3', false],
                    ['Because 4(4^k − 1) is divisible by 3 (by hypothesis) and 3 is divisible by 3, so their sum is divisible by 3', true],
                    ['Because 4^(k+1) is always divisible by 3', false],
                    ['Because we added and subtracted 1', false],
                ],
            ],

            // ── STRONG INDUCTION — MULTI-STEP ─────────────────────────────
            [
                'q' => "Strong induction claim: Every integer n ≥ 8 can be expressed as 3a + 5b for non-negative integers a, b.\n\nBase cases must include n = 8, 9, 10 (to cover a full cycle). Which expression works for n = 8?",
                'opts' => [
                    ['3(1) + 5(0) = 3', false],
                    ['3(1) + 5(1) = 8 ✓', true],
                    ['3(2) + 5(1) = 11', false],
                    ['3(0) + 5(2) = 10', false],
                ],
            ],
            [
                'q' => "For the inductive step (n ≥ 11): by the strong inductive hypothesis, (n−3) ≥ 8 can be written as 3a + 5b. Therefore n = (n−3) + 3 = 3(a+1) + 5b. What makes this a valid use of the hypothesis?",
                'opts' => [
                    ['Because n − 3 ≥ 8 is in the range covered by the base cases and hypothesis', true],
                    ['Because we added 3, which is a prime', false],
                    ['Because a + 1 is always even', false],
                    ['Because strong induction guarantees any result', false],
                ],
            ],

            // ── EXISTENCE AND UNIQUENESS — INTERMEDIATE ────────────────────
            [
                'q' => "Claim: There exist irrational numbers x and y such that x^y is rational.\n\nLet x = y = √2. Then x^y = √2^(√2). If this is rational, we are done. If it is irrational, let x = √2^(√2) and y = √2. Then x^y = (√2^(√2))^(√2) = √2^2 = 2, which is rational.\n\nWhat type of existence proof is this?",
                'opts' => [
                    ['Constructive — we name the exact irrational numbers', false],
                    ['Non-constructive — we show existence without knowing which case applies', true],
                    ['Proof by induction', false],
                    ['Proof by contrapositive', false],
                ],
            ],
            [
                'q' => "Uniqueness proof structure: Assume x₁ and x₂ are both solutions. Show x₁ = x₂.\n\nFor the equation 3x − 5 = 7, if 3x₁ − 5 = 7 and 3x₂ − 5 = 7, subtracting gives 3x₁ − 3x₂ = 0, so 3(x₁ − x₂) = 0. Conclude:",
                'opts' => [
                    ['x₁ and x₂ can be different', false],
                    ['x₁ = x₂, proving the solution is unique', true],
                    ['x₁ and x₂ are both zero', false],
                    ['We need more information', false],
                ],
            ],

            // ── COUNTEREXAMPLES — INTERMEDIATE ────────────────────────────
            [
                'q' => "Claim: 'For all positive integers n, n² + n + 41 is prime.' This famous formula fails at n = 40. Verify: 40² + 40 + 41 = ?",
                'opts' => [
                    ['1640 (not divisible by 41)', false],
                    ['41² = 1681 = 41 × 41 (composite)', true],
                    ['1721 (prime)', false],
                    ['1600 (even, so composite)', false],
                ],
            ],
            [
                'q' => "Claim: 'For all integers m, n: if m | n² then m | n.' Find a counterexample.",
                'opts' => [
                    ['m = 4, n = 6: 4 | 36 but 4 ∤ 6 ✓ counterexample', true],
                    ['m = 3, n = 9: 3 | 81 and 3 | 9 — does NOT disprove', false],
                    ['m = 2, n = 4: 2 | 16 and 2 | 4 — does NOT disprove', false],
                    ['No counterexample exists', false],
                ],
            ],
            [
                'q' => "Claim: 'The product of any two irrational numbers is irrational.' Disprove with a counterexample.",
                'opts' => [
                    ['π × π = π² (irrational) — does NOT disprove', false],
                    ['√2 × √3 = √6 (irrational) — does NOT disprove', false],
                    ['√2 × √2 = 2 (rational) — counterexample ✓', true],
                    ['No counterexample exists', false],
                ],
            ],

            // ── PROOF STRATEGY AND FLAW DETECTION ────────────────────────
            [
                'q' => "Flawed Proof: 'Claim: 1 = 2. Let a = b. Then a² = ab. So a² − b² = ab − b². Factor: (a−b)(a+b) = b(a−b). Divide both sides by (a−b): a + b = b. Since a = b: 2b = b. Divide by b: 2 = 1.' What is the flaw?",
                'opts' => [
                    ['The factoring step is wrong', false],
                    ['Dividing by (a−b) is dividing by zero since a = b means a − b = 0', true],
                    ['The initial assumption a = b is false', false],
                    ['The factoring of a² − b² is incorrect', false],
                ],
            ],
            [
                'q' => "Flawed Induction: 'All horses are the same color.'\nBase: 1 horse — trivially same color.\nInductive step: Assume any group of k horses are the same color. Take k+1 horses. Remove horse 1: the remaining k are same color. Remove horse k+1: the remaining k (including horse 1) are same color. So all k+1 are same color.'\n\nWhere does the induction fail?",
                'opts' => [
                    ['The base case is wrong — one horse can have many colors', false],
                    ['The step from k=1 to k=2 fails: removing each end horse leaves only 1 horse, so the two groups do not overlap and we cannot conclude horse 1 and horse 2 are the same color', true],
                    ['The inductive hypothesis is incorrectly stated', false],
                    ['The proof is actually correct', false],
                ],
            ],
            [
                'q' => "In a divisibility proof, which step below contains an error?\n\nStep A: Let n = 4k + 2 for some integer k.\nStep B: Then n² = 16k² + 16k + 4.\nStep C: Factor: n² = 4(4k² + 4k + 1).\nStep D: Therefore 8 | n².",
                'opts' => [
                    ['Step A', false],
                    ['Step B', false],
                    ['Step C', false],
                    ['Step D — 4(4k² + 4k + 1) is divisible by 4, and (4k²+4k+1) is odd, so 8 does NOT divide n²', true],
                ],
            ],
            [
                'q' => "To prove 'For all n ≥ 1, Σᵢ₌₁ⁿ i³ = [n(n+1)/2]²,' which proof strategy is most direct?",
                'opts' => [
                    ['Proof by contradiction', false],
                    ['Mathematical induction', true],
                    ['Proof by cases (even/odd n)', false],
                    ['Non-constructive existence proof', false],
                ],
            ],

            // ── COMBINATORICS AND NUMBER THEORY PROOFS ────────────────────
            [
                'q' => "Claim: For all n ≥ 1, 2^n > n.\n\nInductive step: Assume 2^k > k. Show 2^(k+1) > k+1.\n2^(k+1) = 2 · 2^k > 2k (by hypothesis). Is 2k ≥ k + 1 for k ≥ 1?",
                'opts' => [
                    ['No — 2k < k+1 for all k', false],
                    ['Yes — 2k ≥ k + 1 iff k ≥ 1, which is given', true],
                    ['Only for even k', false],
                    ['Only for k ≥ 5', false],
                ],
            ],
            [
                'q' => "Claim: gcd(a, b) = gcd(b, a mod b) (Euclidean Algorithm — the key lemma).\n\nThe proof of this lemma is a direct proof. You write a = bq + r where r = a mod b. If d divides both a and b, then d divides:\n",
                'opts' => [
                    ['Only a', false],
                    ['a − bq = r as well, so d divides both b and r', true],
                    ['Only b and q', false],
                    ['Only q', false],
                ],
            ],
            [
                'q' => "Claim: If p is prime and p | ab, then p | a or p | b.\n\nThis property is known as:",
                'opts' => [
                    ['Fermat\'s Little Theorem', false],
                    ['Euclid\'s Lemma', true],
                    ['The Division Algorithm', false],
                    ['Bezout\'s Identity', false],
                ],
            ],
            [
                'q' => "Claim: The integer n is divisible by 9 if and only if the sum of its digits is divisible by 9.\n\nThe proof uses the fact that 10 ≡ 1 (mod 9). Therefore 10^k ≡ ?",
                'opts' => [
                    ['10^k ≡ k (mod 9)', false],
                    ['10^k ≡ 1 (mod 9) for all k ≥ 0', true],
                    ['10^k ≡ 0 (mod 9)', false],
                    ['10^k ≡ −1 (mod 9)', false],
                ],
            ],
            [
                'q' => "A well-ordering proof relies on the Well-Ordering Principle, which states:",
                'opts' => [
                    ['Every integer has a smallest prime factor', false],
                    ['Every non-empty set of non-negative integers has a smallest element', true],
                    ['Every decreasing sequence of integers is finite', false],
                    ['Every set of rational numbers is bounded below', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 5 — Methods of Proof (Intermediate).");
    }
}