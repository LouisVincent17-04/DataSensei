<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module5ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 5 — Methods of Proof (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Methods of Proof',
            'description'           => 'Professional-level proof challenges grounded in real-world software correctness, cryptographic security arguments, formal verification, and performance-critical system design. Questions demand mastery of advanced proof construction, edge-case reasoning, and algorithmic invariants.',
            'time_limit_seconds'    => 2400,
            'base_xp'               => 3000,
            'order_index'           => 5,
        ]);

        $this->command->info("Seeding 50 professional-level questions on Methods of Proof...");

        $qaData = [

            // ── FORMAL CORRECTNESS AND LOOP INVARIANTS ────────────────────
            [
                'q' => "The following implements in-place array reversal:\n\ndef reverse(arr):\n    lo, hi = 0, len(arr) - 1\n    while lo < hi:\n        arr[lo], arr[hi] = arr[hi], arr[lo]\n        lo += 1\n        hi -= 1\n\nThe loop invariant is: 'arr[0..lo-1] and arr[hi+1..n-1] contain the final reversed values; arr[lo..hi] is yet to be processed.'\n\nTo formally prove this invariant is maintained, which proof technique applies at each loop iteration?",
                'opts' => [
                    ['Proof by contradiction on the swap step', false],
                    ['Direct proof: show the invariant holds after the swap and pointer updates, given it held at the start of the iteration', true],
                    ['Strong induction on the total number of swaps', false],
                    ['Proof by cases on whether n is odd or even', false],
                ],
            ],
            [
                'q' => "Hoare Triple notation {P} C {Q} states that:\n\nIf the precondition P holds before command C executes, and C terminates, then the postcondition Q holds after.\n\nFor the assignment x := x + 1 with postcondition Q ≡ (x = 5), what is the weakest precondition P?",
                'opts' => [
                    ['x = 5', false],
                    ['x = 4', true],
                    ['x ≥ 0', false],
                    ['x = 6', false],
                ],
            ],
            [
                'q' => "Consider partial correctness vs. total correctness in formal verification:\n\ndef collatz(n):\n    while n != 1:\n        n = n // 2 if n % 2 == 0 else 3 * n + 1\n    return n\n\nWe can prove {n > 0} collatz(n) {result = 1} is partially correct (if it terminates, result = 1). Why can we NOT currently prove total correctness?",
                'opts' => [
                    ['Because the function contains a division by 2 which may cause float errors', false],
                    ['Because it is an open problem whether the Collatz sequence reaches 1 for all positive integers — termination is unproven', true],
                    ['Because Hoare logic cannot handle while loops', false],
                    ['Because n = 1 is not a valid input', false],
                ],
            ],
            [
                'q' => "The following segment sorts a 3-element array:\n\nif arr[0] > arr[1]: arr[0], arr[1] = arr[1], arr[0]\nif arr[1] > arr[2]: arr[1], arr[2] = arr[2], arr[1]\nif arr[0] > arr[1]: arr[0], arr[1] = arr[1], arr[0]\n\nA complete correctness proof must show this works for ALL orderings of 3 distinct elements. How many cases must be exhausted?",
                'opts' => [
                    ['3 cases', false],
                    ['6 cases (3! permutations)', true],
                    ['2 cases (sorted and reversed)', false],
                    ['9 cases', false],
                ],
            ],

            // ── CRYPTOGRAPHIC PROOF ARGUMENTS ─────────────────────────────
            [
                'q' => "RSA correctness relies on the proof that (m^e)^d ≡ m (mod n), where n = pq, ed ≡ 1 (mod (p-1)(q-1)), and 0 < m < n.\n\nThe proof uses Fermat's Little Theorem (or its generalization via Euler's theorem). The key claim is m^(ed) ≡ m^(1 + k(p-1)(q-1)) for some integer k. Applying Fermat's theorem to the prime p gives m^(p-1) ≡ ? (mod p) when gcd(m, p) = 1.",
                'opts' => [
                    ['m (mod p)', false],
                    ['1 (mod p)', true],
                    ['0 (mod p)', false],
                    ['p − 1 (mod p)', false],
                ],
            ],
            [
                'q' => "In a security proof for a symmetric cipher, the proof structure typically follows:\n\n'If an adversary A can break the cipher with advantage ε, then we can construct a reduction B that breaks [harder problem] with related advantage.'\n\nWhat proof technique is this?",
                'opts' => [
                    ['Mathematical induction on the number of cipher rounds', false],
                    ['Proof by contrapositive (or reduction): if the cipher is insecure, the hard problem is solvable — contradicting its hardness assumption', true],
                    ['Direct proof of cipher security', false],
                    ['Existence proof for the adversary', false],
                ],
            ],
            [
                'q' => "In a zero-knowledge proof protocol, the prover convinces the verifier that they know a secret without revealing it. The correctness proof involves three properties. Which of the following is NOT one of them?",
                'opts' => [
                    ['Completeness: an honest prover always convinces an honest verifier', false],
                    ['Soundness: a cheating prover cannot convince the verifier without knowing the secret', false],
                    ['Efficiency: the proof runs in O(log n) time', true],
                    ['Zero-knowledge: the verifier learns nothing beyond the validity of the statement', false],
                ],
            ],
            [
                'q' => "The discrete logarithm problem: given g, h, p (prime), find x such that g^x ≡ h (mod p).\n\nEl-Gamal encryption's security is argued by reduction. A proof shows: 'If you can decrypt El-Gamal ciphertexts efficiently, you can solve the Diffie-Hellman problem efficiently.' This is an example of:",
                'opts' => [
                    ['A constructive existence proof of a decryption algorithm', false],
                    ['A polynomial-time reduction — showing El-Gamal security is at least as hard as the computational Diffie-Hellman problem', true],
                    ['An inductive proof on the size of the prime p', false],
                    ['A direct proof that no adversary can decrypt', false],
                ],
            ],

            // ── COMPLEXITY AND DECIDABILITY PROOFS ────────────────────────
            [
                'q' => "The Halting Problem proof (Turing, 1936): Assume a program H(P, I) correctly decides whether program P halts on input I. Construct D(P): if H(P,P) = 'halts' then loop forever, else halt.\n\nWhat happens when you run D(D)?",
                'opts' => [
                    ['D(D) always loops forever regardless of H', false],
                    ['D(D) halts if and only if D(D) does not halt — a contradiction, disproving H\'s existence', true],
                    ['D(D) crashes due to a stack overflow', false],
                    ['D(D) correctly decides the halting problem for itself', false],
                ],
            ],
            [
                'q' => "A problem X is NP-hard if every problem in NP reduces to X in polynomial time. To prove a new problem Y is NP-hard, the standard technique is:",
                'opts' => [
                    ['Show Y can be solved in polynomial time', false],
                    ['Find a polynomial-time reduction from a known NP-hard problem to Y', true],
                    ['Prove Y is in NP by exhibiting a polynomial-time verifier', false],
                    ['Show Y cannot be solved by a deterministic Turing machine', false],
                ],
            ],
            [
                'q' => "The proof that SAT is NP-complete (Cook-Levin theorem) proceeds by:\n\n1. Showing SAT is in NP (a satisfying assignment can be verified in polynomial time).\n2. Showing every NP problem reduces to SAT in polynomial time.\n\nStep 2 uses which proof technique?",
                'opts' => [
                    ['Direct proof by constructing a polynomial-time algorithm for every NP problem', false],
                    ['A direct construction: for any NP problem (solved by nondeterministic TM M on input x), build a propositional formula that is satisfiable iff M accepts x', true],
                    ['Proof by contradiction — assume SAT is not NP-hard', false],
                    ['Mathematical induction on the size of NP problems', false],
                ],
            ],
            [
                'q' => "Rice's Theorem states: For any non-trivial semantic property P of programs (one that is not true of ALL programs nor of NO programs), the problem 'Does program M have property P?' is undecidable.\n\nThe proof reduces the Halting Problem to P using which structure?",
                'opts' => [
                    ['Strong induction on the size of M', false],
                    ['A many-one reduction: given a Halting Problem instance (M, I), construct a program M\' whose behavior on inputs encodes whether M halts on I', true],
                    ['A proof by cases on all possible properties P', false],
                    ['A non-constructive existence argument', false],
                ],
            ],

            // ── AMORTIZED ANALYSIS AND INVARIANT PROOFS ───────────────────
            [
                'q' => "Amortized analysis of a dynamic array (Python list append): Each append is O(1) amortized even though occasional resizes are O(n).\n\nThe proof uses a potential function Φ = 2·(size) − capacity. After a resize that doubles capacity from n to 2n, the new Φ = 2n − 2n = 0. Before the resize, Φ = 2n − n = n, meaning n 'saved up' operations covered the O(n) resize.\n\nThis argument is a form of:",
                'opts' => [
                    ['Proof by contradiction on the resize cost', false],
                    ['Direct proof using an accounting/potential method — the amortized cost is actual cost plus ΔΦ, shown to be O(1) on average', true],
                    ['Mathematical induction on the number of appends', false],
                    ['Proof by cases on whether capacity is prime', false],
                ],
            ],
            [
                'q' => "Consider Union-Find with path compression and union by rank. The amortized cost per operation is O(α(n)) where α is the inverse Ackermann function.\n\nThe correctness proof that path compression maintains correctness (i.e., still correctly reports connected components) relies on:",
                'opts' => [
                    ['Proof by contradiction that two separate components could be merged', false],
                    ['An invariant proof: path compression only redirects pointers to ancestors already in the same component — the representative root is unchanged', true],
                    ['Mathematical induction on the height of the tree before compression', false],
                    ['A case analysis on all possible tree shapes', false],
                ],
            ],

            // ── REAL-WORLD SYSTEM DESIGN AND PROOF ────────────────────────
            [
                'q' => "In distributed systems, the CAP theorem states: A distributed system cannot simultaneously guarantee Consistency, Availability, and Partition tolerance.\n\nThe proof of CAP is by contradiction: assume a system guarantees all three. During a network partition, a write to node A cannot reach node B. If the system is available, node B must respond (possibly stale). If it responds with stale data, it violates Consistency. Therefore:\n",
                'opts' => [
                    ['Consistency and availability can coexist during partitions', false],
                    ['In the presence of a partition, a designer must sacrifice either Consistency or Availability', true],
                    ['Partition tolerance is always optional', false],
                    ['The CAP theorem only applies to SQL databases', false],
                ],
            ],
            [
                'q' => "Byzantine Fault Tolerance (BFT): A system of n nodes can tolerate at most f Byzantine (arbitrarily malicious) nodes if n ≥ 3f + 1.\n\nThe proof that n < 3f + 1 is insufficient uses a contradiction argument involving which scenario?",
                'opts' => [
                    ['Two groups of f faulty nodes cancel each other out', false],
                    ['With n = 3f, it is possible to partition nodes into three groups of f each, where one is faulty and the two correct groups cannot distinguish which of the other two is faulty — consensus is impossible', true],
                    ['f faulty nodes can always impersonate correct nodes when n < 3f', false],
                    ['The network must be a complete graph for BFT to work', false],
                ],
            ],
            [
                'q' => "Claim: The following cache invalidation strategy is correct: 'On write to key k, delete k from all caches.'\n\nA formal correctness proof uses the invariant: 'At any point, if key k is in a cache, the cached value equals the current database value.'\n\nWhich event can VIOLATE this invariant if not handled carefully?",
                'opts' => [
                    ['Reading a key that is not in the cache', false],
                    ['A race condition where a stale read populates the cache AFTER a write-triggered deletion', true],
                    ['Deleting a key that was already absent from the cache', false],
                    ['Writing the same value as the current database value', false],
                ],
            ],
            [
                'q' => "In a concurrent system, a mutex (mutual exclusion lock) proof requires showing:\n\n1. Safety: At most one thread holds the lock at any time.\n2. Liveness: Every thread that requests the lock eventually acquires it.\n\nProving safety is typically done by:",
                'opts' => [
                    ['Mathematical induction on the number of threads', false],
                    ['An invariant proof: the lock state transitions are atomic and the invariant count ≤ 1 is maintained at every step', true],
                    ['Proof by contradiction that two threads simultaneously hold the lock', false],
                    ['Proof by cases on the number of processors', false],
                ],
            ],

            // ── ADVANCED INDUCTION AND STRUCTURAL PROOFS ──────────────────
            [
                'q' => "Structural induction proves properties of recursively defined data structures (e.g., trees, lists).\n\nTo prove 'For all binary trees T, the number of leaves is one more than the number of internal nodes,' the inductive step for a tree with root r and subtrees Tₗ and Tᵣ proceeds:\n\nBy structural inductive hypothesis, leaves(Tₗ) = internal(Tₗ) + 1 and leaves(Tᵣ) = internal(Tᵣ) + 1. Then leaves(T) = leaves(Tₗ) + leaves(Tᵣ) and internal(T) = internal(Tₗ) + internal(Tᵣ) + 1. Therefore leaves(T) = ?",
                'opts' => [
                    ['internal(T)', false],
                    ['internal(T) + 1', true],
                    ['internal(T) + 2', false],
                    ['2 · internal(T)', false],
                ],
            ],
            [
                'q' => "To prove type safety ('Well-typed programs do not go wrong') in programming language theory, the standard approach uses two lemmas proven by structural induction on the type derivation:\n\n1. Progress: A well-typed term is either a value or can take a step.\n2. Preservation: If a well-typed term takes a step, the result is also well-typed.\n\nTogether, these prove by induction on the number of steps that:",
                'opts' => [
                    ['All programs terminate', false],
                    ['Any well-typed program either terminates as a value or runs forever — but never reaches a stuck/undefined state', true],
                    ['No well-typed program can have runtime errors', false],
                    ['Well-typed programs always produce the same output', false],
                ],
            ],
            [
                'q' => "In lambda calculus, the Church-Rosser theorem (confluence) states: if term M can be reduced to N₁ and N₂, then there exists N₃ such that N₁ and N₂ both reduce to N₃.\n\nThe proof of this theorem is done by induction on reduction sequences. A key consequence is:\n\nIf a term has a normal form (fully reduced), that normal form is:",
                'opts' => [
                    ['One of many possible normal forms', false],
                    ['Unique — no term has two different normal forms', true],
                    ['Always a variable', false],
                    ['Reachable only by the leftmost-outermost reduction strategy', false],
                ],
            ],

            // ── EDGE CASES, ROBUSTNESS, REAL-WORLD FAILURES ───────────────
            [
                'q' => "The following Python integer square root claims to be proven correct:\n\ndef isqrt(n):\n    if n < 0:\n        raise ValueError\n    x = int(n ** 0.5)\n    # Adjust for floating point error\n    while x * x > n:\n        x -= 1\n    while (x + 1) * (x + 1) <= n:\n        x += 1\n    return x\n\nThe postcondition is: x² ≤ n < (x+1)².\n\nWhich part of this code specifically handles a known edge-case failure mode in the naive `int(n**0.5)` approach?",
                'opts' => [
                    ['The ValueError for negative inputs', false],
                    ['The two while loops that correct floating-point rounding errors in the initial estimate', true],
                    ['The use of int() to truncate the float', false],
                    ['The condition n < 0 check', false],
                ],
            ],
            [
                'q' => "In integer overflow proofs for embedded systems (C/C++), a critical correctness claim is:\n\n'The expression (a + b) / 2 computes the average of a and b correctly for non-negative integers a and b.'\n\nThis claim is FALSE in practice. A correct counterexample and fix are:",
                'opts' => [
                    ['a = 0, b = 0 causes a divide-by-zero; fix: check b ≠ 0 first', false],
                    ['If a and b are both close to INT_MAX, a+b overflows; correct form is a + (b-a)/2', true],
                    ['The result should be (a + b) // 2 using floor division instead', false],
                    ['The claim is actually correct — integer overflow is handled automatically', false],
                ],
            ],
            [
                'q' => "A proof of absence of SQL injection in a web application would formally show:\n\n'For all user-supplied strings s, the query construction function f(s) produces a string that the database parser cannot interpret as executable SQL beyond the intended query template.'\n\nWhich proof method is most appropriate for this kind of security argument?",
                'opts' => [
                    ['Mathematical induction on the length of s', false],
                    ['A direct proof using a formal grammar / regular language argument: show that parameterized queries separate data from syntax so no s can alter the parse tree', true],
                    ['Proof by contradiction: assume injection occurs and show it leads to a database error', false],
                    ['Proof by exhaustion: test all strings of length ≤ 100', false],
                ],
            ],
            [
                'q' => "In the proof of the master theorem for recurrences T(n) = aT(n/b) + f(n), Case 2 applies when f(n) = Θ(n^(log_b(a)) · log^k(n)) for k ≥ 0. The conclusion is T(n) = Θ(n^(log_b(a)) · log^(k+1)(n)).\n\nThe proof technique used to derive this closed form from the recurrence is:",
                'opts' => [
                    ['Proof by contradiction', false],
                    ['Iterative substitution (unrolling the recurrence) combined with geometric series summation', true],
                    ['Strong induction on n restricted to powers of b', false],
                    ['A non-constructive existence argument', false],
                ],
            ],

            // ── PROOF COMPOSITION AND PROFESSIONAL REASONING ──────────────
            [
                'q' => "A software verification team uses Dafny (a verifying compiler). The prover automatically checks loop invariants using SMT solvers. A developer writes:\n\ninvariant 0 <= i <= n\ninvariant sum == i * (i - 1) / 2\n\nThe SMT solver fails to verify the invariant automatically. The developer should:",
                'opts' => [
                    ['Remove the invariant and trust the code is correct', false],
                    ['Add auxiliary assertions (lemmas) inside the loop to guide the solver, or manually prove the invariant by induction and encode the steps', true],
                    ['Increase the solver timeout and resubmit', false],
                    ['Conclude the code is incorrect and rewrite the loop', false],
                ],
            ],
            [
                'q' => "Claim: A binary search tree (BST) contains at most 2^(h+1) − 1 nodes, where h is the height.\n\nThe proof is by induction on h. For the inductive step, a BST of height h has a root with left subtree of height ≤ h−1 and right subtree of height ≤ h−1. By the inductive hypothesis each subtree has at most 2^h − 1 nodes. Total nodes ≤ 1 + 2(2^h − 1) = ?",
                'opts' => [
                    ['2^h', false],
                    ['2^(h+1) − 1', true],
                    ['2^(h+1)', false],
                    ['2h − 1', false],
                ],
            ],
            [
                'q' => "In a correctness proof for Dijkstra's algorithm, the key invariant is: 'When a node u is extracted from the priority queue with distance d[u], d[u] is the true shortest distance from the source to u.'\n\nThis invariant is proven by contradiction: assume u is the first node extracted with an incorrect d[u]. Since all edge weights are non-negative, any alternative path through unvisited nodes cannot be shorter. This step uses:",
                'opts' => [
                    ['Mathematical induction on the number of edges in the graph', false],
                    ['A contradiction: any shorter path would have to pass through an already-extracted (correctly labeled) node, then through a non-negative edge — making it no shorter', true],
                    ['Proof by cases on the parity of d[u]', false],
                    ['Strong induction on the number of nodes extracted', false],
                ],
            ],
            [
                'q' => "Consider the following claim about a lock-free queue implementation:\n\n'The enqueue and dequeue operations are linearizable — each operation appears to take effect instantaneously at some point between its invocation and response.'\n\nProving linearizability requires identifying a 'linearization point' for each operation. This proof is essentially:",
                'opts' => [
                    ['A proof by contradiction that no concurrent execution is incorrect', false],
                    ['A direct mapping (existential proof): for every concurrent execution history, exhibit a linearization point that produces a valid sequential execution order', true],
                    ['A mathematical induction on the number of threads', false],
                    ['A proof by contrapositive about sequential consistency', false],
                ],
            ],
            [
                'q' => "A database uses two-phase locking (2PL) to guarantee serializability. The correctness proof shows:\n\n'Any execution produced by 2PL is equivalent to some serial execution.'\n\nThe proof constructs a serialization graph and shows it is acyclic. An acyclic serialization graph implies serializability by:",
                'opts' => [
                    ['Mathematical induction on the number of transactions', false],
                    ['A direct proof: a topological ordering of an acyclic graph gives a serial order equivalent to the concurrent execution', true],
                    ['Proof by contradiction: assume a cycle exists in the 2PL graph', false],
                    ['Proof by cases on read-write vs. write-write conflicts', false],
                ],
            ],
            [
                'q' => "Claim: The following memoized Fibonacci is correct and terminates:\n\nfrom functools import lru_cache\n@lru_cache(maxsize=None)\ndef fib(n):\n    if n <= 1: return n\n    return fib(n-1) + fib(n-2)\n\nA complete formal proof of correctness requires proving three things. Which of the following is NOT required?",
                'opts' => [
                    ['Base case correctness: fib(0)=0 and fib(1)=1 match the Fibonacci definition', false],
                    ['Inductive correctness: fib(n) = fib(n-1) + fib(n-2) for n ≥ 2, given the recursive calls are correct', false],
                    ['Termination: the recursion reaches the base case for all n ≥ 0', false],
                    ['Time complexity: fib(n) runs in O(n) time with memoization — this is a performance claim, not a correctness requirement', true],
                ],
            ],
            [
                'q' => "In a proof of the correctness of TLS handshake authentication, the security argument relies on:\n\n'If an adversary can successfully impersonate a server, the adversary can break the underlying [X] assumption.'\n\nWhat does X represent in a typical TLS certificate-based authentication proof?",
                'opts' => [
                    ['The AES encryption assumption', false],
                    ['The unforgeability of digital signatures (EU-CMA security of the signature scheme)', true],
                    ['The Diffie-Hellman assumption alone', false],
                    ['The correctness of TCP/IP packet ordering', false],
                ],
            ],
            [
                'q' => "The following sorting algorithm claims O(n log n) worst-case:\n\ndef mystery_sort(arr):\n    if len(arr) <= 1:\n        return arr\n    pivot = arr[len(arr) // 2]\n    left = [x for x in arr if x < pivot]\n    mid  = [x for x in arr if x == pivot]\n    right= [x for x in arr if x > pivot]\n    return mystery_sort(left) + mid + mystery_sort(right)\n\nA formal proof of worst-case O(n log n) cannot be made for this algorithm. Why?",
                'opts' => [
                    ['Because list concatenation is O(n²)', false],
                    ['Because in the worst case (e.g., already sorted input with many duplicates handled by choosing the median each time), partitions are unbalanced — a pathological input gives T(n) = T(n-1) + O(n) = O(n²)', true],
                    ['Because the mid array may be empty', false],
                    ['Because Python list comprehensions are not O(n)', false],
                ],
            ],
            [
                'q' => "A proof engineer is verifying a property of a concurrent garbage collector:\n\n'No live object is ever collected.'\n\nThe proof uses a tricolor invariant (white = unreachable, gray = reachable but not fully scanned, black = reachable and fully scanned) and shows the invariant is maintained throughout the collection cycle.\n\nWhat is the key challenge that makes this proof harder than a sequential GC proof?",
                'opts' => [
                    ['The tricolor invariant is harder to state for concurrent systems', false],
                    ['Mutator threads may create new references between objects while the collector is running — the proof must show the invariant holds even under concurrent pointer mutations', true],
                    ['The proof requires strong induction on the heap size', false],
                    ['The garbage collector may terminate before collecting all garbage', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 5 — Methods of Proof (Professional).");
    }
}