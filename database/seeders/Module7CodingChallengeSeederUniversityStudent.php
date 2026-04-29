<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 7 — Data Structures & Algorithms (University Student / Level 2) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the University Student tier
 *   2. coding_questions    — 50 questions covering intermediate DSA concepts
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
 * Difficulty: University Student — problems require deeper algorithmic reasoning,
 * involve combined data structures, and demand correct complexity analysis.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module7CodingChallengeSeederUniversityStudent extends Seeder
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

        $this->command->info('Creating Module 7 — Data Structures & Algorithms (University Student) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Data Structures & Algorithms',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Tackle intermediate Data Structures & Algorithms problems using pure Python. Apply amortised complexity arguments, implement advanced array techniques, design multi-structure solutions with stacks and queues, build and balance BSTs, leverage hash tables for optimal lookup, implement graph algorithms with weighted edges, and analyse algorithm correctness — all from scratch.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 1200,
                'order_index'        => 2,
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
Compute the **exact number of iterations** of the following nested-loop pattern: the outer loop runs `i` from 1 to `n`; the inner loop runs `j` from 1 to `i*i`. Read `n` and print the total iteration count.

Example:
```
Input:
3
Output:
14
```
*(Outer i=1: 1 inner; i=2: 4 inner; i=3: 9 inner → total 14)*
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
**Amortised cost analysis**: A dynamic array doubles its capacity whenever it is full. Starting with capacity 1, simulate `n` push operations. Each push costs 1; each doubling copies all current elements (cost = current size). Read `n`. Print the **total cost** of all `n` pushes.

Example:
```
Input:
5
Output:
12
```
*(Pushes at size 1,2,4 trigger copies; total = 5 pushes + 1+2+4 copy costs = 12)*
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Given `k` code snippets described by their loop structures, determine the **tightest Big-O** for each. Read `k` lines, each containing two integers `a` (power of outer loop) and `b` (power of inner loop relative to outer). The total work per snippet is O(n^(a+b)). Print the complexity as `O(n^p)` where `p = a + b`. If `p == 0` print `O(1)`, if `p == 1` print `O(n)`.

Example:
```
Input:
3
1 1
2 0
0 0
Output:
O(n^2)
O(n^2)
O(1)
```
MD,
                'starter_code'        => "k = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
**Recurrence solving**: Given a recurrence T(n) = a·T(n/b) + n^c (Master Theorem), determine which case applies and print the asymptotic complexity. Read `a`, `b`, `c` (integers). Apply the Master Theorem:
- If log_b(a) > c → print `O(n^log_b(a))` (rounded to 2 decimal places if not integer)
- If log_b(a) == c → print `O(n^c * log n)`
- If log_b(a) < c → print `O(n^c)`

Example:
```
Input:
4
2
1
Output:
O(n^2)
```
*(log_2(4) = 2 == c=1? No. log_b(a)=2 > c=1 → O(n^2))*
MD,
                'starter_code'        => "import math\na = int(input())\nb = int(input())\nc = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
**Space-complexity analysis**: Given `n`, determine the maximum memory (number of integers stored at any time) used by a recursive Fibonacci implementation with memoisation. It stores results for indices 0 through n. Read `n` and print `n+1`.

Then, without memoisation (pure recursion), the call stack depth is `n`. Print `stack_depth: n`.

Example:
```
Input:
6
Output:
memo_size: 7
stack_depth: 6
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Arrays & Dynamic Arrays (Q6–Q10) → L339
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Given `n` integers, find the **minimum length subarray** whose sum is ≥ target `s`. Print the minimum length. If no such subarray exists, print `0`. Use the sliding window approach.

Example:
```
Input:
6
2 3 1 2 4 3
7
Output:
2
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\ns = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Given an `n × m` matrix (row-major, `n` rows each with `m` integers), compute the **prefix sum matrix** and answer `q` range-sum queries. Each query gives `r1 c1 r2 c2` (0-indexed, inclusive). Print the sum for each query on a new line.

Example:
```
Input:
2 3
1 2 3
4 5 6
1
0 0 1 2
Output:
21
```
MD,
                'starter_code'        => "n, m = map(int, input().split())\ngrid = [list(map(int, input().split())) for _ in range(n)]\nq = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Given `n` integers, find the length of the **longest increasing subsequence** (LIS) using an O(n log n) patience-sorting approach.

Example:
```
Input:
8
10 9 2 5 3 7 101 18
Output:
4
```
MD,
                'starter_code'        => "import bisect\nn = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
**Trapping rainwater**: Given `n` non-negative integers representing an elevation map, compute how much water can be trapped. Print the total water units.

Example:
```
Input:
12
0 1 0 2 1 0 1 3 2 1 2 1
Output:
6
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Given `n` integers, find the **three elements** that sum closest to target `t`. Print the closest sum. (Use a sorted array + two-pointer for O(n²) solution.)

Example:
```
Input:
4
-1 2 1 -4
1
Output:
2
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nt = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Stacks: LIFO & Applications (Q11–Q15) → L340
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Design a **stack with O(1) minimum retrieval**. Read `n` operations:
- `push x`
- `pop` — remove top; print `empty` if empty
- `get_min` — print current minimum; print `empty` if empty

Example:
```
Input:
6
push 5
push 3
push 7
get_min
pop
get_min
Output:
3
3
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Compute the **largest rectangle in a histogram**. Read `n` bar heights. Print the area of the largest rectangle.

Example:
```
Input:
6
2 1 5 6 2 3
Output:
10
```
MD,
                'starter_code'        => "n = int(input())\nheights = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Convert an **infix expression** to **postfix** (Reverse Polish Notation) using the shunting-yard algorithm. Operators: `+`, `-`, `*`, `/` with standard precedence (`*` and `/` > `+` and `-`), all left-associative. Operands are single lowercase letters or digits. Print the postfix expression with tokens space-separated.

Example:
```
Input:
a + b * c - d
Output:
a b c * + d -
```
MD,
                'starter_code'        => "tokens = input().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
**Decode string**: Given an encoded string where `k[encoded_string]` means the `encoded_string` repeated `k` times, decode it. Numbers can be multi-digit. Read the encoded string and print the decoded result.

Example:
```
Input:
3[a]2[bc]
Output:
aaabcbc
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
**Asteroid collision**: Given `n` integers representing asteroids moving in a row (positive = right, negative = left). When two collide, the smaller explodes; equal sizes both explode. Read `n` integers and print the surviving asteroids, space-separated.

Example:
```
Input:
4
5 10 -5 -10
Output:
5
```
MD,
                'starter_code'        => "n = int(input())\nasteroids = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Queues, Deques & Priority Queues (Q16–Q20) → L341
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Implement a **queue using two stacks**. Read `n` operations:
- `enqueue x`
- `dequeue` — print dequeued value or `empty`

Example:
```
Input:
5
enqueue 1
enqueue 2
dequeue
enqueue 3
dequeue
Output:
1
2
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
**Task scheduler**: Given `n` tasks (each with a character label) and cooldown `k` (same task must wait at least `k` intervals), find the minimum number of intervals to complete all tasks. Read `k`, then `n` task labels space-separated.

Example:
```
Input:
2
6
A B A B A B
Output:
8
```
MD,
                'starter_code'        => "k = int(input())\nn = int(input())\ntasks = input().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
**Rotten oranges**: Given an `n × m` grid where `0` = empty, `1` = fresh orange, `2` = rotten orange, every minute all fresh oranges adjacent (4-directional) to a rotten one become rotten. Print the minimum minutes to rot all oranges, or `-1` if impossible.

Example:
```
Input:
3 3
2 1 1
1 1 0
0 1 1
Output:
4
```
MD,
                'starter_code'        => "from collections import deque\nn, m = map(int, input().split())\ngrid = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
**Merge k sorted lists**: Given `k` sorted arrays (each of length `m`), merge them into one sorted array using a min-heap. Read `k`, `m`, then `k` arrays (each on its own line). Print the merged sorted array, space-separated.

Example:
```
Input:
3
3
1 4 7
2 5 8
3 6 9
Output:
1 2 3 4 5 6 7 8 9
```
MD,
                'starter_code'        => "import heapq\nk = int(input())\nm = int(input())\nlists = [list(map(int, input().split())) for _ in range(k)]\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
**Redesign hitting minimum**: Implement a **max-priority queue** that also supports `increase_key` operations. Read `n` operations:
- `insert x` — insert integer x
- `extract_max` — remove and print maximum; print `empty` if empty
- `peek_max` — print maximum without removing; print `empty` if empty

Example:
```
Input:
6
insert 3
insert 7
insert 5
peek_max
extract_max
extract_max
Output:
7
7
5
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Linked Lists: Singly, Doubly & Circular (Q21–Q25) → L342
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
**LRU Cache**: Implement a Least Recently Used cache of capacity `c`. Read `c` then `n` operations:
- `put k v` — insert/update key k with value v
- `get k` — print value for key k, or `-1` if absent; marks k as recently used

Example:
```
Input:
2
7
put 1 10
put 2 20
get 1
put 3 30
get 2
get 1
get 3
Output:
10
-1
10
30
```
MD,
                'starter_code'        => "from collections import OrderedDict\nc = int(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
**Rotate linked list**: Given a list of `n` integers and rotation count `k`, rotate the list to the right by `k` places. Print the rotated list, space-separated.

Example:
```
Input:
5
1 2 3 4 5
2
Output:
4 5 1 2 3
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
**Intersection of two linked lists**: Given two lists (as integer arrays), find the value at which they first intersect (share a common suffix). Read `n1`, list1, `n2`, list2. The intersection is defined by shared tail values. Print the first intersecting value, or `none` if they don't intersect. (Assume values are unique and the common suffix, if present, forms the tail of both.)

Example:
```
Input:
4
1 2 3 4
3
5 3 4
Output:
3
```
MD,
                'starter_code'        => "n1 = int(input())\na = list(map(int, input().split()))\nn2 = int(input())\nb = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
**Sort a linked list** in O(n log n) using merge sort. Read `n` integers representing the list. Print the sorted list, space-separated.

Example:
```
Input:
6
4 2 1 3 6 5
Output:
1 2 3 4 5 6
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
**Flatten a multilevel doubly linked list**: A node can have a `child` pointer creating a sublist. Given the flat representation as space-separated integers where `-1` marks a child branch start and `-2` marks the end of a branch, flatten it depth-first and print the resulting list, space-separated.

Example:
```
Input:
1 2 -1 3 4 -2 5
Output:
1 2 3 4 5
```
*(Node 2 has child branch [3,4], which is inserted after 2)*
MD,
                'starter_code'        => "tokens = list(map(int, input().split()))\n# Write your solution below\n",
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
**Longest substring without repeating characters**: Read a string. Print the length of the longest substring with all unique characters. Use a sliding window + hash set.

Example:
```
Input:
abcabcbb
Output:
3
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
**Subarray sum equals k**: Given `n` integers and target `k`, count the number of contiguous subarrays whose sum equals `k`. Use a prefix-sum hash map for O(n) time.

Example:
```
Input:
5
1 1 1 2 1
2
Output:
3
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
**Minimum window substring**: Given strings `s` and `t`, find the minimum window in `s` that contains all characters of `t` (including duplicates). Print the window substring. If none, print `none`.

Example:
```
Input:
ADOBECODEBANC
ABC
Output:
BANC
```
MD,
                'starter_code'        => "s = input()\nt = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
**Top k frequent elements**: Given `n` integers and `k`, print the `k` most frequent elements in descending order of frequency. Break ties by ascending value.

Example:
```
Input:
6
1 1 1 2 2 3
2
Output:
1 2
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
**Four-sum count**: Given four arrays `A`, `B`, `C`, `D` of `n` integers each, count the number of tuples `(i, j, k, l)` such that `A[i] + B[j] + C[k] + D[l] == 0`. Use a hash map for O(n²) time. Read `n`, then the four arrays each on its own line.

Example:
```
Input:
2
1 2
-2 -1
-1 2
0 2
Output:
2
```
MD,
                'starter_code'        => "n = int(input())\nA = list(map(int, input().split()))\nB = list(map(int, input().split()))\nC = list(map(int, input().split()))\nD = list(map(int, input().split()))\n# Write your solution below\n",
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
Given `n` integers inserted into a BST, **delete** a value `d` from the BST (standard BST deletion: replace with in-order successor if two children). Print the in-order traversal after deletion.

Example:
```
Input:
5
5 3 7 1 4
3
Output:
1 4 5 7
```
MD,
                'starter_code'        => "n = int(input())\nvalues = list(map(int, input().split()))\nd = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Check if a binary tree (given as level-order array with `-1` for null nodes) is **balanced** (every node's left and right subtree heights differ by at most 1). Print `balanced` or `not balanced`.

Example:
```
Input:
7
1 2 3 4 5 -1 -1
Output:
balanced
```
MD,
                'starter_code'        => "n = int(input())\nnodes = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Find the **Lowest Common Ancestor (LCA)** of two nodes `p` and `q` in a BST built from `n` integers. Print the LCA value.

Example:
```
Input:
5
6 2 8 0 4
2
8
Output:
6
```
MD,
                'starter_code'        => "n = int(input())\nvalues = list(map(int, input().split()))\np = int(input())\nq = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Given a binary tree as a level-order array (with `-1` for null), find its **diameter** (longest path between any two nodes, measured in edges). Print the diameter.

Example:
```
Input:
5
1 2 3 4 5
Output:
3
```
MD,
                'starter_code'        => "n = int(input())\nnodes = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Given a BST built from `n` integers, find the **k-th smallest element** (1-indexed) using in-order traversal. Read `n`, the values, then `k`. Print the k-th smallest.

Example:
```
Input:
5
5 3 7 1 4
3
Output:
4
```
MD,
                'starter_code'        => "n = int(input())\nvalues = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Heaps & Priority Queues (Q36–Q40) → L345
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
**Kth smallest in sorted matrix**: Given an `n × n` matrix where each row and column is sorted in ascending order, find the k-th smallest element. Read `n`, the matrix, then `k`. Use a min-heap.

Example:
```
Input:
3
1 5 9
10 11 13
12 13 15
8
Output:
13
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\nmatrix = [list(map(int, input().split())) for _ in range(n)]\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
**Reorganize string**: Given a string, rearrange it so no two adjacent characters are the same. Use a max-heap by frequency. Print the rearranged string. If impossible, print `impossible`.

Example:
```
Input:
aab
Output:
aba
```
MD,
                'starter_code'        => "import heapq\nfrom collections import Counter\ns = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
**Find k pairs with smallest sums**: Given two sorted arrays `A` and `B` of `n` integers each, find the `k` pairs `(A[i], B[j])` with the smallest sums. Print each pair on a new line as `a b`, in ascending order of sum. Use a min-heap.

Example:
```
Input:
3
1 7 11
3
2 4 6
3
Output:
1 2
1 4
1 6
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\nA = list(map(int, input().split()))\nm = int(input())\nB = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
**Minimum cost to connect ropes**: Given `n` rope lengths, connect all ropes into one. The cost of connecting two ropes is their total length. Use a greedy min-heap strategy. Read `n` and the lengths. Print the minimum total cost.

Example:
```
Input:
4
4 3 2 6
Output:
29
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\nlengths = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
**Ugly numbers**: Ugly numbers are positive integers whose prime factors are only 2, 3, and 5. Read `n`. Print the n-th ugly number using a min-heap.

Example:
```
Input:
10
Output:
12
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Graphs: Representation, BFS & DFS (Q41–Q45) → L346
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
**Topological sort**: Given a directed acyclic graph (DAG) with `n` nodes (1-indexed) and `e` edges (format `u v` meaning u → v), print one valid topological ordering using Kahn's algorithm (BFS). If multiple orderings, pick smallest node index first.

Example:
```
Input:
4
4
1 2
1 3
2 4
3 4
Output:
1 2 3 4
```
MD,
                'starter_code'        => "from collections import deque\nn = int(input())\ne = int(input())\nadj = {i: [] for i in range(1, n+1)}\nindegree = {i: 0 for i in range(1, n+1)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n    indegree[v] += 1\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
**Dijkstra's shortest path**: Given a weighted directed graph with `n` nodes and `e` edges (format `u v w`), find the shortest distance from source `s` to all other nodes. Print `node: distance` for nodes 1 to n. If unreachable, print `node: inf`.

Example:
```
Input:
4
5
1 2 1
1 3 4
2 3 2
2 4 5
3 4 1
1
Output:
1: 0
2: 1
3: 3
4: 4
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\ne = int(input())\nadj = {i: [] for i in range(1, n+1)}\nfor _ in range(e):\n    u, v, w = map(int, input().split())\n    adj[u].append((v, w))\ns = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
**Detect cycle in a directed graph**: Given `n` nodes and `e` directed edges, print `cycle` if the graph contains a cycle, otherwise `no cycle`. Use DFS with colouring (white/grey/black).

Example:
```
Input:
4
4
1 2
2 3
3 4
4 2
Output:
cycle
```
MD,
                'starter_code'        => "n = int(input())\ne = int(input())\nadj = {i: [] for i in range(1, n+1)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
**Bipartite check**: Given an undirected graph with `n` nodes and `e` edges, determine if it is bipartite (2-colourable). Print `bipartite` or `not bipartite`.

Example:
```
Input:
4
4
1 2
2 3
3 4
4 1
Output:
bipartite
```
MD,
                'starter_code'        => "from collections import deque\nn = int(input())\ne = int(input())\nadj = {i: [] for i in range(1, n+1)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n    adj[v].append(u)\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
**Minimum spanning tree (Kruskal's)**: Given an undirected weighted graph with `n` nodes and `e` edges (format `u v w`), find the total weight of the MST. Use Kruskal's algorithm with Union-Find. Print the MST total weight.

Example:
```
Input:
4
5
1 2 1
1 3 4
2 3 2
2 4 5
3 4 1
Output:
4
```
MD,
                'starter_code'        => "n = int(input())\ne = int(input())\nedges = []\nfor _ in range(e):\n    u, v, w = map(int, input().split())\n    edges.append((w, u, v))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Sorting & Searching Algorithms (Q46–Q50) → L347
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Implement **quicksort** (pivot = last element, Lomuto partition). Read `n` integers, sort them, and print the sorted array space-separated. Also print the total number of comparisons made during partitioning.

Example:
```
Input:
5
3 1 4 1 5
Output:
1 1 3 4 5
comparisons: 8
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
**Search in rotated sorted array**: Given a rotated sorted array of `n` distinct integers and a target `t`, find the index of `t` using binary search. Print the 0-based index, or `-1` if not found. O(log n) expected.

Example:
```
Input:
7
4 5 6 7 0 1 2
0
Output:
4
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nt = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
**Dutch national flag** (3-way partition): Given `n` integers each in {0, 1, 2}, sort them in-place in a single pass using the DNF algorithm. Print the sorted array space-separated.

Example:
```
Input:
6
2 0 2 1 1 0
Output:
0 0 1 1 2 2
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
**Radix sort**: Sort `n` non-negative integers using LSD radix sort (base 10). Print the sorted array space-separated and also print the number of passes (equal to the number of digits in the largest number).

Example:
```
Input:
5
170 45 75 90 802
Output:
45 75 90 170 802
passes: 3
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**Find the median of two sorted arrays** in O(log(min(m, n))) time. Read `n`, array A of `n` integers (sorted), `m`, array B of `m` integers (sorted). Print the median rounded to 1 decimal place.

Example:
```
Input:
2
1 3
2
2 4
Output:
2.5
```
MD,
                'starter_code'        => "n = int(input())\nA = list(map(int, input().split()))\nm = int(input())\nB = list(map(int, input().split()))\n# Write your solution below\n",
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

        // ── Q1: Nested-loop count ────────────────────────────────────────
        $seed(1, [
            ['input' => "3",   'expected_output' => "14",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2",   'expected_output' => "5",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1",   'expected_output' => "1",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4",   'expected_output' => "30",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Amortised cost ───────────────────────────────────────────
        $seed(2, [
            ['input' => "5",    'expected_output' => "12",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1",    'expected_output' => "1",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "8",    'expected_output' => "22",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "16",   'expected_output' => "46",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Big-O per snippet ────────────────────────────────────────
        $seed(3, [
            ['input' => "3\n1 1\n2 0\n0 0",   'expected_output' => "O(n^2)\nO(n^2)\nO(1)",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1",          'expected_output' => "O(n)\nO(n)",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n3 2",              'expected_output' => "O(n^5)",                 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0 0\n2 2",         'expected_output' => "O(1)\nO(n^4)",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Master Theorem ───────────────────────────────────────────
        $seed(4, [
            ['input' => "4\n2\n1",   'expected_output' => "O(n^2)",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2\n1",   'expected_output' => "O(n^1 * log n)",'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n2\n2",   'expected_output' => "O(n^2)",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "8\n2\n3",   'expected_output' => "O(n^3)",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Space complexity ─────────────────────────────────────────
        $seed(5, [
            ['input' => "6",    'expected_output' => "memo_size: 7\nstack_depth: 6",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0",    'expected_output' => "memo_size: 1\nstack_depth: 0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "10",   'expected_output' => "memo_size: 11\nstack_depth: 10", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1",    'expected_output' => "memo_size: 2\nstack_depth: 1",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Min-length subarray ──────────────────────────────────────
        $seed(6, [
            ['input' => "6\n2 3 1 2 4 3\n7",   'expected_output' => "2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1 1 1 1 1\n11",    'expected_output' => "0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 4 4 1\n8",        'expected_output' => "2",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 2 3\n6",          'expected_output' => "3",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Prefix sum 2D ────────────────────────────────────────────
        $seed(7, [
            ['input' => "2 3\n1 2 3\n4 5 6\n1\n0 0 1 2",         'expected_output' => "21",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n1 2\n3 4\n2\n0 0 0 0\n1 1 1 1",    'expected_output' => "1\n4",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 3\n1 2 3\n4 5 6\n7 8 9\n1\n1 1 2 2",  'expected_output' => "28",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 4\n1 2 3 4\n1\n0 1 0 2",              'expected_output' => "9",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: LIS ──────────────────────────────────────────────────────
        $seed(8, [
            ['input' => "8\n10 9 2 5 3 7 101 18",   'expected_output' => "4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2 3 4",                'expected_output' => "4",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5 4 3 2 1",              'expected_output' => "1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n3 1 4 1 5 9",            'expected_output' => "4",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Trapping rainwater ───────────────────────────────────────
        $seed(9, [
            ['input' => "12\n0 1 0 2 1 0 1 3 2 1 2 1",   'expected_output' => "6",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n4 2 0 3 2 5",                'expected_output' => "9",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3",                       'expected_output' => "0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n3 0 2 0 4",                  'expected_output' => "7",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: 3-sum closest ───────────────────────────────────────────
        $seed(10, [
            ['input' => "4\n-1 2 1 -4\n1",    'expected_output' => "2",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0 0 0\n1",         'expected_output' => "0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1 1 0\n-100",   'expected_output' => "2",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 2 3 4 5\n10",   'expected_output' => "10",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Stack with min ──────────────────────────────────────────
        $seed(11, [
            ['input' => "6\npush 5\npush 3\npush 7\nget_min\npop\nget_min",  'expected_output' => "3\n3",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\npush 1\npush 2\npop\nget_min",                   'expected_output' => "1",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nget_min\npush 4\nget_min",                        'expected_output' => "empty\n4",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\npush 5\npush 3\npush 1\npop\nget_min",           'expected_output' => "3",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Largest rectangle ───────────────────────────────────────
        $seed(12, [
            ['input' => "6\n2 1 5 6 2 3",     'expected_output' => "10",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n2 4 2 1 10",       'expected_output' => "10",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 1 1",            'expected_output' => "3",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4 3 2 1",          'expected_output' => "6",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Infix to postfix ────────────────────────────────────────
        $seed(13, [
            ['input' => "a + b * c - d",      'expected_output' => "a b c * + d -",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "a * b + c * d",       'expected_output' => "a b * c d * +",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "a + b + c",           'expected_output' => "a b + c +",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "a * b * c + d",       'expected_output' => "a b * c * d +",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Decode string ───────────────────────────────────────────
        $seed(14, [
            ['input' => "3[a]2[bc]",     'expected_output' => "aaabcbc",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3[a2[c]]",      'expected_output' => "accaccacc", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2[ab3[c]]",     'expected_output' => "abcccabccc",'is_hidden' => true,  'order_index' => 3],
            ['input' => "10[a]",         'expected_output' => "aaaaaaaaaa",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Asteroid collision ──────────────────────────────────────
        $seed(15, [
            ['input' => "4\n5 10 -5 -10",   'expected_output' => "5",            'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5 -5 5",         'expected_output' => "5",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n8 -8 5 -5",      'expected_output' => "",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 2 3",          'expected_output' => "1 2 3",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Queue from two stacks ───────────────────────────────────
        $seed(16, [
            ['input' => "5\nenqueue 1\nenqueue 2\ndequeue\nenqueue 3\ndequeue",  'expected_output' => "1\n2",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\ndequeue\nenqueue 5\ndequeue",                        'expected_output' => "empty\n5",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nenqueue 10\nenqueue 20\ndequeue\ndequeue",           'expected_output' => "10\n20",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nenqueue 7\ndequeue",                                 'expected_output' => "7",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Task scheduler ─────────────────────────────────────────
        $seed(17, [
            ['input' => "2\n6\nA B A B A B",   'expected_output' => "8",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n3\nA A A",          'expected_output' => "3",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n6\nA A A B B B",   'expected_output' => "8",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n5\nA B A C A",     'expected_output' => "9",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Rotten oranges ──────────────────────────────────────────
        $seed(18, [
            ['input' => "3 3\n2 1 1\n1 1 0\n0 1 1",   'expected_output' => "4",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 3\n2 1 1\n0 1 1\n1 0 1",   'expected_output' => "-1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 1\n2",                       'expected_output' => "0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n2 1\n1 2",               'expected_output' => "1",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Merge k sorted lists ────────────────────────────────────
        $seed(19, [
            ['input' => "3\n3\n1 4 7\n2 5 8\n3 6 9",     'expected_output' => "1 2 3 4 5 6 7 8 9",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2\n1 3\n2 4",                 'expected_output' => "1 2 3 4",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2\n1 2\n3 4\n5 6",           'expected_output' => "1 2 3 4 5 6",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n4\n4 3 2 1",                  'expected_output' => "4 3 2 1",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Max-priority queue ──────────────────────────────────────
        $seed(20, [
            ['input' => "6\ninsert 3\ninsert 7\ninsert 5\npeek_max\nextract_max\nextract_max",  'expected_output' => "7\n7\n5",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nextract_max\ninsert 4\nextract_max",                                 'expected_output' => "empty\n4",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\ninsert 1\ninsert 2\ninsert 3\nextract_max",                         'expected_output' => "3",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\npeek_max\ninsert 9\npeek_max",                                      'expected_output' => "empty\n9",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: LRU cache ───────────────────────────────────────────────
        $seed(21, [
            ['input' => "2\n7\nput 1 10\nput 2 20\nget 1\nput 3 30\nget 2\nget 1\nget 3",  'expected_output' => "10\n-1\n10\n30",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n4\nput 2 1\nput 3 2\nget 2\nget 3",                             'expected_output' => "-1\n2",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n5\nput 1 1\nput 2 2\nput 1 10\nget 1\nget 2",                  'expected_output' => "10\n2",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n3\nput 1 1\nput 2 2\nget 1",                                   'expected_output' => "1",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Rotate linked list ──────────────────────────────────────
        $seed(22, [
            ['input' => "5\n1 2 3 4 5\n2",    'expected_output' => "4 5 1 2 3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n4",         'expected_output' => "3 1 2",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4\n0",       'expected_output' => "1 2 3 4",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 2 3\n3",         'expected_output' => "1 2 3",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Intersection of lists ───────────────────────────────────
        $seed(23, [
            ['input' => "4\n1 2 3 4\n3\n5 3 4",       'expected_output' => "3",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n3\n4 5 6",         'expected_output' => "none",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 2 3 4 5\n3\n3 4 5",     'expected_output' => "3",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 2\n2\n3 4",             'expected_output' => "none",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Sort linked list ────────────────────────────────────────
        $seed(24, [
            ['input' => "6\n4 2 1 3 6 5",    'expected_output' => "1 2 3 4 5 6",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3 1 2",           'expected_output' => "1 2 3",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5",              'expected_output' => "5",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4 4 2 2",        'expected_output' => "2 2 4 4",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Flatten multilevel list ─────────────────────────────────
        $seed(25, [
            ['input' => "1 2 -1 3 4 -2 5",        'expected_output' => "1 2 3 4 5",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 -1 2 -2 3",             'expected_output' => "1 2 3",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 3",                   'expected_output' => "1 2 3",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 -1 2 -1 3 -2 -2 4",    'expected_output' => "1 2 3 4",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Longest substring no repeat ────────────────────────────
        $seed(26, [
            ['input' => "abcabcbb",   'expected_output' => "3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "bbbbb",      'expected_output' => "1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "pwwkew",     'expected_output' => "3",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "abcdef",     'expected_output' => "6",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Subarray sum k ──────────────────────────────────────────
        $seed(27, [
            ['input' => "5\n1 1 1 2 1\n2",    'expected_output' => "3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n3",         'expected_output' => "2",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 -1 1 -1\n0",    'expected_output' => "4",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n3 3 3\n3",         'expected_output' => "1",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Minimum window substring ────────────────────────────────
        $seed(28, [
            ['input' => "ADOBECODEBANC\nABC",   'expected_output' => "BANC",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "aa\naa",               'expected_output' => "aa",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "a\nb",                 'expected_output' => "none",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "ABBC\nBC",             'expected_output' => "BBC",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Top k frequent ──────────────────────────────────────────
        $seed(29, [
            ['input' => "6\n1 1 1 2 2 3\n2",   'expected_output' => "1 2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 1 2 2\n1",        'expected_output' => "1",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 2 3 4 5\n3",      'expected_output' => "1 2 3",'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4 4 4 4\n1",        'expected_output' => "4",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Four-sum count ──────────────────────────────────────────
        $seed(30, [
            ['input' => "2\n1 2\n-2 -1\n-1 2\n0 2",    'expected_output' => "2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0\n0\n0\n0",               'expected_output' => "1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 -1\n-1 1\n1 -1\n-1 1",  'expected_output' => "8",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0 0\n0 0\n0 0\n0 0",       'expected_output' => "16", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: BST delete ──────────────────────────────────────────────
        $seed(31, [
            ['input' => "5\n5 3 7 1 4\n3",   'expected_output' => "1 4 5 7",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2 1 3\n2",        'expected_output' => "1 3",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n5 3 7 1\n7",     'expected_output' => "1 3 5",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2 1 3\n1",        'expected_output' => "2 3",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Balanced tree ───────────────────────────────────────────
        $seed(32, [
            ['input' => "7\n1 2 3 4 5 -1 -1",     'expected_output' => "balanced",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "7\n1 2 -1 3 -1 -1 -1",   'expected_output' => "not balanced",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1",                    'expected_output' => "balanced",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2 3 4",              'expected_output' => "not balanced",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: LCA in BST ──────────────────────────────────────────────
        $seed(33, [
            ['input' => "5\n6 2 8 0 4\n2\n8",     'expected_output' => "6",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n6 2 8 0 4\n0\n4",     'expected_output' => "2",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n4 2 6\n2\n6",          'expected_output' => "4",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n4 2 6\n2\n2",          'expected_output' => "2",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Diameter ────────────────────────────────────────────────
        $seed(34, [
            ['input' => "5\n1 2 3 4 5",    'expected_output' => "3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1",            'expected_output' => "0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3",        'expected_output' => "2",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "7\n1 2 3 4 5 6 7",'expected_output' => "4", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Kth smallest in BST ─────────────────────────────────────
        $seed(35, [
            ['input' => "5\n5 3 7 1 4\n3",   'expected_output' => "4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2 1 3\n1",        'expected_output' => "1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4 2 6 1\n4",     'expected_output' => "6",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2 1 3\n3",        'expected_output' => "3",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Kth smallest in matrix ──────────────────────────────────
        $seed(36, [
            ['input' => "3\n1 5 9\n10 11 13\n12 13 15\n8",  'expected_output' => "13",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n3 4\n2",                   'expected_output' => "2",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3\n4 5 6\n7 8 9\n5",        'expected_output' => "5",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 3\n2 4\n3",                   'expected_output' => "3",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Reorganize string ───────────────────────────────────────
        $seed(37, [
            ['input' => "aab",    'expected_output' => "aba",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "aaa",    'expected_output' => "impossible",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "ab",     'expected_output' => "ab",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "aaab",   'expected_output' => "impossible",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: K smallest pairs ────────────────────────────────────────
        $seed(38, [
            ['input' => "3\n1 7 11\n3\n2 4 6\n3",    'expected_output' => "1 2\n1 4\n1 6",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n2\n3 4\n2",          'expected_output' => "1 3\n1 4",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3\n3\n4 5 6\n4",      'expected_output' => "1 4\n1 5\n1 6\n2 4", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n1\n1\n1\n1",              'expected_output' => "1 1",               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Min cost ropes ──────────────────────────────────────────
        $seed(39, [
            ['input' => "4\n4 3 2 6",   'expected_output' => "29",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2",       'expected_output' => "3",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3",     'expected_output' => "9",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n5 5 5 5",   'expected_output' => "40",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Ugly numbers ────────────────────────────────────────────
        $seed(40, [
            ['input' => "10",   'expected_output' => "12",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1",    'expected_output' => "1",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "15",   'expected_output' => "24",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "20",   'expected_output' => "36",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Topological sort ────────────────────────────────────────
        $seed(41, [
            ['input' => "4\n4\n1 2\n1 3\n2 4\n3 4",   'expected_output' => "1 2 3 4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2\n1 3\n2 3",              'expected_output' => "1 2 3",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n3\n1 2\n2 3\n3 4",        'expected_output' => "1 2 3 4",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0",                        'expected_output' => "1 2 3",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Dijkstra ────────────────────────────────────────────────
        $seed(42, [
            ['input' => "4\n5\n1 2 1\n1 3 4\n2 3 2\n2 4 5\n3 4 1\n1",   'expected_output' => "1: 0\n2: 1\n3: 3\n4: 4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2\n1 2 2\n2 3 3\n1",                         'expected_output' => "1: 0\n2: 2\n3: 5",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n3\n1 2 1\n2 3 2\n3 4 3\n1",                 'expected_output' => "1: 0\n2: 1\n3: 3\n4: 6",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n1 2 5\n1",                               'expected_output' => "1: 0\n2: 5\n3: inf",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Cycle detection directed ────────────────────────────────
        $seed(43, [
            ['input' => "4\n4\n1 2\n2 3\n3 4\n4 2",   'expected_output' => "cycle",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2\n1 2\n2 3",              'expected_output' => "no cycle",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1\n1 1",                   'expected_output' => "cycle",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4\n1 2\n1 3\n2 4\n3 4",   'expected_output' => "no cycle",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Bipartite check ─────────────────────────────────────────
        $seed(44, [
            ['input' => "4\n4\n1 2\n2 3\n3 4\n4 1",   'expected_output' => "bipartite",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3\n1 2\n2 3\n3 1",         'expected_output' => "not bipartite",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n1 2",                   'expected_output' => "bipartite",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n4\n1 2\n2 3\n3 1\n4 5",   'expected_output' => "not bipartite",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Kruskal MST ─────────────────────────────────────────────
        $seed(45, [
            ['input' => "4\n5\n1 2 1\n1 3 4\n2 3 2\n2 4 5\n3 4 1",   'expected_output' => "4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3\n1 2 1\n2 3 2\n1 3 5",                  'expected_output' => "3",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4\n1 2 10\n1 3 6\n2 4 5\n3 4 1",         'expected_output' => "12", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1\n1 2 7",                                'expected_output' => "7",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Quicksort + comparisons ─────────────────────────────────
        $seed(46, [
            ['input' => "5\n3 1 4 1 5",    'expected_output' => "1 1 3 4 5\ncomparisons: 8",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3",         'expected_output' => "1 2 3\ncomparisons: 3",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4 3 2 1",       'expected_output' => "1 2 3 4\ncomparisons: 6",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5",             'expected_output' => "5\ncomparisons: 0",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Search in rotated array ─────────────────────────────────
        $seed(47, [
            ['input' => "7\n4 5 6 7 0 1 2\n0",   'expected_output' => "4",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "7\n4 5 6 7 0 1 2\n3",   'expected_output' => "-1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n3 4 1 2\n4",          'expected_output' => "1",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n2 3 4 5 1\n1",        'expected_output' => "4",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Dutch national flag ──────────────────────────────────────
        $seed(48, [
            ['input' => "6\n2 0 2 1 1 0",   'expected_output' => "0 0 1 1 2 2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0 1 2",          'expected_output' => "0 1 2",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n2 2 2 2",        'expected_output' => "2 2 2 2",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 0 2 0 1",      'expected_output' => "0 0 1 1 2",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Radix sort ──────────────────────────────────────────────
        $seed(49, [
            ['input' => "5\n170 45 75 90 802",   'expected_output' => "45 75 90 170 802\npasses: 3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3",              'expected_output' => "1 2 3\npasses: 1",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n100 10 1 1000",      'expected_output' => "1 10 100 1000\npasses: 4",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n99 11 55",           'expected_output' => "11 55 99\npasses: 2",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Median of two sorted arrays ─────────────────────────────
        $seed(50, [
            ['input' => "2\n1 3\n2\n2 4",        'expected_output' => "2.5",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n3\n4 5 6",    'expected_output' => "3.5",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 2\n1\n3",           'expected_output' => "2.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5\n1\n5",            'expected_output' => "5.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 7 Coding (University Student) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}