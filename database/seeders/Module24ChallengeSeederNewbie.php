<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module24ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 24 — Sequential Decision Making (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Sequential Decision Making',
            'description'           => 'Test your very first knowledge of sequential decision making — what it means, why it matters, and the basic vocabulary every aspiring RL practitioner needs. No prior experience assumed!',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 24,
        ]);

        $this->command->info("Seeding 50 newbie-friendly sequential decision making questions...");

        $qaData = [

            // ── WHAT IS SEQUENTIAL DECISION MAKING ────────────────────────
            [
                'q' => 'What does "sequential decision making" mean?',
                'opts' => [
                    ['Making one single decision and stopping', false],
                    ['Making a series of decisions over time where each decision affects future options', true],
                    ['Sorting decisions alphabetically', false],
                    ['Making decisions without any data', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of sequential decision making?',
                'opts' => [
                    ['Choosing a shirt color once in the morning', false],
                    ['A chess player choosing moves one at a time to win the game', true],
                    ['Reading the temperature from a thermometer', false],
                    ['Counting items in a list', false],
                ],
            ],
            [
                'q' => 'In sequential decision making, an "agent" is:',
                'opts' => [
                    ['A database table', false],
                    ['The entity that takes actions and learns from the environment', true],
                    ['A type of reward signal', false],
                    ['The goal the agent tries to avoid', false],
                ],
            ],
            [
                'q' => 'The "environment" in sequential decision making is:',
                'opts' => [
                    ['The agent\'s memory', false],
                    ['Everything outside the agent that the agent interacts with', true],
                    ['The reward function only', false],
                    ['The list of all possible actions', false],
                ],
            ],
            [
                'q' => 'A "reward" in reinforcement learning is:',
                'opts' => [
                    ['A penalty for making a wrong move', false],
                    ['A numerical signal that tells the agent how good its action was', true],
                    ['The number of steps the agent took', false],
                    ['The state the agent is currently in', false],
                ],
            ],
            [
                'q' => 'The goal of a reinforcement learning agent is to:',
                'opts' => [
                    ['Minimize the number of steps taken', false],
                    ['Memorize all possible states', false],
                    ['Maximize the total cumulative reward over time', true],
                    ['Avoid all states with negative rewards', false],
                ],
            ],
            [
                'q' => 'A "state" in a sequential decision problem describes:',
                'opts' => [
                    ['The agent\'s final answer', false],
                    ['The current situation or configuration of the environment', true],
                    ['The total reward collected so far', false],
                    ['The number of actions available', false],
                ],
            ],
            [
                'q' => 'An "action" in reinforcement learning is:',
                'opts' => [
                    ['A reward signal from the environment', false],
                    ['Something the agent chooses to do that affects the environment', true],
                    ['A description of the current state', false],
                    ['The final output of training', false],
                ],
            ],

            // ── MARKOV DECISION PROCESSES ──────────────────────────────────
            [
                'q' => 'A Markov Decision Process (MDP) is a mathematical framework for:',
                'opts' => [
                    ['Sorting data in a database', false],
                    ['Modeling sequential decision making under uncertainty', true],
                    ['Drawing flowcharts', false],
                    ['Training neural networks only', false],
                ],
            ],
            [
                'q' => 'The "Markov property" means that:',
                'opts' => [
                    ['The agent must remember all past states', false],
                    ['The future depends only on the current state, not the history of past states', true],
                    ['All rewards are the same regardless of state', false],
                    ['Actions have no effect on future states', false],
                ],
            ],
            [
                'q' => 'In an MDP, a "policy" is:',
                'opts' => [
                    ['A list of all rewards received', false],
                    ['A rule that tells the agent which action to take in each state', true],
                    ['The transition function of the environment', false],
                    ['The initial state of the environment', false],
                ],
            ],
            [
                'q' => 'A "transition function" in an MDP describes:',
                'opts' => [
                    ['How the agent chooses its action', false],
                    ['The probability of moving from one state to another after taking an action', true],
                    ['The total reward accumulated', false],
                    ['The learning rate of the algorithm', false],
                ],
            ],
            [
                'q' => 'The four main components of an MDP are:',
                'opts' => [
                    ['Model, Loss, Optimizer, Data', false],
                    ['States, Actions, Rewards, and Transition Probabilities', true],
                    ['Encoder, Decoder, Attention, Output', false],
                    ['Input, Hidden Layer, Output, Bias', false],
                ],
            ],
            [
                'q' => 'A "discount factor" (γ, gamma) in RL is used to:',
                'opts' => [
                    ['Reduce the number of states', false],
                    ['Make future rewards worth less than immediate rewards', true],
                    ['Increase the learning speed', false],
                    ['Remove negative rewards', false],
                ],
            ],
            [
                'q' => 'If γ = 0, the agent cares about:',
                'opts' => [
                    ['All future rewards equally', false],
                    ['Only the very next immediate reward', true],
                    ['No rewards at all', false],
                    ['Only the final reward at the end', false],
                ],
            ],
            [
                'q' => 'If γ = 1, the agent cares about:',
                'opts' => [
                    ['Only immediate rewards', false],
                    ['All future rewards equally, as if time doesn\'t matter', true],
                    ['No future rewards at all', false],
                    ['Only negative rewards', false],
                ],
            ],

            // ── DYNAMIC PROGRAMMING ────────────────────────────────────────
            [
                'q' => 'Dynamic programming in RL solves problems by:',
                'opts' => [
                    ['Randomly trying all possible actions', false],
                    ['Breaking the problem into smaller subproblems and using the known model of the environment', true],
                    ['Training a neural network from scratch', false],
                    ['Only using data from past experiences', false],
                ],
            ],
            [
                'q' => 'The "value function" V(s) tells us:',
                'opts' => [
                    ['The immediate reward at state s', false],
                    ['The expected total cumulative reward starting from state s following a policy', true],
                    ['The number of actions available at state s', false],
                    ['The probability of reaching state s', false],
                ],
            ],
            [
                'q' => 'Policy iteration alternates between which two steps?',
                'opts' => [
                    ['Collecting data and training a network', false],
                    ['Policy evaluation (computing value of current policy) and policy improvement (making policy greedy)', true],
                    ['Exploration and exploitation only', false],
                    ['Forward pass and backward pass', false],
                ],
            ],
            [
                'q' => 'Value iteration updates the value function by:',
                'opts' => [
                    ['Taking the action with the lowest reward', false],
                    ['Repeatedly applying the Bellman optimality equation until values converge', true],
                    ['Randomly resetting state values', false],
                    ['Only updating the terminal state', false],
                ],
            ],

            // ── MONTE CARLO METHODS ────────────────────────────────────────
            [
                'q' => 'Monte Carlo methods in RL learn from:',
                'opts' => [
                    ['The exact mathematical model of the environment', false],
                    ['Complete episodes of experience (from start to finish)', true],
                    ['A single step of interaction', false],
                    ['Only the first reward received', false],
                ],
            ],
            [
                'q' => 'A "complete episode" in RL means:',
                'opts' => [
                    ['The agent takes exactly one action', false],
                    ['The agent interacts with the environment until reaching a terminal state', true],
                    ['The agent collects 100 rewards', false],
                    ['The model finishes training', false],
                ],
            ],
            [
                'q' => 'Monte Carlo methods can be used when:',
                'opts' => [
                    ['The exact model of the environment is known', false],
                    ['The environment model is unknown and only experience (sampled episodes) is available', true],
                    ['The environment never has a terminal state', false],
                    ['The reward is always zero', false],
                ],
            ],
            [
                'q' => '"Return" G in RL is defined as:',
                'opts' => [
                    ['The immediate reward only', false],
                    ['The sum of discounted rewards from the current time step to the end of the episode', true],
                    ['The average reward across all episodes', false],
                    ['The difference between two consecutive rewards', false],
                ],
            ],

            // ── TEMPORAL DIFFERENCE & Q-LEARNING ──────────────────────────
            [
                'q' => 'Temporal Difference (TD) learning is different from Monte Carlo because it:',
                'opts' => [
                    ['Requires a complete episode before updating', false],
                    ['Updates value estimates after each individual step, not waiting for the episode to end', true],
                    ['Only works with discrete actions', false],
                    ['Requires a known model of the environment', false],
                ],
            ],
            [
                'q' => 'Q-learning learns a function Q(s, a) that estimates:',
                'opts' => [
                    ['The probability of taking action a in state s', false],
                    ['The expected total reward for taking action a in state s and acting optimally afterwards', true],
                    ['The immediate reward for state s only', false],
                    ['The number of times state s was visited', false],
                ],
            ],
            [
                'q' => 'In Q-learning, the agent\'s optimal action at state s is:',
                'opts' => [
                    ['A random action', false],
                    ['The action with the highest Q-value: argmax_a Q(s, a)', true],
                    ['The action with the lowest Q-value', false],
                    ['Always the first action in the list', false],
                ],
            ],
            [
                'q' => 'The "learning rate" (α) in Q-learning controls:',
                'opts' => [
                    ['The amount of discount on future rewards', false],
                    ['How quickly the Q-values are updated toward new information', true],
                    ['The number of episodes to run', false],
                    ['Whether the environment is stochastic', false],
                ],
            ],
            [
                'q' => '"Exploration" in RL means:',
                'opts' => [
                    ['Always choosing the action with the highest known reward', false],
                    ['Trying new or less-visited actions to discover potentially better rewards', true],
                    ['Stopping training early', false],
                    ['Using the environment model to plan ahead', false],
                ],
            ],
            [
                'q' => '"Exploitation" in RL means:',
                'opts' => [
                    ['Trying random new actions', false],
                    ['Using the best known action based on current knowledge', true],
                    ['Exploring all states equally', false],
                    ['Ignoring all rewards', false],
                ],
            ],

            // ── DEEP Q-NETWORKS ────────────────────────────────────────────
            [
                'q' => 'A Deep Q-Network (DQN) replaces the Q-table with:',
                'opts' => [
                    ['A lookup table with more rows', false],
                    ['A neural network that approximates Q-values for each state-action pair', true],
                    ['A random forest', false],
                    ['A sorted list of rewards', false],
                ],
            ],
            [
                'q' => 'DQN was famously used to:',
                'opts' => [
                    ['Solve linear regression problems', false],
                    ['Play Atari video games at superhuman level using raw pixel inputs', true],
                    ['Train a chatbot', false],
                    ['Classify images of cats and dogs', false],
                ],
            ],
            [
                'q' => '"Experience replay" in DQN stores past experiences and:',
                'opts' => [
                    ['Deletes them after use', false],
                    ['Randomly samples them to train the network, breaking correlations between consecutive samples', true],
                    ['Replays only the most recent experience', false],
                    ['Uses them to reset the environment', false],
                ],
            ],
            [
                'q' => 'A "target network" in DQN is a copy of the main network used to:',
                'opts' => [
                    ['Double the number of parameters', false],
                    ['Provide stable target Q-values during training, updated less frequently', true],
                    ['Generate random actions for exploration', false],
                    ['Store the replay buffer', false],
                ],
            ],

            // ── POLICY GRADIENT ────────────────────────────────────────────
            [
                'q' => 'Policy gradient methods directly optimize:',
                'opts' => [
                    ['The Q-value function', false],
                    ['The policy (the mapping from states to actions) using gradient ascent on expected return', true],
                    ['The transition probabilities', false],
                    ['The discount factor γ', false],
                ],
            ],
            [
                'q' => 'The REINFORCE algorithm is a basic policy gradient method that uses:',
                'opts' => [
                    ['Dynamic programming with a full model', false],
                    ['Full episode returns to estimate the gradient of the policy', true],
                    ['Only the last reward in an episode', false],
                    ['A neural network for the environment model', false],
                ],
            ],
            [
                'q' => 'A "stochastic policy" outputs:',
                'opts' => [
                    ['The exact best action deterministically', false],
                    ['A probability distribution over actions', true],
                    ['The value of each state', false],
                    ['A fixed action regardless of state', false],
                ],
            ],

            // ── ACTOR-CRITIC & PPO ─────────────────────────────────────────
            [
                'q' => 'An "Actor-Critic" method has two components. The "actor" is responsible for:',
                'opts' => [
                    ['Estimating the value of states', false],
                    ['Selecting actions by maintaining and updating the policy', true],
                    ['Storing experiences in the replay buffer', false],
                    ['Computing the discount factor', false],
                ],
            ],
            [
                'q' => 'In Actor-Critic, the "critic" is responsible for:',
                'opts' => [
                    ['Choosing actions to take', false],
                    ['Evaluating how good the actor\'s action was by estimating the value function', true],
                    ['Storing the episode history', false],
                    ['Setting the learning rate', false],
                ],
            ],
            [
                'q' => 'PPO stands for:',
                'opts' => [
                    ['Partial Policy Optimization', false],
                    ['Proximal Policy Optimization', true],
                    ['Parallel Processing Output', false],
                    ['Policy Propagation Operator', false],
                ],
            ],
            [
                'q' => 'PPO is popular because it:',
                'opts' => [
                    ['Requires no hyperparameter tuning', false],
                    ['Balances stable training with good performance by clipping policy updates to prevent large changes', true],
                    ['Only works for discrete action spaces', false],
                    ['Uses no neural networks', false],
                ],
            ],

            // ── MULTI-ARMED BANDITS ────────────────────────────────────────
            [
                'q' => 'The multi-armed bandit problem is a simplified version of RL where:',
                'opts' => [
                    ['The agent has many arms and must choose the fastest', false],
                    ['There is no state — the agent only decides which action (arm) to pull to maximize rewards', true],
                    ['The agent plays against multiple opponents', false],
                    ['All arms always give the same reward', false],
                ],
            ],
            [
                'q' => 'The exploration-exploitation dilemma in bandits is:',
                'opts' => [
                    ['Whether to use a fast or slow computer', false],
                    ['The trade-off between trying new arms (exploration) and using the best known arm (exploitation)', true],
                    ['How many rewards to keep in memory', false],
                    ['Choosing between discrete and continuous actions', false],
                ],
            ],
            [
                'q' => 'The ε-greedy strategy for bandits:',
                'opts' => [
                    ['Always picks the best known arm', false],
                    ['Picks a random arm with probability ε and the best known arm with probability 1 − ε', true],
                    ['Only picks random arms', false],
                    ['Never explores new arms', false],
                ],
            ],
            [
                'q' => 'UCB stands for:',
                'opts' => [
                    ['Uncertain Choice Balancing', false],
                    ['Upper Confidence Bound', true],
                    ['Uniform Cumulative Bonus', false],
                    ['Unit Control Block', false],
                ],
            ],

            // ── PARTIALLY OBSERVABLE MDPs ──────────────────────────────────
            [
                'q' => 'A "Partially Observable MDP" (POMDP) is different from a regular MDP because:',
                'opts' => [
                    ['The rewards are hidden', false],
                    ['The agent cannot observe the full true state — it only gets partial observations', true],
                    ['There are no actions available', false],
                    ['The transitions are always deterministic', false],
                ],
            ],
            [
                'q' => 'A robot navigating with a camera that only sees what\'s directly in front of it is facing:',
                'opts' => [
                    ['A fully observable MDP', false],
                    ['A partially observable environment', true],
                    ['A multi-armed bandit problem', false],
                    ['A deterministic MDP', false],
                ],
            ],
            [
                'q' => 'In a POMDP, the agent maintains a "belief state" which is:',
                'opts' => [
                    ['A deterministic memory of all past states', false],
                    ['A probability distribution over possible true states given the observations so far', true],
                    ['The set of all actions ever taken', false],
                    ['The policy function itself', false],
                ],
            ],

            // ── GENERAL REVIEW ─────────────────────────────────────────────
            [
                'q' => 'The difference between "model-based" and "model-free" RL is:',
                'opts' => [
                    ['Model-based uses neural networks; model-free does not', false],
                    ['Model-based learns or uses a model of the environment; model-free learns directly from experience without a model', true],
                    ['Model-free is always better', false],
                    ['Model-based only works for discrete environments', false],
                ],
            ],
            [
                'q' => 'An "episode" in RL is:',
                'opts' => [
                    ['One single step in the environment', false],
                    ['A complete sequence of states, actions, and rewards from start to a terminal state', true],
                    ['The final trained policy', false],
                    ['A batch of training data', false],
                ],
            ],
            [
                'q' => 'The Bellman equation relates:',
                'opts' => [
                    ['The reward to the learning rate', false],
                    ['The value of a state to the immediate reward plus the discounted value of the next state', true],
                    ['The policy to the transition probabilities directly', false],
                    ['The discount factor to the number of episodes', false],
                ],
            ],
            [
                'q' => 'Which of the following is a real-world application of reinforcement learning?',
                'opts' => [
                    ['Sorting a list of numbers', false],
                    ['Training a robot arm to pick and place objects', true],
                    ['Calculating the mean of a dataset', false],
                    ['Drawing a bar chart', false],
                ],
            ],
            [
                'q' => 'AlphaGo, the AI that defeated the world champion at the board game Go, used:',
                'opts' => [
                    ['Only supervised learning from human games', false],
                    ['Reinforcement learning combined with Monte Carlo Tree Search and deep neural networks', true],
                    ['Only Q-tables', false],
                    ['Linear regression', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 24 — Sequential Decision Making (Newbie).");
    }
}