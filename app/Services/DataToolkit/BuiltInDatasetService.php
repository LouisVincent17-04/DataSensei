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
                'suggested_x_column' => 'sleep_hours',
                'suggested_y_column' => 'wellness_score',
                'suggested_numeric_column' => 'wellness_score',
                'suggested_category_column' => 'activity_level',
                'columns' => ['person_id', 'age', 'activity_level', 'steps', 'calories_burned', 'sleep_hours', 'water_intake_liters', 'wellness_score'],
                'rows' => $this->fitnessRows(),
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
