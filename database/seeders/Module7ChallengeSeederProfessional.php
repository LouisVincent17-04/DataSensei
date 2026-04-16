<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module7ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 7 — Algorithms & Data Structures for Data Scientists (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Algorithms & Data Structures for Data Scientists',
            'description'           => 'Tackle real-world data engineering and machine learning system design problems. Questions demand expert-level reasoning about scalability, memory constraints, distributed computation, algorithmic correctness under edge cases, and production performance.',
            'time_limit_seconds'    => 2400,
            'base_xp'               => 3000,
            'order_index'           => 7,
        ]);

        $this->command->info("Seeding 50 professional-level questions on Algorithms & Data Structures...");

        $qaData = [

            // ── 7.1 BIG-O NOTATION & COMPLEXITY ANALYSIS ─────────────────
            [
                'q' => "A data preprocessing pipeline has the following steps run on n samples with d features:\n\n1. Standardise each feature: O(n · d)\n2. Compute PCA (covariance matrix + eigen decomposition): O(n · d² + d³)\n3. K-Means clustering (k clusters, t iterations): O(n · k · d · t)\n4. Output cluster labels: O(n)\n\nFor a dataset with n=10⁶, d=500, k=50, t=100, which step dominates?",
                'opts' => [
                    ['Step 1 — standardisation dominates for large n', false],
                    ['Step 2 — O(n·d²) = 10⁶·250,000 = 2.5×10¹¹ and O(d³) = 1.25×10⁸', false],
                    ['Step 3 — O(n·k·d·t) = 10⁶·50·500·100 = 2.5×10¹² dominates all other steps', true],
                    ['Step 4 — output is the bottleneck', false],
                ],
            ],
            [
                'q' => "What is the time complexity of computing pairwise Euclidean distances between all n points in d-dimensional space using naive nested loops, and what is the NumPy vectorised equivalent complexity?",
                'opts' => [
                    ['Naive: O(n²·d) via loops; vectorised: O(n²·d) but with constant factor ~100x smaller due to BLAS', true],
                    ['Naive: O(n³·d); vectorised: O(n·d)', false],
                    ['Naive: O(n²); vectorised: O(n log n)', false],
                    ['Both are O(n²) with identical constants', false],
                ],
            ],
            [
                'q' => "In the Master Theorem for recurrences T(n) = aT(n/b) + f(n), Merge Sort has a=2, b=2, f(n)=O(n). Since n^(log_b a) = n^1 = n and f(n) = Θ(n), Case 2 applies, giving T(n) = Θ(n log n). What does Case 3 (f(n) dominates) look like for an algorithm?",
                'opts' => [
                    ['When f(n) = O(1) — the combining step is trivial', false],
                    ['When f(n) = Ω(n^(log_b a + ε)) for some ε > 0 — the split/merge overhead dominates the recursive calls, e.g. T(n) = T(n/2) + O(n) has T(n) = O(n)', true],
                    ['When a=1 and b=2 — always Case 3', false],
                    ['When the recursion depth is O(log n)', false],
                ],
            ],
            [
                'q' => "A streaming data platform processes events in real-time. For each event, it checks membership in a set of 100 million blacklisted IDs. Memory is limited to 200 MB. A plain Python set of 100M integers would require approximately:\n\n100M × 28 bytes (Python int) × ~2 overhead ≈ 5.6 GB\n\nWhich data structure fits in 200 MB with acceptable false-positive rate?",
                'opts' => [
                    ['A sorted numpy array with binary search — O(log n) lookup, ~800 MB', false],
                    ['A Bloom filter — configurable space/error trade-off; 200 MB supports ~100M elements at ~1% FPR', true],
                    ['A doubly linked list of IDs', false],
                    ['A trie of ID strings', false],
                ],
            ],
            [
                'q' => "You benchmark two algorithms:\n- Algorithm A: 0.001n² ms\n- Algorithm B: 100n ms\n\nFor n = 100,000 rows, which is faster? At what crossover point does B become faster than A?",
                'opts' => [
                    ['A is always faster because its constant is smaller', false],
                    ['At n=100,000: A takes 10,000 ms; B takes 10,000,000 ms — A is faster. Crossover: 0.001n² = 100n → n = 100,000. Above this, A is faster; below, B is faster', true],
                    ['B is always faster for data science workloads', false],
                    ['They perform identically for n > 1,000', false],
                ],
            ],

            // ── 7.2 ARRAYS & DYNAMIC ARRAYS ──────────────────────────────
            [
                'q' => "You need to compute a rolling 7-day average over 10 years of daily stock prices (3,650 rows). Which implementation is correct AND most performant?\n\nA: `pd.Series(prices).rolling(7).mean()`\nB: `[sum(prices[i-7:i])/7 for i in range(7, len(prices))]`\nC: `np.convolve(prices, np.ones(7)/7, mode='valid')`",
                'opts' => [
                    ['Only A is correct', false],
                    ['A, B, and C all produce equivalent results; A and C are vectorised O(n) implementations — A is idiomatic pandas, C uses FFT under the hood for large windows', true],
                    ['B is the fastest because list comprehensions avoid pandas overhead', false],
                    ['C is incorrect — np.convolve does not compute averages', false],
                ],
            ],
            [
                'q' => "A data engineer receives daily appended CSV files. They load each day's file and need to compute cumulative statistics. Which approach avoids re-reading all historical data each day?\n\nA) Reload all historical data and recompute\nB) Maintain running count and sum; update incrementally: new_mean = (old_sum + new_sum) / (old_count + new_count)\nC) Store all data in a heap for O(log n) updates\nD) Hash all values daily for deduplication",
                'opts' => [
                    ['A — most accurate', false],
                    ['B — O(1) per day update using Welford\'s online algorithm for numerical stability', true],
                    ['C — heap updates are sufficient', false],
                    ['D — hashing preserves statistics', false],
                ],
            ],
            [
                'q' => "What is the output and what does it reveal about NumPy broadcasting?\n\nimport numpy as np\nA = np.array([[1],[2],[3]])   # shape (3,1)\nB = np.array([10, 20, 30])    # shape (3,)\nprint((A + B).shape)",
                'opts' => [
                    ['(3,) — arrays are added element-wise after flattening', false],
                    ['Error — shapes (3,1) and (3,) are incompatible', false],
                    ['(3, 3) — B is broadcast to shape (3,3) and A\'s column is broadcast across 3 columns', true],
                    ['(1, 3)', false],
                ],
            ],
            [
                'q' => "A production ML pipeline stores model predictions in a pre-allocated numpy array vs. appending to a Python list. For 10 million predictions, which is faster and why?\n\npredictions = np.empty(10_000_000)\nfor i, x in enumerate(data):\n    predictions[i] = model.predict(x)\n\nvs.\n\npredictions = []\nfor x in data:\n    predictions.append(model.predict(x))",
                'opts' => [
                    ['The list approach is faster — append is O(1) amortised', false],
                    ['The numpy approach is faster — pre-allocation eliminates all resize operations and produces a contiguous array immediately usable in downstream NumPy operations', true],
                    ['Both are identical in speed', false],
                    ['The numpy approach is slower due to index overhead', false],
                ],
            ],

            // ── 7.3 STACKS ────────────────────────────────────────────────
            [
                'q' => "An expression evaluator for a data query language must handle nested function calls:\nf(g(h(x), y), z)\n\nDuring AST (Abstract Syntax Tree) construction via recursive descent parsing, the call stack depth equals the nesting depth. For deeply nested queries (depth 10,000+), this causes stack overflow. What is the professional solution?",
                'opts' => [
                    ['Increase Python\'s recursion limit to sys.setrecursionlimit(100000)', false],
                    ['Convert the recursive parser to an iterative one using an explicit operator/operand stack, eliminating call-stack dependency entirely', true],
                    ['Pre-process the query string to reduce nesting', false],
                    ['Use threads to distribute the recursion', false],
                ],
            ],
            [
                'q' => "The following implements undo/redo in a data annotation tool:\n\nundo_stack = []\nredo_stack = []\n\ndef apply_action(action):\n    action.execute()\n    undo_stack.append(action)\n    redo_stack.clear()   # <-- Why?\n\ndef undo():\n    action = undo_stack.pop()\n    action.reverse()\n    redo_stack.append(action)\n\nWhy must redo_stack.clear() be called in apply_action?",
                'opts' => [
                    ['To free memory after every action', false],
                    ['Because once a new action is taken after an undo, the previous redo history is no longer valid — the state has diverged from that branch', true],
                    ['Because the redo stack is limited in size', false],
                    ['To prevent the undo stack from growing unboundedly', false],
                ],
            ],

            // ── 7.4 QUEUES & PRIORITY QUEUES ─────────────────────────────
            [
                'q' => "A Kubernetes-style job scheduler must:\n1. Handle jobs with priorities (0–100)\n2. Support job cancellation by job_id\n3. Guarantee O(log n) insertion and extraction\n\nA standard `heapq` does NOT support O(log n) cancellation. What is the professional workaround?",
                'opts' => [
                    ['Rebuild the heap after every cancellation — O(n)', false],
                    ['Use a \"lazy deletion\" approach: mark jobs as cancelled in a set; when popping, skip cancelled jobs — still O(log n) amortised insertion/extraction', true],
                    ['Use a doubly linked list sorted by priority — O(1) deletion', false],
                    ['Use a Python dict as the priority queue', false],
                ],
            ],
            [
                'q' => "In Apache Kafka\'s consumer group rebalancing, partition assignment uses a sorted queue of consumers ordered by their current load. When a consumer joins or leaves, the assignment must rebalance. What data structure minimises rebalance cost?",
                'opts' => [
                    ['A plain sorted list — O(n log n) to re-sort after each join/leave', false],
                    ['A sorted set (like a balanced BST / SortedList in Python sortedcontainers) — O(log n) insert/delete while maintaining sorted order', true],
                    ['A hash table of consumer → partition count', false],
                    ['A deque of consumers in FIFO order', false],
                ],
            ],
            [
                'q' => "The A* pathfinding algorithm (used in ML-based robot navigation) uses a priority queue ordered by f(n) = g(n) + h(n). Compared to Dijkstra's algorithm, A* is:\n\nA) Always faster\nB) Faster when the heuristic h(n) is admissible and guides search toward the goal\nC) Slower because it computes h(n) for every node\nD) Equivalent to Dijkstra when h(n) = 0",
                'opts' => [
                    ['A only', false],
                    ['C only', false],
                    ['B and D — A* with h=0 degenerates to Dijkstra; a good heuristic makes A* explore fewer nodes', true],
                    ['A and C', false],
                ],
            ],

            // ── 7.5 LINKED LISTS ──────────────────────────────────────────
            [
                'q' => "PyTorch's autograd builds a computational graph as a singly linked list of operations (each node holds a reference to the operation that produced it). During `loss.backward()`, the engine traverses this list in reverse. What traversal pattern is this?",
                'opts' => [
                    ['Breadth-first traversal using a queue', false],
                    ['Reverse traversal of a DAG (treated as a linked structure) using a topological sort in reverse order — effectively a stack-based DFS in reverse', true],
                    ['In-order traversal of a BST', false],
                    ['Circular linked list traversal', false],
                ],
            ],
            [
                'q' => "Spark's RDD lineage graph is a DAG of transformations. When a partition is lost, Spark recomputes it by replaying the lineage from the last checkpoint. This is equivalent to:\n\nA) Traversing a linked list from the head\nB) Topological traversal of the lineage DAG from the failed partition back to a checkpoint node\nC) A stack-based DFS to find the shortest recomputation path\nD) Heap-based selection of the cheapest RDD to recompute",
                'opts' => [
                    ['A — linked list replay', false],
                    ['C — DFS shortest path', false],
                    ['B — DAG traversal from failure to checkpoint', true],
                    ['D — heap selection', false],
                ],
            ],

            // ── 7.6 HASH TABLES ───────────────────────────────────────────
            [
                'q' => "A distributed feature store uses consistent hashing to assign features to servers. When a new server is added:\n\nA) All features must be rehashed — O(n) data movement\nB) Only features in the ring segment adjacent to the new server need to be moved — O(n/k) movement for k servers\nC) No data needs to be moved — the hash ring is static\nD) All servers must be restarted",
                'opts' => [
                    ['A', false],
                    ['C', false],
                    ['D', false],
                    ['B — consistent hashing minimises data movement to O(n/k) on average when adding/removing servers', true],
                ],
            ],
            [
                'q' => "What does the following data deduplication code output, and is there a correctness issue?\n\nfrom collections import defaultdict\nimport hashlib\n\ndef dedup(records):\n    seen = defaultdict(list)\n    for r in records:\n        key = hashlib.md5(str(r).encode()).hexdigest()\n        seen[key].append(r)\n    return [v[0] for v in seen.values()]\n\nrecords = [{'a':1}, {'a':1}, {'b':2}]\nprint(len(dedup(records)))",
                'opts' => [
                    ['3 — all records are unique', false],
                    ['2 — MD5 hashing correctly deduplicates identical dicts', true],
                    ['1 — MD5 produces the same hash for all dicts', false],
                    ['Error — dicts are not hashable', false],
                ],
            ],
            [
                'q' => "Count-Min Sketch is used in streaming analytics to estimate the frequency of events. Compared to an exact frequency dictionary, Count-Min Sketch:\n\nA) Uses O(1) space regardless of data size\nB) Always overestimates frequencies (never underestimates) with configurable error bounds\nC) Provides exact counts for all elements\nD) Requires sorted data to function",
                'opts' => [
                    ['A and D', false],
                    ['C only', false],
                    ['B — Count-Min Sketch overestimates due to hash collisions but never underestimates, with error controlled by width and depth parameters', true],
                    ['A only', false],
                ],
            ],
            [
                'q' => "A real-time fraud detection system must look up whether a transaction ID was seen in the last 24 hours. The system processes 100,000 transactions/second. IDs expire after 24 hours. Which design is optimal?",
                'opts' => [
                    ['A Python dict with manual expiry checks on every lookup — O(1) lookup but unbounded memory growth', false],
                    ['A TTL-enabled hash map (like Redis with EXPIRE) — O(1) lookup with automatic expiry, bounded memory, and distributed access', true],
                    ['A sorted list of (timestamp, id) pairs with binary search — O(log n) lookup', false],
                    ['A Bloom filter alone — provides membership testing but cannot delete expired entries', false],
                ],
            ],

            // ── 7.7 TREES: BINARY TREES & BSTs ───────────────────────────
            [
                'q' => "XGBoost builds an ensemble of gradient-boosted trees. Each tree is a regression tree that fits the residuals of the previous ensemble. The node-splitting algorithm evaluates the gain for each potential split:\n\nGain = (G_L²/H_L + G_R²/H_R - (G_L+G_R)²/(H_L+H_R)) / 2 - λ\n\nTo efficiently find the best split across d features × n samples, XGBoost uses:",
                'opts' => [
                    ['Exhaustive O(n²·d) scan of all pairs', false],
                    ['Sorted histograms per feature: for each feature, pre-sort values and scan thresholds — O(n·d·log n) total or O(n·d) with histogram approximation', true],
                    ['Random search — pick 1000 random splits', false],
                    ['BST insertion of all feature values — O(n·d·log n) insertion only', false],
                ],
            ],
            [
                'q' => "A k-d tree (k-dimensional tree) is a BST that partitions k-dimensional space. It is used in:\n\nA) KNN (k-Nearest Neighbours) queries — O(log n) average vs. O(n) brute force\nB) Exact nearest-neighbour in high dimensions (d > 20) efficiently\nC) Range queries in spatial data\nD) Approximate nearest-neighbour when combined with ball trees",
                'opts' => [
                    ['A only', false],
                    ['A and C — k-d trees work well for low-to-moderate d; C is a direct use case', true],
                    ['B only — high-dimensional NN is the primary use case', false],
                    ['D only', false],
                ],
            ],
            [
                'q' => "What does the following code build and what is the structure's purpose in a data pipeline?\n\nclass TrieNode:\n    def __init__(self):\n        self.children = {}\n        self.is_end = False\n\nclass Trie:\n    def __init__(self):\n        self.root = TrieNode()\n    def insert(self, word):\n        node = self.root\n        for ch in word:\n            node = node.children.setdefault(ch, TrieNode())\n        node.is_end = True\n    def starts_with(self, prefix):\n        node = self.root\n        for ch in prefix:\n            if ch not in node.children:\n                return False\n            node = node.children[ch]\n        return True",
                'opts' => [
                    ['A BST sorted alphabetically', false],
                    ['A Trie (prefix tree): O(L) insert and prefix search where L is word/prefix length — used for autocomplete, URL routing, and log prefix filtering', true],
                    ['A hash table of words', false],
                    ['A segment tree for range prefix queries', false],
                ],
            ],

            // ── 7.8 HEAPS & PRIORITY QUEUES ──────────────────────────────
            [
                'q' => "An online learning system updates model weights after each mini-batch. You need to maintain the top-K most uncertain samples (highest entropy) across a stream of 10M samples efficiently. Which approach is optimal?\n\nA) Sort all 10M samples by entropy — O(n log n), requires all data\nB) Maintain a min-heap of size K: if a new sample's entropy > heap[0], replace — O(n log K)\nC) Maintain a max-heap of all n samples — O(n log n) space\nD) Random sampling of K items",
                'opts' => [
                    ['A', false],
                    ['C', false],
                    ['D', false],
                    ['B — O(n log K) time and O(K) space; exactly what `heapq.nlargest(K, stream, key=entropy)` implements under the hood', true],
                ],
            ],
            [
                'q' => "The following implements a min-heap-based merge of k sorted lists of total n elements:\n\nimport heapq\ndef merge_k_sorted(lists):\n    heap = []\n    for i, lst in enumerate(lists):\n        if lst:\n            heapq.heappush(heap, (lst[0], i, 0))\n    result = []\n    while heap:\n        val, i, j = heapq.heappop(heap)\n        result.append(val)\n        if j + 1 < len(lists[i]):\n            heapq.heappush(heap, (lists[i][j+1], i, j+1))\n    return result\n\nWhat is the time complexity?",
                'opts' => [
                    ['O(n·k) — for each of n elements, scan k lists', false],
                    ['O(n log k) — each of n elements is pushed and popped from a heap of size at most k', true],
                    ['O(n log n)', false],
                    ['O(k log n)', false],
                ],
            ],

            // ── 7.9 GRAPHS: BFS & DFS ─────────────────────────────────────
            [
                'q' => "A data lineage system tracks how datasets are derived from each other (a DAG). Given a DAG with V datasets and E derivation edges, you need to find all datasets that would be invalidated if dataset X changes. What is the optimal algorithm and complexity?",
                'opts' => [
                    ['BFS/DFS from X following edges in the FORWARD direction — O(V + E) — finds all descendants of X', true],
                    ['Dijkstra\'s algorithm from X — O((V+E) log V)', false],
                    ['Binary search on sorted dataset IDs — O(log V)', false],
                    ['Reverse BFS from all leaf nodes — O(V²)', false],
                ],
            ],
            [
                'q' => "Tarjan's Strongly Connected Components (SCC) algorithm finds groups of nodes mutually reachable from each other. In a feature dependency graph, an SCC of size > 1 indicates:\n\nA) A valid processing order exists\nB) A circular dependency — features that depend on each other, which is typically a data pipeline error\nC) An isolated subgraph with no external connections\nD) A perfectly balanced dependency tree",
                'opts' => [
                    ['A', false],
                    ['C', false],
                    ['D', false],
                    ['B — circular dependencies break topological ordering and must be resolved before a valid execution order can be produced', true],
                ],
            ],
            [
                'q' => "PageRank is computed by iterating the power method on the adjacency matrix of a web graph. In data science, the same algorithm appears as:\n\nA) HITS algorithm for hub-authority scoring\nB) Eigenvector centrality in social network analysis — the node score is proportional to the sum of its neighbours' scores\nC) Betweenness centrality using Brandes' algorithm\nD) Louvain community detection",
                'opts' => [
                    ['A only', false],
                    ['C only', false],
                    ['B — PageRank and eigenvector centrality are both solutions to the leading eigenvector of the (normalised) adjacency matrix', true],
                    ['D only', false],
                ],
            ],
            [
                'q' => "Prim's and Kruskal's algorithms both find a Minimum Spanning Tree (MST). In a data science context, MSTs are used in:\n\nA) Single-linkage hierarchical clustering — the MST encodes the merge sequence\nB) Dimensionality reduction — MST replaces PCA\nC) Feature selection — the MST selects the minimum set of features\nD) Neural network pruning — removing minimum-weight connections",
                'opts' => [
                    ['B only', false],
                    ['C and D', false],
                    ['A — single-linkage clustering builds the same dendrogram as the MST; also used in Kruskal\'s-based clustering algorithms', true],
                    ['D only', false],
                ],
            ],

            // ── 7.10 SORTING & SEARCHING ALGORITHMS ──────────────────────
            [
                'q' => "A pandas merge (join) operation on two DataFrames of sizes n and m on a key column uses which algorithm internally, and what is its complexity?\n\npd.merge(df1, df2, on='key')",
                'opts' => [
                    ['Nested loop join — O(n·m)', false],
                    ['Hash join: build a hash table on the smaller DataFrame (O(m)), probe with each row of the larger (O(n)) — total O(n + m)', true],
                    ['Sort-merge join — O((n+m) log(n+m))', false],
                    ['Binary search join — O(n log m)', false],
                ],
            ],
            [
                'q' => "External merge sort is used when data does not fit in RAM. A dataset of 100 GB must be sorted on a machine with 8 GB RAM. The algorithm:\n\n1. Read 8 GB chunks, sort in memory (QuickSort), write sorted runs to disk\n2. Merge sorted runs using a k-way merge with a min-heap\n\nIf each sorted run is 8 GB and we have 13 runs, the merge step reads 100 GB once. What is the total I/O cost in terms of passes over the data?",
                'opts' => [
                    ['1 pass (sort) + 1 pass (merge) = 2 passes over 100 GB', true],
                    ['13 passes — one per sorted run', false],
                    ['Only 1 pass — in-memory sorting avoids multiple reads', false],
                    ['log(13) ≈ 4 passes', false],
                ],
            ],
            [
                'q' => "Approximate Nearest Neighbour (ANN) search algorithms like FAISS (Facebook AI Similarity Search) are preferred over exact k-d tree search for high-dimensional embeddings because:\n\nA) k-d trees have O(n) query time for d > 20 due to the curse of dimensionality — ANN trades a small accuracy loss for O(log n) or sublinear query time\nB) FAISS uses sorting internally, which is faster than tree traversal\nC) Exact k-d tree search is O(n²) in high dimensions\nD) FAISS stores embeddings in a linked list",
                'opts' => [
                    ['B', false],
                    ['C', false],
                    ['D', false],
                    ['A — the curse of dimensionality makes all points roughly equidistant in high-d space, collapsing k-d tree pruning efficiency; ANN methods like HNSW or IVF provide practical sublinear search', true],
                ],
            ],
            [
                'q' => "A production recommendation engine must re-rank 10,000 candidate items for each of 1 million user requests per day using a scoring function that is O(d) per item (d = feature dimension). Total daily computation:\n\n10,000 items × 1,000,000 requests × d operations\n\nFor d = 200, this is 2 × 10¹² operations. Which systems-level optimisation reduces this most significantly?",
                'opts' => [
                    ['Switch from Python to C++ for the scoring loop — ~100x faster but still 2×10¹⁰ operations', false],
                    ['Use Maximum Inner Product Search (MIPS) with an approximate index to reduce candidates from 10,000 to ~100 per request before exact re-ranking — reduces computation by ~100x', true],
                    ['Cache all user scores in a dictionary — O(1) lookup but memory is 10,000 × 1M × 200 floats ≈ impossible', false],
                    ['Sort candidates by ID and use binary search to skip low-scoring items', false],
                ],
            ],
            [
                'q' => "The following code is a production bug in a data pipeline. Identify the issue:\n\ndef process_batch(batch, seen=set()):\n    results = []\n    for item in batch:\n        if item['id'] not in seen:\n            seen.add(item['id'])\n            results.append(transform(item))\n    return results\n\n# Called daily with new batches\nday1 = process_batch(batch1)\nday2 = process_batch(batch2)  # Bug manifests here",
                'opts' => [
                    ['`set()` cannot store string IDs', false],
                    ['Mutable default argument `seen=set()` is shared across all calls — day2 will incorrectly skip items whose IDs appeared in day1, causing data loss', true],
                    ['The function does not handle empty batches', false],
                    ['`transform(item)` is called too many times', false],
                ],
            ],
            [
                'q' => "A data scientist implements feature hashing (the hashing trick) to encode high-cardinality categorical features:\n\nfrom sklearn.feature_extraction import FeatureHasher\nfh = FeatureHasher(n_features=2**18, input_type='string')\n\nThe primary trade-off of feature hashing vs. one-hot encoding is:",
                'opts' => [
                    ['Feature hashing is always more accurate', false],
                    ['Feature hashing produces a fixed-size sparse vector in O(1) space per category regardless of cardinality, but introduces hash collisions (two categories may map to the same index), causing a small accuracy penalty', true],
                    ['Feature hashing requires sorting categories alphabetically first', false],
                    ['Feature hashing cannot handle unseen categories at inference time', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 7 — Algorithms & Data Structures (Professional).");
    }
}