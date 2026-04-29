<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 12 — Forecasting & Time Series (Newbie) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering beginner forecasting in Python
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered (mirrors Module 12 curriculum):
 *   12.1  What Is Forecasting? Goals, Use Cases & the Forecasting Workflow
 *   12.2  Time Series Decomposition: Trend, Seasonality & Residuals
 *   12.3  Stationarity, Differencing & the ADF Test
 *   12.4  Autocorrelation: ACF, PACF & White Noise
 *   12.5  Exponential Smoothing: SES, Holt's & Holt-Winters
 *   12.6  ARIMA Models: Identification, Estimation & Diagnostics
 *   12.7  SARIMA: Seasonal ARIMA Models
 *   12.8  Forecast Accuracy Metrics: MAE, RMSE, MAPE & MASE
 *   12.9  Cross-Validation for Time Series & Forecast Evaluation
 *   12.10 Prophet & Modern Forecasting Tools
 *
 * Difficulty: Newbie — straightforward formulas, no advanced libraries,
 *             use only Python's `math`, `statistics`, and basic built-ins.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module12CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 12 — Forecasting & Time Series (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Forecasting & Time Series',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply fundamental forecasting and time series concepts in Python. Compute moving averages, decompose series, measure forecast accuracy, and implement basic exponential smoothing using only Python\'s built-in tools and the math/statistics modules.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 500,
                'order_index'        => 12,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 12.1: What Is Forecasting? (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Given a time series of `n` values, compute the **simple moving average (SMA)** with a window of `k`.

The SMA at position `i` (0-indexed, for `i >= k-1`) = average of values from index `i-k+1` to `i` inclusive.

Read `n`, then `n` values, then `k`. Print the SMA values (from index `k-1` onward), one per line, rounded to 2 decimal places.

Example:
```
Input:
5
1
2
3
4
5
3
Output:
2.0
3.0
4.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nk = int(input())\n# Compute and print SMA\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Determine if a forecasting problem is **univariate** or **multivariate**.

- If only 1 series is given → `univariate`
- If 2 or more series are given → `multivariate`

Read the number of series `m`. Print `univariate` or `multivariate`.

Example:
```
Input:
1
Output: univariate
```
```
Input:
3
Output: multivariate
```
MD,
                'starter_code'        => "m = int(input())\n# Print univariate or multivariate\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Given a time series, compute the **naive forecast**: the next predicted value is the last observed value.

Read `n` values (one per line). Print the naive forecast for the next time step.

Example:
```
Input:
4
10
20
30
40
Output: 40.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Print naive forecast\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Compute the **mean forecast**: the next predicted value is the mean of all observed values.

Read `n` values (one per line). Print the mean forecast rounded to 4 decimal places.

Example:
```
Input:
4
10
20
30
40
Output: 25.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Print mean forecast\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Compute the **drift forecast** for one step ahead.

Drift forecast = last value + (last value − first value) / (n − 1)

Read `n` values (one per line). Print the drift forecast rounded to 4 decimal places.

Example:
```
Input:
3
10
20
30
Output: 40.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Compute drift forecast\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 12.2: Time Series Decomposition (Q6–Q11)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Compute the **trend component** of a time series using a centered moving average of window `k` (k must be odd).

The trend at position `i` = average of values from index `i - k//2` to `i + k//2` inclusive.

Read `n`, then `n` values, then `k`. Print the trend values (only for positions where the full window fits), one per line, rounded to 4 decimal places.

Example:
```
Input:
5
2
4
6
8
10
3
Output:
4.0
6.0
8.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nk = int(input())\nhalf = k // 2\n# Compute centered MA (trend)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Given actual values and their trend values (same length), compute the **residuals** (actual − trend).

Read `n`, then `n` actual values, then `n` trend values. Print each residual rounded to 4 decimal places, one per line.

Example:
```
Input:
3
10
12
14
9
12
15
Output:
1.0
0.0
-1.0
```
MD,
                'starter_code'        => "n = int(input())\nactual = [float(input()) for _ in range(n)]\ntrend = [float(input()) for _ in range(n)]\n# Compute and print residuals\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Classify whether a pattern is **trend**, **seasonal**, or **random** based on its description.

Rules:
- `trend` → values consistently increase or decrease
- `seasonal` → values repeat with a fixed period
- `random` → values follow no discernible pattern

Read a string description. Print `trend`, `seasonal`, or `random`.

For this problem, check: if the string contains "repeat" or "period" → `seasonal`; if it contains "increase" or "decrease" → `trend`; otherwise → `random`.

Example:
```
Input:
values increase over time
Output: trend
```
```
Input:
values repeat every 12 months
Output: seasonal
```
MD,
                'starter_code'        => "desc = input().lower()\n# Classify and print pattern type\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Detect the **period** (season length) of a time series by finding the lag at which the series most closely repeats.

Given `n` values and a maximum lag to test `max_lag`, compute the sum of squared differences between the series and its shifted version for each lag from 1 to `max_lag`. The period is the lag with the **smallest** sum of squared differences.

Read `n`, then `n` values, then `max_lag`. Print the detected period.

Example:
```
Input:
6
1
2
3
1
2
3
3
Output: 3
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nmax_lag = int(input())\n# Find period with min SSD across lags\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Given an **additive decomposition** (value = trend + seasonal + residual), reconstruct the original value.

Read three floats: `trend`, `seasonal`, `residual`. Print their sum rounded to 4 decimal places.

Example:
```
Input:
100.0
5.0
-2.0
Output: 103.0
```
MD,
                'starter_code'        => "trend = float(input())\nseasonal = float(input())\nresidual = float(input())\nprint(round(trend + seasonal + residual, 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Given a **multiplicative decomposition** (value = trend × seasonal × residual), reconstruct the original value.

Read three floats: `trend`, `seasonal`, `residual`. Print their product rounded to 4 decimal places.

Example:
```
Input:
100.0
1.05
0.98
Output: 102.9
```
MD,
                'starter_code'        => "trend = float(input())\nseasonal = float(input())\nresidual = float(input())\nprint(round(trend * seasonal * residual, 4))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 12.3: Stationarity & Differencing (Q12–Q16)
            // ═══════════════════════════════════════════════════════════════

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Compute the **first difference** of a time series.

The first difference at position `i` = value[i] − value[i-1], for i from 1 to n-1.

Read `n` values (one per line). Print the differenced series, one per line, rounded to 4 decimal places.

Example:
```
Input:
5
10
13
17
22
28
Output:
3.0
4.0
5.0
6.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Compute first difference\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Compute the **second difference** of a time series (difference the first-differenced series once more).

Read `n` values (one per line). Print the second-differenced series, one per line, rounded to 4 decimal places.

Example:
```
Input:
5
10
13
17
22
28
Output:
1.0
1.0
1.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Compute second difference\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Compute the **seasonal difference** of a time series with period `s`.

Seasonal difference at position `i` = value[i] − value[i - s], for i from `s` to n-1.

Read `n`, then `n` values, then `s`. Print the seasonally differenced series, one per line, rounded to 4 decimal places.

Example:
```
Input:
6
10
12
14
13
15
17
3
Output:
3.0
3.0
3.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\ns = int(input())\n# Compute seasonal difference\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Compute the **mean** and **variance** of a time series and determine if the series is **likely stationary**.

A series is considered likely stationary if its variance is less than 10% of the square of its mean (i.e., coefficient of variation < ~31.6%).

Read `n` values (one per line). Print `mean` and `variance` each rounded to 4 decimal places on separate lines, then print `stationary` or `non-stationary`.

Example:
```
Input:
5
10
10
10
10
10
Output:
10.0
0.0
stationary
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Compute mean, variance, classify\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Given a sequence, check if it is **white noise**: a series where every value equals the mean (i.e., all residuals are zero).

Read `n` values (one per line). Print `white noise` if all values are equal, otherwise print `not white noise`.

Example:
```
Input:
4
5
5
5
5
Output: white noise
```
```
Input:
3
1
2
3
Output: not white noise
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Check for white noise\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 12.4: Autocorrelation: ACF, PACF & White Noise (Q17–Q21)
            // ═══════════════════════════════════════════════════════════════

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Compute the **autocorrelation** at lag `k` for a time series.

ACF(k) = [Σ(i=k to n-1)(x[i] - mean)(x[i-k] - mean)] / [Σ(i=0 to n-1)(x[i] - mean)²]

Read `n`, then `n` values, then `k`. Print the autocorrelation rounded to 4 decimal places.

Example:
```
Input:
5
1
2
3
4
5
1
Output: 0.4
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nk = int(input())\n# Compute autocorrelation at lag k\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Compute autocorrelations at lags 1 through `max_lag` and print each rounded to 4 decimal places, one per line.

ACF(k) = [Σ(i=k to n-1)(x[i] - mean)(x[i-k] - mean)] / [Σ(i=0 to n-1)(x[i] - mean)²]

Read `n`, then `n` values, then `max_lag`.

Example:
```
Input:
5
1
2
3
4
5
2
Output:
0.4
-0.1
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nmax_lag = int(input())\n# Compute ACF for lags 1 to max_lag\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Determine if an autocorrelation value is **significant** at the 95% confidence level.

The significance threshold for a series of length `n` is: ±1.96 / √n

Read `n` and then the autocorrelation value `r`. Print `significant` if |r| > 1.96/√n, otherwise `not significant`.

Example:
```
Input:
100
0.25
Output: significant
```
```
Input:
100
0.1
Output: not significant
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nr = float(input())\nthreshold = 1.96 / math.sqrt(n)\n# Print significant or not significant\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Compute the **Ljung-Box Q statistic** for a time series at lag `h`.

Q = n(n+2) × Σ(k=1 to h) [ACF(k)² / (n-k)]

where ACF(k) is the autocorrelation at lag k.

Read `n`, then `n` values, then `h`. Print Q rounded to 4 decimal places.

Example:
```
Input:
5
1
2
3
4
5
2
Output: 2.4
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nh = int(input())\nmean = sum(values) / n\n# Compute ACF and Q statistic\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Compute the **cross-correlation** at lag 0 between two series (equivalent to Pearson correlation).

Cross-correlation at lag 0 = Pearson r = [Σ(xi - x̄)(yi - ȳ)] / [√Σ(xi-x̄)² × √Σ(yi-ȳ)²]

Read `n`, then `n` x values, then `n` y values. Print the cross-correlation rounded to 4 decimal places.

Example:
```
Input:
3
1
2
3
2
4
6
Output: 1.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nx = [float(input()) for _ in range(n)]\ny = [float(input()) for _ in range(n)]\n# Compute Pearson r (cross-correlation at lag 0)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 12.5: Exponential Smoothing (Q22–Q27)
            // ═══════════════════════════════════════════════════════════════

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Apply **Simple Exponential Smoothing (SES)** to a time series.

SES formula:
- S[0] = x[0]
- S[t] = α × x[t] + (1 - α) × S[t-1]

Read `n`, then `n` values, then `α`. Print the smoothed values S[0] through S[n-1], one per line, rounded to 4 decimal places.

Example:
```
Input:
4
10
20
30
40
0.5
Output:
10.0
15.0
22.5
31.25
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nalpha = float(input())\n# Apply SES and print smoothed values\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Using **SES**, produce a **one-step-ahead forecast** (the value after the last observation).

S[0] = x[0], S[t] = α × x[t] + (1 - α) × S[t-1]. Forecast = S[n-1].

Read `n`, then `n` values, then `α`. Print the forecast rounded to 4 decimal places.

Example:
```
Input:
4
10
20
30
40
0.5
Output: 31.25
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nalpha = float(input())\n# Compute SES forecast\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Apply **Holt's Double Exponential Smoothing** (linear trend method).

Equations (initialise with x[0] for level, x[1]-x[0] for trend):
- L[0] = x[0], T[0] = x[1] - x[0]
- L[t] = α × x[t] + (1-α)(L[t-1] + T[t-1])
- T[t] = β × (L[t] - L[t-1]) + (1-β) × T[t-1]
- Forecast h steps ahead = L[n-1] + h × T[n-1]

Read `n`, then `n` values, then `α`, then `β`, then `h` (steps ahead). Print the h-step forecast rounded to 4 decimal places.

Example:
```
Input:
4
10
20
30
40
0.8
0.2
1
Output: 49.9936
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nalpha = float(input())\nbeta = float(input())\nh = int(input())\n# Apply Holt's method and forecast\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Determine the **best alpha** for SES by trying all values {0.1, 0.2, …, 0.9} and choosing the one that minimises the **Mean Squared Error (MSE)** of one-step-ahead in-sample forecasts.

One-step-ahead forecast at time t = S[t-1], where S is the SES sequence. MSE = average of (x[t] - S[t-1])² for t from 1 to n-1.

Read `n`, then `n` values. Print the best alpha (1 decimal place) and its MSE (4 decimal places) on separate lines.

Example:
```
Input:
4
10
20
30
40
Output:
0.9
16.9
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\n# Try alphas 0.1 to 0.9, find best by MSE\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Compute a **weighted moving average (WMA)** with weights given explicitly.

WMA = Σ(weight[i] × value[i]) / Σ(weight[i]) for a window of the last `k` values.

Read `n`, then `n` values, then `k` weights (for the k most recent values, oldest weight first). Print the WMA for each window of size `k` (starting at index k-1), one per line, rounded to 4 decimal places.

Example:
```
Input:
5
1
2
3
4
5
3
1
2
3
Output:
2.3333
3.3333
4.3333
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\nk = int(input())\nweights = [float(input()) for _ in range(k)]\n# Compute WMA for each window\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Compare a **naive forecast** vs a **SES forecast** by computing each method's MAE on a hold-out set.

Given a training series and a test series:
- Naive forecast for each test value = last training value
- SES forecast: smooth the training series (S[0]=x[0], S[t]=α×x[t]+(1-α)×S[t-1]), then forecast each test step using S[last_train]

Read `n_train`, then `n_train` training values, then `n_test`, then `n_test` test values, then `α`.
Print `Naive MAE: X` and `SES MAE: X` (each rounded to 4 decimal places), one per line. Then print `naive` or `ses` (whichever has lower MAE; if equal, print `tie`).

Example:
```
Input:
3
10
20
30
2
32
34
0.5
Output:
Naive MAE: 2.0
SES MAE: 3.75
naive
```
MD,
                'starter_code'        => "n_train = int(input())\ntrain = [float(input()) for _ in range(n_train)]\nn_test = int(input())\ntest = [float(input()) for _ in range(n_test)]\nalpha = float(input())\n# Compute Naive and SES MAE, print winner\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 12.6: ARIMA Models (Q28–Q32)
            // ═══════════════════════════════════════════════════════════════

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Given ARIMA order parameters `p`, `d`, `q`, classify the model type:

- If p>0 and q==0 → `AR`
- If p==0 and q>0 → `MA`
- If p>0 and q>0 → `ARMA`
- If d>0 → append ` with differencing` to the label (e.g., `AR with differencing`)
- If all are 0 → `white noise`

Read three integers: `p`, `d`, `q`. Print the classification.

Example:
```
Input:
1
0
0
Output: AR
```
```
Input:
1
1
1
Output: ARMA with differencing
```
MD,
                'starter_code'        => "p = int(input())\nd = int(input())\nq = int(input())\n# Classify ARIMA model\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Simulate one step of an **AR(1) process**.

AR(1): x[t] = φ × x[t-1] + ε

Read `φ`, `x[t-1]`, and `ε`. Print x[t] rounded to 4 decimal places.

Example:
```
Input:
0.8
10.0
0.5
Output: 8.5
```
MD,
                'starter_code'        => "phi = float(input())\nx_prev = float(input())\neps = float(input())\n# Compute AR(1) step\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Simulate one step of an **MA(1) process**.

MA(1): x[t] = μ + ε[t] + θ × ε[t-1]

Read `μ`, `θ`, `ε[t]`, `ε[t-1]`. Print x[t] rounded to 4 decimal places.

Example:
```
Input:
0.0
0.5
1.0
-1.0
Output: 0.5
```
MD,
                'starter_code'        => "mu = float(input())\ntheta = float(input())\neps_t = float(input())\neps_prev = float(input())\n# Compute MA(1) step\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Given an AR(1) coefficient φ, determine the **stationarity** of the process.

An AR(1) process is stationary if |φ| < 1.

Read φ. Print `stationary` or `non-stationary`.

Example:
```
Input:
0.8
Output: stationary
```
```
Input:
1.2
Output: non-stationary
```
MD,
                'starter_code'        => "phi = float(input())\n# Check stationarity\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Compute the **AIC** (Akaike Information Criterion) for a model.

AIC = 2k − 2 × ln(L)

where `k` is the number of parameters and `L` is the likelihood value.

Read two floats: `k` (parameters) and `L` (likelihood). Print AIC rounded to 4 decimal places.

Example:
```
Input:
3
0.5
Output: 7.3863
```
MD,
                'starter_code'        => "import math\n\nk = float(input())\nL = float(input())\n# Compute AIC\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 12.7: SARIMA (Q33–Q36)
            // ═══════════════════════════════════════════════════════════════

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Format a **SARIMA order** as a string given its parameters.

SARIMA notation: SARIMA(p,d,q)(P,D,Q)[s]

Read six integers `p d q P D Q` and one integer `s`. Print the SARIMA notation string.

Example:
```
Input:
1
1
1
1
0
1
12
Output: SARIMA(1,1,1)(1,0,1)[12]
```
MD,
                'starter_code'        => "p=int(input()); d=int(input()); q=int(input())\nP=int(input()); D=int(input()); Q=int(input())\ns=int(input())\n# Print SARIMA notation\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Compute the **seasonal naive forecast**: the forecast for the next value is the value from exactly one season ago.

Read `n`, then `n` values, then `s` (season length). Print the seasonal naive forecast for the next step (i.e., value at index n-s), rounded to 4 decimal places.

Example:
```
Input:
6
10
20
30
11
21
31
3
Output: 10.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\ns = int(input())\n# Print seasonal naive forecast\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Compute the **seasonal average forecast**: average the values at the same seasonal position across all observed seasons.

Read `n`, `n` values, and `s`. Print the average of values at positions `i % s == (n % s)` (i.e., same position in the next season's cycle), rounded to 4 decimal places.

If `n % s == 0`, use position 0.

Example:
```
Input:
6
10
20
30
12
22
32
3
Output: 11.0
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\ns = int(input())\n# Compute seasonal average forecast\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Compute the total number of parameters in a SARIMA(p,d,q)(P,D,Q)[s] model.

Total parameters = p + q + P + Q + 1 (the +1 is for the constant/intercept term).

Note: d and D are differencing orders and do not count as estimated parameters.

Read p, d, q, P, D, Q (one per line). Print the total parameter count.

Example:
```
Input:
1
1
1
1
0
1
Output: 5
```
MD,
                'starter_code'        => "p=int(input()); d=int(input()); q=int(input())\nP=int(input()); D=int(input()); Q=int(input())\n# Compute and print total parameters\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 12.8: Forecast Accuracy Metrics (Q37–Q42)
            // ═══════════════════════════════════════════════════════════════

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Compute the **Mean Absolute Error (MAE)**.

MAE = (1/n) × Σ |actual[i] - forecast[i]|

Read `n`, then `n` actual values, then `n` forecast values. Print MAE rounded to 4 decimal places.

Example:
```
Input:
4
10
20
30
40
11
19
31
39
Output: 1.0
```
MD,
                'starter_code'        => "n = int(input())\nactual = [float(input()) for _ in range(n)]\nforecast = [float(input()) for _ in range(n)]\n# Compute and print MAE\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Compute the **Root Mean Squared Error (RMSE)**.

RMSE = √[(1/n) × Σ (actual[i] - forecast[i])²]

Read `n`, then `n` actual values, then `n` forecast values. Print RMSE rounded to 4 decimal places.

Example:
```
Input:
4
10
20
30
40
11
19
31
39
Output: 1.0
```
MD,
                'starter_code'        => "import math\n\nn = int(input())\nactual = [float(input()) for _ in range(n)]\nforecast = [float(input()) for _ in range(n)]\n# Compute and print RMSE\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Compute the **Mean Absolute Percentage Error (MAPE)**.

MAPE = (100/n) × Σ |actual[i] - forecast[i]| / |actual[i]|

Read `n`, then `n` actual values, then `n` forecast values. Print MAPE rounded to 4 decimal places (as a percentage, e.g. `5.0` means 5%).

Example:
```
Input:
4
100
200
300
400
110
190
310
390
Output: 3.75
```
MD,
                'starter_code'        => "n = int(input())\nactual = [float(input()) for _ in range(n)]\nforecast = [float(input()) for _ in range(n)]\n# Compute and print MAPE\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Compute the **Mean Squared Error (MSE)**.

MSE = (1/n) × Σ (actual[i] - forecast[i])²

Read `n`, then `n` actual values, then `n` forecast values. Print MSE rounded to 4 decimal places.

Example:
```
Input:
3
10
20
30
12
18
33
Output: 6.3333
```
MD,
                'starter_code'        => "n = int(input())\nactual = [float(input()) for _ in range(n)]\nforecast = [float(input()) for _ in range(n)]\n# Compute and print MSE\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Compute the **Mean Error (ME)** (also called bias).

ME = (1/n) × Σ (forecast[i] - actual[i])

Read `n`, then `n` actual values, then `n` forecast values. Print ME rounded to 4 decimal places. Then print `overforecast` if ME > 0, `underforecast` if ME < 0, `unbiased` if ME == 0.

Example:
```
Input:
3
10
20
30
12
22
32
Output:
2.0
overforecast
```
MD,
                'starter_code'        => "n = int(input())\nactual = [float(input()) for _ in range(n)]\nforecast = [float(input()) for _ in range(n)]\n# Compute ME and bias label\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Compute the **MASE** (Mean Absolute Scaled Error).

MASE = MAE_model / MAE_naive

where MAE_naive for in-sample data = (1/(n-1)) × Σ(i=1 to n-1) |x[i] - x[i-1]|

Read `n` training values, then `m` test actual values and `m` test forecast values. Compute MASE and print rounded to 4 decimal places.

Input format: `n`, then `n` training values, then `m`, then `m` actual values, then `m` forecast values.

Example:
```
Input:
4
10
20
30
40
2
45
50
44
52
Output: 0.35
```
MD,
                'starter_code'        => "n = int(input())\ntrain = [float(input()) for _ in range(n)]\nm = int(input())\nactual = [float(input()) for _ in range(m)]\nforecast = [float(input()) for _ in range(m)]\n# Compute MASE\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 12.9: Cross-Validation for Time Series (Q43–Q47)
            // ═══════════════════════════════════════════════════════════════

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Perform a **train/test split** for a time series.

Given `n` values and a split ratio `r` (e.g., 0.8 for 80% train), split the series sequentially. Print the number of training observations, then the number of test observations.

Read `n`, then `r`. Print `Train: X` and `Test: Y` on separate lines.

Example:
```
Input:
100
0.8
Output:
Train: 80
Test: 20
```
MD,
                'starter_code'        => "n = int(input())\nr = float(input())\n# Compute and print split\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Describe the **walk-forward validation** splits for a time series.

Given `n` values, an initial training size `init`, and a step size `step`, print each (train_end, test_index) pair until the test index exceeds `n-1`. Print one pair per line as `Train: 1-X, Test: Y` (1-indexed).

Example:
```
Input:
8
4
1
Output:
Train: 1-4, Test: 5
Train: 1-5, Test: 6
Train: 1-6, Test: 7
Train: 1-7, Test: 8
```
MD,
                'starter_code'        => "n = int(input())\ninit = int(input())\nstep = int(input())\n# Print walk-forward splits\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Compute the **average MAE** across multiple walk-forward folds using the naive forecast.

For each fold, the naive forecast = last training value. Compute MAE for that fold (one test point). Average the MAE across all folds.

Read `n`, then `n` values, then `init`. Print the average naive MAE rounded to 4 decimal places.

Example:
```
Input:
5
10
13
17
22
28
3
Output: 4.5
```
MD,
                'starter_code'        => "n = int(input())\nvalues = [float(input()) for _ in range(n)]\ninit = int(input())\n# Walk-forward validate naive forecast, print avg MAE\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Compute the **coverage** of a prediction interval.

Given `n` actual values, lower bounds, and upper bounds, count what fraction of actual values fall within [lower, upper] (inclusive). Print the coverage as a decimal rounded to 4 decimal places.

Read `n`, then `n` actual, `n` lower, `n` upper values.

Example:
```
Input:
4
10
20
30
40
8
18
28
38
12
22
32
42
Output: 1.0
```
MD,
                'starter_code'        => "n = int(input())\nactual = [float(input()) for _ in range(n)]\nlower = [float(input()) for _ in range(n)]\nupper = [float(input()) for _ in range(n)]\n# Compute and print coverage\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Compute the **interval width** of prediction intervals and print the average width.

Read `n`, then `n` lower bounds, then `n` upper bounds. Print the average width rounded to 4 decimal places.

Example:
```
Input:
3
5
15
25
10
20
30
Output: 5.0
```
MD,
                'starter_code'        => "n = int(input())\nlower = [float(input()) for _ in range(n)]\nupper = [float(input()) for _ in range(n)]\n# Compute and print average interval width\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 50,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 12.10: Prophet & Modern Forecasting Tools (Q48–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Simulate a **linear trend component** of Prophet's model.

Prophet trend (simplified): y(t) = k + a × t

where `k` is the baseline (intercept) and `a` is the growth rate.

Read `k`, `a`, and a list of `n` time points. Print the trend value at each time point rounded to 4 decimal places, one per line.

Example:
```
Input:
5.0
2.0
3
0
1
2
Output:
5.0
7.0
9.0
```
MD,
                'starter_code'        => "k = float(input())\na = float(input())\nn = int(input())\ntimes = [float(input()) for _ in range(n)]\n# Compute and print trend values\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Given forecast values and their uncertainty (standard deviation), compute the **95% prediction interval** for each forecast.

95% PI: [forecast - 1.96 × std, forecast + 1.96 × std]

Read `n`, then `n` forecast values, then `n` std values. Print each interval as `lower upper` rounded to 4 decimal places, one per line.

Example:
```
Input:
2
100.0
200.0
5.0
10.0
Output:
90.2 109.8
180.4 219.6
```
MD,
                'starter_code'        => "n = int(input())\nforecasts = [float(input()) for _ in range(n)]\nstds = [float(input()) for _ in range(n)]\n# Compute and print 95% prediction intervals\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 75,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Print a **forecasting model comparison summary**.

Given MAE, RMSE, and MAPE for two models (A and B), print which model wins each metric (lower is better) and an overall winner (the model that wins at least 2 out of 3 metrics). If a metric is tied, print `tie` for that metric.

Read: `MAE_A`, `RMSE_A`, `MAPE_A`, `MAE_B`, `RMSE_B`, `MAPE_B`.

Print:
```
MAE: A/B/tie
RMSE: A/B/tie
MAPE: A/B/tie
Winner: A/B/tie
```

Example:
```
Input:
2.0
3.0
5.0
3.0
4.0
4.0
Output:
MAE: A
RMSE: A
MAPE: B
Winner: A
```
MD,
                'starter_code'        => "mae_a, rmse_a, mape_a = float(input()), float(input()), float(input())\nmae_b, rmse_b, mape_b = float(input()), float(input()), float(input())\n# Compare models and print summary\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 125,
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

        // ── Q1: simple moving average ─────────────────────────────────────
        $seed(1, [
            ['input' => "5\n1\n2\n3\n4\n5\n3",           'expected_output' => "2.0\n3.0\n4.0",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n10\n20\n30\n40\n2",           'expected_output' => "15.0\n25.0\n35.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1\n1\n1\n1\n1\n1\n3",        'expected_output' => "1.0\n1.0\n1.0\n1.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n5\n10\n15\n20\n25\n5",        'expected_output' => "15.0",                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: univariate vs multivariate ───────────────────────────────
        $seed(2, [
            ['input' => "1",   'expected_output' => "univariate",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3",   'expected_output' => "multivariate",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2",   'expected_output' => "multivariate",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5",   'expected_output' => "multivariate",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: naive forecast ────────────────────────────────────────────
        $seed(3, [
            ['input' => "4\n10\n20\n30\n40",   'expected_output' => "40.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n15\n25",        'expected_output' => "25.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n100",              'expected_output' => "100.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n2\n4\n6\n8\n10",  'expected_output' => "10.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: mean forecast ─────────────────────────────────────────────
        $seed(4, [
            ['input' => "4\n10\n20\n30\n40",   'expected_output' => "25.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n6\n6\n6",          'expected_output' => "6.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0\n100",           'expected_output' => "50.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1\n2\n3\n4\n5",   'expected_output' => "3.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: drift forecast ────────────────────────────────────────────
        $seed(5, [
            ['input' => "3\n10\n20\n30",    'expected_output' => "40.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n5\n10\n15\n20", 'expected_output' => "25.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n1\n3",          'expected_output' => "5.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n30\n20\n10",    'expected_output' => "0.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: centered moving average (trend) ───────────────────────────
        $seed(6, [
            ['input' => "5\n2\n4\n6\n8\n10\n3",    'expected_output' => "4.0\n6.0\n8.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1\n3\n5\n7\n9\n3",     'expected_output' => "3.0\n5.0\n7.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n1\n2\n3\n4\n5\n6\n7\n3",  'expected_output' => "2.0\n3.0\n4.0\n5.0\n6.0",  'is_hidden' => true, 'order_index' => 3],
            ['input' => "5\n10\n10\n10\n10\n10\n3", 'expected_output' => "10.0\n10.0\n10.0",  'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q7: residuals ─────────────────────────────────────────────────
        $seed(7, [
            ['input' => "3\n10\n12\n14\n9\n12\n15",   'expected_output' => "1.0\n0.0\n-1.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5\n10\n5\n10",            'expected_output' => "0.0\n0.0",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4\n0\n2\n3\n5", 'expected_output' => "1.0\n0.0\n0.0\n-1.0",'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n100\n200\n300\n110\n190\n310", 'expected_output' => "-10.0\n10.0\n-10.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q8: pattern classification ────────────────────────────────────
        $seed(8, [
            ['input' => "values increase over time",        'expected_output' => "trend",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "values repeat every 12 months",   'expected_output' => "seasonal",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "values show no clear structure",  'expected_output' => "random",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "values consistently decrease",    'expected_output' => "trend",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: period detection ──────────────────────────────────────────
        $seed(9, [
            ['input' => "6\n1\n2\n3\n1\n2\n3\n3",    'expected_output' => "3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "8\n1\n2\n1\n2\n1\n2\n1\n2\n2", 'expected_output' => "2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => "8\n5\n5\n5\n5\n5\n5\n5\n5\n4", 'expected_output' => "1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "8\n1\n0\n0\n0\n1\n0\n0\n0\n4", 'expected_output' => "4", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: additive reconstruction ─────────────────────────────────
        $seed(10, [
            ['input' => "100.0\n5.0\n-2.0",    'expected_output' => "103.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "50.0\n0.0\n0.0",      'expected_output' => "50.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "200.0\n-10.0\n3.5",   'expected_output' => "193.5",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1000.0\n25.0\n-5.0",  'expected_output' => "1020.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: multiplicative reconstruction ───────────────────────────
        $seed(11, [
            ['input' => "100.0\n1.05\n0.98",   'expected_output' => "102.9",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "50.0\n1.0\n1.0",      'expected_output' => "50.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "200.0\n1.1\n0.9",     'expected_output' => "198.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1000.0\n1.2\n0.8",    'expected_output' => "960.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: first difference ─────────────────────────────────────────
        $seed(12, [
            ['input' => "5\n10\n13\n17\n22\n28",   'expected_output' => "3.0\n4.0\n5.0\n6.0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",              'expected_output' => "1.0\n1.0",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n10\n10\n10",       'expected_output' => "0.0\n0.0\n0.0",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n5\n3\n7\n2",           'expected_output' => "-2.0\n4.0\n-5.0",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: second difference ────────────────────────────────────────
        $seed(13, [
            ['input' => "5\n10\n13\n17\n22\n28",   'expected_output' => "1.0\n1.0\n1.0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n4\n7",           'expected_output' => "1.0\n1.0",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n3\n6\n10",          'expected_output' => "1.0\n1.0",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n0\n2\n6\n12\n20",      'expected_output' => "2.0\n2.0\n2.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: seasonal difference ──────────────────────────────────────
        $seed(14, [
            ['input' => "6\n10\n12\n14\n13\n15\n17\n3",   'expected_output' => "3.0\n3.0\n3.0",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n5\n6\n7\n8\n2",               'expected_output' => "2.0\n2.0",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n1\n2\n3\n4\n5\n6\n3",         'expected_output' => "3.0\n3.0\n3.0",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n10\n20\n10\n20\n10\n2",        'expected_output' => "0.0\n0.0\n0.0",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: mean, variance, stationary classification ────────────────
        $seed(15, [
            ['input' => "5\n10\n10\n10\n10\n10",   'expected_output' => "10.0\n0.0\nstationary",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n100\n1\n100",        'expected_output' => "50.5\n2450.25\nnon-stationary",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5\n5\n5",              'expected_output' => "5.0\n0.0\nstationary",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n20\n30\n40",        'expected_output' => "25.0\n125.0\nnon-stationary",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: white noise check ────────────────────────────────────────
        $seed(16, [
            ['input' => "4\n5\n5\n5\n5",      'expected_output' => "white noise",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",         'expected_output' => "not white noise",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n42",              'expected_output' => "white noise",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0\n0\n1",         'expected_output' => "not white noise",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: autocorrelation at lag k ─────────────────────────────────
        $seed(17, [
            ['input' => "5\n1\n2\n3\n4\n5\n1",   'expected_output' => "0.4",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n0",      'expected_output' => "1.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n-1\n1\n-1\n1",    'expected_output' => "-1.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1\n1\n1\n1\n1\n1",   'expected_output' => "0.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: ACF for lags 1 to max_lag ───────────────────────────────
        $seed(18, [
            ['input' => "5\n1\n2\n3\n4\n5\n2",   'expected_output' => "0.4\n-0.1",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4\n1",      'expected_output' => "0.2",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n1\n1\n1\n1\n3",   'expected_output' => "0.0\n0.0\n0.0",'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n-1\n1\n-1\n2",    'expected_output' => "-1.0\n1.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: ACF significance test ────────────────────────────────────
        $seed(19, [
            ['input' => "100\n0.25",   'expected_output' => "significant",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "100\n0.1",    'expected_output' => "not significant",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "25\n0.4",     'expected_output' => "significant",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "25\n0.3",     'expected_output' => "not significant",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Ljung-Box Q statistic ────────────────────────────────────
        $seed(20, [
            ['input' => "5\n1\n2\n3\n4\n5\n2",   'expected_output' => "2.4",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n1\n1\n1\n1",      'expected_output' => "0.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n-1\n1\n-1\n1",    'expected_output' => "8.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1\n2\n3\n4\n5\n1",   'expected_output' => "0.8",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: cross-correlation at lag 0 ───────────────────────────────
        $seed(21, [
            ['input' => "3\n1\n2\n3\n2\n4\n6",       'expected_output' => "1.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n3\n2\n1",       'expected_output' => "-1.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3\n5\n5\n5",       'expected_output' => "0.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4\n2\n4\n6\n8", 'expected_output' => "1.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: SES smoothed values ──────────────────────────────────────
        $seed(22, [
            ['input' => "4\n10\n20\n30\n40\n0.5",     'expected_output' => "10.0\n15.0\n22.5\n31.25",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5\n0.3",            'expected_output' => "5.0\n5.0\n5.0",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0\n10\n20\n1.0",          'expected_output' => "0.0\n10.0\n20.0",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n100\n200\n300\n400\n0.2", 'expected_output' => "100.0\n120.0\n156.0\n204.8", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: SES one-step-ahead forecast ──────────────────────────────
        $seed(23, [
            ['input' => "4\n10\n20\n30\n40\n0.5",     'expected_output' => "31.25",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n5\n5\n0.3",            'expected_output' => "5.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n0\n10\n20\n1.0",          'expected_output' => "20.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n100\n200\n300\n400\n0.2", 'expected_output' => "204.8",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Holt's double exponential smoothing ──────────────────────
        $seed(24, [
            ['input' => "4\n10\n20\n30\n40\n0.8\n0.2\n1",  'expected_output' => "49.9936",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n10\n15\n0.9\n0.5\n1",       'expected_output' => "20.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5\n10\n15\n0.9\n0.5\n2",       'expected_output' => "25.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n10\n10\n10\n0.5\n0.5\n1",      'expected_output' => "10.0",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: best alpha for SES ───────────────────────────────────────
        $seed(25, [
            ['input' => "4\n10\n20\n30\n40",    'expected_output' => "0.9\n16.9",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10\n10\n10",        'expected_output' => "0.1\n0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4",        'expected_output' => "0.9\n0.169",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n5\n10\n15",         'expected_output' => "0.9\n2.5",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: weighted moving average ──────────────────────────────────
        $seed(26, [
            ['input' => "5\n1\n2\n3\n4\n5\n3\n1\n2\n3",    'expected_output' => "2.3333\n3.3333\n4.3333",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n10\n20\n30\n40\n2\n1\n2",      'expected_output' => "16.6667\n26.6667\n36.6667",'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5\n5\n5\n2\n1\n1",             'expected_output' => "5.0\n5.0",                 'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4\n3\n1\n1\n1",      'expected_output' => "1.6667\n2.6667\n3.6667",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: naive vs SES comparison ─────────────────────────────────
        $seed(27, [
            ['input' => "3\n10\n20\n30\n2\n32\n34\n0.5",   'expected_output' => "Naive MAE: 2.0\nSES MAE: 3.75\nnaive",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10\n10\n10\n2\n10\n10\n0.5",   'expected_output' => "Naive MAE: 0.0\nSES MAE: 0.0\ntie",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4\n2\n5\n6\n0.8",    'expected_output' => "Naive MAE: 1.5\nSES MAE: 0.56\nses",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n5\n10\n15\n1\n20\n0.5",        'expected_output' => "Naive MAE: 5.0\nSES MAE: 6.25\nnaive",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: ARIMA classification ─────────────────────────────────────
        $seed(28, [
            ['input' => "1\n0\n0",   'expected_output' => "AR",                       'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1\n1",   'expected_output' => "ARMA with differencing",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0\n1",   'expected_output' => "MA",                       'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n0\n0",   'expected_output' => "white noise",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: AR(1) step ───────────────────────────────────────────────
        $seed(29, [
            ['input' => "0.8\n10.0\n0.5",    'expected_output' => "8.5",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.5\n4.0\n1.0",     'expected_output' => "3.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.9\n100.0\n-5.0",  'expected_output' => "85.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "0.0\n50.0\n2.5",    'expected_output' => "2.5",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: MA(1) step ───────────────────────────────────────────────
        $seed(30, [
            ['input' => "0.0\n0.5\n1.0\n-1.0",    'expected_output' => "0.5",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "5.0\n0.3\n2.0\n1.0",     'expected_output' => "7.3",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "0.0\n-0.5\n2.0\n2.0",    'expected_output' => "1.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "10.0\n0.0\n0.0\n5.0",    'expected_output' => "10.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: AR(1) stationarity ───────────────────────────────────────
        $seed(31, [
            ['input' => "0.8",    'expected_output' => "stationary",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "1.2",    'expected_output' => "non-stationary",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "-0.9",   'expected_output' => "stationary",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "1.0",    'expected_output' => "non-stationary",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: AIC ──────────────────────────────────────────────────────
        $seed(32, [
            ['input' => "3\n0.5",    'expected_output' => "7.3863",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1.0",    'expected_output' => "4.0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0.1",    'expected_output' => "32.6052",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n2.0",    'expected_output' => "-0.7726",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: SARIMA notation ──────────────────────────────────────────
        $seed(33, [
            ['input' => "1\n1\n1\n1\n0\n1\n12",   'expected_output' => "SARIMA(1,1,1)(1,0,1)[12]",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0\n1\n0\n1\n0\n4",    'expected_output' => "SARIMA(2,0,1)(0,1,0)[4]",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n1\n1\n1\n1\n1\n12",   'expected_output' => "SARIMA(0,1,1)(1,1,1)[12]",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0\n0\n0\n0\n0\n1",    'expected_output' => "SARIMA(1,0,0)(0,0,0)[1]",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: seasonal naive forecast ─────────────────────────────────
        $seed(34, [
            ['input' => "6\n10\n20\n30\n11\n21\n31\n3",   'expected_output' => "10.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n5\n10\n5\n10\n2",             'expected_output' => "5.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n2\n3\n4\n5\n3",            'expected_output' => "3.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "8\n1\n2\n3\n4\n5\n6\n7\n8\n4",  'expected_output' => "5.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: seasonal average forecast ───────────────────────────────
        $seed(35, [
            ['input' => "6\n10\n20\n30\n12\n22\n32\n3",   'expected_output' => "11.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n5\n10\n5\n10\n2",             'expected_output' => "7.5",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3\n3",                  'expected_output' => "1.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "8\n10\n20\n30\n40\n12\n22\n32\n42\n4", 'expected_output' => "11.0", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q36: SARIMA parameter count ───────────────────────────────────
        $seed(36, [
            ['input' => "1\n1\n1\n1\n0\n1",   'expected_output' => "5",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0\n1\n0\n1\n0",   'expected_output' => "4",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0\n0\n0\n0\n0",   'expected_output' => "1",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2\n2\n1\n1",   'expected_output' => "9",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: MAE ──────────────────────────────────────────────────────
        $seed(37, [
            ['input' => "4\n10\n20\n30\n40\n11\n19\n31\n39",   'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n10\n15\n5\n10\n15",            'expected_output' => "0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n100\n200\n90\n210",               'expected_output' => "10.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2\n3\n3\n2\n1",               'expected_output' => "1.3333",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: RMSE ─────────────────────────────────────────────────────
        $seed(38, [
            ['input' => "4\n10\n20\n30\n40\n11\n19\n31\n39",   'expected_output' => "1.0",     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n10\n15\n5\n10\n15",            'expected_output' => "0.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n100\n200\n80\n220",               'expected_output' => "20.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1\n2\n3\n3\n2\n1",               'expected_output' => "1.6330",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: MAPE ─────────────────────────────────────────────────────
        $seed(39, [
            ['input' => "4\n100\n200\n300\n400\n110\n190\n310\n390",   'expected_output' => "3.75",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n100\n200\n100\n200",                       'expected_output' => "0.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n50\n100\n200\n55\n90\n210",               'expected_output' => "5.8333",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10\n20\n12\n18",                          'expected_output' => "10.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: MSE ──────────────────────────────────────────────────────
        $seed(40, [
            ['input' => "3\n10\n20\n30\n12\n18\n33",   'expected_output' => "6.3333",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5\n10\n5\n10",             'expected_output' => "0.0",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3\n2\n3\n4",        'expected_output' => "1.0",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n20\n30\n40\n0\n0\n0\n0", 'expected_output' => "750.0",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: ME and bias ──────────────────────────────────────────────
        $seed(41, [
            ['input' => "3\n10\n20\n30\n12\n22\n32",   'expected_output' => "2.0\noverforecast",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10\n20\n30\n8\n18\n28",    'expected_output' => "-2.0\nunderforecast",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n10\n20\n10\n20",           'expected_output' => "0.0\nunbiased",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4\n2\n3\n4\n5",  'expected_output' => "1.0\noverforecast",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: MASE ─────────────────────────────────────────────────────
        $seed(42, [
            ['input' => "4\n10\n20\n30\n40\n2\n45\n50\n44\n52",   'expected_output' => "0.35",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n10\n15\n1\n16\n10",                'expected_output' => "0.1",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4\n1\n3\n3\n5",             'expected_output' => "0.5",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n10\n20\n30\n2\n30\n30",              'expected_output' => "0.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: train/test split ─────────────────────────────────────────
        $seed(43, [
            ['input' => "100\n0.8",   'expected_output' => "Train: 80\nTest: 20",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n0.7",    'expected_output' => "Train: 7\nTest: 3",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "50\n0.9",    'expected_output' => "Train: 45\nTest: 5",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "20\n0.5",    'expected_output' => "Train: 10\nTest: 10",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: walk-forward splits ──────────────────────────────────────
        $seed(44, [
            ['input' => "8\n4\n1",   'expected_output' => "Train: 1-4, Test: 5\nTrain: 1-5, Test: 6\nTrain: 1-6, Test: 7\nTrain: 1-7, Test: 8",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n3\n1",   'expected_output' => "Train: 1-3, Test: 4\nTrain: 1-4, Test: 5",                                              'is_hidden' => false, 'order_index' => 2],
            ['input' => "6\n4\n2",   'expected_output' => "Train: 1-4, Test: 5\nTrain: 1-4, Test: 6",                                              'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n3\n1",   'expected_output' => "Train: 1-3, Test: 4",                                                                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: avg naive MAE walk-forward ───────────────────────────────
        $seed(45, [
            ['input' => "5\n10\n13\n17\n22\n28\n3",    'expected_output' => "4.5",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n5\n5\n5\n5\n2",            'expected_output' => "0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n20\n30\n40\n2",        'expected_output' => "10.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1\n3\n6\n10\n15\n3",       'expected_output' => "4.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: prediction interval coverage ────────────────────────────
        $seed(46, [
            ['input' => "4\n10\n20\n30\n40\n8\n18\n28\n38\n12\n22\n32\n42",   'expected_output' => "1.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10\n20\n30\n12\n22\n32\n15\n25\n35",              'expected_output' => "0.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n5\n15\n25\n35\n0\n10\n20\n30\n10\n20\n30\n40",   'expected_output' => "0.75",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n10\n20\n9\n19\n11\n21",                           'expected_output' => "1.0",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: average interval width ───────────────────────────────────
        $seed(47, [
            ['input' => "3\n5\n15\n25\n10\n20\n30",    'expected_output' => "5.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n0\n10\n5\n15",             'expected_output' => "5.0",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4\n3\n4\n5\n6",  'expected_output' => "2.0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n10\n10\n10\n20\n20\n20",   'expected_output' => "10.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Prophet linear trend ─────────────────────────────────────
        $seed(48, [
            ['input' => "5.0\n2.0\n3\n0\n1\n2",     'expected_output' => "5.0\n7.0\n9.0",       'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0\n1.0\n4\n0\n1\n2\n3",  'expected_output' => "0.0\n1.0\n2.0\n3.0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "10.0\n-1.0\n3\n0\n5\n10",  'expected_output' => "10.0\n5.0\n0.0",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "100.0\n0.0\n2\n1\n100",    'expected_output' => "100.0\n100.0",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: 95% prediction interval ─────────────────────────────────
        $seed(49, [
            ['input' => "2\n100.0\n200.0\n5.0\n10.0",   'expected_output' => "90.2 109.8\n180.4 219.6",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n50.0\n0.0",                 'expected_output' => "50.0 50.0",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0.0\n100.0\n10.0\n20.0",   'expected_output' => "-19.6 19.6\n60.8 139.2",     'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n200.0\n5.0",               'expected_output' => "190.2 209.8",                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: model comparison summary ────────────────────────────────
        $seed(50, [
            ['input' => "2.0\n3.0\n5.0\n3.0\n4.0\n4.0",   'expected_output' => "MAE: A\nRMSE: A\nMAPE: B\nWinner: A",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5.0\n6.0\n7.0\n4.0\n5.0\n8.0",   'expected_output' => "MAE: B\nRMSE: B\nMAPE: A\nWinner: B",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3.0\n3.0\n3.0\n3.0\n3.0\n3.0",   'expected_output' => "MAE: tie\nRMSE: tie\nMAPE: tie\nWinner: tie", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "1.0\n2.0\n10.0\n2.0\n1.0\n5.0",  'expected_output' => "MAE: A\nRMSE: B\nMAPE: B\nWinner: B",  'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 12 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}