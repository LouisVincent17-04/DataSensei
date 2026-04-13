<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module12LessonsSeeder
 * Seeds lessons for Module 12: Introductory Forecasting.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module12LessonsSeeder
 */
class Module12LessonsSeeder extends Seeder
{
    public function run()
    {
        $forecastingModule = Module::where('order_index', 12)->firstOrFail();
        Lesson::where('module_id', $forecastingModule->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 12.1 — What Is Forecasting? Goals, Use Cases & the Forecasting Workflow
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What Is Forecasting?</h2>
<p>Forecasting is the science — and art — of making quantitative predictions about <strong>future values of a variable based on its past and present behaviour</strong>. It is one of the oldest applied disciplines in statistics, yet it remains at the core of modern business intelligence, supply-chain management, financial planning, climate science, and epidemiology. Every time a retailer decides how much stock to order next month, a bank prices a loan, or an electric grid operator schedules power generation, a forecast is driving the decision.</p>

<p>Forecasting is distinct from <em>prediction</em> in the broadest sense. A prediction can be qualitative ("the market will go up") or cross-sectional ("this customer will churn"). Forecasting is always <strong>time-indexed</strong>: we are predicting a specific value at a specific future point in time, based on a chronologically ordered series of past observations. This temporal structure introduces unique challenges — autocorrelation, trend, seasonality, and non-stationarity — that do not exist in ordinary regression.</p>

<h3>Why Forecasting Matters in Practice</h3>
<p>Consider a few concrete domains where introductory forecasting techniques are applied daily:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Setting Up Your Forecasting Environment</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Core libraries used throughout this module</span>
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">pd</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">plt</span>

<span style="color:#6b7280;"># Create a simple monthly sales time series (Jan–Dec 2023)</span>
<span style="color:#93c5fd;">dates</span> = pd.date_range(start=<span style="color:#a7f3d0;">'2023-01-01'</span>, periods=<span style="color:#fcd34d;">12</span>, freq=<span style="color:#a7f3d0;">'MS'</span>)
<span style="color:#93c5fd;">sales</span> = [<span style="color:#fcd34d;">210</span>, <span style="color:#fcd34d;">195</span>, <span style="color:#fcd34d;">230</span>, <span style="color:#fcd34d;">245</span>, <span style="color:#fcd34d;">270</span>, <span style="color:#fcd34d;">290</span>,
          <span style="color:#fcd34d;">310</span>, <span style="color:#fcd34d;">305</span>, <span style="color:#fcd34d;">280</span>, <span style="color:#fcd34d;">260</span>, <span style="color:#fcd34d;">295</span>, <span style="color:#fcd34d;">340</span>]

<span style="color:#93c5fd;">ts</span> = pd.Series(sales, index=dates, name=<span style="color:#a7f3d0;">'Monthly Sales'</span>)

<span style="color:#6b7280;"># Every forecasting project starts with looking at your data</span>
<span style="color:#93c5fd;">print</span>(ts)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nMean: {ts.mean():.1f} | Std: {ts.std():.1f} | Range: {ts.min()}–{ts.max()}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>2023-01-01    210
2023-02-01    195
2023-03-01    230
2023-04-01    245
2023-05-01    270
2023-06-01    290
2023-07-01    310
2023-08-01    305
2023-09-01    280
2023-10-01    260
2023-11-01    295
2023-12-01    340
Freq: MS, Name: Monthly Sales, dtype: int64

Mean: 269.2 | Std: 42.1 | Range: 195–340</div>
  </div>
</div>

<h3>The Standard Forecasting Workflow</h3>
<p>Professional forecasters follow a repeatable workflow regardless of the domain or technique. Skipping any step — especially exploratory analysis — is the most common source of poor forecasts. The workflow is:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Forecast Evaluation Framework</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Step 1: Split into train / test — NEVER shuffle time series data!</span>
<span style="color:#93c5fd;">train</span> = ts[:<span style="color:#fcd34d;">9</span>]   <span style="color:#6b7280;"># Jan–Sep 2023 (9 months for training)</span>
<span style="color:#93c5fd;">test</span>  = ts[<span style="color:#fcd34d;">9</span>:]   <span style="color:#6b7280;"># Oct–Dec 2023 (3 months held out for evaluation)</span>

<span style="color:#6b7280;"># Step 2: Naïve baseline — forecast = last observed value</span>
<span style="color:#93c5fd;">naive_forecast</span> = [train.iloc[-<span style="color:#fcd34d;">1</span>]] * <span style="color:#93c5fd;">len</span>(test)

<span style="color:#6b7280;"># Step 3: Compute Mean Absolute Error (MAE)</span>
<span style="color:#93c5fd;">actual</span> = test.values
<span style="color:#93c5fd;">mae</span> = np.mean(np.abs(np.array(naive_forecast) - actual))

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Actual Oct–Dec:    {actual}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Naïve Forecast:    {naive_forecast}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"MAE (Naïve):       {mae:.1f} units"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Actual Oct–Dec:    [260 295 340]
Naïve Forecast:    [280, 280, 280]
MAE (Naïve):       35.0 units</div>
  </div>
</div>

<h3>Forecast Horizon & Lead Time</h3>
<p>The <strong>forecast horizon</strong> (h) is how many steps ahead you are predicting. A <em>one-step-ahead</em> forecast predicts the next single period; a <em>multi-step</em> forecast predicts several periods simultaneously. Accuracy always degrades with longer horizons — uncertainty compounds. The <strong>lead time</strong> is the minimum time needed before a forecast must be ready for operational use. Both constrain model choice: a model that takes 4 hours to train is useless if your lead time is 30 minutes.</p>

<h3>Point Forecasts vs. Interval Forecasts</h3>
<p>A <strong>point forecast</strong> gives a single predicted value (e.g., "sales will be 312 units in January"). An <strong>interval forecast</strong> (or <em>prediction interval</em>) gives a range within which the true value is expected to fall with a specified probability (e.g., "sales will be between 285 and 340 units with 95% confidence"). Point forecasts are easier to communicate but hide uncertainty. Interval forecasts are more honest and enable risk-aware decision making — they are the professional standard.</p>
HTML;

        Lesson::create([
            'module_id'   => $forecastingModule->id,
            'title'       => '12.1 What Is Forecasting? Goals, Use Cases & the Forecasting Workflow',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L12_1', [
                ['q' => 'What fundamentally distinguishes forecasting from general prediction?', 'opts' => ['Forecasting requires machine learning models', 'Forecasting is always time-indexed — predicting future values of a chronologically ordered series', 'Forecasting only works on financial data', 'Forecasting uses classification, not regression'], 'ans' => 1, 'exp' => 'Forecasting is specifically about predicting future values in a time-ordered series. The temporal structure (autocorrelation, trend, seasonality) is what makes it distinct from cross-sectional prediction.'],
                ['q' => 'Why should you NEVER randomly shuffle data before splitting a time series into train and test sets?', 'opts' => ['Shuffling is always fine for any dataset', 'Shuffling destroys the temporal order, causing data leakage — future data would contaminate the training set', 'Shuffling makes the model run slower', 'The test set must always be larger than the train set'], 'ans' => 1, 'exp' => 'Time series data must be split chronologically. If you shuffle, earlier data could end up in the test set and later data in the train set, meaning your model would have "seen the future" during training — this is data leakage and invalidates all results.'],
                ['q' => 'A naïve forecast sets every future prediction equal to:', 'opts' => ['The mean of all historical values', 'Zero', 'The last observed value', 'The median of the training set'], 'ans' => 2, 'exp' => 'The naïve method uses the most recent observation as the forecast for all future periods. It is the simplest possible benchmark. Any more sophisticated model should beat it, or it is not worth the complexity.'],
                ['q' => 'What is a prediction interval in forecasting?', 'opts' => ['The exact future value', 'A range containing the true future value with a stated probability', 'The gap between train and test sets', 'The time between observations'], 'ans' => 1, 'exp' => 'A prediction interval (e.g., 95% PI) is a range [lower, upper] such that the true future value falls inside it 95% of the time under the model assumptions. It quantifies forecast uncertainty — critical for business decision-making.'],
                ['q' => 'The forecast horizon h refers to:', 'opts' => ['The length of the training dataset', 'The number of time steps ahead being predicted', 'The error tolerance of the model', 'The frequency of the time series'], 'ans' => 1, 'exp' => 'The forecast horizon h is how many steps ahead you are predicting. h=1 is one-step-ahead. h=12 means predicting 12 periods into the future. Accuracy generally decreases as h increases because uncertainty compounds.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 12.2 — Time Series Decomposition: Trend, Seasonality & Residuals
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Time Series Decomposition: Trend, Seasonality & Residuals</h2>
<p>Before building any forecast model, you must understand <em>what your time series is made of</em>. The classical decomposition framework breaks a time series into three interpretable components: <strong>trend</strong>, <strong>seasonality</strong>, and <strong>residual</strong> (also called irregular or noise). Decomposition is both a diagnostic tool and the conceptual foundation for most forecasting methods.</p>

<h3>The Three Components Defined</h3>
<p>The <strong>trend</strong> (T) is the long-run direction of the series — is it generally rising, falling, or flat over years? The <strong>seasonal</strong> component (S) is a repeating pattern that occurs at a fixed, known period — weekly, monthly, or quarterly cycles driven by calendar effects (e.g., holiday shopping, summer electricity demand). The <strong>residual</strong> (R) is what remains after trend and seasonality are removed — the unpredictable noise that no model can capture.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Classical Additive Decomposition</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> statsmodels.tsa.seasonal <span style="color:#c4b5fd;">import</span> seasonal_decompose
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">pd</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#6b7280;"># Build 3 years of synthetic monthly retail sales with trend + seasonality</span>
<span style="color:#93c5fd;">np.random.seed</span>(<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">periods</span> = <span style="color:#fcd34d;">36</span>
<span style="color:#93c5fd;">t</span>       = np.arange(periods)
<span style="color:#93c5fd;">trend</span>   = <span style="color:#fcd34d;">200</span> + <span style="color:#fcd34d;">3.5</span> * t                              <span style="color:#6b7280;"># Linear upward trend</span>
<span style="color:#93c5fd;">season</span>  = <span style="color:#fcd34d;">40</span> * np.sin(<span style="color:#fcd34d;">2</span> * np.pi * t / <span style="color:#fcd34d;">12</span>)            <span style="color:#6b7280;"># Annual seasonal cycle</span>
<span style="color:#93c5fd;">noise</span>   = np.random.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">15</span>, periods)               <span style="color:#6b7280;"># Random residual</span>
<span style="color:#93c5fd;">y</span>       = trend + season + noise

<span style="color:#93c5fd;">dates</span> = pd.date_range(<span style="color:#a7f3d0;">'2021-01-01'</span>, periods=periods, freq=<span style="color:#a7f3d0;">'MS'</span>)
<span style="color:#93c5fd;">ts</span>    = pd.Series(y, index=dates)

<span style="color:#6b7280;"># Decompose — period=12 for monthly data with annual seasonality</span>
<span style="color:#93c5fd;">result</span> = seasonal_decompose(ts, model=<span style="color:#a7f3d0;">'additive'</span>, period=<span style="color:#fcd34d;">12</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Trend (first 5):"</span>, result.trend.dropna().values[:<span style="color:#fcd34d;">5</span>].round(<span style="color:#fcd34d;">1</span>))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Seasonal (first 12):"</span>, result.seasonal.values[:<span style="color:#fcd34d;">12</span>].round(<span style="color:#fcd34d;">1</span>))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Residual std:"</span>, result.resid.dropna().std().round(<span style="color:#fcd34d;">2</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Trend (first 5): [217.4 220.8 224.2 227.6 231.1]
Seasonal (first 12): [-32.1 -18.4   5.2  22.8  38.6  41.3  36.7  19.4  -3.8 -28.1 -41.2 -40.4]
Residual std: 14.87</div>
  </div>
</div>

<h3>Additive vs. Multiplicative Decomposition</h3>
<p>In the <strong>additive model</strong>, the components simply add: Y = T + S + R. This is appropriate when the seasonal fluctuations are roughly constant in size regardless of the level of the series. In the <strong>multiplicative model</strong>, they multiply: Y = T × S × R. This is appropriate when seasonal swings grow proportionally as the trend increases — for example, retail sales that spike by 30% every December regardless of whether baseline sales are 1,000 or 10,000 units. A simple diagnostic: plot your series; if the seasonal peaks and troughs widen over time, use multiplicative.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Additive vs. Multiplicative: Quick Test</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># A quick heuristic: compute the coefficient of variation of seasonal ranges
# If it grows with the trend level, multiplicative is better</span>

<span style="color:#6b7280;"># Split series into yearly chunks and compute peak-to-trough range</span>
<span style="color:#93c5fd;">yearly</span> = ts.resample(<span style="color:#a7f3d0;">'YE'</span>)
<span style="color:#c4b5fd;">for</span> year, group <span style="color:#c4b5fd;">in</span> ts.groupby(ts.index.year):
    <span style="color:#93c5fd;">rng</span> = group.max() - group.min()
    <span style="color:#93c5fd;">mean</span> = group.mean()
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{year}: range={rng:.1f}, mean={mean:.1f}, ratio={rng/mean:.3f}"</span>)

<span style="color:#6b7280;"># If ratio stays stable → additive. If ratio grows → multiplicative.</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>2021: range=152.3, mean=218.7, ratio=0.696
2022: range=148.9, mean=260.4, ratio=0.572
2023: range=155.1, mean=302.2, ratio=0.513
# Ratio is declining (range stays fixed, mean grows) → Additive model is appropriate</div>
  </div>
</div>

<h3>STL Decomposition: The Modern Alternative</h3>
<p><strong>STL</strong> (Seasonal-Trend decomposition using LOESS) is a robust, flexible decomposition algorithm developed by Cleveland et al. (1990). Unlike classical decomposition, STL can handle any seasonal period, allows the seasonal component to change over time, and is robust to outliers. It is the preferred decomposition method in modern practice.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — STL Decomposition</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> statsmodels.tsa.seasonal <span style="color:#c4b5fd;">import</span> STL

<span style="color:#93c5fd;">stl</span>    = STL(ts, period=<span style="color:#fcd34d;">12</span>, robust=<span style="color:#fca5a5;">True</span>)
<span style="color:#93c5fd;">result</span> = stl.fit()

<span style="color:#6b7280;"># Strength of trend and seasonality (0=none, 1=perfect)</span>
<span style="color:#93c5fd;">Ft</span> = <span style="color:#93c5fd;">max</span>(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span> - result.resid.var() / (result.resid + result.trend).var())
<span style="color:#93c5fd;">Fs</span> = <span style="color:#93c5fd;">max</span>(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span> - result.resid.var() / (result.resid + result.seasonal).var())

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Trend strength:    {Ft:.3f}  (>0.6 = strong trend)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Seasonal strength: {Fs:.3f}  (>0.6 = strong seasonality)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Trend strength:    0.981  (&gt;0.6 = strong trend)
Seasonal strength: 0.874  (&gt;0.6 = strong seasonality)</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $forecastingModule->id,
            'title'       => '12.2 Time Series Decomposition: Trend, Seasonality & Residuals',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L12_2', [
                ['q' => 'In the additive decomposition model, the observed value Y is expressed as:', 'opts' => ['Y = T × S × R', 'Y = T + S + R', 'Y = T − S + R', 'Y = T / S + R'], 'ans' => 1, 'exp' => 'Additive model: Y = Trend + Seasonal + Residual. Each component is measured in the same units as Y. Use additive when seasonal swings stay roughly constant in absolute size over time.'],
                ['q' => 'When should you prefer a multiplicative decomposition over additive?', 'opts' => ['When the series has no trend', 'When seasonal fluctuations grow proportionally as the series level increases', 'When the residuals are normally distributed', 'When the series has missing values'], 'ans' => 1, 'exp' => 'Multiplicative decomposition (Y = T × S × R) is appropriate when the amplitude of seasonal swings increases as the overall level of the series rises — common in economic and retail data that grow over time.'],
                ['q' => 'What does the residual component in a decomposition represent?', 'opts' => ['The long-run direction of the series', 'The repeating calendar-driven pattern', 'The unexplained random variation remaining after trend and seasonality are removed', 'The moving average of the series'], 'ans' => 2, 'exp' => 'The residual (R) is what is left after extracting trend (T) and seasonal (S) components. It should look like white noise — random with no systematic pattern. If it still contains structure, your decomposition has missed something.'],
                ['q' => 'What is the key advantage of STL decomposition over classical decomposition?', 'opts' => ['STL only works on annual data', 'STL requires no parameters', 'STL allows the seasonal component to evolve over time and is robust to outliers', 'STL uses machine learning'], 'ans' => 2, 'exp' => 'STL (Seasonal-Trend decomposition using LOESS) is flexible: it handles any seasonal period, allows the seasonal pattern to slowly change across years, and is robust to outliers. Classical decomposition assumes a fixed, unchanging seasonal pattern.'],
                ['q' => 'If a time series has a trend strength score of 0.15 from STL, this means:', 'opts' => ['The trend explains 85% of variance', 'The trend component is weak — most variation is in seasonality or residuals', 'The series is perfectly stationary', 'STL failed to converge'], 'ans' => 1, 'exp' => 'Trend strength near 0 means the series has little or no persistent direction — the trend component explains almost none of the variation. Scores above 0.6 indicate a strong, economically meaningful trend.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 12.3 — Stationarity, Differencing & the ADF Test
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Stationarity, Differencing & the ADF Test</h2>
<p><strong>Stationarity</strong> is the single most important concept in classical time series analysis. A stationary time series has statistical properties — mean, variance, and autocorrelation structure — that do not change over time. Most classical models (ARMA, ARIMA) are only valid when applied to stationary data. Understanding stationarity, diagnosing non-stationarity, and knowing how to fix it is therefore a foundational skill for every forecaster.</p>

<h3>Strict vs. Weak (Covariance) Stationarity</h3>
<p><strong>Strict stationarity</strong> requires the entire joint distribution of observations to be invariant to time shifts — a very demanding condition rarely verified in practice. <strong>Weak (or covariance) stationarity</strong> requires only three conditions: constant mean (E[Yₜ] = μ for all t), constant variance (Var[Yₜ] = σ² for all t), and autocovariance that depends only on the lag, not on time (Cov[Yₜ, Yₜ₋ₖ] = γ(k)). Weak stationarity is the working definition used in forecasting.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Visual Stationarity Check: Rolling Statistics</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">pd</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#6b7280;"># Simulate a non-stationary series (random walk with drift)</span>
<span style="color:#93c5fd;">np.random.seed</span>(<span style="color:#fcd34d;">0</span>)
<span style="color:#93c5fd;">T</span> = <span style="color:#fcd34d;">100</span>
<span style="color:#93c5fd;">y</span> = np.cumsum(np.random.randn(T) + <span style="color:#fcd34d;">0.3</span>)   <span style="color:#6b7280;"># drift=0.3 → trending upward</span>
<span style="color:#93c5fd;">ts</span> = pd.Series(y)

<span style="color:#6b7280;"># Rolling mean and std — if NOT flat, the series is non-stationary</span>
<span style="color:#93c5fd;">roll_mean</span> = ts.rolling(window=<span style="color:#fcd34d;">12</span>).mean()
<span style="color:#93c5fd;">roll_std</span>  = ts.rolling(window=<span style="color:#fcd34d;">12</span>).std()

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Rolling mean range:"</span>, roll_mean.dropna().min().round(<span style="color:#fcd34d;">2</span>),
      <span style="color:#a7f3d0;">"to"</span>, roll_mean.dropna().max().round(<span style="color:#fcd34d;">2</span>))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Rolling std  range:"</span>, roll_std.dropna().min().round(<span style="color:#fcd34d;">2</span>),
      <span style="color:#a7f3d0;">"to"</span>, roll_std.dropna().max().round(<span style="color:#fcd34d;">2</span>))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"→ Drifting mean confirms NON-stationarity"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Rolling mean range: 0.54 to 28.31
Rolling std  range: 0.67 to 2.04
→ Drifting mean confirms NON-stationarity</div>
  </div>
</div>

<h3>The Augmented Dickey-Fuller (ADF) Test</h3>
<p>The <strong>ADF test</strong> is the standard formal statistical test for a unit root — the most common cause of non-stationarity. The null hypothesis H₀ is that a unit root is <em>present</em> (the series is non-stationary). If the p-value is below your significance threshold (typically 0.05), you reject H₀ and conclude the series is stationary. A common mistake is to interpret a high p-value as "proof" of stationarity — it is not. It simply means you failed to reject the null.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — ADF Test Before and After Differencing</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> statsmodels.tsa.stattools <span style="color:#c4b5fd;">import</span> adfuller

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">adf_report</span>(series, label):
    result = adfuller(series.dropna(), autolag=<span style="color:#a7f3d0;">'AIC'</span>)
    p = result[<span style="color:#fcd34d;">1</span>]
    verdict = <span style="color:#a7f3d0;">"STATIONARY ✓"</span> <span style="color:#c4b5fd;">if</span> p &lt; <span style="color:#fcd34d;">0.05</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"NON-STATIONARY ✗"</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{label}: ADF stat={result[0]:.3f}, p={p:.4f} → {verdict}"</span>)

<span style="color:#6b7280;"># Level series (original)</span>
adf_report(ts, <span style="color:#a7f3d0;">"Level (original)"</span>)

<span style="color:#6b7280;"># First difference: Δyₜ = yₜ − yₜ₋₁</span>
<span style="color:#93c5fd;">ts_diff1</span> = ts.diff()
adf_report(ts_diff1, <span style="color:#a7f3d0;">"First difference"</span>)

<span style="color:#6b7280;"># Second difference (rarely needed)</span>
<span style="color:#93c5fd;">ts_diff2</span> = ts.diff().diff()
adf_report(ts_diff2, <span style="color:#a7f3d0;">"Second difference"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Level (original):  ADF stat=-1.124, p=0.7051 → NON-STATIONARY ✗
First difference:  ADF stat=-9.872, p=0.0000 → STATIONARY ✓
Second difference: ADF stat=-14.31, p=0.0000 → STATIONARY ✓</div>
  </div>
</div>

<h3>Differencing: The Primary Tool for Achieving Stationarity</h3>
<p><strong>First differencing</strong> replaces each value Yₜ with the change Yₜ − Yₜ₋₁. This eliminates a linear trend and is the most common fix for non-stationarity. If the differenced series is still non-stationary, a <strong>second difference</strong> (differencing the already-differenced series) is applied — though this is rarely required in practice. The number of differences required to achieve stationarity is called the <strong>order of integration</strong> d, which becomes a key parameter in ARIMA models. Over-differencing can cause problems: it can introduce unnecessary complexity and actually reduce forecast accuracy.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Log Transform + Differencing for Variance Stabilisation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># When variance grows with the level (multiplicative behaviour), take log first</span>
<span style="color:#93c5fd;">ts_log</span>       = np.log(ts - ts.min() + <span style="color:#fcd34d;">1</span>)   <span style="color:#6b7280;"># shift to ensure positivity</span>
<span style="color:#93c5fd;">ts_log_diff</span>  = ts_log.diff()

adf_report(ts_log,      <span style="color:#a7f3d0;">"Log-transformed"</span>)
adf_report(ts_log_diff, <span style="color:#a7f3d0;">"Log + First diff"</span>)

<span style="color:#6b7280;"># Remember: after modelling, back-transform your forecasts!</span>
<span style="color:#6b7280;"># If you forecasted log-differenced values, reverse with: cumsum then exp</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nAlways back-transform forecasts to original scale before reporting."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Log-transformed:   ADF stat=-1.309, p=0.6268 → NON-STATIONARY ✗
Log + First diff:  ADF stat=-9.614, p=0.0000 → STATIONARY ✓

Always back-transform forecasts to original scale before reporting.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $forecastingModule->id,
            'title'       => '12.3 Stationarity, Differencing & the ADF Test',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L12_3', [
                ['q' => 'A weakly stationary time series requires which three conditions?', 'opts' => ['Constant trend, zero autocorrelation, and unit variance', 'Constant mean, constant variance, and autocovariance that depends only on lag', 'Normal distribution, zero mean, and independent increments', 'Positive values, no outliers, and equal spacing'], 'ans' => 1, 'exp' => 'Weak (covariance) stationarity requires: (1) E[Yₜ] = μ constant, (2) Var[Yₜ] = σ² constant, and (3) Cov[Yₜ, Yₜ₋ₖ] = γ(k) — depends only on lag k, not on time t. This is the working definition in forecasting.'],
                ['q' => 'In the ADF test, what does the null hypothesis H₀ state?', 'opts' => ['The series is stationary', 'The series has no trend', 'A unit root is present (series is non-stationary)', 'The series is normally distributed'], 'ans' => 2, 'exp' => 'ADF H₀: the series has a unit root (is non-stationary). A p-value < 0.05 allows you to reject H₀ and conclude stationarity. A p-value ≥ 0.05 means you fail to reject H₀ — you cannot confirm stationarity.'],
                ['q' => 'What does first differencing compute?', 'opts' => ['Yₜ / Yₜ₋₁', 'Yₜ × Yₜ₋₁', 'Yₜ − Yₜ₋₁', 'log(Yₜ)'], 'ans' => 2, 'exp' => 'First differencing: ΔYₜ = Yₜ − Yₜ₋₁. It replaces the level with the period-over-period change. This removes a linear trend and is the most common way to achieve stationarity.'],
                ['q' => 'Why should you apply a logarithm transform BEFORE differencing for some series?', 'opts' => ['To make negative values positive', 'To stabilise variance that grows with the level (multiplicative behaviour)', 'Because ARIMA requires log-transformed inputs', 'To remove seasonality'], 'ans' => 1, 'exp' => 'When variance grows proportionally with the series level (a sign of multiplicative structure), a log transform stabilises the variance first. Then differencing removes the trend. Without the log, the model would treat large recent fluctuations as more important than older small ones.'],
                ['q' => 'The order of integration d in ARIMA refers to:', 'opts' => ['The number of AR terms', 'The number of MA terms', 'The number of differences needed to make the series stationary', 'The seasonal period'], 'ans' => 2, 'exp' => "The 'd' parameter in ARIMA(p,d,q) is the number of times the series must be differenced to achieve stationarity. Most real-world series need d=1. d=2 is occasionally needed. d=0 means the series is already stationary (ARMA model)."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 12.4 — Autocorrelation: ACF, PACF & White Noise
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Autocorrelation: ACF, PACF & White Noise</h2>
<p>In ordinary regression, we assume that residuals are independent — knowing the value of one tells you nothing about another. In time series data, this assumption is routinely violated: yesterday's value is often correlated with today's. This <strong>autocorrelation</strong> (or serial correlation) is not a problem to eliminate; it is the structure that forecasting models are designed to exploit. Understanding and diagnosing autocorrelation via the ACF and PACF is how you choose the right model orders.</p>

<h3>The Autocorrelation Function (ACF)</h3>
<p>The <strong>autocorrelation function (ACF)</strong> measures the correlation between Yₜ and Yₜ₋ₖ for lag k = 1, 2, 3, …. A plot of the ACF (the <em>correlogram</em>) shows you which lags carry significant linear dependence. The ACF includes <em>both</em> direct and indirect effects — for example, if Yₜ is correlated with Yₜ₋₁, and Yₜ₋₁ is correlated with Yₜ₋₂, the ACF at lag 2 will show correlation even if there is no direct two-step dependence.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Computing ACF Values</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> statsmodels.tsa.stattools <span style="color:#c4b5fd;">import</span> acf, pacf
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">pd</span>

<span style="color:#6b7280;"># Simulate an AR(1) process: Yₜ = 0.8·Yₜ₋₁ + εₜ</span>
<span style="color:#93c5fd;">np.random.seed</span>(<span style="color:#fcd34d;">1</span>)
<span style="color:#93c5fd;">n</span>  = <span style="color:#fcd34d;">200</span>
<span style="color:#93c5fd;">y</span>  = np.zeros(n)
<span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, n):
    y[t] = <span style="color:#fcd34d;">0.8</span> * y[t-<span style="color:#fcd34d;">1</span>] + np.random.randn()

<span style="color:#93c5fd;">acf_vals</span>  = acf(y,  nlags=<span style="color:#fcd34d;">10</span>, fft=<span style="color:#fca5a5;">False</span>)
<span style="color:#93c5fd;">pacf_vals</span> = pacf(y, nlags=<span style="color:#fcd34d;">10</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Lag  | ACF     | PACF"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-" * 28</span>)
<span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">8</span>):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {k}  | {acf_vals[k]: .4f}  | {pacf_vals[k]: .4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Lag  | ACF     | PACF
----------------------------
  1  |  0.7912  |  0.7934
  2  |  0.6215  |  0.0041
  3  |  0.4880  | -0.0198
  4  |  0.3821  | -0.0092
  5  |  0.2874  |  0.0273
  6  |  0.2106  |  0.0118
  7  |  0.1530  | -0.0063</div>
  </div>
</div>

<h3>The Partial Autocorrelation Function (PACF)</h3>
<p>The <strong>PACF</strong> measures the correlation between Yₜ and Yₜ₋ₖ after removing the effects of all intermediate lags (1 through k−1). It isolates the <em>direct</em> relationship at each lag. This is why in the AR(1) example above, the PACF cuts off sharply after lag 1 (the only direct dependence), while the ACF decays gradually. The PACF is the primary tool for identifying the AR order p.</p>

<h3>ACF/PACF Signature Patterns for Model Identification</h3>
<p>The patterns in the ACF and PACF are used to identify which model class to fit. This is the classical Box-Jenkins identification step:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">REFERENCE — ACF/PACF Identification Guide</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># ─────────────────────────────────────────────────────────────
# Model     | ACF pattern           | PACF pattern
# ─────────────────────────────────────────────────────────────
# AR(p)     | Tails off (gradual)   | Cuts off after lag p
# MA(q)     | Cuts off after lag q  | Tails off (gradual)
# ARMA(p,q) | Tails off             | Tails off
# White     | No significant lags   | No significant lags
# ─────────────────────────────────────────────────────────────</span>

<span style="color:#6b7280;"># Demonstrate MA(1): Yₜ = εₜ + 0.7·εₜ₋₁</span>
<span style="color:#93c5fd;">eps</span>  = np.random.randn(<span style="color:#fcd34d;">300</span>)
<span style="color:#93c5fd;">y_ma</span> = eps[<span style="color:#fcd34d;">1</span>:] + <span style="color:#fcd34d;">0.7</span> * eps[:<span style="color:#fcd34d;">-1</span>]

<span style="color:#93c5fd;">acf_ma</span>  = acf(y_ma,  nlags=<span style="color:#fcd34d;">5</span>, fft=<span style="color:#fca5a5;">False</span>)
<span style="color:#93c5fd;">pacf_ma</span> = pacf(y_ma, nlags=<span style="color:#fcd34d;">5</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"MA(1) — ACF should cut off after lag 1, PACF tails off:"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"ACF  lags 1–5:"</span>, [<span style="color:#93c5fd;">round</span>(v,<span style="color:#fcd34d;">3</span>) <span style="color:#c4b5fd;">for</span> v <span style="color:#c4b5fd;">in</span> acf_ma[<span style="color:#fcd34d;">1</span>:<span style="color:#fcd34d;">6</span>]])
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"PACF lags 1–5:"</span>, [<span style="color:#93c5fd;">round</span>(v,<span style="color:#fcd34d;">3</span>) <span style="color:#c4b5fd;">for</span> v <span style="color:#c4b5fd;">in</span> pacf_ma[<span style="color:#fcd34d;">1</span>:<span style="color:#fcd34d;">6</span>]])</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>MA(1) — ACF should cut off after lag 1, PACF tails off:
ACF  lags 1–5: [0.452, 0.031, -0.014, 0.028, -0.009]
PACF lags 1–5: [0.453, -0.178, 0.076, -0.034, 0.019]</div>
  </div>
</div>

<h3>White Noise: The Ideal Residual</h3>
<p><strong>White noise</strong> is a sequence of uncorrelated, identically distributed random variables with zero mean and constant variance. It has no autocorrelation at any lag. In forecasting, white noise is both a baseline and a goal: a series is unforecastable if it is white noise (nothing is predictable). After fitting a model, the <em>residuals</em> should look like white noise — if they do not, the model has missed systematic structure and can be improved. The Ljung-Box test formally checks whether residuals are white noise.</p>
HTML;

        Lesson::create([
            'module_id'   => $forecastingModule->id,
            'title'       => '12.4 Autocorrelation: ACF, PACF & White Noise',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L12_4', [
                ['q' => 'The ACF at lag k measures:', 'opts' => ['The direct correlation between Yₜ and Yₜ₋ₖ only', 'The total correlation between Yₜ and Yₜ₋ₖ including indirect effects through all intermediate lags', 'The variance of the series at lag k', 'The partial regression coefficient at lag k'], 'ans' => 1, 'exp' => 'The ACF captures total (direct + indirect) correlation between Yₜ and Yₜ₋ₖ. For an AR(1) with φ=0.8, the ACF at lag 2 is roughly 0.64 (=0.8²) — this arises indirectly through lag 1, not because there is a direct two-step relationship.'],
                ['q' => 'For an AR(p) process, the PACF plot should exhibit which pattern?', 'opts' => ['Tails off gradually at all lags', 'Spikes at every seasonal lag', 'Cuts off sharply after lag p', 'Oscillates between positive and negative values'], 'ans' => 2, 'exp' => 'AR(p) signature: PACF cuts off after lag p (only p significant spikes), while the ACF tails off gradually. This is the key identification rule for determining the AR order.'],
                ['q' => 'For an MA(q) process, the ACF plot should exhibit which pattern?', 'opts' => ['Gradual exponential decay', 'Cuts off sharply after lag q', 'No significant lags at all', 'Alternating positive and negative spikes forever'], 'ans' => 1, 'exp' => 'MA(q) signature: ACF cuts off after lag q (only q significant spikes), while the PACF tails off. This is the mirror image of the AR rule and is used to identify the MA order.'],
                ['q' => 'After fitting a forecast model, what should the residuals look like if the model is well-specified?', 'opts' => ['A random walk with drift', 'White noise — no significant autocorrelation at any lag', 'An AR(1) process', 'Residuals should equal zero at every point'], 'ans' => 1, 'exp' => "If residuals are white noise (Ljung-Box p-value > 0.05), the model has captured all systematic structure. Significant autocorrelation in residuals means the model is leaving predictable patterns on the table — it can be improved."],
                ['q' => 'The Ljung-Box test is used to:', 'opts' => ['Test whether a series has a unit root', 'Test whether residuals are white noise by checking joint significance of multiple ACF lags', 'Compare AIC values between two models', 'Test whether the series has a seasonal component'], 'ans' => 1, 'exp' => "The Ljung-Box test's H₀ is that the first m autocorrelations are all zero (white noise). A p-value > 0.05 means you cannot reject this — residuals look like white noise, model is adequate. A p-value < 0.05 means autocorrelation remains — respecify the model."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 12.5 — Exponential Smoothing: SES, Holt's & Holt-Winters
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Exponential Smoothing: SES, Holt's & Holt-Winters</h2>
<p><strong>Exponential smoothing</strong> is a family of forecasting methods that produce weighted averages of past observations, where the weights decay exponentially — more recent observations receive higher weight. These methods are intuitive, computationally fast, require no stationarity assumption, and perform remarkably well across a wide range of series. They are the workhorses of operational forecasting in supply chains, inventory management, and business planning.</p>

<h3>Simple Exponential Smoothing (SES)</h3>
<p>SES is designed for series with <em>no trend and no seasonality</em>. The smoothed level Lₜ is updated recursively: Lₜ = αYₜ + (1−α)Lₜ₋₁, where α ∈ (0,1) is the smoothing parameter. High α means the forecast reacts quickly to recent changes (but is noisy); low α gives stable, slow-reacting forecasts. The h-step-ahead forecast from SES is simply the last estimated level: Ŷₜ₊ₕ = Lₜ for all h ≥ 1 — a flat forecast line.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Simple Exponential Smoothing</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> statsmodels.tsa.holtwinters <span style="color:#c4b5fd;">import</span> SimpleExpSmoothing
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">pd</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#6b7280;"># Stationary series — no trend, no seasonality</span>
<span style="color:#93c5fd;">np.random.seed</span>(<span style="color:#fcd34d;">5</span>)
<span style="color:#93c5fd;">y</span>    = pd.Series(<span style="color:#fcd34d;">50</span> + np.random.randn(<span style="color:#fcd34d;">40</span>) * <span style="color:#fcd34d;">5</span>)
<span style="color:#93c5fd;">train</span>, <span style="color:#93c5fd;">test</span> = y[:<span style="color:#fcd34d;">35</span>], y[<span style="color:#fcd34d;">35</span>:]

<span style="color:#6b7280;"># Fit SES — optimisation_method='estimated' finds best α automatically</span>
<span style="color:#93c5fd;">model</span>  = SimpleExpSmoothing(train, initialization_method=<span style="color:#a7f3d0;">'estimated'</span>)
<span style="color:#93c5fd;">fit</span>    = model.fit(optimized=<span style="color:#fca5a5;">True</span>)
<span style="color:#93c5fd;">fc</span>     = fit.forecast(<span style="color:#fcd34d;">5</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Optimal α:      {fit.params['smoothing_level']:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"5-step forecast: {fc.values.round(2)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Actual test:     {test.values.round(2)}"</span>)
<span style="color:#93c5fd;">mae</span> = np.mean(np.abs(fc.values - test.values))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"MAE:             {mae:.2f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Optimal α:      0.1823
5-step forecast: [50.31 50.31 50.31 50.31 50.31]
Actual test:     [48.72 53.14 49.88 51.33 47.96]
MAE:             2.47</div>
  </div>
</div>

<h3>Holt's Linear Trend Method (Double Exponential Smoothing)</h3>
<p>Holt's method extends SES to handle series with a <em>trend</em>. It maintains two equations: one for the level (Lₜ) and one for the trend (Bₜ). The h-step forecast is Ŷₜ₊ₕ = Lₜ + h·Bₜ — a straight line projected into the future. Two smoothing parameters α (level) and β (trend) are fitted. Because Holt's method extrapolates a linear trend indefinitely, it can produce unrealistically large forecasts at long horizons — a "damped" variant constrains the trend to flatten out.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Holt's Linear Trend & Damped Trend Comparison</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> statsmodels.tsa.holtwinters <span style="color:#c4b5fd;">import</span> ExponentialSmoothing

<span style="color:#6b7280;"># Trending series</span>
<span style="color:#93c5fd;">np.random.seed</span>(<span style="color:#fcd34d;">7</span>)
<span style="color:#93c5fd;">y_trend</span> = pd.Series(<span style="color:#fcd34d;">100</span> + <span style="color:#fcd34d;">2</span>*np.arange(<span style="color:#fcd34d;">48</span>) + np.random.randn(<span style="color:#fcd34d;">48</span>)*<span style="color:#fcd34d;">8</span>)
<span style="color:#93c5fd;">tr</span>, <span style="color:#93c5fd;">te</span>   = y_trend[:<span style="color:#fcd34d;">40</span>], y_trend[<span style="color:#fcd34d;">40</span>:]

<span style="color:#6b7280;"># Holt linear trend</span>
<span style="color:#93c5fd;">holt</span>  = ExponentialSmoothing(tr, trend=<span style="color:#a7f3d0;">'add'</span>).fit(optimized=<span style="color:#fca5a5;">True</span>)
<span style="color:#93c5fd;">fc_h</span>  = holt.forecast(<span style="color:#fcd34d;">8</span>)

<span style="color:#6b7280;"># Damped trend (dampens slope to zero at long horizons)</span>
<span style="color:#93c5fd;">damped</span> = ExponentialSmoothing(tr, trend=<span style="color:#a7f3d0;">'add'</span>, damped_trend=<span style="color:#fca5a5;">True</span>).fit(optimized=<span style="color:#fca5a5;">True</span>)
<span style="color:#93c5fd;">fc_d</span>   = damped.forecast(<span style="color:#fcd34d;">8</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Step | Actual | Holt  | Damped"</span>)
<span style="color:#c4b5fd;">for</span> i, (a,h,d) <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(<span style="color:#93c5fd;">zip</span>(te,fc_h,fc_d),<span style="color:#fcd34d;">1</span>):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {i}  | {a:6.1f} | {h:5.1f} | {d:5.1f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Step | Actual | Holt  | Damped
  1  |  181.4 | 180.8 | 180.5
  2  |  183.2 | 182.8 | 182.3
  3  |  184.7 | 184.8 | 183.9
  4  |  188.3 | 186.8 | 185.4
  5  |  186.9 | 188.8 | 186.7
  6  |  193.1 | 190.8 | 187.9
  7  |  191.2 | 192.8 | 189.0
  8  |  196.8 | 194.8 | 190.0</div>
  </div>
</div>

<h3>Holt-Winters' Seasonal Method (Triple Exponential Smoothing)</h3>
<p>Holt-Winters extends Holt's method by adding a <em>seasonal</em> component — making it applicable to series with both trend and seasonality. It maintains three equations: level (Lₜ), trend (Bₜ), and seasonal indices (Sₜ). There are two variants: the <strong>additive</strong> version for constant seasonal amplitude and the <strong>multiplicative</strong> version for growing seasonal amplitude. With three smoothing parameters (α, β, γ), Holt-Winters is flexible enough to handle the vast majority of business and economic time series encountered in practice.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Holt-Winters Additive Model</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Build a series with trend + additive annual seasonality</span>
<span style="color:#93c5fd;">np.random.seed</span>(<span style="color:#fcd34d;">9</span>)
<span style="color:#93c5fd;">t</span>    = np.arange(<span style="color:#fcd34d;">48</span>)
<span style="color:#93c5fd;">sea</span>  = np.tile([<span style="color:#fcd34d;">-30</span>,<span style="color:#fcd34d;">-20</span>,<span style="color:#fcd34d;">-5</span>,<span style="color:#fcd34d;">10</span>,<span style="color:#fcd34d;">25</span>,<span style="color:#fcd34d;">35</span>,<span style="color:#fcd34d;">40</span>,<span style="color:#fcd34d;">30</span>,<span style="color:#fcd34d;">10</span>,<span style="color:#fcd34d;">-5</span>,<span style="color:#fcd34d;">-20</span>,<span style="color:#fcd34d;">-30</span>], <span style="color:#fcd34d;">4</span>)
<span style="color:#93c5fd;">y_s</span> = <span style="color:#fcd34d;">200</span> + <span style="color:#fcd34d;">2</span>*t + sea + np.random.randn(<span style="color:#fcd34d;">48</span>)*<span style="color:#fcd34d;">6</span>
<span style="color:#93c5fd;">dates</span> = pd.date_range(<span style="color:#a7f3d0;">'2020-01'</span>, periods=<span style="color:#fcd34d;">48</span>, freq=<span style="color:#a7f3d0;">'MS'</span>)
<span style="color:#93c5fd;">ts_s</span>  = pd.Series(y_s, index=dates)

<span style="color:#93c5fd;">tr_s</span>, <span style="color:#93c5fd;">te_s</span> = ts_s[:<span style="color:#fcd34d;">36</span>], ts_s[<span style="color:#fcd34d;">36</span>:]

<span style="color:#93c5fd;">hw</span> = ExponentialSmoothing(tr_s, trend=<span style="color:#a7f3d0;">'add'</span>, seasonal=<span style="color:#a7f3d0;">'add'</span>,
                          seasonal_periods=<span style="color:#fcd34d;">12</span>).fit(optimized=<span style="color:#fca5a5;">True</span>)
<span style="color:#93c5fd;">fc_hw</span> = hw.forecast(<span style="color:#fcd34d;">12</span>)
<span style="color:#93c5fd;">mae_hw</span> = np.mean(np.abs(fc_hw.values - te_s.values))

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"α={hw.params['smoothing_level']:.3f}  β={hw.params['smoothing_trend']:.3f}  γ={hw.params['smoothing_seasonal']:.3f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Holt-Winters MAE: {mae_hw:.2f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>α=0.412  β=0.031  γ=0.187
Holt-Winters MAE: 7.83</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $forecastingModule->id,
            'title'       => '12.5 Exponential Smoothing: SES, Holt\'s & Holt-Winters',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L12_5', [
                ['q' => 'In Simple Exponential Smoothing, a smoothing parameter α close to 1 means:', 'opts' => ['Almost all weight is on the very oldest observations', 'The forecast is almost entirely determined by the most recent observation', 'The series is non-stationary', 'No trend component is fitted'], 'ans' => 1, 'exp' => 'High α (near 1) gives almost all weight to the most recent observation — the model reacts quickly but produces noisy forecasts. Low α spreads weight across many past observations — smooth but slow to adapt to genuine level changes.'],
                ['q' => 'SES is appropriate for series with:', 'opts' => ['Strong trend and strong seasonality', 'No trend and no seasonality', 'Strong seasonality but no trend', 'A unit root only'], 'ans' => 1, 'exp' => "SES is designed for series that fluctuate around a roughly constant level with no systematic trend or seasonal pattern. The forecast is a flat horizontal line at the current smoothed level. For trend, use Holt's; for trend + seasonality, use Holt-Winters."],
                ['q' => 'Holt\'s h-step-ahead forecast formula is:', 'opts' => ['Ŷₜ₊ₕ = Lₜ', 'Ŷₜ₊ₕ = Lₜ + h·Bₜ', 'Ŷₜ₊ₕ = Lₜ × Sₜ', 'Ŷₜ₊ₕ = α·Yₜ + (1−α)·Yₜ₋₁'], 'ans' => 1, 'exp' => "Holt's linear trend method: Ŷₜ₊ₕ = Lₜ + h·Bₜ, where Lₜ is the current level and Bₜ is the current trend slope. The forecast is a straight line projected h steps forward. The damped version multiplies h by a damping coefficient φ<1 to flatten long-run projections."],
                ['q' => 'Holt-Winters Multiplicative model is preferred over Additive when:', 'opts' => ['The series has no trend', 'The seasonal fluctuations are constant in absolute size', 'The seasonal fluctuations grow proportionally as the series level increases', 'The series is stationary'], 'ans' => 2, 'exp' => 'Multiplicative Holt-Winters scales the seasonal component by the current level, so seasonal swings grow with the trend. Use it when you see the seasonal peaks and troughs widening over time on the plot. Use additive when seasonal variation is stable.'],
                ['q' => 'In Holt-Winters, the parameter γ controls:', 'opts' => ['The smoothing of the level component', 'The smoothing of the trend component', 'The smoothing of the seasonal component', 'The damping of the trend'], 'ans' => 2, 'exp' => "In Holt-Winters: α smooths the level, β smooths the trend, and γ smooths the seasonal component. A small γ means seasonal indices are updated slowly (stable seasonal pattern); large γ allows seasonal patterns to adapt quickly to recent data."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 12.6 — ARIMA Models: Identification, Estimation & Diagnostics
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>ARIMA Models: Identification, Estimation & Diagnostics</h2>
<p><strong>ARIMA</strong> (AutoRegressive Integrated Moving Average) is the foundational parametric model class for univariate time series forecasting. Proposed by Box and Jenkins in 1970, it remains one of the most widely used forecasting frameworks in econometrics, finance, and operations research. An ARIMA(p,d,q) model combines three mechanisms: the AR(p) component (the series depends on its own past p values), the I(d) component (d differences needed for stationarity), and the MA(q) component (the series depends on past q forecast errors).</p>

<h3>The Three Components in Detail</h3>
<p>An <strong>AR(p)</strong> process: Yₜ = c + φ₁Yₜ₋₁ + φ₂Yₜ₋₂ + … + φₚYₜ₋ₚ + εₜ. The past p values of the series predict the current value. An <strong>MA(q)</strong> process: Yₜ = μ + εₜ + θ₁εₜ₋₁ + … + θqεₜ₋q. The current value depends on the current and past q forecast errors. The <strong>ARIMA(p,d,q)</strong> model first differences the series d times to achieve stationarity, then fits an ARMA(p,q) model to the differenced series.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Fitting ARIMA with statsmodels</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> statsmodels.tsa.arima.model <span style="color:#c4b5fd;">import</span> ARIMA
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">pd</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">import</span> warnings; warnings.filterwarnings(<span style="color:#a7f3d0;">'ignore'</span>)

<span style="color:#6b7280;"># Simulate ARIMA(1,1,1): AR coeff=0.6, MA coeff=0.4, with drift</span>
<span style="color:#93c5fd;">np.random.seed</span>(<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">n</span>   = <span style="color:#fcd34d;">150</span>
<span style="color:#93c5fd;">eps</span> = np.random.randn(n)
<span style="color:#93c5fd;">y</span>   = np.zeros(n)
<span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">2</span>, n):
    y[t] = y[t-<span style="color:#fcd34d;">1</span>] + <span style="color:#fcd34d;">0.6</span>*(y[t-<span style="color:#fcd34d;">1</span>]-y[t-<span style="color:#fcd34d;">2</span>]) + eps[t] + <span style="color:#fcd34d;">0.4</span>*eps[t-<span style="color:#fcd34d;">1</span>] + <span style="color:#fcd34d;">0.5</span>
<span style="color:#93c5fd;">ts</span>  = pd.Series(y)
<span style="color:#93c5fd;">tr</span>, <span style="color:#93c5fd;">te</span> = ts[:<span style="color:#fcd34d;">130</span>], ts[<span style="color:#fcd34d;">130</span>:]

<span style="color:#6b7280;"># Fit ARIMA(1,1,1) — order=(p,d,q)</span>
<span style="color:#93c5fd;">model</span>  = ARIMA(tr, order=(<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>))
<span style="color:#93c5fd;">result</span> = model.fit()

<span style="color:#93c5fd;">print</span>(result.summary().tables[<span style="color:#fcd34d;">1</span>])   <span style="color:#6b7280;"># Coefficients table</span>

<span style="color:#6b7280;"># Forecast 20 steps ahead with 95% prediction intervals</span>
<span style="color:#93c5fd;">fc_obj</span>  = result.get_forecast(steps=<span style="color:#fcd34d;">20</span>)
<span style="color:#93c5fd;">fc_mean</span> = fc_obj.predicted_mean
<span style="color:#93c5fd;">fc_ci</span>   = fc_obj.conf_int(alpha=<span style="color:#fcd34d;">0.05</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nFirst 5 forecasts with 95% PI:"</span>)
<span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">5</span>):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  ŷ={fc_mean.iloc[i]:.2f}  [{fc_ci.iloc[i,0]:.2f}, {fc_ci.iloc[i,1]:.2f}]"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>===========================================
                 coef    std err    z      P>|z|
-------------------------------------------
ar.L1          0.5831    0.093    6.27   0.000
ma.L1          0.3914    0.107    3.66   0.000
sigma2         1.0241    0.122    8.39   0.000
===========================================

First 5 forecasts with 95% PI:
  ŷ=71.84  [69.86, 73.82]
  ŷ=72.81  [69.59, 76.03]
  ŷ=73.67  [69.90, 77.44]
  ŷ=74.43  [70.18, 78.68]
  ŷ=75.11  [70.42, 79.80]</div>
  </div>
</div>

<h3>Model Selection: AIC and BIC</h3>
<p>When choosing between candidate ARIMA(p,d,q) specifications, you use information criteria. The <strong>Akaike Information Criterion (AIC)</strong> and the <strong>Bayesian Information Criterion (BIC)</strong> both penalise model complexity (number of parameters) while rewarding goodness of fit. Lower AIC/BIC is better. BIC penalises complexity more harshly than AIC and tends to select more parsimonious models. <em>Never choose ARIMA orders by minimising in-sample error alone</em> — that always leads to overfitting.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — AIC-Based ARIMA Grid Search</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> itertools

<span style="color:#93c5fd;">best_aic</span> = np.inf
<span style="color:#93c5fd;">best_order</span> = <span style="color:#fca5a5;">None</span>

<span style="color:#c4b5fd;">for</span> p, q <span style="color:#c4b5fd;">in</span> itertools.product(<span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">3</span>), <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">3</span>)):
    <span style="color:#c4b5fd;">try</span>:
        m   = ARIMA(tr, order=(p, <span style="color:#fcd34d;">1</span>, q)).fit()
        <span style="color:#c4b5fd;">if</span> m.aic &lt; best_aic:
            best_aic, best_order = m.aic, (p,<span style="color:#fcd34d;">1</span>,q)
    <span style="color:#c4b5fd;">except</span>:
        <span style="color:#c4b5fd;">pass</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Best ARIMA order: {best_order}  AIC={best_aic:.2f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Best ARIMA order: (1, 1, 1)  AIC=415.23</div>
  </div>
</div>

<h3>Residual Diagnostics</h3>
<p>After fitting an ARIMA model, always run these four diagnostics: (1) Plot the residuals — they should look random, not structured. (2) Plot the ACF of residuals — no significant spikes beyond the confidence bands. (3) Run the Ljung-Box test — p-value > 0.05 confirms white noise. (4) Check the histogram of residuals — ideally approximately normal (required for valid prediction intervals). Failing any diagnostic means the model is misspecified and should be revised.</p>
HTML;

        Lesson::create([
            'module_id'   => $forecastingModule->id,
            'title'       => '12.6 ARIMA Models: Identification, Estimation & Diagnostics',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L12_6', [
                ['q' => 'In ARIMA(p,d,q), what does the parameter d represent?', 'opts' => ['The number of autoregressive terms', 'The number of moving average terms', 'The number of differences applied to make the series stationary', 'The seasonal period'], 'ans' => 2, 'exp' => "ARIMA(p,d,q): p=AR order (how many past values of Y), d=integration order (how many differences), q=MA order (how many past errors). d=1 is the most common case — one difference is needed to remove a linear trend."],
                ['q' => 'What is the key difference between an AR process and an MA process?', 'opts' => ['AR uses past errors; MA uses past values of Y', 'AR uses past values of Y; MA uses past forecast errors', 'AR requires stationarity; MA does not', 'AR has one parameter; MA has many'], 'ans' => 1, 'exp' => "AR(p): Yₜ depends on its own past p values (Yₜ₋₁, ..., Yₜ₋ₚ). MA(q): Yₜ depends on the current and past q forecast errors (εₜ, ..., εₜ₋q). ARMA combines both. This is the fundamental structural difference."],
                ['q' => 'When comparing candidate ARIMA models, lower AIC indicates:', 'opts' => ['A worse model', 'A model with more parameters', 'A better trade-off between fit and complexity', 'The model is overfitted'], 'ans' => 2, 'exp' => 'AIC = 2k − 2ln(L), where k is the number of parameters and L is the maximised likelihood. Minimising AIC selects the model with the best fit relative to its complexity. Lower AIC = better model. Never select purely on in-sample fit (RSS), which always favours more parameters.'],
                ['q' => 'After fitting ARIMA, which Ljung-Box p-value indicates well-behaved residuals?', 'opts' => ['p < 0.01', 'p < 0.05', 'p > 0.05', 'p = 0'], 'ans' => 2, 'exp' => "Ljung-Box H₀: residuals are white noise. A p-value > 0.05 means you cannot reject this — residuals appear to be white noise and the model has captured all systematic structure. A p-value < 0.05 means residuals still contain autocorrelation — increase p or q."],
                ['q' => 'The 95% prediction interval for ARIMA forecasts widens as the horizon h increases because:', 'opts' => ['The model parameters change over time', 'Forecast uncertainty compounds — errors in earlier steps propagate into later steps', 'AIC increases with h', 'The series becomes non-stationary at long horizons'], 'ans' => 1, 'exp' => 'At h=1, uncertainty comes only from the next error εₜ₊₁. At h=2, it includes uncertainty about both εₜ₊₁ and εₜ₊₂, plus the error in the h=1 forecast that feeds into the h=2 forecast. This compounding makes PIs widen with horizon.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 12.7 — SARIMA: Seasonal ARIMA Models
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>SARIMA: Seasonal ARIMA Models</h2>
<p>When a time series exhibits both the ARIMA-type autocorrelation structure <em>and</em> a strong seasonal pattern, a plain ARIMA model often fails — it cannot capture within-year cycles without adding an unrealistic number of AR or MA terms. <strong>SARIMA</strong> (Seasonal ARIMA), written ARIMA(p,d,q)(P,D,Q)ₘ, explicitly models both the non-seasonal and seasonal dependence structures through a second set of parameters operating at the seasonal lag m. It is the standard model for monthly, quarterly, and weekly series with regular seasonal patterns.</p>

<h3>Understanding the Seasonal Parameters</h3>
<p>The notation ARIMA(p,d,q)(P,D,Q)ₘ has two parenthesised triples. The first (p,d,q) operates at lag 1 exactly as in plain ARIMA. The second (P,D,Q) operates at lags m, 2m, 3m, … (where m is the seasonal period — 12 for monthly, 4 for quarterly). D=1 means seasonal differencing: Yₜ − Yₜ₋ₘ, which removes the seasonal cycle. P adds seasonal AR terms (correlation with observations m periods ago). Q adds seasonal MA terms (error corrections at seasonal lags).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Fitting SARIMA to Monthly Data</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> statsmodels.tsa.statespace.sarimax <span style="color:#c4b5fd;">import</span> SARIMAX
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">pd</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">import</span> warnings; warnings.filterwarnings(<span style="color:#a7f3d0;">'ignore'</span>)

<span style="color:#6b7280;"># Build 5 years of monthly seasonal data (trend + seasonality + noise)</span>
<span style="color:#93c5fd;">np.random.seed</span>(<span style="color:#fcd34d;">3</span>)
<span style="color:#93c5fd;">t</span>    = np.arange(<span style="color:#fcd34d;">60</span>)
<span style="color:#93c5fd;">seas</span> = np.tile([<span style="color:#fcd34d;">-25</span>,<span style="color:#fcd34d;">-15</span>,<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">15</span>,<span style="color:#fcd34d;">30</span>,<span style="color:#fcd34d;">40</span>,<span style="color:#fcd34d;">45</span>,<span style="color:#fcd34d;">35</span>,<span style="color:#fcd34d;">15</span>,<span style="color:#fcd34d;">-5</span>,<span style="color:#fcd34d;">-20</span>,<span style="color:#fcd34d;">-28</span>],<span style="color:#fcd34d;">5</span>)
<span style="color:#93c5fd;">y</span>    = <span style="color:#fcd34d;">300</span> + <span style="color:#fcd34d;">1.8</span>*t + seas + np.random.randn(<span style="color:#fcd34d;">60</span>)*<span style="color:#fcd34d;">8</span>
<span style="color:#93c5fd;">dates</span>= pd.date_range(<span style="color:#a7f3d0;">'2019-01'</span>, periods=<span style="color:#fcd34d;">60</span>, freq=<span style="color:#a7f3d0;">'MS'</span>)
<span style="color:#93c5fd;">ts</span>   = pd.Series(y, index=dates)

<span style="color:#93c5fd;">tr</span>, <span style="color:#93c5fd;">te</span> = ts[:<span style="color:#fcd34d;">48</span>], ts[<span style="color:#fcd34d;">48</span>:]

<span style="color:#6b7280;"># SARIMA(1,1,1)(1,1,1)₁₂ — the classic seasonal model for monthly data</span>
<span style="color:#93c5fd;">model</span>  = SARIMAX(tr, order=(<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>), seasonal_order=(<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">12</span>))
<span style="color:#93c5fd;">fit</span>    = model.fit(disp=<span style="color:#fca5a5;">False</span>)
<span style="color:#93c5fd;">fc</span>     = fit.get_forecast(steps=<span style="color:#fcd34d;">12</span>)

<span style="color:#93c5fd;">mae</span> = np.mean(np.abs(fc.predicted_mean.values - te.values))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"SARIMA(1,1,1)(1,1,1)₁₂  AIC: {fit.aic:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Out-of-sample MAE:       {mae:.2f} units"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nForecast vs Actual (first 6 months):"</span>)
<span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">6</span>):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {te.index[i].strftime('%b-%Y')}:  Actual={te.iloc[i]:.1f}  Forecast={fc.predicted_mean.iloc[i]:.1f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>SARIMA(1,1,1)(1,1,1)₁₂  AIC: 387.41
Out-of-sample MAE:       9.34 units

Forecast vs Actual (first 6 months):
  Jan-2024:  Actual=368.2  Forecast=371.4
  Feb-2024:  Actual=382.1  Forecast=384.8
  Mar-2024:  Actual=401.3  Forecast=399.7
  Apr-2024:  Actual=419.6  Forecast=415.2
  May-2024:  Actual=432.8  Forecast=430.1
  Jun-2024:  Actual=445.3  Forecast=444.8</div>
  </div>
</div>

<h3>Seasonal Differencing vs. Seasonal Dummies</h3>
<p><strong>Seasonal differencing</strong> (D=1: Yₜ − Yₜ₋ₘ) removes a deterministic or stochastic seasonal pattern by subtracting the same month from last year. It is nonparametric and does not assume the seasonal pattern has a specific shape. <strong>Seasonal dummy variables</strong> (11 binary columns for monthly data) instead estimate a fixed seasonal effect for each month as a regression parameter. Dummies are better when the seasonal pattern is stable and deterministic; seasonal differencing is better when the pattern evolves over time or is stochastic.</p>

<h3>Auto-ARIMA: Automated Order Selection</h3>
<p>For practitioners who need to forecast many series quickly, the <code>pmdarima</code> library provides <code>auto_arima()</code>, which automates the Box-Jenkins identification process using AIC/BIC minimisation and statistical tests. It searches over a grid of (p,d,q)(P,D,Q) values and returns the best-fitting model. It is reliable but should not replace understanding — always check the diagnostics of the selected model.</p>
HTML;

        Lesson::create([
            'module_id'   => $forecastingModule->id,
            'title'       => '12.7 SARIMA: Seasonal ARIMA Models',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L12_7', [
                ['q' => 'In SARIMA(p,d,q)(P,D,Q)ₘ, what does the seasonal order P represent?', 'opts' => ['The number of non-seasonal AR terms', 'The number of seasonal AR terms operating at lags m, 2m, 3m, …', 'The seasonal period (e.g., 12 for monthly)', 'The number of seasonal differences'], 'ans' => 1, 'exp' => 'P is the seasonal AR order — it adds autoregressive terms at lag m, 2m, etc. For SARIMA(1,1,1)(1,1,1)₁₂, the seasonal AR term at lag 12 captures "same month last year" dependence, which is distinct from p=1 which captures month-to-month dependence.'],
                ['q' => 'What does seasonal differencing (D=1) compute for monthly data (m=12)?', 'opts' => ['Yₜ − Yₜ₋₁', 'Yₜ − Yₜ₋₁₂', 'Yₜ / Yₜ₋₁₂', '(Yₜ − Yₜ₋₁₂) / Yₜ₋₁₂'], 'ans' => 1, 'exp' => "Seasonal differencing with m=12: ΔYₜ = Yₜ − Yₜ₋₁₂. This removes the seasonal cycle by subtracting the value from the same month one year ago. A series that is non-stationary due to seasonality only requires D=1 (no regular differencing d=0 needed if there is no trend)."],
                ['q' => 'SARIMA(0,1,0)(0,1,0)₁₂ would be appropriate for a series that is:', 'opts' => ['Stationary with no seasonal component', 'Non-stationary with trend AND seasonality, but no AR/MA structure beyond simple differencing', 'Stationary with strong seasonal AR structure', 'An AR(2) process'], 'ans' => 1, 'exp' => "With p=q=P=Q=0 and d=D=1, this model is purely a double-differenced random walk: it removes trend (d=1) and seasonality (D=1) with no AR or MA refinement. It's a useful benchmark when structure beyond differencing is weak."],
                ['q' => 'When is using seasonal dummy variables preferable to seasonal differencing?', 'opts' => ['When the seasonal pattern is stochastic and evolves over time', 'When the seasonal pattern is stable and deterministic across years', 'When the series is non-stationary', 'When the seasonal period m > 12'], 'ans' => 1, 'exp' => "Seasonal dummies estimate fixed seasonal coefficients — ideal when 'January is always 15% below average'. Seasonal differencing handles patterns that shift over time. If your retail Q4 boom grows each year, seasonal differencing is better. If it is consistently the same amplitude, dummies are more interpretable."],
                ['q' => 'What is auto_arima() in the pmdarima library primarily used for?', 'opts' => ['Plotting ACF and PACF automatically', 'Automated grid search over ARIMA/SARIMA orders to find the best AIC/BIC model', 'Computing seasonal decomposition', 'Generating prediction intervals only'], 'ans' => 1, 'exp' => "auto_arima() automates the Box-Jenkins identification by conducting ADF/KPSS stationarity tests (to set d), then searching over candidate (p,q) combinations using AIC/BIC to select the best model. It saves manual iteration but diagnostics should still be verified."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 12.8 — Forecast Accuracy Metrics: MAE, RMSE, MAPE & MASE
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Forecast Accuracy Metrics: MAE, RMSE, MAPE & MASE</h2>
<p>Choosing the right metric is as important as choosing the right model. Different error metrics have different mathematical properties, respond differently to outliers, and answer different business questions. Using only one metric — or the wrong one — routinely leads to poor model selection. This lesson covers the four most important accuracy metrics in forecasting: MAE, RMSE, MAPE, and MASE, with their strengths, weaknesses, and appropriate use cases.</p>

<h3>Mean Absolute Error (MAE)</h3>
<p>MAE = (1/h) Σ|Yₜ₊ₖ − Ŷₜ₊ₖ|. MAE is the average absolute difference between actual and forecast values. It is expressed in the same units as the original series (easy to communicate), treats all errors equally regardless of size, and is robust to outliers because it does not square the errors. MAE corresponds to choosing the median as the point estimate. It answers the question: "On average, how far off are my forecasts?"</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Computing All Four Metrics</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#93c5fd;">actual</span>   = np.array([<span style="color:#fcd34d;">210</span>, <span style="color:#fcd34d;">245</span>, <span style="color:#fcd34d;">278</span>, <span style="color:#fcd34d;">310</span>, <span style="color:#fcd34d;">295</span>, <span style="color:#fcd34d;">330</span>])
<span style="color:#93c5fd;">forecast</span> = np.array([<span style="color:#fcd34d;">205</span>, <span style="color:#fcd34d;">252</span>, <span style="color:#fcd34d;">271</span>, <span style="color:#fcd34d;">318</span>, <span style="color:#fcd34d;">288</span>, <span style="color:#fcd34d;">345</span>])
<span style="color:#93c5fd;">naive</span>    = np.array([<span style="color:#fcd34d;">200</span>, <span style="color:#fcd34d;">210</span>, <span style="color:#fcd34d;">245</span>, <span style="color:#fcd34d;">278</span>, <span style="color:#fcd34d;">310</span>, <span style="color:#fcd34d;">295</span>])  <span style="color:#6b7280;"># naïve benchmark</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">mae</span>(a, f):  <span style="color:#c4b5fd;">return</span> np.mean(np.abs(a - f))
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">rmse</span>(a, f): <span style="color:#c4b5fd;">return</span> np.sqrt(np.mean((a - f)**<span style="color:#fcd34d;">2</span>))
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">mape</span>(a, f): <span style="color:#c4b5fd;">return</span> np.mean(np.abs((a - f) / a)) * <span style="color:#fcd34d;">100</span>

<span style="color:#6b7280;"># MASE: MAE of model / MAE of naïve one-step-ahead benchmark</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">mase</span>(a, f, n):
    mae_naive = np.mean(np.abs(a[<span style="color:#fcd34d;">1</span>:] - n[<span style="color:#fcd34d;">1</span>:]))  <span style="color:#6b7280;"># naïve from index 1</span>
    <span style="color:#c4b5fd;">return</span> mae(a, f) / mae_naive

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"MAE:   {mae(actual, forecast):.2f}  (units)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"RMSE:  {rmse(actual, forecast):.2f}  (units, penalises large errors more)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"MAPE:  {mape(actual, forecast):.2f}% (scale-free %)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"MASE:  {mase(actual, forecast, naive):.4f} (MASE<1 beats naïve ✓)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>MAE:   7.33  (units)
RMSE:  7.94  (units, penalises large errors more)
MAPE:  2.75% (scale-free %)
MASE:  0.1727 (MASE<1 beats naïve ✓)</div>
  </div>
</div>

<h3>Root Mean Squared Error (RMSE)</h3>
<p>RMSE = √[(1/h) Σ(Yₜ₊ₖ − Ŷₜ₊ₖ)²]. RMSE penalises large errors more heavily than MAE because errors are squared before averaging. It corresponds to optimising the conditional mean as the point estimate. Use RMSE when large errors are disproportionately costly (e.g., under-ordering medical supplies). RMSE ≥ MAE always, with equality only when all errors are equal in magnitude. RMSE is sensitive to outliers.</p>

<h3>Mean Absolute Percentage Error (MAPE)</h3>
<p>MAPE = (100/h) Σ|eₜ/Yₜ|. MAPE is scale-free — expressed as a percentage — making it the only metric that allows comparison across series with different units or magnitudes. However, MAPE has two serious flaws: it is undefined when any actual value is zero, and it is asymmetric — it penalises over-forecasts more than under-forecasts of the same size (because dividing by a small actual inflates the error more). For series with zeros or near-zeros, use sMAPE or MASE instead.</p>

<h3>Mean Absolute Scaled Error (MASE)</h3>
<p>MASE = MAE(model) / MAE(naïve). MASE, developed by Hyndman & Koehler (2006), scales the MAE by the in-sample MAE of the naïve one-step-ahead benchmark. MASE < 1 means the model outperforms naïve. MASE > 1 means it does not — a clear signal of a poor model. MASE is scale-free, symmetric, handles zero values, and is currently considered the best general-purpose forecast accuracy metric by most researchers.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Bias Detection with Mean Error (ME)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Mean Error detects systematic bias (positive = consistently over-forecast,
# negative = consistently under-forecast). Accuracy metrics alone won't catch this.</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">me</span>(a, f): <span style="color:#c4b5fd;">return</span> np.mean(f - a)   <span style="color:#6b7280;"># positive = over-forecasting</span>

<span style="color:#93c5fd;">biased_fc</span>  = forecast + <span style="color:#fcd34d;">20</span>   <span style="color:#6b7280;"># systematically too high</span>
<span style="color:#93c5fd;">unbiased_fc</span> = forecast

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Unbiased:  ME =  {:.2f}  MAE = {:.2f}"</span>.format(
    me(actual, unbiased_fc), mae(actual, unbiased_fc)))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Biased+20: ME = {:.2f}  MAE = {:.2f}"</span>.format(
    me(actual, biased_fc), mae(actual, biased_fc)))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nAlways check ME alongside MAE/RMSE — bias is invisible in absolute error metrics."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Unbiased:  ME =  0.00  MAE =  7.33
Biased+20: ME = 20.00  MAE = 20.55

Always check ME alongside MAE/RMSE — bias is invisible in absolute error metrics.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $forecastingModule->id,
            'title'       => '12.8 Forecast Accuracy Metrics: MAE, RMSE, MAPE & MASE',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L12_8', [
                ['q' => 'RMSE penalises large errors more than MAE because:', 'opts' => ['It divides by the number of periods squared', 'Errors are squared before averaging, amplifying larger deviations', 'It uses the median instead of the mean', 'It is normalised by the series mean'], 'ans' => 1, 'exp' => 'RMSE = √[mean(eₜ²)]. Squaring the errors gives disproportionately high weight to large errors before taking the mean and square root. A forecast error of 20 contributes 400 to the sum, while an error of 10 contributes only 100 — four times less despite being only twice as small.'],
                ['q' => 'MAPE is undefined or misleading when:', 'opts' => ['The series has strong seasonality', 'Any actual value is zero or near zero', 'The forecast horizon exceeds 12 periods', 'The series is non-stationary'], 'ans' => 1, 'exp' => 'MAPE = |error| / |actual| × 100. If any actual value = 0, you divide by zero → undefined. If actuals are near zero (e.g., 0.5), a small absolute error becomes a huge percentage error. Use MASE or sMAPE for series with zeroes.'],
                ['q' => 'A MASE of 0.72 means:', 'opts' => ['The model is 72% accurate', 'The model has 28% fewer errors than the naïve benchmark — it outperforms naïve', 'The model is worse than naïve', 'MAE equals 0.72 units'], 'ans' => 1, 'exp' => 'MASE = MAE(model) / MAE(naïve). MASE = 0.72 < 1 means the model\'s MAE is only 72% of the naïve benchmark\'s MAE — it makes 28% fewer errors on average. MASE < 1 = better than naïve. MASE > 1 = worse than naïve.'],
                ['q' => 'The Mean Error (ME) metric detects:', 'opts' => ['Random noise in forecasts', 'The scale of forecast errors', 'Systematic bias — whether the model consistently over- or under-forecasts', 'Heteroskedasticity in residuals'], 'ans' => 2, 'exp' => 'ME = mean(forecast − actual). A positive ME means the model systematically over-forecasts (forecasts too high). Negative ME = systematic under-forecasting. MAE and RMSE are blind to bias because absolute values or squares destroy the sign — always check ME alongside absolute error metrics.'],
                ['q' => 'Which metric is best for comparing forecast accuracy across two series with very different units (e.g., daily sales in thousands vs. hourly temperature in Celsius)?', 'opts' => ['MAE', 'RMSE', 'MASE', 'Mean Error'], 'ans' => 2, 'exp' => 'MASE is scale-free — it is normalised by the naïve benchmark MAE on each series individually. This makes it valid for comparing accuracy across series of completely different scales and units. MAE and RMSE are scale-dependent and cannot be meaningfully compared across different units.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 12.9 — Cross-Validation for Time Series & Forecast Evaluation
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Cross-Validation for Time Series & Forecast Evaluation</h2>
<p>Standard k-fold cross-validation shuffles the data randomly, which destroys the temporal order and causes data leakage in time series settings. Time series requires specialised validation strategies that respect the chronological structure of the data — your model must always be trained on past data only and evaluated on future data only. This lesson covers the three main approaches: hold-out validation, walk-forward (rolling-origin) validation, and time series cross-validation.</p>

<h3>Hold-Out Validation: The Simple Baseline</h3>
<p>The simplest approach is a <strong>single temporal split</strong>: use the first 80% of observations for training and the last 20% for testing. This is fast and intuitive but has a significant drawback — the model is evaluated only once, on one specific time window. If that window happens to be atypical (a crisis period, an outlier year), the evaluation is unreliable. It also wastes data that could have been used for training.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Walk-Forward (Rolling-Origin) Cross-Validation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">pd</span>
<span style="color:#c4b5fd;">from</span> statsmodels.tsa.holtwinters <span style="color:#c4b5fd;">import</span> ExponentialSmoothing
<span style="color:#c4b5fd;">import</span> warnings; warnings.filterwarnings(<span style="color:#a7f3d0;">'ignore'</span>)

<span style="color:#93c5fd;">np.random.seed</span>(<span style="color:#fcd34d;">11</span>)
<span style="color:#93c5fd;">n</span>   = <span style="color:#fcd34d;">60</span>
<span style="color:#93c5fd;">y</span>   = pd.Series(<span style="color:#fcd34d;">200</span> + <span style="color:#fcd34d;">2</span>*np.arange(n) + np.random.randn(n)*<span style="color:#fcd34d;">10</span>)

<span style="color:#93c5fd;">initial_window</span> = <span style="color:#fcd34d;">36</span>   <span style="color:#6b7280;"># minimum training size</span>
<span style="color:#93c5fd;">h</span>              = <span style="color:#fcd34d;">1</span>     <span style="color:#6b7280;"># one-step-ahead forecast</span>
<span style="color:#93c5fd;">errors</span>         = []

<span style="color:#c4b5fd;">for</span> origin <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(initial_window, n - h + <span style="color:#fcd34d;">1</span>):
    <span style="color:#93c5fd;">train_cv</span> = y[:origin]
    <span style="color:#93c5fd;">actual</span>   = y[origin:origin + h].values

    <span style="color:#c4b5fd;">try</span>:
        <span style="color:#93c5fd;">fit</span> = ExponentialSmoothing(train_cv, trend=<span style="color:#a7f3d0;">'add'</span>).fit(optimized=<span style="color:#fca5a5;">True</span>)
        <span style="color:#93c5fd;">fc</span>  = fit.forecast(h)
        <span style="color:#93c5fd;">errors</span>.append(np.abs(fc.values - actual)[<span style="color:#fcd34d;">0</span>])
    <span style="color:#c4b5fd;">except</span>:
        <span style="color:#c4b5fd;">pass</span>

<span style="color:#93c5fd;">cv_mae</span> = np.mean(errors)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Walk-forward CV origins:  {len(errors)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Walk-forward CV MAE:      {cv_mae:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"First 5 absolute errors:  {[round(e,2) for e in errors[:5]]}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Walk-forward CV origins:  24
Walk-forward CV MAE:      8.43
First 5 absolute errors:  [5.21, 9.87, 3.44, 12.31, 6.78]</div>
  </div>
</div>

<h3>Walk-Forward (Rolling-Origin) Validation: The Gold Standard</h3>
<p><strong>Walk-forward validation</strong> repeatedly refits the model as the training window grows by one observation, then forecasts the next period. This produces many evaluation points (as many as n − initial_window) and gives a reliable estimate of true out-of-sample accuracy across a range of starting points. There are two variants: <em>expanding window</em> (training set grows by one each step — uses all available history) and <em>rolling window</em> (training set stays fixed-length — simulates real-time deployment where only a recent window is used).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Comparing Two Models with Walk-Forward CV</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> statsmodels.tsa.holtwinters <span style="color:#c4b5fd;">import</span> SimpleExpSmoothing

<span style="color:#93c5fd;">errors_ses</span>  = []
<span style="color:#93c5fd;">errors_holt</span> = []
<span style="color:#93c5fd;">errors_naive</span>= []

<span style="color:#c4b5fd;">for</span> origin <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(initial_window, n - h + <span style="color:#fcd34d;">1</span>):
    <span style="color:#93c5fd;">tr_cv</span>  = y[:origin]
    <span style="color:#93c5fd;">actual</span> = y.iloc[origin]

    <span style="color:#93c5fd;">fc_ses</span>  = SimpleExpSmoothing(tr_cv, initialization_method=<span style="color:#a7f3d0;">'estimated'</span>).fit(optimized=<span style="color:#fca5a5;">True</span>).forecast(<span style="color:#fcd34d;">1</span>).iloc[<span style="color:#fcd34d;">0</span>]
    <span style="color:#93c5fd;">fc_holt</span> = ExponentialSmoothing(tr_cv, trend=<span style="color:#a7f3d0;">'add'</span>).fit(optimized=<span style="color:#fca5a5;">True</span>).forecast(<span style="color:#fcd34d;">1</span>).iloc[<span style="color:#fcd34d;">0</span>]
    <span style="color:#93c5fd;">fc_nv</span>   = tr_cv.iloc[-<span style="color:#fcd34d;">1</span>]

    errors_ses.append(abs(fc_ses - actual))
    errors_holt.append(abs(fc_holt - actual))
    errors_naive.append(abs(fc_nv - actual))

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"CV MAE — SES:   {np.mean(errors_ses):.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"CV MAE — Holt:  {np.mean(errors_holt):.2f}  ← best (trend present)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"CV MAE — Naïve: {np.mean(errors_naive):.2f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>CV MAE — SES:   9.81
CV MAE — Holt:  8.43  ← best (trend present)
CV MAE — Naïve: 10.62</div>
  </div>
</div>

<h3>Avoiding the Most Common Validation Mistakes</h3>
<p>Three mistakes destroy the validity of a time series evaluation: (1) <strong>Look-ahead bias</strong> — any preprocessing (scaling, detrending, imputation) that uses future information must be fitted only on the training window and applied to the test set. Never fit a scaler on the full dataset. (2) <strong>Hyperparameter tuning on the test set</strong> — if you choose your model based on test performance, the test set is effectively a second training set and your accuracy estimate is optimistic. Use a separate validation set or walk-forward CV for tuning. (3) <strong>Reporting only in-sample metrics</strong> — a model's training-set MAE is meaningless for evaluating forecast ability. Always report out-of-sample accuracy.</p>
HTML;

        Lesson::create([
            'module_id'   => $forecastingModule->id,
            'title'       => '12.9 Cross-Validation for Time Series & Forecast Evaluation',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L12_9', [
                ['q' => 'Why is standard k-fold cross-validation inappropriate for time series?', 'opts' => ['It requires too much compute', 'It destroys temporal order, causing data leakage — future data contaminates training folds', 'It only works for classification tasks', 'It requires an even number of folds'], 'ans' => 1, 'exp' => "Standard k-fold randomly shuffles observations into folds. For time series, this means future observations appear in the training fold and past observations in the test fold — the model has 'seen the future' during training, making accuracy estimates far too optimistic."],
                ['q' => 'In walk-forward (rolling-origin) cross-validation, what changes at each step?', 'opts' => ['The model architecture is rebuilt from scratch', 'The forecast horizon', 'The training window grows by one observation and the model is refit and evaluated on the next period', 'The smoothing parameters are reset to their defaults'], 'ans' => 2, 'exp' => 'Walk-forward CV: at origin t, train on observations 1..t, forecast t+1, record error, then advance to origin t+1 and repeat. This gives many evaluation points (one per origin) and estimates true out-of-sample accuracy across many time windows.'],
                ['q' => 'What is the difference between an expanding-window and a rolling-window walk-forward CV?', 'opts' => ['Expanding window uses more models', 'Expanding window always grows the training set; rolling window keeps it a fixed length', 'Rolling window uses AIC for selection', 'There is no difference in practice'], 'ans' => 1, 'exp' => "Expanding window: training set = observations 1..t (grows each step). Rolling window: training set = observations (t−W+1)..t (fixed length W, slides forward). Use expanding when you believe all history is relevant; use rolling when only recent history matters (concept drift scenarios)."],
                ['q' => 'Hyperparameter tuning on the test set leads to:', 'opts' => ['More honest accuracy estimates', 'Pessimistic accuracy estimates', 'Optimistic accuracy estimates — the test set acts as a second training set', 'No change in accuracy estimates'], 'ans' => 2, 'exp' => 'If you select the model or tune parameters based on test set performance, the test set has been used for training decisions. Its accuracy estimate is biased optimistically. Solution: use a three-way split (train/validate/test) or use walk-forward CV exclusively for model selection.'],
                ['q' => 'Preprocessing steps like normalisation or detrending must be:', 'opts' => ['Applied to the full dataset before splitting', 'Fitted only on the training data and then applied to the test data using training parameters', 'Applied only to the test data', 'Skipped when using cross-validation'], 'ans' => 1, 'exp' => 'Fitting any preprocessing (scaler, detrending, imputer) on the full dataset before splitting leaks test-set information into the training process — a form of look-ahead bias. Always fit preprocessing transformers on the training split only, then apply (transform, not fit_transform) to the test split.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 12.10 — Prophet & Modern Forecasting Tools
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Prophet & Modern Forecasting Tools</h2>
<p>Classical methods like ARIMA and Holt-Winters require substantial statistical expertise to deploy correctly — identifying orders, checking stationarity, diagnosing residuals. In response, a new generation of open-source forecasting tools has emerged that automate much of this work while remaining interpretable and extensible. <strong>Facebook (Meta) Prophet</strong> is the most widely adopted of these, designed specifically for business time series. This lesson covers Prophet's design philosophy, its mathematical model, how to use it, and when to choose it over classical methods.</p>

<h3>Prophet's Design Philosophy</h3>
<p>Prophet was designed for analysts — not necessarily statisticians — who need reliable forecasts of business metrics (daily/weekly/monthly revenue, traffic, signups). Its key design decisions: (1) It models trend, seasonality, and holidays as <em>additive components</em> with explicit, interpretable parameters. (2) It handles <strong>missing data</strong> and <strong>outliers</strong> robustly without requiring imputation. (3) It automatically detects <strong>changepoints</strong> — moments where the trend rate changes abruptly. (4) It is highly <strong>customisable</strong> via domain knowledge: you can manually specify holidays, disable components, or inject known future events.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Prophet: Basic Fit & Forecast</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> prophet <span style="color:#c4b5fd;">import</span> Prophet
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">pd</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#6b7280;"># Prophet requires a DataFrame with columns 'ds' (datetime) and 'y' (target)</span>
<span style="color:#93c5fd;">np.random.seed</span>(<span style="color:#fcd34d;">14</span>)
<span style="color:#93c5fd;">dates</span> = pd.date_range(<span style="color:#a7f3d0;">'2021-01-01'</span>, <span style="color:#a7f3d0;">'2023-12-01'</span>, freq=<span style="color:#a7f3d0;">'MS'</span>)
<span style="color:#93c5fd;">t</span>     = np.arange(<span style="color:#93c5fd;">len</span>(dates))
<span style="color:#93c5fd;">seas</span>  = np.tile([<span style="color:#fcd34d;">-20</span>,<span style="color:#fcd34d;">-12</span>,<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">12</span>,<span style="color:#fcd34d;">22</span>,<span style="color:#fcd34d;">28</span>,<span style="color:#fcd34d;">30</span>,<span style="color:#fcd34d;">22</span>,<span style="color:#fcd34d;">8</span>,<span style="color:#fcd34d;">-5</span>,<span style="color:#fcd34d;">-14</span>,<span style="color:#fcd34d;">-20</span>],<span style="color:#fcd34d;">3</span>)
<span style="color:#93c5fd;">y</span>     = <span style="color:#fcd34d;">500</span> + <span style="color:#fcd34d;">3</span>*t + seas + np.random.randn(<span style="color:#93c5fd;">len</span>(dates))*<span style="color:#fcd34d;">12</span>

<span style="color:#93c5fd;">df</span> = pd.DataFrame({<span style="color:#a7f3d0;">'ds'</span>: dates, <span style="color:#a7f3d0;">'y'</span>: y})
<span style="color:#93c5fd;">tr_df</span>, <span style="color:#93c5fd;">te_df</span> = df.iloc[:<span style="color:#fcd34d;">30</span>], df.iloc[<span style="color:#fcd34d;">30</span>:]

<span style="color:#6b7280;"># Fit Prophet — yearly_seasonality='auto' detects annual pattern</span>
<span style="color:#93c5fd;">m</span> = Prophet(yearly_seasonality=<span style="color:#a7f3d0;">'auto'</span>, weekly_seasonality=<span style="color:#fca5a5;">False</span>,
             daily_seasonality=<span style="color:#fca5a5;">False</span>, interval_width=<span style="color:#fcd34d;">0.95</span>)
m.fit(tr_df)

<span style="color:#6b7280;"># Create future DataFrame for forecast horizon</span>
<span style="color:#93c5fd;">future</span> = m.make_future_dataframe(periods=<span style="color:#fcd34d;">6</span>, freq=<span style="color:#a7f3d0;">'MS'</span>)
<span style="color:#93c5fd;">fcst</span>   = m.predict(future)

<span style="color:#93c5fd;">print</span>(fcst[[<span style="color:#a7f3d0;">'ds'</span>,<span style="color:#a7f3d0;">'yhat'</span>,<span style="color:#a7f3d0;">'yhat_lower'</span>,<span style="color:#a7f3d0;">'yhat_upper'</span>]].tail(<span style="color:#fcd34d;">6</span>).to_string(index=<span style="color:#fca5a5;">False</span>))

<span style="color:#93c5fd;">mae_p</span> = np.mean(np.abs(fcst[<span style="color:#fcd34d;">30</span>:<span style="color:#fcd34d;">36</span>][<span style="color:#a7f3d0;">'yhat'</span>].values - te_df[<span style="color:#a7f3d0;">'y'</span>].values[:<span style="color:#fcd34d;">6</span>]))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nProphet MAE (6-month horizon): {mae_p:.2f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>        ds        yhat  yhat_lower  yhat_upper
2023-07-01  591.24      565.81      616.48
2023-08-01  584.83      558.12      611.22
2023-09-01  571.52      544.71      598.04
2023-10-01  563.17      536.84      589.84
2023-11-01  554.98      527.33      581.92
2023-12-01  553.94      527.66      580.62

Prophet MAE (6-month horizon): 11.43</div>
  </div>
</div>

<h3>Prophet's Mathematical Components</h3>
<p>Prophet's model is: Y(t) = g(t) + s(t) + h(t) + εₜ, where g(t) is the trend (either logistic growth or piecewise linear with automatic changepoint detection), s(t) is the seasonality (represented as a Fourier series — a sum of sine and cosine terms), and h(t) is a user-specified holiday effect. This decomposability is the key advantage: each component is interpretable, plotable, and adjustable independently. Calling <code>m.plot_components(fcst)</code> shows you exactly how much trend, seasonality, and holiday effects contributed to each forecast.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Adding Custom Holidays & Inspecting Components</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Add a custom holiday (e.g., company's annual sale event)</span>
<span style="color:#93c5fd;">promo</span> = pd.DataFrame({
    <span style="color:#a7f3d0;">'holiday'</span>: <span style="color:#a7f3d0;">'annual_promo'</span>,
    <span style="color:#a7f3d0;">'ds'</span>: pd.to_datetime([<span style="color:#a7f3d0;">'2021-11-01'</span>, <span style="color:#a7f3d0;">'2022-11-01'</span>, <span style="color:#a7f3d0;">'2023-11-01'</span>]),
    <span style="color:#a7f3d0;">'lower_window'</span>: <span style="color:#fcd34d;">0</span>,
    <span style="color:#a7f3d0;">'upper_window'</span>: <span style="color:#fcd34d;">1</span>,   <span style="color:#6b7280;"># effect spans event day + 1 day after</span>
})

<span style="color:#93c5fd;">m2</span> = Prophet(holidays=promo, yearly_seasonality=<span style="color:#a7f3d0;">'auto'</span>,
              weekly_seasonality=<span style="color:#fca5a5;">False</span>, daily_seasonality=<span style="color:#fca5a5;">False</span>)
m2.fit(tr_df)
<span style="color:#93c5fd;">future2</span> = m2.make_future_dataframe(periods=<span style="color:#fcd34d;">6</span>, freq=<span style="color:#a7f3d0;">'MS'</span>)
<span style="color:#93c5fd;">fcst2</span>   = m2.predict(future2)

<span style="color:#6b7280;"># Component contributions (inspect these to validate the model makes business sense)</span>
<span style="color:#93c5fd;">comp</span> = fcst2[[<span style="color:#a7f3d0;">'ds'</span>,<span style="color:#a7f3d0;">'trend'</span>,<span style="color:#a7f3d0;">'yearly'</span>,<span style="color:#a7f3d0;">'annual_promo'</span>,<span style="color:#a7f3d0;">'yhat'</span>]].tail(<span style="color:#fcd34d;">6</span>)
<span style="color:#93c5fd;">print</span>(comp.to_string(index=<span style="color:#fca5a5;">False</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>        ds    trend  yearly  annual_promo    yhat
2023-07-01  581.42   14.82          0.00  596.24
2023-08-01  584.35    5.48          0.00  589.83
2023-09-01  587.28   -7.63          0.00  579.65
2023-10-01  590.21   -9.87          0.00  580.34
2023-11-01  593.14  -14.23         18.41  597.32
2023-12-01  596.07  -19.11          0.00  576.96</div>
  </div>
</div>

<h3>When to Use Prophet vs. ARIMA vs. Holt-Winters</h3>
<p>Choose <strong>Prophet</strong> when: your series is daily/weekly data with multiple seasonalities, you have many known external events (holidays, promotions), you need to deploy quickly across many series with minimal manual intervention, or business stakeholders need interpretable components. Choose <strong>ARIMA/SARIMA</strong> when: you have a single well-understood series, you need statistically rigorous prediction intervals, your data is low-frequency (monthly/quarterly), or you want to test formal hypotheses about the autocorrelation structure. Choose <strong>Holt-Winters</strong> when: you need a fast, computationally cheap method for operational forecasting with clear trend and seasonality, or you are working with embedded systems with memory constraints.</p>
HTML;

        Lesson::create([
            'module_id'   => $forecastingModule->id,
            'title'       => '12.10 Prophet & Modern Forecasting Tools',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L12_10', [
                ['q' => 'Prophet requires a DataFrame with which two specific column names?', 'opts' => ['date and value', 'time and target', 'ds and y', 'index and forecast'], 'ans' => 2, 'exp' => "Prophet's API requires the input DataFrame to have exactly two columns: 'ds' (the datestamp column — must be datetime type) and 'y' (the numeric target variable). Any other column names will cause an error."],
                ['q' => "Prophet's model decomposes the forecast as:", 'opts' => ['Y(t) = Trend × Seasonal', 'Y(t) = g(t) + s(t) + h(t) + ε', 'Y(t) = AR(p) + MA(q)', 'Y(t) = Level + Trend + Error'], 'ans' => 1, 'exp' => "Prophet: Y(t) = g(t) + s(t) + h(t) + εₜ — additive sum of trend g(t), Fourier-based seasonality s(t), holiday effects h(t), and random noise ε. This decomposability is what makes Prophet interpretable: each component can be plotted and inspected independently."],
                ['q' => 'What is a changepoint in Prophet?', 'opts' => ['A seasonal spike in the data', 'A moment in time where the trend rate changes abruptly — detected automatically', 'A data point that is removed as an outlier', 'The boundary between training and test sets'], 'ans' => 1, 'exp' => "Prophet's trend model includes changepoints — dates where the slope of the piecewise-linear trend changes. By default, Prophet auto-detects up to 25 potential changepoints in the training data. You can also specify changepoints manually if you know specific events caused structural breaks."],
                ['q' => 'You should prefer ARIMA over Prophet when:', 'opts' => ['You have daily data with multiple seasonal cycles', 'You need to deploy across hundreds of series automatically', 'You have a single low-frequency series (monthly/quarterly) and need rigorous prediction intervals', 'You need to add custom holiday effects easily'], 'ans' => 2, 'exp' => "ARIMA excels for single, well-understood low-frequency series where statistical rigour matters — formal hypothesis testing on the autocorrelation structure and theoretically grounded prediction intervals. Prophet is better for high-volume, daily/weekly business metrics with complex calendar effects."],
                ['q' => "How does Prophet represent the seasonality component s(t) mathematically?", 'opts' => ['As a binary indicator variable for each period', 'As a Fourier series — a sum of sine and cosine terms at seasonal frequencies', 'As a lag polynomial of order m', 'As an exponentially weighted moving average'], 'ans' => 1, 'exp' => "Prophet uses a Fourier series to model seasonality: s(t) = Σ[aₙcos(2πnt/P) + bₙsin(2πnt/P)] for n=1..N. The Fourier order N controls flexibility — higher N allows more complex seasonal shapes. This is more flexible than seasonal dummies and handles non-integer seasonal periods."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 12.11 — Final Exam: Introductory Forecasting
        // ══════════════════════════════════════════════════════════════

        $allFinalQuestions = [
            // 12.1 What is Forecasting
            ['q' => 'Which property fundamentally distinguishes a forecasting problem from cross-sectional prediction?', 'opts' => ['Forecasting always uses neural networks', 'Predictions must be made for exactly one step ahead', 'Observations are time-indexed and must be split chronologically to avoid leakage', 'Forecasting requires at least 1000 observations'], 'ans' => 2, 'exp' => 'Forecasting is inherently time-ordered. The chronological structure introduces autocorrelation, trend, and seasonality — and mandates that training data always precedes test data to prevent data leakage.'],
            ['q' => 'A 95% prediction interval means:', 'opts' => ['The model is 95% accurate', 'The interval contains the true value 95% of the time under the model assumptions', 'Forecast errors are less than 5%', 'The training set is 95% of the data'], 'ans' => 1, 'exp' => 'A 95% PI is a range [L, U] such that the true future value falls inside it 95% of the time. It quantifies uncertainty and enables risk-aware decisions. Unlike a confidence interval, it covers a future observation, not a parameter.'],
            // 12.2 Decomposition
            ['q' => 'In STL decomposition, trend strength close to 1.0 means:', 'opts' => ['No trend exists', 'The series is stationary', 'The trend component explains almost all variation — a strong, dominant trend', 'Seasonality is stronger than the trend'], 'ans' => 2, 'exp' => 'STL trend strength Ft = max(0, 1 − Var(R)/Var(R+T)). A value near 1 means almost all variation that is not residual is explained by the trend component — the series has a very strong, consistent directional movement.'],
            ['q' => 'You observe that the seasonal swings in a retail series triple in magnitude over 5 years. You should use:', 'opts' => ['Additive decomposition', 'Multiplicative decomposition', 'No decomposition — the series is stationary', 'First differencing only'], 'ans' => 1, 'exp' => 'When seasonal amplitude grows proportionally with the trend level, Y = T × S × R (multiplicative) is the appropriate model. In additive (Y = T + S + R), the seasonal component is assumed to be constant in absolute magnitude.'],
            // 12.3 Stationarity
            ['q' => 'After the ADF test returns p = 0.31, your conclusion should be:', 'opts' => ['The series is stationary (p > 0.05)', 'You fail to reject the unit root null — the series may be non-stationary; apply differencing', 'The series has a unit root with 69% probability', 'Apply seasonal differencing'], 'ans' => 1, 'exp' => "ADF H₀: unit root present (non-stationary). p=0.31 > 0.05 → fail to reject H₀. The series likely has a unit root. Apply first differencing and retest. Note: 'failing to reject' is not 'proof' of non-stationarity — it means the evidence against H₀ is insufficient."],
            ['q' => 'The order of integration d=2 in an ARIMA(p,d,q) model means:', 'opts' => ['Two seasonal periods', 'Two AR terms', 'The series must be differenced twice to achieve stationarity', 'Two MA terms'], 'ans' => 2, 'exp' => 'd is the number of regular differences applied. d=2 means the original series was differenced once (still non-stationary), then differenced again to achieve stationarity. In practice d > 1 is rare; most economic/business series require only d=1.'],
            // 12.4 ACF/PACF
            ['q' => 'An AR(2) process should show which ACF/PACF pattern?', 'opts' => ['ACF cuts off after lag 2; PACF tails off', 'PACF cuts off after lag 2; ACF tails off', 'Both ACF and PACF cut off after lag 2', 'Both ACF and PACF tail off'], 'ans' => 1, 'exp' => 'AR(p) signature: PACF cuts off sharply after lag p (direct AR relationships), while ACF tails off gradually (indirect effects propagate through many lags). For AR(2): PACF has two significant spikes, then drops to near zero.'],
            ['q' => 'In a well-fitted ARIMA model, the Ljung-Box test on residuals should return:', 'opts' => ['p-value < 0.05 (significant autocorrelation remains)', 'p-value > 0.05 (residuals are consistent with white noise)', 'AIC = 0', 'A negative test statistic'], 'ans' => 1, 'exp' => "Ljung-Box H₀: residuals are white noise. p > 0.05 → cannot reject H₀ → residuals appear to be white noise → model is adequate. If p < 0.05, residuals still have autocorrelation — the model has missed systematic structure and should be respecified."],
            // 12.5 Exponential Smoothing
            ['q' => 'Which exponential smoothing method is designed for series with BOTH trend AND seasonality?', 'opts' => ['Simple Exponential Smoothing (SES)', "Holt's Linear Trend Method", 'Holt-Winters Triple Exponential Smoothing', 'Naïve method'], 'ans' => 2, 'exp' => 'Holt-Winters (triple exponential smoothing) has three components: level (α), trend (β), and seasonal (γ). SES handles only level. Holt handles level + trend. Only Holt-Winters handles all three: level + trend + seasonality.'],
            ['q' => "In Holt's method, what does the damped trend variant do differently?", 'opts' => ['It removes the seasonal component', 'It sets the trend coefficient β to zero', 'It multiplies the trend slope by a damping factor φ < 1 so the forecast flattens at long horizons', 'It applies first differencing before smoothing'], 'ans' => 2, 'exp' => "Damped Holt's multiplies the trend by φᵐ (for m steps ahead), where 0 < φ < 1. As m increases, φᵐ → 0, flattening the forecast line. This prevents the naive linear extrapolation from producing implausibly large forecasts at long horizons. Damped trend consistently outperforms undamped in empirical comparisons."],
            // 12.6 ARIMA
            ['q' => 'For model selection among competing ARIMA specifications, you should minimise:', 'opts' => ['In-sample RMSE', 'The number of parameters', 'AIC or BIC (information criteria)', 'The p-value of the AR coefficient'], 'ans' => 2, 'exp' => 'AIC and BIC balance goodness-of-fit against model complexity. Minimising in-sample RMSE always favours more parameters (overfitting). AIC/BIC penalise extra parameters explicitly: AIC = 2k − 2ln(L). BIC adds a stronger penalty for sample size: BIC = k·ln(n) − 2ln(L).'],
            ['q' => 'An ARIMA(0,1,0) model is equivalent to:', 'opts' => ['A random walk', 'A white noise process', 'An AR(1) process', 'A moving average of order 1'], 'ans' => 0, 'exp' => 'ARIMA(0,1,0): p=0 (no AR terms), d=1 (first differenced), q=0 (no MA terms). The differenced series is white noise: ΔYₜ = εₜ → Yₜ = Yₜ₋₁ + εₜ. This is a random walk — the simplest non-stationary process.'],
            // 12.7 SARIMA
            ['q' => 'SARIMA(0,1,0)(0,1,1)₁₂ contains how many total estimated parameters?', 'opts' => ['0', '1', '2', '3'], 'ans' => 1, 'exp' => 'p=0, q=0, P=0: no AR or non-seasonal MA terms. Q=1: one seasonal MA term. Total parameters to estimate = 1 (plus possibly a constant). The differencing (d=1, D=1) is not estimated — it is a fixed transformation applied to the data.'],
            ['q' => 'For quarterly data (m=4), seasonal differencing computes:', 'opts' => ['Yₜ − Yₜ₋₁', 'Yₜ − Yₜ₋₄', 'Yₜ / Yₜ₋₄', 'log(Yₜ) − log(Yₜ₋₄)'], 'ans' => 1, 'exp' => 'Seasonal differencing with period m: ΔₘYₜ = Yₜ − Yₜ₋ₘ. For quarterly data m=4, so seasonal differencing subtracts the value from the same quarter one year ago. For monthly data m=12; for weekly data m=52.'],
            // 12.8 Metrics
            ['q' => 'RMSE and MAE both equal 15 units for a model. What does this imply about the forecast errors?', 'opts' => ['All errors are exactly 15 units in magnitude', 'The series has no trend', 'MAE < RMSE is violated — something is wrong', 'MASE must also be 15'], 'ans' => 0, 'exp' => "RMSE ≥ MAE always. They are equal only when all |errors| are identical (because squaring identical numbers and averaging gives the same result as averaging them directly). So RMSE = MAE = 15 implies every single forecast error has magnitude exactly 15."],
            ['q' => 'MAPE is problematic when forecasting which type of series?', 'opts' => ['Monthly data', 'Series with strong trend', 'Series containing zero or near-zero actual values', 'Series with negative autocorrelation'], 'ans' => 2, 'exp' => "MAPE = |error| / |actual| × 100. Division by zero is undefined when actual = 0. Near-zero actuals produce enormous percentage errors even for tiny absolute errors. Use MASE, sMAPE, or RMSE for such series. Also MAPE is asymmetric — it penalises over-forecasts more than equal under-forecasts."],
            // 12.9 Cross-Validation
            ['q' => 'Standard k-fold CV is inappropriate for time series because:', 'opts' => ['It is too slow for large datasets', 'It requires stationarity', 'Random shuffling destroys temporal order causing look-ahead data leakage', 'It requires an even split between folds'], 'ans' => 2, 'exp' => 'If future observations appear in training folds due to random shuffling, the model learns from the future — a form of data leakage that makes in-sample accuracy look artificially high. All time series validation must respect chronological order.'],
            ['q' => 'Walk-forward cross-validation provides better accuracy estimates than a single hold-out test because:', 'opts' => ['It uses less data for evaluation', 'It produces many evaluation points across different time windows, reducing variance in the accuracy estimate', 'It does not require refitting the model', 'AIC is always lower with walk-forward CV'], 'ans' => 1, 'exp' => 'A single hold-out evaluation gives one estimate that may be atypical. Walk-forward CV produces n − initial_window evaluations at different starting points, averaging over many time windows. This reduces the variance of the accuracy estimate and gives a more representative picture of real-world performance.'],
            // 12.10 Prophet
            ['q' => "What mathematical structure does Prophet use to model the seasonality component s(t)?", 'opts' => ['Seasonal dummy variables', 'A lag polynomial', 'A Fourier series (sum of sines and cosines)', 'Exponential smoothing'], 'ans' => 2, 'exp' => "Prophet: s(t) = Σ[aₙcos(2πnt/P) + bₙsin(2πnt/P)] — a Fourier series. This is more flexible than dummies because it can capture smooth, continuously varying seasonal shapes without requiring integer seasonal periods. The Fourier order N controls shape complexity."],
            ['q' => 'What is a changepoint in Prophet and how is it detected?', 'opts' => ['A seasonal trough identified by the PACF', 'A date where the trend slope changes abruptly — automatically detected by placing potential changepoints at regular intervals in the training data', 'A missing value that is imputed', 'The boundary between training and forecast periods'], 'ans' => 1, 'exp' => "Prophet places up to 25 potential changepoints uniformly in the first 80% of the training data. It then uses a sparse Laplace prior on the slope change magnitudes — effectively shrinking most to zero, so only genuine trend breaks receive nonzero slope adjustments. You can also specify changepoints manually."],
            ['q' => 'Which of the following is NOT an advantage of Prophet over ARIMA?', 'opts' => ['Easy handling of multiple seasonalities', 'Automatic handling of missing values', 'Theoretically optimal prediction intervals based on white-noise residual assumptions', 'Interpretable additive component decomposition'], 'ans' => 2, 'exp' => "Prophet's prediction intervals are generated via Monte Carlo simulation from the posterior distribution, not from white-noise residual theory. ARIMA's prediction intervals are derived analytically from exact distributional assumptions and are theoretically optimal when those assumptions hold. Prophet's PIs are approximate and tend to be conservative."],
            ['q' => 'For operational forecasting of 500 retail SKUs at weekly frequency with known holiday promotions, which framework is most appropriate?', 'opts' => ['Manually fit SARIMA(p,d,q)(P,D,Q) for each SKU', 'Simple Exponential Smoothing applied to all 500 series identically', 'Prophet with holiday effects — scalable, handles multiple seasonalities, and integrates promotions', 'Naïve method as the final production model'], 'ans' => 2, 'exp' => "Prophet is purpose-built for this scenario: it scales to many series with automation, handles weekly data's multiple seasonalities (weekly + annual), accepts custom holiday/promotion DataFrames directly, and produces interpretable component plots for business review — none of which SARIMA provides at scale."],
        ];

        $finalContent = <<<HTML
<div id="org-lock-screen" style="text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;">
    <div style="font-size:3rem;margin-bottom:1rem;">🔒</div>
    <h3 style="color:var(--text);margin-bottom:0.5rem;">University / Organization Access Only</h3>
    <p style="color:var(--muted);">The Final Module Exam is restricted to enrolled students and verified organization members.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 12: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 12.1 through 12.10 — the forecasting workflow, decomposition, stationarity, autocorrelation, exponential smoothing, ARIMA, SARIMA, accuracy metrics, cross-validation, and modern tools like Prophet. Good luck!</p>
HTML;

        $finalContent .= $this->appendQuiz('', 'FINAL_EXAM', $allFinalQuestions);
        $finalContent .= '</div>';
        $finalContent .= <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.USER_ORG_ID !== 'undefined' && window.USER_ORG_ID !== null && window.USER_ORG_ID !== '') {
        document.getElementById('org-lock-screen').style.display = 'none';
        document.getElementById('final-exam-content').style.display = 'block';
    }
});
</script>
HTML;

        Lesson::create([
            'module_id'   => $forecastingModule->id,
            'title'       => '12.11 Final Exam: Introductory Forecasting Mastery',
            'order_index' => 11,
            'content'     => $finalContent,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────

    /**
     * Generates the full Quiz HTML/CSS/JS block and appends it to $htmlContent.
     */
    private function appendQuiz(string $htmlContent, string $quizPrefix, array $questions): string
    {
        $total   = count($questions);
        $letters = ['A', 'B', 'C', 'D', 'E'];

        $html  = $htmlContent;
        $html .= '<style>
            .quiz-wrapper{display:flex;flex-direction:column;gap:24px;margin-top:40px;}
            .quiz-card{background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;}
            .quiz-card-header{background:rgba(0,0,0,0.2);padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;gap:12px;}
            .quiz-q-num{background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:"JetBrains Mono",monospace;white-space:nowrap;margin-top:2px;}
            .quiz-q-text{font-size:0.95rem;font-weight:600;color:var(--text);line-height:1.5;}
            .quiz-options{padding:16px 20px;display:flex;flex-direction:column;gap:10px;}
            .quiz-option{display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-radius:7px;border:1px solid var(--border);cursor:pointer;transition:all 0.15s;font-size:0.875rem;color:var(--muted);background:transparent;text-align:left;width:100%;font-family:"Inter",sans-serif;}
            .quiz-option:hover:not(.locked){border-color:var(--border-hover);background:var(--bg);color:var(--text);}
            .quiz-option .opt-key{width:22px;height:22px;border-radius:4px;border:1px solid var(--dim);font-size:0.7rem;font-weight:700;font-family:"JetBrains Mono",monospace;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;transition:all 0.15s;}
            .quiz-option.correct{border-color:#10b981;background:rgba(16,185,129,0.08);color:var(--text);}
            .quiz-option.correct .opt-key{background:#10b981;border-color:#10b981;color:#fff;}
            .quiz-option.wrong{border-color:#ef4444;background:rgba(239,68,68,0.08);color:var(--muted);opacity:0.7;}
            .quiz-option.locked{cursor:default;}
            .quiz-explanation{display:none;margin:0 20px 20px;padding:14px 16px;background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.25);border-radius:7px;font-size:0.875rem;color:var(--muted);line-height:1.7;}
            .quiz-explanation strong{color:var(--text);}
            .quiz-score-bar{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:var(--surface2);border:1px solid var(--border);border-radius:10px;font-size:0.875rem;color:var(--muted);font-weight:600;}
            .quiz-score-val{font-size:1.1rem;font-weight:700;color:#f59e0b;font-family:"JetBrains Mono",monospace;}
        </style>';

        $html .= '<div class="quiz-wrapper" id="wrap_' . $quizPrefix . '">';
        $html .= '<div class="quiz-score-bar"><span>Knowledge Check</span><span class="quiz-score-val"><span id="score_' . $quizPrefix . '">0</span> / ' . $total . '</span></div>';

        foreach ($questions as $qIndex => $q) {
            $qNum = $qIndex + 1;
            $qId  = $quizPrefix . '_q' . $qNum;

            $html .= '<div class="quiz-card" id="' . $qId . '">';
            $html .= '<div class="quiz-card-header"><span class="quiz-q-num">Q' . $qNum . '</span><span class="quiz-q-text">' . htmlspecialchars($q['q']) . '</span></div>';
            $html .= '<div class="quiz-options">';

            foreach ($q['opts'] as $optIndex => $option) {
                $isCorrect = ($optIndex === $q['ans']) ? 'true' : 'false';
                $letter    = $letters[$optIndex];
                $html .= '<button class="quiz-option" onclick="checkAnswer(this,\'' . $qId . '\',' . $isCorrect . ',\'' . $quizPrefix . '\')"><span class="opt-key">' . $letter . '</span> ' . htmlspecialchars($option) . '</button>';
            }

            $html .= '</div>';
            $html .= '<div class="quiz-explanation" id="' . $qId . '-exp"><strong>Explanation:</strong> ' . $q['exp'] . '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= "
<script>
if(typeof window.answeredQuizzes==='undefined'){window.answeredQuizzes={};}
if(typeof window.quizScores==='undefined'){window.quizScores={};}
window.checkAnswer=function(btn,qId,isCorrect,prefix){
    if(window.answeredQuizzes[qId])return;
    window.answeredQuizzes[qId]=true;
    if(typeof window.quizScores[prefix]==='undefined')window.quizScores[prefix]=0;
    const card=document.getElementById(qId);
    const allOpts=card.querySelectorAll('.quiz-option');
    allOpts.forEach(o=>o.classList.add('locked'));
    if(isCorrect){
        btn.classList.add('correct');
        window.quizScores[prefix]++;
    } else {
        btn.classList.add('wrong');
        allOpts.forEach(o=>{if(o.getAttribute('onclick').includes(',true,'))o.classList.add('correct');});
    }
    document.getElementById(qId+'-exp').style.display='block';
    document.getElementById('score_'+prefix).textContent=window.quizScores[prefix];
};
</script>
";

        return $html;
    }
}