<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module12ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 12 — Introductory Forecasting (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introductory Forecasting',
            'description'           => 'Apply multi-step forecasting logic, interpret model diagnostics, and reason through method selection. Expect calculation traces, decomposition problems, and model comparison scenarios.',
            'time_limit_seconds'    => 1500, // 25 minutes
            'base_xp'               => 900,
            'order_index'           => 12,
        ]);

        $this->command->info("Seeding Intermediate forecasting questions...");

        $qaData = [

            // ── DECOMPOSITION ─────────────────────────────────────────────
            [
                'q' => "A time series has: Trend = 200, Seasonal Index = 1.15, Residual = 0.98.\nUsing the multiplicative model (Y = T × S × R), what is the observed value Y?\n\n(Round to 2 decimal places)",
                'opts' => [
                    ['225.54', true],
                    ['213.00', false],
                    ['200.00', false],
                    ['231.00', false],
                ],
            ],
            [
                'q' => "Using an additive model, a series has: Trend = 150, Seasonal = −20, Residual = 5.\nWhat is the observed value?",
                'opts' => [
                    ['175', false],
                    ['135', true],
                    ['125', false],
                    ['165', false],
                ],
            ],
            [
                'q' => "A quarterly series' seasonal indices are: Q1=0.80, Q2=1.10, Q3=1.25, Q4=0.85.\nWhat property should multiplicative seasonal indices satisfy across one full year?",
                'opts' => [
                    ['They must all equal 1.0', false],
                    ['They must sum to the number of seasons (4)', true],
                    ['They must sum to 0', false],
                    ['They must all be greater than 1', false],
                ],
            ],
            [
                'q' => "Monthly data shows actual = 360, trend = 300, seasonal index = 1.20 (multiplicative).\nWhat is the residual component R?\n\nR = Y / (T × S)",
                'opts' => [
                    ['1.00', true],
                    ['0.80', false],
                    ['60', false],
                    ['1.20', false],
                ],
            ],
            [
                'q' => "To seasonally adjust a multiplicative time series, you divide the observed value by:\n\nY = T × S × R  →  Adjusted = ?",
                'opts' => [
                    ['The trend', false],
                    ['The seasonal index', true],
                    ['The residual', false],
                    ['The sum of all components', false],
                ],
            ],

            // ── MOVING AVERAGE / SES CALCULATIONS ────────────────────────
            [
                'q' => "Given the series: 10, 14, 18, 22, 26 — compute the 4-period centered moving average for period 3.\n\nCMA₃ = (y₁ + y₂ + y₃ + y₄) / 4 using periods 1–4",
                'opts' => [
                    ['16', true],
                    ['18', false],
                    ['20', false],
                    ['14', false],
                ],
            ],
            [
                'q' => "SES with α = 0.4. The series and forecasts are:\n  t=1: A=50, F=50\n  t=2: A=60, F=?\n  t=3: A=55, F=?\n\nWhat is the forecast for t=3?\n\nF_t = α·A_{t-1} + (1−α)·F_{t-1}",
                'opts' => [
                    ['54', true],
                    ['56', false],
                    ['58', false],
                    ['60', false],
                ],
            ],
            [
                'q' => "Using SES with α = 0.5, F₁ = 100:\n  A₁ = 120, A₂ = 110, A₃ = 130\n\nWhat is F₄?",
                'opts' => [
                    ['117.5', true],
                    ['120', false],
                    ['115', false],
                    ['122.5', false],
                ],
            ],
            [
                'q' => "A 3-period weighted moving average assigns weights 0.5, 0.3, 0.2 (most recent first).\nFor the series: 8, 12, 10 (t=1,2,3), forecast for t=4 is:\n\nF₄ = 0.5×10 + 0.3×12 + 0.2×8",
                'opts' => [
                    ['10.2', true],
                    ['10.0', false],
                    ['11.0', false],
                    ['9.8', false],
                ],
            ],

            // ── HOLT'S TREND METHOD ───────────────────────────────────────
            [
                'q' => "Holt's method: α = 0.4, β = 0.3\nInitial level L₀ = 100, initial trend T₀ = 5\nActual at t=1: A₁ = 112\n\nCompute L₁:\nL₁ = α·A₁ + (1−α)·(L₀ + T₀)",
                'opts' => [
                    ['108.2', false],
                    ['109.8', false],
                    ['110.2', true],
                    ['112.0', false],
                ],
            ],
            [
                'q' => "Continuing from the previous question:\nL₁ = 110.2, T₀ = 5, β = 0.3\n\nCompute T₁:\nT₁ = β·(L₁ − L₀) + (1−β)·T₀",
                'opts' => [
                    ['5.06', true],
                    ['4.50', false],
                    ['6.10', false],
                    ['3.56', false],
                ],
            ],
            [
                'q' => "Using Holt's method with L₅ = 200 and T₅ = 8, what is the 3-step-ahead forecast F₈?\n\nF_{t+h} = L_t + h·T_t",
                'opts' => [
                    ['216', false],
                    ['224', true],
                    ['208', false],
                    ['232', false],
                ],
            ],
            [
                'q' => "Holt's method with a damped trend uses an additional parameter φ (0 < φ < 1). The h-step forecast becomes:\n\nF_{t+h} = L_t + (φ + φ² + ... + φʰ)·T_t\n\nWhat happens to forecasts as h → ∞ with damping?",
                'opts' => [
                    ['The forecast grows without bound', false],
                    ['The forecast converges to a finite value (flattens out)', true],
                    ['The forecast oscillates around the mean', false],
                    ['The forecast returns to zero', false],
                ],
            ],

            // ── ACCURACY METRICS CALCULATIONS ────────────────────────────
            [
                'q' => "Actual: [100, 110, 120], Forecast: [105, 108, 125]\n\nCompute RMSE.\nRMSE = sqrt(mean( (A - F)² ))",
                'opts' => [
                    ['4.97', true],
                    ['6.00', false],
                    ['3.67', false],
                    ['5.50', false],
                ],
            ],
            [
                'q' => "Actual: [200, 250, 300], Forecast: [210, 240, 315]\n\nCompute MAPE.\nMAPE = mean(|A − F| / A) × 100%",
                'opts' => [
                    ['4.17%', false],
                    ['4.44%', true],
                    ['5.00%', false],
                    ['3.33%', false],
                ],
            ],
            [
                'q' => "You compare two models:\n  Model A: MAE = 5, RMSE = 12\n  Model B: MAE = 6, RMSE = 7\n\nModel B has a lower RMSE despite a higher MAE. This suggests that Model A:",
                'opts' => [
                    ['Is uniformly worse across all periods', false],
                    ['Has a few large errors that inflate RMSE', true],
                    ['Is more biased overall', false],
                    ['Cannot be used for forecasting', false],
                ],
            ],
            [
                'q' => "MASE (Mean Absolute Scaled Error) scales the MAE by the MAE of a:\n\nMASE = MAE_model / MAE_naïve",
                'opts' => [
                    ['Linear regression benchmark', false],
                    ['Seasonal decomposition', false],
                    ['Naïve (random walk) forecast', true],
                    ['Exponential smoothing baseline', false],
                ],
            ],

            // ── STATIONARITY & ADF ────────────────────────────────────────
            [
                'q' => "Series: 2, 4, 8, 16, 32 (each term doubles).\n\nAfter taking the natural log, the transformed series is approximately:\nln(2), ln(4), ln(8), ln(16), ln(32)",
                'opts' => [
                    ['Still exponential', false],
                    ['Roughly linear, making it easier to difference to stationarity', true],
                    ['Already stationary without further transformation', false],
                    ['Seasonal', false],
                ],
            ],
            [
                'q' => "ADF test result:\n  Test statistic = −3.85\n  Critical value (5%) = −2.86\n  p-value = 0.003\n\nConclusion:",
                'opts' => [
                    ['Fail to reject H₀; series is non-stationary', false],
                    ['Reject H₀; series is stationary', true],
                    ['Cannot conclude anything', false],
                    ['Apply second-order differencing', false],
                ],
            ],
            [
                'q' => "A series requires second-order differencing (d=2) to become stationary. This most likely means the original series had:",
                'opts' => [
                    ['No trend', false],
                    ['A quadratic (accelerating) trend', true],
                    ['A pure seasonal pattern', false],
                    ['Only white noise', false],
                ],
            ],
            [
                'q' => "Seasonal differencing of order 1 with season length s=12 computes:\n\nΔ₁₂yₜ = yₜ − yₜ₋₁₂\n\nThis transformation removes:",
                'opts' => [
                    ['The trend component', false],
                    ['The seasonal component with period 12', true],
                    ['All autocorrelation', false],
                    ['Random noise', false],
                ],
            ],

            // ── ACF / PACF INTERPRETATION ─────────────────────────────────
            [
                'q' => "An ACF plot shows a large spike at lag 1 that cuts off sharply, with all other lags inside the confidence bands. This pattern suggests:",
                'opts' => [
                    ['AR(1) process', false],
                    ['MA(1) process', true],
                    ['ARIMA(1,1,1)', false],
                    ['Random walk', false],
                ],
            ],
            [
                'q' => "A PACF plot shows significant spikes at lags 1 and 2, then cuts off. The ACF decays exponentially. This pattern suggests:",
                'opts' => [
                    ['MA(2) process', false],
                    ['AR(2) process', true],
                    ['ARIMA(0,1,2)', false],
                    ['Pure trend', false],
                ],
            ],
            [
                'q' => "After fitting an ARIMA model, you plot the ACF of residuals and find spikes at lags 12 and 24. This suggests:",
                'opts' => [
                    ['The model is overfitted', false],
                    ['Unmodeled annual seasonality remains in the residuals', true],
                    ['The trend was not removed properly', false],
                    ['The residuals are white noise', false],
                ],
            ],

            // ── ARIMA MODEL IDENTIFICATION ────────────────────────────────
            [
                'q' => "For an ARIMA(2, 1, 0) model applied to a series yₜ, the model uses:\n- d=1 differencing\n- 2 lagged values of the differenced series\n- No moving average terms\n\nThe model equation for Δyₜ = yₜ − yₜ₋₁ is:",
                'opts' => [
                    ['Δyₜ = c + φ₁Δyₜ₋₁ + φ₂Δyₜ₋₂ + εₜ', true],
                    ['Δyₜ = c + θ₁εₜ₋₁ + θ₂εₜ₋₂ + εₜ', false],
                    ['Δyₜ = c + φ₁yₜ₋₁ + θ₁εₜ₋₁ + εₜ', false],
                    ['yₜ = c + φ₁yₜ₋₁ + φ₂yₜ₋₂ + εₜ', false],
                ],
            ],
            [
                'q' => "You are choosing between ARIMA(1,1,0) and ARIMA(1,1,1) using AIC values:\n  ARIMA(1,1,0): AIC = 345.2\n  ARIMA(1,1,1): AIC = 341.8\n\nWhich model do you prefer and why?",
                'opts' => [
                    ['ARIMA(1,1,0) — simpler models are always better', false],
                    ['ARIMA(1,1,1) — lower AIC indicates a better balance of fit and parsimony', true],
                    ['ARIMA(1,1,0) — higher AIC is better', false],
                    ['Cannot compare models with different numbers of parameters', false],
                ],
            ],
            [
                'q' => "A SARIMA(1,1,1)(1,1,1)[12] model notation means:\n  - Non-seasonal: p=1, d=1, q=1\n  - Seasonal: P=1, D=1, Q=1, s=12\n\nHow many total differencing operations are applied to the series?",
                'opts' => [
                    ['1 (only non-seasonal)', false],
                    ['2 (one non-seasonal + one seasonal)', true],
                    ['12', false],
                    ['0', false],
                ],
            ],

            // ── CROSS-VALIDATION ──────────────────────────────────────────
            [
                'q' => "You have 60 months of data. You use expanding-window cross-validation with an initial training size of 48 months and a 1-step-ahead forecast.\n\nHow many validation forecasts will you produce?",
                'opts' => [
                    ['48', false],
                    ['12', true],
                    ['60', false],
                    ['6', false],
                ],
            ],
            [
                'q' => "In time series cross-validation, which of these practices would cause data leakage?",
                'opts' => [
                    ['Training on data before the test period', false],
                    ['Scaling the data using statistics computed on the entire dataset before splitting', true],
                    ['Evaluating RMSE on the validation set', false],
                    ['Using expanding windows for retraining', false],
                ],
            ],

            // ── PROPHET ───────────────────────────────────────────────────
            [
                'q' => "Prophet decomposes the forecast as:\n\ny(t) = trend(t) + seasonality(t) + holidays(t) + εₜ\n\nIf holiday effects are not modeled and a major holiday causes a large spike, the model will:",
                'opts' => [
                    ['Capture the spike correctly via the trend component', false],
                    ['Attribute the spike to residuals, reducing accuracy', true],
                    ['Automatically detect it as a changepoint', false],
                    ['Produce a negative seasonal index', false],
                ],
            ],
            [
                'q' => "Prophet uses a piecewise linear or logistic growth model for the trend.\n\nThe logistic growth option is most appropriate when:",
                'opts' => [
                    ['The series grows without any upper bound', false],
                    ['The series is expected to saturate at a carrying capacity', true],
                    ['The series is stationary', false],
                    ['The series has weekly seasonality only', false],
                ],
            ],

            // ── APPLIED REASONING ─────────────────────────────────────────
            [
                'q' => "You fit an SES model and notice the forecast tracks actual values with a consistent 1-period delay and always underpredicts when the series is rising. The most likely cause is:",
                'opts' => [
                    ['α is too high', false],
                    ['SES cannot model trend; switch to Holt\'s or ARIMA with d≥1', true],
                    ['The seasonal component was not removed', false],
                    ['MAPE is greater than 10%', false],
                ],
            ],
            [
                'q' => "Monthly airline passenger data (classic Box-Jenkins dataset) shows:\n- Clear upward trend\n- Seasonal amplitude that grows with the level\n\nWhich transformation is recommended before fitting an additive model?",
                'opts' => [
                    ['No transformation needed', false],
                    ['Log transformation to stabilize the variance', true],
                    ['First differencing only', false],
                    ['Seasonal decomposition and discard trend', false],
                ],
            ],
            [
                'q' => "When comparing an in-sample fit (training error) versus out-of-sample forecast accuracy (test error), a large gap means:",
                'opts' => [
                    ['The model is underfitting', false],
                    ['The model is overfitting the training data', true],
                    ['The forecast horizon is too short', false],
                    ['More differencing is needed', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 12 — Introductory Forecasting (Intermediate).");
    }
}