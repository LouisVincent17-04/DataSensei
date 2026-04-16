<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module2ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {

        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove any existing Module 2 challenge for this category to avoid duplicates
        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Basics of Statistics')
                 ->delete();

        $this->command->info("Creating Module 2 — Basics of Statistics (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Basics of Statistics',
            'description'           => 'Test your understanding of foundational statistics — types of data, measures of central tendency, spread, probability, distributions, and correlation. Essential concepts for every data journey.',
            'time_limit_seconds'    => 900, // 15 minutes for 50 questions
            'base_xp'               => 500,
            'order_index'           => 2,
        ]);

        $this->command->info("Seeding 50 statistics questions...");

        $qaData = [

            // ── WHAT IS STATISTICS / TYPES OF DATA ───────────────────────
            [
                'q' => 'Which branch of statistics summarizes and describes a dataset?',
                'opts' => [
                    ['Inferential Statistics', false],
                    ['Descriptive Statistics', true],
                    ['Predictive Statistics', false],
                    ['Prescriptive Statistics', false],
                ],
            ],
            [
                'q' => 'Which branch of statistics uses a sample to make conclusions about a larger population?',
                'opts' => [
                    ['Descriptive Statistics', false],
                    ['Exploratory Statistics', false],
                    ['Inferential Statistics', true],
                    ['Experimental Statistics', false],
                ],
            ],
            [
                'q' => 'What is the difference between a population and a sample?',
                'opts' => [
                    ['A population is always smaller than a sample', false],
                    ['A sample is the entire group; a population is a subset', false],
                    ['A population is the entire group; a sample is a subset of it', true],
                    ['They mean the same thing in statistics', false],
                ],
            ],
            [
                'q' => 'Blood type (A, B, O, AB) is an example of which level of measurement?',
                'opts' => [
                    ['Ordinal', false],
                    ['Interval', false],
                    ['Ratio', false],
                    ['Nominal', true],
                ],
            ],
            [
                'q' => 'A satisfaction survey rated "Poor, Fair, Good, Excellent" is an example of which data type?',
                'opts' => [
                    ['Nominal', false],
                    ['Ordinal', true],
                    ['Interval', false],
                    ['Ratio', false],
                ],
            ],
            [
                'q' => 'Temperature in Celsius is an example of which level of measurement?',
                'opts' => [
                    ['Nominal', false],
                    ['Ordinal', false],
                    ['Interval', true],
                    ['Ratio', false],
                ],
            ],
            [
                'q' => 'Which level of measurement has equal gaps between values AND a true zero point?',
                'opts' => [
                    ['Nominal', false],
                    ['Ordinal', false],
                    ['Interval', false],
                    ['Ratio', true],
                ],
            ],
            [
                'q' => 'The number of goals scored in a football match is what kind of quantitative data?',
                'opts' => [
                    ['Continuous', false],
                    ['Discrete', true],
                    ['Ordinal', false],
                    ['Nominal', false],
                ],
            ],
            [
                'q' => 'A person\'s height measured in centimetres is an example of what kind of data?',
                'opts' => [
                    ['Discrete', false],
                    ['Nominal', false],
                    ['Continuous', true],
                    ['Ordinal', false],
                ],
            ],
            [
                'q' => 'Which of the following is qualitative (categorical) data?',
                'opts' => [
                    ['A student\'s exam score', false],
                    ['The temperature today', false],
                    ['A person\'s favourite colour', true],
                    ['The number of items in a cart', false],
                ],
            ],

            // ── MEASURES OF CENTRAL TENDENCY ─────────────────────────────
            [
                'q' => 'What is the MEAN of the dataset: [4, 8, 6, 5, 7]?',
                'opts' => [
                    ['5', false],
                    ['6', true],
                    ['7', false],
                    ['30', false],
                ],
            ],
            [
                'q' => 'What is the MEDIAN of the dataset: [3, 7, 1, 9, 5]?',
                'opts' => [
                    ['3', false],
                    ['7', false],
                    ['5', true],
                    ['1', false],
                ],
            ],
            [
                'q' => 'What is the MODE of the dataset: [2, 4, 4, 6, 8, 4, 2]?',
                'opts' => [
                    ['2', false],
                    ['6', false],
                    ['8', false],
                    ['4', true],
                ],
            ],
            [
                'q' => 'Which measure of central tendency is MOST affected by extreme outliers?',
                'opts' => [
                    ['Median', false],
                    ['Mode', false],
                    ['Mean', true],
                    ['Range', false],
                ],
            ],
            [
                'q' => 'A dataset has values [10, 10, 10, 10, 10]. What is the mean, median, and mode?',
                'opts' => [
                    ['Mean=10, Median=10, Mode=None', false],
                    ['Mean=10, Median=10, Mode=10', true],
                    ['Mean=50, Median=10, Mode=10', false],
                    ['Mean=10, Median=5, Mode=10', false],
                ],
            ],
            [
                'q' => 'Which measure of central tendency is best to use for skewed data or data with outliers?',
                'opts' => [
                    ['Mean', false],
                    ['Mode', false],
                    ['Median', true],
                    ['Standard deviation', false],
                ],
            ],
            [
                'q' => 'What is the median of [5, 10, 15, 20]? (even number of values)',
                'opts' => [
                    ['10', false],
                    ['15', false],
                    ['12.5', true],
                    ['13', false],
                ],
            ],
            [
                'q' => 'A dataset has NO value that appears more than once. How many modes does it have?',
                'opts' => [
                    ['1', false],
                    ['The mean is the mode', false],
                    ['No mode', true],
                    ['All values are modes', false],
                ],
            ],

            // ── MEASURES OF SPREAD / VARIABILITY ─────────────────────────
            [
                'q' => 'What does the RANGE of a dataset measure?',
                'opts' => [
                    ['The average of all values', false],
                    ['The middle value', false],
                    ['The difference between the maximum and minimum values', true],
                    ['How often values repeat', false],
                ],
            ],
            [
                'q' => 'What is the range of [2, 5, 8, 12, 20]?',
                'opts' => [
                    ['10', false],
                    ['18', true],
                    ['12', false],
                    ['20', false],
                ],
            ],
            [
                'q' => 'Variance measures which of the following?',
                'opts' => [
                    ['The middle value of the data', false],
                    ['The average squared distance from the mean', true],
                    ['The most frequent value', false],
                    ['The difference between max and min', false],
                ],
            ],
            [
                'q' => 'Standard deviation is best described as:',
                'opts' => [
                    ['The square of the variance', false],
                    ['The average of all values', false],
                    ['The square root of the variance', true],
                    ['The middle value of sorted data', false],
                ],
            ],
            [
                'q' => 'A low standard deviation means:',
                'opts' => [
                    ['Data points are spread far from the mean', false],
                    ['Data points are clustered close to the mean', true],
                    ['The dataset has many outliers', false],
                    ['The mean is very high', false],
                ],
            ],
            [
                'q' => 'What does IQR stand for?',
                'opts' => [
                    ['Initial Quartile Ratio', false],
                    ['Interquartile Range', true],
                    ['Internal Quantity Range', false],
                    ['Integrated Quartile Result', false],
                ],
            ],
            [
                'q' => 'The IQR is calculated as:',
                'opts' => [
                    ['Q3 − Q1', true],
                    ['Q2 − Q1', false],
                    ['Max − Min', false],
                    ['Q4 − Q2', false],
                ],
            ],
            [
                'q' => 'Which measure of spread is LEAST affected by outliers?',
                'opts' => [
                    ['Range', false],
                    ['Variance', false],
                    ['Standard Deviation', false],
                    ['IQR', true],
                ],
            ],

            // ── PROBABILITY BASICS ────────────────────────────────────────
            [
                'q' => 'What is the probability of flipping a fair coin and getting Heads?',
                'opts' => [
                    ['1', false],
                    ['0.25', false],
                    ['0.5', true],
                    ['0.75', false],
                ],
            ],
            [
                'q' => 'Probability values always fall between:',
                'opts' => [
                    ['-1 and 1', false],
                    ['0 and 100', false],
                    ['0 and 1', true],
                    ['1 and 10', false],
                ],
            ],
            [
                'q' => 'A bag has 3 red balls and 7 blue balls. What is the probability of picking a red ball?',
                'opts' => [
                    ['0.7', false],
                    ['0.3', true],
                    ['3', false],
                    ['1/7', false],
                ],
            ],
            [
                'q' => 'Two events are INDEPENDENT when:',
                'opts' => [
                    ['One event affects the probability of the other', false],
                    ['The outcome of one does NOT affect the probability of the other', true],
                    ['They cannot happen at the same time', false],
                    ['They always happen together', false],
                ],
            ],
            [
                'q' => 'What is the probability of rolling a 6 on a fair six-sided die?',
                'opts' => [
                    ['1/3', false],
                    ['1/2', false],
                    ['1/4', false],
                    ['1/6', true],
                ],
            ],
            [
                'q' => 'If P(A) = 0.4 and A and B are mutually exclusive with P(B) = 0.3, what is P(A or B)?',
                'opts' => [
                    ['0.12', false],
                    ['0.7', true],
                    ['0.4', false],
                    ['1.0', false],
                ],
            ],
            [
                'q' => 'What does P(A|B) mean in probability notation?',
                'opts' => [
                    ['The probability of A times B', false],
                    ['The probability of A or B', false],
                    ['The probability of A given that B has occurred', true],
                    ['The probability of neither A nor B', false],
                ],
            ],

            // ── DISTRIBUTIONS ─────────────────────────────────────────────
            [
                'q' => 'In a NORMAL distribution, which of the following is true?',
                'opts' => [
                    ['The mean, median, and mode are all different', false],
                    ['The distribution is always skewed to the right', false],
                    ['The mean, median, and mode are all equal', true],
                    ['It has two peaks', false],
                ],
            ],
            [
                'q' => 'The bell-shaped curve is associated with which distribution?',
                'opts' => [
                    ['Uniform Distribution', false],
                    ['Binomial Distribution', false],
                    ['Normal Distribution', true],
                    ['Exponential Distribution', false],
                ],
            ],
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
                'q' => 'A distribution where all outcomes are equally likely is called:',
                'opts' => [
                    ['Normal Distribution', false],
                    ['Skewed Distribution', false],
                    ['Uniform Distribution', true],
                    ['Binomial Distribution', false],
                ],
            ],
            [
                'q' => 'A distribution is POSITIVELY SKEWED (right-skewed) when:',
                'opts' => [
                    ['The tail extends to the left', false],
                    ['The mean equals the median', false],
                    ['The tail extends to the right', true],
                    ['There are no outliers', false],
                ],
            ],
            [
                'q' => 'In a right-skewed distribution, which is typically true?',
                'opts' => [
                    ['Mean < Median < Mode', false],
                    ['Mean = Median = Mode', false],
                    ['Mode < Median < Mean', true],
                    ['Median > Mean > Mode', false],
                ],
            ],
            [
                'q' => 'What does a standard normal distribution have as its mean and standard deviation?',
                'opts' => [
                    ['Mean = 1, SD = 0', false],
                    ['Mean = 0, SD = 1', true],
                    ['Mean = 0, SD = 0', false],
                    ['Mean = 1, SD = 1', false],
                ],
            ],

            // ── CORRELATION & RELATIONSHIPS ───────────────────────────────
            [
                'q' => 'Correlation measures:',
                'opts' => [
                    ['Whether one variable causes another to change', false],
                    ['The strength and direction of a linear relationship between two variables', true],
                    ['The average of two variables', false],
                    ['The difference between two variables', false],
                ],
            ],
            [
                'q' => 'A correlation coefficient of +1 means:',
                'opts' => [
                    ['No relationship between the variables', false],
                    ['A perfect negative linear relationship', false],
                    ['A perfect positive linear relationship', true],
                    ['A weak positive relationship', false],
                ],
            ],
            [
                'q' => 'A correlation coefficient of -0.9 indicates:',
                'opts' => [
                    ['A weak negative relationship', false],
                    ['No relationship', false],
                    ['A strong positive relationship', false],
                    ['A strong negative relationship', true],
                ],
            ],
            [
                'q' => 'A correlation coefficient close to 0 means:',
                'opts' => [
                    ['A strong relationship between variables', false],
                    ['A perfect positive relationship', false],
                    ['Little to no linear relationship', true],
                    ['The variables are identical', false],
                ],
            ],
            [
                'q' => 'Which famous phrase reminds us that a correlation does NOT prove a cause?',
                'opts' => [
                    ['"No data, no conclusion"', false],
                    ['"Correlation does not imply causation"', true],
                    ['"Statistics never lie"', false],
                    ['"Every variable is related"', false],
                ],
            ],
            [
                'q' => 'In a scatter plot, what does a positive correlation look like?',
                'opts' => [
                    ['Points form a horizontal line', false],
                    ['Points go from top-left to bottom-right', false],
                    ['Points go from bottom-left to top-right', true],
                    ['Points are scattered randomly with no pattern', false],
                ],
            ],

            // ── SAMPLING & BIAS ───────────────────────────────────────────
            [
                'q' => 'What is sampling bias?',
                'opts' => [
                    ['When every member of a population has an equal chance of being selected', false],
                    ['When a sample does not accurately represent the population it is drawn from', true],
                    ['When the sample size is very large', false],
                    ['When all data is collected from the population', false],
                ],
            ],
            [
                'q' => 'In RANDOM SAMPLING, each member of the population has:',
                'opts' => [
                    ['No chance of being selected', false],
                    ['An equal chance of being selected', true],
                    ['A higher chance of being selected than others', false],
                    ['A chance only if they volunteer', false],
                ],
            ],
            [
                'q' => 'As sample size INCREASES, the sample mean generally:',
                'opts' => [
                    ['Moves further from the population mean', false],
                    ['Stays exactly the same no matter what', false],
                    ['Gets closer to the true population mean', true],
                    ['Becomes less reliable', false],
                ],
            ],

            // ── GENERAL / APPLIED CONCEPTS ────────────────────────────────
            [
                'q' => 'An OUTLIER in a dataset is:',
                'opts' => [
                    ['The most common value', false],
                    ['A value that lies far away from the other data points', true],
                    ['The middle value when data is sorted', false],
                    ['Any value below the mean', false],
                ],
            ],
            [
                'q' => 'Which Python library is most commonly used for basic statistics and data manipulation?',
                'opts' => [
                    ['matplotlib', false],
                    ['tensorflow', false],
                    ['pandas', true],
                    ['flask', false],
                ],
            ],
            [
                'q' => 'What does the `describe()` method in pandas return?',
                'opts' => [
                    ['A list of all column names', false],
                    ['Summary statistics like mean, std, min, and max', true],
                    ['The first 5 rows of a DataFrame', false],
                    ['A count of null values', false],
                ],
            ],
            [
                'q' => 'A histogram is used to visualize:',
                'opts' => [
                    ['The relationship between two variables', false],
                    ['The distribution (frequency) of a single numerical variable', true],
                    ['The proportion of categories in a dataset', false],
                    ['Time-series trends', false],
                ],
            ],
            [
                'q' => 'A box plot (box-and-whisker plot) is most useful for:',
                'opts' => [
                    ['Showing the exact value of every data point', false],
                    ['Displaying median, quartiles, and outliers at a glance', true],
                    ['Comparing categories using bars', false],
                    ['Plotting two variables against each other', false],
                ],
            ],

        ];

        foreach ($qaData as $data) {
            $question = ChallengeQuestion::create([
                'challenge_id'  => $challenge->id,
                'question_text' => $data['q'],
                'challenge_category_id' => $category->id, // Assuming all questions belong to the 'newbie' category with ID 1
            ]);

            foreach ($data['opts'] as $opt) {
                ChallengeOption::create([
                    'challenge_question_id' => $question->id,
                    'option_text'           => $opt[0],
                    'is_correct'            => $opt[1],
                ]);
            }
        }

        $this->command->info("✅ Done! 50 questions seeded for Module 2 — Basics of Statistics.");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Newbie");
        $this->command->newLine();
        $this->command->comment("NOTE: If Module 2 doesn't appear second on the map, ensure the challenges");
        $this->command->comment("table has an `order_index` column and update the map controller query:");
        $this->command->comment("  ->orderBy('order_index', 'asc')");
        $this->command->comment("This challenge was seeded with order_index = 2.");
    }
}

