<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 15 — Data Visualization (Newbie) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering beginner data visualization in Python
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mirrors Module 15 curriculum):
 *   15.1  Introduction to Data Visualization
 *   15.2  Matplotlib: Line & Bar Charts
 *   15.3  Histograms, Box Plots & Distributions
 *   15.4  Scatter Plots & Correlation Analysis
 *   15.5  Heatmaps & Correlation Matrices
 *   15.6  Pair Plots & FacetGrids
 *   15.7  Pie, Donut & Part-to-Whole Charts
 *   15.8  Subplots, Layouts & Customization
 *   15.9  Interactive Charts with Plotly
 *   15.10 Best Practices, Color & Storytelling
 *
 * Difficulty: Newbie — chart data computations, formatting, and descriptive
 *             statistics using only Python built-ins and the math module.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module15CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 15 — Data Visualization (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Data Visualization',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Compute and format the data behind common charts and visualizations. Practice preparing datasets for line charts, bar charts, histograms, scatter plots, heatmaps, pie charts, and more using only Python built-ins and the math module.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 500,
                'order_index'        => 15,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 15.1: Introduction to Data Visualization (Q1–Q4)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Identify the best chart type for a given data scenario.

Rules:
- `trend over time` → `line chart`
- `compare categories` → `bar chart`
- `show distribution` → `histogram`
- `show relationship` → `scatter plot`
- `show proportions` → `pie chart`

Read a scenario string (one line). Print the recommended chart type.

Example:
```
Input: trend over time
Output: line chart
```
MD,
                'starter_code'        => "scenario = input()\n# Map scenario to chart type and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Determine if a dataset is better suited for a **categorical** or **continuous** x-axis.

Rules:
- If all x-values can be converted to float → `continuous`
- Otherwise → `categorical`

Read `n`, then `n` x-values (one per line). Print `continuous` or `categorical`.

Example:
```
Input:
3
Jan
Feb
Mar
Output: categorical
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [input() for _ in range(n)]\n# Determine axis type\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Compute the **data range** for setting chart axis limits.

Read `n` numbers (one per line). Print `Min: <min>` and `Max: <max>`.

Example:
```
Input:
5
10
40
20
30
50
Output:
Min: 10.0
Max: 50.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Print min and max\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Count how many data points fall into a given y-range for chart annotation.

Read `n` numbers, then `low` and `high` (floats). Print the count of values where `low <= value <= high`.

Example:
```
Input:
6
10
20
30
40
50
60
20
40
Output: 3
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nlow = float(input())\nhigh = float(input())\n# Count and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 15.2: Matplotlib: Line & Bar Charts (Q5–Q9)
            // ═══════════════════════════════════════════════════════════════

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Compute **period-over-period change** (used to annotate line charts).

Change = current − previous

Read `n` values (one per line). Print the change for each period starting from the second value, rounded to 2 decimal places, one per line.

Example:
```
Input:
5
100
120
110
130
125
Output:
20.0
-10.0
20.0
-5.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Print period changes\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Compute **percentage change** between consecutive values (useful for line chart annotations).

% Change = ((current − previous) / previous) × 100

Read `n` values (one per line). Print each percentage change rounded to 2 decimal places, one per line.

Example:
```
Input:
3
100
120
90
Output:
20.0
-25.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Print % changes\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Prepare **bar chart data** by sorting categories by value descending.

Read `n` pairs of `category value` (one per line). Print each `category: value` sorted by value descending. On ties, sort alphabetically.

Example:
```
Input:
3
apples 50
bananas 80
cherries 30
Output:
bananas: 80
apples: 50
cherries: 30
```
MD,
                'starter_code'        => "n = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    data.append((parts[0], float(parts[1])))\n# Sort and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Compute the **cumulative sum** of values (used in cumulative line charts).

Read `n` values (one per line). Print the running cumulative total at each step, one per line.

Example:
```
Input:
4
10
20
15
25
Output:
10
30
45
70
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Print cumulative sums\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Compute **grouped bar chart totals** for two groups.

Read `n` category names, then `n` values for Group A, then `n` values for Group B (each on its own line). For each category, print `<category>: A=<a> B=<b> Total=<total>`.

Example:
```
Input:
2
Sales
Marketing
100
80
60
40
Output:
Sales: A=100.0 B=60.0 Total=160.0
Marketing: A=80.0 B=40.0 Total=120.0
```
MD,
                'starter_code'        => "n = int(input())\ncategories = [input() for _ in range(n)]\na_vals = [float(input()) for _ in range(n)]\nb_vals = [float(input()) for _ in range(n)]\n# Print grouped totals\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 15.3: Histograms, Box Plots & Distributions (Q10–Q14)
            // ═══════════════════════════════════════════════════════════════

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Create **histogram bin counts** from a dataset.

Given `n` values and `k` equal-width bins between `low` and `high`, count how many values fall in each bin.

Bin edges: low, low + width, low + 2·width, ..., high (where width = (high − low) / k)

A value belongs to bin i if: low + i·width <= value < low + (i+1)·width
The last bin also includes `high`.

Read `n`, then `n` values, then `k`, `low`, `high`. Print each bin count on its own line.

Example:
```
Input:
6
1
2
3
4
5
6
3
1
6
Output:
2
2
2
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nk = int(input())\nlow = float(input())\nhigh = float(input())\n# Compute and print bin counts\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Compute **box plot statistics**: minimum, Q1, median, Q3, maximum.

Use the exclusive method for Q1 and Q3 (median of lower/upper half excluding the median).

Read `n` numbers (one per line). Print:
```
Min: <min>
Q1: <q1>
Median: <median>
Q3: <q3>
Max: <max>
```
All values rounded to 2 decimal places.

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
Min: 1.0
Q1: 1.5
Median: 3.0
Q3: 4.5
Max: 5.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = sorted([float(input()) for _ in range(n)])\n# Compute and print box plot stats\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Detect **box plot outliers** using the 1.5×IQR rule.

A value is an outlier if it is below Q1 − 1.5×IQR or above Q3 + 1.5×IQR.

Read `n` numbers (one per line). Print outliers sorted ascending, one per line. If none, print `None`.

Example:
```
Input:
7
1
2
3
4
5
6
20
Output: 20.0
```
MD,
                'starter_code'        => "n = int(input())\nnums = sorted([float(input()) for _ in range(n)])\n# Detect and print outliers\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Classify the **skewness** of a distribution using mean and median.

Rules:
- If mean > median → `right skewed`
- If mean < median → `left skewed`
- If mean == median → `symmetric`

Read `n` numbers (one per line). Print the skewness classification.

Example:
```
Input:
5
1
2
3
4
100
Output: right skewed
```
MD,
                'starter_code'        => "n = int(input())\nnums = [float(input()) for _ in range(n)]\n# Compute and classify skewness\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Compute the **frequency density** for each histogram bin (used on the y-axis of density histograms).

Frequency density = count / (total × bin_width)

Read `k` bin counts (one per line), then `total` (total number of values), then `bin_width`. Print each frequency density rounded to 4 decimal places, one per line.

Example:
```
Input:
3
5
10
5
20
2
Output:
0.125
0.25
0.125
```
MD,
                'starter_code'        => "k = int(input())\ncounts = [int(input()) for _ in range(k)]\ntotal = int(input())\nbin_width = float(input())\n# Compute and print frequency densities\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 15.4: Scatter Plots & Correlation Analysis (Q15–Q19)
            // ═══════════════════════════════════════════════════════════════

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Compute the **Pearson correlation coefficient** between two variables.

r = (n·Σxy − Σx·Σy) / sqrt((n·Σx² − (Σx)²) × (n·Σy² − (Σy)²))

Read `n`, then `n` pairs of `x y` values (space-separated). Print `r` rounded to 4 decimal places.

Example:
```
Input:
3
1 2
2 4
3 6
Output: 1.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Compute and print Pearson r\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Classify the **correlation strength** from a Pearson r value.

Rules (use absolute value of r):
- |r| >= 0.8 → `strong`
- |r| >= 0.5 → `moderate`
- Otherwise → `weak`

Also classify direction: if r > 0 → `positive`, if r < 0 → `negative`, if r == 0 → `none`.

Read `r`. Print `<strength> <direction>`.

Example:
```
Input: -0.9
Output: strong negative
```
MD,
                'starter_code'        => "r = float(input())\n# Classify and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Compute the **trend line** (linear regression) for a scatter plot.

Read `n`, then `n` pairs of `x y` values. Print `y = <slope>x + <intercept>` with slope and intercept rounded to 4 decimal places.

Example:
```
Input:
3
1 2
2 4
3 6
Output: y = 2.0x + 0.0
```
MD,
                'starter_code'        => "n = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Compute and print trend line\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Find the **furthest point from the trend line** in a scatter plot (the point with the largest absolute residual).

Read `n`, then `n` pairs of `x y`. Compute the regression line, then find the x value with the maximum absolute residual. Print `x=<x> residual=<residual>` rounded to 4 decimal places.

Example:
```
Input:
4
1 2
2 4
3 6
4 10
Output: x=4.0 residual=2.2
```
MD,
                'starter_code'        => "n = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Find and print the point with the largest residual\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Count the number of points in each **quadrant** of a scatter plot centered at the origin.

Quadrant rules (strict — points on axes are counted in `Q1` if x>=0 and y>=0, etc.):
- Q1: x >= 0, y >= 0
- Q2: x < 0, y >= 0
- Q3: x < 0, y < 0
- Q4: x >= 0, y < 0

Read `n`, then `n` pairs of `x y`. Print `Q1: <c1>`, `Q2: <c2>`, `Q3: <c3>`, `Q4: <c4>`.

Example:
```
Input:
4
1 2
-1 3
-2 -1
3 -4
Output:
Q1: 1
Q2: 1
Q3: 1
Q4: 1
```
MD,
                'starter_code'        => "n = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Count quadrant membership and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 15.5: Heatmaps & Correlation Matrices (Q20–Q24)
            // ═══════════════════════════════════════════════════════════════

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Build a **correlation matrix** for two variables.

The matrix has entries: corr(X,X)=1, corr(Y,Y)=1, corr(X,Y)=corr(Y,X)=r.

Read `n`, then `n` pairs of `x y`. Compute the Pearson r between X and Y. Print the 2×2 matrix row by row, values space-separated, rounded to 4 decimal places.

Example:
```
Input:
3
1 2
2 4
3 6
Output:
1.0 1.0
1.0 1.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Compute and print correlation matrix\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Find the **highest correlated pair** from a list of variable-pair correlations.

Read `m` lines of `var1 var2 r` (space-separated). Print the pair with the highest absolute r. If tied, print the one that appears first.

Example:
```
Input:
3
A B 0.9
C D -0.95
E F 0.7
Output: C D -0.95
```
MD,
                'starter_code'        => "m = int(input())\npairs = []\nfor _ in range(m):\n    parts = input().split()\n    pairs.append((parts[0], parts[1], float(parts[2])))\n# Find and print highest correlated pair\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Normalize values to **heatmap color scale** [0, 1] using min-max normalization.

Read `n` correlation values (floats). Print each normalized to [0, 1] rounded to 4 decimal places.

Example:
```
Input:
4
-1.0
-0.5
0.5
1.0
Output:
0.0
0.25
0.75
1.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Normalize and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Count the number of **strong correlations** in a list of r values.

A correlation is strong if |r| >= 0.7 (excluding self-correlations where r == 1.0).

Read `n` r values (one per line). Print the count of strong correlations.

Example:
```
Input:
5
1.0
0.85
-0.75
0.3
-0.4
Output: 2
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Count and print strong correlations\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Compute the **covariance** between two variables for a heatmap.

Cov(X, Y) = Σ((xi − mean_x)(yi − mean_y)) / n  (population covariance)

Read `n`, then `n` pairs of `x y`. Print the covariance rounded to 4 decimal places.

Example:
```
Input:
3
1 2
2 4
3 6
Output: 1.3333
```
MD,
                'starter_code'        => "n = int(input())\npoints = [list(map(float, input().split())) for _ in range(n)]\n# Compute and print covariance\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 15.6: Pair Plots & FacetGrids (Q25–Q28)
            // ═══════════════════════════════════════════════════════════════

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Count the number of **subplot panels** needed for a pair plot.

A pair plot with `k` variables produces a k×k grid of subplots.

Read `k`. Print `Subplots: <k*k>`.

Example:
```
Input: 4
Output: Subplots: 16
```
MD,
                'starter_code'        => "k = int(input())\n# Compute and print subplot count\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
List all **unique variable pairs** for a pair plot (off-diagonal scatter plots).

Given `k` variable names (one per line), list all unique pairs `(A, B)` where A comes before B alphabetically. Print each pair as `<A> vs <B>`, one per line.

Example:
```
Input:
3
height
weight
age
Output:
age vs height
age vs weight
height vs weight
```
MD,
                'starter_code'        => "k = int(input())\nvariables = sorted([input() for _ in range(k)])\n# List and print unique pairs\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Split a dataset into **groups for a FacetGrid**.

Read `n` data rows, each as `label value` (space-separated). Group the values by label and print each group sorted alphabetically by label:

```
<label>: <space-separated values>
```

Example:
```
Input:
6
A 10
B 20
A 30
B 40
A 50
B 60
Output:
A: 10.0 30.0 50.0
B: 20.0 40.0 60.0
```
MD,
                'starter_code'        => "n = int(input())\nrows = []\nfor _ in range(n):\n    parts = input().split()\n    rows.append((parts[0], float(parts[1])))\n# Group and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Compute **per-group statistics** for a FacetGrid panel.

Read `n` rows of `group value`. For each group (sorted alphabetically), print `<group>: mean=<mean> std=<std>` with values rounded to 4 decimal places. Use population std.

Example:
```
Input:
4
A 10
A 20
B 30
B 40
Output:
A: mean=15.0 std=5.0
B: mean=35.0 std=5.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nrows = []\nfor _ in range(n):\n    parts = input().split()\n    rows.append((parts[0], float(parts[1])))\n# Compute and print group stats\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 15.7: Pie, Donut & Part-to-Whole Charts (Q29–Q33)
            // ═══════════════════════════════════════════════════════════════

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Compute the **percentage** of each category for a pie chart.

Read `n` pairs of `category value` (one per line). Print each `<category>: <pct>%` sorted by percentage descending, rounded to 2 decimal places.

Example:
```
Input:
3
cats 50
dogs 30
fish 20
Output:
cats: 50.0%
dogs: 30.0%
fish: 20.0%
```
MD,
                'starter_code'        => "n = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    data.append((parts[0], float(parts[1])))\n# Compute and print percentages\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Compute **slice angles** (in degrees) for a pie chart.

Angle = (value / total) × 360

Read `n` values (one per line). Print each angle rounded to 2 decimal places, one per line.

Example:
```
Input:
4
25
25
25
25
Output:
90.0
90.0
90.0
90.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Compute and print angles\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Compute the **"other" category** for a pie chart when small slices are grouped.

Categories with less than `threshold`% of the total are merged into an `Other` category.

Read `n` pairs of `category value`, then `threshold` (float percentage). Print the remaining categories plus `Other: <combined_value>` (if any were grouped), sorted by value descending. Values as floats.

Example:
```
Input:
4
A 50
B 30
C 5
D 3
10
Output:
A: 50.0
B: 30.0
Other: 8.0
```
MD,
                'starter_code'        => "n = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    data.append((parts[0], float(parts[1])))\nthreshold = float(input())\n# Group small slices and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Compute the **cumulative percentage** for a Pareto (part-to-whole) chart.

Read `n` pairs of `category value` (one per line). Sort by value descending. Print each `<category>: <cumulative_pct>%`, rounded to 2 decimal places.

Example:
```
Input:
3
A 60
B 30
C 10
Output:
A: 60.0%
B: 90.0%
C: 100.0%
```
MD,
                'starter_code'        => "n = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    data.append((parts[0], float(parts[1])))\n# Sort, compute cumulative %, and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Determine the **Pareto 80/20 cutoff**: which categories account for the first 80% of the total.

Read `n` pairs of `category value` (sorted descending by value, one per line). Print the category names that together first reach >= 80% of the total, one per line.

Example:
```
Input:
5
A 50
B 30
C 10
D 7
E 3
Output:
A
B
```
MD,
                'starter_code'        => "n = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    data.append((parts[0], float(parts[1])))\n# Find and print 80% cutoff categories\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 15.8: Subplots, Layouts & Customization (Q34–Q38)
            // ═══════════════════════════════════════════════════════════════

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Compute the **grid dimensions** for `n` subplots arranged in rows with a maximum of `cols_max` columns per row.

Rows = ceil(n / cols_max)
Cols = min(n, cols_max)

Read `n` and `cols_max`. Print `Rows: <rows>` and `Cols: <cols>`.

Example:
```
Input:
7
3
Output:
Rows: 3
Cols: 3
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\ncols_max = int(input())\n# Compute and print grid dimensions\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Identify the **subplot position** (row, col) for subplot index `i` (0-based) in a grid with `cols` columns.

Row = i // cols  (0-based)
Col = i % cols   (0-based)

Read `i` and `cols`. Print `Row: <row>` and `Col: <col>`.

Example:
```
Input:
5
3
Output:
Row: 1
Col: 2
```
MD,
                'starter_code'        => "i = int(input())\ncols = int(input())\n# Compute and print position\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Compute the **figure size** in inches for a grid of subplots.

Given `rows`, `cols`, and per-subplot dimensions `w` (width) and `h` (height), the total figure size is `rows*h` tall and `cols*w` wide.

Read `rows`, `cols`, `w`, `h`. Print `Width: <total_w>` and `Height: <total_h>`.

Example:
```
Input:
2
3
4
3
Output:
Width: 12
Height: 6
```
MD,
                'starter_code'        => "rows = int(input())\ncols = int(input())\nw = int(input())\nh = int(input())\n# Compute and print figure size\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Apply a **custom tick formatter** that appends a unit suffix to numeric tick labels.

Read `n` tick values (floats, one per line), then a `suffix` string. Print each formatted label as `<value><suffix>`.

Example:
```
Input:
4
0
25
50
75
%
Output:
0.0%
25.0%
50.0%
75.0%
```
MD,
                'starter_code'        => "n = int(input())\nticks = [float(input()) for _ in range(n)]\nsuffix = input()\n# Format and print tick labels\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Generate **evenly spaced tick values** between a min and max value.

Read `min_val`, `max_val`, and `n_ticks`. Print `n_ticks` evenly spaced values from min to max (inclusive), each rounded to 4 decimal places, one per line.

Example:
```
Input:
0
100
5
Output:
0.0
25.0
50.0
75.0
100.0
```
MD,
                'starter_code'        => "min_val = float(input())\nmax_val = float(input())\nn_ticks = int(input())\n# Generate and print tick values\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 15.9: Interactive Charts with Plotly (Q39–Q43)
            // ═══════════════════════════════════════════════════════════════

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Prepare a **hover tooltip** string for a data point.

Read `label` (string), `x` (float), `y` (float). Format and print:
`<label>: x=<x>, y=<y>`

Values rounded to 2 decimal places.

Example:
```
Input:
Sales
3.5
120.75
Output: Sales: x=3.5, y=120.75
```
MD,
                'starter_code'        => "label = input()\nx = float(input())\ny = float(input())\n# Format and print tooltip\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Generate **hover tooltip strings** for multiple data points.

Read `n`, then `n` lines of `label x y`. Print the formatted tooltip for each on its own line:
`<label>: x=<x>, y=<y>` (values rounded to 2 decimal places).

Example:
```
Input:
3
Alpha 1 10
Beta 2 20
Gamma 3 30
Output:
Alpha: x=1.0, y=10.0
Beta: x=2.0, y=20.0
Gamma: x=3.0, y=30.0
```
MD,
                'starter_code'        => "n = int(input())\npoints = []\nfor _ in range(n):\n    parts = input().split()\n    points.append((parts[0], float(parts[1]), float(parts[2])))\n# Print tooltips\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Compute a **zoom window** for an interactive chart.

Given data values and a zoom factor `z` (e.g., 0.5 means show the central 50%), compute the visible range centered on the mean.

- Center = mean of all values
- Half-range = (max − min) / 2 × z
- Visible: [center − half_range, center + half_range]

Read `n` values then `z`. Print `Low: <low>` and `High: <high>`, rounded to 4 decimal places.

Example:
```
Input:
4
0
10
20
30
0.5
Output:
Low: 7.5
High: 22.5
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nz = float(input())\n# Compute and print zoom window\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Compute the **click-selected point summary** for an interactive chart.

Given a query x-value, find the data point with the closest x-value (absolute difference). Print `Nearest: <label> at x=<x>, y=<y>`.

Read `n` data points (`label x y` per line), then `query_x`. Print the nearest data point.

Example:
```
Input:
3
A 1 10
B 5 20
C 9 30
4
Output: Nearest: B at x=5.0, y=20.0
```
MD,
                'starter_code'        => "n = int(input())\npoints = []\nfor _ in range(n):\n    parts = input().split()\n    points.append((parts[0], float(parts[1]), float(parts[2])))\nquery = float(input())\n# Find and print nearest point\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Prepare a **dropdown filter** by extracting unique category values for an interactive chart.

Read `n` rows of `category value` (space-separated). Print all unique categories sorted alphabetically, one per line.

Example:
```
Input:
6
North 100
South 80
North 90
East 70
South 60
East 110
Output:
East
North
South
```
MD,
                'starter_code'        => "n = int(input())\nrows = [input().split()[0] for _ in range(n)]\n# Print unique sorted categories\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 15.10: Best Practices, Color & Storytelling (Q44–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Choose the appropriate **color palette** type for a dataset.

Rules:
- `sequential` → data goes from low to high (one direction)
- `diverging` → data has a meaningful center (e.g. correlation or temperature)
- `qualitative` → data represents distinct categories

Read a palette description string (one line). Print the palette type.

Descriptions:
- If it contains `categor` → `qualitative`
- If it contains `center` or `diverge` → `diverging`
- Otherwise → `sequential`

Example:
```
Input: data has a meaningful center
Output: diverging
```
MD,
                'starter_code'        => "description = input().lower()\n# Determine and print palette type\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Compute the **data-to-ink ratio** score for chart simplification.

Data-to-ink ratio ≈ meaningful_elements / total_elements

Read `meaningful` and `total` (ints). Print the ratio rounded to 4 decimal places.

Example:
```
Input:
6
10
Output: 0.6
```
MD,
                'starter_code'        => "meaningful = int(input())\ntotal = int(input())\nprint(round(meaningful / total, 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Identify the **chart anti-pattern** from a description.

Rules (check in order):
- If description contains `3d pie` → `avoid 3D pie charts`
- If description contains `too many colors` → `limit color palette`
- If description contains `no label` → `always label axes`
- If description contains `truncated` → `start y-axis at zero`
- Otherwise → `no issue detected`

Read a description string (one line, lowercased). Print the advice.

Example:
```
Input: the chart uses a truncated y axis
Output: start y-axis at zero
```
MD,
                'starter_code'        => "description = input().lower()\n# Identify and print anti-pattern advice\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Generate a **chart title** from data metadata.

Read:
- `chart_type` (string, e.g. "Bar Chart")
- `x_label` (string, e.g. "Month")
- `y_label` (string, e.g. "Revenue")

Print: `<chart_type> of <y_label> by <x_label>`

Example:
```
Input:
Bar Chart
Month
Revenue
Output: Bar Chart of Revenue by Month
```
MD,
                'starter_code'        => "chart_type = input()\nx_label = input()\ny_label = input()\n# Print formatted chart title\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Compute the **moving average** (smoothed line) for a line chart.

Given `n` values and a window size `w`, compute the simple moving average for each position where a full window is available (starting at index w−1).

Moving average[i] = mean of values[i−w+1 : i+1]

Read `n`, then `n` values, then `w`. Print each moving average rounded to 2 decimal places, one per line.

Example:
```
Input:
5
10
20
30
40
50
3
Output:
20.0
30.0
40.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nw = int(input())\n# Compute and print moving averages\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Identify the **key insight** from a dataset for chart storytelling.

Read `n` pairs of `label value`. Find:
1. The label with the maximum value → `Highest: <label> (<value>)`
2. The label with the minimum value → `Lowest: <label> (<value>)`
3. The range → `Range: <range>`

All values as floats rounded to 2 decimal places.

Example:
```
Input:
4
Q1 120
Q2 95
Q3 150
Q4 80
Output:
Highest: Q3 (150.0)
Lowest: Q4 (80.0)
Range: 70.0
```
MD,
                'starter_code'        => "n = int(input())\ndata = []\nfor _ in range(n):\n    parts = input().split()\n    data.append((parts[0], float(parts[1])))\n# Find and print key insights\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Generate a **complete visualization summary report** for a dataset.

Read:
- `title` (string)
- `chart_type` (string)
- `n` data points (int)
- `n` values (one per line, floats)

Print:
```
Title: <title>
Chart: <chart_type>
Points: <n>
Min: <min>
Max: <max>
Mean: <mean>
Trend: <Rising/Falling/Flat>
```

- Trend is `Rising` if last value > first, `Falling` if last < first, else `Flat`.
- Min, Max, Mean rounded to 2 decimal places.

Example:
```
Input:
Monthly Sales
Line Chart
4
100
120
110
130
Output:
Title: Monthly Sales
Chart: Line Chart
Points: 4
Min: 100.0
Max: 130.0
Mean: 115.0
Trend: Rising
```
MD,
                'starter_code'        => "title = input()\nchart_type = input()\nn = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Print visualization summary report\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],
        ];

        // ─────────────────────────────────────────────────────────────────
        // Insert questions
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
        // 3. TEST CASES
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $seed = function (int $qIndex, array $cases) use ($questions) {
            $qId = $questions[$qIndex] ?? null;
            if (! $qId) return;

            foreach ($cases as $case) {
                $exists = DB::table('test_cases')->where([
                    'coding_question_id' => $qId,
                    'input'              => $case['input'],
                    'expected_output'    => $case['expected_output'],
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

        // ── Q1: chart type selector ───────────────────────────────────────
        $seed(1, [
            ['input' => "trend over time",       'expected_output' => "line chart",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "compare categories",    'expected_output' => "bar chart",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "show distribution",     'expected_output' => "histogram",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "show proportions",      'expected_output' => "pie chart",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: categorical vs continuous axis ────────────────────────────
        $seed(2, [
            ['input' => "3\nJan\nFeb\nMar",        'expected_output' => "categorical",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",              'expected_output' => "continuous",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nA\nB\nC\nD",           'expected_output' => "categorical",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n20\n30\n40",       'expected_output' => "continuous",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: data range ────────────────────────────────────────────────
        $seed(3, [
            ['input' => "5\n10\n40\n20\n30\n50",   'expected_output' => "Min: 10.0\nMax: 50.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",              'expected_output' => "Min: 1.0\nMax: 3.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n-5\n0\n5\n10",         'expected_output' => "Min: -5.0\nMax: 10.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n100\n200",             'expected_output' => "Min: 100.0\nMax: 200.0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: count in range ────────────────────────────────────────────
        $seed(4, [
            ['input' => "6\n10\n20\n30\n40\n50\n60\n20\n40",   'expected_output' => "3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n2\n3",                'expected_output' => "2",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5\n10\n15\n20\n25\n10\n20",        'expected_output' => "3",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2\n3\n4\n5",                   'expected_output' => "0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: period change ─────────────────────────────────────────────
        $seed(5, [
            ['input' => "5\n100\n120\n110\n130\n125",   'expected_output' => "20.0\n-10.0\n20.0\n-5.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n50\n75\n60",               'expected_output' => "25.0\n-15.0",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n10\n10\n10",           'expected_output' => "0.0\n0.0\n0.0",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n100\n80\n60",              'expected_output' => "-20.0\n-20.0",               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: percentage change ─────────────────────────────────────────
        $seed(6, [
            ['input' => "3\n100\n120\n90",    'expected_output' => "20.0\n-25.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n50\n100\n50",     'expected_output' => "100.0\n-50.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n20\n30\n40",  'expected_output' => "100.0\n50.0\n33.33", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n200\n100",        'expected_output' => "-50.0",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: sort categories for bar chart ─────────────────────────────
        $seed(7, [
            ['input' => "3\napples 50\nbananas 80\ncherries 30",    'expected_output' => "bananas: 80.0\napples: 50.0\ncherries: 30.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA 100\nB 100",                          'expected_output' => "A: 100.0\nB: 100.0",                           'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nx 5\ny 10\nz 1",                        'expected_output' => "y: 10.0\nx: 5.0\nz: 1.0",                      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nW 40\nX 30\nY 20\nZ 10",               'expected_output' => "W: 40.0\nX: 30.0\nY: 20.0\nZ: 10.0",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: cumulative sum ────────────────────────────────────────────
        $seed(8, [
            ['input' => "4\n10\n20\n15\n25",     'expected_output' => "10.0\n30.0\n45.0\n70.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5",            'expected_output' => "5.0\n10.0\n15.0",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n2\n3\n4\n5",      'expected_output' => "1.0\n3.0\n6.0\n10.0\n15.0",'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n100\n0\n-50",        'expected_output' => "100.0\n100.0\n50.0",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: grouped bar chart totals ──────────────────────────────────
        $seed(9, [
            ['input' => "2\nSales\nMarketing\n100\n80\n60\n40",           'expected_output' => "Sales: A=100.0 B=60.0 Total=160.0\nMarketing: A=80.0 B=40.0 Total=120.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nQ1\nQ2\n50\n70\n50\n30",                      'expected_output' => "Q1: A=50.0 B=50.0 Total=100.0\nQ2: A=70.0 B=30.0 Total=100.0",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nJan\nFeb\nMar\n10\n20\n30\n5\n10\n15",       'expected_output' => "Jan: A=10.0 B=5.0 Total=15.0\nFeb: A=20.0 B=10.0 Total=30.0\nMar: A=30.0 B=15.0 Total=45.0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\nA\nB\n0\n0\n0\n0",                           'expected_output' => "A: A=0.0 B=0.0 Total=0.0\nB: A=0.0 B=0.0 Total=0.0",                       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: histogram bin counts ─────────────────────────────────────
        $seed(10, [
            ['input' => "6\n1\n2\n3\n4\n5\n6\n3\n1\n6",          'expected_output' => "2\n2\n2",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n10\n20\n30\n40\n2\n10\n40",           'expected_output' => "2\n2",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n1\n2\n4\n5\n2\n1\n5",             'expected_output' => "3\n1\n1",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "6\n0\n1\n2\n3\n4\n5\n3\n0\n5",          'expected_output' => "2\n2\n2",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: box plot statistics ──────────────────────────────────────
        $seed(11, [
            ['input' => "5\n1\n2\n3\n4\n5",         'expected_output' => "Min: 1.0\nQ1: 1.5\nMedian: 3.0\nQ3: 4.5\nMax: 5.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n2\n4\n6\n8",            'expected_output' => "Min: 2.0\nQ1: 3.0\nMedian: 5.0\nQ3: 7.0\nMax: 8.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1\n2\n3\n7\n8\n9",      'expected_output' => "Min: 1.0\nQ1: 1.5\nMedian: 5.0\nQ3: 8.5\nMax: 9.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n10\n20\n30",            'expected_output' => "Min: 10.0\nQ1: 10.0\nMedian: 20.0\nQ3: 30.0\nMax: 30.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q12: box plot outliers ────────────────────────────────────────
        $seed(12, [
            ['input' => "7\n1\n2\n3\n4\n5\n6\n20",   'expected_output' => "20.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1\n2\n3\n4\n5",          'expected_output' => "None",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1\n2\n3\n4\n5\n100",     'expected_output' => "100.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n11\n12\n13",         'expected_output' => "None",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: skewness ─────────────────────────────────────────────────
        $seed(13, [
            ['input' => "5\n1\n2\n3\n4\n100",    'expected_output' => "right skewed",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1\n2\n3\n4\n5",      'expected_output' => "symmetric",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0\n1\n2\n3\n100",    'expected_output' => "right skewed",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n5\n5\n5",         'expected_output' => "left skewed",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: frequency density ────────────────────────────────────────
        $seed(14, [
            ['input' => "3\n5\n10\n5\n20\n2",     'expected_output' => "0.125\n0.25\n0.125",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10\n10\n20\n5",       'expected_output' => "0.1\n0.1",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n5\n15\n10\n20\n50\n1",'expected_output' => "0.1\n0.3\n0.2\n0.4",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n100\n0\n0\n100\n10",  'expected_output' => "0.1\n0.0\n0.0",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Pearson r ────────────────────────────────────────────────
        $seed(15, [
            ['input' => "3\n1 2\n2 4\n3 6",           'expected_output' => "1.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 3\n2 2\n3 1",           'expected_output' => "-1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2\n2 4\n3 3\n4 5",      'expected_output' => "0.9487",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 5\n2 5\n3 5",           'expected_output' => "0.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: correlation classification ──────────────────────────────
        $seed(16, [
            ['input' => "-0.9",   'expected_output' => "strong negative",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.6",    'expected_output' => "moderate positive",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.3",    'expected_output' => "weak positive",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0",    'expected_output' => "weak none",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: trend line ───────────────────────────────────────────────
        $seed(17, [
            ['input' => "3\n1 2\n2 4\n3 6",      'expected_output' => "y = 2.0x + 0.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0 1\n1 3",           'expected_output' => "y = 2.0x + 1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 4\n2 3\n3 2",      'expected_output' => "y = -1.0x + 5.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 5\n2 5\n3 5",      'expected_output' => "y = 0.0x + 5.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: furthest point from trend ────────────────────────────────
        $seed(18, [
            ['input' => "4\n1 2\n2 4\n3 6\n4 10",    'expected_output' => "x=4.0 residual=2.2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 1\n2 2\n3 5",          'expected_output' => "x=3.0 residual=1.3333", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 2\n2 4\n3 6",          'expected_output' => "x=1.0 residual=0.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 5\n2 3\n3 4\n4 2",     'expected_output' => "x=2.0 residual=0.5",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: quadrant counts ──────────────────────────────────────────
        $seed(19, [
            ['input' => "4\n1 2\n-1 3\n-2 -1\n3 -4",     'expected_output' => "Q1: 1\nQ2: 1\nQ3: 1\nQ4: 1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 1\n2 2\n3 3\n4 4",         'expected_output' => "Q1: 4\nQ2: 0\nQ3: 0\nQ4: 0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n-1 -1\n-2 -2\n-3 -3",        'expected_output' => "Q1: 0\nQ2: 0\nQ3: 3\nQ4: 0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n0 0\n0 1\n1 0\n-1 0",        'expected_output' => "Q1: 2\nQ2: 0\nQ3: 0\nQ4: 1",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: correlation matrix ───────────────────────────────────────
        $seed(20, [
            ['input' => "3\n1 2\n2 4\n3 6",        'expected_output' => "1.0 1.0\n1.0 1.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 3\n2 2\n3 1",        'expected_output' => "1.0 -1.0\n-1.0 1.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 5\n2 5\n3 5",        'expected_output' => "1.0 0.0\n0.0 1.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2\n2 4\n3 3\n4 5",   'expected_output' => "1.0 0.9487\n0.9487 1.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q21: highest correlated pair ──────────────────────────────────
        $seed(21, [
            ['input' => "3\nA B 0.9\nC D -0.95\nE F 0.7",    'expected_output' => "C D -0.95",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nX Y 0.5\nP Q 0.5",              'expected_output' => "X Y 0.5",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\na b 0.2\nc d 0.8\ne f -0.85",   'expected_output' => "e f -0.85",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nM N -1.0\nP Q 0.99",            'expected_output' => "M N -1.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: heatmap normalization ────────────────────────────────────
        $seed(22, [
            ['input' => "4\n-1.0\n-0.5\n0.5\n1.0",    'expected_output' => "0.0\n0.25\n0.75\n1.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0\n100",                  'expected_output' => "0.0\n1.0",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3",                 'expected_output' => "0.0\n0.5\n1.0",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n5\n5\n5\n5",              'expected_output' => "0.0\n0.0\n0.0\n0.0",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: count strong correlations ───────────────────────────────
        $seed(23, [
            ['input' => "5\n1.0\n0.85\n-0.75\n0.3\n-0.4",   'expected_output' => "2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1.0\n0.9\n-0.8",                'expected_output' => "2",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n0.5\n0.4\n0.3\n0.2",            'expected_output' => "0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n-0.7\n0.7\n0.0",                'expected_output' => "2",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: covariance ───────────────────────────────────────────────
        $seed(24, [
            ['input' => "3\n1 2\n2 4\n3 6",        'expected_output' => "1.3333",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 3\n2 2\n3 1",        'expected_output' => "-0.6667",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 5\n2 5\n3 5",        'expected_output' => "0.0",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1 2\n2 4\n3 6\n4 8",   'expected_output' => "2.5",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: pair plot subplots ───────────────────────────────────────
        $seed(25, [
            ['input' => "4",   'expected_output' => "Subplots: 16",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3",   'expected_output' => "Subplots: 9",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "5",   'expected_output' => "Subplots: 25",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2",   'expected_output' => "Subplots: 4",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: unique variable pairs ────────────────────────────────────
        $seed(26, [
            ['input' => "3\nheight\nweight\nage",        'expected_output' => "age vs height\nage vs weight\nheight vs weight",                              'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA\nB",                      'expected_output' => "A vs B",                                                                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nx1\nx2\nx3\nx4",            'expected_output' => "x1 vs x2\nx1 vs x3\nx1 vs x4\nx2 vs x3\nx2 vs x4\nx3 vs x4",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nc\na\nb",                   'expected_output' => "a vs b\na vs c\nb vs c",                                                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: group by label (FacetGrid) ──────────────────────────────
        $seed(27, [
            ['input' => "6\nA 10\nB 20\nA 30\nB 40\nA 50\nB 60",    'expected_output' => "A: 10.0 30.0 50.0\nB: 20.0 40.0 60.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nX 1\nY 2\nX 3\nY 4",                    'expected_output' => "X: 1.0 3.0\nY: 2.0 4.0",                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\nP 5\nQ 10\nR 15\nP 20\nQ 25\nR 30",     'expected_output' => "P: 5.0 20.0\nQ: 10.0 25.0\nR: 15.0 30.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nM 1\nM 2\nM 3",                         'expected_output' => "M: 1.0 2.0 3.0",                           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: per-group stats ──────────────────────────────────────────
        $seed(28, [
            ['input' => "4\nA 10\nA 20\nB 30\nB 40",          'expected_output' => "A: mean=15.0 std=5.0\nB: mean=35.0 std=5.0",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nX 1\nX 3\nY 2\nY 4",              'expected_output' => "X: mean=2.0 std=1.0\nY: mean=3.0 std=1.0",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\nA 5\nA 5\nA 5\nB 10\nB 20\nB 30", 'expected_output' => "A: mean=5.0 std=0.0\nB: mean=20.0 std=8.1650",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nG 100\nH 200",                    'expected_output' => "G: mean=100.0 std=0.0\nH: mean=200.0 std=0.0",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: pie chart percentages ────────────────────────────────────
        $seed(29, [
            ['input' => "3\ncats 50\ndogs 30\nfish 20",       'expected_output' => "cats: 50.0%\ndogs: 30.0%\nfish: 20.0%",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA 75\nB 25",                     'expected_output' => "A: 75.0%\nB: 25.0%",                      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nW 10\nX 20\nY 30\nZ 40",         'expected_output' => "Z: 40.0%\nY: 30.0%\nX: 20.0%\nW: 10.0%", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nP 33\nQ 33\nR 34",               'expected_output' => "R: 34.0%\nP: 33.0%\nQ: 33.0%",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: slice angles ─────────────────────────────────────────────
        $seed(30, [
            ['input' => "4\n25\n25\n25\n25",    'expected_output' => "90.0\n90.0\n90.0\n90.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n50\n50",            'expected_output' => "180.0\n180.0",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n60\n30\n10",        'expected_output' => "216.0\n108.0\n36.0",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n20\n30\n40",    'expected_output' => "36.0\n72.0\n108.0\n144.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: "other" category ─────────────────────────────────────────
        $seed(31, [
            ['input' => "4\nA 50\nB 30\nC 5\nD 3\n10",      'expected_output' => "A: 50.0\nB: 30.0\nOther: 8.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nX 70\nY 20\nZ 10\n15",          'expected_output' => "X: 70.0\nY: 20.0\nOther: 10.0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nA 40\nB 35\nC 15\nD 10\n20",    'expected_output' => "A: 40.0\nB: 35.0\nC: 15.0\nOther: 10.0", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\nA 50\nB 30\nC 20\n5",           'expected_output' => "A: 50.0\nB: 30.0\nC: 20.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: cumulative percentage ────────────────────────────────────
        $seed(32, [
            ['input' => "3\nA 60\nB 30\nC 10",              'expected_output' => "A: 60.0%\nB: 90.0%\nC: 100.0%",                          'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nW 40\nX 30\nY 20\nZ 10",        'expected_output' => "W: 40.0%\nX: 70.0%\nY: 90.0%\nZ: 100.0%",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nP 75\nQ 25",                   'expected_output' => "P: 75.0%\nQ: 100.0%",                                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nX 50\nY 30\nZ 20",             'expected_output' => "X: 50.0%\nY: 80.0%\nZ: 100.0%",                          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Pareto 80/20 ─────────────────────────────────────────────
        $seed(33, [
            ['input' => "5\nA 50\nB 30\nC 10\nD 7\nE 3",   'expected_output' => "A\nB",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nX 60\nY 30\nZ 10",             'expected_output' => "X\nY",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\nA 40\nB 40\nC 10\nD 10",       'expected_output' => "A\nB",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nP 80\nQ 15\nR 5",              'expected_output' => "P",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: grid dimensions ──────────────────────────────────────────
        $seed(34, [
            ['input' => "7\n3",    'expected_output' => "Rows: 3\nCols: 3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n2",    'expected_output' => "Rows: 2\nCols: 2",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "10\n4",   'expected_output' => "Rows: 3\nCols: 4",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5",    'expected_output' => "Rows: 1\nCols: 1",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: subplot position ─────────────────────────────────────────
        $seed(35, [
            ['input' => "5\n3",    'expected_output' => "Row: 1\nCol: 2",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n4",    'expected_output' => "Row: 0\nCol: 0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n3",    'expected_output' => "Row: 2\nCol: 1",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2",    'expected_output' => "Row: 2\nCol: 0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: figure size ──────────────────────────────────────────────
        $seed(36, [
            ['input' => "2\n3\n4\n3",   'expected_output' => "Width: 12\nHeight: 6",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1\n6\n4",   'expected_output' => "Width: 6\nHeight: 4",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2\n5\n3",   'expected_output' => "Width: 10\nHeight: 9",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4\n3\n3",   'expected_output' => "Width: 12\nHeight: 12",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: tick formatter ───────────────────────────────────────────
        $seed(37, [
            ['input' => "4\n0\n25\n50\n75\n%",    'expected_output' => "0.0%\n25.0%\n50.0%\n75.0%",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10\n20\n30\nk",       'expected_output' => "10.0k\n20.0k\n30.0k",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n5\n10\n$",            'expected_output' => "5.0$\n10.0$",                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2\n3\nM",          'expected_output' => "1.0M\n2.0M\n3.0M",               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: evenly spaced ticks ──────────────────────────────────────
        $seed(38, [
            ['input' => "0\n100\n5",    'expected_output' => "0.0\n25.0\n50.0\n75.0\n100.0",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "0\n10\n3",     'expected_output' => "0.0\n5.0\n10.0",                      'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5\n5",      'expected_output' => "1.0\n2.0\n3.0\n4.0\n5.0",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "-10\n10\n5",   'expected_output' => "-10.0\n-5.0\n0.0\n5.0\n10.0",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: single tooltip ───────────────────────────────────────────
        $seed(39, [
            ['input' => "Sales\n3.5\n120.75",      'expected_output' => "Sales: x=3.5, y=120.75",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "Revenue\n10\n5000",        'expected_output' => "Revenue: x=10.0, y=5000.0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "Point\n0\n0",             'expected_output' => "Point: x=0.0, y=0.0",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "Peak\n7.2\n99.99",        'expected_output' => "Peak: x=7.2, y=99.99",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: multiple tooltips ────────────────────────────────────────
        $seed(40, [
            ['input' => "3\nAlpha 1 10\nBeta 2 20\nGamma 3 30",    'expected_output' => "Alpha: x=1.0, y=10.0\nBeta: x=2.0, y=20.0\nGamma: x=3.0, y=30.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nA 5 50\nB 10 100",                     'expected_output' => "A: x=5.0, y=50.0\nB: x=10.0, y=100.0",                              'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nX 0 0\nY 1 1\nZ 2 4",                  'expected_output' => "X: x=0.0, y=0.0\nY: x=1.0, y=1.0\nZ: x=2.0, y=4.0",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nSingle 99 100",                        'expected_output' => "Single: x=99.0, y=100.0",                                           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: zoom window ──────────────────────────────────────────────
        $seed(41, [
            ['input' => "4\n0\n10\n20\n30\n0.5",    'expected_output' => "Low: 7.5\nHigh: 22.5",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0\n100\n1.0",           'expected_output' => "Low: 0.0\nHigh: 100.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n20\n30\n40\n0.5",   'expected_output' => "Low: 17.5\nHigh: 32.5",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0\n50\n100\n0.2",       'expected_output' => "Low: 40.0\nHigh: 60.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: nearest point ────────────────────────────────────────────
        $seed(42, [
            ['input' => "3\nA 1 10\nB 5 20\nC 9 30\n4",     'expected_output' => "Nearest: B at x=5.0, y=20.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nX 0 100\nY 10 200\n3",          'expected_output' => "Nearest: X at x=0.0, y=100.0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nP 2 5\nQ 5 10\nR 8 15\n6",      'expected_output' => "Nearest: Q at x=5.0, y=10.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nM 0 0\nN 100 100\n60",          'expected_output' => "Nearest: N at x=100.0, y=100.0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: dropdown filter categories ──────────────────────────────
        $seed(43, [
            ['input' => "6\nNorth 100\nSouth 80\nNorth 90\nEast 70\nSouth 60\nEast 110",   'expected_output' => "East\nNorth\nSouth",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\nA 1\nB 2\nA 3\nC 4",                                          'expected_output' => "A\nB\nC",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nX 1\nX 2\nX 3",                                               'expected_output' => "X",                   'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nD 4\nC 3\nB 2\nA 1",                                          'expected_output' => "A\nB\nC\nD",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: color palette type ───────────────────────────────────────
        $seed(44, [
            ['input' => "data has a meaningful center",   'expected_output' => "diverging",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "distinct categories",            'expected_output' => "qualitative",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "temperature range low to high",  'expected_output' => "sequential",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "categor groups",                 'expected_output' => "qualitative",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: data-to-ink ratio ────────────────────────────────────────
        $seed(45, [
            ['input' => "6\n10",    'expected_output' => "0.6",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n10",   'expected_output' => "1.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n12",    'expected_output' => "0.25",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n4",     'expected_output' => "0.25",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: chart anti-pattern ───────────────────────────────────────
        $seed(46, [
            ['input' => "the chart uses a truncated y axis",     'expected_output' => "start y-axis at zero",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "using 3d pie chart",                    'expected_output' => "avoid 3D pie charts",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "the axes have no label",                'expected_output' => "always label axes",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "chart uses too many colors",            'expected_output' => "limit color palette",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: chart title generator ────────────────────────────────────
        $seed(47, [
            ['input' => "Bar Chart\nMonth\nRevenue",             'expected_output' => "Bar Chart of Revenue by Month",         'is_hidden' => false, 'order_index' => 1],
            ['input' => "Line Chart\nYear\nTemperature",         'expected_output' => "Line Chart of Temperature by Year",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "Scatter Plot\nAge\nSalary",             'expected_output' => "Scatter Plot of Salary by Age",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "Histogram\nScore\nFrequency",           'expected_output' => "Histogram of Frequency by Score",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: moving average ───────────────────────────────────────────
        $seed(48, [
            ['input' => "5\n10\n20\n30\n40\n50\n3",       'expected_output' => "20.0\n30.0\n40.0",         'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n2",              'expected_output' => "1.5\n2.5\n3.5",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n5\n10\n15\n20\n25\n30\n4",   'expected_output' => "12.5\n17.5\n22.5",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n2\n4\n6\n8\n10\n5",          'expected_output' => "6.0",                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: key insight ──────────────────────────────────────────────
        $seed(49, [
            ['input' => "4\nQ1 120\nQ2 95\nQ3 150\nQ4 80",    'expected_output' => "Highest: Q3 (150.0)\nLowest: Q4 (80.0)\nRange: 70.0",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\nJan 10\nFeb 20\nMar 30",          'expected_output' => "Highest: Mar (30.0)\nLowest: Jan (10.0)\nRange: 20.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nA 100\nB 100",                    'expected_output' => "Highest: A (100.0)\nLowest: A (100.0)\nRange: 0.0",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nW 5\nX 15\nY 25\nZ 35",          'expected_output' => "Highest: Z (35.0)\nLowest: W (5.0)\nRange: 30.0",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: visualization summary report ─────────────────────────────
        $seed(50, [
            ['input' => "Monthly Sales\nLine Chart\n4\n100\n120\n110\n130",   'expected_output' => "Title: Monthly Sales\nChart: Line Chart\nPoints: 4\nMin: 100.0\nMax: 130.0\nMean: 115.0\nTrend: Rising",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "Scores\nBar Chart\n3\n80\n90\n70",                  'expected_output' => "Title: Scores\nChart: Bar Chart\nPoints: 3\nMin: 70.0\nMax: 90.0\nMean: 80.0\nTrend: Falling",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "Flat Line\nLine Chart\n3\n50\n50\n50",              'expected_output' => "Title: Flat Line\nChart: Line Chart\nPoints: 3\nMin: 50.0\nMax: 50.0\nMean: 50.0\nTrend: Flat",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "Weekly Views\nLine Chart\n4\n200\n180\n220\n300",   'expected_output' => "Title: Weekly Views\nChart: Line Chart\nPoints: 4\nMin: 180.0\nMax: 300.0\nMean: 225.0\nTrend: Rising",    'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 15 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}