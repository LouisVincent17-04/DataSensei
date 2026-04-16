<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module8ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
            ->where('title', 'Statistical Methods & Experimental Design')
            ->delete();

        $this->command->info("Creating Module 8 — Statistical Methods & Experimental Design (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Statistical Methods & Experimental Design',
            'description'           => 'Test your basic understanding of statistics — mean, median, mode, probability, and simple data concepts. No advanced math required!',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 8,
        ]);

        $this->command->info("Seeding 50 newbie-friendly statistics questions...");

        $qaData = [

            // ── BASIC STATS CONCEPTS ─────────────────────────────────────
            [
                'q' => 'What is the MEAN of the data set: 2, 4, 6, 8, 10?',
                'opts' => [
                    ['4', false],
                    ['5', false],
                    ['6', true],
                    ['8', false],
                ],
            ],
            [
                'q' => 'What is the MEDIAN of: 3, 7, 1, 9, 5?',
                'opts' => [
                    ['3', false],
                    ['5', true],
                    ['7', false],
                    ['9', false],
                ],
            ],
            [
                'q' => 'What is the MODE of: 4, 2, 4, 3, 1, 4, 2?',
                'opts' => [
                    ['2', false],
                    ['3', false],
                    ['4', true],
                    ['1', false],
                ],
            ],
            [
                'q' => 'Which measure of central tendency is most affected by extreme values (outliers)?',
                'opts' => [
                    ['Mode', false],
                    ['Median', false],
                    ['Mean', true],
                    ['Range', false],
                ],
            ],
            [
                'q' => 'What is the RANGE of the data set: 5, 12, 3, 8, 20?',
                'opts' => [
                    ['15', false],
                    ['17', true],
                    ['20', false],
                    ['8', false],
                ],
            ],
            [
                'q' => 'The range is calculated as:',
                'opts' => [
                    ['Mean minus Median', false],
                    ['Largest value minus Smallest value', true],
                    ['Sum of all values divided by count', false],
                    ['Middle value of the sorted set', false],
                ],
            ],
            [
                'q' => 'You have the scores: 10, 10, 20, 30, 30. What is the mode?',
                'opts' => [
                    ['10 only', false],
                    ['30 only', false],
                    ['10 and 30', true],
                    ['20', false],
                ],
            ],
            [
                'q' => 'What is the mean of: 0, 0, 0, 12?',
                'opts' => [
                    ['0', false],
                    ['3', true],
                    ['4', false],
                    ['12', false],
                ],
            ],

            // ── PROBABILITY BASICS ───────────────────────────────────────
            [
                'q' => 'A fair coin is flipped once. What is the probability of getting HEADS?',
                'opts' => [
                    ['1/4', false],
                    ['1/3', false],
                    ['1/2', true],
                    ['1', false],
                ],
            ],
            [
                'q' => 'A bag has 3 red and 7 blue marbles. What is the probability of picking a RED marble?',
                'opts' => [
                    ['3/7', false],
                    ['7/10', false],
                    ['3/10', true],
                    ['1/3', false],
                ],
            ],
            [
                'q' => 'Probability values must always be between:',
                'opts' => [
                    ['-1 and 1', false],
                    ['0 and 1', true],
                    ['0 and 100', false],
                    ['1 and 10', false],
                ],
            ],
            [
                'q' => 'A single die is rolled. What is the probability of rolling a 6?',
                'opts' => [
                    ['1/4', false],
                    ['1/5', false],
                    ['1/6', true],
                    ['1/2', false],
                ],
            ],
            [
                'q' => 'If an event is IMPOSSIBLE, its probability is:',
                'opts' => [
                    ['1', false],
                    ['0.5', false],
                    ['-1', false],
                    ['0', true],
                ],
            ],
            [
                'q' => 'If an event is CERTAIN to happen, its probability is:',
                'opts' => [
                    ['0', false],
                    ['0.5', false],
                    ['1', true],
                    ['100', false],
                ],
            ],
            [
                'q' => 'A standard deck has 52 cards. What is the probability of drawing an ACE?',
                'opts' => [
                    ['1/52', false],
                    ['4/52', true],
                    ['13/52', false],
                    ['2/52', false],
                ],
            ],
            [
                'q' => 'Which of the following is NOT a valid probability?',
                'opts' => [
                    ['0.25', false],
                    ['0', false],
                    ['1', false],
                    ['1.5', true],
                ],
            ],

            // ── DATA TYPES & LEVELS ──────────────────────────────────────
            [
                'q' => 'Which of the following is an example of CATEGORICAL data?',
                'opts' => [
                    ['Height in centimeters', false],
                    ['Temperature in Celsius', false],
                    ['Eye color (blue, brown, green)', true],
                    ['Weight in kilograms', false],
                ],
            ],
            [
                'q' => 'Which of the following is NUMERICAL (quantitative) data?',
                'opts' => [
                    ['Favorite sport', false],
                    ['Number of siblings', true],
                    ['Country of birth', false],
                    ['Blood type', false],
                ],
            ],
            [
                'q' => 'A survey asks "How many hours of sleep do you get?" — this data is:',
                'opts' => [
                    ['Categorical', false],
                    ['Qualitative', false],
                    ['Numerical (Quantitative)', true],
                    ['Ordinal only', false],
                ],
            ],
            [
                'q' => 'T-shirt sizes (Small, Medium, Large) are an example of which data type?',
                'opts' => [
                    ['Nominal', false],
                    ['Ordinal', true],
                    ['Interval', false],
                    ['Ratio', false],
                ],
            ],

            // ── GRAPHS & VISUALIZATION ───────────────────────────────────
            [
                'q' => 'Which chart is BEST for showing how a whole is divided into parts?',
                'opts' => [
                    ['Bar chart', false],
                    ['Pie chart', true],
                    ['Line graph', false],
                    ['Histogram', false],
                ],
            ],
            [
                'q' => 'Which graph is BEST for showing a trend over time?',
                'opts' => [
                    ['Pie chart', false],
                    ['Bar chart', false],
                    ['Line graph', true],
                    ['Scatter plot', false],
                ],
            ],
            [
                'q' => 'A histogram is most similar to which other chart type?',
                'opts' => [
                    ['Pie chart', false],
                    ['Bar chart', true],
                    ['Line graph', false],
                    ['Scatter plot', false],
                ],
            ],
            [
                'q' => 'What does each bar in a bar chart typically represent?',
                'opts' => [
                    ['A data point', false],
                    ['A category and its frequency or value', true],
                    ['A probability', false],
                    ['The median', false],
                ],
            ],

            // ── DESCRIPTIVE STATISTICS ───────────────────────────────────
            [
                'q' => 'Descriptive statistics are used to:',
                'opts' => [
                    ['Make predictions about future data', false],
                    ['Summarize and describe a data set', true],
                    ['Prove cause and effect', false],
                    ['Remove outliers from data', false],
                ],
            ],
            [
                'q' => 'Which of the following is a measure of SPREAD (variability)?',
                'opts' => [
                    ['Mean', false],
                    ['Mode', false],
                    ['Range', true],
                    ['Median', false],
                ],
            ],
            [
                'q' => 'A data set where most values cluster near the mean is said to have:',
                'opts' => [
                    ['High variability', false],
                    ['Low variability', true],
                    ['High skewness', false],
                    ['No mode', false],
                ],
            ],
            [
                'q' => 'What does it mean if a data set has NO mode?',
                'opts' => [
                    ['All values are the same', false],
                    ['No value repeats', true],
                    ['The mean equals the median', false],
                    ['The range is zero', false],
                ],
            ],

            // ── SAMPLES & POPULATIONS ────────────────────────────────────
            [
                'q' => 'A POPULATION in statistics refers to:',
                'opts' => [
                    ['A sample of data points', false],
                    ['The entire group being studied', true],
                    ['The most common value in a data set', false],
                    ['A type of chart', false],
                ],
            ],
            [
                'q' => 'A SAMPLE in statistics refers to:',
                'opts' => [
                    ['The entire population', false],
                    ['A subset of the population selected for study', true],
                    ['The average of the data', false],
                    ['The largest value in the data', false],
                ],
            ],
            [
                'q' => 'Why do researchers usually study a sample instead of an entire population?',
                'opts' => [
                    ['Samples are always more accurate', false],
                    ['It is faster and less costly', true],
                    ['Samples have no bias', false],
                    ['Populations are too small to study', false],
                ],
            ],
            [
                'q' => 'In a random sample, every member of the population has:',
                'opts' => [
                    ['A zero chance of being selected', false],
                    ['An equal chance of being selected', true],
                    ['A guaranteed chance of being selected', false],
                    ['No chance of being selected', false],
                ],
            ],

            // ── EXPERIMENTS & VARIABLES ──────────────────────────────────
            [
                'q' => 'In an experiment, the variable that the researcher CHANGES is called the:',
                'opts' => [
                    ['Dependent variable', false],
                    ['Controlled variable', false],
                    ['Independent variable', true],
                    ['Constant variable', false],
                ],
            ],
            [
                'q' => 'The variable that is MEASURED as the outcome of an experiment is called the:',
                'opts' => [
                    ['Independent variable', false],
                    ['Dependent variable', true],
                    ['Extraneous variable', false],
                    ['Control variable', false],
                ],
            ],
            [
                'q' => 'In a study testing whether caffeine improves test scores, the test score is the:',
                'opts' => [
                    ['Independent variable', false],
                    ['Control variable', false],
                    ['Dependent variable', true],
                    ['Confounding variable', false],
                ],
            ],
            [
                'q' => 'What is a CONTROL GROUP in an experiment?',
                'opts' => [
                    ['The group that receives the treatment', false],
                    ['The group that does NOT receive the treatment, used for comparison', true],
                    ['The group with the highest scores', false],
                    ['The group selected randomly', false],
                ],
            ],

            // ── BASIC INFERENCE & HYPOTHESIS ─────────────────────────────
            [
                'q' => 'A NULL HYPOTHESIS (H₀) usually states that:',
                'opts' => [
                    ['There is a significant difference', false],
                    ['There is no effect or no difference', true],
                    ['The experiment has failed', false],
                    ['The sample size is too small', false],
                ],
            ],
            [
                'q' => 'An ALTERNATIVE HYPOTHESIS (H₁) typically states that:',
                'opts' => [
                    ['There is no difference', false],
                    ['There is a significant effect or difference', true],
                    ['The null hypothesis is true', false],
                    ['More data is needed', false],
                ],
            ],
            [
                'q' => 'What does a p-value of 0.03 roughly mean?',
                'opts' => [
                    ['There is a 97% chance the result is false', false],
                    ['There is a 3% chance of getting results this extreme if H₀ is true', true],
                    ['The effect size is 3%', false],
                    ['The null hypothesis is true', false],
                ],
            ],
            [
                'q' => 'If the p-value is LESS than 0.05, we typically:',
                'opts' => [
                    ['Fail to reject the null hypothesis', false],
                    ['Reject the null hypothesis', true],
                    ['Accept the null hypothesis', false],
                    ['Conclude the study is invalid', false],
                ],
            ],

            // ── CORRELATION ──────────────────────────────────────────────
            [
                'q' => 'A POSITIVE correlation between two variables means:',
                'opts' => [
                    ['As one increases, the other decreases', false],
                    ['As one increases, the other also increases', true],
                    ['The two variables are unrelated', false],
                    ['One variable causes the other', false],
                ],
            ],
            [
                'q' => 'A NEGATIVE correlation means:',
                'opts' => [
                    ['As one variable increases, the other also increases', false],
                    ['There is no relationship', false],
                    ['As one variable increases, the other decreases', true],
                    ['The data is invalid', false],
                ],
            ],
            [
                'q' => 'Correlation does NOT imply:',
                'opts' => [
                    ['A relationship between variables', false],
                    ['Causation', true],
                    ['A pattern in data', false],
                    ['A statistical association', false],
                ],
            ],
            [
                'q' => 'The correlation coefficient ranges from:',
                'opts' => [
                    ['0 to 1', false],
                    ['-1 to 0', false],
                    ['-1 to 1', true],
                    ['-100 to 100', false],
                ],
            ],

            // ── GENERAL STATISTICS VOCABULARY ────────────────────────────
            [
                'q' => 'What does "frequency" mean in a data set?',
                'opts' => [
                    ['The average of the values', false],
                    ['How often a value appears', true],
                    ['The highest value', false],
                    ['The spread of the data', false],
                ],
            ],
            [
                'q' => 'A survey result where almost everyone answers the same way is described as having:',
                'opts' => [
                    ['High variance', false],
                    ['Low variance', true],
                    ['High range', false],
                    ['No mean', false],
                ],
            ],
            [
                'q' => 'Which term describes a value that is far away from most other values in a data set?',
                'opts' => [
                    ['Mode', false],
                    ['Mean', false],
                    ['Outlier', true],
                    ['Frequency', false],
                ],
            ],
            [
                'q' => 'What is a FREQUENCY TABLE used for?',
                'opts' => [
                    ['Calculating the mean of data', false],
                    ['Showing how often each value occurs in a data set', true],
                    ['Drawing a scatter plot', false],
                    ['Finding the range of data', false],
                ],
            ],
            [
                'q' => 'Which of the following best describes INFERENTIAL statistics?',
                'opts' => [
                    ['Summarizing data you already have', false],
                    ['Drawing conclusions about a population from a sample', true],
                    ['Counting how many items are in a data set', false],
                    ['Creating charts and graphs', false],
                ],
            ],
            [
                'q' => 'A researcher collects data from 200 students at one school to represent all students nationwide. The 200 students are the:',
                'opts' => [
                    ['Population', false],
                    ['Parameter', false],
                    ['Sample', true],
                    ['Control group', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 8 — Statistical Methods & Experimental Design (Newbie).");
    }
}