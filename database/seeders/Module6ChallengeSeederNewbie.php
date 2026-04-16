<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module6ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Modeling and Simulation')
                 ->delete();

        $this->command->info("Creating Module 6 — Modeling and Simulation (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Modeling and Simulation',
            'description'           => 'Test your understanding of the very basics of modeling and simulation — what models are, why we simulate, types of models, and simple real-world analogies. No prior math or coding experience required.',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 6,
        ]);

        $this->command->info("Seeding 50 newbie-level Modeling and Simulation questions...");

        $qaData = [

            // ── WHAT IS A MODEL ───────────────────────────────────────────
            [
                'q' => 'In the context of science and data, what is a "model"?',
                'opts' => [
                    ['A physical sculpture of a real object', false],
                    ['A simplified representation of a real-world system used to understand or predict behaviour', true],
                    ['A type of database table', false],
                    ['A Python function that prints output', false],
                ],
            ],
            [
                'q' => 'Why do we use models instead of studying the real system directly?',
                'opts' => [
                    ['Because real systems are always too small to observe', false],
                    ['Models are cheaper, safer, and faster to experiment with than the real system', true],
                    ['Models are always 100% accurate', false],
                    ['Real systems cannot be measured', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of a real-world model?',
                'opts' => [
                    ['A weather forecast map', true],
                    ['A cloud in the sky', false],
                    ['A running computer fan', false],
                    ['A printed photograph', false],
                ],
            ],
            [
                'q' => 'A model is always:',
                'opts' => [
                    ['A perfect copy of reality', false],
                    ['An abstraction — it leaves out some details on purpose', true],
                    ['Written in Python', false],
                    ['Based on physical experiments only', false],
                ],
            ],
            [
                'q' => 'Which statement best describes the purpose of simulation?',
                'opts' => [
                    ['To replace the real system permanently', false],
                    ['To run experiments on a model to observe how the system behaves over time', true],
                    ['To generate random numbers', false],
                    ['To train machine learning models', false],
                ],
            ],
            [
                'q' => 'A flight simulator used to train pilots is an example of:',
                'opts' => [
                    ['A real aircraft', false],
                    ['A physical model', false],
                    ['A computer simulation of a real system', true],
                    ['A statistical report', false],
                ],
            ],
            [
                'q' => 'What does "validating a model" mean?',
                'opts' => [
                    ['Writing the model in Python', false],
                    ['Checking that the model accurately represents the real system it is meant to describe', true],
                    ['Running the model many times', false],
                    ['Deleting unused variables', false],
                ],
            ],
            [
                'q' => 'Which of the following is NOT a good reason to build a simulation?',
                'opts' => [
                    ['The real experiment is too dangerous', false],
                    ['The real experiment is too expensive', false],
                    ['You want to decorate a report', true],
                    ['The real system does not exist yet', false],
                ],
            ],

            // ── TYPES OF MODELS ───────────────────────────────────────────
            [
                'q' => 'A mathematical model uses:',
                'opts' => [
                    ['Physical materials like clay and wood', false],
                    ['Equations and formulas to represent a system', true],
                    ['Only graphs and charts', false],
                    ['Only survey data', false],
                ],
            ],
            [
                'q' => 'Which of the following is a mathematical model?',
                'opts' => [
                    ['A globe of the Earth', false],
                    ['F = ma (Newton\'s second law)', true],
                    ['A photograph of a river', false],
                    ['A spreadsheet of names', false],
                ],
            ],
            [
                'q' => 'A DETERMINISTIC model is one where:',
                'opts' => [
                    ['The output is random every time you run it', false],
                    ['Given the same inputs, you always get the same output', true],
                    ['The model uses probability', false],
                    ['The model changes over time on its own', false],
                ],
            ],
            [
                'q' => 'A STOCHASTIC model is one where:',
                'opts' => [
                    ['The output is always the same for the same input', false],
                    ['The model includes randomness or probability', true],
                    ['The model has no variables', false],
                    ['The model only works with integers', false],
                ],
            ],
            [
                'q' => 'A STATIC model represents a system:',
                'opts' => [
                    ['That changes continuously over time', false],
                    ['At a single point in time — it does not evolve', true],
                    ['That runs forever', false],
                    ['Only in 3D space', false],
                ],
            ],
            [
                'q' => 'A DYNAMIC model represents a system:',
                'opts' => [
                    ['That never changes', false],
                    ['That evolves and changes over time', true],
                    ['That only uses integers', false],
                    ['That is always in equilibrium', false],
                ],
            ],
            [
                'q' => 'Which of the following is a CONTINUOUS model?',
                'opts' => [
                    ['A model that counts people in a queue one at a time', false],
                    ['A model describing water temperature changing smoothly over time', true],
                    ['A model that only runs once per day', false],
                    ['A model that uses yes/no variables', false],
                ],
            ],
            [
                'q' => 'Which of the following is a DISCRETE model?',
                'opts' => [
                    ['A model tracking temperature every millisecond', false],
                    ['A model counting the number of customers who arrive each hour', true],
                    ['A model describing fluid pressure', false],
                    ['A model using differential equations', false],
                ],
            ],

            // ── SIMULATION BASICS ─────────────────────────────────────────
            [
                'q' => 'What is a "random number generator" used for in simulation?',
                'opts' => [
                    ['To sort lists faster', false],
                    ['To introduce randomness and uncertainty into a simulation', true],
                    ['To calculate averages', false],
                    ['To connect to the internet', false],
                ],
            ],
            [
                'q' => 'In simulation, what does "initializing" a model mean?',
                'opts' => [
                    ['Deleting all model data', false],
                    ['Setting the starting conditions of the simulation before it runs', true],
                    ['Printing the results', false],
                    ['Saving the simulation to a file', false],
                ],
            ],
            [
                'q' => 'In a simulation, a "time step" refers to:',
                'opts' => [
                    ['The total duration of the simulation', false],
                    ['The small unit of time by which the simulation advances each iteration', true],
                    ['The number of variables in the model', false],
                    ['The speed of the computer', false],
                ],
            ],
            [
                'q' => 'Why would you run a simulation many times (multiple "runs")?',
                'opts' => [
                    ['Because each run uses different hardware', false],
                    ['To get a range of possible outcomes and reduce the effect of randomness', true],
                    ['Because one run always produces an error', false],
                    ['To make the code run faster', false],
                ],
            ],
            [
                'q' => 'What is the purpose of a "seed" in a random simulation?',
                'opts' => [
                    ['To plant data in a database', false],
                    ['To make the random sequence reproducible so results can be verified', true],
                    ['To delete random values', false],
                    ['To speed up the simulation', false],
                ],
            ],
            [
                'q' => 'Which Python function sets the random seed?',
                'opts' => [
                    ['random.reset(42)', false],
                    ['random.seed(42)', true],
                    ['random.start(42)', false],
                    ['random.fix(42)', false],
                ],
            ],

            // ── MONTE CARLO (INTRO) ───────────────────────────────────────
            [
                'q' => 'Monte Carlo simulation is a technique that uses:',
                'opts' => [
                    ['Exact mathematical formulas only', false],
                    ['Repeated random sampling to estimate results', true],
                    ['Physical experiments in a lab', false],
                    ['Neural networks to predict outcomes', false],
                ],
            ],
            [
                'q' => 'Monte Carlo methods are especially useful when:',
                'opts' => [
                    ['The problem has an exact closed-form solution', false],
                    ['The problem is too complex for an exact analytical solution', true],
                    ['You only have one data point', false],
                    ['The system has no randomness at all', false],
                ],
            ],
            [
                'q' => 'In a Monte Carlo simulation to estimate π, you randomly throw darts at a square containing a circle. You use the ratio of darts that land INSIDE the circle to estimate:',
                'opts' => [
                    ['The area of the square', false],
                    ['The value of π', true],
                    ['The circumference of the circle', false],
                    ['The number of darts thrown', false],
                ],
            ],
            [
                'q' => 'As you increase the number of random samples in a Monte Carlo simulation, the estimate generally:',
                'opts' => [
                    ['Gets less accurate', false],
                    ['Stays exactly the same', false],
                    ['Gets more accurate (converges to the true value)', true],
                    ['Becomes undefined', false],
                ],
            ],
            [
                'q' => 'Which real-world field commonly uses Monte Carlo simulation?',
                'opts' => [
                    ['Interior design', false],
                    ['Financial risk analysis', true],
                    ['Cooking recipes', false],
                    ['Social media management', false],
                ],
            ],

            // ── SYSTEM COMPONENTS ─────────────────────────────────────────
            [
                'q' => 'In modeling, a "state variable" is:',
                'opts' => [
                    ['A variable that never changes', false],
                    ['A variable that describes the current condition of the system', true],
                    ['A variable used only for output', false],
                    ['A variable that stores text', false],
                ],
            ],
            [
                'q' => 'In a population growth model, which of these is most likely a state variable?',
                'opts' => [
                    ['The name of the species', false],
                    ['The current population count', true],
                    ['The color of the animals', false],
                    ['The date the model was created', false],
                ],
            ],
            [
                'q' => 'A "parameter" in a model is:',
                'opts' => [
                    ['A variable that changes at every time step', false],
                    ['A fixed value that controls the behaviour of the model', true],
                    ['A random number', false],
                    ['The output of the model', false],
                ],
            ],
            [
                'q' => 'In a simple population model, the "birth rate" is best described as:',
                'opts' => [
                    ['A state variable', false],
                    ['A parameter', true],
                    ['A random seed', false],
                    ['A simulation output', false],
                ],
            ],
            [
                'q' => 'What is the difference between an INPUT and an OUTPUT in a simulation?',
                'opts' => [
                    ['There is no difference', false],
                    ['Inputs are the values you provide to the model; outputs are the results the model produces', true],
                    ['Inputs are always random; outputs are always fixed', false],
                    ['Outputs are provided by the user; inputs are calculated by the model', false],
                ],
            ],

            // ── SIMPLE MODELS & EQUATIONS ────────────────────────────────
            [
                'q' => 'A simple linear model predicts output as y = mx + b. If m = 3 and b = 2, what is y when x = 4?',
                'opts' => [
                    ['9', false],
                    ['12', false],
                    ['14', true],
                    ['10', false],
                ],
            ],
            [
                'q' => 'The equation P(t) = P₀ · e^(rt) models:',
                'opts' => [
                    ['Linear growth', false],
                    ['Exponential growth or decay', true],
                    ['Periodic oscillation', false],
                    ['Random walk', false],
                ],
            ],
            [
                'q' => 'In the exponential growth model P(t) = P₀ · e^(rt), what does P₀ represent?',
                'opts' => [
                    ['The rate of growth', false],
                    ['The initial population at time t = 0', true],
                    ['The final population', false],
                    ['The time variable', false],
                ],
            ],
            [
                'q' => 'In the model P(t) = 100 · e^(0.05t), what is the population at t = 0?',
                'opts' => [
                    ['0', false],
                    ['0.05', false],
                    ['100', true],
                    ['105', false],
                ],
            ],
            [
                'q' => 'If a population starts at 200 and grows by 10% each year, after 1 year the population is:',
                'opts' => [
                    ['210', false],
                    ['220', true],
                    ['200', false],
                    ['202', false],
                ],
            ],
            [
                'q' => 'A decay model has r = −0.1. This means the population is:',
                'opts' => [
                    ['Growing over time', false],
                    ['Staying the same', false],
                    ['Shrinking over time', true],
                    ['Oscillating', false],
                ],
            ],

            // ── SIMULATION WORKFLOW ───────────────────────────────────────
            [
                'q' => 'What is the correct order for building and running a simulation?',
                'opts' => [
                    ['Run → Build → Validate → Analyse', false],
                    ['Define the problem → Build the model → Run the simulation → Analyse results', true],
                    ['Analyse → Define → Run → Build', false],
                    ['Validate → Run → Build → Define', false],
                ],
            ],
            [
                'q' => 'What is "sensitivity analysis" in the context of simulation?',
                'opts' => [
                    ['Checking if the code has bugs', false],
                    ['Testing how much the output changes when you slightly change the input parameters', true],
                    ['Making the model run faster', false],
                    ['Adding more random numbers to the model', false],
                ],
            ],
            [
                'q' => 'What does it mean when a simulation has reached "steady state"?',
                'opts' => [
                    ['The simulation has crashed', false],
                    ['The simulation has been running for exactly 1 hour', false],
                    ['The key variables of the system are no longer changing significantly over time', true],
                    ['All variables equal zero', false],
                ],
            ],
            [
                'q' => 'What is a "scenario" in simulation?',
                'opts' => [
                    ['A bug in the simulation code', false],
                    ['A specific set of input conditions used to run the simulation', true],
                    ['The final output of the simulation', false],
                    ['The visual display of the simulation', false],
                ],
            ],

            // ── AGENT-BASED & SYSTEM THINKING ─────────────────────────────
            [
                'q' => 'In an agent-based model, "agents" are:',
                'opts' => [
                    ['The equations used in the model', false],
                    ['Individual entities (people, animals, particles) with their own rules and behaviours', true],
                    ['The random seeds of the simulation', false],
                    ['The output charts of the simulation', false],
                ],
            ],
            [
                'q' => 'A traffic simulation where each car follows its own rules is an example of:',
                'opts' => [
                    ['A differential equation model', false],
                    ['An agent-based model', true],
                    ['A static model', false],
                    ['A linear regression model', false],
                ],
            ],
            [
                'q' => 'What is "emergence" in complex system simulations?',
                'opts' => [
                    ['A bug that appears after many simulation runs', false],
                    ['Complex behaviour that arises from many simple individual interactions', true],
                    ['The simulation starting up for the first time', false],
                    ['A parameter that is added late in the model', false],
                ],
            ],
            [
                'q' => 'Which Python library is most commonly used to generate random numbers for simulations?',
                'opts' => [
                    ['pandas', false],
                    ['matplotlib', false],
                    ['random or numpy.random', true],
                    ['flask', false],
                ],
            ],
            [
                'q' => 'What does `numpy.random.uniform(0, 1)` return?',
                'opts' => [
                    ['Always exactly 0.5', false],
                    ['A random float between 0 and 1', true],
                    ['A random integer between 0 and 1', false],
                    ['A list of 100 random numbers', false],
                ],
            ],
            [
                'q' => 'In a coin-flip simulation, which value represents "Heads" most naturally?',
                'opts' => [
                    ['−1', false],
                    ['100', false],
                    ['1', true],
                    ['0.5', false],
                ],
            ],
            [
                'q' => 'What is the term for the difference between a model\'s prediction and the actual real-world value?',
                'opts' => [
                    ['Variance', false],
                    ['Error or residual', true],
                    ['Iteration', false],
                    ['Parameter', false],
                ],
            ],
            [
                'q' => 'Which of the following best describes a "simulation model" vs a "theoretical model"?',
                'opts' => [
                    ['They are identical in every way', false],
                    ['A simulation model is run computationally over time; a theoretical model derives results analytically from equations', true],
                    ['A theoretical model always uses Python; a simulation model uses mathematics', false],
                    ['A simulation model is always more accurate than a theoretical model', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 6 — Modeling and Simulation (Newbie).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Newbie");
    }
}