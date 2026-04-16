<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module24ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 24 — Sequential Decision Making (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Sequential Decision Making',
            'description'           => 'Debug RL implementations, interpret training instability, reason through code-level algorithm design choices, and analyze complex policy gradient and DQN configurations.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1100,
            'order_index'           => 24,
        ]);

        $this->command->info("Seeding Advanced sequential decision making questions...");

        $qaData = [

            // ── DQN CODE — ADVANCED ───────────────────────────────────────
            [
                'q' => "```python\nimport torch\nimport torch.nn as nn\n\nclass DQN(nn.Module):\n    def __init__(self, state_dim, action_dim):\n        super().__init__()\n        self.net = nn.Sequential(\n            nn.Linear(state_dim, 128),\n            nn.ReLU(),\n            nn.Linear(128, 128),\n            nn.ReLU(),\n            nn.Linear(128, action_dim)\n        )\n\n    def forward(self, x):\n        return self.net(x)\n\nonline_net = DQN(8, 4)\ntarget_net = DQN(8, 4)\ntarget_net.load_state_dict(online_net.state_dict())\n```\n\nThe target network is initialized with the SAME weights as the online network. Why is this important?",
                'opts' => [
                    ['To save memory by sharing parameters', false],
                    ['Ensures the first TD targets are consistent with the current Q estimates, preventing immediate divergence at the start of training', true],
                    ['The target network must always equal the online network', false],
                    ['It prevents the replay buffer from overflowing', false],
                ],
            ],
            [
                'q' => "```python\ndef train_step(batch, online_net, target_net, optimizer, gamma=0.99):\n    states, actions, rewards, next_states, dones = batch\n\n    current_q = online_net(states).gather(1, actions.unsqueeze(1))\n\n    with torch.no_grad():\n        next_q = target_net(next_states).max(1)[0]\n        target_q = rewards + gamma * next_q * (1 - dones)\n\n    loss = nn.MSELoss()(current_q.squeeze(), target_q)\n    optimizer.zero_grad()\n    loss.backward()\n    optimizer.step()\n```\n\nThe `(1 - dones)` mask in the target calculation serves to:",
                'opts' => [
                    ['Normalize rewards to [0, 1]', false],
                    ['Zero out the bootstrapped future value for terminal transitions — done=1 means no future rewards', true],
                    ['Reduce the learning rate for difficult transitions', false],
                    ['Weight transitions by their recency', false],
                ],
            ],
            [
                'q' => "```python\n# Double DQN modification:\nwith torch.no_grad():\n    # Online net selects best action:\n    best_actions = online_net(next_states).argmax(1, keepdim=True)\n    # Target net evaluates that action:\n    next_q = target_net(next_states).gather(1, best_actions).squeeze()\n    target_q = rewards + gamma * next_q * (1 - dones)\n```\n\nCompared to standard DQN which uses `target_net(next_states).max(1)[0]`, Double DQN reduces:",
                'opts' => [
                    ['Computational cost per step', false],
                    ['Overestimation bias — standard DQN\'s max operation over target Q-values systematically overestimates because it always selects the highest noisy estimate', true],
                    ['Memory usage in the replay buffer', false],
                    ['The number of parameters in the network', false],
                ],
            ],
            [
                'q' => "```python\n# Prioritized Experience Replay\nclass PrioritizedReplayBuffer:\n    def __init__(self, capacity, alpha=0.6):\n        self.alpha = alpha  # priority exponent\n        self.priorities = []\n\n    def add(self, transition, td_error):\n        priority = (abs(td_error) + 1e-6) ** self.alpha\n        self.priorities.append(priority)\n\n    def sample(self, batch_size, beta=0.4):\n        probs = np.array(self.priorities) / sum(self.priorities)\n        indices = np.random.choice(len(self.priorities),\n                                   batch_size, p=probs)\n        weights = (1 / (len(self.priorities) * probs[indices])) ** beta\n        return indices, weights / weights.max()\n```\n\nThe `beta` parameter for importance sampling weights (IS weights) corrects for:",
                'opts' => [
                    ['The neural network learning rate', false],
                    ['The bias introduced by non-uniform sampling — high-priority samples are over-represented, IS weights correct the distribution back to uniform', true],
                    ['The temporal correlation between episodes', false],
                    ['The decay of old priorities over time', false],
                ],
            ],

            // ── POLICY GRADIENT — ADVANCED CODE ───────────────────────────
            [
                'q' => "```python\nimport torch\nfrom torch.distributions import Categorical\n\ndef reinforce_update(policy, optimizer, episode_log_probs,\n                     episode_rewards, gamma=0.99):\n    returns = []\n    G = 0\n    for r in reversed(episode_rewards):\n        G = r + gamma * G\n        returns.insert(0, G)\n\n    returns = torch.tensor(returns)\n    returns = (returns - returns.mean()) / (returns.std() + 1e-8)\n\n    loss = 0\n    for log_prob, G in zip(episode_log_probs, returns):\n        loss -= log_prob * G\n\n    optimizer.zero_grad()\n    loss.backward()\n    optimizer.step()\n```\n\nThe line `returns = (returns - returns.mean()) / (returns.std() + 1e-8)` normalizes returns. The primary effect is:",
                'opts' => [
                    ['Making all returns positive so gradient ascent always increases action probabilities', false],
                    ['Reducing variance of gradient estimates — actions with above-average return are reinforced, below-average are discouraged, stabilizing training', true],
                    ['Converting returns to z-scores for mathematical correctness', false],
                    ['Ensuring the discount factor is applied correctly', false],
                ],
            ],
            [
                'q' => "```python\n# PPO update loop\nfor _ in range(ppo_epochs):  # multiple epochs per data batch\n    ratio = torch.exp(new_log_probs - old_log_probs)\n    surr1 = ratio * advantages\n    surr2 = torch.clamp(ratio, 1 - clip_eps, 1 + clip_eps) * advantages\n    actor_loss = -torch.min(surr1, surr2).mean()\n\n    critic_loss = F.mse_loss(values, returns)\n    entropy_bonus = -new_dist.entropy().mean()\n\n    loss = actor_loss + 0.5 * critic_loss + 0.01 * entropy_bonus\n```\n\nThe `entropy_bonus` term (`-entropy`) is added to the loss with coefficient 0.01. Since we minimize the loss, this:\n  - entropy is high → entropy_bonus is negative → loss decreases\n  - Encourages the policy to maintain higher entropy (more randomness)\n\nThe purpose is to:",
                'opts' => [
                    ['Reduce the actor loss magnitude', false],
                    ['Prevent premature convergence to a deterministic policy by encouraging continued exploration', true],
                    ['Normalize the advantage estimates', false],
                    ['Balance the actor and critic learning rates', false],
                ],
            ],
            [
                'q' => "```python\n# GAE (Generalized Advantage Estimation)\ndef compute_gae(rewards, values, next_values, dones,\n                gamma=0.99, lam=0.95):\n    advantages = []\n    gae = 0\n    for t in reversed(range(len(rewards))):\n        delta = rewards[t] + gamma * next_values[t] * (1-dones[t]) - values[t]\n        gae = delta + gamma * lam * (1 - dones[t]) * gae\n        advantages.insert(0, gae)\n    return advantages\n```\n\nGAE with λ=0 reduces to the 1-step TD advantage: A_t = r_t + γV(s_{t+1}) - V(s_t)\nGAE with λ=1 reduces to the full Monte Carlo advantage.\n\nλ=0.95 provides:",
                'opts' => [
                    ['Only 1-step lookahead, high bias, low variance', false],
                    ['A bias-variance trade-off — lower variance than MC (λ=1) but lower bias than 1-step TD (λ=0)', true],
                    ['The exact same result as Monte Carlo always', false],
                    ['Zero bias with high variance', false],
                ],
            ],

            // ── ACTOR-CRITIC — ADVANCED ────────────────────────────────────
            [
                'q' => "```python\nclass ActorCritic(nn.Module):\n    def __init__(self, state_dim, action_dim):\n        super().__init__()\n        self.shared = nn.Sequential(\n            nn.Linear(state_dim, 256), nn.ReLU()\n        )\n        self.actor = nn.Linear(256, action_dim)\n        self.critic = nn.Linear(256, 1)\n\n    def forward(self, x):\n        features = self.shared(x)\n        logits = self.actor(features)\n        value = self.critic(features)\n        return logits, value\n```\n\nSharing the feature extraction layers between actor and critic (instead of two separate networks):\n\nThe main RISK of weight sharing is:",
                'opts' => [
                    ['The actor and critic will always have identical outputs', false],
                    ['Conflicting gradients from actor and critic losses can interfere, potentially hurting both — separate networks avoid this at the cost of more parameters', true],
                    ['The shared network cannot learn nonlinear features', false],
                    ['The critic value is forced to equal the action logits', false],
                ],
            ],
            [
                'q' => "```python\n# A3C: Asynchronous Advantage Actor-Critic\n# Multiple workers run in parallel, each collecting experience\n# and pushing gradients to a global network\n\ndef worker_train(global_net, local_net, optimizer):\n    for episode in range(max_episodes):\n        local_net.load_state_dict(global_net.state_dict())\n        trajectory = collect_trajectory(local_net)\n        loss = compute_loss(trajectory, local_net)\n        optimizer.zero_grad()\n        loss.backward()\n        # Push gradients to global net\n        for global_p, local_p in zip(global_net.parameters(),\n                                      local_net.parameters()):\n            global_p._grad = local_p.grad\n        optimizer.step()\n```\n\nA3C's asynchronous parallelism provides decorrelated experience because:",
                'opts' => [
                    ['Each worker uses a different neural network architecture', false],
                    ['Multiple workers explore different parts of the environment simultaneously, naturally decorrelating experience — replacing the replay buffer\'s role in DQN', true],
                    ['Workers share a replay buffer to avoid redundant experience', false],
                    ['The global network is synchronized every step', false],
                ],
            ],

            // ── Q-LEARNING DEBUGGING ──────────────────────────────────────
            [
                'q' => "```python\n# Training loop: Q-values are diverging\n# Episode 1: avg Q = 2.3\n# Episode 50: avg Q = 8.1\n# Episode 100: avg Q = 47.3\n# Episode 200: avg Q = 4821.2\n# Episode 300: avg Q = nan\n\noptimizer = torch.optim.Adam(online_net.parameters(), lr=0.01)\ntarget_update_freq = 10000\nbatch_size = 32\n```\n\nThe Q-values are diverging. The primary contributing factor is:",
                'opts' => [
                    ['The batch size of 32 is too small', false],
                    ['A combination of a high learning rate (0.01) and infrequent target updates (10000 steps) — the target is moving too slowly, creating a large feedback loop that amplifies Q-value overestimation', true],
                    ['Adam optimizer is incompatible with DQN', false],
                    ['The replay buffer is too large', false],
                ],
            ],
            [
                'q' => "```python\n# Reward shaping issue:\nenv_reward = 0 if not done else 1  # only +1 at goal\n\n# Shaped reward:\nshaped_reward = env_reward + 0.01 * get_potential(state)\n```\n\nReward shaping (adding potential-based bonuses) is safe only if the shaping term is a potential function:\n\n  F(s, a, s\') = γ·Φ(s\') − Φ(s)\n\nThis form is safe because:",
                'opts' => [
                    ['It always produces positive rewards', false],
                    ['It can be shown that the optimal policy under the shaped reward is the same as under the original reward — potential-based shaping is provably policy-invariant', true],
                    ['It eliminates the discount factor', false],
                    ['It is equivalent to adding a constant to all rewards', false],
                ],
            ],
            [
                'q' => "```python\n# Observation: policy converged but performance is poor\n# The environment: 10×10 grid, goal at (9,9)\n# Reward: -0.001 per step, +1 at goal\n# Training: 500 episodes of 200 steps each\n\n# Diagnosis: episode length is capped at 200 steps\n# but optimal path requires ~18 steps\n# At step 200, done=True is forced\n```\n\nThe agent is not learning to reach the goal. The root cause is:",
                'opts' => [
                    ['The reward of -0.001 is too large', false],
                    ['Sparse reward — the agent rarely reaches the goal in 200 random/early-stage steps, so Q-values for the goal are never updated; the episode cutoff creates a horizon problem', true],
                    ['The grid is too large for Q-learning', false],
                    ['The learning rate is too small', false],
                ],
            ],

            // ── POLICY GRADIENT DEBUGGING ─────────────────────────────────
            [
                'q' => "```python\n# Training REINFORCE on CartPole\n# Observation after 500 episodes:\n# Episode rewards: [12, 8, 200, 9, 11, 200, 7, 8, ...]\n# Mean reward oscillates between ~10 and ~200\n\n# Current setup:\n# - No baseline subtracted\n# - Learning rate: 1e-3\n# - No entropy bonus\n```\n\nThe high variance (oscillating between 10 and 200) is primarily caused by:",
                'opts' => [
                    ['The CartPole environment being non-stationary', false],
                    ['REINFORCE without a baseline has very high gradient variance — occasionally succeeding episodes have large returns that drastically update the policy, while failure episodes push it back', true],
                    ['The learning rate is too low', false],
                    ['CartPole cannot be solved with policy gradients', false],
                ],
            ],
            [
                'q' => "```python\n# PPO training diagnostic\nfor epoch in range(10):\n    ratio = torch.exp(log_probs - old_log_probs)\n    print(f'Epoch {epoch}: ratio mean={ratio.mean():.3f},\n                          ratio max={ratio.max():.3f}')\n# Output:\n# Epoch 0: mean=1.000, max=1.001\n# Epoch 3: mean=1.241, max=4.832\n# Epoch 7: mean=1.893, max=12.4\n# Epoch 9: mean=2.341, max=31.2\n```\n\nThe ratio is growing far beyond [1-ε, 1+ε] in later epochs. This happens when:",
                'opts' => [
                    ['The actor network is too small', false],
                    ['Too many PPO epochs on the same data batch causes the updated policy to diverge far from the reference policy — the clipping provides insufficient constraint when ratio grows this large', true],
                    ['The critic loss is not being computed correctly', false],
                    ['The environment is providing false rewards', false],
                ],
            ],

            // ── DEEP RL COMPONENTS — ADVANCED ────────────────────────────
            [
                'q' => "```python\n# Noisy Networks for exploration\nclass NoisyLinear(nn.Module):\n    def __init__(self, in_features, out_features, sigma=0.5):\n        super().__init__()\n        self.weight_mu = nn.Parameter(torch.zeros(out_features, in_features))\n        self.weight_sigma = nn.Parameter(\n            torch.full((out_features, in_features), sigma))\n        self.register_buffer('weight_eps', torch.zeros(out_features, in_features))\n\n    def forward(self, x):\n        if self.training:\n            self.weight_eps.normal_()\n            weight = self.weight_mu + self.weight_sigma * self.weight_eps\n        else:\n            weight = self.weight_mu\n        return F.linear(x, weight)\n```\n\nNoisy Networks replace ε-greedy exploration by:",
                'opts' => [
                    ['Adding noise directly to the state observations', false],
                    ['Injecting parametric noise into network weights — the learned σ parameters control exploration intensity per neuron, allowing state-dependent adaptive exploration', true],
                    ['Using random action selection with probability σ', false],
                    ['Adding noise only during target network updates', false],
                ],
            ],
            [
                'q' => "```python\n# Rainbow DQN combines 6 improvements:\n# 1. Double DQN\n# 2. Dueling networks\n# 3. Prioritized replay\n# 4. Multi-step returns (n-step)\n# 5. Noisy Networks\n# 6. Distributional RL (C51)\n\n# C51 predicts a distribution over returns:\nclass DistributionalDQN(nn.Module):\n    def __init__(self, state_dim, action_dim, n_atoms=51):\n        super().__init__()\n        self.net = ...\n        self.n_atoms = n_atoms  # 51 discrete return values\n```\n\nDistributional RL (C51) predicts the DISTRIBUTION of returns rather than just the mean Q-value.\nA key advantage over standard DQN is:",
                'opts' => [
                    ['It eliminates the need for a discount factor', false],
                    ['The full return distribution captures uncertainty and multi-modality — e.g., a risky action might have bimodal returns (great or terrible); standard Q misses this information', true],
                    ['It reduces the number of parameters needed', false],
                    ['It automatically handles continuous action spaces', false],
                ],
            ],

            // ── CONTINUOUS ACTION SPACES ──────────────────────────────────
            [
                'q' => "```python\nimport torch\nfrom torch.distributions import Normal\n\nclass ContinuousActorCritic(nn.Module):\n    def forward(self, state):\n        features = self.shared(state)\n        mu = self.actor_mu(features)      # mean of action\n        log_std = self.actor_log_std(features)  # log std\n        std = torch.exp(log_std.clamp(-20, 2))\n        dist = Normal(mu, std)\n        action = dist.rsample()  # reparameterization trick\n        log_prob = dist.log_prob(action).sum(-1)\n        value = self.critic(features)\n        return action, log_prob, value\n```\n\nThe `.rsample()` (reparameterized sample) is used instead of `.sample()` because:",
                'opts' => [
                    ['rsample is faster than sample', false],
                    ['rsample allows gradients to flow through the sampling operation (action = μ + ε·σ where ε is fixed noise), enabling backpropagation through the stochastic action', true],
                    ['rsample prevents actions from being outside [-1, 1]', false],
                    ['rsample generates more diverse actions than sample', false],
                ],
            ],
            [
                'q' => "```python\n# SAC (Soft Actor-Critic) objective:\n# Maximize: E[Σ γ^t (r_t + α·H(π(·|s_t)))]\n# where H(π) = -E[log π(a|s)] is entropy\n\n# Temperature parameter α:\nalpha = torch.tensor(0.2, requires_grad=True)\nalpha_optimizer = torch.optim.Adam([alpha], lr=3e-4)\n\n# Alpha loss (auto-tuning):\nalpha_loss = -(alpha * (log_pi + target_entropy).detach()).mean()\nalpha_optimizer.zero_grad()\nalpha_loss.backward()\nalpha_optimizer.step()\n```\n\nSAC's entropy maximization objective encourages the policy to be as random as possible while still earning rewards. The automatic temperature tuning adjusts α so that:",
                'opts' => [
                    ['The policy entropy always equals exactly 0.2', false],
                    ['The policy entropy matches a target entropy level — if entropy is too low (too deterministic), α increases to push toward more exploration; if too high, α decreases', true],
                    ['The learning rate is adapted based on entropy', false],
                    ['α controls the discount factor γ adaptively', false],
                ],
            ],

            // ── MODEL-BASED RL ────────────────────────────────────────────
            [
                'q' => "```python\n# Dyna-Q: model-based + model-free hybrid\nfor step in range(max_steps):\n    # Real environment interaction\n    a = epsilon_greedy(Q, s)\n    s_next, r, done = env.step(a)\n    update_Q(s, a, r, s_next)  # real update\n    update_model(s, a, r, s_next)  # store transition\n\n    # Simulated planning steps\n    for _ in range(n_planning_steps):  # e.g., n=50\n        s_sim, a_sim = random.choice(model.visited)\n        r_sim, s_next_sim = model.sample(s_sim, a_sim)\n        update_Q(s_sim, a_sim, r_sim, s_next_sim)\n```\n\nDyna-Q's planning steps (using the learned model) improve sample efficiency because:",
                'opts' => [
                    ['The learned model is always perfectly accurate', false],
                    ['Each real environment step generates n additional Q-updates using simulated data, propagating value information through state space much faster than pure model-free methods', true],
                    ['The model removes the need for exploration', false],
                    ['Simulated transitions have zero variance', false],
                ],
            ],
            [
                'q' => "```python\n# World Models / Dreamer approach:\n# 1. Learn a latent world model from pixels\n# 2. Imagine trajectories in latent space\n# 3. Train actor-critic entirely in imagination\n\nfor epoch in range(training_epochs):\n    # World model training\n    sequences = replay_buffer.sample_sequences()\n    encoder_loss, decoder_loss, rssm_loss = world_model.update(sequences)\n\n    # Imagination rollouts (no env interaction needed!)\n    imagined_trajectories = world_model.imagine(policy, horizon=15)\n    actor_loss, critic_loss = actor_critic.update(imagined_trajectories)\n```\n\nTraining the actor-critic purely in imagination (without real environment steps) is risky because:",
                'opts' => [
                    ['The GPU cannot handle imagined trajectories', false],
                    ['If the world model is inaccurate (model error), the agent optimizes for imagined rewards that may not correspond to real rewards — compounding model errors over the 15-step horizon can lead to poor real-world behavior', true],
                    ['Imagined trajectories have no randomness', false],
                    ['The policy cannot explore in latent space', false],
                ],
            ],

            // ── MULTI-ARMED BANDITS — ADVANCED ────────────────────────────
            [
                'q' => "```python\nclass ThompsonSamplingBeta:\n    \"\"\"For Bernoulli rewards: use Beta posterior\"\"\"\n    def __init__(self, n_arms):\n        self.alpha = np.ones(n_arms)  # successes + 1\n        self.beta = np.ones(n_arms)   # failures + 1\n\n    def select_arm(self):\n        samples = np.random.beta(self.alpha, self.beta)\n        return np.argmax(samples)\n\n    def update(self, arm, reward):\n        self.alpha[arm] += reward       # 1 if success\n        self.beta[arm] += (1 - reward)  # 1 if failure\n```\n\nAfter 100 rounds, arm 0 has α=62, β=40 and arm 1 has α=5, β=10.\nArm 0 is more likely to be selected because:",
                'opts' => [
                    ['α+β is larger for arm 0', false],
                    ['Beta(62,40) has mean 62/102 ≈ 0.61 and is tightly concentrated; Beta(5,10) has mean 5/15 ≈ 0.33. Thompson samples from arm 0 will almost always exceed those from arm 1', true],
                    ['The algorithm always favors arms that have been pulled more', false],
                    ['Arm 0\'s β is larger than arm 1\'s β', false],
                ],
            ],
            [
                'q' => "```python\nclass LinUCB:\n    \"\"\"Contextual bandit with linear reward model\"\"\"\n    def __init__(self, n_arms, n_features, alpha=1.0):\n        self.A = [np.eye(n_features) for _ in range(n_arms)]\n        self.b = [np.zeros(n_features) for _ in range(n_arms)]\n        self.alpha = alpha\n\n    def select_arm(self, context):\n        ucb_values = []\n        for a in range(len(self.A)):\n            theta = np.linalg.solve(self.A[a], self.b[a])\n            ucb = theta @ context + self.alpha * np.sqrt(\n                context @ np.linalg.solve(self.A[a], context))\n            ucb_values.append(ucb)\n        return np.argmax(ucb_values)\n```\n\nThe term `alpha * sqrt(context @ A_inv @ context)` is the exploration bonus. It is LARGE when:",
                'opts' => [
                    ['The context vector is close to zero', false],
                    ['The context is in a direction with high uncertainty — A^{-1} is large in that direction, meaning few similar contexts have been observed before', true],
                    ['The theta vector is large', false],
                    ['Alpha is set to 0', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 24 — Sequential Decision Making (Advanced).");
    }
}