<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 7 — Data Structures & Algorithms (Advanced / Tier 4) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Advanced tier
 *   2. coding_questions    — 50 questions covering advanced DSA concepts
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
 * Difficulty: Advanced (Tier 4) — problems require solid understanding of
 * classic algorithms, multi-step logic, and efficient Python implementations
 * using stdlib (heapq, collections, bisect). No third-party libraries needed.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module7CodingChallengeSeederAdvanced extends Seeder
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

        $this->command->info('Creating Module 7 — Data Structures & Algorithms (Advanced) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Data Structures & Algorithms',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Push your DSA skills to the next level with Advanced-tier problems in pure Python. Analyse amortized complexity, apply Kadane\'s algorithm on 2D matrices, implement monotonic stacks, simulate LRU caches, detect cycles in graphs, run Dijkstra\'s algorithm, count inversions with merge sort, and solve classic interview-level problems across all core data structures and algorithms.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 1400,
                'order_index'        => 4,
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
Count the **total number of operations** executed by a triple nested loop. Given `n`, a loop runs `i` from 1 to `n`, `j` from 1 to `i`, and `k` from 1 to `j`. Each innermost iteration performs 1 operation. Read `n` and print the total count.

The closed-form formula is `n(n+1)(n+2) / 6`.

Example:
```
Input:
3
Output:
10
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
**Amortised analysis — dynamic array doubling.** A dynamic array starts with capacity 1. When a push would exceed capacity, all current elements are copied to a new array of double the capacity (this counts as one copy per element copied). Read `n` and print the total number of **element copy operations** performed across all `n` pushes.

Example:
```
Input:
5
Output:
7
```
*(Copies happen at push 2: 1 copy; push 3: 2 copies; push 5: 4 copies → total 7)*
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Given an algorithm code, print its **best-case**, **worst-case**, and **average-case** time complexity on separate lines.

Codes:
- `1` → Linear Search
- `2` → Binary Search
- `3` → Bubble Sort
- `4` → Quick Sort

Format: `best: <X>`, `worst: <X>`, `average: <X>`

Example:
```
Input:
1
Output:
best: O(1)
worst: O(n)
average: O(n)
```
MD,
                'starter_code'        => "code = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Count the **exact number of comparisons** performed by insertion sort on its worst-case input (a reverse-sorted array) for a given `n`. The formula is `n*(n-1)/2`.

Example:
```
Input:
5
Output:
10
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Given four algorithm labels and their approximate operation counts at `n = 1000`, sort the algorithms from **fewest to most operations** and print their labels separated by spaces.

Read four lines, each with a label (single character) and operation count (integer). Print labels sorted ascending by operation count; break ties alphabetically.

Example:
```
Input:
A 500000
B 1000
C 10000000
D 3000
Output:
B D A C
```
MD,
                'starter_code'        => "algos = []\nfor _ in range(4):\n    parts = input().split()\n    algos.append((parts[0], int(parts[1])))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Arrays & Dynamic Arrays (Q6–Q10) → L339
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Count the number of **contiguous subarrays** whose product is strictly less than `k`. All elements are positive integers.

Read `n`, then the array, then `k`.

Example:
```
Input:
4
10 5 2 6
100
Output:
8
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Find the **maximum sum of non-adjacent elements** in an array. You may not take two elements that are adjacent (next to each other). Read `n` and the array. Print the maximum sum.

Example:
```
Input:
5
3 2 5 10 7
Output:
15
```
*(Take 3 + 5 + 7 = 15)*
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Compute the total amount of **trapped rainwater** given an elevation map. Read `n` and the array of non-negative integer heights. Print the total water trapped.

Example:
```
Input:
6
3 0 1 3 0 2
Output:
7
```
MD,
                'starter_code'        => "n = int(input())\nh = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Find the length of the **longest consecutive sequence** of integers in the array. Use an O(n) approach with a hash set. Read `n` and the array. Print the length.

Example:
```
Input:
8
100 4 200 1 3 2 101 102
Output:
4
```
*(Sequence 1, 2, 3, 4 has length 4)*
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Given an array of `n` integers, return the **product of all elements except self** — without using division. Read `n` and the array. Print the result space-separated.

Example:
```
Input:
4
1 2 3 4
Output:
24 12 8 6
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Stacks: LIFO & Applications (Q11–Q15) → L340
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Find the **largest rectangle area** in a histogram. Read `n` bar heights and print the maximum rectangular area that can be formed.

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
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Solve the **stock span problem**. The span of a stock's price on a given day is the maximum number of consecutive days (ending on that day) for which the stock price was ≤ today's price. Read `n` and the daily prices. Print the span for each day, space-separated.

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
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Simulate **asteroid collision**. Asteroids move in a line. Positive integers move right; negative move left. When two asteroids meet, the smaller absolute value explodes; equal values both explode. Read `n` and the asteroid values. Print the surviving asteroids space-separated, or `empty` if none survive.

Example:
```
Input:
3
5 10 -5
Output:
5 10
```
MD,
                'starter_code'        => "n = int(input())\nasteroids = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
**Decode a compressed string** of the form `k[encoded_string]`, where `encoded_string` is repeated exactly `k` times. Brackets can be nested. Read the encoded string and print the decoded result.

Example:
```
Input:
3[a2[b]]
Output:
abbabbabb
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Design a **MinStack** that supports `push`, `pop`, and `min` all in O(1). Read `n` operations:
- `push x` — push x (no output)
- `pop` — remove top and print it; print `empty` if stack is empty
- `min` — print the current minimum; print `empty` if stack is empty

Example:
```
Input:
7
push 5
push 3
min
pop
min
push 1
min
Output:
3
3
5
1
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Queues, Deques & Priority Queues (Q16–Q20) → L341
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Find the **sliding window minimum** using a deque. Read `n` integers, then window size `k`. For each window of size `k`, print the minimum, space-separated.

Example:
```
Input:
8
2 1 3 1 2 3 4 5
3
Output:
1 1 1 1 2 3
```
MD,
                'starter_code'        => "from collections import deque\nn = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Find the **first negative number in every window** of size `k`. If a window has no negative, print `0`. Read `n`, the array, then `k`. Print results space-separated.

Example:
```
Input:
8
12 -1 -7 8 -15 30 16 28
3
Output:
-1 -1 -7 -15 -15 0
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
**Implement a queue using two stacks.** Read `n` operations:
- `enqueue x` — add x to the queue (no output)
- `dequeue` — remove and print the front; print `empty` if queue is empty

Example:
```
Input:
6
enqueue 1
enqueue 2
dequeue
enqueue 3
dequeue
dequeue
Output:
1
2
3
```
MD,
                'starter_code'        => "n = int(input())\nstack1, stack2 = [], []\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Find the **shortest path** (minimum steps) from cell `(0,0)` to cell `(n-1,m-1)` in a grid of `0`s (walkable) and `1`s (blocked). You may move up, down, left, or right. If no path exists, print `-1`.

Read `n` and `m`, then `n` rows of `m` space-separated values.

Example:
```
Input:
3 3
0 0 1
0 0 1
0 0 0
Output:
4
```
MD,
                'starter_code'        => "from collections import deque\nn, m = map(int, input().split())\ngrid = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Compute the **minimum number of CPU intervals** to complete all tasks given a cooldown period `n` between identical tasks. Read the number of tasks, the task labels (uppercase letters) on one line, and the cooldown `n`. Print the minimum intervals needed.

Example:
```
Input:
6
A A A B B B
2
Output:
8
```
MD,
                'starter_code'        => "from collections import Counter\nt = int(input())\ntasks = input().split()\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Linked Lists: Singly, Doubly & Circular (Q21–Q25) → L342
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
**Detect the start of a cycle** in a linked list given as a `next[]` array (0-indexed). Each value is the index of the next node, or `-1` if there is no next node. Starting from node `0`, follow the chain. If you revisit any node, print its index (cycle start). If no cycle, print `-1`.

Read `n` and the next array.

Example:
```
Input:
5
1 2 3 4 2
Output:
2
```
MD,
                'starter_code'        => "n = int(input())\nnxt = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
**Reverse a linked list in k-groups.** Given an array of `n` integers and group size `k`, reverse every consecutive group of `k` elements. If the last group has fewer than `k` elements, leave it as-is. Print the result space-separated.

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
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
**Add two numbers** represented as linked lists in **reverse digit order**. Read `n1` and the digits of list 1, then `n2` and the digits of list 2. Print the sum as a reversed linked list (space-separated).

Example:
```
Input:
3
2 4 3
3
5 6 4
Output:
7 0 8
```
*(342 + 465 = 807 → stored as [7, 0, 8])*
MD,
                'starter_code'        => "n1 = int(input())\nl1 = list(map(int, input().split()))\nn2 = int(input())\nl2 = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
**Remove the k-th node from the end** of a linked list (given as an array) in a single pass using two pointers. Read `n`, the array, and `k`. Print the resulting list space-separated, or `empty` if the result is empty.

Example:
```
Input:
5
1 2 3 4 5
2
Output:
1 2 3 5
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
**Merge k sorted arrays** into one sorted list using a min-heap. Read `k`, then `k` lines each starting with the array length followed by its sorted values. Print the merged sorted list space-separated.

Example:
```
Input:
3
3 1 4 7
3 2 5 8
3 3 6 9
Output:
1 2 3 4 5 6 7 8 9
```
MD,
                'starter_code'        => "import heapq\nk = int(input())\narrays = []\nfor _ in range(k):\n    row = list(map(int, input().split()))\n    arrays.append(row[1:])\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Hash Tables: Dictionaries Demystified (Q26–Q30) → L343
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Find the **length of the longest subarray with sum equal to k** using a prefix-sum hash map. Read `n`, the array (which may contain negative integers), and `k`. Print the length, or `0` if no such subarray exists.

Example:
```
Input:
6
1 2 -1 3 1 -2
5
Output:
4
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Count the number of **subarrays with exactly k distinct integers**. Use the "at most k" sliding window technique. Read `n`, the array of positive integers, and `k`. Print the count.

Example:
```
Input:
5
1 2 1 2 3
2
Output:
7
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Find the **minimum length of a substring** of `s` that contains **all characters of `t`** (including duplicates). If no such substring exists, print `0`.

Read `s` and `t` on separate lines.

Example:
```
Input:
ADOBECODEBANC
ABC
Output:
4
```
MD,
                'starter_code'        => "from collections import Counter\ns = input()\nt = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Check whether two strings are **isomorphic** — characters in `s` can be replaced one-to-one to produce `t`, with no two characters mapping to the same character. Read `s` and `t`. Print `yes` or `no`.

Example:
```
Input:
egg
add
Output:
yes
```
MD,
                'starter_code'        => "s = input()\nt = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Find the **minimum number of characters to remove** (from either string) to make two strings anagrams of each other. Read `s` and `t`. Print the count.

Example:
```
Input:
abc
bcd
Output:
2
```
MD,
                'starter_code'        => "from collections import Counter\ns = input()\nt = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Trees: Binary Trees & BSTs (Q31–Q35) → L344
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
**Validate a BST** given its in-order traversal. An in-order traversal of a valid BST yields a strictly increasing sequence. Read `n` and the traversal values. Print `valid` or `invalid`.

Example:
```
Input:
5
1 3 4 5 7
Output:
valid
```
MD,
                'starter_code'        => "n = int(input())\ntraversal = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Find the **Lowest Common Ancestor (LCA)** of two nodes `p` and `q` in a BST. The BST is built by inserting `n` values in order. Read `n`, the values, then `p` and `q`. Print the LCA value.

Example:
```
Input:
5
6 2 8 0 4
0
4
Output:
2
```
MD,
                'starter_code'        => "n = int(input())\nvalues = list(map(int, input().split()))\np = int(input())\nq = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Find the **diameter** of a BST (the number of edges in the longest path between any two nodes). Build the BST by inserting `n` values in order. Read `n` and the values. Print the diameter.

Example:
```
Input:
6
4 2 6 1 3 5
Output:
4
```
MD,
                'starter_code'        => "n = int(input())\nvalues = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Print the **zigzag level-order traversal** of a BST: odd-numbered levels (1-indexed) go left-to-right, even-numbered levels go right-to-left. Build the BST by inserting `n` values in order. Read `n` and the values. Print each level on a separate line, values space-separated.

Example:
```
Input:
5
5 3 7 1 4
Output:
5
7 3
1 4
```
MD,
                'starter_code'        => "from collections import deque\nn = int(input())\nvalues = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Count the number of **BST nodes whose values lie within the range [lo, hi]** (inclusive). Build the BST by inserting `n` values in order. Read `n`, the values, `lo`, and `hi`. Print the count.

Example:
```
Input:
6
6 2 8 0 4 10
3
8
Output:
3
```
MD,
                'starter_code'        => "n = int(input())\nvalues = list(map(int, input().split()))\nlo = int(input())\nhi = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Heaps & Priority Queues (Q36–Q40) → L345
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Maintain the **k-th largest element in a stream** using a min-heap of size `k`. Read `k`, then `n`, then `n` integers one per line. After each integer, print the k-th largest element, or `null` if fewer than `k` elements have been seen.

Example:
```
Input:
3
5
4
5
8
2
3
Output:
null
null
4
4
4
```
MD,
                'starter_code'        => "import heapq\nk = int(input())\nn = int(input())\nheap = []\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Find the **minimum total cost to merge ropes**. Given `n` ropes of various lengths, merging two ropes costs the sum of their lengths. Find the minimum cost to merge all into one rope. Use a min-heap (greedy approach).

Read `n` and the rope lengths. Print the total minimum cost.

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
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
**Sort characters by frequency** (descending). For characters with the same frequency, sort them in ascending alphabetical order. Read a string `s` and print the rearranged string.

Example:
```
Input:
tree
Output:
eert
```
MD,
                'starter_code'        => "from collections import Counter\ns = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
**Rearrange a string** so that no two adjacent characters are the same, using a greedy max-heap approach. If it is impossible, print `impossible`. Otherwise print the rearranged string.

Use the greedy rule: always place the highest-frequency character that differs from the previous one.

Example:
```
Input:
aab
Output:
aba
```
MD,
                'starter_code'        => "import heapq\nfrom collections import Counter\ns = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Find the **k closest points to the origin** from a list of 2D points. Use a max-heap of size `k`. Read `n` and `k`, then `n` lines each with `x y`. Print the `k` closest points sorted by distance ascending (ties broken by x ascending, then y ascending), one per line.

Example:
```
Input:
4 2
1 3
-2 2
5 -1
1 -1
Output:
1 -1
-2 2
```
MD,
                'starter_code'        => "import heapq\nfirst_line = input().split()\nn, k = int(first_line[0]), int(first_line[1])\npoints = [list(map(int, input().split())) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Graphs: Representation, BFS & DFS (Q41–Q45) → L346
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Run **Dijkstra's algorithm** on a weighted undirected graph from a given source. Read `n` (nodes, 1-indexed), `e` (edges), then `e` lines each with `u v w`, then the source node. Print the shortest distance from source to each node (1 to n) space-separated. Use `INF` for unreachable nodes.

Example:
```
Input:
5 7
1 2 10
1 3 3
2 3 1
2 4 2
3 4 7
3 5 8
4 5 5
1
Output:
0 4 3 6 11
```
MD,
                'starter_code'        => "import heapq\nfirst = input().split()\nn, e = int(first[0]), int(first[1])\nadj = {i: [] for i in range(1, n+1)}\nfor _ in range(e):\n    u, v, w = map(int, input().split())\n    adj[u].append((v, w))\n    adj[v].append((u, w))\nsrc = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
**Detect a cycle in a directed graph** using DFS with a recursion stack (white-grey-black colouring). Read `n` nodes (1-indexed), `e` edges, then `e` directed edges `u v`. Print `cycle` if a cycle exists, otherwise `no cycle`.

Example:
```
Input:
4 4
1 2
2 3
3 4
4 2
Output:
cycle
```
MD,
                'starter_code'        => "first = input().split()\nn, e = int(first[0]), int(first[1])\nadj = {i: [] for i in range(1, n+1)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Perform **topological sort** on a DAG using Kahn's algorithm (BFS on in-degrees). When multiple nodes have in-degree 0, process them in ascending numerical order. Read `n` nodes (1-indexed), `e` edges, then `e` directed edges. Print the topological order space-separated.

Example:
```
Input:
4 3
1 2
1 3
3 4
Output:
1 2 3 4
```
MD,
                'starter_code'        => "from collections import deque\nfirst = input().split()\nn, e = int(first[0]), int(first[1])\nadj = {i: [] for i in range(1, n+1)}\nin_degree = {i: 0 for i in range(1, n+1)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n    in_degree[v] += 1\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Check whether an undirected graph is **bipartite** (2-colourable with no two adjacent nodes sharing a colour) using BFS. Read `n` nodes (1-indexed), `e` edges, then `e` edges. Print `bipartite` or `not bipartite`.

Example:
```
Input:
4 4
1 2
1 3
2 4
3 4
Output:
bipartite
```
MD,
                'starter_code'        => "from collections import deque\nfirst = input().split()\nn, e = int(first[0]), int(first[1])\nadj = {i: [] for i in range(1, n+1)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n    adj[v].append(u)\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Count the **number of distinct shortest paths** from a source node to every other node in an unweighted undirected graph using BFS. Read `n` nodes (1-indexed), `e` edges, then `e` edges, then `source`. Print the counts for nodes 1 to n space-separated (count for source is 1).

Example:
```
Input:
5 5
1 2
1 3
2 4
3 4
4 5
1
Output:
1 1 1 2 2
```
MD,
                'starter_code'        => "from collections import deque\nfirst = input().split()\nn, e = int(first[0]), int(first[1])\nadj = {i: [] for i in range(1, n+1)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n    adj[v].append(u)\nsrc = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Sorting & Searching Algorithms (Q46–Q50) → L347
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Perform one **Lomuto partition step** (pivot = last element). Count the number of non-trivial swaps made (swaps where the two indices differ, including the final pivot placement swap). Read `n` and the array. Print the number of swaps and the pivot's final 0-based index.

Example:
```
Input:
5
8 3 7 1 5
Output:
swaps: 3
pivot index: 2
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Count the number of **inversions** in an array using merge sort. An inversion is a pair `(i, j)` where `i < j` but `a[i] > a[j]`. Read `n` and the array. Print the inversion count.

Example:
```
Input:
5
1 20 6 4 5
Output:
5
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
**Search in a rotated sorted array** (no duplicates). Find the 0-based index of the target using a modified binary search. Read `n`, the rotated sorted array, and the target. Print the index, or `-1` if not found.

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
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\ntarget = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Sort an array containing only `0`s, `1`s, and `2`s using the **Dutch National Flag algorithm** (single pass, O(1) extra space). Read `n` and the array. Print the sorted array space-separated.

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
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Find the **median of two sorted arrays** in O(log(min(m, n))) time. Read `n1` and the first sorted array, then `n2` and the second sorted array. Print the median rounded to 1 decimal place.

Example:
```
Input:
3
1 3 5
4
2 4 6 8
Output:
4.0
```
MD,
                'starter_code'        => "n1 = int(input())\na = list(map(int, input().split()))\nn2 = int(input())\nb = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],
        ];

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

        $seed = function (int $qIndex, array $cases) use ($questions): void {
            $qId = $questions[$qIndex] ?? null;
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

        // ── Q1: Triple nested loop count ─────────────────────────────────
        $seed(1, [
            ['input' => "3",  'expected_output' => "10",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4",  'expected_output' => "20",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "6",  'expected_output' => "56",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5",  'expected_output' => "35",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Dynamic array amortised copies ───────────────────────────
        $seed(2, [
            ['input' => "5",  'expected_output' => "7",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4",  'expected_output' => "3",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "9",  'expected_output' => "15",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "17", 'expected_output' => "31",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Algorithm case complexity ────────────────────────────────
        $seed(3, [
            ['input' => "1", 'expected_output' => "best: O(1)\nworst: O(n)\naverage: O(n)",               'is_hidden' => false, 'order_index' => 1],
            ['input' => "2", 'expected_output' => "best: O(1)\nworst: O(log n)\naverage: O(log n)",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "3", 'expected_output' => "best: O(n)\nworst: O(n^2)\naverage: O(n^2)",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "4", 'expected_output' => "best: O(n log n)\nworst: O(n^2)\naverage: O(n log n)", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Insertion sort worst-case comparisons ────────────────────
        $seed(4, [
            ['input' => "5",  'expected_output' => "10",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4",  'expected_output' => "6",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "10", 'expected_output' => "45",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2",  'expected_output' => "1",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Sort algorithms by operation count ───────────────────────
        $seed(5, [
            ['input' => "A 500000\nB 1000\nC 10000000\nD 3000",  'expected_output' => "B D A C",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "W 100\nX 400\nY 200\nZ 50",             'expected_output' => "Z W Y X",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "P 1\nQ 1\nR 2\nS 3",                    'expected_output' => "P Q R S",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "A 9\nB 7\nC 8\nD 6",                   'expected_output' => "D B C A",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Subarray product less than k ─────────────────────────────
        $seed(6, [
            ['input' => "4\n10 5 2 6\n100",   'expected_output' => "8",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n5",         'expected_output' => "4",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 1 1 1 1\n2",     'expected_output' => "15", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2 3 4\n10",         'expected_output' => "4",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Max sum non-adjacent elements ────────────────────────────
        $seed(7, [
            ['input' => "5\n3 2 5 10 7",   'expected_output' => "15",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 20 3 10",    'expected_output' => "30",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5 1 5",         'expected_output' => "10",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n3 5 3 7 5 3",  'expected_output' => "15",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Trapping rainwater ────────────────────────────────────────
        $seed(8, [
            ['input' => "6\n3 0 1 3 0 2",   'expected_output' => "7",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1 0 1 0 1",     'expected_output' => "2",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n4 2 0 3 2 5",   'expected_output' => "9",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2 0 2 0",        'expected_output' => "2",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Longest consecutive sequence ─────────────────────────────
        $seed(9, [
            ['input' => "8\n100 4 200 1 3 2 101 102",  'expected_output' => "4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1 2 0 1 3",                 'expected_output' => "4",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n3 10 4 20 5 21 22",         'expected_output' => "3",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n5 5 5 5",                   'expected_output' => "1",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Product of array except self ────────────────────────────
        $seed(10, [
            ['input' => "4\n1 2 3 4",    'expected_output' => "24 12 8 6",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2 3 4",       'expected_output' => "12 8 6",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 1 1 1 1",  'expected_output' => "1 1 1 1 1",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0 1 2 3",    'expected_output' => "6 0 0 0",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Largest rectangle in histogram ──────────────────────────
        $seed(11, [
            ['input' => "6\n2 1 5 6 2 3",  'expected_output' => "10",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n2 4 2 1 3",    'expected_output' => "6",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n3 3 3",         'expected_output' => "9",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2 3 4",       'expected_output' => "6",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Stock span problem ───────────────────────────────────────
        $seed(12, [
            ['input' => "7\n100 80 60 70 60 75 85",  'expected_output' => "1 1 1 2 1 4 6",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n10 10 10 10",             'expected_output' => "1 2 3 4",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n10 4 5 90 120 80",        'expected_output' => "1 1 2 4 5 1",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n5 4 3",                   'expected_output' => "1 1 1",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Asteroid collision ───────────────────────────────────────
        $seed(13, [
            ['input' => "3\n5 10 -5",           'expected_output' => "5 10",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n8 -8",              'expected_output' => "empty", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n-2 -1 1 2 3",       'expected_output' => "-2 -1 1 2 3", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n1 -1 2 -2",         'expected_output' => "empty", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Decode string ────────────────────────────────────────────
        $seed(14, [
            ['input' => "3[a2[b]]",         'expected_output' => "abbabbabb",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2[abc]3[cd]ef",    'expected_output' => "abcabccdcdcdef", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3[a]",             'expected_output' => "aaa",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "2[2[y]pq]",        'expected_output' => "yypqyypq",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: MinStack ─────────────────────────────────────────────────
        $seed(15, [
            ['input' => "7\npush 5\npush 3\nmin\npop\nmin\npush 1\nmin",   'expected_output' => "3\n3\n5\n1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\npush 10\npush 5\npush 15\nmin",                'expected_output' => "5",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\npush 3\npush 1\npush 2\npop\nmin\npop",        'expected_output' => "2\n1\n1",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\npush 3\npush 3\npop\nmin",                     'expected_output' => "3\n3",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Sliding window minimum ───────────────────────────────────
        $seed(16, [
            ['input' => "8\n2 1 3 1 2 3 4 5\n3",    'expected_output' => "1 1 1 1 2 3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n3 2 1 2 3\n2",           'expected_output' => "2 1 1 2",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4\n2",             'expected_output' => "1 2 3",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n4 3 2 1 2 3\n3",         'expected_output' => "2 1 1 1",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: First negative in every window ──────────────────────────
        $seed(17, [
            ['input' => "8\n12 -1 -7 8 -15 30 16 28\n3",  'expected_output' => "-1 -1 -7 -15 -15 0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1 2 3 4 5\n2",                 'expected_output' => "0 0 0 0",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n-1 -2 -3 -4\n2",              'expected_output' => "-1 -2 -3",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1 -2 3 -4 5 -6\n3",           'expected_output' => "-2 -2 -4 -4",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Queue using two stacks ───────────────────────────────────
        $seed(18, [
            ['input' => "6\nenqueue 1\nenqueue 2\ndequeue\nenqueue 3\ndequeue\ndequeue",  'expected_output' => "1\n2\n3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nenqueue 5\nenqueue 10\ndequeue\ndequeue",                     'expected_output' => "5\n10",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\ndequeue\nenqueue 7\ndequeue",                                 'expected_output' => "empty\n7", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\nenqueue 1\nenqueue 2\nenqueue 3\ndequeue\ndequeue",           'expected_output' => "1\n2",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: BFS shortest path in grid ───────────────────────────────
        $seed(19, [
            ['input' => "3 3\n0 0 1\n0 0 1\n0 0 0",    'expected_output' => "4",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n0 0\n0 0",                'expected_output' => "2",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 3\n0 1 0\n0 1 0\n0 0 0",    'expected_output' => "4",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 3\n0 1 0\n1 0 0\n0 0 0",    'expected_output' => "-1",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Task scheduler ───────────────────────────────────────────
        $seed(20, [
            ['input' => "6\nA A A B B B\n2",   'expected_output' => "8",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA A\n3",            'expected_output' => "5",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nA B C D\n3",        'expected_output' => "4",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nA A A\n2",          'expected_output' => "7",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Detect cycle start ───────────────────────────────────────
        $seed(21, [
            ['input' => "5\n1 2 3 4 2",    'expected_output' => "2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2 3 -1",     'expected_output' => "-1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 0",         'expected_output' => "0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1 2 3 4 5 3",  'expected_output' => "3",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Reverse in k-groups ──────────────────────────────────────
        $seed(22, [
            ['input' => "6\n1 2 3 4 5 6\n2",  'expected_output' => "2 1 4 3 6 5",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "6\n1 2 3 4 5 6\n3",  'expected_output' => "3 2 1 6 5 4",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 2 3 4 5\n2",    'expected_output' => "2 1 4 3 5",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 2 3 4 5\n3",    'expected_output' => "3 2 1 4 5",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Add two linked list numbers ─────────────────────────────
        $seed(23, [
            ['input' => "3\n2 4 3\n3\n5 6 4",  'expected_output' => "7 0 8",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n9 9 9\n1\n1",       'expected_output' => "0 0 0 1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5\n1\n5",           'expected_output' => "0 1",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 2\n2\n3 4",       'expected_output' => "4 6",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Remove k-th from end ─────────────────────────────────────
        $seed(24, [
            ['input' => "5\n1 2 3 4 5\n2",  'expected_output' => "1 2 3 5",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n1",       'expected_output' => "1 2",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4\n4",    'expected_output' => "2 3 4",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1\n1",           'expected_output' => "empty",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Merge k sorted arrays ────────────────────────────────────
        $seed(25, [
            ['input' => "3\n3 1 4 7\n3 2 5 8\n3 3 6 9",  'expected_output' => "1 2 3 4 5 6 7 8 9",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3 1 3 5\n3 2 4 6",           'expected_output' => "1 2 3 4 5 6",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2 1 2\n2 3 4\n1 5",          'expected_output' => "1 2 3 4 5",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n3 1 1 1\n3 1 1 1",           'expected_output' => "1 1 1 1 1 1",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Longest subarray with sum k ──────────────────────────────
        $seed(26, [
            ['input' => "6\n1 2 -1 3 1 -2\n5",   'expected_output' => "4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1 1 1 1 1\n3",         'expected_output' => "3",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 -1 5 -2 3\n3",      'expected_output' => "4",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2 3 4\n10",          'expected_output' => "4",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Subarrays with exactly k distinct ────────────────────────
        $seed(27, [
            ['input' => "5\n1 2 1 2 3\n2",  'expected_output' => "7",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2 1 2\n2",    'expected_output' => "6",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 1 1\n1",       'expected_output' => "6",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2 3 4\n2",    'expected_output' => "3",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Minimum window substring ─────────────────────────────────
        $seed(28, [
            ['input' => "ADOBECODEBANC\nABC",  'expected_output' => "4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "aa\naa",               'expected_output' => "2",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "a\na",                 'expected_output' => "1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "a\nb",                 'expected_output' => "0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Isomorphic strings ───────────────────────────────────────
        $seed(29, [
            ['input' => "egg\nadd",    'expected_output' => "yes",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "foo\nbar",    'expected_output' => "no",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "paper\ntitle",'expected_output' => "yes",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "ab\naa",      'expected_output' => "no",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Min removals to make anagrams ────────────────────────────
        $seed(30, [
            ['input' => "abc\nbcd",      'expected_output' => "2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "anagram\nnagaram", 'expected_output' => "0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "ab\ncd",         'expected_output' => "4",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "hello\nhello",   'expected_output' => "0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Validate BST via in-order ────────────────────────────────
        $seed(31, [
            ['input' => "5\n1 3 4 5 7",  'expected_output' => "valid",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 3 2 4",    'expected_output' => "invalid",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2 3",       'expected_output' => "valid",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4 3 2 1",    'expected_output' => "invalid",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: LCA in BST ───────────────────────────────────────────────
        $seed(32, [
            ['input' => "5\n6 2 8 0 4\n0\n4",  'expected_output' => "2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n6 2 8 0 4\n2\n8",  'expected_output' => "6",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n4 2 6\n2\n6",       'expected_output' => "4",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n5 3 7 1\n1\n3",    'expected_output' => "3",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Diameter of BST ──────────────────────────────────────────
        $seed(33, [
            ['input' => "6\n4 2 6 1 3 5",  'expected_output' => "4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2 1 3",         'expected_output' => "2",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 2 3 4 5",    'expected_output' => "4",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5",             'expected_output' => "0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Zigzag level-order ───────────────────────────────────────
        $seed(34, [
            ['input' => "5\n5 3 7 1 4",  'expected_output' => "5\n7 3\n1 4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2 1 3",       'expected_output' => "2\n3 1",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5",           'expected_output' => "5",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4 2 6 1",    'expected_output' => "4\n6 2\n1",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: BST nodes in range ───────────────────────────────────────
        $seed(35, [
            ['input' => "6\n6 2 8 0 4 10\n3\n8",  'expected_output' => "3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n5 3 7 1 4\n1\n7",     'expected_output' => "5",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5 3 7 1 4\n3\n5",     'expected_output' => "2",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2 1 3\n1\n1",          'expected_output' => "1",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: K-th largest in stream ───────────────────────────────────
        $seed(36, [
            ['input' => "3\n5\n4\n5\n8\n2\n3",    'expected_output' => "null\nnull\n4\n4\n4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n4\n1\n2\n3\n4",        'expected_output' => "null\n1\n2\n3",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n3\n5\n3\n7",           'expected_output' => "5\n5\n7",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n3\n10\n5\n3",          'expected_output' => "null\nnull\n3",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Minimum cost to merge ropes ─────────────────────────────
        $seed(37, [
            ['input' => "4\n4 3 2 6",      'expected_output' => "29",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3",         'expected_output' => "9",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 1 1 1 1",    'expected_output' => "12",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n3 5",           'expected_output' => "8",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Sort characters by frequency ────────────────────────────
        $seed(38, [
            ['input' => "tree",    'expected_output' => "eert",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "cccaaa",  'expected_output' => "aaaccc",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "z",       'expected_output' => "z",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "aab",     'expected_output' => "aab",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Rearrange string no adjacent same ────────────────────────
        $seed(39, [
            ['input' => "aab",   'expected_output' => "aba",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "aaab",  'expected_output' => "impossible",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "aabb",  'expected_output' => "abab",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "aaabb", 'expected_output' => "ababa",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: K closest points to origin ──────────────────────────────
        $seed(40, [
            ['input' => "4 2\n1 3\n-2 2\n5 -1\n1 -1",   'expected_output' => "1 -1\n-2 2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n3 3\n5 -1\n-2 4",          'expected_output' => "3 3\n-2 4",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 1\n1 0\n0 1",                  'expected_output' => "0 1",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 2\n0 0\n1 1\n2 2",             'expected_output' => "0 0\n1 1",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Dijkstra's shortest path ─────────────────────────────────
        $seed(41, [
            ['input' => "5 7\n1 2 10\n1 3 3\n2 3 1\n2 4 2\n3 4 7\n3 5 8\n4 5 5\n1",  'expected_output' => "0 4 3 6 11",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 4\n1 2 1\n2 3 2\n3 4 3\n1 4 10\n1",                         'expected_output' => "0 1 3 6",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 3\n1 2 1\n2 3 1\n1 3 5\n1",                                 'expected_output' => "0 1 2",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 1\n1 2 3\n1",                                                'expected_output' => "0 3 INF",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Detect cycle in directed graph ───────────────────────────
        $seed(42, [
            ['input' => "4 4\n1 2\n2 3\n3 4\n4 2",  'expected_output' => "cycle",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 3\n1 2\n2 3\n3 4",        'expected_output' => "no cycle",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 3\n1 2\n2 3\n3 1",        'expected_output' => "cycle",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 2\n1 2\n1 3",             'expected_output' => "no cycle",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Topological sort (Kahn's) ───────────────────────────────
        $seed(43, [
            ['input' => "4 3\n1 2\n1 3\n3 4",                     'expected_output' => "1 2 3 4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 4\n4 3\n3 1\n3 2\n1 2",               'expected_output' => "4 3 1 2",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2\n1 2\n2 3",                           'expected_output' => "1 2 3",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 4\n5 1\n5 2\n3 1\n4 2",               'expected_output' => "3 4 5 1 2",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Check bipartite ──────────────────────────────────────────
        $seed(44, [
            ['input' => "4 4\n1 2\n1 3\n2 4\n3 4",        'expected_output' => "bipartite",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 3\n1 2\n2 3\n3 1",              'expected_output' => "not bipartite",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 3\n1 2\n2 3\n3 4",             'expected_output' => "bipartite",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "5 5\n1 2\n2 3\n3 4\n4 5\n5 1",  'expected_output' => "not bipartite",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Number of shortest paths ─────────────────────────────────
        $seed(45, [
            ['input' => "5 5\n1 2\n1 3\n2 4\n3 4\n4 5\n1",  'expected_output' => "1 1 1 2 2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 4\n1 2\n1 3\n2 4\n3 4\n1",        'expected_output' => "1 1 1 2",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2\n1 2\n2 3\n1",                   'expected_output' => "1 1 1",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4 3\n1 2\n1 3\n1 4\n1",             'expected_output' => "1 1 1 1",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Lomuto partition ─────────────────────────────────────────
        $seed(46, [
            ['input' => "5\n8 3 7 1 5",   'expected_output' => "swaps: 3\npivot index: 2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n4 3 2 1",     'expected_output' => "swaps: 1\npivot index: 0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4",     'expected_output' => "swaps: 0\npivot index: 3",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n3 1 4 1 5 2", 'expected_output' => "swaps: 3\npivot index: 2",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Count inversions ─────────────────────────────────────────
        $seed(47, [
            ['input' => "5\n1 20 6 4 5",   'expected_output' => "5",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n2 4 1 3",       'expected_output' => "3",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4 3 2 1",       'expected_output' => "6",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1 2 3 4 5",    'expected_output' => "0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Search in rotated sorted array ───────────────────────────
        $seed(48, [
            ['input' => "7\n4 5 6 7 0 1 2\n0",  'expected_output' => "4",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n3 4 5 1 2\n1",       'expected_output' => "3",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n6 7 1 2 3 5\n3",    'expected_output' => "4",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2 3 4 1\n5",         'expected_output' => "-1",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Dutch National Flag ──────────────────────────────────────
        $seed(49, [
            ['input' => "6\n2 0 2 1 1 0",  'expected_output' => "0 0 1 1 2 2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n0 1 2 0 1",    'expected_output' => "0 0 1 1 2",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n2 2 1 0",       'expected_output' => "0 1 2 2",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1 1 1 0 0 0",  'expected_output' => "0 0 0 1 1 1",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Median of two sorted arrays ─────────────────────────────
        $seed(50, [
            ['input' => "3\n1 3 5\n4\n2 4 6 8",   'expected_output' => "4.0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n2\n3 4",          'expected_output' => "2.5",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n3\n2\n1 2",            'expected_output' => "2.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 2 3\n3\n4 5 6",      'expected_output' => "3.5",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 7 Coding (Advanced) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}
