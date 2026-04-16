<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module12ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 12 — Introductory Forecasting (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introductory Forecasting',
            'description'           => 'Real-world forecasting scenarios: production pipelines, edge cases, model failure diagnosis, performance trade-offs, and multi-model ensemble decisions. Designed for practitioners.',
            'time_limit_seconds'    => 2100, // 35 minutes
            'base_xp'               => 1400,
            'order_index'           => 12,
        ]);

        $this->command->info("Seeding Professional forecasting questions...");

        $qaData = [

            // ── PRODUCTION PIPELINE ISSUES ────────────────────────────────
            [
                'q' => "```python\ndef retrain_and_forecast(series, order=(1,1,1)):\n    model = ARIMA(series, order=order).fit()\n    return model.forecast(steps=1)[0]\n\n# Called daily in production:\nfor day in new_data:\n    series.append(day)\n    forecast = retrain_and_forecast(series)\n```\n\nThis pipeline retrains the ARIMA model from scratch every day on the full growing history.\nWhat is the primary production concern as `series` grows to millions of data points?",
                'opts' => [
                    ['The ARIMA order will automatically change', false],
                    ['Computational cost grows significantly; consider online updating or capped rolling windows', true],
                    ['ARIMA cannot accept more than 1000 data points', false],
                    ['The forecast will degrade in accuracy over time', false],
                ],
            ],
            [
                'q' => "```python\nmodel = ARIMA(series, order=(2, 1, 2)).fit()\nnew_obs = [105, 110]\nupdated = model.append(new_obs, refit=False)\nforecast = updated.forecast(steps=3)\n```\n\nUsing `refit=False` when appending new observations means:",
                'opts' => [
                    ['The model will refit its parameters using all historical data', false],
                    ['New observations extend the state but parameters stay fixed — faster but may drift from optimal', true],
                    ['The model is discarded and rebuilt from scratch', false],
                    ['Only the last 2 observations are used for forecasting', false],
                ],
            ],
            [
                'q' => "In a real-time forecasting system, the series contains a structural break — an abrupt permanent shift in level after a major policy change.\n\nWhich approach best handles this WITHOUT discarding all historical data?",
                'opts' => [
                    ['Increase differencing order to d=2', false],
                    ['Use intervention modeling (adding a step-dummy regressor at the break point) in ARIMAX/SARIMAX', true],
                    ['Increase the AR order to p=5', false],
                    ['Switch to a 3-period moving average', false],
                ],
            ],

            // ── EDGE CASES ────────────────────────────────────────────────
            [
                'q' => "```python\nseries = [100, 102, 101, 103, 0, 104, 103]\n# Note: index 4 is 0 (likely a missing/erroneous reading)\n```\n\nBefore fitting any forecasting model, what is the correct treatment for this zero value?",
                'opts' => [
                    ['Leave it as-is; ARIMA handles zeros naturally', false],
                    ['Investigate if it is a true zero or a missing/erroneous reading, then impute or flag it', true],
                    ['Replace all zeros with the series mean', false],
                    ['Remove the entire period from the dataset', false],
                ],
            ],
            [
                'q' => "You are forecasting daily retail sales. December 25th always shows near-zero sales (store closed).\nIn Prophet, the correct way to handle this is:",
                'opts' => [
                    ['Remove all December 25th rows from training data', false],
                    ['Add a holidays dataframe specifying December 25th as a holiday with lower_window/upper_window', true],
                    ['Apply seasonal differencing with s=365', false],
                    ['Set changepoint_prior_scale = 0.001 to suppress spikes', false],
                ],
            ],
            [
                'q' => "MAPE has a known failure mode. Which of the following cases will cause MAPE to be unreliable or undefined?",
                'opts' => [
                    ['When actual values contain zeros or near-zeros', true],
                    ['When RMSE > MAE', false],
                    ['When the series is non-stationary', false],
                    ['When the forecast horizon exceeds 12 periods', false],
                ],
            ],
            [
                'q' => "A series of daily web traffic counts contains:\n  - A long-run upward trend\n  - Weekly seasonality (lower on weekends)\n  - A 1-week spike caused by a viral marketing campaign\n\nWhich metric is MOST appropriate for evaluating this model while being robust to the campaign spike?",
                'opts' => [
                    ['MAPE — scale-free', false],
                    ['RMSE — sensitive to large errors', false],
                    ['MASE — uses the naive benchmark, inherently robust to scale and outlier periods', true],
                    ['MAE — always best', false],
                ],
            ],

            // ── MULTI-MODEL / ENSEMBLE ────────────────────────────────────
            [
                'q' => "You train three models on the same series:\n  Model A (SARIMA): test RMSE = 12.4\n  Model B (Holt-Winters): test RMSE = 11.8\n  Model C (Prophet): test RMSE = 13.1\n\nAn equal-weight ensemble forecast averages the three models.\nThe ensemble RMSE will likely be:",
                'opts' => [
                    ['Equal to the worst model (13.1)', false],
                    ['Lower than the best individual model (11.8) in most cases', true],
                    ['Exactly 12.43 (arithmetic mean of RMSEs)', false],
                    ['Higher than all three models', false],
                ],
            ],
            [
                'q' => "In an optimally weighted ensemble, weights are determined by minimizing:\n\n  w* = argmin Σ wᵢ · RMSEᵢ   subject to Σwᵢ = 1, wᵢ ≥ 0\n\nCompared to simple averaging, optimal weighting:",
                'opts' => [
                    ['Always performs worse due to overfitting the weights', false],
                    ['Can improve performance on the validation set but may overfit if weights are tuned on the test set', true],
                    ['Is equivalent to selecting only the best model', false],
                    ['Is only valid when all models have the same RMSE', false],
                ],
            ],

            // ── PRODUCTION ARIMA / SARIMAX ────────────────────────────────
            [
                'q' => "```python\nfrom statsmodels.tsa.statespace.sarimax import SARIMAX\nmodel = SARIMAX(\n    endog=y,\n    exog=X,   # e.g., price promotions, temperature\n    order=(1,1,1),\n    seasonal_order=(1,1,1,52)\n).fit(disp=False)\n```\n\nIn a production forecast, the exogenous variables X for future periods must be:",
                'opts' => [
                    ['Estimated automatically by SARIMAX', false],
                    ['Known or separately forecasted ahead of time before generating y forecasts', true],
                    ['Set to their historical mean values always', false],
                    ['Dropped from the forecast call', false],
                ],
            ],
            [
                'q' => "```python\nresult = SARIMAX(y, order=(1,1,1), seasonal_order=(1,1,1,12)).fit()\nprint(result.aic, result.bic)\n# AIC=1245.3, BIC=1268.7\n\nresult2 = SARIMAX(y, order=(2,1,2), seasonal_order=(2,1,1,12)).fit()\nprint(result2.aic, result2.bic)\n# AIC=1241.0, BIC=1289.4\n```\n\nAIC favors result2 but BIC favors result. In a production environment where interpretability and stability over new data matters, you should:",
                'opts' => [
                    ['Always follow AIC', false],
                    ['Always follow BIC', false],
                    ['Prefer the BIC-selected model (result) — BIC penalizes complexity more heavily, reducing overfitting risk on new data', true],
                    ['Average the forecasts from both models', false],
                ],
            ],

            // ── FORECAST UNCERTAINTY & INTERVALS ──────────────────────────
            [
                'q' => "```python\nforecast = result.get_forecast(steps=24)\nci = forecast.conf_int(alpha=0.05)\nwidth_h1 = ci.iloc[0,1] - ci.iloc[0,0]   # width at h=1\nwidth_h24 = ci.iloc[23,1] - ci.iloc[23,0] # width at h=24\nprint(width_h24 / width_h1)\n# Output: 4.7\n```\n\nPrediction interval width at h=24 is 4.7× the width at h=1. This means:",
                'opts' => [
                    ['The model is performing poorly', false],
                    ['Forecast uncertainty grows substantially at longer horizons due to compounding error variance', true],
                    ['The model needs more AR terms', false],
                    ['The 24-step forecast is unusable', false],
                ],
            ],
            [
                'q' => "A business stakeholder asks: 'Can you give me a guaranteed sales forecast for next quarter?'\n\nThe statistically correct response is:",
                'opts' => [
                    ['Yes — ARIMA produces deterministic forecasts', false],
                    ['No — all forecasts are probabilistic estimates; I can provide a point forecast and a 95% prediction interval', true],
                    ['Only Prophet can guarantee accuracy', false],
                    ['Yes if MAPE < 5%', false],
                ],
            ],

            // ── PERFORMANCE & SCALABILITY ──────────────────────────────────
            [
                'q' => "You need to generate daily 30-day-ahead forecasts for 50,000 SKUs in a retail inventory system.\nFitting individual ARIMA models for each SKU is computationally feasible but takes 8 hours.\n\nWhich approach best trades accuracy for scalability?",
                'opts' => [
                    ['Use a single global ARIMA model for all SKUs', false],
                    ['Use a global deep learning model (e.g., N-BEATS, LightGBM-based) trained across all SKUs simultaneously', true],
                    ['Fit Holt-Winters for every SKU; it is faster than ARIMA', false],
                    ['Use only the naive forecast for all SKUs', false],
                ],
            ],
            [
                'q' => "In a distributed forecasting pipeline:\n\n```python\nfrom joblib import Parallel, delayed\n\ndef fit_forecast_sku(sku_data):\n    model = ExponentialSmoothing(\n        sku_data, trend='add', seasonal='add',\n        seasonal_periods=52\n    ).fit()\n    return model.forecast(12)\n\nresults = Parallel(n_jobs=-1)(\n    delayed(fit_forecast_sku)(df) for df in sku_list\n)\n```\n\nThe `n_jobs=-1` parameter means:",
                'opts' => [
                    ['Run on 1 CPU core for reliability', false],
                    ['Use all available CPU cores in parallel', true],
                    ['Run the forecasts sequentially with no parallelism', false],
                    ['Limit processing to 1 thread for memory safety', false],
                ],
            ],

            // ── ADVANCED DIAGNOSTICS ──────────────────────────────────────
            [
                'q' => "```python\nfrom statsmodels.stats.diagnostic import acorr_ljungbox\nlb_result = acorr_ljungbox(resid, lags=[10, 20], return_df=True)\nprint(lb_result)\n#     lb_stat  lb_pvalue\n# 10    8.12     0.619\n# 20   22.45     0.317\n```\n\nBased on these Ljung-Box results (all p-values > 0.05), you conclude:",
                'opts' => [
                    ['The residuals show significant autocorrelation; respecify the model', false],
                    ['The residuals behave as white noise; the model is adequate', true],
                    ['The series needs log transformation', false],
                    ['The model is overfitted', false],
                ],
            ],
            [
                'q' => "After fitting ARIMA, you check residual normality:\n\n```python\nfrom scipy import stats\nstat, p = stats.shapiro(resid)\n# stat=0.88, p=0.003\n```\n\nThe Shapiro-Wilk test rejects normality (p=0.003). The practical implication for production forecasting is:",
                'opts' => [
                    ['The ARIMA model must be discarded entirely', false],
                    ['Point forecasts remain valid, but Gaussian prediction intervals may be miscalibrated; consider bootstrapped intervals', true],
                    ['More differencing is required', false],
                    ['The model has perfect accuracy', false],
                ],
            ],
            [
                'q' => "You observe that your SARIMA model's one-step-ahead forecast errors are:\n  - Mean ≈ 0 ✓\n  - No autocorrelation ✓\n  - Variance is stable for 80% of the series, but spikes dramatically during Q4 every year\n\nThis Q4 variance spike is an example of:",
                'opts' => [
                    ['Non-stationarity in mean', false],
                    ['Conditional heteroskedasticity — consider GARCH or seasonal variance modeling', true],
                    ['Model overfitting', false],
                    ['A broken trend component', false],
                ],
            ],

            // ── PROPHET — PRODUCTION ──────────────────────────────────────
            [
                'q' => "```python\nm = Prophet(uncertainty_samples=1000)\nm.fit(df)\nfuture = m.make_future_dataframe(periods=90)\nforecast = m.predict(future)\n```\n\nIncreasing `uncertainty_samples` from the default 1000 to 10000 will:",
                'opts' => [
                    ['Improve point forecast accuracy', false],
                    ['Produce smoother and more reliable prediction intervals at higher computational cost', true],
                    ['Reduce the forecast horizon', false],
                    ['Change the trend model from linear to logistic', false],
                ],
            ],
            [
                'q' => "In a Prophet model trained on 3 years of daily data, you notice the model performs poorly in the last 6 months of the training set — the residuals are large and systematic.\n\nThe most likely cause and fix is:",
                'opts' => [
                    ['α is too small; increase it', false],
                    ['The trend changepoints are not capturing a recent trend shift — increase n_changepoints or extend the changepoint_range closer to the end of training', true],
                    ['The model needs a higher Fourier order for seasonality', false],
                    ['The model requires more holiday regressors', false],
                ],
            ],

            // ── REAL-WORLD SCENARIO INTEGRATION ──────────────────────────
            [
                'q' => "A data scientist builds an ARIMA(1,1,1) model that achieves test RMSE = 50 on monthly electricity demand (in MWh). A senior colleague notes that a naive seasonal forecast (y_{t} = y_{t-12}) achieves test RMSE = 48.\n\nWhat is the correct interpretation?",
                'opts' => [
                    ['ARIMA is better because it has lower complexity', false],
                    ['The ARIMA model adds no value over the naive seasonal benchmark and should be discarded or improved', true],
                    ['The naive seasonal forecast is always the best baseline', false],
                    ['RMSE of 50 is low enough for production use', false],
                ],
            ],
            [
                'q' => "You are deploying a forecasting model to production. The training data runs from 2018–2023, and the model is deployed in 2024.\n\nA business decision requires forecasts for 2026 (2 years ahead). What is the primary risk?",
                'opts' => [
                    ['The model will run out of memory', false],
                    ['Forecast uncertainty is very high at 2-year horizons; structural changes (policy shifts, demand shocks) are not captured in the 2018–2023 patterns', true],
                    ['ARIMA cannot produce 2-year forecasts', false],
                    ['The seasonal period will reset to zero', false],
                ],
            ],
            [
                'q' => "A retail chain uses SARIMA to forecast weekly sales per store.\nAfter deployment, you monitor forecast performance and notice MASE is gradually increasing month over month from 0.82 to 1.15 over 6 months.\n\nMASE > 1 means the model is now performing worse than the naive benchmark.\nThe correct operational response is:",
                'opts' => [
                    ['Continue monitoring; occasional MASE spikes are normal', false],
                    ['Trigger a model retraining pipeline using more recent data to capture the updated patterns', true],
                    ['Switch all stores to manual forecasting', false],
                    ['Reduce the forecast horizon to 1 week', false],
                ],
            ],
            [
                'q' => "You fit Holt-Winters Multiplicative on a 5-year monthly series.\nWhen you try to forecast the next 12 months, the predicted values go negative for some periods.\n\nWhat is the most likely cause?",
                'opts' => [
                    ['β is too large', false],
                    ['The series contains zeros or near-zeros, causing division issues in multiplicative seasonal indices', true],
                    ['The seasonal period was set to 4 instead of 12', false],
                    ['There were too few training observations', false],
                ],
            ],
            [
                'q' => "In a production forecasting system, you must choose between:\n  - Model A: ARIMA — 98% accuracy, 45 minutes to train, highly interpretable\n  - Model B: Deep learning ensemble — 99.2% accuracy, 6 hours to train, black-box\n\nFor a compliance-regulated financial institution where explainability is required, you recommend:",
                'opts' => [
                    ['Model B — always optimize for accuracy', false],
                    ['Model A — regulatory explainability constraints outweigh the marginal accuracy gain', true],
                    ['Neither — use a naive forecast for compliance', false],
                    ['Average both models to satisfy both concerns', false],
                ],
            ],
            [
                'q' => "```python\nfrom statsmodels.tsa.statespace.sarimax import SARIMAX\n\nmodel = SARIMAX(\n    y_train,\n    order=(1,1,1),\n    seasonal_order=(0,1,1,12),\n    enforce_stationarity=False,\n    enforce_invertibility=False\n).fit(disp=False)\n```\n\nSetting `enforce_stationarity=False` and `enforce_invertibility=False` means:",
                'opts' => [
                    ['The model guarantees stationarity and invertibility automatically', false],
                    ['The optimizer is not constrained to the stationary/invertible parameter space — can yield explosive or non-invertible solutions if the data or initial values are problematic', true],
                    ['The model will always converge faster', false],
                    ['Differencing is disabled', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 12 — Introductory Forecasting (Professional).");
    }
}