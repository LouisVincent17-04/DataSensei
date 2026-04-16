<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module7ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 7 — Algorithms & Data Structures for Data Scientists (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Algorithms & Data Structures for Data Scientists',
            'description'           => 'Debug, optimise, and reason about complex algorithm implementations used in real data science systems. Questions involve code snippets with subtle bugs, performance trade-offs, and algorithm design for large-scale data problems.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 2000,
            'order_index'           => 7,
        ]);

        $this->command->info("Seeding 50 advanced-level questions on Algorithms & Data Structures...");

        $qaData = [

            // ── 7.1 BIG-O NOTATION & COMPLEXITY ANALYSIS ─────────────────
            [
                'q' => "Identify the tight Big-O bound for the following function:\n\ndef mystery(n):\n    count = 0\n    i = n\n    while i > 1:\n        i = i // 2\n        count += 1\n    return count",
                'opts' => [
                    ['O(n)', false],
                    ['O(√n)', false],
                    ['O(log n)', true],
                    ['O(n log n)', false],
                ],
            ],
            [
                'q' => "What is the time complexity of the following?\n\ndef has_duplicate(arr):\n    seen = set()\n    for x in arr:\n        if x in seen:\n            return True\n        seen.add(x)\n    return False\n\nCompare to sorting-based duplicate detection.",
                'opts' => [
                    ['O(n²) — set operations are slow', false],
                    ['O(n) average with O(n) space — faster than O(n log n) sort-based approach', true],
                    ['O(n log n) — same as sorting', false],
                    ['O(log n) — set uses binary search internally', false],
                ],
            ],
            [
                'q' => "A feature engineering pipeline runs the following steps on a dataset of n rows:\n1. Normalise each of k features: O(n·k)\n2. Pairwise correlation matrix: O(k²·n)\n3. Sort features by variance: O(k log k)\n\nWhat is the overall complexity in terms of n and k (assume k << n)?",
                'opts' => [
                    ['O(n·k)', false],
                    ['O(k²·n) — dominated by the pairwise correlation step', true],
                    ['O(n log n + k²)', false],
                    ['O(n² + k²)', false],
                ],
            ],
            [
                'q' => "What is wrong with the following complexity analysis?\n\n'This function is O(n) because the outer loop runs n times.'\n\ndef f(lst):\n    for i in range(len(lst)):\n        if lst[i] in lst:   # <-- this line\n            return True\n    return False",
                'opts' => [
                    ['The analysis is correct — the outer loop dominates', false],
                    ['The `in` operator on a list is O(n), making the total O(n²)', true],
                    ['The `in` operator is O(1) for lists', false],
                    ['The function always returns True so complexity does not matter', false],
                ],
            ],
            [
                'q' => "Amortised O(1) for list `.append()` breaks down when:\n\nA) Every append triggers a resize\nB) The list is initialised with a capacity of 0 and n appends are performed\nC) The list contains strings instead of integers\nD) The list is pre-allocated with `[None] * n`",
                'opts' => [
                    ['A — but this cannot happen in a doubling strategy', false],
                    ['D — pre-allocation makes every append O(n)', false],
                    ['B — amortised analysis still holds; individual appends may be O(n) but the average across n appends is O(1)', true],
                    ['C — strings change the complexity', false],
                ],
            ],

            // ── 7.2 ARRAYS & DYNAMIC ARRAYS ──────────────────────────────
            [
                'q' => "Find the bug in this sliding window maximum implementation:\n\ndef sliding_max(arr, k):\n    result = []\n    for i in range(len(arr) - k + 1):\n        result.append(max(arr[i:i+k]))  # Bug here?\n    return result\n\nWhat is the time complexity and what is the more efficient approach?",
                'opts' => [
                    ['O(n) — max on a slice is O(1)', false],
                    ['O(n·k) — max on each window slice is O(k); use a deque-based monotonic queue for O(n)', true],
                    ['O(n log n) — internally uses sorting', false],
                    ['O(k²) — nested slicing causes quadratic behaviour', false],
                ],
            ],
            [
                'q' => "What does the following NumPy code compute, and what is its complexity vs. a Python loop?\n\nimport numpy as np\nA = np.random.rand(1000, 1000)\nresult = A @ A.T",
                'opts' => [
                    ['Element-wise product — O(n²) same as a loop', false],
                    ['Matrix multiplication — O(n³) mathematically, but NumPy calls optimised BLAS routines making it far faster in practice than a Python loop', true],
                    ['Matrix transpose only — O(n²)', false],
                    ['Dot product of two vectors — O(n)', false],
                ],
            ],
            [
                'q' => "A data scientist uses the following to remove duplicates from a large list:\n\nunique = []\nfor item in data:\n    if item not in unique:  # <-- potential issue\n        unique.append(item)\n\nWhat is the problem and a better alternative?",
                'opts' => [
                    ['No problem — `not in` on a list is O(1)', false],
                    ['`not in` on a list is O(n), making the whole loop O(n²). Use `seen = set()` for O(n) total', true],
                    ['The list cannot store duplicate values', false],
                    ['`append` is O(n) making this O(n²)', false],
                ],
            ],
            [
                'q' => "What is the output?\n\nimport numpy as np\na = np.array([1, 2, 3])\nb = a\nb[0] = 99\nprint(a[0])",
                'opts' => [
                    ['1 — b is a copy of a', false],
                    ['99 — b is a view (reference) of a, so modifying b modifies a', true],
                    ['Error — NumPy arrays are immutable', false],
                    ['0 — indexing resets the value', false],
                ],
            ],

            // ── 7.3 STACKS ────────────────────────────────────────────────
            [
                'q' => "The following recursive DFS on a large graph causes a RecursionError. How do you fix it?\n\ndef dfs(graph, node, visited=set()):\n    visited.add(node)\n    for neighbor in graph[node]:\n        if neighbor not in visited:\n            dfs(graph, neighbor, visited)\n    return visited",
                'opts' => [
                    ['Increase Python\'s default recursion limit to infinity', false],
                    ['Convert to iterative DFS using an explicit stack: push unvisited neighbours onto a stack and loop until empty', true],
                    ['Use a queue instead of recursion', false],
                    ['Sort the adjacency list before traversal', false],
                ],
            ],
            [
                'q' => "The function `dfs(graph, node, visited=set())` has a subtle bug unrelated to recursion depth. What is it?",
                'opts' => [
                    ['`set()` cannot hold node references', false],
                    ['Using a mutable default argument `visited=set()` means the same set is reused across all calls to the function — state leaks between calls', true],
                    ['`visited.add()` is not a valid set method', false],
                    ['The function returns visited which is incorrect', false],
                ],
            ],
            [
                'q' => "What does the following monotonic stack code compute?\n\ndef next_greater(arr):\n    result = [-1] * len(arr)\n    stack = []\n    for i, val in enumerate(arr):\n        while stack and arr[stack[-1]] < val:\n            result[stack.pop()] = val\n        stack.append(i)\n    return result\n\nFor arr = [2, 1, 4, 3], what is result?",
                'opts' => [
                    ['[4, 4, -1, -1]', true],
                    ['[1, 4, 3, -1]', false],
                    ['[-1, 2, -1, 4]', false],
                    ['[2, 1, 4, 3]', false],
                ],
            ],

            // ── 7.4 QUEUES & PRIORITY QUEUES ─────────────────────────────
            [
                'q' => "What does the following Dijkstra's-style code compute?\n\nimport heapq\ndef shortest_path(graph, start):\n    dist = {node: float('inf') for node in graph}\n    dist[start] = 0\n    pq = [(0, start)]\n    while pq:\n        d, u = heapq.heappop(pq)\n        for v, w in graph[u]:\n            if dist[u] + w < dist[v]:\n                dist[v] = dist[u] + w\n                heapq.heappush(pq, (dist[v], v))\n    return dist\n\nWhat is the time complexity for a graph with V vertices and E edges?",
                'opts' => [
                    ['O(V²)', false],
                    ['O((V + E) log V)', true],
                    ['O(E log E)', false],
                    ['O(V · E)', false],
                ],
            ],
            [
                'q' => "In the Dijkstra code above, what happens if the graph contains a negative edge weight?",
                'opts' => [
                    ['The algorithm still produces the correct result', false],
                    ['The algorithm may produce incorrect shortest paths because it assumes once a node is popped from the heap its distance is final — negative edges can violate this', true],
                    ['The heap raises an error for negative weights', false],
                    ['The algorithm runs faster with negative weights', false],
                ],
            ],
            [
                'q' => "A machine learning model serving system assigns GPU jobs by expected training time (shortest job first). Which data structure correctly implements this?\n\nimport heapq\nqueue = []\nheapq.heappush(queue, (estimated_time, job_id))\nnext_job = heapq.heappop(queue)\n\nWhat does `next_job` contain?",
                'opts' => [
                    ['The job with the longest estimated_time', false],
                    ['The job with the smallest estimated_time — a min-heap returns the minimum first', true],
                    ['A random job', false],
                    ['The most recently inserted job', false],
                ],
            ],

            // ── 7.5 LINKED LISTS ──────────────────────────────────────────
            [
                'q' => "What does the following function do, and what is its time complexity?\n\ndef find_middle(head):\n    slow = fast = head\n    while fast and fast.next:\n        slow = slow.next\n        fast = fast.next.next\n    return slow",
                'opts' => [
                    ['Detects a cycle in the linked list — O(n)', false],
                    ['Finds the middle node of the linked list in one pass using two pointers — O(n)', true],
                    ['Reverses the linked list — O(n)', false],
                    ['Finds the last node — O(n)', false],
                ],
            ],
            [
                'q' => "A pandas DataFrame index internally behaves like a hash map. When you do `df.loc[key]`, it is:\n\nA) O(1) for a default RangeIndex\nB) O(1) average for a non-default Index (uses a hash table)\nC) O(n) for all index types\nD) O(log n) using a BST",
                'opts' => [
                    ['A only', false],
                    ['C only', false],
                    ['A and B', true],
                    ['D only', false],
                ],
            ],
            [
                'q' => "What is the bug in this linked list merge function?\n\ndef merge_sorted(l1, l2):\n    dummy = Node(0)\n    curr = dummy\n    while l1 and l2:\n        if l1.val < l2.val:\n            curr.next = l1\n            l1 = l1.next\n        else:\n            curr.next = l2\n            l2 = l2.next\n    curr.next = l1 or l2\n    return dummy  # <-- Bug",
                'opts' => [
                    ['The while loop condition is wrong', false],
                    ['The function returns dummy instead of dummy.next — it should return the node after the sentinel', true],
                    ['`l1 or l2` does not correctly append the remainder', false],
                    ['Node(0) is not a valid sentinel value', false],
                ],
            ],

            // ── 7.6 HASH TABLES ───────────────────────────────────────────
            [
                'q' => "What is the output of the following?\n\nfrom collections import defaultdict\ngraph = defaultdict(list)\nedges = [('A','B'), ('A','C'), ('B','D')]\nfor u, v in edges:\n    graph[u].append(v)\nprint(dict(graph))",
                'opts' => [
                    ["{'A': ['B', 'C'], 'B': ['D']}", true],
                    ["{'A': 'B', 'B': 'D', 'A': 'C'}", false],
                    ["{'A': 2, 'B': 1}", false],
                    ["defaultdict(<class 'list'>, {'A': ['B','C'], 'B': ['D']})", false],
                ],
            ],
            [
                'q' => "A feature store maps feature_name → feature_vector (a numpy array of floats). Which Python structure is most appropriate and why?",
                'opts' => [
                    ['A list of tuples — easy to iterate', false],
                    ['A dict with string keys and numpy array values — O(1) feature lookup by name, flexible value storage', true],
                    ['A set of feature names — fastest lookup', false],
                    ['A sorted list of feature names — O(log n) binary search', false],
                ],
            ],
            [
                'q' => "What does `collections.OrderedDict` guarantee that a regular `dict` in Python 3.7+ does NOT formally guarantee (even if CPython preserves order)?",
                'opts' => [
                    ['Faster key lookups', false],
                    ['OrderedDict explicitly guarantees insertion-order in its specification and provides `move_to_end()` and order-sensitive equality comparison', true],
                    ['Immutable keys', false],
                    ['Automatic sorting of keys', false],
                ],
            ],
            [
                'q' => "Bloom filters are probabilistic data structures used in data pipelines. They can definitively tell you:",
                'opts' => [
                    ['Exactly whether an item is in a set', false],
                    ['That an item is DEFINITELY NOT in the set (no false negatives), but may give false positives', true],
                    ['The count of how many times an item was inserted', false],
                    ['The sorted order of all inserted items', false],
                ],
            ],

            // ── 7.7 TREES: BINARY TREES & BSTs ───────────────────────────
            [
                'q' => "What does the following function compute on a binary tree?\n\ndef compute(root):\n    if root is None:\n        return 0\n    return 1 + max(compute(root.left), compute(root.right))",
                'opts' => [
                    ['The number of nodes in the tree', false],
                    ['The height (depth) of the tree', true],
                    ['The number of leaf nodes', false],
                    ['The sum of all node values', false],
                ],
            ],
            [
                'q' => "A scikit-learn Random Forest builds many decision trees. Each tree is trained on a bootstrap sample and a random subset of features. This reduces overfitting primarily because:\n\nA) Trees are shallower\nB) Feature randomness decorrelates the trees — averaging decorrelated trees reduces variance\nC) Bootstrap sampling increases the dataset size\nD) Decision trees have O(log n) prediction time",
                'opts' => [
                    ['A only', false],
                    ['D only', false],
                    ['B — decorrelated trees combine to reduce variance without increasing bias', true],
                    ['C only', false],
                ],
            ],
            [
                'q' => "What is the worst-case search time in an AVL tree (self-balancing BST) with n nodes?",
                'opts' => [
                    ['O(n) — it can degenerate like a plain BST', false],
                    ['O(log n) — AVL trees maintain a height of O(log n) by rotating on insertions/deletions', true],
                    ['O(1) — it uses a hash table internally', false],
                    ['O(n log n)', false],
                ],
            ],
            [
                'q' => "In a segment tree built over an array of n elements, what are the time complexities for point update and range sum query?",
                'opts' => [
                    ['O(n) update, O(1) query', false],
                    ['O(log n) update, O(log n) query', true],
                    ['O(1) update, O(n) query', false],
                    ['O(n) update, O(n) query', false],
                ],
            ],

            // ── 7.8 HEAPS & PRIORITY QUEUES ──────────────────────────────
            [
                'q' => "The following code tries to implement a running median using one heap but is incorrect:\n\nimport heapq\nheap = []\ndata = [5, 1, 3, 2, 4]\nfor x in data:\n    heapq.heappush(heap, x)\n    print(heap[0])  # Claims to print median\n\nWhy is this wrong?",
                'opts' => [
                    ['heapq.heappush does not work with integers', false],
                    ['heap[0] gives the minimum, not the median — a correct running median requires two heaps: a max-heap for the lower half and a min-heap for the upper half', true],
                    ['The median of a stream cannot be computed without storing all elements', false],
                    ['heapq does not support running insertion', false],
                ],
            ],
            [
                'q' => "To maintain a running median correctly, you use:\n\nmax_heap = []  # lower half (negate values)\nmin_heap = []  # upper half\n\nAfter inserting [5, 1, 3], the median is 3. After inserting 2, the median is 2.5. What is the key invariant to maintain?",
                'opts' => [
                    ['max_heap always has strictly more elements than min_heap', false],
                    ['len(max_heap) and len(min_heap) differ by at most 1, and max(-max_heap[0]) ≤ min_heap[0]', true],
                    ['Both heaps have the same size at all times', false],
                    ['min_heap always has strictly more elements than max_heap', false],
                ],
            ],
            [
                'q' => "Heap sort has O(n log n) time and O(1) space. Why is it rarely used in practice despite these properties?",
                'opts' => [
                    ['Heap sort is not stable and has poor cache performance due to non-sequential memory access patterns — Quick Sort and Merge Sort have better cache behaviour', true],
                    ['Heap sort is O(n²) on nearly-sorted data', false],
                    ['Heap sort cannot handle negative numbers', false],
                    ['Heap sort requires O(n) extra space', false],
                ],
            ],

            // ── 7.9 GRAPHS: BFS & DFS ─────────────────────────────────────
            [
                'q' => "What does the following BFS-based code compute?\n\nfrom collections import deque\ndef bfs_levels(graph, start):\n    visited = {start: 0}\n    queue = deque([start])\n    while queue:\n        node = queue.popleft()\n        for nbr in graph[node]:\n            if nbr not in visited:\n                visited[nbr] = visited[node] + 1\n                queue.append(nbr)\n    return visited",
                'opts' => [
                    ['Checks whether the graph is connected', false],
                    ['Returns the shortest distance (in hops) from start to every reachable node', true],
                    ['Returns a topological ordering of the graph', false],
                    ['Detects cycles in the graph', false],
                ],
            ],
            [
                'q' => "Topological sort is applicable to:\n\nA) Any undirected graph\nB) Directed Acyclic Graphs (DAGs) only\nC) Weighted graphs only\nD) Graphs where all nodes have the same degree",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "Kahn's algorithm for topological sort uses which data structure to process nodes with in-degree 0?",
                'opts' => [
                    ['A stack', false],
                    ['A min-heap sorted by node value', false],
                    ['A queue', true],
                    ['A set', false],
                ],
            ],
            [
                'q' => "In a recommender system modelled as a bipartite graph (users on one side, items on the other, edges = interactions), which algorithm finds all items reachable from a user within 2 hops most efficiently?",
                'opts' => [
                    ['DFS to depth 2 — O(V + E) per user', false],
                    ['BFS limited to 2 levels — O(V + E) but terminates early, visiting only nodes within 2 hops', true],
                    ['Dijkstra\'s algorithm with edge weight = hop count', false],
                    ['Sorting users by number of interactions', false],
                ],
            ],

            // ── 7.10 SORTING & SEARCHING ALGORITHMS ──────────────────────
            [
                'q' => "The following quicksort pivot selection causes worst-case O(n²) on sorted input:\n\ndef quicksort(arr):\n    if len(arr) <= 1: return arr\n    pivot = arr[0]  # <-- always first element\n    left = [x for x in arr[1:] if x <= pivot]\n    right = [x for x in arr[1:] if x > pivot]\n    return quicksort(left) + [pivot] + quicksort(right)\n\nWhat is the best fix for sorted inputs?",
                'opts' => [
                    ['Always use the last element as pivot', false],
                    ['Use the median-of-three pivot: median of arr[0], arr[len//2], arr[-1]', true],
                    ['Sort the array before applying quicksort', false],
                    ['Use a fixed pivot of 0', false],
                ],
            ],
            [
                'q' => "What does the following code implement and what is its time complexity?\n\ndef search(matrix, target):\n    r, c = 0, len(matrix[0]) - 1\n    while r < len(matrix) and c >= 0:\n        if matrix[r][c] == target:\n            return True\n        elif matrix[r][c] > target:\n            c -= 1\n        else:\n            r += 1\n    return False\n\nAssume matrix is n×m with rows and columns sorted.",
                'opts' => [
                    ['Binary search on a flattened array — O(log(n·m))', false],
                    ['Search in a row-and-column-sorted matrix starting from the top-right corner — O(n + m)', true],
                    ['Linear scan of the matrix — O(n·m)', false],
                    ['DFS on the matrix treating it as a graph — O(n·m)', false],
                ],
            ],
            [
                'q' => "Radix sort can achieve O(n) time complexity under certain conditions. Those conditions are:",
                'opts' => [
                    ['The input contains only positive integers', false],
                    ['The number of digits d is constant (or grows as O(log n)) and the base b is fixed — giving O(d · n) = O(n)', true],
                    ['The input is already nearly sorted', false],
                    ['The input fits in CPU cache', false],
                ],
            ],
            [
                'q' => "Tim Sort (Python\'s built-in sort) combines which two algorithms for optimal real-world performance?",
                'opts' => [
                    ['Quick Sort and Heap Sort', false],
                    ['Insertion Sort (for small runs) and Merge Sort (for combining runs)', true],
                    ['Radix Sort and Merge Sort', false],
                    ['Bubble Sort and Quick Sort', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 7 — Algorithms & Data Structures (Advanced).");
    }
}