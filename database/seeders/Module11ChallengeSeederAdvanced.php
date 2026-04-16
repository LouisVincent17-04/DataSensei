<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module11ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 11 — Introduction to Bayesian Data Analysis (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Bayesian Data Analysis',
            'description'           => 'Tackle advanced Bayesian problems — debug probabilistic code, reason about MCMC pathologies, work through hierarchical models, and optimize posterior inference pipelines. Deep understanding and debugging skills required.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1500,
            'order_index'           => 11,
        ]);

        $this->command->info("Seeding 30 advanced questions...");

        $qaData = [

            // ── CODE DEBUGGING: MCMC ──────────────────────────────────────
            [
                'q' => "The following Python snippet implements a Metropolis-Hastings sampler. Identify the bug:\n\n```python\nimport numpy as np\n\ndef log_posterior(theta):\n    return -0.5 * theta**2  # Standard normal prior, no data\n\ntheta = 0.0\nsamples = []\nfor _ in range(1000):\n    proposal = theta + np.random.normal(0, 1)\n    log_ratio = log_posterior(proposal) - log_posterior(theta)\n    if np.log(np.random.uniform()) < log_ratio:\n        theta = proposal\n    # BUG IS HERE\n    samples.append(theta)\n```\n\nWhat is wrong with this code?",
                'opts' => [
                    ['The proposal distribution should use uniform noise, not normal noise', false],
                    ['There is no bug — the code is correct', false],
                    ['samples.append(theta) is inside the loop but should be outside it', false],
                    ['Nothing is wrong — but burn-in samples are not discarded before using `samples`', true],
                ],
            ],
            [
                'q' => "The following code computes a posterior using conjugate updates but gives wrong results:\n\n```python\nalpha, beta = 1, 1  # Beta(1,1) prior\ndata = [1, 0, 1, 1, 0, 1]  # 1=success, 0=failure\n\nfor obs in data:\n    if obs == 1:\n        alpha += 1\n    if obs == 0:\n        alpha += 1  # BUG\n\nprint(f'Posterior: Beta({alpha}, {beta})')\n```\n\nWhat is the bug?",
                'opts' => [
                    ['The loop should use `while` not `for`', false],
                    ['The failure case increments alpha instead of beta', true],
                    ['The prior Beta(1,1) is incorrect for this data', false],
                    ['The data list should contain floats, not integers', false],
                ],
            ],
            [
                'q' => "Review this PyMC model and identify the issue:\n\n```python\nimport pymc as pm\n\nwith pm.Model() as model:\n    mu = pm.Normal('mu', mu=0, sigma=10)\n    sigma = pm.Normal('sigma', mu=0, sigma=1)  # BUG\n    y_obs = pm.Normal('y_obs', mu=mu, sigma=sigma, observed=data)\n    trace = pm.sample(1000)\n```",
                'opts' => [
                    ['mu should use a Beta prior, not Normal', false],
                    ['sigma is given a Normal prior, which allows negative values — it should use HalfNormal or Exponential', true],
                    ['y_obs should use a Beta distribution', false],
                    ['pm.sample should have 2000 draws minimum', false],
                ],
            ],
            [
                'q' => "A PyMC model is sampling extremely slowly and R-hat values are all > 1.1. Which of the following is the MOST likely cause?\n\n```python\nwith pm.Model():\n    mu = pm.Uniform('mu', lower=-1000, upper=1000)\n    sigma = pm.Uniform('sigma', lower=0, upper=1000)\n    obs = pm.Normal('obs', mu=mu, sigma=sigma, observed=data)\n    trace = pm.sample(2000, tune=500)\n```",
                'opts' => [
                    ['The data variable is not defined', false],
                    ['Overly wide uniform priors create a large, difficult-to-explore parameter space', true],
                    ['tune=500 is too many tuning steps', false],
                    ['Normal observation model is incorrect for this type of data', false],
                ],
            ],

            // ── HIERARCHICAL MODELS ───────────────────────────────────────
            [
                'q' => "In a hierarchical model for 8 schools:\n\n```\nμ_global ~ Normal(0, 10)     # hyperprior\nτ ~ HalfNormal(5)            # hyperprior\nθ_j ~ Normal(μ_global, τ)   # school-level effects\ny_j ~ Normal(θ_j, σ_j)      # observations\n```\n\nWhat does a small estimated τ (close to 0) imply?",
                'opts' => [
                    ['The schools have very different treatment effects', false],
                    ['The schools are very similar — partial pooling toward the global mean is strong', true],
                    ['The model has failed to converge', false],
                    ['The hyperprior on μ_global is too wide', false],
                ],
            ],
            [
                'q' => "The 'eight schools' problem is a classic example of hierarchical Bayesian modeling. In the non-centered parameterization:\nθ_j = μ + τ × η_j, where η_j ~ Normal(0, 1)\n\nWhy is this reparameterization preferred over the centered form θ_j ~ Normal(μ, τ)?",
                'opts' => [
                    ['It produces different posterior means', false],
                    ['It eliminates the correlation between τ and θ_j, improving MCMC geometry', true],
                    ['It removes the need for a hyperprior on τ', false],
                    ['It makes the model non-hierarchical', false],
                ],
            ],
            [
                'q' => "In a hierarchical model, 'complete pooling' means treating all groups identically (one shared θ), and 'no pooling' means treating each group completely independently. Hierarchical ('partial pooling') models:",
                'opts' => [
                    ['Are equivalent to complete pooling when τ → 0', false],
                    ['Adaptively borrow strength across groups — shrinking estimates toward the group mean', true],
                    ['Always produce wider credible intervals than no-pooling', false],
                    ['Cannot be estimated using MCMC', false],
                ],
            ],

            // ── VARIATIONAL INFERENCE ─────────────────────────────────────
            [
                'q' => "Variational Inference (VI) approximates the posterior by:",
                'opts' => [
                    ['Drawing exact samples from the posterior using MCMC', false],
                    ['Optimizing a simpler distribution q(θ) to be as close to P(θ|data) as possible', true],
                    ['Computing the posterior analytically using conjugate priors', false],
                    ['Bootstrapping the data to estimate posterior uncertainty', false],
                ],
            ],
            [
                'q' => "The Evidence Lower Bound (ELBO) in variational inference is maximized. The ELBO is:\nELBO = E_q[log P(data,θ)] − E_q[log q(θ)]\n\nMaximizing the ELBO is equivalent to minimizing:",
                'opts' => [
                    ['The log likelihood', false],
                    ['The KL divergence KL(q(θ) || P(θ|data))', true],
                    ['The posterior variance', false],
                    ['The number of model parameters', false],
                ],
            ],
            [
                'q' => "A key limitation of mean-field variational inference compared to full MCMC is:",
                'opts' => [
                    ['It is slower to compute', false],
                    ['It assumes posterior parameters are independent, potentially underestimating uncertainty', true],
                    ['It cannot handle continuous parameters', false],
                    ['It requires more data than MCMC', false],
                ],
            ],

            // ── POSTERIOR PREDICTIVE CHECKS ───────────────────────────────
            [
                'q' => "A posterior predictive check (PPC) works by:\n\n1. Sample θ from the posterior P(θ|data)\n2. Simulate new data ỹ from P(ỹ|θ)\n3. Compare ỹ to observed data\n\nThe purpose of this procedure is to:",
                'opts' => [
                    ['Compute the Bayes Factor between two models', false],
                    ['Assess whether the model generates data consistent with observations — model checking', true],
                    ['Update the prior using simulated data', false],
                    ['Replace MCMC with a faster simulation method', false],
                ],
            ],
            [
                'q' => "A PPC reveals that simulated datasets have much lower variance than the observed data. This indicates:",
                'opts' => [
                    ['The MCMC chain has not converged', false],
                    ['The model is underdispersed — it underestimates variability in the data', true],
                    ['The prior is too informative', false],
                    ['The posterior is too wide', false],
                ],
            ],

            // ── BAYESIAN REGRESSION ADVANCED ──────────────────────────────
            [
                'q' => "In Bayesian logistic regression with a Normal(0, 1) prior on coefficients:\n\n```python\nwith pm.Model():\n    beta = pm.Normal('beta', mu=0, sigma=1, shape=X.shape[1])\n    p = pm.math.sigmoid(pm.math.dot(X, beta))\n    y_obs = pm.Bernoulli('y_obs', p=p, observed=y)\n```\n\nThe Normal(0, 1) prior on beta acts as which regularization?",
                'opts' => [
                    ['L1 (Lasso) regularization', false],
                    ['L2 (Ridge) regularization', true],
                    ['Elastic Net regularization', false],
                    ['No regularization', false],
                ],
            ],
            [
                'q' => "In the same logistic regression model, if you replace Normal(0, 1) with Laplace(0, 1) prior on beta, this is equivalent to:",
                'opts' => [
                    ['Ridge regression (L2)', false],
                    ['Lasso regression (L1) — promoting sparse solutions', true],
                    ['No regularization', false],
                    ['Elastic Net', false],
                ],
            ],
            [
                'q' => "A Bayesian logistic regression gives a posterior predictive probability of 0.72 for a new observation, with a 95% credible interval of [0.55, 0.89]. What is the most accurate interpretation?",
                'opts' => [
                    ['The prediction is definitely 72% accurate', false],
                    ['The point estimate is 0.72 but there is meaningful uncertainty between 55%-89%', true],
                    ['The model rejected the null hypothesis at p=0.05', false],
                    ['The prior was set to 0.72', false],
                ],
            ],

            // ── MODEL COMPARISON & SELECTION ──────────────────────────────
            [
                'q' => "The Leave-One-Out Cross-Validation (LOO-CV) estimate in Bayesian analysis is preferred over WAIC because:",
                'opts' => [
                    ['LOO-CV always gives lower values', false],
                    ['LOO-CV is more robust when posterior distributions have heavy tails (high Pareto-k values)', true],
                    ['LOO-CV uses the prior, not the posterior', false],
                    ['LOO-CV penalizes model complexity less harshly', false],
                ],
            ],
            [
                'q' => "When using ArviZ's `az.compare()`, models are ranked by their 'elpd_loo' score. A higher elpd_loo indicates:",
                'opts' => [
                    ['Worse out-of-sample predictive performance', false],
                    ['Better expected log predictive density — better out-of-sample fit', true],
                    ['More model parameters', false],
                    ['Greater posterior uncertainty', false],
                ],
            ],
            [
                'q' => "A Bayes Factor of BF₁₂ = 150 comparing Model 1 to Model 2 indicates (using Jeffreys scale):",
                'opts' => [
                    ['Moderate evidence for Model 1', false],
                    ['Decisive evidence for Model 1 over Model 2', true],
                    ['Evidence for Model 2', false],
                    ['Inconclusive evidence', false],
                ],
            ],

            // ── OPTIMIZATION & DEBUGGING ──────────────────────────────────
            [
                'q' => "Your MCMC trace shows the sampler is 'stuck' — the chain barely moves and accepts very few proposals. The most likely fix is:",
                'opts' => [
                    ['Increase the number of samples', false],
                    ['Reduce the proposal step size (or re-parameterize the model)', true],
                    ['Switch from NUTS to Metropolis-Hastings', false],
                    ['Remove the prior from the model', false],
                ],
            ],
            [
                'q' => "The No-U-Turn Sampler (NUTS) is preferred over standard Metropolis-Hastings for high-dimensional posteriors because:",
                'opts' => [
                    ['NUTS is easier to implement', false],
                    ['NUTS uses gradient information to make large, efficient moves without manual step-size tuning', true],
                    ['NUTS does not require a proposal distribution', false],
                    ['NUTS always accepts proposals', false],
                ],
            ],
            [
                'q' => "Divergences in NUTS sampling (flagged by PyMC) indicate:\n\nA) The sampler explored regions of very high curvature where numerical integration breaks down\nB) The model is perfectly identified\n\nWhat should you do when you see many divergences?",
                'opts' => [
                    ['Ignore them — divergences are normal', false],
                    ['Reparameterize the model (e.g., use non-centered parameterization) to improve geometry', true],
                    ['Increase the target_accept parameter to 1.0', false],
                    ['Decrease the number of tuning steps', false],
                ],
            ],

            // ── EDGE CASES & IDENTIFIABILITY ──────────────────────────────
            [
                'q' => "A model is 'non-identifiable' when:\n\nMultiple different parameter values produce the exact same likelihood for all possible datasets.\n\nIn practice this means:",
                'opts' => [
                    ['The model is too simple and needs more parameters', false],
                    ['The posterior will be very flat or multimodal — MCMC will explore many parameter combinations equally', true],
                    ['The prior dominates the posterior completely', false],
                    ['The data has missing values', false],
                ],
            ],
            [
                'q' => "In a mixture model with K components, swapping the labels of components (e.g., component 1 ↔ component 2) produces the same likelihood. This is called:",
                'opts' => [
                    ['Non-convergence', false],
                    ['Label switching — a form of non-identifiability that creates multimodal posteriors', true],
                    ['Overfitting', false],
                    ['A prior specification error', false],
                ],
            ],
            [
                'q' => "To handle label switching in Bayesian mixture models, a common approach is to impose an ordering constraint, such as μ₁ < μ₂ < ... < μ_K. This is achieved by:",
                'opts' => [
                    ['Using a uniform prior on all parameters', false],
                    ['Applying an ordered transformation or using pm.math.sort in the model definition', true],
                    ['Running multiple chains and averaging', false],
                    ['Setting the number of components K = 1', false],
                ],
            ],

            // ── ADVANCED POSTERIOR SUMMARIES ──────────────────────────────
            [
                'q' => "The Highest Density Interval (HDI) differs from a symmetric quantile-based credible interval in that:",
                'opts' => [
                    ['The HDI always has the same width as the quantile interval', false],
                    ['The HDI is the shortest interval containing the specified probability mass', true],
                    ['The HDI is centered at the posterior mean', false],
                    ['The HDI requires the posterior to be normally distributed', false],
                ],
            ],
            [
                'q' => "For a skewed posterior, the 95% HDI compared to the equal-tail 95% credible interval will be:",
                'opts' => [
                    ['Wider on both sides', false],
                    ['Shifted toward the region of highest posterior density — not necessarily symmetric around the mean', true],
                    ['Identical to the equal-tail interval', false],
                    ['Always narrower on the left side', false],
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

        $this->command->info("✅ Done! 30 questions seeded for Module 11 — Introduction to Bayesian Data Analysis (Advanced).");
    }
}