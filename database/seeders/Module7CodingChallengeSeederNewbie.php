<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 7 — Data Structures & Algorithms (Newbie / Level 1) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering beginner DSA concepts
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
 * Difficulty: Newbie — all problems solvable with pure Python, no third-party
 * libraries required. Learners build intuition for core data structures and
 * algorithm analysis.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module7CodingChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (! $category) {
            $this->command->error('Newbie category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 7 — Data Structures & Algorithms (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Data Structures & Algorithms',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Explore the fundamentals of Data Structures & Algorithms using pure Python. Analyse complexity with Big-O, manipulate arrays and stacks, simulate queues, implement linked list operations, use hash tables, traverse trees and graphs, and apply classic sorting and searching algorithms — all from scratch.',
                'time_limit_seconds' => 1200,
                'base_xp'            => 800,
                'order_index'        => 1,
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
Count the **total number of basic operations** in the following loop pattern. Given `n`, a loop runs from `i = 1` to `n` (inclusive) and for each `i`, an inner loop runs from `j = 1` to `i`. Each inner iteration does 1 operation. Read `n` and print the total operation count.

Example:
```
Input:
3
Output:
6
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Given a list of `n` integers, count how many **pairs (i, j)** satisfy `i < j` and `a[i] > a[j]` (inversions). Print the count. This is an O(n²) brute-force approach.

Example:
```
Input:
4
3 1 2 4
Output:
2
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Determine the **dominant Big-O class** of an algorithm given its operation count formula. Read `a` (coefficient of n²), `b` (coefficient of n), and `c` (constant). Print the Big-O class: `O(n^2)` if a > 0, `O(n)` if a == 0 and b > 0, else `O(1)`.

Example:
```
Input:
2
5
10
Output:
O(n^2)
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\nc = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Simulate the number of iterations in a **logarithmic loop**. Given `n`, a loop starts with `x = n` and repeatedly halves it (integer division) until `x < 1`. Count and print the number of iterations.

Example:
```
Input:
8
Output:
3
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Given two algorithms with operation counts `f(n) = a1*n + b1` and `g(n) = a2*n^2 + b2`, read `a1`, `b1`, `a2`, `b2`, and `n`. Print whichever has **fewer operations** at that value of `n`: `f` or `g`. If equal, print `equal`.

Example:
```
Input:
3
10
1
0
5
Output:
g
```
MD,
                'starter_code'        => "a1 = int(input())\nb1 = int(input())\na2 = int(input())\nb2 = int(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Arrays & Dynamic Arrays (Q6–Q10) → L339
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Read `n` integers into an array. Print the **second largest** unique value. If no second largest exists, print `none`.

Example:
```
Input:
5
3 1 4 1 5
Output:
4
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
**Rotate an array** to the right by `k` positions. Read `n`, then the array, then `k`. Print the rotated array, space-separated.

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
                'base_xp'             => 100,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Given an array of `n` integers, find the **maximum subarray sum** (Kadane's algorithm). Print the maximum sum.

Example:
```
Input:
6
-2 1 -3 4 -1 2
Output:
5
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read `n` integers. Remove all **duplicate values** while preserving the original order of first appearances. Print the resulting array, space-separated.

Example:
```
Input:
6
4 3 2 4 1 3
Output:
4 3 2 1
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Given a sorted array of `n` integers and a target sum `s`, find **two indices** (1-based, i < j) such that `a[i] + a[j] == s`. Use the two-pointer technique. Print `i j`. If no pair exists, print `none`.

Example:
```
Input:
5
1 2 3 4 6
6
Output:
2 4
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\ns = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Stacks: LIFO & Applications (Q11–Q15) → L340
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Simulate a **stack** with operations. Read `n` operations, each on its own line:
- `push x` — push integer x
- `pop` — pop and print the top element; print `empty` if stack is empty
- `peek` — print the top element without removing; print `empty` if stack is empty

Example:
```
Input:
5
push 10
push 20
peek
pop
pop
Output:
20
20
10
```
MD,
                'starter_code'        => "n = int(input())\nstack = []\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Check if a string of brackets is **balanced**. Read a string containing only `(`, `)`, `[`, `]`, `{`, `}`. Print `balanced` if every opening bracket has a matching closing bracket in correct order, otherwise print `not balanced`.

Example:
```
Input:
({[]})
Output:
balanced
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Evaluate a **postfix (Reverse Polish Notation) expression**. Read a single line of space-separated tokens (integers and operators `+`, `-`, `*`, `/`). Print the integer result. Division is integer division (truncate toward zero).

Example:
```
Input:
3 4 + 2 *
Output:
14
```
MD,
                'starter_code'        => "tokens = input().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Use a stack to **reverse a string**. Read a string, push each character onto a stack, then pop all characters and print the resulting reversed string.

Example:
```
Input:
hello
Output:
olleh
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Find the **next greater element** for each element in an array using a stack. For each element, the next greater element is the first element to its right that is larger. If none exists, use `-1`. Read `n` and the array. Print the next greater element for each index, space-separated.

Example:
```
Input:
5
4 5 2 10 8
Output:
5 10 10 -1 -1
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Queues, Deques & Priority Queues (Q16–Q20) → L341
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Simulate a **queue** with operations. Read `n` operations:
- `enqueue x` — add x to the rear
- `dequeue` — remove and print the front element; print `empty` if queue is empty
- `front` — print front element without removing; print `empty` if queue is empty

Example:
```
Input:
5
enqueue 1
enqueue 2
front
dequeue
dequeue
Output:
1
1
2
```
MD,
                'starter_code'        => "from collections import deque\nn = int(input())\nq = deque()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Simulate **circular queue** of fixed size `k`. Read `k` then `n` operations:
- `enqueue x` — add x; print `full` if queue is at capacity
- `dequeue` — remove front; print `empty` if queue is empty

Example:
```
Input:
2
5
enqueue 1
enqueue 2
enqueue 3
dequeue
dequeue
Output:
full
1
2
```
MD,
                'starter_code'        => "k = int(input())\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Simulate a **deque** (double-ended queue). Read `n` operations:
- `push_front x`
- `push_back x`
- `pop_front` — print removed element or `empty`
- `pop_back` — print removed element or `empty`

Example:
```
Input:
6
push_back 1
push_back 2
push_front 0
pop_front
pop_back
pop_front
Output:
0
2
1
```
MD,
                'starter_code'        => "from collections import deque\nn = int(input())\ndq = deque()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Simulate a **min-priority queue** using a sorted list. Read `n` operations:
- `insert x` — insert integer x
- `extract_min` — remove and print the minimum; print `empty` if queue is empty

Example:
```
Input:
5
insert 5
insert 2
insert 8
extract_min
extract_min
Output:
2
5
```
MD,
                'starter_code'        => "n = int(input())\npq = []\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Solve the **sliding window maximum** problem. Read `n` integers and window size `k`. For each window of size `k`, print the maximum. Use a deque approach.

Example:
```
Input:
6
1 3 -1 -3 5 3
3
Output:
3 3 5 5
```
MD,
                'starter_code'        => "from collections import deque\nn = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
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
Simulate a **singly linked list**. Read `n` operations:
- `append x` — add x to the end
- `prepend x` — add x to the front
- `delete x` — remove the first occurrence of x; print `not found` if absent
- `print` — print all elements space-separated, or `empty` if list is empty

Example:
```
Input:
5
append 1
append 2
prepend 0
delete 1
print
Output:
0 2
```
MD,
                'starter_code'        => "n = int(input())\nnodes = []\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Given `n` integers representing a linked list, **reverse** the list and print the elements space-separated.

Example:
```
Input:
5
1 2 3 4 5
Output:
5 4 3 2 1
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Given `n` integers representing a linked list, find the **k-th element from the end** (1-based). Print the value. If `k` exceeds the list length, print `invalid`.

Example:
```
Input:
5
10 20 30 40 50
2
Output:
40
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
**Merge two sorted linked lists** (given as sorted arrays of integers) into one sorted list and print the result space-separated.

Example:
```
Input:
3
1 3 5
3
2 4 6
Output:
1 2 3 4 5 6
```
MD,
                'starter_code'        => "n1 = int(input())\na = list(map(int, input().split()))\nn2 = int(input())\nb = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Detect if a linked list (given as a sequence of integers) has a **palindrome** structure. Read `n` integers. Print `yes` if the sequence is a palindrome, otherwise `no`.

Example:
```
Input:
5
1 2 3 2 1
Output:
yes
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Hash Tables: Dictionaries Demystified (Q26–Q30) → L343
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Read `n` words and print the **frequency** of each unique word in the order of first appearance. Format: `word: count`

Example:
```
Input:
6
apple banana apple cherry banana apple
Output:
apple: 3
banana: 2
cherry: 1
```
MD,
                'starter_code'        => "n = int(input())\nwords = input().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Given `n` integers, find the **first non-repeating element**. Print the element, or `none` if all repeat.

Example:
```
Input:
6
4 5 1 2 0 4
Output:
5
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Given two arrays of `n` integers each, find all **common elements** (intersection). Print the unique common elements in ascending order, space-separated. If none, print `none`.

Example:
```
Input:
5
1 2 3 4 5
5
3 4 5 6 7
Output:
3 4 5
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nm = int(input())\nb = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Given `n` integers, check if any **two elements sum to target** `s` using a hash set. Print `yes` if such a pair exists, otherwise `no`.

Example:
```
Input:
5
2 7 11 15 1
9
Output:
yes
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\ns = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Read `n` strings and group **anagrams** together. Print each group on a separate line, with words sorted alphabetically within each group. Groups are output in order of the first appearance of their sorted key.

Example:
```
Input:
4
eat tea tan ate
Output:
ate eat tea
tan
```
MD,
                'starter_code'        => "n = int(input())\nwords = input().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Trees: Binary Trees & BSTs (Q31–Q35) → L344
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Given a **Binary Search Tree** built by inserting `n` integers in order, print the **in-order traversal** (left → root → right) space-separated. Insert values one by one.

Example:
```
Input:
5
5 3 7 1 4
Output:
1 3 4 5 7
```
MD,
                'starter_code'        => "n = int(input())\nvalues = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Given `n` integers inserted into a BST, print the **height** of the tree (number of edges on the longest root-to-leaf path). A single-node tree has height 0.

Example:
```
Input:
5
5 3 7 1 4
Output:
2
```
MD,
                'starter_code'        => "n = int(input())\nvalues = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Given `n` integers inserted into a BST, print the **pre-order traversal** (root → left → right) space-separated.

Example:
```
Input:
5
5 3 7 1 4
Output:
5 3 1 4 7
```
MD,
                'starter_code'        => "n = int(input())\nvalues = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Given `n` integers inserted into a BST and a query value `q`, print `found` if `q` exists in the BST, otherwise `not found`.

Example:
```
Input:
5
5 3 7 1 4
4
Output:
found
```
MD,
                'starter_code'        => "n = int(input())\nvalues = list(map(int, input().split()))\nq = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Given `n` integers inserted into a BST, print the **level-order (BFS) traversal** space-separated.

Example:
```
Input:
5
5 3 7 1 4
Output:
5 3 7 1 4
```
MD,
                'starter_code'        => "from collections import deque\nn = int(input())\nvalues = list(map(int, input().split()))\n# Write your solution below\n",
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
Simulate a **min-heap** using Python's `heapq`. Read `n` operations:
- `push x` — push x onto the heap
- `pop` — remove and print the minimum; print `empty` if heap is empty

Example:
```
Input:
5
push 3
push 1
push 4
pop
pop
Output:
1
3
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\nheap = []\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Given `n` integers, use a heap to find the **k largest elements** and print them in descending order, space-separated.

Example:
```
Input:
6
3 1 4 1 5 9
3
Output:
9 5 4
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\na = list(map(int, input().split()))\nk = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Perform **heap sort** on `n` integers. Read the integers and print them in ascending order, space-separated, using a heap-based approach.

Example:
```
Input:
5
5 3 8 1 2
Output:
1 2 3 5 8
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Given `n` tasks each with a priority (lower number = higher priority) and a name (format: `priority name`), process them using a **min-heap priority queue**. Read `n` and the tasks, then print the names in processing order (by priority, ties broken alphabetically).

Example:
```
Input:
3
2 cook
1 eat
2 sleep
Output:
eat
cook
sleep
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\ntasks = []\nfor _ in range(n):\n    parts = input().split()\n    tasks.append((int(parts[0]), parts[1]))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Find the **median** of a stream of integers using two heaps. Read `n` integers one per line. After each integer, print the current median rounded to 1 decimal place.

Example:
```
Input:
4
5
2
3
8
Output:
5.0
3.5
3.0
4.0
```
MD,
                'starter_code'        => "import heapq\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Graphs: Representation, BFS & DFS (Q41–Q45) → L346
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Build an **adjacency list** from `e` undirected edges. Read `n` (nodes, 1-indexed), `e`, then `e` pairs `u v`. Print the adjacency list for each node in ascending order of node number. Format: `node: neighbor1 neighbor2 ...` (neighbors sorted ascending). If no neighbors, print `node:`.

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
1: 2 3
2: 1 4
3: 1 4
4: 2 3
```
MD,
                'starter_code'        => "n = int(input())\ne = int(input())\nadj = {i: [] for i in range(1, n+1)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n    adj[v].append(u)\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Perform **BFS** on an undirected graph. Read `n` nodes, `e` edges, and start node `s`. Print the nodes visited in BFS order, space-separated. Ties in order are broken by ascending node number.

Example:
```
Input:
5
5
1 2
1 3
2 4
3 5
2 3
1
Output:
1 2 3 4 5
```
MD,
                'starter_code'        => "from collections import deque\nn = int(input())\ne = int(input())\nadj = {i: [] for i in range(1, n+1)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n    adj[v].append(u)\ns = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Perform **DFS** on an undirected graph. Read `n` nodes, `e` edges, and start node `s`. Print the nodes visited in DFS order (iterative, using a stack; neighbours explored in ascending order), space-separated.

Example:
```
Input:
5
5
1 2
1 3
2 4
3 5
2 3
1
Output:
1 2 3 4 5
```
MD,
                'starter_code'        => "n = int(input())\ne = int(input())\nadj = {i: [] for i in range(1, n+1)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n    adj[v].append(u)\ns = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Count the number of **connected components** in an undirected graph. Read `n` nodes and `e` edges. Print the number of connected components.

Example:
```
Input:
6
4
1 2
1 3
4 5
6
Output:
3
```
MD,
                'starter_code'        => "n = int(input())\ne = int(input())\nadj = {i: [] for i in range(1, n+1)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n    adj[v].append(u)\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Find the **shortest path length** (in edges) between nodes `s` and `t` in an unweighted undirected graph using BFS. Read `n`, `e`, edges, `s`, and `t`. Print the minimum number of edges, or `-1` if unreachable.

Example:
```
Input:
5
5
1 2
1 3
2 4
3 5
4 5
1
5
Output:
2
```
MD,
                'starter_code'        => "from collections import deque\nn = int(input())\ne = int(input())\nadj = {i: [] for i in range(1, n+1)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    adj[u].append(v)\n    adj[v].append(u)\ns = int(input())\nt = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Sorting & Searching Algorithms (Q46–Q50) → L347
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Implement **bubble sort**. Read `n` integers, sort them in ascending order using bubble sort, and print the sorted array space-separated. Also print the total number of swaps performed.

Example:
```
Input:
5
5 3 8 1 2
Output:
1 2 3 5 8
swaps: 7
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Implement **binary search**. Read `n`, a sorted array of `n` integers, and a target `t`. Print the **0-based index** of `t` in the array, or `-1` if not found.

Example:
```
Input:
6
1 3 5 7 9 11
7
Output:
3
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nt = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Implement **merge sort** and print the sorted array space-separated.

Example:
```
Input:
6
5 2 8 1 9 3
Output:
1 2 3 5 8 9
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
Given a sorted array and a target `t`, find the **first and last position** of `t` using binary search. Print `first: i` and `last: j` (0-based). If not found, print `not found`.

Example:
```
Input:
8
1 2 2 2 3 4 5 5
2
Output:
first: 1
last: 3
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\nt = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Implement **counting sort** for non-negative integers. Read `n` integers (all values 0–100). Print the sorted array space-separated.

Example:
```
Input:
6
5 2 8 1 5 3
Output:
1 2 3 5 5 8
```
MD,
                'starter_code'        => "n = int(input())\na = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
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

        // ── Q1: Operation count ──────────────────────────────────────────
        $seed(1, [
            ['input' => "3",  'expected_output' => "6",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4",  'expected_output' => "10",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1",  'expected_output' => "1",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "10", 'expected_output' => "55",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Inversions ───────────────────────────────────────────────
        $seed(2, [
            ['input' => "4\n3 1 2 4",          'expected_output' => "2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3",             'expected_output' => "0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4 3 2 1",           'expected_output' => "6",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n2 4 1 3 5",         'expected_output' => "3",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Big-O class ──────────────────────────────────────────────
        $seed(3, [
            ['input' => "2\n5\n10",   'expected_output' => "O(n^2)",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n3\n5",    'expected_output' => "O(n)",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0\n7",    'expected_output' => "O(1)",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0\n0",    'expected_output' => "O(n^2)",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Logarithmic loop ─────────────────────────────────────────
        $seed(4, [
            ['input' => "8",    'expected_output' => "3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "16",   'expected_output' => "4",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1",    'expected_output' => "0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1024", 'expected_output' => "10",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Compare algorithms ───────────────────────────────────────
        $seed(5, [
            ['input' => "3\n10\n1\n0\n5",    'expected_output' => "g",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0\n1\n0\n1",     'expected_output' => "equal",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n0\n1\n0\n2",   'expected_output' => "f",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n0\n1\n0\n10",    'expected_output' => "g",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Second largest ───────────────────────────────────────────
        $seed(6, [
            ['input' => "5\n3 1 4 1 5",       'expected_output' => "4",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n7 7 7",            'expected_output' => "none",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4",          'expected_output' => "3",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10 20",            'expected_output' => "10",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Rotate array ─────────────────────────────────────────────
        $seed(7, [
            ['input' => "5\n1 2 3 4 5\n2",    'expected_output' => "4 5 1 2 3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n3",         'expected_output' => "1 2 3",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4\n1",       'expected_output' => "4 1 2 3",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n5 4 3 2 1\n0",     'expected_output' => "5 4 3 2 1",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Maximum subarray ─────────────────────────────────────────
        $seed(8, [
            ['input' => "6\n-2 1 -3 4 -1 2",  'expected_output' => "5",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n-1 -2 -3 -4",      'expected_output' => "-1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 2 3 4 5",         'expected_output' => "15",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n-2 -3 4 -1 2 1",   'expected_output' => "6",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Remove duplicates ────────────────────────────────────────
        $seed(9, [
            ['input' => "6\n4 3 2 4 1 3",    'expected_output' => "4 3 2 1",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 1 1 1",          'expected_output' => "1",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5 4 3 2 1",        'expected_output' => "5 4 3 2 1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n1 2 1 2 1 2",      'expected_output' => "1 2",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Two-sum sorted ──────────────────────────────────────────
        $seed(10, [
            ['input' => "5\n1 2 3 4 6\n6",     'expected_output' => "2 4",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2 3 4\n10",       'expected_output' => "none",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 2 3 4 5\n9",      'expected_output' => "4 5",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 3 5\n6",           'expected_output' => "2 3",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Stack operations ────────────────────────────────────────
        $seed(11, [
            ['input' => "5\npush 10\npush 20\npeek\npop\npop",          'expected_output' => "20\n20\n10",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\npop\npush 5\npop",                           'expected_output' => "empty\n5",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\npush 1\npush 2\npush 3\npeek",               'expected_output' => "3",                 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\npeek\npush 7\npeek",                         'expected_output' => "empty\n7",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Balanced brackets ───────────────────────────────────────
        $seed(12, [
            ['input' => "({[]})",     'expected_output' => "balanced",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "([)]",        'expected_output' => "not balanced",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "{[()]}",      'expected_output' => "balanced",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "(((",          'expected_output' => "not balanced",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Postfix evaluation ──────────────────────────────────────
        $seed(13, [
            ['input' => "3 4 + 2 *",     'expected_output' => "14",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5 1 2 + 4 * + 3 -", 'expected_output' => "14",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3 4 * +",      'expected_output' => "14",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "10 2 /",          'expected_output' => "5",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Reverse string ──────────────────────────────────────────
        $seed(14, [
            ['input' => "hello",     'expected_output' => "olleh",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "abcde",     'expected_output' => "edcba",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "a",          'expected_output' => "a",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "racecar",   'expected_output' => "racecar",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Next greater element ────────────────────────────────────
        $seed(15, [
            ['input' => "5\n4 5 2 10 8",     'expected_output' => "5 10 10 -1 -1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n4 3 2 1",         'expected_output' => "-1 -1 -1 -1",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4",         'expected_output' => "2 3 4 -1",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n3 1 4 2 5",       'expected_output' => "4 4 5 5 -1",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Queue operations ────────────────────────────────────────
        $seed(16, [
            ['input' => "5\nenqueue 1\nenqueue 2\nfront\ndequeue\ndequeue",  'expected_output' => "1\n1\n2",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\ndequeue\nenqueue 5\ndequeue",                    'expected_output' => "empty\n5",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nenqueue 10\nenqueue 20\nfront\ndequeue",         'expected_output' => "10\n10",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nfront\nenqueue 3\nfront",                        'expected_output' => "empty\n3",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Circular queue ──────────────────────────────────────────
        $seed(17, [
            ['input' => "2\n5\nenqueue 1\nenqueue 2\nenqueue 3\ndequeue\ndequeue",  'expected_output' => "full\n1\n2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n4\nenqueue 1\nenqueue 2\nenqueue 3\ndequeue",           'expected_output' => "1",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n3\nenqueue 5\nenqueue 6\ndequeue",                      'expected_output' => "full\n5",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n2\ndequeue\nenqueue 9",                                 'expected_output' => "empty",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Deque operations ────────────────────────────────────────
        $seed(18, [
            ['input' => "6\npush_back 1\npush_back 2\npush_front 0\npop_front\npop_back\npop_front",  'expected_output' => "0\n2\n1",         'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\npop_front\npush_back 5\npop_front",                                        'expected_output' => "empty\n5",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\npush_front 1\npush_front 2\npop_back\npop_back",                           'expected_output' => "1\n2",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\npop_back\npush_front 7",                                                   'expected_output' => "empty",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Min-priority queue ──────────────────────────────────────
        $seed(19, [
            ['input' => "5\ninsert 5\ninsert 2\ninsert 8\nextract_min\nextract_min",  'expected_output' => "2\n5",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nextract_min\ninsert 3\nextract_min",                       'expected_output' => "empty\n3",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\ninsert 10\ninsert 1\ninsert 5\nextract_min",               'expected_output' => "1",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\ninsert 7\ninsert 7\nextract_min",                          'expected_output' => "7",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Sliding window maximum ──────────────────────────────────
        $seed(20, [
            ['input' => "6\n1 3 -1 -3 5 3\n3",    'expected_output' => "3 3 5 5",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n4 3 2 1\n2",            'expected_output' => "4 3 2",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 2 3 4 5\n1",          'expected_output' => "1 2 3 4 5",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n5 4 3 2 1\n3",          'expected_output' => "5 4 3",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Linked list operations ──────────────────────────────────
        $seed(21, [
            ['input' => "5\nappend 1\nappend 2\nprepend 0\ndelete 1\nprint",  'expected_output' => "0 2",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nappend 5\ndelete 9\nprint",                        'expected_output' => "not found\n5",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nprepend 3\nprepend 1\nappend 5\nprint",            'expected_output' => "1 3 5",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nprint\nappend 7",                                  'expected_output' => "empty",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Reverse linked list ─────────────────────────────────────
        $seed(22, [
            ['input' => "5\n1 2 3 4 5",    'expected_output' => "5 4 3 2 1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n42",            'expected_output' => "42",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n7 8 9",         'expected_output' => "9 8 7",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4 3 2 1",       'expected_output' => "1 2 3 4",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Kth from end ────────────────────────────────────────────
        $seed(23, [
            ['input' => "5\n10 20 30 40 50\n2",  'expected_output' => "40",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n5",            'expected_output' => "invalid",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n5 10 15 20\n1",       'expected_output' => "20",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n5 10 15 20\n4",       'expected_output' => "5",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Merge sorted lists ──────────────────────────────────────
        $seed(24, [
            ['input' => "3\n1 3 5\n3\n2 4 6",    'expected_output' => "1 2 3 4 5 6",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 5\n2\n2 3",          'expected_output' => "1 2 3 5",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n10\n1\n5",             'expected_output' => "5 10",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 2 3\n3\n1 2 3",     'expected_output' => "1 1 2 2 3 3",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Palindrome list ─────────────────────────────────────────
        $seed(25, [
            ['input' => "5\n1 2 3 2 1",    'expected_output' => "yes",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2 3 4",       'expected_output' => "no",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n7",             'expected_output' => "yes",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2 2 1",       'expected_output' => "yes",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Word frequency ──────────────────────────────────────────
        $seed(26, [
            ['input' => "6\napple banana apple cherry banana apple",  'expected_output' => "apple: 3\nbanana: 2\ncherry: 1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nhello world hello",                        'expected_output' => "hello: 2\nworld: 1",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\na b c d",                                  'expected_output' => "a: 1\nb: 1\nc: 1\nd: 1",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\nx x x x x",                                'expected_output' => "x: 5",                          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: First non-repeating ─────────────────────────────────────
        $seed(27, [
            ['input' => "6\n4 5 1 2 0 4",   'expected_output' => "5",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 1 2 2",         'expected_output' => "none",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n3 3 3 3 7",       'expected_output' => "7",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n9 8 9",            'expected_output' => "8",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Intersection ────────────────────────────────────────────
        $seed(28, [
            ['input' => "5\n1 2 3 4 5\n5\n3 4 5 6 7",  'expected_output' => "3 4 5",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n3\n4 5 6",           'expected_output' => "none",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 2 3\n3\n2 3 4",         'expected_output' => "2 3",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n5 10\n2\n5 10",             'expected_output' => "5 10",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Two-sum hash ────────────────────────────────────────────
        $seed(29, [
            ['input' => "5\n2 7 11 15 1\n9",   'expected_output' => "yes",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2 3 4\n10",       'expected_output' => "no",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5 5 5\n10",          'expected_output' => "yes",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 2 3\n7",           'expected_output' => "no",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Group anagrams ──────────────────────────────────────────
        $seed(30, [
            ['input' => "4\neat tea tan ate",       'expected_output' => "ate eat tea\ntan",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nabc bca xyz",           'expected_output' => "abc bca\nxyz",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nhello world",           'expected_output' => "hello\nworld",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\ncar arc rac dog",       'expected_output' => "arc car rac\ndog",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: BST in-order ────────────────────────────────────────────
        $seed(31, [
            ['input' => "5\n5 3 7 1 4",    'expected_output' => "1 3 4 5 7",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2 1 3",         'expected_output' => "1 2 3",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4 2 6 1",       'expected_output' => "1 2 4 6",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n10",            'expected_output' => "10",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: BST height ──────────────────────────────────────────────
        $seed(32, [
            ['input' => "5\n5 3 7 1 4",  'expected_output' => "2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3",       'expected_output' => "2",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5",           'expected_output' => "0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4 2 6 1",    'expected_output' => "2",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: BST pre-order ───────────────────────────────────────────
        $seed(33, [
            ['input' => "5\n5 3 7 1 4",  'expected_output' => "5 3 1 4 7",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2 1 3",       'expected_output' => "2 1 3",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4 2 6 1",    'expected_output' => "4 2 1 6",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n10",          'expected_output' => "10",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: BST search ──────────────────────────────────────────────
        $seed(34, [
            ['input' => "5\n5 3 7 1 4\n4",   'expected_output' => "found",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n5 3 7 1 4\n6",   'expected_output' => "not found",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2 1 3\n1",        'expected_output' => "found",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2 1 3\n9",        'expected_output' => "not found",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: BST level-order ─────────────────────────────────────────
        $seed(35, [
            ['input' => "5\n5 3 7 1 4",  'expected_output' => "5 3 7 1 4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2 1 3",       'expected_output' => "2 1 3",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4 2 6 1",    'expected_output' => "4 2 6 1",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n10",          'expected_output' => "10",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Min-heap push/pop ───────────────────────────────────────
        $seed(36, [
            ['input' => "5\npush 3\npush 1\npush 4\npop\npop",   'expected_output' => "1\n3",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\npop\npush 5\npop",                    'expected_output' => "empty\n5",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\npush 9\npush 2\npush 7\npop",         'expected_output' => "2",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\npush 1\npop\npop",                    'expected_output' => "1\nempty",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: K largest ───────────────────────────────────────────────
        $seed(37, [
            ['input' => "6\n3 1 4 1 5 9\n3",   'expected_output' => "9 5 4",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n10 20 5 15 25\n2",  'expected_output' => "25 20",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4 3 2 1\n4",         'expected_output' => "4 3 2 1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n7 7 7\n2",           'expected_output' => "7 7",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Heap sort ───────────────────────────────────────────────
        $seed(38, [
            ['input' => "5\n5 3 8 1 2",    'expected_output' => "1 2 3 5 8",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3 1 2",         'expected_output' => "1 2 3",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4 4 4 4",       'expected_output' => "4 4 4 4",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n9",             'expected_output' => "9",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Task priority queue ─────────────────────────────────────
        $seed(39, [
            ['input' => "3\n2 cook\n1 eat\n2 sleep",    'expected_output' => "eat\ncook\nsleep",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3 c\n1 a\n2 b",             'expected_output' => "a\nb\nc",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1 z\n1 a",                  'expected_output' => "a\nz",                 'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2 d\n1 b\n3 a\n1 c",        'expected_output' => "b\nc\nd\na",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Median of stream ────────────────────────────────────────
        $seed(40, [
            ['input' => "4\n5\n2\n3\n8",    'expected_output' => "5.0\n3.5\n3.0\n4.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",        'expected_output' => "1.0\n1.5\n2.0",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n10\n20",         'expected_output' => "10.0\n15.0",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n3\n1\n2",        'expected_output' => "3.0\n2.0\n2.0",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Adjacency list ──────────────────────────────────────────
        $seed(41, [
            ['input' => "4\n4\n1 2\n1 3\n2 4\n3 4",   'expected_output' => "1: 2 3\n2: 1 4\n3: 1 4\n4: 2 3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2\n1 2\n2 3",              'expected_output' => "1: 2\n2: 1 3\n3: 2",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0",                         'expected_output' => "1:\n2:\n3:",                      'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1\n1 2",                   'expected_output' => "1: 2\n2: 1",                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: BFS ─────────────────────────────────────────────────────
        $seed(42, [
            ['input' => "5\n5\n1 2\n1 3\n2 4\n3 5\n2 3\n1",   'expected_output' => "1 2 3 4 5",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2\n1 2\n2 3\n1",                   'expected_output' => "1 2 3",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n3\n1 2\n1 3\n3 4\n1",              'expected_output' => "1 2 3 4",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2\n1 2\n3 4\n3",                   'expected_output' => "3 4",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: DFS ─────────────────────────────────────────────────────
        $seed(43, [
            ['input' => "5\n5\n1 2\n1 3\n2 4\n3 5\n2 3\n1",   'expected_output' => "1 2 3 4 5",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2\n1 2\n2 3\n1",                   'expected_output' => "1 2 3",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n3\n1 2\n1 3\n3 4\n1",              'expected_output' => "1 2 3 4",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n3\n1 2\n2 3\n1 3\n2",              'expected_output' => "2 1 3",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Connected components ────────────────────────────────────
        $seed(44, [
            ['input' => "6\n4\n1 2\n1 3\n4 5\n6",   'expected_output' => "3",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n4\n1 2\n2 3\n3 4\n4 1", 'expected_output' => "1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0",                       'expected_output' => "5",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2\n1 2\n3 4",            'expected_output' => "2",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Shortest path ───────────────────────────────────────────
        $seed(45, [
            ['input' => "5\n5\n1 2\n1 3\n2 4\n3 5\n4 5\n1\n5",   'expected_output' => "2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n3\n1 2\n2 3\n3 4\n1\n4",             'expected_output' => "3",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n2\n1 2\n3 4\n1\n4",                  'expected_output' => "-1",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n3\n1 2\n2 3\n1 3\n1\n3",             'expected_output' => "1",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Bubble sort ─────────────────────────────────────────────
        $seed(46, [
            ['input' => "5\n5 3 8 1 2",    'expected_output' => "1 2 3 5 8\nswaps: 7",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3",         'expected_output' => "1 2 3\nswaps: 0",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4 3 2 1",       'expected_output' => "1 2 3 4\nswaps: 6",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5",             'expected_output' => "5\nswaps: 0",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Binary search ───────────────────────────────────────────
        $seed(47, [
            ['input' => "6\n1 3 5 7 9 11\n7",   'expected_output' => "3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n2 4 6 8 10\n5",     'expected_output' => "-1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4\n1",         'expected_output' => "0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2 3 4\n4",         'expected_output' => "3",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Merge sort ──────────────────────────────────────────────
        $seed(48, [
            ['input' => "6\n5 2 8 1 9 3",   'expected_output' => "1 2 3 5 8 9",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n4 3 2 1",         'expected_output' => "1 2 3 4",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n7",               'expected_output' => "7",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n5 5 5 5 5",       'expected_output' => "5 5 5 5 5",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: First and last position ─────────────────────────────────
        $seed(49, [
            ['input' => "8\n1 2 2 2 3 4 5 5\n2",   'expected_output' => "first: 1\nlast: 3",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1 2 3 4 5\n6",          'expected_output' => "not found",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 1 1 1\n1",            'expected_output' => "first: 0\nlast: 3",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 2 3\n3",              'expected_output' => "first: 2\nlast: 2",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Counting sort ───────────────────────────────────────────
        $seed(50, [
            ['input' => "6\n5 2 8 1 5 3",    'expected_output' => "1 2 3 5 5 8",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0 0 1 1",          'expected_output' => "0 0 1 1",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n10 1 5 3 7",       'expected_output' => "1 3 5 7 10",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n100 50 0",         'expected_output' => "0 50 100",    'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 7 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}