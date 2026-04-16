<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module5ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 5 — Methods of Proof (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Methods of Proof',
            'description'           => 'Deepen your understanding of proof techniques through analytical reasoning, logical tracing, and structured argument construction. Questions require you to evaluate proof steps and spot logical gaps.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 1000,
            'order_index'           => 5,
        ]);

        $this->command->info("Seeding 50 university-level questions on Methods of Proof...");

        $qaData = [

            // ── 5.1 INTRODUCTION TO MATHEMATICAL PROOF ───────────────────
            [
                'q' => 'Which of the following best describes a "corollary"?',
                'opts' => [
                    ['A statement assumed to be true without proof', false],
                    ['A result that follows easily and directly from a theorem already proven', true],
                    ['A hypothesis that leads to a contradiction', false],
                    ['A proof technique involving cases', false],
                ],
            ],
            [
                'q' => 'What distinguishes a "conjecture" from a "theorem"?',
                'opts' => [
                    ['A conjecture is always false; a theorem is always true', false],
                    ['A conjecture is a statement believed to be true but not yet proven; a theorem has been proven', true],
                    ['A conjecture applies only to integers; a theorem applies to all numbers', false],
                    ['There is no difference between a conjecture and a theorem', false],
                ],
            ],
            [
                'q' => 'In logic, the form "If P, then Q" is called a:',
                'opts' => [
                    ['Biconditional', false],
                    ['Conditional statement (or implication)', true],
                    ['Contrapositive', false],
                    ['Tautology', false],
                ],
            ],
            [
                'q' => 'What is the converse of "If P, then Q"?',
                'opts' => [
                    ['If not P, then not Q', false],
                    ['If Q, then P', true],
                    ['If not Q, then not P', false],
                    ['If P, then not Q', false],
                ],
            ],
            [
                'q' => 'If "If P, then Q" is true, which related statement is also guaranteed to be true?',
                'opts' => [
                    ['The converse: If Q, then P', false],
                    ['The inverse: If not P, then not Q', false],
                    ['The contrapositive: If not Q, then not P', true],
                    ['None of the related statements', false],
                ],
            ],

            // ── 5.2 DIRECT PROOF ─────────────────────────────────────────
            [
                'q' => 'To directly prove "The sum of two even integers is even," you write m = 2a and n = 2b. What is m + n?',
                'opts' => [
                    ['2a + b', false],
                    ['2(a + b)', true],
                    ['4ab', false],
                    ['a + 2b', false],
                ],
            ],
            [
                'q' => 'After computing m + n = 2(a + b) in the above proof, why does this show m + n is even?',
                'opts' => [
                    ['Because a + b is always even', false],
                    ['Because m + n equals 2 times the integer (a + b), which matches the definition of even', true],
                    ['Because m and n were both positive', false],
                    ['Because we divided by 2 and got a whole number', false],
                ],
            ],
            [
                'q' => 'Prove directly: If n is odd, then n² is odd. If n = 2k + 1, what is n²?',
                'opts' => [
                    ['4k + 1', false],
                    ['4k² + 4k + 1', true],
                    ['2k² + 1', false],
                    ['4k² + 1', false],
                ],
            ],
            [
                'q' => 'Continuing: n² = 4k² + 4k + 1 = 2(2k² + 2k) + 1. Why does this show n² is odd?',
                'opts' => [
                    ['Because 2k² + 2k is always odd', false],
                    ['Because n² is written as 2(integer) + 1, matching the definition of odd', true],
                    ['Because we added 1 at the end', false],
                    ['Because k² is always a perfect square', false],
                ],
            ],
            [
                'q' => 'In a direct proof that "the product of two odd numbers is odd," you write m = 2a + 1 and n = 2b + 1. What is mn?',
                'opts' => [
                    ['2ab + 1', false],
                    ['4ab + 2a + 2b + 1', true],
                    ['2(a + b) + 1', false],
                    ['4ab + 1', false],
                ],
            ],

            // ── 5.3 PROOF BY CONTRAPOSITIVE ───────────────────────────────
            [
                'q' => 'To prove "If n² is even, then n is even" by contrapositive, you instead prove:',
                'opts' => [
                    ['If n² is odd, then n is even', false],
                    ['If n is odd, then n² is odd', true],
                    ['If n is even, then n² is odd', false],
                    ['If n is odd, then n² is even', false],
                ],
            ],
            [
                'q' => 'In that contrapositive proof you assume n is odd, so n = 2k + 1. What is n²?',
                'opts' => [
                    ['4k + 1', false],
                    ['4k² + 4k + 1', true],
                    ['2k² + 2k', false],
                    ['2k + 1', false],
                ],
            ],
            [
                'q' => 'n² = 4k² + 4k + 1 = 2(2k² + 2k) + 1. This is odd. What have you proven by contrapositive?',
                'opts' => [
                    ['If n is even, then n² is even', false],
                    ['If n² is even, then n is even', true],
                    ['If n is odd, then n² is odd only sometimes', false],
                    ['If n² is odd, then n is even', false],
                ],
            ],
            [
                'q' => 'Which of the following is the correct contrapositive of "If a · b = 0, then a = 0 or b = 0"?',
                'opts' => [
                    ['If a ≠ 0 or b ≠ 0, then a · b = 0', false],
                    ['If a ≠ 0 and b ≠ 0, then a · b ≠ 0', true],
                    ['If a · b ≠ 0, then a = 0 or b = 0', false],
                    ['If a = 0 and b = 0, then a · b = 0', false],
                ],
            ],

            // ── 5.4 PROOF BY CONTRADICTION ────────────────────────────────
            [
                'q' => 'In the proof that √2 is irrational, after assuming √2 = p/q in lowest terms and squaring both sides, you get 2q² = p². What does this tell you about p?',
                'opts' => [
                    ['p is odd', false],
                    ['p is even (since p² is even)', true],
                    ['p must equal q', false],
                    ['p is irrational', false],
                ],
            ],
            [
                'q' => 'Continuing: since p is even, write p = 2m. Substituting into 2q² = p² gives 2q² = 4m², so q² = 2m². What does this tell you about q?',
                'opts' => [
                    ['q is odd', false],
                    ['q is even', true],
                    ['q = m', false],
                    ['q is prime', false],
                ],
            ],
            [
                'q' => 'Both p and q are even contradicts the assumption that p/q is in lowest terms. Therefore:',
                'opts' => [
                    ['√2 must be rational after all', false],
                    ['Our assumption that √2 is rational must be false, so √2 is irrational', true],
                    ['We need to check more cases', false],
                    ['p and q cannot both be integers', false],
                ],
            ],
            [
                'q' => 'To prove "There are infinitely many prime numbers" by contradiction, you assume:',
                'opts' => [
                    ['Every prime number is odd', false],
                    ['There are only finitely many primes, say p₁, p₂, ..., pₙ', true],
                    ['The number 1 is prime', false],
                    ['The product of all primes equals 1', false],
                ],
            ],
            [
                'q' => 'In the primes proof, you form N = (p₁ · p₂ · ... · pₙ) + 1. Why is this a contradiction?',
                'opts' => [
                    ['N is divisible by every prime on the list, which is impossible', false],
                    ['N is either prime itself (not on the list) or has a prime factor not on the list — both contradict the list being complete', true],
                    ['N is always equal to 1', false],
                    ['N must be negative', false],
                ],
            ],

            // ── 5.5 PROOF BY CASES ────────────────────────────────────────
            [
                'q' => 'To prove "n² + n is even for all integers n" by cases:\n\nCase 1 (n even): n = 2k → n² + n = 4k² + 2k = 2(2k² + k). What can you conclude?',
                'opts' => [
                    ['n² + n is odd', false],
                    ['n² + n is even', true],
                    ['n² + n = 0', false],
                    ['n must be zero', false],
                ],
            ],
            [
                'q' => 'Case 2 (n odd): n = 2k + 1 → n² + n = (4k² + 4k + 1) + (2k + 1) = 4k² + 6k + 2 = 2(2k² + 3k + 1). What can you conclude for this case?',
                'opts' => [
                    ['n² + n is odd in this case', false],
                    ['n² + n is even in this case too', true],
                    ['The expression is undefined', false],
                    ['k must be negative', false],
                ],
            ],
            [
                'q' => 'Since n² + n is even in both cases (n even and n odd), and every integer falls into one of these two cases, what is the final conclusion?',
                'opts' => [
                    ['n² + n is even only for positive n', false],
                    ['n² + n is even for ALL integers n', true],
                    ['n² + n is sometimes odd', false],
                    ['We need more cases to be sure', false],
                ],
            ],
            [
                'q' => 'For proving a statement about |x| (absolute value), a natural case split is:',
                'opts' => [
                    ['Case 1: x is even, Case 2: x is odd', false],
                    ['Case 1: x ≥ 0, Case 2: x < 0', true],
                    ['Case 1: x = 0, Case 2: x ≠ 0', false],
                    ['Case 1: x is rational, Case 2: x is irrational', false],
                ],
            ],

            // ── 5.6 MATHEMATICAL INDUCTION ────────────────────────────────
            [
                'q' => 'Prove by induction: 1 + 2 + ... + n = n(n+1)/2.\n\nBase case (n = 1): LHS = 1. What is the RHS when n = 1?',
                'opts' => [
                    ['1/2', false],
                    ['1(2)/2 = 1', true],
                    ['2', false],
                    ['0', false],
                ],
            ],
            [
                'q' => 'For the inductive step, you assume 1 + 2 + ... + k = k(k+1)/2. You must show the formula holds for n = k + 1. What is the LHS for n = k + 1?',
                'opts' => [
                    ['1 + 2 + ... + k', false],
                    ['1 + 2 + ... + k + (k + 1)', true],
                    ['(k + 1)(k + 2)/2', false],
                    ['k(k + 1)/2 + k', false],
                ],
            ],
            [
                'q' => 'Using the inductive hypothesis, 1 + 2 + ... + k + (k+1) = k(k+1)/2 + (k+1). Simplify this expression.',
                'opts' => [
                    ['(k+1)(k+2)/2', true],
                    ['k(k+1)/2', false],
                    ['(k+1)²/2', false],
                    ['k²/2 + k + 1', false],
                ],
            ],
            [
                'q' => 'The result k(k+1)/2 + (k+1) = (k+1)(k+2)/2 matches the formula n(n+1)/2 evaluated at n = k+1. The inductive step is therefore:',
                'opts' => [
                    ['Incomplete — we need another case', false],
                    ['Complete — the formula holds for n = k+1 assuming it holds for n = k', true],
                    ['Incorrect — we should substitute n = k − 1', false],
                    ['A circular argument', false],
                ],
            ],
            [
                'q' => 'For the induction proof that 2^n > n for all n ≥ 1, the base case checks n = 1. Which of the following is true for n = 1?',
                'opts' => [
                    ['2¹ = 2 > 1 ✓', true],
                    ['2¹ = 1 = 1, so it fails', false],
                    ['2¹ = 2 < 1, so it fails', false],
                    ['2¹ = 0 < 1, so it fails', false],
                ],
            ],

            // ── 5.7 STRONG INDUCTION ──────────────────────────────────────
            [
                'q' => 'To prove by strong induction that every integer n ≥ 2 can be written as a product of primes, the inductive hypothesis assumes:',
                'opts' => [
                    ['n = k can be written as a product of primes', false],
                    ['Every integer from 2 to k can be written as a product of primes', true],
                    ['Only even integers up to k are products of primes', false],
                    ['k is itself a prime', false],
                ],
            ],
            [
                'q' => 'In the strong induction proof of prime factorization, if k + 1 is prime, it is already a product of primes. If k + 1 is NOT prime (composite), then k + 1 = a · b where 2 ≤ a, b < k + 1. Why can you apply the inductive hypothesis to a and b?',
                'opts' => [
                    ['Because a and b are even', false],
                    ['Because a and b are both in the range [2, k], so the inductive hypothesis guarantees they have prime factorizations', true],
                    ['Because a · b = k + 1 is always prime', false],
                    ['Because a and b were chosen randomly', false],
                ],
            ],
            [
                'q' => 'For proving facts about the Fibonacci sequence (F(n) = F(n−1) + F(n−2)), strong induction is preferred over weak induction because:',
                'opts' => [
                    ['The Fibonacci sequence has no base case', false],
                    ['Each term depends on TWO previous terms, so you need to assume truth for both n = k and n = k−1', true],
                    ['Weak induction does not apply to sequences', false],
                    ['Strong induction requires only one base case regardless of the recurrence', false],
                ],
            ],

            // ── 5.8 EXISTENCE AND UNIQUENESS PROOFS ───────────────────────
            [
                'q' => 'To prove "There exists an integer n such that n² − n − 6 = 0," the most direct approach is:',
                'opts' => [
                    ['Assume no such n exists and derive a contradiction', false],
                    ['Factor or solve the equation to exhibit a specific solution: n = 3 works since 9 − 3 − 6 = 0', true],
                    ['Use mathematical induction', false],
                    ['Prove by contrapositive', false],
                ],
            ],
            [
                'q' => 'A non-constructive existence proof shows something exists:',
                'opts' => [
                    ['By naming the specific object', false],
                    ['Without necessarily identifying the specific object, often by contradiction or a counting argument', true],
                    ['Only when the object is a prime number', false],
                    ['By strong induction', false],
                ],
            ],
            [
                'q' => 'To prove uniqueness of the additive identity (0) in the integers: assume both 0 and 0\' satisfy "n + 0 = n for all n." Then:',
                'opts' => [
                    ['0 = 0 + 0\' = 0\', so they are equal', true],
                    ['0 and 0\' can be different values', false],
                    ['We need to check every integer n separately', false],
                    ['Uniqueness cannot be proven for the additive identity', false],
                ],
            ],

            // ── 5.9 DISPROVING: COUNTEREXAMPLES ───────────────────────────
            [
                'q' => 'To disprove "For all integers n ≥ 1, n² − n + 41 is prime," you need to find one n where it fails. What happens at n = 41?',
                'opts' => [
                    ['41² − 41 + 41 = 41² = 1681 = 41 × 41, which is NOT prime', true],
                    ['41² − 41 + 41 = 41, which is prime', false],
                    ['The expression equals 0', false],
                    ['The expression is undefined', false],
                ],
            ],
            [
                'q' => 'To disprove "For all real x, if x² > 0 then x > 0," find a counterexample:',
                'opts' => [
                    ['x = 2 (since 4 > 0 and 2 > 0)', false],
                    ['x = −3 (since 9 > 0 but −3 < 0, not > 0)', true],
                    ['x = 0 (since 0 > 0 fails the hypothesis)', false],
                    ['No counterexample exists; the statement is true', false],
                ],
            ],
            [
                'q' => 'A student claims "The sum of any two prime numbers is even." Disprove this claim:',
                'opts' => [
                    ['3 + 5 = 8, which is even — supports the claim', false],
                    ['2 + 3 = 5, which is odd — counterexample disproves the claim', true],
                    ['7 + 11 = 18, which is even — supports the claim', false],
                    ['The claim is actually true', false],
                ],
            ],

            // ── 5.10 CHOOSING THE RIGHT PROOF STRATEGY ────────────────────
            [
                'q' => 'You want to prove "For all integers n, if 3 ∤ n² then 3 ∤ n" (where ∤ means "does not divide"). Which strategy is most efficient?',
                'opts' => [
                    ['Proof by cases over n mod 3', false],
                    ['Proof by contrapositive: assume 3 | n, then show 3 | n²', true],
                    ['Mathematical induction on n', false],
                    ['Existence proof', false],
                ],
            ],
            [
                'q' => 'A proof is called "elegant" when it:',
                'opts' => [
                    ['Uses the most complex mathematics available', false],
                    ['Is clear, concise, and achieves the result with minimal unnecessary steps', true],
                    ['Contains the most cases', false],
                    ['Relies only on contradiction', false],
                ],
            ],
            [
                'q' => 'What is the key difference between a "proof" and a "verification by example"?',
                'opts' => [
                    ['They are the same thing in mathematics', false],
                    ['A proof works for ALL cases covered by the statement; a numerical example only checks one specific case', true],
                    ['A verification is more rigorous than a proof', false],
                    ['A proof only works for small numbers', false],
                ],
            ],
            [
                'q' => 'Which of the following correctly identifies when proof by induction should NOT be your first choice?',
                'opts' => [
                    ['When the statement involves all positive integers', false],
                    ['When the statement is an existence claim like "there exists an x such that P(x)"', true],
                    ['When proving a summation formula', false],
                    ['When the base case is n = 1', false],
                ],
            ],
            [
                'q' => 'If you are asked to prove "P if and only if Q" (a biconditional), how many directions do you need to prove?',
                'opts' => [
                    ['Just one direction: P → Q', false],
                    ['Both directions: P → Q and Q → P', true],
                    ['Three directions to be rigorous', false],
                    ['Zero — biconditionals are always obvious', false],
                ],
            ],

            // ── MIXED ANALYTICAL QUESTIONS ────────────────────────────────
            [
                'q' => 'A student writes: "Claim: For all n, n² ≥ n. Proof: 4² = 16 ≥ 4. QED." What is wrong with this proof?',
                'opts' => [
                    ['The arithmetic is wrong', false],
                    ['One example does not prove a universal statement — it only verifies one case', true],
                    ['The claim is false, so no proof exists', false],
                    ['QED should not be written at the end', false],
                ],
            ],
            [
                'q' => 'The claim "n² ≥ n for all integers n" is actually false. Which counterexample shows this?',
                'opts' => [
                    ['n = 5 (since 25 ≥ 5)', false],
                    ['n = −1 (since 1 ≥ −1, actually TRUE — not a counterexample)', false],
                    ['n = 1/2 — wait, this restricts to integers. Try n = 0: 0² = 0 ≥ 0 (TRUE). Try n = −1: 1 ≥ −1 (TRUE). The claim is TRUE for all integers.', false],
                    ['Actually n² ≥ n holds for all integers; no counterexample exists among integers', true],
                ],
            ],
            [
                'q' => 'In a proof by contradiction, after assuming ¬P and deriving a contradiction C ∧ ¬C, you conclude:',
                'opts' => [
                    ['C is false', false],
                    ['¬P must have been false, so P is true', true],
                    ['P is false', false],
                    ['The proof is circular', false],
                ],
            ],
            [
                'q' => 'Modus Ponens is the inference rule: Given P → Q and P is true, conclude:',
                'opts' => [
                    ['P is false', false],
                    ['Q is true', true],
                    ['Q is false', false],
                    ['P → Q is false', false],
                ],
            ],
            [
                'q' => 'Modus Tollens is the inference rule: Given P → Q and Q is false, conclude:',
                'opts' => [
                    ['P is true', false],
                    ['P is false', true],
                    ['Q is true', false],
                    ['P → Q is false', false],
                ],
            ],
            [
                'q' => 'A proof that says "n is divisible by 6" is complete when you show:',
                'opts' => [
                    ['n is divisible by 2 alone', false],
                    ['n is divisible by both 2 and 3 (since 6 = 2 × 3 and gcd(2,3) = 1)', true],
                    ['n is divisible by 12', false],
                    ['n ends in 0 or 6', false],
                ],
            ],
            [
                'q' => 'The logical structure of a proof by contrapositive is identical to:',
                'opts' => [
                    ['A proof by cases', false],
                    ['A direct proof of the contrapositive statement', true],
                    ['A proof by contradiction of the original statement', false],
                    ['Mathematical induction starting from n = 0', false],
                ],
            ],
            [
                'q' => 'Which of the following is a valid step in a direct proof that "the square of any rational number is rational"?',
                'opts' => [
                    ['Let r be irrational and show r² is irrational', false],
                    ['Let r = p/q where p, q are integers and q ≠ 0; then r² = p²/q², and p², q² are integers with q² ≠ 0', true],
                    ['Assume r² is irrational and derive a contradiction', false],
                    ['Use induction on the numerator p', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 5 — Methods of Proof (University Student).");
    }
}