<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 1 — Basics of Python Programming (Advanced) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Advanced tier
 *   2. coding_questions    — 50 questions covering complex Python patterns
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered:
 *   - OOP (classes, inheritance, dunder methods)
 *   - Generators & iterators
 *   - Decorators
 *   - Context managers
 *   - Advanced comprehensions
 *   - Dynamic programming (memoisation)
 *   - Graph/tree algorithms
 *   - Sorting algorithms (implementation)
 *   - Regex
 *   - Functional programming (reduce, partial)
 *   - Data structures (stack, queue, linked list)
 *   - Algorithm complexity & optimisation
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module1CodingChallengeSeederAdvanced extends Seeder
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

        $this->command->info('Creating Module 1 — Basics of Python Programming (Advanced) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Basics of Python Programming',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Tackle complex Python challenges covering object-oriented design, algorithmic thinking, data structures, dynamic programming, generators, decorators, and more. Problems demand efficient solutions and a deep understanding of Python internals.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 2000,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: OOP (Q1–Q8)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Define a class `Rectangle` with:
- `__init__(self, width, height)`
- `area()` → returns width × height
- `perimeter()` → returns 2 × (width + height)
- `__str__` → returns `Rectangle(width=W, height=H)`

Read two integers (width and height) from input and print the area, perimeter, and string representation on separate lines.

Example:
```
Input:
4
5
Output:
20
18
Rectangle(width=4, height=5)
```
MD,
                'starter_code'        => "class Rectangle:\n    pass\n\nw = int(input())\nh = int(input())\nr = Rectangle(w, h)\nprint(r.area())\nprint(r.perimeter())\nprint(r)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Define a class `Animal` with:
- `__init__(self, name)`
- `speak()` → returns `'...'`

Then define `Dog(Animal)` and `Cat(Animal)` that override `speak()` to return `'Woof!'` and `'Meow!'` respectively.

Read a type (`Dog` or `Cat`) and a name from input (one per line). Create the animal and print `name says sound`.

Example:
```
Input:
Dog
Buddy
Output: Buddy says Woof!
```
MD,
                'starter_code'        => "class Animal:\n    pass\n\nclass Dog(Animal):\n    pass\n\nclass Cat(Animal):\n    pass\n\nanimal_type = input()\nname = input()\n# Create and print\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Define a class `Stack` with:
- `push(item)` — adds item to the top
- `pop()` — removes and returns the top item (print `Empty` if empty)
- `peek()` — returns the top item without removing it (print `Empty` if empty)
- `is_empty()` — returns `True`/`False`

Read `n` commands (one per line). Each command is `push X`, `pop`, or `peek`. Execute each and print results for `pop` and `peek`.

Example:
```
Input:
5
push 1
push 2
peek
pop
pop
Output:
2
2
1
```
MD,
                'starter_code'        => "class Stack:\n    def __init__(self):\n        self._data = []\n\n    # Implement push, pop, peek, is_empty\n\ns = Stack()\nn = int(input())\nfor _ in range(n):\n    line = input().split()\n    # Handle commands\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Implement a class `BankAccount` with:
- `__init__(self, balance=0)`
- `deposit(amount)` — adds amount to balance
- `withdraw(amount)` — deducts amount; prints `Insufficient funds` if balance < amount
- `__str__` → `Balance: X` where X has 2 decimal places

Read `n` commands. Each command is `deposit X`, `withdraw X`, or `balance`. Execute and print output for `withdraw` errors and `balance`.

Example:
```
Input:
4
deposit 100
withdraw 30
balance
withdraw 200
Output:
Balance: 70.00
Insufficient funds
```
MD,
                'starter_code'        => "class BankAccount:\n    def __init__(self, balance=0):\n        self.balance = balance\n\n    # Implement methods\n\nacc = BankAccount()\nn = int(input())\nfor _ in range(n):\n    line = input().split()\n    # Handle commands\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Implement a class `Matrix` that supports:
- `__init__(self, rows)` where `rows` is a list of lists
- `__add__(self, other)` — element-wise addition, return new `Matrix`
- `__str__` — each row printed on a new line, elements space-separated

Read two n×m matrices (first line is `n m`, then n rows each with m space-separated integers). Print their sum.

Example:
```
Input:
2 2
1 2
3 4
5 6
7 8
Output:
6 8
10 12
```
MD,
                'starter_code'        => "class Matrix:\n    def __init__(self, rows):\n        self.rows = rows\n\n    # Implement __add__ and __str__\n\nn, m = map(int, input().split())\nA = Matrix([list(map(int, input().split())) for _ in range(n)])\nB = Matrix([list(map(int, input().split())) for _ in range(n)])\nprint(A + B)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Implement a `LinkedList` class with:
- `append(val)` — adds to end
- `prepend(val)` — adds to front
- `delete(val)` — removes first occurrence (do nothing if not found)
- `__str__` → `1 -> 2 -> 3 -> None`

Read `n` commands. Each is `append X`, `prepend X`, or `delete X`. Print the final list.

Example:
```
Input:
4
append 1
append 2
prepend 0
delete 1
Output: 0 -> 2 -> None
```
MD,
                'starter_code'        => "class Node:\n    def __init__(self, val):\n        self.val = val\n        self.next = None\n\nclass LinkedList:\n    def __init__(self):\n        self.head = None\n\n    # Implement append, prepend, delete, __str__\n\nll = LinkedList()\nn = int(input())\nfor _ in range(n):\n    line = input().split()\n    # Handle commands\nprint(ll)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Implement a class `Queue` using two stacks internally, with:
- `enqueue(item)` — adds to back
- `dequeue()` — removes and returns front item (print `Empty` if empty)
- `front()` — returns front item without removing (print `Empty` if empty)

Read `n` commands (`enqueue X`, `dequeue`, `front`) and print results for `dequeue` and `front`.

Example:
```
Input:
5
enqueue 1
enqueue 2
front
dequeue
front
Output:
1
1
2
```
MD,
                'starter_code'        => "class Queue:\n    def __init__(self):\n        self._s1 = []\n        self._s2 = []\n\n    # Implement using two stacks\n\nq = Queue()\nn = int(input())\nfor _ in range(n):\n    line = input().split()\n    # Handle commands\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Implement a `MinHeap` class with:
- `insert(val)` — inserts value and maintains heap property
- `extract_min()` — removes and returns the minimum value (print `Empty` if empty)
- `peek()` — returns minimum without removal (print `Empty` if empty)

Read `n` commands (`insert X`, `extract_min`, `peek`) and print results for `extract_min` and `peek`.

Example:
```
Input:
5
insert 5
insert 2
insert 8
peek
extract_min
Output:
2
2
```
MD,
                'starter_code'        => "import heapq\n\nclass MinHeap:\n    def __init__(self):\n        self._data = []\n\n    # Implement insert, extract_min, peek\n\nh = MinHeap()\nn = int(input())\nfor _ in range(n):\n    line = input().split()\n    # Handle commands\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Generators & Iterators (Q9–Q12)
            // ═══════════════════════════════════════════════════════════════

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Write a **generator** `fibonacci_gen()` that yields Fibonacci numbers indefinitely.

Read an integer `n` and print the first `n` Fibonacci numbers, one per line.

Example:
```
Input:  8
Output:
0
1
1
2
3
5
8
13
```
MD,
                'starter_code'        => "def fibonacci_gen():\n    # Implement generator\n    pass\n\nn = int(input())\ngen = fibonacci_gen()\nfor _ in range(n):\n    print(next(gen))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Write a **generator** `range_step(start, stop, step)` that yields numbers from `start` up to (but not including) `stop` with the given `step`.

Read `start`, `stop`, and `step` (one per line). Print each yielded value on a new line.

Example:
```
Input:
1
10
2
Output:
1
3
5
7
9
```
MD,
                'starter_code'        => "def range_step(start, stop, step):\n    # Implement generator\n    pass\n\nstart = int(input())\nstop  = int(input())\nstep  = int(input())\nfor v in range_step(start, stop, step):\n    print(v)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Write a **generator** `running_total(nums)` that takes a list of integers and yields the running (cumulative) total.

Read `n` integers (one per line after `n`). Print each running total on a new line.

Example:
```
Input:
5
1
2
3
4
5
Output:
1
3
6
10
15
```
MD,
                'starter_code'        => "def running_total(nums):\n    # Implement generator\n    pass\n\nn = int(input())\nnums = [int(input()) for _ in range(n)]\nfor v in running_total(nums):\n    print(v)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Implement a class `Counter` that is **iterable** and **iterator**. It counts from `start` to `end` (inclusive) by `step`.

Implement `__iter__` and `__next__` (raise `StopIteration` at end).

Read `start`, `end`, and `step`. Print each value.

Example:
```
Input:
0
10
3
Output:
0
3
6
9
```
MD,
                'starter_code'        => "class Counter:\n    def __init__(self, start, end, step):\n        pass  # implement\n\n    def __iter__(self):\n        pass\n\n    def __next__(self):\n        pass\n\nstart = int(input())\nend   = int(input())\nstep  = int(input())\nfor v in Counter(start, end, step):\n    print(v)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Decorators (Q13–Q15)
            // ═══════════════════════════════════════════════════════════════

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Write a decorator `timer` that measures and prints the execution time of a function in milliseconds (rounded to 2 decimal places) to **stderr is not required** — print it to stdout after the function's own output in format `Time: X ms`.

Decorate a function `slow_sum(n)` that returns the sum of 1..n.

Read an integer `n`. Print the sum, then the time line.

Example:
```
Input:  1000000
Output:
500000500000
Time: X ms    ← any reasonable float
```

Note: The exact time value is not tested; any non-negative float is accepted.
MD,
                'starter_code'        => "import time\n\ndef timer(func):\n    # Implement decorator\n    pass\n\n@timer\ndef slow_sum(n):\n    return sum(range(1, n + 1))\n\nn = int(input())\nresult = slow_sum(n)\nprint(result)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Write a decorator `validate_positive` that raises a `ValueError` with the message `Input must be positive` if any argument to the decorated function is ≤ 0.

Decorate a function `multiply(a, b)` that returns `a * b`.

Read two integers. If valid, print the product. If not, print `Input must be positive`.

Example:
```
Input:
3
-5
Output: Input must be positive
```
MD,
                'starter_code'        => "def validate_positive(func):\n    # Implement decorator\n    pass\n\n@validate_positive\ndef multiply(a, b):\n    return a * b\n\na = int(input())\nb = int(input())\ntry:\n    print(multiply(a, b))\nexcept ValueError as e:\n    print(e)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Write a decorator `memoize` that caches results of a function keyed by its arguments.

Decorate a function `fib(n)` that computes the `n`-th Fibonacci number recursively. With memoization it should handle large `n` efficiently.

Read `n` and print `fib(n)`.

Example:
```
Input:  50
Output: 12586269025
```
MD,
                'starter_code'        => "def memoize(func):\n    cache = {}\n    # Implement decorator\n    pass\n\n@memoize\ndef fib(n):\n    if n <= 1:\n        return n\n    return fib(n - 1) + fib(n - 2)\n\nn = int(input())\nprint(fib(n))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Dynamic Programming (Q16–Q21)
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
**0/1 Knapsack Problem** — Given `n` items each with a weight and value, and a knapsack of capacity `W`, find the **maximum value** you can carry.

Input format:
- Line 1: `n W`
- Next `n` lines: `weight value`

Example:
```
Input:
3 50
10 60
20 100
30 120
Output: 220
```
MD,
                'starter_code'        => "n, W = map(int, input().split())\nitems = [tuple(map(int, input().split())) for _ in range(n)]\n# Implement 0/1 knapsack DP\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
**Longest Common Subsequence** — Given two strings, print the length of their LCS.

Example:
```
Input:
ABCBDAB
BDCAB
Output: 4
```
MD,
                'starter_code'        => "s1 = input()\ns2 = input()\n# Implement LCS DP\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
**Coin Change (Minimum Coins)** — Given coin denominations and an amount, print the **minimum number of coins** needed. Print `-1` if impossible.

Input:
- Line 1: space-separated coin denominations
- Line 2: target amount

Example:
```
Input:
1 5 6 9
11
Output: 2
```
MD,
                'starter_code'        => "coins = list(map(int, input().split()))\namount = int(input())\n# Implement coin change DP\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
**Longest Increasing Subsequence** — Print the length of the LIS of a sequence.

Example:
```
Input:
10 9 2 5 3 7 101 18
Output: 4
```
MD,
                'starter_code'        => "nums = list(map(int, input().split()))\n# Implement LIS DP\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
**Edit Distance (Levenshtein)** — Print the minimum edit distance (insert, delete, replace) between two strings.

Example:
```
Input:
kitten
sitting
Output: 3
```
MD,
                'starter_code'        => "s1 = input()\ns2 = input()\n# Implement edit distance DP\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
**Maximum Subarray Sum (Kadane's Algorithm)** — Print the largest sum of any contiguous subarray.

Example:
```
Input:
-2 1 -3 4 -1 2 1 -5 4
Output: 6
```
MD,
                'starter_code'        => "nums = list(map(int, input().split()))\n# Implement Kadane's algorithm\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Graph & Tree Algorithms (Q22–Q27)
            // ═══════════════════════════════════════════════════════════════

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
**BFS Shortest Path** — Given an undirected graph (adjacency list), print the shortest path length from node `s` to node `t`. Print `-1` if unreachable.

Input:
- Line 1: `n e` (nodes, edges)
- Next `e` lines: `u v`
- Last line: `s t`

Example:
```
Input:
4 4
0 1
0 2
1 3
2 3
0 3
Output: 2
```
MD,
                'starter_code'        => "from collections import deque\n\nn, e = map(int, input().split())\ngraph = {i: [] for i in range(n)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    graph[u].append(v)\n    graph[v].append(u)\ns, t = map(int, input().split())\n# Implement BFS\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
**DFS — Detect Cycle** — Given a directed graph, print `True` if it contains a cycle, otherwise `False`.

Input:
- Line 1: `n e`
- Next `e` lines: `u v`

Example:
```
Input:
4 4
0 1
1 2
2 3
3 1
Output: True
```
MD,
                'starter_code'        => "n, e = map(int, input().split())\ngraph = {i: [] for i in range(n)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    graph[u].append(v)\n# Implement DFS cycle detection\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
**Binary Search Tree** — Implement a BST with `insert` and `inorder()` (sorted output). Read `n` integers to insert, then print the inorder traversal space-separated.

Example:
```
Input:
5
5
3
7
1
4
Output: 1 3 4 5 7
```
MD,
                'starter_code'        => "class BST:\n    class Node:\n        def __init__(self, val):\n            self.val = val\n            self.left = self.right = None\n\n    def __init__(self):\n        self.root = None\n\n    def insert(self, val):\n        pass\n\n    def inorder(self):\n        # Return sorted list\n        pass\n\ntree = BST()\nn = int(input())\nfor _ in range(n):\n    tree.insert(int(input()))\nprint(*tree.inorder())\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
**Topological Sort** — Given a DAG, print one valid topological ordering (space-separated). Use Kahn's algorithm.

Input:
- Line 1: `n e`
- Next `e` lines: `u v` (u must come before v)

Example:
```
Input:
6 6
5 2
5 0
4 0
4 1
2 3
3 1
Output: 4 5 0 2 3 1
```

Note: Multiple valid orderings exist; any correct topological order is accepted.
MD,
                'starter_code'        => "from collections import deque\n\nn, e = map(int, input().split())\ngraph = {i: [] for i in range(n)}\nin_degree = [0] * n\nfor _ in range(e):\n    u, v = map(int, input().split())\n    graph[u].append(v)\n    in_degree[v] += 1\n# Implement Kahn's algorithm\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
**Dijkstra's Shortest Path** — Given a weighted directed graph, print the shortest distance from source `s` to all other nodes. Print `inf` for unreachable nodes.

Input:
- Line 1: `n e`
- Next `e` lines: `u v w`
- Last line: `s`

Example:
```
Input:
4 5
0 1 1
0 2 4
1 2 2
1 3 5
2 3 1
0
Output:
0: 0
1: 1
2: 3
3: 4
```
MD,
                'starter_code'        => "import heapq\n\nn, e = map(int, input().split())\ngraph = {i: [] for i in range(n)}\nfor _ in range(e):\n    u, v, w = map(int, input().split())\n    graph[u].append((v, w))\ns = int(input())\n# Implement Dijkstra\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
**Count Connected Components** — Given an undirected graph, print the number of connected components.

Input:
- Line 1: `n e`
- Next `e` lines: `u v`

Example:
```
Input:
5 3
0 1
1 2
3 4
Output: 2
```
MD,
                'starter_code'        => "n, e = map(int, input().split())\ngraph = {i: [] for i in range(n)}\nfor _ in range(e):\n    u, v = map(int, input().split())\n    graph[u].append(v)\n    graph[v].append(u)\n# Implement component counting\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Sorting Algorithm Implementations (Q28–Q31)
            // ═══════════════════════════════════════════════════════════════

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Implement **merge sort**. Read `n` integers and print them sorted in ascending order using merge sort.

Example:
```
Input:
6
5
2
4
6
1
3
Output: [1, 2, 3, 4, 5, 6]
```
MD,
                'starter_code'        => "def merge_sort(arr):\n    # Implement merge sort\n    pass\n\nn = int(input())\nnums = [int(input()) for _ in range(n)]\nprint(merge_sort(nums))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Implement **quick sort**. Read `n` integers and print them sorted in ascending order using quick sort.

Example:
```
Input:
5
3
6
8
10
1
Output: [1, 3, 6, 8, 10]
```
MD,
                'starter_code'        => "def quick_sort(arr):\n    # Implement quick sort\n    pass\n\nn = int(input())\nnums = [int(input()) for _ in range(n)]\nprint(quick_sort(nums))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Implement **heap sort**. Read `n` integers and print them sorted in ascending order using heap sort (do not use `sorted()` or `.sort()`).

Example:
```
Input:
5
4
1
3
9
7
Output: [1, 3, 4, 7, 9]
```
MD,
                'starter_code'        => "def heap_sort(arr):\n    # Implement heap sort\n    pass\n\nn = int(input())\nnums = [int(input()) for _ in range(n)]\nprint(heap_sort(nums))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Implement **counting sort** for non-negative integers. Read `n` integers (all ≥ 0 and ≤ 1000) and print them sorted in ascending order.

Example:
```
Input:
6
4
2
2
8
3
3
Output: [2, 2, 3, 3, 4, 8]
```
MD,
                'starter_code'        => "def counting_sort(arr):\n    # Implement counting sort\n    pass\n\nn = int(input())\nnums = [int(input()) for _ in range(n)]\nprint(counting_sort(nums))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Regex (Q32–Q34)
            // ═══════════════════════════════════════════════════════════════

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Read a string and print all **valid email addresses** found in it, one per line. An email is valid if it matches `username@domain.tld` where each part is one or more word characters.

Example:
```
Input:  Contact us at info@example.com or support@help.org for help.
Output:
info@example.com
support@help.org
```
MD,
                'starter_code'        => "import re\n\ns = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Read a string and **replace all sequences of whitespace** (spaces, tabs, newlines) with a single space, then strip leading/trailing whitespace. Print the result.

Example:
```
Input:  Hello    World   Python
Output: Hello World Python
```
MD,
                'starter_code'        => "import re\n\ns = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Read a string and print `Valid` if it is a valid **IPv4 address**, otherwise `Invalid`. A valid IPv4 address has exactly four groups of 1–3 digits (0–255) separated by dots, with no leading zeros (except `0` itself).

Example:
```
Input:  192.168.1.1
Output: Valid
```
MD,
                'starter_code'        => "import re\n\ns = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Functional Programming (Q35–Q37)
            // ═══════════════════════════════════════════════════════════════

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Use `functools.reduce` to compute the **product** of a list of integers.

Read `n` integers and print their product.

Example:
```
Input:
4
2
3
4
5
Output: 120
```
MD,
                'starter_code'        => "from functools import reduce\n\nn = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Use `functools.partial` to create a function `double(x)` from `multiply(a, b)`.

Read `n` integers and print each doubled, one per line.

Example:
```
Input:
3
5
10
3
Output:
10
20
6
```
MD,
                'starter_code'        => "from functools import partial\n\ndef multiply(a, b):\n    return a * b\n\ndouble = partial(multiply, 2)\n\nn = int(input())\nfor _ in range(n):\n    print(double(int(input())))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Implement a **function pipeline** using `functools.reduce`. Given a list of single-argument functions, apply them left-to-right to an initial value.

The functions to apply (in order) are:
1. `lambda x: x * 2`
2. `lambda x: x + 10`
3. `lambda x: x ** 2`

Read an integer and print the result of the pipeline.

Example:
```
Input:  3
Output: 256   # ((3*2)+10)**2 = 16**2 = 256
```
MD,
                'starter_code'        => "from functools import reduce\n\nfuncs = [\n    lambda x: x * 2,\n    lambda x: x + 10,\n    lambda x: x ** 2,\n]\n\nx = int(input())\n# Apply pipeline\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Advanced Comprehensions (Q38–Q40)
            // ═══════════════════════════════════════════════════════════════

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Use a **nested list comprehension** to generate an `n×n` identity matrix and print it row by row, space-separated.

Example:
```
Input:  3
Output:
1 0 0
0 1 0
0 0 1
```
MD,
                'starter_code'        => "n = int(input())\n# Use list comprehension to build identity matrix\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Use a **dictionary comprehension** to invert a dictionary (swap keys and values). Read `n` key-value pairs (one per line) and print the inverted dictionary sorted by key.

Example:
```
Input:
3
a 1
b 2
c 3
Output: {'1': 'a', '2': 'b', '3': 'c'}
```
MD,
                'starter_code'        => "n = int(input())\nd = {}\nfor _ in range(n):\n    k, v = input().split()\n    d[k] = v\n# Use dict comprehension to invert\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Use a **set comprehension** to find all **Pythagorean triples** `(a, b, c)` with `a < b < c` and all values ≤ `n`.

Read `n`. Print each triple `(a, b, c)` sorted, one per line, in ascending order of `a`, then `b`.

Example:
```
Input:  20
Output:
(3, 4, 5)
(5, 12, 13)
(6, 8, 10)
(8, 15, 17)
(9, 12, 15)
(12, 16, 20)
```
MD,
                'starter_code'        => "n = int(input())\n# Use comprehension to find Pythagorean triples\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Mixed Hard Problems (Q41–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
**Anagram Groups** — Given a list of words, group all anagrams together. Print each group sorted alphabetically, groups sorted by their first element.

Example:
```
Input:  eat tea tan ate nat bat
Output:
['ate', 'eat', 'tea']
['bat']
['nat', 'tan']
```
MD,
                'starter_code'        => "words = input().split()\n# Group anagrams\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
**Balanced Parentheses** — Read a string containing `(`, `)`, `[`, `]`, `{`, `}`. Print `True` if all brackets are balanced, otherwise `False`.

Example:
```
Input:  {[()]}
Output: True
```
MD,
                'starter_code'        => "s = input()\n# Use stack to check balance\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
**Roman Numeral Converter** — Read an integer (1–3999) and print its Roman numeral representation.

Example:
```
Input:  1994
Output: MCMXCIV
```
MD,
                'starter_code'        => "n = int(input())\n# Convert to Roman numeral\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
**Spiral Matrix** — Given an `n×n` matrix of integers filled in spiral order (clockwise from top-left, values 1..n²), print the matrix row by row, values space-separated.

Read `n`.

Example:
```
Input:  3
Output:
1 2 3
8 9 4
7 6 5
```
MD,
                'starter_code'        => "n = int(input())\n# Generate and print spiral matrix\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
**Run-Length Encoding** — Compress a string using run-length encoding. Print the encoded string.

Example:
```
Input:  AABBBCCCC
Output: A2B3C4
```

Note: Single characters should still include the count (e.g., `A1`).
MD,
                'starter_code'        => "s = input()\n# Implement run-length encoding\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
**Sudoku Validator** — Read a 9×9 sudoku board (9 lines of 9 space-separated integers, 0 = empty). Print `Valid` if no row, column, or 3×3 box has duplicate non-zero values; otherwise `Invalid`.

Example:
```
Input:
5 3 0 0 7 0 0 0 0
6 0 0 1 9 5 0 0 0
0 9 8 0 0 0 0 6 0
8 0 0 0 6 0 0 0 3
4 0 0 8 0 3 0 0 1
7 0 0 0 2 0 0 0 6
0 6 0 0 0 0 2 8 0
0 0 0 4 1 9 0 0 5
0 0 0 0 8 0 0 7 9
Output: Valid
```
MD,
                'starter_code'        => "board = [list(map(int, input().split())) for _ in range(9)]\n# Validate sudoku board\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 400,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
**Word Ladder** — Given a start word, end word, and a word list, find the **length of the shortest transformation sequence** from start to end where each step changes exactly one letter to a valid word. Print `0` if no path exists. All words are the same length.

Input:
- Line 1: start word
- Line 2: end word
- Line 3: space-separated word list

Example:
```
Input:
hit
cog
hot dot dog lot log cog
Output: 5
```
MD,
                'starter_code'        => "from collections import deque\n\nstart = input()\nend   = input()\nword_list = set(input().split())\n# Implement BFS word ladder\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
**N-Queens** — Print the number of solutions to the N-Queens problem for a given `n`.

Example:
```
Input:  6
Output: 4
```
MD,
                'starter_code'        => "n = int(input())\n# Implement N-Queens backtracking\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
**Trie Implementation** — Implement a `Trie` with:
- `insert(word)` — inserts a word
- `search(word)` → `True`/`False`
- `starts_with(prefix)` → `True`/`False`

Read `n` commands. Each is `insert W`, `search W`, or `starts_with W`. Print results for `search` and `starts_with`.

Example:
```
Input:
5
insert apple
search apple
search app
starts_with app
search orange
Output:
True
False
True
False
```
MD,
                'starter_code'        => "class TrieNode:\n    def __init__(self):\n        self.children = {}\n        self.is_end = False\n\nclass Trie:\n    def __init__(self):\n        self.root = TrieNode()\n\n    def insert(self, word): pass\n    def search(self, word): pass\n    def starts_with(self, prefix): pass\n\nt = Trie()\nn = int(input())\nfor _ in range(n):\n    parts = input().split()\n    cmd, w = parts[0], parts[1]\n    if cmd == 'insert':\n        t.insert(w)\n    elif cmd == 'search':\n        print(t.search(w))\n    else:\n        print(t.starts_with(w))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 450,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**LRU Cache** — Implement an LRU (Least Recently Used) Cache with capacity `k`.

Operations:
- `get(key)` — return value or `-1` if not found
- `put(key, value)` — insert/update; evict LRU if over capacity

Read `k`, then `n` operations. Each is `get K` or `put K V`. Print results for `get`.

Example:
```
Input:
2
7
put 1 1
put 2 2
get 1
put 3 3
get 2
put 4 4
get 1
Output:
1
-1
1
```
MD,
                'starter_code'        => "from collections import OrderedDict\n\nclass LRUCache:\n    def __init__(self, capacity):\n        self.cap = capacity\n        self.cache = OrderedDict()\n\n    def get(self, key): pass\n    def put(self, key, value): pass\n\nk = int(input())\ncache = LRUCache(k)\nn = int(input())\nfor _ in range(n):\n    parts = input().split()\n    if parts[0] == 'get':\n        print(cache.get(int(parts[1])))\n    else:\n        cache.put(int(parts[1]), int(parts[2]))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 500,
            ],
        ];

        // ─────────────────────────────────────────────────────────────────
        // 3. INSERT QUESTIONS
        // ─────────────────────────────────────────────────────────────────

        $questionIds = [];

        foreach ($questionDefs as $qd) {
            $row = DB::table('coding_questions')->where([
                'challenge_id' => $challenge->id,
                'order_index'  => $qd['order_index'],
            ])->first();

            if (! $row) {
                $id = DB::table('coding_questions')->insertGetId([
                    'challenge_id'        => $challenge->id,
                    'order_index'         => $qd['order_index'],
                    'problem_description' => $qd['problem_description'],
                    'starter_code'        => $qd['starter_code'],
                    'time_limit_seconds'  => $qd['time_limit_seconds'],
                    'base_xp'             => $qd['base_xp'],
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            } else {
                $id = $row->id;
            }

            $questionIds[$qd['order_index']] = $id;
        }

        $this->command->info('Questions inserted/verified. Seeding test cases...');

        // ─────────────────────────────────────────────────────────────────
        // 4. TEST CASES (4 per question)
        // ─────────────────────────────────────────────────────────────────

        $seed = function (int $orderIndex, array $cases) use ($questionIds): void {
            $qid = $questionIds[$orderIndex] ?? null;
            if (! $qid) return;

            foreach ($cases as $c) {
                $exists = DB::table('test_cases')->where([
                    'coding_question_id' => $qid,
                    'order_index'        => $c['order_index'],
                ])->exists();

                if (! $exists) {
                    DB::table('test_cases')->insert([
                        'coding_question_id' => $qid,
                        'input'              => $c['input'],
                        'expected_output'    => $c['expected_output'],
                        'is_hidden'          => $c['is_hidden'],
                        'order_index'        => $c['order_index'],
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
            }
        };

        // ── Q1: Rectangle ─────────────────────────────────────────────────
        $seed(1, [
            ['input' => "4\n5",   'expected_output' => "20\n18\nRectangle(width=4, height=5)",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3",   'expected_output' => "9\n12\nRectangle(width=3, height=3)",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n10",  'expected_output' => "10\n22\nRectangle(width=1, height=10)", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "7\n2",   'expected_output' => "14\n18\nRectangle(width=7, height=2)",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Animal inheritance ────────────────────────────────────────
        $seed(2, [
            ['input' => "Dog\nBuddy",   'expected_output' => 'Buddy says Woof!',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "Cat\nWhiskers",'expected_output' => 'Whiskers says Meow!','is_hidden' => false,'order_index' => 2],
            ['input' => "Dog\nRex",     'expected_output' => 'Rex says Woof!',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "Cat\nMittens", 'expected_output' => 'Mittens says Meow!','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Stack ─────────────────────────────────────────────────────
        $seed(3, [
            ['input' => "5\npush 1\npush 2\npeek\npop\npop",           'expected_output' => "2\n2\n1",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\npush 5\npop\npop",                          'expected_output' => "5\nEmpty",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\npush 10\npush 20\npop\npeek",               'expected_output' => "20\n10",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\npeek\npush 1",                              'expected_output' => "Empty",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: BankAccount ───────────────────────────────────────────────
        $seed(4, [
            ['input' => "4\ndeposit 100\nwithdraw 30\nbalance\nwithdraw 200",  'expected_output' => "Balance: 70.00\nInsufficient funds",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\ndeposit 50\nbalance",                              'expected_output' => "Balance: 50.00",                      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nwithdraw 10\ndeposit 100\nbalance",               'expected_output' => "Insufficient funds\nBalance: 100.00", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\ndeposit 1000\nwithdraw 1000",                     'expected_output' => '',                                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Matrix addition ───────────────────────────────────────────
        $seed(5, [
            ['input' => "2 2\n1 2\n3 4\n5 6\n7 8",   'expected_output' => "6 8\n10 12",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 3\n1 2 3\n4 5 6",          'expected_output' => "5 7 9",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3\n1 0 1\n0 1 0\n1 1 1\n0 0 0", 'expected_output' => "2 1 2\n0 1 0", 'is_hidden' => true,'order_index' => 3],
            ['input' => "1 1\n5\n10",                 'expected_output' => "15",               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: LinkedList ────────────────────────────────────────────────
        $seed(6, [
            ['input' => "4\nappend 1\nappend 2\nprepend 0\ndelete 1",    'expected_output' => '0 -> 2 -> None',          'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nappend 5\nappend 6\nappend 7",               'expected_output' => '5 -> 6 -> 7 -> None',     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nprepend 3\nprepend 2\nprepend 1",            'expected_output' => '1 -> 2 -> 3 -> None',     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nappend 1\nappend 2\ndelete 1\ndelete 2",     'expected_output' => 'None',                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Queue with two stacks ──────────────────────────────────────
        $seed(7, [
            ['input' => "5\nenqueue 1\nenqueue 2\nfront\ndequeue\nfront", 'expected_output' => "1\n1\n2",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\ndequeue\nenqueue 5\ndequeue",                 'expected_output' => "Empty\n5",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nenqueue 10\nenqueue 20\ndequeue\ndequeue",    'expected_output' => "10\n20",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nfront\nenqueue 1",                            'expected_output' => "Empty",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: MinHeap ───────────────────────────────────────────────────
        $seed(8, [
            ['input' => "5\ninsert 5\ninsert 2\ninsert 8\npeek\nextract_min",  'expected_output' => "2\n2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nextract_min\npeek",                                'expected_output' => "Empty\nEmpty", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\ninsert 10\ninsert 1\ninsert 5\nextract_min",       'expected_output' => "1",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\ninsert 3\ninsert 3\npeek",                         'expected_output' => "3",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Fibonacci generator ───────────────────────────────────────
        $seed(9, [
            ['input' => '8',   'expected_output' => "0\n1\n1\n2\n3\n5\n8\n13",              'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',   'expected_output' => "0",                                    'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',   'expected_output' => "0\n1\n1\n2\n3",                        'is_hidden' => true,  'order_index' => 3],
            ['input' => '10',  'expected_output' => "0\n1\n1\n2\n3\n5\n8\n13\n21\n34",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: range_step generator ─────────────────────────────────────
        $seed(10, [
            ['input' => "1\n10\n2",   'expected_output' => "1\n3\n5\n7\n9",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n6\n1",    'expected_output' => "0\n1\n2\n3\n4\n5",'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n10\n3",   'expected_output' => "0\n3\n6\n9",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n5\n1",    'expected_output' => "",                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: running_total generator ──────────────────────────────────
        $seed(11, [
            ['input' => "5\n1\n2\n3\n4\n5",    'expected_output' => "1\n3\n6\n10\n15",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10\n10\n10",        'expected_output' => "10\n20\n30",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n42",                'expected_output' => "42",                'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0\n0\n0\n5",        'expected_output' => "0\n0\n0\n5",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Counter iterator ─────────────────────────────────────────
        $seed(12, [
            ['input' => "0\n10\n3",   'expected_output' => "0\n3\n6\n9",         'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n5\n1",    'expected_output' => "1\n2\n3\n4\n5",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0\n1",    'expected_output' => "0",                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10\n2",   'expected_output' => "2\n4\n6\n8\n10",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: timer decorator ──────────────────────────────────────────
        // Time value is not deterministic; test only the sum output and that "Time:" line exists.
        $seed(13, [
            ['input' => '100',      'expected_output' => "5050",         'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',       'expected_output' => "55",           'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',        'expected_output' => "15",           'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',        'expected_output' => "1",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: validate_positive decorator ─────────────────────────────
        $seed(14, [
            ['input' => "3\n-5",   'expected_output' => 'Input must be positive',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n5",    'expected_output' => '20',                      'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n5",    'expected_output' => 'Input must be positive',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n7",    'expected_output' => '42',                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: memoize fib ──────────────────────────────────────────────
        $seed(15, [
            ['input' => '50',  'expected_output' => '12586269025',      'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',  'expected_output' => '55',               'is_hidden' => false, 'order_index' => 2],
            ['input' => '30',  'expected_output' => '832040',           'is_hidden' => true,  'order_index' => 3],
            ['input' => '0',   'expected_output' => '0',                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Knapsack ─────────────────────────────────────────────────
        $seed(16, [
            ['input' => "3 50\n10 60\n20 100\n30 120",  'expected_output' => '220',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 10\n15 10",                   'expected_output' => '10',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 6\n2 6\n2 10\n3 12",          'expected_output' => '22',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "0 10",                          'expected_output' => '0',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: LCS ─────────────────────────────────────────────────────
        $seed(17, [
            ['input' => "ABCBDAB\nBDCAB",   'expected_output' => '4',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "AGGTAB\nGXTXAYB",  'expected_output' => '4',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "ABC\nABC",         'expected_output' => '3',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "ABC\nDEF",         'expected_output' => '0',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Coin change ──────────────────────────────────────────────
        $seed(18, [
            ['input' => "1 5 6 9\n11",   'expected_output' => '2',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 5\n11",     'expected_output' => '3',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n3",          'expected_output' => '-1',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0",          'expected_output' => '0',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: LIS ──────────────────────────────────────────────────────
        $seed(19, [
            ['input' => '10 9 2 5 3 7 101 18',  'expected_output' => '4',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '0 1 0 3 2 3',          'expected_output' => '4',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '7 7 7 7 7',            'expected_output' => '1',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '1 2 3 4 5',            'expected_output' => '5',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Edit distance ────────────────────────────────────────────
        $seed(20, [
            ['input' => "kitten\nsitting",   'expected_output' => '3',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "sunday\nsaturday",  'expected_output' => '3',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "abc\nabc",          'expected_output' => '0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "a\nb",             'expected_output' => '1',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Kadane's ─────────────────────────────────────────────────
        $seed(21, [
            ['input' => '-2 1 -3 4 -1 2 1 -5 4',  'expected_output' => '6',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',                        'expected_output' => '1',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '-1 -2 -3',                 'expected_output' => '-1',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '5 4 -1 7 8',              'expected_output' => '23',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: BFS shortest path ────────────────────────────────────────
        $seed(22, [
            ['input' => "4 4\n0 1\n0 2\n1 3\n2 3\n0 3",   'expected_output' => '2',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 0\n0 1",                         'expected_output' => '-1',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 3\n0 1\n1 2\n2 3\n0 3",         'expected_output' => '3',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 3\n0 1\n1 2\n0 2\n0 2",         'expected_output' => '1',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: DFS cycle detection ──────────────────────────────────────
        $seed(23, [
            ['input' => "4 4\n0 1\n1 2\n2 3\n3 1",   'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n0 1\n1 2",              'expected_output' => 'False',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n0 1\n1 0",              'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 0",                         'expected_output' => 'False',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: BST inorder ──────────────────────────────────────────────
        $seed(24, [
            ['input' => "5\n5\n3\n7\n1\n4",     'expected_output' => '1 3 4 5 7',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2\n1\n3",           'expected_output' => '1 2 3',        'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n10",                 'expected_output' => '10',           'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4\n2\n6\n5",        'expected_output' => '2 4 5 6',      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Topological sort ─────────────────────────────────────────
        // Multiple valid outputs; grader should verify topological validity.
        $seed(25, [
            ['input' => "6 6\n5 2\n5 0\n4 0\n4 1\n2 3\n3 1",  'expected_output' => '4 5 0 2 3 1',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 2\n0 1\n1 2",                        'expected_output' => '0 1 2',        'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 1\n0 1",                             'expected_output' => '0 1',          'is_hidden' => true,  'order_index' => 3],
            ['input' => "1 0",                                   'expected_output' => '0',            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Dijkstra ─────────────────────────────────────────────────
        $seed(26, [
            ['input' => "4 5\n0 1 1\n0 2 4\n1 2 2\n1 3 5\n2 3 1\n0",  'expected_output' => "0: 0\n1: 1\n2: 3\n3: 4",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 1\n0 1 7\n0",                                'expected_output' => "0: 0\n1: 7",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2\n0 1 2\n1 2 3\n0",                        'expected_output' => "0: 0\n1: 2\n2: 5",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 1\n0 1 10\n0",                               'expected_output' => "0: 0\n1: 10\n2: inf",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Connected components ─────────────────────────────────────
        $seed(27, [
            ['input' => "5 3\n0 1\n1 2\n3 4",   'expected_output' => '2',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 0",                    'expected_output' => '3',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 4\n0 1\n1 2\n2 3\n3 0", 'expected_output' => '1','is_hidden' => true,  'order_index' => 3],
            ['input' => "1 0",                    'expected_output' => '1',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Merge sort ───────────────────────────────────────────────
        $seed(28, [
            ['input' => "6\n5\n2\n4\n6\n1\n3",    'expected_output' => '[1, 2, 3, 4, 5, 6]',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3\n1\n2",             'expected_output' => '[1, 2, 3]',             'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n7",                    'expected_output' => '[7]',                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4\n4\n2\n2",          'expected_output' => '[2, 2, 4, 4]',          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Quick sort ───────────────────────────────────────────────
        $seed(29, [
            ['input' => "5\n3\n6\n8\n10\n1",    'expected_output' => '[1, 3, 6, 8, 10]',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n9\n5\n7",           'expected_output' => '[5, 7, 9]',         'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n2\n1",              'expected_output' => '[1, 2]',            'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4\n3\n2\n1",        'expected_output' => '[1, 2, 3, 4]',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Heap sort ────────────────────────────────────────────────
        $seed(30, [
            ['input' => "5\n4\n1\n3\n9\n7",    'expected_output' => '[1, 3, 4, 7, 9]',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5",          'expected_output' => '[5, 5, 5]',          'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0",                 'expected_output' => '[0]',                'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n9\n8\n7",      'expected_output' => '[7, 8, 9, 10]',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Counting sort ────────────────────────────────────────────
        $seed(31, [
            ['input' => "6\n4\n2\n2\n8\n3\n3",   'expected_output' => '[2, 2, 3, 3, 4, 8]',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n0\n2",            'expected_output' => '[0, 1, 2]',           'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n5\n5\n5\n5",         'expected_output' => '[5, 5, 5, 5]',        'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0",                   'expected_output' => '[0]',                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Email regex ──────────────────────────────────────────────
        $seed(32, [
            ['input' => 'Contact us at info@example.com or support@help.org for help.',  'expected_output' => "info@example.com\nsupport@help.org",  'is_hidden' => false, 'order_index' => 1],
            ['input' => 'No emails here!',                                                'expected_output' => '',                                    'is_hidden' => false, 'order_index' => 2],
            ['input' => 'a@b.c and x@y.z',                                               'expected_output' => "a@b.c\nx@y.z",                        'is_hidden' => true,  'order_index' => 3],
            ['input' => 'admin@mysite.io',                                                'expected_output' => 'admin@mysite.io',                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Whitespace normalisation ─────────────────────────────────
        $seed(33, [
            ['input' => 'Hello    World   Python',   'expected_output' => 'Hello World Python',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '  leading and trailing  ',  'expected_output' => 'leading and trailing','is_hidden' => false, 'order_index' => 2],
            ['input' => 'one  two  three',            'expected_output' => 'one two three',       'is_hidden' => true,  'order_index' => 3],
            ['input' => 'no_extra_spaces',            'expected_output' => 'no_extra_spaces',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: IPv4 validator ───────────────────────────────────────────
        $seed(34, [
            ['input' => '192.168.1.1',    'expected_output' => 'Valid',    'is_hidden' => false, 'order_index' => 1],
            ['input' => '256.100.0.1',    'expected_output' => 'Invalid',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.0.0.0',        'expected_output' => 'Valid',    'is_hidden' => true,  'order_index' => 3],
            ['input' => '01.02.03.04',    'expected_output' => 'Invalid',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: reduce product ───────────────────────────────────────────
        $seed(35, [
            ['input' => "4\n2\n3\n4\n5",   'expected_output' => '120',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n7",            'expected_output' => '7',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n10\n10\n10",   'expected_output' => '1000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n0\n100",       'expected_output' => '0',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: partial double ───────────────────────────────────────────
        $seed(36, [
            ['input' => "3\n5\n10\n3",    'expected_output' => "10\n20\n6",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0",            'expected_output' => "0",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n7\n8",         'expected_output' => "14\n16",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4",   'expected_output' => "2\n4\n6\n8",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: function pipeline ────────────────────────────────────────
        $seed(37, [
            ['input' => '3',   'expected_output' => '256',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',   'expected_output' => '100',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',   'expected_output' => '400',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '-5',  'expected_output' => '0',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Identity matrix comprehension ───────────────────────────
        $seed(38, [
            ['input' => '3',  'expected_output' => "1 0 0\n0 1 0\n0 0 1",                          'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',  'expected_output' => "1 0\n0 1",                                     'is_hidden' => false, 'order_index' => 2],
            ['input' => '4',  'expected_output' => "1 0 0 0\n0 1 0 0\n0 0 1 0\n0 0 0 1",          'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',  'expected_output' => "1",                                             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Dict invert comprehension ───────────────────────────────
        $seed(39, [
            ['input' => "3\na 1\nb 2\nc 3",   'expected_output' => "{'1': 'a', '2': 'b', '3': 'c'}",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\nx y",             'expected_output' => "{'y': 'x'}",                        'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nhello world\nfoo bar", 'expected_output' => "{'bar': 'foo', 'world': 'hello'}",  'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n1 a\n2 b",        'expected_output' => "{'a': '1', 'b': '2'}",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Pythagorean triples ──────────────────────────────────────
        $seed(40, [
            ['input' => '20',  'expected_output' => "(3, 4, 5)\n(5, 12, 13)\n(6, 8, 10)\n(8, 15, 17)\n(9, 12, 15)\n(12, 16, 20)",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '5',   'expected_output' => "(3, 4, 5)",                                                                    'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',  'expected_output' => "(3, 4, 5)\n(6, 8, 10)",                                                        'is_hidden' => true,  'order_index' => 3],
            ['input' => '2',   'expected_output' => '',                                                                             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Anagram groups ───────────────────────────────────────────
        $seed(41, [
            ['input' => 'eat tea tan ate nat bat',  'expected_output' => "['ate', 'eat', 'tea']\n['bat']\n['nat', 'tan']",  'is_hidden' => false, 'order_index' => 1],
            ['input' => 'abc cba bca',              'expected_output' => "['abc', 'bca', 'cba']",                          'is_hidden' => false, 'order_index' => 2],
            ['input' => 'dog',                       'expected_output' => "['dog']",                                       'is_hidden' => true,  'order_index' => 3],
            ['input' => 'abc def',                   'expected_output' => "['abc']\n['def']",                              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Balanced parentheses ─────────────────────────────────────
        $seed(42, [
            ['input' => '{[()]}',   'expected_output' => 'True',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '([)]',     'expected_output' => 'False',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '{}[]',     'expected_output' => 'True',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '(((',      'expected_output' => 'False',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Roman numerals ───────────────────────────────────────────
        $seed(43, [
            ['input' => '1994',  'expected_output' => 'MCMXCIV',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '58',    'expected_output' => 'LVIII',     'is_hidden' => false, 'order_index' => 2],
            ['input' => '3',     'expected_output' => 'III',       'is_hidden' => true,  'order_index' => 3],
            ['input' => '400',   'expected_output' => 'CD',        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Spiral matrix ────────────────────────────────────────────
        $seed(44, [
            ['input' => '3',  'expected_output' => "1 2 3\n8 9 4\n7 6 5",                                          'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',  'expected_output' => "1",                                                             'is_hidden' => false, 'order_index' => 2],
            ['input' => '2',  'expected_output' => "1 2\n4 3",                                                      'is_hidden' => true,  'order_index' => 3],
            ['input' => '4',  'expected_output' => "1 2 3 4\n12 13 14 5\n11 16 15 6\n10 9 8 7",                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Run-length encoding ──────────────────────────────────────
        $seed(45, [
            ['input' => 'AABBBCCCC',  'expected_output' => 'A2B3C4',      'is_hidden' => false, 'order_index' => 1],
            ['input' => 'ABCD',       'expected_output' => 'A1B1C1D1',    'is_hidden' => false, 'order_index' => 2],
            ['input' => 'AAAA',       'expected_output' => 'A4',          'is_hidden' => true,  'order_index' => 3],
            ['input' => 'AABB',       'expected_output' => 'A2B2',        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Sudoku validator ─────────────────────────────────────────
        $seed(46, [
            [
                'input' => "5 3 0 0 7 0 0 0 0\n6 0 0 1 9 5 0 0 0\n0 9 8 0 0 0 0 6 0\n8 0 0 0 6 0 0 0 3\n4 0 0 8 0 3 0 0 1\n7 0 0 0 2 0 0 0 6\n0 6 0 0 0 0 2 8 0\n0 0 0 4 1 9 0 0 5\n0 0 0 0 8 0 0 7 9",
                'expected_output' => 'Valid',
                'is_hidden' => false, 'order_index' => 1,
            ],
            [
                'input' => "1 1 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0",
                'expected_output' => 'Invalid',
                'is_hidden' => false, 'order_index' => 2,
            ],
            [
                'input' => "0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0\n0 0 0 0 0 0 0 0 0",
                'expected_output' => 'Valid',
                'is_hidden' => true, 'order_index' => 3,
            ],
            [
                'input' => "5 3 0 0 7 0 0 0 0\n6 0 0 1 9 5 0 0 0\n0 9 8 0 0 0 0 6 0\n8 0 0 0 6 0 0 0 3\n4 0 0 8 0 3 0 0 1\n7 0 0 0 2 0 0 0 6\n0 6 0 0 0 0 2 8 0\n0 0 0 4 1 9 0 0 5\n0 0 0 0 8 0 7 7 9",
                'expected_output' => 'Invalid',
                'is_hidden' => true, 'order_index' => 4,
            ],
        ]);

        // ── Q47: Word ladder ──────────────────────────────────────────────
        $seed(47, [
            ['input' => "hit\ncog\nhot dot dog lot log cog",    'expected_output' => '5',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "hit\ncog\nhot",                         'expected_output' => '0',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "a\nc\na b c",                           'expected_output' => '2',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "abc\nabc\nabc",                         'expected_output' => '1',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: N-Queens ─────────────────────────────────────────────────
        $seed(48, [
            ['input' => '6',  'expected_output' => '4',    'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',  'expected_output' => '2',    'is_hidden' => false, 'order_index' => 2],
            ['input' => '1',  'expected_output' => '1',    'is_hidden' => true,  'order_index' => 3],
            ['input' => '8',  'expected_output' => '92',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Trie ─────────────────────────────────────────────────────
        $seed(49, [
            ['input' => "5\ninsert apple\nsearch apple\nsearch app\nstarts_with app\nsearch orange",  'expected_output' => "True\nFalse\nTrue\nFalse",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\ninsert cat\nsearch cat\nsearch ca",                                        'expected_output' => "True\nFalse",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\ninsert a\nstarts_with a\nstarts_with b\nsearch a",                        'expected_output' => "True\nFalse\nTrue",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nsearch x\nstarts_with x",                                                  'expected_output' => "False\nFalse",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: LRU Cache ────────────────────────────────────────────────
        $seed(50, [
            ['input' => "2\n7\nput 1 1\nput 2 2\nget 1\nput 3 3\nget 2\nput 4 4\nget 1",  'expected_output' => "1\n-1\n1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n4\nput 1 1\nput 2 2\nget 1\nget 2",                            'expected_output' => "-1\n2",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n3\nput 1 10\nput 2 20\nget 1",                                  'expected_output' => "10",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n2\nget 1\nput 1 5",                                             'expected_output' => "-1",       'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 1 Coding (Advanced) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}