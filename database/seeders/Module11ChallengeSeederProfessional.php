<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module11ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 11 — Introduction to Bayesian Data Analysis (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Bayesian Data Analysis',
            'description'           => 'Real-world Bayesian system design, production edge cases, scalability, model governance, and cross-disciplinary integration. Problems reflect the complexity and ambiguity of professional data science practice.',
            'time_limit_seconds'    => 2400,
            'base_xp'               => 1000,
            'order_index'           => 1,
        ]);

        $this->command->info("Seeding 50 professional-level Bayesian analysis questions...");

        $qaData = [

            // ── PRODUCTION PIPELINE EDGE CASES ─────────────────────────────
            [
                'q' => "Your production Bayesian AB test pipeline samples from a Beta-Binomial posterior nightly. Over 30 days, the posterior on conversion rate for variant B has consistently shown 99%+ probability of beating control. However, the engineering team reports that variant B was accidentally rolled out to only mobile users — a segment that historically converts 40% higher than desktop. How should you handle this finding?",
                'opts' => [
                    ['Accept the result; higher conversion is the goal regardless of cause', false],
                    ['Invalidate the experiment; re-run with proper randomisation. Optionally, model the effect with a hierarchical model stratified by device type to separate the treatment effect from selection bias', true],
                    ['Apply a simple scaling factor of 0.6 to the posterior mean to correct for the bias', false],
                    ['Report the result with a note that the effect may be inflated', false],
                ],
            ],
            [
                'q' => "A real-time Bayesian anomaly detection system monitors server latency. The model uses a conjugate Normal-Gamma posterior updated online. After a datacenter migration, latency increases by 200ms permanently. The posterior credible interval no longer covers the new normal. What is the correct production response?",
                'opts' => [
                    ['Widen the prior variance to encompass all possible values permanently', false],
                    ['Implement a forgetting factor (discounted updates) or a change-point model that detects the structural break and resets the posterior; alert the on-call team immediately', true],
                    ['Retrain from scratch every hour using only the last 60 minutes of data', false],
                    ['Ignore the alert; posterior updates will self-correct eventually', false],
                ],
            ],
            [
                'q' => "You deploy a Bayesian recommendation model to production. After six months, you discover that the prior on user preferences was informed by historical data collected entirely from users aged 18–25. Your current user base is now 40% aged 50+. This is an example of:",
                'opts' => [
                    ['MCMC non-convergence due to a stale prior', false],
                    ['Prior-data conflict arising from distribution shift; the informative prior is misspecified for the new population, biasing posterior estimates', true],
                    ['Overfitting caused by too many hyperparameters', false],
                    ['Label switching in a mixture model', false],
                ],
            ],
            [
                'q' => "A Bayesian clinical trial uses sequential analysis: the trial stops early if P(θ_treatment > θ_control | data) > 0.99. A statistician raises a concern about 'optional stopping'. Under the Bayesian framework, is this a valid concern?",
                'opts' => [
                    ['Yes — Bayesian methods suffer from the same multiple-testing inflation as frequentist methods', false],
                    ['No — Bayesian methods obey the likelihood principle; inference is valid at any stopping point as long as the stopping rule does not depend on unobserved data. However, regulatory bodies may still require pre-registration and decision thresholds to be specified a priori', true],
                    ['Optional stopping is always invalid regardless of framework', false],
                    ['Yes — each interim analysis inflates the posterior probability by a fixed factor', false],
                ],
            ],

            // ── SCALABILITY & COMPUTATIONAL TRADE-OFFS ─────────────────────
            [
                'q' => "You need to fit a hierarchical Bayesian model to 50 million observations. Full MCMC is computationally infeasible. Rank the following approaches from most to least appropriate for production:\n\n1. Stochastic Variational Inference (SVI) with mini-batches\n2. Full NUTS MCMC on all data\n3. Approximate MCMC with subsampling (e.g., SGLD)\n4. MAP estimation with bootstrapped uncertainty",
                'opts' => [
                    ['2, 1, 3, 4', false],
                    ['1, 3, 4, 2', true],
                    ['4, 3, 2, 1', false],
                    ['3, 4, 1, 2', false],
                ],
            ],
            [
                'q' => "You use Gaussian Processes for spatial interpolation of environmental sensor data across 10,000 locations. Exact GP inference is O(N³). You decide to use a sparse GP with M=200 inducing points. Which consequence must you explicitly account for in your production system?",
                'opts' => [
                    ['The sparse GP always gives wider credible intervals than the exact GP', false],
                    ['The sparse GP is an approximation; predictive uncertainty may be underestimated in regions with few inducing points — inducing point placement and the inducing point count M must be validated against held-out data', true],
                    ['Sparse GPs cannot handle non-stationary kernels', false],
                    ['The marginal likelihood cannot be computed for sparse GPs', false],
                ],
            ],
            [
                'q' => "Your team is debating between ADVI (Automatic Differentiation Variational Inference) and NUTS for a production hierarchical model. ADVI converges in minutes; NUTS takes 8 hours. Which scenario would justify accepting the ADVI approximation?",
                'opts' => [
                    ['High-stakes medical diagnosis where uncertainty quantification must be exact', false],
                    ['A low-stakes product recommendation system where approximate posteriors suffice and retraining occurs daily, making NUTS impractical within the deployment window', true],
                    ['Any scenario where R̂ > 1.01 for NUTS', false],
                    ['Scenarios with fewer than 1000 data points', false],
                ],
            ],

            // ── MODEL GOVERNANCE & ETHICS ──────────────────────────────────
            [
                'q' => "A Bayesian credit scoring model is deployed to assess loan eligibility. Auditors find that the prior on default probability was set higher for applicants from certain ZIP codes, correlating with race due to historical redlining. What is the correct professional response?",
                'opts' => [
                    ['Retrain with a uniform prior across ZIP codes and add fairness constraints; document the prior specification process and conduct an impact assessment', true],
                    ['The prior is based on historical data and is therefore objective and unbiased', false],
                    ['Reduce the prior weight so the likelihood dominates', false],
                    ['Switch to a frequentist model to avoid the prior specification problem', false],
                ],
            ],
            [
                'q' => "In a Bayesian decision framework for medical diagnosis, you compute the expected utility of each action (treat vs. do not treat) as:\n\nEU(action) = Σ_θ P(θ|data) × U(action, θ)\n\nThe utility function assigns U(treat|disease) = +10, U(no treat|disease) = −100, U(treat|no disease) = −1, U(no treat|no disease) = 0. P(disease|data) = 0.08. Should the model recommend treating?",
                'opts' => [
                    ['No — P(disease|data) = 8% is too low to justify treatment', false],
                    ['Yes — EU(treat) = 0.08×10 + 0.92×(−1) = −0.12, EU(no treat) = 0.08×(−100) + 0.92×0 = −8. EU(treat) > EU(no treat)', true],
                    ['The model is indeterminate; a frequentist test is required', false],
                    ['Yes — EU(treat) = 0.08 × 10 = 0.8, which is positive', false],
                ],
            ],
            [
                'q' => "A Bayesian model predicting recidivism risk is used in parole decisions. The posterior credible interval for an individual is [0.3, 0.8]. A judge asks for a single risk score. What is the most professionally responsible way to communicate this result?",
                'opts' => [
                    ['Report the posterior mean (0.55) as the single risk score without caveats', false],
                    ['Report the posterior mean AND the credible interval, explicitly communicating the uncertainty; recommend that decision-makers receive training on interpreting probabilistic outputs', true],
                    ['Report the upper bound (0.8) to be conservative', false],
                    ['Refuse to provide a risk score; Bayesian models are not suitable for individual decisions', false],
                ],
            ],

            // ── ADVANCED EDGE CASES ────────────────────────────────────────
            [
                'q' => "Your Bayesian model returns an improper posterior — the posterior does not integrate to 1 (i.e., ∫p(θ|y)dθ = ∞). This is most likely caused by:",
                'opts' => [
                    ['Too many MCMC samples', false],
                    ['An improper prior (e.g., flat prior on an unbounded parameter) combined with a likelihood that does not provide enough information to make the product proper', true],
                    ['Using a conjugate prior', false],
                    ['Non-identifiability that can be fixed by adding more data', false],
                ],
            ],
            [
                'q' => "Cold posterior effect: In practice, Bayesian deep learning with standard priors often requires a 'tempered' posterior p(θ|y)^(1/T) with T < 1 to achieve good predictive performance. The most theoretically supported explanation for this phenomenon is:",
                'opts' => [
                    ['The prior is too informative, so tempering reduces its influence', false],
                    ['The likelihood is misspecified (e.g., model does not capture true data distribution), and tempering compensates by down-weighting the likelihood; it signals a need for better model specification rather than routine practice', true],
                    ['Neural network posteriors are always multimodal, requiring tempering to flatten them', false],
                    ['Tempering is mathematically equivalent to using a larger dataset', false],
                ],
            ],
            [
                'q' => "You observe that your Bayesian model's posterior predictive checks pass (simulated data matches observed data), yet the model performs poorly on a held-out test set. What is the most likely explanation?",
                'opts' => [
                    ['The MCMC sampler has not converged', false],
                    ['The posterior predictive check uses the same data for fitting and checking; the model has overfit the training data, and predictive checks on training data do not reveal poor generalisation', true],
                    ['The prior is too weak', false],
                    ['Bayesian models cannot generalise to new data by construction', false],
                ],
            ],

            // ── REAL-WORLD SYSTEM DESIGN ───────────────────────────────────
            [
                'q' => "Design a Bayesian bandit system for content personalisation at scale (10M users, 1000 content items). Which architecture is most appropriate?",
                'opts' => [
                    ['One global Beta posterior updated with all users\' interactions', false],
                    ['A hierarchical model: item-level Beta posteriors shrunk toward a global prior, updated incrementally via Thompson sampling, with periodic batch re-estimation of hyperparameters using type-II ML', true],
                    ['One separate Beta posterior per user per item (10B parameters, updated in real time)', false],
                    ['A frequentist bandit (UCB) is more appropriate at this scale', false],
                ],
            ],
            [
                'q' => "You are building a Bayesian forecasting system for supply chain demand. The model must: (1) quantify uncertainty, (2) incorporate prior business knowledge, (3) update daily with new sales data, (4) scale to 50,000 SKUs. Which framework is most suitable for production?",
                'opts' => [
                    ['Full MCMC with Stan, one model per SKU, run nightly', false],
                    ['A hierarchical time series model (e.g., Prophet-style or BSTS) with shared hyperparameters across SKUs, fit using variational inference or Kalman filtering for conjugate cases, enabling O(1) online updates per SKU', true],
                    ['A flat Bayesian regression with 50,000 independent parameters', false],
                    ['Classical ARIMA models with bootstrapped confidence intervals', false],
                ],
            ],
            [
                'q' => "In a Bayesian causal inference pipeline using propensity score models, you must estimate the Average Treatment Effect (ATE). A colleague suggests using the posterior mean of the propensity score as a point estimate. Why is this suboptimal?",
                'opts' => [
                    ['Propensity scores must always be estimated with logistic regression', false],
                    ['Using a point estimate discards posterior uncertainty in the propensity score; uncertainty should be propagated through the outcome model to give honest uncertainty intervals on the ATE', true],
                    ['The ATE cannot be computed from propensity scores', false],
                    ['Bayesian propensity scores always produce biased ATE estimates', false],
                ],
            ],

            // ── INTEGRATION WITH DOMAIN KNOWLEDGE ─────────────────────────
            [
                'q' => "A pharmacokinetics (PK) team wants to use Bayesian inference to estimate drug clearance rates from sparse patient data (3 blood draws per patient, 20 patients). The physical ODE model is:\n\ndC/dt = −k_e × C\n\nWhat is the professional Bayesian modelling approach?",
                'opts' => [
                    ['Fit a polynomial regression to the sparse data', false],
                    ['Use a Bayesian hierarchical ODE model: patient-level k_e drawn from a population prior; the ODE defines the likelihood structure. Use NUTS via Stan/PyMC for inference, leveraging the ODE solver within the probabilistic program', true],
                    ['Use a Gaussian Process prior on C(t) without incorporating the ODE', false],
                    ['Pool all 20 patients and fit a single k_e value', false],
                ],
            ],
            [
                'q' => "Bayesian optimisation (BO) uses a surrogate model (typically a GP) to optimise an expensive black-box function f(x). The acquisition function balances exploration and exploitation. In a production hyperparameter tuning system with 48 hours of GPU budget, which acquisition function is most appropriate?",
                'opts' => [
                    ['Probability of Improvement (PI) — maximises the chance of any improvement, avoiding exploration', false],
                    ['Expected Improvement (EI) — balances exploration and exploitation with a single tunable parameter; robust and well-understood in practice', true],
                    ['Thompson Sampling on the GP posterior, which is always optimal', false],
                    ['Upper Confidence Bound (UCB) with β=0 to be fully exploitative', false],
                ],
            ],
            [
                'q' => "A financial risk team uses a Bayesian Value-at-Risk (VaR) model. The posterior predictive distribution of daily returns has heavy tails. A regulator requires a 99% VaR estimate with a 95% credible interval around it. The production system should compute this as:",
                'opts' => [
                    ['The 1st percentile of the posterior predictive distribution', false],
                    ['For each posterior sample θ⁽ˢ⁾, compute the 1st percentile of p(return|θ⁽ˢ⁾); the collection of these percentiles gives the posterior over VaR, from which a median point estimate and 95% HDI can be reported', true],
                    ['The posterior mean of the return distribution minus 2.33 standard deviations', false],
                    ['The MLE-based VaR with bootstrap confidence intervals', false],
                ],
            ],

            // ── ADVANCED MODEL DIAGNOSTICS IN PRODUCTION ──────────────────
            [
                'q' => "Your production Bayesian model's PSIS-LOO computation flags 12% of observations as having Pareto k̂ > 0.7 (high influence points). What is the recommended professional action?",
                'opts' => [
                    ['Remove all flagged observations and refit', false],
                    ['Investigate flagged points for data quality issues, outliers, or model misspecification; consider robust likelihoods (Student-t), outlier models, or exact LOO for flagged points rather than PSIS approximation', true],
                    ['Switch to DIC which does not have this limitation', false],
                    ['Ignore the diagnostic; LOO is an approximation anyway', false],
                ],
            ],
            [
                'q' => "You notice that the posterior predictive distribution from your time series model shows calibration drift over time: early predictions are well-calibrated, but recent predictions are systematically overconfident. What is the most likely cause and fix?",
                'opts' => [
                    ['Burn-in was too short; run more MCMC iterations', false],
                    ['The model does not account for non-stationarity or concept drift; the variance of the data-generating process has changed. Fix: introduce time-varying volatility (e.g., stochastic volatility model) or a rolling window posterior update', true],
                    ['The posterior is multimodal; merge the modes', false],
                    ['The credible interval width should be reported instead of calibration', false],
                ],
            ],
            [
                'q' => "Model monitoring in a production Bayesian system should include which of the following as a primary signal for model degradation?",
                'opts' => [
                    ['R̂ values from initial training', false],
                    ['Continuous monitoring of posterior predictive p-values or proper scoring rules (e.g., log score, CRPS) on incoming production data compared to historical baselines', true],
                    ['Weekly manual inspection of the posterior mean', false],
                    ['The number of divergent transitions in the original training run', false],
                ],
            ],

            // ── ADVANCED STATISTICAL THEORY ───────────────────────────────
            [
                'q' => "Complete class theorem in Bayesian decision theory states that every admissible decision rule is a Bayes rule (or a limit of Bayes rules). The practical implication for data scientists is:",
                'opts' => [
                    ['All decision rules are equivalent', false],
                    ['Optimal decision-making under a well-specified loss function is Bayesian; specifying an appropriate prior is equivalent to specifying an admissible estimator', true],
                    ['Frequentist methods are always inadmissible', false],
                    ['The Bayes rule only applies to discrete decision problems', false],
                ],
            ],
            [
                'q' => "De Finetti's representation theorem states that an exchangeable sequence of binary random variables (X₁, X₂,...) has a unique representation as:\n\nP(X₁=x₁,...,Xₙ=xₙ) = ∫ ∏ θˣⁱ(1−θ)^(1−xᵢ) dμ(θ)\n\nThe operational significance for Bayesian practice is:",
                'opts' => [
                    ['Bayesian inference is only valid for binary data', false],
                    ['Assuming exchangeability (not IID) is sufficient to justify using a prior over θ; the Bayesian model is the unique coherent representation of exchangeable beliefs', true],
                    ['The prior μ(θ) must always be uniform by this theorem', false],
                    ['Exchangeability implies independence of all Xᵢ', false],
                ],
            ],
            [
                'q' => "Robust Bayesian analysis considers a class of priors Γ = {priors satisfying some constraint} and reports ranges of posterior quantities over Γ. When should a professional data scientist use robust Bayesian analysis rather than a single prior?",
                'opts' => [
                    ['Always — a single prior is never justified', false],
                    ['When prior specification is genuinely uncertain or contested (e.g., in policy analysis, regulatory submissions, or adversarial settings) and the robustness of conclusions to prior choice must be demonstrated', true],
                    ['Only when data are sparse', false],
                    ['When MCMC fails to converge', false],
                ],
            ],

            // ── CROSS-DISCIPLINARY INTEGRATION ─────────────────────────────
            [
                'q' => "A Bayesian network (DAG-based probabilistic graphical model) encodes conditional independence structure. In a production system, which scenario most justifies using a Bayesian network over a standard Bayesian regression?",
                'opts' => [
                    ['When the number of features exceeds 100', false],
                    ['When the data-generating process has known causal or conditional independence structure that should be encoded explicitly (e.g., medical diagnosis with documented disease-symptom dependencies)', true],
                    ['When the outcome variable is continuous', false],
                    ['When MCMC is too slow', false],
                ],
            ],
            [
                'q' => "In a multi-armed bandit problem with non-stationary rewards, Thompson Sampling with standard Beta-Binomial posteriors underperforms because:",
                'opts' => [
                    ['Thompson Sampling is only valid for Gaussian rewards', false],
                    ['The posterior accumulates evidence from the past, making it slow to adapt to reward changes; a sliding window, discounted updates, or change-point detection layer is needed', true],
                    ['The Beta distribution cannot model binary rewards', false],
                    ['Thompson Sampling always requires informative priors', false],
                ],
            ],
            [
                'q' => "Bayesian deep learning via Monte Carlo Dropout approximates the posterior over weights. A critical limitation in production is:",
                'opts' => [
                    ['Monte Carlo Dropout cannot be applied to convolutional networks', false],
                    ['The dropout-as-inference approximation corresponds to a specific (often poorly specified) variational distribution; uncertainty estimates may be miscalibrated and should be validated with proper scoring rules before deployment', true],
                    ['It requires 10× more compute than standard inference', false],
                    ['The posterior samples cannot be averaged for prediction', false],
                ],
            ],

            // ── EDGE CASE CALCULATIONS ─────────────────────────────────────
            [
                'q' => "In a high-dimensional Bayesian regression (p >> n, e.g., p=5000 predictors, n=200 observations), a horseshoe prior is used. The posterior is sampled with NUTS and R̂ values are all 1.0, but divergent transitions are >10%. What is the correct diagnosis?",
                'opts' => [
                    ['R̂ < 1.1 confirms convergence; divergences are acceptable', false],
                    ['Divergent transitions indicate regions of high posterior curvature the sampler cannot explore, even if R̂ appears acceptable. Reparameterise (non-centred horseshoe), increase adapt_delta to 0.99, and investigate divergent parameter regions', true],
                    ['Reduce the model to the top 200 predictors to fix divergences', false],
                    ['Divergent transitions are a known limitation of NUTS on high-dimensional problems and cannot be resolved', false],
                ],
            ],
            [
                'q' => "You are computing the Expected Calibration Error (ECE) for your Bayesian classifier. The model outputs posterior predictive probabilities. Your ECE is 0.02 (excellent calibration on the test set). A colleague argues the model is ready for deployment. What additional check should be mandatory before deployment?",
                'opts' => [
                    ['Compute the Bayes Factor against the null model', false],
                    ['Evaluate calibration on the deployment distribution, not just the test set. Distribution shift between test and production populations can degrade calibration; also evaluate ECE stratified by subgroups for fairness', true],
                    ['Verify that MCMC R̂ < 1.01 on production data', false],
                    ['Ensure the prior predictive distribution matches the test set distribution', false],
                ],
            ],
            [
                'q' => "A production hierarchical Bayesian model has 500 groups, each with n_g observations. Group sizes range from 2 to 5000. For groups with n_g = 2, the posterior for that group's parameter will be:",
                'opts' => [
                    ['Identical to the MLE for that group', false],
                    ['Strongly shrunk toward the population-level hyperparameter estimate due to the small sample; the prior (hyperparameter) dominates — this is the desired partial-pooling behaviour, but should be validated to ensure the hyperprior is well-specified', true],
                    ['Uninformative — the model has no information for these groups', false],
                    ['Equal to the population mean regardless of the 2 observations', false],
                ],
            ],

            // ── SYSTEM DESIGN PATTERNS ─────────────────────────────────────
            [
                'q' => "You are designing a Bayesian model versioning and rollback system for a production ML platform. Which strategy best preserves the ability to perform principled Bayesian model comparison between versions?",
                'opts' => [
                    ['Store only the MAP estimate for each model version to save space', false],
                    ['Store the full posterior samples (or variational parameters) for each version, along with the data version and prior specification. This enables LOO model comparison, prior sensitivity analysis, and posterior predictive checks on historical data', true],
                    ['Only store the model weights; prior and data can be recomputed', false],
                    ['Store the posterior mean and variance only; that is sufficient for all downstream use', false],
                ],
            ],
            [
                'q' => "In a federated Bayesian learning system where client data cannot leave the device, how can the global posterior be approximated without sharing raw data?",
                'opts' => [
                    ['Each client sends its raw posterior samples to the server', false],
                    ['Each client computes a local posterior (or sufficient statistics/likelihood approximation) and sends these to the server; the server combines them via product-of-experts or power likelihood aggregation to form a global approximate posterior', true],
                    ['Federated Bayesian learning is theoretically impossible', false],
                    ['Each client sends its MAP estimate; the server averages them', false],
                ],
            ],
            [
                'q' => "Your team is tasked with A/B testing 200 simultaneous product variants. A naive frequentist approach requires Bonferroni correction, severely reducing power. The Bayesian alternative is:",
                'opts' => [
                    ['Run 200 independent Bayesian A/B tests and combine p-values', false],
                    ['Use a hierarchical Bayesian model where each variant\'s effect is drawn from a shared prior; the hierarchy provides automatic regularisation (partial pooling), avoiding the need for explicit multiple comparisons correction while sharing statistical strength across variants', true],
                    ['Use Bayesian tests but still apply Bonferroni correction to posterior probabilities', false],
                    ['Reduce to 10 variants to make the problem tractable', false],
                ],
            ],

            // ── PHILOSOPHICAL & META-LEVEL ─────────────────────────────────
            [
                'q' => "A senior stakeholder insists that Bayesian methods are 'subjective' because they require a prior, and therefore frequentist methods should be used for all company decisions. What is the most professionally accurate and constructive response?",
                'opts' => [
                    ['Agree — frequentist methods are objective and should be preferred', false],
                    ['Frequentist methods also involve subjective choices (model family, test statistic, significance threshold); the key difference is where subjectivity is made explicit. Bayesian methods make prior assumptions transparent and auditable, which is an advantage for governance', true],
                    ['The prior can always be set to uniform, making Bayesian methods fully objective', false],
                    ['Subjectivity is always bad; avoid both methods and use machine learning instead', false],
                ],
            ],
            [
                'q' => "Cromwell's Rule states that one should not assign prior probability of 0 or 1 to any empirical hypothesis (except logical contradictions). In a production Bayesian system, violating Cromwell's Rule by setting P(hypothesis) = 0 means:",
                'opts' => [
                    ['The posterior is always uniform', false],
                    ['No amount of data can ever update the posterior away from 0 for that hypothesis; the model is permanently blind to that possibility — a dangerous rigidity in real systems where surprises occur', true],
                    ['The MCMC sampler will diverge immediately', false],
                    ['The marginal likelihood becomes negative', false],
                ],
            ],
            [
                'q' => "Bayesian workflow (as described by Gelman et al., 2020) emphasises iterative model building. In a professional context, this means:",
                'opts' => [
                    ['Fit the most complex model first and simplify until R̂ < 1.01', false],
                    ['Cycle between prior predictive checks, model fitting, posterior predictive checks, and model criticism; treat model building as a collaborative, interpretable process rather than a one-shot procedure', true],
                    ['Use Bayesian methods only for the final model; use frequentist tools for exploratory analysis', false],
                    ['Bayesian workflow only applies to hierarchical models', false],
                ],
            ],
            [
                'q' => "You are asked to justify the computational budget for a full Bayesian analysis (MCMC) vs. a quick MAP estimate to stakeholders who care only about the final prediction. Which argument most accurately captures when full Bayesian inference is worth the cost?",
                'opts' => [
                    ['Full Bayes always gives better point predictions than MAP', false],
                    ['Full Bayes is critical when downstream decisions require calibrated uncertainty (e.g., medical treatment decisions, financial risk management, safety-critical systems), or when the posterior is multimodal and the MAP estimate is misleading', true],
                    ['Full Bayes is only necessary when n < 100', false],
                    ['MAP is always sufficient because the Bernstein-von Mises theorem guarantees identical results for large n', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 11 — Bayesian Data Analysis (Professional).");
    }
}