<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module7ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 7 — Algorithms & Data Structures for Data Scientists (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Algorithms & Data Structures for Data Scientists',
            'description'           => 'Go beyond definitions — evaluate algorithm behaviour, trace through operations, and compare data structure trade-offs. Questions require analytical thinking about time complexity, traversal logic, and structural properties.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 1000,
            'order_index'           => 7,
        ]);

        $this->command->info("Seeding 50 university-level questions on Algorithms & Data Structures...");

        $qaData = [

            // ── 7.1 BIG-O NOTATION & COMPLEXITY ANALYSIS ─────────────────
            [
                'q' => 'What is the time complexity of the following loop?\n\nfor i in range(n):\n    for j in range(n):\n        print(i, j)',
                'opts' => [
                    ['O(n)', false],
                    ['O(n log n)', false],
                    ['O(n²)', true],
                    ['O(2n)', false],
                ],
            ],
            [
                'q' => 'Given f(n) = 5n² + 3n + 10, what is the Big-O classification?',
                'opts' => [
                    ['O(n)', false],
                    ['O(n²)', true],
                    ['O(5n²)', false],
                    ['O(n³)', false],
                ],
            ],
            [
                'q' => 'An algorithm halves the problem size at each step (like binary search). What is its time complexity?',
                'opts' => [
                    ['O(n)', false],
                    ['O(n²)', false],
                    ['O(log n)', true],
                    ['O(1)', false],
                ],
            ],
            [
                'q' => 'Which complexity correctly describes an algorithm that runs in O(n log n)?',
                'opts' => [
                    ['Slower than O(n²) for large n', false],
                    ['Faster than O(n) for all n', false],
                    ['Faster than O(n²) but slower than O(n) for large n', true],
                    ['Equivalent to O(n) asymptotically', false],
                ],
            ],
            [
                'q' => 'What is the space complexity of storing n items in a Python list?',
                'opts' => [
                    ['O(1)', false],
                    ['O(n)', true],
                    ['O(log n)', false],
                    ['O(n²)', false],
                ],
            ],

            // ── 7.2 ARRAYS & DYNAMIC ARRAYS ──────────────────────────────
            [
                'q' => 'What is the time complexity of inserting an element at the BEGINNING of a Python list of size n?',
                'opts' => [
                    ['O(1)', false],
                    ['O(log n)', false],
                    ['O(n) — all existing elements must be shifted right', true],
                    ['O(n²)', false],
                ],
            ],
            [
                'q' => 'What is the amortized time complexity of appending (`.append()`) to a Python list?',
                'opts' => [
                    ['O(n) always', false],
                    ['O(1) amortized — occasional resizes are spread across many appends', true],
                    ['O(log n)', false],
                    ['O(n²)', false],
                ],
            ],
            [
                'q' => 'What does `arr[1:4]` return for `arr = [10, 20, 30, 40, 50]`?',
                'opts' => [
                    ['[10, 20, 30]', false],
                    ['[20, 30, 40]', true],
                    ['[20, 30, 40, 50]', false],
                    ['[10, 20, 30, 40]', false],
                ],
            ],
            [
                'q' => 'Which operation on a Python list has O(n) time complexity?',
                'opts' => [
                    ['Accessing arr[i] by index', false],
                    ['Appending to the end with .append()', false],
                    ['Checking if a value exists with `in`', true],
                    ['Getting the length with len()', false],
                ],
            ],
            [
                'q' => 'A NumPy array differs from a Python list primarily because:',
                'opts' => [
                    ['NumPy arrays can store strings only', false],
                    ['NumPy arrays are stored contiguously in memory with a fixed type, enabling vectorised operations', true],
                    ['NumPy arrays are slower for all operations', false],
                    ['NumPy arrays cannot be sliced', false],
                ],
            ],

            // ── 7.3 STACKS ────────────────────────────────────────────────
            [
                'q' => 'After these operations on an empty stack — push(1), push(2), push(3), pop() — what is on top of the stack?',
                'opts' => [
                    ['1', false],
                    ['3', false],
                    ['2', true],
                    ['The stack is empty', false],
                ],
            ],
            [
                'q' => 'What is the time complexity of both push and pop operations on a stack implemented with a Python list?',
                'opts' => [
                    ['O(n)', false],
                    ['O(log n)', false],
                    ['O(1)', true],
                    ['O(n²)', false],
                ],
            ],
            [
                'q' => 'A stack is used to check if parentheses in a string are balanced. Which step is correct?',
                'opts' => [
                    ['Push closing brackets; pop when an opening bracket is seen', false],
                    ['Push opening brackets; pop and match when a closing bracket is seen', true],
                    ['Push all characters; the stack will be empty only if balanced', false],
                    ['Sort the string and compare with the reversed version', false],
                ],
            ],
            [
                'q' => 'Which Python list method is used to simulate a stack pop operation?',
                'opts' => [
                    ['.remove()', false],
                    ['.pop()', true],
                    ['.dequeue()', false],
                    ['.shift()', false],
                ],
            ],

            // ── 7.4 QUEUES & PRIORITY QUEUES ─────────────────────────────
            [
                'q' => 'After these operations — enqueue(A), enqueue(B), enqueue(C), dequeue() — what is at the front of the queue?',
                'opts' => [
                    ['A', false],
                    ['C', false],
                    ['B', true],
                    ['The queue is empty', false],
                ],
            ],
            [
                'q' => 'Why is `collections.deque` preferred over a plain list for implementing a queue in Python?',
                'opts' => [
                    ['deque uses less memory', false],
                    ['deque supports O(1) appends and popleft from both ends, while list.pop(0) is O(n)', true],
                    ['deque is sorted automatically', false],
                    ['deque supports key-value pairs', false],
                ],
            ],
            [
                'q' => 'In a Min-Priority Queue backed by a min-heap, extracting the minimum element has what time complexity?',
                'opts' => [
                    ['O(1)', false],
                    ['O(log n)', true],
                    ['O(n)', false],
                    ['O(n log n)', false],
                ],
            ],
            [
                'q' => 'A task scheduler processes jobs in order of urgency (highest urgency first). Which data structure is best?',
                'opts' => [
                    ['A plain queue (FIFO)', false],
                    ['A stack (LIFO)', false],
                    ['A max-priority queue / max-heap', true],
                    ['A sorted linked list rebuilt each time', false],
                ],
            ],

            // ── 7.5 LINKED LISTS ──────────────────────────────────────────
            [
                'q' => 'What is the time complexity of accessing the k-th element of a singly linked list?',
                'opts' => [
                    ['O(1)', false],
                    ['O(k) — you must traverse from the head', true],
                    ['O(log n)', false],
                    ['O(n²)', false],
                ],
            ],
            [
                'q' => 'What is the time complexity of inserting a new node at the HEAD of a singly linked list (given a pointer to the head)?',
                'opts' => [
                    ['O(n)', false],
                    ['O(log n)', false],
                    ['O(1)', true],
                    ['O(n²)', false],
                ],
            ],
            [
                'q' => 'Given a linked list: Head → 1 → 2 → 3 → None. After deleting node with value 2, the list becomes:',
                'opts' => [
                    ['Head → 1 → None', false],
                    ['Head → 2 → 3 → None', false],
                    ['Head → 1 → 3 → None', true],
                    ['Head → 3 → 2 → 1 → None', false],
                ],
            ],
            [
                'q' => 'In which situation is a linked list BETTER than a dynamic array?',
                'opts' => [
                    ['When you need fast random access by index', false],
                    ['When you need to frequently insert or delete elements at the beginning', true],
                    ['When you need to sort data quickly', false],
                    ['When you need cache-friendly sequential reads', false],
                ],
            ],

            // ── 7.6 HASH TABLES ───────────────────────────────────────────
            [
                'q' => 'What is a "hash collision" in a hash table?',
                'opts' => [
                    ['When two keys are identical', false],
                    ['When two different keys produce the same hash index', true],
                    ['When the hash table runs out of memory', false],
                    ['When a value is deleted from the table', false],
                ],
            ],
            [
                'q' => 'What is the worst-case time complexity of a lookup in a hash table when many collisions occur?',
                'opts' => [
                    ['O(1)', false],
                    ['O(log n)', false],
                    ['O(n)', true],
                    ['O(n²)', false],
                ],
            ],
            [
                'q' => 'Which Python data structure provides O(1) average-case membership testing (`in` operator)?',
                'opts' => [
                    ['list', false],
                    ['tuple', false],
                    ['set', true],
                    ['deque', false],
                ],
            ],
            [
                'q' => 'What does `d.get("key", "default")` return if "key" is NOT in dictionary `d`?',
                'opts' => [
                    ['None', false],
                    ['An error (KeyError)', false],
                    ['"default"', true],
                    ['0', false],
                ],
            ],

            // ── 7.7 TREES: BINARY TREES & BSTs ───────────────────────────
            [
                'q' => 'What is the height of a binary tree with only the root node?',
                'opts' => [
                    ['0', true],
                    ['1', false],
                    ['-1', false],
                    ['Undefined', false],
                ],
            ],
            [
                'q' => 'In a balanced Binary Search Tree with n nodes, the average search time is:',
                'opts' => [
                    ['O(n)', false],
                    ['O(log n)', true],
                    ['O(1)', false],
                    ['O(n²)', false],
                ],
            ],
            [
                'q' => 'What is the result of an in-order traversal of a BST?',
                'opts' => [
                    ['Nodes in the order they were inserted', false],
                    ['Nodes in descending order', false],
                    ['Nodes in ascending (sorted) order', true],
                    ['Only the leaf nodes', false],
                ],
            ],
            [
                'q' => 'A BST degenerates into a linked list when:',
                'opts' => [
                    ['All values are the same', false],
                    ['Elements are inserted in sorted order, making every node have only a right child', true],
                    ['The tree has more than 100 nodes', false],
                    ['Leaf nodes are deleted first', false],
                ],
            ],

            // ── 7.8 HEAPS & PRIORITY QUEUES ──────────────────────────────
            [
                'q' => 'What is the time complexity of inserting an element into a heap of size n?',
                'opts' => [
                    ['O(1)', false],
                    ['O(log n)', true],
                    ['O(n)', false],
                    ['O(n log n)', false],
                ],
            ],
            [
                'q' => 'In Python\'s `heapq` module, `heapq.heappush(heap, item)` maintains a:',
                'opts' => [
                    ['Max-heap', false],
                    ['Sorted list', false],
                    ['Min-heap', true],
                    ['Balanced BST', false],
                ],
            ],
            [
                'q' => 'What does `heapq.heappop(heap)` return?',
                'opts' => [
                    ['The last inserted element', false],
                    ['A random element', false],
                    ['The smallest element (from the min-heap)', true],
                    ['The largest element', false],
                ],
            ],
            [
                'q' => 'The `heapq.nlargest(k, data)` function returns the k largest elements in what time complexity?',
                'opts' => [
                    ['O(k)', false],
                    ['O(n)', false],
                    ['O(n log k)', true],
                    ['O(n²)', false],
                ],
            ],

            // ── 7.9 GRAPHS: BFS & DFS ─────────────────────────────────────
            [
                'q' => 'Which data structure does DFS (Depth-First Search) use internally?',
                'opts' => [
                    ['A queue', false],
                    ['A hash table', false],
                    ['A stack (or recursion call stack)', true],
                    ['A priority queue', false],
                ],
            ],
            [
                'q' => 'BFS is guaranteed to find the shortest path in which type of graph?',
                'opts' => [
                    ['A weighted directed graph', false],
                    ['An unweighted graph (all edges have equal cost)', true],
                    ['A graph with negative edge weights', false],
                    ['A tree with more than 3 levels', false],
                ],
            ],
            [
                'q' => 'What is an "adjacency list" representation of a graph?',
                'opts' => [
                    ['A 2D matrix showing which nodes are connected', false],
                    ['A list where each node stores a list of its neighbouring nodes', true],
                    ['A sorted list of all edges in the graph', false],
                    ['A heap of all node weights', false],
                ],
            ],
            [
                'q' => 'For a sparse graph (few edges), which representation is more memory-efficient?',
                'opts' => [
                    ['Adjacency matrix', false],
                    ['Adjacency list', true],
                    ['Edge matrix', false],
                    ['Both use the same memory', false],
                ],
            ],

            // ── 7.10 SORTING & SEARCHING ──────────────────────────────────
            [
                'q' => 'What is the average-case time complexity of Quick Sort?',
                'opts' => [
                    ['O(n)', false],
                    ['O(n²)', false],
                    ['O(n log n)', true],
                    ['O(log n)', false],
                ],
            ],
            [
                'q' => 'What is the worst-case time complexity of Quick Sort?',
                'opts' => [
                    ['O(n log n)', false],
                    ['O(n²) — when the pivot is always the smallest or largest element', true],
                    ['O(n)', false],
                    ['O(log n)', false],
                ],
            ],
            [
                'q' => 'Merge Sort splits a list in half repeatedly. What is its time complexity?',
                'opts' => [
                    ['O(n)', false],
                    ['O(n²)', false],
                    ['O(n log n) in all cases', true],
                    ['O(log n)', false],
                ],
            ],
            [
                'q' => 'Linear Search on an unsorted list of n items has what average-case time complexity?',
                'opts' => [
                    ['O(1)', false],
                    ['O(log n)', false],
                    ['O(n)', true],
                    ['O(n²)', false],
                ],
            ],
            [
                'q' => 'After one pass of Bubble Sort on [5, 3, 1, 4, 2], which element is guaranteed to be in its final correct position?',
                'opts' => [
                    ['The smallest element (1)', false],
                    ['The largest element (5)', true],
                    ['The middle element (3)', false],
                    ['No element is guaranteed after one pass', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 7 — Algorithms & Data Structures (University Student).");
    }
}