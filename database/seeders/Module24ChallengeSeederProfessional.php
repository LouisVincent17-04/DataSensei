<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module24ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 24 — Sequential Decision Making (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Sequential Decision Making',
            'description'           => 'Production-grade RL: system-level training failures, safety constraints, sample efficiency, sim-to-real transfer, multi-agent dynamics, and real-world deployment edge cases.',
            'time_limit_seconds'    => 2100,
            'base_xp'               => 1400,
            'order_index'           => 24,
        ]);

        $this->command->info("Seeding Professional sequential decision making questions...");

        $qaData = [

            // ── PRODUCTION RL FAILURES ────────────────────────────────────
            [
                'q' => "```python\n# Production RL training log:\n# Episode 100:  avg_reward=15.2,  policy_entropy=2.31\n# Episode 500:  avg_reward=48.7,  policy_entropy=1.82\n# Episode 1000: avg_reward=61.3,  policy_entropy=0.41\n# Episode 1200: avg_reward=23.1,  policy_entropy=0.08\n# Episode 1500: avg_reward=18.4,  policy_entropy=0.03\n\n# Config: PPO, lr=3e-4, clip_eps=0.2, entropy_coeff=0\n```\n\nThe policy entropy collapsed near episode 1000, followed by performance regression. This is called 'entropy collapse' or 'premature convergence'.\n\nThe correct fix is:",
                'opts' => [
                    ['Increase the learning rate to escape the local optimum', false],
                    ['Add an entropy bonus coefficient (e.g. 0.01) to the loss, and/or use learning rate scheduling — entropy_coeff=0 removed the exploration incentive entirely, causing the policy to converge deterministically too early', true],
                    ['Reduce the PPO clip_eps to slow down updates', false],
                    ['Use a smaller neural network to reduce overfitting', false],
                ],
            ],
            [
                'q' => "```python\n# Reward hacking in a simulated robot locomotion task\n# Objective: maximize forward velocity\n# Observed behavior after training:\n#   Agent learned to make the robot fall forward repeatedly\n#   (falling counts as forward velocity but damages the robot)\n#   Reward per episode: 847.3 (extremely high)\n#   Actual useful locomotion: 0 steps\n```\n\nThis is a classic example of reward hacking / Goodhart's Law in RL:\n'When a measure becomes a target, it ceases to be a good measure.'\n\nThe correct engineering approach to prevent this is:",
                'opts' => [
                    ['Train for more episodes to allow the agent to discover the intended behavior', false],
                    ['Use a more carefully specified reward that includes posture stability, energy efficiency, and survival constraints — or use RLHF (human feedback) to evaluate trajectories holistically rather than a single scalar metric', true],
                    ['Add more negative rewards for bad states', false],
                    ['Switch from PPO to SAC', false],
                ],
            ],
            [
                'q' => "```python\n# Multi-environment parallel training\nimport ray\nfrom ray import tune\n\n@ray.remote\nclass RolloutWorker:\n    def collect_rollout(self, policy_weights):\n        self.policy.load_state_dict(policy_weights)\n        return collect_trajectory(self.env, self.policy)\n\n# 32 parallel workers\nworkers = [RolloutWorker.remote() for _ in range(32)]\n\n# Training observation:\n# Workers with stale policy weights (lag = 5-10 update steps)\n# cause off-policy data to contaminate on-policy PPO updates\n```\n\nThe 'policy lag' problem in distributed on-policy RL means that data collected by workers with weights from 5-10 updates ago is technically off-policy for PPO.\n\nThe production solution used in IMPALA and VTRACE is:",
                'opts' => [
                    ['Discard all data collected with stale weights', false],
                    ['Use importance sampling with V-trace clipping to correct for the off-policy data — the V-trace operator clips importance weights to reduce variance from large policy lags while preserving unbiasedness', true],
                    ['Synchronize all workers at every gradient step', false],
                    ['Reduce the number of workers to 4 to minimize lag', false],
                ],
            ],

            // ── SAFE RL ───────────────────────────────────────────────────
            [
                'q' => "```python\n# Safe RL with Constrained MDP (CMDP)\n# Maximize: E[Σ γ^t r_t]\n# Subject to: E[Σ γ^t c_t] ≤ d  (safety constraint)\n\nclass CPO_Agent:  # Constrained Policy Optimization\n    def update(self, batch):\n        policy_gradient = compute_policy_gradient(batch)\n        constraint_gradient = compute_constraint_gradient(batch)\n        # Project policy update to satisfy constraint\n        update = self.project_onto_constraint_manifold(\n            policy_gradient,\n            constraint_gradient,\n            self.safety_budget\n        )\n        self.apply_update(update)\n```\n\nCPO (Constrained Policy Optimization) solves a CMDP by updating the policy in the direction that maximizes reward while:",
                'opts' => [
                    ['Completely ignoring the safety constraint during updates', false],
                    ['Projecting the policy gradient onto the feasible set defined by the safety constraint — ensuring each update never increases expected constraint cost beyond the budget d', true],
                    ['Only applying the constraint every 100 episodes', false],
                    ['Using the safety budget to scale the learning rate', false],
                ],
            ],
            [
                'q' => "A real-world autonomous driving RL agent is tested in simulation.\n\nSafety metrics:\n  - Collision rate in sim: 0.002%\n  - Constraint budget: 0.01% collision rate\n  - Status: ✅ Passes safety test\n\nAfter real-world deployment:\n  - Collision rate: 3.1%\n  - Status: ❌ Catastrophic failure\n\nThe primary cause of the sim-to-real safety gap is:",
                'opts' => [
                    ['The real world has more states than the simulation', false],
                    ['Distribution shift — the simulated environment underrepresents long-tail edge cases (unusual weather, rare pedestrian behaviors, sensor noise) that occur in reality; safety constraints met in simulation do not transfer', true],
                    ['The collision reward was set incorrectly', false],
                    ['The agent needed more training epochs', false],
                ],
            ],
            [
                'q' => "```python\n# Shield-based safe exploration\nclass SafetyShield:\n    def __init__(self, safety_model):\n        self.safety_model = safety_model\n\n    def filter_action(self, state, proposed_action):\n        if self.safety_model.is_safe(state, proposed_action):\n            return proposed_action\n        else:\n            return self.safety_model.get_safe_action(state)\n\nagent_action = policy.act(state)\nactual_action = shield.filter_action(state, agent_action)\n```\n\nA safety shield guarantees safe behavior during deployment, but its practical limitation is:",
                'opts' => [
                    ['The shield makes training slower', false],
                    ['The safety model itself must be verified correct — if the safety model has errors or incomplete coverage of dangerous states, the shield provides false assurance of safety', true],
                    ['The agent cannot learn from shielded actions', false],
                    ['Shields only work in discrete action spaces', false],
                ],
            ],

            // ── SAMPLE EFFICIENCY & REAL-WORLD DEPLOYMENT ─────────────────
            [
                'q' => "```python\n# Sample efficiency comparison for a robotic manipulation task:\n# Method          | Steps to solve | Wall-clock time\n# ----------------+----------------+----------------\n# PPO (on-policy) | 50M steps      | 14 hours\n# SAC (off-policy)| 1M steps       | 1.5 hours\n# MBPO (model-based)| 100K steps   | 2 hours\n# Human demos+RL  | 10K steps      | 0.5 hours\n\n# Task: pick-and-place in a real robot environment\n# Real robot constraint: max 1000 real steps/hour\n```\n\nGiven the real robot constraint (1000 real steps/hour), PPO (50M steps) would require:\n  50,000,000 / 1,000 = 50,000 hours (~5.7 years)\n\nThe correct approach for real-world robotics is:",
                'opts' => [
                    ['Use PPO with a faster computer', false],
                    ['Use sim-to-real transfer (train in sim, fine-tune on real), offline RL from human demonstrations, or model-based methods — pure on-policy model-free RL is prohibitively sample-inefficient for physical systems', true],
                    ['Reduce the episode length to improve steps/hour', false],
                    ['Run 50,000 robots in parallel', false],
                ],
            ],
            [
                'q' => "```python\n# Offline RL: learning from a fixed dataset of past transitions\n# (no further environment interaction allowed)\n\nclass CQL_Agent:  # Conservative Q-Learning\n    def compute_loss(self, batch):\n        # Standard TD loss:\n        td_loss = bellman_error(batch)\n\n        # CQL regularization:\n        # Penalize Q-values for out-of-distribution actions\n        log_sum_exp_q = torch.logsumexp(Q(states), dim=1).mean()\n        data_q = Q(states, actions).mean()\n        cql_penalty = log_sum_exp_q - data_q\n\n        return td_loss + alpha * cql_penalty\n```\n\nThe CQL penalty penalizes Q-values for actions NOT in the dataset. Without this penalty, offline RL suffers from:",
                'opts' => [
                    ['Overfitting to the most frequent transitions', false],
                    ['Overestimation of Q-values for out-of-distribution actions — the agent learns to exploit its own Q-function\'s extrapolation errors, selecting actions that look great on the Q-function but were never tried in the real environment', true],
                    ['Ignoring rare but important transitions in the dataset', false],
                    ['Converging to the behavior policy instead of the optimal policy', false],
                ],
            ],
            [
                'q' => "```python\n# Sim-to-real transfer with domain randomization\ndef create_randomized_env():\n    return gym.make('RobotArm-v1',\n        gravity=np.random.uniform(9.0, 10.0),\n        link_mass=np.random.uniform(0.9, 1.1),\n        joint_damping=np.random.uniform(0.05, 0.15),\n        sensor_noise=np.random.uniform(0, 0.02),\n        latency_ms=np.random.randint(0, 50)\n    )\n\n# Train across thousands of randomized environments\nenvs = [create_randomized_env() for _ in range(128)]\n```\n\nDomain randomization forces the agent to learn a policy that is robust to parameter variation. The key insight is that if the real robot's parameters fall WITHIN the randomization range:",
                'opts' => [
                    ['The policy will fail because it was trained on too many different environments', false],
                    ['The real robot looks like just one more sample from the training distribution — the policy generalizes to it because it has already learned to handle this range of dynamics', true],
                    ['The policy will always choose the median parameter values', false],
                    ['Domain randomization only works for visual inputs', false],
                ],
            ],

            // ── MULTI-AGENT RL ────────────────────────────────────────────
            [
                'q' => "```python\n# Multi-agent environment: 2 agents compete for resources\n# Agent 0: policy π_0\n# Agent 1: policy π_1\n\n# Training observation:\n# When both agents use independent Q-learning:\n# - Q-values oscillate and never converge\n# - Neither agent finds a stable policy\n```\n\nIndependent Q-learning in multi-agent settings fails to converge because:",
                'opts' => [
                    ['Q-learning cannot handle more than one agent', false],
                    ['From each agent\'s perspective, the environment is non-stationary — the other agent is also learning and changing its policy, violating the stationarity assumption that Q-learning convergence requires', true],
                    ['The replay buffer becomes too large with two agents', false],
                    ['Competing agents always cancel each other\'s gradients', false],
                ],
            ],
            [
                'q' => "```python\n# CTDE: Centralized Training, Decentralized Execution (QMIX)\nclass QMIXMixer(nn.Module):\n    '''Mixes individual Q-values using a monotonic mixing network\n       that conditions on global state during training'''\n\n    def forward(self, agent_qs, global_state):\n        # Weights must be positive (monotonicity constraint)\n        weights = F.elu(self.hyper_w(global_state)) + 1\n        bias = self.hyper_b(global_state)\n        return (agent_qs * weights).sum(-1) + bias\n```\n\nQMIX's monotonicity constraint (positive mixing weights) ensures that the joint greedy action is the same as each agent acting greedily on its own Q_i.\n\nThis property is called the IGM (Individual-Global-Max) condition. Without it:",
                'opts' => [
                    ['The mixer network cannot be trained with backpropagation', false],
                    ['Decentralized execution (each agent picking argmax Q_i) would not reproduce the centrally-optimal joint action — agents would act sub-optimally at deployment time', true],
                    ['The global state cannot be used during training', false],
                    ['Individual agents would receive incorrect reward signals', false],
                ],
            ],
            [
                'q' => "In a cooperative multi-agent RL task (e.g., 8 agents coordinating to achieve a joint goal), the credit assignment problem refers to:\n\n  The team receives a single joint reward: r_team = +1 (success) or 0 (failure)\n  Question: which agents contributed to the success?",
                'opts' => [
                    ['Deciding which agent trains first each episode', false],
                    ['Determining how much credit each individual agent deserves from the joint reward — without per-agent credit signals, agents cannot learn which of their individual actions were helpful or harmful', true],
                    ['Assigning data collection responsibility to agents', false],
                    ['Splitting the team reward equally is always sufficient', false],
                ],
            ],

            // ── PARTIAL OBSERVABILITY IN PRODUCTION ───────────────────────
            [
                'q' => "```python\n# LSTM-based policy for POMDP\nclass RecurrentPolicy(nn.Module):\n    def __init__(self, obs_dim, action_dim, hidden_dim=256):\n        super().__init__()\n        self.lstm = nn.LSTM(obs_dim, hidden_dim, batch_first=True)\n        self.actor = nn.Linear(hidden_dim, action_dim)\n\n    def forward(self, obs_sequence, hidden_state):\n        # obs_sequence: (batch, time, obs_dim)\n        lstm_out, new_hidden = self.lstm(obs_sequence, hidden_state)\n        logits = self.actor(lstm_out)\n        return logits, new_hidden\n\n    def reset_hidden(self, batch_size):\n        return (torch.zeros(1, batch_size, 256),\n                torch.zeros(1, batch_size, 256))\n```\n\nA critical bug when training this recurrent policy with PPO is:",
                'opts' => [
                    ['LSTM is incompatible with the actor-critic architecture', false],
                    ['Hidden states must be correctly managed across episode boundaries — hidden states should be reset at episode starts and carried across rollout time steps, not reset between every time step; incorrect truncation corrupts the agent\'s memory', true],
                    ['The actor linear layer must match the LSTM hidden dimension exactly', false],
                    ['LSTMs cannot handle batch sizes larger than 1', false],
                ],
            ],
            [
                'q' => "```python\n# Belief state tracking in production POMDP\nclass ParticleFilter:\n    def __init__(self, n_particles=1000):\n        self.particles = initialize_particles(n_particles)\n        self.weights = np.ones(n_particles) / n_particles\n\n    def update(self, action, observation):\n        # Propagate particles through transition model\n        self.particles = [transition_sample(p, action)\n                          for p in self.particles]\n        # Weight by observation likelihood\n        self.weights *= [obs_likelihood(p, observation)\n                         for p in self.particles]\n        self.weights /= self.weights.sum()  # normalize\n        # Resample to avoid degeneracy\n        if effective_sample_size(self.weights) < 0.5 * len(self.particles):\n            self.resample()\n```\n\nParticle filter degeneracy (most weights → 0 except a few) is prevented by resampling when ESS is low. In high-dimensional state spaces, particle filters suffer from the 'curse of dimensionality' because:",
                'opts' => [
                    ['The particle filter code has a bug in the weight normalization', false],
                    ['The number of particles required to cover the state space grows exponentially with dimension — 1000 particles that adequately represent a 2D state space are completely inadequate for a 20D state space', true],
                    ['Resampling removes too many particles from the filter', false],
                    ['The observation likelihood function cannot handle high dimensions', false],
                ],
            ],

            // ── REWARD DESIGN & RLHF ──────────────────────────────────────
            [
                'q' => "```python\n# RLHF: Reinforcement Learning from Human Feedback\n# Step 1: Train reward model from human preferences\nclass RewardModel(nn.Module):\n    def forward(self, trajectory):\n        return self.net(trajectory)  # scalar reward score\n\n# Trained on: 50,000 human comparison labels\n# 'Which trajectory is better?' → binary preference\n\n# Step 2: Use PPO to optimize against reward model\n# Step 3: Apply KL penalty to stay close to reference policy:\n# Loss = -R_model(trajectory) + β * KL(π || π_ref)\n```\n\nThe KL divergence penalty `β * KL(π || π_ref)` prevents the RLHF agent from:",
                'opts' => [
                    ['Learning too slowly', false],
                    ['Reward model exploitation — finding adversarial trajectories that score highly on the reward model but diverge so far from natural behavior that the reward model\'s predictions are no longer reliable (the model was only trained on trajectories near the reference policy)', true],
                    ['Generating responses that are too long', false],
                    ['Forgetting the pretraining loss', false],
                ],
            ],
            [
                'q' => "```python\n# Reward model overoptimization in RLHF:\n# KL penalty β is too small:\n\n# β=0.001:\n# PPO episodes: 0    → KL=0,    human_eval=7.2/10\n# PPO episodes: 500  → KL=0.8,  human_eval=7.8/10\n# PPO episodes: 1000 → KL=3.2,  human_eval=7.1/10\n# PPO episodes: 2000 → KL=12.4, human_eval=5.3/10\n# PPO episodes: 5000 → KL=31.7, human_eval=2.1/10\n```\n\nHuman evaluation scores peaked then declined catastrophically despite the reward model score increasing. This phenomenon is known as:",
                'opts' => [
                    ['Catastrophic forgetting of pretraining', false],
                    ['Reward model overoptimization / Goodhart\'s Law — the RL agent exploits weaknesses in the reward model, generating responses that score well on the proxy reward model but are actually worse by the true human judgment standard', true],
                    ['KL divergence collapse', false],
                    ['The agent converging to a local optimum', false],
                ],
            ],

            // ── SCALABILITY & SYSTEMS ─────────────────────────────────────
            [
                'q' => "```python\n# GPU utilization in RL training:\n# Environment step: runs on CPU (physics simulation)\n# Neural network forward/backward: runs on GPU\n\n# Profiling result:\n# GPU utilization: 12%\n# CPU utilization: 98%\n# Bottleneck: environment simulation\n\n# Current setup: 1 environment, batch_size=32\n```\n\nThe GPU is underutilized because CPU environment steps are the bottleneck.\nThe production fix is:",
                'opts' => [
                    ['Use a faster GPU', false],
                    ['Vectorize environments — run N parallel environments simultaneously on CPU to generate N×32 experience per step, keeping the GPU fully saturated with training batches; envs like VectorEnv in Gymnasium implement this', true],
                    ['Reduce batch_size to 1 to speed up steps', false],
                    ['Move the environment simulation to GPU', false],
                ],
            ],
            [
                'q' => "A production recommender system uses a contextual bandit.\n\n  - 10 million users per day\n  - 500 item actions per user context\n  - LinUCB requires inverting A matrices: O(d³) per arm\n  - d = 200 feature dimensions\n  - 500 arms × 200³ operations = 4×10⁹ FLOPs per user\n  - At 10M users/day: 4×10¹⁶ FLOPs — infeasible in real-time\n\nThe engineering solution for production-scale contextual bandits is:",
                'opts' => [
                    ['Reduce d to 1 feature dimension', false],
                    ['Use approximate methods: diagonal A approximation (O(d) instead of O(d³)), or neural contextual bandits (NeuralUCB/NeuralTS) with batch updates, reducing per-request computation to milliseconds', true],
                    ['Only update the bandit weekly to amortize compute', false],
                    ['Switch to a non-contextual ε-greedy bandit', false],
                ],
            ],
            [
                'q' => "An RL agent is deployed to control HVAC systems across 1000 buildings.\n\nAfter 3 months:\n  - Buildings in northern regions: performance improved 18%\n  - Buildings in southern regions: performance degraded 12%\n  - Root cause: the policy was trained on summer data\n    and now faces winter conditions in the south\n\nThis production failure illustrates:",
                'opts' => [
                    ['The agent was not trained long enough', false],
                    ['Non-stationarity and distribution shift in real deployments — physical systems change seasonally; a production RL system requires continuous retraining or online adaptation mechanisms to track distribution shifts, plus separate evaluation pipelines per deployment context', true],
                    ['The reward function was designed incorrectly', false],
                    ['HVAC control is fundamentally unsolvable with RL', false],
                ],
            ],
            [
                'q' => "A team is evaluating whether to deploy an RL-based trading agent versus the existing rule-based system:\n\n  - Backtest (historical data): RL agent +34% returns\n  - Paper trading (live but no real money, 3 months): RL +12%\n  - Live trading (small allocation, 1 month): RL −3%\n\nPerformance degrades at each stage. The most important question before full deployment is:",
                'opts' => [
                    ['Should we train the agent for more episodes?', false],
                    ['Is the backtest using future data? Are the paper trading and live environments truly identical? Has the agent learned patterns specific to the backtest period that don\'t generalize? Does the agent\'s own trading affect market prices (market impact)?', true],
                    ['Should we switch from PPO to SAC?', false],
                    ['Is the discount factor γ set correctly for financial returns?', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 24 — Sequential Decision Making (Professional).");
    }
}