<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 21 — Unsupervised Learning (Newbie) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering all unsupervised learning topics
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered:
 *   - What Is Unsupervised Learning?
 *   - K-Means Clustering
 *   - DBSCAN: Density-Based Clustering
 *   - Hierarchical Clustering & Dendrograms
 *   - Principal Component Analysis (PCA)
 *   - t-SNE & UMAP: Non-Linear Dimensionality Reduction
 *   - Anomaly Detection: Isolation Forest
 *   - Autoencoders for Unsupervised Learning
 *   - Gaussian Mixture Models & Soft Clustering
 *   - End-to-End Unsupervised ML Pipeline
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module21CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 21 — Unsupervised Learning (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Unsupervised Learning',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Implement core unsupervised learning algorithms from scratch in Python. Tasks cover distance calculations, k-means assignment, DBSCAN neighborhood queries, hierarchical linkage, PCA projection, anomaly scoring, autoencoder reconstruction error, Gaussian mixture responsibilities, and end-to-end pipeline steps. Each task is short and self-contained.',
                'time_limit_seconds' => 900,
                'base_xp'            => 500,
                'order_index'        => 21,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: What Is Unsupervised Learning? (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Read an integer `n` followed by `n` lines, each either `"labeled: <value>"` or `"unlabeled"`. Count and print the number of unlabeled examples.

Example:
```
Input:
4
labeled: cat
unlabeled
labeled: dog
unlabeled
Output: 2
```
MD,
                'starter_code'        => "n = int(input())\ncount = 0\nfor _ in range(n):\n    line = input()\n    # Count unlabeled examples\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read an integer `n` followed by `n` space-separated 2D points (x y per line). Print the centroid (mean x, mean y) rounded to 4 decimal places, space-separated.

Example:
```
Input:
3
1.0 2.0
3.0 4.0
5.0 6.0
Output: 3.0000 4.0000
```
MD,
                'starter_code'        => "n = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Compute and print centroid\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Euclidean distance: read two 2D points (each as space-separated floats, one per line) and print their Euclidean distance rounded to 4 decimal places.

Example:
```
Input:
0.0 0.0
3.0 4.0
Output: 5.0000
```
MD,
                'starter_code'        => "import math\np1 = list(map(float, input().split()))\np2 = list(map(float, input().split()))\n# Compute and print distance\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read a dataset size `n` and a percentage `p` (integer). Print how many samples would go into a train split of size `p`% (truncated to int) and how many into the remaining test split.

Example:
```
Input:
100
80
Output:
train: 80
test: 20
```
MD,
                'starter_code'        => "n = int(input())\np = int(input())\n# Compute and print split sizes\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read an integer `n` followed by `n` integers representing cluster labels (may be -1 for noise). Print the number of distinct clusters (excluding -1).

Example:
```
Input:
6
0
1
0
-1
1
2
Output: 3
```
MD,
                'starter_code'        => "n = int(input())\nlabels = [int(input()) for _ in range(n)]\n# Count distinct clusters excluding -1\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: K-Means Clustering (Q6–Q12)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
K-means assignment step: given `k` centroids and `n` points (all 2D), assign each point to its nearest centroid (0-indexed). Print the cluster assignment for each point, one per line.

Input format: first line `k n`, then `k` centroid lines, then `n` point lines.

Example:
```
Input:
2 3
0.0 0.0
10.0 10.0
1.0 1.0
9.0 9.0
5.0 5.0
Output:
0
1
0
```
MD,
                'starter_code'        => "import math\nk, n = map(int, input().split())\ncentroids = [list(map(float, input().split())) for _ in range(k)]\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Assign each point to nearest centroid\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
K-means update step: given cluster assignments and 2D points, compute the new centroid for a specified cluster. Read `n` (number of points), then `n` lines of `x y label`, then a target cluster label. Print the new centroid rounded to 4 decimal places, space-separated.

Example:
```
Input:
4
1.0 2.0 0
3.0 4.0 1
5.0 6.0 0
7.0 8.0 1
0
Output: 3.0000 4.0000
```
MD,
                'starter_code'        => "n = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    x, y, label = float(parts[0]), float(parts[1]), int(parts[2])\n    data.append((x, y, label))\ntarget = int(input())\n# Compute new centroid for target cluster\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Compute the inertia (sum of squared distances from each point to its assigned centroid) given assignments and centroids.

Input: first line `k n`, then `k` centroid lines, then `n` lines of `x y cluster_label`. Print inertia rounded to 4 decimal places.

Example:
```
Input:
2 4
0.0 0.0
10.0 10.0
1.0 1.0 0
-1.0 -1.0 0
9.0 9.0 1
11.0 11.0 1
Output: 8.0000
```
MD,
                'starter_code'        => "k, n = map(int, input().split())\ncentroids = [list(map(float, input().split())) for _ in range(k)]\ninertia = 0.0\nfor _ in range(n):\n    parts = input().split()\n    x, y, label = float(parts[0]), float(parts[1]), int(parts[2])\n    # Add squared distance to assigned centroid\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Run one full k-means iteration (assign + update): given initial centroids and points, print the new centroids after one iteration rounded to 4 decimal places.

Input: first line `k n`, then `k` centroid lines, then `n` point lines (x y).

Example:
```
Input:
2 4
0.0 0.0
8.0 8.0
1.0 1.0
2.0 2.0
7.0 7.0
9.0 9.0
Output:
1.5000 1.5000
8.0000 8.0000
```
MD,
                'starter_code'        => "import math\nk, n = map(int, input().split())\ncentroids = [list(map(float, input().split())) for _ in range(k)]\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Assign then update centroids\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read cluster assignments (integers, one per line, preceded by count `n`) and print the size of each cluster in ascending order of cluster id, in the format `cluster X: Y`.

Example:
```
Input:
6
0
1
0
2
1
0
Output:
cluster 0: 3
cluster 1: 2
cluster 2: 1
```
MD,
                'starter_code'        => "n = int(input())\nlabels = [int(input()) for _ in range(n)]\n# Count and print cluster sizes\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Given a set of 1D data points and k=2 initial centroids, run k-means until convergence (max 100 iterations). Print the final two centroids rounded to 4 decimal places, one per line (smaller first).

Input: first line `n`, second line the `n` space-separated floats, third line the 2 initial centroids space-separated.

Example:
```
Input:
6
1.0 1.5 2.0 8.0 8.5 9.0
0.0 10.0
Output:
1.5000
8.5000
```
MD,
                'starter_code'        => "n = int(input())\npoints = list(map(float, input().split()))\nc1, c2 = map(float, input().split())\n# Run k-means until convergence\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Read `n` 2D points and their cluster labels (x y label per line, preceded by count `n`). Compute and print the within-cluster variance for each cluster in ascending cluster id order, rounded to 4 decimal places, in format `cluster X: Y`.

Variance = mean of squared distances from centroid.

Example:
```
Input:
4
0.0 0.0 0
2.0 0.0 0
10.0 0.0 1
12.0 0.0 1
Output:
cluster 0: 1.0000
cluster 1: 1.0000
```
MD,
                'starter_code'        => "n = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    x, y, label = float(parts[0]), float(parts[1]), int(parts[2])\n    data.append((x, y, label))\n# Compute and print variance per cluster\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: DBSCAN (Q13–Q17)
            // ═══════════════════════════════════════════════════════════════

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
DBSCAN neighborhood query: given a set of 1D points and parameters `eps` and `min_samples`, print `"core"` if the first point is a core point (has at least `min_samples` points within distance `eps`, including itself), otherwise print `"non-core"`.

Input: first line `n eps min_samples`, then `n` floats one per line. The first point is the query.

Example:
```
Input:
5 1.5 3
1.0
1.5
2.0
5.0
6.0
Output: core
```
MD,
                'starter_code'        => "parts = input().split()\nn, eps, min_samples = int(parts[0]), float(parts[1]), int(parts[2])\npoints = [float(input()) for _ in range(n)]\n# Check if points[0] is a core point\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Count neighbors: given a query point and a list of 1D points with an epsilon radius, print how many points (including the query point itself if in the list) fall within distance `eps` of the query point.

Input: first line is `query eps`, second line is `n`, then `n` floats.

Example:
```
Input:
5.0 2.0
4
3.0
5.5
8.0
6.0
Output: 3
```
MD,
                'starter_code'        => "query, eps = map(float, input().split())\nn = int(input())\npoints = [float(input()) for _ in range(n)]\n# Count neighbors within eps\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Given DBSCAN output labels for `n` points (read `n` then labels one per line), print the number of noise points (label == -1).

Example:
```
Input:
5
0
-1
0
-1
1
Output: 2
```
MD,
                'starter_code'        => "n = int(input())\nlabels = [int(input()) for _ in range(n)]\n# Count noise points\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Simple 1D DBSCAN: given `n` 1D points, `eps`, and `min_samples`, classify each point as `"core"`, `"border"`, or `"noise"`. A core point has >= `min_samples` points within eps. A border point is within eps of a core but is not core itself. Otherwise noise. Print one label per line.

Input: first line `n eps min_samples`, then `n` floats.

Example:
```
Input:
5 1.5 3
1.0
1.5
2.0
5.0
6.0
Output:
core
core
core
noise
noise
```
MD,
                'starter_code'        => "parts = input().split()\nn, eps, min_samples = int(parts[0]), float(parts[1]), int(parts[2])\npoints = [float(input()) for _ in range(n)]\n# Classify each point\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Read `n` DBSCAN cluster labels (preceded by `n`). Print the cluster label that has the most points (excluding -1 noise). If tied, print the smallest label.

Example:
```
Input:
7
0
1
0
-1
1
0
2
Output: 0
```
MD,
                'starter_code'        => "n = int(input())\nlabels = [int(input()) for _ in range(n)]\n# Find most frequent non-noise cluster\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Hierarchical Clustering & Dendrograms (Q18–Q22)
            // ═══════════════════════════════════════════════════════════════

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Single-linkage distance: given two clusters (each as a set of 1D points), compute the minimum pairwise distance between any point in cluster A and any point in cluster B. Print rounded to 4 decimal places.

Input: first line `na nb`, then `na` floats for cluster A, then `nb` floats for cluster B.

Example:
```
Input:
2 2
1.0
3.0
7.0
9.0
Output: 4.0000
```
MD,
                'starter_code'        => "na, nb = map(int, input().split())\nA = [float(input()) for _ in range(na)]\nB = [float(input()) for _ in range(nb)]\n# Compute single-linkage distance\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Complete-linkage distance: given two clusters of 1D points, compute the maximum pairwise distance between any point in cluster A and any point in cluster B. Print rounded to 4 decimal places.

Input: first line `na nb`, then `na` floats for A, then `nb` floats for B.

Example:
```
Input:
2 2
1.0
3.0
7.0
9.0
Output: 8.0000
```
MD,
                'starter_code'        => "na, nb = map(int, input().split())\nA = [float(input()) for _ in range(na)]\nB = [float(input()) for _ in range(nb)]\n# Compute complete-linkage distance\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Average-linkage distance: given two clusters of 1D points, compute the average of all pairwise distances. Print rounded to 4 decimal places.

Input: first line `na nb`, then `na` floats for A, then `nb` floats for B.

Example:
```
Input:
2 2
1.0
3.0
7.0
9.0
Output: 6.0000
```
MD,
                'starter_code'        => "na, nb = map(int, input().split())\nA = [float(input()) for _ in range(na)]\nB = [float(input()) for _ in range(nb)]\n# Compute average-linkage distance\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Agglomerative step: given `n` singleton clusters (1D points) and using single-linkage, find and print the pair of cluster indices (0-based) that should be merged first (smallest distance). If tied, print the pair with the smallest first index, then smallest second.

Input: `n` then `n` floats.

Example:
```
Input:
4
1.0
3.0
2.0
8.0
Output: 0 2
```
MD,
                'starter_code'        => "n = int(input())\npoints = [float(input()) for _ in range(n)]\n# Find pair with smallest distance\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Read a dendrogram cut threshold `t` and `n` merge heights (floats, one per line). Count how many merges occur at height <= `t` (these form the clusters below the cut). Print the count.

Example:
```
Input:
5.0
4
1.0
3.0
5.0
8.0
Output: 3
```
MD,
                'starter_code'        => "t = float(input())\nn = int(input())\ncount = sum(1 for _ in range(n) if float(input()) <= t)\nprint(count)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Principal Component Analysis (PCA) (Q23–Q27)
            // ═══════════════════════════════════════════════════════════════

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Mean-center a dataset: read `n` 2D points and print each point minus the mean of the dataset, rounded to 4 decimal places.

Input: `n`, then `n` lines of `x y`.

Example:
```
Input:
3
1.0 2.0
3.0 4.0
5.0 6.0
Output:
-2.0000 -2.0000
0.0000 0.0000
2.0000 2.0000
```
MD,
                'starter_code'        => "n = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Compute mean, then center and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Project a 2D point onto a unit vector (principal component). Read a 2D point and a 2D unit vector (one per line), and print the projected scalar value (dot product) rounded to 4 decimal places.

Example:
```
Input:
3.0 4.0
0.6 0.8
Output: 5.0000
```
MD,
                'starter_code'        => "point = list(map(float, input().split()))\nvec = list(map(float, input().split()))\n# Compute and print projection\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Compute the variance of a list of 1D projected values. Read `n` floats (one per line) and print the variance (population variance) rounded to 4 decimal places.

Example:
```
Input:
4
2.0
4.0
4.0
6.0
Output: 2.0000
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Compute population variance and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Given explained variance ratios for each principal component (comma-separated floats on one line), print the minimum number of components needed to explain at least `threshold`% of total variance. The threshold is given on the second line.

Example:
```
Input:
0.5,0.3,0.15,0.05
80
Output: 2
```
MD,
                'starter_code'        => "ratios = list(map(float, input().split(',')))\nthreshold = float(input()) / 100\n# Find minimum components\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Reconstruct a 1D point from its projection: given a projection scalar `p` and a unit vector, compute the reconstructed point as `p * vec` and print each element rounded to 4 decimal places, space-separated.

Example:
```
Input:
5.0
0.6 0.8
Output: 3.0000 4.0000
```
MD,
                'starter_code'        => "p = float(input())\nvec = list(map(float, input().split()))\n# Reconstruct and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: t-SNE & UMAP: Non-Linear Dimensionality Reduction (Q28–Q31)
            // ═══════════════════════════════════════════════════════════════

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Perplexity intuition: given a list of probability values (space-separated floats that sum to 1), compute and print the perplexity = 2^(-sum(p * log2(p))) rounded to 4 decimal places.

Example:
```
Input:  0.25 0.25 0.25 0.25
Output: 4.0000
```
MD,
                'starter_code'        => "import math\nprobs = list(map(float, input().split()))\n# Compute perplexity\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Read `n` 2D embedded points (output of t-SNE, one per line) and their cluster labels (one per line after the points). Print the mean distance from each point to the centroid of its cluster, averaged over all points, rounded to 4 decimal places.

Input: `n`, then `n` lines `x y`, then `n` integer labels.

Example:
```
Input:
4
0.0 0.0
1.0 0.0
10.0 0.0
11.0 0.0
0
0
1
1
Output: 0.5000
```
MD,
                'starter_code'        => "import math\nn = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\nlabels = [int(input()) for _ in range(n)]\n# Compute mean distance to cluster centroid\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Read `n` 2D points. For each point, find and print the index of its nearest neighbor (closest other point, 0-based). If tied, pick the smallest index.

Input: `n`, then `n` lines `x y`.

Example:
```
Input:
3
0.0 0.0
1.0 0.0
10.0 0.0
Output:
1
0
1
```
MD,
                'starter_code'        => "import math\nn = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\n# For each point find nearest neighbor index\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Read `n` 2D points and print the two points (by index, 0-based) that are farthest apart. Print the two indices separated by a space (smaller index first).

Input: `n`, then `n` lines `x y`.

Example:
```
Input:
4
0.0 0.0
5.0 0.0
0.0 3.0
1.0 1.0
Output: 0 1
```
MD,
                'starter_code'        => "import math\nn = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Find farthest pair\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Anomaly Detection: Isolation Forest (Q32–Q36)
            // ═══════════════════════════════════════════════════════════════

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Z-score anomaly detection: read `n` floats (one per line). Compute mean and standard deviation (population). Print `"anomaly"` for points where |z-score| > threshold, else `"normal"`. Threshold is given on the first line.

Input: `threshold` on line 1, `n` on line 2, then `n` floats.

Example:
```
Input:
2.0
5
10.0
12.0
11.0
100.0
10.5
Output:
normal
normal
normal
anomaly
normal
```
MD,
                'starter_code'        => "import math\nthreshold = float(input())\nn = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Compute z-scores and classify\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
IQR anomaly detection: read `n` sorted floats and a multiplier `m`. An outlier is a value below `Q1 - m * IQR` or above `Q3 + m * IQR` where IQR = Q3 - Q1. Q1 = value at 25th percentile index (floor((n-1)*0.25)), Q3 = value at 75th percentile index (floor((n-1)*0.75)). Print `"outlier"` or `"normal"` for each, one per line.

Input: `n m`, then `n` sorted floats.

Example:
```
Input:
6 1.5
1.0
2.0
3.0
4.0
5.0
100.0
Output:
normal
normal
normal
normal
normal
outlier
```
MD,
                'starter_code'        => "import math\nn, m = input().split()\nn, m = int(n), float(m)\nvalues = [float(input()) for _ in range(n)]\n# Compute IQR bounds and classify\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Read `n` anomaly scores (floats, higher = more anomalous). Print the index (0-based) of the most anomalous point. If tied, print the smallest index.

Input: `n` then `n` floats.

Example:
```
Input:
4
0.2
0.9
0.3
0.7
Output: 1
```
MD,
                'starter_code'        => "n = int(input())\nscores = [float(input()) for _ in range(n)]\n# Find index of max score\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Read `n` anomaly scores and a threshold. Print how many points are classified as anomalies (score >= threshold).

Input: `threshold`, then `n`, then `n` floats.

Example:
```
Input:
0.5
5
0.2
0.6
0.4
0.8
0.1
Output: 2
```
MD,
                'starter_code'        => "threshold = float(input())\nn = int(input())\ncount = sum(1 for _ in range(n) if float(input()) >= threshold)\nprint(count)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Isolation score simulation: an isolation score is inversely proportional to path length — shorter path = more anomalous. Read `n` path lengths and print the isolation score for each as `1 / path_length`, rounded to 4 decimal places, one per line.

Input: `n` then `n` floats.

Example:
```
Input:
3
2.0
4.0
1.0
Output:
0.5000
0.2500
1.0000
```
MD,
                'starter_code'        => "n = int(input())\nfor _ in range(n):\n    path = float(input())\n    # Print isolation score\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Autoencoders for Unsupervised Learning (Q37–Q41)
            // ═══════════════════════════════════════════════════════════════

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Reconstruction error: given an original vector and a reconstructed vector (floats, space-separated, one per line), compute and print the Mean Squared Error (MSE) rounded to 4 decimal places.

MSE = mean((original - reconstructed)^2)

Example:
```
Input:
1.0 2.0 3.0
1.1 1.9 3.2
Output: 0.0200
```
MD,
                'starter_code'        => "original = list(map(float, input().split()))\nreconstructed = list(map(float, input().split()))\n# Compute and print MSE\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Read `n` reconstruction errors (floats, one per line). Print the index (0-based) of the sample with the highest reconstruction error (most likely anomaly). If tied, print the smallest index.

Input: `n` then `n` floats.

Example:
```
Input:
4
0.01
0.03
0.95
0.02
Output: 2
```
MD,
                'starter_code'        => "n = int(input())\nerrors = [float(input()) for _ in range(n)]\n# Find index of max error\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Apply ReLU activation: read a vector of floats (space-separated) and print the result of applying ReLU (max(0, x)) to each element, rounded to 4 decimal places, space-separated.

Example:
```
Input:  -1.5 0.0 2.3 -0.7
Output: 0.0000 0.0000 2.3000 0.0000
```
MD,
                'starter_code'        => "values = list(map(float, input().split()))\n# Apply ReLU and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Apply sigmoid activation: read a vector of floats (space-separated) and print sigmoid(x) = 1 / (1 + e^(-x)) for each element, rounded to 4 decimal places, space-separated.

Example:
```
Input:  0.0 1.0 -1.0
Output: 0.5000 0.7311 0.2689
```
MD,
                'starter_code'        => "import math\nvalues = list(map(float, input().split()))\n# Apply sigmoid and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Bottleneck compression ratio: given an input dimension and a bottleneck dimension, compute the compression ratio as `bottleneck / input` and print rounded to 4 decimal places. Also print whether this is `"high"` compression (ratio < 0.5) or `"low"` compression (ratio >= 0.5).

Input: two integers on one line: `input_dim bottleneck_dim`

Example:
```
Input:  128 16
Output:
0.1250
high
```
MD,
                'starter_code'        => "input_dim, bottleneck_dim = map(int, input().split())\n# Compute ratio and classify\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Gaussian Mixture Models & Soft Clustering (Q42–Q46)
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
1D Gaussian probability density: given mean `mu`, standard deviation `sigma`, and a value `x`, compute and print the Gaussian PDF value rounded to 6 decimal places.

PDF = (1 / (sigma * sqrt(2*pi))) * exp(-((x - mu)^2) / (2 * sigma^2))

Input: three floats on one line: `mu sigma x`

Example:
```
Input:  0.0 1.0 0.0
Output: 0.398942
```
MD,
                'starter_code'        => "import math\nmu, sigma, x = map(float, input().split())\n# Compute and print Gaussian PDF\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Soft assignment (E-step): given `k` Gaussian components with equal weights, each defined by `mu` and `sigma`, and a data point `x`, compute the responsibility of each component and print them rounded to 4 decimal places, one per line. Responsibility = p_k / sum(p_i) where p_k = PDF(x | mu_k, sigma_k).

Input: first line `k x`, then `k` lines of `mu sigma`.

Example:
```
Input:
2 0.5
0.0 1.0
5.0 1.0
Output:
0.9933
0.0067
```
MD,
                'starter_code'        => "import math\nk, x = input().split()\nk, x = int(k), float(x)\ncomponents = []\nfor _ in range(k):\n    mu, sigma = map(float, input().split())\n    components.append((mu, sigma))\n# Compute responsibilities\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
M-step mean update: given `n` data points and their soft responsibilities for a single component (floats in [0,1]), compute the updated mean as the weighted average. Print rounded to 4 decimal places.

Input: `n`, then `n` lines of `x responsibility`.

Example:
```
Input:
3
1.0 0.8
3.0 0.2
5.0 0.0
Output: 1.4000
```
MD,
                'starter_code'        => "n = int(input())\nweighted_sum = 0.0\ntotal_weight = 0.0\nfor _ in range(n):\n    x, r = map(float, input().split())\n    # Accumulate weighted sum\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Hard assignment from soft: read `n` data points each with `k` responsibilities (soft cluster assignments). Assign each point to the cluster with the highest responsibility and print the cluster index (0-based), one per line.

Input: first line `n k`, then `n` lines each with `k` floats.

Example:
```
Input:
3 2
0.9 0.1
0.3 0.7
0.5 0.5
Output:
0
1
0
```
MD,
                'starter_code'        => "n, k = map(int, input().split())\nfor _ in range(n):\n    responsibilities = list(map(float, input().split()))\n    # Print index of max responsibility\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Log-likelihood: given `n` data point probability values (one per line, the probability of each point under the model), compute and print the total log-likelihood (sum of natural logs) rounded to 4 decimal places.

Input: `n`, then `n` floats > 0.

Example:
```
Input:
3
0.5
0.3
0.2
Output: -4.0246
```
MD,
                'starter_code'        => "import math\nn = int(input())\ntotal = sum(math.log(float(input())) for _ in range(n))\nprint(f'{total:.4f}')\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: End-to-End Unsupervised ML Pipeline (Q47–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Pipeline step — feature scaling: read `n` floats (one per line) and apply min-max normalization to scale them to [0, 1]. Print each scaled value rounded to 4 decimal places.

Example:
```
Input:
4
0.0
5.0
10.0
2.5
Output:
0.0000
0.5000
1.0000
0.2500
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Apply min-max scaling and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Pipeline step — silhouette score for a single point: given a point's mean intra-cluster distance `a` and mean nearest-cluster distance `b`, compute its silhouette score `s = (b - a) / max(a, b)` and print rounded to 4 decimal places.

Input: two floats on one line: `a b`

Example:
```
Input:  1.0 3.0
Output: 0.6667
```
MD,
                'starter_code'        => "a, b = map(float, input().split())\n# Compute and print silhouette score\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Pipeline step — evaluate clustering purity: given `n` points with a predicted cluster label and a ground-truth label (both integers), compute purity = (number of correctly matched majority class assignments) / n.

For each cluster, find the most common ground-truth label. Purity = sum of max ground-truth counts per cluster / n. Print purity rounded to 4 decimal places.

Input: `n`, then `n` lines of `predicted ground_truth`.

Example:
```
Input:
6
0 0
0 0
0 1
1 1
1 1
1 0
Output: 0.8333
```
MD,
                'starter_code'        => "n = int(input())\nclusters = {}\nfor _ in range(n):\n    pred, truth = map(int, input().split())\n    if pred not in clusters:\n        clusters[pred] = {}\n    clusters[pred][truth] = clusters[pred].get(truth, 0) + 1\n# Compute purity\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Full mini pipeline: read `n` 1D data points, perform min-max scaling, then assign each scaled point to the nearest of 2 given centroids (also in [0,1] scale). Print the cluster assignment (0 or 1) for each point, one per line.

Input: `n`, then `n` floats, then `c1 c2` (the two centroids, space-separated).

Example:
```
Input:
4
0.0
10.0
3.0
7.0
0.2 0.8
Output:
0
1
0
1
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nc1, c2 = map(float, input().split())\n# Scale then assign to nearest centroid\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

        ];

        // ─────────────────────────────────────────────────────────────────
        // Persist questions
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
        // 3. TEST CASES (4 per question)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $seed = function (int $qIndex, array $cases) use ($questions): void {
            $questionId = $questions[$qIndex];
            foreach ($cases as $case) {
                $exists = DB::table('test_cases')->where([
                    'coding_question_id' => $questionId,
                    'order_index'        => $case['order_index'],
                ])->exists();

                if (! $exists) {
                    DB::table('test_cases')->insert([
                        'coding_question_id' => $questionId,
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

        // ── Q1: Count unlabeled ────────────────────────────────────────
        $seed(1, [
            ['input' => "4\nlabeled: cat\nunlabeled\nlabeled: dog\nunlabeled", 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nunlabeled\nunlabeled\nunlabeled", 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nlabeled: 1\nlabeled: 2", 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5\nunlabeled\nlabeled: x\nunlabeled\nlabeled: y\nunlabeled", 'expected_output' => '3', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q2: Centroid ───────────────────────────────────────────────
        $seed(2, [
            ['input' => "3\n1.0 2.0\n3.0 4.0\n5.0 6.0", 'expected_output' => '3.0000 4.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0 0.0\n4.0 4.0", 'expected_output' => '2.0000 2.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1.0 1.0\n2.0 2.0\n3.0 3.0\n4.0 4.0", 'expected_output' => '2.5000 2.5000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n7.5 3.5", 'expected_output' => '7.5000 3.5000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q3: Euclidean distance ─────────────────────────────────────
        $seed(3, [
            ['input' => "0.0 0.0\n3.0 4.0", 'expected_output' => '5.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0 1.0\n1.0 1.0", 'expected_output' => '0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0 0.0\n1.0 0.0", 'expected_output' => '1.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2.0 3.0\n5.0 7.0", 'expected_output' => '5.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q4: Train/test split sizes ─────────────────────────────────
        $seed(4, [
            ['input' => "100\n80", 'expected_output' => "train: 80\ntest: 20", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "50\n70", 'expected_output' => "train: 35\ntest: 15", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "200\n90", 'expected_output' => "train: 180\ntest: 20", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "10\n60", 'expected_output' => "train: 6\ntest: 4", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q5: Distinct cluster count ─────────────────────────────────
        $seed(5, [
            ['input' => "6\n0\n1\n0\n-1\n1\n2", 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n-1\n-1\n-1", 'expected_output' => '0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0\n0\n0\n0\n0", 'expected_output' => '1', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "7\n0\n1\n2\n3\n-1\n4\n4", 'expected_output' => '5', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q6: K-means assignment ─────────────────────────────────────
        $seed(6, [
            ['input' => "2 3\n0.0 0.0\n10.0 10.0\n1.0 1.0\n9.0 9.0\n5.0 5.0", 'expected_output' => "0\n1\n0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n0.0 0.0\n10.0 0.0\n3.0 0.0\n8.0 0.0", 'expected_output' => "0\n1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 3\n0.0 0.0\n5.0 5.0\n10.0 10.0\n1.0 0.0\n5.0 6.0\n9.0 10.0", 'expected_output' => "0\n1\n2", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 4\n0.0 0.0\n100.0 0.0\n1.0 0.0\n2.0 0.0\n99.0 0.0\n98.0 0.0", 'expected_output' => "0\n0\n1\n1", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q7: K-means update centroid ────────────────────────────────
        $seed(7, [
            ['input' => "4\n1.0 2.0 0\n3.0 4.0 1\n5.0 6.0 0\n7.0 8.0 1\n0", 'expected_output' => '3.0000 4.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.0 0.0 0\n2.0 2.0 0\n10.0 10.0 1\n0", 'expected_output' => '1.0000 1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1.0 1.0 1\n3.0 3.0 1\n5.0 5.0 1\n0.0 0.0 0\n1", 'expected_output' => '3.0000 3.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n4.0 8.0 0\n6.0 2.0 0\n0", 'expected_output' => '5.0000 5.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q8: Inertia ────────────────────────────────────────────────
        $seed(8, [
            ['input' => "2 4\n0.0 0.0\n10.0 10.0\n1.0 1.0 0\n-1.0 -1.0 0\n9.0 9.0 1\n11.0 11.0 1", 'expected_output' => '8.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2\n5.0 5.0\n5.0 5.0 0\n5.0 5.0 0", 'expected_output' => '0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n0.0 0.0\n10.0 0.0\n1.0 0.0 0\n9.0 0.0 1", 'expected_output' => '2.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 4\n0.0 0.0\n10.0 0.0\n2.0 0.0 0\n-2.0 0.0 0\n8.0 0.0 1\n12.0 0.0 1", 'expected_output' => '16.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q9: One k-means iteration ──────────────────────────────────
        $seed(9, [
            ['input' => "2 4\n0.0 0.0\n8.0 8.0\n1.0 1.0\n2.0 2.0\n7.0 7.0\n9.0 9.0", 'expected_output' => "1.5000 1.5000\n8.0000 8.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 2\n0.0 0.0\n10.0 0.0\n2.0 0.0\n8.0 0.0", 'expected_output' => "2.0000 0.0000\n8.0000 0.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 4\n0.0 0.0\n100.0 0.0\n1.0 0.0\n3.0 0.0\n99.0 0.0\n97.0 0.0", 'expected_output' => "2.0000 0.0000\n98.0000 0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 3\n5.0 0.0\n1.0 0.0\n5.0 0.0\n9.0 0.0", 'expected_output' => '5.0000 0.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q10: Cluster sizes ─────────────────────────────────────────
        $seed(10, [
            ['input' => "6\n0\n1\n0\n2\n1\n0", 'expected_output' => "cluster 0: 3\ncluster 1: 2\ncluster 2: 1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0\n0\n1\n1", 'expected_output' => "cluster 0: 2\ncluster 1: 2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2\n2\n2", 'expected_output' => 'cluster 2: 3', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5\n0\n1\n2\n0\n1", 'expected_output' => "cluster 0: 2\ncluster 1: 2\ncluster 2: 1", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q11: 1D k-means convergence ────────────────────────────────
        $seed(11, [
            ['input' => "6\n1.0 1.5 2.0 8.0 8.5 9.0\n0.0 10.0", 'expected_output' => "1.5000\n8.5000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1.0 2.0 9.0 10.0\n0.0 11.0", 'expected_output' => "1.5000\n9.5000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n0.0 1.0 2.0 10.0 11.0 12.0\n1.0 11.0", 'expected_output' => "1.0000\n11.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n5.0 5.0\n0.0 10.0", 'expected_output' => "5.0000\n5.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q12: Within-cluster variance ───────────────────────────────
        $seed(12, [
            ['input' => "4\n0.0 0.0 0\n2.0 0.0 0\n10.0 0.0 1\n12.0 0.0 1", 'expected_output' => "cluster 0: 1.0000\ncluster 1: 1.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5.0 5.0 0\n5.0 5.0 0", 'expected_output' => 'cluster 0: 0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n0.0 0.0 0\n4.0 0.0 0\n2.0 0.0 0\n10.0 0.0 1\n14.0 0.0 1\n12.0 0.0 1", 'expected_output' => "cluster 0: 2.6667\ncluster 1: 2.6667", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n1.0 1.0 0\n2.0 2.0 0\n3.0 3.0 0", 'expected_output' => 'cluster 0: 0.6667', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q13: DBSCAN core point check ──────────────────────────────
        $seed(13, [
            ['input' => "5 1.5 3\n1.0\n1.5\n2.0\n5.0\n6.0", 'expected_output' => 'core', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 0.5 3\n1.0\n5.0\n10.0\n15.0", 'expected_output' => 'non-core', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2.0 3\n1.0\n2.0\n3.0", 'expected_output' => 'core', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5 0.1 2\n1.0\n2.0\n3.0\n4.0\n5.0", 'expected_output' => 'non-core', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q14: Count neighbors ───────────────────────────────────────
        $seed(14, [
            ['input' => "5.0 2.0\n4\n3.0\n5.5\n8.0\n6.0", 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0 1.0\n3\n0.5\n1.5\n2.0", 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0 0.5\n3\n9.6\n10.3\n15.0", 'expected_output' => '2', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.0 100.0\n3\n-50.0\n50.0\n0.0", 'expected_output' => '3', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q15: Count noise points ────────────────────────────────────
        $seed(15, [
            ['input' => "5\n0\n-1\n0\n-1\n1", 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0\n1\n2", 'expected_output' => '0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n-1\n-1\n-1\n0", 'expected_output' => '3', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n-1", 'expected_output' => '1', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q16: DBSCAN classify core/border/noise ─────────────────────
        $seed(16, [
            ['input' => "5 1.5 3\n1.0\n1.5\n2.0\n5.0\n6.0", 'expected_output' => "core\ncore\ncore\nnoise\nnoise", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3 1.0 2\n0.0\n0.8\n5.0", 'expected_output' => "core\ncore\nnoise", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4 1.5 3\n1.0\n2.0\n1.5\n10.0", 'expected_output' => "core\ncore\ncore\nnoise", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5 0.5 4\n1.0\n1.3\n1.6\n1.9\n10.0", 'expected_output' => "core\ncore\ncore\ncore\nnoise", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q17: Most frequent cluster ─────────────────────────────────
        $seed(17, [
            ['input' => "7\n0\n1\n0\n-1\n1\n0\n2", 'expected_output' => '0', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n1\n0\n0", 'expected_output' => '0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n-1\n2\n2\n2\n1", 'expected_output' => '2', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n-1\n-1\n0", 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q18: Single linkage distance ───────────────────────────────
        $seed(18, [
            ['input' => "2 2\n1.0\n3.0\n7.0\n9.0", 'expected_output' => '4.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1\n0.0\n5.0", 'expected_output' => '5.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3\n1.0\n2.0\n3.0\n4.0\n5.0", 'expected_output' => '1.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3 2\n10.0\n20.0\n30.0\n35.0\n40.0", 'expected_output' => '5.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q19: Complete linkage distance ─────────────────────────────
        $seed(19, [
            ['input' => "2 2\n1.0\n3.0\n7.0\n9.0", 'expected_output' => '8.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1\n0.0\n5.0", 'expected_output' => '5.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3\n1.0\n2.0\n3.0\n4.0\n5.0", 'expected_output' => '4.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3 2\n10.0\n20.0\n30.0\n35.0\n40.0", 'expected_output' => '30.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q20: Average linkage distance ──────────────────────────────
        $seed(20, [
            ['input' => "2 2\n1.0\n3.0\n7.0\n9.0", 'expected_output' => '6.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1\n0.0\n5.0", 'expected_output' => '5.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2\n0.0\n4.0\n6.0\n10.0", 'expected_output' => '6.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1 3\n0.0\n2.0\n4.0\n6.0", 'expected_output' => '4.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q21: Agglomerative first merge pair ────────────────────────
        $seed(21, [
            ['input' => "4\n1.0\n3.0\n2.0\n8.0", 'expected_output' => '0 2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.0\n10.0\n11.0", 'expected_output' => '1 2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n5.0\n5.5\n10.0\n15.0", 'expected_output' => '0 1', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n1.0\n4.0\n2.0", 'expected_output' => '0 2', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q22: Merges at or below threshold ──────────────────────────
        $seed(22, [
            ['input' => "5.0\n4\n1.0\n3.0\n5.0\n8.0", 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2.0\n3\n1.0\n3.0\n5.0", 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0\n4\n2.0\n4.0\n6.0\n8.0", 'expected_output' => '4', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.5\n3\n1.0\n2.0\n3.0", 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q23: Mean-center dataset ───────────────────────────────────
        $seed(23, [
            ['input' => "3\n1.0 2.0\n3.0 4.0\n5.0 6.0", 'expected_output' => "-2.0000 -2.0000\n0.0000 0.0000\n2.0000 2.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0 0.0\n4.0 4.0", 'expected_output' => "-2.0000 -2.0000\n2.0000 2.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5.0 3.0", 'expected_output' => '0.0000 0.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n1.0 0.0\n2.0 0.0\n3.0 0.0\n4.0 0.0", 'expected_output' => "-1.5000 0.0000\n-0.5000 0.0000\n0.5000 0.0000\n1.5000 0.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q24: Project point onto PC ─────────────────────────────────
        $seed(24, [
            ['input' => "3.0 4.0\n0.6 0.8", 'expected_output' => '5.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0 0.0\n1.0 0.0", 'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0 1.0\n0.7071 0.7071", 'expected_output' => '1.4142', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.0 5.0\n0.0 1.0", 'expected_output' => '5.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q25: Population variance ───────────────────────────────────
        $seed(25, [
            ['input' => "4\n2.0\n4.0\n4.0\n6.0", 'expected_output' => '2.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1.0\n1.0\n1.0", 'expected_output' => '0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n2.0\n4.0\n4.0\n4.0\n6.0", 'expected_output' => '1.6000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0.0\n10.0", 'expected_output' => '25.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q26: Min components for variance ──────────────────────────
        $seed(26, [
            ['input' => "0.5,0.3,0.15,0.05\n80", 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.9,0.08,0.02\n95", 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.4,0.3,0.2,0.1\n100", 'expected_output' => '4', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.6,0.3,0.1\n60", 'expected_output' => '1', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q27: Reconstruct from projection ──────────────────────────
        $seed(27, [
            ['input' => "5.0\n0.6 0.8", 'expected_output' => '3.0000 4.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3.0\n1.0 0.0", 'expected_output' => '3.0000 0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n0.0 1.0", 'expected_output' => '0.0000 2.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.0\n0.7071 0.7071", 'expected_output' => '0.0000 0.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q28: Perplexity ────────────────────────────────────────────
        $seed(28, [
            ['input' => '0.25 0.25 0.25 0.25', 'expected_output' => '4.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '1.0', 'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.5 0.5', 'expected_output' => '2.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => '0.1 0.1 0.1 0.1 0.1 0.1 0.1 0.1 0.1 0.1', 'expected_output' => '10.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q29: Mean distance to cluster centroid ─────────────────────
        $seed(29, [
            ['input' => "4\n0.0 0.0\n1.0 0.0\n10.0 0.0\n11.0 0.0\n0\n0\n1\n1", 'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0 0.0\n4.0 0.0\n0\n0", 'expected_output' => '2.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1.0 0.0\n3.0 0.0\n5.0 0.0\n7.0 0.0\n0\n0\n1\n1", 'expected_output' => '1.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n5.0 5.0\n5.0 5.0\n0\n0", 'expected_output' => '0.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q30: Nearest neighbor index ────────────────────────────────
        $seed(30, [
            ['input' => "3\n0.0 0.0\n1.0 0.0\n10.0 0.0", 'expected_output' => "1\n0\n1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0 0.0\n5.0 0.0", 'expected_output' => "1\n0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.0 0.0\n3.0 4.0\n6.0 8.0", 'expected_output' => "1\n0\n1", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n0.0 0.0\n1.0 0.0\n2.0 0.0\n10.0 0.0", 'expected_output' => "1\n0\n1\n2", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q31: Farthest pair ─────────────────────────────────────────
        $seed(31, [
            ['input' => "4\n0.0 0.0\n5.0 0.0\n0.0 3.0\n1.0 1.0", 'expected_output' => '0 1', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0 0.0\n10.0 0.0", 'expected_output' => '0 1', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.0 0.0\n3.0 4.0\n1.0 1.0", 'expected_output' => '0 1', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n1.0 0.0\n2.0 0.0\n0.0 0.0", 'expected_output' => '0 2', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q32: Z-score anomaly ───────────────────────────────────────
        $seed(32, [
            ['input' => "2.0\n5\n10.0\n12.0\n11.0\n100.0\n10.5", 'expected_output' => "normal\nnormal\nnormal\nanomaly\nnormal", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.0\n3\n0.0\n0.0\n100.0", 'expected_output' => "normal\nnormal\nanomaly", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2.0\n4\n1.0\n2.0\n3.0\n4.0", 'expected_output' => "normal\nnormal\nnormal\nnormal", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0.5\n3\n5.0\n5.1\n10.0", 'expected_output' => "normal\nnormal\nanomaly", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q33: IQR outlier detection ─────────────────────────────────
        $seed(33, [
            ['input' => "6 1.5\n1.0\n2.0\n3.0\n4.0\n5.0\n100.0", 'expected_output' => "normal\nnormal\nnormal\nnormal\nnormal\noutlier", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4 1.5\n1.0\n2.0\n3.0\n4.0", 'expected_output' => "normal\nnormal\nnormal\nnormal", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5 1.5\n1.0\n2.0\n3.0\n4.0\n50.0", 'expected_output' => "normal\nnormal\nnormal\nnormal\noutlier", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4 1.5\n-100.0\n1.0\n2.0\n3.0", 'expected_output' => "outlier\nnormal\nnormal\nnormal", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q34: Most anomalous index ──────────────────────────────────
        $seed(34, [
            ['input' => "4\n0.2\n0.9\n0.3\n0.7", 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.1\n0.1\n0.1", 'expected_output' => '0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0.5\n0.4\n0.99\n0.3\n0.6", 'expected_output' => '2', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0.7\n0.7", 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q35: Count anomalies by threshold ─────────────────────────
        $seed(35, [
            ['input' => "0.5\n5\n0.2\n0.6\n0.4\n0.8\n0.1", 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.9\n3\n0.5\n0.95\n0.1", 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n3\n0.1\n0.2\n0.3", 'expected_output' => '3', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0\n3\n0.5\n0.9\n0.99", 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q36: Isolation scores ──────────────────────────────────────
        $seed(36, [
            ['input' => "3\n2.0\n4.0\n1.0", 'expected_output' => "0.5000\n0.2500\n1.0000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5.0\n2.0", 'expected_output' => "0.2000\n0.5000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1.0", 'expected_output' => '1.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n10.0\n5.0\n2.0\n1.0", 'expected_output' => "0.1000\n0.2000\n0.5000\n1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q37: Reconstruction MSE ────────────────────────────────────
        $seed(37, [
            ['input' => "1.0 2.0 3.0\n1.1 1.9 3.2", 'expected_output' => '0.0200', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0 0.0\n0.0 0.0", 'expected_output' => '0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0 0.0\n0.0 1.0", 'expected_output' => '1.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5.0 5.0 5.0\n4.0 6.0 5.0", 'expected_output' => '0.6667', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q38: Max reconstruction error index ────────────────────────
        $seed(38, [
            ['input' => "4\n0.01\n0.03\n0.95\n0.02", 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.1\n0.2\n0.3", 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0.5\n0.5", 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "5\n0.1\n0.9\n0.2\n0.8\n0.3", 'expected_output' => '1', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q39: ReLU activation ───────────────────────────────────────
        $seed(39, [
            ['input' => '-1.5 0.0 2.3 -0.7', 'expected_output' => '0.0000 0.0000 2.3000 0.0000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '1.0 2.0 3.0', 'expected_output' => '1.0000 2.0000 3.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '-5.0 -3.0 0.0 3.0 5.0', 'expected_output' => '0.0000 0.0000 0.0000 3.0000 5.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => '0.0', 'expected_output' => '0.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q40: Sigmoid activation ────────────────────────────────────
        $seed(40, [
            ['input' => '0.0 1.0 -1.0', 'expected_output' => '0.5000 0.7311 0.2689', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0', 'expected_output' => '0.5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '2.0 -2.0', 'expected_output' => '0.8808 0.1192', 'is_hidden' => true, 'order_index' => 3],
            ['input' => '-10.0 10.0', 'expected_output' => '0.0000 1.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q41: Compression ratio ─────────────────────────────────────
        $seed(41, [
            ['input' => '128 16', 'expected_output' => "0.1250\nhigh", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '100 60', 'expected_output' => "0.6000\nlow", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '256 32', 'expected_output' => "0.1250\nhigh", 'is_hidden' => true, 'order_index' => 3],
            ['input' => '10 5', 'expected_output' => "0.5000\nlow", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q42: Gaussian PDF ──────────────────────────────────────────
        $seed(42, [
            ['input' => '0.0 1.0 0.0', 'expected_output' => '0.398942', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '0.0 1.0 1.0', 'expected_output' => '0.241971', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '5.0 2.0 5.0', 'expected_output' => '0.199471', 'is_hidden' => true, 'order_index' => 3],
            ['input' => '0.0 0.5 0.0', 'expected_output' => '0.797885', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q43: GMM responsibilities ──────────────────────────────────
        $seed(43, [
            ['input' => "2 0.5\n0.0 1.0\n5.0 1.0", 'expected_output' => "0.9933\n0.0067", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 0.0\n0.0 1.0\n0.0 1.0", 'expected_output' => "0.5000\n0.5000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 2.5\n0.0 1.0\n5.0 1.0", 'expected_output' => "0.5000\n0.5000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2 10.0\n0.0 1.0\n5.0 1.0", 'expected_output' => "0.0000\n1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q44: M-step weighted mean ──────────────────────────────────
        $seed(44, [
            ['input' => "3\n1.0 0.8\n3.0 0.2\n5.0 0.0", 'expected_output' => '1.4000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0 0.5\n4.0 0.5", 'expected_output' => '2.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1.0 1.0\n2.0 1.0\n3.0 1.0", 'expected_output' => '2.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n0.0 0.9\n10.0 0.1", 'expected_output' => '1.0000', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q45: Hard from soft assignment ────────────────────────────
        $seed(45, [
            ['input' => "3 2\n0.9 0.1\n0.3 0.7\n0.5 0.5", 'expected_output' => "0\n1\n0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2 3\n0.1 0.5 0.4\n0.7 0.2 0.1", 'expected_output' => "1\n0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2\n0.4 0.6", 'expected_output' => '1', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4 2\n1.0 0.0\n0.0 1.0\n0.8 0.2\n0.3 0.7", 'expected_output' => "0\n1\n0\n1", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q46: Log-likelihood ────────────────────────────────────────
        $seed(46, [
            ['input' => "3\n0.5\n0.3\n0.2", 'expected_output' => '-4.0246', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1.0\n1.0", 'expected_output' => '0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0.1\n0.2\n0.3", 'expected_output' => '-7.0192', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n0.5", 'expected_output' => '-0.6931', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q47: Min-max scaling ───────────────────────────────────────
        $seed(47, [
            ['input' => "4\n0.0\n5.0\n10.0\n2.5", 'expected_output' => "0.0000\n0.5000\n1.0000\n0.2500", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0.0\n50.0\n100.0", 'expected_output' => "0.0000\n0.5000\n1.0000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5.0\n5.0\n5.0", 'expected_output' => "0.0000\n0.0000\n0.0000", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "4\n2.0\n4.0\n6.0\n8.0", 'expected_output' => "0.0000\n0.3333\n0.6667\n1.0000", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q48: Silhouette score ──────────────────────────────────────
        $seed(48, [
            ['input' => '1.0 3.0', 'expected_output' => '0.6667', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2.0 2.0', 'expected_output' => '0.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.5 4.0', 'expected_output' => '0.8750', 'is_hidden' => true, 'order_index' => 3],
            ['input' => '3.0 1.0', 'expected_output' => '-0.6667', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q49: Clustering purity ─────────────────────────────────────
        $seed(49, [
            ['input' => "6\n0 0\n0 0\n0 1\n1 1\n1 1\n1 0", 'expected_output' => '0.8333', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n0 0\n0 0\n1 1\n1 1", 'expected_output' => '1.0000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0 1\n0 1\n1 0\n1 0", 'expected_output' => '1.0000', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "6\n0 0\n0 1\n0 2\n1 0\n1 1\n1 2", 'expected_output' => '0.3333', 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q50: Full mini pipeline ────────────────────────────────────
        $seed(50, [
            ['input' => "4\n0.0\n10.0\n3.0\n7.0\n0.2 0.8", 'expected_output' => "0\n1\n0\n1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0.0\n100.0\n0.3 0.7", 'expected_output' => "0\n1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0.0\n4.0\n8.0\n12.0\n0.4 0.6", 'expected_output' => "0\n0\n1\n1", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\n5.0\n5.0\n5.0\n0.3 0.7", 'expected_output' => "0\n0\n0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        $this->command->info('✅ Module 21 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}