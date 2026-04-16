<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module5ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 5 — Methods of Proof (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Methods of Proof',
            'description'           => 'Test your basic understanding of mathematical proof techniques — direct proof, contrapositive, contradiction, and more. Perfect for beginners just getting started with logic and proof writing.',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 5,
        ]);

        $this->command->info("Seeding 50 newbie-friendly questions on Methods of Proof...");

        $qaData = [

            // ── 5.1 INTRODUCTION TO MATHEMATICAL PROOF ───────────────────
            [
                'q' => 'What is a mathematical proof?',
                'opts' => [
                    ['A guess about whether a statement is true', false],
                    ['A logical argument that shows a statement must be true', true],
                    ['An example that shows a statement works once', false],
                    ['A definition of a mathematical term', false],
                ],
            ],
            [
                'q' => 'What is a "theorem" in mathematics?',
                'opts' => [
                    ['A statement that has not been proven yet', false],
                    ['A statement that has been proven to be true', true],
                    ['A statement that is always false', false],
                    ['An assumption we accept without proof', false],
                ],
            ],
            [
                'q' => 'What is an "axiom" (or postulate) in mathematics?',
                'opts' => [
                    ['A statement that must be proven before it can be used', false],
                    ['A statement accepted as true without proof, used as a starting point', true],
                    ['A statement that is sometimes true and sometimes false', false],
                    ['A statement that contradicts a theorem', false],
                ],
            ],
            [
                'q' => 'What does it mean for a mathematical statement to be a "proposition"?',
                'opts' => [
                    ['A statement that is neither true nor false', false],
                    ['A declarative statement that is either true or false', true],
                    ['A question asked in mathematics', false],
                    ['A proven theorem used to prove other theorems', false],
                ],
            ],
            [
                'q' => 'What is a "lemma" in mathematics?',
                'opts' => [
                    ['A major, important theorem', false],
                    ['A helper result proved in order to help prove a larger theorem', true],
                    ['An unproven conjecture', false],
                    ['A type of number system', false],
                ],
            ],

            // ── 5.2 DIRECT PROOF ─────────────────────────────────────────
            [
                'q' => 'What is a direct proof?',
                'opts' => [
                    ['A proof that assumes the conclusion is false', false],
                    ['A proof that starts from the hypothesis and logically reaches the conclusion', true],
                    ['A proof that uses a counterexample', false],
                    ['A proof that works backwards from the conclusion', false],
                ],
            ],
            [
                'q' => 'In a direct proof of "If P, then Q," where do you start?',
                'opts' => [
                    ['By assuming Q is false', false],
                    ['By assuming P is true', true],
                    ['By assuming both P and Q are false', false],
                    ['By assuming Q is true', false],
                ],
            ],
            [
                'q' => 'An integer n is called "even" if it can be written as:',
                'opts' => [
                    ['n = 2k + 1 for some integer k', false],
                    ['n = 2k for some integer k', true],
                    ['n = k² for some integer k', false],
                    ['n = k/2 for some integer k', false],
                ],
            ],
            [
                'q' => 'An integer n is called "odd" if it can be written as:',
                'opts' => [
                    ['n = 2k for some integer k', false],
                    ['n = 2k + 1 for some integer k', true],
                    ['n = k + 1 for some integer k', false],
                    ['n = 2k − 1 for some integer k', false],
                ],
            ],
            [
                'q' => 'To directly prove "If n is even, then n² is even," you would first write n = 2k and then compute n². What is n²?',
                'opts' => [
                    ['2k²', false],
                    ['4k²', true],
                    ['2k + 2', false],
                    ['k²', false],
                ],
            ],
            [
                'q' => 'If n = 2k, then n² = 4k² = 2(2k²). Why does this show n² is even?',
                'opts' => [
                    ['Because 4k² ends in an even digit', false],
                    ['Because n² is written as 2 times an integer, matching the definition of even', true],
                    ['Because k must be an even number too', false],
                    ['Because squaring always gives an even result', false],
                ],
            ],
            [
                'q' => 'Which of the following is the best first step in a direct proof of "If n is odd, then n + 1 is even"?',
                'opts' => [
                    ['Assume n + 1 is odd', false],
                    ['Write n = 2k + 1 for some integer k', true],
                    ['Write n = 2k for some integer k', false],
                    ['Assume the conclusion is false', false],
                ],
            ],

            // ── 5.3 PROOF BY CONTRAPOSITIVE ───────────────────────────────
            [
                'q' => 'What is the contrapositive of the statement "If P, then Q"?',
                'opts' => [
                    ['If Q, then P', false],
                    ['If not Q, then not P', true],
                    ['If not P, then not Q', false],
                    ['If P, then not Q', false],
                ],
            ],
            [
                'q' => 'Is the contrapositive of a true statement always true?',
                'opts' => [
                    ['No, they can have different truth values', false],
                    ['Yes, a statement and its contrapositive are logically equivalent', true],
                    ['Only if the original statement is about even numbers', false],
                    ['Only in some special cases', false],
                ],
            ],
            [
                'q' => 'What is the contrapositive of "If it is raining, then the ground is wet"?',
                'opts' => [
                    ['If the ground is wet, then it is raining', false],
                    ['If the ground is not wet, then it is not raining', true],
                    ['If it is not raining, then the ground is not wet', false],
                    ['If the ground is wet, then it is not raining', false],
                ],
            ],
            [
                'q' => 'Why would you choose a proof by contrapositive instead of a direct proof?',
                'opts' => [
                    ['Because contrapositive proofs are always shorter', false],
                    ['Because sometimes it is easier to prove "not Q → not P" than "P → Q" directly', true],
                    ['Because direct proofs are not valid in all cases', false],
                    ['Because the contrapositive gives a different result', false],
                ],
            ],
            [
                'q' => 'What is the contrapositive of "If n² is odd, then n is odd"?',
                'opts' => [
                    ['If n is odd, then n² is odd', false],
                    ['If n is not odd, then n² is not odd" — i.e., "If n is even, then n² is even"', true],
                    ['If n² is even, then n is odd', false],
                    ['If n is even, then n² is odd', false],
                ],
            ],

            // ── 5.4 PROOF BY CONTRADICTION ────────────────────────────────
            [
                'q' => 'What is the main idea of a proof by contradiction?',
                'opts' => [
                    ['Assume the statement is true and derive an obvious fact', false],
                    ['Assume the statement is false and derive a logical impossibility', true],
                    ['Show the statement works for one specific example', false],
                    ['Prove the contrapositive of the statement', false],
                ],
            ],
            [
                'q' => 'When you "derive a contradiction," what have you found?',
                'opts' => [
                    ['Two statements that are both true but unrelated', false],
                    ['A statement that is both true and false at the same time, which is impossible', true],
                    ['A counterexample to the original statement', false],
                    ['An alternative proof method', false],
                ],
            ],
            [
                'q' => 'In the classic proof that √2 is irrational, the first step is to assume:',
                'opts' => [
                    ['√2 = 0', false],
                    ['√2 is rational, i.e., √2 = p/q in lowest terms', true],
                    ['√2 is a whole number', false],
                    ['√2 is negative', false],
                ],
            ],
            [
                'q' => 'Which Latin phrase is often written at the end of a proof by contradiction to mark the moment a contradiction is found?',
                'opts' => [
                    ['Q.E.D.', false],
                    ['Reductio ad absurdum', true],
                    ['Ex falso quodlibet', false],
                    ['Modus ponens', false],
                ],
            ],
            [
                'q' => 'To prove "There is no largest even number" by contradiction, you would start by assuming:',
                'opts' => [
                    ['All even numbers are negative', false],
                    ['There exists a largest even number N', true],
                    ['Every even number is divisible by 4', false],
                    ['Even numbers do not exist', false],
                ],
            ],

            // ── 5.5 PROOF BY CASES (EXHAUSTION) ──────────────────────────
            [
                'q' => 'What is a proof by cases (exhaustion)?',
                'opts' => [
                    ['A proof that tests every possible input value individually', false],
                    ['A proof that splits all possibilities into cases and proves the statement holds in each case', true],
                    ['A proof that only checks the most likely case', false],
                    ['A proof by contradiction applied repeatedly', false],
                ],
            ],
            [
                'q' => 'To prove a statement about all integers using cases, a common split is:',
                'opts' => [
                    ['Case 1: n = 0, Case 2: n ≠ 0', false],
                    ['Case 1: n is even, Case 2: n is odd', true],
                    ['Case 1: n is positive, Case 2: n = 1', false],
                    ['Case 1: n < 100, Case 2: n ≥ 100', false],
                ],
            ],
            [
                'q' => 'Why must the cases in a proof by exhaustion cover ALL possibilities?',
                'opts' => [
                    ['To make the proof look more complete', false],
                    ['Because if any possibility is missed, the proof is incomplete and invalid', true],
                    ['Because extra cases add extra proof points', false],
                    ['Cases do not need to be exhaustive', false],
                ],
            ],
            [
                'q' => 'To prove "n² + n is always even for any integer n," which case split makes the most sense?',
                'opts' => [
                    ['Case 1: n > 0, Case 2: n ≤ 0', false],
                    ['Case 1: n is even, Case 2: n is odd', true],
                    ['Case 1: n = 1, Case 2: n = 2', false],
                    ['Case 1: n is prime, Case 2: n is composite', false],
                ],
            ],

            // ── 5.6 MATHEMATICAL INDUCTION ────────────────────────────────
            [
                'q' => 'Mathematical induction is used to prove statements about:',
                'opts' => [
                    ['Real numbers only', false],
                    ['All positive integers (or natural numbers)', true],
                    ['Irrational numbers only', false],
                    ['Statements that have no pattern', false],
                ],
            ],
            [
                'q' => 'What are the two main steps of a proof by mathematical induction?',
                'opts' => [
                    ['Guess and check', false],
                    ['Base case and inductive step', true],
                    ['Contradiction and resolution', false],
                    ['Direct step and contrapositive step', false],
                ],
            ],
            [
                'q' => 'In the base case of a proof by induction, what do you do?',
                'opts' => [
                    ['Assume the statement is true for n = k', false],
                    ['Prove the statement is true for the smallest value (usually n = 1)', true],
                    ['Show the statement fails for n = 0', false],
                    ['Prove the statement for all values at once', false],
                ],
            ],
            [
                'q' => 'In the inductive step, what is the "inductive hypothesis"?',
                'opts' => [
                    ['The statement we are trying to prove', false],
                    ['The assumption that the statement is true for some arbitrary n = k', true],
                    ['The base case result', false],
                    ['A guess that the statement might be true', false],
                ],
            ],
            [
                'q' => 'After assuming the statement is true for n = k (inductive hypothesis), what must you then prove?',
                'opts' => [
                    ['That the statement is true for n = k − 1', false],
                    ['That the statement is true for n = k + 1', true],
                    ['That k must be even', false],
                    ['That the base case holds again', false],
                ],
            ],
            [
                'q' => 'The "domino analogy" for induction says: if the first domino falls (base case) and each falling domino knocks over the next (inductive step), then:',
                'opts' => [
                    ['Only the first few dominos fall', false],
                    ['All dominos will eventually fall', true],
                    ['The last domino falls first', false],
                    ['No dominos fall at all', false],
                ],
            ],

            // ── 5.7 STRONG INDUCTION ──────────────────────────────────────
            [
                'q' => 'How does strong induction differ from regular (weak) induction?',
                'opts' => [
                    ['Strong induction only proves the base case', false],
                    ['In strong induction, you assume the statement is true for ALL values from the base case up to k (not just k alone)', true],
                    ['Strong induction skips the inductive step', false],
                    ['Strong induction is a weaker form of proof', false],
                ],
            ],
            [
                'q' => 'Strong induction is especially useful when proving statements where the next case (n = k + 1) depends on:',
                'opts' => [
                    ['Only n = k', false],
                    ['Multiple earlier values, not just n = k', true],
                    ['Only the base case', false],
                    ['Values larger than k + 1', false],
                ],
            ],
            [
                'q' => 'Which of the following is a classic example where strong induction is more convenient than weak induction?',
                'opts' => [
                    ['Proving 1 + 2 + ... + n = n(n+1)/2', false],
                    ['Proving every integer ≥ 2 has a prime factorization', true],
                    ['Proving the sum of the first n even numbers', false],
                    ['Proving n < 2n for all n ≥ 1', false],
                ],
            ],

            // ── 5.8 EXISTENCE AND UNIQUENESS PROOFS ───────────────────────
            [
                'q' => 'An existence proof shows that:',
                'opts' => [
                    ['A mathematical object does NOT exist', false],
                    ['At least one mathematical object with a given property exists', true],
                    ['A mathematical object is the only one of its kind', false],
                    ['A statement is true for all integers', false],
                ],
            ],
            [
                'q' => 'What is the simplest way to give a "constructive" existence proof?',
                'opts' => [
                    ['Show the object cannot exist', false],
                    ['Directly exhibit (build or name) a specific object that satisfies the required property', true],
                    ['Use contradiction to show existence', false],
                    ['Prove it by strong induction', false],
                ],
            ],
            [
                'q' => 'A uniqueness proof shows that:',
                'opts' => [
                    ['At least one solution exists', false],
                    ['The solution that exists is the only one with the given property', true],
                    ['No solution exists', false],
                    ['There are infinitely many solutions', false],
                ],
            ],
            [
                'q' => 'A standard technique to prove uniqueness is to assume there are two objects x and y that both satisfy the property, and then show:',
                'opts' => [
                    ['x and y are both even numbers', false],
                    ['x = y, meaning they must be the same object', true],
                    ['x and y cannot both exist', false],
                    ['x > y always', false],
                ],
            ],

            // ── 5.9 DISPROVING: COUNTEREXAMPLES ───────────────────────────
            [
                'q' => 'What is a counterexample?',
                'opts' => [
                    ['An example that proves a statement is true', false],
                    ['A specific example that shows a universal statement is false', true],
                    ['A type of proof by contradiction', false],
                    ['A proof technique for existence statements', false],
                ],
            ],
            [
                'q' => 'To disprove the statement "All prime numbers are odd," which counterexample works?',
                'opts' => [
                    ['3 (an odd prime)', false],
                    ['2 (an even prime)', true],
                    ['1 (not prime)', false],
                    ['4 (an even non-prime)', false],
                ],
            ],
            [
                'q' => 'How many counterexamples are needed to disprove a universal statement "For all n, P(n)"?',
                'opts' => [
                    ['At least ten', false],
                    ['Just one', true],
                    ['At least half of all possible cases', false],
                    ['Every possible case must fail', false],
                ],
            ],
            [
                'q' => 'To disprove "For all integers n, n² > n," which counterexample works?',
                'opts' => [
                    ['n = 5 (since 25 > 5)', false],
                    ['n = 1 (since 1² = 1, which is not greater than 1)', true],
                    ['n = −2 (since 4 > −2)', false],
                    ['n = 100 (since 10000 > 100)', false],
                ],
            ],

            // ── 5.10 CHOOSING THE RIGHT PROOF STRATEGY ────────────────────
            [
                'q' => 'If a statement says "There exists an integer n such that n + 5 = 12," the best proof strategy is:',
                'opts' => [
                    ['Proof by contradiction', false],
                    ['Existence proof — just find the specific n (n = 7)', true],
                    ['Mathematical induction', false],
                    ['Proof by cases', false],
                ],
            ],
            [
                'q' => 'If a statement says "For all integers n, if n² is even then n is even," a good proof strategy is:',
                'opts' => [
                    ['Find a counterexample', false],
                    ['Proof by contrapositive: assume n is odd, then show n² is odd', true],
                    ['Proof by exhaustion over all integers', false],
                    ['Existence proof', false],
                ],
            ],
            [
                'q' => 'If a statement says "For all n ≥ 1, 1 + 2 + ... + n = n(n+1)/2," the best strategy is:',
                'opts' => [
                    ['Direct proof using algebra alone', false],
                    ['Mathematical induction', true],
                    ['Proof by contradiction', false],
                    ['Provide one numerical example', false],
                ],
            ],
            [
                'q' => 'Which abbreviation is written at the end of a completed proof to signal it is finished?',
                'opts' => [
                    ['P.S.', false],
                    ['Q.E.D. (or a filled square ∎)', true],
                    ['N.B.', false],
                    ['i.e.', false],
                ],
            ],
            [
                'q' => 'To disprove a universal statement "For all n, P(n)," what is your goal?',
                'opts' => [
                    ['Prove P(n) is true for the majority of values', false],
                    ['Find at least one specific n where P(n) is false', true],
                    ['Prove P(n) is true for n = 1', false],
                    ['Show P(n) leads to a contradiction', false],
                ],
            ],
            [
                'q' => 'Which proof method is being used when you split the proof into "Case 1: n is even" and "Case 2: n is odd"?',
                'opts' => [
                    ['Proof by contradiction', false],
                    ['Proof by cases (exhaustion)', true],
                    ['Proof by contrapositive', false],
                    ['Mathematical induction', false],
                ],
            ],
            [
                'q' => 'What does "without loss of generality" (WLOG) mean in a proof?',
                'opts' => [
                    ['We are skipping an important case', false],
                    ['By symmetry, we can assume a particular condition and the argument covers all other cases too', true],
                    ['The proof only works for one specific case', false],
                    ['We are using the contrapositive', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 5 — Methods of Proof (Newbie).");
    }
}