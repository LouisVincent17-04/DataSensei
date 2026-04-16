<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module12ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 12 — Introductory Forecasting (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introductory Forecasting',
            'description'           => 'Test your very first knowledge of forecasting — what it is, why we use it, and the basic building blocks of time series data. No prior statistics experience assumed!',
            'time_limit_seconds'    => 900, // 15 minutes
            'base_xp'               => 500,
            'order_index'           => 12,
        ]);

        $this->command->info("Seeding 50 newbie-friendly forecasting questions...");

        $qaData = [

            // ── WHAT IS FORECASTING ────────────────────────────────────────
            [
                'q' => 'What does "forecasting" mean?',
                'opts' => [
                    ['Analyzing what happened in the past', false],
                    ['Predicting or estimating future values based on past data', true],
                    ['Collecting new data from surveys', false],
                    ['Fixing errors in a dataset', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of forecasting?',
                'opts' => [
                    ['Counting how many customers visited last month', false],
                    ['Predicting how many customers will visit next month', true],
                    ['Listing all products sold in 2022', false],
                    ['Calculating last year\'s profit', false],
                ],
            ],
            [
                'q' => 'A "time series" is best described as:',
                'opts' => [
                    ['A list of names sorted alphabetically', false],
                    ['A series of measurements collected over time at regular intervals', true],
                    ['A table of student grades', false],
                    ['A random collection of numbers', false],
                ],
            ],
            [
                'q' => 'Which of the following is a time series?',
                'opts' => [
                    ['The heights of 20 students measured at the same time', false],
                    ['Monthly rainfall recorded in Manila from 2015 to 2024', true],
                    ['A list of product names in a store', false],
                    ['A one-time survey of 100 customers', false],
                ],
            ],
            [
                'q' => 'In forecasting, what does the word "horizon" refer to?',
                'opts' => [
                    ['The line where the sky meets the ground', false],
                    ['How far into the future you are trying to predict', true],
                    ['The number of past observations used', false],
                    ['The accuracy of the forecast', false],
                ],
            ],
            [
                'q' => 'A company records daily ice cream sales for 2 years and wants to predict next month\'s sales. This is called a:',
                'opts' => [
                    ['Classification problem', false],
                    ['Time series forecasting problem', true],
                    ['Clustering problem', false],
                    ['Data cleaning task', false],
                ],
            ],
            [
                'q' => 'Which of these fields commonly uses forecasting?',
                'opts' => [
                    ['Weather prediction', true],
                    ['Painting pictures', false],
                    ['Learning a language', false],
                    ['Cooking recipes', false],
                ],
            ],
            [
                'q' => 'A "forecast" is always:',
                'opts' => [
                    ['100% accurate', false],
                    ['An estimate with some degree of uncertainty', true],
                    ['A guarantee of the future', false],
                    ['Calculated without any data', false],
                ],
            ],

            // ── TIME SERIES COMPONENTS ────────────────────────────────────
            [
                'q' => 'If ice cream sales are higher every summer and lower every winter, year after year, this repeating pattern is called:',
                'opts' => [
                    ['Trend', false],
                    ['Noise', false],
                    ['Seasonality', true],
                    ['Residual', false],
                ],
            ],
            [
                'q' => 'If smartphone sales have been gradually increasing every year for the past 10 years, this long-term movement is called:',
                'opts' => [
                    ['Seasonality', false],
                    ['Trend', true],
                    ['Cycle', false],
                    ['Residual', false],
                ],
            ],
            [
                'q' => 'The "residual" (or irregular) component in a time series refers to:',
                'opts' => [
                    ['The overall direction of the data over time', false],
                    ['The repeating seasonal pattern', false],
                    ['Random, unexplained variation that cannot be predicted', true],
                    ['The average of all values', false],
                ],
            ],
            [
                'q' => 'A time series has four main components. Which of the following is NOT one of them?',
                'opts' => [
                    ['Trend', false],
                    ['Seasonality', false],
                    ['Correlation', true],
                    ['Residual', false],
                ],
            ],
            [
                'q' => 'Looking at a line graph of monthly sales, you notice the line generally goes upward from left to right over 5 years. This indicates a:',
                'opts' => [
                    ['Seasonal pattern', false],
                    ['Downward trend', false],
                    ['Upward trend', true],
                    ['Random walk', false],
                ],
            ],
            [
                'q' => 'Electricity usage spikes every December due to Christmas lights and heaters. This is an example of:',
                'opts' => [
                    ['A residual', false],
                    ['A trend', false],
                    ['Seasonality', true],
                    ['A cycle', false],
                ],
            ],
            [
                'q' => 'A sudden unexplained spike in sales data due to a one-time viral social media post would most likely appear in which component?',
                'opts' => [
                    ['Trend', false],
                    ['Seasonality', false],
                    ['Residual (irregular)', true],
                    ['Cycle', false],
                ],
            ],
            [
                'q' => 'Which statement about seasonality is correct?',
                'opts' => [
                    ['It repeats at unpredictable intervals', false],
                    ['It always lasts exactly 12 months', false],
                    ['It repeats within a fixed, known period (e.g., daily, weekly, yearly)', true],
                    ['It only occurs in weather data', false],
                ],
            ],

            // ── FORECASTING WORKFLOW ──────────────────────────────────────
            [
                'q' => 'What is the first step in building a forecast?',
                'opts' => [
                    ['Pick the most complicated model available', false],
                    ['Collect and understand the historical data', true],
                    ['Publish the results immediately', false],
                    ['Skip directly to predicting the future', false],
                ],
            ],
            [
                'q' => 'Why do we split data into a "training set" and a "test set" when building a forecast model?',
                'opts' => [
                    ['To make the dataset smaller', false],
                    ['To evaluate how well the model predicts data it has never seen', true],
                    ['Training set is for managers and test set is for analysts', false],
                    ['There is no reason; it is optional', false],
                ],
            ],
            [
                'q' => 'In time series, the training set should come _____ the test set.',
                'opts' => [
                    ['After', false],
                    ['At the same time as', false],
                    ['Before', true],
                    ['In a random order relative to', false],
                ],
            ],
            [
                'q' => 'After you make a forecast, how do you know if it was good?',
                'opts' => [
                    ['Compare the forecasted values to the actual values that occurred', true],
                    ['Ask a friend if the numbers look right', false],
                    ['A forecast is always perfect, so there is nothing to check', false],
                    ['Use the most complicated formula available', false],
                ],
            ],
            [
                'q' => '"Forecast error" is defined as:',
                'opts' => [
                    ['Actual value − Forecasted value', true],
                    ['Forecasted value × 100', false],
                    ['The average of all past values', false],
                    ['The trend divided by the season', false],
                ],
            ],
            [
                'q' => 'You predicted sales of 100 units but actual sales were 115 units. The forecast error is:',
                'opts' => [
                    ['−15', false],
                    ['15', true],
                    ['115', false],
                    ['1.15', false],
                ],
            ],

            // ── SIMPLE FORECASTING METHODS ────────────────────────────────
            [
                'q' => 'The simplest possible forecast is the "naive forecast." It predicts the next value as:',
                'opts' => [
                    ['The average of all past observations', false],
                    ['The same value as the most recent observation', true],
                    ['Zero', false],
                    ['A randomly chosen past value', false],
                ],
            ],
            [
                'q' => 'Yesterday\'s temperature was 32°C. Using a naive forecast, what is today\'s temperature forecast?',
                'opts' => [
                    ['0°C', false],
                    ['32°C', true],
                    ['The average of all past temperatures', false],
                    ['Cannot be determined', false],
                ],
            ],
            [
                'q' => 'A "moving average" forecast computes the next value as:',
                'opts' => [
                    ['The maximum of all past values', false],
                    ['The average of a fixed number of most recent past values', true],
                    ['The difference between the last two values', false],
                    ['The minimum of all past values', false],
                ],
            ],
            [
                'q' => 'Given sales: 10, 12, 14 — what is the 3-period simple moving average forecast for the next period?',
                'opts' => [
                    ['14', false],
                    ['12', true],
                    ['10', false],
                    ['36', false],
                ],
            ],
            [
                'q' => 'A larger number of periods in a moving average makes the forecast:',
                'opts' => [
                    ['React quickly to recent changes', false],
                    ['Smoother but slower to react to new changes', true],
                    ['Always more accurate', false],
                    ['Smaller in value', false],
                ],
            ],
            [
                'q' => 'Simple Exponential Smoothing (SES) is different from a moving average because it:',
                'opts' => [
                    ['Gives equal weight to all past values', false],
                    ['Gives more weight to recent values and less to older ones', true],
                    ['Only works with seasonal data', false],
                    ['Requires at least 100 data points', false],
                ],
            ],
            [
                'q' => 'The smoothing parameter in SES is called:',
                'opts' => [
                    ['Beta (β)', false],
                    ['Gamma (γ)', false],
                    ['Alpha (α)', true],
                    ['Lambda (λ)', false],
                ],
            ],
            [
                'q' => 'In SES, alpha (α) must be a value between:',
                'opts' => [
                    ['−1 and 1', false],
                    ['0 and 1', true],
                    ['1 and 100', false],
                    ['0 and 10', false],
                ],
            ],

            // ── ACCURACY BASICS ───────────────────────────────────────────
            [
                'q' => 'MAE stands for:',
                'opts' => [
                    ['Maximum Accuracy Error', false],
                    ['Mean Absolute Error', true],
                    ['Moving Average Estimate', false],
                    ['Model Accuracy Evaluation', false],
                ],
            ],
            [
                'q' => 'MAE measures:',
                'opts' => [
                    ['The largest single forecast error', false],
                    ['The average size of forecast errors, ignoring direction (positive or negative)', true],
                    ['The percentage accuracy of a forecast', false],
                    ['The total number of forecasts made', false],
                ],
            ],
            [
                'q' => 'If your forecast errors are: 3, −2, 4 — what is the MAE?\n\nMAE = average of |errors|',
                'opts' => [
                    ['5', false],
                    ['3', true],
                    ['1.67', false],
                    ['9', false],
                ],
            ],
            [
                'q' => 'RMSE stands for:',
                'opts' => [
                    ['Root Mean Squared Error', true],
                    ['Random Mean Standard Estimate', false],
                    ['Regression Model Score Evaluation', false],
                    ['Relative Mean Squared Error', false],
                ],
            ],
            [
                'q' => 'Compared to MAE, RMSE punishes _____ errors more heavily.',
                'opts' => [
                    ['Small', false],
                    ['Negative', false],
                    ['Large', true],
                    ['All errors equally', false],
                ],
            ],
            [
                'q' => 'MAPE expresses forecast accuracy as a:',
                'opts' => [
                    ['Dollar amount', false],
                    ['Count of wrong forecasts', false],
                    ['Percentage', true],
                    ['Squared value', false],
                ],
            ],
            [
                'q' => 'If MAPE = 10%, your forecast is off by an average of:',
                'opts' => [
                    ['10 units', false],
                    ['10% of the actual values', true],
                    ['10 squared units', false],
                    ['$10', false],
                ],
            ],
            [
                'q' => 'A lower MAE means the forecast is:',
                'opts' => [
                    ['Less accurate', false],
                    ['More accurate (smaller average error)', true],
                    ['More complex', false],
                    ['Slower to compute', false],
                ],
            ],

            // ── BASIC CONCEPTS ────────────────────────────────────────────
            [
                'q' => 'What does "stationarity" roughly mean for a time series?',
                'opts' => [
                    ['The data never changes', false],
                    ['The average and variability of the data stay roughly constant over time', true],
                    ['The data only goes up', false],
                    ['The data has no seasonal pattern', false],
                ],
            ],
            [
                'q' => 'A time series with a strong upward trend is considered:',
                'opts' => [
                    ['Stationary', false],
                    ['Non-stationary', true],
                    ['Seasonal', false],
                    ['Perfect for SES', false],
                ],
            ],
            [
                'q' => '"Differencing" a time series means:',
                'opts' => [
                    ['Dividing each value by the previous one', false],
                    ['Subtracting each value from the one before it', true],
                    ['Adding a constant to each value', false],
                    ['Squaring each value', false],
                ],
            ],
            [
                'q' => 'The series 3, 5, 8, 12 after first differencing becomes:',
                'opts' => [
                    ['2, 3, 4', true],
                    ['3, 5, 8', false],
                    ['1, 2, 3', false],
                    ['5, 8, 12', false],
                ],
            ],
            [
                'q' => 'ARIMA is a forecasting model. What does the "I" stand for?',
                'opts' => [
                    ['Index', false],
                    ['Integrated (differencing to remove trend)', true],
                    ['Iterative', false],
                    ['Interval', false],
                ],
            ],
            [
                'q' => 'Which method is designed to handle time series with BOTH trend AND seasonality?',
                'opts' => [
                    ['Simple Moving Average', false],
                    ['Simple Exponential Smoothing (SES)', false],
                    ['Holt-Winters (Triple Exponential Smoothing)', true],
                    ['Naive Forecast', false],
                ],
            ],
            [
                'q' => 'Prophet is a forecasting tool created by:',
                'opts' => [
                    ['Google', false],
                    ['Microsoft', false],
                    ['Meta (Facebook)', true],
                    ['Amazon', false],
                ],
            ],
            [
                'q' => 'The ACF stands for:',
                'opts' => [
                    ['Average Computed Forecast', false],
                    ['AutoCorrelation Function', true],
                    ['Adjusted Confidence Factor', false],
                    ['Annual Change Figure', false],
                ],
            ],
            [
                'q' => 'The ACF is used to measure:',
                'opts' => [
                    ['How a series is correlated with a completely different series', false],
                    ['How a series is correlated with its own past values (lags)', true],
                    ['The trend of the series', false],
                    ['Seasonal indices', false],
                ],
            ],
            [
                'q' => 'In time series forecasting, "lag" refers to:',
                'opts' => [
                    ['A delay in computing the forecast', false],
                    ['A past value of the series at a specific number of time steps behind the current value', true],
                    ['A type of error metric', false],
                    ['The difference between the maximum and minimum values', false],
                ],
            ],
            [
                'q' => 'You want to forecast monthly electricity demand that has a clear upward trend every year. Which method is LEAST appropriate?',
                'opts' => [
                    ['Holt\'s Double Exponential Smoothing', false],
                    ['ARIMA with differencing', false],
                    ['Naive Forecast (use last value only)', true],
                    ['Holt-Winters', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 12 — Introductory Forecasting (Newbie).");
    }
}