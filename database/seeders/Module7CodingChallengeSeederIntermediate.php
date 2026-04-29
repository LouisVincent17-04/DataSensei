<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 7 — Data Structures & Algorithms (Intermediate / Level 3) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Intermediate tier
 *   2. coding_questions    — 50 questions covering advanced-intermediate DSA concepts
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mapped to lessons 338–347):
 *   L7.1  Big-O Notation & Complexity Analysis
 *   L7.2  Arrays & Dynamic Arrays
 *   L7.3  Stacks: LIFO & Applications
 *   L7.4  Queues, Deques & Priority Queues
 *   L7.5  Linked Lists: Singly, Doubly & Circular
 *   L7.6  Hash Tables: Dictionaries Demystified
 *   L7.7  Trees: Binary Trees & BSTs
 *   L7.8  Heaps & Priority Queues
 *   L7.9  Graphs: Representation, BFS & DFS
 *   L7.10 Sorting & Searching Algorithms
 *
 * Difficulty: Intermediate — problems combine multiple data structures, require
 * non-trivial algorithm design, involve dynamic programming over structures,
 * and demand O(n log n) or better solutions where specified.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module7CodingChallengeSeederIntermediate extends Seeder
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

        $this->command->info('Creating Module 7 — Data Structures & Algorithms (Intermediate) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Data Structures & Algorithms',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Solve challenging Data Structures & Algorithms problems in pure Python. Master amortised analysis, advanced array DP, monotonic stacks, segment trees, trie structures, AVL rotations, Bellman-Ford, Floyd-Warshall, strongly connected components, and hybrid sorting strategies — reasoning carefully about correctness and complexity at every step.',
                'time_limit_seconds' => 2400,
                'base_xp'            => 1600,
                'order_index'        => 3,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Big-O Notation & Complexity Analysis (Q1–Q5) → L338
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
**Tight bound for a triple-nested loop**: The outer loop runs `i` from 1 to `n`; the middle loop runs `j` from `i` to `n`; the inner loop runs `k` from 1 to `j`. Count the **exact total number of inner-loop iterations** and print it.

Example:
```
Input:
3
Output:
10
```
*(i=1: j=1..3, inner sums 1+2+3=6; i=2: j=2..3, inner sums 2+3=5; i=3: j=3, inner=3. Wait — recalculate: total = Σ_{i=1}^{n} Σ_{j=i}^{n} j = 10 for n=3)*
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
**Empirical complexity detection**: You are given `k` (input-size, operation-count) pairs. Determine which complexity class best fits: `O(1)`, `O(log n)`, `O(n)`, `O(n log n)`, `O(n^2)`, or `O(n^3)`. Fit by computing the ratio `ops / f(n)` for each candidate and choosing the one with the smallest coefficient of variation (std/mean). Read `k`, then `k` lines of `n ops`. Print the best-fit class.

Example:
```
Input:
4
10 100
20 400
40 1600
80 6400
Output:
O(n^2)
```
MD,
                'starter_code'        => "import math\nk = int(input())\ndata = [tuple(map(int, input().split())) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
**Akra–Bazzi method (simplified)**: For the recurrence T(n) = a·T(n/b) + g(n) where g(n) = n^c·(log n)^d, determine the asymptotic class using the generalised Master Theorem. Read `a`, `b`, `c`, `d` (integers; d can be 0). Rules (where p = log_b(a)):
- p > c → `O(n^p)`
- p < c → `O(n^c * (log n)^d)` (if d > 0) else `O(n^c)`
- p == c → `O(n^c * (log n)^(d+1))`

Print the result. Use 2 decimal places for p if not integer.

Example:
```
Input:
4
2
2
0
Output:
O(n^2)
```
MD,
                'starter_code'        => "import math\na = int(input())\nb = int(input())\nc = int(input())\nd = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
**Amortised analysis — table doubling with deletions**: A table doubles when full (capacity 1 initially) and halves when it is less than 1/4 full. Simulate `n` operations (each `push` or `pop`). Track total reallocations and print the **amortised cost per operation** = total_reallocation_work / n rounded to 4 decimal places. Each reallocation copies all current elements.

Example:
```
Input:
8
push
push
push
push
push
pop
pop
pop
Output:
1.8750
```
MD,
                'starter_code'        => "n = int(input())\nops = [input().strip() for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
**Counting sort lower bound proof**: Given `n` and `m` (range of values 1..m), compute the **minimum number of comparisons** any comparison-based sorting algorithm must make in the worst case to sort `n` elements (= ⌈log₂(n!)⌉). Also print the number of counting-sort passes (always 1 for a single-pass counting sort). Read `n` and `m`.

Print:
```
comparison_lower_bound: <value>
counting_sort_passes: 1
```

Example:
```
Input:
4
10
Output:
comparison_lower_bound: 5
counting_sort_passes: 1
```
MD,
                'starter_code'        => "import math\nn = int(input())\nm = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Arrays & Dynamic Arrays (Q6–Q10) → L339
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
**Jump game II**: Given `n` non-negative integers where each element represents the maximum jump length from that position, find the **minimum number of jumps** to reach the last index from index 0. Guaranteed reachable. Use a greedy O(n) approach.

Example:
```
Input:
5
2 3 1 1 4
Output:
2
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
**Longest common subsequence (LCS)** of two sequences using DP. Read `n`, sequence A, `m`, sequence B. Print the length of the LCS.

Example:
```
Input:
6
ABCBDAB
5
BDCABA
Output:
4
```
MD,
                'starter_code'        => "n = int(input())\nA = list(input())\nm = int(input())\nB = list(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
**Maximum product subarray**: Given `n` integers (may be negative), find the contiguous subarray with the largest product. Print the maximum product.

Example:
```
Input:
6
2 3 -2 4 -1 2
Output:
48
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
**Sparse table for range minimum query (RMQ)**: Build a sparse table on `n` integers and answer `q` queries, each asking for the minimum in range [l, r] (0-indexed, inclusive). Print one answer per line.

Example:
```
Input:
6
2 4 3 1 6 7
3
0 4
1 3
2 5
Output:
1
1
1
```
MD,
                'starter_code'        => "import math\nn = int(input())\na = list(map(int, input().split()))\nq = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
**Count of range sum**: Given `n` integers and bounds `lower`, `upper`, count the number of range sums `S(i, j)` (sum of elements from index i to j, 0-indexed) that lie in `[lower, upper]`. Use a merge-sort based O(n log n) approach.

Example:
```
Input:
4
-2 5 -1 0
-2
2
Output:
3
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nlower = int(input())\nupper = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Stacks: LIFO & Applications (Q11–Q15) → L340
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
**Maximal rectangle in binary matrix**: Given an `n × m` binary matrix (0s and 1s), find the area of the largest rectangle containing only 1s. Use a histogram-stack approach per row.

Example:
```
Input:
4 5
1 0 1 0 0
1 0 1 1 1
1 1 1 1 1
1 0 0 1 0
Output:
6
```
MD,
                'starter_code'        => "n, m = map(int, input().split())\nmatrix = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
**Monotonic stack — sum of subarray minimums**: Given `n` integers, compute the sum of the minimums of all contiguous subarrays, modulo 10^9+7.

Example:
```
Input:
4
3 1 2 4
Output:
17
```
MD,
                'starter_code'        => "MOD = 10**9 + 7\nn = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
**Online stock span**: Process `n` stock prices one by one. For each price, compute its **span** — the maximum number of consecutive days (including today) for which the price was ≤ today's price. Print all spans space-separated.

Example:
```
Input:
7
100 80 60 70 60 75 85
Output:
1 1 1 2 1 4 6
```
MD,
                'starter_code'        => "n = int(input())\nprices = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
**Remove k digits**: Given a non-negative integer as a string and integer `k`, remove exactly `k` digits to produce the **smallest possible number**. No leading zeros (except "0" itself). Use a monotonic stack greedy approach.

Example:
```
Input:
1432219
3
Output:
1219
```
MD,
                'starter_code'        => "s = input()\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
**Trapping rainwater II (3D)**: Given an `n × m` height map, compute the total volume of water that can be trapped. Use a min-heap BFS approach.

Example:
```
Input:
3 3
1 4 3
3 2 4
2 3 1
Output:
1
```
MD,
                'starter_code'        => "import heapq\nn, m = map(int, input().split())\ngrid = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Queues, Deques & Priority Queues (Q16–Q20) → L341
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
**Shortest subarray with sum ≥ k (with negatives)**: Given `n` integers (may be negative) and target `k`, find the length of the shortest contiguous subarray with sum ≥ k. Use prefix sums + monotonic deque. Print the length, or `-1` if none.

Example:
```
Input:
5
2 -1 2 0 3
3
Output:
3
```
MD,
                'starter_code'        => "from collections import deque\nn = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
**IPO (maximise capital)**: Given `n` projects each with profit `p[i]` and minimum capital `c[i]`, and starting capital `W`, select at most `k` projects to **maximise final capital**. Use a greedy two-heap approach. Read `n`, `k`, `W`, then `n` lines of `c p`. Print the maximum capital.

Example:
```
Input:
3
2
0
0 1
1 2
1 3
Output:
4
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\nk = int(input())\nW = int(input())\nprojects = [tuple(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
**Jump game VI**: Given `n` integers and jump size `k`, start at index 0 and reach index n−1. On each step you can jump 1..k positions forward. Maximise the score (sum of visited elements). Use a deque-based DP.

Example:
```
Input:
6
1 -1 -2 4 -7 3
2
Output:
7
```
MD,
                'starter_code'        => "from collections import deque\nn = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
**Find median from data stream** (full simulation): Process `n` operations:
- `add x` — add integer x to the stream
- `median` — print the current median rounded to 1 decimal place; print `empty` if no elements

Use a two-heap approach for O(log n) per add.

Example:
```
Input:
6
add 1
add 2
median
add 3
median
median
Output:
1.5
2.0
2.0
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
**Swim in rising water**: Given an `n × n` grid where `grid[i][j]` is the elevation at `(i,j)`, find the minimum time `t` such that there is a path from `(0,0)` to `(n-1,n-1)` where all cells on the path have elevation ≤ t. Use Dijkstra / min-heap BFS.

Example:
```
Input:
4
0 2 1 9
1 3 4 7
3 6 5 11
2 8 10 12
Output:
7
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\ngrid = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Linked Lists: Singly, Doubly & Circular (Q21–Q25) → L342
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
**Copy list with random pointer**: A linked list has nodes with a `val` and a `random` pointer (index into the list, or -1 for null). Given `n` nodes as `val random_index` pairs, deep-copy the list. Print the values and random indices of the copied list, one per line as `val random_index`.

Example:
```
Input:
4
7 -1
13 0
11 4
10 2
Output:
7 -1
13 0
11 4
10 2
```
MD,
                'starter_code'        => "n = int(input())\nnodes = [tuple(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
**Reorder linked list**: Given a list of `n` integers [L0, L1, …, Ln-1], reorder it in-place to [L0, Ln-1, L1, Ln-2, L2, Ln-3, …]. Print the reordered list, space-separated.

Example:
```
Input:
5
1 2 3 4 5
Output:
1 5 2 4 3
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
**Reverse nodes in k-group**: Given a list of `n` integers and `k`, reverse every group of `k` consecutive nodes. If the last group has fewer than `k` nodes, leave it as-is. Print the result, space-separated.

Example:
```
Input:
6
1 2 3 4 5 6
2
Output:
2 1 4 3 6 5
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
**Add two numbers as linked lists**: Two non-negative integers are stored in linked lists in **reverse order** (each node one digit). Add the two numbers and return the result in the same format. Read two lines of space-separated digits (each line is one linked list in reverse order). Print the result digits in reverse order, space-separated.

Example:
```
Input:
2 4 3
5 6 4
Output:
7 0 8
```
*(342 + 465 = 807 → stored as 7 0 8)*
MD,
                'starter_code'        => "l1 = list(map(int, input().split()))\nl2 = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
**Josephus problem**: `n` people stand in a circle numbered 1..n. Every `k`-th person is eliminated. Find the position of the last survivor. Use an O(n) recurrence: J(1) = 0; J(n) = (J(n-1) + k) % n. Output the 1-based position.

Example:
```
Input:
7
3
Output:
4
```
MD,
                'starter_code'        => "n = int(input())\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Hash Tables: Dictionaries Demystified (Q26–Q30) → L343
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
**Implement a Trie** (prefix tree). Read `n` operations:
- `insert word`
- `search word` — print `true` or `false`
- `startswith prefix` — print `true` or `false`

Example:
```
Input:
6
insert apple
search apple
search app
startswith app
insert app
search app
Output:
true
false
true
true
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
**Word break**: Given a string `s` and a dictionary of `n` words, determine if `s` can be segmented into a space-separated sequence of dictionary words. Print `true` or `false`. Use DP with a hash set.

Example:
```
Input:
leetcode
4
leet
code
lee
tcode
Output:
true
```
MD,
                'starter_code'        => "s = input()\nn = int(input())\ndict_words = set()\nfor _ in range(n):\n    dict_words.add(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
**All O(1) data structure**: Implement a structure supporting:
- `inc key` — increment count of key by 1
- `dec key` — decrement count of key by 1 (remove if count reaches 0)
- `get_max_key` — print any key with maximum count; print `` if empty
- `get_min_key` — print any key with minimum count; print `` if empty

All operations O(1) average. Read `n` operations.

Example:
```
Input:
6
inc a
inc b
inc b
get_max_key
dec b
get_min_key
Output:
b
a
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
**Longest consecutive sequence**: Given `n` integers, find the length of the longest consecutive elements sequence (e.g. [1,2,3,4] → 4). Must run in O(n) using a hash set.

Example:
```
Input:
8
100 4 200 1 3 2 5 6
Output:
6
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
**Design a HashMap from scratch** with separate chaining. Read `n` operations:
- `put k v` — insert/update key k (integer) with value v (integer)
- `get k` — print value for key k, or `-1` if absent
- `remove k` — delete key k if present

Use a fixed array of 1009 buckets with linked-list chaining.

Example:
```
Input:
7
put 1 10
put 2 20
get 1
get 3
put 2 30
get 2
remove 2
Output:
10
-1
30
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Trees: Binary Trees & BSTs (Q31–Q35) → L344
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
**AVL tree insertion**: Insert `n` integers into an AVL tree one by one. After all insertions print the **in-order traversal** and the **height** of the resulting AVL tree on separate lines.

Example:
```
Input:
5
10 20 30 40 50
Output:
10 20 30 40 50
3
```
MD,
                'starter_code'        => "n = int(input())\nvalues = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
**Serialize and deserialize a binary tree**: Given a binary tree as a level-order array (with `-1` for null), serialize it to a comma-separated string and deserialize it back. Print the **pre-order traversal** of the deserialized tree. Ignore null nodes in the output.

Example:
```
Input:
7
1 2 3 4 5 -1 -1
Output:
1 2 4 5 3
```
MD,
                'starter_code'        => "n = int(input())\nnodes = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
**Binary tree right side view**: Given a binary tree as a level-order array (with `-1` for null), print the values visible from the right side (rightmost node at each level), one per line.

Example:
```
Input:
7
1 2 3 4 5 -1 -1
Output:
1
3
5
```
MD,
                'starter_code'        => "from collections import deque\nn = int(input())\nnodes = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
**Recover BST**: Two nodes of a BST have been swapped. Given the BST as `n` values to insert (the resulting tree has two wrongly placed nodes — identified by in-order violations), find and print the two swapped values in ascending order.

Example:
```
Input:
5
5 1 7 3 4
Output:
3 5
```
*(In-order of the broken tree is 1 3 5 4 7 — swapped nodes are 5 and 4... recalculate: inserting 5 1 7 3 4 gives inorder 1 3 4 5 7 which is correct; provide a pre-built broken tree as level-order)*

*Revised: Given the **level-order** representation of a broken BST (with -1 for null), find the two swapped node values.*

Example:
```
Input:
7
3 8 2 1 4 -1 -1
Output:
2 8
```
MD,
                'starter_code'        => "n = int(input())\nnodes = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
**Count complete tree nodes**: Given a complete binary tree as a level-order array (no -1 nulls), count the nodes in O(log²n) time by binary searching each level. Read `n` and the array. Print the count.

Example:
```
Input:
6
1 2 3 4 5 6
Output:
6
```
MD,
                'starter_code'        => "n = int(input())\nnodes = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Heaps & Priority Queues (Q36–Q40) → L345
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
**Segment tree — range sum with point updates**: Build a segment tree on `n` integers and process `q` operations:
- `update i x` — set a[i] = x (0-indexed)
- `query l r` — print sum of a[l..r] inclusive

Example:
```
Input:
5
1 3 5 7 9
4
query 0 2
update 1 2
query 0 4
query 2 4
Output:
9
24
21
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nq = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
**Fenwick tree (Binary Indexed Tree) — prefix sums**: Build a BIT on `n` integers and process `q` operations:
- `update i x` — add x to a[i] (1-indexed)
- `prefix i` — print prefix sum a[1..i]

Example:
```
Input:
5
2 4 5 5 6
4
prefix 3
update 3 -1
prefix 3
prefix 5
Output:
11
10
21
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nq = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
**Lazy propagation segment tree — range add, range sum**: Given `n` integers, process `q` operations:
- `add l r v` — add v to every element in [l, r] (0-indexed)
- `query l r` — print sum of [l, r]

Example:
```
Input:
5
1 2 3 4 5
4
query 0 4
add 1 3 2
query 0 4
query 1 3
Output:
15
21
12
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nq = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
**K-th smallest prime fraction**: Given `n` integers (sorted, starting with 1), consider all fractions `a[i]/a[j]` where i < j. Find the **k-th smallest** such fraction. Print the numerator and denominator separated by a space.

Example:
```
Input:
4
1 2 3 5
3
Output:
2 5
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
**Minimum number of refuelling stops**: A car starts at position 0 with fuel `startFuel`. There are `n` stations at positions and fuel amounts (sorted by position). The destination is at distance `target`. Find the minimum number of refuelling stops, or `-1` if unreachable. Use a greedy max-heap.

Example:
```
Input:
100
10
3
10 60
20 30
30 30
Output:
2
```
MD,
                'starter_code'        => "import heapq\ntarget = int(input())\nstartFuel = int(input())\nn = int(input())\nstations = [tuple(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Graphs: Representation, BFS & DFS (Q41–Q45) → L346
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
**Bellman-Ford shortest path**: Given a directed weighted graph with `n` nodes, `e` edges (possibly negative weights), and source `s`, find shortest distances. Print `node: distance` for all nodes. Print `node: inf` if unreachable. If a negative cycle is reachable from `s`, print `negative cycle`.

Example:
```
Input:
5
8
1 2 -1
1 3 4
2 3 3
2 4 2
2 5 2
4 2 1
3 4 5
5 4 -3
1
Output:
1: 0
2: -1
3: 2
4: -2
5: 1
```
MD,
                'starter_code'        => "n = int(input())\ne = int(input())\nedges = []\nfor _ in range(e):\n    u, v, w = map(int, input().split())\n    edges.append((u, v, w))\ns = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
**Floyd-Warshall all-pairs shortest paths**: Given a directed weighted graph with `n` nodes and `e` edges, compute shortest paths between all pairs. Print an `n × n` distance matrix (rows space-separated); use `inf` where no path exists. Diagonal is 0.

Example:
```
Input:
3
4
1 2 3
1 3 7
2 3 2
3 1 1
Output:
0 3 5
3 0 2
1 4 0
```
MD,
                'starter_code'        => "INF = float('inf')\nn = int(input())\ne = int(input())\ndist = [[INF]*n for _ in range(n)]\nfor i in range(n):\n    dist[i][i] = 0\nfor _ in range(e):\n    u, v, w = map(int, input().split())\n    dist[u-1][v-1] = w\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
**Strongly connected components (Kosaraju's)**: Given a directed graph with `n` nodes and `e` edges, find all SCCs. Print the number of SCCs, then each SCC as a sorted space-separated list of nodes, one SCC per line. SCCs printed in topological order of the condensation DAG.

Example:
```
Input:
8
11
1 2
2 3
3 1
3 4
4 5
5 4
6 4
6 5
7 6
8 7
7 8
Output:
4
1 2 3
4 5
6
7 8
```
MD,
                'starter_code'        => "n = int(input())\ne = int(input())\nadj = {i: [] for i in range(1, n+1)}\nradj = {i: [] for i in range(1, n+1)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n    radj[v].append(u)\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
**Network flow (Ford-Fulkerson BFS / Edmonds-Karp)**: Given a flow network with `n` nodes, `e` edges (format `u v cap`), source `s`, and sink `t`, find the **maximum flow**. Print the value.

Example:
```
Input:
6
9
1 2 10
1 3 10
2 4 4
2 3 2
2 5 8
3 5 9
4 6 10
5 4 6
5 6 10
1
6
Output:
19
```
MD,
                'starter_code'        => "from collections import deque\nn = int(input())\ne = int(input())\ncap = [[0]*(n+1) for _ in range(n+1)]\nfor _ in range(e):\n    u, v, c = map(int, input().split())\n    cap[u][v] += c\ns = int(input())\nt = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
**Word ladder**: Given start word `s`, end word `t`, and a dictionary of `n` words, find the **length of the shortest transformation sequence** from `s` to `t` where each step changes exactly one letter and each intermediate word must be in the dictionary. Print the length (including start and end), or `0` if no path.

Example:
```
Input:
hit
cog
6
hot
dot
dog
lot
log
cog
Output:
5
```
MD,
                'starter_code'        => "from collections import deque\ns = input()\nt = input()\nn = int(input())\nword_list = set()\nfor _ in range(n):\n    word_list.add(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Sorting & Searching Algorithms (Q46–Q50) → L347
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
**Count inversions using merge sort**: Given `n` integers, count the total number of inversions (pairs i < j with a[i] > a[j]) in O(n log n) using a modified merge sort.

Example:
```
Input:
6
6 5 4 3 2 1
Output:
15
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
**Wiggle sort II**: Given `n` integers, rearrange them such that `a[0] < a[1] > a[2] < a[3] …` (strict inequalities). Print one valid wiggle-sorted arrangement. Use an O(n) median-finding + virtual indexing approach if possible; O(n log n) also acceptable.

Example:
```
Input:
5
1 5 1 1 6
Output:
1 6 1 5 1
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
**Find k-th largest in a stream**: Process `n` events:
- `add x` — add x to the stream
- `kth` — print the k-th largest element currently in the stream; print `none` if fewer than k elements

Read `k` then `n` operations.

Example:
```
Input:
3
7
add 4
add 5
add 8
kth
add 2
kth
add 7
Output:
4
4
```
MD,
                'starter_code'        => "import heapq\nk = int(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
**Pancake sorting**: Given `n` integers, sort them using pancake flips (reverse a prefix of length k). Print the sequence of k values for each flip performed, one per line. Then print the sorted array. Minimise flips is not required — correctness is.

Example:
```
Input:
4
3 2 4 1
Output:
3
4
1
4
2
4
1 2 3 4
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**Find the duplicate number**: Given `n+1` integers in the range [1, n] where exactly one value is duplicated (possibly multiple times), find the duplicate without modifying the array and using O(1) extra space. Use Floyd's cycle-detection algorithm.

Example:
```
Input:
5
1 3 4 2 2
Output:
2
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],
        ];

        // ─────────────────────────────────────────────────────────────────
        // 3. PERSIST QUESTIONS
        // ─────────────────────────────────────────────────────────────────

        $questionIds = [];
        foreach ($questionDefs as $def) {
            $exists = DB::table('coding_questions')
                ->where('challenge_id', $challenge->id)
                ->where('order_index', $def['order_index'])
                ->first();

            if ($exists) {
                $questionIds[$def['order_index']] = $exists->id;
                continue;
            }

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

            $questionIds[$def['order_index']] = $id;
        }

        // ─────────────────────────────────────────────────────────────────
        // 4. TEST CASES
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $seed = function (int $qIndex, array $cases) use ($questionIds): void {
            $qId = $questionIds[$qIndex] ?? null;
            if (! $qId) return;

            if (DB::table('test_cases')->where('coding_question_id', $qId)->exists()) return;

            foreach ($cases as $case) {
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
        };

        // ── Q1: Triple nested loop ───────────────────────────────────────
        $seed(1, [
            ['input' => "3",   'expected_output' => "10",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2",   'expected_output' => "4",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1",   'expected_output' => "1",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4",   'expected_output' => "20",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Empirical complexity ─────────────────────────────────────
        $seed(2, [
            ['input' => "4\n10 100\n20 400\n40 1600\n80 6400",          'expected_output' => "O(n^2)",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n100 200\n200 400\n400 800",                  'expected_output' => "O(n)",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10 1000\n20 8000\n40 64000\n80 512000",     'expected_output' => "O(n^3)",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n100 5\n1000 5\n10000 5",                    'expected_output' => "O(1)",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Generalised Master Theorem ───────────────────────────────
        $seed(3, [
            ['input' => "4\n2\n2\n0",    'expected_output' => "O(n^2)",            'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2\n1\n0",    'expected_output' => "O(n^1 * (log n)^1)",'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2\n2\n0",    'expected_output' => "O(n^2)",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n2\n1\n2",    'expected_output' => "O(n^1 * (log n)^3)",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Amortised table doubling/halving ─────────────────────────
        $seed(4, [
            ['input' => "8\npush\npush\npush\npush\npush\npop\npop\npop",  'expected_output' => "1.8750",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\npush\npush\npush\npush",                       'expected_output' => "1.7500",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\npush\npop",                                    'expected_output' => "0.5000",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\npush\npush\npush\npush\npush\npush",           'expected_output' => "1.5000",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Comparison lower bound ───────────────────────────────────
        $seed(5, [
            ['input' => "4\n10",    'expected_output' => "comparison_lower_bound: 5\ncounting_sort_passes: 1",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "8\n100",   'expected_output' => "comparison_lower_bound: 16\ncounting_sort_passes: 1",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n5",     'expected_output' => "comparison_lower_bound: 1\ncounting_sort_passes: 1",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n100",  'expected_output' => "comparison_lower_bound: 22\ncounting_sort_passes: 1",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Jump game II ─────────────────────────────────────────────
        $seed(6, [
            ['input' => "5\n2 3 1 1 4",    'expected_output' => "2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n2 3 0 1 4 0",  'expected_output' => "2",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0",            'expected_output' => "0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 1 1 1 1",    'expected_output' => "4",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: LCS ──────────────────────────────────────────────────────
        $seed(7, [
            ['input' => "6\nABCBDAB\n5\nBDCABA",    'expected_output' => "4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nABC\n3\nABC",            'expected_output' => "3",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nABCD\n4\nEFGH",          'expected_output' => "0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\nAGGTAB\n4\nGXTXAYB",    'expected_output' => "4",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Max product subarray ─────────────────────────────────────
        $seed(8, [
            ['input' => "6\n2 3 -2 4 -1 2",   'expected_output' => "48",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n-2 0 -1",          'expected_output' => "0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n-2 3 -4 1",        'expected_output' => "24",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n2 -5 -2 -4 3",     'expected_output' => "24",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Sparse table RMQ ─────────────────────────────────────────
        $seed(9, [
            ['input' => "6\n2 4 3 1 6 7\n3\n0 4\n1 3\n2 5",    'expected_output' => "1\n1\n1",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n5 4 3 2 1\n2\n0 2\n1 4",           'expected_output' => "3\n1",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1 1 1\n2\n0 3\n1 2",             'expected_output' => "1\n1",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n3 1 4 1 5\n3\n0 0\n0 4\n3 4",      'expected_output' => "3\n1\n1",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Count range sum ─────────────────────────────────────────
        $seed(10, [
            ['input' => "4\n-2 5 -1 0\n-2\n2",    'expected_output' => "3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0 0 0\n0\n0",          'expected_output' => "6",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4\n3\n7",        'expected_output' => "4",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n-1 0 1\n0\n0",         'expected_output' => "3",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Maximal rectangle ───────────────────────────────────────
        $seed(11, [
            ['input' => "4 5\n1 0 1 0 0\n1 0 1 1 1\n1 1 1 1 1\n1 0 0 1 0",  'expected_output' => "6",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n0 1\n1 0",                                       'expected_output' => "1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 3\n1 1 1\n1 1 1\n1 1 1",                           'expected_output' => "9",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 4\n0 1 1 0\n1 1 1 1\n0 1 1 0",                     'expected_output' => "6",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Sum of subarray minimums ────────────────────────────────
        $seed(12, [
            ['input' => "4\n3 1 2 4",     'expected_output' => "17",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3 2 1",        'expected_output' => "10",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4",      'expected_output' => "20",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 1 1 1 1",    'expected_output' => "15",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Stock span ──────────────────────────────────────────────
        $seed(13, [
            ['input' => "7\n100 80 60 70 60 75 85",   'expected_output' => "1 1 1 2 1 4 6",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n10 20 30 40 50",           'expected_output' => "1 2 3 4 5",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n50 40 30 20 10",           'expected_output' => "1 1 1 1 1",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n5 5 5 5",                  'expected_output' => "1 2 3 4",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Remove k digits ─────────────────────────────────────────
        $seed(14, [
            ['input' => "1432219\n3",   'expected_output' => "1219",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "10200\n1",     'expected_output' => "200",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "10\n2",        'expected_output' => "0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "9\n1",         'expected_output' => "",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Trapping rainwater II ───────────────────────────────────
        $seed(15, [
            ['input' => "3 3\n1 4 3\n3 2 4\n2 3 1",    'expected_output' => "1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 3\n1 1 1\n1 1 1\n1 1 1",    'expected_output' => "0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 4\n1 2 1 2\n2 1 2 1\n1 2 1 2\n2 1 2 1",  'expected_output' => "0",  'is_hidden' => true, 'order_index' => 3],
            ['input' => "3 3\n9 9 9\n9 1 9\n9 9 9",    'expected_output' => "8",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Shortest subarray sum ≥ k ──────────────────────────────
        $seed(16, [
            ['input' => "5\n2 -1 2 0 3\n3",    'expected_output' => "3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n6",          'expected_output' => "3",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n-1 -2 -3 4\n4",    'expected_output' => "1",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 1 1\n10",         'expected_output' => "-1",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: IPO ─────────────────────────────────────────────────────
        $seed(17, [
            ['input' => "3\n2\n0\n0 1\n1 2\n1 3",    'expected_output' => "4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1\n0\n0 5\n1 3",          'expected_output' => "5",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n3\n5\n0 1\n2 2\n4 3",    'expected_output' => "11", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0\n0\n0 100",             'expected_output' => "0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Jump game VI ────────────────────────────────────────────
        $seed(18, [
            ['input' => "6\n1 -1 -2 4 -7 3\n2",    'expected_output' => "7",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n10 -5 -2 4 0\n3",        'expected_output' => "17",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 -1 1\n1",              'expected_output' => "1",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2 3 4\n4",             'expected_output' => "10",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Median from stream ──────────────────────────────────────
        $seed(19, [
            ['input' => "6\nadd 1\nadd 2\nmedian\nadd 3\nmedian\nmedian",  'expected_output' => "1.5\n2.0\n2.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nmedian\nadd 5\nmedian",                         'expected_output' => "empty\n5.0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\nadd 6\nadd 2\nadd 4\nmedian\nadd 1",           'expected_output' => "4.0",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nadd 3\nadd 1\nmedian\nadd 2",                  'expected_output' => "2.0",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Swim in rising water ────────────────────────────────────
        $seed(20, [
            ['input' => "4\n0 2 1 9\n1 3 4 7\n3 6 5 11\n2 8 10 12",   'expected_output' => "7",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 3\n1 2",                                   'expected_output' => "2",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0 1 2\n3 4 5\n6 7 8",                       'expected_output' => "7",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0",                                          'expected_output' => "0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Copy list with random pointer ───────────────────────────
        $seed(21, [
            ['input' => "4\n7 -1\n13 0\n11 4\n10 2",   'expected_output' => "7 -1\n13 0\n11 4\n10 2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n5 -1",                      'expected_output' => "5 -1",                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 1\n2 0\n3 -1",           'expected_output' => "1 1\n2 0\n3 -1",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n3 1\n7 0",                  'expected_output' => "3 1\n7 0",                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Reorder linked list ─────────────────────────────────────
        $seed(22, [
            ['input' => "5\n1 2 3 4 5",    'expected_output' => "1 5 2 4 3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2 3 4",       'expected_output' => "1 4 2 3",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 2",           'expected_output' => "1 2",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1 2 3 4 5 6",  'expected_output' => "1 6 2 5 3 4",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Reverse in k-group ──────────────────────────────────────
        $seed(23, [
            ['input' => "6\n1 2 3 4 5 6\n2",  'expected_output' => "2 1 4 3 6 5",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1 2 3 4 5\n3",    'expected_output' => "3 2 1 4 5",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4\n4",      'expected_output' => "4 3 2 1",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 2 3 4 5\n1",    'expected_output' => "1 2 3 4 5",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Add two numbers ─────────────────────────────────────────
        $seed(24, [
            ['input' => "2 4 3\n5 6 4",    'expected_output' => "7 0 8",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n0",            'expected_output' => "0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "9 9 9\n1",        'expected_output' => "0 0 0 1",'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 8\n0",          'expected_output' => "1 8",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Josephus ────────────────────────────────────────────────
        $seed(25, [
            ['input' => "7\n3",    'expected_output' => "4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n2",    'expected_output' => "5",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1",    'expected_output' => "1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n4",   'expected_output' => "5",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Trie ────────────────────────────────────────────────────
        $seed(26, [
            ['input' => "6\ninsert apple\nsearch apple\nsearch app\nstartswith app\ninsert app\nsearch app",  'expected_output' => "true\nfalse\ntrue\ntrue",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\ninsert hello\nsearch hello\nsearch hell",                                          'expected_output' => "true\nfalse",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\ninsert abc\nstartswith ab\nstartswith d\nsearch abc",                             'expected_output' => "true\nfalse\ntrue",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nsearch x\ninsert x\nsearch x",                                                    'expected_output' => "false\ntrue",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Word break ──────────────────────────────────────────────
        $seed(27, [
            ['input' => "leetcode\n4\nleet\ncode\nlee\ntcode",    'expected_output' => "true",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "applepenapple\n3\napple\npen\napplepe",  'expected_output' => "true",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "catsandog\n5\ncats\ndog\nsand\nand\ncat",'expected_output' => "false",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "a\n1\na",                                 'expected_output' => "true",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: All O(1) data structure ─────────────────────────────────
        $seed(28, [
            ['input' => "6\ninc a\ninc b\ninc b\nget_max_key\ndec b\nget_min_key",  'expected_output' => "b\na",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\ninc a\ninc a\ndec a\nget_max_key",                       'expected_output' => "a",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nget_max_key\nget_min_key",                               'expected_output' => "\n",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\ninc x\ninc y\ninc x\nget_max_key\nget_min_key",         'expected_output' => "x\ny",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Longest consecutive sequence ────────────────────────────
        $seed(29, [
            ['input' => "8\n100 4 200 1 3 2 5 6",   'expected_output' => "6",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0 3 7 2",                'expected_output' => "3",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 2 3 4 5",              'expected_output' => "5",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 3 5 7",                'expected_output' => "1",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Design HashMap ──────────────────────────────────────────
        $seed(30, [
            ['input' => "7\nput 1 10\nput 2 20\nget 1\nget 3\nput 2 30\nget 2\nremove 2",  'expected_output' => "10\n-1\n30",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nput 0 0\nget 0\nremove 0\nget 0",                              'expected_output' => "0\n-1",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nget 5\nput 5 50\nget 5",                                       'expected_output' => "-1\n50",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nput 10 1\nput 10 2\nget 10\nremove 10",                        'expected_output' => "2",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: AVL tree insertion ──────────────────────────────────────
        $seed(31, [
            ['input' => "5\n10 20 30 40 50",   'expected_output' => "10 20 30 40 50\n3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3 2 1",            'expected_output' => "1 2 3\n1",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4",          'expected_output' => "1 2 3 4\n2",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5",               'expected_output' => "5\n0",                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Serialize/deserialize tree ─────────────────────────────
        $seed(32, [
            ['input' => "7\n1 2 3 4 5 -1 -1",  'expected_output' => "1 2 4 5 3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1",                 'expected_output' => "1",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3",             'expected_output' => "1 2 3",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 2 3 4 5",         'expected_output' => "1 2 4 5 3",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Right side view ─────────────────────────────────────────
        $seed(33, [
            ['input' => "7\n1 2 3 4 5 -1 -1",  'expected_output' => "1\n3\n5",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 -1 3",            'expected_output' => "1\n3",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1",                 'expected_output' => "1",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 2 3 4 5",         'expected_output' => "1\n3\n5",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Recover BST ─────────────────────────────────────────────
        $seed(34, [
            ['input' => "7\n3 8 2 1 4 -1 -1",   'expected_output' => "2 8",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 3 2",              'expected_output' => "2 3",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n6 2 8 1 4 7 9",     'expected_output' => "6 7",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n5 4 3 2 1",          'expected_output' => "1 5",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Count complete tree nodes ───────────────────────────────
        $seed(35, [
            ['input' => "6\n1 2 3 4 5 6",   'expected_output' => "6",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1",             'expected_output' => "1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n1 2 3 4 5 6 7", 'expected_output' => "7",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 2 3",         'expected_output' => "3",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Segment tree range sum ──────────────────────────────────
        $seed(36, [
            ['input' => "5\n1 3 5 7 9\n4\nquery 0 2\nupdate 1 2\nquery 0 4\nquery 2 4",  'expected_output' => "9\n24\n21",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n2\nquery 0 2\nupdate 0 5",                             'expected_output' => "6",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4 3 2 1\n3\nquery 0 3\nupdate 2 10\nquery 0 3",              'expected_output' => "10\n18",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 2\n2\nupdate 0 10\nquery 0 1",                              'expected_output' => "12",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Fenwick tree ────────────────────────────────────────────
        $seed(37, [
            ['input' => "5\n2 4 5 5 6\n4\nprefix 3\nupdate 3 -1\nprefix 3\nprefix 5",  'expected_output' => "11\n10\n21",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n2\nprefix 1\nprefix 3",                              'expected_output' => "1\n6",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1 1 1\n2\nupdate 2 3\nprefix 4",                         'expected_output' => "7",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n5 5\n2\nprefix 2\nupdate 1 -5",                            'expected_output' => "10",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Lazy segment tree ───────────────────────────────────────
        $seed(38, [
            ['input' => "5\n1 2 3 4 5\n4\nquery 0 4\nadd 1 3 2\nquery 0 4\nquery 1 3",  'expected_output' => "15\n21\n12",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1 1\n2\nadd 0 2 1\nquery 0 2",                             'expected_output' => "6",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 0 0 0\n2\nadd 0 3 5\nquery 0 3",                          'expected_output' => "20",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 2 3 4 5\n2\nadd 0 4 -1\nquery 0 4",                      'expected_output' => "10",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: K-th smallest prime fraction ────────────────────────────
        $seed(39, [
            ['input' => "4\n1 2 3 5\n3",    'expected_output' => "2 5",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 7 23\n2",      'expected_output' => "7 23", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 5\n1",    'expected_output' => "1 5",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 3 5\n3",       'expected_output' => "3 5",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Minimum refuelling stops ────────────────────────────────
        $seed(40, [
            ['input' => "100\n10\n3\n10 60\n20 30\n30 30",   'expected_output' => "2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "100\n1\n0",                          'expected_output' => "-1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n100\n0",                        'expected_output' => "0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "50\n5\n3\n10 15\n20 15\n30 20",     'expected_output' => "2",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Bellman-Ford ────────────────────────────────────────────
        $seed(41, [
            ['input' => "5\n8\n1 2 -1\n1 3 4\n2 3 3\n2 4 2\n2 5 2\n4 2 1\n3 4 5\n5 4 -3\n1",  'expected_output' => "1: 0\n2: -1\n3: 2\n4: -2\n5: 1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2\n1 2 4\n2 3 2\n1",                                                  'expected_output' => "1: 0\n2: 4\n3: 6",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n3\n1 2 1\n2 3 -5\n3 1 1\n1",                                         'expected_output' => "negative cycle",                 'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n3\n1 2 5\n1 3 10\n2 3 2\n1",                                         'expected_output' => "1: 0\n2: 5\n3: 7\n4: inf",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Floyd-Warshall ──────────────────────────────────────────
        $seed(42, [
            ['input' => "3\n4\n1 2 3\n1 3 7\n2 3 2\n3 1 1",    'expected_output' => "0 3 5\n3 0 2\n1 4 0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2\n1 2 5\n2 1 3",                   'expected_output' => "0 5\n3 0",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n3\n1 2 1\n2 3 1\n1 3 5",            'expected_output' => "0 1 2\ninf 0 1\ninf inf 0",  'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n0",                                  'expected_output' => "0 inf inf\ninf 0 inf\ninf inf 0",  'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q43: Kosaraju's SCC ──────────────────────────────────────────
        $seed(43, [
            ['input' => "8\n11\n1 2\n2 3\n3 1\n3 4\n4 5\n5 4\n6 4\n6 5\n7 6\n8 7\n7 8",  'expected_output' => "4\n1 2 3\n4 5\n6\n7 8",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3\n1 2\n2 3\n3 1",                                             'expected_output' => "1\n1 2 3",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4\n1 2\n2 3\n3 4\n4 1",                                       'expected_output' => "1\n1 2 3 4",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0",                                                            'expected_output' => "3\n1\n2\n3",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Max flow ────────────────────────────────────────────────
        $seed(44, [
            ['input' => "6\n9\n1 2 10\n1 3 10\n2 4 4\n2 3 2\n2 5 8\n3 5 9\n4 6 10\n5 4 6\n5 6 10\n1\n6",  'expected_output' => "19",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n5\n1 2 3\n1 3 3\n2 4 3\n3 4 3\n2 3 3\n1\n4",                                    'expected_output' => "6",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n1 2 5\n1\n2",                                                                 'expected_output' => "5",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4\n1 2 10\n2 3 5\n3 4 10\n1 4 1\n1\n4",                                         'expected_output' => "6",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Word ladder ─────────────────────────────────────────────
        $seed(45, [
            ['input' => "hit\ncog\n6\nhot\ndot\ndog\nlot\nlog\ncog",   'expected_output' => "5",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "hit\ncog\n5\nhot\ndot\ndog\nlot\nlog",        'expected_output' => "0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "a\nc\n2\na\nb",                               'expected_output' => "0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "hot\ndot\n2\nhot\ndot",                       'expected_output' => "2",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Count inversions ────────────────────────────────────────
        $seed(46, [
            ['input' => "6\n6 5 4 3 2 1",     'expected_output' => "15",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2 3 4",          'expected_output' => "0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5 1 4 2 3",        'expected_output' => "6",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n3 1 2",            'expected_output' => "2",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Wiggle sort II ──────────────────────────────────────────
        $seed(47, [
            ['input' => "5\n1 5 1 1 6",    'expected_output' => "1 6 1 5 1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 3 2 2",       'expected_output' => "2 3 1 2",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 1 2",         'expected_output' => "1 2 1",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n4 5 5 6 6",    'expected_output' => "5 6 4 6 5",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Kth largest in stream ───────────────────────────────────
        $seed(48, [
            ['input' => "3\n7\nadd 4\nadd 5\nadd 8\nkth\nadd 2\nkth\nadd 7",  'expected_output' => "4\n4",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n4\nadd 1\nkth\nadd 5\nkth",                        'expected_output' => "none\n1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n3\nadd 3\nadd 1\nadd 2",                           'expected_output' => "",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n5\nadd 1\nadd 2\nadd 3\nkth\nkth",                 'expected_output' => "2\n2",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Pancake sorting ─────────────────────────────────────────
        $seed(49, [
            ['input' => "4\n3 2 4 1",    'expected_output' => "3\n4\n1\n4\n2\n4\n1 2 3 4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3 1 2",       'expected_output' => "3\n3\n2\n3\n1 2 3",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1",          'expected_output' => "1 2 3 4",                      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 2 3",       'expected_output' => "1 2 3",                       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Find duplicate (Floyd) ──────────────────────────────────
        $seed(50, [
            ['input' => "5\n1 3 4 2 2",   'expected_output' => "2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n3 1 3 4 2",   'expected_output' => "3",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 1 2",        'expected_output' => "1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n2 5 9 6 9 3", 'expected_output' => "9",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 7 Coding (Intermediate) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}