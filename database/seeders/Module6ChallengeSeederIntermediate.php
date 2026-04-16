<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module6ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Modeling and Simulation')
                 ->delete();

        $this->command->info("Creating Module 6 — Modeling and Simulation (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Modeling and Simulation',
            'description'           => 'Multi-step problems on simulation implementation, numerical ODE solvers, Markov chains, variance reduction, and tracing Python simulation code. Requires combining concepts to interpret and debug model behaviour.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 1000,
            'order_index'           => 6,
        ]);

        $this->command->info("Seeding 50 intermediate-level Modeling and Simulation questions...");

        $qaData = [

            // ── MARKOV CHAINS ─────────────────────────────────────────────
            [
                'q' => 'A Markov chain has the property that:',
                'opts' => [
                    ['Future states depend on all past states', false],
                    ['Future states depend only on the current state (memoryless property)', true],
                    ['All states are visited with equal probability', false],
                    ['The chain always converges to zero', false],
                ],
            ],
            [
                'q' => "A two-state Markov chain has transition matrix:\nP = [[0.7, 0.3],\n     [0.4, 0.6]]\n\nIf the system is currently in state 0, what is the probability of being in state 1 after ONE step?",
                'opts' => [
                    ['0.7', false],
                    ['0.4', false],
                    ['0.6', false],
                    ['0.3', true],
                ],
            ],
            [
                'q' => "Using the same transition matrix P = [[0.7, 0.3], [0.4, 0.6]], the STEADY-STATE distribution π satisfies πP = π.\nSolving this system gives π₀ = ?",
                'opts' => [
                    ['0.5', false],
                    ['0.571', false],
                    ['4/7 ≈ 0.571', true],
                    ['0.3', false],
                ],
            ],
            [
                'q' => 'A Markov chain is ERGODIC when it is:',
                'opts' => [
                    ['Absorbing and periodic', false],
                    ['Irreducible and aperiodic — every state can be reached from every other state', true],
                    ['Reducible and absorbing', false],
                    ['Periodic with period 2', false],
                ],
            ],
            [
                'q' => 'In a Markov chain simulation, an ABSORBING state is one where:',
                'opts' => [
                    ['The chain visits it most frequently', false],
                    ['Once entered, the chain cannot leave', true],
                    ['It has the highest transition probability', false],
                    ['It is visited exactly once per simulation run', false],
                ],
            ],

            // ── VARIANCE REDUCTION TECHNIQUES ────────────────────────────
            [
                'q' => 'The purpose of VARIANCE REDUCTION techniques in Monte Carlo simulation is to:',
                'opts' => [
                    ['Reduce the number of random variables in the model', false],
                    ['Achieve the same accuracy with fewer simulation runs', true],
                    ['Make the simulation deterministic', false],
                    ['Eliminate all randomness from the model', false],
                ],
            ],
            [
                'q' => 'ANTITHETIC VARIATES reduce variance by:',
                'opts' => [
                    ['Running the simulation twice with the same seed', false],
                    ['Pairing each run using U with a complementary run using (1 − U), then averaging', true],
                    ['Using stratified random samples', false],
                    ['Increasing the number of random variates', false],
                ],
            ],
            [
                'q' => 'CONTROL VARIATES work by:',
                'opts' => [
                    ['Fixing all random variables', false],
                    ['Using a known analytical quantity correlated with the estimate to correct the simulation output', true],
                    ['Running simulations in parallel', false],
                    ['Replacing random variates with deterministic values', false],
                ],
            ],
            [
                'q' => 'STRATIFIED SAMPLING improves Monte Carlo estimates by:',
                'opts' => [
                    ['Sampling more points near the mean', false],
                    ['Dividing the sample space into strata and sampling proportionally from each', true],
                    ['Using only the highest-probability regions', false],
                    ['Removing outlier samples', false],
                ],
            ],
            [
                'q' => 'IMPORTANCE SAMPLING shifts the sampling distribution to:',
                'opts' => [
                    ['Focus more samples in regions that contribute most to the integral or estimate', true],
                    ['Use equally spaced deterministic samples', false],
                    ['Eliminate tails of the distribution', false],
                    ['Only sample near the mode', false],
                ],
            ],

            // ── CODE TRACING: ODE SOLVERS ─────────────────────────────────
            [
                'q' => "Trace this Euler method code. What is the final value of `y`?\n\nh = 0.5\ny = 1.0\nt = 0.0\nfor _ in range(2):\n    y = y + h * y   # dy/dt = y\n    t += h\nprint(round(y, 4))",
                'opts' => [
                    ['2.0', false],
                    ['2.25', false],
                    ['2.5625', true],
                    ['e ≈ 2.7183', false],
                ],
            ],
            [
                'q' => "What does this code simulate?\n\nimport numpy as np\n\nnp.random.seed(0)\nN = 1000\nsteps = np.random.choice([-1, 1], size=(N, 100))\npositions = np.cumsum(steps, axis=1)\nfinal_pos = positions[:, -1]\nprint(np.mean(np.abs(final_pos)))",
                'opts' => [
                    ['The average of 1000 coin flips', false],
                    ['The mean absolute final position of 1000 independent 100-step random walks', true],
                    ['The variance of a normal distribution', false],
                    ['The Monte Carlo estimate of π', false],
                ],
            ],
            [
                'q' => "What will this SIR model snippet print after one time step (h = 1)?\n\nbeta, gamma = 0.3, 0.1\nS, I, R = 990, 10, 0\nN = 1000\n\ndS = -beta * S * I / N\ndI =  beta * S * I / N - gamma * I\ndR =  gamma * I\n\nS += dS; I += dI; R += dR\nprint(round(I, 2))",
                'opts' => [
                    ['10.0', false],
                    ['12.97', false],
                    ['11.97', true],
                    ['10.3', false],
                ],
            ],
            [
                'q' => "This code implements a simulation but has a bug. What is it?\n\ndef simulate_queue(lam, mu, T=100):\n    t = 0\n    queue = 0\n    while t < T:\n        interarrival = np.random.exponential(lam)   # BUG HERE\n        service      = np.random.exponential(mu)\n        t += interarrival\n        queue = max(0, queue - 1) + 1\n    return queue",
                'opts' => [
                    ['The queue update logic is wrong', false],
                    ['np.random.exponential takes the MEAN (1/λ), not the rate λ — should be 1/lam and 1/mu', true],
                    ['The while loop should be a for loop', false],
                    ['T=100 is too small', false],
                ],
            ],
            [
                'q' => "What does this Monte Carlo code estimate?\n\nimport numpy as np\nnp.random.seed(42)\nN = 100000\nx = np.random.uniform(0, 1, N)\ny = np.random.uniform(0, 1, N)\ninside = np.sum(x**2 + y**2 <= 1)\nprint(4 * inside / N)",
                'opts' => [
                    ['The area of a unit square', false],
                    ['An estimate of π using the quarter-circle method', true],
                    ['The variance of a uniform distribution', false],
                    ['The probability that x > y', false],
                ],
            ],

            // ── RUNGE-KUTTA & NUMERICAL METHODS ───────────────────────────
            [
                'q' => 'The 4th-order Runge-Kutta (RK4) method improves on Euler by:',
                'opts' => [
                    ['Taking only a single slope estimate per step', false],
                    ['Using four slope estimates per step and combining them to achieve O(h⁴) local error', true],
                    ['Using a variable step size automatically', false],
                    ['Solving the ODE analytically first then numerically refining', false],
                ],
            ],
            [
                'q' => 'For the RK4 method applied to dy/dt = f(t, y), the four slope estimates k₁, k₂, k₃, k₄ are combined as:',
                'opts' => [
                    ['y_new = y + h(k₁ + k₂ + k₃ + k₄)/4', false],
                    ['y_new = y + h(k₁ + 2k₂ + 2k₃ + k₄)/6', true],
                    ['y_new = y + h(k₁ + k₄)/2', false],
                    ['y_new = y + h · k₂', false],
                ],
            ],
            [
                'q' => 'A "stiff" ODE is one where:',
                'opts' => [
                    ['The solution oscillates with constant amplitude', false],
                    ['The system has components that evolve on vastly different time scales, requiring very small step sizes for explicit methods', true],
                    ['The solution grows without bound', false],
                    ['The ODE has no analytical solution', false],
                ],
            ],
            [
                'q' => 'The `scipy.integrate.solve_ivp` function is preferred over `odeint` because it:',
                'opts' => [
                    ['Uses only the Euler method', false],
                    ['Offers adaptive step size control and a unified interface for multiple solvers (RK45, BDF, Radau, etc.)', true],
                    ['Is faster for all ODE types', false],
                    ['Does not require initial conditions', false],
                ],
            ],

            // ── AGENT-BASED MODELING ──────────────────────────────────────
            [
                'q' => 'In an agent-based model (ABM), EMERGENT behaviour refers to:',
                'opts' => [
                    ['Behaviour explicitly programmed into each agent', false],
                    ['Global patterns that arise from local agent interactions without being explicitly programmed', true],
                    ['The behaviour of the slowest agent', false],
                    ['Random crashes in the simulation', false],
                ],
            ],
            [
                'q' => 'Boids (flocking simulation) uses which three simple rules per agent?',
                'opts' => [
                    ['Eat, Sleep, Reproduce', false],
                    ['Separation, Alignment, Cohesion', true],
                    ['Move, Stop, Turn', false],
                    ['Attract, Repel, Ignore', false],
                ],
            ],
            [
                'q' => 'The Schelling segregation model demonstrates that:',
                'opts' => [
                    ['Agents always choose to segregate completely', false],
                    ['Even mild individual preferences for similar neighbours can produce large-scale segregation', true],
                    ['Segregation never occurs in random populations', false],
                    ['Agents must be programmed with extreme preferences to produce segregation', false],
                ],
            ],
            [
                'q' => 'In a cellular automaton (like Conway\'s Game of Life), the state of a cell at the next step depends on:',
                'opts' => [
                    ['Only its own current state', false],
                    ['Its current state and the states of its neighbours', true],
                    ['A global random number generator', false],
                    ['The state of the cell 10 steps ago', false],
                ],
            ],

            // ── SYSTEM DYNAMICS CALCULATIONS ─────────────────────────────
            [
                'q' => 'A system dynamics model has:\n  Stock = 500\n  Inflow rate = 50/month\n  Outflow rate = 30/month\n\nWhat is the net rate of change per month?',
                'opts' => [
                    ['80', false],
                    ['−20', false],
                    ['20', true],
                    ['500', false],
                ],
            ],
            [
                'q' => 'Using the same model (Stock=500, inflow=50, outflow=30), what is the stock value after 3 months (linear Euler approximation)?',
                'opts' => [
                    ['530', false],
                    ['560', true],
                    ['590', false],
                    ['650', false],
                ],
            ],
            [
                'q' => 'A first-order delay in system dynamics means that the output approaches the input:',
                'opts' => [
                    ['Instantly', false],
                    ['Never', false],
                    ['Exponentially with a time constant τ', true],
                    ['Linearly with slope 1/τ permanently', false],
                ],
            ],
            [
                'q' => 'In a predator-prey (Lotka-Volterra) model:\n  dx/dt = αx − βxy\n  dy/dt = δxy − γy\n\nWhat happens to the prey population (x) when there are NO predators (y = 0)?',
                'opts' => [
                    ['x declines exponentially', false],
                    ['x stays constant', false],
                    ['x grows exponentially at rate α', true],
                    ['x oscillates', false],
                ],
            ],
            [
                'q' => 'In the Lotka-Volterra model, the equilibrium point (where both dx/dt = 0 and dy/dt = 0) is at:',
                'opts' => [
                    ['x = 0, y = 0', false],
                    ['x = γ/δ, y = α/β', true],
                    ['x = α/β, y = γ/δ', false],
                    ['x = β/α, y = δ/γ', false],
                ],
            ],

            // ── SIMULATION STATISTICS ─────────────────────────────────────
            [
                'q' => 'You run a simulation 30 times and get a sample mean of 42.5 with sample std of 6.0.\nThe 95% confidence interval half-width is approximately t₀.₀₂₅,₂₉ × (6/√30) ≈ 2.045 × 1.095.\nWhat is the CI half-width?',
                'opts' => [
                    ['1.095', false],
                    ['2.045', false],
                    ['2.24', true],
                    ['6.0', false],
                ],
            ],
            [
                'q' => 'In simulation, the NUMBER OF REPLICATIONS needed to achieve a desired half-width h in a confidence interval scales with:',
                'opts' => [
                    ['n ∝ h', false],
                    ['n ∝ 1/h', false],
                    ['n ∝ 1/h²', true],
                    ['n ∝ h²', false],
                ],
            ],
            [
                'q' => 'Which test is commonly used to check if simulation output has reached steady state?',
                'opts' => [
                    ['Chi-squared test', false],
                    ['Welch\'s spectral method or Schruben\'s test for initialization bias', true],
                    ['A t-test comparing two means', false],
                    ['The Kolmogorov-Smirnov test', false],
                ],
            ],

            // ── MULTI-STEP REASONING ──────────────────────────────────────
            [
                'q' => "A simulation models daily sales: Sales ~ Poisson(λ=20).\nOver 10 days, the expected total sales and standard deviation of total sales are:",
                'opts' => [
                    ['Mean = 20, SD = √20', false],
                    ['Mean = 200, SD = √200 ≈ 14.14', true],
                    ['Mean = 200, SD = 20', false],
                    ['Mean = 20, SD = 10', false],
                ],
            ],
            [
                'q' => "Using antithetic variates: you estimate E[f(U)] using pairs (U, 1−U).\nIf f(U) = U², what is E[f(U) + f(1−U)] / 2?",
                'opts' => [
                    ['1/3', false],
                    ['1/2', false],
                    ['1/3 (same as E[U²] = 1/3 — no variance reduction for symmetric functions)', false],
                    ['(U² + (1−U)²)/2 = U² − U + 0.5, which has E = 1/3', true],
                ],
            ],
            [
                'q' => "A Markov chain transition matrix is:\nP = [[0.9, 0.1],\n     [0.2, 0.8]]\n\nStarting in state 0, after TWO steps, what is the probability of being in state 1?\n(Hint: compute P² row 0, col 1)",
                'opts' => [
                    ['0.1', false],
                    ['0.19', true],
                    ['0.2', false],
                    ['0.01', false],
                ],
            ],
            [
                'q' => "A simulation uses the inverse-transform method to generate Exponential(λ=2) samples.\nGiven U = 0.6, what is the generated value x?\nx = −(1/λ)·ln(U)",
                'opts' => [
                    ['0.3', false],
                    ['0.255', true],
                    ['0.5', false],
                    ['0.693', false],
                ],
            ],
            [
                'q' => "In the SIR model with β = 0.5, γ = 0.25, N = 10000, I₀ = 1, S₀ = 9999:\nR₀ = β/γ. Will this epidemic take off?",
                'opts' => [
                    ['No — R₀ = 0.5 < 1', false],
                    ['No — R₀ = 2, but S₀/N < 1/R₀', false],
                    ['Yes — R₀ = 2 > 1 and S₀/N ≈ 1 > 1/R₀ = 0.5', true],
                    ['Cannot be determined without running the simulation', false],
                ],
            ],
            [
                'q' => "This function simulates a simple G/G/1 queue. What does it return?\n\ndef gg1_mean_wait(arrival_times, service_times):\n    n = len(arrival_times)\n    completion = [0.0] * n\n    completion[0] = arrival_times[0] + service_times[0]\n    for i in range(1, n):\n        start = max(arrival_times[i], completion[i-1])\n        completion[i] = start + service_times[i]\n    waits = [completion[i] - arrival_times[i] - service_times[i]\n             for i in range(n)]\n    return sum(waits) / n",
                'opts' => [
                    ['The average total time in system', false],
                    ['The average waiting time in queue (excluding service time)', true],
                    ['The server utilisation', false],
                    ['The average service time', false],
                ],
            ],
            [
                'q' => "What is the expected output of this code?\n\nimport numpy as np\nnp.random.seed(1)\nU = np.random.uniform(0, 1, 5)\nlam = 2\nsamples = -np.log(U) / lam\nprint(round(np.mean(samples), 3))",
                'opts' => [
                    ['0.5 (theoretical mean of Exp(2))', false],
                    ['The result will be close to 0.5 but not exact due to random variation', true],
                    ['2.0', false],
                    ['0.0', false],
                ],
            ],
            [
                'q' => "A discrete-event simulation processes events in a priority queue. What happens if two events have the SAME scheduled time?",
                'opts' => [
                    ['The simulation crashes', false],
                    ['Both events are cancelled', false],
                    ['A tie-breaking rule (e.g. event type priority or FIFO) is applied — the order must be defined', true],
                    ['The simulation skips both events', false],
                ],
            ],
            [
                'q' => "What type of model would BEST capture the spread of a rumour through a social network?",
                'opts' => [
                    ['A differential equation model only', false],
                    ['An agent-based or network-based simulation where individual connections matter', true],
                    ['A static statistical model', false],
                    ['A continuous fluid dynamics model', false],
                ],
            ],
            [
                'q' => "In cellular automaton Rule 110, the next state of a cell depends on:\nits current state and the states of its LEFT and RIGHT neighbours.\n\nGiven a cell = 1, left = 0, right = 1, Rule 110 maps pattern '011' → 1.\nIs the cell alive (1) or dead (0) in the next step?",
                'opts' => [
                    ['Dead (0)', false],
                    ['Alive (1)', true],
                    ['Cannot be determined', false],
                    ['It alternates each step', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 6 — Modeling and Simulation (Intermediate).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Intermediate");
    }
}