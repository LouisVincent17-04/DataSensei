<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module24ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 24 — Sequential Decision Making (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Sequential Decision Making',
            'description'           => 'Apply your understanding of MDPs, Bellman equations, and basic RL algorithms analytically. Expect scenario-based reasoning, simple value calculations, and algorithm tracing.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 700,
            'order_index'           => 24,
        ]);

        $this->command->info("Seeding University Student sequential decision making questions...");

        $qaData = [

            // ── MDP FRAMEWORK ─────────────────────────────────────────────
            [
                'q' => 'An MDP is defined as a 5-tuple (S, A, P, R, γ). What does P represent?',
                'opts' => [
                    ['The policy — which action to take in each state', false],
                    ['The transition probability P(s\'|s, a) — the chance of reaching s\' from s by taking action a', true],
                    ['The performance metric of the agent', false],
                    ['The priority of each state', false],
                ],
            ],
            [
                'q' => 'A grid world has 4 states. The agent is in state S1 and takes action "right". With probability 0.8 it moves to S2, and with probability 0.2 it stays in S1.\n\nP(S2 | S1, right) = ?',
                'opts' => [
                    ['0.2', false],
                    ['1.0', false],
                    ['0.8', true],
                    ['0.5', false],
                ],
            ],
            [
                'q' => 'A deterministic policy π maps each state to:',
                'opts' => [
                    ['A probability distribution over actions', false],
                    ['Exactly one specific action', true],
                    ['A value estimate', false],
                    ['A set of all possible actions', false],
                ],
            ],
            [
                'q' => 'The "optimal policy" π* is the policy that:',
                'opts' => [
                    ['Takes the fewest steps to reach the goal', false],
                    ['Maximizes the expected cumulative discounted reward from every state', true],
                    ['Always selects random actions', false],
                    ['Minimizes the immediate reward', false],
                ],
            ],
            [
                'q' => 'An "episodic" task is one that:',
                'opts' => [
                    ['Continues indefinitely with no end', false],
                    ['Has a clear terminal state and resets after each episode', true],
                    ['Only runs for exactly 100 steps', false],
                    ['Never gives any rewards', false],
                ],
            ],
            [
                'q' => 'A "continuing" (non-episodic) task is one that:',
                'opts' => [
                    ['Ends after a fixed number of steps', false],
                    ['Runs indefinitely without a natural terminal state', true],
                    ['Resets after every action', false],
                    ['Always converges in 10 steps', false],
                ],
            ],

            // ── BELLMAN EQUATION ──────────────────────────────────────────
            [
                'q' => 'The Bellman expectation equation for V^π(s) is:\n\n  V^π(s) = Σ_a π(a|s) · Σ_{s\'} P(s\'|s,a) · [R(s,a,s\') + γ·V^π(s\')]\n\nIf there is only one action, R = 5, γ = 0.9, and V^π(s\') = 10:\n\nV^π(s) = ?',
                'opts' => [
                    ['5', false],
                    ['14', true],
                    ['9', false],
                    ['15', false],
                ],
            ],
            [
                'q' => 'The Bellman optimality equation for V*(s) takes the maximum over actions:\n\n  V*(s) = max_a Σ_{s\'} P(s\'|s,a) · [R + γ·V*(s\')]\n\nFor state s with two actions:\n  Action A: R=2, next V* = 8, γ=0.5 → 2 + 0.5×8 = 6\n  Action B: R=4, next V* = 4, γ=0.5 → 4 + 0.5×4 = 6\n\nV*(s) = ?',
                'opts' => [
                    ['4', false],
                    ['8', false],
                    ['6', true],
                    ['12', false],
                ],
            ],
            [
                'q' => 'The Q-function (action-value function) Q*(s, a) relates to V*(s) by:\n\n  V*(s) = max_a Q*(s, a)\n\nIf Q*(s, left) = 3, Q*(s, right) = 7, Q*(s, up) = 5, then V*(s) = ?',
                'opts' => [
                    ['3', false],
                    ['5', false],
                    ['7', true],
                    ['15', false],
                ],
            ],
            [
                'q' => 'A discounted return with γ = 0.9 for rewards [10, 5, 2] (at t=0, 1, 2) is:\n\n  G = r₀ + γ·r₁ + γ²·r₂',
                'opts' => [
                    ['17', false],
                    ['14.12', false],
                    ['15.12', false],
                    ['16.12', true],
                ],
            ],

            // ── DYNAMIC PROGRAMMING ────────────────────────────────────────
            [
                'q' => 'Policy evaluation computes V^π(s) for a given policy π. It does this by:',
                'opts' => [
                    ['Running the policy once and recording rewards', false],
                    ['Repeatedly applying the Bellman expectation equation until values converge', true],
                    ['Taking the maximum over all actions', false],
                    ['Randomly sampling transitions', false],
                ],
            ],
            [
                'q' => 'Policy improvement makes the policy greedy with respect to the current value function:\n\n  π\'(s) = argmax_a Σ_{s\'} P(s\'|s,a) · [R + γ·V^π(s\')]\n\nThis step improves or maintains policy quality because of:',
                'opts' => [
                    ['The policy improvement theorem — a greedy policy is at least as good as the original', true],
                    ['The law of large numbers', false],
                    ['The convergence of neural networks', false],
                    ['The discount factor being less than 1', false],
                ],
            ],
            [
                'q' => 'Value iteration differs from policy iteration in that it:',
                'opts' => [
                    ['Requires a complete policy evaluation before each improvement step', false],
                    ['Combines the evaluation and improvement into a single update using the Bellman optimality operator', true],
                    ['Only works for deterministic environments', false],
                    ['Uses sampled data instead of the known model', false],
                ],
            ],
            [
                'q' => 'Both value iteration and policy iteration require knowing P(s\'|s,a) and R(s,a). This means they are:',
                'opts' => [
                    ['Model-free methods', false],
                    ['Model-based methods — they require a complete model of the environment', true],
                    ['Policy gradient methods', false],
                    ['Sampling-based methods', false],
                ],
            ],

            // ── MONTE CARLO ────────────────────────────────────────────────
            [
                'q' => 'First-visit Monte Carlo estimates V(s) as the average return from episodes where:',
                'opts' => [
                    ['State s was visited at least once anywhere in the episode', false],
                    ['The first time state s is visited in each episode', true],
                    ['State s was the starting state of the episode', false],
                    ['State s appears at the end of the episode', false],
                ],
            ],
            [
                'q' => 'Every-visit Monte Carlo estimates V(s) by averaging returns from:',
                'opts' => [
                    ['Only the first visit to s per episode', false],
                    ['Every time state s is visited across all episodes', true],
                    ['Only terminal state visits', false],
                    ['A random sample of states', false],
                ],
            ],
            [
                'q' => 'Monte Carlo methods have HIGH variance compared to TD because:',
                'opts' => [
                    ['They use the exact model of the environment', false],
                    ['Returns are computed from complete episodes, and the full return can vary widely across episodes', true],
                    ['They update after every single step', false],
                    ['They only use the immediate reward', false],
                ],
            ],
            [
                'q' => 'TD(0) has lower variance than Monte Carlo but is said to have BIAS because:',
                'opts' => [
                    ['It uses a neural network approximator', false],
                    ['It bootstraps — using its own estimated value of the next state instead of the true return', true],
                    ['It uses random sampling', false],
                    ['It requires a complete model of the environment', false],
                ],
            ],

            // ── TD & Q-LEARNING ────────────────────────────────────────────
            [
                'q' => 'The TD(0) update rule is:\n\n  V(s) ← V(s) + α[r + γ·V(s\') − V(s)]\n\nThe term [r + γ·V(s\') − V(s)] is called the:',
                'opts' => [
                    ['Policy gradient', false],
                    ['TD error (or temporal difference error)', true],
                    ['Discount factor', false],
                    ['Advantage function', false],
                ],
            ],
            [
                'q' => 'Q-learning is "off-policy" because:',
                'opts' => [
                    ['It learns without any rewards', false],
                    ['It learns the optimal Q-function regardless of the behavior policy used to collect data', true],
                    ['It does not use a policy at all', false],
                    ['It trains on a separate dataset unrelated to the environment', false],
                ],
            ],
            [
                'q' => 'SARSA is "on-policy" because:',
                'opts' => [
                    ['It uses a separate target policy', false],
                    ['It updates Q-values using the action actually taken by the current behavior policy, not the greedy optimal action', true],
                    ['It requires the full episode before updating', false],
                    ['It always uses a random policy', false],
                ],
            ],
            [
                'q' => 'The Q-learning update is:\n\n  Q(s,a) ← Q(s,a) + α[r + γ·max_{a\'} Q(s\',a\') − Q(s,a)]\n\nWith α=0.1, γ=0.9, current Q(s,a)=5, r=3, max Q(s\',a\')=8:\n\nNew Q(s,a) = ?',
                'opts' => [
                    ['5.42', false],
                    ['5.52', true],
                    ['5.30', false],
                    ['6.20', false],
                ],
            ],

            // ── EXPLORATION STRATEGIES ─────────────────────────────────────
            [
                'q' => 'With ε = 0.1 in an ε-greedy strategy, what fraction of the time does the agent explore?',
                'opts' => [
                    ['90%', false],
                    ['1%', false],
                    ['10%', true],
                    ['50%', false],
                ],
            ],
            [
                'q' => '"Epsilon decay" is the practice of:',
                'opts' => [
                    ['Increasing ε over time to explore more', false],
                    ['Gradually reducing ε over training so the agent explores less and exploits more as it learns', true],
                    ['Setting ε to 0 from the start', false],
                    ['Randomly changing ε at each step', false],
                ],
            ],
            [
                'q' => 'UCB (Upper Confidence Bound) selects the arm with the highest:\n\n  Q̄(a) + c · √(ln(t) / N(a))\n\nThe term c · √(ln(t) / N(a)) serves as a bonus for:',
                'opts' => [
                    ['Arms with the highest estimated reward', false],
                    ['Arms that have been pulled fewer times — favoring less-explored options', true],
                    ['Arms with the smallest variance', false],
                    ['Arms selected most recently', false],
                ],
            ],
            [
                'q' => 'Thompson Sampling is a Bayesian exploration strategy that:',
                'opts' => [
                    ['Always selects the arm with the highest mean reward estimate', false],
                    ['Samples a reward estimate from each arm\'s posterior distribution and selects the arm with the highest sample', true],
                    ['Uses a fixed ε throughout training', false],
                    ['Only explores for the first 10 steps', false],
                ],
            ],

            // ── DEEP Q-NETWORKS ────────────────────────────────────────────
            [
                'q' => 'The "deadly triad" in deep RL refers to instability caused by combining:',
                'opts' => [
                    ['Too many layers, too many parameters, too many episodes', false],
                    ['Function approximation, bootstrapping, and off-policy learning simultaneously', true],
                    ['High learning rates, high discount factors, and high epsilon', false],
                    ['Policy gradients, value functions, and actor-critic methods', false],
                ],
            ],
            [
                'q' => 'DQN\'s target network is updated:',
                'opts' => [
                    ['At every gradient step', false],
                    ['Periodically (e.g., every C steps) by copying the online network weights', true],
                    ['Only at the end of training', false],
                    ['Whenever the loss exceeds a threshold', false],
                ],
            ],
            [
                'q' => 'Double DQN addresses the "overestimation bias" in standard DQN by:',
                'opts' => [
                    ['Using two separate replay buffers', false],
                    ['Using the online network to select the best action but the target network to evaluate its Q-value', true],
                    ['Doubling the number of training episodes', false],
                    ['Running two independent DQN agents', false],
                ],
            ],

            // ── POLICY GRADIENT & ACTOR-CRITIC ────────────────────────────
            [
                'q' => 'The policy gradient theorem states that the gradient of expected return with respect to policy parameters θ is:\n\n  ∇_θ J(θ) ∝ Σ_s d^π(s) Σ_a Q^π(s,a) · ∇_θ π_θ(a|s)\n\nThis tells us to increase the probability of actions that have:',
                'opts' => [
                    ['Low Q-values', false],
                    ['High Q-values (positive expected return)', true],
                    ['Zero Q-values', false],
                    ['Negative Q-values', false],
                ],
            ],
            [
                'q' => 'The "advantage function" A(s,a) = Q(s,a) − V(s) measures:',
                'opts' => [
                    ['The total return of the episode', false],
                    ['How much better action a is compared to the average action in state s', true],
                    ['The discount applied to future rewards', false],
                    ['The difference between two consecutive states', false],
                ],
            ],
            [
                'q' => 'An advantage value A(s, a) < 0 means the action a is:',
                'opts' => [
                    ['Better than average in state s', false],
                    ['Worse than average in state s — the policy should reduce its probability', true],
                    ['The optimal action in state s', false],
                    ['Not a valid action in state s', false],
                ],
            ],
            [
                'q' => 'PPO clips the policy update ratio r_t(θ) = π_θ(a|s) / π_θ_old(a|s) to the range [1−ε, 1+ε].\n\nWith ε = 0.2, a ratio of 1.5 is clipped to:',
                'opts' => [
                    ['0.8', false],
                    ['1.5', false],
                    ['1.2', true],
                    ['2.0', false],
                ],
            ],

            // ── MULTI-ARMED BANDITS ────────────────────────────────────────
            [
                'q' => 'You have 3 bandit arms with estimated values:\n  Arm 1: Q̄ = 2.5\n  Arm 2: Q̄ = 4.1\n  Arm 3: Q̄ = 3.8\n\nUsing a purely greedy strategy (ε=0), which arm is selected?',
                'opts' => [
                    ['Arm 1', false],
                    ['Arm 3', false],
                    ['Arm 2', true],
                    ['A random arm', false],
                ],
            ],
            [
                'q' => '"Regret" in the bandit problem is defined as:',
                'opts' => [
                    ['The number of wrong actions taken', false],
                    ['The cumulative difference between the optimal reward and the reward actually obtained', true],
                    ['The total number of explorations', false],
                    ['The variance in rewards across arms', false],
                ],
            ],
            [
                'q' => 'Contextual bandits differ from standard bandits because:',
                'opts' => [
                    ['They always have exactly 10 arms', false],
                    ['They receive a "context" (state/feature vector) at each step that influences which arm is optimal', true],
                    ['They use neural networks only', false],
                    ['They cannot model exploration', false],
                ],
            ],

            // ── POMDPs & REAL-WORLD ────────────────────────────────────────
            [
                'q' => 'In a POMDP, an observation function O(o | s\', a) gives:',
                'opts' => [
                    ['The reward for reaching state s\'', false],
                    ['The probability of receiving observation o after taking action a and landing in state s\'', true],
                    ['The optimal action to take in state s\'', false],
                    ['The transition probability between states', false],
                ],
            ],
            [
                'q' => 'A "belief update" in a POMDP uses Bayes\' theorem to update:\n\n  b\'(s\') ∝ O(o|s\',a) · Σ_s P(s\'|s,a) · b(s)\n\nThis belief represents:',
                'opts' => [
                    ['The agent\'s best guess of the exact current state', false],
                    ['The posterior probability distribution over hidden states given all past observations', true],
                    ['The immediate reward expected from each state', false],
                    ['The discount factor applied to future observations', false],
                ],
            ],
            [
                'q' => 'Real-world RL challenges include all EXCEPT:',
                'opts' => [
                    ['Sparse rewards making learning slow', false],
                    ['High-dimensional state and action spaces', false],
                    ['Safety constraints during exploration', false],
                    ['The environment always being fully observable and stationary', true],
                ],
            ],
            [
                'q' => 'Transfer learning in RL refers to:',
                'opts' => [
                    ['Moving the agent from one computer to another', false],
                    ['Using knowledge or policies learned in one environment to accelerate learning in a related new environment', true],
                    ['Transferring replay buffer data between agents', false],
                    ['Copying neural network weights randomly', false],
                ],
            ],
            [
                'q' => 'Sparse rewards in RL mean:',
                'opts' => [
                    ['Rewards are given every step', false],
                    ['The agent only receives a reward at rare moments (e.g., at the end of a game), making it hard to learn which actions were responsible', true],
                    ['All rewards are the same constant value', false],
                    ['Rewards are never negative', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 24 — Sequential Decision Making (University Student).");
    }
}