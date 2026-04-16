<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module7ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 7 — Algorithms & Data Structures for Data Scientists (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Algorithms & Data Structures for Data Scientists',
            'description'           => 'Test your basic knowledge of algorithms and data structures — the building blocks every data scientist needs. No prior computer science degree required. We start from the very fundamentals!',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 7,
        ]);

        $this->command->info("Seeding 50 newbie-friendly questions on Algorithms & Data Structures...");

        $qaData = [

            // ── 7.1 BIG-O NOTATION & COMPLEXITY ANALYSIS ─────────────────
            [
                'q' => 'What does Big-O notation describe in computer science?',
                'opts' => [
                    ['The exact time a program takes to run in seconds', false],
                    ['How the running time or memory of an algorithm grows as the input size grows', true],
                    ['The programming language an algorithm is written in', false],
                    ['The number of lines of code in a program', false],
                ],
            ],
            [
                'q' => 'Which Big-O complexity is considered the FASTEST (best)?',
                'opts' => [
                    ['O(n)', false],
                    ['O(n²)', false],
                    ['O(1)', true],
                    ['O(log n)', false],
                ],
            ],
            [
                'q' => 'An algorithm that runs in O(1) time means:',
                'opts' => [
                    ['It runs in exactly 1 second', false],
                    ['It takes the same amount of time no matter how large the input is', true],
                    ['It only works on inputs of size 1', false],
                    ['It loops once through the data', false],
                ],
            ],
            [
                'q' => 'An algorithm that checks every element in a list of n items has a time complexity of:',
                'opts' => [
                    ['O(1)', false],
                    ['O(log n)', false],
                    ['O(n)', true],
                    ['O(n²)', false],
                ],
            ],
            [
                'q' => 'Which of the following Big-O complexities is SLOWEST for large inputs?',
                'opts' => [
                    ['O(n)', false],
                    ['O(log n)', false],
                    ['O(1)', false],
                    ['O(n²)', true],
                ],
            ],

            // ── 7.2 ARRAYS & DYNAMIC ARRAYS ──────────────────────────────
            [
                'q' => 'What is an array?',
                'opts' => [
                    ['A collection of items stored in random locations in memory', false],
                    ['An ordered collection of items stored in a fixed sequence in memory', true],
                    ['A collection of key-value pairs', false],
                    ['A structure that only stores text', false],
                ],
            ],
            [
                'q' => 'In Python, which built-in type acts as a dynamic array (automatically resizes)?',
                'opts' => [
                    ['tuple', false],
                    ['set', false],
                    ['list', true],
                    ['dict', false],
                ],
            ],
            [
                'q' => 'What is the index of the FIRST element in a Python list?',
                'opts' => [
                    ['1', false],
                    ['0', true],
                    ['-1', false],
                    ['It depends on the list', false],
                ],
            ],
            [
                'q' => 'What does `arr[-1]` return in Python?',
                'opts' => [
                    ['An error', false],
                    ['The first element', false],
                    ['The last element', true],
                    ['The length of the array', false],
                ],
            ],
            [
                'q' => 'Accessing an element by its index in an array is how fast?',
                'opts' => [
                    ['O(n) — it must search through the whole array', false],
                    ['O(1) — it jumps directly to that position', true],
                    ['O(log n) — it uses binary search', false],
                    ['O(n²) — it uses nested loops', false],
                ],
            ],

            // ── 7.3 STACKS: LIFO & APPLICATIONS ──────────────────────────
            [
                'q' => 'What does LIFO stand for?',
                'opts' => [
                    ['Last In, First Out', true],
                    ['Last In, First Over', false],
                    ['Linear Input, First Output', false],
                    ['List In, First Out', false],
                ],
            ],
            [
                'q' => 'A stack works like a stack of plates. If you add a plate on top, which plate do you take off first?',
                'opts' => [
                    ['The plate on the bottom', false],
                    ['The plate you just placed on top', true],
                    ['A random plate from the middle', false],
                    ['All plates come off at once', false],
                ],
            ],
            [
                'q' => 'What is the name of the operation that adds an item to a stack?',
                'opts' => [
                    ['enqueue', false],
                    ['insert', false],
                    ['push', true],
                    ['append', false],
                ],
            ],
            [
                'q' => 'What is the name of the operation that removes the top item from a stack?',
                'opts' => [
                    ['pop', true],
                    ['dequeue', false],
                    ['delete', false],
                    ['shift', false],
                ],
            ],
            [
                'q' => 'Which real-world scenario best represents a stack?',
                'opts' => [
                    ['People standing in a checkout line at a grocery store', false],
                    ['A browser\'s "Back" button history — the last page visited is the first to go back to', true],
                    ['A playlist that plays songs in the order they were added', false],
                    ['A dictionary sorted alphabetically', false],
                ],
            ],

            // ── 7.4 QUEUES, DEQUES & PRIORITY QUEUES ─────────────────────
            [
                'q' => 'What does FIFO stand for?',
                'opts' => [
                    ['First In, First Out', true],
                    ['Fast Input, Fast Output', false],
                    ['First Index, First Output', false],
                    ['Forward In, Forward Out', false],
                ],
            ],
            [
                'q' => 'A queue works like a line at a coffee shop. Who gets served first?',
                'opts' => [
                    ['The last person who joined the line', false],
                    ['A randomly chosen person', false],
                    ['The first person who joined the line', true],
                    ['The person who ordered the most items', false],
                ],
            ],
            [
                'q' => 'What is the operation called when you ADD an item to a queue?',
                'opts' => [
                    ['push', false],
                    ['enqueue', true],
                    ['insert_front', false],
                    ['stack', false],
                ],
            ],
            [
                'q' => 'What is the operation called when you REMOVE an item from the front of a queue?',
                'opts' => [
                    ['pop', false],
                    ['delete', false],
                    ['dequeue', true],
                    ['shift_out', false],
                ],
            ],
            [
                'q' => 'In a Priority Queue, which item is removed first?',
                'opts' => [
                    ['The item added first (FIFO order)', false],
                    ['The item added last (LIFO order)', false],
                    ['The item with the highest priority', true],
                    ['A randomly selected item', false],
                ],
            ],

            // ── 7.5 LINKED LISTS ──────────────────────────────────────────
            [
                'q' => 'What is a linked list?',
                'opts' => [
                    ['A list that is linked to a database', false],
                    ['A sequence of nodes where each node contains data and a pointer to the next node', true],
                    ['A sorted array of numbers', false],
                    ['A two-dimensional grid of values', false],
                ],
            ],
            [
                'q' => 'In a singly linked list, each node points to:',
                'opts' => [
                    ['The previous node only', false],
                    ['Both the next and previous nodes', false],
                    ['The next node only', true],
                    ['The first node only', false],
                ],
            ],
            [
                'q' => 'What is the name of the first node in a linked list?',
                'opts' => [
                    ['Tail', false],
                    ['Root', false],
                    ['Head', true],
                    ['Start', false],
                ],
            ],
            [
                'q' => 'In a doubly linked list, each node points to:',
                'opts' => [
                    ['Only the next node', false],
                    ['Both the next and the previous node', true],
                    ['Only the previous node', false],
                    ['The first and last nodes only', false],
                ],
            ],
            [
                'q' => 'What is the value of the pointer in the LAST node of a linked list?',
                'opts' => [
                    ['It points back to the first node', false],
                    ['It points to itself', false],
                    ['None / Null — indicating there is no next node', true],
                    ['It stores the length of the list', false],
                ],
            ],

            // ── 7.6 HASH TABLES / DICTIONARIES ───────────────────────────
            [
                'q' => 'What is a hash table (called a "dictionary" in Python)?',
                'opts' => [
                    ['A sorted list of items', false],
                    ['A data structure that maps keys to values for fast lookups', true],
                    ['A table with numbered rows and columns', false],
                    ['A structure that stores only numbers', false],
                ],
            ],
            [
                'q' => 'In Python, how do you look up the value associated with the key "name" in a dictionary `d`?',
                'opts' => [
                    ['d.lookup("name")', false],
                    ['d["name"]', true],
                    ['d.get_key("name")', false],
                    ['d{name}', false],
                ],
            ],
            [
                'q' => 'What is the average time complexity to look up a value in a hash table?',
                'opts' => [
                    ['O(n)', false],
                    ['O(log n)', false],
                    ['O(1)', true],
                    ['O(n²)', false],
                ],
            ],
            [
                'q' => 'What is a "key" in a Python dictionary?',
                'opts' => [
                    ['The index number of an item', false],
                    ['A unique identifier used to access its associated value', true],
                    ['The length of the dictionary', false],
                    ['A special type of number', false],
                ],
            ],
            [
                'q' => 'Which of the following is a valid Python dictionary?',
                'opts' => [
                    ['{"Alice", "Bob", "Charlie"}', false],
                    ['["Alice", 25, "Engineer"]', false],
                    ['{"name": "Alice", "age": 25}', true],
                    ['("Alice", 25)', false],
                ],
            ],

            // ── 7.7 TREES: BINARY TREES & BSTS ───────────────────────────
            [
                'q' => 'In a tree data structure, what is the "root"?',
                'opts' => [
                    ['The bottom-most node', false],
                    ['The topmost node with no parent', true],
                    ['Any node that has children', false],
                    ['The leftmost node', false],
                ],
            ],
            [
                'q' => 'What is a "leaf" node in a tree?',
                'opts' => [
                    ['The root node', false],
                    ['A node with exactly two children', false],
                    ['A node with no children', true],
                    ['The deepest node on the right side', false],
                ],
            ],
            [
                'q' => 'In a Binary Search Tree (BST), values smaller than the root are stored:',
                'opts' => [
                    ['To the right of the root', false],
                    ['To the left of the root', true],
                    ['Randomly on either side', false],
                    ['Below the root in a separate list', false],
                ],
            ],
            [
                'q' => 'In a Binary Search Tree (BST), values larger than the root are stored:',
                'opts' => [
                    ['To the left of the root', false],
                    ['To the right of the root', true],
                    ['In a separate array', false],
                    ['As the root itself', false],
                ],
            ],
            [
                'q' => 'What is the maximum number of children each node can have in a binary tree?',
                'opts' => [
                    ['1', false],
                    ['3', false],
                    ['2', true],
                    ['Unlimited', false],
                ],
            ],

            // ── 7.8 HEAPS & PRIORITY QUEUES ──────────────────────────────
            [
                'q' => 'What is a heap in computer science?',
                'opts' => [
                    ['A pile of unsorted data', false],
                    ['A special tree-based structure where the parent is always larger (or smaller) than its children', true],
                    ['A type of linked list', false],
                    ['A sorted array', false],
                ],
            ],
            [
                'q' => 'In a Min-Heap, the element at the top (root) is always:',
                'opts' => [
                    ['The largest element', false],
                    ['A randomly chosen element', false],
                    ['The smallest element', true],
                    ['The most recently added element', false],
                ],
            ],
            [
                'q' => 'In a Max-Heap, the element at the root is always:',
                'opts' => [
                    ['The smallest element', false],
                    ['The largest element', true],
                    ['The middle element', false],
                    ['The first element that was inserted', false],
                ],
            ],
            [
                'q' => 'Which Python module provides a ready-to-use heap (min-heap)?',
                'opts' => [
                    ['collections', false],
                    ['heapq', true],
                    ['queue', false],
                    ['sortlib', false],
                ],
            ],

            // ── 7.9 GRAPHS: BFS & DFS ─────────────────────────────────────
            [
                'q' => 'What is a graph in computer science?',
                'opts' => [
                    ['A chart like a bar chart or pie chart', false],
                    ['A collection of nodes (vertices) connected by edges', true],
                    ['A type of sorted array', false],
                    ['A two-dimensional table of numbers', false],
                ],
            ],
            [
                'q' => 'What does BFS stand for?',
                'opts' => [
                    ['Binary First Search', false],
                    ['Breadth-First Search', true],
                    ['Bottom-First Scan', false],
                    ['Backward Flow Search', false],
                ],
            ],
            [
                'q' => 'What does DFS stand for?',
                'opts' => [
                    ['Data Flow Search', false],
                    ['Depth-First Search', true],
                    ['Downward-First Scan', false],
                    ['Double-Forward Search', false],
                ],
            ],
            [
                'q' => 'BFS explores a graph by visiting:',
                'opts' => [
                    ['All neighbors of the current node before going deeper', true],
                    ['The deepest possible path first', false],
                    ['Nodes in random order', false],
                    ['Only nodes that are connected by two or more edges', false],
                ],
            ],
            [
                'q' => 'Which data structure does BFS use internally to keep track of nodes to visit?',
                'opts' => [
                    ['A stack', false],
                    ['A heap', false],
                    ['A queue', true],
                    ['A linked list', false],
                ],
            ],

            // ── 7.10 SORTING & SEARCHING ALGORITHMS ──────────────────────
            [
                'q' => 'What does a sorting algorithm do?',
                'opts' => [
                    ['Finds a specific item in a list', false],
                    ['Arranges items in a list in a specific order (e.g. ascending or descending)', true],
                    ['Removes duplicate items from a list', false],
                    ['Counts the number of items in a list', false],
                ],
            ],
            [
                'q' => 'Which of the following is the simplest sorting algorithm, though not the most efficient?',
                'opts' => [
                    ['Merge Sort', false],
                    ['Quick Sort', false],
                    ['Bubble Sort', true],
                    ['Heap Sort', false],
                ],
            ],
            [
                'q' => 'What does Binary Search require before it can be used?',
                'opts' => [
                    ['The list must have an even number of elements', false],
                    ['The list must be sorted', true],
                    ['The list must contain only integers', false],
                    ['The list must be stored in a hash table', false],
                ],
            ],
            [
                'q' => 'Binary Search has a time complexity of:',
                'opts' => [
                    ['O(n)', false],
                    ['O(n²)', false],
                    ['O(log n)', true],
                    ['O(1)', false],
                ],
            ],
            [
                'q' => 'What is the time complexity of Python\'s built-in `sorted()` function?',
                'opts' => [
                    ['O(n)', false],
                    ['O(n log n)', true],
                    ['O(n²)', false],
                    ['O(log n)', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 7 — Algorithms & Data Structures (Newbie).");
    }
}