<?php

namespace App\Services\DataToolkit;

class BuiltInDatasetService
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function all(): array
    {
        return [
            'student-performance' => [
                'key' => 'student-performance',
                'title' => 'Student Performance Dataset',
                'category' => 'Education',
                'difficulty' => 'Beginner',
                'description' => 'Practice analyzing how study habits, attendance, quiz results, and project scores relate to final grades.',
                'learning_objective' => 'Use descriptive statistics, correlation, and regression to understand academic performance patterns across many student records.',
                'analysis_objective' => 'Understand which study habits and assessment results are connected with student performance.',
                'research_questions' => [
                    'What is the typical final grade?',
                    'How are study hours and final grades related?',
                    'Do final grades differ across academic programs?',
                    'Are there unusual student records that need review?',
                ],
                'feature_suggestions' => [[
                    'key' => 'assessment-average',
                    'name' => 'assessment_average',
                    'formula' => 'assessment_average = mean(quiz_score, project_score)',
                    'explanation' => 'Combines quiz and project performance into one simple assessment measure.',
                    'operation' => 'mean',
                    'columns' => ['quiz_score', 'project_score'],
                ]],
                'validation_rules' => ['numeric_ranges' => [
                    'study_hours' => ['min' => 0, 'max' => 24],
                    'attendance_rate' => ['min' => 0, 'max' => 100],
                    'quiz_score' => ['min' => 0, 'max' => 100],
                    'project_score' => ['min' => 0, 'max' => 100],
                    'final_grade' => ['min' => 0, 'max' => 100],
                ]],
                'suggested_x_column' => 'study_hours',
                'suggested_y_column' => 'final_grade',
                'suggested_numeric_column' => 'final_grade',
                'suggested_category_column' => 'program',
                'columns' => ['student_id', 'program', 'year_level', 'study_hours', 'attendance_rate', 'quiz_score', 'project_score', 'final_grade'],
                'rows' => $this->studentPerformanceRows(),
            ],
            'monthly-sales' => [
                'key' => 'monthly-sales',
                'title' => 'Monthly Sales Dataset',
                'category' => 'Business',
                'difficulty' => 'Beginner',
                'description' => 'Analyze product sales, revenue, pricing, seasonality, and customer ratings across multiple months and product categories.',
                'learning_objective' => 'Connect units sold, unit price, revenue, and customer rating using charts, correlation, and regression.',
                'analysis_objective' => 'Understand the sales patterns and factors connected with monthly revenue.',
                'research_questions' => [
                    'Which product categories generate the highest revenue?',
                    'How are units sold and revenue related?',
                    'How much do sales vary across the dataset?',
                    'Are any monthly sales records unusually high or low?',
                ],
                'feature_suggestions' => [[
                    'key' => 'calculated-revenue',
                    'name' => 'calculated_revenue',
                    'formula' => 'calculated_revenue = units_sold × unit_price',
                    'explanation' => 'Recalculates revenue from units and price so it can be compared with the recorded revenue.',
                    'operation' => 'product',
                    'columns' => ['units_sold', 'unit_price'],
                ]],
                'validation_rules' => ['numeric_ranges' => [
                    'units_sold' => ['min' => 0, 'max' => 10000],
                    'unit_price' => ['min' => 0],
                    'revenue' => ['min' => 0],
                    'customer_rating' => ['min' => 1, 'max' => 5],
                ]],
                'suggested_x_column' => 'units_sold',
                'suggested_y_column' => 'revenue',
                'suggested_numeric_column' => 'revenue',
                'suggested_category_column' => 'product_category',
                'columns' => ['month', 'product_category', 'units_sold', 'unit_price', 'revenue', 'customer_rating'],
                'rows' => $this->monthlySalesRows(),
            ],
            'weather-observations' => [
                'key' => 'weather-observations',
                'title' => 'Weather Observations Dataset',
                'category' => 'Environment',
                'difficulty' => 'Intermediate',
                'description' => 'Explore daily weather measurements such as temperature, humidity, rainfall, wind speed, and observed weather condition.',
                'learning_objective' => 'Use statistics and visualizations to understand environmental changes, seasonal movement, and relationships between weather variables.',
                'analysis_objective' => 'Explore daily weather conditions and identify measurements associated with rainfall.',
                'research_questions' => [
                    'What are the typical temperature and humidity levels?',
                    'How are humidity and rainfall related?',
                    'How do measurements differ across weather conditions?',
                    'Are there days with unusual weather measurements?',
                ],
                'feature_suggestions' => [[
                    'key' => 'rain-humidity-index',
                    'name' => 'rain_humidity_index',
                    'formula' => 'rain_humidity_index = rainfall × humidity',
                    'explanation' => 'Combines rainfall and humidity into a simple comparison index for wet conditions.',
                    'operation' => 'product',
                    'columns' => ['rainfall', 'humidity'],
                ]],
                'validation_rules' => ['numeric_ranges' => [
                    'day' => ['min' => 1, 'max' => 366],
                    'temperature' => ['min' => -50, 'max' => 60],
                    'humidity' => ['min' => 0, 'max' => 100],
                    'rainfall' => ['min' => 0],
                    'wind_speed' => ['min' => 0, 'max' => 250],
                ]],
                'suggested_x_column' => 'humidity',
                'suggested_y_column' => 'rainfall',
                'suggested_numeric_column' => 'temperature',
                'suggested_category_column' => 'condition',
                'columns' => ['day', 'temperature', 'humidity', 'rainfall', 'wind_speed', 'condition'],
                'rows' => $this->weatherRows(),
            ],
            'learning-survey' => [
                'key' => 'learning-survey',
                'title' => 'Learning Survey Dataset',
                'category' => 'Survey',
                'difficulty' => 'Beginner',
                'description' => 'Practice summarizing categorical responses and satisfaction scores from learners who use different learning platforms.',
                'learning_objective' => 'Use frequency distributions and descriptive statistics to understand learning preferences, difficulty levels, and study behavior.',
                'analysis_objective' => 'Understand learner preferences, study behavior, and satisfaction with learning platforms.',
                'research_questions' => [
                    'Which learning platform is used most often?',
                    'What is the typical satisfaction score?',
                    'Does study time differ across learning platforms?',
                    'Are there unusual or incomplete survey responses?',
                ],
                'feature_suggestions' => [[
                    'key' => 'study-hours',
                    'name' => 'study_time_hours',
                    'formula' => 'study_time_hours = study_time_minutes ÷ 60',
                    'explanation' => 'Converts study time from minutes to hours, which may be easier to interpret.',
                    'operation' => 'scale',
                    'columns' => ['study_time_minutes'],
                    'factor' => 0.0166666667,
                ]],
                'validation_rules' => ['numeric_ranges' => [
                    'satisfaction_score' => ['min' => 1, 'max' => 5],
                    'difficulty_rating' => ['min' => 1, 'max' => 5],
                    'study_time_minutes' => ['min' => 0, 'max' => 1440],
                ]],
                'suggested_x_column' => 'study_time_minutes',
                'suggested_y_column' => 'satisfaction_score',
                'suggested_numeric_column' => 'satisfaction_score',
                'suggested_category_column' => 'learning_platform_used',
                'columns' => ['respondent_id', 'program', 'year_level', 'learning_platform_used', 'satisfaction_score', 'difficulty_rating', 'study_time_minutes'],
                'rows' => $this->learningSurveyRows(),
            ],
            'fitness-wellness' => [
                'key' => 'fitness-wellness',
                'title' => 'Fitness and Wellness Dataset',
                'category' => 'Health',
                'difficulty' => 'Intermediate',
                'description' => 'Analyze activity level, steps, sleep, calories, hydration, and wellness scores from student wellness records.',
                'learning_objective' => 'Use correlation and regression to explore possible relationships between wellness habits and wellness scores.',
                'analysis_objective' => 'Explore how daily activity, sleep, and hydration are connected with wellness scores.',
                'research_questions' => [
                    'What is the typical wellness score?',
                    'How are sleep hours and wellness scores related?',
                    'How do wellness scores differ by activity level?',
                    'Are there unusual wellness records?',
                ],
                'feature_suggestions' => [[
                    'key' => 'steps-thousands',
                    'name' => 'steps_in_thousands',
                    'formula' => 'steps_in_thousands = steps ÷ 1,000',
                    'explanation' => 'Rescales step counts into thousands so differences are easier to read.',
                    'operation' => 'scale',
                    'columns' => ['steps'],
                    'factor' => 0.001,
                ]],
                'validation_rules' => ['numeric_ranges' => [
                    'age' => ['min' => 12, 'max' => 100],
                    'steps' => ['min' => 0, 'max' => 100000],
                    'calories_burned' => ['min' => 0, 'max' => 10000],
                    'sleep_hours' => ['min' => 0, 'max' => 24],
                    'water_intake_liters' => ['min' => 0, 'max' => 20],
                    'wellness_score' => ['min' => 0, 'max' => 100],
                ]],
                'suggested_x_column' => 'sleep_hours',
                'suggested_y_column' => 'wellness_score',
                'suggested_numeric_column' => 'wellness_score',
                'suggested_category_column' => 'activity_level',
                'columns' => ['person_id', 'age', 'activity_level', 'steps', 'calories_burned', 'sleep_hours', 'water_intake_liters', 'wellness_score'],
                'rows' => $this->fitnessRows(),
            ],
            'customer-eda-quality' => [
                'key' => 'customer-eda-quality',
                'title' => 'Customer Behavior EDA Dataset',
                'category' => 'Data Quality',
                'difficulty' => 'Intermediate',
                'description' => 'Explore a customer behavior dataset containing intentional missing values, duplicate records, and extreme observations for realistic EDA practice.',
                'learning_objective' => 'Profile data types, inspect missingness, detect duplicates and IQR outliers, and interpret distributions, box plots, scatter plots, and correlation heatmaps.',
                'analysis_objective' => 'Understand customer behavior while practicing how to handle missing values, duplicate records, and unusual observations.',
                'research_questions' => [
                    'What is the typical monthly customer spending?',
                    'How are visits per month and monthly spending related?',
                    'Which customer segments spend the most?',
                    'Which records require cleaning or outlier review?',
                ],
                'feature_suggestions' => [[
                    'key' => 'estimated-annual-spend',
                    'name' => 'estimated_annual_spend',
                    'formula' => 'estimated_annual_spend = monthly_spend × 12',
                    'explanation' => 'Converts monthly spending into a simple annual estimate for comparison.',
                    'operation' => 'scale',
                    'columns' => ['monthly_spend'],
                    'factor' => 12,
                ]],
                'validation_rules' => ['numeric_ranges' => [
                    'age' => ['min' => 18, 'max' => 100],
                    'visits_per_month' => ['min' => 0, 'max' => 60],
                    'monthly_spend' => ['min' => 0],
                    'satisfaction_score' => ['min' => 1, 'max' => 5],
                ]],
                'suggested_x_column' => 'visits_per_month',
                'suggested_y_column' => 'monthly_spend',
                'suggested_numeric_column' => 'monthly_spend',
                'suggested_category_column' => 'region',
                'columns' => ['customer_id', 'region', 'customer_segment', 'age', 'visits_per_month', 'monthly_spend', 'satisfaction_score', 'churned'],
                'rows' => $this->customerEdaRows(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $key): ?array
    {
        $datasets = $this->all();

        return $datasets[$key] ?? null;
    }

    public function exists(string $key): bool
    {
        return $this->find($key) !== null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function preview(string $key, int $limit = 10): array
    {
        $dataset = $this->find($key);

        if (! $dataset) {
            return [];
        }

        return array_slice($dataset['rows'], 0, max(1, $limit));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function studentPerformanceRows(): array
    {
        $programs = ['BSIT', 'BSCS', 'BSDS', 'BSIS'];
        $years = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
        $rows = [];

        for ($i = 1; $i <= 120; $i++) {
            $programIndex = ($i - 1) % count($programs);
            $yearIndex = (int) floor(($i - 1) / count($programs)) % count($years);
            $studyHours = $this->roundValue(1.4 + (($i * 17) % 76) / 10 + ($yearIndex * 0.18), 1);
            $attendance = (int) $this->clamp(63 + (($i * 11) % 35) + ($studyHours * 1.7) - ($programIndex * 1.2), 55, 99);
            $quiz = (int) $this->clamp(50 + ($studyHours * 4.3) + ($attendance * 0.17) + (($i * 7) % 13) - 5, 45, 100);
            $project = (int) $this->clamp(52 + ($studyHours * 3.8) + ($attendance * 0.16) + (($i * 5) % 15) - 4, 48, 100);
            $final = (int) $this->clamp(round(($quiz * 0.35) + ($project * 0.40) + ($attendance * 0.15) + ($studyHours * 1.2)), 50, 99);

            $rows[] = [
                'student_id' => 'S' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'program' => $programs[$programIndex],
                'year_level' => $years[$yearIndex],
                'study_hours' => $studyHours,
                'attendance_rate' => $attendance,
                'quiz_score' => $quiz,
                'project_score' => $project,
                'final_grade' => $final,
            ];
        }

        // Controlled imperfections make the cleaning step a real learner decision.
        $rows[4]['quiz_score'] = null;
        $rows[30]['project_score'] = '';
        $rows[56]['final_grade'] = null;
        $rows[9]['program'] = strtolower((string) $rows[9]['program']);
        $rows[35]['program'] = '  ' . $rows[35]['program'] . '  ';
        $rows[71]['year_level'] = strtolower((string) $rows[71]['year_level']);
        $rows[25]['study_hours'] = -4;
        $rows[69]['attendance_rate'] = 140;
        $rows[88]['quiz_score'] = 'absent';
        $rows[102]['final_grade'] = 10;
        $rows[] = $rows[6];
        $rows[] = $rows[47];

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function monthlySalesRows(): array
    {
        $categories = ['Books', 'Software', 'Hardware', 'Online Course', 'Accessories'];
        $basePrices = [240, 950, 1850, 620, 310];
        $rows = [];

        for ($i = 1; $i <= 120; $i++) {
            $categoryIndex = ($i - 1) % count($categories);
            $monthNumber = (($i - 1) % 12) + 1;
            $year = 2024 + (int) floor(($i - 1) / 60);
            $trend = (int) floor(($i - 1) / 5);
            $seasonalBoost = in_array($monthNumber, [3, 6, 9, 12], true) ? 22 : (in_array($monthNumber, [1, 7], true) ? -10 : 5);
            $units = (int) $this->clamp(38 + ($categoryIndex * 18) + (($i * 13) % 52) + $trend + $seasonalBoost, 20, 230);
            $unitPrice = $basePrices[$categoryIndex] + (($monthNumber % 4) * 15) + ((int) floor($trend / 4) * 8);
            $revenue = $units * $unitPrice;
            $rating = $this->roundValue($this->clamp(3.55 + ($units / 180) + (($i * 3) % 9) / 20 - ($categoryIndex * 0.03), 3.1, 5.0), 1);

            $rows[] = [
                'month' => sprintf('%d-%02d', $year, $monthNumber),
                'product_category' => $categories[$categoryIndex],
                'units_sold' => $units,
                'unit_price' => $unitPrice,
                'revenue' => $revenue,
                'customer_rating' => $rating,
            ];
        }

        $rows[7]['product_category'] = null;
        $rows[39]['unit_price'] = '';
        $rows[11]['product_category'] = strtolower((string) $rows[11]['product_category']);
        $rows[63]['product_category'] = ' ' . $rows[63]['product_category'] . ' ';
        $rows[23]['units_sold'] = -18;
        $rows[67]['customer_rating'] = 8.7;
        $rows[91]['revenue'] = 'unknown';
        $rows[107]['units_sold'] = 980;
        $rows[107]['revenue'] = 980 * (float) $rows[107]['unit_price'];
        $rows[] = $rows[18];

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function weatherRows(): array
    {
        $rows = [];

        for ($i = 1; $i <= 120; $i++) {
            $seasonWave = sin($i / 11);
            $rainWave = cos($i / 8);
            $temperature = $this->roundValue(30.2 + ($seasonWave * 2.1) + ((($i * 5) % 9) / 10), 1);
            $humidity = (int) $this->clamp(70 + ($rainWave * 10) + (($i * 7) % 11) - 4, 52, 94);
            $rainfall = $this->roundValue($this->clamp(($humidity - 62) * 0.42 + (($i * 3) % 12) - 6, 0, 34), 1);
            $wind = (int) $this->clamp(7 + (($i * 5) % 14) + ($rainfall > 15 ? 3 : 0), 5, 28);
            $condition = $rainfall >= 12 ? 'Rainy' : ($rainfall >= 3 ? 'Cloudy' : 'Sunny');

            $rows[] = [
                'day' => $i,
                'temperature' => $temperature,
                'humidity' => $humidity,
                'rainfall' => $rainfall,
                'wind_speed' => $wind,
                'condition' => $condition,
            ];
        }

        $rows[13]['condition'] = null;
        $rows[42]['temperature'] = '';
        $rows[17]['condition'] = strtolower((string) $rows[17]['condition']);
        $rows[74]['condition'] = ' ' . $rows[74]['condition'] . ' ';
        $rows[28]['humidity'] = 145;
        $rows[65]['rainfall'] = -12;
        $rows[96]['wind_speed'] = 'calm';
        $rows[111]['temperature'] = 52;

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function learningSurveyRows(): array
    {
        $programs = ['BSIT', 'BSCS', 'BSIS', 'BSDS'];
        $years = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
        $platforms = ['Video Lessons', 'Coding Platform', 'Modules', 'Live Class', 'DataSensei Lab'];
        $rows = [];

        for ($i = 1; $i <= 120; $i++) {
            $programIndex = ($i - 1) % count($programs);
            $platformIndex = ($i * 2 + $programIndex) % count($platforms);
            $yearIndex = ($i * 3) % count($years);
            $studyTime = (int) $this->clamp(28 + (($i * 9) % 82) + ($platformIndex === 1 ? 18 : 0) + ($platformIndex === 4 ? 22 : 0), 20, 150);
            $difficulty = $this->roundValue($this->clamp(4.4 - ($studyTime / 80) + (($i * 5) % 8) / 10, 1.2, 5.0), 1);
            $satisfaction = $this->roundValue($this->clamp(2.8 + ($studyTime / 75) - ($difficulty * 0.18) + ($platformIndex === 4 ? 0.45 : 0) + (($i * 7) % 5) / 10, 1.5, 5.0), 1);

            $rows[] = [
                'respondent_id' => 'R' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'program' => $programs[$programIndex],
                'year_level' => $years[$yearIndex],
                'learning_platform_used' => $platforms[$platformIndex],
                'satisfaction_score' => $satisfaction,
                'difficulty_rating' => $difficulty,
                'study_time_minutes' => $studyTime,
            ];
        }

        $rows[6]['learning_platform_used'] = null;
        $rows[38]['satisfaction_score'] = '';
        $rows[14]['program'] = strtolower((string) $rows[14]['program']);
        $rows[58]['learning_platform_used'] = ' ' . $rows[58]['learning_platform_used'] . ' ';
        $rows[21]['satisfaction_score'] = 9.5;
        $rows[69]['study_time_minutes'] = -30;
        $rows[94]['difficulty_rating'] = 'hard';
        $rows[109]['study_time_minutes'] = 720;
        $rows[] = $rows[32];

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fitnessRows(): array
    {
        $levels = ['Low', 'Moderate', 'High'];
        $rows = [];

        for ($i = 1; $i <= 120; $i++) {
            $levelIndex = ($i * 5) % count($levels);
            $age = 18 + (($i * 2) % 12);
            $activityBoost = $levelIndex * 3100;
            $steps = (int) $this->clamp(3200 + $activityBoost + (($i * 137) % 2500), 2500, 16000);
            $sleep = $this->roundValue($this->clamp(5.1 + ($levelIndex * 0.55) + (($i * 11) % 25) / 10, 4.5, 9.2), 1);
            $water = $this->roundValue($this->clamp(1.1 + ($levelIndex * 0.48) + (($i * 7) % 18) / 10, 1.0, 4.2), 1);
            $calories = (int) $this->clamp(1550 + ($steps * 0.085) + ($age * 8) + ($levelIndex * 120), 1500, 3300);
            $wellness = (int) $this->clamp(round(42 + ($steps / 380) + ($sleep * 4.8) + ($water * 5.2) - (($age - 18) * 0.35)), 45, 98);

            $rows[] = [
                'person_id' => 'P' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'age' => $age,
                'activity_level' => $levels[$levelIndex],
                'steps' => $steps,
                'calories_burned' => $calories,
                'sleep_hours' => $sleep,
                'water_intake_liters' => $water,
                'wellness_score' => $wellness,
            ];
        }

        $rows[8]['activity_level'] = null;
        $rows[44]['sleep_hours'] = '';
        $rows[16]['activity_level'] = strtolower((string) $rows[16]['activity_level']);
        $rows[61]['activity_level'] = ' ' . $rows[61]['activity_level'] . ' ';
        $rows[26]['age'] = -3;
        $rows[73]['water_intake_liters'] = 'two liters';
        $rows[95]['sleep_hours'] = 30;
        $rows[113]['steps'] = 80000;

        return $rows;
    }


    /**
     * Dataset intentionally includes missing values, duplicate records, and
     * extreme values so learners can practice a complete EDA workflow.
     *
     * @return array<int, array<string, mixed>>
     */
    private function customerEdaRows(): array
    {
        $regions = ['North', 'South', 'East', 'West'];
        $segments = ['Budget', 'Standard', 'Premium'];
        $rows = [];

        for ($i = 1; $i <= 90; $i++) {
            $segmentIndex = ($i * 5) % count($segments);
            $regionIndex = ($i * 3) % count($regions);
            $age = 18 + (($i * 7) % 48);
            $visits = 1 + (($i * 11) % 18) + ($segmentIndex * 2);
            $spend = 450 + ($segmentIndex * 900) + ($visits * 115) + (($i * 37) % 480);
            $satisfaction = $this->roundValue($this->clamp(2.2 + ($segmentIndex * 0.65) + ($visits / 30) + (($i * 3) % 8) / 10, 1.0, 5.0), 1);

            if ($i === 23) {
                $spend = 18000;
            }
            if ($i === 61) {
                $visits = 95;
            }
            if ($i === 72) {
                $age = 130;
            }

            $rows[] = [
                'customer_id' => 'C' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'region' => $i % 19 === 0 ? '' : $regions[$regionIndex],
                'customer_segment' => $segments[$segmentIndex],
                'age' => $i % 17 === 0 ? null : $age,
                'visits_per_month' => $visits,
                'monthly_spend' => $i % 13 === 0 ? null : $spend,
                'satisfaction_score' => $i % 29 === 0 ? null : $satisfaction,
                'churned' => ($satisfaction < 3.0 || $visits < 4) ? 'Yes' : 'No',
            ];
        }

        // Exact duplicates are deliberate teaching examples.
        $rows[10]['region'] = strtolower((string) $rows[10]['region']);
        $rows[35]['customer_segment'] = ' ' . $rows[35]['customer_segment'] . ' ';
        $rows[47]['monthly_spend'] = -650;
        $rows[80]['satisfaction_score'] = 'unknown';
        $rows[] = $rows[8];
        $rows[] = $rows[22];
        $rows[] = $rows[22];

        return $rows;
    }

    private function clamp(float|int $value, float|int $min, float|int $max): float|int
    {
        return max($min, min($max, $value));
    }

    private function roundValue(float $value, int $precision = 2): float
    {
        return round($value, $precision);
    }
}
