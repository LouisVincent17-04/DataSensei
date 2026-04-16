<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module6ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Modeling and Simulation')
                 ->delete();

        $this->command->info("Creating Module 6 — Modeling and Simulation (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Modeling and Simulation',
            'description'           => 'Deep simulation problems involving MCMC, hidden Markov models, stochastic differential equations, event-driven architectures, and debugging/optimising complex Python simulation code. Requires strong mathematical and implementation skills.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1500,
            'order_index'           => 6,
        ]);

        $this->command->info("Seeding 50 advanced-level Modeling and Simulation questions...");

        $qaData = [

            // ── MCMC ──────────────────────────────────────────────────────
            [
                'q' => 'The Metropolis-Hastings algorithm accepts a proposed sample x\' with probability:',
                'opts' => [
                    ['p(x\') / p(x)', false],
                    ['min(1, p(x\')·q(x|x\') / [p(x)·q(x\'|x)])', true],
                    ['p(x\') − p(x)', false],
                    ['1 always, to ensure exploration', false],
                ],
            ],
            [
                'q' => 'In MCMC, the "burn-in" period is discarded because:',
                'opts' => [
                    ['It contains the most accurate samples', false],
                    ['The chain has not yet converged to the target distribution', true],
                    ['Random seeds are not effective early in the chain', false],
                    ['The first samples are always outliers by definition', false],
                ],
            ],
            [
                'q' => 'The Gelman-Rubin diagnostic R̂ is used to:',
                'opts' => [
                    ['Estimate the mean of the posterior distribution', false],
                    ['Assess convergence of MCMC by comparing within-chain and between-chain variance across multiple chains', true],
                    ['Tune the proposal distribution step size', false],
                    ['Compute the effective sample size', false],
                ],
            ],
            [
                'q' => 'Gibbs sampling is a special case of Metropolis-Hastings where:',
                'opts' => [
                    ['The acceptance probability is always 0', false],
                    ['Proposals are always rejected', false],
                    ['Each variable is sampled from its conditional distribution given all others — acceptance rate is always 1', true],
                    ['Only one chain is used', false],
                ],
            ],
            [
                'q' => 'The effective sample size (ESS) in MCMC is less than the total number of samples because:',
                'opts' => [
                    ['Some samples are discarded as burn-in', false],
                    ['Autocorrelation between successive samples reduces the information content', true],
                    ['The proposal distribution is too wide', false],
                    ['Gibbs sampling always duplicates samples', false],
                ],
            ],

            // ── HIDDEN MARKOV MODELS ──────────────────────────────────────
            [
                'q' => 'A Hidden Markov Model (HMM) has hidden states and observed emissions. The FORWARD algorithm computes:',
                'opts' => [
                    ['The most likely sequence of hidden states', false],
                    ['The probability of the observed sequence P(O|λ)', true],
                    ['The model parameters via EM', false],
                    ['The stationary distribution of the hidden chain', false],
                ],
            ],
            [
                'q' => 'The Viterbi algorithm for HMMs finds:',
                'opts' => [
                    ['The marginal probability of each hidden state', false],
                    ['The most likely sequence of hidden states given the observations', true],
                    ['The expected number of state transitions', false],
                    ['The model log-likelihood', false],
                ],
            ],
            [
                'q' => 'The Baum-Welch algorithm is used to:',
                'opts' => [
                    ['Decode the most likely state sequence', false],
                    ['Learn HMM parameters (transition, emission, initial probabilities) from unlabelled data using EM', true],
                    ['Compute P(O|λ) efficiently', false],
                    ['Sample from the HMM generatively', false],
                ],
            ],
            [
                'q' => "In an HMM with 2 hidden states and Gaussian emissions, you observe x = 3.5.\nState 0: N(μ=2, σ=1), State 1: N(μ=4, σ=1).\nWhich state is more likely to have emitted x = 3.5?",
                'opts' => [
                    ['State 0, because 2 < 3.5', false],
                    ['State 1, because 3.5 is closer to μ=4 than μ=2', true],
                    ['Both equally likely', false],
                    ['Cannot be determined without the transition matrix', false],
                ],
            ],

            // ── STOCHASTIC DIFFERENTIAL EQUATIONS ────────────────────────
            [
                'q' => 'The Euler-Maruyama method for solving the SDE dX = f(X,t)dt + g(X,t)dW is:\nX_{n+1} = ?',
                'opts' => [
                    ['X_n + f(X_n,t_n)·h', false],
                    ['X_n + f(X_n,t_n)·h + g(X_n,t_n)·√h·Z where Z~N(0,1)', true],
                    ['X_n + g(X_n,t_n)·dW only', false],
                    ['X_n + f(X_n,t_n)·h²', false],
                ],
            ],
            [
                'q' => 'Geometric Brownian Motion (GBM) for stock prices is dS = μS·dt + σS·dW.\nThe analytical solution is:',
                'opts' => [
                    ['S(t) = S(0) + μt + σW(t)', false],
                    ['S(t) = S(0)·exp((μ − σ²/2)t + σW(t))', true],
                    ['S(t) = S(0)·exp(μt)', false],
                    ['S(t) = S(0)·(1 + μt + σ√t)', false],
                ],
            ],
            [
                'q' => 'Why does the Itô correction term −σ²/2 appear in the GBM solution?',
                'opts' => [
                    ['It corrects for discrete time stepping', false],
                    ['Itô\'s lemma applied to ln(S) introduces a second-order term from the quadratic variation of Brownian motion', true],
                    ['It represents mean reversion', false],
                    ['It accounts for the risk-free rate', false],
                ],
            ],
            [
                'q' => 'The Ornstein-Uhlenbeck (OU) process dX = θ(μ − X)dt + σdW models:',
                'opts' => [
                    ['Pure random walk with drift', false],
                    ['Mean-reverting stochastic dynamics — X is pulled back toward μ', true],
                    ['Geometric growth with noise', false],
                    ['A Poisson jump process', false],
                ],
            ],

            // ── CODE DEBUGGING & OPTIMISATION ─────────────────────────────
            [
                'q' => "Identify the bug in this Metropolis-Hastings implementation:\n\nimport numpy as np\n\ndef mh_sample(log_target, n=10000, step=0.5):\n    x = 0.0\n    samples = []\n    for _ in range(n):\n        x_prop = x + np.random.normal(0, step)\n        log_alpha = log_target(x_prop) - log_target(x)\n        if np.log(np.random.uniform()) < log_alpha:\n            x = x_prop\n        samples.append(x)  # BUG\n    return samples",
                'opts' => [
                    ['log_alpha should use absolute values', false],
                    ['Samples should only be appended AFTER burn-in; collecting every sample including rejected ones is fine but burn-in is missing', true],
                    ['x_prop should be x * np.random.normal()', false],
                    ['np.log(np.random.uniform()) should be np.random.uniform()', false],
                ],
            ],
            [
                'q' => "This SDE simulation is slow. What is the primary performance issue?\n\nimport numpy as np\n\ndef simulate_gbm_slow(S0, mu, sigma, T, dt, n_paths):\n    paths = []\n    for _ in range(n_paths):\n        path = [S0]\n        S = S0\n        t = 0\n        while t < T:\n            dW = np.random.normal(0, np.sqrt(dt))\n            S = S * np.exp((mu - 0.5*sigma**2)*dt + sigma*dW)\n            path.append(S)\n            t += dt\n        paths.append(path)\n    return paths",
                'opts' => [
                    ['np.exp is too slow for large inputs', false],
                    ['Python loops over n_paths and time steps — vectorise using numpy arrays across all paths simultaneously', true],
                    ['The GBM formula is mathematically incorrect', false],
                    ['Should use dt² instead of dt in the Wiener term', false],
                ],
            ],
            [
                'q' => "What is wrong with this Markov chain steady-state calculation?\n\nimport numpy as np\n\nP = np.array([[0.8, 0.2],\n              [0.3, 0.7]])\n\n# Power method to find steady state\npi = np.array([1.0, 0.0])\nfor _ in range(100):\n    pi = pi @ P\n\nprint(pi)  # Expected: [0.6, 0.4]",
                'opts' => [
                    ['The matrix P is not a valid transition matrix', false],
                    ['The code is correct — 100 iterations of the power method converges to the stationary distribution [0.6, 0.4]', true],
                    ['pi should be a column vector, not a row vector', false],
                    ['Should use np.linalg.eig instead', false],
                ],
            ],
            [
                'q' => "This event-driven simulation has a performance bug. Identify it:\n\nimport heapq, random\n\nevents = []\nfor i in range(100000):\n    t = random.expovariate(1.0)\n    heapq.heappush(events, (t, i))\n\nresults = []\nwhile events:\n    t, event_id = heapq.heappop(events)\n    results.append(t)\n    # Add new event\n    new_t = t + random.expovariate(1.0)\n    heapq.heappush(events, (new_t, event_id))  # BUG",
                'opts' => [
                    ['heapq is the wrong data structure for events', false],
                    ['This creates an infinite loop — new events are always added inside the while loop with no termination condition', true],
                    ['random.expovariate takes the mean, not the rate', false],
                    ['results should use a deque not a list', false],
                ],
            ],
            [
                'q' => "What does this SimPy-style pseudocode model?\n\ndef customer(env, server, service_time):\n    arrival = env.now\n    with server.request() as req:\n        yield req\n        yield env.timeout(service_time)\n    wait = env.now - arrival - service_time\n    return wait",
                'opts' => [
                    ['A multi-server queue with priorities', false],
                    ['A single-server queuing system where the customer waits for the server, receives service, then records queue wait time', true],
                    ['A non-preemptive scheduling system', false],
                    ['A batch arrival queuing model', false],
                ],
            ],

            // ── ADVANCED MONTE CARLO ──────────────────────────────────────
            [
                'q' => 'Quasi-Monte Carlo (QMC) methods replace pseudo-random numbers with:',
                'opts' => [
                    ['Deterministic numbers from a fixed table', false],
                    ['Low-discrepancy sequences (e.g. Halton, Sobol) that fill the space more uniformly', true],
                    ['Normally distributed samples instead of uniform', false],
                    ['Stratified samples with equal probability', false],
                ],
            ],
            [
                'q' => 'QMC can achieve convergence rate O(1/N) vs Monte Carlo\'s O(1/√N). When does QMC lose this advantage?',
                'opts' => [
                    ['When N is very large', false],
                    ['When the dimension d is very high (curse of dimensionality — low-discrepancy sequences lose their uniformity)', true],
                    ['When the integrand is smooth', false],
                    ['When using Sobol sequences instead of Halton', false],
                ],
            ],
            [
                'q' => 'Multilevel Monte Carlo (MLMC) reduces computational cost by:',
                'opts' => [
                    ['Running all simulations at the finest resolution', false],
                    ['Using a hierarchy of models with increasing resolution — most work done at coarse level, corrections at fine level', true],
                    ['Parallelising across multiple CPUs', false],
                    ['Importance sampling at each level', false],
                ],
            ],
            [
                'q' => 'In importance sampling, the IS estimator uses weight w(x) = p(x)/q(x). The estimator is UNBIASED when:',
                'opts' => [
                    ['q(x) = p(x) everywhere', false],
                    ['q(x) > 0 wherever p(x) > 0', true],
                    ['w(x) = 1 for all x', false],
                    ['The variance of w(x) is minimised', false],
                ],
            ],

            // ── PARAMETER ESTIMATION & CALIBRATION ────────────────────────
            [
                'q' => 'Model CALIBRATION in simulation refers to:',
                'opts' => [
                    ['Verifying that the code runs without errors', false],
                    ['Adjusting model parameters so that simulation outputs match historical real-world data', true],
                    ['Validating the model against a different dataset', false],
                    ['Setting random seeds for reproducibility', false],
                ],
            ],
            [
                'q' => 'The method of MAXIMUM LIKELIHOOD ESTIMATION (MLE) for calibrating a simulation model finds parameters θ that:',
                'opts' => [
                    ['Minimise the sum of squared residuals only', false],
                    ['Maximise P(data | θ) — the probability of observing the data given the parameters', true],
                    ['Minimise the number of parameters', false],
                    ['Maximise the prior probability P(θ)', false],
                ],
            ],
            [
                'q' => 'Approximate Bayesian Computation (ABC) is used when:',
                'opts' => [
                    ['The likelihood function is analytically tractable', false],
                    ['The likelihood is intractable — instead, parameters are accepted if simulated data is "close enough" to observed data', true],
                    ['Only conjugate priors are available', false],
                    ['The model has fewer than 5 parameters', false],
                ],
            ],
            [
                'q' => 'Profile likelihood confidence intervals for a parameter θ are constructed by:',
                'opts' => [
                    ['Computing the Hessian of the log-likelihood at the MLE', false],
                    ['Finding all θ values where the log-likelihood drops by no more than χ²₁,₀.₉₅/2 ≈ 1.92 from the maximum', true],
                    ['Bootstrapping the simulation output', false],
                    ['Using the Fisher information matrix only', false],
                ],
            ],

            // ── ADVANCED ODE / PDE ────────────────────────────────────────
            [
                'q' => 'The finite difference method for the heat equation ∂u/∂t = α∂²u/∂x² discretises the spatial derivative as:',
                'opts' => [
                    ['(u[i+1] − u[i]) / Δx', false],
                    ['(u[i+1] − 2u[i] + u[i-1]) / Δx²', true],
                    ['(u[i+1] − u[i-1]) / (2Δx)', false],
                    ['u[i] / Δx²', false],
                ],
            ],
            [
                'q' => 'The CFL (Courant-Friedrichs-Lewy) stability condition for explicit finite difference methods requires:',
                'opts' => [
                    ['Δt > Δx / c (time step larger than spatial step)', false],
                    ['Δt ≤ Δx / c (CFL number ≤ 1) to ensure numerical stability', true],
                    ['Δx = Δt always', false],
                    ['No condition — explicit methods are always stable', false],
                ],
            ],
            [
                'q' => 'Which numerical scheme for PDEs is UNCONDITIONALLY STABLE regardless of step size?',
                'opts' => [
                    ['Forward Euler (explicit)', false],
                    ['Lax-Friedrichs', false],
                    ['Crank-Nicolson (implicit)', true],
                    ['Runge-Kutta 4', false],
                ],
            ],

            // ── COMPLEX SYSTEM TOPICS ─────────────────────────────────────
            [
                'q' => 'In network simulation, percolation theory asks: at what fraction p of active nodes/edges does a GIANT CONNECTED COMPONENT emerge?',
                'opts' => [
                    ['At p = 0.5 always', false],
                    ['At a critical threshold p_c that depends on the network structure', true],
                    ['At p = 1 only when all nodes are connected', false],
                    ['There is no such threshold — connectivity grows smoothly', false],
                ],
            ],
            [
                'q' => 'In an SIR simulation on a network (rather than a well-mixed population), herd immunity threshold depends on:',
                'opts' => [
                    ['Only R₀', false],
                    ['R₀ and the degree distribution of the network (highly connected nodes spread disease disproportionately)', true],
                    ['Only the number of nodes', false],
                    ['The simulation time step only', false],
                ],
            ],
            [
                'q' => 'Self-organised criticality (SOC) in complex systems refers to:',
                'opts' => [
                    ['Systems that require external tuning to reach critical behaviour', false],
                    ['Systems that naturally evolve toward a critical state with power-law distributed event sizes (e.g. sandpile model)', true],
                    ['Systems that always collapse to a stable fixed point', false],
                    ['Systems where all agents behave identically', false],
                ],
            ],
            [
                'q' => 'A power-law distribution P(x) ∝ x^(−α) in simulation output indicates:',
                'opts' => [
                    ['Normal variation around a mean', false],
                    ['Heavy tails — extreme events are far more likely than a normal distribution would predict', true],
                    ['Exponential decay of event frequency', false],
                    ['The simulation has a bug causing runaway values', false],
                ],
            ],

            // ── SIMULATION DESIGN & ANALYSIS ──────────────────────────────
            [
                'q' => 'A DESIGNED EXPERIMENT in simulation (e.g. factorial design) is used to:',
                'opts' => [
                    ['Generate random input data', false],
                    ['Systematically vary input parameters to understand their effects on outputs', true],
                    ['Run the simulation in parallel', false],
                    ['Calibrate the random number generator', false],
                ],
            ],
            [
                'q' => 'The 2ᵏ factorial design with k factors runs simulations at:',
                'opts' => [
                    ['k levels for each factor', false],
                    ['2k total runs', false],
                    ['All 2ᵏ combinations of high/low settings for each factor', true],
                    ['k² total configurations', false],
                ],
            ],
            [
                'q' => 'Response Surface Methodology (RSM) in simulation optimisation is used to:',
                'opts' => [
                    ['Visualise the simulation code flow', false],
                    ['Fit a surrogate model (metamodel) to simulation outputs and use it to find optimal parameter settings', true],
                    ['Replace the simulation with a statistical model', false],
                    ['Reduce the number of state variables', false],
                ],
            ],
            [
                'q' => 'A METAMODEL (surrogate model) in simulation is:',
                'opts' => [
                    ['A model that simulates other models', false],
                    ['A cheap-to-evaluate approximation of the expensive simulation, used for optimisation and sensitivity analysis', true],
                    ['A model with no random components', false],
                    ['A theoretical model derived analytically', false],
                ],
            ],

            // ── ADVANCED TRACING ──────────────────────────────────────────
            [
                'q' => "Trace this GBM simulation. With S0=100, mu=0.05, sigma=0.2, dt=1, and dW=0.1 (fixed),\nwhat is S after one step using the exact formula?\nS = S0 * exp((mu - 0.5*sigma²)*dt + sigma*dW)",
                'opts' => [
                    ['105.0', false],
                    ['S0 * exp(0.03 + 0.02) = 100 * exp(0.05) ≈ 105.13', true],
                    ['100 * (1 + 0.05) = 105', false],
                    ['100 * exp(0.05 + 0.2*0.1) = 100 * exp(0.07) ≈ 107.25', false],
                ],
            ],
            [
                'q' => "What is the expected value of E[S(T)] for GBM with S(0)=100, μ=0.05, T=1?",
                'opts' => [
                    ['100 * exp(0.03)', false],
                    ['100 * exp(0.07)', false],
                    ['100 * exp(0.05) ≈ 105.13', true],
                    ['100 * (1 + 0.05)', false],
                ],
            ],
            [
                'q' => "The variance of S(T) for GBM is S(0)²·e^(2μT)·(e^(σ²T) − 1).\nFor S0=100, μ=0, σ=0.2, T=1, what is Var[S(T)]?",
                'opts' => [
                    ['100² · (e^0.04 − 1) ≈ 408', true],
                    ['100² · 0.04 = 400', false],
                    ['100² · 0.2 = 2000', false],
                    ['100 · e^0.04 ≈ 104.08', false],
                ],
            ],
            [
                'q' => "A Markov chain has stationary distribution π = [0.6, 0.4].\nIf you start at state 1 and run for a very long time,\nwhat fraction of time will be spent in state 0?",
                'opts' => [
                    ['0.4', false],
                    ['0.5', false],
                    ['0.6', true],
                    ['Depends on the starting state', false],
                ],
            ],
            [
                'q' => "In an MCMC chain of length 10,000 with autocorrelation time τ = 25,\nwhat is the effective sample size (ESS)?\nESS ≈ N / (2τ − 1)",
                'opts' => [
                    ['10,000', false],
                    ['400', false],
                    ['204', true],
                    ['2,500', false],
                ],
            ],
            [
                'q' => "A simulation of a Black-Scholes call option uses N=10,000 GBM paths.\nPayoff at expiry: max(S(T) − K, 0). The estimate is the discounted mean payoff.\nWhich variance reduction technique would most naturally reduce variance here?",
                'opts' => [
                    ['Stratified sampling of time steps', false],
                    ['Control variates using the known E[S(T)] = S(0)e^(rT)', true],
                    ['Antithetic variates are inappropriate for GBM', false],
                    ['Importance sampling on the initial stock price', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 6 — Modeling and Simulation (Advanced).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Advanced");
    }
}