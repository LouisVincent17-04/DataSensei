<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module7ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 7 — Algorithms & Data Structures for Data Scientists (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Algorithms & Data Structures for Data Scientists',
            'description'           => 'Apply your knowledge to multi-step tracing problems, code snippet analysis, and algorithm design decisions common in data science workflows — from feature lookup to graph traversal on real datasets.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 1500,
            'order_index'           => 7,
        ]);

        $this->command->info("Seeding 50 intermediate-level questions on Algorithms & Data Structures...");

        $qaData = [

            // ── 7.1 BIG-O NOTATION & COMPLEXITY ANALYSIS ─────────────────
            [
                'q' => "What is the time complexity of the following function?\n\ndef process(data):\n    result = []\n    for item in data:          # O(n)\n        for val in data:       # O(n)\n            result.append(item + val)\n    return sorted(result)      # O(n² log n²)",
                'opts' => [
                    ['O(n²)', false],
                    ['O(n² log n)', true],
                    ['O(n log n)', false],
                    ['O(n³)', false],
                ],
            ],
            [
                'q' => "Rank these complexities from FASTEST to SLOWEST for large n:\nO(n!), O(2^n), O(n²), O(n log n), O(log n)",
                'opts' => [
                    ['O(log n) < O(n log n) < O(n²) < O(2^n) < O(n!)', true],
                    ['O(n log n) < O(log n) < O(n²) < O(2^n) < O(n!)', false],
                    ['O(log n) < O(n²) < O(n log n) < O(2^n) < O(n!)', false],
                    ['O(n!) < O(2^n) < O(n²) < O(n log n) < O(log n)', false],
                ],
            ],
            [
                'q' => "A data pipeline processes each of n records, and for each record it performs a binary search over m pre-sorted reference entries. What is the overall time complexity?",
                'opts' => [
                    ['O(n + m)', false],
                    ['O(n · log m)', true],
                    ['O(n · m)', false],
                    ['O(log n · log m)', false],
                ],
            ],
            [
                'q' => "What is the time complexity of computing the mean of a pandas Series of length n?\n\nimport pandas as pd\nresult = series.mean()",
                'opts' => [
                    ['O(1)', false],
                    ['O(log n)', false],
                    ['O(n) — every element must be summed', true],
                    ['O(n²)', false],
                ],
            ],
            [
                'q' => "You have an algorithm that runs in O(n²) on 10,000 rows of data and takes 5 seconds. Approximately how long will it take on 100,000 rows?",
                'opts' => [
                    ['50 seconds', false],
                    ['500 seconds', true],
                    ['5000 seconds', false],
                    ['55 seconds', false],
                ],
            ],

            // ── 7.2 ARRAYS & DYNAMIC ARRAYS ──────────────────────────────
            [
                'q' => "What does the following NumPy operation produce?\n\nimport numpy as np\na = np.array([1, 2, 3, 4, 5])\nprint(a[a > 2])",
                'opts' => [
                    ['[1, 2]', false],
                    ['[3, 4, 5]', true],
                    ['[False, False, True, True, True]', false],
                    ['An error', false],
                ],
            ],
            [
                'q' => "What does the following code output?\n\na = [10, 20, 30, 40, 50]\nprint(a[::2])",
                'opts' => [
                    ['[20, 40]', false],
                    ['[10, 30, 50]', true],
                    ['[10, 20, 30]', false],
                    ['[50, 40, 30, 20, 10]', false],
                ],
            ],
            [
                'q' => "A data scientist stores 1 million floating-point values. Which is MORE memory-efficient?\n\nOption A: Python list of floats\nOption B: numpy.ndarray of float32",
                'opts' => [
                    ['Python list — it stores references efficiently', false],
                    ['numpy float32 array — 4 bytes per value vs ~28 bytes per Python float object', true],
                    ['Both use the same memory', false],
                    ['Python list — NumPy adds overhead per element', false],
                ],
            ],
            [
                'q' => "What does `np.zeros((3, 4))` create?",
                'opts' => [
                    ['A 1D array of 12 zeros', false],
                    ['A 3×4 matrix filled with zeros', true],
                    ['A 4×3 matrix filled with zeros', false],
                    ['An error — shape must be a single integer', false],
                ],
            ],

            // ── 7.3 STACKS ────────────────────────────────────────────────
            [
                'q' => "Trace through the following code and give the final output:\n\nstack = []\nfor ch in '([{}])':\n    if ch in '([{':\n        stack.append(ch)\n    else:\n        stack.pop()\nprint(len(stack))",
                'opts' => [
                    ['3', false],
                    ['0', true],
                    ['1', false],
                    ['Error — pop on empty stack', false],
                ],
            ],
            [
                'q' => "A call stack in Python grows with each function call and shrinks on return. This is modelled by which structure?\n\ndef f(n):\n    if n == 0: return 0\n    return f(n - 1) + 1\n\nf(4) generates how many stack frames?",
                'opts' => [
                    ['3', false],
                    ['4', false],
                    ['5', true],
                    ['2', false],
                ],
            ],
            [
                'q' => "Which of the following correctly implements a stack-based reversal of a list?\n\nOption A:\nstack = []\nfor item in lst: stack.append(item)\nresult = [stack.pop() for _ in range(len(stack))]\n\nOption B:\nresult = lst[::-1]",
                'opts' => [
                    ['Only Option A is correct', false],
                    ['Only Option B is correct', false],
                    ['Both produce a reversed list, but Option B is simpler', true],
                    ['Neither option reverses the list', false],
                ],
            ],

            // ── 7.4 QUEUES & PRIORITY QUEUES ─────────────────────────────
            [
                'q' => "What is the output of the following?\n\nfrom collections import deque\nq = deque()\nq.append('A')\nq.append('B')\nq.append('C')\nq.popleft()\nprint(q[0])",
                'opts' => [
                    ['"A"', false],
                    ['"C"', false],
                    ['"B"', true],
                    ['An error', false],
                ],
            ],
            [
                'q' => "In a BFS implementation over a social network graph (finding shortest path between two users), why is a queue used instead of a stack?",
                'opts' => [
                    ['Queues use less memory than stacks', false],
                    ['A queue ensures we explore all nodes at distance k before nodes at distance k+1, guaranteeing the shortest path in an unweighted graph', true],
                    ['Stacks cannot store node references', false],
                    ['Queues sort nodes by their degree automatically', false],
                ],
            ],
            [
                'q' => "Using `heapq` to simulate a max-heap in Python requires:\n\nimport heapq\nheap = []\nheapq.heappush(heap, ???)",
                'opts' => [
                    ['Reversing the list after each push', false],
                    ['Negating values before inserting: heapq.heappush(heap, -value)', true],
                    ['Using heapq.heappushmax(heap, value)', false],
                    ['Sorting the heap manually after each push', false],
                ],
            ],

            // ── 7.5 LINKED LISTS ──────────────────────────────────────────
            [
                'q' => "Given a singly linked list: 1 → 3 → 5 → 7 → None\nAfter inserting value 4 after node with value 3, the list becomes:",
                'opts' => [
                    ['1 → 3 → 4 → 5 → 7 → None', true],
                    ['1 → 4 → 3 → 5 → 7 → None', false],
                    ['1 → 3 → 5 → 4 → 7 → None', false],
                    ['4 → 1 → 3 → 5 → 7 → None', false],
                ],
            ],
            [
                'q' => "What is the standard algorithm to detect a cycle in a linked list?",
                'opts' => [
                    ['Sort the list and look for duplicates', false],
                    ['Floyd\'s Cycle Detection (two-pointer): use a slow pointer (1 step) and a fast pointer (2 steps) — if they meet, there is a cycle', true],
                    ['Count the total nodes; if count exceeds n, there is a cycle', false],
                    ['Hash every node\'s value; duplicates mean a cycle', false],
                ],
            ],
            [
                'q' => "Reversing a singly linked list of n nodes has what time complexity?",
                'opts' => [
                    ['O(1)', false],
                    ['O(log n)', false],
                    ['O(n)', true],
                    ['O(n²)', false],
                ],
            ],
            [
                'q' => "Why does pandas use a contiguous block of memory (like a NumPy array) rather than a linked list for its DataFrame columns?",
                'opts' => [
                    ['Linked lists support faster insertion at arbitrary positions', false],
                    ['Contiguous memory enables vectorised CPU instructions (SIMD), cache efficiency, and O(1) index access — critical for large datasets', true],
                    ['Linked lists cannot store floating-point numbers', false],
                    ['NumPy arrays use less code to implement', false],
                ],
            ],

            // ── 7.6 HASH TABLES ───────────────────────────────────────────
            [
                'q' => "What does the following code produce?\n\ncounts = {}\nwords = ['apple', 'banana', 'apple', 'cherry', 'banana', 'apple']\nfor w in words:\n    counts[w] = counts.get(w, 0) + 1\nprint(counts['apple'])",
                'opts' => [
                    ['1', false],
                    ['2', false],
                    ['3', true],
                    ['0', false],
                ],
            ],
            [
                'q' => "A data scientist needs to find duplicate patient IDs in a list of 1 million records as fast as possible. Which approach is optimal?",
                'opts' => [
                    ['Sort the list and compare adjacent elements — O(n log n)', false],
                    ['Use a set to track seen IDs; any ID already in the set is a duplicate — O(n) average', true],
                    ['Nested loops checking every pair — O(n²)', false],
                    ['Use a BST to insert each ID — O(n log n)', false],
                ],
            ],
            [
                'q' => "What does `collections.Counter(['a','b','a','c','a','b'])` produce?",
                'opts' => [
                    ["{'a': 1, 'b': 1, 'c': 1}", false],
                    ["Counter({'a': 3, 'b': 2, 'c': 1})", true],
                    ["['a', 'a', 'a', 'b', 'b', 'c']", false],
                    ["{'a', 'b', 'c'}", false],
                ],
            ],

            // ── 7.7 TREES: BINARY TREES & BSTs ───────────────────────────
            [
                'q' => "Trace an in-order traversal of this BST:\n\n        8\n       / \\\n      3   10\n     / \\    \\\n    1   6   14\n       / \\\n      4   7\n\nWhat is the output order?",
                'opts' => [
                    ['8, 3, 10, 1, 6, 14, 4, 7', false],
                    ['1, 3, 4, 6, 7, 8, 10, 14', true],
                    ['1, 6, 3, 14, 10, 8, 4, 7', false],
                    ['8, 10, 14, 3, 6, 7, 1, 4', false],
                ],
            ],
            [
                'q' => "What is the result of a pre-order traversal (Root → Left → Right) on the BST above?",
                'opts' => [
                    ['8, 3, 1, 6, 4, 7, 10, 14', true],
                    ['1, 3, 4, 6, 7, 8, 10, 14', false],
                    ['1, 4, 7, 6, 3, 14, 10, 8', false],
                    ['14, 10, 7, 4, 6, 1, 3, 8', false],
                ],
            ],
            [
                'q' => "Decision trees in scikit-learn are trained using recursive binary splitting. The internal nodes of a trained decision tree correspond to which operation in a BST?",
                'opts' => [
                    ['Balancing the tree to minimise height', false],
                    ['Comparing a feature value to a threshold to route a sample left or right — analogous to BST comparison', true],
                    ['Sorting training labels before splitting', false],
                    ['Hashing feature values into buckets', false],
                ],
            ],

            // ── 7.8 HEAPS & PRIORITY QUEUES ──────────────────────────────
            [
                'q' => "What does the following output?\n\nimport heapq\ndata = [5, 1, 8, 3, 2]\nheapq.heapify(data)\nprint(heapq.heappop(data))",
                'opts' => [
                    ['5', false],
                    ['8', false],
                    ['1', true],
                    ['3', false],
                ],
            ],
            [
                'q' => "A data scientist needs the top-10 most frequent words from a corpus of 10 million words. Which approach is most efficient?",
                'opts' => [
                    ['Sort all unique words by frequency — O(u log u) where u = unique words', false],
                    ['Use a min-heap of size 10: insert each word frequency; if the heap exceeds 10 items, pop the smallest — O(u log 10) = O(u)', true],
                    ['Use BFS on a word frequency graph', false],
                    ['Load all data into a BST sorted by frequency', false],
                ],
            ],
            [
                'q' => "What is the time complexity of `heapq.heapify(data)` for a list of n elements?",
                'opts' => [
                    ['O(n log n)', false],
                    ['O(n)', true],
                    ['O(log n)', false],
                    ['O(n²)', false],
                ],
            ],

            // ── 7.9 GRAPHS: BFS & DFS ─────────────────────────────────────
            [
                'q' => "Given this adjacency list:\n{'A': ['B','C'], 'B': ['D'], 'C': ['D','E'], 'D': [], 'E': []}\n\nBFS starting from 'A' visits nodes in what order?",
                'opts' => [
                    ['A, B, D, C, E', false],
                    ['A, B, C, D, E', true],
                    ['A, C, E, B, D', false],
                    ['A, D, B, E, C', false],
                ],
            ],
            [
                'q' => "DFS starting from 'A' on the same graph (visiting neighbours in list order) visits nodes in what order?",
                'opts' => [
                    ['A, B, C, D, E', false],
                    ['A, B, D, C, E', true],
                    ['A, C, D, E, B', false],
                    ['A, D, B, E, C', false],
                ],
            ],
            [
                'q' => "In a knowledge graph for a recommendation engine, each node is an item and edges indicate 'bought together'. Finding items within 2 hops of a given item is best done with:",
                'opts' => [
                    ['DFS up to depth 2', false],
                    ['BFS up to depth 2, using the queue to track distance level', true],
                    ['Dijkstra\'s algorithm with weight = 2', false],
                    ['Binary search on the adjacency list', false],
                ],
            ],
            [
                'q' => "What is a Directed Acyclic Graph (DAG) and where does it appear in data science?",
                'opts' => [
                    ['A tree with weights on edges, used in neural networks', false],
                    ['A graph with directed edges and no cycles — used in pipeline dependency graphs and Airflow DAGs', true],
                    ['A hash table of graph edges', false],
                    ['A min-heap organised as a graph', false],
                ],
            ],

            // ── 7.10 SORTING & SEARCHING ALGORITHMS ──────────────────────
            [
                'q' => "What does the following sort and what is its output?\n\ndata = [(3, 'c'), (1, 'a'), (2, 'b')]\ndata.sort(key=lambda x: x[0])\nprint(data)",
                'opts' => [
                    ["[(3, 'c'), (2, 'b'), (1, 'a')]", false],
                    ["[(1, 'a'), (2, 'b'), (3, 'c')]", true],
                    ["[('a', 1), ('b', 2), ('c', 3)]", false],
                    ["[(3, 'c'), (1, 'a'), (2, 'b')]", false],
                ],
            ],
            [
                'q' => "Binary search is applied to a sorted array of 1,024 elements. In the worst case, how many comparisons are needed?",
                'opts' => [
                    ['512', false],
                    ['10', true],
                    ['1024', false],
                    ['32', false],
                ],
            ],
            [
                'q' => "A data scientist receives a new batch of 100 records to add to a sorted list of 10,000 records. What is the most efficient strategy?",
                'opts' => [
                    ['Re-sort the entire 10,100-record list — O(n log n)', false],
                    ['Sort the 100 new records and merge with the existing sorted list — O(m log m + n) where m=100, n=10,000', true],
                    ['Use bubble sort on the combined list — O(n²)', false],
                    ['Insert each record one by one using binary search for position — O(m · n)', false],
                ],
            ],
            [
                'q' => "Merge Sort is preferred over Quick Sort for sorting linked lists because:",
                'opts' => [
                    ['Merge Sort is always faster', false],
                    ['Quick Sort requires random access to pick the pivot efficiently, which is O(n) for linked lists; Merge Sort only needs sequential access', true],
                    ['Merge Sort uses less memory on linked lists', false],
                    ['Quick Sort cannot sort linked lists at all', false],
                ],
            ],
            [
                'q' => "Which sorting algorithm is most efficient for nearly-sorted data with only a few elements out of place?",
                'opts' => [
                    ['Merge Sort', false],
                    ['Quick Sort', false],
                    ['Insertion Sort — O(n + k) where k is the number of inversions', true],
                    ['Heap Sort', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 7 — Algorithms & Data Structures (Intermediate).");
    }
}