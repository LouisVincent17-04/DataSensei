<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module2ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        $this->command->info("Creating Module 2 — Basics of Statistics (University Student)...CatID".$category->id);

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Basics of Statistics',
            'description'           => 'Apply your understanding of statistics through analytical and calculation-based questions. Covers measures of central tendency, variability, probability, and introductory distributions.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 700,
            'order_index'           => 2,
        ]);

        $this->command->info("Seeding 50 university-level statistics questions...");

        $qaData = [

            // ── MEAN / WEIGHTED MEAN ──────────────────────────────────────
            [
                'q' => 'A student scores 60, 75, and 90 on three exams worth equal weight. What is the mean score?',
                'opts' => [
                    ['70', false],
                    ['75', true],
                    ['80', false],
                    ['65', false],
                ],
            ],
            [
                'q' => 'Given the dataset [12, 15, 18, 21, 24], what is the mean?',
                'opts' => [
                    ['16', false],
                    ['17', false],
                    ['18', true],
                    ['19', false],
                ],
            ],
            [
                'q' => "A weighted mean calculation:\nSubject A: score = 80, weight = 3\nSubject B: score = 60, weight = 1\nWhat is the weighted mean?",
                'opts' => [
                    ['70', false],
                    ['72', false],
                    ['75', true],
                    ['76', false],
                ],
            ],
            [
                'q' => 'The mean of [x, 8, 12, 16] is 11. What is the value of x?',
                'opts' => [
                    ['6', false],
                    ['7', false],
                    ['8', true],
                    ['9', false],
                ],
            ],
            [
                'q' => 'Adding an outlier of 200 to the dataset [10, 11, 12, 13, 14] will have the greatest impact on which measure?',
                'opts' => [
                    ['Median', false],
                    ['Mode', false],
                    ['Mean', true],
                    ['Range is not impacted', false],
                ],
            ],

            // ── MEDIAN ───────────────────────────────────────────────────
            [
                'q' => 'What is the median of [7, 3, 9, 1, 5]?',
                'opts' => [
                    ['3', false],
                    ['5', true],
                    ['7', false],
                    ['9', false],
                ],
            ],
            [
                'q' => 'What is the median of [4, 8, 15, 16, 23, 42]?',
                'opts' => [
                    ['15', false],
                    ['15.5', true],
                    ['16', false],
                    ['17', false],
                ],
            ],
            [
                'q' => 'In a right-skewed distribution, which relationship is generally true?',
                'opts' => [
                    ['Mean < Median < Mode', false],
                    ['Mean = Median = Mode', false],
                    ['Mode < Median < Mean', true],
                    ['Median > Mean > Mode', false],
                ],
            ],

            // ── MODE & DISTRIBUTION SHAPE ─────────────────────────────────
            [
                'q' => 'Dataset: [2, 3, 3, 4, 5, 5, 5, 6, 7]. What is the mode?',
                'opts' => [
                    ['3', false],
                    ['4', false],
                    ['5', true],
                    ['6', false],
                ],
            ],
            [
                'q' => 'A distribution has two peaks at different values. What is it called?',
                'opts' => [
                    ['Unimodal', false],
                    ['Symmetric', false],
                    ['Bimodal', true],
                    ['Uniform', false],
                ],
            ],

            // ── RANGE, VARIANCE, STANDARD DEVIATION ──────────────────────
            [
                'q' => 'What is the range of [4, 7, 13, 2, 1, 9]?',
                'opts' => [
                    ['10', false],
                    ['11', false],
                    ['12', true],
                    ['13', false],
                ],
            ],
            [
                'q' => "What is the variance of [2, 4, 4, 4, 5, 5, 7, 9]?\n(Hint: Mean = 5. Variance = average of squared deviations from the mean.)",
                'opts' => [
                    ['2', false],
                    ['3', false],
                    ['4', true],
                    ['5', false],
                ],
            ],
            [
                'q' => 'If the variance of a dataset is 25, what is the standard deviation?',
                'opts' => [
                    ['25', false],
                    ['625', false],
                    ['5', true],
                    ['12.5', false],
                ],
            ],
            [
                'q' => 'Dataset A has SD = 2. Dataset B has SD = 10. Which dataset has values that are MORE spread out?',
                'opts' => [
                    ['Dataset A', false],
                    ['Dataset B', true],
                    ['They are equally spread', false],
                    ['Cannot be determined from SD alone', false],
                ],
            ],
            [
                'q' => 'Which formula correctly describes population variance?\n(μ = mean, N = total count, xᵢ = each value)',
                'opts' => [
                    ['σ² = Σ(xᵢ - μ) / N', false],
                    ['σ² = Σ(xᵢ - μ)² / N', true],
                    ['σ² = Σ(xᵢ)² / N', false],
                    ['σ² = Σ(xᵢ - μ)² / (N - 1)', false],
                ],
            ],

            // ── IQR & QUARTILES ───────────────────────────────────────────
            [
                'q' => 'What does IQR stand for?',
                'opts' => [
                    ['Integer Quartile Result', false],
                    ['Interquartile Range', true],
                    ['Internal Quality Rating', false],
                    ['Integrated Quartile Ratio', false],
                ],
            ],
            [
                'q' => 'How is the IQR calculated?',
                'opts' => [
                    ['Maximum - Minimum', false],
                    ['Q3 - Q1', true],
                    ['Q2 - Q1', false],
                    ['Q3 - Q2', false],
                ],
            ],
            [
                'q' => 'In a sorted dataset of 9 values, Q1 is the __ value.',
                'opts' => [
                    ['1st', false],
                    ['2nd', false],
                    ['3rd', true],
                    ['4th', false],
                ],
            ],
            [
                'q' => 'What is the IQR of [1, 3, 5, 7, 9, 11, 13]?\n(Q1 = 3, Q3 = 11)',
                'opts' => [
                    ['6', false],
                    ['7', false],
                    ['8', true],
                    ['10', false],
                ],
            ],
            [
                'q' => 'A value is considered an outlier using the IQR method if it is below Q1 - (1.5 × IQR) or above Q3 + (1.5 × IQR).\nIf Q1 = 10, Q3 = 20, IQR = 10, which value is an outlier?',
                'opts' => [
                    ['5', false],
                    ['25', false],
                    ['35', false],
                    ['-5', true],
                ],
            ],

            // ── PROBABILITY ───────────────────────────────────────────────
            [
                'q' => 'What is the probability of drawing a red card from a standard 52-card deck?',
                'opts' => [
                    ['1/4', false],
                    ['1/2', true],
                    ['1/13', false],
                    ['1/52', false],
                ],
            ],
            [
                'q' => 'Two events A and B are mutually exclusive. P(A) = 0.3, P(B) = 0.4. What is P(A or B)?',
                'opts' => [
                    ['0.12', false],
                    ['0.1', false],
                    ['0.7', true],
                    ['1.0', false],
                ],
            ],
            [
                'q' => 'What is the complement of an event A?\n(P(A) = 0.65)',
                'opts' => [
                    ['0.65', false],
                    ['1.65', false],
                    ['0.35', true],
                    ['0', false],
                ],
            ],
            [
                'q' => 'Two independent events: P(A) = 0.4, P(B) = 0.5. What is P(A and B)?',
                'opts' => [
                    ['0.9', false],
                    ['0.45', false],
                    ['0.2', true],
                    ['0.04', false],
                ],
            ],
            [
                'q' => 'A bag has 3 red and 7 blue balls. You pick one at random. What is P(blue)?',
                'opts' => [
                    ['0.3', false],
                    ['0.7', true],
                    ['0.5', false],
                    ['7/3', false],
                ],
            ],

            // ── CONDITIONAL PROBABILITY ───────────────────────────────────
            [
                'q' => 'What does conditional probability P(A|B) mean?',
                'opts' => [
                    ['The probability of A and B happening at the same time', false],
                    ['The probability of A happening given that B has already happened', true],
                    ['The probability of A or B happening', false],
                    ['The probability of neither A nor B', false],
                ],
            ],
            [
                'q' => 'Given P(A and B) = 0.12 and P(B) = 0.4, what is P(A|B)?',
                'opts' => [
                    ['0.048', false],
                    ['0.3', true],
                    ['0.52', false],
                    ['3.33', false],
                ],
            ],

            // ── NORMAL DISTRIBUTION ───────────────────────────────────────
            [
                'q' => 'In a normal distribution, approximately what percentage of data falls within 1 standard deviation of the mean?',
                'opts' => [
                    ['50%', false],
                    ['68%', true],
                    ['95%', false],
                    ['99.7%', false],
                ],
            ],
            [
                'q' => 'In a normal distribution, approximately what percentage of data falls within 2 standard deviations of the mean?',
                'opts' => [
                    ['68%', false],
                    ['90%', false],
                    ['95%', true],
                    ['99.7%', false],
                ],
            ],
            [
                'q' => 'What is the shape of a perfect normal distribution when graphed?',
                'opts' => [
                    ['U-shaped curve', false],
                    ['Flat line', false],
                    ['Bell-shaped curve', true],
                    ['J-shaped curve', false],
                ],
            ],
            [
                'q' => 'In a normal distribution with mean = 50 and SD = 5, what range covers approximately 95% of the data?',
                'opts' => [
                    ['45 to 55', false],
                    ['40 to 60', true],
                    ['35 to 65', false],
                    ['30 to 70', false],
                ],
            ],

            // ── Z-SCORE ───────────────────────────────────────────────────
            [
                'q' => 'What does a z-score tell you?',
                'opts' => [
                    ['The raw value of a data point', false],
                    ['How many standard deviations a data point is from the mean', true],
                    ['The probability of an event', false],
                    ['The median of the dataset', false],
                ],
            ],
            [
                'q' => 'The formula for a z-score is z = (x - μ) / σ.\nIf x = 80, μ = 70, σ = 5, what is z?',
                'opts' => [
                    ['1', false],
                    ['1.5', false],
                    ['2', true],
                    ['2.5', false],
                ],
            ],
            [
                'q' => 'A z-score of 0 means the data point is:',
                'opts' => [
                    ['At the minimum value', false],
                    ['One SD above the mean', false],
                    ['Equal to the mean', true],
                    ['An extreme outlier', false],
                ],
            ],
            [
                'q' => 'A negative z-score indicates the data point is:',
                'opts' => [
                    ['Above the mean', false],
                    ['Below the mean', true],
                    ['Equal to the mean', false],
                    ['An error', false],
                ],
            ],

            // ── SAMPLING ─────────────────────────────────────────────────
            [
                'q' => 'In simple random sampling, every member of the population has:',
                'opts' => [
                    ['No chance of being selected', false],
                    ['An equal chance of being selected', true],
                    ['A chance based on their rank', false],
                    ['A guaranteed spot in the sample', false],
                ],
            ],
            [
                'q' => 'A researcher divides the population into age groups and randomly samples from each group. This is called:',
                'opts' => [
                    ['Simple random sampling', false],
                    ['Cluster sampling', false],
                    ['Stratified sampling', true],
                    ['Convenience sampling', false],
                ],
            ],
            [
                'q' => 'What is "sampling bias"?',
                'opts' => [
                    ['When the sample is too large', false],
                    ['When the sample does not accurately represent the population', true],
                    ['When data is collected too slowly', false],
                    ['When the mean is greater than the median', false],
                ],
            ],

            // ── CORRELATION ───────────────────────────────────────────────
            [
                'q' => 'What does correlation measure?',
                'opts' => [
                    ['How one variable causes another to change', false],
                    ['The strength and direction of the linear relationship between two variables', true],
                    ['The average of two variables', false],
                    ['The difference between two variables', false],
                ],
            ],
            [
                'q' => 'A correlation coefficient (r) of -0.9 indicates:',
                'opts' => [
                    ['A weak positive relationship', false],
                    ['No relationship', false],
                    ['A strong negative relationship', true],
                    ['A perfect positive relationship', false],
                ],
            ],
            [
                'q' => 'What is the range of the Pearson correlation coefficient?',
                'opts' => [
                    ['0 to 1', false],
                    ['-1 to 0', false],
                    ['-1 to 1', true],
                    ['0 to 100', false],
                ],
            ],
            [
                'q' => '"Correlation does not imply causation." What does this mean?',
                'opts' => [
                    ['Correlated variables always have a causal link', false],
                    ['Two variables can be correlated without one causing the other', true],
                    ['Causation always implies correlation', false],
                    ['Only negative correlations are meaningful', false],
                ],
            ],

            // ── HYPOTHESIS TESTING INTRO ──────────────────────────────────
            [
                'q' => 'What is a null hypothesis (H₀)?',
                'opts' => [
                    ['The hypothesis you are trying to prove', false],
                    ['A statement that assumes no effect or no difference exists', true],
                    ['The conclusion of a study', false],
                    ['A hypothesis based on personal opinion', false],
                ],
            ],
            [
                'q' => 'What is the alternative hypothesis (H₁ or Hₐ)?',
                'opts' => [
                    ['A statement assuming no effect', false],
                    ['The hypothesis that challenges the null — it asserts a difference or effect exists', true],
                    ['The final conclusion of the test', false],
                    ['A hypothesis that is always rejected', false],
                ],
            ],
            [
                'q' => 'In hypothesis testing, the p-value represents:',
                'opts' => [
                    ['The probability that the null hypothesis is true', false],
                    ['The probability of observing your results (or more extreme) if the null hypothesis is true', true],
                    ['The effect size of your experiment', false],
                    ['The confidence level of your test', false],
                ],
            ],
            [
                'q' => 'A p-value of 0.03 with a significance level α = 0.05 leads you to:',
                'opts' => [
                    ['Fail to reject the null hypothesis', false],
                    ['Reject the null hypothesis', true],
                    ['Accept the alternative hypothesis as proven', false],
                    ['Increase the sample size', false],
                ],
            ],

            // ── REGRESSION INTRO ──────────────────────────────────────────
            [
                'q' => 'What is the purpose of simple linear regression?',
                'opts' => [
                    ['To find the mode of a dataset', false],
                    ['To model the relationship between one independent and one dependent variable using a line', true],
                    ['To calculate the IQR', false],
                    ['To count frequencies of categories', false],
                ],
            ],
            [
                'q' => 'In the regression equation ŷ = mx + b, what does "b" represent?',
                'opts' => [
                    ['The slope of the line', false],
                    ['The dependent variable', false],
                    ['The y-intercept (the value of ŷ when x = 0)', true],
                    ['The correlation coefficient', false],
                ],
            ],
            [
                'q' => 'In regression, what does R² (R-squared) tell you?',
                'opts' => [
                    ['The slope of the regression line', false],
                    ['The proportion of variance in the dependent variable explained by the independent variable', true],
                    ['The number of data points', false],
                    ['Whether the data is normally distributed', false],
                ],
            ],
            [
                'q' => 'An R² value of 0.85 means that the model:',
                'opts' => [
                    ['Is 85% accurate on new data', false],
                    ['Has a correlation of 0.85', false],
                    ['Explains 85% of the variability in the dependent variable', true],
                    ['Has 85 data points', false],
                ],
            ],

        ];

        foreach ($qaData as $data) {
            $question = ChallengeQuestion::create([
                'challenge_id'  => $challenge->id,
                'question_text' => $data['q'],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 2 — Basics of Statistics (University Student).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: University Student");
    }
}