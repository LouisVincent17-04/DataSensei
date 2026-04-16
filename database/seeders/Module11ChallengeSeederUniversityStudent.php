<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module11ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 11 — Introduction to Bayesian Data Analysis (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Bayesian Data Analysis',
            'description'           => 'Apply Bayes\' Theorem analytically, trace through simple probability calculations, and interpret prior/posterior distributions. Requires basic probability knowledge and logical reasoning.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 600,
            'order_index'           => 1,
        ]);

        $this->command->info("Seeding 50 university-level Bayesian analysis questions...");

        $qaData = [

            // ── BAYES' THEOREM ────────────────────────────────────────────
            [
                'q' => 'Bayes\' Theorem is written as P(A|B) = P(B|A) × P(A) / P(B). What does P(A|B) represent?',
                'opts' => [
                    ['The probability of B given A', false],
                    ['The probability of A given that B has occurred', true],
                    ['The joint probability of A and B', false],
                    ['The marginal probability of A', false],
                ],
            ],
            [
                'q' => 'In Bayesian terminology, what is the "prior"?',
                'opts' => [
                    ['The probability of the data given the hypothesis', false],
                    ['Your belief about a parameter before observing data', true],
                    ['The updated belief after observing data', false],
                    ['The most likely outcome given the evidence', false],
                ],
            ],
            [
                'q' => 'A test for a disease has 95% sensitivity (P(+|disease) = 0.95) and 90% specificity (P(-|no disease) = 0.90). The disease prevalence is 1% (P(disease) = 0.01). Using Bayes\' Theorem, what is P(disease|+)?',
                'opts' => [
                    ['About 95%', false],
                    ['About 87%', false],
                    ['About 8.7%', true],
                    ['About 1%', false],
                ],
            ],
            [
                'q' => 'What is the "likelihood" in Bayes\' Theorem?',
                'opts' => [
                    ['P(A) — the prior probability', false],
                    ['P(A|B) — the posterior probability', false],
                    ['P(B|A) — the probability of the data given the hypothesis', true],
                    ['P(B) — the marginal probability of the data', false],
                ],
            ],
            [
                'q' => 'The denominator P(B) in Bayes\' Theorem is also called the "marginal likelihood." Which formula correctly expands P(B) for two mutually exclusive hypotheses H and ¬H?',
                'opts' => [
                    ['P(B) = P(B|H) + P(B|¬H)', false],
                    ['P(B) = P(B|H) × P(B|¬H)', false],
                    ['P(B) = P(B|H)P(H) + P(B|¬H)P(¬H)', true],
                    ['P(B) = P(H|B) + P(¬H|B)', false],
                ],
            ],
            [
                'q' => 'What does "posterior ∝ likelihood × prior" mean?',
                'opts' => [
                    ['The posterior equals the likelihood times the prior exactly', false],
                    ['The posterior is proportional to the product of the likelihood and prior, before normalising', true],
                    ['You multiply likelihood and prior to get the marginal probability', false],
                    ['The likelihood is always greater than the prior', false],
                ],
            ],
            [
                'q' => 'A bag contains 3 red and 2 blue balls. You draw one at random without replacement and it is red. What is P(next draw is red)?',
                'opts' => [
                    ['3/5', false],
                    ['2/4 = 0.5', true],
                    ['3/4', false],
                    ['1/2', false],
                ],
            ],
            [
                'q' => 'You flip a fair coin and it comes up heads 3 times in a row. After applying Bayes\' Theorem with equal priors over {fair, biased}, which hypothesis gains posterior probability?',
                'opts' => [
                    ['Fair coin, because runs of heads are expected', false],
                    ['Biased coin, because P(data|biased) > P(data|fair)', true],
                    ['Both remain equally probable', false],
                    ['Neither — Bayes\' Theorem cannot update on coin flips', false],
                ],
            ],

            // ── PRIORS & POSTERIORS ────────────────────────────────────────
            [
                'q' => 'Which best describes a "non-informative" (flat) prior?',
                'opts' => [
                    ['A prior that strongly favours one hypothesis', false],
                    ['A prior that assigns equal probability to all parameter values', true],
                    ['A prior derived entirely from the data', false],
                    ['A prior used only when data is unavailable', false],
                ],
            ],
            [
                'q' => 'If your prior is P(θ) = 0.5 and after observing data the posterior P(θ|data) = 0.9, what happened?',
                'opts' => [
                    ['The data strongly contradicted the hypothesis', false],
                    ['The data strongly supported the hypothesis, updating belief upward', true],
                    ['The prior was overwritten entirely by the data', false],
                    ['The likelihood equalled the prior', false],
                ],
            ],
            [
                'q' => 'In Bayesian updating, each new posterior becomes the ______ for the next update.',
                'opts' => [
                    ['Likelihood', false],
                    ['Marginal probability', false],
                    ['Prior', true],
                    ['Evidence', false],
                ],
            ],
            [
                'q' => 'A Beta(1,1) prior on θ is equivalent to which distribution?',
                'opts' => [
                    ['Normal(0,1)', false],
                    ['Uniform(0,1)', true],
                    ['Binomial(1,0.5)', false],
                    ['Exponential(1)', false],
                ],
            ],
            [
                'q' => 'You observe 7 successes and 3 failures with a Beta(1,1) prior. What is the posterior?',
                'opts' => [
                    ['Beta(7, 3)', false],
                    ['Beta(8, 4)', true],
                    ['Beta(7, 4)', false],
                    ['Normal(0.7, 0.1)', false],
                ],
            ],
            [
                'q' => 'The mode of Beta(α, β) is (α−1)/(α+β−2). For Beta(8,4), the mode is:',
                'opts' => [
                    ['0.636', false],
                    ['0.70', true],
                    ['0.75', false],
                    ['0.80', false],
                ],
            ],
            [
                'q' => 'What is the mean of a Beta(α, β) distribution?',
                'opts' => [
                    ['α / β', false],
                    ['α / (α + β)', true],
                    ['(α − 1) / (α + β − 2)', false],
                    ['β / (α + β)', false],
                ],
            ],

            // ── LIKELIHOOD & MODELS ───────────────────────────────────────
            [
                'q' => 'You model emails per hour as Poisson(λ). You observe counts [2,3,5]. The likelihood L(λ) is:',
                'opts' => [
                    ['P(λ | 2,3,5)', false],
                    ['P(2,3,5 | λ) = ∏ Poisson(xᵢ; λ)', true],
                    ['P(2) + P(3) + P(5) for each λ separately', false],
                    ['The normalising constant of the posterior', false],
                ],
            ],
            [
                'q' => 'Why is the log-likelihood often used instead of the raw likelihood?',
                'opts' => [
                    ['It always gives a larger numerical value', false],
                    ['Multiplying many small probabilities causes numerical underflow; log turns products into sums', true],
                    ['The log-likelihood is easier to interpret visually', false],
                    ['The posterior is always proportional to the log-likelihood', false],
                ],
            ],
            [
                'q' => 'The Binomial likelihood for k successes in n trials: P(k|θ) = C(n,k)θᵏ(1−θ)^(n−k). For n=5, k=2, θ=0.5, what is P(2|0.5)?',
                'opts' => [
                    ['0.25', false],
                    ['0.3125', true],
                    ['0.5', false],
                    ['0.1', false],
                ],
            ],
            [
                'q' => 'In a Binomial model with n=10 and θ=0.6, the most likely number of successes is:',
                'opts' => [
                    ['5', false],
                    ['6', true],
                    ['7', false],
                    ['10', false],
                ],
            ],
            [
                'q' => 'Which prior distribution is conjugate to the Binomial likelihood?',
                'opts' => [
                    ['Normal', false],
                    ['Gamma', false],
                    ['Beta', true],
                    ['Dirichlet', false],
                ],
            ],

            // ── PROBABILITY CONCEPTS ──────────────────────────────────────
            [
                'q' => 'What does it mean for two events A and B to be independent?',
                'opts' => [
                    ['P(A ∩ B) = P(A) + P(B)', false],
                    ['P(A|B) = P(A) — knowing B gives no information about A', true],
                    ['P(A) = P(B)', false],
                    ['A and B cannot occur together', false],
                ],
            ],
            [
                'q' => 'A fair die is rolled. What is P(even | number > 3)?',
                'opts' => [
                    ['1/2', false],
                    ['2/3', true],
                    ['1/3', false],
                    ['1/4', false],
                ],
            ],
            [
                'q' => 'Two mutually exclusive events: P(A)=0.3, P(B)=0.4. What is P(A ∪ B)?',
                'opts' => [
                    ['0.12', false],
                    ['0.58', false],
                    ['0.7', true],
                    ['1.0', false],
                ],
            ],
            [
                'q' => 'Using total probability: P(B|A)=0.8, P(B|¬A)=0.2, P(A)=0.3. What is P(B)?',
                'opts' => [
                    ['0.38', true],
                    ['0.80', false],
                    ['0.50', false],
                    ['0.24', false],
                ],
            ],
            [
                'q' => 'What is the key difference between frequentist and Bayesian probability?',
                'opts' => [
                    ['Frequentists use data; Bayesians do not', false],
                    ['Frequentists treat probability as long-run frequency; Bayesians treat it as a degree of belief', true],
                    ['Bayesians only work with discrete distributions', false],
                    ['They always agree on the result', false],
                ],
            ],
            [
                'q' => 'P(rain tomorrow) = 0.35. What is P(no rain tomorrow)?',
                'opts' => [
                    ['0.35', false],
                    ['0.65', true],
                    ['0.70', false],
                    ['Cannot be determined', false],
                ],
            ],

            // ── DISTRIBUTIONS ─────────────────────────────────────────────
            [
                'q' => 'X ~ N(μ=50, σ=10). Approximately what percentage of values fall within one standard deviation of the mean?',
                'opts' => [
                    ['50%', false],
                    ['68%', true],
                    ['95%', false],
                    ['99.7%', false],
                ],
            ],
            [
                'q' => 'If a Normal prior combines with a Normal likelihood to yield a Normal posterior, this is called:',
                'opts' => [
                    ['Sufficient statistics', false],
                    ['Conjugacy', true],
                    ['Central limit theorem', false],
                    ['Exchangeability', false],
                ],
            ],
            [
                'q' => 'For a Poisson likelihood, which prior is conjugate?',
                'opts' => [
                    ['Beta', false],
                    ['Normal', false],
                    ['Gamma', true],
                    ['Dirichlet', false],
                ],
            ],
            [
                'q' => 'The Gamma distribution is commonly used as a prior for which type of parameter?',
                'opts' => [
                    ['Probabilities bounded between 0 and 1', false],
                    ['Positive rate or precision parameters', true],
                    ['Integer-valued count data', false],
                    ['Parameters ranging from −∞ to +∞', false],
                ],
            ],
            [
                'q' => 'After observing 10 successes and 0 failures with a Uniform prior, the MAP estimate of θ is:',
                'opts' => [
                    ['0.5', false],
                    ['0.9', false],
                    ['1.0', true],
                    ['0.91', false],
                ],
            ],

            // ── CREDIBLE INTERVALS ────────────────────────────────────────
            [
                'q' => 'A 95% Bayesian credible interval [0.4, 0.8] for θ means:',
                'opts' => [
                    ['There is a 95% probability that θ lies between 0.4 and 0.8', true],
                    ['95% of data points fall between 0.4 and 0.8', false],
                    ['95 out of 100 repeated intervals would contain θ', false],
                    ['The true θ is exactly 0.6', false],
                ],
            ],
            [
                'q' => 'How does a Bayesian credible interval differ from a frequentist confidence interval?',
                'opts' => [
                    ['They are identical in interpretation', false],
                    ['A credible interval makes a direct probability statement about the parameter; a confidence interval does not', true],
                    ['Confidence intervals are always wider', false],
                    ['Credible intervals require no data', false],
                ],
            ],
            [
                'q' => 'The Highest Density Interval (HDI) is defined as:',
                'opts' => [
                    ['The interval from the 2.5th to 97.5th percentile', false],
                    ['The narrowest interval containing a specified probability mass of the posterior', true],
                    ['The range between prior mean and posterior mean', false],
                    ['The interval where the likelihood exceeds the prior', false],
                ],
            ],

            // ── BAYESIAN UPDATING ─────────────────────────────────────────
            [
                'q' => 'Start with Beta(2,2). Observe 5 successes and 3 failures. What is the posterior?',
                'opts' => [
                    ['Beta(5, 3)', false],
                    ['Beta(7, 5)', true],
                    ['Beta(5, 5)', false],
                    ['Beta(10, 8)', false],
                ],
            ],
            [
                'q' => 'Start with Beta(7,5). Observe 2 more successes and 4 failures. New posterior?',
                'opts' => [
                    ['Beta(9, 9)', true],
                    ['Beta(9, 11)', false],
                    ['Beta(2, 4)', false],
                    ['Beta(7, 9)', false],
                ],
            ],
            [
                'q' => 'As the amount of data grows, the posterior distribution generally:',
                'opts' => [
                    ['Becomes more spread out', false],
                    ['Becomes more concentrated around the true parameter value', true],
                    ['Moves toward the prior regardless of data', false],
                    ['Becomes bimodal', false],
                ],
            ],
            [
                'q' => 'With a very strong prior and very little data, the posterior is:',
                'opts' => [
                    ['Dominated by the data', false],
                    ['Dominated by the prior', true],
                    ['Equal to the likelihood', false],
                    ['Uniformly distributed', false],
                ],
            ],
            [
                'q' => 'What is a "conjugate prior"?',
                'opts' => [
                    ['A prior that equals the likelihood function', false],
                    ['A prior that yields a posterior in the same distribution family when combined with a specific likelihood', true],
                    ['A prior used exclusively for Gaussian data', false],
                    ['A prior that is always uniform', false],
                ],
            ],

            // ── INTERPRETATION ────────────────────────────────────────────
            [
                'q' => 'The posterior predictive distribution answers which question?',
                'opts' => [
                    ['What is the most likely parameter value?', false],
                    ['What data values would be expected for a new observation, given the posterior over parameters?', true],
                    ['What is the probability of the null hypothesis?', false],
                    ['Which model has the lowest AIC?', false],
                ],
            ],
            [
                'q' => 'The Bayes Factor BF₁₀ = P(data|H₁) / P(data|H₀) = 10 means:',
                'opts' => [
                    ['H₀ is 10 times more likely than H₁', false],
                    ['The data are 10 times more probable under H₁ than H₀', true],
                    ['There is a 90% probability that H₁ is true', false],
                    ['H₁ must be accepted at the 5% significance level', false],
                ],
            ],
            [
                'q' => 'You have posterior samples [0.3, 0.5, 0.7, 0.4, 0.6]. What is the posterior mean?',
                'opts' => [
                    ['0.5', true],
                    ['0.6', false],
                    ['0.45', false],
                    ['0.55', false],
                ],
            ],
            [
                'q' => 'Which summary statistic of the posterior coincides with the mean in a symmetric distribution?',
                'opts' => [
                    ['Mode only', false],
                    ['Median only', false],
                    ['Both median and mean coincide for symmetric posteriors', true],
                    ['Variance', false],
                ],
            ],

            // ── MODEL CONCEPTS ────────────────────────────────────────────
            [
                'q' => 'A generative model in Bayesian analysis describes:',
                'opts' => [
                    ['A model that generates only positive outputs', false],
                    ['A probabilistic model that describes how data are produced from parameters', true],
                    ['Any model that outperforms classical regression', false],
                    ['A model with no priors on parameters', false],
                ],
            ],
            [
                'q' => 'Posterior predictive checking involves:',
                'opts' => [
                    ['Comparing the prior to the posterior directly', false],
                    ['Simulating data from the posterior and comparing it to the observed data to assess model fit', true],
                    ['Computing the Bayes Factor between two models', false],
                    ['Setting a threshold on the credible interval', false],
                ],
            ],
            [
                'q' => 'The prior predictive distribution is used to:',
                'opts' => [
                    ['Update the prior after observing data', false],
                    ['Generate data from the model before observing any actual data, to check model reasonableness', true],
                    ['Compute the marginal likelihood numerically', false],
                    ['Estimate the posterior mean directly', false],
                ],
            ],
            [
                'q' => 'In a hierarchical Bayesian model, hyperparameters are:',
                'opts' => [
                    ['Parameters fixed by the analyst before any analysis', false],
                    ['Parameters governing the prior distributions of lower-level parameters', true],
                    ['Outputs of the posterior distribution', false],
                    ['Constant values set to zero by default', false],
                ],
            ],
            [
                'q' => 'The Bayesian Information Criterion (BIC) penalises model complexity. Bayesian model selection via the marginal likelihood also penalises:',
                'opts' => [
                    ['Model complexity implicitly through the prior', true],
                    ['The number of data points', false],
                    ['The posterior mean only', false],
                    ['Non-Gaussian posteriors exclusively', false],
                ],
            ],
            [
                'q' => 'Which is a disadvantage of a highly informative prior with little supporting evidence?',
                'opts' => [
                    ['It makes the posterior wider', false],
                    ['It can bias results if the prior is wrong and the dataset is small', true],
                    ['It always leads to faster computation', false],
                    ['It eliminates the need for a likelihood function', false],
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

        $this->command->info("Done! 50 questions seeded for Module 11 - Bayesian Data Analysis (University Student).");
    }
}