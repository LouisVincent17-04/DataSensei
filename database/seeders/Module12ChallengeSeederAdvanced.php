<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module12ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 12 — Introductory Forecasting (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introductory Forecasting',
            'description'           => 'Tackle deeper model diagnostics, code-level reasoning, parameter tuning trade-offs, and multi-concept forecasting problems. Code snippets are included — read them carefully.',
            'time_limit_seconds'    => 1800, // 30 minutes
            'base_xp'               => 1100,
            'order_index'           => 12,
        ]);

        $this->command->info("Seeding Advanced forecasting questions...");

        $qaData = [

            // ── EXPONENTIAL SMOOTHING — DEEP ──────────────────────────────
            [
                'q' => "Consider this Python snippet:\n\n```python\nfrom statsmodels.tsa.holtwinters import SimpleExpSmoothing\nmodel = SimpleExpSmoothing(series).fit(smoothing_level=0.9, optimized=False)\nfcast = model.forecast(3)\n```\n\nWith α = 0.9, the 3-step-ahead forecasts F_{t+1}, F_{t+2}, F_{t+3} will:",
                'opts' => [
                    ['All equal the most recent observation (SES produces flat forecasts)', true],
                    ['Grow linearly using the trend', false],
                    ['Decrease toward the long-run mean', false],
                    ['Vary based on seasonal indices', false],
                ],
            ],
            [
                'q' => "```python\nfrom statsmodels.tsa.holtwinters import ExponentialSmoothing\nmodel = ExponentialSmoothing(\n    series,\n    trend='add',\n    seasonal='add',\n    seasonal_periods=12\n).fit()\n```\n\nThis code fits which forecasting method?",
                'opts' => [
                    ['Holt\'s Double Exponential Smoothing', false],
                    ['Simple Exponential Smoothing', false],
                    ['Holt-Winters Additive (Triple Exponential Smoothing)', true],
                    ['SARIMA', false],
                ],
            ],
            [
                'q' => "Holt-Winters additive smoothing equations:\n\n  Lₜ = α(yₜ − Sₜ₋ₘ) + (1−α)(Lₜ₋₁ + Tₜ₋₁)\n  Tₜ = β(Lₜ − Lₜ₋₁) + (1−β)Tₜ₋₁\n  Sₜ = γ(yₜ − Lₜ) + (1−γ)Sₜ₋ₘ\n\nIf γ → 1, the seasonal component:\n",
                'opts' => [
                    ['Becomes a fixed constant across all years', false],
                    ['Tracks the most recent season deviation very aggressively', true],
                    ['Is effectively removed from the model', false],
                    ['Converges to the global mean seasonal factor', false],
                ],
            ],
            [
                'q' => "You have monthly data for 3 years. After fitting Holt-Winters, you extract:\n  Lₜ = 500, Tₜ = 3, S_{t+1} = −25, S_{t+2} = +40\n\nCompute F_{t+2} (2-step-ahead additive forecast):\n  F_{t+h} = Lₜ + h·Tₜ + Sₜ₋ₘ₊ₕ",
                'opts' => [
                    ['552', true],
                    ['506', false],
                    ['543', false],
                    ['540', false],
                ],
            ],

            // ── ARIMA — DEEPER REASONING ──────────────────────────────────
            [
                'q' => "```python\nfrom statsmodels.tsa.arima.model import ARIMA\nresult = ARIMA(y, order=(0, 1, 1)).fit()\nprint(result.summary())\n```\n\nAn ARIMA(0,1,1) model is mathematically equivalent to which smoothing method?",
                'opts' => [
                    ['Holt\'s Double Exponential Smoothing', false],
                    ['Simple Exponential Smoothing (SES)', true],
                    ['Holt-Winters Multiplicative', false],
                    ['4-period Moving Average', false],
                ],
            ],
            [
                'q' => "```python\nimport pmdarima as pm\nmodel = pm.auto_arima(\n    y,\n    seasonal=True,\n    m=12,\n    information_criterion='aic',\n    stepwise=True\n)\nprint(model.order, model.seasonal_order)\n# Output: (1, 1, 1) (1, 1, 1, 12)\n```\n\nHow many total parameters (φ, θ, Φ, Θ) does this model have (excluding the constant and differencing)?",
                'opts' => [
                    ['2', false],
                    ['3', false],
                    ['4', true],
                    ['6', false],
                ],
            ],
            [
                'q' => "After fitting ARIMA(1,1,1) to a series, the Ljung-Box test on residuals returns:\n  Q-statistic = 18.4, p-value = 0.001 (at lag 10)\n\nWhat does this indicate?",
                'opts' => [
                    ['The residuals are white noise; the model is adequate', false],
                    ['Significant autocorrelation remains in the residuals; the model is inadequate', true],
                    ['The series needs log transformation', false],
                    ['The model has too few parameters', false],
                ],
            ],
            [
                'q' => "For an AR(1) process: yₜ = φ₁yₜ₋₁ + εₜ\n\nThe process is stationary if and only if:",
                'opts' => [
                    ['φ₁ > 0', false],
                    ['|φ₁| < 1', true],
                    ['φ₁ = 1', false],
                    ['φ₁ > 1', false],
                ],
            ],
            [
                'q' => "An ARIMA model with d=1 and large p-values on all AR and MA coefficients suggests:\n\n```python\nresult.summary()\n# coef  std err  z  P>|z|\n#  ar.L1  0.05   0.48    0.10  0.92\n#  ma.L1 -0.03   0.51   -0.06  0.95\n```",
                'opts' => [
                    ['The model fits perfectly', false],
                    ['The AR and MA terms are not statistically significant; consider a simpler model', true],
                    ['More lags should be added', false],
                    ['The series needs further differencing', false],
                ],
            ],

            // ── INFORMATION CRITERIA & MODEL SELECTION ────────────────────
            [
                'q' => "AIC and BIC are used for model selection. The formulas are:\n  AIC = −2·ln(L) + 2k\n  BIC = −2·ln(L) + k·ln(n)\n\nFor large n, BIC tends to select _____ models compared to AIC.",
                'opts' => [
                    ['More complex (more parameters)', false],
                    ['More parsimonious (fewer parameters)', true],
                    ['Identical models', false],
                    ['Models with more differencing', false],
                ],
            ],
            [
                'q' => "You compare 4 models on the same training data:\n  ARIMA(1,1,0): AIC=310, BIC=318\n  ARIMA(1,1,1): AIC=304, BIC=315\n  ARIMA(2,1,1): AIC=303, BIC=320\n  ARIMA(2,1,2): AIC=301, BIC=330\n\nBased on AIC AND BIC together, which model offers the best balance?",
                'opts' => [
                    ['ARIMA(2,1,2) — lowest AIC', false],
                    ['ARIMA(1,1,0) — simplest model', false],
                    ['ARIMA(1,1,1) — both AIC and BIC are comparatively low', true],
                    ['ARIMA(2,1,1) — best AIC with moderate BIC', false],
                ],
            ],

            // ── ACF/PACF IDENTIFICATION ───────────────────────────────────
            [
                'q' => "```\nACF of differenced series:\n  lag 1:  0.62 ***\n  lag 2:  0.41 **\n  lag 3:  0.18\n  lag 4:  0.07\n\nPACF of differenced series:\n  lag 1:  0.62 ***\n  lag 2:  0.03\n  lag 3: -0.01\n```\n\nBased on the ACF (tailing off) and PACF (cut-off after lag 1), the appropriate non-seasonal ARIMA order is:",
                'opts' => [
                    ['ARIMA(0,1,1) — MA(1) after one differencing', false],
                    ['ARIMA(1,1,0) — AR(1) after one differencing', true],
                    ['ARIMA(2,1,0)', false],
                    ['ARIMA(0,1,2)', false],
                ],
            ],
            [
                'q' => "ACF shows spikes at lags 12 and 24 (seasonal period = 12). PACF shows a single spike at lag 12.\n\nThis pattern in the seasonal component suggests a seasonal:",
                'opts' => [
                    ['SMA(1) term: Q=1', false],
                    ['SAR(1) term: P=1', true],
                    ['Both SAR and SMA of order 1', false],
                    ['Second-order seasonal differencing', false],
                ],
            ],

            // ── FORECAST ERROR ANALYSIS ───────────────────────────────────
            [
                'q' => "```python\nerrors = actuals - forecasts\n# errors = [2, -3, 4, -2, 3, -4, 2, -3]\n```\n\nThe mean of errors ≈ 0 but they alternate in sign. This pattern in residuals suggests:",
                'opts' => [
                    ['The model is unbiased and adequate', false],
                    ['Systematic alternating pattern — possible MA or AR structure not captured', true],
                    ['The data has a trend that was not removed', false],
                    ['The forecast window is too short', false],
                ],
            ],
            [
                'q' => "You compute forecast errors for periods t=1 to t=10:\n  MAE = 8.2, RMSE = 15.7\n\nThe large gap between RMSE and MAE (RMSE >> MAE) indicates:",
                'opts' => [
                    ['All errors are approximately equal in magnitude', false],
                    ['A few extreme errors dominate; outlier periods exist', true],
                    ['The model is negatively biased', false],
                    ['The series is non-stationary', false],
                ],
            ],
            [
                'q' => "A naive forecast simply uses yₜ as the forecast for yₜ₊₁.\nYour ARIMA model achieves MASE = 0.72.\n\nWhat does this mean?",
                'opts' => [
                    ['The ARIMA forecast is worse than the naive forecast', false],
                    ['The ARIMA forecast is 28% better than the naive forecast on average', true],
                    ['The ARIMA model has 72% accuracy', false],
                    ['The naive forecast has MAE of 0.72', false],
                ],
            ],

            // ── STATIONARITY & TRANSFORMATION ────────────────────────────
            [
                'q' => "```python\nfrom statsmodels.tsa.stattools import adfuller\nresult = adfuller(series, autolag='AIC')\nprint(f'ADF: {result[0]:.2f}, p-value: {result[1]:.4f}')\n# Output: ADF: -1.23, p-value: 0.6612\n```\n\nWhat should you do next?",
                'opts' => [
                    ['Conclude the series is stationary and proceed with ARIMA(p,0,q)', false],
                    ['Apply differencing (d≥1) and re-run the ADF test', true],
                    ['Apply log transformation before differencing directly', false],
                    ['Fit a SARIMA model immediately', false],
                ],
            ],
            [
                'q' => "```python\nimport numpy as np\nlog_series = np.log(series)\ndiff_log = log_series.diff().dropna()\n```\n\nThis two-step transformation (log then difference) is used to:",
                'opts' => [
                    ['Remove seasonality and trend simultaneously', false],
                    ['Stabilize variance (log) and remove a linear trend (difference)', true],
                    ['Remove autocorrelation at all lags', false],
                    ['Convert multiplicative seasonality to additive', false],
                ],
            ],
            [
                'q' => "The Box-Cox transformation with λ=0 is equivalent to:\n\nyₜ* = (yₜ^λ − 1)/λ  as λ→0",
                'opts' => [
                    ['Square root transformation', false],
                    ['Natural log transformation', true],
                    ['First difference', false],
                    ['Inverse transformation', false],
                ],
            ],

            // ── CROSS-VALIDATION — ADVANCED ───────────────────────────────
            [
                'q' => "```python\nfrom sklearn.model_selection import TimeSeriesSplit\ntscv = TimeSeriesSplit(n_splits=5)\nfor train_idx, test_idx in tscv.split(X):\n    ...\n```\n\nCompared to k-fold cross-validation on time series, TimeSeriesSplit ensures:",
                'opts' => [
                    ['Equal-sized test sets across all folds', false],
                    ['Test set always occurs after training data, preventing leakage', true],
                    ['The model is retrained from scratch only once', false],
                    ['Data is randomly shuffled before splitting', false],
                ],
            ],
            [
                'q' => "You evaluate an ARIMA model using rolling origin evaluation with h=3 (3-step-ahead).\nRMSE at h=1: 4.2, h=2: 6.8, h=3: 10.1\n\nThe increasing RMSE across horizons is:",
                'opts' => [
                    ['A sign of overfitting', false],
                    ['Expected — forecast uncertainty compounds over longer horizons', true],
                    ['Caused by underdifferencing', false],
                    ['Caused by seasonal components', false],
                ],
            ],

            // ── PROPHET — ADVANCED ────────────────────────────────────────
            [
                'q' => "```python\nfrom prophet import Prophet\nm = Prophet(\n    changepoint_prior_scale=0.5,\n    seasonality_prior_scale=10.0\n)\nm.fit(df)\n```\n\nIncreasing `changepoint_prior_scale` to 0.5 (from default 0.05) will cause the trend to:",
                'opts' => [
                    ['Be smoother and less flexible', false],
                    ['Follow the training data more closely, risking overfitting', true],
                    ['Completely ignore changepoints', false],
                    ['Use additive seasonality only', false],
                ],
            ],
            [
                'q' => "In Prophet, you add a custom seasonality:\n\n```python\nm.add_seasonality(name='monthly', period=30.5, fourier_order=5)\n```\n\nThe `fourier_order` parameter controls:",
                'opts' => [
                    ['The number of historical periods used', false],
                    ['How flexible/complex the seasonal pattern can be', true],
                    ['The smoothing of the trend', false],
                    ['The number of changepoints allowed', false],
                ],
            ],

            // ── APPLIED DEBUGGING ─────────────────────────────────────────
            [
                'q' => "```python\nmodel = ARIMA(y, order=(2, 0, 2)).fit()\nresid = model.resid\nprint(resid.mean())   # -12.4\nprint(resid.std())    # 1.8\n```\n\nA residual mean of −12.4 indicates:",
                'opts' => [
                    ['The model is well-calibrated', false],
                    ['The model has a systematic positive bias — it consistently over-forecasts by ~12.4 units', true],
                    ['The variance is too high', false],
                    ['The model needs a higher AR order', false],
                ],
            ],
            [
                'q' => "```python\nforecast_ci = result.get_forecast(steps=6).conf_int(alpha=0.05)\n```\n\nWhat does `alpha=0.05` produce in the output?",
                'opts' => [
                    ['5% significance test on each forecast', false],
                    ['95% prediction intervals for each of the 6 forecasted steps', true],
                    ['Forecasts at 5 different horizons', false],
                    ['AIC penalty of 5', false],
                ],
            ],
            [
                'q' => "You fit SARIMA(1,1,1)(1,1,1)[12] and the seasonal MA coefficient Θ₁ = 0.99 with a standard error of 0.02.\n\nThis near-unit value (|Θ₁| ≈ 1) suggests:",
                'opts' => [
                    ['The seasonal MA term is very significant and useful', false],
                    ['Possible over-differencing — the seasonal MA root is near the unit circle (model instability)', true],
                    ['The model needs a higher seasonal AR order', false],
                    ['Seasonality was not present in the data', false],
                ],
            ],

            // ── CONCEPTUAL DEPTH ──────────────────────────────────────────
            [
                'q' => "The Box-Jenkins methodology for ARIMA modeling follows these steps in order:\n\n1. Identification (using ACF/PACF)\n2. Estimation\n3. Diagnostic checking\n4. Forecasting\n\nIf step 3 reveals model inadequacy, you return to:",
                'opts' => [
                    ['Step 4 — produce forecasts anyway', false],
                    ['Step 1 — re-identify a better model', true],
                    ['Step 2 — re-estimate with the same order', false],
                    ['Abandon ARIMA and use SES', false],
                ],
            ],
            [
                'q' => "Forecast intervals (prediction intervals) for an ARIMA model widen over the forecast horizon because:",
                'opts' => [
                    ['The model parameters change over time', false],
                    ['Error variance accumulates as forecasts compound on previous forecasts', true],
                    ['The training data becomes less relevant', false],
                    ['Seasonal indices are reset each period', false],
                ],
            ],
            [
                'q' => "An analyst fits both an ETS (Exponential Smoothing State Space) model and ARIMA(0,1,1) to the same data and gets nearly identical point forecasts.\n\nThe reason is that:",
                'opts' => [
                    ['Both models use the same AIC criterion', false],
                    ['ARIMA(0,1,1) is mathematically equivalent to SES, and ETS(A,N,N) is also SES — they share the same structure', true],
                    ['The data has no trend or seasonality', false],
                    ['Both models used the same smoothing parameter α', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 12 — Introductory Forecasting (Advanced).");
    }
}