/*
 * ─── TOPICS COVERED (mapped from Module2LessonsSeeder) ───────────────────────
 *
 *  Lesson 2.1  — What is Statistics? Types of Data
 *                (Descriptive vs Inferential, Population vs Sample,
 *                 Nominal / Ordinal / Interval / Ratio, Discrete vs Continuous)
 *
 *  Lesson 2.2  — Measures of Central Tendency
 *                (Mean, Median, Mode — when to use each, effect of outliers)
 *
 *  Lesson 2.3  — Measures of Spread / Variability
 *                (Range, Variance, Standard Deviation, IQR)
 *
 *  Lesson 2.4  — Probability Basics
 *                (P(A), 0–1 range, independent events, conditional P(A|B),
 *                 mutually exclusive events)
 *
 *  Lesson 2.5  — Distributions
 *                (Normal / Bell curve, 68-95-99.7 rule, Uniform,
 *                 Skewness, Standard Normal)
 *
 *  Lesson 2.6  — Correlation & Relationships
 *                (Correlation coefficient, scatter plots, causation ≠ correlation)
 *
 *  Lesson 2.7  — Sampling & Bias
 *                (Random sampling, sampling bias, law of large numbers)
 *
 *  General     — Outliers, pandas describe(), histogram, box plot
 *
 * ─── USAGE ───────────────────────────────────────────────────────────────────
 *
 *  php artisan db:seed --class=Module2ChallengeSeederNewbie
 *
 * ─── DEPENDENCY ──────────────────────────────────────────────────────────────
 *
 *  Run ChallengeCategorySeeder first to ensure the 'newbie' category exists.
 */