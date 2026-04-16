<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module6ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Modeling and Simulation')
                 ->delete();

        $this->command->info("Creating Module 6 — Modeling and Simulation (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Modeling and Simulation',
            'description'           => 'Professional-grade simulation challenges: production system failures, high-performance simulation architecture, real-world financial and epidemiological model edge cases, digital twins, and simulation at scale. For working simulation engineers, quants, and data scientists.',
            'time_limit_seconds'    => 2400,
            'base_xp'               => 2000,
            'order_index'           => 6,
        ]);

        $this->command->info("Seeding 50 professional-level Modeling and Simulation questions...");

        $qaData = [

            // ── PRODUCTION SIMULATION FAILURES ────────────────────────────
            [
                'q' => "A production Monte Carlo risk engine gives different results on different machines despite a fixed seed. What is the most likely cause?\n\nnp.random.seed(42)\nresults = pool.map(simulate_scenario, scenarios)  # multiprocessing",
                'opts' => [
                    ['The seed is not large enough', false],
                    ['Multiprocessing workers share the same seed — all produce identical streams; also, non-deterministic process scheduling causes varying result ordering', true],
                    ['np.random is not thread-safe', false],
                    ['pool.map does not preserve order', false],
                ],
            ],
            [
                'q' => "A GBM-based option pricer returns NaN for long maturities. The code:\n\nS = S0 * np.exp((mu - 0.5*sigma**2)*T + sigma*np.sqrt(T)*Z)\npayoff = np.maximum(S - K, 0)\nprice = np.exp(-r*T) * np.mean(payoff)\n\nWith T=50, sigma=0.8. What fails?",
                'opts' => [
                    ['np.exp overflows to inf for large (mu-0.5*sigma²)*T + sigma*√T*Z when Z is large, then inf - K = inf, but mean(inf values) is NaN when mixed with 0s', true],
                    ['np.maximum does not work with NaN', false],
                    ['np.sqrt(T) is not defined for T=50', false],
                    ['np.exp(-r*T) underflows to 0', false],
                ],
            ],
            [
                'q' => "A discrete-event simulation of a hospital system runs correctly for small inputs but hangs indefinitely for large ones. The event loop:\n\nwhile event_queue:\n    t, event = heapq.heappop(event_queue)\n    if event == 'ARRIVAL':\n        heapq.heappush(event_queue, (t + exp_sample(lam), 'ARRIVAL'))\n        heapq.heappush(event_queue, (t + exp_sample(mu), 'SERVICE'))\n\nWhat is the problem?",
                'opts' => [
                    ['heapq cannot handle string event types', false],
                    ['Every arrival generates a new arrival AND a service event unconditionally, causing exponential event queue growth — arrivals must be bounded by a simulation end time or capacity', true],
                    ['exp_sample is too slow for large inputs', false],
                    ['The queue should be a deque, not a heap', false],
                ],
            ],
            [
                'q' => "An MCMC sampler produces a chain that appears converged (R̂ ≈ 1.0) but posterior predictive checks fail badly. The most likely explanation is:",
                'opts' => [
                    ['The chain is too short', false],
                    ['R̂ only measures convergence between chains, not whether chains have found ALL modes — the sampler is stuck in one mode of a multimodal distribution', true],
                    ['The proposal distribution has the wrong variance', false],
                    ['Burn-in was not applied', false],
                ],
            ],
            [
                'q' => "A climate simulation runs stably for 100 years but diverges at year 101. The explicit finite-difference solver uses:\n  dt = 3600  # seconds\n  dx = 100000  # metres\n  alpha = 1e6  # diffusivity\n\nThe CFL number α·dt/dx² = ?  Is the solver stable?",
                'opts' => [
                    ['CFL = 1e6·3600/1e10 = 0.36 — stable', true],
                    ['CFL = 1e6·3600/1e10 = 0.36 — unstable because CFL must be < 0.25 for 2D', false],
                    ['CFL = 1e6/3600 — cannot determine stability', false],
                    ['The solver is stable because CFL < 1', false],
                ],
            ],

            // ── HIGH-PERFORMANCE SIMULATION ────────────────────────────────
            [
                'q' => "You need to simulate 10⁷ GBM paths in Python. Which approach gives the best performance?\n\n# Option A: loop\nfor i in range(N):\n    paths[i] = simulate_single_path()\n\n# Option B: vectorised\nZ = np.random.normal(0, 1, (N, T))\npaths = S0 * np.exp(np.cumsum((mu-0.5*sigma**2)*dt + sigma*np.sqrt(dt)*Z, axis=1))",
                'opts' => [
                    ['Option A — loops are more memory efficient', false],
                    ['Option B — NumPy vectorisation eliminates Python overhead, running all paths in compiled C code', true],
                    ['Both are equivalent in performance', false],
                    ['Option A with multiprocessing is always faster', false],
                ],
            ],
            [
                'q' => "For a simulation requiring 10⁹ samples, which memory layout maximises cache efficiency when computing per-path statistics?",
                'opts' => [
                    ['Paths stored as a list of Python lists (row-major access by path)', false],
                    ['A contiguous numpy array of shape (n_paths, n_steps) accessed row-by-row — each path is contiguous in memory', true],
                    ['A dictionary of paths for flexibility', false],
                    ['Sparse matrix format to save memory', false],
                ],
            ],
            [
                'q' => "When parallelising Monte Carlo simulation across CPU cores using Python multiprocessing, the correct approach to ensure independent random streams per worker is:",
                'opts' => [
                    ['Pass the same seed to all workers', false],
                    ['Use np.random.default_rng(seed + worker_id) or the SeedSequence API to generate independent Generator objects per worker', true],
                    ['Let each worker generate its own seed from time.time()', false],
                    ['Use threading instead of multiprocessing for numpy', false],
                ],
            ],
            [
                'q' => "A simulation runs on a GPU using CuPy. Which operation pattern causes a significant slowdown?\n\n# Pattern A\nfor step in range(T):\n    result = cp.sum(cp_array)  # GPU\n    cpu_val = float(result)    # GPU→CPU transfer\n    if cpu_val > threshold:\n        ...\n\n# Pattern B\nresults = cp.empty(T)\nfor step in range(T):\n    results[step] = cp.sum(cp_array)\ncpu_results = results.get()  # single transfer",
                'opts' => [
                    ['Pattern B is slower due to batch transfer overhead', false],
                    ['Pattern A causes T GPU→CPU synchronisation points per simulation — each float() forces a synchronisation that stalls the GPU pipeline', true],
                    ['Both patterns have identical performance', false],
                    ['Pattern A is faster because it uses less GPU memory', false],
                ],
            ],

            // ── FINANCIAL SIMULATION EDGE CASES ───────────────────────────
            [
                'q' => "A Monte Carlo pricer for an Asian option (payoff based on average price) underestimates the option value consistently. The code uses:\n\nprices = simulate_gbm_path(S0, r, sigma, T, steps)\npayoff = max(np.mean(prices) - K, 0)\n\nThe prices array does NOT include the initial price S0. What is the bias?",
                'opts' => [
                    ['The mean excludes S0, so the average is computed over fewer points than intended — if S0 is above the average path, this lowers the mean and underestimates the payoff', true],
                    ['GBM paths always underestimate', false],
                    ['np.mean is biased for small arrays', false],
                    ['K should be discounted', false],
                ],
            ],
            [
                'q' => "The Heston stochastic volatility model:\n  dS = μS dt + √v · S dW₁\n  dv = κ(θ−v)dt + ξ√v dW₂  (corr ρ)\n\nThe Feller condition for the variance process to remain non-negative almost surely is:",
                'opts' => [
                    ['κθ > ξ', false],
                    ['2κθ > ξ²', true],
                    ['κ > θ', false],
                    ['ξ < 1', false],
                ],
            ],
            [
                'q' => "In a full-revaluation Monte Carlo for a bond portfolio under interest rate stress, which numerical issue arises when the yield curve shift is large?",
                'opts' => [
                    ['Bond prices become negative for very large yield increases — you must floor prices at zero or cap yield shifts', true],
                    ['The discount factor e^(−rT) overflows', false],
                    ['Duration becomes negative', false],
                    ['Convexity adjustments are negligible', false],
                ],
            ],
            [
                'q' => "You are pricing a barrier option using Monte Carlo. The option knocks out if S ever crosses H during [0,T]. With daily monitoring (252 steps), results differ from continuous monitoring. The correction is:",
                'opts' => [
                    ['Run more paths', false],
                    ['Apply the Broadie-Glasserman-Kou continuity correction: adjust the barrier by exp(±β·σ√(T/steps)) where β ≈ 0.5826', true],
                    ['Use smaller time steps only', false],
                    ['Use the exact barrier probability formula instead', false],
                ],
            ],

            // ── EPIDEMIOLOGICAL SIMULATION AT SCALE ───────────────────────
            [
                'q' => "An individual-based COVID model with 10⁷ agents runs too slowly (48 hours per simulation day). The bottleneck is contact network generation:\n\nfor agent in agents:\n    contacts = [a for a in agents if distance(agent, a) < threshold]\n\nThe correct algorithmic fix is:",
                'opts' => [
                    ['Use a faster distance function', false],
                    ['Replace O(N²) brute force with spatial indexing (k-d tree, grid cells, or R-tree) to find contacts in O(N log N)', true],
                    ['Reduce the number of agents to 10⁵', false],
                    ['Use multiprocessing with 48 cores', false],
                ],
            ],
            [
                'q' => "A stochastic SIR simulation with very few initial infecteds (I₀ = 1, N = 10⁶) frequently shows 'fade-out' (epidemic dies before taking off) even when R₀ = 3. This is because:",
                'opts' => [
                    ['The ODE model is inaccurate', false],
                    ['When I is small, demographic stochasticity dominates — the single infected individual may recover before transmitting; the probability of fade-out is 1/R₀ per generation', true],
                    ['R₀ = 3 is too small for epidemic take-off', false],
                    ['The simulation has a bug in the recovery step', false],
                ],
            ],
            [
                'q' => "In a network SIR simulation, 'superspreader' events arise from degree heterogeneity. The threshold for epidemic invasion in a random network with degree distribution P(k) is:\n\nR₀_network > 1 when β/γ > ?",
                'opts' => [
                    ['⟨k⟩', false],
                    ['⟨k²⟩/⟨k⟩ − 1 (heterogeneous mean field threshold)', true],
                    ['max(k)', false],
                    ['1/⟨k⟩', false],
                ],
            ],

            // ── DIGITAL TWINS & REAL-TIME SIMULATION ──────────────────────
            [
                'q' => "A digital twin for a manufacturing plant must update its state every 100ms from sensor data. The simulation uses a stiff ODE solver (BDF method) that takes 500ms per step. The correct engineering approach is:",
                'opts' => [
                    ['Replace the physics model with a linear approximation', false],
                    ['Decouple the high-fidelity simulation from real-time control using a fast surrogate (neural network or reduced-order model) for real-time feedback, with the full model running asynchronously', true],
                    ['Increase server CPU count until 100ms is achievable', false],
                    ['Use Euler method instead of BDF', false],
                ],
            ],
            [
                'q' => "A digital twin receives noisy sensor measurements and must estimate the true system state. The correct mathematical framework is:",
                'opts' => [
                    ['Least squares fitting of the sensor data', false],
                    ['A Kalman filter (or extended/unscented KF for nonlinear systems) that fuses model predictions with sensor observations optimally', true],
                    ['A moving average of sensor readings', false],
                    ['Running the simulation forward without correction', false],
                ],
            ],
            [
                'q' => "The Unscented Kalman Filter (UKF) is preferred over the Extended Kalman Filter (EKF) for highly nonlinear systems because:",
                'opts' => [
                    ['UKF is always faster to compute', false],
                    ['UKF propagates a carefully chosen set of sigma points through the nonlinear function, capturing mean and covariance to higher order without computing Jacobians', true],
                    ['EKF requires more memory', false],
                    ['UKF handles discrete measurements; EKF handles continuous only', false],
                ],
            ],

            // ── SIMULATION VERIFICATION & VALIDATION ──────────────────────
            [
                'q' => "The distinction between VERIFICATION and VALIDATION in simulation is:",
                'opts' => [
                    ['They are the same thing', false],
                    ['Verification: did we build the model RIGHT? (code matches specification). Validation: did we build the RIGHT model? (model represents reality)', true],
                    ['Verification: does the model match data? Validation: is the code bug-free?', false],
                    ['Verification applies to deterministic models; validation to stochastic', false],
                ],
            ],
            [
                'q' => "Face validity, operational validity, and predictive validity are three types of model validation. A model passes PREDICTIVE validity when:",
                'opts' => [
                    ['Experts agree the model looks reasonable', false],
                    ['The model performs correctly under a range of input conditions', false],
                    ['The model accurately predicts system behaviour on NEW data not used in calibration', true],
                    ['The simulation code has no bugs', false],
                ],
            ],
            [
                'q' => "A simulation model is calibrated to historical data from 2010-2020 and validated on 2021 data. The model performs well in validation. In 2022, a structural change occurs in the system. This is an example of:",
                'opts' => [
                    ['Overfitting', false],
                    ['Model invalidation due to non-stationarity — the model was valid for the old regime but the system has changed', true],
                    ['Poor calibration', false],
                    ['Insufficient validation data', false],
                ],
            ],

            // ── RARE EVENT SIMULATION ──────────────────────────────────────
            [
                'q' => "You need to estimate P(loss > L) = 10⁻⁹ using Monte Carlo. Standard sampling requires roughly how many samples for a relative error of 10%?",
                'opts' => [
                    ['10⁶ samples', false],
                    ['10⁹ samples (1/p for ~1 expected event)', false],
                    ['~100/p = 10¹¹ samples for relative error ≈ 10%', true],
                    ['10³ samples with importance sampling always suffices', false],
                ],
            ],
            [
                'q' => "Splitting (or RESTART) simulation for rare event estimation works by:",
                'opts' => [
                    ['Running multiple independent simulations in parallel', false],
                    ['When a path reaches an intermediate rare threshold, cloning it into multiple sub-paths, amplifying trajectories heading toward the rare event', true],
                    ['Adjusting the probability of rare events analytically', false],
                    ['Using stratified sampling on the event space', false],
                ],
            ],
            [
                'q' => "Cross-entropy method for rare event simulation adaptively finds the optimal importance sampling distribution by:",
                'opts' => [
                    ['Minimising the KL divergence between the IS distribution and the zero-variance IS distribution', true],
                    ['Maximising the number of rare event occurrences', false],
                    ['Computing the exact probability analytically first', false],
                    ['Using the empirical distribution of past samples', false],
                ],
            ],

            // ── COMPLEX PRODUCTION CODE REVIEW ────────────────────────────
            [
                'q' => "This production simulation pipeline has a subtle statistical bug:\n\ndef run_experiment(n_replications=100):\n    results = []\n    for seed in range(n_replications):\n        np.random.seed(seed)\n        results.append(simulate())\n    return np.mean(results), np.std(results) / np.sqrt(n_replications)\n\n# simulate() calls a global model object that caches state between runs",
                'opts' => [
                    ['np.std should use ddof=1', false],
                    ['If simulate() uses a global stateful object not reset between runs, the runs are NOT independent — state leaks across replications, violating the iid assumption of the confidence interval', true],
                    ['Seeds 0-99 are not sufficiently random', false],
                    ['Should use multiprocessing for independence', false],
                ],
            ],
            [
                'q' => "A real-time simulation system must process 10,000 events/second. Profiling shows 80% of time is in:\n\ndef compute_interaction(agent_i, agent_j):\n    return np.linalg.norm(agent_i.pos - agent_j.pos) < agent_i.radius + agent_j.radius\n\nCalled O(N²) times per step. The correct fix is:",
                'opts' => [
                    ['Replace np.linalg.norm with math.sqrt for speed', false],
                    ['Use a spatial hash grid or BVH tree to reduce interaction checks to O(N log N), only checking nearby agents', true],
                    ['Use Cython to compile the function', false],
                    ['Reduce the agent radius to avoid most checks', false],
                ],
            ],
            [
                'q' => "A Bayesian network simulation uses forward sampling to generate scenarios. With 50 binary nodes and highly correlated structure, the issue is:\n\nscenarios = [forward_sample(bn) for _ in range(10000)]\nrare_event_prob = sum(s['crisis']==True for s in scenarios) / 10000",
                'opts' => [
                    ['10000 samples is always sufficient', false],
                    ['If P(crisis) is very small (e.g. 10⁻⁶), forward sampling produces ~0 crisis events in 10,000 draws — use likelihood weighting or importance sampling in the Bayesian network instead', true],
                    ['Binary nodes cannot be used in forward sampling', false],
                    ['Should use rejection sampling instead', false],
                ],
            ],

            // ── THEORETICAL DEPTH ─────────────────────────────────────────
            [
                'q' => "The No Free Lunch theorem applied to simulation-based optimisation states:",
                'opts' => [
                    ['All optimisation algorithms are equally fast for convex problems', false],
                    ['No single optimisation algorithm outperforms all others across all possible objective functions — algorithm choice must match problem structure', true],
                    ['Stochastic optimisation always outperforms deterministic methods', false],
                    ['Gradient-free methods are always inferior', false],
                ],
            ],
            [
                'q' => "In simulation-based inference (SBI), a neural posterior estimator replaces likelihood computation with:",
                'opts' => [
                    ['A deterministic surrogate model', false],
                    ['A neural network trained to approximate p(θ|x_obs) directly from (θ, simulated data) pairs, enabling inference when the likelihood is intractable', true],
                    ['MCMC sampling from the prior', false],
                    ['Variational autoencoder compression of simulation outputs', false],
                ],
            ],
            [
                'q' => "The Wasserstein distance is preferred over KL divergence for comparing simulation output distributions to real data when:",
                'opts' => [
                    ['The distributions have heavy tails', false],
                    ['The distributions have non-overlapping support — KL divergence is infinite but Wasserstein remains finite and meaningful', true],
                    ['The sample size is very large', false],
                    ['The distributions are Gaussian', false],
                ],
            ],
            [
                'q' => "A physics-informed neural network (PINN) embeds simulation equations into the loss function:\n\nL = L_data + λ·L_physics\nwhere L_physics = ||∂u/∂t + N[u] − f||²\n\nCompared to traditional numerical simulation, PINNs are advantageous when:",
                'opts' => [
                    ['The PDE has a known analytical solution', false],
                    ['Data is available at scattered locations and the PDE boundary conditions are complex or partially unknown — PINNs can incorporate both data and physics simultaneously', true],
                    ['High accuracy is required in all regions', false],
                    ['The simulation is already fast', false],
                ],
            ],
            [
                'q' => "In large-scale agent-based modelling (ABM) with 10⁸ agents on distributed hardware, maintaining global synchrony (all agents update simultaneously per tick) becomes impractical. The standard solution is:",
                'opts' => [
                    ['Reduce the number of agents', false],
                    ['Use asynchronous event-driven updates — agents update only when triggered by events, eliminating the global barrier and enabling massive parallelism', true],
                    ['Use a single master process to coordinate all agents', false],
                    ['Increase the time step to reduce barrier frequency', false],
                ],
            ],
            [
                'q' => "A professional simulation of supply chain disruptions must account for fat-tailed disruption durations. Using a normal distribution for disruption times leads to:\n\n- Underestimation of the probability of very long disruptions\n- Models that appear validated on historical data but fail catastrophically on rare events\n\nThe correct distributional choice is:",
                'opts' => [
                    ['Exponential distribution', false],
                    ['Log-normal or Pareto distribution to capture fat tails and rare extreme disruptions', true],
                    ['Uniform distribution for simplicity', false],
                    ['Poisson distribution for count data', false],
                ],
            ],
            [
                'q' => "When designing a simulation study for regulatory submission (e.g. FDA medical device validation), which practice is MANDATORY?",
                'opts' => [
                    ['Using only open-source simulation tools', false],
                    ['Pre-registration of the simulation protocol, explicit V&V documentation, uncertainty quantification, and traceable random seeds — simulation as evidence requires full reproducibility', true],
                    ['Running at least 10⁶ Monte Carlo samples', false],
                    ['Using a commercial solver only', false],
                ],
            ],
            [
                'q' => "A simulation produces output y = f(x₁, x₂, ..., x₁₀) with 10 input parameters. Sobol sensitivity indices are preferred over one-at-a-time (OAT) sensitivity analysis because:",
                'opts' => [
                    ['Sobol indices are faster to compute', false],
                    ['Sobol indices capture variance explained by each parameter AND interactions between parameters; OAT misses interaction effects entirely', true],
                    ['OAT cannot handle continuous parameters', false],
                    ['Sobol indices are exact analytical results', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 6 — Modeling and Simulation (Professional).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Professional");
    }
}