<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 5 — Introduction to Mathematical Proof (Professional / Tier 4) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Professional tier
 *   2. coding_questions    — 50 questions at research/olympiad proof level
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
 * Difficulty: Professional — research/olympiad level. Problems demand
 * advanced number theory (quadratic reciprocity, primitive roots, p-adic
 * valuations), combinatorial proof engines (RSK correspondence, Lindstrom
 * lemma), algebraic structures (group theory, ring ideals), advanced
 * induction over ordinals, Ramsey-type arguments, and proof complexity
 * metrics. All must be implemented as efficient, well-structured Python.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module5CodingChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (! $category) {
            $this->command->error('Professional category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 5 — Introduction to Mathematical Proof (Professional) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Mathematical Proof — Professional Mastery',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Implement research-grade proof engines: p-adic valuations, primitive root certificates, Berlekamp factoring over finite fields, RSK correspondence, Ramsey witness generators, and proof-complexity analyzers. Each problem encodes a deep mathematical theorem into efficient, correct Python that must handle large inputs and edge cases with precision.',
                'time_limit_seconds' => 2400,
                'base_xp'            => 2000,
                'order_index'        => 6,
            ]
        );

        $this->command->info('Seeding 50 coding questions...');

        // ── Q descriptions ────────────────────────────────────────────────

        $q1desc = "**Proof validity classifier**: Read a proof attempt encoded as a sequence of inference steps. Each step is one of: `ASSUME <P>`, `DERIVE <Q> FROM <P1> [AND <P2>]`, `CONCLUDE <R>`. Verify the proof is valid (each DERIVE step's antecedents have all been established, and CONCLUDE closes an open assumption). Print `valid` or `invalid: <reason>`.\n\nInput: first line is `n` (number of steps), then `n` lines of steps.\n\nExample:\n```\nInput:\n3\nASSUME P\nDERIVE Q FROM P\nCONCLUDE Q\nOutput: valid\n```";

        $q2desc = "**Resolution refutation engine**: Given a set of clauses in CNF, implement the resolution rule to derive the empty clause (proving UNSAT by contradiction) or report `satisfiable`. Read `m` (variables), `n` (clauses), then `n` clauses as space-separated signed integers. Apply resolution until the empty clause is derived or no new clauses can be generated. Print `UNSAT: empty clause derived after <k> steps` or `SAT: no refutation found`.\n\nExample:\n```\nInput:\n2\n4\n1 2\n-1 2\n1 -2\n-1 -2\nOutput: UNSAT: empty clause derived after 3 steps\n```";

        $q3desc = "**Godel numbering encoder/decoder**: Encode a finite sequence of positive integers `a1, a2, ..., an` as its Godel number `G = p1^a1 * p2^a2 * ... * pn^an` (where `pi` is the i-th prime). Read `n` then the sequence. Print `G` (the Godel number). Then decode it back: factorize `G`, recover the sequence, and print `decoded: <sequence>` and `match: True/False`.\n\nExample:\n```\nInput:\n3\n2\n3\n1\nOutput:\nG: 500\ndecoded: 2 3 1\nmatch: True\n```";

        $q4desc = "**Proof length lower bound via information theory**: For a propositional tautology given as a truth table of `2^n` bits, compute the minimum number of connectives needed in any formula (Shannon complexity lower bound = n - log2(number of 1s + 1)). Read `n`, then `2^n` output bits. Print `satisfying: <k>`, `lower_bound: <floor value>`, and the tight formula size if it is a tautology.\n\nExample:\n```\nInput:\n2\n1 1 0 1\nOutput:\nsatisfying: 3\nlower_bound: 1\n```";

        $q5desc = "**Proof by reflection**: A statement S is *decidable* if there is an algorithm that always terminates with True/False for S(n). Given a list of `n` predicates encoded as Python lambda expressions (as strings), attempt to evaluate each on inputs 0..99 and classify as: `always_true`, `always_false`, `mixed`, or `timeout` (if evaluation raises an exception). Read `n` predicate strings (one per line). Print the classification of each.\n\nExample:\n```\nInput:\n2\nn % 2 == 0\nn > 0\nOutput:\nmixed\nmixed\n```";

        $q6desc = "**Bezout coefficient engine with proof trace**: Given integers `a` and `b`, compute Bezout coefficients `x, y` such that `ax + by = gcd(a, b)` using the Extended Euclidean Algorithm. Print the full trace: each step of the algorithm showing the quotient, remainder, and current coefficients. Finally print `gcd: <g>`, `x: <x>`, `y: <y>`, `verified: ax+by=gcd: True/False`.\n\nExample:\n```\nInput:\n35\n15\nOutput:\nstep 1: 35 = 2*15 + 5\nstep 2: 15 = 3*5 + 0\ngcd: 5\nx: 1\ny: -2\nverified: ax+by=gcd: True\n```";

        $q7desc = "**Quadratic reciprocity verifier**: For odd primes `p` and `q`, compute both Legendre symbols (p|q) and (q|p) using Euler's criterion and verify the law of quadratic reciprocity: `(p|q)(q|p) = (-1)^((p-1)/2 * (q-1)/2)`. Read `n` pairs of odd primes, one pair per line after `n`. For each pair print `(p|q)=<v> (q|p)=<v> product=<v> expected=<v> holds=True/False`.\n\nExample:\n```\nInput:\n1\n3 5\nOutput:\n(3|5)=-1 (5|3)=-1 product=1 expected=1 holds=True\n```";

        $q8desc = "**p-adic valuation proof**: The p-adic valuation v_p(n) is the largest power of prime `p` dividing `n`. Prove by direct computation that `v_p(n!) = sum_{k=1}^{inf} floor(n / p^k)` (Legendre's formula). Read prime `p` and integer `n`. Compute `v_p(n!)` both by counting factors in 1..n directly and by Legendre's formula. Print both, verify they match, then print the full Legendre sum term by term.\n\nExample:\n```\nInput:\n3\n10\nOutput:\ndirect: 4\nlegendre: 4\nmatch: True\nterms: floor(10/3)=3 floor(10/9)=1\n```";

        $q9desc = "**Primitive root certificate**: A primitive root `g` modulo prime `p` is an integer whose powers generate all of Z_p*. Read prime `p`. Find the smallest primitive root `g`. Print the full order table: `g^k mod p` for k=1..p-1, and verify the set equals {1, 2, ..., p-1}. Print `primitive root: <g>`, then the order table, then `generates_all: True/False`.\n\nExample:\n```\nInput:\n7\nOutput:\nprimitive root: 3\n3^1=3 3^2=2 3^3=6 3^4=4 3^5=5 3^6=1\ngenerates_all: True\n```";

        $q10desc = "**Waring's problem witness**: Waring's theorem states every positive integer is a sum of at most `g(k)` k-th powers. For k=2, g(2)=4 (Lagrange's four-square theorem). Read `n`. Express it as a sum of at most 4 perfect squares using a greedy/BFS approach. Print `squares: <list>` and `count: <c>` and `sum: <n>` and `verified: True/False`.\n\nExample:\n```\nInput:\n23\nOutput:\nsquares: 9 9 4 1\ncount: 4\nsum: 23\nverified: True\n```";

        $q11desc = "**Contrapositive of Fermat's Last Theorem witness for small cases**: The contrapositive: if `a^n + b^n = c^n` has a positive integer solution, then `n <= 2`. Read `N_max` and `c_max`. For n=1,2 find ALL solutions with `a <= b < c <= c_max`. For n >= 3 verify no solutions exist up to `c_max`. Print solutions for n=1,2 and `no solution: n=<k>` for each k from 3 to N_max.\n\nExample:\n```\nInput:\n4\n5\nOutput:\nn=1: (1,1,2) (1,2,3) (1,3,4) (2,2,4) (1,4,5) (2,3,5)\nn=2: (3,4,5)\nno solution: n=3\nno solution: n=4\n```";

        $q12desc = "**Contrapositive of the four-color theorem (small cases)**: The contrapositive: if a planar graph requires 5 colors, it is not planar. Read a graph as `n` vertices and `m` edges. (a) Check planarity using Kuratowski's subgraph criterion for K5/K3,3 minors (for n<=10). (b) Compute the chromatic number by backtracking. Print `planar: True/False`, `chromatic_number: <k>`, and `consistent_with_4CT: True/False` (consistent if planar => chromatic <= 4).\n\nExample:\n```\nInput:\n4\n6\n1 2\n1 3\n1 4\n2 3\n2 4\n3 4\nOutput:\nplanar: True\nchromatic_number: 4\nconsistent_with_4CT: True\n```";

        $q13desc = "**Contrapositive of Dirichlet's theorem**: If an arithmetic progression `a, a+d, a+2d, ...` contains no prime, then `gcd(a, d) > 1`. The contrapositive: if `gcd(a, d) = 1`, the progression must contain a prime (search up to `a + 1000*d`). Read `T` test cases, each a pair `a d`. For each, print `gcd(a,d)=<g>` and either `prime found: <p> at position <k>` or `no prime found in range`.\n\nExample:\n```\nInput:\n2\n3 4\n4 6\nOutput:\ngcd(3,4)=1 prime found: 7 at position 1\ngcd(4,6)=2 no prime found in range\n```";

        $q14desc = "**Contrapositive of the Prime Number Theorem**: The PNT states pi(x) ~ x/ln(x). By contrapositive: if the ratio pi(x)/(x/ln(x)) deviates significantly from 1, the distribution is non-prime-like. Read `x`. Compute `pi(x)` (prime counting function via sieve), `x/ln(x)`, the ratio, and the Li(x) approximation (logarithmic integral via numerical integration using the trapezoidal rule with 10000 steps from 2 to x). Print all four values rounded to 6dp.\n\nExample:\n```\nInput:\n1000\nOutput:\npi(x): 168\nx/ln(x): 144.764770\nratio: 1.160841\nLi(x): 177.609670\n```";

        $q15desc = "**Contrapositive in graph theory — Ore's theorem**: Ore's theorem: if for every pair of non-adjacent vertices u, v in G, deg(u) + deg(v) >= n, then G has a Hamiltonian cycle. Contrapositive: if G has no Hamiltonian cycle, there exist non-adjacent u, v with deg(u) + deg(v) < n. Read a graph. Check for a Hamiltonian cycle (backtracking, n<=12). If none found, find and print the witness pair. If Hamiltonian cycle exists, print it.\n\nExample:\n```\nInput:\n4\n4\n1 2\n2 3\n3 4\n4 1\nOutput:\nhamiltonian: True\ncycle: 1 2 3 4 1\n```";

        $q16desc = "**Proof by contradiction via coloring — Ramsey R(3,3)=6**: Prove by contradiction that any 2-coloring of K6 edges contains a monochromatic triangle. Read a 2-coloring of K6 as a 6x6 upper-triangle adjacency list (0=red, 1=blue). Find a monochromatic triangle (three vertices all connected by same color). Print `monochromatic triangle: <v1> <v2> <v3> color=<red/blue>`. If somehow none (impossible for K6), print `none found`.\n\nInput format: 15 lines each `u v color` for all pairs u<v.\n\nExample:\n```\nInput:\n15\n1 2 0\n1 3 0\n1 4 1\n1 5 1\n1 6 0\n2 3 1\n2 4 0\n2 5 0\n2 6 1\n3 4 0\n3 5 1\n3 6 1\n4 5 0\n4 6 1\n5 6 0\nOutput: monochromatic triangle: 1 2 4 color=red\n```";

        $q17desc = "**Proof by contradiction — Cantor's theorem (constructive)**: Cantor's theorem: for any set S, |S| < |P(S)|. Given a proposed surjection f: {1..n} -> P({1..n}) (encoded as n subsets), construct the diagonalization set D = {i : i not in f(i)} and show D is not in the range of f. Read `n`, then `n` lines each listing the subset f(i) as space-separated integers (or empty). Print `D: <elements>` and for each i, `f(<i>)=<subset>: D_equals=True/False`. Print `surjection_disproved: True`.\n\nExample:\n```\nInput:\n3\n1 2\n2 3\n1\nOutput:\nD: 3\nf(1)={1,2}: D_equals=False\nf(2)={2,3}: D_equals=False\nf(3)={1}: D_equals=False\nsurjection_disproved: True\n```";

        $q18desc = "**Proof by contradiction — Liouville's theorem on transcendental numbers**: A Liouville number is transcendental. The classic example is L = sum_{k=1}^{inf} 10^{-k!}. Compute L to 50 decimal places by summing terms until the next term < 10^{-50}. Print `L: <value to 50dp>` and `terms_used: <k>`. Then for each rational p/q with q <= 100, verify that |L - p/q| > 1/q^(floor(log10(q))+2) (Liouville condition). Print `liouville_condition_holds: True/False`.\n\nExample:\n```\nInput: 100\nOutput:\nL: 0.11000100000000000000000100000000000000000000000000\nterms_used: 6\nliouville_condition_holds: True\n```";

        $q19desc = "**Proof by contradiction — infinitely many Pythagorean triples**: Assume finitely many; derive contradiction. Euclid's formula generates ALL primitive triples: `a=m^2-n^2, b=2mn, c=m^2+n^2` for `m>n>0, gcd(m,n)=1, m-n odd`. Read `T` (upper bound for m+n). Generate all primitive Pythagorean triples with `m+n <= T`. Print them sorted by `c`, one per line as `(<a>,<b>,<c>)`. Print `total: <k>`.\n\nExample:\n```\nInput: 5\nOutput:\n(3,4,5)\n(5,12,13)\n(8,15,17)\n(7,24,25)\ntotal: 4\n```";

        $q20desc = "**Proof by contradiction — irrationality of log_2(3)**: Assume log_2(3) = p/q in lowest terms. Then 3^q = 2^p. The left side is odd, right side even — contradiction. Implement: read `Q_max`. For all `q` from 1 to `Q_max`, compute `3^q` and check if it is a power of 2. Print `checking q=<v>: 3^q=<v> power_of_2=False` for each. Print `no solution found: log2(3) is irrational (confirmed up to q=<Q_max>)`.\n\nExample:\n```\nInput: 5\nOutput:\nchecking q=1: 3^q=3 power_of_2=False\nchecking q=2: 3^q=9 power_of_2=False\nchecking q=3: 3^q=27 power_of_2=False\nchecking q=4: 3^q=81 power_of_2=False\nchecking q=5: 3^q=243 power_of_2=False\nno solution found: log2(3) is irrational (confirmed up to q=5)\n```";

        $q21desc = "**Proof by cases — quadratic residues mod p via Euler's criterion**: For prime `p`, partition Z_p* into quadratic residues (QR) and quadratic non-residues (QNR). For each element `a`, use Euler's criterion a^((p-1)/2) mod p to classify. Prove by cases: (QR*QR=QR), (QR*QNR=QNR), (QNR*QNR=QR). Read `p`. Print the QR and QNR sets. Then verify all three multiplicative closure cases by checking all products. Print `case QR*QR: all_QR=True/False`, etc.\n\nExample:\n```\nInput: 7\nOutput:\nQR: 1 2 4\nQNR: 3 5 6\ncase QR*QR: all_QR=True\ncase QR*QNR: all_QNR=True\ncase QNR*QNR: all_QR=True\n```";

        $q22desc = "**Proof by cases — Chicken McNugget theorem (Frobenius coin)**: For coprime `a` and `b`, the largest number NOT representable as `xa + yb` (x,y>=0) is `ab - a - b`. Read `T` pairs `(a, b)` with `gcd(a,b)=1`. For each, compute the Frobenius number by formula and verify by brute-force (check all integers up to `ab`). Print `formula: <v>`, `brute_force: <v>`, `match: True/False`.\n\nExample:\n```\nInput:\n2\n3 5\n7 11\nOutput:\nformula: 7\nbrute_force: 7\nmatch: True\nformula: 59\nbrute_force: 59\nmatch: True\n```";

        $q23desc = "**Proof by cases — Legendre's three-square theorem**: A positive integer `n` is representable as a sum of three squares iff `n` is NOT of the form `4^a(8b+7)`. Read `T` integers. For each, check the form condition, attempt to find an explicit representation (enumerate all triples), and verify consistency. Print for each: `n=<v> form=4^a(8b+7): True/False representable: True/False a=<> b=<> consistent: True/False`.\n\nExample:\n```\nInput:\n3\n7\n5\n28\nOutput:\nn=7 form=4^a(8b+7): True representable: False a=0 b=0 consistent: True\nn=5 form=4^a(8b+7): False representable: True a=0 b=0 consistent: True\nn=28 form=4^a(8b+7): True representable: False a=2 b=0 consistent: True\n```";

        $q24desc = "**Proof by cases — Sylvester-Gallai theorem (no three on a line)**: Given `n` points in the plane, if they are NOT all collinear, there must exist an ordinary line (containing exactly 2 points). Read `n` points as `x y` pairs. Check if all are collinear. If not, find and print ONE ordinary line as the two point indices. If all collinear, print `all collinear: no ordinary line`.\n\nExample:\n```\nInput:\n4\n0 0\n1 1\n2 0\n3 1\nOutput:\nordinary line: points 1 3\n```";

        $q25desc = "**Proof by exhaustive cases — sum of divisors function**: Prove by exhaustive case analysis that `sigma(p^k) = (p^(k+1) - 1)/(p - 1)` for prime p. Read `T` pairs `(p, k)`. For each, compute sigma(p^k) both by summing all divisors of `p^k` (which are 1, p, p^2, ..., p^k) and by the formula. Print `sigma(<p>^<k>): direct=<v> formula=<v> match=True/False`.\n\nExample:\n```\nInput:\n2\n2 3\n3 2\nOutput:\nsigma(2^3): direct=15 formula=15 match=True\nsigma(3^2): direct=13 formula=13 match=True\n```";

        $q26desc = "**Proof by strong induction — Dilworth's theorem**: The minimum number of chains needed to partition a poset equals the maximum antichain size. Read a partial order on `n` elements as `m` relations `a < b` (transitive closure). Compute: (a) the maximum antichain by greedy/backtracking, (b) a minimum chain decomposition using Mirsky's algorithm. Print `max_antichain: <size> elements: <list>`, `min_chains: <k>`, `dilworth_holds: True/False`.\n\nExample:\n```\nInput:\n4\n3\n1 2\n1 3\n2 4\nOutput:\nmax_antichain: 2 elements: 3 4\nmin_chains: 2\ndilworth_holds: True\n```";

        $q27desc = "**Proof by induction — Cayley's formula**: The number of labeled trees on `n` vertices is `n^(n-2)`. Read `n`. Enumerate all labeled trees using Pruefer sequences (bijection: each sequence of length n-2 with values in {1..n} corresponds to a unique labeled tree). Count them, verify count = n^(n-2), and print one sample tree (from Pruefer sequence [1,1,...,1]).\n\nFormat:\n```\npruefer_sequences: <n^(n-2)>\ntrees_counted: <n^(n-2)>\ncayley_verified: True/False\nsample_tree_edges: <edge list>\n```\n\nExample:\n```\nInput: 4\nOutput:\npruefer_sequences: 16\ntrees_counted: 16\ncayley_verified: True\nsample_tree_edges: (1,2) (1,3) (1,4)\n```";

        $q28desc = "**Induction proof — matrix exponentiation Fibonacci**: Prove by induction that `[[1,1],[1,0]]^n = [[F(n+1),F(n)],[F(n),F(n-1)]]`. Read `n` and `m` (modulus). Compute the matrix power using fast exponentiation, extract F(n) mod m. Also compute F(n) mod m via the standard recurrence. Print both, verify they match, and print the full matrix.\n\nFormat:\n```\nmatrix_fib: <value>\nrecurrence_fib: <value>\nmatch: True/False\nmatrix: [[<a>,<b>],[<c>,<d>]]\n```\n\nExample:\n```\nInput:\n10\n1000\nOutput:\nmatrix_fib: 55\nrecurrence_fib: 55\nmatch: True\nmatrix: [[89,55],[55,34]]\n```";

        $q29desc = "**Induction — Burnside's lemma**: The number of distinct colorings of `n` objects arranged in a cycle using `k` colors, under rotation symmetry, equals `(1/n) * sum_{d|n} phi(d) * k^(n/d)`. Read `n` and `k`. Compute the formula. Also enumerate all k^n colorings and count distinct necklaces by canonical form (minimum rotation). Print `formula: <v>`, `enumeration: <v>`, `match: True/False`.\n\nExample:\n```\nInput:\n4\n2\nOutput:\nformula: 6\nenumeration: 6\nmatch: True\n```";

        $q30desc = "**Induction — Kirchhoff's matrix-tree theorem**: The number of spanning trees of a graph equals any cofactor of its Laplacian matrix. Read a graph as `n` vertices and `m` edges. Compute the Laplacian `L = D - A`. Compute the determinant of the (n-1)x(n-1) minor by removing row/col 1 (using Gaussian elimination over rationals). Print `laplacian_cofactor: <v>`. Verify by counting spanning trees directly (Pruefer sequences for complete graphs, or edge-subset enumeration for small graphs).\n\nExample:\n```\nInput:\n4\n5\n1 2\n1 3\n2 3\n2 4\n3 4\nOutput:\nlaplacian_cofactor: 8\nverified_by_enumeration: 8\nmatch: True\n```";

        $q31desc = "**Strong induction — Berlekamp's algorithm over GF(2)**: Factor a polynomial over GF(2) = {0,1} using Berlekamp's algorithm. Read a polynomial as a list of coefficients (0 or 1) from degree 0 upward, then a line with the number of coefficients `n`. Find all irreducible factors over GF(2). Print each factor as a coefficient list and verify their product equals the original polynomial (all arithmetic mod 2).\n\nExample:\n```\nInput:\n5\n1 1 0 1 1\nOutput:\nfactors:\n1 1\n1 1 1\nproduct_verified: True\n```";

        $q32desc = "**Strong induction — RSK correspondence**: The Robinson-Schensted-Knuth correspondence gives a bijection between permutations of {1..n} and pairs of standard Young tableaux of the same shape. Read a permutation `p` of {1..n} (space-separated on one line after `n`). Apply RSK insertion to produce the insertion tableau P and recording tableau Q. Print both tableaux row by row. Then apply the inverse RSK to recover the permutation and verify.\n\nExample:\n```\nInput:\n4\n2 1 4 3\nOutput:\nP:\n1 3\n2 4\nQ:\n1 3\n2 4\nrecovered: 2 1 4 3\nverified: True\n```";

        $q33desc = "**Strong induction — van der Waerden's theorem W(2,3)=9**: Prove that any 2-coloring of {1..9} contains a monochromatic 3-term arithmetic progression. Read a 2-coloring of {1..9} as 9 digits (0 or 1). Find a monochromatic AP of length 3. Print `AP found: <a> <a+d> <a+2d> color=<0/1>` or `none found` (impossible for n=9, W(2,3)=9).\n\nExample:\n```\nInput:\n0 1 0 1 0 1 0 1 0\nOutput: AP found: 1 3 5 color=0\n```";

        $q34desc = "**Strong induction — Lindstrom-Gessel-Viennot lemma**: Count lattice paths from source set `S = {(0,a_i)}` to sink set `T = {(n,b_j)}` using the determinant of the path-count matrix. Read `k` (number of paths), `n` (steps), then `k` values `a_1..a_k` and `k` values `b_1..b_k`. Count paths from `(0,a_i)` to `(n,b_j)` as `C(n, (n+b_j-a_i)/2)` if `n+b_j-a_i` is non-negative even, else 0. Print the path-count matrix, its determinant, and `non_intersecting_paths: <det>`.\n\nExample:\n```\nInput:\n2\n4\n0 2\n2 4\nOutput:\nmatrix:\n3 1\n1 3\ndet: 8\nnon_intersecting_paths: 8\n```";

        $q35desc = "**Strong induction — Sprague-Grundy for Nim with restricted moves**: Compute Grundy values G(n) for a Nim game where from pile n you may remove any divisor of n (not n itself, but including 1). G(0)=0. G(n) = mex{G(n-d) : d | n, d < n}. Read `N`. Print G(0) through G(N) one per line, and identify the first losing position (G=0) > 0 if any.\n\nExample:\n```\nInput: 6\nOutput:\n0\n1\n2\n1\n2\n3\n2\nfirst losing position > 0: none\n```";

        $q36desc = "**Existence proof — Chebyshev's theorem (Bertrand's postulate, constructive)**: For every n>=1 there is a prime p with n < p <= 2n. Prove constructively via Chebyshev's psi function. Read `N`. For each n from 1 to N, find the prime in (n, 2n], and compute psi(2n) = sum of log(p) for primes p <= 2n (von Mangoldt). Print for each n: `n=<v> prime=<p> psi(2n)=<v rounded to 4dp>`.\n\nExample:\n```\nInput: 5\nOutput:\nn=1 prime=2 psi(2)=0.6931\nn=2 prime=3 psi(4)=1.7918\nn=3 prime=5 psi(6)=2.4849\nn=4 prime=5 psi(8)=3.1781\nn=5 prime=7 psi(10)=3.8712\n```";

        $q37desc = "**Uniqueness — Smith normal form**: Every integer matrix has a unique Smith normal form D = UAV where U,V are unimodular integer matrices and D is diagonal with d_1 | d_2 | ... | d_r. Read an `m x n` integer matrix (m lines of n space-separated integers). Compute the Smith normal form (invariant factors). Print the diagonal entries of D (the invariant factors), verifying each divides the next.\n\nExample:\n```\nInput:\n2 3\n2\n0 6\n0 0\nOutput:\ninvariant factors: 2 6\ndivisibility: 2|6 True\n```";

        $q38desc = "**Existence and uniqueness — CRT (Chinese Remainder Theorem)**: Given `n` congruences x = a_i (mod m_i) with pairwise coprime moduli, CRT guarantees a unique solution mod M = product(m_i). Read `n` then `n` pairs `a_i m_i`. Compute the solution x in [0, M) using the standard CRT construction. Print `x: <value>`, `M: <value>`, and verify all congruences hold.\n\nExample:\n```\nInput:\n3\n2 3\n3 5\n2 7\nOutput:\nx: 23\nM: 105\nverified: True\n```";

        $q39desc = "**Existence — algebraic proof of Sylvester's matrix**: The resultant of two polynomials `f` and `g` is nonzero iff they share no common root. Read two polynomials as coefficient lists (constant term first). Compute the Sylvester matrix, its determinant (resultant) using Gaussian elimination, and find common roots numerically (using the companion matrix eigenvalues, approximated to 4dp). Print `resultant: <v>`, `common_roots: <list or 'none'>`, `consistent: True/False`.\n\nExample:\n```\nInput:\n3\n-6 1 1\n3\n2 -3 1\nOutput:\nresultant: 0\ncommon_roots: 2.0000\nconsistent: True\n```";

        $q40desc = "**Existence and uniqueness — Haar measure on finite groups**: For any finite group G (given by its Cayley table), the normalized counting measure is the unique left-invariant probability measure. Read `n` (group order), then the `n x n` multiplication table (0-indexed). Verify: (a) the table defines a group (closure, identity, inverses, associativity for all triples), (b) the uniform distribution 1/n is left-invariant under all group elements. Print `is_group: True/False`, `haar_unique: True/False`.\n\nExample:\n```\nInput:\n2\n0 1\n1 0\nOutput:\nis_group: True\nhaar_unique: True\n```";

        $q41desc = "**Counterexample search — Euler's sum of powers conjecture**: Euler conjectured that at least `n` n-th powers are needed to sum to an n-th power (generalizing FLT). This is FALSE for n=5: 27^5 + 84^5 + 110^5 + 133^5 = 144^5. Read `n=5` and a search bound `B`. Verify the known counterexample by computing both sides. Print `LHS: <v>`, `RHS: <v>`, `counterexample_verified: True/False`. Also search for any other solution with all terms <= B and print them.\n\nExample:\n```\nInput:\n5\n150\nOutput:\nLHS: 4215885696344350028\nRHS: 4215885696344350028\ncounterexample_verified: True\nadditional found: none\n```";

        $q42desc = "**Counterexample — Mertens conjecture**: Mertens conjecture states |M(n)| < sqrt(n) for all n>=1 where M(n) = sum_{k=1}^{n} mu(k) (Mobius function). This is FALSE but not known until very large n. Read `N`. Compute M(n) for n=1..N via a linear sieve for mu(k). Print M(n) for n=1..N (one per line) and `max |M(n)|/sqrt(n): <value rounded to 6dp>`. Print `mertens_holds_in_range: True/False` (True if |M(n)| < sqrt(n) for all checked n).\n\nExample:\n```\nInput: 10\nOutput:\nM(1)=1 ratio=1.000000\nM(2)=0 ratio=0.000000\nM(3)=-1 ratio=0.577350\nM(4)=-1 ratio=0.500000\nM(5)=-2 ratio=0.894427\nM(6)=-1 ratio=0.408248\nM(7)=-2 ratio=0.755929\nM(8)=-2 ratio=0.707107\nM(9)=-2 ratio=0.666667\nM(10)=-1 ratio=0.316228\nmax |M(n)|/sqrt(n): 1.000000\nmertens_holds_in_range: True\n```";

        $q43desc = "**Counterexample — Goldbach's conjecture search**: Goldbach's conjecture (unproven): every even integer > 2 is the sum of two primes. Read `N`. For each even `n` from 4 to N, find all Goldbach pairs (p, q) with p <= q and p+q=n. Print the pair count and the pair with p closest to n/2 (the 'strong' pair). If any even number has no Goldbach decomposition, print `counterexample: <n>`. Otherwise print `no counterexample found`.\n\nExample:\n```\nInput: 20\nOutput:\n4: pairs=1 strong=2+2\n6: pairs=1 strong=3+3\n8: pairs=1 strong=3+5\n10: pairs=2 strong=5+5\n12: pairs=1 strong=5+7\n14: pairs=2 strong=7+7\n16: pairs=2 strong=5+11\n18: pairs=2 strong=7+11\n20: pairs=2 strong=7+13\nno counterexample found\n```";

        $q44desc = "**Counterexample — Polya's conjecture**: Polya's conjecture states that for n>=2, at least half of integers up to n have an ODD number of prime factors (with multiplicity). This is FALSE; the first counterexample is n=906150257. Read `N` (<=10000). Compute L(n) = #{k<=n : Omega(k) odd} - #{k<=n : Omega(k) even} for n=1..N where Omega(k) is the number of prime factors with multiplicity. Print `L(n)` for each n, and `polya_holds_in_range: True/False` (True if L(n)>=0 for all checked n). Also print the minimum L(n) found.\n\nExample:\n```\nInput: 10\nOutput:\nL(1)=1\nL(2)=0\nL(3)=-1\nL(4)=0\nL(5)=-1\nL(6)=0\nL(7)=-1\nL(8)=-2\nL(9)=-1\nL(10)=-2\nmin L(n): -2\npolya_holds_in_range: False\n```";

        $q45desc = "**Counterexample — Lander-Parkin-Selfridge conjecture**: LPS conjecture (generalization of FLT): if sum of `m` k-th powers equals a k-th power, then m >= k. For k=4: find the smallest counterexample to the naive claim 'sum of 2 fourth powers never equals a fourth power' (it cannot for k=4, m=2 by FLT-like arguments, but for k=3,m=2 it can: 3^3+4^3+5^3=6^3 is actually m=3). Read `k` and search bound `B`. For the given k, search for any solution with m < k terms each at most B. Print solutions found or `no counterexample found`.\n\nExample:\n```\nInput:\n3\n10\nOutput:\n3^3 + 4^3 + 5^3 = 6^3\ncounterexample: m=3 = k=3 (not a counterexample, m=k)\nno counterexample found\n```";

        $q46desc = "**Strategy engine — automated proof classifier**: Given a mathematical statement as a structured input, classify the best proof strategy and implement it. Statements are encoded as: `TYPE PARAM` where TYPE is one of DIOPHANTINE, DIVISIBILITY, PIGEONHOLE, EXTREMAL, PROBABILISTIC. Read `n` statements. For each, apply the corresponding strategy and print the result.\n\n- `DIOPHANTINE a b c`: find integers x,y with ax+by=c or print `no solution` (use extended GCD)\n- `DIVISIBILITY n k`: prove or disprove k|n^k - n (Fermat generalization)\n- `PIGEONHOLE n k`: given n items in k boxes, find guaranteed collision count floor((n-1)/k)+1\n- `EXTREMAL n`: find max sum subset of {1..n} with no two consecutive (Fibonacci structure)\n- `PROBABILISTIC p n`: expected number of trials to see all n outcomes in a coupon-collector with p-sided die\n\nPrint one result line per statement.\n\nExample:\n```\nInput:\n3\nDIOPHANTINE 3 5 1\nDIVISIBILITY 10 3\nPIGEONHOLE 10 3\nOutput:\nDIOPHANTINE: x=2 y=-1\nDIVISIBILITY: True (3|10^3-10=990)\nPIGEONHOLE: guaranteed 4 in same box\n```";

        $q47desc = "**Proof strategy cost model**: Implement a proof cost model that estimates the computational complexity of verifying a claim using each of 5 strategies, then selects the cheapest. For a given `n`, compute:\n- direct_cost = O(n) = n\n- contrapositive_cost = O(n log n) = n * ceil(log2(n+1))\n- contradiction_cost = O(sqrt(n)) = ceil(sqrt(n))\n- induction_cost = O(log n) = ceil(log2(n+1))\n- cases_cost = O(n^(1/3)) = ceil(n^(1/3))\n\nRead `T` values of `n`. For each, print all five costs and the recommended strategy (minimum cost), breaking ties alphabetically.\n\nExample:\n```\nInput:\n2\n8\n100\nOutput:\nn=8: direct=8 contrapositive=24 contradiction=3 induction=3 cases=2 recommended=cases\nn=100: direct=100 contrapositive=700 contradiction=10 induction=7 cases=5 recommended=cases\n```";

        $q48desc = "**Proof portfolio verifier**: Read `n` theorems to verify, each given as `THEOREM <name> <type> <param>`. Verify each using its designated proof type and output a formal proof certificate. Types:\n- `FERMAT p a`: verify a^(p-1)=1 mod p; certificate = `FLT(p,a,pow(a,p-1,p))`\n- `WILSON p`: verify (p-1)!=-1 mod p; certificate = `W(p,factorial(p-1)%p)`\n- `BEZOUT a b`: compute gcd and coefficients; certificate = `B(a,b,gcd,x,y)`\n- `EULER n`: verify Euler's product formula for phi(n); certificate = `E(n,phi(n))`\n- `LUCAS p`: verify Lucas primality test (if p-1 factors are known); certificate = `L(p,factors)`\n\nPrint each certificate on one line.\n\nExample:\n```\nInput:\n2\nTHEOREM T1 FERMAT 7 3\nTHEOREM T2 WILSON 7\nOutput:\nT1: FLT(7,3,1) verified=True\nT2: W(7,6) verified=True\n```";

        $q49desc = "**Proof complexity — circuit lower bounds (toy model)**: For Boolean functions over n variables, the circuit size complexity is at least n (trivially). Compute a tighter lower bound using the gate elimination method: given a truth table of 2^n bits, greedily eliminate variables by fixing them to 0 or 1, counting the minimum gates needed. Read `n` (<=4), then 2^n bits of the truth table. Implement gate elimination: at each step pick the variable whose elimination reduces the function most (fewest remaining 1s). Print the elimination order, the reduced function at each step, and the final lower bound.\n\nExample:\n```\nInput:\n2\n0 1 1 0\nOutput:\neliminate x1=0: 1 0\neliminate x1=1: 0 1\nlower_bound: 2\n```";

        $q50desc = "**Grand professional capstone**: Read `n` and execute a complete proof portfolio for all 10 proof strategies applied to number-theoretic properties of `n`. For each strategy, print a one-line certificate. Strategies and tasks:\n1. direct: verify n*(n+1)*(n+2) divisible by 6\n2. contrapositive: find smallest prime factor of n (or 'prime')\n3. contradiction: show sqrt(n) is irrational unless perfect square\n4. cases_mod2: classify n*(n+1) divisibility pattern\n5. cases_mod3: classify n^2 mod 3\n6. induction: verify sum_{i=1}^{n} i = n*(n+1)/2\n7. strong_induction: Zeckendorf representation\n8. existence: find prime p in (n, 2n]\n9. uniqueness: n = 2^k * m, m odd\n10. strategy_choice: recommend best proof strategy for 'n is prime'\n\nExample:\n```\nInput: 12\nOutput:\n1. direct: 12*13*14=2184 div6=True\n2. contrapositive: factor=2\n3. contradiction: sqrt(12) irrational (not perfect square)\n4. cases_mod2: n=12(even) n*(n+1)=156 div2=True\n5. cases_mod3: n^2 mod3=0\n6. induction: sum=78 formula=78 match=True\n7. strong_induction: 8+3+1=12 fibs=[8,3,1]\n8. existence: prime=13\n9. uniqueness: 12=2^2*3\n10. strategy_choice: trial_division or contradiction\n```";

        $questionDefs = [
            ['order_index' => 1,  'problem_description' => $q1desc,  'starter_code' => "n = int(input())\n# Read n proof steps and validate\n# Write your solution below\n",                                                                     'time_limit_seconds' => 1200, 'base_xp' => 300],
            ['order_index' => 2,  'problem_description' => $q2desc,  'starter_code' => "m = int(input())\nn = int(input())\nclauses = [list(map(int, input().split())) for _ in range(n)]\n# Implement resolution refutation\n",             'time_limit_seconds' => 1200, 'base_xp' => 400],
            ['order_index' => 3,  'problem_description' => $q3desc,  'starter_code' => "import math\nn = int(input())\nseq = [int(input()) for _ in range(n)]\n# Compute Godel number and decode\n",                                            'time_limit_seconds' => 1200, 'base_xp' => 350],
            ['order_index' => 4,  'problem_description' => $q4desc,  'starter_code' => "import math\nn = int(input())\nbits = list(map(int, input().split()))\n# Compute Shannon lower bound\n",                                               'time_limit_seconds' => 1200, 'base_xp' => 350],
            ['order_index' => 5,  'problem_description' => $q5desc,  'starter_code' => "n = int(input())\npredicates = [input().strip() for _ in range(n)]\n# Evaluate predicates on 0..99\n",                                                'time_limit_seconds' => 1200, 'base_xp' => 300],
            ['order_index' => 6,  'problem_description' => $q6desc,  'starter_code' => "a = int(input())\nb = int(input())\n# Extended Euclidean with trace\n",                                                                               'time_limit_seconds' => 1200, 'base_xp' => 350],
            ['order_index' => 7,  'problem_description' => $q7desc,  'starter_code' => "n = int(input())\npairs = [tuple(map(int, input().split())) for _ in range(n)]\n# Verify quadratic reciprocity\n",                                   'time_limit_seconds' => 1200, 'base_xp' => 400],
            ['order_index' => 8,  'problem_description' => $q8desc,  'starter_code' => "p = int(input())\nn = int(input())\n# Legendre formula for v_p(n!)\n",                                                                                'time_limit_seconds' => 1200, 'base_xp' => 400],
            ['order_index' => 9,  'problem_description' => $q9desc,  'starter_code' => "p = int(input())\n# Find primitive root and verify\n",                                                                                               'time_limit_seconds' => 1200, 'base_xp' => 400],
            ['order_index' => 10, 'problem_description' => $q10desc, 'starter_code' => "import math\nn = int(input())\n# Four-square representation\n",                                                                                      'time_limit_seconds' => 1200, 'base_xp' => 400],
            ['order_index' => 11, 'problem_description' => $q11desc, 'starter_code' => "N_max = int(input())\nc_max = int(input())\n# Search for FLT solutions\n",                                                                           'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 12, 'problem_description' => $q12desc, 'starter_code' => "n = int(input())\nm = int(input())\nedges = [tuple(map(int, input().split())) for _ in range(m)]\n# Check planarity and chromatic number\n",         'time_limit_seconds' => 1200, 'base_xp' => 500],
            ['order_index' => 13, 'problem_description' => $q13desc, 'starter_code' => "import math\nT = int(input())\ncases = [tuple(map(int, input().split())) for _ in range(T)]\n# Dirichlet arithmetic progression primes\n",          'time_limit_seconds' => 1200, 'base_xp' => 400],
            ['order_index' => 14, 'problem_description' => $q14desc, 'starter_code' => "import math\nx = int(input())\n# PNT: pi(x), x/ln(x), Li(x)\n",                                                                                     'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 15, 'problem_description' => $q15desc, 'starter_code' => "n = int(input())\nm = int(input())\nedges = [tuple(map(int, input().split())) for _ in range(m)]\n# Hamiltonian cycle + Ore's theorem witness\n",    'time_limit_seconds' => 1200, 'base_xp' => 500],
            ['order_index' => 16, 'problem_description' => $q16desc, 'starter_code' => "lines = int(input())\ncoloring = [tuple(map(int, input().split())) for _ in range(lines)]\n# Find Ramsey monochromatic triangle\n",               'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 17, 'problem_description' => $q17desc, 'starter_code' => "n = int(input())\nf = []\nfor _ in range(n):\n    line = input().split()\n    f.append(set(map(int, line)) if line[0] else set())\n# Cantor diagonalization\n", 'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 18, 'problem_description' => $q18desc, 'starter_code' => "from decimal import Decimal, getcontext\ngetcontext().prec = 60\nQ_max = int(input())\n# Liouville number computation\n",                           'time_limit_seconds' => 1200, 'base_xp' => 500],
            ['order_index' => 19, 'problem_description' => $q19desc, 'starter_code' => "import math\nT = int(input())\n# Euclid formula for Pythagorean triples\n",                                                                          'time_limit_seconds' => 1200, 'base_xp' => 400],
            ['order_index' => 20, 'problem_description' => $q20desc, 'starter_code' => "Q_max = int(input())\n# Irrationality of log2(3)\n",                                                                                                 'time_limit_seconds' => 1200, 'base_xp' => 350],
            ['order_index' => 21, 'problem_description' => $q21desc, 'starter_code' => "p = int(input())\n# QR/QNR partition and multiplicative cases\n",                                                                                   'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 22, 'problem_description' => $q22desc, 'starter_code' => "import math\nT = int(input())\npairs = [tuple(map(int, input().split())) for _ in range(T)]\n# Frobenius/Chicken McNugget theorem\n",              'time_limit_seconds' => 1200, 'base_xp' => 400],
            ['order_index' => 23, 'problem_description' => $q23desc, 'starter_code' => "T = int(input())\nnums = [int(input()) for _ in range(T)]\n# Legendre three-square theorem\n",                                                      'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 24, 'problem_description' => $q24desc, 'starter_code' => "n = int(input())\npoints = [tuple(map(int, input().split())) for _ in range(n)]\n# Sylvester-Gallai ordinary line\n",                             'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 25, 'problem_description' => $q25desc, 'starter_code' => "T = int(input())\npairs = [tuple(map(int, input().split())) for _ in range(T)]\n# Sigma function for prime powers\n",                             'time_limit_seconds' => 1200, 'base_xp' => 350],
            ['order_index' => 26, 'problem_description' => $q26desc, 'starter_code' => "n = int(input())\nm = int(input())\nrelations = [tuple(map(int, input().split())) for _ in range(m)]\n# Dilworth theorem computation\n",           'time_limit_seconds' => 1200, 'base_xp' => 500],
            ['order_index' => 27, 'problem_description' => $q27desc, 'starter_code' => "n = int(input())\n# Cayley formula via Pruefer sequences\n",                                                                                         'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 28, 'problem_description' => $q28desc, 'starter_code' => "n = int(input())\nm = int(input())\n# Matrix exponentiation Fibonacci\n",                                                                           'time_limit_seconds' => 1200, 'base_xp' => 400],
            ['order_index' => 29, 'problem_description' => $q29desc, 'starter_code' => "n = int(input())\nk = int(input())\n# Burnside's lemma necklace count\n",                                                                           'time_limit_seconds' => 1200, 'base_xp' => 500],
            ['order_index' => 30, 'problem_description' => $q30desc, 'starter_code' => "n = int(input())\nm = int(input())\nedges = [tuple(map(int, input().split())) for _ in range(m)]\n# Kirchhoff matrix-tree theorem\n",              'time_limit_seconds' => 1200, 'base_xp' => 500],
            ['order_index' => 31, 'problem_description' => $q31desc, 'starter_code' => "n = int(input())\ncoeffs = list(map(int, input().split()))\n# Berlekamp factoring over GF(2)\n",                                                    'time_limit_seconds' => 1200, 'base_xp' => 500],
            ['order_index' => 32, 'problem_description' => $q32desc, 'starter_code' => "n = int(input())\nperm = list(map(int, input().split()))\n# RSK correspondence\n",                                                                  'time_limit_seconds' => 1200, 'base_xp' => 500],
            ['order_index' => 33, 'problem_description' => $q33desc, 'starter_code' => "coloring = list(map(int, input().split()))\n# van der Waerden W(2,3)=9\n",                                                                          'time_limit_seconds' => 1200, 'base_xp' => 400],
            ['order_index' => 34, 'problem_description' => $q34desc, 'starter_code' => "import math\nk = int(input())\nn = int(input())\na = list(map(int, input().split()))\nb = list(map(int, input().split()))\n# LGV lemma\n",           'time_limit_seconds' => 1200, 'base_xp' => 500],
            ['order_index' => 35, 'problem_description' => $q35desc, 'starter_code' => "N = int(input())\n# Grundy values for divisor Nim\n",                                                                                               'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 36, 'problem_description' => $q36desc, 'starter_code' => "import math\nN = int(input())\n# Chebyshev psi function + Bertrand's postulate\n",                                                                  'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 37, 'problem_description' => $q37desc, 'starter_code' => "m, n = map(int, input().split())\nmatrix = [list(map(int, input().split())) for _ in range(m)]\n# Smith normal form\n",                            'time_limit_seconds' => 1200, 'base_xp' => 500],
            ['order_index' => 38, 'problem_description' => $q38desc, 'starter_code' => "n = int(input())\ncongruences = [tuple(map(int, input().split())) for _ in range(n)]\n# Chinese Remainder Theorem\n",                             'time_limit_seconds' => 1200, 'base_xp' => 400],
            ['order_index' => 39, 'problem_description' => $q39desc, 'starter_code' => "deg_f = int(input())\nf = list(map(int, input().split()))\ndeg_g = int(input())\ng = list(map(int, input().split()))\n# Sylvester matrix + resultant\n", 'time_limit_seconds' => 1200, 'base_xp' => 500],
            ['order_index' => 40, 'problem_description' => $q40desc, 'starter_code' => "n = int(input())\ntable = [list(map(int, input().split())) for _ in range(n)]\n# Verify group axioms + Haar measure\n",                           'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 41, 'problem_description' => $q41desc, 'starter_code' => "k = int(input())\nB = int(input())\n# Euler sum of powers conjecture counterexample\n",                                                            'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 42, 'problem_description' => $q42desc, 'starter_code' => "N = int(input())\n# Mertens conjecture and Mobius function\n",                                                                                      'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 43, 'problem_description' => $q43desc, 'starter_code' => "N = int(input())\n# Goldbach conjecture search\n",                                                                                                  'time_limit_seconds' => 1200, 'base_xp' => 400],
            ['order_index' => 44, 'problem_description' => $q44desc, 'starter_code' => "N = int(input())\n# Polya conjecture and Liouville lambda function\n",                                                                             'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 45, 'problem_description' => $q45desc, 'starter_code' => "k = int(input())\nB = int(input())\n# Lander-Parkin-Selfridge counterexample search\n",                                                            'time_limit_seconds' => 1200, 'base_xp' => 500],
            ['order_index' => 46, 'problem_description' => $q46desc, 'starter_code' => "n = int(input())\nstatements = [input().split() for _ in range(n)]\n# Automated proof strategy engine\n",                                         'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 47, 'problem_description' => $q47desc, 'starter_code' => "import math\nT = int(input())\nvalues = [int(input()) for _ in range(T)]\n# Proof cost model\n",                                                   'time_limit_seconds' => 1200, 'base_xp' => 400],
            ['order_index' => 48, 'problem_description' => $q48desc, 'starter_code' => "import math\nn = int(input())\ntheorems = [input().split() for _ in range(n)]\n# Proof portfolio verifier\n",                                      'time_limit_seconds' => 1200, 'base_xp' => 450],
            ['order_index' => 49, 'problem_description' => $q49desc, 'starter_code' => "n = int(input())\nbits = list(map(int, input().split()))\n# Circuit lower bounds via gate elimination\n",                                          'time_limit_seconds' => 1200, 'base_xp' => 500],
            ['order_index' => 50, 'problem_description' => $q50desc, 'starter_code' => "import math\nn = int(input())\n# Grand professional proof portfolio\n",                                                                             'time_limit_seconds' => 1200, 'base_xp' => 600],
        ];

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

        $this->command->info('Seeding test cases...');

        $seed = function (int $qIdx, array $cases) use ($questions): void {
            $qId = $questions[$qIdx] ?? null;
            if (! $qId) {
                return;
            }
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

        // ── Q1: Proof step validator ──────────────────────────────────────
        $seed(1, [
            ['input' => "3\nASSUME P\nDERIVE Q FROM P\nCONCLUDE Q",             'expected_output' => "valid",                                  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nASSUME P\nDERIVE Q FROM R\nCONCLUDE Q",             'expected_output' => "invalid: R not established",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nASSUME P\nASSUME Q\nDERIVE R FROM P AND Q\nCONCLUDE R", 'expected_output' => "valid",                             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nDERIVE Q FROM P\nCONCLUDE Q",                       'expected_output' => "invalid: P not established",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Resolution refutation ──────────────────────────────────────
        $seed(2, [
            ['input' => "2\n4\n1 2\n-1 2\n1 -2\n-1 -2",    'expected_output' => "UNSAT: empty clause derived after 3 steps", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2\n1 2\n-1 2",                  'expected_output' => "SAT: no refutation found",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2\n1\n-1",                      'expected_output' => "UNSAT: empty clause derived after 1 steps", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1\n1 2",                        'expected_output' => "SAT: no refutation found",                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Godel numbering ────────────────────────────────────────────
        $seed(3, [
            ['input' => "3\n2\n3\n1",  'expected_output' => "G: 500\ndecoded: 2 3 1\nmatch: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n1",     'expected_output' => "G: 6\ndecoded: 1 1\nmatch: True",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3",  'expected_output' => "G: 360\ndecoded: 1 2 3\nmatch: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n4",        'expected_output' => "G: 16\ndecoded: 4\nmatch: True",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Shannon lower bound ────────────────────────────────────────
        $seed(4, [
            ['input' => "2\n1 1 0 1",  'expected_output' => "satisfying: 3\nlower_bound: 1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 1 1 1",  'expected_output' => "satisfying: 4\nlower_bound: 2",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 0 0 1",  'expected_output' => "satisfying: 1\nlower_bound: 0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 0 0 1",  'expected_output' => "satisfying: 2\nlower_bound: 1",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Predicate classifier ───────────────────────────────────────
        $seed(5, [
            ['input' => "2\nn % 2 == 0\nn > 0",             'expected_output' => "mixed\nmixed",           'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nn == n\nn > n",                 'expected_output' => "always_true\nalways_false", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nn * 0 == 0",                    'expected_output' => "always_true",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nn < 0",                         'expected_output' => "mixed",                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Bezout trace ───────────────────────────────────────────────
        $seed(6, [
            ['input' => "35\n15",   'expected_output' => "step 1: 35 = 2*15 + 5\nstep 2: 15 = 3*5 + 0\ngcd: 5\nx: 1\ny: -2\nverified: ax+by=gcd: True",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "48\n18",   'expected_output' => "step 1: 48 = 2*18 + 12\nstep 2: 18 = 1*12 + 6\nstep 3: 12 = 2*6 + 0\ngcd: 6\nx: 1\ny: -2\nverified: ax+by=gcd: True", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n37",  'expected_output' => "step 1: 100 = 2*37 + 26\nstep 2: 37 = 1*26 + 11\nstep 3: 26 = 2*11 + 4\nstep 4: 11 = 2*4 + 3\nstep 5: 4 = 1*3 + 1\nstep 6: 3 = 3*1 + 0\ngcd: 1\nx: 10\ny: -27\nverified: ax+by=gcd: True", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "7\n3",     'expected_output' => "step 1: 7 = 2*3 + 1\nstep 2: 3 = 3*1 + 0\ngcd: 1\nx: 1\ny: -2\nverified: ax+by=gcd: True",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Quadratic reciprocity ──────────────────────────────────────
        $seed(7, [
            ['input' => "1\n3 5",   'expected_output' => "(3|5)=-1 (5|3)=-1 product=1 expected=1 holds=True",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n5 7",   'expected_output' => "(5|7)=-1 (7|5)=-1 product=1 expected=1 holds=True",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n3 7",   'expected_output' => "(3|7)=1 (7|3)=1 product=1 expected=-1 holds=False",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5 11",  'expected_output' => "(5|11)=1 (11|5)=1 product=1 expected=1 holds=True",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: p-adic valuation ───────────────────────────────────────────
        $seed(8, [
            ['input' => "3\n10",  'expected_output' => "direct: 4\nlegendre: 4\nmatch: True\nterms: floor(10/3)=3 floor(10/9)=1",                    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n8",   'expected_output' => "direct: 7\nlegendre: 7\nmatch: True\nterms: floor(8/2)=4 floor(8/4)=2 floor(8/8)=1",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n25",  'expected_output' => "direct: 6\nlegendre: 6\nmatch: True\nterms: floor(25/5)=5 floor(25/25)=1",                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n16",  'expected_output' => "direct: 15\nlegendre: 15\nmatch: True\nterms: floor(16/2)=8 floor(16/4)=4 floor(16/8)=2 floor(16/16)=1", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q9: Primitive root ─────────────────────────────────────────────
        $seed(9, [
            ['input' => "7",   'expected_output' => "primitive root: 3\n3^1=3 3^2=2 3^3=6 3^4=4 3^5=5 3^6=1\ngenerates_all: True",                       'is_hidden' => false, 'order_index' => 1],
            ['input' => "5",   'expected_output' => "primitive root: 2\n2^1=2 2^2=4 2^3=3 2^4=1\ngenerates_all: True",                                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "11",  'expected_output' => "primitive root: 2\n2^1=2 2^2=4 2^3=8 2^4=5 2^5=10 2^6=9 2^7=7 2^8=3 2^9=6 2^10=1\ngenerates_all: True", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "13",  'expected_output' => "primitive root: 2\n2^1=2 2^2=4 2^3=8 2^4=3 2^5=6 2^6=12 2^7=11 2^8=9 2^9=5 2^10=10 2^11=7 2^12=1\ngenerates_all: True", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q10: Four-square theorem ───────────────────────────────────────
        $seed(10, [
            ['input' => "23",  'expected_output' => "squares: 9 9 4 1\ncount: 4\nsum: 23\nverified: True",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "7",   'expected_output' => "squares: 4 1 1 1\ncount: 4\nsum: 7\nverified: True",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "15",  'expected_output' => "squares: 9 4 1 1\ncount: 4\nsum: 15\nverified: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "25",  'expected_output' => "squares: 25\ncount: 1\nsum: 25\nverified: True",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: FLT small cases ───────────────────────────────────────────
        $seed(11, [
            ['input' => "4\n5",   'expected_output' => "n=1: (1,1,2) (1,2,3) (1,3,4) (2,2,4) (1,4,5) (2,3,5)\nn=2: (3,4,5)\nno solution: n=3\nno solution: n=4", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n4",   'expected_output' => "n=1: (1,1,2) (1,2,3) (1,3,4) (2,2,4)\nn=2: (3,4,5)\nno solution: n=3",                                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n10",  'expected_output' => "n=1: (1,1,2) (1,2,3) (1,3,4) (2,2,4) (1,4,5) (2,3,5) (1,5,6) (2,4,6) (3,3,6) (1,6,7) (2,5,7) (3,4,7) (1,7,8) (2,6,8) (3,5,8) (4,4,8) (1,8,9) (2,7,9) (3,6,9) (4,5,9) (1,9,10) (2,8,10) (3,7,10) (4,6,10) (5,5,10)\nn=2: (3,4,5) (6,8,10)\nno solution: n=3", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5\n3",   'expected_output' => "n=1: (1,1,2) (1,2,3)\nn=2: none\nno solution: n=3\nno solution: n=4\nno solution: n=5",                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Planarity + chromatic number ──────────────────────────────
        $seed(12, [
            ['input' => "4\n6\n1 2\n1 3\n1 4\n2 3\n2 4\n3 4",    'expected_output' => "planar: True\nchromatic_number: 4\nconsistent_with_4CT: True",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3\n1 2\n2 3\n1 3",                    'expected_output' => "planar: True\nchromatic_number: 3\nconsistent_with_4CT: True",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0",                                    'expected_output' => "planar: True\nchromatic_number: 1\nconsistent_with_4CT: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4\n1 2\n2 3\n3 4\n4 1",               'expected_output' => "planar: True\nchromatic_number: 2\nconsistent_with_4CT: True",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Dirichlet AP primes ───────────────────────────────────────
        $seed(13, [
            ['input' => "2\n3 4\n4 6",    'expected_output' => "gcd(3,4)=1 prime found: 7 at position 1\ngcd(4,6)=2 no prime found in range",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n5 6",         'expected_output' => "gcd(5,6)=1 prime found: 11 at position 1",                                      'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1 2",         'expected_output' => "gcd(1,2)=1 prime found: 3 at position 1",                                        'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n9 12",        'expected_output' => "gcd(9,12)=3 no prime found in range",                                            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: PNT ──────────────────────────────────────────────────────
        $seed(14, [
            ['input' => "1000",   'expected_output' => "pi(x): 168\nx/ln(x): 144.764770\nratio: 1.160841\nLi(x): 177.609670",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "100",    'expected_output' => "pi(x): 25\nx/ln(x): 21.714724\nratio: 1.151343\nLi(x): 29.080978",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "500",    'expected_output' => "pi(x): 95\nx/ln(x): 80.299686\nratio: 1.183065\nLi(x): 101.346039",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "200",    'expected_output' => "pi(x): 46\nx/ln(x): 37.926820\nratio: 1.212944\nLi(x): 50.204498",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Ore's theorem / Hamiltonian ──────────────────────────────
        $seed(15, [
            ['input' => "4\n4\n1 2\n2 3\n3 4\n4 1",           'expected_output' => "hamiltonian: True\ncycle: 1 2 3 4 1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2\n1 2\n2 3",                     'expected_output' => "hamiltonian: False\nore_witness: 1 3 deg_sum=2 < n=3", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n1 2\n2 3\n3 4\n4 5\n5 1",     'expected_output' => "hamiltonian: True\ncycle: 1 2 3 4 5 1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n3\n1 2\n2 3\n3 4",               'expected_output' => "hamiltonian: False\nore_witness: 1 4 deg_sum=2 < n=4", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q16: Ramsey R(3,3)=6 ──────────────────────────────────────────
        $seed(16, [
            ['input' => "15\n1 2 0\n1 3 0\n1 4 1\n1 5 1\n1 6 0\n2 3 1\n2 4 0\n2 5 0\n2 6 1\n3 4 0\n3 5 1\n3 6 1\n4 5 0\n4 6 1\n5 6 0", 'expected_output' => "monochromatic triangle: 1 2 4 color=red", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "15\n1 2 1\n1 3 1\n1 4 0\n1 5 0\n1 6 1\n2 3 0\n2 4 1\n2 5 1\n2 6 0\n3 4 1\n3 5 0\n3 6 0\n4 5 1\n4 6 0\n5 6 1", 'expected_output' => "monochromatic triangle: 1 2 4 color=blue", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "15\n1 2 0\n1 3 0\n1 4 0\n1 5 0\n1 6 0\n2 3 0\n2 4 0\n2 5 0\n2 6 0\n3 4 0\n3 5 0\n3 6 0\n4 5 0\n4 6 0\n5 6 0", 'expected_output' => "monochromatic triangle: 1 2 3 color=red", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "15\n1 2 1\n1 3 1\n1 4 1\n1 5 1\n1 6 1\n2 3 1\n2 4 1\n2 5 1\n2 6 1\n3 4 1\n3 5 1\n3 6 1\n4 5 1\n4 6 1\n5 6 1", 'expected_output' => "monochromatic triangle: 1 2 3 color=blue", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Cantor diagonalization ────────────────────────────────────
        $seed(17, [
            ['input' => "3\n1 2\n2 3\n1",            'expected_output' => "D: 3\nf(1)={1,2}: D_equals=False\nf(2)={2,3}: D_equals=False\nf(3)={1}: D_equals=False\nsurjection_disproved: True",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n2",                   'expected_output' => "D: 2\nf(1)={1}: D_equals=False\nf(2)={2}: D_equals=False\nsurjection_disproved: True",                                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n\n1 2 3\n2",             'expected_output' => "D: 1 3\nf(1)={}: D_equals=False\nf(2)={1,2,3}: D_equals=False\nf(3)={2}: D_equals=False\nsurjection_disproved: True",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 2\n",                  'expected_output' => "D: 2\nf(1)={1,2}: D_equals=False\nf(2)={}: D_equals=False\nsurjection_disproved: True",                                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Liouville number ──────────────────────────────────────────
        $seed(18, [
            ['input' => "100",   'expected_output' => "L: 0.11000100000000000000000100000000000000000000000000\nterms_used: 6\nliouville_condition_holds: True",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "50",    'expected_output' => "L: 0.11000100000000000000000100000000000000000000000000\nterms_used: 6\nliouville_condition_holds: True",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "200",   'expected_output' => "L: 0.11000100000000000000000100000000000000000000000000\nterms_used: 6\nliouville_condition_holds: True",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "10",    'expected_output' => "L: 0.11000100000000000000000100000000000000000000000000\nterms_used: 6\nliouville_condition_holds: True",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Pythagorean triples ───────────────────────────────────────
        $seed(19, [
            ['input' => "5",    'expected_output' => "(3,4,5)\n(5,12,13)\n(8,15,17)\n(7,24,25)\ntotal: 4",                     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3",    'expected_output' => "(3,4,5)\ntotal: 1",                                                        'is_hidden' => false, 'order_index' => 2],
            ['input' => "7",    'expected_output' => "(3,4,5)\n(5,12,13)\n(8,15,17)\n(7,24,25)\n(20,21,29)\n(9,40,41)\n(12,35,37)\n(11,60,61)\n(28,45,53)\ntotal: 9", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4",    'expected_output' => "(3,4,5)\n(5,12,13)\n(8,15,17)\ntotal: 3",                                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Irrationality of log2(3) ─────────────────────────────────
        $seed(20, [
            ['input' => "5",    'expected_output' => "checking q=1: 3^q=3 power_of_2=False\nchecking q=2: 3^q=9 power_of_2=False\nchecking q=3: 3^q=27 power_of_2=False\nchecking q=4: 3^q=81 power_of_2=False\nchecking q=5: 3^q=243 power_of_2=False\nno solution found: log2(3) is irrational (confirmed up to q=5)",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3",    'expected_output' => "checking q=1: 3^q=3 power_of_2=False\nchecking q=2: 3^q=9 power_of_2=False\nchecking q=3: 3^q=27 power_of_2=False\nno solution found: log2(3) is irrational (confirmed up to q=3)",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "10",   'expected_output' => "checking q=1: 3^q=3 power_of_2=False\nchecking q=2: 3^q=9 power_of_2=False\nchecking q=3: 3^q=27 power_of_2=False\nchecking q=4: 3^q=81 power_of_2=False\nchecking q=5: 3^q=243 power_of_2=False\nchecking q=6: 3^q=729 power_of_2=False\nchecking q=7: 3^q=2187 power_of_2=False\nchecking q=8: 3^q=6561 power_of_2=False\nchecking q=9: 3^q=19683 power_of_2=False\nchecking q=10: 3^q=59049 power_of_2=False\nno solution found: log2(3) is irrational (confirmed up to q=10)", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1",    'expected_output' => "checking q=1: 3^q=3 power_of_2=False\nno solution found: log2(3) is irrational (confirmed up to q=1)", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q21: QR/QNR cases ─────────────────────────────────────────────
        $seed(21, [
            ['input' => "7",    'expected_output' => "QR: 1 2 4\nQNR: 3 5 6\ncase QR*QR: all_QR=True\ncase QR*QNR: all_QNR=True\ncase QNR*QNR: all_QR=True",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5",    'expected_output' => "QR: 1 4\nQNR: 2 3\ncase QR*QR: all_QR=True\ncase QR*QNR: all_QNR=True\ncase QNR*QNR: all_QR=True",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "11",   'expected_output' => "QR: 1 3 4 5 9\nQNR: 2 6 7 8 10\ncase QR*QR: all_QR=True\ncase QR*QNR: all_QNR=True\ncase QNR*QNR: all_QR=True", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "13",   'expected_output' => "QR: 1 3 4 9 10 12\nQNR: 2 5 6 7 8 11\ncase QR*QR: all_QR=True\ncase QR*QNR: all_QNR=True\ncase QNR*QNR: all_QR=True", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q22: Frobenius coin ────────────────────────────────────────────
        $seed(22, [
            ['input' => "2\n3 5\n7 11",    'expected_output' => "formula: 7\nbrute_force: 7\nmatch: True\nformula: 59\nbrute_force: 59\nmatch: True",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n5 7",          'expected_output' => "formula: 23\nbrute_force: 23\nmatch: True",                                            'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n3 7",          'expected_output' => "formula: 11\nbrute_force: 11\nmatch: True",                                            'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n4 9",          'expected_output' => "formula: 23\nbrute_force: 23\nmatch: True",                                            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Legendre three-square ─────────────────────────────────────
        $seed(23, [
            ['input' => "3\n7\n5\n28",    'expected_output' => "n=7 form=4^a(8b+7): True representable: False a=0 b=0 consistent: True\nn=5 form=4^a(8b+7): False representable: True a=0 b=0 consistent: True\nn=28 form=4^a(8b+7): True representable: False a=2 b=0 consistent: True", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3\n14",       'expected_output' => "n=3 form=4^a(8b+7): False representable: True a=0 b=0 consistent: True\nn=14 form=4^a(8b+7): False representable: True a=0 b=0 consistent: True",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n15",       'expected_output' => "n=1 form=4^a(8b+7): False representable: True a=0 b=0 consistent: True\nn=15 form=4^a(8b+7): False representable: True a=0 b=0 consistent: True",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n112",         'expected_output' => "n=112 form=4^a(8b+7): True representable: False a=4 b=0 consistent: True",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Sylvester-Gallai ─────────────────────────────────────────
        $seed(24, [
            ['input' => "4\n0 0\n1 1\n2 0\n3 1",   'expected_output' => "ordinary line: points 1 3",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0 0\n1 0\n2 0",         'expected_output' => "all collinear: no ordinary line", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 0\n1 0\n0 1\n1 1",   'expected_output' => "ordinary line: points 1 2",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0 0\n1 1\n2 2",         'expected_output' => "all collinear: no ordinary line", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Sigma for prime powers ────────────────────────────────────
        $seed(25, [
            ['input' => "2\n2 3\n3 2",    'expected_output' => "sigma(2^3): direct=15 formula=15 match=True\nsigma(3^2): direct=13 formula=13 match=True",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n5 1",         'expected_output' => "sigma(5^1): direct=6 formula=6 match=True",                                                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2 10",        'expected_output' => "sigma(2^10): direct=2047 formula=2047 match=True",                                            'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n7 3",         'expected_output' => "sigma(7^3): direct=400 formula=400 match=True",                                               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Dilworth's theorem ────────────────────────────────────────
        $seed(26, [
            ['input' => "4\n3\n1 2\n1 3\n2 4",    'expected_output' => "max_antichain: 2 elements: 3 4\nmin_chains: 2\ndilworth_holds: True",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2\n1 2\n2 3",          'expected_output' => "max_antichain: 1 elements: 3\nmin_chains: 1\ndilworth_holds: True",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0",                    'expected_output' => "max_antichain: 4 elements: 1 2 3 4\nmin_chains: 4\ndilworth_holds: True", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5\n4\n1 3\n1 4\n2 4\n2 5", 'expected_output' => "max_antichain: 2 elements: 3 5\nmin_chains: 2\ndilworth_holds: True", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q27: Cayley's formula ──────────────────────────────────────────
        $seed(27, [
            ['input' => "4",   'expected_output' => "pruefer_sequences: 16\ntrees_counted: 16\ncayley_verified: True\nsample_tree_edges: (1,2) (1,3) (1,4)",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3",   'expected_output' => "pruefer_sequences: 3\ntrees_counted: 3\ncayley_verified: True\nsample_tree_edges: (1,2) (1,3)",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "5",   'expected_output' => "pruefer_sequences: 125\ntrees_counted: 125\ncayley_verified: True\nsample_tree_edges: (1,2) (1,3) (1,4) (1,5)", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2",   'expected_output' => "pruefer_sequences: 1\ntrees_counted: 1\ncayley_verified: True\nsample_tree_edges: (1,2)",                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Matrix Fibonacci ──────────────────────────────────────────
        $seed(28, [
            ['input' => "10\n1000",   'expected_output' => "matrix_fib: 55\nrecurrence_fib: 55\nmatch: True\nmatrix: [[89,55],[55,34]]",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n100",     'expected_output' => "matrix_fib: 8\nrecurrence_fib: 8\nmatch: True\nmatrix: [[13,8],[8,5]]",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "20\n1000",   'expected_output' => "matrix_fib: 765\nrecurrence_fib: 765\nmatch: True\nmatrix: [[1236,765],[765,472]]", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n100",     'expected_output' => "matrix_fib: 1\nrecurrence_fib: 1\nmatch: True\nmatrix: [[1,1],[1,0]]",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Burnside's lemma ──────────────────────────────────────────
        $seed(29, [
            ['input' => "4\n2",   'expected_output' => "formula: 6\nenumeration: 6\nmatch: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3",   'expected_output' => "formula: 11\nenumeration: 11\nmatch: True",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n2",   'expected_output' => "formula: 14\nenumeration: 14\nmatch: True",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n2",   'expected_output' => "formula: 8\nenumeration: 8\nmatch: True",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Kirchhoff matrix-tree ─────────────────────────────────────
        $seed(30, [
            ['input' => "4\n5\n1 2\n1 3\n2 3\n2 4\n3 4",   'expected_output' => "laplacian_cofactor: 8\nverified_by_enumeration: 8\nmatch: True",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3\n1 2\n2 3\n1 3",             'expected_output' => "laplacian_cofactor: 3\nverified_by_enumeration: 3\nmatch: True",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4\n1 2\n2 3\n3 4\n4 1",        'expected_output' => "laplacian_cofactor: 4\nverified_by_enumeration: 4\nmatch: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2\n1 2\n2 3",                  'expected_output' => "laplacian_cofactor: 1\nverified_by_enumeration: 1\nmatch: True",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Berlekamp GF(2) ──────────────────────────────────────────
        $seed(31, [
            ['input' => "5\n1 1 0 1 1",   'expected_output' => "factors:\n1 1\n1 1 1\nproduct_verified: True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1 1",        'expected_output' => "factors:\n1 1 1\nproduct_verified: True",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 0 0 1",      'expected_output' => "factors:\n1 1\n1 1 1\nproduct_verified: True",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 0 1",        'expected_output' => "factors:\n1 0 1\nproduct_verified: True",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: RSK correspondence ────────────────────────────────────────
        $seed(32, [
            ['input' => "4\n2 1 4 3",   'expected_output' => "P:\n1 3\n2 4\nQ:\n1 3\n2 4\nrecovered: 2 1 4 3\nverified: True",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3",     'expected_output' => "P:\n1 2 3\nQ:\n1 2 3\nrecovered: 1 2 3\nverified: True",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n3 2 1",     'expected_output' => "P:\n1\n2\n3\nQ:\n1\n2\n3\nrecovered: 3 2 1\nverified: True",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 3 2 4",   'expected_output' => "P:\n1 2 4\n3\nQ:\n1 2 4\n3\nrecovered: 1 3 2 4\nverified: True",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: van der Waerden W(2,3)=9 ─────────────────────────────────
        $seed(33, [
            ['input' => "0 1 0 1 0 1 0 1 0",   'expected_output' => "AP found: 1 3 5 color=0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 0 1 0 0 1 0 0 1",   'expected_output' => "AP found: 1 2 3 color=0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 0 1 0 1 0 1 0 1",   'expected_output' => "AP found: 1 3 5 color=1",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0 1 1 0 1 0 0 1 1",   'expected_output' => "AP found: 2 5 8 color=1",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: LGV lemma ────────────────────────────────────────────────
        $seed(34, [
            ['input' => "2\n4\n0 2\n2 4",   'expected_output' => "matrix:\n3 1\n1 3\ndet: 8\nnon_intersecting_paths: 8",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2\n0 1\n1 2",   'expected_output' => "matrix:\n1 0\n0 1\ndet: 1\nnon_intersecting_paths: 1",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n6\n0 2\n2 4",   'expected_output' => "matrix:\n10 4\n4 10\ndet: 84\nnon_intersecting_paths: 84", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n4\n0 0\n2 4",   'expected_output' => "matrix:\n6 1\n1 1\ndet: 5\nnon_intersecting_paths: 5",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Divisor Nim Grundy ────────────────────────────────────────
        $seed(35, [
            ['input' => "6",    'expected_output' => "0\n1\n2\n1\n2\n3\n2\nfirst losing position > 0: none",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3",    'expected_output' => "0\n1\n2\n1\nfirst losing position > 0: none",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "10",   'expected_output' => "0\n1\n2\n1\n2\n3\n2\n1\n4\n2\n3\nfirst losing position > 0: none", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1",    'expected_output' => "0\n1\nfirst losing position > 0: none",                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Chebyshev + Bertrand ──────────────────────────────────────
        $seed(36, [
            ['input' => "5",    'expected_output' => "n=1 prime=2 psi(2)=0.6931\nn=2 prime=3 psi(4)=1.7918\nn=3 prime=5 psi(6)=2.4849\nn=4 prime=5 psi(8)=3.1781\nn=5 prime=7 psi(10)=3.8712",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3",    'expected_output' => "n=1 prime=2 psi(2)=0.6931\nn=2 prime=3 psi(4)=1.7918\nn=3 prime=5 psi(6)=2.4849",                                                           'is_hidden' => false, 'order_index' => 2],
            ['input' => "7",    'expected_output' => "n=1 prime=2 psi(2)=0.6931\nn=2 prime=3 psi(4)=1.7918\nn=3 prime=5 psi(6)=2.4849\nn=4 prime=5 psi(8)=3.1781\nn=5 prime=7 psi(10)=3.8712\nn=6 prime=7 psi(12)=4.5643\nn=7 prime=11 psi(14)=5.2575", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1",    'expected_output' => "n=1 prime=2 psi(2)=0.6931",                                                                                                                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Smith normal form ─────────────────────────────────────────
        $seed(37, [
            ['input' => "2 3\n2\n0 6\n0 0",      'expected_output' => "invariant factors: 2 6\ndivisibility: 2|6 True",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n1 0\n0 1",          'expected_output' => "invariant factors: 1 1\ndivisibility: 1|1 True",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n2 4\n4 8",          'expected_output' => "invariant factors: 2 0\ndivisibility: n/a",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n3 6\n0 6",          'expected_output' => "invariant factors: 3 6\ndivisibility: 3|6 True",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: CRT ──────────────────────────────────────────────────────
        $seed(38, [
            ['input' => "3\n2 3\n3 5\n2 7",    'expected_output' => "x: 23\nM: 105\nverified: True",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 3\n2 5",         'expected_output' => "x: 7\nM: 15\nverified: True",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 7\n0 11",        'expected_output' => "x: 0\nM: 77\nverified: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 2\n1 3\n1 5",    'expected_output' => "x: 1\nM: 30\nverified: True",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Sylvester matrix / resultant ─────────────────────────────
        $seed(39, [
            ['input' => "3\n-6 1 1\n3\n2 -3 1",   'expected_output' => "resultant: 0\ncommon_roots: 2.0000\nconsistent: True",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2 0 1\n2\n-1 1",      'expected_output' => "resultant: 3\ncommon_roots: none\nconsistent: True",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n-2 3 -1\n3\n-1 0 1",  'expected_output' => "resultant: 0\ncommon_roots: 1.0000\nconsistent: True",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n-1 1\n2\n-2 1",       'expected_output' => "resultant: 1\ncommon_roots: none\nconsistent: True",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Haar measure / group axioms ──────────────────────────────
        $seed(40, [
            ['input' => "2\n0 1\n1 0",                     'expected_output' => "is_group: True\nhaar_unique: True",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0 1 2\n1 2 0\n2 0 1",          'expected_output' => "is_group: True\nhaar_unique: True",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 1 2 3\n1 0 3 2\n2 3 0 1\n3 2 1 0", 'expected_output' => "is_group: True\nhaar_unique: True", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0 1\n0 1",                     'expected_output' => "is_group: False\nhaar_unique: False",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Euler sum of powers conjecture ────────────────────────────
        $seed(41, [
            ['input' => "5\n150",    'expected_output' => "LHS: 4215885696344350028\nRHS: 4215885696344350028\ncounterexample_verified: True\nadditional found: none",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n100",    'expected_output' => "LHS: 4215885696344350028\nRHS: 4215885696344350028\ncounterexample_verified: True\nadditional found: none",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n200",    'expected_output' => "LHS: 4215885696344350028\nRHS: 4215885696344350028\ncounterexample_verified: True\nadditional found: none",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n50",     'expected_output' => "LHS: 4215885696344350028\nRHS: 4215885696344350028\ncounterexample_verified: True\nadditional found: none",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Mertens conjecture ────────────────────────────────────────
        $seed(42, [
            ['input' => "10",   'expected_output' => "M(1)=1 ratio=1.000000\nM(2)=0 ratio=0.000000\nM(3)=-1 ratio=0.577350\nM(4)=-1 ratio=0.500000\nM(5)=-2 ratio=0.894427\nM(6)=-1 ratio=0.408248\nM(7)=-2 ratio=0.755929\nM(8)=-2 ratio=0.707107\nM(9)=-2 ratio=0.666667\nM(10)=-1 ratio=0.316228\nmax |M(n)|/sqrt(n): 1.000000\nmertens_holds_in_range: True",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5",    'expected_output' => "M(1)=1 ratio=1.000000\nM(2)=0 ratio=0.000000\nM(3)=-1 ratio=0.577350\nM(4)=-1 ratio=0.500000\nM(5)=-2 ratio=0.894427\nmax |M(n)|/sqrt(n): 1.000000\nmertens_holds_in_range: True",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3",    'expected_output' => "M(1)=1 ratio=1.000000\nM(2)=0 ratio=0.000000\nM(3)=-1 ratio=0.577350\nmax |M(n)|/sqrt(n): 1.000000\nmertens_holds_in_range: True",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1",    'expected_output' => "M(1)=1 ratio=1.000000\nmax |M(n)|/sqrt(n): 1.000000\nmertens_holds_in_range: True",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Goldbach ──────────────────────────────────────────────────
        $seed(43, [
            ['input' => "20",   'expected_output' => "4: pairs=1 strong=2+2\n6: pairs=1 strong=3+3\n8: pairs=1 strong=3+5\n10: pairs=2 strong=5+5\n12: pairs=1 strong=5+7\n14: pairs=2 strong=7+7\n16: pairs=2 strong=5+11\n18: pairs=2 strong=7+11\n20: pairs=2 strong=7+13\nno counterexample found",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "10",   'expected_output' => "4: pairs=1 strong=2+2\n6: pairs=1 strong=3+3\n8: pairs=1 strong=3+5\n10: pairs=2 strong=5+5\nno counterexample found",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "6",    'expected_output' => "4: pairs=1 strong=2+2\n6: pairs=1 strong=3+3\nno counterexample found",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4",    'expected_output' => "4: pairs=1 strong=2+2\nno counterexample found",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Polya's conjecture ────────────────────────────────────────
        $seed(44, [
            ['input' => "10",   'expected_output' => "L(1)=1\nL(2)=0\nL(3)=-1\nL(4)=0\nL(5)=-1\nL(6)=0\nL(7)=-1\nL(8)=-2\nL(9)=-1\nL(10)=-2\nmin L(n): -2\npolya_holds_in_range: False",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5",    'expected_output' => "L(1)=1\nL(2)=0\nL(3)=-1\nL(4)=0\nL(5)=-1\nmin L(n): -1\npolya_holds_in_range: False",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3",    'expected_output' => "L(1)=1\nL(2)=0\nL(3)=-1\nmin L(n): -1\npolya_holds_in_range: False",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1",    'expected_output' => "L(1)=1\nmin L(n): 1\npolya_holds_in_range: True",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: LPS conjecture ────────────────────────────────────────────
        $seed(45, [
            ['input' => "3\n10",   'expected_output' => "3^3 + 4^3 + 5^3 = 6^3\ncounterexample: m=3 = k=3 (not a counterexample, m=k)\nno counterexample found",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5",    'expected_output' => "3^3 + 4^3 + 5^3 = 6^3\ncounterexample: m=3 = k=3 (not a counterexample, m=k)\nno counterexample found",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n20",   'expected_output' => "3^3 + 4^3 + 5^3 = 6^3\ncounterexample: m=3 = k=3 (not a counterexample, m=k)\nno counterexample found",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n50",   'expected_output' => "no counterexample found",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Strategy engine ───────────────────────────────────────────
        $seed(46, [
            ['input' => "3\nDIOPHANTINE 3 5 1\nDIVISIBILITY 10 3\nPIGEONHOLE 10 3",   'expected_output' => "DIOPHANTINE: x=2 y=-1\nDIVISIBILITY: True (3|10^3-10=990)\nPIGEONHOLE: guaranteed 4 in same box",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nEXTREMAL 6\nPROBABILISTIC 6 3",                             'expected_output' => "EXTREMAL: max_sum=12\nPROBABILISTIC: expected=5.5000",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nDIOPHANTINE 4 6 2",                                         'expected_output' => "DIOPHANTINE: x=2 y=-1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nPIGEONHOLE 7 3",                                            'expected_output' => "PIGEONHOLE: guaranteed 3 in same box",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Proof cost model ──────────────────────────────────────────
        $seed(47, [
            ['input' => "2\n8\n100",      'expected_output' => "n=8: direct=8 contrapositive=24 contradiction=3 induction=3 cases=2 recommended=cases\nn=100: direct=100 contrapositive=700 contradiction=10 induction=7 cases=5 recommended=cases",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n4",           'expected_output' => "n=4: direct=4 contrapositive=8 contradiction=2 induction=2 cases=2 recommended=cases",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1000",        'expected_output' => "n=1000: direct=1000 contrapositive=10000 contradiction=32 induction=10 cases=10 recommended=cases",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n2",           'expected_output' => "n=2: direct=2 contrapositive=2 contradiction=2 induction=1 cases=2 recommended=induction",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Proof portfolio verifier ──────────────────────────────────
        $seed(48, [
            ['input' => "2\nTHEOREM T1 FERMAT 7 3\nTHEOREM T2 WILSON 7",                    'expected_output' => "T1: FLT(7,3,1) verified=True\nT2: W(7,6) verified=True",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nTHEOREM A BEZOUT 48 18\nTHEOREM B EULER 10",                    'expected_output' => "A: B(48,18,6,1,-2) verified=True\nB: E(10,4) verified=True",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nTHEOREM X FERMAT 11 5",                                         'expected_output' => "X: FLT(11,5,1) verified=True",                                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nTHEOREM Z WILSON 11",                                           'expected_output' => "Z: W(11,10) verified=True",                                       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Circuit lower bounds ──────────────────────────────────────
        $seed(49, [
            ['input' => "2\n0 1 1 0",   'expected_output' => "eliminate x1=0: 1 0\neliminate x1=1: 0 1\nlower_bound: 2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 1 1 1",   'expected_output' => "eliminate x1=0: 1 1\neliminate x1=1: 1 1\nlower_bound: 0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0 0 0 1",   'expected_output' => "eliminate x1=0: 0 0\neliminate x1=1: 0 1\nlower_bound: 1",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 0 0 0",   'expected_output' => "eliminate x1=0: 1 0\neliminate x1=1: 0 0\nlower_bound: 1",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Grand capstone ────────────────────────────────────────────
        $seed(50, [
            ['input' => "12",   'expected_output' => "1. direct: 12*13*14=2184 div6=True\n2. contrapositive: factor=2\n3. contradiction: sqrt(12) irrational (not perfect square)\n4. cases_mod2: n=12(even) n*(n+1)=156 div2=True\n5. cases_mod3: n^2 mod3=0\n6. induction: sum=78 formula=78 match=True\n7. strong_induction: 8+3+1=12 fibs=[8,3,1]\n8. existence: prime=13\n9. uniqueness: 12=2^2*3\n10. strategy_choice: trial_division or contradiction",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5",    'expected_output' => "1. direct: 5*6*7=210 div6=True\n2. contrapositive: prime\n3. contradiction: sqrt(5) irrational (not perfect square)\n4. cases_mod2: n=5(odd) n*(n+1)=30 div2=True\n5. cases_mod3: n^2 mod3=1\n6. induction: sum=15 formula=15 match=True\n7. strong_induction: 5+0=5 fibs=[5]\n8. existence: prime=7\n9. uniqueness: 5=2^0*5\n10. strategy_choice: trial_division or contradiction",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "9",    'expected_output' => "1. direct: 9*10*11=990 div6=True\n2. contrapositive: factor=3\n3. contradiction: sqrt(9) rational (perfect square, sqrt=3)\n4. cases_mod2: n=9(odd) n*(n+1)=90 div2=True\n5. cases_mod3: n^2 mod3=0\n6. induction: sum=45 formula=45 match=True\n7. strong_induction: 8+1=9 fibs=[8,1]\n8. existence: prime=11\n9. uniqueness: 9=2^0*9\n10. strategy_choice: trial_division or contradiction",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "16",   'expected_output' => "1. direct: 16*17*18=4896 div6=True\n2. contrapositive: factor=2\n3. contradiction: sqrt(16) rational (perfect square, sqrt=4)\n4. cases_mod2: n=16(even) n*(n+1)=272 div2=True\n5. cases_mod3: n^2 mod3=1\n6. induction: sum=136 formula=136 match=True\n7. strong_induction: 13+3=16 fibs=[13,3]\n8. existence: prime=17\n9. uniqueness: 16=2^4*1\n10. strategy_choice: trial_division or contradiction",   'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('Module 5 Coding (Professional) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}