<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module6ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Modeling and Simulation')
                 ->delete();

        $this->command->info("Creating Module 6 — Modeling and Simulation (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Modeling and Simulation',
            'description'           => 'Analytical questions on simulation methodology, probability distributions, discrete-event simulation, simple ODE-based models, and basic Monte Carlo calculations. Requires tracing logic and interpreting model outputs.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 750,
            'order_index'           => 6,
        ]);

        $this->command->info("Seeding 50 university-level Modeling and Simulation questions...");

        $qaData = [

            // ── PROBABILITY DISTRIBUTIONS IN SIMULATION ───────────────────
            [
                'q' => 'Which probability distribution is most commonly used to model the TIME BETWEEN random events (e.g. customer arrivals)?',
                'opts' => [
                    ['Normal distribution', false],
                    ['Exponential distribution', true],
                    ['Uniform distribution', false],
                    ['Binomial distribution', false],
                ],
            ],
            [
                'q' => 'The Poisson distribution models:',
                'opts' => [
                    ['The time until an event occurs', false],
                    ['The number of events occurring in a fixed time interval', true],
                    ['Continuous outcomes between 0 and 1', false],
                    ['The sum of independent normal variables', false],
                ],
            ],
            [
                'q' => 'If customer arrivals follow a Poisson process with rate λ = 5 per hour, what is the expected number of arrivals in 2 hours?',
                'opts' => [
                    ['5', false],
                    ['2.5', false],
                    ['10', true],
                    ['25', false],
                ],
            ],
            [
                'q' => 'The inter-arrival time for a Poisson process with rate λ follows which distribution?',
                'opts' => [
                    ['Poisson with mean λ', false],
                    ['Exponential with mean 1/λ', true],
                    ['Normal with mean λ', false],
                    ['Uniform between 0 and λ', false],
                ],
            ],
            [
                'q' => 'A uniform distribution U(a, b) has its probability spread:',
                'opts' => [
                    ['Concentrated near the mean', false],
                    ['Equally across all values between a and b', true],
                    ['Only at the endpoints a and b', false],
                    ['Concentrated at a', false],
                ],
            ],
            [
                'q' => 'Which distribution would you use to model a single yes/no (success/failure) trial with probability p of success?',
                'opts' => [
                    ['Normal', false],
                    ['Poisson', false],
                    ['Bernoulli', true],
                    ['Exponential', false],
                ],
            ],
            [
                'q' => 'The Central Limit Theorem is important in simulation because it says:',
                'opts' => [
                    ['All simulation outputs follow a uniform distribution', false],
                    ['The mean of many independent simulation runs will approximate a normal distribution regardless of the underlying distribution', true],
                    ['You need at least 1 million runs for any valid simulation', false],
                    ['Simulation outputs are always normally distributed', false],
                ],
            ],

            // ── DISCRETE-EVENT SIMULATION ─────────────────────────────────
            [
                'q' => 'In discrete-event simulation (DES), the simulation advances by:',
                'opts' => [
                    ['Fixed equal time steps', false],
                    ['Jumping from one event to the next in time', true],
                    ['Continuously integrating differential equations', false],
                    ['Random time jumps with no schedule', false],
                ],
            ],
            [
                'q' => 'What is an "event" in discrete-event simulation?',
                'opts' => [
                    ['A Python exception', false],
                    ['An instantaneous occurrence that changes the state of the system', true],
                    ['A loop iteration', false],
                    ['A fixed time interval', false],
                ],
            ],
            [
                'q' => 'In a queue simulation (e.g. a bank), which two events are fundamental?',
                'opts' => [
                    ['Open and Close', false],
                    ['Arrival and Departure', true],
                    ['Start and Pause', false],
                    ['Enter and Exit Queue (same event)', false],
                ],
            ],
            [
                'q' => 'In queuing theory, the notation M/M/1 represents a queue where:',
                'opts' => [
                    ['Multiple servers, multiple queues, 1 customer type', false],
                    ['Markovian arrivals, Markovian service times, and 1 server', true],
                    ['Manual arrivals, manual service, 1 queue', false],
                    ['Mixed arrivals, mixed service, 1 hour limit', false],
                ],
            ],
            [
                'q' => 'For an M/M/1 queue, the system is STABLE (queue does not grow infinitely) when:',
                'opts' => [
                    ['The arrival rate λ equals the service rate μ', false],
                    ['The arrival rate λ is greater than the service rate μ', false],
                    ['The arrival rate λ is less than the service rate μ', true],
                    ['There are no arrivals', false],
                ],
            ],
            [
                'q' => 'In an M/M/1 queue with arrival rate λ = 3/hr and service rate μ = 5/hr, what is the server utilisation ρ?',
                'opts' => [
                    ['5/3', false],
                    ['3/5 = 0.6', true],
                    ['3 × 5 = 15', false],
                    ['5 − 3 = 2', false],
                ],
            ],
            [
                'q' => 'The "event calendar" (future event list) in DES is typically implemented as:',
                'opts' => [
                    ['A stack (LIFO)', false],
                    ['A priority queue ordered by event time', true],
                    ['A random array', false],
                    ['A linked list ordered by event type', false],
                ],
            ],

            // ── ODE-BASED MODELS ──────────────────────────────────────────
            [
                'q' => 'The Euler method for numerically solving dy/dt = f(t, y) updates y as:',
                'opts' => [
                    ['y_new = y_old / f(t, y)', false],
                    ['y_new = y_old + h · f(t, y)', true],
                    ['y_new = y_old − h · f(t, y)', false],
                    ['y_new = h · f(t, y)', false],
                ],
            ],
            [
                'q' => 'In the Euler method, a SMALLER step size h generally produces:',
                'opts' => [
                    ['A faster but less accurate solution', false],
                    ['A slower but more accurate solution', true],
                    ['An identical solution regardless', false],
                    ['An unstable solution', false],
                ],
            ],
            [
                'q' => 'The logistic growth model is dP/dt = rP(1 − P/K). What does K represent?',
                'opts' => [
                    ['The initial population', false],
                    ['The carrying capacity (maximum sustainable population)', true],
                    ['The growth rate', false],
                    ['The time variable', false],
                ],
            ],
            [
                'q' => 'In the logistic model, when P approaches K, the growth rate dP/dt approaches:',
                'opts' => [
                    ['Infinity', false],
                    ['r', false],
                    ['0', true],
                    ['K', false],
                ],
            ],
            [
                'q' => 'The SIR model in epidemiology has three compartments. What do S, I, R stand for?',
                'opts' => [
                    ['Stable, Infected, Recovered', false],
                    ['Susceptible, Infected, Recovered', true],
                    ['Susceptible, Immune, Resistant', false],
                    ['Seeded, Infected, Removed', false],
                ],
            ],
            [
                'q' => 'In the SIR model, the basic reproduction number R₀ > 1 means:',
                'opts' => [
                    ['The disease dies out immediately', false],
                    ['Each infected person infects more than one other person on average — the disease spreads', true],
                    ['The recovered population grows faster than the infected', false],
                    ['The simulation has reached steady state', false],
                ],
            ],

            // ── MONTE CARLO — ANALYTICAL ──────────────────────────────────
            [
                'q' => 'You run a Monte Carlo simulation to estimate π by sampling points in a unit square. In 1000 trials, 785 points land inside the quarter-circle. Your estimate of π is approximately:',
                'opts' => [
                    ['785/1000', false],
                    ['4 × 785/1000 = 3.14', true],
                    ['785/4', false],
                    ['1000/785', false],
                ],
            ],
            [
                'q' => 'In Monte Carlo integration, the estimate of ∫₀¹ f(x) dx using N samples is:',
                'opts' => [
                    ['N × f(0.5)', false],
                    ['(1/N) × Σf(xᵢ) where xᵢ are uniform random samples', true],
                    ['f(x₁) + f(xₙ) / 2', false],
                    ['N × max f(x)', false],
                ],
            ],
            [
                'q' => 'The standard error of a Monte Carlo estimate based on N samples scales as:',
                'opts' => [
                    ['1/N', false],
                    ['1/N²', false],
                    ['1/√N', true],
                    ['√N', false],
                ],
            ],
            [
                'q' => 'To halve the standard error in a Monte Carlo simulation, you must:',
                'opts' => [
                    ['Double the number of samples', false],
                    ['Quadruple the number of samples', true],
                    ['Use a better random seed', false],
                    ['Use a finer time step', false],
                ],
            ],

            // ── RANDOM VARIATE GENERATION ────────────────────────────────
            [
                'q' => 'The inverse transform method generates random variates from a distribution F by computing:',
                'opts' => [
                    ['x = F(U) where U ~ Uniform(0,1)', false],
                    ['x = F⁻¹(U) where U ~ Uniform(0,1)', true],
                    ['x = 1 − U where U ~ Uniform(0,1)', false],
                    ['x = F(U²)', false],
                ],
            ],
            [
                'q' => 'To generate an Exponential(λ) random variate using the inverse transform, you compute:',
                'opts' => [
                    ['x = λ · ln(U)', false],
                    ['x = −(1/λ) · ln(U)', true],
                    ['x = 1 / (λ · U)', false],
                    ['x = U / λ', false],
                ],
            ],
            [
                'q' => 'The Box-Muller transform generates:',
                'opts' => [
                    ['Uniform random numbers', false],
                    ['Exponential random variates', false],
                    ['Standard normal random variates from uniform inputs', true],
                    ['Poisson random variates', false],
                ],
            ],

            // ── SIMULATION OUTPUT ANALYSIS ────────────────────────────────
            [
                'q' => 'The "warm-up period" (transient phase) in a simulation refers to:',
                'opts' => [
                    ['The time it takes to install the simulation software', false],
                    ['The initial period where the simulation has not yet reached steady state and data should be discarded', true],
                    ['The period after the simulation ends', false],
                    ['The first random seed iteration', false],
                ],
            ],
            [
                'q' => 'Why is autocorrelation a problem when analysing simulation output?',
                'opts' => [
                    ['It makes the model run slower', false],
                    ['Correlated observations violate the independence assumption of standard confidence intervals', true],
                    ['It introduces bias in random number generation', false],
                    ['It only affects discrete-event simulations', false],
                ],
            ],
            [
                'q' => 'The method of BATCH MEANS in simulation output analysis works by:',
                'opts' => [
                    ['Running one long simulation split into batches to obtain approximately independent mean estimates', true],
                    ['Running multiple short simulations and averaging all outputs', false],
                    ['Using only the first 10% of simulation data', false],
                    ['Averaging every second data point', false],
                ],
            ],
            [
                'q' => 'A 95% confidence interval from simulation output means:',
                'opts' => [
                    ['There is a 95% chance the true value is in this specific interval', false],
                    ['If we repeated the experiment many times, 95% of the constructed intervals would contain the true value', true],
                    ['95% of all simulation runs fall within the interval', false],
                    ['The simulation is 95% accurate', false],
                ],
            ],

            // ── SYSTEM DYNAMICS ───────────────────────────────────────────
            [
                'q' => 'In system dynamics, a "stock" represents:',
                'opts' => [
                    ['A financial asset', false],
                    ['An accumulation — a quantity that builds up or drains over time', true],
                    ['A rate of change', false],
                    ['An event in the simulation', false],
                ],
            ],
            [
                'q' => 'In system dynamics, a "flow" represents:',
                'opts' => [
                    ['The current value of a stock', false],
                    ['The rate at which a stock increases or decreases', true],
                    ['A connection between two agents', false],
                    ['An external input to the system', false],
                ],
            ],
            [
                'q' => 'A POSITIVE feedback loop in a system:',
                'opts' => [
                    ['Stabilises the system toward equilibrium', false],
                    ['Reinforces change — growth leads to more growth (or decline leads to more decline)', true],
                    ['Always causes the system to crash', false],
                    ['Reduces all variables to zero', false],
                ],
            ],
            [
                'q' => 'A NEGATIVE (balancing) feedback loop in a system:',
                'opts' => [
                    ['Amplifies deviations from the target', false],
                    ['Acts to stabilise the system — pushes it back toward a goal or equilibrium', true],
                    ['Always causes oscillation', false],
                    ['Only exists in biological models', false],
                ],
            ],

            // ── TRACING & CALCULATIONS ────────────────────────────────────
            [
                'q' => 'Using Euler\'s method with h = 1 for dy/dt = y, y(0) = 1:\nWhat is y(1)?',
                'opts' => [
                    ['e ≈ 2.718', false],
                    ['1', false],
                    ['2', true],
                    ['1.5', false],
                ],
            ],
            [
                'q' => 'Using Euler\'s method with h = 0.5 for dy/dt = y, y(0) = 1:\nWhat is y(1) after two steps?',
                'opts' => [
                    ['2', false],
                    ['2.25', true],
                    ['e', false],
                    ['2.5', false],
                ],
            ],
            [
                'q' => 'A logistic model has r = 0.3, K = 1000, P(0) = 100.\nIs dP/dt positive, negative, or zero at t = 0?',
                'opts' => [
                    ['Negative', false],
                    ['Zero', false],
                    ['Positive', true],
                    ['Cannot be determined', false],
                ],
            ],
            [
                'q' => 'In the SIR model with β = 0.3 and γ = 0.1, what is R₀?',
                'opts' => [
                    ['0.3', false],
                    ['0.1', false],
                    ['3', true],
                    ['30', false],
                ],
            ],
            [
                'q' => 'For a random walk starting at position 0, where each step is +1 or −1 with equal probability, the EXPECTED position after 100 steps is:',
                'opts' => [
                    ['100', false],
                    ['10', false],
                    ['0', true],
                    ['50', false],
                ],
            ],
            [
                'q' => 'For the same random walk (start at 0, ±1 steps), the expected DISTANCE from origin (root mean square) after n steps is:',
                'opts' => [
                    ['n', false],
                    ['n²', false],
                    ['√n', true],
                    ['n/2', false],
                ],
            ],
            [
                'q' => 'A simulation of a coin flip is run 10,000 times. The expected proportion of heads is approximately:',
                'opts' => [
                    ['0.25', false],
                    ['0.75', false],
                    ['0.5', true],
                    ['1.0', false],
                ],
            ],
            [
                'q' => 'In an M/M/1 queue with ρ = 0.6, the average number of customers IN THE SYSTEM (Ls) is:\nLs = ρ / (1 − ρ)',
                'opts' => [
                    ['0.6', false],
                    ['1.0', false],
                    ['1.5', true],
                    ['2.5', false],
                ],
            ],
            [
                'q' => 'In the same M/M/1 queue (λ = 3/hr, μ = 5/hr), average waiting time in system Ws = 1/(μ − λ) equals:',
                'opts' => [
                    ['1/5 hr', false],
                    ['1/2 hr', true],
                    ['1/3 hr', false],
                    ['1/8 hr', false],
                ],
            ],
            [
                'q' => 'Which Python library provides a straightforward ODE solver used in simulation?',
                'opts' => [
                    ['pandas', false],
                    ['scipy.integrate (odeint / solve_ivp)', true],
                    ['matplotlib', false],
                    ['sklearn', false],
                ],
            ],
            [
                'q' => 'The NumPy function used to generate N uniform random numbers between 0 and 1 is:',
                'opts' => [
                    ['np.random.randn(N)', false],
                    ['np.random.uniform(0, 1, N)', true],
                    ['np.linspace(0, 1, N)', false],
                    ['np.random.normal(0, 1, N)', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 6 — Modeling and Simulation (University Student).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: University Student");
    }
}