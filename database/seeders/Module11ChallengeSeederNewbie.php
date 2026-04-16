<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module11ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 11 — Introduction to Bayesian Data Analysis (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Bayesian Data Analysis',
            'description'           => 'Test your knowledge of the very basics of Bayesian thinking — what it means to update beliefs with evidence, key vocabulary like prior and posterior, and simple probability concepts. No advanced math required!',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 11,
        ]);

        $this->command->info("Seeding 30 newbie-friendly questions...");

        $qaData = [

            // ── WHAT IS BAYESIAN THINKING ─────────────────────────────────
            [
                'q' => 'What is the main idea behind Bayesian thinking?',
                'opts' => [
                    ['You can never change your mind once you form an opinion', false],
                    ['You update your beliefs when you get new evidence', true],
                    ['You only trust data that has been collected perfectly', false],
                    ['Probability is always exactly 0 or 1', false],
                ],
            ],
            [
                'q' => 'In Bayesian analysis, what is a "prior"?',
                'opts' => [
                    ['The final conclusion after seeing all the data', false],
                    ['Your belief about something BEFORE seeing new evidence', true],
                    ['The total number of data points collected', false],
                    ['A type of graph used in statistics', false],
                ],
            ],
            [
                'q' => 'What is a "posterior" in Bayesian analysis?',
                'opts' => [
                    ['Your updated belief AFTER seeing new evidence', true],
                    ['Your belief before seeing any data', false],
                    ['The probability that the data is wrong', false],
                    ['A chart showing all possible outcomes', false],
                ],
            ],
            [
                'q' => 'Which of the following best describes Bayesian probability?',
                'opts' => [
                    ['The long-run frequency of an event over many trials', false],
                    ['A degree of belief or confidence in a statement', true],
                    ['The exact value from a mathematical formula', false],
                    ['Only applies to coin flips and dice rolls', false],
                ],
            ],
            [
                'q' => 'You believe there is a 30% chance of rain today. In Bayesian terms, this 30% is your:',
                'opts' => [
                    ['Posterior', false],
                    ['Likelihood', false],
                    ['Prior', true],
                    ['Evidence', false],
                ],
            ],
            [
                'q' => 'After checking the weather forecast and seeing dark clouds, you now think there is a 75% chance of rain. This 75% is your:',
                'opts' => [
                    ['Prior', false],
                    ['Likelihood', false],
                    ['Posterior', true],
                    ['Sample size', false],
                ],
            ],

            // ── BAYES THEOREM BASICS ──────────────────────────────────────
            [
                'q' => "Bayes' Theorem is named after which person?",
                'opts' => [
                    ['Thomas Bayes', true],
                    ['Isaac Newton', false],
                    ['Albert Einstein', false],
                    ['Carl Gauss', false],
                ],
            ],
            [
                'q' => "What does Bayes' Theorem help us calculate?",
                'opts' => [
                    ['The average of a set of numbers', false],
                    ['The probability of an event given that another event has occurred', true],
                    ['The standard deviation of a dataset', false],
                    ['The total number of outcomes in an experiment', false],
                ],
            ],
            [
                'q' => 'In the formula P(A|B), what does the vertical bar "|" mean?',
                'opts' => [
                    ['A divided by B', false],
                    ['A plus B', false],
                    ['"given that" — the probability of A given B has happened', true],
                    ['A multiplied by B', false],
                ],
            ],
            [
                'q' => 'If P(Rain) = 0.3, what is P(No Rain)?',
                'opts' => [
                    ['0.3', false],
                    ['0.6', false],
                    ['0.7', true],
                    ['1.3', false],
                ],
            ],
            [
                'q' => 'Probabilities must always be between which two values?',
                'opts' => [
                    ['-1 and 1', false],
                    ['0 and 1', true],
                    ['0 and 100', false],
                    ['1 and 10', false],
                ],
            ],
            [
                'q' => 'If two events cannot happen at the same time, they are called:',
                'opts' => [
                    ['Independent events', false],
                    ['Dependent events', false],
                    ['Mutually exclusive events', true],
                    ['Conditional events', false],
                ],
            ],

            // ── LIKELIHOOD ────────────────────────────────────────────────
            [
                'q' => 'In Bayesian terms, what is the "likelihood"?',
                'opts' => [
                    ['How confident you are before seeing data', false],
                    ['How probable the observed data is, given a specific hypothesis', true],
                    ['The final updated belief after all analysis', false],
                    ['The number of times an experiment was repeated', false],
                ],
            ],
            [
                'q' => 'A coin lands heads 8 out of 10 flips. The likelihood measures:',
                'opts' => [
                    ['How fair the coin is after flipping', false],
                    ['How probable getting 8 heads out of 10 is for a given coin bias', true],
                    ['The total number of possible outcomes', false],
                    ['Whether the flipper is skilled or not', false],
                ],
            ],
            [
                'q' => 'Which of the following is the correct order in Bayesian updating?',
                'opts' => [
                    ['Posterior → Prior → Likelihood', false],
                    ['Likelihood → Prior → Posterior', false],
                    ['Prior → Likelihood → Posterior', true],
                    ['Data → Posterior → Prior', false],
                ],
            ],

            // ── PRIOR DISTRIBUTIONS BASICS ────────────────────────────────
            [
                'q' => 'What is a "uniform prior"?',
                'opts' => [
                    ['A prior that says some outcomes are much more likely than others', false],
                    ['A prior that gives equal probability to all possible outcomes', true],
                    ['A prior based on expert knowledge', false],
                    ['A prior taken from a previous experiment', false],
                ],
            ],
            [
                'q' => 'If you have no idea whether a coin is fair or not, which prior is most appropriate?',
                'opts' => [
                    ['A prior strongly favoring heads', false],
                    ['A uniform prior giving all biases equal chance', true],
                    ['A prior strongly favoring tails', false],
                    ['No prior is needed', false],
                ],
            ],
            [
                'q' => 'An "informative prior" is one that:',
                'opts' => [
                    ['Contains no useful information', false],
                    ['Reflects existing knowledge or beliefs about the parameter', true],
                    ['Is always correct', false],
                    ['Can only be used with large datasets', false],
                ],
            ],
            [
                'q' => 'Why do we use priors in Bayesian analysis?',
                'opts' => [
                    ['To make the math simpler by ignoring data', false],
                    ['To incorporate existing knowledge before seeing new data', true],
                    ['To ensure the posterior is always the same as the prior', false],
                    ['To make the likelihood equal to 1', false],
                ],
            ],

            // ── SIMPLE REASONING ──────────────────────────────────────────
            [
                'q' => 'You test positive for a rare disease. The test is not perfect. What does Bayesian thinking suggest you should consider?',
                'opts' => [
                    ['Only the test result — it must be correct', false],
                    ['Both the rarity of the disease (prior) and the test accuracy (likelihood)', true],
                    ['Ignore the test and assume the worst', false],
                    ['Run the test again with no changes', false],
                ],
            ],
            [
                'q' => 'In Bayesian analysis, as you collect MORE data, your posterior generally:',
                'opts' => [
                    ['Becomes more and more like your prior', false],
                    ['Becomes dominated by the data and less influenced by the prior', true],
                    ['Stays exactly the same as the prior', false],
                    ['Becomes impossible to calculate', false],
                ],
            ],
            [
                'q' => 'Two people have different priors about the same event. After seeing the same large dataset, their posteriors will:',
                'opts' => [
                    ['Remain very different forever', false],
                    ['Tend to converge and become similar', true],
                    ['Both become zero', false],
                    ['Both become one', false],
                ],
            ],
            [
                'q' => 'What does it mean to "update" a belief in Bayesian analysis?',
                'opts' => [
                    ['Delete your old belief completely', false],
                    ['Combine your prior belief with new evidence to form a posterior', true],
                    ['Replace your belief with someone else\'s opinion', false],
                    ['Keep your belief exactly the same no matter what', false],
                ],
            ],

            // ── KEY VOCABULARY ────────────────────────────────────────────
            [
                'q' => 'What does "P(A)" mean in probability notation?',
                'opts' => [
                    ['The value of A', false],
                    ['The probability of event A occurring', true],
                    ['A divided by something', false],
                    ['The posterior of A', false],
                ],
            ],
            [
                'q' => 'What is a "hypothesis" in the context of Bayesian analysis?',
                'opts' => [
                    ['A proven fact', false],
                    ['A statement or model about the world that we assign a probability to', true],
                    ['A type of chart', false],
                    ['The final answer after all computations', false],
                ],
            ],
            [
                'q' => 'What is "evidence" in Bayesian analysis?',
                'opts' => [
                    ['The prior belief', false],
                    ['The observed data that we use to update our beliefs', true],
                    ['The posterior distribution', false],
                    ['A type of statistical test', false],
                ],
            ],
            [
                'q' => 'The term "posterior predictive" refers to:',
                'opts' => [
                    ['A prediction made before collecting any data', false],
                    ['A prediction for new data based on the posterior distribution', true],
                    ['The prior before any update', false],
                    ['A type of prior distribution', false],
                ],
            ],

            // ── EVERYDAY BAYESIAN THINKING ────────────────────────────────
            [
                'q' => 'A doctor knows a disease affects 1 in 1000 people. A test for the disease is 99% accurate. If you test positive, which factor is important for interpreting the result?',
                'opts' => [
                    ['Only the 99% accuracy matters', false],
                    ['Both the 1-in-1000 base rate and the test accuracy matter', true],
                    ['Only the base rate matters', false],
                    ['Neither factor matters', false],
                ],
            ],
            [
                'q' => 'Your friend says, "I flipped a coin 5 times and got heads every time, so it MUST be biased." What does Bayesian thinking say?',
                'opts' => [
                    ['Your friend is definitely correct', false],
                    ['5 heads is unlikely but possible for a fair coin — more data is needed before concluding bias', true],
                    ['The coin is definitely fair', false],
                    ['Probability does not apply to coins', false],
                ],
            ],
            [
                'q' => 'Which statement best describes the Bayesian approach to statistics?',
                'opts' => [
                    ['Probability describes only the long-run frequency of repeatable experiments', false],
                    ['Probability represents our degree of belief, which we update with data', true],
                    ['Statistics should never include personal beliefs', false],
                    ['Only large datasets can be analyzed with Bayesian methods', false],
                ],
            ],
            [
                'q' => 'Bayesian analysis is different from "frequentist" statistics mainly because:',
                'opts' => [
                    ['Bayesian analysis does not use data', false],
                    ['Bayesian analysis incorporates prior beliefs; frequentist does not', true],
                    ['Frequentist analysis always gives better answers', false],
                    ['Bayesian analysis can only be done by computers', false],
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

        $this->command->info("✅ Done! 30 questions seeded for Module 11 — Introduction to Bayesian Data Analysis (Newbie).");
    }
}