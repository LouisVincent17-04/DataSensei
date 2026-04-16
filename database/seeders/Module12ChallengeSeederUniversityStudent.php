<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module12ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 12 — Introductory Forecasting (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introductory Forecasting',
            'description'           => 'Test your understanding of core forecasting concepts — time series components, basic smoothing methods, and simple model interpretation. Analytical thinking and light calculations required.',
            'time_limit_seconds'    => 1200, // 20 minutes
            'base_xp'               => 700,
            'order_index'           => 12,
        ]);

        $this->command->info("Seeding University Student forecasting questions...");

        $qaData = [

            // ── FORECASTING FUNDAMENTALS ──────────────────────────────────
            [
                'q' => 'A retail store tracks weekly sales from January to December. Which type of data is this?',
                'opts' => [
                    ['Cross-sectional data', false],
                    ['Time series data', true],
                    ['Panel data', false],
                    ['Categorical data', false],
                ],
            ],
            [
                'q' => 'Which of the following best describes a forecast?',
                'opts' => [
                    ['An exact calculation of a future value', false],
                    ['An estimate of a future value based on historical patterns', true],
                    ['A record of past observations', false],
                    ['A fixed target set by management', false],
                ],
            ],
            [
                'q' => 'If monthly electricity demand is consistently higher in summer than in winter every year, this pattern is called:',
                'opts' => [
                    ['Trend', false],
                    ['Residual', false],
                    ['Seasonality', true],
                    ['Cycle', false],
                ],
            ],
            [
                'q' => 'A time series has these values: 10, 12, 15, 19, 24. What component is most dominant?',
                'opts' => [
                    ['Seasonality', false],
                    ['Trend', true],
                    ['Noise', false],
                    ['Cycle', false],
                ],
            ],
            [
                'q' => 'In time series decomposition using an additive model, the relationship between components is:',
                'opts' => [
                    ['Y = T × S × R', false],
                    ['Y = T + S + R', true],
                    ['Y = T − S + R', false],
                    ['Y = T × S − R', false],
                ],
            ],
            [
                'q' => 'Which decomposition model is more appropriate when seasonal fluctuations grow proportionally with the trend?',
                'opts' => [
                    ['Additive model', false],
                    ['Multiplicative model', true],
                    ['Linear model', false],
                    ['Residual model', false],
                ],
            ],
            [
                'q' => 'After removing trend and seasonality from a time series, the leftover component is called:',
                'opts' => [
                    ['Forecast error', false],
                    ['White noise', false],
                    ['Residual (irregular) component', true],
                    ['Baseline', false],
                ],
            ],
            [
                'q' => 'You have quarterly sales data spanning 5 years. How many full seasonal cycles are present?',
                'opts' => [
                    ['4', false],
                    ['5', true],
                    ['20', false],
                    ['10', false],
                ],
            ],

            // ── SIMPLE MOVING AVERAGE ─────────────────────────────────────
            [
                'q' => 'Given the series: 4, 6, 8, 10, 12 — what is the 3-period simple moving average for period 4?',
                'opts' => [
                    ['8', true],
                    ['9', false],
                    ['10', false],
                    ['7', false],
                ],
            ],
            [
                'q' => 'A larger window (more periods) in a moving average produces a forecast that is:',
                'opts' => [
                    ['More responsive to recent changes', false],
                    ['Smoother but slower to react to changes', true],
                    ['Always more accurate', false],
                    ['More volatile', false],
                ],
            ],
            [
                'q' => 'Using a 2-period moving average, if the last two observations are 50 and 60, the next forecast is:',
                'opts' => [
                    ['60', false],
                    ['55', true],
                    ['50', false],
                    ['110', false],
                ],
            ],
            [
                'q' => 'One weakness of the simple moving average for forecasting is:',
                'opts' => [
                    ['It requires exponential calculations', false],
                    ['It treats all past observations in the window equally, ignoring recency', true],
                    ['It can only be applied to seasonal data', false],
                    ['It always over-forecasts', false],
                ],
            ],

            // ── SIMPLE EXPONENTIAL SMOOTHING ──────────────────────────────
            [
                'q' => 'In Simple Exponential Smoothing (SES), what does the smoothing parameter α control?',
                'opts' => [
                    ['The number of periods to include', false],
                    ['The weight given to the most recent observation vs the previous forecast', true],
                    ['The seasonal period length', false],
                    ['The trend growth rate', false],
                ],
            ],
            [
                'q' => 'If α = 0.3, the previous forecast F₁ = 100, and the actual value A₁ = 120, what is F₂ using SES?\n\nFormula: F₂ = α × A₁ + (1 − α) × F₁',
                'opts' => [
                    ['106', true],
                    ['110', false],
                    ['114', false],
                    ['100', false],
                ],
            ],
            [
                'q' => 'An α value close to 1 in SES means the forecast heavily weights:',
                'opts' => [
                    ['Very old observations', false],
                    ['The long-term average', false],
                    ['The most recent observation', true],
                    ['The seasonal component', false],
                ],
            ],
            [
                'q' => 'SES is most appropriate for time series that have:',
                'opts' => [
                    ['A strong upward trend', false],
                    ['Both trend and seasonality', false],
                    ['No trend and no seasonality', true],
                    ['Only a seasonal component', false],
                ],
            ],
            [
                'q' => "If α = 0, the SES forecast for every future period will be:\n\nHint: F_{t+1} = α·A_t + (1−α)·F_t",
                'opts' => [
                    ['The most recent actual value', false],
                    ['Always zero', false],
                    ['Equal to the initial forecast, unchanged forever', true],
                    ['The average of all past values', false],
                ],
            ],

            // ── HOLT'S METHOD ─────────────────────────────────────────────
            [
                'q' => "Holt's Double Exponential Smoothing extends SES by adding which component?",
                'opts' => [
                    ['Seasonality', false],
                    ['Trend', true],
                    ['Residuals', false],
                    ['Cycle', false],
                ],
            ],
            [
                'q' => "In Holt's method, the parameter β controls the smoothing of:",
                'opts' => [
                    ['The level of the series', false],
                    ['The trend estimate', true],
                    ['The seasonal factor', false],
                    ['The forecast error', false],
                ],
            ],
            [
                'q' => "In Holt's method, if the current level L₁ = 50 and trend T₁ = 5, what is the 2-step-ahead forecast?\n\nFormula: F_{t+h} = L_t + h·T_t",
                'opts' => [
                    ['55', false],
                    ['60', true],
                    ['65', false],
                    ['50', false],
                ],
            ],

            // ── HOLT-WINTERS METHOD ───────────────────────────────────────
            [
                'q' => "The Holt-Winters method is an extension of Holt's method that additionally handles:",
                'opts' => [
                    ['Autocorrelation', false],
                    ['Seasonality', true],
                    ['Stationarity', false],
                    ['Differencing', false],
                ],
            ],
            [
                'q' => 'The Holt-Winters additive model uses how many smoothing parameters?',
                'opts' => [
                    ['1 (α only)', false],
                    ['2 (α and β)', false],
                    ['3 (α, β, and γ)', true],
                    ['4', false],
                ],
            ],

            // ── FORECAST ACCURACY ─────────────────────────────────────────
            [
                'q' => 'Mean Absolute Error (MAE) is computed as:',
                'opts' => [
                    ['Average of squared forecast errors', false],
                    ['Average of the absolute differences between actual and forecasted values', true],
                    ['Sum of all forecast errors', false],
                    ['Percentage of correct forecasts', false],
                ],
            ],
            [
                'q' => 'Given actual = [10, 20, 30] and forecast = [12, 18, 33], what is the MAE?\n\nMAE = mean(|actual − forecast|)',
                'opts' => [
                    ['2.33', true],
                    ['3.00', false],
                    ['5.67', false],
                    ['1.00', false],
                ],
            ],
            [
                'q' => 'Why is RMSE (Root Mean Squared Error) often preferred over MAE in practice?',
                'opts' => [
                    ['It is always smaller than MAE', false],
                    ['It penalizes larger errors more heavily due to squaring', true],
                    ['It is scale-free', false],
                    ['It works only for seasonal data', false],
                ],
            ],
            [
                'q' => 'MAPE expresses forecast accuracy as:',
                'opts' => [
                    ['An absolute difference in units', false],
                    ['A percentage of the actual value', true],
                    ['A squared error value', false],
                    ['A count of correct predictions', false],
                ],
            ],
            [
                'q' => 'If MAPE = 5%, this means the forecast is off by an average of:',
                'opts' => [
                    ['5 units', false],
                    ['5% of the actual values', true],
                    ['5 squared units', false],
                    ['$5 in cost', false],
                ],
            ],

            // ── STATIONARITY & DIFFERENCING ───────────────────────────────
            [
                'q' => 'A stationary time series is one that:',
                'opts' => [
                    ['Has a clearly visible upward trend', false],
                    ['Has constant mean, variance, and autocovariance over time', true],
                    ['Has strong seasonal peaks', false],
                    ['Has an exponentially growing variance', false],
                ],
            ],
            [
                'q' => 'First-order differencing (Δyₜ = yₜ − yₜ₋₁) is used to remove:',
                'opts' => [
                    ['Seasonality only', false],
                    ['A linear trend', true],
                    ['Residual noise', false],
                    ['Outliers', false],
                ],
            ],
            [
                'q' => 'If the original series is: 2, 5, 9, 14 — what is the first difference series?',
                'opts' => [
                    ['3, 4, 5', true],
                    ['2, 5, 9', false],
                    ['1, 2, 3', false],
                    ['5, 4, 3', false],
                ],
            ],
            [
                'q' => 'The Augmented Dickey-Fuller (ADF) test is used to check for:',
                'opts' => [
                    ['Seasonality in a series', false],
                    ['A unit root (non-stationarity)', true],
                    ['Forecast accuracy', false],
                    ['Autocorrelation at lag 1', false],
                ],
            ],
            [
                'q' => 'In the ADF test, a p-value less than 0.05 leads you to:',
                'opts' => [
                    ['Conclude the series is non-stationary', false],
                    ['Reject the null hypothesis and conclude the series is stationary', true],
                    ['Accept that a unit root exists', false],
                    ['Apply seasonal differencing', false],
                ],
            ],

            // ── ACF & PACF ────────────────────────────────────────────────
            [
                'q' => 'The AutoCorrelation Function (ACF) measures:',
                'opts' => [
                    ['How a series correlates with a different series', false],
                    ['The correlation between a time series and its own lagged values', true],
                    ['The seasonal period of a series', false],
                    ['The strength of the trend component', false],
                ],
            ],
            [
                'q' => 'If the ACF of a series decays slowly and remains positive for many lags, this suggests the series is:',
                'opts' => [
                    ['Stationary', false],
                    ['Non-stationary (has a trend)', true],
                    ['Purely seasonal', false],
                    ['White noise', false],
                ],
            ],
            [
                'q' => 'The Partial AutoCorrelation Function (PACF) shows the correlation between yₜ and yₜ₋ₖ after:',
                'opts' => [
                    ['Squaring the residuals', false],
                    ['Removing the effects of intermediate lags', true],
                    ['Differencing the series once', false],
                    ['Applying a moving average filter', false],
                ],
            ],
            [
                'q' => 'In ACF/PACF plots, the dashed lines represent:',
                'opts' => [
                    ['The mean of the series', false],
                    ['Confidence intervals for significance testing', true],
                    ['The trend line', false],
                    ['Seasonal period markers', false],
                ],
            ],

            // ── ARIMA BASICS ──────────────────────────────────────────────
            [
                'q' => 'In ARIMA(p, d, q), what does the parameter d represent?',
                'opts' => [
                    ['The number of autoregressive terms', false],
                    ['The number of times the series is differenced', true],
                    ['The number of moving average terms', false],
                    ['The seasonal period', false],
                ],
            ],
            [
                'q' => 'An ARIMA(1, 0, 0) model is equivalent to which simpler model?',
                'opts' => [
                    ['A moving average model', false],
                    ['An autoregressive model of order 1 (AR(1))', true],
                    ['A random walk', false],
                    ['An exponential smoothing model', false],
                ],
            ],
            [
                'q' => 'SARIMA extends ARIMA by adding which additional capability?',
                'opts' => [
                    ['Handling non-linear trends', false],
                    ['Modeling seasonal patterns explicitly', true],
                    ['Removing outliers automatically', false],
                    ['Producing probability forecasts', false],
                ],
            ],

            // ── CROSS-VALIDATION & PROPHET ────────────────────────────────
            [
                'q' => 'In time series cross-validation, why must the test set always come AFTER the training set in time?',
                'opts' => [
                    ['To ensure equal sample sizes', false],
                    ['To prevent using future information to predict the past (data leakage)', true],
                    ['Because older data is less accurate', false],
                    ['To maximize the training set size', false],
                ],
            ],
            [
                'q' => 'The "walk-forward" validation strategy in time series forecasting means:',
                'opts' => [
                    ['Using a sliding window that always has the same fixed size', false],
                    ['Progressively expanding the training window and re-evaluating at each step', true],
                    ['Randomly sampling train and test sets', false],
                    ['Splitting data 70/30 once', false],
                ],
            ],
            [
                'q' => "Facebook's Prophet model is primarily designed to handle which time series challenges automatically?",
                'opts' => [
                    ['High-frequency sensor data', false],
                    ['Trend changepoints, multiple seasonalities, and holiday effects', true],
                    ['Only financial time series', false],
                    ['Only stationary series', false],
                ],
            ],
            [
                'q' => 'In Prophet, a "changepoint" refers to:',
                'opts' => [
                    ['A missing value in the series', false],
                    ['A point in time where the trend rate abruptly changes', true],
                    ['The beginning of a seasonal cycle', false],
                    ['An outlier detected by the model', false],
                ],
            ],

            // ── APPLIED INTERPRETATION ────────────────────────────────────
            [
                'q' => "A manager sees that their SES forecast always lags behind when demand spikes upward. The best fix is to:",
                'opts' => [
                    ['Decrease α to give more weight to older data', false],
                    ['Increase α to give more weight to recent observations', true],
                    ['Switch to a 12-period moving average', false],
                    ['Remove trend differencing', false],
                ],
            ],
            [
                'q' => "You are forecasting ice cream sales and notice the series has both an upward trend and strong summer peaks. Which method is most suitable?",
                'opts' => [
                    ['Simple Moving Average', false],
                    ['Simple Exponential Smoothing (SES)', false],
                    ['Holt-Winters (Triple Exponential Smoothing)', true],
                    ['AR(1) model', false],
                ],
            ],
            [
                'q' => 'Which accuracy metric is most useful when comparing forecasts across datasets with different scales?',
                'opts' => [
                    ['MAE', false],
                    ['RMSE', false],
                    ['MAPE', true],
                    ['Sum of squared errors', false],
                ],
            ],
            [
                'q' => 'A time series shows values: 100, 102, 101, 103, 102. This series most likely is:',
                'opts' => [
                    ['Trending strongly upward', false],
                    ['Roughly stationary around a mean of ~102', true],
                    ['Highly seasonal', false],
                    ['A random walk with drift', false],
                ],
            ],
            [
                'q' => "When your ARIMA model's residuals look like white noise (random, no pattern), this means:",
                'opts' => [
                    ['The model is underfitting the data', false],
                    ['The model has successfully captured all systematic patterns', true],
                    ['The model needs more differencing', false],
                    ['The forecast will have zero error', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 12 — Introductory Forecasting (University Student).");
    }
}