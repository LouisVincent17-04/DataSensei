<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module11ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 11 — Bayesian Data Analysis (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Bayesian Data Analysis',
            'description'           => 'Solve multi-step Bayesian inference problems, work through conjugate update derivations, interpret MCMC output, and apply Bayesian regression and model comparison concepts.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 700,
            'order_index'           => 1,
        ]);

        $this->command->info("Seeding 50 intermediate Bayesian analysis questions...");

        $qaData = [

            // ── MULTI-STEP BAYES' THEOREM ─────────────────────────────────
            [
                'q' => "Factory: Machine A produces 60% of parts, B produces 40%. Defect rates: P(defect|A)=0.02, P(defect|B)=0.05. A part is defective.\n\nP(defect) = 0.60×0.02 + 0.40×0.05\nP(A|defect) = (0.02×0.60) / P(defect)\n\nWhich answer is correct?",
                'opts' => [
                    ['P(defect)=0.032, P(A|defect)≈0.375', true],
                    ['P(defect)=0.07, P(A|defect)=0.60', false],
                    ['P(defect)=0.032, P(A|defect)≈0.625', false],
                    ['P(defect)=0.02, P(A|defect)=0.50', false],
                ],
            ],
            [
                'q' => "Three hypotheses: θ∈{0.3, 0.5, 0.7}, equal priors 1/3. Observe 4 heads in 5 flips. Binomial likelihood P(4H|θ)=C(5,4)×θ⁴×(1−θ). Which θ has the highest posterior probability?",
                'opts' => [
                    ['θ = 0.3', false],
                    ['θ = 0.5', false],
                    ['θ = 0.7', true],
                    ['All three are equally likely', false],
                ],
            ],
            [
                'q' => "Sequential updating: Start Beta(1,1). Round 1: 3H,1T → Beta(4,2). Round 2: 1H,3T → Beta(5,5). Posterior mean of θ after Round 2?",
                'opts' => [
                    ['0.5', true],
                    ['0.6', false],
                    ['0.45', false],
                    ['0.55', false],
                ],
            ],
            [
                'q' => "Medical screening: sensitivity=0.90, specificity=0.85, prevalence=0.05.\n\nP(+) = P(+|D)×P(D) + P(+|¬D)×P(¬D)\n\nWhat is P(disease | positive test)? (Round to two decimal places.)",
                'opts' => [
                    ['0.90', false],
                    ['0.24', true],
                    ['0.50', false],
                    ['0.05', false],
                ],
            ],

            // ── CONJUGATE UPDATES ─────────────────────────────────────────
            [
                'q' => "Prior: θ ~ Beta(2, 3). Observe n=20 Bernoulli trials: 14 successes, 6 failures. Correct posterior?",
                'opts' => [
                    ['Beta(16, 9)', true],
                    ['Beta(14, 6)', false],
                    ['Beta(12, 9)', false],
                    ['Beta(14, 9)', false],
                ],
            ],
            [
                'q' => "Prior: λ ~ Gamma(3, 1) (rate param). Observe n=5 Poisson counts summing to 20. Posterior = Gamma(α+Σxᵢ, β+n). What is it?",
                'opts' => [
                    ['Gamma(23, 6)', true],
                    ['Gamma(20, 5)', false],
                    ['Gamma(3, 6)', false],
                    ['Gamma(23, 1)', false],
                ],
            ],
            [
                'q' => "Prior: μ ~ N(0, 1). Observe x̄=2 from n=4 with known σ²=4.\n\nPosterior mean = (μ₀/σ₀² + n·x̄/σ²) / (1/σ₀² + n/σ²)\n\nWhat is the posterior mean?",
                'opts' => [
                    ['1.0', false],
                    ['1.6', true],
                    ['2.0', false],
                    ['0.5', false],
                ],
            ],
            [
                'q' => "Posterior variance: σ_post² = 1/(1/σ₀² + n/σ²). With σ₀²=1, n=4, σ²=4. What is σ_post²?",
                'opts' => [
                    ['0.5', true],
                    ['1.0', false],
                    ['0.25', false],
                    ['2.0', false],
                ],
            ],

            // ── POSTERIOR SUMMARIES ────────────────────────────────────────
            [
                'q' => "Beta(8,4): mean=α/(α+β), mode=(α−1)/(α+β−2), variance=αβ/[(α+β)²(α+β+1)]. Which set is correct?",
                'opts' => [
                    ['Mean=0.67, Mode=0.70, Var≈0.0152', true],
                    ['Mean=0.70, Mode=0.67, Var≈0.02', false],
                    ['Mean=0.67, Mode=0.64, Var≈0.05', false],
                    ['Mean=0.75, Mode=0.70, Var≈0.01', false],
                ],
            ],
            [
                'q' => "A posterior distribution is right-skewed. Which summary statistic is pulled toward the long tail?",
                'opts' => [
                    ['Median', false],
                    ['Mode', false],
                    ['Mean', true],
                    ['HDI lower bound', false],
                ],
            ],
            [
                'q' => "What property does the HDI guarantee that a central quantile interval does not?",
                'opts' => [
                    ['The HDI always contains the prior mean', false],
                    ['Every point inside the HDI has higher posterior density than any point outside it', true],
                    ['The HDI is always symmetric around the posterior mean', false],
                    ['The HDI has exactly half the width of the 95% confidence interval', false],
                ],
            ],

            // ── MCMC CONCEPTS ─────────────────────────────────────────────
            [
                'q' => "What is the purpose of the 'burn-in' period in MCMC?",
                'opts' => [
                    ['To increase the total number of samples', false],
                    ['To discard early samples before the chain has converged to the target distribution', true],
                    ['To set initial parameter values to zero', false],
                    ['To calibrate the likelihood function', false],
                ],
            ],
            [
                'q' => "The Gelman-Rubin statistic R̂ ≈ 1.0 indicates:",
                'opts' => [
                    ['The chain has not converged', false],
                    ['Multiple chains have mixed well and likely converged', true],
                    ['Exactly 100 samples are needed per chain', false],
                    ['The posterior is unimodal', false],
                ],
            ],
            [
                'q' => "High autocorrelation in an MCMC chain is problematic because:",
                'opts' => [
                    ['It makes the posterior multimodal', false],
                    ['Successive samples are correlated, reducing the effective sample size and slowing exploration', true],
                    ['It causes the posterior mean to shift toward zero', false],
                    ['It prevents the Metropolis-Hastings acceptance step', false],
                ],
            ],
            [
                'q' => "In the Metropolis-Hastings algorithm, a proposed sample θ* is accepted with probability min(1, r). What is r?",
                'opts' => [
                    ['r = P(θ*|data) / P(θ_current|data)', false],
                    ['r = [P(data|θ*)P(θ*) / q(θ*|θ)] / [P(data|θ)P(θ) / q(θ|θ*)]', true],
                    ['r = P(θ*) / P(θ_current)', false],
                    ['r = likelihood(θ*) / likelihood(θ_current)', false],
                ],
            ],
            [
                'q' => "Why does Hamiltonian Monte Carlo (HMC) outperform random-walk Metropolis-Hastings on high-dimensional problems?",
                'opts' => [
                    ['HMC does not require a likelihood function', false],
                    ['HMC uses gradient information to make larger, directed proposals, reducing random-walk behaviour', true],
                    ['HMC always accepts every proposal', false],
                    ['HMC eliminates burn-in', false],
                ],
            ],

            // ── BAYESIAN REGRESSION ───────────────────────────────────────
            [
                'q' => "Bayesian linear regression y=Xβ+ε with prior β~N(0,τ²I). A small τ² (tight prior) causes:",
                'opts' => [
                    ['Posterior coefficients are pulled toward zero (shrinkage)', true],
                    ['All coefficients are set exactly to zero', false],
                    ['The likelihood is ignored', false],
                    ['The model becomes equivalent to ridge regression with λ=0', false],
                ],
            ],
            [
                'q' => "Bayesian linear regression with a Normal prior on β and Normal likelihood produces which posterior for β?",
                'opts' => [
                    ['Beta distribution', false],
                    ['Normal distribution (conjugate result)', true],
                    ['Gamma distribution', false],
                    ['Uniform distribution', false],
                ],
            ],
            [
                'q' => "To predict the distribution of a new observation y* given x*, integrating over uncertainty in β, you use:",
                'opts' => [
                    ['The MAP estimate of β plugged into the regression equation', false],
                    ['The posterior predictive distribution p(y*|x*, data)', true],
                    ['The marginal likelihood of the training data', false],
                    ['The prior predictive distribution alone', false],
                ],
            ],

            // ── MODEL COMPARISON ──────────────────────────────────────────
            [
                'q' => "The marginal likelihood P(data|M) = ∫P(data|θ,M)P(θ|M)dθ automatically penalises complex models because:",
                'opts' => [
                    ['It divides the likelihood by the number of parameters', false],
                    ['Complex models spread probability mass over a larger parameter space, reducing average likelihood', true],
                    ['It adds a BIC-style penalty term', false],
                    ['It ignores the likelihood and uses only the prior', false],
                ],
            ],
            [
                'q' => "BF₂₁ = P(data|M₂)/P(data|M₁) = 3. According to Jeffreys' scale, this is:",
                'opts' => [
                    ['Strong evidence for M₂', false],
                    ['Anecdotal/weak evidence for M₂', true],
                    ['Decisive evidence for M₁', false],
                    ['No evidence either way', false],
                ],
            ],
            [
                'q' => "WAIC is preferred over DIC in modern Bayesian practice because:",
                'opts' => [
                    ['WAIC is always lower than DIC', false],
                    ['WAIC is based on the full posterior predictive distribution and is more reliable for singular models', true],
                    ['DIC requires MCMC while WAIC does not', false],
                    ['WAIC penalises models with fewer parameters', false],
                ],
            ],

            // ── PRIOR SENSITIVITY ─────────────────────────────────────────
            [
                'q' => "A sensitivity analysis in Bayesian modelling involves:",
                'opts' => [
                    ['Testing how the posterior changes when the prior is varied', true],
                    ['Removing outliers from the dataset', false],
                    ['Increasing MCMC chain length until R̂ < 1.01', false],
                    ['Comparing credible intervals to confidence intervals', false],
                ],
            ],
            [
                'q' => "Weakly informative priors (e.g., N(0,10) on regression coefficients) are recommended over flat priors because:",
                'opts' => [
                    ['Flat priors always produce improper posteriors', false],
                    ['They provide mild regularisation without overwhelming the data, improving sampler behaviour', true],
                    ['Weakly informative priors are always conjugate', false],
                    ['They guarantee the posterior is Normal', false],
                ],
            ],

            // ── CALCULATION PROBLEMS ───────────────────────────────────────
            [
                'q' => "Prior Beta(1,1). Observe 6H, 4T → Beta(7,5). 90% central credible interval: 5th pct≈0.37, 95th pct≈0.84. What is the interval?",
                'opts' => [
                    ['[0.37, 0.84]', true],
                    ['[0.40, 0.90]', false],
                    ['[0.50, 0.80]', false],
                    ['[0.30, 0.70]', false],
                ],
            ],
            [
                'q' => "Posterior for coefficient β₁ is approximately Normal(1.2, 0.3²). What is the approximate probability that β₁ > 0?",
                'opts' => [
                    ['About 50%', false],
                    ['About 84%', false],
                    ['Greater than 99.9%', true],
                    ['Exactly 95%', false],
                ],
            ],
            [
                'q' => "4 MCMC chains, each length 2000 (1000 burn-in discarded). ESS per chain = 250. Total ESS across all chains?",
                'opts' => [
                    ['4000', false],
                    ['1000', true],
                    ['8000', false],
                    ['250', false],
                ],
            ],
            [
                'q' => "Posterior samples [0.2, 0.8, 0.5, 0.6, 0.7, 0.3, 0.9, 0.4, 0.6, 0.5]. What proportion exceed 0.5?",
                'opts' => [
                    ['0.5', false],
                    ['0.6', true],
                    ['0.7', false],
                    ['0.4', false],
                ],
            ],
            [
                'q' => "Prior Gamma(3,2) (mean=1.5). Observe Poisson counts summing to 30 over n=10. Posterior = Gamma(33,12). Posterior mean?",
                'opts' => [
                    ['2.75', true],
                    ['3.0', false],
                    ['1.5', false],
                    ['30.0', false],
                ],
            ],

            // ── HIERARCHICAL MODELS ────────────────────────────────────────
            [
                'q' => "Exchangeability in a hierarchical model means:",
                'opts' => [
                    ['All groups have identical parameter values', false],
                    ['The joint distribution of group parameters is invariant to relabelling', true],
                    ['Parameters are sampled independently from the prior', false],
                    ['All hyperparameters are set to zero', false],
                ],
            ],
            [
                'q' => "Partial pooling in hierarchical models is a compromise between:",
                'opts' => [
                    ['Maximum likelihood and MAP estimation', false],
                    ['Complete pooling (one shared parameter) and no pooling (fully independent per-group estimates)', true],
                    ['Informative and non-informative priors', false],
                    ['Frequentist and Bayesian confidence intervals', false],
                ],
            ],
            [
                'q' => "In a hierarchical Beta-Binomial model, what is the main benefit of placing a prior on the hyperparameters (a,b)?",
                'opts' => [
                    ['It eliminates the need for MCMC', false],
                    ['The data inform the shape of the prior, allowing adaptive shrinkage across groups', true],
                    ['It always leads to a conjugate posterior', false],
                    ['It fixes the posterior mean at the grand mean', false],
                ],
            ],

            // ── PREDICTIVE DISTRIBUTIONS ──────────────────────────────────
            [
                'q' => "The posterior predictive distribution p(ỹ|y) is preferred over the plug-in predictive p(ỹ|θ̂) because:",
                'opts' => [
                    ['It is always computationally cheaper', false],
                    ['It propagates parameter uncertainty into the prediction, giving calibrated predictive intervals', true],
                    ['It gives point predictions only', false],
                    ['It ignores the likelihood', false],
                ],
            ],
            [
                'q' => "Using MCMC samples {θ⁽¹⁾,...,θ⁽ˢ⁾}, how do you approximate the posterior predictive distribution?",
                'opts' => [
                    ['Draw one sample ỹ ~ p(ỹ|θ̂_MAP)', false],
                    ['For each θ⁽ˢ⁾, draw ỹ⁽ˢ⁾ ~ p(ỹ|θ⁽ˢ⁾); the collection {ỹ⁽ˢ⁾} approximates the predictive', true],
                    ['Average all θ⁽ˢ⁾, then draw ỹ ~ p(ỹ|θ̄)', false],
                    ['Use the prior predictive instead', false],
                ],
            ],

            // ── IDENTIFIABILITY & DIAGNOSTICS ────────────────────────────
            [
                'q' => "A bimodal posterior distribution typically indicates:",
                'opts' => [
                    ['Excellent MCMC convergence', false],
                    ['Two competing explanations for the data that the prior does not resolve', true],
                    ['An overly diffuse prior', false],
                    ['The model is identifiable', false],
                ],
            ],
            [
                'q' => "Label switching in mixture models is a symptom of:",
                'opts' => [
                    ['High autocorrelation in MCMC chains', false],
                    ['Non-identifiability: the likelihood is symmetric under relabelling of components', true],
                    ['An improper prior', false],
                    ['Too many MCMC samples', false],
                ],
            ],
            [
                'q' => "Posterior predictive p-values are used to:",
                'opts' => [
                    ['Compute frequentist p-values from Bayesian models', false],
                    ['Assess whether observed data statistics fall within the range generated by the posterior predictive', true],
                    ['Test whether the prior is proper', false],
                    ['Replace the Bayes Factor in model comparison', false],
                ],
            ],

            // ── APPLIED PROBLEMS ──────────────────────────────────────────
            [
                'q' => "A/B test: A has 100 visits, 20 conversions; B has 100 visits, 30 conversions. Beta(1,1) priors → Beta(21,81) for A, Beta(31,71) for B. How do you estimate P(θ_B > θ_A) using Monte Carlo?",
                'opts' => [
                    ['Compute P(θ_B > θ_A) = P(θ_B) - P(θ_A)', false],
                    ['Draw S samples from each posterior; estimate as fraction of draws where θ_B sample > θ_A sample', true],
                    ['Compare posterior means with a z-test', false],
                    ['Compute the HDI overlap', false],
                ],
            ],
            [
                'q' => "Posterior for log-rate parameter β₁ is Normal(0.5, 0.2²). Posterior median of the rate ratio exp(β₁)?",
                'opts' => [
                    ['0.5', false],
                    ['exp(0.5) ≈ 1.65', true],
                    ['exp(0.25) ≈ 1.28', false],
                    ['1.0', false],
                ],
            ],
            [
                'q' => "Bayesian change-point detection models a change in rate at unknown time τ with a Uniform prior over the observation period. After observing data, the posterior concentrates around a specific time. This is:",
                'opts' => [
                    ['Conjugate updating', false],
                    ['Bayesian inference about a discrete latent variable', true],
                    ['Hierarchical modelling', false],
                    ['Prior predictive checking', false],
                ],
            ],

            // ── ADDITIONAL MULTI-STEP ─────────────────────────────────────
            [
                'q' => "Prior μ~N(0,9). Observe x̄=4 from n=9 with σ²=9. Posterior: μ_post=3.6, σ²_post=0.9. What is the posterior standard deviation?",
                'opts' => [
                    ['0.9', false],
                    ['3.0', false],
                    ['0.949', true],
                    ['1.0', false],
                ],
            ],
            [
                'q' => "Two-component mixture: posterior on mixing weight p concentrates near 0.3. Components: N(0,1) and N(5,1). What is the posterior predictive mean?",
                'opts' => [
                    ['2.5', false],
                    ['0.3×0 + 0.7×5 = 3.5', true],
                    ['0.7×0 + 0.3×5 = 1.5', false],
                    ['5.0', false],
                ],
            ],
            [
                'q' => "Dirichlet-Multinomial: K=3, prior Dir(1,1,1), observed counts (10,5,5). Posterior?",
                'opts' => [
                    ['Dir(10, 5, 5)', false],
                    ['Dir(11, 6, 6)', true],
                    ['Dir(1, 1, 1)', false],
                    ['Dir(10, 10, 10)', false],
                ],
            ],
            [
                'q' => "ESS ≈ N/(1+2ρ₁). If N=1000 and ρ₁=0.8, approximate ESS?",
                'opts' => [
                    ['200', false],
                    ['111', false],
                    ['125', true],
                    ['500', false],
                ],
            ],
            [
                'q' => "LOO-CV in Bayesian modelling estimates:",
                'opts' => [
                    ['The in-sample fit of the model', false],
                    ['The expected log predictive density for new observations, accounting for overfitting', true],
                    ['The Bayes Factor between two models', false],
                    ['The number of parameters needed for identifiability', false],
                ],
            ],
            [
                'q' => "Variational Inference (VI) approximates the posterior by:",
                'opts' => [
                    ['Drawing exact samples via MCMC', false],
                    ['Optimising a simpler distribution q(θ) to minimise KL divergence from the true posterior p(θ|data)', true],
                    ['Computing the posterior analytically using conjugacy', false],
                    ['Averaging the prior and the likelihood directly', false],
                ],
            ],
            [
                'q' => "Bayesian model averaging (BMA) produces predictions as a weighted average of multiple models. Weights are proportional to:",
                'opts' => [
                    ['The number of parameters in each model', false],
                    ['The posterior model probabilities, which depend on the marginal likelihood', true],
                    ['The MAP estimates of each model\'s parameters', false],
                    ['The MCMC chain length for each model', false],
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

        $this->command->info("Done! 50 questions seeded for Module 11 - Bayesian Data Analysis (Intermediate).");
    }
}