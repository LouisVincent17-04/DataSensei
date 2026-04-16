<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module24ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 24 — Sequential Decision Making (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Sequential Decision Making',
            'description'           => 'Work through multi-step Bellman calculations, trace Q-learning and TD updates, compare algorithm properties, and reason through policy convergence. Code snippets included.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 900,
            'order_index'           => 24,
        ]);

        $this->command->info("Seeding Intermediate sequential decision making questions...");

        $qaData = [

            // ── BELLMAN MULTI-STEP CALCULATIONS ───────────────────────────
            [
                'q' => "A 3-state MDP: S = {s1, s2, s3}. s3 is terminal with V=0.\n\nUsing policy π: always go right.\nTransitions are deterministic. Rewards: R(s1→s2)=1, R(s2→s3)=5. γ=0.9.\n\nApply Bellman evaluation once starting with V(s1)=V(s2)=0:\n  V(s2) = 5 + 0.9×0 = 5\n  V(s1) = 1 + 0.9×5 = ?\n",
                'opts' => [
                    ['4.5', false],
                    ['5.5', true],
                    ['6.0', false],
                    ['1.0', false],
                ],
            ],
            [
                'q' => "Running one more iteration of policy evaluation with V(s1)=5.5, V(s2)=5, V(s3)=0 (γ=0.9):\n\n  V(s2) = 5 + 0.9×0 = 5 (unchanged)\n  V(s1) = 1 + 0.9×5 = ?\n",
                'opts' => [
                    ['5.5 (converged)', true],
                    ['6.4', false],
                    ['4.6', false],
                    ['5.0', false],
                ],
            ],
            [
                'q' => "Value iteration on a 2-state MDP, s2 is terminal (V=0).\nTwo actions from s1: A1 gives R=3, A2 gives R=1. Both deterministically lead to s2. γ=0.9.\n\nAfter iteration 1 (starting from V(s1)=0):\n  V(s1) = max(3 + 0.9×0, 1 + 0.9×0) = ?",
                'opts' => [
                    ['1', false],
                    ['2', false],
                    ['3', true],
                    ['4', false],
                ],
            ],
            [
                'q' => "A stochastic MDP: from state s, action a leads to s1 with P=0.7 (R=10) and s2 with P=0.3 (R=2).\nBoth s1, s2 are terminal. γ = 1 (for simplicity).\n\nExpected Q*(s, a) = ?",
                'opts' => [
                    ['8.6', true],
                    ['6.0', false],
                    ['10.0', false],
                    ['12.0', false],
                ],
            ],

            // ── Q-LEARNING TRACES ─────────────────────────────────────────
            [
                'q' => "Q-learning update: Q(s,a) ← Q(s,a) + α[r + γ·max Q(s\',·) − Q(s,a)]\n\nα=0.5, γ=0.8, Q(s,a)=2.0, r=4, max Q(s\',·)=6.\n\nNew Q(s,a) = ?",
                'opts' => [
                    ['5.4', false],
                    ['4.8', false],
                    ['4.4', true],
                    ['3.0', false],
                ],
            ],
            [
                'q' => "After the update above (Q(s,a)=4.4), the agent receives another transition:\n  r=0, max Q(s\'\',·)=5, same α=0.5, γ=0.8\n\nNew Q(s,a) = ?",
                'opts' => [
                    ['2.7', false],
                    ['4.2', false],
                    ['3.2', false],
                    ['4.4 + 0.5×(0 + 0.8×5 − 4.4) = 4.4 + 0.5×(−0.4) = 4.2', true],
                ],
            ],
            [
                'q' => "SARSA update: Q(s,a) ← Q(s,a) + α[r + γ·Q(s\',a\') − Q(s,a)]\n\nThe key difference from Q-learning is that SARSA uses Q(s\',a\') where a\' is:\n\nα=0.1, γ=0.9, Q(s,a)=3, r=1, Q(s\',a\')=5\n\nNew Q(s,a) = ?",
                'opts' => [
                    ['3.25', false],
                    ['3.35', true],
                    ['3.45', false],
                    ['4.50', false],
                ],
            ],
            [
                'q' => "In a cliff-walking gridworld, Q-learning finds the optimal (shorter, cliff-edge) path, while SARSA finds a safer (longer) path.\n\nThis difference occurs because:",
                'opts' => [
                    ['SARSA uses a larger learning rate', false],
                    ['Q-learning learns the optimal Q* regardless of the ε-greedy behavior, while SARSA learns the value of the ε-greedy policy itself, accounting for the risk of accidental cliff falls from exploration', true],
                    ['Q-learning only works for deterministic environments', false],
                    ['SARSA cannot handle negative rewards', false],
                ],
            ],

            // ── TD(λ) & ELIGIBILITY TRACES ─────────────────────────────────
            [
                'q' => "TD(λ) interpolates between TD(0) (λ=0) and Monte Carlo (λ=1).\n\nWith λ=0, updates are based only on:",
                'opts' => [
                    ['The full episode return', false],
                    ['The one-step TD target: r + γ·V(s\')', true],
                    ['The average of all past returns', false],
                    ['Random bootstrapped estimates', false],
                ],
            ],
            [
                'q' => "The n-step return G_t^(n) = r_{t+1} + γr_{t+2} + ... + γ^{n-1}r_{t+n} + γ^n·V(s_{t+n}).\n\nFor n=2, γ=0.9, r₁=3, r₂=2, V(s_{t+2})=10:\n\nG_t^(2) = ?",
                'opts' => [
                    ['13.0', false],
                    ['12.9', true],
                    ['11.1', false],
                    ['14.3', false],
                ],
            ],
            [
                'q' => "Eligibility traces e(s) decay as:\n  e(s) ← γλ·e(s)  for unvisited states\n  e(s) ← γλ·e(s) + 1  when state s is visited\n\nWith γ=0.9, λ=0.5, e(s)=0.8 (before visiting s again):\n  New e(s) after visiting = ?",
                'opts' => [
                    ['0.36', false],
                    ['1.36', true],
                    ['0.8', false],
                    ['1.0', false],
                ],
            ],

            // ── POLICY GRADIENT CALCULATIONS ──────────────────────────────
            [
                'q' => "The REINFORCE update for policy parameters θ is:\n\n  θ ← θ + α · G_t · ∇_θ ln π_θ(a_t|s_t)\n\nIf G_t > 0, this update makes the action a_t in state s_t:",
                'opts' => [
                    ['Less likely to be selected', false],
                    ['More likely to be selected — gradient ascent increases log-probability of good actions', true],
                    ['Equally likely as before', false],
                    ['Replaced with the greedy action', false],
                ],
            ],
            [
                'q' => "The baseline in policy gradient (using advantage A_t = G_t − b(s_t) instead of G_t) reduces:\n\n  θ ← θ + α · A_t · ∇_θ ln π_θ(a_t|s_t)\n\nThe baseline b(s_t) reduces variance because:",
                'opts' => [
                    ['It removes negative returns entirely', false],
                    ['Subtracting the state value baseline centers the return signal, reducing the variance of gradient estimates without introducing bias', true],
                    ['It normalizes the rewards to [0,1]', false],
                    ['It eliminates the need for the discount factor', false],
                ],
            ],
            [
                'q' => "PPO's clipped objective is:\n\n  L^CLIP = E[min(r_t·Â_t, clip(r_t, 1−ε, 1+ε)·Â_t)]\n\nwhere r_t = π_θ(a|s)/π_θ_old(a|s).\n\nWith ε=0.2, r_t=0.7, Â_t=2.0:\n  clip(0.7, 0.8, 1.2) = 0.8 (since 0.7 < 0.8)\n  Term 1: 0.7×2.0 = 1.4\n  Term 2: 0.8×2.0 = 1.6\n  L^CLIP = min(1.4, 1.6) = ?",
                'opts' => [
                    ['1.6', false],
                    ['1.4', true],
                    ['0.8', false],
                    ['2.0', false],
                ],
            ],

            // ── ACTOR-CRITIC TRACES ────────────────────────────────────────
            [
                'q' => "In Advantage Actor-Critic (A2C), the advantage is estimated as:\n\n  A(s,a) = r + γ·V(s\') − V(s)\n\nWith r=3, γ=0.9, V(s\')=10, V(s)=8:\n\nA(s,a) = ?",
                'opts' => [
                    ['2.0', false],
                    ['4.0', false],
                    ['4.0 — wait: 3 + 9 − 8 = 4.0', true],
                    ['1.0', false],
                ],
            ],
            [
                'q' => "With the advantage A(s,a)=4.0 from above:\n  - If A > 0, the actor should make action a _____ likely.\n  - If A < 0, the actor should make action a _____ likely.",
                'opts' => [
                    ['More; More', false],
                    ['Less; More', false],
                    ['More; Less', true],
                    ['Less; Less', false],
                ],
            ],
            [
                'q' => "The critic loss in A2C is typically the squared TD error:\n\n  L_critic = (r + γ·V(s\') − V(s))²\n\nUsing the same values (r=3, γ=0.9, V(s\')=10, V(s)=8):\n\nL_critic = ?",
                'opts' => [
                    ['4.0', false],
                    ['16.0', true],
                    ['8.0', false],
                    ['2.0', false],
                ],
            ],

            // ── DQN MECHANICS ─────────────────────────────────────────────
            [
                'q' => "```python\nimport numpy as np\n\nreplay_buffer = []\n\ndef store_transition(s, a, r, s_next, done):\n    replay_buffer.append((s, a, r, s_next, done))\n    if len(replay_buffer) > 10000:\n        replay_buffer.pop(0)\n```\n\nThis replay buffer removes the OLDEST experience when full.\nA key advantage of experience replay is:",
                'opts' => [
                    ['It reduces the total amount of data collected', false],
                    ['Breaking temporal correlations between consecutive training samples, stabilizing learning', true],
                    ['It guarantees the agent only trains on positive rewards', false],
                    ['It automatically tunes the learning rate', false],
                ],
            ],
            [
                'q' => "```python\ndef compute_td_target(r, s_next, done, gamma, target_net):\n    if done:\n        return r\n    else:\n        q_next = target_net(s_next).max().item()\n        return r + gamma * q_next\n```\n\nWhen `done=True`, the TD target is just `r` because:",
                'opts' => [
                    ['The target network is not available for terminal states', false],
                    ['Terminal states have no future — there is no next state value to bootstrap from', true],
                    ['The gamma factor becomes 0 at terminal states', false],
                    ['The reward at terminal states is always 0', false],
                ],
            ],
            [
                'q' => "Dueling DQN decomposes Q(s,a) into:\n\n  Q(s,a) = V(s) + A(s,a) − mean_a A(s,a)\n\nThe benefit of separating V(s) and A(s,a) is:",
                'opts' => [
                    ['It doubles the number of parameters for better capacity', false],
                    ['V(s) can be learned even for actions that are rarely taken, improving value estimates in states where action choice doesn\'t matter much', true],
                    ['It eliminates the need for a target network', false],
                    ['A(s,a) replaces the reward signal entirely', false],
                ],
            ],

            // ── MULTI-ARMED BANDITS — INTERMEDIATE ───────────────────────
            [
                'q' => "Three bandit arms have been pulled N=[5, 2, 10] times with sample means Q̄=[4.0, 6.0, 3.5].\nUsing UCB1 with c=1 and total pulls t=17:\n\nUCB(a) = Q̄(a) + √(ln(17)/N(a))\nln(17) ≈ 2.83\n\nUCB(Arm 2) = 6.0 + √(2.83/2) ≈ 6.0 + 1.19 = ?",
                'opts' => [
                    ['6.75', false],
                    ['7.19', true],
                    ['7.50', false],
                    ['6.19', false],
                ],
            ],
            [
                'q' => "UCB(Arm 1) = 4.0 + √(2.83/5) ≈ 4.0 + 0.75 = 4.75\nUCB(Arm 2) ≈ 7.19\nUCB(Arm 3) = 3.5 + √(2.83/10) ≈ 3.5 + 0.53 = 4.03\n\nUCB1 selects:",
                'opts' => [
                    ['Arm 1', false],
                    ['Arm 2', true],
                    ['Arm 3', false],
                    ['A random arm', false],
                ],
            ],
            [
                'q' => "After 1000 rounds of a 3-arm bandit problem:\n  Arm 1 (true μ=5): pulled 500 times\n  Arm 2 (true μ=8): pulled 450 times\n  Arm 3 (true μ=3): pulled 50 times\n\nCumulative regret ≈ (8−5)×500 + (8−8)×450 + (8−3)×50 = ?",
                'opts' => [
                    ['2500', false],
                    ['1750', true],
                    ['1000', false],
                    ['500', false],
                ],
            ],

            // ── POMDP — INTERMEDIATE ──────────────────────────────────────
            [
                'q' => "A POMDP has 2 hidden states: s1, s2. Initial belief b = [0.6, 0.4].\nAfter taking action a and receiving observation o:\n  P(o|s1,a) = 0.9, P(o|s2,a) = 0.2\n  Transition keeps same state with P=1 (deterministic)\n\nUnnormalized b\'(s1) = P(o|s1)·b(s1) = 0.9×0.6 = 0.54\nUnnormalized b\'(s2) = P(o|s2)·b(s2) = 0.2×0.4 = 0.08\nNormalized sum = 0.62\n\nb\'(s1) = 0.54/0.62 ≈ ?",
                'opts' => [
                    ['0.60', false],
                    ['0.87', true],
                    ['0.75', false],
                    ['0.54', false],
                ],
            ],
            [
                'q' => "The POMDP belief update moved the belief from [0.6, 0.4] to approximately [0.87, 0.13].\n\nThis means after observing o, the agent is now more confident that:",
                'opts' => [
                    ['The true state is s2', false],
                    ['The true state is s1 — the observation was more likely under s1', true],
                    ['Both states are equally likely', false],
                    ['No conclusion can be drawn', false],
                ],
            ],

            // ── CONVERGENCE & THEORETICAL PROPERTIES ──────────────────────
            [
                'q' => "Q-learning converges to the optimal Q* if:\n  1. Every state-action pair is visited infinitely often\n  2. The learning rate α satisfies: Σα=∞ and Σα²<∞\n\nWhich learning rate schedule satisfies these conditions?",
                'opts' => [
                    ['α = 0.1 (constant)', false],
                    ['α_t = 1/t', true],
                    ['α_t = 1/t² (Σ diverges? No — Σ 1/t² converges, violating condition 1)', false],
                    ['α_t = 0 for all t', false],
                ],
            ],
            [
                'q' => "The contraction mapping property guarantees that the Bellman optimality operator T:\n\n  ||TV − TV\'||_∞ ≤ γ·||V − V\'||_∞\n\nThis means value iteration converges because:",
                'opts' => [
                    ['The reward function is always positive', false],
                    ['Each application of T brings value functions closer together by a factor of γ, guaranteeing convergence to V* when γ < 1', true],
                    ['The state space is finite', false],
                    ['The policy is deterministic', false],
                ],
            ],
            [
                'q' => "In practice, function approximation (neural networks) in RL does NOT guarantee convergence because:\n\n  The projected Bellman operator T_π is no longer a contraction mapping in general.",
                'opts' => [
                    ['Neural networks train too slowly', false],
                    ['Combining function approximation with bootstrapping can cause the value estimates to diverge — the deadly triad', true],
                    ['Neural networks cannot represent Q-functions', false],
                    ['The discount factor must equal 1 for neural networks', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 24 — Sequential Decision Making (Intermediate).");
    }
}